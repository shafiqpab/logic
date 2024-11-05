<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.id=b.buyer_id and buy.status_active =1 and buy.is_deleted=0 and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))", "id,buyer_name", 1, "--Select--", "", "");
	exit();
}

if ($action == "fabric_booking_popup") {
	echo load_html_head_contents("Booking Search", "../../../", 1, 1, $unicode);
	//$booking_no.'__'.$app_nessity.'__'.$validate_page.'__'.$allow_partial.'__'.$row_data[17]  //ISD-20-25043 before commit pls contract with Common Team
?>
	<script>
		function js_set_value(booking_data) {
			var exdata = booking_data.split('__');
			var booking_no = exdata[0];
			var app_nessity = exdata[1];
			var validate_page = exdata[2];
			var allow_partial = exdata[3];
			var isapproved = exdata[4];
			var booking_type = exdata[5];
			var is_short = exdata[6];
			var smn_allocation = exdata[7];
			var booking_with_order = exdata[8];
			var po_ids = exdata[9];
			var proceed_knitting = exdata[10];
			var proceed_dyeing = exdata[11];

			document.getElementById('booking_type').value = booking_type;
			document.getElementById('is_short').value = is_short;
			document.getElementById('po_ids').value = po_ids;

			//for proceed_knitting/dyeing
			if (proceed_knitting == 2 && proceed_dyeing == 1) {
				alert("Only Proceed Dyeing has been Checked on Fabric Booking Pages.");
				return;
			}
			//end for proceed_knitting/dyeing

			if (booking_type == 4 && smn_allocation == 2 && booking_with_order == 1) {
				alert("Allocation for Sample Non Order Booking is not allowed. Please check variable settings.");
				return;
			}

			if (app_nessity == 1 && validate_page == 1 && allow_partial == 1) {
				if (isapproved == 1 || isapproved == 3) {
					document.getElementById('selected_booking').value = booking_no;
					parent.emailwindow.hide();
				} else {
					alert("This Booking Is Not Approved.");
					return;
				}
			} else if (app_nessity == 1 && validate_page == 1 && allow_partial == 2) {
				if (isapproved == 1) {
					document.getElementById('selected_booking').value = booking_no;
					parent.emailwindow.hide();
				} else if (isapproved == 3) {
					alert("This Booking Is Not Full Approved.");
					return;
				} else {
					alert("This Booking Is Not Approved.");
					return;
				}
			} else // if(app_nessity==1 && validate_page==2)
			{
				document.getElementById('selected_booking').value = booking_no;
				parent.emailwindow.hide();
			}

		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="1250" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
					<thead>
						<th class="must_entry_caption">Company Name</th>
						<th>Buyer Name</th>
						<th>Booking No</th>
						<th>Job No</th>
						<th>Style Ref</th>
						<th>IR/CN</th>
						<th>File No</th>
						<th>Date Range</th>
						<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						</th>
					</thead>
					<tr class="general">
						<td><input type="hidden" id="selected_booking">
							<input type="hidden" id="booking_type">
							<input type="hidden" id="is_short">
							<input type="hidden" id="po_ids">
							<?
							echo create_drop_down("cbo_company_mst", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'yarn_allocation_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
						</td>
						<td id="buyer_td"> <? echo create_drop_down("cbo_buyer_name", 170, $blank_array, "", 1, "--Select--"); ?></td>
						<td>
							<input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
						</td>
						<td>
							<input type="text" style="width:100px" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />
						</td>
						<td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
						<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+ document.getElementById('cbo_year_selection').value+'_'+ document.getElementById('txt_job_no').value+'_'+ document.getElementById('txt_style_ref').value, 'create_booking_search_list_view', 'search_div', 'yarn_allocation_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if ($action == "create_booking_search_list_view") {
	$data = explode('_', $data);
	if ($data[0] != 0) $company = "  a.company_id='$data[0]'";
	else {
		echo "<span style='color:red; font-size:14px;'>Please Select Company First.</span>";
		die;
	}
	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] > 0) $buyer = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else $buyer_id_cond = "";
		} else {
			$buyer = "";
		}
	} else {
		$buyer = " and a.buyer_id='$data[1]'";
	}

	if (trim($data[4]) == "")
		$search_field_cond = "";
	else
		$search_field_cond = " and a.booking_no like '%" . trim($data[4]) . "'";
	$internal_ref = trim($data[5]);
	$file_no = trim($data[6]);
	if ($file_no == "")
		$file_no_cond = "";
	else
		$file_no_cond = " and b.file_no='" . trim($file_no) . "' ";

	if ($internal_ref == "")
		$internal_ref_cond = "";
	else
		$internal_ref_cond = " and b.grouping='" . trim($internal_ref) . "' ";
	$internal_ref_cond2 = " and d.internal_ref='" . trim($internal_ref) . "' ";

	if ($data[2] != "" && $data[3] != "") {
		if ($db_type == 0) {
			$booking_date = "and a.booking_date between '" . change_date_format($data[2], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[3], "yyyy-mm-dd", "-") . "'";
		} else {
			$booking_date = "and a.booking_date between '" . change_date_format($data[2], '', '', 1) . "' and '" . change_date_format($data[3], '', '', 1) . "'";
		}
	} else {
		$booking_date = "";
		$year_id = $data[7];

		if ($db_type == 0) {
			if ($year_id != 0) {
				$bookin_year_cond = " and YEAR(a.insert_date)=$year_id";
				$bookin_year_cond_2 = " and YEAR(a.insert_date)=$year_id";
			} else {
				$bookin_year_cond = "";
				$bookin_year_cond_2 = "";
			}
		} else if ($db_type == 2) {
			if ($year_id != 0) {
				$bookin_year_cond = " and to_char(a.insert_date,'YYYY')=$year_id";
				$bookin_year_cond_2 = " and to_char(a.insert_date,'YYYY')=$year_id";
			} else {
				$bookin_year_cond = "";
				$bookin_year_cond_2 = "";
			}
		}
	}

	//for job no
	if (trim($data[8]) == "")
		$job_no_cond = "";
	else
		$job_no_cond = " and c.job_no like '%" . trim($data[8]) . "'";

	//for style ref
	if (trim($data[9]) == "") {
		$style_ref_cond = "";
	} else {
		$style_ref_cond = " and d.style_ref_no like '%" . trim($data[9]) . "'";
	}

	if ($data[1] == 0 && trim($data[4]) == "" && trim($data[8]) == ""  && trim($data[9]) == ""  && trim($data[5]) == "" && trim($data[6]) == "") {

		if ($data[2] == "" && $data[3] == "") {
			echo "<span style='color:red; font-size:14px;'>Please select either date range or any one reference</span>";
			die();
		}
	}

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$suplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
		$job_field = "a.job_no  as job_no ,";
		$cast_po_break_down_id  = "cast(c.po_break_down_id as NUMBER(11)) po_break_down_id";
		$cast_po_break_down_id2 = "cast(a.po_break_down_id as NUMBER(11)) po_break_down_id";
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year,";
		$job_field = "cast( a.job_no as nvarchar2(25) ),";
		$cast_po_break_down_id  = "cast(c.po_break_down_id as NUMBER(11)) po_break_down_id";
		$cast_po_break_down_id2 = "cast(a.po_break_down_id as NUMBER(11)) po_break_down_id";
	} else $year_field = "";

	$booking_arr = array();
	$booking_no_arr = array();

	$sql = sql_select("select b.approval_need, b.allow_partial, b.validate_page, b.page_id from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date ASC"); // Fabric Booking(5),  Short Fabric Booking(6)

	$app_nessity_arr = array();
	foreach ($sql as $row) {
		$app_nessity_arr[$row[csf('page_id')]]['approval_need'] = $row[csf('approval_need')];
		$app_nessity_arr[$row[csf('page_id')]]['allow_partial'] = $row[csf('allow_partial')];
		$app_nessity_arr[$row[csf('page_id')]]['validate_page'] = $row[csf('validate_page')];
	}
	//echo '<pre>';print_r($app_nessity_arr);

	// check variable settings if allocation is available or not
	$variable_set_allocation = return_field_value("smn_allocation", "variable_settings_inventory", "company_name=$data[0] and variable_list=18 and item_category_id = 1");


	$bookingAppCond = ""; //ISD-20-25043 before commit pls contract with Common Team
	/*if($app_nessity==1 && $validate_page==1){
		$bookingAppCond=" and a.is_approved in (1,3)";
	}
	*/
	if ($internal_ref_cond != "" || $job_no_cond != '') {
		$sql = "select a.booking_no_prefix_num, a.is_approved, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.buyer_id,a.pay_mode, c.job_no, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number, 0 as booking_with_order, a.proceed_knitting, a.proceed_dyeing from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b where c.po_break_down_id=b.id and a.booking_no=c.booking_no and c.job_no= b.job_no_mst and $company $buyer $booking_date $bookin_year_cond $search_field_cond  and a.booking_type in(1,4) and c.booking_type in (1,4) and a.status_active=1 and a.fabric_source!=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $file_no_cond $internal_ref_cond $job_no_cond group by a.id, a.booking_no_prefix_num, a.is_approved, a.insert_date, a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.pay_mode, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number, a.proceed_knitting, a.proceed_dyeing

		union all

		select a.booking_no_prefix_num, a.is_approved, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.buyer_id,a.pay_mode, c.job_no, $cast_po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number, 0 as booking_with_order, a.proceed_knitting, a.proceed_dyeing from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b, wo_po_details_master d where c.po_break_down_id=b.id and a.booking_no=c.booking_no and c.job_no=b.job_no_mst and a.job_no=d.job_no and b.job_no_mst=d.job_no and c.job_no=d.job_no and $company $buyer $booking_date $bookin_year_cond $search_field_cond and a.booking_type in(1,4) and a.status_active=1 and a.fabric_source!=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $file_no_cond $internal_ref_cond $style_ref_cond $job_no_cond  group by a.booking_no_prefix_num, a.is_approved, a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.pay_mode, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number, a.proceed_knitting, a.proceed_dyeing

		union all

		select a.booking_no_prefix_num, a.is_approved, null as grouping, null as file_no, $year_field a.booking_no, a.booking_date, a.booking_type, null as is_short,a.company_id, a.buyer_id,a.pay_mode, $job_field $cast_po_break_down_id2, a.id, a.item_category, a.fabric_source, a.supplier_id,null as po_number, 1 as booking_with_order, 2 as proceed_knitting, 2 as proceed_dyeing from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst d where a.booking_no=b.booking_no and b.style_id = d.id and $company $buyer $booking_date $bookin_year_cond_2 $search_field_cond and a.booking_type=4 and a.status_active=1 and a.fabric_source!=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_ref_cond $internal_ref_cond2 group by a.booking_no_prefix_num, a.is_approved, a.insert_date, a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.pay_mode, a.job_no, a.booking_type, a.is_short, a.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id order by id desc";
		// echo $sql;
	} else {
		if ($style_ref_cond == '') {
			$sql = "select a.booking_no_prefix_num, a.is_approved, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.buyer_id,a.pay_mode, c.job_no, $cast_po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number, 0 as booking_with_order, a.proceed_knitting, a.proceed_dyeing from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b where c.po_break_down_id=b.id and a.booking_no=c.booking_no and c.job_no=b.job_no_mst and $company $buyer $booking_date $bookin_year_cond $search_field_cond  and a.booking_type in(1,4) and a.status_active=1 and a.fabric_source!=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $file_no_cond $internal_ref_cond group by a.booking_no_prefix_num, a.is_approved, a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.pay_mode, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number, a.proceed_knitting, a.proceed_dyeing
			union all
			select a.booking_no_prefix_num, a.is_approved, null as grouping, null as file_no, $year_field a.booking_no, a.booking_date, a.booking_type, null as is_short,a.company_id, a.buyer_id,a.pay_mode, $job_field $cast_po_break_down_id2, a.id, a.item_category, a.fabric_source, a.supplier_id,null as po_number, 1 as booking_with_order, 2 as proceed_knitting, 2 as proceed_dyeing from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and $company $buyer $booking_date $bookin_year_cond_2 $search_field_cond and a.booking_type=4 and a.status_active=1 and a.fabric_source!=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no_prefix_num, a.is_approved, a.insert_date, a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.pay_mode, a.job_no, a.booking_type, a.is_short, a.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id order by id desc";

			//echo $sql;
		} else {
			$sql = "select a.booking_no_prefix_num, a.is_approved, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.buyer_id,a.pay_mode, c.job_no, $cast_po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number, 0 as booking_with_order, a.proceed_knitting, a.proceed_dyeing from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b, wo_po_details_master d where c.po_break_down_id=b.id and a.booking_no=c.booking_no and c.job_no=b.job_no_mst and a.job_no=d.job_no and b.job_no_mst=d.job_no and c.job_no=d.job_no and $company $buyer $booking_date $bookin_year_cond $search_field_cond and a.booking_type in(1,4) and a.status_active=1 and a.fabric_source!=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $file_no_cond $internal_ref_cond $style_ref_cond group by a.booking_no_prefix_num, a.is_approved, a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.pay_mode, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number, a.proceed_knitting, a.proceed_dyeing
			union all
			select a.booking_no_prefix_num, a.is_approved, null as grouping, null as file_no, $year_field a.booking_no, a.booking_date, a.booking_type, null as is_short,a.company_id, a.buyer_id,a.pay_mode, $job_field $cast_po_break_down_id2, a.id, a.item_category, a.fabric_source, a.supplier_id,null as po_number, 1 as booking_with_order, 2 as proceed_knitting, 2 as proceed_dyeing from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst d where a.booking_no=b.booking_no and b.style_id = d.id and $company $buyer $booking_date $bookin_year_cond_2 $search_field_cond and a.booking_type=4 and a.status_active=1 and a.fabric_source!=2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_ref_cond group by a.booking_no_prefix_num, a.is_approved, a.insert_date, a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.pay_mode, a.job_no, a.booking_type, a.is_short, a.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id order by id desc";

			//echo $sql;
		}
	}

	// echo $sql;
	$result = sql_select($sql);

	// for checking Reference Closing

	$sql_2 = "SELECT DISTINCT INV_PUR_REQ_MST_ID, CLOSING_STATUS FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 AND insert_date IN (  SELECT MAX (insert_date) FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 GROUP BY INV_PUR_REQ_MST_ID)";
	// echo $sql_2;

	$result_2 = sql_select($sql_2);

	foreach ($result as $key => $row) {
		foreach ($result_2 as $val) {
			if (($row['ID'] == $val['INV_PUR_REQ_MST_ID']) && ($val['CLOSING_STATUS'] == 1)) {
				// echo $row['ID']."  ";
				unset($result[$key]);
			}
		}
	}

	$po_break_down_ids = "";
	foreach ($result as $row) {
		$booking_no_arr[$row[csf("booking_no")]]["po_number"] .= $row[csf("po_number")] . ",";
		$booking_no_arr[$row[csf("booking_no")]]["job_no"] .= $row[csf("job_no")] . ",";

		if ($po_break_down_ids == "") {
			$po_break_down_ids = $row[csf("po_break_down_id")];
		} else {
			$po_break_down_ids .= "," . $row[csf("po_break_down_id")];
		}

		$booking_arr[$row[csf("booking_no")]] = $row[csf("booking_no_prefix_num")] . "**" . $row[csf("grouping")] . "**" . $row[csf("file_no")] . "**" . $row[csf("year")] . "**" . $row[csf("booking_no")] . "**" . $row[csf("booking_date")] . "**" . $row[csf("booking_type")] . "**" . $row[csf("is_short")] . "**" . $row[csf("company_id")] . "**" . $row[csf("buyer_id")] . "**" . $row[csf("job_no")] . "**" . $po_break_down_ids . "**" . $row[csf("id")] . "**" . $row[csf("item_category")] . "**" . $row[csf("fabric_source")] . "**" . $row[csf("supplier_id")] . "**" . $row[csf("pay_mode")] . "**" . $row[csf("is_approved")] . "**" . $row[csf("booking_with_order")] . "**" . $row[csf("proceed_knitting")] . "**" . $row[csf("proceed_dyeing")];
	}


	$po_break_down_ids = chop($po_break_down_ids, ",");
	$po_break_down_ids = implode(",", array_filter(array_unique(explode(",", $po_break_down_ids))));
	if ($po_break_down_ids != "") {
		$po_break_down_ids = explode(",", $po_break_down_ids);
		$po_break_down_ids_chnk = array_chunk($po_break_down_ids, 999);
		$po_break_down_ids_cond = " and";
		foreach ($po_break_down_ids_chnk as $dtls_id) {
			if ($po_break_down_ids_cond == " and")  $po_break_down_ids_cond .= "(id in(" . implode(',', $dtls_id) . ")";
			else $po_break_down_ids_cond .= " or id in(" . implode(',', $dtls_id) . ")";
		}
		$po_break_down_ids_cond .= ")";
		//echo $po_break_down_ids_cond;die;
		$sql_po_data = sql_select("select id,po_number,grouping from wo_po_break_down where status_active=1 and is_deleted=0 $po_break_down_ids_cond");
	}
	foreach ($sql_po_data as $row) {
		$po_data_arr[$row[csf("id")]]["po_number"] = $row[csf("po_number")];
		$po_data_arr[$row[csf("id")]]["grouping"] = $row[csf("grouping")];
	}
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="50">Year</th>
			<th width="70">Booking No</th>
			<th width="60">Type</th>
			<th width="80">Booking Date</th>
			<th width="60">Buyer</th>
			<th width="60">Job No.</th>
			<th width="180">PO number</th>
			<th width="110">Fabric Nature</th>
			<th width="80">Fabric Source</th>
			<th width="80">Supplier</th>
			<th width="80">Internal Ref</th>
			<th>File No</th>
		</thead>
	</table>
	<div style="width:1060px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="list_view">
			<?
			$i = 1;

			foreach ($booking_arr as $booking_no => $row) {
				$app_nessity = 2;
				$validate_page = 0;
				$allow_partial = 2;
				$row_data = array();
				$row_data = explode("**", $row);
				if ($row_data[17] == 1) {
					$bgcolor = "#00FF00";
					$appTitel = "Full Approved";
				} else if ($row_data[17] == 3) {
					$bgcolor = "#FFCC99";
					$appTitel = "Partial Approved";
				} else {
					$bgcolor = "#ffa38f";
					$appTitel = "Not Approved";
				}

				// Fabric Booking
				$app_nessity = $app_nessity_arr[5]['approval_need'];
				$allow_partial = $app_nessity_arr[5]['allow_partial'];
				$validate_page = $app_nessity_arr[5]['validate_page'];

				if ($row_data[6] == 4) $booking_type = "Sample";
				else {
					if ($row_data[7] == 1) {
						$booking_type = "Short";
						$app_nessity = $app_nessity_arr[6]['approval_need'];
						$allow_partial = $app_nessity_arr[6]['allow_partial'];
						$validate_page = $app_nessity_arr[6]['validate_page'];
					} else $booking_type = "Main";
				}
				$job_no = rtrim(implode(",", array_unique(explode(",", $booking_no_arr[$booking_no]["job_no"]))), ",");

				$poEx = array();
				$poEx = explode(",", $row_data[11]);

				$poNumberS = "";
				$interRef = "";
				$po_ids = '';
				foreach ($poEx as $poIds) {
					if ($row_data[18] != 1) {
						$po_ids .= $poIds . ",";
						$poNumberS .= $po_data_arr[$poIds]["po_number"] . ",";
						$interRef .= $po_data_arr[$poIds]["grouping"] . ",";
					}
				}
				$po_ids = chop($po_ids, ",");
			?>
				<tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $booking_no . '__' . $app_nessity . '__' . $validate_page . '__' . $allow_partial . '__' . $row_data[17] . '__' . $row_data[6] . '__' . $row_data[7] . '__' . $variable_set_allocation . '__' . $row_data[18] . '__' . $po_ids . '__' . $row_data[19] . '__' . $row_data[20]; ?>');">
					<td width="40" title="<?= $appTitel; ?>" align="center"><? echo $i; ?></td>
					<td width="50" align="center"><? echo $row_data[3]; //year
													?></td>
					<td width="70" align="center"><? echo $row_data[0]; ?></td>
					<td width="60" align="center">
						<p>&nbsp;<? echo $booking_type; ?></p>
					</td>
					<td width="80" align="center">
						<p><? echo change_date_format($row_data[5]); ?></p>
					</td>
					<td width="60">
						<p><? echo $buyer_arr[$row_data[9]]; ?></p>
					</td>
					<td width="60">
						<p><? echo $job_no; ?></p>
					</td>
					<td width="180" Style="max-width:180px;">
						<p><?
							echo chop($poNumberS, ",");
							//echo $po_data_arr[$row_data[11]]["po_number"];
							//rtrim($booking_no_arr[$booking_no]["po_number"], ",");
							?>&nbsp;</p>
					</td>
					<td width="110">
						<p><? echo $item_category[$row_data[13]]; ?>&nbsp;</p>
					</td>
					<td width="80" align="center">
						<p><? echo $fabric_source[$row_data[14]]; ?>&nbsp;</p>
					</td>
					<td width="80">
						<p><? echo ($row_data[16] == 5 || $row_data[16] == 3) ? $comp[$row_data[15]] : $suplier[$row_data[15]]; ?></p>
					</td>
					<td width="80" Style="max-width:80px;">
						<p><?
							$interREF = implode(",", array_unique(explode(",", $interRef)));
							echo chop($interREF, ",");
							//echo $po_data_arr[$row_data[11]]["grouping"];
							//echo $row_data[1];
							?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $row_data[2]; ?></p>
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

if ($action == "populate_data_from_search_popup") {
	$booking_arr = array();

	$sql = "select b.booking_no,a.company_id,a.buyer_id,b.po_break_down_id,sum(b.grey_fab_qnty) as booking_qnty,b.job_no,c.po_number,a.entry_form, a.booking_type, a.is_short, 0 as is_without_order from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and b.booking_no='$data' and a.status_active=1 and b.is_deleted=0 group by b.booking_no,a.company_id,a.buyer_id,b.po_break_down_id,b.job_no,c.po_number,a.entry_form, a.booking_type, a.is_short
	union all
	select b.booking_no,a.company_id,a.buyer_id,null as order_no,sum(b.grey_fabric) as booking_qnty,null as job_no,null as po_number,a.entry_form_id as entry_form, a.booking_type, a.is_short, 1 as is_without_order from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and  b.booking_no ='$data' and b.status_active=1 and b.is_deleted=0 group by b.booking_no,a.company_id,a.buyer_id,a.entry_form_id, a.booking_type, a.is_short";

	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$booking_arr[$row[csf("booking_no")]]["booking_info"] += $row[csf("booking_qnty")];
		$booking_arr[$row[csf("booking_no")]]["job_nos"] .= $row[csf("job_no")] . ",";
		$booking_arr[$row[csf("booking_no")]]["order_ids"] .= $row[csf("po_break_down_id")] . ",";
		$booking_arr[$row[csf("booking_no")]]["po_number"] .= $row[csf("po_number")] . ",";
		$booking_arr[$row[csf("booking_no")]]["company_id"] = $row[csf("company_id")];
		$booking_arr[$row[csf("booking_no")]]["buyer_id"] = $row[csf("buyer_id")];
		$booking_arr[$row[csf("booking_no")]]["entry_form"] = $row[csf("entry_form")];
		$booking_arr[$row[csf("booking_no")]]["booking_type"] = $row[csf("booking_type")];
		$booking_arr[$row[csf("booking_no")]]["is_short"] = $row[csf("is_short")];
		$booking_arr[$row[csf("booking_no")]]["is_without_order"] = $row[csf("is_without_order")];
	}

	$job_nos = implode(",", array_unique(explode(",", rtrim($booking_arr[$data]["job_nos"], ","))));
	$order_ids = rtrim($booking_arr[$data]["order_ids"], ",");
	$po_number = rtrim($booking_arr[$data]["po_number"], ",");
	$booking_info = explode("**", $booking_arr[$data]["booking_info"]);

	echo "load_drop_down( 'requires/yarn_allocation_controller', '" . $booking_arr[$data]["company_id"] . "', 'load_drop_down_buyer', 'buyer_td' ) ;\n";

	echo "document.getElementById('txt_booking_no').value = '" . $row[csf("booking_no")] . "';\n";
	echo "document.getElementById('txt_job_no').value = '" . $job_nos . "';\n";
	echo "document.getElementById('cbo_company_name').value = '" . $booking_arr[$data]["company_id"] . "';\n";
	echo "document.getElementById('cbo_buyer_name').value = '" . $booking_arr[$data]["buyer_id"] . "';\n";
	echo "document.getElementById('txt_booking_qnty').value = '" . $booking_arr[$data]["booking_info"] . "';\n";
	echo "document.getElementById('txt_entry_form').value = '" . $booking_arr[$data]["entry_form"] . "';\n";
	echo "document.getElementById('txt_order_no').value = '" . $po_number . "';\n";
	echo "document.getElementById('txt_order_id').value = '" . $order_ids . "';\n";
	echo "$('#cbo_buyer_name').attr('disabled','true')" . ";\n";

	echo "document.getElementById('hdn_booking_type').value = '" . $booking_arr[$data]["booking_type"] . "';\n";
	echo "document.getElementById('hdn_is_short').value = '" . $booking_arr[$data]["is_short"] . "';\n";
	echo "document.getElementById('hdn_is_without_order').value = '" . $booking_arr[$data]["is_without_order"] . "';\n";

	if ($order_ids == "") {
		echo "$('#caption_job_no font').css('color','black')" . ";\n";
		echo "$('#txt_qnty').removeAttr('onclick')" . ";\n";
		echo "$('#txt_qnty').attr('placeholder','write')" . ";\n";
		echo "$('#txt_qnty').attr('readonly', false); " . ";\n";
	} else {
		echo "$('#caption_job_no font').css('color','blue')" . ";\n";
		echo "$('#txt_qnty').attr('onClick','open_qnty_popup(\"requires/yarn_allocation_controller.php?action=open_qnty_popup\")')\n";
		echo "$('#txt_qnty').attr('placeholder','click')" . ";\n";
		echo "$('#txt_qnty').attr('readonly', true); " . ";\n";
	}

	$varialble_setting = sql_select("select during_issue,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name=" . $booking_arr[$data]["company_id"] . "  and variable_list=25 and status_active=1 and is_deleted=0");

	$during_issue = $varialble_setting[0][csf('during_issue')];
	$control_level = $varialble_setting[0][csf('user_given_code_status')];
	$tolerant_percent = $varialble_setting[0][csf('tolerant_percent')];

	echo "document.getElementById('during_issue').value = '" . $during_issue . "';\n";
	echo "document.getElementById('control_level').value = '" . $control_level . "';\n";
	echo "document.getElementById('tolerant_percent').value = '" . $tolerant_percent . "';\n";

	exit();
}

