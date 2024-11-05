<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Created by		 : Zakaria joy
Creation date 	 : 20-02-2023
Updated by 		 :
Update date		 :
QC Performed BY	 :
QC Date			 :
Comments		 : 
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission = $_SESSION['page_permission'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");
$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");
$country_name_library = return_library_array("select id,country_name from lib_country", "id", "country_name");


if ($action == "load_button") {
	echo "$('#print_booking1').hide();";
	echo "$('#print_booking2').hide();";
	echo "$('#print_booking3').hide();";
	echo "$('#print_booking4').hide();";
	echo "$('#print_booking5').hide();";
	echo "$('#print_booking7').hide();";
	echo "$('#print_booking9').hide();";

	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=2 and report_id=89 and is_deleted=0 and status_active=1");
	foreach (explode(',', $print_report_format) as $button_id) {
		if ($button_id == 13) {
			echo "$('#print_booking1').show();";
		}
		if ($button_id == 15) {
			echo "$('#print_booking2').show();";
		}
		if ($button_id == 16) {
			echo "$('#print_booking3').show();";
		}
		if ($button_id == 177) {
			echo "$('#print_booking4').show();";
		}
		if ($button_id == 175) {
			echo "$('#print_booking5').show();";
		}
		if ($button_id == 746) {
			echo "$('#print_booking7').show();";
		}
		if ($button_id == 235) {
			echo "$('#print_booking9').show();";
		}
		
	}
	exit();
}


if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
}
if ($action == "load_attention") {
	$supplier_name = return_field_value("contact_person", "lib_supplier", "id ='" . $data . "' and is_deleted=0 and status_active=1");
	echo $supplier_name;
	exit();
}

if ($action == "supplier_company_action2") {
	$data = explode("_", $data);
	$company = $data[0];
	$pay_mode_id = $data[1];
	if ($pay_mode_id == 3 || $pay_mode_id == 5) {
		$sql = "select c.id, c.company_name as label from lib_company c where c.status_active=1 and c.is_deleted=0 group by c.id, c.company_name order by company_name";
	} else {
		$sql = "select c.id, c.supplier_name as label from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$company and b.party_type =23 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";
	}

	$result = sql_select($sql);
	$supplierArr = array();
	foreach ($result as $key => $val) {
		$supplierArr[$key]["id"] = $val[csf("id")];
		$supplierArr[$key]["label"] = $val[csf("label")];
	}
	echo json_encode($supplierArr);
	exit();
}
if ($action == "supplier_company_action") {
	$data = explode("_", $data);
	$company = $data[0];
	$pay_mode = $data[1];
	if ($pay_mode == 1 || $pay_mode == 2 || $pay_mode == 4) {
		$sql = "select c.id, c.supplier_name as label from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$company  and c.status_active=1 and c.is_deleted=0 and b.party_type=23 group by c.id, c.supplier_name order by supplier_name";
	} else {
		$sql = "select c.id, c.company_name as label from lib_company c where c.status_active=1 and c.is_deleted=0 group by c.id, c.company_name order by company_name";
	}
	$result = sql_select($sql);
	$supplierArr = array();
	foreach ($result as $key => $val) {
		$supplierArr[$key]["id"] = $val[csf("id")];
		$supplierArr[$key]["label"] = $val[csf("label")];
	}
	echo json_encode($supplierArr);
	exit();
}

if ($action == "fabric_emb_item_popup") {
	echo load_html_head_contents("Booking Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_id = new Array();
		var selected_name = new Array();
		var selected_item = new Array();
		var selected_po = new Array();

		function js_set_value(str) {
			if ($("#search" + str).css("display") != 'none') {
				toggle(document.getElementById('search' + str), '#FFFFCC');
				if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
					selected_id.push($('#txt_individual_id' + str).val());
					selected_name.push($('#txt_job_no' + str).val());
					selected_item.push($('#precost_emb_id' + str).val());
					selected_po.push($('#txt_po_id' + str).val());
				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
					selected_item.splice(i, 1);
					selected_po.splice(i, 1);
				}
			}
			var id = '';
			var job = '';
			var precost_emb_id = '';
			var txt_po_id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				job += selected_name[i] + ',';
				precost_emb_id += selected_item[i] + ',';
				txt_po_id += selected_po[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			job = job.substr(0, job.length - 1);
			precost_emb_id = precost_emb_id.substr(0, precost_emb_id.length - 1);
			txt_po_id = txt_po_id.substr(0, txt_po_id.length - 1);
			$('#txt_selected_id').val(id);
			$('#txt_job_id').val(job);
			$('#emb_id').val(precost_emb_id);
			$('#txt_selected_po').val(txt_po_id);
		}

		function check() {

			var cbo_company_name = document.getElementById('cbo_company_name').value;
			var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
			var cbo_supplier_name = document.getElementById('cbo_supplier_name').value;
			var cbo_year_selection = document.getElementById('cbo_year_selection').value;

			var cbo_currency = document.getElementById('cbo_currency').value;
			var txt_style = document.getElementById('txt_style').value;
			var txt_order_search = document.getElementById('txt_order_search').value;
			var txt_job = document.getElementById('txt_job').value;
			var cbo_isshort = document.getElementById('cbo_isshort').value;

			var cbo_item = document.getElementById('cbo_item').value;
			var txt_ref_no = document.getElementById('txt_ref_no').value;
			show_list_view(cbo_company_name + '_' + cbo_buyer_name + '_' + cbo_supplier_name + '_' + cbo_year_selection + '_' + cbo_currency + '_' + txt_style + '_' + txt_order_search + '_' + txt_job + '_' + cbo_item + '_' + txt_ref_no + '_' + '<? echo $txt_booking_no; ?>' + '_' + '<? echo $cbo_level; ?>' + '_' + cbo_isshort, 'create_fnc_process_data', 'search_div', 'multi_job_additional_print_booking_controller', setFilterGrid('tbl_list_search', -1))
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th width="100">Style Ref </th>
							<th width="100">Job No </th>
							<th width="60">Year</th>
							<th width="100">Int. Ref. No </th>
							<th width="100">Order No</th>
							<th width="120">Item Name</th>
							<th>&nbsp;
								<input type="hidden" name="txt_garments_nature" id="txt_garments_nature" value="<? echo $garments_nature; ?>" />
								<input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id; ?>" />
								<input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name; ?>" />
								<input type="hidden" name="cbo_currency" id="cbo_currency" value="<? echo $cbo_currency; ?>" />
								<input type="hidden" name="cbo_supplier_name" id="cbo_supplier_name" value="<? echo $cbo_supplier_name; ?>" />
								<input type="hidden" name="cbo_isshort" id="cbo_isshort" value="<? echo $cbo_isshort; ?>" />
							</th>
						</tr>
					</thead>
					<tr>
						<td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
						<td><input name="txt_job" id="txt_job" class="text_boxes" style="width:90px"></td>
						<td><? echo create_drop_down("cbo_year_selection", 60, $year, "", 1, "-- Select --", date('Y'), "", 0);	?></td>
						<td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px"></td>
						<td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
						<td><? echo create_drop_down("cbo_item", 120, $emblishment_name_array, "", 1, "-- Select Emb Name --", $selected, "", 0); ?></td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="check()" style="width:60px;" />
						</td>
					</tr>
				</table>

			</form>
			<div id="search_div"></div>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_fnc_process_data") {
	$data = explode('_', $data);
	$company_id = $data[0];
	$cbo_buyer_name = $data[1];
	$cbo_supplier_name = $data[2];
	$cbo_year_selection = $data[3];
	$cbo_currency = $data[4];

	$txt_style = $data[5];
	$txt_order_search = $data[6];
	$txt_job = $data[7];
	$cbo_item = $data[8];
	$ref_no = $data[9];
	$booking_no = $data[10];
	$cbo_level = $data[11];
	$cbo_isshort = $data[12];

	if ($db_type == 0) {
		/*$year_field = "YEAR(a.insert_date) as year";*/
		$year_cond = " and YEAR(a.insert_date) = $cbo_year_selection ";
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year";
		$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year_selection ";
	}

	if ($txt_style != "") $style_cond = " and a.style_ref_no='$txt_style'";
	else $style_cond = $txt_style;
	if ($txt_order_search != "") $order_cond = " and d.po_number='$txt_order_search'";
	else $order_cond = "";
	if ($ref_no != "") $ref_cond = " and d.grouping='$ref_no'";
	else $ref_cond = "";
	if ($txt_job != "") $job_cond = " and a.job_no_prefix_num='$txt_job'";
	else $job_cond = "";
	if ($cbo_item != 0) $itemgroup_cond = " and c.emb_name=$cbo_item";
	else $itemgroup_cond = "";
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

?>
	</head>

	<body>
		<div style="width:1220px;">
			<?
			extract($_REQUEST);
			?>
			<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
			<input type="hidden" name="emb_id" id="emb_id" value="" />
			<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
			<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
			<table cellspacing="0" cellpadding="0" rules="all" width="1320" class="rpt_table">
				<thead>
					<th width="20">SL</th>
					<th width="50">Buyer</th>
					<th width="50">Year</th>
					<th width="50">Job No</th>
					<th width="60">File No</th>
					<th width="60">Ref. No</th>
					<th width="100">Style No</th>
					<th width="100">Ord. No</th>
					<th width="100">Garmentes Item</th>
					<th width="100">Embl. Name</th>
					<th width="130">Embl. Type</th>
					<th width="70">Body Part</th>
					<th width="70">Req. Qty</th>
					<th width="45">UOM</th>
					<th width="70">CU WOQ</th>
					<th width="70">Bal WOQ</th>
					<th width="45">Exch. Rate</th>
					<th width="40">Rate</th>
					<th>Amount</th>
				</thead>
			</table>
			<div style="width:1320px; overflow-y:scroll; max-height:350px;" id="buyer_list_view">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table" id="tbl_list_search">
					<?
					if ($db_type == 0) {

						$year_field = "YEAR(a.insert_date) as year";
					} else if ($db_type == 2) {
						$year_field = "to_char(a.insert_date,'YYYY') as year";
					}
					$condition = new condition();
					if (str_replace("'", "", $company_id) != '') {
						$condition->company_name("=$company_id");
					}
					if (str_replace("'", "", $cbo_buyer_name) != '') {
						$condition->buyer_name("=$cbo_buyer_name");
					}
					if (str_replace("'", "", $txt_job) != '') {
						$condition->job_no_prefix_num("=$txt_job");
					}
					if (str_replace("'", "", $txt_order_search) != '') {
						$condition->po_number("='$txt_order_search'");
					}
					if (str_replace("'", "", $txt_style) != '') {
						$condition->style_ref_no("='$txt_style'");
					}
					if (str_replace("'", "", $ref_no) != '') {
						$condition->grouping("='$ref_no'");
					}

					$condition->init();
					$emblishment = new emblishment($condition);
					$req_qty_arr = $emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
					//echo $emblishment->getQuery(); die;

					$req_amount_arr = $emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();
					$wash = new wash($condition);
					$req_qty_arr_wash = $wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
					$req_amount_arr_wash = $wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();
					//print_r($req_qty_arr_wash);

					$cu_booking_arr = array();
					$sql_cu_booking = sql_select("select c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_mst b,wo_booking_dtls c where a.id=d.job_id and a.job_no=c.job_no and b.booking_no=c.booking_no and  d.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name  and b.booking_type=11  and c.booking_type=11 and b.entry_form=612 and d.is_deleted=0 and d.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $job_cond $order_cond $ref_cond $style_cond $year_cond group by a.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item");
					foreach ($sql_cu_booking as $row_cu_booking) {
						$cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]][$row_cu_booking[csf('gmt_item')]]['cu_wo_qnty'] = $row_cu_booking[csf('cu_wo_qnty')];
						$cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]][$row_cu_booking[csf('gmt_item')]]['cu_amount'] = $row_cu_booking[csf('cu_amount')];
					}
					unset($sql_cu_booking);

		$approval_allow = sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b 
