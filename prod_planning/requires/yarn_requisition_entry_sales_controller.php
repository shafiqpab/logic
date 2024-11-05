<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

// get buyer condition according to priviledge
//if ($_SESSION['logic_erp']["data_level_secured"]==1){
if ($_SESSION['logic_erp']["buyer_id"] != "") {
	$buyer_id_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
} else {
	$buyer_id_cond = "";
}
// } else {
// 	$buyer_id_cond="";
// }


if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if ($action == "style_ref_search_popup_old") {
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>



	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function() {
			set_field_level_access(<? echo $companyID; ?>);
		}
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_booking = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

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

		function js_set_value(str, job) {
			$('#hide_job_id').val(str);
			$('#hide_job_no').val(job);

			parent.emailwindow.hide();
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());
				selected_booking.push($('#txt_booking_no' + str).val());
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_booking.splice(i, 1);
			}
			var id = '';
			var name = '';
			var booking = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				booking += selected_booking[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			booking = booking.substr(0, booking.length - 1);
			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
			$('#hide_booking_no').val(booking);
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>PO Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Sales Order No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />

							<!-- for contents type search  -->
							<input type="hidden" id="cbo_string_search_type" value="4" />

						</thead>
						<tbody>
							<tr>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
									$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:90px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:05px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "style_ref_search_popup") {
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function() {
			set_field_level_access(<? echo $companyID; ?>);
		}
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_booking = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

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

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());
				selected_booking.push($('#txt_booking_no' + str).val());
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_booking.splice(i, 1);
			}
			var id = '';
			var name = '';
			var booking = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				booking += selected_booking[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			booking = booking.substr(0, booking.length - 1);
			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
			$('#hide_booking_no').val(booking);
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>PO Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Sales Order No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
									$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:05px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}