if ($action == "fabric_description_list") {
?>
	<table cellspacing="0" width="340" class="rpt_table" border="0" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="220">Fabric Description</th>
				<th width="90">Booking Qnty</th>
			</tr>
		</thead>
		<?
		if ($db_type == 0) {
			$select_field = "group_concat(distinct(b.po_break_down_id)) as order_no ";
			$sql = "select $select_field,a.item_number_id,a.fabric_description,a.gsm_weight,a.width_dia_type,sum(b.grey_fab_qnty) as grey_fab_qnty,a.lib_yarn_count_deter_id
			from
			wo_pre_cost_fabric_cost_dtls a,
			wo_booking_dtls b
			where
			a.job_no=b.job_no and
			a.id= b.pre_cost_fabric_cost_dtls_id and
			b.booking_no ='$data' and
			a.status_active=1
			and a.is_deleted=0
			and b.status_active=1
			and b.is_deleted=0 group by a.id,a.lib_yarn_count_deter_id

			union all
			select null as order_no,0 as item_number_id,b.fabric_description, b.gsm_weight,0 as width_dia_type,sum(b.grey_fabric) as grey_fab_qnty,b.lib_yarn_count_deter_id
			from
			wo_non_ord_samp_booking_dtls b
			where
			b.booking_no ='$data' and
			b.status_active=1 and
			b.is_deleted=0
			group by b.fabric_description, b.gsm_weight,b.lib_yarn_count_deter_id

			";
		} else {
			$select_field = "RTRIM(XMLAGG(XMLELEMENT(e,b.po_break_down_id,',').EXTRACT('//text()') ORDER BY b.po_break_down_id).GETCLOBVAL(),',') as order_no";
			$sql = "select $select_field,a.item_number_id,a.fabric_description, a.gsm_weight, a.width_dia_type,sum(b.grey_fab_qnty) as grey_fab_qnty,a.lib_yarn_count_deter_id
			from
			wo_pre_cost_fabric_cost_dtls a,
			wo_booking_dtls b
			where
			a.job_no=b.job_no and
			a.id= b.pre_cost_fabric_cost_dtls_id and
			b.booking_no ='$data' and
			b.status_active=1 and
			b.is_deleted=0 and
			a.status_active=1 and
			a.is_deleted=0
			group by a.item_number_id,a.fabric_description, a.gsm_weight, a.width_dia_type,a.lib_yarn_count_deter_id
			union all
			select null as order_no,0 as item_number_id,b.fabric_description, b.gsm_weight,0 as width_dia_type,sum(b.grey_fabric) as grey_fab_qnty,b.lib_yarn_count_deter_id
			from
			wo_non_ord_samp_booking_dtls b
			where
			b.booking_no ='$data' and
			b.status_active=1 and
			b.is_deleted=0
			group by b.fabric_description, b.gsm_weight,b.lib_yarn_count_deter_id
			";
		}
		//echo $sql;
		$DataArray = sql_select($sql);
		$i = 1;
		$total_qnty = 0;
		foreach ($DataArray as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";

			if ($db_type == 2) {
				if (!empty($row[csf("order_no")])) {
					$row[csf("order_no")] = $row[csf("order_no")]->load();
				}
			}

			$po_ids = implode(",", array_unique(explode(",", $row[csf('order_no')])));
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="fab_list_<? echo $i; ?>">
				<td><? echo $i; ?></td>
				<td>
					<div onClick="fabric_order_list(<? echo $i; ?>)" style="text-decoration: underline; cursor: pointer;">
						<? echo $row[csf("fabric_description")] . "," . $row[csf("gsm_weight")] . "," . $fabric_typee[$row[csf("width_dia_type")]]; ?>
					</div>
					<input type="hidden" id="order_no_<? echo $i; ?>" value="<? echo $po_ids; ?>" />
					<input type="hidden" id="lib_yarn_count_deter_id_<? echo $i; ?>" value="<? echo $row[csf("lib_yarn_count_deter_id")]; ?>" />
					<input type="hidden" id="item_number_id_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>" />
					<input type="hidden" id="gsm_weight_<? echo $i; ?>" value="<? echo $row[csf('gsm_weight')]; ?>" />
					<input type="hidden" id="width_dia_type_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')]; ?>" />
				</td>
				<td align="right">
					<input type="hidden" id="fab_booking_qnty_<? echo $i; ?>" value="<? echo $row[csf('grey_fab_qnty')]; ?>" />
					<? echo number_format($row[csf("grey_fab_qnty")], 2);
					$total_qnty += $row[csf("grey_fab_qnty")]; ?>
				</td>
			</tr>
		<?
			$i++;
		}
		?>
		<tfoot>
			<th></th>
			<th></th>
			<th><? echo number_format($total_qnty, 2); ?></th>
		</tfoot>
	</table>
<?
	exit();
}

if ($action == "fabric_order_list") {
	extract($_REQUEST);
?>
	<div align="center">
		<table width="340" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
			<thead>
				<th>SL No</th>
				<th>Job No</th>
				<th>Style</th>
				<th>Po Number</th>
				<th>Booking Qnty</th>
			</thead>
			<?php
			$data = explode("_", $data);
			$job_no = explode(",", $data[1]);
			foreach ($job_no as $job) {
				$job_arr[] = "'" . $job . "'";
			}
			$job_cond = " and c.job_no in(" . implode(",", $job_arr) . ")";
			$po_ids = implode(",", array_unique(explode(",", $data[5])));
			$fabric_order_list_sql = "select c.job_no,c.style_ref_no,a.po_number,sum(b.grey_fab_qnty) grey_fab_qnty from wo_po_break_down a,wo_booking_dtls b,wo_po_details_master c,wo_pre_cost_fabric_cost_dtls d where a.id=b.po_break_down_id and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=d.id $job_cond and d.item_number_id=$data[2] and d.gsm_weight=$data[3] and d.width_dia_type=$data[4] and d.lib_yarn_count_deter_id=$data[6] and a.id in($po_ids) and a.status_active=1 and b.status_active=1 and c.status_active=1  and d.status_active=1 group by c.job_no,c.style_ref_no,a.po_number,b.fabric_description";
			$fabric_order_list = sql_select($fabric_order_list_sql);
			$i = 1;
			$total = 0;
			foreach ($fabric_order_list as $row) {
			?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("job_no")]; ?></td>
					<td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
					<td align="center"><? echo $row[csf("po_number")]; ?></td>
					<td align="right"><? echo number_format($row[csf("grey_fab_qnty")], 2, ".", ""); ?></td>
				</tr>
			<?
				$total += $row[csf("grey_fab_qnty")];
				$i++;
			}
			?>
			<tr style="font-weight: bold;">
				<td colspan="4" align="right">Total= </td>
				<td align="right"><? echo number_format($total, 2, ".", ""); ?></td>
			</tr>
		</table>
	</div>
<?
}


if ($action == "yarn_description_list") {
	include('../../../includes/class4/class.conditions.php');
	include('../../../includes/class4/class.reports.php');
	include('../../../includes/class4/class.fabrics.php');
	include('../../../includes/class4/class.yarns.php');
?>
	<table cellspacing="0" width="340" class="rpt_table" border="0" rules="all">
		<thead>
			<th width="30">SL</th>
			<th width="220">Yarn Description</th>
			<th width="90">Cons/ Dzn</th>
			<th width="90">Required</th>
		</thead>
		<?

		$count_array = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", "id", "yarn_count");

		$data_arr = explode("_", $data);
		$job_nos = explode(",", $data_arr[0]);
		$txt_order_no_id = $data_arr[1];
		$txt_booking_no = "'" . $data_arr[2] . "'";

		if (count($job_nos) > 1) {
			foreach ($job_nos as $job) {
				$jobs[] = "'" . $job . "'";
			}

			$job_cond = " and a.job_no in(" . implode(",", $jobs) . ")";
			$job_no = implode(",", $jobs);
		} else {
			$job_no = "'" . $data_arr[0] . "'";
			$job_cond = " and a.job_no in($job_no)";
		}

		$condition = new condition();
		if (str_replace("'", "", $job_nos) != '') {
			$condition->job_no("in($job_no)");
		}

		if ($txt_order_no_id != '') {
			$condition->po_id("in($txt_order_no_id)");
		}

		$condition->init();
		$yarn = new yarn($condition);
		$yarn_data_array = $yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		//echo $yarn->getQuery(); die();
		//echo "<pre>";
		//print_r($yarn_data_array);

		if ($txt_order_no_id != '') {
			$sql = "SELECT MIN (a.id) AS id,a.count_id,a.copm_one_id,a.percent_one,a.copm_two_id,a.percent_two,a.color,a.type_id,MIN (a.cons_ratio)  AS cons_ratio,SUM (a.cons_qnty) AS cons_qnty,a.rate,SUM (a.amount)  AS amount FROM wo_pre_cost_fab_yarn_cost_dtls a WHERE a.status_active = 1 AND a.is_deleted = 0 $job_cond GROUP BY a.count_id,a.copm_one_id,a.percent_one,a.copm_two_id,a.percent_two,a.color,a.type_id,a.rate";
		} else {
			$sql = "select a.count_id,a.copm_one_id,a.percent_one,a.copm_two_id,a.percent_two,a.type_id,a.color,a.rate,a.cons_ratio,a.cons_qnty,b.uom from wo_pre_cost_fab_yarn_cost_dtls a,wo_pre_cost_fabric_cost_dtls b where a.fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 $job_cond group by a.count_id,a.copm_one_id,a.percent_one,a.copm_two_id,a.percent_two,a.typ  e_id,a.color,a.rate,a.cons_ratio,a.cons_qnty,b.uom";
		}

		//echo $sql;
		$dataarray = sql_select($sql);
		$i = 1;
		foreach ($dataarray as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";

			$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];

			$cons_qnty = number_format($row[csf("cons_qnty")], 3);

		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $i; ?></td>
				<td>
					<?
					echo $count_array[$row[csf("count_id")]];
					if ($row[csf("copm_one_id")] != 0 || $row[csf("copm_one_id")] != "") {
						echo "," . $composition[$row[csf("copm_one_id")]];
					}

					if ($row[csf("percent_one")] != 0 || $row[csf("percent_one")] != "") {
						echo "," . $row[csf("percent_one")] . "%";
					}

					if ($row[csf("copm_two_id")] != 0) {
						echo "," . $composition[$row[csf("copm_two_id")]];
					}

					if ($row[csf("percent_two")] != 0) {
						echo "," . $row[csf("percent_two")] . "%";
					}

					if ($row[csf("type_id")] != 0 || $row[csf("type_id")] != "") {
						echo "," . $yarn_type[$row[csf("type_id")]];
					}

					if ($row[csf("cons_ratio")] != 0 || $row[csf("cons_ratio")] != "") {
						echo "," . $row[csf("cons_ratio")] . "%";
					}
					?>
				</td>
				<td align="right"><? echo $cons_qnty; ?></td>
				<td align="right"><? echo number_format($rowcons_qnty, 2); ?></td>
			</tr>
		<?
			$i++;
		}
		?>
	</table>
<?
	exit();
}

