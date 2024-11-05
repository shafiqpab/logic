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
$user_supplier_ids = trim($_SESSION['logic_erp']['supplier_id']);
//--------------------------------------------------------------------------------------------
if ($action == "company_wise_report_button_setting") {

	extract($_REQUEST);

	$print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $data . "'  and module_id=6 and report_id=302 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",", $print_report_format);
	echo "$('#print3').hide();\n";
	echo "$('#print2').hide();\n";
	echo "$('#print').hide();\n";

	if ($print_report_format != "") {
		foreach ($print_report_format_arr as $id) {
			if ($id == 85) {
				echo "$('#print3').show();\n";
			}
			if ($id == 110) {
				echo "$('#print2').show();\n";
			}
			if ($id == 134) {
				echo "$('#print').show();\n";
			}
		}
	} else {
		echo "$('#print3').show();\n";
		echo "$('#print2').show();\n";
		echo "$('#print').show();\n";
	}

	// $yarn_rate_match_sql=sql_select("select during_issue from variable_settings_inventory where variable_list=25 and company_name=$data and status_active=1 and is_deleted=0");
	// echo "$('#yarn_rate_match').val(".$yarn_rate_match_sql[0][csf("during_issue")].");\n";

	exit();
}

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

if ($action == "check_conversion_rate") {
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	echo $exchange_rate;
	exit();
}

//load drop down supplier//
if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location", 170, "select ID, LOCATION_NAME from LIB_LOCATION where COMPANY_ID = '$data' and status_active = 1 and is_deleted = 0", "id,location_name", 1, "-- Select Location--", 0, "", 0);
	exit();
}
if ($action == "load_drop_down_wlocation") {
	echo create_drop_down("cbo_wlocation", 170, "select ID, LOCATION_NAME from LIB_LOCATION where COMPANY_ID = '$data' and status_active = 1 and is_deleted = 0", "id,location_name", 1, "-- Select WC.Location--", 0, "load_drop_down( 'requires/leftover_yarn_receive_sweater_controller', this.value+'_'+'$data', 'load_drop_down_wfloor', 'wfloor_td' );", 0);
	exit();
}
if ($action == "load_drop_down_wfloor") {
	$data = explode("_", $data);
	$location_id = $data[0];
	$company_id = $data[1];
	echo create_drop_down("cbo_wfloor", 170, "select a.FLOOR_ROOM_RACK_ID, a.FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST a, LIB_FLOOR_ROOM_RACK_DTLS b
	where b.FLOOR_ID = a.FLOOR_ROOM_RACK_ID and b.COMPANY_ID = '$company_id' and b.location_id = '$location_id' and a.status_active = 1 and b.status_active = 1", "floor_room_rack_id,floor_room_rack_name", 1, "-- Select WC.Floor--", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_buyer") {
	//$data=explode("_",$data);
	echo create_drop_down("cbo_buyer_name", 133, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", '');
	exit();
}

//load drop down party//
if ($action == "load_drop_down_party") {
	echo create_drop_down("cbo_party", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=91 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_company_from_check_wo_paymode") {
	$data = explode("_", $data);

	if ($data[4] == 94) {
		$service_type_cond = "and b.service_type in(2,12,15,38,44,46,50,51)";
	} else {
		$service_type_cond = "";
	}

	if ($data[3] == 3 || $data[3] == 5) {
		echo create_drop_down("cbo_supplier", 170, "select a.id, a.company_name from lib_company a,wo_yarn_dyeing_mst b where a.id=b.supplier_id and b.ydw_no='$data[2]' and b.pay_mode=$data[3] $service_type_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.company_name", "id,company_name", 0, "-- Select --", 1, "", 0);
	} else {
		//and b.entry_form=$data[4]
		echo create_drop_down("cbo_supplier", 170, "select a.id, a.supplier_name from lib_supplier a,wo_yarn_dyeing_mst b where a.id=b.supplier_id and b.ydw_no='$data[2]' and b.pay_mode=$data[3] $service_type_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.supplier_name", "id,supplier_name", 0, "-- Select --", 1, "", 0);
	}
	exit();
}

if ($action == "load_drop_down_supplier_from_issue") {
	echo create_drop_down("cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_issue_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.knit_dye_company and d.knit_dye_source=3 and d.issue_purpose in(15,50,51) and d.entry_form=3 and a.tag_company='$data' and b.party_type in(2,93,94) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_room_rack_self_bin") {
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$explodeData[13] = '170';
	$data = implode('*', $explodeData);
	load_room_rack_self_bin("requires/yarn_receive_controller", $data);
}

if ($action == "load_drop_down_color") {
	// 04-12-2022 update by Didar CRM ID : 25137
	$data_arr = explode("_", $data);
	$receive_basis = $data_arr[0];
	$receive_purpose = $data_arr[1];
	$wo_pi_id = $data_arr[2];

	if ($db_type == 0) $color_cond = " and c.color_name!=''";
	else $color_cond = " and c.color_name IS NOT NULL";

	if ($wo_pi_id != "") {
		if ($receive_basis == 1 && $receive_purpose == 16) {
			$sql = "select c.id,c.color_name from com_pi_master_details a , com_pi_item_details b, lib_color c where a.id=b.pi_id and b.color_id=c.id and a.id=$wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name order by c.color_name";
		} else if ($receive_basis == 2 && $receive_purpose == 2) {
			$sql = "select c.id, c.color_name from lib_color c, wo_yarn_dyeing_dtls b where c.id=b.yarn_color and c.status_active =1 and c.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and b.mst_id=$wo_pi_id group by c.id, c.color_name order by c.color_name";
		} else if ($receive_basis == 2 && ($receive_purpose == 16 || $receive_purpose == 5)) {
			$sql = "select c.id,c.color_name from wo_non_order_info_mst a, wo_non_order_info_dtls b, lib_color c where a.id=b.mst_id and b.color_name=c.id and a.id=$wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name order by c.color_name";
		} else if ($receive_basis == 2 && ($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51)) {
			$sql = "select c.id,c.color_name from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls_fin_prod b,lib_color c, wo_yarn_dyeing_dtls d where a.id=b.mst_id and a.id=d.mst_id and c.id=b.yarn_color and a.id=$wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.id, c.color_name order by c.color_name";
		} else if ($receive_basis == 14) {
			$sql = "select c.id,c.color_name from fabric_sales_order_dtls b, lib_color c where b.mst_id=$wo_pi_id and b.color_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.color_name order by c.color_name";
		} else {
			$sql = "select c.id,c.color_name from lib_color c where c.status_active=1 and c.is_deleted=0 $color_cond order by c.color_name";
		}
	} else {
		if ($receive_basis == 4 && $receive_purpose == 16) {
			$sql = "select c.id,c.color_name from lib_color c where c.status_active=1 and c.is_deleted=0 $color_cond order by c.color_name";
			//and c.grey_color=1 omit : instructed by saeed vai and jahid hasan
		} else {
			$sql = "select c.id,c.color_name from lib_color c where c.status_active=1 and c.is_deleted=0 $color_cond order by c.color_name";
		}
	}

	echo create_drop_down("cbo_color", 110, $sql, "id,color_name", 0, "--Select--", 0, "", 0);

	echo '<input type="button" name="btn_color" id="btn_color" class="formbuttonplasminus"  style="width:20px" onClick="fn_color_new(this.id)" value="N" />';
	exit();
}

//load drop down composition
if ($action == "load_drop_down_composition") // not used
{
	if ($data == 1) //new if
	{
		echo create_drop_down("cbocomposition1", 80, $composition, "", 1, "-- Select --", "", "", $disabled, "");
		echo '<input type="text" id="percentage1" name="percentage1" class="text_boxes_numeric" style="width:40px" maxlength="3" placeholder="%" />';
		echo create_drop_down("cbocomposition2", 80, $composition, "", 1, "-- Select --", "", "", $disabled, "");
		echo '<input type="text" id="percentage2" name="percentage2" class="text_boxes_numeric" style="width:40px" maxlength="3" placeholder="%" />';
		echo '<input type="button" class="formbutton" name="btn_composition" id="btn_composition" width="15" onClick="fn_comp_new(this.id)" value="F" />';
	} else {
		$sql = sql_select("select id,composition1,percentage1,composition2,percentage2 from lib_composition where is_deleted=0 AND status_active=1");
		$arr = array();
		foreach ($sql as $row) {
			if ($row[csf("composition1")] == 0) {
				$row[csf("composition1")] = "";
				$row[csf("percentage1")] = "";
			}
			if ($row[csf("composition2")] == 0) {
				$row[csf("composition2")] = "";
				$row[csf("percentage2")] = "";
			}
			$arr[$row[csf("id")]] = $composition[$row[csf("composition1")]] . " " . $row[csf("percentage1")] . " " . $composition[$row[csf("composition2")]] . " " . $row[csf("percentage2")];
		}

		echo create_drop_down("cbo_composition", 110, $arr, "", 1, "--Select--", 0, "", 0);
		echo '<input type="button" class="formbutton" name="btn_composition" id="btn_composition" width="15" onClick="fn_comp_new(this.id)" value="N" />';
	}
	exit();
}

// wo/pi popup here----------------------//
if ($action == "wopi_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		function js_set_value(str) {
			var receive_basis = '<? echo $receive_basis; ?>';
			var splitData = str.split("_");
			$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
			$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
			$("#hidden_paymode").val(splitData[2]); // paymode
			$("#hidden_entry_form").val(splitData[3]); // entry form
			$("#booking_without_order").val(splitData[5]); // entry form
			$("#is_sales").val(splitData[6]); // entry form

			if (splitData[4] == 1) {
				alert("This PI Already Closed");
				return;
			}

			parent.emailwindow.hide();
		}

		function fn_supplier_buyer(company_id, basis) {
			var page_link = 'yarn_receive_controller.php?action=supplier_buyer_popup&company_id=' + company_id + '&basis=' + basis;

			if (basis == 14) {
				var title = "Buyer Info";
			} else {
				var title = "Supplier Info";
			}

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=275px, height=300px, center=1, resize=0, scrolling=0', '../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];
				var suplier_byer_id = this.contentDoc.getElementById("hdn_supplier_buyer_id").value;
				var suplier_buyer_name = this.contentDoc.getElementById("hdn_supplier_buyer_name").value;

				$('#txt_supplier_buyer_id').val(suplier_byer_id);
				$('#txt_supplier_buyer_name').val(suplier_buyer_name);
			}
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="1050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<!--<tr>
							<td colspan="4" align="center"><? // echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
															?></td>
				</tr>-->
						<tr>
							<th width="150">PI No</th>
							<th width="150">LC No</th>
							<th width="150">WO No</th>
							<th width="150">FSO No</th>
							<? echo ($receive_basis == 14) ? "<th width='150'>Buyer</th>" : "<th width='150'>Supplier</th>"; ?>
							<th width="200">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td style="display:none">
								<?
								echo create_drop_down("cbo_search_by", 170, $receive_basis_arr, "", 1, "--Select--", $receive_basis, "", 1);
								?>
							</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes" name="txt_pi_no" id="txt_pi_no" placeholder="write" <?php echo ($receive_basis == 1) ? "" : "disabled='disabled'"; ?> />
							</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes" name="txt_lc_no" id="txt_lc_no" <?php echo ($receive_basis == 1) ? "" : "disabled='disabled'"; ?> placeholder="write" />
							</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes" name="txt_wo_no" id="txt_wo_no" <?php echo ($receive_basis == 2) ? "" : "disabled='disabled'"; ?> placeholder="write" />
							</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" <?php echo ($receive_basis == 14) ? "" : "disabled='disabled'"; ?> placeholder="write" />
							</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes" name="txt_supplier_buyer_name" id="txt_supplier_buyer_name" placeholder="Browse" onDblClick="fn_supplier_buyer(<? echo $company; ?>,<? echo $receive_basis; ?>)" />
								<input type="hidden" name="txt_supplier_buyer_id" id="txt_supplier_buyer_id" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_lc_no').value+'_'+document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_supplier_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_<? echo $company; ?>'+'_<? echo $receive_purpose; ?>'+'_<? echo $receive_basis; ?>'+'_'+document.getElementById('txt_fso_no').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wopi_search_list_view', 'search_div', 'yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="7">
								<? echo load_month_buttons(1); ?>
								<!-- Hidden field here -->
								<input type="hidden" id="hidden_tbl_id" value="" />
								<input type="hidden" id="hidden_wopi_number" value="" />
								<input type="hidden" id="hidden_paymode" value="" />
								<input type="hidden" id="hidden_entry_form" value="" />
								<input type="hidden" id="booking_without_order" value="" />
								<input type="hidden" id="is_sales" value="" />
								<!-- -END -->
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" style="margin-top:5px" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit;
}

//after select wo/pi number get form data here---------------------------//
if ($action == "create_wopi_search_list_view") {

	$ex_data = explode("_", $data);
	$txt_search_by = trim($ex_data[0]);
	$txt_pi_no = trim($ex_data[1]);
	$txt_lc_no = trim($ex_data[2]);
	$txt_wo_no = trim($ex_data[3]);
	$txt_date_from = trim($ex_data[5]);
	$txt_date_to = trim($ex_data[6]);
	$company = trim($ex_data[7]);
	$receive_purpose = trim($ex_data[8]);
	$receive_basis = trim($ex_data[9]);

	if ($receive_basis == 14) {
		$txt_buyer_id = trim($ex_data[4]);
		$txt_supplier_id = "";
	} else {
		$txt_supplier_id = trim($ex_data[4]);
		$txt_buyer_id = "";
	}

	$fso_no = trim($ex_data[10]);
	$booking_year = $ex_data[11];

	//echo $txt_search_by;die;
	if ($txt_search_by == 1) {
		$sql_cond = "";
		if ($txt_pi_no != "") $sql_cond .= " and a.pi_number like '%$txt_pi_no%'";
		if ($txt_lc_no != "") $sql_cond .= " and c.lc_number like '%$txt_lc_no%'";
		if ($txt_supplier_id != "") $sql_cond .= " and a.supplier_id=$txt_supplier_id";
		if ($txt_date_from != "" && $txt_date_to != "") {
			if ($db_type == 0) {
				$sql_cond .= " and a.pi_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and a.pi_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
			}
		}

		$approval_status_cond = "";
		if ($db_type == 0) {
			$approval_status = "select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), 'yyyy-mm-dd') . "' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		} else {
			$approval_status = "select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), "", "", 1) . "' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;

		$approval_status = sql_select($approval_status);
		if ($approval_status[0][csf('approval_need')] == 1) {
			if ($approval_status[0][csf('allow_partial')] == 1) {
				$approval_status_cond = " and a.approved in (1,3)";
			} else {
				$approval_status_cond = " and a.approved in (1)";
			}
			//$approval_status_cond= "and a.approved = 1";
		}

		$sql = "select a.id as id, a.pi_number as wopi_number, a.pi_number as wopi_prefix, a.pi_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, c.lc_number as lc_number, a.ref_closing_status
		from com_pi_item_details m, com_pi_master_details a
		left join com_btb_lc_pi b on a.id=b.pi_id and b.status_active=1 and b.is_deleted=0
		left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id and c.status_active=1 and c.is_deleted=0
		where m.pi_id=a.id and a.item_category_id = 1 and a.importer_id=$company and a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status=2 and m.order_source <>5 $sql_cond $approval_status_cond
		group by a.id, a.pi_number, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, a.source, c.lc_number, a.ref_closing_status";
	} else if ($txt_search_by == 2) {
		$approval_status_cond = "";
		if ($db_type == 0) {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), 'yyyy-mm-dd') . "' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
		} else {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), "", "", 1) . "' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
		}

		$approval_status = sql_select($approval_status);
		if ($approval_status[0][csf('approval_need')] == 1) {
			$approval_status_cond = "and is_approved = 1";
		}

		if ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 44 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51) {
			$sql_cond = "";
			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and booking_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if ($txt_wo_no != "") $sql_cond .= " and yarn_dyeing_prefix_num='$txt_wo_no'";
			if ($txt_supplier_id != "") $sql_cond .= " and supplier_id=$txt_supplier_id";

			if ($receive_purpose == 2) {
				$entry_form = "(41,42,114,125,135)";
				$purpose = "";
				$select_purpose = " 2 as service_type";
			} else {
				$entry_form = "(94,340)";
				$purpose = " and service_type = $receive_purpose";
				$select_purpose = "service_type";
			}

			$sql = "select id, yarn_dyeing_prefix_num, ydw_no, booking_date, delivery_date, supplier_id, currency, source, pay_mode, entry_form,booking_without_order,is_sales, $select_purpose, 0 as ref_closing_status from wo_yarn_dyeing_mst where status_active=1 and is_deleted=0 and entry_form in $entry_form $purpose and pay_mode!=2 and company_id='$company' $sql_cond order by id DESC ";
		} else {
			$sql_cond = "";
			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and wo_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and wo_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if ($txt_wo_no != "") $sql_cond .= " and wo_number_prefix_num='$txt_wo_no'";
			if ($txt_supplier_id != "") $sql_cond .= " and supplier_id=$txt_supplier_id";

			$sql = "select id,wo_number as wopi_number,wo_number_prefix_num as wopi_prefix,' ' as lc_number,wo_date as wopi_date,supplier_id as supplier_id,currency_id as currency_id,source as source,entry_form, 0 as ref_closing_status
			from wo_non_order_info_mst where status_active=1 and is_deleted=0 and entry_form=144 and company_name='$company' and pay_mode!=2 and payterm_id<>5 $sql_cond $approval_status_cond order by id";
		}
	}

	//echo $sql;
	if ($txt_search_by == 1 || $txt_search_by == 2) {
		$result = sql_select($sql);
		$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

		foreach ($result as $row) {
			if ($receive_basis == 2 && ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 44 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51)) {
				if ($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) {
					$supplier_ref_arr[$row[csf('id')]] = $company_arr[$row[csf('supplier_id')]];
				} else {
					$supplier_ref_arr[$row[csf('id')]] = $supplier_arr[$row[csf('supplier_id')]];
				}
			} else {
				if ($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) {
					$supplier_ref_arr[$row[csf('id')]] = $company_arr[$row[csf('supplier_id')]];
				} else {
					$supplier_ref_arr[$row[csf('id')]] = $supplier_arr[$row[csf('supplier_id')]];
				}
			}
		}
	}

	if ($txt_search_by == 2 && ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 44 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51)) {
		$arr = array(1 => $yarn_issue_purpose, 4 => $supplier_ref_arr, 5 => $currency, 6 => $source, 7 => $pay_mode);
		echo create_list_view("list_view", "WO No,Service Type,Booking Date,Delivery Date,Supplier,Currency,Source,Pay Mode", "50,100,80,80,180,100,80,120,100,80", "900", "260", 0, $sql, "js_set_value", "id,ydw_no,pay_mode,entry_form,ref_closing_status,booking_without_order,is_sales", "", 1, "0,service_type,0,0,id,currency,source,pay_mode", $arr, "yarn_dyeing_prefix_num,service_type,booking_date,delivery_date,id,currency,source,pay_mode", "", '', '0,0,3,3,0,0,0,0');
	} else if ($txt_search_by == 14) {
		$sql_cond = "";
		if ($txt_date_from != "" && $txt_date_to != "") {
			if ($db_type == 0) {
				$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and a.booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
			}
		}

		if ($txt_fso_no != "") $sql_cond .= " and a.yarn_dyeing_prefix_num='$txt_fso_no'";
		if ($txt_buyer_id != "") $sql_cond .= " and a.buyer_id=$txt_buyer_id";

		$year_field = "";
		$year_cond = "";
		if ($db_type == 0) {
			$year_field = "YEAR(a.insert_date) as year";
			if ($booking_year > 0) {
				$year_cond = " and YEAR(a.booking_date)=$booking_year";
			}
		} else if ($db_type == 2) {
			$year_field = "to_char(a.insert_date,'YYYY') as year";
			if ($booking_year > 0) {
				$year_cond = " and to_char(a.booking_date,'YYYY')=$booking_year";
			}
		}

		$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.within_group=2 and a.company_id=$company $sql_cond $year_cond order by id";

		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$within_group_arr = array(1 => "Yes", 2 => "No");
		$arr = array(2 => $within_group_arr, 3 => $buyer_arr);
		echo create_list_view("list_view", "Job No,Year,Within Group,Buyer,FSO No,Booking date,Style Ref", "150,80,80,150,150,80,200", "900", "260", 0, $sql, "js_set_value", "id,job_no", "", 1, "0,0,within_group,buyer_id,0,0,0,0", $arr, "job_no,year,within_group,buyer_id,job_no_prefix_num,booking_date,style_ref_no", "", '', '');
	} else {
		$arr = array(3 => $supplier_ref_arr, 4 => $currency, 5 => $source);
		echo create_list_view("list_view", "WO/PI No, LC ,Date, Supplier, Currency, Source", "80,120,90,250,100,100", "800", "260", 0, $sql, "js_set_value", "id,wopi_number,wopi_prefix,entry_form,ref_closing_status", "", 1, "0,0,0,id,currency_id,source", $arr, "wopi_prefix,lc_number,wopi_date,id,currency_id,source", "", '', '0,0,3,0,0,0');
	}

	exit();
}

//right side product list create here--------------------//
if ($action == "show_product_listview") {
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$receive_purpose = $ex_data[2];
	$company_id = $ex_data[3];
	$vs_rate_hide = $ex_data[4];
	//2**1960**15**1

	$variable_rcv_result = sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$company_id and variable_list='31' and status_active=1 and is_deleted=0");
	$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];

	if ($receive_basis == 2 && ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 44 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51)) {
		if ($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51) {
			$selected_yarn_dtls_ids = " listagg (c.id,',') within group (order by c.id) as yarn_dyeing_dtls_id ";
			$sql = "select a.ydw_no, a.company_id, b.id, b.mst_id, b.dtls_id, b.job_no, b.yarn_count count, b.yarn_comp yarn_comp_type1st, b.yarn_perc yarn_comp_percent1st, b.yarn_type, b.yarn_color, b.yarn_rate, a.ecchange_rate, $selected_yarn_dtls_ids from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls_fin_prod b, wo_yarn_dyeing_dtls c where a.id=b.mst_id and a.id=c.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.ydw_no, a.company_id, b.id, b.mst_id, b.dtls_id, b.job_no, b.yarn_count, b.yarn_comp, b.yarn_perc, b.yarn_type, b.yarn_color, b.yarn_rate, a.ecchange_rate";

			if ($db_type == 0) {
				$yarn_dyeing_sql = "select b.mst_id,b.id,group_concat(b.product_id) as product_id,sum(b.yarn_wo_qty) quantity from wo_yarn_dyeing_dtls b where b.status_active =1 and b.is_deleted=0 and b.mst_id=$wo_pi_ID group by b.mst_id,b.id";
			} else {
				$yarn_dyeing_sql = "select b.mst_id,b.id,listagg(b.product_id, ',') within group (order by b.product_id asc) as product_id,sum(b.yarn_wo_qty) quantity from wo_yarn_dyeing_dtls b where b.status_active =1 and b.is_deleted=0 and b.mst_id=$wo_pi_ID group by b.mst_id,b.id";
			}

			$yarn_dyeing_result = sql_select($yarn_dyeing_sql);
			foreach ($yarn_dyeing_result as $row) {
				$product_id_arr[$row[csf("id")]] += $row[csf("quantity")];
				$product_arr[$row[csf("mst_id")]][] = $row[csf("product_id")];
			}
		} else {
			$sql = "select a.ydw_no, a.company_id, a.ecchange_rate, b.id, b.job_no, b.yarn_color, b.yarn_wo_qty quantity, b.yarn_description, b.yarn_comp_percent1st, b.yarn_type, b.yarn_comp_type1st, b.count, b.product_id, b.entry_form, b.dyeing_charge as yarn_rate, b.id as yarn_dyeing_dtls_id from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.mst_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by a.ydw_no, a.company_id, a.ecchange_rate, b.id, b.job_no, b.yarn_color, b.yarn_wo_qty, b.yarn_description, b.yarn_comp_percent1st, b.yarn_type, b.yarn_comp_type1st,  b.count, b.product_id, b.entry_form, b.dyeing_charge";

			$product_sql = "select a.job_no, c.id as prod_id, c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_count_id,a.dyeing_color_id,c.yarn_type from inv_transaction a, inv_issue_master b, product_details_master c,wo_yarn_dyeing_dtls d where a.mst_id=b.id and a.prod_id=c.id and b.booking_id=d.mst_id and b.booking_id=$wo_pi_ID and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=$receive_purpose and a.item_category=1 and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0 group by c.id, a.job_no,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_count_id,a.dyeing_color_id,c.yarn_type";

			$pr_result = sql_select($product_sql); // and b.issue_purpose=$receive_purpose
			$product_arr = $product_data_arr = array();
			foreach ($pr_result as $pr_row) {
				$product_arr[$pr_row[csf("job_no")]][$pr_row[csf("yarn_comp_percent1st")]][$pr_row[csf("yarn_comp_type1st")]][$pr_row[csf("yarn_count_id")]][$pr_row[csf("yarn_type")]][$pr_row[csf("dyeing_color_id")]] = $pr_row[csf("prod_id")];

				$product_data_arr[$pr_row[csf("prod_id")]]['count'] = $pr_row[csf("yarn_count_id")];
				$product_data_arr[$pr_row[csf("prod_id")]]['yarn_comp_type1st'] = $pr_row[csf("yarn_comp_type1st")];
				$product_data_arr[$pr_row[csf("prod_id")]]['yarn_comp_percent1st'] = $pr_row[csf("yarn_comp_percent1st")];
				$product_data_arr[$pr_row[csf("prod_id")]]['yarn_type'] = $pr_row[csf("yarn_type")];
			}
		}

		//echo "<pre>";
		//print_r($product_arr);

		//echo $sql;
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active = 1 and is_deleted=0", 'id', 'color_name');
		$i = 1;
	?>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" id="tbl_product">
			<thead>
				<tr>
					<th>SL</th>
					<th>Job/Fso No</th>
					<th>Product Name</th>
					<th>Color</th>
					<th>Qnty</th>
				</tr>
			</thead>
			<tbody>
				<?
				//echo $sql;
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";


					if ($receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 44 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51) {
						//echo "<pre>";
						//print_r($product_arr);

						if ($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51) {
							$product_id = implode(",", array_values($product_arr[$row[csf("mst_id")]]));
							$yarn = $yarn_count_arr[$row[csf("count")]] . " " . $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];
						} else {
							$product_id = $row[csf("PRODUCT_ID")];
							$row[csf("count")] = $product_data_arr[$product_id]['count'];
							$row[csf("yarn_comp_type1st")] = $product_data_arr[$product_id]['yarn_comp_type1st'];
							$row[csf("yarn_comp_percent1st")] = $product_data_arr[$product_id]['yarn_comp_percent1st'];
							$row[csf("yarn_type")] = $product_data_arr[$product_id]['yarn_type'];
						}

						$yarn = $yarn_count_arr[$row[csf("count")]] . " " . $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];

						$on_click_action = "wo_product_form_yarn_services";
						$on_click_action_param = $row[csf("job_no")] . "**" . $row[csf("count")] . "**" . $row[csf("yarn_comp_type1st")] . "**" . $row[csf("yarn_comp_percent1st")] . "**" . $row[csf("yarn_type")] . "**" . $row[csf("yarn_color")] . "**" . $row[csf("dtls_id")] . "**" . $wo_pi_ID . "**" . $receive_purpose . "**" . $row[csf("yarn_rate")] . "**" . $row[csf("ecchange_rate")] . "**" . $row[csf("company_id")] . "**" . $product_id . "**" . $row[csf("yarn_dyeing_dtls_id")];
					} else {
						if ($row[csf("product_id")] == "") // without lot
						{
							$product_id = $product_arr[$row[csf("job_no")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_comp_type1st")]][$row[csf("count")]][$row[csf("yarn_type")]][$row[csf("yarn_color")]];

							$yarn = $yarn_count_arr[$row[csf("count")]] . " " . $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];
						} else {
							$product_id = $row[csf("product_id")];

							$row[csf("count")] = $product_data_arr[$product_id]['count'];
							$row[csf("yarn_comp_type1st")] = $product_data_arr[$product_id]['yarn_comp_type1st'];
							$row[csf("yarn_comp_percent1st")] = $product_data_arr[$product_id]['yarn_comp_percent1st'];
							$row[csf("yarn_type")] = $product_data_arr[$product_id]['yarn_type'];

							$yarn = $yarn_count_arr[$row[csf("count")]] . " " . $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];
						}

						$on_click_action = "wo_product_form_yarn_dyeing";
						$on_click_action_param = $product_id . "**" . $row[csf("job_no")] . "**" . $wo_pi_ID . "**" . $row[csf("yarn_color")] . "**" . $receive_purpose . "**" . $row[csf("id")] . "**" . $row[csf("entry_form")] . "**" . $row[csf("count")] . "**" . $row[csf("yarn_comp_type1st")] . "**" . $row[csf("yarn_comp_percent1st")] . "**" . $row[csf("yarn_type")] . "**" . $row[csf("id")] . "**" . $company_id;
					}

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $on_click_action_param; ?>","<? echo $on_click_action ?>","requires/yarn_receive_controller");change_color_tr("<? echo $i; ?>","<? echo $bgcolor; ?>")' style="cursor:pointer" id="tr_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td title="<?php echo $row[csf("ydw_no")]; ?>"><? echo $row[csf("job_no")]; ?></td>
						<td><? echo $yarn; ?></td>
						<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
						<td align="right">
							<?
							$dtls_qnty = 0;
							$dtls_ids = explode(",", $row[csf("dtls_id")]);
							foreach ($dtls_ids as $dtls_row) {
								$dtls_qnty += $product_id_arr[$dtls_row];
							}
							echo ($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51) ? number_format($dtls_qnty, 2, '.', '') : number_format($row[csf("quantity")], 2, '.', '');
							?>
						</td>
					</tr>
				<?
					$i++;
				}
				?>
			</tbody>
		</table>
	<?
	} elseif ($receive_basis == 14) {
		$color_arr = return_library_array("select id, color_name from lib_color where status_active = 1 and is_deleted=0", 'id', 'color_name');
		$sql = "select job_no_mst,fabric_desc,color_id,grey_qty from fabric_sales_order_dtls where mst_id=$wo_pi_ID and status_active=1 and is_deleted=0";
	?>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" id="tbl_product">
			<thead>
				<tr>
					<th>SL</th>
					<th>Job/Fso No</th>
					<th>Product Name</th>
					<th>Color</th>
					<th>Qnty</th>
				</tr>
			</thead>
			<tbody>
				<?
				$result = sql_select($sql);
				$i = 1;
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='change_color_tr("<? echo $i; ?>","<? echo $bgcolor; ?>")' style="cursor:pointer" id="tr_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td title=""><? echo $row[csf("job_no_mst")]; ?></td>
						<td><? echo $row[csf("fabric_desc")]; ?></td>
						<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td align="right"><? echo number_format($row[csf("grey_qty")], 2); ?></td>
					</tr>
				<?
					$i++;
				}
				?>
			</tbody>
		</table>
	<?
	} else {
		if ($receive_basis == 1) // pi basis
		{
			if ($variable_rcv_level) {
				$sql = "select  a.id as mst_id, a.pi_number, a.pi_basis_id, b.work_order_no as wo_pi_no, a.importer_id as company_id, a.supplier_id, b.id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, b.rate, b.quantity as quantity
				from com_pi_master_details a, com_pi_item_details b
				where a.id=b.pi_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_source<>5";
			} else {
				if ($db_type == 0) {
					$sql = "select a.id as mst_id, a.pi_number, a.pi_basis_id, a.importer_id as company_id, a.supplier_id, group_concat(b.id) as id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, avg(b.rate) as rate, sum(b.quantity) as quantity
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_source<>5
					group by a.id, a.pi_number, a.pi_basis_id, a.importer_id, a.supplier_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.color_id";
				} else {
					$sql = "select a.id as mst_id, a.pi_number, a.pi_basis_id, a.importer_id as company_id, a.supplier_id, listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, sum(b.net_pi_amount)/sum(b.quantity) as rate, sum(b.quantity) as quantity
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_source<>5
					group by a.id, a.pi_number, a.pi_basis_id, a.importer_id, a.supplier_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.color_id";
				}
			}
		} else if ($receive_basis == 2) // wo basis
		{
			if ($variable_rcv_level == 2) {
				$sql = "select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, b.id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, b.rate, b.supplier_order_quantity as quantity
				from wo_non_order_info_mst a, wo_non_order_info_dtls b
				where a.id=b.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			} else {
				if ($db_type == 0) {
					$sql = "select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, group_concat(b.id) as id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, avg(b.rate) as rate, sum(b.supplier_order_quantity) as quantity
					from wo_non_order_info_mst a, wo_non_order_info_dtls b
					where a.id=b.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.id, a.company_name, a.supplier_id, a.wo_number, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name";
				} else {
					$sql = "select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, sum(b.AMOUNT)/sum(b.supplier_order_quantity) as rate, sum(b.supplier_order_quantity) as quantity
					from wo_non_order_info_mst a, wo_non_order_info_dtls b
					where a.id=b.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.id, a.company_name, a.supplier_id, a.wo_number, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name";
				}
			}
		}
		//echo $sql;//die;
		$result = sql_select($sql);
		$yarn_sum_receive_arr = array();
		foreach ($result as $row_count) {
			$yarn_sum_receive_arr[$row_count[csf("mst_id")]][$row_count[csf("supplier_id")]][$row_count[csf("yarn_count")]][$row_count[csf("yarn_comp_type1st")]][$row_count[csf("yarn_comp_percent1st")]][$row_count[csf("yarn_type")]][$row_count[csf("color_name")]] += $row_count[csf("quantity")];
		}
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$i = 1;
	?>

		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" id="tbl_product" width="100%">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="200">Product Name</th>
					<th width="50" <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>>Rate</th>
					<th>Qnty</th>
				</tr>
			</thead>
			<tbody>
				<?
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$compositionPart = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];
					if ($row[csf("yarn_comp_type2nd")] != 0) {
						$compositionPart .= " " . $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")];
					}

					$productName = $yarn_count_arr[$row[csf("yarn_count")]] . " " . $compositionPart . " " . $yarn_type[$row[csf("yarn_type")]] . " " . $color_name_arr[$row[csf("color_name")]];
					$data = $row[csf("yarn_comp_type1st")] . "_" . $row[csf("yarn_comp_percent1st")] . "_" . $row[csf("yarn_comp_type2nd")] . "_" . $row[csf("yarn_comp_percent2nd")] . "_" . $row[csf("yarn_count")] . "_" . $row[csf("yarn_type")] . "_" . $row[csf("color_name")];
					$quantity = $yarn_sum_receive_arr[$row[csf("mst_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color_name")]];
					if ($variable_rcv_level == 2) $wo_pi_qnty = $row[csf("quantity")];
					else $wo_pi_qnty = $quantity;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $receive_basis . "**" . $row[csf("id")] . "**" . $row[csf("company_id")] . "**" . $row[csf("supplier_id")] . "**" . $row[csf("quantity")]; // $quantity 
																					?>","wo_pi_product_form_input","requires/yarn_receive_controller");change_color_tr("<? echo $i; ?>","<? echo $bgcolor; ?>")' style="cursor:pointer" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i; ?></td>
						<td title="<?php echo $row[csf("wo_pi_no")]; ?>"><? echo $productName; ?></td>
						<td align="right" <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>><? echo number_format($row[csf("rate")], 2); ?></td>
						<td align="right"><? echo $row[csf("quantity")]; ?></td>
					</tr>
				<? $i++;
				} ?>
			</tbody>
		</table>
	<?
	}

	exit();
}

//after select wo/pi number get form data here---------------------------//
if ($action == "populate_data_from_wopi_popup") {
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$receive_purpose = $ex_data[2];
	$company_id = $ex_data[3];

	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

	if ($receive_basis == 1) {
		$sql = "select c.id as id, c.lc_number as lc_number, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, a.buyer_id
		from com_pi_master_details a
		left join com_btb_lc_pi b on a.id=b.pi_id and b.status_active=1 and b.is_deleted=0
		left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id and c.status_active=1 and c.is_deleted=0
		where
		a.item_category_id = 1 and
		a.status_active=1 and a.is_deleted=0 and
		a.id=$wo_pi_ID";
	} else if ($receive_basis == 2) {
		if ($receive_purpose == 2 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 44 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51) //currency as currency_id,
		{
			$sql = "select id, '' as lc_number, supplier_id, currency as currency_id, source, pay_mode, ydw_no,entry_form
			from wo_yarn_dyeing_mst
			where status_active=1 and  is_deleted=0 and id=$wo_pi_ID";
		} else {
			$sql = "select id,wo_number as ydw_no, '' as lc_number,supplier_id as supplier_id,currency_id as currency_id, source as source,entry_form
			from wo_non_order_info_mst
			where status_active=1 and is_deleted=0 and  id=$wo_pi_ID";
		}
	}

	if ($receive_basis == 1 || $receive_basis == 2) {
		//echo $sql;die;
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($receive_basis == 2) {
				if ($receive_purpose == 2 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 44 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51) {
					$ydw_no = $row[csf('ydw_no')];
					$wopi_pay_mode = $row[csf('pay_mode')];
					$wo_entry_form = $row[csf('entry_form')];

					$data_ref = "'" . $receive_basis . "_" . $receive_purpose . "_" . $ydw_no . "_" . $wopi_pay_mode . "_" . $wo_entry_form . "'"; // die();

					echo "load_drop_down( 'requires/yarn_receive_controller', $data_ref, 'load_drop_down_company_from_check_wo_paymode', 'supplier');\n";

					$currency = 1;
				} else {
					$currency = $row[csf("currency_id")];
				}
			} else {
				$currency = $row[csf("currency_id")];
			}

			//for pi basis buyer
			if ($receive_basis == 1) {
				echo "$('#cbo_buyer_name').val(" . $row[csf('buyer_id')] . ");\n";
			}

			echo "$('#cbo_currency').val(" . $currency . ");\n";
			echo "$('#cbo_wo_currency').val(" . $row[csf("currency_id")] . ");\n";
			echo "exchange_rate($currency);\n";
			echo "$('#cbo_supplier').val(" . $row[csf('supplier_id')] . ");\n";
			echo "$('#cbo_source').val(" . $row[csf("source")] . ");\n";
			echo "$('#txt_lc_no').val('" . $row[csf("lc_number")] . "');\n";

			if ($row[csf("lc_number")] != "") {
				echo "$('#hidden_lc_id').val(" . $row[csf("id")] . ");\n";
			}

			echo "$('#cbo_supplier').attr('disabled','disabled');\n";
			echo "$('#hdnPayMode').val('" . $wopi_pay_mode . "');\n"; //for pay mode
		}
	}
	exit();
}

