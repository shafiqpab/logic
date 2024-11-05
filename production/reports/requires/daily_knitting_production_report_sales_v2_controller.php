<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_floor") {
	echo create_drop_down("cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

if ($action == "load_drop_down_buyer") {
	$ex_data = explode('**', $data);
	if ($ex_data[1] == 1) {
		echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", 0);
	} else if ($ex_data[1] == 2) {
		echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", 0);
	} else {
		echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- All Buyer --", $selected, "", 1, "");
	}
	exit();
}

if ($action == "load_drop_down_cust_buyer")
{

	if ($data == 0)
	{
		echo create_drop_down("cbo_cust_buyer_id", 100, $blank_array, "", 1, "--Select Cust Buyer--", 0, "");
	}
	else
	{
		echo create_drop_down("cbo_cust_buyer_id", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", $selected, "", 0);
	}
	exit();
}

$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$floor_details = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
// common end

if ($action == "sales_order_no_search_popup") {
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(job_no) {
			document.getElementById('hidden_job_no').value = job_no;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:0px;">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
							<thead>
								<th>Within Group</th>
								<th>Search By</th>
								<th>Search No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
									<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">

								</th>
							</thead>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", $cbo_within_group, $dd, 0);
									?>
								</td>
								<td align="center">
									<?
									$serach_type_arr = array(1 => 'Sales Order No', 2 => 'Fab. Booking No');
									echo create_drop_down("cbo_serach_type", 150, $serach_type_arr, "", 0, "--Select--", "", "", 0);
									?>
								</td>
								<td align="center">
									<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('hidden_yearID').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'daily_knitting_production_report_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
								</td>
							</tr>
						</table>
						<div style="margin-top:15px" id="search_div"></div>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_sales_order_no_search_list") {
	$data 			= explode('_', $data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	if ($db_type == 0) {
		if ($yearID != 0) $year_cond = " and YEAR(a.insert_date)=$yearID";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($yearID != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=$yearID";
		else $year_cond = "";
	}

	$within_group_cond  = ($within_group == 0) ? "" : " and a.within_group=$within_group";
	if ($serach_type == 1) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.job_no like '%$sales_order_no%'";
	} else if ($serach_type == 2) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.sales_booking_no like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2) ? "to_char(a.insert_date,'YYYY') as year" : "YEAR(a.insert_date) as year";

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond $year_cond order by a.id";
	$result = sql_select($sql);
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order ID</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:950px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1) {
					$buyer = $company_arr[$row[csf('buyer_id')]];
				} else {
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="110" align="center">
						<p>&nbsp;<? echo $row[csf('job_no')]; ?></p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('sales_booking_no')]; ?></p>
					</td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>
					<td width="110" align="center">
						<p><? echo $row[csf('style_ref_no')]; ?></p>
					</td>
					<td>
						<p><? echo $location_arr[$row[csf('location_id')]]; ?></p>
					</td>
				</tr>
			<?
				$i++;
			}
			?>
		</table>
	</div>
<?
	exit();
}

if ($action == "booking_no_search_popup") {
	echo load_html_head_contents("Booking No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

?>
	<script>
		var tableFilters = {
			col_11: "select",
			display_all_text: 'Show All'
		}

		function js_set_value(data) {
			$('#hidden_booking_data').val(data);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="searchwofrm" id="searchwofrm" autocomplete=off>
				<fieldset style="width:98%;">
					<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Enter search words</h3>
					<div id="content_search_panel">
						<table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
							<thead>
								<th>Buyer</th>
								<th>Booking Date</th>
								<th>Search By</th>
								<th id="search_by_td_up" width="200">Please Enter Booking No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $companyID; ?>">
									<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" class="text_boxes" value="">
								</th>
							</thead>
							<tr>
								<td align="center">
									<?
									$user_wise_buyer = $_SESSION['logic_erp']['buyer_id'];
									$buyer_sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in ($user_wise_buyer) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
									echo create_drop_down("cbo_buyer", 150, $buyer_sql, "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Booking No", 2 => "Job No");
									$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value+'_'+'<? echo $cbo_booking_type; ?>', 'create_booking_search_list_view', 'search_div', 'daily_knitting_production_report_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
							</tr>
						</table>
					</div>
					<table width="100%" style="margin-top:5px">
						<tr>
							<td colspan="5">
								<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_booking_search_list_view") {
	$data = explode("_", $data);

	$search_string 	= "%" . trim($data[0]) . "%";
	$search_by 		= $data[1];
	$company_id 	= $data[2];
	$date_from 		= trim($data[3]);
	$date_to 		= trim($data[4]);
	$buyer_id 		= $data[5];
	$booking_type 		= $data[6];
	$buyer_arr 		= return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	if ($unit_id == 0) {
		$unit_id_cond = "";
	} else {
		$unit_id_cond = " and a.company_id=$unit_id";
	}
	if ($buyer_id == 0) {
		$buyer_id_cond = $buyer_id_cond;
	} else {
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.booking_no like '$search_string'";
		else
			$search_field_cond = "and a.job_no like '$search_string'";
	}

	$date_cond = '';
	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$import_booking_id_arr = return_library_array("select id, booking_id from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0", 'id', 'booking_id');

	$apporved_date_arr = return_library_array("select mst_id,max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id", 'mst_id', 'approved_date');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');

	$entry_form_cond = ($booking_type > 0) ? " and a.entry_form=$booking_type" : "";

	$sql = "SELECT a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks FROM wo_booking_mst a, wo_po_details_master b WHERE a.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $entry_form_cond group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.booking_date asc";
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="65">Buyer</th>
			<th width="65">Unit</th>
			<th width="90">Booking No</th>
			<th width="50">Booking ID</th>
			<th width="90">Job No</th>
			<th width="110">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th width="80">App. Date</th>
			<th width="80">Delivery Date</th>
			<th width="70">Currency</th>
			<th width="60">Approved</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:1080px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$result = sql_select($sql);
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				if ($row[csf('po_break_down_id')] != "") {
					$po_no = '';
					$po_ids = explode(",", $row[csf('po_break_down_id')]);
					foreach ($po_ids as $po_id) {
						if ($po_no == "") $po_no = $po_arr[$po_id];
						else $po_no .= "," . $po_arr[$po_id];
					}
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')] == 1) ? "Yes" : "No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
			}

			//partial booking start;
			$partial_sql = "SELECT a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date,a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, listagg(d.po_break_down_id, ',') within group (order by d.po_break_down_id) as po_break_down_id, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks FROM wo_booking_mst a, wo_po_details_master b,wo_booking_dtls d WHERE a.booking_no=d.booking_no and d.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 $entry_form_cond $buyer_id_cond $unit_id_cond $search_field_cond $date_cond group by a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date, a.entry_form,a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.booking_date asc";
			$partial_result = sql_select($partial_sql);
			foreach ($partial_result as $row) {
				if (!in_array($row[csf('id')], $import_booking_id_arr)) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					if ($row[csf('po_break_down_id')] != "") {
						$po_no = '';
						$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
						foreach ($po_ids as $po_id) {
							if ($po_no == "") $po_no = $po_arr[$po_id];
							else $po_no .= "," . $po_arr[$po_id];
						}
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
						<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
						<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
						<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
						<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
						<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
						<td width="60" align="center"><? echo ($row[csf('is_approved')] == 1) ? "Yes" : "No"; ?></td>
						<td style="word-break: break-all;"><? echo $po_no; ?></td>
					</tr>
			<?
					$i++;
				}
			}
			?>
		</table>
	</div>
<?
	exit();
}
//--------Internal ref search popup--------------
if ($action == "internal_ref_no_search_popup") {
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(booking_no,internalref) {
			document.getElementById('hidden_internalref_no').value = internalref;
			document.getElementById('hidden_booking_no').value = booking_no;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:0px;">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
							<thead>
								<th>Within Group</th>
								<th>Search By</th>
								<th>Search No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_internalref_no" id="hidden_internalref_no" value="">
									<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
									<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">

								</th>
							</thead>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", $cbo_within_group, $dd, 0);
									?>
								</td>
								<td align="center">
									<?
									$serach_type_arr = array(1 => 'Sales Order No', 2 => 'Fab. Booking No', 3 => 'IR/IB');
									echo create_drop_down("cbo_serach_type", 150, $serach_type_arr, "", 0, "--Select--", "", "", 0);
									?>
								</td>
								<td align="center">
									<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('hidden_yearID').value+'_'+document.getElementById('cbo_serach_type').value, 'create_internalref_no_search_list', 'search_div', 'daily_knitting_production_report_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
								</td>
							</tr>
						</table>
						<div style="margin-top:15px" id="search_div"></div>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_internalref_no_search_list") {
	$data 			= explode('_', $data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	if ($db_type == 0) {
		if ($yearID != 0) $year_cond = " and YEAR(a.insert_date)=$yearID";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($yearID != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=$yearID";
		else $year_cond = "";
	}

	$within_group_cond  = ($within_group == 0) ? "" : " and a.within_group=$within_group";
	if ($serach_type == 1) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.job_no like '%$sales_order_no%'";
	} else if ($serach_type == 2) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.sales_booking_no like '%$sales_order_no%'";
	}
	else if ($serach_type == 3) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and c.grouping like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2) ? "to_char(a.insert_date,'YYYY') as year" : "YEAR(a.insert_date) as year";

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id,c.grouping  from fabric_sales_order_mst a,wo_booking_dtls b,wo_po_break_down c
	where a.sales_booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0  and c.grouping is not null $within_group_cond $search_field_cond $sales_order_cond $year_cond group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id,
	a.style_ref_no, a.location_id ,c.grouping order by a.id";


	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order ID</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>
			<th width="110">IR/IB</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:1060px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1) {
					$buyer = $company_arr[$row[csf('buyer_id')]];
				} else {
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('sales_booking_no')]; ?>','<? echo $row[csf('grouping')]; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="110" align="center">
						<p>&nbsp;<? echo $row[csf('job_no')]; ?></p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('sales_booking_no')]; ?></p>
					</td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>
					<td width="110" align="center">
						<p><? echo $row[csf('grouping')]; ?></p>
					</td>
					<td width="110" align="center">
						<p><? echo $row[csf('style_ref_no')]; ?></p>
					</td>
					<td>
						<p><? echo $location_arr[$row[csf('location_id')]]; ?></p>
					</td>
				</tr>
			<?
				$i++;
			}
			?>
		</table>
	</div>
<?
	exit();
}
//-------------------------
if ($action == "job_no_search_popup") {
	echo load_html_head_contents("Job No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id) {
			document.getElementById('hide_job_no').value = id;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:580px;">
					<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="120">Please Enter Order No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									if ($ordType == 1) {
										echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
									} else if ($ordType == 2) {
										echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
									}
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No");
									$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
									echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $yearID; ?>'+'**'+'<? echo $ordType; ?>', 'create_job_no_search_list_view', 'search_div', 'daily_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}
if ($action == "create_job_no_search_list_view") {
	$data = explode('**', $data);
	$company_id = $data[0];
	if ($data[5] == 1) {
		if ($data[1] == 0) {
			if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
				if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				else $buyer_id_cond = "";
			} else {
				$buyer_id_cond = "";
			}
		} else {
			$buyer_id_cond = " and a.buyer_name=$data[1]";
		}



		$search_by = $data[2];
		$search_string = "%" . trim($data[3]) . "%";

		if ($search_by == 1)
			$search_field = "b.po_number";
		else if ($search_by == 2)
			$search_field = "a.style_ref_no";
		else
			$search_field = "a.job_no";

		$start_date = trim($data[4]);
		$end_date = trim($data[5]);

		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
		} else {
			$date_cond = "";
		}

		$search_year = $data[4];
		if ($db_type == 0) {
			$year_field = "YEAR(a.insert_date) as year,";
			if ($search_year != 0) $year_cond = " and YEAR(a.insert_date)='$search_year'";
			else $year_cond = "";
		} else if ($db_type == 2) {
			$year_field = "to_char(a.insert_date,'YYYY') as year,";
			if ($search_year != 0) $year_cond = " and to_char(a.insert_date,'YYYY')='$search_year'";
			else $year_cond = "";
		} else
			$year_field = "";


		$arr = array(0 => $company_arr, 1 => $buyer_arr);

		$sql = "select $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by a.id DESC";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,130,50,60", "560", "280", 0, $sql, "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0,", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "", '', '0,0,0,0,0', '');
	} else if ($data[5] == 2) {
		if ($data[1] == 0) {
			if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
				if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_id_cond = " and a.party_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				else $buyer_id_cond = "";
			} else {
				$buyer_id_cond = "";
			}
		} else {
			$buyer_id_cond = " and a.party_id=$data[1]";
		}

		$search_by = $data[2];
		$search_string = "%" . trim($data[3]) . "%";

		if ($search_by == 1)
			$search_field = "b.order_no";
		else if ($search_by == 2)
			$search_field = "b.cust_style_ref";
		else
			$search_field = "a.job_no_prefix_num";

		$start_date = trim($data[4]);
		$end_date = trim($data[5]);

		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond = "and b.delivery_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond = "and b.delivery_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
		} else {
			$date_cond = "";
		}

		$search_year = $data[4];
		if ($db_type == 0) {
			$year_field = "YEAR(a.insert_date)";
			$style_cond = "group_concat(b.cust_style_ref)";
			if ($search_year != 0) $year_cond = " and YEAR(a.insert_date)='$search_year'";
			else $year_cond = "";
		} else if ($db_type == 2) {
			$year_field = "to_char(a.insert_date,'YYYY')";
			$style_cond = "listagg((cast(b.cust_style_ref as varchar2(4000))),',') within group (order by b.cust_style_ref)";
			if ($search_year != 0) $year_cond = " and to_char(a.insert_date,'YYYY')='$search_year'";
			else $year_cond = "";
		} else
			$year_field = ""; //defined Later

		$arr = array(0 => $company_arr, 1 => $buyer_arr);

		$sql = "select $year_field as year, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, $style_cond as cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.party_id, a.insert_date order by a.id DESC";

		echo create_list_view("tbl_list_search", "Company,Party Name,Year,Job No,Cust. Style Ref.", "120,130,50,60", "560", "280", 0, $sql, "js_set_value", "job_no_prefix_num", "", 1, "company_id,party_id,0,0,0,", $arr, "company_id,party_id,year,job_no_prefix_num,cust_style_ref", "", '', '0,0,0,0,0', '');
	}
	exit();
}

$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
$floor_details = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
$reqsn_details = return_library_array("select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id,requisition_no", "knit_id", "requisition_no");
$color_details = return_library_array("select id, color_name from lib_color", "id", "color_name");


$tmplte = explode("**", $data);

if ($tmplte[0] == "viewtemplate") $template = $tmplte[1];
else $template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template == "") $template = 1;