if ($action == "storeStockPopup") {
	extract($_REQUEST);
	$sql = "select  a.store_id,a.transaction_type,a.cons_quantity,b.product_name_details,b.lot,a.cons_uom,b.id,c.store_name
	from inv_transaction a, product_details_master b, lib_store_location c
	where a.prod_id = b.id and a.store_id = c.id and a.prod_id = $prod_id
	and a.status_active = 1 and b.status_active = 1";
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

if ($action == "buyerPopup") {
	extract($_REQUEST);
	// echo $buyer;
	$buyer_arr = explode(",", $buyer);
?>
	<table width="340" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
		<thead>
			<tr>
				<th width="340" style="background: #202a48;color: white;height: 35px;font-weight: bold;">
					<p>Buyer Name</p>
				</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			foreach ($buyer_arr as $key => $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $row ?></td>
				</tr>
			<?
				$i++;
			}
			?>
		</tbody>

	</table>
<?
}

if ($action == "open_item_popup") {
	echo load_html_head_contents("Alocated Search", "../../../", 1, 1, $unicode,1,'');
	extract($_REQUEST);
	$company_name = str_replace("'", "", $cbo_company_name);
	$item_category = str_replace("'", "", $cbo_item_category);
	$job_no = str_replace("'", "", $job_no);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
?>
	<script>
		function js_set_value(str, selectable, job_no, ratep, vs_yrn_mandatory, yrn_tested, tr, vs_yrn_approve_mandatory, yrntest_is_approved, vs_allocation_control, vs_minimum_available_qty, vs_age_limit) {
			var str_array = str.split("**");

			var prod_id = str_array[0];
			var product_name = str_array[1];
			var availableQty = str_array[2];
			var uom = str_array[3];
			var dyed_type = str_array[4];
			var count_id = str_array[5];
			var composition_id = str_array[6];
			var yarn_type = str_array[7];
			var color_id = str_array[8];
			var age_days = str_array[9];

			//for yarn test
			if (vs_yrn_mandatory == 1) {
				alert(yrn_tested);
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
			//end for yarn test

			if (selectable == 0) {
				var booking_no = $('#txt_booking_no').val();
				var buyer = $('#cbo_buyer_name').val();
				var yarn_rate = ratep;
				var page_link = 'yarn_allocation_controller.php?action=budget_yarn_comparision_popup&job_no=' + job_no + '&booking_no=' + booking_no + '&buyer=' + buyer + '&prod_id=' + prod_id + '&yarn_rate=' + yarn_rate;

				var title = "Yarn Info Budget VS Actual";
				emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=250px,center=1,resize=0,scrolling=0', '../../');
				return;

				alert("Yarn Count, Type and rate not match with Budget");
				return;
			}

			if (vs_allocation_control * 1 == 1) // VS : Age wise Yarn Selection in Yarn Allocation
			{
				var company_id = '<? echo $company_name; ?>';
				var dataStr = company_id + '*' + prod_id + '*' + count_id + '*' + composition_id + '*' + yarn_type + '*' + color_id + '*' + vs_minimum_available_qty + '*' + vs_age_limit * 1;
				var semiller_record_found = return_global_ajax_value(dataStr, 'find_similer_yarn', '', 'yarn_allocation_controller');

				//alert(semiller_record_found+'>0 &&'+availableQty*1+'>= &&'+vs_minimum_available_qty*1+"&&"+age_days*1+"<"+vs_age_limit*1 );

				if (semiller_record_found > 0 && (availableQty * 1 >= (vs_minimum_available_qty * 1)) && (age_days * 1 < vs_age_limit * 1)) {
					similer_yarn_poup(event, str, selectable, job_no, ratep, vs_yrn_mandatory, yrn_tested, tr, vs_yrn_approve_mandatory, yrntest_is_approved, vs_allocation_control, vs_minimum_available_qty, vs_age_limit);
					return;
				}
			}

			$('#product_id').val(prod_id);
			$('#product_name').val(product_name);
			$('#available_qnty').val(availableQty);
			$('#unit_of_measurment').val(uom);
			$('#dyed_type').val(dyed_type);
			$('#item_avg_rate_usd').val(ratep);
			parent.emailwindow.hide()
		}

		function similer_yarn_poup(e, str, selectable, job_no, ratep, vs_yrn_mandatory, yrn_tested, tr, vs_yrn_approve_mandatory, yrntest_is_approved, vs_allocation_control, vs_minimum_available_qty, vs_age_limit) {
			var str_array = str.split("**");

			if (vs_allocation_control == 1) {
				var company_id = '<? echo $company_name; ?>';
				var item_category = '<? echo $item_category; ?>'
				var job_no = '<? echo $job_no; ?>'
				var txt_booking_no = '<? echo $txt_booking_no; ?>'
				var cbo_buyer_name = '<? echo $cbo_buyer_name; ?>'

				$('#product_id').val(str_array[0]);

				$('#available_qnty').val();

				var prod_id = str_array[0];
				var availableQty = str_array[2];
				var count_id = str_array[5];
				var composition_id = str_array[6];
				var yarn_type = str_array[7];
				var color_id = str_array[8];
				var age_days = str_array[9];

				var r = confirm("There are even more lot of the same composition that are older than " + vs_age_limit + " days and have a minimum quantity of more than " + vs_minimum_available_qty + " kg.Will you check out those lot?");
				if (r == 1) {
					var page_link = 'yarn_allocation_controller.php?action=create_similer_lot_search_list_view&company=' + company_id + '&count_id=' + count_id + '&composition_id=' + composition_id + '&yarn_type=' + yarn_type + '&color_id=' + color_id + '&job_no=' + job_no + '&txt_booking_no=' + txt_booking_no + '&cbo_buyer_name=' + cbo_buyer_name + '&item_category=' + item_category + '&vs_minimum_available_qty=' + vs_minimum_available_qty + '&vs_age_limit=' + vs_age_limit + '&selected_lot_available_qty=' + availableQty + '&selected_lot_age_days=' + age_days + '&selected_prod_id=' + prod_id;

					var title = "Similar yarn list";
					emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=350px,center=1,resize=0,scrolling=0', '../../');
					e.stopPropagation();
					emailwindow.onclose = function() {
						var str = this.contentDoc.getElementById("prod_str").value;
						//alert(str);
						var selectable = this.contentDoc.getElementById("selectable").value * 1;
						var job_no = this.contentDoc.getElementById("job_no").value;
						var ratep = this.contentDoc.getElementById("ratep").value * 1;
						var vs_yrn_mandatory = this.contentDoc.getElementById("vs_yrn_mandatory").value * 1;
						var yrn_tested = this.contentDoc.getElementById("yrn_tested").value * 1;
						var tr = this.contentDoc.getElementById("tr").value * 1;
						var vs_yrn_approve_mandatory = this.contentDoc.getElementById("vs_yrn_approve_mandatory").value * 1;
						var yrntest_is_approved = this.contentDoc.getElementById("yrntest_is_approved").value * 1;
						var vs_allocation_control = this.contentDoc.getElementById("vs_allocation_control").value * 1;

						js_set_value(str, selectable, job_no, ratep, vs_yrn_mandatory, yrn_tested, tr, vs_yrn_approve_mandatory, yrntest_is_approved, vs_allocation_control);
					}
				}
			}
		}

		function store_pop(e) {
			var prod_id = $(e.target).closest('td').find(':input').val();
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', "yarn_allocation_controller.php?action=storeStockPopup&prod_id=" + prod_id, "Store Wise Stock", 'width=380px,height=300px,center=1,resize=0,scrolling=0', '../../')
			e.stopPropagation();
		}

		function buyer_pop(e, buyer) {
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', "yarn_allocation_controller.php?action=buyerPopup&buyer=" + buyer, "Buyer", 'width=380px,height=300px,center=1,resize=0,scrolling=0', '../../')
			e.stopPropagation();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
					<thead>
						<th>Supplier Name</th>
						<th>Lot</th>
						<th>Yarn Count</th>
						<th>Composition</th>
						<th>Yarn Type</th>
						<th>Budget Coun</th>
						<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="hidden" name="company_id" id="company_id" value="<? echo $company_name ?>">
							<input type="hidden" name="category_id" id="category_id" value="<? echo $item_category ?>">
							<input type="hidden" name="job_no" id="job_no" value="<? echo $job_no ?>">
							<input type="hidden" name="txt_booking_no" id="txt_booking_no" value="<? echo $txt_booking_no ?>">
							<input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name ?>">
							<?
							echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company_name' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
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
							asort($composition);
							echo create_drop_down("cbo_yarn_composition", 100, $composition, "", 1, "--Select--", 0, "", 0);
							?>
						</td>
						<td>
							<?
							asort($yarn_type);
							echo create_drop_down("cbo_yarn_type", 100, $yarn_type, "", 1, "--Select--", 0, "", 0);
							?>
						</td>
						<td>
							<?
							 echo create_drop_down("cbo_buget_count", 100, $yes_no, '', 1, '-Select-', 0, "", 0);
							?>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('txt_lot').value+'_'+document.getElementById('company_id').value+'_'+document.getElementById('category_id').value+'_'+document.getElementById('cbo_yarn_count').value+'_'+document.getElementById('cbo_yarn_type').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('cbo_yarn_composition').value+'_'+document.getElementById('cbo_buget_count').value, 'create_lot_search_list_view', 'search_div', 'yarn_allocation_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if ($action == "create_lot_search_list_view") {
	$data = explode('_', $data);
	// print_r($data);
	$supplier = $data[0];
	$lots = $data[1];
	$company = $data[2];
	$category = $data[3];
	$cbo_count = $data[4];
	$cbo_type = $data[5];
	$job_no = $data[6];
	$cbo_composition = $data[7];
	$cbo_buget_count = $data[8];
	//echo $cbo_composition;

	if ($lots != '') {
		$lot_cond = " and a.lot='$lots'";
	} else {
		$lot_cond = "";
	}

	if ($supplier != 0) {
		$supplier_cond = " and a.supplier_id=$supplier";
	} else {
		$supplier_cond = "";
	}

	if ($cbo_count != 0) {
		$count_cond = " and a.yarn_count_id='$cbo_count'";
	} else {
		$count_cond = "";
	}

	if ($cbo_composition != 0) {
		$composition_cond = " and a.yarn_comp_type1st='$cbo_composition'";
	} else {
		$composition_cond = "";
	}

	if ($cbo_type != 0) {
		$type_cond = " and a.yarn_type='$cbo_type'";
	} else {
		$type_cond = "";
	}

	$varialble_setting = sql_select("select during_issue,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name=$company  and variable_list=25 and status_active=1 and is_deleted=0");
	$during_issue = $varialble_setting[0][csf('during_issue')];
	$control_level = $varialble_setting[0][csf('user_given_code_status')];
	$tolerant_percent = $varialble_setting[0][csf('tolerant_percent')];

	$varialble_setting_yarn_age = sql_select("select allocation_control, minimum_available_qty, age_limit from variable_settings_production where company_name='$company' and variable_list=156");

	if($cbo_buget_count==1){
		$data_count_arr=sql_select("select  count_id from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0 group by count_id");
		if(!empty($data_count_arr)){
			foreach($data_count_arr as $row){
				$CountId=$row["COUNT_ID"].",";
			}	
		}
		$count= 'and a.yarn_count_id in('.rtrim($CountId,",").')';
	}

	$allocation_control = $varialble_setting_yarn_age[0][csf('allocation_control')];
	$vs_allocation_control = ($allocation_control == "" || $allocation_control == 2) ? 0 : $allocation_control;
	$vs_minimum_available_qty = $varialble_setting_yarn_age[0][csf('minimum_available_qty')];
	$vs_age_limit = $varialble_setting_yarn_age[0][csf('age_limit')];

	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$supplier = return_library_array("select id, supplier_name from  lib_supplier", 'id', 'supplier_name');
	$buyer_arr = return_library_array("select id, BUYER_NAME from  lib_buyer", 'id', 'BUYER_NAME');
	$arr = array(0 => $comp, 1 => $item_category, 2 => $supplier);
	$sql = "select a.id, LISTAGG(b.buyer_id, ',') WITHIN GROUP (ORDER BY b.buyer_id) AS buyer_ids, a.company_id, a.item_category_id, a.supplier_id, a.lot, a.product_name_details, a.current_stock, a.allocated_qnty, a.available_qnty, a.unit_of_measure, a.yarn_comp_percent1st, a.yarn_comp_type1st, a.color, a.yarn_count_id, a.yarn_type, a.avg_rate_per_unit, a.dyed_type from product_details_master a  INNER JOIN INV_TRANSACTION b ON a.id = b.prod_id where b.transaction_type IN (1, 5) and a.company_id=$company and a.item_category_id=1 and a.current_stock > 0 and a.available_qnty > 0 and a.status_active=1 and a.is_deleted=0" . $lot_cond . $supplier_cond . $count_cond . $composition_cond . $type_cond . $count . " GROUP BY a.id, a.company_id, a.item_category_id, a.supplier_id, a.lot, a.product_name_details, a.current_stock, a.allocated_qnty, a.available_qnty, a.unit_of_measure, a.yarn_comp_percent1st, a.yarn_comp_type1st, a.color, a.yarn_count_id, a.yarn_type, a.avg_rate_per_unit, a.dyed_type";
	// echo $sql;
	$DataArray = sql_select($sql);
	$products = '';
	foreach ($DataArray as $row) {
		$products .= $row['ID'] . ",";
	}
	// echo $products;
	$products = trim($products, ",");
	$allocation_date_arr = array();
	$allocataion_date_sql =
		$transaction_date_arr = array();
	if ($db_type == 0) $date_cond = " and transaction_date!='0000-00-00'";
	else $date_cond = " and transaction_date is not null";
	$sql_date = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where item_category=1 $date_cond group by prod_id");
	// echo $date_cond;
	foreach ($sql_date as $row_d) {
		$transaction_date_arr[$row_d[csf('prod_id')]]['min_date'] = $row_d[csf('min_date')];
		$transaction_date_arr[$row_d[csf('prod_id')]]['max_date'] = $row_d[csf('max_date')];
	}

	//allocation date
	$sql_alloc_date = sql_select("select ITEM_ID, min(ALLOCATION_DATE) as min_date, max(ALLOCATION_DATE) as max_date from INV_MATERIAL_ALLOCATION_MST where item_category=1 and ALLOCATION_DATE is not null and status_active = 1 and is_deleted = 0 group by ITEM_ID");
	foreach ($sql_alloc_date as $row) {
		$allocation_date_arr[$row['ITEM_ID']]['MIN_DATE'] = $row['MIN_DATE'];
		$allocation_date_arr[$row['ITEM_ID']]['MAX_DATE'] = $row['MAX_DATE'];
	}

	//yarn test
	$yrn_arr = array();
	$yrn_arr['company_id'] = $company;
	$yrn_arr['variable_list'] = 36; // Yarn Test Mandatory For Allocation
	$vs_test = get_vs_yarn_test_mandatory($yrn_arr);

	$yrn_arr['variable_list'] = 37; // Yarn Test Approval Mandatory For Allocation
	$vs_test_approval = get_vs_yarn_test_mandatory($yrn_arr);

	$sql_yrn_test = "select a.id, c.comments_author_acceptance from product_details_master a, inv_yarn_test_mst b, inv_yarn_test_comments c where a.id = b.prod_id and a.company_id = b.company_id and b.id = c.mst_table_id and a.company_id = " . $company . " and a.item_category_id = 1 and a.current_stock > 0 and c.comments_author_acceptance in(1,2,3) and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0" . $lot_cond . $supplier_cond . $count_cond . $type_cond;
	//echo $sql_yrn_test;
	$sql_yrn_test_rslt = sql_select($sql_yrn_test);
	$yrn_test_data = array();
	foreach ($sql_yrn_test_rslt as $row) {
		$yrn_test_data[$row[csf('id')]] = $row[csf('id')];
	}

	$sql_yrn_test_approved = "select a.id, b.approved from product_details_master a, inv_yarn_test_mst b where a.id = b.prod_id and a.company_id = b.company_id and a.company_id = " . $company . " and a.item_category_id = 1 and a.current_stock > 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $lot_cond $supplier_cond $count_cond $type_cond";
	//echo $sql_yrn_test_approved;
	$sql_yrn_test_approved_rslt = sql_select($sql_yrn_test_approved);
	$yrn_test_approve_data = array();
	foreach ($sql_yrn_test_approved_rslt as $row) {
		$yrn_test_approve_data[$row[csf('id')]][] = $row[csf('approved')];
	}

?>

	<body>
		<div style=" width:1210px;">
			<input type="hidden" id="product_id" />
			<input type="hidden" id="dyed_type" />
			<input type="hidden" name="product_name" id="product_name" value="" />
			<input type="hidden" name="available_qnty" id="available_qnty" value="" />
			<input type="hidden" name="unit_of_measurment" id="unit_of_measurment" value="" />
			<input type="hidden" name="item_avg_rate_usd" id="item_avg_rate_usd" value="" />

			<table cellspacing="0" width="1210" class="rpt_table" border="0" rules="all">
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="70">Company</th>
						<th width="130">Supplier</th>
						<th width="50">Buyer</th>
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
						<th width="60">Allocation Ageing Date</th>
					</tr>
				</thead>
			</table>
			<div align="" style="width:1230;max-height:300px; overflow-y:scroll;">
				<table id="list_view" cellspacing="0" width="1210" class="rpt_table" border="0" rules="all">
					<tbody>
						<?
						$currency_conversion = sql_select("select id, conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and company_id = $company order by id desc");

						$lib_conversion_rate = $currency_conversion[0][csf("conversion_rate")];

						$countTypeArr = array();
						$sqlY = sql_select("select id,count_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0");
						foreach ($sqlY as $sqlY_row) {
							//$rate=$sqlY_row[csf('rate')];
							//$countTypeArr[$sqlY_row[csf('count_id')]][$sqlY_row[csf('type_id')]]["$rate"]=1;
							$countTypeArr[$sqlY_row[csf('count_id')]][$sqlY_row[csf('type_id')]]['yarn_count_type'] = 1;
							$countTypeArr[$sqlY_row[csf('count_id')]][$sqlY_row[csf('type_id')]]['yarn_rate'] = $sqlY_row[csf('rate')];
						}

						/*
						echo "<pre>";
						print_r($countTypeArr);
						echo "</pre>";
						*/

						//$sqlY = sql_select("select count_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and count_id='".$row[csf("yarn_count_id")]."' and type_id='".$row[csf("yarn_type")]."' and rate >= $ratep and status_active=1 and is_deleted=0");


						$colorArr = return_library_array("select id, color_name from lib_color", "id", "color_name");
						$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

						$i = 1;
						$is_approved = 0;
						foreach ($DataArray as $row) {
							//for yarn test
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
							//$sqlY = $countTypeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$ratep];
							$sqlY = $countTypeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]]['yarn_count_type'];
							$budgetRate = number_format($countTypeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]]['yarn_rate'], 4);

							if ((count($sqlY) == 0)) //count or type not match
							{
								$match = 0;
							} else //count or type match
							{
								if ($ratep > $budgetRate) {
									$match = 0;
								} else {
									$match = 1;
								}
							}

							//echo $match."test"."<br>";
							$selectable = 1;
							if (($during_issue != "") && ($job_no != "")) // ommit sample booking
							{
								if (!$match && $during_issue == 1 && $control_level == 1) // rate level
								{
									$selectable = 0;
								} else {
									$selectable = 1;
								}
							}
							$buyer = '';
							if ($row[csf("buyer_ids")] != 0 && $row[csf("buyer_ids")] != '') {
								$buyer_ids = array_unique(explode(",", $row[csf("buyer_ids")]));
								if (count($buyer_ids) > 0) {
									foreach ($buyer_ids as $buyer_id) {
										// echo $buyer_id;
										$buyer .= $buyer_arr[$buyer_id] . ",";
									}
								}
							}


							// echo "<pre>";
							// print_r($buyer_ids);
							// echo $buyer;
							$ageOfDays = datediff("d", $transaction_date_arr[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $transaction_date_arr[$row[csf("id")]]['max_date'], date("Y-m-d"));
							$daysOnHand_allocation = datediff("d", $allocation_date_arr[$row[csf("id")]]['MAX_DATE'], date("Y-m-d"));
							$buyer = trim($buyer, ",");
						?>
							<tr title="<? echo $title; ?>" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf("id")] . "**" . str_replace(array("\r", "\n"), '', $row[csf("product_name_details")]) . "**" . $row[csf("available_qnty")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dyed_type")] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_comp_type1st')] . "**" . $row[csf('yarn_type')] . "**" . $row[csf('color')] . "**" . $ageOfDays; ?>',<? echo $selectable; ?>,'<? echo $job_no; ?>','<? echo $ratep; ?>','<? echo $vs_test['is_mandatory']; ?>','<? echo $is_tested; ?>','<? echo $i; ?>','<? echo $vs_test_approval['is_mandatory']; ?>','<? echo $is_approved; ?>','<? echo $vs_allocation_control; ?>','<? echo $vs_minimum_available_qty; ?>','<? echo $vs_age_limit; ?>' );" style="cursor:pointer">
								<td width="40">
									<p><? echo $i; ?></p>
								</td>
								<td width="70">
									<p><? echo $comp[$row[csf("company_id")]]; ?></p>
								</td>
								<td width="130">
									<p><? echo $supplier[$row[csf("supplier_id")]]; ?></p>
								</td>
								<td width="50" align="center" onClick="buyer_pop(event,'<? echo $buyer; ?>');">
									<p><a href="##"><?
													if ($buyer != '' || $buyer != 0)
														echo "View";
													?></p></a>
								</td>
								<td width="70" title="<? echo $row[csf("id")]; ?>">
									<p><? echo $row[csf("lot")]; ?></p>
								</td>
								<td width="60">
									<p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
								</td>
								<td width="150" title='<? echo $row[csf('yarn_comp_type1st')]; ?>'>
									<p><? echo $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% "; ?> </p>
								</td>
								<td width="80">
									<p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
								</td>
								<td width="80">
									<p><? echo $colorArr[$row[csf('color')]]; ?></p>
								</td>
								<td width="80" align="right" onClick="store_pop(event);" class='dataClass'>
									<p><a href="##"><? echo number_format($row[csf("current_stock")], 2); ?><input type="hidden" value='<? echo $row[csf("id")] ?>'></p>
								</td>
								<td width="80" align="right">
									<p><? echo number_format($row[csf("allocated_qnty")], 2); ?></p>
								</td>
								<td width="80" align="right">
									<p><? echo number_format($row[csf("available_qnty")], 2); ?></p>
								</td>
								<td width="60" align="right">
									<p><? echo $ageOfDays; ?></p>
								</td>
								<td width="60" align="right">
									<p><? echo $ratep; ?></p>
								</td>
								<td width="60" align="right">
									<p><? echo $daysOnHand; ?></p>
								</td>
								<td width="60" align="right">
									<p><?
										if ($daysOnHand_allocation == '') echo 0;
										else echo $daysOnHand_allocation;
										?></p>
								</td>
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

if ($action == "create_similer_lot_search_list_view") {
	echo load_html_head_contents("Similer Yarn Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_similer_value(str, selectable, job_no, ratep, vs_yrn_mandatory, yrn_tested, tr, vs_yrn_approve_mandatory, yrntest_is_approved, vs_allocation_control) {
			$("#prod_str").val(str);
			$("#selectable").val(selectable);
			$("#job_no").val(job_no);
			$("#ratep").val(ratep);
			$("#vs_yrn_mandatory").val(vs_yrn_mandatory);
			$("#yrn_tested").val(yrn_tested);
			$("#tr").val(tr);
			$("#vs_yrn_approve_mandatory").val(vs_yrn_approve_mandatory);
			$("#yrntest_is_approved").val(yrntest_is_approved);
			$("#vs_allocation_control").val(vs_allocation_control);
			parent.emailwindow.hide();
		}
	</script>

	<?
	$varialble_setting = sql_select("select during_issue,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name=$company  and variable_list=25 and status_active=1 and is_deleted=0");
	$during_issue = $varialble_setting[0][csf('during_issue')];
	$control_level = $varialble_setting[0][csf('user_given_code_status')];
	$tolerant_percent = $varialble_setting[0][csf('tolerant_percent')];

	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$supplier = return_library_array("select id, supplier_name from  lib_supplier", 'id', 'supplier_name');
	$arr = array(0 => $comp, 1 => $item_category, 2 => $supplier);

	$similer_yarn_cond = " and a.yarn_count_id='$count_id'  and a.yarn_comp_type1st='$composition_id' and a.yarn_type='$yarn_type' and a.color=$color_id";
	if ($vs_minimum_available_qty > 0) {
		$vs_min_available_cond = " and $vs_minimum_available_qty<=a.available_qnty";
	}

	$sql = "select a.id, a.company_id, a.item_category_id, a.supplier_id, a.lot, a.product_name_details, a.current_stock, a.allocated_qnty, a.available_qnty, a.unit_of_measure, a.yarn_comp_percent1st, a.yarn_comp_type1st, a.color, a.yarn_count_id, a.yarn_type, a.avg_rate_per_unit, a.dyed_type from product_details_master a where a.company_id=$company and a.item_category_id=1 and a.current_stock > 0 and a.status_active=1 and a.is_deleted=0 and a.id!=$selected_prod_id $similer_yarn_cond $vs_min_available_cond";

	//echo $sql;
	$DataArray = sql_select($sql);


	$transaction_date_arr = array();
	if ($db_type == 0) $date_cond = " and transaction_date!='0000-00-00'";
	else $date_cond = " and transaction_date is not null";
	$sql_date = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where item_category=1 $date_cond group by prod_id");
	foreach ($sql_date as $row_d) {
		$transaction_date_arr[$row_d[csf('prod_id')]]['min_date'] = $row_d[csf('min_date')];
		$transaction_date_arr[$row_d[csf('prod_id')]]['max_date'] = $row_d[csf('max_date')];
	}

	//yarn test
	$yrn_arr = array();
	$yrn_arr['company_id'] = $company;
	$yrn_arr['variable_list'] = 36; // Yarn Test Mandatory For Allocation
	$vs_test = get_vs_yarn_test_mandatory($yrn_arr);

	$yrn_arr['variable_list'] = 37; // Yarn Test Approval Mandatory For Allocation
	$vs_test_approval = get_vs_yarn_test_mandatory($yrn_arr);

	$sql_yrn_test = "select a.id, c.comments_author_acceptance from product_details_master a, inv_yarn_test_mst b, inv_yarn_test_comments c where a.id = b.prod_id and a.company_id = b.company_id and b.id = c.mst_table_id and a.company_id = " . $company . " and a.item_category_id = 1 and a.current_stock > 0 and c.comments_author_acceptance in(1,2,3) and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0" . $similer_yarn_cond;
	//echo $sql_yrn_test;
	$sql_yrn_test_rslt = sql_select($sql_yrn_test);
	$yrn_test_data = array();
	foreach ($sql_yrn_test_rslt as $row) {
		$yrn_test_data[$row[csf('id')]] = $row[csf('id')];
	}

	$sql_yrn_test_approved = "select a.id, b.approved from product_details_master a, inv_yarn_test_mst b where a.id = b.prod_id and a.company_id = b.company_id and a.company_id = " . $company . " and a.item_category_id = 1 and a.current_stock > 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $similer_yarn_cond";
	//echo $sql_yrn_test_approved;
	$sql_yrn_test_approved_rslt = sql_select($sql_yrn_test_approved);
	$yrn_test_approve_data = array();
	foreach ($sql_yrn_test_approved_rslt as $row) {
		$yrn_test_approve_data[$row[csf('id')]][] = $row[csf('approved')];
	}

	?>

	<body>
		<div style=" width:1120px;">

			<input type="hidden" name="prod_str" id="prod_str" value="" />
			<input type="hidden" name="selectable" id="selectable" value="" />
			<input type="hidden" name="job_no" id="job_no" value="" />
			<input type="hidden" name="ratep" id="ratep" value="" />
			<input type="hidden" name="vs_yrn_mandatory" id="vs_yrn_mandatory" value="" />
			<input type="hidden" name="yrn_tested" id="yrn_tested" value="" />
			<input type="hidden" name="tr" id="tr" value="" />
			<input type="hidden" name="vs_yrn_approve_mandatory" id="vs_yrn_approve_mandatory" value="" />
			<input type="hidden" name="yrntest_is_approved" id="yrntest_is_approved" value="" />
			<input type="hidden" name="vs_allocation_control" id="vs_allocation_control" value="" />

			<table cellspacing="0" width="1120" class="rpt_table" border="0" rules="all">
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
						<th width="">DOH</th>
					</tr>
				</thead>
			</table>
			<div align="" style="width:1120px;max-height:300px; overflow-y:scroll;">
				<table id="list_view" cellspacing="0" width="1100" class="rpt_table" border="0" rules="all">
					<tbody>
						<?
						$currency_conversion = sql_select("select id, conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and company_id = $company order by id desc");

						$lib_conversion_rate = $currency_conversion[0][csf("conversion_rate")];

						$countTypeArr = array();
						$sqlY = sql_select("select id,count_id,type_id,rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0");
						foreach ($sqlY as $sqlY_row) {
							//$rate=$sqlY_row[csf('rate')];
							//$countTypeArr[$sqlY_row[csf('count_id')]][$sqlY_row[csf('type_id')]]["$rate"]=1;
							$countTypeArr[$sqlY_row[csf('count_id')]][$sqlY_row[csf('type_id')]]['yarn_count_type'] = 1;
							$countTypeArr[$sqlY_row[csf('count_id')]][$sqlY_row[csf('type_id')]]['yarn_rate'] = $sqlY_row[csf('rate')];
						}

						/*
						echo "<pre>";
						print_r($countTypeArr);
						echo "</pre>";
						*/

						$colorArr = return_library_array("select id, color_name from lib_color", "id", "color_name");
						$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

						$i = 1;
						$is_approved = 0;
						foreach ($DataArray as $row) {
							//for yarn test
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
							//$sqlY = $countTypeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$ratep];
							$sqlY = $countTypeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]]['yarn_count_type'];
							$budgetRate = number_format($countTypeArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]]['yarn_rate'], 4);

							if ((count($sqlY) == 0)) //count or type not match
							{
								$match = 0;
							} else //count or type match
							{
								if ($ratep > $budgetRate) {
									$match = 0;
								} else {
									$match = 1;
								}
							}

							//echo $match."test"."<br>";
							$selectable = 1;
							if (($during_issue != "") && ($job_no != "")) // ommit sample booking
							{
								if (!$match && $during_issue == 1 && $control_level == 1) // rate level
								{
									$selectable = 0;
								} else {
									$selectable = 1;
								}
							}

							//echo $selectable."test";
							//$product_name_details = str_replace(array("\r", "\n"), '',$row[csf("product_name_details")]);
							$ageOfDays = datediff("d", $transaction_date_arr[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $transaction_date_arr[$row[csf("id")]]['max_date'], date("Y-m-d"));

							if ($ageOfDays > str_replace("'", "", $vs_age_limit)) {
						?>
								<tr title="<? echo $title; ?>" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_similer_value('<? echo $row[csf("id")] . "**" . str_replace(array("\r", "\n"), '', $row[csf("product_name_details")]) . "**" . $row[csf("available_qnty")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dyed_type")] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_comp_type1st')] . "**" . $row[csf('yarn_type')] . "**" . $row[csf('color')]; ?>',<? echo $selectable; ?>,'<? echo $job_no; ?>','<? echo $ratep; ?>','<? echo $vs_test['is_mandatory']; ?>','<? echo $is_tested; ?>','<? echo $i; ?>','<? echo $vs_test_approval['is_mandatory']; ?>',<? echo $is_approved; ?>,<? echo $vs_allocation_control; ?> )" style="cursor:pointer">
									<td width="40">
										<p><? echo $i; ?></p>
									</td>
									<td width="70">
										<p><? echo $comp[$row[csf("company_id")]]; ?></p>
									</td>
									<td width="130">
										<p><? echo $supplier[$row[csf("supplier_id")]]; ?></p>
									</td>
									<td width="70" title="<? echo $row[csf("id")]; ?>">
										<p><? echo $row[csf("lot")]; ?></p>
									</td>
									<td width="60">
										<p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p>
									</td>
									<td width="150" title='<? echo $row[csf('yarn_comp_type1st')]; ?>'>
										<p><? echo $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% "; ?> </p>
									</td>
									<td width="80">
										<p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p>
									</td>
									<td width="80">
										<p><? echo $colorArr[$row[csf('color')]]; ?></p>
									</td>
									<td width="80" align="right" onClick="store_pop(event);" class='dataClass'>
										<p><a href="##"><? echo number_format($row[csf("current_stock")], 2); ?><input type="hidden" value='<? echo $row[csf("id")] ?>'></p>
									</td>
									<td width="80" align="right">
										<p><? echo number_format($row[csf("allocated_qnty")], 2); ?></p>
									</td>
									<td width="80" align="right">
										<p><? echo number_format($row[csf("available_qnty")], 2); ?></p>
									</td>
									<td width="60" align="right">
										<p><? echo $ageOfDays; ?></p>
									</td>
									<td width="60" align="right">
										<p><? echo $ratep; ?></p>
									</td>
									<td width="" align="right">
										<p><? echo $daysOnHand; ?></p>
									</td>
								</tr>
						<?
								$i++;
							}
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
	exit();
}