// get form data from product click in right side
if ($action == "wo_pi_product_form_input") {
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$company_id = $ex_data[2];
	$supplier_id = $ex_data[3];
	$wo_po_qnty = $ex_data[4];

	$variable_rcv_result = sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$company_id and variable_list='31' and status_active=1 and is_deleted=0");
	$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];

	if ($db_type == 0) {
		$orderBy_cond = "IFNULL";
		$select_dtls_id = "group_concat(distinct(b.id)) as dtls_id";
	} else if ($db_type == 2) {
		$orderBy_cond = "NVL";
		$select_dtls_id = "listagg(b.id,',') within group (order by b.id) as dtls_id";
	} else {
		$orderBy_cond = "ISNULL";
		$select_dtls_id = "";
	}

	if ($receive_basis == 1) // pi basis
	{
		$sql = "select a.id as mst_id, a.version, a.buyer_id, a.pi_basis_id, $select_dtls_id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, sum(b.quantity)as quantity,avg(b.rate) as rate, b.uom from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.id in($wo_pi_ID) group by a.id,a.version,a.buyer_id,a.pi_basis_id,b.count_name,b.yarn_composition_item1,b.yarn_composition_percentage1,b.yarn_composition_item2,b.yarn_composition_percentage2,b.yarn_type, b.color_id,b.uom";
	} else if ($receive_basis == 2) // wo basis
	{
		$sql = "select a.id as mst_id, b.id as dtls_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type,b.color_name, b.supplier_order_quantity as quantity, rate, uom  
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=b.mst_id and b.id in($wo_pi_ID)";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row) {
		if ($receive_basis == 1) {
			if ($row[csf('version')] == 0) {
				echo "$('#cbo_yarn_count').removeAttr('disabled','disabled');\n";
				echo "$('#cbocomposition1').removeAttr('disabled','disabled');\n";
				echo "$('#cbocomposition2').removeAttr('disabled','disabled');\n";
				echo "$('#percentage2').removeAttr('disabled','disabled');\n";
				echo "$('#cbo_yarn_type').removeAttr('disabled','disabled');\n";
				echo "$('#txt_rate').removeAttr('disabled','disabled');\n";
			}
			if ($row[csf('version')] == 1) {
				echo "$('#cbo_yarn_count').attr('disabled','disabled');\n";
				echo "$('#cbocomposition1').attr('disabled','disabled');\n";
				echo "$('#cbocomposition2').attr('disabled','disabled');\n";
				echo "$('#percentage2').attr('disabled','disabled');\n";
				echo "$('#cbo_yarn_type').attr('disabled','disabled');\n";
				echo "$('#txt_rate').attr('disabled','disabled');\n";
			}
			echo "$('#cbo_buyer_name').val(" . $row[csf("buyer_id")] . ");\n";
		}


		echo "$('#txt_pi_basis').val(" . $row[csf("pi_basis_id")] . ");\n";
		echo "$('#txt_wo_pi_dtls_id').val(" . $row[csf("dtls_id")] . ");\n";
		echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count")] . ");\n";
		echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
		echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
		if ($row[csf("yarn_comp_percent2nd")] > 0) {
			echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
			echo "$('#percentage2').val(" . $row[csf("yarn_comp_percent2nd")] . ");\n";
		} else {
			echo "$('#cbocomposition2').val(0);\n";
			echo "$('#percentage2').val('');\n";
		}

		echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
		echo "$('#cbo_uom').val(" . $row[csf("uom")] . ");\n";
		echo "$('#cbo_color').val(" . $row[csf("color_name")] . ").attr('disabled','disabled');\n";
		echo "$('#txt_rate').val(" . number_format($row[csf("rate")], 4, '.', '') . ");\n";

		if ($row[csf("yarn_comp_type2nd")] <= 0 || $row[csf("yarn_comp_type2nd")] == "") {
			$yarn_comp_type2nd = 0;
		} else {
			$yarn_comp_type2nd = $row[csf("yarn_comp_type2nd")];
		}
		if ($row[csf("yarn_comp_percent2nd")] <= 0 || $row[csf("yarn_comp_percent2nd")] == "") {
			$yarn_comp_percent2nd = 0;
		} else {
			$yarn_comp_percent2nd = $row[csf("yarn_comp_percent2nd")];
		}


		if ($variable_rcv_level == 2) // ############## for wo pi dtls level
		{
			$whereCondition = " a.id=b.prod_id and b.mst_id = c.id and b.company_id=$company_id and a.supplier_id=$supplier_id and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_req_dtls_id=" . $row[csf("dtls_id")] . " and b.receive_basis=$receive_basis and b.status_active=1 and b.is_deleted = 0";
		} else // ############## for wo pi item level
		{
			$whereCondition = "a.id=b.prod_id and b.mst_id = c.id and a.yarn_count_id=" . $row[csf("yarn_count")] . " and a.yarn_comp_type1st=" . $row[csf("yarn_comp_type1st")] . " and a.yarn_comp_percent1st=" . $row[csf("yarn_comp_percent1st")] . " and $orderBy_cond(a.yarn_comp_type2nd,0)=" . $yarn_comp_type2nd . " and $orderBy_cond(a.yarn_comp_percent2nd,0)=" . $yarn_comp_percent2nd . " and a.yarn_type=" . $row[csf("yarn_type")] . " and a.color=" . $row[csf("color_name")] . " and b.company_id=$company_id and a.supplier_id=$supplier_id and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_batch_no=" . $row[csf("mst_id")] . " and b.receive_basis=$receive_basis and b.status_active=1 and b.is_deleted = 0";
		}

		//echo $whereCondition;die;
		$totalRcvQnty = 0;
		$mrr_result = sql_select("select sum(b.cons_quantity) as recv_qnty, c.recv_number,c.id rcv_id,a.id as prod_id from product_details_master a, inv_transaction b, inv_receive_master c where $whereCondition group by c.recv_number,a.id,c.id");
		foreach ($mrr_result as $val) {
			$totalRcvQnty += $val[csf("recv_qnty")];
			$mrr_arr[$val[csf("recv_number")]] = $val[csf("recv_number")];
			$mrr_id_arr[$val[csf("rcv_id")]] = $val[csf("rcv_id")];
			$prod_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
		}
		unset($mrr_result);
	}

	// PICK ISSUE RETURN MRR FROM RECEIVE
	$sql_issue_transec = sql_select("select b.mst_id,b.id,c.issue_trans_id,c.entry_form,d.id
    from  inv_receive_master a , inv_transaction b, inv_mrr_wise_issue_details c
    left join inv_transaction d on c.issue_trans_id=d.id and d.status_active=1 and d.transaction_type=2
    where a.id=b.mst_id and  b.id=c.recv_trans_id
    and a.id in(" . implode(",", $mrr_id_arr) . ") and b.transaction_type=1 and a.entry_form=1 and c.entry_form=3
    and a.status_active=1 and b.status_active=1 and c.status_active=1
    and a.item_category=1 and b.transaction_type=1");

	foreach ($sql_issue_transec as $trn_issue_row) {
		$issue_trans_id .= $trn_issue_row[csf('issue_trans_id')] . ",";
	}

	$issue_trans_id = chop($issue_trans_id, " , ");

	if ($issue_trans_id != "") {
		$issue_id_from_issue_rtn = sql_select("SELECT a.id from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.id in($issue_trans_id) and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($issue_id_from_issue_rtn as $issue_row) {
			$issue_id .= $issue_row[csf('id')] . ",";
		}

		$issue_ids = chop($issue_id, " , ");
	}

	if ($issue_ids != "") {
		$sql_issue_rtn_rcv = sql_select("SELECT sum(b.cons_quantity) as recv_qnty, a.recv_number,b.prod_id as prod_id from inv_receive_master a,inv_transaction b where a.id=b.mst_id and a.issue_id in($issue_ids) and b.prod_id in(" . implode(",", $prod_arr) . ") and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 GROUP BY a.recv_number,b.prod_id");

		foreach ($sql_issue_rtn_rcv as $val) {
			//$totalRcvQnty += $val[csf("recv_qnty")];
			$mrr_arr[$val[csf("recv_number")]] = $val[csf("recv_number")];
			$prod_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
		}
	}
	// END PICK ISSUE RETURN MRR FROM RECEIVE

	$mrr_nos = "'" . implode("','", array_filter($mrr_arr)) . "'";
	$prod_ids = implode(",", array_filter($prod_arr));

	if ($prod_ids != '') {
		$prod_ids_condition = " and b.prod_id in ($prod_ids)";
	} else {
		$prod_ids_condition = "";
	}

	if (str_replace("'", "", $mrr_nos) != "" && $prod_arr != "") {
		//Receive Return From Receive Return Mrr
		$direct_mrr_return_res = sql_select("select c.id,sum(b.cons_quantity) as ret_qnty from product_details_master a, inv_transaction b , inv_issue_master c where a.id=b.prod_id and b.mst_id = c.id and c.entry_form = 8 and c.received_mrr_no in ($mrr_nos) and a.id in ($prod_ids) and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 group by c.id");
		$rcvReturnQnty = 0;
		foreach ($direct_mrr_return_res as $val) {
			$direct_mrr_rtn_id[$val[csf("id")]] = $val[csf("id")];
			$rcvReturnQnty += $val[csf("ret_qnty")];
		}
		unset($direct_mrr_return_res);
		$direct_mrr_rtn_ids = implode(",", array_filter($direct_mrr_rtn_id));
	}

	if ($direct_mrr_rtn_ids != "") {	//Rcv return from Issuer Return
		$mrr_rcv_return_not_cond = "and a.id not in($direct_mrr_rtn_ids)";
	}

	if ($prod_ids != '') {
		//comments out date 11.08.2021
		//$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty from  inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1 and b.transaction_type=3 and b.status_active = 1 and b.is_deleted = 0 and b.prod_id in ($prod_ids) $mrr_rcv_return_not_cond");

		$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty from inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1 and b.transaction_type=3 and b.status_active = 1 and b.is_deleted = 0 and b.prod_id in (" . $prod_ids . ") and a.received_mrr_no in(" . $mrr_nos . ") $mrr_rcv_return_not_cond");
	}

	foreach ($rcvReturnQntyFromIssueRetArr as $val) {
		$rcvReturnQnty += $val[csf("return_qnty")];
	}

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 1 order by id");
	$over_receive_limit = (!empty($variable_set_invent)) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	$over_receive_limit_qnty = ($over_receive_limit > 0) ? ($over_receive_limit / 100) * $wo_po_qnty : 0;

	//$orderQnty = $wo_po_qnty - $totalRcvQnty + $rcvReturnQnty + $over_receive_limit_qnty;

	$actual_rcv = ($totalRcvQnty - $rcvReturnQnty);
	//echo $totalRcvQnty."-".$rcvReturnQnty;die;
	$orderQnty = ($wo_po_qnty - $actual_rcv);
	echo "$('#txt_order_qty').val('" . number_format($orderQnty, 2, '.', '') . "');\n";
	echo "$('#txt_over_recv_limt').val(" . $over_receive_limit_qnty . ");\n";
	echo "$('#txt_woQnty').val(" . $wo_po_qnty . ");\n";
	echo "$('#txt_overRecPerc').val(" . $over_receive_limit . ");\n";
	echo "control_composition('percent_one');\n";
	echo "$('#txt_totRecv').val(" . $actual_rcv . ");\n";
	echo "$('#txt_receive_qty').removeAttr('onclick','func_onclick_qty()').attr({'onblur':'fn_calile()','readonly':false});\n";
	exit();
}

if ($action == "wo_product_form_yarn_dyeing") {
	$ex_data = explode("**", $data);
	$prod_id = $ex_data[0];
	$job_no = $ex_data[1];
	$wo_pi_ID = $ex_data[2];
	$color_id = $ex_data[3];
	$purpose = $ex_data[4];
	$wo_dtls_ID = $ex_data[5];
	$entry_form = $ex_data[6];
	$count = $ex_data[7];
	$yarn_comp_type1st = $ex_data[8];
	$yarn_comp_percent1st = $ex_data[9];
	$yarn_type = $ex_data[10];
	$company_id = $ex_data[12];


	if ($purpose == 12 || $purpose == 38 || $purpose == 44 || $purpose == 46 || $purpose == 15 ||  $purpose == 50 || $purpose == 51) {
		$item_category_cond = " and e.item_category_id=0";
	} else {
		$item_category_cond = " and e.item_category_id=24";
	}

	if ($prod_id != "") {
		$avg_rate = return_field_value("sum((cons_quantity*cons_rate))/sum(cons_quantity) AS order_rate", "inv_transaction", "status_active=1 and is_deleted=0 and prod_id=$prod_id and item_category=1 and transaction_type in(1,5)", "order_rate");
	} else {
		$avg_rate = return_field_value("sum((cons_quantity*cons_rate))/sum(cons_quantity) AS order_rate", "inv_transaction a,product_details_master b", "a.prod_id=b.id and b.yarn_count_id=$count and b.yarn_comp_type1st=$yarn_comp_type1st and b.yarn_type=$yarn_type and b.yarn_comp_percent1st=$yarn_comp_percent1st and b.item_category_id=1 and a.item_category=1 and a.transaction_type in(1,5) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 ", "order_rate");
	}

	if ($entry_form == 114 || $entry_form == 125 || $entry_form == 340) { // Without lot
		$productCond = "";
	} else {
		$productCond = "and d.product_id=$prod_id";
	}

	$dyeing_charge_sql = "select e.ecchange_rate*d.dyeing_charge as dyeing_charge from wo_yarn_dyeing_dtls d,wo_yarn_dyeing_mst e where d.mst_id=e.id and d.id=$wo_dtls_ID  and d.status_active=1 and d.is_deleted=0 and d.yarn_color=$color_id $productCond $item_category_cond";

	//echo $dyeing_charge_sql;//die();

	$dyeing_charge_result = sql_select($dyeing_charge_sql);

	$dyeing_charge = $dyeing_charge_result[0][csf("dyeing_charge")];
	//echo $dyeing_charge;die;
	$dyed_yarn_rate = $avg_rate + $dyeing_charge;

	if ($prod_id != "") {
		$sql = "select yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, unit_of_measure,color from product_details_master where id=$prod_id";
		//echo $sql;//die;	

		$result = sql_select($sql);
		foreach ($result as $row) {
			echo "$('#txt_grey_yarn_prod_id').val(" . $prod_id . ");\n";
			echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
			echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
			echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
			if ($row[csf("yarn_comp_type2nd")] > 0) {
				echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
				echo "$('#percentage2').val(" . $row[csf("yarn_comp_percent2nd")] . ");\n";
			} else {
				echo "$('#cbocomposition2').val(0);\n";
				echo "$('#percentage2').val('');\n";
			}
			echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
			echo "$('#cbo_uom').val(" . $row[csf("unit_of_measure")] . ");\n";
			echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
			echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
			echo "$('#hidden_grey_yarn_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
			echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "').attr('disabled','disabled');\n";
			echo "$('#job_no').val('" . $job_no . "');\n";

			if ($purpose == 7 || $purpose == 12 || $purpose == 38 || $purpose == 44 || $purpose == 46) {
				echo "$('#cbo_color').val('" . $row[csf("color")] . "');\n";
			} else {
				echo "$('#cbo_color').val('" . $color_id . "');\n";
			}

			echo "$('#txt_order_qty').val('');\n";
		}
	} else {
		echo "$('#cbo_yarn_count').val(" . $count . ");\n";
		echo "$('#cbocomposition1').val(" . $yarn_comp_type1st . ");\n";
		echo "$('#percentage1').val(" . $yarn_comp_percent1st . ");\n";
		echo "$('#cbo_yarn_type').val(" . $yarn_type . ");\n";
		echo "$('#cbo_uom').val(12);\n";
		echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
		echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
		echo "$('#hidden_grey_yarn_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
		echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "').attr('disabled','disabled');\n";
		echo "$('#job_no').val('" . $job_no . "');\n";
		echo "$('#cbo_color').val('" . $color_id . "').attr('disabled','disabled');\n";
		echo "$('#txt_order_qty').val('');\n";
	}

	echo "$('#hdnYarnDyingDtlsId').val('" . $wo_dtls_ID . "');\n";

	$sqlResult = sql_select("select category as service_type,over_rcv_percent as process_percentage,over_rcv_payment as process_control_status from variable_inv_ile_standard where company_name='$company_id' and variable_list=22 and category=$purpose order by id");

	if (!empty($sqlResult)) // Yarn service process loss Variable setting
	{
		$vs_service_type = $sqlResult[0][csf('service_type')];
		$vs_process_percentage = $sqlResult[0][csf('process_percentage')];
		$vs_process_control_status = $sqlResult[0][csf('process_control_status')];

		if ($vs_service_type == $purpose && $vs_process_control_status == 1 && $vs_process_percentage > 0) {
			echo "$('#txt_grey_qty').val('').attr('disabled','disabled');\n";
			echo "$('#hdn_service_process_loss_percentage').val(" . $vs_process_percentage . ");\n";
		} else {
			echo "$('#txt_grey_qty').val('').removeAttr('disabled','disabled');\n";
			echo "$('#hdn_service_process_loss_percentage').val(0);\n";
		}
	} else {
		echo "$('#txt_grey_qty').val('').removeAttr('disabled','disabled');\n";
		echo "$('#hdn_service_process_loss_percentage').val(0);\n";
	}

	if ($entry_form == 42 || $entry_form == 114) {
		echo "$('#txt_receive_qty').removeAttr('onclick','func_onclick_qty()').attr({'onblur':'fn_calile()','readonly':false});\n";
	} else {
		echo "$('#txt_receive_qty').attr('onclick','func_onclick_qty()');\n";
	}
	exit();
}

if ($action == "wo_product_form_yarn_services") {
	$ex_data = explode("**", $data);

	$job_no = $ex_data[0];
	$count = $ex_data[1];
	$yarn_comp_type1st = $ex_data[2];
	$yarn_comp_percent1st = $ex_data[3];
	$yarn_type = $ex_data[4];
	$color_id = $ex_data[5];
	$wo_dtls_ID = $ex_data[6];
	$wo_pi_ID = $ex_data[7];
	$purpose = $ex_data[8];
	$exchange_rate = $ex_data[10];
	$dyeing_charge = $ex_data[9] * $exchange_rate;
	$company_id = $ex_data[11];
	$product_ids = $ex_data[12];
	$yarn_dyeing_dtls_id = $ex_data[13];

	$avg_rate = return_field_value("(sum(cons_amount)/ sum(cons_quantity)) as order_rate", "inv_transaction", "status_active=1 and is_deleted=0 and prod_id in($product_ids) and item_category=1 and transaction_type in(1,5)", "order_rate");
	$dyed_yarn_rate = $avg_rate + $dyeing_charge;

	if ($purpose == 15 || $purpose == 50 || $purpose == 51) {

		if ($product_ids != "") {
			echo "$('#txt_grey_yarn_prod_id').val('" . $product_ids . "');\n";
		}

		echo "$('#cbo_yarn_count').val(" . $count . ");\n";
		echo "$('#cbocomposition1').val(" . $yarn_comp_type1st . ");\n";
		echo "$('#percentage1').val(" . $yarn_comp_percent1st . ");\n";
		echo "$('#cbo_yarn_type').val(" . $yarn_type . ");\n";
		echo "$('#cbo_uom').val(12);\n";
		echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
		echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
		echo "$('#hidden_grey_yarn_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
		echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "');\n";
		echo "$('#job_no').val('" . $job_no . "');\n";
		echo "$('#cbo_color').val('" . $color_id . "').attr('disabled','disabled');\n";
		echo "$('#txt_order_qty').val('');\n";
	} else {
		if ($product_ids != "") {
			$sql = "select yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, unit_of_measure,color from product_details_master where id=$product_ids";
			//echo $sql;//die;	
			$result = sql_select($sql);
			foreach ($result as $row) {
				echo "$('#txt_grey_yarn_prod_id').val(" . $product_ids . ");\n";
				echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
				echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
				echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
				if ($row[csf("yarn_comp_type2nd")] > 0) {
					echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
					echo "$('#percentage2').val(" . $row[csf("yarn_comp_percent2nd")] . ");\n";
				} else {
					echo "$('#cbocomposition2').val(0);\n";
					echo "$('#percentage2').val('');\n";
				}
				echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
				echo "$('#cbo_uom').val(" . $row[csf("unit_of_measure")] . ");\n";
				echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
				echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
				echo "$('#hidden_grey_yarn_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";
				echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "');\n";
				echo "$('#job_no').val('" . $job_no . "');\n";
				if ($purpose == 12 || $purpose == 38 || $purpose == 46) {
					echo "$('#cbo_color').val('" . $row[csf("color")] . "');\n";
				} else {
					echo "$('#cbo_color').val('" . $color_id . "');\n";
				}
				echo "$('#txt_order_qty').val('');\n";
			}
		}
	}
	echo "$('#hdnYarnDyingDtlsId').val('" . $yarn_dyeing_dtls_id . "');\n";

	$sqlResult = sql_select("select category as service_type,over_rcv_percent as process_percentage,over_rcv_payment as process_control_status from variable_inv_ile_standard where company_name='$company_id' and variable_list=22 and category=$purpose order by id");

	if (!empty($sqlResult)) // Yarn service process loss Variable setting
	{
		$vs_service_type = $sqlResult[0][csf('service_type')];
		$vs_process_percentage = $sqlResult[0][csf('process_percentage')];
		$vs_process_control_status = $sqlResult[0][csf('process_control_status')];

		if ($vs_service_type == $purpose && $vs_process_control_status == 1 && $vs_process_percentage > 0) {
			echo "$('#txt_grey_qty').attr('disabled','disabled');\n";
			echo "$('#hdn_service_process_loss_percentage').val(" . $vs_process_percentage . ");\n";
		} else {
			echo "$('#txt_grey_qty').removeAttr('disabled','disabled');\n";
			echo "$('#hdn_service_process_loss_percentage').val(0);\n";
		}
	} else {
		echo "$('#txt_grey_qty').removeAttr('disabled','disabled');\n";
		echo "$('#hdn_service_process_loss_percentage').val(0);\n";
	}

	exit();
}