if ($action == "report_generate")
{
	$process = array(&$_POST);

	extract(check_magic_quote_gpc($process));
	$cbo_type = str_replace("'", "", $cbo_type);
	$cbo_year = str_replace("'", "", $cbo_year);
	$txt_job = str_replace("'", "", $txt_job);
	$txt_order = str_replace("'", "", $txt_order);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$cbo_floor_id = str_replace("'", "", $cbo_floor_id);
	$report_type = str_replace("'", "", $report_type);
	$sales_order_no = str_replace("'", "", $txt_sales_order);
	//$cbo_booking_type = str_replace("'", "", $cbo_booking_type);
	$cbo_within_group = str_replace("'", "", $cbo_within_group);
	$cbo_cust_buyer_id = str_replace("'", "", $cbo_cust_buyer_id);
	$txt_program_no = str_replace("'", "", $txt_program_no);

	//var_dump($txt_date_to);


	$sales_order_cond = ($sales_order_no != "") ? " and e.job_no like '%$sales_order_no%' " : "";
	$program_no_cond = ($txt_program_no != "") ? " and a.booking_no='$txt_program_no' " : "";
	$program_no_cond2 = ($txt_program_no != "") ? " and a.program_no='$txt_program_no' " : "";
	//echo $cbo_type.'=='.$report_type;
	$vari_knit_charge_source_arr = sql_select("select editable from variable_order_tracking where company_name=$cbo_company_name and variable_list=70 and status_active=1");
	if ($vari_knit_charge_source_arr[0][csf("editable")] == 2) {
		$vari_knit_charge_source = $vari_knit_charge_source_arr[0][csf("editable")];
	} else {
		$vari_knit_charge_source = 1;
	}

	if ($report_type == 1)
	{
		if ($cbo_type == 1 || $cbo_type == 0) {
			if (str_replace("'", "", $cbo_buyer_name) == 0) $buyer_cond = '';
			else $buyer_cond = " and a.buyer_id=$cbo_buyer_name";

			if (str_replace("'", "", $cbo_cust_buyer_id) == 0) $cust_buyer_cond = '';
			else $cust_buyer_cond = " and e.customer_buyer=$cbo_cust_buyer_id";

			if (str_replace("'", "", $cbo_cust_buyer_id) == 0) $cust_buyer_in_cond = '';
			else $cust_buyer_in_cond = " id=$cbo_cust_buyer_id";


			$cust_buyer_in_bound = sql_select("select id,short_name from lib_buyer where $cust_buyer_in_cond and status_active=1 and is_deleted=0 ");
			$cust_buyer_in_bound_arr = array();
			foreach ($cust_buyer_in_bound as $row)
			{
				$cust_buyer_in_bound_arr[] = "'".$row[csf('short_name')]."'";
			}
			//var_dump($cust_buyer_in_bound_arr);

			if ($cust_buyer_in_bound_arr) $cust_buyer_in_bound_cond = " and d.cust_buyer in(".implode(",",$cust_buyer_in_bound_arr).") ";
			else $cust_buyer_in_bound_cond = "";

			$txt_ir_hdn_booking_no = str_replace("'", "", $txt_ir_hdn_booking_no);

			$booking_no = str_replace("'", "", $txt_booking_no);

			if($booking_no==""){$booking_no=$txt_ir_hdn_booking_no;}else{$booking_no=$booking_no;}
			if ($booking_no != "") $booking_no_cond = " and e.sales_booking_no like '%$booking_no%' ";
			else $booking_no = "";
			if (str_replace("'", "", $cbo_floor_id) == 0) $floor_id = '';
			else $floor_id = " and b.floor_id=$cbo_floor_id";
			if (str_replace("'", "", $txt_booking_no) == "") $booking_cond = '';
			else $booking_cond = " and e.sales_booking_no='$booking_no'";

			if ($db_type == 0) {
				$year_field = "YEAR(f.insert_date)";
				$year_field_sam = "YEAR(a.insert_date)";
				if ($cbo_year != 0) $job_year_cond = " and YEAR(f.insert_date)=$cbo_year";
				else $job_year_cond = "";
			} else if ($db_type == 2) {
				$year_field = "to_char(f.insert_date,'YYYY')";
				$year_field_sam = "to_char(a.insert_date,'YYYY')";
				if ($cbo_year != 0) $job_year_cond = " and to_char(f.insert_date,'YYYY')=$cbo_year";
				else $job_year_cond = "";
			} else $year_field = "";
			$from_date = $txt_date_from;
			if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
			else $to_date = $txt_date_to;

			if (str_replace("'", "", $cbo_knitting_source) == 0) $source = "%%";
			else $source = str_replace("'", "", $cbo_knitting_source);

			$date_con = "";
			if ($from_date != "" && $to_date != "") $date_con = " and a.receive_date between '$from_date' and '$to_date'";
			$machine_details = array();
			$machine_data = sql_select("select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and machine_no is not null");
			//echo "select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0";
			$machine_in_not = array("CC", "GS");
			foreach ($machine_data as $row) {
				$machine_details[$row[csf('id')]]['no'] = $row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia_width'] = $row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
				$machine_details[$row[csf('id')]]['brand'] = $row[csf('brand')];

				if (!in_array($row[csf('machine_no')], $machine_in_not) && ($row[csf('dia_width')] != "" && $row[csf('gauge')] != "")) {
					//if($row[csf('machine_no')]=='GS') echo $row[csf('machine_no')].', ';
					$total_machine[$row[csf('id')]] = $row[csf('id')];
				}
			}
			//print_r($machine_in_not);
			$composition_arr = $construction_arr = array();
			$sql_deter = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array = sql_select($sql_deter);
			if (count($data_array) > 0) {
				foreach ($data_array as $row) {
					if (array_key_exists($row[csf('id')], $composition_arr)) {
						$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					} else {
						$composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					}

					$construction_arr[$row[csf('id')]] = $row[csf('construction')];
					$yarn_type_arr[$row[csf('id')]] = $yarn_type[$row[csf('yarn_type')]];
				}
			}

			$knit_plan_arr = array();
			$plan_data = sql_select("select id, color_range, stitch_length, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
			foreach ($plan_data as $row) {
				$knit_plan_arr[$row[csf('id')]]['cr'] = $row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl'] = $row[csf('stitch_length')];
				$knit_plan_arr[$row[csf('id')]]['machine_dia'] = $row[csf('machine_dia')];
				$knit_plan_arr[$row[csf('id')]]['machine_gg'] = $row[csf('machine_gg')];
			}
		}

		$width = 0;
		$colspan = 0;
		if ($vari_knit_charge_source == 2) {
			$width = 200;
			$colspan = 2;
		}
		$tbl_width = 2720 + $width + count($shift_name) * 60;
		ob_start();
		?>
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:12px"><strong><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
					if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></strong></td>
			</tr>
		</table>
		<div align="left" style="background-color:#E1E1E1; color:#000; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif; width: 945px;"><strong><u><i>In-House + Outbound + Inbound [Knitting Production]</i></u></strong></div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th colspan="12">Job Summary (In-House + Outbound + Inbound)</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="100" rowspan="2">Sales Order<br> No</th>
					<th width="100" rowspan="2">Sales / <br>Booking No</th>
					<th width="100" rowspan="2">IR / IB</th>
					<th width="100" rowspan="2">Cust.Buyer</th>
					<th width="100" rowspan="2">Total No<br> of Machine</th>
					<th width="90" colspan="2">Inhouse</th>
					<th width="140" colspan="2">Outbound-Subcon</th>
					<th width="90" rowspan="2">Inbound-Subcon</th>
					<th width="60" rowspan="2">Total</th>
				</tr>
				<tr>
					<th width="60">WG Yes</th>
					<th>WG No</th>
					<th>WG Yes</th>
					<th>WG No</th>
				</tr>
			</thead>
			<tbody>
				<?
				// within group "yes" & "no", both are considered
				$i = 1;
				if ($db_type == 0) {
					$sql_inhouse = "select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, group_concat(a.remarks) as remarks, group_concat(b.id) as dtls_id, group_concat(b.prod_id) as prod_id, group_concat(b.febric_description_id) as febric_description_id, group_concat(b.gsm) as gsm,group_concat(b.width) as width, group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, group_concat(b.stitch_length) as stitch_length, group_concat(b.brand_id) as brand_id, b.machine_no_id,d.brand as mc_brand, b.floor_id as floor_id,  group_concat(b.color_id) as color_id,  group_concat(b.color_range_id) as color_range_id, group_concat(c.po_breakdown_id) as po_breakdown_id, d.seq_no, d.machine_no as machine_name, group_concat(e.po_number) as po_number, group_concat(e.file_no) as file_no,group_concat(e.grouping) as grouping,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company, group_concat(b.yarn_prod_id) as yarn_prod_id";
					foreach ($shift_name as $key => $val) {
						$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
					}
					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
						where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond $program_no_cond
						group by b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no
						order by a.receive_date,d.seq_no, b.floor_id";
				} else {
					$sql_inhouse = "SELECT * from (
						(select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,nvl(f.booking_type, 1) booking_type, 1 as is_order, f.entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,e.customer_buyer,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id";
					foreach ($shift_name as $key => $val) {
						$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
					}
					$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";



					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,  wo_booking_mst f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_cond $program_no_cond group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, f.booking_type, f.entry_form, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id,e.customer_buyer)";

					$sql_inhouse .= " union all  (select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,nvl(g.booking_type, 1) booking_type, 2 as is_order, g.entry_form_id as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,e.customer_buyer,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id
						";

					foreach ($shift_name as $key => $val) {
						$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
					}
					$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";



					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_cond $program_no_cond  group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, g.booking_type, g.entry_form_id, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id,e.customer_buyer)";

					$sql_inhouse .= " union all (select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,999 as booking_type, 1 as is_order, null as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,e.customer_buyer,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id, sum(case when b.shift_name=1 then c.quantity else 0 end) as qntyshifta, sum(case when b.shift_name=2 then c.quantity else 0 end) as qntyshiftb, sum(case when b.shift_name=3 then c.quantity else 0 end) as qntyshiftc";


					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_cond $program_no_cond group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id,e.customer_buyer)
						) order by knitting_source,receive_date,machine_no_id
						";
					//echo $sql_inhouse;
				}

				if (str_replace("'", "", $cbo_knitting_source) == 0 || str_replace("'", "", $cbo_knitting_source) == 2)
				{
					if ($from_date != "" && $to_date != "") $date_con_sub = " and a.product_date between '$from_date' and '$to_date'";
					else $date_con_sub = "";
					$const_comp_arr = return_library_array("select id, const_comp from lib_subcon_charge", "id", "const_comp");
					//ubcon_ord_mst e //and e.subcon_job=b.job_no and e.subcon_job=d.job_no_mst
					$sql_inhouse_sub = " SELECT 999 as receive_basis,a.insert_date,a.inserted_by, a.product_date as receive_date, a.program_no as booking_no, 999 as booking_type, 1 as is_order, null as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.cons_comp_id as varchar2(4000))),',') within group (order by b.cons_comp_id) as prod_id, 0 as febric_description_id,

						listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm,
						listagg((cast(b.dia_width as varchar2(4000))),',') within group (order by b.dia_width) as width,
						listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot,
						listagg((cast(b.yrn_count_id as varchar2(4000))),',') within group (order by b.yrn_count_id) as yarn_count,
						listagg((cast(b.stitch_len as varchar2(4000))),',') within group (order by b.stitch_len) as stitch_length,
						listagg((cast(b.brand as varchar2(4000))),',') within group (order by b.brand) as brand_id,

						listagg((cast(b.machine_dia as varchar2(4000))),',') within group (order by b.machine_dia) as machine_dia,
						listagg((cast(b.machine_gg as varchar2(4000))),',') within group (order by b.machine_gg) as machine_gg,

						b.machine_id as machine_no_id, b.floor_id as floor_id,
						listagg((cast(nvl(b.color_id,0) as varchar2(4000))),',') within group (order by nvl(b.color_id,0)) as color_id,
						listagg((cast(b.color_range as varchar2(4000))),',') within group (order by b.color_range) as color_range_id, listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as po_breakdown_id, listagg((cast(d.order_no as varchar2(4000))),',') within group (order by d.order_no) as order_nos, d.job_no_mst as job_no, null as sales_booking_no,sum(b.reject_qnty) as reject_qty,0 as is_sales, a.party_id as unit_id,0 as within_group,  2 as knitting_source, a.knitting_company,a.party_id as buyer_id,d.cust_buyer,
						sum(case when b.shift=0 then b.product_qnty else 0 end) as without_shift,
						sum(case when b.shift=1 then b.product_qnty else 0 end) as qntyshifta,
						sum(case when b.shift=2 then b.product_qnty else 0 end) as qntyshiftb,
						sum(case when b.shift=3 then b.product_qnty else 0 end) as qntyshiftc,a.company_id,
						sum(d.rate) as rate, sum(d.amount) as amount
						from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d
						where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0
						and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_in_bound_cond $program_no_cond2
						group by a.product_date,a.knitting_source,a.knitting_company,a.insert_date,a.inserted_by, b.machine_id, b.floor_id, d.job_no_mst, a.party_id,a.company_id,a.program_no,d.cust_buyer

						order by a.product_date, b.machine_id ";

					//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
					$nameArray_inhouse_subcon = sql_select($sql_inhouse_sub);

				}

				//echo $sql_inhouse;
				//$get_booking_buyer = sql_select("select booking_no,buyer_id from wo_booking_mst where status_active=1 and is_deleted=0");
				$get_booking_buyer = sql_select("select booking_no,buyer_id from wo_booking_mst where status_active=1 and is_deleted=0 union all select booking_no,buyer_id from wo_non_ord_samp_booking_mst where status_active = 1 and is_deleted = 0");
				foreach ($get_booking_buyer as $booking_row) {
					$booking_arr[$booking_row[csf("booking_no")]] = $buyer_arr[$booking_row[csf("buyer_id")]];
				}

				//for yarn type
				$nameArray_inhouse = sql_select($sql_inhouse);
				$yarnProdIdArr = array();
				//$product_id_array = array();
				foreach ($nameArray_inhouse as $row) {
					$exp_yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
					foreach ($exp_yarn_prod_id as $key => $val) {
						$yarnProdIdArr[$val] = $val;
					}

					//$product_id_array[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
				}
				//var_dump($yarnProdIdArr);
				// echo "<pre>";
				// print_r($product_id_array);
				$product_details_array = array();
				$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0".where_con_using_array($yarnProdIdArr, '1', 'id');
				//echo $sql;
				$result = sql_select($sql);
				foreach ($result as $row)
				{
					$compos = '';
					if ($row[csf('yarn_comp_percent2nd')] != 0)
					{
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
					}
					else
					{
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
					}

					$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos;
				}

				//var_dump($product_details_array);


				$yarn_type_arr = return_library_array("select id, yarn_type from product_details_master where 1=1 " . where_con_using_array($yarnProdIdArr, '0', 'id'), "id", "yarn_type");
				// echo "<pre>";
				// print_r($yarn_type_arr);
				//end

				$machine_inhouse_array = $total_running_machine = $buyer_wise_production_arr = $job_wise_machine = array();
				foreach ($nameArray_inhouse as $row)
				{
					if ($row[csf('machine_no_id')] != 0)
					{
						if ($row[csf("knitting_source")] != 3)
						{
							$total_running_machine[$row[csf('machine_no_id')]] = $row[csf('machine_no_id')];
						}
						if ($mc_no_check[$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('customer_buyer')]][$row[csf('machine_no_id')]]=="")
						{
							$job_wise_machine[$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('customer_buyer')]]['machine_no_id']++;
							$mc_no_check[$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('customer_buyer')]][$row[csf('machine_no_id')]]=$row[csf('machine_no_id')];
						}

					}
					$machine_inhouse_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
					foreach ($shift_name as $key => $val) {
						$machine_inhouse_qty[$row[csf('machine_no_id')]][$row[csf('receive_date')]] += $row[csf('qntyshift' . strtolower($val))];
					}
					$job_no = $row[csf("job_no")];
					//$buyer_id = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("buyer_id")]];
					//$buyer_wise_production_arr[$buyer_id][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]] += ($row[csf("qntyshifta")]+$row[csf("qntyshiftb")]+$row[csf("qntyshiftc")]+$row[csf("without_shift")]);
					$sales_booking_no = $row[csf('sales_booking_no')];
					$customer_buyer = $row[csf("customer_buyer")];
					$buyer_wise_production_arr[$job_no][$sales_booking_no][$customer_buyer][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] += ($row[csf("qntyshifta")] + $row[csf("qntyshiftb")] + $row[csf("qntyshiftc")] + $row[csf("without_shift")]);
					$booking_nos .= $row[csf("booking_no")] . ",";
					$booking_no_arr[$row[csf('booking_no')]] = "'".$row[csf('sales_booking_no')]."'"; // booking
				}

				foreach ($nameArray_inhouse_subcon as $row)
				{
					if ($row[csf('machine_no_id')] != 0)
					{
						if ($row[csf("knitting_source")] != 3)
						{
							$total_running_machine[$row[csf('machine_no_id')]] = $row[csf('machine_no_id')];
						}
						if ($mc_no_check2[$row[csf('job_no')]][$row[csf('order_nos')]][$row[csf('cust_buyer')]][$row[csf('machine_no_id')]]=="")
						{
							$job_wise_machine[$row[csf('job_no')]][$row[csf('order_nos')]][$row[csf('cust_buyer')]]['machine_no_id']++;
							//$job_wise_machine2[$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('customer_buyer')]]['machine_no'].=$row[csf('machine_no_id')].',';
							$mc_no_check2[$row[csf('job_no')]][$row[csf('order_nos')]][$row[csf('cust_buyer')]][$row[csf('machine_no_id')]]=$row[csf('machine_no_id')];
						}
					}
					$machine_inhouse_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
					foreach ($shift_name as $key => $val) {
						$machine_inhouse_qty[$row[csf('machine_no_id')]][$row[csf('receive_date')]] += $row[csf('qntyshift' . strtolower($val))];
					}

					$job_no = $row[csf("job_no")]; // ($row[csf("within_group")]==1)?$booking_arr[$row[csf("sales_booking_no")]]:$buyer_arr[$row[csf("buyer_id")]];
					//$buyer_wise_production_arr[$buyer_id][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]] += ($row[csf("qntyshifta")]+$row[csf("qntyshiftb")]+$row[csf("qntyshiftc")]+$row[csf("without_shift")]);
					$sales_booking_no = $row[csf('order_nos')];
					$customer_buyer = $row[csf("cust_buyer")];
					$buyer_wise_production_arr[$job_no][$sales_booking_no][$customer_buyer][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] += ($row[csf("qntyshifta")] + $row[csf("qntyshiftb")] + $row[csf("qntyshiftc")]);
				}
				// echo "<pre>";print_r($job_wise_machine2);die;

				$booking_no_arr = array_filter($booking_no_arr);
				if(!empty($booking_no_arr))
				{
					$booking_no_arr = array_filter($booking_no_arr);
					if($db_type==2 && count($booking_no_arr)>999)
					{
						$booking_no_arr_chunk=array_chunk($booking_no_arr,999) ;
						foreach($booking_no_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$bookCond.="  a.booking_no in($chunk_arr_value) or ";
						}

						$all_book_no_cond.=" and (".chop($bookCond,'or ').")";
					}
					else
					{
						$all_book_no_cond=" and a.booking_no in(".implode(",",$booking_no_arr).")";
					}

				}
				$bookingInternalRefSql =sql_select("select a.booking_no,b.grouping from wo_booking_mst x,wo_booking_dtls a,wo_po_break_down b where x.booking_no=a.booking_no and a.po_break_down_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null $all_book_no_cond and x.company_id=$cbo_company_name group by a.booking_no,b.grouping");

				$internalRefArrChk=array();
				foreach($bookingInternalRefSql  as $rows)
				{
					if($internalRefArrChk[$rows[csf("booking_no")]]['grouping']!=$rows[csf("grouping")])
					{
						$internalRefArr[$rows[csf("booking_no")]]['grouping'].=$rows[csf("grouping")].",";
						$internalRefArrChk[$rows[csf("booking_no")]]['grouping']=$rows[csf("grouping")];
					}

				}



				//echo "<pre>";
				//print_r($buyer_wise_production_arr);die;

				$k = 1;
				$sql_result = sql_select($sql_qty);
				$total_no_machine= 0;
				foreach ($buyer_wise_production_arr as $job_no => $job_data)
				{
					foreach ($job_data as $sales_booking_no => $sales_booking_data)
					{
						foreach ($sales_booking_data as $buyer => $rows)
						{

							// echo "<pre>";
							// print_r($buyer);
							$total_no_machine = $job_wise_machine[$job_no][$sales_booking_no][$buyer]['machine_no_id'];

							//var_dump($total_no_machine);
							if ($k % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							$out_bound_qnty = 0;
							$out_bound_qnty_wg_yes = $rows[3][1][1][1] + $rows[3][1][1][2] + $rows[3][999][1][1] + $service_buyer_data[$buyer];
							$out_bound_qnty_wg_no = $rows[3][1][2][1] + $rows[3][1][2][2] + $rows[3][999][2][1] + $service_buyer_data[$buyer];
							$in_bound_qnty = 0;
							$in_bound_qnty = $rows[2][999][0][1];

							//$tot_summ=$rows[1][1][1][1]+$rows[1][999][2][1]+$rows[3][1][1][1]+$rows[3][1][2][1]+$sample_without_order+$sample_with_order;
							//$buyer_id = ($row[csf("within_group")]==1)?$booking_arr[$row[csf("sales_booking_no")]]:$row[csf("buyer_id")];
							$tot_summ = $rows[1][1][1][1] + $rows[1][999][2][1] + $out_bound_qnty_wg_yes + $out_bound_qnty_wg_no +  $in_bound_qnty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center" style="width: 40px;"><? echo $k; ?></td>
								<td align="center" title="">
									<?
									// $data = explode("-",$job_no);
									// echo ltrim($data[3],0);
									echo $job_no;
									?>
								</td>
								<td align="center" title=""><? echo $sales_booking_no; ?></td>
								<td align="center" title="">
								   <p><? echo chop($internalRefArr[$sales_booking_no]['grouping'],","); ?></p>
								</td>
								<td align="center" title="">
									<?
									if($buyer_arr[$buyer] !=""){
										echo $buyer_arr[$buyer];
									}else{
										echo $buyer;
									}
									 ?>
								</td>

								<td align="center" title="">
								<a href='#report_details' onClick="openmypage('<? echo $job_no; ?>','<? echo $sales_booking_no; ?>','<? echo $buyer; ?>','<? echo 1; ?>','machine_popup');"><? echo $total_no_machine; ?></a> </td>
								<td align="right" style="width: 45px;"><? echo number_format($rows[1][1][1][1], 2, '.', ''); // $rows[1][1][1][1]
																		?></td>
								<td align="right" style="width: 45px;"><? echo number_format($rows[1][999][2][1], 2, '.', ''); // $rows[1][1][2][1] ?></td>
								<td align="right"><? echo number_format($out_bound_qnty_wg_yes, 2, '.', ''); ?></td>
								<td align="right"><? echo number_format($out_bound_qnty_wg_no, 2, '.', ''); ?></td>
								<td align="right"><? echo number_format($in_bound_qnty, 2, '.', ''); ?></td>
								<td align="right"><? echo  number_format($tot_summ, 2, '.', ''); ?></td>
							</tr>
							<?
							$tot_qtyinhouse_wg_yes += $rows[1][1][1][1];
							$tot_qtyinhouse_wg_no += $rows[1][999][2][1];
							$tot_qtyoutbound_wg_yes += $out_bound_qnty_wg_yes;
							$tot_qtyoutbound_wg_no += $out_bound_qnty_wg_no;
							$tot_qtyinbound += $in_bound_qnty;
							$total_summ += $tot_summ;
							$total_machine_count += $total_no_machine;
							unset($subcon_buyer_samary[$buyer]);
							$k++;
						}
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5" align="right"><strong>Total</strong></th>
					<th align="center" style="text-align: center;"><? echo $total_machine_count; ?></th>
					<th align="right"><? echo number_format($tot_qtyinhouse_wg_yes, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyinhouse_wg_no, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyoutbound_wg_yes, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyoutbound_wg_no, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyinbound, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($total_summ, 2, '.', ''); ?></th>
				</tr>
				<tr>
					<th colspan="6"><strong>In %</strong></th>
					<th align="right"><? $qtyinhouse_per = ($tot_qtyinhouse_wg_yes / $total_summ) * 100;
										echo number_format($qtyinhouse_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyinhouse_per = ($tot_qtyinhouse_wg_no / $total_summ) * 100;
										echo number_format($qtyinhouse_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyoutbound_per = ($tot_qtyoutbound_wg_yes / $total_summ) * 100;
										echo number_format($qtyoutbound_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyoutbound_per = ($tot_qtyoutbound_wg_no / $total_summ) * 100;
										echo number_format($qtyoutbound_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyinbound_per = ($tot_qtyinbound / $total_summ) * 100;
										echo number_format($qtyinbound_per, 2) . ' %'; ?></th>
					<th align="right"><? echo "100 %"; ?></th>
				</tr>
			</tfoot>
		</table>
		<br />
		<fieldset style="width:<? echo $tbl_width + 20; ?>px;">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_head">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="60" rowspan="2">WG<br />(Yes/No)</th>
						<th width="100" rowspan="2">Knitting Company</th>
						<th width="100" rowspan="2">Floor</th>
						<th width="90" rowspan="2">M/C No.</th>
						<th width="70" rowspan="2">M/C Brand </th>
						<th width="70" rowspan="2">Production Date</th>
						<th width="60" rowspan="2">M/C Dia & Gauge</th>
						<th width="70" rowspan="2">Unit Name</th>
						<th width="70" rowspan="2">Customer</th>
						<th width="70" rowspan="2">Cust.Buyer</th>
						<th width="140" rowspan="2">Program/ Sales Order No</th>
						<th width="140" rowspan="2">Sales / <br>Booking No</th>
						<th width="140" rowspan="2">IR/IB</th>
						<th width="70" rowspan="2">Yarn Count</th>
						<th width="150" rowspan="2">Yarn Composition</th>
						<th width="80" rowspan="2">Yarn Type</th>
						<th width="80" rowspan="2">Brand</th>
						<th width="80" rowspan="2">Lot</th>
						<th width="100" rowspan="2">Body Part</th>
						<th width="100" rowspan="2">Construction</th>
						<th width="150" rowspan="2">Composition</th>
						<th width="130" rowspan="2">Color</th>
						<th width="100" rowspan="2">Color Range</th>
						<th width="60" rowspan="2">Stich</th>
						<th width="60" rowspan="2">Dia</th>
						<th width="60" rowspan="2">GSM</th>
						<th colspan="<? echo count($shift_name); ?>">Production</th>
						<th width="100" rowspan="2">Shift Total</th>
						<?
						if ($vari_knit_charge_source == 2) {
						?>
							<th width="100" rowspan="2">Rate</th>
							<th width="100" rowspan="2">Amount</th>
						<?
						}
						?>
						<th width="100" rowspan="2">Insert User</th>
						<th width="100" rowspan="2">Insert Date and Tiime</th>

						<th width="80" rowspan="2">Reject Qty</th>
						<th rowspan="2"> Remarks</th>
					</tr>
					<tr>
						<?
						$ship_count = 0;
						foreach ($shift_name as $val) {
							$ship_count++;
						?>
							<th width="60"><? echo $val; ?></th>
						<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?
					$lib_userArr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
					$total_grand_rejectQnty = 0;
					$p = 1;
					$q = 1;
					$z = 1;
					if ($cbo_type == 1 || $cbo_type == 0) {
						// echo count($nameArray_inhouse);
						if (count($nameArray_inhouse) > 0) {
							// echo $row[csf("knitting_source")].'D'.$row[csf("body_part_id")];
							$km = 0;
							$tot_reject_qty = 0;
							$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

							foreach ($nameArray_inhouse as $row) {
								//for yarn type
								$yarn_type_name = '';
								$yarn_type_name = getYarnType($yarn_type_arr, $row[csf('yarn_prod_id')]);


								if ($row[csf("knitting_source")] == 3) // Out-bound
								{
									if ($p == 1) {
									?>
										<tr class="tbl_bottom">
											<td colspan="27" align="right"><b>In-house Total (with order)</b></td>
											<?
											foreach ($shift_name as $key => $val) {
											?>
												<td align="right"><? echo number_format($inhouse_ship[$key], 2, '.', ''); ?></td>
											<?
											}
											?>
											<td align="right"><? echo number_format($inhouse_tot_qty, 2, '.', ''); ?></td>
											<?
											if ($vari_knit_charge_source == 2) {
											?>
												<td align="right"></td>
												<td align="right"><? echo number_format($inhouse_tot_charge_amount, 2, '.', ''); ?></td>
											<?
											}
											?>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td align="right"><? echo number_format($grand_reject_qty, 2, '.', ''); ?></td>
											<td>&nbsp;</td>
										</tr>
										<tr bgcolor="#CCCCCC">
											<td colspan="<? echo $ship_count + 33 + $colspan; ?>" align="left"><b>Outbound Subcontract</b></td>
										</tr>
									<?
										$total_grand_rejectQnty += $grand_reject_qty;
										$inhouse_ship = array();
										$inhouse_tot_qty =  $grand_reject_qty = $inhouse_tot_charge_amount = 0;
									}

									if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";

									$count = '';
									$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
									foreach ($yarn_count as $count_id) {
										if ($count == '') $count = $yarn_count_details[$count_id];
										else $count .= "," . $yarn_count_details[$count_id];
									}

									if ($row[csf('receive_basis')] == 2) {
										$machine_dia_gage = $knit_plan_arr[$row[csf('booking_no')]]['machine_dia'] . " X " . $knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
									} else {
										$machine_dia_gage = $machine_details[$row[csf('machine_no_id')]]['dia_width'] . " X " . $machine_details[$row[csf('machine_no_id')]]['gauge'];
									}
									$buyer_id_obs = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("buyer_id")]];
									$cust_buyer_obs = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("customer_buyer")]];

									$machine_brand = $machine_details[$row[csf('machine_no_id')]]['brand'];
									$body_part_ids = array_unique(explode(",", $row[csf('body_part_id')]));
									$body_partName = '';
									foreach ($body_part_ids as $body_id) {
										if ($body_partName == '') $body_partName = $body_part[$body_id];
										else $body_partName .= "," . $body_part[$body_id];
									}

									// $yarn_type_name='';
									// $febric_description_id=array_unique(explode(",",$row[csf('febric_description_id')]));
									// foreach($febric_description_id as $y_id)
									// {
									// 	if($yarn_type_name=='')
									// 		$yarn_type_name=$yarn_type[$y_id];
									// 	else
									// 		$yarn_type_name.=",".$yarn_type[$y_id];
									// }

									$yarn_desc_name='';
									$yarn_prod_id=array_unique(explode(",",$row[csf('yarn_prod_id')]));
									foreach($yarn_prod_id as $y_id)
									{
										if($yarn_desc_name=='')
											$yarn_desc_name=$product_details_array[$y_id]['desc'];
										else
											$yarn_desc_name.=",".$product_details_array[$y_id]['desc'];
									}
									//var_dump($yarn_desc_name);

									//$product_details_array[$row[csf('id')]]['desc'];


									if ($row[csf('knitting_source')] == 1)
										$knitting_party = $company_arr[$row[csf('knitting_company')]];
									else if ($row[csf('knitting_source')] == 3)
										$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
									else
										$knitting_party = "&nbsp;";

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
										<td align="center"><? $km++;
															echo $km;  ?></td>
										<td align="center"><? echo ($row[csf('within_group')] == 1) ? "Yes" : "No"; ?></td>
										<td align="center"><? echo $knitting_party; ?></td>
										<td align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
										<td align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
										<td align="center"></td>
										<td align="center">
											<p><? if ($row[csf('receive_date')] != "" && $row[csf('receive_date')] != "0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p>
										</td>
										<td align="center">
											<p><? echo $machine_dia_gage; ?></p>
										</td>
										<td align="center">
											<p><? if ($row[csf('within_group')] == 1) {
													echo $company_arr[$row[csf('unit_id')]];
												} else {
													echo $company_arr[$row[csf('company_id')]];
												} ?></p>
										</td>
										<td width="70" align="center">
											<p><? echo $buyer_id_obs; ?></p>
										</td>
										<td width="70" align="center">
											<p><? echo $cust_buyer_obs; ?></p>
										</td>
										<td align="center"><strong>P:</strong><? echo $row[csf('booking_no')] . "<br /><strong>S:</strong>" . $row[csf('job_no')]; ?>
										</td>
										<td align="center">
											<p><? echo $row[csf('sales_booking_no')]; ?></p>
										</td>
										<td align="center">
										  <p><? echo chop($internalRefArr[$row[csf("sales_booking_no")]]['grouping'],","); ?></p>
										</td>
										<td align="center">
											<p><? echo $count; ?></p>
										</td>
										<td align="center">
											<p><? echo $yarn_desc_name; ?></p>
										</td>
										<td align="center">
											<p><? echo $yarn_type_name; ?></p>
										</td>
										<td>
											<P>
												<?
												$brand_arr = array_unique(explode(",", $row[csf('brand_id')]));
												$all_brand = "";
												foreach ($brand_arr as $id) {
													$all_brand .= $brand_details[$id] . ",";
												}
												$all_brand = chop($all_brand, " , ");
												echo $all_brand;
												?>&nbsp;
											</P>
										</td>
										<td align="center">
											<P><? echo implode(",", array_unique(explode(",", $row[csf('yarn_lot')]))); ?>&nbsp;</P>
										</td>
										<td width="100" align="center">
											<p><? echo $body_partName; ?></p>
										</td>
										<td>
											<P>
												<?
												$description_arr = array_unique(explode(",", $row[csf('febric_description_id')]));
												$all_construction = "";
												foreach ($description_arr as $id) {
													$all_construction .= $construction_arr[$id] . ",";
												}
												$all_construction = chop($all_construction, " , ");
												echo $all_construction;
												?></P>
										</td>
										<td>
											<P>
												<?
												$all_composition = "";
												foreach ($description_arr as $id) {
													$all_composition .= $composition_arr[$id] . ",";
												}
												$all_composition = chop($all_composition, " , ");
												echo $all_composition;
												?>&nbsp;</P>
										</td>
										<td>
											<P>
												<?
												$color_arr = array_unique(explode(",", $row[csf('color_id')]));
												$all_color = "";
												foreach ($color_arr as $id) {
													$all_color .= $color_details[$id] . ", ";
												}
												$all_color = chop($all_color, " , ");
												echo $all_color;
												?>&nbsp;</P>
										</td>
										<td>
											<P>
												<?
												$color_range_arr = array_unique(explode(",", $row[csf('color_range_id')]));
												$all_color_range = "";
												foreach ($color_range_arr as $id) {
													$all_color_range .= $color_range[$id] . ",";
												}
												$all_color_range = chop($all_color_range, " , ");
												echo $all_color_range;
												?>&nbsp;</P>
										</td>
										<td align="center">
											<p><? echo  implode(",", array_unique(explode(",", $row[csf('stitch_length')]))); ?>&nbsp;</p>
										</td>
										<td width="60" align="center">
											<p><? echo  implode(",", array_unique(explode(",", $row[csf('width')]))); ?>&nbsp;</p>
										</td>
										<td width="60" align="center">
											<p><? echo  implode(",", array_unique(explode(",", $row[csf('gsm')]))); ?>&nbsp;</p>
										</td>
										<?
										$row_tot_roll = 0;
										$row_tot_qnty = 0;
										foreach ($shift_name as $key => $val) {
											$row_tot_qnty += $row[csf('qntyshift' . strtolower($val))];
										?>
											<td align="right" title="<? echo $key . "==" . $val; ?> "><? echo number_format($row[csf('qntyshift' . strtolower($val))], 2); ?> </td>
										<?
											$grand_total_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
											$inhouse_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
										}
										?>
										<td align="right">
											<a href='#report_details' onClick="openmypageshift('<? echo $row[csf('dtls_id')]; ?>','<? echo 1; ?>','shift_qty_popup');"><? echo number_format($row_tot_qnty, 2, '.', ''); ?></a>
										</td>

										<?
										if ($vari_knit_charge_source == 2) {
										?>
											<td align="right"><? echo number_format($row[csf('kniting_charge')], 2, '.', ''); ?></td>
											<td align="right"><? $charge_amount = $row_tot_qnty * $row[csf('kniting_charge')];
																echo number_format($charge_amount, 2, '.', ''); ?></td>
										<?
										}
										?>

										<td width="100" align="center">
											<p><? echo $lib_userArr[$row[csf('inserted_by')]]; ?>&nbsp;</p>
										</td>
										<td width="100" align="center">
											<p><? echo $row[csf('insert_date')]; ?>&nbsp;</p>
										</td>

										<td align="right">
											<p><? echo number_format($row[csf('reject_qty')], 2, '.', ''); ?></p>
										</td>
										<td>
											<p><? echo $row[csf('remarks')]; ?>&nbsp;</p>
										</td>
									</tr>

									<?
									$inhouse_tot_qty += $row_tot_qnty;
									$grand_tot_qnty += $row_tot_qnty;
									$inhouse_tot_charge_amount += $charge_amount;
									$grand_tot_charge_amount += $charge_amount;
									$grand_reject_qty += $row[csf('reject_qty')];
									$outBoundWithoutShiftQnty += $row[csf('without_shift')];
									$i++;

									$p++;
								} else // In-house
								{
									if ($q == 1) {
									?>
										<tr bgcolor="#CCCCCC">
											<td colspan="<? echo $ship_count + 33 + $colspan; ?>" align="left"><b>In-House</b></td>
										</tr>
									<?
									}
									if ($row[csf('machine_no_id')] != 0) {

										if ($i % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";
										$count = '';
										$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
										foreach ($yarn_count as $count_id) {
											if ($count == '') $count = $yarn_count_details[$count_id];
											else $count .= "," . $yarn_count_details[$count_id];
										}

										if ($row[csf('receive_basis')] == 2) {
											$machine_dia_gage = $knit_plan_arr[$row[csf('booking_no')]]['machine_dia'] . " X " . $knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
										} else {
											$machine_dia_gage = $machine_details[$row[csf('machine_no_id')]]['dia_width'] . " X " . $machine_details[$row[csf('machine_no_id')]]['gauge'];
										}
										$buyer_id_inhouse = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("buyer_id")]];
										$cust_buyer_inhouse = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("customer_buyer")]];

										$machine_brand = $machine_details[$row[csf('machine_no_id')]]['brand'];
										$body_part_ids = array_unique(explode(",", $row[csf('body_part_id')]));
										$body_partName = '';
										foreach ($body_part_ids as $body_id) {
											if ($body_partName == '')
												$body_partName = $body_part[$body_id];
											else
												$body_partName .= "," . $body_part[$body_id];
										}

										/*$yarn_type_name='';
												$febric_description_id=array_unique(explode(",",$row[csf('febric_description_id')]));
												foreach($febric_description_id as $y_id)
												{
													if($yarn_type_name=='') $yarn_type_name=$yarn_type[$y_id]; else $yarn_type_name.=",".$yarn_type[$y_id];
												}*/
										$yarn_desc_name='';
										$yarn_prod_id=array_unique(explode(",",$row[csf('yarn_prod_id')]));
										foreach($yarn_prod_id as $y_id)
										{
											if($yarn_desc_name=='')
												$yarn_desc_name=$product_details_array[$y_id]['desc'];
											else
												$yarn_desc_name.=",".$product_details_array[$y_id]['desc'];
										}
										//var_dump($yarn_desc_name);

										//$product_details_array[$row[csf('id')]]['desc'];

										if ($row[csf('knitting_source')] == 1)
											$knitting_party = $company_arr[$row[csf('knitting_company')]];
										else if ($row[csf('knitting_source')] == 3)
											$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party = "&nbsp;";

									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
											<?
											$km++;
											?>
											<td align="center" style="width: 40px;"><? echo $km; ?></td>
											<td align="center"><? echo ($row[csf('within_group')] == 1) ? "Yes" : "No"; ?></td>
											<td align="center">
												<p><? echo $knitting_party; ?></p>
											</td>
											<td align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
											<td align="center">
												<p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p>
											</td>
											<td align="center">
												<p><? echo $machine_brand; ?></p>
											</td>
											<td align="center">
												<p><? if ($row[csf('receive_date')] != "" && $row[csf('receive_date')] != "0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p>
											</td>
											<td align="center">
												<p><? echo $machine_dia_gage; ?></p>
											</td>
											<td align="center">
												<p><? if ($row[csf('within_group')] == 1) {
														echo $company_arr[$row[csf('unit_id')]];
													} else {
														echo $company_arr[$row[csf('company_id')]];
													} ?></p>
											</td>
											<td width="70" align="center">
												<p><? echo $buyer_id_inhouse; ?></p>
											</td>
											<td width="70" align="center">
												<p><? echo $cust_buyer_inhouse; ?></p>
											</td>
											<td align="center"><strong>P:</strong><? echo $row[csf('booking_no')] . "<br /><strong>S:</strong>" . $row[csf('job_no')]; ?>
											</td>
											<td align="center">
												<p><? echo $row[csf('sales_booking_no')]; ?></p>
											</td>
											<td align="center">
												<p><? echo chop($internalRefArr[$row[csf("sales_booking_no")]]['grouping'],","); ?></p>
											</td>
											<td align="center">
												<p><? echo $count; ?></p>
											</td>
											<td align="center">
												<p><? echo $yarn_desc_name; ?></p>
											</td>
											<td align="center">
												<p><? echo $yarn_type_name; ?></p>
											</td>
											<td>
												<P>
													<?
													$brand_arr = array_unique(explode(",", $row[csf('brand_id')]));
													$all_brand = "";
													foreach ($brand_arr as $id) {
														$all_brand .= $brand_details[$id] . ",";
													}
													$all_brand = chop($all_brand, " , ");
													echo $all_brand;
													?>&nbsp;
												</P>
											</td>
											<td align="center">
												<P><? echo implode(",", array_unique(explode(",", $row[csf('yarn_lot')]))); ?>&nbsp;</P>
											</td>
											<td width="100" align="center">
												<p><? echo $body_partName; ?></>
											</td>
											<td>
												<P>
													<?
													$description_arr = array_unique(explode(",", $row[csf('febric_description_id')]));
													$all_construction = "";
													foreach ($description_arr as $id) {
														$all_construction .= $construction_arr[$id] . ",";
													}
													$all_construction = chop($all_construction, " , ");
													echo $all_construction;
													?></P>
											</td>
											<td>
												<P>
													<?
													$all_composition = "";
													foreach ($description_arr as $id) {
														$all_composition .= $composition_arr[$id] . ",";
													}
													$all_composition = chop($all_composition, " , ");
													echo $all_composition;
													?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_arr = array_unique(explode(",", $row[csf('color_id')]));
													$all_color = "";
													foreach ($color_arr as $id) {
														$all_color .= $color_details[$id] . ", ";
													}
													$all_color = chop($all_color, " , ");
													echo $all_color;
													?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_range_arr = array_unique(explode(",", $row[csf('color_range_id')]));
													$all_color_range = "";
													foreach ($color_range_arr as $id) {
														$all_color_range .= $color_range[$id] . ",";
													}
													$all_color_range = chop($all_color_range, " , ");
													echo $all_color_range;
													?>&nbsp;</P>
											</td>
											<td align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('stitch_length')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('width')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('gsm')]))); ?>&nbsp;</p>
											</td>
											<?
											$row_tot_roll = 0;
											$row_tot_qnty = 0;
											foreach ($shift_name as $key => $val) {
												$row_tot_qnty += $row[csf('qntyshift' . strtolower($val))];
											?>
												<td align="right"><? echo number_format($row[csf('qntyshift' . strtolower($val))], 2); ?> </td>
											<?
												$grand_total_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
												$inhouse_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
											}
											?>
											<td align="right">
											<a href='#report_details' onClick="openmypageshift('<? echo $row[csf('dtls_id')]; ?>','<? echo 1; ?>','shift_qty_popup');"><? echo number_format($row_tot_qnty, 2, '.', ''); ?></a> </td>


											<?
											if ($vari_knit_charge_source == 2) {
											?>
												<td align="right"><? echo number_format($row[csf('kniting_charge')], 2, '.', ''); ?></td>
												<td align="right"><? $charge_amount = $row_tot_qnty * $row[csf('kniting_charge')];
																	echo number_format($charge_amount, 2, '.', ''); ?></td>
											<?
											}
											?>

											<td width="100" align="center">
												<p><? echo $lib_userArr[$row[csf('inserted_by')]]; ?>&nbsp;</p>
											</td>
											<td width="100" align="center">
												<p><? echo $row[csf('insert_date')]; ?>&nbsp;</p>
											</td>

											<td align="right">
												<p><? echo number_format($row[csf('reject_qty')], 2, '.', ''); ?></p>
											</td>
											<td>
												<p><? echo $row[csf('remarks')]; ?>&nbsp;</p>
											</td>
										</tr>
									<?
										$inhouse_tot_qty += $row_tot_qnty;
										$grand_tot_qnty += $row_tot_qnty;
										$inhouse_tot_charge_amount += $charge_amount;
										$grand_tot_charge_amount += $charge_amount;
										$grand_reject_qty += $row[csf('reject_qty')];
										$i++;
									}
									$q++;
								}
							}
							?>
							<tr class="tbl_bottom">
								<td colspan="27" align="right"><b>Outbound Total (with order)</b></td>
								<?
								foreach ($shift_name as $key => $val) {
								?>
									<td align="right"><? echo number_format($inhouse_ship[$key], 2, '.', ''); ?></td>
								<?
								}
								?>
								<td align="right"><? echo number_format($inhouse_tot_qty + $outBoundWithoutShiftQnty, 2, '.', ''); ?></td>

								<?
								if ($vari_knit_charge_source == 2) {
								?>
									<td align="right"></td>
									<td align="right"><? echo number_format($inhouse_tot_charge_amount, 2, '.', ''); ?></td>
								<?
								}
								?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($grand_reject_qty, 2, '.', ''); ?></td>
								<td>&nbsp;</td>
							</tr>
							<?
							$total_grand_rejectQnty += $grand_reject_qty;
						}
						// if ($cbo_booking_type == 0) // In-Bound Subcontract
						// {
							if (count($nameArray_inhouse_subcon) > 0)
							{
								$km = 0;
								$tot_reject_qty = 0;
								$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
								foreach ($nameArray_inhouse_subcon as $row) {
									if ($z == 1) {
									?>
										<tr bgcolor="#CCCCCC">
											<td colspan="<? echo $ship_count + 33 + $colspan; ?>" align="left"><b>In-Bound Subcontract</b></td>
										</tr>
									<?
									}
									if ($row[csf('machine_no_id')] != 0) {

										if ($i % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";

										$count = '';
										$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
										foreach ($yarn_count as $count_id) {
											if ($count == '') $count = $yarn_count_details[$count_id];
											else $count .= "," . $yarn_count_details[$count_id];
										}

										$machine_dia = implode(',', array_unique(explode(",", $row[csf('machine_dia')])));

										$machine_gg = implode(',', array_unique(explode(",", $row[csf('machine_gg')])));

										//$machine_dia_gage=$machine_details[$row[csf('machine_no_id')]]['dia_width']." X ".$machine_details[$row[csf('machine_no_id')]]['gauge'];
										$machine_dia_gage = $machine_dia . " X " . $machine_gg;

										$buyer_id_ibs = $buyer_arr[$row[csf("buyer_id")]];
										$cust_buyer_id_ibs = $row[csf("cust_buyer")];

										/*$febric_description_id=array_unique(explode(",",$row[csf('febric_description_id')]));
												$yarn_type_name='';
												foreach($febric_description_id as $y_id)
												{
													if($yarn_type_name=='') $yarn_type_name=$yarn_type[$y_id]; else $yarn_type_name.=",".$yarn_type[$y_id];
												}*/
										$order_nos = implode(',', array_unique(explode(",", $row[csf('order_nos')])));

										$machine_brand = $machine_details[$row[csf('machine_no_id')]]['brand'];
										if ($row[csf('knitting_source')] == 1)
											$knitting_party = $company_arr[$row[csf('knitting_company')]];
										else if ($row[csf('knitting_source')] == 3)
											$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party = "&nbsp;";

									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
											<? $km++; ?>
											<td align="center" style="width: 40px;"><? echo $km; ?></td>
											<td align="center">&nbsp;</td>
											<td align="center"><? echo $knitting_party; ?></td>
											<td align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
											<td align="center">
												<p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p>
											</td>
											<td align="center">
												<p><? echo $machine_brand; ?></p>
											</td>
											<td align="center">
												<p><? if ($row[csf('receive_date')] != "" && $row[csf('receive_date')] != "0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p>
											</td>
											<td align="center">
												<p><? echo $machine_dia_gage; ?></p>
											</td>
											<td align="center">
												<p><? echo $company_arr[$row[csf('company_id')]]; ?></p>
											</td>
											<td width="70" align="center">
												<p><? echo $buyer_id_ibs; ?></>
											</td>
											<td width="70" align="center">
												<p><? echo $cust_buyer_id_ibs; ?></p>
											</td>
											<td align="center"><strong>P:</strong><? echo $row[csf('booking_no')] . "<br /><strong>S:</strong>" . $row[csf('job_no')]; ?></td>

											<td align="center"><? echo $order_nos; ?></td>
											<td align="center">

											</td>
											<td align="center">
												<p><? echo $count; ?></p>
											</td>
											<td align="center">
												<p><? //echo $count; ?></p>
											</td>
											<td align="center">
												<p><? //echo $count;
													?></p>
											</td>
											<td>
												<P><? $brand_data = implode(",", array_unique(explode(",", $row[csf('brand_id')])));
													echo $brand_data; ?>&nbsp; </P>
											</td>

											<td align="center">
												<P><? echo implode(",", array_unique(explode(",", $row[csf('yarn_lot')]))); ?>&nbsp;</P>
											</td>
											<td width="100" align="center">
												<p>&nbsp; </p>
											</td>
											<td colspan="2">
												<P>
													<?
													$prod_id_arr = array_unique(explode(",", $row[csf('prod_id')]));
													$all_prod = "";
													foreach ($prod_id_arr as $id) {
														$all_prod .= $const_comp_arr[$id] . ", ";
													}
													$all_prod = chop($all_prod, " , ");
													echo $all_prod; ?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_arr = array_unique(explode(",", $row[csf('color_id')]));
													$all_color = "";
													foreach ($color_arr as $id) {
														$all_color .= $color_details[$id] . ", ";
													}
													$all_color = chop($all_color, " , ");
													echo $all_color; ?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_range_arr = array_unique(explode(",", $row[csf('color_range_id')]));
													$all_color_range = "";
													foreach ($color_range_arr as $id) {
														$all_color_range .= $color_range[$id] . ",";
													}
													$all_color_range = chop($all_color_range, " , ");
													echo $all_color_range; ?>&nbsp;</P>
											</td>
											<td align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('stitch_length')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('width')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('gsm')]))); ?>&nbsp;</p>
											</td>
											<?
											$row_tot_roll = 0;
											$row_tot_qnty = 0;
											foreach ($shift_name as $key => $val) {
												$row_tot_qnty += $row[csf('qntyshift' . strtolower($val))];
											?>
												<td align="right"><? echo number_format($row[csf('qntyshift' . strtolower($val))], 2); ?> </td>
											<?
												$grand_total_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
												$inbound_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
											}
											?>
											<td align="right">
											<a href='#report_details' onClick="openmypageshift('<? echo $row[csf('dtls_id')]; ?>','<? echo 1; ?>','shift_qty_popup');"><? echo number_format($row_tot_qnty, 2, '.', ''); ?></a> </td>

											<?
											if ($vari_knit_charge_source == 2) {
											?>
												<td align="right"><? echo number_format($row[csf('rate')], 2, '.', ''); ?></td>
												<td align="right"><? echo number_format($row_tot_qnty * $row[csf('rate')], 2, '.', ''); ?></td>
											<?
											}
											?>

											<td width="100" align="center">
												<p><? echo $lib_userArr[$row[csf('inserted_by')]]; ?>&nbsp;</p>
											</td>
											<td width="100" align="center">
												<p><? echo $row[csf('insert_date')]; ?>&nbsp;</p>
											</td>
											<td align="right">
												<p><? echo number_format($row[csf('reject_qty')], 2, '.', ''); ?></p>
											</td>
											<td>
												<p><? echo $row[csf('remarks')]; ?>&nbsp;</p>
											</td>
										</tr>
								<?
										$inbound_tot_qty += $row_tot_qnty;
										$grand_tot_qnty += $row_tot_qnty;
										$inbound_tot_amount += $row_tot_qnty * $row[csf('rate')];
										$grand_tot_charge_amount += $row_tot_qnty * $row[csf('rate')];
										$inboundreject_qty += $row[csf('reject_qty')];
										$grand_reject_qty += $row[csf('reject_qty')];
										$i++;
									}
									$z++;
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="27" align="right"><b>Inbound Total</b></td>
									<?
									foreach ($shift_name as $key => $val) {
									?>
										<td align="right"><? echo number_format($inbound_ship[$key], 2, '.', ''); ?></td>
									<?
									}
									?>
									<td align="right"><? echo number_format($inbound_tot_qty, 2, '.', ''); ?></td>
									<?
									if ($vari_knit_charge_source == 2) {
									?>
										<td align="right"></td>
										<td align="right"><? echo number_format($inbound_tot_amount, 2, '.', ''); ?></td>
									<?
									}
									?>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right"><? echo number_format($inboundreject_qty, 2, '.', ''); ?></td>
									<td>&nbsp;</td>
								</tr>
								<?
								$total_grand_rejectQnty += $inboundreject_qty;
							}
						//}
					}
					$j = 0;
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="27">Grand Total</th>
						<?
						foreach ($shift_name as $key => $val) {
						?>
							<th align="right" width="60"><? echo number_format($grand_total_ship[$key], 2, '.', ''); ?></th>
						<?
						}
						?>
						<th align="right"><? echo number_format($grand_tot_qnty + $outBoundWithoutShiftQnty, 2, '.', ''); ?></th>
						<?
						if ($vari_knit_charge_source == 2) {
						?>
							<th align="right"></th>
							<th align="right"><? echo number_format($grand_tot_charge_amount, 2, '.', ''); ?></th>
						<?
						}
						?>

						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? echo number_format($total_grand_rejectQnty, 2, '.', ''); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
			<br />
			<?
				if ($txt_date_from != "") {
					if ($txt_date_to == "") $txt_date_to = $txt_date_from;
					$date_distance = datediff("d", $txt_date_from, $txt_date_to);
					$month_name = date('F', strtotime($txt_date_from));
					$year_name = date('Y', strtotime($txt_date_from));
					$day_of_month = explode("-", $txt_date_from);
					if ($db_type == 0) {
						$fist_day_of_month = $day_of_month[2] * 1;
					} else {
						$fist_day_of_month = $day_of_month[0] * 1;
					}

					$tot_machine = count($total_machine);
					$running_machine = count($total_running_machine);
					$stop_machine = $tot_machine - $running_machine;
					$running_machine_percent = (($running_machine / $tot_machine) * 100);
					$stop_machine_percent = (($stop_machine / $tot_machine) * 100);
					if ($date_distance == 1 && $fist_day_of_month > 1) {
						$query_cond_month = date('m', strtotime($txt_date_from));
						$query_cond_year = date('Y', strtotime($txt_date_from));
						$sql_cond = "";
						if ($db_type == 0) $sql_cond = "  and month(a.receive_date)='$query_cond_month' and year(a.receive_date)='$query_cond_year'";
						else $sql_cond = "  and to_char(a.receive_date,'mm')='$query_cond_month' and to_char(a.receive_date,'yyyy')='$query_cond_year'";
						if ($from_date != "" && $to_date != "") $date_con = " and a.receive_date between '$from_date' and '$to_date'";
						$sql_montyly_inhouse = sql_select("select sum(c.quantity ) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date<'" . $txt_date_from . "' $sql_cond");


						$sql_monthly_wout_order = sql_select("select sum( b.grey_receive_qnty) as qnty  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 and a.receive_date<'" . $txt_date_from . "' $sql_cond");

						$yesterday_prod = $sql_montyly_inhouse[0][csf("qnty")] + $sql_monthly_wout_order[0][csf("qnty")];
						$today_prod = $yesterday_prod + $grand_tot_qnty;
					}
			?>
				<table width="<? echo $tbl_width; ?>">
					<tr>
						<td width="25%" valign="top">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
								<tr>
									<td>Total number of m/c running </td>
									<td width="100" align="right"><? echo $running_machine; ?></td>
									<td align="right" width="100"><? if ($running_machine_percent > 0) echo number_format($running_machine_percent, 2, '.', '') . " % "; ?></td>
								</tr>
								<tr>
									<td>Total number of m/c stop</td>
									<td align="right"><? echo $stop_machine; ?></td>
									<td align="right"><? if ($running_machine_percent > 0) echo number_format($stop_machine_percent, 2, '.', '') . " % "; ?></td>
								</tr>
								<tr>
									<td>Total production</td>
									<td align="right"><? echo number_format($grand_tot_qnty, 2); ?></td>
									<td align="center">Kg</td>
								</tr>
							</table>
						</td>
						<td width="10%" valign="top">&nbsp; </td>
						<td width="25%" valign="top">
							<?
							if ($date_distance == 1 && $fist_day_of_month > 1) {
							?>
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
									<tr>
										<td>Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td align="right" width="100"><? echo number_format($yesterday_prod, 2); ?></td>
										<td align="center" width="100">Kg</td>
									</tr>
									<tr>
										<td>Upto today production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td align="right"><? echo number_format($today_prod, 2); ?> </td>
										<td align="center">Kg</td>
									</tr>
								</table>
							<?
							}
							?>
						</td>
						<td valign="top">&nbsp; </td>
					</tr>
				</table>
			<?
				}
			?>
		</fieldset>
		<br>
		<?
		foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		echo "$total_data####$filename";
		exit();
	}
	else
	{
		if ($cbo_type == 1 || $cbo_type == 0) {
			if (str_replace("'", "", $cbo_buyer_name) == 0) $buyer_cond = '';
			else $buyer_cond = " and a.buyer_id=$cbo_buyer_name";
			if (str_replace("'", "", $cbo_cust_buyer_id) == 0) $cust_buyer_cond = '';
			else $cust_buyer_cond = " and e.customer_buyer=$cbo_cust_buyer_id";

			if (str_replace("'", "", $cbo_cust_buyer_id) == 0) $cust_buyer_in_cond = '';
			else $cust_buyer_in_cond = " id=$cbo_cust_buyer_id";


			$cust_buyer_in_bound = sql_select("select id,short_name from lib_buyer where $cust_buyer_in_cond and status_active=1 and is_deleted=0 ");
			$cust_buyer_in_bound_arr = array();
			foreach ($cust_buyer_in_bound as $row)
			{
				$cust_buyer_in_bound_arr[] = "'".$row[csf('short_name')]."'";
			}
			//var_dump($cust_buyer_in_bound_arr);

			if ($cust_buyer_in_bound_arr) $cust_buyer_in_bound_cond = " and d.cust_buyer in(".implode(",",$cust_buyer_in_bound_arr).") ";
			else $cust_buyer_in_bound_cond = "";

			$booking_no = str_replace("'", "", $txt_booking_no);
			if ($booking_no != "") $booking_no_cond = " and e.sales_booking_no like '%$booking_no%' ";
			else $booking_no = "";
			if (str_replace("'", "", $cbo_floor_id) == 0) $floor_id = '';
			else $floor_id = " and b.floor_id=$cbo_floor_id";
			if (str_replace("'", "", $txt_booking_no) == "") $booking_cond = '';
			else $booking_cond = " and e.sales_booking_no='$booking_no'";

			if ($db_type == 0) {
				$year_field = "YEAR(f.insert_date)";
				$year_field_sam = "YEAR(a.insert_date)";
				if ($cbo_year != 0) $job_year_cond = " and YEAR(f.insert_date)=$cbo_year";
				else $job_year_cond = "";
			} else if ($db_type == 2) {
				$year_field = "to_char(f.insert_date,'YYYY')";
				$year_field_sam = "to_char(a.insert_date,'YYYY')";
				if ($cbo_year != 0) $job_year_cond = " and to_char(f.insert_date,'YYYY')=$cbo_year";
				else $job_year_cond = "";
			} else $year_field = "";
			$from_date = $txt_date_from;
			if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
			else $to_date = $txt_date_to;

			if (str_replace("'", "", $cbo_knitting_source) == 0) $source = "%%";
			else $source = str_replace("'", "", $cbo_knitting_source);

			$date_con = "";
			if ($from_date != "" && $to_date != "") $date_con = " and a.receive_date between '$from_date' and '$to_date'";
			$machine_details = array();
			$machine_data = sql_select("select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and machine_no is not null");
			//echo "select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0";
			$machine_in_not = array("CC", "GS");
			foreach ($machine_data as $row) {
				$machine_details[$row[csf('id')]]['no'] = $row[csf('machine_no')];
				$machine_details[$row[csf('id')]]['dia_width'] = $row[csf('dia_width')];
				$machine_details[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
				$machine_details[$row[csf('id')]]['brand'] = $row[csf('brand')];

				if (!in_array($row[csf('machine_no')], $machine_in_not) && ($row[csf('dia_width')] != "" && $row[csf('gauge')] != "")) {
					//if($row[csf('machine_no')]=='GS') echo $row[csf('machine_no')].', ';
					$total_machine[$row[csf('id')]] = $row[csf('id')];
				}
			}
			//print_r($machine_in_not);
			$composition_arr = $construction_arr = array();
			$sql_deter = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array = sql_select($sql_deter);
			if (count($data_array) > 0) {
				foreach ($data_array as $row) {
					if (array_key_exists($row[csf('id')], $composition_arr)) {
						$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					} else {
						$composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					}

					$construction_arr[$row[csf('id')]] = $row[csf('construction')];
					$yarn_type_arr[$row[csf('id')]] = $yarn_type[$row[csf('yarn_type')]];
				}
			}

			$knit_plan_arr = array();
			$plan_data = sql_select("select id, color_range, stitch_length, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
			foreach ($plan_data as $row) {
				$knit_plan_arr[$row[csf('id')]]['cr'] = $row[csf('color_range')];
				$knit_plan_arr[$row[csf('id')]]['sl'] = $row[csf('stitch_length')];
				$knit_plan_arr[$row[csf('id')]]['machine_dia'] = $row[csf('machine_dia')];
				$knit_plan_arr[$row[csf('id')]]['machine_gg'] = $row[csf('machine_gg')];
			}
		}

		$width = 0;
		$colspan = 0;
		if ($vari_knit_charge_source == 2) {
			$width = 200;
			$colspan = 2;
		}
		$tbl_width = 2580 + $width + count($shift_name) * 60;
		ob_start();
		?>
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:12px"><strong><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
																																			if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></strong></td>
			</tr>
		</table>
		<div align="left" style="background-color:#E1E1E1; color:#000; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif; width: 645px;"><strong><u><i>In-House + Outbound + Inbound [Knitting Production]</i></u></strong></div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th colspan="9">Cust.Buyer Summary (In-House + Outbound + Inbound)</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="100" rowspan="2">Cust.Buyer</th>
					<th width="100" rowspan="2">Total No<br> of Machine</th>
					<th width="90" colspan="2">Inhouse</th>
					<th width="140" colspan="2">Outbound-Subcon</th>
					<th width="90" rowspan="2">Inbound-Subcon</th>
					<th width="60" rowspan="2">Total</th>
				</tr>
				<tr>
					<th width="60">WG Yes</th>
					<th>WG No</th>
					<th>WG Yes</th>
					<th>WG No</th>
				</tr>
			</thead>
			<tbody>
				<?
				// within group "yes" & "no", both are considered
				$i = 1;
				if ($db_type == 0) {
					$sql_inhouse = "select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no, max(a.buyer_id) as buyer_id, group_concat(a.remarks) as remarks, group_concat(b.id) as dtls_id, group_concat(b.prod_id) as prod_id, group_concat(b.febric_description_id) as febric_description_id, group_concat(b.gsm) as gsm,group_concat(b.width) as width, group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, group_concat(b.stitch_length) as stitch_length, group_concat(b.brand_id) as brand_id, b.machine_no_id,d.brand as mc_brand, b.floor_id as floor_id,  group_concat(b.color_id) as color_id,  group_concat(b.color_range_id) as color_range_id, group_concat(c.po_breakdown_id) as po_breakdown_id, d.seq_no, d.machine_no as machine_name, group_concat(e.po_number) as po_number, group_concat(e.file_no) as file_no,group_concat(e.grouping) as grouping,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company, group_concat(b.yarn_prod_id) as yarn_prod_id";
					foreach ($shift_name as $key => $val) {
						$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
					}
					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f
						where a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and e.job_no_mst=f.job_no and c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $job_cond $order_cond $job_year_cond
						group by b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no, b.machine_no_id,d.brand, b.floor_id, d.seq_no, d.machine_no
						order by a.receive_date,d.seq_no, b.floor_id";
				} else {
					$sql_inhouse = "select * from (
						(select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,nvl(f.booking_type, 1) booking_type, 1 as is_order, f.entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,e.customer_buyer,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id";
					foreach ($shift_name as $key => $val) {
						$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
					}
					$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";


					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,  wo_booking_mst f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_cond group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, f.booking_type, f.entry_form, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id,e.customer_buyer)";

					$sql_inhouse .= " union all  (select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,nvl(g.booking_type, 1) booking_type, 2 as is_order, g.entry_form_id as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,e.customer_buyer,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id
						";

					foreach ($shift_name as $key => $val) {
						$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
					}
					$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";


					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_cond group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, g.booking_type, g.entry_form_id, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id,e.customer_buyer)";

					$sql_inhouse .= " union all (select b.kniting_charge, a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.receive_date, a.booking_no,999 as booking_type, 1 as is_order, null as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.prod_id as varchar2(4000))),',') within group (order by b.prod_id) as prod_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, listagg((cast(b.body_part_id as varchar2(4000))),',') within group (order by b.body_part_id) as body_part_id, listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm, listagg((cast(b.width as varchar2(4000))),',') within group (order by b.width) as width, listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot, listagg((cast(b.yarn_count as varchar2(4000))),',') within group (order by b.yarn_count) as yarn_count, listagg((cast(b.stitch_length as varchar2(4000))),',') within group (order by b.stitch_length) as stitch_length, listagg((cast(b.brand_id as varchar2(4000))),',') within group (order by b.brand_id) as brand_id, b.machine_no_id, b.floor_id as floor_id, listagg((cast(b.color_id as varchar2(4000))),',') within group (order by b.color_id) as color_id, listagg((cast(b.color_range_id as varchar2(4000))),',') within group (order by b.color_range_id) as color_range_id, listagg((cast(c.po_breakdown_id as varchar2(4000))),',') within group (order by c.po_breakdown_id) as po_breakdown_id, e.job_no,e.sales_booking_no,sum(b.reject_fabric_receive) as reject_qty,c.is_sales,e.buyer_id unit_id,e.within_group,a.knitting_source,a.knitting_company,e.buyer_id,e.customer_buyer,sum(case when b.shift_name=0 then c.quantity else 0 end) as without_shift, listagg((cast(b.yarn_prod_id as varchar2(4000))),',') within group (order by b.yarn_prod_id) as yarn_prod_id, sum(case when b.shift_name=1 then c.quantity else 0 end) as qntyshifta, sum(case when b.shift_name=2 then c.quantity else 0 end) as qntyshiftb, sum(case when b.shift_name=3 then c.quantity else 0 end) as qntyshiftc";

					$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name and a.knitting_source like '$source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $floor_id $buyer_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_cond group by b.kniting_charge, b.machine_no_id,a.receive_date,e.job_no,e.sales_booking_no, e.within_group,a.company_id,a.receive_basis,a.insert_date,a.inserted_by, a.booking_no, b.floor_id, a.knitting_source,a.knitting_company,c.is_sales,e.buyer_id,e.customer_buyer)
						) order by knitting_source,customer_buyer,receive_date asc
						";
					//echo $sql_inhouse;
				}

				if (str_replace("'", "", $cbo_knitting_source) == 0 || str_replace("'", "", $cbo_knitting_source) == 2)
				{
					if ($from_date != "" && $to_date != "") $date_con_sub = " and a.product_date between '$from_date' and '$to_date'";
					else $date_con_sub = "";
					$const_comp_arr = return_library_array("select id, const_comp from lib_subcon_charge", "id", "const_comp");
					//ubcon_ord_mst e //and e.subcon_job=b.job_no and e.subcon_job=d.job_no_mst
					$sql_inhouse_sub = " SELECT 999 as receive_basis,a.insert_date,a.inserted_by, a.product_date as receive_date, a.program_no as booking_no, 999 as booking_type, 1 as is_order, null as entry_form, listagg((cast(a.remarks as varchar2(4000))),',') within group (order by a.remarks) as remarks, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.cons_comp_id as varchar2(4000))),',') within group (order by b.cons_comp_id) as prod_id, 0 as febric_description_id,

						listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm,
						listagg((cast(b.dia_width as varchar2(4000))),',') within group (order by b.dia_width) as width,
						listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot,
						listagg((cast(b.yrn_count_id as varchar2(4000))),',') within group (order by b.yrn_count_id) as yarn_count,
						listagg((cast(b.stitch_len as varchar2(4000))),',') within group (order by b.stitch_len) as stitch_length,
						listagg((cast(b.brand as varchar2(4000))),',') within group (order by b.brand) as brand_id,

						listagg((cast(b.machine_dia as varchar2(4000))),',') within group (order by b.machine_dia) as machine_dia,
						listagg((cast(b.machine_gg as varchar2(4000))),',') within group (order by b.machine_gg) as machine_gg,

						b.machine_id as machine_no_id, b.floor_id as floor_id,
						listagg((cast(nvl(b.color_id,0) as varchar2(4000))),',') within group (order by nvl(b.color_id,0)) as color_id,
						listagg((cast(b.color_range as varchar2(4000))),',') within group (order by b.color_range) as color_range_id, listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as po_breakdown_id, listagg((cast(d.order_no as varchar2(4000))),',') within group (order by d.order_no) as order_nos, d.job_no_mst as job_no, null as sales_booking_no,sum(b.reject_qnty) as reject_qty,0 as is_sales, a.party_id as unit_id,0 as within_group,  2 as knitting_source, a.knitting_company,a.party_id as buyer_id,d.cust_buyer,
						sum(case when b.shift=0 then b.product_qnty else 0 end) as without_shift,
						sum(case when b.shift=1 then b.product_qnty else 0 end) as qntyshifta,
						sum(case when b.shift=2 then b.product_qnty else 0 end) as qntyshiftb,
						sum(case when b.shift=3 then b.product_qnty else 0 end) as qntyshiftc,a.company_id,
						sum(d.rate) as rate, sum(d.amount) as amount
						from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d
						where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0
						and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name $date_con_sub $floor_id_cond $buyer_id_cond $job_no_cond $order_no_cond $job_year_sub_cond $sales_order_cond $booking_no_cond $within_group_cond $cust_buyer_in_bound_cond
						group by a.product_date,a.knitting_source,a.knitting_company,a.insert_date,a.inserted_by, b.machine_id, b.floor_id, d.job_no_mst, a.party_id,a.company_id,a.program_no,d.cust_buyer

						order by a.product_date, b.machine_id ";

					//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
					$nameArray_inhouse_subcon = sql_select($sql_inhouse_sub);

				}

				//echo $sql_inhouse;
				//$get_booking_buyer = sql_select("select booking_no,buyer_id from wo_booking_mst where status_active=1 and is_deleted=0");
				$get_booking_buyer = sql_select("select booking_no,buyer_id from wo_booking_mst where status_active=1 and is_deleted=0 union all select booking_no,buyer_id from wo_non_ord_samp_booking_mst where status_active = 1 and is_deleted = 0");
				foreach ($get_booking_buyer as $booking_row) {
					$booking_arr[$booking_row[csf("booking_no")]] = $buyer_arr[$booking_row[csf("buyer_id")]];
				}

				//for yarn type
				$nameArray_inhouse = sql_select($sql_inhouse);
				$yarnProdIdArr = array();
				//$product_id_array = array();
				foreach ($nameArray_inhouse as $row) {
					$exp_yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
					foreach ($exp_yarn_prod_id as $key => $val) {
						$yarnProdIdArr[$val] = $val;
					}

					//$product_id_array[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
				}
				//var_dump($yarnProdIdArr);
				// echo "<pre>";
				// print_r($product_id_array);
				$product_details_array = array();
				$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0".where_con_using_array($yarnProdIdArr, '1', 'id');
				//echo $sql;
				$result = sql_select($sql);
				foreach ($result as $row)
				{
					$compos = '';
					if ($row[csf('yarn_comp_percent2nd')] != 0)
					{
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
					}
					else
					{
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
					}

					$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos;
				}

				//var_dump($product_details_array);


				$yarn_type_arr = return_library_array("select id, yarn_type from product_details_master where 1=1 " . where_con_using_array($yarnProdIdArr, '0', 'id'), "id", "yarn_type");
				// echo "<pre>";
				// print_r($yarn_type_arr);
				//end

				$machine_inhouse_array = $total_running_machine = $cust_buyer_wise_production_arr = $cust_buyer_wise_machine = array();
				foreach ($nameArray_inhouse as $row) {
					if ($row[csf('machine_no_id')] != 0) {
						if ($row[csf("knitting_source")] != 3) {
							$total_running_machine[$row[csf('machine_no_id')]] = $row[csf('machine_no_id')];
							//$job_wise_machine[$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('customer_buyer')]]['machine_no_id']++;

						}
						$cust_buyer_wise_machine[$row[csf('customer_buyer')]]['machine_no_id']++;

					}
					$machine_inhouse_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
					foreach ($shift_name as $key => $val) {
						$machine_inhouse_qty[$row[csf('machine_no_id')]][$row[csf('receive_date')]] += $row[csf('qntyshift' . strtolower($val))];
					}
					$job_no = $row[csf("job_no")];
					//$buyer_id = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("buyer_id")]];
					//$buyer_wise_production_arr[$buyer_id][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]] += ($row[csf("qntyshifta")]+$row[csf("qntyshiftb")]+$row[csf("qntyshiftc")]+$row[csf("without_shift")]);
					$sales_booking_no = $row[csf('sales_booking_no')];
					$customer_buyer = $row[csf("customer_buyer")];
					$cust_buyer_wise_production_arrr[$customer_buyer][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] += ($row[csf("qntyshifta")] + $row[csf("qntyshiftb")] + $row[csf("qntyshiftc")] + $row[csf("without_shift")]);
					$booking_nos .= $row[csf("booking_no")] . ",";
				}

				foreach ($nameArray_inhouse_subcon as $row) {
					if ($row[csf('machine_no_id')] != 0) {
						if ($row[csf("knitting_source")] != 3) {
							$total_running_machine[$row[csf('machine_no_id')]] = $row[csf('machine_no_id')];
							//$job_wise_machine[$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('customer_buyer')]]['machine_no_id']++;

						}
						$cust_buyer_wise_machine[$row[csf('cust_buyer')]]['machine_no_id']++;
					}
					$machine_inhouse_array[$row[csf('machine_no_id')]][$row[csf('receive_date')]]++;
					foreach ($shift_name as $key => $val) {
						$machine_inhouse_qty[$row[csf('machine_no_id')]][$row[csf('receive_date')]] += $row[csf('qntyshift' . strtolower($val))];
					}

					$job_no = $row[csf("job_no")]; // ($row[csf("within_group")]==1)?$booking_arr[$row[csf("sales_booking_no")]]:$buyer_arr[$row[csf("buyer_id")]];
					//$buyer_wise_production_arr[$buyer_id][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]] += ($row[csf("qntyshifta")]+$row[csf("qntyshiftb")]+$row[csf("qntyshiftc")]+$row[csf("without_shift")]);
					$sales_booking_no = $row[csf('sales_booking_no')];
					$customer_buyer = $row[csf("cust_buyer")];
					$cust_buyer_wise_production_arrr[$customer_buyer][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] += ($row[csf("qntyshifta")] + $row[csf("qntyshiftb")] + $row[csf("qntyshiftc")]);
					// $cust_buyer_wise_production_arrr[$customer_buyer][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] .= $row[csf("knitting_source")].', ';

				}

				// echo "<pre>";
				// print_r($cust_buyer_wise_production_arrr);die;

				$k = 1;
				$sql_result = sql_select($sql_qty);

				$total_no_machine =0;
				foreach ($cust_buyer_wise_production_arrr as $buyer => $rows)
				{

					// echo "<pre>";
					// print_r($buyer);
					$total_no_machine = $cust_buyer_wise_machine[$buyer]['machine_no_id'];

					//var_dump($total_no_machine);
					if ($k % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					$out_bound_qnty = 0;
					$out_bound_qnty_wg_yes = $rows[3][1][1][1] + $rows[3][1][1][2] + $rows[3][999][1][1] + $service_buyer_data[$buyer];
					$out_bound_qnty_wg_no = $rows[3][1][2][1] + $rows[3][1][2][2] + $rows[3][999][2][1] + $service_buyer_data[$buyer];
					$in_bound_qnty = 0;
					$in_bound_qnty = $rows[2][999][0][1];

					//$tot_summ=$rows[1][1][1][1]+$rows[1][999][2][1]+$rows[3][1][1][1]+$rows[3][1][2][1]+$sample_without_order+$sample_with_order;
					//$buyer_id = ($row[csf("within_group")]==1)?$booking_arr[$row[csf("sales_booking_no")]]:$row[csf("buyer_id")];
					$tot_summ = $rows[1][1][1][1] + $rows[1][999][2][1] + $out_bound_qnty_wg_yes + $out_bound_qnty_wg_no +  $in_bound_qnty;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center" style="width: 40px;"><? echo $k; ?></td>
						<td align="center" title="">
							<? if($buyer_arr[$buyer] !=''){
								echo $buyer_arr[$buyer];
							}else{
								echo $buyer;
							}
							 ?>
						</td>
						<td align="center" title="">
						<a href='#report_details' onClick="openmypage('','','<? echo $buyer; ?>','<? echo 1; ?>','cust_buyer_machine_popup');"><? echo $total_no_machine; ?></a> </td>
						<td align="right" style="width: 45px;"><? echo number_format($rows[1][1][1][1], 2, '.', ''); // $rows[1][1][1][1]
																?></td>
						<td align="right" style="width: 45px;"><? echo number_format($rows[1][999][2][1], 2, '.', ''); // $rows[1][1][2][1]
																?></td>
						<td align="right"><? echo number_format($out_bound_qnty_wg_yes, 2, '.', ''); ?></td>
						<td align="right"><? echo number_format($out_bound_qnty_wg_no, 2, '.', ''); ?></td>
						<td align="right"><? echo number_format($in_bound_qnty, 2, '.', ''); ?></td>
						<td align="right"><? echo  number_format($tot_summ, 2, '.', ''); ?></td>
					</tr>
					<?
					$tot_qtyinhouse_wg_yes += $rows[1][1][1][1];
					$tot_qtyinhouse_wg_no += $rows[1][999][2][1];
					$tot_qtyoutbound_wg_yes += $out_bound_qnty_wg_yes;
					$tot_qtyoutbound_wg_no += $out_bound_qnty_wg_no;
					$tot_qtyinbound += $in_bound_qnty;
					$total_summ += $tot_summ;
					unset($subcon_buyer_samary[$buyer]);
					$k++;
				}

				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3" align="right"><strong>Total</strong></th>
					<th align="right"><? echo number_format($tot_qtyinhouse_wg_yes, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyinhouse_wg_no, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyoutbound_wg_yes, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyoutbound_wg_no, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_qtyinbound, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($total_summ, 2, '.', ''); ?></th>
				</tr>
				<tr>
					<th colspan="3"><strong>In %</strong></th>
					<th align="right"><? $qtyinhouse_per = ($tot_qtyinhouse_wg_yes / $total_summ) * 100;
										echo number_format($qtyinhouse_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyinhouse_per = ($tot_qtyinhouse_wg_no / $total_summ) * 100;
										echo number_format($qtyinhouse_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyoutbound_per = ($tot_qtyoutbound_wg_yes / $total_summ) * 100;
										echo number_format($qtyoutbound_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyoutbound_per = ($tot_qtyoutbound_wg_no / $total_summ) * 100;
										echo number_format($qtyoutbound_per, 2) . ' %'; ?></th>
					<th align="right"><? $qtyinbound_per = ($tot_qtyinbound / $total_summ) * 100;
										echo number_format($qtyinbound_per, 2) . ' %'; ?></th>
					<th align="right"><? echo "100 %"; ?></th>
				</tr>
			</tfoot>
		</table>
		<br />
		<fieldset style="width:<? echo $tbl_width + 20; ?>px;">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_head">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="60" rowspan="2">WG<br />(Yes/No)</th>
						<th width="100" rowspan="2">Knitting Company</th>
						<th width="100" rowspan="2">Floor</th>
						<th width="90" rowspan="2">M/C No.</th>
						<th width="70" rowspan="2">M/C Brand </th>
						<th width="70" rowspan="2">Production Date</th>
						<th width="60" rowspan="2">M/C Dia & Gauge</th>
						<th width="70" rowspan="2">Unit Name</th>
						<th width="70" rowspan="2">Customer</th>
						<th width="70" rowspan="2">Cust.Buyer</th>
						<th width="140" rowspan="2">Program/ Sales Order No</th>
						<th width="140" rowspan="2">Sales / <br>Booking No</th>
						<th width="70" rowspan="2">Yarn Count</th>
						<th width="150" rowspan="2">Yarn Composition</th>
						<th width="80" rowspan="2">Yarn Type</th>
						<th width="80" rowspan="2">Brand</th>
						<th width="80" rowspan="2">Lot</th>
						<th width="100" rowspan="2">Body Part</th>
						<th width="100" rowspan="2">Construction</th>
						<th width="150" rowspan="2">Composition</th>
						<th width="130" rowspan="2">Color</th>
						<th width="100" rowspan="2">Color Range</th>
						<th width="60" rowspan="2">Stich</th>
						<th width="60" rowspan="2">Dia</th>
						<th width="60" rowspan="2">GSM</th>
						<th colspan="<? echo count($shift_name); ?>">Production</th>
						<th width="100" rowspan="2">Shift Total</th>
						<?
						if ($vari_knit_charge_source == 2) {
						?>
							<th width="100" rowspan="2">Rate</th>
							<th width="100" rowspan="2">Amount</th>
						<?
						}
						?>
						<th width="100" rowspan="2">Insert User</th>
						<th width="100" rowspan="2">Insert Date and Tiime</th>

						<th width="80" rowspan="2">Reject Qty</th>
						<th rowspan="2"> Remarks</th>
					</tr>
					<tr>
						<?
						$ship_count = 0;
						foreach ($shift_name as $val) {
							$ship_count++;
						?>
							<th width="60"><? echo $val; ?></th>
						<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?
					$lib_userArr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
					$total_grand_rejectQnty = 0;
					$p = 1;
					$q = 1;
					$z = 1;
					if ($cbo_type == 1 || $cbo_type == 0) {
						// echo count($nameArray_inhouse);
						if (count($nameArray_inhouse) > 0) {
							// echo $row[csf("knitting_source")].'D'.$row[csf("body_part_id")];
							$km = 0;
							$tot_reject_qty = 0;
							$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

							foreach ($nameArray_inhouse as $row) {
								//for yarn type
								$yarn_type_name = '';
								$yarn_type_name = getYarnType($yarn_type_arr, $row[csf('yarn_prod_id')]);


								if ($row[csf("knitting_source")] == 3) // Out-bound
								{
									if ($p == 1) {
									?>
										<tr class="tbl_bottom">
											<td colspan="26" align="right"><b>In-house Total (with order)</b></td>
											<?
											foreach ($shift_name as $key => $val) {
											?>
												<td align="right"><? echo number_format($inhouse_ship[$key], 2, '.', ''); ?></td>
											<?
											}
											?>
											<td align="right"><? echo number_format($inhouse_tot_qty, 2, '.', ''); ?></td>
											<?
											if ($vari_knit_charge_source == 2) {
											?>
												<td align="right"></td>
												<td align="right"><? echo number_format($inhouse_tot_charge_amount, 2, '.', ''); ?></td>
											<?
											}
											?>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td align="right"><? echo number_format($grand_reject_qty, 2, '.', ''); ?></td>
											<td>&nbsp;</td>
										</tr>
										<tr bgcolor="#CCCCCC">
											<td colspan="<? echo $ship_count + 32 + $colspan; ?>" align="left"><b>Outbound Subcontract</b></td>
										</tr>
									<?
										$total_grand_rejectQnty += $grand_reject_qty;
										$inhouse_ship = array();
										$inhouse_tot_qty =  $grand_reject_qty = $inhouse_tot_charge_amount = 0;
									}

									if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";

									$count = '';
									$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
									foreach ($yarn_count as $count_id) {
										if ($count == '') $count = $yarn_count_details[$count_id];
										else $count .= "," . $yarn_count_details[$count_id];
									}

									if ($row[csf('receive_basis')] == 2) {
										$machine_dia_gage = $knit_plan_arr[$row[csf('booking_no')]]['machine_dia'] . " X " . $knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
									} else {
										$machine_dia_gage = $machine_details[$row[csf('machine_no_id')]]['dia_width'] . " X " . $machine_details[$row[csf('machine_no_id')]]['gauge'];
									}
									$buyer_id_obs = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("buyer_id")]];
									$cust_buyer_obs = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("customer_buyer")]];

									$machine_brand = $machine_details[$row[csf('machine_no_id')]]['brand'];
									$body_part_ids = array_unique(explode(",", $row[csf('body_part_id')]));
									$body_partName = '';
									foreach ($body_part_ids as $body_id) {
										if ($body_partName == '') $body_partName = $body_part[$body_id];
										else $body_partName .= "," . $body_part[$body_id];
									}

									// $yarn_type_name='';
									// $febric_description_id=array_unique(explode(",",$row[csf('febric_description_id')]));
									// foreach($febric_description_id as $y_id)
									// {
									// 	if($yarn_type_name=='')
									// 		$yarn_type_name=$yarn_type[$y_id];
									// 	else
									// 		$yarn_type_name.=",".$yarn_type[$y_id];
									// }

									$yarn_desc_name='';
									$yarn_prod_id=array_unique(explode(",",$row[csf('yarn_prod_id')]));
									foreach($yarn_prod_id as $y_id)
									{
										if($yarn_desc_name=='')
											$yarn_desc_name=$product_details_array[$y_id]['desc'];
										else
											$yarn_desc_name.=",".$product_details_array[$y_id]['desc'];
									}
									//var_dump($yarn_desc_name);

									//$product_details_array[$row[csf('id')]]['desc'];


									if ($row[csf('knitting_source')] == 1)
										$knitting_party = $company_arr[$row[csf('knitting_company')]];
									else if ($row[csf('knitting_source')] == 3)
										$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
									else
										$knitting_party = "&nbsp;";

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
										<td align="center"><? $km++;
															echo $km;  ?></td>
										<td align="center"><? echo ($row[csf('within_group')] == 1) ? "Yes" : "No"; ?></td>
										<td align="center"><? echo $knitting_party; ?></td>
										<td align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
										<td align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
										<td align="center"></td>
										<td align="center">
											<p><? if ($row[csf('receive_date')] != "" && $row[csf('receive_date')] != "0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p>
										</td>
										<td align="center">
											<p><? echo $machine_dia_gage; ?></p>
										</td>
										<td align="center">
											<p><? if ($row[csf('within_group')] == 1) {
													echo $company_arr[$row[csf('unit_id')]];
												} else {
													echo $company_arr[$row[csf('company_id')]];
												} ?></p>
										</td>
										<td width="70" align="center">
											<p><? echo $buyer_id_obs; ?></p>
										</td>
										<td width="70" align="center">
											<p><? echo $cust_buyer_obs; ?></p>
										</td>
										<td align="center"><strong>P:</strong><? echo $row[csf('booking_no')] . "<br /><strong>S:</strong>" . $row[csf('job_no')]; ?>
										</td>
										<td align="center">
											<p><? echo $row[csf('sales_booking_no')]; ?></p>
										</td>
										<td align="center">
											<p><? echo $count; ?></p>
										</td>
										<td align="center">
											<p><? echo $yarn_desc_name; ?></p>
										</td>
										<td align="center">
											<p><? echo $yarn_type_name; ?></p>
										</td>
										<td>
											<P>
												<?
												$brand_arr = array_unique(explode(",", $row[csf('brand_id')]));
												$all_brand = "";
												foreach ($brand_arr as $id) {
													$all_brand .= $brand_details[$id] . ",";
												}
												$all_brand = chop($all_brand, " , ");
												echo $all_brand;
												?>&nbsp;
											</P>
										</td>
										<td align="center">
											<P><? echo implode(",", array_unique(explode(",", $row[csf('yarn_lot')]))); ?>&nbsp;</P>
										</td>
										<td width="100" align="center">
											<p><? echo $body_partName; ?></p>
										</td>
										<td>
											<P>
												<?
												$description_arr = array_unique(explode(",", $row[csf('febric_description_id')]));
												$all_construction = "";
												foreach ($description_arr as $id) {
													$all_construction .= $construction_arr[$id] . ",";
												}
												$all_construction = chop($all_construction, " , ");
												echo $all_construction;
												?></P>
										</td>
										<td>
											<P>
												<?
												$all_composition = "";
												foreach ($description_arr as $id) {
													$all_composition .= $composition_arr[$id] . ",";
												}
												$all_composition = chop($all_composition, " , ");
												echo $all_composition;
												?>&nbsp;</P>
										</td>
										<td>
											<P>
												<?
												$color_arr = array_unique(explode(",", $row[csf('color_id')]));
												$all_color = "";
												foreach ($color_arr as $id) {
													$all_color .= $color_details[$id] . ", ";
												}
												$all_color = chop($all_color, " , ");
												echo $all_color;
												?>&nbsp;</P>
										</td>
										<td>
											<P>
												<?
												$color_range_arr = array_unique(explode(",", $row[csf('color_range_id')]));
												$all_color_range = "";
												foreach ($color_range_arr as $id) {
													$all_color_range .= $color_range[$id] . ",";
												}
												$all_color_range = chop($all_color_range, " , ");
												echo $all_color_range;
												?>&nbsp;</P>
										</td>
										<td align="center">
											<p><? echo  implode(",", array_unique(explode(",", $row[csf('stitch_length')]))); ?>&nbsp;</p>
										</td>
										<td width="60" align="center">
											<p><? echo  implode(",", array_unique(explode(",", $row[csf('width')]))); ?>&nbsp;</p>
										</td>
										<td width="60" align="center">
											<p><? echo  implode(",", array_unique(explode(",", $row[csf('gsm')]))); ?>&nbsp;</p>
										</td>
										<?
										$row_tot_roll = 0;
										$row_tot_qnty = 0;
										foreach ($shift_name as $key => $val) {
											$row_tot_qnty += $row[csf('qntyshift' . strtolower($val))];
										?>
											<td align="right" title="<? echo $key . "==" . $val; ?> "><? echo number_format($row[csf('qntyshift' . strtolower($val))], 2); ?> </td>
										<?
											$grand_total_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
											$inhouse_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
										}
										?>
										<td align="right">
											<a href='#report_details' onClick="openmypageshift('<? echo $row[csf('dtls_id')]; ?>','<? echo 1; ?>','shift_qty_popup');"><? echo number_format($row_tot_qnty, 2, '.', ''); ?></a>
										</td>

										<?
										if ($vari_knit_charge_source == 2) {
										?>
											<td align="right"><? echo number_format($row[csf('kniting_charge')], 2, '.', ''); ?></td>
											<td align="right"><? $charge_amount = $row_tot_qnty * $row[csf('kniting_charge')];
																echo number_format($charge_amount, 2, '.', ''); ?></td>
										<?
										}
										?>

										<td width="100" align="center">
											<p><? echo $lib_userArr[$row[csf('inserted_by')]]; ?>&nbsp;</p>
										</td>
										<td width="100" align="center">
											<p><? echo $row[csf('insert_date')]; ?>&nbsp;</p>
										</td>

										<td align="right">
											<p><? echo number_format($row[csf('reject_qty')], 2, '.', ''); ?></p>
										</td>
										<td>
											<p><? echo $row[csf('remarks')]; ?>&nbsp;</p>
										</td>
									</tr>

									<?
									$inhouse_tot_qty += $row_tot_qnty;
									$grand_tot_qnty += $row_tot_qnty;
									$inhouse_tot_charge_amount += $charge_amount;
									$grand_tot_charge_amount += $charge_amount;
									$grand_reject_qty += $row[csf('reject_qty')];
									$outBoundWithoutShiftQnty += $row[csf('without_shift')];
									$i++;

									$p++;
								} else // In-house
								{
									if ($q == 1) {
									?>
										<tr bgcolor="#CCCCCC">
											<td colspan="<? echo $ship_count + 32 + $colspan; ?>" align="left"><b>In-House </b></td>
										</tr>
									<?
									}
									if ($row[csf('machine_no_id')] != 0) {

										if ($i % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";
										$count = '';
										$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
										foreach ($yarn_count as $count_id) {
											if ($count == '') $count = $yarn_count_details[$count_id];
											else $count .= "," . $yarn_count_details[$count_id];
										}

										if ($row[csf('receive_basis')] == 2) {
											$machine_dia_gage = $knit_plan_arr[$row[csf('booking_no')]]['machine_dia'] . " X " . $knit_plan_arr[$row[csf('booking_no')]]['machine_gg'];
										} else {
											$machine_dia_gage = $machine_details[$row[csf('machine_no_id')]]['dia_width'] . " X " . $machine_details[$row[csf('machine_no_id')]]['gauge'];
										}
										$buyer_id_inhouse = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("buyer_id")]];
										$cust_buyer_inhouse = ($row[csf("within_group")] == 1) ? $booking_arr[$row[csf("sales_booking_no")]] : $buyer_arr[$row[csf("customer_buyer")]];

										$machine_brand = $machine_details[$row[csf('machine_no_id')]]['brand'];
										$body_part_ids = array_unique(explode(",", $row[csf('body_part_id')]));
										$body_partName = '';
										foreach ($body_part_ids as $body_id) {
											if ($body_partName == '')
												$body_partName = $body_part[$body_id];
											else
												$body_partName .= "," . $body_part[$body_id];
										}

										/*$yarn_type_name='';
												$febric_description_id=array_unique(explode(",",$row[csf('febric_description_id')]));
												foreach($febric_description_id as $y_id)
												{
													if($yarn_type_name=='') $yarn_type_name=$yarn_type[$y_id]; else $yarn_type_name.=",".$yarn_type[$y_id];
												}*/
										$yarn_desc_name='';
										$yarn_prod_id=array_unique(explode(",",$row[csf('yarn_prod_id')]));
										foreach($yarn_prod_id as $y_id)
										{
											if($yarn_desc_name=='')
												$yarn_desc_name=$product_details_array[$y_id]['desc'];
											else
												$yarn_desc_name.=",".$product_details_array[$y_id]['desc'];
										}
										//var_dump($yarn_desc_name);

										//$product_details_array[$row[csf('id')]]['desc'];

										if ($row[csf('knitting_source')] == 1)
											$knitting_party = $company_arr[$row[csf('knitting_company')]];
										else if ($row[csf('knitting_source')] == 3)
											$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party = "&nbsp;";

									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
											<?
											$km++;
											?>
											<td align="center" style="width: 40px;"><? echo $km; ?></td>
											<td align="center"><? echo ($row[csf('within_group')] == 1) ? "Yes" : "No"; ?></td>
											<td align="center">
												<p><? echo $knitting_party; ?></p>
											</td>
											<td align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
											<td align="center">
												<p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p>
											</td>
											<td align="center">
												<p><? echo $machine_brand; ?></p>
											</td>
											<td align="center">
												<p><? if ($row[csf('receive_date')] != "" && $row[csf('receive_date')] != "0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p>
											</td>
											<td align="center">
												<p><? echo $machine_dia_gage; ?></p>
											</td>
											<td align="center">
												<p><? if ($row[csf('within_group')] == 1) {
														echo $company_arr[$row[csf('unit_id')]];
													} else {
														echo $company_arr[$row[csf('company_id')]];
													} ?></p>
											</td>
											<td width="70" align="center">
												<p><? echo $buyer_id_inhouse; ?></p>
											</td>
											<td width="70" align="center">
												<p><? echo $cust_buyer_inhouse; ?></p>
											</td>
											<td align="center"><strong>P:</strong><? echo $row[csf('booking_no')] . "<br /><strong>S:</strong>" . $row[csf('job_no')]; ?>
											</td>
											<td align="center">
												<p><? echo $row[csf('sales_booking_no')]; ?></p>
											</td>
											<td align="center">
												<p><? echo $count; ?></p>
											</td>
											<td align="center">
												<p><? echo $yarn_desc_name; ?></p>
											</td>
											<td align="center">
												<p><? echo $yarn_type_name; ?></p>
											</td>
											<td>
												<P>
													<?
													$brand_arr = array_unique(explode(",", $row[csf('brand_id')]));
													$all_brand = "";
													foreach ($brand_arr as $id) {
														$all_brand .= $brand_details[$id] . ",";
													}
													$all_brand = chop($all_brand, " , ");
													echo $all_brand;
													?>&nbsp;
												</P>
											</td>
											<td align="center">
												<P><? echo implode(",", array_unique(explode(",", $row[csf('yarn_lot')]))); ?>&nbsp;</P>
											</td>
											<td width="100" align="center">
												<p><? echo $body_partName; ?></>
											</td>
											<td>
												<P>
													<?
													$description_arr = array_unique(explode(",", $row[csf('febric_description_id')]));
													$all_construction = "";
													foreach ($description_arr as $id) {
														$all_construction .= $construction_arr[$id] . ",";
													}
													$all_construction = chop($all_construction, " , ");
													echo $all_construction;
													?></P>
											</td>
											<td>
												<P>
													<?
													$all_composition = "";
													foreach ($description_arr as $id) {
														$all_composition .= $composition_arr[$id] . ",";
													}
													$all_composition = chop($all_composition, " , ");
													echo $all_composition;
													?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_arr = array_unique(explode(",", $row[csf('color_id')]));
													$all_color = "";
													foreach ($color_arr as $id) {
														$all_color .= $color_details[$id] . ", ";
													}
													$all_color = chop($all_color, " , ");
													echo $all_color;
													?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_range_arr = array_unique(explode(",", $row[csf('color_range_id')]));
													$all_color_range = "";
													foreach ($color_range_arr as $id) {
														$all_color_range .= $color_range[$id] . ",";
													}
													$all_color_range = chop($all_color_range, " , ");
													echo $all_color_range;
													?>&nbsp;</P>
											</td>
											<td align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('stitch_length')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('width')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('gsm')]))); ?>&nbsp;</p>
											</td>
											<?
											$row_tot_roll = 0;
											$row_tot_qnty = 0;
											foreach ($shift_name as $key => $val) {
												$row_tot_qnty += $row[csf('qntyshift' . strtolower($val))];
											?>
												<td align="right"><? echo number_format($row[csf('qntyshift' . strtolower($val))], 2); ?> </td>
											<?
												$grand_total_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
												$inhouse_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
											}
											?>
											<td align="right">
											<a href='#report_details' onClick="openmypageshift('<? echo $row[csf('dtls_id')]; ?>','<? echo 1; ?>','shift_qty_popup');"><? echo number_format($row_tot_qnty, 2, '.', ''); ?></a> </td>
											<?
											if ($vari_knit_charge_source == 2) {
											?>
												<td align="right"><? echo number_format($row[csf('kniting_charge')], 2, '.', ''); ?></td>
												<td align="right"><? $charge_amount = $row_tot_qnty * $row[csf('kniting_charge')];
																	echo number_format($charge_amount, 2, '.', ''); ?></td>
											<?
											}
											?>

											<td width="100" align="center">
												<p><? echo $lib_userArr[$row[csf('inserted_by')]]; ?>&nbsp;</p>
											</td>
											<td width="100" align="center">
												<p><? echo $row[csf('insert_date')]; ?>&nbsp;</p>
											</td>

											<td align="right">
												<p><? echo number_format($row[csf('reject_qty')], 2, '.', ''); ?></p>
											</td>
											<td>
												<p><? echo $row[csf('remarks')]; ?>&nbsp;</p>
											</td>
										</tr>
									<?
										$inhouse_tot_qty += $row_tot_qnty;
										$grand_tot_qnty += $row_tot_qnty;
										$inhouse_tot_charge_amount += $charge_amount;
										$grand_tot_charge_amount += $charge_amount;
										$grand_reject_qty += $row[csf('reject_qty')];
										$i++;
									}
									$q++;
								}
							}
							?>
							<tr class="tbl_bottom">
								<td colspan="26" align="right"><b>Outbound Total (with order)</b></td>
								<?
								foreach ($shift_name as $key => $val) {
								?>
									<td align="right"><? echo number_format($inhouse_ship[$key], 2, '.', ''); ?></td>
								<?
								}
								?>
								<td align="right"><? echo number_format($inhouse_tot_qty + $outBoundWithoutShiftQnty, 2, '.', ''); ?></td>

								<?
								if ($vari_knit_charge_source == 2) {
								?>
									<td align="right"></td>
									<td align="right"><? echo number_format($inhouse_tot_charge_amount, 2, '.', ''); ?></td>
								<?
								}
								?>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($grand_reject_qty, 2, '.', ''); ?></td>
								<td>&nbsp;</td>
							</tr>
							<?
							$total_grand_rejectQnty += $grand_reject_qty;
						}
						// if ($cbo_booking_type == 0) // In-Bound Subcontract
						// {
							if (count($nameArray_inhouse_subcon) > 0)
							{
								$km = 0;
								$tot_reject_qty = 0;
								$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
								foreach ($nameArray_inhouse_subcon as $row) {
									if ($z == 1) {
									?>
										<tr bgcolor="#CCCCCC">
											<td colspan="<? echo $ship_count + 32 + $colspan; ?>" align="left"><b>In-Bound Subcontract</b></td>
										</tr>
									<?
									}
									if ($row[csf('machine_no_id')] != 0) {

										if ($i % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";

										$count = '';
										$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
										foreach ($yarn_count as $count_id) {
											if ($count == '') $count = $yarn_count_details[$count_id];
											else $count .= "," . $yarn_count_details[$count_id];
										}

										$machine_dia = implode(',', array_unique(explode(",", $row[csf('machine_dia')])));

										$machine_gg = implode(',', array_unique(explode(",", $row[csf('machine_gg')])));

										//$machine_dia_gage=$machine_details[$row[csf('machine_no_id')]]['dia_width']." X ".$machine_details[$row[csf('machine_no_id')]]['gauge'];
										$machine_dia_gage = $machine_dia . " X " . $machine_gg;

										$buyer_id_ibs = $buyer_arr[$row[csf("buyer_id")]];
										$cust_buyer_id_ibs = $row[csf("cust_buyer")];

										/*$febric_description_id=array_unique(explode(",",$row[csf('febric_description_id')]));
												$yarn_type_name='';
												foreach($febric_description_id as $y_id)
												{
													if($yarn_type_name=='') $yarn_type_name=$yarn_type[$y_id]; else $yarn_type_name.=",".$yarn_type[$y_id];
												}*/
										$order_nos = implode(',', array_unique(explode(",", $row[csf('order_nos')])));

										$machine_brand = $machine_details[$row[csf('machine_no_id')]]['brand'];
										if ($row[csf('knitting_source')] == 1)
											$knitting_party = $company_arr[$row[csf('knitting_company')]];
										else if ($row[csf('knitting_source')] == 3)
											$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
										else
											$knitting_party = "&nbsp;";

									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
											<? $km++; ?>
											<td align="center" style="width: 40px;"><? echo $km; ?></td>
											<td align="center">&nbsp;</td>
											<td align="center"><? echo $knitting_party; ?></td>
											<td align="center"><? echo $floor_details[$row[csf('floor_id')]]; ?></td>
											<td align="center">
												<p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p>
											</td>
											<td align="center">
												<p><? echo $machine_brand; ?></p>
											</td>
											<td align="center">
												<p><? if ($row[csf('receive_date')] != "" && $row[csf('receive_date')] != "0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p>
											</td>
											<td align="center">
												<p><? echo $machine_dia_gage; ?></p>
											</td>
											<td align="center">
												<p><? echo $company_arr[$row[csf('company_id')]]; ?></p>
											</td>
											<td width="70" align="center">
												<p><? echo $buyer_id_ibs; ?></p>
											</td>
											<td width="70" align="center">
												<p><? echo $cust_buyer_id_ibs; ?></p>
											</td>
											<td align="center"><strong>P:</strong><? echo $row[csf('booking_no')] . "<br /><strong>S:</strong>" . $row[csf('job_no')]; ?></td>

											<td align="center"><? echo $order_nos; ?></td>
											<td align="center">
												<p><? echo $count; ?></p>
											</td>
											<td align="center">
												<p><? //echo $count; ?></p>
											</td>
											<td align="center">
												<p><? //echo $count;
													?></p>
											</td>
											<td>
												<P><? $brand_data = implode(",", array_unique(explode(",", $row[csf('brand_id')])));
													echo $brand_data; ?>&nbsp; </P>
											</td>

											<td align="center">
												<P><? echo implode(",", array_unique(explode(",", $row[csf('yarn_lot')]))); ?>&nbsp;</P>
											</td>
											<td width="100" align="center">
												<p>&nbsp; </p>
											</td>
											<td colspan="2">
												<P>
													<?
													$prod_id_arr = array_unique(explode(",", $row[csf('prod_id')]));
													$all_prod = "";
													foreach ($prod_id_arr as $id) {
														$all_prod .= $const_comp_arr[$id] . ", ";
													}
													$all_prod = chop($all_prod, " , ");
													echo $all_prod; ?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_arr = array_unique(explode(",", $row[csf('color_id')]));
													$all_color = "";
													foreach ($color_arr as $id) {
														$all_color .= $color_details[$id] . ", ";
													}
													$all_color = chop($all_color, " , ");
													echo $all_color; ?>&nbsp;</P>
											</td>
											<td>
												<P>
													<?
													$color_range_arr = array_unique(explode(",", $row[csf('color_range_id')]));
													$all_color_range = "";
													foreach ($color_range_arr as $id) {
														$all_color_range .= $color_range[$id] . ",";
													}
													$all_color_range = chop($all_color_range, " , ");
													echo $all_color_range; ?>&nbsp;</P>
											</td>
											<td align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('stitch_length')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('width')]))); ?>&nbsp;</p>
											</td>
											<td width="60" align="center">
												<p><? echo  implode(",", array_unique(explode(",", $row[csf('gsm')]))); ?>&nbsp;</p>
											</td>
											<?
											$row_tot_roll = 0;
											$row_tot_qnty = 0;
											foreach ($shift_name as $key => $val) {
												$row_tot_qnty += $row[csf('qntyshift' . strtolower($val))];
											?>
												<td align="right"><? echo number_format($row[csf('qntyshift' . strtolower($val))], 2); ?> </td>
											<?
												$grand_total_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
												$inbound_ship[$key] += $row[csf('qntyshift' . strtolower($val))];
											}
											?>
											<td align="right">
											<a href='#report_details' onClick="openmypageshift('<? echo $row[csf('dtls_id')]; ?>','<? echo 1; ?>','shift_qty_popup');"><? echo number_format($row_tot_qnty, 2, '.', ''); ?></a> </td>

											<?
											if ($vari_knit_charge_source == 2) {
											?>
												<td align="right"><? echo number_format($row[csf('rate')], 2, '.', ''); ?></td>
												<td align="right"><? echo number_format($row_tot_qnty * $row[csf('rate')], 2, '.', ''); ?></td>
											<?
											}
											?>

											<td width="100" align="center">
												<p><? echo $lib_userArr[$row[csf('inserted_by')]]; ?>&nbsp;</p>
											</td>
											<td width="100" align="center">
												<p><? echo $row[csf('insert_date')]; ?>&nbsp;</p>
											</td>
											<td align="right">
												<p><? echo number_format($row[csf('reject_qty')], 2, '.', ''); ?></p>
											</td>
											<td>
												<p><? echo $row[csf('remarks')]; ?>&nbsp;</p>
											</td>
										</tr>
								<?
										$inbound_tot_qty += $row_tot_qnty;
										$grand_tot_qnty += $row_tot_qnty;
										$inbound_tot_amount += $row_tot_qnty * $row[csf('rate')];
										$grand_tot_charge_amount += $row_tot_qnty * $row[csf('rate')];
										$inboundreject_qty += $row[csf('reject_qty')];
										$grand_reject_qty += $row[csf('reject_qty')];
										$i++;
									}
									$z++;
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="26" align="right"><b>Inbound Total</b></td>
									<?
									foreach ($shift_name as $key => $val) {
									?>
										<td align="right"><? echo number_format($inbound_ship[$key], 2, '.', ''); ?></td>
									<?
									}
									?>
									<td align="right"><? echo number_format($inbound_tot_qty, 2, '.', ''); ?></td>
									<?
									if ($vari_knit_charge_source == 2) {
									?>
										<td align="right"></td>
										<td align="right"><? echo number_format($inbound_tot_amount, 2, '.', ''); ?></td>
									<?
									}
									?>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right"><? echo number_format($inboundreject_qty, 2, '.', ''); ?></td>
									<td>&nbsp;</td>
								</tr>
								<?
								$total_grand_rejectQnty += $inboundreject_qty;
							}
						//}
					}
					$j = 0;
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="26">Grand Total</th>
						<?
						foreach ($shift_name as $key => $val) {
						?>
							<th align="right" width="60"><? echo number_format($grand_total_ship[$key], 2, '.', ''); ?></th>
						<?
						}
						?>
						<th align="right"><? echo number_format($grand_tot_qnty + $outBoundWithoutShiftQnty, 2, '.', ''); ?></th>
						<?
						if ($vari_knit_charge_source == 2) {
						?>
							<th align="right"></th>
							<th align="right"><? echo number_format($grand_tot_charge_amount, 2, '.', ''); ?></th>
						<?
						}
						?>

						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? echo number_format($total_grand_rejectQnty, 2, '.', ''); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
			<br />
			<?
				if ($txt_date_from != "") {
					if ($txt_date_to == "") $txt_date_to = $txt_date_from;
					$date_distance = datediff("d", $txt_date_from, $txt_date_to);
					$month_name = date('F', strtotime($txt_date_from));
					$year_name = date('Y', strtotime($txt_date_from));
					$day_of_month = explode("-", $txt_date_from);
					if ($db_type == 0) {
						$fist_day_of_month = $day_of_month[2] * 1;
					} else {
						$fist_day_of_month = $day_of_month[0] * 1;
					}

					$tot_machine = count($total_machine);
					$running_machine = count($total_running_machine);
					$stop_machine = $tot_machine - $running_machine;
					$running_machine_percent = (($running_machine / $tot_machine) * 100);
					$stop_machine_percent = (($stop_machine / $tot_machine) * 100);
					if ($date_distance == 1 && $fist_day_of_month > 1) {
						$query_cond_month = date('m', strtotime($txt_date_from));
						$query_cond_year = date('Y', strtotime($txt_date_from));
						$sql_cond = "";
						if ($db_type == 0) $sql_cond = "  and month(a.receive_date)='$query_cond_month' and year(a.receive_date)='$query_cond_year'";
						else $sql_cond = "  and to_char(a.receive_date,'mm')='$query_cond_month' and to_char(a.receive_date,'yyyy')='$query_cond_year'";
						if ($from_date != "" && $to_date != "") $date_con = " and a.receive_date between '$from_date' and '$to_date'";
						$sql_montyly_inhouse = sql_select("select sum(c.quantity ) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date<'" . $txt_date_from . "' $sql_cond");


						$sql_monthly_wout_order = sql_select("select sum( b.grey_receive_qnty) as qnty  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.knitting_source=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 and a.receive_date<'" . $txt_date_from . "' $sql_cond");

						$yesterday_prod = $sql_montyly_inhouse[0][csf("qnty")] + $sql_monthly_wout_order[0][csf("qnty")];
						$today_prod = $yesterday_prod + $grand_tot_qnty;
					}
			?>
				<table width="<? echo $tbl_width; ?>">
					<tr>
						<td width="25%" valign="top">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
								<tr>
									<td>Total number of m/c running </td>
									<td width="100" align="right"><? echo $running_machine; ?></td>
									<td align="right" width="100"><? if ($running_machine_percent > 0) echo number_format($running_machine_percent, 2, '.', '') . " % "; ?></td>
								</tr>
								<tr>
									<td>Total number of m/c stop</td>
									<td align="right"><? echo $stop_machine; ?></td>
									<td align="right"><? if ($running_machine_percent > 0) echo number_format($stop_machine_percent, 2, '.', '') . " % "; ?></td>
								</tr>
								<tr>
									<td>Total production</td>
									<td align="right"><? echo number_format($grand_tot_qnty, 2); ?></td>
									<td align="center">Kg</td>
								</tr>
							</table>
						</td>
						<td width="10%" valign="top">&nbsp; </td>
						<td width="25%" valign="top">
							<?
							if ($date_distance == 1 && $fist_day_of_month > 1) {
							?>
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
									<tr>
										<td>Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td align="right" width="100"><? echo number_format($yesterday_prod, 2); ?></td>
										<td align="center" width="100">Kg</td>
									</tr>
									<tr>
										<td>Upto today production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?></td>
										<td align="right"><? echo number_format($today_prod, 2); ?> </td>
										<td align="center">Kg</td>
									</tr>
								</table>
							<?
							}
							?>
						</td>
						<td valign="top">&nbsp; </td>
					</tr>
				</table>
			<?
				}
			?>
		</fieldset>
		<br>
		<?
		foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		echo "$total_data####$filename";
		exit();
	}

}

