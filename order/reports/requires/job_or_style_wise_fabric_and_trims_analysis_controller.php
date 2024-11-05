<?

header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.trims.php');


$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$size_lib = return_library_array("select id,size_name from lib_size", "id", "size_name");

$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$trim_groupArr = return_library_array("select id, item_name from lib_item_group", 'id', 'item_name');
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id FROM user_passwd where id=$user_id");
$brand_id = $userCredential[0][csf('brand_id')];
if ($brand_id) {
	$brand_cond = " and id in ( $brand_id)";
}

if ($action == "load_drop_down_buyer") {
	//$data=explode('_',$data);
	echo create_drop_down("cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/job_or_style_wise_fabric_and_trims_analysis_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/job_or_style_wise_fabric_and_trims_analysis_controller', this.value, 'load_drop_down_brand', 'brand_td');", 0);
	exit();
}
if ($action == "load_drop_down_season") {
	$data_arr = explode("*", $data);
	if ($data_arr[1] == 1) $width = 70;
	else $width = 70;
	echo create_drop_down("cbo_season_id", $width, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "-- Select Season--", "", "");
	exit();
}

if ($action == "load_drop_down_brand") {
	$data_arr = explode("*", $data);
	if ($data_arr[1] == 1) $width = 70;
	else $width = 70;
	echo create_drop_down("cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC", "id,brand_name", 1, "--Brand--", $selected, "");
	exit();
}