// LC popup here----------------------Not Used//
if ($action == "lc_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str) {
			var splitData = str.split("_");
			$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
			$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Search By</th>
							<th width="150" align="center" id="search_by_td_up">Enter WO/PI Number</th>
							<th>
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" />
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_tbl_id" value="" />
								<input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
								<!-- END -->
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								$search_by_arr = array(0 => 'LC Number', 1 => 'Supplier Name');
								$dd = "change_search_event(this.value, '0*1', '0*select id, supplier_name from lib_supplier', '../../') ";
								echo create_drop_down("cbo_search_by", 170, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

//Not Used
if ($action == "create_lc_search_list_view") {
	$ex_data = explode("_", $data);
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company = $ex_data[2];

	if ($cbo_search_by == 1 && $txt_search_common != "") // lc number
	{
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where lc_number LIKE '%$search_string%' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	} else if ($cbo_search_by == 1 && $txt_search_common != "") //supplier
	{
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where supplier_id='$search_string' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	} else {
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	}

	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$arr = array(1 => $company_arr, 2 => $supplier_arr, 3 => $item_category);
	echo create_list_view("list_view", "LC No,Importer,Supplier Name,Item Category,Value", "120,150,150,120,120", "750", "260", 0, $sql, "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,item_category_id,0", $arr, "lc_number,importer_id,supplier_id,item_category_id,lc_value", "", '', '0,0,0,0,0,1');
	exit();
}

if ($action == "show_ile") {
	$ex_data = explode("**", $data);
	$company = $ex_data[0];
	$source = $ex_data[1];
	$rate = $ex_data[2];

	$sql = "select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=1 and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql, 1);
	foreach ($result as $row) {
		// NOTE :- ILE=standard, ILE% = standard/100*rate
		$ile = $row[csf("standard")];
		$ile_percentage = ($row[csf("standard")] / 100) * $rate;
		echo $ile . "**" . number_format($ile_percentage, $dec_place[3], ".", "");
		exit();
	}
	exit();
}

if ($action == "supplier_buyer_popup") {

	echo load_html_head_contents("Buyer/Supplier Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function js_set_value(str) {
			var splitData = str.split("_");
			$("#hdn_supplier_buyer_id").val(splitData[0]); // wo/pi id        	
			$("#hdn_supplier_buyer_name").val(splitData[1]); // wo/pi number
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="hdn_supplier_buyer_id" value="" />
	<input type="hidden" id="hdn_supplier_buyer_name" value="" />
	<?
	if ($basis == 14) {
		echo  create_list_view("list_view", "Buyer Name", "250", "260", "260", 0, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "js_set_value", "ID,BUYER_NAME", "", 1, "", "", "BUYER_NAME", "", 'setFilterGrid("list_view",-1);');
	} else {
		if ($user_supplier_ids != "") {
			$user_supplier_cond = "and c.id in ($user_supplier_ids)";
		} else {
			$user_supplier_cond = "";
		}

		echo  create_list_view("list_view", "Supplier Name", "250", "260", "260", 0, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company_id' $user_supplier_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "js_set_value", "ID,SUPPLIER_NAME", "", 1, "", "", "SUPPLIER_NAME", "", 'setFilterGrid("list_view",-1);');
	}
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$log_data = array();
	$hdnReceiveString = str_replace("'", "", $hdnReceiveString);
	$txt_weight_per_cone = (str_replace("'", "", $txt_weight_per_cone) == "") ? 0 : $txt_weight_per_cone;
	$txt_weight_per_bag = (str_replace("'", "", $txt_weight_per_bag) == "") ? 0 : $txt_weight_per_bag;

	$txt_rate = str_replace("'", "", $txt_rate);
	if ($txt_rate == "") $txt_rate = 0;
	$txt_ile = str_replace("'", "", $txt_ile);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$txt_exchange_rate = str_replace("'", "", $txt_exchange_rate);
	$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
	$txt_avg_rate = str_replace("'", "", $txt_avg_rate);
	if ($txt_avg_rate == "") $txt_avg_rate = 0;
	$txt_dyeing_charge = str_replace("'", "", $txt_dyeing_charge);
	if ($txt_dyeing_charge == "") $txt_dyeing_charge = 0;

	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$variable_store_wise_rate = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate != 1) $variable_store_wise_rate = 2;

	$store_wise_cond = ($variable_store_wise_rate == 1) ? " and store_id=$cbo_store_name" : "";
	$max_trans_query = sql_select("SELECT MAX ( CASE WHEN transaction_type IN (2, 3, 6) THEN id ELSE NULL END) AS max_id, 
		max(transaction_date) as max_date from inv_transaction where prod_id=$txt_prod_code $store_wise_cond and item_category=1 and status_active=1");

	if (!empty($max_trans_query)) {
		$max_transaction_date = $max_trans_query[0][csf('max_date')];

		if ($max_transaction_date != "") {
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date) {
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot \nReceive Date $receive_date \nLast Transaction Date $max_transaction_date";
				disconnect($con);
				die;
			}
		}

		if ($operation == 1 || $operation == 2) {
			$currentTransId = (int)str_replace("'", "", $update_id);
			$max_trans_id = (int)$max_trans_query[0][csf('max_id')];

			if ($max_trans_id > $currentTransId) {
				echo "20**Transaction found of this store and product ";
				disconnect($con);
				die;
			}
		}
	}

	$variable_set_invent = return_field_value("user_given_code_status", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=19 and item_category_id=1", "user_given_code_status");

	$variable_store_wise_rate = return_field_value("auto_transfer_rcv", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0", "auto_transfer_rcv");
	if ($variable_store_wise_rate != 1) $variable_store_wise_rate = 2;

	//for variable settings
	$sql_variable = "SELECT allocation AS ALLOCATION, sales_allocation AS SALES_ALLOCATION, smn_allocation AS SMN_ALLOCATION FROM variable_settings_inventory WHERE variable_list = 18 AND company_name = " . $cbo_company_id . " AND item_category_id = 1";
	$sql_variable_rslt = sql_select($sql_variable);
	$variable_set_allocation = 2;
	$variable_set_smn_allocation = 2;
	$is_sales_order = 2;
	$is_auto_allocation = 2;
	foreach ($sql_variable_rslt as $row) {
		$variable_set_allocation = $row['ALLOCATION'];
		$is_sales_order = $row['SALES_ALLOCATION'];
		$variable_set_smn_allocation = $row['SMN_ALLOCATION'];
	}
	unset($sql_variable_rslt);

	$sql_variable_auto = "SELECT auto_allocate_yarn_from_requis AS AUTO_ALLOCATION FROM variable_settings_production WHERE variable_list = 6 AND company_name = " . $cbo_company_id . " AND status_active = 1 AND is_deleted=0";
	$sql_variable_auto_rslt = sql_select($sql_variable_auto);
	foreach ($sql_variable_auto_rslt as $row) {
		$is_auto_allocation = $row['AUTO_ALLOCATION'];
	}
	unset($sql_variable_auto_rslt);

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent as over_rcv_percent_grey_yarn,over_rcv_percent_textile as over_rcv_percent_dyed_yarn,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 1 and over_rcv_payment=1 order by id");

	if ((str_replace("'", "", $cbo_receive_basis) == 1)) {
		$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent_grey_yarn')] : 0;
	} else if ((str_replace("'", "", $cbo_receive_basis) == 2)) {
		$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent_dyed_yarn')] : 0;
	} else {
		$over_receive_limit = 0;
	}

	if (str_replace("'", "", $cbo_receive_basis) == 2 && str_replace("'", "", $txt_wo_pi_id) > 0 && (str_replace("'", "", $cbo_receive_purpose) == 16 || str_replace("'", "", $cbo_receive_purpose) == 5)) {
		$sql_wo = sql_select("select pay_mode, payterm_id from wo_non_order_info_mst where id=" . str_replace("'", "", $txt_wo_pi_id) . " and status_active=1");
		$pay_mode = $sql_wo[0][csf("pay_mode")];
		$payterm_id = $sql_wo[0][csf("payterm_id")];
		if ($pay_mode != 1 && $pay_mode != 4) // CRM id :25579 
		{
			echo "40** WO Pay Mode Not Match.";
			disconnect($con);
			die;
		}
		if ($payterm_id == 5) {
			echo "40** WO Pay Term Not Match.";
			disconnect($con);
			die;
		}
	}

	if (str_replace("'", "", $cbo_receive_basis) == 1) {
		$pi_sql = sql_select("select ref_closing_status from com_pi_master_details where id=$txt_wo_pi_id");
		$ref_closing_status = $pi_sql[0][csf("ref_closing_status")];
		if ($ref_closing_status == 1) {
			echo "30** This PI is Already Closed.";
			disconnect($con);
			die;
		}
	}

	if (str_replace("'", '', $txt_mrr_no) != '') {
		$is_audited = return_field_value("is_audited", "inv_receive_master", "recv_number='" . str_replace("'", '', $txt_mrr_no) . "' and status_active=1 and is_deleted=0", "is_audited");
		//echo "10**$is_audited".'rakib';die;
		if ($is_audited == 1) {
			echo "50**This MRR is Audited. Save, Update and Delete Not Allowed..";
			disconnect($con);
			die;
		}
	}


	/* Prevent same WO/PI/FSO purpose mixing */
	$sql_rcv = "select count(id) as number_of_rcv,receive_purpose,listagg (recv_number_prefix_num,',') within group (order by id) as mrr_no from inv_receive_master where receive_basis=$cbo_receive_basis and booking_no = $txt_wo_pi and item_category = 1 and entry_form=1 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 group by receive_purpose";

	$rcv_result = sql_select($sql_rcv);

	if (!empty($rcv_result)) {
		$previous_receive_purpose = $yarn_issue_purpose[$rcv_result[0][csf('receive_purpose')]];
		$mrr_nos = $rcv_result[0][csf('mrr_no')];
		if ($rcv_result[0][csf('receive_purpose')] != str_replace("'", "", $cbo_receive_purpose)) {
			echo "50**In same WO/PI/FSO received purpose mixing is not allowed.\nPrevious MRR No:$mrr_nos\n Receive purpose: $previous_receive_purpose ";
			disconnect($con);
			die;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| for color
	| if new color entry from yarn receive page then
	|--------------------------------------------------------------------------
	|
	*/
	if (str_replace("'", "", $btn_color) == 'F') {
		// Simillar like order entry page
		if (str_replace("'", "", $cbo_color) != "") {
			$color_library = return_library_array("select id,color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
			if (!in_array(str_replace("'", "", $cbo_color), $new_array_color)) {
				$color_id = return_id(str_replace("'", "", $cbo_color), $color_library, "lib_color", "id,color_name", "1");
				$new_array_color[$color_id] = str_replace("'", "", $cbo_color);
			} else {
				$color_id = array_search(str_replace("'", "", $cbo_color), $new_array_color);
			}
		} else {
			$color_id = 0;
		}

		$cbo_color = $color_id;
	} else {
		$color_id = str_replace("'", "", $cbo_color);
	}
	/*
	|--------------------------------------------------------------------------
	| end for color
	|--------------------------------------------------------------------------
	|
	*/

	if (($operation == 0 || $operation == 1) && (str_replace("'", "", $cbo_receive_purpose) == 2)) {
		$dyeing_charge = return_field_value("dyeing_charge", "wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b ", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.ydw_no=$txt_wo_pi and a.id=$txt_wo_pi_id and b.yarn_color=$cbo_color and b.id=$hdnYarnDyingDtlsId and b.status_active=1 and b.is_deleted=0", "dyeing_charge");

		if ($dyeing_charge < 0.01) {
			echo "40**Dyeing charge can not be zero";
			disconnect($con);
			die;
		}
	}

	//for receive id
	$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");

	$sql_variable_challan = sql_select("select auto_transfer_rcv from variable_settings_inventory where company_name = $cbo_company_id and variable_list = 38 and status_active=1 and is_deleted=0");
	$variale_challan_dup = $sql_variable_challan[0][csf("auto_transfer_rcv")];

	if ($operation == 0 || $operation == 1) {
		if ((str_replace("'", "", $cbo_receive_basis) == 1)) {
			if ($db_type == 0) {
				$orderBy_cond = "IFNULL";
			} else if ($db_type == 2) {
				$orderBy_cond = "NVL";
			} else {
				$orderBy_cond = "ISNULL";
			}

			$variable_rcv_result = sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$cbo_company_id and variable_list='31' and status_active=1 and is_deleted=0");
			$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];

			if ($variable_rcv_level == 2) // ############## for wo pi dtls level
			{
				$pi_qty_data = sql_select("select sum(quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.status_active=1 and b.is_deleted = 0 and b.id=$txt_wo_pi_dtls_id");

				$whereCondition = " a.id=b.prod_id and b.mst_id = c.id and b.company_id=$cbo_company_id and a.supplier_id=$cbo_supplier and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_req_dtls_id=" . $txt_wo_pi_dtls_id . " and b.receive_basis=$cbo_receive_basis and b.status_active=1 and b.is_deleted = 0";

				$recev_return = sql_select("select sum(c.cons_quantity) as recv_rtn_qnty from  inv_receive_master a, inv_issue_master b,inv_transaction c left join product_details_master d on c.prod_id=d.id and d.status_active=1 and d.is_deleted=0 where a.pi_id=$txt_wo_pi_id and a.id=b.received_id and b.id=c.mst_id and a.entry_form = 1 and a.receive_basis = 2 and a.status_active=1 and b.status_active=1 and c.transaction_type=3 and b.entry_form=8");
			} else // ############## for wo pi item level
			{
				$percentage2 = (str_replace("'", "", $percentage2) == "") ? 0 : $percentage2;
				$cbocomposition2 = (str_replace("'", "", $cbocomposition2) == "") ? 0 : $cbocomposition2;

				$whereCondition = "a.id=b.prod_id and b.mst_id = c.id and a.yarn_count_id=" . $cbo_yarn_count . " and a.yarn_comp_type1st=" . $cbocomposition1 . " and a.yarn_comp_percent1st=" . $percentage1 . " and $orderBy_cond(a.yarn_comp_type2nd,0)=" . $cbocomposition2 . " and $orderBy_cond(a.yarn_comp_percent2nd,0)=" . $percentage2 . " and a.yarn_type=" . $cbo_yarn_type . " and a.color=" . $cbo_color . " and b.company_id=$cbo_company_id and a.supplier_id=$cbo_supplier and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_batch_no=" . $txt_wo_pi_id . " and b.receive_basis=$cbo_receive_basis and b.status_active=1 and b.is_deleted = 0";

				$pi_qty_data = sql_select("select sum(quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.order_source<>5 and b.pi_id=$txt_wo_pi_id and b.color_id=$cbo_color and b.yarn_composition_item1=$cbocomposition1 and b.yarn_composition_percentage1=$percentage1 and b.yarn_type=$cbo_yarn_type and b.status_active=1 and b.is_deleted = 0 and b.count_name=" . $cbo_yarn_count . "");
			}

			$mrr_result = sql_select("select b.id as trans_id, b.cons_quantity as recv_qnty, c.recv_number, a.id as prod_id 
			from product_details_master a, inv_transaction b, inv_receive_master c 
			where " . $whereCondition . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

			$prev_rev_qnty = 0;
			foreach ($mrr_result as $val) {
				if (str_replace("'", "", $update_id) != $val[csf("trans_id")]) {
					$prev_rev_qnty += $val[csf("recv_qnty")];
				}
				$mrr_arr[$val[csf("recv_number")]] = $val[csf("recv_number")];
				$prod_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
			}
			unset($mrr_result);

			$mrr_nos = "'" . implode("','", array_filter($mrr_arr)) . "'";
			$prod_ids = implode(",", array_filter($prod_arr));

			if (str_replace("'", "", $mrr_nos) != "" && $prod_arr != "") {
				$direct_mrr_return_res = sql_select("select c.id,sum(b.cons_quantity) as ret_qnty from product_details_master a,inv_transaction b , inv_issue_master c where a.id=b.prod_id and b.mst_id = c.id and c.entry_form = 8 and c.received_mrr_no in ($mrr_nos) and a.id in ($prod_ids) and b.status_active = 1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 group by c.id");

				$rcvReturnQnty = 0;
				foreach ($direct_mrr_return_res as $val) {
					$rcvReturnQnty += $val[csf("ret_qnty")];
					$direct_mrr_rtn_id[$val[csf("id")]] = $val[csf("id")];
				}
				unset($direct_mrr_return_res);

				$direct_mrr_rtn_ids = implode(",", array_filter($direct_mrr_rtn_id));

				if ($direct_mrr_rtn_ids != "") {	//Rcv return from Issuer Return
					$mrr_rcv_return_not_cond = "and a.id not in($direct_mrr_rtn_ids)";
				}

				if ($prod_ids != '') {
					$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty
					from  inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1 and b.transaction_type=3 and b.status_active = 1 and b.is_deleted = 0 and b.prod_id in ($prod_ids) $mrr_rcv_return_not_cond");
				}

				foreach ($rcvReturnQntyFromIssueRetArr as $val) {
					$rcvReturnQnty += $val[csf("return_qnty")];
				}
			}

			$piQnty = $pi_qty_data[0][csf('quantity')];
			$over_receive_limit_qnty = ($over_receive_limit > 0) ? ($over_receive_limit / 100) * $piQnty : 0;
			$allow_total_qty = number_format(($piQnty + $over_receive_limit_qnty), 2, '.', '');
			$overRecvLimitMsg = "Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);

			$total_recvQnty = number_format((($txt_receive_qty + $prev_rev_qnty) - $rcvReturnQnty), 2, '.', '');

			//echo "40**".$prev_rev_qnty."==".$rcvReturnQnty."test"; die();
			if ($allow_total_qty < $total_recvQnty) {
				echo "40**Receive quantity can not be greater than PI quantity\nPI quantity=$piQnty\n$overRecvLimitMsg $over_msg\nAllowed quantity = $allow_total_qty\nPrevious Receive quantity = $prev_rev_qnty\nU Input = $txt_receive_qty";
				disconnect($con);
				die;
			}
		}
	}

	// Yarn service process loss Variable setting
	$vs_sqlResult = sql_select("select category as service_type,over_rcv_percent as process_percentage,over_rcv_payment as process_control_status from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=22 and category=" . str_replace("'", "", $cbo_receive_purpose) . " order by id");

	$vs_service_type = $vs_sqlResult[0][csf('service_type')];
	$vs_process_percentage = $vs_sqlResult[0][csf('process_percentage')];
	$vs_process_control_status = $vs_sqlResult[0][csf('process_control_status')];
	$vs_process_control_validation = (($vs_service_type == str_replace("'", "", $cbo_receive_purpose)) && $vs_process_control_status == 1 && $vs_process_percentage > 0) ? 1 : 0;

	//echo "10**".$vs_process_control_validation; oci_rollback($con); die();

	// lot type 
	if (str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 44 || str_replace("'", "", $cbo_receive_purpose) == 43 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51) $dyed_type = 1;
	else $dyed_type = 2;

	if ($operation == 0) //Insert Here
	{
		$flag = 1;
		//--------Check Receive control on Gate Entry according to variable settings inventory--------//
		if ($variable_set_invent == 1) {
			$challan_no = str_replace("'", "", $txt_challan_no);
			if ($challan_no != "") {
				$variable_set_invent = return_field_value("a.id as id", " inv_gate_in_mst a,  inv_gate_in_dtl b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
				if (empty($variable_set_invent)) {
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					disconnect($con);
					die;
				}
			}
		}

		$challan_no = str_replace("'", "", $txt_challan_no);
		if ($challan_no != "") {
			if ($variale_challan_dup == 2) {
				$supplier_id = str_replace("'", "", $cbo_supplier);
				if ($recieve_id) $mrr_cond = " and id<>$recieve_id";
				$duplicate_chalan = is_duplicate_field("id", "inv_receive_master ", "status_active=1 and is_deleted=0 and supplier_id=$supplier_id and challan_no='$challan_no' $mrr_cond and entry_form=1");
				if ($duplicate_chalan == 1) {
					echo "30**Duplicate Challan No. Not Allow.";
					disconnect($con);
					die;
				}
			}
		}

		//---------------End Check Receive control on Gate Entry---------------------------//

		//-------------Yarn Service Booking. Receive can not be greater than service booking(wo/booking) ----//
		if ((str_replace("'", "", $cbo_receive_basis) == 2)) {
			$cbo_receive_purpose = str_replace("'", "", $cbo_receive_purpose);
			$cbo_color = str_replace("'", "", $cbo_color);

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 44  || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {
				//echo "10**==".$hdn_entry_form; die;
				if (str_replace("'", "", $hdn_entry_form) == 125 || str_replace("'", "", $hdn_entry_form) == 114) // Withou lot work order 
				{
					$lot_parametter_condition_issue = " and c.yarn_comp_type1st=" . $cbocomposition1 . "";
					$lot_parametter_condition_rcv = " and c.yarn_comp_type1st=" . $cbocomposition1 . " and c.color=" . $cbo_color . "  ";
				} else {
					$lot_parametter_condition_rcv = " and c.yarn_type=" . $cbo_yarn_type . " and c.color=" . $cbo_color . " and c.yarn_count_id=" . $cbo_yarn_count . " and c.yarn_comp_type1st=" . $cbocomposition1 . " and c.yarn_comp_percent1st=" . $percentage1 . "";
					$lot_parametter_condition_issue = " and c.yarn_type=" . $cbo_yarn_type . " and c.yarn_count_id=" . $cbo_yarn_count . " and c.yarn_comp_type1st=" . $cbocomposition1 . " and c.yarn_comp_percent1st=" . $percentage1 . "";
				}

				$sql_exis_servc_recvQnty = sql_select("select listagg(a.recv_number_prefix_num, ',') within group (order by a.id) as rcv_no,a.booking_id,sum(b.cons_quantity) as recv_quantity,sum(b.grey_quantity) as grey_quantity from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and a.company_id=" . $cbo_company_id . " and a.booking_id=" . $txt_wo_pi_id . " and b. transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.receive_purpose=" . $cbo_receive_purpose . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lot_parametter_condition_rcv group by a.booking_id"); // and c.lot='$txt_yarn_lot'

				$sql_wo_yarn_qnty = sql_select("select a.id,sum(b.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.id=$txt_wo_pi_id and a.entry_form in(41,42,94,114,125,135) and b.id=$hdnYarnDyingDtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id");
			} else {
				$sql_exis_servc_recvQnty = sql_select("select listagg(a.recv_number_prefix_num, ',') within group (order by a.id) as rcv_no,a.booking_id,sum(b.cons_quantity) as recv_quantity from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and a.company_id=" . $cbo_company_id . " and a.booking_id=" . $txt_wo_pi_id . " and b. transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.receive_purpose=" . $cbo_receive_purpose . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.yarn_type=" . $cbo_yarn_type . " and c.color=" . $cbo_color . " and c.yarn_count_id=" . $cbo_yarn_count . " and c.yarn_comp_type1st=" . $cbocomposition1 . " and yarn_comp_percent1st=" . $percentage1 . " group by a.booking_id");

				$variable_rcv_result = sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$cbo_company_id and variable_list='31' and item_category_id=1 and status_active=1 and is_deleted=0");

				$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];

				if ($variable_rcv_level == 2) // for wo pi dtls level
				{
					$sql_wo_yarn_qnty = sql_select("select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, b.id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, b.rate, b.supplier_order_quantity as yarn_wo_qty
					from wo_non_order_info_mst a, wo_non_order_info_dtls b
					where a.id=b.mst_id and a.id=$txt_wo_pi_id  and b.id=$txt_wo_pi_dtls_id and a.company_name=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				} else // for wo pi dtls level
				{
					if ($db_type == 0) {
						$sql_wo_yarn_qnty = sql_select("select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, group_concat(b.id) as id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, avg(b.rate) as rate, sum(b.supplier_order_quantity) as yarn_wo_qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id=$txt_wo_pi_id and a.company_name=$cbo_company_id and b.yarn_count=$cbo_yarn_count and b.yarn_comp_type1st=$cbocomposition1 and b.yarn_type = " . $cbo_yarn_type . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.company_name, a.supplier_id, a.wo_number, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name");
					} else {
						$sql_wo_yarn_qnty = sql_select("select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, avg(b.rate) as rate, sum(b.supplier_order_quantity) as yarn_wo_qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.id=" . $txt_wo_pi_id . " and a.company_name=" . $cbo_company_id . " and b.yarn_count=" . $cbo_yarn_count . " and b.yarn_comp_type1st=" . $cbocomposition1 . " and b.yarn_type = " . $cbo_yarn_type . " and b.color_name=" . $cbo_color . " and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.company_name, a.supplier_id, a.wo_number, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name");
					}
				}
			}

			$woYarnQnty = $sql_wo_yarn_qnty[0][csf('yarn_wo_qty')];

			//issue check only for service
			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 44 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {
				if ($cbo_receive_purpose == 15 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {
					$sql_issue_qnty = sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(15,50,51) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				} else {
					if ($cbo_receive_purpose == 2) {
						$color_cond = "and b.dyeing_color_id=$cbo_color";
					}

					$sql_issue_qnty = sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(2,12,38,44,46) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id $lot_parametter_condition_issue $color_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}

				$issueQnty_service = $sql_issue_qnty[0][csf('cons_quantity')];
			}

			// ===========  Deduction all kind of received return qunatity start ========//
			if ($txt_wo_pi_id != "") {
				$sql_issue_transec = sql_select("select b.mst_id,b.id,c.issue_trans_id,c.entry_form,d.id
				from  inv_receive_master a , inv_transaction b, inv_mrr_wise_issue_details c
				left join inv_transaction d on c.issue_trans_id=d.id and d.status_active=1 and d.transaction_type=2
				where a.id=b.mst_id and  b.id=c.recv_trans_id
				and a.booking_id = $txt_wo_pi_id and b.transaction_type=1 and a.entry_form=1 and c.entry_form=3
				and a.status_active=1 and b.status_active=1 and c.status_active=1
				and a.item_category=1 and b.transaction_type=1");

				foreach ($sql_issue_transec as $trn_issue_row) {
					$issue_trans_id .= $trn_issue_row[csf('issue_trans_id')] . ",";
				}

				$issue_trans_id = chop($issue_trans_id, " , ");
			}

			if ($issue_trans_id != "") {
				$issue_id_from_issue_rtn = sql_select("select a.id from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.id in($issue_trans_id) and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				foreach ($issue_id_from_issue_rtn as $issue_row) {
					$issue_id .= $issue_row[csf('id')] . ",";
				}

				$issue_ids = chop($issue_id, " , ");
			}

			if ($issue_ids != "") {
				$sql_issue_rtn_rcv = sql_select("select a.id as received_id  from inv_receive_master a,inv_transaction b where a.id=b.mst_id and a.issue_id in($issue_ids) and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

				foreach ($sql_issue_rtn_rcv as $iss_rcv_row) {
					$total_rcv_id[] = $iss_rcv_row[csf('received_id')];
				}
			}

			if ($cbo_receive_purpose == 16) {
				$wo_pi_cond = "and e.pi_wo_batch_no = $txt_wo_pi_id";
			} else {
				$wo_pi_cond = "and d.booking_id = $txt_wo_pi_id";
			}

			if (str_replace("'", "", $job_no) != "") {
				$job_no_cond = "and e.job_no = $job_no";
			}

			$mrr_rcv_id_sql = sql_select("select d.id as received_id from inv_receive_master d, inv_transaction e where d.id = e.mst_id and e.transaction_type = 1 and e.item_category = 1 and e.receive_basis = 2 and d.receive_purpose = $cbo_receive_purpose and d.entry_form = 1 $wo_pi_cond $job_no_cond and d.is_deleted = 0 and d.status_active = 1 and d.company_id = $cbo_company_id group by d.id");

			foreach ($mrr_rcv_id_sql as $rcv_row) {
				$total_rcv_id[] = $rcv_row[csf('received_id')];
			}

			$total_received_ids = implode(",", array_unique($total_rcv_id));

			if ($total_received_ids != "") {
				$sql_return_recev = sql_select("select sum(a.cons_quantity) as rec_ret_qnty from inv_transaction a, inv_issue_master b, product_details_master c where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3 and a.company_id = $cbo_company_id and b.is_deleted = 0 and b.status_active = 1 and c.color = $cbo_color $lot_cond and b.received_id in ($total_received_ids)");
				$return_receiveQnty = $sql_return_recev[0][csf('rec_ret_qnty')];
			}
			// =========== End Deduction all kind of received return qunatity  ========//	


			$over_receive_limit = (float)$over_receive_limit;
			$over_receive_limit_qnty = ($over_receive_limit > 0) ? ($over_receive_limit / 100) * $woYarnQnty : 0;
			//echo "10**".$over_receive_limit_qnty; die; 

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 44 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {
				$wo_qnty = $issueQnty_service;
				$allow_total_val = $issueQnty_service + $over_receive_limit_qnty;
				//echo "10**".$over_receive_limit_qnty; die;
				$issud_msg = "Issue";
				$overRecvLimitMsg = "Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			} else {
				$wo_qnty = $woYarnQnty;
				$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;

				$overRecvLimitMsg = "Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";

				if ((str_replace("'", "", $cbo_receive_basis) == 2)) {
					$issud_msg = "WO. Quantity";
				} else if ((str_replace("'", "", $cbo_receive_basis) == 1)) {
					$issud_msg = "PI. Quantity";
				}
			}

			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
			$hdn_receive_qty = str_replace("'", "", $hdn_receive_qty);
			$total_recvQnty = ($sql_exis_servc_recvQnty[0][csf('recv_quantity')] - $hdn_receive_qty) + $txt_receive_qty;

			$allow_total_val = number_format($allow_total_val, 2, '.', '');
			$over_receive_limit_qnty = number_format($over_receive_limit_qnty, 2, '.', '');
			$balance = number_format(($total_recvQnty - $return_receiveQnty), 2, '.', '');

			// Grey used qnty validation depends on yarn service process loss control variable setting		
			if ($vs_process_control_validation == 0 && ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 44)) {
				$txt_grey_qty = str_replace("'", "", $txt_grey_qty) * 1;
				$txt_receive_qty = str_replace("'", "", $txt_receive_qty) * 1;

				$hdn_grey_qty = str_replace("'", "", $hdn_grey_qty);
				$total_greyQnty = (($sql_exis_servc_recvQnty[0][csf('grey_quantity')] - $return_receiveQnty) + $txt_grey_qty);
				$grey_quantity_balance = number_format(($total_greyQnty), 2, '.', '');

				if ($txt_grey_qty < $txt_receive_qty) {
					echo "40**Grey quantity can not be less than received quantity";
					disconnect($con);
					die;
				}

				if (str_replace("'", "", $hdn_entry_form) == 135) {
					$zs = number_format(($allow_total_val), 2, '.', '');
				} else {
					$zs = number_format(($allow_total_val - $over_receive_limit_qnty), 2, '.', '');
				}

				//echo "40**".$allow_total_val."-".$over_receive_limit_qnty."-".$zs."<".$grey_quantity_balance; die();
				//echo "40**".$zs ."<". $grey_quantity_balance."balance=". number_format(($wo_qnty-$actualGreyQty),2,'.',''); die();
				//echo  "40**".$zs ."<". $grey_quantity_balance;

				if ($zs < $grey_quantity_balance) {
					$thismrrGreyQty = str_replace("'", "", $hdn_grey_qty);
					$previousGreyQty = $sql_exis_servc_recvQnty[0][csf('grey_quantity')];
					$rcvNumbers = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
					$actualGreyQty = (($previousGreyQty - $return_receiveQnty) + $thismrrGreyQty);
					//$over_grey_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
					$allowedGreyBalance = number_format(($wo_qnty - $actualGreyQty), 2, '.', '');

					echo "40**Grey quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty\nReceived No=$rcvNumbers\nGrey Qty = $actualGreyQty\nBalance = $allowedGreyBalance";
					disconnect($con);
					die;
				}
			}

			// echo "40**".$allow_total_val."<".$balance; die();
			if ($allow_total_val < $balance) {
				$thismrrRcv = str_replace("'", "", $hdn_receive_qty);
				$previousRcv = $sql_exis_servc_recvQnty[0][csf('recv_quantity')];
				$rcvNumbers = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
				$actualRcv = (($previousRcv + $thismrrRcv) - $return_receiveQnty);
				$over_msg = ($over_receive_limit > 0) ? "\nAllowed Quantity = $allow_total_val" : "";
				$allowedBalance = number_format(($wo_qnty - $actualRcv), 2, '.', '');
				echo "40**Recv. quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty\nReceived No=$rcvNumbers\nReceived Qty = $actualRcv\n$overRecvLimitMsg $over_msg\nBalance = $allowedBalance";
				disconnect($con);
				die;
			}
		}
		//-------------End Yarn Service Booking. Receive can not be greater than service booking(wo/booking) ----//



		//---------------Check Brand---------------------------//
		if (str_replace("'", "", $txt_brand) != "") {
			//$brand_library = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
			//$txt_brand = return_id(str_replace("'", "", $txt_brand), $brand_library, "lib_brand", "id,brand_name");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

			if (str_replace("'", "", trim($txt_brand)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_brand)), $new_array_brand)) {
					$brand_id = return_id(str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name", "1");
					$new_array_brand[$brand_id] = str_replace("'", "", trim($txt_brand));
				} else {
					$brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
				}
			} else {
				$brand_id = 0;
			}

			$txt_brand = $brand_id;
		}
		//----------------Check Brand END---------------------//

		//---------------Check Product ID --------------------------//
		$insertR = true;
		$rtnString = return_product_id($cbo_yarn_count, $cbocomposition1, $cbocomposition2, $percentage1, $percentage2, $cbo_yarn_type, $color_id, $txt_yarn_lot, $txt_prod_code, $cbo_company_id, $cbo_supplier, $cbo_store_name, $cbo_uom, $yarn_type, $composition, $cbo_receive_purpose, $hdnPayMode);
		$expString = explode("***", $rtnString);

		if ($expString[0] == true && $expString[0] != "") {
			$prodMSTID = $expString[1];
		} else {
			$field_array = $expString[1];
			$data_array = $expString[2];
			//echo "20**"."insert into product_details_master (".$field_array.") values ".$data_array;die;
			$insertR = sql_insert("product_details_master", $field_array, $data_array, 0);
			$prodMSTID = $expString[3];
		}

		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id", "inv_receive_master a, inv_transaction b", "a.id = b.mst_id and a.recv_number = " . $txt_mrr_no . " and b.prod_id = " . $prodMSTID . " and b.transaction_type = 1 and a.item_category = 1");
		if ($duplicate == 1 && str_replace("'", "", $txt_mrr_no) == "") {
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		//------------------------------Check Duplicate product END---------------------------------------//

		//---------------Check yarn sticker page ------------------------//
		$sticker_cond = "";
		if (str_replace("'", "", $cbo_receive_basis) == 1) {
			$sticker_cond = " and receive_basis=1";
		} else if (str_replace("'", "", $cbo_receive_basis) == 2) {
			if (str_replace("'", "", $cbo_receive_purpose) == 2)
				$sticker_cond = " and receive_basis in(3,4)";
			else
				$sticker_cond = " and receive_basis=2";
		}

		if (str_replace("'", "", $cbo_receive_basis) == 1 || str_replace("'", "", $cbo_receive_basis) == 2) {
			$wo_pi_stiker = return_field_value("wo_pi_no", "com_yarn_bag_sticker", " status_active=1 and wo_pi_no=$txt_wo_pi_id  $sticker_cond", "wo_pi_no");
			if ($wo_pi_stiker != "") {
				echo "20**Yarn Sticker Found, Receive Not Allow.";
				disconnect($con);
				die;
			}
		}

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty,allocated_qnty from product_details_master where id = " . $prodMSTID . " and status_active = 1 and is_deleted = 0");
		$presentStock = $presentStockValue = $presentAvgRate = $allocated_qnty = $available_qnty = 0;
		$product_name_details = "";
		foreach ($sql as $result) {
			$presentStock = $result[csf("current_stock")];
			$presentStockValue = $result[csf("stock_value")];
			$presentAvgRate = $result[csf("avg_rate_per_unit")];
			$product_name_details = $result[csf("product_name_details")];
			$available_qnty = $result[csf("available_qnty")];
			$allocated_qnty = $result[csf("allocated_qnty")];
		}
		//----------------Check Product ID END---------------------//

		if (str_replace("'", "", $txt_mrr_no) != "") {
			$new_recv_number[0] = str_replace("'", "", $txt_mrr_no);
			$prev_dataArray = sql_select("select id, currency_id from inv_receive_master where recv_number = " . $txt_mrr_no . "");
			$id = $prev_dataArray[0][csf('id')];
			$prev_currency_id = $prev_dataArray[0][csf('currency_id')];

			if (str_replace("'", "", $cbo_currency) != $prev_currency_id) {
				echo "30**Multiple Currency Not Allow In Same MRR Number";
				disconnect($con);
				die;
			}
			//yarn master table UPDATE here START----------------------//
			$field_array = "item_category*receive_basis*receive_purpose*receive_date*challan_date*booking_id*booking_no*challan_no*store_id*exchange_rate*currency_id*supplier_id*loan_party*yarn_issue_challan_no*issue_id*lc_no*source*remarks*gate_entry_no*gate_entry_date*updated_by*update_date";
			$data_array = "1*" . $cbo_receive_basis . "*" . $cbo_receive_purpose . "*" . $txt_receive_date . "*" . $txt_challan_date . "*" . $txt_wo_pi_id . "*" . $txt_wo_pi . "*" . $txt_challan_no . "*" . $cbo_store_name . "*" . $txt_exchange_rate . "*" . $cbo_currency . "*" . $cbo_supplier . "*" . $cbo_party . "*" . $txt_issue_challan_no . "*" . $txt_issue_id . "*" . $hidden_lc_id . "*" . $cbo_source . "*" . $txt_mst_remarks . "*" . $txt_gate_entry_no . "*" . $txt_gate_entry_date . "*'" . $user_id . "'*'" . $pc_date_time . "'";

			//for transaction log
			$log_ref_number = str_replace("'", "", $txt_mrr_no);
			//yarn master table UPDATE here END---------------------------------------//
		} else {
			// yarn master table entry here START---------------------------------------//
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			if ($db_type == 0)
				$year_cond = "YEAR(insert_date)";
			else if ($db_type == 2)
				$year_cond = "to_char(insert_date,'YYYY')";
			else
				$year_cond = "";

			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con, 1, $cbo_company_id, 'YRV', 1, date("Y", time()), 1));

			$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_basis, receive_purpose, receive_date, challan_date, booking_id, booking_no, challan_no, store_id, exchange_rate, currency_id, supplier_id, loan_party, yarn_issue_challan_no, issue_id, lc_no, source,remarks,gate_entry_no, gate_entry_date,boe_mushak_challan_no, boe_mushak_challan_date, inserted_by, insert_date";
			$data_array = "(" . $id . ",'" . $new_recv_number[1] . "','" . $new_recv_number[2] . "','" . $new_recv_number[0] . "',1,1," . $cbo_company_id . "," . $cbo_receive_basis . "," . $cbo_receive_purpose . "," . $txt_receive_date . "," . $txt_challan_date . "," . $txt_wo_pi_id . "," . $txt_wo_pi . "," . $txt_challan_no . "," . $cbo_store_name . "," . $txt_exchange_rate . "," . $cbo_currency . "," . $cbo_supplier . "," . $cbo_party . "," . $txt_issue_challan_no . "," . $txt_issue_id . "," . $hidden_lc_id . "," . $cbo_source . "," . $txt_mst_remarks . "," . $txt_gate_entry_no . "," . $txt_gate_entry_date . "," . $txt_boe_mushak_challan_no . "," . $txt_boe_mushak_challan_date . ",'" . $user_id . "','" . $pc_date_time . "')";

			//for transaction log
			$log_ref_number = $new_recv_number[0];
			// yarn master table entry here END---------------------------------------//
		}

		// yarn details table entry here START-----------------------------------//


		$rate = $txt_rate;
		if ($txt_ile == '') $txt_ile = 0;
		$ile = ($txt_ile / $rate) * 100; // ile cost to ile
		$ile = (is_nan($ile)) ? 0 : $ile;
		$ile_cost = $txt_ile; //ile cost = (ile/100)*rate
		$order_amount = (($txt_receive_qty * $rate) + $ile_cost);
		$exchange_rate = $txt_exchange_rate;
		$conversion_factor = 1; // yarn always KG
		$domestic_rate = return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor);
		$cons_rate = number_format($domestic_rate, $dec_place[3], ".", ""); //number_format($rate*$exchange_rate,$dec_place[3],".","");
		$con_amount = $cons_rate * $txt_receive_qty;
		$con_ile = $ile; //($ile/$domestic_rate)*100;
		$con_ile_cost = ($ile / 100) * ($rate * $exchange_rate);
		$con_ile_cost = (is_nan($con_ile_cost)) ? 0 : $con_ile_cost;

		$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,origin_prod_id,product_code,item_category,transaction_type,transaction_date,store_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_avg_rate,dye_charge,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,no_of_bags,cone_per_bag,no_loose_cone,weight_per_bag,weight_per_cone,room,rack,self,bin_box,floor_id,remarks,job_no,buyer_id,inserted_by,insert_date,pi_wo_req_dtls_id,grey_quantity,booking_no,dyeing_color_id,store_rate,store_amount";

		$data_array_trans = "(" . $dtlsid . "," . $id . "," . $cbo_receive_basis . "," . $txt_wo_pi_id . "," . $cbo_company_id . "," . $cbo_supplier . "," . $prodMSTID . "," . $prodMSTID . "," . $txt_prod_code . ",1,1," . $txt_receive_date . "," . $cbo_store_name . "," . $txt_brand . "," . $cbo_uom . "," . $txt_receive_qty . "," . number_format($txt_rate, 10, ".", "") . "," . $ile . "," . $ile_cost . "," . number_format($order_amount, 8, ".", "") . "," . $cbo_uom . "," . $txt_receive_qty . "," . number_format($cons_rate, 10, ".", "") . "," . number_format($txt_avg_rate, 10, ".", "") . "," . $txt_dyeing_charge . "," . $con_ile . "," . $con_ile_cost . "," . number_format($con_amount, 8, ".", "") . "," . $txt_receive_qty . "," . $con_amount . "," . $txt_no_bag . "," . $txt_cone_per_bag . "," . $txt_no_loose_cone . "," . $txt_weight_per_bag . "," . $txt_weight_per_cone . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_floor . "," . $txt_remarks . "," . $job_no . ",'" . $cbo_buyer_name . "','" . $user_id . "','" . $pc_date_time . "'," . $txt_wo_pi_dtls_id . "," . $txt_grey_qty . "," . $txt_wo_pi . "," . $cbo_color . "," . number_format($cons_rate, 10, ".", "") . "," . number_format($con_amount, 8, ".", "") . ")";
		//yarn details table entry here END-----------------------------------//

		//product master table data UPDATE START----------------------------------------------------------//
		$stock_value = $domestic_rate * $txt_receive_qty;
		$currentStock = $presentStock + $txt_receive_qty;

		$StockValue = $presentStockValue + $stock_value;
		$avgRate = $StockValue / $currentStock;
		$is_without_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=" . $txt_wo_pi_id . "", "entry_form");
		$is_with_order_yarn_service_work_order = return_field_value("booking_without_order", "wo_yarn_dyeing_mst", " status_active=1 and id=" . $txt_wo_pi_id . "", "booking_without_order");

		// yarn allocation variable set to yes 
		if ($variable_set_allocation == 1) {
			if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 44 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51) && (str_replace("'", "", $cbo_receive_basis) == 2)) {
				if (str_replace("'", "", $cbo_receive_purpose) == 2 && ($is_without_order == 42 || $is_without_order == 114)) {
					if ($variable_set_smn_allocation == 1) {
						$allocated_qnty = $allocated_qnty + $txt_receive_qty;
						$available_qnty = $available_qnty;
					} else {
						$allocated_qnty = $allocated_qnty;
						$available_qnty = $available_qnty + $txt_receive_qty;
					}
				} else {
					if ($is_sales_order == 1 && $is_auto_allocation == 1 && ($is_without_order == 135 || ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2))) {
						$allocated_qnty = $allocated_qnty;
						$available_qnty = $available_qnty + $txt_receive_qty;
					} else {
						if ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2) {
							$allocated_qnty = $allocated_qnty;
							$available_qnty = $available_qnty + $txt_receive_qty;
						} else {
							$allocated_qnty = $allocated_qnty + $txt_receive_qty;
							$available_qnty = $available_qnty;
						}
					}
				}
			} else {
				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty + $txt_receive_qty;
			}
		} else {
			$allocated_qnty = $allocated_qnty;
			$available_qnty = $available_qnty + $txt_receive_qty;
		}

		if (str_replace("'", "", $txt_brand) == "")
			$txt_brand = 0;
		$field_array_prod_update = "brand*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod_update = "" . $txt_brand . "*" . number_format($avgRate, 10, ".", "") . "*" . $txt_receive_qty . "*" . $currentStock . "*" . number_format($StockValue, 8, ".", "") . "*'" . $allocated_qnty . "'*'" . $available_qnty . "'*'" . $user_id . "'*'" . $pc_date_time . "'";

		$store_up_id = 0;
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$prodMSTID and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

			if (count($sql_store) < 1) {
				$field_array_store = "id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

				$sdtlsid = return_next_id("id", "inv_store_wise_yarn_qty_dtls", 1);
				$data_array_store = "(" . $sdtlsid . "," . $cbo_company_id . "," . $cbo_store_name . ",1," . $prodMSTID . "," . $txt_receive_qty . "," . number_format($cons_rate, 10, ".", "") . "," . number_format($con_amount, 8, ".", "") . "," . $txt_receive_qty . ",'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "'," . $txt_yarn_lot . "," . $txt_receive_date . "," . $txt_receive_date . ")";
			} else {
				if (count($sql_store) > 1) {
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

					$store_currentStock = $store_presentStock + $txt_receive_qty;
					$store_StockValue	= $store_presentStockValue + $con_amount;
					$store_avgRate = 0;
					if ($store_StockValue != 0 && $store_currentStock != 0) $store_avgRate = abs($store_StockValue / $store_currentStock);

					$field_array_store = "rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";
					$data_array_store = "" . number_format($store_avgRate, 10, ".", "") . "*" . $txt_receive_qty . "*" . $store_currentStock . "*" . number_format($store_StockValue, 8, ".", "") . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*" . $txt_receive_date . "";
				}
			}
		}

		//for transaction log
		$log_data['entry_form'] = 1;
		$log_data['ref_id'] = $id;
		$log_data['ref_number'] = $log_ref_number;
		$log_data['product_id'] = $prodMSTID;
		$log_data['current_stock'] = $currentStock;
		$log_data['allocated_qty'] = $allocated_qnty;
		$log_data['available_qty'] = $available_qnty;
		$log_data['dyed_type'] = $dyed_type;

		$log_data['insert_date'] = $pc_date_time;
		//end for transaction log

		if ($variable_set_allocation == 1) {
			// update dyied yarn allocation start
			$is_sales = 0;
			if ((str_replace("'", "", $cbo_receive_basis) == 2) && (str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 44 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51)) {
				if (str_replace("'", "", $cbo_receive_purpose) == 2 && ($is_without_order == 42 || $is_without_order == 114)) {
					if ($variable_set_smn_allocation != 1) {
						$allocation_mst_insert = 1;
						$allocation_dtls_insert = 1;
					} else {
						/*
						|--------------------------------------------------------------------------
						| for booking no
						|--------------------------------------------------------------------------
						|
						*/
						$sqlBookingNo = "SELECT  a.is_sales, c.id, c.booking_no FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c WHERE a.id = b.mst_id AND b.booking_no = c.booking_no AND a.id = " . $txt_wo_pi_id . " AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form in (42,114)"; //AND b.product_id = ".$prodMSTID."
						$sqlBookingNo = sql_select($sqlBookingNo);
						$bookingId = '';
						$bookingNo = '';
						foreach ($sqlBookingNo as $row) {
							$bookingId = $row[csf('id')];
							$bookingNo = $row[csf('booking_no')];
							$is_sales = $row[csf('is_sales')];
						}

						$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.booking_no = '" . $bookingNo . "' AND a.item_id = " . $prodMSTID . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
						$check_allocation_info = sql_select($sqlAllocation);
						if (!empty($check_allocation_info)) {
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_mst
							| data preparing and updating
							|--------------------------------------------------------------------------
							|
							*/
							foreach ($check_allocation_info as $row) {
								$allocation_id = $row[csf('id')];
								$allocation_dtls_id = $row[csf('dtls_id')];
								$allocation_qnty = $row[csf('qnty')] + $txt_receive_qty;
								$qnty_breakdown = $allocation_qnty . '_' . $bookingId . '_';
							}

							$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
							$data_allocation = "" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
							$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", $allocation_id, 0);
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data preparing and updating
							|--------------------------------------------------------------------------
							|
							*/
							$field_allocation_dtls = "qnty*updated_by*update_date";
							$data_allocation_dtls = "" . $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

							$allocation_dtls_insert = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", $allocation_dtls_id, 0);
						} else {
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_mst

							| data preparing and inserting
							|--------------------------------------------------------------------------
							|
							*/
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation = "id,mst_id,entry_form,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,booking_without_order,qnty_break_down,is_sales,inserted_by,insert_date";
							$qnty_breakdown = $txt_receive_qty . '_' . $bookingId . '_';
							$data_allocation = "(" . $allocation_id . "," . $id . ",1,1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $bookingId . "','" . $bookingNo . "',1,'" . $qnty_breakdown . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data preparing and updating
							|--------------------------------------------------------------------------
							|
							*/
							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id,mst_id,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,is_sales,inserted_by,insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $bookingId . "','" . $bookingNo . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
						}
					}
				} else {
					if ($is_sales_order == 1 && $is_auto_allocation == 1 && ($is_without_order == 135 || ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2))) {
						$allocation_mst_insert = 1;
						$allocation_dtls_insert = 1;
					} else if ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2) {
						$allocation_mst_insert = 1;
						$allocation_dtls_insert = 1;
					} else {
						/*
						|--------------------------------------------------------------------------
						| if receive basis yarn dyeing work order or Yarn Service Work Order then
						| booking no will be fabric bookin_no
						|--------------------------------------------------------------------------
						*/
						if (str_replace("'", "", $cbo_receive_basis) == 2 && (str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 44 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51)) {
							$expBookinNo = explode("-", $txt_wo_pi);
							if ($expBookinNo[1] == 'YDW' || $expBookinNo[1] == 'YSW') {
								$woDyingDtlsId = str_replace("'", "", $hdnYarnDyingDtlsId);

								$sqlFabricBooking = "SELECT a.is_sales AS IS_SALES, b.fab_booking_no AS FAB_BOOKING_NO FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE a.id = b.mst_id AND b.id in( " . $woDyingDtlsId . ") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
								$rsltFabricBooking = sql_select($sqlFabricBooking);
								foreach ($rsltFabricBooking as $row) {
									$txt_wo_pi = "'" . $row['FAB_BOOKING_NO'] . "'";
									$is_sales = $row['IS_SALES'];
								}
							}
						}

						$sqlAllocation = "SELECT a.id,a.qnty_break_down,b.id AS dtls_id, b.po_break_down_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $job_no . " AND a.item_id = " . $prodMSTID . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.booking_no = " . $txt_wo_pi . " AND b.status_active = 1 AND b.is_deleted = 0";

						//echo "10**"; die($sqlAllocation);
						$check_allocation_info = sql_select($sqlAllocation);
						//check_allocation_info
						if (!empty($check_allocation_info)) {
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data preparing for
							| $data_allocation_dtls
							|--------------------------------------------------------------------------
							|
							*/
							$field_allocation_dtls_upd = "po_break_down_id*qnty*updated_by*update_date";
							$qnty_breakdown = '';

							$prevAllocationDataArr = array();
							$data_allocation_dtls = array();
							foreach ($check_allocation_info as $row) {
								$allocation_id = $row[csf('id')];
								$po_id = $row[csf('po_break_down_id')];
								$prevAllocationDataArr[$po_id]['dtls_id'] = $row[csf('dtls_id')];
								$prevAllocationDataArr[$po_id]['qty'] = $row[csf('qnty')];
								$prevAllocationDataArr[$po_id]['po_id'] = $po_id;
							}

							//hdnReceiveString
							$expRcvString = explode(',', $hdnReceiveString);
							$rcvAllocationDataArr = array();
							foreach ($expRcvString as $expRcv) {
								$rcvData = explode('_', $expRcv);
								$rcv_po_id = $rcvData[0];

								$rcvAllocationDataArr[$rcv_po_id]['qty'] = $rcvData[1];
								$rcvAllocationDataArr[$rcv_po_id]['po_id'] = $rcv_po_id;
							}

							$combinedPrcvNrcvData = array_merge($prevAllocationDataArr, $rcvAllocationDataArr);

							$updateDtlsIdArr = array();
							foreach ($combinedPrcvNrcvData as $key => $val) {
								$poId = $val['po_id'];
								$prev_po_id = $prevAllocationDataArr[$poId]['po_id'];
								$dtlsId = $prevAllocationDataArr[$poId]['dtls_id'];
								//echo "10**";
								//var_dump($poId)."==".var_dump($poId);
								if ($poId == $prev_po_id) {
									$po_wise_data[$poId]['qty'] += $val['qty'];
									$po_wise_data[$poId]['dtlsId'] = ($dtlsId > 0) ? $dtlsId : 0;
								} else {
									$po_wise_data[$poId]['qty'] += $val['qty'];
									$po_wise_data[$poId]['dtlsId'] = ($dtlsId > 0) ? $dtlsId : 0;
								}
							}

							$allocation_qnty = 0;
							$data_allocation_dtls_data = '';
							$qnty_breakdown_str = $all_po_ids = "";

							foreach ($po_wise_data as $orderId => $val) {
								$qnty = $val['qty'];

								if ($val['dtlsId'] > 0) {
									$dtlsId = $val['dtlsId'];
									$updateDtlsIdArr[] = $dtlsId;
									$data_allocation_dtls[$dtlsId] = explode("*", ("'" . $orderId . "'*" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
								} else {
									$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
									$field_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";

									if ($data_allocation_dtls_data != '') {
										$data_allocation_dtls_data .= ',';
									}

									$data_allocation_dtls_data .= "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",'" . $orderId . "'," . $txt_wo_pi . ",1," . $txt_receive_date . "," . $prodMSTID . ",'" . $qnty . "',1," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								}

								//for inv_material_allocation_mst
								if ($qnty_breakdown_str != '') {
									$qnty_breakdown_str .= ',';
									$all_po_ids .= ',';
								}

								$all_po_ids .= $orderId;
								$qnty_breakdown_str .= $qnty . '_' . $orderId . '_' . str_replace("'", "", $job_no);
								$allocation_qnty += $qnty;
							}

							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_mst
							| data preparing and updating
							|--------------------------------------------------------------------------
							|
							*/
							$field_allocation = "allocation_date*qnty*po_break_down_id*qnty_break_down*updated_by*update_date";
							$data_allocation = "" . $txt_receive_date . "*" . $allocation_qnty . "*'" . $all_po_ids . "'*'" . $qnty_breakdown_str . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

							$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);

							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data updating
							|--------------------------------------------------------------------------
							|
							*/
							if ($allocation_mst_insert) {
								$allocation_dtls_insert = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls_upd, $data_allocation_dtls, $updateDtlsIdArr));
							}

							if ($data_allocation_dtls_data != "") {
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls_data, 0);
							}
						} else {
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data preparing and inserting
							|--------------------------------------------------------------------------
							|
							*/
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";
							$data_allocation_dtls = '';
							$qnty_breakdown = '';
							$po_breakdown_id = '';
							$expRcvString = explode(',', $hdnReceiveString);
							foreach ($expRcvString as $expRcv) {
								$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
								$rcvData = explode('_', $expRcv);
								if ($data_allocation_dtls != '') {
									$data_allocation_dtls .= ',';
									$qnty_breakdown .= ',';
									$po_breakdown_id .= ',';
								}
								//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
								$po_breakdown_id .= $rcvData[0];
								$qnty_breakdown .= $rcvData[1] . '_' . $rcvData[0] . '_' . str_replace("'", "", $job_no);
								$data_allocation_dtls .= "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",'" . $rcvData[0] . "'," . $txt_wo_pi . ",1," . $txt_receive_date . "," . $prodMSTID . ",'" . $rcvData[1] . "',1," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							}

							$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);

							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_mst
							| data preparing and inserting
							|--------------------------------------------------------------------------
							|
							*/
							$field_allocation = "id,mst_id,entry_form,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,qnty_break_down,is_sales,inserted_by,insert_date";
							$data_allocation = "(" . $allocation_id . "," . $id . ",1," . $job_no . ",'" . $po_breakdown_id . "'," . $txt_wo_pi . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $qnty_breakdown . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
						}
					}
				}
			} else {
				$allocation_mst_insert = 1;
				$allocation_dtls_insert = 1;
			}
		} else {
			$allocation_mst_insert = 1;
			$allocation_dtls_insert = 1;
		}


		//echo "10**insert into inv_material_allocation_dtls (".$field_allocation_dtls.") values ".$data_allocation_dtls;die;
		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data preparing for
		| $data_proportionate
		|--------------------------------------------------------------------------
		|
		*/
		$data_proportionate = '';
		if ($hdnReceiveString != '') {
			$field_proportionate = "id, trans_id, trans_type, entry_form, po_breakdown_id, prod_id, grey_prod_id, color_id, quantity, inserted_by, insert_date";
			$expRcvString = explode(',', $hdnReceiveString);
			foreach ($expRcvString as $expRcv) {
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$rcvData = explode('_', $expRcv);
				if ($data_proportionate != '') {
					$data_proportionate .= ',';
				}

				$grey_prod_id = implode(",", array_unique(explode("**", $rcvData[3])));

				//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
				$data_proportionate .= "(" . $id_prop . "," . $dtlsid . ",1,1," . $rcvData[0] . "," . $prodMSTID . ", '" . $grey_prod_id . "', " . $cbo_color . "," . $rcvData[1] . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
		}
		//$txt_wo_pi;
		/*
		|--------------------------------------------------------------------------
		| inv_receive_master
		| data inserting/updating
		|--------------------------------------------------------------------------
		|
		*/
		if (str_replace("'", "", $txt_mrr_no) != "") {
			$rID = sql_update("inv_receive_master", $field_array, $data_array, "id", $id, 0);
		} else {
			$rID = sql_insert("inv_receive_master", $field_array, $data_array, 0);
		}

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/

		/*
		oci_rollback($con);
		echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans; die;
		disconnect($con);
		die;*/

		$dtlsrID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$prodUpdate = sql_update("product_details_master", $field_array_prod_update, $data_array_prod_update, "id", $prodMSTID, 0);

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		$rslt_proportionate = 1;
		if ($data_proportionate != '') {
			$rslt_proportionate = sql_insert("order_wise_pro_details", $field_proportionate, $data_proportionate, 0);
		}
		$storeRID = true;
		if ($variable_store_wise_rate == 1) {
			if ($store_up_id > 0) {
				$storeRID = sql_update("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
			} else {
				$storeRID = sql_insert("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, 1);
			}
		}

		//echo "10**".$rID ."&&". $dtlsrID ."&&". $prodUpdate ."&&". $insertR  ."&&". $rslt_proportionate ."&&". $allocation_mst_insert ."&&". $allocation_dtls_insert."&&". $storeRID;oci_rollback($con); disconnect($con); die();

		if ($db_type == 0) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR && $rslt_proportionate && $allocation_mst_insert && $allocation_dtls_insert && $storeRID) {
				mysql_query("COMMIT");
				echo "0**" . $new_recv_number[0] . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_recv_number[0] . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR && $rslt_proportionate && $allocation_mst_insert && $allocation_dtls_insert && $storeRID) {
				$allocation_log = manage_allocation_transaction_log($log_data);

				if ($allocation_log) {
					oci_commit($con);
					echo "0**" . $new_recv_number[0] . "**" . $id;
				} else {
					oci_rollback($con);
					echo "10**" . $new_recv_number[0] . "**" . $id;
				}
			} else {
				oci_rollback($con);
				echo "10**" . $new_recv_number[0] . "**" . $id;
			}
		}

		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	} else if ($operation == 1) //Update Here
	{
		$cbo_receive_purpose = str_replace("'", "", $cbo_receive_purpose);
		$prev_data = sql_select("select a.id,a.currency_id,count(b.id) as dtls_row from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.recv_number=$txt_mrr_no and b.id!=$update_id and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 group by a.id, a.currency_id");
		if ($prev_data[0][csf('dtls_row')] > 0) {
			if (str_replace("'", "", $cbo_currency) != $prev_data[0][csf('currency_id')]) {
				echo "30**Multiple Currency Not Allow In Same MRR Number";
				die;
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
			}
		}

		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if ($variable_set_invent == 1) {
			$challan_no = str_replace("'", "", $txt_challan_no);
			if ($challan_no != "") {
				$variable_set_invent = return_field_value("a.id as id", " inv_gate_in_mst a,  inv_gate_in_dtl b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
				if (empty($variable_set_invent)) {
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					//check_table_status($_SESSION['menu_id'], 0);
					disconnect($con);
					die;
				}
			}
		}

		$challan_no = str_replace("'", "", $txt_challan_no);
		if ($challan_no != "") {
			if ($variale_challan_dup == 2) {
				$supplier_id = str_replace("'", "", $cbo_supplier);
				if ($recieve_id) $mrr_cond = " and id<>$recieve_id";
				$duplicate_chalan = is_duplicate_field("id", "inv_receive_master ", "status_active=1 and is_deleted=0 and supplier_id=$supplier_id and challan_no='$challan_no' $mrr_cond and entry_form=1");
				if ($duplicate_chalan == 1) {
					echo "30**Duplicate Challan No. Not Allow.";
					disconnect($con);
					die;
				}
			}
		}


		$mrr_issue_check = return_field_value("sum(issue_qnty) issue_qnty", "inv_mrr_wise_issue_details", "recv_trans_id=$update_id and status_active=1 and	is_deleted=0", "issue_qnty");
		if (str_replace("'", "", $txt_receive_qty) < $mrr_issue_check) {
			echo "30**Receive quantity can not be less than Issue quantity";
			disconnect($con);
			die;
		}
		//---------------End Check Receive control on Gate Entry---------------------------//

		//-------------Yarn Service Booking. Receive can not be greater than service booking----//
		if ((str_replace("'", "", $cbo_receive_basis) == 2)) {
			$cbo_receive_purpose = str_replace("'", "", $cbo_receive_purpose);
			$cbo_color = str_replace("'", "", $cbo_color);

			$sql_exis_servc_recvQnty = sql_select("select listagg(a.recv_number_prefix_num, ',') within group (order by a.id) as rcv_no,a.booking_id,sum(b.cons_quantity) as recv_quantity,sum(b.grey_quantity) as grey_quantity from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and b.id != $update_id and a.company_id=$cbo_company_id and a.booking_id=$txt_wo_pi_id and b. transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.receive_purpose=$cbo_receive_purpose and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.yarn_type=$cbo_yarn_type and c.color=$cbo_color and c.yarn_count_id=$cbo_yarn_count  and c.yarn_comp_type1st=$cbocomposition1 and c.yarn_comp_type2nd=$cbocomposition2 and yarn_comp_percent1st=$percentage1 group by a.booking_id");

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {
				$sql_wo_yarn_qnty = sql_select("select a.id,sum(b.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.id=$txt_wo_pi_id and a.entry_form in(41,42,94,114,125,135) and b.id=$hdnYarnDyingDtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id");
			} else {
				$sql_wo_yarn_qnty = sql_select("select a.id,sum(b.supplier_order_quantity) as yarn_wo_qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_id and a.id=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id");
			}

			$woYarnQnty = $sql_wo_yarn_qnty[0][csf('yarn_wo_qty')];

			//issue check only for service
			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 44 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {

				if ($cbo_receive_purpose == 15 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {
					$sql_issue_qnty = sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(15,50,51) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				} else {
					if ($cbo_receive_purpose == 2) {
						$color_cond = "and b.dyeing_color_id=$cbo_color";
					}

					$sql_issue_qnty = sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(2,12,38,46) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id and c.yarn_type=$cbo_yarn_type and c.yarn_count_id=$cbo_yarn_count  and c.yarn_comp_type1st=$cbocomposition1 and yarn_comp_percent1st=$percentage1 $color_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}

				$issueQnty_service = $sql_issue_qnty[0][csf('cons_quantity')];
			}

			// ===========  Deduction all kind of received return qunatity start ========//
			if ($txt_wo_pi_id != "") {
				$sql_issue_transec = sql_select("select b.mst_id,b.id,c.issue_trans_id,c.entry_form,d.id
				from  inv_receive_master a , inv_transaction b, inv_mrr_wise_issue_details c
				left join inv_transaction d on c.issue_trans_id=d.id and d.status_active=1 and d.transaction_type=2
				where a.id=b.mst_id and  b.id=c.recv_trans_id
				and a.booking_id = $txt_wo_pi_id and b.transaction_type=1 and a.entry_form=1 and c.entry_form=3
				and a.status_active=1 and b.status_active=1 and c.status_active=1
				and a.item_category=1 and b.transaction_type=1");

				foreach ($sql_issue_transec as $trn_issue_row) {
					$issue_trans_id .= $trn_issue_row[csf('issue_trans_id')] . ",";
				}

				$issue_trans_id = chop($issue_trans_id, " , ");
			}

			if ($issue_trans_id != "") {
				$issue_id_from_issue_rtn = sql_select("select a.id from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.id in($issue_trans_id) and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				foreach ($issue_id_from_issue_rtn as $issue_row) {
					$issue_id .= $issue_row[csf('id')] . ",";
				}

				$issue_ids = chop($issue_id, " , ");
			}

			if ($issue_ids != "") {
				$sql_issue_rtn_rcv = sql_select("select a.id as received_id  from inv_receive_master a,inv_transaction b where a.id=b.mst_id and a.issue_id in($issue_ids) and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

				foreach ($sql_issue_rtn_rcv as $iss_rcv_row) {
					$total_rcv_id[] = $iss_rcv_row[csf('received_id')];
				}
			}

			$mrr_rcv_id_sql = sql_select("select d.id as received_id from inv_receive_master d, inv_transaction e where d.id = e.mst_id and e.transaction_type = 1 and e.item_category = 1 and e.receive_basis = 2 and d.receive_purpose = 2 and d.booking_id = $txt_wo_pi_id and e.job_no = $job_no and d.entry_form = 1 and d.is_deleted = 0 and d.status_active = 1 and d.company_id = $cbo_company_id group by d.id");

			foreach ($mrr_rcv_id_sql as $rcv_row) {
				$total_rcv_id[] = $rcv_row[csf('received_id')];
			}

			$total_received_ids = implode(",", array_unique($total_rcv_id));

			if ($total_received_ids != "") {
				$sql_return_recev = sql_select("select sum(a.cons_quantity) as rec_ret_qnty from inv_transaction a, inv_issue_master b, product_details_master c where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3 and a.company_id = $cbo_company_id and b.is_deleted = 0 and b.status_active = 1 and c.color = $cbo_color $lot_cond and b.received_id in ($total_received_ids)");
				$return_receiveQnty = $sql_return_recev[0][csf('rec_ret_qnty')];
			}
			// =========== End Deduction all kind of received return qunatity  ========//	

			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
			$total_recvQnty = $sql_exis_servc_recvQnty[0][csf('recv_quantity')] + $txt_receive_qty;
			$over_receive_limit = (float)$over_receive_limit;
			$over_receive_limit_qnty = ($over_receive_limit > 0) ? ($over_receive_limit / 100) * $woYarnQnty : 0;

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 44 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) {
				$issud_msg = "Issue";
				$wo_qnty = $issueQnty_service;
				$allow_total_val = $issueQnty_service + $over_receive_limit_qnty;
				$overRecvLimitMsg = "Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			} else {
				$issud_msg = "";
				$wo_qnty = $woYarnQnty;
				$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;
				$overRecvLimitMsg = "Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			}

			$allow_total_val = number_format($allow_total_val, 2, '.', '');
			$over_receive_limit_qnty = number_format($over_receive_limit_qnty, 2, '.', '');
			$balance = number_format(($total_recvQnty - $return_receiveQnty), 2, '.', '');

			// Grey used qnty validation depends on yarn service process loss control variable setting		
			if ($vs_process_control_validation == 0 && ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38)) {
				$txt_grey_qty = str_replace("'", "", $txt_grey_qty) * 1;
				$txt_receive_qty = str_replace("'", "", $txt_receive_qty) * 1;

				$hdn_grey_qty = str_replace("'", "", $hdn_grey_qty);

				$total_greyQnty = (($sql_exis_servc_recvQnty[0][csf('grey_quantity')] - $return_receiveQnty) + $txt_grey_qty);
				//-$return_receiveQnty
				$grey_quantity_balance = number_format(($total_greyQnty), 2, '.', '');

				if ($txt_grey_qty < $txt_receive_qty) {
					echo "40**Grey quantity can not be less than received quantity";
					die;
				}

				if (str_replace("'", "", $hdn_entry_form) == 135) {
					$zs = number_format(($allow_total_val), 2, '.', '');
				} else {
					$zs = number_format(($allow_total_val - $over_receive_limit_qnty), 2, '.', '');
				}
				//echo "40**".$zs ."<". $grey_quantity_balance; die();

				if ($zs < $grey_quantity_balance) {
					$thismrrGreyQty = str_replace("'", "", $hdn_grey_qty);
					$previousGreyQty = $sql_exis_servc_recvQnty[0][csf('grey_quantity')];
					$rcvNumbers = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
					$actualGreyQty = (($previousGreyQty - $return_receiveQnty) + $thismrrGreyQty);
					//$over_grey_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
					$allowedGreyBalance = number_format(($wo_qnty - $actualGreyQty), 2, '.', '');

					echo "40**Grey quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty\nReceived No=$rcvNumbers\nGrey Qty = $actualGreyQty\nBalance = $allowedGreyBalance";
					die;
				}
			}

			if ($allow_total_val < $balance) {
				$thismrrRcv = str_replace("'", "", $hdn_receive_qty);
				$receivedNumber = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
				$previousRcv = $sql_exis_servc_recvQnty[0][csf('recv_quantity')];
				$actualRcv = (($previousRcv + $thismrrRcv) - $return_receiveQnty);
				$over_msg = ($over_receive_limit > 0) ? "\nAllowed Quantity = $allow_total_val" : "";
				$allowedBalance = number_format(($wo_qnty - $actualRcv), 2, '.', '');
				echo "40**Recv. quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty \nReceived No=$receivedNumber\nReceived Qty = $actualRcv\n$overRecvLimitMsg $over_msg\nBalance = $allowedBalance";
				die;
			}
		}
		//-------------End Yarn Service Booking. Receive can not be greater than service booking ----//

		//previous product stock adjust here--------------------------//
		//product master table UPDATE here START ---------------------//
		if ((str_replace("'", "", $cbo_receive_basis) == 1)  && $cbo_receive_purpose == 16) {
			$hdn_receive_qty = str_replace("'", "", $hdn_receive_qty);
			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
			$txt_rate = str_replace("'", "", $txt_rate);

			if ($txt_wo_pi_id != "") {
				$total_rcv_value = return_field_value("sum(order_qnty*order_rate) as rcv_value", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and a.booking_id=$txt_wo_pi_id and b.pi_wo_batch_no=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "rcv_value");

				$current_acceptance_value = return_field_value("a.current_acceptance_value", "com_import_invoice_dtls a", "a.pi_id=$txt_wo_pi_id and a.status_active=1 and  a.is_deleted=0", "current_acceptance_value");
			}

			$previous_amount = 	($hdn_receive_qty * $txt_rate);
			$current_text_amount = ($txt_receive_qty * $txt_rate);

			$pi_against_current_rcv = ($total_rcv_value - $previous_amount) + $current_text_amount;
			$reduce_value = ($total_rcv_value - $current_acceptance_value);

			//echo "40**ttt".$total_rcv_value."==".$txt_amount."==".$pi_against_current_rcv; die();
			if ($pi_against_current_rcv < $current_acceptance_value) {
				$current_not_allowed_value = ($current_acceptance_value - $current_text_amount);

				echo "40**Total Payable value=  $total_rcv_value\n\nTotal Accp.value= $current_acceptance_value\n\nAllowed to reduce value=$reduce_value\n\nSo current reduce value $current_not_allowed_value is not allowed";
				die;
			}
		}

		$sql = sql_select("select a.prod_id, a.cons_quantity, a.cons_rate, a.cons_amount, a.balance_qnty, a.balance_amount, a.store_amount, b.avg_rate_per_unit, b.current_stock, b.stock_value, b.allocated_qnty, b.available_qnty, c.receive_purpose, c.receive_basis, b.dyed_type 
		from inv_transaction a, product_details_master b, inv_receive_master c 
		where a.id=$update_id and a.prod_id=b.id and a.mst_id=c.id and c.entry_form=1 and a.status_active=1 and b.status_active=1 and c.status_active=1");

		$before_prod_id = $before_receive_qnty = $before_rate = $beforeAmount = $beforeBalanceQnty = $beforeBalanceAmount = $beforeStoreAmount = 0;
		$before_brand = "";
		$beforeStock = $beforeStockValue = $beforeAvgRate = $before_allocated_qnty = $before_available_qnty = $adj_allocated_qnty = $adj_beforeAvailableQnty = 0;
		foreach ($sql as $row) {
			$before_prod_id = $row[csf("prod_id")];
			$before_receive_qnty = $row[csf("cons_quantity")]; //stock qnty
			$before_rate = $row[csf("cons_rate")];
			$beforeAmount = $row[csf("cons_amount")]; //stock value
			$beforeBalanceQnty = $row[csf("balance_qnty")];
			$beforeBalanceAmount = $row[csf("balance_amount")];
			$beforeStoreAmount = $row[csf("store_amount")];

			$before_brand = $row[csf("brand")];
			$beforeStock = $row[csf("current_stock")];
			$beforeStockValue = $row[csf("stock_value")];
			$beforeAvgRate = $row[csf("avg_rate_per_unit")];
			$before_available_qnty = $row[csf("available_qnty")];
			$before_allocated_qnty = $row[csf("allocated_qnty")];
			$before_receive_purpose = $row[csf("receive_purpose")];
			$before_receive_basis = $row[csf("receive_basis")];
			$dyed_type = $row[csf("dyed_type")];
		}

		if ($dyed_type != 1 && (str_replace("'", "", $txt_receive_qty) < $before_receive_qnty)) {
			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
			$before_allocated_qnty = (int)$before_allocated_qnty;
			$allow_reduce_rcv = $beforeStock - $row[csf("cons_quantity")] + $txt_receive_qty;

			if (($beforeStock - $row[csf("cons_quantity")] + $txt_receive_qty) < (int)$before_allocated_qnty) {
				//echo "30**".$beforeStock."-".$row[csf("cons_quantity")]."-".$txt_receive_qty."<".(int)$before_allocated_qnty; die;
				echo "30**Receive quantity can not be less than Allocation quantity.\nAllocation Quantity=" . $before_allocated_qnty;
				disconnect($con);
				die;
			}
		}

		//stock value minus here---------------------------//
		$adj_beforeStock = $beforeStock - $before_receive_qnty;
		$adj_beforeStockValue = $beforeStockValue - $beforeAmount;
		if ($adj_beforeStock == 0) {
			$adj_beforeAvgRate = 0;
		} else {
			$adj_beforeAvgRate = number_format(($adj_beforeStockValue / $adj_beforeStock), $dec_place[3], '.', '');
		}

		$is_without_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "entry_form");
		$is_with_order_yarn_service_work_order = return_field_value("booking_without_order", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "booking_without_order");

		if ($variable_set_allocation == 1) {
			// update dyied yarn allocation start
			if (($before_receive_purpose == 2 || $before_receive_purpose == 12 || $before_receive_purpose == 15 || $before_receive_purpose == 38 || $before_receive_purpose == 44 || $before_receive_purpose == 46 || $before_receive_purpose == 50 || $before_receive_purpose == 51) && $before_receive_basis == 2) {
				if ($before_receive_purpose == 2 && ($is_without_order == 42 || $is_without_order == 114)) {
					if ($variable_set_smn_allocation == 1) {
						$adj_allocated_qnty = $before_allocated_qnty - $before_receive_qnty;
						$adj_beforeAvailableQnty = $before_available_qnty;
					} else {
						$adj_allocated_qnty = $before_allocated_qnty;
						$adj_beforeAvailableQnty = $before_available_qnty - $before_receive_qnty;
					}
				} else {
					if ($is_sales_order == 1 && $is_auto_allocation == 1 && ($is_without_order == 135 || ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2))) {
						$adj_allocated_qnty = $before_allocated_qnty;
						$adj_beforeAvailableQnty = $before_available_qnty - $before_receive_qnty;
					} else {
						if ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2) {
							$adj_allocated_qnty = $before_allocated_qnty;
							$adj_beforeAvailableQnty = $before_available_qnty - $before_receive_qnty;
						} else {
							$adj_allocated_qnty = $before_allocated_qnty - $before_receive_qnty;
							$adj_beforeAvailableQnty = $before_available_qnty;
						}
					}
				}
			} else {
				$adj_allocated_qnty = $before_allocated_qnty;
				$adj_beforeAvailableQnty = $before_available_qnty - $before_receive_qnty;
			}
		}
		//product master table UPDATE here END   ---------------------//
		//----------------- END PREVIOUS STOCK ADJUST-----------------//

		//---------------Check Brand---------------------------//
		if (str_replace("'", "", $txt_brand) != "") {
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

			if (str_replace("'", "", trim($txt_brand)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_brand)), $new_array_brand)) {
					$brand_id = return_id(str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name", "1");
					$new_array_brand[$brand_id] = str_replace("'", "", trim($txt_brand));
				} else $brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
			} else $brand_id = 0;

			$txt_brand = $brand_id;
		}
		//----------------Check Brand END---------------------//
		//---------------Check Product ID --------------------------//
		$insertR = true;
		$rtnString = return_product_id($cbo_yarn_count, $cbocomposition1, $cbocomposition2, $percentage1, $percentage2, $cbo_yarn_type, $color_id, $txt_yarn_lot, $txt_prod_code, $cbo_company_id, $cbo_supplier, $cbo_store_name, $cbo_uom, $yarn_type, $composition, $cbo_receive_purpose, $hdnPayMode);
		$expString = explode("***", $rtnString);
		if ($expString[0] == true) {
			$prodMSTID = $expString[1];
		} else {
			if ($adj_beforeStock < 0) {
				echo "30** Stock cannot be less than zero.";
				disconnect($con);
				die;
			}

			$field_array_prod = $expString[1];
			$data_array_prod = $expString[2];
			$prodMSTID = $expString[3];
		}

		if ($before_prod_id != $prodMSTID) {
			if ((int)$before_allocated_qnty > 0) {
				echo "13**Lot can not be changed.\nAllocation found. Allocation quantity=$before_allocated_qnty";
				oci_rollback($con);
				die;
			}

			if ($db_type == 0) {

				$check_requisition = sql_select("select group_concat(requisition_no) as requisition_no from ppl_yarn_requisition_entry where prod_id=$before_prod_id and status_active=1 and is_deleted=0");
				if (!empty($check_requisition) && $check_requisition[0][csf('requisition_no')] != "") {
					echo "13**Lot can not be changed.Requisition found(" . $check_requisition[0][csf('requisition_no')] . ").";
					disconnect($con);
					mysql_query("ROLLBACK");
					die;
				}
			} else {
				$check_requisition = sql_select("select listagg(requisition_no, ',') within group (order by requisition_no) as requisition_no from ppl_yarn_requisition_entry where prod_id=$before_prod_id and status_active=1 and is_deleted=0");
				if (!empty($check_requisition) && $check_requisition[0][csf('requisition_no')] != "") {
					echo "13**Lot can not be changed.Requisition found(" . $check_requisition[0][csf('requisition_no')] . ").";
					disconnect($con);
					oci_rollback($con);
					die;
				}
			}
		} else {

			if ($db_type == 0) {
				$check_requisition = sql_select("select group_concat(requisition_no) as requisition_no,sum(yarn_qnty) as total_requisition_qnty from ppl_yarn_requisition_entry where prod_id=$before_prod_id and status_active=1 and is_deleted=0");

				$yarnRcvQnty = number_format(str_replace("'", "", $txt_receive_qty), 2, ".", "");
				$total_requisition_qnty = numfmt_format($check_requisition[0][csf('total_requisition_qnty')], 2, ".", "");

				if (!empty($check_requisition) && $check_requisition[0][csf('requisition_no')] != "" && ($yarnRcvQnty < $total_requisition_qnty)) {
					echo "13**Requisition found\nRequisition no = " . $check_requisition[0][csf('requisition_no')] . "\nRequisition quantity=$total_requisition_qnty";
					disconnect($con);
					mysql_query("ROLLBACK");
					die;
				}
			} else {
				$check_requisition = sql_select("select listagg(requisition_no, ',') within group (order by requisition_no) as requisition_no,sum(yarn_qnty) as total_requisition_qnty from ppl_yarn_requisition_entry where prod_id=$before_prod_id and status_active=1 and is_deleted=0");

				$yarnRcvQnty = number_format(str_replace("'", "", $txt_receive_qty), 2, ".", "");
				$total_requisition_qnty = number_format($check_requisition[0][csf('total_requisition_qnty')], 2, ".", "");

				if (!empty($check_requisition) && $check_requisition[0][csf('requisition_no')] != "" && ($yarnRcvQnty < $total_requisition_qnty)) {
					echo "13**Requisition found\nRequisition no = " . $check_requisition[0][csf('requisition_no')] . "\nRequisition quantity=$total_requisition_qnty";
					disconnect($con);
					oci_rollback($con);
					die;
				}
			}
		}



		//current product stock-------------------------//
		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty,allocated_qnty, dyed_type from product_details_master where id=$prodMSTID");
		$presentStock = $presentStockValue = $presentAvgRate = $allocated_qnty = $available_qnty = 0;
		$product_name_details = "";
		foreach ($sql as $result) {
			$presentStock = $result[csf("current_stock")];
			$presentStockValue = $result[csf("stock_value")];
			$presentAvgRate = $result[csf("avg_rate_per_unit")];
			$product_name_details = $result[csf("product_name_details")];
			$available_qnty = $result[csf("available_qnty")];
			$allocated_qnty = $result[csf("allocated_qnty")];
			$log_dyed_type = $result[csf("dyed_type")];
		}
		if ($allocated_qnty == "") $allocated_qnty = 0;
		if ($available_qnty == "") $available_qnty = 0;
		//----------------Check Product ID END---------------------//

		//yarn master table UPDATE here START----------------------//
		$field_array_update = "item_category*receive_date*challan_date*challan_no*store_id*loan_party*yarn_issue_challan_no*issue_id*lc_no*remarks*gate_entry_no*gate_entry_date*boe_mushak_challan_no*boe_mushak_challan_date*updated_by*update_date";
		$data_array_update = "1*" . $txt_receive_date . "*" . $txt_challan_date . "*" . $txt_challan_no . "*" . $cbo_store_name . "*" . $cbo_party . "*" . $txt_issue_challan_no . "*" . $txt_issue_id . "*" . $hidden_lc_id . "*" . $txt_mst_remarks . "*" . $txt_gate_entry_no . "*" . $txt_gate_entry_date . "*" . $txt_boe_mushak_challan_no . "*" . $txt_boe_mushak_challan_date . "*'" . $user_id . "'*'" . $pc_date_time . "'";

		// yarn details table UPDATE here START-----------------------------------//
		$rate = $txt_rate;
		if ($txt_ile == '') $txt_ile = 0;
		$hdn_receive_qty = str_replace("'", "", $hdn_receive_qty);
		$ile = ($txt_ile / $rate) * 100; // ile cost to ile
		$ile = (is_nan($ile)) ? 0 : $ile;

		$ile_cost = str_replace("'", "", $txt_ile); //ile cost = (ile/100)*rate
		$order_amount = (($txt_receive_qty * $rate) + $ile_cost);

		$exchange_rate = $txt_exchange_rate;
		$conversion_factor = 1; // yarn always KG
		$domestic_rate = return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor);
		$cons_rate = number_format($domestic_rate, $dec_place[3], ".", ""); //number_format($rate*$exchange_rate,$dec_place[3],".","");

		$con_amount = $cons_rate * $txt_receive_qty;
		$con_ile = $ile;
		$con_ile_cost = ($ile / 100) * ($rate * $exchange_rate);
		$con_ile_cost = (is_nan($con_ile_cost)) ? 0 : $con_ile_cost;

		$adjBalanceQnty = $beforeBalanceQnty - $before_receive_qnty + $txt_receive_qty;
		$adjBalanceAmount = $beforeBalanceAmount - $beforeAmount + $con_amount;

		$field_array_trans = "receive_basis*pi_wo_batch_no*company_id*supplier_id*prod_id*origin_prod_id*product_code*item_category*transaction_type*transaction_date*store_id*brand_id*order_uom*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_uom*cons_quantity*cons_rate*cons_avg_rate*dye_charge*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*no_of_bags*cone_per_bag*no_loose_cone*weight_per_bag*weight_per_cone*room*rack*self*bin_box*floor_id*remarks*job_no*buyer_id*updated_by*update_date*pi_wo_req_dtls_id*grey_quantity*dyeing_color_id*store_rate*store_amount";
		$data_array_trans = "" . $cbo_receive_basis . "*" . $txt_wo_pi_id . "*" . $cbo_company_id . "*" . $cbo_supplier . "*" . $prodMSTID . "*" . $prodMSTID . "*" . $txt_prod_code . "*1*1*" . $txt_receive_date . "*" . $cbo_store_name . "*" . $txt_brand . "*" . $cbo_uom . "*" . $txt_receive_qty . "*" . number_format($txt_rate, 10, ".", "") . "*" . $ile . "*" . $ile_cost . "*" . number_format($order_amount, 8, ".", "") . "*" . $cbo_uom . "*" . $txt_receive_qty . "*" . number_format($cons_rate, 10, ".", "") . "*" . $txt_avg_rate . "*" . $txt_dyeing_charge . "*" . $con_ile . "*" . $con_ile_cost . "*" . number_format($con_amount, 8, ".", "") . "*" . $adjBalanceQnty . "*" . $adjBalanceAmount . "*" . $txt_no_bag . "*" . $txt_cone_per_bag . "*" . $txt_no_loose_cone . "*" . $txt_weight_per_bag . "*" . $txt_weight_per_cone . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_floor . "*" . $txt_remarks . "*" . $job_no . "*'" . $cbo_buyer_name . "'*'" . $user_id . "'*'" . $pc_date_time . "'*" . $txt_wo_pi_dtls_id . "*" . $txt_grey_qty . "*" . $cbo_color  . "*" . number_format($cons_rate, 10, ".", "") . "*" . number_format($con_amount, 8, ".", "") . "";

		/*
		|--------------------------------------------------------------------------
		| if receive basis yarn dyeing work order or Yarn Service Work Order then
		| booking no will be fabric bookin_no 
		|--------------------------------------------------------------------------
		|
		*/
		$is_sales = 0;
		if (str_replace("'", "", $cbo_receive_basis) == 2 && (str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 15)) {
			$expBookinNo = explode("-", $txt_wo_pi);

			if ($expBookinNo[1] == 'YDW') {
				$sqlFabricBookin =  "SELECT a.is_sales,b.fab_booking_no AS BOOKING_NO FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE  a.id=b.mst_id and b.mst_id = " . $txt_wo_pi_id . " AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.is_sales,b.fab_booking_no";

				$rsltFabricBookin = sql_select($sqlFabricBookin);
				foreach ($rsltFabricBookin as $row) {
					$txt_wo_pi = "'" . $row['BOOKING_NO'] . "'";
					$is_sales = $row['IS_SALES'];
				}
			}
		}

		//for transaction log
		$log_ref_id = return_field_value('id', 'inv_receive_master', " status_active = 1 and recv_number = " . $txt_mrr_no, 'id');

		//product master table data UPDATE START
		$field_array = "brand*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$allocation_mst_insert = 1;
		$allocation_dtls_insert = 1;

		if ($before_prod_id == $prodMSTID) {
			$currentStock = $adj_beforeStock + $txt_receive_qty;
			//echo "30**".$txt_receive_qty."==".$hdn_receive_qty; die();
			if ($currentStock <= 0) {
				echo "30**Stock cannot be less than zero.";
				disconnect($con);
				die;
			}
			//echo "10**";
			$StockValue = $adj_beforeStockValue + ($domestic_rate * $txt_receive_qty);
			$avgRate = number_format($StockValue / $currentStock, $dec_place[3], '.', '') * 1;

			if ($variable_set_allocation == 1) {
				// update dyied yarn allocation start
				if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 44 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51) && (str_replace("'", "", $cbo_receive_basis) == 2)) {
					if (str_replace("'", "", $cbo_receive_purpose) == 2 && ($is_without_order == 42 || $is_without_order == 114)) {
						if ($variable_set_smn_allocation != 1) {
							$allocated_qnty = $adj_allocated_qnty;
							$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
						} else {
							$allocated_qnty = $adj_allocated_qnty + $txt_receive_qty;
							$available_qnty = $adj_beforeAvailableQnty;
							/*
							|--------------------------------------------------------------------------
							| for booking no
							|--------------------------------------------------------------------------
							|
							*/
							$sqlBookingNo = "SELECT a.is_sales, c.id, c.booking_no FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c WHERE a.id = b.mst_id AND b.booking_no = c.booking_no AND a.id = " . $txt_wo_pi_id . " AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form in (42,114)"; //AND b.product_id = ".$prodMSTID."
							$sqlBookingNo = sql_select($sqlBookingNo);
							$bookingId = '';
							$bookingNo = '';
							foreach ($sqlBookingNo as $row) {
								$bookingId = $row[csf('id')];
								$bookingNo = $row[csf('booking_no')];
								$is_sales = $row[csf('is_sales')];
							}

							$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.booking_no = '" . $bookingNo . "' AND a.item_id = " . $prodMSTID . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
							$check_allocation_info = sql_select($sqlAllocation);

							if (!empty($check_allocation_info)) {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								foreach ($check_allocation_info as $row) {
									$allocation_id = $row[csf('id')];
									$allocation_dtls_id = $row[csf('dtls_id')];
									$allocation_qnty = $row[csf('qnty')] + ($txt_receive_qty - $hdn_receive_qty);
									$qnty_breakdown = $allocation_qnty . '_' . $bookingId . '_';
								}

								$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
								$data_allocation = "" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
								$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", $allocation_id, 0);
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation_dtls = "qnty*updated_by*update_date";
								$data_allocation_dtls = "" . $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
								$allocation_dtls_insert = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", $allocation_dtls_id, 0);
							} else {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and inserting
								|--------------------------------------------------------------------------
								|
								*/
								$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
								$field_allocation = "id,mst_id,entry_form,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,booking_without_order,qnty_break_down,is_sales,inserted_by,insert_date";
								$qnty_breakdown = $txt_receive_qty . '_' . $bookingId . '_';
								$data_allocation = "(" . $allocation_id . "," . $recieve_id . ",1,1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $bookingId . "','" . $bookingNo . "',1,'" . $qnty_breakdown . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
								$field_allocation_dtls = "id,mst_id,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,is_sales,inserted_by,insert_date";
								$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $bookingId . ",'" . $bookingNo . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
					} else {
						if ($is_sales_order == 1 && $is_auto_allocation == 1 && ($is_without_order == 135 || ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2))) {
							$allocated_qnty = $adj_allocated_qnty;
							$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
						} else if ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2) {
							$allocated_qnty = $adj_allocated_qnty;
							$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
						} else {
							$allocated_qnty = $adj_allocated_qnty + $txt_receive_qty;
							$available_qnty = $adj_beforeAvailableQnty;

							$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.po_break_down_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $job_no . " AND a.item_id = " . $prodMSTID . " AND a.is_dyied_yarn=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";

							//echo "10**$sqlAllocation"; die();
							$check_allocation_info = sql_select($sqlAllocation);

							if (!empty($check_allocation_info)) {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing for
								| $data_allocation_dtls
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation_dtls = "po_break_down_id*qnty*updated_by*update_date";
								$qnty_breakdown = '';
								$po_breakdown_id = '';
								$allocation_qnty = 0;
								$allocationDataArr = array();
								$updateDtlsIdArr = array();
								$data_allocation_dtls = array();
								foreach ($check_allocation_info as $row) {
									$allocation_id = $row[csf('id')];
									$allocationDataArr[$row[csf('po_break_down_id')]]['dtls_id'] = $row[csf('dtls_id')];
									$allocationDataArr[$row[csf('po_break_down_id')]]['qnty'] = $row[csf('qnty')];
									$allocationDataArr[$row[csf('po_break_down_id')]]['po_id'] = $row[csf('po_break_down_id')];
								}

								$expOldRcvString = explode(',', $hdnOldReceiveString);
								$powise_old_rcv_alc = array();
								foreach ($expOldRcvString as $expOldRcv) {
									$oldRcvData = explode('_', $expOldRcv);
									$powise_old_rcv_alc[$oldRcvData[0]] = $oldRcvData[1];
								}

								//hdnReceiveString
								$expRcvString = explode(',', $hdnReceiveString);
								$rcv_po_id_arr = array();
								foreach ($expRcvString as $expRcv) {
									//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
									$rcvData = explode('_', $expRcv);
									$qnty = $rcvData[1] + ($allocationDataArr[$rcvData[0]]['qnty'] -  $rcvData[2]);

									$dtlsId = $allocationDataArr[$rcvData[0]]['dtls_id'];
									if ($dtlsId > 0) {
										$updateDtlsIdArr[] = $dtlsId;
									}

									$data_allocation_dtls[$dtlsId] = explode("*", ("" . $rcvData[0] . "*" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));

									//for inv_material_allocation_mst
									if ($qnty_breakdown != '') {
										$qnty_breakdown .= ',';
										$po_breakdown_id .= ',';
									}
									$po_breakdown_id .= $rcvData[0];
									$qnty_breakdown .= $qnty . '_' . $rcvData[0] . '_' . str_replace("'", "", $job_no);
									$allocation_qnty += $qnty;

									$rcv_po_id_arr[] = $rcvData[0];
								}

								//echo "10**$allocation_qnty"; die();
								foreach ($allocationDataArr as $prev_alc_po => $prev_alc_po_data) {
									if (!in_array($prev_alc_po, $rcv_po_id_arr)) {
										$qnty_breakdown .= "," . $allocationDataArr[$prev_alc_po]['qnty'] . '_' . $prev_alc_po . '_' . str_replace("'", "", $job_no);

										$po_breakdown_id .= ',' . $prev_alc_po;

										$allocation_qnty = $allocation_qnty + $allocationDataArr[$prev_alc_po]['qnty'];
									}
								}

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation = "allocation_date*qnty*po_break_down_id*qnty_break_down*updated_by*update_date";
								$data_allocation = "" . $txt_receive_date . "*" . $allocation_qnty . "*'" . $po_breakdown_id . "'*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
								$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data updating
								|--------------------------------------------------------------------------
								|
								*/
								if ($allocation_mst_insert) {
									//echo "10**".bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr); die();
									$allocation_dtls_insert = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr));
								}
							} else {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing for
								| $data_allocation_dtls
								|--------------------------------------------------------------------------
								|
								*/

								$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
								$field_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";
								$data_allocation_dtls = '';
								$qnty_breakdown = '';
								$po_breakdown_id = '';
								$expRcvString = explode(',', $hdnReceiveString);
								foreach ($expRcvString as $expRcv) {
									$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
									$rcvData = explode('_', $expRcv);
									if ($data_allocation_dtls != '') {
										$data_allocation_dtls .= ',';
										$qnty_breakdown .= ',';
										$po_breakdown_id .= ',';
									}
									//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
									$po_breakdown_id .= $rcvData[0];
									$qnty_breakdown .= $rcvData[1] . '_' . $rcvData[0] . '_' . str_replace("'", "", $job_no);
									$data_allocation_dtls .= "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . "," . $rcvData[0] . "," . $txt_wo_pi . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $rcvData[1] . ",1," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								}

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and inserting
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation = "id,mst_id,entry_form,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,qnty_break_down,is_sales,inserted_by,insert_date";
								$data_allocation = "(" . $allocation_id . "," . $recieve_id . ",1," . $job_no . ",'" . $po_breakdown_id . "'," . $txt_wo_pi . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $qnty_breakdown . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data inserting
								|--------------------------------------------------------------------------
								|
								*/
								if ($allocation_mst_insert) {
									$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
								}
							}
						}
					}
				} else {
					$allocated_qnty = $adj_allocated_qnty;
					$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
				}
			} else {
				$allocated_qnty = $adj_allocated_qnty;
				$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
			}

			if (str_replace("'", "", $txt_brand) == "")
				$txt_brand = 0;

			if ($available_qnty < 0) {
				echo "30**Un-allocated quantity is not available";
				die;
			}
			if ($allocated_qnty < 0) {
				echo "30**Allocation quantity is not available";
				die;
			}

			$data_array = "" . $txt_brand . "*" . number_format($avgRate, 10, '.', '') . "*" . $txt_receive_qty . "*" . $currentStock . "*" . number_format($StockValue, 8, '.', '') . "*'" . $allocated_qnty . "'*'" . $available_qnty . "'*'" . $user_id . "'*'" . $pc_date_time . "'";

			//for transaction log
			$log_data['entry_form'] = 1;
			$log_data['ref_id'] = $log_ref_id;
			$log_data['ref_number'] = str_replace("'", "", $txt_mrr_no);
			$log_data['product_id'] = $prodMSTID;
			$log_data['current_stock'] = $currentStock;
			$log_data['allocated_qty'] = $allocated_qnty;
			$log_data['available_qty'] = $available_qnty;
			$log_data['dyed_type'] = $log_dyed_type;
			$log_data['insert_date'] = $pc_date_time;
			//end for transaction log
		} else {
			//before
			$updateID_array = array();
			$update_data = array();
			$updateID_array[] = $before_prod_id;
			if (str_replace("'", "", $before_brand) == "")
				$before_brand = 0;

			$update_data[$before_prod_id] = explode("*", ("" . $before_brand . "*" . number_format($adj_beforeAvgRate, 10, '.', '') . "*" . $before_receive_qnty . "*" . $adj_beforeStock . "*" . number_format($adj_beforeStockValue, 8, '.', '') . "*" . $adj_allocated_qnty . "*" . $adj_beforeAvailableQnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			//for transaction log
			$log_data_b4prod['entry_form'] = 1;
			$log_data_b4prod['ref_id'] = $log_ref_id;
			$log_data_b4prod['ref_number'] = str_replace("'", "", $txt_mrr_no);
			$log_data_b4prod['product_id'] = $before_prod_id;
			$log_data_b4prod['current_stock'] = $adj_beforeStock;
			$log_data_b4prod['allocated_qty'] = $adj_allocated_qnty;
			$log_data_b4prod['available_qty'] = $adj_beforeAvailableQnty;
			$log_data_b4prod['dyed_type'] = $dyed_type;
			$log_data_b4prod['insert_date'] = $pc_date_time;
			//end for transaction log

			//current
			$presentStock = $presentStock + $txt_receive_qty;
			//$available_qnty  		= $available_qnty+$txt_receive_qty;

			$presentStockValue = $presentStockValue + ($domestic_rate * $txt_receive_qty);
			$presentAvgRate = number_format($presentStockValue / $presentStock, $dec_place[3], '.', '');
			if ($variable_set_allocation == 1) {
				if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51) && (str_replace("'", "", $cbo_receive_basis) == 2)) {
					if (str_replace("'", "", $cbo_receive_purpose) == 2 && ($is_without_order == 42 || $is_without_order == 114)) {
						if ($variable_set_smn_allocation != 1) {
							$allocated_qnty = $allocated_qnty;
							$available_qnty = $available_qnty + $txt_receive_qty;
						} else {
							$allocated_qnty = $allocated_qnty + $txt_receive_qty;
							$available_qnty = $available_qnty;

							/*
							|--------------------------------------------------------------------------
							| for booking no
							|--------------------------------------------------------------------------
							|
							*/
							$sqlBookingNo = "SELECT a.is_sales, c.id, c.booking_no FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c WHERE a.id = b.mst_id AND b.booking_no = c.booking_no AND a.id = " . $txt_wo_pi_id . " AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form in (42,114)"; //AND b.product_id = ".$prodMSTID."
							$sqlBookingNo = sql_select($sqlBookingNo);
							$bookingId = '';
							$bookingNo = '';
							foreach ($sqlBookingNo as $row) {
								$bookingId = $row[csf('id')];
								$bookingNo = $row[csf('booking_no')];
								$is_sales = $row[csf('is_sales')];
							}

							//for before product id
							$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.booking_no = '" . $bookingNo . "' AND a.item_id = " . $before_prod_id . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
							$check_allocation_info = sql_select($sqlAllocation);
							if (!empty($check_allocation_info)) {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								foreach ($check_allocation_info as $row) {
									$allocation_id = $row[csf('id')];
									$allocation_dtls_id = $row[csf('dtls_id')];
									$allocation_qnty = $row[csf('qnty')] - $hdn_receive_qty;
									$qnty_breakdown = $allocation_qnty . '_' . $bookingId . '_';
								}

								$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
								$data_allocation = "" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
								$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", $allocation_id, 0);
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation_dtls = "qnty*updated_by*update_date";
								$data_allocation_dtls = "" . $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
								$allocation_dtls_insert = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", $allocation_dtls_id, 0);
							}

							//for new product id
							$is_insert = 1;
							if ($expString[0] == true) {
								$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.booking_no = '" . $bookingNo . "' AND a.item_id = " . $prodMSTID . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
								$check_allocation_info = sql_select($sqlAllocation);
								if (!empty($check_allocation_info)) {
									$is_insert = 0;
									/*
									|--------------------------------------------------------------------------
									| inv_material_allocation_mst
									| data preparing and updating
									|--------------------------------------------------------------------------
									|
									*/
									foreach ($check_allocation_info as $row) {
										$allocation_id = $row[csf('id')];
										$allocation_dtls_id = $row[csf('dtls_id')];
										$allocation_qnty = $row[csf('qnty')] + ($txt_receive_qty - $hdn_receive_qty);
										$qnty_breakdown = $allocation_qnty . '_' . $bookingId . '_';
									}

									$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
									$data_allocation = "" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
									$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", $allocation_id, 0);
									/*
									|--------------------------------------------------------------------------
									| inv_material_allocation_dtls
									| data preparing and updating
									|--------------------------------------------------------------------------
									|
									*/
									$field_allocation_dtls = "qnty*updated_by*update_date";
									$data_allocation_dtls = "" . $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
									$allocation_dtls_insert = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", $allocation_dtls_id, 0);
								}
							}

							//newly inserting
							if ($is_insert == 1) {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and inserting
								|--------------------------------------------------------------------------
								|
								*/
								$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
								$field_allocation = "id,mst_id,entry_form,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,booking_without_order,qnty_break_down,is_sales,inserted_by,insert_date";
								$qnty_breakdown = $txt_receive_qty . '_' . $bookingId . '_';
								$data_allocation = "(" . $allocation_id . "," . $recieve_id . ",1,1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $bookingId . "','" . $bookingNo . "',1,'" . $qnty_breakdown . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
								$field_allocation_dtls = "id,mst_id,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,is_sales,inserted_by,insert_date";
								$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $bookingId . "','" . $bookingNo . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
					} else {
						if ($is_sales_order == 1 && $is_auto_allocation == 1 && ($is_without_order == 135 || ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2))) {
							$allocated_qnty = $allocated_qnty;
							$available_qnty = $available_qnty + $txt_receive_qty;
						} else if ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2) {
							$allocated_qnty = $allocated_qnty;
							$available_qnty = $available_qnty + $txt_receive_qty;
						} else {
							$allocated_qnty = $allocated_qnty + $txt_receive_qty;
							$available_qnty = $available_qnty;

							$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.po_break_down_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $job_no . " AND a.item_id = " . $before_prod_id . " AND a.is_dyied_yarn=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
							$check_allocation_info = sql_select($sqlAllocation);
							if (!empty($check_allocation_info)) {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing for
								| $data_allocation_dtls
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation_dtls = "po_break_down_id*qnty*updated_by*update_date";
								$qnty_breakdown = '';
								$po_breakdown_id = '';
								$allocation_qnty = 0;
								$allocationDataArr = array();
								$updateDtlsIdArr = array();
								$data_allocation_dtls = array();
								foreach ($check_allocation_info as $row) {
									$allocation_id = $row[csf('id')];
									$allocationDataArr[$row[csf('po_break_down_id')]]['dtls_id'] = $row[csf('dtls_id')];
									$allocationDataArr[$row[csf('po_break_down_id')]]['qnty'] = $row[csf('qnty')];
								}

								//hdnReceiveString
								$expRcvString = explode(',', $hdnReceiveString);

								foreach ($expRcvString as $expRcv) {
									//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
									$rcvData = explode('_', $expRcv);
									$qnty = ($allocationDataArr[$rcvData[0]]['qnty'] - $rcvData[2]);
									$dtlsId = $allocationDataArr[$rcvData[0]]['dtls_id'];
									$updateDtlsIdArr[] = $dtlsId;
									$data_allocation_dtls[$dtlsId] = explode("*", ("" . $rcvData[0] . "*" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));

									//for inv_material_allocation_mst
									if ($qnty_breakdown != '') {
										$qnty_breakdown .= ',';
										$po_breakdown_id .= ',';
									}

									$po_breakdown_id .= $rcvData[0];

									$qnty_breakdown .= $qnty . '_' . $rcvData[0] . '_' . str_replace("'", "", $job_no);
									$allocation_qnty += $qnty;
								}
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and updating
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation = "allocation_date*qnty*qnty_break_down*updated_by*update_date";
								$data_allocation = "" . $txt_receive_date . "*" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
								$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data updating
								|--------------------------------------------------------------------------
								|
								*/
								if ($allocation_mst_insert) {
									$allocation_dtls_insert = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr));
								}
							}

							//INSERT NEW PRODUCT ALLOCATION
							$is_insert = 1;
							if ($expString[0] == true) {
								$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.po_break_down_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $job_no . " AND a.item_id = " . $prodMSTID . " AND a.is_dyied_yarn=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
								$check_allocation_info = sql_select($sqlAllocation);
								if (!empty($check_allocation_info)) {
									$is_insert = 0;
									/*
									|--------------------------------------------------------------------------
									| inv_material_allocation_dtls
									| data preparing for
									| $data_allocation_dtls
									|--------------------------------------------------------------------------
									|
									*/
									$field_allocation_dtls = "po_break_down_id*qnty*updated_by*update_date";
									$qnty_breakdown = '';
									$po_breakdown_id = '';
									$allocation_qnty = 0;
									$allocationDataArr = array();
									$updateDtlsIdArr = array();
									$data_allocation_dtls = array();
									foreach ($check_allocation_info as $row) {
										$allocation_id = $row[csf('id')];
										$allocationDataArr[$row[csf('po_break_down_id')]]['dtls_id'] = $row[csf('dtls_id')];
										$allocationDataArr[$row[csf('po_break_down_id')]]['qnty'] = $row[csf('qnty')];
									}

									//hdnReceiveString
									$expRcvString = explode(',', $hdnReceiveString);
									foreach ($expRcvString as $expRcv) {
										//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
										$rcvData = explode('_', $expRcv);
										$qnty = $rcvData[1] + $allocationDataArr[$rcvData[0]]['qnty'];
										$dtlsId = $allocationDataArr[$rcvData[0]]['dtls_id'];
										$updateDtlsIdArr[] = $dtlsId;
										$data_allocation_dtls[$dtlsId] = explode("*", ("" . $rcvData[0] . "*" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));

										//for inv_material_allocation_mst
										if ($qnty_breakdown != '') {
											$qnty_breakdown .= ',';
											$po_breakdown_id .= ',';
										}
										$po_breakdown_id .= $rcvData[0];
										$qnty_breakdown .= $qnty . '_' . $rcvData[0] . '_' . str_replace("'", "", $job_no);
										$allocation_qnty += $qnty;
									}

									/*
									|--------------------------------------------------------------------------
									| inv_material_allocation_mst
									| data preparing and updating
									|--------------------------------------------------------------------------
									|
									*/
									$field_allocation = "allocation_date*qnty*qnty_break_down*updated_by*update_date";
									$data_allocation = "" . $txt_receive_date . "*" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
									$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);

									/*
									|--------------------------------------------------------------------------
									| inv_material_allocation_dtls
									| data updating
									|--------------------------------------------------------------------------
									|
									*/
									if ($allocation_mst_insert) {
										$allocation_dtls_insert = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr));
									}
								}
							}

							if ($is_insert == 1) {
								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data preparing for
								| $data_allocation_dtls
								|--------------------------------------------------------------------------
								|
								*/
								$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
								$field_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,is_sales,inserted_by,insert_date";
								$data_allocation_dtls = '';
								$qnty_breakdown = '';
								$po_breakdown_id = '';
								$expRcvString = explode(',', $hdnReceiveString);
								foreach ($expRcvString as $expRcv) {
									$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
									$rcvData = explode('_', $expRcv);
									if ($data_allocation_dtls != '') {
										$data_allocation_dtls .= ',';
										$qnty_breakdown .= ',';
										$po_breakdown_id .= ',';
									}
									//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
									$po_breakdown_id .= $rcvData[0];
									$qnty_breakdown .= $rcvData[1] . '_' . $rcvData[0] . '_' . str_replace("'", "", $job_no);
									$data_allocation_dtls .= "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . "," . $rcvData[0] . "," . $txt_wo_pi . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $rcvData[1] . ",1," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								}

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_mst
								| data preparing and inserting
								|--------------------------------------------------------------------------
								|
								*/
								$field_allocation = "id,mst_id,entry_form,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,qnty_break_down,is_sales,inserted_by,insert_date";
								$data_allocation = "(" . $allocation_id . "," . $recieve_id . ",1," . $job_no . ",'" . $po_breakdown_id . "'," . $txt_wo_pi . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,'" . $qnty_breakdown . "'," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
								$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

								/*
								|--------------------------------------------------------------------------
								| inv_material_allocation_dtls
								| data inserting
								|--------------------------------------------------------------------------
								|
								*/
								if ($allocation_mst_insert) {
									$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
								}
							}
						}
					}
				} else {
					$allocated_qnty = $allocated_qnty;
					$available_qnty = $available_qnty + $txt_receive_qty;
				}
			} else {
				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty + $txt_receive_qty;
			}

			if (str_replace("'", "", $txt_brand) == "")
				$txt_brand = 0;

			if ($available_qnty < 0) {
				echo "30**Un-allocated quantity is not available";
				die;
			}
			if ($allocated_qnty < 0) {
				echo "30**Allocation quantity is not available";
				die;
			}

			$updateID_array[] = $prodMSTID;
			$update_data[$prodMSTID] = explode("*", ("" . $txt_brand . "*" . number_format($presentAvgRate, 10, '.', '') . "*" . $txt_receive_qty . "*" . $presentStock . "*" . number_format($presentStockValue, 8, '.', '') . "*" . $allocated_qnty . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			//for transaction log
			$log_data['entry_form'] = 1;
			$log_data['ref_id'] = $log_ref_id;
			$log_data['ref_number'] = str_replace("'", "", $txt_mrr_no);
			$log_data['product_id'] = $prodMSTID;
			$log_data['current_stock'] = $presentStock;
			$log_data['allocated_qty'] = $allocated_qnty;
			$log_data['available_qty'] = $available_qnty;
			$log_data['dyed_type'] = $log_dyed_type;
			$log_data['insert_date'] = $pc_date_time;
			//end for transaction log
		}
		//------------------ product_details_master END---------------------------------------------------//

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data preparing for
		| $data_proportionate
		|--------------------------------------------------------------------------
		|
		*/
		$data_proportionate = '';
		if ($hdnReceiveString != '') {
			$field_proportionate = "id, trans_id, trans_type, entry_form, po_breakdown_id, prod_id, grey_prod_id, color_id, quantity, inserted_by, insert_date";
			$expRcvString = explode(',', $hdnReceiveString);
			foreach ($expRcvString as $expRcv) {
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$rcvData = explode('_', $expRcv);
				if ($data_proportionate != '') {
					$data_proportionate .= ',';
				}
				$grey_prod_id = implode(",", array_unique(explode("**", $rcvData[3])));
				//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
				$data_proportionate .= "(" . $id_prop . "," . $update_id . ",1,1," . $rcvData[0] . "," . $prodMSTID . ",'" . $grey_prod_id . "', " . $cbo_color . "," . $rcvData[1] . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
		}

		$store_up_id = 0;
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$prodMSTID and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

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
				$adj_beforeStock_store			= $store_presentStock - $before_receive_qnty;
				$adj_beforeStockValue_store		= $store_presentStockValue - $beforeStoreAmount;

				$field_array_store = "rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";

				$currentStock_store		= $adj_beforeStock_store + $txt_receive_qty;
				$currentValue_store		= $adj_beforeStockValue_store + $con_amount;
				$store_avgRate = 0;
				if ($currentValue_store != 0 && $currentStock_store != 0) $store_avgRate = abs($currentValue_store / $currentStock_store);

				$data_array_store = "" . number_format($store_avgRate, 10, '.', '') . "*" . $txt_receive_qty . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*" . $txt_receive_date . "";
				//echo "20**$data_array_store.";disconnect($con);die;
			}
		}


		//echo "10**".$data_proportionate;// die();
		//hdnReceiveString: '48570_50.00_100.00'
		//echo "20**".$beforeAmount."==".$adj_beforeAvailableQnty."==".$avgRate;mysql_query("ROLLBACK");die;
		$prodUpdate = $insertR = $rID_adj = $rID_adjal = true;

		/*
		|--------------------------------------------------------------------------
		| inv_receive_master
		| data updating
		|--------------------------------------------------------------------------
		|
		*/

		$rID = sql_update("inv_receive_master", $field_array_update, $data_array_update, "recv_number", $txt_mrr_no, 0);

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data inserting/updating
		|--------------------------------------------------------------------------
		|
		*/
		$dtlsrID = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 0);

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data inserting/updating
		|--------------------------------------------------------------------------
		|
		*/
		if ($before_prod_id == $prodMSTID) {
			$prodUpdate = sql_update("product_details_master", $field_array, $data_array, "id", $prodMSTID, 0);
		} else {
			if ($data_array_prod != "") {
				$insertR = sql_insert("product_details_master", $field_array_prod, $data_array_prod, 0);
				$rID_adj = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$txt_receive_qty) where id=$before_prod_id", 0);
				$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$before_prod_id", 0);
			}
			$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array, $update_data, $updateID_array));
		}

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		$rslt_proportionate = 1;
		if ($data_proportionate != '') {
			execute_query("DELETE FROM order_wise_pro_details WHERE trans_id = " . $update_id . " AND trans_type = 1 AND entry_form = 1", 0);
			$rslt_proportionate = sql_insert("order_wise_pro_details", $field_proportionate, $data_proportionate, 0);
		}
		$storeRID = true;
		if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
			$storeRID = sql_update("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
		}

		//echo "10**".$rID ."&&". $dtlsrID ."&&". $prodUpdate ."&&". $insertR ."&&". $rslt_proportionate ."&&". $allocation_mst_insert."&&".$allocation_dtls_insert."&&".$storeRID;die;

		if ($db_type == 0) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR && $rID_adj && $rID_adjal && $rslt_proportionate && $allocation_mst_insert && $allocation_dtls_insert && $storeRID) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $cbo_receive_basis);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $cbo_receive_basis);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR && $rslt_proportionate && $allocation_mst_insert && $allocation_dtls_insert && $storeRID) {
				//for transaction log before product
				if (!empty($log_data_b4prod)) {
					$allocation_log_b4prod = manage_allocation_transaction_log($log_data_b4prod);
				}

				//for transaction log current product
				$allocation_log = manage_allocation_transaction_log($log_data);

				//echo "10**".$allocation_log."test"; die();

				if ($allocation_log) {
					oci_commit($con);
					echo "1**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $cbo_receive_basis);
				} else {
					oci_rollback($con);
					echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $cbo_receive_basis);
				}
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no) . "**" . str_replace("'", "", $cbo_receive_basis);
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 2) //Delete Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$hdn_receive_qty = str_replace("'", "", $hdn_receive_qty);
		$mrr_data = sql_select("select a.id, a.is_posted_account, a.receive_purpose, a.receive_basis, b.cons_quantity, b.cons_rate, b.cons_amount, b.store_amount, c.id as prod_id, c.current_stock, c.stock_value, c.allocated_qnty, c.available_qnty, c.dyed_type 
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 and b.id=" . $update_id . "");
		$master_id = $mrr_data[0][csf("id")];
		$is_posted_account = $mrr_data[0][csf("is_posted_account")] * 1;
		$receive_purpose = $mrr_data[0][csf("receive_purpose")];
		$receive_basis = $mrr_data[0][csf("receive_basis")];
		$cons_quantity = $mrr_data[0][csf("cons_quantity")];
		$cons_rate = $mrr_data[0][csf("cons_rate")];
		$cons_amount = $mrr_data[0][csf("cons_amount")];
		$prod_id = $mrr_data[0][csf("prod_id")];
		$current_stock = $mrr_data[0][csf("current_stock")];
		$stock_value = $mrr_data[0][csf("stock_value")];
		$allocated_qnty = $mrr_data[0][csf("allocated_qnty")];
		$available_qnty = $mrr_data[0][csf("available_qnty")];
		$log_dyed_type = $mrr_data[0][csf("dyed_type")];
		$before_store_amount = $mrr_data[0][csf("store_amount")];

		$cu_current_stock = $current_stock - $cons_quantity;
		$cu_stock_value = $stock_value - $cons_amount;
		if ($cu_stock_value > 0 && $cu_current_stock > 0)
			$cu_avg_rate = $cu_stock_value / $cu_current_stock;
		else
			$cu_avg_rate = 0;

		/*
		|--------------------------------------------------------------------------
		| is_posted_account checking
		|--------------------------------------------------------------------------
		|
		*/
		if ($is_posted_account > 0) {
			echo "13**Delete restricted, This Information is used in another Table.";
			oci_rollback($con);
			disconnect($con);
			die;
		}

		/*
		|--------------------------------------------------------------------------
		| next_operation checking
		|--------------------------------------------------------------------------
		|
		*/
		$next_operation = return_field_value("max(id) as max_trans_id", "inv_transaction", "status_active=1 and item_category=1 and transaction_type<>1 and prod_id=$prod_id", "max_trans_id");
		if ($next_operation) {
			if ($next_operation > str_replace("'", "", $update_id)) {
				echo "13**Delete restricted, This Information is used in another Table.";
				oci_rollback($con);
				disconnect($con);
				die;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| allocation qty checking
		|--------------------------------------------------------------------------
		|
		*/
		if ($allocated_qnty > 0) {
			echo "13**Delete restricted, allocation found.";
			oci_rollback($con);
			disconnect($con);
			die;
		}

		$is_without_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "entry_form");
		$is_with_order_yarn_service_work_order = return_field_value("booking_without_order", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "booking_without_order");

		if ($variable_set_allocation == 1) {
			if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 44 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51) && (str_replace("'", "", $cbo_receive_basis) == 2)) {
				if (str_replace("'", "", $cbo_receive_purpose) == 2 && ($is_without_order == 42 || $is_without_order == 114)) {
					if ($variable_set_smn_allocation != 1) {
						$cu_allocated_qnty = $allocated_qnty;
						$cu_available_qnty = $available_qnty - $cons_quantity;

						$allocation_mst_update = 1;
						$allocation_dtls_update = 1;
					} else {

						$cu_allocated_qnty = $allocated_qnty - $cons_quantity;
						$cu_available_qnty = $available_qnty;

						/*
						|--------------------------------------------------------------------------
						| for booking no
						|--------------------------------------------------------------------------
						|
						*/
						$sqlBookingNo = "SELECT c.id, c.booking_no FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c WHERE a.id = b.mst_id AND b.booking_no = c.booking_no AND a.id = " . $txt_wo_pi_id . " AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form in (42,114)"; //AND b.product_id = ".$prodMSTID."

						$sqlBookingNo = sql_select($sqlBookingNo);
						$bookingId = '';
						$bookingNo = '';
						foreach ($sqlBookingNo as $row) {
							$bookingId = $row[csf('id')];
							$bookingNo = $row[csf('booking_no')];
						}

						//for before product id
						$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.booking_no = '" . $bookingNo . "' AND a.item_id = " . $prod_id . " AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active=1 AND b.is_deleted=0";
						$check_allocation_info = sql_select($sqlAllocation);

						if (!empty($check_allocation_info)) {
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_mst
							| data preparing and updating
							|--------------------------------------------------------------------------
							|
							*/
							foreach ($check_allocation_info as $row) {
								$allocation_id = $row[csf('id')];
								$allocation_dtls_id = $row[csf('dtls_id')];
								$allocation_qnty = $row[csf('qnty')] - $hdn_receive_qty;
								$qnty_breakdown = $allocation_qnty . '_' . $bookingId . '_';
							}

							$field_allocation = "qnty*qnty_break_down*updated_by*update_date";
							$data_allocation = "" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
							$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", $allocation_id, 0);
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data preparing and updating
							|--------------------------------------------------------------------------
							|
							*/
							$field_allocation_dtls = "qnty*updated_by*update_date";
							$data_allocation_dtls = "" . $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
							$allocation_dtls_update = sql_update("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, "id", $allocation_dtls_id, 0);
						}
					}
				} else {
					if ($is_sales_order == 1 && $is_auto_allocation == 1 && ($is_without_order == 135 || ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2))) {
						$cu_allocated_qnty = $allocated_qnty;
						$cu_available_qnty = $available_qnty - $cons_quantity;

						$allocation_mst_update = 1;
						$allocation_dtls_update = 1;
					} else if ($is_without_order == 94 && $is_with_order_yarn_service_work_order == 2) {
						$cu_allocated_qnty = $allocated_qnty;
						$cu_available_qnty = $available_qnty - $cons_quantity;

						$allocation_mst_update = 1;
						$allocation_dtls_update = 1;
					} else {
						$cu_allocated_qnty = $allocated_qnty - $cons_quantity;
						$cu_available_qnty = $available_qnty;

						$sqlAllocation = "SELECT a.id, b.id AS dtls_id, b.po_break_down_id, b.qnty FROM inv_material_allocation_mst a INNER JOIN inv_material_allocation_dtls b ON a.id = b.mst_id WHERE a.job_no = " . $job_no . " AND a.item_id = " . $prod_id . " AND a.is_dyied_yarn=1 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
						$check_allocation_info = sql_select($sqlAllocation);
						if (!empty($check_allocation_info)) {
							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data preparing for
							| $data_allocation_dtls
							|--------------------------------------------------------------------------
							|
							*/
							$field_allocation_dtls = "po_break_down_id*qnty*updated_by*update_date";
							$qnty_breakdown = '';
							$po_breakdown_id = '';
							$allocation_qnty = 0;
							$allocationDataArr = array();
							$updateDtlsIdArr = array();
							$data_allocation_dtls = array();
							foreach ($check_allocation_info as $row) {
								$allocation_id = $row[csf('id')];
								$allocationDataArr[$row[csf('po_break_down_id')]]['dtls_id'] = $row[csf('dtls_id')];
								$allocationDataArr[$row[csf('po_break_down_id')]]['qnty'] = $row[csf('qnty')];
							}

							//hdnReceiveString
							$expRcvString = explode(',', $hdnReceiveString);
							foreach ($expRcvString as $expRcv) {
								//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
								$rcvData = explode('_', $expRcv);
								$qnty = ($allocationDataArr[$rcvData[0]]['qnty'] - $rcvData[2]);
								$dtlsId = $allocationDataArr[$rcvData[0]]['dtls_id'];
								$updateDtlsIdArr[] = $dtlsId;
								$data_allocation_dtls[$dtlsId] = explode("*", ("" . $rcvData[0] . "*" . $qnty . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));

								//for inv_material_allocation_mst
								if ($qnty_breakdown != '') {
									$qnty_breakdown .= ',';
									$po_breakdown_id .= ',';
								}
								$po_breakdown_id .= $rcvData[0];
								$qnty_breakdown .= $qnty . '_' . $rcvData[0] . '_' . str_replace("'", "", $job_no);
								$allocation_qnty += $qnty;
							}

							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_mst
							| data preparing and updating
							|--------------------------------------------------------------------------
							|
							*/
							$field_allocation = "allocation_date*qnty*qnty_break_down*updated_by*update_date";
							$data_allocation = "" . $txt_receive_date . "*" . $allocation_qnty . "*'" . $qnty_breakdown . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
							$allocation_mst_update = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);

							/*
							|--------------------------------------------------------------------------
							| inv_material_allocation_dtls
							| data updating
							|--------------------------------------------------------------------------
							|
							*/
							if ($allocation_mst_update) {

								$allocation_dtls_update = execute_query(bulk_update_sql_statement("inv_material_allocation_dtls", "id", $field_allocation_dtls, $data_allocation_dtls, $updateDtlsIdArr));
							}
						}
					}
				}
			} else {
				$cu_allocated_qnty = $allocated_qnty;
				$cu_available_qnty = $available_qnty - $cons_quantity;

				$allocation_mst_update = 1;
				$allocation_dtls_update = 1;
			}
		} else {
			$cu_allocated_qnty = $allocated_qnty;
			$cu_available_qnty = $available_qnty - $cons_quantity;

			$allocation_mst_update = 1;
			$allocation_dtls_update = 1;
		}

		/*
		|--------------------------------------------------------------------------
		| inv_receive_master
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";

		$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");
		$checkTransaction = sql_select("select a.id from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 and a.id = " . $recieve_id . " and b.id !=" . $update_id . "");

		$rcvID = 1;
		if (count($checkTransaction) == 0) {
			$rcvID = sql_update("inv_receive_master", $field_array, $data_array, "id", $recieve_id, 0);
		}

		/*oci_rollback($con);
		echo "10**" . count($checkTransaction);		
		disconnect($con);
		die;*/

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$dtlsrID = sql_update("inv_transaction", $field_array, $data_array, "id", $update_id, 0);

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_prod = "current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = "" . $cu_current_stock . "*" . number_format($cu_avg_rate, 10, '.', '') . "*" . number_format($cu_stock_value, 8, '.', '') . "*'" . $cu_allocated_qnty . "'*'" . $cu_available_qnty . "'*'" . $user_id . "'*'" . $pc_date_time . "'";
		if ($variable_store_wise_rate == 1) {
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");
			$store_up_id = 0;
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
				$currentStock_store		= $store_presentStock - $cons_quantity;
				$currentValue_store		= $store_presentStockValue - $before_store_amount;

				$store_avgRate = 0;
				if ($currentValue_store != 0 && $currentStock_store != 0) $store_avgRate = abs($currentValue_store / $currentStock_store);

				$field_array_store = "rate*last_purchased_qnty*cons_qty*amount*updated_by*update_date*last_receive_date";
				$data_array_store = "" . number_format($store_avgRate, 10, '.', '') . "*" . $cons_quantity . "*" . $currentStock_store . "*" . number_format($currentValue_store, 8, '.', '') . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*" . $txt_receive_date . "";
			}
		}

		$prodID = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", "$prod_id", 0);
		$storeRID = true;
		if ($store_up_id > 0 && $variable_store_wise_rate == 1) {
			$storeRID = sql_update("inv_store_wise_yarn_qty_dtls", $field_array_store, $data_array_store, "id", $store_up_id, 1);
		}
		//for transaction log
		$log_data['entry_form'] = 1;
		$log_data['ref_id'] = $master_id;
		$log_data['ref_number'] = str_replace("'", "", $txt_mrr_no);
		$log_data['product_id'] = $prod_id;
		$log_data['current_stock'] = $cu_current_stock;
		$log_data['allocated_qty'] = $cu_allocated_qnty;
		$log_data['available_qty'] = $cu_available_qnty;
		$log_data['dyed_type'] = $log_dyed_type;
		$log_data['insert_date'] = $pc_date_time;
		//end for transaction log

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$rslt_proportionate = sql_update("order_wise_pro_details", $field_array, $data_array, "trans_id", $update_id, 0);

		//echo "10**$rcvID && $prodID && $dtlsrID && $rslt_proportionate && $allocation_mst_update && $allocation_dtls_update && $storeRID";oci_rollback($con);disconnect($con); die;

		if ($db_type == 0) {
			if ($rcvID && $prodID && $dtlsrID && $rslt_proportionate && $allocation_mst_update && $allocation_dtls_update && $storeRID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_mrr_no) . "**" . count($checkTransaction);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rcvID && $prodID && $dtlsrID && $rslt_proportionate && $allocation_mst_update && $allocation_dtls_update && $storeRID) {
				$allocation_log = manage_allocation_transaction_log($log_data);

				if ($allocation_log) {
					oci_commit($con);
					echo "2**" . str_replace("'", "", $txt_mrr_no) . "**" . count($checkTransaction);
				} else {
					oci_rollback($con);
					echo "10**" . str_replace("'", "", $txt_mrr_no);
				}
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		}

		disconnect($con);
		die;
	}
}

