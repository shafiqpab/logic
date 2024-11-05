<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = $_SESSION['logic_erp']['supplier_id'];
$user_comp_location_ids = $_SESSION['logic_erp']['company_location_id'];
//--------------------------------------------------------------------------------------------

if ($action == "upto_variable_settings") {
	$sql =  sql_select("select store_method from variable_settings_inventory where company_name = $data and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$return_data = "";
	if (count($sql) > 0) {
		$return_data = $sql[0][csf('store_method')];
	} else {
		$return_data = 0;
	}

	echo $return_data;
	die;
}

if ($action == "load_drop_down_location") {
	if ($user_comp_location_ids) $user_comp_location_cond = " and id in ($user_comp_location_ids)";
	else $user_comp_location_cond = "";
	echo create_drop_down("cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data $user_comp_location_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "load_room_rack_self_bin('requires/yarn_issue_return_controller*1', 'store','store_td', $('#cbo_company_id').val(), this.value);storeUpdateUptoDisable();");
	exit();
}

if ($action == "load_drop_down_basis") {
	$issue_basis_requisition_or_demand_variable = return_field_value("yarn_issue_basis", "variable_settings_inventory", "company_name=$data and variable_list=28");
	if ($issue_basis_requisition_or_demand_variable == 2) {
		$issue_basis = array(1 => "Booking", 2 => "Independent", 8 => "Demand", 4 => "Sales Order");
	} else {
		$issue_basis = array(1 => "Booking", 2 => "Independent", 3 => "Requisition", 4 => "Sales Order");
	}
	echo create_drop_down("cbo_basis", 170, $issue_basis, "", 1, "-- Select Basis --", $selected, "active_inactive(this.value);", "", "");
	exit();
}

if ($action == "load_room_rack_self_bin") {
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data = implode('*', $explodeData);
	load_room_rack_self_bin("requires/yarn_issue_return_controller", $data);
}

if ($action == "load_drop_down_knit_com") {
	$exDataArr = explode("**", $data);
	$knit_source = $exDataArr[0];
	$company = $exDataArr[1];
	if ($company == "" || $company == 0) $company_cod = "";
	else $company_cod = " and id=$company";
	if ($knit_source == 1) {
		echo create_drop_down("cbo_knitting_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select --", $company, "");
	} else if ($knit_source == 3) {
		echo create_drop_down("cbo_knitting_company", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", $company, "", 0);
	} else
		echo create_drop_down("cbo_knitting_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	exit();
}

if ($action == "fabbook_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function fn_check() {

			if (document.getElementById('txt_search_common').value == "") {
				if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false) {
					return;
				}
			}


			show_list_view(document.getElementById('cbo_buyer_name').value + '_' + document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_<? echo $company; ?>_' + <? echo $receive_basis; ?> + '_' + document.getElementById('cbo_search_category').value, 'create_fabbook_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'list_view\',-1)');
		}

		function js_set_value(booking_dtls) {
			$("#hidden_booking_number").val(booking_dtls);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="870" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th colspan="5" align="center"><? echo create_drop_down("cbo_search_category", 130, $string_search_type, '', 1, "-- Search Catagory --"); ?></th>
						</tr>
						<tr>
							<th width="160">Buyer Name </th>
							<th width="150">Search By</th>
							<th width="220" align="center" id="search_by_td_up">Enter WO/Reqsn. No/Demand No/FSO No</th>
							<th width="200" class="must_entry_caption">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td>
								<?
								echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", "");
								?>
							</td>
							<td>
								<?
								if ($receive_basis == 1) {
									$search_by = array(1 => 'Booking No', 2 => 'Buyer Order', 3 => 'Job No', 4 => "Internal Ref", 5 => "File No", 6 => "Issue No");
									$selected = 1;
								} else if ($receive_basis == 4) {
									$search_by = array(1 => 'Booking No', 2 => 'Sales Order No', 3 => "Style Ref. No", 4 => "Issue No");
									$selected = 2;
								} else {
									$search_by = array(1 => 'Booking No', 2 => 'Requisition No', 3 => 'Program No', 4 => "Internal Ref", 5 => "File No", 6 => "Issue No", 8 => "Demand No");
									$selected = 2;
								}

								$dd = "change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../')";
								echo create_drop_down("cbo_search_by", 130, $search_by, "", 0, "--Select--", $selected, $dd, 0, '');
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check()" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here -->
								<input type="hidden" id="hidden_booking_id" value="" />
								<input type="hidden" id="hidden_booking_number" value="" />
								<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
								<!-- -END -->
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
}

if ($action == "create_fabbook_search_list_view") {
	$ex_data = explode("_", $data);
	$buyer 				= $ex_data[0];
	$txt_search_by 		= $ex_data[1];
	$txt_search_common 	= trim($ex_data[2]);
	$txt_date_from 		= $ex_data[3];
	$txt_date_to 		= $ex_data[4];
	$company 			= $ex_data[5];
	$receive_basis		= $ex_data[6];
	$search_category	= $ex_data[7];

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');

	if ($receive_basis == 1) // Booking
	{
		$sql_cond = "";
		$sql_cond_yarn_dyeing = '';

		if (trim($txt_search_common) != "") {
			if (trim($txt_search_by) == 1) // for Booking No
			{
				if ($search_category == 1) {
					$sql_cond .= " and a.booking_no_prefix_num LIKE '$txt_search_common'";
					$search_field_cond_sample = "and s.booking_no_prefix_num LIKE '$txt_search_common'";
					$sql_cond_yarn_dyeing .= " and w.ydw_no LIKE '$txt_search_common'";
				} else if ($search_category == 2) {
					if ($db_type == 2) {
						$sql_cond .= " and regexp_like (a.booking_no_prefix_num,'^" . trim($txt_search_common) . "')";
						$search_field_cond_sample = "and regexp_like (s.booking_no_prefix_num, '^" . trim($txt_search_common) . "')";
						$sql_cond_yarn_dyeing .= " and regexp_like (w.ydw_no , '^" . trim($txt_search_common) . "')";
					} else {
						$sql_cond .= " and a.booking_no_prefix_num LIKE '$txt_search_common%'";
						$search_field_cond_sample = "and s.booking_no_prefix_num LIKE '$txt_search_common%'";
						$sql_cond_yarn_dyeing .= " and w.ydw_no LIKE '$txt_search_common%'";
					}
				} else if ($search_category == 3) {
					$sql_cond .= " and a.booking_no_prefix_num LIKE '%$txt_search_common'";
					$search_field_cond_sample = "and s.booking_no_prefix_num LIKE '%$txt_search_common'";
					$sql_cond_yarn_dyeing .= " and w.ydw_no LIKE '%$txt_search_common'";
				} else if ($search_category == 4  || $search_category == 0) {
					$sql_cond .= " and a.booking_no_prefix_num LIKE '%$txt_search_common%'";
					$search_field_cond_sample = "and s.booking_no_prefix_num LIKE '%$txt_search_common%'";
					$sql_cond_yarn_dyeing .= " and w.ydw_no LIKE '%$txt_search_common%'";
				}
			} else if (trim($txt_search_by) == 2) // for buyer order
			{
				if ($search_category == 1) {
					$sql_cond .= " and b.po_number LIKE '$txt_search_common'";
				} else if ($search_category == 2) {

					if ($db_type == 2) {
						$sql_cond .= " and regexp_like (b.po_number,'^" . trim($txt_search_common) . "')";
					} else {
						$sql_cond .= " and b.po_number LIKE '$txt_search_common%'";
					}
				} else if ($search_category == 3) {
					$sql_cond .= " and b.po_number LIKE '%$txt_search_common'";
				} else if ($search_category == 4 || $search_category == 0) {
					$sql_cond .= " and b.po_number LIKE '%$txt_search_common%'";
				}

				$search_field_cond_sample = "";
			} else if (trim($txt_search_by) == 3) // for job no
			{
				if ($search_category == 1) {
					$sql_cond .= " and a.job_no LIKE '$txt_search_common'";
					$sql_cond_yarn_dyeing .= " and y.job_no LIKE '$txt_search_common'";
				} else if ($search_category == 2) {
					$sql_cond .= " and a.job_no LIKE '$txt_search_common%'";
					$sql_cond_yarn_dyeing .= " and y.job_no LIKE '$txt_search_common%'";
				} else if ($search_category == 3) {
					if ($db_type == 2) {
						$sql_cond .= " and regexp_like (a.job_no,'^" . trim($txt_search_common) . "')";
						$sql_cond_yarn_dyeing .= " and regexp_like (y.job_no,'^" . trim($txt_search_common) . "')";
					} else {
						$sql_cond .= " and a.job_no LIKE '%$txt_search_common'";
						$sql_cond_yarn_dyeing .= " and y.job_no LIKE '%$txt_search_common'";
					}
				} else if ($search_category == 4 || $search_category == 0) {
					$sql_cond .= " and a.job_no LIKE '%$txt_search_common%'";
					$sql_cond_yarn_dyeing .= " and y.job_no LIKE '%$txt_search_common%'";
				}
				$search_field_cond_sample = "";
			} else if (trim($txt_search_by) == 4) // for Internal ref
			{
				if ($search_category == 1) {
					$sql_cond .= " and b.grouping LIKE '$txt_search_common'";
				} else if ($search_category == 2) {
					if ($db_type == 2) {
						$sql_cond .= " and regexp_like (b.grouping ,'^" . trim($txt_search_common) . "')";
					} else {
						$sql_cond .= " and b.grouping LIKE '$txt_search_common%'";
					}
				} else if ($search_category == 3) {
					$sql_cond .= " and b.grouping LIKE '%$txt_search_common'";
				} else if ($search_category == 4 || $search_category == 0) {
					$sql_cond .= " and b.grouping LIKE '%$txt_search_common%'";
				}
				$search_field_cond_sample = "";
			} else if (trim($txt_search_by) == 5) // for file No
			{
				if ($search_category == 1) {
					$sql_cond .= " and b.file_no LIKE '$txt_search_common'";
				} else if ($search_category == 2) {
					if ($db_type == 2) {
						$sql_cond .= " and regexp_like (b.file_no,'^" . trim($txt_search_common) . "')";
					} else {
						$sql_cond .= " and b.file_no LIKE '$txt_search_common%'";
					}
				} else if ($search_category == 3) {
					$sql_cond .= " and b.file_no LIKE '%$txt_search_common'";
				} else if ($search_category == 4 || $search_category == 0) {
					$sql_cond .= " and b.file_no LIKE '%$txt_search_common%'";
				}
				$search_field_cond_sample = "";
			} else if (trim($txt_search_by) == 6) // Issue No
			{
				if ($search_category == 1) {
					$sql_cond .= " and c.issue_number LIKE '$txt_search_common'";
					$search_field_cond_sample .= " and c.issue_number LIKE '$txt_search_common'";
					$sql_cond_yarn_dyeing .= " and c.issue_number LIKE '$txt_search_common'";
				} else if ($search_category == 2) {
					if ($db_type == 2) {
						$sql_cond .= " and regexp_like (c.issue_number,'^" . trim($txt_search_common) . "')";
						$search_field_cond_sample .= " and regexp_like (c.issue_number,'^" . trim($txt_search_common) . "')";
						$sql_cond_yarn_dyeing .= " and regexp_like (c.issue_number,'^" . trim($txt_search_common) . "')";
					} else {
						$sql_cond .= " and c.issue_number LIKE '$txt_search_common%'";
						$search_field_cond_sample .= " and c.issue_number LIKE '$txt_search_common%'";
						$sql_cond_yarn_dyeing .= " and c.issue_number LIKE '$txt_search_common%'";
					}
				} else if ($search_category == 3) {
					$sql_cond .= " and c.issue_number LIKE '%$txt_search_common'";
					$search_field_cond_sample .= " and c.issue_number LIKE '%$txt_search_common'";
					$sql_cond_yarn_dyeing .= " and c.issue_number LIKE '%$txt_search_common'";
				} else if ($search_category == 4 || $search_category == 0) {
					$sql_cond .= " and c.issue_number LIKE '%$txt_search_common%'";
					$search_field_cond_sample .= " and c.issue_number LIKE '%$txt_search_common%'";
					$sql_cond_yarn_dyeing .= " and c.issue_number LIKE '%$txt_search_common%'";
				}
			}
		}

		if ($txt_date_from != "" && $txt_date_to != "") {
			if ($db_type == 0) {
				$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				$search_field_cond_sample .= "and s.booking_date between '" . change_date_format($txt_date_from, "yyyy-mm-dd", "-") . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				$sql_cond_yarn_dyeing .= " and w.booking_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				$search_field_cond_sample .= "and s.booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				$sql_cond_yarn_dyeing .= " and w.booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
			}
		}

		if (trim($buyer) != 0) $sql_cond .= " and a.buyer_id='$buyer'";
		if (trim($company) != 0) $sql_cond .= " and a.company_id='$company'";

		if (trim($buyer) != 0) $search_field_cond_sample .= " and s.buyer_id='$buyer'";
		if (trim($company) != 0) $search_field_cond_sample .= " and s.company_id='$company'";

		$job_arr = array();
		$po_arr = array();
		$job_data = sql_select("select a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.file_no, b.grouping, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");

		foreach ($job_data as $row) {
			$job_arr[$row[csf('job_id')]] .= $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('style_ref_no')] . "**" . $row[csf('buyer_name')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')] . ",";
			$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
		}

		$select_job_field = '';

		if ($txt_search_common != "" && ($txt_search_by == 2 || $txt_search_by == 3)) {
			if ($db_type == 0) {
				$sql_yarn_dyeing = "select w.id, w.yarn_dyeing_prefix_num as booking_no_prefix_num, w.ydw_no as booking_no, w.booking_date, 0 as buyer_id, w.item_category_id as item_category, w.delivery_date, null as po_break_down_id, group_concat(y.job_no) as job_no_mst, w.entry_form, group_concat(y.job_no_id) as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_yarn_dyeing_mst w, wo_yarn_dyeing_dtls y, inv_issue_master c
				where
				w.id=y.mst_id and
				w.id=c.booking_id and
				c.issue_basis=1 and c.issue_purpose=2 and
				c.item_category=1 and
				w.status_active=1 and
				w.is_deleted=0 and
				y.status_active=1 and
				y.is_deleted=0
				and c.status_active=1
				and c.is_deleted=0
				and w.company_id=$company
				$sql_cond_yarn_dyeing
				group by w.id, w.yarn_dyeing_prefix_num, w.ydw_no, w.booking_date, w.item_category_id, w.delivery_date, w.entry_form, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company";

				$select_job_field = 'a.job_no';
			} else {
				$sql_yarn_dyeing = "select w.id, w.yarn_dyeing_prefix_num as booking_no_prefix_num, w.ydw_no as booking_no, w.booking_date, 0 as buyer_id, w.item_category_id as item_category, w.delivery_date, null as po_break_down_id, LISTAGG(y.job_no, ',') WITHIN GROUP (ORDER BY y.job_no_id) as job_no_mst, w.entry_form, LISTAGG(y.job_no_id, ',') WITHIN GROUP (ORDER BY y.job_no_id) as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_yarn_dyeing_mst w, wo_yarn_dyeing_dtls y, inv_issue_master c
				where
				w.id=y.mst_id and
				w.id=c.booking_id and
				c.issue_basis=1 and c.issue_purpose=2 and
				c.item_category=1 and
				w.status_active=1 and
				w.is_deleted=0 and
				y.status_active=1 and
				y.is_deleted=0
				and c.status_active=1
				and c.is_deleted=0
				and w.company_id=$company
				$sql_cond_yarn_dyeing
				group by w.id, w.yarn_dyeing_prefix_num, w.ydw_no, w.booking_date, w.item_category_id, w.delivery_date, w.entry_form, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company";

				$select_job_field = 'cast(a.job_no as varchar(4000))';
			}

			$sql = "select a.id, a.booking_no_prefix_num, a.booking_no,(b.fab_booking_no || b.booking_no) as fab_booking_no, a.booking_date, a.buyer_id, a.item_category, a.delivery_date, a.po_break_down_id, $select_job_field as job_no_mst, 0 as entry_form, null as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
			from wo_booking_mst a, wo_po_break_down b , inv_issue_master c
			where a.job_no=b.job_no_mst and a.id=c.booking_id and c.issue_basis=1 and c.issue_purpose<>2 and a.item_category=2 and c.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond
			group by a.id, a.booking_no_prefix_num, a.booking_no, b.fab_booking_no, b.booking_no, a.booking_date, a.buyer_id, a.po_break_down_id, a.item_category, a.delivery_date, a.job_no, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company
			union all " . $sql_yarn_dyeing;
			//echo $sql; die;
		} else {
			if ($db_type == 0) {
				$sql = "select a.id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.buyer_id,a.item_category,a.delivery_date,a.po_break_down_id,a.job_no as job_no_mst,0 as entry_form, null as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_booking_mst a, wo_po_break_down b, inv_issue_master c
				where
				a.job_no=b.job_no_mst and
				a.id=c.booking_id and
				c.issue_basis=1 and c.issue_purpose<>2 and
				a.item_category=2 and
				c.item_category=1 and
				a.status_active=1 and
				a.is_deleted=0 and
				b.status_active=1 and
				b.is_deleted=0 and
				c.status_active=1 and
				c.is_deleted=0
				$sql_cond
				group by a.id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.buyer_id,a.po_break_down_id,a.item_category,a.delivery_date,a.job_no, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				union all
				select s.id, s.booking_no_prefix_num as booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, s.item_category, s.delivery_date, null as po_break_down_id, null as job_no_mst, 1 as entry_form, null as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_non_ord_samp_booking_mst s, inv_issue_master c
				where
				s.item_category=2 and
				s.id=c.booking_id and
				c.issue_basis=1 and c.issue_purpose=8 and
				c.item_category=1 and
				s.status_active=1 and
				s.is_deleted=0 and
				c.status_active=1 and
				c.is_deleted=0
				$search_field_cond_sample
				group by s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, s.item_category, s.delivery_date, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				union all
				select w.id, w.yarn_dyeing_prefix_num as booking_no_prefix_num, w.ydw_no as booking_no, w.booking_date, 0 as buyer_id, w.item_category_id as item_category, w.delivery_date, null as po_break_down_id, group_concat(y.job_no) as job_no_mst, w.entry_form, group_concat(y.job_no_id) as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_yarn_dyeing_mst w, wo_yarn_dyeing_dtls y, inv_issue_master c
				where
				w.id=y.mst_id and
				w.id=c.booking_id and
				c.issue_basis=1 and c.issue_purpose=2 and
				c.item_category=1 and
				w.status_active=1 and
				w.is_deleted=0 and
				y.status_active=1 and
				y.is_deleted=0 and
				c.status_active=1 and
				c.is_deleted=0
				and w.company_id=$company
				$sql_cond_yarn_dyeing
				group by w.id, w.yarn_dyeing_prefix_num, w.ydw_no, w.booking_date, w.item_category_id, w.delivery_date, w.entry_form, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company";
			} else {
				$sql = "select * from (select a.id,a.booking_no_prefix_num,a.booking_no,a.booking_no as fab_booking_no,a.booking_date,a.buyer_id,a.item_category,a.delivery_date,a.po_break_down_id,cast(a.job_no as varchar(4000)) as job_no_mst,0 as entry_form, null as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_booking_mst a,wo_booking_dtls d, wo_po_break_down b, inv_issue_master c
				where
				a.booking_no=d.booking_no and d.po_break_down_id=b.id and a.id=c.booking_id and
				c.issue_basis=1 and c.issue_purpose<>2 and
				a.item_category=2 and
				c.item_category=1 and
				a.status_active=1 and
				a.is_deleted=0 and
				b.status_active=1 and
				b.is_deleted=0 and
				c.status_active=1 and
				c.is_deleted=0

				$sql_cond
				group by a.id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.buyer_id,a.po_break_down_id,a.item_category,a.delivery_date,a.job_no, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				union all
				select s.id, s.booking_no_prefix_num as booking_no_prefix_num, s.booking_no, s.booking_no as fab_booking_no, s.booking_date, s.buyer_id, s.item_category, s.delivery_date, null as po_break_down_id, null as job_no_mst, 1 as entry_form, null as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_non_ord_samp_booking_mst s, inv_issue_master c
				where
				s.item_category=2 and
				s.id=c.booking_id and
				c.issue_basis=1 and c.issue_purpose=8 and
				c.item_category=1 and
				s.status_active=1 and
				s.is_deleted=0
				$search_field_cond_sample
				group by s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, s.item_category, s.delivery_date, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				union all
				select w.id, w.yarn_dyeing_prefix_num as booking_no_prefix_num, w.ydw_no as booking_no, (y.fab_booking_no || y.booking_no) as fab_booking_no, w.booking_date, 0 as buyer_id, w.item_category_id as item_category, w.delivery_date, null as po_break_down_id, LISTAGG(y.job_no, ',') WITHIN GROUP (ORDER BY y.job_no_id) as job_no_mst, w.entry_form, LISTAGG(y.job_no_id, ',') WITHIN GROUP (ORDER BY y.job_no_id) as job_no_id, c.id as issue_id, c.issue_number, c.knit_dye_source, c.knit_dye_company
				from wo_yarn_dyeing_mst w, wo_yarn_dyeing_dtls y, inv_issue_master c
				where
				w.id=y.mst_id and
				w.id=c.booking_id and
				c.issue_basis=1 and
				c.item_category=1 and
				w.status_active=1 and
				w.is_deleted=0 and
				y.status_active=1 and
				y.is_deleted=0
				and w.company_id=$company
				$sql_cond_yarn_dyeing
				group by w.id, w.yarn_dyeing_prefix_num, w.ydw_no, y.fab_booking_no,
				y.booking_no, w.booking_date, w.item_category_id, w.delivery_date, w.entry_form, c.id, c.issue_number, c.knit_dye_source, c.knit_dye_company) order by issue_number desc";

				//echo $sql;
			}
		}

		//echo $sql;

		$result = sql_select($sql);
		$all_issue_id = array();
		foreach ($result as $row) {
			$all_issue_id[$row[csf("issue_id")]] = $row[csf("issue_id")];
		}

		if (!empty($all_issue_id)) {
			$all_issue_id = array_chunk($all_issue_id, 999);
			$issue_cond = " and(";
			foreach ($all_issue_id as $issue_id) {
				if ($issue_cond == " and(") $issue_cond .= " a.mst_id in(" . implode(',', $issue_id) . ")";
				else $issue_cond .= "  or a.mst_id in(" . implode(',', $issue_id) . ")";
			}
			$issue_cond .= ")";
		}

		$issue_lot = "select a.mst_id, b.lot, a.cons_quantity,a.return_qnty from inv_transaction a, product_details_master b where a.prod_id=b.id and a.transaction_type=2 and a.item_category=1 and b.item_category_id=1 and a.status_active=1 $issue_cond";
		//echo $issue_lot;//die;
		$issue_lot_data = array();
		$issue_qty_arr = array();
		$issue_lot_result = sql_select($issue_lot);
		foreach ($issue_lot_result as $row) {
			$issue_lot_data[$row[csf("mst_id")]] .= $row[csf("lot")] . ",";
			$issue_qty_arr[$row[csf("mst_id")]] += $row[csf("cons_quantity")];
			$issue_return_able_qty_arr[$row[csf("mst_id")]] += $row[csf("return_qnty")];
		}
	?>
		<div align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1170" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="60">Issue No</th>
					<th width="60">Issue Qty.</th>
					<th width="80">Knit. Comp.</th>
					<th width="105">Booking No</th>
					<th width="65">Type</th>
					<th width="70">Booking Date</th>
					<th width="60">Buyer</th>
					<th width="90">Item Category</th>
					<th width="80">Job No</th>
					<th width="80">Order Qnty</th>
					<th width="80">Ship. Date</th>
					<th width="100">Order No</th>
					<th width="80">Internal Ref</th>
					<th width="50">File No</th>
					<th>Lot No</th>
				</thead>
			</table>
			<div style="width:1190px; max-height:240px; overflow-y:scroll" id="list_container_batch">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1170" class="rpt_table" id="list_view">
					<?
					$i = 1;
					foreach ($result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";

						$po_qnty_in_pcs = 0;
						$po_no = '';
						$min_shipment_date = '';
						$buyer_id = '';
						$fileNo = "";
						$internal_ref = "";
						//echo $row[csf('entry_form')].test;
						if ($row[csf('entry_form')] == 0) {
							$po_id = explode(",", $row[csf('po_break_down_id')]);
							foreach ($po_id as $id) {
								$po_data = explode("**", $po_arr[$id]);
								$po_number = $po_data[0];
								$pub_shipment_date = $po_data[1];
								$po_qnty = $po_data[2];
								$poQntyPcs = $po_data[3];
								$grouping = $po_data[4];
								$file_no = $po_data[5];

								if ($po_no == "") $po_no = $po_number;
								else $po_no .= "," . $po_number;
								if ($internal_ref == "") $internal_ref = $grouping;
								else $internal_ref .= "," . $grouping;
								if ($fileNo == "") $fileNo = $file_no;
								else $fileNo .= "," . $file_no;

								if ($min_shipment_date == '') {
									$min_shipment_date = $pub_shipment_date;
								} else {
									if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date;
									else $min_shipment_date = $min_shipment_date;
								}

								$po_qnty_in_pcs += $poQntyPcs;
							}
							$buyer_id = $row[csf('buyer_id')];
						} else if ($row[csf('entry_form')] == 41) {
							$job_no_mst = array_unique(explode(",", $row[csf('job_no_id')]));
							foreach ($job_no_mst as $job_id) {
								$job_data = explode(",", substr($job_arr[$job_id], 0, -1));
								foreach ($job_data as $value) {
									$po_data = explode("**", $value);
									$po_number = $po_data[0];
									$pub_shipment_date = $po_data[1];
									$po_qnty = $po_data[2];
									$poQntyPcs = $po_data[3];
									$style_ref_no = $po_data[4];
									$buyer_id = $po_data[5];
									$grouping = $po_data[6];
									$file_no = $po_data[7];

									if ($po_no == "") {
										$po_no = $po_number;
									} else {
										$po_no .= "," . $po_number;
									}

									if ($internal_ref == "") $internal_ref = $grouping;
									else $internal_ref .= "," . $grouping;
									if ($fileNo == "") $fileNo = $file_no;
									else $fileNo .= "," . $file_no;

									if ($min_shipment_date == '') {
										$min_shipment_date = $pub_shipment_date;
									} else {
										if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date;
										else $min_shipment_date = $min_shipment_date;
									}

									$po_qnty_in_pcs += $poQntyPcs;
								}
							}

							$type = 2;
						} else if ($row[csf('entry_form')] == 42 || $row[csf('entry_form')] == 114) {
							$type = 3;
						} else if ($row[csf('entry_form')] == 94) {
							if ($row[csf('job_no_mst')] == "") {
								$type = 3;
							} else {
								$type = 2;
							}
						} else {
							$buyer_id = $row[csf('buyer_id')];

							if ($row[csf('job_no_mst')] == "") {
								$type = 3;
							} else {
								$type = 2;
							}
						}



						$kniting_comp = "";
						if ($row[csf('knit_dye_source')] == 1) $kniting_comp = $company_arr[$row[csf('knit_dye_company')]];
						else $kniting_comp = $supplier_arr[$row[csf('knit_dye_company')]];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $receive_basis; ?>_<? echo $row[csf('id')]; ?>_<? echo $row[csf('booking_no')]; ?>_<? echo $type; ?>_<? echo $row[csf('issue_id')]; ?>_<? echo $row[csf('knit_dye_source')]; ?>_<? echo $row[csf('knit_dye_company')]; ?>_1_<? echo $row[csf('job_no_mst')]; ?>_<? echo $buyer_arr[$buyer_id]; ?>_<? echo $row[csf('entry_form')] ?>***<? echo $row[csf('fab_booking_no')]; ?>');">
							<td width="30"><? echo $i; ?></td>
							<td width="60">
								<p><? echo $row[csf('issue_number')]; ?></p>
							</td>
							<td width="60" align="right">
								<p><? echo number_format($issue_qty_arr[$row[csf("issue_id")]], 2); ?></p>
							</td>

							<td width="80">
								<p><? echo $kniting_comp; ?></p>
							</td>
							<td width="105">
								<p><? echo $row[csf('booking_no')]; ?></p>
							</td>
							<td width="65">
								<p><? if ($type == 0 || $type == 2) echo "With Order";
									else echo "Without Order"; ?></p>
							</td>
							<td width="70" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
							<td width="60">
								<p><? echo $buyer_arr[$buyer_id]; ?></p>
							</td>
							<td width="90">
								<p><? echo $item_category[$row[csf('item_category')]]; ?></p>
							</td>
							<td width="80">
								<p><? if ($type == 0 || $type == 2) echo implode(",", array_unique(explode(",", $row[csf('job_no_mst')])));
									else echo "&nbsp;"; ?></p>
							</td>
							<td width="80" align="right"><? if ($type == 0 || $type == 2) echo $po_qnty_in_pcs;
															else echo "&nbsp;"; ?></td>
							<td width="80" align="center"><? if ($type == 0 || $type == 2) echo change_date_format($min_shipment_date); ?>&nbsp;</td>
							<td width="100">
								<p><? if ($type == 0 || $type == 2) echo $po_no;
									else echo "&nbsp;"; ?></p>
							</td>
							<td width="80">
								<p><? if ($type == 0 || $type == 2) echo implode(",", array_unique(explode(",", $internal_ref)));
									else echo "&nbsp;"; ?>&nbsp;</p>
							</td>
							<td width="50">
								<p><? if ($type == 0 || $type == 2) echo implode(",", array_unique(explode(",", $fileNo)));
									else echo "&nbsp;"; ?>&nbsp;</p>
							</td>
							<td>
								<p><? $issue_lot = implode(",", array_unique(explode(",", chop($issue_lot_data[$row[csf("issue_id")]], ','))));
									echo $issue_lot; ?></p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
	<?
	} else if ($receive_basis == 4) // Sales order
	{
	?>
		<div align="center">
			<div style="width:1060px;">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="1050" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="80">Issue No</th>
							<th width="60">Issue Qty.</th>
							<th width="130">Buyer</th>
							<th width="130">Booking No</th>
							<th width="130">Order No</th>
							<th width="100">Within Group</th>
							<th width="90">Style Ref. No</th>
							<th width="100">Delivery Date</th>
							<th>Lot No.</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:1060px; overflow-y:scroll; max-height:340px;">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="1035" rules="all" id="list_view">
					<tbody>
						<?
						$search_cond = "";
						if ($txt_date_from != "" && $txt_date_to != "") {
							if ($db_type == 0) {
								$search_cond .= " and e.delivery_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
							} else {
								$search_cond .= " and e.delivery_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
							}
						}
						if ($txt_search_common != "") {
							if ($txt_search_by == 1) {
								$search_cond .= " and e.sales_booking_no like '%$txt_search_common'";
							} else if ($txt_search_by == 2) {
								$search_cond .= " and e.job_no_prefix_num=$txt_search_common";
							} else if (trim($txt_search_by) == 4) {
								$search_cond .= " and a.issue_number_prefix_num=$txt_search_common";
							} else {
								$search_cond = " and e.style_ref_no like '%$txt_search_common%'";
							}
						}

						$sql = "select a.id issue_id,a.issue_number_prefix_num,listagg(b.prod_id, ',') within group (order by b.prod_id) as prod_id,sum(b.cons_quantity) issue_qnty,e.id,e.job_no,e.sales_booking_no,e.within_group,e.style_ref_no,e.buyer_id,e.delivery_date from inv_issue_master a,inv_transaction b,fabric_sales_order_mst e where a.id=b.mst_id and a.buyer_job_no=e.job_no and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_basis=4 and e.status_active=1 and e.is_deleted=0  $search_cond and a.company_id=$company group by a.id,a.issue_number_prefix_num,e.id,e.job_no,e.sales_booking_no,e.within_group,e.style_ref_no,e.buyer_id,e.delivery_date order by a.issue_number_prefix_num desc
						";
						$result = sql_select($sql);
						if (!empty($result)) {
							$i = 1;
							foreach ($result as $row) {
								if ($row[csf('within_group')] == 1) $buyer_name = $company_arr[$row[csf('buyer_id')]];
								else if ($row[csf('within_group')] == 2) $buyer_name = $buyer_arr[$row[csf('buyer_id')]];
						?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $receive_basis; ?>_<? echo $row[csf('id')]; ?>_<? echo $row[csf('job_no')]; ?>_<? echo $row[csf('issue_id')]; ?>_<? echo $buyer_name; ?>***<? echo $row[csf('sales_booking_no')]; ?>')" style="cursor:pointer">
									<td width="50" align="center"><?php echo $i; ?></td>
									<td width="80" align="center"><?php echo $row[csf("issue_number_prefix_num")]; ?></td>
									<td width="60" align="right"><?php echo number_format($row[csf("issue_qnty")], 2); ?></td>
									<td width="130" align="center"><?php echo $buyer_name; ?></td>
									<td width="130" align="center"><?php echo $row[csf("sales_booking_no")]; ?></td>
									<td width="130" align="center"><?php echo $row[csf('job_no')]; ?></td>
									<td width="100" align="center"><?php echo ($row[csf('within_group')] == 1) ? "Yes" : "No"; ?></td>
									<td width="90"><? echo $row[csf('style_ref_no')]; ?></td>
									<td align="center" width="100"><?php echo change_date_format($row[csf("delivery_date")]); ?></td>
									<td align="center">
										<?
										$issue_lot = implode(",", array_unique(explode(",", chop($row[csf("prod_id")], ','))));
										echo $issue_lot;
										?>
									</td>
								</tr>
							<?
								$i++;
							}
						} else {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td colspan="10" align="center"><strong>No Data Found</strong></td>
							</tr>
						<?
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	<?
	} else {
		if ($txt_date_from != "" && $txt_date_to != "") {
			if ($db_type == 0) {
				$sql_cond = " and c.requisition_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond = " and c.requisition_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
			}
		}

		if ($buyer == 0) $buyer_cond = "%%";
		else $buyer_cond = $buyer;

		if ($txt_search_by == 1)
			$search_field = " a.booking_no";
		else if ($txt_search_by == 2)
			$search_field = " c.requisition_no";
		else if (trim($txt_search_by) == 6) // Issue No
			$search_field = " e.issue_number";
		else if (trim($txt_search_by) == 8)
			$search_field = " d.demand_no";
		else
			$search_field = " c.knit_id";

		if ($txt_search_common != "" && ($txt_search_by == 4 || $txt_search_by == 5)) {
			if ($txt_search_by == 4)
				$search_field = " w.grouping";
			else
				$search_field = " w.file_no";

			$sql = "select a.buyer_id, a.booking_no, b.knitting_source, b.knitting_party, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id as knit_id, sum(c.yarn_qnty) as yarn_qnty, d.mst_id as issue_id,e.issue_number_prefix_num
			from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls p, wo_po_break_down w, ppl_yarn_requisition_entry c, inv_transaction d ,inv_issue_master e
			where e.id=d.mst_id and  a.id=b.mst_id and b.id=p.dtls_id and p.po_id=w.id and b.id=c.knit_id and d.requisition_no=c.requisition_no and d.receive_basis=3 and d.item_category=1 and d.transaction_type=2 and a.company_id=$company and a.buyer_id like '$buyer_cond' and $search_field like '%$txt_search_common' $sql_cond and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
			group by a.buyer_id, a.booking_no, b.knitting_source, b.knitting_party, c.requisition_no, c.prod_id, c.requisition_date, c.knit_id, d.mst_id,e.issue_number_prefix_num";
		} else {

			$sql = " select a.buyer_id,a.is_sales,a.within_group,a.booking_no, b.knitting_source, b.knitting_party, c.requisition_no, d.prod_id, c.requisition_date, c.knit_id as knit_id, sum(d.cons_quantity ) as yarn_qnty,d.mst_id as issue_id,d.demand_no,d.demand_id,e.issue_number_prefix_num from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c,inv_transaction d,inv_issue_master e, ppl_planning_entry_plan_dtls f where a.id=b.mst_id and b.id=c.knit_id and c.requisition_no=d.requisition_no and c.prod_id=d.prod_id and d.mst_id=e.id and d.transaction_type=2 and d.receive_basis in(3,8) and a.company_id=$company and a.buyer_id like '$buyer_cond' and $search_field like '%$txt_search_common' $sql_cond and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 group by a.buyer_id,a.is_sales,a.within_group,a.booking_no, b.knitting_source, b.knitting_party, c.requisition_no, d.prod_id, c.requisition_date, c.knit_id,d.mst_id,d.demand_no,d.demand_id,e.issue_number_prefix_num";
			// ommit for delete program- crm id : 20651 --a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			//echo $sql;
		}
		//echo $sql;

		$result = sql_select($sql);

		$all_issue_id = array();
		$po_no_arr = array();
		foreach ($result as $row) {
			$all_issue_id[$row[csf("issue_id")]] = $row[csf("issue_id")];
			$all_knit_id[$row[csf("knit_id")]] 	 = $row[csf("knit_id")];
		}

		if (!empty($all_knit_id)) {
			if ($db_type == 0) {
				$plan_details_data = sql_select("select dtls_id,is_sales, group_concat(po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company and dtls_id in(" . implode(",", $all_knit_id) . ") group by dtls_id,is_sales");
				foreach ($plan_details_data as $plan_row) {
					$plan_details_array[$plan_row[csf("dtls_id")]] = $plan_row[csf("po_id")];
					if ($plan_row[csf("is_sales")] == 1) {
						$sales_id_arr[$plan_row[csf("po_id")]] = $plan_row[csf("po_id")];
					} else {
						$all_po_id[$plan_row[csf("po_id")]] = $plan_row[csf("po_id")];
					}
				}
			} else {
				$plan_details_data = sql_select("select dtls_id,is_sales, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company and dtls_id in(" . implode(",", $all_knit_id) . ") group by dtls_id,is_sales");
				foreach ($plan_details_data as $plan_row) {
					$plan_details_array[$plan_row[csf("dtls_id")]] = $plan_row[csf("po_id")];
					if ($plan_row[csf("is_sales")] == 1) {
						$sales_id_arr[$plan_row[csf("po_id")]] = $plan_row[csf("po_id")];
					} else {
						$all_po_id[$plan_row[csf("po_id")]] = $plan_row[csf("po_id")];
					}
				}
			}
		}

		if (!empty($all_po_id)) {
			$data_array = sql_select("select a.id,a.po_number, a.grouping, a.file_no,b.buyer_name from wo_po_break_down a,wo_po_details_master b where a.id in(" . implode(",", $all_po_id) . ") and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$po_arr = array();
			foreach ($data_array as $row) {
				$po_arr[$row[csf('id')]]['no'] 			= $row[csf('po_number')];
				$po_arr[$row[csf('id')]]['ref'] 		= $row[csf('grouping')];
				$po_arr[$row[csf('id')]]['file'] 		= $row[csf('file_no')];
				$po_arr[$row[csf('id')]]['buyer_name'] 	= $row[csf('buyer_name')];
			}
		}

		if (!empty($sales_id_arr)) {
			$sales_order_result = sql_select("select id,job_no,buyer_id,po_buyer from fabric_sales_order_mst where id in(" . implode(",", $sales_id_arr) . ") and status_active=1 and is_deleted=0");
			$sales_arr = array();
			foreach ($sales_order_result as $sales_row) {
				$sales_arr[$sales_row[csf("id")]]["buyer_id"] 	= $sales_row[csf("buyer_id")];
				$sales_arr[$sales_row[csf("id")]]["job_no"] 	= $sales_row[csf("job_no")];
				$sales_arr[$sales_row[csf("id")]]["job_no"] 	= $sales_row[csf("job_no")];
				$sales_arr[$sales_row[csf("id")]]["po_buyer"] 	= $sales_row[csf("po_buyer")];
			}
		}

		if (!empty($all_issue_id)) {
			$all_issue_id = array_chunk($all_issue_id, 999);
			$issue_cond = " and(";
			foreach ($all_issue_id as $issue_id) {
				if ($issue_cond == " and(") $issue_cond .= " a.mst_id in(" . implode(',', $issue_id) . ")";
				else $issue_cond .= "  or a.mst_id in(" . implode(',', $issue_id) . ")";
			}
			$issue_cond .= ")";
			$issue_lot = "select a.mst_id,a.prod_id,b.lot,a.cons_quantity,a.return_qnty from inv_transaction a,product_details_master b where a.prod_id=b.id and a.transaction_type=2 and a.item_category=1 and b.item_category_id=1 and a.status_active=1 $issue_cond";

			$issue_lot_data = array();
			$issue_qty_arr = array();
			$issue_lot_result = sql_select($issue_lot);
			foreach ($issue_lot_result as $row) {
				$issue_lot_data[$row[csf("prod_id")]] 			.= $row[csf("lot")] . ",";
				$issue_qty_arr[$row[csf("mst_id")]] 			+= $row[csf("cons_quantity")];
				$issue_return_able_qty_arr[$row[csf("mst_id")]]	+= $row[csf("return_qnty")];
			}
		}
		$i = 1;
	?>
		<div align="center">
			<div style="width:1230px;">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="100%" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="60">Issue No</th>
							<th width="60">Issue Qty.</th>
							<th width="80">Returnable Qty.</th>
							<th width="120">Knit. Comp.</th>
							<th width="100">Buyer</th>
							<th width="70">Program No</th>
							<th width="120">Reqsn./Demand No</th>
							<th width="100">Booking No</th>
							<th width="110">Order/FSO No</th>
							<th width="90">Internal Ref</th>
							<th width="100">File No</th>
							<th width="70">Reqsn. Date</th>
							<th>Lot No.</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:1230px; overflow-y:scroll; max-height:250px;">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="1210" rules="all" id="list_view">
					<tbody>
						<?
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";

							if ($row[csf('is_sales')] == 1) {
								$po_no = $sales_arr[$row[csf("po_id")]]["job_no"];
								if ($row[csf('within_group')] == 1) {
									$buyer_nam = $buyer_arr[$sales_arr[$row[csf("po_id")]]["po_buyer"]];
								} else {
									$buyer_nam = $buyer_arr[$sales_arr[$row[csf("po_id")]]["buyer_id"]];
								}
							} else {
								$po_id = array_unique(explode(",", $plan_details_array[$row[csf('knit_id')]]));
								$po_no = "";
								$fileNo = "";
								$buyer_nam = "";
								$internal_ref = "";
								foreach ($po_id as $val) {
									if ($po_no == '') $po_no = $po_arr[$val]['no'];
									else $po_no .= "," . $po_arr[$val]['no'];
									if ($internal_ref == "") $internal_ref = $po_arr[$val]['ref'];
									else $internal_ref .= "," . $po_arr[$val]['ref'];
									if ($fileNo == "") $fileNo = $po_arr[$val]['file'];
									else $fileNo .= "," . $po_arr[$val]['file'];
								}

								$buyer_nam = $buyer_arr[$row[csf("buyer_id")]];
							}

							$kniting_comp = "";
							if ($row[csf('knitting_source')] == 1) $kniting_comp = $company_arr[$row[csf('knitting_party')]];
							else $kniting_comp = $supplier_arr[$row[csf('knitting_party')]];
							//echo $receive_basis."_".$row[csf('requisition_no')]."_".$row[csf('requisition_no')]."_".$row[csf('demand_no')]."_".$row[csf('demand_id')]."_".$buyer_nam;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $receive_basis; ?>_<? echo $row[csf('requisition_no')]; ?>_<? echo $row[csf('requisition_no')]; ?>_<? echo $row[csf('demand_no')]; ?>_<? echo $row[csf('demand_id')]; ?>_<? echo $buyer_nam; ?>_<? echo $row[csf('is_sales')]; ?>_<? echo $row[csf('issue_id')]; ?>***<? echo $row[csf('booking_no')]; ?>')" style="cursor:pointer">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="60" align="center">
									<p><? echo $row[csf("issue_number_prefix_num")]; ?></p>
								</td>
								<td width="60" align="right">
									<p><? echo number_format($issue_qty_arr[$row[csf("issue_id")]], 2); ?></p>
								</td>
								<td width="80" align="right">
									<p><? echo number_format($issue_return_able_qty_arr[$row[csf("issue_id")]], 2); ?></p>
								</td>
								<td width="120" align="center">
									<p><? echo $kniting_comp; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo $buyer_nam; ?></p>
								</td>
								<td width="70" align="center">
									<p><? echo $row[csf("knit_id")]; ?></p>
								</td>
								<td width="120" align="center">
									<p><? echo "<strong>R:</strong>" . $row[csf("requisition_no")] . "<br /><strong>D:</strong>" . $row[csf("demand_no")]; ?></p>
								</td>
								<td width="100" align="center">
									<p><? echo $row[csf("booking_no")]; ?></p>
								</td>
								<td width="110" align="center">
									<p><? echo $po_no; ?></p>
								</td>
								<td width="90">
									<p><? echo implode(",", array_unique(explode(",", $internal_ref))); ?>&nbsp;</p>
								</td>
								<td width="100">
									<p><? echo implode(",", array_unique(explode(",", $fileNo))); ?>&nbsp;</p>
								</td>
								<td align="center" width="70">
									<p><? echo change_date_format($row[csf("requisition_date")]); ?></p>
								</td>
								<td align="center"><? $issue_lot = implode(",", array_unique(explode(",", chop($issue_lot_data[$row[csf("prod_id")]], ','))));
													echo $issue_lot; ?></td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	<?
	}
	exit();
}

if ($action == "populate_knitting_source") {
	$ex_data = explode("**", $data);
	$req_id = str_replace("'", "", $ex_data[0]);
	$company_id = str_replace("'", "", $ex_data[1]);
	$basis = str_replace("'", "", $ex_data[2]);
	$issue_id = str_replace("'", "", $ex_data[3]);

	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');

	if ($basis == 3 ||  $basis == 8) {
		$knit_sql = sql_select("select b.knitting_source,b.knitting_party from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.id and a.requisition_no='$req_id' and a.status_active=1"); // ommit cause :program can delete: and b.status_active=1

		foreach ($knit_sql as $row) {
			echo "$('#cbo_knitting_source').val('" . $row[csf("knitting_source")] . "');\n";
			echo "load_drop_down( 'requires/yarn_issue_return_controller','" . $row[csf("knitting_source")] . "'+'**'+'" . $company_id . "', 'load_drop_down_knit_com', 'knitting_company_td' );\n";
			echo "$('#cbo_knitting_company').val('" . $row[csf("knitting_party")] . "');\n";
		}
	} else {
		$knit_sql = sql_select("select knit_dye_source, knit_dye_company from inv_issue_master where id=$issue_id");

		foreach ($knit_sql as $row) {
			echo "$('#cbo_knitting_source').val('" . $row[csf("knit_dye_source")] . "');\n";
			echo "load_drop_down( 'requires/yarn_issue_return_controller','" . $row[csf("knit_dye_source")] . "'+'**'+'" . $company_id . "', 'load_drop_down_knit_com', 'knitting_company_td' );\n";
			echo "$('#cbo_knitting_company').val('" . $row[csf("knit_dye_company")] . "');\n";
		}
	}

	exit();
}

if ($action == "populate_knitting_source_book") {
	if ($data != "") $knit_sql = sql_select("select knit_dye_source, knit_dye_company from inv_issue_master where id=$data");
	foreach ($knit_sql as $row) {
		echo "$('#cbo_knitting_source').val('" . $row[csf("knit_dye_source")] . "');\n";
		echo "load_drop_down( 'requires/yarn_issue_return_controller','" . $row[csf("knitting_source")] . "'+'**'+'" . $company_id . "', 'load_drop_down_knit_com', 'knitting_company_td' );;\n";
		echo "$('#cbo_knitting_company').val('" . $row[csf("knit_dye_company")] . "');\n";
		exit();
	}
}


if ($action == "po_popup") {
	echo load_html_head_contents("PO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$data = explode("_", $data);
	$po_id = $data[0]; //order ID
	if ($data[1]) $type = $data[1];
	else $type = 0; //is popup search or not
	$prevQnty = $data[2]; //previous input qnty po wise
	$rejecprevQnty = $data[5];
	if ($data[3] != "") $prev_method = $data[3];
	else $prev_method = $distribution_method;
	if ($data[4] != "") $issueQnty = $data[4];
	else $issueQnty = $issueQnty;
	?>

	<script>
		var receive_basis = <? echo $receive_basis; ?>;

		function fn_show_check() {
			if (form_validation('cbo_buyer_name', 'Buyer Name') == false) {
				return;
			}
			show_list_view($('#txt_search_common').val() + '_' + $('#cbo_search_by').val() + '_<? echo $cbo_company_id; ?>_' + $('#cbo_buyer_name').val() + '_' + '<? echo $all_po_id; ?>' + '_' + <? echo $receive_basis; ?>, 'create_po_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}

		function distribute_qnty(str) {
			/* Formula Date : 29-09-2020
			// Formula instructed by : MD Showrab
			// Formula Implemented by : Didarula Alam
			// Formula is : Return qty = (po wise issue QTY/Total issue QTY)*Total Return QTY
			*/

			var tot_po_qnty = $('#tot_po_qnty').val() * 1;
			var tot_issue_qnty = $('#tot_issue_qnty').val() * 1;
			var userGiven = $('#txt_prop_grey_qnty').val() * 1;
			var tblRow = $("#tbl_list_search tr").length;
			var len = totalGrey = 0;

			$("#tbl_list_search").find('tr').each(function() {
				len = len + 1;

				var txtOrginal = $(this).find('input[name="txtOrginal[]"]').val() * 1;
				var txtIsSales = $(this).find('input[name="txtIsSales[]"]').val() * 1;

				if (txtOrginal == 0) {
					$(this).remove();
				} else {
					var po_wise_issue_qnty = $(this).find('input[name="txtPoIssueQnty[]"]').val() * 1;
					var po_wise_returnable_qnty = $(this).find('input[name="txtReturnableQnty[]"]').val() * 1;

					var return_qnty = (po_wise_issue_qnty / tot_issue_qnty) * userGiven;

					var perc = (po_wise_issue_qnty / tot_issue_qnty) * 100;

					var return_qnty = (perc * userGiven) / 100;

					if (po_wise_returnable_qnty > 0) // returnable balance available
					{
						if (return_qnty > po_wise_returnable_qnty) {
							return_qnty = po_wise_returnable_qnty;
						} else {
							return_qnty = return_qnty;
						}
					} else {
						return_qnty = 0; //returnable balance not available
					}

					$(this).find('input[name="txtGreyQnty[]"]').val(return_qnty.toFixed(2));

				}

			});

		}

		//===Reject Qty====
		function distribute_qnty2(str) {
			if (str == 1) {
				var tot_po_qnty = $('#tot_po_qnty').val() * 1;
				var txt_reject_grey_qnty = $('#txt_prop_reject_qnty').val() * 1;
				var tblRow = $("#tbl_list_search tr").length;
				var len = totalReject = 0;
				$("#tbl_list_search").find('tr').each(function() {
					len = len + 1;
					var txtOrginal = $(this).find('input[name="txtOrginal[]"]').val() * 1;
					var txtIsSales = $(this).find('input[name="txtIsSales[]"]').val() * 1;

					if (txtOrginal == 0) {
						$(this).remove();
					} else {
						var po_qnty = $(this).find('input[name="txtPoQnty[]"]').val() * 1;
						var perc = (po_qnty / tot_po_qnty) * 100;
						var reject_qnty = (perc * txt_reject_grey_qnty) / 100;

						totalReject = totalReject * 1 + reject_qnty * 1;
						totalReject = totalReject.toFixed(2);
						if (tblRow == len) {
							var balance = txt_reject_grey_qnty - totalReject;
							if (balance != 0) reject_qnty = reject_qnty + (balance);
						}
						if (txtIsSales == 1) reject_qnty = txt_reject_grey_qnty;
						$(this).find('input[name="txtRejectQnty[]"]').val(reject_qnty.toFixed(2));
					}

				});
			} else {
				$('#txt_prop_reject_qnty').val('');
				$("#tbl_list_search").find('tr').each(function() {
					$(this).find('input[name="txtRejectQnty[]"]').val('');
				});
			}
		}

		//==process loss distribution
		function process_loss_qty_distribution(str) {
			var tblRow = $("#tbl_list_search tr").length;
			var userGiven = $('#txt_prop_process_loss_qnty').val() * 1;
			var processLossQty = (userGiven / tblRow);

			if (str == 1) {
				$("#tbl_list_search").find('tr').each(function() {
					$(this).find('input[name="txtproCessLossQnty[]"]').val(processLossQty.toFixed(2));
				});
			} else {
				$('#txt_prop_process_loss_qnty').val('');
				$("#tbl_list_search").find('tr').each(function() {
					$(this).find('input[name="txtproCessLossQnty[]"]').val('');
				});
			}

		}

		var selected_id = new Array();

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

		function set_all() {
			var old = document.getElementById('txt_po_row_id').value;
			if (old != "") {
				old = old.split(",");
				for (var i = 0; i < old.length; i++) {
					js_set_value(old[i])
				}
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_individual_id' + str).val());

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
			}
			var id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}
			id = id.substr(0, id.length - 1);

			$('#po_id').val(id);
		}

		function show_grey_prod_recv() {
			var po_id = $('#po_id').val();
			var prev_save_string = $('#prev_save_string').val();
			var prev_method = $('#prev_method').val();
			var prev_total_qnty = $('#prev_total_qnty').val();
			show_list_view(po_id + '_' + '1' + '_' + prev_save_string + '_' + prev_method + '_' + prev_total_qnty, 'po_popup', 'search_div', 'yarn_issue_return_controller', '');
		}

		function hidden_field_reset() {
			$('#po_id').val('');
			$('#save_string').val('');
			$('#tot_grey_qnty').val('');
			$('#tot_reject_qnty').val('');
			selected_id = new Array();
		}

		function fnc_close() {
			var save_string = '';
			var tot_grey_qnty = '';
			var no_of_roll = '';
			var tot_reject_qnty = '';
			var tot_processloss_qnty = '';
			var po_id_array = new Array();

			$("#tbl_list_search").find('tr').each(function() {
				var txtPoId = $(this).find('input[name="txtPoId[]"]').val();
				var txtGreyQnty = $(this).find('input[name="txtGreyQnty[]"]').val();
				var txtRejectQnty = $(this).find('input[name="txtRejectQnty[]"]').val();
				var txtIsSales = $(this).find('input[name="txtIsSales[]"]').val();
				var txtProcessLossQty = $(this).find('input[name="txtproCessLossQnty[]"]').val();

				tot_grey_qnty = tot_grey_qnty * 1 + txtGreyQnty * 1;
				tot_reject_qnty = tot_reject_qnty * 1 + txtRejectQnty * 1;
				tot_processloss_qnty = tot_processloss_qnty * 1 + txtProcessLossQty * 1;


				//alert(txtRejectQnty);
				if (txtGreyQnty * 1 > 0 || txtRejectQnty * 1 > 0) {
					if (save_string == "") {
						save_string = txtPoId + "**" + txtGreyQnty + "**" + txtRejectQnty + "**" + txtIsSales + "**" + txtProcessLossQty;
					} else {
						save_string += "," + txtPoId + "**" + txtGreyQnty + "**" + txtRejectQnty + "**" + txtIsSales + "**" + txtProcessLossQty;
					}

					if (jQuery.inArray(txtPoId, po_id_array) == -1) {
						po_id_array.push(txtPoId);
					}
				}

			});

			$('#save_string').val(save_string);
			$('#tot_grey_qnty').val(tot_grey_qnty);
			$('#tot_reject_qnty').val(tot_reject_qnty);
			$('#tot_processloss_qnty').val(tot_processloss_qnty);
			$('#all_po_id').val(po_id_array);
			$('#distribution_method').val($('#cbo_distribiution_method').val());
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>

		<form name="searchdescfrm" id="searchdescfrm">
			<fieldset style="width:800px;margin-left:10px">
				<?
				$req_cond = '';

				if ($receive_basis == 1) {
					$sql_cond = "and a.booking_no='$booking_no' and a.booking_id=$booking_id";

					if ($issue_purpose == 2) {
						$sql_cond .= "and b.dyeing_color_id=$dyeing_color_id";
					}
				} else if ($receive_basis == 3) {
					$sql_cond = "and b.requisition_no='$booking_no'";
				} else if ($receive_basis == 8) {
					$sql_cond = "and b.requisition_no='$hdn_req_no' and b.demand_id=$booking_id";
				}

				$sql_po = "select c.po_breakdown_id as po_id,c.is_sales from inv_issue_master a , inv_transaction b,order_wise_pro_details c where  a.id=b.mst_id and a.item_category=1 and b.id=c.trans_id and b.company_id=$cbo_company_id and b.mst_id = $txt_issue_id and b.prod_id = $txt_prod_id and b.transaction_type=2 and b.item_category=1 $sql_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

				//echo $sql_po;die();

				$sql_po_result = sql_select($sql_po);

				foreach ($sql_po_result as $po_row) {
					$po_ids .= $po_row[csf("po_id")] . ",";
					$po_data[$po_row[csf("po_id")]]["is_sales"] = $po_row[csf("is_sales")];
					$is_sales = $po_row[csf("is_sales")];
				}

				$po_id = implode(",", array_unique(explode(",", chop($po_ids, ","))));

				$transact_sql = "select c.po_breakdown_id,c.prod_id,b.mst_id,b.issue_id,b.transaction_type,
					(case when b.mst_id = $txt_issue_id and b.transaction_type=2 and c.entry_form=3 then c.quantity else 0 end) as quantity
					from inv_issue_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=$txt_prod_id and c.po_breakdown_id in($po_id) and b.transaction_type in (2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 " . $sql_cond . "

					union all select c.po_breakdown_id,c.prod_id,b.mst_id,b.issue_id,b.transaction_type,
					(case when b.issue_id=$txt_issue_id and b.transaction_type=4 and c.entry_form=9 then c.quantity else 0 end) as quantity
					from inv_receive_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=$txt_prod_id and c.po_breakdown_id in($po_id) and b.transaction_type in (4) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 " . $sql_cond;

				//echo $transact_sql; die();

				$tract_result = sql_select($transact_sql);
				$order_wise_return_qty = $order_wise_issue_qty = array();
				foreach ($tract_result as $row) {
					if ($row[csf("transaction_type")] == 2) {
						if ($row[csf("quantity")] > 0) {
							$order_wise_issue_qty[$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("quantity")];
						}
					} else {

						if ($row[csf("quantity")] > 0) {
							$order_wise_return_qty[$row[csf("issue_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("quantity")];
						}
					}
				}

				if ($type != 1) {
				?>
					<!-- previous data here-->
					<input type="hidden" name="prev_save_string" id="prev_save_string" class="text_boxes" value="<? echo $save_data; ?>">
					<input type="hidden" name="prev_total_qnty" id="prev_total_qnty" class="text_boxes" value="<? echo $issueQnty; ?>">
					<input type="hidden" name="prev_total_reject_qnty" id="prev_total_reject_qnty" class="text_boxes" value="<? echo $reject_qnty; ?>">
					<input type="hidden" name="prev_total_reject_qnty" id="prev_total_reject_qnty" class="text_boxes" value="<? echo $processloss_qnty; ?>">
					<input type="hidden" name="prev_method" id="prev_method" class="text_boxes" value="<? echo $distribution_method; ?>">
					<!--- END-->
					<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
					<input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
					<input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes" value="">
					<input type="hidden" name="tot_processloss_qnty" id="tot_processloss_qnty" class="text_boxes" value="">
					<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
					<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<?
				}

				if ($receive_basis == 2) {
				?>
					<div id="search_div" style="margin-top:10px">
						<?
						if ($all_po_id != "" || $po_id != "") {
						?>
							<div style="width:800px; margin-top:10px" align="center">
								<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500" align="center">
									<thead>
										<th>Total Return Qty</th>
										<th>Total Reject Qty</th>
										<th>Distribution Method</th>
									</thead>
									<tr class="general">
										<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $issueQnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
										<td><input type="text" name="txt_prop_reject_qnty" id="txt_prop_reject_qnty" class="text_boxes_numeric" value="<? echo $reject_qnty; ?>" style="width:120px" onBlur="distribute_qnty2(document.getElementById('cbo_distribiution_method').value)"></td>

										<td><input type="text" name="txt_prop_process_loss_qnty" id="txt_prop_process_loss_qnty" class="text_boxes_numeric" value="<? echo $process_loss_qnty; ?>" style="width:120px" onBlur="process_loss_qty_distribution(document.getElementById('cbo_distribiution_method').value)"></td>

										<td>
											<?
											$distribiution_method = array(1 => "Proportionately", 2 => "Manually");
											echo create_drop_down("cbo_distribiution_method", 160, $distribiution_method, "", 0, "--Select--", $prev_method, "distribute_qnty(this.value);distribute_qnty2(this.value);", 0);
											?>
										</td>
									</tr>
								</table>
							</div>
							<div style="margin-left:10px; margin-top:10px">
								<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800">
									<thead>
										<th width="150">PO No</th>
										<th width="100">PO Qnty</th>
										<th width="100">Return Qnty</th>
										<th width="100">Reject Qnty</th>
										<th width="100">Process Loss Qnty</th>
									</thead>
								</table>
								<div style="width:1000px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
									<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" id="tbl_list_search">
										<?

										if ($po_id == "") $po_id = $all_po_id;
										else $po_id = $po_id;
										$i = 1;
										$tot_po_qnty = 0;
										if ($po_id != "") {
											$po_sql = "select b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b,order_wise_pro_details c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and b.id in ($po_id) and c.prod_id = $txt_prod_id order by b.id";
										}
										$explSaveData = explode(",", $save_data);
										$nameArray = sql_select($po_sql);
										foreach ($nameArray as $row) {
											if ($i % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";

											$tot_po_qnty += $row[csf('po_qnty_in_pcs')];

											$woQnty = explode("**", $explSaveData[$i - 1]);
											if ($woQnty[0] == $row[csf('id')]) {
												$qnty = $woQnty[1];
												$reject_qnty = $woQnty[2];
												$process_loss_qnty = $woQnty[4];
											} else {
												$qnty = "";
												$reject_qnty = "";
												$process_loss_qnty = "";
											}
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
												<td width="150">
													<p><? echo $row[csf('po_number')]; ?></p>
													<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
													<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="1">
													<input type="hidden" name="txtIsSales[]" id="txtIsSales_<? echo $i; ?>" value="<? echo $po_data[$row[csf("id")]]["is_sales"]; ?>">
												</td>
												<td width="110" align="right">
													<? echo $row[csf('po_qnty_in_pcs')]; ?>
													<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
												</td>
												<td width="110" align="center">
													<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $qnty; ?>">
												</td>
												<td width="100" align="center">
													<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $reject_qnty; ?>">
												</td>

												<td width="100" align="center">
													<input type="text" name="txtproCessLossQnty[]" id="txtproCessLossQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $process_loss_qnty; ?>">
												</td>
											</tr>
										<?
											$i++;
										}
										?>
										<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
									</table>
								</div>
								<table width="800" id="table_id">
									<tr>
										<td align="center">
											<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
										</td>
									</tr>
								</table>
							</div>
						<?
						}
						?>

					</div>
				<?
				} else {
				?>
					<div style="width:800px; margin-top:10px" align="center">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500" align="center">
							<thead>
								<th>Total Return Qnty</th>
								<th>Total Reject Qnty</th>
								<th>Total Process Qnty</th>
								<th>Distribution Method</th>
							</thead>
							<tr class="general">
								<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $issueQnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)"></td>
								<td><input type="text" name="txt_prop_reject_qnty" id="txt_prop_reject_qnty" class="text_boxes_numeric" value="<? echo $reject_qnty; ?>" style="width:120px" onBlur="distribute_qnty2(document.getElementById('cbo_distribiution_method').value)"></td>
								<td><input type="text" name="txt_prop_process_loss_qnty" id="txt_prop_process_loss_qnty" class="text_boxes_numeric" value="<? echo $process_loss_qnty; ?>" style="width:120px" onBlur="process_loss_qty_distribution(document.getElementById('cbo_distribiution_method').value)"></td>
								<td>
									<?
									$distribiution_method = array(1 => "Proportionately", 2 => "Manually");
									echo create_drop_down("cbo_distribiution_method", 160, $distribiution_method, "", 0, "--Select--", $prev_method, "distribute_qnty(this.value);distribute_qnty2(this.value);", 0);
									?>
								</td>
							</tr>
						</table>
					</div>

					<div style="margin-left:10px; margin-top:10px">

						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
							<thead>
								<th width="150">PO/FSO No</th>
								<th width="100">PO Qnty</th>
								<th width="100">Issue Qnty</th>
								<th width="100">Received Qnty</th>
								<th width="100">Rreturnable Qty</th>
								<th width="100">Return Qnty</th>
								<th width="100">Reject Qynt</th>
								<th width="100">Process Loss Qnty</th>
							</thead>
						</table>

						<div style="width:920px; max-height:230px; overflow-y:scroll" id="list_container" align="left">
							<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" id="tbl_list_search">
								<?
								if ($receive_basis == 1) // Booking Basis
								{
									$is_sales = return_field_value("is_sales", "wo_yarn_dyeing_mst", "ydw_no='" . $booking_no . "' and id=$booking_id", "is_sales");

									if ($is_sales == 1) {

										$po_sql = "select a.po_breakdown_id id,d.job_no po_number,sum(b.cons_quantity) po_quantity,1 as total_set_qnty from order_wise_pro_details a, inv_transaction b, inv_issue_master c,fabric_sales_order_mst d where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id=d.id and c.entry_form=3 and c.item_category=1 and b.item_category=1 and b.transaction_type=2 and d.id in($po_id) and b.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id,d.job_no";
									} else {
										$po_sql = "select b.id, b.po_number, b.po_quantity, a.total_set_qnty from wo_po_details_master a, wo_po_break_down b, inv_transaction c, inv_issue_master d,order_wise_pro_details e where a.job_no=b.job_no_mst and b.id=e.po_breakdown_id and c.mst_id=d.id and d.entry_form=3 and d.item_category=1 and c.item_category=1 and c.transaction_type=2 and b.id in($po_id) and c.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.po_number, b.po_quantity, a.total_set_qnty order by b.id";
									}

									// =========================================
									if (str_replace("'", "", $cbo_receive_purpose) == 2) // dyeing color
									{
										$dyeing_color_id = str_replace("'", "", $color_id);
										$dyeing_color_cond = " AND b.dyeing_color_id=$dyeing_color_id";
									}

									if ($issue_purpose == 2) // dyeing color
									{
										$dyeing_color_id = str_replace("'", "", $dyeing_color_id);
										$dyeing_color_cond = " AND b.dyeing_color_id=$dyeing_color_id";
										$grey_yarn_prod_cond = " and c.grey_prod_id in ('" . $txt_prod_id . "')";
									}

									//for receive qty
									$sqlRcv = " SELECT a.id,b.prod_id,c.po_breakdown_id, c.quantity  FROM inv_receive_master a INNER JOIN inv_transaction b ON a.id = b.mst_id INNER JOIN order_wise_pro_details c ON b.id = c.trans_id INNER JOIN product_details_master d ON c.prod_id=d.id WHERE a.booking_no = '" . $booking_no . "' AND booking_id=$booking_id and c.trans_type = 1 AND b.item_category = 1 AND b.transaction_type = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 $dyeing_color_cond $grey_yarn_prod_cond";
									//echo $sqlRcv;									
									$sqlRcvRslt = sql_select($sqlRcv);

									$receiveIdArr = $dyed_prod = $order_wise_rcv_qty = array();
									foreach ($sqlRcvRslt as $row) {
										$receiveIdArr[$row[csf('id')]] = $row[csf('id')];
										$order_wise_rcv_qty[$row[csf('po_breakdown_id')]]['receive_qnty'] += $row[csf('quantity')];
										$dyed_prod[$row[csf('prod_id')]] = $row[csf('prod_id')];
									}
									//echo "<pre>";
									//print_r($dataRcvArr);

									//for both type of receive return 
									if (!empty($dyed_prod)) {
										$sqlRcvRtn = " SELECT c.po_breakdown_id, c.quantity  FROM inv_issue_master a INNER JOIN inv_transaction b ON a.id = b.mst_id INNER JOIN order_wise_pro_details c ON b.id = c.trans_id WHERE ( a.received_id IN (" . implode(',', $receiveIdArr) . ") or a.received_id IN (select a.received_id as issure_rtn_rcv_id from inv_mrr_wise_issue_details c,inv_transaction b, inv_issue_master a where c.issue_trans_id=b.id and b.mst_id=a.id and b.prod_id=c.prod_id and c.entry_form=8 and a.item_category=1 and b.prod_id IN(" . implode(',', $dyed_prod) . ")) ) AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form = 8 AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active = 1 AND b.is_deleted = 0 AND c.trans_type = 3 AND c.status_active = 1 AND c.is_deleted = 0 AND c.entry_form = 8 ";
										//echo $sqlRcvRtn;
										$sqlRcvRtnRslt = sql_select($sqlRcvRtn);
										$dataRcvRtnArr = array();
										foreach ($sqlRcvRtnRslt as $row) {
											$order_wise_rcv_rtn_qty[$row[csf('po_breakdown_id')]]['return_qnty'] += $row[csf('quantity')];
										}
										//echo "<pre>";
										//print_r($dataRcvRtnArr);
									}
									// =========================================
								} else if ($receive_basis == 3 || $receive_basis == 8) // Requisition && Demand basis
								{
									if ($receive_basis == 3) {
										$is_sales = return_field_value("a.is_sales", "ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b", "a.dtls_id=b.knit_id and b.requisition_no='" . $booking_no . "' group by a.is_sales", "is_sales"); //ommit : and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
									} else {
										$is_sales = return_field_value("a.is_sales", "ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b", "a.dtls_id=b.knit_id and b.requisition_no='" . $hdn_req_no . "' group by a.is_sales", "is_sales"); //omm: and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
									}

									$requisition_cond = ($receive_basis == 3) ? " and b.requisition_no='$booking_no' " : " and b.requisition_no='$hdn_req_no' and b.demand_id=$booking_id";

									if ($is_sales != 1) {
										$po_sql = "select d.id, d.po_number,e.total_set_qnty, d.po_quantity, d.pub_shipment_date,b.prod_id from inv_issue_master a, inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e where a.id = b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.company_id=$cbo_company_id and a.id = $txt_issue_id and b.prod_id = $txt_prod_id and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $requisition_cond";
									} else {
										$po_sql = "select d.id,d.job_no as po_number, sum(e.grey_qty) po_quantity,1 as total_set_qnty from inv_issue_master a, inv_transaction b,order_wise_pro_details c,fabric_sales_order_mst d,fabric_sales_order_dtls e where a.id = b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.id=e.mst_id and a.company_id=$cbo_company_id and a.id = $txt_issue_id and b.prod_id = $txt_prod_id and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $requisition_cond group by d.id,d.job_no";
									}
									//echo $po_sql;
								} else if ($receive_basis == 4) // Sales order basis
								{
									$is_sales = 1;
									$sales_cond = " job_no='$booking_no'";

									$po_sql = "select a.id, a.job_no as po_number, sum(b.grey_qty) po_quantity,1 as total_set_qnty  from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sales_cond";
								}

								$i = 1;
								$tot_po_qnty = 0;

								if ($save_string == "" && $type == 1) $save_data = $prevQnty;
								$explSaveData = explode(",", $save_data);
								foreach ($explSaveData as $save_row) {
									$po_save_row = explode("**", $save_row);
									$po_wise_return[$po_save_row[0]] = $po_save_row[1];
									$po_wise_reject[$po_save_row[0]] = $po_save_row[2];
									$po_wise_process_loss[$po_save_row[0]] = $po_save_row[4];
								}

								//echo $po_sql;

								$nameArray = sql_select($po_sql);
								foreach ($nameArray as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";

									$po_qnty_in_pcs = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
									$tot_po_qnty += $po_qnty_in_pcs;

									$woQnty = explode("**", $explSaveData[$i - 1]);
									$prod_id = str_replace("'", " ", $txt_prod_id);
									$issue_id = str_replace("'", " ", $txt_issue_id);

									if ($receive_basis == 1) {
										$po_wise_issue_qty = $order_wise_issue_qty[$issue_id][$row[csf('id')]][$prod_id];
										$po_wise_issue_rtn_qty = $order_wise_return_qty[$issue_id][$row[csf('id')]][$prod_id];
										$pow_wise_net_rcv_qty = ($order_wise_rcv_qty[$row[csf('id')]]['receive_qnty'] - $order_wise_rcv_rtn_qty[$row[csf('id')]]['return_qnty']);
										$po_wise_returnable_qty = ($po_wise_issue_qty - $po_wise_issue_rtn_qty - $pow_wise_net_rcv_qty);
									} else {
										$po_wise_issue_qty = $order_wise_issue_qty[$issue_id][$row[csf('id')]][$prod_id];
										$po_wise_returnable_qty = ($order_wise_issue_qty[$issue_id][$row[csf('id')]][$prod_id] - $order_wise_return_qty[$issue_id][$row[csf('id')]][$prod_id]);
									}

									$qnty = $po_wise_return[$row[csf('id')]];
									$reject_qnty = $po_wise_reject[$row[csf('id')]];
									$process_loss_qnty = $po_wise_process_loss[$row[csf('id')]];

									//$is_sales = $po_data[$row[csf("id")]]["is_sales"];

								?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">

										<td width="150" align="center" title="<? echo $row[csf('id')]; ?>">
											<p><? echo $row[csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="1">
											<input type="hidden" name="txtIsSales[]" id="txtIsSales_<? echo $i; ?>" value="<? echo $is_sales; ?>">
										</td>

										<td width="100" align="right">
											<? echo number_format($po_qnty_in_pcs, 2); ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
										</td>

										<td width="100" align="right">
											<? echo number_format($po_wise_issue_qty, 2); ?>
											<input type="hidden" name="txtPoIssueQnty[]" id="txtPoIssueQnty_<? echo $i; ?>" value="<? echo $po_wise_issue_qty; ?>">
										</td>

										<td width="100" align="right">
											<? echo number_format($pow_wise_net_rcv_qty, 2); ?>
										</td>

										<td width="100" align="right">
											<? echo number_format($po_wise_returnable_qty, 2); ?>
											<input type="hidden" name="txtReturnableQnty[]" id="txtReturnableQnty_<? echo $i; ?>" value="<? echo $po_wise_returnable_qty; ?>">
										</td>

										<td width="100" align="center">
											<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $qnty; ?>">
										</td>

										<td width="100" align="center">
											<input type="text" name="txtRejectQnty[]" id="txtRejectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $reject_qnty; ?>">
										</td>

										<td width="100" align="center">
											<input type="text" name="txtproCessLossQnty[]" id="txtproCessLossQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $process_loss_qnty; ?>">
										</td>

									</tr>
								<?
									$i++;
									$totalIssueQty += $po_wise_issue_qty;
								}
								?>
								<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
								<input type="hidden" name="tot_issue_qnty" id="tot_issue_qnty" class="text_boxes" value="<? echo $totalIssueQty; ?>">
							</table>
						</div>

						<table width="900" id="table_id">
							<tr>
								<td align="center">
									<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								</td>
							</tr>
						</table>

					</div>
				<?
				}
				?>

			</fieldset>
		</form>

	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_po_search_list_view") {
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];

	$search_con = "";
	if ($search_by == 1 && $search_string != "")
		$search_con = " and b.po_number like '%$search_string%'";
	else if ($search_by == 2 && $search_string != "")
		$search_con = " and a.job_no like '%$search_string%'";

	$company_id = $data[2];
	$buyer_id = $data[3];
	$all_po_id = $data[4];
	$receiveBasis = $data[5];

	if ($all_po_id != "")
		$po_id_cond = " or b.id in($all_po_id)";
	else
		$po_id_cond = "";

	$hidden_po_id = explode(",", $all_po_id);

	if ($buyer_id == 0) {
		echo "<b>Please Select Buyer First</b>";
		die;
	}

	if ($receiveBasis == 1) {
		$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date
		from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$buyer_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date";
	} else {
		$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //$po_id_cond

	}
	//echo $sql;die;
?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="110">Style No</th>
				<th width="110">PO No</th>
				<th width="90">PO Quantity</th>
				<th width="50">UOM</th>
				<th>Shipment Date</th>
			</thead>
		</table>
		<div style="width:618px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				$po_row_id = '';
				$nameArray = sql_select($sql);
				foreach ($nameArray as $selectResult) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if (in_array($selectResult[csf('id')], $hidden_po_id)) {
						if ($po_row_id == "") $po_row_id = $i;
						else $po_row_id .= "," . $i;
					}

					$po_qnty_in_pcs = $selectResult[csf('po_quantity')] * $selectResult[csf('total_set_qnty')];

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="40" align="center"><? echo "$i"; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>" />
						</td>
						<td width="100">
							<p><? echo $selectResult[csf('job_no')]; ?></p>
						</td>
						<td width="110">
							<p><? echo $selectResult[csf('style_ref_no')]; ?></p>
						</td>
						<td width="110">
							<p><? echo $selectResult[csf('po_number')]; ?></p>
						</td>
						<td width="90" align="right"><? echo $po_qnty_in_pcs; ?></td>
						<td width="50" align="center">
							<p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p>
						</td>
						<td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>
					</tr>
				<?
					$i++;
				}
				?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>" />
			</table>
		</div>
		<table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="show_grey_prod_recv();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
<?

	exit();
}

if ($action == "itemdesc_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(mrr) {
			$("#hidden_recv_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="170">Search By</th>
							<th width="180" align="center" id="search_by_td_up">Enter Lot No</th>
							<th width="240">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								$search_by = array(1 => 'Lot No', 2 => 'Issue Number', 3 => 'Challan No', 4 => 'Item Name', 5 => 'Store Name');
								$dd = "change_search_event(this.value, '0*0*0*0*1', '0*0*0*0*select id,store_name from lib_store_location where company_id=$company', '../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_<? echo $company; ?>_'+'<? echo $booking_no; ?>'+'_'+'<? echo $basis; ?>'+'_'+'<? echo $issue_id; ?>', 'create_item_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_recv_number" value="" />

							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div style="margin-top:5px" align="center" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_item_search_list_view") {
	$ex_data = explode("_", $data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$booking_no = $ex_data[5];
	$basis = $ex_data[6];
	$issue_id = $ex_data[7];

	//echo $issue_id."test";

	$sql_cond = "";
	if (trim($txt_search_common) != "") {
		if (trim($txt_search_by) == 1) // for lot no
			$sql_cond .= " and c.lot LIKE '%$txt_search_common%'";
		else if (trim($txt_search_by) == 2) // for issue no
			$sql_cond .= " and a.issue_number LIKE '%$txt_search_common%'";
		else if (trim($txt_search_by) == 3) // for chllan no
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		else if (trim($txt_search_by) == 4) // for Item Description
			$sql_cond .= " and c.product_name_details LIKE '%$txt_search_common%'";
		else if (trim($txt_search_by) == 5) // for Store Name
			$sql_cond .= " and b.store_id='$txt_search_common'";
	}

	if ($basis == 1 || $basis == 4) // Booking / Sales order
	{
		if ($booking_no != "") {
			$sql_cond .= " and a.issue_basis=$basis and a.booking_no='$booking_no'";
		}
	} else // independent/requisition/demand
	{
		if ($basis == 8) // demand
		{
			if ($booking_no != "") {
				$sql_cond .= " and a.issue_basis=$basis and b.receive_basis=$basis and b.demand_no='$booking_no'";
			}
		} else // requisiiton/independent
		{
			if ($booking_no != "") // requisition
			{
				$sql_cond .= " and a.issue_basis=$basis and b.receive_basis=$basis and b.requisition_no='$booking_no'";
			} else { // independent
				$sql_cond .= " and a.issue_basis=$basis and b.receive_basis=$basis";
			}
		}
	}

	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.issue_date between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.issue_date between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";

	if (trim($issue_id) != "" && trim($issue_id) > 0) $sql_cond .= " and a.id=$issue_id";
	//echo $sql_cond;die;
	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	$sql = "SELECT a.id,a.booking_id,a.issue_date,a.issue_basis,a.issue_purpose,a.issue_number_prefix_num,a.issue_number,$year_field b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.product_name_details,c.lot,a.challan_no,c.current_stock,b.supplier_id,c.id as prod_id,b.dyeing_color_id,b.requisition_no,sum(b.cons_quantity) as issue_qnty,sum(b.return_qnty) as returnable_qnty,b.demand_id, d.supplier_name
	from inv_issue_master a, inv_transaction b left join  lib_supplier d on d.id = b.supplier_id, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and c.status_active=1 and b.item_category=1 and b.transaction_type=2 $sql_cond 
	group by a.id,a.booking_id,a.issue_date,a.issue_basis,a.issue_purpose,a.issue_number_prefix_num,a.issue_number,a.challan_no,a.insert_date,b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, b.supplier_id, c.id,b.dyeing_color_id,c.product_name_details,c.lot,c.current_stock,b.requisition_no,b.demand_id, d.supplier_name order by a.id desc";
	// echo $sql;// and c.current_stock>0
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	$arr = array(3 => $yarn_issue_purpose, 6 => $color_arr, 7 => $store_arr, 8 => $floor_room_rack_arr, 9 => $floor_room_rack_arr, 10 => $floor_room_rack_arr, 11 => $floor_room_rack_arr, 12 => $floor_room_rack_arr, 14 => $supplier_arr);

	echo create_list_view("list_view", "Issue No,Year,Issue Date,Issue Purpose,Item Name Details,Lot No,Dyeing Color,Requisition No,Store,Floor,Room,Rack,Shelf,Bin, Challan No, Supplier,Issue Qty, Returnable Qty", "70,55,75,105,180,100,80,80,90,80,80,80,80,80,90,55,55", "1590", "260", 0, $sql, "js_set_value", "challan_no,prod_id,id,requisition_no,demand_id,issue_basis,booking_id,store_id,floor_id,room,rack,self,bin_box,dyeing_color_id,issue_purpose", "", 1, "0,0,0,issue_purpose,0,0,dyeing_color_id,store_id,floor_id,room,rack,self,bin_box,0,0", $arr, "issue_number_prefix_num,year,issue_date,issue_purpose,product_name_details,lot,dyeing_color_id,requisition_no,store_id,floor_id,room,rack,self,bin_box,challan_no,supplier_name,issue_qnty,returnable_qnty", "", "", '0,0,3,0,0,0,0,0,0,0');
	exit();
}

if ($action == "populate_data_from_data") {
	$ex_data = explode("_", $data);

	//echo "<pre>";
	//print_r($ex_data);

	$challan_no = $ex_data[0];
	$prodID = $ex_data[1];
	$issueID = $ex_data[2];
	$requisition_no = $ex_data[3];
	$demand_id = $ex_data[4];
	$issueBasis = $ex_data[5];
	$booking_id = $ex_data[6];
	$store_name = $ex_data[7];
	$floor = $ex_data[8];
	$room = $ex_data[9];
	$rack = $ex_data[10];
	$shelf = $ex_data[11];
	$bin = $ex_data[12];
	$dyeing_color_id = $ex_data[13];
	$issue_purpose = $ex_data[14];
	$company = $ex_data[15];
	$location = $ex_data[16];
	$bookingWithoutOrder = $ex_data[17];

	$sqlCon = $requisition_cond  = "";
	if ($floor != "") {
		$sqlCon = " and b.floor_id=$floor";
	}
	if ($room != "") {
		$sqlCon .= " and b.room=$room";
	}
	if ($rack != "") {
		$sqlCon .= " and b.rack=$rack";
	}
	if ($shelf != "") {
		$sqlCon .= " and b.self=$shelf";
	}
	if ($bin != "") {
		$sqlCon .= " and b.bin_box=$bin";
	}
	// echo $sqlCon;die;

	if ($issueBasis == 1 && $booking_id != "") {
		$booking_id = $booking_id;

		if ($issue_purpose == 2 && (str_replace("'", "", $dyeing_color_id) != "")) {
			$dyeing_color_cond = " and b.dyeing_color_id=$dyeing_color_id";
		}
	} else if ($issueBasis == 3 && $requisition_no != "") {
		$booking_id = $requisition_no;
		$requisition_cond  = " and b.requisition_no=$requisition_no";
	} else if ($issueBasis == 8 && $demand_id != "") {
		$booking_id = $demand_id;
	}

	if ($issueBasis == 8 && $demand_id != "") {
		if ($requisition_no != "" && $requisition_no > 0) {
			$demand_requisition_cond = " and b.demand_id=$demand_id and b.requisition_no=$requisition_no";
		}
	}

	if ($db_type == 0) {
		$totalIssuedQty = return_field_value("sum(cons_quantity + IFNULL(cons_reject_qnty, 0)) as issue_qnty", "inv_transaction b", "b.prod_id=$prodID and b.item_category=1 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$issueID $demand_requisition_cond $dyeing_color_cond", "issue_qnty");
	} else {
		$totalIssuedQty = return_field_value("sum(cons_quantity + NVL(cons_reject_qnty, 0)) as issue_qnty", "inv_transaction b", "b.prod_id=$prodID and b.item_category=1 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$issueID $demand_requisition_cond $dyeing_color_cond", "issue_qnty");
	}

	if ($db_type == 0) {
		$return_sql = sql_select("select sum(b.cons_quantity+IFNULL(cons_reject_qnty, 0)) as return_qty from  inv_receive_master a,inv_transaction b where a.id=b.mst_id and b.issue_id=$issueID and b.prod_id=$prodID and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$booking_id and a.entry_form=9 $demand_requisition_cond");
	} else {
		$return_sql = sql_select("select sum(b.cons_quantity+NVL(cons_reject_qnty, 0)) as return_qty from  inv_receive_master a,inv_transaction b where a.id=b.mst_id and b.issue_id=$issueID and b.prod_id=$prodID and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$booking_id and a.entry_form=9 $dyeing_color_cond $demand_requisition_cond");
	}

	$sql = "SELECT a.id,a.issue_date,a.booking_no,a.booking_id, a.issue_purpose, a.issue_number_prefix_num, c.id as prod_id,c.supplier_id,c.product_name_details, c.lot,c.unit_of_measure,c.avg_rate_per_unit,sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, sum(b.return_qnty) as returnable_qnty, b.btb_lc_id,b.requisition_no
	from inv_issue_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.id='$issueID' and b.store_id=$store_name $sqlCon and c.id=$prodID and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $requisition_cond $demand_id_cond $demand_requisition_cond group by a.id,a.issue_date,a.booking_no,a.booking_id,a.issue_purpose,a.issue_number_prefix_num,c.id,c.supplier_id, c.product_name_details,c.lot,c.unit_of_measure,c.avg_rate_per_unit, b.btb_lc_id,b.requisition_no";

	//echo $sql;die();
	$res = sql_select($sql);
	$totalReturned = $netUsed = 0;
	foreach ($res as $row) {
		if ($row[csf("avg_rate_per_unit")] > 0) {
			$avg_rate = $row[csf("avg_rate_per_unit")];
		} else {
			$avg_rate = number_format($row[csf("cons_amount")] / $row[csf("cons_quantity")], 4, '.', '');
		}

		$product_name_details = str_replace(array("\r", "\n"), '', $row[csf("product_name_details")]);

		echo "$('#txt_item_description').val('" . $product_name_details . "');\n";
		echo "$('#txt_prod_id').val('" . $row[csf("prod_id")] . "');\n";
		echo "$('#txt_supplier_id').val('" . $row[csf("supplier_id")] . "');\n";
		echo "$('#txt_yarn_lot').val('" . $row[csf("lot")] . "');\n";
		echo "$('#txt_dyeing_color_id').val(" . $dyeing_color_id . ");\n";
		echo "$('#cbo_uom').val(" . $row[csf("unit_of_measure")] . ");\n";
		echo "$('#txt_rate').val(" . $avg_rate . ");\n";
		echo "$('#txt_issue_challan_no').val('" . $row[csf("issue_number_prefix_num")] . "');\n";
		echo "$('#hdn_req_no').val('" . $requisition_no . "');\n";
		echo "$('#txt_issue_id').val('" . $row[csf("id")] . "');\n";
		echo "$('#txt_issue_purpose').val('" . $row[csf("issue_purpose")] . "');\n";
		echo "$('#txt_requisition_no').val('" . $row[csf("requisition_no")] . "');\n";
		echo "$('#txt_issue_qnty').val('" . $totalIssuedQty . "');\n";
		echo "$('#txt_amount').val('" . $row[csf("cons_amount")] . "');\n";
		echo "$('#hide_issue_date').val('" . change_date_format($row[csf("issue_date")]) . "');\n";

		if ($issueBasis == 8 && $demand_id != "") {
			$requisition_no = $demand_id;
		}

		if ($issueBasis == 1) {
			$requisition_no = $booking_id;
		}

		$totalReturned = number_format($return_sql[0][csf('return_qty')], 2, ".", "");
		if ($totalReturned == "") $totalReturned = 0;

		echo "$('#txt_total_return').val('" . $totalReturned . "');\n";
		echo "$('#txt_total_return_display').val('" . $totalReturned . "');\n";
		$netUsed = number_format($totalIssuedQty - $totalReturned, 2, ".", "");
		echo "$('#txt_net_used').val('" . $netUsed . "');\n";

		$returnableBl = $row[csf("cons_quantity")] - $totalReturned;
		echo "$('#txt_returnable_qnty').val('" . number_format($row[csf("returnable_qnty")], 2, ".", "") . "');\n";
		echo "$('#txt_returnable_bl_qnty').val('" . number_format($returnableBl, 2, ".", "") . "');\n";
		echo "return_qnty_basis(" . $row[csf("issue_purpose")] . ");\n";

		if ($issueBasis == 1 && ($row[csf("issue_purpose")] == 2 || $row[csf("issue_purpose")] == 7 || $row[csf("issue_purpose")] == 12 || $row[csf("issue_purpose")] == 15 || $row[csf("issue_purpose")] == 38 || $row[csf("issue_purpose")] == 46 || $row[csf("issue_purpose")] == 50 || $row[csf("issue_purpose")] == 51)) {
			$wo_sql = sql_select("select ydw_no,booking_without_order,is_sales,entry_form from wo_yarn_dyeing_mst where id='" . $row[csf("booking_id")] . "' and status_active=1 and is_deleted=0");

			$booking_without_order = $wo_sql['0'][csf('booking_without_order')];
			$entry_form = $wo_sql['0'][csf('entry_form')];
			$isSalesOrder = $wo_sql['0'][csf('is_sales')];

			if ($issueBasis == 1 && ($entry_form == 42 || $entry_form == 114)) {
				echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
				echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
				echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
				echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
			} else if ($issueBasis == 1 && ($entry_form == 94 || $entry_form == 340)) {
				if ($booking_without_order == 2 && $isSalesOrder == 2) {
					echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
					echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
					echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
					echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
				} else {
					echo "$('#txt_return_qnty').attr('ondblclick','openmypage_po()');\n";
				}
			}
		} else if (($issueBasis == 3 || $issueBasis == 8) && $bookingWithoutOrder == 1) //for requisition basis sample without order
		{
			echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
			echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
			echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
			echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
		} else if ($issueBasis == 2) //for Independent Bsis
		{
			echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
			echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
			echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
			echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
		}

		if ($row[csf("btb_lc_id")] > 0) {
			$btb_lc_num = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $row[csf("btb_lc_id")] . "'", "lc_number");
			echo "$('#txt_btb').val('" . $btb_lc_num . "');\n";
		}
	}

	echo "load_room_rack_self_bin('requires/yarn_issue_return_controller*1', 'store','store_td', '" . $company . "','" . '' . "',this.value);\n";
	echo "$('#cbo_store_name').val(" . $store_name . ");\n";

	echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'floor','floor_td', '" . $company . "','" . $location . "','" . $store_name . "',this.value);\n";
	echo "$('#cbo_floor').val('" . $floor . "');\n";

	echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'room','room_td', '" . $company . "','" . $location . "','" . $store_name . "','" . $floor . "',this.value);\n";
	echo "$('#cbo_room').val('" . $room . "');\n";
	echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'rack','rack_td', '" . $company . "','" . $location . "','" . $store_name . "','" . $floor . "','" . $room . "',this.value);\n";
	echo "$('#txt_rack').val('" . $rack . "');\n";
	echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'shelf','shelf_td', '" . $company . "','" . $location . "','" . $store_name . "','" . $floor . "','" . $room . "','" . $rack . "',this.value);\n";
	echo "$('#txt_shelf').val('" . $shelf . "');\n";
	echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'bin','bin_td', '" . $company . "','" . $location . "','" . $store_name . "','" . $floor . "','" . $room . "','" . $rack . "','" . $shelf . "',this.value);\n";
	echo "$('#cbo_bin').val('" . $bin . "');\n";
	echo "storeUpdateUptoDisable();\n";
	exit();
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$txt_bag = (str_replace("'", "", $txt_bag) == "") ? 0 : $txt_bag;
	$txt_cone = (str_replace("'", "", $txt_cone) == "") ? 0 : $txt_cone;
	$txt_processloss_qnty = (str_replace("'", "", $txt_processloss_qnty) == "") ? 0 : $txt_processloss_qnty;

	$variable_store_wise_rate = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate != 1) $variable_store_wise_rate = 2;

	$is_update_cond = ($operation == 0) ? "" : " and id <> $update_id ";
	$store_wise_cond = ($variable_store_wise_rate == 1) ? " and store_id=$cbo_store_name" : "";
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id $store_wise_cond  $is_update_cond  and status_active = 1", "max_date");

	/* if($max_transaction_date != "")
	{
		$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
		if ($receive_date < $max_transaction_date)
		{
			echo "20**Return Date Can not Be Less Than Last Transaction Date Of This Lot";
			disconnect($con);die;
		}
	} */

	$sql_condition = '';
	if (str_replace("'", "", $cbo_basis) == 1) {
		$sql_condition = "and a.booking_no=$txt_booking_no and a.booking_id=$txt_booking_id";

		if ((str_replace("'", "", $txt_dyeing_color_id)) != "") {
			$sql_condition .= "and b.dyeing_color_id=$txt_dyeing_color_id";
		}
	} else if (str_replace("'", "", $cbo_basis) == 3) {
		$sql_condition = "and b.requisition_no=$txt_booking_no";
	} else if (str_replace("'", "", $cbo_basis) == 8) {
		$sql_condition = "and b.requisition_no=$hdn_req_no and b.demand_id=$txt_booking_id";
	}

	$sql_issue = "select a.id as issue_id,a.booking_no,a.issue_basis,a.issue_purpose,c.trans_id,c.po_breakdown_id,b.prod_id ,b.cons_quantity as issue_qnty, c.quantity as order_wuse_issue_qnty,b.dyeing_color_id from inv_issue_master a,inv_transaction b  left join order_wise_pro_details c on b.id=c.trans_id and b.prod_id=c.prod_id and c.trans_type=2 where a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 $sql_condition";

	//echo "13**".$sql_issue; die();

	$sqlissue_result = sql_select($sql_issue);

	$order_wise_issue = array();
	$total_issue_Qty = 0;

	$transissueIdChk = array();

	foreach ($sqlissue_result as $issue_row) {
		$issue_purpose = $issue_row[csf("issue_purpose")];
		$issue_basis = $issue_row[csf("issue_basis")];

		//if( $issue_row[csf("issue_id")] == str_replace("'","",$txt_issue_id) ) // omit and observe 
		//{
		if ($issue_row[csf("po_breakdown_id")] != "") {
			$order_wise_issue[$issue_row[csf("po_breakdown_id")]][$issue_row[csf("prod_id")]]['issue_qty'] += $issue_row[csf("order_wuse_issue_qnty")];
		}

		if ($transissueIdChk[$issue_row[csf("trans_id")]] == "") {
			$transissueIdChk[$issue_row[csf("trans_id")]] = $issue_row[csf("trans_id")];
			$total_issue_Qty += $issue_row[csf("issue_qnty")];

			if ($issue_row[csf("issue_basis")] == 1) {
				if ((str_replace("'", "", $txt_dyeing_color_id) > 0) && (str_replace("'", "", $txt_dyeing_color_id) != "")) {
					$total_wo_issue_qty[$issue_row[csf("booking_no")]][$issue_row[csf("dyeing_color_id")]] += $issue_row[csf("issue_qnty")];
				} else {
					$total_wo_issue_qty[$issue_row[csf("booking_no")]][$issue_row[csf("dyeing_color_id")]][$issue_row[csf("prod_id")]] += $issue_row[csf("issue_qnty")];
				}
			}
		}
		//}

	}

	if ($operation == 0) {
		$sqlrtn = "select c.trans_id,c.po_breakdown_id,c.prod_id, sum(b.cons_quantity) as issue_rtn_qty,  sum(c.quantity+coalesce(c.reject_qty, 0)) as order_wise_return_qty from inv_receive_master a,inv_transaction b left join order_wise_pro_details c on  b.id=c.trans_id and b.prod_id=c.prod_id and c.trans_type=4 and c.entry_form=9 and c.status_active=1 and c.is_deleted=0 where a.id=b.mst_id and b.issue_id=$txt_issue_id and b.prod_id=$txt_prod_id and b.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_condition group by c.trans_id,c.po_breakdown_id,c.prod_id";

		//echo "13**".$sqlrtn; die();

		$sqlrtn_result = sql_select($sqlrtn);
		$transissueRtnIdChk = array();
		$order_wise_return = array();
		$return_qnty = 0;
		foreach ($sqlrtn_result as $rtn_row) {

			if ($rtn_row[csf("po_breakdown_id")] != "") {
				$order_wise_return[$rtn_row[csf("po_breakdown_id")]][$rtn_row[csf("prod_id")]]['return_qty'] += $rtn_row[csf("order_wise_return_qty")];
			}

			if ($transissueRtnIdChk[$rtn_row[csf("trans_id")]] == "") {
				$transissueRtnIdChk[$rtn_row[csf("trans_id")]] = $rtn_row[csf("trans_id")];
				$return_qnty += $rtn_row[csf("issue_rtn_qty")];
			}
		}

		$total_return_qnty = str_replace("'", "", $txt_return_qnty) + $return_qnty;
	} else {
		$sqlrtn = "select c.trans_id,c.po_breakdown_id,c.prod_id, sum(b.cons_quantity) as issue_rtn_qty,  sum(c.quantity+coalesce(c.reject_qty, 0)) as order_wise_return_qty from inv_receive_master a,inv_transaction b left join order_wise_pro_details c on  b.id=c.trans_id and b.prod_id=c.prod_id and c.trans_type=4 and c.entry_form=9 and c.status_active=1 and c.is_deleted=0 where a.id=b.mst_id and a.id!=$txt_mst_id and b.issue_id=$txt_issue_id  and b.prod_id=$txt_prod_id and b.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_condition  group by c.trans_id,c.po_breakdown_id,c.prod_id";

		//echo "31**".$sqlrtn;
		//die();
		$sqlrtn_result = sql_select($sqlrtn);

		$order_wise_return = array();
		$return_qnty = 0;
		$transissueRtnIdChk = array();
		foreach ($sqlrtn_result as $rtn_row) {
			if ($rtn_row[csf("po_breakdown_id")] != "") {
				$order_wise_return[$rtn_row[csf("po_breakdown_id")]][$rtn_row[csf("prod_id")]]['return_qty'] += $rtn_row[csf("order_wise_return_qty")];
			}

			if ($transissueRtnIdChk[$rtn_row[csf("trans_id")]] == "") {
				$transissueRtnIdChk[$rtn_row[csf("trans_id")]] = $rtn_row[csf("trans_id")];
				$return_qnty += $rtn_row[csf("issue_rtn_qty")];
			}
		}

		$total_return_qnty = str_replace("'", "", $txt_return_qnty) + $return_qnty;
	}

	if ((str_replace("'", "", $cbo_basis) == 1) && ($issue_purpose == 2 || $issue_purpose == 7 || $issue_purpose == 12 || $issue_purpose == 15 || $issue_purpose == 38  || $issue_purpose == 44 || $issue_purpose == 46 || $issue_purpose == 50 || $issue_purpose == 51)) // work order basis
	{
		$txt_dyeing_color_id = str_replace("'", "", $txt_dyeing_color_id);
		$txtProdId = str_replace("'", "", $txt_prod_id);
		$dyeing_color_cond =  (($txt_dyeing_color_id > 0) && ($txt_dyeing_color_id != "")) ? " AND b.dyeing_color_id=$txt_dyeing_color_id " : "";

		if ($operation == 0) {
			$receive_sql = "select distinct(b.id) as trans_id,a.id as receieved_id, a.recv_number_prefix_num,a.booking_no,(case when b.transaction_type = 1 and c.grey_prod_id like('%" . $txtProdId . "%') then b.cons_quantity else 0 end) as receive_qty,(case when b.transaction_type = 4  then b.cons_quantity else 0 end) as issue_rtn_rcvqty,b.dyeing_color_id,b.prod_id,b.transaction_type from inv_receive_master a, inv_transaction b left join order_wise_pro_details c on b.id = c.trans_id AND b.prod_id=c.prod_id where a.id=b.mst_id and a.receive_basis in (1,2) and b.item_category=1 and b.transaction_type in (1,4) and a.booking_no=$txt_booking_no and a.booking_id=$txt_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dyeing_color_cond order by a.recv_number_prefix_num"; //and a.issue_id=$txt_issue_id : // omit and observe 

		} else {
			$receive_sql = "SELECT distinct(b.id) as trans_id,a.id as receieved_id,a.recv_number_prefix_num, a.booking_no, (CASE WHEN b.transaction_type = 1 and c.grey_prod_id like('%" . $txtProdId . "%') THEN b.cons_quantity ELSE 0 END) AS receive_qty, (CASE WHEN b.transaction_type = 4 AND  THEN b.cons_quantity ELSE 0 END) AS issue_rtn_rcvqty, b.dyeing_color_id, b.prod_id, b.transaction_type FROM inv_receive_master a, inv_transaction b left join order_wise_pro_details c on b.id = c.trans_id AND b.prod_id=c.prod_id WHERE a.id = b.mst_id AND a.receive_basis IN (1, 2) AND b.item_category = 1 AND b.transaction_type IN (1, 4) AND a.booking_no = =$txt_booking_no AND a.booking_id = $txt_booking_id AND a.id != $txt_mst_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 $dyeing_color_cond ORDER BY a.recv_number_prefix_num"; //a.issue_id = $txt_issue_id  : // omit and observe 
		}

		//echo "10**". $receive_sql; die();

		$rcvData = sql_select($receive_sql);
		$rcvMrrData = array();
		$total_wo_rcvqty = array();
		foreach ($rcvData as  $row) {
			if ($row[csf('transaction_type')] == 1) {
				$rcvMrrData[$row[csf('recv_number_prefix_num')]] = $row[csf('receive_qty')];

				if ((str_replace("'", "", $txt_dyeing_color_id) > 0) && (str_replace("'", "", $txt_dyeing_color_id) != "")) {
					$total_wo_rcvqty[$row[csf('booking_no')]][$row[csf('dyeing_color_id')]] += $row[csf('receive_qty')];
				} else {
					$total_wo_rcvqty[$row[csf('booking_no')]][$row[csf('dyeing_color_id')]][$row[csf('prod_id')]] += $row[csf('receive_qty')];
				}


				$wo_rcv_dyed_yarn_prod_id_arr[$row[csf('booking_no')]][$row[csf('dyeing_color_id')]] = $row[csf('prod_id')];
				$receieved_ids .= $row[csf('receieved_id')] . ",";
				$receieved_dyed_prod_id = $row[csf('prod_id')];
			} else {
				$issueRtnRcvMrrData[$row[csf('recv_number_prefix_num')]] = $row[csf('issue_rtn_rcvqty')];

				if ((str_replace("'", "", $txt_dyeing_color_id) > 0) && (str_replace("'", "", $txt_dyeing_color_id) != "")) {
					$total_wo_issue_rtn_rcvqty[$row[csf('booking_no')]][$row[csf('dyeing_color_id')]] += $row[csf('issue_rtn_rcvqty')];
				} else {
					$total_wo_issue_rtn_rcvqty[$row[csf('booking_no')]][$row[csf('dyeing_color_id')]][$row[csf('prod_id')]] += $row[csf('issue_rtn_rcvqty')];
				}
			}
		}

		$receieved_ids = chop($receieved_ids, ",");

		if ($receieved_ids != "") {
			$receive_rtn_sql = "SELECT b.prod_id,b.cons_quantity FROM inv_issue_master  a, inv_transaction b  WHERE  a.id=b.mst_id  AND a.item_category = 1  AND (a.received_id IN ($receieved_dyed_prod_id) OR a.received_id IN (SELECT a.received_id AS issure_rtn_rcv_id FROM inv_mrr_wise_issue_details  c, inv_transaction b, inv_issue_master  a WHERE c.issue_trans_id = b.id AND b.mst_id = a.id AND b.prod_id = c.prod_id AND c.entry_form = 8 AND a.item_category = 1 AND b.prod_id IN ($receieved_dyed_prod_id))) AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form = 8 AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active = 1 AND b.is_deleted = 0";
			$rcvRtnData = sql_select($receive_rtn_sql);

			foreach ($rcvRtnData as  $row) {
				$total_wo_rcv_return_qty_arr[$row[csf('prod_id')]] += $row[csf('cons_quantity')];
			}
		}
	}

	if ($operation == 0 || $operation == 1) {
		$save_string = explode(",", str_replace("'", "", $save_data));
		$prodId = str_replace("'", "", $txt_prod_id);
		$po_array = array();

		for ($i = 0; $i < count($save_string); $i++) {
			$order_dtls = explode("**", $save_string[$i]);
			$order_id = $order_dtls[0];
			$order_qnty = $order_dtls[1];
			$po_array[$order_id] = $order_qnty;
		}

		// order wise issue return validate with order wise issue
		if (!empty($po_array)) {
			foreach ($po_array as $po_id => $rtnQty) {
				$po_issue_qty = $order_wise_issue[$po_id][$prodId]['issue_qty'] * 1;
				$po_rtn_balance = ($po_issue_qty - $order_wise_return[$po_id][$prodId]['return_qty']) * 1;

				if (number_format($po_rtn_balance, 2, '.', '') < number_format($po_array[$po_id], 2, '.', '')) {
					$msg = "Issue return quantity can not be greater than issue quantity of this order\nIssue quantity of this order = " . $po_issue_qty . "\nReturnable balance of this order = $po_rtn_balance\nTotal Issue = " . $total_issue_Qty . "\nTotal Issue Return = " . $total_return_qnty . "";

					echo "31**" . $msg;
					disconnect($con);
					exit();
				}
			}
		}

		//echo "31**test"; die();
		//echo "31**".$total_return_qnty.">".$total_issue_Qty."test"; die();
		$total_return_qnty = number_format($total_return_qnty, 2, ".", "") * 1;
		$total_issue_Qty = number_format($total_issue_Qty, 2, ".", "") * 1;

		if ($total_return_qnty > $total_issue_Qty) {
			$now_rtn = str_replace("'", "", $txt_return_qnty);
			$total_previous_rtn = ($total_return_qnty - $now_rtn);

			echo "31**Return quantity can not be greater than Issue Quantity.\nIssue quantity = $total_issue_Qty\nReturn quantity=$total_previous_rtn";
			disconnect($con);
			die();
		}

		if ((str_replace("'", "", $cbo_basis) == 1) && ($issue_purpose == 2 || $issue_purpose == 7 || $issue_purpose == 12 || $issue_purpose == 15 || $issue_purpose == 38  || $issue_purpose == 44 || $issue_purpose == 46 || $issue_purpose == 50 || $issue_purpose == 51)) // work order basis
		{
			$booking_no = str_replace("'", "", $txt_booking_no);
			$txt_dyeing_color_id = str_replace("'", "", $txt_dyeing_color_id);
			$prod_id = str_replace("'", "", $txt_prod_id);
			$wo_dyed_yarn_rcv_prod_id = $wo_rcv_dyed_yarn_prod_id_arr[$booking_no][$txt_dyeing_color_id];

			if (($txt_dyeing_color_id > 0) && ($txt_dyeing_color_id != "")) {
				$total_wo_clr_issue = $total_wo_issue_qty[$booking_no][$txt_dyeing_color_id];
				$total_wo_clr_rcv = ($total_wo_rcvqty[$booking_no][$txt_dyeing_color_id] - $total_wo_rcv_return_qty_arr[$wo_dyed_yarn_rcv_prod_id]);
				$total_wo_clr_issue_rtn_rcv = $total_wo_issue_rtn_rcvqty[$booking_no][$txt_dyeing_color_id];
			} else {
				$total_wo_clr_issue = $total_wo_issue_qty[$booking_no][$txt_dyeing_color_id][$prod_id];
				$total_wo_clr_rcv = ($total_wo_rcvqty[$booking_no][$txt_dyeing_color_id][$wo_dyed_yarn_rcv_prod_id] - $total_wo_rcv_return_qty_arr[$wo_dyed_yarn_rcv_prod_id]);
				$total_wo_clr_issue_rtn_rcv = $total_wo_issue_rtn_rcvqty[$booking_no][$txt_dyeing_color_id][$prod_id];
			}

			$total_wo_rcv = ($total_wo_clr_rcv + $total_wo_clr_issue_rtn_rcv);
			$allowedRtnQty = ($total_wo_clr_issue - $total_wo_clr_issue_rtn_rcv - $total_wo_clr_rcv);
			$actualIssueQty = $total_wo_clr_issue - $total_wo_clr_issue_rtn_rcv;
			$currentRtn = str_replace("'", "", $txt_return_qnty);
			//echo "31**hh=$allowedRtnQty=".$total_wo_clr_issue."-".$return_qnty."-".$total_wo_clr_rcv; die();
			//echo "31**".$total_issue_Qty."-".$return_qnty."-".$total_wo_clr_rcv; die();
			//echo "31**rcvqty= $total_wo_clr_rcv "."balance= $allowedRtnQty > $currentrtn"; die();
			if (($total_wo_rcv > 0) && ($currentRtn > $allowedRtnQty)) {
				$rcvNum = implode(",", array_keys($rcvMrrData));
				$IssueRtnRcvNum = implode(",", array_keys($issueRtnRcvMrrData));
				if ($IssueRtnRcvNum != "") {
					$IssueRtnMsg = "Issue Return No=$IssueRtnRcvNum\nIssue Return Quantity=$total_wo_clr_issue_rtn_rcv";
				}

				echo "31**Receive Found\nReceive No=$rcvNum\nNet receive quantity=$total_wo_clr_rcv\n$IssueRtnMsg\nIssue Quantity=$actualIssueQty\nAllowed Quantity=$allowedRtnQty";
				disconnect($con);
				die();
			}
		}

		$production_sql = "SELECT LISTAGG(cast(a.recv_number_prefix_num as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.recv_number_prefix_num) as production_no,SUM (b.grey_receive_qnty) AS production_qty FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE  a.id = b.mst_id AND a.item_category = 13 AND a.booking_id in (SELECT booking_id FROM inv_issue_master c, inv_transaction d WHERE  c.id = d.mst_id AND d.prod_id = $txt_prod_id AND d.mst_id = $txt_issue_id AND (   d.requisition_no = $txt_booking_id OR d.requisition_no = $hdn_req_no or c.booking_id =$txt_booking_id) AND d.receive_basis IN (1,3, 8) AND d.transaction_type = 2 AND d.item_category = 1 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 ) AND a.receive_basis in (1,2) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";

		//echo "31**".$production_sql; die();
		$production_qty = 0;
		$product_sql_rslt = sql_select($production_sql);
		$production_qty = number_format($product_sql_rslt[0][csf('production_qty')], 2, '.', '') * 1;
		$net_issue_qty = ($total_issue_Qty - $total_return_qnty);
		//echo "31**$total_issue_Qty==$total_return_qnty<$production_qty"; die();

		/* if( $production_qty>0  && ($net_issue_qty<$production_qty) ) // omit temporary
		{
			$production_no = $product_sql_rslt[0][csf('production_no')];
			$now_rtn = str_replace("'", "", $txt_return_qnty);
	    	$total_previous_rtn = ($total_return_qnty-$now_rtn);

			echo "31**Knitting Production Found.\nNet issue quantity can not be less than Production Quantity.\nNet Issue Quantity=$net_issue_qty\nProduction No=$production_no\nProduction quantity = $production_qty\nPrevious Return quantity=$total_previous_rtn";
			disconnect($con);
			die();
		} */
	}

	$cbo_bin = (str_replace("'", "", $cbo_bin) == "") ? 0 : $cbo_bin;
	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=18 and item_category_id = 1");
	$variable_set_smn_allocation = return_field_value("smn_allocation", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=18 and item_category_id = 1", "smn_allocation");

	$txt_return_qnty = str_replace("'", "", $txt_return_qnty);
	$txt_return_value = str_replace("'", "", $txt_amount);
	$cbo_basis = str_replace("'", "", $cbo_basis);
	$cbo_issue_purpose = str_replace("'", "", $cbo_issue_purpose);
	$wo_entry_form = str_replace("'", "", $txt_wo_entry_form);
	$booking_without_order = str_replace("'", "", $booking_without_order);
	$txt_dyeing_color_id = str_replace("'", "", $txt_dyeing_color_id);
	$txt_rate = str_replace("'", "", $txt_rate);
	$txt_amount = str_replace("'", "", $txt_amount);

	if ($booking_without_order == "") $booking_without_order = 0;
	if ($txt_return_qnty == "") $txt_return_qnty = 0;
	if ($txt_amount == "") $txt_amount = 0;
	if ($txt_return_qnty == "" || $txt_return_qnty == 0) $txt_amount = 0;

	//######### this stock item store level and calculate rate ########//
	$update_conds = "";
	if (str_replace("'", "", $update_id) > 0) $update_conds = " and a.id <> $update_id";
	$store_stock_sql = "select b.ID, b.AVG_RATE_PER_UNIT, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when a.transaction_type in(1,4,5) then a.store_amount else 0 end)-(case when a.transaction_type in(2,3,6) then a.store_amount else 0 end)) as BALANCE_AMT 
	from inv_transaction a, product_details_master b 
	where a.prod_id=b.id and a.status_active=1 and a.prod_id=$txt_prod_id and a.store_id=$cbo_store_name $update_conds
	group by b.ID, b.AVG_RATE_PER_UNIT";
	//echo "20**$store_stock_sql";disconnect($con);die;
	$store_stock_sql_result = sql_select($store_stock_sql);
	$store_item_rate = 0;
	if ($store_stock_sql_result[0]["BALANCE_AMT"] != 0 && $store_stock_sql_result[0]["BALANCE_STOCK"] != 0) {
		$store_item_rate = $store_stock_sql_result[0]["BALANCE_AMT"] / $store_stock_sql_result[0]["BALANCE_STOCK"];
	} else {
		$store_item_rate = $store_stock_sql_result[0]["AVG_RATE_PER_UNIT"];
	}
	$issue_store_value = ($txt_return_qnty * $store_item_rate);

	if ($operation == 0) // Insert Here
	{
		//---------------Check Duplicate product in Same return number ------------------------//
		$dyeing_color_cond = ($cbo_basis == 1 && $cbo_issue_purpose == 2) ? " and b.dyeing_color_id = $txt_dyeing_color_id " : "";
		$duplicate = is_duplicate_field("b.id", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and a.recv_number=$txt_return_no and b.prod_id=$txt_prod_id and b.issue_id=$txt_issue_id and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 $dyeing_color_cond");

		if ($duplicate == 1) {
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		//------------------------------Check Brand END---------------------------------------//

		//adjust product master table START-------------------------------------//

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,allocated_qnty,available_qnty,dyed_type from product_details_master where id=$txt_prod_id");
		$presentStock = $presentStockValue = $presentAvgRate = $allocated_qnty = $available_qnty = $allocated_qnty_balance = $available_qnty_balance = 0;
		foreach ($sql as $result) {
			$presentStock		= $result[csf("current_stock")];
			$presentStockValue	= $result[csf("stock_value")];
			$presentAvgRate		= $result[csf("avg_rate_per_unit")];
			$allocated_qnty		= $result[csf("allocated_qnty")];
			$available_qnty		= $result[csf("available_qnty")];
			$dyed_type			= $result[csf("dyed_type")];
		}

		$cbo_adjust_to = str_replace("'", "", $cbo_adjust_to); //1=>"Allocation Quantity",2=>"Available Quantity
		// if yarn allocation variable set to yes
		if ($variable_set_allocation == 1) {
			if (($cbo_basis == 3 || $cbo_basis == 8) && ($issue_purpose == 1 || $issue_purpose == 4)) {

				$plan_sql = "select a.booking_no,c.job_no,a.is_sales from ppl_yarn_requisition_entry b,ppl_planning_entry_plan_dtls a,wo_booking_dtls c where b.knit_id=a.dtls_id and a.booking_no=c.booking_no and b.status_active=1 and b.requisition_no=$txt_booking_no and b.prod_id=$txt_prod_id group by a.booking_no,c.job_no,a.is_sales"; // ommit :and a.status_active=1

				$planData = sql_select($plan_sql);
				$job_no = $planData[0][csf("job_no")];
				$booking_no = $planData[0][csf("booking_no")];

				$requisition_no_cond = ($cbo_basis == 3) ? " and a.requisition_no=$txt_booking_no" : " and a.requisition_no=$hdn_req_no";

				$is_sales_order = sql_select("select b.is_sales from ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b where a.knit_id=b.dtls_id and a.prod_id=$txt_prod_id $requisition_no_cond");

				$allocated_qnty_balance = $allocated_qnty + $txt_return_qnty;
				$available_qnty_balance = $available_qnty;
			} else if ($cbo_basis == 1 && ($issue_purpose == 2 || $issue_purpose == 7 || $issue_purpose == 12 || $issue_purpose == 15 || $issue_purpose == 38 || $issue_purpose == 44 || $issue_purpose == 46 || $issue_purpose == 50 || $issue_purpose == 51)) {

				$is_sales_order = sql_select("select a.is_sales,b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no=$txt_booking_no and a.id=$txt_booking_id and b.product_id=$txt_prod_id and a.company_id=$cbo_company_id");

				$job_no 	= $is_sales_order[0][csf("job_no")];
				$is_sales 	= $is_sales_order[0][csf("is_sales")];
				$booking_no = '';

				if ($wo_entry_form == 42 || $wo_entry_form == 114) // Without Order
				{
					if ($variable_set_smn_allocation == 1) {
						$allocated_qnty_balance = $allocated_qnty + $txt_return_qnty;
						$available_qnty_balance = $available_qnty;
					} else {
						$allocated_qnty_balance = $allocated_qnty;
						$available_qnty_balance = $available_qnty + $txt_return_qnty;
					}
				} else if ($wo_entry_form == 94 || $wo_entry_form == 340) // services wo
				{

					if ($booking_without_order == 0 || $is_sales == 1)  // with order
					{
						$allocated_qnty_balance = $allocated_qnty + $txt_return_qnty;
						$available_qnty_balance = $available_qnty;
					} else  // without order
					{
						/*
						if($variable_set_smn_allocation==1)
						{
							$allocated_qnty_balance = $allocated_qnty + $txt_return_qnty;
							$available_qnty_balance = $available_qnty;
						}
						else
						{*/
						$allocated_qnty_balance = $allocated_qnty;
						$available_qnty_balance = $available_qnty + $txt_return_qnty;
						//}
					}
				} else // 41,125,135
				{
					$allocated_qnty_balance = $allocated_qnty + $txt_return_qnty;
					$available_qnty_balance = $available_qnty;
				}
			} else {
				$allocated_qnty_balance = $allocated_qnty;
				$available_qnty_balance = $available_qnty + $txt_return_qnty;
			}

			if (str_replace("'", "", $save_data) != "") {

				$save_string = explode(",", str_replace("'", "", $save_data));
				$save_string_pre = explode(",", str_replace("'", "", $save_data_pre));
				$po_array = array();
				for ($i = 0; $i < count($save_string); $i++) {
					$order_dtls = explode("**", $save_string[$i]);
					$order_id = $order_dtls[0];
					$order_qnty = $order_dtls[1];
					$po_array[$order_id] = $order_qnty;
					$po_id = $order_id . ",";
				}

				$pre_po_array = array();
				for ($j = 0; $j < count($save_string_pre); $j++) {
					$order_dtls = explode("**", $save_string_pre[$j]);
					$order_id = $order_dtls[0];
					$order_qnty = $order_dtls[1];
					$pre_po_array[$order_id] = $order_qnty;
				}
				$po_id = rtrim($po_id, ", ");
				//echo "10**";
				$booking_cond = ($booking_no != "") ? " and a.booking_no='$booking_no' " : "";
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $txt_prod_id) . " and a.job_no='$job_no' $booking_cond and a.status_active=1 and a.is_deleted=0";

				$check_allocation_array = sql_select($sql_allocation);

				if (str_replace("'", "", $cbo_adjust_to) == 2) {
					// if allocation found
					if (!empty($check_allocation_array)) {

						$mst_id = $check_allocation_array[0][csf('id')];
						$qnty_break_down_str = explode(",", $check_allocation_array[0][csf('qnty_break_down')]);

						foreach ($po_array as $key => $val) {
							$allo_qnty = $val;
							execute_query("update inv_material_allocation_dtls set qnty=(qnty-$allo_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id=$key and item_id = $txt_prod_id", 0);
							$po_wise_allocation[$key] = $val;
						}

						$qnty_break_down_str_new = "";
						foreach ($qnty_break_down_str as $qnty_break_down_row) {
							$qnty_break_down_row_info = explode("_", $qnty_break_down_row);
							$allo_qnty = $po_wise_allocation[$qnty_break_down_row_info[1]];
							$qnty_break_down_str_new = ($qnty_break_down_row_info[0] - $allo_qnty) . "_" . $qnty_break_down_row_info[1] . "_" . $qnty_break_down_row_info[2] . ",";
							$mst_qnty += $allo_qnty;
						}

						$qnty_break_down_str_new = rtrim($qnty_break_down_str_new, ", ");
						execute_query("update inv_material_allocation_mst set qnty=(qnty-$mst_qnty),qnty_break_down='$qnty_break_down_str_new',updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
					}
				}
			}
		} else {
			$allocated_qnty_balance = $allocated_qnty;
			$available_qnty_balance = $available_qnty + $txt_return_qnty;
		}

		if ($cbo_basis == 4) {
			$is_sales_order[0][csf("is_sales")] = 1;
		}

		$nowStock 		= $presentStock + $txt_return_qnty;
		$nowStockValue 	= $presentStockValue + $txt_return_value;
		$nowAvgRate		= trim($nowStockValue / $nowStock);
		$nowAvgRate		= number_format($nowAvgRate, 10, ".", "");

		$field_array_prod = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = $txt_return_qnty . "*" . $nowStock . "*'" . $nowAvgRate . "'*" . number_format($nowStockValue, 8, ".", "") . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		//adjust product master table END  -------------------------------------//

		//yarn master table entry here START---------------------------------------//
		//$currency=array(1=>"Taka",2=>"USD",3=>"EURO");
		if (str_replace("'", "", $txt_return_no) == "") {
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con, 1, $cbo_company_id, 'YIR', 9, date("Y", time())));

			$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_basis, receive_date, booking_id, booking_no, booking_without_order, knitting_source, knitting_company, challan_no, store_id,floor,room,rack,shelf,bin, location_id, exchange_rate, currency_id, supplier_id, issue_id, remarks, inserted_by, insert_date,requisition_no,no_bag,no_cone";
			$data_array = "(" . $id . ",'" . $new_recv_number[1] . "','" . $new_recv_number[2] . "','" . $new_recv_number[0] . "',9,1," . $cbo_company_id . "," . $cbo_basis . "," . $txt_return_date . "," . $txt_booking_id . "," . $txt_booking_no . ",'" . $booking_without_order . "'," . $cbo_knitting_source . "," . $cbo_knitting_company . "," . $txt_return_challan_no . "," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_location . ",1,1," . $txt_supplier_id . "," . $txt_issue_id . "," . $txt_remarks . ",'" . $user_id . "','" . $pc_date_time . "'," . $hdn_req_no . "," . $txt_bag . "," . $txt_cone . ")";
		} else {
			$new_recv_number[0] = str_replace("'", "", $txt_return_no);
			$id = str_replace("'", "", $txt_mst_id);
			$field_array = "entry_form*item_category*company_id*receive_basis*receive_date*booking_id*booking_no*booking_without_order*knitting_source*knitting_company*challan_no*store_id*floor*room*rack*shelf*bin*location_id*exchange_rate*currency_id*supplier_id*issue_id*remarks*updated_by*update_date*requisition_no*no_bag*no_cone";
			$data_array = "9*1*" . $cbo_company_id . "*" . $cbo_basis . "*" . $txt_return_date . "*" . $txt_booking_id . "*" . $txt_booking_no . "*" . $booking_without_order . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . $txt_return_challan_no . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_location . "*1*1*" . $txt_supplier_id . "*" . $txt_issue_id . "*" . $txt_remarks . "*'" . $user_id . "'*'" . $pc_date_time . "'*" . $hdn_req_no . "*" . $txt_bag . "*" . $txt_cone;

			//echo $data_array;
		}
		//yarn master table entry here END---------------------------------------//

		/******** original product id check start ********/
		$origin_prod_id = return_field_value("origin_prod_id", "inv_transaction", "prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_issue_id and transaction_type in (2) and item_category=1", "origin_prod_id");

		/******** original product id check end ********/

		//transaction table insert here START--------------------------------//
		$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$field_array_trans = "id,mst_id,receive_basis,company_id,supplier_id,prod_id,origin_prod_id,item_category,transaction_type,transaction_date,store_id,floor_id,room,rack,self,bin_box,order_uom,order_qnty,order_rate,order_amount,cons_uom,cons_quantity,cons_reject_qnty,cons_rate,cons_amount,balance_qnty,balance_amount,issue_challan_no,issue_id,remarks,no_bag,no_cone,dyeing_color_id,requisition_no,demand_id,inserted_by,insert_date,store_rate,store_amount,no_of_qty";
		$data_array_trans = "(" . $dtlsid . "," . $id . "," . $cbo_basis . "," . $cbo_company_id . "," . $txt_supplier_id . "," . $txt_prod_id . ",'" . $origin_prod_id . "',1,4," . $txt_return_date . "," . $cbo_store_name . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_uom . "," . $txt_return_qnty . "," . number_format($txt_rate, 10, ".", "") . "," . number_format($txt_amount, 8, ".", "") . "," . $cbo_uom . "," . $txt_return_qnty . "," . $txt_reject_qnty . "," . number_format($txt_rate, 10, ".", "") . "," . number_format($txt_amount, 8, ".", "") . "," . $txt_return_qnty . "," . number_format($txt_amount, 10, ".", "") . "," . $txt_issue_challan_no . "," . $txt_issue_id . "," . $txt_remarks . "," . $txt_bag . "," . $txt_cone . "," . $txt_dyeing_color_id . "," . $hdn_req_no . "," . $txt_booking_id . ",'" . $user_id . "','" . $pc_date_time . "'," . number_format($store_item_rate, 10, '.', '') . "," . number_format($issue_store_value, 8, '.', '') . "," . $txt_processloss_qnty . ")";
		//transaction table insert here END ---------------------------------//

		$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,reject_qty,issue_purpose,inserted_by,insert_date,is_sales,processloss_qty";
		$proportQ = true;
		if (str_replace("'", "", $save_data) != "") {
			//order_wise_pro_details table data insert START-----//
			$save_string = explode(",", str_replace("'", "", $save_data));
			$po_array = array();
			for ($i = 0; $i < count($save_string); $i++) {
				$order_dtls = explode("**", $save_string[$i]);
				$order_id = $order_dtls[0];
				$order_qnty = $order_dtls[1];
				$order_reject_qnty = $order_dtls[2];
				$order_sales_status = $order_dtls[3];
				$con_processloss_qty = $order_dtls[4];
				$po_array[$order_id] = $order_qnty . "##" . $order_reject_qnty . "##" . $order_sales_status . "##" . $con_processloss_qty;
			}

			$i = 0;
			$id_proport = "";
			foreach ($po_array as $key => $val) {
				$val = explode("##", $val);
				if ($i > 0) $data_array_prop .= ",";
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_id = $key;
				$order_qnty = $val[0];
				$order_reject_qnty = $val[1];

				if (str_replace("'", "", $cbo_basis) == 2) {
					$sales_status_flag = $val[2];
				} else {
					$sales_status_flag =  $is_sales_order[0][csf("is_sales")];
				}

				$order_processloss_qnty = $val[3];

				$data_array_prop .= "(" . $id_proport . "," . $dtlsid . ",4,9," . $order_id . "," . $txt_prod_id . ",'" . $order_qnty . "','" . $order_reject_qnty . "','" . $issue_purpose . "'," . $user_id . ",'" . $pc_date_time . "','" . $sales_status_flag . "','" . $order_processloss_qnty . "')";
				$i++;
			}
		} //end if

		//order_wise_pro_details table data insert END

		$field_array_allocation = "id,entry_form,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,inserted_by,insert_date,is_dyied_yarn";
		$field_array_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date, is_dyied_yarn";

		$allo_qnty = 0;
		if (str_replace("'", "", $save_data_adjust_po) != "" && $variable_set_allocation == 1) {
			$save_string = explode(",", str_replace("'", "", $save_data_adjust_po));
			$po_array = array();
			$qnty_break_down = $po_break_down_ids = $job_no = $booking_no = "";

			for ($i = 0; $i < count($save_string); $i++) {
				$order_dtls = explode("_", $save_string[$i]);
				$order_id = $order_dtls[0];
				$allocation_qnty = $order_dtls[2];
				$job_no = $order_dtls[3];
				$booking_no = $order_dtls[4];
				$po_break_down_ids .= $order_id . ",";
				$qnty_break_down .= $allocation_qnty . "_" . $order_id . "_" . $job_no . ",";
				$allo_qnty += $allocation_qnty;
			}

			$po_break_down_ids = trim($po_break_down_ids, ", ");
			$qnty_break_down = trim($qnty_break_down, ", ");
			//echo "10**";
			$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_break_down_ids' and a.item_id=$txt_prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";
			$check_allocation_array = sql_select($sql_allocation);
			if (!empty($check_allocation_array)) {
				$allocation_id = $check_allocation_array[0][csf('id')];
				$existing_qnty = $check_allocation_array[0][csf('qnty')] + $allo_qnty;
				$field_array_allocation_update = "allocation_date*qnty*qnty_break_down*updated_by*update_date*is_dyied_yarn";
				$data_array_allocation_update = "" . $txt_return_date . "*" . $existing_qnty . "*'" . $qnty_break_down . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $dyed_type;

				for ($i = 0; $i < count($save_string); $i++) {
					$order_dtls = explode("_", $save_string[$i]);
					$order_id = $order_dtls[0];
					$allocation_dtls_qnty = $order_dtls[2];
					$job_no = $order_dtls[3];
					$booking_no = $order_dtls[4];

					$id_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $allocation_id . ",'" . $job_no . "'," . $order_id . ",'" . $booking_no . "',1," . $txt_return_date . "," . $txt_prod_id . "," . $allocation_dtls_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $dyed_type . ")";
				}

				execute_query("delete from inv_material_allocation_dtls where mst_id=$allocation_id", 1);
			} else {
				$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				if ($data_array_allocation != "") $data_array_allocation .= ",";
				$data_array_allocation .= "(" . $allocation_id . ",0,'" . $job_no . "','" . $order_id . "',1," . $txt_return_date . ",'" . $booking_no . "'," . $txt_prod_id . "," . $allo_qnty . ",'" . $qnty_break_down . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $dyed_type . ")";

				for ($i = 0; $i < count($save_string); $i++) {
					$order_dtls = explode("_", $save_string[$i]);
					$order_id = $order_dtls[0];
					$allocation_dtls_qnty = $order_dtls[2];
					$job_no = $order_dtls[3];
					$booking_no = $order_dtls[4];

					$id_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $allocation_id . ",'" . $job_no . "'," . $order_id . ",'" . $booking_no . "',1," . $txt_return_date . "," . $txt_prod_id . "," . $allocation_dtls_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $dyed_type . ")";
				}
			}
			//echo "10**";
			//echo $allocated_qnty_balance;
			//echo "10**update product_details_master set allocated_qnty=(allocated_qnty+$allo_qnty) where id=$txt_prod_id";die;

		} else {
			$rID_allocation_mst = $rID_allocation_dtls = $rID_de = $rID_dep = true;
		}

		$store_up_id = 0;
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

			if (count($sql_store) < 1) {
				echo "20**No Data Found.";
				disconnect($con);
				die;
			} elseif (count($sql_store) > 1) {
				echo "20**Duplicate Product is Not Allow in Same REF Number.";
				disconnect($con);
				die;
			} else {
				$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
				foreach ($sql_store as $result) {
					$store_up_id = $result[csf("id")];
					$store_presentStock	= $result[csf("current_stock")];
					$store_presentStockValue = $result[csf("stock_value")];
					$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
				}

				$field_array_store = "last_purchased_qnty*cons_qty*amount*updated_by*update_date";
				$currentStock_store	= $store_presentStock + $txt_return_qnty;
				$currentValue_store	= $store_presentStockValue + $issue_store_value;
				$data_array_store = "" . $txt_return_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'";
			}
		}

		$prodUpdate = $rID = $dtlsrID = $proportQ = $rID_allocation_mst = $rID_allocation_dtls = $rID_de = $rID_dep = $storeRID = true;
		//echo "10** $field_array_prod = $data_array_prod";oci_rollback($con);disconnect($con);die;
		if (str_replace("'", "", $txt_return_qnty) > 0) {
			$prodUpdate = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $txt_prod_id, 0);
		}


		if (str_replace("'", "", $save_data_adjust_po) != "" && $variable_set_allocation == 1) {
			$rID_de = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$allo_qnty) where id=$txt_prod_id", 0);
			$rID_dep = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_prod_id  ", 0);
		}


		if (str_replace("'", "", $txt_return_no) == "") {
			//echo "10** insert into inv_receive_master ($field_array) values $data_array";die;
			$rID = sql_insert("inv_receive_master", $field_array, $data_array, 0);
		} else {
			$rID = sql_update("inv_receive_master", $field_array, $data_array, "id", $id, 0);
		}

		//echo "10** insert into inv_transaction ($field_array_trans) values $data_array_trans";die;

		$dtlsrID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);

		if ($data_array_prop != "") {
			//echo "10** insert into order_wise_pro_details ($field_array_proportionate) values $data_array_prop";die;
			$proportQ = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
		}

		if ($data_array_allocation_update != "") {
			$rID_allocation_mst = sql_update("inv_material_allocation_mst", $field_array_allocation_update, $data_array_allocation_update, "id", "" . $allocation_id . "", 0);
		} else {
			if ($data_array_allocation != "") {
				$rID_allocation_mst = sql_insert("inv_material_allocation_mst", $field_array_allocation, $data_array_allocation, 0);
			}
		}

		if ($data_array_dtls != '') {
			//echo "10** insert into inv_material_allocation_dtls ($field_array_dtls) values $data_array_dtls";die;
			$rID_allocation_dtls = sql_insert("inv_material_allocation_dtls", $field_array_dtls, $data_array_dtls, 0);
		}

		if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
			$storeRID = sql_update("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
		}


		//echo "10**".$prodUpdate." && ".$rID." && ".$dtlsrID." && ".$proportQ." && ".$rID_allocation_mst." && ".$rID_allocation_dtls." && ".$rID_de." && ".$rID_dep ." && ". $storeRID; oci_rollback($con); die;

		if ($db_type == 0) {
			if ($prodUpdate && $rID && $dtlsrID && $proportQ && $rID_allocation_mst && $rID_allocation_dtls && $rID_de && $rID_dep && $storeRID) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_recv_number[0];
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_recv_number[0];
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($prodUpdate && $rID && $dtlsrID && $proportQ && $rID_allocation_mst && $rID_allocation_dtls && $rID_de && $rID_dep && $storeRID) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_recv_number[0];
			} else {
				oci_rollback($con);
				echo "10**" . $new_recv_number[0];
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		//check update id
		if (str_replace("'", "", $update_id) == "" || str_replace("'", "", $txt_prod_id) == "" || str_replace("'", "", $before_prod_id) == "") {
			echo "15";
			disconnect($con);
			exit();
		}

		$mrr_issue_check = return_field_value("sum(issue_qnty) as  issue_qnty", "inv_mrr_wise_issue_details", "recv_trans_id=$update_id and status_active=1 and	is_deleted=0", "issue_qnty");
		if (str_replace("'", "", $txt_return_qnty) < $mrr_issue_check) {
			echo "31**Issue Return quantity can not be less than Issue quantity";
			disconnect($con);
			die;
		}

		$sql = sql_select("select a.cons_quantity, a.cons_amount, a.store_amount, b.current_stock, b.stock_value, b.allocated_qnty, b.available_qnty, b.dyed_type from inv_transaction a, product_details_master b where a.id=$update_id and a.prod_id=b.id");
		$beforeReturnQnty = $beforeReturnValue = 0;
		$currentStockQnty = $currentStockValue = $before_available_qnty = 0;
		foreach ($sql as $result) {
			//current stock
			$currentStockQnty		= $result[csf("current_stock")];
			$currentStockValue		= $result[csf("stock_value")];
			//before return qnty
			$beforeReturnQnty		= $result[csf("cons_quantity")];
			$beforeReturnValue		= $result[csf("cons_amount")];
			$before_store_amount	= $result[csf("store_amount")];

			$before_allocated_qnty	= $result[csf("allocated_qnty")];
			$before_available_qnty	= $result[csf("available_qnty")];
			$dyed_type				= $result[csf("dyed_type")];
		}


		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,allocated_qnty,available_qnty from product_details_master where id=$txt_prod_id");
		$presentStock = $presentStockValue = $presentAvgRate = $available_qnty = 0;
		foreach ($sql as $result) {
			$presentStock			= $result[csf("current_stock")];
			$presentStockValue		= $result[csf("stock_value")];
			$presentAvgRate			= $result[csf("avg_rate_per_unit")];
			$allocated_qnty			= $result[csf("allocated_qnty")];
			$available_qnty			= $result[csf("available_qnty")];
		}

		/*
		$issue_row = sql_select("select id,issue_purpose,issue_basis,buyer_job_no,booking_no from inv_issue_master where id=$txt_issue_id");
		$issue_purpose = $issue_row[0][csf('issue_purpose')];
		$issue_basis = $issue_row[0][csf('issue_basis')];
		*/

		//adjust product master table START-------------------------------------//
		$txt_return_qnty = str_replace("'", "", $txt_return_qnty);
		$txt_return_value = str_replace("'", "", $txt_amount);
		$wo_entry_form = str_replace("'", "", $txt_wo_entry_form);

		if ($txt_return_qnty == "") $txt_return_qnty = 0;
		if ($txt_return_value == "") $txt_return_value = 0;

		$txt_prod_id = str_replace("'", "", $txt_prod_id);
		$update_array = "current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$field_array_store = "last_issued_qnty*cons_qty*amount*updated_by*update_date";
		$update_data = $updateID_array = array();
		$cbo_adjust_to = str_replace("'", "", $cbo_adjust_to);

		if (str_replace("'", "", $txt_prod_id) == str_replace("'", "", $before_prod_id)) // same product
		{
			// if yarn allocation variable set to yes
			if ($variable_set_allocation == 1) {
				if (($issue_basis == 3 || $issue_basis == 8) && ($issue_purpose == 1 || $issue_purpose == 4)) {
					$requisition_no_cond = ($issue_basis == 3) ? " and b.requisition_no=$txt_booking_no" : " and b.requisition_no=$hdn_req_no";

					$plan_sql = "select a.booking_no,c.job_no,a.is_sales from ppl_yarn_requisition_entry b,ppl_planning_entry_plan_dtls a,wo_booking_dtls c where b.knit_id=a.dtls_id and a.booking_no=c.booking_no and b.prod_id=$txt_prod_id $requisition_no_cond and b.status_active=1  group by a.booking_no,c.job_no,a.is_sales"; //ommit and a.status_active=1
					//echo "10**".$plan_sql; die;
					$planData = sql_select($plan_sql);

					$job_no = $planData[0][csf("job_no")];
					$booking_no = $planData[0][csf("booking_no")];
					$is_sales = $planData[0][csf("is_sales")];

					$presentallocatedQnty = $before_allocated_qnty - $beforeReturnQnty + $txt_return_qnty;
					$presentAvailableQnty = $before_available_qnty;
				} else if ($issue_basis == 1 && ($issue_purpose == 2 || $issue_purpose == 7 || $issue_purpose == 12 || $issue_purpose == 15 || $issue_purpose == 38 || $issue_purpose == 44 || $issue_purpose == 46 || $issue_purpose == 50 || $issue_purpose == 51)) {
					$is_sales_order = sql_select("select a.is_sales, b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no=$txt_booking_no and a.id=$txt_booking_id and b.product_id=$txt_prod_id and a.company_id=$cbo_company_id");
					$job_no 	= $is_sales_order[0][csf("job_no")];
					$is_sales 	= $is_sales_order[0][csf("is_sales")];
					$booking_no = '';

					if ($wo_entry_form == 42 || $wo_entry_form == 114) // Without Order
					{
						if ($variable_set_smn_allocation == 1) // sample booking allocation
						{
							$presentallocatedQnty = $before_allocated_qnty - $beforeReturnQnty + $txt_return_qnty;
							$presentAvailableQnty = $before_available_qnty;
						} else {
							$presentallocatedQnty = $before_allocated_qnty;
							$presentAvailableQnty = $before_available_qnty - $beforeReturnQnty + $txt_return_qnty;
						}
					} else if ($wo_entry_form == 94 || $wo_entry_form == 340) // Service Wo
					{
						if ($booking_without_order == 0 || $is_sales == 1)  // With order
						{
							$presentallocatedQnty = $before_allocated_qnty - $beforeReturnQnty + $txt_return_qnty;
							$presentAvailableQnty = $before_available_qnty;
						} else // Without order
						{
							/*
							if($variable_set_smn_allocation==1) // sample booking allocation
							{
								$presentallocatedQnty = $before_allocated_qnty-$beforeReturnQnty+$txt_return_qnty;
								$presentAvailableQnty = $before_available_qnty;
							}
							else
							{*/
							$presentallocatedQnty = $before_allocated_qnty;
							$presentAvailableQnty = $before_available_qnty - $beforeReturnQnty + $txt_return_qnty;
							//}
						}
					} else // 41,125,135
					{
						$presentallocatedQnty = $before_allocated_qnty - $beforeReturnQnty + $txt_return_qnty;
						$presentAvailableQnty = $before_available_qnty;
					}
				} else {
					$presentallocatedQnty = $before_allocated_qnty;
					$presentAvailableQnty = $before_available_qnty - $beforeReturnQnty + $txt_return_qnty;
				}

				if (str_replace("'", "", $save_data) != "") {
					$save_string = explode(",", str_replace("'", "", $save_data));
					$save_string_pre = explode(",", str_replace("'", "", $save_data_pre));
					$po_array = array();
					for ($i = 0; $i < count($save_string); $i++) {
						$order_dtls = explode("**", $save_string[$i]);
						$order_id = $order_dtls[0];
						$order_qnty = $order_dtls[1];
						$po_array[$order_id] = $order_qnty;
						$po_id = $order_id . ",";
					}

					$pre_po_array = array();
					for ($j = 0; $j < count($save_string_pre); $j++) {
						$order_dtls = explode("**", $save_string_pre[$j]);
						$order_id = $order_dtls[0];
						$order_qnty = $order_dtls[1];
						$pre_po_array[$order_id] = $order_qnty;
					}
					$po_id = rtrim($po_id, ", ");
					//echo "10**";
					$booking_cond = ($booking_no != "") ? " and a.booking_no='$booking_no' " : "";
					$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $txt_prod_id) . " and a.job_no='$job_no' $booking_cond and a.status_active=1 and a.is_deleted=0";
					$check_allocation_array = sql_select($sql_allocation);

					if (str_replace("'", "", $cbo_adjust_to) == 2) {

						// if allocation found
						if (!empty($check_allocation_array)) {

							$mst_id = $check_allocation_array[0][csf('id')];
							$qnty_break_down_str = explode(",", $check_allocation_array[0][csf('qnty_break_down')]);
							$mstQnty = $check_allocation_array[0][csf('qnty')];

							foreach ($po_array as $key => $val) {
								$allo_qnty = (str_replace("'", "", $pre_cbo_adjust_to) != 2) ? $mstQnty - $val : ($mstQnty + ($pre_po_array[$key] - $val));
								execute_query("update inv_material_allocation_dtls set qnty=$allo_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id=$key and item_id = $txt_prod_id", 0);
								$po_wise_allocation[$key] = $val;
							}
							//echo $allo_qnty;
							//die;
							//print_r($po_wise_allocation);
							$qnty_break_down_str_new = "";
							$allo_qnty = 0;
							foreach ($qnty_break_down_str as $qnty_break_down_row) {
								$qnty_break_down_row_info = explode("_", $qnty_break_down_row);
								if (str_replace("'", "", $pre_cbo_adjust_to) == 2) {
									//echo $qnty_break_down_row_info[0];
									$allo_qnty = $mstQnty + ($pre_po_array[$qnty_break_down_row_info[1]] - $po_wise_allocation[$qnty_break_down_row_info[1]]);
								} else {
									$allo_qnty = $mstQnty - $po_wise_allocation[$qnty_break_down_row_info[1]];
								}
								$qnty_break_down_str_new = ($allo_qnty) . "_" . $qnty_break_down_row_info[1] . "_" . $qnty_break_down_row_info[2] . ",";
								$mst_qnty += $allo_qnty;
							}

							//echo $qnty_break_down_str_new;
							//die;
							$qnty_break_down_str_new = rtrim($qnty_break_down_str_new, ", ");
							execute_query("update inv_material_allocation_mst set qnty=$mst_qnty,qnty_break_down='$qnty_break_down_str_new',updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
						}
					} else {
						if (!empty($check_allocation_array)) {
							if (str_replace("'", "", $pre_cbo_adjust_to) == 2) {
								$mst_id = $check_allocation_array[0][csf('id')];
								$qnty_break_down_str = explode(",", $check_allocation_array[0][csf('qnty_break_down')]);
								//echo "10**";
								foreach ($po_array as $key => $val) {
									//$allo_qnty = (str_replace("'","",$pre_cbo_adjust_to)==2)?$val:$pre_po_array[$key]-$val;
									$allo_qnty = $val;
									execute_query("update inv_material_allocation_dtls set qnty=(qnty+$allo_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id=$key and item_id = $txt_prod_id", 0);
									$po_wise_allocation[$key] = $allo_qnty;
								}


								$qnty_break_down_str_new = "";
								$mst_qnty = $allo_qnty = 0;
								foreach ($qnty_break_down_str as $qnty_break_down_row) {
									$qnty_break_down_row_info = explode("_", $qnty_break_down_row);
									$allo_qnty = $po_wise_allocation[$qnty_break_down_row_info[1]];

									$qnty_break_down_str_new = ($qnty_break_down_row_info[0] + $allo_qnty) . "_" . $qnty_break_down_row_info[1] . "_" . $qnty_break_down_row_info[2] . ",";
									$mst_qnty += $allo_qnty;
								}
								//echo $allo_qnty;
								//die;
								$qnty_break_down_str_new = rtrim($qnty_break_down_str_new, ", ");
								execute_query("update inv_material_allocation_mst set qnty=(qnty+$mst_qnty),qnty_break_down='$qnty_break_down_str_new',updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
							}
						}
					}
				}
			} else {
				$presentallocatedQnty = $before_allocated_qnty;
				$presentAvailableQnty = $before_available_qnty - $beforeReturnQnty + $txt_return_qnty;
			}

			$presentStockQnty   = $currentStockQnty - $beforeReturnQnty + $txt_return_qnty; //current qnty - before qnty + present return qnty
			$presentStockValue  = $currentStockValue - $beforeReturnValue + $txt_return_value;
			$avgRate			= trim($presentStockValue / $presentStockQnty);
			$avgRate			= number_format($avgRate, 10, ".", "");
			$data_array			= $presentStockQnty . "*" . number_format($presentStockValue, 8, '.', '') . "*" . $presentallocatedQnty . "*" . $presentAvailableQnty . "*'" . $user_id . "'*'" . $pc_date_time . "'";

			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");
				if (count($sql_store) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}

					if ($store_up_id) {
						$adj_beforeStock_store			= $store_presentStock + $beforeReturnQnty;
						$adj_beforeStockValue_store		= $store_presentStockValue + $before_store_amount;

						$currentStock_store		= $adj_beforeStock_store - $txt_return_qnty;
						$currentValue_store		= $adj_beforeStockValue_store - $issue_store_value;
						$updateID_Storeprod[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_return_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}
		} else // change product
		{
			// if yarn allocation variable set to yes
			if ($variable_set_allocation == 1) {
				if (($issue_basis == 3 || $issue_basis == 8) && ($issue_purpose == 1 || $issue_purpose == 4)) {
					$requisition_no_cond = ($issue_basis == 3) ? " and a.requisition_no=$txt_booking_no" : " and a.requisition_no=$hdn_req_no";

					$is_sales_order = sql_select("select b.is_sales from ppl_yarn_requisition_entry a,ppl_planning_info_entry_dtls b where a.knit_id=b.id and a.prod_id=$txt_prod_id $requisition_no_cond");
					$is_sales = $is_sales_order[0][csf("is_sales")];

					$adj_allocated_qnty = $before_allocated_qnty - $beforeReturnQnty;
					$adj_available_qnty = $before_available_qnty;

					$presentallocatedQnty = $allocated_qnty + $txt_return_qnty;
					$presentAvailableQnty = $available_qnty;
				} else if ($issue_basis == 1 && ($issue_purpose == 2 || $issue_purpose == 7 || $issue_purpose == 12 || $issue_purpose == 15 || $issue_purpose == 38 || $issue_purpose == 44 || $issue_purpose == 46 || $issue_purpose == 50 || $issue_purpose == 51)) {

					$is_sales_order = sql_select("select a.is_sales,b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no=$txt_booking_no and a.id=$txt_booking_id and b.product_id=$txt_prod_id and a.company_id=$cbo_company_id");

					$job_no = $is_sales_order[0][csf("job_no")];
					$is_sales = $is_sales_order[0][csf("is_sales")];

					if ($wo_entry_form == 42 || $wo_entry_form == 114) // Without order
					{
						if ($variable_set_smn_allocation == 1) // sample booking allocation
						{
							$adj_allocated_qnty = $before_allocated_qnty - $beforeReturnQnty;
							$adj_available_qnty = $before_available_qnty;

							$presentallocatedQnty = $allocated_qnty + $txt_return_qnty;
							$presentAvailableQnty = $available_qnty;
						} else {
							$adj_allocated_qnty = $before_allocated_qnty;
							$adj_available_qnty = $before_available_qnty - $beforeReturnQnty;

							$presentallocatedQnty = $allocated_qnty;
							$presentAvailableQnty = $available_qnty + $txt_return_qnty;
						}
					} else if ($wo_entry_form == 94 || $wo_entry_form == 340) // Service Wo
					{
						if ($booking_without_order == 0 || $is_sales == 1) // With order
						{
							$adj_allocated_qnty = $before_allocated_qnty - $beforeReturnQnty;
							$adj_available_qnty = $before_available_qnty;

							$presentallocatedQnty = $allocated_qnty + $txt_return_qnty;
							$presentAvailableQnty = $available_qnty;
						} else {
							/*
							if($variable_set_smn_allocation==1) // sample booking allocation
							{
								$adj_allocated_qnty = $before_allocated_qnty-$beforeReturnQnty;
								$adj_available_qnty = $before_available_qnty;

								$presentallocatedQnty = $allocated_qnty+$txt_return_qnty;
								$presentAvailableQnty = $available_qnty;
							}
							else
							{*/
							$adj_allocated_qnty = $before_allocated_qnty;
							$adj_available_qnty = $before_available_qnty - $beforeReturnQnty;

							$presentallocatedQnty = $allocated_qnty;
							$presentAvailableQnty = $available_qnty + $txt_return_qnty;
							//}
						}
					} else // 41,125,135
					{

						$adj_allocated_qnty = $before_allocated_qnty - $beforeReturnQnty;
						$adj_available_qnty = $before_available_qnty;

						$presentallocatedQnty = $allocated_qnty + $txt_return_qnty;
						$presentAvailableQnty = $available_qnty;
					}
				} else {
					$adj_allocated_qnty = $before_allocated_qnty;
					$adj_available_qnty = $before_available_qnty - $beforeReturnQnty;

					$presentallocatedQnty = $allocated_qnty;
					$presentAvailableQnty = $available_qnty + $txt_return_qnty;
				}
			} else {
				$adj_allocated_qnty = $before_allocated_qnty;
				$adj_available_qnty = $before_available_qnty - $beforeReturnQnty;

				$presentallocatedQnty = $allocated_qnty;
				$presentAvailableQnty = $available_qnty + $txt_return_qnty;
			}

			//before
			$presentStockQnty   = $currentStockQnty - $txt_return_qnty; //current qnty - before qnty
			$presentStockValue  = $currentStockValue - $txt_return_value;
			$avgRate			= trim($presentStockValue / $presentStockQnty);
			$avgRate			= number_format($avgRate, 10, ".", "");
			$before_prod_id 	= str_replace("'", "", $before_prod_id);
			$update_data[$before_prod_id] = explode("*", ("" . $presentStockQnty . "*" . $presentStockValue . "*" . $adj_allocated_qnty . "*" . $adj_available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
			$updateID_array[] = $before_prod_id;

			//current
			$presentStockQnty   = $presentStock + $txt_return_qnty; //current qnty - before qnty + present return qnty
			$presentStockValue  = $presentStockValue + $txt_return_value;
			$avgRate			= trim($presentStockValue / $presentStockQnty);
			$avgRate			= number_format($avgRate, 10, ".", "");
			$txt_prod_id 		= str_replace("'", "", $txt_prod_id);
			$update_data[$txt_prod_id] = explode("*", ("" . $presentStockQnty . "*" . $presentStockValue . "*" . $presentallocatedQnty . "*" . $presentAvailableQnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
			$updateID_array[] = $txt_prod_id;

			$store_up_id = 0;
			if ($variable_store_wise_rate == 1) {
				$sql_store_before = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$before_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

				if (count($sql_store_before) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store_before) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_before as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}

					if ($store_up_id) {
						$currentStock_store		= $store_presentStock + $beforeReturnQnty;
						$currentValue_store		= $store_presentStockValue + $before_store_amount;
						$updateID_Storeprod[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $before_issue_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}

				$sql_store_after = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");
				$store_up_id = 0;
				if (count($sql_store_after) < 1) {
					echo "20**No Data Found.";
					disconnect($con);
					die;
				} elseif (count($sql_store_after) > 1) {
					echo "20**Duplicate Product is Not Allow in Same REF Number.";
					disconnect($con);
					die;
				} else {
					$store_presentStock = $store_presentStockValue = $store_presentAvgRate = 0;
					foreach ($sql_store_after as $result) {
						$store_up_id = $result[csf("id")];
						$store_presentStock	= $result[csf("current_stock")];
						$store_presentStockValue = $result[csf("stock_value")];
						$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
					}
					if ($store_up_id) {
						$currentStock_store		= $store_presentStock - $txt_return_qnty;
						$currentValue_store		= $store_presentStockValue - $issue_store_value;
						$updateID_Storeprod[] = $store_up_id;
						$data_array_store[$store_up_id] = explode("*", ("" . $txt_issue_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}
				}
			}
		}
		//adjust product master table END  -------------------------------------//

		//echo "10**$data_array"; die();

		//yarn receive master table UPDATE here START----------------------//

		/*#### Stop not eligible field from update operation start ####*/
		// company_id*knitting_source*knitting_company*booking_id*booking_no*booking_without_order*receive_basis*
		// "".$cbo_company_id."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$txt_booking_id."*".$txt_booking_no."*".$booking_without_order."*".$cbo_basis."*".
		/*#### Stop not eligible field from update operation end ####*/

		$field_array_upd = "receive_date*challan_no*store_id*floor*room*rack*shelf*bin*location_id*exchange_rate*currency_id*supplier_id*issue_id*remarks*no_bag*no_cone*updated_by*update_date";
		$data_array_upd = $txt_return_date . "*" . $txt_return_challan_no . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_location . "*1*1*" . $txt_supplier_id . "*" . $txt_issue_id . "*" . $txt_remarks . "*" . $txt_bag . "*" . $txt_cone . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		//yarn receive master table entry here END---------------------------------------//

		/******** original product id check start ********/

		//$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$txt_prod_id and status_active=1","origin_prod_id");
		$origin_prod_id = return_field_value("origin_prod_id", "inv_transaction", "prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_issue_id and transaction_type in (2) and item_category=1", "origin_prod_id");

		/******** original product id check end ********/
		//transaction table update here START--------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$field_array_trans = "receive_basis*company_id*supplier_id*prod_id*origin_prod_id*item_category*transaction_type*transaction_date*store_id*floor_id*room*rack*self*bin_box*order_uom*order_qnty*order_rate*order_amount*cons_uom*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*balance_qnty*balance_amount*issue_challan_no*issue_id*remarks*no_bag*no_cone*dyeing_color_id*requisition_no*demand_id*updated_by*update_date*store_rate*store_amount*no_of_qty";
		$data_array_trans = "" . $cbo_basis . "*" . $cbo_company_id . "*" . $txt_supplier_id . "*" . $txt_prod_id . "*'" . $origin_prod_id . "'*1*4*" . $txt_return_date . "*" . $cbo_store_name . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_uom . "*" . $txt_return_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_amount, 8, '.', '') . "*" . $cbo_uom . "*" . $txt_return_qnty . "*" . $txt_reject_qnty . "*" . number_format($txt_rate, 10, '.', '') . "*" . number_format($txt_amount, 8, '.', '') . "*" . $txt_return_qnty . "*" . number_format($txt_amount, 8, '.', '') . "*" . $txt_issue_challan_no . "*" . $txt_issue_id . "*" . $txt_remarks . "*" . $txt_bag . "*" . $txt_cone . "*" . $txt_dyeing_color_id . "*" . $hdn_req_no . "*" . $txt_booking_id . "*'" . $user_id . "'*'" . $pc_date_time . "'*" . number_format($store_item_rate, 10, '.', '') . "*" . number_format($issue_store_value, 8, '.', '') . "*" . $txt_processloss_qnty . "";
		//transaction table update here END ---------------------------------//


		$field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,reject_qty,issue_purpose,inserted_by,insert_date,is_sales,processloss_qty";
		if (str_replace("'", "", $save_data) != "") {
			//order_wise_pro_details table data insert START-----//
			$save_string = explode(",", str_replace("'", "", $save_data));
			$po_array = array();
			for ($i = 0; $i < count($save_string); $i++) {
				$order_dtls = explode("**", $save_string[$i]);
				$order_id = $order_dtls[0];
				$order_qnty = $order_dtls[1];
				$order_reject_qnty = $order_dtls[2];
				$order_sales_status = $order_dtls[3];
				$processloss_qty = $order_dtls[4];
				$po_array[$order_id] = $order_qnty . "##" . $order_reject_qnty . "##" . $order_sales_status . "##" . $processloss_qty;
			}

			$i = 0;
			$id_proport = "";
			foreach ($po_array as $key => $val) {
				$val = explode("##", $val);
				if ($i > 0) $data_array_prop .= ",";
				$id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_id = $key;
				$order_qnty = $val[0];
				$order_reject_qnty = $val[1];

				if (str_replace("'", "", $cbo_basis) == 2) {
					$sales_status_flag = $val[2];
				} else if (str_replace("'", "", $cbo_basis) == 4) {
					$sales_status_flag = 1;
				} else {
					$sales_status_flag = $is_sales;
				}

				$order_processloss_qnty = $val[3];

				$data_array_prop .= "(" . $id_proport . "," . $update_id . ",4,9," . $order_id . "," . $txt_prod_id . ",'" . $order_qnty . "','" . $order_reject_qnty . "','" . $issue_purpose . "'," . $user_id . ",'" . $pc_date_time . "','" . $sales_status_flag . "','" . $order_processloss_qnty . "')";
				$i++;
			}
		} //end if

		//order_wise_pro_details table data insert END -----//
		$prodUpdate = true;
		$proportQ = true;

		if (str_replace("'", "", $txt_prod_id) == str_replace("'", "", $before_prod_id)) {
			if ($data_array != "") {

				$prodUpdate = sql_update("product_details_master", $update_array, $data_array, "id", $txt_prod_id, 1);

				//echo "10** hi"; die();

			}
		} else {
			if (!empty($update_data)) {
				$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $update_array, $update_data, $updateID_array));
			}
		}


		$id = str_replace("'", "", $txt_mst_id);
		$rID = sql_update("inv_receive_master", $field_array_upd, $data_array_upd, "id", $id, 0);
		$transID = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 0);
		$propQr = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=9 and trans_type=4");

		if ($data_array_prop != "") {
			//echo "10** insert into order_wise_pro_details ($field_array_proportionate) values $data_array_prop";die;
			$proportQ = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
		}

		$field_array_allocation = "id,entry_form,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,inserted_by,insert_date,is_dyied_yarn";
		$field_array_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date, is_dyied_yarn";
		if (str_replace("'", "", $save_data_adjust_po) != "" && $variable_set_allocation == 1) {
			$save_string = explode(",", str_replace("'", "", $save_data_adjust_po));
			$po_array = array();
			$qnty_break_down = $po_break_down_ids = $job_no = $booking_no = "";
			$allo_qnty = 0;
			for ($i = 0; $i < count($save_string); $i++) {
				$order_dtls = explode("_", $save_string[$i]);
				$order_id = $order_dtls[0];
				$allocation_qnty = $order_dtls[2];
				$job_no = $order_dtls[3];
				$booking_no = $order_dtls[4];
				$po_break_down_ids .= $order_id . ",";
				$qnty_break_down .= $allocation_qnty . "_" . $order_id . "_" . $job_no . ",";
				$allo_qnty += $allocation_qnty;
			}

			$po_break_down_ids = trim($po_break_down_ids, ", ");
			$qnty_break_down = trim($qnty_break_down, ", ");
			$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_break_down_ids' and a.item_id=$txt_prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";
			$check_allocation_array = sql_select($sql_allocation);
			if (!empty($check_allocation_array)) {
				$allocation_id = $check_allocation_array[0][csf('id')];
				$existing_qnty = $check_allocation_array[0][csf('qnty')] + ($allo_qnty - str_replace("'", "", $hdn_adjust_po));
				$field_array_allocation_update = "allocation_date*qnty*qnty_break_down*updated_by*update_date*is_dyied_yarn";
				$data_array_allocation_update = "" . $txt_return_date . "*" . $existing_qnty . "*'" . $qnty_break_down . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $dyed_type;
				for ($i = 0; $i < count($save_string); $i++) {
					$order_dtls = explode("_", $save_string[$i]);
					$order_id = $order_dtls[0];
					$allocation_dtls_qnty = $order_dtls[2];
					$job_no = $order_dtls[3];
					$booking_no = $order_dtls[4];

					$id_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $allocation_id . ",'" . $job_no . "'," . $order_id . ",'" . $booking_no . "',1," . $txt_return_date . "," . $txt_prod_id . "," . $allocation_dtls_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $dyed_type . ")";
				}

				execute_query("delete from inv_material_allocation_dtls where mst_id=$allocation_id", 1);
			} else {
				$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
				if ($data_array_allocation != "") $data_array_allocation .= ",";
				$data_array_allocation .= "(" . $allocation_id . ",0,'" . $job_no . "','" . $order_id . "',1," . $txt_return_date . ",'" . $booking_no . "'," . $txt_prod_id . "," . $allo_qnty . ",'" . $qnty_break_down . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $dyed_type . ")";

				for ($i = 0; $i < count($save_string); $i++) {
					$order_dtls = explode("_", $save_string[$i]);
					$order_id = $order_dtls[0];
					$allocation_dtls_qnty = $order_dtls[2];
					$job_no = $order_dtls[3];
					$booking_no = $order_dtls[4];

					$id_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $allocation_id . ",'" . $job_no . "'," . $order_id . ",'" . $booking_no . "',1," . $txt_return_date . "," . $txt_prod_id . "," . $allocation_dtls_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $dyed_type . ")";
				}
			}

			$allo_qnty = ($allo_qnty - str_replace("'", "", $hdn_adjust_po));
			$rID_de = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$allo_qnty) where id=$txt_prod_id", 0);
			$rID_dep = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_prod_id  ", 0);
		} else {
			$rID_allocation_mst = $rID_allocation_dtls = $rID_de = $rID_dep = true;
		}

		if ($data_array_allocation_update != "") {
			$rID_allocation_mst = sql_update("inv_material_allocation_mst", $field_array_allocation_update, $data_array_allocation_update, "id", "" . $allocation_id . "", 0);
		} else {
			if ($data_array_allocation != "") {
				$rID_allocation_mst = sql_insert("inv_material_allocation_mst", $field_array_allocation, $data_array_allocation, 0);
			}
		}

		if ($data_array_dtls != '') {
			//echo "10** insert into inv_material_allocation_dtls ($field_array_dtls) values $data_array_dtls";die;
			$rID_allocation_dtls = sql_insert("inv_material_allocation_dtls", $field_array_dtls, $data_array_dtls, 0);
		}

		$storeRID = true;
		if (count($updateID_Storeprod) > 0 && $variable_store_wise_rate == 1) {
			$storeRID = execute_query(bulk_update_sql_statement("inv_store_wise_yarn_qty_dtls", "id", $field_array_store, $data_array_store, $updateID_Storeprod), 0);
		}

		//echo "10**".$prodUpdate." && ".$rID." && ".$transID." && ".$proportQ." && ".$propQr." && ".$rID_allocation_mst." && ".$rID_allocation_dtls." && ".$rID_de." && ".$rID_dep." && ".$storeRID;oci_rollback($con);disconnect($con);die;

		if ($db_type == 0) {
			if ($prodUpdate && $rID && $transID && $proportQ && $propQr && $rID_allocation_mst && $rID_allocation_dtls && $rID_de && $rID_dep && $storeRID) {
				mysql_query("COMMIT");
				echo "1**" . $id . "**" . str_replace("'", "", $txt_return_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_return_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($prodUpdate && $rID && $transID && $proportQ && $propQr && $rID_allocation_mst && $rID_allocation_dtls && $rID_de && $rID_dep && $storeRID) {
				oci_commit($con);
				echo "1**" . $id . "**" . str_replace("'", "", $txt_return_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_return_no);
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$mrr_data = sql_select("select a.id, a.is_posted_account, a.receive_purpose, a.receive_basis, b.cons_quantity, b.cons_rate, b.cons_amount, b.store_amount, c.id as prod_id, c.current_stock, c.stock_value, c.allocated_qnty, c.available_qnty 
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and b.status_active=1 and b.id=$update_id");
		$master_id = $mrr_data[0][csf("id")];
		$is_posted_account = $mrr_data[0][csf("is_posted_account")] * 1;
		$receive_purpose = $mrr_data[0][csf("receive_purpose")];
		$receive_basis = $mrr_data[0][csf("receive_basis")];
		$cons_quantity = $mrr_data[0][csf("cons_quantity")];
		$cons_rate = $mrr_data[0][csf("cons_rate")];
		$cons_amount = $mrr_data[0][csf("cons_amount")];
		$before_store_amount = $mrr_data[0][csf("store_amount")];
		$prod_id = $mrr_data[0][csf("prod_id")];
		$current_stock = $mrr_data[0][csf("current_stock")];
		$stock_value = $mrr_data[0][csf("stock_value")];
		$allocated_qnty = $mrr_data[0][csf("allocated_qnty")];
		$available_qnty = $mrr_data[0][csf("available_qnty")];

		$cu_current_stock = $current_stock - $cons_quantity;
		$cu_stock_value = $stock_value - $cons_amount;
		if ($cu_stock_value > 0 && $cu_current_stock > 0) $cu_avg_rate = $cu_stock_value / $cu_current_stock;
		else $cu_avg_rate = 0;
		/*
		$issue_row = sql_select("select issue_purpose,issue_basis from inv_issue_master where id=$txt_issue_id");
		$issue_purpose = $issue_row[0][csf('issue_purpose')];
		$issue_basis = $issue_row[0][csf('issue_basis')];
		*/

		$cbo_adjust_to = str_replace("'", "", $cbo_adjust_to);
		$wo_entry_form = str_replace("'", "", $txt_wo_entry_form);

		if ($variable_set_allocation == 1) {
			if (($issue_basis == 3 || $issue_basis == 8) && ($issue_purpose == 1 || $issue_purpose == 4)) {
				$plan_sql = "select a.booking_no,c.job_no,a.is_sales from ppl_yarn_requisition_entry b,ppl_planning_entry_plan_dtls a,wo_booking_dtls c where b.knit_id=a.dtls_id and a.booking_no=c.booking_no and b.status_active=1 and b.requisition_no=$txt_booking_no and b.prod_id=$txt_prod_id group by a.booking_no,c.job_no,a.is_sales"; // ommit :  and a.status_active=1
				$planData = sql_select($plan_sql);

				$job_no = $planData[0][csf("job_no")];
				$booking_no = $planData[0][csf("booking_no")];

				$cu_allocated_qnty = $allocated_qnty - $cons_quantity;
				$cu_available_qnty = $available_qnty;
			} else if ($issue_basis == 1 && ($issue_purpose == 2 || $issue_purpose == 7 || $issue_purpose == 12 || $issue_purpose == 15 || $issue_purpose == 38 || $issue_purpose == 44 || $issue_purpose == 46 || $issue_purpose == 50 || $issue_purpose == 51)) {
				$is_sales_order = sql_select("select a.is_sales,b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no=$txt_booking_no and a.id=$txt_booking_id and b.product_id=$txt_prod_id and a.company_id=$cbo_company_id");
				$job_no 	= $is_sales_order[0][csf("job_no")];
				$is_sales 	= $is_sales_order[0][csf("is_sales")];

				$booking_no = '';

				if ($wo_entry_form == 42 || $wo_entry_form == 114) // Without order
				{
					if ($variable_set_smn_allocation == 1) {
						$cu_allocated_qnty = $allocated_qnty - $cons_quantity;
						$cu_available_qnty = $available_qnty;
					} else {
						$cu_allocated_qnty = $allocated_qnty;
						$cu_available_qnty = $available_qnty - $cons_quantity;
					}
				} else if ($wo_entry_form == 94 || $wo_entry_form == 340) // Service Wo
				{
					if ($booking_without_order == 0 || $is_sales == 1) // With order
					{
						$cu_allocated_qnty = $allocated_qnty - $cons_quantity;
						$cu_available_qnty = $available_qnty;
					} else // Without order
					{
						/*
						if($variable_set_smn_allocation==1) // sample booking allocation
						{
							$cu_allocated_qnty=$allocated_qnty-$cons_quantity;
							$cu_available_qnty=$available_qnty;
						}
						else
						{*/
						$cu_allocated_qnty = $allocated_qnty;
						$cu_available_qnty = $available_qnty - $cons_quantity;
						//}

					}
				} else // 41,125,135
				{
					$cu_allocated_qnty = $allocated_qnty - $cons_quantity;
					$cu_available_qnty = $available_qnty;
				}
			} else {
				$cu_allocated_qnty = $allocated_qnty;
				$cu_available_qnty = $available_qnty - $cons_quantity;
			}

			if (str_replace("'", "", $save_data) != "") {
				$save_string = explode(",", str_replace("'", "", $save_data));
				$save_string_pre = explode(",", str_replace("'", "", $save_data_pre));
				$po_array = array();
				for ($i = 0; $i < count($save_string); $i++) {
					$order_dtls = explode("**", $save_string[$i]);
					$order_id = $order_dtls[0];
					$order_qnty = $order_dtls[1];
					$po_array[$order_id] = $order_qnty;
					$po_id = $order_id . ",";
				}

				$pre_po_array = array();
				for ($j = 0; $j < count($save_string_pre); $j++) {
					$order_dtls = explode("**", $save_string_pre[$j]);
					$order_id = $order_dtls[0];
					$order_qnty = $order_dtls[1];
					$pre_po_array[$order_id] = $order_qnty;
				}
				$po_id = rtrim($po_id, ", ");
				$booking_cond = ($booking_no != "") ? " and a.booking_no='$booking_no' " : "";
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $txt_prod_id) . " and a.job_no='$job_no' $booking_cond and a.status_active=1 and a.is_deleted=0";
				$check_allocation_array = sql_select($sql_allocation);

				if (str_replace("'", "", $cbo_adjust_to) == 2) {

					// if allocation found
					if (!empty($check_allocation_array)) {

						$mst_id = $check_allocation_array[0][csf('id')];
						$qnty_break_down_str = explode(",", $check_allocation_array[0][csf('qnty_break_down')]);

						foreach ($po_array as $key => $val) {
							$allo_qnty = $val;
							execute_query("update inv_material_allocation_dtls set qnty=(qnty+$allo_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id=$key and item_id = $txt_prod_id", 0);
							$po_wise_allocation[$key] = $val;
						}

						$qnty_break_down_str_new = "";
						foreach ($qnty_break_down_str as $qnty_break_down_row) {
							$qnty_break_down_row_info = explode("_", $qnty_break_down_row);
							$allo_qnty = $po_wise_allocation[$qnty_break_down_row_info[1]];
							$qnty_break_down_str_new = ($qnty_break_down_row_info[0] + $allo_qnty) . "_" . $qnty_break_down_row_info[1] . "_" . $qnty_break_down_row_info[2] . ",";
							$mst_qnty += $allo_qnty;
						}
						$qnty_break_down_str_new = rtrim($qnty_break_down_str_new, ", ");
						execute_query("update inv_material_allocation_mst set qnty=(qnty+$mst_qnty),qnty_break_down='$qnty_break_down_str_new',updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
					}
				}
			}
		} else {
			$cu_allocated_qnty = $allocated_qnty;
			$cu_available_qnty = $available_qnty - $cons_quantity;
		}

		if ($is_posted_account > 0) {
			echo "13**Delete restricted, This Information is used in another Table.";
			disconnect($con);
			oci_rollback($con);
			die;
		}

		$next_operation = return_field_value("max(id) as max_trans_id", "inv_transaction", "status_active=1 and item_category=1 and transaction_type<>4 and prod_id=$prod_id", "max_trans_id");
		if ($next_operation) {
			if ($next_operation > str_replace("'", "", $update_id)) {
				echo "13**Delete restricted, This Information is used in another Table.";
				disconnect($con);
				oci_rollback($con);
				die;
			}
		}

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";
		$field_array_prod = "current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = "" . $cu_current_stock . "*" . number_format($cu_avg_rate, 10, '.', '') . "*" . number_format($cu_stock_value, 8, '.', '') . "*'" . $cu_allocated_qnty . "'*'" . $cu_available_qnty . "'*'" . $user_id . "'*'" . $pc_date_time . "'";

		$store_up_id = 0;
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

			if (count($sql_store) < 1) {
				echo "20**No Data Found.";
				disconnect($con);
				die;
			} elseif (count($sql_store) > 1) {
				echo "20**Duplicate Product is Not Allow in Same REF Number.";
				disconnect($con);
				die;
			} else {
				$store_presentStock = $store_presentStockValue = $store_presentAvgRate = $store_before_receive_qnty = 0;
				foreach ($sql_store as $result) {
					$store_up_id = $result[csf("id")];
					$store_presentStock	= $result[csf("current_stock")];
					$store_presentStockValue = $result[csf("stock_value")];
					$store_presentAvgRate	= $result[csf("avg_rate_per_unit")];
				}
				$currentStock_store		= $store_presentStock + $cons_quantity;
				$currentValue_store		= $store_presentStockValue + $before_store_amount;

				$field_array_store = "last_issued_qnty*cons_qty*amount*updated_by*update_date";
				$data_array_store = "" . $before_receive_qnty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'";
			}
		}

		//$rID=sql_update("inv_receive_master",$field_array,$data_array,"recv_number","$txt_return_no",1);
		$rID = 1;
		$rIDTr = sql_update("inv_transaction", $field_array, $data_array, "id", "$update_id", 1);
		if (str_replace("'", "", $update_id)) {
			$rIDProp = sql_update("order_wise_pro_details", $field_array, $data_array, "trans_id", "$update_id", 1);
		}
		$rIDprodID = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", "$prod_id", 1);

		$storeRID = true;
		if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
			$storeRID = sql_update("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
		}

		//echo "10**".$rID."*".$rIDTr."*".$rIDProp."*".$rIDprodID; die;
		if ($db_type == 0) {
			if ($rID && $rIDTr && $rIDProp && $rIDprodID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_mrr_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rIDTr && $rIDProp && $rIDprodID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_mrr_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "return_number_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(mrr, posted_status) {
			$("#hidden_return_number").val(mrr);
			$("#hidden_posted_in_account").val(posted_status); // posted account
			parent.emailwindow.hide();
		}

		function popup_print() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			$('#list_view tbody tr:first').hide();
			document.getElementById('list_container_batch').style.overflow = "auto";
			document.getElementById('list_container_batch').style.maxHeight = "none";

			d.write(document.getElementById('popup_data').innerHTML);

			document.getElementById('list_container_batch').style.overflowY = "scroll";
			document.getElementById('list_container_batch').style.maxHeight = "240px";

			$('#list_view tbody tr:first').show();
			d.close();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="170">Search By</th>
							<th width="200" align="center" id="search_by_td_up">Enter Return Number</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								$search_by = array(1 => 'Return Number', 2 => 'Return Challan');
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 130, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_return_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_return_number" value="" />
								<input type="hidden" id="hidden_posted_in_account" value="" />
								<!--END-->
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div style="margin-top:5px" align="center" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_return_search_list_view") {
	$ex_data = explode("_", $data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$cbo_year = $ex_data[5];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sql_cond = "";
	if ($search_by == 1) {
		if ($search_common != "") $sql_cond .= " and recv_number like '%$search_common'";
	} else if ($search_by == 2) {
		if ($search_common != "") $sql_cond .= " and challan_no='$search_common'";
	}
	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.receive_date  between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.receive_date  between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	}
	if ($cbo_year != '' || $cbo_year != 0) {
		$sql_cond .= " and TO_CHAR (a.receive_date, 'YYYY') = " . $cbo_year . "";
	}

	if ($company != "") $sql_cond .= " and a.company_id='$company'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";

	$sql = "select a.id as mst_id, a.recv_number_prefix_num,a.challan_no, a.recv_number, a.company_id, a.receive_date, a.item_category, a.recv_number, a.knitting_source, a.knitting_company, $year_field b.id, b.cons_quantity, b.cons_reject_qnty, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_type,c.color,c.yarn_comp_percent1st, c.id as prod_id, c.lot, a.is_posted_account, c.supplier_id
	from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	where a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";
?>
	<div id="popup_data" style=" width:1010px;">
		<table class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" width="1010">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="50">Return No</th>
					<th width="150">Knitting Company</th>
					<th width="70">Ret Challan</th>
					<th width="70">Date</th>
					<th width="40">Year</th>
					<th width="130">Supplier Name</th>
					<th width="60">Lot No</th>
					<th width="170">Item Description</th>
					<th width="80">Return Qnty</th>
					<th width="80">Rejected Qnty</th>
					<th>UOM</th>
				</tr>
			</thead>
		</table>
		<div style="width:1010px; max-height:240px; overflow-y:scroll" id="list_container_batch">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table" id="list_view">
				<tbody>
					<?
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
					$sql_result = sql_select($sql);
					$i = 1;
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						if ($row[csf("knitting_source")] == 1) $kint_com = $company_library[$row[csf("knitting_company")]];
						else $kint_com = $supplier_library[$row[csf("knitting_company")]];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("mst_id")]; ?>','<? echo $row[csf("is_posted_account")]; ?>')" style="cursor:pointer;">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="50" align="center" style="word-break:break-all">
								<p><? echo $row[csf("recv_number_prefix_num")]; ?>&nbsp;</p>
							</td>
							<td width="150" style="word-break:break-all">
								<p><? echo $kint_com; ?>&nbsp;</p>
							</td>
							<td width="70" align="center" style="word-break:break-all">
								<p><? echo $row[csf("challan_no")]; ?>&nbsp;</p>
							</td>
							<td width="70" align="center" style="word-break:break-all"><? if ($row[csf("receive_date")] != "" && $row[csf("receive_date")] != "0000-00-00") echo change_date_format($row[csf("receive_date")]); ?></td>
							<td width="40" align="center" style="word-break:break-all">
								<p><? echo $row[csf("year")]; ?>&nbsp;</p>
							</td>
							<td width="130" style="word-break:break-all">
								<p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p>
							</td>
							<td width="60" align="center" style="word-break:break-all">
								<p><? echo $row[csf("lot")]; ?>&nbsp;</p>
							</td>
							<td width="170" style="word-break:break-all">
								<p>
									<?
									echo $count_arr[$row[csf('yarn_count_id')]] . " " . $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_arr[$row[csf('color')]];
									?>
								</p>
							</td>
							<td width="80" align="right"><? echo number_format($row[csf("cons_quantity")], 2); ?></td>
							<td width="80" align="right"><? echo number_format($row[csf("cons_reject_qnty")], 2); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div style="margin-top:5px" align="center" valign="top"><input type="button" id="btn_print" class="formbutton" style="width:100px;" value="Print" onClick="popup_print()" /> </div>
<?
	exit();
}

if ($action == "populate_master_from_data") {
	$sql = "select id,recv_number,entry_form,item_category,company_id,receive_basis,receive_purpose,receive_date,booking_id,booking_no,booking_without_order,knitting_source,knitting_company,yarn_issue_challan_no,challan_no,store_id,location_id,buyer_id,exchange_rate,currency_id,supplier_id,lc_no,source,requisition_no,issue_id from inv_receive_master where id='$data'";
	// echo $sql; die;

	$res = sql_select($sql);
	$issue_id = $res[0][csf('issue_id')];
	$issue_buyer_id_arr = return_library_array("select id, buyer_id from inv_issue_master where id='$issue_id'", 'id', 'buyer_id');
	$buyer_arr 	= return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$customer_buyer_sql = sql_select("SELECT  a.REQUISITION_NO,c.customer_buyer from PPL_YARN_REQUISITION_ENTRY a, PPL_PLANNING_ENTRY_PLAN_DTLS b, FABRIC_SALES_ORDER_MST c where a.knit_id = b.dtls_id and b.po_id = c.id");
	foreach($customer_buyer_sql as $value)
	{
		$custbuyer_arr[$value['REQUISITION_NO']]['CUST_BUYER'] = $value['CUSTOMER_BUYER'];
	}

	foreach ($res as $row) {
		echo "set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);";
		echo "$('#txt_mst_id').val('" . $row[csf("id")] . "');\n";
		echo "$('#txt_return_no').val('" . $row[csf("recv_number")] . "');\n";
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		echo "$('#cbo_basis').val('" . $row[csf("receive_basis")] . "');\n";
		echo "active_inactive('" . $row[csf("receive_basis")] . "');\n";
		echo "$('#txt_booking_no').val('" . trim($row[csf("booking_no")]) . "');\n";
		echo "$('#txt_booking_id').val('" . $row[csf("booking_id")] . "');\n";
		echo "$('#hdn_req_no').val('" . $row[csf("requisition_nofrom")] . "');\n";
		echo "$('#booking_without_order').val('" . $row[csf("booking_without_order")] . "');\n";
		echo "$('#cbo_location').val('" . $row[csf("location_id")] . "');\n";
		echo "$('#cbo_knitting_source').val('" . $row[csf("knitting_source")] . "');\n";
		echo "$('#cbo_knitting_source').attr('disabled','disabled');\n";
		echo "load_drop_down( 'requires/yarn_issue_return_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_knit_com', 'knitting_company_td' );\n";
		echo "$('#cbo_knitting_company').val('" . $row[csf("knitting_company")] . "');\n";
		echo "$('#cbo_knitting_company').attr('disabled','disabled');\n";
		echo "$('#txt_return_date').val('" . change_date_format($row[csf("receive_date")]) . "');\n";
		echo "$('#txt_return_challan_no').val('" . $row[csf("challan_no")] . "');\n";
		echo "disable_enable_fields( 'cbo_company_id*cbo_basis*txt_booking_no', 1, '', '' );\n"; // disable true

		if ($row[csf("booking_without_order")] == 1 || $row[csf("booking_without_order")] == 3) {
			echo "$('#txt_return_qnty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_return_qnty').removeAttr('onDblClick','openmypage_po();');\n";
			echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
		} else {
			echo "$('#txt_return_qnty').attr('readonly','readonly');\n";
			echo "$('#txt_return_qnty').attr('onDblClick','openmypage_po();');\n";
			echo "$('#txt_return_qnty').attr('placeholder','Double Click To Search');\n";
		}

		if ($row[csf("item_category")] == 24) {
			echo "$('#cbo_knitting_company').prop('disabled',true);\n";
		}
		
		if($custbuyer_arr[$row['REQUISITION_NO']]['CUST_BUYER'])
		{
			echo "$('#txt_buyer_name').val('" . $buyer_arr[$custbuyer_arr[$row['REQUISITION_NO']]['CUST_BUYER']] . "');\n";
		}
		else
		{
			$issueBuyerid = $issue_buyer_id_arr[$row[csf("issue_id")]];
			echo "$('#txt_buyer_name').val('" . $buyer_arr[$issueBuyerid] . "');\n";
		}

		echo "load_drop_down( 'requires/yarn_issue_return_controller', " . $row[csf("issue_id")] . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_purpose', 'issue_purpose_td' );\n";
	}
	exit();
}

if ($action == "show_dtls_list_view") {
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$sql = "select a.recv_number,a.company_id,a.supplier_id,a.receive_date,a.item_category,a.recv_number,b.id,b.dyeing_color_id, b.cons_quantity, b.cons_reject_qnty, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_count_id,c.yarn_type,c.color, c.id as prod_id,c.lot
	from  inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	where a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and b.status_active=1  and a.id=$data";

	$result    = sql_select($sql);
	$i = 1;
	$rettotalQnty = 0;
	$rcvtotalQnty = 0;
	$rejtotalQnty = 0;
	$totalAmount = 0;
?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:980px" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>Return No</th>
				<th>Item Description</th>
				<th>Product ID</th>
				<th>Lot</th>
				<th>Dyeing Color</th>
				<th>Return Qty</th>
				<th>Reject Qty</th>
				<th>UOM</th>
				<th>Rate</th>
				<th>Return Value</th>
			</tr>
		</thead>
		<tbody>
			<?
			foreach ($result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$rettotalQnty += $row[csf("cons_quantity")];
				$rejtotalQnty += $row[csf("cons_reject_qnty")];
				$totalAmount += $row[csf("cons_amount")];
				$description = $count_arr[$row[csf('yarn_count_id')]] . " " . $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_arr[$row[csf('color')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")] . "**" . $description; ?>","child_form_input_data","requires/yarn_issue_return_controller")' style="cursor:pointer">
					<td width="30"><? echo $i; ?></td>
					<td width="100">
						<p><? echo $row[csf("recv_number")]; ?></p>
					</td>
					<td width="250">
						<p><? echo $description; ?> </p>
					</td>
					<td width="80">
						<p><? echo $row[csf("prod_id")]; ?></p>
					</td>
					<td width="80">
						<p><? echo $row[csf("lot")]; ?></p>
					</td>
					<td width="80">
						<p><? echo $color_arr[$row[csf("dyeing_color_id")]]; ?></p>
					</td>
					<td width="80" align="right">
						<p><? echo number_format($row[csf("cons_quantity")], 2, '.', ''); ?></p>
					</td>
					<td width="80" align="right">
						<p><? echo number_format($row[csf("cons_reject_qnty")], 2, '.', ''); ?></p>
					</td>
					<td width="60">
						<p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p>
					</td>
					<td width="80" align="right">
						<p><? echo number_format($row[csf("cons_rate")], 3); ?></p>
					</td>
					<td width="100" align="right">
						<p><? echo number_format($row[csf("cons_amount")], 2, '.', ''); ?></p>
					</td>
				</tr>
			<? $i++;
			} ?>
		<tfoot>
			<th colspan="6">Total</th>
			<th><? echo number_format($rettotalQnty, 2, '.', ''); ?></th>
			<th><? echo number_format($rejtotalQnty, 2, '.', ''); ?></th>
			<th colspan="2"></th>
			<th><? echo number_format($totalAmount, 2, '.', ''); ?></th>
		</tfoot>
		</tbody>
	</table>
<?
	exit();
}

if ($action == "child_form_input_data") {
	$data = explode('**', $data);
	$id = $data[0];
	$description = $data[1];
	$sql = "select b.id as prod_id,a.company_id, b.product_name_details, b.lot, a.id as tr_id, a.store_id, a.floor_id, a.room, a.rack,a.self,a.bin_box, a.issue_id,a.requisition_no, a.dyeing_color_id,a.cons_uom, a.cons_rate, a.cons_quantity,a.cons_reject_qnty, a.cons_amount,a.no_of_qty,a.issue_challan_no,a.remarks,a.no_bag,a.no_cone,a.adjust_allocation_qnty,a.adjust_allocation_str,a.adjust_to, b.supplier_id from inv_transaction a, product_details_master b where a.id=$id and a.status_active=1 and a.item_category=1 and transaction_type=4 and a.prod_id=b.id and b.status_active=1";
	//echo $sql;
	$result = sql_select($sql);
	foreach ($result as $row) {
		$issueid = $row[csf("issue_id")];
		$prod_id = $row[csf("prod_id")];
		$dyeing_color_id = $row[csf("dyeing_color_id")];
	}

	if ($issueid != "") {
		$dyeing_color_cond = ($dyeing_color_id != "") ? " and b.dyeing_color_id=$dyeing_color_id" : "";
		$issue_sql = sql_select("select a.id,a.issue_purpose,a.issue_basis,a.issue_date,a.booking_no, b.id as trans_id, b.requisition_no, receive_basis,b.pi_wo_batch_no,b.prod_id,b.cons_quantity,b.return_qnty, b.btb_lc_id from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.id=$issueid and b.prod_id =$prod_id and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dyeing_color_cond");

		$issueQtyArr = array();
		$issueReturnAbleQtyArr = array();
		$issueData = array();
		$btt_arr = array();
		foreach ($issue_sql as $row) {
			$issueData[$row[csf("id")]]['issue_purpose'] = $row[csf("issue_purpose")];
			$issueData[$row[csf("id")]]['issue_basis'] = $row[csf("issue_basis")];
			$issueData[$row[csf("id")]]['booking_no'] = $row[csf("booking_no")];
			$issueData[$row[csf("id")]]['trans_id'] = $row[csf("trans_id")];

			$issueReturnAbleQtyArr[$row[csf("id")]][$row[csf("prod_id")]] += $row[csf("return_qnty")];

			if ($row[csf("issue_basis")] == 1) {
				$booking_noArr[$row[csf("booking_no")]] = $row[csf("booking_no")];
				$issueQtyArr[$row[csf("id")]][$row[csf("booking_no")]][$row[csf("prod_id")]] += $row[csf("cons_quantity")];
				$btt_arr[$row[csf("id")]][$row[csf("booking_no")]][$row[csf("prod_id")]] = $row[csf("btb_lc_id")];
			} elseif ($row[csf("issue_basis")] == 2) {
				$issueQtyArr[$row[csf("id")]][$row[csf("prod_id")]] += $row[csf("cons_quantity")];
				$btt_arr[$row[csf("id")]][$row[csf("prod_id")]] = $row[csf("btb_lc_id")];
			} elseif ($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")] == 8) {
				$requisition_noArr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
				$issueQtyArr[$row[csf("id")]][$row[csf("prod_id")]] += $row[csf("cons_quantity")];
				$btt_arr[$row[csf("id")]][$row[csf("prod_id")]] = $row[csf("btb_lc_id")];
			}
		}

		$issue_retur_sql = sql_select("select b.issue_id,b.prod_id, sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty from inv_transaction b where b.issue_id =$issueid and b.prod_id=$prod_id and b.item_category=1 and b.transaction_type=4 and b.is_deleted=0 $dyeing_color_cond group by b.issue_id,b.prod_id");

		$issueReturnQtyArr = array();
		foreach ($issue_retur_sql as $row) {
			$issueReturnQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]] = ($row[csf("cons_quantity")] + $row[csf("cons_reject_qnty")]);
		}
	}
	//echo "<pre>";
	//print_r($issueData); die;

	foreach ($result as $row) {
		$row[csf("cons_quantity")] = number_format($row[csf("cons_quantity")], 2, '.', '');
		$issue_date = $issueData[$row[csf("issue_id")]]['issue_date'];
		$issue_purpose = $issueData[$row[csf("issue_id")]]['issue_purpose'];
		$issue_basis = $issueData[$row[csf("issue_id")]]['issue_basis'];
		$bin = (str_replace("'", "", $row[csf("bin_box")]) == "") ? 0 : $row[csf("bin_box")];
		echo "return_qnty_basis(" . $issue_purpose . ");\n";
		echo "$('#hide_issue_date').val('" . change_date_format($issue_date) . "');\n";
		echo "$('#txt_item_description').val('" . $description . "');\n";
		echo "$('#txt_prod_id').val('" . $row[csf("prod_id")] . "');\n";
		echo "$('#txt_supplier_id').val('" . $row[csf("supplier_id")] . "');\n";
		echo "$('#before_prod_id').val('" . $row[csf("prod_id")] . "');\n";
		echo "$('#txt_dyeing_color_id').val(" . $row[csf("dyeing_color_id")] . ");\n";
		echo "$('#txt_yarn_lot').val('" . $row[csf("lot")] . "');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller*1', 'store','store_td', '" . $row[csf('company_id')] . "','" . "',this.value);\n";
		echo "$('#cbo_store_name').val('" . $row[csf("store_id")] . "');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'floor','floor_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "',this.value);\n";
		echo "$('#cbo_floor').val('" . $row[csf("floor_id")] . "');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'room','room_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "',this.value);\n";
		echo "$('#cbo_room').val('" . $row[csf("room")] . "');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'rack','rack_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "',this.value);\n";
		echo "$('#txt_rack').val('" . $row[csf("rack")] . "');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'shelf','shelf_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "','" . $row[csf('rack')] . "',this.value);\n";
		echo "$('#txt_shelf').val('" . $row[csf("self")] . "');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'bin','bin_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "','" . $row[csf('rack')] . "','" . $row[csf('self')] . "',this.value);\n";
		echo "$('#cbo_bin').val('" . $bin . "');\n";
		echo "storeUpdateUptoDisable();\n";

		echo "$('#txt_return_qnty').val('" . $row[csf("cons_quantity")] . "');\n";
		echo "$('#txt_reject_qnty').val('" . $row[csf("cons_reject_qnty")] . "');\n";
		echo "$('#txt_processloss_qnty').val('" . $row[csf("no_of_qty")] . "');\n";
		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";
		echo "$('#txt_bag').val('" . $row[csf("no_bag")] . "');\n";
		echo "$('#txt_cone').val('" . $row[csf("no_cone")] . "');\n";
		echo "$('#cbo_uom').val('" . $row[csf("cons_uom")] . "');\n";
		echo "$('#txt_issue_id').val('" . $row[csf("issue_id")] . "');\n";
		echo "$('#txt_requisition_no').val('" . $row[csf("requisition_no")] . "');\n";

		if ($issue_basis == 1) {
			foreach ($booking_noArr as $bookingNo) {
				$totalIssued = $issueQtyArr[$row[csf("issue_id")]][$bookingNo][$row[csf("prod_id")]];
				$btb_lc_id = $btt_arr[$row[csf("issue_id")]][$bookingNo][$row[csf("prod_id")]];
			}
		} elseif ($issue_basis == 2) {
			$totalIssued = $issueQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]];
			$btb_lc_id = $btt_arr[$row[csf("issue_id")]][$row[csf("prod_id")]];
		} elseif ($issue_basis == 3 || $issue_basis == 8) {
			$totalIssued = $issueQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]];
			$btb_lc_id = $btt_arr[$row[csf("issue_id")]][$row[csf("prod_id")]];

			if ($issue_basis == 3) {
				echo "$('#booking_no').val('" . $row[csf("booking_no")] . "');\n";
				echo "$('#hdn_req_no').val('" . $row[csf("requisition_no")] . "');\n";
			} else {
				echo "$('#hdn_req_no').val('" . $row[csf("requisition_no")] . "');\n";
			}
		}

		if ($totalIssued == "")
			$totalIssued = 0;

		$totalIssued = number_format($totalIssued, 2, '.', '');
		echo "$('#txt_issue_qnty').val('" . $totalIssued . "');\n";

		$totalReturn = number_format($issueReturnQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]], 2, '.', '');
		echo "$('#txt_total_return_display').val('" . $totalReturn . "');\n";
		$netUsed = number_format($totalIssued - $totalReturn, 2, '.', '');
		echo "$('#txt_net_used').val('" . $netUsed . "');\n";
		echo "$('#hide_net_used').val('" . $row[csf("cons_quantity")] . "');\n";
		$totalreturnable = number_format($issueReturnAbleQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]], 2, '.', '');
		echo "$('#txt_returnable_qnty').val('" . $totalreturnable . "');\n";
		$returnableBl = number_format($totalIssued - $totalReturn, 2, '.', '');
		echo "$('#txt_returnable_bl_qnty').val('" . $returnableBl . "');\n";
		if ($totalReturn == "")
			$totalReturn = 0;
		else
			$totalReturn = number_format($totalReturn - $row[csf("cons_quantity")] - $row[csf("cons_reject_qnty")], 2, '.', '');
		echo "$('#txt_total_return').val('" . $totalReturn . "');\n";
		echo "$('#txt_rate').val('" . $row[csf("cons_rate")] . "');\n";
		echo "$('#txt_amount').val(" . $row[csf("cons_amount")] . ");\n";
		echo "$('#txt_issue_challan_no').val('" . $row[csf("issue_challan_no")] . "');\n";
		echo "$('#update_id').val('" . $row[csf("tr_id")] . "');\n";
		//issue qnty popup data arrange
		$sqlIN = sql_select("select po_breakdown_id,quantity,reject_qty,is_sales,processloss_qty from order_wise_pro_details where trans_id=" . $row[csf("tr_id")] . " and entry_form=9 and trans_type=4 order by po_breakdown_id");
		$poWithValue = "";
		$poWithValueReject = "";
		$poID = "";

		foreach ($sqlIN as $res) {
			if ($poWithValue != "") $poWithValue .= ",";
			if ($poID != "") $poID .= ",";
			$poWithValue .= $res[csf("po_breakdown_id")] . "**" . $res[csf("quantity")] . "**" . $res[csf("reject_qty")] . "**" . $res[csf("is_sales")] . "**" . $res[csf("processloss_qty")];
			$poID .= $res[csf("po_breakdown_id")];
		}

		echo "$('#save_data').val('" . $poWithValue . "');\n";
		echo "$('#save_data_pre').val('" . $poWithValue . "');\n";
		echo "$('#all_po_id').val('" . $poID . "');\n";

		if ($issue_purpose == 7 || $issue_purpose == 15 || $issue_purpose == 38 || $issue_purpose == 46) {
			$booking_no = $issueData[$row[csf("issue_id")]]['booking_no'];

			$wo_sql = sql_select("select ydw_no,booking_without_order,is_sales from wo_yarn_dyeing_mst where ydw_no='" . $booking_no . "' and status_active=1 and is_deleted=0");

			$booking_without_order = $wo_sql[0][csf('booking_without_order')];
			$is_sales = $wo_sql[0][csf('is_sales')];

			if ($booking_without_order == 2 && $is_sales != 1) {
				echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
				echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
				echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
				echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
			}
		}

		if (($issue_basis == 2) && ($issue_purpose == 54)) {
			echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
			echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
			echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
			echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
		}

		//for sample without order
		$trans_id = $issueData[$row[csf("issue_id")]]['trans_id'];
		$sqlIN = sql_select("select po_breakdown_id, quantity, returnable_qnty from order_wise_pro_details where trans_id='" . $trans_id . "' and entry_form=3 and trans_type=2 and status_active=1");
		if (count($sqlIN) == 0) {
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick');\n";
		}
	}

	//for lc no
	if ($btb_lc_id > 0) {
		$btb_lc_num = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $btb_lc_id . "'", "lc_number");
		echo "$('#txt_btb').val('" . $btb_lc_num . "');\n";
	}

	echo "set_button_status(1, permission, 'fnc_yarn_issue_return_entry',1,1);\n";
	exit();
}

if ($action == "yarn_issue_return_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	// echo "<pre>";
	// print_r($data);
	$company = $data[0];
	$location = $data[6];
	$buyer_name = trim($location = $data[7]);

	$sql_iss_order = "SELECT c.grouping as int_ref_no from inv_transaction a, order_wise_pro_details b, wo_po_break_down c
	where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=4 and a.mst_id=$data[5]";

	//echo $sql_iss_order;die;
	$sql_iss_order = sql_select($sql_iss_order);
	foreach ($sql_iss_order as $key => $value) {
		$int_ref .= $value[csf('int_ref_no')] . ',';
	}
	$internal_ref_no = implode(",", array_unique(explode(",", chop($int_ref, ','))));

	if ($data[3] == 3 && $data[4] != "") {
		$requires_cond = "and c.requisition_no =$data[4]";

		$booking_by_requno_arr = return_library_array("select a.booking_no, c.requisition_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where  a.id=b.mst_id and b.id=c.knit_id and a.company_id=$data[0] $requires_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ", 'requisition_no', 'booking_no');
	}

	$sql = " SELECT id, recv_number, receive_basis, booking_no, booking_id, knitting_source, knitting_company, challan_no, receive_date, inserted_by from  inv_receive_master where recv_number='$data[1]' and entry_form=9 and item_category=1";
	// echo $sql;
	$dataArray 			= sql_select($sql);
	$company_library 	= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library 	= return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library 		= return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$buyer_arr 			= return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$count_arr 			= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_arr 			= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$user_arr 			= return_library_array("select id, user_full_name from USER_PASSWD where status_active=1 and is_deleted=0", 'id', 'user_full_name');

	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
	<div style="width:1230px;">
		<table width="1200" cellspacing="0" align="left">
			<tr>
				<td colspan="9" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="9" align="center" style="font-size:14px">
					<?
					echo $com_dtls[1];
					?>
				</td>
			</tr>
			<tr>
				<td colspan="18" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?> Challan</strong></td>
			</tr>
			<tr>
				<td width="170"><strong>Return ID:</strong></td>
				<td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="150"><strong>Basis:</strong></td>
				<td width="250px"><? echo $issue_basis[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="150"><strong>F.Book/Req. No:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Return Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Knitting Com :</strong></td>
				<td width="175px"><? if ($dataArray[0][csf('knitting_source')] == 1) echo $company_library[$dataArray[0][csf('knitting_company')]];
									elseif ($dataArray[0][csf('knitting_source')] == 3) echo $supplier_library[$dataArray[0][csf('knitting_company')]]; ?></td>
				<td><strong>Return Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Return Challan:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<? if ($data[3] == 3) {
				?>
					<td><strong>Booking No:</strong></td>
					<td width="175px"><? echo  $booking_by_requno_arr[$dataArray[0][csf('booking_no')]]; ?></td>
				<?
				}
				?>
				<td><strong>Internal Ref. No: </strong></td>
				<td width="175px"><? echo $internal_ref_no; ?></td>
				<td><strong>Buyer:</strong></td>
				<td width="175px"><? echo $buyer_name; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="220" align="center">Item Description</th>
					<th width="60" align="center">Yarn Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="80" align="center">Returned Qnty</th>
					<th width="80" align="center">Rejected Qnty</th>
					<th width="100" align="center">Store</th>
					<th width="100" align="center">No. of Cone</th>
					<th width="100" align="center">No. of Bag</th>
					<th width="" align="center">Remarks</th>
				</thead>
				<tbody>
					<?
					$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

					$i = 1;
					$mst_id = $dataArray[0][csf('id')];

					$sql_dtls = "SELECT a.id as pd_id, a.product_name_details,a.yarn_comp_type1st,a.yarn_comp_percent1st,a.yarn_count_id,a.yarn_type,a.color, a.lot, b.id, b.cons_uom, b.cons_quantity, b.store_id, b.cons_reject_qnty, b.remarks, b.no_bag, b.no_cone from product_details_master a, inv_transaction b where a.id=b.prod_id and b.transaction_type=4 and b.item_category=1 and b.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0";

					$sql_result = sql_select($sql_dtls);
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$rej_quantity += $row[csf('cons_reject_qnty')];
						$cons_quantity_sum += $row[csf('cons_quantity')];
						$description =  $count_arr[$row[csf('yarn_count_id')]] . " " . $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_arr[$row[csf('color')]];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td>
								<p><? echo $description; ?></p>
							</td>
							<td align="center"><? echo $row[csf("lot")]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
							<td align="center" align="right"><? echo number_format($row[csf("cons_quantity")], 2); ?></td>
							<td align="center" align="right"><? echo number_format($row[csf("cons_reject_qnty")], 2); ?></td>
							<td align="center"><? echo $store_arr[$row[csf("store_id")]]; ?></td>
							<td align="center"><? echo $row[csf("no_cone")]; ?></td>
							<td align="center"><? echo $row[csf("no_bag")]; ?></td>
							<td align="center"><? echo $row[csf("remarks")]; ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" align="right">Total :</td>
						<td align="center"><? echo number_format($cons_quantity_sum, 2); ?></td>
						<td align="center"><? echo number_format($rej_quantity, 2); ?></td>
						<td colspan="4">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(37, $data[0], "1200px", "", "", $dataArray[0]['INSERTED_BY']);
			?>
		</div>
	</div>
<?
	exit();
}


if ($action == "yarn_issue_return_print2") {
	extract($_REQUEST);
	$data = explode('*', $data);

	$company = $data[0];
	$location = $data[6];
	$buyer_name = trim($location = $data[7]);
	$store_library 		= return_library_array("select id, store_name from  lib_store_location where status_active=1 and is_deleted=0", "id", "store_name");

	$sql_iss_order = "SELECT a.id, d.job_no, a.store_id, c.po_number, d.style_ref_no  from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
	where a.id=b.trans_id and b.po_breakdown_id=c.id and d.job_no = c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=4 and a.mst_id=$data[5]
	group  by a.id, d.job_no, a.store_id, c.po_number, d.style_ref_no order by a.id";
	$sql_iss_order = sql_select($sql_iss_order);
	$job_arr = [];
	$store = [];
	foreach ($sql_iss_order as $key => $value) {
		$store[$value[csf('store_id')]] = $store_library[$value[csf('store_id')]];
		$job_arr[$value[csf('id')]]['style'][$key] = $value[csf('style_ref_no')];
		$job_arr[$value[csf('id')]]['order'][$key] = $value[csf('po_number')];
	}

	$program_arr = [];
	if ($data[3] == 3 && $data[4] != "") {
		$requires_cond = "and c.requisition_no =$data[4]";
		$program_arr = return_library_array("select b.id, c.requisition_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where  a.id=b.mst_id and b.id=c.knit_id and a.company_id=$data[0] $requires_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ", 'requisition_no', 'id');
	}

	if ($data[3] == 8 && $data[4] != "") {
		$plan_sql = "SELECT a.requisition_no, c.po_id as po_id,b.is_sales,d.id from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_yarn_demand_entry_dtls d where d.mst_id=$data[4] and a.requisition_no=d.requisition_no and a.knit_id=b.id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
		//echo $plan_sql;
		$plan_rslt = sql_select($plan_sql);

		$demand_details_array = array();
		$poIdArr = array();
		foreach ($plan_rslt as $row) {
			$demand_details_array[$row[csf("requisition_no")]]["po_id"] .= $row[csf("po_id")] . ',';
			$demand_details_array[$row[csf("requisition_no")]]["is_sales"] = $row[csf("is_sales")];
			array_push($poIdArr, $row[csf("po_id")]);
		}
		unset($plan_rslt);
		//var_dump($demand_details_array);
	}

	$sales_order_array = array();
	$sales_order_sql = sql_select("SELECT a.id,a.sales_booking_no, a.job_no,a.style_ref_no, a.buyer_id,a.within_group from fabric_sales_order_mst a where a.status_active = 1 and a.is_deleted = 0 and a.company_id=$company " . where_con_using_array($poIdArr, 0, 'a.id') . "");
	foreach ($sales_order_sql as $row) {
		$sales_order_array[$row[csf('id')]]['sales_order_no'] = $row[csf('job_no')];
		$sales_order_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
		$sales_order_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$sales_order_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$sales_order_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
	}

	$sql = " SELECT id, recv_number, receive_basis, booking_no, booking_id, knitting_source, knitting_company, challan_no, to_char(receive_date, 'dd-mm-YYYY') as receive_date from  inv_receive_master where recv_number='$data[1]' and entry_form=9 and item_category=1";
	$dataArray 			= sql_select($sql);
	$company_library 	= return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$supplier_library 	= return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$count_arr 			= return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$brand_arr 			= return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$color_arr 			= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
	<div style="width:1150px;">
		<table width="1150" cellspacing="0" align="left">
			<tr>
				<td colspan="12" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="12" align="center" style="font-size:14px">
					<?
					echo $com_dtls[1];
					?>
				</td>
			</tr>
			<tr>
				<td colspan="12" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?> Challan</strong></td>
			</tr>
			<tr>
				<td valign="center" colspan="6" style="padding: 3px 0px;">
					<strong>Delivery From: </strong><? if ($dataArray[0][csf('knitting_source')] == 1) echo $company_library[$dataArray[0][csf('knitting_company')]];
													elseif ($dataArray[0][csf('knitting_source')] == 3) echo $supplier_library[$dataArray[0][csf('knitting_company')]]; ?>
				</td>
				<td valign="center" colspan="6" rowspan="2" align="right">
					<strong>Challan No.: </strong><?= $dataArray[0][csf('challan_no')]; ?>
				</td>
			</tr>
			<tr>
				<td valign="center" colspan="6" style="padding: 3px 0px;">
					<strong>Delivery To: </strong><?= implode(", ", $store) ?>
				</td>
			</tr>
			<tr>
				<td valign="center" colspan="6" style="padding: 3px 0px;">
					<strong>Date: </strong><?= $dataArray[0][csf('receive_date')]; ?>
				</td>
				<td valign="center" colspan="6" style="padding: 3px 0px;" align="right">
					<strong>Print Date: </strong><?= date("d-m-Y") ?>
				</td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1190" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<th width="90" align="center">Program No.</th>
						<th width="90" align="center">Yarn Count</th>
						<th width="120" align="center">Composition</th>
						<th width="110" align="center">Brand Name</th>
						<th width="70" align="center">Lot No.</th>
						<th width="130" align="center">Style No.</th>
						<th width="150" align="center">Order No.</th>
						<th width="100" align="center">Color</th>
						<th width="110" align="center">Qty. in KG</th>
						<th width="80" align="center">No. of Cone</th>
						<th align="center">No. of Bag</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$mst_id = $dataArray[0][csf('id')];

					$sql_dtls = "SELECT b.id, a.id as pd_id, a.brand, b.requisition_no, a.product_name_details,a.yarn_comp_type1st,a.yarn_comp_percent1st,a.yarn_count_id,a.yarn_type,a.color, a.lot, b.id, b.cons_uom, b.cons_quantity, b.store_id, b.cons_reject_qnty, b.remarks, b.no_bag, b.no_cone, b.receive_basis, b.demand_id from product_details_master a, inv_transaction b where a.id=b.prod_id and b.transaction_type=4 and b.item_category=1 and b.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0";
					//echo $sql_dtls;
					$sql_result = sql_select($sql_dtls);
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$po_no = $style_ref = '';

						if ($row[csf("receive_basis")] == 8) {
							$is_sales = $demand_details_array[$row[csf("requisition_no")]]["is_sales"];
							//var_dump($is_sales);
							if ($is_sales == 1) {
								$po_ids_arr = explode(",", $demand_details_array[$row[csf("requisition_no")]]["po_id"]);
								$po_no = $sales_order_array[$po_ids_arr[0]]['sales_order_no'];
								$style_ref = $sales_order_array[$po_ids_arr[0]]['style_ref_no'];
							}
						} else {
							$style_ref = implode(", ", array_unique($job_arr[$row[csf("id")]]['style']));
							$po_no = implode(", ", array_unique($job_arr[$row[csf("id")]]['order']));
						}

						$cons_quantity_sum += $row[csf('cons_quantity')];
						$composition_sst = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% " . $yarn_type[$row[csf('yarn_type')]];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center">
								<p><? echo $program_arr[$row[csf("requisition_no")]]; ?></p>
							</td>
							<td align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
							<td><? echo $composition_sst; ?></td>
							<td><? echo $brand_arr[$row[csf('brand')]]; ?></td>
							<td align="center"><? echo $row[csf("lot")]; ?></td>
							<td align="center"><? echo $style_ref; ?></td>
							<td align="center"><? echo $po_no; ?></td>
							<td align="center"><?= $color_arr[$row[csf('color')]] ?></td>
							<td align="right"><? echo number_format($row[csf('cons_quantity')], 2); ?></td>
							<td align="center"><? echo $row[csf("no_cone")]; ?></td>
							<td align="center"><? echo $row[csf("no_bag")]; ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="9" align="right">Total :</td>
						<td align="right"><? echo number_format($cons_quantity_sum, 2); ?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(37, $data[0], "1150px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "yarn_issue_return_print3") {
	extract($_REQUEST);
	$data = explode('*', $data);
	// echo "<pre>";
	// print_r($data);
	$company = $data[0];
	$location = $data[6];
	$buyer_name = trim($location = $data[7]);

	$sql_iss_order = "SELECT c.grouping as int_ref_no from inv_transaction a, order_wise_pro_details b, wo_po_break_down c
	where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=4 and a.mst_id=$data[5]";

	//echo $sql_iss_order;die;
	$sql_iss_order = sql_select($sql_iss_order);
	foreach ($sql_iss_order as $key => $value) {
		$int_ref .= $value[csf('int_ref_no')] . ',';
	}
	$internal_ref_no = implode(",", array_unique(explode(",", chop($int_ref, ','))));

	if (($data[3] == 3) && $data[4] != "") {
		$requires_cond = "and c.requisition_no =$data[4]";

		// $booking_by_requno_arr=return_library_array( "SELECT a.booking_no, c.requisition_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where  a.id=b.mst_id and b.id=c.knit_id and a.company_id=$data[0] $requires_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ",'requisition_no','booking_no');

		$sql_req_info = "SELECT a.booking_no, c.requisition_no, d.job_no, d.style_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c, fabric_sales_order_mst d where  a.id=b.mst_id and b.id=c.knit_id and a.booking_no=d.sales_booking_no and a.company_id=$data[0] $requires_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
		//echo $sql_req_info;
		$rslt_req_info = sql_select($sql_req_info);
		$booking_by_requno_arr = array();
		foreach ($rslt_req_info as $req_row) {
			$booking_by_requno_arr[$req_row[csf("requisition_no")]]['booking_no'] = $req_row[csf("booking_no")];
			$booking_by_requno_arr[$req_row[csf("requisition_no")]]['job_no'] = $req_row[csf("job_no")];
			$booking_by_requno_arr[$req_row[csf("requisition_no")]]['style_ref_no'] = $req_row[csf("style_ref_no")];
		}
		//var_dump($booking_by_requno_arr);

	}

	$sql = " SELECT id, recv_number, receive_basis, booking_no, booking_id, knitting_source, knitting_company, challan_no, receive_date, issue_id from inv_receive_master where recv_number='$data[1]' and entry_form=9 and item_category=1";

	$dataArray 			= sql_select($sql);
	$company_library 	= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library 	= return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library 		= return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$buyer_arr 			= return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$count_arr 			= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_arr 			= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$issue_id = $dataArray[0][csf('issue_id')];


	$yarn_issue_no_arr = return_library_array("SELECT a.id, a.issue_number from inv_issue_master a where a.company_id=$data[0] and a.id=$issue_id and a.status_active=1 and a.is_deleted=0 ", 'id', 'issue_number');

	$yarn_issue_supplier_arr = return_library_array("SELECT a.id, a.supplier_id from inv_issue_master a where a.company_id=$data[0] and a.id=$issue_id and a.status_active=1 and a.is_deleted=0 ", 'id', 'supplier_id');

	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
	<div style="width:1230px;">
		<table width="1200" cellspacing="0" align="left">
			<tr>
				<td colspan="9" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="9" align="center" style="font-size:14px">
					<?
					echo $com_dtls[1];
					?>
				</td>
			</tr>
			<tr>
				<td colspan="18" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?> Challan</strong></td>
			</tr>
		</table>
		<table align="left" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
			<tr>
				<td width="170"><strong>Return ID</strong></td>
				<td width="200px">: <? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="150"><strong>Basis</strong></td>
				<td width="250px">: <? echo $issue_basis[$dataArray[0][csf('receive_basis')]]; ?></td>
				<? if ($data[3] == 3) {
				?>
					<td width="150"><strong>Booking No</strong></td>
					<td width="175px">: <? echo  $booking_by_requno_arr[$dataArray[0][csf('booking_no')]]['booking_no']; ?></td>
				<?
				}
				?>

				<td width="150"><strong>Return Date</strong></td>
				<td width="175px">: <? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Return Source</strong></td>
				<td width="175px">: <? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Knitting Com </strong></td>
				<td width="175px">: <? if ($dataArray[0][csf('knitting_source')] == 1) echo $company_library[$dataArray[0][csf('knitting_company')]];
									elseif ($dataArray[0][csf('knitting_source')] == 3) echo $supplier_library[$dataArray[0][csf('knitting_company')]]; ?></td>
				<td><strong>Sales Order No</strong></td>
				<td width="175px">: <? echo $booking_by_requno_arr[$dataArray[0][csf('booking_no')]]['job_no']; ?></td>
				<td width="150"><strong>F.Book/Req. No</strong></td>
				<td width="175px">: <? echo $dataArray[0][csf('booking_no')]; ?>
				</td>
			</tr>
			<tr>
				<td width="170"><strong>Return Challan</strong></td>
				<td width="175px">: <? echo $dataArray[0][csf('challan_no')]; ?></td>
				<? if ($data[3] == 3) {
				?>
					<td width="170"><strong>Issue Challan No</strong></td>
					<td width="175px">: <? echo $yarn_issue_no_arr[$dataArray[0][csf('issue_id')]]; ?></td>
				<?
				}
				?>

				<td width="170"><strong>Buyer</strong></td>
				<td width="175px">: <? echo $buyer_name; ?></td>
				<td width="170"><strong>Style Ref. No </strong></td>
				<td width="175px">: <? echo $booking_by_requno_arr[$dataArray[0][csf('booking_no')]]['style_ref_no']; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1150" border="1" rules="all" class="rpt_table" style="margin-top:10px">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="220" align="center">Item Description</th>
					<th width="60" align="center">Yarn Lot</th>
					<th width="100" align="center">Yarn Supplier</th>
					<th width="80" align="center">Returned Qnty</th>
					<th width="80" align="center">Rejected Qnty</th>
					<th width="100" align="center">UOM</th>
					<th width="100" align="center">No. of Cone</th>
					<th width="100" align="center">No. of Bag</th>
					<th width="" align="center">Remarks</th>
				</thead>
				<tbody>
					<?
					$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

					$i = 1;
					$mst_id = $dataArray[0][csf('id')];

					$sql_dtls = "SELECT a.id as pd_id, a.product_name_details,a.yarn_comp_type1st,a.yarn_comp_percent1st,a.yarn_count_id,a.yarn_type,a.color, a.lot, b.id,b.issue_id, b.cons_uom, b.cons_quantity, b.store_id, b.cons_reject_qnty, b.remarks, b.no_bag, b.no_cone from product_details_master a, inv_transaction b where a.id=b.prod_id and b.transaction_type=4 and b.item_category=1 and b.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0";
					//echo  $sql_dtls;
					$sql_result = sql_select($sql_dtls);
					foreach ($sql_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$rej_quantity += $row[csf('cons_reject_qnty')];
						$cons_quantity_sum += $row[csf('cons_quantity')];
						$description =  $count_arr[$row[csf('yarn_count_id')]] . " " . $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% " . $yarn_type[$row[csf('yarn_type')]] . " " . $color_arr[$row[csf('color')]];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td>
								<p><? echo $description; ?></p>
							</td>
							<td align="center"><? echo $row[csf("lot")]; ?></td>
							<td align="center"><? echo $supplier_library[$yarn_issue_supplier_arr[$row[csf("issue_id")]]] ?></td>
							<td align="center" align="right"><? echo number_format($row[csf("cons_quantity")], 2); ?></td>
							<td align="center" align="right"><? echo number_format($row[csf("cons_reject_qnty")], 2); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
							<td align="center"><? echo $row[csf("no_cone")]; ?></td>
							<td align="center"><? echo $row[csf("no_bag")]; ?></td>
							<td align="center"><? echo $row[csf("remarks")]; ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5" align="right">Total :</td>
						<td align="center"><? echo number_format($cons_quantity_sum, 2); ?></td>
						<td align="center"><? echo number_format($rej_quantity, 2); ?></td>
						<td colspan="4">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(37, $data[0], "1200px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "return_multy_number_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}


		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				var attrData = $('#tr_' + i).attr('onclick');
				var splitArr = attrData.split("'");
				js_set_value(splitArr[1]);
			}
		}

		var selected_id = Array();
		var selected_name = Array();

		function js_set_value(mrr) {
			var splitArr = mrr.split("_");
			$("#hidden_return_number").val(splitArr[1]); // mrr number
			$("#hidden_return_id").val(splitArr[2]); // id

			toggle(document.getElementById('tr_' + splitArr[0]), '#FFFFCC');

			if (jQuery.inArray(splitArr[2], selected_id) == -1) {
				selected_name.push(splitArr[1]);
				selected_id.push(splitArr[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == splitArr[2]) break;
				}
				selected_name.splice(i, 1);
				selected_id.splice(i, 1);
			}

			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hidden_return_id').val(id);
			$('#hidden_return_number').val(name);
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="120" class="must_entry_caption">Working Company</th>
							<th width="180">Search By</th>
							<th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								//echo $return_source."test".$working_company;
								if ($return_source == 1) {
									$sql = "select id,company_name as working_company from lib_company where status_active=1 and is_deleted=0";
								} else {
									$sql = "select id,supplier_name as working_company from lib_supplier where status_active=1 and is_deleted=0";
								}
								echo create_drop_down("cbo_return_to", 120, "$sql", "id,working_company", 1, "-- Select --", $working_company, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1 => 'Return Number');
								//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by, "", 0, "--Select--", "", 1, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_return_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_multy_return_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-------->
								<!-- ---------END------------->
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>

			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_multy_return_search_list_view") {
	echo '<input type="hidden" id="hidden_return_number" value="" /><input type="hidden" id="hidden_return_id" value="" />';

	$ex_data = explode("_", $data);

	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$return_to = $ex_data[5];
	$cbo_year = $ex_data[6];


	$sql_cond = "";
	if ($search_by == 1) {
		if ($search_common != "") $sql_cond .= " and a.recv_number like '%$search_common'";
	}

	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.receive_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.receive_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	} else {

		if (trim($cbo_year) != 0) {

			if ($db_type == 0) {
				$sql_cond .= " and YEAR(a.insert_date)=$cbo_year";
			} else if ($db_type == 2) {
				$sql_cond .= " and to_char(a.insert_date,'YYYY')=$cbo_year";
			}
		}
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	if (str_replace("'", "", $return_to == 0)) {
		echo "<p style='font-size:25px; color:#F00'>Please Select Working Company.</p>";
		die;
	} else {
		$working_comp_con = " and a.knitting_company=$return_to";
	}

	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.company_id, a.supplier_id,a.receive_date, a.item_category, a.issue_id,a.challan_no, sum(b.cons_quantity)as cons_quantity,a.is_posted_account
	from inv_receive_master a, inv_transaction b
	where a.id=b.mst_id and b.transaction_type=4 and a.status_active=1 and a.item_category=1 and b.item_category=1 and a.entry_form=9 $working_comp_con $sql_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.company_id, a.supplier_id, a.receive_date, a.item_category, a.issue_id, a.challan_no, a.insert_date,a.is_posted_account order by a.id";

	//echo $sql;
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

	$arr = array(2 => $company_arr, 3 => $supplier_arr);

	echo create_list_view("list_view", "Return No, Year, Company Name, Supplier, Return Date,Return Qty,Issue Challan No", "70,60,150,170,80,100,150", "850", "230", 0, $sql, "js_set_value", "recv_number,id,issue_id,challan_no", "1", 1, "0,0,company_id,supplier_id,0,0,0", $arr, "recv_number_prefix_num,year,company_id,supplier_id,receive_date,cons_quantity,challan_no", "", "", "0,0,0,0,3,1,0", "", 1);
	exit();
}

if ($action == "yarn_issue_multy_return_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$recv_number = str_replace(",", "','", $data[1]);
	$return_number_arr = explode(",", $data[1]);
	$issue_return_ids = $data[2];
	//$working_company=$data[4];
	$return_source = $data[5];

	if ($issue_return_ids != "") {
		$sql_issue_rtn = "SELECT a.company_id,a.recv_number, a.receive_basis, a.receive_purpose, a.receive_date, a.booking_id, a.booking_no, a.booking_without_order, a.knitting_source, a.knitting_company, a.issue_id, a.challan_no, a.challan_date, a.buyer_id, a.source,b.id as trans_id, b.pi_wo_batch_no, b.prod_id, b.transaction_date, b.store_id, b.floor_id, b.room, sum(b.cons_quantity) as return_qnty , sum(b.cons_reject_qnty) as reject_qnty, b.no_bag,b.no_cone, b.remarks,c.detarmination_id, c.lot,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_type2nd,c.yarn_comp_percent1st,c.yarn_type, c.color, c.supplier_id FROM inv_receive_master a, inv_transaction b, product_details_master c WHERE a.id=b.mst_id and b.prod_id=c.id and a.entry_form=9 and a.item_category=1 and a.id in($issue_return_ids) and a.status_active=1 and a.is_deleted=0 and b.item_category=1 and b.transaction_type=4 and b.company_id='$data[0]' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id,a.recv_number, a.receive_basis, a.receive_purpose, a.receive_date, a.booking_id, a.booking_no, a.booking_without_order, a.knitting_source, a.knitting_company, a.issue_id, a.challan_no, a.challan_date, a.buyer_id, a.source,b.id, b.pi_wo_batch_no, b.prod_id, b.transaction_date, b.store_id, b.floor_id, b.room, b.no_bag,b.no_cone, b.remarks,c.detarmination_id, c.lot,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_type2nd,c.yarn_comp_percent1st,c.yarn_type, c.color, c.supplier_id";
		//echo $sql_issue_rtn;
		//order_wise_pro_details
		$main_squery_result = sql_select($sql_issue_rtn);
		foreach ($main_squery_result as $row) {
			$trans_id .= $row[csf("trans_id")] . ",";

			if ($row[csf("receive_basis")] == 3) {
				$requisition_no .= $row[csf("booking_no")] . ",";
			}

			$supplier_id .= $row[csf("supplier_id")] . ",";
			$store_id .= $row[csf("store_id")] . ",";
			$yarn_count_id .= $row[csf("yarn_count_id")] . ",";
			$color .= $row[csf("color")] . ",";
		}

		$store_ids = implode(",", array_unique(explode(",", chop($store_id, ','))));
		$supplier_ids = implode(",", array_unique(explode(",", chop($supplier_id, ','))));
		$yarn_count_ids = implode(",", array_unique(explode(",", chop($yarn_count_id, ','))));
		$colorids = implode(",", array_unique(explode(",", chop($color, ','))));

		$trans_ids = implode(",", array_unique(explode(",", chop($trans_id, ','))));
		$requisition_numbers = implode(",", array_unique(explode(",", chop($requisition_no, ','))));


		$company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");

		if ($store_ids != "") {
			$store_library = return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0 and id in($store_ids)", "id", "store_name");
		}
		if ($supplier_ids != "") {
			$supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0 and id in ($supplier_ids)", "id", "supplier_name");
		}
		if ($yarn_count_ids != "") {
			$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 and id in ($yarn_count_ids)", "id", "yarn_count");
		}
		if ($colorids != "") {
			$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($colorids)", "id", "color_name");
		}

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
		$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');

		if ($trans_ids != "") {
			$job_sql = "select a.trans_id, a.po_breakdown_id,c.buyer_name, c.job_no, c.style_ref_no,b.grouping from order_wise_pro_details a , wo_po_break_down b , wo_po_details_master c where a.trans_id in($trans_ids) and  a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and c.company_name='$data[0]' and c.is_deleted=0 and c.status_active=1 group by a.trans_id,a.po_breakdown_id,c.job_no,c.style_ref_no,c.buyer_name,b.grouping order by c.job_no,a.po_breakdown_id";

			$job_result_Array = sql_select($job_sql);

			if (count($job_result_Array) > 0) {
				$job_alldata_arr = array();
				foreach ($job_result_Array as $row) {
					$tot_rows++;

					$job_alldata_arr[$row[csf("trans_id")]]['job_no']   	= $row[csf("job_no")];
					$job_alldata_arr[$row[csf("trans_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
					$job_alldata_arr[$row[csf("trans_id")]]['buyer_name']   = $row[csf("buyer_name")];

					if (!empty($job_alldata_arr[$row[csf("trans_id")]]['grouping'])) {
						$job_alldata_arr[$row[csf("trans_id")]]['grouping'] .= "," . $row[csf("grouping")];
					} else {
						$job_alldata_arr[$row[csf("trans_id")]]['grouping'] = $row[csf("grouping")];
					}
					//$job_alldata_arr[$row[csf("job_no")]]['po_id'].= $row[csf("po_id")].",";
					//$job_alldata_arr[$row[csf("job_no")]]['pub_shipment_date'] = $row[csf("pub_shipment_date")];
					//$job_alldata_arr[$row[csf("job_no")]]['job_qnty'] += $row[csf("po_qnty")];

					//$po_wise_job_arr[$row[csf("po_id")]] = $row[csf("job_no")];

					//$jobNo .= "'".$row[csf("job_no")]."',";
					$buyer_name .= $row[csf("buyer_name")] . ",";
				}
				unset($job_result_Array);

				$buyer_ids = implode(",", array_unique(explode(",", chop($buyer_name, ','))));


				if ($buyer_ids != "") {
					$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyer_ids)", 'id', 'buyer_name');
				}
			}
		}

		if ($requisition_numbers != "") {
			$requsition_sql = "select a.knit_id as program_no, a.requisition_no from ppl_yarn_requisition_entry a where a.status_active=1 and a.is_deleted=0 and a.requisition_no in($requisition_numbers) group by a.knit_id, a.requisition_no";
			$requsition_result_array = sql_select($requsition_sql);
			$requisition_data = array();

			foreach ($requsition_result_array as $row) {
				$requisition_data[$row[csf("requisition_no")]]['program_no'] = $row[csf("program_no")];
			}
		}
	}

	//echo "<pre>";
	//print_r($job_alldata_arr);
?>
	<div style="width:2060px;">
		<table cellspacing="0" border="0" width="100%">

			<tr>
				<td colspan="21" align="center">
					<strong style="font-size:xx-large"><? echo $company_library[$data[0]]; ?></strong>
					<br><? echo chop(show_company($data[0], '', ''), ","); ?>
				</td>
			</tr>

			<tr>
				<td colspan="21" align="center">
					<?
					if ($return_source == 1) {
						$working_company = $main_squery_result[0][csf('knitting_company')];
						$working_com_location = show_company($working_company, '', '');
						$working_company_name = $company_library[$working_company];
					} else {
						$working_company = $main_squery_result[0][csf('knitting_company')];
						$working_company_name =  return_field_value("supplier_name", "lib_supplier", "id=" . $working_company, "supplier_name");
						$working_com_location = return_field_value("address_1", "lib_supplier", "id=$working_company", "address_1");
					}

					$issue_id = $main_squery_result[0][csf('issue_id')];
					$issue_location_id =  return_field_value("location_id", "INV_ISSUE_MASTER", "id=$issue_id", "location_id");
					$issue_location_name = ($location_arr[$issue_location_id] != "") ? ", " . $location_arr[$issue_location_id] : ""
					?>
					<p style="font-size:20px">Working Company: <? echo $working_company_name; ?></p>
				</td>
			</tr>

			<tr>
				<td colspan="19" align="center"> <? echo chop($working_com_location, ",") . " " . $issue_location_name; ?></td>
			</tr>

			<tr class="form_caption">
				<td colspan="19" style="font-size:x-large" align="center"><strong><u>Issue Return</u></strong></td>
			</tr>
		</table>

		<div style="width:100%;">
			<table align="right" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30" align="center">SL</th>
					<th width="80">Trans. Date</th>
					<th width="130">Trans. Ref.</th>
					<th width="80">Internal Ref. No</th>
					<th width="80">Program No</th>
					<th width="80">Book. No/ Req. No</th>
					<th width="100">Job No</th>
					<th width="130">Style Ref. No</th>
					<th width="100">Buyer</th>
					<th width="100">Store Name</th>
					<th width="100">Floor Name</th>
					<th width="100">Room Name</th>
					<th width="100">Supplier Name</th>
					<th width="80">No Of Cone</th>
					<th width="80">No Of Bag</th>
					<th width="80">Lot</th>
					<th width="80">Count</th>
					<th width="80">Composition</th>
					<th width="80">Yarn Type</th>
					<th width="80">Color</th>
					<th width="80" align="left">Return Qty.</th>
					<th width="80" align="left">Reject Qty.</th>
					<th>Remarks</th>
				</thead>
				<?
				$i = 1;
				foreach ($main_squery_result as $row) {
					$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
					$buyer_name = $buyer_arr[$job_alldata_arr[$row[csf("trans_id")]]['buyer_name']];

					if ($row[csf('yarn_count_id')] != "") {
						$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
					} else {
						$compos = "";
					}

					$booking_requ_no = $row[csf('booking_no')];

					if ($row[csf("receive_basis")] == 3) {
						$programNo = $requisition_data[$row[csf("booking_no")]]['program_no'];
					}


				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="80"><? echo change_date_format($row[csf('transaction_date')]); ?></td>
						<td width="130"><? echo $row[csf('recv_number')]; ?></td>
						<td width="80"><? echo $job_alldata_arr[$row[csf("trans_id")]]['grouping']; ?></td>
						<td width="80"><? echo $programNo; ?></td>
						<td width="80"><? echo $booking_requ_no; ?></td>
						<td width="100"><? echo $job_alldata_arr[$row[csf("trans_id")]]['job_no']; ?></td>
						<td width="130"><? echo $job_alldata_arr[$row[csf("trans_id")]]['style_ref_no']; ?></td>
						<td width="100"><? echo $buyer_name; ?></td>
						<td width="100"><? echo $store_library[$row[csf('store_id')]]; ?></td>
						<td width="100"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></td>
						<td width="100"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></td>
						<td width="100"><? echo $supplier_library[$row[csf('supplier_id')]]; ?></td>
						<td width="80" align="center"><? echo $row[csf('no_cone')]; ?></td>
						<td width="80" align="center"><? echo $row[csf('no_bag')]; ?></td>
						<td width="80"><? echo $row[csf('lot')]; ?></td>
						<td width="80"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
						<td width="80"><? echo $compos; ?></td>
						<td width="80"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
						<td width="80" align="right"><? echo $color_arr[$row[csf('color')]]; ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('return_qnty')], 0, '', ','); ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('reject_qnty')], 0, '', ','); ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
				<?php
					$total_return_qnty += $row[csf('return_qnty')];
					$total_reject_qnty += $row[csf('reject_qnty')];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="20">Total</td>
					<td align="right"><? echo number_format($total_return_qnty, 0, '', ','); ?></td>
					<td align="right"><? echo number_format($total_reject_qnty, 0, '', ','); ?></td>
					<td align="right">&nbsp;</td>
				</tr>
				<tfoot>
					<tr>
						<th colspan="23" align="left">In Word : <? echo number_to_words(number_format($total_return_qnty, 2)); ?></th>
					</tr>
				</tfoot>
			</table>

			<br>
			<?
			echo signature_table(37, $data[0], "1300px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "load_drop_down_purpose") {
	$data = explode("**", $data);

	$issue_id = $data[0];
	$company_id = $data[1];

	//print_r($data);
	$issue_purpose = return_field_value("issue_purpose", "inv_issue_master", "id='" . $issue_id . "'", "issue_purpose");
	echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $issue_purpose, "", "1", $issue_purpose, "", "", "");

	exit();
}
?>