where a.id=b.mst_id and a.company_id='$company_id' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");

		if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 1)
			$approval_cond = "and b.approved in (1,3)";
		else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 2)
			$approval_cond = "and b.approved in (1)";
		else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 0)
			$approval_cond = "and b.approved in (1,3)";
		else $approval_cond = "";


		$sql = "select a.job_no_prefix_num, $year_field,a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, a.order_uom, b.costing_per, b.exchange_rate, b.entry_from,  c.id as precost_emb_id, c.emb_name, c.emb_type,c.body_part_id, c.rate, d.id as po_id, d.po_number, d.file_no, d.grouping, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id,e.item_number_id, avg(e.requirment) AS cons
        from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e

        where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$company_id and
        a.buyer_name=$cbo_buyer_name and (c.supplier_id = $cbo_supplier_name or c.supplier_id= 0 ) $approval_cond and d.shiping_status not in(3)  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0
         and b.status_active=1 
         and c.status_active=1
         and e.status_active=1
        " . $buyer_cond_test . " $itemgroup_cond $job_cond $order_cond $ref_cond $style_cond $year_cond
        group by
        a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name,c.emb_type,c.body_part_id, c.rate, a.insert_date, d.id, d.po_number, d.file_no, d.grouping, d.po_quantity, e.po_break_down_id, e.item_number_id, a.order_uom,b.entry_from  order by d.id, c.id";

					$i = 1;
					$req_qty = 0;
					$req_amount = 0;
					$rate = 0;
					$total_req = 0;
					$total_amount = 0;
					$nameArray = sql_select($sql);

					foreach ($nameArray as $selectResult) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						$cbo_currency_job = $selectResult[csf('currency_id')];
						$exchange_rate = $selectResult[csf('exchange_rate')];
						if ($cbo_currency == $cbo_currency_job) {
							$exchange_rate = 1;
						}
						if ($selectResult[csf('emb_name')] == 3) {
							$req_qty = $req_qty_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
							$req_amount = $req_amount_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
							//echo $req_qty."<br/>" ;
						} else {
							$req_qty = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
							$req_amount = $req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('precost_emb_id')]][$selectResult[csf('item_number_id')]];
							//echo $req_qty."<br/>" ;
						}
						$rate = $req_amount / $req_qty;

						$cu_wo_qnty = $cu_booking_arr[$selectResult[csf('precost_emb_id')]][$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]]['cu_wo_qnty'];
						$cu_wo_amnt = $cu_booking_arr[$selectResult[csf('precost_emb_id')]][$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]]['cu_amount'];
						$bal_woq = def_number_format($req_qty - $cu_wo_qnty, 5, "");
						$bal_wom = def_number_format($req_amount - $cu_wo_amnt, 5, "");

						$total_req += $req_qnty;
						$total_req_amount += $req_amount;
						$total_cu_amount += $selectResult[csf('cu_amount')];
						$amount = def_number_format($rate * $bal_woq, 4, "");
						//echo $selectResult[csf('emb_name')]."==".$bal_woq."==".$cu_wo_qnty."<br/>" ;
						//if($bal_woq>0 && ($cu_wo_qnty=="" || $cu_wo_qnty==0) && $cbo_isshort==1)
						$uom_name = '';
						if ($selectResult[csf('entry_from')] == 520) {
							$uom_name = $unit_of_measurement[$selectResult[csf('order_uom')]];
						} else {
							if ($selectResult[csf('costing_per')] == 2) {
								$uom_name = 'Pcs';
							} else {
								$uom_name = 'Dzn';
							}
						}
						//echo $selectResult[csf('costing_per')].'DD';

						if ($cbo_isshort == 1) {
					?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
								<td width="20"><? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>" />
									<input type="hidden" name="precost_emb_id" id="precost_emb_id<?php echo $i ?>" value="<? echo $selectResult[csf('precost_emb_id')]; ?>" />
									<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>" />
									<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>" />
									<input type="hidden" name="hiddemb_name" id="hiddemb_name<?php echo $i ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" />
								</td>
								<td width="50">
									<p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p>
								</td>
								<td width="50">
									<p><? echo $selectResult[csf('year')]; ?></p>
								</td>
								<td width="50">
									<p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p>
								</td>
								<td width="60">
									<p><? echo $selectResult[csf('file_no')]; ?></p>
								</td>
								<td width="60">
									<p><? echo $selectResult[csf('grouping')]; ?></p>
								</td>
								<td width="100">
									<div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')]; ?></div>
								</td>
								<td width="100">
									<p><? echo $selectResult[csf('po_number')]; ?></p>
								</td>
								<td width="100">
									<p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p>
								</td>
								<td width="100">
									<div style="width:100px; word-wrap:break-word;"><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?></div>
								</td>
								<td width="130" id="td_item_des<?php echo $i; ?>">
									<div style="width:130px; word-wrap:break-word;">
										<?
										if ($selectResult[csf('emb_name')] == 1) $emb_type = $emblishment_print_type[$selectResult[csf('emb_type')]];
										if ($selectResult[csf('emb_name')] == 2) $emb_type = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
										if ($selectResult[csf('emb_name')] == 3) $emb_type = $emblishment_wash_type[$selectResult[csf('emb_type')]];
										if ($selectResult[csf('emb_name')] == 4) $emb_type = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
										if ($selectResult[csf('emb_name')] == 5) $emb_type = $emblishment_gmts_type[$selectResult[csf('emb_type')]];

										echo $emb_type;
										?>
									</div>
								</td>
								<td width="70">
									<div style="width:70px; word-wrap:break-word;"><? echo $body_part[$selectResult[csf('body_part_id')]]; ?></div>
								</td>
								<td width="70" align="right"><? echo number_format($req_qty, 4); ?></td>
								<td width="45">Dzn<? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
													?></td>
								<td width="70" align="right"><? echo def_number_format($cu_wo_qnty, 5, ""); ?></td>
								<td width="70" align="right"><? echo number_format($bal_woq, 4); ?></td>
								<td width="45" align="right">
									<p><? echo number_format($exchange_rate, 2); ?></p>
								</td>
								<td width="40" align="right">
									<p><? echo number_format($rate, 4); ?></p>
								</td>
								<td align="right"><? echo number_format($amount, 2); ?></td>
							</tr>
							<?
							$i++;
							$total_amount += $amount;
						} else {
							if ($bal_woq > 0 && ($cu_wo_qnty == "" || $cu_wo_qnty == 0)) {
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="20"><? echo $i; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>" />
										<input type="hidden" name="precost_emb_id" id="precost_emb_id<?php echo $i ?>" value="<? echo $selectResult[csf('precost_emb_id')]; ?>" />
										<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>" />
										<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>" />
										<input type="hidden" name="hiddemb_name" id="hiddemb_name<?php echo $i ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" />
									</td>
									<td width="50">
										<p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p>
									</td>
									<td width="50">
										<p><? echo $selectResult[csf('year')]; ?></p>
									</td>
									<td width="50">
										<p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p>
									</td>
									<td width="60">
										<p><? echo $selectResult[csf('file_no')]; ?></p>
									</td>
									<td width="60">
										<p><? echo $selectResult[csf('grouping')]; ?></p>
									</td>
									<td width="100">
										<div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')]; ?></div>
									</td>
									<td width="100">
										<p><? echo $selectResult[csf('po_number')]; ?></p>
									</td>
									<td width="100">
										<p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p>
									</td>
									<td width="100">
										<div style="width:100px; word-wrap:break-word;"><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?></div>
									</td>
									<td width="130" id="td_item_des<?php echo $i; ?>">
										<div style="width:130px; word-wrap:break-word;">
											<?
											if ($selectResult[csf('emb_name')] == 1) $emb_type = $emblishment_print_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 2) $emb_type = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 3) $emb_type = $emblishment_wash_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 4) $emb_type = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 5) $emb_type = $emblishment_gmts_type[$selectResult[csf('emb_type')]];

											echo $emb_type;
											?>
										</div>
									</td>
									<td width="70">
										<div style="width:70px; word-wrap:break-word;"><? echo $body_part[$selectResult[csf('body_part_id')]]; ?></div>
									</td>
									<td width="70" align="right"><? echo number_format($req_qty, 4); ?></td>
									<td width="45"><?= $uom_name ?><? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
																	?></td>
									<td width="70" align="right"><? echo def_number_format($cu_wo_qnty, 5, ""); ?></td>
									<td width="70" align="right"><? echo number_format($bal_woq, 4); ?></td>
									<td width="45" align="right">
										<p><? echo number_format($exchange_rate, 2); ?></p>
									</td>
									<td width="40" align="right">
										<p><? echo number_format($rate, 4); ?></p>
									</td>
									<td align="right"><? echo number_format($amount, 2); ?></td>
								</tr>
							<?
								$i++;
								$total_amount += $amount;
							} elseif ($bal_woq >= 1 && $cu_wo_qnty > 0) {
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="20"><? echo $i; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>" />
										<input type="hidden" name="precost_emb_id" id="precost_emb_id<?php echo $i ?>" value="<? echo $selectResult[csf('precost_emb_id')]; ?>" />
										<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>" />
										<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>" />
										<input type="hidden" name="hiddemb_name" id="hiddemb_name<?php echo $i ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" />
									</td>
									<td width="50">
										<p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p>
									</td>
									<td width="50">
										<p><? echo $selectResult[csf('year')]; ?></p>
									</td>
									<td width="50">
										<p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p>
									</td>
									<td width="60">
										<p><? echo $selectResult[csf('file_no')]; ?></p>
									</td>
									<td width="60">
										<p><? echo $selectResult[csf('grouping')]; ?></p>
									</td>
									<td width="100">
										<div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')]; ?></div>
									</td>
									<td width="100">
										<p><? echo $selectResult[csf('po_number')]; ?></p>
									</td>
									<td width="100">
										<p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p>
									</td>
									<td width="100">
										<div style="width:100px; word-wrap:break-word;"><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?></div>
									</td>
									<td width="130" id="td_item_des<?php echo $i; ?>">
										<div style="width:130px; word-wrap:break-word;">
											<?
											if ($selectResult[csf('emb_name')] == 1) $emb_type = $emblishment_print_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 2) $emb_type = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 3) $emb_type = $emblishment_wash_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 4) $emb_type = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
											if ($selectResult[csf('emb_name')] == 5) $emb_type = $emblishment_gmts_type[$selectResult[csf('emb_type')]];

											echo $emb_type;
											?>
										</div>
									</td>
									<td width="70">
										<div style="width:70px; word-wrap:break-word;"><? echo $body_part[$selectResult[csf('body_part_id')]]; ?></div>
									</td>
									<td width="70" align="right"><? echo number_format($req_qty, 4); ?></td>
									<td width="45"><?= $uom_name ?><? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
																	?></td>
									<td width="70" align="right"><? echo def_number_format($cu_wo_qnty, 5, ""); ?></td>
									<td width="70" align="right"><? echo number_format($bal_woq, 4); ?></td>
									<td width="45" align="right">
										<p><? echo number_format($exchange_rate, 2); ?></p>
									</td>
									<td width="40" align="right">
										<p><? echo number_format($rate, 4); ?></p>
									</td>
									<td align="right"><? echo number_format($amount, 2); ?></td>
								</tr>
					<?
								$i++;
								$total_amount += $amount;
							}
						}
					}
					?>
				</table>
			</div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1320" class="rpt_table">
				<tfoot>
					<th width="20">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="130">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70" id="value_total_req"></th>
					<th width="45"><input type="hidden" style="width:40px" id="txt_tot_req_amount" value="<? echo number_format($total_req_amount, 2); ?>" /></th>
					<th width="70"><input type="hidden" style="width:40px" id="txt_tot_cu_amount" value="<? echo number_format($total_cu_amount, 2); ?>" /></th>
					<th width="70">&nbsp;</th>
					<th width="45">&nbsp;</th>
					<th width="40">&nbsp;</th>
					<th id="value_total_amount"><? echo number_format($total_amount, 2); ?></th>
				</tfoot>
			</table>

			<table width="790" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>
			<script>
				var tableFilters = {
					col_operation: {
						id: ["value_total_req", "value_total_amount"],
						col: [11, 17],
						operation: ["sum", "sum"],
						write_method: ["innerHTML", "innerHTML"]
					}
				}
				setFilterGrid('tbl_list_search', -1, tableFilters)
			</script>
		</div>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "generate_fabric_booking") {
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($garments_nature == 0) {
		$garment_nature_cond = "";
	} else {
		$garment_nature_cond = " and a.garments_nature=$garments_nature";
	}
	$param = implode(",", array_unique(explode(",", str_replace("'", "", $param))));
	$data = implode(",", array_unique(explode(",", str_replace("'", "", $data))));
	$pre_cost_id = implode(",", array_unique(explode(",", str_replace("'", "", $pre_cost_id))));
	$condition = new condition();
	if (str_replace("'", "", $data) != '') {
		$condition->po_id("in($data)");
	}
	$condition->init();

	$emblishment = new emblishment($condition);
	$req_qty_arr = $emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
	$req_amount_arr = $emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();

	$wash = new wash($condition);
	$req_qty_arr_wash = $wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
	$req_amount_arr_wash = $wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();

	$cu_booking_arr = array();
	$sql_cu_booking = sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_mst b, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  b.booking_no=c.booking_no and b.entry_form=612  d.id=c.po_break_down_id and a.company_name=$cbo_company_name  and c.status_active=1 and c.is_deleted=0 and c.booking_type=11   group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item");
	foreach ($sql_cu_booking as $row_cu_booking) {
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
	}

	$sql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no,a.order_uom, b.entry_from, b.costing_per, b.exchange_rate, c.id as pre_cost_emb_cost_dtls_id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, e.item_number_id, avg(e.requirment) as cons from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$cbo_company_name  and e.id in($param) and e.po_break_down_id in($data) and c.id in($pre_cost_id) and d.is_deleted=0 and d.status_active=1 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, e.item_number_id, a.order_uom, b.entry_from order by d.id,c.id";
	//echo $sql; die;

	$job_and_trimgroup_level = array();
	$i = 1;
	$nameArray = sql_select($sql);

	foreach ($nameArray as $selectResult) {
		$cbo_currency_job = $selectResult[csf('currency_id')];
		$exchange_rate = $selectResult[csf('exchange_rate')];
		$order_uom = $selectResult[csf('order_uom')];
		$entry_from = $selectResult[csf('entry_from')];

		if ($cbo_currency == $cbo_currency_job) {
			$exchange_rate = 1;
		}
		if ($selectResult[csf('emb_name')] == 3) {
			$req_qnty_cons_uom = $req_qty_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
			$req_amount_cons_uom = $req_amount_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
		} else {
			$req_qnty_cons_uom = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
			$req_amount_cons_uom = $req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
		}
		$rate_cons_uom = $req_amount_cons_uom / $req_qnty_cons_uom;


		$cu_woq = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['cu_woq'][$selectResult[csf('po_id')]];
		$cu_amount = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['cu_amount'][$selectResult[csf('po_id')]];

		$bal_woq = def_number_format($req_qnty_cons_uom - $cu_woq, 5, "");
		$amount = def_number_format($rate_cons_uom * $bal_woq, 5, "");

		if ($selectResult[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['job_no'][$selectResult[csf('po_id')]] = $selectResult[csf('job_no')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['po_id'][$selectResult[csf('po_id')]] = $selectResult[csf('po_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['po_number'][$selectResult[csf('po_id')]] = $selectResult[csf('po_number')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['item_number_id'][$selectResult[csf('po_id')]] = $selectResult[csf('item_number_id')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['country'][$selectResult[csf('po_id')]] = $selectResult[csf('country')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['body_part_id'][$selectResult[csf('po_id')]] = $selectResult[csf('body_part_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['body_part'][$selectResult[csf('po_id')]] = $body_part[$selectResult[csf('body_part_id')]];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['emb_type'][$selectResult[csf('po_id')]] = $selectResult[csf('emb_type')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['emb_type_name'][$selectResult[csf('po_id')]] = $emb_type_name;

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['emb_name'][$selectResult[csf('po_id')]] = $selectResult[csf('emb_name')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['costing_per'][$selectResult[csf('po_id')]] = $selectResult[csf('costing_per')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['emb_name_name'][$selectResult[csf('po_id')]] = $emblishment_name_array[$selectResult[csf('emb_name')]];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['pre_cost_emb_cost_dtls_id'][$selectResult[csf('po_id')]] = $selectResult[csf('pre_cost_emb_cost_dtls_id')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['req_qnty'][$selectResult[csf('po_id')]] = $req_qnty_cons_uom;

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['req_amount'][$selectResult[csf('po_id')]] = $req_amount_cons_uom;


		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['cu_woq'][$selectResult[csf('po_id')]] = $cu_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['cu_amount'][$selectResult[csf('po_id')]] = $cu_amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['bal_woq'][$selectResult[csf('po_id')]] = $bal_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['exchange_rate'][$selectResult[csf('po_id')]] = $exchange_rate;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['rate'][$selectResult[csf('po_id')]] = $rate_cons_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['amount'][$selectResult[csf('po_id')]] = $amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['txt_delivery_date'][$selectResult[csf('po_id')]] = $txt_delivery_date;
	}
	// print_r($job_and_trimgroup_level);
?>

	<input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">Job No</th>
			<th width="100">Ord. No</th>
			<th width="100">Garmentes Item</th>
			<th width="100">Emb. Name</th>
			<th width="150">Body Part</th>
			<th width="150">Emb. Type</th>
			<th width="70">Req. Qnty</th>
			<th width="50">UOM</th>
			<th width="80">CU WOQ</th>
			<th width="80">Bal WOQ</th>
			<th width="100">Sensitivity</th>
			<th width="80">WOQ</th>
			<th width="55">Exch.Rate</th>
			<th width="80">Rate</th>
			<th width="80">Amount</th>
			<th width="">Delv. Date</th>
		</thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search">
		<tbody>
			<?
			if ($cbo_level == 1) {
				foreach ($nameArray as $selectResult) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$cbo_currency_job = $selectResult[csf('currency_id')];
					$exchange_rate = $selectResult[csf('exchange_rate')];
					if ($cbo_currency == $cbo_currency_job) {
						$exchange_rate = 1;
					}

					if ($selectResult[csf('emb_name')] == 3) {
						$req_qnty_cons_uom = $req_qty_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
						$req_amount_cons_uom = $req_amount_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
					} else {
						$req_qnty_cons_uom = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
						$req_amount_cons_uom = $req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
					}
					$rate_cons_uom = $req_amount_cons_uom / $req_qnty_cons_uom;


					$cu_woq = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['cu_woq'][$selectResult[csf('po_id')]];
					$cu_amount = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]]['cu_amount'][$selectResult[csf('po_id')]];

					$bal_woq = def_number_format($req_qnty_cons_uom - $cu_woq, 5, "");
					$amount = def_number_format($rate_cons_uom * $bal_woq, 5, "");

					if ($selectResult[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];

					$uom_name = '';
					$uom_id = 0;
					if ($selectResult[csf('entry_from')] == 520) {
						$uom_name = $unit_of_measurement[$selectResult[csf('order_uom')]];
						$uom_id = $selectResult[csf('order_uom')];
					} else {
						if ($selectResult[csf('costing_per')] == 2) {
							$uom_name = 'Pcs';
							$uom_id = 1;
						} else {
							$uom_name = 'Dzn';
							$uom_id = 2;
						}
					}
							if($cbo_currency==1) //Tk
							{
								$rate_cons_uom =$rate_cons_uom*$exchange_rate;
								$amount=$bal_woq*$rate_cons_uom;
								$req_amount_cons_uom=$bal_woq*$rate_cons_uom;
							}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="80">
							<? echo $selectResult[csf('job_no')]; ?>
							<input type="hidden" id="txtjob_<? echo $i; ?>" value="<? echo $selectResult[csf('job_no')]; ?>" style="width:30px" class="text_boxes" readonly />
						</td>
						<td width="100">
							<? echo $selectResult[csf('po_number')]; ?>
							<input type="hidden" id="txtbookingid_<? echo $i; ?>" value="" readonly />
							<input type="hidden" id="txtpoid_<? echo $i; ?>" value="<? echo $selectResult[csf('po_id')]; ?>" readonly />
							<input type="hidden" id="txtcountry_<? echo $i; ?>" value="<? echo $selectResult[csf('country')] ?>" readonly />
						</td>
						<td width="100">
							<? echo $garments_item[$selectResult[csf('item_number_id')]]; ?>
							<input type="hidden" id="txtgmtitemid_<? echo $i; ?>" value="<? echo $selectResult[csf('item_number_id')]; ?>" readonly />
						</td>
						<td width="100">
							<? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?>
							<input type="hidden" id="txtembcostid_<? echo $i; ?>" value="<? echo $selectResult[csf('pre_cost_emb_cost_dtls_id')]; ?>" readonly />
							<input type="hidden" id="emb_name_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" readonly />
						</td>
						<td width="150">
							<? echo $body_part[$selectResult[csf('body_part_id')]]; ?>
							<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i; ?>" value="<? echo $selectResult[csf('body_part_id')]; ?>" />
						</td>
						<td width="150">
							<? echo $emb_type_name; ?>
							<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_type')]; ?>" />
						</td>
						<td width="70" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i; ?>" value="<? echo number_format($req_qnty_cons_uom, 4, '.', ''); ?>" readonly />
							<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i; ?>" value="<? echo number_format($req_amount_cons_uom, 4, '.', ''); ?>" readonly />

						</td>
						<td width="50">
							<?= $uom_name ?>
							<? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
							?>
							<input type="hidden" id="txtuom_<? echo $i; ?>" value="<? echo $uom_id; ?>" readonly />
							<? //echo $sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom];
							?>
						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('cu_woq')], 4, '.', ''); ?>" readonly />
							<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i; ?>" value="<? echo number_format($selectResult[csf('cu_amount')], 4, '.', ''); ?>" readonly />
						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" readonly />
						</td>
						<td width="100" align="right">
							<? echo create_drop_down("cbocolorsizesensitive_" . $i, 100, $size_color_sensitive, "", 1, "--Select--", "4", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)", 1, "1,2,3,4"); ?>
						</td>
						<td width="80" align="right">

							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" onClick="open_consumption_popup('requires/multi_job_additional_print_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i; ?>',<? echo $i; ?>)" readonly />
						</td>
						<td width="55" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i; ?>" value="<? echo $exchange_rate; ?>" readonly />

						</td>
						<td width="80" align="right">
							<?
							$ratetexcolor = "#000000";
							$decimal = explode(".", $rate_cons_uom);
							if (strlen($decimal[1] > 6)) {
								$ratetexcolor = "#F00";
							}
							?>
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

							<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" readonly />

						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i; ?>" value="<? echo number_format($amount, 4, '.', ''); ?>" readonly />
						</td>
						<td width="" align="right">
							<input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i; ?>" class="datepicker" value="<? echo $txt_delivery_date; ?>" readonly />
							<input type="hidden" id="consbreckdown_<? echo $i; ?>" value="" />
							<input type="hidden" id="jsondata_<? echo $i; ?>" value="" />
						</td>
					</tr>
				<?
					$i++;
				}
			}
			if ($cbo_level == 2) {
				?>
				<?
				$i = 1;
				foreach ($job_and_trimgroup_level as $job_no) {
					foreach ($job_no as $wo_pre_cost_trim_cost_dtlsArr) {
						foreach ($wo_pre_cost_trim_cost_dtlsArr as $wo_pre_cost_trim_cost_dtls) {

							$job_no = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
							$po_number = implode(",", $wo_pre_cost_trim_cost_dtls['po_number']);
							$po_id = implode(",", $wo_pre_cost_trim_cost_dtls['po_id']);
							$item_number_id = implode(",", array_unique(explode(",", implode(",", $wo_pre_cost_trim_cost_dtls['item_number_id'])))); //implode(",",$wo_pre_cost_trim_cost_dtls['item_number_id']);

							$country = implode(",", array_unique(explode(",", implode(",", $wo_pre_cost_trim_cost_dtls['country']))));
							$body_part_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['body_part_id']));
							$body_part = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['body_part']));
							$emb_type = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_type']));
							$emb_type_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_type_name']));

							$pre_cost_emb_cost_dtls_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['pre_cost_emb_cost_dtls_id']));
							$emb_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_name']));
							$emb_name_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_name_name']));
							$uom = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['uom']));

							$req_qnty_cons_uom = array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
							$rate_cons_uom = array_sum($wo_pre_cost_trim_cost_dtls['req_amount']) / array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
							$req_amount_cons_uom = array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);

							$bal_woq = array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
							$amount = array_sum($wo_pre_cost_trim_cost_dtls['amount']);

							$cu_woq = array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
							$cu_amount = array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);

							$costing_per_arr = array_unique($wo_pre_cost_trim_cost_dtls['costing_per']);
							$uom_name = '';
							$uom_id = 0;
							if ($entry_from == 520) {
								$uom_name = $unit_of_measurement[$order_uom];
								$uom_id = $order_uom;
							} else {								
								foreach ($costing_per_arr as $key => $costing_per) {
									if ($costing_per == 2) {
										$uom_name = 'Pcs';
										$uom_id = 1;
									} else {
										$uom_name = 'Dzn';
										$uom_id = 2;
									}
								}
							}
							if($cbo_currency==1) //Tk
							{
								$rate_cons_uom =$rate_cons_uom*$exchange_rate;
								$amount=$bal_woq*$rate_cons_uom;
								$req_amount_cons_uom=$bal_woq*$rate_cons_uom;
							}


							//$reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];
				?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="80">
									<? echo $job_no ?>
									<input type="hidden" id="txtjob_<? echo $i; ?>" value="<? echo $job_no; ?>" style="width:30px" class="text_boxes" readonly />
								</td>
								<td width="100" style="word-wrap:break-word;word-break: break-all">
									<? echo $po_number; ?>
									<input type="hidden" id="txtbookingid_<? echo $i; ?>" value="" readonly />
									<input type="hidden" id="txtpoid_<? echo $i; ?>" value="<? echo $po_id; ?>" readonly />
									<input type="hidden" id="txtcountry_<? echo $i; ?>" value="<? echo $country; ?>" readonly />
								</td>
								<td width="100">
									<? echo $garments_item[$item_number_id]; ?>
									<input type="hidden" id="txtgmtitemid_<? echo $i; ?>" value="<? echo $item_number_id; ?>" readonly />
								</td>
								<td width="100">
									<? echo $emb_name_name; ?>
									<input type="hidden" id="txtembcostid_<? echo $i; ?>" value="<? echo $pre_cost_emb_cost_dtls_id; ?>" readonly />
									<input type="hidden" id="emb_name_<? echo $i; ?>" value="<? echo $emb_name; ?>" readonly />
								</td>
								<td width="150">
									<? echo $body_part; ?>
									<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i; ?>" value="<? echo $body_part_id; ?>" />
								</td>
								<td width="150">
									<? echo $emb_type_name; ?>
									<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i; ?>" value="<? echo $emb_type; ?>" />
								</td>
								<td width="70" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i; ?>" value="<? echo number_format($req_qnty_cons_uom, 4, '.', ''); ?>" readonly />
									<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i; ?>" value="<? echo number_format($req_amount_cons_uom, 4, '.', ''); ?>" readonly />
								</td>
								<td width="50"><? echo $uom_name; ?>
									<input type="hidden" id="txtuom_<? echo $i; ?>" value="<? echo $uom_id; ?>" readonly />
								</td>
								<td width="80" align="right">

									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i; ?>" value="<? echo number_format($cu_woq, 4, '.', ''); ?>" readonly />
									<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i; ?>" value="<? echo number_format($cu_amount, 4, '.', ''); ?>" readonly />
								</td>
								<td width="80" align="right">
									<?
									?>
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" readonly />
								</td>
								<td width="100" align="right">
									<? echo create_drop_down("cbocolorsizesensitive_" . $i, 100, $size_color_sensitive, "", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)", "", "1,2,3,4"); ?>
								</td>
								<td width="80" align="right">

									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" onClick="open_consumption_popup('requires/multi_job_additional_print_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i; ?>',<? echo $i; ?>)" readonly />
								</td>
								<td width="55" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i; ?>" value="<? echo $exchange_rate; ?>" readonly />

								</td>
								<td width="80" align="right">
									<?
									$ratetexcolor = "#000000";
									$decimal = explode(".", $rate_cons_uom);
									if (strlen($decimal[1]) > 6) {
										$ratetexcolor = "#F00";
									}
									//echo strlen($decimal[1]);
									?>
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; color:<? echo $ratetexcolor;  ?>;  background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

									<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" readonly />

								</td>

								<td width="80" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i; ?>" value="<? echo number_format($amount, 4, '.', ''); ?>" readonly />
								</td>
								<td width="" align="right">
									<input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i; ?>" class="datepicker" value="<? echo $txt_delivery_date; ?>" readonly />
									<input type="hidden" id="consbreckdown_<? echo $i; ?>" value="" />
									<input type="hidden" id="jsondata_<? echo $i; ?>" value="" />
								</td>
							</tr>
				<?
							$i++;
						}
					}
				}
				?>
			<?
			}
			?>
		</tbody>
	</table>
	<table width="1500" class="rpt_table" border="0" rules="all">
		<tfoot>
			<tr>
				<th width="40">&nbsp;</th>
				<th width="80"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
				<th width="150"></th>
				<th width="150"></th>
				<th width="70"><? echo $tot_req_qty; ?></th>
				<th width="50"></th>
				<th width="80"><? echo $tot_cu_woq; ?></th>
				<th width="80"><? echo $tot_bal_woq; ?></th>
				<th width="100"></th>
				<th width="80"></th>
				<th width="55"></th>
				<th width="80"></th>
				<th width="80"><input type="hidden" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px" readonly /></th>
				<th width=""><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly /></th>
			</tr>
		</tfoot>
	</table>
	<table width="1100" colspan="14" cellspacing="0" class="" border="0">
		<tr>
			<td align="center" class="button_container">
				<?
				echo load_submit_buttons($permission, "fnc_trims_booking_dtls", 0, 0, "reset_form('','booking_list_view','','','')", 2);
				?>
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "consumption_popup") {
	echo load_html_head_contents("Consumption Entry", "../../../", 1, 1, $unicode, '', '');
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library = return_library_array("select id, size_name from lib_size", "id", "size_name");
?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete("select size_name from  lib_size", "size_name"), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete("select color_name from lib_color", "color_name"), 0, -1); ?>];

		function poportionate_qty_old(qty) {
			var po_qty = document.getElementById('po_qty').value;
			var txtwoq_qty = document.getElementById('txtwoq_qty').value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for (var i = 1; i <= rowCount; i++) {
				var pcs = $('#pcsset_' + i).val();
				var txtwoq_cal = number_format_common((txtwoq_qty / po_qty) * (pcs), 5, 0);
				$('#qty_' + i).val(txtwoq_cal);
				calculate_requirement(i)
			}
			set_sum_value('qty_sum', 'qty_')
		}

		function poportionate_qty(qty) {
			var txtwoq = document.getElementById('txtwoq').value;
			var txtwoq_qty = document.getElementById('txtwoq_qty').value * 1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for (var i = 1; i <= rowCount; i++) {
				var poreqqty = $('#poreqqty_' + i).val();
				var txtwoq_cal = number_format_common((txtwoq_qty / txtwoq) * (poreqqty), 5, 0);
				//alert(txtwoq_cal);
				$('#qty_' + i).val(txtwoq_cal);
				calculate_requirement(i)
			}
			set_sum_value('qty_sum', 'qty_')
			var j = i - 1;
			var qty_sum = document.getElementById('qty_sum').value * 1;
			if (qty_sum > txtwoq_qty) {
				$('#qty_' + j).val(number_format_common(txtwoq_cal * 1 - (qty_sum - txtwoq_qty), 5, 0))
			} else if (qty_sum < txtwoq_qty) {
				$('#qty_' + j).val(number_format_common((txtwoq_cal * 1) + (txtwoq_qty - qty_sum), 5, 0))
			} else {
				$('#qty_' + j).val(number_format_common(txtwoq_cal, 5, 0));
			}
			set_sum_value('qty_sum', 'qty_');
			calculate_requirement(j)
		}

		function calculate_requirement(i) {
			var process_loss_method_id = document.getElementById('process_loss_method_id').value;
			var cons = (document.getElementById('qty_' + i).value) * 1;
			var processloss = (document.getElementById('excess_' + i).value) * 1;
			var WastageQty = '';
			if (process_loss_method_id == 1) {
				WastageQty = cons + cons * (processloss / 100);
			} else if (process_loss_method_id == 2) {
				var devided_val = 1 - (processloss / 100);
				var WastageQty = parseFloat(cons / devided_val);
			} else {
				WastageQty = 0;
			}
			WastageQty = number_format_common(WastageQty, 5, 0);
			document.getElementById('woqny_' + i).value = WastageQty;
			set_sum_value('woqty_sum', 'woqny_')
			calculate_amount(i);
		}

		function set_sum_value(des_fil_id, field_id) {
			if (des_fil_id == 'qty_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'excess_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'woqty_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'amount_sum') var ddd = {
				dec_type: 5,
				comma: 0,
				currency: 0
			};
			if (des_fil_id == 'pcs_sum') var ddd = {
				dec_type: 6,
				comma: 0
			};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			math_operation(des_fil_id, field_id, '+', rowCount, ddd);
		}

		function copy_value(value, field_id, i) {
			var gmtssizesid = document.getElementById('gmtssizesid_' + i).value;
			var pocolorid = document.getElementById('pocolorid_' + i).value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis = $('input[name="copy_basis"]:checked').val()

			for (var j = i; j <= rowCount; j++) {
				if (field_id == 'des_') {
					if (copy_basis == 0) document.getElementById(field_id + j).value = value;
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
				}
				if (field_id == 'itemcolor_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
					}
					if (copy_basis == 1) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
				}

				if (field_id == 'itemsizes_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
						}
					}
				}
				if (field_id == 'qty_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
						calculate_requirement(j)
						set_sum_value('qty_sum', 'qty_');
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
							set_sum_value('qty_sum', 'qty_');
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
							set_sum_value('qty_sum', 'qty_');
						}
					}
				}
				if (field_id == 'excess_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
						calculate_requirement(j)
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_requirement(j)
						}
					}
				}
				if (field_id == 'rate_') {
					if (copy_basis == 0) {
						document.getElementById(field_id + j).value = value;
						calculate_amount(j)
					}
					if (copy_basis == 1) {
						if (gmtssizesid == document.getElementById('gmtssizesid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_amount(j)
						}
					}
					if (copy_basis == 2) {
						if (pocolorid == document.getElementById('pocolorid_' + j).value) {
							document.getElementById(field_id + j).value = value;
							calculate_amount(j)
						}
					}
				}
			}
		}

		function calculate_amount(i) {
			var rate = (document.getElementById('rate_' + i).value) * 1;
			var woqny = (document.getElementById('woqny_' + i).value) * 1;
			var amount = number_format_common((rate * woqny), 5, 0);
			document.getElementById('amount_' + i).value = amount;
			set_sum_value('amount_sum', 'amount_');
			calculate_avg_rate()
		}

		function calculate_avg_rate() {
			var woqty_sum = document.getElementById('woqty_sum').value;
			var amount_sum = document.getElementById('amount_sum').value;
			var avg_rate = number_format_common((amount_sum / woqty_sum), 5, 0);
			document.getElementById('rate_sum').value = avg_rate;
		}

		function js_set_value() {
			var reg = /[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num = $('#tbl_consmption_cost tbody tr').length;
			var cons_breck_down = "";
			for (var i = 1; i <= row_num; i++) {
				var txtdescription = $('#des_' + i).val();
				//alert(txtdescription.match(reg))
				if (txtdescription.match(reg)) {
					alert("Your Description Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					//release_freezing();
					$('#des_' + i).css('background-color', 'red');
					return;
				}
				var pocolorid = $('#pocolorid_' + i).val()
				if (pocolorid == '') pocolorid = 0;

				var gmtssizesid = $('#gmtssizesid_' + i).val()
				if (gmtssizesid == '') gmtssizesid = 0;

				var des = trim($('#des_' + i).val())
				if (des == '') des = 0;

				var itemcolor = $('#itemcolor_' + i).val()
				if (itemcolor == '') itemcolor = 0;

				var itemsizes = $('#itemsizes_' + i).val()
				if (itemsizes == '') itemsizes = 0;

				var qty = $('#qty_' + i).val()
				if (qty == '') qty = 0;

				var excess = $('#excess_' + i).val()
				if (excess == '') excess = 0;

				var woqny = $('#woqny_' + i).val()
				if (woqny == '') woqny = 0;

				var rate = $('#rate_' + i).val()
				if (rate == '') rate = 0;

				var amount = $('#amount_' + i).val()
				if (amount == '') amount = 0;

				var pcs = $('#pcs_' + i).val()
				if (pcs == '') pcs = 0;

				var colorsizetableid = $('#colorsizetableid_' + i).val()
				if (colorsizetableid == '') colorsizetableid = 0;

				var updateid = $('#updateid_' + i).val()
				if (updateid == '') updateid = 0;

				var reqqty = $('#reqqty_' + i).val()
				if (reqqty == '') reqqty = 0;

				var poarticle = $('#poarticle_' + i).val()
				if (poarticle == '') poarticle = 'no article';

				if (cons_breck_down == "") {
					cons_breck_down += pocolorid + '_' + gmtssizesid + '_' + des + '_' + itemcolor + '_' + itemsizes + '_' + qty + '_' + excess + '_' + woqny + '_' + rate + '_' + amount + '_' + pcs + '_' + colorsizetableid + '_' + reqqty + '_' + poarticle;
				} else {
					cons_breck_down += "__" + pocolorid + '_' + gmtssizesid + '_' + des + '_' + itemcolor + '_' + itemsizes + '_' + qty + '_' + excess + '_' + woqny + '_' + rate + '_' + amount + '_' + pcs + '_' + colorsizetableid + '_' + reqqty + '_' + poarticle;
				}
			}
			document.getElementById('cons_breck_down').value = cons_breck_down;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<?
		extract($_REQUEST);
		if ($txt_job_no == "") {
			$txt_job_no_cond = "";
			$txt_job_no_cond1 = "";
		} else {
			$txt_job_no_cond = "and a.job_no='$txt_job_no'";
			$txt_job_no_cond1 = "and job_no='$txt_job_no'";
		}
		if ($txt_country == "") {
			$txt_country_cond = "";
		} else {
			$txt_country_cond = "and c.country_id in ($txt_country)";
		}
		$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
		$tot_po_qty = 0;
		$sql_po_qty = sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) and c.item_number_id=$txtgmtitemid  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
		foreach ($sql_po_qty as $sql_po_qty_row) {
			$po_qty_arr[$sql_po_qty_row[csf('id')]] = $sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty += $sql_po_qty_row[csf('order_quantity_set')];
		}
		?>
		<div align="center" style="width:1150px;">
			<fieldset>
				<form id="consumptionform_1" autocomplete="off">
					<table width="1150" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
						<thead>
							<tr>
								<th colspan="14" id="td_sync_msg" style="color:#FF0000"></th>
							</tr>
							<tr>
								<th colspan="14">
									<input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
									<input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity; ?>" />
									Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtwoq; ?>" />
									<input type="radio" name="copy_basis" value="0" <? if (!$txt_update_dtls_id) {
																						echo "checked";
																					} ?>>Copy to All
									<input type="radio" name="copy_basis" value="1">Gmts Size Wise
									<input type="radio" name="copy_basis" value="2">Gmts Color Wise
									<input type="radio" name="copy_basis" value="10" <? if ($txt_update_dtls_id) {
																							echo "checked";
																						} ?>>No Copy
									<input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>" />
									<input type="hidden" id="po_qty" name="po_qty" value="<? echo $tot_po_qty; ?>" />
								</th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="100">Article No</th>
								<th width="100">Gmts. Color</th>
								<th width="70">Gmts. sizes</th>
								<th width="100">Description</th>
								<th width="100">Item Color</th>
								<th width="80">Item Sizes</th>
								<th width="70"> Wo Qty</th>
								<th width="40">Excess %</th>
								<th width="70">WO Qty.</th>
								<th width="120">Rate</th>
								<th width="100">Amount</th>
								<th width="">RMG Qnty</th>

							</tr>
						</thead>
						<tbody>
							<?

							$booking_data_arr = array();
							if ($txt_update_dtls_id == "") {
								$txt_update_dtls_id = 0;
							}
							$booking_data = sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_emb_book_con_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
							foreach ($booking_data as $row) {
								$booking_data_arr[$row[csf('color_size_table_id')]][id] = $row[csf('id')];
								$booking_data_arr[$row[csf('color_size_table_id')]][description] = $row[csf('description')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_color] = $row[csf('item_color')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_size] = $row[csf('item_size')];

								$booking_data_arr[$row[csf('color_size_table_id')]][cons] += $row[csf('cons')];
								$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent] = $row[csf('process_loss_percent')];
								$booking_data_arr[$row[csf('color_size_table_id')]][requirment] += $row[csf('requirment')];
								$booking_data_arr[$row[csf('color_size_table_id')]][rate] = $row[csf('rate')];
								$booking_data_arr[$row[csf('color_size_table_id')]][amount] += $row[csf('amount')];
							}



							$condition = new condition();
							if (str_replace("'", "", $txt_po_id) != '') {
								$condition->po_id("in($txt_po_id)");
							}

							$condition->init();
							$emblishment = new emblishment($condition);
							$wash = new wash($condition);

							$gmt_color_edb = "";
							$item_color_edb = "";
							$gmt_size_edb = "";
							$item_size_edb = "";
							if ($cbo_colorsizesensitive == 1) {
								$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
								$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

								$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
								$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

								$sql = "select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
								$gmt_size_edb = 1;
								$item_size_edb = 1;
							} else if ($cbo_colorsizesensitive == 2) {
								$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
								$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

								$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
								$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

								$sql = "select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number,c.item_number_id order by b.id,size_order";
								$gmt_color_edb = 1;
								$item_color_edb = 1;
							} else if ($cbo_colorsizesensitive == 3) {
								$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
								$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

								$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
								$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();


								$sql = "select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
								$gmt_size_edb = 1;
								$item_size_edb = 1;
							} else if ($cbo_colorsizesensitive == 4) {
								$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
								$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

								$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
								$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

								$sql = "select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id  order by b.id, color_order,size_order";
							} else {
								$req_qty_arr = $emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
								$req_amount_arr = $emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();

								$req_qty_arr_wash = $wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();

								$req_amount_arr_wash = $wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();

								$sql = "select b.id, b.po_number,b.po_quantity,c.item_number_id,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,c.item_number_id,a.total_set_qnty order by b.id";
							}

							$po_color_level_data_arr = array();
							$po_size_level_data_arr = array();

							$po_no_sen_level_data_arr = array();
							$po_color_size_level_data_arr = array();
							$data_array = sql_select($sql);
							if (count($data_array) > 0) {
								$i = 0;
								foreach ($data_array as $row) {
									$data = explode('_', $data_array_cons[$i]);
									$i++;
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if ($item_color == 0 || $item_color == "") $item_color = $row[csf('color_number_id')];

									$item_size = $booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if ($item_size == 0 || $item_size == "") $item_size = $row[csf('item_size')];

									$rate = $booking_data_arr[$row[csf('color_size_table_id')]][rate];
									if ($rate == 0 || $rate == "") $rate = $txt_avg_price;

									$description = $booking_data_arr[$row[csf('color_size_table_id')]][description];
									if ($description == "") $description = trim($txt_pre_des);

									$brand_supplier = $booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
									if ($brand_supplier == "") $brand_supplier = trim($txt_pre_brand_sup);

									if ($cbo_colorsizesensitive == 1 || $cbo_colorsizesensitive == 3) {
										if ($emb_name == 3) {
											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");

										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]] = $po_qty;
										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]] = $row[csf('id')];
										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];
										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];

										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][cons];
										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][requirment];
										$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_amt'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][amount];
									} else if ($cbo_colorsizesensitive == 2) {
										if ($emb_name == 3) {
											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]] = $po_qty;
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]] = $row[csf('id')];
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];

										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]] = $row[csf('article_number')];

										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][cons];
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][requirment];
										$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][amount];
									} else if ($cbo_colorsizesensitive == 4) {
										if ($emb_name == 3) {
											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");

										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]] = $po_qty;
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]] = $row[csf('id')];
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]] = $row[csf('article_number')];

										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][cons];
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][requirment];
										$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][amount];
									} else if ($cbo_colorsizesensitive == 0) {

										if ($emb_name == 3) {

											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
										$po_no_sen_level_data_arr[$txtembcostid]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
										$po_no_sen_level_data_arr[$txtembcostid]['po_qty'][$row[csf('id')]] = $po_qty;
										$po_no_sen_level_data_arr[$txtembcostid]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
										$po_no_sen_level_data_arr[$txtembcostid]['po_id'][$row[csf('id')]] = $row[csf('id')];
										$po_no_sen_level_data_arr[$txtembcostid]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];
										$po_no_sen_level_data_arr[$txtembcostid]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];

										$po_no_sen_level_data_arr[$txtembcostid]['booking_cons'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][cons];
										$po_no_sen_level_data_arr[$txtembcostid]['booking_qty'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][requirment];
										$po_no_sen_level_data_arr[$txtembcostid]['booking_amt'][$row[csf('id')]] = $booking_data_arr[$row[csf('color_size_table_id')]][amount];
									}
								}
							}

							//print_r($po_no_sen_level_data_arr);


							$piNumber = 0;
							$pi_number = return_field_value("pi_number", "com_pi_master_details a,com_pi_item_details b", " a.id=b.pi_id  and b.work_order_no='$txt_booking_no' and b.item_group='" . $txt_gmt_item_id . "' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
							if ($pi_number) {
								$piNumber = 1;
							}
							$recvNumber = 0;
							$recv_number = return_field_value("recv_number", "inv_receive_master a,inv_trims_entry_dtls b", " a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and b.item_group_id='" . $txt_gmt_item_id . "' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
							if ($recv_number) {
								$recvNumber = 1;
							}
							//echo $piNumber."PI8888888";

							if (count($data_array) > 0 && $cbo_level == 1) {
								$i = 0;
								foreach ($data_array as $row) {
									$data = explode('_', $data_array_cons[$i]);

									if ($cbo_colorsizesensitive == 1 || $cbo_colorsizesensitive == 3) {
										if ($emb_name == 3) {
											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
									} else if ($cbo_colorsizesensitive == 2) {
										if ($emb_name == 3) {
											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
									} else if ($cbo_colorsizesensitive == 4) {
										if ($emb_name == 3) {
											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
									} else if ($cbo_colorsizesensitive == 0) {
										if ($emb_name == 3) {
											$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
										} else {
											$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
										}
										$txtwoq_cal = def_number_format($txt_req_quantity, 5, "");
									}


									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if ($item_color == 0 || $item_color == "") $item_color = $row[csf('color_number_id')];

									$item_size = $booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if ($item_size == 0 || $item_size == "") $item_size = $size_library[$row[csf('size_number_id')]];

									$rate = $booking_data_arr[$row[csf('color_size_table_id')]][rate];
									if ($rate == 0 || $rate == "") $rate = $txt_avg_price;

									$description = $booking_data_arr[$row[csf('color_size_table_id')]][description];
									if ($description == "") $description = trim($txt_pre_des);

									$brand_supplier = $booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
									if ($brand_supplier == "") $brand_supplier = trim($txt_pre_brand_sup);

									if ($txtwoq_cal > 0) {
										$i++;
							?>
										<tr id="break_1" align="center">
											<td><? echo $i; ?></td>
											<td><input type="text" id="poarticle_<? echo $i; ?>" name="poarticle_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('article_number')]; ?>" readonly /></td>
											<td>
												<input type="text" id="pocolor_<? echo $i; ?>" name="pocolor_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if ($gmt_color_edb || $piNumber || $recvNumber) {
																																																										echo  "disabled";
																																																									} else {
																																																										echo "";
																																																									} ?> readonly />
												<input type="hidden" id="pocolorid_<? echo $i; ?>" name="pocolorid_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
												<input type="hidden" id="poid_<? echo $i; ?>" name="poid_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('id')]; ?>" />
												<input type="hidden" id="poqty_<? echo $i; ?>" name="poqty_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $po_qty_arr[$row[csf('id')]]; ?>" readonly />
												<input type="hidden" id="poreqqty_<? echo $i; ?>" name="poreqqty_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $txtwoq_cal; ?>" readonly />
											</td>
											<td>
												<input type="text" id="gmtssizes_<? echo $i; ?>" name="gmtssizes_<? echo $i; ?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if ($gmt_size_edb || $piNumber || $recvNumber) {
																																																										echo  "disabled";
																																																									} else {
																																																										echo "";
																																																									} ?> readonly />
												<input type="hidden" id="gmtssizesid_<? echo $i; ?>" name="gmtssizesid_<? echo $i; ?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
											</td>
											<td><input type="text" id="des_<? echo $i; ?>" name="des_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $description; ?>" onChange="copy_value(this.value,'des_',<? echo $i; ?>)" <? if ($piNumber || $recvNumber) {
																																																														echo  "disabled";
																																																													} else {
																																																														echo "";
																																																													} ?> />
											</td>

											<td><input type="text" id="itemcolor_<? echo $i; ?>" value="<? echo $color_library[$item_color]; ?>" name="itemcolor_<? echo $i; ?>" class="text_boxes" style="width:100px" onChange="copy_value(this.value,'itemcolor_',<? echo $i; ?>)" <? if ($item_color_edb || $piNumber || $recvNumber) {
																																																																						echo  "disabled";
																																																																					} else {
																																																																						echo "";
																																																																					} ?> />
											</td>
											<td><input type="text" id="itemsizes_<? echo $i; ?>" name="itemsizes_<? echo $i; ?>" class="text_boxes" style="width:80px" onChange="copy_value(this.value,'itemsizes_',<? echo $i; ?>)" value="<? echo $item_size; ?>" <? if ($item_size_edb || $piNumber || $recvNumber) {
																																																																		echo  "disabled";
																																																																	} else {
																																																																		echo "";
																																																																	} ?> />
											</td>
											<td><input type="hidden" id="reqqty_<? echo $i; ?>" name="reqqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $txtwoq_cal ?>" readonly />
												<input type="text" id="qty_<? echo $i; ?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i; ?>);copy_value(this.value,'qty_',<? echo $i; ?>)" name="qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>" />
											</td>
											<td>
												<input type="text" id="excess_<? echo $i; ?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) " name="excess_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i; ?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i; ?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled />
											</td>
											<td>
												<input type="text" id="woqny_<? echo $i; ?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' )" onChange="set_sum_value( 'woqty_sum', 'woqny_' )" name="woqny_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly />
											</td>
											<td>
												<input type="text" id="rate_<? echo $i; ?>" name="rate_<? echo $i; ?>" class="text_boxes_numeric" style="width:120px" onChange="calculate_amount(<? echo $i; ?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i; ?>) " value="<? echo $rate; ?>" <? if ($piNumber || $recvNumber) {
																																																																																	echo  "disabled";
																																																																																} else {
																																																																																	echo "";
																																																																																} ?> />
											</td>
											<td>
												<input type="text" id="amount_<? echo $i; ?>" name="amount_<? echo $i; ?>" onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:100px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
											</td>
											<td>
												<input type="text" id="pcs_<? echo $i; ?>" name="pcs_<? echo $i; ?>" onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf('order_quantity')]; ?>" readonly>
												<input type="hidden" id="pcsset_<? echo $i; ?>" name="pcsset_<? echo $i; ?>" onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
												<input type="hidden" id="colorsizetableid_<? echo $i; ?>" name="colorsizetableid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
												<input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
											</td>
										</tr>
									<?
									}
								}
							}

							$level_arr = array();
							$gmt_color_edb = "";
							$item_color_edb = "";
							$gmt_size_edb = "";
							$item_size_edb = "";
							if ($cbo_colorsizesensitive == 1) {
								$sql = "select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";

								$level_arr = $po_color_level_data_arr;
								$gmt_size_edb = 1;
								$item_size_edb = 1;
							} else if ($cbo_colorsizesensitive == 2) {
								$sql = "select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.size_number_id,c.article_number,c.item_number_id order by size_order";
								$level_arr = $po_size_level_data_arr;
								$gmt_color_edb = 1;
								$item_color_edb = 1;
							} else if ($cbo_colorsizesensitive == 3) {
								$sql = "select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)    $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";
								$level_arr = $po_color_level_data_arr;
								$gmt_size_edb = 1;
								$item_size_edb = 1;
							} else if ($cbo_colorsizesensitive == 4) {
								$sql = "select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.size_number_id, c.article_number,c.item_number_id order by  color_order,size_order,c.article_number";
								$level_arr = $po_color_size_level_data_arr;
							} else {
								$sql = "select b.job_no_mst,c.item_number_id,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.job_no_mst,c.item_number_id";
								$level_arr = $po_no_sen_level_data_arr;
							}
							$data_array = sql_select($sql);
							if (count($data_array) > 0 && $cbo_level == 2) {
								$i = 0;
								foreach ($data_array as $row) {

									if ($cbo_colorsizesensitive == 1) {
										$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']), 5, "");
										$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
										$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
										$booking_cons = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_cons']), 5, "");
										$booking_qty = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty']), 5, "");
										$booking_amt = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_amt']), 5, "");
									}
									if ($cbo_colorsizesensitive == 2) {
										$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']), 5, "");
										$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
										$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
										$booking_cons = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']), 5, "");
										$booking_qty = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']), 5, "");
										$booking_amt = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']), 5, "");
									}
									if ($cbo_colorsizesensitive == 3) {
										$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']), 5, "");
										$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
										$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
										$booking_cons = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_cons']), 5, "");
										$booking_qty = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty']), 5, "");
										$booking_amt = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_amt']), 5, "");
									}
									if ($cbo_colorsizesensitive == 4) {
										$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']), 5, "");
										$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
										$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
										$booking_cons = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']), 5, "");
										$booking_qty = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']), 5, "");
										$booking_amt = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']), 5, "");
									}
									if ($cbo_colorsizesensitive == 0) {
										$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid]['req_qty']), 5, "");
										$po_qty = array_sum($level_arr[$txtembcostid]['po_qty']);
										$order_quantity_set = array_sum($level_arr[$txtembcostid]['order_quantity_set']);
										$booking_cons = def_number_format(array_sum($level_arr[$txtembcostid]['booking_cons']), 5, "");
										$booking_qty = def_number_format(array_sum($level_arr[$txtembcostid]['booking_qty']), 5, "");
										$booking_amt = def_number_format(array_sum($level_arr[$txtembcostid]['booking_amt']), 5, "");
									}

									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if ($item_color == 0 || $item_color == "") $item_color = $row[csf('color_number_id')];

									$item_size = $booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if ($item_size == 0 || $item_size == "") $item_size = $size_library[$row[csf('size_number_id')]];

									//$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
									if ($booking_amt > 0) {
										$rate = $booking_amt / $booking_qty;
									} else {
										$rate = $txt_avg_price;
									}


									$description = $booking_data_arr[$row[csf('color_size_table_id')]][description];
									if ($description == "") $description = trim($txt_pre_des);
									//echo $description.'='.$txt_pre_des.'<br/>';
									$brand_supplier = $booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
									if ($brand_supplier == "") $brand_supplier = trim($txt_pre_brand_sup);
									//echo $txtwoq_cal.'===<br/>';
									if ($txtwoq_cal > 0) {
										$i++;
									?>
										<tr id="break_1" align="center">
											<td><? echo $i; ?></td>
											<td><input type="text" id="poarticle_<? echo $i; ?>" name="poarticle_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('article_number')]; ?>" readonly />
											</td>
											<td>
												<input type="text" id="pocolor_<? echo $i; ?>" name="pocolor_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if ($gmt_color_edb || $piNumber || $recvNumber) {
																																																										echo  "disabled";
																																																									} else {
																																																										echo "";
																																																									} ?> readonly />
												<input type="hidden" id="pocolorid_<? echo $i; ?>" name="pocolorid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
												<input type="hidden" id="poid_<? echo $i; ?>" name="poid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>" readonly />
												<input type="hidden" id="poqty_<? echo $i; ?>" name="poqty_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $po_qty; ?>" readonly />
												<input type="hidden" id="poreqqty_<? echo $i; ?>" name="poreqqty_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $txtwoq_cal; ?>" readonly />
											</td>
											<td>
												<input type="text" id="gmtssizes_<? echo $i; ?>" name="gmtssizes_<? echo $i; ?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if ($gmt_size_edb || $piNumber || $recvNumber) {
																																																										echo  "disabled";
																																																									} else {
																																																										echo "";
																																																									} ?> readonly />
												<input type="hidden" id="gmtssizesid_<? echo $i; ?>" name="gmtssizesid_<? echo $i; ?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
											</td>
											<td><input type="text" id="des_<? echo $i; ?>" name="des_<? echo $i; ?>" class="text_boxes" style="width:100px" value="<? echo $description; ?>" onChange="copy_value(this.value,'des_',<? echo $i; ?>)" <? if ($piNumber || $recvNumber) {
																																																														echo  "disabled";
																																																													} else {
																																																														echo "";
																																																													} ?> />
											</td>

											<td><input type="text" id="itemcolor_<? echo $i; ?>" value="<? echo $color_library[$item_color]; ?>" name="itemcolor_<? echo $i; ?>" class="text_boxes" style="width:100px" onChange="copy_value(this.value,'itemcolor_',<? echo $i; ?>)" <? if ($item_color_edb || $piNumber || $recvNumber) {
																																																																						echo  "disabled";
																																																																					} else {
																																																																						echo "";
																																																																					} ?> />
											</td>
											<td><input type="text" id="itemsizes_<? echo $i; ?>" name="itemsizes_<? echo $i; ?>" class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i; ?>)" value="<? echo $item_size; ?>" <? if ($item_size_edb || $piNumber || $recvNumber) {
																																																																		echo  "disabled";
																																																																	} else {
																																																																		echo "";
																																																																	} ?> />
											</td>
											<td><input type="hidden" id="reqqty_<? echo $i; ?>" name="reqqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $txtwoq_cal ?>" readonly />

												<input type="text" id="qty_<? echo $i; ?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i; ?>);copy_value(this.value,'qty_',<? echo $i; ?>)" name="qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" placeholder="<? echo $txtwoq_cal; ?>" value="<? if ($booking_cons > 0) {
																																																																																																								echo $booking_cons;
																																																																																																							} ?>" />
											</td>
											<td>
												<input type="text" id="excess_<? echo $i; ?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) " name="excess_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i; ?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i; ?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled />
											</td>
											<td><input type="text" id="woqny_<? echo $i; ?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )" name="woqny_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? if ($booking_qty) {
																																																																					echo $booking_qty;
																																																																				} ?>" readonly />
											</td>
											<td><input type="text" id="rate_<? echo $i; ?>" name="rate_<? echo $i; ?>" class="text_boxes_numeric" style="width:120px" onChange="calculate_amount(<? echo $i; ?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i; ?>) " value="<? echo $rate; ?>" <? if ($piNumber || $recvNumber) {
																																																																																	echo  "disabled";
																																																																																} else {
																																																																																	echo "";
																																																																																} ?> />
											</td>
											<td><input type="text" id="amount_<? echo $i; ?>" name="amount_<? echo $i; ?>" onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:100px" value="<? echo $booking_amt; //$booking_data_arr[$row[csf('color_size_table_id')]][amount]; 
																																																									?>" readonly>
											</td>

											<td><input type="text" id="pcs_<? echo $i; ?>" name="pcs_<? echo $i; ?>" onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf('order_quantity')]; ?>" readonly>
												<input type="hidden" id="pcsset_<? echo $i; ?>" name="pcsset_<? echo $i; ?>" onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px" value="<? echo $order_quantity_set; ?>" readonly>
												<input type="hidden" id="colorsizetableid_<? echo $i; ?>" name="colorsizetableid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
												<input type="hidden" id="updateid_<? echo $i; ?>" name="updateid_<? echo $i; ?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
											</td>
										</tr>
							<?
									}
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th width="40">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
								<th width="40"><input type="text" id="excess_sum" name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
								<th width="70"><input type="text" id="woqty_sum" name="woqty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
								<th width="40"><input type="text" id="rate_sum" name="rate_sum" class="text_boxes_numeric" style="width:120px" readonly></th>
								<th width="50"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:100px" readonly></th>
								<th><input type="hidden" id="json_data" name="json_data" class="text_boxes_numeric" style="width:50px" value='<? echo json_encode($level_arr); ?>' readonly>
									<input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
								</th>
							</tr>
						</tfoot>
					</table>
					<table width="1150" cellspacing="0" class="" border="0" rules="all">
						<tr>
							<td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()" /> </td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script>
		$("input[type=text]").focus(function() {
			$(this).select();
		});
		<?
		if ($txt_update_dtls_id == "") {
		?>
			poportionate_qty(<? echo $txtwoq; ?>);
		<?
		}
		?>
		set_sum_value('qty_sum', 'qty_');
		set_sum_value('woqty_sum', 'woqny_');
		set_sum_value('amount_sum', 'amount_');
		set_sum_value('pcs_sum', 'pcs_');
		calculate_avg_rate();
		var wo_qty = $('#txtwoq_qty').val() * 1;

		var wo_qty_sum = $('#qty_sum').val() * 1;

		if (wo_qty != wo_qty_sum) {
			$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
		}
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "set_cons_break_down") {
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$data = explode("_", $data);
	$garments_nature = $data[0];
	$cbo_company_name = $data[1];
	$txt_job_no = $data[2];
	$txt_po_id = $data[3];
	$txtembcostid = $data[4];
	$txtgmtitemid = $data[5];
	$txt_update_dtls_id = trim($data[6]);
	$cbo_colorsizesensitive = $data[7];
	$txt_req_quantity = $data[8];
	$txt_avg_price = $data[9];
	$txt_country = $data[10];
	$emb_name = $data[11];
	$emb_type = $data[12];
	$cbo_level = $data[13];

	if ($txt_job_no == "") {
		$txt_job_no_cond = "";
		$txt_job_no_cond1 = "";
	} else {
		$txt_job_no_cond = "and a.job_no='$txt_job_no'";
		$txt_job_no_cond1 = "and job_no='$txt_job_no'";
	}

	if ($txt_country == "") $txt_country_cond = "";
	else $txt_country_cond = "and c.country_id in ($txt_country)";

	$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	$sql_po_qty = sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) and c.item_number_id=$txtgmtitemid   $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty = 0;
	foreach ($sql_po_qty as $sql_po_qty_row) {
		$po_qty_arr[$sql_po_qty_row[csf('id')]] = $sql_po_qty_row[csf('order_quantity_set')];
		$tot_po_qty += $sql_po_qty_row[csf('order_quantity_set')];
	}


	$booking_data_arr = array();
	if ($txt_update_dtls_id == "" || $txt_update_dtls_id == 0) $txt_update_dtls_id = 0;
	else $txt_update_dtls_id = $txt_update_dtls_id;
	$booking_data = sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_emb_book_con_dtls where wo_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	foreach ($booking_data as $booking_data_row) {
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id] = $booking_data_row[csf('id')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description] = $booking_data_row[csf('description')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color] = $booking_data_row[csf('item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size] = $booking_data_row[csf('item_size')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons] += $booking_data_row[csf('cons')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent] = $booking_data_row[csf('process_loss_percent')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment] += $booking_data_row[csf('requirment')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate] = $booking_data_row[csf('rate')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount] += $booking_data_row[csf('amount')];
	}
	/*$cu_booking_data_arr=array();
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id,b.id,b.wo_booking_dtls_id,b.po_break_down_id,b.color_number_id,b.gmts_sizes,b.requirment,b.article_number  from wo_booking_dtls a, wo_emb_book_con_dtls b where a.id=b.wo_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in($txt_update_dtls_id)");
	foreach($cu_booking_data as $cu_booking_data_row){
		if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==2 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==4 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==0 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
	}*/

	$condition = new condition();
	if (str_replace("'", "", $txt_po_id) != '') {
		$condition->po_id("in($txt_po_id)");
	}

	$condition->init();
	$emblishment = new emblishment($condition);
	$wash = new wash($condition);

	$gmt_color_edb = "";
	$item_color_edb = "";
	$gmt_size_edb = "";
	$item_size_edb = "";
	if ($cbo_colorsizesensitive == 1) {
		/*$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";*/

		$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

		$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();

		$sql = "select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
		$gmt_size_edb = 1;
		$item_size_edb = 1;
	} else if ($cbo_colorsizesensitive == 2) {
		/*$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number order by b.id,size_order";
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";*/
		$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
		$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

		$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();
		$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtssizeArticleAndGmtsitem();

		$sql = "select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number,c.item_number_id order by b.id,size_order";
		$gmt_color_edb = 1;
		$item_color_edb = 1;
	} else if ($cbo_colorsizesensitive == 3) {
		/*$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";*/

		$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtscolorAndGmtsitem();
		$sql = "select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id order by b.id, color_order";
		$gmt_size_edb = 1;
		$item_size_edb = 1;
	} else if ($cbo_colorsizesensitive == 4) {
		/*$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
		$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();

		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number  order by b.id, color_order,size_order";*/

		$req_qty_arr = $emblishment->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
		$req_amount_arr = $emblishment->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

		$req_qty_arr_wash = $wash->getQtyArray_by_OrderEmblishmentidAndGmtscolorGmtssizeArticleAndGmtsitem();
		$req_amount_arr_wash = $wash->getAmountArray_by_OrderEmblishmentidGmtscolorGmtssizeArticleAndGmtsitem();

		$sql = "select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id  order by b.id, color_order,size_order";
	} else {
		$req_qty_arr = $emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
		$req_amount_arr = $emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();

		$req_qty_arr_wash = $wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
		$req_amount_arr_wash = $wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();

		$sql = "select b.id, b.po_number,b.po_quantity,c.item_number_id,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  b.id, b.po_number,b.po_quantity,c.item_number_id,a.total_set_qnty order by b.id";
	}

	$data_array = sql_select($sql);
	if (count($data_array) > 0) {
		$i = 0;
		foreach ($data_array as $row) {
			$color_number_id = $row[csf('color_number_id')];
			if ($color_number_id == "") $color_number_id = 0;

			$size_number_id = $row[csf('size_number_id')];
			if ($size_number_id == "") $size_number_id = 0;

			$description = $txt_pre_des;
			if ($description == "") $description = 0;



			$item_color = $color_library[$row[csf('color_number_id')]];
			if ($item_color == "") $item_color = 0;

			$item_size = $row[csf('item_size')];
			if ($item_size == "") $item_size = 0;
			$excess = 0;
			$pcs = $row[csf('order_quantity_set')];
			if ($pcs == "") $pcs = 0;

			$colorsizetableid = $row[csf('color_size_table_id')];
			if ($colorsizetableid == "") $colorsizetableid = 0;

			$articleNumber = $row[csf('article_number')];
			if ($articleNumber == "") $articleNumber = 'no article';

			if ($cbo_colorsizesensitive == 1 || $cbo_colorsizesensitive == 3) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}

				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");

				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]] = $po_qty;
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]] = $row[csf('id')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];
				$po_color_level_data_arr[$txtembcostid][$row[csf('color_number_id')]]['amount'][$row[csf('id')]] = $amount;
			} else if ($cbo_colorsizesensitive == 2) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");

				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]] = $po_qty;
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]] = $row[csf('id')];
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];
				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];

				$po_size_level_data_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['amount'][$row[csf('id')]] = $amount;
			} else if ($cbo_colorsizesensitive == 4) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");

				$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
				$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]] = $po_qty;
				$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
				$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]] = $row[csf('id')];
				$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];

				$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];

				$po_color_size_level_data_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['amount'][$row[csf('id')]] = $amount;
			} else if ($cbo_colorsizesensitive == 0) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");

				$po_no_sen_level_data_arr[$txtembcostid]['req_qty'][$row[csf('id')]] = $txtwoq_cal;
				$po_no_sen_level_data_arr[$txtembcostid]['po_qty'][$row[csf('id')]] = $po_qty;
				$po_no_sen_level_data_arr[$txtembcostid]['order_quantity_set'][$row[csf('id')]] = $row[csf('order_quantity_set')];
				$po_no_sen_level_data_arr[$txtembcostid]['po_id'][$row[csf('id')]] = $row[csf('id')];
				$po_no_sen_level_data_arr[$txtembcostid]['order_quantity'][$row[csf('id')]] = $row[csf('order_quantity')];
				$po_no_sen_level_data_arr[$txtembcostid]['color_size_table_id'][$row[csf('id')]] = $row[csf('color_size_table_id')];
				$po_no_sen_level_data_arr[$txtembcostid]['amount'][$row[csf('id')]] = $amount;
			}
		}
	}

	$cons_breck_down = "";
	if (count($data_array) > 0 && $cbo_level == 1) {
		$i = 0;
		foreach ($data_array as $row) {
			$color_number_id = $row[csf('color_number_id')];
			if ($color_number_id == "") $color_number_id = 0;

			$size_number_id = $row[csf('size_number_id')];
			if ($size_number_id == "") $size_number_id = 0;

			$description = $txt_pre_des;
			if ($description == "") $description = 0;

			$brand_supplier = $txt_pre_brand_sup;
			if ($brand_supplier == "") $brand_supplier = 0;

			$item_color = $color_library[$row[csf('color_number_id')]];
			if ($item_color == "") $item_color = 0;

			//$item_size=$row[csf('item_size')];
			$item_size = $size_library[$row[csf('size_number_id')]];
			if ($item_size == "") $item_size = 0;
			$excess = 0;

			$pcs = $row[csf('order_quantity_set')];
			if ($pcs == "") $pcs = 0;

			$colorsizetableid = $row[csf('color_size_table_id')];
			if ($colorsizetableid == "") $colorsizetableid = 0;

			$articleNumber = $row[csf('article_number')];
			if ($articleNumber == "") $articleNumber = 'no article';

			if ($cbo_colorsizesensitive == 1 || $cbo_colorsizesensitive == 3) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			} else if ($cbo_colorsizesensitive == 2) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			} else if ($cbo_colorsizesensitive == 4) {
				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			} else if ($cbo_colorsizesensitive == 0) {

				if ($emb_name == 3) {
					$txt_req_quantity = $req_qty_arr_wash[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				} else {
					$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$txtembcostid][$row[csf('item_number_id')]];
				}
				$req_qnty_ordUom = def_number_format((($data[14] / $data[8]) * $txt_req_quantity), 5, "");
				$txtwoq_cal = def_number_format($req_qnty_ordUom, 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			}
			if ($txtwoq_cal > 0) {
				if ($cons_breck_down == "") {
					$cons_breck_down .= $color_number_id . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount . '_' . $pcs . '_' . $colorsizetableid . "_" . $txtwoq_cal . "_" . $articleNumber;
				} else {
					$cons_breck_down .= "__" . $color_number_id . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount . '_' . $pcs . '_' . $colorsizetableid . "_" . $txtwoq_cal . "_" . $articleNumber;
				}
			}
		}
		echo $cons_breck_down;
	}

	/*$level_arr=array();
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id,c.article_number order by size_order";
		$level_arr=$po_size_level_data_arr;
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){
		$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id,c.article_number  order by  color_order,size_order";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		  $sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
		$level_arr=$po_no_sen_level_data_arr;
	}*/

	$level_arr = array();
	$gmt_color_edb = "";
	$item_color_edb = "";
	$gmt_size_edb = "";
	$item_size_edb = "";
	if ($cbo_colorsizesensitive == 1) {
		$sql = "select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)    $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";
		$level_arr = $po_color_level_data_arr;
		$gmt_size_edb = 1;
		$item_size_edb = 1;
	} else if ($cbo_colorsizesensitive == 2) {
		$sql = "select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,c.item_number_id,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.size_number_id,c.article_number,c.item_number_id order by size_order";
		$level_arr = $po_size_level_data_arr;
		$gmt_color_edb = 1;
		$item_color_edb = 1;
	} else if ($cbo_colorsizesensitive == 3) {
		$sql = "select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id,c.item_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.item_number_id order by color_order";
		$level_arr = $po_color_level_data_arr;
		$gmt_size_edb = 1;
		$item_size_edb = 1;
	} else if ($cbo_colorsizesensitive == 4) {
		$sql = "select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,c.item_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.size_number_id) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)   $txt_country_cond and d.id=$txtembcostid group by  c.color_number_id,c.size_number_id, c.article_number,c.item_number_id order by  color_order,size_order,c.article_number";
		$level_arr = $po_color_size_level_data_arr;
	} else {
		$sql = "select b.job_no_mst,c.item_number_id,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.pre_cost_emb_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) and c.item_number_id in($txtgmtitemid)  $txt_country_cond and d.id=$txtembcostid group by  b.job_no_mst,c.item_number_id";
		$level_arr = $po_no_sen_level_data_arr;
	}
	$data_array = sql_select($sql);

	$cons_breck_down = "";
	if (count($data_array) > 0 && $cbo_level == 2) {
		$i = 0;
		foreach ($data_array as $row) {
			if ($cbo_colorsizesensitive == 1) {
				$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']), 5, "");
				$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
				$booking_qty = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['booking_qty']), 5, "");
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			}
			if ($cbo_colorsizesensitive == 2) {
				$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']), 5, "");
				$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
				$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			}
			if ($cbo_colorsizesensitive == 3) {
				$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['req_qty']), 5, "");
				$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]]['order_quantity_set']);
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			}
			if ($cbo_colorsizesensitive == 4) {
				$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']), 5, "");
				$po_qty = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
				$order_quantity_set = array_sum($level_arr[$txtembcostid][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			}
			if ($cbo_colorsizesensitive == 0) {
				$txtwoq_cal = def_number_format(array_sum($level_arr[$txtembcostid]['req_qty']), 5, "");
				$po_qty = array_sum($level_arr[$txtembcostid]['po_qty']);
				$order_quantity_set = array_sum($level_arr[$txtembcostid]['order_quantity_set']);
				$amount = def_number_format($txtwoq_cal * $txt_avg_price, 5, "");
			}
			$color_number_id = $row[csf('color_number_id')];
			if ($color_number_id == "") $color_number_id = 0;

			$size_number_id = $row[csf('size_number_id')];
			if ($size_number_id == "") $size_number_id = 0;

			$description = $txt_pre_des;
			if ($description == "") $description = 0;



			$item_color = $color_library[$row[csf('color_number_id')]];
			if ($item_color == "") $item_color = 0;

			//$item_size=$row[csf('item_size')];
			$item_size = $size_library[$row[csf('size_number_id')]];
			if ($item_size == "") $item_size = 0;
			$excess = 0;

			$pcs = $row[csf('order_quantity_set')];
			if ($pcs == "") $pcs = 0;

			$colorsizetableid = $row[csf('color_size_table_id')];
			if ($colorsizetableid == "") $colorsizetableid = 0;

			$articleNumber = $row[csf('article_number')];
			if ($articleNumber == "") $articleNumber = 'no article';

			if ($txtwoq_cal > 0) {
				if ($cons_breck_down == "") {
					$cons_breck_down .= trim($color_number_id) . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount . '_' . $pcs . '_' . $colorsizetableid . "_" . $txtwoq_cal . "_" . $articleNumber;
				} else {
					$cons_breck_down .= "__" . trim($color_number_id) . '_' . $size_number_id . '_' . $description . '_' . $item_color . '_' . $item_size . '_' . $txtwoq_cal . '_' . $excess . '_' . $txtwoq_cal . '_' . $txt_avg_price . '_' . $amount . '_' . $pcs . '_' . $colorsizetableid . "_" . $txtwoq_cal . "_" . $articleNumber;
				}
			}
		}
		echo $cons_breck_down . "**" . json_encode($level_arr);
	}
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$lock_another_process = '';
	if (str_replace("'", "", $txt_booking_no) != '') {
		$sql = sql_select("select embellishment_job, subcon_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach ($sql as $row) {

			if ($row[csf('embellishment_job')] == "") $row[csf('embellishment_job')] = $row[csf('subcon_job')];
			$lock_another_process = $row[csf('embellishment_job')];
		}
		if ($lock_another_process != '') {
			echo "lockAnotherProcess**" . $lock_another_process;
			disconnect($con);
			die;
		}
	}
	//echo $lock_another_process; die;

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if ($db_type == 0) {
			$new_booking_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'AEB', date("Y", time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=11 and entry_form=612 and YEAR(insert_date)=" . date('Y', time()) . " order by id desc ", "booking_no_prefix", "booking_no_prefix_num"));
		} else if ($db_type == 2) {
			$new_booking_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'AEB', date("Y", time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=11 and entry_form=612 and to_char(insert_date,'YYYY')=" . date('Y', time()) . " order by id desc ", "booking_no_prefix", "booking_no_prefix_num"));
		}

		$id = return_next_id("id", "wo_booking_mst", 1); //cbo_isshort
		$field_array = "id,booking_type,is_short,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,currency_id,item_category,pay_mode,source,booking_date,delivery_date,supplier_id,attention,tenor,ready_to_approved,delivery_to,entry_form,inserted_by,insert_date,cbo_level,remarks";
		$data_array = "(" . $id . ",11," . $cbo_isshort . ",'" . $new_booking_no[1] . "'," . $new_booking_no[2] . ",'" . $new_booking_no[0] . "'," . $cbo_company_name . "," . $cbo_buyer_name . "," . $cbo_currency . ",25," . $cbo_pay_mode . "," . $cbo_source . "," . $txt_booking_date . "," . $txt_delivery_date . "," . $hidden_supplier_id . "," . $txt_attention . "," . $txt_tenor . "," . $cbo_ready_to_approved.",".$txt_delivery_to . ",612," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_level . "," . $remarks . ")";
		//echo "10** insert into wo_booking_mst (".$field_array.") values ".$data_array;die;
		$rID = sql_insert("wo_booking_mst", $field_array, $data_array, 0);
		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "0**" . $new_booking_no[0] . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_booking_no[0] . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "0**" . $new_booking_no[0] . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . $new_booking_no[0] . "**" . $id;
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$booking_mst_id = str_replace("'", "", $booking_mst_id);
		$con = connect();
		$is_approved = 0;
		$sql = sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach ($sql as $row) {
			$is_approved = $row[csf('is_approved')];
		}
		if ($is_approved == 1) {
			echo "approved**" . str_replace("'", "", $txt_booking_no);
			disconnect($con);
			die;
		}
		$sales_order = 0;
		$sqls = sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach ($sqls as $rows) {
			$sales_order = $rows[csf('job_no')];
		}
		if ($sales_order) {
			echo "sal1**" . str_replace("'", "", $txt_booking_no) . "**" . $sales_order;
			disconnect($con);
			die;
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number = return_field_value("pi_number", "com_pi_master_details a,com_pi_item_details b", " a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and b.item_category_id=25 and  b.is_deleted=0");
		if ($pi_number) {
			echo "piNo**" . str_replace("'", "", $txt_booking_no) . "**" . $pi_number;
			disconnect($con);
			die;
		}
		//}
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$field_array = "buyer_id*currency_id*item_category*pay_mode*source*booking_date*delivery_date*supplier_id*attention*tenor*delivery_to*ready_to_approved*entry_form*is_short*updated_by*update_date*cbo_level*remarks";
		$data_array = "" . $cbo_buyer_name . "*" . $cbo_currency . "*25*" . $cbo_pay_mode . "*" . $cbo_source . "*" . $txt_booking_date . "*" . $txt_delivery_date . "*" . $hidden_supplier_id . "*" . $txt_attention . "*" . $txt_tenor . "*" . $txt_delivery_to. "*" . $cbo_ready_to_approved  . "*612*" . $cbo_isshort . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_level . "*" . $remarks . "";
		$rID = sql_update("wo_booking_mst", $field_array, $data_array, "id", $booking_mst_id, 0);

		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 2)   // Delete Here
	{
		$booking_mst_id = str_replace("'", "", $booking_mst_id);
		$con = connect();
		$is_approved = 0;
		$sql = sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach ($sql as $row) {
			$is_approved = $row[csf('is_approved')];
		}
		if ($is_approved == 1) {
			echo "approved**" . str_replace("'", "", $txt_booking_no);
			disconnect($con);
			die;
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number = return_field_value("pi_number", "com_pi_master_details a,com_pi_item_details b", " a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_category_id=25  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if ($pi_number) {
			echo "piNo**" . str_replace("'", "", $txt_booking_no) . "**" . $pi_number;
			disconnect($con);
			die;
		}
		//}
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$delete_cause = str_replace("'", "", $delete_cause);
		$delete_cause = str_replace('"', '', $delete_cause);
		$delete_cause = str_replace('(', '', $delete_cause);
		$delete_cause = str_replace(')', '', $delete_cause);
		$rID = execute_query("update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where  id=$booking_mst_id", 0);
		$rID1 = execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where  booking_mst_id=$booking_mst_id", 0);
		$rID2 = execute_query("update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  booking_mst_id=$booking_mst_id", 0);
		if ($db_type == 0) {
			if ($rID && $rID1 && $rID2) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID1 && $rID2) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "save_update_delete_dtls") {
	$booking_mst_id = str_replace("'", "", $booking_mst_id);
	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$is_approved = return_field_value("is_approved", "wo_booking_mst", "booking_no=$txt_booking_no");
	if ($is_approved == 1) {
		echo "app1**" . str_replace("'", "", $txt_booking_no);
		disconnect($con);
		die;
	}
	$lock_another_process = '';
	if (str_replace("'", "", $txt_booking_no) != '') {
		$sql = sql_select("select embellishment_job, subcon_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach ($sql as $row) {
			if ($row[csf('embellishment_job')] == "") $row[csf('embellishment_job')] = $row[csf('subcon_job')];
			$lock_another_process = $row[csf('embellishment_job')];
		}
		if ($lock_another_process != '') {
			echo "lockAnotherProcess**" . $lock_another_process;
			disconnect($con);
			die;
		}
	}
	if ($operation == 0) {
		$gmtArr = array();
		$poArr = array();
		$pre_emb_id_arr = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtembcostid = str_replace("'", "", $$txtembcostid);
			$poid = str_replace("'", "", $$txtpoid);
			$gmtItem = str_replace("'", "", $$txtgmtitemid);
			$pre_emb_id_arr[$pretrimcostid] = $txtembcostid;
			$poArr[$poid] = $poid;
			$gmtArr[$gmtItem] = $gmtItem;
		}
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**0";
			disconnect($con);
			die;
		}

		if (is_duplicate_field("booking_no", "wo_booking_dtls", "gmt_item in(" . implode(",", $gmtArr) . ") and po_break_down_id in (" . implode(",", $poArr) . ") and pre_cost_fabric_cost_dtls_id in(" . implode(",", $pre_emb_id_arr) . ") and booking_type=11 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1) {
			check_table_status($_SESSION['menu_id'], 0);
			echo "11**0";
			disconnect($con);
			die;
		}

		$id_dtls = return_next_id("id", "wo_booking_dtls", 1);
		//$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, is_short, gmt_item,description,brand_supplier, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, country_id_string, inserted_by, insert_date";

		//$field_array2="id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number";

		$field_array1 = "id,booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no,gmt_item, booking_no, booking_type, is_short, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date,country_id_string, inserted_by, insert_date";
		//=pocolorid+'_'+gmtssizesid+'_'+des+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle;

		$field_array2 = "id,booking_mst_id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs,color_size_table_id,article_number";

		$add_comma = 0;
		$id1 = return_next_id("id", "wo_emb_book_con_dtls", 1);
		$new_array_color = array();
		for ($i = 1; $i <= $total_row; $i++) {


			//===============
			$txtembcostid = "txtembcostid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtuom = "txtuom_" . $i;
			$cbocolorsizesensitive = "cbocolorsizesensitive_" . $i;
			$txtwoq = "txtwoq_" . $i;
			$txtexchrate = "txtexchrate_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtddate = "txtddate_" . $i;
			$consbreckdown = "consbreckdown_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$txtcountry = "txtcountry_" . $i;
			$txtjob_id = "txtjob_" . $i;
			$txtreqqnty = "txtreqqnty_" . $i;
			$jsondata = "jsondata_" . $i;
			$txtreqamount = "txtreqamount_" . $i;

			$uom_id = str_replace("'", "", $$txtuom);
			$job = str_replace("'", "", $$txtjob_id);
			$embcostid = str_replace("'", "", $$txtembcostid);
			$gmtitemid = str_replace("'", "", $$txtgmtitemid);
			$reqqnty = str_replace("'", "", $$txtreqqnty);
			$woq = str_replace("'", "", $$txtwoq);
			$rate = str_replace("'", "", $$txtrate);
			$amt = str_replace("'", "", $$txtamount);
			$exRate = str_replace("'", "", $$txtexchrate);
			//==============

			$data_array1 = "(" . $id_dtls . "," . $booking_mst_id . "," . $$txtembcostid . "," . $$txtpoid . "," . $$txtjob_id . "," . $$txtgmtitemid . "," . $txt_booking_no . ",11,2," . $$txtuom . "," . $$cbocolorsizesensitive . "," . $$txtwoq . "," . $$txtexchrate . "," . $$txtrate . "," . $$txtamount . "," . $$txtddate . "," . $$txtcountry . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			//	CONS break down===============================================================================================
			if (str_replace("'", '', $$consbreckdown) != '') {
				$data_array2 = "";
				$rID_de1 = execute_query("delete from wo_emb_book_con_dtls where  wo_booking_dtls_id =" . $$txtbookingid . "", 0);
				$consbreckdown_array = explode('__', str_replace("'", '', $$consbreckdown));
				for ($c = 0; $c < count($consbreckdown_array); $c++) {
					$consbreckdownarr = explode('_', $consbreckdown_array[$c]);
					if (str_replace("'", "", $consbreckdownarr[3]) != "") {
						if (!in_array(str_replace("'", "", $consbreckdownarr[3]), $new_array_color)) {
							$color_id = return_id(str_replace("'", "", $consbreckdownarr[3]), $color_library, "lib_color", "id,color_name", "403");
							$new_array_color[$color_id] = str_replace("'", "", $consbreckdownarr[3]);
						} else $color_id =  array_search(str_replace("'", "", $consbreckdownarr[3]), $new_array_color);
					} else $color_id = 0;

					//=pocolorid+'_'+gmtssizesid+'_'+des+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle;

					//$field_array2="id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs,color_size_table_id,article_number";

					if ($c != 0) $data_array2 .= ",";
					$data_array2 .= "(" . $id1 . "," . $booking_mst_id . "," . $id_dtls . "," . $txt_booking_no . "," . $$txtjob_id . "," . $$txtpoid . ",'" . $consbreckdownarr[0] . "','" . $consbreckdownarr[1] . "','" . $consbreckdownarr[2] . "','" . $color_id . "','" . $consbreckdownarr[4] . "','" . $consbreckdownarr[5] . "','" . $consbreckdownarr[6] . "','" . $consbreckdownarr[7] . "','" . $consbreckdownarr[8] . "','" . $consbreckdownarr[9] . "','" . $consbreckdownarr[10] . "','" . $consbreckdownarr[11] . "','" . $consbreckdownarr[13] . "')";
					$id1 = $id1 + 1;
					$add_comma++;
					//echo "10** insert into wo_emb_book_con_dtls (".$field_array2.") values ".$data_array2;die;
				}
			}
			//CONS break down end===============================================================================================
			$rID1 = sql_insert("wo_booking_dtls", $field_array1, $data_array1, 0);
			$rID2 = 1;
			if ($data_array2 != "") {
				$rID2 = sql_insert("wo_emb_book_con_dtls", $field_array2, $data_array2, 1);
			}
			$id_dtls = $id_dtls + 1;
		}

		check_table_status($_SESSION['menu_id'], 0);
		//echo "10**".$rID1." &&". $rID2;die;
		if ($db_type == 0) {
			if ($rID1 && $rID2) {
				mysql_query("COMMIT");
				echo "0**" . $new_booking_no[0];
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_booking_no[0];
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2) {
				oci_commit($con);
				echo "0**" . $new_booking_no[0];
			} else {
				oci_rollback($con);
				echo "10**" . $new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) {
		$sql_pi = sql_select("select a.pi_number,b.quantity,b.embell_name,b.gmts_item_id,b.pi_id,b.work_order_no,b.work_order_id,b.work_order_dtls_id from com_pi_master_details a,com_pi_item_details b where b.pi_id=a.id and a.status_active=1 and b.status_active=1 and b.item_category_id=25 and b.work_order_no=$txt_booking_no");
		foreach ($sql_pi as $row) {
			$pi_no_arr[$row[csf('gmts_item_id')]][$row[csf('embell_name')]] += $row[csf('quantity')];
			$pi_number[$row[csf('pi_number')]] = $row[csf('pi_number')];
		}

		$gmtArr = array();
		$poArr = array();
		$pre_emb_id_arr = array();
		$booking_dtls_id_arr = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtbookingid = "txtbookingid_" . $i;

			$txtwoq = "txtwoq_" . $i;
			$txtemb_name_id = "emb_name_" . $i;
			$emb_name_id = str_replace("'", "", $$txtemb_name_id);

			$txtembcostid = str_replace("'", "", $$txtembcostid);
			$poid = str_replace("'", "", $$txtpoid);
			$gmtItem = str_replace("'", "", $$txtgmtitemid);
			$bookingdtlsid = str_replace("'", "", $$txtbookingid);

			$pre_emb_id_arr[$pretrimcostid] = $txtembcostid;
			$poArr[$poid] = $poid;
			$gmtArr[$gmtItem] = $gmtItem;
			$booking_dtls_id_arr[$bookingdtlsid] = $bookingdtlsid;

			$pi_qty = $pi_no_arr[$gmtItem][$emb_name_id];
			if ($pi_qty && str_replace("'", "", $$txtwoq) < $pi_qty) {
				echo "piNo**" . str_replace("'", "", $txt_booking_no) . "**" . implode(",", $pi_number) . "**" . $pi_qty;
				check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}



		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**1";
			disconnect($con);
			die;
		}

		if (is_duplicate_field("booking_no", "wo_booking_dtls", "gmt_item in(" . implode(",", $gmtArr) . ") and po_break_down_id in (" . implode(",", $poArr) . ") and pre_cost_fabric_cost_dtls_id in(" . implode(",", $pre_emb_id_arr) . ") and id not in (" . implode(",", $booking_dtls_id_arr) . ") and booking_type=11 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1) {
			check_table_status($_SESSION['menu_id'], 0);
			echo "11**0";
			disconnect($con);
			die;
		}


		//$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*gmt_item*description*brand_supplier*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
		//$field_array_up2="id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id,article_number";

		$field_array_up1 = "pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*gmt_item*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
		$field_array_up2 = "id,booking_mst_id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id,article_number";

		$add_comma = 0;
		$id1 = return_next_id("id", "wo_emb_book_con_dtls", 1);
		$new_array_color = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtuom = "txtuom_" . $i;
			$cbocolorsizesensitive = "cbocolorsizesensitive_" . $i;
			$txtwoq = "txtwoq_" . $i;
			$txtexchrate = "txtexchrate_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtddate = "txtddate_" . $i;
			$consbreckdown = "consbreckdown_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$txtcountry = "txtcountry_" . $i;
			$txtjob_id = "txtjob_" . $i;
			$txtreqqnty = "txtreqqnty_" . $i;
			$jsondata = "jsondata_" . $i;
			$txtreqamount = "txtreqamount_" . $i;

			$uom_id = str_replace("'", "", $$txtuom);
			$job = str_replace("'", "", $$txtjob_id);
			$embcostid = str_replace("'", "", $$txtembcostid);
			$gmtitemid = str_replace("'", "", $$txtgmtitemid);
			$reqqnty = str_replace("'", "", $$txtreqqnty);
			$woq = str_replace("'", "", $$txtwoq);
			$rate = str_replace("'", "", $$txtrate);
			$amt = str_replace("'", "", $$txtamount);
			$exRate = str_replace("'", "", $$txtexchrate);

			if (str_replace("'", '', $$txtbookingid) != "") {
				$id_arr = array();
				$data_array_up1 = array();
				$id_arr[] = str_replace("'", '', $$txtbookingid);
				$data_array_up1[str_replace("'", '', $$txtbookingid)] = explode("*", ("" . $$txtembcostid . "*" . $$txtpoid . "*" . $$txtjob_id . "*" . $$txtgmtitemid . "*" . $$txtuom . "*" . $$cbocolorsizesensitive . "*" . $$txtwoq . "*" . $$txtexchrate . "*" . $$txtrate . "*" . $$txtamount . "*" . $$txtddate . "*" . $$txtcountry . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

				//	CONS break down===============================================================================================
				if (str_replace("'", '', $$consbreckdown) != '') {
					$data_array_up2 = "";
					//$rID_de1=execute_query( "delete from wo_emb_book_con_dtls where  wo_booking_dtls_id =".$$txtbookingid."",0);
					$rID_de1 = execute_query("update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id =" . $$txtbookingid . "", 0);
					$consbreckdown_array = explode('__', str_replace("'", '', $$consbreckdown));
					for ($c = 0; $c < count($consbreckdown_array); $c++) {
						$consbreckdownarr = explode('_', $consbreckdown_array[$c]);
						if (str_replace("'", "", $consbreckdownarr[3]) != "") {
							if (!in_array(str_replace("'", "", $consbreckdownarr[3]), $new_array_color)) {
								$color_id = return_id(str_replace("'", "", $consbreckdownarr[3]), $color_library, "lib_color", "id,color_name", "403");
								$new_array_color[$color_id] = str_replace("'", "", $consbreckdownarr[3]);
							} else $color_id =  array_search(str_replace("'", "", $consbreckdownarr[3]), $new_array_color);
						} else $color_id = 0;


						if ($c != 0) $data_array_up2 .= ",";
						$data_array_up2 .= "(" . $id1 . "," . $booking_mst_id . "," . $$txtbookingid . "," . $txt_booking_no . "," . $$txtjob_id . "," . $$txtpoid . ",'" . $consbreckdownarr[0] . "','" . $consbreckdownarr[1] . "','" . $consbreckdownarr[2] . "','" . $color_id . "','" . $consbreckdownarr[4] . "','" . $consbreckdownarr[5] . "','" . $consbreckdownarr[6] . "','" . $consbreckdownarr[7] . "','" . $consbreckdownarr[8] . "','" . $consbreckdownarr[9] . "','" . $consbreckdownarr[10] . "','" . $consbreckdownarr[11] . "','" . $consbreckdownarr[13] . "')";
						$id1 = $id1 + 1;
						$add_comma++;
					}
				}
				//CONS break down end===============================================================================================
				if ($data_array_up1 != "") {
					$rID1 = execute_query(bulk_update_sql_statement("wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr));
				}
			}
			$rID2 = 1;
			if ($data_array_up2 != "") {
				$rID2 = sql_insert("wo_emb_book_con_dtls", $field_array_up2, $data_array_up2, 1);
			}
		}
		$rID = execute_query("update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_booking_no", 0);
		check_table_status($_SESSION['menu_id'], 0);
		if ($db_type == 0) {
			if ($rID1 &&  $rID2) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID1 &&  $rID2) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) {
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$pi_number = return_field_value("pi_number", "com_pi_master_details a,com_pi_item_details b", " a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and b.item_category_id=25 and  b.is_deleted=0");
		if ($pi_number) {
			echo "piNo**" . str_replace("'", "", $txt_booking_no) . "**" . $pi_number;
			disconnect($con);
			die;
		}
		for ($i = 1; $i <= $total_row; $i++) {
			$txtpoid = "txtpoid_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$delete_cause = str_replace("'", "", $delete_cause);
			$delete_cause = str_replace('"', '', $delete_cause);
			$delete_cause = str_replace('(', '', $delete_cause);
			$delete_cause = str_replace(')', '', $delete_cause);

			//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
			//$rID2=execute_query( "delete from wo_emb_book_con_dtls where  wo_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);

			$rID1 = execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "'   where  id in (" . str_replace("'", "", $$txtbookingid) . ") and booking_no=$txt_booking_no", 0);
			$rID2 = execute_query("update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id in(" . str_replace("'", "", $$txtbookingid) . ") and booking_no=$txt_booking_no", 0);
		}
		if ($db_type == 0) {
			if ($rID1 &&  $rID2) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID1 &&  $rID2) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "save_update_delete_dtls_job_level") {
	$booking_mst_id = str_replace("'", "", $booking_mst_id);
	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$is_approved = return_field_value("is_approved", "wo_booking_mst", "booking_no=$txt_booking_no");
	if ($is_approved == 1) {
		echo "app1**" . str_replace("'", "", $txt_booking_no);
		disconnect($con);
		die;
	}
	$lock_another_process = '';
	if (str_replace("'", "", $txt_booking_no) != '') {
		$sql = sql_select("select embellishment_job, subcon_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach ($sql as $row) {
			if ($row[csf('embellishment_job')] == "") $row[csf('embellishment_job')] = $row[csf('subcon_job')];
			$lock_another_process = $row[csf('embellishment_job')];
		}
		if ($lock_another_process != '') {
			echo "lockAnotherProcess**" . $lock_another_process;
			disconnect($con);
			die;
		}
	}
	$strdata = json_decode(str_replace("'", "", $strdata));
	if ($operation == 0) {
		$gmtArr = array();
		$poArr = array();
		$pre_emb_id_arr = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;

			$txtembcostid = str_replace("'", "", $$txtembcostid);
			$poid = str_replace("'", "", $$txtpoid);
			$gmtItem = str_replace("'", "", $$txtgmtitemid);

			$pre_emb_id_arr[$pretrimcostid] = $txtembcostid;
			$poArr[$poid] = $poid;
			$gmtArr[$gmtItem] = $gmtItem;
		}


		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**0";
			disconnect($con);
			die;
		}

		if (is_duplicate_field("booking_no", "wo_booking_dtls", "gmt_item in(" . implode(",", $gmtArr) . ") and po_break_down_id in (" . implode(",", $poArr) . ") and pre_cost_fabric_cost_dtls_id in(" . implode(",", $pre_emb_id_arr) . ") and booking_type=11 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1) {
			check_table_status($_SESSION['menu_id'], 0);
			echo "11**0";
			disconnect($con);
			die;
		}

		$id_dtls = return_next_id("id", "wo_booking_dtls", 1);

		$field_array1 = "id,booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no,gmt_item, booking_no, booking_type, is_short, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date,country_id_string, inserted_by, insert_date";

		$field_array2 = "id,booking_mst_id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs,color_size_table_id,article_number";

		$add_comma = 0;
		$id1 = return_next_id("id", "wo_emb_book_con_dtls", 1);
		$new_array_color = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtuom = "txtuom_" . $i;
			$cbocolorsizesensitive = "cbocolorsizesensitive_" . $i;
			$txtwoq = "txtwoq_" . $i;
			$txtexchrate = "txtexchrate_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtddate = "txtddate_" . $i;
			$consbreckdown = "consbreckdown_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$txtcountry = "txtcountry_" . $i;
			$txtjob_id = "txtjob_" . $i;
			$txtreqqnty = "txtreqqnty_" . $i;
			$jsondata = "jsondata_" . $i;
			$txtreqamount = "txtreqamount_" . $i;

			$jsonarr = json_decode(str_replace("'", "", $$jsondata));
			$uom_id = str_replace("'", "", $$txtuom);
			$job = str_replace("'", "", $$txtjob_id);
			$embcostid = str_replace("'", "", $$txtembcostid);
			$gmtitemid = str_replace("'", "", $$txtgmtitemid);
			$reqqnty = str_replace("'", "", $$txtreqqnty);
			$woq = str_replace("'", "", $$txtwoq);
			$rate = str_replace("'", "", $$txtrate);
			$amt = str_replace("'", "", $$txtamount);
			$exRate = str_replace("'", "", $$txtexchrate);

			foreach ($strdata->$job->$embcostid->$gmtitemid->po_id as $poId) {
				$wqQty = ($strdata->$job->$embcostid->$gmtitemid->req_qnty->$poId / $reqqnty) * $woq;
				$amount = $wqQty * $rate;
				$data_array1 = "(" . $id_dtls . "," . $booking_mst_id . "," . $$txtembcostid . "," . $poId . "," . $$txtjob_id . "," . $$txtgmtitemid . "," . $txt_booking_no . ",11,2," . $$txtuom . "," . $$cbocolorsizesensitive . "," . $wqQty . "," . $$txtexchrate . "," . $$txtrate . "," . $amount . "," . $$txtddate . "," . $$txtcountry . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//echo "10** insert into wo_booking_dtls (".$field_array1.") values ".$data_array1; die;
				$rID1 = sql_insert("wo_booking_dtls", $field_array1, $data_array1, 0);
				if (str_replace("'", '', $$consbreckdown) != '') {
					$rID_de1 = execute_query("update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id =" . $$txtbookingid . "", 0);
					$consbreckdown_array = explode('__', str_replace("'", '', $$consbreckdown));
					$d = 0;
					for ($c = 0; $c < count($consbreckdown_array); $c++) {
						$consbreckdownarr = explode('_', $consbreckdown_array[$c]);
						if (str_replace("'", "", $consbreckdownarr[3]) != "") {
							if (!in_array(str_replace("'", "", $consbreckdownarr[3]), $new_array_color)) {
								$color_id = return_id(str_replace("'", "", $consbreckdownarr[3]), $color_library, "lib_color", "id,color_name", "403");
								$new_array_color[$color_id] = str_replace("'", "", $consbreckdownarr[3]);
							} else $color_id =  array_search(str_replace("'", "", $consbreckdownarr[3]), $new_array_color);
						} else $color_id = 0;

						$gmc = $consbreckdownarr[0];
						$gms = $consbreckdownarr[1];
						$art = $consbreckdownarr[13];
						if (str_replace("'", "", $$cbocolorsizesensitive) == 1 || str_replace("'", "", $$cbocolorsizesensitive) == 3) {
							$bQty = ($jsonarr->$embcostid->$gmc->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
							$bwqQty = ($jsonarr->$embcostid->$gmc->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
							$order_qty = $jsonarr->$embcostid->$gmc->order_quantity->$poId;
							$colorSizeTableId = $jsonarr->$embcostid->$gmc->color_size_table_id->$poId;
						}
						if (str_replace("'", "", $$cbocolorsizesensitive) == 2) {
							$bQty = ($jsonarr->$embcostid->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
							$bwqQty = ($jsonarr->$embcostid->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
							$order_qty = $jsonarr->$embcostid->$gms->$art->order_quantity->$poId;
							$colorSizeTableId = $jsonarr->$embcostid->$gms->$art->color_size_table_id->$poId;
						}
						if (str_replace("'", "", $$cbocolorsizesensitive) == 4) {
							$bQty = ($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
							$bwqQty = ($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
							$order_qty = $jsonarr->$embcostid->$gmc->$gms->$art->order_quantity->$poId;
							$colorSizeTableId = $jsonarr->$embcostid->$gmc->$gms->$art->color_size_table_id->$poId;
						}
						if (str_replace("'", "", $$cbocolorsizesensitive) == 0) {
							$bQty = ($jsonarr->$embcostid->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
							$bwqQty = ($jsonarr->$embcostid->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
							$order_qty = $jsonarr->$embcostid->order_quantity->$poId;
							$colorSizeTableId = $jsonarr->$embcostid->color_size_table_id->$poId;
						}
						$bamount = $bwqQty * $consbreckdownarr[8];
						if ($d != 0) {
							$data_array2 .= ",";
						}
						$data_array2 = "(" . $id1 . "," . $booking_mst_id . "," . $id_dtls . "," . $txt_booking_no . "," . $$txtjob_id . "," . $poId . ",'" . $consbreckdownarr[0] . "','" . $consbreckdownarr[1] . "','" . $consbreckdownarr[2] . "','" . $color_id . "','" . $consbreckdownarr[4] . "','" . $bQty . "','" . $consbreckdownarr[6] . "','" . $bwqQty . "','" . $consbreckdownarr[8] . "','" . $bamount . "','" . $order_qty . "','" . $colorSizeTableId . "','" . $consbreckdownarr[13] . "')";
						$id1 = $id1 + 1;
						$add_comma++;
						$d++;
						$rID2 = sql_insert("wo_emb_book_con_dtls", $field_array2, $data_array2, 0);
					}
				} //CONS break down end==============================================================================================
				$id_dtls = $id_dtls + 1;
			}
		}
		check_table_status($_SESSION['menu_id'], 0);
		//echo "10**".$rID1."==".$rID2;
		if ($db_type == 0) {
			if ($rID1 && $rID2) {
				mysql_query("COMMIT");
				echo "0**" . $new_booking_no[0];
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_booking_no[0];
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2) {
				oci_commit($con);
				echo "0**" . $new_booking_no[0];
			} else {
				oci_rollback($con);
				echo "**10**" . $new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) {
		$gmtArr = array();
		$poArr = array();
		$pre_emb_id_arr = array();
		$booking_dtls_id_arr = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtbookingid = "txtbookingid_" . $i;

			$txtembcostid = str_replace("'", "", $$txtembcostid);
			$poid = str_replace("'", "", $$txtpoid);
			$gmtItem = str_replace("'", "", $$txtgmtitemid);
			$bookingdtlsid = str_replace("'", "", $$txtbookingid);

			$pre_emb_id_arr[$pretrimcostid] = $txtembcostid;
			$poArr[$poid] = $poid;
			$gmtArr[$gmtItem] = $gmtItem;
			$booking_dtls_id_arr[$bookingdtlsid] = $bookingdtlsid;
		}

		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**1";
			disconnect($con);
			die;
		}
		if (is_duplicate_field("booking_no", "wo_booking_dtls", "gmt_item in(" . implode(",", $gmtArr) . ") and po_break_down_id in (" . implode(",", $poArr) . ") and pre_cost_fabric_cost_dtls_id in(" . implode(",", $pre_emb_id_arr) . ") and id not in (" . implode(",", $booking_dtls_id_arr) . ") and booking_type=11 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0") == 1) {
			check_table_status($_SESSION['menu_id'], 0);
			echo "11**0";
			disconnect($con);
			die;
		}

		$sql_pi = sql_select("select a.pi_number,b.quantity,b.embell_name,b.gmts_item_id,b.pi_id,b.work_order_no,b.work_order_id,b.work_order_dtls_id from com_pi_master_details a,com_pi_item_details b where b.pi_id=a.id and a.status_active=1 and b.status_active=1 and b.item_category_id=25 and b.work_order_no=$txt_booking_no");
		foreach ($sql_pi as $row) {
			$pi_no_arr[$row[csf('gmts_item_id')]][$row[csf('embell_name')]] += $row[csf('quantity')];
			$pi_number[$row[csf('pi_number')]] = $row[csf('pi_number')];
		}
		for ($i = 1; $i <= $total_row; $i++) {
			$txtwoq = "txtwoq_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtemb_name_id = "emb_name_" . $i;
			$gmt_itemid = str_replace("'", "", $$txtgmtitemid);
			$emb_name_id = str_replace("'", "", $$txtemb_name_id);
			//$wo_qty=str_replace("'","",$$txtwoq);
			$pi_qty = $pi_no_arr[$gmt_itemid][$emb_name_id];

			if ($pi_qty && str_replace("'", "", $$txtwoq) < $pi_qty) {
				echo "piNo**" . str_replace("'", "", $txt_booking_no) . "**" . implode(",", $pi_number) . "**" . $pi_qty;
				check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}


		$field_array_up1 = "pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*gmt_item*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";

		$field_array_up2 = "id,booking_mst_id,wo_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id,article_number";
		$add_comma = 0;
		$id1 = return_next_id("id", "wo_emb_book_con_dtls", 1);
		$new_array_color = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$txtembcostid = "txtembcostid_" . $i;
			$txtgmtitemid = 'txtgmtitemid_' . $i;
			$txtpoid = "txtpoid_" . $i;
			$txtuom = "txtuom_" . $i;
			$cbocolorsizesensitive = "cbocolorsizesensitive_" . $i;
			$txtwoq = "txtwoq_" . $i;
			$txtexchrate = "txtexchrate_" . $i;
			$txtrate = "txtrate_" . $i;
			$txtamount = "txtamount_" . $i;
			$txtddate = "txtddate_" . $i;
			$consbreckdown = "consbreckdown_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$txtcountry = "txtcountry_" . $i;
			$txtjob_id = "txtjob_" . $i;
			$txtreqqnty = "txtreqqnty_" . $i;
			$jsondata = "jsondata_" . $i;
			$txtreqamount = "txtreqamount_" . $i;

			$jsonarr = json_decode(str_replace("'", "", $$jsondata));
			$uom_id = str_replace("'", "", $$txtuom);
			$job = str_replace("'", "", $$txtjob_id);
			$embcostid = str_replace("'", "", $$txtembcostid);
			$gmtitemid = str_replace("'", "", $$txtgmtitemid);
			$reqqnty = str_replace("'", "", $$txtreqqnty);
			$woq = str_replace("'", "", $$txtwoq);
			$rate = str_replace("'", "", $$txtrate);
			$amt = str_replace("'", "", $$txtamount);
			$exRate = str_replace("'", "", $$txtexchrate);

			if (str_replace("'", '', $$txtbookingid) != "") {
				foreach ($strdata->$job->$embcostid->$gmtitemid->po_id as $poId) {
					$wqQty = ($strdata->$job->$embcostid->$gmtitemid->req_qnty->$poId / $reqqnty) * $woq;
					$amount = $wqQty * $rate;
					$id_arr = array();
					$data_array_up1 = array();
					$id_arr[] = str_replace("'", '', $strdata->$job->$embcostid->$gmtitemid->booking_id->$poId);
					$data_array_up1[str_replace("'", '', $strdata->$job->$embcostid->$gmtitemid->booking_id->$poId)] = explode("*", ("" . $$txtembcostid . "*" . $poId . "*" . $$txtjob_id . "*" . $gmtitemid . "*" . $$txtuom . "*" . $$cbocolorsizesensitive . "*" . $wqQty . "*" . $$txtexchrate . "*" . $$txtrate . "*" . $amount . "*" . $$txtddate . "*" . $$txtcountry . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
					//echo "10**".bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );die;
					if ($data_array_up1 != "") {
						$rID1 = execute_query(bulk_update_sql_statement("wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr));
					}
					//	CONS break down===============================================================================================
					$rID2 = 1;
					if (str_replace("'", '', $$consbreckdown) != '') {
						$rID_de1 = execute_query("update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id =" . $strdata->$job->$embcostid->$gmtitemid->booking_id->$poId . "", 0);
						$consbreckdown_array = explode('__', str_replace("'", '', $$consbreckdown));
						$d = 0;
						for ($c = 0; $c < count($consbreckdown_array); $c++) {
							$consbreckdownarr = explode('_', $consbreckdown_array[$c]);
							if (str_replace("'", "", $consbreckdownarr[3]) != "") {
								if (!in_array(str_replace("'", "", $consbreckdownarr[3]), $new_array_color)) {
									$color_id = return_id(str_replace("'", "", $consbreckdownarr[3]), $color_library, "lib_color", "id,color_name", "403");
									$new_array_color[$color_id] = str_replace("'", "", $consbreckdownarr[3]);
								} else $color_id =  array_search(str_replace("'", "", $consbreckdownarr[3]), $new_array_color);
							} else $color_id = 0;

							$gmc = $consbreckdownarr[0];
							$gms = $consbreckdownarr[1];
							$art = $consbreckdownarr[13];

							if (str_replace("'", "", $$cbocolorsizesensitive) == 1 || str_replace("'", "", $$cbocolorsizesensitive) == 3) {
								$bQty = ($jsonarr->$embcostid->$gmc->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
								$bwqQty = ($jsonarr->$embcostid->$gmc->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
								$order_qty = $jsonarr->$embcostid->$gmc->order_quantity->$poId;
								$colorSizeTableId = $jsonarr->$embcostid->$gmc->color_size_table_id->$poId;
							}
							if (str_replace("'", "", $$cbocolorsizesensitive) == 2) {
								$bQty = ($jsonarr->$embcostid->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
								$bwqQty = ($jsonarr->$embcostid->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
								$order_qty = $jsonarr->$embcostid->$gms->$art->order_quantity->$poId;
								$colorSizeTableId = $jsonarr->$embcostid->$gms->$art->color_size_table_id->$poId;
							}
							if (str_replace("'", "", $$cbocolorsizesensitive) == 4) {
								$bQty = ($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
								$bwqQty = ($jsonarr->$embcostid->$gmc->$gms->$art->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
								$order_qty = $jsonarr->$embcostid->$gmc->$gms->$art->order_quantity->$poId;
								$colorSizeTableId = $jsonarr->$embcostid->$gmc->$gms->$art->color_size_table_id->$poId;
							}
							if (str_replace("'", "", $$cbocolorsizesensitive) == 0) {
								$bQty = ($jsonarr->$embcostid->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[5];
								$bwqQty = ($jsonarr->$embcostid->req_qty->$poId / $consbreckdownarr[12]) * $consbreckdownarr[7];
								$order_qty = $jsonarr->$embcostid->order_quantity->$poId;
								$colorSizeTableId = $jsonarr->$embcostid->color_size_table_id->$poId;
							}

							$bamount = $bwqQty * $consbreckdownarr[8];
							if ($d != 0) $data_array2 .= ",";
							$data_array2 = "(" . $id1 . "," . $booking_mst_id . "," . $strdata->$job->$embcostid->$gmtitemid->booking_id->$poId . "," . $txt_booking_no . "," . $$txtjob_id . "," . $poId . ",'" . $consbreckdownarr[0] . "','" . $consbreckdownarr[1] . "','" . $consbreckdownarr[2] . "','" . $color_id . "','" . $consbreckdownarr[4] . "','" . $bQty . "','" . $consbreckdownarr[6] . "','" . $bwqQty . "','" . $consbreckdownarr[8] . "','" . $bamount . "','" . $order_qty . "','" . $colorSizeTableId . "','" . $consbreckdownarr[13] . "')";
							$id1 = $id1 + 1;
							$add_comma++;
							$d++;
							check_table_status($_SESSION['menu_id'], 0);

							//echo "10**insert into wo_emb_book_con_dtls (".$field_array_up2.") values ".$data_array2;die;
							$rID2 = sql_insert("wo_emb_book_con_dtls", $field_array_up2, $data_array2, 0);
						}
					} //CONS break down end==============================================================================================
				}
			}
		}
		$rID = execute_query("update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_booking_no", 0);

		check_table_status($_SESSION['menu_id'], 0);
		//echo "10**".$rID1 ."&&". $rID2;
		if ($db_type == 0) {
			if ($rID1 && $rID2) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) {

		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$pi_number = return_field_value("pi_number", "com_pi_master_details a,com_pi_item_details b", " a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and b.item_category_id=25 and  b.is_deleted=0");
		if ($pi_number) {
			echo "piNo**" . str_replace("'", "", $txt_booking_no) . "**" . $pi_number . "**0";
			disconnect($con);
			die;
		}
		for ($i = 1; $i <= $total_row; $i++) {
			$txtpoid = "txtpoid_" . $i;
			$txtbookingid = "txtbookingid_" . $i;
			$delete_cause = str_replace("'", "", $delete_cause);
			$delete_cause = str_replace('"', '', $delete_cause);
			$delete_cause = str_replace('(', '', $delete_cause);
			$delete_cause = str_replace(')', '', $delete_cause);
			$rID1 = execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "'   where  id in (" . str_replace("'", "", $$txtbookingid) . ") and booking_no=$txt_booking_no", 0);
			$rID2 = execute_query("update wo_emb_book_con_dtls set status_active=0,is_deleted=1 where  wo_booking_dtls_id in(" . str_replace("'", "", $$txtbookingid) . ") and booking_no=$txt_booking_no", 0);
		}

		if ($db_type == 0) {
			if ($rID1 &&  $rID2) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID1 &&  $rID2) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_booking_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "show_trim_booking") {
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($garments_nature == 0) {
		$garment_nature_cond = "";
	} else {
		$garment_nature_cond = " and a.garments_nature=$garments_nature";
	}



	$condition = new condition();
	if (str_replace("'", "", $job_no) != '') {
		$condition->job_no("in('$job_no')");
	}
	$condition->init();
	$emblishment = new emblishment($condition);

	$req_qty_arr = $emblishment->getQtyArray_by_orderEmblishmentidAndGmtsitem();
	$req_amount_arr = $emblishment->getAmountArray_by_orderEmblishmentidAndGmtsitem();

	$wash = new wash($condition);
	$req_qty_arr_wash = $wash->getQtyArray_by_orderEmblishmentidAndGmtsitem();
	$req_amount_arr_wash = $wash->getAmountArray_by_orderEmblishmentidAndGmtsitem();


	$cu_booking_arr = array();

	$sql_cu_booking = sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.booking_type=11 and c.status_active=1 and c.is_deleted=0 and c.id not in($booking_id)  group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.gmt_item");
	foreach ($sql_cu_booking as $row_cu_booking) {
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('gmt_item')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	$sql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as pre_cost_emb_cost_dtls_id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id,e.item_number_id, avg(e.requirment) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.gmt_item,f.uom as uom 

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f

	where
	a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and e.item_number_id=f.gmt_item and f.booking_type=11 and f.booking_no=$txt_booking_no and f.id in($booking_id) and a.company_name=$cbo_company_name $garment_nature_cond and e.pre_cost_emb_cost_dtls_id=$pre_cost_id and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by
	a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name, c.emb_type, c.body_part_id, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id,e.item_number_id, f.id, f.sensitivity, f.delivery_date, f.gmt_item,f.uom
	order by d.id,c.id";
	$job_and_trimgroup_level = array();
	$i = 1;
	$nameArray = sql_select($sql);
	foreach ($nameArray as $infr) {
		$cbo_currency_job = $infr[csf('currency_id')];
		$exchange_rate = $infr[csf('exchange_rate')];
		if ($cbo_currency == $cbo_currency_job) {
			$exchange_rate = 1;
		}
		//echo $cbo_currency.'='.$cbo_currency_job;

		$pre_cost_emb_id = $infr[csf('pre_cost_emb_cost_dtls_id')];

		if ($infr[csf('emb_name')] == 3) {
			$req_qnty_cons_uom = $req_qty_arr_wash[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
			$req_amount_cons_uom = $req_amount_arr_wash[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
		} else {
			$req_qnty_cons_uom = $req_qty_arr[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
			$req_amount_cons_uom = $req_amount_arr[$infr[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$infr[csf('item_number_id')]];
		}
		$rate_cons_uom = $req_amount_cons_uom / $req_qnty_cons_uom;

		//$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$infr[csf('gmt_item')]][conversion_factor],5,"");
		//$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$infr[csf('gmt_item')]][conversion_factor])*$exchange_rate,5,"");
		//$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

		$cu_woq = $cu_booking_arr[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_woq'][$infr[csf('po_id')]];
		$cu_amount = $cu_booking_arr[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_amount'][$infr[csf('po_id')]];

		$bal_woq = def_number_format($req_qnty_cons_uom - $cu_woq, 5, "");
		$amount = def_number_format($rate_cons_uom * $bal_woq, 5, "");

		//$total_req_amount+=$req_amount;
		//$total_cu_amount+=$infr[csf('cu_amount')];

		if ($infr[csf('emb_name')] == 1) {
			$emb_type_name = $emblishment_print_type[$infr[csf('emb_type')]];
		}
		if ($infr[csf('emb_name')] == 2) {
			$emb_type_name = $emblishment_embroy_type[$infr[csf('emb_type')]];
		}
		if ($infr[csf('emb_name')] == 3) {
			$emb_type_name = $emblishment_wash_type[$infr[csf('emb_type')]];
		}
		if ($infr[csf('emb_name')] == 4) {
			$emb_type_name = $emblishment_spwork_type[$infr[csf('emb_type')]];
		}
		if ($infr[csf('emb_name')] == 5) {
			$emb_type_name = $emblishment_gmts_type[$infr[csf('emb_type')]];
		}


		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['job_no'][$infr[csf('po_id')]] = $infr[csf('job_no')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['po_id'][$infr[csf('po_id')]] = $infr[csf('po_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['po_number'][$infr[csf('po_id')]] = $infr[csf('po_number')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['item_number_id'][$infr[csf('po_id')]] = $infr[csf('item_number_id')];

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['country'][$infr[csf('po_id')]] = $infr[csf('country')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['body_part_id'][$infr[csf('po_id')]] = $infr[csf('body_part_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['body_part'][$infr[csf('po_id')]] = $body_part[$infr[csf('body_part_id')]];

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]][$infr[csf('item_number_id')]]['emb_type'][$infr[csf('po_id')]] = $infr[csf('emb_type')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['emb_type_name'][$infr[csf('po_id')]] = $emb_type_name;

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['emb_name'][$infr[csf('po_id')]] = $infr[csf('emb_name')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['uom'][$infr[csf('po_id')]] = $infr[csf('uom')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['emb_name_name'][$infr[csf('po_id')]] = $emblishment_name_array[$infr[csf('emb_name')]];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['pre_cost_emb_cost_dtls_id'][$infr[csf('po_id')]] = $pre_cost_emb_id;


		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['req_qnty'][$infr[csf('po_id')]] = $req_qnty_cons_uom;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['req_amount'][$infr[csf('po_id')]] = $req_amount_cons_uom;



		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_woq'][$infr[csf('po_id')]] = $cu_woq;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['cu_amount'][$infr[csf('po_id')]] = $cu_amount;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['bal_woq'][$infr[csf('po_id')]] = $bal_woq;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['exchange_rate'][$infr[csf('po_id')]] = $exchange_rate;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['rate'][$infr[csf('po_id')]] = $rate_cons_uom;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['amount'][$infr[csf('po_id')]] = $amount;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['txt_delivery_date'][$infr[csf('po_id')]] = $infr[csf('delivery_date')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['booking_id'][$infr[csf('po_id')]] = $infr[csf('booking_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_emb_id][$infr[csf('item_number_id')]]['sensitivity'][$infr[csf('po_id')]] = $infr[csf('sensitivity')];
	}
	$sql_booking = sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id,c.gmt_item, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id  and c.id in($booking_id) and c.booking_type=11 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id,c.gmt_item");
	foreach ($sql_booking as $row_booking) {
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('gmt_item')]]['woq'][$row_booking[csf('po_break_down_id')]] = $row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('gmt_item')]]['amount'][$row_booking[csf('po_break_down_id')]] = $row_booking[csf('amount')];
	}
?>

	<input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">Job No</th>
			<th width="100">Ord. No</th>
			<th width="100">Gmt. Item</th>
			<th width="100">Emb Name</th>
			<th width="150">Body Part</th>
			<th width="150">Emb Type</th>
			<th width="70">Req. Qnty</th>
			<th width="50">UOM</th>
			<th width="80">CU WOQ</th>
			<th width="80">Bal WOQ</th>
			<th width="100">Sensitivity</th>
			<th width="80">WOQ</th>
			<th width="55">Exch.Rate</th>
			<th width="80">Rate</th>
			<th width="80">Amount</th>
			<th>Delv. Date</th>
		</thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search">
		<tbody>
			<?
			if ($cbo_level == 1) {
				foreach ($nameArray as $selectResult) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$cbo_currency_job = $selectResult[csf('currency_id')];
					$exchange_rate = $selectResult[csf('exchange_rate')];
					if ($cbo_currency == $cbo_currency_job) {
						$exchange_rate = 1;
					}

					$pre_cost_emb_id = $selectResult[csf('pre_cost_emb_cost_dtls_id')];
					if ($selectResult[csf('emb_name')] == 3) {
						$req_qnty_cons_uom = $req_qty_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
						$req_amount_cons_uom = $req_amount_arr_wash[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
					} else {
						$req_qnty_cons_uom = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
						$req_amount_cons_uom = $req_amount_arr[$selectResult[csf('po_id')]][$infr[csf('pre_cost_emb_cost_dtls_id')]][$selectResult[csf('item_number_id')]];
					}
					$rate_cons_uom = $req_amount_cons_uom / $req_qnty_cons_uom;



					$cu_woq = $cu_booking_arr[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['cu_woq'][$selectResult[csf('po_id')]];
					$cu_amount = $cu_booking_arr[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['cu_amount'][$selectResult[csf('po_id')]];

					$bal_woq = def_number_format($req_qnty_cons_uom - $cu_woq, 5, "");

					$woq = $job_and_trimgroup_level[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['woq'][$selectResult[csf('po_id')]];
					$amount = $job_and_trimgroup_level[$selectResult[csf('job_no')]][$pre_cost_emb_id][$selectResult[csf('item_number_id')]]['amount'][$selectResult[csf('po_id')]];
					$rate = $amount / $woq;
					$total_amount += $amount;
					if ($selectResult[csf('emb_name')] == 1) {
						$emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
					}
					if ($selectResult[csf('emb_name')] == 2) {
						$emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
					}
					if ($selectResult[csf('emb_name')] == 3) {
						$emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
					}
					if ($selectResult[csf('emb_name')] == 4) {
						$emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
					}
					if ($selectResult[csf('emb_name')] == 5) {
						$emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];
					}

					if (!empty($selectResult[uom])) {
						$uom_id = $selectResult[uom];
					} else {
						$uom_id = 2;
					}
					
							if($cbo_currency==1) //Tk
							{
								 $rate_cal =$req_amount_cons_uom/$woq;
								 $rate =$rate_cal*$exchange_rate;
								 $req_amount_cons_uom=$req_qnty_cons_uom*$rate;
							}

			?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="80"><? echo $selectResult[csf('job_no')]; ?>
							<input type="hidden" id="txtjob_<? echo $i; ?>" value="<? echo $selectResult[csf('job_no')]; ?>" style="width:30px" class="text_boxes" readonly />
						</td>
						<td width="100"><? echo $selectResult[csf('po_number')]; ?>
							<input type="hidden" id="txtbookingid_<? echo $i; ?>" value="<? echo $selectResult[csf('booking_id')]; ?>" readonly />
							<input type="hidden" id="txtpoid_<? echo $i; ?>" value="<? echo $selectResult[csf('po_id')]; ?>" readonly />
							<input type="hidden" id="txtcountry_<? echo $i; ?>" value="<? echo $selectResult[csf('country')] ?>" readonly />
						</td>
						<td width="100">
							<? echo $garments_item[$selectResult[csf('item_number_id')]]; ?>
							<input type="hidden" id="txtgmtitemid_<? echo $i; ?>" value="<? echo $selectResult[csf('item_number_id')]; ?>" readonly />
						</td>
						<td width="100">
							<? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?>
							<input type="hidden" id="txtembcostid_<? echo $i; ?>" value="<? echo $selectResult[csf('pre_cost_emb_cost_dtls_id')]; ?>" readonly />
							<input type="hidden" id="emb_name_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_name')]; ?>" readonly />
						</td>
						<td width="150">
							<? echo $body_part[$selectResult[csf('body_part_id')]]; ?>
							<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i; ?>" value="<? echo $selectResult[csf('body_part_id')]; ?>" />
						</td>
						<td width="150">
							<? echo $emb_type_name; ?>
							<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i; ?>" value="<? echo $selectResult[csf('emb_type')]; ?>" />
						</td>
						<td width="70" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i; ?>" value="<? echo number_format($req_qnty_cons_uom, 4, '.', ''); ?>" readonly />

							<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i; ?>" value="<? echo number_format($req_amount_cons_uom, 4, '.', ''); ?>" readonly />
						</td>
						<td width="50">
							<? echo $unit_of_measurement[$uom_id] ?>
							<? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];
							?>
							<input type="hidden" id="txtuom_<? echo $i; ?>" value="<? echo $uom_id; ?>" readonly />
						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i; ?>" value="<? echo number_format($cu_woq, 4, '.', ''); ?>" readonly />
							<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i; ?>" value="<? echo number_format($cu_amount, 4, '.', ''); ?>" readonly />
						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" readonly />
						</td>
						<td width="100" align="right"><? echo create_drop_down("cbocolorsizesensitive_" . $i, 100, $size_color_sensitive, "", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)", 1, "1,2,3,4"); ?>
						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i; ?>" value="<? echo number_format($woq, 4, '.', ''); ?>" onClick="open_consumption_popup('requires/multi_job_additional_print_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i; ?>',<? echo $i; ?>)" readonly />
						</td>
						<td width="55" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i; ?>" value="<? echo $exchange_rate; ?>" readonly />
						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i; ?>" value="<? echo number_format($rate, 4, '.', ''); ?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

							<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" readonly />
						</td>
						<td width="80" align="right">
							<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i; ?>" value="<? echo number_format($amount, 4, '.', ''); ?>" readonly />
						</td>
						<td width="" align="right">
							<input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i; ?>" class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')], "dd-mm-yyyy", "-"); ?>" readonly <? if ($disAbled) {
																																																																																				echo "disabled";
																																																																																			} else {
																																																																																				echo "";
																																																																																			} ?> />
							<input type="hidden" id="consbreckdown_<? echo $i; ?>" value="" />
							<input type="hidden" id="jsondata_<? echo $i; ?>" value="" />
						</td>
					</tr>
					<?
					$i++;
				}
			}
			if ($cbo_level == 2) {
				$i = 1;
				foreach ($job_and_trimgroup_level as $job_no) {
					foreach ($job_no as $wo_pre_cost_trim_cost_dtlsArr) {
						foreach ($wo_pre_cost_trim_cost_dtlsArr as $gmtItem => $wo_pre_cost_trim_cost_dtls) {
							$job_no = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
							$po_number = implode(",", $wo_pre_cost_trim_cost_dtls['po_number']);
							$po_id = implode(",", $wo_pre_cost_trim_cost_dtls['po_id']);
							$item_number_id = $gmtItem; //implode(",",$wo_pre_cost_trim_cost_dtls['item_number_id']);
							$country = implode(",", array_unique(explode(",", implode(",", $wo_pre_cost_trim_cost_dtls['country']))));

							$body_part_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['body_part_id']));
							$body_part = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['body_part']));
							$emb_type = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_type']));
							$emb_type_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_type_name']));

							$pre_cost_emb_cost_dtls_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['pre_cost_emb_cost_dtls_id']));
							$emb_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_name']));
							$emb_name_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_name_name']));

							$uom = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['uom']));
							$booking_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
							$sensitivity = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
							$delivery_date = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));

							$req_qnty_cons_uom = array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
							$rate_cons_uom = array_sum($wo_pre_cost_trim_cost_dtls['req_amount']) / array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
							$req_amount_cons_uom = array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);


							$bal_woq = array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
							$cu_woq = array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
							$cu_amount = array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);

							$woq = array_sum($wo_pre_cost_trim_cost_dtls['woq']);
							$amount = array_sum($wo_pre_cost_trim_cost_dtls['amount']);
							$rate = $amount / $woq;
							
							
							$total_amount += $amount;
							if (empty($uom)) {
								$uom = 2;
							}
							
							if($cbo_currency==1) //Tk
							{
								 $rate_cal =$req_amount_cons_uom/$woq;
								 $rate =$rate_cal*$exchange_rate;
								//$amount=$woq*$rate;
								 $req_amount_cons_uom=$req_qnty_cons_uom*$rate;
							}
							//echo $req_amount_cons_uom.'ff';


					?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="80"><? echo $job_no ?><input type="hidden" id="txtjob_<? echo $i; ?>" value="<? echo $job_no; ?>" style="width:30px" class="text_boxes" readonly /></td>
								<td width="100" style="word-wrap:break-word;word-break: break-all"><? echo $po_number; ?>
									<input type="hidden" id="txtbookingid_<? echo $i; ?>" value="<? echo $booking_id; ?>" readonly />
									<input type="hidden" id="txtpoid_<? echo $i; ?>" value="<? echo $po_id; ?>" readonly />
									<input type="hidden" id="txtcountry_<? echo $i; ?>" value="<? echo $country; ?>" readonly />
								</td>
								<td width="100">
									<? echo $garments_item[$item_number_id]; ?>
									<input type="hidden" id="txtgmtitemid_<? echo $i; ?>" value="<? echo $item_number_id; ?>" readonly />
								</td>
								<td width="100">
									<? echo $emb_name_name; ?>
									<input type="hidden" id="txtembcostid_<? echo $i; ?>" value="<? echo $pre_cost_emb_cost_dtls_id; ?>" readonly />
									<input type="hidden" id="emb_name_<? echo $i; ?>" value="<? echo $emb_name; ?>" readonly />
								</td>
								<td width="150">
									<? echo $body_part; ?>
									<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="body_part_id_<? echo $i; ?>" value="<? echo $body_part_id; ?>" />
								</td>
								<td width="150">
									<? echo $emb_type_name; ?>
									<input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="emb_type_<? echo $i; ?>" value="<? echo $emb_type; ?>" />
								</td>
								<td width="70" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i; ?>" value="<? echo number_format($req_qnty_cons_uom, 4, '.', ''); ?>" readonly />
									<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i; ?>" value="<? echo number_format($req_amount_cons_uom, 4, '.', ''); ?>" readonly />
								</td>
								<td width="50">

									<? echo $unit_of_measurement[$uom]; ?>
									<input type="hidden" id="txtuom_<? echo $i; ?>" value="<? echo $uom; ?>" readonly />
								</td>
								<td width="80" align="right">
									<input type="text" style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i; ?>" value="<? echo number_format($cu_woq, 4, '.', ''); ?>" readonly />
									<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i; ?>" value="<? echo $cu_amount; ?>" readonly />
								</td>
								<td width="80" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i; ?>" value="<? echo number_format($bal_woq, 4, '.', ''); ?>" readonly />
								</td>
								<td width="100" align="right"><? echo create_drop_down("cbocolorsizesensitive_" . $i, 100, $size_color_sensitive, "", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)", 1, "1,2,3,4"); ?>
								</td>
								<td width="80" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i; ?>" value="<? echo number_format($woq, 4, '.', '');$tot_woq+=$woq; ?>" onClick="open_consumption_popup('requires/multi_job_additional_print_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i; ?>',<? echo $i; ?>)" readonly />
								</td>
								<td width="55" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i; ?>" value="<? echo $exchange_rate; ?>" readonly />
								</td>
								<td width="80" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i; ?>" value="<? echo number_format($rate, 4, '.', ''); ?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />
									<input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i; ?>" value="<? echo $rate_cons_uom; ?>" readonly />
								</td>
								<td width="80" align="right">
									<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i; ?>" value="<? echo number_format($amount, 4, '.', ''); $tot_amount+=$amount; ?>" readonly />
								</td>
								<td width="" align="right">
									<input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i; ?>" class="datepicker" value="<? echo change_date_format($delivery_date, "dd-mm-yyyy", "-"); ?>" readonly <? if ($disAbled) {
																																																																																echo "disabled";
																																																																															} else {
																																																																																echo "";
																																																																															} ?> />
									<input type="hidden" id="consbreckdown_<? echo $i; ?>" value="" />
									<input type="hidden" id="jsondata_<? echo $i; ?>" value="" />
								</td>
							</tr>
			<?
							$i++;
						}
					}
				}
			}
			?>
		</tbody>
	</table>
	<table width="1500" class="rpt_table" border="0" rules="all">
		<tfoot>
			<tr>
				<th width="40">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="150">&nbsp;</th>
				<th width="150">&nbsp;</th>
				<th width="70"><? echo $tot_req_qty; ?></th>
				<th width="50">&nbsp;</th>
				<th width="80"><? echo $tot_cu_woq; ?></th>
				<th width="80"><? echo $tot_bal_woq; ?></th>
				<th width="100"><b></b></th>
				<th width="80"><? //echo number_format($tot_woq, 4, '.', '');?></th>
				<th width="55">&nbsp;</th>
				<th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount, 4, '.', ''); ?>" class="text_boxes_numeric" style="width:140px" /></th>
				<th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount, 4, '.', ''); ?>" class="text_boxes_numeric" style="width:140px" /><? //echo number_format($tot_amount, 4, '.', '');?></th>
				<th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly /></th>
			</tr>
		</tfoot>
	</table>
	<table width="1100" colspan="14" cellspacing="0" class="" border="0">
		<tr>
			<td align="center" class="button_container">
				<? echo load_submit_buttons($permission, "fnc_trims_booking_dtls", 1, 0, "reset_form('','booking_list_view','','','')", 2); ?>
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "show_trim_booking_list") {
	extract($_REQUEST);

	if ($garments_nature == 0) {
		$garment_nature_cond = "";
	} else {
		$garment_nature_cond = " and a.garments_nature=$garments_nature";
	}	
	$sql = "SELECT a.job_no_prefix_num, a.job_no,a.order_uom, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_embe_cost_dtls, c.emb_name, c.body_part_id , c.emb_type, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.requirment) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity,f.gmt_item, f.delivery_date, f.description as description,f.uom as uom

	from
	wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f

	where
	a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=11 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num, a.job_no,a.order_uom, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.emb_name, c.body_part_id, c.emb_type, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity,f.gmt_item, f.delivery_date, f.description,f.uom
	order by d.id, c.id";

	$job_and_trimgroup_level = array();
	$i = 1;
	$nameArray = sql_select($sql);
	foreach ($nameArray as $selectResult) {
		$cbo_currency_job = $selectResult[csf('currency_id')];
		$exchange_rate = $selectResult[csf('exchange_rate')];
		if ($cbo_currency == $cbo_currency_job) $exchange_rate = 1;

		if ($selectResult[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
		if ($selectResult[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];



		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['order_uom'][$selectResult[csf('po_id')]] = $unit_of_measurement[$selectResult[csf('order_uom')]];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['job_no'][$selectResult[csf('po_id')]] = $selectResult[csf('job_no')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['po_id'][$selectResult[csf('po_id')]] = $selectResult[csf('po_id')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['po_number'][$selectResult[csf('po_id')]] = $selectResult[csf('po_number')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['country'][$selectResult[csf('po_id')]] = $selectResult[csf('country')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['gmt_item'][$selectResult[csf('po_id')]] = $selectResult[csf('gmt_item')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['gmt_item_name'][$selectResult[csf('po_id')]] = $garments_item[$selectResult[csf('gmt_item')]];


		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_name'][$selectResult[csf('po_id')]] = $selectResult[csf('emb_name')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_name_name'][$selectResult[csf('po_id')]] = $emblishment_name_array[$selectResult[csf('emb_name')]];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_type'][$selectResult[csf('po_id')]] = $selectResult[csf('emb_type')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['uom'][$selectResult[csf('po_id')]] = $selectResult[csf('uom')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_type_name'][$selectResult[csf('po_id')]] = $emb_type_name;

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['body_part_id'][$selectResult[csf('po_id')]] = $selectResult[csf('body_part_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['body_part_name'][$selectResult[csf('po_id')]] = $body_part[$selectResult[csf('body_part_id')]];
		//$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['emb_name_name'][$selectResult[csf('po_id')]]=$gmt_item_library[$selectResult[csf('gmt_item')]];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['wo_pre_cost_embe_cost_dtls'][$selectResult[csf('po_id')]] = $selectResult[csf('wo_pre_cost_embe_cost_dtls')];

		//$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom];

		//$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]];


		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['cu_woq'][$selectResult[csf('po_id')]] = $cu_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['cu_amount'][$selectResult[csf('po_id')]] = $cu_amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['bal_woq'][$selectResult[csf('po_id')]] = $bal_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['exchange_rate'][$selectResult[csf('po_id')]] = $exchange_rate;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['rate'][$selectResult[csf('po_id')]] = $rate_ord_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['txt_delivery_date'][$selectResult[csf('po_id')]] = $selectResult[csf('delivery_date')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['booking_id'][$selectResult[csf('po_id')]] = $selectResult[csf('booking_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['sensitivity'][$selectResult[csf('po_id')]] = $selectResult[csf('sensitivity')];
	}

	$sql_booking = sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity, c.gmt_item,c.brand_supplier,c.wo_qnty as wo_qnty, c.amount as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.booking_type=11 and c.status_active=1 and c.is_deleted=0");
	foreach ($sql_booking as $row_booking) {
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('gmt_item')]]['woq'][$row_booking[csf('po_break_down_id')]] += $row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('gmt_item')]]['amount'][$row_booking[csf('po_break_down_id')]] += $row_booking[csf('amount')];

		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('gmt_item')]]['gmt_item'][$row_booking[csf('po_break_down_id')]] = $row_booking[csf('gmt_item')];
	}
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Job No</th>
			<th width="100">Ord. No</th>
			<th width="100">Gmts. Item</th>
			<th width="100">Emb Name</th>
			<th width="150">Emb Type</th>
			<th width="150">Body Part</th>
			<th width="80">UOM</th>
			<th width="100">Sensitivity</th>
			<th width="80">WOQ</th>
			<th width="80">Exch.Rate</th>
			<th width="80">Rate</th>
			<th width="80">Amount</th>
			<th width="">Delv. Date</th>
		</thead>
		<tbody id="save_list">
			<?
			if ($cbo_level == 1) {
				foreach ($nameArray as $selectResult) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$cbo_currency_job = $selectResult[csf('currency_id')];
					$exchange_rate = $selectResult[csf('exchange_rate')];
					if ($cbo_currency == $cbo_currency_job) $exchange_rate = 1;

					if ($selectResult[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
					if ($selectResult[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$selectResult[csf('emb_type')]];

					$woq = def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['woq'][$selectResult[csf('po_id')]], 5, "");
					$amount = def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('gmt_item')]]['amount'][$selectResult[csf('po_id')]], 5, "");
					$rate = def_number_format($amount / $woq, 5, "");
					$total_amount += $amount;
					$uom_name = '';
					$uom_id = 0;
					if ($selectResult[csf('uom')] == 2) {
						$uom_name = 'Pcs';
						$uom_id = 1;
					} else {
						$uom_name = 'Dzn';
						$uom_id = 2;
					}

			?>
					<tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?= $i; ?>" onClick="fnc_show_booking(<?= $selectResult[csf('wo_pre_cost_embe_cost_dtls')]; ?>,'<?= $selectResult[csf('po_id')]; ?>','<?= $selectResult[csf('booking_id')]; ?>','<?= $selectResult[csf('job_no')]; ?>')">
						<td width="40"><? echo $i; ?></td>
						<td width="100"><? echo $selectResult[csf('job_no')]; ?></td>
						<td width="100"><? echo $selectResult[csf('po_number')]; ?></td>
						<td width="100"><? echo $garments_item[$selectResult[csf('gmt_item')]]; ?> </td>
						<td width="100"><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?> </td>
						<td width="150"><? echo $emb_type_name; ?></td>
						<td width="150"><? echo $body_part[$selectResult[csf('body_part_id')]]; ?></td>
						<td width="80"><?= $uom_name ?><? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('gmt_item')]][cons_uom]]; 
														?></td>
						<td width="100" align="right"><? echo $size_color_sensitive[$selectResult[csf("sensitivity")]]; ?></td>
						<td width="80" align="right"><? echo number_format($woq, 4, '.', '');$tot_woq+=$woq; ?></td>
						<td width="80" align="right"><? echo $exchange_rate; ?></td>
						<td width="80" align="right"><? echo number_format($rate, 4, '.', ''); ?></td>
						<td width="80" align="right"><? echo number_format($amount, 4, '.', '');$tot_amount+=$amount; ?></td>
						<td width="" align="right"><? echo change_date_format($selectResult[csf('delivery_date')], "dd-mm-yyyy", "-"); ?></td>
					</tr>
					<?
					$i++;
				}
			}

			if ($cbo_level == 2) {
				$i = 1;
				foreach ($job_and_trimgroup_level as $job_no) {
					foreach ($job_no as $sen) {
						foreach ($sen as $gmtItemId) {
							//foreach ($desc as $brandsup){
							foreach ($gmtItemId as $wo_pre_cost_trim_cost_dtls) {
								$job_no = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
								$po_number = implode(",", $wo_pre_cost_trim_cost_dtls['po_number']);
								$po_id = implode(",", $wo_pre_cost_trim_cost_dtls['po_id']);
								//$gmtItemName=$garments_item[implode(",",$wo_pre_cost_trim_cost_dtls['gmt_item'])];
								$gmtItemName = implode(",", array_unique(explode(",", implode(",", $wo_pre_cost_trim_cost_dtls['gmt_item_name']))));

								$country = implode(",", array_unique(explode(",", implode(",", $wo_pre_cost_trim_cost_dtls['country']))));
								$wo_pre_cost_emb_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_embe_cost_dtls']));
								$emb_name_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_name']));
								$emb_name_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_name_name']));
								$emb_type_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_type']));
								$embTypeName = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['emb_type_name']));
								$body_part_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['body_part_id']));
								$body_part_name = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['body_part_name']));
								$order_uom_array = array_unique($wo_pre_cost_trim_cost_dtls['uom']);

								//$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
								$booking_id = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
								$sensitivity = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
								$delivery_date = implode(",", array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));
								$woq = def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['woq']), 5, "");
								$amount = def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['amount']), 5, "");
								$rate = def_number_format($amount / $woq, 5, "");
								$total_amount += $amount;
								$uom_name = '';
								$uom_id = 0;
								foreach ($order_uom_array as $key => $uom_u) {
									
									$uom_name=$unit_of_measurement[$uom_u];
									if ($uom_u == 1) {
										//$uom_name = 'Pcs';
										$uom_id = 1;
									} else {
										//$uom_name = 'Dzn';
										$uom_id = 2;
									}
								}

					?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="fnc_show_booking(<? echo $wo_pre_cost_emb_id; ?>,'<? echo $po_id; ?>','<? echo $booking_id; ?>','<? echo $job_no; ?>')">
									<td width="40"><? echo $i; ?></td>
									<td width="100"><? echo $job_no ?></td>
									<td width="100" style="word-wrap:break-word;word-break: break-all"><? echo $po_number; ?></td>
									<td width="100"><? echo $gmtItemName; ?></td>
									<td width="100"><? echo $emb_name_name; ?></td>
									<td width="150"><? echo $embTypeName; ?></td>
									<td width="150"><? echo $body_part_name; ?></td>
									<td width="80"><? echo $uom_name; ?></td>
									<td width="100" align="right"><? echo $size_color_sensitive[$sensitivity]; ?></td>
									<td width="80" align="right"><? echo number_format($woq, 4, '.', '');$tot_woq+=$woq; ?></td>
									<td width="80" align="right"><? echo $exchange_rate; ?></td>
									<td width="80" align="right"><? echo number_format($rate, 4, '.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($amount, 4, '.', '');$tot_amount+=$amount; ?></td>
									<td width="" align="right"><? echo change_date_format($delivery_date, "dd-mm-yyyy", "-"); ?></td>
								</tr>
			<?
								$i++;
							}
							//}
						}
					}
				}
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9" align="right"><b>Grand Total</b></td>
				<td width="80" align="right"><? echo number_format($tot_woq, 4, '.', ''); ?></td>
				<td width="80" align="right">&nbsp;</td>
				<td width="80" align="right">&nbsp;</td>
				<td width="80" align="right"><? echo number_format($tot_amount, 4, '.', ''); ?></td>
				<td width="" align="right">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
<?
	exit();
}

if ($action == "fabric_booking_popup") {
	echo load_html_head_contents("Booking Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(booking_no) {
			document.getElementById('selected_booking').value = booking_no;
			parent.emailwindow.hide();
		}

		function check_orphan(str) {
			if ($("#chk_orphan").prop('checked') == true)

				$('#chk_orphan').val(1);

			else

				$('#chk_orphan').val(0);

		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<th colspan="8">
							<?
							echo create_drop_down("cbo_search_category", 110, $string_search_type, '', 1, "-- Search Catagory --");
							?>
						</th>
					</thead>
					<thead>
						<th width="150">Company Name</th>
						<th width="150">Buyer Name</th>
						<th width="100">Booking No</th>
						<th width="100">Job No</th>
						<th width="100">Internal Ref. No</th>
						<th width="130" colspan="2">Date Range</th>
						<th> <input type="checkbox" id="chk_orphan" onClick="check_orphan(this.value)" value="0"> Orphan WO</th>
					</thead>
					<tr class="general">
						<td> <input type="hidden" id="selected_booking">
							<?
							echo create_drop_down("cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'multi_job_additional_print_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
						</td>

						<td id="buyer_td">
							<?
							echo create_drop_down("cbo_buyer_name", 172, $blank_array, "", 1, "-- Select Buyer --");
							?>
						</td>
						<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
						<td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px"></td>
						<td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px"></td>

						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
						<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_orphan').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_booking_search_list_view', 'search_div', 'multi_job_additional_print_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="8" align="center" valign="middle"> <? echo load_month_buttons(1);  ?></td>
					</tr>
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

if ($action == "create_booking_search_list_view") {
	$data = explode('_', $data);
	if ($data[0] != 0) $company = "  a.company_id='$data[0]'";
	else {
		echo "Please Select Company First.";
		die;
	}
	if ($data[1] != 0) $buyer = " and a.buyer_id='$data[1]'";
	else $buyer = ""; //{ echo "Please Select Buyer First."; die; }
	if ($db_type == 0) {
		$booking_year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		if ($data[2] != "" &&  $data[3] != "") $booking_date  = "and a.booking_date  between '" . change_date_format($data[2], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[3], "yyyy-mm-dd", "-") . "'";
		else $booking_date = "";
	}
	if ($db_type == 2) {
		$booking_year_cond = " and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2] != "" &&  $data[3] != "") $booking_date  = "and a.booking_date  between '" . change_date_format($data[2], "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($data[3], "yyyy-mm-dd", "-", 1) . "'";
		else $booking_date = "";
	}
	if ($data[6] == 4 || $data[6] == 0) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  ";
		else $booking_cond = "";
	}
	if ($data[6] == 1) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num ='$data[5]' ";
		else $booking_cond = "";
	}
	if ($data[6] == 2) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  ";
		else $booking_cond = "";
	}
	if ($data[6] == 3) {
		if (str_replace("'", "", $data[5]) != "") $booking_cond = " and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  ";
		else $booking_cond = "";
	}
	$approved = array(0 => "No", 1 => "Yes");
	$is_ready = array(0 => "No", 1 => "Yes", 2 => "No");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$suplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
	$po_sql = sql_select("select a.job_no_prefix_num,b.id, b.po_number,b.job_no_mst from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst");
	foreach ($po_sql as $row) {
		$po_array[$row[csf('id')]]['po_no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_prefix_num')];
	}

	if (trim($data[9]) != "") $ref_cond = " and d.grouping like '%$data[9]'";
	else $ref_cond = "";
	if (trim($data[8]) != "") $job_cond = " and b.job_no like '%$data[8]%'";
	else $job_cond = "";
	$arr = array(2 => $buyer_arr, 3 => $po_num, 5 => $po_array, 6 => $garments_item, 7 => $emblishment_name_array, 8 => $suplier, 9 => $approved, 10 => $is_ready);
	if ($data[7] == 0)
		$sql = "select a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.pay_mode,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_po_break_down d where $company $buyer $booking_date $booking_cond and d.job_no_mst=b.job_no and d.id=b.po_break_down_id  and  b.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.booking_type=11 and a.entry_form=612 and  a.status_active=1  and 	a.is_deleted=0 and  b.status_active=1  and 	b.is_deleted=0 $job_cond $ref_cond group by a.id, a.pay_mode,a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping order by a.id DESC";
	else

		$sql = "select a.id, a.booking_no_prefix_num,  a.pay_mode,a.booking_no,a.booking_date,company_id,a.buyer_id,a.supplier_id,a.is_approved,a.ready_to_approved,'' as po_break_down_id,'' as gmt_item,'' as grouping  from wo_booking_mst a where $company $buyer $booking_date $booking_cond and NOT EXISTS (SELECT booking_no FROM wo_booking_dtls b WHERE a.booking_no=b.booking_no and b.status_active=1) and a.booking_type=11 and a.entry_form=612 and a.status_active=1 and a.is_deleted=0 $job_cond group by a.id, a.pay_mode,a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.supplier_id,a.is_approved,a.ready_to_approved order by a.id DESC";
	$result = sql_select($sql);

	// echo  create_list_view("list_view", "Booking No,Booking Date,Buyer,Job No.,Ref. No,PO number,Gmts Item,Embl Name,Supplier,Approved,Is-Ready", "60,65,70,60,100,100,110,80,110,50","970","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,buyer_id,job_no,0,po_break_down_id,gmt_item,emb_name,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,buyer_id,job_no,grouping,po_break_down_id,gmt_item,emb_name,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0','','');
?>
	<br>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="1020" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th width="30">SL No</th>
				<th width="70">Booking No</th>
				<th width="70">Booking Date</th>
				<th width="100">Buyer</th>
				<th width="110">Job No.</th>
				<th width="100">PO Number</th>
				<th width="70">Ref. No</th>
				<th width="120">Gmts Item</th>
				<th width="100">Embl Name</th>
				<th width="100">Supplier</th>
				<th width="70">Approved</th>
				<th width="">Is-Ready</th>
			</tr>
		</thead>
	</table>
	<div style="max-height:320px; width:1020px; overflow-y:scroll" id="">
		<table class="rpt_table" id="list_view" rules="all" width="1000" height="" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<?
				$i = 0;
				foreach ($result as $row) {
					$i++;
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					$suplier_name = "";
					if ($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) $suplier_name = $comp[$row[csf('supplier_id')]];
					else $suplier_name = $suplier[$row[csf('supplier_id')]];
				?>
					<tr onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="70">
							<p><? echo $row[csf('booking_no_prefix_num')]; ?></p>
						</td>
						<td width="70">
							<p><? echo change_date_format($row[csf('booking_date')]); ?></p>
						</td>

						<td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
						<td width="110"><? echo $po_array[$row[csf('po_break_down_id')]]['job_no']; ?></td>
						<td width="100" style="word-break:break-all"><? echo $po_array[$row[csf('po_break_down_id')]]['po_no']; ?></td>
						<td width="70" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
						<td width="120">
							<p><? echo $garments_item[$row[csf('gmt_item')]]; ?></p>
						</td>
						<td width="100">
							<p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?></p>
						</td>
						<td width="100" style="word-break:break-all"><? echo $suplier_name; ?></td>
						<td width="70">
							<p><? echo $yes_no[$row[csf('is_approved')]]; ?></p>
						</td>
						<td width="">
							<p><? echo $is_ready[$row[csf('ready_to_approved')]]; ?></p>
						</td>
					</tr>
				<?
				}
				?>
			</tbody>
		</table>
	</div>
<?
	exit();
}

if ($action == "populate_data_from_search_popup") {
	$sql = "select booking_no,booking_date,company_id,buyer_id,currency_id,exchange_rate,is_short,pay_mode,booking_month,supplier_id,attention,tenor,delivery_date,source,booking_year,is_approved,ready_to_approved,cbo_level,is_short,remarks,id,delivery_to from wo_booking_mst  where booking_no='$data' and  status_active=1 and is_deleted=0";

	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$supplier_library = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");

		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value = '" . $row[csf("buyer_id")] . "';\n";
		echo "document.getElementById('txt_booking_no').value = '" . $row[csf("booking_no")] . "';\n";
		echo "document.getElementById('booking_mst_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_currency').value = '" . $row[csf("currency_id")] . "';\n";
		echo "document.getElementById('cbo_isshort').value = '" . $row[csf("is_short")] . "';\n";
		echo "document.getElementById('cbo_pay_mode').value = '" . $row[csf("pay_mode")] . "';\n";
		echo "document.getElementById('txt_booking_date').value = '" . change_date_format($row[csf("booking_date")], 'dd-mm-yyyy', '-') . "';\n";
		echo "document.getElementById('hidden_supplier_id').value = '" . $row[csf("supplier_id")] . "';\n";
		if ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) {

			echo "document.getElementById('cbo_supplier_name').value = '" . $company_library[$row[csf("supplier_id")]] . "';\n";
			echo "document.getElementById('hidden_supplier_name').value = '" . $company_library[$row[csf("supplier_id")]] . "';\n";
		} else {
			echo "document.getElementById('cbo_supplier_name').value = '" . $supplier_library[$row[csf("supplier_id")]] . "';\n";
			echo "document.getElementById('hidden_supplier_name').value = '" . $supplier_library[$row[csf("supplier_id")]] . "';\n";
		}
		echo "document.getElementById('txt_attention').value = '" . $row[csf("attention")] . "';\n";
		echo "document.getElementById('txt_tenor').value = '" . $row[csf("tenor")] . "';\n";
		echo "document.getElementById('txt_delivery_date').value = '" . change_date_format($row[csf("delivery_date")], 'dd-mm-yyyy', '-') . "';\n";
		echo "document.getElementById('cbo_source').value = '" . $row[csf("source")] . "';\n";
		echo "document.getElementById('id_approved_id').value = '" . $row[csf("is_approved")] . "';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '" . $row[csf("ready_to_approved")] . "';\n";
		echo "document.getElementById('cbo_level').value = '" . $row[csf("cbo_level")] . "';\n";
		echo "document.getElementById('remarks').value = '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_delivery_to').value = '" . $row[csf("delivery_to")] . "';\n";
		echo "load_print_button();\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_currency').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_show_booking_list();\n";
		echo "$('#print_booking1').hide();";
		$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $row[csf("company_id")] . "' and module_id=2 and report_id=89 and is_deleted=0 and status_active=1");
		foreach (explode(',', $print_report_format) as $button_id) {
			if ($button_id == 13) {
				echo "$('#print_booking1').show();";
			}
		}
		if ($row[csf("is_approved")] == 1) {
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		} else {
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	}
}

if ($action == "show_trim_booking_report2") {
	extract($_REQUEST);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$id_approved_id = str_replace("'", "", $id_approved_id);
	$report_type = str_replace("'", "", $report_type);
	$show_comment = str_replace("'", "", $show_comment);
	$cbo_template_id = str_replace("'", "", $cbo_template_id);

	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library = return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr = return_library_array("select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$country_arr = return_library_array("select id,country_name from   lib_country", 'id', 'country_name');
	$supplier_name_arr = return_library_array("select id,supplier_name from   lib_supplier", 'id', 'supplier_name');
	$supplier_address_arr = return_library_array("select id,address_1 from   lib_supplier", 'id', 'address_1');
	$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$order_uom_arr = return_library_array("select id,order_uom  from lib_item_group", "id", "order_uom");
	$deling_marcent_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$season_arr = return_library_array("select id,season_name from lib_buyer_season", "id", "season_name");
	$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row) = $nameArray_approved;
	$booking_grand_total = 0;
	$currency_id = "";
	$buyer_string = array();
	$style_owner = array();
	$job_no = array();
	$style_ref = array();
	$all_dealing_marcent = array();
	$season = array();
	$order_repeat_no = array();
	$po_id_arr = array();

	$nameArray_buyer = sql_select("select  a.style_ref_no, a.job_no,a.order_uom, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
	foreach ($nameArray_buyer as $result_buy) {
		$buyer_string[$result_buy[csf('buyer_name')]] = $buyer_name_arr[$result_buy[csf('buyer_name')]];
		$style_owner[$result_buy[csf('job_no')]] = $company_library[$result_buy[csf('style_owner')]];
		$job_no[$result_buy[csf('job_no')]] = $result_buy[csf('job_no')];
		$job_uom_no[$result_buy[csf('job_no')]] = $unit_of_measurement[$result_buy[csf('order_uom')]];
		$style_ref[$result_buy[csf('job_no')]] = $result_buy[csf('style_ref_no')];
		$all_dealing_marcent[$result_buy[csf('job_no')]] = $deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
		$season_matrix = $result_buy[csf('season_matrix')];
		$season_buyer_wise = $result_buy[csf('season_buyer_wise')];
		if ($season_matrix != 0 && $season_buyer_wise == 0) {
			$season_matrix_con = $season_matrix;
		} else if ($season_buyer_wise != 0 && $season_matrix == 0) {
			$season_matrix_con = $season_buyer_wise;
		}
		$seasons_name .= $season_arr[$season_matrix_con] . ',';
		$order_rept_no .= $result_buy[csf('order_repeat_no')] . ',';
		$order_repeat_no[$result_buy[csf('order_repeat_no')]] = $result_buy[csf('order_repeat_no')];

		$po_id_arr[$result_buy[csf('po_break_down_id')]] = $result_buy[csf('po_break_down_id')];
	}
	$style_sting = implode(",", array_unique($style_ref));
	$job_no = implode(",", $job_no);
	$seasons_names = rtrim($seasons_name, ',');

	$seasons_names = implode(",", array_unique(explode(",", $seasons_names)));
	$poid_arr = array_unique($po_id_arr);

	$order_rept_no = rtrim($order_rept_no, ',');
	$order_rept_no = implode(",", array_unique(explode(",", $order_rept_no)));

	$po_no = array();
	$file_no = array();
	$ref_no = array();
	$po_quantity = array();
	$pub_shipment_date = '';
	$int_ref_no = '';
	$tot_po_quantity = 0;
	$po_idss = '';
	$nameArray_job = sql_select("select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
	foreach ($nameArray_job as $result_job) {
		$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]] = $result_job[csf('po_number')];
		$file_no[$result_job[csf('id')]] = $result_job[csf('file_no')];
		$ref_no[$result_job[csf('id')]] = $result_job[csf('grouping')];
		$po_quantity[$result_job[csf('id')]] = $result_job[csf('po_quantity')];
		$job_ref_no[$result_job[csf('job_no_mst')]] .= $result_job[csf('grouping')] . ',';
		$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'] .= $result_job[csf('id')] . ',';
		$pub_shipment_date .= $result_job[csf('pub_shipment_date')] . ',';
		$int_ref_no .= $result_job[csf('grouping')] . ',';
		if ($po_idss == '') $po_idss = $result_job[csf('id')];
		else $po_idss .= "," . $result_job[csf('id')];
		$job_nos .= "'" . $result_job[csf('job_no_mst')] . "'" . ',';
	}
	$job_nos = rtrim($job_nos, ",");
	$sql_job = sql_select("select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(" . $po_idss . ") ");
	foreach ($sql_job as $row) {
		$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]] += $row[csf('po_quantity')];
		$tot_po_quantity += $row[csf('po_quantity')];
	}

	$nameArray = sql_select("select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
	foreach ($nameArray as $row) {
		$varcode_booking_no = $row[csf('booking_no')];
		$booking_date = $row[csf('booking_date')];
		$delivery_date = $row[csf('delivery_date')];
		$pay_mode_id = $row[csf('pay_mode')];
		$supplier_id = $row[csf('supplier_id')];
		$currency_id = $row[csf('currency_id')];
		$buyer_id = $row[csf('buyer_id')];
		$exchange_rate = $row[csf('exchange_rate')];
		$attention = $row[csf('attention')];
		$remarks = $row[csf('remarks')];
		$revised_no = $row[csf('revised_no')];
		$source_id = $row[csf('source')];
	}
	?>
	<html>
	<div style="width:1333px" align="center">
		<table width="1333px" cellpadding="0" cellspacing="0" style="border:0px solid black">
			<table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
				<tr>
					<td width="20px">
						<table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
							<tr>
								<td width="50">
									<?
									if ($report_type == 1) {
										if ($link == 1) {
									?><img src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' /><?
																															} else {
																																?><img src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' /><?
																															}
																														} else {
																															?><img src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' /><?
																														}
																												?>
								</td>
								<td width="40px" align="center">
									&nbsp; &nbsp; &nbsp;
								</td>
								<td width="30px" align="center">
									<b style="font-size:25px;"> <?
																echo $company_library[$cbo_company_name]; ?>
									</b>
									<br>
									<label>
										<?
										$nameArray = sql_select("select id,plot_no,level_no,road_no,block_no,bin_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
										foreach ($nameArray as $result) {
											if ($result[csf('plot_no')] != '') echo $result[csf('plot_no')];
											else echo ''; ?> &nbsp;
											<? echo $result[csf('level_no')]; ?> &nbsp;
											<? echo $result[csf('road_no')]; ?> &nbsp;
											<? echo $result[csf('block_no')]; ?> &nbsp;
											<? echo $result[csf('city')]; ?> &nbsp;
											<? echo $result[csf('zip_code')]; ?> &nbsp;
											<?php echo $result[csf('province')]; ?> &nbsp;
											<? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br />
											<? echo $result[csf('email')]; ?> &nbsp;
										<? echo $result[csf('website')];
											if ($result[csf('plot_no')] != '') $plot_no = $result[csf('plot_no')];
											if ($result[csf('level_no')] != '') $level_no = $result[csf('level_no')];
											if ($result[csf('road_no')] != '') $road_no = $result[csf('road_no')];
											if ($result[csf('block_no')] != '') $block_no = $result[csf('block_no')];
											if ($result[csf('city')] != '') $city = $result[csf('city')];
											$company_address[$result[csf('id')]] = $plot_no . '&nbsp' . $level_no . '&nbsp' . $road_no . '&nbsp' . $block_no . '&nbsp' . $city;
											if($result[csf('bin_no')]!='') echo "<br> BIN:".$result[csf('bin_no')];
										}
										?>
									</label>
									<br />
									<b style="font-size:20px;">
										<?php echo $report_title; ?>
									</b>
								</td>
								<td width="10px" align="center" style="font-size:20px;">
									<table width="80%" align="right" cellpadding="0" cellspacing="0" style="border:0px solid black">
										<tr>
											<td width="80"> Booking No:&nbsp; <?php echo $varcode_booking_no; ?> </td>
										</tr>
										<tr>
											<td> Booking Date:&nbsp; <?php echo change_date_format($booking_date); ?> </td>
										</tr>
										<?
										if ($revised_no > 0) {
										?>
											<tr>
												<td> Revised No:&nbsp; <?php echo $revised_no; ?> </td>
											</tr>
										<?
										}
										if (str_replace("'", "", $id_approved_id) == 1) {
										?>
											<tr>
												<td>Approved Status :&nbsp; <? if (str_replace("'", "", $id_approved_id) == 1) {
																				echo "(Approved)";
																			} else {
																				echo "";
																			}; ?> </td>
											</tr>
										<?
										}
										?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<table width="100%" style="border:0px solid black;table-layout: fixed;">
				<tr>
					<td colspan="6" valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td width="100" style="font-size:18px"><span><b>To, </b></span> </td>
					<td width="110" colspan="5" style="font-size:18px">&nbsp;<span></span></td>
				</tr>
				<tr>
					<td width="210" colspan="2" style="font-size:18px">&nbsp; <b>
							<?
							if ($pay_mode_id == 5 || $pay_mode_id == 3) echo $company_library[$supplier_id];
							else echo $supplier_name_arr[$supplier_id];
							?></b>
					</td>
					<td width="100" style="font-size:12px"><b>Buyer.</b></td>
					<td width="110">:&nbsp;<? echo $buyer_name_arr[$buyer_id]; ?></td>
					<td width="100" style="font-size:12px"><b>Delivery Date</b></td>
					<td width="110">:&nbsp;<? echo change_date_format($delivery_date); ?></td>
				</tr>
				<tr>
					<td width="110" colspan="2" rowspan="2" style="font-size:18px">Address :&nbsp;
						<?
						if ($pay_mode_id == 5 || $pay_mode_id == 3) $address = $company_address[$supplier_id];
						else $address = $supplier_address_arr[$supplier_id];
						echo $address;
						?>
					</td>
					<td width="100" style="font-size:12px"><b>PO Qty.</b> </td>
					<td width="110">:&nbsp;<? echo $tot_po_quantity; ?></td>
					<td width="100" style="font-size:12px"><b>Season</b> </td>
					<td width="110">:&nbsp;<? echo $seasons_names; ?></td>
				</tr>
				<tr>
					<td width="100" style="font-size:12px"><b>Currency</b></td>
					<td width="110">:&nbsp;<? echo $currency[$currency_id]; ?></td>
					<td width="100" style="font-size:12px"><b>Order Repeat </b> </td>
					<td width="110">:&nbsp;<? echo $order_rept_no; ?></td>
				</tr>
				<tr>
					<td style="font-size:12px"><b>Attention </b> </td>
					<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
						<? echo $attention; ?>
					</td>
					<td style="font-size:12px"><b>Dealing Merchant</b></td>
					<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
						<? echo implode(",", array_unique($all_dealing_marcent)); ?>
					</td>
					<td style="font-size:12px"><b>Pay mode</b></td>
					<td>:&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
				</tr>
				<tr>
					<td style="font-size:12px"><b>Source</b></td>
					<td>:&nbsp;<? echo $source[$source_id]; ?></td>
				</tr>
				<tr>
					<td width="100" style="font-size:12px"><b>Remarks</b> </td>
					<td width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<? echo $remarks; ?></td>
				</tr>
			</table>
			<!--==============================================AS PER GMTS COLOR START=========================================  -->
			<?
			$booking_country_arr = array();
			$nameArray_booking_country = sql_select("select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0");
			foreach ($nameArray_booking_country as $nameArray_booking_country_row) {
				$country_id_string = explode(",", $nameArray_booking_country_row[csf('country_id_string')]);
				$tocu = count($country_id_string);
				for ($cu = 0; $cu < $tocu; $cu++) {
					$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]] = $country_arr[$country_id_string[$cu]];
				}
			}

			$nameArray_job_po = sql_select("select job_no from wo_booking_dtls  where booking_no='$txt_booking_no' and status_active =1 and is_deleted=0 group by job_no order by job_no ");
			foreach ($nameArray_job_po as $nameArray_job_po_row) {
				$nameArray_item = sql_select("select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'   and a.sensitivity=1 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
				if (count($nameArray_item) > 0) {
					$po_ids = rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
					$po_ids = array_unique(explode(",", $po_ids));
					$ref_nos = rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]], ',');
					$ref_nos = implode(",", array_unique(explode(",", $ref_nos)));
					$po_no_qty = 0;
					$job_no = $nameArray_job_po_row[csf('job_no')];
					foreach ($po_ids as $poid) {
						$po_no_qty += $job_po_qty_arr[$job_no][$poid];
					}
			?>
					&nbsp;
					<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all">
						<tr>
							<td colspan="11" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:" . $nameArray_job_po_row[csf('job_no')]; ?>) <? echo "Style NO:" . $style_ref[$nameArray_job_po_row[csf('job_no')]];
																																								if ($ref_nos != '') echo " &nbsp;Int Ref.:&nbsp;" . $ref_nos;
																																								else " ";
																																								echo " &nbsp;  Po Qty.:&nbsp;" . $po_no_qty; ?></strong></td>
										<td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",", $po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black"><strong>Sl</strong> </td>
							<td style="border:1px solid black"><strong>Name/Country</strong> </td>
							<td style="border:1px solid black"><strong>Embl. Type</strong> </td>
							<td style="border:1px solid black"><strong>Gmts Item</strong> </td>
							<td style="border:1px solid black"><strong>Body Part</strong> </td>
							<td style="border:1px solid black"><strong>Description</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
							<td style="border:1px solid black" align="center"><strong>UOM</strong></td>
							<td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
							<td style="border:1px solid black" align="center"><strong>Rate</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						foreach ($nameArray_item as $result_item) {
							$i++;
							$nameArray_item_description = sql_select("select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id,a.uom as uom  from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'  and a.sensitivity=1  and c.emb_name=" . $result_item[csf('emb_name')] . " and a.pre_cost_fabric_cost_dtls_id=" . $result_item[csf('pre_cost_fabric_cost_dtls_id')] . " and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,c.emb_name,c.emb_type,c.body_part_id,a.uom order by bid ");


						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>"> <? echo $i; ?></td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $emblishment_name_array[$result_item[csf('emb_name')]] . "<br/>";
									echo implode(", ", $booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_color = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
									if ($result_item[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
								?>
									<td style="border:1px solid black"><? echo $emb_type_name; ?></td>
									<td style="border:1px solid black"><? echo $garments_item[$result_itemdescription[csf('gmt_item')]]; ?></td>
									<td style="border:1px solid black; text-align:left"><? echo $body_part[$result_itemdescription[csf('body_part_id')]]; ?></td>
									<td style="border:1px solid black; text-align:left"><? if ($result_itemdescription[csf('description')]) {
																							echo $result_itemdescription[csf('description')];
																						} ?></td>
									<td style="border:1px solid black; text-align:right"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?></td>
									<td style="border:1px solid black; text-align:right">

										<?
										//echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										if (empty($result_itemdescription[csf('uom')])) {
											echo $unit_of_measurement[2];
										} else {
											echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										}
										?>

									</td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')], 4);
																							$item_desctiption_total += $result_itemdescription[csf('cons')]; ?></td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										$amount_as_per_gmts_color = $result_itemdescription[csf('cons')] * $result_itemdescription[csf('rate')];
										echo number_format($amount_as_per_gmts_color, 4);
										$total_amount_as_per_gmts_color += $amount_as_per_gmts_color;
										?>
									</td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total, 4); ?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<? echo number_format($total_amount_as_per_gmts_color, 2);
								$grand_total_as_per_gmts_color += $total_amount_as_per_gmts_color;

								$booking_grand_total += $total_amount_as_per_gmts_color;
								$total_amount_as_per_gmts_color = 0;
								?>

							</td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="10"><strong>Total</strong></td>
						<td style="border:1px solid black; text-align:right"><? echo number_format($grand_total_as_per_gmts_color, 2);  ?></td>
					</tr>
					</table>
				<?
				}
				?>
				<!--==============================================AS PER GMTS COLOR END=========================================  -->


				<!--==============================================Size Sensitive START=========================================  -->
				<?
				$nameArray_item = sql_select("select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'   and a.sensitivity=2 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
				if (count($nameArray_item) > 0) {
					$po_ids = rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
					$po_ids = array_unique(explode(",", $po_ids));
					$ref_nos = rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]], ',');
					$ref_nos = implode(",", array_unique(explode(",", $ref_nos)));
					$po_no_qty = 0;
					$job_no = $nameArray_job_po_row[csf('job_no')];
					foreach ($po_ids as $poid) {
						$po_no_qty += $job_po_qty_arr[$job_no][$poid];
					}
				?>
					<br />
					<table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all">
						<tr>
							<td colspan="11" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="60%"><strong>Size Sensitive (<? echo "Job NO:" . $nameArray_job_po_row[csf('job_no')]; ?>) <? echo "Style NO:" . $style_ref[$nameArray_job_po_row[csf('job_no')]];
																																			echo "&nbsp;&nbsp;Int Ref.:" . $ref_nos;
																																			echo "&nbsp;&nbsp; Po Qty..:" . $po_no_qty; ?></strong></td>
										<td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",", $po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black"><strong>Sl</strong> </td>
							<td style="border:1px solid black"><strong>Name/Country</strong> </td>
							<td style="border:1px solid black"><strong>Embl. Type</strong> </td>
							<td style="border:1px solid black"><strong>Gmts Item</strong> </td>
							<td style="border:1px solid black"><strong>Body Part</strong> </td>
							<td style="border:1px solid black"><strong>Description</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
							<td style="border:1px solid black" align="center"><strong>UOM</strong></td>
							<td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
							<td style="border:1px solid black" align="center"><strong>Rate</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						foreach ($nameArray_item as $result_item) {
							$i++;
							$nameArray_item_description = sql_select("select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id,a.uom as uom  from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'  and a.sensitivity=2  and c.emb_name=" . $result_item[csf('emb_name')] . " and a.pre_cost_fabric_cost_dtls_id=" . $result_item[csf('pre_cost_fabric_cost_dtls_id')] . " and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_size,c.emb_name,c.emb_type,c.body_part_id,a.uom order by bid ");
						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>"><? echo $i; ?></td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $emblishment_name_array[$result_item[csf('emb_name')]] . "<br/>";
									echo implode(",", $booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_size = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
									if ($result_item[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
								?>
									<td style="border:1px solid black"><? echo $emb_type_name; ?></td>
									<td style="border:1px solid black"><? echo $garments_item[$result_itemdescription[csf('gmt_item')]]; ?></td>
									<td style="border:1px solid black; text-align:left"><? echo $body_part[$result_itemdescription[csf('body_part_id')]]; ?></td>
									<td style="border:1px solid black; text-align:left"><? if ($result_itemdescription[csf('description')]) {
																							echo $result_itemdescription[csf('description')];
																						} ?></td>
									<td style="border:1px solid black; text-align:right"><? echo $result_itemdescription[csf('item_size')]; ?></td>
									<td style="border:1px solid black; text-align:right">
										<?
										if (empty($result_itemdescription[csf('uom')])) {
											echo $unit_of_measurement[2];
										} else {
											echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										}
										?>

									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo number_format($result_itemdescription[csf('cons')], 4);
										$item_desctiption_total += $result_itemdescription[csf('cons')];
										?>
									</td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										$amount_as_per_gmts_size = $result_itemdescription[csf('cons')] * $result_itemdescription[csf('rate')];
										echo number_format($amount_as_per_gmts_size, 4);
										$total_amount_as_per_gmts_size += $amount_as_per_gmts_size;
										?>
									</td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total, 4); ?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<?
								echo number_format($total_amount_as_per_gmts_size, 2);
								$grand_total_as_per_gmts_size += $total_amount_as_per_gmts_size;
								$booking_grand_total += $total_amount_as_per_gmts_size;
								$total_amount_as_per_gmts_size = 0;
								?>
							</td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="10"><strong>Total</strong></td>
						<td style="border:1px solid black; text-align:right"><? echo number_format($grand_total_as_per_gmts_size, 2);  ?></td>
					</tr>
					</table>
					<br />
				<?
				}
				?>
				<!--==============================================Size Sensitive END=========================================  -->
				<!--==============================================AS PER CONTRAST COLOR START=========================================  -->
				<!--==============================================AS PER CONTRAST COLOR END=========================================  -->
				<?
				$nameArray_item = sql_select("select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'   and a.sensitivity=3 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
				if (count($nameArray_item) > 0) {
					$po_ids = rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
					$po_ids = array_unique(explode(",", $po_ids));
					$ref_nos = rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]], ',');
					$ref_nos = implode(",", array_unique(explode(",", $ref_nos)));
					$po_no_qty = 0;
					$job_no = $nameArray_job_po_row[csf('job_no')];
					foreach ($po_ids as $poid) {
						$po_no_qty += $job_po_qty_arr[$job_no][$poid];
					}
				?>
					&nbsp;
					<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all">
						<tr>
							<td colspan="12" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="60%" align="left"><strong>Contrast Color (<? echo "Job NO:" . $nameArray_job_po_row[csf('job_no')]; ?>) <? echo "Style NO:" . $style_ref[$nameArray_job_po_row[csf('job_no')]];
																																						if ($ref_nos != '') echo " &nbsp;Int Ref.:&nbsp;" . $ref_nos;
																																						else " ";
																																						echo " &nbsp;  Po Qty.:&nbsp;" . $po_no_qty; ?></strong></td>
										<td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",", $po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black"><strong>Sl</strong> </td>
							<td style="border:1px solid black"><strong>Name/Country</strong> </td>
							<td style="border:1px solid black"><strong>Embl. Type</strong> </td>
							<td style="border:1px solid black"><strong>Gmts Item</strong> </td>
							<td style="border:1px solid black"><strong>Body Part</strong> </td>
							<td style="border:1px solid black"><strong>Description</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
							<td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
							<td align="center" style="border:1px solid black"><strong>UOM</strong></td>
							<td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
							<td style="border:1px solid black" align="center"><strong>Rate</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						foreach ($nameArray_item as $result_item) {
							$i++;
							$nameArray_item_description = sql_select("select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id,a.uom as uom from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'  and a.sensitivity=3  and c.emb_name=" . $result_item[csf('emb_name')] . " and a.pre_cost_fabric_cost_dtls_id=" . $result_item[csf('pre_cost_fabric_cost_dtls_id')] . " and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,b.color_number_id,c.emb_name,c.emb_type,c.body_part_id,a.uom order by bid ");

						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<? echo $i; ?>
								</td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $emblishment_name_array[$result_item[csf('emb_name')]] . "<br/>";
									echo implode(", ", $booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_color = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
									if ($result_item[csf('emb_name')] == 1) {
										$emb_type_name = $emblishment_print_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 2) {
										$emb_type_name = $emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 3) {
										$emb_type_name = $emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 4) {
										$emb_type_name = $emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 5) {
										$emb_type_name = $emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
									}
								?>
									<td style="border:1px solid black">
										<? echo $emb_type_name; ?>
									</td>
									<td style="border:1px solid black">
										<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
									</td>
									<td style="border:1px solid black; text-align:left">
										<? echo $body_part[$result_itemdescription[csf('body_part_id')]] // 
										?>
									</td>
									<td style="border:1px solid black; text-align:left">
										<?
										if ($result_itemdescription[csf('description')]) {
											echo $result_itemdescription[csf('description')];
										}
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $color_library[$result_itemdescription[csf('item_color')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $color_library[$result_itemdescription[csf('color_number_id')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">


										<?
										//echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										if (empty($result_itemdescription[csf('uom')])) {
											echo $unit_of_measurement[2];
										} else {
											echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										}
										?>

									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo number_format($result_itemdescription[csf('cons')], 4);
										$item_desctiption_total += $result_itemdescription[csf('cons')];
										?>
									</td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										$amount_as_per_gmts_color = $result_itemdescription[csf('cons')] * $result_itemdescription[csf('rate')];
										echo number_format($amount_as_per_gmts_color, 4);
										$total_amount_as_per_gmts_color += $amount_as_per_gmts_color;
										?>
									</td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
																										echo number_format($item_desctiption_total, 4);
																										?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<?
								echo number_format($total_amount_as_per_gmts_color, 2);
								$grand_total_as_per_gmts_color += $total_amount_as_per_gmts_color;
								$booking_grand_total += $total_amount_as_per_gmts_color;
								$total_amount_as_per_gmts_color = 0;
								?>
							</td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="11"><strong>Total</strong></td>
						<td style="border:1px solid black;  text-align:right"><? echo number_format($grand_total_as_per_gmts_color, 2);  ?></td>
					</tr>
					</table>

				<?
				}
				?>
				<!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
				<?
				$nameArray_item = sql_select("select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'   and a.sensitivity=4 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
				if (count($nameArray_item) > 0) {
					$po_ids = rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
					$po_ids = array_unique(explode(",", $po_ids));
					$ref_nos = rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]], ',');
					$ref_nos = implode(",", array_unique(explode(",", $ref_nos)));
					$po_no_qty = 0;
					$job_no = $nameArray_job_po_row[csf('job_no')];
					foreach ($po_ids as $poid) {
						$po_no_qty += $job_po_qty_arr[$job_no][$poid];
					}
				?>
					&nbsp;
					<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all">
						<tr>
							<td colspan="14" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="60%" align="left"><strong>Color & Size Sensitive (<? echo "Job NO:" . $nameArray_job_po_row[csf('job_no')]; ?>) <? echo "Style NO:" . $style_ref[$nameArray_job_po_row[csf('job_no')]];
																																								if ($ref_nos != '') echo " &nbsp;Int Ref.:&nbsp;" . $ref_nos;
																																								else " ";
																																								echo " &nbsp;  Po Qty.:&nbsp;" . $po_no_qty; ?></strong></td>
										<td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",", $po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black"><strong>Sl</strong> </td>
							<td style="border:1px solid black"><strong>Name/Country</strong> </td>
							<td style="border:1px solid black"><strong>Embl. Type</strong> </td>
							<td style="border:1px solid black"><strong>Gmts Item</strong> </td>
							<td style="border:1px solid black"><strong>Body Part</strong> </td>
							<td style="border:1px solid black"><strong>Description</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
							<td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
							<td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
							<td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
							<td align="center" style="border:1px solid black"><strong>UOM</strong></td>
							<td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
							<td style="border:1px solid black" align="center"><strong>Rate</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						foreach ($nameArray_item as $result_item) {
							$i++;
							$nameArray_item_description = sql_select("select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, c.emb_name,c.emb_type,c.body_part_id,a.uom as uom from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'  and a.sensitivity=4  and c.emb_name=" . $result_item[csf('emb_name')] . " and a.pre_cost_fabric_cost_dtls_id=" . $result_item[csf('pre_cost_fabric_cost_dtls_id')] . " and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,c.emb_name,c.emb_type,c.body_part_id,a.uom order by bid ");

						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<? echo $i; ?>
								</td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $emblishment_name_array[$result_item[csf('emb_name')]] . "<br/>";
									echo implode(", ", $booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_color = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
									if ($result_item[csf('emb_name')] == 1) {
										$emb_type_name = $emblishment_print_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 2) {
										$emb_type_name = $emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 3) {
										$emb_type_name = $emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 4) {
										$emb_type_name = $emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
									}
									if ($result_item[csf('emb_name')] == 5) {
										$emb_type_name = $emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
									}
								?>
									<td style="border:1px solid black">
										<? echo $emb_type_name; ?>
									</td>
									<td style="border:1px solid black">
										<? echo $garments_item[$result_itemdescription[csf('gmt_item')]];  ?>
									</td>
									<td style="border:1px solid black; text-align:left">
										<? echo $body_part[$result_itemdescription[csf('body_part_id')]] // 
										?>
									</td>
									<td style="border:1px solid black; text-align:left">
										<?
										if ($result_itemdescription[csf('description')]) {
											echo $result_itemdescription[csf('description')];
										}
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $color_library[$result_itemdescription[csf('item_color')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $color_library[$result_itemdescription[csf('color_number_id')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $result_itemdescription[csf('item_size')];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo $size_library[$result_itemdescription[csf('gmts_sizes')]];
										?>
									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										//echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										if (empty($result_itemdescription[csf('uom')])) {
											echo $unit_of_measurement[2];
										} else {
											echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										}

										?>

									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo number_format($result_itemdescription[csf('cons')], 4);
										$item_desctiption_total += $result_itemdescription[csf('cons')];
										?>
									</td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										$amount_as_per_gmts_color = $result_itemdescription[csf('cons')] * $result_itemdescription[csf('rate')];
										echo number_format($amount_as_per_gmts_color, 4);
										$total_amount_as_per_gmts_color += $amount_as_per_gmts_color;
										?>
									</td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="9"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
																										echo number_format($item_desctiption_total, 4);
																										?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<?
								echo number_format($total_amount_as_per_gmts_color, 2);
								$grand_total_as_per_gmts_color += $total_amount_as_per_gmts_color;
								$booking_grand_total += $total_amount_as_per_gmts_color;
								$total_amount_as_per_gmts_color = 0;
								?>
							</td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="13"><strong>Total</strong></td>
						<td style="border:1px solid black;  text-align:right"><? echo number_format($grand_total_as_per_gmts_color, 2);  ?></td>
					</tr>
					</table>

				<?
				}
				?>

				<!--==============================================AS PER Color & SIZE  END=========================================  -->

				<!--==============================================NO NENSITIBITY START=========================================  -->
				<?
				$nameArray_item = sql_select("select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'   and a.sensitivity=0 group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
				if (count($nameArray_item) > 0) {
					$po_ids = rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
					$po_ids = array_unique(explode(",", $po_ids));
					$ref_nos = rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]], ',');
					$ref_nos = implode(",", array_unique(explode(",", $ref_nos)));
					$po_no_qty = 0;
					$job_no = $nameArray_job_po_row[csf('job_no')];
					foreach ($po_ids as $poid) {
						$po_no_qty += $job_po_qty_arr[$job_no][$poid];
					}
				?>
					&nbsp;
					<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all">
						<tr>
							<td colspan="11" align="">
								<table width="100%" style="table-layout: fixed;">
									<tr>
										<td width="60%" align="left"><strong>NO sensitive (<? echo "Job NO:" . $nameArray_job_po_row[csf('job_no')]; ?>) <? echo "Style NO:" . $style_ref[$nameArray_job_po_row[csf('job_no')]];
																																						if ($ref_nos != '') echo " &nbsp;Int Ref.:&nbsp;" . $ref_nos;
																																						else " ";
																																						echo " &nbsp;  Po Qty.:&nbsp;" . $po_no_qty; ?></strong></td>
										<td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",", $po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border:1px solid black"><strong>Sl</strong> </td>
							<td style="border:1px solid black"><strong>Name/Country</strong> </td>
							<td style="border:1px solid black"><strong>Embl. Type</strong> </td>
							<td style="border:1px solid black"><strong>Gmts Item</strong> </td>
							<td style="border:1px solid black"><strong>Body Part</strong> </td>
							<td style="border:1px solid black"><strong>Description</strong> </td>
							<td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
							<td style="border:1px solid black" align="center"><strong>UOM </strong></td>
							<td style="border:1px solid black" align="center"><strong>WO Qty </strong></td>
							<td style="border:1px solid black" align="center"><strong>Rate</strong></td>
							<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
						</tr>
						<?
						$i = 0;
						$grand_total_as_per_gmts_color = 0;
						foreach ($nameArray_item as $result_item) {
							$i++;
							$nameArray_item_description = sql_select("select a.pre_cost_fabric_cost_dtls_id,a.gmt_item,min(b.id) as bid, b.description,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(a.amount) as amount, c.emb_name,c.emb_type,c.body_part_id,a.uom as uom from wo_booking_dtls a,  wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c where a.id= b.wo_booking_dtls_id and a.booking_no=b.booking_no  and a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no='$txt_booking_no' and a.job_no='" . $nameArray_job_po_row[csf('job_no')] . "'  and a.sensitivity=0  and c.emb_name=" . $result_item[csf('emb_name')] . " and a.pre_cost_fabric_cost_dtls_id=" . $result_item[csf('pre_cost_fabric_cost_dtls_id')] . " and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,a.gmt_item, b.description,b.item_color,c.emb_name,c.emb_type,c.body_part_id,a.uom order by bid ");

						?>
							<tr>
								<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<? echo $i; ?>
								</td>
								<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description) + 1; ?>">
									<?
									echo $emblishment_name_array[$result_item[csf('emb_name')]] . "<br/>";
									echo implode(", ", $booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);
									?>
								</td>
								<?
								$item_desctiption_total = 0;
								$total_amount_as_per_gmts_color = 0;
								foreach ($nameArray_item_description as $result_itemdescription) {
									if ($result_item[csf('emb_name')] == 1) $emb_type_name = $emblishment_print_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 2) $emb_type_name = $emblishment_embroy_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 3) $emb_type_name = $emblishment_wash_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 4) $emb_type_name = $emblishment_spwork_type[$result_itemdescription[csf('emb_type')]];
									if ($result_item[csf('emb_name')] == 5) $emb_type_name = $emblishment_gmts_type[$result_itemdescription[csf('emb_type')]];
								?>
									<td style="border:1px solid black"><? echo $emb_type_name; ?></td>
									<td style="border:1px solid black"><? echo $garments_item[$result_itemdescription[csf('gmt_item')]]; ?></td>
									<td style="border:1px solid black; text-align:left"><? echo $body_part[$result_itemdescription[csf('body_part_id')]]; ?></td>
									<td style="border:1px solid black; text-align:left"><? if ($result_itemdescription[csf('description')]) {
																							echo $result_itemdescription[csf('description')];
																						} ?></td>
									<td style="border:1px solid black; text-align:right"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?></td>
									<td style="border:1px solid black; text-align:right">
										<?
										//echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										if (empty($result_itemdescription[csf('uom')])) {
											echo $unit_of_measurement[2];
										} else {
											echo $unit_of_measurement[$result_itemdescription[csf('uom')]];
										}
										?>

									</td>
									<td style="border:1px solid black; text-align:right">
										<?
										echo number_format($result_itemdescription[csf('cons')], 4);
										$item_desctiption_total += $result_itemdescription[csf('cons')];
										?>
									</td>
									<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')], 4); ?> </td>
									<td style="border:1px solid black; text-align:right">
										<?
										// $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
										$amount_as_per_gmts_color = $result_itemdescription[csf('amount')];
										echo number_format($amount_as_per_gmts_color, 4);
										$total_amount_as_per_gmts_color += $amount_as_per_gmts_color;
										?>
									</td>
							</tr>
						<?
								}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total, 4); ?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
								<?
								echo number_format($total_amount_as_per_gmts_color, 2);
								$grand_total_as_per_gmts_color += $total_amount_as_per_gmts_color;
								$booking_grand_total += $total_amount_as_per_gmts_color;
								$total_amount_as_per_gmts_color = 0;
								?>
							</td>
						</tr>
					<?
						}
					?>
					<tr>
						<td align="right" style="border:1px solid black" colspan="10"><strong>Total</strong></td>
						<td style="border:1px solid black; text-align:right"><? echo number_format($grand_total_as_per_gmts_color, 2);  ?></td>
					</tr>
					</table>

				<?
				}
				?>
				<!--==============================================NO NENSITIBITY END=========================================  -->
			<?
			}
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
			<table width="100%" style="margin-top:1px">
				<tr>
					<td>
						<table width="100%" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
							<tr style="border:1px solid black;">
								<td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
								<td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total, 2); ?></td>
							</tr>
							<tr style="border:1px solid black;">
								<td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
								<td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total, 2, ""), $mcurrency, $dcurrency); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td width="49%"><? echo get_spacial_instruction($txt_booking_no); ?></td>
					<td width="2%">&nbsp;</td>
					<? $data_array = sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0"); ?>
					<td width="49%" valign="top">
						<table width="100%" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
							<tr style="border:1px solid black;">
								<td colspan="3" style="border:1px solid black;">Approval Status</td>
							</tr>
							<tr style="border:1px solid black;">
								<td width="3%" style="border:1px solid black;">Sl</td>
								<td width="50%" style="border:1px solid black;">Name</td>
								<td width="27%" style="border:1px solid black;">Approval Date</td>
								<td width="20%" style="border:1px solid black;">Approval No</td>
							</tr>
							<?
							$i;
							foreach ($data_array as $row) {
							?>
								<tr style="border:1px solid black;">
									<td width="3%" style="border:1px solid black;"><? echo $i; ?></td>
									<td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]; ?></td>
									<td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')], "dd-mm-yyyy", "-"); ?></td>
									<td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')]; ?></td>
								</tr>
							<?
								$i++;
							}
							?>
						</table>
					</td>
				</tr>
			</table>
			<?
			if (str_replace("'", "", $show_comment) == 1) //Aziz
			{
				$cbo_currency = str_replace("'", "", $currency_id);
			?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table">
					<tr>
						<td colspan="8" align="center"> <b>Comments</b> </td>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="200">Embl Name</th>
						<th width="100">Booking Type</th>
						<th width="200">PO No</th>
						<th width="80">Pre-Cost/Budget Value</th>
						<th width="80">WO Value</th>
						<th width="80">Balance</th>
						<th width="">Comments </th>
					</tr>
					<tbody>
						<?
						$sql_po_qty = sql_select("select b.id as po_id, b.po_number, sum(b.plan_cut) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.id in($po_idss) group by b.id, b.po_number, a.total_set_qnty");
						foreach ($sql_po_qty as $row) {
							$po_qty_arr[$row[csf("po_id")]]['order_quantity'] = $row[csf("order_quantity")];
							$po_number_arr[$row[csf("po_id")]] = $row[csf("po_number")];
							//$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
						}

						$sql_cons_data = sql_select("select a.job_no,a.emb_name,a.amount,a.rate as rate from wo_pre_cost_embe_cost_dtls a  where  a.is_deleted=0  and a.status_active=1");

						foreach ($sql_cons_data as $row) {
							$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['amount'] = $row[csf("amount")];
							$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['rate'] = $row[csf("rate")];
						}


						$embl_booking_array = array();
						$embl_booking_data = array();
						$sql_wo = sql_select("select b.po_break_down_id as po_id,b.booking_no,a.exchange_rate,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,sum(b.amount) as amount  from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=11  and
					b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($po_idss) group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate"); //and b.is_short=2
						foreach ($sql_wo as $row) { //pre_cost_fabric_cost_dtls_id
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['amount'] = $row[csf('amount')];
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['exchange_rate'] = $row[csf('exchange_rate')];
						}

						if ($db_type == 0) $group_concat = "group_concat( distinct booking_no,',') AS booking_no";
						else if ($db_type == 2)  $group_concat = "listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";


						$wo_book = sql_select("select po_break_down_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id,$group_concat,sum(amount) as amount  from wo_booking_dtls where
					 booking_type=11  and status_active=1 and is_deleted=0 and po_break_down_id in($po_idss) group by po_break_down_id,pre_cost_fabric_cost_dtls_id"); //and is_short=2
						foreach ($wo_book as $row) { //pre_cost_fabric_cost_dtls_id
							$embl_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no'] = $row[csf('booking_no')];
						}



						$sql_booking_cu = "select b.po_break_down_id,b.job_no,c.emb_type,b.pre_cost_fabric_cost_dtls_id,sum(b.amount) as amount,c.emb_name from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where b.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$txt_booking_no' and a.booking_type=11  and  a.status_active=1 and  b.status_active=1  and 	b.is_deleted=0  and a.is_deleted=0 and b.amount>0  group by b.po_break_down_id,b.job_no,b.pre_cost_fabric_cost_dtls_id,c.emb_name,emb_type  order by b.po_break_down_id"; //and a.is_short=2
						//echo $sql_booking_cu;

						//$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no='".$txt_booking_no."'");
						$i = 1;
						$tot_pre_amount = 0;
						$tot_embl_amount = 0;
						$tot_embl_amount_up = 0;
						$tot_balance_cost = 0;
						//$po_qty=0;
						$nameArray = sql_select($sql_booking_cu);
						$po_id = array();
						foreach ($nameArray as $forpoid) {
							$po_id[$forpoid[csf('po_break_down_id')]] = $forpoid[csf('po_break_down_id')];
						}
						$condition = new condition();
						$condition->company_name("=$cbo_company_name");
						if (str_replace("'", "", $po_idss) != '') {
							$condition->po_id_in("$po_idss");
						}
						$condition->init();

						$emblishment = new emblishment($condition);
						$wash = new wash($condition);
						//echo $emblishment->getQuery();die;
						//$emblishment= new emblishment($po_id,'po');
						$precostembamount = $emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
						//print_r($precostembamount);
						//$wash= new wash($po_id,'po');
						$emblishment_costing_arr_name_wash = $wash->getAmountArray_by_orderAndEmbname();

						foreach ($nameArray as $selectResult) {
							//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
							$sql_pre = sql_select("select b.costing_per,b.exchange_rate from wo_pre_cost_mst b where b.job_no='" . $selectResult[csf('job_no')] . "'");
							//echo "select b.costing_per,b.exchange_rate from wo_pre_cost_mst b where b.job_no='".$selectResult[csf('job_no')]."' ";
							foreach ($sql_pre as $row) {
								$job_exchaned_rate = $row[csf('exchange_rate')];
								$costing_per = $row[csf('exchange_rate')];
							}
							//echo $job_exchaned_rate;
							if ($costing_per == 1) $costing_per_qty = 12;
							else if ($costing_per == 2) $costing_per_qty = 1;
							else if ($costing_per == 3) $costing_per_qty = 24;
							else if ($costing_per == 4) $costing_per_qty = 36;
							else if ($costing_per == 5) $costing_per_qty = 48;

							$po_qty = $po_qty_arr[$selectResult[csf('po_break_down_id')]]['order_quantity'];

							$pre_amount2 = (($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount'] / $costing_per_qty) * $po_qty);
							$pre_amount = $pre_amount2;

							$booking_data = array_unique(explode(",", $embl_booking_data[$selectResult[csf('po_break_down_id')]][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no']));
							$booking_amount = 0;
							$exchaned_rate = 0;

							foreach ($booking_data as $book_no) {
								if ($book_no != str_replace("'", "", $txt_booking_no)) {
									$booking_amount = $embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['amount'];
									$exchaned_rate = $embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['exchange_rate'];
								}
							} //echo $booking_amount;
							$bookAmt = 0;
							if ($exchaned_rate) {
								$bookAmt = $booking_amount / $exchaned_rate;
							}

							if ($cbo_currency == 2) {
								$embl_pre_amount = $selectResult[csf("amount")] + $booking_amount;
								//echo "A";
							} else {
								$embl_pre_amount = ($selectResult[csf("amount")] / $job_exchaned_rate) + $bookAmt;
								//echo $selectResult[csf("amount")].'='.$booking_amount.'='.$exchaned_rate.'<br>';
							}
						?>
							<tr>
								<td width="40"><? echo $i; ?></td>
								<td width="200">
									<? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?>
								</td>
								<td width="100">
									<?

									if ($selectResult[csf('emb_name')] == 1) {
										$emb_type = $emblishment_print_type[$selectResult[csf('emb_type')]];
									}
									if ($selectResult[csf('emb_name')] == 2) {
										$emb_type = $emblishment_embroy_type[$selectResult[csf('emb_type')]];
									}
									if ($selectResult[csf('emb_name')] == 3) {
										$emb_type = $emblishment_wash_type[$selectResult[csf('emb_type')]];
									}
									if ($selectResult[csf('emb_name')] == 4) {
										$emb_type = $emblishment_spwork_type[$selectResult[csf('emb_type')]];
									}
									echo $emb_type;

									//echo $emblishment_print_type[$selectResult[csf('emb_type')]];
									?>
								</td>
								<td width="200">
									<? echo $po_number_arr[$selectResult[csf('po_break_down_id')]]; ?>
								</td>
								<td width="80" align="right">
									<?
									if ($selectResult[csf('emb_name')] == 3) {
										$wash_cost = $emblishment_costing_arr_name_wash[$selectResult[csf('po_break_down_id')]][3];
									}
									echo number_format($precostembamount[$selectResult[csf('po_break_down_id')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]] + $wash_cost, 2);
									$pre_amount = $precostembamount[$selectResult[csf('po_break_down_id')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]] + $wash_cost;

									// echo number_format($pre_amount,2); 
									?>
								</td>
								<td width="80" align="right" title="<? echo 'Prev. Value=' . $booking_amount ?>">
									<? echo number_format($embl_pre_amount, 2); ?>
								</td>

								<td width="80" align="right">
									<?
									$embl_amount = number_format($embl_pre_amount, 2);
									$precost_amount = number_format($pre_amount, 2);
									$tot_balance = $pre_amount - $embl_pre_amount;
									echo number_format($tot_balance, 2); ?>
								</td>
								<td width="">
									<?
									//echo $precost_amount.'='.$embl_amount;
									if ($pre_amount > $embl_pre_amount) echo "Less Booking";
									else if ($pre_amount < $embl_pre_amount) echo "Over Booking";
									else if ($pre_amount == $embl_pre_amount) echo "As Per";
									else echo "";
									?>
								</td>
							</tr>
						<?
							$tot_pre_amount += $pre_amount;
							$tot_embl_amount += $embl_pre_amount;
							$tot_balance_cost += $tot_balance;
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3" align="right"> <b>Total</b></td>
							<td align="right"> <b></b></td>
							<td align="right"><b><? echo number_format($tot_pre_amount, 2); ?> </b></td>
							<td align="right"> <b> <? echo number_format($tot_embl_amount, 2); ?></b> </td>
							<td align="right"><b><? echo number_format($tot_balance_cost, 2); ?></b> </td>
						</tr>
					</tfoot>
				</table>
			<?
			}
			?>
		</table>
		<?
		// image show here  -------------------------------------------
		$sql_img = "select id, master_tble_id, image_location from common_photo_library where form_name='print_booking_multijob' and master_tble_id ='$txt_booking_no' ";
		$data_array = sql_select($sql_img);
		?>
		<div align="left" style="margin:5px 2px;float:left;width:100%">
			<? foreach ($data_array as $inf) { ?>
				<img src='../../<? echo $inf[csf("image_location")]; ?>' height='70' width='80' />
			<? } ?>
		</div>
	</div>
	<!--class="footer_signature"-->
	<div style="margin-top:-5px;"><? echo signature_table(133, $cbo_company_name, "1300px", $cbo_template_id); ?></div>
	<br>
	<div id="page_break_div"></div>
	<div><? echo "****" . custom_file_name($txt_booking_no, $style_sting, $job_no); ?></div>
	<?
	if ($link == 1) {
	?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<?
	} else {
	?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<?
	}
	?>
	<script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>', 'barcode_img_id');
	</script>

	</html>
	<?
	exit();
}

if ($action == "check_pi_number") {
	$piNumber = 0;
	$pi_number = return_field_value("pi_number", "com_pi_master_details a,com_pi_item_details b", " a.id=b.pi_id  and b.work_order_no='$data' and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
	if ($pi_number) {
		$piNumber = 1;
	}

	echo $piNumber;
	die;
}
?>