if ($action == "mrr_popup_info") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrrID) {
			var splitArr = mrrID.split("_");
			$("#hidden_recv_id").val(splitArr[0]); // id number
			$("#hidden_recv_number").val(splitArr[1]); // mrr number
			$("#hidden_posted_in_account").val(splitArr[2]); // check posted in account
			$("#supp_id").val(splitArr[3]);
			
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Supplier</th>
							<th width="150">Search By</th>
							<th width="250" align="center" id="search_by_td_up">Enter MRR Number</th>
							<th width="200">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1 => 'MRR No', 2 => 'Challan No', 3 => 'WO No.', 4 => 'PI No.');
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 120, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1); ?>
								<!-- Hidden field here -->
								<input type="hidden" id="hidden_recv_number" value="" />
								<input type="hidden" id="hidden_recv_id" value="" />
								<input type="hidden" id="hidden_posted_in_account" value="" />
								<input type="hidden" id="supp_id" value="" />
								<!--END -->
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit;
}

if ($action == "job_popup_info") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data) {
			var splitArr = data.split("_");
			issue_id = splitArr[0];
			job_no = splitArr[1];
			alert(job_no);
			$("#hidden_issue_id").val(issue_id); // id number
			$("#hidden_job_no").val(job_no); // job no
			parent.emailwindow.hide();
		}

	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="120">Buyer</th>
							<th width="120">Search By</th>
							<th width="200" align="center" id="search_by_td_up">Enter Issue No</th>
							<th width="120">Style Type</th>
							<th width="180">Date Range</th>
							<th ><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_buyer", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1 => 'Issue No', 2 => 'Job No', 3 => 'Style Ref');
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 100, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<?
								$style_type = array(1 => 'Bulk', 2 => 'Sample');
								echo create_drop_down("cbo_style_type", 100, $style_type, "", 1, "--Select--", "1", "", 0);
								?>
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_style_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'leftover_yarn_receive_sweater_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1); ?>
								<!-- Hidden field here -->
								<input type="hidden" id="hidden_issue_id" value="" />
								<input type="hidden" id="hidden_job_no" value="" />
								<!-- <input type="hidden" id="hidden_posted_in_account" value="" />
								<input type="hidden" id="supp_id" value="" /> --> 
								<!--END -->
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit;
}