if ($action == "get_program_by_booking_for_req") {
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function() {
			set_field_level_access(<? echo $companyID; ?>);
		}
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_booking = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

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

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_booking_no' + str).val(), selected_booking) == -1) {
				selected_booking.push($('#txt_booking_no' + str).val());
			} else {
				for (var i = 0; i < selected_booking.length; i++) {
					if (selected_booking[i] == $('#txt_booking_no' + str).val()) break;
				}
				selected_booking.splice(i, 1);
			}
			var booking = '';
			for (var i = 0; i < selected_booking.length; i++) {
				booking += selected_booking[i] + '*';
			}

			booking = booking.substr(0, booking.length - 1);
			$('#hide_booking_no').val(booking);
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Cust. Buyer</th>
							<th id="search_by_td_up" width="170">Sales Job/ Booking No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="cbo_search_by" id="cbo_search_by" value="3" />
						</thead>
						<tbody>
							<tr>
								<td id="buyer_td">
									<?
									//echo create_drop_down("cbo_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_buyer_brand', 'cust_buyer_brand_td' );load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_season', 'season_td' )", 0);

									echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=" . $companyID . " $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:05px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_job_search_list_view") {
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');

	$company_id = $data[0];
	$buyer_id = $data[1];
	$po_buyer_id = $data[2];
	$within_group = $data[3];
	$search_by = $data[4];
	$search_string = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "%'";
		} else if ($search_by == 3) {
			$search_field_cond = " and a.sales_booking_no like '%" . $search_string . "%'";
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($within_group == 0) $within_group_cond = "";
	else $within_group_cond = " and a.within_group=$within_group";
	if ($buyer_id == 0) $buyer_id_cond = "";
	else $buyer_id_cond = " and a.buyer_id=$buyer_id";

	if ($po_buyer_id == 0) {
		if ($_SESSION['logic_erp']["buyer_id"] != "") {
			$po_buyer_id_cond = " and b.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		} else {
			$po_buyer_id_cond = "";
		}
	} else {
		$po_buyer_id_cond = " and b.buyer_id=$po_buyer_id";
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = ""; //defined Later


	if ($db_type == 0) {
		$booking_year = " b.booking_year";
		$booking_year2 = "YEAR(c.booking_date) as booking_year";
	} else {
		$booking_year = " cast(b.booking_year as varchar(4000)) as booking_year";
		$booking_year2 = "to_char(c.booking_date,'YYYY') as booking_year";
	}

	if ($within_group == 1) {
		$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no,b.booking_no_prefix_num,$booking_year from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $buyer_id_cond $po_buyer_id_cond
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, c.buyer_id po_buyer,c.booking_no,c.booking_no_prefix_num,$booking_year2  from fabric_sales_order_mst a inner join wo_non_ord_samp_booking_mst c on a.sales_booking_no = c.booking_no where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond
		order by id desc";
	} else {
		$sql = "select a.id, $year_field,a.job_no_prefix_num,a.job_no,a.within_group,a.sales_booking_no booking_no_prefix_num,a.booking_date,a.buyer_id,a.style_ref_no,a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond and a.within_group=2 order by a.id desc";
	}
	//echo $sql;
	$result = sql_select($sql);
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Sales Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th width="60">Booking Year</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:770px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
					<td width="40" align="center"><? echo $i; ?>
						<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>" />
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row[csf('job_no')]; ?>" />
						<?php
						if ($within_group == 1) {
							$booking = $row[csf('booking_no_prefix_num')];
						} else {
							$booking = $row[csf('sales_booking_no')];
						}
						?>
						<input type="hidden" name="txt_booking_no" id="txt_booking_no<?php echo $i ?>" value="<? echo $row[csf('booking_no_prefix_num')]; ?>" />
					</td>
					<td width="70" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center">
						<p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p>
					</td>
					<td width="70">
						<p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p>
					</td>
					<td width="70" align="center">
						<p><? echo $buyer; ?>&nbsp;</p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('booking_no_prefix_num')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $row[csf('booking_year')]; ?></p>
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
	<table width="760" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
<?
	exit();
}

//--------------
if ($action == "get_internal_ref") {
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function() {
			set_field_level_access(<? echo $companyID; ?>);
		}
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_booking = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

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

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_internalref' + str).val(), selected_booking) == -1) {
				selected_booking.push($('#txt_internalref' + str).val());
			} else {
				for (var i = 0; i < selected_booking.length; i++) {
					if (selected_booking[i] == $('#txt_internalref' + str).val()) break;
				}
				selected_booking.splice(i, 1);
			}
			var booking = '';
			for (var i = 0; i < selected_booking.length; i++) {
				booking += selected_booking[i] + '*';
			}

			booking = booking.substr(0, booking.length - 1);
			$('#hide_internalref').val(booking);
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:880px;">
					<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>PO Buyer</th>
							<th id="search_by_td_up" width="170">Booking No</th>
							<th>IR/IB</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_internalref" id="hide_internalref" value="" />
							<input type="hidden" name="cbo_search_by" id="cbo_search_by" value="3" />
						</thead>
						<tbody>
							<tr>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_internal_ref').value+'**', 'create_internal_ref_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:05px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_internal_ref_search_list_view") {
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');

	$company_id = $data[0];
	$buyer_id = $data[1];
	$po_buyer_id = $data[2];
	$within_group = $data[3];
	$search_by = $data[4];
	$search_string = trim($data[5]);
	$search_internal_ref = trim($data[6]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "%'";
		} else if ($search_by == 3) {
			$search_field_cond = " and a.sales_booking_no like '%" . $search_string . "%'";
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($search_internal_ref != "") {
		$internalRef_cond = " and a.grouping like '%" . $search_internal_ref . "%'";
		//for internal ref.
		$sql_bookings = sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond group by b.booking_no,a.job_no_mst,a.grouping");
		$internalRefArr = array();
		$booksArrChks1 = array();
		$bookinss_nos1 = "";
		foreach ($sql_bookings as $row) {

			if ($row[csf('booking_no')] != "") {
				if ($booksArrChks1[$row[csf('booking_no')]] != $row[csf('booking_no')]) {
					$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping'] = $row[csf('grouping')];
					$bookinss_nos1 .= "'" . $row[csf('booking_no')] . "',";
					$booksArrChks1[$row[csf('booking_no')]] = $row[csf('booking_no')];
				}
			}
		}
		$bookinss_nos1 = chop($bookinss_nos1, ",");
		$booking_nos_cond2 = "and a.sales_booking_no in($bookinss_nos1)";
		unset($sql_bookings);
	}


	if ($within_group == 0) $within_group_cond = "";
	else $within_group_cond = " and a.within_group=$within_group";
	if ($buyer_id == 0) $buyer_id_cond = "";
	else $buyer_id_cond = " and a.buyer_id=$buyer_id";

	if ($po_buyer_id == 0) {
		if ($_SESSION['logic_erp']["buyer_id"] != "") {
			$po_buyer_id_cond = " and b.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		} else {
			$po_buyer_id_cond = "";
		}
	} else {
		$po_buyer_id_cond = " and b.buyer_id=$po_buyer_id";
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = ""; //defined Later


	if ($db_type == 0) {
		$booking_year = " b.booking_year";
		$booking_year2 = "YEAR(c.booking_date) as booking_year";
	} else {
		$booking_year = " cast(b.booking_year as varchar(4000)) as booking_year";
		$booking_year2 = "to_char(c.booking_date,'YYYY') as booking_year";
	}

	if ($within_group == 1) {
		$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num,b.booking_no as booking_no,a.po_job_no,$booking_year from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $buyer_id_cond $po_buyer_id_cond $booking_nos_cond2
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, c.buyer_id po_buyer,c.booking_no_prefix_num,c.booking_no as booking_no,a.po_job_no,$booking_year2  from fabric_sales_order_mst a inner join wo_non_ord_samp_booking_mst c on a.sales_booking_no = c.booking_no where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $booking_nos_cond2
		order by id desc";
	} else {
		$sql = "select a.id, $year_field,a.job_no_prefix_num,a.job_no,a.within_group,a.sales_booking_no,a.sales_booking_no as booking_no, booking_no_prefix_num,a.booking_date,a.buyer_id,a.style_ref_no,a.location_id,a.po_job_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $booking_nos_cond2 and a.within_group=2 order by a.id desc";
	}
	//echo $sql;
	$result = sql_select($sql);
	$booksArrChks = array();
	$bookinss_nos = "";
	foreach ($result as $row) {
		if ($row[csf('sales_booking_no')] != "") {
			if ($booksArrChks[$row[csf('sales_booking_no')]] != $row[csf('sales_booking_no')]) {
				$bookinss_nos .= "'" . $row[csf('sales_booking_no')] . "',";
				$booksArrChks[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
			}
		}
	}

	//for internal ref.
	$bookins_nos = chop($bookinss_nos, ",");
	if ($search_string == "" || $search_internal_ref == "") {
		//for internal ref.
		$sql_bookings = sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_no in($bookins_nos) group by b.booking_no,a.job_no_mst,a.grouping");
		$internalRefArr = array();
		foreach ($sql_bookings as $row) {
			$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping'] = $row[csf('grouping')];
		}
		unset($sql_bookings);
	}

?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Sales Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th width="60">Booking Year</th>
			<th width="100">IR/IB</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:870px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
					<td width="40" align="center"><? echo $i; ?>
						<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>" />
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row[csf('job_no')]; ?>" />
						<?php
						if ($within_group == 1) {
							$booking = $row[csf('booking_no_prefix_num')];
						} else {
							$booking = $row[csf('sales_booking_no')];
						}
						?>
						<input type="hidden" name="txt_booking_no" id="txt_booking_no<?php echo $i ?>" value="<? echo $row[csf('booking_no_prefix_num')]; ?>" />
						<input type="hidden" name="txt_internalref" id="txt_internalref<?php echo $i ?>" value="<? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('po_job_no')]]['grouping']; ?>" />
					</td>
					<td width="70" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center">
						<p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p>
					</td>
					<td width="70">
						<p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p>
					</td>
					<td width="70" align="center">
						<p><? echo $buyer; ?>&nbsp;</p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('booking_no_prefix_num')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $row[csf('booking_year')]; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('po_job_no')]]['grouping']; ?></p>
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
	<table width="860" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
<?
	exit();
}
//-------------

if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');
	$company_arr = return_library_array("select id, company_short_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_short_name');
	$lib_color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$type = str_replace("'", "", $cbo_type);
	$planning_status = str_replace("'", "", $cbo_planning_status);
	$cbo_within_group = str_replace("'", "", $cbo_within_group);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$company_name = $cbo_company_name;
	$barcode = str_replace("'", "", trim($txt_barcode));
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$txt_internal_ref = str_replace("'", "", $txt_internal_ref);
	$txt_prog = str_replace("'", "", trim($txt_prog));
	$txt_requistionNo = str_replace("'", "", trim($txt_requistionNo));

	if ($txt_prog != '') $porg_no_cond = "  and b.id=$txt_prog";
	else $porg_no_cond = "";
	if ($txt_requistionNo != '') $req_no_cond = "  and requisition_no=$txt_requistionNo";
	else $req_no_cond = "";

	if ($txt_internal_ref != "") {
		$txt_internal_refExplode = explode('*', $txt_internal_ref);
		$internalRefStr = "";
		foreach ($txt_internal_refExplode as $keyval) {
			$internalRefStr .= "'" . $keyval . "',";
		}
		$internalRefStr = chop($internalRefStr, ",");
		$sql_bookings = sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.grouping in($internalRefStr) group by b.booking_no,a.job_no_mst,a.grouping");
		$booking_nos = "";
		$bookingArrChk = array();
		foreach ($sql_bookings as $row) {
			if ($bookingArrChk[$row[csf('booking_no')]] != $row[csf('booking_no')]) {
				$booking_nos .= "'" . $row[csf('booking_no')] . "',";
				$bookingArrChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
			}
		}
		$booking_nos = chop($booking_nos, ",");
		$booking_nos_cond = "and a.booking_no in($booking_nos)";
		unset($sql_bookings);
	}


	$job_no_cond = "";
	if (str_replace("'", "", $hide_job_id) != "") {
		$hideJobId = str_replace("'", "", $hide_job_id);
		$expHideJobId = explode(',', $hideJobId);
		$hideJobIdArr = array();
		foreach ($expHideJobId as $key => $val) {
			$hideJobIdArr[$val] = $val;
		}

		$job_no_cond = where_con_using_array($hideJobIdArr, '0', 'c.po_id');
	}

	$within_group_cond = " and a.within_group=$cbo_within_group";
	if ($buyer_name == 0)
		$buyer_id_cond = "";
	else
		$buyer_id_cond = " and a.buyer_id=$buyer_name";

	if (str_replace("'", "", $txt_machine_dia) == "") {
		$machine_dia_cond = "";
	} else {
		$txt_machine_dia = str_replace("'", "", $txt_machine_dia);
		$machine_dia_cond = "and machine_dia like '%$txt_machine_dia%'";
	}

	if ($txt_booking_no != '') $booking_no = "  and a.booking_no LIKE '%$txt_booking_no%'";
	else $booking_no = "";

	if ($barcode != "") {
		$expBarcode = explode(',', $barcode);
		$barcodeArr = array();
		foreach ($expBarcode as $key => $val) {
			$barcodeArr[$val] = $val;
		}

		$barcode_cond = where_con_using_array($barcodeArr, '0', 'b.barcode_no');
		$sales_dtls_result = sql_select("select b.id from fabric_sales_order_dtls b where b.status_active=1 and b.is_deleted=0 $barcode_cond");
		foreach ($sales_dtls_result as $srow) {
			$salseDetailsIdArr[$srow[csf('id')]] = $srow[csf('id')];
		}

		if (!empty($salseDetailsIdArr)) {
			$salse_detls_id = chop($salse_detls_id, " , ");
			$barcodeSalse_detlsCond = where_con_using_array($salseDetailsIdArr, '0', 'sales_order_dtls_ids');
		}
	}
	if ($txt_requistionNo != '') {
		$reqs_sql_search = sql_select("select knit_id  from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $req_no_cond group by knit_id");
		$knit_ids = "";
		foreach ($reqs_sql_search as $row) {
			$knit_ids .= $row[csf('knit_id')] . ",";
		}
		$knit_ids = chop($knit_ids, ",");
		if ($knit_ids != '') $porg_no_cond2 = "  and b.id=$knit_ids";
		else $porg_no_cond2 = "";
	}




	$i = 1;
	$k = 1;
	$tot_program_qnty = 0;
	$tot_yarn_req_qnty = 0;
	$machine_dia_gg_array = array();
	$program_no_arr = array();
	$sales_order_arr = array();
	$sql = "select a.company_id, a.within_group, a.booking_no, a.buyer_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,c.po_id, b.color_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_name and b.knitting_source=$type and (a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 or c.is_issued=1) and b.is_sales=1 $job_no_cond $booking_no $buyer_id_cond $within_group_cond $barcodeSalse_detlsCond $machine_dia_cond $booking_nos_cond $porg_no_cond $porg_no_cond2 group by a.company_id, a.buyer_id, a.within_group, a.booking_no, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,c.po_id, b.color_id order by b.machine_dia,b.machine_gg";
	//echo $sql;
	$nameArray = sql_select($sql);
	foreach ($nameArray as $row) {
		$program_no_arr[$row[csf('id')]] = $row[csf('id')];
		$sales_order_arr[$row[csf('po_id')]] = $row[csf('po_id')];
	}

	$reqs_array = array();
	$program_cond = '';
	$program_color_cond = '';
	if (!empty($sales_order_arr)) {
		$program_cond = where_con_using_array($program_no_arr, '0', 'knit_id');
		$program_color_cond = where_con_using_array($program_no_arr, '0', 'program_no');
	}

	$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond group by knit_id, requisition_no");
	foreach ($reqs_sql as $row) {
		$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
		$reqs_array[$row[csf('knit_id')]]['qnty'] = $row[csf('yarn_req_qnty')];
	}

	$job_no_array = array();
	$booking_no_arr = array();
	$sales_cond = '';
	if (!empty($sales_order_arr)) {
		$sales_cond = where_con_using_array($sales_order_arr, '0', 'a.id');
	}

	$jobData = sql_select("select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no, a.po_job_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $sales_cond");
	$po_job_no_arr = array();
	foreach ($jobData as $row) {
		$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$job_no_array[$row[csf('id')]]['job_id'] = $row[csf('id')];
		$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		$job_no_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$job_no_array[$row[csf('id')]]['po_job_no'] = $row[csf('po_job_no')];
		$booking_no_arr[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];

		if ($jo_no_chk[$row['po_job_no']] == "") {
			$jo_no_chk[$row['po_job_no']] = $row['po_job_no'];
			array_push($po_job_no_arr, $row[csf('po_job_no')]);
		}
	}
	$break_down_arr = array();
	$break_down_cond = '';
	if (!empty($po_job_no_arr)) {
		$break_down_cond = where_con_using_array($po_job_no_arr, '1', 'job_no_mst');
	}

	$poBreakData = "select job_no_mst, grouping from wo_po_break_down where status_active=1 and is_deleted=0 $break_down_cond";
	//echo $poBreakData;

	foreach (sql_select($poBreakData) as $rows) {
		$break_down_arr[$rows[csf('job_no_mst')]]['grouping'] = $rows[csf('grouping')];
	}
	//var_dump($break_down_arr);

	if ($within_group == 0) {
		$fld_cap = "Booking";
		$booking_cond = '';
		if (!empty($booking_no_arr)) {
			$booking_cond = where_con_using_array($booking_no_arr, '1', 'booking_no');
		}

		$sql_data = sql_select("select buyer_id, booking_no from wo_booking_mst where item_category in(2,13) and status_active=1 and is_deleted=0 $booking_cond
		union all
		select buyer_id,booking_no from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $booking_cond
		");
		$booking_buyer_array = array();
		foreach ($sql_data as $row) {
			$booking_buyer_array[$row[csf('booking_no')]] = $row[csf('buyer_id')];
		}
	} else
		$fld_cap = "Sale";


	$prog_color_sql = sql_select("select  program_no,color_id from ppl_color_wise_break_down where status_active=1 and is_deleted=0 $program_color_cond group by program_no, color_id");
	foreach ($prog_color_sql as $row) {
		$progColorArr[$row[csf('program_no')]]['colorID'] = $lib_color_arr[$row[csf('color_id')]];
	}


	$print_report_format = return_field_value("format_id", " lib_report_template", "template_name =$company_name  and module_id=4 and report_id=269 and is_deleted=0 and status_active=1");
	$fReportId = explode(",", $print_report_format);
	$fReportId = $fReportId[0];
?>
	<fieldset style="width:1900px; margin-top:10px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1900" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="70">Program No</th>
				<th width="80">Program Date</th>
				<th width="70">Customers</th>
				<th width="110">Sales Order No</th>
				<th width="110"><? echo $fld_cap; ?> No</th>
				<th width="70">Customers Buyer</th>
				<th title="internal Ref." width="100">IR/IB</th>
				<th width="110">Style</th>
				<th width="80">Dia / GG</th>
				<th width="145">Fabric Description</th>
				<th width="70">Fabric Gsm</th>
				<th width="60">Fabric Dia</th>
				<th width="80">Width/Dia Type</th>
				<th width="90">Color Range</th>
				<th width="100">Color Name</th>
				<th width="90">Program Qnty</th>
				<th width="90">Yarn Req. Qnty</th>
				<th width="70">Req. No</th>
				<th width="75">Start Date</th>
				<th width="75">T.O.D</th>
				<th>Status</th>
			</thead>
		</table>
		<div style="width:1900px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1880" class="rpt_table" id="tbl_list_search">
				<tbody>
					<?
					foreach ($nameArray as $row) {
						$machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];
						$yarn_req_qnty = $reqs_array[$row[csf('id')]]['qnty'];
						$reqs_no = $reqs_array[$row[csf('id')]]['reqs_no'];
						$buyer_nam = $job_no_array[$row[csf('po_id')]]['buyer_id'];


						$balance_qnty = $row[csf('program_qnty')] - $yarn_req_qnty;

						if (($planning_status == 3 && $balance_qnty <= 0) || ($planning_status == 1 && $balance_qnty > 0)) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							if (!in_array($machine_dia_gg, $machine_dia_gg_array)) {
								if ($k != 1) {
					?>
									<tr bgcolor="#CCCCCC">
										<td colspan="16" align="right"><b>Sub Total</b></td>
										<td align="right">
											<b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b>
										</td>
										<td align="right">
											<b><? echo number_format($sub_tot_yarn_req_qnty, 2, '.', ''); ?></b>
										</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								<?
									$sub_tot_program_qnty = 0;
									$sub_tot_yarn_req_qnty = 0;
								}

								?>
								<tr bgcolor="#EFEFEF">
									<td colspan="22"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
								</tr>
							<?
								$machine_dia_gg_array[] = $machine_dia_gg;
								$k++;
							}

							$style_ref 	  = $job_no_array[$row[csf('po_id')]]['style_ref'];
							$job_no    	  = $job_no_array[$row[csf('po_id')]]['job_no'];
							$job_ids   	  = $job_no_array[$row[csf('po_id')]]['job_id'];
							$grouping  	  = $job_no_array[$row[csf('po_id')]]['po_job_no'];
							$internal_ref = $break_down_arr[$grouping]['grouping'];

							if ($row[csf('within_group')] == 1) {
								$buyer = $company_arr[$row[csf('buyer_id')]];
								$customers_buyer = $buyer_arr[$booking_buyer_array[$row[csf('booking_no')]]];
							} else {
								$buyer = $buyer_arr[$buyer_nam];
								$customers_buyer = $buyer_arr[$buyer_nam];
							}

							$cons_comps = explode(",", $row[csf('fabric_desc')]);
							$comps = $cons_comps[1];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="70" align="center">&nbsp;&nbsp;<? echo $row[csf('id')]; ?>&nbsp;</td>
								<td width="80" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
								<td width="70" align="center">
									<p><? echo $buyer; ?></p>
								</td>
								<td width="110" align="center">
									<p><? echo $job_no; ?></p>
								</td>
								<td width="110" align="center">
									<p><? echo $row[csf('booking_no')]; ?></p>
								</td>
								<td width="70" align="center">
									<p><? echo $customers_buyer; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo $internal_ref; ?></p>
								</td>
								<td width="110" align="center">
									<p><? echo $style_ref; ?></p>
								</td>
								<td width="80" align="center">
									<p><? echo $machine_dia_gg; ?></p>
								</td>
								<td width="145" align="center">
									<p><? echo $row[csf('fabric_desc')]; ?></p>
								</td>
								<td width="70" align="center">
									<p><? echo $row[csf('gsm_weight')]; ?></p>
								</td>
								<td width="60" align="center">
									<p><? echo $row[csf('dia')]; ?></p>
								</td>
								<td width="80" align="center"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
								<td width="90" align="center">
									<p><? echo $color_range[$row[csf('color_range')]]; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo $lib_color_arr[$row[csf('color_id')]]; ?></p>
								</td>
								<td align="right" width="90" title="<? echo $progColorArr[$row[csf('id')]]['colorID']; ?>"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
								<td align="center" valign="middle" width="90">
									<input type="text" name="txt_yarn_req_qnty[]" id="txt_yarn_req_qnty_<? echo $i; ?>" style="width:70px" class="text_boxes_numeric" readonly value="<? if ($yarn_req_qnty > 0) echo number_format($yarn_req_qnty, 2); ?>" placeholder="Single Click" onClick="openmypage_yarnReq(<? echo $i; ?>,'<? echo $row[csf('id')]; ?>',<? echo $company_name; ?>,'<? echo $comps; ?>','<? echo $job_no; ?>','<? echo $reqs_no; ?>','<? echo $job_ids; ?>','<? echo $cbo_within_group; ?>','<? echo $row[csf('program_qnty')]; ?>')" />
								</td>
								<td align="center" width="70"><? echo "<a href='##' onclick=\"generate_report2(" . $row[csf('company_id')] . "," . $row[csf('id')] . "," . $row[csf('within_group')] . "," . $fReportId . ")\">$reqs_no</a>"; ?>
									&nbsp;</td>
								<td width="75" align="center">
									&nbsp;<? if ($row[csf('start_date')] != "" && $row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
								<td width="75" align="center">
									&nbsp;<? if ($row[csf('end_date')] != "" && $row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
								<td align="center">
									<p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p>
								</td>
							</tr>
						<?

							$sub_tot_program_qnty += $row[csf('program_qnty')];
							$sub_tot_yarn_req_qnty += $yarn_req_qnty;

							$tot_program_qnty += $row[csf('program_qnty')];
							$tot_yarn_req_qnty += $yarn_req_qnty;

							$i++;
						}
					}
					if ($i > 1) {
						?>
						<tr bgcolor="#CCCCCC">
							<td colspan="16" align="right"><b>Sub Total</b></td>
							<td align="right"><b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
							<td align="right"><b><? echo number_format($sub_tot_yarn_req_qnty, 2, '.', ''); ?></b></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					<?
					}
					?>
				</tbody>
				<tfoot>
					<th colspan="16" align="right">Grand Total</th>
					<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_yarn_req_qnty, 2, '.', ''); ?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$path = $data[2];
	//echo $path;die;
	echo load_html_head_contents("Program Qnty Info", $path, 1, 1, '', '', '');
	//echo $company_id;die;

	$company_details = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$country_arr = return_library_array("select id, country_name from lib_country where status_active=1 and is_deleted=0", 'id', 'country_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", "id", "machine_no");
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$sales_info = sql_select("select a.job_no, a.style_ref_no,a.location_id, c.color_type_id from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b, fabric_sales_order_dtls c where a.id=b.po_id and a.id=c.mst_id and b.dtls_id = $program_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no, a.style_ref_no,a.location_id, c.color_type_id ");
	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

?>
	<div style="width:860px">
		<div style="margin-left:20px; width:850px">
			<div style="width:100px;float:left;position:relative;margin-top:10px">
				<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
				<img src="<? echo $path . $image_location; ?>" height='100%' width='100%' />
			</div>
			<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
			<div style="float:left;position:relative;">
				<table width="100%" style="margin-top:10px; font-family: tahoma;">
					<tr>
						<td align="center" style="font-size:16px;">
							<? echo $company_details[$company_id]; ?>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px">
							<?
							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result) {
							?>
								Plot No: <? echo $result['plot_no']; ?>
								Level No: <? echo $result['level_no'] ?>
								Road No: <? echo $result['road_no']; ?>
								Block No: <? echo $result['block_no']; ?>
								City No: <? echo $result['city']; ?>
								Zip Code: <? echo $result['zip_code']; ?>
								Country: <? echo $country_arr[$result['country_id']]; ?><br>
								Email Address: <? echo $result['email']; ?>
								Website No: <? echo $result['website'];
										}
											?>
						</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
					</tr>
				</table>
			</div>
		</div>
		<div style="margin-left:10px;float:left; width:850px;">
			<?
			$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id,fabric_dia from ppl_planning_info_entry_dtls where id=$program_id and status_active=1 and is_deleted=0");


			if ($dataArray[0][csf('knitting_source')] == 1) {

				$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");
			} else if ($dataArray[0][csf('knitting_source')] == 3) {
				$location = return_field_value("location_name", "lib_location", "id='" . $sales_info[0][csf('location_id')] . "'");
			}

			$advice = $dataArray[0][csf('advice')];

			$mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia, within_group, color_type_id from ppl_planning_info_entry_mst where status_active=1 and is_deleted=0 and id=" . $dataArray[0][csf('mst_id')]);
			$booking_no = $mst_dataArray[0][csf('booking_no')];
			$buyer_id = $mst_dataArray[0][csf('buyer_id')];
			$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
			$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
			$dia = $mst_dataArray[0][csf('dia')];
			$within_group = $mst_dataArray[0][csf('within_group')];
			$color_type_id = $mst_dataArray[0][csf('color_type_id')];

			?>
			&nbsp;&nbsp;<b>Attention- Knitting Manager</b>
			<table width="100%" style="margin-top:5px; font-family: tahoma;" cellspacing="7">
				<tr>
					<td width="140"><b>Program No:</b></td>
					<td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="170"><b>Program Date:</b></td>
					<td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
				</tr>
				<tr>
					<td><b>Factory:</b></td>
					<td>
						<?
						if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
						else if ($dataArray[0][csf('knitting_source')] == 3) echo $supllier_arr[$dataArray[0][csf('knitting_party')]];
						?>
					</td>
					<td><b>Fabrication & FGSM:</b></td>
					<td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
				</tr>
				<tr>
					<td><b>Address:</b></td>
					<td colspan="3">
						<?
						$address = '';
						if ($dataArray[0][csf('knitting_source')] == 1) {
							$addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result) {
						?>
								Plot No: <? echo $result[csf('plot_no')]; ?>
								Road No: <? echo $result[csf('road_no')]; ?>
								Block No: <? echo $result[csf('block_no')]; ?>
								City No: <? echo $result[csf('city')];
										}
									} else if ($dataArray[0][csf('knitting_source')] == 3) {
										$address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
										echo $address;
									}

									$machine_no = '';
									$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
									foreach ($machine_id as $val) {
										if ($machine_no == '') $machine_no = $machine_arr[$val];
										else $machine_no .= "," . $machine_arr[$val];
									}

									if ($within_group == 1) {
										$buyer = $company_details[$buyer_id];
										$booking_buyer = return_field_value("buyer_id", "wo_booking_mst", "booking_no='" . $booking_no . "'");
										$customers_buyer = $buyer_arr[$booking_buyer];
									} else {
										$buyer = $buyer_arr[$buyer_id];
										$customers_buyer = $buyer_arr[$buyer_id];
									}
											?>
					</td>
				</tr>
				<tr>
					<td><b>PO Company:</b></td>
					<td><b><? echo $buyer; ?></b></td>
					<td><b>PO Buyer:</b></td>
					<td><b><? echo $customers_buyer; ?></b></td>
				</tr>
				<tr>
					<td><b>Sales Order No:</b></td>
					<td><b><? echo $sales_info[0][csf('job_no')]; ?></b></td>
					<td><b>Fabric/Booking No:</b></td>
					<td><b><? echo $booking_no; ?></b></td>
				</tr>
				<tr>
					<td><b>Style Ref.:</b></td>
					<td><b><? echo $sales_info[0][csf('style_ref_no')]; ?></b></td>
					<td><b>Location:</b></td>
					<td><b><? echo $location; ?></b></td>
				</tr>
				<tr>
					<td><b>Machine No:</b></td>
					<td><b><? echo $machine_no; ?></b></td>
					<td><b>Color Type:</b></td>
					<td><b><? echo $color_type[$color_type_id]; ?></b></td>
				</tr>
			</table>

			<table style="margin-top:10px; font-family: tahoma;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="70">Requisition No</th>
					<th width="70">Lot No</th>
					<th width="225">Yarn Description</th>
					<th width="110">Brand</th>
					<th width="80">Requisition Qnty</th>
					<th width="120">Yarn Color</th>
					<th>Remarks</th>
				</thead>
				<?
				$i = 1;
				$tot_reqsn_qnty = 0;


				//sql = "select a.requisition_no, a.prod_id, a.yarn_qnty from ppl_yarn_requisition_entry a,ppl_planning_count_feed_dtls b where a.knit_id=b.dtls_id and a.prod_id=b.prod_id and a.knit_id='" . $dataArray[0][csf('id')] . "' and a.status_active=1 and a.is_deleted=0 order by b.seq_no asc";

				$sql_seq = sql_select("select a.requisition_no, a.prod_id, a.yarn_qnty from ppl_yarn_requisition_entry a,ppl_planning_count_feed_dtls b where a.knit_id=b.dtls_id and a.prod_id=b.prod_id and a.knit_id='" . $dataArray[0][csf('id')] . "' and a.status_active=1 and a.is_deleted=0 and b.prod_desc is not null  order by seq_no asc");
				$prodIds = "";
				foreach ($sql_seq as $selectRes) {
					$prodIds .= $selectRes[csf('prod_id')] . ',';
				}
				$prodIds = chop($prodIds, ",");
				if ($prodIds != "") {
					$seqProdCond = "and prod_id not in($prodIds)";
				}

				$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0 $seqProdCond";



				$nameArray = sql_select($sql);
				foreach ($sql_seq as $selectResult) {
				?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td align="center">
							<p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
								&nbsp;</p>
						</td>
						<td>
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
								&nbsp;</p>
						</td>
						<td>
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p>
						</td>
						<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
						<td>
							<p>
								&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
						</td>
						<td>&nbsp;</td>
					</tr>
				<?
					$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
					$i++;
				}
				foreach ($nameArray as $selectResult) {
				?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td align="center">
							<p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
								&nbsp;</p>
						</td>
						<td>
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
								&nbsp;</p>
						</td>
						<td>
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p>
						</td>
						<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
						<td>
							<p>
								&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
						</td>
						<td>&nbsp;</td>
					</tr>
				<?
					$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
					$i++;
				}
				?>
				<tfoot>
					<th colspan="5" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
			<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px; font-family: tahoma;" class="rpt_table">
				<tr>
					<td width="120"><b>Colour Range:</b></td>
					<td width="150">
						<p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p>
					</td>
					<td width="120"><b>GGSM OR S/L:</b></td>
					<td width="150">
						<p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p>
					</td>
					<td width="120"><b>FGSM:</b></td>
					<td>
						<p><? echo $gsm_weight; ?>&nbsp;</p>
					</td>
				</tr>
				<tr>
					<td><b>Finish Dia</b></td>
					<td>
						<p><? echo $dataArray[0][csf('fabric_dia')] . "  (" . $fabric_typee[$dataArray[0][csf('width_dia_type')]] . ")"; ?>
							&nbsp;</p>
					</td>
					<td><b>Machine Dia & Gauge:</b></td>
					<td>
						<p><? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?>
							&nbsp;</p>
					</td>
					<td><b>Program Qnty:</b></td>
					<td>
						<p><? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;</p>
					</td>
				</tr>
				<tr>
					<td><b>Feeder:</b></td>
					<td>
						<p>
							<?
							$feeder_array = array(1 => "Full Feeder", 2 => "Half Feeder");
							echo $feeder_array[$dataArray[0][csf('feeder')]];
							?>&nbsp;</p>
					</td>
					<td><b>Garments Color</b></td>
					<td>
						<p>
							<?
							$color_id_arr = array_unique(explode(",", $dataArray[0][csf('color_id')]));
							$all_color = "";
							foreach ($color_id_arr as $color_id) {
								$all_color .= $color_library[$color_id] . ",";
							}
							$all_color = chop($all_color, ",");
							echo $all_color;

							?>&nbsp;</p>
					</td>
					<td><b>Remarks</b></td>
					<td>
						<p><? echo $dataArray[0][csf('remarks')]; ?>&nbsp;</p>
					</td>
				</tr>
			</table>
			<?

			//$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id");

			$sql_fedder = sql_select("select b.sequence, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0  and a.sequence=b.sequence group by b.sequence, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.color_id,b.sequence asc");
			if (count($sql_fedder) > 0) {
			?>
				<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="200">Color</th>
							<th width="200">Stripe Color</th>
							<th width="120">Measurement</th>
							<th width="120">UOM</th>
							<th>No Of Feeder</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 1;
						$total_feeder = 0;
						foreach ($sql_fedder as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
						?>
							<tr>
								<td align="center">
									<p><? echo $i; ?>&nbsp;</p>
								</td>
								<td>
									<p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p>
								</td>
								<td>
									<p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p>
								</td>
								<td align="right">
									<p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p>
								</td>
								<td align="center">
									<p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p>
								</td>
								<td align="right">
									<p><? printf("%.3f", $row[csf('no_of_feeder')]);
										$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th align="right">Total:</th>
							<th align="right"><? echo number_format($total_feeder, 0); ?></th>
						</tr>
					</tfoot>
				</table>
			<?
			}
			$sql_info = "select coller_cuf_size_planning from variable_settings_production where company_name='$company_id' and variable_list=53 and status_active=1 and is_deleted=0";
			//echo $sql_info;// die;
			$result_dtls = sql_select($sql_info);
			$collarCuff = $result_dtls[0]['COLLER_CUF_SIZE_PLANNING'];

			if ($collarCuff == 1) {

				$sql_fedder = sql_select("select a.id,c.plan_id,c.program_no, c.color_id,c.body_part_id,c.grey_size_id as grey_or_gmts_size, c.finish_size_id, a.stripe_color_id, min(b.measurement) as measurement
			from ppl_planning_feeder_dtls a, wo_pre_stripe_color b,ppl_size_wise_break_down c
			where   a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.mst_id=c.plan_id and a.dtls_id=c.program_no and c.color_id=a.color_id and c.program_no in($program_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.no_of_feeder>0 and b.sales_dtls_id>0  group by a.id,c.plan_id,c.program_no, c.color_id,c.body_part_id,c.grey_size_id,c.finish_size_id ,a.stripe_color_id order by a.id, c.color_id asc");


				$size_plan_sql = sql_select("select c.plan_id,c.program_no, c.color_id,c.body_part_id, c.grey_size_id as grey_or_gmts_size, c.finish_size_id, sum(c.current_qty) as current_qty from ppl_size_wise_break_down c where c.program_no in($program_id) group by  c.plan_id,c.program_no, c.color_id,c.body_part_id, c.grey_size_id, c.finish_size_id");
			} else {

				$sql_fedder = sql_select("select a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, min(b.measurement) as measurement,c.body_part_id,c.finish_size as finish_size_id from ppl_planning_feeder_dtls a, wo_pre_stripe_color b,ppl_color_wise_break_down d ,ppl_planning_collar_cuff_dtls c where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.mst_id=d.plan_id and a.dtls_id=d.program_no and d.color_id=a.color_id and d.program_no=c.dtls_id and d.plan_id=c.mst_id and c.dtls_id in($program_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and a.dtls_id in($program_id) and a.no_of_feeder>0 and b.sales_dtls_id>0 group by a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,d.color_id,c.body_part_id,c.finish_size order by a.id, d.color_id");

				$size_plan_sql = sql_select("select c.mst_id as plan_id,c.dtls_id as program_no, c.body_part_id, c.grey_size as grey_or_gmts_size, c.finish_size as finish_size_id, sum(c.qty_pcs) as current_qty,b.color_id
			from ppl_color_wise_break_down b ,ppl_planning_collar_cuff_dtls c
			where   b.program_no=c.dtls_id and b.plan_id=c.mst_id and c.dtls_id in($program_id)
			group by   c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,c.grey_size, b.color_id
			order by b.color_id asc");


				//select c.mst_id as plan_id,c.dtls_id as program_no, c.body_part_id,c.finish_size as finish_size_id, sum(c.qty_pcs) as current_qty,b.color_id from ppl_color_wise_break_down b left join ppl_planning_collar_cuff_dtls c on  b.program_no=c.dtls_id and b.plan_id=c.mst_id where c.dtls_id in(17846) group by   c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,b.color_id order by b.color_id asc

			}

			$qntyArry = array();
			foreach ($size_plan_sql as $rowData) {
				if (!in_array($rowData[csf('current_qty')], $current_qty_duplicate_chk)) {
					$current_qty_duplicate_chk[] = $rowData[csf('current_qty')];
					$qntyArry[$rowData[csf('body_part_id')]][$rowData[csf('color_id')]][$rowData[csf('finish_size_id')]] += $rowData[csf('current_qty')];
				}
			}
			/*echo "<pre>";
		print_r($mainDataArry);
		echo "</pre>";*/

			$plan_color_type_array = return_library_array("select dtls_id, color_type_id from PPL_PLANNING_ENTRY_PLAN_DTLS where dtls_id in($program_id) group by dtls_id,color_type_id", "dtls_id", "color_type_id");

			$color_type_id = $plan_color_type_array[$program_id];


			/*$sql_fedder = sql_select("select a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, min(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.dtls_id in($program_ids)  and a.no_of_feeder>0 group by a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id order by a.id");*/

			if (count($sql_fedder) > 0) {
				foreach ($sql_fedder as $row) {
					$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['stripe_color_id'] = $row[csf('stripe_color_id')];
					$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['measurement'] = $row[csf('measurement')];
					$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['body_part_id'] = $row[csf('body_part_id')];
					$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['finish_size_id'] = $row[csf('finish_size_id')];

					$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['grey_or_gmts_size'] = $row[csf('grey_or_gmts_size')];
				}
			}


			$bodypart_library = return_library_array("select id,body_part_full_name from lib_body_part", "id", "body_part_full_name");
			$size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");

			foreach ($arrMeasurement as $planId => $planData) {
				foreach ($planData as $progNo => $progData) {
					foreach ($progData as $body_part_id => $body_part_idData) {
						foreach ($body_part_idData as $finish_size_id => $finish_size_idData) {
							foreach ($finish_size_idData as $color_ids => $color_idData) {
								foreach ($color_idData as $strp_color_ids => $strip_colorData) {
									foreach ($strip_colorData as $measurementNo => $rows) {
										$bodyPartCountArr[$body_part_id] += 1;
										$colorIdsCountArr[$color_ids] += 1;
										$finishSizeCountArr[$finish_size_id] += 1;
									}
								}
							}
						}
					}
				}
			}



			?>
			<table style="margin-top:10px;" width="700" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">

				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="150">Body Part</th>
						<th width="150">GMTS Color</th>
						<th width="100">Stripe Color</th>
						<th width="50">Measurement</th>
						<th width="50">Grey/Gmts Size </th>
						<th width="50">Finish Size </th>
						<th>Quantity Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					if ($color_type_id == 2 || $color_type_id == 3 || $color_type_id == 4) {
						foreach ($arrMeasurement as $planId => $planData) {
							foreach ($planData as $progNo => $progData) {
								foreach ($progData as $body_part_id => $body_part_idData) {
									foreach ($body_part_idData as $finish_size_id => $finish_size_idData) {
										foreach ($finish_size_idData as $color_ids => $color_idData) {
											foreach ($color_idData as $strp_color_ids => $strip_colorData) {
												foreach ($strip_colorData as $measurementNo => $rows) {
													$bodyPart_span = $bodyPartCountArr[$body_part_id]++;
													$colorIds_span = $colorIdsCountArr[$color_ids]++;
													$finishSize_span = $finishSizeCountArr[$finish_size_id]++;
					?>

													<tr>
														<?
														if (!in_array($body_part_id, $body_part_id_chk)) {
															$body_part_id_chk[] = $body_part_id;
														?>
															<td align="center" rowspan="<? echo $bodyPart_span; ?>">
																<p><? echo $i; ?>&nbsp;</p>
															</td>
															<td align="center" rowspan="<? echo $bodyPart_span; ?>"><? echo $bodypart_library[$body_part_id]; ?></td>
														<?
														}

														if (!in_array($color_ids, $color_id_chk)) {
															$color_id_chk[] = $color_ids;
														?>
															<td align="center" rowspan="<? echo $colorIds_span; ?>">
																<p><? echo $color_library[$color_ids]; ?>&nbsp;</p>
															</td>
														<?
														}
														?>
														<td align="center">
															<p><? echo $color_library[$strp_color_ids]; ?>&nbsp;</p>
														</td>
														<td align="right">
															<p><? echo number_format($measurementNo, 2); ?>&nbsp;</p>
														</td>
														<?
														if (!in_array($finish_size_id, $finishSize_chk)) {
															$finishSize_chk[] = $finish_size_id;
														?>
															<td align="right" rowspan="<? echo $finishSize_span; ?>">
																<p><? echo $size_library[$rows['grey_or_gmts_size']]; ?>rwerwerwer&nbsp;</p>
															</td>
															<td align="right" rowspan="<? echo $finishSize_span; ?>">
																<p><? echo $finish_size_id; ?>&nbsp;</p>
															</td>
															<td align="right" rowspan="<? echo $finishSize_span; ?>">
																<p><? echo $qntyArry[$body_part_id][$color_ids][$finish_size_id]; ?>&nbsp;</p>
															</td>
														<?
														}
														?>
													</tr>
							<?
													$i++;
												}
											}
										}
									}
								}
							}
						}
					} else {
						foreach ($size_plan_sql as $rows) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							//$stripeColor=$arrMeasurement[$rows[csf('plan_id')]][$rows[csf('program_no')]][$rows[csf('color_id')]]['stripe_color_id'];
							//$measurQnty=$arrMeasurement[$rows[csf('plan_id')]][$rows[csf('program_no')]][$rows[csf('color_id')]]['measurement'];

							if ($collarCuff == 1) {
								$grey_or_gmts_size = $size_library[$rows[csf('grey_or_gmts_size')]];
							} else {
								$grey_or_gmts_size = $rows[csf('grey_or_gmts_size')];
							}

							?>
							<tr>
								<td align="center">
									<p><? echo $i; ?>&nbsp;</p>
								</td>
								<td align="center">
									<p><? echo $bodypart_library[$rows[csf('body_part_id')]];; ?></p>
								</td>
								<td align="center">
									<p><? echo $color_library[$rows[csf('color_id')]]; ?>&nbsp;</p>
								</td>
								<td align="center">
									<p></p>
								</td>
								<td align="right">
									<p></p>
								</td>
								<td align="right">
									<p><? echo $grey_or_gmts_size; ?></p>
								</td>
								<td align="right">
									<p><? echo $rows[csf('finish_size_id')]; ?></p>
								</td>
								<td align="right">
									<p><? echo number_format($rows[csf('current_qty')], 2); ?>&nbsp;</p>
								</td>
							</tr>
					<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>


			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<tr>
					<td colspan="4" style="word-wrap:break-word"><b>Advice:</b> <strong><? echo $advice; ?></strong>
					</td>
				</tr>
			</table>

			<table width="850" style="display:none">
				<tr>
					<td width="100%" height="90" colspan="4"></td>
				</tr>
				<tr>
					<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Receive By</strong></td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
					</td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
				</tr>
			</table>
			<br>

			<?
			$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$program_id' and form_name='Planning Info Entry For Sales Order' and is_deleted=0"); ?>
			<? if (count($image_location) > 0) { ?>
				<div style="width:850px">
					<div style="width:850px;margin-top:10px">

						<img src="<? echo $path . $image_location; ?>" height='100%' width='100%' />
					</div>
				</div>
			<? }
			echo signature_table(100, $company_id, "850px", "", "50"); ?>
		</div>
	</div>
<?
	exit();
}

if ($action == "print_popup") {
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	//echo $company_id;die;

	$company_details = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", "id", "machine_no");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$sales_info = sql_select("select a.job_no, a.style_ref_no, a.buyer_id, a.booking_without_order from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b where a.id=b.po_id and b.dtls_id = $program_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

?>
	<style type="text/css">
		table tr td,
		table tr th {
			font-size: 13px;
		}
	</style>
	<div style="width:860px">
		<div style="margin-left:20px; width:850px">
			<div style="width:100px;float:left;position:relative;margin-top:10px">
				<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
				<img src="../../<? echo $image_location; ?>" height='100%' width='100%' />
			</div>
			<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
			<div style="float:left;position:relative;">
				<table width="100%" style="margin-top:10px">
					<tr>
						<td align="center" style="font-size:16px;">
							<? echo $company_details[$company_id]; ?>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px">
							<?
							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result) {
							?>
								Plot No: <? echo $result['plot_no']; ?>
								Level No: <? echo $result['level_no'] ?>
								Road No: <? echo $result['road_no']; ?>
								Block No: <? echo $result['block_no']; ?>
								City No: <? echo $result['city']; ?>
								Zip Code: <? echo $result['zip_code']; ?>
								Country: <? echo $country_arr[$result['country_id']]; ?><br>
								Email Address: <? echo $result['email']; ?>
								Website No: <? echo $result['website'];
										}
											?>
						</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
					</tr>
				</table>
			</div>
		</div>
		<div style="margin-left:20px;float:left; width:850px;">
			<?
			$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id,fabric_dia from ppl_planning_info_entry_dtls where id=$program_id and status_active=1 and is_deleted=0");

			$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");
			$advice = $dataArray[0][csf('advice')];

			$mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia, within_group from ppl_planning_info_entry_mst where status_active=1 and is_deleted=0 and id=" . $dataArray[0][csf('mst_id')]);
			$booking_no = $mst_dataArray[0][csf('booking_no')];
			$buyer_id = $mst_dataArray[0][csf('buyer_id')];
			$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
			$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
			$dia = $mst_dataArray[0][csf('dia')];
			$within_group = $mst_dataArray[0][csf('within_group')];

			?>
			&nbsp;&nbsp;<b>Attention- Knitting Manager</b>
			<table width="100%" style="margin-top:20px" cellspacing="7">
				<tr>
					<td width="140"><b>Program No:</b></td>
					<td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="170"><b>Program Date:</b></td>
					<td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
				</tr>
				<tr>
					<td><b>Factory:</b></td>
					<td>
						<?

						if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
						else if ($dataArray[0][csf('knitting_source')] == 3) echo $supllier_arr[$dataArray[0][csf('knitting_party')]];
						?>
					</td>
					<td><b>Fabrication & FGSM:</b></td>
					<td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
				</tr>
				<tr>
					<td><b>Address:</b></td>
					<td colspan="3">
						<?
						$address = '';
						if ($dataArray[0][csf('knitting_source')] == 1) {
							$addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result) {
						?>
								Plot No: <? echo $result[csf('plot_no')]; ?>
								Road No: <? echo $result[csf('road_no')]; ?>
								Block No: <? echo $result[csf('block_no')]; ?>
								City No: <? echo $result[csf('city')];
										}
									} else if ($dataArray[0][csf('knitting_source')] == 3) {
										$address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
										echo $address;
									}

									$machine_no = '';
									$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
									foreach ($machine_id as $val) {
										if ($machine_no == '') $machine_no = $machine_arr[$val];
										else $machine_no .= "," . $machine_arr[$val];
									}

									if ($within_group == 1) {
										$buyer = $company_details[$buyer_id];
										if ($sales_info[0][csf('booking_without_order')] != 1) {
											$booking_buyer = return_field_value("buyer_id", "wo_booking_mst", "booking_no='" . $booking_no . "'");
										} else {
											//for sample without order
											$booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
										}
										$customers_buyer = $buyer_arr[$booking_buyer];
									} else {
										$buyer = '';
										$customers_buyer = $buyer_arr[$sales_info[0]["BUYER_ID"]];
									}
											?>
					</td>
				</tr>
				<tr>
					<td><b>PO Company:</b></td>
					<td><b><? echo $buyer; ?></b></td>
					<td><b>Buyer Name:</b></td>
					<td><b><? echo $customers_buyer; ?></b></td>
				</tr>
				<tr>
					<td><b>Sales Order No:</b></td>
					<td><b><? echo $sales_info[0]["JOB_NO"]; ?></b></td>
					<td><b>Sales / Booking No:</b></td>
					<td><b><? echo $booking_no; ?></b></td>
				</tr>
				<tr>
					<td><b>Style Ref.:</b></td>
					<td><b><? echo $sales_info[0]["STYLE_REF_NO"]; ?></b></td>
					<td><b>Location:</b></td>
					<td><b><? echo $location; ?></b></td>
				</tr>
				<tr>
					<td><b>Machine No:</b></td>
					<td><b><? echo $machine_no; ?></b></td>
				</tr>
			</table>

			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="70">Requisition No</th>
					<th width="70">Lot No</th>
					<th width="220">Yarn Description</th>
					<th width="110">Brand</th>
					<th width="80">Requisition Qnty</th>
					<th width="120">Yarn Color</th>
					<th>Remarks</th>
				</thead>
				<?
				$i = 1;
				$tot_reqsn_qnty = 0;
				$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
				$nameArray = sql_select($sql);
				foreach ($nameArray as $selectResult) {
				?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td align="center">
							<p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p>
						</td>
						<td align="center">
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
								&nbsp;</p>
						</td>
						<td>
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
								&nbsp;</p>
						</td>
						<td>
							<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p>
						</td>
						<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
						<td>
							<p>
								&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
						</td>
						<td>&nbsp;</td>
					</tr>
				<?
					$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
					$i++;
				}
				?>
				<tfoot>
					<th colspan="5" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
			<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;" class="rpt_table">
				<tr>
					<td width="120"><b>Colour Range:</b></td>
					<td width="150">
						<p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p>
					</td>
					<td width="120"><b>GGSM OR S/L:</b></td>
					<td width="150">
						<p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p>
					</td>
					<td width="120"><b>FGSM:</b></td>
					<td>
						<p><? echo $gsm_weight; ?>&nbsp;</p>
					</td>
				</tr>
				<tr>
					<td><b>Finish Dia</b></td>
					<td>
						<p><? echo $dataArray[0][csf('fabric_dia')] . "  (" . $fabric_typee[$dataArray[0][csf('width_dia_type')]] . ")"; ?>
							&nbsp;</p>
					</td>
					<td><b>Machine Dia & Gauge:</b></td>
					<td>
						<p><? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?>
							&nbsp;</p>
					</td>
					<td><b>Program Qnty:</b></td>
					<td>
						<p><? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;</p>
					</td>
				</tr>
				<tr>
					<td><b>Feeder:</b></td>
					<td>
						<p>
							<?
							$feeder_array = array(1 => "Full Feeder", 2 => "Half Feeder");
							echo $feeder_array[$dataArray[0][csf('feeder')]];
							?>&nbsp;</p>
					</td>
					<td><b>Garments Color</b></td>
					<td>
						<p>
							<?
							$color_id_arr = array_unique(explode(",", $dataArray[0][csf('color_id')]));
							$all_color = "";
							foreach ($color_id_arr as $color_id) {
								$all_color .= $color_library[$color_id] . ",";
							}
							$all_color = chop($all_color, ",");
							echo $all_color;

							?>&nbsp;</p>
					</td>
					<td><b>Remarks</b></td>
					<td>
						<p><? echo $dataArray[0][csf('remarks')]; ?>&nbsp;</p>
					</td>
				</tr>
			</table>
			<?

			if ($program_id != "") {
				$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder");
			}

			if (count($sql_fedder) > 0) {
			?>
				<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="200">Color</th>
							<th width="200">Stripe Color</th>
							<th width="120">Measurement</th>
							<th width="120">UOM</th>
							<th>No Of Feeder</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 1;
						$total_feeder = 0;
						foreach ($sql_fedder as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
						?>
							<tr>
								<td align="center">
									<p><? echo $i; ?>&nbsp;</p>
								</td>
								<td>
									<p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p>
								</td>
								<td>
									<p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p>
								</td>
								<td align="right">
									<p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p>
								</td>
								<td align="center">
									<p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p>
								</td>
								<td align="right">
									<p><? echo number_format($row[csf('no_of_feeder')], 0);
										$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th align="right">Total:</th>
							<th align="right"><? echo number_format($total_feeder, 0); ?></th>
						</tr>
					</tfoot>
				</table>
			<?
			}
			?>
			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<tr>
					<td colspan="4" style="word-wrap:break-word; font-size: 14px;"><b>Advice:</b>
						<strong><? echo $advice; ?></strong>
					</td>
				</tr>
			</table>

			<table width="850" style="display:none">
				<tr>
					<td width="100%" height="90" colspan="4"></td>
				</tr>
				<tr>
					<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Receive By</strong></td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
					</td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
				</tr>
			</table>
			<br>

			<?
			$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$program_id' and form_name='Planning Info Entry For Sales Order' and is_deleted=0"); ?>
			<? if (count($image_location) > 0) { ?>
				<div style="width:850px">
					<div style="width:850px;margin-top:10px">

						<img src="<? echo base_url($image_location); ?>" height='100%' width='100%' />
					</div>
				</div>
			<? }
			echo signature_table(100, $company_id, "850px"); ?>
		</div>
	</div>
<?
	exit();
}

if ($action == "yarn_req_qnty_popup") {
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		var permission = '<? echo $permission; ?>';

		function calculate(field_id) {
			var txt_no_of_cone = $('#txt_no_of_cone').val() * 1;
			var txt_weight_per_cone = $('#txt_weight_per_cone').val() * 1;
			var txt_yarn_qnty = $('#txt_yarn_qnty').val() * 1;

			if (field_id == "txt_yarn_qnty") {
				if (txt_no_of_cone > 0) {
					var weightPerCone = txt_yarn_qnty / txt_no_of_cone;
					$('#txt_weight_per_cone').val(weightPerCone.toFixed(2));
				} else {
					$('#txt_weight_per_cone').val('');
				}
			} else {
				if (txt_weight_per_cone == "" && txt_yarn_qnty != "") {
					if (txt_no_of_cone > 0) {
						var weightPerCone = txt_yarn_qnty / txt_no_of_cone;
						$('#txt_weight_per_cone').val(weightPerCone.toFixed(2));
					} else {
						$('#txt_weight_per_cone').val('');
					}
				} else {
					var yarnQnty = txt_no_of_cone * txt_weight_per_cone;
					$('#txt_yarn_qnty').val(yarnQnty);
				}
			}
		}

		function openpage_lot() {
			var page_link = "yarn_requisition_entry_sales_controller.php?action=lot_info_popup&companyID=<? echo $companyID; ?>" +
				"&knit_dtlsId=<? echo $knit_dtlsId; ?>" + "&comps=" + '<? echo $comps; ?>' + "&job_no=" + '<? echo $job_no; ?>';
			var title = 'Lot Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];
				var prod_id = this.contentDoc.getElementById("hidden_prod_id").value;
				var data = this.contentDoc.getElementById("hidden_data").value.split("**");
				$('#prod_id').val(prod_id);
				$('#txt_lot').val(data[0]);
				$('#cbo_yarn_count').val(data[1]);
				$('#cbo_yarn_type').val(data[2]);
				$('#txt_color').val(data[3]);
				$('#txt_composition').val(data[4]);
				$('#txt_available_qty').val(data[5]);
				$('#hidden_lot_available_qnty').val(data[5]);
				$('#hidden_dyed_type').val(data[6]);
			}
		}

		function fnc_yarn_req_entry(operation) {
			var hidden_lot_available_qnty = $('#hidden_lot_available_qnty').val() * 1;
			var txt_yarn_qnty = $('#txt_yarn_qnty').val() * 1;


			if (txt_yarn_qnty > hidden_lot_available_qnty) {
				alert("Requisition Quantity is not Available");
				return;
			}

			if (operation == 0) {
				if (form_validation('txt_lot*txt_yarn_qnty*txt_reqs_date', 'Lot*Yarn Qnty*Requisition Date') == false) {
					return;
				}
			} else if (operation == 1) {
				if (form_validation('txt_lot*txt_reqs_date', 'Lot*Requisition Date') == false) {
					return;
				}
			}

			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('prod_id*txt_no_of_cone*txt_reqs_date*txt_yarn_qnty*updateId*update_dtls_id*txt_requisition_no*companyID*sale_order_id*txt_within_group*job_no*original_prod_id*original_prod_qnty*hidden_dyed_type', "../../");

			freeze_window(operation);

			http.open("POST", "yarn_requisition_entry_sales_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_req_entry_Reply_info;
		}

		function fnc_yarn_req_entry_Reply_info() {
			if (http.readyState == 4) {
				var reponse = trim(http.responseText).split('**');

				if (reponse[0] == 11) {
					alert("Duplicate Item Not Allowed");
				} else if (reponse[0] == 17) {
					alert(reponse[1]);
				} else if (reponse[0] == 18) {
					alert(reponse[1]);
				} else {
					show_msg(reponse[0]);

					if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)) {
						reset_form('yarnReqQnty_1', '', '', '', '', 'updateId*companyID*sale_order_id*txt_within_group*job_no');
						$('#txt_requisition_no').val(reponse[3]);
						$('#hide_req_no').val(reponse[3]);
						show_list_view(reponse[1], 'requisition_info_details', 'list_view', 'yarn_requisition_entry_sales_controller', '');
					}
				}

				set_button_status(reponse[2], permission, 'fnc_yarn_req_entry', 1);
				release_freezing();

				var today = new Date();
				var date = ('0' + today.getDate()).slice(-2) + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + today.getFullYear();
				document.getElementById('txt_reqs_date').value = date;
			}

		}

		function fnc_close() {
			//$yarn_req_qnty=return_field_value( "sum(yarn_qnty) as yarn_qnty","ppl_yarn_requisition_entry", "knit_id=$knit_dtlsId","yarn_qnty");
			//$('#hidden_yarn_req_qnty').val( //echo $yarn_req_qnty; );
			parent.emailwindow.hide();
		}

		//generate_report2(".$row[csf('company_id')].",".$row[csf('id')].")

		function generate_report_print2(company_id, program_id, within_group) {
			var req_no = $('#hide_req_no').val();
			if (req_no != "") {
				//print_report(company_id + '*' + program_id, "print_popup", "yarn_requisition_entry_sales_controller");
				var path = '';
				print_report(program_id + '**0**' + path + '**' + within_group, "requisition_print_two", "../reports/requires/knitting_status_report_sales_controller");
			} else {
				alert("Save Data First");
				return;
			}
		}

		function generate_report_print(company_id, program_id) {
			var req_no = $('#hide_req_no').val();
			if (req_no != "") {
				print_report(company_id + '*' + program_id, "print_popup", "yarn_requisition_entry_sales_controller");
			} else {
				alert("Save Data First");
				return;
			}
		}

		function generate_report_print3(company_id, program_id) {
			//var cbo_template_id = $('#cbo_template_id').val();
			var req_no = $('#hide_req_no').val();
			if (req_no != "") {
				print_report(company_id + '*' + program_id + '*' + 'btnClick', "requisition_print3", "yarn_requisition_entry_sales_controller");
			} else {
				alert("Save Data First");
				return;
			}
		}
	</script>

	</head>

	<body>
		<div align="center">
			<div><? echo load_freeze_divs("../../", $permission, 1); ?></div>
			<form name="yarnReqQnty_1" id="yarnReqQnty_1">
				<fieldset style="width:950px !important; margin-top:10px">
					<legend>New Entry</legend>
					<table width="900" align="center" border="0">
						<tr>
							<td colspan="4" align="right"><strong>Requisition No</strong></td>
							<td colspan="4" align="left">
								<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:130px;" placeholder="Display" disabled />
								<input type="hidden" name="hide_req_no" id="hide_req_no" class="text_boxes" value="<? echo $reqs_no; ?>" />
							</td>
						</tr>
						<tr>
							<td class="must_entry_caption">Lot</td>
							<td>
								<input type="text" name="txt_lot" id="txt_lot" class="text_boxes" placeholder="Double Click" style="width:130px;" onDblClick="openpage_lot();" readonly />
								<input type="hidden" name="prod_id" id="prod_id" class="text_boxes" readonly />
								<input type="hidden" name="hidden_dyed_type" id="hidden_dyed_type" class="text_boxes" readonly /><input type="hidden" name="original_prod_id" id="original_prod_id" class="text_boxes" readonly />
								<input type="hidden" name="original_prod_qnty" id="original_prod_qnty" class="text_boxes" readonly />
								<input type="hidden" name="hidden_yarn_req_qnty" id="hidden_yarn_req_qnty" class="text_boxes" readonly />
								<input type="hidden" name="hidden_lot_available_qnty" id="hidden_lot_available_qnty" readonly />
								<input type="hidden" name="companyID" id="companyID" value="<?php echo $companyID; ?>" readonly />
							</td>
							<td>Yarn Count</td>
							<td>
								<?
								echo create_drop_down("cbo_yarn_count", 142, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "Display", 0, "", 1);
								?>
							</td>
							<td>Yarn Type</td>
							<td>
								<?
								echo create_drop_down("cbo_yarn_type", 142, $yarn_type, "", 1, "Display", 0, "", 1);
								?>
							</td>

							<td>Color</td>
							<td>
								<input type="text" name="txt_color" id="txt_color" class="text_boxes" placeholder="Display" style="width:130px;" disabled />
							</td>
						</tr>
						<tr>
							<td>Composition</td>
							<td colspan="3">
								<input type="text" name="txt_composition" id="txt_composition" class="text_boxes" placeholder="Display" style="width:372px;" disabled />
							</td>
							<td class="must_entry_caption">Yarn Reqs. Qnty</td>
							<td>
								<input type="text" name="txt_yarn_qnty" id="txt_yarn_qnty" class="text_boxes_numeric" style="width:130px;" />
							</td>

							<td>No of Cone</td>
							<td>
								<input type="text" name="txt_no_of_cone" id="txt_no_of_cone" class="text_boxes_numeric" style="width:130px;" />
							</td>

						</tr>
						<tr>
							<td>Available Req.</td>
							<td>
								<input type="text" name="txt_available_qty" id="txt_available_qty" class="text_boxes_numeric" style="width:130px;" readonly />
							</td>
							<td class="must_entry_caption">Requisition Date</td>
							<td>
								<input type="text" name="txt_reqs_date" id="txt_reqs_date" class="datepicker" style="width:130px;" value="<? echo date("d-m-Y"); ?>" />
							</td>
							<td>Program Qnty : </td>
							<td>
								<input type="text" style="width:130px; text-align:center" value="<?php echo $prog_qnty; ?>" class="text_boxes_numeric" readonly />
							</td>
						</tr>
						<tr>
							<td colspan="8">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="8" align="center" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_yarn_req_entry", 0, 0, "reset_form('yarnReqQnty_1','','','','','updateId');", 1);
								?>
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />

								<?php
								$print_report_format = 0;
								$print_report_format = return_field_value("format_id", "lib_report_template", "template_name in (" . $companyID . ") and module_id=4 and report_id=269 and is_deleted=0 and status_active=1");
								$print_reports = explode(",", $print_report_format);
								// print_r($print_reports);
								$buttonHtml = '';
								foreach ($print_reports as $id) {
									if ($id == 78) {
										$company_id  = str_replace("'", '', $company_id);
										$knit_id = str_replace("'", '', $knit_id);
										$buttonHtml .= '<input type="button" name="btn_print" class="formbutton" value="Print" id="btn_print" onClick="generate_report_print(' . $company_id . ',' . $knit_id . ')" style="width:100px"/>';
									}

									if ($id == 84) {
										$companyID = str_replace("'", '', $companyID);
										$knit_dtlsId = str_replace("'", '', $knit_dtlsId);
										$cbo_within_group = str_replace("'", '', $cbo_within_group);
										$buttonHtml .= '<input type="button" name="btn_print" class="formbutton" value="Print2" id="btn_print" onClick="generate_report_print2(' . $companyID . ',' . $knit_dtlsId . ',' . $cbo_within_group . ')" style="width:100px"/>';
									}
									if ($id == 85) {
										$companyID = str_replace("'", '', $companyID);
										$knit_dtlsId = str_replace("'", '', $knit_dtlsId);
										$buttonHtml .= '<input type="button" name="btn_print_3" class="formbutton" value="Print3" id="btn_print_3" onClick="generate_report_print3(' . $companyID . ',' . $knit_dtlsId . ')" style="width:100px"/>';
									}
								}
								echo $buttonHtml;
								?>
								<input type="hidden" name="updateId" id="updateId" class="text_boxes" value="<? echo str_replace("'", '', $knit_dtlsId); ?>">
								<input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
								<input type="hidden" name="sale_order_id" id="sale_order_id" value="<? echo str_replace("'", '', $sale_order_id); ?>">
								<input type="hidden" name="txt_within_group" id="txt_within_group" value="<? echo str_replace("'", '', $cbo_within_group); ?>">
								<input type="hidden" name="job_no" id="job_no" value="<? echo str_replace("'", '', $job_no); ?>">
							</td>

							<span id="button_data_panelsss"></span>

						</tr>
					</table>
				</fieldset>
				<div id="list_view" style="margin-top:10px">
					<?
					if (str_replace("'", '', $knit_dtlsId) != "") {
					?>
						<script>
							show_list_view('<? echo str_replace("'", '', $knit_dtlsId); ?>', 'requisition_info_details', 'list_view', 'yarn_requisition_entry_sales_controller', '');
						</script>
					<?
					}
					?>
				</div>
				<br>
				<div>
					<?
					if (str_replace("'", '', $knit_dtlsId) != "") {
						$program_sql = sql_select("SELECT a.color_id, b.determination_id, b.body_part_id, a.color_range, b.gsm_weight, b.color_type_id
					from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and dtls_id=$knit_dtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

						foreach ($program_sql as $row) {
							$color_id = $row[csf("color_id")];
							$determination_id = $row[csf("determination_id")];
							$body_part_id = $row[csf("body_part_id")];
							$color_range_id = $row[csf("color_range")];
							$gsm_weight = $row[csf("gsm_weight")];
							$color_type_id = $row[csf("color_type_id")];
						}

						$fso_yarn_sql = sql_select("SELECT b.composition_id, b.cons_ratio
					from fabric_sales_order_yarn a, fabric_sales_order_yarn_dtls b
					where a.id=b.yarn_dtls_id and a.mst_id=$sale_order_id and a.deter_id=$determination_id and a.gsm=$gsm_weight and a.color_range_id=$color_range_id group by b.composition_id, b.cons_ratio"); //and a.color_range_id=$color_range_id

						foreach ($fso_yarn_sql as $row) {
							$fso_comp_ratio[$row[csf("composition_id")] . '*' . $row[csf("cons_ratio")]] = $row[csf("cons_ratio")];
							$prog_wise_without_stripe_td_span[$prog_qnty]++;
						}

						$stripe_color_types = array("2" => "2", "7" => "7", "34" => "34", "44" => "44", "47" => "47", "48" => "48", "65" => "65", "74" => "74", "82" => "82", "84" => "84", "91" => "91");
						if ($stripe_color_types[$color_type_id]) {

							$stripe_arr = sql_select("SELECT a.stripe_color, a.measurement from wo_pre_stripe_color a, fabric_sales_order_dtls b where a.sales_dtls_id=b.id and a.job_no=b.job_no_mst and a.job_no='$job_no' and b.color_id=$color_id and b.determination_id=$determination_id and b.body_part_id=$body_part_id");

							foreach ($stripe_arr as $row) {
								$total_measurement += $row[csf("measurement")];
								foreach ($fso_comp_ratio as $key => $val) {
									$stripe_comp_ratio_arr[$prog_qnty][$row[csf('stripe_color')]][$row[csf('measurement')]][$key] = $val;

									$prog_wise_td_span[$prog_qnty]++;
									$color_wise_td_span[$prog_qnty][$row[csf('stripe_color')]]++;
								}
							}

					?>
							<table width="520" border="1" rules="all" class="rpt_table" align="left">
								<thead>
									<th width="100">P. Qnty</th>
									<th width="120">Stripe Color</th>
									<th width="100">Ttl. Mes.</th>
									<th width="100">Dis Mes.</th>
									<th width="100">Yarn Qty</th>
									<th width="100">Composition</th>
									<th width="100">Con Ratio</th>
									<th width="100">dis. Qty</th>
								</thead>
							</table>
							<div style="width:540px; overflow-y:scroll; max-height:300px; float:left" id="scroll_body" align="left">
								<table class="rpt_table" rules="all" border="1" width="520" id="tbl_list_search" align="left">
									<?
									$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
									$i = 1;
									foreach ($stripe_comp_ratio_arr as $progQty => $progQtyData) {
										foreach ($progQtyData as $stripeColor => $stripeColorData) {
											$j = 1;
											foreach ($stripeColorData as $measure => $measureData) {
												foreach ($measureData as $compRatio => $row) {
													$comRatioArr = explode("*", $compRatio);
									?>
													<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
														<?
														if ($i == 1) {
														?>
															<td width="100" align="center" rowspan="<? echo $prog_wise_td_span[$progQty]; ?>"><? echo $progQty; ?></td>
														<?
														}
														if ($j == 1) {
														?>
															<td width="120" rowspan="<? echo $color_wise_td_span[$progQty][$stripeColor]; ?>"><? echo $color_library[$stripeColor]; ?></td>
														<?
														}
														if ($i == 1) {
														?>
															<td width="100" align="center" rowspan="<? echo $prog_wise_td_span[$progQty]; ?>"><? echo $total_measurement; ?></td>
														<?
														}

														if ($j == 1) {
														?>
															<td width="100" rowspan="<? echo $color_wise_td_span[$progQty][$stripeColor]; ?>"><? echo $measure; ?></td>
															<td width="100" rowspan="<? echo $color_wise_td_span[$progQty][$stripeColor]; ?>">
																<?
																$grey_qty = ($measure * $progQty) / $total_measurement;
																echo number_format($grey_qty, 2);
																?>
															</td>
														<?
														}
														?>

														<td width="100" align="center"><? echo $composition[$comRatioArr[0]]; ?></td>
														<td width="100" align="center"><? echo $comRatioArr[1]; ?></td>
														<td width="100" align="center">
															<?
															$yarn_qty = $grey_qty * $comRatioArr[1] / 100;
															echo number_format($yarn_qty, 2);
															?>
														</td>

										<?
													$i++;
													$j++;
												}
											}
										}
									}
										?>
								</table>
							</div>
						<?
						}

						?>
						<table width="420" border="1" rules="all" class="rpt_table" align="left">
							<thead>
								<th width="100">P. Qnty</th>
								<th width="120">Composition</th>
								<th width="100">Con Ratio</th>
								<th width="100">dis. Qty</th>
							</thead>
							<?
							$i = 1;
							foreach ($fso_comp_ratio as $compRatio => $val) {
								$comRatioArr = explode("*", $compRatio);

							?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<?
									if ($i == 1) {
									?>
										<td width="100" align="center" rowspan="<? echo $prog_wise_without_stripe_td_span[$prog_qnty]; ?>"><? echo $prog_qnty; ?></td>
									<?
									}
									?>
									<td width="100" align="center"><? echo $composition[$comRatioArr[0]]; ?></td>
									<td width="100" align="center"><? echo $comRatioArr[1]; ?></td>
									<td width="100" align="center">
										<?
										$yarn_qty = $prog_qnty * $comRatioArr[1] / 100;
										echo number_format($yarn_qty, 2);
										?>
									</td>
								</tr>
							<?
								$i++;
							}
							?>

						</table>
					<?

					}
					?>
				</div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}
if ($action == "requisition_print3") {
	extract($_REQUEST);

	$data = explode('*', $data);

	$company_id = $data[0];
	$program_ids = $data[1];
	$from_page_source = $data[2];
	if ($from_page_source == 'hyperLink') {
		$hperLinkOrBtn = '../';
	} else {
		$hperLinkOrBtn = '../../';
	}

	echo load_html_head_contents("Program Qnty Info", $hperLinkOrBtn, 1, 1, '', '', '');

	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_details = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");


	//for sales order information
	$po_dataArray = sql_select("SELECT ID, JOB_NO, BUYER_ID, STYLE_REF_NO, WITHIN_GROUP, SALES_BOOKING_NO, BOOKING_WITHOUT_ORDER FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(" . $program_ids . ") AND STATUS_ACTIVE=1 AND IS_DELETED=0)");
	foreach ($po_dataArray as $row) {
		$sales_array[$row['ID']]['no'] = $row['JOB_NO'];
		$sales_array[$row['ID']]['sales_booking_no'] = $row['SALES_BOOKING_NO'];
		$sales_array[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
		$sales_array[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$sales_array[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
		$sales_array[$row['ID']]['booking_without_order'] = $row['BOOKING_WITHOUT_ORDER'];
	}
	//for booking information
	$booking_shipdate = "";
	$book_dataArray = sql_select("SELECT A.BUYER_ID, B.BOOKING_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.JOB_NO, C.PO_NUMBER, D.STYLE_REF_NO, C.GROUPING,C.SHIPMENT_DATE FROM WO_BOOKING_MST A,WO_BOOKING_DTLS B, WO_PO_BREAK_DOWN C,WO_PO_DETAILS_MASTER D WHERE A.BOOKING_NO=B.BOOKING_NO AND B.PO_BREAK_DOWN_ID=C.ID AND C.JOB_NO_MST=D.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND B.BOOKING_NO IN(SELECT SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID IN(" . $program_ids . ") AND STATUS_ACTIVE=1 AND IS_DELETED=0)) group by A.BUYER_ID, B.BOOKING_NO, B.PO_BREAK_DOWN_ID, B.JOB_NO, C.PO_NUMBER, D.STYLE_REF_NO, C.GROUPING,C.SHIPMENT_DATE");
	foreach ($book_dataArray as $row) {
		$booking_array[$row['BOOKING_NO']]['booking_no'] = $row['BOOKING_NO'];
		$booking_array[$row['BOOKING_NO']]['po_id'] = $row['PO_ID'];
		$booking_array[$row['BOOKING_NO']]['buyer_id'] = $row['BUYER_ID'];
		$booking_array[$row['BOOKING_NO']]['po_no'] = $row['PO_NUMBER'];
		$booking_array[$row['BOOKING_NO']]['job_no'] = $row['JOB_NO'];
		$booking_array[$row['BOOKING_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$booking_array[$row['BOOKING_NO']]['internal_ref'] = $row['GROUPING'];
		$booking_shipdate .= change_date_format($row['SHIPMENT_DATE']) . ",";
	}
	//echo $booking_shipdate; die;
	$machine_arr = array();
	$sql_mc = sql_select("select id, machine_no, floor_id from lib_machine_name");
	foreach ($sql_mc as $row) {
		$machine_arr[$row[csf('id')]]['machine_no'] = $row[csf('machine_no')];
		$machine_arr[$row[csf('id')]]['floor_id'] = $row[csf('floor_id')];
	}
	unset($sql_mc);

	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	}

	$po_ids = implode(",", $plan_details_array);
	if ($po_ids != "") {
		$po_dataArray = sql_select("select id, po_number, job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)");
		foreach ($po_dataArray as $row) {
			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		}
	}

	//$po_dataArray = sql_select("select id, grouping, file_no, po_number, job_no_mst from wo_po_break_down");
	$po_dataArray = sql_select("select a.id, a.grouping, a.file_no, a.po_number, a.job_no_mst,b.style_ref_no,a.shipment_date  from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no");
	foreach ($po_dataArray as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		$po_array[$row[csf('job_no_mst')]]['shipment_date'] = $row[csf('shipment_date')];
	}
	unset($po_dataArray);

	$knit_id_array = array();
	$prod_id_array = array();
	$rqsn_array = array();
	$prod_idArr = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];

		$prod_idArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
	}

	//for Yarn Description, lot, brand, color
	//for Yarn Description, lot, brand, color
	// $sqlBrand = "SELECT prod_id, brand_id FROM inv_transaction WHERE transaction_type IN(1, 5) AND brand_id >0 AND prod_id IN(".implode(",", $prod_idArr).") ORDER BY id DESC";
	$multi_brand = array();
	$sqlBrand = "SELECT distinct  b.brand, b.lot FROM inv_transaction a JOIN PRODUCT_DETAILS_MASTER b ON a.prod_id = b.id WHERE a.transaction_type IN (1, 5) AND a.brand_id >0 AND b.lot IN ( SELECT c.lot FROM PRODUCT_DETAILS_MASTER c WHERE c.id in (" . implode(",", $prod_idArr) . ") )";
	// echo $sqlBrand; die;
	$resultBrand = sql_select($sqlBrand);
	$brandIdArr = array();
	foreach ($resultBrand as $row) {
		// $brandIdArr[$row[csf('prod_id')]] = $row[csf('brand_id')];
		$multi_brand[$row[csf('lot')]] .= $brand_arr[$row[csf('brand')]] . ",";
	}
	// echo "<pre>";
	// print_r($multi_brand); die;


	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 and id in(" . implode(",", $prod_idArr) . ")";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		foreach ($multi_brand as $key => $val) {
			if ($row[csf('lot')] == $key) {
				// echo $val;
				$product_details_array[$row[csf('id')]]['brand'] = $val;
			}
		}
		// $product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$brandIdArr[$row[csf('id')]]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}
	//end
	// echo "<pre>";
	// print_r($product_details_array);die;

	$order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
	$job_no = '';
	$booking_no = '';
	$company = '';
	if ($db_type == 0) {
		$dataArray = sql_select("SELECT a.id, a.knitting_source, a.knitting_party,a.attention,a.machine_id, a.remarks, a.start_date, a.end_date, a.batch_no,a.feeder, b.buyer_id, a.is_sales, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id,a.program_date,a.location_id,a.inserted_by,a.update_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party,a.attention,a.machine_id,a.remarks, a.start_date, a.end_date, a.batch_no,a.feeder, a.program_date,a.location_id, b.buyer_id, a.is_sales, b.booking_no, b.company_id,a.inserted_by,a.update_date");
	} else {

		$dataArray = sql_select("SELECT a.id, a.knitting_source, a.knitting_party,a.attention,a.machine_id, a.remarks,a.start_date, a.end_date, a.batch_no, a.feeder, b.buyer_id, a.is_sales, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id,a.program_date,a.location_id,a.inserted_by,a.update_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party,a.attention, a.machine_id,a.remarks, a.start_date, a.end_date, a.batch_no,a.feeder, a.program_date,a.location_id, b.buyer_id, a.is_sales, b.booking_no, b.company_id,a.attention,a.inserted_by,a.update_date");
	}

	/* $dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, subcontract_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id,attention,fabric_dia,spandex_stitch_length,start_date,end_date,knitting_pdo,batch_no from ppl_planning_info_entry_dtls where id=$program_id"); */

	$inserted_by = $dataArray[0][csf('inserted_by')];

	foreach ($dataArray as $row) {
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];

			if ($row[csf('knitting_source')] == 1) {
				$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
			} else if ($row[csf('knitting_source')] == 3) {
				$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
			}
		}

		if ($buyer_name == "")
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		if ($booking_no == "")
			$booking_no = $row[csf('booking_no')];
		if ($company == "")
			$company = $company_details[$row[csf('company_id')]];

		$po_id = explode(",", $row[csf('po_id')]);

		/*foreach ($po_id as $val) {
			$order_no .= $po_array[$val]['no'] . ",";
			if ($job_no == "")
				$job_no = $po_array[$val]['job_no'];
		}*/


		//-----------



		$order_nos .= "," . $booking_array2[$row[csf('booking_no')]]['po_no'];
		$is_sales = $row[csf('is_sales')];
		$sales_ids .= $row[csf('po_id')] . ",";
		$k_source = $row[csf('knitting_source')];
		$sup = $row[csf('knitting_party')];
	}
	$sales_id = array_unique(explode(",", $sales_ids));
	$booking_nos = array_unique(explode(",", $booking_no));

	//$order_no = array_unique(explode(",", substr($order_no, 0, -1)));

	$order_buyer = $style_ref_no = $job_no = $order_nos = "";
	foreach ($sales_id as $pid) {
		$sales_order_no .= $sales_array[$pid]['no'] . ",";
		if ($sales_array[$pid]['within_group'] == 2) {
			$order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
			$style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
			$job_no .= "";
			$order_ids .= "";
			$internal_ref .= "";
		} else {
			if ($sales_array[$pid]['booking_without_order'] != 1) {
				$order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
			} else {
				//for sample without order
				$booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='" . $sales_array[$pid]['sales_booking_no'] . "'");
				$order_buyer .= $buyer_arr[$booking_buyer] . ",";
			}
			if ($sales_array[$pid]['booking_without_order'] != 1) {
				$style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
			} else {
				$style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
			}

			$job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
			$order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
			$internal_ref .= $booking_array[$sales_array[$pid]['sales_booking_no']]['internal_ref'] . ",";
			//$shipment_date .= $booking_array[$sales_array[$pid]['sales_booking_no']]['shipment_date'] . ",";
		}
	}
	$sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
	$order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
	$style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
	$job_nos = implode(",", array_unique(explode(",", rtrim($job_no, ","))));
	$booking_noss = implode(",", $booking_nos);
	$shipment_date = rtrim(implode(",", array_unique(explode(",", $booking_shipdate))), ",");



?>
	<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
	<div style="float:left;position:relative; width:845px;">
		<!-- <div style="width:1200px; margin-left:5px"> -->
		<!-- <table width="100%" style="margin-top:10px"> -->
		<table width="75%" style="margin-top:10px; float:left;">
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<p style="word-break:break-all;">

						<?
						$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
						foreach ($nameArray as $result) {
							if ($result[csf('plot_no')]) {
								echo $result[csf('plot_no')] . ', ';
							}
							if ($result[csf('level_no')]) {
								echo $result[csf('level_no')] . ', ';
							}
							if ($result[csf('road_no')]) {
								echo $result[csf('road_no')] . ', ';
							};
							if ($result[csf('block_no')]) {
								echo $result[csf('block_no')] . ', ';
							}
							if ($result[csf('city')]) {
								echo $result[csf('city')] . ', ';
							}
							if ($result[csf('zip_code')]) {
								echo $result[csf('zip_code')] . ', ';
							}
							if ($result[csf('country_id')]) {
								echo $country_arr[$result[csf('country_id')]];
							} ?><br>
							Email Address: <? echo $result[csf('email')]; ?>
							Website No: <? echo $result[csf('website')];
									}

									$machine_no = '';
									$floor_id_all = '';
									$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
									foreach ($machine_id as $val) {
										if ($machine_no == '') $machine_no = $machine_arr[$val]['machine_no'];
										else $machine_no .= "," . $machine_arr[$val]['machine_no'];
										if ($floor_id_all == '') $floor_id_all = $machine_arr[$val]['floor_id'];
										else $floor_id_all .= "," . $machine_arr[$val]['floor_id'];
									}

									$floor_name = "";
									$floor_ids = array_filter(array_unique(explode(",", $floor_id_all)));
									foreach ($floor_ids as $ids) {
										if ($floor_name == '') $floor_name = $floor_arr[$ids];
										else $floor_name .= "," . $floor_arr[$ids];
									}

										?>
					</p>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program - <? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?> </u></b></td>
			</tr>
		</table>
		<table style="width:25%; float:right; margin-top:10px;">
			<tr>
				<td><b>Program No</b></td>
				<td><b>:&nbsp;<? echo $dataArray[0][csf('id')]; ?></b></td>
			</tr>
			<tr>
				<td><b>Program Date</b></td>
				<td><b>:&nbsp; <? echo change_date_format($dataArray[0][csf('program_date')]); ?></b></td>
			</tr>
			<tr>
				<td><b>Job No</b></td>
				<td><b>: <? echo $job_nos; ?></b></td>
			</tr>
			<tr>
				<td><b>Last Update Date Time</b></td>
				<td><b>: <? echo $dataArray[0][csf('update_date')]; ?></b></td>
			</tr>
			<tr>
				<td colspan="2" id="barcode_img_id" align="center"></td>
			</tr>
		</table>

		<div style="margin-left:10px;float:left; width:950px;">
			<?
			$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");

			$mst_dataArray = sql_select("select a.booking_no,a.buyer_id,a.fabric_desc,a.gsm_weight,b.fabric_dia,a.inserted_by from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.id=" . $dataArray[0][csf('id')]);
			$booking_no = $mst_dataArray[0][csf('booking_no')];
			$buyer_id = $mst_dataArray[0][csf('buyer_id')];
			$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
			$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
			$dia = $mst_dataArray[0][csf('fabric_dia')];
			$prepared_by = $user_library[$mst_dataArray[0][csf('inserted_by')]];

			$po_id = array_unique(explode(",", $plan_details_array[$dataArray[0][csf('id')]]));
			$po_no = '';

			$job_no = '';
			$ref_cond = '';
			$file_cond = '';
			$styleRef = "";
			foreach ($po_id as $val) {
				if ($po_no == '') $po_no = $po_array[$val]['no'];
				else $po_no .= "," . $po_array[$val]['no'];
				if ($job_no == '') $job_no = $po_array[$val]['job_no'];
				if ($ref_cond == "") $ref_cond = $po_array[$val]['ref'];
				else $ref_cond .= "," . $po_array[$val]['ref'];
				if ($file_cond == "") $file_cond = $po_array[$val]['file'];
				else $file_cond .= "," . $po_array[$val]['file'];
				if ($styleRef == '') $styleRef = $po_array[$val]['style_ref'];
				else $styleRef .= "," . $po_array[$val]['style_ref'];
			}


			?>
			<div style="width:380px; float:left;">
				<table width="100%" style="margin-top:5px" cellspacing="7">
					<tr>
						<td><b>To</b></td>
					</tr>
					<tr>
						<td width=""><b><? echo $dataArray[0][csf('attention')]; ?></b></td>
					</tr>
					<tr>
						<td width=""><b>
								<?
								if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
								else if ($dataArray[0][csf('knitting_source')] == 3) echo $supplier_details[$dataArray[0][csf('knitting_party')]];
								?>
							</b>
						</td>
					</tr>
					<tr>
						<td width=""><b>
								<?
								$address_knit = '';
								if ($dataArray[0][csf('knitting_source')] == 1) {
									$addressArray_knit = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id");
									foreach ($addressArray_knit as $result) {
										if ($result[csf('plot_no')]) {
											echo $result[csf('plot_no')] . ', ';
										}
										if ($result[csf('road_no')]) {
											echo $result[csf('road_no')] . ', ';
										}
										if ($result[csf('block_no')]) {
											echo $result[csf('block_no')] . ', ';
										}
										if ($result[csf('city')]) {
											echo $result[csf('city')] . ', ';
										}
									}
								} else if ($dataArray[0][csf('knitting_source')] == 3) {
									$address_knit = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
									echo $address_knit;
								}
								?></b>
						</td>
					</tr>
					<tr>
						<td width="200"><b>Knitting Location:</b></td>
						<td width=""><b><? echo $location; ?></b></td>
					</tr>
					<tr>
						<td width="200"><b>Knitting Floor:</b></td>
						<td width=""><b><? echo $floor_name; ?></b></td>
					</tr>

					<tr>
						<td width=""><b>Machine No:</b></td>
						<td width=""><b><? echo $machine_no; ?></b></td>
					</tr>
					<tr>
						<td width=""><b>Start Date:</b></td>
						<td width=""><b><? echo change_date_format($dataArray[0][csf("start_date")]); ?></b></td>
					</tr>
					<tr>
						<td width=""><b>End Date:</b></td>
						<td width=""><b><? echo change_date_format($dataArray[0][csf("end_date")]); ?></b></td>
					</tr>

				</table>
			</div>
			<div style="float:left; width:550px;">
				<table width="100%" style="margin-top:5px" cellspacing="7">
					<tr>
						<td><b>Fabrication & FGSM:</b></td>
						<td style="font-size: 13px;"><b><? echo $fabric_desc . " & " . $gsm_weight; ?></b></td>
					</tr>
					<tr>
						<td><b>Buyer Name:</b></td>
						<td><b><? echo $order_buyers; ?></b></td>
					</tr>
					<tr>
						<td><b>Booking No:</b></td>
						<td><b><? echo $booking_no; ?></b></td>
					</tr>
					<tr>
						<td><b>Style Ref:</b></td>
						<td><b><? echo implode(",", array_unique(explode(",", $style_ref_nos))); ?></b></td>
					</tr>
					<tr>
						<td valign="top"><b>Textile Ref:</b></td>
						<td><b><? echo $sales_nos; //implode(", ", array_unique(explode(",", $po_no))); 
								?></b></td>
					</tr>
					<tr>
						<td><b>Batch No:</b></td>
						<td><b><? echo $dataArray[0][csf("batch_no")]; ?></b></td>
					</tr>
					<tr>
						<td><b>Feeder:</b></td>
						<td><b><? echo $feeder[$dataArray[0][csf("feeder")]]; ?></b></td>
					</tr>

					<tr>
						<td><b>Remarks: </b></td>
						<td><b><? echo $dataArray[0][csf("remarks")]; ?></b></td>
					</tr>
					<tr>
						<td><b>Shipment Date: </b></td>
						<td><b><? echo $shipment_date; ?></b></td>
					</tr>
				</table>
			</div>
		</div>

		<table width="950" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Requisition No</th>
				<th width="100">Lot No</th>
				<th width="200">Yarn Description</th>
				<th width="100">Brand</th>
				<th width="100">Requisition Qnty</th>
				<th width="100">Yarn Color</th>
				<th>Remarks</th>
			</thead>
			<?
			$j = 1;
			$tot_reqsn_qty = 0;
			foreach ($rqsn_array as $prod_id => $data) {
				if ($j % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $j; ?></td>
					<td width="100" align="center"><? echo substr($data['reqsn'], 0, -1); ?></td>
					<td width="100" align="center">
						<p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p>
					</td>
					<td width="200" align="center">
						<p><? echo $product_details_array[$prod_id]['desc']; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo trim($product_details_array[$prod_id]['brand'], ","); ?></p>
						</th>
					<td width="100" align="right">
						<p><? echo number_format($data['qnty'], 2, '.', ''); ?>&nbsp;</p>
					</td>
					<td width="100" align="center">
						<p><? echo $product_details_array[$prod_id]['color']; ?></p>
					</td>
					<td align="center"><? //echo number_format($data['no_of_cone']); 
										?></td>
				</tr>
			<?
				$tot_reqsn_qty += $data['qnty'];
				$j++;
			}
			?>
			<tfoot>
				<th colspan="5" align="right">Total</th>
				<th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
				<th align="right">&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>

		<table style="margin-top:10px;" width="1025" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<tr>
					<th colspan="2" align="left">Program Details:</th>
					<th colspan="9"></th>
				</tr>
				<tr>
					<th width="25">SL</th>
					<th width="100">Garments Color</th>
					<th width="100">Color Range</th>
					<th width="100">S/L</th>
					<th width="100">Spandex Stitch Length</th>
					<th width="100">Machine Dia & Gauge</th>
					<th width="100">Finish Dia [Plan Wise]</th>
					<th width="100">Finish GSM</th>
					<th width="100">Program Qnty</th>
					<th>Remarks</th>
				</tr>
			</thead>
			<?
			$i = 1;
			$s = 1;
			$tot_program_qnty = 0;
			$tot_yarn_reqsn_qnty = 0;
			$company_id = '';
			// $sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

			$sql =  "SELECT a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, d.color_id, b.color_range, b.machine_dia,b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, d.color_prog_qty as program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id,b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,
				ppl_planning_entry_plan_dtls c, ppl_color_wise_break_down d where a.id=b.mst_id and b.id=c.dtls_id and c.dtls_id = d.program_no and c.mst_id = d.plan_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type,
				b.id , d.color_id, b.color_range, b.machine_dia,b.width_dia_type, b.machine_gg, b.fabric_dia, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id,b.start_date, b.end_date, b.remarks,d.color_prog_qty";
			//echo $sql;


			$nameArray = sql_select($sql);
			foreach ($nameArray as $row) {
				$programQntyArr[$row[csf('company_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('program_id')]][$row[csf('color_id')]][$row[csf('color_range')]][$row[csf('machine_dia')]][$row[csf('diatype')]][$row[csf('machine_gg')]][$row[csf('fabric_dia')]][$row[csf('program_date')]][$row[csf('stitch_length')]][$row[csf('spandex_stitch_length')]][$row[csf('feeder')]][$row[csf('machine_id')]][$row[csf('start_date')]][$row[csf('end_date')]][$row[csf('remarks')]] += $row[csf('program_qnty')];
			}
			//print_r($programQntyArr);
			foreach ($nameArray as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$color = '';
				$color_id = explode(",", $row[csf('color_id')]);

				foreach ($color_id as $val) {
					if ($color == '')
						$color = $color_library[$val];
					else
						$color .= "," . $color_library[$val];
				}

				if ($company_id == '')
					$company_id = $row[csf('company_id')];

				$machine_no = '';
				$machine_id = explode(",", $row[csf('machine_id')]);

				foreach ($machine_id as $val) {
					if ($machine_no == '')
						$machine_no = $machine_arr[$val];
					else
						$machine_no .= "," . $machine_arr[$val];
				}

				if ($knit_id_array[$row[csf('program_id')]] != "") {
					//$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
					//$row_span = count($all_prod_id);

					// foreach ($all_prod_id as $prod_id)
					// {

					$progranQnty = $programQntyArr[$row[csf('company_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('program_id')]][$row[csf('color_id')]][$row[csf('color_range')]][$row[csf('machine_dia')]][$row[csf('diatype')]][$row[csf('machine_gg')]][$row[csf('fabric_dia')]][$row[csf('program_date')]][$row[csf('stitch_length')]][$row[csf('spandex_stitch_length')]][$row[csf('feeder')]][$row[csf('machine_id')]][$row[csf('start_date')]][$row[csf('end_date')]][$row[csf('remarks')]];
			?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="25"><? echo $i; ?></td>
						<td width="100" align="center">
							<p><? echo $color; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $color_range[$row[csf('color_range')]]; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('stitch_length')]; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('spandex_stitch_length')]; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('fabric_dia')] . ' (' . $fabric_typee[$row[csf('diatype')]] . ')'; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('gsm_weight')]; ?></p>
						</td>
						<td width="100" align="right" align="center"><? echo number_format($progranQnty, 2, '.', ''); ?></td>
						<td></td>

					</tr>
				<?
					$tot_program_qnty += $progranQnty;
					$i++;
					//}
				} else {
					$progranQnty = $programQntyArr[$row[csf('company_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('program_id')]][$row[csf('color_id')]][$row[csf('color_range')]][$row[csf('machine_dia')]][$row[csf('diatype')]][$row[csf('machine_gg')]][$row[csf('fabric_dia')]][$row[csf('program_date')]][$row[csf('stitch_length')]][$row[csf('spandex_stitch_length')]][$row[csf('feeder')]][$row[csf('machine_id')]][$row[csf('start_date')]][$row[csf('end_date')]][$row[csf('remarks')]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="25"><? echo $i; ?></td>
						<td width="100" align="center">
							<p><? echo $color; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $color_range[$row[csf('color_range')]]; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('stitch_length')]; ?></p>
							</th>
						<td width="100" align="center">
							<p><? echo $row[csf('spandex_stitch_length')]; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('fabric_dia')] . ' (' . $fabric_typee[$row[csf('diatype')]] . ')'; ?></p>
						</td>
						<td width="100" align="center">
							<p><? echo $row[csf('gsm_weight')]; ?></p>
						</td>
						<td width="100"><? echo number_format($progranQnty, 2, '.', ''); ?></td>
						<td>
							<p><? //echo $row[csf('remarks')]; 
								?></p>
						</td>
					</tr>
			<?
					$tot_program_qnty += $progranQnty;
					$i++;
				}
			}
			?>
			<tfoot>
				<th colspan="8" align="right"><b>Total</b></th>
				<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
		<br>
		<?

		$sql = "select pre_cost_id, color_id, stripe_color_id,no_of_feeder from ppl_planning_feeder_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id";
		//echo $sql;
		$sql_fedder = sql_select($sql);

		$$preCostIdArray = array();
		$i = 0;
		foreach ($sql_fedder as $preCostIdRows) {
			$preCostIdArray[$preCostIdRows[csf("pre_cost_id")]] = $preCostIdRows[csf("pre_cost_id")];
			$no_of_feeders[$i] = $preCostIdRows[csf('no_of_feeder')];
			$i++;
		}

		$preCostIdAr = implode(",", $preCostIdArray);
		if ($preCostIdAr == "") {
			$preCostIdAr = 0;
		}

		//-----------------------------------------------------------------------


		$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, min(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id in ($preCostIdAr) and a.dtls_id  in($program_ids)  and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id");

		// echo "select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, min(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id in ($preCostIdAr) and a.dtls_id  in($program_ids)  and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id";

		if (count($sql_fedder) > 0) {
		?>
			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="200">Color</th>
						<th width="200">Stripe Color</th>
						<th width="120">Measurement</th>
						<th width="120">UOM</th>
						<th>No Of Feeder</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$total_feeder = 0;
					foreach ($sql_fedder as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
					?>
						<tr>
							<td align="center">
								<p><? echo $i; ?>&nbsp;</p>
							</td>
							<td>
								<p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p>
							</td>
							<td>
								<p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p>
							</td>
							<td align="right">
								<p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p>
							</td>
							<td align="center">
								<p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p>
							</td>
							<td align="right">
								<p><? printf("%.3f", $row[csf('no_of_feeder')]);
									$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th align="right">Total:</th>
						<th align="right"><? echo number_format($total_feeder, 0); ?></th>
					</tr>
				</tfoot>
			</table>
		<?
		}

		//--------------------------------------------------------------------------


		//$sql_color_size_data = sql_select("select a.body_part_id,b.grey_size_id,b.finish_size_id,b.per_kg,b.kg_wise_total_qnty,b.current_qty,b.color_id,a.po_id,a.booking_no,a.pre_cost_fabric_cost_dtls_id from ppl_planning_entry_plan_dtls a,ppl_size_wise_break_down b where a.mst_id=b.plan_id and a.dtls_id=b.program_no and a.dtls_id in($program_ids) and a.body_part_id=b.body_part_id  order by b.grey_size_id");
		$sql_color_size_data = sql_select("select a.body_part_id,b.grey_size_id,b.finish_size_id,b.per_kg,b.kg_wise_total_qnty,b.current_qty,b.color_id,a.po_id,a.booking_no,a.pre_cost_fabric_cost_dtls_id from ppl_planning_entry_plan_dtls a,ppl_size_wise_break_down b,PPL_COLOR_WISE_BREAK_DOWN c  where a.mst_id=b.plan_id and a.dtls_id=b.program_no and a.dtls_id in($program_ids) and B.COLOR_WISE_MST_ID=c.id and b.color_id=c.color_id and c.program_no=b.program_no and a.body_part_id=b.body_part_id order by b.grey_size_id");

		$poIDS = "";
		$bookingNOS = "";
		$colorIDS = "";
		$pre_cost_fabric_cost_dtls_id = "";

		foreach ($sql_color_size_data as $row) {
			$poIDS .= $row[csf('po_id')] . ",";
			$bookingNOS .= "'" . $row[csf('booking_no')] . "',";
			$colorIDS .= $row[csf('color_id')] . ",";
			$pre_cost_fabric_cost_dtls_id .= $row[csf('pre_cost_fabric_cost_dtls_id')] . ",";
			$dataArrInfo[$row[csf('body_part_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]]["per_kg"] += $row[csf('per_kg')];
			$dataArrInfo[$row[csf('body_part_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]]["kg_wise_total_qnty"] += $row[csf('kg_wise_total_qnty')];
			$dataArrInfo[$row[csf('body_part_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]]["grey_size_id"] = $row[csf('grey_size_id')];
			$dataArrInfo[$row[csf('body_part_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]]["current_qty"] = $row[csf('current_qty')];
			$dataArrInfo[$row[csf('body_part_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]]["finish_size_id"] = $row[csf('finish_size_id')];
			$dataArrInfo[$row[csf('body_part_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]]["color_id"] = $row[csf('color_id')];
			$bodyPartArr[$row[csf('body_part_id')]] = $row[csf('body_part_id')];
		}
		/* echo "<pre>";
					print_r($dataArrInfo);
					echo "</pre>";*/

		$poIDS = chop($poIDS, ",");
		$bookingNOS = chop($bookingNOS, ",");
		$colorIDS = chop($colorIDS, ",");
		$pre_cost_fabric_cost_dtls_id = chop($pre_cost_fabric_cost_dtls_id, ",");


		$sql_po = sql_select("SELECT x.fabric_color_id, x.body_part_id,x.size_number_id,x.qnty ,x.order_quantity ,x.plan_cut_qnty,x.colar_cuff_per,x.body_part_type,x.colar_excess_percent, x.cuff_excess_percent,x.collar_cuff_breakdown , x.booking_without_order from(SELECT b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity ,sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent,null as collar_cuff_breakdown , 0 as booking_without_order
					from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
					and  b.color_size_table_id=d.id and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$company_id and a.item_category=2 and a.booking_no in($bookingNOS)  and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_fabric_cost_dtls_id)
					and b.fabric_color_id in($colorIDS) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
					group by b.fabric_color_id, c.body_part_id,d.size_number_id,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent
					union all
					select b.fabric_color as fabric_color_id,b.body_part as body_part_id,f.size_id as size_number_id,sum(b.grey_fabric) as qnty ,null as order_quantity,
					null as plan_cut_qnty,null as colar_cuff_per,e.body_part_type ,null as colar_excess_percent, null as cuff_excess_percent,d.collar_cuff_breakdown, 1 as booking_without_order
					from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b ,sample_development_fabric_acc d,sample_development_dtls c,sample_development_size f, lib_body_part e
					where a.booking_no=b.booking_no
					and b.dtls_id=d.id and b.gmts_item_id=d.gmts_item_id and b.body_part=d.body_part_id and b.dia=d.dia and b.lib_yarn_count_deter_id=d.determination_id
					and d.sample_mst_id=c.sample_mst_id and c.id=f.dtls_id and  c.sample_mst_id=f.mst_id
					and b.body_part=e.id and a.booking_no in($bookingNOS) and a.company_id=$company_id and a.item_category=2 and b.fabric_color in($colorIDS)
					and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1
					and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and f.status_active=1 and f.is_deleted=0
					group by b.fabric_color,b.body_part,f.size_id,e.body_part_type,d.collar_cuff_breakdown) x group by x.fabric_color_id, x.body_part_id,x.size_number_id,x.qnty ,x.order_quantity ,x.plan_cut_qnty,x.colar_cuff_per,x.body_part_type,x.colar_excess_percent, x.cuff_excess_percent,x.collar_cuff_breakdown , x.booking_without_order order by x.size_number_id");
		//order by d.size_number_id
		foreach ($sql_po as $row) {
			if ($row[csf('body_part_type')] == 50) {

				$plantCutQnty = $row[csf('plan_cut_qnty')] * 2;
			} else {
				$plantCutQnty = $row[csf('plan_cut_qnty')];
			}
			$collar_ex_per = $row[csf('colar_cuff_per')];
			$cuff_excess_percent = $row[csf('cuff_excess_percent')];
			$colar_excess_percent = $row[csf('colar_excess_percent')];

			if ($row[csf('body_part_type')] == 50) {
				if ($collar_ex_per == 0 || $collar_ex_per == "") $collar_ex_per = $cuff_excess_percent;
				else $collar_ex_per = $collar_ex_per;
			} else if ($row[csf('body_part_type')] == 40) {
				if ($collar_ex_per == 0 || $collar_ex_per == "") $collar_ex_per = $colar_excess_percent;
				else $collar_ex_per = $collar_ex_per;
			}

			$tot_exPer = ($plantCutQnty * $collar_ex_per) / 100;
			$colar_excess_per = $tot_exPer;
			$collerqty = ($plantCutQnty + $colar_excess_per);


			if ($row[csf('booking_without_order')] == 0) {
				$poArrInfo[$row[csf('body_part_id')]][$row[csf('size_number_id')]][$row[csf('fabric_color_id')]]['plan_cut_ration_qnty'] = $collerqty;
			} else {
				$collar_cuff_breakdownArr = explode("_", $row[csf('collar_cuff_breakdown')]);
				$po_qnty_req = $collar_cuff_breakdownArr[3];
				$poArrInfo[$row[csf('body_part_id')]][$row[csf('size_number_id')]][$row[csf('fabric_color_id')]]['plan_cut_ration_qnty'] = $po_qnty_req;
			}
		}


		/*echo "<pre>";
					print_r($poArrInfo);
					echo "</pre>";*/

		$body_part_type = return_library_array("select id, body_part_type from lib_body_part where status_active=1", 'id', 'body_part_type');
		//echo $cbo_body_part;
		//echo $body_part_type[$cbo_body_part];die;
		if ($body_part_type[$sql_color_size_data[0][csf('body_part_id')]] == 40 || $body_part_type[$sql_color_size_data[0][csf('body_part_id')]] == 50) {
			$popupOnOff = 1;
		} else {
			$popupOnOff = 0;
		}

		if ($popupOnOff == 1) {
			$size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");
		?>
			<br />
			<?
			/*foreach ($bodyPartArr as $bodyPartID)
						{*/
			?>

			<table style="margin-top:10px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">

				<thead>
					<tr>
						<td colspan="6" align="center"><b>Collar & Cuff Details</b></td>
					</tr>
					<tr>
						<th width="50">SL</th>
						<th width="200">Color</th>
						<th width="50">Gmt Size</th>
						<th width="100">Finish Size</th>
						<th width="50">Qty In Pcs</th>
						<th width="50">Current Qty </th>
						<th width="50">Per Kg Qty</th>
						<th>Qty In Kg</th>
					</tr>
				</thead>
				<tbody>
					<?
					$totKgWiseQnty = 0;
					$totPoReqQnty = 0;
					//foreach ($sql_color_size_data as $row) {
					foreach ($dataArrInfo as $bodyPartID => $bodyPartdata) {
						$i = 1;
					?>
						<tr>
							<td style="font-size:16px; text-align: center;" colspan="6"><? echo $body_part[$bodyPartID]; ?></td>
						</tr>
						<?
						foreach ($bodyPartdata as $grey_size_key => $grey_size_data) {
							foreach ($grey_size_data as $finish_size_key => $finish_size_data) {
								foreach ($finish_size_data as $colorKey => $rows) {
									$po_qnty_req = $poArrInfo[$bodyPartID][$rows['grey_size_id']][$rows['color_id']]['plan_cut_ration_qnty'];

									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
						?>
									<tr>
										<td align="center">
											<p><? echo $i; ?>&nbsp;</p>
										</td>
										<td align="center">
											<p><? echo $color_library[$rows['color_id']]; ?></p>
										</td>
										<td align="center">
											<p><? echo $size_library[$rows['grey_size_id']]; ?>&nbsp;</p>
										</td>
										<td align="center">
											<p><? echo $rows['finish_size_id']; ?>&nbsp;</p>
										</td>
										<td align="right">
											<p><? echo number_format($po_qnty_req, 2); ?>&nbsp;</p>
										</td>
										<td align="right">
											<p><? echo $rows['current_qty']; ?></p>
										</td>
										<!-- <td align="right"><p><? //echo  number_format($po_qnty_req,0,'.','')-$rows['current_qty']; 
																	?>&nbsp;</p></td> -->
										<td align="right">
											<p><? echo $rows['per_kg']; ?>&nbsp;</p>
										</td>
										<td align="right">
											<p><? echo number_format($rows['kg_wise_total_qnty'], 2);  ?>&nbsp;</p>
										</td>
									</tr>
					<?
									$i++;
									$totPerKG += $rows['per_kg'];
									$totKgWiseQnty += $rows['kg_wise_total_qnty'];
									$totPoReqQnty += $po_qnty_req;
								}
							}
						}
					}

					?>
				</tbody>
				<tr>
					<th colspan="4" align="right">Total</th>
					<th align="right"><? echo number_format($totPoReqQnty, 2); ?>&nbsp;</th>
					<th align="right"></th>
					<th align="right"></th>
					<th align="right"><? echo number_format($totKgWiseQnty, 2); ?>&nbsp;</th>
				</tr>
			</table>

		<?
			//}
		}

		// coller cuff size wise part

		$sql_info = "select coller_cuf_size_planning from variable_settings_production where company_name='$company_id' and variable_list=53 and status_active=1 and is_deleted=0";
		//echo $sql_info;// die;
		$result_dtls = sql_select($sql_info);
		$collarCuff = $result_dtls[0]['COLLER_CUF_SIZE_PLANNING'];

		if ($collarCuff == 1) {

			$sql_fedder = sql_select("select a.id,c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id,
						a.stripe_color_id, min(b.measurement) as measurement
						from ppl_planning_feeder_dtls a, wo_pre_stripe_color b,ppl_size_wise_break_down c
						where   a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.mst_id=c.plan_id and a.dtls_id=c.program_no and c.color_id=a.color_id and c.program_no in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.no_of_feeder>0 and b.sales_dtls_id>0  group by a.id,c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id ,a.stripe_color_id order by a.id, c.color_id asc");


			$size_plan_sql = sql_select("select c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id, sum(c.current_qty) as current_qty from ppl_size_wise_break_down c where c.program_no in($program_ids) group by  c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id");
		} else {

			$sql_fedder = sql_select("select a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, min(b.measurement) as measurement,c.body_part_id,c.finish_size as finish_size_id from ppl_planning_feeder_dtls a, wo_pre_stripe_color b,ppl_color_wise_break_down d ,ppl_planning_collar_cuff_dtls c where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.mst_id=d.plan_id and a.dtls_id=d.program_no and d.color_id=a.color_id and d.program_no=c.dtls_id and d.plan_id=c.mst_id and c.dtls_id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and a.dtls_id in($program_ids) and a.no_of_feeder>0 and b.sales_dtls_id>0 group by a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,d.color_id,c.body_part_id,c.finish_size order by a.id, d.color_id");

			$size_plan_sql = sql_select("select c.mst_id as plan_id,c.dtls_id as program_no, c.body_part_id,c.finish_size as finish_size_id, sum(c.qty_pcs) as current_qty,b.color_id
						from ppl_color_wise_break_down b ,ppl_planning_collar_cuff_dtls c
						where   b.program_no=c.dtls_id and b.plan_id=c.mst_id and c.dtls_id in($program_ids)
						group by   c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,b.color_id
						order by b.color_id asc");


			//select c.mst_id as plan_id,c.dtls_id as program_no, c.body_part_id,c.finish_size as finish_size_id, sum(c.qty_pcs) as current_qty,b.color_id from ppl_color_wise_break_down b left join ppl_planning_collar_cuff_dtls c on  b.program_no=c.dtls_id and b.plan_id=c.mst_id where c.dtls_id in(17846) group by   c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,b.color_id order by b.color_id asc

		}

		$qntyArry = array();
		foreach ($size_plan_sql as $rowData) {
			if (!in_array($rowData[csf('current_qty')], $current_qty_duplicate_chk)) {
				$current_qty_duplicate_chk[] = $rowData[csf('current_qty')];
				$qntyArry[$rowData[csf('body_part_id')]][$rowData[csf('color_id')]][$rowData[csf('finish_size_id')]] += $rowData[csf('current_qty')];
			}
		}
		/*echo "<pre>";
					print_r($mainDataArry);
					echo "</pre>";*/

		$plan_color_type_array = return_library_array("select dtls_id, color_type_id from PPL_PLANNING_ENTRY_PLAN_DTLS where dtls_id in($program_ids) group by dtls_id,color_type_id", "dtls_id", "color_type_id");

		$color_type_id = $plan_color_type_array[$program_ids];


		/*$sql_fedder = sql_select("select a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, min(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.dtls_id in($program_ids)  and a.no_of_feeder>0 group by a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id order by a.id");*/

		if (count($sql_fedder) > 0) {
			foreach ($sql_fedder as $row) {
				$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['stripe_color_id'] = $row[csf('stripe_color_id')];
				$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['measurement'] = $row[csf('measurement')];
				$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['body_part_id'] = $row[csf('body_part_id')];
				$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['finish_size_id'] = $row[csf('finish_size_id')];
			}
		}


		$bodypart_library = return_library_array("select id,body_part_full_name from lib_body_part", "id", "body_part_full_name");
		if ($from_page_source == 'hyperLink') {
			foreach ($arrMeasurement as $planId => $planData) {
				foreach ($planData as $progNo => $progData) {
					foreach ($progData as $body_part_id => $body_part_idData) {
						foreach ($body_part_idData as $finish_size_id => $finish_size_idData) {
							foreach ($finish_size_idData as $color_ids => $color_idData) {
								foreach ($color_idData as $strp_color_ids => $strip_colorData) {
									foreach ($strip_colorData as $measurementNo => $rows) {
										$bodyPartCountArr[$body_part_id] += 1;
										$colorIdsCountArr[$color_ids] += 1;
										$finishSizeCountArr[$finish_size_id] += 1;
									}
								}
							}
						}
					}
				}
			}



		?>
			<table style="margin-top:10px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">

				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="150">Body Part</th>
						<th width="150">GMTS Color</th>
						<th width="100">Stripe Color</th>
						<th width="50">Measurement</th>
						<th width="50">Finish Size </th>
						<th>Quantity Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					if ($color_type_id == 2 || $color_type_id == 3 || $color_type_id == 4) {
						foreach ($arrMeasurement as $planId => $planData) {
							foreach ($planData as $progNo => $progData) {
								foreach ($progData as $body_part_id => $body_part_idData) {
									foreach ($body_part_idData as $finish_size_id => $finish_size_idData) {
										foreach ($finish_size_idData as $color_ids => $color_idData) {
											foreach ($color_idData as $strp_color_ids => $strip_colorData) {
												foreach ($strip_colorData as $measurementNo => $rows) {
													$bodyPart_span = $bodyPartCountArr[$body_part_id]++;
													$colorIds_span = $colorIdsCountArr[$color_ids]++;
													$finishSize_span = $finishSizeCountArr[$finish_size_id]++;
					?>

													<tr>
														<?
														if (!in_array($body_part_id, $body_part_id_chk)) {
															$body_part_id_chk[] = $body_part_id;
														?>
															<td align="center" rowspan="<? echo $bodyPart_span; ?>">
																<p><? echo $i; ?>&nbsp;</p>
															</td>
															<td align="center" rowspan="<? echo $bodyPart_span; ?>"><? echo $bodypart_library[$body_part_id]; ?></td>
														<?
														}

														if (!in_array($color_ids, $color_id_chk)) {
															$color_id_chk[] = $color_ids;
														?>
															<td align="center" rowspan="<? echo $colorIds_span; ?>">
																<p><? echo $color_library[$color_ids]; ?>&nbsp;</p>
															</td>
														<?
														}
														?>
														<td align="center">
															<p><? echo $color_library[$strp_color_ids]; ?>&nbsp;</p>
														</td>
														<td align="right">
															<p><? echo number_format($measurementNo, 2); ?>&nbsp;</p>
														</td>
														<?
														if (!in_array($finish_size_id, $finishSize_chk)) {
															$finishSize_chk[] = $finish_size_id;
														?>
															<td align="right" rowspan="<? echo $finishSize_span; ?>">
																<p><? echo $finish_size_id; ?>&nbsp;</p>
															</td>
															<td align="right" rowspan="<? echo $finishSize_span; ?>">
																<p><? echo $qntyArry[$body_part_id][$color_ids][$finish_size_id]; ?>&nbsp;</p>
															</td>
														<?
														}
														?>
													</tr>
							<?
													$i++;
												}
											}
										}
									}
								}
							}
						}
					} else {
						foreach ($size_plan_sql as $rows) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							//$stripeColor=$arrMeasurement[$rows[csf('plan_id')]][$rows[csf('program_no')]][$rows[csf('color_id')]]['stripe_color_id'];
							//$measurQnty=$arrMeasurement[$rows[csf('plan_id')]][$rows[csf('program_no')]][$rows[csf('color_id')]]['measurement'];
							?>
							<tr>
								<td align="center">
									<p><? echo $i; ?>&nbsp;</p>
								</td>
								<td align="center">
									<p><? echo $bodypart_library[$rows[csf('body_part_id')]];; ?></p>
								</td>
								<td align="center">
									<p><? echo $color_library[$rows[csf('color_id')]]; ?>&nbsp;</p>
								</td>
								<td align="center">
									<p></p>
								</td>
								<td align="right">
									<p></p>
								</td>
								<td align="right">
									<p><? echo $rows[csf('finish_size_id')]; ?></p>
								</td>
								<td align="right">
									<p><? echo number_format($rows[csf('current_qty')], 2); ?>&nbsp;</p>
								</td>
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
		//--------------------Cout Feeding Instruction-------------------------
		$countFeeding_array = sql_select("select DTLS_ID,SEQ_NO,COUNT_ID,FEEDING_ID,PROD_DESC from  PPL_PLANNING_COUNT_FEED_DTLS where dtls_id in($program_ids) order by DTLS_ID,SEQ_NO");
		if (count($countFeeding_array) > 0) {
		?>
			<table style="margin-top:10px;" width="650" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">

				<caption><b>Count Feeding Instruction</b></caption>

				<thead>

					<th width="80">Program No</th>
					<th width="80">Seq. No</th>
					<th width="200">Composition</th>
					<th width="80">Count</th>
					<th>Feeding</th>
				</thead>
				<tbody>
					<?

					if (count($countFeeding_array) > 0) {

						foreach ($countFeeding_array as $rows) {
							$countArrdata[$rows['DTLS_ID']][$rows['SEQ_NO']][$rows['COUNT_ID']][$rows['FEEDING_ID']][$rows['PROD_DESC']] = $rows['PROD_DESC'];
						}
						foreach ($countArrdata as $dtls_id => $dtlsData) {
							foreach ($dtlsData as $seq_no => $seq_noData) {
								foreach ($seq_noData as $count_id => $count_idData) {
									foreach ($count_idData as $feeding_id => $feeding_idData) {
										foreach ($feeding_idData as $prod_desc => $rowData) {
											$countArr[$dtls_id] += 1;
										}
									}
								}
							}
						}

						/*echo "<pre>";
								print_r($countArr);
								echo "</pre>";*/

						foreach ($countArrdata as $dtls_id => $dtlsData) {
							foreach ($dtlsData as $seq_no => $seq_noData) {
								foreach ($seq_noData as $count_id => $count_idData) {
									foreach ($count_idData as $feeding_id => $feeding_idData) {
										foreach ($feeding_idData as $prod_desc => $rowData) {
											$prog_span = $countArr[$dtls_id]++;

					?>
											<tr>
												<?
												if (!in_array($dtls_id, $prog_chk)) {
													$prog_chk[] = $dtls_id;
												?>
													<td width="80" align="center" rowspan="<? echo $prog_span; ?>"><? echo $dtls_id; ?></td>
												<?
												}
												?>
												<td width="80" align="center"><? echo $seq_no; ?></td>
												<td width="200"><? echo $prod_desc; ?></td>
												<td width="80" align="center"><? echo  $count_arr[$count_id]; ?></td>
												<td align="center"><? echo $feeding_arr[$feeding_id]; ?></td>
											</tr>

					<?

										}
									}
								}
							}
						}
					}
					?>
				</tbody>
			</table>



		<?
		}

		$dataAdviceArray = sql_select("select id, location_id, advice from ppl_planning_info_entry_dtls where id in($program_ids)");

		$location = return_field_value("location_name", "lib_location", "id='" . $dataAdviceArray[0][csf('location_id')] . "'");
		$advice = $dataAdviceArray[0][csf('advice')];

		?>


		<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<tr>
				<td colspan="4" style="word-wrap:break-word"><b>Advice:</b><? echo str_replace(array('\n', ';'), '</br>', $advice); ?></td>
			</tr>
		</table>
		<?
		$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$program_ids' and form_name='Planning Info Entry For Sales Order' and is_deleted=0"); ?>
		<? if (count($image_location) > 0) { ?>
			<div style="width:850px">
				<div style="width:850px;margin-top:10px">

					<img src="<? echo base_url($image_location); ?>" height='100%' width='100%' />
				</div>
			</div>
		<? }

		$user_lib_name = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		echo signature_table(209, $company_id, '1180px', '', '', $user_lib_name[$inserted_by]);
		?>
	</div>
<?
	exit();
}


if ($action == "lot_info_popup") {
	echo load_html_head_contents("Lot Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');

	$prod_data = sql_select("select id, supplier_id, yarn_count_id, yarn_type from product_details_master where item_category_id=1 and company_id=$companyID and status_active=1 and is_deleted=0");
	foreach ($prod_data as $row) {
		$supplierArr[$row[csf('supplier_id')]] = $supplier_arr[$row[csf('supplier_id')]];
		$countArr[$row[csf('yarn_count_id')]] = $count_arr[$row[csf('yarn_count_id')]];
		$yarn_type_arr[$row[csf('yarn_type')]] = $yarn_type[$row[csf('yarn_type')]];
	}
	//asort($supplierArr);
	//asort($countArr);
	//asort($yarn_type_arr);

	$vs_arr = array();
	$vs_arr['company_id'] = $companyID;
	$vs_aafr_arr = get_vs_auto_allocation_from_requisition($vs_arr);
	$vs_aafr = $vs_aafr_arr['is_auto_allocation'];
?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function js_set_value(id, data) {
			$('#hidden_prod_id').val(id);
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<?
		if ($vs_aafr == 1) {
		?>
			<div align="center" style="">
				<form name="searchfrm" id="searchfrm">
					<fieldset>
						<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
						<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
						<div><b><? echo $comps; ?></b></div>
						<table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
							<thead>
								<th>Supplier</th>
								<th>Count</th>
								<th>Yarn Description</th>
								<th>Type</th>
								<th>Lot</th>
								<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;"></th>
							</thead>
							<tbody>
								<tr align="center">
									<td><? echo create_drop_down("cbo_supplier", 130, $supplierArr, "", 1, "-- Select --", '', "", 0); ?></td>
									<td><? echo create_drop_down("cbo_count", 80, $countArr, "", 1, "-- Select --", '', "", 0); ?></td>
									<td><input type="text" name="txt_desc" id="txt_desc" class="text_boxes" style="width:150px">
									</td>
									<td><? echo create_drop_down("cbo_type", 110, $yarn_type_arr, "", 1, "-- Select --", '', "", 0); ?></td>
									<td><input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:70px">
									</td>
									<td>
										<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_supplier').value+'**'+document.getElementById('cbo_count').value+'**'+document.getElementById('txt_desc').value+'**'+document.getElementById('cbo_type').value+'**'+document.getElementById('txt_lot_no').value+'**'+<? echo $companyID; ?>, 'create_product_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
									</td>
								</tr>
							</tbody>
						</table>
						<div style="margin-top:02px" id="search_div"></div>
					</fieldset>
				</form>
			</div>
		<?
		} else {
			$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
		?>
			<div align="center" style="width:920px;">
				<form name="searchfrm" id="searchfrm">
					<fieldset style="width:910px;">
						<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
						<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
						<input type="hidden" name="available_qnty" id="available_qnty" value="" readonly="" />
						<div><b><? echo $comps; ?></b></div>
						<div style="float:left"><b><u>Allocated Grey Yarn</u></b></div>
						<table width="100%" border="1" rules="all" class="rpt_table">
							<thead>
								<th width="40">Sl No</th>
								<th width="120">Supplier</th>
								<th width="60">Count</th>
								<th width="230">Composition</th>
								<th width="80">Type</th>
								<th width="80">Color</th>
								<th width="80">Lot No</th>
								<th width="80">Allocation Qty</th>
								<th>Cumulative Balance Qty</th>
							</thead>
						</table>
						<div style="width:100%; overflow-y:scroll; max-height:140px;" id="scroll_body" align="left">
							<table class="rpt_table" rules="all" border="1" width="890" id="tbl_list_search">
								<?
								//for booking no
								$sql_prog = "SELECT BOOKING_NO FROM PPL_PLANNING_ENTRY_PLAN_DTLS WHERE DTLS_ID = " . $knit_dtlsId . " AND STATUS_ACTIVE = 1 AND IS_DELETED = 0";
								$sql_prog_rslt = sql_select($sql_prog);
								foreach ($sql_prog_rslt as $row) {
									$selected_booking_no = $row['BOOKING_NO'];
								}
								//end for booking no

								//for fso no
								$job_nos = explode(",", rtrim($job_no, ", "));
								foreach ($job_nos as $job) {
									$jobs .= "'" . $job . "',";
								}
								//end for fso no

								//$testprodcond = "and b.item_id in(52404)";
								$sql_allo = "select b.booking_no, b.job_no, b.item_id as prod_id, b.po_break_down_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.dyed_type, b.qnty as allocated_qnty from inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c where b.booking_no = '" . $selected_booking_no . "' and b.job_no in(" . rtrim($jobs, ", ") . ") and a.id=b.mst_id and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and c.status_active=1 and c.is_deleted=0 $testprodcond";
								//echo $sql_allo;
								$data_array = sql_select($sql_allo);
								$all_prod_id = '';
								$allocationQty = 0;
								foreach ($data_array as $row_allo) {
									$booking_no = $row_allo[csf('booking_no')];
									$job_no = $row_allo[csf('job_no')];
									$yarn_count_id = $row_allo[csf('yarn_count_id')];
									$yarn_comp_type1st = $row_allo[csf('yarn_comp_type1st')];
									$yarn_comp_percent1st = $row_allo[csf('yarn_comp_percent1st')];
									$yarn_type_id = $row_allo[csf('yarn_type')];
									$product_type_arr[$row_allo[csf('prod_id')]] = $row_allo[csf('dyed_type')];
									//$product_type_arr[$row_allo[csf('booking_no')]][$row_allo[csf('prod_id')]] = $row_allo[csf('dyed_type')];

									if ($row_allo[csf('dyed_type')] != 1) {
										if ($all_prod_id == '')
											$all_prod_id = $row_allo[csf('prod_id')];
										else
											$all_prod_id .= "," . $row_allo[csf('prod_id')];


										$job_total_allocation_arr[$job_no][$row_allo[csf('prod_id')]] += $row_allo[csf('allocated_qnty')];

										if ($row_allo[csf('booking_no')] != "") {
											$booking_alocation_arr[$row_allo[csf('booking_no')]][$row_allo[csf('prod_id')]] += $row_allo[csf('allocated_qnty')];
										}
									}

									//25.06.2020
									$expPoId = explode(',', $poId);
									for ($zs = 0; $zs <= count($expPoId); $zs++) {
										if ($row_allo[csf('po_break_down_id')] == $expPoId[$zs]) {
											$allocationQty += $row_allo[csf('allocated_qnty')];
										}
									}
								}
								unset($data_array);
								//echo "<pre>";
								//print_r($booking_alocation_arr);
								//die();

								$all_prod_id = implode(",", array_unique(explode(",", $all_prod_id)));
								if ($all_prod_id != "") {
									$prod_id_cond = "and b.product_id in($all_prod_id)";
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

								$ydsw_sql = "select x.job_no, x.product_id, sum(x.yarn_wo_qty) yarn_wo_qty from(select b.job_no, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.job_no='" . $job_no . "' $prod_id_cond group by b.job_no,b.product_id
                        union all
                        select b.job_no, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125,340) and b.entry_form in(125,340) and b.job_no='$job_no' $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond group by b.job_no,b.product_id )x group by x.job_no,x.product_id";
								//echo $ydsw_sql;
								$check_ydsw = sql_select($ydsw_sql);
								$prod_wise_ydsw = array();
								foreach ($check_ydsw as $row) {
									$prod_wise_ydsw[$row[csf("job_no")]][$row[csf("product_id")]] = $row[csf("yarn_wo_qty")];
								}
								unset($check_ydsw);
								//echo "<pre>";
								//print_r($prod_wise_ydsw);

								/*
						|--------------------------------------------------------------------------
						| for sales_booking_no
						|--------------------------------------------------------------------------
						|
						*/
								$sql_sales = "SELECT a.sales_booking_no AS BOOKING_NO FROM fabric_sales_order_mst a WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.job_no = '" . $job_no . "'";
								$sql_sales_rslt = sql_select($sql_sales);
								$booking_no_arr = array();
								foreach ($sql_sales_rslt as $row) {
									$booking_no_arr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
								}
								unset($sql_sales_rslt);
								$booking_nos = "'" . implode("','", $booking_no_arr) . "'";
								//echo "<pre>";
								//print_r($all_booking_no);

								/*
								|--------------------------------------------------------------------------
								| for program no
								|--------------------------------------------------------------------------
								|
								*/
								$sql_program = "SELECT b.id AS ID FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no in(" . $booking_nos . ") and a.status_active=1 and a.is_deleted=0"; //and b.status_active=1 and b.is_deleted=0: ommit cause program can be deleted even after issue
								$sql_program_rslt = sql_select($sql_program);
								$program_no_arr = array();
								foreach ($sql_program_rslt as $row) {
									$program_no_arr[$row['ID']] = $row['ID'];
								}
								unset($sql_program_rslt);
								$all_knit_id = implode(",", $program_no_arr);
								//echo "<pre>";
								//print_r($program_no_arr);

								if ($all_prod_id != "") {
									$req_sql = "SELECT a.booking_no AS BOOKING_NO, c.knit_id AS KNIT_ID, c.prod_id AS PROD_ID, c.requisition_no AS REQUISITION_NO, c.yarn_qnty AS YARN_QNTY FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.id=c.knit_id AND b.id in (" . $all_knit_id . ") AND c.prod_id in(" . $all_prod_id . ") AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0"; // AND b.status_active=1 AND b.is_deleted=0 : ommit cause program can be deleted even after issue
									//echo $req_sql;
									$req_result = sql_select($req_sql);
									foreach ($req_result as $row) {
										$product_type = $product_type_arr[$row['PROD_ID']];
										if ($product_type != 1) {
											$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']] += $row['YARN_QNTY'];
										}
									}
									unset($req_result);
									//echo "<pre>";
									//print_r($booking_requsition_arr);

									$sql = "SELECT id AS ID, supplier_id SUPPLIER_ID, lot AS LOT, current_stock AS CURRENT_STOCK, yarn_comp_type1st AS YARN_COMP_TYPE1ST, yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, yarn_comp_type2nd AS YARN_COMP_TYPE2ND, yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, yarn_count_id AS YARN_COUNT_ID, yarn_type AS YARN_TYPE, color AS COLOR, is_within_group AS IS_WITHIN_GROUP FROM product_details_master WHERE company_id = " . $companyID . " AND current_stock>0 AND id in($all_prod_id) AND item_category_id=1 AND status_active=1 AND is_deleted=0 ORDER BY id";
									//echo $sql;
									$result = sql_select($sql);
									$i = 1;
									$ydw_qty = 0;
									$job_total_allocation_qty = 0;
									$booking_total_allocation_qty = 0;
									$existing_requsition_qty = 0;
									$booking_issue_rtn_qty = 0;
									$balance = 0;
									$bal_alloc_qnty = 0;
									foreach ($result as $row) {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";


										$compos = '';
										if ($row['YARN_COMP_PERCENT2ND'] != 0) {
											$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
										} else {
											$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
										}
										//echo $selected_booking_requsition_qty."-".$selected_booking_issue_rtn_qty."<br>";

										$ydsw_qty = $prod_wise_ydsw[$job_no][$row['ID']] * 1;
										$job_total_allocation_qty = $job_total_allocation_arr[$job_no][$row['ID']] * 1;
										$existing_requsition_qty = $booking_requsition_arr[$selected_booking_no][$row['ID']] * 1;
										$booking_total_allocation_qty = ($booking_alocation_arr[$selected_booking_no][$row['ID']] - $existing_requsition_qty);

										//echo job_total_allocation_qty."-".$ydsw_qty."+".$existing_requsition_qty; die();
										$balance = ($job_total_allocation_qty - ($ydsw_qty + $existing_requsition_qty));
										if ($balance > $booking_total_allocation_qty) {
											$cumalative_balance = $booking_total_allocation_qty;
										} else {
											if ($balance > 0) {
												$cumalative_balance = $balance;
											} else {
												$cumalative_balance = 0;
											}
										}

										//company supplier checking here
										$company_supplier_name = ($row['IS_WITHIN_GROUP'] == 1 ? $company_arr[$row['SUPPLIER_ID']]['name'] : $supplier_arr[$row['SUPPLIER_ID']]);

										$balance_title = "PROD ID->" . $row['ID'] . " WYDS QTY->" . number_format($ydsw_qty, 2, ".", "") . " JOB TALL QTY->" . number_format($job_total_allocation_qty, 2, ".", "") . " PREV RQTY->" . number_format($existing_requsition_qty, 2, ".", "") . " Balance->" . number_format($balance, 2, ".", "") . " BALL QTY->(This Booking Allocation-This Booking Previous Requisition)=" . number_format($booking_total_allocation_qty, 2, ".", "") . "\nBalance Formula (" . number_format($job_total_allocation_qty, 2, ".", "") . "-(" . number_format($ydsw_qty, 2, ".", "") . "+" . number_format($existing_requsition_qty, 2, ".", "") . "))";

										$bal_alloc_qnty = $cumalative_balance;
										$data = $row['LOT'] . "**" . $row['YARN_COUNT_ID'] . "**" . $row['YARN_TYPE'] . "**" . $color_library[$row['COLOR']] . "**" . $compos . "**" .  number_format($cumalative_balance, 2, ".", "") . "**2";
								?>
										<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value(<? echo $row['ID']; ?>,'<? echo $data; ?>');">
											<td width="40" align="center"><? echo $i; ?></td>
											<td width="120" align="center">
												<p><? echo $company_supplier_name; ?></p>
											</td>
											<td width="60" align="center">
												<p><? echo $count_arr[$row['YARN_COUNT_ID']]; ?></p>
											</td>
											<td width="230" align="center">
												<p><? echo $compos; ?></p>
											</td>
											<td width="80" align="center">
												<p><? echo $yarn_type[$row['YARN_TYPE']]; ?></p>
											</td>
											<td width="80" align="center">
												<p><? echo $color_library[$row['COLOR']]; ?></p>
											</td>
											<td width="80" align="center">
												<p><? echo $row['LOT']; ?></p>
											</td>
											<td width="80" align="center">
												<? echo number_format($job_total_allocation_qty, 2); ?>
											</td>
											<td align="right" title="<? echo $balance_title; ?>"><? echo number_format($bal_alloc_qnty, 2); ?></td>
										</tr>
								<?
										$i++;
									}
									unset($result);
								} else
									echo "<tr><td colspan='9' align='center' style ='color:#F00;'><strong>No Item Found</strong></td></tr>";
								?>
							</table>
						</div>
						<div style="float:left"><b><u>Dyed Yarn</u></b></div>
						<table width="100%" border="1" rules="all" class="rpt_table">
							<thead>
								<th width="30">Sl</th>
								<th width="200">Supplier</th>
								<th width="60">Count</th>
								<th width="230">Composition</th>
								<th width="70">Type</th>
								<th width="70">Color</th>
								<th width="80">Lot No</th>
								<th width="70">Allocation Qty</th>
								<th>Cumulative Balance Qty</th>
							</thead>
						</table>
						<div style="width:100%; overflow-y:scroll; max-height:140px;" id="scroll_body" align="left">
							<table class="rpt_table" rules="all" border="1" width="890" id="tbl_list_search_dyied">
								<?
								$req_qnty_array = array();
								$sql_requs = "select c.prod_id, sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b, ppl_yarn_requisition_entry c where b.id=a.mst_id and a.id=c.knit_id and a.id in (" . $all_knit_id . ") and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no = '" . $selected_booking_no . "' group by c.prod_id";
								// and a.status_active=1 and a.is_deleted=0 : ommit cause program can be deleted even after issue
								$sql_requs_result = sql_select($sql_requs);
								foreach ($sql_requs_result as $row) {
									$req_qnty_array[$row[csf('prod_id')]]['req'] = $row[csf('yarn_qnty')];
								}
								unset($sql_requs_result);

								$check_ysw = sql_select("select x.wo_num, x.product_id, sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(94,340) and b.entry_form in(94,340) and a.service_type not in(7) and b.fab_booking_no = '" . $selected_booking_no . "' and b.job_no='" . $job_no . "' group by b.product_id)x group by x.wo_num,x.product_id");
								$ysw_qnty_arr = array();
								foreach ($check_ysw as $row) {
									$ysw_qnty_arr[$row[csf('product_id')]] = $row[csf('yarn_wo_qty')];
								}
								unset($check_ysw);

								$i = 1;
								$bal_alloc_qnty = 0;

								$sql_dyied_yarn_sql = "select a.job_no, a.po_break_down_id, a.item_id,sum(a.qnty) as qnty, sum(c.allocated_qnty) as allo_qty, c.id prod_id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_percent1st, c.yarn_comp_percent2nd, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.color, c.supplier_id, c.is_within_group from inv_material_allocation_mst a, product_details_master c where a.booking_no = '" . $selected_booking_no . "' and a.job_no='" . $job_no . "' and a.item_id=c.id and a.status_active=1 and a.is_deleted=0 and a.is_dyied_yarn=1 and a.booking_without_order = 0 group by a.job_no, a.po_break_down_id, a.item_id, c.id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_percent1st, c.yarn_comp_percent2nd, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.color, c.supplier_id, c.is_within_group";
								//echo $sql_dyied_yarn_sql;
								$dyedYarnData = sql_select($sql_dyied_yarn_sql);
								foreach ($dyedYarnData as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

									$compos = '';
									if ($row[csf('yarn_comp_percent2nd')] != 0) {
										$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
									} else {
										$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
									}

									//company supplier checking here
									$company_supplier_name = ($row[csf('is_within_group')] == 1 ? $company_arr[$row[csf('supplier_id')]]['name'] : $supplier_arr[$row[csf('supplier_id')]]);

									$allocation_qty = $row[csf('qnty')];
									$requstion_qty = $req_qnty_array[$row[csf('prod_id')]]['req'];
									$ysw_qnty = $ysw_qnty_arr[$row[csf('prod_id')]];
									$cu_balance_qty = ($allocation_qty - ($requstion_qty + $ysw_qnty));

									$balance_title = "PROD ID->" . $row[csf('prod_id')] . " YSW QTY->" . number_format($ysw_qnty, 2, ".", "") . ", PREV RQTY->" . number_format($requstion_qty, 2, ".", "") . ", Balance=" . number_format(($allocation_qty - ($requstion_qty + $ysw_qnty)), 2, ".", "") . "";

									$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**" . number_format($cu_balance_qty, 2, ".", "") . "**1";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="searchf<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value(<? echo $row[csf('prod_id')]; ?>,'<? echo $data; ?>');">
										<td width="30" align="center"><? echo $i; ?></td>
										<td width="200" align="center"><? echo $company_supplier_name; ?></td>
										<td width="60" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
										<td width="230" align="center"><? echo $compos; ?></td>
										<td width="70" align="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
										<td width="70" align="center"><? echo $color_library[$row[csf('color')]]; ?></td>
										<td width="80" align="center" title="<? echo $row[csf('prod_id')]; ?>"><? echo $row[csf('lot')]; ?></td>
										<td width="70" align="right"><? echo number_format($allocation_qty, 2); ?></td>
										<td align="right" title="<? echo $balance_title; ?>"><? echo number_format($cu_balance_qty, 2); ?></td>
									</tr>
								<?
									$i++;
								}
								?>
							</table>
						</div>
					</fieldset>
				</form>
			</div>
		<?php
		}
		?>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_product_search_list_view") {
	$data = explode('**', $data);

	if ($data[0] == 0) $supp_cond = "";
	else $supp_cond = " and a.supplier_id='" . trim($data[0]) . "' ";
	if ($data[1] == 0) $yarn_count_cond = "";
	else $yarn_count_cond = " and a.yarn_count_id='" . trim($data[1]) . "' ";
	if ($data[3] == 0) $yarn_type_cond = "";
	else $yarn_type_cond = " and a.yarn_type='" . trim($data[3]) . "' ";

	$yarn_desc_cond = " and a.supplier_id like '%" . trim($data[2]) . "%'";
	$lot_no_cond = " and lot like '%" . trim($data[4]) . "%'";
	$companyID = $data[5];

	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');

?>
	<table width="1210" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="40">Sl No</th>
			<th width="70">Buyer ID</th>
			<th width="70">Count</th>
			<th width="170">Composition</th>
			<th width="80">Type</th>
			<th width="80">Color</th>
			<th width="80">Lot No</th>
			<th width="140">Supplier</th>
			<th width="80">Wgt. Bag/Cone</th>
			<th width="80">Current Stock</th>
			<th width="80">Allocated Qnty</th>
			<th width="80">Available For Req.</th>
			<th width="80">Age (Days)</th>
			<th>DOH</th>
		</thead>


		<tbody class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
			$date_array = array();
			if ($db_type == 0) {
				$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date,group_concat(buyer_id) as buyer_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and receive_basis in(1,2,4) group by prod_id";
			} else {
				$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date,listagg(buyer_id, ',') within group (order by buyer_id) as buyer_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and receive_basis in(1,2,4) group by prod_id";
			}

			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$trans_info_arr[$row[csf("prod_id")]]['buyer_id'] = $row[csf("buyer_id")];
				$trans_info_arr[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
				$trans_info_arr[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
			}

			$sql = "select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,a.dyed_type
			from product_details_master a where a.company_id=$companyID and a.current_stock>0 and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd,a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,a.dyed_type order by a.lot";

			//echo $sql;
			// left join inv_transaction d on (a.id=d.prod_id and d.receive_basis in(1,2,4))
			//,sum(b.yarn_qnty) yarn_qnty
			//left join ppl_yarn_requisition_entry b on a.id=b.prod_id

			$result = sql_select($sql);
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
				$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

				$buyer = implode(",", array_unique(explode(",", $trans_info_arr[$row[csf("id")]]['buyer_id'])));
				$weight_per_bag = $trans_info_arr[$row[csf("id")]]['weight_per_bag'];
				$weight_per_cone = $trans_info_arr[$row[csf("id")]]['weight_per_cone'];

				$compos = '';
				if ($row[csf('yarn_comp_percent2nd')] != 0) {
					$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
				} else {
					$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
				}
				//$available_qnty = ($row[csf('current_stock')] - $row[csf('yarn_qnty')]);
				$available_qnty = $row[csf('available_qnty')];
				$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**" . number_format($available_qnty, 2, ".", "") . "**" . $row[csf('dyed_type')];
			?>

				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="70" align="center"><? echo $buyer_arr[$buyer]; ?></td>
					<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
					<td width="170" align="center">
						<p><? echo $compos; ?></p>
					</td>
					<td width="80" align="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
					<td width="80" align="center"><? echo $color_library[$row[csf('color')]]; ?></td>
					<td width="80" align="center"><? echo $row[csf('lot')]; ?></td>
					<td width="140" align="center"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
					<td width="80" align="center"><? echo 'Bg:' . $weight_per_bag . '; ' . '<br>' . 'Cn:' . $weight_per_cone; ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('current_stock')], 2); ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('allocated_qnty')], 2); ?></td>
					<td width="80" align="right"><? echo number_format($available_qnty, 2); ?></td>
					<td width="80" align="center"><? echo $ageOfDays; ?></td>
					<td align="center"><? echo $daysOnHand; ?></td>
				</tr>

			<?
				$i++;
			}
			?>
		</tbody>
	</table>
<?
	exit();
}

if ($action == "requisition_info_details") {
?>
	<table width="790" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="80">Lot No</th>
			<th width="70">Count</th>
			<th width="80">Type</th>
			<th width="150">Composition</th>
			<th width="90">Color</th>
			<th width="90">No of Cone</th>
			<th width="90">Requisition Date</th>
			<th>Yarn Reqs. Qnty</th>
		</thead>
	</table>
	<div style="width:790px; overflow-y:scroll; max-height:300px;" id="scroll_body" align="left">
		<table class="rpt_table" rules="all" border="1" width="773" id="tbl_list_search">
			<?
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
			$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

			if ($data != "") {
				$query = sql_select("select id, knit_id, requisition_no, prod_id, no_of_cone, requisition_date, yarn_qnty from ppl_yarn_requisition_entry where knit_id=$data and status_active = '1' and is_deleted = '0'");
			}

			$i = 1;
			$tot_yarn_qnty = 0;
			foreach ($query as $selectResult) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$dataArray = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color from product_details_master where status_active=1 and is_deleted=0 and id=" . $selectResult[csf('prod_id')] . "");

				$compos = '';
				if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0) {
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
				} else {
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
				}

				$tot_yarn_qnty += $selectResult[csf('yarn_qnty')];

			?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>, 'populate_requisition_data', 'yarn_requisition_entry_sales_controller' );">
					<td width="80">
						<p><? echo $dataArray[0][csf('lot')]; ?></p>
					</td>
					<td width="70">
						<p><? echo $count_arr[$dataArray[0][csf('yarn_count_id')]]; ?></p>
					</td>
					<td width="80">
						<p><? echo $yarn_type[$dataArray[0][csf('yarn_type')]]; ?></p>
					</td>
					<td width="150">
						<p><? echo $compos; ?></p>
					</td>
					<td width="90">
						<p><? echo $color_library[$dataArray[0][csf('color')]]; ?></p>
					</td>
					<td align="right" width="90"><? echo number_format($selectResult[csf('no_of_cone')], 0); ?></td>
					<td align="center" width="90"><? echo change_date_format($selectResult[csf('requisition_date')]); ?></td>
					<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
				</tr>
			<?
				$i++;
			}
			?>
			<tfoot>
				<th colspan="7">Total</th>
				<th><? echo number_format($tot_yarn_qnty, 2); ?></th>
			</tfoot>
		</table>
	</div>
<?
	exit();
}

if ($action == "populate_requisition_data") {
	if ($data != "") {
		$sql = "select a.id, a.knit_id, a.requisition_no, a.prod_id, a.no_of_cone, a.requisition_date, a.yarn_qnty,c.company_id from ppl_yarn_requisition_entry a,ppl_planning_info_entry_dtls b ,ppl_planning_info_entry_mst c where a.knit_id=b.id and b.mst_id=c.id and a.id=$data";
	}

	$data_array = sql_select($sql);

	$vs_arr = array();
	$vs_arr['company_id'] = $data_array[0][csf("company_id")];
	$vs_aafr_arr = get_vs_auto_allocation_from_requisition($vs_arr);
	$vs_aafr = $vs_aafr_arr['is_auto_allocation'];

	$prod_id = $data_array[0][csf("prod_id")];

	if ($vs_aafr == 1) {
		$available_qty = return_field_value("available_qnty", "product_details_master", "id =$prod_id", "available_qnty");
	} else {
		//for booking no
		$sql_prog = "SELECT a.BOOKING_NO, a.PO_ID FROM PPL_PLANNING_ENTRY_PLAN_DTLS a, PPL_YARN_REQUISITION_ENTRY b WHERE a.DTLS_ID=b.KNIT_ID AND b.id=$data and b.prod_id=$prod_id AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0";
		$sql_prog_rslt = sql_select($sql_prog);

		$selected_booking_no = $sql_prog_rslt[0][csf("BOOKING_NO")];
		$po_id = $sql_prog_rslt[0][csf("po_id")];

		$sql_allo = "select b.booking_no, b.job_no, b.item_id as prod_id, b.po_break_down_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.dyed_type, b.qnty as allocated_qnty from inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c where b.booking_no = '" . $selected_booking_no . "' and b.po_break_down_id = $po_id and b.item_id=$prod_id and a.id=b.mst_id and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and c.status_active=1 and c.is_deleted=0";

		$reslt_alc = sql_select($sql_allo);
		$booking_no = $reslt_alc[0][csf('booking_no')];
		$job_no = $reslt_alc[0][csf('job_no')];
		$yarn_count_id = $reslt_alc[0][csf('yarn_count_id')];
		$yarn_comp_type1st = $reslt_alc[0][csf('yarn_comp_type1st')];
		$yarn_comp_percent1st = $reslt_alc[0][csf('yarn_comp_percent1st')];
		$yarn_type_id = $reslt_alc[0][csf('yarn_type')];

		if ($job_no != "") {
			$job_total_allocation_arr[$job_no][$reslt_alc[0][csf('prod_id')]] += $reslt_alc[0][csf('allocated_qnty')];
		}

		if ($reslt_alc[0][csf('booking_no')] != "") {
			$booking_alocation_arr[$reslt_alc[0][csf('booking_no')]][$reslt_alc[0][csf('prod_id')]] += $reslt_alc[0][csf('allocated_qnty')];
		}
		//echo "<pre>";
		//print_r($booking_alocation_arr);
		//die();

		if ($prod_id != "") {
			$prod_id_cond = "and b.product_id = $prod_id";
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

		$ydsw_sql = "select x.booking_no, x.product_id, sum(x.yarn_wo_qty) yarn_wo_qty from(select b.fab_booking_no || b.booking_no as booking_no, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.job_no='" . $job_no . "' $prod_id_cond group by b.fab_booking_no,b.booking_no,b.product_id
        union all
        select b.fab_booking_no || b.booking_no as booking_no, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125,340) and b.entry_form in(125,340) and b.job_no='$job_no' $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond group by b.fab_booking_no,b.booking_no,b.product_id )x group by x.booking_no,x.product_id";
		//echo $ydsw_sql; die();
		$check_ydsw = sql_select($ydsw_sql);
		$prod_wise_ydsw = array();
		foreach ($check_ydsw as $row) {
			$prod_wise_ydsw[$row[csf("booking_no")]][$row[csf("product_id")]] = $row[csf("yarn_wo_qty")];
		}
		unset($check_ydsw);
		//echo "<pre>";
		//print_r($prod_wise_ydsw);

		$req_sql = "SELECT a.booking_no AS BOOKING_NO, c.knit_id AS KNIT_ID, c.prod_id AS PROD_ID, c.requisition_no AS REQUISITION_NO, c.yarn_qnty AS YARN_QNTY FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.id=c.knit_id AND a.booking_no = '" . $selected_booking_no . "' AND c.prod_id = $prod_id AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0"; //AND b.status_active=1 AND b.is_deleted=0 :  ommit cause program can be deleted even after issue
		//echo $req_sql; die();
		$req_result = sql_select($req_sql);
		foreach ($req_result as $row) {
			if ($row['BOOKING_NO'] != "") {
				$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']] += $row['YARN_QNTY'];
			}
		}
		unset($req_result);
		//echo "<pre>";
		//print_r($booking_requsition_arr);

		$ydsw_qty = $prod_wise_ydsw[$selected_booking_no][$prod_id] * 1;
		$job_total_allocation_qty = $job_total_allocation_arr[$job_no][$prod_id] * 1;
		$existing_requsition_qty = $booking_requsition_arr[$selected_booking_no][$prod_id] * 1;
		$booking_total_allocation_qty = ($booking_alocation_arr[$selected_booking_no][$prod_id] - $existing_requsition_qty);

		//echo $job_total_allocation_qty."-".$ydsw_qty."+".$existing_requsition_qty;

		$balance = ($job_total_allocation_qty - ($ydsw_qty + $existing_requsition_qty));
		if ($balance > $booking_total_allocation_qty) {
			$cumalative_balance = $booking_total_allocation_qty;
		} else {
			if ($balance > 0) {
				$cumalative_balance = $balance;
			} else {
				$cumalative_balance = 0;
			}
		}

		$available_qty = $cumalative_balance;
	}

	foreach ($data_array as $row) {
		$dataArray = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color,available_qnty from product_details_master where id=" . $row[csf('prod_id')]);

		$compos = '';
		if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
		}

		$color = return_field_value("color_name", "lib_color", "id='" . $dataArray[0][csf("color")] . "'");

		echo "document.getElementById('txt_requisition_no').value 			= '" . $row[csf("requisition_no")] . "';\n";
		echo "document.getElementById('txt_lot').value 						= '" . $dataArray[0][csf("lot")] . "';\n";
		echo "document.getElementById('cbo_yarn_count').value 				= '" . $dataArray[0][csf("yarn_count_id")] . "';\n";
		echo "document.getElementById('cbo_yarn_type').value 				= '" . $dataArray[0][csf("yarn_type")] . "';\n";

		echo "document.getElementById('txt_composition').value 				= '" . $compos . "';\n";

		echo "document.getElementById('txt_color').value 					= '" . $color . "';\n";
		echo "document.getElementById('txt_no_of_cone').value 				= '" . $row[csf("no_of_cone")] . "';\n";
		echo "document.getElementById('txt_reqs_date').value 				= '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('txt_yarn_qnty').value 				= '" . $row[csf("yarn_qnty")] . "';\n";
		echo "document.getElementById('prod_id').value 						= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('original_prod_id').value 			= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('original_prod_qnty').value 			= '" . $row[csf("yarn_qnty")] . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('companyID').value 				    = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('hidden_lot_available_qnty').value 	= '" . ($row[csf("yarn_qnty")] + $available_qty) . "';\n";
		echo "document.getElementById('txt_available_qty').value 	= '" . number_format($row[csf("yarn_qnty")] + $available_qty, 2) . "';\n";


		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_req_entry',1);\n";
		exit();
	}
}

if ($action == "barcode_popup") {
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function() {
			set_field_level_access(<? echo $companyID; ?>);
		}
		var selected_id = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

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

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_barcode_no' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_barcode_no' + str).val());
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_barcode_no' + str).val()) break;
				}
				selected_id.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}

			id = id.substr(0, id.length - 1);

			$('#hidden_barcode_nos').val(id);
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>PO Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Sales Order No</th>
							<th>Barcode No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos" value="" />
						</thead>
						<tbody>
							<tr>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
									$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>

								<td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td>

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value +'**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:05px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_barcode_search_list_view") {
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');

	$company_id = $data[0];
	$po_buyer_id = $data[1];
	$within_group = $data[2];
	$search_by = $data[3];
	$search_string = trim($data[4]);
	$barcode_no = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "%'";
		} else if ($search_by == 3) {
			$search_field_cond = " and a.sales_booking_no like '%" . $search_string . "%'";
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($barcode_no != "") {
		$search_field_cond = " and b.barcode_no = '" . $barcode_no . "'";
	}

	if ($within_group == 0) $within_group_cond = "";
	else $within_group_cond = " and a.within_group=$within_group";

	if ($po_buyer_id == 0) {
		if ($_SESSION['logic_erp']["buyer_id"] != "") {
			$po_buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		} else {
			$po_buyer_id_cond = "";
		}
	} else {
		$po_buyer_id_cond = " and a.buyer_id=$po_buyer_id";
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = ""; //defined Later


	if ($db_type == 0) {
		$booking_year = " c.booking_year";
		$booking_year2 = "YEAR(c.booking_date) as booking_year";
	} else {
		$booking_year = " cast(c.booking_year as varchar(4000)) as booking_year";
		$booking_year2 = "to_char(c.booking_date,'YYYY') as booking_year";
	}

	if ($within_group == 1) {
		$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, c.buyer_id po_buyer,c.booking_no_prefix_num,$booking_year,b.barcode_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no = c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $po_buyer_id_cond
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, c.buyer_id po_buyer,c.booking_no_prefix_num,$booking_year2,b.barcode_no  from fabric_sales_order_mst a , fabric_sales_order_dtls b , wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.sales_booking_no = c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond order by id";
	} else {
		$sql = "select a.id, $year_field,a.job_no_prefix_num,a.job_no,a.within_group,a.sales_booking_no booking_no_prefix_num,a.booking_date,a.buyer_id,a.style_ref_no,a.location_id,b.barcode_no from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond and a.within_group=2 order by a.id";
	}
	//echo $sql;
	$result = sql_select($sql);
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Sales Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th width="60">Booking Year</th>
			<th width="100">Barcode</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:870px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
					<td width="40" align="center"><? echo $i; ?>
						<input type="hidden" name="txt_barcode_no" id="txt_barcode_no<?php echo $i ?>" value="<? echo $row[csf('barcode_no')]; ?>" />
						<?php
						if ($within_group == 1) {
							$booking = $row[csf('booking_no_prefix_num')];
						} else {
							$booking = $row[csf('sales_booking_no')];
						}
						?>
						<input type="hidden" name="txt_booking_no" id="txt_booking_no<?php echo $i ?>" value="<? echo $row[csf('booking_no_prefix_num')]; ?>" />
					</td>
					<td width="70" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center">
						<p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p>
					</td>
					<td width="70">
						<p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p>
					</td>
					<td width="70" align="center">
						<p><? echo $buyer; ?>&nbsp;</p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('booking_no_prefix_num')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $row[csf('booking_year')]; ?></p>
					</td>
					<td width="100" align="center">
						<p><? echo $row[csf('barcode_no')]; ?></p>
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
	<table width="760" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=$updateId and prod_id=$prod_id and status_active=1 and is_deleted=0") == 1) {
			echo "11**" . str_replace("'", "", $updateId) . "**0";
			disconnect($con);
			exit();
		}

		//for vs allocated qty
		$vs_arr = array();
		$vs_arr['company_id'] = $companyID;
		$vs_arr['category_id'] = 1;
		$vs_aq_arr = get_vs_allocated_qty($vs_arr);
		$vs_aq = $vs_aq_arr['is_allocated'];
		//end for vs allocated qty

		//for vs auto_allocation_from_requisition
		$vs_arr = array();
		$vs_arr['company_id'] = $companyID;
		$vs_aafr_arr = get_vs_auto_allocation_from_requisition($vs_arr);
		$vs_aafr = $vs_aafr_arr['is_auto_allocation'];
		//end for vs auto_allocation_from_requisition

		/*
		|--------------------------------------------------------------------------
		| for allocation balance check
		|--------------------------------------------------------------------------
		|
		*/
		$vs_job_no = str_replace("'", "", $job_no);
		$vs_prod_id = str_replace("'", "", $prod_id);
		$vs_poId = str_replace("'", "", $sale_order_id);
		$vs_booking_no = return_field_value('sales_booking_no', "fabric_sales_order_mst", "id = " . $sale_order_id);

		$arr = array();
		$arr['product_id'] = $vs_prod_id;
		$arr['is_auto_allocation'] = $vs_aafr;
		$arr['job_no'] = $vs_job_no;
		$arr['booking_no'] = $vs_booking_no;
		$arr['po_id'] = $vs_poId;

		$allocation_arr = get_allocation_balance($arr);
		$booking_requisition_qty = $allocation_arr['booking_requisition'][$vs_booking_no][$vs_prod_id]['qty'] * 1;
		$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$vs_job_no][$vs_prod_id]['qty'] * 1;
		$booking_allocation_qty = $allocation_arr['booking_allocation'][$vs_booking_no][$vs_prod_id] * 1;
		$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);

		if ($vs_aq == 1 && $vs_aafr == 1) {
			$available_qty = $allocation_arr['available_qnty'];
			if ($allocation_arr['current_stock'] < 0.01) {
				echo "17**Stock Quantity is not Available\nStock quantity = " . $allocation_arr['current_stock'] . "";
				disconnect($con);
				exit();
			}
		}

		$txtYarnQnty = str_replace("'", "", $txt_yarn_qnty);
		$txtYarnQnty = number_format($txtYarnQnty, 2, ".", "");
		$available_qty = number_format($available_qty, 2, ".", "");
		if ($txtYarnQnty > $available_qty) {
			echo "17**Quantity is not available for Requisition.\nAvailable quantity = " . $available_qty;
			disconnect($con);
			exit();
		}

		//for requisition information
		$requisition_no = return_field_value("requisition_no", "ppl_yarn_requisition_entry", "knit_id=$updateId", "requisition_no");
		if ($requisition_no == "") {
			$requisition_no = return_next_id("requisition_no", "ppl_yarn_requisition_entry", 1);
			$rechk_requisition_no = return_field_value("requisition_no", "ppl_yarn_requisition_entry", "requisition_no=$requisition_no", "requisition_no"); //Recheck requsition no

			if ($rechk_requisition_no != "") //system have
			{
				$requisition_no = return_next_id("requisition_no", "ppl_yarn_requisition_entry", 1);
			} else {
				$requisition_no = $requisition_no;
			}
		} else
			$requisition_no = $requisition_no;

		//for vs checking
		if ($vs_aq == 1 && $vs_aafr == 1) {
			// prepare data to update allocation
			if ($updateId != "") {
				$sql = "select a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id=$updateId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc,c.po_id";
			}

			$planning_array = sql_select($sql);

			$po_id = $planning_array[0][csf('po_id')];
			$booking_no = $planning_array[0][csf('booking_no')];
			$job_no = str_replace("'", "", $job_no);
			$prod_id = str_replace("'", "", $prod_id);
			$hidden_dyed_type = str_replace("'", "", $hidden_dyed_type);

			if ($hidden_dyed_type == 2 || $hidden_dyed_type == 0 || $hidden_dyed_type == "") {
				$hidden_dyed_type = 0;
			} else {
				$hidden_dyed_type = $hidden_dyed_type;
			}

			// if allocation found
			$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $prod_id) . " and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";

			$check_allocation_array = sql_select($sql_allocation);

			$id = return_next_id("id", "ppl_yarn_requisition_entry", 1);
			$field_array = "id, knit_id, requisition_no, prod_id, no_of_cone, requisition_date, yarn_qnty, inserted_by, insert_date";

			$data_array = "(" . $id . "," . $updateId . "," . $requisition_no . "," . $prod_id . "," . $txt_no_of_cone . "," . $txt_reqs_date . "," . $txt_yarn_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$rID1 = sql_insert("ppl_yarn_requisition_entry", $field_array, $data_array, 0);

			if (!empty($check_allocation_array)) {
				$txt_yarn_qnty = str_replace("'", "", $txt_yarn_qnty) * 1;
				$mst_id = $check_allocation_array[0][csf('id')];

				if ($rID1) //requsition data transection true then allocation
				{
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty+$txt_yarn_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
				}

				$rID3 = false;
				if ($rID1 && $rID2) // requsition and mst allocation data transection true then dtls allocation
				{
					$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty+$txt_yarn_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id='$po_id' and item_id = $prod_id", 0);
				}
			} else {
				if ($job_no != "") {
					$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
					$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
					$data_array_allocation_mst = "(" . $id_allocation . "," . $id . ",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
					$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					if ($rID1) // Requsition true then
					{
						$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
					}

					$rID3 = false;
					if ($data_array_allocation_dtls != '') {
						if ($rID1 && $rID2) // requsition and mst allocation data transection true then dtls allocation
						{
							$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
						}
					}
				} else {
					$rID2 = $rID3 = false;
				}
			}
			// update product allocation,available details
			if ($rID2 && $rID3) // mst allocation and dtls allocation data transection true then
			{
				$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$txt_yarn_qnty),available_qnty=(current_stock-(allocated_qnty+$txt_yarn_qnty)),update_date='" . $pc_date_time . "' where id=$prod_id", 0);
			}
		} else {
			$rID2 = true;
			$rID3 = true;
			$rID4 = true;
			$id = return_next_id("id", "ppl_yarn_requisition_entry", 1);
			$field_array = "id, knit_id, requisition_no, prod_id, no_of_cone, requisition_date, yarn_qnty, inserted_by, insert_date";
			$data_array = "(" . $id . "," . $updateId . "," . $requisition_no . "," . $prod_id . "," . $txt_no_of_cone . "," . $txt_reqs_date . "," . $txt_yarn_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$rID1 = sql_insert("ppl_yarn_requisition_entry", $field_array, $data_array, 0);
		}

		/*oci_rollback($con);
		echo "10**$rID1 && $rID2 && $rID3 && $rID4";
		disconnect($con);
		die();*/

		if ($db_type == 0) {
			if ($rID1 && $rID2 && $rID3 && $rID4) {
				mysql_query("COMMIT");
				echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
			} else {
				mysql_query("ROLLBACK");
				echo "5**" . str_replace("'", "", $updateId) . "**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2 && $rID3 && $rID4) {
				oci_commit($con);
				echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
			} else {
				oci_rollback($con);
				echo "5**" . str_replace("'", "", $updateId) . "**0";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here
	{
		$con = connect();
		$original_prod_id = str_replace("'", "", $original_prod_id);

		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		// check if item is already found against this requisition
		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=$updateId and prod_id=$prod_id and id<>$update_dtls_id and status_active=1 and is_deleted=0") == 1) {
			echo "11**" . str_replace("'", "", $updateId) . "**1";
			disconnect($con);
			exit();
		}

		//for vs allocated qty
		$vs_arr = array();
		$vs_arr['company_id'] = $companyID;
		$vs_arr['category_id'] = 1;
		$vs_aq_arr = get_vs_allocated_qty($vs_arr);
		$vs_aq = $vs_aq_arr['is_allocated'];
		//end for vs allocated qty

		//for vs auto_allocation_from_requisition
		$vs_arr = array();
		$vs_arr['company_id'] = $companyID;
		$vs_aafr_arr = get_vs_auto_allocation_from_requisition($vs_arr);
		$vs_aafr = $vs_aafr_arr['is_auto_allocation'];
		//end for vs auto_allocation_from_requisition

		/*
		|--------------------------------------------------------------------------
		| for allocation balance check
		|--------------------------------------------------------------------------
		|
		*/
		$vs_job_no = str_replace("'", "", $job_no);
		$vs_prod_id = str_replace("'", "", $prod_id);
		$vs_poId = str_replace("'", "", $sale_order_id);
		$vs_booking_no = return_field_value('sales_booking_no', "fabric_sales_order_mst", "id = " . $sale_order_id);

		$arr = array();
		$arr['product_id'] = $vs_prod_id;
		$arr['is_auto_allocation'] = $vs_aafr;
		$arr['job_no'] = $vs_job_no;
		$arr['booking_no'] = $vs_booking_no;
		$arr['po_id'] = $vs_poId;

		$allocation_arr = get_allocation_balance($arr);
		$booking_requisition_qty = $allocation_arr['booking_requisition'][$vs_booking_no][$vs_prod_id]['qty'] * 1;
		$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$vs_job_no][$vs_prod_id]['qty'] * 1;
		$booking_allocation_qty = $allocation_arr['booking_allocation'][$vs_booking_no][$vs_prod_id] * 1;
		$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);

		if ($vs_aq == 1 && $vs_aafr == 1) {
			$available_qty = $allocation_arr['available_qnty'];
			if ($allocation_arr['current_stock'] < 0.01) {
				echo "17**Stock Quantity is not Available\nStock quantity = " . $allocation_arr['current_stock'] . "";
				disconnect($con);
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| product checking
		| if original product and update product is same then
		|--------------------------------------------------------------------------
		|
		*/
		if (str_replace("'", "", $prod_id) == str_replace("'", "", $original_prod_id)) {
			$qnty_limit = (str_replace("'", "", $original_prod_qnty) + $available_qty);

			$txtYarnQnty = str_replace("'", "", $txt_yarn_qnty);
			$txtYarnQnty = number_format($txtYarnQnty, 2, ".", "");
			$qnty_limit = number_format($qnty_limit, 2, ".", "");
			if ($txtYarnQnty > $qnty_limit) {
				echo "17**Quantity is not available for Requisition.\nAvailable quantity = " . $qnty_limit;
				disconnect($con);
				exit();
			}
		} else {
			$txtYarnQnty = str_replace("'", "", $txt_yarn_qnty);
			$txtYarnQnty = number_format($txtYarnQnty, 2, ".", "");
			$available_qty = number_format($available_qty, 2, ".", "");

			if ($txtYarnQnty > $available_qty) {
				echo "17**Quantity is not available for Requisition.\nAvailable quantity = " . $available_qty;
				disconnect($con);
				exit();
			}
		}

		// check if daily yarn demand found and demand cumalative balancing
		$check_demand_entry = sql_select("select sum(yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls where requisition_no=$txt_requisition_no and prod_id=$original_prod_id and status_active=1 and is_deleted=0");

		if ($original_prod_id != str_replace("'", "", $prod_id)) {
			if ((!empty($check_demand_entry)) && ($check_demand_entry[0][csf("yarn_demand_qnty")] != null)) {
				echo "17**Demand found. Lot can not be changed.";
				disconnect($con);
				exit();
			}
		} else {
			if (($check_demand_entry[0][csf("yarn_demand_qnty")] != "") && (str_replace("'", "", $txt_yarn_qnty) < $check_demand_entry[0][csf("yarn_demand_qnty")])) {
				echo "17**Requisition quantity can not be less than daily yarn demand entry.\nDemand quantity=" . $check_demand_entry[0][csf('yarn_demand_qnty')];
				disconnect($con);
				exit();
			}
		}

		// Check if issue found against Requisition and cumalative issue quantity balancing
		$check_issue = sql_select("select listagg(cast(b.ISSUE_NUMBER_PREFIX_NUM as varchar2(4000)), ',') within group (order by b.ISSUE_NUMBER_PREFIX_NUM) as issue_num,sum(a.cons_quantity) issue_qnty from inv_transaction a,inv_issue_master b where a.mst_id=b.id and a.receive_basis in(3,8) and a.transaction_type=2 and a.requisition_no=$txt_requisition_no and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		$check_issue_return = sql_select("select listagg(cast(c.recv_number_prefix_num as varchar2(4000)), ',') within group (order by c.recv_number_prefix_num) as return_num, sum(b.cons_quantity) issue_return_qnty from inv_transaction b,inv_receive_master c where b.mst_id=c.id and b.issue_id in(select a.mst_id from inv_transaction a where a.transaction_type in(2) and a.requisition_no=$txt_requisition_no and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$original_prod_id) and b.transaction_type=4 and b.receive_basis in (3,8) and b.prod_id=$original_prod_id and b.requisition_no=$txt_requisition_no and b.status_active=1
		and b.is_deleted=0 and b.status_active=1
		and b.is_deleted=0");

		if ($original_prod_id != str_replace("'", "", $prod_id)) {
			if ((!empty($check_issue)) && ($check_issue[0][csf('issue_qnty')] != null)) {
				$issue_mrr_mo = $check_issue[0][csf('issue_num')];
				echo "17**Issue found.\nIssue mrr no=$issue_mrr_mo\nYou can not change this lot.";
				disconnect($con);
				exit();
			}
		} else {
			//echo "10**";
			//echo (str_replace("'", "", $txt_yarn_qnty) ."<". ($check_issue[0][csf('issue_qnty')]-$check_issue_return[0][csf('issue_return_qnty')])); die;

			$issueQty = $check_issue[0][csf('issue_qnty')];
			$issueReturnQty = $check_issue_return[0][csf('issue_return_qnty')];
			$balanceQnty = number_format(($issueQty - $issueReturnQty), 2, '.', '');
			$requisitionQty = str_replace("'", "", $txt_yarn_qnty) * 1;
			$requisitionQty = number_format($requisitionQty, 2, '.', '');

			if (!empty($check_issue) && (($requisitionQty < $balanceQnty) || ('0.00' > $balanceQnty))) {
				$issue_mrr_mo = $check_issue[0][csf('issue_num')];
				$return_mrrno = $check_issue_return[0][csf('return_num')];
				$return_msg = "";
				$return_msg = ($issueReturnQty > 0) ? "Return Mrr no=$return_mrrno\nIssue Return quantity =$issueReturnQty" : "";
				echo "17**Issue Found.\nIssue mrr no=$issue_mrr_mo\nIssue Quantity =$issueQty\n$return_msg\nUpto Reduce Balance =$balanceQnty";
				disconnect($con);
				exit();
			}
		}

		//for vs checking
		if ($vs_aq == 1 && $vs_aafr == 1) {
			if ($updateId != "") {
				$sql = "select a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id=$updateId group by a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc,c.po_id";
			}

			$planning_array = sql_select($sql);

			$po_id = $planning_array[0][csf('po_id')];
			$booking_no = $planning_array[0][csf('booking_no')];
			$job_no = str_replace("'", "", $job_no);
			$prod_id = str_replace("'", "", $prod_id);
			$hidden_dyed_type = str_replace("'", "", $hidden_dyed_type);

			if ($hidden_dyed_type == 2 || $hidden_dyed_type == 0 || $hidden_dyed_type == "") {
				$hidden_dyed_type = 0;
			} else {
				$hidden_dyed_type = $hidden_dyed_type;
			}

			// IF USER CHANGE LOT WHILE UPDATE
			if (str_replace("'", "", $prod_id) != str_replace("'", "", $original_prod_id)) {
				$new_allocate_data = str_replace("'", "", $original_prod_qnty);
				// CHECK PREVIOUS PRODUCT ALLOCATION
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=$original_prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";

				$check_allocation_array = sql_select($sql_allocation);
				$mst_id = $check_allocation_array[0][csf('id')];

				if (!empty($check_allocation_array)) {
					// UPDATE PREVIOUS PRODUCT ALLOCATION MST
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty-$new_allocate_data),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and item_id=$original_prod_id", 0);

					$rID3 = false;
					if ($rID2) {
						// UPDATE PREVIOUS PRODUCT ALLOCATION DTLS
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty-$new_allocate_data),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id='$po_id' and item_id=$original_prod_id", 0);
					}
					// UPDATE PREVIOUS PRODUCT DETAILS
					if ($rID2 && $rID3) // Requsition  and allocation mst and allocation dtls data transection true then
					{
						$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$new_allocate_data),available_qnty=(current_stock-(allocated_qnty-$new_allocate_data)),update_date='" . $pc_date_time . "' where id=$original_prod_id  ", 0);
					}
				}

				// NEW PRODUCT ALLOCATION
				$new_prod_id = str_replace("'", "", $prod_id);
				$pro_allocate_data = str_replace("'", "", $txt_yarn_qnty);
				// CHECK IF NEW PRODUCT ALLOCATION FOUND
				$sql_new_prod_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=$new_prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and status_active=1 and is_deleted=0";
				$check_new_allocation_array = sql_select($sql_new_prod_allocation);
				if (!empty($check_new_allocation_array)) {
					$mst_id = $check_new_allocation_array[0][csf('id')];
					$allocation_qnty = $check_new_allocation_array[0][csf('qnty')] + str_replace("'", "", $txt_yarn_qnty);

					$rID2 = execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and item_id=$prod_id", 0);

					$rID3 = false;
					if ($rID2) {
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and item_id=$prod_id", 0);
					}
					$rID3 = $rID5 = $rID6 = true;
				} else {
					if ($job_no != "") {
						// INSERT NEW ALLOCATION WITH CHANGED PRODUCT
						$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_mst = "(" . $id_allocation . "," . $update_dtls_id . ",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";


						$rID5 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
						$rID6 = false;
						if ($data_array_allocation_dtls != '') {
							$rID6 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
						}
					} else {
						$rID2 = $rID3 = false;
					}
				}

				// UPDATE CHANGED PRODUCT DETAILS
				if ($rID2 && $rID3) // Requsition true then
				{
					$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data),available_qnty=(current_stock-(allocated_qnty+$pro_allocate_data)),update_date='" . $pc_date_time . "' where id=$new_prod_id  ", 0);
				}
			} else {
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $prod_id) . " and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";

				//echo "6**".$sql_allocation; die();

				$check_allocation_array = sql_select($sql_allocation);

				if (!empty($check_allocation_array)) {
					$pro_allocate_data = (str_replace("'", "", $txt_yarn_qnty) - str_replace("'", "", $original_prod_qnty));
					$mst_id = $check_allocation_array[0][csf('id')];
					$allocation_qnty = ($check_allocation_array[0][csf('qnty')] - str_replace("'", "", $original_prod_qnty)) + str_replace("'", "", $txt_yarn_qnty);

					$rID2 = execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and item_id=$prod_id", 0);


					$rID3 = false;
					if ($rID2) {
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and item_id=$prod_id", 0);
					}
				} else {
					if ($job_no != "") {
						// NER YARN ALLOCATION TO SALES ORDER
						$pro_allocate_data = str_replace("'", "", $txt_yarn_qnty);
						$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_mst = "(" . $id_allocation . "," . $update_dtls_id . ",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);


						$rID3 = false;
						if ($data_array_allocation_dtls != '') {
							$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
						}
					} else {
						$rID2 = $rID3 = false;
					}
				}
				// UPDATE PRODUCT DETAILS
				if ($rID2 && $rID3) // Requsition and allocation mst, allocation dtls true then
				{
					$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data),available_qnty=(current_stock-(allocated_qnty+$pro_allocate_data)),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);
				}

				$rID5 = $rID6 = 1;
			}

			$field_array_update = "prod_id*no_of_cone*requisition_date*yarn_qnty*updated_by*update_date";
			$data_array_update = $prod_id . "*" . $txt_no_of_cone . "*" . $txt_reqs_date . "*" . $txt_yarn_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$rID1 = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		} else {
			$rID2 = true;
			$rID3 = true;
			$rID4 = true;
			$rID5 = true;
			$rID6 = true;
			$field_array_update = "prod_id*no_of_cone*requisition_date*yarn_qnty*updated_by*update_date";
			$data_array_update = $prod_id . "*" . $txt_no_of_cone . "*" . $txt_reqs_date . "*" . $txt_yarn_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$rID1 = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		}

		//echo $rID1 ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID5."&&". $rID6; die();
		if ($db_type == 0) {
			if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} else {
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) // Deleted
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if ($db_type == 0) {
			// check if demand found against requisition
			$check_demand_entry = sql_select("select group_concat(b.demand_system_no) as demand_system_no,sum(a.yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls a,ppl_yarn_demand_entry_mst b,ppl_yarn_demand_entry_dtls c where a.dtls_id=c.id and a.mst_id=b.id and a.requisition_no=$txt_requisition_no and a.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		} else {
			// check if demand found against requisition
			$check_demand_entry = sql_select("select listagg(cast(b.demand_system_no as varchar2(4000)), ',') within group (order by b.demand_system_no) as demand_system_no,sum(a.yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls a,ppl_yarn_demand_entry_mst b,ppl_yarn_demand_entry_dtls c where a.dtls_id=c.id and a.mst_id=b.id and a.requisition_no=$txt_requisition_no and a.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		}

		if ($check_demand_entry[0][csf("yarn_demand_qnty")] != "" || $check_demand_entry[0][csf("yarn_demand_qnty")] != null) {
			echo "18**Daily yarn demand found.Requisition can not be deleted.\nDemand Id=" . $check_demand_entry[0][csf("demand_system_no")];
			disconnect($con);
			exit();
		}

		// CHECK IF ISSUE FOUND
		$check_issue = sql_select("select sum(cons_quantity) cons_quantity from inv_transaction where receive_basis=3 and transaction_type=2 and item_category=1 and requisition_no=$txt_requisition_no and prod_id=$original_prod_id and status_active=1 and is_deleted=0");
		if ($check_issue[0][csf("cons_quantity")] > 0 || $check_issue[0][csf("cons_quantity")] != null) {
			echo "17**Issue found.You can not change this lot.";
			disconnect($con);
			exit();
		}

		$txt_yarn_qnty = str_replace("'", "", $txt_yarn_qnty);

		//for vs allocated qty
		$vs_arr = array();
		$vs_arr['company_id'] = $companyID;
		$arr['category_id'] = 1;
		$vs_aq_arr = get_vs_allocated_qty($vs_arr);
		$vs_aq = $vs_aq_arr['is_allocated'];
		//end for vs allocated qty

		//for vs auto_allocation_from_requisition
		$vs_arr = array();
		$vs_arr['company_id'] = $companyID;
		$vs_aafr_arr = get_vs_auto_allocation_from_requisition($vs_arr);
		$vs_aafr = $vs_aafr_arr['is_auto_allocation'];
		//end for vs auto_allocation_from_requisition

		if ($vs_aq == 1 && $vs_aafr == 1) {
			if ($updateId != "") {
				$sql = "select a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id=$updateId group by a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc,c.po_id";
			}

			$planning_array = sql_select($sql);

			$po_id = $planning_array[0][csf('po_id')];
			$booking_no = $planning_array[0][csf('booking_no')];
			$job_no = str_replace("'", "", $job_no);
			$prod_id = str_replace("'", "", $prod_id);

			$sql_allocation = "select a.id,a.qnty from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=$prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";
			$check_allocation_array = sql_select($sql_allocation);


			$field_array_update = "status_active*is_deleted*updated_by*update_date";
			$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$rID = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);

			if (!empty($check_allocation_array)) {
				$mst_id = $check_allocation_array[0][csf('id')];
				$current_allocation = $check_allocation_array[0][csf('qnty')];

				if ($txt_yarn_qnty < $current_allocation) {
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=qnty-$txt_yarn_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
				} else {
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=qnty-$txt_yarn_qnty,status_active=0,is_deleted=1,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
				}

				$rID3 = false;
				if ($rID2) {
					if ($txt_yarn_qnty < $current_allocation) {
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=qnty-$txt_yarn_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id", 0);
					} else {
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=qnty-$txt_yarn_qnty,status_active=0,is_deleted=1,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id", 0);
					}
				}
			}

			$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$txt_yarn_qnty),available_qnty=(current_stock-(allocated_qnty-$txt_yarn_qnty)),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);
		} else {
			$rID2 = true;
			$rID3 = true;
			$rID4 = true;
			$field_array_update = "status_active*is_deleted*updated_by*update_date";
			$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$rID = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		}

		//echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4;die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} else {
				mysql_query("ROLLBACK");
				echo "7**0**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} else {
				oci_rollback($con);
				echo "7**0**1";
			}
		}
		disconnect($con);
		die;
	}
}
?>