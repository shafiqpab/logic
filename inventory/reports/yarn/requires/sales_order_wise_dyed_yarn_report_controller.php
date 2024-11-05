<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer") {
	extract($_REQUEST);
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($choosenCompany) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if ($action == "fso_no_popup") {
	echo load_html_head_contents("Job Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);

?>


	<script>
		var hide_fso_id = '<? echo $hide_fso_id; ?>';

		//var attach_approval_pi='<? //echo $attach_approval_pi; 
									?>';

		var selected_id = new Array,
			selected_name = new Array();

		function check_all_data(is_checked) {
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
			var old = document.getElementById('txt_fso_row_id').value;
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
				selected_name.push($('#txt_individual' + str).val());

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_fso_id').val(id);
			$('#hide_fso_no').val(name);
		}

		/*function reset_hide_field(type)
		{
			$('#hide_fso_id').val( '' );
			$('#hide_fso_no').val( '' );
			if(type==1)
			{
				$('#search_div').html( '' );
			}
		}*/
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:710px;">
					<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Company</th>
							<th>Buyer Name</th>
							<th>Job Year</th>
							<th>Within Group</th>
							<th>FSO NO.</th>
							<th>Booking NO.</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
							<input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
							<input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

						</thead>
						<tbody>
							<tr>
								<td>
									<?
									echo create_drop_down("cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $companyID, "", 1);
									?>
								</td>
								<td align="center">
									<?
									echo create_drop_down("cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$companyID and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyer_name, "", 0);
									?>
								</td>
								<td><? echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, ""); ?></td>
								<td><? echo create_drop_down("cbo_within_group", 80, $yes_no, "", 1, "-- Select --", $selected, "", 0, ""); ?></td>
								<td>
									<input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
								</td>
								<td>
									<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id; ?>', 'create_fso_no_search_list_view', 'search_div', 'sales_order_wise_dyed_yarn_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_fso_no_search_list_view") {
	$data = explode('**', $data);
	$company_id = $data[0];
	$buyer_id = $data[1];
	$year = $data[2];
	$within_group = $data[3];
	$fso_no = trim($data[4]);
	$booking_no = trim($data[5]);

	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	$search_cond = "";


	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_cond_with_1 = " and c.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyer_cond_with_2 = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$buyer_cond_with_1 =  "";
				$buyer_cond_with_2 =  "";
			}
		} else {
			$buyer_cond_with_1 =  "";
			$buyer_cond_with_2 =  "";
		}
	} else {
		$buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
		$buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
	}


	if ($fso_no != "") {
		$search_cond .= " and a.job_no_prefix_num = '$fso_no'";
	}
	if ($booking_no != "") {
		$search_cond .= " and a.sales_booking_no like '%$booking_no%'";
	}
	if ($db_type == 0) {
		if ($year != 0) $search_cond .= " and YEAR(a.insert_date)=$year";
		else $search_cond .= "";
	} else if ($db_type == 2) {
		$year_field_con = " and to_char(a.insert_date,'YYYY')";
		if ($year != 0) $search_cond .= " $year_field_con=$year";
		else $search_cond .= "";
	}


	//echo $sql = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id  order by a.id desc";

	$sql_2 = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	order by id desc";

	$sql_1 = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	union all
	select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_non_ord_samp_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id";

	if ($within_group == 1) {
		$sql = $sql_1;
	} else if ($within_group == 2) {
		$sql = $sql_2;
	} else {
		$sql = $sql_1 . " union all " . $sql_2;
	}
	//echo $sql;
?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Buyer</th>
			<th width="150">FSO No</th>
			<th width="">Booking No</th>
		</thead>
	</table>
	<div style="width:618px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search">
			<?php
			$i = 1;
			$fso_row_id = "";
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				/*if(in_array($selectResult[csf('id')],$hidden_fso_id))
			{
				if($fso_row_id=="") $fso_row_id=$i; else $fso_row_id.=",".$i;
			}*/
			?>

				<tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="40" align="center"><?php echo "$i"; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>" />
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>" />
					</td>
					<td width="130">
						<p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p>
					</td>
					<td width="120" title="<? echo $selectResult[csf('buyer_id')]; ?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
					<td width="150">
						<p><?php echo $selectResult[csf('job_no')]; ?></p>
					</td>
					<td width=""><?php echo $selectResult[csf('sales_booking_no')]; ?></td>
				</tr>
			<?
				$i++;
			}
			?>
			<!-- <input type="hidden" name="txt_fso_row_id" id="txt_fso_row_id" value="<?php //echo $fso_row_id; 
																						?>"/>	 -->
		</table>
	</div>

	<table width="520" cellspacing="0" cellpadding="0" style="border:none" align="left">
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

	<?
	exit();
}