if ($action == "open_qnty_popup") {
	echo load_html_head_contents("Item List", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	include('../../../includes/class4/class.conditions.php');
	include('../../../includes/class4/class.reports.php');
	include('../../../includes/class4/class.fabrics.php');
	include('../../../includes/class4/class.yarns.php');

?>
	<script>
		function distribution_value(mehtod) {
			if (mehtod == 1) {
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').removeAttr('disabled', 'disabled');
				$('#allocated_qnty').attr('disabled', 'disabled');
			} else {
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').attr('disabled', 'disabled');
				$('#allocated_qnty').removeAttr('disabled', 'disabled');
			}
		}

		function set_sum_value(des_fil_id, field_id, table_id) {
			var rowCount = $('#tbl_order_qnty_list tr').length - 2;
			var ddd = {
				dec_type: 6,
				comma: 0,
				currency: 1
			};
			math_operation(des_fil_id, field_id, '+', rowCount, ddd);
		}

		function js_set_value_qnty() // allocation popup close
		{
			var rowCount = $('#tbl_order_qnty_list tr').length - 2;
			var qnty_breck_down = "";

			for (var i = 1; i <= rowCount; i++) {
				if (qnty_breck_down == "") {
					qnty_breck_down = $('#txt_qnty_' + i).val() + "_" + $('#txt_order_id_' + i).val() + "_" + $('#txt_job_no_' + i).val();
				} else {
					qnty_breck_down += "," + $('#txt_qnty_' + i).val() + "_" + $('#txt_order_id_' + i).val() + "_" + $('#txt_job_no_' + i).val();
				}

				budget_value_validation(i);
			}

			document.getElementById('qnty_breck_down').value = qnty_breck_down;
			var allocated_qnty = document.getElementById('allocated_qnty').value;
			var hide_allocated_qnty = document.getElementById('hide_allocated_qnty').value;
			var available_qnty = document.getElementById('available_qnty').value;
			var available_qnty_curr = available_qnty * 1 + hide_allocated_qnty * 1;

			if (allocated_qnty * 1 > available_qnty_curr * 1) {
				alert("Allocated qnty greater than available qnty");
				return;
			} else {
				parent.emailwindow.hide();
			}
		}

		function calculate_poportion(value) {
			var pre_qnty_breck_down = '<? echo $pre_qnty_breck_down; ?>';
			var pre_qnty_breck_down_arr = pre_qnty_breck_down.split(',');
			var po_data = [];
			for (var k = 0; k < pre_qnty_breck_down_arr.length; k++) {
				var po_data_arr = pre_qnty_breck_down_arr[k].split('_');
				po_data[po_data_arr[1]] = po_data_arr[0];
			}

			if (is_fabric_level == 1) {
				var tot_po_qnty = (document.getElementById('tot_fab_booking_qnty').value) * 1;
			} else {
				var tot_po_qnty = (document.getElementById('tot_po_qnty').value) * 1;
			}

			var rowCount = $('#tbl_order_qnty_list tr').length - 2;
			var len = totalProp = 0;

			for (var i = 1; i <= rowCount; i++) {
				len = len + 1;

				if (is_fabric_level == 1) {
					var txt_order_qnty = ($('#txt_fab_booking_qnty_' + i).val()) * 1;
				} else {
					var txt_order_qnty = ($('#txt_order_qnty_' + i).val()) * 1;
				}

				var proportionate_qnty = number_format_common((((value / tot_po_qnty) * txt_order_qnty)), 2, 0, 1);
				totalProp += (proportionate_qnty * 1);

				if (rowCount == len) {
					var balance = value - totalProp;
					proportionate_qnty = (proportionate_qnty * 1) + (balance * 1);
				}

				var order_id = $('#txt_order_id_' + i).val();
				var order_status_id = $('#txt_order_status_id_' + i).val() * 1;
				var qnty = po_data[order_id];
				var update_id = '<? echo $update_id; ?>';

				if (update_id != "" && order_status_id == 3 && qnty < proportionate_qnty) // at update even do not allow cancel po allocated qnty
				{
					$('#txt_qnty_' + i).val(number_format_common(qnty, 2, 0, 1));
				} else {
					$('#txt_qnty_' + i).val(number_format_common(proportionate_qnty, 2, 0, 1));
				}

			}
		}

		function validation_booking_qty_with_order_ratio() {

			var allocated_qty = $('#po_wise_allocated_qty').val();
			var allocated_qty_arr = allocated_qty.split(',');
			var pre_qnty_breck_down = '<? echo $pre_qnty_breck_down; ?>';
			var pre_qnty_breck_down_arr = pre_qnty_breck_down.split(',');


			//var bookingQty=$('#booking_qnty').val() * 1;
			var bookingQty = <? echo $txt_booking_qnty * 1; ?>;
			var tot_po_qnty = (document.getElementById('tot_po_qnty').value) * 1;
			//alert(tot_po_qnty);

			var rowCount = $('#tbl_order_qnty_list tr').length - 2;
			for (var i = 1; i <= rowCount; i++) {
				var order_qnty = $('#txt_order_qnty_' + i).val() * 1;
				var qnty = $('#txt_qnty_' + i).val() * 1;
				var allocation_capacity = (bookingQty / tot_po_qnty) * order_qnty;
				var order_id = $('#txt_order_id_' + i).val();

				for (var k = 0; k < pre_qnty_breck_down_arr.length; k++) {
					var qty_po_id_po = pre_qnty_breck_down_arr[k].split('_');
					if (qty_po_id_po[1] == order_id) {
						qnty = qnty - (qty_po_id_po[0] * 1);
					}
				}

				for (var s = 0; s < allocated_qty_arr.length; s++) {
					var po_val = allocated_qty_arr[s].split('*');
					if (order_id == po_val[0]) {
						qnty = qnty + (po_val[1] * 1);
					}
				}

				if (allocation_capacity < qnty) {
					alert("Booking Capacity:" + allocation_capacity + ", allocatation qty:" + qnty);
					$('#allocated_qnty').val(0);
					$('#txt_qnty_' + i).val(0);
					$('#txt_qnty_' + i).css("background-color", "red");
				}

			}

			if ($('#allocated_qnty').val() == 0) {
				for (var ii = 1; ii <= rowCount; ii++) {
					$('#txt_qnty_' + ii).val(0);
				}
			}
		}
	</script>
	</head>

	<body>
		<?
		$prev_datas = explode(",", $qnty_breck_down);

		?>
		<div align="center">
			<strong>Distribution Method:</strong>
			<input type="radio" name="distribution_type" id="distribution_type_0" value="0" onClick="distribution_value(this.value)" checked />
			<label for="distribution_type_0">Proportionately</label>
			<input type="radio" name="distribution_type" id="distribution_type_1" value="1" onClick="distribution_value(this.value)" />
			<label for="distribution_type_1">Manually</label>
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<input type="hidden" id="is_fabric_level" value="<? echo ($txt_fabric_po != "") ? 1 : 0; ?>" />
				<table id="tbl_order_qnty_list" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th width="150" colspan="11">
								Available Qnty:<input type="text" name="available_qnty" id="available_qnty" style="width:60px;" value="<? echo $available_qnty; ?>" class="text_boxes_numeric" disabled />
								Allocated Qnty:<input type="text" name="allocated_qnty" id="allocated_qnty" style="width:60px;" class="text_boxes_numeric" value="<? echo $txt_qnty; ?>" onChange="calculate_poportion(this.value),budget_value_validation();" />
								<input type="hidden" name="hide_allocated_qnty" id="hide_allocated_qnty" style="width:60px;" class="text_boxes" value="<? echo ($update_id != "") ? ($txt_qnty + ($txt_old_qnty - $txt_qnty)) : ''; ?>" />
								<input type="hidden" name="qnty_breck_down" id="qnty_breck_down" style="width:60px;" class="text_boxes" value="<? echo $qnty_breck_down; ?>" />
								Booking Qnty:<input type="text" name="booking_qnty" id="booking_qnty" style="width:60px;" class="text_boxes_numeric" value="<? echo ($txt_fab_booking_qnty != "") ? $txt_fab_booking_qnty : $txt_booking_qnty; ?>" readonly />
							</th>
						</tr>
						<tr>
							<th>Job No</th>
							<th width="150">Order No</th>
							<th width="100">Order Status</th>
							<th width="100">IR/CN</th>
							<th width="100">File No</th>
							<th width="100">Order Qnty</th>
							<?
							if ($during_issue == 1 && $control_level == 2) // value level : [Yarn item and rate matching with budget variable]
							{ ?>
								<th width="100">Budget Value</th>
								<th width="100">Prev. Allocated Value</th>
							<? } ?>

							<? if ($txt_fabric_po != "") { ?>
								<th width="100">Booking Qnty</th>
							<? } ?>

							<th width="150" class="must_entry_caption">Allocated Qnty</th>
							<?
							if ($during_issue == 1 && $control_level == 2) // value level : [Yarn item and rate matching with budget variable]
							{ ?>
								<th width="100">Allocated Value</th>
								<th width="100">Balance Value</th>
							<? } ?>
						</tr>
					</thead>
					<tbody>
						<?
						if ($during_issue == 1 && $control_level == 2) // value level : [Yarn item and rate matching with budget variable]
						{
							$currency_conversion = sql_select("select id, conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and company_id=$cbo_company_name order by id desc");
							$lib_conversion_rate = $currency_conversion[0][csf("conversion_rate")];


							// Yarn cost from budget start
							$condition = new condition();
							if (str_replace("'", "", $txt_job_no) != '') {
								$condition->job_no("='$txt_job_no'");
							}

							$condition->init();
							$yarn = new yarn($condition);
							$yarn_po_wise_costing_arr = $yarn->getOrderWiseYarnAmountArray();
							// Yarn cost from budget end

							if ($update_id != "" && $item_id != "") {
								$update_id_cond = "and b.mst_id not in ($update_id) and b.item_id not in($item_id)";
							}

							//$sql_alc_result = sql_select("SELECT b.po_break_down_id, b.qnty as allocated_qnty,c.avg_rate_per_unit  FROM inv_material_allocation_dtls b, product_details_master c WHERE b.item_id = c.id AND b.job_no = '$txt_job_no' AND b.status_active = 1 AND b.is_deleted = 0 and c.status_active=1 and c.is_deleted=0  $update_id_cond order by b.po_break_down_id");

							$sql_alc_result = sql_select(" SELECT  b.po_break_down_id, sum(b.qnty)                               AS allocated_qnty, sum(b.qnty* c.avg_rate_per_unit)/sum(b.qnty)     AS avg_rate_per_unit FROM inv_material_allocation_dtls b, product_details_master c WHERE     b.item_id = c.id AND b.job_no = '$txt_job_no' AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 and b.is_dyied_yarn <>1  $update_id_cond GROUP BY b.po_break_down_id ORDER BY b.po_break_down_id");

							$po_wise_prev_allocated_data_arr = array();
							foreach ($sql_alc_result as $row) {
								$usd_ratep = number_format(($row[csf('avg_rate_per_unit')] / $lib_conversion_rate), 4);
								$po_wise_prev_allocated_data_arr[$row[csf('po_break_down_id')]] = ($row[csf('allocated_qnty')] * $usd_ratep);
								//echo $row[csf('allocated_qnty')]."*".$usd_ratep;
							}

							$currentItemAvgRate = return_field_value("avg_rate_per_unit", "product_details_master a", "id=$item_id and status_active=1 and is_deleted=0", "avg_rate_per_unit");
						}

						$sl = 1;
						$tot_po_qnty = 0;
						$qnty_array = $po_wise_allocated_data_arr = array();
						foreach ($prev_datas as $prev_data) {
							$po_wise_data = explode("_", $prev_data);
							$qnty_array[$po_wise_data[1]] = $po_wise_data[0];
							//echo $po_wise_data[0]."*".$usd_ratep."<br>";

							$current_item_usd_ratep = number_format(($currentItemAvgRate / $lib_conversion_rate), 4);
							$po_wise_allocated_data_arr[$po_wise_data[1]] = ($po_wise_data[0] * $current_item_usd_ratep);
						}

						$booking_arr = array();
						$booking_no_arr = array();

						if ($update_id != "") // Cancel Po
						{
							$po_status_cond = " and b.status_active in (1,3) and b.is_deleted=0 and c.status_active in (1,3) and c.is_deleted in(0,2)";
						} else {
							$po_status_cond = " and b.status_active in (1) and b.is_deleted=0 and c.status_active in (1) and c.is_deleted=0";
						}

						if ($txt_fabric_po != "") {
							$po_cond = " and b.id in($txt_fabric_po)";
							$fabric_data = explode("_", $txt_selectted_fabric);
							$sql = "select a.booking_no_prefix_num, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short,sum(c.grey_fab_qnty) grey_fab_qnty, a.company_id, a.buyer_id, c.job_no, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number,b.plan_cut,b.status_active as po_status from wo_booking_mst a,wo_booking_dtls c,wo_po_break_down b,wo_pre_cost_fabric_cost_dtls d where c.po_break_down_id=b.id and a.booking_no=c.booking_no and c.po_break_down_id=b.id and c.pre_cost_fabric_cost_dtls_id=d.id and c.job_no=b.job_no_mst and a.booking_no='$txt_booking_no' $po_cond and d.item_number_id=$fabric_data[0] and d.gsm_weight=$fabric_data[1] and d.width_dia_type=$fabric_data[2] and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $po_status_cond group by a.booking_no_prefix_num,a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number,b.plan_cut,b.status_active order by a.booking_date,a.booking_no_prefix_num desc";
						} else {
							$sql = "select a.booking_no_prefix_num, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.buyer_id, c.job_no, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id,b.po_number,b.plan_cut,b.status_active as po_status from wo_booking_mst a,wo_booking_dtls c,wo_po_break_down b where c.po_break_down_id=b.id and c.job_no=b.job_no_mst and a.booking_no=c.booking_no and c.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 $po_status_cond group by a.booking_no_prefix_num,a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no, a.booking_type, a.is_short, c.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no,b.po_number,b.plan_cut,b.status_active order by a.booking_date,a.booking_no_prefix_num desc";
						}

						//echo $sql;

						$result = sql_select($sql);
						$po_break_down_id_arr = array();
						$tolerant_percent_value = 0;
						foreach ($result as $order_data) {
							$tot_po_qnty += $order_data[csf('plan_cut')];
							$tot_booking_qnty += $order_data[csf('grey_fab_qnty')];

							if ($during_issue == 1 && $control_level == 2) // value level : [Yarn item and rate matching with budget variable]
							{
								$po_wise_budget_value = number_format($yarn_po_wise_costing_arr[$order_data[csf('po_break_down_id')]], 2, ".", "");

								if ($tolerant_percent > 0) // Budget value enhance till toerant percent
								{
									$tolerant_percent_value = number_format(($po_wise_budget_value * $tolerant_percent / 100), 2, ".", "");
								}

								$po_wise_prev_allocated_value = number_format($po_wise_prev_allocated_data_arr[$order_data[csf('po_break_down_id')]], 2, ".", "");
								$po_wise_allocated_value = number_format($po_wise_allocated_data_arr[$order_data[csf('po_break_down_id')]], 2, ".", "");
								$po_wise_balance_value = number_format(($po_wise_budget_value - ($po_wise_prev_allocated_value + $po_wise_allocated_value)), 2, ".", "");
							}

							if ($order_data[csf('po_status')] == 3) {
								$bgcolor = "#f0ad4e";
								$title = "This PO is cancel PO";
							} else {
								$bgcolor = "";
								$title = "";
							}
						?>

							<tr bgcolor="<? echo $bgcolor; ?>" title="<? echo $title; ?>">
								<td>
									<input type="text" class="text_boxes" name="txt_job_no[]" id="txt_job_no_<? echo $sl; ?>" value="<? echo $order_data[csf('job_no')]; ?>" disabled />
								</td>
								<td width="150">
									<input type="text" class="text_boxes" name="txt_order_no[]" id="txt_order_no_<? echo $sl; ?>" style="width:150px " value="<? echo $order_data[csf('po_number')]; ?>" disabled />
									<input type="hidden" name="txt_order_id[]" id="txt_order_id_<? echo $sl; ?>" style="width:160px " value="<? echo $order_data[csf('po_break_down_id')]; ?>" disabled />
								</td>
								<td width="100">
									<input type="text" class="text_boxes" name="txt_order_status[]" id="txt_order_status_<? echo $sl; ?>" style="width:100px" value="<? echo $order_status = ($order_data[csf('po_status')] == 3) ? "Cancel" : "Active"; ?>" disabled />
									<input type="hidden" name="txt_order_status_id[]" id="txt_order_status_id_<? echo $sl; ?>" value="<? echo $order_data[csf('po_status')]; ?>" disabled>
								</td>
								<td width="90" align="right">
									<input type="text" class="text_boxes" name="txt_ref[]" id="txt_ref_<? echo $sl; ?>" style="width:90px " value="<? echo $order_data[csf('grouping')]; ?>" disabled />
								</td>
								<td width="90" align="right">
									<input type="text" class="text_boxes" name="txt_file[]" id="txt_file_<? echo $sl; ?>" style="width:90px " value="<? echo $order_data[csf('file_no')]; ?>" disabled />
								</td>
								<td width="100">
									<input type="text" name="txt_order_qnty[]" id="txt_order_qnty_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo $order_data[csf('plan_cut')]; ?>" disabled />
								</td>
								<?
								if ($during_issue == 1 && $control_level == 2) // value level : [Yarn item and rate matching with budget variable]
								{ ?>
									<td width="100">
										<input type="text" name="txt_order_budget_value[]" id="txt_order_budget_value_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo $po_wise_budget_value; ?>" disabled />
										<input type="hidden" name="tolerant_percent_value" id="tolerant_percent_value_<? echo $sl; ?>" value="<? echo $tolerant_percent_value; ?>" />
									</td>
									<td width="100">
										<input type="text" name="txt_order_prev_allocated_value[]" id="txt_order_prev_allocated_value_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo $po_wise_prev_allocated_value; ?>" disabled />
									</td>
								<? } ?>

								<? if ($txt_fabric_po != "") { ?>
									<td width="100">
										<input type="text" name="txt_fab_booking_qnty[]" id="txt_fab_booking_qnty_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo number_format($order_data[csf('grey_fab_qnty')], 2, ".", ""); ?>" disabled />
									</td>
								<? } ?>

								<td width="150">
									<input type="text" name="txt_qnty[]" id="txt_qnty_<? echo $sl; ?>" style="width:150px " value="<? echo $qnty_array[$order_data[csf('po_break_down_id')]]; ?>" class="text_boxes_numeric" onChange="budget_value_validation(<? echo $sl; ?>);set_sum_value('allocated_qnty','txt_qnty_','tbl_order_qnty_list');" disabled />
								</td>

								<?
								if ($during_issue == 1 && $control_level == 2) // value level : [Yarn item and rate matching with budget variable]
								{ ?>
									<td width="100">
										<input type="text" name="txt_order_allocated_value[]" id="txt_order_allocated_value_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo $po_wise_allocated_value; ?>" disabled />
									</td>

									<td width="100">
										<input type="text" name="txt_order_balance_value[]" id="txt_order_balance_value_<? echo $sl; ?>" style="width:100px " class="text_boxes_numeric" value="<? echo $po_wise_balance_value; ?>" disabled />
									</td>
								<? } ?>
							</tr>
						<?
							$sl++;
							$po_break_down_id_arr[$order_data[csf('po_break_down_id')]] = $order_data[csf('po_break_down_id')];
						}
						?>
					</tbody>

				</table>
				<table width="98.5%" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
					<tr>
						<td align="center" width="100%" class="button_container">
							<input type="button" class="formbutton" value="Close" onClick="js_set_value_qnty()" />
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" value="<? echo $tot_po_qnty; ?>" />
							<input type="hidden" name="tot_fab_booking_qnty" id="tot_fab_booking_qnty" value="<? echo ($txt_fabric_po != "") ? $tot_booking_qnty : ''; ?>" />
							<input type="hidden" name="po_wise_allocated_qty" id="po_wise_allocated_qty" value="" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>

	<script type="text/javascript">
		function budget_value_validation(row_sl_no = "") {
			var mehtod = $('input[name="distribution_type"]:checked').val() * 1;
			var item_avg_rate_usd = '<? echo $item_avg_rate_usd; ?>';
			var during_issue = '<? echo $during_issue; ?>';
			var control_level = '<? echo $control_level; ?>';
			var tolerant_percent = '<? echo $tolerant_percent; ?>';
			var update_id = '<? echo $update_id; ?>';
			var previous_qnty_breck_down = '<? echo $pre_qnty_breck_down; ?>';

			var proportionate_qnty = 0
			var additional_value = 0;
			var allowed_cumulative_balance_value = 0;
			var total_allocated_qnty = 0;

			if (mehtod != 1) {
				var rowCount = $('#tbl_order_qnty_list tr').length - 2;
				var len = totalProp = 0;

				for (var i = 1; i <= rowCount; i++) {
					len = len + 1;

					if (during_issue == 1 && control_level == 2) // Value lavel
					{
						proportionate_qnty = $('#txt_qnty_' + i).val() * 1;

						$('#txt_order_allocated_value_' + i).val(number_format_common((proportionate_qnty * item_avg_rate_usd), 2, 0, 1));

						//alert(proportionate_qnty+"=="+item_avg_rate_usd);
						var po_id = $('#txt_order_id_' + i).val() * 1;
						var budget_value = $('#txt_order_budget_value_' + i).val() * 1;
						var additional_value = $('#tolerant_percent_value_' + i).val() * 1;
						var total_allowed_value = (budget_value + additional_value);
						var prev_allocated_value = $('#txt_order_prev_allocated_value_' + i).val() * 1;
						var current_allocated_value = $('#txt_order_allocated_value_' + i).val() * 1;
						var total_allocated_value = (prev_allocated_value + current_allocated_value);

						allowed_cumulative_balance_value = (total_allowed_value - prev_allocated_value);

						if (allowed_cumulative_balance_value < current_allocated_value) {
							alert("Total allocated value can not be greater than budget value.\n\nAllow additional percentage= " + tolerant_percent + "% and value=" + number_format_common(additional_value, 2.0, 1) + "\nBudget Value=" + number_format_common(budget_value, 2, 0, 1) + "\nIncluding additional total allowed value=" + number_format_common(total_allowed_value, 2, 0, 1) + "\nPrevious allocated value= " + number_format_common(prev_allocated_value, 2, 0, 1) + "\nCurrent allocated value=" + number_format_common(current_allocated_value, 2, 0, 1) + "\nTotal allocated value= " + number_format_common(total_allocated_value, 2, 0, 1) + "\nCumulative allowed balance value= " + number_format_common(allowed_cumulative_balance_value, 2, 0, 1));

							$('#txt_qnty_' + i).val(0);
							$('#txt_order_allocated_value_' + i).val(0);
							//return;
						}

					} // End
				}
			} else {
				if (during_issue == 1 && control_level == 2) // Value lavel
				{
					proportionate_qnty = $('#txt_qnty_' + row_sl_no).val() * 1;

					$('#txt_order_allocated_value_' + row_sl_no).val(number_format_common((proportionate_qnty * item_avg_rate_usd), 2, 0, 1));

					var budget_value = $('#txt_order_budget_value_' + row_sl_no).val() * 1;
					var additional_value = $('#tolerant_percent_value_' + row_sl_no).val() * 1;
					var total_allowed_value = (budget_value + additional_value);

					var prev_allocated_value = $('#txt_order_prev_allocated_value_' + row_sl_no).val() * 1;
					var current_allocated_value = $('#txt_order_allocated_value_' + row_sl_no).val() * 1;
					var total_allocated_value = (prev_allocated_value + current_allocated_value);
					allowed_cumulative_balance_value = ((budget_value + additional_value) - prev_allocated_value);

					//alert(budget_value+"=="+additional_value+"=="+prev_allocated_value+"=="+allowed_cumulative_balance_value);
					//alert(proportionate_qnty+"=="+item_avg_rate_usd);

					if (allowed_cumulative_balance_value < current_allocated_value) {
						alert("Total allocated value can not be greater than budget value.\n\nAllow additional percentage= " + tolerant_percent + "% and value=" + number_format_common(additional_value, 2, 0, 1) + "\nBudget Value=" + number_format_common(budget_value, 2, 0, 1) + "\nIncluding additional total allowed value=" + number_format_common(total_allowed_value, 2, 0, 1) + "\nPrevious allocated value= " + number_format_common(prev_allocated_value, 2, 0, 1) + "\nCurrent allocated value=" + number_format_common(current_allocated_value, 2, 0, 1) + "\nTotal allocated value= " + number_format_common(total_allocated_value, 2, 0, 1) + "\nCumulative allowed balance value= " + number_format_common(allowed_cumulative_balance_value, 2, 0, 1));
						$('#txt_qnty_' + row_sl_no).val(0);
						$('#txt_order_allocated_value_' + row_sl_no).val(0);
						return;
					}

				} // End
			}

		}
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var dataStr = return_global_ajax_value('<? echo implode(',', $po_break_down_id_arr) . '*' . $txt_booking_no; ?>', 'get_order_wise_allocated_qty', '', 'yarn_allocation_controller');
		document.getElementById('po_wise_allocated_qty').value = dataStr;
	</script>

	</html>
<?
	exit();
}

if ($action == 'get_order_wise_allocated_qty') {
	list($orderStr, $booking) = explode('*', $data);

	$sql_result = sql_select("select po_break_down_id,sum(qnty) as qnty from inv_material_allocation_dtls where po_break_down_id in($orderStr) and booking_no='$booking' and status_active=1 and is_deleted=0 group by po_break_down_id");
	$allocation_qty_arr = array();
	foreach ($sql_result as $row) {
		$allocation_qty_arr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')] . '*' . $row[csf('qnty')];
	}
	echo implode(',', $allocation_qty_arr);
	exit();
}

if ($action == 'find_similer_yarn') {

	list($company, $prod_id, $count_id, $composition_id, $yarn_type, $color_id, $vs_minimum_available_qty, $vs_age_limit) = explode('*', $data);

	$similer_yarn_cond = " and a.yarn_count_id='$count_id'  and a.yarn_comp_type1st='$composition_id' and a.yarn_type='$yarn_type' and a.color=$color_id";

	if ($vs_minimum_available_qty > 0) {
		$vs_min_available_cond = " and $vs_minimum_available_qty<=a.available_qnty";
	}

	$sql = "select a.id from product_details_master a where a.company_id=$company and a.item_category_id=1 and a.current_stock > 0 and a.id!=$prod_id and a.status_active=1 and a.is_deleted=0 $similer_yarn_cond $vs_min_available_cond ";
	//echo $sql; die();
	$result = sql_select($sql);
	$prod_id_arr = array();

	if (!empty($result)) {
		foreach ($result as $row) {
			$prod_id_arr[$row[csf('id')]] = $row[csf('id')];
		}
	}

	if (!empty($prod_id_arr)) {
		$transaction_date_arr = array();
		$sql_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where item_category=1 and prod_id in (" . implode(",", $prod_id_arr) . ") and status_active=1 and is_deleted=0 group by prod_id";
		$sql_date = sql_select($sql_date);

		foreach ($sql_date as $row_d) {
			$transaction_date_arr[$row_d[csf('prod_id')]]['min_date'] = $row_d[csf('min_date')];
			$transaction_date_arr[$row_d[csf('prod_id')]]['max_date'] = $row_d[csf('max_date')];
		}
	}

	if (!empty($result)) {
		$number_of_record_arr = array();
		foreach ($result as $row) {
			$ageOfDays = datediff("d", $transaction_date_arr[$row[csf("id")]]['min_date'], date("Y-m-d"));

			if ($ageOfDays > $vs_age_limit) {
				$number_of_record_arr[$row[csf("id")]] = $row[csf("id")];
			}
		}
	}


	$number_of_records = count($number_of_record_arr);
	echo $number_of_records;
	exit();
}

function validation_booking_qty_with_order_ratio($qnty_breck_down_str, $booking_no, $pre_qnty_breck_down_str)
{
	$poIdArr = array();
	foreach (explode(',', $qnty_breck_down_str) as $dataStr) {
		list($qty, $poId, $poNo) = explode('_', $dataStr);
		$poIdArr[$poId] = $poId;
		$poWiseQtyArr[$poId] = $qty;
	}

	foreach (explode(',', $pre_qnty_breck_down_str) as $dataStr) {
		list($qty, $poId, $poNo) = explode('_', $dataStr);
		$poWiseQtyArr[$poId] -= $qty;
	}


	$sql_result = sql_select("select sum(grey_fab_qnty) as booking_qty from wo_booking_dtls where booking_no=$booking_no and status_active=1 and is_deleted=0");
	foreach ($sql_result as $row) {
		$book_qty = $row[csf('booking_qty')];
	}
	//return $book_qty;die;


	$sql_result = sql_select("select a.po_break_down_id, (a.qnty) as qnty, (b.po_quantity) as po_quantity   from inv_material_allocation_dtls a, wo_po_break_down b where b.id=a.po_break_down_id   and a.po_break_down_id in(" . implode(',', $poIdArr) . ") and a.booking_no=$booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$data_arr = array();
	foreach ($sql_result as $row) {
		$data_arr['po_qty'][$row[csf('po_break_down_id')]] = $row[csf('po_quantity')];
		$data_arr['allo_qty'][$row[csf('po_break_down_id')]] += $row[csf('qnty')];
	}


	foreach ($poIdArr as $po_id) {
		$totalPoQty = array_sum($data_arr['po_qty']);
		$totalAlloQty = array_sum($data_arr['allo_qty']);

		$allocation_capacity_qty = ($book_qty / $totalPoQty) * $data_arr['po_qty'][$po_id];
		$allocation_qty = $data_arr['allo_qty'][$po_id] + $poWiseQtyArr[$po_id];

		if ($allocation_capacity_qty < $allocation_qty) {
			return 1;
		} else {
			return 0;
		}
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

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	//print_r($process);die;
	extract(check_magic_quote_gpc($process));

	$txt_qnty = str_replace("'", '', $txt_qnty) * 1;
	$txt_old_qnty = str_replace("'", '', $txt_old_qnty);

	$hdn_dyed_type = str_replace("'", '', $hdn_dyed_type) * 1;
	$hdn_is_without_order = str_replace("'", '', $hdn_is_without_order) * 1;

	// check variable settings if allocation is available or not
	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id = 1");

	if ($variable_set_allocation == 2) {
		echo "3**Yarn allocation is not available.";
		die;
	}

	if ($operation == 0) // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$varialble_setting = sql_select("select during_issue,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name=$cbo_company_name  and variable_list=25 and status_active=1 and is_deleted=0");
		$during_issue = $varialble_setting[0][csf('during_issue')];
		$control_level = $varialble_setting[0][csf('user_given_code_status')];
		$tolerant_percent = $varialble_setting[0][csf('tolerant_percent')];

		if (($during_issue != "") && (str_replace("'", '', $txt_job_no) != "")) // ommit sample
		{
			if (!count_type_rate_validate($txt_job_no, $txt_item_id) && $during_issue == 1 && $control_level == 1) {
				echo "CTV**";
				die;
			}
		}

		$sql_prod = "SELECT current_stock AS CURRENT_STOCK,available_qnty AS AVAILABLE_QNTY FROM product_details_master WHERE id=" . $txt_item_id;
		$sql_prod_rslt = sql_select($sql_prod);
		$check_prod_available_qnty = $sql_prod_rslt[0]['AVAILABLE_QNTY'];
		$check_prod_stock_qnty = $sql_prod_rslt[0]['CURRENT_STOCK'];

		if (str_replace("'", '', $txt_qnty) * 1 > $check_prod_available_qnty) {
			echo "7**Allocation quantity is not available\nAvailable quantity = " . $check_prod_available_qnty;
			die;
		}

		if (str_replace("'", '', $txt_qnty) * 1 > $check_prod_stock_qnty) {
			echo "7**Allocation quantity is not available\nAvailable quantity = " . $check_prod_stock_qnty;
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

		// monzu end
		$field_array = "id,entry_form,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,booking_without_order,is_dyied_yarn,remarks,inserted_by,insert_date";
		$field_array_mst_log = "id,entry_form,mst_id,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,remarks,inserted_by,insert_date";

		$field_array1 = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
		$field_array_dtls_log = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		$field_array_hystory = "id,mst_id,dtls_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date,company_id";

		// Prepare JOB order array
		if (str_replace("'", '', $txt_job_no) == '') {
			//for sample without order qnty_break_down
			$bookingId = return_field_value("id", "wo_non_ord_samp_booking_mst", "booking_no=$txt_booking_no");
			$qnty_breck_down_order = "'" . $txt_qnty . '_' . $bookingId . '_' . "'";
			$order_nos = $bookingId;

			//for transaction log
			$log_ref_id = $bookingId;
			$log_ref_number = str_replace("'", '', $txt_booking_no);
			$log_dyed_type = $hdn_dyed_type;

			$txt_qnty = str_replace("'", '', $txt_qnty);

			$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
			$sql_allocation = "select * from inv_material_allocation_mst a where a.item_id=$txt_item_id and a.booking_no=$txt_booking_no and booking_without_order=1 and a.status_active=1 and a.is_deleted=0";

			//echo "10**".$sql_allocation; die();

			$check_allocation_array = sql_select($sql_allocation);
			if (!empty($check_allocation_array)) {
				$mst_id = $check_allocation_array[0][csf('id')];
				$exitting_qnty = $check_allocation_array[0][csf('qnty')] + $txt_qnty;
				$field_array_update = "allocation_date*qnty*qnty_break_down*updated_by*update_date";
				$data_array_update = "" . $txt_allocation_date . "*" . $exitting_qnty . "*" . $qnty_breck_down_order . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

				//for inv_mat_allocation_mst_log
				if ($data_array_mst_log != "")
					$data_array_mst_log .= ",";

				$data_array_mst_log .= "(" . $id_mst_log . ",0," . $mst_id . ",'','" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . $exitting_qnty . "," . $qnty_breck_down . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			} else {
				$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
				$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				if ($data_array != "")
					$data_array .= ",";

				$data_array .= "(" . $id . ",0,'','" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $qnty_breck_down_order . "," . $hdn_is_without_order . "," . $hdn_dyed_type . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				if ($data_array1 != "")
					$data_array1 .= ",";

				$data_array1 .= "(" . $id1 . "," . $id . ",'" . $po_wise_data[2] . "','" . $po_wise_data[1] . "'," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $hdn_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				if ($data_array_dtls_log != "")
					$data_array_dtls_log .= ",";

				$id_dtls = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
				$data_array_dtls_log .= "(" . $id_dtls . "," . $id_mst_log . ",'" . $po_wise_data[2] . "','" . $po_wise_data[1] . "'," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$id_hystory = return_next_id_by_sequence("INV_ALLOCAT_HYSTORY_SEQ", "inv_material_allocat_hystory", $con);
				if ($data_array_hystory != "")
					$data_array_hystory .= ",";

				$data_array_hystory .= "(" . $id_hystory . "," . $id . "," . $id1 . ",'" . $po_wise_data[2] . "','" . $po_wise_data[1] . "'," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $txt_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";

				if ($data_array_mst_log != "")
					$data_array_mst_log .= ",";
				$data_array_mst_log .= "(" . $id_mst_log . ",0," . $id . ",'','" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . $txt_qnty . "," . $qnty_breck_down_order . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
		} else {
			$job_order = array();
			$prev_datas = explode(",", str_replace("'", '', $qnty_breck_down));
			foreach ($prev_datas as $prev_data) {
				$po_wise_data = explode("_", $prev_data);
				$job_order[$po_wise_data[2]]["job_wise_order"] .= $po_wise_data[1] . ",";
				$job_order[$po_wise_data[2]]["job_wise_qnty"] += $po_wise_data[0];
			}

			// JOB wise master part save
			$job_data = explode(",", str_replace("'", '', $txt_job_no));
			$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
			foreach ($job_data as $row) {
				$order_nos = rtrim($job_order[$row]["job_wise_order"], ",");
				$qnty_breck_down_order = "";
				foreach ($prev_datas as $prev_data) {
					$qnty_breck_down_by_order = explode("_", $prev_data);
					if ($qnty_breck_down_by_order[2] == $row) {
						// prepare JOB wise qnty_break_down column value e.g. OrderQuantity_OrderNo_JOBNo
						$qnty_breck_down_order .= $qnty_breck_down_by_order[0] . "_" . $qnty_breck_down_by_order[1] . "_" . $qnty_breck_down_by_order[2] . ",";
					}
				}

				$qnty_breck_down_order = rtrim($qnty_breck_down_order, ",");
				$qnty = $job_order[$row]["job_wise_qnty"];
				if ($qnty != "") {
					//for transaction log
					$log_ref_id = return_field_value('ID', 'WO_BOOKING_MST', " STATUS_ACTIVE = 1 AND BOOKING_NO = " . $txt_booking_no, 'id');
					$log_ref_number = str_replace("'", '', $txt_booking_no);
					$log_dyed_type = $hdn_dyed_type;

					$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$order_nos' and a.item_id=$txt_item_id and a.job_no='$row' and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0";
					$check_allocation_array = sql_select($sql_allocation);
					if (!empty($check_allocation_array)) {
						$mst_id = $check_allocation_array[0][csf('id')];
						$exitting_qnty = $check_allocation_array[0][csf('qnty')] + str_replace("'", '', $txt_qnty);
						$field_array_update = "allocation_date*qnty*qnty_break_down*updated_by*update_date";
						$data_array_update = "" . $txt_allocation_date . "*" . $exitting_qnty . "*" . $qnty_breck_down . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

						//for inv_mat_allocation_mst_log
						if ($data_array_mst_log != "")
							$data_array_mst_log .= ",";

						$data_array_mst_log .= "(" . $id_mst_log . ",0," . $mst_id . ",'" . $row . "','" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . $exitting_qnty . "," . $qnty_breck_down . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					} else {
						$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						if ($data_array != "")
							$data_array .= ",";
						$data_array .= "(" . $id . ",0,'" . $row . "','" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . $qnty . ",'" . $qnty_breck_down_order . "'," . $hdn_is_without_order . "," . $hdn_dyed_type . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						//for inv_mat_allocation_mst_log
						if ($data_array_mst_log != "")
							$data_array_mst_log .= ",";

						$data_array_mst_log .= "(" . $id_mst_log . ",0," . $id . ",'" . $row . "','" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . $qnty . ",'" . $qnty_breck_down_order . "'," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					}
				}

				$id_mst_log = $id_mst_log + 1;
			}

			$id_dtls = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
			foreach ($prev_datas as $prev_data) {
				$po_wise_data = explode("_", $prev_data);
				$qnty = ($po_wise_data[0] != "") ? $po_wise_data[0] : 0;
				$order_nos = rtrim($job_order[$po_wise_data[2]]["job_wise_order"], ",");

				if ($qnty > 0) {
					$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$order_nos' and a.item_id=$txt_item_id and a.job_no='" . $po_wise_data[2] . "' and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0";
					$check_allocation_array = sql_select($sql_allocation);
					if (!empty($check_allocation_array)) {
						$mst_id = $check_allocation_array[0][csf('id')];
						execute_query("delete from inv_material_allocation_dtls where mst_id=$mst_id", 1);
						$id = $mst_id;
						$qnty = $qnty + $check_allocation_array[0][csf('qnty')];
					}

					$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					$id_hystory = return_next_id_by_sequence("INV_ALLOCAT_HYSTORY_SEQ", "inv_material_allocat_hystory", $con);
					if ($data_array1 != "")
						$data_array1 .= ",";

					$data_array1 .= "(" . $id1 . "," . $id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $qnty . "," . $hdn_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					if ($data_array_dtls_log != "")
						$data_array_dtls_log .= ",";

					$data_array_dtls_log .= "(" . $id_dtls . "," . $id_mst_log . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					if ($data_array_hystory != "") $data_array_hystory .= ",";
					$data_array_hystory .= "(" . $id_hystory . "," . $id . "," . $id1 . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";
				}

				$id_dtls = $id_dtls + 1;
			}
		}

		/*oci_rollback($con);
		echo "10**INSERT INTO inv_material_allocation_mst (".$field_array.") VALUES ".$data_array."";
		disconnect($con);
		die;*/

		if ($data_array_update != "") {
			$rID = sql_update("inv_material_allocation_mst", $field_array_update, $data_array_update, "id", "" . $mst_id . "", 0);

			$rID_mst_log = sql_insert("inv_mat_allocation_mst_log", $field_array_mst_log, $data_array_mst_log, 0);
		} else {
			//echo "10**INSERT INTO inv_mat_allocation_mst_log (".$field_array_mst_log.") VALUES ".$data_array_mst_log.""; die;
			$rID = sql_insert("inv_material_allocation_mst", $field_array, $data_array, 0);
			$rID_mst_log = sql_insert("inv_mat_allocation_mst_log", $field_array_mst_log, $data_array_mst_log, 0);
		}

		$rID1 = false;
		if ($data_array1 != '') {
			//echo "10**INSERT INTO inv_material_allocation_dtls (".$field_array1.") VALUES ".$data_array1.""; die;
			$rID1 = sql_insert("inv_material_allocation_dtls", $field_array1, $data_array1, 0);
			$rID1_dtls_log = sql_insert("inv_mat_allocation_dtls_log", $field_array_dtls_log, $data_array_dtls_log, 0);
		}

		$rID_history = false;
		if ($data_array_hystory != '') {
			//echo "10**INSERT INTO inv_material_allocat_hystory (".$field_array_hystory.") VALUES ".$data_array_hystory.""; die;
			$rID_history = sql_insert("inv_material_allocat_hystory", $field_array_hystory, $data_array_hystory, 0);
		}

		$txt_qnty = str_replace("'", '', $txt_qnty);
		$rID_de = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$txt_qnty) where id=$txt_item_id", 0);

		$rID_dep = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id  ", 0);

		/*
		echo "10**".$rID ."&&". $rID1 ."&&". $rID_de ."&&". $rID_dep."&&".$rID_mst_log."&&".$rID1_dtls_log."&&".$rID_history;
		disconnect($con);
		die;*/


		if ($db_type == 0) {
			if ($rID && $rID1 && $rID_de && $rID_dep && $rID_mst_log && $rID1_dtls_log && $rID_history) {
				mysql_query("COMMIT");
				echo "0**" . $rID;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $rID;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID1 && $rID_de && $rID_dep && $rID_mst_log && $rID1_dtls_log && $rID_history) {
				//for transaction log
				$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY FROM PRODUCT_DETAILS_MASTER WHERE STATUS_ACTIVE = 1 AND ID = " . $txt_item_id);
				$log_data['entry_form'] = 0;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = $log_ref_number;
				$log_data['product_id'] = $txt_item_id;
				$log_data['current_stock'] = $sql_prod[0]['CURRENT_STOCK'];
				$log_data['allocated_qty'] = $sql_prod[0]['ALLOCATED_QNTY'];
				$log_data['available_qty'] = $sql_prod[0]['AVAILABLE_QNTY'];
				$log_data['dyed_type'] = $log_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);
				//end for transaction log

				oci_commit($con);
				echo "0**" . $rID;
			} else {
				oci_rollback($con);
				echo "10**" . $rID;
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1)  // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$prod_data = sql_select("select id, product_name_details, color, lot, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_type from product_details_master where id=$txt_item_id_old and item_category_id=1");
		foreach ($prod_data as $row) {
			$yarn_count_id = $row[csf('yarn_count_id')];
			$yarn_comp_type1st = $row[csf('yarn_comp_type1st')];
			$yarn_comp_percent1st = $row[csf('yarn_comp_percent1st')];
			$yarn_type = $row[csf('yarn_type')];
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
		if ($yarn_type != "") {
			$yarn_type_cond = "and b.yarn_type=$yarn_type";
		}

		if ($db_type == 0) {
			$check_requisition = sql_select("select sum(y.yarn_qnty) yarn_qnty,group_concat(requisition_no) requisition_no from (select b.dtls_id from ppl_planning_entry_plan_dtls b where b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0 group by b.dtls_id)x, ppl_yarn_requisition_entry y where x.dtls_id=y.knit_id and y.prod_id=$txt_item_id_old and y.status_active=1 and y.is_deleted=0");

			$check_ydw = sql_select("select x.wo_num,sum(x.yarn_wo_qty) yarn_wo_qty from(select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.job_no=$txt_job_no and b.product_id=" . str_replace("'", "", $txt_item_id_old) . "
			union all
			select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125) and b.entry_form in(125) and b.job_no=$txt_job_no $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $yarn_type_cond group by b.job_no,b.product_id)x group by x.wo_num");
		} else {
			$check_requisition = sql_select("select sum(y.yarn_qnty) yarn_qnty,listagg(y.requisition_no, ',') within group (order by y.requisition_no) as requisition_no from (select b.dtls_id from ppl_planning_entry_plan_dtls b where b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0 group by b.dtls_id)x,ppl_yarn_requisition_entry y where x.dtls_id=y.knit_id and y.prod_id=$txt_item_id_old and y.status_active=1 and y.is_deleted=0");

			$check_ydw = sql_select("select x.wo_num,sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.job_no=$txt_job_no and b.product_id=" . str_replace("'", "", $txt_item_id_old) . "
			union all
			select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125) and b.entry_form in(125) and b.job_no=$txt_job_no $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $yarn_type_cond group by b.job_no,b.product_id)x group by x.wo_num");
		}

		/*
		|--------------------------------------------------------------------------
		| sample without order
		|--------------------------------------------------------------------------
		|
		*/
		$req_sql = "
			SELECT
				a.booking_no, c.knit_id, c.prod_id, c.requisition_no, c.yarn_qnty
			FROM
				ppl_planning_info_entry_mst a
				INNER JOIN ppl_planning_info_entry_dtls b ON a.id = b.mst_id
				INNER JOIN ppl_yarn_requisition_entry c ON b.id = c.knit_id
			WHERE
				a.status_active = 1
				AND a.is_deleted = 0
				AND a.is_sales = 2
				AND a.booking_no = " . $txt_booking_no . "
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND b.is_sales = 2
				AND c.status_active = 1
				AND c.is_deleted = 0
				AND c.prod_id IN(" . $txt_item_id_old . ") ";

		//echo "10**".$req_sql; die;
		$req_result = sql_select($req_sql);
		$requisitionQtyArr = array();
		foreach ($req_result as $row) {
			$requisitionQtyArr['reqQty'] += $row[csf("yarn_qnty")] * 1;
			$requisitionQtyArr['reqNo'] = $row[csf("requisition_no")];
		}
		//echo "10**<pre>";
		//print_r($requisitionQtyArr);
		//die();

		// check if product is changed in update event
		if ($txt_item_id_old != $txt_item_id) {
			// check if previous product has any YDW
			if (!empty($check_ydw) && ($check_ydw[0][csf('yarn_wo_qty')] != "" || $check_ydw[0][csf('yarn_wo_qty')] > 0)) {
				echo "7**Yarn Dyeing Work Order found. Allocated yarn can not be changed.\nYarn Dyeing Work Order no = " . $check_ydw[0][csf('ydw_no')];
				die;
			}

			// check if previous product has any requisition
			if (!empty($check_requisition) && (trim($check_requisition[0][csf('yarn_qnty')]) != "" || trim($check_requisition[0][csf('yarn_qnty')]) > 0)) {
				echo "7**Requisition found. Allocated yarn can not be changed.\nRequisition no = " . $check_requisition[0][csf('requisition_no')];
				die;
			}

			// check if new product has available qnty for allocation
			$available_qnty = return_field_value("available_qnty", "product_details_master", "company_id=$cbo_company_name and id=$txt_item_id");

			if ($txt_qnty > $available_qnty) {
				echo "5**Allocation quantity is not available.\nAvailable=" . $available_qnty;
				die;
			}

			/*
			|--------------------------------------------------------------------------
			| for sample without order
			|--------------------------------------------------------------------------
			|
			*/
			if (!empty($requisitionQtyArr) && $requisitionQtyArr['reqQty'] > 0) {
				echo "7**Requisition found. Allocated yarn can not be changed.\nRequisition no = " . $requisitionQtyArr['reqNo'];
				die;
			}
		} else {
			//echo "10**test"; die();
			$check_prod_available_qnty = return_field_value("available_qnty", "product_details_master", "id=$txt_item_id_old");
			$txt_old_qnty = str_replace("'", '', $txt_old_qnty);
			$availableQty = ($check_prod_available_qnty + $txt_old_qnty);
			$txt_qnty = str_replace("'", '', $txt_qnty);
			//echo "10**test=$txt_qnty==$check_prod_available_qnty==$availableQty"; die();
			if (round($txt_qnty, 2) > round($availableQty, 2)) {
				echo "7**Allocation quantity is not available\nAvailable quantity = " . $availableQty;
				die;
			}

			$order_ids = str_replace("'", '', $txt_order_id);
			$booking_no = str_replace("'", '', $txt_booking_no);
			$hdn_dyed_type = str_replace("'", '', $hdn_dyed_type);
			$txt_job_no = str_replace("'", '', $txt_job_no);
			$txt_old_qnty = str_replace("'", '', $txt_old_qnty);

			//new
			$total_allocation_order_wise = sql_select("select booking_no,sum(qnty) qnty from inv_material_allocation_dtls where item_id=$txt_item_id_old and job_no='$txt_job_no' and status_active=1 and is_deleted=0 group by booking_no");
			$booking_nos = "";
			$other_reference_allocation = 0;
			foreach ($total_allocation_order_wise as $al_allocation_row) {
				$booking_wise_allocation[$al_allocation_row[csf("booking_no")]] = $al_allocation_row[csf("qnty")];
				if ($al_allocation_row[csf("booking_no")] != $booking_no) {
					$other_reference_allocation += $al_allocation_row[csf("qnty")];
				}

				$booking_nos .= "'" . $al_allocation_row[csf("booking_no")] . "',";
			}
			$booking_nos = rtrim($booking_nos, ", ");

			if ($db_type == 0) {
				$check_booking_requisition = sql_select("select x.booking_no,sum(y.yarn_qnty) yarn_qnty,group_concat(distinct(y.requisition_no)) as requisition_no from (select b.dtls_id,b.booking_no from ppl_planning_entry_plan_dtls b where b.booking_no in($booking_nos) and b.status_active=1 and b.is_deleted=0 group by b.dtls_id,b.booking_no)x,ppl_yarn_requisition_entry y where x.dtls_id=y.knit_id and y.prod_id=$txt_item_id_old and y.status_active=1 and y.is_deleted=0 group by x.booking_no");
			} else {
				$check_booking_requisition = sql_select("select x.booking_no,sum(y.yarn_qnty) yarn_qnty,listagg(y.requisition_no, ',') within group (order by y.requisition_no) as requisition_no from (select b.dtls_id,b.booking_no from ppl_planning_entry_plan_dtls b where b.booking_no in($booking_nos) and b.status_active=1 and b.is_deleted=0 group by b.dtls_id,b.booking_no)x,ppl_yarn_requisition_entry y where x.dtls_id=y.knit_id and y.prod_id=$txt_item_id_old and y.status_active=1 and y.is_deleted=0 group by x.booking_no");
			}

			$other_reference_requisition = 0;
			foreach ($check_booking_requisition as $br_row) {
				$booking_wise_requisition[$br_row[csf("booking_no")]]["req_qnty"] = $br_row[csf("yarn_qnty")];
				$booking_wise_requisition[$br_row[csf("booking_no")]]["requisition_no"] = $br_row[csf("requisition_no")];
				if ($br_row[csf("booking_no")] != $booking_no) {
					$other_reference_requisition += $br_row[csf("yarn_qnty")];
				}
			}

			$other_ref_balance = ($other_reference_allocation - $other_reference_requisition) - $check_ydw[0][csf("yarn_wo_qty")] * 1;
			//echo "6**$other_reference_allocation==$other_reference_requisition==$ywo_qty"; die();
			$balance_qnty = $booking_wise_requisition[$booking_no]["req_qnty"] + (($other_ref_balance < 0) ?  abs($other_ref_balance) : 0);
			//echo "6**".$balance_qnty; die();

			if (str_replace("'", '', $txt_qnty) < $balance_qnty) {
				$allocation_total = $booking_wise_allocation[$booking_no] + $other_reference_allocation;
				$requisition_total = $booking_wise_requisition[$booking_no]["req_qnty"] + $other_reference_requisition;
				$wo_numbers = implode(",", array_unique(explode(",", $check_ydw[0][csf("wo_num")])));
				$wo_total = $check_ydw[0][csf("yarn_wo_qty")] * 1;
				$requisition_nos = $booking_wise_requisition[$booking_no]["requisition_no"];
				echo "6**Allocation quantity can not be less than Requisition/WO quantity.\nTotal Allocation =" . number_format($allocation_total, 2, ".", "") . "\nRequisition No: $requisition_nos\nTotal Requisition quantity= " . number_format($requisition_total, 2, ".", "") . "\nWo No:$wo_numbers\nTotal WO quantity=" . number_format($wo_total, ".", "") . "\nReducible Qty = " . number_format(($allocation_total - $balance_qnty), 2, ".", "");
				disconnect($con);
				die;
			}

			//for order wise allocation and requisition check
			$reqNo = $booking_wise_requisition[$booking_no]["requisition_no"];
			$sqlReq = "SELECT id, requisition_id, program_id, order_id, item_id, order_requisition_qty, requisition_qty, booking_no FROM ppl_yarn_requisition_breakdown WHERE booking_no = " . $txt_booking_no . " AND requisition_id IN(" . $reqNo . ") AND status_active = 1 AND is_deleted = 0";
			$sqlReqRslt = sql_select($sqlReq);
			$reqData = array();
			foreach ($sqlReqRslt as $row) {
				$reqData[$row[csf('item_id')]][$row[csf('order_id')]] += $row[csf('order_requisition_qty')];
			}

			if (!empty($reqData)) {
				$itemId = str_replace("'", "", $txt_item_id);
				$exp = explode(",", str_replace("'", '', $qnty_breck_down));
				$errrorArr = array();
				$ordIdArr = array();
				$number_errrorArr = array();
				foreach ($exp as $val) {
					$expVal = explode("_", $val);

					$order_wise_req_qty = number_format($reqData[$itemId][$expVal[1]], 2, '.', '');
					$po_alc_qty = number_format($expVal[0], 2, '.', '');
					//echo "10**".$order_wise_req_qty .">". $expVal[0]."<br>";
					$er = 0;
					if (($order_wise_req_qty != '0.00') && ($order_wise_req_qty > $po_alc_qty)) {
						$ordIdArr[$expVal[1]] = $expVal[1];
						$errrorArr[$expVal[1]]['allocationQty'] = $expVal[0];
						$errrorArr[$expVal[1]]['requisitionQty'] = $reqData[$itemId][$expVal[1]];
						$number_errrorArr[$er] = $er + 1;
					}
				}

				if (count($number_errrorArr) > 0) {
					$ordDtls = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a WHERE a.id IN(" . implode(',', $ordIdArr) . ")", 'id', 'po_number');
					$msg = '';
					foreach ($errrorArr as $ordId => $ordArr) {
						$msg .= "\nOrder no " . $ordDtls[$ordId] . "\nAllocation qt: " . number_format($ordArr['allocationQty'], 2, ".", "") . "\nrequisition qty : " . number_format($ordArr['requisitionQty'], 2, ".", "");
					}

					echo "6**Order Wise allocation quantity can not be less than order requisition quantity.\nRequisition No: " . $reqNo . $msg;
					die;
				}
			}

			if (!empty($requisitionQtyArr) &&  $requisitionQtyArr['reqQty'] > 0 && str_replace("'", '', $txt_qnty) < $requisitionQtyArr['reqQty']) {
				$requisitionNo = $requisitionQtyArr['reqNo'];
				echo "6**Allocation quantity can not be less than Requisition quantity.\nRequisition Qty = " . number_format($requisitionQtyArr['reqQty'], 2, ".", "") . " \nRequisition No : " . $requisitionNo . "";
				disconnect($con);
				die;
			}

			//for order wise allocation and issue and issue return balance check
			$orderIds = str_replace("'", "", $txt_order_id);
			$orderTransSql = "select distinct(c.id),c.po_breakdown_id, c.prod_id,b.transaction_type, c.quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c, wo_yarn_dyeing_dtls d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and a.issue_basis=1 and a.booking_id=d.mst_id and b.transaction_type=2 and b.item_category=1 and b.receive_basis=1 and c.po_breakdown_id in ($orderIds) and c.prod_id=$txt_item_id and (d.fab_booking_no=$txt_booking_no or d.booking_no=$txt_booking_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0

				union all

				select distinct(c.id),c.po_breakdown_id, c.prod_id,b.transaction_type, c.quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_yarn_dyeing_dtls d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and a.receive_basis=1 and a.booking_id=d.mst_id and b.transaction_type=4 and b.item_category=1 and b.receive_basis=1 and c.po_breakdown_id in ($orderIds) and c.prod_id=$txt_item_id and (d.fab_booking_no=$txt_booking_no or d.booking_no=$txt_booking_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";

			//echo "10**".$orderTransSql; die();

			$orderTransResult = sql_select($orderTransSql);
			$orderwiseIssueArr = $orderwiseIssueRtnArr = array();
			foreach ($orderTransResult as $row) {
				if ($row[csf('transaction_type')] == 2) {
					$orderwiseIssueArr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
				} else {
					$orderwiseIssueRtnArr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
				}
			}

			//echo "10**".$orderwise_issue_rtnsql; die();
			if (!empty($orderwiseIssueArr)) {
				//echo "10**test"; die();
				$itemId = str_replace("'", "", $txt_item_id);
				$exp = explode(",", str_replace("'", '', $qnty_breck_down));
				$errrorArr = array();
				$ordIdArr = array();
				foreach ($exp as $val) {
					$expVal = explode("_", $val);

					$actualIssueQty = ($orderwiseIssueArr[$itemId][$expVal[1]] - $orderwiseIssueRtnArr[$itemId][$expVal[1]]);

					if ($actualIssueQty > $expVal[0]) {
						$ordIdArr[$expVal[1]] = $expVal[1];
						$errrorArr[$expVal[1]]['allocationQty'] = $expVal[0];
						$errrorArr[$expVal[1]]['actual_issueQty'] = $actualIssueQty;
					}
				}

				if (!empty($errrorArr)) {
					$ordDtls = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a WHERE a.id IN(" . implode(',', $ordIdArr) . ")", 'id', 'po_number');
					$msg = '';
					foreach ($errrorArr as $ordId => $ordArr) {
						$msg .= "\nOrder no " . $ordDtls[$ordId] . ", Issue qty : " . number_format($ordArr['actual_issueQty'], 2, ".", "");
					}

					echo "6**Issue Found.\nOrder Wise allocation quantity\ncan not be less than order requisition quantity.\n" . $msg;
					die;
				}
			}
		}

		//match yarn description with budget
		$varialble_setting = sql_select("select during_issue,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name=$cbo_company_name  and variable_list=25 and status_active=1 and is_deleted=0");
		$during_issue = $varialble_setting[0][csf('during_issue')];
		$control_level = $varialble_setting[0][csf('user_given_code_status')];
		$tolerant_percent = $varialble_setting[0][csf('tolerant_percent')];

		if (($during_issue != "") && (str_replace("'", '', $txt_job_no) != "")) {
			if (!count_type_rate_validate($txt_job_no, $txt_item_id) && $during_issue == 1 && $control_level == 1) {
				echo "CTV**";
				disconnect($con);
				die;
			}
		}
		// monzu end

		$job_order = $pre_job_order = array();
		$old_item_id = str_replace('"', '', $txt_item_id_old);
		$pre_qnty_breck_down = explode(",", str_replace("'", '', $pre_qnty_breck_down));
		foreach ($pre_qnty_breck_down as $prev_data) {
			$po_wise_data = explode("_", $prev_data);
			$pre_job_order[$po_wise_data[1]] .= $po_wise_data[0] . ",";
			$pre_job_order[$po_wise_data[1]] .= $po_wise_data[0] . ",";
			$pre_order_qnty[$po_wise_data[1]] = $po_wise_data[0];
		}

		if (str_replace("'", "", $update_id) != "") // cancel po validation
		{
			$qnty_breck_down_arr = explode(",", str_replace("'", '', $qnty_breck_down));
			foreach ($qnty_breck_down_arr as $current_data_str) {
				$po_wise_current_data = explode("_", $current_data_str);
				$po_id = $po_wise_current_data[1];
				$current_allocation_qty = $po_wise_current_data[0];
				$prev_allocated_qty = $pre_order_qnty[$po_id];

				$sql_po_result = sql_select("select status_active as po_status,po_number from wo_po_break_down where id=$po_id and is_deleted=0");

				$po_status = $sql_po_result[0][csf('po_status')];
				$po_number = $sql_po_result[0][csf('po_number')];

				if ($po_status == 3 && $prev_allocated_qty < $current_allocation_qty) {
					echo "7**The '$po_number' is cancel PO.\nAllocation quantity can not exceed exist quantity.\nExisting quantity=$prev_allocated_qty";
					die();
				}
			}
		}

		$rID_history = false;
		$field_array = "allocation_date*item_id*qnty*qnty_break_down*remarks*updated_by*update_date";
		$field_array1 = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
		$field_array_mst_log = "id,entry_form,mst_id,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,remarks,inserted_by,insert_date";
		$field_array_dtls_log = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		$field_array_hystory = "id,mst_id,dtls_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date,company_id";

		//for grey yarn
		if ($hdn_is_without_order != 1) {
			$data_array = "" . $txt_allocation_date . "*" . $txt_item_id . "*" . $txt_qnty . "*" . $qnty_breck_down . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
			$quantity_break_datas = explode(",", str_replace("'", '', $qnty_breck_down));
			$newlogMstQntBreak = "";
			$new_log_mst_total_qnty = 0;
			$newMstPoId = "";
			foreach ($quantity_break_datas as $break_data) {
				//echo "10**";
				$dtls_id 	= return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
				$id_hystory = return_next_id_by_sequence("INV_ALLOCAT_HYSTORY_SEQ", "inv_material_allocat_hystory", $con);
				if ($old_item_id != $txt_item_id) {
					$id_hystory1 = return_next_id_by_sequence("INV_ALLOCAT_HYSTORY_SEQ", "inv_material_allocat_hystory", $con);
				}

				$po_wise_data = explode("_", $break_data);
				$qnty = ($po_wise_data[0] != "") ? $po_wise_data[0] : 0;

				if ($data_array1 != "")
					$data_array1 .= ",";

				$data_array1 .= "(" . $dtls_id . "," . $update_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $qnty . "," . $hdn_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$new_log_dtls_qnty = ($qnty - $pre_order_qnty[$po_wise_data[1]]);
				$new_log_mst_total_qnty += $new_log_dtls_qnty;

				if ($newlogMstQntBreak == "") {
					$newlogMstQntBreak = $new_log_dtls_qnty . "_" . $po_wise_data[1] . "_" . $po_wise_data[2];
					$newMstPoId = $po_wise_data[1];
				} else {
					$newlogMstQntBreak .= "," . $new_log_dtls_qnty . "_" . $po_wise_data[1] . "_" . $po_wise_data[2];
					$newMstPoId .= "," . $po_wise_data[1];
				}

				//if($new_log_dtls_qnty!=0)
				//{
				$data_array_dtls_log .= "(" . $id_dtls_log . "," . $id_mst_log . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $new_log_dtls_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$id_dtls_log = $id_dtls_log + 1;

				if ($data_array_dtls_log != "") {
					$data_array_dtls_log .= ",";
				}
				//}

				$pre_allocated_dtls = return_field_value("id", "inv_material_allocation_dtls", "job_no='$txt_job_no' and item_id=$txt_item_id_old and po_break_down_id=$po_wise_data[1] and status_active=1 and is_deleted=0");

				if ($pre_allocated_dtls != "") {
					$check_allocation_history_sql = "select * from inv_material_allocat_hystory where dtls_id=$pre_allocated_dtls and job_no='$txt_job_no' and item_id=$txt_item_id_old and po_break_down_id=$po_wise_data[1] order by id desc";
					$check_allocation_history_array = sql_select($check_allocation_history_sql);
					foreach ($check_allocation_history_array as $his_row) {
						$allocation_insert_date = date("d-M-Y", strtotime($his_row[csf("insert_date")]));
						$date_wise_allocation[$his_row[csf("dtls_id")]][$his_row[csf("po_break_down_id")]][$his_row[csf("job_no")]][$his_row[csf("item_id")]][$allocation_insert_date] = $his_row[csf("id")];
						$date_allocation[$allocation_insert_date]["id"]   = $his_row[csf("id")];
						$date_allocation[$allocation_insert_date]["date"] = $allocation_insert_date;
						$date_allocation[$allocation_insert_date]["qnty"] = $his_row[csf("qnty")];
					}
				}

				$current_date = date("d-M-Y", strtotime($pc_date_time));
				$history_id = $date_wise_allocation[$pre_allocated_dtls][$po_wise_data[1]][str_replace("'", '', $txt_job_no)][str_replace("'", '', $txt_item_id_old)][$current_date];

				$cur_date_allocation = $qnty - $pre_order_qnty[$po_wise_data[1]];

				$insert_qnty = $qnty - $pre_order_qnty[$po_wise_data[1]];
				$old_po_qnty = $pre_order_qnty[$po_wise_data[1]];
				if ($old_item_id != $txt_item_id) {
					$insert_qnty = $qnty;
				}

				if ($qnty != $pre_order_qnty[$po_wise_data[1]]) {
					if ($qnty > $pre_order_qnty[$po_wise_data[1]]) {
						$qnty_cond = ",qnty=qnty+$cur_date_allocation";
					} else {
						$al_qnty = $pre_order_qnty[$po_wise_data[1]] - $qnty;
						$qnty_cond = ",qnty=qnty-$al_qnty";
					}
				} else {
					$qnty_cond = "";
				}

				if ($history_id != "") {
					$updated_by = $_SESSION['logic_erp']['user_id'];
					$rID_history = execute_query("update inv_material_allocat_hystory set dtls_id=$dtls_id,item_id=$txt_item_id,allocation_date='$pc_date',updated_by=$updated_by,update_date='$pc_date_time' where id=$history_id", 1);
					execute_query("update inv_material_allocat_hystory set dtls_id=$dtls_id where mst_id=$update_id", 1);

					if ($qnty >= $pre_order_qnty[$po_wise_data[1]]) {
						if ($data_array_hystory != "")
							$data_array_hystory .= ",";

						$data_array_hystory .= "(" . $id_hystory . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $insert_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";

						if ($old_item_id != $txt_item_id) {
							if ($data_array_hystory != "")
								$data_array_hystory .= ",";

							$data_array_hystory .= "(" . $id_hystory1 . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $old_item_id . "," . -$old_po_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";
						}

						$rID_history = execute_query("update inv_material_allocat_hystory set dtls_id=$dtls_id where mst_id=$update_id and id!=$id_hystory", 1);
					} else {
						if ($data_array_hystory != "")
							$data_array_hystory .= ",";

						$cur_date_allocation = ($cur_date_allocation != $pre_order_qnty[$po_wise_data[1]]) ? $cur_date_allocation - $pre_order_qnty[$po_wise_data[1]] : $cur_date_allocation;
						$data_array_hystory .= "(" . $id_hystory . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $insert_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";

						if ($old_item_id != $txt_item_id) {
							if ($data_array_hystory != "")
								$data_array_hystory .= ",";

							$data_array_hystory .= "(" . $id_hystory1 . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $old_item_id . "," . -$old_po_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";
						}
					}
				} else {
					if ($qnty >= $pre_order_qnty[$po_wise_data[1]]) {
						if ($data_array_hystory != "")
							$data_array_hystory .= ",";

						$data_array_hystory .= "(" . $id_hystory . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $insert_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";

						if ($txt_item_id != $old_item_id) {
							if ($data_array_hystory != "")
								$data_array_hystory .= ",";

							$data_array_hystory .= "(" . $id_hystory1 . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $old_item_id . "," . -$old_po_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";
						}
						$rID_history = execute_query("update inv_material_allocat_hystory set dtls_id=$dtls_id where mst_id=$update_id and id!=$id_hystory", 1);
					} else {
						if ($data_array_hystory != "")
							$data_array_hystory .= ",";

						$cur_date_allocation = ($cur_date_allocation != $pre_order_qnty[$po_wise_data[1]]) ? $cur_date_allocation - $pre_order_qnty[$po_wise_data[1]] : $cur_date_allocation;
						$data_array_hystory .= "(" . $id_hystory . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $insert_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";
						if ($txt_item_id != $old_item_id) {
							if ($data_array_hystory != "")
								$data_array_hystory .= ",";

							$data_array_hystory .= "(" . $id_hystory1 . "," . $update_id . "," . $dtls_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $old_item_id . "," . -$old_po_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_company_name . ")";
						}

						$al_balance = $cur_date_allocation;
						foreach ($date_allocation as $date => $date_row) {
							$hist_id   = $date_row["id"];
							$date_qnty = $date_row["qnty"];
							$balance   = $al_balance - $date_qnty;
							if ($balance > 0) {
								$al_balance = ($al_balance > $date_qnty) ? $al_balance - $date_qnty : $date_qnty - $al_balance;
								$qnty = 0;
							} else {
								if ($al_balance > 0) {
									$al_balance = ($al_balance > $date_qnty) ? $al_balance - $date_qnty : $date_qnty - $al_balance;
									$qnty = $al_balance;
								} else {
									$al_balance = 0;
									$qnty = $date_qnty;
								}
							}

							$qnty_cond = ",qnty=$qnty";
							$updated_by = $_SESSION['logic_erp']['user_id'];
							if ($al_balance >= 0) {
								$rID_history = execute_query("update inv_material_allocat_hystory set dtls_id=$dtls_id $qnty_cond where id=$hist_id", 1);
							} else {
								$rID_history = execute_query("update inv_material_allocat_hystory set dtls_id=$dtls_id,qnty=$al_balance where id=$hist_id", 1);
							}
						}
					}
				}
			}
			$data_array_dtls_log = chop($data_array_dtls_log, ",");

			//if($new_log_dtls_qnty!=0)
			//{
			$data_array_mst_log = "(" . $id_mst_log . ",0," . $update_id . ",'" . $po_wise_data[2] . "','" . $newMstPoId . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . $new_log_mst_total_qnty . ",'" . $newlogMstQntBreak . "'," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			//}
		} else //for sample without order qnty_break_down
		{
			$bookingId = return_field_value("id", "wo_non_ord_samp_booking_mst", "booking_no=$txt_booking_no");
			$qntyBreakdown = $txt_qnty . '_' . $bookingId . '_';
			$order_nos = $bookingId;

			$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
			$dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
			$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
			$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);

			$pre_allocated_qnty = return_field_value("qnty", "inv_material_allocation_mst", "id=$update_id");
			$qnty = ($po_wise_data[0] != "") ? $po_wise_data[0] : 0;

			//for inv_material_allocation_mst
			$data_array = "" . $txt_allocation_date . "*" . $txt_item_id . "*" . $txt_qnty . "*'" . $qntyBreakdown . "'*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			//for inv_material_allocation_dtls
			$data_array1 = "(" . $dtls_id . "," . $update_id . ",'" . $txt_job_no . "'," . $order_nos . "," . $txt_booking_no . ",1," . $txt_allocation_date . "," . str_replace("'", '', $txt_item_id) . "," . $txt_qnty . "," . $hdn_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			//for inv_mat_allocation_mst_log
			$data_array_mst_log = "(" . $id_mst_log . ",0," . $update_id . ",'" . $txt_job_no . "','" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . str_replace("'", '', $txt_item_id) . "," . $txt_qnty . ",'" . $qntyBreakdown . "'," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			//for inv_mat_allocation_dtls_log
			$data_array_dtls_log = "(" . $id_dtls_log . "," . $id_mst_log . ",'" . $txt_job_no . "'," . $order_nos . "," . $txt_booking_no . ",1," . $txt_allocation_date . "," . str_replace("'", '', $txt_item_id) . "," . $txt_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		}

		$rID = sql_update("inv_material_allocation_mst", $field_array, $data_array, "id", "" . $update_id . "", 0);
		$rID_delete = execute_query("delete from inv_material_allocation_dtls where mst_id=$update_id", 1);

		$rID1 = true;
		$rID1_dtls_log = true;
		$rID_de = true;
		$rID_deal = true;
		$rID_mst_log = true;
		$rID_history = true;

		if ($data_array1 != '') {
			$rID1 = sql_insert("inv_material_allocation_dtls", $field_array1, $data_array1, 0);
		}

		if ($data_array_mst_log != '') {
			$rID_mst_log = sql_insert("inv_mat_allocation_mst_log", $field_array_mst_log, $data_array_mst_log, 0);
			$rID1_dtls_log = sql_insert("inv_mat_allocation_dtls_log", $field_array_dtls_log, $data_array_dtls_log, 0);
		}

		if ($txt_item_id_old == $txt_item_id) {
			$rID_adj = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-($txt_old_qnty-$txt_qnty)) where id=$txt_item_id_old", 0);
			$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id_old  ", 0);
		} else {
			$rID_adj = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$txt_old_qnty) where id=$txt_item_id_old", 0);
			$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id_old  ", 0);
			$rID_de = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$txt_qnty) where id=$txt_item_id", 0);
			$rID_deal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id", 0);
		}

		if ($data_array_hystory != '') {
			$rID_history = sql_insert("inv_material_allocat_hystory", $field_array_hystory, $data_array_hystory, 0);
		}

		//echo "10**insert into inv_mat_allocation_mst_log($field_array_mst_log)values($data_array_mst_log)";die;

		/*oci_rollback($con);
		echo "10**".$rID ."&&". $rID1 ."&&". $rID_adj ."&&". $rID_adjal ."&&". $rID_de ."&&". $rID_deal ."&&". $rID_mst_log ."&&". $rID1_dtls_log ."&&". $rID_history;
		disconnect();
		die;*/

		if ($db_type == 0) {
			if ($rID && $rID1 && $rID_adj && $rID_adjal && $rID_de && $rID_deal && $rID_mst_log && $rID1_dtls_log && $rID_history) {
				mysql_query("COMMIT");
				echo "1**" . $rID;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $rID;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID1 && $rID_adj && $rID_adjal && $rID_de && $rID_deal && $rID_mst_log && $rID1_dtls_log && $rID_history) {
				//for transaction log
				if ($hdn_is_without_order != 1) {
					$log_ref_id = $log_ref_id = return_field_value('ID', 'WO_BOOKING_MST', " STATUS_ACTIVE = 1 AND BOOKING_NO = " . $txt_booking_no, 'id');
				} else {
					$log_ref_id = $bookingId;
				}

				//for same product
				$sql_prod_old = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY FROM PRODUCT_DETAILS_MASTER WHERE STATUS_ACTIVE = 1 AND ID = " . $txt_item_id_old);
				$log_data_old['entry_form'] = 0;
				$log_data_old['ref_id'] = $log_ref_id;
				$log_data_old['ref_number'] = str_replace("'", '', $txt_booking_no);
				$log_data_old['product_id'] = $txt_item_id_old;
				$log_data_old['current_stock'] = $sql_prod_old[0]['CURRENT_STOCK'];
				$log_data_old['allocated_qty'] = $sql_prod_old[0]['ALLOCATED_QNTY'];
				$log_data_old['available_qty'] = $sql_prod_old[0]['AVAILABLE_QNTY'];
				$log_data_old['dyed_type'] = $hdn_dyed_type;
				$log_data_old['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data_old);
				//end for transaction log

				//for new product
				if ($txt_item_id_old != $txt_item_id) {
					$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY FROM PRODUCT_DETAILS_MASTER WHERE STATUS_ACTIVE = 1 AND ID = " . $txt_item_id);
					$log_data['entry_form'] = 0;
					$log_data['ref_id'] = $log_ref_id;
					$log_data['ref_number'] = str_replace("'", '', $txt_booking_no);
					$log_data['product_id'] = $txt_item_id;
					$log_data['current_stock'] = $sql_prod[0]['CURRENT_STOCK'];
					$log_data['allocated_qty'] = $sql_prod[0]['ALLOCATED_QNTY'];
					$log_data['available_qty'] = $sql_prod[0]['AVAILABLE_QNTY'];
					$log_data['dyed_type'] = $hdn_dyed_type;
					$log_data['insert_date'] = $pc_date_time;
					manage_allocation_transaction_log($log_data);
				}
				//end for transaction log

				oci_commit($con);
				echo "1**" . $rID;
			} else {
				oci_rollback($con);
				echo "10**" . $rID;
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) //  Delete here
	{
		// Delete Here
		$con = connect();
		$txt_qnty = str_replace("'", '', $txt_qnty);
		$txt_old_qnty = str_replace("'", '', $txt_old_qnty);
		$jobNo = str_replace("'", '', $txt_job_no);

		$prod_data = sql_select("select id, product_name_details, color, lot, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_type from product_details_master where id=$txt_item_id_old and item_category_id=1");
		foreach ($prod_data as $row) {
			$yarn_count_id = $row[csf('yarn_count_id')];
			$yarn_comp_type1st = $row[csf('yarn_comp_type1st')];
			$yarn_comp_percent1st = $row[csf('yarn_comp_percent1st')];
			$yarn_type = $row[csf('yarn_type')];
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
		if ($yarn_type != "") {
			$yarn_type_cond = "and b.yarn_type=$yarn_type";
		}

		// and b.job_no=$txt_job_no
		// and b.booking_no=$txt_booking_no
		$jobBookingCon = '';
		if ($jobNo != '') {
			$jobBookingCon = " and b.job_no=" . $txt_job_no . "";
		} else {
			$jobBookingCon = " and ( b.booking_no=" . $txt_booking_no . " || b.fab_booking_no=" . $txt_booking_no . ") ";
		}

		// check if product has any requisition
		if ($db_type == 0) {
			$check_requisition = sql_select("select sum(y.yarn_qnty) yarn_qnty,group_concat(requisition_no) requisition_no from (select b.dtls_id from ppl_planning_entry_plan_dtls b where b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0 group by b.dtls_id)x, ppl_yarn_requisition_entry y where x.dtls_id=y.knit_id and y.prod_id=$txt_item_id_old and y.status_active=1 and y.is_deleted=0");

			$check_ydw = sql_select("select x.wo_num,sum(x.yarn_wo_qty) yarn_wo_qty from(select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.product_id=" . str_replace("'", "", $txt_item_id_old) . $jobBookingCon . "
			union all
			select group_concat(distinct(a.yarn_dyeing_prefix_num)) as wo_num,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125) and b.entry_form in(125) $jobBookingCon $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $yarn_type_cond group by b.job_no,b.product_id)x group by x.wo_num");
		} else {
			$check_requisition = sql_select("select sum(y.yarn_qnty) yarn_qnty, listagg(y.requisition_no, ',') within group (order by y.requisition_no) as requisition_no from (select b.dtls_id from ppl_planning_entry_plan_dtls b where b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0 group by b.dtls_id)x, ppl_yarn_requisition_entry y where x.dtls_id=y.knit_id and y.prod_id=$txt_item_id_old and y.status_active=1 and y.is_deleted=0");


			$check_ydw = sql_select("select x.wo_num, sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.ydw_no, ',') WITHIN GROUP (ORDER BY b.id) as wo_num, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.product_id=" . str_replace("'", "", $txt_item_id_old) . $jobBookingCon . "
			union all
			select LISTAGG(a.ydw_no, ',') WITHIN GROUP (ORDER BY b.id) as wo_num, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125) and b.entry_form in(125) $jobBookingCon $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $yarn_type_cond group by b.job_no,b.product_id)x group by x.wo_num");
		}

		// check if previous product has any YDW
		if (!empty($check_ydw) && ($check_ydw[0][csf('yarn_wo_qty')] != "" || $check_ydw[0][csf('yarn_wo_qty')] > 0)) {
			$yd_data = array();
			foreach ($check_ydw as $yrow) {
				$exp_yd = array();
				$exp_yd = explode(',', $yrow[csf('wo_num')]);
				foreach ($exp_yd as $key => $val) {
					$yd_data[$val] = $val;
				}
			}
			echo "7**Work Order found. Allocated yarn can not be deleted.\nWork Order no = " . implode(', ', $yd_data);
			disconnect($con);
			die;
		}

		if (!empty($check_requisition) && (trim($check_requisition[0][csf('yarn_qnty')]) != "" || trim($check_requisition[0][csf('yarn_qnty')]) > 0)) {
			echo "7**Requisition found. Allocated yarn can not be deleted.\nRequisition no = " . $check_requisition[0][csf('requisition_no')] . "\nRequisition quantity = " . number_format($check_requisition[0][csf('yarn_qnty')], 2, ".", "");
			disconnect($con);
			die;
		}

		//======================iNSERT lOG TABLE==========================
		$prev_datas = explode(",", str_replace("'", '', $qnty_breck_down));
		$job_order = array();
		$data_array_hystory = "";
		foreach ($prev_datas as $prev_data) {
			$po_wise_data = explode("_", $prev_data);
			$job_order[$po_wise_data[2]] .= $po_wise_data[1] . ",";
		}

		if (str_replace("'", '', $txt_order_id) != "") {
			// Grey Yarn
			$field_array_mst_log = "id,entry_form,mst_id,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,status_active,is_deleted,inserted_by,insert_date";
			$field_array1 = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,status_active,is_deleted,inserted_by,insert_date";
			$field_array_dtls_log = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,status_active,is_deleted,inserted_by,insert_date";

			$field_array_hystory = "id,mst_id,dtls_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date,company_id,status_active,is_deleted";

			$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
			$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
			foreach ($prev_datas as $prev_data) {
				$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
				$id_hystory = return_next_id_by_sequence("INV_ALLOCAT_HYSTORY_SEQ", "inv_material_allocat_hystory", $con);

				$po_wise_data = explode("_", $prev_data);
				$qnty = ($po_wise_data[0] != "") ? $po_wise_data[0] : 0;

				if ($data_array1 != "")
					$data_array1 .= ",";

				$data_array1 .= "(" . $id1 . "," . $update_id . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . $qnty . ",0,1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				if ($data_array_dtls_log != "")
					$data_array_dtls_log .= ",";

				$data_array_dtls_log .= "(" . $id_dtls_log . "," . $id_mst_log . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_item_id . "," . -$qnty . ",1,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$id_dtls_log = $id_dtls_log + 1;

				if ($data_array_mst_log == "") {
					$allocatedQnty = str_replace("'", " ", $txt_qnty);
					$data_array_mst_log = "(" . $id_mst_log . ",0," . $update_id . ",'" . $po_wise_data[2] . "','" . $po_wise_data[1] . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . $txt_item_id . "," . -$allocatedQnty . "," . $qnty_breck_down . ",1,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
				$id_dtls_log + 1;
				$po_breakdown_id = $po_wise_data[1];
				//echo $cbo_company_name;die;
				//echo "h*e";die;
				//if ($data_array_hystory != "") $data_array_hystory .= ",";
				$data_array_hystory .= "(" . $id_hystory . "," . str_replace("'", "", $update_id) . "," . $id1 . ",'" . $po_wise_data[2] . "'," . $po_wise_data[1] . "," . $txt_booking_no . "," . str_replace("'", "", $cbo_item_category) . "," . $txt_allocation_date . "," . str_replace("'", "", $txt_item_id) . "," . -$qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . str_replace("'", "", $cbo_company_name) . ",1,0)";
				//echo $data_array_hystory;die;
				//$query="INSERT INTO inv_material_allocat_hystory (".$field_array_hystory.") VALUES ".$data_array_hystory."";
				//echo $query." test";die;

				$history_select = "select id from inv_material_allocat_hystory where item_id=$txt_item_id and booking_no=$txt_booking_no and po_break_down_id='$po_breakdown_id'";
				//echo $history_select;die;
				$history_select = sql_select($history_select);
				//print_r( $history_select);die;
				foreach ($history_select as $key) {
					$history_ids = $key[csf('id')];
					$history_delete_id = sql_delete("inv_material_allocat_hystory", $field_array, $data_array, "id",  $history_ids, 1);
				}
			}
		} else {
			// Dyied Yarn
			$pre_allocated_qnty = return_field_value("qnty", "inv_material_allocation_mst", "id=$update_id");
			//$field_array = "item_id*qnty*updated_by*update_date";
			//$data_array = "" . str_replace("'", '', $txt_item_id) . "*" . $txt_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$qnty = ($po_wise_data[0] != "") ? $po_wise_data[0] : 0;
			$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
			$id_mst_log = return_next_id("id", "inv_mat_allocation_mst_log", 1);
			$id_dtls_log = return_next_id("id", "inv_mat_allocation_dtls_log", 1);
			$field_array1 = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,status_active,is_deleted,inserted_by,insert_date";
			$field_array_dtls_log = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,status_active,is_deleted,inserted_by,insert_date";

			$data_array1 = "(" . $id1 . "," . $update_id . "," . $txt_job_no . ",1," . $txt_allocation_date . "," . str_replace("'", '', $txt_item_id) . "," . $txt_qnty . "," . $hdn_dyed_type . ",0,1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$allocatedQnty = str_replace("'", " ", $txt_qnty);
			$data_array_dtls_log = "(" . $id_dtls_log . "," . $id_mst_log . "," . $txt_job_no . ",1," . $txt_allocation_date . "," . str_replace("'", '', $txt_item_id) . "," . -$allocatedQnty . "," . $hdn_dyed_type . ",1,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$field_array_mst_log = "id,entry_form,mst_id,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,is_dyied_yarn,status_active,is_deleted,inserted_by,insert_date";

			$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);

			$data_array_mst_log = "(" . $id_mst_log . ",0," . $update_id . "," . $txt_job_no . ",'" . $order_nos . "'," . $cbo_item_category . "," . $txt_allocation_date . "," . $txt_booking_no . "," . str_replace("'", '', $txt_item_id) . "," . -$allocatedQnty . ",'" . $qnty_breck_down_order . "'," . $hdn_dyed_type . ",1,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		}

		if ($data_array_hystory != '') {
			$rID_history = sql_insert("inv_material_allocat_hystory", $field_array_hystory, $data_array_hystory, 0);
		}

		//for inv_material_allocation_mst
		$field_array = "status_active*is_deleted*updated_by*update_date";
		$data_array = "'0'*'1'*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'";
		$rID = sql_delete("inv_material_allocation_mst", $field_array, $data_array, "id", "" . $update_id . "", 1);

		$rID2 = sql_delete("inv_material_allocation_dtls", $field_array, $data_array, "mst_id", "" . $update_id . "", 1);
		$rID3 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$txt_old_qnty) where id=$txt_item_id_old ", 1);
		$rID4 = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id_old ", 1);

		$rID_history = true;
		$rID1_dtls_log = true;

		if ($data_array1 != '') {
			$rID_mst_log = sql_insert("inv_mat_allocation_mst_log", $field_array_mst_log, $data_array_mst_log, 0);
			$rID1_dtls_log = sql_insert("inv_mat_allocation_dtls_log", $field_array_dtls_log, $data_array_dtls_log, 0);
		}

		//echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID_mst_log ."&&". $rID1_dtls_log; die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID_mst_log && $rID1_dtls_log && $rID_history) {
				mysql_query("COMMIT");
				echo "2**" . $rID;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $rID;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID_mst_log && $rID1_dtls_log && $rID_history) {
				if (str_replace("'", '', $txt_order_id) != "") {
					$log_ref_id = return_field_value('ID', 'WO_BOOKING_MST', " STATUS_ACTIVE = 1 AND BOOKING_NO = " . $txt_booking_no, 'id');
				} else {
					$log_ref_id = return_field_value('ID', "WO_NON_ORD_SAMP_BOOKING_MST", " STATUS_ACTIVE = 1 AND BOOKING_NO = " . $txt_booking_no);
				}

				$sql_prod = sql_select("SELECT CURRENT_STOCK, ALLOCATED_QNTY, AVAILABLE_QNTY FROM PRODUCT_DETAILS_MASTER WHERE STATUS_ACTIVE = 1 AND ID = " . $txt_item_id_old);
				$log_data['entry_form'] = 0;
				$log_data['ref_id'] = $log_ref_id;
				$log_data['ref_number'] = str_replace("'", '', $txt_booking_no);
				$log_data['product_id'] = $txt_item_id_old;
				$log_data['current_stock'] = $sql_prod[0]['CURRENT_STOCK'];
				$log_data['allocated_qty'] = $sql_prod[0]['ALLOCATED_QNTY'];
				$log_data['available_qty'] = $sql_prod[0]['AVAILABLE_QNTY'];
				$log_data['dyed_type'] = $hdn_dyed_type;
				$log_data['insert_date'] = $pc_date_time;
				manage_allocation_transaction_log($log_data);

				oci_commit($con);
				echo "2**" . $rID;
			} else {
				oci_rollback($con);
				echo "10**" . $rID;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "show_item_active_listview") {
	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer = return_library_array("select id, short_name from  lib_buyer", 'id', 'short_name');
	$supplier = return_library_array("select id, short_name from  lib_supplier", 'id', 'short_name');

	//MF-20-00351_1_MF-Fb-20-00264
	$data = explode("_", $data);
	$job_nos = explode(",", $data[0]);

	$jobs = "";
	foreach ($job_nos as $job) {
		$jobs .= "'" . $job . "',";
	}
	$jobs = rtrim($jobs, ",");

	$item_category = $data[1];
	$booking_no = $data[2];
	$booking_type = $data[3];
	$is_short = $data[4];

	if ($data[0] != "") {
		$job_cond1 = "and b.job_no in($jobs)";
	}

	if ($db_type == 0) {
		$po_sql = sql_select("select distinct a.id po_id,a.po_number,a.grouping,a.file_no,b.id,b.item_id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where b.id=c.mst_id and a.id=c.po_break_down_id $job_cond1 and b.booking_no='$booking_no' and  FIND_IN_SET(a.id, b.po_break_down_id)");
	} else {
		$po_sql = sql_select("select a.id po_id,a.po_number,a.grouping,a.file_no,b.id,b.item_id from wo_po_break_down a, inv_material_allocation_mst b, inv_material_allocation_dtls c where b.id=c.mst_id $job_cond1 and b.booking_no='$booking_no' and a.id=c.po_break_down_id and b.booking_without_order<>1 group by b.id, a.id,a.po_number,a.grouping,a.file_no,b.item_id");
	}

	$po_num_array = $po_data_arr = array();
	foreach ($po_sql as $row) {
		$po_data_arr[$row[csf('po_id')]]['ref'] = $row[csf('grouping')];
		$po_data_arr[$row[csf('po_id')]]['file'] = $row[csf('file_no')];
		$po_data_arr[$row[csf('po_id')]]['po_number'] = $row[csf('po_number')];
		$product_arr[] = $row[csf("item_id")];
	}

	$prod_data_arr = array();
	$prod_data = sql_select("select id, product_name_details, supplier_id, lot,is_within_group from product_details_master where item_category_id=1");
	foreach ($prod_data as $row) {
		$prod_data_arr[$row[csf('id')]]['prod_details'] = $row[csf('product_name_details')];
		$prod_data_arr[$row[csf('id')]]['supp'] = $row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['is_within_group'] = $row[csf('is_within_group')];
	}

	if ($data[0] != "") {
		$sql = "select a.id as sid,a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,a.allocation_date,a.booking_without_order,b.company_name,b.buyer_name,b.location_name from inv_material_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and a.job_no in($jobs) and a.item_category=1 and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (a.is_dyied_yarn!=1 or a.is_dyied_yarn is null)";
	} else {
		$sql = "select a.id as sid,a.id as id,a.po_break_down_id,a.item_id,a.qnty,a.allocation_date,a.booking_without_order,b.buyer_id as buyer_name,b.company_id as company_name from inv_material_allocation_mst a,wo_non_ord_samp_booking_mst b
		where a.booking_no=b.booking_no and a.item_category=1 and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0  and (a.is_dyied_yarn!=1 or a.is_dyied_yarn is null) group by a.id,a.po_break_down_id, a.item_id,a.qnty,a.allocation_date,a.booking_without_order,b.buyer_id,b.company_id";
	}
	//echo $sql;
	$result = sql_select($sql);
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">SID</th>
			<th width="60">Company</th>
			<th width="60">Buyer</th>
			<th width="70">Supplier</th>
			<th width="70">Allocation Date</th>
			<th width="80">Internal Ref</th>
			<th width="70">File No</th>
			<th width="70">Job No</th>
			<th width="120">Order No</th>
			<th width="130">Allocated Yarn</th>
			<th width="70">Lot</th>
			<th>Quantity</th>
		</thead>
	</table>
	<div style="width:970px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="grey_yarn_list">
			<?
			$i = 1;
			$qntyGrndTotal = 0;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				$ref_cond = $file_cond = $po_number = '';

				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $row_data) {
					if ($ref_cond == "") $ref_cond = $po_data_arr[$row_data]['ref'];
					else $ref_cond .= "," . $po_data_arr[$row_data]['ref'];
					if ($file_cond == "") $file_cond = $po_data_arr[$row_data]['file'];
					else $file_cond .= "," . $po_data_arr[$row_data]['file'];

					if ($booking_type == 4) {
						$po_number = "";
					} else if ($po_number == "") $po_number = $po_data_arr[$row_data]['po_number'];
					else $po_number .= "," . $po_data_arr[$row_data]['po_number'];

					if ($prod_data_arr[$row[csf('item_id')]]['is_within_group'] == 1) {

						$supplier_name = $comp[$prod_data_arr[$row[csf('item_id')]]['supp']];
					} else {
						$supplier_name = $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']];
					}
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>_0_<? echo $row[csf('po_break_down_id')]; ?>_<? echo $row[csf('booking_without_order')]; ?>','populate_material_allocation_data','requires/yarn_allocation_controller');">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="50" align="center">
						<p><? echo $row[csf('sid')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $comp[$row[csf('company_name')]]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $buyer[$row[csf('buyer_name')]]; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo $supplier_name; ?></p>
					</td>
					<td width="70" align="center">
						<p><? echo change_date_format($row[csf("allocation_date")], "dd-mm-yyyy", "-"); ?></p>
					</td>
					<td width="80" style="max-width: 80px;">
						<p><? echo trim(implode(",", array_unique(explode(",", $ref_cond))), ", "); ?>&nbsp;</p>
					</td>
					<td width="70" style="max-width: 70px;">
						<p><? echo trim(implode(",", array_unique(explode(",", $file_cond))), ", "); ?>&nbsp;</p>
					</td>
					<td width="70"><? echo $row[csf('job_no')]; ?></td>
					<td width="120" style="max-width: 120px;">
						<p><? echo $po_number ?></p>
					</td>
					<td width="130" style="max-width: 130px;">
						<p><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p>
					</td>
					<td width="70" align="center" title="<? echo $row[csf('item_id')]; ?>" style="word-break: break-all;">
						<? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?>
						<input type="hidden" name="prod_id[]" value="<? echo $row[csf('item_id')]; ?>" />
					</td>
					<td align="right"><? echo number_format($row[csf('qnty')], 2);
										$qntyGrndTotal += $row[csf('qnty')]; ?>&nbsp;</td>
				</tr>
			<?
				$i++;
			}
			?>
			<tr style="background-color:#CCCCCC;">
				<td colspan="12" align="right"><strong>Total</strong></td>
				<td align="right"><strong><? echo number_format($qntyGrndTotal, 2); ?> </strong></td>
			</tr>
		</table>
	</div>
	<fieldset style="width:700px; margin-top:10px; float:left">
		<legend>Dyed Yarn List</legend>
		<div id="item_list_view">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:100%;" rules="all" id="dyied_yarn_list">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="50">SID</th>
						<th width="60">Company</th>
						<th width="60">Buyer</th>
						<th width="70">Supplier</th>
						<th width="150">Allocated Yarn</th>
						<th width="70">Lot</th>
						<th>Quantity</th>
						<th>Issued</th>
					</tr>
				</thead>
				<tbody>
					<?
					if ($data[0] != "") // job no
					{
						$sql_dyied_yarn_sql = "select a.id as sid, a.id as id,a.job_no,a.booking_no,a.po_break_down_id,a.item_id,a.qnty,a.is_dyied_yarn,a.booking_without_order,b.company_name,b.buyer_name, b.location_name,c.available_qnty from inv_material_allocation_mst a, wo_po_details_master b, product_details_master c,wo_booking_mst d where a.job_no=b.job_no and a.job_no in($jobs) and a.booking_no=d.booking_no and d.booking_no='" . $booking_no . "' and d.booking_type='" . $booking_type . "' and a.item_id=c.id and c.item_category_id=1 and a.is_dyied_yarn=1 and a.qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
					} else {
						$sql_dyied_yarn_sql = "select a.id as sid, a.id as id,a.job_no,a.booking_no,a.po_break_down_id,a.item_id,a.qnty,a.is_dyied_yarn,a.booking_without_order,b.buyer_id as buyer_name,b.company_id as company_name from inv_material_allocation_mst a, wo_non_ord_samp_booking_mst b, product_details_master c where a.booking_no=b.booking_no and a.item_id=c.id and a.item_category=1 and c.item_category_id=1 and a.is_dyied_yarn=1 and a.qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_no='" . $booking_no . "' and a.booking_without_order = 1";
					}
					//echo $sql_dyied_yarn_sql;
					$sql_dyied_yarn = sql_select($sql_dyied_yarn_sql);
					foreach ($sql_dyied_yarn as $allocation_row) {
						$prod_id_arr[] = $allocation_row[csf('item_id')];
					}

					$planning_array = array();
					$plan_sql = "select a.booking_no,a.po_id,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and b.status_active=1 and b.prod_id in (" . implode(",", $prod_id_arr) . ") and a.booking_no='" . $booking_no . "' group by a.booking_no,a.po_id,b.requisition_no,b.prod_id"; //and a.status_active=1 : ommit cause program can be delete evern after issue
					$all_requisition = "";
					$planData = sql_select($plan_sql);
					foreach ($planData as $row) {
						$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('booking_no')];
						$all_requisition .= $row[csf('requisition_no')] . ",";
					}
					$all_requisition = trim($all_requisition, ",");
					if ($prod_id_arr != "") {
						$yarn_issue_sql = "SELECT a.id AS issue_id, b.requisition_no, b.prod_id, b.cons_quantity AS issue_quantity, d.job_no_mst, LISTAGG(c.po_breakdown_id, ', ') WITHIN GROUP (ORDER BY c.po_breakdown_id) AS po_breakdown_ids FROM inv_issue_master a JOIN inv_transaction b ON a.id = b.mst_id JOIN order_wise_pro_details c ON b.id = c.trans_id JOIN wo_po_break_down d ON c.po_breakdown_id = d.id WHERE a.issue_purpose = 1 AND d.job_no_mst = '$data[0]' AND b.prod_id in (" . implode(",", $prod_id_arr) . ") AND b.transaction_type = 2 AND b.item_category = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 and b.requisition_no in ($all_requisition) GROUP BY a.id, b.requisition_no, b.prod_id, b.cons_quantity, d.job_no_mst";
						$yarn_issue = sql_select($yarn_issue_sql);
					}

					$issue_arr = array();
					$po_id_array = array();

					foreach ($yarn_issue as $issue) {
						$issue_arr[$booking_no][$issue[csf("job_no_mst")]][$issue[csf("prod_id")]] += $issue[csf("issue_quantity")];
						$issue_id_arr[] = $issue[csf('issue_id')];
					}


					if (!empty($issue_id_arr)) {
						$yarn_issue_return = sql_select("SELECT b.prod_id, b.cons_quantity AS issue_rtn_qty, d.job_no_mst, LISTAGG(c.po_breakdown_id, ',') WITHIN GROUP (ORDER BY c.po_breakdown_id) AS po_breakdown_ids FROM inv_receive_master A INNER JOIN inv_transaction b ON a.ID = b.mst_id INNER JOIN order_wise_pro_details C ON b.ID = c.trans_id INNER JOIN wo_po_break_down d ON c.po_breakdown_id = d.ID WHERE d.job_no_mst = '$data[0]' AND b.prod_id IN (" . implode(",", $prod_id_arr) . ") AND a.issue_id IN (" . implode(",", $issue_id_arr) . ") AND b.transaction_type = 4 AND b.item_category = 1 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.cons_quantity, d.job_no_mst");

						$issue_return_arr = array();
						foreach ($yarn_issue_return as $issue_rtn) {
							$issue_return_arr[$issue_rtn[csf("job_no_mst")]][$issue_rtn[csf("prod_id")]] += $issue_rtn[csf("issue_rtn_qty")];
						}
					}

					//for supplier
					if (implode(",", $prod_id_arr) != "") {
						$sqlRcv = "SELECT a.pay_mode AS PAY_MODE, b.prod_id AS PROD_ID, b.receive_basis AS RECEIVE_BASIS FROM wo_yarn_dyeing_mst a, inv_transaction b WHERE a.id = b.pi_wo_batch_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.transaction_type IN (1) AND b.item_category = 1 AND b.status_active = 1 AND b.is_deleted = 0 AND b.prod_id in (" . implode(",", $prod_id_arr) . ")"; // AND b.booking_no = '".$data[2]."'
						//echo $sqlRcv;
						$sqlRcvRslt = sql_select($sqlRcv);
						$paymodeBasisArr = array();
						foreach ($sqlRcvRslt as $row) {
							$paymodeBasisArr[$row['PROD_ID']]['PAY_MODE'] = $row['PAY_MODE'];
							$paymodeBasisArr[$row['PROD_ID']]['RECEIVE_BASIS'] = $row['RECEIVE_BASIS'];
						}
					}

					$i = 1;
					$issue_qnty = 0;
					$issue_return_qty = 0;
					foreach ($sql_dyied_yarn as $row) {

						$available_qnty = $row[csf('available_qnty')];

						$po_id_arr = explode(",", $row[csf("po_break_down_id")]);
						foreach ($po_id_arr as $po_id) {
							$issue_qnty = $issue_arr[$row[csf("booking_no")]][$row[csf("job_no")]][$row[csf("item_id")]];
							$issue_return_qty = $issue_return_arr[$row[csf("job_no")]][$row[csf("item_id")]];
						}

						//for supplier
						if ($paymodeBasisArr[$row[csf('item_id')]]['RECEIVE_BASIS'] == 2 && ($paymodeBasisArr[$row[csf('item_id')]]['PAY_MODE'] == 3 || $paymodeBasisArr[$row[csf('item_id')]]['PAY_MODE'] == 5)) {
							$supplier_name = $comp[$prod_data_arr[$row[csf('item_id')]]['supp']];
						} else {
							if ($prod_data_arr[$row[csf('item_id')]]['is_within_group'] == 1) {
								$supplier_name = $comp[$prod_data_arr[$row[csf('item_id')]]['supp']];
							} else {
								$supplier_name = $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']];
							}
						}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>_1_<? echo $row[csf('po_break_down_id')]; ?>_<? echo $row[csf('booking_without_order')]; ?>','populate_material_allocation_data','requires/yarn_allocation_controller')">
							<td width="30"><? echo $i; ?></td>
							<td width="50">
								<p><? echo $row[csf('sid')]; ?></p>
							</td>
							<td width="60">
								<p><? echo $comp[$row[csf('company_name')]]; ?></p>
							</td>
							<td width="60">
								<p><? echo $buyer[$row[csf('buyer_name')]]; ?></p>
							</td>
							<td width="70">
								<p><? echo $supplier_name; ?></p>
							</td>
							<td width="150">
								<p><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p>
							</td>
							<td width="70" title="<? echo $row[csf('item_id')]; ?>">
								<? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?>
								<input type="hidden" name="prod_id[]" value="<? echo $row[csf('item_id')]; ?>" />
							</td>
							<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
							<td align="right"><? echo number_format($issue_qnty - $issue_return_qty, 2); ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "populate_material_allocation_data") {
	$data = explode("_", $data);
	$allocation_id = $data[0];
	$is_dyied_yarn = $data[1];
	$po_number = $data[2];
	$booking_without_order = $data[3];

	if ($db_type == 0) {
		if ($po_number != "") {
			$po_sql = sql_select("select distinct a.po_number,b.id from wo_po_break_down a,inv_material_allocation_mst b where a.job_no_mst=b.job_no and b.id='$allocation_id' and  FIND_IN_SET(a.id, b.po_break_down_id)");
		}
	} else {
		if ($po_number != "") {
			$po_sql = sql_select("select a.po_number,b.id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where c.po_break_down_id=a.id and b.id=c.mst_id and b.id='$allocation_id' and a.id=c.po_break_down_id group by b.id, a.po_number");
		}
	}

	$po_num_array = array();
	foreach ($po_sql as $row) {
		if (array_key_exists($row[csf('id')], $po_num_array)) {
			$po_num_array[$row[csf('id')]] = $po_num_array[$row[csf('id')]] . "," . $row[csf('po_number')];
		} else {
			$po_num_array[$row[csf('id')]] = $row[csf('po_number')];
		}
	}

	if ($booking_without_order == 1) {
		$sql = sql_select("select a.id,a.job_no,a.po_break_down_id,a.item_category,a.allocation_date,a.item_id,a.qnty,a.qnty_break_down,a.is_dyied_yarn,b.company_id as company_name,b.buyer_id as buyer_name,c.lot,c.allocated_qnty,c.available_qnty,a.qnty mat_allocation_qnty, a.remarks from inv_material_allocation_mst a,wo_non_ord_samp_booking_mst b, product_details_master c where a.booking_no=b.booking_no and a.item_id=c.id and a.id='$allocation_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
	} else {
		$sql = sql_select("select a.id,a.job_no,a.po_break_down_id,a.item_category,a.allocation_date,a.item_id,a.qnty,a.qnty_break_down,a.is_dyied_yarn,b.company_name,b.buyer_name,b.location_name,c.lot,c.allocated_qnty,c.available_qnty,a.qnty mat_allocation_qnty, a.remarks from inv_material_allocation_mst a,wo_po_details_master b, product_details_master c where a.job_no=b.job_no and a.item_id=c.id and a.id='$allocation_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
	}


	foreach ($sql as $row_data) {
		$available_qnty = (number_format($row_data[csf('qnty')], 2) - number_format($row_data[csf('allocated_qnty')], 2));
		$item_name = sql_select("select product_name_details,available_qnty,unit_of_measure,avg_rate_per_unit from product_details_master where id='" . $row_data[csf('item_id')] . "'");
		list($item_name_row) = $item_name;

		if ($row_data[csf("is_dyied_yarn")] == 1 && $booking_without_order == 1) {
			echo "document.getElementById('txt_order_no').value = '';\n";
			echo "document.getElementById('txt_order_id').value = '';\n";
		} else {
			if ($booking_without_order == 1) {
				echo "document.getElementById('txt_order_no').value = '';\n";
				echo "document.getElementById('txt_order_id').value = '';\n";
			} else {
				echo "document.getElementById('txt_order_no').value = '" . $po_num_array[$row_data[csf("id")]] . "';\n";
				echo "document.getElementById('txt_order_id').value = '" . $row_data[csf("po_break_down_id")] . "';\n";
			}
		}

		$currency_conversion = sql_select("select id, conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and company_id = " . $row_data[csf("company_name")] . "  order by id desc");

		$lib_conversion_rate = $currency_conversion[0][csf("conversion_rate")];
		$ratep = number_format($item_name_row[csf("avg_rate_per_unit")] / $lib_conversion_rate, 4);
		echo "document.getElementById('item_avg_rate_usd').value = '" . $ratep . "';\n";
		echo "document.getElementById('cbo_item_category').value = '" . $row_data[csf("item_category")] . "';\n";
		echo "document.getElementById('txt_allocation_date').value='" . change_date_format($row_data[csf("allocation_date")], "dd-mm-yyyy", "-") . "';\n";
		echo "document.getElementById('txt_item').value = '" . $item_name_row[csf("product_name_details")] . "';\n";
		echo "document.getElementById('txt_remarks').value = '" . $row_data[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_item_id').value = '" . $row_data[csf("item_id")] . "';\n";
		echo "document.getElementById('txt_item_id_old').value = '" . $row_data[csf("item_id")] . "';\n";
		echo "document.getElementById('txt_qnty').value = '" . abs($row_data[csf("mat_allocation_qnty")]) . "';\n";
		echo "document.getElementById('txt_old_qnty').value = '" . $row_data[csf('qnty')] . "';\n";
		echo "document.getElementById('qnty_breck_down').value = '" . $row_data[csf("qnty_break_down")] . "';\n";
		echo "document.getElementById('pre_qnty_breck_down').value = '" . $row_data[csf("qnty_break_down")] . "';\n";
		echo "document.getElementById('available_qnty').value = '" . $item_name_row[csf("available_qnty")] . "';\n";
		echo "document.getElementById('cbo_uom').value = '" . $item_name_row[csf("unit_of_measure")] . "';\n";
		echo "document.getElementById('hdn_dyed_type').value = '" . $data[1] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row_data[csf("id")] . "';\n";
		echo "document.getElementById('txt_fab_booking_qnty').value = '';\n";
		echo "document.getElementById('txt_selectted_fabric').value = '';\n";
		echo "document.getElementById('txt_fabric_po').value = '';\n";

		//if($row_data[csf("po_break_down_id")]=="")
		if ($booking_without_order == 1) {
			echo "$('#txt_qnty').attr('placeholder','write')" . ";\n";
			echo "$('#txt_qnty').attr('readonly', false); " . ";\n";
			echo "$('#caption_job_no font').css('color','black')" . ";\n";
			echo "$('#txt_qnty').prop('onClick', null)\n";
		} else {
			echo "$('#caption_job_no font').css('color','blue')" . ";\n";
			echo "$('#txt_qnty').attr('placeholder','click')" . ";\n";
			echo "$('#txt_qnty').attr('readonly','true')" . ";\n";
			echo "$('#txt_qnty').attr('onClick','open_qnty_popup(\"requires/yarn_allocation_controller.php?action=open_qnty_popup\")')\n";
		}

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_material_allocation_entry',1);\n";
	}
	exit();
}

if ($action == "update_breakdown_column_script") {
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}
	$allocation_arr = array();
	$allocation_mst_sql = "select a.id,a.booking_no,a.job_no, b.po_break_down_id,b.qnty from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and a.item_category=1 and a.is_dyied_yarn=0";
	$result = sql_select($allocation_mst_sql);
	foreach ($result as $row) {
		$allocation_arr[$row[csf("id")]] .= $row[csf("qnty")] . "_" . $row[csf("po_break_down_id")] . "_" . $row[csf("job_no")] . ",";
	}
	foreach ($allocation_arr as $key => $allocation) {
		$allocation = rtrim($allocation, ",");
		$rID = execute_query("update inv_material_allocation_mst set QNTY_BREAK_DOWN='$allocation' where id=$key and item_category=1 and is_dyied_yarn=0", 1);
	}
	if ($rID) {
		oci_commit($con);
		echo "0**" . $rID;
	} else {
		oci_rollback($con);
		echo "10**" . $rID;
	}
	exit();
}
?>