if ($action == "create_mrr_search_list_view") 
{
	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$cbo_year = $ex_data[6];

	$com_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

	$sql_cond = "";
	if (trim($txt_search_common) != "") {
		if (trim($txt_search_by) == 1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common'";
		} else if (trim($txt_search_by) == 2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		} else if (trim($txt_search_by) == 3) // for wo
		{
			$sql_cond .= " and a.booking_no LIKE '%$txt_search_common' and a.receive_basis = 2";
		} else if (trim($txt_search_by) == 4) // for pi
		{
			$sql_cond .= " and a.booking_no LIKE '%$txt_search_common%' and a.receive_basis = 1";
		}
	}
	if ($cbo_year != '' || $cbo_year != 0) {
		$sql_cond .= " and TO_CHAR (a.receive_date, 'YYYY') = " . $cbo_year . "";
	}

	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.receive_date between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.receive_date between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";
	if (trim($supplier) != 0) $sql_cond .= " and a.supplier_id='$supplier'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later


	if ($user_store_ids) $user_store_cond = " and a.store_id in ($user_store_ids)";
	else $user_store_cond = "";

	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number,a.yarn_bag_receive, a.supplier_id, a.challan_no, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty,a.is_posted_account,c.lc_number, d.pay_mode from inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=1 and a.is_multi=0 and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $user_store_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.supplier_id, a.challan_no, a.receive_date, a.receive_basis,a.insert_date,a.is_posted_account,a.yarn_bag_receive,c.lc_number,d.pay_mode order by a.id desc"; //a.yarn_bag_receive !=1 and

	// echo $sql;

	$sqlresult = sql_select($sql);
	?>

		<style>
			.wrd_brk {
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='900'>
			<thead>
				<tr>
					<th width="30" class="wrd_brk">SL</th>
					<th width="60" class="wrd_brk">Year</th>
					<th width="70" class="wrd_brk">MRR No</th>
					<th width="130" class="wrd_brk">Supplier Name</th>
					<th width="120" class="wrd_brk">Challan No</th>
					<th width="120" class="wrd_brk">LC No</th>
					<th width="120" class="wrd_brk">Receive Date</th>
					<th width="100" class="wrd_brk">Receive Basis</th>
					<th width="" class="wrd_brk">Receive Qnty</th>
				</tr>
			</thead>
		</table>
		<div style="width:900px; max-height:220px; overflow-y:scroll" id="scroll_body">
			<table width="880px" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
				<tbody>
					<?
					$i = 1;
					foreach ($sqlresult as $row) {

						if ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) {
							$supplier_name = $com_arr[$row[csf("supplier_id")]];
						} else {
							$supplier_name = $supplier_arr[$row[csf("supplier_id")]];
						}
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('recv_number')]; ?>_<? echo $row[csf('is_posted_account')]; ?>_<?php echo $row[csf("supplier_id")] ; ?>  '   );">
							<td width="30" class="wrd_brk"><?php echo $i; ?></td>
							<td width="60" class="wrd_brk"><?php echo $row[csf("year")]; ?></td>
							<td width="70" class="wrd_brk"><?php echo $row[csf("recv_number_prefix_num")]; ?></td>
							<td width="130" class="wrd_brk"><?php echo $supplier_name; ?></td>
							<td width="120" class="wrd_brk"><?php echo $row[csf("challan_no")]; ?></td>
							<td width="120" class="wrd_brk"><?php echo $row[csf("lc_number")]; ?></td>
							<td width="120" class="wrd_brk"><?php echo change_date_format($row[csf("receive_date")]); ?></td>
							<td width="100" class="wrd_brk"><?php echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></td>
							<td width="" class="wrd_brk"><?php echo number_format($row[csf("receive_qnty")], 2); ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
		</div>
		</table>

	<?
		exit();
}