if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	// echo $txt_fso_no;die;
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');

	$product_res = sql_select("select a.id, a.product_name_details,a.lot,a.brand, b.brand_name yarn_brand,a.yarn_comp_type1st,a.yarn_type,a.yarn_count_id from product_details_master a  left join lib_brand b on a.brand = b.id ");
	$product_arr = array();
	foreach ($product_res as $value) {
		$product_arr[$value[csf("id")]]["product_name_details"] = $value[csf("product_name_details")];
		$product_arr[$value[csf("id")]]["lot"] = $value[csf("lot")];
		$product_arr[$value[csf("id")]]["brand"] = $value[csf("yarn_brand")];
		$product_arr[$value[csf("id")]]["yarn_comp_type1st"] = $value[csf("yarn_comp_type1st")];
		$product_arr[$value[csf("id")]]["yarn_type"] = $value[csf("yarn_type")];
		$product_arr[$value[csf("id")]]["yarn_count_id"] = $value[csf("yarn_count_id")];
		$product_arr_ref[$value[csf("id")]] = $value[csf("yarn_count_id")] . "**" . $value[csf("yarn_comp_type1st")] . "**" . $value[csf("yarn_type")];
	}
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$txt_process_loss = str_replace("'", "", trim($txt_process_loss));
	$txt_yd_wo_no = str_replace("'", "", trim($txt_yd_wo_no));
	$txt_job_no = str_replace("'", "", trim($txt_job_no));
	$txt_style_no = str_replace("'", "", trim($txt_style_no));
	$txt_ir_no = str_replace("'", "", trim($txt_ir_no));


	if ($txt_job_no) {
		$po_job_no_cond = " and a.po_job_no like  '%$txt_job_no%'";
	} else {
		$po_job_no_cond = "";
	}

	if ($txt_style_no) {
		$po_style_no_cond = " and a.STYLE_REF_NO like  '%$txt_style_no%'";
	} else {
		$po_style_no_cond = "";
	}

	if ($txt_yd_wo_no) {
		$yd_wo_no_cond = " and c.ydw_no like  '%$txt_yd_wo_no%'";
		$yd_wo_no_cond2 = " and a.booking_no like  '%$txt_yd_wo_no%'";
	} else {
		$yd_wo_no_cond = "";
		$yd_wo_no_cond2 = "";
	}

	if (str_replace("'", "", $txt_ir_no) != "") $ir_cond = " and c.grouping like '%$txt_ir_no%' ";
	else $ir_cond = "";
	if (str_replace("'", "", $txt_job_no) != "" || str_replace("'", "", $txt_yd_wo_no) != "" || str_replace("'", "", $txt_ir_no) != "") {
		$sql = "SELECT a.id,  c.grouping,  a.job_no, a.booking_no  from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_name) $ir_cond";
		// echo $sql;
		$jobBookingArray = sql_select($sql);
		$all_booking_no_arr = array();
		foreach ($jobBookingArray as $row) {
			if ($bookingNoChk[$row[csf('booking_no')]] == "") {
				$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
				array_push($all_booking_no_arr, $row[csf('booking_no')]);
			}
		}
	}
	// echo "<pre>";
	// print_r($all_booking_no_arr);
	if (!empty($all_booking_no_arr)) {
		$job_booking_cond = " " . where_con_using_array($all_booking_no_arr, 1, 'a.sales_booking_no') . " ";
	}

	if (str_replace("'", "", $cbo_buyer_name) == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				//$buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_cond_with_1 = " and e.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyer_cond_with_2 = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				//$buyer_id_cond="";
				$buyer_cond_with_1 = "";
				$buyer_cond_with_2 = "";
			}
		} else {
			//$buyer_id_cond="";
			$buyer_cond_with_1 = "";
			$buyer_cond_with_2 = "";
		}
	} else {
		//$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		$buyer_cond_with_1 =  " and e.buyer_id=$cbo_buyer_name";
		$buyer_cond_with_2 =  " and a.buyer_id=$cbo_buyer_name";
	}

	$job_no_cond = "";
	$hide_fso_id = str_replace("'", "", $hide_fso_id);
	$txt_fso_no = str_replace("'", "", $txt_fso_no);
	if ($hide_fso_id) {
		$allFsoIdArr = array_unique(explode(",", $hide_fso_id));
		if (count($allFsoIdArr) > 999 && $db_type == 2) {
			$allFsoIdChunkArr = array_chunk($allFsoIdArr, 999);
			foreach ($allFsoIdChunkArr as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$fsoid_cond .= "  a.id in($chunk_arr_value) or ";
			}

			$job_no_cond .= " and (" . chop($fsoid_cond, 'or ') . ")";
		} else {
			$job_no_cond = " and a.id in ($hide_fso_id)";
		}
	} else {
		// echo $txt_fso_no;die;
		if ($txt_fso_no) {
			$allFsoArr = array_unique(explode(",", $txt_fso_no));

			if (count($allFsoArr) > 999 && $db_type == 2) {
				$allFsoChunkArr = array_chunk($allFsoArr, 999);
				foreach ($allFsoChunkArr as $chunk_arr_fso) {
					$chunk_arr_value = implode("','", $chunk_arr_fso);
					$fsoid_cond .= "  a.job_no_prefix_num in($chunk_arr_value) or ";
				}

				$job_no_cond .= " and (" . chop($fsoid_cond, 'or ') . ")";
			} else {
				$txt_fso_no_arr = explode(",", $txt_fso_no);
				$txt_fso_no_str = '';
				foreach ($txt_fso_no_arr as $val) {
					$txt_fso_no_str .= "'" . trim($val, ' ') . "',";
				}
				$txt_fso_no_str = trim($txt_fso_no_str, ",");
				$job_no_cond = " and a.job_no_prefix_num in ($txt_fso_no_str)";
			}
		}
	}

	$color_array = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$from_date = str_replace("'", "", $txt_date_from);
	$to_date = str_replace("'", "", $txt_date_to);

	if ($db_type == 0) {
		$date_from = change_date_format($from_date, 'yyyy-mm-dd');
		$date_to = change_date_format($to_date, 'yyyy-mm-dd');
	} else if ($db_type == 2) {
		$date_from = change_date_format($from_date, '', '', 1);
		$date_to = change_date_format($to_date, '', '', 1);
	} else {
		$date_from = "";
		$date_to = "";
	}
	$date_cond = "";
	if ($date_from != "" && $date_to != "") $date_cond = " and c.booking_date between '" . $date_from . "' and '" . $date_to . "'";
	else $date_cond = "";

	$process_loss_cond = "";
	//if($txt_process_loss != "") $process_loss_cond = "and b.process_loss = $txt_process_loss ";

	$lot_color_array = array();
	$trans_id_chk = array();
	$lot_rcv_array = array();

	$lot_res = sql_select("SELECT b.job_no, a.booking_id,a.booking_no, b.brand_id,c.lot as lot ,c.yarn_count_id, c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_type,c.color, b.cons_quantity, b.id,c.id as product_id,d.grey_prod_id,b.remarks, a.receive_date, a.challan_no from inv_receive_master a, inv_transaction b, order_wise_pro_details d, product_details_master c where a.id=b.mst_id and b.id=d.trans_id and b.prod_id=d. prod_id and b.prod_id=c.id and a.entry_form=1 and a.receive_basis=2 and a.receive_purpose=2 and b.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and b.transaction_type=1 $yd_wo_no_cond2"); //and a.booking_id = 479

	$booking_no_chk = array();
	$booking_no_arr = array();
	foreach ($lot_res as $lot_r) {
		if ($trans_id_chk[$lot_r[csf("id")]] == "") {
			$trans_id_chk[$lot_r[csf("id")]] = $lot_r[csf("id")];
			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["rcvQnty"] +=  $lot_r[csf("cons_quantity")];
			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["color_id"] =  $lot_r[csf("color")];

			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["product_id"] =  $lot_r[csf("product_id")];
			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["grey_prod_id"] =  $lot_r[csf("grey_prod_id")];
			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["lot"] =  $lot_r[csf("lot")];
			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["remarks"] .=  $lot_r[csf("remarks")] . ",";
			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["receive_date"] =  $lot_r[csf("receive_date")];
			$lot_rcv_array[$lot_r[csf("job_no")] . "**" . $lot_r[csf("booking_id")] . "**" . $lot_r[csf("yarn_count_id")] . "**" . $lot_r[csf("yarn_comp_type1st")] . "**" . $lot_r[csf("yarn_type")] . "**" . $lot_r[csf("color")]][$lot_r[csf("product_id")]]["challan_no"] =  $lot_r[csf("challan_no")];

			if ($booking_no_chk[$lot_r[csf("booking_no")]] == "") {
				$booking_no_chk[$lot_r[csf("booking_no")]] = $lot_r[csf("booking_no")];
				array_push($booking_no_arr, $lot_r[csf("booking_no")]);
			}
		}
	}

	if (!empty($booking_no_arr)) {
		$booking_no_sql = "SELECT b.fab_booking_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id  and a.company_id in($cbo_company_name) and a.is_deleted=0 and a.status_active=1 and b.fab_booking_no is not null " . where_con_using_array($booking_no_arr, 1, 'a.ydw_no') . "";
		//echo $booking_no_sql;die;
		$booking_noData = sql_select($booking_no_sql);
		$fab_booking_no_arr = [];
		foreach ($booking_noData as $row) {
			if ($fab_booking_no_chk[$row[csf("fab_booking_no")]] == "") {
				$fab_booking_no_chk[$row[csf("fab_booking_no")]] = $row[csf("fab_booking_no")];
				array_push($fab_booking_no_arr, $row[csf("fab_booking_no")]);
			}
		}
		unset($booking_noData);

		$plan_sql = "SELECT a.po_id,a.booking_no,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.company_id in($cbo_company_name)  and a.status_active=1 and b.status_active=1 " . where_con_using_array($fab_booking_no_arr, 1, 'a.booking_no') . ""; //and a.booking_no in (".implode(",",$febric_booking_no_arr).")
		//echo $plan_sql;die;
		$planData = sql_select($plan_sql);
		$plan_requisiotn_booking_arr = array();
		foreach ($planData as $row) {
			$plan_requisiotn_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('booking_no')];
		}
		unset($planData);
	}


	//$issue_knitting_sql2 = "SELECT a.id, a.issue_number,a.issue_basis,b.requisition_no, a.booking_no,a.booking_id, a.buyer_job_no,b.prod_id,b.dyeing_color_id,a.knit_dye_source,a.knit_dye_company, a.issue_date, a.challan_no,c.po_breakdown_id, c.quantity from inv_issue_master a, inv_transaction b,order_wise_pro_details c where a.id = b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and c.trans_type=2 and a.issue_basis in (3,4,8) and a.issue_purpose = 1 and b.transaction_type = 2 and c.status_active = 1 and c.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and a.entry_form = 3 and a.company_id in($cbo_company_name) order by a.id desc";

	//echo $issue_knitting_sql; die();

	$issue_knitting_sql = "SELECT b.requisition_no, b.prod_id, a.knit_dye_source, a.knit_dye_company, a.issue_date, a.challan_no,b.transaction_type, c.po_breakdown_id, c.quantity FROM inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d WHERE a.id = b.mst_id AND b.id = c.trans_id AND b.prod_id = c.prod_id AND c.prod_id=d.id AND c.trans_type = 2 AND a.issue_basis IN (3, 4, 8) AND a.issue_purpose = 1 AND b.transaction_type = 2  AND d.dyed_type=1 AND c.status_active = 1 AND c.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form = 3 AND a.company_id IN ($cbo_company_name)
	union all 
	SELECT b.requisition_no, b.prod_id, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no,b.transaction_type, c.po_breakdown_id, c.quantity FROM inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d WHERE a.id = b.mst_id AND b.id = c.trans_id AND b.prod_id = c.prod_id AND c.prod_id=d.id AND  c.trans_type = 4 and a.receive_basis in (3, 4, 8) AND b.transaction_type = 4 AND d.dyed_type=1  AND c.status_active = 1 AND c.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form = 9 AND a.company_id IN ($cbo_company_name)";

	//echo $issue_knitting_sql;
	//die;
	$issue_knitting = sql_select($issue_knitting_sql);

	$issue_knitting_arr = $issue_rtn_knitting_arr = array();
	foreach ($issue_knitting as $row) {
		$booking_no = $plan_requisiotn_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]];

		if ($row[csf("transaction_type")] == 2) {
			$issue_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["Qnty"] +=  $row[csf("quantity")];
			$issue_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["source_company"] .=  $row[csf("knit_dye_source")] . "_" . $row[csf("knit_dye_company")] . "*";
			$issue_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["challan_no"] .=  $row[csf("challan_no")] . "*";
			$issue_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_date"] .=  $row[csf("issue_date")] . "*";
		} else {
			$issue_rtn_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["Qnty"] +=  $row[csf("quantity")];
			$issue_rtn_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["source_company"] .=  $row[csf("knit_dye_source")] . "_" . $row[csf("knit_dye_company")] . "*";
			$issue_rtn_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["challan_no"] .=  $row[csf("challan_no")] . "*";
			$issue_rtn_knitting_arr[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_date"] .=  $row[csf("issue_date")] . "*";
		}
	}
	ob_start();
	if ($type == 1) {
	?>
		<style>
			.wrd_brk {
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
		<fieldset style="width:3370px;">
			<table cellpadding="0" cellspacing="0" width="2600">
				<tr>
					<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? //echo $company_arr[str_replace("'","",$cbo_company_name)]; 
																								?></strong></td>
				</tr>
			</table>
			<table width="3360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="80" rowspan="2">Buyer Name</th>
						<th width="110" rowspan="2">Style No</th>
						<th width="150" rowspan="2">FSO No</th>
						<th width="150" rowspan="2">Booking No</th>
						<th width="100" rowspan="2">Job No</th>
						<th width="100" rowspan="2">IR/IB</th>
						<th width="110" rowspan="2">WO No</th>
						<th width="100" rowspan="2">Wo Date</th>
						<th width="100" rowspan="2">Y/D Company</th>
						<th width="900" colspan="9">Grey Yarn</th>
						<th width="100" rowspan="2">Color</th>
						<th width="100" rowspan="2">Dyeing Charge</th>
						<th width="600" colspan="6">Dyed Yarn Receive</th>
						<th width="500" colspan="6">YD Delivery for</th>
					</tr>
					<tr>
						<th width="100">Brand</th>
						<th width="100">Yarn Details</th>
						<th width="100">Lot</th>
						<th width="100">Total Required</th>
						<th width="100">Del.Date</th>
						<th width="100">Challan no</th>
						<th width="100">Delivery</th>
						<th width="100">Issue Return</th>
						<th width="100">Balance</th>

						<th width="100">Rcvd.Date</th>
						<th width="100">Challan no</th>
						<th width="100">Lot/Batch</th>
						<th width="100">Received Qty.</th>
						<th width="100">Rec. Balance <? echo ($txt_process_loss * 1 > 0) ? "<br><p style='font-size:11px;'>process loss $txt_process_loss %</p>" : ""; ?></th>
						<th width="100">CK Number</th>

						<th width="100">Issue Date</th>
						<th width="100">Challan no</th>
						<th width="100">Issue To Knitting</th>
						<th width="100">Issue Return from Knitting</th>
						<th width="100">Issue Balance</th>
						<th>Knitting Party</th>
					</tr>
				</thead>
			</table>
			<div style="width:3380px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="3360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$sql = "SELECT a.id as sales_order_id, a.company_id, e.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, c.booking_date, c.ydw_no, c.id as order_id, d.product_id, d.yarn_wo_qty, c.supplier_id, d.id as wo_yarn_dyeing_dtls_id, d.yarn_color,d.fab_booking_no, b.process_loss, d.dyeing_charge,c.pay_mode,a.po_job_no,e.booking_date as book_date
					from  fabric_sales_order_mst a,fabric_sales_order_dtls b, wo_yarn_dyeing_dtls d, wo_yarn_dyeing_mst c, wo_booking_mst e
					where a.id = b.mst_id and a.id=d.job_no_id and d.mst_id=c.id and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.within_group=1 and e.booking_no = a.sales_booking_no and a.company_id in($cbo_company_name) $job_no_cond $date_cond $process_loss_cond $yd_wo_no_cond $job_booking_cond $buyer_cond_with_1 $po_job_no_cond $po_style_no_cond and c.entry_form= 135
					union all
					select a.id as sales_order_id, a.company_id, e.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, c.booking_date, c.ydw_no, c.id as order_id, d.product_id, d.yarn_wo_qty, c.supplier_id, d.id as wo_yarn_dyeing_dtls_id, d.yarn_color,d.fab_booking_no, b.process_loss, d.dyeing_charge,c.pay_mode,a.po_job_no,e.booking_date as book_date
					from fabric_sales_order_mst a,fabric_sales_order_dtls b, wo_yarn_dyeing_dtls d, wo_yarn_dyeing_mst c, wo_non_ord_samp_booking_mst e
					where a.id = b.mst_id and a.id=d.job_no_id and d.mst_id=c.id and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.within_group=1 and e.booking_no = a.sales_booking_no and a.company_id in($cbo_company_name) $job_no_cond $date_cond $process_loss_cond $yd_wo_no_cond $job_booking_cond $buyer_cond_with_1 $po_job_no_cond $po_style_no_cond and c.entry_form= 135
					union all
					 select a.id as sales_order_id, a.company_id, a.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, c.booking_date, c.ydw_no, c.id as order_id, d.product_id, d.yarn_wo_qty, c.supplier_id, d.id as wo_yarn_dyeing_dtls_id, d.yarn_color,d.fab_booking_no, b.process_loss, d.dyeing_charge ,c.pay_mode,a.po_job_no,null as book_date
					from fabric_sales_order_mst a,fabric_sales_order_dtls b, wo_yarn_dyeing_dtls d, wo_yarn_dyeing_mst  c
					where a.id = b.mst_id and a.id=d.job_no_id and d.mst_id=c.id and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.within_group=2 and a.company_id in($cbo_company_name) $job_no_cond $date_cond $process_loss_cond $yd_wo_no_cond $buyer_cond_with_2 $po_job_no_cond $job_booking_cond $po_style_no_cond and c.entry_form= 135
					order by sales_order_id, order_id, product_id, yarn_color";

					// echo $sql;die;
					$result = sql_select($sql);
					$chk_woYarnDyeingDtlsId = array();
					$data_array = array();
					$book_row_arr = array();
					$order_id_chk = array();
					$order_id_arr = array();
					$all_booking_arr = array();
					$allbookingNoChk = array();
					foreach ($result as $value) {
						if ($allbookingNoChk[$value[csf('sales_booking_no')]] == "") {
							$allbookingNoChk[$value[csf('sales_booking_no')]] = $value[csf('sales_booking_no')];
							array_push($all_booking_arr, $value[csf('sales_booking_no')]);
						}
						if ($chk_woYarnDyeingDtlsId[$value[csf("wo_yarn_dyeing_dtls_id")]] == "") {
							$chk_woYarnDyeingDtlsId[$value[csf("wo_yarn_dyeing_dtls_id")]] = $value[csf("wo_yarn_dyeing_dtls_id")];

							//$book_key=$value[csf("job_no")]."**".$value[csf("order_id")]."**".$value[csf("sales_order_id")];
							$book_key = $value[csf("job_no")] . "**" . $value[csf("order_id")];
							$book_row_key = $value[csf("job_no")] . "**" . $value[csf("order_id")] . "**" . $value[csf("product_id")] . "**" . $value[csf("yarn_color")];

							$buyer_style_within_supplier[$book_key] = $value[csf("buyer_id")] . "#" . $value[csf("style_ref_no")] . "#" . $value[csf("within_group")] . "#" . $value[csf("supplier_id")] . "#" . $value[csf("sales_booking_no")] . "#" . $value[csf("ydw_no")] . "#" . $value[csf("dyeing_charge")] . "#" . $value[csf("pay_mode")] . "#" . $value[csf("order_id")] . "#" . $value[csf("po_job_no")] . "#" . $value[csf("company_id")] . "#" . $value[csf("booking_date")];

							$product_ref_key[$value[csf("product_id")]] = $product_arr[$value[csf("product_id")]]["yarn_count_id"] . "**" . $product_arr[$value[csf("product_id")]]["yarn_comp_type1st"] . "**" . $product_arr[$value[csf("product_id")]]["yarn_type"];

							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["sales_order_id"] = $value[csf("sales_order_id")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["company_id"] = $value[csf("company_id")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["buyer_id"] = $value[csf("buyer_id")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["style_ref_no"] = $value[csf("style_ref_no")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["job_no"] = $value[csf("job_no")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["within_group"] = $value[csf("within_group")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["sales_booking_no"] = $value[csf("sales_booking_no")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["booking_date"] = $value[csf("booking_date")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["ydw_no"] = $value[csf("ydw_no")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["order_id"] = $value[csf("order_id")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["product_id"] = $value[csf("product_id")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["supplier_id"] = $value[csf("supplier_id")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["yarn_color"] = $value[csf("yarn_color")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["yarn_wo_qty"] += $value[csf("yarn_wo_qty")];
							$source_array[$book_key][$value[csf("product_id")]][$value[csf("yarn_color")]]["dyeing_charge"] += $value[csf("dyeing_charge")];
						}
						if ($order_id_chk[$value[csf("order_id")]] == "") {
							$order_id_chk[$value[csf("order_id")]] = $value[csf("order_id")];
							array_push($order_id_arr, $value[csf("order_id")]);
						}
					}
					//var_dump($order_id_arr);
					// echo "<pre>";
					// print_r($job_info_arr);

					$sql_job = "SELECT a.id,  c.grouping,  a.job_no, a.booking_no  from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c where a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company " . where_con_using_array($all_booking_arr, 1, 'a.booking_no') . " ";
					// echo $sql_job;
					$sql_job_rslt = sql_select($sql_job);
					$job_info_arr = array();
					foreach ($sql_job_rslt as $val) {
						$job_info_arr[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
						$job_info_arr[$val[csf("booking_no")]]["grouping"] .= $val[csf("grouping")] . ',';
					}

					// echo "<pre>";
					// print_r($job_info_arr);
					if (!empty($order_id_arr)) {
						$grey_issue_res = sql_select("SELECT a.id, a.issue_number, b.cons_quantity, a.booking_no,a.booking_id, b.job_no,b.prod_id,b.dyeing_color_id, a.issue_date, a.challan_no from inv_issue_master a, inv_transaction b 	where a.id = b.mst_id and a.issue_basis = 1 and a.issue_purpose = 2 and b.transaction_type = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $yd_wo_no_cond2 " . where_con_using_array($order_id_arr, 0, 'a.booking_id') . "");
						$grey_issue_arr = array();
						$grey_issue_arr_color = array();
						foreach ($grey_issue_res as $gi) {
							$grey_issue_arr_color[$gi[csf("job_no")] . "**" . $gi[csf("booking_id")]][$gi[csf("prod_id")]][$gi[csf("dyeing_color_id")]] += $gi[csf("cons_quantity")];
							$grey_issue_arr[$gi[csf("job_no")] . "**" . $gi[csf("booking_id")]][$gi[csf("prod_id")]][$gi[csf("dyeing_color_id")]]['issue_date'] = $gi[csf("issue_date")];
							$grey_issue_arr[$gi[csf("job_no")] . "**" . $gi[csf("booking_id")]][$gi[csf("prod_id")]][$gi[csf("dyeing_color_id")]]['challan_no'] = $gi[csf("challan_no")];
						}
						unset($grey_issue_res);

						$grey_issue_rtn_res = sql_select("SELECT a.id,b.cons_quantity, a.booking_no,a.booking_id, b.prod_id,b.dyeing_color_id from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.receive_basis = 1 and b.transaction_type = 4 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 " . where_con_using_array($order_id_arr, 0, 'a.booking_id') . "");
						$grey_issue_rtn_arr_color = array();
						foreach ($grey_issue_rtn_res as $gi) {
							$grey_issue_rtn_arr_color[$gi[csf("booking_id")]][$gi[csf("prod_id")]][$gi[csf("dyeing_color_id")]] += $gi[csf("cons_quantity")];
						}
						unset($grey_issue_rtn_res);
						//echo "<pre>";print_r($grey_issue_rtn_arr_color);
					}
					/*echo "<pre>";
					print_r($source_array);
					die;*/

					$Data_Array = array();
					foreach ($source_array as $book_order_key => $book_bal) {
						foreach ($book_bal as $product_id => $product_data) {
							foreach ($product_data as $color_id => $color_val) {
								list($fso_no, $ydw_pk_id) = explode("**", $book_order_key);
								if (($lot_rcv_array[$book_order_key . "**" . $product_ref_key[$product_id] . "**" . $color_id])) {
									foreach ($lot_rcv_array[$book_order_key . "**" . $product_ref_key[$product_id] . "**" . $color_id] as $dyeProd => $Qnty) {
										//echo $lot ."=". $color_id."=".$Qnty['color_id']."<br>";
										//print_r($lot);
										if (($ydw_pk_id == $color_val["order_id"]) && ($Qnty['grey_prod_id'] == $product_id) && ($color_id == $Qnty['color_id'])) {
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["within_group"] = $color_val["within_group"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["ydw_no"] = $color_val["ydw_no"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["sales_order_id"] = $color_val["sales_order_id"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["sales_booking_no"] = $color_val["sales_booking_no"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["style_ref_no"] = $color_val["style_ref_no"];
											$Data_Array[$book_order_key][$Qnty['grey_prod_id']][$color_id][$dyeProd]["lot_qnty"] = $Qnty["rcvQnty"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["dye_product_id"] = $Qnty["product_id"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["dyelot"] = $Qnty["lot"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["remarks"] .= $Qnty["remarks"] . ",";
											$prod_lot_qnty[$book_order_key][$Qnty['grey_prod_id']][$color_id]["prod_lot_qnty"] += $Qnty["rcvQnty"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["receive_date"] = $Qnty["receive_date"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["challan_no"] = $Qnty["challan_no"];
											$Data_Array[$book_order_key][$product_id][$color_id][$dyeProd]["fab_booking_no"] = $Qnty["fab_booking_no"];
										} else {
											$Data_Array[$book_order_key][$product_id][$color_id][]["within_group"] = 0;
										}
									}
								} else {
									$Data_Array[$book_order_key][$product_id][$color_id][""]["within_group"] = 0;
								}
							}
						}
					}

					$print_report_format = return_field_value("format_id", " lib_report_template", "template_name  in($cbo_company_name)  and module_id=2 and report_id=228 and is_deleted=0 and status_active=1");
					$fReportId = explode(",", $print_report_format);
					$fbReportId = $fReportId[0];

					//for row span
					$book_row_arr = array();
					$prod_item_count = array();
					$prod_color_count = array();
					foreach ($Data_Array as $bKey => $bVal) {
						foreach ($bVal as $pKey => $pVal) {
							foreach ($pVal as $cKey => $cVal) {
								foreach ($cVal as $dKey => $dVal) {
									$book_row_arr[$bKey]++;
									$prod_item_count[$bKey][$pKey]++;
									$prod_color_count[$bKey][$pKey][$cKey]++;
								}
							}
						}
					}
					//end for row span

					$i = 1;
					foreach ($Data_Array as $bookOrdeKey => $book_val) {
						// echo $bookOrdeKey;
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						$row_span_val = $book_row_arr[$bookOrdeKey];
						$buyerstylewithinsupplier = array();
						$book_order = array();
						$buyerstylewithinsupplier = explode("#", $buyer_style_within_supplier[$bookOrdeKey]);
						$book_order = explode("**", $bookOrdeKey);
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
							<td class="wrd_brk" width="40" rowspan="<? echo $row_span_val; ?>"><? echo $i; ?></td>
							<td class="wrd_brk" width="80" rowspan="<? echo $row_span_val; ?>">
								<p>
									<?
									/*if($buyerstylewithinsupplier[2] == 1)
	                            		echo $company_arr[$buyerstylewithinsupplier[0]];
	                            	else*/ echo $buyer_arr[$buyerstylewithinsupplier[0]];
									?>
								</p>
							</td>
							<td class="wrd_brk" width="110" rowspan="<? echo $row_span_val; ?>">
								<p><? echo $buyerstylewithinsupplier[1]; ?></p>
							</td>
							<td class="wrd_brk" width="150" rowspan="<? echo $row_span_val; ?>">
								<p><? echo $book_order[0]; ?></p>
							</td>
							<td class="wrd_brk" width="150" rowspan="<? echo $row_span_val; ?>">
								<p><? echo $buyerstylewithinsupplier[4]; ?></p>
							</td>
							<td class="wrd_brk" width="100" rowspan="<? echo $row_span_val; ?>">
								<p><? echo $buyerstylewithinsupplier[9]; ?></p>
							</td>
							<td class="wrd_brk" width="100" rowspan="<? echo $row_span_val; ?>">
								<p>
									<?
									// echo $buyerstylewithinsupplier[4];echo "int ref";
									$int_ref = $job_info_arr[$buyerstylewithinsupplier[4]]["grouping"];
									echo implode(",", array_unique(explode(",", chop($int_ref, ","))));
									?>
								</p>
							</td>
							<td class="wrd_brk" width="110" rowspan="<? echo $row_span_val; ?>">
								<p>
									<? echo "<a href='##' onclick=\"generate_ydwos_report('" . $book_order[0] . "','" . $book_order[2] . "','" . $buyerstylewithinsupplier[10] . "'," . $buyerstylewithinsupplier[8] . ",'" . $buyerstylewithinsupplier[5] . "','" . $buyerstylewithinsupplier[2] . "','" . $fbReportId . "' )\">$buyerstylewithinsupplier[5]</a>"; ?>
								</p>
							</td>

							<td class="wrd_brk" width="100" rowspan="<? echo $row_span_val; ?>">
								<p><? echo change_date_format($buyerstylewithinsupplier[11]); ?></p>
							</td>

							<td class="wrd_brk" width="100" rowspan="<? echo $row_span_val; ?>"><? echo $supplier_arr[$buyerstylewithinsupplier[3]];
																								if ($buyerstylewithinsupplier[7] == 3 || $buyerstylewithinsupplier[7] == 5) {
																									echo $supplier_name = $company_arr[$buyerstylewithinsupplier[3]];
																								} else {
																									echo $supplier_name = $supplier_arr[$buyerstylewithinsupplier[3]];
																								}

																								?></td>

							<?
							foreach ($book_val as $item_id => $item_val) {
							?>

								<td class="wrd_brk" width="100" rowspan="<? echo $prod_item_count[$bookOrdeKey][$item_id]; ?>"><? echo $product_arr[$item_id]["brand"]; ?></td>
								<td class="wrd_brk" width="100" rowspan="<? echo $prod_item_count[$bookOrdeKey][$item_id]; ?>"><? echo $product_arr[$item_id]["product_name_details"]; ?></td>
								<td class="wrd_brk" width="100" rowspan="<? echo $prod_item_count[$bookOrdeKey][$item_id]; ?>"><? echo $product_arr[$item_id]["lot"]; ?></td>
								<?

								foreach ($item_val as $dyeing_color => $dyeing_val) {
									$wo_QntyColor = $source_array[$bookOrdeKey][$item_id][$dyeing_color]["yarn_wo_qty"];
									$wo_dyeing_charge_Color = $source_array[$bookOrdeKey][$item_id][$dyeing_color]["dyeing_charge"];

									if ($txt_process_loss * 1 > 0) {
										$process_loss_qnty = ($wo_QntyColor * $txt_process_loss) / 100;
									} else {
										$process_loss_qnty = 0;
									}

									$bal_qnty = $wo_QntyColor - $grey_issue_arr_color[$bookOrdeKey][$item_id][$dyeing_color];
									$total_wo_QntyColor += $wo_QntyColor;
									$total_grey_issue += $grey_issue_arr_color[$bookOrdeKey][$item_id][$dyeing_color];
									$total_grey_issue_rtn += $grey_issue_rtn_arr_color[$book_order[1]][$item_id][$dyeing_color];
									$total_bal_qnty += $bal_qnty;
									$total_wo_dyeing_charge_Color += $wo_dyeing_charge_Color;
								?>

									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="right"><? echo number_format($wo_QntyColor, 2, '.', ''); ?></td>

									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="center"><? echo change_date_format($grey_issue_arr[$bookOrdeKey][$item_id][$dyeing_color]['issue_date']); ?></td>

									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="center"><? echo $grey_issue_arr[$bookOrdeKey][$item_id][$dyeing_color]['challan_no']; ?></td>
									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="right" title="<? echo $book_order[0] . '=' . $book_order[1]; ?>">
										<a href="#report_details" onClick="openmypage('<? echo $book_order[0]; ?>','<? echo $book_order[1]; ?>','<? echo $item_id; ?>','<? echo $dyeing_color; ?>','delivery_info_popup','Delivery Info Details','<? echo $cbo_company_name; ?>')"><? echo number_format($grey_issue_arr_color[$bookOrdeKey][$item_id][$dyeing_color], 2, '.', ''); ?></a>
									</td>
									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="right">
										<? echo number_format($grey_issue_rtn_arr_color[$book_order[1]][$item_id][$dyeing_color], 2, '.', '');

										?>
									</td>
									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="right"><? echo number_format($bal_qnty, 2, '.', ''); ?></td>
									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="center"><? echo $color_array[$dyeing_color]; ?></td>
									<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="right"><? echo number_format($wo_dyeing_charge_Color, 2); ?></td>
									<?
									$kk = 1;
									$source_company = array();
									$source_company_arr = array();
									$knitting_company = "";
									$challanNo = "";
									$issueDate = "";
									// echo "<pre>";print_r($dyeing_val);
									foreach ($dyeing_val as $dyeprod => $lot_Q) {
										$knitting_company = "";
										if ($lot_Q["fab_booking_no"]) {
											$booking_number = $lot_Q["fab_booking_no"];
										} else {
											$booking_number = $lot_Q["sales_booking_no"];
										}
										$booming_no =
											$issue_balance =  $lot_Q["lot_qnty"] - ($issue_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["Qnty"] - $issue_rtn_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["Qnty"]);
										$source_company_str = $issue_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["source_company"];
										$source_company_arr = array_unique(explode("*", chop($source_company_str, "*")));
										foreach ($source_company_arr as $sRVal) {
											$source_company = explode("_", $sRVal);
											if ($source_company[0] == 1) {
												$knitting_company .= $company_arr[$source_company[1]] . ",";
											} elseif ($source_company[0] == 3) {
												$knitting_company .= $supplier_arr[$source_company[1]] . ",";
											}
										}
										$knitting_company = implode(",", array_unique(explode(",", chop($knitting_company, ","))));

										$challan_no = "";
										$challan_no_str = $issue_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["challan_no"];
										$challan_no_arr = array_unique(explode("*", chop($challan_no_str, "*")));
										foreach ($challan_no_arr as $cVal) {
											$challan_no .= $cVal . ",";
										}
										$challanNo = implode(",", array_unique(explode(",", chop($challan_no, ","))));

										$issue_date = "";
										$issue_date_str = $issue_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["issue_date"];

										$issue_date_arr = array_unique(explode("*", chop($issue_date_str, "*")));
										foreach ($issue_date_arr as $idVal) {
											$issue_date .= change_date_format($idVal) . ",<br>";
										}
										$issueDate = implode(",", array_unique(explode(",", chop($issue_date, ",<br>"))));

									?>
										<td class="wrd_brk" width="100" align="center"><? echo ($lot_Q["lot_qnty"] == '') ? '' : change_date_format($lot_Q['receive_date']); ?></td>
										<td class="wrd_brk" width="100" align="center"><? echo ($lot_Q["lot_qnty"] == '') ? '' : $lot_Q['challan_no']; ?></td>
										<td class="wrd_brk" width="100" align="center"><? echo ($lot_Q["lot_qnty"] == '') ? '' : $lot_Q['dyelot']; ?></td>
										<td class="wrd_brk" width="100" align="right">
											<a href="#report_details" onClick="openmypage('<? echo $book_order[0]; ?>','<? echo $book_order[1]; ?>','<? echo $lot_Q["dye_product_id"]; ?>','<? echo $dyeing_color; ?>','rcv_info_popup','Receive Info Details','<? echo $cbo_company_name; ?>')"><? echo number_format($lot_Q["lot_qnty"], 2, '.', ''); ?></a>

										</td>
										<?
										if ($kk == 1) {
											$wo_QntyColorRcv  = $wo_QntyColor - $prod_lot_qnty[$bookOrdeKey][$item_id][$dyeing_color]["prod_lot_qnty"] - $grey_issue_rtn_arr_color[$book_order[1]][$item_id][$dyeing_color] - $process_loss_qnty;
											$total_wo_QntyColorRcv += $wo_QntyColorRcv;
										?>
											<td class="wrd_brk" width="100" rowspan="<? echo $prod_color_count[$bookOrdeKey][$item_id][$dyeing_color]; ?>" align="right"><? echo number_format($wo_QntyColorRcv, 2, ".", ""); ?></td>
										<?
										}
										?>
										<td class="wrd_brk" width="100" align="center"><? echo chop($lot_Q["remarks"], ","); ?></td>
										<td class="wrd_brk" width="100" align="center"><? echo $issueDate; ?></td>
										<td class="wrd_brk" width="100" align="center" title="<? echo $lot_Q["dye_product_id"]; ?> "><? echo $challanNo; ?></td>
										<td class="wrd_brk" width="100" align="right" title="<? echo $lot_Q["dye_product_id"]; ?>">
											<a href="#report_details" onClick="openmypageknitting('<? echo $lot_Q["dye_product_id"]; ?>','iss_to_knit_info_popup','Knitting Info Details','<? echo $cbo_company_name; ?>')"><? echo number_format($issue_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["Qnty"], 2, ".", ""); ?></a>

										</td>
										<td class="wrd_brk" width="100" align="right" title="<? echo $lot_Q["dye_product_id"]; ?>">
											<? echo number_format($issue_rtn_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["Qnty"], 2, ".", ""); ?>

										</td>
										<td class="wrd_brk" width="100" align="right"><? echo number_format($issue_balance, 2, ".", ""); ?></td>
										<td class="wrd_brk" width="" align="center"><? echo $knitting_company; ?></td>
						</tr>
		<?
										$total_lot_Q += $lot_Q["lot_qnty"];
										$total_issue_knitting += $issue_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["Qnty"];
										$total_issue_rtn_knitting += $issue_rtn_knitting_arr[$booking_number][$lot_Q["sales_order_id"]][$lot_Q["dye_product_id"]]["Qnty"];
										$total_issue_balance += $issue_balance;
										$kk++;
									}
								}
							}
							$i++;
						}
		?>
		<tfoot>
			<th colspan="13" align="right">Total</th>
			<th align="right"><? echo number_format($total_wo_QntyColor, 2, ".", ""); ?></th>
			<th align="right">&nbsp;</th>
			<th align="right">&nbsp;</th>
			<th align="right"><? echo number_format($total_grey_issue, 2, ".", ""); ?></th>
			<th align="right"><? echo number_format($total_grey_issue_rtn, 2, ".", ""); ?></th>
			<th align="right"><? echo number_format($total_bal_qnty, 2, ".", ""); ?></th>
			<th align="right">&nbsp;</th>
			<th align="right"><? echo number_format($total_wo_dyeing_charge_Color, 2, ".", ""); ?></th>
			<th align="right">&nbsp;</th>
			<th align="right">&nbsp;</th>
			<th align="right">&nbsp;</th>
			<th align="right"><? echo number_format($total_lot_Q, 2, ".", ""); ?></th>
			<th align="right"><? echo number_format($total_wo_QntyColorRcv, 2, ".", ""); ?></th>
			<th align="right">&nbsp;</th>
			<th align="right">&nbsp;</th>
			<th align="right">&nbsp;</th>
			<th align="right"><? echo number_format($total_issue_knitting, 2, ".", ""); ?></th>
			<th align="right"><? echo number_format($total_issue_rtn_knitting, 2, ".", ""); ?></th>
			<th align="right"><? echo number_format($total_issue_balance, 2, ".", ""); ?></th>
			<th align="right">&nbsp;</th>
		</tfoot>
				</table>
			</div>
		</fieldset>
	<?
	}
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
	echo "$total_data####$filename";
	exit();
}

if ($action == "delivery_info_popup") {
	echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

	$grey_issue_res = sql_select("SELECT a.id, a.issue_number, a.store_id, sum(b.cons_quantity) as issue_qnty, a.issue_date, a.challan_no from inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.issue_basis = 1 and a.issue_purpose = 2 and b.transaction_type = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and a.company_id in($company_id) and a.booking_id=$booking_id and b.job_no='$job_no' and b.prod_id=$item_id and b.dyeing_color_id=$dyeing_color group by a.id, a.issue_number, a.store_id, a.issue_date, a.challan_no");

	?>
	<script>
		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}
	</script>
	<div style="width:560px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" /></div>
	<fieldset style="width:550px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0">

				<thead>
					<th width="30">SL</th>
					<th width="100">Issue No</th>
					<th width="100">Date</th>
					<th width="100">Challan No</th>
					<th width="100">Issue Qnty</th>
					<th>Store</th>
				</thead>
				<?


				$i = 1;
				$totan_iss_qnty = 0;
				foreach ($grey_issue_res as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf('issue_number')]; ?></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td width="100" align="center"><? echo $row[csf('challan_no')]; ?></td>
						<td width="100" align="right"><? echo number_format($row[csf('issue_qnty')], 2); ?></td>
						<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>

					</tr>
				<?
					$i++;
					$totan_iss_qnty += $row[csf('issue_qnty')];
				}
				?>
				<tr style="font-weight:bold">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right">Total</td>
					<td align="right"><? echo number_format($totan_iss_qnty, 2); ?></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "rcv_info_popup") {
	echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

	$lot_res =  sql_select("SELECT b.booking_id, sum(a.cons_quantity) as rcv_qnty, b.receive_date, b.challan_no, b.recv_number, b.supplier_id, b.lc_no, b.receive_date from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id in($company_id) and a.job_no='$job_no' and b.booking_id=$booking_id and c.id=$item_id and c.color=$dyeing_color and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1 group by b.booking_id, b.receive_date, b.challan_no, b.recv_number, b.supplier_id, b.lc_no, b.receive_date ");

?>
	<script>
		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}
	</script>
	<div style="width:560px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" /></div>
	<fieldset style="width:550px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0">

				<thead>
					<th width="30">SL</th>
					<th width="100">MRR No</th>
					<th width="100">Supplier Name</th>
					<th width="100">Challan No</th>
					<th width="100">Receive Date</th>
					<th>Receive Qnty</th>
				</thead>
				<?


				$i = 1;
				$totan_rcv_qnty = 0;
				foreach ($lot_res as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf('recv_number')]; ?></td>
						<td width="100" align="center"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
						<td width="100" align="center"><? echo $row[csf('challan_no')]; ?></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td align="right"><? echo number_format($row[csf('rcv_qnty')], 2); ?></td>

					</tr>
				<?
					$i++;
					$totan_rcv_qnty += $row[csf('rcv_qnty')];
				}
				?>
				<tr style="font-weight:bold">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><b>Total:</b></td>
					<td align="right"><b><? echo number_format($totan_rcv_qnty, 2); ?></b></td>
				</tr>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

if ($action == "iss_to_knit_info_popup") {
	echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$com_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');



	if ($db_type == 0) $year_field = " YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "  to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";

	// echo "SELECT a.id, a.issue_number, $year_field, sum(b.cons_quantity) as iss_qnty, a.booking_no,a.booking_id, b.prod_id, a.knit_dye_company, a.issue_date, a.challan_no, a.issue_purpose from inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.issue_basis in (3,4,8) and a.issue_purpose = 1 and b.transaction_type = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and a.entry_form = 3 and a.company_id = $company_id and b.prod_id = $booking_id group by a.id, a.issue_number, a.booking_no,a.booking_id, b.prod_id, a.knit_dye_company, a.issue_date, a.challan_no, a.issue_purpose, a.insert_date order by a.id desc";
	$issue_knitting = sql_select("SELECT a.id, a.issue_number, $year_field, sum(b.cons_quantity) as iss_qnty, a.booking_no,a.booking_id, b.prod_id, a.knit_dye_company, a.issue_date, a.challan_no, a.issue_purpose from inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.issue_basis in (3,4,8) and a.issue_purpose = 1 and b.transaction_type = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and a.entry_form = 3 and a.company_id in( $company_id) and b.prod_id = $booking_id group by a.id, a.issue_number, a.booking_no,a.booking_id, b.prod_id, a.knit_dye_company, a.issue_date, a.challan_no, a.issue_purpose, a.insert_date order by a.id desc");

?>
	<script>
		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}
	</script>
	<div style="width:760px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" /></div>
	<fieldset style="width:750px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">

				<thead>
					<th width="30">SL</th>
					<th width="100">Issue No</th>
					<th width="100">Year</th>
					<th width="100">Date</th>
					<th width="100">Purpose</th>
					<th width="100">Challan No</th>
					<th width="100">Issue Qnty</th>
					<th>knitting Comp.</th>
				</thead>
				<?


				$i = 1;
				$totan_isstoknit_qnty = 0;
				foreach ($issue_knitting as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf('issue_number')]; ?></td>
						<td width="100" align="center"><? echo $row[csf('year')]; ?></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('issue_date')]) ?></td>
						<td width="100" align="center"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
						<td width="100" align="center"><? echo $row[csf('challan_no')]; ?></td>
						<td width="100" align="right"><? echo number_format($row[csf('iss_qnty')], 2); ?></td>
						<td align="center"><? echo $com_arr[$row[csf('knit_dye_company')]]; ?></td>

					</tr>
				<?
					$i++;
					$totan_isstoknit_qnty += $row[csf('iss_qnty')];
				}
				?>
				<tr style="font-weight:bold">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><b>Total:</b></td>
					<td align="right"><b><? echo number_format($totan_isstoknit_qnty, 2); ?></b></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	</fieldset>
<?
	exit();
}

?>