<?php

use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;

header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.yarns.php');

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if ($_SESSION['logic_erp']["buyer_id"] != "") {
	$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
} else {
	$buyer_id_cond = "";
}

// Pending Bookings FSO
if ($action == 'show_change_pending_fso') {
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$company_id = $data;

	$sql = " SELECT a.id, a.job_no, a.sales_booking_no, a.booking_date, a.customer_buyer, SUM(b.grey_qty) AS grey_qty, COALESCE(c.qnty, 0) AS allocated_qty FROM fabric_sales_order_mst a LEFT JOIN fabric_sales_order_dtls b ON a.id = b.mst_id LEFT JOIN ( SELECT job_no, SUM(qnty) AS qnty FROM inv_material_allocation_dtls GROUP BY job_no ) c ON a.job_no = c.job_no WHERE a.entry_form IN (109, 472) AND a.status_active = 1 AND a.is_deleted = 0 AND a.company_id = $company_id AND a.fso_status = 1 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.id, a.job_no, c.qnty, a.sales_booking_no, a.booking_date, a.customer_buyer ORDER BY a.job_no DESC";
	// echo $sql;die;
	$result = sql_select($sql);

	// echo "<pre>";
	// print_r($result);die;

	$allocation_arr = array();

	foreach ($result as $row) {
		$allocation_arr[$row[csf("customer_buyer")]][$row[csf("job_no")]][$row[csf("sales_booking_no")]]['booking_date']  = $row[csf("booking_date")];
		$allocation_arr[$row[csf("customer_buyer")]][$row[csf("job_no")]][$row[csf("sales_booking_no")]]['grey_qty']  = $row[csf("grey_qty")];
		$allocation_arr[$row[csf("customer_buyer")]][$row[csf("job_no")]][$row[csf("sales_booking_no")]]['allocated_qty']  = $row[csf("allocated_qty")];
	}

	// echo "<pre>";
	// print_r($allocation_arr);die;

?>
	<!-- for contents type search  -->
	<input type="hidden" id="cbo_string_search_type" value="4" />
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660">
		<thead>
			<tr>
				<th colspan="8">Allocation Pending</th>
			</tr>
			<tr>
				<th colspan="8">Need To Allocated</th>
			</tr>
			<tr>
				<th width="50" align="center">SL No</th>
				<th width="50">Cust Buyer</th>
				<th width="110">FSO</th>
				<th width="90">F.Booking No</th>
				<th width="90">Booking Date</th>
				<th width="90">Grey Qty</th>
				<th width="90">Allocated Qty</th>
				<th width="90">Allocation Pending</th>
			</tr>
		</thead>
	</table>
	<div style="width:660px; max-height:130px; overflow-y:scroll" id="list_container_batch">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="660" class="rpt_table" id="tbl_list_search_pending_fso">
			<tbody>
				<?
				$i = 0;
				foreach ($allocation_arr as $buyer_key => $buyer_val) {
					foreach ($buyer_val as $job_key => $job_val) {
						$i++;
						foreach ($job_val as $booking_key => $booking_val) {
							$allocated_balance = number_format(($booking_val['grey_qty'] - $booking_val['allocated_qty']), 2);
							if ($allocated_balance == 0) {
								$i--;
								continue;
							}
				?>
							<!-- <tr style="background-color: <? //if($booking_val['allocated_qty'] == 0){ echo "yellow";} else{echo "white";} 
																?>"> -->
							<tr>
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="50" align="center"><? echo $buyer_arr[$buyer_key]; ?></td>
								<td width="110" align="center"><? echo $job_key; ?></td>
								<td width="90" align="center"><? echo $booking_key; ?></td>
								<td width="90" align="center"><? echo $booking_val['booking_date']; ?></td>
								<td width="90" align="center"><? echo number_format($booking_val['grey_qty'], 2); ?></td>
								<td width="90" align="center">
									<p><? echo number_format($booking_val['allocated_qty'], 2); ?></p>
								</td>
								<td width="90" align="center"><? echo $allocated_balance; ?></td>
							</tr>
				<?
						}
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	</div>
<?
	exit();
}

//actn_sales_order_popup
if ($action == "actn_sales_order_popup") 
{
	echo load_html_head_contents("Sales Order Search", "../../../", 1, 1, $unicode);
   ?>
	<script>
		function js_set_value(data) {
			$('#hdn_data').val(data);
			parent.emailwindow.hide();
		}

		//for active_inactive
		function active_inactive() {
			var within_group = $('#cbo_within_group').val();
			var company_id = $('#cbo_company_id').val();
			if (company_id == 0) {
				$("#cbo_customer option[value!='0']").remove();
				$("#cbo_customer_buyer option[value!='0']").remove();
			}
		}

		//for func_active_inactive
		function func_active_inactive() {
			var within_group = $('#cbo_within_group').val();
			var company_id = $('#cbo_company_id').val();
			if (company_id == 0) {
				$("#cbo_customer option[value!='0']").remove();
				$("#cbo_customer_buyer option[value!='0']").remove();
			} else {
				load_drop_down('yarn_allocation_sales_controller', within_group + '_' + company_id, 'load_drop_down_buyer', 'buyer_td');
				load_drop_down('yarn_allocation_sales_controller', within_group + '_' + company_id, 'load_drop_down_cust_buyer', 'cust_buyer_td');
			}
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="1200" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
					<thead>
						<th class="must_entry_caption">Company</th>
						<th>Within Group</th>
						<th>Customer</th>
						<th>Cust. Buyer</th>
						<th>Sales Job/Booking No</th>
						<th title="Internal Ref./Internal Booking">IR/IB</th>
						<th>Sales Order No</th>
						<th>Date Range</th>
						<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="hidden" id="hdn_data" name="hdn_data">
							<?
							echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", '', "active_inactive()");
							?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_within_group", 100, $yes_no, "", 1, "-- Select --", 0, "func_active_inactive()", "");
							?>
						</td>
						<td id="buyer_td"> <? echo create_drop_down("cbo_customer", 150, $blank_array, "", 1, "--Select--"); ?></td>
						<td id="cust_buyer_td"> <? echo create_drop_down("cbo_customer_buyer", 150, $blank_array, "", 1, "--Select--"); ?></td>
						<td>
							<input type="text" style="width:100px" class="text_boxes" name="txt_sales_job_booking" id="txt_sales_job_booking" placeholder="write" />
						</td>
						<td>
							<input type="text" style="width:100px" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" placeholder="write" />
						</td>
						<td>
							<input type="text" style="width:100px" class="text_boxes" name="txt_sales_order_no" id="txt_sales_order_no" placeholder="write" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_customer').value+'_'+document.getElementById('cbo_customer_buyer').value+'_'+document.getElementById('txt_sales_job_booking').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_internal_ref').value, 'actn_sales_order_listview', 'search_div', 'yarn_allocation_sales_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:5px"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

//actn_sales_order_listview
if ($action == "actn_sales_order_listview") {
	$data = explode('_', $data);
	//for company
	if ($data[0] != 0)
		$company_cond = " and company_id=" . $data[0] . "";
	else {
		echo "Please Select Company First.";
		die;
	}

	//for within group
	$within_group_cond = '';
	if ($data[1] != 0) {
		$within_group_cond = " and within_group = " . $data[1] . "";
	}

	//for customer
	$customer_cond = '';
	if ($data[2] != 0) {
		$customer_cond = " and buyer_id = " . $data[2] . "";
	}

	//for customer buyer
	$customer_buyer_cond = '';
	if ($data[3] != 0) {
		$customer_buyer_cond = " and customer_buyer = " . $data[3] . "";
	}

	//for sales job/booking no
	$sales_job_booking_cond = '';
	if ($data[4] != 0) {
		$sales_job_booking_cond = " and sales_booking_no like '%" . $data[4] . "'";
	}

	//for sales order no
	$sales_order_cond = '';
	if ($data[5] != 0) {
		$sales_order_cond = " and job_no like '%" . $data[5] . "'";
	}

	//for date
	$date_cond = '';
	if ($data[6] != "" && $data[7] != "") {
		if ($db_type == 0) {
			$date_cond = " and booking_date between '" . change_date_format($data[6], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[7], "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and booking_date between '" . change_date_format($data[6], '', '', 1) . "' and '" . change_date_format($data[7], '', '', 1) . "'";
		}
	}

	//for year
	$year_cond = '';
	$year_cond2 = '';
	if ($db_type == 0) {
		$year_cond = " and YEAR(insert_date) = " . $data[8] . "";
		$year_cond2 = " and YEAR(a.insert_date) = " . $data[8] . "";
	} else if ($db_type == 2) {
		$year_cond = " and to_char(insert_date,'YYYY') = " . $data[8] . "";
		$year_cond2 = " and to_char(a.insert_date,'YYYY') = " . $data[8] . "";
	}

	//for year field
	if ($db_type == 0)
		$year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(insert_date,'YYYY') as year";
	else
		$year_field = "";

	//for internal ref.
	$internalRef_cond = '';
	$booking_nos_cond = '';
	if ($data[9] != "") {
		$internalRef_cond = " and a.grouping like '%" . $data[9] . "%'";
	}

	$sql_bookings = sql_select("select b.booking_no,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond $year_cond2");
	$booking_nos = "";
	$bookingArrChk = array();
	$internalRefInfoArr = array();
	foreach ($sql_bookings as $row) {
		if ($bookingArrChk[$row[csf('booking_no')]] != $row[csf('booking_no')]) {
			$booking_nos .= "'" . $row[csf('booking_no')] . "',";
			$bookingArrChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
			$internalRefInfoArr[$row[csf('booking_no')]]['internal_ref'] .= $row[csf('grouping')] . ",";
		}
	}
	$booking_nos = chop($booking_nos, ",");
	if ($data[9] != "") {
		$booking_nos_cond = "and sales_booking_no in($booking_nos)";
	}
	unset($sql_bookings);


	//main query
	$sql = "SELECT id, $year_field, job_no_prefix_num, job_no, company_id, within_group, sales_order_type, sales_booking_no, booking_date, buyer_id, customer_buyer,style_ref_no,po_job_no from fabric_sales_order_mst where entry_form in (109,472) and status_active=1 and is_deleted=0 $company_cond $within_group_cond $customer_cond $customer_buyer_cond $sales_job_booking_cond $sales_order_cond $date_cond $year_cond $booking_nos_cond and fso_status=1 order by id desc";
	// echo $sql; die;
	$result = sql_select($sql);
	if (empty($result)) {
		echo "<div style='width:610px; text-align:center'>" . get_empty_data_msg() . "</div>";
		die;
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$within_group = $data[3];
	$sales_order_type = $data[4];

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and job_no like '%" . $search_string . "'";
		} else if ($search_by == 2) {
			$search_field_cond = " and sales_booking_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and style_ref_no like '" . $search_string . "%'";
		}
	}

	if ($within_group == 0) $within_group_cond = "";
	else $within_group_cond = " and within_group=$within_group";
	if ($sales_order_type == 0) $sales_type_cond = "";
	else $sales_type_cond = " and sales_order_type=$sales_order_type";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = ""; //defined Later
	$booking_arr = array();
	$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1 and a.supplier_id=$company_id");
	foreach ($booking_info as $row) {
		$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
		$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
		$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
		$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
		$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
		$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
	}
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="60">Year</th>
			<th width="100">Sales Job/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="120">Customer</th>
			<th width="120">Cust. Buyer</th>
			<th width="150">Sales Order No</th>
			<th width="150">IR/IB</th>
			<th>Style Ref</th>
		</thead>
	</table>
	<div style="width:1010px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1) {
					$buyer = $company_arr[$row[csf('buyer_id')]];
					$customer_buyer = $buyer_arr[$row[csf('customer_buyer')]];
				} else {
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
					$customer_buyer = $buyer_arr[$row[csf('customer_buyer')]];
				}

				$data = $row[csf('id')] . "**" . $row[csf('job_no')] . "**" . $row[csf('company_id')] . "**" . $row[csf('buyer_id')] . "**" . $row[csf('customer_buyer')] . "**" . $row[csf('within_group')] . "**" . $row[csf('sales_booking_no')] . "**" . $row[csf('po_job_no')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="100">
						<p><? echo $row[csf('sales_booking_no')]; ?></p>
					</td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="120">
						<p><? echo $buyer; ?>&nbsp;</p>
					</td>
					<td width="120">
						<p><? echo $customer_buyer; ?>&nbsp;</p>
					</td>
					<td width="150">
						<p><? echo $row[csf('job_no')]; ?></p>
					</td>
					<td width="150">
						<p><? echo chop($internalRefInfoArr[$row[csf('sales_booking_no')]]['internal_ref'], ","); ?></p>
					</td>
					<td>
						<p><? echo $row[csf('style_ref_no')]; ?></p>
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

//load_drop_down_buyer
if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0) {
		echo create_drop_down("cbo_customer", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} else {
		if ($data[0] == 0) {
			echo create_drop_down("cbo_customer", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
		} else if ($data[0] == 1) {
			echo create_drop_down("cbo_customer", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 " . $company_cond . " order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 1);
		} else if ($data[0] == 2) {
			echo create_drop_down("cbo_customer", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='" . $company_id . "' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
	}
	exit();
}

//load_drop_down_cust_buyer
if ($action == "load_drop_down_cust_buyer") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0) {
		echo create_drop_down("cbo_customer_buyer", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} else {
		if ($data[0] == 0) {
			echo create_drop_down("cbo_customer_buyer", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
		} else {
			echo create_drop_down("cbo_customer_buyer", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_buyer_brand', 'cust_buyer_brand_td' );load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_season', 'season_td' )", 0);
		}
	}
	exit();
}

//actn_fabric_description_listview
if ($action == "actn_fabric_description_listview") {
?>
	<table cellspacing="0" width="340" align="left" class="rpt_table" border="0" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="220">Fabric Description</th>
				<th width="90">Booking Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			//SALES_ORDER_NO
			$sql = "SELECT ID, MST_ID, JOB_NO_MST, DETERMINATION_ID, FABRIC_DESC, FINISH_QTY, GREY_QTY FROM FABRIC_SALES_ORDER_DTLS WHERE MST_ID = " . $data . " AND STATUS_ACTIVE = 1 AND IS_DELETED = 0";
			$sql_rslt = sql_select($sql);
			if (empty($sql_rslt)) {
				echo "<tr><td colspan = '3' style='width:340px; text-align:center'>" . get_empty_data_msg() . "</td><tr>";
				die;
			}

			$data_arr = array();
			foreach ($sql_rslt as $row) {
				$data_arr[$row['JOB_NO_MST']][$row['DETERMINATION_ID']]['sales_dtls_id'][$row['ID']] = $row['ID'];
				$data_arr[$row['JOB_NO_MST']][$row['DETERMINATION_ID']]['qty'] += $row['GREY_QTY'];
				$data_arr[$row['JOB_NO_MST']][$row['DETERMINATION_ID']]['fabric_desc'] = $row['FABRIC_DESC'];
			}
			/*echo "<pre>";
		print_r($data_arr);
		echo "</pre>";*/

			$i = 1;
			$total_qnty = 0;
			foreach ($data_arr as $sales_order_no => $sales_order_no_arr) {
				foreach ($sales_order_no_arr as $fabric_desc => $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
			?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="yrn_list_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td>
							<div onClick="func_yarn_listview('<? echo $i; ?>','<? echo implode(',', $row['sales_dtls_id']); ?>')" style="text-decoration: underline; cursor: pointer;">
								<? echo $row['fabric_desc']; ?>
							</div>
						</td>
						<td align="right">
							<?
							echo number_format($row['qty'], 2);
							$total_qnty += number_format($row['qty'], 2, '.', '');
							?>
						</td>
					</tr>
			<?
					$i++;
				}
			}
			?>
		</tbody>
		<tfoot>
			<th colspan="2">Total</th>
			<th><? echo number_format($total_qnty, 2); ?></th>
		</tfoot>
	</table>
<?
	exit();
}

//actn_yrn_desc_listview
if ($action == "actn_yrn_desc_listview") {
?>
	<table cellspacing="0" width="340" class="rpt_table" border="0" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="220">Yarn Description</th>
				<th width="90">Required Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			$sql = "SELECT YARN_DATA FROM FABRIC_SALES_ORDER_YARN WHERE FSO_DTLS_IDS IN('" . $data . "') AND STATUS_ACTIVE=1 AND IS_DELETED=0";
			$sql_rslt = sql_select($sql);
			if (empty($sql_rslt)) {
				echo "<tr><td colspan = '3' style='width:340px; text-align:center'>" . get_empty_data_msg() . "</td><tr>";
				die;
			}

			$yrn_count_dtls = get_yarn_count_array();
			$yrn_color_dtls = get_color_array();
			$data_arr = array();
			foreach ($sql_rslt as $row) {
				$exp_yrn_data = array();
				$exp_yrn_data = explode('|', $row['YARN_DATA']);
				foreach ($exp_yrn_data as $key => $val) {
					$exp_yrn = array();
					$exp_yrn = explode('_', $val);
					$yrn_count = $exp_yrn[0];
					$yrn_composition = $exp_yrn[1];
					$yrn_percentage = $exp_yrn[2];
					$yrn_color = $exp_yrn[3];
					$yrn_type = $exp_yrn[4];
					$yrn_qty = $exp_yrn[6];
					$data_arr[$yrn_count][$yrn_composition][$yrn_percentage][$yrn_type][$yrn_color]['qty'] += $yrn_qty;
				}
			}

			$i = 1;
			$total_qnty = 0;
			foreach ($data_arr as $yrn_cnt => $yrn_cnt_arr) {
				foreach ($yrn_cnt_arr as $yrn_compo => $yrn_compo_arr) {
					foreach ($yrn_compo_arr as $yrn_percent => $yrn_percent_arr) {
						foreach ($yrn_percent_arr as $yrn_type => $yrn_type_arr) {
							foreach ($yrn_type_arr as $yrn_color => $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$yrn_desc = $yrn_count_dtls[$yrn_cnt] . ' ' . $composition[$yrn_compo] . ' ' . $yrn_percent . ' ' . $yarn_type[$yrn_type] . ' ' . $yrn_color_dtls[$yrn_color];
			?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="fab_list_<? echo $i; ?>">
									<td><? echo $i; ?></td>
									<td>
										<? echo $yrn_desc; ?>
									</td>
									<td align="right">
										<?
										echo number_format($row['qty'], 2);
										$total_qnty += number_format($row['qty'], 2, '.', '');
										?>
									</td>
								</tr>
			<?
								$i++;
							}
						}
					}
				}
			}
			?>
		</tbody>
		<tfoot>
			<th colspan="2">Total</th>
			<th><? echo number_format($total_qnty, 2); ?></th>
		</tfoot>
	</table>
<?
	exit();
}

//actn_yarn_description_listview
if ($action == "actn_yarn_description_listview") {
	$expData = explode("_", $data);
	$sales_order_no = $expData[0];
	$booking_no = $expData[1];
	$expBookinNo = explode("-", $booking_no);

?>

	<table cellspacing="0" width="440" class="rpt_table" border="0" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="220">Yarn Description</th>
				<th width="100">Color</th>
				<th width="90">Required Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			$sql = "SELECT YARN_COUNT_ID, COMPOSITION_ID, COMPOSITION_PERC, COLOR_ID, YARN_TYPE,CONS_QTY FROM FABRIC_SALES_ORDER_YARN_DTLS WHERE MST_ID = " . $sales_order_no . " AND STATUS_ACTIVE=1 AND IS_DELETED=0";

			$sql_rslt = sql_select($sql);
			if (empty($sql_rslt)) {
				echo "<tr><td colspan = '4' style='width:340px; text-align:center'>" . get_empty_data_msg() . "</td><tr>";
				die;
			}
			$yrn_count_dtls = get_yarn_count_array();
			$yrn_color_dtls = get_color_array();
			$data_arr = $yarn_composition_arr = array();
			$i = 1;
			$total_qnty = 0;
			foreach ($sql_rslt as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$yrn_desc = $yrn_count_dtls[$row[csf('YARN_COUNT_ID')]] . ' ' . $composition[$row[csf('COMPOSITION_ID')]] . ' ' . $row[csf('COMPOSITION_PERC')] . ' ' . $yarn_type[$row[csf('YARN_TYPE')]] . ' ' . $yrn_color_dtls[$row[csf('COLOR_ID')]];
				$yarn_composition_arr[$row[csf('COMPOSITION_ID')]] = $row[csf('COMPOSITION_ID')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="fab_list_<? echo $i; ?>">
					<td><? echo $i; ?></td>
					<td title="<? echo $row[csf('COMPOSITION_ID')]; ?>"><? echo $yrn_desc; ?></td>
					<td align="center"><? echo $yrn_color_dtls[$row[csf('COLOR_ID')]]; ?></td>
					<td align="right">
						<?
						echo number_format($row[csf('CONS_QTY')], 2);
						$total_qnty += number_format($row[csf('CONS_QTY')], 2, '.', '');
						?>
					</td>
				</tr>
			<?
				$i++;
			}
			?>
		</tbody>
		<tfoot>
			<th colspan="3">Total</th>
			<th><? echo number_format($total_qnty, 2); ?></th>
			<input type="hidden" id="hdn_required_compositions" value="<? echo $required_compositions = implode(",", array_unique($yarn_composition_arr)); ?>">
		</tfoot>
	</table>
<?
	exit();
}


/* if($action == "actn_asper_budget_yarn_description_listview")
{
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$condition= new condition();
	if(str_replace("'","",$data) !='')
	{
		$condition->job_no("='$data'");
	}

	$condition->init();
	$yarn= new yarn($condition);

	$totYarn=0;
	$YarnData=array();
	$yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();

	//print_r($yarn_data_array);
	$sql_yarn="select f.id as yarn_id, f.cons_ratio, f.cons_qnty, f.avg_cons_qnty, f.rate, f.amount, count_id, copm_one_id, percent_one, color, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='".$data."' and f.is_deleted=0 and f.status_active=1  order by f.id";
	//echo $sql_yarn;
	$data_arr_yarn=sql_select($sql_yarn);
	foreach($data_arr_yarn as $yarn_row)
	{
		$yarnrate=$yarn_row[csf("rate")];
		$summary_data[yarn_cost][$yarn_row[csf("yarn_id")]]=$yarn_row[csf("amount")];
		$summary_data[yarn_cost_job]+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
		$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."_".$yarn_row[csf("color")]."_".$yarnrate."'";
		$YarnData[$index]['qty']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
		$YarnData[$index]['amount']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
		$YarnData[$index]['dznqty']+=$yarn_row[csf("cons_qnty")];
		$YarnData[$index]['dznamount']+=$yarn_row[csf("amount")];
		$totYarn+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
	}
	?>

	<fieldset>
	<legend>Asper Budget</legend>
	<table cellspacing="0" width="680" class="rpt_table" border="0" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="250">Yarn Desc</th>
				<th width="100">Yarn Qty</th>
				<th width="100">Avg.Yarn Qnty</th>
				<th width="100">Rate</th>
				<th width="100">Tot.Amount</th>
			</tr>
		</thead>
        <tbody>
		<?
		$i = 1;
		$TotalDznQty = 0; $TotalQty = 0; $TotalDznAmount = 0; $TotalAmount = 0;
		foreach( $YarnData as $index=>$row )
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			$des=explode("_",str_replace("'","",$index));
			$item_descrition = $lib_yarn_count[$des[0]]." ".$composition[$des[1]]." ".$des[2]."% ".$color_library[$des[4]]." ".$yarn_type[$des[3]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="fab_list_<? echo $i;?>">
				<td><? echo $i; ?></td>
				<td align="left"><? echo $item_descrition; ?></td>
				<td align="right"><? echo fn_number_format($row["qty"],4);//echo fn_number_format($row["dznqty"],4); ?></td>
				<td align="right"><? echo fn_number_format($row["qty"],4); ?></td>
				<td align="right"><? echo fn_number_format($des[5],4); ?></td>
				<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
			</tr>
			<?
			 $TotalDznQty += $row["dznqty"];
			 $TotalQty += $row["qty"];
			 $TotalDznAmount += $row["dznamount"];
			 $TotalAmount += $row["amount"];

			 $GrandTotalFabricDznAmount+=$row["dznamount"];
			 $GrandTotalFabricAmount+=$row["amount"];
			 $i++;
		}
		?>
        </tbody>
		<tfoot>
		<tr class="rpt_bottom" style="font-weight:bold">
			<th colspan=2 align="left">Total</th>
			<th align="right"><? echo fn_number_format($TotalQty,4);//echo fn_number_format($TotalDznQty,4); ?></td>
			<th align="right"><? echo fn_number_format($TotalQty,4); ?></th>
			<th>&nbsp;</th>
			<th align="right"><? echo fn_number_format($TotalAmount,4); ?></th>
		</tr>
		</tfoot>
	</table>
	</fieldset>
	<?
	exit();
} */

if ($action == "actn_asper_yarn_purchase_requisition_listview") {
	$lib_yarn_count = return_library_array("select yarn_count,id from lib_yarn_count", "id", "yarn_count");

	//print_r($yarn_data_array);
	$data_arr = explode("**", $data);

	$sql_yarn = "select f.quantity,f.rate, f.amount, count_id, composition_id, com_percent, color_id, yarn_type_id from inv_purchase_requisition_dtls f where f.job_no ='" . $data_arr[0] . "' and booking_no='" . $data_arr[1] . "' and f.is_deleted=0 and f.status_active=1";
	//echo $sql_yarn;
	$data_arr_yarn = sql_select($sql_yarn);
	foreach ($data_arr_yarn as $yarn_row) {
		$index = "'" . $yarn_row[csf("count_id")] . "_" . $yarn_row[csf("composition_id")] . "_" . $yarn_row[csf("com_percent")] . "_" . $yarn_row[csf("yarn_type_id")] . "_" . $yarn_row[csf("color_id")] . "'";
		$YarnData[$index]['qty'] += $yarn_row[csf("quantity")];
		$YarnData[$index]['amount'] += $yarn_row[csf("amount")];
		$YarnData[$index]['rate'] += $yarn_row[csf("rate")];
	}
?>

	<fieldset>
		<legend>As per Yarn Booking</legend>
		<table cellspacing="0" width="680" class="rpt_table" border="0" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="250">Yarn Desc</th>
					<th width="100">Yarn Qty</th>
					<th width="100">Rate</th>
					<th width="100">Tot.Amount</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i = 1;
				$TotalQty = 0;
				$TotalAmount = 0;
				foreach ($YarnData as $index => $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					$des = explode("_", str_replace("'", "", $index));
					$item_descrition = $lib_yarn_count[$des[0]] . " " . $composition[$des[1]] . " " . $des[2] . "% " . $color_library[$des[4]] . " " . $yarn_type[$des[3]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="fab_list_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td align="left"><? echo $item_descrition; ?></td>
						<td align="right"><? echo fn_number_format($row["qty"], 4); ?></td>
						<td align="right"><? echo fn_number_format($row["rate"], 4); ?></td>
						<td align="right"><? echo fn_number_format($row["amount"], 4); ?></td>
					</tr>
				<?

					$TotalQty += $row["qty"];
					$TotalAmount += $row["amount"];
					$GrandTotalFabricAmount += $row["amount"];
					$i++;
				}
				?>
			</tbody>
			<tfoot>
				<tr class="rpt_bottom" style="font-weight:bold">
					<th colspan=2 align="left">Total</th>
					<th align="right"><? echo fn_number_format($TotalQty, 4); ?></td>
					<th>&nbsp;</th>
					<th align="right"><? echo fn_number_format($TotalAmount, 4); ?></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
<?
	exit();
}

//actn_allocated_listview
if ($action == "actn_allocated_listview") {
	$expData = explode("_", $data);
	$sales_order_no = $expData[0];
	$company_id = $expData[1];
	$customer_id = $expData[2];
	$customer_buyer_id = $expData[3];
	$booking_no = $expData[4];
	$sales_order_id = $expData[5];

	$company_dtls = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$company_short_dtls	= return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
	$buyer_dtls = return_library_array("select id, short_name from  lib_buyer", 'id', 'short_name');
	$supplier_dtls = return_library_array("select id, short_name from  lib_supplier", 'id', 'short_name');

	//for product information
	$sql_product = "SELECT id AS ID, supplier_id AS SUPPLIER_ID, lot AS LOT, product_name_details AS PRODUCT_NAME,avg_rate_per_unit AS AVG_RATE_PER_UNIT, (current_stock-allocated_qnty) as AVAILABLE_QNTY FROM product_details_master WHERE id IN(SELECT item_id FROM inv_material_allocation_mst WHERE job_no = '" . $sales_order_no . "' AND item_category=1 AND status_active=1 AND is_deleted=0)";
	//echo $sql_product;
	$sql_product_rslt = sql_select($sql_product);
	$product_data_arr = array();
	foreach ($sql_product_rslt as $row) {
		$product_data_arr[$row['ID']]['supplier_id'] = $row['SUPPLIER_ID'];
		$product_data_arr[$row['ID']]['lot'] = $row['LOT'];
		$product_data_arr[$row['ID']]['product_name'] = $row['PRODUCT_NAME'];
		$product_data_arr[$row['ID']]['available_qnty'] = $row['AVAILABLE_QNTY'];
		$product_data_arr[$row['ID']]['avg_rate_per_unit'] = number_format($row['AVG_RATE_PER_UNIT'], 2);
	}
	unset($sql_product_rslt);

	$allocation_log_sql = "SELECT a.item_id AS ITEM_ID, SUM (a.qnty) AS QUANTITY, SUM (a.avg_usd_amount) AS AVG_USD_AMOUNT, (SUM (a.avg_usd_amount) / SUM (a.qnty)) AS AVG_USD_RATE, SUM (a.AVG_TK_AMOUNT) AS AVG_TK_AMOUNT, (SUM (a.avg_tk_amount) / SUM (a.qnty)) AS AVG_TK_RATE, a.exchange_rate as  EXCHANGE_RATE FROM INV_MAT_ALLOCATION_DTLS_LOG a WHERE  a.job_no = '" . $sales_order_no . "' AND a.item_category = 1 AND a.is_dyied_yarn<>1 AND a.is_sales = 1 AND a.status_active = 1 GROUP BY a.item_id,a.exchange_rate having SUM (a.qnty)>0";

	$allocation_log_sql_result = sql_select($allocation_log_sql);

	$allocation_log_data = array();
	foreach ($allocation_log_sql_result as $row) {
		$allocation_log_data[$row['ITEM_ID']]['quantity'] = $row['QUANTITY'];
		$allocation_log_data[$row['ITEM_ID']]['avg_usd_amount'] = $row['AVG_USD_AMOUNT'];
		$allocation_log_data[$row['ITEM_ID']]['avg_tk_amount'] = $row['AVG_TK_AMOUNT'];
		$allocation_log_data[$row['ITEM_ID']]['avg_usd_rate'] = $row['AVG_USD_RATE'];
		$allocation_log_data[$row['ITEM_ID']]['avg_tk_rate'] = $row['AVG_TK_RATE'];
		$allocation_log_data[$row['ITEM_ID']]['exchange_rate'] = $row['EXCHANGE_RATE'];
	}
	unset($allocation_log_sql_result);
	/* echo "<pre>";
	print_r($allocation_log_data);
	die; */

	$sql = "SELECT a.id AS ID, a.job_no AS SALES_ORDER_NO, a.po_break_down_id AS SALES_ORDER_ID, a.item_id AS ITEM_ID, a.qnty AS QTY, a.allocation_date AS ALLOCATION_DATE, a.is_dyied_yarn AS IS_DYIED_YARN, a.remarks as REMARKS FROM inv_material_allocation_mst a WHERE a.job_no = '" . $sales_order_no . "' AND a.item_category=1 AND a.status_active=1";
	// AND  entry_form=475
	//echo $sql;
	$sql_rslt = sql_select($sql);
	$sales_id_arr = array();
	foreach ($sql_rslt as $row) {
		$sales_id_arr[$row['SALES_ORDER_ID']] = $row['SALES_ORDER_ID'];
	}

	//for sales order infomation
	$sql_fab_sales = sql_select("SELECT id AS ID, within_group as WITHIN_GROUP FROM fabric_sales_order_mst WHERE id IN(" . implode(',', $sales_id_arr) . ")");
	$fab_sales_arr = array();
	foreach ($sql_fab_sales as $row) {
		$fab_sales_arr[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
	}
	//end for sales order infomation

	$data_arr = array();
	$prod_id_arr = array();
	foreach ($sql_rslt as $row) {
		$is_dyied_yarn = 0;
		if ($row['IS_DYIED_YARN'] == 1) {
			$is_dyied_yarn = 1;
		}

		$prod_id_arr[$row['ITEM_ID']] = $row['ITEM_ID'];

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['id'] = $row['ID'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['allocation_date'] = $row['ALLOCATION_DATE'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['qty'] = $row['QTY'];

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['company'] = $company_dtls[$company_id];

		//for buyer
		if ($fab_sales_arr[$row['SALES_ORDER_ID']]['within_group'] == 1) {
			$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['customer'] = $company_short_dtls[$customer_id];
		} else {
			$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['customer'] = $buyer_dtls[$customer_id];
		}

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['customer_buyer'] = $buyer_dtls[$customer_buyer_id];

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['supplier_id'] = $supplier_dtls[$product_data_arr[$row['ITEM_ID']]['supplier_id']];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['lot'] = $product_data_arr[$row['ITEM_ID']]['lot'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['product_id'] = $row['ITEM_ID'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['product_name'] = $product_data_arr[$row['ITEM_ID']]['product_name'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['available_qnty'] = $product_data_arr[$row['ITEM_ID']]['available_qnty'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['avg_usd_rate'] = $row['AVG_USD_RATE'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['remarks'] = $row['REMARKS'];
	}
	unset($sql_rslt);

	//for requisition and demand basis
	$issue_data = array();
	$check_trans_id = array();
	$sql_req = "SELECT a.status_active AS STATUS_ACTIVE, d.id as ISSUE_ID,d.issue_purpose AS ISSUE_PURPOSE,c.id AS ID, c.prod_id AS PROD_ID, c.cons_quantity AS QTY FROM ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b, inv_transaction c, order_wise_pro_details e, inv_issue_master d WHERE a.dtls_id = b.knit_id AND b.requisition_no = c.requisition_no AND c.mst_id=d.id  AND c.id=e.trans_id and a.po_id=e.po_breakdown_id and e.po_breakdown_id=$sales_order_id AND a.booking_no = '" . $booking_no . "' AND d.issue_basis in (3,8) AND c.prod_id IN(" . implode(',', $prod_id_arr) . ") AND c.transaction_type=2 AND c.item_category=1 AND c.status_active=1 AND c.is_deleted=0 AND e.status_active=1 AND e.is_deleted=0"; // AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 : omit cause : program can be delete even issue found
	//echo $sql_req;
	$sql_req_rslt = sql_select($sql_req);
	foreach ($sql_req_rslt as $row) {
		if ($check_trans_id[$row['ID']] != $row['ID']) {
			$check_trans_id[$row['ID']] = $row['ID'];
			$issue_data[$row['PROD_ID']]['issue_to_knitting_qty'] += $row['QTY'];

			if ($row['STATUS_ACTIVE'] == 0) {
				$issue_data[$row['PROD_ID']]['deleted_prog_yarn_issue_qty'] += $row['QTY'];
			}

			$issue_data[$row['PROD_ID']][$row['ISSUE_ID']] = $row['ISSUE_PURPOSE'];
			$issue_data[$row['PROD_ID']]['issue_id'][] = $row['ISSUE_ID'];
			$issue_id .= $row['ISSUE_ID'] . ",";
		}
	}

	//for booking basis
	$sql_issue = "SELECT a.id as ISSUE_ID, a.issue_purpose AS ISSUE_PURPOSE, b.id AS ID, b.prod_id AS PROD_ID, b.cons_quantity AS QTY FROM inv_issue_master a, inv_transaction b,order_wise_pro_details c WHERE a.id=b.mst_id AND b.id=c.trans_id and c.po_breakdown_id=$sales_order_id AND a.issue_basis in(1) AND b.job_no = '" . $sales_order_no . "' AND b.prod_id IN(" . implode(',', $prod_id_arr) . ") AND b.transaction_type=2 AND b.item_category=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0";
	//echo $sql_issue;
	$sql_issue_rslt = sql_select($sql_issue);
	$check_trans_id = array();
	foreach ($sql_issue_rslt as $row) {
		if ($check_trans_id[$row['ID']] != $row['ID']) {
			$check_trans_id[$row['ID']] = $row['ID'];
			$issue_data[$row['PROD_ID']]['issue_to_services_qty'] += $row['QTY'];
			$issue_data[$row['PROD_ID']][$row['ISSUE_ID']] = $row['ISSUE_PURPOSE'];
			$issue_data[$row['PROD_ID']]['issue_id'][] = $row['ISSUE_ID'];
			$issue_id .= $row['ISSUE_ID'] . ",";
		}
	}

	/* echo "<pre>";
	print_r($issue_data);
	echo "</pre>"; */

	$issue_id = substr($issue_id, 0, -1);
	$issue_id = implode(",", array_filter(array_unique(explode(",", $issue_id))));

	$sql_rtn = "select b.ID ,b.issue_id as ISSUE_ID,b.prod_id as PROD_ID, b.CONS_QUANTITY AS QTY from  inv_transaction b,order_wise_pro_details c where b.id=c.trans_id and c.po_breakdown_id=$sales_order_id and b.issue_id in ($issue_id) AND b.prod_id IN(" . implode(',', $prod_id_arr) . ") and b.transaction_type =4 and b.item_category=1 and b.status_active=1 AND b.is_deleted=0 and c.status_active=1 AND c.is_deleted=0";

	//echo $sql_rtn; die;

	$issue_rtn_rslt = sql_select($sql_rtn);
	$check_trans_id = array();
	foreach ($issue_rtn_rslt as $row) {

		if ($check_trans_id[$row['ID']] != $row['ID']) {
			$check_trans_id[$row['ID']] = $row['ID'];

			$issue_rtn_data[$row['PROD_ID']]['issue_rtn_qty'] += $row['QTY'];
			$issue_purpose = $issue_data[$row['PROD_ID']][$row['ISSUE_ID']];
			if ($issue_purpose == 1) {
				$issue_rtn_data[$row['PROD_ID']]['knitting_issue_rtn_qty'] += $row['QTY'];
			} else {
				$issue_rtn_data[$row['PROD_ID']]['services_issue_rtn_qty'] += $row['QTY'];
			}
		}
	}

	/* echo "<pre>";
	print_r($issue_rtn_data); */
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1770" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">SID</th>
			<th width="80">Company</th>
			<th width="80">Customer</th>
			<th width="80">Cust. Buyer</th>
			<th width="80">Supplier</th>
			<th width="70">Allocation Date</th>
			<th width="120">Sales Order No</th>
			<th width="200">Allocated Yarn</th>
			<th width="70">Lot</th>
			<th width="70">Product ID</th>
			<th width="70">UN-allocated Quantity</th>
			<th width="70">Quantity</th>
			<th width="70">Avg. Rate(USD)</th>
			<th width="70">Amount(USD)</th>
			<th width="70">Grey Issue to [Knitting]</th>
			<th width="70">Grey Issued to [Services]</th>
			<th width="70">Delete Prog. Yarn Issue</th>
			<th width="70">Grey Yarn Issue Return </th>
			<th width="70">Total Issue</th>
			<th>Remarks</th>
		</thead>
	</table>
	<div style="width:1770px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1750" class="rpt_table" id="grey_yarn_list">
			<?
			$total_available_qnty = $total_allocated_qnty = $total_allocated_amount = $total_issue_to_knitting_qnty = $total_issue_to_services_qnty = $deleted_prog_yarn_issue_qnty = $total_issue_rtn_qnty = $total_net_issue_qnty = 0;
			$i = 0;
			foreach ($data_arr[0] as $sales_order_no => $sales_order_no_arr) {
				foreach ($sales_order_no_arr as $product_id => $row) {
					$i++;
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$usdRate = $allocation_log_data[$row['product_id']]['avg_usd_rate'];
					$usdAmount = $allocation_log_data[$row['product_id']]['avg_usd_amount'];

					$issue_to_knitting_qty = $issue_data[$product_id]['issue_to_knitting_qty'];
					$issue_to_services_qty = $issue_data[$product_id]['issue_to_services_qty'];
					$issue_rtn_qty = $issue_rtn_data[$product_id]['issue_rtn_qty'];
					$deleted_prog_yarn_issue_qty = $issue_data[$product_id]['deleted_prog_yarn_issue_qty'];
					$net_issue_qty =  ($issue_to_knitting_qty + $issue_to_services_qty) - $issue_rtn_qty;
			?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row['id'] . '_' . $row['product_id']; ?>','actn_populate_data', 'requires/yarn_allocation_sales_controller');">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="50" align="center">
							<p><? echo $row['id']; ?></p>
						</td>
						<td width="80" align="center">
							<p><? echo $row['company']; ?></p>
						</td>
						<td width="80" align="center">
							<p><? echo $row['customer']; ?></p>
						</td>
						<td width="80" align="center">
							<p><? echo $row['customer_buyer']; ?></p>
						</td>
						<td width="80" align="center">
							<p><? echo $row['supplier_id']; ?></p>
						</td>
						<td width="70" align="center">
							<p><? echo change_date_format($row['allocation_date'], "dd-mm-yyyy", "-"); ?></p>
						</td>
						<td width="120" style="max-width: 120px;">
							<p><? echo $sales_order_no ?></p>
						</td>
						<td width="200" style="max-width: 200px;">
							<p><? echo $row['product_name']; ?></p>
						</td>
						<td width="70" align="center" title="<? echo $row['product_id']; ?>" style="word-break: break-all;"><? echo $row['lot']; ?>
							<input type="hidden" name="prod_id[]" value="<? echo $row['product_id']; ?>" />
						</td>
						<td width="70" align="center">
							<p><? echo $row['product_id']; ?></p>
						</td>
						<td width="70" align="right"><? echo number_format($row['available_qnty'], 2); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($row['qty'], 2); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($usdRate, 2); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($usdAmount, 4); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($issue_to_knitting_qty, 2); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($issue_to_services_qty, 2); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($deleted_prog_yarn_issue_qty, 2); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($issue_rtn_qty, 2); ?>&nbsp;</td>
						<td width="70" align="right"><? echo number_format($net_issue_qty, 2); ?>&nbsp;</td>
						<td>
							<p><? echo $row['remarks']; ?></p>
						</td>
					</tr>
			<?
					$total_available_qnty += $row['available_qnty'];
					$total_allocated_qnty += $row['qty'];
					$total_allocated_amount += $usdAmount;
					$total_issue_to_knitting_qnty += $issue_to_knitting_qty;
					$total_issue_to_services_qnty += $issue_to_services_qty;
					$deleted_prog_yarn_issue_qnty += $deleted_prog_yarn_issue_qty;
					$total_issue_rtn_qnty += $issue_rtn_qty;
					$total_net_issue_qnty += $net_issue_qty;
				}
			}
			?>
			<tr style="background-color:#CCCCCC;">
				<td colspan="11" align="right"><strong>Total</strong></td>
				<td align="right"><strong><? echo number_format($total_available_qnty, 2); ?> </strong></td>
				<td align="right"><strong><? echo number_format($total_allocated_qnty, 2); ?> </strong></td>
				<td>&nbsp;</td>
				<td align="right"><strong><? echo number_format($total_allocated_amount, 4); ?></strong></td>
				<td align="right"><strong><? echo number_format($total_issue_to_knitting_qnty, 2); ?> </strong></td>
				<td align="right"><strong><? echo number_format($total_issue_to_services_qnty, 2); ?> </strong></td>
				<td align="right"><strong><? echo number_format($deleted_prog_yarn_issue_qnty, 2); ?> </strong></td>
				<td align="right"><strong><? echo number_format($total_issue_rtn_qnty, 2); ?> </strong></td>
				<td align="right"><strong><? echo number_format($total_net_issue_qnty, 2); ?> </strong></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	<fieldset style="width:1350px; margin-top:10px; float:left">
		<legend>Dyed Yarn List</legend>
		<div id="item_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" id="dyied_yarn_list">
				<thead>
					<th width="30">SL</th>
					<th width="50">SID</th>
					<th width="80">Company</th>
					<th width="80">Customer</th>
					<th width="80">Cust. Buyer</th>
					<th width="80">Supplier</th>
					<th width="200">Allocated Yarn</th>
					<th width="70">Lot</th>
					<th width="70">Product ID</th>
					<th width="70">UN-Allocated Quantity</th>
					<th width="70">Quantity</th>
					<th width="70">Knitting Issued</th>
					<th width="70">Knitting Issued Return</th>
					<th width="70">Delete Prog. Yarn Issue</th>
					<th width="70">Total Issue</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?php
					$total_dyed_available_qnty = $total_dyed_allocated_qnty = $total_dyed_issue_to_knitting_qnty = $total_dyed_knitting_issue_rtn_qnty = $total_dyed_deleted_prog_yarn_issue_qty = $total_dyed_net_issue_qty = 0;
					$i = 0;
					foreach ($data_arr[1] as $sales_order_no => $sales_order_no_arr) {
						foreach ($sales_order_no_arr as $product_id => $row) {
							$i++;
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$issue_to_knitting_qty = $issue_data[$product_id]['issue_to_knitting_qty'];
							$deleted_prog_yarn_issue_qty = $issue_data[$product_id]['deleted_prog_yarn_issue_qty'];
							$knitting_issue_rtn_qty = $issue_rtn_data[$product_id]['knitting_issue_rtn_qty'];

							$issue_rtn_qty = $issue_rtn_data[$product_id]['issue_rtn_qty'];
							$net_issue_qty =  ($issue_to_knitting_qty - $knitting_issue_rtn_qty);
					?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row['id'] . '_' . $row['product_id']; ?>','actn_populate_data', 'requires/yarn_allocation_sales_controller');">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="50" align="center">
									<p><? echo $row['id']; ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $row['company']; ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $row['customer']; ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $row['customer_buyer']; ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $row['supplier_id']; ?></p>
								</td>
								<td width="200" style="max-width: 200px;">
									<p><? echo $row['product_name']; ?></p>
								</td>
								<td width="70" align="center" title="<? echo $row['product_id']; ?>" style="word-break: break-all;">
									<? echo $row['lot']; ?>
									<input type="hidden" name="prod_id[]" value="<? echo $row['product_id']; ?>" />
								</td>
								<td width="50" align="center">
									<p><? echo $row['product_id']; ?></p>
								</td>
								<td width="70" align="right"><? echo number_format($row['available_qnty'], 2); ?>&nbsp;</td>
								<td width="70" align="right"><? echo number_format($row['qty'], 2); ?>&nbsp;</td>
								<td width="70" align="right"><? echo number_format($issue_to_knitting_qty, 2); ?>&nbsp;</td>
								<td width="70" align="right"><? echo number_format($knitting_issue_rtn_qty, 2); ?>&nbsp;</td>
								<td width="70" align="right"><? echo number_format($deleted_prog_yarn_issue_qty, 2); ?>&nbsp;</td>
								<td width="70" align="right"><? echo number_format($net_issue_qty, 2); ?>&nbsp;</td>

								<td>
									<p><? echo $row['remarks']; ?></p>
								</td>
							</tr>
					<?php
							$total_dyed_available_qnty += $row['available_qnty'];
							$total_dyed_allocated_qnty += $row['qty'];
							$total_dyed_issue_to_knitting_qnty += $issue_to_knitting_qty;
							$total_dyed_knitting_issue_rtn_qnty += $knitting_issue_rtn_qty;
							$total_dyed_deleted_prog_yarn_issue_qty += $deleted_prog_yarn_issue_qty;
							$total_dyed_net_issue_qty += $net_issue_qty;
						}
					}
					?>
					<tr style="background-color:#CCCCCC;">
						<td colspan="9" align="right"><strong>Total</strong></td>
						<td align="right"><strong><? echo number_format($total_dyed_available_qnty, 2); ?> </strong></td>
						<td align="right"><strong><? echo number_format($total_dyed_allocated_qnty, 2); ?> </strong></td>
						<td align="right"><strong><? echo number_format($total_dyed_issue_to_knitting_qnty, 2); ?> </strong></td>
						<td align="right"><strong><? echo number_format($total_dyed_knitting_issue_rtn_qnty, 2); ?> </strong></td>
						<td align="right"><strong><? echo number_format($total_dyed_deleted_prog_yarn_issue_qty, 2); ?> </strong></td>
						<td align="right"><strong><? echo number_format($total_dyed_net_issue_qty, 2); ?> </strong></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

//actn_item_popup
if ($action == "actn_item_popup") {
	echo load_html_head_contents("Alocated Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$company_id = str_replace("'", "", $cbo_company_name);
	$item_category = str_replace("'", "", $cbo_item_category);
	$job_no = str_replace("'", "", $job_no);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);

	/*
	| for variable settings Inventory
	| if variable list is allocated quantity and
	| item category is yarn and
	| sales allocation is yes then
	| item popup will be generate otherwise
	| system will give a msg and execution will be stop
	*/

	$sql = "SELECT sales_allocation as SALES_ALLOCATION FROM variable_settings_inventory WHERE variable_list = 18 AND item_category_id = 1 AND status_active = 1 AND is_deleted = 0 AND company_name = " . $company_id . "";
	$sql_rslt = sql_select($sql);
	$is_sales_allocation = 2;
	foreach ($sql_rslt as $row) {
		$is_sales_allocation = $row['SALES_ALLOCATION'];
	}

?>
	<script>
		function js_set_value(str, selectable, job_no, ratep, vs_yrn_mandatory, yrn_tested, tr, vs_yrn_approve_mandatory, yrntest_is_approved, composition_id) {
			//for yarn test
			if (vs_yrn_mandatory == 1) {
				//alert(yrn_tested);	
				if (yrn_tested == 0) {
					alert('Yarn Test Required');
					return;
				}
			}
			//end for yarn test		

			//for yarn test approval
			if (vs_yrn_approve_mandatory == 1) {
				if (yrntest_is_approved == 0) {
					alert('Yarn Test Approved Required');
					return;
				}
			}
			//end for yarn test approval
			var str_array = str.split("_");
			if (selectable == 0) {
				var booking_no = '<? echo $hdn_booking_no; ?>';
				var buyer = '<? echo $customer_buyer_id; ?>';
				var job_no = '<? echo $hdn_po_job_no; ?>';

				var yarn_rate = ratep;
				var page_link = 'yarn_allocation_sales_controller.php?action=budget_yarn_comparision_popup&job_no=' + job_no + '&booking_no=' + booking_no + '&buyer=' + buyer + '&prod_id=' + str_array[0] + '&yarn_rate=' + yarn_rate;

				var title = "Yarn Info Budget VS Actual";
				emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=250px,center=1,resize=0,scrolling=0', '');
				return;

				alert("Yarn Count, Type and rate not match with Budget");
				return;
			}

			var required_compositions = '<? echo $required_compositions; ?>';
			required_compositions_arr = required_compositions.split(',');

			if ((required_compositions != 'undefined') && (jQuery.inArray(composition_id, required_compositions_arr) == -1)) {
				var r = confirm("You Selected Different Yarn Composition. Are you sure?");
				if (r == 1) {
					$('#product_id').val(str_array[0]);
					$('#product_name').val(str_array[1]);
					$('#available_qnty').val(str_array[2]);
					//$('#unit_of_measurment').val(str_array[3]);
					$('#dyed_type').val(str_array[4]);
					parent.emailwindow.hide();
				} else {
					// Nothing happend
				}
			} else {
				$('#product_id').val(str_array[0]);
				$('#product_name').val(str_array[1]);
				$('#available_qnty').val(str_array[2]);
				//$('#unit_of_measurment').val(str_array[3]);
				$('#dyed_type').val(str_array[4]);
				parent.emailwindow.hide();
			}

		}

		function store_pop(e) {
			var prod_id = $(e.target).closest('td').find(':input').val();
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', "yarn_allocation_sales_controller.php?action=storeStockPopup&prod_id=" + prod_id, "Store Wise Stock", 'width=380px,height=300px,center=1,resize=0,scrolling=0', '../../')
			e.stopPropagation();
		}

		function show_test_report(e) {
			var data = $(e.target).closest('td').find(':input').val();
			window.open("../../reports/yarn/requires/daily_yarn_stock_report_controller.php?data=" + data + '&action=yarn_test_report', true);
			e.stopPropagation();
		}

		function show_test_report2(e) {
			var data = $(e.target).closest('td').find(':input').val();
			window.open("../../reports/yarn/requires/daily_yarn_stock_report_controller.php?data=" + data + '&action=yarn_test_report2', true);
			e.stopPropagation();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
					<thead>
						<th width="150" colspan="2"> </th>
						<th>
							<?
							echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
							?>
						</th>
						<th width="150" colspan="3"> </th>
					</thead>
					<thead>
						<th>Supplier Name</th>
						<th>Lot</th>
						<th>Yarn Count</th>
						<th>Yarn Type</th>
						<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						</th>
					</thead>
					<?php
					if ($is_sales_allocation != 1) {
					?>

						<tr>
							<td colspan="5" align="center" style="color:#FF0000;"><strong>Not allowed to allocate yarn according to variable settings.</strong></td>
						</tr>
					<?php
						die;
					}
					?>
					<tr class="general">
						<td>
							<input type="hidden" name="company_id" id="company_id" value="<? echo $company_id ?>">
							<input type="hidden" name="category_id" id="category_id" value="<? echo $item_category ?>">
							<input type="hidden" name="job_no" id="job_no" value="<? echo $job_no ?>">
							<input type="hidden" name="txt_booking_no" id="txt_booking_no" value="<? echo $txt_booking_no ?>">
							<input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name ?>">
							<?
							echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=" . $company_id . " and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
							?>
						</td>
						<td><input type="text" style="width:100px" class="text_boxes" name="txt_lot" id="txt_lot" /></td>
						<td>
							<?
							echo create_drop_down("cbo_yarn_count", 100, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "--Select--", 0, "", 0);
							?>
						</td>
						<td>
							<?
							asort($yarn_type);
							echo create_drop_down("cbo_yarn_type", 100, $yarn_type, "", 1, "--Select--", 0, "", 0);
							?>
						</td>

						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('txt_lot').value+'_'+document.getElementById('company_id').value+'_'+document.getElementById('category_id').value+'_'+document.getElementById('cbo_yarn_count').value+'_'+document.getElementById('cbo_yarn_type').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $hdn_booking_no; ?>'+'_'+'<? echo $hdn_po_job_no; ?>'+'_'+'<? echo $customer_buyer_id; ?>', 'actn_item_listview', 'search_div', 'yarn_allocation_sales_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
				</table>

				<div id="search_div" style="margin-top:5px"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

//actn_item_listview
if ($action == "actn_item_listview") {
	$data = explode('_', $data);
	$supplier = $data[0];
	$lots = $data[1];
	$company = $data[2];
	$category = $data[3];
	$cbo_count = $data[4];
	$cbo_type = $data[5];
	$job_no = $data[6];
	$search_cat = $data[7];

	$hdn_booking_no = $data[8];
	$hdn_po_job_no = $data[9];
	$customer_buyer_id = $data[10];

	//var_dump($search_cat);
	//for supplier
	$supplier_cond = '';
	if ($supplier != 0) {
		$supplier_cond = " and supplier_id = " . $supplier . "";
	}

	//for lot
	$lot_cond = '';
	/* if ($lots != '')
	{
		
		$lot_cond = "and lot = '".$lots."'";
	} */
	if ($search_cat == 1) {
		if (trim($lots) != "") $lot_cond = " and lot like '$lots'";
	}
	if ($search_cat == 2) {
		if (trim($lots) != "") $lot_cond = " and lot like '$lots%'";
	}
	if ($search_cat == 3) {
		if (trim($lots) != "") $lot_cond = " and lot like '%$lots'";
	}
	if ($search_cat == 4 || $search_cat == 0) {
		if (trim($lots) != "") $lot_cond = " and lot like '%$lots%'";
	}

	//for yarn count
	$count_cond = '';
	if ($cbo_count != 0) {
		$count_cond = " and yarn_count_id = '" . $cbo_count . "'";
	}

	//for yarn type
	$type_cond = '';
	if ($cbo_type != 0) {
		$type_cond = " and yarn_type = '" . $cbo_type . "'";
	}

	//main query
	$sql = "select id, company_id, item_category_id, supplier_id, lot, product_name_details, current_stock, allocated_qnty, available_qnty, unit_of_measure, yarn_comp_percent1st, yarn_comp_type1st, color, yarn_count_id, yarn_type, avg_rate_per_unit, dyed_type from product_details_master where company_id = " . $company . " and item_category_id=1 and current_stock > 0 and status_active=1 and is_deleted=0 $lot_cond $supplier_cond $count_cond $type_cond";
	//echo $sql;
	$DataArray = sql_select($sql);

	//for transaction date
	if ($db_type == 0)
		$date_cond = " and transaction_date != '0000-00-00'";
	else
		$date_cond = " and transaction_date is not null";

	$transaction_date_arr = array();
	$sql_transaction = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where item_category=1 " . $date_cond . " and prod_id in(select id from product_details_master where company_id = " . $company . " and item_category_id=1 and current_stock > 0 and status_active=1 and is_deleted=0 $lot_cond $supplier_cond $count_cond $type_cond) group by prod_id";
	$sql_transaction_rslt = sql_select($sql_transaction);
	foreach ($sql_transaction_rslt as $row_d) {
		$transaction_date_arr[$row_d[csf('prod_id')]]['min_date'] = $row_d[csf('min_date')];
		$transaction_date_arr[$row_d[csf('prod_id')]]['max_date'] = $row_d[csf('max_date')];
	}
	//end transaction date

	$during_issue = return_field_value("during_issue", "variable_settings_inventory", "company_name=$company  and variable_list=25 and status_active=1 and is_deleted=0");

	$varialble_setting = sql_select("select during_issue,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name=" . $company . "  and variable_list=25 and status_active=1 and is_deleted=0");

	$is_control = $varialble_setting[0][csf('during_issue')];
	$control_level = $varialble_setting[0][csf('user_given_code_status')];
	$tolerant_percent = $varialble_setting[0][csf('tolerant_percent')];

	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$supplier = return_library_array("select id, supplier_name from  lib_supplier", 'id', 'supplier_name');
	$comments_acceptance_arr = array(1 => 'Acceptable', 2 => 'Special', 3 => 'Consideration', 4 => 'Not Acceptable');

	$con = connect();
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = " . $user_id);
	oci_commit($con);

	//for product id
	$con = connect();
	$prod_arr = array();
	foreach ($DataArray as $row) {
		$prod_arr[$row[csf('id')]] = $row[csf('id')];
	}
	foreach ($prod_arr as $key => $val) {
		execute_query("INSERT INTO TMP_PROD_ID(PROD_ID, USERID) VALUES('" . $val . "', '" . $user_id . "')");
	}
	oci_commit($con);

	//yarn test
	$yrn_arr = array();
	$yrn_arr['company_id'] = $company;
	$yrn_arr['variable_list'] = 36; // Yarn Test Mandatory For Allocation
	$vs_test = get_vs_yarn_test_mandatory($yrn_arr);

	$yrn_arr['variable_list'] = 37; // Yarn Test Approval Mandatory For Allocation
	$vs_test_approval = get_vs_yarn_test_mandatory($yrn_arr);

	$sql_yrn_test = "select a.id, c.comments_knit_acceptance, b.yarn_quality_coments, b.lot_number from product_details_master a, inv_yarn_test_mst b, inv_yarn_test_comments c, tmp_prod_id d where a.id = b.prod_id and a.company_id = b.company_id and b.id = c.mst_table_id and a.id = d.prod_id and b.prod_id = d.prod_id and a.company_id = " . $company . " and a.item_category_id = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.userid = " . $user_id;
	//echo $sql_yrn_test;
	$sql_yrn_test_rslt = sql_select($sql_yrn_test);
	$yrn_test_data = array();
	foreach ($sql_yrn_test_rslt as $row) {
		$yrn_test_data[$row[csf('id')]] = $row[csf('id')];
		$yrn_test_data[$row[csf('id')]]['comments_knit_acceptance'] = $row[csf('comments_knit_acceptance')];
		$yrn_test_data[$row[csf('id')]]['yarn_quality_coments'] = $row[csf('yarn_quality_coments')];
		$yrn_test_data[$row[csf('id')]]['lot_number'] = $row[csf('lot_number')];
	}

	$sql_yrn_test_approved = "select a.id, b.approved from product_details_master a, inv_yarn_test_mst b, tmp_prod_id c where a.id = b.prod_id and a.company_id = b.company_id and a.id = c.prod_id and b.prod_id = c.prod_id and a.company_id = " . $company . " and a.item_category_id = 1 and a.current_stock > 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and c.userid = " . $user_id;
	//echo $sql_yrn_test_approved;die;
	$sql_yrn_test_approved_rslt = sql_select($sql_yrn_test_approved);
	$yrn_test_approve_data = array();
	foreach ($sql_yrn_test_approved_rslt as $row) {
		$yrn_test_approve_data[$row[csf('id')]][] = $row[csf('approved')];
	}

?>

	<body>
		<div style=" width:100%;">
			<input type="hidden" id="product_id" />
			<input type="hidden" id="dyed_type" />
			<input type="hidden" name="product_name" id="product_name" value="" />
			<input type="hidden" name="available_qnty" id="available_qnty" value="" />
			<input type="hidden" name="unit_of_measurment" id="unit_of_measurment" value="" />
			<table cellspacing="0" width="100%" class="rpt_table" border="0" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="70">Company</th>
						<th width="130">Supplier</th>
						<th width="70">Lot</th>
						<th width="60">Count</th>
						<th width="150">Composition</th>
						<th width="80">Type</th>
						<th width="80">Color</th>
						<th width="80">Current Stock</th>
						<th width="80">Allocated Qnty</th>
						<th width="80">Unallocated Qnty</th>
						<th width="60">Age (Days)</th>
						<th width="60">Rate USD</th>
						<th width="60">DOH</th>
						<th width="100">Yarn Quality Comment</th>
					</tr>
				</thead>
			</table>
			<div style="width:1210px;max-height:300px; overflow-y:scroll;">
				<table id="list_view" cellspacing="0" width="100%" class="rpt_table" border="0" rules="all">
					<tbody>
						<?
						$currency_conversion = sql_select("select id, conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and company_id = $company order by id desc");

						$lib_conversion_rate = $currency_conversion[0][csf("conversion_rate")];
						$countTypeArr = array();
						$sqlY = sql_select("select id,count_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$hdn_po_job_no' and status_active=1 and is_deleted=0");
						foreach ($sqlY as $sqlY_row) {
							$budget_rate = number_format($sqlY_row[csf('rate')], 4, ".", "");
							$countTypeArr[$sqlY_row[csf('count_id')]][$sqlY_row[csf('type_id')]][$budget_rate] = 1;
						}

						/*echo "<pre>";
                    print_r($DataArray);
                    echo "</pre>";*/

						$colorArr = return_library_array("select id, color_name from lib_color", "id", "color_name");
						$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
						$i = 1;
						foreach ($DataArray as $row) {
							$bgcolor = "#FFFFFF";
							$is_tested = 1;
							if (empty($yrn_test_data[$row[csf('id')]])) {
								$is_tested = 0;
								$bgcolor = "#CCCCCC";
							}
							//end for yarn test

							// yarn test approved
							if (!empty($yrn_test_approve_data[$row[csf('id')]])) {
								$approve_status = max($yrn_test_approve_data[$row[csf('id')]]);
							}
							$is_approved = ($approve_status == 1) ? 1 : 0;
							// end 

							if ($txt_item_id == $row[csf("id")]) {
								$bgcolor = "#FFFF66";
							}

							if ($is_tested == 1) {
								$bgcolor = "#add8e6";
								$title = "Tested Lot";
							} else if ($is_approved == 1) {
								$bgcolor = "#00FF00";
								$title = "Test Approved Lot";
							} else {
								$bgcolor = $bgcolor;
								$title = "";
							}

							$ratep = number_format($row[csf("avg_rate_per_unit")] / $lib_conversion_rate, 4);

							$sqlY = $countTypeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$ratep];

							//echo $sqlY."==".$row[csf("yarn_count_id")]."==".$row[csf("yarn_type")]."==".$ratep;

							if (count($sqlY) == 0) {
								$match = 0;
							} else {
								$match = 1;
							}

							$selectable = 1;

							if ($is_control == 1 && $control_level == 1 && !$match) // rate level
							{
								$selectable = 0;
							} else {
								$selectable = 1;
							}

							$yarn_test = $comments_acceptance_arr[$yrn_test_data[$row[csf('id')]]['comments_knit_acceptance']];
							$product_name_details = str_replace(array("\r", "\n"), '', $row[csf("product_name_details")]);

							$yarn_composition_id = $row[csf('yarn_comp_type1st')];

						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("id")] . "_" . $product_name_details . "_" . $row[csf("available_qnty")] . "_" . $row[csf("unit_of_measure")] . "_" . $row[csf("dyed_type")]; ?>',<? echo $selectable; ?>,'<? echo $hdn_po_job_no; ?>','<? echo $ratep; ?>','<? echo $vs_test['is_mandatory']; ?>','<? echo $is_tested; ?>','<? echo $i; ?>','<? echo $vs_test_approval['is_mandatory']; ?>',<? echo $is_approved; ?>,'<? echo $yarn_composition_id; ?>')" style="cursor:pointer">
								<td width="40">
									<p><? echo $i; ?></p>
								</td>
								<td width="70">
									<p><? echo $comp[$row[csf("company_id")]]; ?></p>
								</td>
								<td width="130">
									<p><? echo $supplier[$row[csf("supplier_id")]]; ?></p>
								</td>
								<td width="70">
									<p><? echo $row[csf("lot")]; ?></p>
								</td>
								<td width="60">
									<p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
								</td>
								<td width="150" title="<? echo $row[csf('yarn_comp_type1st')]; ?>">
									<p><? echo $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% "; ?> </p>
								</td>
								<td width="80">
									<p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
								</td>
								<td width="80">
									<p><? echo $colorArr[$row[csf('color')]]; ?></p>
								</td>
								<td width="80" align="right" onClick="store_pop(event);" class='dataClass'>
									<p><a href="##"><? echo number_format($row[csf("current_stock")], 2); ?></a><input type="hidden" value='<? echo $row[csf("id")] ?>'></p>
								</td>
								<td width="80" align="right">
									<p><? echo number_format($row[csf("allocated_qnty")], 2); ?></p>
								</td>
								<td width="80" align="right">
									<p><? echo number_format($row[csf("available_qnty")], 2); ?></p>
								</td>
								<?
								$ageOfDays = datediff("d", $transaction_date_arr[$row[csf("id")]]['min_date'], date("Y-m-d"));
								$daysOnHand = datediff("d", $transaction_date_arr[$row[csf("id")]]['max_date'], date("Y-m-d"));
								?>
								<td width="60" align="right">
									<p><? echo $ageOfDays; ?></p>
								</td>
								<td width="60" align="right">
									<p><? echo $ratep; ?></p>
								</td>
								<td width="60" align="right">
									<p><? echo $daysOnHand; ?></p>
								</td>
								<?
								if ($yrn_test_data[$row[csf("id")]]['yarn_quality_coments'] != "") {
								?>
									<td width="100" onClick="show_test_report(event);" class='dataClass'>
										<p><a href='##'><? echo $yarn_test; ?></a><input type="hidden" value='<? echo $row[csf("company_id")] . '*' . $row[csf("id")] ?>'></p>
									</td>
								<?
								} else if ($yrn_test_data[$row[csf("id")]]['lot_number'] != "") {
								?>
									<td width="100" onClick="show_test_report2(event);" class='dataClass'>
										<p><a href='##'><? echo $yarn_test; ?></a><input type="hidden" value='<? echo $row[csf("company_id")] . '*' . $row[csf("id")] ?>'></p>
									</td>
								<?
								} else {
								?>
									<td width="100">
										<p><? echo $yarn_test; ?></p>
									</td>
								<?
								}
								?>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
	<script>
		setFilterGrid('list_view', -1)
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "budget_yarn_comparision_popup") {
	echo load_html_head_contents("PO Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	ob_start();
	$buyer_arr 			= return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$yarn_count_arr 	= return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$composition_arr 	= return_library_array("select id,composition_name from lib_composition_array", 'id', 'composition_name');
	$product_info 		= sql_select("select lot,yarn_comp_type1st,yarn_count_id,yarn_type from product_details_master where id=$prod_id");

	$sql_budge = sql_select("select id,count_id,copm_one_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and status_active=1");

	$count_arr = $type_arr = $rate_arr = array();
	foreach ($sql_budge as $row) {
		$count_arr[] = trim($row[csf("count_id")]);
		$type_arr[] = $row[csf("type_id")];
		$rate_arr[] = $row[csf("rate")];
	}
?>
	<div id="report_container">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="100%" class="rpt_table">
			<thead>
				<tr>
					<td width="60" style="font-weight: bold;">Buyer</td>
					<td width="130"><?php echo $buyer_arr[$buyer]; ?></td>
					<td width="70" style="font-weight: bold;">Job No</td>
					<td width="80"><?php echo $job_no; ?></td>
				</tr>
				<tr>
					<td style="font-weight: bold;">Booking No</td>
					<td><?php echo $booking_no; ?></td>
					<td style="font-weight: bold;">Lot</td>
					<td><?php echo $product_info[0][csf("lot")]; ?></td>
				</tr>
				<tr>
					<th colspan="4" align="center">Yarn info as budget</th>
				</tr>
				<tr>
					<th>Count</th>
					<th>Composition</th>
					<th>Type</th>
					<th>Rate (USD)</th>
				</tr>
				<?php
				foreach ($sql_budge as $row) {
				?>
					<tr>
						<td><?php echo $yarn_count_arr[$row[csf("count_id")]]; ?></td>
						<td><?php echo $composition_arr[$row[csf("copm_one_id")]]; ?></td>
						<td><?php echo $yarn_type[$row[csf("type_id")]]; ?></td>
						<td align="right"><?php echo $row[csf("rate")]; ?></td>
					</tr>
				<?
				}
				?>
				<tr>
					<th colspan="4" align="center">Yarn info as Allocation</th>
				</tr>
				<tr>
					<?php
					$count_bg = (!in_array($product_info[0][csf("yarn_count_id")], $count_arr)) ? "background-color:red;color:#fff;" : "";
					$type_bg = (!in_array($product_info[0][csf("yarn_type")], $type_arr)) ? "background-color:red;color:#fff;" : "";
					$rate_bg = (!in_array($yarn_rate, $rate_arr)) ? "background-color:red;color:#fff;" : "";
					$budget_bg = ($yarn_rate > $budge_data[$product_info[0][csf("yarn_count_id")]][$product_info[0][csf("yarn_type")]]["rate"]) ? "background-color:red;color:white;" : "";
					?>
					<td style="<?php //echo $count_bg;
								?>"><?php echo $yarn_count_arr[$product_info[0][csf("yarn_count_id")]]; ?></td>
					<td><?php echo $composition_arr[$product_info[0][csf("yarn_comp_type1st")]]; ?></td>
					<td style="<?php //echo $type_bg;
								?>"><?php echo $yarn_type[$product_info[0][csf("yarn_type")]]; ?></td>
					<td style="<?php //echo $rate_bg;
								?>" align="right"><?php echo $yarn_rate; ?></td>
				</tr>
			</thead>
		</table>
		<h3 style="color: red;text-align: center;">Selected yarn does not match with budget</h3>
		<input type="button" value="Export To Excel" name="excel" id="excel" class="formbutton" style="width:155px; margin-left: 150px;" />
	</div>
	<?
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = $user_name . "_" . $name . ".xls";
	?>
	<script type="text/javascript">
		$("#excel").click(function(e) {
			window.open("<? echo $filename; ?>", +$('#report_container').html());
			e.preventDefault();
		});
	</script>
<?
	die;
}

//for storeStockPopup
if ($action == "storeStockPopup") {
	extract($_REQUEST);
	$sql = "select  a.store_id, a.transaction_type, a.cons_quantity, b.product_name_details, b.lot,a.cons_uom, b.id, c.store_name from inv_transaction a, product_details_master b, lib_store_location c where a.prod_id = b.id and a.store_id = c.id and prod_id = $prod_id and a.status_active = 1 and b.status_active = 1";
	$result = sql_select($sql);
	$store_data = array();
	foreach ($result as $value) {
		if ($value[csf("transaction_type")] == 1 || $value[csf("transaction_type")] == 4 || $value[csf("transaction_type")] == 5) {
			$store_data[$value[csf("store_name")]]["qnty"] += $value[csf("cons_quantity")];
		} else {
			$store_data[$value[csf("store_name")]]["qnty"] -= $value[csf("cons_quantity")];
		}
		$product_details_arr[$value[csf("id")]]["product_name"] = $value[csf("product_name_details")];
		$product_details_arr[$value[csf("id")]]["uom"] = $unit_of_measurement[$value[csf("cons_uom")]];
		$product_details_arr[$value[csf("id")]]["lot"] = $value[csf("lot")];
	}
?>
	<span><? echo "Product ID : $prod_id, " . $product_details_arr[$prod_id]["product_name"] . ", Lot#" . $product_details_arr[$prod_id]["lot"] . ", UOM#" . $product_details_arr[$prod_id]["uom"]; ?></span>
	<table width="340" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
		<thead>
			<tr>
				<th width="40">Sl</th>
				<th width="150">Store Name</th>
				<th>Store Qnty</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			foreach ($store_data as $key => $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $i ?></td>
					<td><? echo $key ?></td>
					<td align="right"><? echo number_format($row["qnty"], 2) ?></td>
				</tr>
			<?
				$total += $row["qnty"];
				$i++;
			}
			?>
		</tbody>
		<tfoot>
			<tr style="background-color:#CCCCCC;">
				<td colspan="2" align="right">Total</td>
				<td align="right"><? echo number_format($total, 2) ?></td>
			</tr>
		</tfoot>
	</table>
<?
}

//actn_populate_data
if ($action == "actn_populate_data") {
	$data = explode("_", $data);
	$allocation_id = $data[0];
	$product_id = $data[1];

	$sql = "SELECT a.item_id AS PRODUCT_ID, a.booking_no AS BOOKING_NO,a.job_no AS JOB_NO, a.allocation_date AS ALLOCATION_DATE, a.qnty AS QNTY, a.REMARKS, b.allocated_qnty AS ALLOCATED_QNTY, b.available_qnty AS AVAILABLE_QNTY, product_name_details AS PRODUCT_NAME FROM inv_material_allocation_mst a, product_details_master b WHERE a.item_id = b.id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.id = " . $allocation_id . "";
	//echo $sql; die;
	$sql_rslt = sql_select($sql);
	$bkn_no = '';
	foreach ($sql_rslt as $rw) {
		$bkn_no = $rw['BOOKING_NO'];
		$job_no = $rw['JOB_NO'];
	}

	//if requisition found then Allocated Yarn field will be disabled
	$sql_req = sql_select("SELECT B.BOOKING_NO FROM PPL_YARN_REQUISITION_ENTRY A, PPL_PLANNING_ENTRY_PLAN_DTLS B WHERE A.KNIT_ID = B.DTLS_ID AND A.PROD_ID = " . $product_id . " AND B.BOOKING_NO = '" . $bkn_no . "' AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0");
	$is_disabled = 0;

	//if wo found then Allocated Yarn field will be disabled
	$sql_wo = sql_select("SELECT A.BOOKING_NO FROM WO_YARN_DYEING_DTLS A WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND A.PRODUCT_ID = " . $product_id . " AND A.BOOKING_NO = '" . $bkn_no . "' AND A.JOB_NO = '" . $job_no . "' ");

	if (!empty($sql_req) || !empty($sql_wo)) {
		$is_disabled = 1;
	}
	//end

	foreach ($sql_rslt as $row) {
		$product_name_details = str_replace(array("\r", "\n"), '', $row['PRODUCT_NAME']);
		echo "$('#txt_allocation_date').val('" . change_date_format($row['ALLOCATION_DATE'], "dd-mm-yyyy", "-") . "');\n";
		echo "$('#txt_item').val('" . $product_name_details . "');\n";
		echo "$('#txt_item_id').val('" . $row['PRODUCT_ID'] . "');\n";
		echo "$('#txt_item_id_old').val('" . $row['PRODUCT_ID'] . "');\n";
		echo "$('#txt_qnty').val('" . $row['QNTY'] . "');\n";
		echo "$('#txt_old_qnty').val('" . $row['QNTY'] . "');\n";
		echo "$('#available_qnty').val('" . $row['AVAILABLE_QNTY'] . "');\n";
		echo "$('#txt_remarks').val('" . $row['REMARKS'] . "');\n";
		echo "$('#update_id').val('" . $row['ID'] . "');\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'func_save_update_delete',1);\n";

		if ($is_disabled == 1) {
			echo "$('#txt_item').attr('disabled', 'disabled');\n";
		} else {
			echo "$('#txt_item').removeAttr('disabled');\n";
		}

		echo "$('#txt_item').attr('disabled', 'disabled');\n"; // temporary : lot replacement to another lot create history tbl bug 
	}
	exit();
}

//save_update_delete
if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$txt_qnty = str_replace("'", '', $txt_qnty) * 1;
	$txt_old_qnty = str_replace("'", '', $txt_old_qnty);
	$poJobNo = str_replace("'", "", $hdn_po_job_no);
	$txt_item_id = str_replace("'", "", $txt_item_id);

	/*
		grabe veriable setting for sales allocation
	*/
	$vs_sql = sql_select("SELECT sales_allocation as SALES_ALLOCATION FROM variable_settings_inventory WHERE variable_list = 18 AND item_category_id = 1 AND status_active = 1 AND is_deleted = 0 AND company_name = " . $cbo_company_name . "");
	$vs_sales_allocation = ($vs_sql[0]['SALES_ALLOCATION'] == 1) ? $vs_sql[0]['SALES_ALLOCATION'] : 2;

	if ($vs_sales_allocation != 1) {
		echo "3**Yarn allocation is not available.";
		die;
	}

	/*
	| catch avaialable qnty of this product
	*/
	$sql_prod = "SELECT current_stock AS CURRENT_STOCK,available_qnty AS AVAILABLE_QNTY, dyed_type AS DYED_TYPE,avg_rate_per_unit AS AVG_RATE_PER_UNIT FROM product_details_master WHERE id=" . $txt_item_id;
	$sql_prod_rslt = sql_select($sql_prod);
	foreach ($sql_prod_rslt as $row) {
		$check_prod_available_qnty = $row['AVAILABLE_QNTY'];
		$check_prod_stock_qnty = $row['CURRENT_STOCK'];
		$is_dyied_yarn = ($row['DYED_TYPE'] == 1 ? 1 : 0);
		$prod_avg_rate = $row['AVG_RATE_PER_UNIT'];
	}

	// 
	/** Allocation rate making */
	$currency_conversion = sql_select("select id, conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and company_id = $cbo_company_name order by id desc");
	$lib_conversion_rate = $currency_conversion[0][csf("conversion_rate")];
	$exchange_rate = return_field_value("exchange_rate", "wo_pre_cost_mst", "job_no='" . $poJobNo . "' and status_active=1 and is_deleted=0"); // from job budget
	$exchange_rate = ($exchange_rate > 0) ? $exchange_rate : $lib_conversion_rate;
	$allocation_usd_rate = number_format($prod_avg_rate / $exchange_rate, 2);

	// End allocation rate making

	/*
	|------------------------------------------------------------------------------------------------------
	| Allocation qty validate with budget required qty according with vs setting
	| VS name : Yarn item and rate matching with budget 
	| Created by : Didar
	|------------------------------------------------------------------------------------------------------
	|
	*/
	$varialble_setting = sql_select("select during_issue,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name=" . $cbo_company_name . "  and variable_list=25 and status_active=1 and is_deleted=0");

	$is_control = $varialble_setting[0][csf('during_issue')];
	$control_level = $varialble_setting[0][csf('user_given_code_status')];
	$tolerant_percent = $varialble_setting[0][csf('tolerant_percent')];

	$is_short = return_field_value("is_short", "wo_booking_mst", "company_id=$cbo_company_name and booking_no=$hdn_booking_no");
	$expBookinNo = explode("-", str_replace("'", "", $hdn_booking_no));

	if (($operation == 0 || $operation == 1) && $expBookinNo[1] != 'SMN' && $is_short != 1) {

		if ($is_control == 1 && $control_level == 3) // Quantity Level
		{
			$fso_sql = "SELECT sum(b.GREY_QTY) as sales_booking_qty FROM fabric_sales_order_mst a, fabric_sales_order_dtls b WHERE a.id=b.mst_id and a.sales_booking_no=$hdn_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$fso_result =  sql_select($fso_sql);
			$total_sales_booking_qty = number_format($fso_result[0][csf('sales_booking_qty')], 2, ".", "");

			if ($tolerant_percent > 0) // Budget value enhance till tolarent percent
			{
				$additional_value = number_format(($total_sales_booking_qty * $tolerant_percent / 100), 2, ".", "");
			}

			$allow_total_sales_booking_qty = ($total_sales_booking_qty + $additional_value);

			$alc_sql = "SELECT sum(b.QNTY) as allocation_booking_qty FROM inv_material_allocation_mst a, inv_material_allocation_dtls b WHERE a.id=b.mst_id and a.item_id=b.item_id and a.booking_no=$hdn_booking_no and b.is_dyied_yarn<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_sales=1";

			$alc_result =  sql_select($alc_sql);
			$allocation_booking_qty = number_format($alc_result[0][csf('allocation_booking_qty')], 2, ".", "");

			$txt_old_qnty = number_format(str_replace("'", "", $txt_old_qnty), 2, ".", "");
			$txt_qnty = number_format(str_replace("'", "", $txt_qnty), 2, ".", "");
			$total_booking_allocation_qty = ($allocation_booking_qty - $txt_old_qnty + $txt_qnty);
			//echo "10**".$total_booking_allocation_qty."=>".$total_sales_booking_qty."<".$allocation_booking_qty."-".$txt_old_qnty."+".$txt_qnty; die;
			//echo "10**".$allow_total_sales_booking_qty."<".$total_booking_allocation_qty; die;
			if ($allow_total_sales_booking_qty < $total_booking_allocation_qty) {
				$allow_balance_quantity = number_format((($total_sales_booking_qty + $additional_value) - $allocation_booking_qty), 2, ".", "");
				echo "3**Booking allocation quantity can not greater than sales booking grey quantity.\nSales booking quantity = $total_sales_booking_qty\nAdditional value=$additional_value\nPrevious Allocation quantity =$allocation_booking_qty\nCurrent allocation booking quantity=$total_booking_allocation_qty\nAllow balance quantity=$allow_balance_quantity";
				die;
			}
		} else if ($is_control == 1 && $control_level == 2) // Value Level
		{
			if ($poJobNo != "") // withing group yes
			{
				// *** Budget value Start**///

				$condition = new condition();
				if ($poJobNo != '') {
					$condition->job_no("='$poJobNo'");
				}

				$condition->init();
				$yarn = new yarn($condition);
				//echo $yarn->getQuery();
				//die;

				$totYarn = 0;
				$YarnData = array();
				$yarn_data_array = $yarn->get_By_Precostdtlsid_YarnQtyAmountArray();

				//print_r($yarn_data_array);
				$sql_yarn = "select f.id as yarn_id,f.job_no from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no ='" . $poJobNo . "' and f.is_deleted=0 and f.status_active=1  order by f.id";
				//echo "10**". $sql_yarn;die;
				$data_arr_yarn = sql_select($sql_yarn);
				foreach ($data_arr_yarn as $yarn_row) {
					$YarnData[$yarn_row[csf("job_no")]]['amount'] += $yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
				}
				/* echo "10**<pre>";
				print_r($YarnData) ; */

				$job_wise_total_budget_value_bdt = ($YarnData[$poJobNo]['amount'] * $exchange_rate);

				if ($tolerant_percent > 0) // Budget value enhance till tolarent percent
				{
					$additional_value_bdt = ($job_wise_total_budget_value_bdt * $tolerant_percent / 100);
				}

				$total_budget_with_additional_value_bdt = number_format(($job_wise_total_budget_value_bdt + $additional_value_bdt), 2, '.', '');
				$total_budget_with_additional_value_usd = number_format(($total_budget_with_additional_value_bdt / $exchange_rate), 2, '.', '');
				// *** Budget value End**///

				// *** Allocation value Start**///
				$logalc_sql = "SELECT SUM (a.qnty) AS QUANTITY, SUM (a.avg_usd_amount) AS AVG_USD_AMOUNT, (SUM (a.avg_usd_amount) / SUM (a.qnty)) AS AVG_USD_RATE, SUM (a.AVG_TK_AMOUNT) AS AVG_TK_AMOUNT, (SUM (a.avg_tk_amount) / SUM (a.qnty)) AS AVG_TK_RATE FROM INV_MAT_ALLOCATION_DTLS_LOG a WHERE  a.job_no = $txt_sales_order_no  AND a.item_category = 1 and a.is_sales = 1 AND a.is_dyied_yarn<>1 AND a.status_active = 1 having SUM (a.qnty)>0";
				$logalc_sql_result = sql_select($logalc_sql);
				//echo "10**" . $logalc_sql;die;
				$allocation_qnty = $logalc_sql_result[0]['QUANTITY'];
				$allocated_avg_usd_rate = $logalc_sql_result[0]['AVG_USD_RATE'];
				$allocated_avg_tk_rate = $logalc_sql_result[0]['AVG_TK_RATE'];
				$allocation_value_usd = number_format($logalc_sql_result[0]['AVG_USD_AMOUNT'], 2, ".", "");
				$allocation_value_bdt = number_format($logalc_sql_result[0]['AVG_TK_AMOUNT'], 2, ".", "");

				/* $alc_sql = "SELECT sum(b.qnty*c.avg_rate_per_unit) as allocation_value FROM inv_material_allocation_mst a, inv_material_allocation_dtls b,product_details_master c WHERE a.id=b.mst_id and a.item_id=b.item_id and b.item_id=c.id and a.booking_no=$hdn_booking_no and b.is_dyied_yarn<>1 and a.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				$alc_result =  sql_select($alc_sql);
				//echo $alc_sql; die;

				$allocation_value_bdt = number_format($alc_result[0][csf('allocation_value')], 2, ".", "");
				$allocation_value_usd = number_format(($alc_result[0][csf('allocation_value')] / $exchange_rate), 2, ".", ""); */

				$selected_prod_avg_rate = return_field_value("avg_rate_per_unit", "product_details_master", "id=$txt_item_id and status_active=1 and is_deleted=0");
				//echo "10**".$allocation_value; die;
				$txt_old_qnty = number_format(str_replace("'", "", $txt_old_qnty), 2, ".", "");
				$old_value_bdt = number_format(($selected_prod_avg_rate * $txt_old_qnty), 2, '.', '');
				$current_value_bdt = number_format(str_replace("'", "", $txt_qnty) * $selected_prod_avg_rate, 2, '.', '');

				if ($operation == 1) {
					$cumalative_allocated_value_bdt = $allocation_value_bdt - $old_value_bdt;
				} else {
					$cumalative_allocated_value_bdt = $allocation_value_bdt;
				}

				//echo "10".$cumalative_allocated_value_bdt; die;
				$cumalative_allow_value_bdt = number_format(($total_budget_with_additional_value_bdt - $cumalative_allocated_value_bdt - $current_value_bdt), 2, '.', '');

				if ($cumalative_allow_value_bdt < 0) {
					$balance_allocation_amount_usd = number_format(($total_budget_with_additional_value_usd - $allocation_value_usd), 2, '.', '');
					$balance_allocation_amount_bdt = number_format(($total_budget_with_additional_value_bdt - $allocation_value_bdt), 2, '.', '');

					echo "3**Booking allocation value can not be greater than budget value:\nEx. Rate (BOM)\t\t\t\t\t= $exchange_rate\nRequired BOM Amount (USD)\t\t= $total_budget_with_additional_value_usd\nCumulative Allocated Amount (USD)\t= $allocation_value_usd\nBalance Allocation Amount (USD)\t\t= $balance_allocation_amount_usd\n\nRequired BOM Amount (BDT)\t\t= $total_budget_with_additional_value_bdt\nCumulative Allocated Amount (BDT)\t= $allocation_value_bdt\nBalance Allocation Amount (BDT)\t\t= $balance_allocation_amount_bdt";
					die;
				}
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for insert
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation == 0) {
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		/*
		| Check allocation qnty whether grater than available qnty or not
		| Allocation qnty can't grater than available qnty
		*/

		if (str_replace("'", '', $txt_qnty) * 1 > $check_prod_available_qnty) {
			echo "5**Allocation quantity is not available\nAvailable quantity = " . number_format($check_prod_available_qnty, 2);
			die;
		}

		if (str_replace("'", '', $txt_qnty) * 1 > $check_prod_stock_qnty) {
			echo "5**Allocation quantity is not available\nAvailable quantity = " . number_format($check_prod_stock_qnty, 2);
			die;
		}

		if (str_replace("'", "", $txt_item_id) != "") {
			$check_trans_sql = "select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end) -(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$txt_item_id";
			//echo $trans_stock_check;
			$check_trans_sql_result = sql_select($check_trans_sql);
			$check_trans_stock_qty = $check_trans_sql_result[0][csf('bal_qnty')];

			if (str_replace("'", '', $txt_qnty) * 1 > $check_trans_stock_qty) {
				echo "5**Allocation quantity is not available\nAvailable quantity = " . number_format($check_trans_stock_qty, 2);
				die;
			}
		}

		$field_hystory_tbl = "id, mst_id, dtls_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, inserted_by, insert_date, company_id";
		/*
		| if same product and sales order no are already allocated then
		| data will be updabe otherwise
		| data will be insert 
		*/
		$sql_allocation = "SELECT a.id AS ID, b.id AS DTLS_ID, b.qnty AS QNTY FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $txt_sales_order_no . " AND a.item_id = " . $txt_item_id . " AND a.is_sales = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";

		//echo "10**" . $sql_allocation;
		//die;

		$sql_allocation_rslt = sql_select($sql_allocation);
		if (!empty($sql_allocation_rslt)) {
			foreach ($sql_allocation_rslt as $row) {
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing and updating
				|--------------------------------------------------------------------------
				|
				*/
				$mst_id = $row['ID'];
				$qry_mst_tbl = "UPDATE inv_material_allocation_mst SET allocation_date = " . $txt_allocation_date . ", qnty=(qnty+$txt_qnty), remarks = " . $txt_remarks . ", updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' WHERE id = " . $mst_id . "";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing and updating
				|--------------------------------------------------------------------------
				|
				*/
				$dtls_id = $row['DTLS_ID'];
				$qry_dtls_tbl = "UPDATE inv_material_allocation_dtls SET qnty = (qnty+$txt_qnty), updated_by = " . $_SESSION['logic_erp']['user_id'] . ", update_date = '" . $pc_date_time . "' WHERE id = " . $dtls_id . "";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_mst_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
				$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
				$log_qnty = $txt_qnty;
				$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $mst_id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_dtls_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
				$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales,avg_usd_rate,avg_usd_amount, exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

				$avg_usd_rate = $allocation_usd_rate;
				$avg_usd_amount = number_format(($log_qnty * $avg_usd_rate), 4, ".", "");
				$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
				$avg_tk_amount = number_format(($log_qnty * $avg_tk_rate), 4, ".", "");

				$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}

			$rslt_mst_tbl = execute_query($qry_mst_tbl, 0);
			$rslt_dtls_tbl = execute_query($qry_dtls_tbl, 0);
			$rslt_mst_log_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
			$rslt_dtls_log_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);
		} else {
			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_mst
			| data preparing and inserting
			|--------------------------------------------------------------------------
			|
			*/
			$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
			$field_mst_tbl = "id, entry_form, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
			$data_mst_tbl = "(" . $id . ",475," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			/*
			|--------------------------------------------------------------------------
			| inv_material_allocation_dtls
			| data preparing and inserting
			|--------------------------------------------------------------------------
			|
			*/
			$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
			$field_dtls_tbl = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";
			$data_dtls_tbl = "(" . $id1 . "," . $id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			/*
			|--------------------------------------------------------------------------
			| inv_mat_allocation_mst_log
			| data preparing and inserting
			|--------------------------------------------------------------------------
			|
			*/
			$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
			$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
			$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			/*
			|--------------------------------------------------------------------------
			| inv_mat_allocation_dtls_log
			| data preparing and inserting
			|--------------------------------------------------------------------------
			|
			*/
			$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
			$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, avg_usd_rate, avg_usd_amount,exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

			$avg_usd_rate = $allocation_usd_rate;
			$avg_usd_amount = number_format(($txt_qnty * $avg_usd_rate), 4, ".", "");
			$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
			$avg_tk_amount = number_format(($txt_qnty * $avg_tk_rate), 4, ".", "");

			$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$rslt_mst_tbl = sql_insert("inv_material_allocation_mst", $field_mst_tbl, $data_mst_tbl, 0);
			$rslt_dtls_tbl = sql_insert("inv_material_allocation_dtls", $field_dtls_tbl, $data_dtls_tbl, 0);
			$rslt_mst_log_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
			$rslt_dtls_log_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);

			//echo "10**INSERT INTO inv_mat_allocation_dtls_log (" . $field_dtls_log_tbl . ") VALUES " . $data_dtls_log_tbl . "";
			//die;
		}

		/*oci_rollback($con);
		echo "10**".$rslt_mst_tbl.'='.$rslt_dtls_tbl.'='.$rslt_product_tbl.'='.$rslt_mst_log_tbl.'='.$rslt_dtls_log_tbl;
		disconnect($con);
		die;*/

		/*
		| if inv_material_allocation_mst and inv_material_allocation_dtls table data transaction true then
		| product_details_master table allocated_qnty, available_qnty and update_date field will be update
		*/
		if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_mst_log_tbl && $rslt_dtls_log_tbl) {
			$rslt_product_tbl = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty+$txt_qnty), available_qnty = (current_stock-(allocated_qnty+$txt_qnty)), update_date = '" . $pc_date_time . "' WHERE id = " . $txt_item_id . "", 0);
		} else {
			$rslt_product_tbl = false;
		}

		/* echo  $rslt_mst_tbl . "&&" . $rslt_dtls_tbl . "&&" . $rslt_product_tbl . "&&" . $rslt_mst_log_tbl . "&&" . $rslt_dtls_log_tbl;
		oci_rollback($con);
		die; */

		if ($db_type == 0) {
			if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_mst_log_tbl && $rslt_dtls_log_tbl && $rslt_product_tbl) {
				mysql_query("COMMIT");
				echo "0**";
			} else {
				mysql_query("ROLLBACK");
				echo "10**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_product_tbl && $rslt_mst_log_tbl && $rslt_dtls_log_tbl) {
				oci_commit($con);
				echo "0**";
			} else {
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	/*
	|--------------------------------------------------------------------------
	| for update
	|--------------------------------------------------------------------------
	|
	*/ else if ($operation == 1) {
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$txt_item_id = str_replace("'", '', $txt_item_id) * 1;
		$txt_item_id_old = str_replace("'", '', $txt_item_id_old);

		/*
		|--------------------------------------------------------------------------
		| for allocation balance check
		|--------------------------------------------------------------------------
		|
		*/
		$job_no = str_replace("'", '', $txt_sales_order_no);
		$selected_booking_no = str_replace("'", '', $hdn_booking_no);
		$arr = array();
		$arr['product_id'] = $txt_item_id;
		$arr['is_auto_allocation'] = 0; // [ This page produce auto allocation off forcefully ];
		$arr['job_no'] = $job_no;
		$arr['booking_no'] = $selected_booking_no;
		$arr['po_id'] = str_replace("'", '', $hdn_sales_order_id);
		$allocation_arr = get_allocation_balance($arr);

		$booking_requisition_qty = $allocation_arr['booking_requisition'][$selected_booking_no][$txt_item_id]['qty'] * 1;
		$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$job_no][$txt_item_id]['qty'] * 1;
		$booking_allocation_qty = $allocation_arr['booking_allocation'][$selected_booking_no][$txt_item_id] * 1;
		/*
		|--------------------------------------------------------------------------
		| if update qnty is less than old qnty then
		| update qnty can't be less than sum of requisition, yarn dyeing and yarn service qnty
		|--------------------------------------------------------------------------
		|
		*/
		if ($txt_old_qnty > $txt_qnty || $txt_old_qnty == $txt_qnty) {
			$minimum_qty_limit = $booking_requisition_qty + $yarn_dyeing_service_qty;
			if ($txt_qnty < $minimum_qty_limit) {
				$msg = "17**Allocation qnty can't be less than sum of requisition, yarn dyeing and yarn service qnty.";
				$msg .= "\nAllocation qty = " . $booking_allocation_qty;
				if ($allocation_arr['booking_requisition'][$selected_booking_no][$txt_item_id]) {
					$msg .= "\nRequisition no = " . implode(', ', $allocation_arr['booking_requisition'][$selected_booking_no][$txt_item_id]['requisition_no']);
					$msg .= "\nRequisition qty = " . $booking_requisition_qty;
				}

				if ($allocation_arr['yarn_dyeing_service'][$job_no][$txt_item_id]) {
					$msg .= "\nWork order no = " . implode(', ', $allocation_arr['yarn_dyeing_service'][$job_no][$txt_item_id]['yarn_dyeing_prefix_num']);
					$msg .= "\nWork order qty = " . $yarn_dyeing_service_qty;
				}
				$msg .= "\nMinimum qnty limit = " . $minimum_qty_limit;

				echo $msg;
				disconnect($con);
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| if update qnty is greater than old qnty then
		| update qnty can't be greater than sum of allocation and available qnty
		|--------------------------------------------------------------------------
		|
		*/
		if ($txt_old_qnty < $txt_qnty || $txt_old_qnty == $txt_qnty) {
			$maximum_qty_limit = $txt_old_qnty + $check_prod_available_qnty;

			if ($txt_qnty > $maximum_qty_limit) {
				echo "17**Allocation qnty can't be greater than available qnty.\nMaximum qnty limit = " . $maximum_qty_limit;
				disconnect($con);
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| if the product changes during the update and
		| yarn dyeing work order found and
		| yarn requisition found then
		|--------------------------------------------------------------------------
		|
		*/
		if ($txt_item_id != $txt_item_id_old) {
			if (!empty($allocation_arr['yarn_dyeing_service']) && $yarn_dyeing_service_qty > 0) {
				echo "17**Yarn dyeing work order found. Allocated yarn can not be changed.";
				die;
			}

			if (!empty($allocation_arr['booking_requisition']) && $booking_requisition_qty > 0) {
				echo "17**Yarn requisition found. Allocated yarn can not be changed.";
				die;
			}
		}

		//for same product id
		$rslt_mst_tbl_old_product = true;
		$rslt_dtls_tbl_old_product = true;
		$rslt_product_tbl_old_product = true;
		$rslt_mst_log_old_prod_tbl = true;
		$rslt_dtls_log_old_prod_tbl = true;
		$rslt_mst_log_tbl = true;
		$rslt_dtls_log_tbl = true;

		if ($txt_item_id == $txt_item_id_old) {
			$sql_allocation = "SELECT a.id AS ID, b.id AS DTLS_ID, b.qnty AS QNTY FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $txt_sales_order_no . " AND a.item_id = " . $txt_item_id . " AND a.is_sales = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
			$sql_allocation_rslt = sql_select($sql_allocation);

			if (!empty($sql_allocation_rslt)) {
				foreach ($sql_allocation_rslt as $row) {
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data preparing and updating
					|--------------------------------------------------------------------------
					|
					*/
					$mst_id = $row['ID'];
					$qry_mst_tbl = "UPDATE inv_material_allocation_mst SET allocation_date = " . $txt_allocation_date . ", qnty=(qnty+($txt_qnty-$txt_old_qnty)), remarks=" . $txt_remarks . ", updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' WHERE id = " . $mst_id . "";

					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data preparing and updating
					|--------------------------------------------------------------------------
					|
					*/
					$dtls_id = $row['DTLS_ID'];
					$qry_dtls_tbl = "UPDATE inv_material_allocation_dtls SET qnty = (qnty+($txt_qnty-$txt_old_qnty)), updated_by = " . $_SESSION['logic_erp']['user_id'] . ", update_date = '" . $pc_date_time . "' WHERE id = " . $dtls_id . "";

					if ($txt_qnty != $txt_old_qnty) {
						/*
						|--------------------------------------------------------------------------
						| inv_mat_allocation_mst_log
						| data preparing and inserting
						|--------------------------------------------------------------------------
						|
						*/
						$log_qnty = $txt_qnty - $txt_old_qnty;
						$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
						$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
						$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $mst_id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						/*
						|--------------------------------------------------------------------------
						| inv_mat_allocation_dtls_log
						| data preparing and inserting
						|--------------------------------------------------------------------------
						|
						*/
						$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
						$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, avg_usd_rate, avg_usd_amount,exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

						$avg_usd_rate = $allocation_usd_rate;
						$avg_usd_amount = number_format(($log_qnty * $avg_usd_rate), 4, ".", "");
						$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
						$avg_tk_amount = number_format(($log_qnty * $avg_tk_rate), 4, ".", "");

						$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$rslt_mst_log_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
						$rslt_dtls_log_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);
					}

					$rslt_mst_tbl = execute_query($qry_mst_tbl, 0);
					$rslt_dtls_tbl = execute_query($qry_dtls_tbl, 0);
				}


				/*
				| if inv_material_allocation_mst and inv_material_allocation_dtls table data transaction true then
				| product_details_master table allocated_qnty, available_qnty and update_date field will be update
				*/
				if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_mst_log_tbl && $rslt_dtls_log_tbl) {
					//echo "10**",$txt_old_qnty.'='.$txt_qnty; die;
					$rslt_product_tbl = true;
					if ($txt_old_qnty > $txt_qnty) {
						$allocated_qty = $txt_old_qnty - $txt_qnty;
						$rslt_product_tbl = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty-$allocated_qty), available_qnty = (available_qnty+$allocated_qty), update_date = '" . $pc_date_time . "' WHERE id = " . $txt_item_id . "", 0);
					} elseif ($txt_old_qnty < $txt_qnty) {
						$allocated_qty = $txt_qnty - $txt_old_qnty;
						$rslt_product_tbl = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty+$allocated_qty), available_qnty = (available_qnty-$allocated_qty), update_date = '" . $pc_date_time . "' WHERE id = " . $txt_item_id . "", 0);
					}
				}
			} else {
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				$field_mst_tbl = "id,entry_form,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,is_dyied_yarn,is_sales,remarks,inserted_by,insert_date";
				$data_mst_tbl = "(" . $id . ",475," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$field_dtls_tbl = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";
				$data_dtls_tbl = "(" . $id1 . "," . $id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_mst_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
				$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
				$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_dtls_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
				$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, avg_usd_rate, avg_usd_amount, exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

				$avg_usd_rate = $allocation_usd_rate;
				$avg_usd_amount = number_format(($txt_qnty * $avg_usd_rate), 4, ".", "");
				$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
				$avg_tk_amount = number_format(($txt_qnty * $avg_tk_rate), 4, ".", "");

				$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$rslt_mst_tbl = sql_insert("inv_material_allocation_mst", $field_mst_tbl, $data_mst_tbl, 0);
				$rslt_dtls_tbl = sql_insert("inv_material_allocation_dtls", $field_dtls_tbl, $data_dtls_tbl, 0);
				$rslt_mst_log_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
				$rslt_dtls_log_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);

				/*
				| if inv_material_allocation_mst and inv_material_allocation_dtls table data transaction true then
				| product_details_master table allocated_qnty, available_qnty and update_date field will be update
				*/
				if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_mst_log_tbl && $rslt_dtls_log_tbl) {
					$rslt_product_tbl = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty+$txt_qnty), available_qnty = (current_stock-(allocated_qnty+$txt_qnty)), update_date = '" . $pc_date_time . "' WHERE id = " . $txt_item_id . "", 0);
				}
			}
		} else {
			//for old product
			$sql_allocation_old_product = "SELECT a.id AS ID, b.id AS DTLS_ID, b.qnty AS QNTY FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $txt_sales_order_no . " AND a.item_id = " . $txt_item_id_old . " AND a.is_sales = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";

			$sql_allocation_rslt_old_product = sql_select($sql_allocation_old_product);
			if (!empty($sql_allocation_rslt_old_product)) {
				foreach ($sql_allocation_rslt_old_product as $row) {
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data preparing and updating
					| for old product
					|--------------------------------------------------------------------------
					|
					*/
					$mst_id = $row['ID'];
					$qry_mst_tbl_old_product = "UPDATE inv_material_allocation_mst SET allocation_date = " . $txt_allocation_date . ", qnty=(qnty-$txt_old_qnty), remarks = " . $txt_remarks . ", updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' WHERE id = " . $mst_id . "";

					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data preparing and updating
					| for old product
					|--------------------------------------------------------------------------
					|
					*/
					$dtls_id = $row['DTLS_ID'];
					$qry_dtls_tbl_old_product = "UPDATE inv_material_allocation_dtls SET qnty = (qnty-$txt_old_qnty), updated_by = " . $_SESSION['logic_erp']['user_id'] . ", update_date = '" . $pc_date_time . "' WHERE id = " . $dtls_id . "";

					if ($txt_qnty != $txt_old_qnty) {
						/*
						|--------------------------------------------------------------------------
						| inv_mat_allocation_mst_log
						| data preparing and inserting
						|--------------------------------------------------------------------------
						|
						*/
						$log_qnty = $txt_qnty - $txt_old_qnty;
						$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
						$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
						$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $mst_id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id_old . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						/*
						|--------------------------------------------------------------------------
						| inv_mat_allocation_dtls_log
						| data preparing and inserting
						|--------------------------------------------------------------------------
						|
						*/
						$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
						$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, avg_usd_rate, avg_usd_amount, exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

						$avg_usd_rate = $allocation_usd_rate;
						$avg_usd_amount = number_format(($log_qnty * $avg_usd_rate), 4, ".", "");
						$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
						$avg_tk_amount = number_format(($log_qnty * $avg_tk_rate), 4, ".", "");

						$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id_old . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$rslt_mst_log_old_prod_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
						$rslt_dtls_log_old_prod_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);
					}

					$rslt_mst_tbl_old_product = execute_query($qry_mst_tbl_old_product, 0);
					$rslt_dtls_tbl_old_product = execute_query($qry_dtls_tbl_old_product, 0);
				}

				/*
				| if inv_material_allocation_mst and inv_material_allocation_dtls table data transaction true then
				| product_details_master table allocated_qnty, available_qnty and update_date field will be update
				*/
				if ($rslt_mst_tbl_old_product && $rslt_dtls_tbl_old_product && $rslt_mst_log_old_prod_tbl && $rslt_dtls_log_old_prod_tbl) {
					$rslt_product_tbl_old_product = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty-$txt_old_qnty), available_qnty = (available_qnty+$txt_old_qnty), update_date = '" . $pc_date_time . "' WHERE id = " . $txt_item_id_old . "", 0);
				}
			}
			unset($sql_allocation_rslt_old_product);

			/*
			| if same product and sales order no are already allocated then
			| data will be updabe otherwise
			| data will be insert 
			*/
			$sql_allocation = "SELECT a.id AS ID, b.id AS DTLS_ID, b.qnty AS QNTY FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $txt_sales_order_no . " AND a.item_id = " . $txt_item_id . " AND a.is_sales = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
			$sql_allocation_rslt = sql_select($sql_allocation);
			if (!empty($sql_allocation_rslt)) {
				foreach ($sql_allocation_rslt as $row) {
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data preparing and updating
					|--------------------------------------------------------------------------
					|
					*/
					$mst_id = $row['ID'];
					$qry_mst_tbl = "UPDATE inv_material_allocation_mst SET allocation_date = " . $txt_allocation_date . ", qnty=(qnty+$txt_qnty), remarks = " . $txt_remarks . ", updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' WHERE id = " . $mst_id . "";

					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data preparing and updating
					|--------------------------------------------------------------------------
					|
					*/
					$dtls_id = $row['DTLS_ID'];
					$qry_dtls_tbl = "UPDATE inv_material_allocation_dtls SET qnty = (qnty+$txt_qnty), updated_by = " . $_SESSION['logic_erp']['user_id'] . ", update_date = '" . $pc_date_time . "' WHERE id = " . $dtls_id . "";

					if ($txt_qnty != $txt_old_qnty) {
						/*
						|--------------------------------------------------------------------------
						| inv_mat_allocation_mst_log
						| data preparing and inserting
						|--------------------------------------------------------------------------
						|
						*/
						$log_qnty = $txt_qnty - $txt_old_qnty;
						$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
						$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
						$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $mst_id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						/*
						|--------------------------------------------------------------------------
						| inv_mat_allocation_dtls_log
						| data preparing and inserting
						|--------------------------------------------------------------------------
						|
						*/
						$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
						$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, avg_usd_rate ,avg_usd_amount, exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

						$avg_usd_rate = $allocation_usd_rate;
						$avg_usd_amount = number_format(($log_qnty * $avg_usd_rate), 4, ".", "");
						$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
						$avg_tk_amount = number_format(($log_qnty * $avg_tk_rate), 4, ".", "");

						$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$rslt_mst_log_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
						$rslt_dtls_log_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);
					}
				}

				$rslt_mst_tbl = execute_query($qry_mst_tbl, 0);
				$rslt_dtls_tbl = execute_query($qry_dtls_tbl, 0);
			} else {
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				$field_mst_tbl = "id,entry_form,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,is_dyied_yarn,is_sales,remarks,inserted_by,insert_date";
				$data_mst_tbl = "(" . $id . ",475," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing and updating
				|--------------------------------------------------------------------------
				|
				*/
				$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$field_dtls_tbl = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";
				$data_dtls_tbl = "(" . $id1 . "," . $id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_mst_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
				$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
				$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_dtls_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
				$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, avg_usd_rate, avg_usd_amount, exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

				$avg_usd_rate = $allocation_usd_rate;
				$avg_usd_amount = number_format(($txt_qnty * $avg_usd_rate), 4, ".", "");
				$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
				$avg_tk_amount = number_format(($txt_qnty * $avg_tk_rate), 4, ".", "");

				$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}

			if ($data_mst_tbl != "") {
				$rslt_mst_tbl = sql_insert("inv_material_allocation_mst", $field_mst_tbl, $data_mst_tbl, 0);
			}

			if ($data_dtls_tbl != "") {
				$rslt_dtls_tbl = sql_insert("inv_material_allocation_dtls", $field_dtls_tbl, $data_dtls_tbl, 0);
			}

			if ($data_mst_log_tbl != "") {
				$rslt_mst_log_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
			}

			if ($data_dtls_log_tbl != "") {
				$rslt_dtls_log_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);
			}

			//echo "10**INSERT INTO inv_material_allocation_mst (".$field_mst_tbl.") VALUES ".$data_mst_tbl.""; die;

			/*
			| if inv_material_allocation_mst and inv_material_allocation_dtls table data transaction true then
			| product_details_master table allocated_qnty, available_qnty and update_date field will be update
			*/
			if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_mst_log_tbl && $rslt_dtls_log_tbl) {
				$rslt_product_tbl = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty+$txt_qnty), available_qnty = (current_stock-(allocated_qnty+$txt_qnty)), update_date = '" . $pc_date_time . "' WHERE id = " . $txt_item_id . "", 0);
			}
		}

		//echo "10**".$rslt_mst_tbl ."&&". $rslt_dtls_tbl ."&&". $rslt_product_tbl ."&&". $rslt_mst_tbl_old_product ."&&". $rslt_dtls_tbl_old_product ."&&". $rslt_product_tbl_old_product; oci_rollback($con); die();

		if ($db_type == 0) {
			if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_product_tbl && $rslt_mst_tbl_old_product && $rslt_dtls_tbl_old_product && $rslt_product_tbl_old_product) {
				mysql_query("COMMIT");
				echo "1**";
			} else {
				mysql_query("ROLLBACK");
				echo "10**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_product_tbl && $rslt_mst_tbl_old_product && $rslt_dtls_tbl_old_product && $rslt_product_tbl_old_product) {
				oci_commit($con);
				echo "1**";
			} else {
				oci_rollback($con);
				echo "10**";
			}
		}

		disconnect($con);
		die;
	}
	/*
	|--------------------------------------------------------------------------
	| for delete
	|--------------------------------------------------------------------------
	|
	*/ else if ($operation == 2) {
		$con = connect();
		$txt_item_id = str_replace("'", '', $txt_item_id) * 1;
		$txt_item_id_old = str_replace("'", '', $txt_item_id_old);
		$job_no = str_replace("'", '', $txt_sales_order_no);
		$selected_booking_no = str_replace("'", '', $hdn_booking_no);
		$txt_old_qnty = str_replace("'", '', $txt_old_qnty);

		/*
		|--------------------------------------------------------------------------
		| if the product changes during the delete
		|--------------------------------------------------------------------------
		|
		*/
		if ($txt_item_id != $txt_item_id_old) {
			echo "17**The allocated yarn can not be changed during data delete.";
			disconnect($con);
			die;
		}

		/*
		|--------------------------------------------------------------------------
		| for allocation balance check
		|--------------------------------------------------------------------------
		|
		*/
		$arr = array();
		$arr['product_id'] = $txt_item_id_old;
		$arr['is_auto_allocation'] = 0; // [ This page produce auto allocation off forcefully ];
		$arr['job_no'] = $job_no;
		$arr['booking_no'] = $selected_booking_no;
		$arr['po_id'] = str_replace("'", '', $hdn_sales_order_id);
		$allocation_arr = get_allocation_balance($arr);

		$booking_requisition_qty = $allocation_arr['booking_requisition'][$selected_booking_no][$txt_item_id_old]['qty'] * 1;
		$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$job_no][$txt_item_id_old]['qty'] * 1;
		$booking_allocation_qty = $allocation_arr['booking_allocation'][$selected_booking_no][$txt_item_id_old] * 1;

		/*
		|--------------------------------------------------------------------------
		| for yarn requisition check
		|--------------------------------------------------------------------------
		|
		*/
		//echo "17**";
		//echo "<pre>";
		//print_r($allocation_arr);
		//die();

		if (!empty($allocation_arr['booking_requisition']) && $booking_requisition_qty > 0) {
			$booking_requisition_numbers = $allocation_arr['booking_requisition'][$selected_booking_no][$txt_item_id_old]['requisition_no'];
			echo "17**Requisition found. Allocated yarn can not be deleted.\nRequisition no = " . implode(', ', $booking_requisition_numbers) . "\nRequisition quantity = " . number_format($booking_requisition_qty, 2, ".", "");
			disconnect($con);
			die;
		}

		/*
		|--------------------------------------------------------------------------
		| for yarn dyeing work order check
		|--------------------------------------------------------------------------
		|
		*/
		if (!empty($allocation_arr['yarn_dyeing_service']) && $yarn_dyeing_service_qty > 0) {
			$yarn_dyeing_prefix_num = $allocation_arr['yarn_dyeing_service'][$job_no][$txt_item_id_old]['yarn_dyeing_prefix_num'];
			echo "17**Work Order found. Allocated yarn can not be deleted.\nWork Order no = " . implode(', ', $yarn_dyeing_prefix_num);
			disconnect($con);
			die;
		}

		$rslt_mst_tbl = true;
		$rslt_dtls_tbl = true;
		$rslt_product_tbl = true;
		$sql_allocation = "SELECT a.id AS ID, b.id AS DTLS_ID, b.qnty AS QNTY FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $txt_sales_order_no . " AND a.item_id = " . $txt_item_id_old . " AND a.is_sales = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
		$sql_allocation_rslt = sql_select($sql_allocation);
		if (!empty($sql_allocation_rslt)) {
			foreach ($sql_allocation_rslt as $row) {
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing and updating
				|--------------------------------------------------------------------------
				|
				*/
				$mst_id = $row['ID'];
				$qry_mst_tbl = "UPDATE inv_material_allocation_mst SET status_active = 0, is_deleted = 1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' WHERE id = " . $mst_id . "";

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing and updating
				|--------------------------------------------------------------------------
				|
				*/
				$dtls_id = $row['DTLS_ID'];
				$qry_dtls_tbl = "UPDATE inv_material_allocation_dtls SET status_active = 0, is_deleted = 1, updated_by = " . $_SESSION['logic_erp']['user_id'] . ", update_date = '" . $pc_date_time . "' WHERE id = " . $dtls_id . "";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_mst_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$log_qnty = 0 - $txt_old_qnty;
				$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
				$field_mst_log_tbl = "id, entry_form, mst_id, job_no, po_break_down_id, item_category, allocation_date, booking_no, item_id, qnty, is_dyied_yarn, is_sales, remarks, inserted_by, insert_date";
				$data_mst_log_tbl = "(" . $id_mst_log . ",475," . $mst_id . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . ",1," . $txt_allocation_date . "," . $hdn_booking_no . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_mat_allocation_dtls_log
				| data preparing and inserting
				|--------------------------------------------------------------------------
				|
				*/
				$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
				$field_dtls_log_tbl = "id, mst_id, job_no, po_break_down_id, booking_no, item_category, allocation_date, item_id, qnty, is_dyied_yarn, is_sales, avg_usd_rate, avg_usd_amount, exchange_rate, avg_tk_rate, avg_tk_amount, inserted_by, insert_date";

				$avg_usd_rate = $allocation_usd_rate;
				$avg_usd_amount = number_format(($log_qnty * $avg_usd_rate), 4, ".", "");
				$avg_tk_rate = number_format($prod_avg_rate, 2, ".", "");
				$avg_tk_amount = number_format(($log_qnty * $avg_tk_rate), 4, ".", "");

				$data_dtls_log_tbl = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_sales_order_no . "," . $hdn_sales_order_id . "," . $hdn_booking_no . ",1," . $txt_allocation_date . "," . $txt_item_id . "," . $log_qnty . "," . $is_dyied_yarn . ",1," . $avg_usd_rate . "," . $avg_usd_amount . "," . $exchange_rate . "," . $avg_tk_rate . "," . $avg_tk_amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}

			$rslt_mst_tbl = execute_query($qry_mst_tbl, 0);
			$rslt_dtls_tbl = execute_query($qry_dtls_tbl, 0);
			$rslt_mst_log_tbl = sql_insert("inv_mat_allocation_mst_log", $field_mst_log_tbl, $data_mst_log_tbl, 0);
			$rslt_dtls_log_tbl = sql_insert("inv_mat_allocation_dtls_log", $field_dtls_log_tbl, $data_dtls_log_tbl, 0);

			/*
			|--------------------------------------------------------------------------
			| if inv_material_allocation_mst and inv_material_allocation_dtls table data transaction true then
			| product_details_master table allocated_qnty, available_qnty and update_date field will be update
			|--------------------------------------------------------------------------
			*/
			if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_mst_log_tbl && $rslt_dtls_log_tbl) {
				$rslt_product_tbl = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty-$txt_old_qnty), available_qnty = (available_qnty+$txt_old_qnty), update_date = '" . $pc_date_time . "' WHERE id = " . $txt_item_id_old . "", 0);
			} else {
				$rslt_product_tbl = false;
			}
		}

		/*oci_rollback($con);
		echo "10**".$rslt_mst_tbl.'='.$rslt_dtls_tbl.'='.$rslt_mst_log_tbl.'='.$rslt_dtls_log_tbl.'='.$rslt_product_tbl;
		disconnect($con);
		die;*/

		if ($rslt_mst_tbl && $rslt_dtls_tbl && $rslt_product_tbl) {
			oci_commit($con);
			echo "2**";
		} else {
			oci_rollback($con);
			echo "10**";
		}
		disconnect($con);
		die;
	}
}

function count_type_rate_validate($job, $prodId)
{
	$job_no = str_replace("'", "", $job);
	$countP = '';
	$typeP = '';
	$sqlP = sql_select("select id,yarn_count_id,yarn_type from product_details_master where id=$prodId");
	foreach ($sqlP as $rowP) {
		$countP = $rowP[csf('yarn_count_id')];
		$typeP = $rowP[csf('yarn_type')];
	}
	$ratep = number_format(return_itemWise_usdRate($prodId), 4, ".", "");
	$sqlY = sql_select("select count_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and count_id='$countP' and type_id='$typeP' and rate >= $ratep and status_active=1 and is_deleted=0");
	if (count($sqlY) == 0) {
		return false;
	} else {
		return true;
	}
}
?>