if ($action == "create_job_search_list_view") 
{
	$ex_data = explode("_", $data);
	$buyer = $ex_data[0];
	$cbo_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$txt_style_type = trim($ex_data[3]);
	$fromDate = $ex_data[4];
	$toDate = $ex_data[5];
	$company = $ex_data[6];
	$cbo_year = $ex_data[7];

	$com_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$style_type = array(1 => 'Bulk', 2 => 'Sample');

	$sql_cond = "";
	if (trim($txt_search_common) != "") {
		if (trim($cbo_search_by) == 1) // for issue
		{
			$sql_cond .= " and a.ISSUE_NUMBER_PREFIX_NUM LIKE '%$txt_search_common'";
		} else if (trim($cbo_search_by) == 2) // for Job no
		{
			$sql_cond .= " and a.BUYER_JOB_NO LIKE '%$txt_search_common%'";
		} else if (trim($cbo_search_by) == 3) // for Style
		{
			$sql_cond .= " and a.STYLE_REF LIKE '%$txt_search_common'";
		} 
	}


	if ($cbo_year != '' || $cbo_year != 0) {
		$sql_cond .= " and TO_CHAR (a.issue_date, 'YYYY') = " . $cbo_year . "";
	}

	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.issue_date between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.issue_date between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";
	if (trim($buyer) != 0) $sql_cond .= " and a.BUYER_ID='$buyer'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = ""; //defined Later

	$sql = "select a.id, $year_field a.issue_number, a.buyer_id, a.style_ref, a.buyer_job_no, a.issue_date, 1 as style_type from inv_issue_master a where a.entry_form = 277 and a.issue_purpose = 80 $sql_cond";

	// echo $sql; die;

	$sqlresult = sql_select($sql);
	?>

		<style>
			.wrd_brk {
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='720'>
			<thead>
				<tr>
					<th width="30" class="wrd_brk">SL</th>
					<th width="130" class="wrd_brk">Issue No</th>
					<th width="70" class="wrd_brk">Issue Date</th>
					<th width="130" class="wrd_brk">Buyer Name</th>
					<th width="120" class="wrd_brk">Job No</th>
					<th width="120" class="wrd_brk">Style Ref</th>
					<th width="120" class="wrd_brk">Style Type</th>
				</tr>
			</thead>
		</table>
		<div style="width:740px; max-height:220px; overflow-y:scroll" id="scroll_body">
			<table width="720px" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
				<tbody>
					<?
					$i = 1;
					foreach ($sqlresult as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $row[csf('buyer_job_no')]; ?>');">
							<td align="center" width="30" class="wrd_brk"><?php echo $i; ?></td>
							<td align="center" width="130" class="wrd_brk"><?php echo $row[csf("issue_number")]; ?></td>
							<td align="center" width="70" class="wrd_brk"><?php echo change_date_format($row[csf("issue_date")]); ?></td>
							<td align="center" width="130" class="wrd_brk"><?php echo $buyer_arr[$row['BUYER_ID']]; ?></td>
							<td align="center" width="120" class="wrd_brk"><?php echo $row[csf("buyer_job_no")]; ?></td>
							<td align="center" width="120" class="wrd_brk"><?php echo $row[csf("style_ref")]; ?></td>
							<td align="center" width="120" class="wrd_brk"><?php echo $style_type[$row['STYLE_TYPE']]; ?></td>
						</tr>
					<? $i++;
					} ?>
				</tbody>
		</div>
		</table>

	<?
		exit();
}

if ($action == "populate_data_from_data") {
	$ex_data = explode("_", $data);
	$mrrNo = $ex_data[0];
	$rcvID = $ex_data[1];
	$vs_rate_hide = $ex_data[2];

	$sql = "select id,recv_number,company_id,receive_basis,receive_purpose,receive_date,booking_id,challan_no,store_id,lc_no,supplier_id,loan_party,exchange_rate,currency_id,lc_no,source, yarn_issue_challan_no, issue_id, remarks, is_audited, gate_entry_no, gate_entry_date,boe_mushak_challan_no,boe_mushak_challan_date from inv_receive_master
	where id=$rcvID and recv_number='$mrrNo' and entry_form=1";
	// echo $sql;die;
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		echo "$('#cbo_receive_basis').val(" . $row[csf("receive_basis")] . ");\n";
		echo "$('#cbo_receive_purpose').val(" . $row[csf("receive_purpose")] . ");\n";
		echo "change_placeholder(" . $row[csf("receive_purpose")] . ");\n";
		echo "$('#txt_receive_date').val('" . change_date_format($row[csf("receive_date")]) . "');\n";
		echo "$('#txt_challan_no').val('" . $row[csf("challan_no")] . "');\n";
		echo "$('#txt_gate_entry_no').val('" . $row[csf("gate_entry_no")] . "');\n";
		echo "$('#txt_gate_entry_date').val('" . change_date_format($row[csf("gate_entry_date")]) . "');\n";
		echo "load_room_rack_self_bin('requires/yarn_receive_controller*1', 'store','store_td', '" . $row[csf('company_id')] . "','" . "',this.value);\n";
		echo "$('#cbo_store_name').val(" . $row[csf("store_id")] . ");\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_currency').val(" . $row[csf("currency_id")] . ");\n";
		echo "$('#txt_exchange_rate').val(" . $row[csf("exchange_rate")] . ");\n";
		echo "$('#cbo_source').val(" . $row[csf("source")] . ");\n";
		echo "$('#txt_mst_remarks').val('" . $row[csf("remarks")] . "');\n";
		echo "$('#txt_boe_mushak_challan_no').val('" . $row[csf("boe_mushak_challan_no")] . "');\n";
		echo "$('#txt_boe_mushak_challan_date').val('" . change_date_format($row[csf("boe_mushak_challan_date")]) . "');\n";

		$wopi_pay_mode = 0;
		if ($row[csf("receive_basis")] == 1) {
			$wopi = return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
			$pi_basis = return_field_value("pi_basis_id", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
		} else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 44 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51)) {
			$wopi_sql = sql_select("select ydw_no, pay_mode,entry_form,booking_without_order,is_sales from wo_yarn_dyeing_mst where id=" . $row[csf("booking_id")] . "");

			$wopi = $wopi_sql[0][csf("ydw_no")];
			$wopi_pay_mode = $wopi_sql[0][csf("pay_mode")];
			$wo_entry_form = $wopi_sql[0][csf("entry_form")];
			$booking_without_order = $wopi_sql[0][csf("booking_without_order")];
			$is_sales = $wopi_sql[0][csf("is_sales")];

			if ($row[csf("receive_basis")] == 2 && ($wo_entry_form == 42 || $wo_entry_form == 114)) {
				echo "$('#txt_receive_qty').removeAttr('onclick','func_onclick_qty()').attr({'onblur':'fn_calile()','readonly':false})\n";
			} else if ($row[csf("receive_basis")] == 2 && ($wo_entry_form == 94 || $wo_entry_form == 340)) {
				if ($booking_without_order == 2 && $is_sales == 2) {
					echo "$('#txt_receive_qty').removeAttr('onclick','func_onclick_qty()').attr({'onblur':'fn_calile()','readonly':false})\n";
				} else {
					echo "$('#txt_receive_qty').attr('onclick','func_onclick_qty()');\n";
				}
			}
		} else {
			$wo_non_pi_sql = sql_select("select wo_number,pay_mode, entry_form from wo_non_order_info_mst where id=" . $row[csf("booking_id")] . "");

			$wopi = $wo_non_pi_sql[0][csf("wo_number")];
			$wopi_pay_mode = $wo_non_pi_sql[0][csf("pay_mode")];
			$wo_entry_form = $wo_non_pi_sql[0][csf("entry_form")];
		}

		echo "$('#txt_wo_pi').val('" . $wopi . "');\n";
		echo "$('#txt_wo_pi_id').val(" . $row[csf("booking_id")] . ");\n";
		echo "$('#txt_pi_basis').val(" . $pi_basis . ");\n";
		if ($wo_entry_form) {
			echo "$('#hdn_entry_form').val(" . $wo_entry_form . ");\n";
		} else {
			echo "$('#hdn_entry_form').val('');\n";
		}

		if ($row[csf("receive_basis")] == 1 || $row[csf("receive_basis")] == 2) {
			echo "show_list_view('" . $row[csf("receive_basis")] . "**" . $row[csf("booking_id")] . "**" . $row[csf("receive_purpose")] . "**" . $row[csf("company_id")] . "**" . $vs_rate_hide . " ','show_product_listview','list_product_container','requires/yarn_receive_controller','');\n";

			if ($row[csf("receive_basis")] == 2 && $row[csf("receive_purpose")] == 2) {
				echo "load_drop_down( 'requires/yarn_receive_controller','" . $row[csf("receive_basis")] . "_" . $row[csf("receive_purpose")] . "_" . $row[csf("booking_id")] . "','load_drop_down_color', 'color_td_id' );\n";
			}
			echo "$('#cbo_source').attr('disabled','disabled');\n";
		} else {

			echo "$('#cbo_source').removeAttr('disabled','disabled');\n";
		}

		if ($row[csf("receive_basis")] == 2 && ($wopi_pay_mode == 3 || $wopi_pay_mode == 5) && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 44 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51)) {
			$data_ref = "'" . $row[csf("receive_basis")] . "_" . $row[csf("receive_purpose")] . "_" . $wopi . "_" . $wopi_pay_mode . "_" . $wo_entry_form . "'";
			echo "load_drop_down( 'requires/yarn_receive_controller', $data_ref,'load_drop_down_company_from_check_wo_paymode', 'supplier' );\n";
		} else {
			echo "load_supplier();\n";
		}

		echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
		echo "$('#cbo_party').val(" . $row[csf("loan_party")] . ");\n";
		echo "$('#txt_issue_challan_no').val(" . $row[csf("yarn_issue_challan_no")] . ");\n";
		echo "$('#txt_issue_id').val(" . $row[csf("issue_id")] . ");\n";


		echo "$('#hidden_lc_id').val(" . $row[csf("lc_no")] . ");\n";
		$lcNumber = return_field_value("lc_number", "com_btb_lc_master_details", "id='" . $row[csf("lc_no")] . "'");
		echo "$('#txt_lc_no').val('" . $lcNumber . "');\n";

		// Check Audited
		if ($row[csf("is_audited")] == 1) echo "$('#audited').text('Audited');\n";
		else echo "$('#audited').text('');\n";

		//for pay mode
		echo "$('#hdnPayMode').val('" . $wopi_pay_mode . "');\n";

		//right side list view
		echo "show_list_view('" . $row[csf("recv_number")] . "**" . $row[csf("id")] . "**" . $vs_rate_hide . "','show_dtls_list_view','list_container_yarn','requires/yarn_receive_controller','');\n";
	}
	exit();
}

if ($action == "show_dtls_list_view") {
	$ex_data = explode("**", $data);
	$recv_number = $ex_data[0];
	$rcv_mst_id = $ex_data[1];
	$vs_rate_hide = $ex_data[2];

	$cond = "";
	if ($recv_number != "") $cond .= " and a.recv_number='$recv_number'";
	if ($rcv_mst_id != "") $cond .= " and a.id='$rcv_mst_id'";

	$sql = "select a.recv_number, a.receive_purpose, b.id, a.booking_id, a.receive_basis,b.pi_wo_batch_no,c.product_name_details,c.lot,b.order_uom,b.order_qnty,b.order_rate,b.order_ile_cost,b.order_amount,b.cons_amount,b.booking_no
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and c.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 $cond";
	//echo $sql;
	$result = sql_select($sql);
	$i = 1;
	$totalQnty = 0;
	$totalAmount = 0;
	$totalbookCurr = 0;
	?>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
			<thead>
				<tr>
					<th>SL</th>
					<th>WO/PI No/FSO</th>
					<th>MRR No</th>
					<th>Product Details</th>
					<th>Yarn Lot</th>
					<th>UOM</th>
					<th>Receive Qty</th>
					<th <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>>Rate</th>
					<th>ILE Cost</th>
					<th <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>>Amount</th>
					<th <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>>Book Currency</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($result as $row) {

					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$wopi = "";
					if ($row[csf("receive_basis")] == 1)
						$wopi = return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
					else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 7 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 44 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51))
						$wopi = return_field_value("ydw_no", "wo_yarn_dyeing_mst", "id=" . $row[csf("booking_id")] . "");
					else if ($row[csf("receive_basis")] == 14) {
						$wopi = $row[csf("booking_no")];
					} else
						$wopi = return_field_value("wo_number", "wo_non_order_info_mst", "id=" . $row[csf("booking_id")] . "");

					//order amount calculation here
					$qty = number_format($row[csf("order_qnty")], 2, '.', '') * 1;
					$rate = $row[csf("order_rate")] * 1;
					$ileCost = $row[csf("order_ile_cost")] * 1;
					$row[csf("order_amount")] = $qty * ($rate + $ileCost);

					$totalQnty += $row[csf("order_qnty")];
					$totalAmount += ($row[csf("order_rate")] * $row[csf("order_qnty")]);
					$totalbookCurr += $row[csf("cons_amount")];

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]; ?>","child_form_input_data","requires/yarn_receive_controller")' style="cursor:pointer">
						<td width="30"><?php echo $i; ?></td>
						<td width="120">
							<p><?php echo $wopi; ?></p>
						</td>
						<td width="100">
							<p><?php echo $row[csf("recv_number")]; ?></p>
						</td>
						<td width="250">
							<p><?php echo $row[csf("product_name_details")]; ?></p>
						</td>
						<td width="80">
							<p><?php echo $row[csf("lot")]; ?></p>
						</td>
						<td width="40">
							<p><?php echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p>
						</td>
						<td width="80" align="right">
							<p><?php echo number_format($row[csf("order_qnty")], 2); ?></p>
						</td>
						<td width="60" align="right" <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>>
							<p><?php echo number_format($row[csf("order_rate")], 2); ?></p>
						</td>
						<td width="80" align="right">
							<p><?php echo $row[csf("order_ile_cost")]; ?></p>
						</td>
						<td width="70" align="right" <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>>
							<p><?php echo number_format(($row[csf("order_rate")] * $row[csf("order_qnty")]), 2); ?></p>
						</td>
						<td width="50" align="right" <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>>
							<p><?php echo number_format($row[csf("cons_amount")], 2); ?></p>
						</td>
					</tr>
				<? $i++;
				} ?>
			<tfoot>
				<th colspan="6">Total= </th>
				<th><?php echo number_format($totalQnty, 2); ?></th>
				<th colspan="2"></th>
				<th <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>><?php echo number_format($totalAmount, 2); ?></th>
				<th <? echo $rate_dislpay = ($vs_rate_hide == 1) ? "style='display: none;'" : ""; ?>><?php echo number_format($totalbookCurr, 2); ?></th>
				<th></th>
			</tfoot>
			</tbody>
		</table>
	<?
		exit();
}