if ($action == "style_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data = explode('_', $data);
	//print_r ($data); 
?>
	<script>
		function js_set_value(id) {
			var str = id.split("_");
			$('#txt_po_id').val(str[0]);
			$('#txt_po_val').val(str[1]);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="txt_po_id" />
	<input type="hidden" id="txt_po_val" />
<?
	if ($data[0] == 0) $company_name = "";
	else $company_name = "company_name='$data[0]'";
	if ($data[1] == 0) $buyer_name = "";
	else $buyer_name = " and buyer_name='$data[1]'";
	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";
	$sql = "select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name order by insert_date desc";
	echo create_list_view("list_view", "Style Ref. No.,Job No,Year", "200,100,100", "450", "370", 0, $sql, "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "employee_info_controller", 'setFilterGrid("list_view",-1);', '0');
	exit();
}

if ($action == "order_no_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data = explode('_', $data);
	$company = $data[0];
	$buyer = $data[1];
	$style = $data[2];

	//print ($data[1]);
?>
	<script>
		var selected_id = new Array,
			selected_name = new Array();
		selected_attach_id = new Array();

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function check_all_data() {
			var row_num = $('#list_view tr').length - 1;
			for (var i = 1; i <= row_num; i++) {
				$("#tr_" + i).click();
			}

		}

		function js_set_value(id) { //alert(id);
			var str = id.split("_");
			toggle(document.getElementById('tr_' + str[0]), '#FFFFFF');
			var strdt = str[2];
			str = str[1];

			if (jQuery.inArray(str, selected_id) == -1) {
				selected_id.push(str);
				selected_name.push(strdt);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var ddd = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			ddd = ddd.substr(0, ddd.length - 1);
			$('#txt_po_id').val(id);
			$('#txt_po_val').val(ddd);
		}
	</script>
	<input type="hidden" id="txt_po_id" />
	<input type="hidden" id="txt_po_val" />
	<?
	if ($data[0] == 0) $company_id = "";
	else $company_id = " and company_name=$data[0]";
	if ($data[1] == 0) $buyer_id = "";
	else $buyer_id = " and buyer_name=$data[1]";
	if ($data[2] == 0) $style = "";
	else $style = " and b.id in($data[2])";

	if ($db_type == 0) $year_field = "YEAR(b.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(b.insert_date,'YYYY') as year";
	else $year_field = "";

	$sql = "select a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 $company_id  $buyer_id $style order by b.insert_date desc";

	//echo $sql;

	echo create_list_view("list_view", "Order Number,Job No, Year", "150,100,50", "440", "310", 0, $sql, "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "", "setFilterGrid('list_view',-1)", "0", "", 1);
	exit();
}

if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$report_type = str_replace("'", "", $operation);

	$cbo_company = str_replace("'", "", $cbo_company_id);
	$cbo_buyer = str_replace("'", "", $cbo_buyer_id);
	$all_style = str_replace("'", "", $txt_style_id);
	$all_style = array_unique(explode(",", $all_style));
	$all_style_quted = "";
	foreach ($all_style as $style_no) {
		$all_style_quted .= "'" . $style_no . "'" . ",";
	}
	$all_style_quted = chop($all_style_quted, ",");

	$cbo_brand_id = str_replace("'", "", $cbo_brand_id);
	$cbo_season_id = str_replace("'", "", $cbo_season_id);
	$cbo_season_year = str_replace("'", "", $cbo_season_year);
	//echo $all_style_quted;die;



	if ($cbo_brand_id > 0) {

		$brand_cond = " and a.brand_id=$cbo_brand_id";
	} else {
		$brand_cond = "";
	}

	if ($cbo_season_id > 0) {

		$season_cond = " and a.season_buyer_wise=$cbo_season_id";
	} else {
		$season_cond = "";
	}

	if ($cbo_season_year > 0) {

		$season_year_cond = " and a.season_year=$cbo_season_year";
	} else {
		$season_year_cond = "";
	}

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else $buyer_id_cond = "";
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$cbo_buyer";
	}

	if (str_replace("'", "", trim($txt_order_no)) == "") {
		$po_id_cond = "";
	} else {
		if (str_replace("'", "", $txt_order_no_id) != "") {
			$po_id_cond = " and b.id in(" . str_replace("'", "", $txt_order_no_id) . ")";
		} else {
			$po_number = trim(str_replace("'", "", $txt_order_no)) . "%";
			$po_id_cond = " and b.po_number like '$po_number'";
		}
	}
	if (str_replace("'", "", $txt_style) != "") $style = " and a.id in(" . str_replace("'", "", $txt_style) . ")";
	else $style = "";

	$year_id = str_replace("'", "", $cbo_year);
	if ($db_type == 0) {
		if ($year_id != 0) $year_cond = " and year(a.insert_date)=$year_id";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($year_id != 0) $year_cond = " and TO_CHAR(a.insert_date,'YYYY')=$year_id";
		else $year_cond = "";
	}

	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	if ($date_from != "" && $date_to != "") $pub_date = " and b.pub_shipment_date between '" . $date_from . "' and '" . $date_to . "'";
	else $pub_date = "";

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent,a.type from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array_deter = sql_select($sql_deter);
	foreach ($data_array_deter as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		$constr_fab_arr[$row[csf('id')]] = $row[csf('type')];
	}

	ob_start();

	if ($report_type == 1) {
	?>
		<fieldset style="width:1850px;">
			<!-- Fabric Part -->
			<table width="1850">
				<tr class="form_caption">
					<td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="24" align="center"><? echo $company_library[$cbo_company]; ?></td>
				</tr>
			</table>
			<h2 style="float: left; font-size: 20px;">Fabric</h2>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1846" class="rpt_table">
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="70">Buyer</th>
						<th rowspan="2" width="100">Job No</th>
						<th rowspan="2" width="110">Style</th>
						<th rowspan="2" width="140">Order No</th>
						<th rowspan="2" width="100">Fab. Contr.</th>
						<th rowspan="2" width="150">Fab. Desc.</th>
						<th rowspan="2" width="40">UOM</th>
						<th rowspan="2" width="100">Color</th>
						<th rowspan="2" width="80">WO. Qty</th>
						<th colspan="4">Receive Details</th>
						<th colspan="4">Issue Details</th>
						<th rowspan="2">Balance</th>
					</tr>
					<tr>
						<th width="90">Receive Qty</th>
						<th width="100">Recv. Return Qty</th>
						<th width="80">Transfer in</th>
						<th width="100">Total Receive</th>

						<th width="90">Issue Qty</th>
						<th width="100">Issue Return Qty</th>
						<th width="80">Transfer out</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:1850px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" id="tbl_issue_status">
					<?
					$i = 1;
					$y = 1;
					$fin_tot_receive_qty = 0;
					$fin_tot_receive_value = 0;
					$fin_tot_fin_issue_qty = 0;
					$fin_total_left_over = 0;
					$fin_total_left_over_balance = 0;
					$fin_dataArrayRecv = array();
					$fin_wo_qty_ArrayRecv = array();
					$fin_wo_qty_ArrayRecv_pi = array();
					$fin_total_wo_qty = 0;
					$fin_total_wo_val = $fin_total_wo_amount = $fin_total_issue_amount = $fin_total_recv_qty = $fin_total_rcv_rtn_qty = $fin_total_transfer_in_qty = $fin_total_net_recv_qty = $total_fin_issue_qty = $fin_total_issue_rtn_qty = $fin_total_transfer_out_qty = $fin_total_net_issue_qty = 0;

					/*$fin_sql_bookingqty_non = sql_select("SELECT sum(b.trim_qty) as wo_qnty,sum(b.amount) as amount,b.trim_group as item_group,b.fabric_color as item_color,b.gmts_color as color_number_id,b.fabric_description as description 
			from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
			where a.company_id=$cbo_company and a.booking_no=b.booking_no  $buyer_id_cond group by  b.trim_group,b.fabric_color,b.gmts_color,b.fabric_description");
			foreach($fin_sql_bookingqty_non as $row)
			{
				$fin_wo_qty_ArrayRecv[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('wo_qnty')];
				$fin_wo_qty_ArrayRecvAmt[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('amount')];
			}*/

					if ($db_type == 0) {
						$fin_sql = "SELECT a.style_ref_no, a.buyer_name, a.job_no, group_concat(b.po_number) as po_number, group_concat(b.id) as po_id,group_concat(b.file_no) as file_no,group_concat(b.grouping) as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity 
				from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $year_cond $pub_date  $brand_cond $season_cond $season_year_cond group by a.job_no, a.style_ref_no, a.buyer_name order by a.id";
					} else {
						$fin_sql = "SELECT a.style_ref_no, a.buyer_name, a.job_no, rtrim(xmlagg(xmlelement(e,b.po_number,',').extract('//text()') order by b.id).GetClobVal(),',') as po_number,
				rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as po_id, rtrim(xmlagg(xmlelement(e,b.file_no,',').extract('//text()') order by b.id).GetClobVal(),',') as file_no, rtrim(xmlagg(xmlelement(e,b.grouping,',').extract('//text()') order by b.id).GetClobVal(),',') as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity 
				from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond
				group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.total_set_qnty order by a.id";
					}
					// echo $fin_sql;die;

					$fin_result = sql_select($fin_sql);
					$fin_all_order_id = "";
					foreach ($fin_result as $row) {
						$fin_all_order_id .= $row[csf("po_id")]->load() . ",";
					}
					$fin_all_order_id = chop($fin_all_order_id, ",");

					$fin_sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.fabric_description_id, c.store_id, a.uom, a.rate, a.color_id, b.po_breakdown_id as po_id, b.quantity, b.order_amount as amount, b.trans_type
			from inv_receive_master c, pro_finish_fabric_rcv_dtls a, order_wise_pro_details b 
			where a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category=3 and c.entry_form=17";
					// a.id=b.dtls_id and // if we want to add this condition then play the script --update PRO_FINISH_FABRIC_RCV_DTLS a, ORDER_WISE_PRO_DETAILS b set a.id=b.dtls_id where b.id in(select a.id  from PRO_FINISH_FABRIC_RCV_DTLS a, ORDER_WISE_PRO_DETAILS b where a.trans_id=b.trans_id and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.dtls_id=0 group by a.id) and a.trans_id=b.trans_id and  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.dtls_id=0;

					$p = 1;
					if ($fin_all_order_id != "") {
						$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
						foreach ($fin_all_order_id_arr as $order_id) {
							if ($p == 1) $fin_sql_recv .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
							else $fin_sql_recv .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
							$p++;
						}
						$fin_sql_recv .= " )";
					}
					//echo $fin_sql_recv;die;			
					$fin_sql_recv_result = sql_select($fin_sql_recv);
					foreach ($fin_sql_recv_result as $row) {
						if ($row[csf('color_id')] == 0) $row[csf('color_id')] = 404;
						if ($row[csf('item_size')] == "") $item_size = 0;
						else $item_size = $row[csf('item_size')];
						$fin_dataArrayRecv[$row[csf('po_id')]] .= $row[csf('fabric_description_id')] . "_" . $row[csf('uom')] . "_" . $row[csf('color_id')] . "_" . $row[csf('rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
					}
					//echo "<pre>";print_r($fin_dataArrayRecv);die;			

					if ($db_type == 0) $null_val = "c.color_number_id,c.item_color,";
					else if ($db_type == 2) $null_val = "nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,";

					$fin_sql_bookingqty = "SELECT b.po_break_down_id as po_id, b.construction, b.copmposition, b.fabric_color_id, b.id as dtls_id, b.grey_fab_qnty as wo_qnty, b.amount, c.lib_yarn_count_deter_id as deter_id
			from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls  c
			where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.company_id=$cbo_company and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					$p = 1;
					if ($fin_all_order_id != "") {
						$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
						foreach ($fin_all_order_id_arr as $order_id) {
							if ($p == 1) $fin_sql_bookingqty .= " and (b.po_break_down_id in(" . implode(',', $order_id) . ")";
							else $fin_sql_bookingqty .= " or b.po_break_down_id  in(" . implode(',', $order_id) . ")";
							$p++;
						}
						$fin_sql_bookingqty .= " )";
					}

					//echo $fin_sql_bookingqty;die;
					$fin_sql_bookingqty_result = sql_select($fin_sql_bookingqty);
					//echo "<pre>";print_r($fin_sql_bookingqty_result);die;
					foreach ($fin_sql_bookingqty_result as $row) {
						$fin_wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('fabric_color_id')]] += $row[csf('wo_qnty')];
						$fin_wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('fabric_color_id')]] += $row[csf('amount')];
					}
					//echo count($fin_wo_qty_ArrayRecv);die;
					//echo "<pre>"; print_r($fin_wo_qty_ArrayRecv);die;

					$fin_issue_qty_sql = "SELECT b.po_breakdown_id, a.color as item_color_id, a.item_size, a.detarmination_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type 
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=3 and a.entry_form=0 and b.entry_form in(19,202,209,258) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; // in(25,49,73,78,112)
					$p = 1;
					if ($fin_all_order_id != "") {
						$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
						foreach ($fin_all_order_id_arr as $order_id) {
							if ($p == 1) $fin_issue_qty_sql .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
							else $fin_issue_qty_sql .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
							$p++;
						}
						$fin_issue_qty_sql .= " )";
					}
					//echo $fin_issue_qty_sql;
					/*
			19   Woven Finish Fabric Issue            25 Trims Issue
			202  Woven Finish Fabric Receive Return   49 Trims Receive Return          
			209  Woven Finish Fabric Issue Return     73 Trims Issue Return
			258  Woven Finish Fabric Transfer Entry   78 Trims Order To Order Transfer Entry, 112 Trims Transfer
			*/
					$fin_issue_qty_sql_result = sql_select($fin_issue_qty_sql);
					$fin_issue_data_arr = array();
					foreach ($fin_issue_qty_sql_result as $row) {
						if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
						$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["rate"] = $row[csf('rate')];
						if ($row[csf('entry_form')] == 19 && $row[csf('trans_type')] == 2) {
							$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_quantity"] += $row[csf('quantity')];
						}
						if ($row[csf('entry_form')] == 202 && $row[csf('trans_type')] == 3) {
							$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_rcv_rtn_quantity"] += $row[csf('quantity')];
						}
						if ($row[csf('entry_form')] == 209 && $row[csf('trans_type')] == 4) {
							$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_rtn_quantity"] += $row[csf('quantity')];
						}
						if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 5) {
							$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_in_quantity"] += $row[csf('quantity')];
						}
						if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 6) {
							$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_out_quantity"] += $row[csf('quantity')];
						}
					}
					/*echo "tipu<pre>";
			print_r($fin_issue_data_arr);die;*/
					//echo "<pre>"; print_r($fin_wo_qty_ArrayRecv[13749][9][11]);die;
					//echo "<pre>"; print_r($fin_issue_data_arr[13749][9][11]);die;

					$fin_req_check = array();
					foreach ($fin_result as $row) {
						$x = 0;
						$z = 0;
						$fin_dataArray = array();
						$fin_uomArray = array();
						$fin_rowspan_array = array();
						$fin_rowspan_color_array = array();
						$fin_all_po_id = $fin_all_po_num = $fin_all_file_no = $fin_all_gropping = "";
						if ($db_type == 0) {
							$fin_all_po_id = implode(",", array_unique(explode(",", $row[csf('po_id')])));
							$fin_all_po_num = implode(",", array_unique(explode(",", $row[csf('po_number')])));
							$fin_all_file_no = implode(",", array_unique(explode(",", $row[csf('file_no')])));
							$fin_all_gropping = implode(",", array_unique(explode(",", $row[csf('grouping')])));
						} else {
							$fin_all_po_id = implode(",", array_unique(explode(",", $row[csf('po_id')]->load())));
							$fin_all_po_num = implode(",", array_unique(explode(",", $row[csf('po_number')]->load())));
							$fin_all_file_no = implode(",", array_unique(explode(",", $row[csf('file_no')]->load())));
							$fin_all_gropping = implode(",", array_unique(explode(",", $row[csf('grouping')]->load())));
						}

						$fin_job_po_id = explode(",", $fin_all_po_id);
						foreach ($fin_job_po_id as $po_id) {
							//echo $fin_dataArrayRecv[$po_id];die; chop($fin_dataArrayRecv[$po_id],",")
							$fin_dataRecv = explode("___", substr($fin_dataArrayRecv[$po_id], 0, -1));
							//print_r($fin_dataRecv);die;
							foreach ($fin_dataRecv as $recvRows) {
								$recvRows = explode("_", $recvRows);
								$fin_fabric_desc = $recvRows[0];
								$fin_order_uom = $recvRows[1];
								$fin_fabr_color = $recvRows[2];
								$fin_fabric_rate = $recvRows[3];
								$fin_quantity = $recvRows[4];
								$fin_recv_basis = $recvRows[5];
								$fin_without_order = $recvRows[6];
								$fin_store_name_id = $recvRows[7];
								$fin_recv_value = $recvRows[8];
								$fin_trans_type = $recvRows[9];
								if ($fin_without_order == "") $fin_without_order = 0;
								//$fin_recv_value=$fin_fabric_rate*$fin_quantity;
								//$recv_data='".$fin_recv_basis."'**'".$fin_without_order."';
								if ($fin_quantity > 0) {
									if ($fin_dataArray[$fin_fabric_desc][$fin_fabr_color]['qty'] == "") {
										$fin_rowspan_array[$fin_fabric_desc] += 1;
										$fin_rowspan_color_array[$fin_fabric_desc][$fin_fabr_color] += 1;

										$z++;
									}

									$fin_dataArray[$fin_fabric_desc][$fin_fabr_color]['qty'] += $fin_quantity;
									$fin_dataArray[$fin_fabric_desc][$fin_fabr_color]['val'] += $fin_recv_value;
									$fin_uomArray[$fin_fabric_desc] = $fin_order_uom;

									($descriptionArray[$fin_fabric_desc] == "") ? $descriptionArray[$fin_fabric_desc] = $fin_item_description : $descriptionArray[$fin_fabric_desc] .= "," . $fin_item_description;
									($storeArray[$fin_fabric_desc] == "") ? $storeArray[$fin_fabric_desc] = $store_name_arr[$fin_store_name_id] : $storeArray[$fin_fabric_desc] .= "," . $store_name_arr[$fin_store_name_id];
								}
							}
						}
						//echo $z;die; echo count($fin_dataArray);
						if ($z > 0) {
							//echo "<pre>";print_r($fin_dataArray);die;
							foreach ($fin_dataArray as $fin_fabric_desc => $fin_item_group_data) {
								$s = 0;
								foreach ($fin_item_group_data as $fin_fabr_color_id => $fin_fabr_color_data) {
									$c = 0;
									$fin_recv_qnty = $fin_fabr_color_data['qty'];
									$fin_recv_value = $fin_fabr_color_data['val'];
									$fin_ord_avg_rate = $fin_recv_value / $fin_recv_qnty;
									$fin_issue_qty = $issue_amount = $fin_rcv_rtn_qty = $fin_issue_rtn_qty = $fin_transfer_in_qty = $fin_transfer_out_qty = $net_fin_recv_qnty = $fin_net_recv_value = $fin_net_issue_qnty = $fin_net_issue_value = $fin_wo_qty = $fin_wo_amount = 0;
									foreach ($fin_job_po_id as $fin_po_id) {
										if ($fin_po_mrr_check[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id] == "") {
											$fin_po_mrr_check[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id] = $fin_po_id;
											$issue_rate = $fin_issue_qty_rate_arr[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id]["rate"];
											$fin_issue_qty += $fin_issue_data_arr[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id]["fin_issue_quantity"];
											$fin_rcv_rtn_qty += $fin_issue_data_arr[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id]["fin_rcv_rtn_quantity"];
											$fin_issue_rtn_qty += $fin_issue_data_arr[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id]["fin_issue_rtn_quantity"];
											$fin_transfer_in_qty += $fin_issue_data_arr[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id]["fin_transfer_in_quantity"];
											$fin_transfer_out_qty += $fin_issue_data_arr[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id]["fin_transfer_out_quantity"];
											$issue_amount += $fin_issue_qty * $issue_rate;


											// $constructtion_arr[$fin_fabric_desc]
											// $composition_arr[$fin_fabric_desc] 'Cotton 90% Polyester 10%'
											//$composition=trim($composition_arr[$fin_fabric_desc]);
											//$fin_wo_qty+=$fin_wo_qty_ArrayRecv[$fin_po_id][$constructtion_arr[$fin_fabric_desc]][$composition][$fin_fabr_color_id];
											$fin_wo_qty += $fin_wo_qty_ArrayRecv[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id];

											//$fin_wo_qty+=$fin_wo_qty_ArrayRecv[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id];
											$fin_wo_amount += $fin_wo_qty_ArrayRecvAmt[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id];
										}
									}

									/*$net_fin_recv_qnty=$fin_recv_qnty+$fin_issue_rtn_qty+$fin_transfer_in_qty;
							$fin_net_recv_value=$net_fin_recv_qnty*$fin_ord_avg_rate;
							$fin_net_issue_qnty=$fin_issue_qty+$fin_rcv_rtn_qty+$fin_transfer_out_qty;
							$fin_net_issue_value=$fin_net_issue_qnty*$fin_ord_avg_rate;*/

									$net_fin_recv_qnty = (($fin_recv_qnty - $fin_rcv_rtn_qty) + $fin_transfer_in_qty);
									// $net_fin_recv_qnty=($fin_recv_qnty-$fin_rcv_rtn_qty);
									$fin_net_recv_value = $net_fin_recv_qnty * $fin_ord_avg_rate;
									$fin_net_issue_qnty = (($fin_issue_qty - $fin_issue_rtn_qty) + $fin_transfer_out_qty);
									$fin_net_issue_value = $fin_net_issue_qnty * $fin_ord_avg_rate;




									if ($y % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";
					?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<?
										if ($x == 0) {
										?>
											<td width="40" rowspan="<? echo $z; ?>"><? echo $y; ?></td>
											<td width="70" rowspan="<? echo $z; ?>">
												<p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p>
											</td>
											<td width="100" rowspan="<? echo $z; ?>">
												<p><? echo $row[csf('job_no')]; ?></p>
											</td>
											<td width="110" rowspan="<? echo $z; ?>">
												<p><? echo $row[csf('style_ref_no')]; ?></p>
											</td>
											<td width="140" rowspan="<? echo $z; ?>" style="word-break:break-all">
												<p><? echo $fin_all_po_num; ?></p>
											</td>
										<?
										}
										if ($s == 0) { //$constructtion_arr $composition_arr
										?>
											<td width="100" rowspan="<? echo $fin_rowspan_array[$fin_fabric_desc]; ?>" title="<? echo $fin_fabric_desc . "==" . $fin_without_order; ?>">
												<p><? echo $constructtion_arr[$fin_fabric_desc]; ?></p>
											</td>
											<td width="150" rowspan="<? echo $fin_rowspan_array[$fin_fabric_desc]; ?>">
												<p><? echo implode(",", array_filter(array_unique(explode(",", $composition_arr[$fin_fabric_desc])))); ?></p>
											</td>
											<td width="40" align="center" rowspan="<? echo $fin_rowspan_array[$fin_fabric_desc]; ?>">
												<p><? echo $unit_of_measurement[$fin_uomArray[$fin_fabric_desc]]; ?></p>
											</td>
										<?
										}
										if ($c == 0) {
										?>
											<td width="100" title="<? echo $fin_fabr_color_id; ?>" rowspan="<? echo $fin_rowspan_color_array[$fin_fabric_desc][$fin_fabr_color_id]; ?>" style="word-break:break-all">
												<p><? echo $color_library[$fin_fabr_color_id]; ?></p>
											</td>
										<?
										}
										?>
										<td width="80" align="right" title="<? echo $fin_po_id . '=' . $constructtion_arr[$fin_fabric_desc] . '=' . $composition_arr[$fin_fabric_desc] . '=' . $fin_fabr_color_id; ?>"><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $fin_fabric_desc; ?>','<? echo $fin_fabr_color_id; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',1,'wo_finish_receive_popup');"><? echo number_format($fin_wo_qty, 2); ?></a></td>

										<td width="90" align="right" title="<? echo "rcv:" . $fin_recv_qnty . " rcv rtn:" . $fin_rcv_rtn_qty . " trans in:" . $fin_transfer_in_qty; ?>"><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $fin_fabric_desc; ?>','<? echo $fin_fabr_color_id; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_receive_popup');"><? echo number_format($fin_recv_qnty, 2); ?></a></td>
										<td width="100" align="right">
											<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $fin_fabric_desc; ?>','<? echo $fin_fabr_color_id; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_rcv_rtn_popup');"><? echo number_format($fin_rcv_rtn_qty, 2); ?></a></p>
										</td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $fin_fabric_desc; ?>','<? echo $fin_fabr_color_id; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_transfer_in_popup');"><? echo number_format($fin_transfer_in_qty, 2); ?></a></p>
										</td>
										<td width="100" align="right">
											<p><? echo number_format($net_fin_recv_qnty, 2); ?></p>
										</td>

										<td width="90" align="right" title="<? echo "issue:" . $fin_issue_qty . " iss rtn:" . $fin_issue_rtn_qty . " trans out:" . $fin_transfer_out_qty; ?>"> <a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $fin_fabric_desc; ?>','<? echo $fin_fabr_color_id; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_issue_popup');"><? echo number_format($fin_issue_qty, 2); ?></a></td>
										<td width="100" align="right">
											<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $fin_fabric_desc; ?>','<? echo $fin_fabr_color_id; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_issue_rtn_popup');"><? echo number_format($fin_issue_rtn_qty, 2); ?></a></p>
										</td>
										<td width="80" align="right">
											<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $fin_fabric_desc; ?>','<? echo $fin_fabr_color_id; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_transfer_out_popup');"><? echo number_format($fin_transfer_out_qty, 2); ?></a></p>
										</td>
										<td width="100" align="right">
											<p><? echo number_format($fin_net_issue_qnty, 2); ?></p>
										</td>

										<td align="right"><? $fin_left_over = $net_fin_recv_qnty - $fin_net_issue_qnty;
															echo number_format($fin_left_over, 2); ?></td>
									</tr>
							<? //echo $fin_fabric_rate.',';

									$fin_total_wo_qty += $fin_wo_qty;
									$fin_total_wo_amount += $fin_wo_amount;
									$fin_total_issue_amount += $fin_net_issue_value;
									$fin_total_wo_val += $fin_wo_qty - $net_fin_recv_qnty;
									$fin_total_recv_value += $fin_net_recv_value;

									$fin_total_recv_qty += $fin_recv_qnty;
									$fin_total_rcv_rtn_qty += $fin_rcv_rtn_qty;
									$fin_total_transfer_in_qty += $fin_transfer_in_qty;
									$fin_total_net_recv_qty += $net_fin_recv_qnty;

									$total_fin_issue_qty += $fin_issue_qty;
									$fin_total_issue_rtn_qty += $fin_issue_rtn_qty;
									$fin_total_transfer_out_qty += $fin_transfer_out_qty;
									$fin_total_net_issue_qty += $fin_net_issue_qnty;

									$fin_total_left_val += $fin_tot_left_val;
									$fin_total_left_over += $fin_left_over;

									$i++;
									$x++;
									$s++;
									$c++;
								}
							}
							$total_order_qty += $row[csf('po_quantity')];
						} else {
							if ($y % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $y; ?></td>
								<td width="70">
									<p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p>
								</td>
								<td width="100">
									<p><? echo $row[csf('job_no')]; ?></p>
								</td>
								<td width="110">
									<p><? echo $row[csf('style_ref_no')]; ?></p>
								</td>
								<td width="140">
									<p><? echo $fin_all_po_num;  ?></p>
								</td>
								<td width="100">&nbsp;</td>
								<td width="150">&nbsp;</td>
								<td width="40" align="center">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80" align="right">&nbsp;</td>

								<td width="90" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>

								<td width="90" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>

								<td align="right">&nbsp;</td>
							</tr>
					<?
							$i++;
						}
						$y++;
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($fin_total_wo_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_recv_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_rcv_rtn_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_transfer_in_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_net_recv_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_fin_issue_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_issue_rtn_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_transfer_out_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_net_issue_qty, 2); ?></td>
						<td align="right"><? echo number_format($fin_total_left_over, 2); ?></td>
					</tr>
				</table>
			</div>

			<br> <!-- Trims Part -->

			<h2 style="float: left; font-size: 20px;">Trims</h2>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1846" class="rpt_table">
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="70">Buyer</th>
						<th rowspan="2" width="100">Job No</th>
						<th rowspan="2" width="110">Style</th>
						<th rowspan="2" width="140">Order No</th>
						<th rowspan="2" width="100">Item Group</th>
						<th rowspan="2" width="150">Item Description</th>
						<th rowspan="2" width="40">UOM</th>
						<th rowspan="2" width="100">Colour</th>
						<th rowspan="2" width="60">Size</th>
						<th rowspan="2" width="80">WO. Qty</th>
						<th colspan="4">Receive Details</th>
						<th colspan="4">Issue Details</th>
						<th rowspan="2">Balance</th>
					</tr>
					<tr>
						<th width="90">Receive Qty</th>
						<th width="100">Recv. Return Qty</th>
						<th width="80">Transfer in</th>
						<th width="100">Total Receive</th>

						<th width="90">Issue Qty</th>
						<th width="100">Issue Return Qty</th>
						<th width="80">Transfer out</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:1850px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body2">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" id="tbl_issue_status2">
					<?
					$i = 1;
					$y = 1;
					$tot_receive_qty = 0;
					$tot_receive_value = 0;
					$tot_issue_qty = 0;
					$total_left_over = 0;
					$total_left_over_balance = 0;
					$dataArrayRecv = array();
					$wo_qty_ArrayRecv = array();
					$wo_qty_ArrayRecv_pi = array();
					$total_wo_qty = 0;
					$total_wo_val = $total_wo_amount = $total_issue_amount = $total_recv_qty = $total_recv_rtn_qty = $total_transfer_in_qty = $total_recv_net_qty = $total_issue_qty = $total_issue_rtn_qty = $total_transfer_out_qty = $total_issue_net_qty = 0;

					$sql_bookingqty_non = sql_select("SELECT sum(b.trim_qty) as wo_qnty,sum(b.amount) as amount,b.trim_group as item_group,b.fabric_color as item_color,b.gmts_color as color_number_id,b.fabric_description as description 
			from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
			where a.company_id=$cbo_company and a.booking_no=b.booking_no  $buyer_id_cond 
			group by  b.trim_group,b.fabric_color,b.gmts_color,b.fabric_description");
					foreach ($sql_bookingqty_non as $row) {
						$wo_qty_ArrayRecv[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]] = $row[csf('wo_qnty')];
						$wo_qty_ArrayRecvAmt[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]] = $row[csf('amount')];
					}

					if ($db_type == 0) {
						$sql = "SELECT a.style_ref_no, a.buyer_name, a.job_no, group_concat(b.po_number) as po_number, group_concat(b.id) as po_id,group_concat(b.file_no) as file_no,group_concat(b.grouping) as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity 
				from wo_po_details_master a, wo_po_break_down b 
				where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond group by a.job_no, a.style_ref_no, a.buyer_name order by a.id";
					} else {
						$sql = "SELECT a.style_ref_no, a.buyer_name, a.job_no, rtrim(xmlagg(xmlelement(e,b.po_number,',').extract('//text()') order by b.id).GetClobVal(),',') as po_number,
				rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as po_id, rtrim(xmlagg(xmlelement(e,b.file_no,',').extract('//text()') order by b.id).GetClobVal(),',') as file_no, rtrim(xmlagg(xmlelement(e,b.grouping,',').extract('//text()') order by b.id).GetClobVal(),',') as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity 
				from wo_po_details_master a, wo_po_break_down b 
				where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond
				group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.total_set_qnty order by a.id";
					}
					//echo $sql;die;
					$result = sql_select($sql);
					$all_order_id = "";
					foreach ($result as $row) {
						$all_order_id .= $row[csf("po_id")]->load() . ",";
					}
					$all_order_id = chop($all_order_id, ",");

					$sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.item_group_id, a.item_description, c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , b.order_amount as amount, b.trans_type
			from inv_receive_master c, inv_trims_entry_dtls a, order_wise_pro_details b 
			where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 "; //and a.prod_id=21968
					//echo $sql_recv;die;

					$p = 1;
					if ($all_order_id != "") {
						$all_order_id_arr = array_chunk(array_unique(explode(",", $all_order_id)), 999);
						foreach ($all_order_id_arr as $order_id) {
							if ($p == 1) $sql_recv .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
							else $sql_recv .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
							$p++;
						}
						$sql_recv .= " )";
					}
					//echo $sql_recv;

					$sql_recv_result = sql_select($sql_recv);
					foreach ($sql_recv_result as $row) {
						if ($row[csf('item_color')] == 0) $row[csf('item_color')] = 404;
						if ($row[csf('item_size')] == "") $item_size = 0;
						else $item_size = $row[csf('item_size')];
						$dataArrayRecv[$row[csf('po_id')]] .= $row[csf('item_group_id')] . "_" . $row[csf('order_uom')] . "_" . $row[csf('item_color')] . "_" . $item_size . "_" . $row[csf('cons_rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('gmts_color_id')] . "_" . $row[csf('item_description')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
					}
					//echo "<pre>";print_r($dataArrayRecv);die;			

					if ($db_type == 0) $null_val = "c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
					else if ($db_type == 2) $null_val = "nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,nvl(c.item_size,0) as item_size,";

					$sql_bookingqty = "SELECT c.cons as wo_qnty,c.amount,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity 
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c 
			where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.company_id=$cbo_company and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					$p = 1;
					if ($all_order_id != "") {
						$all_order_id_arr = array_chunk(array_unique(explode(",", $all_order_id)), 999);
						foreach ($all_order_id_arr as $order_id) {
							if ($p == 1) $sql_bookingqty .= " and (b.po_break_down_id in(" . implode(',', $order_id) . ")";
							else $sql_bookingqty .= " or b.po_break_down_id  in(" . implode(',', $order_id) . ")";
							$p++;
						}
						$sql_bookingqty .= " )";
					}

					//echo $sql_bookingqty;die;
					//echo $sql_bookingqty;die;
					$sql_bookingqty_result = sql_select($sql_bookingqty);
					//echo "<pre>";print_r($sql_bookingqty_result);die;
					foreach ($sql_bookingqty_result as $row) {
						if ($row[csf('item_size')] == "" || $row[csf('item_size')] == "0") $item_size = 0;
						else $item_size = $row[csf('item_size')];
						$wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size] += $row[csf('wo_qnty')];
						$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size] += $row[csf('amount')];
					}
					//echo count($wo_qty_ArrayRecv);die;
					//echo "<pre>"; print_r($wo_qty_ArrayRecv);die;

					$issue_qty_sql = "SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type 
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4 and a.entry_form=24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

					$p = 1;
					if ($all_order_id != "") {
						$all_order_id_arr = array_chunk(array_unique(explode(",", $all_order_id)), 999);
						foreach ($all_order_id_arr as $order_id) {
							if ($p == 1) $issue_qty_sql .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
							else $issue_qty_sql .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
							$p++;
						}
						$issue_qty_sql .= " )";
					}
					//echo $issue_qty_sql;
					$issue_qty_sql_result = sql_select($issue_qty_sql);
					$issue_data_arr = array();
					foreach ($issue_qty_sql_result as $row) {
						if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
						if ($row[csf('item_size')] == "") $item_size_id = 0;
						else $item_size_id = $row[csf('item_size')];
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rate"] = $row[csf('rate')];
						if ($row[csf('entry_form')] == 25 && $row[csf('trans_type')] == 2) {
							$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_quantity"] += $row[csf('quantity')];
						}
						if ($row[csf('entry_form')] == 49 && $row[csf('trans_type')] == 3) {
							$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_quantity"] += $row[csf('quantity')];
						}
						if ($row[csf('entry_form')] == 73 && $row[csf('trans_type')] == 4) {
							$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_quantity"] += $row[csf('quantity')];
						}
						if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 5) {
							$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"] += $row[csf('quantity')];
						}
						if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 6) {
							$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"] += $row[csf('quantity')];
						}
					}
					//echo "tipu<pre>";
					//print_r($issue_data_arr[17508][220]);die;
					//echo "<pre>"; print_r($wo_qty_ArrayRecv[13749][9][11]);die;
					//echo "<pre>"; print_r($issue_data_arr[13749][9][11]);die;

					$req_check = array();
					foreach ($result as $row) {
						$x = 0;
						$z = 0;
						$dataArray = array();
						$uomArray = array();
						$rowspan_array = array();
						$rowspan_color_array = array();
						$all_po_id = $all_po_num = $all_file_no = $all_gropping = "";
						if ($db_type == 0) {
							$all_po_id = implode(",", array_unique(explode(",", $row[csf('po_id')])));
							$all_po_num = implode(",", array_unique(explode(",", $row[csf('po_number')])));
							$all_file_no = implode(",", array_unique(explode(",", $row[csf('file_no')])));
							$all_gropping = implode(",", array_unique(explode(",", $row[csf('grouping')])));
						} else {
							$all_po_id = implode(",", array_unique(explode(",", $row[csf('po_id')]->load())));
							$all_po_num = implode(",", array_unique(explode(",", $row[csf('po_number')]->load())));
							$all_file_no = implode(",", array_unique(explode(",", $row[csf('file_no')]->load())));
							$all_gropping = implode(",", array_unique(explode(",", $row[csf('grouping')]->load())));
						}

						$job_po_id = explode(",", $all_po_id);
						foreach ($job_po_id as $po_id) {
							//echo $dataArrayRecv[$po_id];die; chop($dataArrayRecv[$po_id],",")
							$dataRecv = explode("___", substr($dataArrayRecv[$po_id], 0, -1));
							//print_r($dataRecv);die;
							foreach ($dataRecv as $recvRow) {
								$recvRow = explode("_", $recvRow);
								$item_group_id = $recvRow[0];
								$order_uom = $recvRow[1];
								$item_color = $recvRow[2];
								$item_size = $recvRow[3];
								if ($item_size == "") $item_size = 0;
								$cons_rate = $recvRow[4];
								$quantity = $recvRow[5];
								$recv_basis = $recvRow[6];
								$without_order = $recvRow[7];
								$gmts_color = $recvRow[8];
								$item_description = $recvRow[9];
								$store_name_id = $recvRow[10];
								$recv_value = $recvRow[11];
								$trans_type = $recvRow[12];


								if ($without_order == "") $without_order = 0;

								//$recv_value=$cons_rate*$quantity;
								//$recv_data='".$recv_basis."'**'".$without_order."';						

								if ($quantity > 0) {
									if ($dataArray[$item_group_id][$item_color][$item_size]['qty'] == "") {
										$rowspan_array[$item_group_id] += 1;
										$rowspan_color_array[$item_group_id][$item_color] += 1;

										$z++;
									}

									$dataArray[$item_group_id][$item_color][$item_size]['qty'] += $quantity;
									$dataArray[$item_group_id][$item_color][$item_size]['val'] += $recv_value;
									$uomArray[$item_group_id] = $order_uom;


									($descriptionArray[$item_group_id] == "") ? $descriptionArray[$item_group_id] = $item_description : $descriptionArray[$item_group_id] .= "," . $item_description;
									($storeArray[$item_group_id] == "") ? $storeArray[$item_group_id] = $store_name_arr[$store_name_id] : $storeArray[$item_group_id] .= "," . $store_name_arr[$store_name_id];
								}
							}
						}
						//echo $z;die; echo count($dataArray);
						if ($z > 0) {
							//echo "<pre>";print_r($dataArray);die;
							foreach ($dataArray as $item_group_id => $item_group_data) {
								$s = 0;
								foreach ($item_group_data as $item_color_id => $item_color_data) {
									$c = 0;

									foreach ($item_color_data as $item_size => $item_size_data) {
										$recv_qnty = $item_size_data['qty'];
										$recv_value = $item_size_data['val'];
										$ord_avg_rate = $recv_value / $recv_qnty;
										$issue_qty = $issue_amount = $rcv_rtn_qty = $issue_rtn_qty = $transfer_in_qty = $transfer_out_qty = $net_recv_qnty = $net_recv_value = $net_issue_qnty = $net_issue_value = $wo_qty = $wo_amount = 0;
										foreach ($job_po_id as $po_id) {
											if ($po_mrr_check[$po_id][$item_group_id][$item_color_id][$item_size] == "") {
												$po_mrr_check[$po_id][$item_group_id][$item_color_id][$item_size] = $po_id;
												$issue_rate = $issue_qty_rate_arr[$po_id][$item_group_id][$item_color_id][$item_size]["rate"];
												$issue_qty += $issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["issue_quantity"];
												$rcv_rtn_qty += $issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["rcv_rtn_quantity"];
												$issue_rtn_qty += $issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["issue_rtn_quantity"];
												$transfer_in_qty += $issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["transfer_in_quantity"];
												$transfer_out_qty += $issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["transfer_out_quantity"];
												$issue_amount += $issue_qty * $issue_rate;

												$wo_qty += $wo_qty_ArrayRecv[$po_id][$item_group_id][$item_color_id][$item_size];
												$wo_amount += $wo_qty_ArrayRecvAmt[$po_id][$item_group_id][$item_color_id][$item_size];
											}
										}

										/*$net_recv_qnty=$recv_qnty+$issue_rtn_qty+$transfer_in_qty;
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=$issue_qty+$rcv_rtn_qty+$transfer_out_qty;
								$net_issue_value=$net_issue_qnty*$ord_avg_rate;*/

										$net_recv_qnty = (($recv_qnty - $rcv_rtn_qty) + $transfer_in_qty);
										// $net_recv_qnty=($recv_qnty-$rcv_rtn_qty);
										$net_recv_value = $net_recv_qnty * $ord_avg_rate;
										$net_issue_qnty = (($issue_qty - $issue_rtn_qty) + $transfer_out_qty);
										$net_issue_value = $net_issue_qnty * $ord_avg_rate;

										if ($y % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";
					?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<?
											if ($x == 0) {

											?>
												<td width="40" rowspan="<? echo $z; ?>"><? echo $y; ?></td>
												<td width="70" rowspan="<? echo $z; ?>">
													<p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p>
												</td>
												<td width="100" rowspan="<? echo $z; ?>">
													<p><? echo $row[csf('job_no')]; ?></p>
												</td>
												<td width="110" rowspan="<? echo $z; ?>">
													<p><? echo $row[csf('style_ref_no')]; ?></p>
												</td>
												<td width="140" rowspan="<? echo $z; ?>" style="word-break:break-all">
													<p><? echo $all_po_num; ?></p>
												</td>
											<?
											}
											if ($s == 0) {
											?>
												<td width="100" rowspan="<? echo $rowspan_array[$item_group_id]; ?>" title="<? echo $item_group_id . "==" . $without_order; ?>">
													<p><? echo $trim_group[$item_group_id]; ?></p>
												</td>
												<td width="150" rowspan="<? echo $rowspan_array[$item_group_id]; ?>">
													<p><? echo implode(",", array_filter(array_unique(explode(",", $descriptionArray[$item_group_id])))); ?></p>
												</td>
												<td width="40" align="center" rowspan="<? echo $rowspan_array[$item_group_id]; ?>">
													<p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p>
												</td>
											<?
											}
											if ($c == 0) {
											?>
												<td width="100" title="<? echo $item_color_id; ?>" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" style="word-break:break-all">
													<p><? echo $color_library[$item_color_id]; ?></p>
												</td>
											<?
											}
											?>
											<td width="60" title="<? echo $po_id . "==" . $item_group_id . "==" . $item_color_id . "==" . $item_size; ?>">
												<p><? if ($item_size == "0") echo "&nbsp;";
													else echo $item_size; ?>&nbsp;</p>
											</td>
											<td width="80" align="right"><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',1,'wo_receive_popup');"><? echo number_format($wo_qty, 2); ?></a></td>

											<td width="90" align="right" title="<? echo "rcv:" . $recv_qnty . " rcv rtn:" . $rcv_rtn_qty . " trans in:" . $transfer_in_qty; ?>"><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><? echo number_format($recv_qnty, 2); ?></a></td>
											<td width="100" align="right">
												<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_rtn_popup');"><? echo number_format($rcv_rtn_qty, 2); ?></a></p>
											</td>
											<td width="80" align="right">
												<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'transfer_in_popup');"><? echo number_format($transfer_in_qty, 2); ?></a></p>
											</td>
											<td width="100" align="right">
												<p><? echo number_format($net_recv_qnty, 2); ?></p>
											</td>

											<td width="90" align="right" title="<? echo "issue:" . $issue_qty . " iss rtn:" . $issue_rtn_qty . " trans out:" . $transfer_out_qty; ?>"><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><? echo number_format($issue_qty, 2); ?></a></td>
											<td width="100" align="right">
												<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_rtn_popup');"><? echo number_format($issue_rtn_qty, 2); ?></a></p>
											</td>
											<td width="80" align="right">
												<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'transfer_out_popup');"><? echo number_format($transfer_out_qty, 2); ?></a></p>
											</td>
											<td width="100" align="right">
												<p><? echo number_format($net_issue_qnty, 2); ?></p>
											</td>

											<td align="right"><? $left_over = $net_recv_qnty - $net_issue_qnty;
																echo number_format($left_over, 2); ?></td>
										</tr>
							<? //echo $cons_rate.',';

										$total_wo_qty += $wo_qty;
										$total_wo_amount += $wo_amount;
										$total_issue_amount += $net_issue_value;
										$total_wo_val += $wo_qty - $net_recv_qnty;
										$total_recv_value += $net_recv_value;

										$total_recv_qty += $recv_qnty;
										$total_recv_rtn_qty += $rcv_rtn_qty;
										$total_transfer_in_qty += $transfer_in_qty;
										$total_recv_net_qty += $net_recv_qnty;

										$total_issue_qty += $issue_qty;
										$total_issue_rtn_qty += $issue_rtn_qty;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_issue_net_qty += $net_issue_qnty;

										$total_left_val += $tot_left_val;
										$total_left_over += $left_over;

										$i++;
										$x++;
										$s++;
										$c++;
									}
								}
							}
							$total_order_qty += $row[csf('po_quantity')];
						} else {
							if ($y % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $y; ?></td>
								<td width="70">
									<p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p>
								</td>
								<td width="100">
									<p><? echo $row[csf('job_no')]; ?></p>
								</td>
								<td width="110">
									<p><? echo $row[csf('style_ref_no')]; ?></p>
								</td>
								<td width="140">
									<p><? echo $all_po_num;  ?></p>
								</td>
								<td width="100">&nbsp;</td>
								<td width="150">&nbsp;</td>
								<td width="40" align="center">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="80" align="right">&nbsp;</td>

								<td width="90" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>

								<td width="90" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80" align="right">&nbsp;</td>
								<td width="100">&nbsp;</td>

								<td align="right">&nbsp;</td>
							</tr>
					<?
							$i++;
						}
						$y++;
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total</td>
						<td align="right"><? echo number_format($total_wo_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_recv_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_recv_rtn_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_recv_net_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_issue_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_issue_rtn_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_issue_net_qty, 2); ?></td>
						<td align="right"><? echo number_format($total_left_over, 2); ?></td>
					</tr>
				</table>
			</div>

		</fieldset>

		<br>
		<?
	} else if ($report_type == 2) {



		$sql_po = "SELECT a.id as job_id, a.style_ref_no, a.buyer_name, a.job_no,b.po_number,b.id as po_id,(a.total_set_qnty*b.po_quantity) as po_quantity 
	 from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company and a.status_active=1 and a.is_deleted=0  $buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond order by a.id";
		// echo $sql_po;die;
		$sql_po_result = sql_select($sql_po);
		foreach ($sql_po_result as $row) {
			$poIdArr[$row[csf('po_id')]] = $row[csf('po_id')];
			$poJobArr[$row[csf('po_id')]] = $row[csf('job_no')];
			$JobIdArr[$row[csf('job_id')]] = $row[csf('job_id')];

			$jobNoArr[$row[csf('job_no')]]['buyer'] = $row[csf('buyer_name')];
			$jobNoArr[$row[csf('job_no')]]['style'] = $row[csf('style_ref_no')];
			$jobNoArr[$row[csf('job_no')]]['po_number'] .= $row[csf('po_number')] . ',';
			$jobNoArr[$row[csf('job_no')]]['po_id'] .= $row[csf('po_id')] . ',';
			$jobNoArr[$row[csf('job_no')]]['po_quantity'] += $row[csf('po_quantity')];
		} //po_break_down_id
		$po_cond = where_con_using_array($poIdArr, 0, 'c.po_break_down_id');
		$po_cond2 = where_con_using_array($poIdArr, 0, 'b.po_break_down_id');
		$po_cond3 = where_con_using_array($poIdArr, 0, 'b.po_breakdown_id');
		$job_cond = where_con_using_array($JobIdArr, 0, 'b.job_id');

		$condition = new condition();
		$condition->company_name("=$cbo_company");
		if (str_replace("'", "", $cbo_buyer_name) > 0) {
			$condition->buyer_name("=$cbo_buyer");
		}
		$style_ref = str_replace("'", "", $txt_style_id);
		if (str_replace("'", "", $style_ref) != '') {
			$condition->style_ref_no("='$style_ref'");
		}
		$order_id = str_replace("'", "", $txt_order_no_id);
		if (str_replace("'", "", $order_id) != '') {
			$condition->po_id_in("$order_id");
		}
		if (str_replace("'", "", $txt_order_no) != '' && $order_id == '') {
			$condition->po_number("=$txt_order_no");
		}
		if (str_replace("'", "", $season) > 0) {
			$condition->season("=$season");
		}
		if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") {
			$start_date = (str_replace("'", "", $txt_date_from));
			$end_date = (str_replace("'", "", $txt_date_to));
			$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
		}
		$poIdArrCond = implode(',', $poIdArr);
		if ($poIdArrCond) {
			$condition->po_id_in("$poIdArrCond");
		}
		$condition->init();

		$fabric = new fabric($condition);
		//echo $fabric->getQuery();die;
		//$fabric_qty_arr=$fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
		// $fabric_qty_arr=$fabric->getQtyArray_by_orderFabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
		$fabric_qty_arr = $fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
		//echo "<pre>";print_r($fabric_qty_arr);die;
		$trims = new trims($condition);
		$trims_qty_arr = $trims->getQtyArray_by_jobAndPrecostdtlsid();
		/* echo '<pre>';
	 print_r($fabric_qty_arr);die;  */
		// if($db_type==0) $null_val="c.color_number_id,c.item_color,";
		//else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,";			

		$fin_sql_booking = "SELECT b.po_break_down_id as po_id, b.construction, b.copmposition,b.gmts_color_id, b.fabric_color_id, b.id as dtls_id, b.grey_fab_qnty as wo_qnty, b.amount, c.lib_yarn_count_deter_id as deter_id,c.job_no
			from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls  c
			where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.company_id=$cbo_company and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond2";


		//echo $fin_sql_bookingqty;die;
		$fin_sql_booking_result = sql_select($fin_sql_booking);
		//echo "<pre>";print_r($fin_sql_bookingqty_result);die;
		foreach ($fin_sql_booking_result as $row) {
			//$fin_wo_qtyArr[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('gmts_color_id')]]+=$row[csf('wo_qnty')];
			$fin_wo_qtyArr[$row[csf('job_no')]][$row[csf('gmts_color_id')]] += $row[csf('wo_qnty')];
			//$fin_wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('fabric_color_id')]]+=$row[csf('amount')];
		}
		$fin_sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.fabric_description_id, c.store_id, a.uom, a.rate, a.color_id, b.po_breakdown_id as po_id, b.quantity, b.order_amount as amount, b.trans_type
			from inv_receive_master c, pro_finish_fabric_rcv_dtls a, order_wise_pro_details b 
			where a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category=3 and c.entry_form=17 $po_cond3";

		$fin_sql_recv_result = sql_select($fin_sql_recv);
		foreach ($fin_sql_recv_result as $row) {
			$poJob = $poJobArr[$row[csf('po_id')]];

			if ($row[csf('color_id')] == 0) $row[csf('color_id')] = 404;
			$fin_dataArrayRecv[$poJob][$row[csf('color_id')]] += $row[csf('quantity')];
		}

		$fin_issue_qty_sql = "SELECT b.po_breakdown_id, a.color as item_color_id, a.item_size, a.detarmination_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type 
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=3 and a.entry_form=0 and b.entry_form in(19,202,209,258) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond3"; // in(25,49,73,78,112)

		//echo $fin_issue_qty_sql;
		/*
			19   Woven Finish Fabric Issue            25 Trims Issue
			202  Woven Finish Fabric Receive Return   49 Trims Receive Return          
			209  Woven Finish Fabric Issue Return     73 Trims Issue Return
			258  Woven Finish Fabric Transfer Entry   78 Trims Order To Order Transfer Entry, 112 Trims Transfer
			*/
		$fin_issue_qty_sql_result = sql_select($fin_issue_qty_sql);
		$fin_issue_data_arr = array();
		foreach ($fin_issue_qty_sql_result as $row) {
			if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
			$poJob = $poJobArr[$row[csf('po_breakdown_id')]];
			$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["rate"] = $row[csf('rate')];
			if ($row[csf('entry_form')] == 19 && $row[csf('trans_type')] == 2) {
				$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_quantity"] += $row[csf('quantity')];
				$fin_issue_qty_arr[$poJob][$row[csf('item_color_id')]]["fin_issue_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 202 && $row[csf('trans_type')] == 3) {
				$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_rcv_rtn_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 209 && $row[csf('trans_type')] == 4) {
				$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_rtn_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 5) {
				$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_in_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 6) {
				$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_out_quantity"] += $row[csf('quantity')];
			}
		}

		$sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.item_group_id, d.item_description as item_description, a.brand_supplier,c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , b.order_amount as amount, b.trans_type
			from inv_receive_master c, inv_trims_entry_dtls a, order_wise_pro_details b,product_details_master d 
			where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and d.id=b.prod_id and a.prod_id=d.id  and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond3"; //and a.prod_id=21968
		//echo $sql_recv;die;



		$sql_recv_result = sql_select($sql_recv);
		foreach ($sql_recv_result as $row) {
			//if($row[csf('item_color')]==0) $row[csf('item_color')]=404;
			if ($row[csf('brand_supplier')] == '') $row[csf('brand_supplier')] = 0;

			$poJob = $poJobArr[$row[csf('po_id')]];
			$trim_data = $row[csf('item_group_id')];

			$dataArrayRecvQty[$poJob][$trim_data] += $row[csf('quantity')];
		}



		$issue_qty_sql = "SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type,a.item_description,d.brand_supplier
			from product_details_master a , order_wise_pro_details b, inv_transaction c,inv_trims_issue_dtls d
			where a.id=b.prod_id and d.id=b.dtls_id and c.id=d.trans_id and d.prod_id=a.id and b.trans_id=c.id and a.item_category_id=4 and  b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond3";


		//echo $issue_qty_sql;
		$issue_qty_sql_result = sql_select($issue_qty_sql);
		$issue_data_arr = array();
		foreach ($issue_qty_sql_result as $row) {
			if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
			//if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];
			$trim_data = $row[csf('item_group_id')];
			$poJob = $poJobArr[$row[csf('po_breakdown_id')]];
			//echo $trim_data.'<br>';
			//$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rate"]=$row[csf('rate')];
			if ($row[csf('entry_form')] == 25 && $row[csf('trans_type')] == 2) {
				//$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_quantity"]+=$row[csf('quantity')];
				$issue_data_arr2[$poJob][$trim_data]["issue_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 49 && $row[csf('trans_type')] == 3) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 73 && $row[csf('trans_type')] == 4) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 5) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 6) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"] += $row[csf('quantity')];
			}
		}
		$sql_bookingqty = "SELECT c.cons as wo_qnty,c.amount,b.id as dtls_id,c.id,b.trim_group as item_group, c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity 
			from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c 
			where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.company_id=$cbo_company and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond2 ";

		$sql_bookingqty_result = sql_select($sql_bookingqty);
		//echo "<pre>";print_r($sql_bookingqty_result);die;
		foreach ($sql_bookingqty_result as $row) {
			if ($row[csf('brand_supplier')] == '') $row[csf('brand_supplier')] = 0;

			$poJob = $poJobArr[$row[csf('po_id')]];
			$trim_data = $row[csf('item_group')];

			$wo_qty_ArrayRecv[$poJob][$trim_data] += $row[csf('wo_qnty')];
			//$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('amount')];
		}
		//	print_r($wo_qty_ArrayRecv);




		$sql_pre_fab = "SELECT a.id as job_id,a.job_no, a.style_ref_no, a.buyer_name,b.uom,b.id as fab_dtls_id,b.lib_yarn_count_deter_id as deter_id,b.fabric_description,c.po_break_down_id as po_id,c.color_number_id as color_id
	 from wo_po_details_master a, wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_no=b.job_no and c.pre_cost_fabric_cost_dtls_id=b.id and a.id=b.job_id and a.company_name=$cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $style  $year_cond $brand_cond $season_cond $season_year_cond $po_cond order by a.id";
		// echo $sql_po;die;
		$sql_pre_fab_result = sql_select($sql_pre_fab);
		$totQty = 0;
		foreach ($sql_pre_fab_result as $row) {
			$po_id = $row[csf('po_id')];
			$fab_woven_qty = array_sum($fabric_qty_arr['woven']['grey'][$row[csf('fab_dtls_id')]][$row[csf('color_id')]]) + array_sum($fabric_qty_arr['woven']['finish'][$row[csf('fab_dtls_id')]][$row[csf('color_id')]]);
			$fin_wo_qty = $fin_wo_qtyArr[$po_id][$row[csf('deter_id')]][$row[csf('color_id')]];
			$fabricArr[$row[csf('job_no')]][$row[csf('color_id')]]['desc'] .= $row[csf('fabric_description')] . ',';
			$fabricArr[$row[csf('job_no')]][$row[csf('color_id')]]['uom'] .= $unit_of_measurement[$row[csf('uom')]] . ',';
			$fabricArr[$row[csf('job_no')]][$row[csf('color_id')]]['fab_req_Qty'] = $fab_woven_qty;
			$fabricArr[$row[csf('job_no')]][$row[csf('color_id')]]['po_id'] .= $po_id . ',';
			$fabricArr[$row[csf('job_no')]][$row[csf('color_id')]]['type'] = 'fabric';
			$totQty += $fin_wo_qty;
		}

		$sql_pre_trim = "SELECT a.id as job_id,a.job_no, a.style_ref_no, a.buyer_name,b.cons_uom as uom,b.seq,b.id as trim_dtls_id,b.description,b.brand_sup_ref,b.trim_group,c.trim_type
	 from wo_po_details_master a, wo_pre_cost_trim_cost_dtls b,lib_item_group c where a.id=b.job_id and c.id=b.trim_group   and a.company_name=$cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond $buyer_id_cond $style  $year_cond $brand_cond $season_cond $season_year_cond  order by b.seq";
		// echo $sql_po;die;
		$sql_pre_trim_result = sql_select($sql_pre_trim);
		$totTrimQty = 0;
		foreach ($sql_pre_trim_result as $row) {

			//if($row[csf('brand_sup_ref')]=='') $row[csf('brand_sup_ref')]=0;
			$trim_typeId = $row[csf('trim_type')];
			$trim_data = $row[csf('trim_group')];

			if ($trim_typeId == 1) $trimType = 'Sewing';
			else $trimType = 'Finish';
			//$poIdArr[$row[csf('po_id')]]=$row[csf('po_id')];
			$trims_qty = $trims_qty_arr[$row[csf('job_no')]][$row[csf('trim_dtls_id')]];
			$trimArr[$row[csf('job_no')]][$trim_data]['desc'] .= $row[csf('description')] . ',';
			$trimArr[$row[csf('job_no')]][$trim_data]['uom'] .= $unit_of_measurement[$row[csf('uom')]] . ',';
			$trimArr[$row[csf('job_no')]][$trim_data]['trim_req_Qty'] += $trims_qty;
			$trimArr[$row[csf('job_no')]][$trim_data]['trim_type'] = $trimType;
			$trimArr[$row[csf('job_no')]][$trim_data]['type'] = 'trims';
			// $totTrimQty+=$trims_qty;
		}

		//echo $totTrimQty;
		$fin_wo_qty = 0;
		foreach ($jobNoArr as $job_no => $job_val) {
			foreach ($fabricArr[$job_no] as $color_id => $row) {
				$fin_wo_qty = $fin_wo_qtyArr[$job_no][$color_id];
				$fabricQtyArr[$job_no][$color_id]['fin_wo_qty'] += $fin_wo_qty;
			}
		}
		//echo $fin_wo_qty.'D';

		foreach ($jobNoArr as $job_no => $job_val) {
			$width = 1030;
		?>
			<div style="width:<?
								echo $width; ?>px;">
				<fieldset style="width:<? echo $width; ?>px;">
					<!-- Fabric Part -->
					<table width="<? echo $width; ?>">
						<tr class="form_caption">
							<td colspan="13" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
						</tr>
						<tr class="form_caption">
							<td colspan="13" align="center"><? echo $company_library[$cbo_company]; ?></td>
						</tr>
						<?
						//$jobNoArr[$row[csf('job_no')]]['po_id'];
						$po_id = rtrim($job_val['po_id'], ',');
						$all_po_id = implode(',', array_unique(explode(',', $po_id)));

						$poNo = rtrim($job_val['po_number'], ',');
						$po_numbers = implode(',', array_unique(explode(',', $poNo)));

						$head_summary = "Buyer Name :" . $buyer_arr[$job_val['buyer']];
						$head_summary .= ", &nbsp;Style:" . $job_val['style'];
						$head_summary .= ",&nbsp;Job No:" . $job_no;
						$head_summary .= ",&nbsp;Gmts Qty:" . $job_val['po_quantity'];
						$head_summary .= ",&nbsp;Order No:" . $po_numbers;
						?>
						<tr>
							<td colspan="13" align="center">
								<p> <? echo $head_summary; ?></p>
							</td>
						</tr>


					</table>

					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width + 20; ?>" class="rpt_table">

						<thead>
							<tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="130">Item Name</th>
								<th rowspan="2" width="50">Uom</th>
								<th rowspan="2" width="110">Color</th>
								<th rowspan="2" width="80">Req Qty</th>
								<th rowspan="2" width="80">WO Qty</th>
								<th colspan="3">Received Status</th>
								<th colspan="3">Issue Status</th>

								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="80">Receive Qty</th>
								<th width="50">%</th>
								<th width="80">Receive Bal</th>

								<th width="80">Issue Qty</th>
								<th width="50">%</th>
								<th width="80"> Issue Balance</th>
							</tr>
						</thead>
					</table>
					<div style="width:<? echo $width + 20; ?>px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="tbl_status">

							<?
							$f = 1;
							$tot_wo_fab_qty = $tot_pre_fab_qty = $tot_fin_Recv = $tot_recv_bal = $total_issue_qty = $tot_issue_bal = 0;
							foreach ($fabricArr[$job_no] as $color_id => $row) {
								$fabric = $row['type'];
								if ($fabric == 'fabric') {
									$desc = rtrim($row['desc'], ',');
									$fab_desc = implode(',', array_unique(explode(',', $desc)));
									$uomId = rtrim($row['uom'], ',');
									//echo $uomId.'D';
									$uoms = implode(',', array_unique(explode(',', $uomId)));
									$fin_wo_qty = $fabricQtyArr[$job_no][$color_id]['fin_wo_qty'];
									$fin_Recv = $fin_dataArrayRecv[$job_no][$color_id];
									$fin_issueQty = $fin_issue_qty_arr[$job_no][$color_id]["fin_issue_quantity"];
								}
								//$fin_issue_qty_arr[$poJob][$row[csf('item_color_id')]]["fin_issue_quantity"]

								if ($f % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $f; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $f; ?>">

									<td width="30"><? echo $f; ?></td>
									<td width="130" title="FabDesc=<? echo $fab_desc; ?>">
										<p><? echo 'Fabric'; ?></p>
									</td>
									<td width="50">
										<p><? echo $uoms; ?></p>
									</td>
									<td width="110">
										<p><? echo $color_library[$color_id]; ?></p>
									</td>

									<td width="80" align="right">
										<p><? echo number_format($fin_wo_qty, 2); ?></p>
									</td>
									<td width="80" align="right" style="word-break:break-all">
										<p><a href='#report_details' onClick="openmypage2('<? echo $all_po_id; ?>','<? echo $job_no; ?>','<? echo $color_id; ?>',1,'wo_fab_popup');"><? echo number_format($fin_wo_qty, 2); ?></a><? //echo number_format($fin_wo_qty,2); 
																																																									?></p>
									</td>

									<td width="80" align="right">
										<p><a href='#report_details' onClick="openmypage2('<? echo $all_po_id; ?>','<? echo $job_no; ?>','<? echo $color_id; ?>',1,'fin_receive_popup2');"><? echo number_format($fin_Recv, 2); ?></a><? //echo number_format($fin_Recv,2); 
																																																										?></p>
									</td>
									<td width="50" align="right" title="Recv/WoQty*100" style="word-break:break-all">
										<p><? echo number_format(($fin_Recv / $fin_wo_qty) * 100, 2); ?></p>
									</td>
									<td width="80" align="right" title="Wo Qty-Fin RecvQty">
										<p><? $recv_bal = $fin_wo_qty - $fin_Recv;
											echo number_format($recv_bal, 2); ?></p>
									</td>

									<td width="80" align="right">
										<p><a href='#report_details' onClick="openmypage2('<? echo $all_po_id; ?>','<? echo $job_no; ?>','<? echo $color_id; ?>',1,'fin_issue_popup2');"><? echo number_format($fin_issueQty, 2); ?></a><? //echo number_format($fin_issueQty,2); 
																																																										?></p>
									</td>
									<td width="50" title="Fin IssueQty/Fin RecvQty*100" style="word-break:break-all" align="right">
										<p><? echo number_format(($fin_issueQty / $fin_Recv) * 100, 2); ?></p>
									</td>
									<td width="80" align="right">
										<p><? $issue_bal = $fin_Recv - $fin_issueQty;
											echo number_format($issue_bal, 2); ?></p>
									</td>

									<td width="">
										<p><? //echo $row[csf('style_ref_no')]; 
											?></p>
									</td>


								</tr>
							<?
								$f++;
								$tot_wo_fab_qty += $fin_wo_qty;
								$tot_pre_fab_qty += $row[('fab_req_Qty')];
								$tot_fin_Recv += $fin_Recv;
								$tot_recv_bal += $recv_bal;
								$tot_issue_bal += $issue_bal;
								$total_issue_qty += $fin_issueQty;
							}
							?>


							<tr class="tbl_bottom">
								<td colspan="4" align="right">Total</td>
								<td align="right"><? echo number_format($tot_wo_fab_qty, 2); ?></td>
								<td align="right"><? echo number_format($tot_wo_fab_qty, 2); ?></td>
								<td align="right"><? echo number_format($tot_fin_Recv, 2); ?></td>

								<td align="right"><? echo number_format(($tot_fin_Recv / $tot_wo_fab_qty) * 100, 2); ?></td>
								<td align="right"><? echo number_format($tot_recv_bal, 2); ?></td>
								<td align="right"><? echo number_format($total_issue_qty, 2); ?></td>
								<td align="right"><? echo number_format(($total_issue_qty / $tot_fin_Recv) * 100, 2); ?></td>
								<td align="right"><? echo number_format($tot_issue_bal, 2); ?></td>
								<td align="right"><? //echo number_format($total_transfer_out_qty,2); 
													?></td>

							</tr>
							<?
							$m = 1;
							$tot_wo_trim_qty = $tot_pre_trim_qty = $tot_trim_issue_qty = $tot_trim_Recv = $tot_recv_bal = $tot_issue_bal = 0;

							foreach ($trimArr[$job_no] as $item_data => $row) {

								$trimData = explode('_', $item_data);
								$item_id = $trimData[0];


								//$po_id=rtrim($row['po_id'],',');
								//	$all_po_id=implode(',',array_unique(explode(',',$po_id)));

								$type = $row['type'];
								if ($type == 'trims') {
									$desc = rtrim($row['desc'], ',');
									$fab_desc = implode(',', array_unique(explode(',', $desc)));
									$uomId = rtrim($row['uom'], ',');
									//echo $uomId.'D';
									$uoms = implode(',', array_unique(explode(',', $uomId)));
									//$trim_wo_qty=$fabricQtyArr[$job_no][$color_id]['fin_wo_qty'];
									//$fin_Recv=$fin_dataArrayRecv[$job_no][$color_id];
									$trim_issueQty = $issue_data_arr2[$job_no][$item_data]["issue_quantity"];;
									$trim_wo_qty = $wo_qty_ArrayRecv[$job_no][$item_data];
									$RecvQty = $dataArrayRecvQty[$job_no][$item_data];
									// echo $RecvQty.'='.$item_data.'<br>';
								}
								//$fin_issue_qty_arr[$poJob][$row[csf('item_color_id')]]["fin_issue_quantity"]

								//	echo $job_no.'='.$item_data.',';

								if ($m % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trt_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="trt_<? echo $m; ?>">

									<td width="30"><? echo $m; ?></td>
									<td width="130" title="TrimDesc=<? echo $desc; ?>">
										<p><? echo $trim_groupArr[$item_id]; ?></p>
									</td>
									<td width="50">
										<p><? echo $uoms; ?></p>
									</td>
									<td width="110">
										<p><? //echo $color_library[$color_id]; 
											?></p>
									</td>

									<td width="80" align="right">
										<p><? echo number_format($row[('trim_req_Qty')], 2); ?></p>
									</td>
									<td width="80" align="right" style="word-break:break-all">
										<p><a href='#report_details' onClick="openmypage2('<? echo $all_po_id; ?>','<? echo $item_id; ?>','<? echo $item_data; ?>',1,'wo_trim_popup');"><? echo number_format($trim_wo_qty, 2); ?></a><? //echo number_format($trim_wo_qty,2); 
																																																										?></p>
									</td>

									<td width="80" align="right">
										<p><a href='#report_details' onClick="openmypage2('<? echo $all_po_id; ?>','<? echo $item_id; ?>','<? echo $item_data; ?>',2,'receive_popup2');"><? echo number_format($RecvQty, 2); ?></a><? //echo number_format($RecvQty,2); 
																																																									?></p>
									</td>
									<td width="50" align="right" title="Recv/WoQty*100" style="word-break:break-all">
										<p><? echo number_format(($RecvQty / $trim_wo_qty) * 100, 2); ?></p>
									</td>
									<td width="80" align="right" title="Wo Qty-Fin RecvQty">
										<p><? $recv_bal = $trim_wo_qty - $RecvQty;
											echo number_format($recv_bal, 2); ?></p>
									</td>

									<td width="80" align="right">
										<p><a href='#report_details' onClick="openmypage2('<? echo $all_po_id; ?>','<? echo $item_id; ?>','<? echo $item_data; ?>',3,'issue_popup2');"><? echo number_format($trim_issueQty, 2); ?></a><? //echo number_format($trim_issueQty,2); 
																																																										?></p>
									</td>
									<td width="50" title=" IssueQty/ RecvQty*100" style="word-break:break-all" align="right">
										<p><? echo number_format(($trim_issueQty / $RecvQty) * 100, 2);; ?></p>
									</td>
									<td width="80" align="right" title="RecvQty-IssueQty" align="right">
										<p><? $issue_bal = $RecvQty - $trim_issueQty;
											echo number_format($issue_bal, 2); ?></p>
									</td>

									<td width="">
										<p><? //echo $row[csf('style_ref_no')]; 
											?></p>
									</td>


								</tr>
							<?
								$m++;
								$tot_wo_trim_qty += $trim_wo_qty;
								$tot_pre_trim_qty += $row[('trim_req_Qty')];
								$tot_trim_Recv += $RecvQty;
								$tot_recv_bal += $recv_bal;
								$tot_trim_issue_qty += $trim_issueQty;
								$tot_issue_bal += $issue_bal;
							}
							?>


							<tr class="tbl_bottom">
								<td colspan="4" align="right">Total</td>
								<td align="right"><? echo number_format($tot_pre_trim_qty, 2); ?></td>
								<td align="right"><? echo number_format($tot_wo_trim_qty, 2); ?></td>
								<td align="right"><? echo number_format($tot_trim_Recv, 2); ?></td>

								<td align="right"><? echo number_format(($tot_trim_Recv / $tot_wo_trim_qty) * 100, 2);  ?></td>
								<td align="right"><? echo number_format($tot_recv_bal, 2); ?></td>
								<td align="right"><? echo number_format($tot_trim_issue_qty, 2); ?></td>
								<td align="right"><? echo number_format(($tot_trim_issue_qty / $tot_trim_Recv) * 100, 2);  ?></td>
								<td align="right"><? echo number_format($tot_issue_bal, 2); ?></td>
								<td align="right"><? //echo number_format($total_transfer_out_qty,2); 
													?></td>

							</tr>


						</table>
					</div>
				</fieldset>
			<?
		} //Job End
			?>
			</div>
		<?
	} 
	else if ($report_type == 3) {
		?>
			<fieldset style="width:2850px;">
				<!-- Fabric Part -->
				<table width="2850">
					<tr class="form_caption">
						<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="30" align="center"><? echo $company_library[$cbo_company]; ?></td>
					</tr>
				</table>
				<h2 style="float: left; font-size: 20px;">Fabric</h2>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2846" class="rpt_table">
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="70">Buyer</th>
							<th rowspan="2" width="100">Job No</th>
							<th rowspan="2" width="110">Style</th>
							<th rowspan="2" width="110">Style Qnty</th>
							<th rowspan="2" width="80">Order UOM</th>
							<th rowspan="2" width="80">Qnty(pcs)</th>
							<th rowspan="2" width="140">Body Part</th>
							<th rowspan="2" width="80">Fabric Type</th>
							<th rowspan="2" width="100">Fab. Contr.</th>
							<th rowspan="2" width="150">Fab. Discription</th>
							<th rowspan="2" width="80">Color Type</th>
							<th rowspan="2" width="40">UOM</th>
							<th rowspan="2" width="100">Item Color</th>
							<th rowspan="2" width="80">Req Qty</th>
							<th colspan="6">WO Details</th>
							<th colspan="4">Receive Details</th>
							<th colspan="4">Issue Details</th>
							<th rowspan="2">Balance</th>
						</tr>
						<tr>
							<th width="100">WO No</th>
							<th width="100">WO Date</th>
							<th width="100">WO Qty</th>
							<th width="100">Supplier</th>
							<th width="100">Delivery Date</th>
							<th width="100">Dlv. Revised Date</th>

							<th width="90">Receive Qty</th>
							<th width="100">Recv. Return Qty</th>
							<th width="80">Transfer in</th>
							<th width="100">Total Receive</th>

							<th width="90">Issue Qty</th>
							<th width="100">Issue Return Qty</th>
							<th width="80">Transfer out</th>
							<th width="100">Total Issue</th>
						</tr>
					</thead>
				</table>
				<div style="width:2850px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2830" class="rpt_table" id="tbl_issue_status">
						<?
						$i = 1;
						$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
						$suplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');

						$budget_on_sql = sql_select("select embellishment_id,embellishment_budget_id from variable_order_tracking where company_name='$cbo_company' and variable_list=75 and status_active=1 and is_deleted=0 and embellishment_id=3");
						if (count($budget_on_sql) > 0) {
							foreach ($budget_on_sql as $row) {
								$budget_on = $row[csf('embellishment_budget_id')] ? $row[csf('embellishment_budget_id')] : 2;
							}
						} else {
							$budget_on = 2;
						}

						$job_sql = sql_select("select a.company_name, a.job_no, b.id, b.po_number, min(c.id) as cid, sum(c.plan_cut_qnty) as plan_cut_qnty, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where  b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond group by a.company_name, a.job_no, b.id, b.po_number, c.color_number_id, c.size_number_id, c.item_number_id");
						$company2 = 0;
						$po_number = array();
						$paln_cut_qnty_array = array();
						$popaln_cut_qnty_array = array();
						foreach ($job_sql as $jrow) {
							$company2 = $jrow[csf("company_name")];
							$po_number[$jrow[csf("id")]] = $jrow[csf("po_number")];
							$paln_cut_qnty_array[$jrow[csf("cid")]] = $jrow[csf("plan_cut_qnty")];
							$order_qnty_array[$jrow[csf("cid")]] = $jrow[csf("order_quantity")];
							$popaln_cut_qnty_array[$jrow[csf("id")]] += $jrow[csf("plan_cut_qnty")];
						}
						unset($job_sql);



						$fin_sql = "SELECT a.style_ref_no, a.buyer_name, a.job_no,a.job_quantity,a.order_uom, b.po_number,b.id as po_id, sum(a.total_set_qnty*b.po_quantity) as po_quantity,c.id as fab_dtls_id,c.lib_yarn_count_deter_id as deter_id,c.fabric_description ,d.color_number_id as color_id,c.uom,c.body_part_id,c.color_type_id,d.cons,c.costing_per,d.color_size_table_id,a.gmts_item_id,e.booking_mst_id,c.item_number_id,d.requirment
				from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown f,wo_pre_cost_fabric_cost_dtls c  left join wo_booking_dtls e on e.pre_cost_fabric_cost_dtls_id=c.id and e.job_no=c.job_no,wo_pre_cos_fab_co_avg_con_dtls d
				where a.job_no=b.job_no_mst and 
				a.id=c.job_id and 
				a.job_no=f.job_no_mst and 
				b.id=f.po_break_down_id and 
				f.status_active=1 and 
				a.status_active=1 and 
				a.is_deleted=0 and
				b.status_active=1 and 
				b.is_deleted=0 and
				c.status_active=1 and 
				c.is_deleted=0 and
				d.status_active=1 and 
				d.is_deleted=0 and
				d.pre_cost_fabric_cost_dtls_id=c.id and 
				a.company_name=$cbo_company 
				$buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond
				group by a.id, a.job_no,a.job_quantity,a.order_uom, a.style_ref_no, a.buyer_name, a.total_set_qnty,c.id,c.lib_yarn_count_deter_id,c.fabric_description,d.color_number_id,c.uom,c.body_part_id,c.color_type_id,c.costing_per,d.color_size_table_id,a.gmts_item_id,b.id, b.po_number,e.booking_mst_id,c.item_number_id,d.cons,d.requirment order by a.id";



						// echo $fin_sql;die;

						$fin_result = sql_select($fin_sql);
						$p = 1;

						foreach ($fin_result as $row) {
							$fin_order_id_arr[$row[csf("po_id")]] = $row[csf("po_id")];
							$job_no = $row[csf("job_no")];
							if ($row[csf("costing_per")] == 1) $costing_per_pre = 12;
							else if ($row[csf("costing_per")] == 2) $costing_per_pre = 1;
							else if ($row[csf("costing_per")] == 3) $costing_per_pre = 24;
							else if ($row[csf("costing_per")] == 4) $costing_per_pre = 36;
							else if ($row[csf("costing_per")] == 5) $costing_per_pre = 48;
							$item_ratio_array = return_library_array("select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");

							$orderQty = $order_qnty_array[$row[csf("color_size_table_id")]];
							$planQty = $paln_cut_qnty_array[$row[csf("color_size_table_id")]];
							$itemRatio = $item_ratio_array[$row[csf("item_number_id")]];
							if ($budget_on == 1) {
								$finReqQty = ($orderQty / ($costing_per_pre * $itemRatio)) * $row[csf("requirment")];
								// echo $orderQty.'==>'.$costing_per_pre.'==>'.$itemRatio.'==>'.$row[csf("requirment")].'<br>';	
							} else {
								$finReqQty = ($planQty / ($costing_per_pre * $itemRatio)) * $row[csf("requirment")];
							}

							// echo $planQty.'==>'.$costing_per_pre.'==>'.$itemRatio.'==>'.$row[csf("cons")].'<br>';	

							$order_qnty_pcs = $row[csf("job_quantity")] * $itemRatio;

							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['buyer_name'] = $row[csf("buyer_name")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['order_uom'] = $row[csf("order_uom")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['po_quantity'] = $order_qnty_pcs;
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['buyer_name'] = $row[csf("buyer_name")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['body_part_id'] = $row[csf("body_part_id")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['job_quantity'] = $row[csf("job_quantity")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['deter_id'] = $row[csf("deter_id")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['color_type_id'] = $color_type[$row[csf("color_type_id")]];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['deter_id'] = $row[csf("deter_id")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['uom'] = $row[csf("uom")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['fab_req'] += $finReqQty;


							if (isset($row[csf("po_id")])) {
								$fabric_po_arr[$row[csf("job_no")]][$row[csf("po_id")]] = $row[csf("po_id")];
								$poArr[$row[csf("po_id")]] = $row[csf("po_id")];
							}
						}

						$fin_all_order_id = implode(",", $poArr);

						$fin_sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.fabric_description_id, c.store_id, a.uom, a.rate, a.color_id, b.po_breakdown_id as po_id, b.quantity, b.order_amount as amount, b.trans_type,a.body_part_id,c.booking_id
			from inv_receive_master c, pro_finish_fabric_rcv_dtls a, order_wise_pro_details b 
			where a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category=3 and c.entry_form=17";
						// a.id=b.dtls_id and // if we want to add this condition then play the script --update PRO_FINISH_FABRIC_RCV_DTLS a, ORDER_WISE_PRO_DETAILS b set a.id=b.dtls_id where b.id in(select a.id  from PRO_FINISH_FABRIC_RCV_DTLS a, ORDER_WISE_PRO_DETAILS b where a.trans_id=b.trans_id and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.dtls_id=0 group by a.id) and a.trans_id=b.trans_id and  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.dtls_id=0;

						$p = 1;
						if ($fin_all_order_id != "") {
							$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
							foreach ($fin_all_order_id_arr as $order_id) {
								if ($p == 1) $fin_sql_recv .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
								else $fin_sql_recv .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
								$p++;
							}
							$fin_sql_recv .= " )";
						}
						//echo $fin_sql_recv;die;			
						$fin_sql_recv_result = sql_select($fin_sql_recv);
						foreach ($fin_sql_recv_result as $row) {
							if ($row[csf('color_id')] == 0) $row[csf('color_id')] = 404;
							if ($row[csf('item_size')] == "") $item_size = 0;
							else $item_size = $row[csf('item_size')];
							$fin_dataArrayRecv[$row[csf('po_id')]] .= $row[csf('fabric_description_id')] . "_" . $row[csf('uom')] . "_" . $row[csf('color_id')] . "_" . $row[csf('rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
							$fin_rcv_data_arr[$row[csf('amount')]] = $row[csf('amount')];
							$fin_rcv_data[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['fin_rcv_qnty'] += $row[csf('quantity')];

							$fin_rcv_data[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['fin_recv_data'] .= $row[csf('fabric_description_id')] . "_" . $row[csf('uom')] . "_" . $row[csf('color_id')] . "_" . $row[csf('rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";;
						}
						//echo "<pre>";print_r($fin_dataArrayRecv);die;			
						$wo_sql = "SELECT a.id ,a.job_no , a.po_break_down_id ,a.grey_fab_qnty ,a.fin_fab_qnty ,a.rate ,a.amount , a.gmts_color_id ,a.dia_width,b.id AS fab_dtls_id, b.body_part_id ,b.color_type_id,b.fabric_description , b.gsm_weight ,b.uom ,
			 c.type  , c.id as copmposition_id, c.construction ,d.booking_no,d.booking_no,d.booking_date,d.supplier_id,d.pay_mode,d.sup_rev_date,d.delivery_date ,b.lib_yarn_count_deter_id as deter_id,d.id as booking_id
			from wo_booking_dtls a join wo_pre_cost_fabric_cost_dtls b on a.pre_cost_fabric_cost_dtls_id=b.id 
			join lib_yarn_count_determina_mst c on c.id=b.lib_yarn_count_deter_id,wo_booking_mst d
			 where a.status_active =1 and a.is_deleted=0 and d.id=a.booking_mst_id and d.entry_form=271
			 and d.booking_type=1 and d.is_short=2  and a.po_break_down_id in ($fin_all_order_id)";
						$wo_data = sql_select($wo_sql);
						foreach ($wo_data as $row) {
							if ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) {
								$supplier = $comp[$row[csf("supplier_id")]];
							} else {
								$supplier = $suplier[$row[csf("supplier_id")]];
							}
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['supplier_id'] = $supplier;
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['booking_id'] = $row[csf("booking_id")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['delivery_date'] = $row[csf("delivery_date")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['sup_rev_date'] = $row[csf("sup_rev_date")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['wo_no'] = $row[csf("booking_no")];

							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['wo_date'] = $row[csf("booking_date")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['wo_qty'] += $row[csf("grey_fab_qnty")];
						}

						if ($db_type == 0) $null_val = "c.color_number_id,c.item_color,";
						else if ($db_type == 2) $null_val = "nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,";

						$fin_sql_bookingqty = "SELECT b.po_break_down_id as po_id, b.construction, b.copmposition, b.fabric_color_id, b.id as dtls_id, b.grey_fab_qnty as wo_qnty, b.amount, c.lib_yarn_count_deter_id as deter_id
			from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls  c
			where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.company_id=$cbo_company and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
						$p = 1;
						if ($fin_all_order_id != "") {
							$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
							foreach ($fin_all_order_id_arr as $order_id) {
								if ($p == 1) $fin_sql_bookingqty .= " and (b.po_break_down_id in(" . implode(',', $order_id) . ")";
								else $fin_sql_bookingqty .= " or b.po_break_down_id  in(" . implode(',', $order_id) . ")";
								$p++;
							}
							$fin_sql_bookingqty .= " )";
						}

						//echo $fin_sql_bookingqty;die;
						$fin_sql_bookingqty_result = sql_select($fin_sql_bookingqty);
						//echo "<pre>";print_r($fin_sql_bookingqty_result);die;
						foreach ($fin_sql_bookingqty_result as $row) {
							$fin_wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('fabric_color_id')]] += $row[csf('wo_qnty')];
							$fin_wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('fabric_color_id')]] += $row[csf('amount')];
						}
						//echo count($fin_wo_qty_ArrayRecv);die;
						//echo "<pre>"; print_r($fin_wo_qty_ArrayRecv);die;

						$fin_issue_qty_sql = "SELECT b.po_breakdown_id, a.color as item_color_id, a.item_size, a.detarmination_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type,c.body_part_id 
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=3 and a.entry_form=0 and b.entry_form in(19,202,209,258) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; // in(25,49,73,78,112)
						$p = 1;
						if ($fin_all_order_id != "") {
							$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
							foreach ($fin_all_order_id_arr as $order_id) {
								if ($p == 1) $fin_issue_qty_sql .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
								else $fin_issue_qty_sql .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
								$p++;
							}
							$fin_issue_qty_sql .= " )";
						}

						$fin_issue_qty_sql_result = sql_select($fin_issue_qty_sql);
						$fin_issue_data_arr = array();
						foreach ($fin_issue_qty_sql_result as $row) {
							if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
							$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["rate"] = $row[csf('rate')];
							if ($row[csf('entry_form')] == 19 && $row[csf('trans_type')] == 2) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_quantity"] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 202 && $row[csf('trans_type')] == 3) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_rcv_rtn_quantity"] += $row[csf('quantity')];
								$fin_rcv_rtn_data[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]['fin_rcv_rtn_qnty'] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 209 && $row[csf('trans_type')] == 4) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_rtn_quantity"] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 5) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_in_quantity"] += $row[csf('quantity')];
								$fin_rcv_rtn_data[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]['fin_trans_in_qnty'] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 6) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_out_quantity"] += $row[csf('quantity')];
							}
						}
						/*echo "tipu<pre>";
			print_r($fin_issue_data_arr);die;*/
						//echo "<pre>"; print_r($fin_wo_qty_ArrayRecv[13749][9][11]);die;
						//echo "<pre>"; print_r($fin_issue_data_arr[13749][9][11]);die;
						$body_rowspan = array();
						foreach ($fabric_main_data_arr as $jobNo => $jobdata) {

							foreach ($jobdata as $bodypartId => $bodydata) {
								foreach ($bodydata as $diterId => $deterdata) {
									foreach ($deterdata as $colorId => $colorData) {
										foreach ($colorData as $bookingId => $row) {
											$job_rowspan[$jobNo] += 1;
											$body_rowspan[$jobNo][$bodypartId] += 1;
											$desc_rowspan[$jobNo][$bodypartId][$diterId] += 1;
										}
									}
								}
							}
						}


						$fin_req_check = array();
						foreach ($fabric_main_data_arr as $jobNo => $jobdata) {
							$j = 1;
							foreach ($jobdata as $bodypartId => $bodydata) {
								$b = 1;
								foreach ($bodydata as $diterId => $deterdata) {
									$d = 1;
									foreach ($deterdata as $colorId => $colorData) {
										foreach ($colorData as $bookingId => $row) {



											$fin_recv_qnty = 0;
											$fin_rcv_rtn_qty = 0;
											$fin_issue_qty = $issue_amount = $fin_rcv_rtn_qty = $fin_issue_rtn_qty = $fin_transfer_in_qty = $fin_transfer_out_qty = $net_fin_recv_qnty = $fin_net_recv_value = $fin_net_issue_qnty = $fin_net_issue_value = $fin_wo_qty = $fin_wo_amount = 0;
											foreach ($fabric_po_arr[$jobNo] as $poId) {
												$fin_recv_qnty += $fin_rcv_data[$poId][$bodypartId][$diterId][$colorId]['fin_rcv_qnty'];
												$fin_rcv_rtn_qty += $fin_rcv_rtn_data[$poId][$diterId][$colorId]['fin_rcv_rtn_qnty'];
												$fin_transfer_in_qty += $fin_rcv_rtn_data[$poId][$diterId][$colorId]['fin_trans_in_qnty'];
												$fin_recv_datas = $fin_rcv_data[$poId][$bodypartId][$diterId][$colorId]['fin_recv_data'];

												$fin_issue_qty += $fin_issue_data_arr[$poId][$bodypartId][$diterId][$colorId]["fin_issue_quantity"];
												$fin_issue_rtn_qty += $fin_issue_data_arr[$poId][$bodypartId][$diterId][$colorId]["fin_issue_rtn_quantity"];
												$fin_transfer_out_qty += $fin_issue_data_arr[$poId][$bodypartId][$diterId][$colorId]["fin_transfer_out_quantity"];
												$issue_amount += $fin_issue_qty * $issue_rate;

												$recv_data = explode('_', $fin_recv_datas);
												$fabId = $recv_data[0];

												if (isset($recv_data[2])) {
													$fin_fabr_colorArr[$colorId]['color'] = $recv_data[2];
												}
												if (isset($recv_data[5])) {
													$fin_recv_basisArr[$colorId]['basis'] = $recv_data[5];
												}
												if (isset($recv_data[0])) {
													$fin_recv_basisArr[$colorId]['fabId'] = $recv_data[0];
												}
												if (isset($recv_data[6])) {
													$fin_recv_basisArr[$colorId]['without_order'] = $recv_data[6];
												}



												$fin_wo_qty += $fin_wo_qty_ArrayRecv[$poId][$diterId][$fin_fabr_color_id];

												//$fin_wo_qty+=$fin_wo_qty_ArrayRecv[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id];
												$fin_wo_amount += $fin_wo_qty_ArrayRecvAmt[$poId][$diterId][$fin_fabr_color_id];
											}

											$fin_recv_basis = $fin_recv_basisArr[$colorId]['basis'];
											$fabId = $fin_recv_basisArr[$colorId]['fabId'];
											$fin_fabr_color_id = $fin_fabr_colorArr[$colorId]['color'];
											$fin_without_order = $fin_recv_basisArr[$colorId]['without_order'];


											$net_fin_recv_qnty = (($fin_recv_qnty - $fin_rcv_rtn_qty) + $fin_transfer_in_qty);
											// $net_fin_recv_qnty=($fin_recv_qnty-$fin_rcv_rtn_qty);
											$fin_net_recv_value = $net_fin_recv_qnty * $fin_ord_avg_rate;
											$fin_net_issue_qnty = (($fin_issue_qty - $fin_issue_rtn_qty) + $fin_transfer_out_qty);
											$fin_net_issue_value = $fin_net_issue_qnty * $fin_ord_avg_rate;
											$fin_all_po_id = implode(",", $fabric_po_arr[$jobNo]);
											$fin_fabr_colors = implode(",", $fin_fabr_colorArr);

											if ($i % 2 == 0) $bgcolor = "#E9F3FF";
											else $bgcolor = "#FFFFFF";
						?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<?
												if ($j == 1) { ?>
													<td width="40" rowspan="<?= $job_rowspan[$jobNo]; ?>"><? echo $i; ?></td>
													<td width="70" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $buyer_arr[$row['buyer_name']]; ?></p>
													</td>
													<td width="100" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $jobNo; ?></p>
													</td>
													<td width="110" rowspan="<?= $job_rowspan[$jobNo]; ?>"><a href='#report_details' onClick="order_openmypage('<? echo $jobNo; ?>','order_details_popup');"><? echo $row['style_ref_no']; ?></a></td>
													<td width="110" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $row['job_quantity']; ?></p>
													</td>
													<td width="80" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $unit_of_measurement[$row['order_uom']]; ?></p>
													</td>
													<td width="80" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $row['po_quantity']; ?></p>
													</td>
												<? $j++;
												}
												if ($b == 1) { ?>
													<td width="140" rowspan="<?= $body_rowspan[$jobNo][$bodypartId]; ?>">
														<p><? echo $body_part[$row['body_part_id']];; ?></p>
													</td>
												<? $b++;
												}
												if ($d == 1) { ?>
													<td width="80" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $constr_fab_arr[$row['deter_id']]; ?></p>
													</td>
													<td width="100" title="Determenation Id=<?= $diterId; ?>" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $constructtion_arr[$row['deter_id']]; ?></p>
													</td>
													<td width="150" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $composition_arr[$row['deter_id']]; ?></p>
													</td>
													<td width="80" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $row['color_type_id']; ?></p>
													</td>
													<td width="40" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $unit_of_measurement[$row['uom']]; ?></p>
													</td>
												<? $d++;
												} ?>

												<td width="100" title="<? echo $colorId; ?>" style="word-break:break-all">
													<p><? echo $color_library[$colorId]; ?></p>
												</td>

												<td width="80" align="right"><? echo number_format($row['fab_req'], 2); ?></td>

												<td width="100" title="Booking Id=<?= $bookingId; ?>" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['wo_no']; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['wo_date'];; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['wo_qty'];; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['supplier_id'];; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['delivery_date']; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['sup_rev_date'];; ?></p>
												</td>




												<td width="90" align="right" title="<? echo "rcv:" . $fin_recv_qnty . " rcv rtn:" . $fin_rcv_rtn_qty . " trans in:" . $fin_transfer_in_qty; ?>"><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_receive_popup');"><? echo number_format($fin_recv_qnty, 2); ?></a></td>
												<td width="100" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_rcv_rtn_popup');"><? echo number_format($fin_rcv_rtn_qty, 2); ?></a></p>
												</td>
												<td width="80" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_transfer_in_popup');"><? echo number_format($fin_transfer_in_qty, 2); ?></a></p>
												</td>
												<td width="100" align="right">
													<p><? echo number_format($net_fin_recv_qnty, 2); ?></p>
												</td>

												<td width="90" align="right" title="<? echo "issue:" . $fin_issue_qty . " iss rtn:" . $fin_issue_rtn_qty . " trans out:" . $fin_transfer_out_qty; ?>"> <a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_issue_popup');"><? echo number_format($fin_issue_qty, 2); ?></a></td>
												<td width="100" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_issue_rtn_popup');"><? echo number_format($fin_issue_rtn_qty, 2); ?></a></p>
												</td>
												<td width="80" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_transfer_out_popup');"><? echo number_format($fin_transfer_out_qty, 2); ?></a></p>
												</td>
												<td width="100" align="right">
													<p><? echo number_format($fin_net_issue_qnty, 2); ?></p>
												</td>

												<td align="right"><? $fin_left_over = $net_fin_recv_qnty - $fin_net_issue_qnty;
																	echo number_format($fin_left_over, 2); ?></td>
											</tr>
						<? //echo $fin_fabric_rate.',';
											$i++;
											$total_order_qty += $row[csf('po_quantity')];
										}
									}
								}
							}
						}
						?>

					</table>
				</div>

				<br> <!-- Trims Part -->

				<div width="2846">
					<h2 style="float: left; font-size: 20px;">Trims</h2>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2846" class="rpt_table">
						<thead>
							<tr>
								<th rowspan="2" width="40">SL</th>
								<th rowspan="2" width="70">Buyer</th>
								<th rowspan="2" width="100">Job No</th>
								<th rowspan="2" width="110">Style</th>
								<th rowspan="2" width="100">Style Qty.</th>
								<th rowspan="2" width="140">Order UOM</th>
								<th rowspan="2" width="100"> Qty. (Pcs)</th>
								<th rowspan="2" width="100">Item Group</th>
								<th rowspan="2" width="150">Item Description</th>

								<th rowspan="2" width="80">Req Qty</th>
								<th colspan="9">WO Details</th>
								<th colspan="4">Receive Details</th>
								<th colspan="4">Issue Details</th>
								<th rowspan="2">Balance</th>
							</tr>
							<tr>
								<th width="100">Item Color</th>
								<th width="40">UOM</th>
								<th width="60">Item Size</th>
								<th width="100">WO No</th>
								<th width="100">WO Date</th>
								<th width="100">WO Qty</th>
								<th width="100">Supplier</th>
								<th width="100">Delivery Date</th>
								<th width="100">Dlv. Revised Date</th>

								<th width="90">Receive Qty</th>
								<th width="100">Recv. Return Qty</th>
								<th width="80">Transfer in</th>
								<th width="100">Total Receive</th>

								<th width="90">Issue Qty</th>
								<th width="100">Issue Return Qty</th>
								<th width="80">Transfer out</th>
								<th width="100">Total Issue</th>
							</tr>
						</thead>
					</table>
					<div style="width:2850px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body2">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2830" class="rpt_table" id="tbl_issue_status2">
							<?
							$i = 1;
							$y = 1;
							$tot_receive_qty = 0;
							$tot_receive_value = 0;
							$tot_issue_qty = 0;
							$total_left_over = 0;
							$total_left_over_balance = 0;
							$dataArrayRecv = array();
							$wo_qty_ArrayRecv = array();
							$wo_qty_ArrayRecv_pi = array();
							$total_wo_qty = 0;
							$total_wo_val = $total_wo_amount = $total_issue_amount = $total_recv_qty = $total_recv_rtn_qty = $total_transfer_in_qty = $total_recv_net_qty = $total_issue_qty = $total_issue_rtn_qty = $total_transfer_out_qty = $total_issue_net_qty = 0;



							$sql_bookingqty_non = sql_select("SELECT sum(b.trim_qty) as wo_qnty,sum(b.amount) as amount,b.trim_group as item_group,b.fabric_color as item_color,b.gmts_color as color_number_id,b.fabric_description as description 
			from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
			where a.company_id=$cbo_company and a.booking_no=b.booking_no  $buyer_id_cond 
			group by  b.trim_group,b.fabric_color,b.gmts_color,b.fabric_description");
							foreach ($sql_bookingqty_non as $row) {
								$wo_qty_ArrayRecv[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]] = $row[csf('wo_qnty')];
								$wo_qty_ArrayRecvAmt[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]] = $row[csf('amount')];
							}




							//==========================================trims booking=====================================================

	$trims_wo_sql="SELECT a.job_no_prefix_num,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.costing_per,b.exchange_rate, d.id AS po_id, d.po_number, f.wo_qnty AS cu_woq, f.amount AS cu_amount, f.sensitivity, f.delivery_date, f.description AS description,f.trim_group, g.booking_no, g.is_approved, g.is_short, g.delivery_date, g.sup_rev_date,g.id as booking_id, g.supplier_id, g.pay_mode, g.booking_date, g.id AS booking_mst_id, a.order_uom, a.job_quantity,c.gmts_sizes as size_number_id,c.color_number_id,c.cons,c.color_size_table_id,e.cons_dzn_gmts as consumptio FROM wo_po_details_master a, wo_pre_cost_mst b, wo_trim_book_con_dtls c, wo_po_break_down d, wo_booking_dtls f, wo_booking_mst g,wo_pre_cost_trim_cost_dtls e WHERE a.job_no = b.job_no AND a.job_no = c.job_no AND g.id = f.booking_mst_id AND a.job_no = d.job_no_mst AND d.id = f.po_break_down_id and a.job_no = e.job_no and f.PRE_COST_FABRIC_COST_DTLS_ID=e.id AND f.trim_group=e.trim_group AND f.id = c.wo_trim_booking_dtls_id AND f.booking_no = c.booking_no AND f.booking_type = 2 AND f.status_active = 1 AND f.is_deleted = 0 AND a.garments_nature = 3 AND d.is_deleted = 0 AND d.status_active = 1 AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1 AND e.is_deleted = 0 AND e.status_active = 1 AND f.is_deleted = 0 AND f.status_active = 1 AND g.is_deleted = 0 AND g.status_active = 1 and  d.id in ($fin_all_order_id) GROUP BY a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,b.costing_per,b.exchange_rate,d.id,d.po_number,f.id,f.sensitivity,f.delivery_date,f.description,f.wo_qnty,f.amount,g.booking_no,g.is_approved,g.is_short,g.delivery_date,g.sup_rev_date,g.supplier_id,g.pay_mode,g.booking_date,g.id,a.order_uom,a.job_quantity,c.gmts_sizes,c.color_number_id,c.cons,c.color_size_table_id,f.trim_group,e.cons_dzn_gmts ORDER BY d.id";

		$trims_wo_data = sql_select($trims_wo_sql);
		$supplier = "";
		foreach ($trims_wo_data as $row) {


			$job_no = $row[csf("job_no")];
			if ($row[csf("costing_per")] == 1) $costing_per_pre = 12;
			else if ($row[csf("costing_per")] == 2) $costing_per_pre = 1;
			else if ($row[csf("costing_per")] == 3) $costing_per_pre = 24;
			else if ($row[csf("costing_per")] == 4) $costing_per_pre = 36;
			else if ($row[csf("costing_per")] == 5) $costing_per_pre = 48;
			$item_ratio_array = return_library_array("select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");

			$orderQty = $order_qnty_array[$row[csf("color_size_table_id")]];
			$planQty = $paln_cut_qnty_array[$row[csf("color_size_table_id")]];
			$itemRatio = $item_ratio_array[$row[csf("item_number_id")]];

			if ($budget_on == 1) {
				$finReqQty = ($row[csf("job_quantity")] / ($costing_per_pre*1)) * $row[csf("consumptio")];
				//  echo $orderQty.'==>'.$costing_per_pre.'==>'.$itemRatio.'==>'.$row[csf("cons")].'<br>';	
			} else {
				$finReqQty = ($planQty / ($costing_per_pre*1)) * $row[csf("consumptio")];
			}



			if ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) {
				$supplier = $comp[$row[csf("supplier_id")]];
			} else {
				$supplier = $suplier[$row[csf("supplier_id")]];
			}

		if($row[csf('sensitivity')]==1)
		{//Color Wise
			$sensivity_qty=$trims_qty_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_mst_id")]]['consump'];

			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['order_uom'] = $row[csf("order_uom")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['sensivity_qty'] += $sensivity_qty;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['job_quantity'] = $row[csf("job_quantity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['wo_no'] = $row[csf("booking_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['wo_date'] = $row[csf("booking_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['delivery_date'] = $row[csf("delivery_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['is_approved'] = $row[csf("is_approved")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['is_short'] = $row[csf("is_short")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['sup_rev_date'] = $row[csf("sup_rev_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['cu_woq'] += $row[csf("cons")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['sensitivity'] = $row[csf("sensitivity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['supplier_id'] = $supplier;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][0][$row[csf("booking_id")]]['req_qty'] = $finReqQty;
		}
		else if($row[csf('sensitivity')]==2)
		{//size Wise
			$sensivity_qty=$trims_qty_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['consump'];

			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['order_uom'] = $row[csf("order_uom")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['sensivity_qty'] += $sensivity_qty;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['job_quantity'] = $row[csf("job_quantity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['wo_no'] = $row[csf("booking_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['wo_date'] = $row[csf("booking_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['delivery_date'] = $row[csf("delivery_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['is_approved'] = $row[csf("is_approved")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['is_short'] = $row[csf("is_short")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['sup_rev_date'] = $row[csf("sup_rev_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['cu_woq'] += $row[csf("cons")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['sensitivity'] = $row[csf("sensitivity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['supplier_id'] = $supplier;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][$row[csf("size_number_id")]][$row[csf("booking_id")]]['req_qty'] = $finReqQty;	
		}
		else if($row[csf('sensitivity')]==4)
		{//color and size Wise
			$sensivity_qty=$trims_qty_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['consump'];

			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['order_uom'] = $row[csf("order_uom")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['sensivity_qty'] += $sensivity_qty;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['job_quantity'] = $row[csf("job_quantity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['wo_no'] = $row[csf("booking_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['wo_date'] = $row[csf("booking_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['delivery_date'] = $row[csf("delivery_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['is_approved'] = $row[csf("is_approved")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['is_short'] = $row[csf("is_short")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['sup_rev_date'] = $row[csf("sup_rev_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['cu_woq'] += $row[csf("cons")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['sensitivity'] = $row[csf("sensitivity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['supplier_id'] = $supplier;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_id")]]['req_qty'] = $finReqQty;	
		}
		else
		{
			$sensivity_qty=$trims_qty_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['consump'];

			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['order_uom'] = $row[csf("order_uom")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['sensivity_qty'] += $sensivity_qty;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['job_quantity'] = $row[csf("job_quantity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['wo_no'] = $row[csf("booking_no")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['wo_date'] = $row[csf("booking_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['delivery_date'] = $row[csf("delivery_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['is_approved'] = $row[csf("is_approved")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['is_short'] = $row[csf("is_short")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['sup_rev_date'] = $row[csf("sup_rev_date")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['cu_woq'] += $row[csf("cons")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['sensitivity'] = $row[csf("sensitivity")];
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['supplier_id'] = $supplier;
			$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][0][0][$row[csf("booking_id")]]['req_qty'] = $finReqQty;	
		}

			$trims_po_arr[$row[csf("job_no")]][$row[csf("booking_id")]][$row[csf("po_id")]] = $row[csf("po_id")];
		}


			/* echo "<pre>";
		print_r($trims_main_data_arr);   */
		$all_order_id = chop($all_order_id, ",");

	$sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.item_group_id, a.item_description, c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , b.order_amount as amount, b.trans_type,c.booking_id,a.gmts_size_id,a.sensitivity from inv_receive_master c, inv_trims_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.po_breakdown_id in($fin_all_order_id) "; //and a.prod_id=21968

		$sql_recv_result = sql_select($sql_recv);
		foreach ($sql_recv_result as $row) 
		{
			if ($row[csf('item_color')] == 0) $row[csf('item_color')] = 404;
			if ($row[csf('item_size')] == "") $item_size = 0;
			else $item_size = $row[csf('item_size')];
			$dataArrayRecv[$row[csf('po_id')]] .= $row[csf('item_group_id')] . "_" . $row[csf('order_uom')] . "_" . $row[csf('item_color')] . "_" . $item_size . "_" . $row[csf('cons_rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('gmts_color_id')] . "_" . $row[csf('item_description')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
			// $trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['req_qty']
			if( $row[csf('sensitivity')]==4 || $row[csf('sensitivity')]==2){
				$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]['quantity'] += $row[csf('quantity')];
				$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]['others'] .= $row[csf('item_group_id')] . "_" . $row[csf('order_uom')] . "_" . $row[csf('item_color')] . "_" . $item_size . "_" . $row[csf('cons_rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('gmts_color_id')] . "_" . $row[csf('item_description')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___"; 
			}else{
				$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]]['quantity'] += $row[csf('quantity')];
				$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]]['others'] .= $row[csf('item_group_id')] . "_" . $row[csf('order_uom')] . "_" . $row[csf('item_color')] . "_" . $item_size . "_" . $row[csf('cons_rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('gmts_color_id')] . "_" . $row[csf('item_description')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___"; 
			}
			
		}
							//echo "<pre>";print_r($trims_others_arr);die;			

	if ($db_type == 0) $null_val = "c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
	else if ($db_type == 2) $null_val = "nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,nvl(c.item_size,0) as item_size,";

	$sql_bookingqty = "SELECT c.cons as wo_qnty,c.amount,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.company_id=$cbo_company and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.po_break_down_id in($fin_all_order_id)";
	$sql_bookingqty_result = sql_select($sql_bookingqty);

	foreach ($sql_bookingqty_result as $row) {
		if ($row[csf('item_size')] == "" || $row[csf('item_size')] == "0") $item_size = 0;
		else $item_size = $row[csf('item_size')];
		$wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size] += $row[csf('wo_qnty')];
		$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size] += $row[csf('amount')];
	}

							//==========================transfer issue rtn==========================
	$trims_issue_rtn_data = sql_select("SELECT a.id, a.recv_number as issue_number, a.challan_no, c.prod_id, a.receive_date as issue_date, d.item_description,c.quantity as quantity, c.trans_type,c.po_breakdown_id,d.item_group_id from inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(73) and c.entry_form in(73) and b.transaction_type in(4) and c.trans_type in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($fin_all_order_id)");
	$issue_data_arr = array();
	foreach ($trims_issue_rtn_data as $row) {
		$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["issue_rtn_quantity"] += $row[csf('quantity')];
	}

	$issue_qty_sql = "SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type,e.sensitivity,e.gmts_size_id from product_details_master a , order_wise_pro_details b, inv_transaction c, inv_issue_master d,inv_trims_entry_dtls e WHERE a.id = b.prod_id AND b.trans_id = c.id AND item_category_id = 4 AND d.id = c.mst_id AND b.prod_id = e.prod_id and a.item_group_id=e.item_group_id and a.item_color=e.item_color and  a.item_size=e.item_size AND a.entry_form = 24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and e.sensitivity is not null and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.po_breakdown_id in($fin_all_order_id)"; 
	$issue_qty_sql_result = sql_select($issue_qty_sql);

		foreach ($issue_qty_sql_result as $row) {
			if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
			if ($row[csf('item_size')] == "") $item_size_id = 0;
			else $item_size_id = $row[csf('item_size')];
			if($row[csf('sensitivity')] ==4 || $row[csf('sensitivity')] ==2){
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["rate"] = $row[csf('rate')];
			if ($row[csf('entry_form')] == 25 && $row[csf('trans_type')] == 2) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["issue_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 49 && $row[csf('trans_type')] == 3) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["rcv_rtn_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 5) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["transfer_in_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 6) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["transfer_out_quantity"] += $row[csf('quantity')];
			}

			}else{
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["rate"] = $row[csf('rate')];
			if ($row[csf('entry_form')] == 25 && $row[csf('trans_type')] == 2) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["issue_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 49 && $row[csf('trans_type')] == 3) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["rcv_rtn_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 5) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["transfer_in_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 6) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["transfer_out_quantity"] += $row[csf('quantity')];
			}

			}
			
		}

							$itemRowspan = array();
							foreach ($trims_main_data_arr as $jobNo => $job_data) {
								foreach ($job_data as $item_group_id => $trimsGroupId_data) {
									foreach ($trimsGroupId_data as $item_desc_id => $trimsDesc_data) {
										foreach ($trimsDesc_data as $color_id => $color_data) {
											foreach ($color_data as $size_id => $size_data) {
												foreach ($size_data as $booking_id => $row) {

													$jobRowspan[$jobNo] += 1;
													$itemRowspan[$jobNo][$item_group_id] += 1;
													$itemDescRowspan[$jobNo][$item_group_id][$item_desc_id] += 1;
													$itemDescClRowspan[$jobNo][$item_group_id][$item_desc_id][$color_id] += 1;
												}
											}
										}
									}
								}
							}

							$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company."' and module_id=2 and report_id in(219) and is_deleted=0 and status_active=1");

							$report= max(explode(',',$print_report_format));

							if($report==183){$reporAction="show_trim_booking_report2";}
							elseif($report==67){$reporAction="show_trim_booking_report";}
							elseif($report==72){$reporAction="show_trim_booking_report6";}
							elseif($report==177){$reporAction="show_trim_booking_report4";}
							elseif($report==235){$reporAction="show_trim_booking_report9";}
							elseif($report==774){$reporAction="show_trim_booking_report_wg";}
							elseif($report==175){$reporAction="show_trim_booking_report5";}
							elseif($report==14){$reporAction="show_trim_booking_report16";}
							elseif($report==85){$reporAction="print_t";}
							elseif($report==746){$reporAction="print_t7";}

							$req_check = array();
							$t = 1;
							foreach ($trims_main_data_arr as $jobNo => $job_data) {
								$j = 1;
								foreach ($job_data as $item_group_id => $trimsGroupId_data) {
									$it = 1;
									foreach ($trimsGroupId_data as $item_desc_id => $trimsDesc_data) {
										$d = 1;
										foreach ($trimsDesc_data as $color_id => $color_data) {
											$c = 1;
											foreach ($color_data as $size_id => $size_data) {
												foreach ($size_data as $booking_id => $row) {
													$recv_qnty = $issue_qty = $issue_amount = $rcv_rtn_qty = $issue_rtn_qty = $transfer_in_qty = $transfer_out_qty = $net_recv_qnty = $net_recv_value = $net_issue_qnty = $net_issue_value = $wo_qty = $wo_amount = 0;
													foreach ($trims_po_arr[$jobNo][$booking_id] as $poId) {
														
														$item_group_id = $item_group_id;
														$size_id = $size_id;
														$order_uom = $recvRow[1];
														$item_color = $recvRow[2];
														$item_size = $recvRow[3];
														if ($item_size == "") $item_size = 0;
														$cons_rate = $recvRow[4];
														$quantity = $recvRow[5];
														$recv_basis = $recvRow[6];
														$without_order = $recvRow[7];
														$gmts_color = $recvRow[8];
														$item_description = $recvRow[9];
														$store_name_id = $recvRow[10];
														$recv_value = $recvRow[11];
														$trans_type = $recvRow[12];

														if($row['sensitivity']==4 || $row['sensitivity']==2){
															$recv_qnty += $trims_others_arr[$booking_id][$poId][$item_group_id][$size_id]['quantity'];
															$recvRow = explode("_", $trims_others_arr[$booking_id][$poId][$size_id][$item_group_id]['others']);
															$rcv_rtn_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["rcv_rtn_quantity"];
															$issue_rate = $issue_qty_rate_arr[$poId][$item_group_id][$size_id]["rate"];
															$issue_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["issue_quantity"];
															$issue_rtn_qty += $issue_data_arr[$poId][$item_group_id]["issue_rtn_quantity"];
															$transfer_in_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["transfer_in_quantity"];
															$transfer_out_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["transfer_out_quantity"];
														}else{
															$recv_qnty += $trims_others_arr[$booking_id][$poId][$item_group_id]['quantity'];
															$recvRow = explode("_", $trims_others_arr[$booking_id][$poId][$item_group_id]['others']);
															$rcv_rtn_qty += $issue_data_arr[$poId][$item_group_id]["rcv_rtn_quantity"];
															$issue_rate = $issue_qty_rate_arr[$poId][$item_group_id]["rate"];
															$issue_qty += $issue_data_arr[$poId][$item_group_id]["issue_quantity"];
															$issue_rtn_qty += $issue_data_arr[$poId][$item_group_id]["issue_rtn_quantity"];
															$transfer_in_qty += $issue_data_arr[$poId][$item_group_id]["transfer_in_quantity"];
															$transfer_out_qty += $issue_data_arr[$poId][$item_group_id]["transfer_out_quantity"];	
														}
														$issue_amount += $issue_qty * $issue_rate;
														
													}
													$net_recv_qnty = (($recv_qnty - $rcv_rtn_qty) + $transfer_in_qty);

													$net_recv_value = $net_recv_qnty * $ord_avg_rate;
													$net_issue_qnty = (($issue_qty - $issue_rtn_qty) + $transfer_out_qty);
													$net_issue_value = $net_issue_qnty * $ord_avg_rate;
													$all_po_id = implode(",", $trims_po_arr[$jobNo][$booking_id]);

													if ($t % 2 == 0) $bgcolor = "#E9F3FF";
													else $bgcolor = "#FFFFFF";
							?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
														<?
														if ($j == 1) { ?>
															<td width="40" rowspan="<?= $jobRowspan[$jobNo]; ?>"><? echo $t; ?></td>
															<td width="70" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $buyer_arr[$row['buyer_name']]; ?></p>
															</td>
															<td width="100" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $jobNo; ?></p>
															</td>
															<td width="110" rowspan="<?= $jobRowspan[$jobNo]; ?>"><a href='#report_details' onClick="order_openmypage('<? echo $jobNo; ?>','order_details_popup');"><? echo $row['style_ref_no']; ?></a></td>
															<td width="100" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $row['job_quantity']; ?></p>
															</td>
															<td width="140" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><?= $unit_of_measurement[$row['order_uom']]; ?></p>
															</td>
															<td width="100" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $row['style_ref_qnty']; ?></p>
															</td>
														<? $j++;
														}
														if ($it == 1) {
														?>
															<td width="100" title="<? echo $item_group_id . "==" . $without_order; ?>" rowspan="<?= $itemRowspan[$jobNo][$item_group_id]; ?>" style="word-break:break-all">
																<p><? echo $trim_groupArr[$item_group_id]; ?></p>
															</td>
														<? $it++;
														}
														if ($d == 1) { ?>
															<td width="150" rowspan="<?= $itemDescRowspan[$jobNo][$item_group_id][$item_desc_id]; ?>">
																<p><? echo $item_desc_id; ?></p>
															</td>
														<? $d++;
														}?>
														<td width="80" align="right"><? echo number_format($row['req_qty'], 2); ?></td>
														<?
														if ($c == 1) { ?>
															<td width="100" title="<? echo $color_id; ?>" rowspan="<?= $itemDescClRowspan[$jobNo][$item_group_id][$item_desc_id][$color_id]; ?>" style="word-break:break-all">
																<p><? echo $color_library[$color_id]; ?></p>
															</td>
														<? $c++;
														} ?>
														<td width="40" align="center">
															<p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p>
														</td>
														<td width="60" title="<? echo $po_id . "==" . $item_group_id . "==" . $item_color_id . "==" . $$size_id; ?>">
															<p><?= $size_lib[$size_id]; ?>&nbsp;</p>
														</td>
														
														
														<td width="100" title="booking id=<?= $booking_id; ?>" ><p><a href="#" onClick="generate_trim_report('<? echo $reporAction; ?>','<? echo $row['wo_no']; ?>',<? echo $cbo_company; ?>,<? echo $row['is_approved']; ?>,<? echo $row['is_short']; ?>)"><? echo $row['wo_no']; ?></a></p></td>

														<td width="100">
															<p><? echo $row['wo_date']; ?></p>
														</td>
														<td width="100">
															<p><?

																echo $row['cu_woq'];
															 ?></p>
														</td>
														<td width="100">
															<p><? echo $row['supplier_id']; ?></p>
														</td>
														<td width="100">
															<p><? echo $row['delivery_date']; ?></p>
														</td>
														<td width="100">
															<p><? echo $row['sup_rev_date']; ?></p>
														</td>


														<td width="90" align="right" title="<? echo "rcv:" . $recv_qnty . " rcv rtn:" . $rcv_rtn_qty . " trans in:" . $transfer_in_qty; ?>">
														<? if($row['sensitivity']==4){?>
															<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $size_id; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup_color');"><? echo number_format($recv_qnty, 2); ?></a>
															<? }else{ ?>
																<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><? echo number_format($recv_qnty, 2); ?></a>
															<? } ?>
														
														</td>
														<td width="100" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_rtn_popup');"><? echo number_format($rcv_rtn_qty, 2); ?></a></p>
														</td>
														<td width="80" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'transfer_in_popup');"><? echo number_format($transfer_in_qty, 2); ?></a></p>
														</td>
														<td width="100" align="right">
															<p><? echo number_format($net_recv_qnty, 2); ?></p>
														</td>

														<td width="90" align="right" title="<? echo "issue:" . $issue_qty . " iss rtn:" . $issue_rtn_qty . " trans out:" . $transfer_out_qty; ?>">
														<? if($row['sensitivity']==4){?>
															<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $size_id; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup_color');"><? echo number_format($issue_qty, 2); ?></a>
															<? }else{ ?>
																<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><? echo number_format($issue_qty, 2); ?></a>
															<? } ?>
													
													</td>
														<td width="100" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_rtn_popup');"><? echo number_format($issue_rtn_qty, 2); ?></a></p>
														</td>
														<td width="80" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'transfer_out_popup');"><? echo number_format($transfer_out_qty, 2); ?></a></p>
														</td>
														<td width="100" align="right">
															<p><? echo number_format($net_issue_qnty, 2); ?></p>
														</td>

														<td align="right"><? $left_over = $net_recv_qnty - $net_issue_qnty;
																			echo number_format($left_over, 2); ?></td>
													</tr>
							<? //echo $cons_rate.',';


													$t++;

													$total_order_qty += $row[csf('po_quantity')];
												}
											}
										}
									}
								}
							}
							?>

						</table>
					</div>
				</div>

			</fieldset>

			<br>
		<?
	}else if ($report_type == 34) {
		?>
			<fieldset style="width:2850px;">
				<!-- Fabric Part -->
				<table width="2850">
					<tr class="form_caption">
						<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="30" align="center"><? echo $company_library[$cbo_company]; ?></td>
					</tr>
				</table>
				<h2 style="float: left; font-size: 20px;">Fabric</h2>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2846" class="rpt_table">
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="70">Buyer</th>
							<th rowspan="2" width="100">Job No</th>
							<th rowspan="2" width="110">Style</th>
							<th rowspan="2" width="110">Style Qnty</th>
							<th rowspan="2" width="80">Order UOM</th>
							<th rowspan="2" width="80">Qnty(pcs)</th>
							<th rowspan="2" width="140">Body Part</th>
							<th rowspan="2" width="80">Fabric Type</th>
							<th rowspan="2" width="100">Fab. Contr.</th>
							<th rowspan="2" width="150">Fab. Discription</th>
							<th rowspan="2" width="80">Color Type</th>
							<th rowspan="2" width="40">UOM</th>
							<th rowspan="2" width="100">Item Color</th>
							<th rowspan="2" width="80">Req Qty</th>
							<th colspan="6">WO Details</th>
							<th colspan="4">Receive Details</th>
							<th colspan="4">Issue Details</th>
							<th rowspan="2">Balance</th>
						</tr>
						<tr>
							<th width="100">WO No</th>
							<th width="100">WO Date</th>
							<th width="100">WO Qty</th>
							<th width="100">Supplier</th>
							<th width="100">Delivery Date</th>
							<th width="100">Dlv. Revised Date</th>

							<th width="90">Receive Qty</th>
							<th width="100">Recv. Return Qty</th>
							<th width="80">Transfer in</th>
							<th width="100">Total Receive</th>

							<th width="90">Issue Qty</th>
							<th width="100">Issue Return Qty</th>
							<th width="80">Transfer out</th>
							<th width="100">Total Issue</th>
						</tr>
					</thead>
				</table>
				<div style="width:2850px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2830" class="rpt_table" id="tbl_issue_status">
						<?
						$i = 1;
						$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
						$suplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');

						$budget_on_sql = sql_select("select embellishment_id,embellishment_budget_id from variable_order_tracking where company_name='$cbo_company' and variable_list=75 and status_active=1 and is_deleted=0 and embellishment_id=3");
						if (count($budget_on_sql) > 0) {
							foreach ($budget_on_sql as $row) {
								$budget_on = $row[csf('embellishment_budget_id')] ? $row[csf('embellishment_budget_id')] : 2;
							}
						} else {
							$budget_on = 2;
						}

						$job_sql = sql_select("select a.company_name, a.job_no, b.id, b.po_number, min(c.id) as cid, sum(c.plan_cut_qnty) as plan_cut_qnty, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where  b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond group by a.company_name, a.job_no, b.id, b.po_number, c.color_number_id, c.size_number_id, c.item_number_id");
						$company2 = 0;
						$po_number = array();
						$paln_cut_qnty_array = array();
						$popaln_cut_qnty_array = array();
						foreach ($job_sql as $jrow) {
							$company2 = $jrow[csf("company_name")];
							$po_number[$jrow[csf("id")]] = $jrow[csf("po_number")];
							$paln_cut_qnty_array[$jrow[csf("cid")]] = $jrow[csf("plan_cut_qnty")];
							$order_qnty_array[$jrow[csf("cid")]] = $jrow[csf("order_quantity")];
							$popaln_cut_qnty_array[$jrow[csf("id")]] += $jrow[csf("plan_cut_qnty")];
						}
						unset($job_sql);



						$fin_sql = "SELECT a.style_ref_no, a.buyer_name, a.job_no,a.job_quantity,a.order_uom, b.po_number,b.id as po_id, sum(a.total_set_qnty*b.po_quantity) as po_quantity,c.id as fab_dtls_id,c.lib_yarn_count_deter_id as deter_id,c.fabric_description ,d.color_number_id as color_id,c.uom,c.body_part_id,c.color_type_id,d.cons,c.costing_per,d.color_size_table_id,a.gmts_item_id,e.booking_mst_id,c.item_number_id,d.requirment
				from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown f,wo_pre_cost_fabric_cost_dtls c  left join wo_booking_dtls e on e.pre_cost_fabric_cost_dtls_id=c.id and e.job_no=c.job_no,wo_pre_cos_fab_co_avg_con_dtls d
				where a.job_no=b.job_no_mst and 
				a.id=c.job_id and 
				a.job_no=f.job_no_mst and 
				b.id=f.po_break_down_id and 
				f.status_active=1 and 
				a.status_active=1 and 
				a.is_deleted=0 and
				b.status_active=1 and 
				b.is_deleted=0 and
				c.status_active=1 and 
				c.is_deleted=0 and
				d.status_active=1 and 
				d.is_deleted=0 and
				d.pre_cost_fabric_cost_dtls_id=c.id and 
				a.company_name=$cbo_company 
				$buyer_id_cond $style $po_id_cond $year_cond $pub_date $brand_cond $season_cond $season_year_cond
				group by a.id, a.job_no,a.job_quantity,a.order_uom, a.style_ref_no, a.buyer_name, a.total_set_qnty,c.id,c.lib_yarn_count_deter_id,c.fabric_description,d.color_number_id,c.uom,c.body_part_id,c.color_type_id,c.costing_per,d.color_size_table_id,a.gmts_item_id,b.id, b.po_number,e.booking_mst_id,c.item_number_id,d.cons,d.requirment order by a.id";



						// echo $fin_sql;die;

						$fin_result = sql_select($fin_sql);
						$p = 1;

						foreach ($fin_result as $row) {
							$fin_order_id_arr[$row[csf("po_id")]] = $row[csf("po_id")];
							$job_no = $row[csf("job_no")];
							if ($row[csf("costing_per")] == 1) $costing_per_pre = 12;
							else if ($row[csf("costing_per")] == 2) $costing_per_pre = 1;
							else if ($row[csf("costing_per")] == 3) $costing_per_pre = 24;
							else if ($row[csf("costing_per")] == 4) $costing_per_pre = 36;
							else if ($row[csf("costing_per")] == 5) $costing_per_pre = 48;
							$item_ratio_array = return_library_array("select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");

							$orderQty = $order_qnty_array[$row[csf("color_size_table_id")]];
							$planQty = $paln_cut_qnty_array[$row[csf("color_size_table_id")]];
							$itemRatio = $item_ratio_array[$row[csf("item_number_id")]];
							if ($budget_on == 1) {
								$finReqQty = ($orderQty / ($costing_per_pre * $itemRatio)) * $row[csf("requirment")];
								// echo $orderQty.'==>'.$costing_per_pre.'==>'.$itemRatio.'==>'.$row[csf("requirment")].'<br>';	
							} else {
								$finReqQty = ($planQty / ($costing_per_pre * $itemRatio)) * $row[csf("requirment")];
							}

							// echo $planQty.'==>'.$costing_per_pre.'==>'.$itemRatio.'==>'.$row[csf("cons")].'<br>';	

							$order_qnty_pcs = $row[csf("job_quantity")] * $itemRatio;

							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['buyer_name'] = $row[csf("buyer_name")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['order_uom'] = $row[csf("order_uom")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['po_quantity'] = $order_qnty_pcs;
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['buyer_name'] = $row[csf("buyer_name")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['body_part_id'] = $row[csf("body_part_id")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['job_quantity'] = $row[csf("job_quantity")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['deter_id'] = $row[csf("deter_id")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['color_type_id'] = $color_type[$row[csf("color_type_id")]];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['deter_id'] = $row[csf("deter_id")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['uom'] = $row[csf("uom")];
							$fabric_main_data_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]][$row[csf("booking_mst_id")]]['fab_req'] += $finReqQty;


							if (isset($row[csf("po_id")])) {
								$fabric_po_arr[$row[csf("job_no")]][$row[csf("po_id")]] = $row[csf("po_id")];
								$poArr[$row[csf("po_id")]] = $row[csf("po_id")];
							}
						}

						$fin_all_order_id = implode(",", $poArr);

						$fin_sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.fabric_description_id, c.store_id, a.uom, a.rate, a.color_id, b.po_breakdown_id as po_id, b.quantity, b.order_amount as amount, b.trans_type,a.body_part_id,c.booking_id
			from inv_receive_master c, pro_finish_fabric_rcv_dtls a, order_wise_pro_details b 
			where a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category=3 and c.entry_form=17";
						// a.id=b.dtls_id and // if we want to add this condition then play the script --update PRO_FINISH_FABRIC_RCV_DTLS a, ORDER_WISE_PRO_DETAILS b set a.id=b.dtls_id where b.id in(select a.id  from PRO_FINISH_FABRIC_RCV_DTLS a, ORDER_WISE_PRO_DETAILS b where a.trans_id=b.trans_id and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.dtls_id=0 group by a.id) and a.trans_id=b.trans_id and  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.dtls_id=0;

						$p = 1;
						if ($fin_all_order_id != "") {
							$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
							foreach ($fin_all_order_id_arr as $order_id) {
								if ($p == 1) $fin_sql_recv .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
								else $fin_sql_recv .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
								$p++;
							}
							$fin_sql_recv .= " )";
						}
						//echo $fin_sql_recv;die;			
						$fin_sql_recv_result = sql_select($fin_sql_recv);
						foreach ($fin_sql_recv_result as $row) {
							if ($row[csf('color_id')] == 0) $row[csf('color_id')] = 404;
							if ($row[csf('item_size')] == "") $item_size = 0;
							else $item_size = $row[csf('item_size')];
							$fin_dataArrayRecv[$row[csf('po_id')]] .= $row[csf('fabric_description_id')] . "_" . $row[csf('uom')] . "_" . $row[csf('color_id')] . "_" . $row[csf('rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
							$fin_rcv_data_arr[$row[csf('amount')]] = $row[csf('amount')];
							$fin_rcv_data[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['fin_rcv_qnty'] += $row[csf('quantity')];

							$fin_rcv_data[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['fin_recv_data'] .= $row[csf('fabric_description_id')] . "_" . $row[csf('uom')] . "_" . $row[csf('color_id')] . "_" . $row[csf('rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";;
						}
						//echo "<pre>";print_r($fin_dataArrayRecv);die;			
						$wo_sql = "SELECT a.id ,a.job_no , a.po_break_down_id ,a.grey_fab_qnty ,a.fin_fab_qnty ,a.rate ,a.amount , a.gmts_color_id ,a.dia_width,b.id AS fab_dtls_id, b.body_part_id ,b.color_type_id,b.fabric_description , b.gsm_weight ,b.uom ,
			 c.type  , c.id as copmposition_id, c.construction ,d.booking_no,d.booking_no,d.booking_date,d.supplier_id,d.pay_mode,d.sup_rev_date,d.delivery_date ,b.lib_yarn_count_deter_id as deter_id,d.id as booking_id
			from wo_booking_dtls a join wo_pre_cost_fabric_cost_dtls b on a.pre_cost_fabric_cost_dtls_id=b.id 
			join lib_yarn_count_determina_mst c on c.id=b.lib_yarn_count_deter_id,wo_booking_mst d
			 where a.status_active =1 and a.is_deleted=0 and d.id=a.booking_mst_id and d.entry_form=271
			 and d.booking_type=1 and d.is_short=2  and a.po_break_down_id in ($fin_all_order_id)";
						$wo_data = sql_select($wo_sql);
						foreach ($wo_data as $row) {
							if ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) {
								$supplier = $comp[$row[csf("supplier_id")]];
							} else {
								$supplier = $suplier[$row[csf("supplier_id")]];
							}
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['supplier_id'] = $supplier;
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['booking_id'] = $row[csf("booking_id")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['delivery_date'] = $row[csf("delivery_date")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['sup_rev_date'] = $row[csf("sup_rev_date")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['wo_no'] = $row[csf("booking_no")];

							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['wo_date'] = $row[csf("booking_date")];
							$booking_data_arr[$row[csf("booking_id")]][$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['wo_qty'] += $row[csf("grey_fab_qnty")];
						}

						if ($db_type == 0) $null_val = "c.color_number_id,c.item_color,";
						else if ($db_type == 2) $null_val = "nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,";

						$fin_sql_bookingqty = "SELECT b.po_break_down_id as po_id, b.construction, b.copmposition, b.fabric_color_id, b.id as dtls_id, b.grey_fab_qnty as wo_qnty, b.amount, c.lib_yarn_count_deter_id as deter_id
			from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls  c
			where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.company_id=$cbo_company and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
						$p = 1;
						if ($fin_all_order_id != "") {
							$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
							foreach ($fin_all_order_id_arr as $order_id) {
								if ($p == 1) $fin_sql_bookingqty .= " and (b.po_break_down_id in(" . implode(',', $order_id) . ")";
								else $fin_sql_bookingqty .= " or b.po_break_down_id  in(" . implode(',', $order_id) . ")";
								$p++;
							}
							$fin_sql_bookingqty .= " )";
						}

						//echo $fin_sql_bookingqty;die;
						$fin_sql_bookingqty_result = sql_select($fin_sql_bookingqty);
						//echo "<pre>";print_r($fin_sql_bookingqty_result);die;
						foreach ($fin_sql_bookingqty_result as $row) {
							$fin_wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('fabric_color_id')]] += $row[csf('wo_qnty')];
							$fin_wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('fabric_color_id')]] += $row[csf('amount')];
						}
						//echo count($fin_wo_qty_ArrayRecv);die;
						//echo "<pre>"; print_r($fin_wo_qty_ArrayRecv);die;

						$fin_issue_qty_sql = "SELECT b.po_breakdown_id, a.color as item_color_id, a.item_size, a.detarmination_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type,c.body_part_id 
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=3 and a.entry_form=0 and b.entry_form in(19,202,209,258) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; // in(25,49,73,78,112)
						$p = 1;
						if ($fin_all_order_id != "") {
							$fin_all_order_id_arr = array_chunk(array_unique(explode(",", $fin_all_order_id)), 999);
							foreach ($fin_all_order_id_arr as $order_id) {
								if ($p == 1) $fin_issue_qty_sql .= " and (b.po_breakdown_id in(" . implode(',', $order_id) . ")";
								else $fin_issue_qty_sql .= " or b.po_breakdown_id  in(" . implode(',', $order_id) . ")";
								$p++;
							}
							$fin_issue_qty_sql .= " )";
						}

						$fin_issue_qty_sql_result = sql_select($fin_issue_qty_sql);
						$fin_issue_data_arr = array();
						foreach ($fin_issue_qty_sql_result as $row) {
							if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
							$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["rate"] = $row[csf('rate')];
							if ($row[csf('entry_form')] == 19 && $row[csf('trans_type')] == 2) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_quantity"] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 202 && $row[csf('trans_type')] == 3) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_rcv_rtn_quantity"] += $row[csf('quantity')];
								$fin_rcv_rtn_data[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]['fin_rcv_rtn_qnty'] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 209 && $row[csf('trans_type')] == 4) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_issue_rtn_quantity"] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 5) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_in_quantity"] += $row[csf('quantity')];
								$fin_rcv_rtn_data[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]['fin_trans_in_qnty'] += $row[csf('quantity')];
							}
							if ($row[csf('entry_form')] == 258 && $row[csf('trans_type')] == 6) {
								$fin_issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('item_color_id')]]["fin_transfer_out_quantity"] += $row[csf('quantity')];
							}
						}
						/*echo "tipu<pre>";
			print_r($fin_issue_data_arr);die;*/
						//echo "<pre>"; print_r($fin_wo_qty_ArrayRecv[13749][9][11]);die;
						//echo "<pre>"; print_r($fin_issue_data_arr[13749][9][11]);die;
						$body_rowspan = array();
						foreach ($fabric_main_data_arr as $jobNo => $jobdata) {

							foreach ($jobdata as $bodypartId => $bodydata) {
								foreach ($bodydata as $diterId => $deterdata) {
									foreach ($deterdata as $colorId => $colorData) {
										foreach ($colorData as $bookingId => $row) {
											$job_rowspan[$jobNo] += 1;
											$body_rowspan[$jobNo][$bodypartId] += 1;
											$desc_rowspan[$jobNo][$bodypartId][$diterId] += 1;
										}
									}
								}
							}
						}


						$fin_req_check = array();
						foreach ($fabric_main_data_arr as $jobNo => $jobdata) {
							$j = 1;
							foreach ($jobdata as $bodypartId => $bodydata) {
								$b = 1;
								foreach ($bodydata as $diterId => $deterdata) {
									$d = 1;
									foreach ($deterdata as $colorId => $colorData) {
										foreach ($colorData as $bookingId => $row) {



											$fin_recv_qnty = 0;
											$fin_rcv_rtn_qty = 0;
											$fin_issue_qty = $issue_amount = $fin_rcv_rtn_qty = $fin_issue_rtn_qty = $fin_transfer_in_qty = $fin_transfer_out_qty = $net_fin_recv_qnty = $fin_net_recv_value = $fin_net_issue_qnty = $fin_net_issue_value = $fin_wo_qty = $fin_wo_amount = 0;
											foreach ($fabric_po_arr[$jobNo] as $poId) {
												$fin_recv_qnty += $fin_rcv_data[$poId][$bodypartId][$diterId][$colorId]['fin_rcv_qnty'];
												$fin_rcv_rtn_qty += $fin_rcv_rtn_data[$poId][$diterId][$colorId]['fin_rcv_rtn_qnty'];
												$fin_transfer_in_qty += $fin_rcv_rtn_data[$poId][$diterId][$colorId]['fin_trans_in_qnty'];
												$fin_recv_datas = $fin_rcv_data[$poId][$bodypartId][$diterId][$colorId]['fin_recv_data'];

												$fin_issue_qty += $fin_issue_data_arr[$poId][$bodypartId][$diterId][$colorId]["fin_issue_quantity"];
												$fin_issue_rtn_qty += $fin_issue_data_arr[$poId][$bodypartId][$diterId][$colorId]["fin_issue_rtn_quantity"];
												$fin_transfer_out_qty += $fin_issue_data_arr[$poId][$bodypartId][$diterId][$colorId]["fin_transfer_out_quantity"];
												$issue_amount += $fin_issue_qty * $issue_rate;

												$recv_data = explode('_', $fin_recv_datas);
												$fabId = $recv_data[0];

												if (isset($recv_data[2])) {
													$fin_fabr_colorArr[$colorId]['color'] = $recv_data[2];
												}
												if (isset($recv_data[5])) {
													$fin_recv_basisArr[$colorId]['basis'] = $recv_data[5];
												}
												if (isset($recv_data[0])) {
													$fin_recv_basisArr[$colorId]['fabId'] = $recv_data[0];
												}
												if (isset($recv_data[6])) {
													$fin_recv_basisArr[$colorId]['without_order'] = $recv_data[6];
												}



												$fin_wo_qty += $fin_wo_qty_ArrayRecv[$poId][$diterId][$fin_fabr_color_id];

												//$fin_wo_qty+=$fin_wo_qty_ArrayRecv[$fin_po_id][$fin_fabric_desc][$fin_fabr_color_id];
												$fin_wo_amount += $fin_wo_qty_ArrayRecvAmt[$poId][$diterId][$fin_fabr_color_id];
											}

											$fin_recv_basis = $fin_recv_basisArr[$colorId]['basis'];
											$fabId = $fin_recv_basisArr[$colorId]['fabId'];
											$fin_fabr_color_id = $fin_fabr_colorArr[$colorId]['color'];
											$fin_without_order = $fin_recv_basisArr[$colorId]['without_order'];


											$net_fin_recv_qnty = (($fin_recv_qnty - $fin_rcv_rtn_qty) + $fin_transfer_in_qty);
											// $net_fin_recv_qnty=($fin_recv_qnty-$fin_rcv_rtn_qty);
											$fin_net_recv_value = $net_fin_recv_qnty * $fin_ord_avg_rate;
											$fin_net_issue_qnty = (($fin_issue_qty - $fin_issue_rtn_qty) + $fin_transfer_out_qty);
											$fin_net_issue_value = $fin_net_issue_qnty * $fin_ord_avg_rate;
											$fin_all_po_id = implode(",", $fabric_po_arr[$jobNo]);
											$fin_fabr_colors = implode(",", $fin_fabr_colorArr);

											if ($i % 2 == 0) $bgcolor = "#E9F3FF";
											else $bgcolor = "#FFFFFF";
						?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<?
												if ($j == 1) { ?>
													<td width="40" rowspan="<?= $job_rowspan[$jobNo]; ?>"><? echo $i; ?></td>
													<td width="70" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $buyer_arr[$row['buyer_name']]; ?></p>
													</td>
													<td width="100" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $jobNo; ?></p>
													</td>
													<td width="110" rowspan="<?= $job_rowspan[$jobNo]; ?>"><a href='#report_details' onClick="order_openmypage('<? echo $jobNo; ?>','order_details_popup');"><? echo $row['style_ref_no']; ?></a></td>
													<td width="110" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $row['job_quantity']; ?></p>
													</td>
													<td width="80" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $unit_of_measurement[$row['order_uom']]; ?></p>
													</td>
													<td width="80" rowspan="<?= $job_rowspan[$jobNo]; ?>">
														<p><? echo $row['po_quantity']; ?></p>
													</td>
												<? $j++;
												}
												if ($b == 1) { ?>
													<td width="140" rowspan="<?= $body_rowspan[$jobNo][$bodypartId]; ?>">
														<p><? echo $body_part[$row['body_part_id']];; ?></p>
													</td>
												<? $b++;
												}
												if ($d == 1) { ?>
													<td width="80" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $constr_fab_arr[$row['deter_id']]; ?></p>
													</td>
													<td width="100" title="Determenation Id=<?= $diterId; ?>" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $constructtion_arr[$row['deter_id']]; ?></p>
													</td>
													<td width="150" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $composition_arr[$row['deter_id']]; ?></p>
													</td>
													<td width="80" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $row['color_type_id']; ?></p>
													</td>
													<td width="40" rowspan="<?= $desc_rowspan[$jobNo][$bodypartId][$diterId]; ?>">
														<p><? echo $unit_of_measurement[$row['uom']]; ?></p>
													</td>
												<? $d++;
												} ?>

												<td width="100" title="<? echo $colorId; ?>" style="word-break:break-all">
													<p><? echo $color_library[$colorId]; ?></p>
												</td>

												<td width="80" align="right"><? echo number_format($row['fab_req'], 2); ?></td>

												<td width="100" title="Booking Id=<?= $bookingId; ?>" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['wo_no']; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['wo_date'];; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['wo_qty'];; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['supplier_id'];; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['delivery_date']; ?></p>
												</td>
												<td width="100" style="word-break:break-all">
													<p><? echo $booking_data_arr[$bookingId][$jobNo][$bodypartId][$diterId][$colorId]['sup_rev_date'];; ?></p>
												</td>




												<td width="90" align="right" title="<? echo "rcv:" . $fin_recv_qnty . " rcv rtn:" . $fin_rcv_rtn_qty . " trans in:" . $fin_transfer_in_qty; ?>"><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_receive_popup');"><? echo number_format($fin_recv_qnty, 2); ?></a></td>
												<td width="100" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_rcv_rtn_popup');"><? echo number_format($fin_rcv_rtn_qty, 2); ?></a></p>
												</td>
												<td width="80" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',2,'fin_transfer_in_popup');"><? echo number_format($fin_transfer_in_qty, 2); ?></a></p>
												</td>
												<td width="100" align="right">
													<p><? echo number_format($net_fin_recv_qnty, 2); ?></p>
												</td>

												<td width="90" align="right" title="<? echo "issue:" . $fin_issue_qty . " iss rtn:" . $fin_issue_rtn_qty . " trans out:" . $fin_transfer_out_qty; ?>"> <a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_issue_popup');"><? echo number_format($fin_issue_qty, 2); ?></a></td>
												<td width="100" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_issue_rtn_popup');"><? echo number_format($fin_issue_rtn_qty, 2); ?></a></p>
												</td>
												<td width="80" align="right">
													<p><a href='#report_details' onClick="finish_openmypage('<? echo $fin_all_po_id; ?>','<? echo $diterId; ?>','<? echo $colorId; ?>','<? echo $fin_recv_basis; ?>','<? echo $fin_without_order; ?>',3,'fin_transfer_out_popup');"><? echo number_format($fin_transfer_out_qty, 2); ?></a></p>
												</td>
												<td width="100" align="right">
													<p><? echo number_format($fin_net_issue_qnty, 2); ?></p>
												</td>

												<td align="right"><? $fin_left_over = $net_fin_recv_qnty - $fin_net_issue_qnty;
																	echo number_format($fin_left_over, 2); ?></td>
											</tr>
						<? //echo $fin_fabric_rate.',';
											$i++;
											$total_order_qty += $row[csf('po_quantity')];
										}
									}
								}
							}
						}
						?>

					</table>
				</div>

				<br> <!-- Trims Part -->

				<div width="2846">
					<h2 style="float: left; font-size: 20px;">Trims</h2>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2846" class="rpt_table">
						<thead>
							<tr>
								<th rowspan="2" width="40">SL</th>
								<th rowspan="2" width="70">Buyer</th>
								<th rowspan="2" width="100">Job No</th>
								<th rowspan="2" width="110">Style</th>
								<th rowspan="2" width="100">Style Qty.</th>
								<th rowspan="2" width="140">Order UOM</th>
								<th rowspan="2" width="100"> Qty. (Pcs)</th>
								<th rowspan="2" width="100">Item Group</th>
								<th rowspan="2" width="150">Item Description</th>

								<th rowspan="2" width="80">Req Qty</th>
								<th colspan="9">WO Details</th>
								<th colspan="4">Receive Details</th>
								<th colspan="4">Issue Details</th>
								<th rowspan="2">Balance</th>
							</tr>
							<tr>
								<th width="100">Item Color</th>
								<th width="40">UOM</th>
								<th width="60">Item Size</th>
								<th width="100">WO No</th>
								<th width="100">WO Date</th>
								<th width="100">WO Qty</th>
								<th width="100">Supplier</th>
								<th width="100">Delivery Date</th>
								<th width="100">Dlv. Revised Date</th>

								<th width="90">Receive Qty</th>
								<th width="100">Recv. Return Qty</th>
								<th width="80">Transfer in</th>
								<th width="100">Total Receive</th>

								<th width="90">Issue Qty</th>
								<th width="100">Issue Return Qty</th>
								<th width="80">Transfer out</th>
								<th width="100">Total Issue</th>
							</tr>
						</thead>
					</table>
					<div style="width:2850px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body2">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2830" class="rpt_table" id="tbl_issue_status2">
							<?
							$i = 1;
							$y = 1;
							$tot_receive_qty = 0;
							$tot_receive_value = 0;
							$tot_issue_qty = 0;
							$total_left_over = 0;
							$total_left_over_balance = 0;
							$dataArrayRecv = array();
							$wo_qty_ArrayRecv = array();
							$wo_qty_ArrayRecv_pi = array();
							$total_wo_qty = 0;
							$total_wo_val = $total_wo_amount = $total_issue_amount = $total_recv_qty = $total_recv_rtn_qty = $total_transfer_in_qty = $total_recv_net_qty = $total_issue_qty = $total_issue_rtn_qty = $total_transfer_out_qty = $total_issue_net_qty = 0;



							$sql_bookingqty_non = sql_select("SELECT sum(b.trim_qty) as wo_qnty,sum(b.amount) as amount,b.trim_group as item_group,b.fabric_color as item_color,b.gmts_color as color_number_id,b.fabric_description as description 
			from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
			where a.company_id=$cbo_company and a.booking_no=b.booking_no  $buyer_id_cond 
			group by  b.trim_group,b.fabric_color,b.gmts_color,b.fabric_description");
							foreach ($sql_bookingqty_non as $row) {
								$wo_qty_ArrayRecv[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]] = $row[csf('wo_qnty')];
								$wo_qty_ArrayRecvAmt[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]] = $row[csf('amount')];
							}




							//==========================================trims booking=====================================================
	$trims_quntity_sql = "SELECT  a.job_no,c.trim_group,f.description,e.color_number_id,e.size_number_id,f.booking_mst_id,g.gmts_sizes,sum(g.requirment) as consump from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c , wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e,wo_booking_dtls f,wo_trim_book_con_dtls g where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.id= g.wo_trim_booking_dtls_id and f.booking_no=g.booking_no and f.booking_type=2  and f.status_active=1 and f.is_deleted=0  and a.garments_nature=3 and d.is_deleted=0 and d.status_active=1 and  d.id in ($fin_all_order_id) and g.gmts_sizes>0 and e.size_number_id = g.gmts_sizes group by a.job_no,c.trim_group,f.description,e.color_number_id,e.size_number_id,f.booking_mst_id,g.gmts_sizes";
	$trims_quntity_sql_data = sql_select($trims_quntity_sql);
	foreach ($trims_quntity_sql_data as $row) {
		$trims_qty_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['consump'] += $row[csf("consump")];
	}
	$trims_wo_sql = "SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as bom_trim_id, c.trim_group, c.description as description_pre_cost,c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, e.id, e.po_break_down_id, e.cons , f.wo_qnty as cu_woq,f.amount as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date,f.description as description,e.color_number_id,e.size_number_id,g.booking_no,g.is_approved,g.is_short,g.delivery_date,g.sup_rev_date,g.supplier_id,g.pay_mode,g.booking_date ,e.item_number_id,e.color_size_table_id,g.id as booking_mst_id,a.order_uom,a.job_quantity from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c , wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e left join  wo_booking_dtls f on e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2  and f.status_active=1 and f.is_deleted=0 ,wo_booking_mst g where a.job_no=b.job_no and g.id=f.booking_mst_id and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id   and a.garments_nature=3 and d.is_deleted=0 and d.status_active=1 and  d.id in ($fin_all_order_id) group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description,c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, e.id, e.cons, f.wo_qnty,f.amount, f.id, f.sensitivity, f.delivery_date, f.description,e.color_number_id,e.size_number_id,g.booking_no,g.is_approved,g.is_short,g.delivery_date,g.sup_rev_date,g.supplier_id,g.pay_mode,g.booking_date,e.item_number_id,e.color_size_table_id,g.id,a.order_uom,a.job_quantity  order by d.id, c.id";
	//echo $trims_wo_sql;die;


							$trims_wo_data = sql_select($trims_wo_sql);
							$supplier = "";
							foreach ($trims_wo_data as $row) {


								$job_no = $row[csf("job_no")];
								if ($row[csf("costing_per")] == 1) $costing_per_pre = 12;
								else if ($row[csf("costing_per")] == 2) $costing_per_pre = 1;
								else if ($row[csf("costing_per")] == 3) $costing_per_pre = 24;
								else if ($row[csf("costing_per")] == 4) $costing_per_pre = 36;
								else if ($row[csf("costing_per")] == 5) $costing_per_pre = 48;
								$item_ratio_array = return_library_array("select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");

								$orderQty = $order_qnty_array[$row[csf("color_size_table_id")]];
								$planQty = $paln_cut_qnty_array[$row[csf("color_size_table_id")]];
								$itemRatio = $item_ratio_array[$row[csf("item_number_id")]];

								if ($budget_on == 1) {
									$finReqQty = ($orderQty / ($costing_per_pre * $itemRatio)) * $row[csf("cons")];
									//  echo $orderQty.'==>'.$costing_per_pre.'==>'.$itemRatio.'==>'.$row[csf("cons")].'<br>';	
								} else {
									$finReqQty = ($planQty / ($costing_per_pre * $itemRatio)) * $row[csf("cons")];
								}



								if ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) {
									$supplier = $comp[$row[csf("supplier_id")]];
								} else {
									$supplier = $suplier[$row[csf("supplier_id")]];
								}
								$sensivity_qty=$trims_qty_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['consump'];

								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['buyer_name'] = $row[csf("buyer_name")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['order_uom'] = $row[csf("order_uom")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['sensivity_qty'] += $sensivity_qty;
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['job_quantity'] = $row[csf("job_quantity")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['wo_no'] = $row[csf("booking_no")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['wo_date'] = $row[csf("booking_date")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['delivery_date'] = $row[csf("delivery_date")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['is_approved'] = $row[csf("is_approved")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['is_short'] = $row[csf("is_short")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['sup_rev_date'] = $row[csf("sup_rev_date")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['cu_woq'] += $row[csf("cu_woq")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['sensitivity'] = $row[csf("sensitivity")];
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['supplier_id'] = $supplier;
								$trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['req_qty'] = $finReqQty;

								$trims_po_arr[$row[csf("job_no")]][$row[csf("booking_mst_id")]][$row[csf("po_id")]] = $row[csf("po_id")];
							}

/* 
							echo "<pre>";
							print_r($trims_main_data_arr); */
							$all_order_id = chop($all_order_id, ",");

	$sql_recv = "SELECT c.receive_basis, c.booking_without_order as without_order, a.item_group_id, a.item_description, c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , b.order_amount as amount, b.trans_type,c.booking_id,a.gmts_size_id,a.sensitivity from inv_receive_master c, inv_trims_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.po_breakdown_id in($fin_all_order_id) "; //and a.prod_id=21968

		$sql_recv_result = sql_select($sql_recv);
		foreach ($sql_recv_result as $row) 
		{
				if ($row[csf('item_color')] == 0) $row[csf('item_color')] = 404;
				if ($row[csf('item_size')] == "") $item_size = 0;
				else $item_size = $row[csf('item_size')];
				$dataArrayRecv[$row[csf('po_id')]] .= $row[csf('item_group_id')] . "_" . $row[csf('order_uom')] . "_" . $row[csf('item_color')] . "_" . $item_size . "_" . $row[csf('cons_rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('gmts_color_id')] . "_" . $row[csf('item_description')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
				// $trims_main_data_arr[$row[csf("job_no")]][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]][$row[csf("booking_mst_id")]]['req_qty']
				if( $row[csf('sensitivity')]==4 || $row[csf('sensitivity')]==2){
					$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]['quantity'] += $row[csf('quantity')];
					$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]['others'] .= $row[csf('item_group_id')] . "_" . $row[csf('order_uom')] . "_" . $row[csf('item_color')] . "_" . $item_size . "_" . $row[csf('cons_rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('gmts_color_id')] . "_" . $row[csf('item_description')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
				}else{
					$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]]['quantity'] += $row[csf('quantity')];
					$trims_others_arr[$row[csf('booking_id')]][$row[csf('po_id')]][$row[csf('item_group_id')]]['others'] .= $row[csf('item_group_id')] . "_" . $row[csf('order_uom')] . "_" . $row[csf('item_color')] . "_" . $item_size . "_" . $row[csf('cons_rate')] . "_" . $row[csf('quantity')] . "_" . $row[csf('receive_basis')] . "_" . $row[csf('without_order')] . "_" . $row[csf('gmts_color_id')] . "_" . $row[csf('item_description')] . "_" . $row[csf('store_id')] . "_" . $row[csf('amount')] . "_" . $row[csf('trans_type')] . "___";
				}
			
		}
							//echo "<pre>";print_r($dataArrayRecv);die;			

	if ($db_type == 0) $null_val = "c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
	else if ($db_type == 2) $null_val = "nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,nvl(c.item_size,0) as item_size,";

	$sql_bookingqty = "SELECT c.cons as wo_qnty,c.amount,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.company_id=$cbo_company and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.po_break_down_id in($fin_all_order_id)";
	$sql_bookingqty_result = sql_select($sql_bookingqty);

	foreach ($sql_bookingqty_result as $row) {
		if ($row[csf('item_size')] == "" || $row[csf('item_size')] == "0") $item_size = 0;
		else $item_size = $row[csf('item_size')];
		$wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size] += $row[csf('wo_qnty')];
		$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size] += $row[csf('amount')];
	}

							//==========================transfer issue rtn==========================
	$trims_issue_rtn_data = sql_select("SELECT a.id, a.recv_number as issue_number, a.challan_no, c.prod_id, a.receive_date as issue_date, d.item_description,c.quantity as quantity, c.trans_type,c.po_breakdown_id,d.item_group_id from inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(73) and c.entry_form in(73) and b.transaction_type in(4) and c.trans_type in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($fin_all_order_id)");
	$issue_data_arr = array();
	foreach ($trims_issue_rtn_data as $row) {
		$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["issue_rtn_quantity"] += $row[csf('quantity')];
	}

	$issue_qty_sql = "SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type,e.sensitivity,e.gmts_size_id from product_details_master a , order_wise_pro_details b, inv_transaction c, inv_issue_master d,inv_trims_entry_dtls e WHERE a.id = b.prod_id AND b.trans_id = c.id AND item_category_id = 4 AND d.id = c.mst_id AND b.prod_id = e.prod_id and a.item_group_id=e.item_group_id and a.item_color=e.item_color and  a.item_size=e.item_size AND a.entry_form = 24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and e.sensitivity is not null and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.po_breakdown_id in($fin_all_order_id)"; 


							/* echo $issue_qty_sql = "SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type,e.sensitivity
			from product_details_master a , order_wise_pro_details b, inv_transaction c, inv_issue_master d 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4 and d.id=c.mst_id and a.entry_form=24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6,5) and c.transaction_type in(2,3,4,6,5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.po_breakdown_id in($fin_all_order_id)"; */


							 //echo $issue_qty_sql;die;
		$issue_qty_sql_result = sql_select($issue_qty_sql);

		foreach ($issue_qty_sql_result as $row) {
			if ($row[csf('item_color_id')] == 0) $row[csf('item_color_id')] = 404;
			if ($row[csf('item_size')] == "") $item_size_id = 0;
			else $item_size_id = $row[csf('item_size')];
			if($row[csf('sensitivity')] ==4 || $row[csf('sensitivity')] ==2){
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["rate"] = $row[csf('rate')];
			if ($row[csf('entry_form')] == 25 && $row[csf('trans_type')] == 2) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["issue_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 49 && $row[csf('trans_type')] == 3) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["rcv_rtn_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 5) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["transfer_in_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 6) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]]["transfer_out_quantity"] += $row[csf('quantity')];
			}

			}else{
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["rate"] = $row[csf('rate')];
			if ($row[csf('entry_form')] == 25 && $row[csf('trans_type')] == 2) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["issue_quantity"] += $row[csf('quantity')];
			}
			if ($row[csf('entry_form')] == 49 && $row[csf('trans_type')] == 3) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["rcv_rtn_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 5) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["transfer_in_quantity"] += $row[csf('quantity')];
			}
			if (($row[csf('entry_form')] == 78 || $row[csf('entry_form')] == 112) && $row[csf('trans_type')] == 6) {
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]["transfer_out_quantity"] += $row[csf('quantity')];
			}

			}
			
		}

							$itemRowspan = array();
							foreach ($trims_main_data_arr as $jobNo => $job_data) {
								foreach ($job_data as $item_group_id => $trimsGroupId_data) {
									foreach ($trimsGroupId_data as $item_desc_id => $trimsDesc_data) {
										foreach ($trimsDesc_data as $color_id => $color_data) {
											foreach ($color_data as $size_id => $size_data) {
												foreach ($size_data as $booking_id => $row) {

													$jobRowspan[$jobNo] += 1;
													$itemRowspan[$jobNo][$item_group_id] += 1;
													$itemDescRowspan[$jobNo][$item_group_id][$item_desc_id] += 1;
													$itemDescClRowspan[$jobNo][$item_group_id][$item_desc_id][$color_id] += 1;
												}
											}
										}
									}
								}
							}

							$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company."' and module_id=2 and report_id in(219) and is_deleted=0 and status_active=1");

							$report= max(explode(',',$print_report_format));

							if($report==183){$reporAction="show_trim_booking_report2";}
							elseif($report==67){$reporAction="show_trim_booking_report";}
							elseif($report==72){$reporAction="show_trim_booking_report6";}
							elseif($report==177){$reporAction="show_trim_booking_report4";}
							elseif($report==235){$reporAction="show_trim_booking_report9";}
							elseif($report==774){$reporAction="show_trim_booking_report_wg";}
							elseif($report==175){$reporAction="show_trim_booking_report5";}
							elseif($report==14){$reporAction="show_trim_booking_report16";}
							elseif($report==85){$reporAction="print_t";}
							elseif($report==746){$reporAction="print_t7";}

							$req_check = array();
							$t = 1;
							foreach ($trims_main_data_arr as $jobNo => $job_data) {
								$j = 1;
								foreach ($job_data as $item_group_id => $trimsGroupId_data) {
									$it = 1;
									foreach ($trimsGroupId_data as $item_desc_id => $trimsDesc_data) {
										$d = 1;
										foreach ($trimsDesc_data as $color_id => $color_data) {
											$c = 1;
											foreach ($color_data as $size_id => $size_data) {
												foreach ($size_data as $booking_id => $row) {
													$recv_qnty = $issue_qty = $issue_amount = $rcv_rtn_qty = $issue_rtn_qty = $transfer_in_qty = $transfer_out_qty = $net_recv_qnty = $net_recv_value = $net_issue_qnty = $net_issue_value = $wo_qty = $wo_amount = 0;
													foreach ($trims_po_arr[$jobNo][$booking_id] as $poId) {
														
														$item_group_id = $item_group_id;
														$size_id = $size_id;
														$order_uom = $recvRow[1];
														$item_color = $recvRow[2];
														$item_size = $recvRow[3];
														if ($item_size == "") $item_size = 0;
														$cons_rate = $recvRow[4];
														$quantity = $recvRow[5];
														$recv_basis = $recvRow[6];
														$without_order = $recvRow[7];
														$gmts_color = $recvRow[8];
														$item_description = $recvRow[9];
														$store_name_id = $recvRow[10];
														$recv_value = $recvRow[11];
														$trans_type = $recvRow[12];
														

														if($row['sensitivity']==4){
															$recv_qnty += $trims_others_arr[$booking_id][$poId][$item_group_id][$size_id]['quantity'];
															$recvRow = explode("_", $trims_others_arr[$booking_id][$poId][$size_id][$item_group_id]['others']);
															$rcv_rtn_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["rcv_rtn_quantity"];
															$issue_rate = $issue_qty_rate_arr[$poId][$item_group_id][$size_id]["rate"];
															$issue_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["issue_quantity"];
															$issue_rtn_qty += $issue_data_arr[$poId][$item_group_id]["issue_rtn_quantity"];
															$transfer_in_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["transfer_in_quantity"];
															$transfer_out_qty += $issue_data_arr[$poId][$item_group_id][$size_id]["transfer_out_quantity"];
														}else{
															$recv_qnty += $trims_others_arr[$booking_id][$poId][$item_group_id]['quantity'];
															$recvRow = explode("_", $trims_others_arr[$booking_id][$poId][$item_group_id]['others']);
															$rcv_rtn_qty += $issue_data_arr[$poId][$item_group_id]["rcv_rtn_quantity"];
															$issue_rate = $issue_qty_rate_arr[$poId][$item_group_id]["rate"];
															$issue_qty += $issue_data_arr[$poId][$item_group_id]["issue_quantity"];
															$issue_rtn_qty += $issue_data_arr[$poId][$item_group_id]["issue_rtn_quantity"];
															$transfer_in_qty += $issue_data_arr[$poId][$item_group_id]["transfer_in_quantity"];
															$transfer_out_qty += $issue_data_arr[$poId][$item_group_id]["transfer_out_quantity"];	
														}
														$issue_amount += $issue_qty * $issue_rate;
														
													}
													$net_recv_qnty = (($recv_qnty - $rcv_rtn_qty) + $transfer_in_qty);

													$net_recv_value = $net_recv_qnty * $ord_avg_rate;
													$net_issue_qnty = (($issue_qty - $issue_rtn_qty) + $transfer_out_qty);
													$net_issue_value = $net_issue_qnty * $ord_avg_rate;
													$all_po_id = implode(",", $trims_po_arr[$jobNo][$booking_id]);

													if ($t % 2 == 0) $bgcolor = "#E9F3FF";
													else $bgcolor = "#FFFFFF";
							?>
													<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
														<?
														if ($j == 1) { ?>
															<td width="40" rowspan="<?= $jobRowspan[$jobNo]; ?>"><? echo $t; ?></td>
															<td width="70" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $buyer_arr[$row['buyer_name']]; ?></p>
															</td>
															<td width="100" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $jobNo; ?></p>
															</td>
															<td width="110" rowspan="<?= $jobRowspan[$jobNo]; ?>"><a href='#report_details' onClick="order_openmypage('<? echo $jobNo; ?>','order_details_popup');"><? echo $row['style_ref_no']; ?></a></td>
															<td width="100" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $row['job_quantity']; ?></p>
															</td>
															<td width="140" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><?= $unit_of_measurement[$row['order_uom']]; ?></p>
															</td>
															<td width="100" rowspan="<?= $jobRowspan[$jobNo]; ?>">
																<p><? echo $row['style_ref_qnty']; ?></p>
															</td>
														<? $j++;
														}
														if ($it == 1) {
														?>
															<td width="100" title="<? echo $item_group_id . "==" . $without_order; ?>" rowspan="<?= $itemRowspan[$jobNo][$item_group_id]; ?>" style="word-break:break-all">
																<p><? echo $trim_groupArr[$item_group_id]; ?></p>
															</td>
														<? $it++;
														}
														if ($d == 1) { ?>
															<td width="150" rowspan="<?= $itemDescRowspan[$jobNo][$item_group_id][$item_desc_id]; ?>">
																<p><? echo $item_desc_id; ?></p>
															</td>
														<? $d++;
														}?>
														<td width="80" align="right"><? echo number_format($row['req_qty'], 2); ?></td>
														<?
														if ($c == 1) { ?>
															<td width="100" title="<? echo $color_id; ?>" rowspan="<?= $itemDescClRowspan[$jobNo][$item_group_id][$item_desc_id][$color_id]; ?>" style="word-break:break-all">
																<p><? echo $color_library[$color_id]; ?></p>
															</td>
														<? $c++;
														} ?>
														<td width="40" align="center">
															<p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p>
														</td>
														<td width="60" title="<? echo $po_id . "==" . $item_group_id . "==" . $item_color_id . "==" . $$size_id; ?>">
															<p><?= $size_lib[$size_id]; ?>&nbsp;</p>
														</td>
														
														
														<td width="100" title="booking id=<?= $booking_id; ?>" ><p><a href="#" onClick="generate_trim_report('<? echo $reporAction; ?>','<? echo $row['wo_no']; ?>',<? echo $cbo_company; ?>,<? echo $row['is_approved']; ?>,<? echo $row['is_short']; ?>)"><? echo $row['wo_no']; ?></a></p></td>

														<td width="100">
															<p><? echo $row['wo_date']; ?></p>
														</td>
														<td width="100">
															<p><?
															if($row['sensitivity']==4){
																echo $row['sensivity_qty'];
															}else{
																echo $row['cu_woq'];
															}

															 ?></p>
														</td>
														<td width="100">
															<p><? echo $row['supplier_id']; ?></p>
														</td>
														<td width="100">
															<p><? echo $row['delivery_date']; ?></p>
														</td>
														<td width="100">
															<p><? echo $row['sup_rev_date']; ?></p>
														</td>


														<td width="90" align="right" title="<? echo "rcv:" . $recv_qnty . " rcv rtn:" . $rcv_rtn_qty . " trans in:" . $transfer_in_qty; ?>">
														<? if($row['sensitivity']==4){?>
															<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $size_id; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup_color');"><? echo number_format($recv_qnty, 2); ?></a>
															<? }else{ ?>
																<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><? echo number_format($recv_qnty, 2); ?></a>
															<? } ?>
														
														</td>
														<td width="100" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_rtn_popup');"><? echo number_format($rcv_rtn_qty, 2); ?></a></p>
														</td>
														<td width="80" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'transfer_in_popup');"><? echo number_format($transfer_in_qty, 2); ?></a></p>
														</td>
														<td width="100" align="right">
															<p><? echo number_format($net_recv_qnty, 2); ?></p>
														</td>

														<td width="90" align="right" title="<? echo "issue:" . $issue_qty . " iss rtn:" . $issue_rtn_qty . " trans out:" . $transfer_out_qty; ?>">
														<? if($row['sensitivity']==4){?>
															<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $size_id; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup_color');"><? echo number_format($issue_qty, 2); ?></a>
															<? }else{ ?>
																<a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><? echo number_format($issue_qty, 2); ?></a>
															<? } ?>
													
													</td>
														<td width="100" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_rtn_popup');"><? echo number_format($issue_rtn_qty, 2); ?></a></p>
														</td>
														<td width="80" align="right">
															<p><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'transfer_out_popup');"><? echo number_format($transfer_out_qty, 2); ?></a></p>
														</td>
														<td width="100" align="right">
															<p><? echo number_format($net_issue_qnty, 2); ?></p>
														</td>

														<td align="right"><? $left_over = $net_recv_qnty - $net_issue_qnty;
																			echo number_format($left_over, 2); ?></td>
													</tr>
							<? //echo $cons_rate.',';


													$t++;

													$total_order_qty += $row[csf('po_quantity')];
												}
											}
										}
									}
								}
							}
							?>

						</table>
					</div>
				</div>

			</fieldset>

			<br>
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}
if ($action == "wo_fab_popup") {
	echo load_html_head_contents("WO Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

		?>
		<fieldset style="width:470px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
					<caption>WO Detail</caption>
					<thead>
						<th width="30">Sl</th>
						<th width="100">WO No</th>
						<th width="75">WO Date</th>
						<th width="80">WO. Qty</th>
					</thead>
					<tbody>
						<?
						$color_id = $item_data;
						$job_no = $item_group;
						//if($fabric_deter_id) $fabric_deter_id_cond="and c.lib_yarn_count_deter_id=$fabric_deter_id"; else $fabric_deter_id_cond="";
						if ($color_id != "") $fabr_color_id_cond = " and b.gmts_color_id=$color_id";
						else $fabr_color_id_cond = "";

						$sql_bookingqty = ("SELECT a.booking_no, a.booking_date, b.grey_fab_qnty as wo_qnty, b.id as dtls_id, c.id, c.lib_yarn_count_deter_id as deter_id, b.po_break_down_id as po_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id)  and c.job_no='$job_no' $fabr_color_id_cond");
						// echo $sql_bookingqty;
						$dtlsArray = sql_select($sql_bookingqty);

						$i = 1;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('wo_qnty')] > 0) {
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

									<td width="80" align="right">
										<p><? echo number_format($row[csf('wo_qnty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('wo_qnty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="3" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Finish WO_Qty
if ($action == "wo_finish_receive_popup") {
	echo load_html_head_contents("WO Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:470px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
					<caption>WO Detail</caption>
					<thead>
						<th width="30">Sl</th>
						<th width="100">WO No</th>
						<th width="75">WO Date</th>
						<th width="80">WO. Qty</th>
					</thead>
					<tbody>
						<?
						if ($fabric_deter_id) $fabric_deter_id_cond = "and c.lib_yarn_count_deter_id=$fabric_deter_id";
						else $fabric_deter_id_cond = "";
						if ($fabr_color_id != "") $fabr_color_id_cond = " and b.fabric_color_id=$fabr_color_id";
						else $fabr_color_id_cond = " and b.fabric_color_id is null";

						$sql_bookingqty = ("SELECT a.booking_no, a.booking_date, b.grey_fab_qnty as wo_qnty, b.id as dtls_id, c.id, c.lib_yarn_count_deter_id as deter_id, b.po_break_down_id as po_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id) $fabric_deter_id_cond $fabr_color_id_cond");
						// echo $sql_bookingqty;
						$dtlsArray = sql_select($sql_bookingqty);

						$i = 1;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('wo_qnty')] > 0) {
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

									<td width="80" align="right">
										<p><? echo number_format($row[csf('wo_qnty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('wo_qnty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="3" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Finish Recv Qty
if ($action == "fin_receive_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Recevied Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$fabr_color_id = str_replace("'", "", $fabr_color_id);
						$fabr_color_id_con2 = "";
						if ($fabr_color_id != "") {
							if ($fabr_color_id == 404) {
								$fabr_color_id_con2 = " and c.color_id in(0,404)";
							} else {
								$fabr_color_id_con2 = " and c.color_id in ($fabr_color_id)";
							}
						}
						// echo $fabr_color_id_con2;die;
						$mrr_sql = "SELECT a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(17) and c.entry_form in(17) and b.transaction_type in(1) and c.trans_type in(1) and c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.detarmination_id='$fabric_deter_id' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $fabr_color_id_con2";
						// echo $mrr_sql;die;

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('quantity')];
								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Finish Recv Qty
if ($action == "fin_receive_popup2") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Recevied Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$fabr_color_id = $item_data;
						$fabr_color_id_con2 = "";
						if ($fabr_color_id != "") {
							if ($fabr_color_id == 404) {
								$fabr_color_id_con2 = " and c.color_id in(0,404)";
							} else {
								$fabr_color_id_con2 = " and c.color_id=$fabr_color_id";
							}
						}
						//echo $fabr_color_id;
						$mrr_sql = "SELECT a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(17) and c.entry_form in(17) and b.transaction_type in(1) and c.trans_type in(1) and c.po_breakdown_id in($po_id)  and a.company_id='$companyID'   and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $fabr_color_id_con2";
						// echo $mrr_sql; 

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('quantity')];
								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Finish Recv Qty
if ($action == "fin_rcv_rtn_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Recevied Return Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$fabr_color_id = str_replace("'", "", $fabr_color_id);
						$fabr_color_id_con2 = "";
						if ($fabr_color_id != "") {
							if ($fabr_color_id == 404) {
								$fabr_color_id_con2 = " and c.color_id in(0,404)";
							} else {
								$fabr_color_id_con2 = " and c.color_id=$fabr_color_id";
							}
						}

						$mrr_sql = "SELECT a.id, a.issue_number as recv_number, a.challan_no, a.issue_date as receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type 
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and item_category_id=3 and d.entry_form=0 and c.entry_form in(202) and c.trans_type in(3) and b.transaction_type in(3) and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.detarmination_id='$fabric_deter_id' $fabr_color_id_con2";

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('quantity')];
								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Finish transfer_in Qty
if ($action == "fin_transfer_in_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Transfer In Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$fabr_color_id = str_replace("'", "", $fabr_color_id);
						$fabr_color_id_con2 = "";
						if ($fabr_color_id != "") {
							if ($fabr_color_id == 404) {
								$fabr_color_id_con2 = " and c.color_id in(0,404)";
							} else {
								$fabr_color_id_con2 = " and c.color_id=$fabr_color_id";
							}
						}

						$mrr_sql = "SELECT a.id, a.transfer_system_id as issue_number, a.challan_no, c.prod_id, a.transfer_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, c.trans_type
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(258) and c.trans_type in(5) and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.detarmination_id='$fabric_deter_id' $fabr_color_id_con2";
						// echo $mrr_sql;

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('quantity')];
								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Finish Issue Qty
if ($action == "fin_issue_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:680px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th>Issue. Qty.</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$fabr_color_id = str_replace("'", "", $fabr_color_id);
					$fabr_color_id_con2 = "";
					if ($fabr_color_id != "") {
						if ($fabr_color_id == 404) {
							$fabr_color_id_con2 = " and c.color_id in(0,404)";
						} else {
							$fabr_color_id_con2 = " and c.color_id=$fabr_color_id";
						}
					}
					//echo $item_color_con.'=='.$item_size_con;
					$mrr_sql = ("SELECT a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.product_name_details, c.quantity as quantity, c.trans_type 
					from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.prod_id=d.id and a.entry_form in(19) and c.trans_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 
					and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.detarmination_id='$fabric_deter_id' $fabr_color_id_con2");

					// echo $mrr_sql;

					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('product_name_details')]; ?></p>
								</td>
								<td align="right">
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
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}

if ($action == "fin_issue_popup2") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:680px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th>Issue. Qty.</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$fabr_color_id = str_replace("'", "", $item_data);
					$fabr_color_id_con2 = "";
					if ($fabr_color_id != "") {
						if ($fabr_color_id == 404) {
							$fabr_color_id_con2 = " and c.color_id in(0,404)";
						} else {
							$fabr_color_id_con2 = " and c.color_id=$fabr_color_id";
						}
					}
					//echo $item_color_con.'=='.$item_size_con;
					$mrr_sql = ("SELECT a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.product_name_details, c.quantity as quantity, c.trans_type 
					from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.prod_id=d.id and a.entry_form in(19) and c.trans_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 
					and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID'  $fabr_color_id_con2");

					// echo $mrr_sql;

					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('product_name_details')]; ?></p>
								</td>
								<td align="right">
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
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}


// Finish Issue Rtn Qty
if ($action == "fin_issue_rtn_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:680px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Return Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th>Issue. Qty.</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$fabr_color_id = str_replace("'", "", $fabr_color_id);
					$fabr_color_id_con2 = "";
					if ($fabr_color_id != "") {
						if ($fabr_color_id == 404) {
							$fabr_color_id_con2 = " and c.color_id in(0,404)";
						} else {
							$fabr_color_id_con2 = " and c.color_id=$fabr_color_id";
						}
					}

					$mrr_sql = "SELECT a.id, a.recv_number as issue_number, a.challan_no, c.prod_id, a.receive_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, c.trans_type
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(209) and c.entry_form in(209) and b.transaction_type in(4) and c.trans_type in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.detarmination_id='$fabric_deter_id' $fabr_color_id_con2";
					//echo $mrr_sql;

					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('product_name_details')]; ?></p>
								</td>
								<td align="right">
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
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}

// Finish transfer_out Qty
if ($action == "fin_transfer_out_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:680px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Transfer Out Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th>Issue. Qty.</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$fabr_color_id = str_replace("'", "", $fabr_color_id);
					$fabr_color_id_con2 = "";
					if ($fabr_color_id != "") {
						if ($fabr_color_id == 404) {
							$fabr_color_id_con2 = " and c.color_id in(0,404)";
						} else {
							$fabr_color_id_con2 = " and c.color_id=$fabr_color_id";
						}
					}

					$mrr_sql = "SELECT a.id, a.transfer_system_id as issue_number, a.challan_no, c.prod_id, a.transfer_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, c.trans_type
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(258) and c.trans_type in(6) and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.detarmination_id='$fabric_deter_id' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $fabr_color_id_con2";
					//echo $mrr_sql;

					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('product_name_details')]; ?></p>
								</td>
								<td align="right">
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
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}

// Trims WO_Qty
if ($action == "wo_receive_popup") {
	echo load_html_head_contents("WO Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:470px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
					<caption>WO Detail</caption>
					<thead>
						<th width="30">Sl</th>
						<th width="100">WO No</th>
						<th width="75">WO Date</th>
						<th width="80">WO. Qty</th>
					</thead>
					<tbody>
						<?

						$i = 1;
						if ($db_type == 0) $null_val = "c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
						else if ($db_type == 2) $null_val = "nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";
						//echo $item_size."<br>";//die;
						if ($item_group) $item_group_cond = "and b.trim_group=$item_group";
						else $item_group_cond = "";
						if ($item_color != "") $item_color_cond = " and c.item_color=$item_color";
						else $item_color_cond = " and c.item_color is null";
						if ($item_size) $item_size_cond = "and c.item_size='$item_size'";
						else $item_size_cond = "";
						//echo $item_size_cond.jh;die;

						$sql_bookingqty = ("SELECT a.booking_no,a.booking_date,c.cons as wo_qnty,b.id as dtls_id,c.id,b.trim_group as item_group,  c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity 
					from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c 
					where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id   and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id) $item_group_cond $item_color_cond $item_size_cond ");

						//echo $sql_bookingqty;
						$dtlsArray = sql_select($sql_bookingqty);

						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('wo_qnty')] > 0) {
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

									<td width="80" align="right">
										<p><? echo number_format($row[csf('wo_qnty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('wo_qnty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="3" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}
// Trims WO_Qty wo_receive_popup2
if ($action == "wo_trim_popup") {
	echo load_html_head_contents("WO Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:470px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
					<caption>WO Detail</caption>
					<thead>
						<th width="30">Sl</th>
						<th width="100">WO No</th>
						<th width="75">WO Date</th>
						<th width="80">WO. Qty</th>
					</thead>
					<tbody>
						<?
						$item_data = explode('_', $item_data);
						$desc = $item_data[1];
						$brand_supp = $item_data[2];
						$i = 1;
						//if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
						//else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";
						// echo $item_group."SDD";//die;
						if ($item_group > 0) $item_group_cond = "and b.trim_group=$item_group";
						else $item_group_cond = "";
						//if($desc!='') $desc_cond="and c.description='$desc'"; else $desc_cond="";
						//if($brand_supp!='' or $brand_supp!=0) $desc_cond="and c.brand_supplier='$brand_supp'"; else $desc_cond="";
						//if($item_color!="") $item_color_cond=" and c.item_color=$item_color"; else $item_color_cond=" and c.item_color is null";
						//if($item_size) $item_size_cond="and c.item_size='$item_size'"; else $item_size_cond="";
						// echo $item_group;die;

						$sql_bookingqty = ("SELECT a.booking_no,a.booking_date,c.cons as wo_qnty,b.id as dtls_id,c.id,b.trim_group as item_group,  c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity 
					from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c 
					where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id   and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id) $item_group_cond  ");

						// echo $sql_bookingqty;die;
						$dtlsArray = sql_select($sql_bookingqty);

						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('wo_qnty')] > 0) {
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

									<td width="80" align="right">
										<p><? echo number_format($row[csf('wo_qnty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('wo_qnty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="3" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Trims Recv Qty
if ($action == "receive_popup") //MCD>Report>Trims>Style Wise Trims Received Issue And Stock - V2
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Recevied Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						//po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$item_color = str_replace("'", "", $item_color);
						$item_size = str_replace("'", "", $item_size);
						//echo $item_color.'=='.$item_size;
						$item_color_con2 = "";
						$item_size_con2 = "";
						if ($item_color != "") {
							if ($item_color == 404) {
								$item_color_con2 = " and d.item_color in(0,404)";
							} else {
								$item_color_con2 = " and d.item_color=$item_color";
							}
						}

						if ($item_size) {
							$item_size_con2 = " and d.item_size='$item_size'";
						}

						$mrr_sql = "SELECT a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(24) and c.entry_form in(24) and b.transaction_type in(1) and c.trans_type in(1) and c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2";
						//echo $mrr_sql;

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								if ($row[csf('trans_type')] == 1 || $row[csf('trans_type')] == 5) {
									$tot_qty += $row[csf('quantity')];
								} else {
									$tot_qty -= $row[csf('quantity')];
								}

								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

if ($action == "receive_popup_color") //MCD>Report>Trims>Style Wise Trims Received Issue And Stock - V2
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Recevied Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						//po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$item_color = str_replace("'", "", $item_color);
						$item_size = str_replace("'", "", $item_size);
						//echo $item_color.'=='.$item_size;
						$item_color_con2 = "";
						$item_size_con2 = "";
						if ($item_color != "") {
							if ($item_color == 404) {
								$item_color_con2 = " and d.item_color in(0,404)";
							} else {
								$item_color_con2 = " and d.item_color=$item_color";
							}
						}

						if ($item_size) {
							$item_size_con2 = " and d.gmts_size='$item_size'";
						}

						$mrr_sql = "SELECT a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(24) and c.entry_form in(24) and b.transaction_type in(1) and c.trans_type in(1) and c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2";
						//echo $mrr_sql;

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								if ($row[csf('trans_type')] == 1 || $row[csf('trans_type')] == 5) {
									$tot_qty += $row[csf('quantity')];
								} else {
									$tot_qty -= $row[csf('quantity')];
								}

								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

if ($action == "receive_popup2") //MCD>Report>Trims>Style Wise Trims Received Issue And Stock - V2
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Recevied Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						//po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$item_color = str_replace("'", "", $item_color);
						$item_size = str_replace("'", "", $item_size);
						//echo $item_color.'=='.$item_size;
						$item_color_con2 = "";
						$item_size_con2 = "";
						if ($item_color != "") {
							if ($item_color == 404) {
								$item_color_con2 = " and d.item_color in(0,404)";
							} else {
								$item_color_con2 = " and d.item_color=$item_color";
							}
						}

						if ($item_size) {
							$item_size_con2 = " and d.item_size='$item_size'";
						}

						//$color_id=$item_data;
						$job_no = $item_group;
						$dataArr = explode("_", $item_data);
						$desc = $dataArr[1];
						$brand_supp = $dataArr[2];
						//echo $brand_supp.'d';
						if ($brand_supp == '') $brand_supp = 0;
						//echo $brand_supp.'='.$desc.'d';
						//	if($brand_supp!='') $brand_suppcond="and b.brand_supplier='$brand_supp'";else $brand_suppcond='';
						//if($desc!='') $desc_cond="and d.item_description='$desc'";else $desc_cond='';

						//d.item_description,d.brand_supplier
						// echo $desc_cond.'=='.$brand_suppcond;

						$mrr_sql = "SELECT a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, c.reject_qty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.trans_id=c.trans_id  and c.dtls_id=b.id and c.prod_id=d.id and c.prod_id=b.prod_id and a.entry_form in(24) and c.entry_form in(24)  and c.trans_type in(1) and c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $desc_cond $brand_suppcond";
						//echo $mrr_sql;

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								if ($row[csf('trans_type')] == 1 || $row[csf('trans_type')] == 5) {
									$tot_qty += $row[csf('quantity')];
								} else {
									$tot_qty -= $row[csf('quantity')];
								}

								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Trims Recv Rtn Qty
if ($action == "receive_rtn_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Recevied Return Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						//po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$item_color = str_replace("'", "", $item_color);
						$item_size = str_replace("'", "", $item_size);
						//echo $item_color.'=='.$item_size;
						$item_color_con2 = "";
						$item_size_con2 = "";
						if ($item_color != "") {
							if ($item_color == 404) {
								$item_color_con2 = " and d.item_color in(0,404)";
							} else {
								$item_color_con2 = " and d.item_color=$item_color";
							}
						}

						if ($item_size) {
							$item_size_con2 = " and d.item_size='$item_size'";
						}

						$mrr_sql = "SELECT a.id, a.issue_number as recv_number, a.challan_no, a.issue_date as receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(49) and c.entry_form in(49) and b.transaction_type in(3) and c.trans_type in(3) and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2";
						//echo $mrr_sql;

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('quantity')];

								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

// Trims transfer_in Qty
if ($action == "transfer_in_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Transfer In Detail</strong></caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="110">Recv. ID</th>
						<th width="90">Chalan No</th>
						<th width="70">Recv. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Recv. Qty.</th>
						<th>Reject Qty.</th>
					</thead>
					<tbody>
						<?
						//po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action
						$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
						$i = 1;
						$item_color = str_replace("'", "", $item_color);
						$item_size = str_replace("'", "", $item_size);
						//echo $item_color.'=='.$item_size;
						$item_color_con2 = "";
						$item_size_con2 = "";
						if ($item_color != "") {
							if ($item_color == 404) {
								$item_color_con2 = " and d.item_color in(0,404)";
							} else {
								$item_color_con2 = " and d.item_color=$item_color";
							}
						}

						if ($item_size) {
							$item_size_con2 = " and d.item_size='$item_size'";
						}

						$mrr_sql = "SELECT a.id, a.transfer_system_id as recv_number, a.challan_no, a.transfer_date as receive_date, c.quantity as quantity, 0 as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.entry_form in(78,112) and c.trans_type in(5)  and  c.po_breakdown_id in($po_id)   and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2";
						//echo $mrr_sql;

						$dtlsArray = sql_select($mrr_sql);
						$tot_reject_qty = 0;
						$tot_qty = 0;
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							if ($row[csf('quantity')] > 0) {
								if ($row[csf('trans_type')] == 1) $trans_type = "Receive";
								else if ($row[csf('trans_type')] == 3) $trans_type = "Receive Rtn";
								else $trans_type = "Transfer In";
						?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

									<td align="center">
										<p><? echo $i; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('prod_id')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $trans_type ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('recv_number')]; ?></p>
									</td>
									<td align="center">
										<p><? echo $row[csf('challan_no')]; ?></p>
									</td>
									<td align="center">
										<p><? echo change_date_format($row[csf('receive_date')]); ?></p>
									</td>
									<td>
										<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('quantity')], 2); ?></p>
									</td>
									<td align="right">
										<p><? echo number_format($row[csf('reject_qty')], 2); ?></p>
									</td>
								</tr>
						<?
								$tot_qty += $row[csf('quantity')];
								$tot_reject_qty += $row[csf('reject_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_reject_qty, 2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

//Trims Issue 
if ($action == "issue_popup_color") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Issue. Qty.</th>
						<th>Sewing Line</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$item_color = str_replace("'", "", $item_color);
					$item_size = str_replace("'", "", $item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;
					$item_color_con2 = "";
					$item_size_con2 = "";
					if ($item_color != "") {
						if ($item_color == 404) {
							$item_color_con2 = " and d.item_color in(0,404)";
						} else {
							$item_color_con2 = " and d.item_color=$item_color";
						}
					}

					if ($item_size) {
						$item_size_con2 = " and d.gmts_size='$item_size'";
					}
					//echo $item_color_con.'=='.$item_size_con;
					$mrr_sql = "SELECT a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.item_description, c.quantity as quantity, b.sewing_line, c.trans_type
					from  inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.prod_id=d.id and a.entry_form in(25) and c.trans_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $item_color_con2 $item_size_con2";
					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('item_description')]; ?></p>
								</td>
								<td align="right">
									<p><? echo number_format($row[csf('quantity')], 2); ?></p>
								</td>
								<td align="center">
									<p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p>
								</td>
							</tr>
						<?
							if ($row[csf('trans_type')] == 2 || $row[csf('trans_type')] == 6) {
								$tot_qty += $row[csf('quantity')];
							} else {
								$tot_qty -= $row[csf('quantity')];
							}

							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}

if ($action == "issue_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Issue. Qty.</th>
						<th>Sewing Line</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$item_color = str_replace("'", "", $item_color);
					$item_size = str_replace("'", "", $item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;
					$item_color_con2 = "";
					$item_size_con2 = "";
					if ($item_color != "") {
						if ($item_color == 404) {
							$item_color_con2 = " and d.item_color in(0,404)";
						} else {
							$item_color_con2 = " and d.item_color=$item_color";
						}
					}

					if ($item_size) {
						$item_size_con2 = " and d.item_size='$item_size'";
					}
					//echo $item_color_con.'=='.$item_size_con;
					$mrr_sql = "SELECT a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.item_description, c.quantity as quantity, b.sewing_line, c.trans_type
					from  inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.prod_id=d.id and a.entry_form in(25) and c.trans_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $item_color_con2 $item_size_con2";
					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('item_description')]; ?></p>
								</td>
								<td align="right">
									<p><? echo number_format($row[csf('quantity')], 2); ?></p>
								</td>
								<td align="center">
									<p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p>
								</td>
							</tr>
						<?
							if ($row[csf('trans_type')] == 2 || $row[csf('trans_type')] == 6) {
								$tot_qty += $row[csf('quantity')];
							} else {
								$tot_qty -= $row[csf('quantity')];
							}

							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}
//Trims Issue wo_receive_popup
if ($action == "issue_popup2") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th width="80">Issue. Qty.</th>
						<th>Sewing Line</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$item_color = str_replace("'", "", $item_color);
					$item_size = str_replace("'", "", $item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;
					$item_color_con2 = "";
					$item_size_con2 = "";
					if ($item_color != "") {
						if ($item_color == 404) {
							$item_color_con2 = " and d.item_color in(0,404)";
						} else {
							$item_color_con2 = " and d.item_color=$item_color";
						}
					}

					if ($item_size) {
						$item_size_con2 = " and d.item_size='$item_size'";
					}
					//echo $item_color_con.'=='.$item_size_con;
					$dataArr = explode("_", $item_data);
					$desc = $dataArr[1];
					$brand_supp = $dataArr[2];
					//echo $brand_supp.'d';
					if ($brand_supp == '') $brand_supp = 0;
					//echo $brand_supp.'='.$desc.'d';
					//if($brand_supp!='') $brand_suppcond="and b.brand_supplier='$brand_supp'";else $brand_suppcond='';
					//if($desc!='') $desc_cond="and d.item_description='$desc'";else $desc_cond='';


					//if($brand_supp='') $brand_suppcond="and b.brand_supplier='$brand_supp'";else $brand_supp='';
					//if($desc='') $desc_cond="and b.item_description='$desc'";else $desc_cond='';

					$mrr_sql = ("SELECT a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.item_description, c.quantity as quantity, b.sewing_line, c.trans_type
					from  inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.prod_id=d.id and a.entry_form in(25) and c.trans_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $brand_suppcond $desc_cond");

					// echo $mrr_sql;
					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('item_description')]; ?></p>
								</td>
								<td align="right">
									<p><? echo number_format($row[csf('quantity')], 2); ?></p>
								</td>
								<td align="center">
									<p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p>
								</td>
							</tr>
						<?
							if ($row[csf('trans_type')] == 2 || $row[csf('trans_type')] == 6) {
								$tot_qty += $row[csf('quantity')];
							} else {
								$tot_qty -= $row[csf('quantity')];
							}

							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}
//Trims Issue Rtn
if ($action == "issue_rtn_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="710" cellpadding="0" cellspacing="0" align="left">
					<caption>Issue Return Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th>Issue. Qty.</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$item_color = str_replace("'", "", $item_color);
					$item_size = str_replace("'", "", $item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;
					$item_color_con2 = "";
					$item_size_con2 = "";
					if ($item_color != "") {
						if ($item_color == 404) {
							$item_color_con2 = " and d.item_color in(0,404)";
						} else {
							$item_color_con2 = " and d.item_color=$item_color";
						}
					}

					if ($item_size) {
						$item_size_con2 = " and d.item_size='$item_size'";
					}
					//echo $item_color_con.'=='.$item_size_con;
					$mrr_sql = ("SELECT a.id, a.recv_number as issue_number, a.challan_no, c.prod_id, a.receive_date as issue_date, d.item_description, c.quantity as quantity, c.trans_type
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(73) and c.entry_form in(73) and b.transaction_type in(4) and c.trans_type in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $item_color_con2 $item_size_con2");

					//echo $mrr_sql;
					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('item_description')]; ?></p>
								</td>
								<td align="right">
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
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}

//Trims transfer_out
if ($action == "transfer_out_popup") {
	echo load_html_head_contents("Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:730px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="710" cellpadding="0" cellspacing="0" align="left">
					<caption>Transfer Out Details </caption>
					<thead>
						<th width="30">Sl</th>
						<th width="50">Prod. ID</th>
						<th width="90">Trans type</th>
						<th width="130">Issue. ID</th>
						<th width="80">Chalan No</th>
						<th width="80">Issue. Date</th>
						<th width="120">Item Desc.</th>
						<th>Issue. Qty.</th>
					</thead>
					<?
					$product_arr = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
					$line_arr = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i = 1;
					$item_color = str_replace("'", "", $item_color);
					$item_size = str_replace("'", "", $item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;
					$item_color_con2 = "";
					$item_size_con2 = "";
					if ($item_color != "") {
						if ($item_color == 404) {
							$item_color_con2 = " and d.item_color in(0,404)";
						} else {
							$item_color_con2 = " and d.item_color=$item_color";
						}
					}

					if ($item_size) {
						$item_size_con2 = " and d.item_size='$item_size'";
					}
					//echo $item_color_con.'=='.$item_size_con;
					$mrr_sql = ("SELECT a.id, a.transfer_system_id as issue_number, a.challan_no, c.prod_id, a.transfer_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, c.trans_type
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.trans_type in(6) and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2");

					//echo $mrr_sql;
					$dtlsArray = sql_select($mrr_sql);
					?>
					<tbody>
						<?
						foreach ($dtlsArray as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							if ($row[csf('trans_type')] == 2) $trans_type = "Issue";
							else if ($row[csf('trans_type')] == 4) $trans_type = "Issue Rtn";
							else $trans_type = "Transfer Out";
						?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center">
									<p><? echo $i; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('prod_id')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $trans_type; ?></p>
								</td>
								<td>
									<p><? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td align="center">
									<p><? echo $row[csf('challan_no')]; ?></p>
								</td>
								<td align="center">
									<p><? echo  change_date_format($row[csf('issue_date')]); ?></p>
								</td>
								<td>
									<p><? echo $row[csf('item_description')]; ?></p>
								</td>
								<td align="right">
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
							<td colspan="6" align="right"></td>
							<td align="right">Total</td>
							<td><? echo number_format($tot_qty, 2); ?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('list_view', -1);
		</script>>
	<?
	exit();
}
if ($action == "order_details_popup") {
	echo load_html_head_contents("WO Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
		<fieldset style="width:800px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="800" cellpadding="0" cellspacing="0" align="center">
					<caption>PO Detail</caption>
					<thead>
						<th width="30">Sl</th>
						<th width="100">Order No</th>
						<th width="100">PO Receive Date</th>
						<th width="100">PHD/Ready Date</th>
						<th width="100">Pub. Shipment Date</th>
						<th width="100">Order Qty.</th>
						<th width="100">Order Status</th>
					</thead>
					<tbody>
						<?


					$sql_bookingqty = "SELECT b.po_number,b.id as po_id,(a.total_set_qnty*b.po_quantity) as po_quantity,b.po_received_date,b.pack_handover_date,b.pub_shipment_date,b.is_confirmed from wo_po_details_master a, wo_po_break_down b where a.job_no=b job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no='$job_no' and a.company_name=$companyID order by b.id";
						$dtlsArray = sql_select($sql_bookingqty);
						$orderStatus = array(1 => 'Confirmed', 2 => 'Projected');
						$i = 1;
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
								<td width="100" align="center">
									<p><? echo $row[csf('po_number')]; ?></p>
								</td>
								<td width="75" align="center">
									<p><? echo change_date_format($row[csf('po_received_date')]); ?></p>
								</td>

								<td width="80" align="center">
									<p><? echo change_date_format($row[csf('pack_handover_date')]);; ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo change_date_format($row[csf('pub_shipment_date')]) ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo number_format($row[csf('po_quantity')], 2); ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $orderStatus[$row[csf('is_confirmed')]]; ?></p>
								</td>
							</tr>
						<?

							$i++;
						}
						?>
					</tbody>
					<!-- <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty, 2); ?>&nbsp;</td>
                    </tr>
                </tfoot> -->
				</table>
				<br>

			</div>
		</fieldset>
	<?
	exit();
}

	?>