if ($action == "delivery_challan_print")
{
	echo load_html_head_contents("Delivery Challan Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$datas = explode('_', $data);
	$program_ids = $datas[0];
	$source_ids = $datas[1];
	$company = $datas[2];
	$from_date = $datas[3];
	$to_date = $datas[4];
	$in_out_data = explode(',', $datas[1]);
	//echo $from_date;
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	//$poNumber_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

	$machine_details = array();
	$machine_data = sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach ($machine_data as $row) {
		$machine_details[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
	}

	$po_array = array();
	$po_data = sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by b.po_number, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id");
	foreach ($po_data as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_prefix_num')];
		$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}

	$knit_plan_arr = array();
	$plan_data = sql_select("select id, color_range, stitch_length from ppl_planning_info_entry_dtls");
	foreach ($plan_data as $row) {
		$knit_plan_arr[$row[csf('id')]]['cr'] = $row[csf('color_range')];
		$knit_plan_arr[$row[csf('id')]]['sl'] = $row[csf('stitch_length')];
	}

	?>
	<div style="width:1360px;">
		<table width="1350" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="17" align="center" style="font-size:x-large"><strong><? echo $company_details[$company]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="17" align="center">
					<?
					$nameArray = sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company");
					foreach ($nameArray as $result) {
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')] ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						City No: <? echo $result[csf('city')]; ?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <? echo $result[csf('province')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')]; ?>
						Website No: <? echo $result[csf('website')];
								}
									?>
				</td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></center>
				</td>
			</tr>
			<tr>
				<td colspan="17" align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></center>
				</td>
			</tr>
			<tr>
				<td colspan="17" style="font-size:14px"><strong><? echo "Date Range :" . " " . $from_date . " " . "To" . " " . $to_date; ?></strong></center>
				</td>
			</tr>

		</table>
	</div>
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Job No</th>
					<th width="90">Order No</th>
					<th width="60">Buyer</th>
					<th width="50">Prod. ID</th>
					<th width="60">M/C No</th>
					<th width="60">Req. No</th>
					<th width="90">Booking No/ Prog. No</th>
					<th width="60">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="70">Lot No</th>
					<th width="100">Color</th>
					<th width="">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="50">Fab. Dia</th>
					<th width="50">M/C Dia</th>
					<th width="50">Total Roll</th>
					<th width="70">Total Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:1350px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">

				<?
				$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
				$reqsn_details = return_library_array("select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id", "knit_id", "requisition_no");

				if ($db_type == 2) $date_cond = "'" . change_date_format($from_date, '', '', 1) . "' and '" . change_date_format($to_date, '', '', 1) . "'";
				if ($db_type == 0) $date_cond = "'" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
				if ($in_out_data[0] == 1) {
					$sql = "select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id,sum(case when c.entry_form=2 then b.no_of_roll else 0 end)  as roll_no, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift
					from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
					where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id
					order by a.receive_date";
				} else if ($in_out_data[0] == 3) {
					$sql = "select c.id, b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, c.po_breakdown_id, sum(case when c.entry_form=2 then c.quantity else 0 end)  as outqntyshift  from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.knitting_source=3 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.trans_type=1 and a.company_id=$company and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id, c.po_breakdown_id, c.id, b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id
					order by b.floor_id,a.receive_date";
				} else {
					$sql = "select b.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no, a.buyer_id, a.remarks, b.prod_id, b.febric_description_id, b.gsm, b.width,  b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id,sum(case when b.shift_name=0 then  b.no_of_roll end ) as rollnoshift, sum(case when a.entry_form=2 then b.grey_receive_qnty else 0 end)  as outqntyshift
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where  a.item_category=13 and a.id=b.mst_id and a.company_id=$company and a.knitting_source=1 and a.receive_date between $date_cond and a.recv_number_prefix_num in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=2
					and a.booking_without_order=1
					group by a.recv_number, a.receive_basis, a.receive_date, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.brand_id,  b.id, a.recv_number_prefix_num, a.knitting_source, a.knitting_company, a.booking_no, a.buyer_id, a.remarks, b.febric_description_id, b.gsm, b.width, b.machine_no_id
					order by a.receive_date";
				}
				//echo $sql;
				$nameArray = sql_select($sql);
				$i = 1;
				$tot_roll = 0;
				$tot_qty = 0;
				foreach ($nameArray as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$count = '';
					$yarn_count = explode(",", $row[csf('yarn_count')]);
					foreach ($yarn_count as $count_id) {
						if ($count == '') $count = $yarn_count_details[$count_id];
						else $count .= "," . $yarn_count_details[$count_id];
					}

					$reqsn_no = "";
					$stich_length = "";
					$color = "";
					if ($row[csf('receive_basis')] == 2) {
						$reqsn_no = $reqsn_details[$row[csf('booking_id')]];
						$stich_length = $knit_plan_arr[$row[csf('booking_id')]]['sl'];
						$color = $color_range[$knit_plan_arr[$row[csf('booking_id')]]['cr']];
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30">
							<div style="word-wrap:break-word; width:30px;"><? echo $i; ?></div>
						</td>
						<td width="60">
							<div style="word-wrap:break-word; width:60px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['job_no']; ?></div>
						</td>
						<td width="90">
							<div style="word-wrap:break-word; width:90px;"><? echo $po_array[$row[csf('po_breakdown_id')]]['no']; ?></div>
						</td>
						<td width="60">
							<div style="word-wrap:break-word; width:60px;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div>
						</td>
						<td width="50">
							<div style="word-wrap:break-word; width:50px;"><? echo $row[csf('recv_number_prefix_num')]; ?></div>
						</td>
						<td width="60" align="center">
							<div style="word-wrap:break-word; width:60px;"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></div>
						</td>
						<td width="60">
							<div style="word-wrap:break-word; width:60px;"><? echo $reqsn_no; ?></div>
						</td>
						<td width="90">
							<div style="word-wrap:break-word; width:90px;"><? echo $row[csf('booking_no')]; ?></div>
						</td>
						<td width="60">
							<div style="word-wrap:break-word; width:60px;"><? echo $count; ?></div>
						</td>
						<td width="70">
							<div style="word-wrap:break-word; width:70px;"><? echo $brand_details[$row[csf('brand_id')]]; ?></div>
						</td>
						<td width="70">
							<div style="word-wrap:break-word; width:70px;"><? echo $row[csf('yarn_lot')]; ?></div>
						</td>
						<td width="100">
							<div style="word-wrap:break-word; width:100px;"><? echo $color; ?></div>
						</td>
						<td width="">
							<div style="word-wrap:break-word; width:210px;"><? echo $composition_arr[$row[csf('febric_description_id')]];; ?></div>
						</td>
						<td width="50">
							<div style="word-wrap:break-word; width:50px;"><? echo $stich_length; ?></div>
						</td>
						<td width="50">
							<div style="word-wrap:break-word; width:50px;"><? echo $row[csf('gsm')]; ?></div>
						</td>
						<td width="50">
							<div style="word-wrap:break-word; width:50px;"><? echo $row[csf('width')]; ?></div>
						</td>
						<td width="50">
							<div style="word-wrap:break-word; width:50px;"><? echo $machine_details[$row[csf('machine_no_id')]]['dia']; ?></div>
						</td>
						<td width="50" align="right">
							<div style="word-wrap:break-word; width:50px;"><? echo $row[csf('roll_no')];
																			$tot_roll += $row[csf('roll_no')]; ?>&nbsp;</div>
						</td>
						<td width="70" align="right">
							<div style="word-wrap:break-word; width:70px;"><? echo $row[csf('outqntyshift')];
																			$tot_qty += $row[csf('outqntyshift')]; ?>&nbsp;</div>
						</td>
					</tr>
				<?
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="17"><strong>Total:</strong></td>
					<td align="right"><? echo number_format($tot_roll, 2, '.', ''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="left"><b>Remarks: </b></td>
					<td colspan="17"><? //echo number_to_words($tot_qty);
										?>&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(44, $company, "1340px");
			?>
		</div>
	</div>
	<?
	exit();
}

if($action=="machine_popup")
{

	echo load_html_head_contents("machine Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:700px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>


		<div id="scroll_body" align="center">


			<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="100">M/C No</th>
						<th width="100">DiaxGauge</th>
						<th width="200">F. Types</th>
						<th width="80">Production</th>
						<th >Knitting Source</th>
					</tr>
				</thead>
				<tbody>
					<?
					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
					$knit_plan_arr = array();
					$plan_data = sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
					foreach ($plan_data as $row)
					{
						$knit_plan_arr[$row[csf('id')]]['machine_dia'] = $row[csf('machine_dia')];
						$knit_plan_arr[$row[csf('id')]]['machine_gg'] = $row[csf('machine_gg')];
					}

					$machine_details = array();
					$machine_data = sql_select("select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and machine_no is not null");

					foreach ($machine_data as $row)
					{
						$machine_details[$row[csf('id')]]['dia_width'] = $row[csf('dia_width')];
						$machine_details[$row[csf('id')]]['gauge'] = $row[csf('gauge')];

					}

					$composition_arr = $construction_arr = array();
					$sql_deter = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array = sql_select($sql_deter);
					if (count($data_array) > 0) {
						foreach ($data_array as $row) {
							if (array_key_exists($row[csf('id')], $composition_arr)) {
								$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							} else {
								$composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							}

							$construction_arr[$row[csf('id')]] = $row[csf('construction')];
							$yarn_type_arr[$row[csf('id')]] = $yarn_type[$row[csf('yarn_type')]];
						}
					}

					if ($txt_date_from != "" && $txt_date_to != "")
					{
						$txt_date_from = change_date_format(date('d-m-Y', strtotime($txt_date_from)),'','',1);
						$txt_date_to = change_date_format(date('d-m-Y', strtotime($txt_date_to)),'','',1);
					}

					$from_date = $txt_date_from;
					if ( $txt_date_to == "") $to_date = $from_date;
					else $to_date = $txt_date_to;
					$date_con = "";
					if ($from_date != "" && $to_date != "") $date_con = " and a.receive_date between '$from_date' and '$to_date'";

				$sql_inhouse = "SELECT * from (
					(select a.receive_basis, a.receive_date, a.booking_no, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, b.machine_no_id,a.knitting_source,e.customer_buyer";

				foreach ($shift_name as $key => $val) {
					$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
				}
				$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";
				$cust_buyer_cond = ($cust_buyer != "") ? " and e.customer_buyer=$cust_buyer" : "";


				$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,  wo_booking_mst f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$companyID and e.job_no='$job_no' and e.sales_booking_no='$sales_booking_no' $cust_buyer_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_con $within_group_cond  group by b.machine_no_id,a.receive_date, a.receive_basis, a.booking_no, a.knitting_source,e.customer_buyer)";
				$sql_inhouse .= " union all  (SELECT a.receive_basis,a.receive_date, a.booking_no,listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, b.machine_no_id, a.knitting_source,e.customer_buyer";

				foreach ($shift_name as $key => $val) {
					$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
				}
				$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";
				$cust_buyer_cond = ($cust_buyer != "") ? " and e.customer_buyer=$cust_buyer" : "";

				$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$companyID and e.job_no='$job_no' and e.sales_booking_no='$sales_booking_no' $cust_buyer_cond and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $within_group_cond group by b.machine_no_id,a.receive_date, a.receive_basis, a.booking_no, g.entry_form_id, a.knitting_source,e.customer_buyer)";

				$sql_inhouse .= " union all (SELECT a.receive_basis, a.receive_date, a.booking_no, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, b.machine_no_id, a.knitting_source,e.customer_buyer,sum(case when b.shift_name=1 then c.quantity else 0 end) as qntyshifta, sum(case when b.shift_name=2 then c.quantity else 0 end) as qntyshiftb, sum(case when b.shift_name=3 then c.quantity else 0 end) as qntyshiftc";


				$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$companyID and e.job_no='$job_no' and e.sales_booking_no='$sales_booking_no' $cust_buyer_cond and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $within_group_cond group by b.machine_no_id,a.receive_date, a.receive_basis, a.booking_no, a.knitting_source,e.customer_buyer)
					) order by knitting_source,receive_date,machine_no_id";

				$nameArray_inhouse = sql_select($sql_inhouse);

				if ($cbo_knitting_source == 0 || $cbo_knitting_source == 2)
				{
					$txt_date_from = change_date_format(date('d-m-Y', strtotime($txt_date_from)),'','',1);
					$txt_date_to = change_date_format(date('d-m-Y', strtotime($txt_date_to)),'','',1);

					$from_date = $txt_date_from;
					if ($txt_date_to == "") $to_date = $from_date;
					else $to_date = $txt_date_to;

					if ($from_date != "" && $to_date != "") $date_con_sub = " and a.product_date between '$from_date' and '$to_date'";
					else $date_con_sub = "";

					$cust_buyer_cond = ($cust_buyer != "") ? " and d.cust_buyer='$cust_buyer'" : "";

					$const_comp_arr = return_library_array("select id, const_comp from lib_subcon_charge", "id", "const_comp");

					$sql_inhouse_sub = " SELECT  a.product_date as receive_date, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.cons_comp_id as varchar2(4000))),',') within group (order by b.cons_comp_id) as prod_id,

					listagg((cast(b.machine_dia as varchar2(4000))),',') within group (order by b.machine_dia) as machine_dia,
					listagg((cast(b.machine_gg as varchar2(4000))),',') within group (order by b.machine_gg) as machine_gg,

					b.machine_id as machine_no_id,2 as knitting_source, a.knitting_company,
					sum(case when b.shift=1 then b.product_qnty else 0 end) as qntyshifta,
					sum(case when b.shift=2 then b.product_qnty else 0 end) as qntyshiftb,
					sum(case when b.shift=3 then b.product_qnty else 0 end) as qntyshiftc
					from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d
					where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0
					and a.status_active=1 and a.is_deleted=0 and a.company_id=$companyID and d.job_no_mst='$job_no' $cust_buyer_cond $date_con_sub $within_group_cond
					group by a.product_date,a.knitting_source,a.knitting_company, b.machine_id
					order by a.product_date, b.machine_id ";
					//echo $sql_inhouse_sub;
					$nameArray_inhouse_subcon = sql_select($sql_inhouse_sub);

				}

				$tot_machine_arr = array();
				foreach ($nameArray_inhouse as  $row)
				{
					$tot_machine_arr[$row[csf('dtls_id')]]['machine_no_id'] 		= $row[csf('machine_no_id')];
					$tot_machine_arr[$row[csf('dtls_id')]]['booking_no']    		= $row[csf('booking_no')];
					$tot_machine_arr[$row[csf('dtls_id')]]['receive_basis'] 		= $row[csf('receive_basis')];
					$tot_machine_arr[$row[csf('dtls_id')]]['febric_description_id'] = $row[csf('febric_description_id')];
					$tot_machine_arr[$row[csf('dtls_id')]]['knitting_source'] 		= $row[csf('knitting_source')];
					$tot_machine_arr[$row[csf('dtls_id')]]['qntyshift'] 		   += $row[csf('qntyshiftA')]+$row[csf('qntyshiftB')]+$row[csf('qntyshiftC')];
				}

				foreach ($nameArray_inhouse_subcon as  $row)
				{
					$tot_machine_arr[$row[csf('dtls_id')]]['machine_no_id']   = $row[csf('machine_no_id')];
					$tot_machine_arr[$row[csf('dtls_id')]]['machine_dia']     = $row[csf('machine_dia')];
					$tot_machine_arr[$row[csf('dtls_id')]]['machine_gg'] 	  = $row[csf('machine_gg')];
					$tot_machine_arr[$row[csf('dtls_id')]]['knitting_source'] = $row[csf('knitting_source')];
					$tot_machine_arr[$row[csf('dtls_id')]]['prod_id'] 		  = $row[csf('prod_id')];
					$tot_machine_arr[$row[csf('dtls_id')]]['qntyshift'] 	 += $row[csf('qntyshiftA')]+$row[csf('qntyshiftB')]+$row[csf('qntyshiftC')];
				}


					$i=1;
					foreach($tot_machine_arr as $row)
					{
						$machine_dia_gage='';
						if($row['knitting_source'] != 2 )
						{
							if ($row['receive_basis'] == 2) {
								$machine_dia_gage = $knit_plan_arr[$row['booking_no']]['machine_dia'] . " X " . $knit_plan_arr[$row['booking_no']]['machine_gg'];
							} else {
								$machine_dia_gage = $machine_details[$row['machine_no_id']]['dia_width'] . " X " . $machine_details[$row['machine_no_id']]['gauge'];
							}
						}
						else
						{
							$machine_dia = implode(',', array_unique(explode(",", $row['machine_dia'])));
							$machine_gg = implode(',', array_unique(explode(",", $row['machine_gg'])));
							$machine_dia_gage = $machine_dia . " X " . $machine_gg;
						}

						$source='';
						if ($row['knitting_source'] == 1) {
							$source = 'In-House';
						}else if($row['knitting_source'] == 2){
							$source = 'In-Bound Subcontract';
						}else if($row['knitting_source'] == 3){
							$source = 'Out-Bound Subcontract';
						}

						//var_dump($row);
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center" title=""><p><? echo $machine_arr[$row['machine_no_id']]; ?></p></td>
							<td align="center"><p><? echo $machine_dia_gage; ?></p></td>

							<td align="center" title=""><p><?
								$description_arr = array_unique(explode(",", $row['febric_description_id']));
								$all_construction = "";
								foreach ($description_arr as $id) {
									$all_construction .= $construction_arr[$id] . ",";
								}
								$all_construction = chop($all_construction, " , ");


								$prod_id_arr = array_unique(explode(",", $row['prod_id']));

								foreach ($prod_id_arr as $id) {
									$all_construction .= $const_comp_arr[$id] . ", ";
								}
								$all_construction = chop($all_construction, " , ");
								echo $all_construction;

								 ?>
								</P>
							</td>

							<td align="right"><? echo $row['qntyshift']; ?></td>
							<td align="center"><p><? echo $source; ?></p></td>
						</tr>
						<?

						$tot_production+=$row['qntyshift'];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right" title=""><? echo $tot_production; ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>


	</fieldset>
	<?
	exit();
}

if($action=="cust_buyer_machine_popup")
{

	echo load_html_head_contents("machine Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);


	?>
	<fieldset style="width:700px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>


		<div id="scroll_body" align="center">


			<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="100">M/C No</th>
						<th width="100">DiaxGauge</th>
						<th width="200">F. Types</th>
						<th width="80">Production</th>
						<th >Knitting Source</th>
					</tr>
				</thead>
				<tbody>
					<?
					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
					$knit_plan_arr = array();
					$plan_data = sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls");
					foreach ($plan_data as $row)
					{
						$knit_plan_arr[$row[csf('id')]]['machine_dia'] = $row[csf('machine_dia')];
						$knit_plan_arr[$row[csf('id')]]['machine_gg'] = $row[csf('machine_gg')];
					}

					$machine_details = array();
					$machine_data = sql_select("select id,machine_no,dia_width,gauge,brand from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and machine_no is not null");

					foreach ($machine_data as $row)
					{
						$machine_details[$row[csf('id')]]['dia_width'] = $row[csf('dia_width')];
						$machine_details[$row[csf('id')]]['gauge'] = $row[csf('gauge')];

					}

					$composition_arr = $construction_arr = array();
					$sql_deter = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array = sql_select($sql_deter);
					if (count($data_array) > 0) {
						foreach ($data_array as $row) {
							if (array_key_exists($row[csf('id')], $composition_arr)) {
								$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							} else {
								$composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
							}

							$construction_arr[$row[csf('id')]] = $row[csf('construction')];
							$yarn_type_arr[$row[csf('id')]] = $yarn_type[$row[csf('yarn_type')]];
						}
					}


					$txt_date_from = change_date_format(date('d-m-Y', strtotime($txt_date_from)),'','',1);
					$txt_date_to = change_date_format(date('d-m-Y', strtotime($txt_date_to)),'','',1);

					$from_date = $txt_date_from;
					if ( $txt_date_to == "") $to_date = $from_date;
					else $to_date = $txt_date_to;
					$date_con = "";
					if ($from_date != "" && $to_date != "") $date_con = " and a.receive_date between '$from_date' and '$to_date'";

				$sql_inhouse = "SELECT * from (
					(select a.receive_basis, a.receive_date, a.booking_no, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id,  listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id,  b.machine_no_id, e.sales_booking_no,e.within_group,a.knitting_source,e.customer_buyer";

				foreach ($shift_name as $key => $val) {
					$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
				}
				$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";
				$cust_buyer_cond = ($cust_buyer != "") ? " and e.customer_buyer=$cust_buyer" : "";


				$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,  wo_booking_mst f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$companyID $cust_buyer_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $date_con $within_group_cond  group by b.machine_no_id,a.receive_date,e.sales_booking_no, e.within_group,a.receive_basis, a.booking_no,  a.knitting_source,e.customer_buyer)";

				$sql_inhouse .= " union all  (SELECT a.receive_basis, a.receive_date, a.booking_no,listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id,  listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, b.machine_no_id,e.sales_booking_no,e.within_group,a.knitting_source,e.customer_buyer";

				foreach ($shift_name as $key => $val) {
					$sql_inhouse .= ", sum(case when b.shift_name=$key then c.quantity else 0 end) as qntyshift" . strtolower($val);
				}
				$within_group_cond = ($cbo_within_group != 0) ? " and e.within_group=$cbo_within_group" : "";
				$cust_buyer_cond = ($cust_buyer != "") ? " and e.customer_buyer=$cust_buyer" : "";


				$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$companyID  $cust_buyer_cond and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $within_group_cond group by b.machine_no_id,a.receive_date,e.sales_booking_no, e.within_group,a.receive_basis, a.booking_no, a.knitting_source,e.customer_buyer)";

				$sql_inhouse .= " union all (SELECT a.receive_basis, a.receive_date, a.booking_no, listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id,  listagg((cast(b.febric_description_id as varchar2(4000))),',') within group (order by b.febric_description_id) as febric_description_id, b.machine_no_id, e.sales_booking_no,e.within_group,a.knitting_source,e.customer_buyer, sum(case when b.shift_name=1 then c.quantity else 0 end) as qntyshifta, sum(case when b.shift_name=2 then c.quantity else 0 end) as qntyshiftb, sum(case when b.shift_name=3 then c.quantity else 0 end) as qntyshiftc";


				$sql_inhouse .= " from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 and a.company_id=$companyID  $cust_buyer_cond and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_con $within_group_cond group by b.machine_no_id,a.receive_date,e.sales_booking_no, e.within_group,a.receive_basis, a.booking_no, a.knitting_source,e.customer_buyer)
					) order by knitting_source,receive_date,machine_no_id
					";

				//echo $sql_inhouse;
				$nameArray_inhouse = sql_select($sql_inhouse);

				if ($cbo_knitting_source == 0 || $cbo_knitting_source == 2)
				{
					$txt_date_from = change_date_format(date('d-m-Y', strtotime($txt_date_from)),'','',1);
					$txt_date_to = change_date_format(date('d-m-Y', strtotime($txt_date_to)),'','',1);

					$from_date = $txt_date_from;
					if ($txt_date_to == "") $to_date = $from_date;
					else $to_date = $txt_date_to;

					if ($from_date != "" && $to_date != "") $date_con_sub = " and a.product_date between '$from_date' and '$to_date'";
					else $date_con_sub = "";
					$cust_buyer_cond = ($cust_buyer != "") ? " and d.cust_buyer='$cust_buyer'" : "";
					$const_comp_arr = return_library_array("select id, const_comp from lib_subcon_charge", "id", "const_comp");
					//ubcon_ord_mst e //and e.subcon_job=b.job_no and e.subcon_job=d.job_no_mst
					$sql_inhouse_sub = " SELECT a.product_date as receive_date,  listagg((cast(b.id as varchar2(4000))),',') within group (order by b.id) as dtls_id, listagg((cast(b.cons_comp_id as varchar2(4000))),',') within group (order by b.cons_comp_id) as prod_id,

					listagg((cast(b.machine_dia as varchar2(4000))),',') within group (order by b.machine_dia) as machine_dia,
					listagg((cast(b.machine_gg as varchar2(4000))),',') within group (order by b.machine_gg) as machine_gg,

					b.machine_id as machine_no_id, 2 as knitting_source, d.cust_buyer,
					sum(case when b.shift=1 then b.product_qnty else 0 end) as qntyshifta,
					sum(case when b.shift=2 then b.product_qnty else 0 end) as qntyshiftb,
					sum(case when b.shift=3 then b.product_qnty else 0 end) as qntyshiftc
					from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d
					where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0
					and a.status_active=1 and a.is_deleted=0 and a.company_id=$companyID $cust_buyer_cond $date_con_sub $within_group_cond
					group by a.product_date,a.knitting_source, b.machine_id, d.cust_buyer
					order by a.product_date, b.machine_id ";
					//echo $sql_inhouse_sub;
					$nameArray_inhouse_subcon = sql_select($sql_inhouse_sub);

				}



				$tot_machine_arr = array();
				$cbid_machine_arr = array();
				$cb_machine_arr = array();

				foreach ($nameArray_inhouse as  $row)
				{
					if( $row[csf('customer_buyer')] !='' )
					{
						$cbid_machine_arr[$row[csf('dtls_id')]]['machine_no_id'] 		= $row[csf('machine_no_id')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['booking_no']    		= $row[csf('booking_no')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['receive_basis'] 		= $row[csf('receive_basis')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['febric_description_id'] = $row[csf('febric_description_id')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['knitting_source'] 		= $row[csf('knitting_source')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['qntyshift'] 		   += $row[csf('qntyshiftA')]+$row[csf('qntyshiftB')]+$row[csf('qntyshiftC')];
					}
					else
					{
						$cb_machine_arr[$row[csf('dtls_id')]]['machine_no_id'] 		= $row[csf('machine_no_id')];
						$cb_machine_arr[$row[csf('dtls_id')]]['booking_no']    		= $row[csf('booking_no')];
						$cb_machine_arr[$row[csf('dtls_id')]]['receive_basis'] 		= $row[csf('receive_basis')];
						$cb_machine_arr[$row[csf('dtls_id')]]['febric_description_id'] = $row[csf('febric_description_id')];
						$cb_machine_arr[$row[csf('dtls_id')]]['knitting_source'] 		= $row[csf('knitting_source')];
						$cb_machine_arr[$row[csf('dtls_id')]]['qntyshift'] 		   += $row[csf('qntyshiftA')]+$row[csf('qntyshiftB')]+$row[csf('qntyshiftC')];
					}

				}

				foreach ($nameArray_inhouse_subcon as  $row)
				{
					if( $row[csf('cust_buyer')] !='' )
					{
						$cbid_machine_arr[$row[csf('dtls_id')]]['machine_no_id']   = $row[csf('machine_no_id')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['machine_dia']     = $row[csf('machine_dia')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['machine_gg'] 	  = $row[csf('machine_gg')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['knitting_source'] = $row[csf('knitting_source')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['prod_id'] 		  = $row[csf('prod_id')];
						$cbid_machine_arr[$row[csf('dtls_id')]]['qntyshift'] 	 += $row[csf('qntyshiftA')]+$row[csf('qntyshiftB')]+$row[csf('qntyshiftC')];
					}else{
						$cb_machine_arr[$row[csf('dtls_id')]]['machine_no_id']   = $row[csf('machine_no_id')];
						$cb_machine_arr[$row[csf('dtls_id')]]['machine_dia']     = $row[csf('machine_dia')];
						$cb_machine_arr[$row[csf('dtls_id')]]['machine_gg'] 	  = $row[csf('machine_gg')];
						$cb_machine_arr[$row[csf('dtls_id')]]['knitting_source'] = $row[csf('knitting_source')];
						$cb_machine_arr[$row[csf('dtls_id')]]['prod_id'] 		  = $row[csf('prod_id')];
						$cb_machine_arr[$row[csf('dtls_id')]]['qntyshift'] 	 += $row[csf('qntyshiftA')]+$row[csf('qntyshiftB')]+$row[csf('qntyshiftC')];
					}
				}

				if($cust_buyer != ''){
					$tot_machine_arr = array_merge($cbid_machine_arr);
				}else{
					$tot_machine_arr = array_merge($cb_machine_arr);
				}

					$i=1;
					foreach($tot_machine_arr as $row)
					{
						$machine_dia_gage='';
						if($row['knitting_source'] != 2 )
						{
							if ($row['receive_basis'] == 2) {
								$machine_dia_gage = $knit_plan_arr[$row['booking_no']]['machine_dia'] . " X " . $knit_plan_arr[$row['booking_no']]['machine_gg'];
							} else {
								$machine_dia_gage = $machine_details[$row['machine_no_id']]['dia_width'] . " X " . $machine_details[$row['machine_no_id']]['gauge'];
							}
						}
						else
						{
							$machine_dia = implode(',', array_unique(explode(",", $row['machine_dia'])));
							$machine_gg = implode(',', array_unique(explode(",", $row['machine_gg'])));
							$machine_dia_gage = $machine_dia . " X " . $machine_gg;
						}

						$source='';
						if ($row['knitting_source'] == 1) {
							$source = 'In-House';
						}else if($row['knitting_source'] == 2){
							$source = 'In-Bound Subcontract';
						}else if($row['knitting_source'] == 3){
							$source = 'Out-Bound Subcontract';
						}

						//var_dump($row);
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center" title=""><p><? echo $machine_arr[$row['machine_no_id']]; ?></p></td>
							<td align="center"><p><? echo $machine_dia_gage; ?></p></td>

							<td align="center" title=""><p><?
								$description_arr = array_unique(explode(",", $row['febric_description_id']));
								$all_construction = "";
								foreach ($description_arr as $id) {
									$all_construction .= $construction_arr[$id] . ",";
								}
								$all_construction = chop($all_construction, " , ");


								$prod_id_arr = array_unique(explode(",", $row['prod_id']));

								foreach ($prod_id_arr as $id) {
									$all_construction .= $const_comp_arr[$id] . ", ";
								}
								$all_construction = chop($all_construction, " , ");
								echo $all_construction;

								?>
								</P>
							</td>

							<td align="right"><? echo $row['qntyshift']; ?></td>
							<td align="center"><p><? echo $source; ?></p></td>
						</tr>
						<?

						$tot_production+=$row['qntyshift'];
						$i++;


					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right" title=""><? echo $tot_production; ?>&nbsp;</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>


	</fieldset>
	<?
	exit();
}

if($action=="shift_qty_popup")
{

	echo load_html_head_contents("Production Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:490px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>


		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="470" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="100">Production ID</th>
						<th width="40">Shift</th>
						<th width="80"> Roll No</th>
						<th width="70"> Time</th>
						<th width="70">Qty </th>
						<th >Rej. Qty </th>
					</tr>
				</thead>
				<tbody>
					<?
					$mstData = sql_select("SELECT booking_no, booking_without_order, receive_basis from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

					if ($mstData[0][csf('receive_basis')] == 2)
					{

						$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $mstData[0][csf('booking_no')]);
						if ($is_salesOrder == 1)
						{
							$query = "SELECT a.id as dtls_id,a.grey_receive_qnty,a.no_of_roll,a.insert_date as ins_date,a.reject_fabric_receive,b.roll_no,b.barcode_no,b.po_breakdown_id,b.qnty,b.reject_qnty,b.insert_date
							FROM pro_grey_prod_entry_dtls a left join pro_roll_details b on a.id=b.dtls_id and b.status_active=1 and b.is_deleted=0 and  b.entry_form = 2, fabric_sales_order_mst c
							where a.order_id=c.id and a.id=$dtls_id and a.status_active=1 and a.is_deleted=0";
						}
						elseif($is_salesOrder == 2)
						{
							$query = "SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.booking_no as po_number, a.dtls_id, a.reject_qnty, a.insert_date
							from pro_roll_details a, wo_non_ord_samp_booking_mst b
							where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 order by a.barcode_no";
						}
						else
						{
							$query = "SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number, a.dtls_id, a.reject_qnty, a.insert_date
							from pro_roll_details a, wo_po_break_down b
							where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 order by a.barcode_no";
						}
					}
					else
					{
						if ($mstData[0][csf('booking_without_order')] == 1)
						{
							$query = "SELECT id,roll_no,barcode_no,po_breakdown_id,qnty,booking_no as po_number, dtls_id, reject_qnty, a.insert_date
							from pro_roll_details
							where dtls_id=$dtls_id and entry_form=2 and status_active=1 and is_deleted=0 order by barcode_no";
						}
						else
						{
							$query = "SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number, a.dtls_id, a.reject_qnty, a.insert_date
							from pro_roll_details a, wo_po_break_down b
							where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 order by a.barcode_no";
						}
					}
					//echo $query;

					$query_result = sql_select($query);
					$production_qty_arr = array();
					$production_qnty_arr = array();
					$production_dtls_arr = array();
					foreach ($query_result as $row)
					{
						if($row[csf('barcode_no')] !="")
						{
							$production_qty_arr[$row[csf('barcode_no')]]['insert_date'] = $row[csf('insert_date')];
							$production_qty_arr[$row[csf('barcode_no')]]['dtls_id'] = $row[csf('dtls_id')];
							$production_qty_arr[$row[csf('barcode_no')]]['barcode_no'] = $row[csf('barcode_no')];
							$production_qty_arr[$row[csf('barcode_no')]]['qnty'] += $row[csf('qnty')];
							$production_qty_arr[$row[csf('barcode_no')]]['reject_qnty'] += $row[csf('reject_qnty')];
							$production_dtls_arr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
						}
						else
						{
							$production_qnty_arr[$row[csf('id')]]['insert_date'] = $row[csf('ins_date')];
							$production_qnty_arr[$row[csf('id')]]['dtls_id'] = $row[csf('dtls_id')];
							$production_qnty_arr[$row[csf('id')]]['barcode_no'] = $row[csf('no_of_roll')];
							$production_qnty_arr[$row[csf('id')]]['qnty'] += $row[csf('grey_receive_qnty')];
							$production_qnty_arr[$row[csf('id')]]['reject_qnty'] += $row[csf('reject_fabric_receive')];
							$production_dtls_arr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
						}

					}
					//var_dump($production_qnty_arr);

					$production_dtls_arr =array_filter($production_dtls_arr);

					if(count($production_dtls_arr)>0)
					{
						$production_dtls = implode(",", $production_dtls_arr);
						$all_production_dtls_cond=""; $production_dtls_Cond="";
						if($db_type==2 && count($production_dtls_arr)>999)
						{
							$production_dtls_arr_chunk=array_chunk($production_dtls_arr,999) ;
							foreach($yarn_issue_id_arr_chunk as $chunk_arr)
							{
								$chunk_arr_value=implode(",",$chunk_arr);
								$production_dtls_Cond.="  b.id in($chunk_arr_value) or ";
							}
							$all_production_dtls_cond.=" and (".chop($production_dtls_Cond,'or ').")";
						}
						else
						{
							$all_production_dtls_cond=" and b.id in($production_dtls)";
						}

						$inout_data_array = sql_select("SELECT b.id, a.recv_number, a.recv_number_prefix_num,b.shift_name from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id $all_production_dtls_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

						$production_id_arr=array();
						foreach ($inout_data_array as $row)
						{
							$production_id_arr[$row[csf('id')]]['recv_number'] = $row[csf('recv_number')];
							$production_id_arr[$row[csf('id')]]['shift_name'] = $row[csf('shift_name')];
						}
						//var_dump($production_id_arr);
					}

					//============= For In-Bound Subcontract ====================

					$ib_query = "SELECT a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, a.dtls_id, a.reject_qnty, a.insert_date
					from subcon_pro_roll_details a, subcon_ord_dtls b
					where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 order by a.id";
					//echo $ib_query;
					$ib_query_result = sql_select($ib_query);

					foreach ($ib_query_result as $row)
					{
						$production_qty_arr[$row[csf('barcode_no')]]['insert_date'] = $row[csf('insert_date')];
						$production_qty_arr[$row[csf('barcode_no')]]['dtls_id'] = $row[csf('dtls_id')];
						$production_qty_arr[$row[csf('barcode_no')]]['barcode_no'] = $row[csf('barcode_no')];
						$production_qty_arr[$row[csf('barcode_no')]]['qnty'] += $row[csf('qnty')];
						$production_qty_arr[$row[csf('barcode_no')]]['reject_qnty'] += $row[csf('reject_qnty')];
						$production_dtls_arr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
					}
					//var_dump($production_dtls_arr);

					$production_dtls_arr =array_filter($production_dtls_arr);

					if(count($production_dtls_arr)>0)
					{
						$production_dtls = implode(",", $production_dtls_arr);
						$all_production_dtls_cond=""; $production_dtls_Cond="";
						if($db_type==2 && count($production_dtls_arr)>999)
						{
							$production_dtls_arr_chunk=array_chunk($production_dtls_arr,999) ;
							foreach($yarn_issue_id_arr_chunk as $chunk_arr)
							{
								$chunk_arr_value=implode(",",$chunk_arr);
								$production_dtls_Cond.="  b.id in($chunk_arr_value) or ";
							}
							$all_production_dtls_cond.=" and (".chop($production_dtls_Cond,'or ').")";
						}
						else
						{
							$all_production_dtls_cond=" and b.id in($production_dtls)";
						}

						$inBound_data_array = sql_select("SELECT b.id,a.product_no, a.prefix_no_num, b.shift from subcon_production_mst a, subcon_production_dtls b where a.entry_form=159 and a.id=b.mst_id $all_production_dtls_cond and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id DESC");

						foreach ($inBound_data_array as $row)
						{
							$production_id_arr[$row[csf('id')]]['recv_number'] = $row[csf('product_no')];
							$production_id_arr[$row[csf('id')]]['shift_name'] = $row[csf('shift')];
						}
						//var_dump($production_id_arr);
					}

					$i=1;
					if(!empty($production_qty_arr))
					{
						foreach($production_qty_arr as $row)
						{
							//var_dump($row);
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//strtotime($row['insert_date']);
							//$date = "30-12-1899 9:25:52 AM";
							//$date = strtotime($row['insert_date']);
							$time=date('H:i:s A', strtotime($row['insert_date']));
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $production_id_arr[$row['dtls_id']]['recv_number']; ?></p></td>
								<td align="center"><p><? echo $shift_name[$production_id_arr[$row['dtls_id']]['shift_name']]; ?></p></td>
								<td align="center"><? echo $row['barcode_no']; ?></td>
								<td align="center"><? echo $time; ?></td>
								<td align="right"><p><? echo $row['qnty']; ?></p></td>
								<td align="right"><p><? echo $row['reject_qnty']; ?></p></td>
							</tr>
							<?

							$tot_production+=$row['qnty'];
							$tot_production_reject_qnty+=$row['reject_qnty'];
							$i++;

						}
					}
					else
					{
						foreach($production_qnty_arr as $row)
						{
							//var_dump($row);
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//strtotime($row['insert_date']);
							//$date = "30-12-1899 9:25:52 AM";
							//$date = strtotime($row['insert_date']);
							$time=date('H:i:s A', strtotime($row['insert_date']));
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $production_id_arr[$row['dtls_id']]['recv_number']; ?></p></td>
								<td align="center"><p><? echo $shift_name[$production_id_arr[$row['dtls_id']]['shift_name']]; ?></p></td>
								<td align="center"><? echo $row['barcode_no']; ?></td>
								<td align="center"><? echo $time; ?></td>
								<td align="right"><p><? echo $row['qnty']; ?></p></td>
								<td align="right"><p><? echo $row['reject_qnty']; ?></p></td>
							</tr>
							<?

							$tot_production+=$row['qnty'];
							$tot_production_reject_qnty+=$row['reject_qnty'];
							$i++;

						}
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right" title=""><? echo $tot_production; ?>&nbsp;</td>
						<td align="right" title=""><? echo $tot_production_reject_qnty; ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>


	</fieldset>
	<?
	exit();
}

//getYarnType
function getYarnType($yarn_type_arr, $yarnProdId)
{
	global $yarn_type;
	$yarn_type_name = '';
	$expYPId = explode(",", $yarnProdId);
	$yarnTypeIdArr = array();
	foreach ($expYPId as $key => $val) {
		$yarnTypeIdArr[$yarn_type_arr[$val]] = $yarn_type_arr[$val];
	}

	foreach ($yarnTypeIdArr as $key => $val) {
		if ($yarn_type_name == '')
			$yarn_type_name = $yarn_type[$val];
		else
			$yarn_type_name .= "," . $yarn_type[$val];
	}
	return $yarn_type_name;
}
?>