if ($action == "child_form_input_data") {
	$rcv_dtls_id = $data;
	$sql = "select a.receive_purpose,a.currency_id, a.exchange_rate,a.booking_id, b.id, b.receive_basis, b.job_no,a.company_id, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.color, b.order_uom, b.order_qnty,grey_quantity,b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags, b.product_code,b.store_id, b.room, b.rack, b.self, b.bin_box,b.floor_id, b.cone_per_bag,b.no_loose_cone, b.weight_per_bag, b.weight_per_cone, b.remarks, b.supplier_id, b.buyer_id, b.pi_wo_req_dtls_id
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id'";
	//echo $sql;die;
	$rcvDtlsResult = sql_select($sql);

	$rcvDtlsResult[0][csf("prod_id")];
	$company_id = $rcvDtlsResult[0][csf("company_id")];
	$brand_id = $rcvDtlsResult[0][csf("brand_id")];
	$receive_basis = $rcvDtlsResult[0][csf("receive_basis")];
	$rcvPurpose = $rcvDtlsResult[0][csf("receive_purpose")];
	$booking_id = $rcvDtlsResult[0][csf("booking_id")];

	$brand_name = return_field_value("brand_name", "lib_brand", "id=" . $brand_id . "");

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 1 order by id");

	$variable_rcv_result = sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$company_id and variable_list='31' and status_active=1 and is_deleted=0");

	$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];


	if ($db_type == 0) {
		$orderBy_cond = "IFNULL";
	} else if ($db_type == 2) {
		$orderBy_cond = "NVL";
	} else {
		$orderBy_cond = "ISNULL";
	}

	$wo_po_qnty = 0;
	$totalRcvQnty = 0;
	$updateRcvQnty = 0;
	if ($receive_basis == 1 || $receive_basis == 2) {
		if ($rcvDtlsResult[0][csf("yarn_comp_type2nd")] == "") {
			$yarn_comp_type2nd = 0;
		} else {
			$yarn_comp_type2nd = $rcvDtlsResult[0][csf("yarn_comp_type2nd")];
		}
		if ($rcvDtlsResult[0][csf("yarn_comp_percent2nd")] == "") {
			$yarn_comp_percent2nd = 0;
		} else {
			$yarn_comp_percent2nd = $rcvDtlsResult[0][csf("yarn_comp_percent2nd")];
		}

		if ($variable_rcv_level == 2) // ############## for wo pi dtls level
		{
			$whereCondition = " a.id=b.prod_id and b.mst_id = c.id and b.company_id=$company_id and a.supplier_id=" . $rcvDtlsResult[0][csf("supplier_id")] . " and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_req_dtls_id=" . $rcvDtlsResult[0][csf("pi_wo_req_dtls_id")] . " and b.receive_basis=" . $receive_basis . " and b.status_active=1 and b.is_deleted = 0";
		} else // ############## for wo pi item level
		{
			$whereCondition = "a.id=b.prod_id and b.mst_id = c.id and a.yarn_count_id=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and a.yarn_comp_type1st=" . $rcvDtlsResult[0][csf("yarn_comp_type1st")] . " and a.yarn_comp_percent1st=" . $rcvDtlsResult[0][csf("yarn_comp_percent1st")] . " and a.yarn_comp_type2nd=" . $rcvDtlsResult[0][csf("yarn_comp_type2nd")] . " and a.yarn_comp_percent2nd=" . $yarn_comp_percent2nd . " and a.yarn_type=" . $rcvDtlsResult[0][csf("yarn_type")] . " and a.color=" . $rcvDtlsResult[0][csf("color")] . " and a.supplier_id=" . $rcvDtlsResult[0][csf("supplier_id")] . " and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and c.entry_form=1 and b.pi_wo_batch_no=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.receive_basis=" . $receive_basis . "";
		}


		if ($whereCondition == "") $totalRcvQnty = 0;

		if ($receive_basis == 1) {
			if ($variable_rcv_level == 2) // for wo pi dtls level
			{
				$wo_po_qnty = return_field_value("sum(b.quantity) as qnty", "com_pi_master_details a, com_pi_item_details b", "a.id=b.pi_id and b.id=" . $rcvDtlsResult[0][csf("pi_wo_req_dtls_id")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
			} else // for wo pi item level
			{
				$wo_po_qnty = return_field_value("sum(b.quantity) as qnty", "com_pi_master_details a, com_pi_item_details b", "a.id=b.pi_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.count_name=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and b.yarn_composition_item1=" . $rcvDtlsResult[0][csf("yarn_comp_type1st")] . " and b.yarn_composition_percentage1=" . $rcvDtlsResult[0][csf("yarn_comp_percent1st")] . " and $orderBy_cond(b.yarn_composition_item2,0)=" . $yarn_comp_type2nd . " and $orderBy_cond(b.yarn_composition_percentage2,0)=" . $yarn_comp_percent2nd . " and b.yarn_type=" . $rcvDtlsResult[0][csf("yarn_type")] . " and b.color_id=" . $rcvDtlsResult[0][csf("color")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
			}
		} else if ($receive_basis == 2) {
			if ($rcvDtlsResult[0][csf("receive_purpose")] == 2 || $rcvDtlsResult[0][csf("receive_purpose")] == 7 || $rcvDtlsResult[0][csf("receive_purpose")] == 12 || $rcvDtlsResult[0][csf("receive_purpose")] == 38 || $rcvDtlsResult[0][csf("receive_purpose")] == 44 || $rcvDtlsResult[0][csf("receive_purpose")] == 46 || $rcvDtlsResult[0][csf("receive_purpose")] == 63) {
				if ($rcvDtlsResult[0][csf("receive_purpose")] == 2) {
					$colorCond = "  and b.yarn_color=" . $rcvDtlsResult[0][csf("color")];
				}

				$wo_po_qnty = return_field_value("sum(b.yarn_wo_qty) as qnty", "wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b", " a.id=b.mst_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.count=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " $colorCond and b.status_active=1 and b.is_deleted=0", "qnty");
			} else if ($rcvDtlsResult[0][csf("receive_purpose")] == 15 || $rcvDtlsResult[0][csf("receive_purpose")] == 50 || $rcvDtlsResult[0][csf("receive_purpose")] == 51) {

				$wo_po_qnty = return_field_value("sum(b.yarn_wo_qty) as qnty", "wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b,wo_yarn_dyeing_dtls_fin_prod c", " a.id=b.mst_id and b.mst_id=c.mst_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and c.yarn_count=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and c.yarn_color=" . $rcvDtlsResult[0][csf("color")] . " and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0", "qnty");
			} else {
				if ($variable_rcv_level == 2) // for wo pi dtls level
				{
					$wo_po_qnty = return_field_value("sum(b.supplier_order_quantity) as qnty", "wo_non_order_info_mst a, wo_non_order_info_dtls b", "a.id=b.mst_id and b.id=" . $rcvDtlsResult[0][csf("pi_wo_req_dtls_id")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
				} else // for wo pi item level
				{
					$wo_po_qnty = return_field_value("sum(b.supplier_order_quantity) as qnty", "wo_non_order_info_mst a, wo_non_order_info_dtls b", "a.id=b.mst_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.yarn_count=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and b.yarn_comp_type1st=" . $rcvDtlsResult[0][csf("yarn_comp_type1st")] . " and b.yarn_comp_percent1st=" . $rcvDtlsResult[0][csf("yarn_comp_percent1st")] . " and $orderBy_cond(b.yarn_comp_type2nd,0)=" . $yarn_comp_type2nd . " and $orderBy_cond(b.yarn_comp_percent2nd,0)=" . $yarn_comp_percent2nd . " and b.yarn_type=" . $rcvDtlsResult[0][csf("yarn_type")] . " and b.color_name=" . $rcvDtlsResult[0][csf("color")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
				}
			}
		}

		$mrr_result = sql_select("select sum(b.cons_quantity) as recv_qnty, c.recv_number,a.id as prod_id from product_details_master a, inv_transaction b, inv_receive_master c where $whereCondition and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.recv_number,a.id");
		foreach ($mrr_result as $val) {
			$totalRcvQnty += $val[csf("recv_qnty")];
			$mrr_arr[$val[csf("recv_number")]] = $val[csf("recv_number")];
			$prod_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
		}
		unset($mrr_result);

		$mrr_result_update = sql_select("select sum(b.cons_quantity) as recv_qnty, c.recv_number,a.id as prod_id from product_details_master a, inv_transaction b, inv_receive_master c where $whereCondition and b.id not in($rcv_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.recv_number,a.id");
		foreach ($mrr_result_update as $val) {
			$updateRcvQnty += $val[csf("recv_qnty")];
		}
		unset($mrr_result_update);
	}

	$mrr_nos = "'" . implode("','", array_filter($mrr_arr)) . "'";
	$prod_ids = implode(",", array_filter($prod_arr));

	if (str_replace("'", "", $mrr_nos) != "" && $prod_arr != "") {
		$direct_mrr_return_res = sql_select("select sum(b.cons_quantity) as ret_qnty
			from product_details_master a, inv_transaction b , inv_issue_master c
			where a.id=b.prod_id and b.mst_id = c.id and c.entry_form = 8 and c.received_mrr_no in ($mrr_nos)
			and a.id in ($prod_ids) and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0
			group by c.issue_number, b.prod_id");
		$rcvReturnQnty = 0;
		foreach ($direct_mrr_return_res as $val) {
			$rcvReturnQnty += $val[csf("ret_qnty")];
		}
		unset($direct_mrr_return_res);

		//Issue Nos From Receive Mrr
		$issue_sql = sql_select("select a.id,b.cons_quantity, b.id as trans_id
			from  inv_issue_master a, inv_transaction b, inv_mrr_wise_issue_details c, inv_receive_master d, inv_transaction e
			where a.id = b.mst_id and b.id = c.issue_trans_id and c.recv_trans_id = e.id and d.id = e.mst_id
			and d.recv_number in ($mrr_nos) and b.prod_id in ($prod_ids)
			and a.entry_form = 3 and c.entry_form = 3 and d.item_category=1 and b.status_active = 1 and b.is_deleted = 0");
		foreach ($issue_sql as $val) {
			$issue_id_arr[$val[csf("id")]] = $val[csf("id")];
		}

		$issue_ids = implode(",", array_filter($issue_id_arr));

		if ($issue_ids) {
			// $Issue Return From Issue
			$issue_ret_sql = sql_select("select a.id,a.recv_number,b.cons_quantity, b.id as trans_id
				from  inv_receive_master a, inv_transaction b
				where a.id = b.mst_id and a.entry_form =9 and b.item_category = 1
				and b.status_active = 1 and b.is_deleted = 0 and a.issue_id in ($issue_ids) and b.prod_id in ($prod_ids)");

			foreach ($issue_ret_sql as $val) {
				$issue_ret_ids[$val[csf("id")]] = $val[csf("id")];
			}
			unset($issue_ret_sql);

			$issue_ret_ids = implode(",", array_filter($issue_ret_ids));

			if ($issue_ret_ids) {
				//Receive Return From Issue Return
				$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty
					from  inv_issue_master a, inv_transaction b
					where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1
					and b.status_active = 1 and b.is_deleted = 0 and a.received_id in ($issue_ret_ids) and b.prod_id in ($prod_ids)");
				foreach ($rcvReturnQntyFromIssueRetArr as $val) {
					$rcvReturnQnty += $val[csf("return_qnty")];
				}
			}
		}
	}

	foreach ($rcvDtlsResult as $row) {
		//for fabric booking no
		$color_id = $row[csf('color')];
		$yarn_count_id = $row[csf("yarn_count_id")];
		$job_no = $row[csf("job_no")];
		$YarnDyeingMst_id = $row[csf("pi_wo_batch_no")];
		$data_str = "'" . $receive_basis . '_' . $rcvPurpose . '_' . $booking_id . "'";

		$over_receive_limit = (!empty($variable_set_invent)) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
		echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
		echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
		echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
		echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
		if ($row[csf("yarn_comp_percent2nd")] == 0) $row[csf("yarn_comp_percent2nd")] = "";
		echo "$('#percentage2').val('" . $row[csf("yarn_comp_percent2nd")] . "');\n";
		echo "control_composition('percent_one');\n";
		echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
		echo "rate_cond(" . $row[csf("receive_purpose")] . ");\n";
		echo "$('#txt_yarn_lot').val('" . $row[csf("lot")] . "');\n";
		echo "$('#txt_brand').val('" . $brand_name . "');\n";
		echo "$('#txt_receive_qty').val(" . $row[csf("order_qnty")] . ");\n";
		echo "$('#hdn_receive_qty').val(" . $row[csf("order_qnty")] . ");\n";
		echo "$('#txt_grey_qty').val(" . $row[csf("grey_quantity")] . ");\n";
		echo "$('#hdn_grey_qty').val(" . $row[csf("grey_quantity")] . ");\n";
		echo "$('#txt_rate').val(" . $row[csf("order_rate")] . ");\n";
		echo "$('#txt_avg_rate').val(" . $row[csf("cons_avg_rate")] . ");\n";
		echo "$('#txt_dyeing_charge').val(" . $row[csf("dye_charge")] . ");\n";
		echo "$('#txt_ile').val(" . $row[csf("order_ile_cost")] . ");\n";
		echo "$('#cbo_uom').val(" . $row[csf("order_uom")] . ");\n";
		echo "$('#txt_amount').val(" . $row[csf("order_amount")] . ");\n";
		echo "$('#txt_book_currency').val(" . $row[csf("cons_amount")] . ");\n";
		echo "$('#txt_order_qty').val(0);\n";
		echo "$('#txt_no_bag').val(" . $row[csf("no_of_bags")] . ");\n";
		echo "$('#txt_cone_per_bag').val(" . $row[csf("cone_per_bag")] . ");\n";
		echo "$('#txt_no_loose_cone').val(" . $row[csf("no_loose_cone")] . ");\n";
		echo "$('#txt_weight_per_bag').val(" . $row[csf("weight_per_bag")] . ");\n";
		echo "$('#txt_weight_per_cone').val(" . $row[csf("weight_per_cone")] . ");\n";
		echo "$('#txt_prod_code').val(" . $row[csf("prod_id")] . ");\n";
		echo "$('#job_no').val('" . $row[csf("job_no")] . "');\n";

		echo "load_drop_down( 'requires/yarn_receive_controller', $data_str,'load_drop_down_color', 'color_td_id' );\n";

		echo "$('#cbo_color').val(" . $color_id . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'floor','floor_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "',this.value);\n";

		echo "$('#cbo_floor').val(" . $row[csf("floor_id")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'room','room_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "',this.value);\n";

		echo "$('#cbo_room').val(" . $row[csf("room")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'rack','rack_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "',this.value);\n";

		echo "$('#txt_rack').val(" . $row[csf("rack")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'shelf','shelf_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "','" . $row[csf('rack')] . "',this.value);\n";

		echo "$('#txt_shelf').val(" . $row[csf("self")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'bin','bin_td', '" . $row[csf('company_id')] . "','" . "','" . $row[csf('store_id')] . "','" . $row[csf('floor_id')] . "','" . $row[csf('room')] . "','" . $row[csf('rack')] . "','" . $row[csf('self')] . "',this.value);\n";

		echo "$('#cbo_bin').val(" . $row[csf("bin_box")] . ");\n";
		//echo "$('#txt_remarks').val(".$row[csf("remarks")].");\n";
		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";
		echo "$('#cbo_buyer_name').val('" . $row[csf("buyer_id")] . "');\n";
		//update id here

		echo "$('#update_id').val(" . $row[csf("id")] . ");\n";
		echo "$('#txt_wo_pi_dtls_id').val(" . $row[csf("pi_wo_req_dtls_id")] . ");\n";
		$over_receive_limit_qnty = ($over_receive_limit > 0) ? ($over_receive_limit / 100) * $wo_po_qnty : 0;
		$orderQnty = $wo_po_qnty - ($totalRcvQnty - $rcvReturnQnty);
		$actual_rcv = ($updateRcvQnty - $rcvReturnQnty);
		echo "$('#txt_over_recv_limt').val(" . $over_receive_limit_qnty . ");\n";
		echo "$('#txt_order_qty').val(" . $orderQnty . ");\n";
		echo "$('#txt_woQnty').val(" . $wo_po_qnty . ");\n";
		echo "$('#txt_overRecPerc').val(" . $over_receive_limit . ");\n";
		echo "$('#txt_totRecv').val(" . $actual_rcv . ");\n";

		/*if ($rcvPurpose == 16) {
			echo "$('#cbo_color').attr('disabled','disabled');\n"; 
		}*/


		if ($receive_basis != 4 || $receive_basis != 14) {
			echo "$('#cbo_color').attr('disabled','disabled');\n";
		}

		echo "set_button_status(1, permission, 'fnc_yarn_receive_entry',1,1);\n";
		echo "fn_calile();\n";
		echo "storeUpdateUptoDisable();\n";
		echo "disable_enable_fields( 'cbo_yarn_count*cbocomposition1*percentage1*cbo_yarn_type*txt_yarn_lot*txt_rate', 1, '', '' );\n";
	}

	//$rcv_dtls_id

	//for order wise qty
	$sql = "SELECT po_breakdown_id, quantity,grey_prod_id FROM order_wise_pro_details WHERE trans_type = 1 AND entry_form=1 AND status_active = 1 AND is_deleted = 0 AND trans_id = " . $rcv_dtls_id . "";

	$rslt = sql_select($sql);
	$orderQtyString = '';
	foreach ($rslt as $row) {
		if ($orderQtyString != '') {
			$orderQtyString .= ",";
		}

		$po_id = $row[csf('po_breakdown_id')];

		$greyProdIDs = str_replace(",", "**", $row[csf('grey_prod_id')]);
		$orderQtyString .= $po_id . "_" . number_format($row[csf('quantity')], 2, '.', '') . "_" . number_format($row[csf('quantity')], 2, '.', '') . "_" . $greyProdIDs;
		$grey_prod_id = $row[csf('grey_prod_id')];
	}

	echo "$('#hdnReceiveString').val('" . $orderQtyString . "');\n";
	echo "$('#hdnOldReceiveString').val('" . $orderQtyString . "');\n";
	echo "$('#txt_grey_yarn_prod_id').val('" . $grey_prod_id . "');\n";

	if ($receive_basis == 2 && ($rcvPurpose == 2 || $rcvPurpose == 7 || $rcvPurpose == 12 || $rcvPurpose == 15 || $rcvPurpose == 38 || $rcvPurpose == 44  || $rcvPurpose == 46 || $rcvPurpose == 50 || $rcvPurpose == 51)) //2,7,12,15,38,46,50,51
	{
		if ($po_id == "") {
			echo "$('#txt_receive_qty').removeAttr('onclick','func_onclick_qty()').attr({'onblur':'fn_calile()','readonly':false})\n";
		} else {
			echo "$('#txt_receive_qty').attr('onclick','func_onclick_qty()');\n";
		}

		echo "$('#txt_dyeing_charge').attr('disabled','disabled');\n";
	} else {
		echo "$('#txt_receive_qty').removeAttr('onclick','func_onclick_qty()').attr({'onblur':'fn_calile()','readonly':false})\n";
		echo "$('#txt_dyeing_charge').removeAttr('disabled');\n";
	}

	if ($rcvPurpose == 15 || $rcvPurpose == 50 || $rcvPurpose == 51) {
		//for fabric booking no
		$sqlYarnDyeing = "SELECT b.dtls_id AS ID FROM wo_yarn_dyeing_dtls a, wo_yarn_dyeing_dtls_fin_prod b  WHERE a.mst_id=b.mst_id and a.mst_id = " . $YarnDyeingMst_id . " AND a.job_no = '" . $job_no . "'  AND b.yarn_count = '" . $yarn_count_id . "' AND b.yarn_color = " . $color_id . "";
	} else {
		//for fabric booking no
		$sqlYarnDyeing = "SELECT id AS ID FROM wo_yarn_dyeing_dtls WHERE mst_id = " . $YarnDyeingMst_id . " AND job_no = '" . $job_no . "' AND count = '" . $yarn_count_id . "' AND yarn_color = " . $color_id . "";
	}

	$sqlYarnDyeingRslt = sql_select($sqlYarnDyeing);
	$yarnDyeingDtlsId = '';
	foreach ($sqlYarnDyeingRslt as $row) {
		$yarnDyeingDtlsId = $row['ID'];
	}
	echo "$('#hdnYarnDyingDtlsId').val('" . $yarnDyeingDtlsId . "');\n";


	//== Veriable setting Yarn Services Process Loss Start
	$vs_sqlResult = sql_select("select category as service_type,over_rcv_percent as process_percentage,over_rcv_payment as process_control_status from variable_inv_ile_standard where company_name='$company_id' and variable_list=22 and category=" . $rcvDtlsResult[0][csf("receive_purpose")] . " order by id");

	if (!empty($vs_sqlResult)) // Yarn service process loss Variable setting
	{
		if ($greyProdIDs != "") {
			$grey_prod_avg_rate = return_field_value("(sum(cons_amount)/ sum(cons_quantity)) as order_rate", "inv_transaction", "status_active=1 and is_deleted=0 and prod_id in($greyProdIDs) and item_category=1 and transaction_type in(1,5)", "order_rate");

			echo "$('#hidden_grey_yarn_avg_rate').val(" . $grey_prod_avg_rate . ");\n";
		}

		$purpose = $rcvDtlsResult[0][csf("receive_purpose")];
		$vs_service_type = $vs_sqlResult[0][csf('service_type')];
		$vs_process_percentage = $vs_sqlResult[0][csf('process_percentage')];
		$vs_process_control_status = $vs_sqlResult[0][csf('process_control_status')];

		if ($vs_service_type == $purpose && $vs_process_control_status == 1 && $vs_process_percentage > 0) {
			echo "$('#txt_grey_qty').attr('disabled','disabled');\n";
			echo "$('#hdn_service_process_loss_percentage').val(" . $vs_process_percentage . ");\n";
		} else {
			echo "$('#txt_grey_qty').removeAttr('disabled','disabled');\n";
			echo "$('#hdn_service_process_loss_percentage').val(0);\n";
		}
	} else {
		echo "$('#txt_grey_qty').removeAttr('disabled','disabled');\n";
		echo "$('#hdn_service_process_loss_percentage').val(0);\n";
	}
	//== Veriable setting Yarn Services Process Loss End

	exit();
}

if ($action == "issue_challan_popup_info") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);

	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
?>

	<script>
		function js_set_value(data) {
			var splitArr = data.split("_");
			$("#hidden_issue_id").val(splitArr[0]); // id number
			$("#hidden_challan_number").val(splitArr[1]); // mrr number
			parent.emailwindow.hide();
		}

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search', -1);
		});
	</script>

	</head>

	<body>
		<div align="center" style="width:100%; margin-top:10px">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<input type="hidden" name="hidden_issue_id" id="hidden_issue_id">
				<input type="hidden" name="hidden_challan_number" id="hidden_challan_number">
				<table width="630" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="130">Issue System Id</th>
						<th width="130">Challan No</th>
						<th width="140">Issue To</th>
						<th>Yarn Count</th>
					</thead>
				</table>
				<div style="width:630px; overflow-y: scroll; max-height:320px;" id="scroll_body">
					<table width="612" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
						<?
						$i = 1;
						if ($db_type == 0) {
							$sql = "select a.id, a.issue_number, a.challan_no, a.knit_dye_company, group_concat(distinct(c.yarn_count_id)) as yarn_count_id from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=2 and a.knit_dye_source=3 and a.knit_dye_company='$supplier' and a.issue_purpose=15 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 group by a.id";
						} else {
							$sql = "select a.id, a.issue_number, a.challan_no, a.knit_dye_company, LISTAGG(c.yarn_count_id, ',') WITHIN GROUP (ORDER BY c.yarn_count_id) as yarn_count_id from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=2 and a.knit_dye_source=3 and a.knit_dye_company='$supplier' and a.issue_purpose=15 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 group by a.id, a.issue_number, a.challan_no, a.knit_dye_company";
						}
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							$data = $row[csf('id')] . "_" . $row[csf('challan_no')];

							$issue_to = $supplier_library[$row[csf('knit_dye_company')]];

							$yarn_count_id = array_unique(explode(",", $row[csf('yarn_count_id')]));
							$yarn_count = '';
							foreach ($yarn_count_id as $count_id) {
								if ($yarn_count == "") $yarn_count = $yarn_count_arr[$count_id];
								else $yarn_count .= "," . $yarn_count_arr[$count_id];
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>');">
								<td width="40"><? echo $i; ?></td>
								<td width="130">
									<p>&nbsp;<? echo $row[csf('issue_number')]; ?></p>
								</td>
								<td width="130">
									<p><? echo $row[csf('challan_no')]; ?>&nbsp;</p>
								</td>
								<td width="140">
									<p><? echo $issue_to; ?>&nbsp;</p>
								</td>
								<td>
									<p><? echo $yarn_count; ?>&nbsp;</p>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
					</table>
				</div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?

	exit;
}

if ($action == "is_allocation_maintained") {
	$allocation_maintained = return_field_value("allocation", "variable_settings_inventory", "company_name =$data and variable_list=18 and item_category_id=1 and is_deleted=0 and status_active=1");
	if ($allocation_maintained != 1) $allocation_maintained = 0;
	echo "document.getElementById('allocation_maintained').value 	= '" . $allocation_maintained . "';\n";
	$variable_rcv_level = sql_select("select id, user_given_code_status from variable_settings_inventory where company_name='$data' and variable_list='31' and status_active=1 and is_deleted=0");
	echo "document.getElementById('variable_recv_level').value 	= '" . $variable_rcv_level[0][csf("user_given_code_status")] . "';\n";
	exit();
}

//################################################# function Here #########################################//

//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor)
{
	$rate_ile = $rate + $ile_cost;
	$rate_ile_exchange = $rate_ile * $exchange_rate;
	$doemstic_rate = $rate_ile_exchange / $conversion_factor;
	return $doemstic_rate;
}

//return product master table id ----------------------------------------//
function return_product_id($yarncount, $composition_one, $composition_two, $percentage_one, $percentage_two, $yarntype, $color, $yarnlot, $prodCode, $company, $supplier, $store, $uom, $yarn_type, $composition, $cbo_receive_purpose, $hdnPayMode)
{

	$composition_one = str_replace("'", "", $composition_one);
	$composition_two = str_replace("'", "", $composition_two);
	$percentage_one = str_replace("'", "", $percentage_one);
	$percentage_two = str_replace("'", "", $percentage_two);
	$yarntype = str_replace("'", "", $yarntype);
	$color = str_replace("'", "", $color);
	$yarncount = str_replace("'", "", $yarncount);
	if ($percentage_one == "") $percentage_one = 0;
	if ($percentage_two == "") $percentage_two = 0;
	$cbo_receive_purpose = str_replace("'", "", $cbo_receive_purpose);
	if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 44 || $cbo_receive_purpose == 43 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) $dyed_type = 1;
	else $dyed_type = 2;
	if ($cbo_receive_purpose == 15) $is_twisted = 1;
	else $is_twisted = 0;

	//for pay mode
	$payMode = str_replace("'", "", $hdnPayMode);
	$is_within_group = 0;
	if ($payMode == 3 || $payMode == 5) {
		$is_within_group = 1;
	}

	//NOTE :- Yarn category array ID=1
	$whereCondition = "yarn_count_id=$yarncount and yarn_comp_type1st=$composition_one and yarn_comp_percent1st=$percentage_one and yarn_comp_type2nd=$composition_two and yarn_comp_percent2nd=$percentage_two and yarn_type=$yarntype and color=$color and company_id=$company and supplier_id=$supplier and item_category_id=1 and lot=$yarnlot and dyed_type=$dyed_type and status_active=1 and is_deleted=0"; //and store_id=$store
	$prodMSTID = return_field_value("id", "product_details_master", "$whereCondition");
	//return "select id from product_details_master where $whereCondition";die;
	$insertResult = true;
	if ($prodMSTID == false || $prodMSTID == "") {
		// new product create here--------------------------//
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

		$compositionPart = $composition[$composition_one] . " " . $percentage_one;
		if ($percentage_two != 0) {
			$compositionPart .= " " . $composition[$composition_two] . " " . $percentage_two;
		}

		//$yarn_count.','.$composition.','.$ytype.','.$color;
		$product_name_details = $yarn_count_arr[$yarncount] . " " . $compositionPart . " " . $yarn_type[$yarntype] . " " . $color_name_arr[$color];
		$product_name_details = str_replace(array("\r", "\n"), '', $product_name_details);

		$prodMSTID = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		$field_array = "id,company_id,supplier_id,item_category_id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color,dyed_type,inserted_by,insert_date,is_twisted,is_within_group";
		$data_array = "(" . $prodMSTID . "," . $company . "," . $supplier . ",1,'" . $product_name_details . "'," . $yarnlot . "," . $prodCode . "," . $uom . "," . $yarncount . "," . $composition_one . "," . $percentage_one . "," . $composition_two . "," . $percentage_two . "," . $yarntype . "," . $color . ",'" . $dyed_type . "','" . $user_id . "','" . $pc_date_time . "'," . $is_twisted . "," . $is_within_group . ")";
		//echo $field_array."<br>".$data_array."--".$product_name_details;die;
		$insertResult = false;
		//$insertResult = sql_insert("product_details_master",$field_array,$data_array,1);
	}
	if ($insertResult == true) {
		return $insertResult . "***" . $prodMSTID;
	} else {
		return $insertResult . "***" . $field_array . "***" . $data_array . "***" . $prodMSTID;
	}
}

if ($action == "yarn_receive_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = " select id, recv_number,supplier_id,currency_id,challan_no, receive_date, exchange_rate, store_id, receive_basis,lc_no,receive_purpose,booking_id, challan_date, gate_entry_no, gate_entry_date, boe_mushak_challan_no,boe_mushak_challan_date from inv_receive_master where recv_number='$data[1]'";


	$dataArray = sql_select($sql);
	$receive_pur = $dataArray[0][csf("receive_purpose")];
	$receive_basis = $dataArray[0][csf("receive_basis")];

	$wo_id = $dataArray[0][csf("booking_id")];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location = return_field_value("location_name", "lib_location", "company_id=$data[0]");
	$address = return_field_value("address", "lib_location", "company_id=$data[0]");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$supplier_address_library = return_library_array("select id,ADDRESS_1 from  lib_supplier", "id", "ADDRESS_1");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$const_comp_arr = return_library_array("select id,const_comp from lib_subcon_charge", 'id', 'const_comp');
	$lcNum = return_library_array("select id,lc_number from com_btb_lc_master_details", 'id', 'lc_number');
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');

	$wo_service_type = array(2, 7, 12, 15, 38, 44, 46, 50, 51);
	$exchange_currency = 0;
	if (in_array($receive_pur, $wo_service_type)) {
		$table_width = 1615;
		$exchange_currency = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$wo_id", "ecchange_rate");
	} else {
		$table_width = 1130;
	}

	if ($receive_basis == 2 && ($receive_pur == 2 || $receive_pur == 12 || $receive_pur == 15 || $receive_pur == 38 || $receive_pur == 44 || $receive_pur == 46 || $receive_pur == 50 || $receive_pur == 51)) {

		$pay_mode = return_field_value("pay_mode", "wo_yarn_dyeing_mst", "id=$wo_id", "pay_mode");

		if ($pay_mode == 3 || $pay_mode == 5) {
			$supplier_name = $company_library[$dataArray[0][csf('supplier_id')]];
			$com_nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dataArray[0][csf('supplier_id')]");
			foreach ($com_nameArray as $result) {
				$supplier_adress =  $result[csf('plot_no')] . $result[csf('level_no')] . $result[csf('road_no')] . $result[csf('block_no')] . $result[csf('city')] . $result[csf('zip_code')];
			}
		} else {
			$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
			$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
		}
	} else {
		$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
		$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
	}

?>
	<div id="table_row" style="width:<? echo $table_width; ?>px;">
		<table width="<? echo $table_width; ?>" align="right">
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result) {
					?>
						<? echo $result[csf('plot_no')]; ?>
						<? echo $result[csf('level_no')] ?>
						<? echo $result[csf('road_no')]; ?>
						<? echo $result[csf('block_no')]; ?>
						<? echo $result[csf('city')]; ?>
						<? echo $result[csf('zip_code')]; ?>
						<?php echo $result[csf('province')]; ?>
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? echo $result[csf('email')]; ?>
					<? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Material Receive
							Report</u></strong></center>
				</td>
			</tr>
			<tr style="font-size:14px">
				<td width="120" valign='top'><strong>Supplier Name:</strong></td>
				<td width="210px">
					<? echo $supplier_name . '<br>' . $supplier_adress; ?>
				</td>
				<td width="110"><strong>MRR No:</strong></td>
				<td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="120"><strong>Currency:</strong></td>
				<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			</tr>
			<tr style="font-size:14px">
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Receive Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<? if ($exchange_currency > 0) {
				?>
					<td><strong>WO Exc. Rate:</strong></td>
					<td><? echo $exchange_currency; ?></td>
				<?
				} else {
				?>
					<td><strong>Exchange Rate:</strong></td>
					<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<?
				}
				?>
			</tr>
			<tr style="font-size:14px">
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Receive Basis:</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<?
				if ($dataArray[0][csf('receive_basis')] == 1) {
				?>
					<td><strong>LC NO:</strong></td>
					<td><? echo $lcNum[$dataArray[0][csf('lc_no')]]; ?></td>
				<?
				}
				if ($receive_pur == 2) $rate_text = "Avg. Rate BDT";
				else $rate_text = "Rate";
				?>

			</tr>
			<tr>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Gate Entry No:</strong></td>
				<td><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Gate Entry Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
			</tr>
			<tr>
				<td width="300"><strong>BOE/Mushak Challan No:</strong></td>
				<td width="220"><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
				<td width="300"><strong>BOE/Mushak Challan Date:</strong></td>
				<td width="220"><? echo change_date_format($dataArray[0][csf('boe_mushak_challan_date')]); ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="100">WO/PI No</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="140">Item Details</th>
						<?
						if ($receive_basis == 2 && $receive_pur == 2) {
						?>
							<th rowspan="2" width="100">Color Range</th>
						<?
						}
						?>
						<th rowspan="2" width="60">Yarn Lot</th>
						<th rowspan="2" width="40">UOM</th>
						<? if ($receive_pur == 2) { ?>
							<th rowspan="2" width="60">Grey Qty</th>
						<? } ?>

						<th rowspan="2" width="60">Receive Qty</th>
						<th rowspan="2" width="50"><? echo $rate_text; ?></th>
						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<th rowspan="2" width="60">Avg. Rate Currency</th>
							<th rowspan="2" width="60">Grey Rate BDT</th>
							<th rowspan="2" width="60">Grey Rate Currency</th>
							<th rowspan="2" width="60">Dye. Charge BDT</th>
							<th rowspan="2" width="60">Dye. Charge Currency</th>
							<? if ($receive_pur == 2) { ?>
								<th rowspan="2" width="60">Dye. Amount(BDT)</th>
							<? } ?>
						<?
						}
						?>
						<th rowspan="2" width="60">ILE Cost</th>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {
						?>
							<th colspan="2" width="120">Amount(<? echo $currency[$dataArray[0][csf('currency_id')]]; ?>)</th>
						<?
						}
						?>
						<th colspan="2" width="150">Amount(BDT)</th>
						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<th rowspan="2" width="80">Amount Currency</th>
						<?
						}
						?>
						<th rowspan="2" width="50">No. Of Bag</th>
						<th rowspan="2" width="50">No. Cons Per Bag</th>
						<th rowspan="2" width="50">No. Of Loose Cone</th>
						<th rowspan="2" width="100">Floor</th>
						<th rowspan="2" width="100">Room</th>
						<th rowspan="2">Remarks </th>
					</tr>
					<tr>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {
						?>
							<th width="50">With ILE</th>
							<th width="50">Without ILE</th>
						<? } ?>
						<th width="50">With ILE</th>
						<th width="50">Without ILE</th>
					</tr>
				</thead>
				<?
				if ($db_type == 0) $wo_no_cond = " group_concat(b.work_order_no)";
				else if ($db_type == 2) $wo_no_cond = "LISTAGG(b.work_order_no, ',') WITHIN GROUP (ORDER BY b.work_order_no)";
				$pi_arr = array();
				$pi_sql = "select a.id, a.pi_number, a.pi_basis_id, $wo_no_cond as work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 group by a.id, a.pi_number, a.pi_basis_id";
				$pi_sql_res = sql_select($pi_sql);
				foreach ($pi_sql_res as $row) {
					if ($row[csf('pi_basis_id')] == 1) $wowoderno = implode(',', array_unique(explode(',', $row[csf('work_order_no')])));
					else if ($row[csf('pi_basis_id')] == 2) $wowoderno = "Independent";
					else $wowoderno = "";
					$pi_arr[$row[csf('id')]]['pi_number'] = $row[csf('pi_number')];
					$pi_arr[$row[csf('id')]]['work_order'] = $wowoderno;
				}

				$wo_library = return_library_array("select id,wo_number from wo_non_order_info_mst where entry_form=144", "id", "wo_number");

				//$wo_yrn_library = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst", "id", "ydw_no");

				if ($wo_id != "") {
					$wo_yarn_sql = "select a.id,a.ydw_no,b.count,b.yarn_color, b.color_range from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.id=$wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					$wo_yarn_data = sql_select($wo_yarn_sql);
					$wo_yarn_data_array = array();
					foreach ($wo_yarn_data as $row) {
						$wo_yarn_data_array[$row[csf('id')]][$row[csf('count')]][$row[csf('yarn_color')]]['color_range'] = $row[csf('color_range')];
						$wo_yrn_library[$row[csf('id')]] = $row[csf('ydw_no')];
					}
				}

				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$cond = "";
				if ($data[1] != "") $cond .= " and a.recv_number='$data[1]'";

				$i = 1;

				$sql_result = sql_select("SELECT a.recv_number, a.receive_basis, a.receive_purpose, b.id, b.receive_basis, b.pi_wo_batch_no, b.cone_per_bag, c.product_name_details, c.lot,c.yarn_count_id,c.color, b.order_uom, b.order_qnty, b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags,b.no_loose_cone, b.remarks,b.buyer_id,b.booking_no,a.audit_by, a.audit_date, a.is_audited, b.floor_id, b.room, b.garments_qty, b.grey_quantity
				from inv_receive_master a, inv_transaction b,  product_details_master c
				where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond");

				$total_amt_currency = 0;
				$total_grey_qty = 0;
				$total_dyed_charge = 0;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					//order amount calculation here
					$qty = number_format($row[csf("order_qnty")], 2, '.', '') * 1;
					$rate = $row[csf("order_rate")] * 1;
					$ileCost = $row[csf("order_ile_cost")] * 1;
					$row[csf("order_amount")] = $qty * ($rate + $ileCost);
					//end

					$order_qnty_val_sum += $row[csf('order_qnty')];
					$order_amount_val_sum += $row[csf('order_amount')];
					$order_amount_val_without_ile_sum += $row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')]);
					$no_of_bags_val_sum += $row[csf('no_of_bags')];
					$con_per_bags_sum += $row[csf('cone_per_bag')];
					$no_of_loose_cone += $row[csf('no_loose_cone')];

					if ($row[csf("receive_basis")] == 1)
						$receive_basis_cond = $pi_arr[$row[csf('pi_wo_batch_no')]]['pi_number'] . '<br><i>' . $pi_arr[$row[csf('pi_wo_batch_no')]]['work_order'] . '</i>';
					else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 7 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 44 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51))
						$receive_basis_cond = $wo_yrn_library[$row[csf('pi_wo_batch_no')]];
					else if ($row[csf("receive_basis")] == 14) {
						$receive_basis_cond = $row[csf('booking_no')];
					} else
						$receive_basis_cond = $wo_library[$row[csf('pi_wo_batch_no')]];

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td><? echo $i; ?></td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $receive_basis_cond; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:140px"><? echo $row[csf('product_name_details')]; ?></div>
						</td>

						<?
						if ($receive_basis == 2 && $receive_pur == 2) {
							$color_range_id = $wo_yarn_data_array[$row[csf('pi_wo_batch_no')]][$row[csf('yarn_count_id')]][$row[csf('color')]]['color_range'];
						?>
							<td style="word-wrap:break-word; width:140px"><? echo $color_range[$color_range_id]; ?></td>
						<?
						}
						?>

						<td><? echo $row[csf('lot')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<? if ($receive_pur == 2) { ?>
							<td align="right"><?
												echo number_format($row[csf('grey_quantity')], 2, '.', ',');
												$total_grey_qty += $row[csf('grey_quantity')];
												?></td>
						<? } ?>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($row[csf('order_rate')], 2, '.', ','); ?></td>
						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<td align="right"><? echo number_format(($row[csf('order_rate')] / $exchange_currency), 4, '.', ','); ?></td>
							<td align="right"><? echo number_format($row[csf('cons_avg_rate')], 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('cons_avg_rate')] / $exchange_currency), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')]), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')] / $exchange_currency), 2, '.', ','); ?></td>
						<?
						}
						if ($receive_pur == 2) { ?>
							<td align="right"><?
												echo number_format(($row[csf('dye_charge')] * $row[csf('grey_quantity')]), 2, '.', ',');
												$total_dyed_charge += ($row[csf('dye_charge')] * $row[csf('grey_quantity')]);
												?></td>
						<?
						}
						?>
						<td align="right"><? echo $row[csf('order_ile_cost')]; ?></td>
						<? if ($dataArray[0][csf('currency_id')] != 1) {
						?>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')]), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')]); ?>
							</td>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])); ?>
							</td>
						<? }
						?>
						<td align="right">
							<? echo number_format(($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency += ($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]); ?>
						</td>
						<td align="right">
							<? echo number_format((($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency_without_ile += (($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]); ?>
						</td>

						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<td align="right"><? echo number_format(($row[csf('order_amount')] / $exchange_currency), 2, '.', ',');
												$total_amt_currency += ($row[csf('order_amount')] / $exchange_currency); ?></td>

						<?
						}
						?>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo $row[csf('no_loose_cone')]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
				<?php
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="<? echo $colspan = ($receive_basis == 2 && $receive_pur == 2) ? 7 : 6; ?>">Total :</td>
					<? if ($receive_pur == 2) { ?>
						<td align="right"><? echo number_format($total_grey_qty, 2, '.', ','); ?></td>
					<? } ?>


					<td align="right"><? echo number_format($order_qnty_val_sum, 2, '.', ','); ?></td>

					<td align="right">&nbsp;</td>
					<?
					if (in_array($receive_pur, $wo_service_type)) {
					?>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<? if ($receive_pur == 2) { ?>
							<td align="right"><? echo number_format($total_dyed_charge, 2, '.', ','); ?></td>
						<? } ?>
					<?

					}

					if ($dataArray[0][csf('currency_id')] == 1 ||  in_array($receive_pur, $wo_service_type)) {
						$colspan = 2;
					} else {
						$colspan = 1;
					}

					if ($dataArray[0][csf('currency_id')] != 1) {
					?>
						<td align="right" colspan="2"><? echo number_format($order_amount_val_sum, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_amount_val_without_ile_sum, 2, '.', ','); ?></td>
					<?
					}
					?>
					<td align="right" colspan="<? echo $colspan ?>"><? echo number_format($total_bdt_amt_currency, 2, '.', ','); ?></td>
					<td align="right"><? echo number_format($total_bdt_amt_currency_without_ile, 2, '.', ','); ?></td>
					<?
					if (in_array($receive_pur, $wo_service_type)) {
					?>
						<td align="right"><? //echo number_format($total_amt_currency,2,'.',',') 
											?></td>
					<?
					}
					?>
					<td align="right"><? echo $no_of_bags_val_sum; ?></td>
					<td align="right"><? echo $con_per_bags_sum; ?></td>
					<td align="right"><? echo $no_of_loose_cone; ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
				</tr>

			</table>
			<table>
				<tr>
					<?php

					if ($sql_result[0][csf("is_audited")] == 1) {
					?>
						<td><? echo 'Audited By &nbsp;' . $user_name[$sql_result[0][csf("audit_by")]] . '&nbsp;' . $sql_result[0][csf("audit_date")]; ?></td>
					<?php
					}
					?>


				</tr>
			</table>
			<br>
			<?
			echo signature_table(65, $data[0], $table_width . "px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "yarn_receive_print2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = " select id, recv_number,supplier_id,currency_id,challan_no, receive_date, exchange_rate, store_id, receive_basis,lc_no,receive_purpose,booking_id, challan_date, gate_entry_no, gate_entry_date, boe_mushak_challan_no,boe_mushak_challan_date, booking_no from inv_receive_master where recv_number='$data[1]'";


	$dataArray = sql_select($sql);
	$receive_pur = $dataArray[0][csf("receive_purpose")];
	$receive_basis = $dataArray[0][csf("receive_basis")];
	// $wo_no = $dataArray[0][csf("booking_no")];	

	$wo_id = $dataArray[0][csf("booking_id")];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location = return_field_value("location_name", "lib_location", "company_id=$data[0]");
	$address = return_field_value("address", "lib_location", "company_id=$data[0]");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$supplier_address_library = return_library_array("select id,ADDRESS_1 from  lib_supplier", "id", "ADDRESS_1");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$const_comp_arr = return_library_array("select id,const_comp from lib_subcon_charge", 'id', 'const_comp');
	$lcNum = return_library_array("select id,lc_number from com_btb_lc_master_details", 'id', 'lc_number');
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');

	$wo_service_type = array(2, 7, 12, 15, 38, 46, 50, 51);
	$exchange_currency = 0;
	if (in_array($receive_pur, $wo_service_type)) {
		$table_width = 1615;
		$exchange_currency = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$wo_id", "ecchange_rate");
	} else {
		$table_width = 1130;
	}

	if ($receive_basis == 2 && ($receive_pur == 2 || $receive_pur == 12 || $receive_pur == 15 || $receive_pur == 38 || $receive_pur == 44 || $receive_pur == 46 || $receive_pur == 50 || $receive_pur == 51)) {

		$pay_mode = return_field_value("pay_mode", "wo_yarn_dyeing_mst", "id=$wo_id", "pay_mode");

		if ($pay_mode == 3 || $pay_mode == 5) {
			$supplier_name = $company_library[$dataArray[0][csf('supplier_id')]];
			$com_nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dataArray[0][csf('supplier_id')]");
			foreach ($com_nameArray as $result) {
				$supplier_adress =  $result[csf('plot_no')] . $result[csf('level_no')] . $result[csf('road_no')] . $result[csf('block_no')] . $result[csf('city')] . $result[csf('zip_code')];
			}
		} else {
			$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
			$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
		}
	} else {
		$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
		$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
	}

	if ($receive_basis == 2) {

		$pi_sql = sql_select("select a.PI_NUMBER, b.PI_ID from COM_PI_MASTER_DETAILS a, COM_PI_ITEM_DETAILS b where b.pi_id = a.id and b.WORK_ORDER_ID = $wo_id");
		$pi_no = $pi_sql[0]['PI_NUMBER'];
		$pi_id = $pi_sql[0]['PI_ID'];
		$lc_sql = sql_select("select a.LC_NUMBER from COM_BTB_LC_MASTER_DETAILS a, COM_BTB_LC_PI b where a.id = b.COM_BTB_LC_MASTER_DETAILS_ID and b.PI_ID = $pi_id");
		$lc_no = $lc_sql[0]['LC_NUMBER'];
	}

?>
	<div id="table_row" style="width:<? echo $table_width; ?>px;">
		<table width="<? echo $table_width; ?>" align="right">
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result) {
					?>
						<? echo $result[csf('plot_no')]; ?>
						<? echo $result[csf('level_no')] ?>
						<? echo $result[csf('road_no')]; ?>
						<? echo $result[csf('block_no')]; ?>
						<? echo $result[csf('city')]; ?>
						<? echo $result[csf('zip_code')]; ?>
						<?php echo $result[csf('province')]; ?>
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? echo $result[csf('email')]; ?>
					<? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Material Receive
							Report</u></strong></center>
				</td>
			</tr>
			<tr style="font-size:14px">
				<td width="120" valign='top'><strong>Supplier Name:</strong></td>
				<td width="210px">
					<? echo $supplier_name . '<br>' . $supplier_adress; ?>
				</td>
				<td width="110"><strong>MRR No:</strong></td>
				<td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="120"><strong>Currency:</strong></td>
				<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			</tr>
			<tr style="font-size:14px">
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Receive Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<? if ($exchange_currency > 0) {
				?>
					<td><strong>WO Exc. Rate:</strong></td>
					<td><? echo $exchange_currency; ?></td>
				<?
				} else {
				?>
					<td><strong>Exchange Rate:</strong></td>
					<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<?
				}
				?>
			</tr>
			<tr style="font-size:14px">
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Receive Basis:</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<?
				if ($dataArray[0][csf('receive_basis')] == 1) {
				?>
					<td><strong>LC NO:</strong></td>
					<td><? echo $lcNum[$dataArray[0][csf('lc_no')]]; ?></td>
				<?
				}
				if ($receive_pur == 2) $rate_text = "Avg. Rate BDT";
				else $rate_text = "Rate";
				?>

			</tr>
			<tr>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Gate Entry No:</strong></td>
				<td><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Gate Entry Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>PI No:</strong></td>
				<td><? echo $pi_no; ?></td>
				<td><strong>LC No:</strong></td>
				<td><? echo $lc_no; ?></td>
			</tr>
			<tr>
				<td width="300"><strong>BOE/Mushak Challan No:</strong></td>
				<td width="220"><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
				<td width="300"><strong>BOE/Mushak Challan Date:</strong></td>
				<td width="220"><? echo change_date_format($dataArray[0][csf('boe_mushak_challan_date')]); ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="100">WO/PI No</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="140">Item Details</th>
						<?
						if ($receive_basis == 2 && $receive_pur == 2) {
						?>
							<th rowspan="2" width="100">Color Range</th>
						<?
						}
						?>
						<th rowspan="2" width="60">Yarn Lot</th>
						<th rowspan="2" width="40">UOM</th>
						<th rowspan="2" width="60">Receive Qty</th>
						<th rowspan="2" width="50"><? echo $rate_text; ?></th>
						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<th rowspan="2" width="60">Avg. Rate Currency</th>
							<th rowspan="2" width="60">Grey Rate BDT</th>
							<th rowspan="2" width="60">Grey Rate Currency</th>
							<th rowspan="2" width="60">Dye. Charge BDT</th>
							<th rowspan="2" width="60">Dye. Charge Currency</th>
						<?
						}
						?>
						<th rowspan="2" width="60">ILE Cost</th>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {
						?>
							<th colspan="2" width="120">Amount(<? echo $currency[$dataArray[0][csf('currency_id')]]; ?>)</th>
						<?
						}
						?>
						<th colspan="2" width="150">Amount(BDT)</th>
						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<th rowspan="2" width="80">Amount Currency</th>
						<?
						}
						?>
						<th rowspan="2" width="50">No. Of Bag</th>
						<th rowspan="2" width="50">No. Cons Per Bag</th>
						<th rowspan="2" width="50">No. Of Loose Cone</th>
						<th rowspan="2" width="100">Floor</th>
						<th rowspan="2" width="100">Room</th>
						<th rowspan="2" width="100">Rack</th>
						<th rowspan="2" width="100">Shelf</th>
						<th rowspan="2">Remarks </th>
					</tr>
					<tr>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {
						?>
							<th width="50">With ILE</th>
							<th width="50">Without ILE</th>
						<? } ?>
						<th width="50">With ILE</th>
						<th width="50">Without ILE</th>
					</tr>
				</thead>
				<?
				if ($db_type == 0) $wo_no_cond = " group_concat(b.work_order_no)";
				else if ($db_type == 2) $wo_no_cond = "LISTAGG(b.work_order_no, ',') WITHIN GROUP (ORDER BY b.work_order_no)";
				$pi_arr = array();
				$pi_sql = "select a.id, a.pi_number, a.pi_basis_id, $wo_no_cond as work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 group by a.id, a.pi_number, a.pi_basis_id";
				$pi_sql_res = sql_select($pi_sql);
				foreach ($pi_sql_res as $row) {
					if ($row[csf('pi_basis_id')] == 1) $wowoderno = implode(',', array_unique(explode(',', $row[csf('work_order_no')])));
					else if ($row[csf('pi_basis_id')] == 2) $wowoderno = "Independent";
					else $wowoderno = "";
					$pi_arr[$row[csf('id')]]['pi_number'] = $row[csf('pi_number')];
					$pi_arr[$row[csf('id')]]['work_order'] = $wowoderno;
				}

				$wo_library = return_library_array("select id,wo_number from wo_non_order_info_mst where entry_form=144", "id", "wo_number");

				//$wo_yrn_library = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst", "id", "ydw_no");

				if ($wo_id != "") {
					$wo_yarn_sql = "select a.id,a.ydw_no,b.count,b.yarn_color, b.color_range from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.id=$wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					$wo_yarn_data = sql_select($wo_yarn_sql);
					$wo_yarn_data_array = array();
					foreach ($wo_yarn_data as $row) {
						$wo_yarn_data_array[$row[csf('id')]][$row[csf('count')]][$row[csf('yarn_color')]]['color_range'] = $row[csf('color_range')];
						$wo_yrn_library[$row[csf('id')]] = $row[csf('ydw_no')];
					}
				}

				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$cond = "";
				if ($data[1] != "") $cond .= " and a.recv_number='$data[1]'";

				$i = 1;


				$sql_result = sql_select("select a.recv_number, a.receive_basis, a.receive_purpose, b.id, b.receive_basis, b.pi_wo_batch_no, b.cone_per_bag, c.product_name_details, c.lot,c.yarn_count_id,c.color, b.order_uom, b.order_qnty, b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags,b.no_loose_cone, b.remarks,b.buyer_id,b.booking_no,a.audit_by, a.audit_date, a.is_audited, b.floor_id, b.room,b.rack,b.self
					from inv_receive_master a, inv_transaction b,  product_details_master c
					where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond");
				$total_amt_currency = 0;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					//order amount calculation here
					$qty = number_format($row[csf("order_qnty")], 2, '.', '') * 1;
					$rate = $row[csf("order_rate")] * 1;
					$ileCost = $row[csf("order_ile_cost")] * 1;
					$row[csf("order_amount")] = $qty * ($rate + $ileCost);
					//end

					$order_qnty_val_sum += $row[csf('order_qnty')];
					$order_amount_val_sum += $row[csf('order_amount')];
					$order_amount_val_without_ile_sum += $row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')]);
					$no_of_bags_val_sum += $row[csf('no_of_bags')];
					$con_per_bags_sum += $row[csf('cone_per_bag')];
					$no_of_loose_cone += $row[csf('no_loose_cone')];

					if ($row[csf("receive_basis")] == 1)
						$receive_basis_cond = $pi_arr[$row[csf('pi_wo_batch_no')]]['pi_number'] . '<br><i>' . $pi_arr[$row[csf('pi_wo_batch_no')]]['work_order'] . '</i>';
					else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 7 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 44 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51))
						$receive_basis_cond = $wo_yrn_library[$row[csf('pi_wo_batch_no')]];
					else if ($row[csf("receive_basis")] == 14) {
						$receive_basis_cond = $row[csf('booking_no')];
					} else
						$receive_basis_cond = $wo_library[$row[csf('pi_wo_batch_no')]];

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td><? echo $i; ?></td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $receive_basis_cond; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:140px"><? echo $row[csf('product_name_details')]; ?></div>
						</td>

						<?
						if ($receive_basis == 2 && $receive_pur == 2) {
							$color_range_id = $wo_yarn_data_array[$row[csf('pi_wo_batch_no')]][$row[csf('yarn_count_id')]][$row[csf('color')]]['color_range'];
						?>
							<td style="word-wrap:break-word; width:140px"><? echo $color_range[$color_range_id]; ?></td>
						<?
						}
						?>

						<td><? echo $row[csf('lot')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($row[csf('order_rate')], 2, '.', ','); ?></td>
						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<td align="right"><? echo number_format(($row[csf('order_rate')] / $exchange_currency), 4, '.', ','); ?></td>
							<td align="right"><? echo number_format($row[csf('cons_avg_rate')], 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('cons_avg_rate')] / $exchange_currency), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')]), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')] / $exchange_currency), 2, '.', ','); ?></td>
						<?
						}
						?>
						<td align="right"><? echo $row[csf('order_ile_cost')]; ?></td>
						<? if ($dataArray[0][csf('currency_id')] != 1) {
						?>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')]), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')]); ?>
							</td>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])); ?>
							</td>
						<? }
						?>
						<td align="right">
							<? echo number_format(($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency += ($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]); ?>
						</td>
						<td align="right">
							<? echo number_format((($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency_without_ile += (($row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]); ?>
						</td>

						<?
						if (in_array($receive_pur, $wo_service_type)) {
						?>
							<td align="right"><? echo number_format(($row[csf('order_amount')] / $exchange_currency), 2, '.', ',');
												$total_amt_currency += ($row[csf('order_amount')] / $exchange_currency); ?></td>

						<?
						}
						?>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo $row[csf('no_loose_cone')]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('self')]]; ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
				<?php
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="<? echo $colspan = ($receive_basis == 2 && $receive_pur == 2) ? 7 : 6; ?>">Total :</td>
					<td align="right"><? echo number_format($order_qnty_val_sum, 2, '.', ','); ?></td>
					<td align="right">&nbsp;</td>
					<?
					if (in_array($receive_pur, $wo_service_type)) {
					?>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					<?
					}

					if ($dataArray[0][csf('currency_id')] == 1 ||  in_array($receive_pur, $wo_service_type)) {
						$colspan = 2;
					} else {
						$colspan = 1;
					}

					if ($dataArray[0][csf('currency_id')] != 1) {
					?>
						<td align="right" colspan="2"><? echo number_format($order_amount_val_sum, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_amount_val_without_ile_sum, 2, '.', ','); ?></td>
					<?
					}
					?>
					<td align="right" colspan="<? echo $colspan ?>"><? echo number_format($total_bdt_amt_currency, 2, '.', ','); ?></td>
					<td align="right"><? echo number_format($total_bdt_amt_currency_without_ile, 2, '.', ','); ?></td>
					<?
					if (in_array($receive_pur, $wo_service_type)) {
					?>
						<td align="right"><? //echo number_format($total_amt_currency,2,'.',',') 
											?></td>
					<?
					}
					?>
					<td align="right"><? echo $no_of_bags_val_sum; ?></td>
					<td align="right"><? echo $con_per_bags_sum; ?></td>
					<td align="right"><? echo $no_of_loose_cone; ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
				</tr>

			</table>
			<table>
				<tr>
					<?php

					if ($sql_result[0][csf("is_audited")] == 1) {
					?>
						<td><? echo 'Audited By &nbsp;' . $user_name[$sql_result[0][csf("audit_by")]] . '&nbsp;' . $sql_result[0][csf("audit_date")]; ?></td>
					<?php
					}
					?>


				</tr>
			</table>
			<br>
			<?
			echo signature_table(65, $data[0], $table_width . "px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "yarn_receive_print3") {
	extract($_REQUEST);
	echo load_html_head_contents("Yarn Receive Print", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//print_r ($data);

	$sql = " select id, recv_number,supplier_id,currency_id,challan_no, receive_date, exchange_rate, store_id, receive_basis,lc_no,receive_purpose,booking_id, challan_date, gate_entry_no, gate_entry_date, boe_mushak_challan_no,boe_mushak_challan_date from inv_receive_master where recv_number='$data[1]'";


	$dataArray = sql_select($sql);
	$receive_pur = $dataArray[0][csf("receive_purpose")];
	$receive_basis = $dataArray[0][csf("receive_basis")];

	$wo_id = $dataArray[0][csf("booking_id")];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location = return_field_value("location_name", "lib_location", "company_id=$data[0]");
	$address = return_field_value("address", "lib_location", "company_id=$data[0]");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$supplier_address_library = return_library_array("select id,ADDRESS_1 from  lib_supplier", "id", "ADDRESS_1");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$const_comp_arr = return_library_array("select id,const_comp from lib_subcon_charge", 'id', 'const_comp');
	$lcNum = return_library_array("select id,lc_number from com_btb_lc_master_details", 'id', 'lc_number');
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$color_arr 		= return_library_array("select id,COLOR_NAME from LIB_COLOR", "id", "COLOR_NAME");

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');

	$wo_service_type = array(2, 7, 12, 15, 38, 46, 50, 51);
	$exchange_currency = 0;
	if (in_array($receive_pur, $wo_service_type)) {
		$table_width = 1615;
		$exchange_currency = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$wo_id", "ecchange_rate");
	} else {
		$table_width = 1130;
	}

	if ($receive_basis == 2 && ($receive_pur == 2 || $receive_pur == 12 || $receive_pur == 15 || $receive_pur == 38 || $receive_pur == 44 || $receive_pur == 46 || $receive_pur == 50 || $receive_pur == 51)) {

		$pay_mode = return_field_value("pay_mode", "wo_yarn_dyeing_mst", "id=$wo_id", "pay_mode");

		if ($pay_mode == 3 || $pay_mode == 5) {
			$supplier_name = $company_library[$dataArray[0][csf('supplier_id')]];
			$com_nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dataArray[0][csf('supplier_id')]");
			foreach ($com_nameArray as $result) {
				$supplier_adress =  $result[csf('plot_no')] . $result[csf('level_no')] . $result[csf('road_no')] . $result[csf('block_no')] . $result[csf('city')] . $result[csf('zip_code')];
			}
		} else {
			$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
			$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
		}
	} else {
		$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
		$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
	}

?>
	<div id="table_row" style="width:<? echo $table_width; ?>px;">
		<table width="<? echo $table_width; ?>" align="right">
			<tr class="form_caption">
				<?
				$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td align="left" width="50">
					<?
					foreach ($data_array as $img_row) {
					?>
						<img src='../../<? echo $img_row['IMAGE_LOCATION']; ?>' height='50' width='50' align="middle" />
					<?
					}
					?>
				</td>
				<td colspan="2" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
				<td colspan="3" id="barcode_img_id">&nbsp;</td>
			</tr>

			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Material Receive
							Report</u></strong></center>
				</td>
			</tr>
			<tr style="font-size:14px">
				<td width="120" valign='top'><strong>Supplier Name:</strong></td>
				<td width="210px">
					<? echo $supplier_name . '<br>' . $supplier_adress; ?>
				</td>
				<td width="110"><strong>MRR No:</strong></td>
				<td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="120"><strong>Currency:</strong></td>
				<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			</tr>
			<tr style="font-size:14px">
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Receive Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<? if ($exchange_currency > 0) {
				?>
					<td><strong>WO Exc. Rate:</strong></td>
					<td><? echo $exchange_currency; ?></td>
				<?
				} else {
				?>
					<td><strong>Exchange Rate:</strong></td>
					<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<?
				}
				?>
			</tr>
			<tr style="font-size:14px">
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Receive Basis:</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<?
				if ($dataArray[0][csf('receive_basis')] == 1) {
				?>
					<td><strong>LC NO:</strong></td>
					<td><? echo $lcNum[$dataArray[0][csf('lc_no')]]; ?></td>
				<?
				}
				if ($receive_pur == 2) $rate_text = "Avg. Rate BDT";
				else $rate_text = "Rate";
				?>

			</tr>
			<tr>
				<td><strong>Challan Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Gate Entry No:</strong></td>
				<td><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Gate Entry Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
			</tr>
			<tr>
				<td width="300"><strong>BOE/Mushak Challan No:</strong></td>
				<td width="220"><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
				<td width="300"><strong>BOE/Mushak Challan Date:</strong></td>
				<td width="220"><? echo change_date_format($dataArray[0][csf('boe_mushak_challan_date')]); ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<tr>
						<th width="30">SL</th>
						<th width="100">WO/PI No</th>
						<th width="100">Buyer</th>
						<th width="140">Item Details</th>
						<th width="100">Color</th>
						<th width="60">Yarn Lot</th>
						<th width="40">UOM</th>
						<th width="60">Receive Qty</th>
						<th width="50">No. Of Bag</th>
						<th width="50">No. Cons Per Bag</th>
						<th width="50">No. Of Loose Cone</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th>Remarks </th>
					</tr>
				</thead>
				<?
				if ($db_type == 0) $wo_no_cond = " group_concat(b.work_order_no)";
				else if ($db_type == 2) $wo_no_cond = "LISTAGG(b.work_order_no, ',') WITHIN GROUP (ORDER BY b.work_order_no)";
				$pi_arr = array();
				$pi_sql = "select a.id, a.pi_number, a.pi_basis_id, $wo_no_cond as work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 group by a.id, a.pi_number, a.pi_basis_id";
				$pi_sql_res = sql_select($pi_sql);
				foreach ($pi_sql_res as $row) {
					if ($row[csf('pi_basis_id')] == 1) $wowoderno = implode(',', array_unique(explode(',', $row[csf('work_order_no')])));
					else if ($row[csf('pi_basis_id')] == 2) $wowoderno = "Independent";
					else $wowoderno = "";
					$pi_arr[$row[csf('id')]]['pi_number'] = $row[csf('pi_number')];
					$pi_arr[$row[csf('id')]]['work_order'] = $wowoderno;
				}

				$wo_library = return_library_array("select id,wo_number from wo_non_order_info_mst where entry_form=144", "id", "wo_number");

				//$wo_yrn_library = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst", "id", "ydw_no");

				if ($wo_id != "") {
					$wo_yarn_sql = "select a.id,a.ydw_no,b.count,b.yarn_color, b.color_range from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.id=$wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					$wo_yarn_data = sql_select($wo_yarn_sql);
					$wo_yarn_data_array = array();
					foreach ($wo_yarn_data as $row) {
						$wo_yarn_data_array[$row[csf('id')]][$row[csf('count')]][$row[csf('yarn_color')]]['color_range'] = $row[csf('color_range')];
						$wo_yrn_library[$row[csf('id')]] = $row[csf('ydw_no')];
					}
				}

				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$cond = "";
				if ($data[1] != "") $cond .= " and a.recv_number='$data[1]'";

				$i = 1;


				$sql_result = sql_select("select a.recv_number, a.receive_basis, a.receive_purpose, b.id, b.receive_basis, b.pi_wo_batch_no, b.cone_per_bag, c.product_name_details, c.lot,c.yarn_count_id,c.color, b.order_uom, b.order_qnty, b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags,b.no_loose_cone, b.remarks,b.buyer_id,b.booking_no,a.audit_by, a.audit_date, a.is_audited, b.floor_id, b.room,b.rack,b.self
					from inv_receive_master a, inv_transaction b,  product_details_master c
					where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond");
				$total_amt_currency = 0;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					//order amount calculation here
					$qty = number_format($row[csf("order_qnty")], 2, '.', '') * 1;
					$rate = $row[csf("order_rate")] * 1;
					$ileCost = $row[csf("order_ile_cost")] * 1;
					$row[csf("order_amount")] = $qty * ($rate + $ileCost);
					//end

					$order_qnty_val_sum += $row[csf('order_qnty')];
					$order_amount_val_sum += $row[csf('order_amount')];
					$order_amount_val_without_ile_sum += $row[csf('order_amount')] - ($row[csf('order_qnty')] * $row[csf('order_ile_cost')]);
					$no_of_bags_val_sum += $row[csf('no_of_bags')];
					$con_per_bags_sum += $row[csf('cone_per_bag')];
					$no_of_loose_cone += $row[csf('no_loose_cone')];

					if ($row[csf("receive_basis")] == 1)
						$receive_basis_cond = $pi_arr[$row[csf('pi_wo_batch_no')]]['pi_number'] . '<br><i>' . $pi_arr[$row[csf('pi_wo_batch_no')]]['work_order'] . '</i>';
					else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 7 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 44 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51))
						$receive_basis_cond = $wo_yrn_library[$row[csf('pi_wo_batch_no')]];
					else if ($row[csf("receive_basis")] == 14) {
						$receive_basis_cond = $row[csf('booking_no')];
					} else
						$receive_basis_cond = $wo_library[$row[csf('pi_wo_batch_no')]];

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td><? echo $i; ?></td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $receive_basis_cond; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:140px"><? echo $row[csf('product_name_details')]; ?></div>
						</td>
						<td style="word-wrap:break-word; width:140px"><? echo $color_arr[$row[csf('color')]]; ?></td>
						<td><? echo $row[csf('lot')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ','); ?></td>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo $row[csf('no_loose_cone')]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('floor_id')]]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('room')]]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('rack')]]; ?></td>
						<td align="left"><? echo $floor_room_rack_arr[$row[csf('self')]]; ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
				<?php
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="7">Total :</td>
					<td align="right"><? echo number_format($order_qnty_val_sum, 2, '.', ','); ?></td>
					<td align="right"><? echo $no_of_bags_val_sum; ?></td>
					<td align="right"><? echo $con_per_bags_sum; ?></td>
					<td align="right"><? echo $no_of_loose_cone; ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<?php

					if ($sql_result[0][csf("is_audited")] == 1) {
					?>
						<td><? echo 'Audited By &nbsp;' . $user_name[$sql_result[0][csf("audit_by")]] . '&nbsp;' . $sql_result[0][csf("audit_date")]; ?></td>
					<?php
					}
					?>


				</tr>
			</table>
			<br>
			<?
			echo signature_table(65, $data[0], $table_width . "px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	</script>
	<script>
		generateBarcode('<? echo $data[1]; ?>');
	</script>
<?
	exit();
}

/*
|--------------------------------------------------------------------------
| for action
| actn_onclick_qty
|--------------------------------------------------------------------------
|
*/
if ($action == "actn_onclick_qty") {
	echo load_html_head_contents("Item List", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function func_onkeyup_total_requisition_qty() {
			//alert('su..re');
			var updateQty = 0;
			var receiveQty = $('#txt_receive_qnty').val() * 1;
			var originalRcvQty = '<?php echo $originalRcvQty; ?>';
			var hdnIssueBalanceQty = $('#hdnIssueBalanceQty').val() * 1;
			var vs_over_receieve_percentage = $('#vs_over_receieve_percentage').val() * 1;
			var hdnReceiveQty = $('#hdnReceiveQty').val();
			var distribiutionMethod = $('#cbo_distribiution_method').val();

			if (receiveQty < 0) {
				alert("Receive quantity can't be less than zero.");
				$('#txt_receive_qnty').val(updateQty);
				var s = 1;
				$("#tblReceiveQty tbody tr").each(function() {
					$('#textReceiveQty_' + s).val('');
					s++;
				});
				return;
			}

			if (hdnIssueBalanceQty < receiveQty) {
				//Requisition quantity can't exceed allocation quantity10=20
				//alert(vs_over_receieve_percentage);
				if (vs_over_receieve_percentage > 0) {
					$msg = "\nOver receieve percentage=" + vs_over_receieve_percentage + "%\nIncluding over receieve percentage balance quantity=" + hdnIssueBalanceQty;
				} else {
					$msg = "\nBalance  quantity=" + hdnIssueBalanceQty;
				}

				alert("Receive quantity can't exceed issue balance quantity." + $msg);
				$('#txt_receive_qnty').val(updateQty);
				var s = 1;
				$("#tblReceiveQty tbody tr").each(function() {
					$('#textReceiveQty_' + s).val('');
					s++;
				});
				return;
			}

			var dataArr = hdnReceiveQty.split(',');
			var i = 0;
			if (distribiutionMethod == 1) {
				for (i; i < dataArr.length; i++) {
					var data = dataArr[i].split('_');
					var orderIssueQty = data[0];
					var totalIssueQty = data[1];
					var originalQty = ((orderIssueQty * originalRcvQty) / totalIssueQty).toFixed(2);
					var qty = ((orderIssueQty * receiveQty) / totalIssueQty).toFixed(2);
					var sl = i + 1;
					$('#textReceiveQty_' + sl).val(qty);
					$('#hdnOriginalRcvQty_' + sl).val(originalQty);
				}
			} else {
				for (i; i < dataArr.length; i++) {
					var sl = i + 1;
					$('#textReceiveQty_' + sl).removeAttr('readonly');
				}
			}
		}

		//func_onchange_distribution_method
		function func_onchange_distribution_method(distribiutionMethod) {
			//alert('su..re');
			var updateQty = 0;
			var requisitionQty = $('#txt_receive_qnty').val() * 1;
			var hdnIssueBalanceQty = $('#hdnIssueBalanceQty').val() * 1;
			var hdnReceiveQty = $('#hdnReceiveQty').val();
			var distribiutionMethod = $('#cbo_distribiution_method').val();
			var dataArr = hdnReceiveQty.split(',');
			var i = 0;
			if (distribiutionMethod == 1) {
				for (i; i < dataArr.length; i++) {
					var data = dataArr[i].split('_');
					var orderQty = data[0];
					var programQty = data[1];
					var qty = ((orderQty * requisitionQty) / programQty).toFixed(2);
					var sl = i + 1;
					$('#textReceiveQty_' + sl).val(qty);
				}
			} else {
				for (i; i < dataArr.length; i++) {
					var sl = i + 1;
					//$('#textReceiveQty_'+sl).removeAttr('readonly').val('');
					$('#textReceiveQty_' + sl).removeAttr('readonly');
				}
			}
		}

		//func_close
		function func_close() {
			var totalReceiveQty = 0;
			var m = 1;
			$("#tblReceiveQty tbody tr").each(function() {
				var ordReqQty = $(this).find('#textReceiveQty_' + m).val();
				totalReceiveQty = (totalReceiveQty * 1) + (ordReqQty * 1);
				m++;
			});
			$('#txt_receive_qnty').val(totalReceiveQty.toFixed(2));

			var hdnReceiveString = '';
			var isError = 0;
			var msg = ' ';
			var i = 1;
			$("#tblReceiveQty tbody tr").each(function() {
				var hdnOrderNo = $(this).find('#hdnOrderNo_' + i).val();
				var originalOrderRcvQty = $(this).find('#hdnOriginalRcvQty_' + i).val();
				var orderReceiveQty = $(this).find('#textReceiveQty_' + i).val();
				var orderPrevReceiveQty = $(this).find('#hdnReceiveQty_' + i).val();
				var orderBalanceQty = $(this).find('#hdnOrderBalanceQty_' + i).val();
				var greyProdId = $(this).find('#hdnGreyProdId_' + i).val();
				var receiveQty = $('#txt_receive_qnty').val();
				var distribiutionMethod = $('#cbo_distribiution_method').val();

				if (orderReceiveQty * 1 > orderBalanceQty * 1) {
					isError = 1;
					msg = ' balance ';
				}

				if (orderReceiveQty * 1 > 0) {
					if (hdnReceiveString != '') {
						hdnReceiveString += ',';
					}
					//hdnReceiveString += hdnOrderNo+'_'+orderReceiveQty+'_'+receiveQty+'_'+distribiutionMethod;
					hdnReceiveString += hdnOrderNo + '_' + orderReceiveQty + '_' + originalOrderRcvQty + '_' + greyProdId;
				}
				i++;
			});

			if (isError == 1) {
				alert('Order receive quantity is greater than order issue' + msg + 'quantity.');
				$('#txt_receive_qnty').val('');
				var s = 1;
				$("#tblReceiveQty tbody tr").each(function() {
					$('#textReceiveQty_' + s).val('');
					s++;
				});
				return;
			}

			$('#hdnReceiveString').val(hdnReceiveString);
			var z = 1;
			$("#tblReceiveQty tbody tr").each(function() {
				$('#textReceiveQty_' + z).val('');
				$('#hdnReceiveQty_' + z).val('');
				$('#hdnOrderBalanceQty_' + z).val('');
				$('#hdnOrderNo_' + z).val('');
				$('#hdnOriginalRcvQty_' + z).val('');
				z++;
			});
			$('#hdnReceiveQty').val('');
			$('#hdnIssueBalanceQty').val('');

			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<?php
		if (str_replace("'", "", $cbo_receive_purpose) == 2) // dyeing color
		{
			$dyeing_color_id = str_replace("'", "", $color_id);
			$dyeing_color_cond = " AND b.dyeing_color_id=$dyeing_color_id";
			$dyeing_color_cond2 = " AND a.yarn_color=$dyeing_color_id";
		}

		if ($hdn_ydsw_entry_form == 135) {
			$sql_workorder = "select a.entry_form,a.job_no_id as po_break_down_id,sum(a.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_dtls a where a.id=$work_order_pi_dtls_id and a.status_active=1 and a.is_deleted=0 group by a.entry_form, a.job_no_id";
		} else {
			$sql_workorder = "select a.entry_form,b.po_break_down_id from wo_yarn_dyeing_dtls a, wo_booking_dtls b where a.job_no=b.job_no and (a.fab_booking_no=b.booking_no or a.booking_no=b.booking_no) and a.mst_id=$work_order_pi_id and a.id=$work_order_pi_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dyeing_color_cond2 group by a.entry_form, b.po_break_down_id";
		}

		$workorder_result = sql_select($sql_workorder);
		$po_arr = $ydsw_qty_arr = array();
		foreach ($workorder_result as $row) {
			$work_order_entryform = $row[csf('entry_form')];
			$po_arr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
			$ydsw_qty_arr[$work_order_pi_id] = $row[csf('yarn_wo_qty')];
		}

		if (!empty($po_arr)) {
			$po_ids_condition = " AND c.po_breakdown_id in (" . implode(",", array_unique($po_arr)) . ")";
		}

		if ($work_order_entryform == 125 || $work_order_entryform == 340) // without lot
		{
			//$issue_product_cond = " AND d.yarn_count_id=$yarn_count_id AND d.yarn_comp_type1st=$composition_id AND d.yarn_type=$yarn_type_id"; temporarry omit 
			$issue_product_cond = " AND d.yarn_comp_type1st=$composition_id";
		} else {
			$issue_product_cond = " AND d.id in ($grey_yarn_prod_id)";
		}

		$variable_set_invent = sql_select("select over_rcv_percent from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 1 and over_rcv_payment=1 order by id");
		$wo_qnty = $ydsw_qty_arr[$work_order_pi_id];

		$vs_over_receive_percentage = (($hdn_ydsw_entry_form == 135) && (!empty($variable_set_invent))) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
		$over_receive_percentage_qty = ($vs_over_receive_percentage > 0) ? ($vs_over_receive_percentage / 100) * $wo_qnty : 0;
		//echo $over_receive_limit_qnty;

		$sql = "
		SELECT a.booking_id, c.po_breakdown_id, sum(c.quantity) as issue_quantity, c.is_sales,listagg(c.prod_id, '**') within group (order by c.prod_id asc) as grey_prod_id FROM inv_issue_master a INNER JOIN inv_transaction b ON a.id = b.mst_id INNER JOIN order_wise_pro_details c ON b.id = c.trans_id INNER JOIN product_details_master d ON c.prod_id=d.id WHERE a.booking_no = '" . $work_order_pi . "' AND a.booking_id=" . $work_order_pi_id . " AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 2 AND b.status_active = 1 AND b.is_deleted = 0 AND c.trans_type = 2 AND c.status_active = 1 AND c.is_deleted = 0 $po_ids_condition $issue_product_cond $dyeing_color_cond group by a.booking_id,c.po_breakdown_id,c.is_sales";
		//echo $sql;

		$sqlRslt = sql_select($sql);
		$poIdArr = array();
		$dataIssueArr = array();
		$greyProdIDArr = array();
		foreach ($sqlRslt as $row) {
			$poIdArr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			$dataIssueArr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]]['issue_qnty'] += $row[csf('issue_quantity')];
			$dataIssueArr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]]['is_sales'] = $row[csf('is_sales')];
			$dataIssueArr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]]['grey_prod_id'] = $row[csf('grey_prod_id')];
			if ($row[csf('grey_prod_id')] > 0) {
				$greyProdIDArr[$row[csf('grey_prod_id')]] = $row[csf('grey_prod_id')];
			}
		}
		//echo "<pre>";
		//print_r($dataIssueArr);

		$issue_return_sql = "SELECT a.booking_id, c.po_breakdown_id, sum(c.quantity) as issue_return_quantity, c.is_sales,listagg(c.prod_id, '**') within group (order by c.prod_id asc) as grey_prod_id FROM inv_receive_master a INNER JOIN inv_transaction b ON a.id = b.mst_id INNER JOIN order_wise_pro_details c ON b.id = c.trans_id INNER JOIN product_details_master d ON c.prod_id=d.id WHERE a.booking_no = '" . $work_order_pi . "' AND a.booking_id=" . $work_order_pi_id . " AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 4 AND b.status_active = 1 AND b.is_deleted = 0 AND c.trans_type = 4 AND c.status_active = 1 AND c.is_deleted = 0 $po_ids_condition $issue_product_cond $dyeing_color_cond group by a.booking_id,c.po_breakdown_id,c.is_sales";
		$rtnSqlRslt = sql_select($issue_return_sql);

		$dataIssueRtnArr = array();
		foreach ($rtnSqlRslt as $row) {
			$dataIssueRtnArr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]]['issue_return_quantity'] += $row[csf('issue_return_quantity')];
		}
		//echo "<pre>";
		//print_r($dataIssueRtnArr);

		if (!empty($greyProdIDArr)) {
			$grey_yarn_prod_cond = " and c.grey_prod_id in ('" . implode(",", $greyProdIDArr) . "')";
		}

		//for receive qty
		$sqlRcv = "
			SELECT
				a.id, a.booking_id,b.prod_id, c.trans_id, c.po_breakdown_id, c.quantity 
			FROM
				inv_receive_master a
				INNER JOIN inv_transaction b ON a.id = b.mst_id
				INNER JOIN order_wise_pro_details c ON b.id = c.trans_id
				INNER JOIN product_details_master d ON c.prod_id=d.id
			WHERE
				a.booking_no = '" . $work_order_pi . "'
				AND c.trans_type = 1
				AND b.item_category = 1
				AND b.transaction_type = 1
				AND a.status_active = 1
				AND a.is_deleted = 0
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND c.status_active = 1
				AND c.is_deleted = 0
				$dyeing_color_cond
				$grey_yarn_prod_cond
		";
		//echo $sqlRcv;

		$sqlRcvRslt = sql_select($sqlRcv);
		$dataRcvArr = array();
		$receiveIdArr = array();
		foreach ($sqlRcvRslt as $row) {
			$receiveIdArr[$row[csf('id')]] = $row[csf('id')];

			if ($row[csf('trans_id')] != $transId) {
				$dataRcvArr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]]['receive_qnty'] += $row[csf('quantity')];
			}

			if ($row[csf('trans_id')] == $transId) {
				$dataRcvArr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]]['current_receive_qnty'] += $row[csf('quantity')];
			}

			$dyed_prod[$row[csf('prod_id')]] = $row[csf('prod_id')];
		}
		//echo "<pre>";
		//print_r($dataRcvArr);

		//for both type of receive return
		$sqlRcvRtn = "
			SELECT
				c.po_breakdown_id, c.quantity 
			FROM
				inv_issue_master a
				INNER JOIN inv_transaction b ON a.id = b.mst_id
				INNER JOIN order_wise_pro_details c ON b.id = c.trans_id
			WHERE
				( a.received_id IN (" . implode(',', $receiveIdArr) . ") or a.received_id IN (select a.received_id as issure_rtn_rcv_id from inv_mrr_wise_issue_details c,inv_transaction b, inv_issue_master a where c.issue_trans_id=b.id and b.mst_id=a.id and b.prod_id=c.prod_id and c.entry_form=8 and a.item_category=1 and b.prod_id IN(" . implode(',', $dyed_prod) . ")) )
				AND a.status_active = 1
				AND a.is_deleted = 0
				AND a.entry_form = 8
				AND b.item_category = 1
				AND b.transaction_type = 3
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND c.trans_type = 3
				AND c.status_active = 1
				AND c.is_deleted = 0
				AND c.entry_form = 8
		";
		//echo $sqlRcvRtn;
		$sqlRcvRtnRslt = sql_select($sqlRcvRtn);
		$dataRcvRtnArr = array();
		foreach ($sqlRcvRtnRslt as $row) {
			$dataRcvRtnArr[$row[csf('po_breakdown_id')]]['return_qnty'] += $row[csf('quantity')];
		}
		//echo "<pre>";
		//print_r($dataRcvRtnArr);

		//data preparing here
		$dataArr = array();
		$totalIssueQty = 0;
		$totalReceiveQty = 0;
		$hdnIssueBalanceQty = 0;
		foreach ($dataIssueArr as $bookingId => $bookingArr) {
			foreach ($bookingArr as $orderId => $row) {
				$issue_return_quantity = $dataIssueRtnArr[$bookingId][$orderId]['issue_return_quantity'];
				$dataArr[$orderId]['issue_qnty'] = ($row['issue_qnty'] - $issue_return_quantity);
				$dataArr[$orderId]['receive_qnty'] = ($dataRcvArr[$bookingId][$orderId]['receive_qnty'] + $dataRcvArr[$bookingId][$orderId]['current_receive_qnty']) - $dataRcvRtnArr[$orderId]['return_qnty'];
				$dataArr[$orderId]['balance_qnty'] = $dataArr[$orderId]['issue_qnty'] - $dataArr[$orderId]['receive_qnty'] * 1;
				$dataArr[$orderId]['current_receive_qnty'] = $dataRcvArr[$bookingId][$orderId]['current_receive_qnty'];
				$dataArr[$orderId]['is_sales'] = $row['is_sales'];

				$totalIssueQty += $dataArr[$orderId]['issue_qnty'];
				$totalReceiveQty += $dataArr[$orderId]['receive_qnty'];
				$totalIssueBalanceQty += $dataArr[$orderId]['balance_qnty'] + $dataRcvArr[$bookingId][$orderId]['current_receive_qnty'];
				$hdnIssueBalanceQty += $dataArr[$orderId]['balance_qnty'] + $dataRcvArr[$bookingId][$orderId]['current_receive_qnty'] + $over_receive_percentage_qty;
			}
		}
		//echo "<pre>";
		//print_r($dataArr);

		//for order details


		$order_details = return_library_array("SELECT id, po_number FROM wo_po_break_down WHERE id IN(" . implode(',', $poIdArr) . ")", 'id', 'po_number');
		$sales_order_details = return_library_array("SELECT id, job_no FROM fabric_sales_order_mst WHERE id IN(" . implode(',', $poIdArr) . ")", 'id', 'job_no');
		?>
		<div align="center">
			<div style="width:320px; margin-top:25px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300">
					<thead>
						<th>Total Receive Qty</th>
						<th>Distribution Method</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" value="<?php echo $txtRcvQty; ?>" style="width:120px" onKeyUp="func_onkeyup_total_requisition_qty()" />
						</td>
						<td>
							<?php
							// onBlur="func_onkeyup_total_requisition_qty(this.value)"
							$distribiution_method = array(1 => "Proportionately", 2 => "Manually");
							echo create_drop_down("cbo_distribiution_method", 140, $distribiution_method, "", 0, "", $distribution_method, "func_onchange_distribution_method(this.value);", 0);
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="margin-top:10px;">
				<form name="yarnReqQnty_2" id="yarnReqQnty_2" autocomplete="off">
					<table id="tblReceiveQty" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<tr>
								<th width="40">Sl No</th>
								<th width="120">Order No</th>
								<th width="100">Issue Qty</th>
								<th width="100">Prev. Receive Qty</th>
								<th width="100">Balance Qty</th>
								<th width="100">Receive Qty</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sl = 0;
							$hdnReceiveQty = '';
							$totalRequisitionQty = 0;
							$allocationBalanceQty = 0;
							foreach ($dataArr as $orderId => $row) {
								$sl++;
								if ($hdnReceiveQty != '') {
									$hdnReceiveQty .= ',';
								}

								$hdnReceiveQty .= $row['issue_qnty'] . '_' . $totalIssueQty;
								$grey_prod_id = $dataIssueArr[$work_order_pi_id][$orderId]['grey_prod_id'];
							?>
								<tr valign="middle">
									<td align="center"><?php echo $sl; ?></td>
									<td title="<? echo $orderId; ?>">
										<?php
										if ($row['is_sales'] == 1) {
											echo $sales_order_details[$orderId];
										} else {
											echo $order_details[$orderId];
										}
										?>
									</td>
									<td align="right"><?php echo number_format($row['issue_qnty'], 2, '.', ''); ?></td>
									<td align="right"><?php echo number_format($row['receive_qnty'], 2, '.', ''); ?></td>
									<td align="right"><?php echo number_format(($row['balance_qnty'] + $row['current_receive_qnty']), 2, '.', ''); ?></td>
									<td>
										<input type="text" name="textReceiveQty[]" id="textReceiveQty_<?php echo $sl; ?>" class="text_boxes_numeric" style="text-align:right" readonly value="<?php echo number_format($row['current_receive_qnty'], 2, '.', ''); ?>" />
										<input type="hidden" name="hdnOriginalRcvQty[]" id="hdnOriginalRcvQty_<?php echo $sl; ?>" class="text_boxes_numeric" style="text-align:right" readonly value="<?php echo number_format($row['receive_qnty'], 2, '.', ''); ?>" />
										<input type="hidden" name="hdnReceiveQty[]" id="hdnReceiveQty_<?php echo $sl; ?>" class="text_boxes_numeric" value="<?php echo number_format(($row['receive_qnty'] + $row['current_receive_qnty']), 2, '.', ''); ?>" style="text-align:right" readonly />
										<input type="hidden" name="hdnOrderBalanceQty[]" id="hdnOrderBalanceQty_<?php echo $sl; ?>" class="text_boxes_numeric" value="<?php echo number_format((($row['balance_qnty'] + $over_receive_percentage_qty) + $row['current_receive_qnty']), 2, '.', ''); ?>" style="text-align:right" readonly />
										<input type="hidden" name="hdnOrderNo[]" id="hdnOrderNo_<?php echo $sl; ?>" class="text_boxes_numeric" value="<?php echo $orderId; ?>" style="text-align:right" readonly />

										<input type="hidden" name="hdnGreyProdId[]" id="hdnGreyProdId_<?php echo $sl; ?>" class="text_boxes_numeric" value="<?php echo $grey_prod_id; ?>" style="text-align:right" readonly />
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2">Total</th>
								<th><?php echo number_format($totalIssueQty, 2, '.', ''); ?></th>
								<th><?php echo number_format($totalReceiveQty, 2, '.', ''); ?></th>
								<th><?php echo number_format($totalIssueBalanceQty, 2, '.', ''); ?></th>
								<th>
									<input type="hidden" name="hdnReceiveString" id="hdnReceiveString" class="text_boxes" value="" readonly />
									<input type="hidden" name="hdnReceiveQty" id="hdnReceiveQty" class="text_boxes" value="<? echo $hdnReceiveQty; ?>" style="text-align:right" />
									<input type="hidden" name="hdnIssueBalanceQty" id="hdnIssueBalanceQty" class="text_boxes" value="<? echo number_format($hdnIssueBalanceQty, 2, '.', ''); ?>" />
									<input type="hidden" name="vs_over_receieve_percentage" id="vs_over_receieve_percentage" class="text_boxes" value="<? echo $vs_over_receive_percentage; ?>" />
								</th>
							</tr>
						</tfoot>
					</table>
				</form>
			</div>
			<div style="margin-top:10px;">
				<table width="660" id="table_id">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="func_close()" style="width:100px" />
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "file_upload") {
	header("Content-Type: application/json");
	$filename = time() . $_FILES['file']['name'];
	$location = "../../file_upload/" . $filename;
	// echo $txt_mrr_no.'gfgfggf';die;
	$uploadOk = 1;
	if (empty($txt_mrr_no)) {
		$txt_mrr_no = $_GET['txt_mrr_no'];
	}

	if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
		$uploadOk = 1;
	} else {
		$uploadOk = 0;
	}
	// echo "0**".$uploadOk; die;
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$id = return_next_id("id", "COMMON_PHOTO_LIBRARY", 1);
	echo $data_array = "(" . $id . ",'" . $txt_mrr_no . "','yarn_receive','file_upload/" . $filename . "','2','" . $filename . "','" . $pc_date_time . "')";
	$field_array = "id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID = sql_insert("COMMON_PHOTO_LIBRARY", $field_array, $data_array, 1);

	if ($db_type == 0) {
		if ($rID == 1 && $uploadOk == 1) {
			mysql_query("COMMIT");
			echo "0**" . $new_system_id[0] . "**" . $txt_mrr_no;
		} else {
			mysql_query("ROLLBACK");
			echo "10**" . $txt_mrr_no;
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($rID == 1 && $uploadOk == 1) {
			oci_commit($con);
			echo "0**" . $new_system_id[0] . "**" . $txt_mrr_no;
		} else {
			oci_rollback($con);
			return "10";
		}
	} else {
		return 1;
		echo "10**" . $rID . "**" . $uploadOk . "**INSERT INTO COMMON_PHOTO_LIBRARY(" . $field_array . ") VALUES " . $data_array;
	}

	disconnect($con);
	die;
}
?>