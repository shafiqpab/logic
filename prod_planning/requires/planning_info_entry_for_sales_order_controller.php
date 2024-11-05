<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission = $_SESSION['page_permission'];

// get buyer condition according to priviledge
if ($_SESSION['logic_erp']["data_level_secured"] == 1)
{
	if ($_SESSION['logic_erp']["buyer_id"] != "")
	{
		$buyer_id_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
	}
	else
	{
		$buyer_id_cond = "";
	}
}
else
{
	$buyer_id_cond = "";
}

if ($action == "overlapped_popup")
{
	echo load_html_head_contents("Overlapped Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function fnc_close()
		{
			var plan_ids = '';
			var row_num = $('#tbl_list_search tbody tr').length;
			for (var j = 1; j <= row_num; j++)
			{
				var plan_id = $('#planId_' + j).text();
				if ($('#check_' + j).is(':checked'))
				{
					if (plan_ids == "")
					{
						plan_ids = plan_id;
					}
					else
					{
						plan_ids += "," + plan_id;
					}
				}
			}

			$('#hidden_plan_ids').val(plan_ids);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:350px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:100%;">
				<legend>Overlapped Data</legend>
				<table cellpadding="0" cellspacing="0" width="340" class="rpt_table" border="1" rules="all"
				id="tbl_list_search">
				<thead>
					<th><input type="hidden" name="hidden_plan_ids" id="hidden_plan_ids" class="text_boxes" value="">
					</th>
					<th>Machine Plan Id</th>
					<th>Plan Qty</th>
				</thead>
				<tbody>
					<?
					$overlapped_datas = explode(",", $overlapped_data);
					$i = 0;
					foreach ($overlapped_datas as $datas) {
						$i++;
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$data = explode("_", $datas);
						echo '<tr bgcolor="' . $bgcolor . '"><td align="center" valign="middle"><input type="checkbox" name="check[]" id="check_' . $i . '"></td>';
						echo '<td id="planId_' . $i . '">' . $data[0] . '</td>';
						echo '<td align="right">' . $data[2] . '</td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
			<table width="340" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" valign="bottom">
						<input type="button" name="close" onClick="fnc_close()" class="formbutton" value="Close"
						style="width:100px"/>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "load_drop_down_buyer")
{
	$data = explode("_", $data);
	if ($data[1] == 1)
	{
		echo create_drop_down("cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 0);
	}
	else if ($data[1] == 2)
	{
		echo create_drop_down("cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='" . $data[0] . "' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_id_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	}
	else
	{
		echo create_drop_down("cbo_buyer_name", 140, $blank_array, "", 1, "-- Select Buyer --", 0, "");
	}
	exit();
}

if ($action == "load_drop_down_floor")
{
	echo create_drop_down("cbo_floor_id", 160, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}
if($action=="check_collar_cuff_variable")
{
	$sql_info ="select coller_cuf_size_planning from variable_settings_production where company_name='$data' and variable_list=53 and status_active=1 and is_deleted=0";
	//echo $sql_info;// die;
	$result_dtls = sql_select($sql_info);
	$collarCuff=$result_dtls[0]['COLLER_CUF_SIZE_PLANNING'];
	echo "1"."_".$collarCuff;
	exit();
}

if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$datediff = datediff('d', str_replace("'", "", $txt_date_from), str_replace("'", "", $txt_date_to));
	if (str_replace("'", "", $cbo_floor_id) == 0) $floor_cond = ""; else $floor_cond = " and floor_id=$cbo_floor_id";
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$machine_data_array = array();
	$machine_data = sql_select("select id, floor_id, machine_no, upper(dia_width) as dia_width, gauge, prod_capacity from lib_machine_name where company_id=$cbo_company_name and category_id=1 and status_active=1 and is_deleted=0 $floor_cond order by floor_id, dia_width");//, cast(machine_no as unsigned)
	foreach ($machine_data as $row)
	{
		$machine_data_array[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$machine_data_array[$row[csf('id')]]['floor'] = $row[csf('floor_id')];
		$machine_data_array[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$machine_data_array[$row[csf('id')]]['gg'] = $row[csf('gauge')];
		$machine_data_array[$row[csf('id')]]['capacity'] = $row[csf('prod_capacity')];
	}

	$tbl_width = 410 + $datediff * 60;
	$date_array = array();
	$months_array = array();
	$months_width_array = array();
	$header_tr = '';
	$s = 1;
	for ($j = 0; $j < $datediff; $j++)
	{
		$newdate = add_date(str_replace("'", "", $txt_date_from), $j);
		$month = date("M Y", strtotime($newdate));
		$dayname = substr(date("D", strtotime($newdate)), 0, 1);
		$date_array[$j] = $newdate;
		if ($s == $datediff) $width = ""; else $width = "width=60";
		$header_tr .= '<td ' . $width . ' class="top_headerss">' . date("d", strtotime($newdate)) . '<br>' . $dayname . '</td>';

		$months_array[$month] += 1;
		$months_width_array[$month] += 60;
		$s++;
	}
	//print_r($months_array);
	ob_start();
	?>
	<fieldset style="width:<? echo $tbl_width + 20; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $datediff + 5; ?>" style="font-size:16px">
					<strong><? echo $report_title; ?></strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>">
				<thead>
					<tr>
						<th rowspan="2" width="40" class="top_header">SL</th>
						<th rowspan="2" width="100" class="top_header">Floor No</th>
						<th rowspan="2" width="70" class="top_header">Machine Dia</th>
						<th rowspan="2" width="70" class="top_header">Machine GG</th>
						<th rowspan="2" width="70" class="top_header">Machine No</th>
						<?
						foreach ($months_array as $month => $days) {
							echo '<th colspan="' . $days . '" width="' . $months_width_array[$month] . '" class="top_header">' . $month . '</th>';
						}
						?>
					</tr>
					<tr>
						<? echo $header_tr; ?>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$machine_date_array = array();
					$tot_capacity = 0;
					$tot_qnty_array = array();
					$dataArray = sql_select("select a.capacity, a.no_of_days, a.start_date, a.distribution_qnty, a.end_date, b.dtls_id, b.machine_id, b.distribution_date, b.fraction_date, sum(b.days_complete) as days_complete, sum(b.qnty) as qnty, 'Y' as status, b.machine_plan_id from ppl_planning_info_machine_dtls a, ppl_entry_machine_datewise b where a.id=b.machine_plan_id and a.is_sales=1 and b.is_sales=1 group by b.machine_id, b.distribution_date, b.dtls_id, b.fraction_date, b.machine_plan_id, a.no_of_days, a.start_date, a.end_date, a.capacity, a.distribution_qnty");
					foreach ($dataArray as $row) {
						$distribution_date = date("Y-m-d", strtotime($row[csf('distribution_date')]));
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['st'] = $row[csf('status')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['fr'] = $row[csf('fraction_date')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['dc'] = $row[csf('days_complete')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['qnty'] = $row[csf('qnty')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['dtls_id'] = $row[csf('dtls_id')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['machine_plan_id'] = $row[csf('machine_plan_id')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['machine_plan_ids'] .= $row[csf('machine_plan_id')] . ",";
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['overlapped_data'] .= $row[csf('machine_plan_id')] . "_" . $row[csf('dtls_id')] . "_" . $row[csf('qnty')] . "_" . $row[csf('no_of_days')] . "_" . $row[csf('start_date')] . "_" . $row[csf('end_date')] . "_" . $row[csf('capacity')] . ",";
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['duration'] = $row[csf('no_of_days')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['start_date'] = $row[csf('start_date')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['end_date'] = $row[csf('end_date')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['capacity'] = $row[csf('capacity')];
						$machine_date_array[$row[csf('machine_id')]][$distribution_date]['plan_qty'] = $row[csf('distribution_qnty')];
					}

			/*$capacity_arr=array();
					$dataArray=sql_select("select dtls_id, machine_id, capacity from ppl_planning_info_machine_dtls");
					foreach ($dataArray as $row)
					{
						$capacity_arr[$row[csf('machine_id')]][$row[csf('dtls_id')]]=$row[csf('capacity')];
					}*/
			//var_dump($machine_date_array);
					foreach ($machine_data_array as $key => $val) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$tot_capacity += $machine_data_array[$key]['capacity'];

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
							<td class="left_td_header"><p><? echo $i; ?></td>
								<td class="left_td_header"><p><? echo $floor_arr[$machine_data_array[$key]['floor']]; ?>&nbsp;</p>
								</td>
								<td class="left_td_header"><p><? echo $machine_data_array[$key]['dia']; ?>&nbsp;</p></td>
								<td class="left_td_header"><p><? echo $machine_data_array[$key]['gg']; ?>&nbsp;</p></td>
								<td class="left_header"><p><? echo $machine_data_array[$key]['no']; ?>&nbsp;</p></td>
								<?
								$s = 1;
								$prev_plan_id = '';
								foreach ($date_array as $date) {
									if ($s == count($date_array)) $width = ""; else $width = "width=90";

									$class = "verticalStripes1";
									$td_color = '';
									$suffix = "";
									$is_planned = 0;
									$is_partial = 0;
									$style = '';
									$radious = '';
									$is_overlapped = 0;
									$overlapped_data = 0;

									$qnty = $machine_date_array[$key][$date]['qnty'];
									$dtls_id = $machine_date_array[$key][$date]['dtls_id'];
									$machine_plan_id = $machine_date_array[$key][$date]['machine_plan_id'];
									$start_date = $machine_date_array[$key][$date]['start_date'];
									$end_date = $machine_date_array[$key][$date]['end_date'];
									$duration = $machine_date_array[$key][$date]['duration'];
									$plan_qty = $machine_date_array[$key][$date]['plan_qty'];

									$planDtlsData = $key . "**" . $date . "**" . $dtls_id . "**" . $machine_plan_id;
									if ($qnty > 0) {
										$is_planned = 1;
										$capacity = $machine_date_array[$key][$date]['capacity'];

										$machine_plan_ids = array_unique(explode(",", chop($machine_date_array[$key][$date]['machine_plan_ids'], ',')));
										if (count($machine_plan_ids) > 1) {
											$overlapped_data = chop($machine_date_array[$key][$date]['overlapped_data'], ',');
											$is_overlapped = 1;
											$class = "verticalStripes1 verticalStripes1_crossed";
											if ($prev_plan_id != $machine_plan_id) {
												$style = 'style="border-radius:50% 0 0 50%; background:repeating-linear-gradient(45deg, #606dbc, #606dbc 2px, #465298 2px, #465298 4px)"';
												$prev_plan_id = $machine_plan_id;
											} else {
												$style = 'style="background:repeating-linear-gradient(45deg, #606dbc, #606dbc 2px, #465298 2px, #465298 4px)"';
											}
										} else {
											$class = "verticalStripes1 verticalStripes1_plan";
											if ($prev_plan_id != $machine_plan_id) {
												$style = 'style="border-radius:50% 0 0 50%;"';
												$prev_plan_id = $machine_plan_id;
											} else {
												$style = '';
											}
										}

										if ($machine_date_array[$key][$date]['fr'] == 1) {
											$is_partial = 1;
										}
									} else {
										$capacity = $machine_data_array[$key]['capacity'];
									}

									//if($s==$datediff) $width=""; else $width="width=60"; '.$width.'
									//echo '<td align="right" program_id="id" bgcolor="'.$td_color.'" '.$width.' class="'.$class.'"><a href="##" style="color:#000" onclick="openmypage('.$machine_date_array[$key][$date]['dtls_id'].')">'.$qnty.'</a>&nbsp;'.$suffix.'</td>';

									$placeholder = '<span style="font-size:8px; color:#333;">' . date("d-m", strtotime($date)) . "<br>" . $machine_data_array[$key]['no'] . '</span>';
									$tdate = date("dmY", strtotime($date));
									$idd = "-" . $key . "-" . $tdate;
									$start_td_id = "tdbody-" . $key . "-" . date("dmY", strtotime($start_date));

									echo '<td id="tdbody' . $idd . '" name="tdbody' . $i . '_' . $s . '" align="center" onMouseOver="showmenu(this.id)" plan_group="' . $is_planned . '" planDtls="' . $planDtlsData . '" duration="' . $duration . '" dtls_id="' . $dtls_id . '" plan_id="' . $machine_plan_id . '" start_date="' . change_date_format($start_date) . '" end_date="' . change_date_format($end_date) . '" is_partial="' . $is_partial . '" today_plan_qnty="' . $qnty . '" capacity="' . $capacity . '" plan_qnty="' . $plan_qty . '" isnew="0" upd_id="' . $machine_plan_id . '" is_overlapped="' . $is_overlapped . '" overlapped_data="' . $overlapped_data . '" start_td_id="' . $start_td_id . '" class="' . $class . '" ' . $style . '>' . $placeholder . '</td>';

									$tot_qnty_array[$date] += $qnty;

									$s++;
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
		</fieldset>
		<?
		exit();
	}

if ($action == "plan_deails")
{
	echo load_html_head_contents("Report Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_array = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

	?>
	<fieldset style="width:570px; margin-left:7px">
		<b>Order Details</b>
		<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0">
			<thead>
				<th width="40">SL</th>
				<th width="120">Job No</th>
				<th width="130">Buyer</th>
				<th width="140">Order No</th>
				<th>Shipment Date</th>
			</thead>
		</table>
		<div style="width:567px; max-height:170px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0">
				<?
				$i = 1;
				$sql = "select a.buyer_id, b.job_no_mst, b.po_number, b.pub_shipment_date from ppl_planning_entry_plan_dtls a, wo_po_break_down b where a.po_id=b.id and a.dtls_id=$program_id order by b.id, b.pub_shipment_date";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="130"><p><? echo $buyer_array[$row[csf('buyer_id')]]; ?></p></td>
						<td width="140"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<br/>
		<b>Fabric Details</b>
		<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0">
			<thead>
				<th width="70">Fabric Dia</th>
				<th width="60">GSM</th>
				<th width="160">Description</th>
				<th width="60">Stitch Length</th>
				<th width="90">Color Range</th>
				<th>Fabric Color</th>
			</thead>
			<?
			$query = "select a.fabric_desc, a.gsm_weight, upper(b.fabric_dia) as fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id=$program_id";
			$dataArray = sql_select($query);
			$color = '';
			$color_id = explode(",", $dataArray[0][csf('color_id')]);
			foreach ($color_id as $val) {
				if ($color == '') $color = $color_array[$val]; else $color .= "," . $color_array[$val];
			}
			?>
			<tr bgcolor="#FFFFFF">
				<td width="70"><p><? echo $dataArray[0][csf('fabric_dia')]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $dataArray[0][csf('gsm_weight')]; ?>&nbsp;</p></td>
				<td width="160"><p><? echo $dataArray[0][csf('fabric_desc')]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="90"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
				<td><p><? echo $color; ?>&nbsp;</p></td>
			</tr>
		</table>
		<br/>
		<b>TNA Details</b>
		<table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0">
			<thead>
				<th width="170">Kniting Start Date</th>
				<th>Kniting End Date</th>
			</thead>
			<tr bgcolor="#FFFFFF">
				<td align="center">
					<p><? if ($dataArray[0][csf('start_date')] != "0000-00-00") echo change_date_format($dataArray[0][csf('start_date')]); ?>
				&nbsp;</p></td>
				<td align="center">
					<p><? if ($dataArray[0][csf('end_date')] != "0000-00-00") echo change_date_format($dataArray[0][csf('end_date')]); ?>
				&nbsp;</p></td>
			</tr>
		</table>
	</fieldset>
	<?
	exit();
}

if ($action == "booking_item_details_popup")
{
	echo load_html_head_contents("Planning Info Entry", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

	//if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';

	function show_details(type) {
		if (form_validation('cbo_company_name', 'Company') == false) {
			return;
		}

		if (type == 2) {
			if (form_validation('txt_booking_no', 'Booking No.') == false) {
				return;
			}
		}

		var data = "action=booking_item_details" + get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*hide_job_id*txt_booking_no*cbo_planning_status', "../../") + '&type=' + type;

		freeze_window(5);
		http.open("POST", "planning_info_entry_for_sales_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_show_details_reponse;
		//show_list_view(cbo_company_id, 'booking_item_details', 'list_container_fabric_desc', 'requires/planning_info_entry_for_sales_order_controller', '');
	}

	function fn_show_details_reponse() {
		if (http.readyState == 4) {
			var response = trim(http.responseText);
			$('#list_container_fabric_desc').html(response);
			set_all_onclick();
			show_msg('18');
			release_freezing();
		}
	}

	function openmypage_job() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var page_link = 'planning_info_entry_for_sales_order_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID;
		var title = 'Style Ref./ Job No. Search';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0', '../../');
		emailwindow.onclose = function () {
			var theform = this.contentDoc.forms[0];
			var job_no = this.contentDoc.getElementById("hide_job_no").value;
			var job_id = this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}
	function openmypage_internal_ref() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var page_link = 'planning_info_entry_for_sales_order_controller.php?action=internal_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID;
		var title = 'IR/IB Ref./ Job No. Search';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0', '../../');
		emailwindow.onclose = function () {
			var theform = this.contentDoc.forms[0];
			var job_no = this.contentDoc.getElementById("hide_job_no").value;
			var job_id = this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

	function openmypage_booking() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var page_link = 'planning_info_entry_for_sales_order_controller.php?action=booking_no_search_popup&companyID=' + companyID;
		var title = 'Booking Search';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0', '../../');
		emailwindow.onclose = function () {
			var theform = this.contentDoc.forms[0];
			var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;

			$('#txt_booking_no').val(booking_no);
		}
	}

	function openmypage_prog() {

		var type = $('#txt_type').val();
		if (type == 2 || type == 3) {
			alert("Not Allow");
			return;
		}
		var tot_row = $('#tbl_list_search tbody tr').length;
		var data = '';
		var i = 0;
		var selected_row = 0;
		var currentRowColor = '';
		var booking_no = '';
		var body_part_id = '';
		var fabric_typee = '';
		var buyer_id = '';
		var job_id = '';
		var dia = '';
		var gsm = '';
		var desc = '';
		var booking_qnty = 0;
		var plan_id = '';
		var determination_id = '';
		var job_dtls_id = '';
		var within_group = '';
		var color_type_id = '';

		var companyID = $('#company_id').val();

		for (var j = 1; j <= tot_row; j++) {
			currentRowColor = document.getElementById('tr_' + j).style.backgroundColor;
			if (currentRowColor == 'yellow') {
				i++;
				selected_row++;

				if (data == '') {
					var booking=$('#bookingNo_' + j).val();
                    booking=encodeURIComponent(String(booking));

					data = booking + "**"
					+ $('#job_id_' + j).val() + "**"
					+ $('#withinGroup_' + j).val() + "**"
					+ $('#job_dtls_id_' + j).val() + "**"
					+ $('#buyer_id_' + j).val() + "**"
					+ $('#body_part_id_' + j).val() + "**"
					+ $('#fabric_typee_' + j).val() + "**"
					+ $('#desc_' + j).text() + "**"
					+ $('#gsm_weight_' + j).text() + "**"
					+ $('#dia_width_' + j).text() + "**"
					+ $('#determination_id_' + j).val() + "**"
					+ $('#booking_qnty_' + j).text() + "**"
					+ $('#color_type_id_' + j).val() + "**"
					+ $('#sales_order_dtls_id' + j).val();
				}
				else {
					 var booking=$('#bookingNo_' + j).val();
                     booking=encodeURIComponent(String(booking));

					data += "_" + booking + "**"
					+ $('#job_id_' + j).val() + "**"
					+ $('#withinGroup_' + j).val() + "**"
					+ $('#job_dtls_id_' + j).val() + "**"
					+ $('#buyer_id_' + j).val() + "**"
					+ $('#body_part_id_' + j).val() + "**"
					+ $('#fabric_typee_' + j).val() + "**"
					+ $('#desc_' + j).text() + "**"
					+ $('#gsm_weight_' + j).text() + "**"
					+ $('#dia_width_' + j).text() + "**"
					+ $('#determination_id_' + j).val() + "**"
					+ $('#booking_qnty_' + j).text() + "**"
					+ $('#color_type_id_' + j).val() + "**"
					+ $('#sales_order_dtls_id' + j).val();
				}

				booking_no = $('#bookingNo_' + j).val();
				gsm = $('#gsm_weight_' + j).text();
				dia = $('#dia_width_' + j).text();
				desc = $('#desc_' + j).text();
				desc = encodeURIComponent(String(desc));
				within_group = $('#withinGroup_' + j).val();
				buyer_id = $('#buyer_id_' + j).val();
				job_id = $('#job_id_' + j).val();
				determination_id = $('#determination_id_' + j).val();
				body_part_id = $('#body_part_id_' + j).val();
				color_type_id = $('#color_type_id_' + j).val();
				fabric_typee = $('#fabric_typee_' + j).val();
				pre_cost_id_ = $('#pre_cost_id_' + j).val();

				if (plan_id == '') plan_id = $('#plan_id_' + j).text();

				if (job_dtls_id == '') job_dtls_id = $('#job_dtls_id_' + j).val(); else job_dtls_id += "," + $('#job_dtls_id_' + j).val();
				if (sales_order_dtls_id == '') sales_order_dtls_id = $('#sales_order_dtls_id' + j).val(); else sales_order_dtls_id += "_" + $('#sales_order_dtls_id' + j).val();
				booking_qnty = booking_qnty * 1 + $('#booking_qnty_' + j).text() * 1;
			}
		}

		if (selected_row < 1) {
			alert("Please Select At Least One Item");
			return;
		}

		var page_link = 'planning_info_entry_for_sales_order_controller.php?action=prog_qnty_popup&gsm=' + gsm + '&dia=' + dia + '&desc=' + desc + '&within_group=' + within_group + '&job_id=' + job_id + '&booking_qnty=' + encodeURIComponent(String(booking_no)) + '&companyID=' + companyID + '&data="' + data + '"' + '&plan_id=' + plan_id + '&determination_id=' + determination_id + '&booking_no=' + booking_no + '&body_part_id=' + body_part_id + '&fabric_type=' + fabric_typee + '&pre_cost_id=' + pre_cost_id + '&buyer_id=' + buyer_id + '&job_dtls_id=' + job_dtls_id + '&color_type_id=' + color_type_id + '&sales_order_dtls_id=' + sales_order_dtls_id;
		var title = 'Program Qnty Info';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=430px,center=1,resize=1,scrolling=0', '../../');
		/*emailwindow.onclose=function()
		 {
		 var theform=this.contentDoc.forms[0];
		 var program_qnty=this.contentDoc.getElementById("txt_program_qnty").value;

	 }*/
 }

 function selected_row(rowNo) {
	var color = document.getElementById('tr_' + rowNo).style.backgroundColor;
	var bookingNo = $('#bookingNo_' + rowNo).val();
	var determinationId = $('#determination_id_' + rowNo).val();
	var widthDiaType = $('#fabric_typee_' + rowNo).val();
	var gsm = $('#gsm_weight_' + rowNo).text();
	var fabricDia = $('#dia_width_' + rowNo).text();
	var plan_id = $('#plan_id_' + rowNo).text();
	var color_type_id = $('#color_type_id_' + rowNo).val();
	var job_id = $('#job_id_' + rowNo).val();

	var stripe_or_not = '';

	if (color_type_id == 2 || color_type_id == 3 || color_type_id == 4) {
			stripe_or_not = 1;//1 means stripe yes
		}
		else {
			stripe_or_not = 0;//0 means stripe no
		}

		var currentRowColor = '';
		var check = '';
		if (color != 'yellow') {
			var tot_row = $('#tbl_list_search tbody tr').length;
			for (var i = 1; i <= tot_row; i++) {
				if (i != rowNo) {
					currentRowColor = document.getElementById('tr_' + i).style.backgroundColor;
					if (currentRowColor == 'yellow') {
						var bookingNoCur = $('#bookingNo_' + i).val();
						var determinationIdCur = $('#determination_id_' + i).val();
						var widthDiaTypeCur = $('#fabric_typee_' + i).val();
						var gsmCur = $('#gsm_weight_' + i).text();
						var fabricDiaCur = $('#dia_width_' + i).text();
						var plan_idCur = $('#plan_id_' + i).text();
						var color_type_idCur = $('#color_type_id_' + i).val();


						var job_idCur = $('#job_id_' + i).val();
						var sales_order_dtls_id = $('#sales_order_dtls_id' + i).val();

						var stripe_or_notCur = '';
						if (color_type_idCur == 2 || color_type_idCur == 3 || color_type_idCur == 4) {
							stripe_or_notCur = 1;//1 means stripe yes
						}
						else {
							stripe_or_notCur = 0;//0 means stripe no
						}

						if (plan_id == "" || plan_idCur == "") {
							if (!(bookingNo == bookingNoCur && determinationId == determinationIdCur && widthDiaType == widthDiaTypeCur && gsm == gsmCur && fabricDia == fabricDiaCur && stripe_or_not == stripe_or_notCur && job_id == job_idCur && color_type_id == color_type_idCur)) {
								alert("Please Select Same Description");
								return;
							}
						}
						else {
							if (!(plan_id == plan_idCur && bookingNo == bookingNoCur && determinationId == determinationIdCur && widthDiaType == widthDiaTypeCur && gsm == gsmCur && fabricDia == fabricDiaCur && stripe_or_not == stripe_or_notCur && job_id == job_idCur && color_type_id == color_type_idCur)) {
								alert("Please Select Same Description and Same Plan ID");
								return;
							}
						}
					}
				}
			}

			$('#tr_' + rowNo).css('background-color', 'yellow');
		}
		else {
			var reqsn_found_or_not = $('#reqsn_found_or_not_' + rowNo).val();
			if (reqsn_found_or_not == 0) {
				$('#tr_' + rowNo).css('background-color', '#FFFFCC');
			}
			else {
				alert("Requisition Found Against This Planning. So Change Not Allowed");
				return;
			}
		}
	}

	function delete_prog() {
		var program_ids = "";
		var total_tr = $('#tbl_list_search tr').length;
		for (i = 1; i < total_tr; i++) {
			try {
				if ($('#tbl_' + i).is(":checked")) {
					program_id = $('#promram_id_' + i).val();
					if (program_ids == "") program_ids = program_id; else program_ids += ',' + program_id;
				}
			}
			catch (e) {
				//got error no operation
			}
		}

		if (program_ids == "") {
			alert("Please Select At Least One Program");
			return;
		}

		var data = "action=delete_program&operation=" + operation + '&program_ids=' + program_ids + get_submitted_data_string('cbo_company_name', "../");

		freeze_window(operation);

		http.open("POST", "requires/planning_info_entry_for_sales_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_delete_prog_Reply_info;
		//alert(program_ids);
	}

	function fnc_delete_prog_Reply_info() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split('**');

			show_msg(trim(reponse[0]));

			if (reponse[0] == 2) {
				fnc_remove_tr();
			}

			release_freezing();
		}
	}

	function fnc_remove_tr() {
		var tot_row = $('#tbl_list_search tr').length;
		for (var i = 1; i <= tot_row; i++) {
			try {
				if ($('#tbl_' + i).is(':checked')) {
					$('#tr_' + i).remove();
				}
			}
			catch (e) {
				//got error no operation
			}
		}
	}

	function fnc_update(i,activeBtn) {

		var prog_qty = $('#prog_qty_' + i).val();
		var program_id = $('#promram_id_' + i).val();
		var data = "action=update_program&operation=" + operation + '&program_id=' + program_id + '&prog_qty=' + prog_qty+ '&activeBtn=' + activeBtn;
		freeze_window(operation);
		http.open("POST", "requires/planning_info_entry_for_sales_order_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_update_prog_Reply_info;
	}

	function fnc_update_prog_Reply_info() {
		if (http.readyState == 4) {
			var response = trim(http.responseText);
			if (response == 20) {
				alert("Program Qty Cannot Be Less Than Knitting Qty.");
				release_freezing();
				return;
			}
			show_msg(response);
			release_freezing();
		}
	}

	function active_inactive() {
		reset_form('', '', 'txt_job_no*hide_job_id*txt_booking_no', '', '', '');

		var within_group = $('#cbo_within_group').val();
		var company_id = document.getElementById('cbo_company_name').value;

		if (within_group == 1) {
			$('#txt_booking_no').attr('onDblClick', 'openmypage_booking();');
			$('#txt_booking_no').attr('placeholder', 'Browse Or Write');
			$('#txt_booking_no').removeAttr('disabled', 'disabled');
		}
		else {
			$('#txt_booking_no').removeAttr('onDblClick', 'onDblClick');
			$('#txt_booking_no').attr('placeholder', '');
			$('#txt_booking_no').attr('disabled', 'disabled');
		}

		if (company_id == 0) {
			$("#cbo_buyer_name option[value!='0']").remove();
		}
		else {
			load_drop_down('planning_info_entry_for_sales_order_controller', company_id + '_' + within_group, 'load_drop_down_buyer', 'buyer_td');
		}
	}

	function fnc_close() {
		var data = '';
		$('#selected_data').val(data);
		parent.emailwindow.hide();
	}

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../", $permission, 1); ?>
	<form name="palnningEntry_1" id="palnningEntry_1">
		<h3 style="width:1110px;" align="left" id="accordion_h1" class="accordion_h"
		onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
		<div id="content_search_panel">
			<fieldset style="width:1110px;">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
				align="center">
				<thead>
					<th class="must_entry_caption">Company Name</th>
					<th>Within Group</th>
					<th>Buyer Name</th>
					<th>Job No.</th>
					<th>Sales/Booking No.</th>
					<th>Planning Status</th>
					<th><input type="reset" name="res" id="res" value="Reset"
						onClick="reset_form('palnningEntry_1','list_container_fabric_desc','','','')"
						class="formbutton" style="width:100px"/></th>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $company_id, "active_inactive();", 1);
								?>
							</td>
							<td>
								<?php echo create_drop_down("cbo_within_group", 110, $yes_no, "", 0, "-- Select --", 0, "active_inactive();"); ?>
							</td>
							<td id="buyer_td">
								<?
							// echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
								echo create_drop_down("cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 0);
								?>
							</td>
							<td>
								<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes"
								style="width:130px" placeholder="Browse" onDblClick="openmypage_job();"
								autocomplete="off" readonly>
								<input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
							</td>
							<td>
								<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"
								style="width:100px" placeholder="Browse Or Write"
								onDblClick="openmypage_booking();">
							</td>
							<td>
								<? echo create_drop_down("cbo_planning_status", 100, $planning_status, "", 0, "", $selected, "", "", "1,2"); ?>
							</td>
							<td>
								<input type="button" value="Show" name="show" id="show" class="formbutton"
								style="width:100px" onClick="show_details(1)"/>
								&nbsp;
								<input type="button" value="Revised Booking" name="show" id="show" class="formbutton"
								style="width:105px" onClick="show_details(2)"/>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div style="width:100%;margin-top:2px;">
			<input type="button" value="Click For Program" name="generate" id="generate" class="formbuttonplasminus"
			style="width:150px" onClick="openmypage_prog()"/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="hidden" value="" id="selected_data"/>
			<input type="button" value="Close" name="close" id="close" class="formbuttonplasminus"
			style="width:150px" onClick="fnc_close()"/>
		</div>
	</form>
</div>
<div id="list_container_fabric_desc"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "booking_item_details")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$within_group = str_replace("'", "", $cbo_within_group);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$planning_status = str_replace("'", "", $cbo_planning_status);
	$barcode = str_replace("'", "", trim($txt_barcode));

	$job_no_cond = "";
	$booking_cond = "";
	if (str_replace("'", "", $hide_job_id) != "")
	{
		$job_no_cond = "and a.id in(" . str_replace("'", "", $hide_job_id) . ")";
		$ppl_job_no_cond = "and c.po_id in(" . str_replace("'", "", $hide_job_id) . ")";
	}

	$txt_booking = "%" . str_replace("'", "", trim($txt_booking_no)) . "%";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_cond = "and a.sales_booking_no like '$txt_booking'";
		$ppl_booking_cond = "and a.booking_no like '$txt_booking'";
	}
	//internal ref
	$txt_internalref = "%" . str_replace("'", "", trim($txt_internal_ref)) . "%";
	if (str_replace("'", "", trim($txt_internal_ref)) != "")
	{
			//for internal ref.
			$internalRef_cond = '';$booking_nos_internal_ref_cond = '';
			$internalRef_cond = " and a.grouping like '$txt_internalref'";
			$sql_bookings=sql_select("select b.booking_no from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond");
			$booking_nos="";$bookingArrChk=array();
			foreach ($sql_bookings as $row) {
				if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
					$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			$booking_nos=chop($booking_nos,",");
			$booking_nos_internal_ref_cond = "and a.sales_booking_no in($booking_nos)";
			unset($sql_bookings);
	}
	//echo $booking_nos_internal_ref_cond;

	if ($within_group == 0)
		$within_group_cond = "";
	else
		$within_group_cond = " and a.within_group=$within_group";

	if ($within_group == 1)
	{
		if ($buyer_name == 0)
			$buyer_id_cond_to = "";
		else
			$buyer_id_cond_to = " and a.buyer_id=$buyer_name";
	}

	if ($barcode != "")
	{
		$barcode_cond = "and b.barcode_no in($barcode)";
	}

	$date_cond = '';
	$date_from = str_replace("'", "", trim($txt_date_from));
	$date_to = str_replace("'", "", trim($txt_date_to));
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and a.insert_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$date_cond = "and a.insert_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$print_report_format =return_field_value("format_id"," lib_report_template","template_name ='".$company_name."' and module_id=4 and report_id=88 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",",$print_report_format);


	$print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=4 and report_id=269 and is_deleted=0 and status_active=1");
	$fReportId=explode(",",$print_report_format);
	$fReportId=$fReportId[0];



	if( $print_report_format_arr[0]!="" )
	{
		if( $print_report_format_arr[0]==272 )
		{
			$program_info_format_id = 272;

		}
		else if($print_report_format_arr[0]==273)
		{
			$program_info_format_id = 273;
		}
	}
	else if ($print_report_format_arr[0]==0 || $fReportId!="")
	{
		$program_info_format_id = $fReportId;
	}
	else
	{
		$program_info_format_id = 272;
	}





	if ($type == 2) // Revised
	{
		//$sql = "SELECT a.*,(select sum(q.grey_qty) grey_qty from fabric_sales_order_dtls q where q.id in (select regexp_substr(a.sales_order_dtls_ids,'[^,]+', 1, level) from dual  connect by regexp_substr(a.sales_order_dtls_ids, '[^,]+', 1, level) is not null)) grey_qty from (select a.company_id, a.buyer_id,a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id,b.mst_id,b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks,e.job_no,e.style_ref_no,e.booking_date,c.sales_order_dtls_ids from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id $ppl_booking_cond $ppl_job_no_cond and a.is_deleted=0 and a.status_active=1 and  b.is_sales=1 and e.is_deleted=0 and e.status_active=1 and c.is_revised=1 group by a.company_id, a.buyer_id,a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id,b.mst_id,b.knitting_source, b.knitting_party, b.color_id, c.id,b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks,e.job_no,e.style_ref_no,e.booking_date, c.sales_order_dtls_ids) a";
	}
	else
	{
		if ($type == 1)
		{
			$active_status_sql = "and b.status_active=1 and b.is_deleted=0";
			if ($db_type==0)
			{
				$sales_order_dtls_id="group_concat(b.id) as sales_order_dtls_id";
				$po_break_down_id_cast="cast(c.po_break_down_id as char(4000)) po_break_down_id";
			}
			else
			{
				//$sales_order_dtls_id="listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id";
				//tmp solution
				$sales_order_dtls_id = "RTRIM(XMLAGG(XMLELEMENT(e,b.id,',').EXTRACT('//text()') ORDER BY b.id).GETCLOBVAL(),',') AS sales_order_dtls_id";
				$po_break_down_id_cast="cast(c.po_break_down_id as varchar2(4000)) po_break_down_id";
			}

			if($within_group==1)
			{ // within_group yes

				$sql = " SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, upper(b.dia) as dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,$po_break_down_id_cast, a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,a.booking_without_order, a.booking_type from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no=c.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and c.fabric_source in(1,2) and a.booking_without_order=0 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, a.po_job_no,a.booking_without_order, a.booking_type
				union all
				select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, upper(b.dia) as dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, '' as po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,a.booking_without_order, a.booking_type from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,a.booking_without_order, a.booking_type  order by dia";
			}
			else
			{
				$sql = "SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, upper(b.dia) as dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,(select c.po_break_down_id from wo_booking_mst c where a.sales_booking_no = c.booking_no) po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,a.booking_without_order, a.booking_type from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,a.booking_without_order, a.booking_type order by b.dia"; 
				//echo $sql;
			}
		}
		else
		{
			$active_status_sql = "and b.status_active=0 and b.is_deleted=1 and d.status_active=0 and d.is_deleted=1";
			if ($db_type==0)
			{
				$sales_order_dtls_id="group_concat(b.id) as sales_order_dtls_id";
			}
			else
			{
				//$sales_order_dtls_id="listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id";
				$sales_order_dtls_id = "RTRIM(XMLAGG(XMLELEMENT(e,b.id,',').EXTRACT('//text()') ORDER BY b.id).GETCLOBVAL(),',') AS sales_order_dtls_id";
			}

			$sql = "SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, upper(b.dia) as dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,(select c.po_break_down_id,a.booking_without_order from wo_booking_mst c where a.sales_booking_no = c.booking_no) po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,ppl_planning_entry_plan_dtls d where a.id=b.mst_id and a.id=d.po_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,a.booking_without_order order by b.dia";
		}
		//echo $sql;
	}
	// echo $sql;
	$all_sales_booking_arr=array();
	$nameArray = sql_select($sql);
	$salesMstIdArr = array();
	$salesDtlsIdArr = array();
	$po_job_no_arr=array();
	foreach ($nameArray as $value)
	{
		/*if ($value[csf('within_group')]==1)
		{
			$all_sales_booking_arr[]=$value[csf('sales_booking_no')];
			$sales_booking_arr[] = "'".$value[csf('sales_booking_no')]."'";
		}*/
		$all_sales_booking_arr[]=$value[csf('sales_booking_no')];
		$sales_booking_arr[] = "'".$value[csf('sales_booking_no')]."'";

		//for sales mst id
		$salesMstIdArr[$value[csf('id')]] = $value[csf('id')];

		//for sales dtls id
		$expSalesDtlsId = explode(',', $value[csf('sales_order_dtls_id')]->load());
		foreach($expSalesDtlsId as $dtlsID)
		{
			$salesDtlsIdArr[$dtlsID] = $dtlsID;
		}

		array_push($po_job_no_arr,$value[csf('po_job_no')]);

	}
	//echo "<pre>";
	//print_r($salesDtlsIdArr); die;

	$break_down_arr = array();
	$break_down_cond = '';
	if(!empty($po_job_no_arr))
	{
		$break_down_cond = where_con_using_array($po_job_no_arr, '1', 'job_no_mst');
	}

	$poBreakData = "select job_no_mst, grouping from wo_po_break_down where status_active=1 and is_deleted=0 $break_down_cond";
	//echo $poBreakData;

	foreach (sql_select($poBreakData) as $rows)
	{
		$break_down_arr[$rows[csf('job_no_mst')]]['grouping'] = $rows[csf('grouping')];
	}
	//var_dump($break_down_arr);

	$booking_data_array = array();
	$booking_body_part_type_array = array();
	$program_data_array = array();
	$booking_program_arr = array();
	if(!empty($sales_booking_arr))
	{
		/*$pre_cost_sql = sql_select("select a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, upper(b.dia_width) as dia_width, b.color_type,c.body_part_type
		from wo_booking_mst a inner join wo_booking_dtls b on b.booking_no = a.booking_no inner join WO_PRE_COST_FABRIC_COST_DTLS c on b.pre_cost_fabric_cost_dtls_id = c.id
		where a.booking_no in(".implode(",",$sales_booking_arr).")
		group by a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type,c.body_part_type");*/

		$pre_cost_sql = sql_select("select a.id as id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no as job_no, b.construction, b.copmposition, b.gsm_weight, upper(b.dia_width) as dia_width, b.color_type,c.body_part_id,c.body_part_type , null as fabric_desc, null as determination_id, 0 as booking_without_order_sts
		from wo_booking_mst a inner join wo_booking_dtls b on b.booking_no = a.booking_no inner join WO_PRE_COST_FABRIC_COST_DTLS c on b.pre_cost_fabric_cost_dtls_id = c.id
		where a.booking_no in(".implode(",",$sales_booking_arr).")
		group by a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type,c.body_part_id,c.body_part_type
		union all
		select c.id as id,a.sales_booking_no as booking_no, null as po_break_down_id,c.entry_form_id as entry_form,b.pre_cost_fabric_cost_dtls_id,a.job_no as job_no, null as construction, null as copmposition, b.gsm_weight, upper(b.dia) as dia_width, b.color_type_id as color_type,d.body_part as body_part_id,e.body_part_type
		, b.fabric_desc, b.determination_id, 1 as booking_without_order_sts
		from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d , lib_body_part  e
		where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no and d.body_part=e.id and b.status_active=1 and b.is_deleted=0 and a.company_id=1 and a.sales_booking_no in(".implode(",",$sales_booking_arr).")
		and (c.fabric_source in(1,2) or d.fabric_source in(1,2)) and a.booking_without_order=1
		group by c.id ,a.sales_booking_no,c.entry_form_id ,b.pre_cost_fabric_cost_dtls_id,a.job_no, b.gsm_weight, b.dia, b.color_type_id,d.body_part,e.body_part_type
		, b.fabric_desc, b.determination_id");
	}

	foreach ($pre_cost_sql as $row)
	{
		if($row[csf('booking_without_order_sts')]==0)
		{
			$desc = $row[csf('construction')] . " " . $row[csf('copmposition')];
			$booking_data_array[$row[csf('booking_no')]][$desc][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_type')]] = $row[csf('pre_cost_fabric_cost_dtls_id')];
			$booking_body_part_type_array[$row[csf('booking_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_type')]] = $row[csf('body_part_type')];
		}
		else
		{
			$desc = $row[csf('fabric_desc')];
			$booking_data_array[$row[csf('booking_no')]][$desc][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_type')]] = $row[csf('pre_cost_fabric_cost_dtls_id')];
			$booking_body_part_type_array[$row[csf('booking_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_type')]][$row[csf('body_part_id')]] = $row[csf('body_part_type')];

		}

	}
	 //echo "<pre>";
	 //print_r($booking_body_part_type_array);
	if(!empty($sales_booking_arr))
	{
		$sales_booking = implode(",",$sales_booking_arr);
	    $sales_booking=implode(",",array_filter(array_unique(explode(",",$sales_booking))));
	    if($sales_booking!="")
	    {
	        $sales_booking=explode(",",$sales_booking);
	        $sales_booking_chnk=array_chunk($sales_booking,999);
	        $sales_booking_cond=" and";
	        foreach($sales_booking_chnk as $dtls_id)
	        {
	        	if($sales_booking_cond==" and")
					$sales_booking_cond.="(b.booking_no in(".implode(',',$dtls_id).")";
				else
					$sales_booking_cond.=" or b.booking_no in(".implode(',',$dtls_id).")";
	        }
	        $sales_booking_cond.=")";
	        //echo $sales_booking_cond;die;
	    }
		//$sales_booking_cond = "and b.booking_no in(".implode(",",$sales_booking_arr).")";
	}

	if ($db_type == 0)
	{
		$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, upper(a.dia) as dia, a.color_type_id, group_concat(a.dtls_id) as prog_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number,a.status_active from ppl_planning_entry_plan_dtls a left join inv_receive_master b on a.id=b.booking_id where a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.is_revised=0 $sales_booking_cond  group by a.id,a.mst_id,booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number,a.status_active ";
	}
	else
	{
		//$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, listagg(a.dtls_id, ',') within group (order by a.dtls_id) as prog_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids, a.pre_cost_fabric_cost_dtls_id,a.status_active from ppl_planning_entry_plan_dtls a where a.is_sales=1 and a.is_revised=0 $sales_booking_cond group by a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,a.status_active";

		$sql_plan = "SELECT b.id,b.mst_id,b.booking_no, b.po_id, b.yarn_desc as job_dtls_id, b.body_part_id, b.fabric_desc, b.gsm_weight, upper(b.dia) as dia,b.width_dia_type, b.color_type_id, listagg(b.dtls_id, ',') within group (order by b.dtls_id) as prog_no,sum(b.program_qnty) as program_qnty,b.sales_order_dtls_ids, b.pre_cost_fabric_cost_dtls_id,b.status_active,a.determination_id
		from ppl_planning_info_entry_mst a,ppl_planning_entry_plan_dtls b
		where a.id=b.mst_id and b.is_sales=1 and b.is_revised=0  $sales_booking_cond
		group by b.id,b.mst_id,b.booking_no, b.po_id, b.yarn_desc, b.body_part_id, b.fabric_desc, b.gsm_weight, b.dia,b.width_dia_type, b.color_type_id,b.sales_order_dtls_ids,b.pre_cost_fabric_cost_dtls_id,b.status_active ,a.determination_id";


	}
	//echo $sql_plan;
	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan)
	{
		/*$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];

		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['prog_no'][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['sales_order_dtls_ids'] = $rowPlan[csf('sales_order_dtls_ids')];*/
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];

		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['prog_no'][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]]['sales_order_dtls_ids'] = $rowPlan[csf('sales_order_dtls_ids')];

		$program_data_array1[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]]['program'] .= $rowPlan[csf('prog_no')] . ",";
		$booking_program_arr[$rowPlan[csf('booking_no')]] .= $rowPlan[csf('prog_no')] . ",";

		// for sales order if within group no
		$sales_order_dtls_ids = explode(",",$rowPlan[csf('sales_order_dtls_ids')]);
		foreach ($sales_order_dtls_ids as $sales_dtls_row)
		{
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['prog_no'] .= $rowPlan[csf('prog_no')].",";
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		}
	}


	//for show
	if ($type == 1)
	{
		// Approval Necessity Setup Starts ------------------------------------------------------------------------------
		if($db_type==0)
		{
			$main_booking_approval_check="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_name' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_name')) and page_id=5 and status_active=1 and is_deleted=0";

			$short_booking_approval_check="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_name' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_name')) and page_id=6 and status_active=1 and is_deleted=0";

			$sample_with_order_booking_approval_check="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_name' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_name')) and page_id=7 and status_active=1 and is_deleted=0";
		}
		else
		{
			$main_booking_approval_check="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_name' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_name')) and page_id=5 and status_active=1 and is_deleted=0";

			$short_booking_approval_check="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_name' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_name')) and page_id=6 and status_active=1 and is_deleted=0";

			$sample_with_order_booking_approval_check="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_name' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_name')) and page_id=7 and status_active=1 and is_deleted=0";
		}

		$main_booking_approval_check				= sql_select($main_booking_approval_check);
		$short_booking_approval_check				= sql_select($short_booking_approval_check);
		$sample_with_order_booking_approval_check	= sql_select($sample_with_order_booking_approval_check);

		$main_setup_status=($main_booking_approval_check[0][csf('approval_need')]==1)? array(0=>'no', 1=>'yes') : array(0=>'yes', 1=>'yes');
		$short_setup_status=($short_booking_approval_check[0][csf('approval_need')]==1)? array(0=>'no', 1=>'yes') : array(0=>'yes', 1=>'yes');
		$sample_setup_status=($sample_with_order_booking_approval_check[0][csf('approval_need')]==1)? array(0=>'no', 1=>'yes') : array(0=>'yes', 1=>'yes');

		// Approval Necessity Setup End -------------------------------------------------------------------------------
		if(!empty($all_sales_booking_arr))
		{
			$booking_list=implode(",", array_unique($all_sales_booking_arr));
			//$is_approved_status_arr = return_library_array( "select booking_no, is_approved from wo_booking_mst where booking_no in ('".$booking_list."')",'booking_no','is_approved');
			$is_approved_status_arr = return_library_array( "select booking_no, is_approved from wo_booking_mst where booking_no in(".implode(",",$sales_booking_arr).")",'booking_no','is_approved');


		}

		if(!empty($all_sales_booking_arr))
		{
			$job_no_array=array();
			$booking_list=implode(",", array_unique($all_sales_booking_arr));
			/*$sql_data=sql_select("select a.id, b.buyer_name,c.booking_no from wo_po_break_down a, wo_po_details_master b, wo_booking_dtls c where b.job_no=a.job_no_mst and a.id=c.po_break_down_id and c.booking_no in ('".$booking_list."') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				union all
				select 0 as id, buyer_id,booking_no from wo_non_ord_samp_booking_mst where booking_no in ('".$booking_list."') and status_active=1 and is_deleted=0");*/


			$sql_data=sql_select("select a.id, b.buyer_name,c.booking_no from wo_po_break_down a, wo_po_details_master b, wo_booking_dtls c where b.job_no=a.job_no_mst and a.id=c.po_break_down_id and c.booking_no in(".implode(",",$sales_booking_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				union all
				select 0 as id, buyer_id,booking_no from wo_non_ord_samp_booking_mst where booking_no in(".implode(",",$sales_booking_arr).") and status_active=1 and is_deleted=0");



			foreach ($sql_data as $row)
			{
				$job_no_array[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_name')];
			}
		}

		//for grey qty
		$sqlGreyQty = " SELECT a.id, a.sales_booking_no, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, upper(b.dia) as dia, b.width_dia_type,b.pre_cost_fabric_cost_dtls_id, sum(b.grey_qty) as grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id $active_status_sql and a.company_id=$company_name ".where_con_using_array($salesMstIdArr,0,'a.id')." ".where_con_using_array($salesDtlsIdArr,0,'b.id')."
		group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,b.pre_cost_fabric_cost_dtls_id";
		//echo $sqlGreyQty;
		$sqlGreyQtyRslt = sql_select($sqlGreyQty);
		$greyQtyData = array();
		foreach($sqlGreyQtyRslt as $row)
		{
			$row[csf('fabric_desc')] = trim($row[csf('fabric_desc')]);
			$greyQtyData[$row[csf('sales_booking_no')]][$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_type_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['grey_qty'] += $row[csf('grey_qty')];
		}
		//echo "<pre>";
		//print_r($greyQtyData); die;
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset>
				<legend>Fabric Description Details</legend>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
					<thead>
						<th width="40">SL</th>
						<th width="50">Plan Id</th>
						<th width="60">Prog. No</th>
						<th width="120">Booking No</th>
						<th width="70">Booking Date</th>
						<th title="Internal Ref." width="100">IR/IB</th>
						<th width="60">Buyer</th>
						<th width="120">Sales Order No</th>
						<th width="100">Style</th>
						<th width="100">Body Part</th>
						<th width="70">Color Type</th>
						<th width="160">Fabric Desc.</th>
						<th width="50">Gsm</th>
						<th width="50">Dia</th>
						<th width="70">Width/Dia Type</th>
						<th width="70">Sales Order Qty</th>
						<th width="70">Prog. Qnty</th>
						<th>Balance Prog. Qnty
							<input type="hidden" name="action_type" id="action_type" value="<? echo $type; ?>"/>
						</th>
						<?php
						if($type == 3)
						{
							echo '<th>&nbsp;</th>';
						}
						?>
					</thead>
					<tbody>
						<?

							$sql_print=sql_select("select template_name,format_id from lib_report_template where  template_name in($company_name)  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
							foreach($sql_print as $row)
							{
								$print_report_arr[$row[csf('template_name')]]=$row[csf('format_id')];
							}
							$print_report_format_par=$print_report_arr[$company_name];
							$print_report_format_part=explode(",",$print_report_format_par);
							$print_report_format_id=$print_report_format_part[0];


						$i = 1;
						$k = 1;
						$z = 1;
						$dia_array = array();
						$nameArray = sql_select($sql);
						$a = '';
						foreach ($nameArray as $row)
						{
							$plan_id = '';
							//$compId = $row[csf('company_id')];
							$job_no = $row[csf('job_no')];
							$style_ref_no = $row[csf('style_ref_no')];
							$sales_booking_no = $row[csf('sales_booking_no')];
							$booking_date = change_date_format($row[csf('booking_date')]);
							$gsm = $row[csf('gsm_weight')];
							$dia = $row[csf('dia')];
							$desc = trim($row[csf('fabric_desc')]);
							$determination_id = $row[csf('determination_id')];
							$body_part_id = $row[csf('body_part_id')];
							$color_type_id = $row[csf('color_type_id')];
							$width_dia_type = $row[csf('width_dia_type')];
							$internal_ref = $break_down_arr[$row[csf('po_job_no')]]['grouping'];

							//add date 30.06.2020
							$expSalesDtlsIdArr = array();
							$alesDtlsIdArr =array();
							//tmp solution
							$row[csf('sales_order_dtls_id')] = $row[csf('sales_order_dtls_id')]->load();
							$expSalesDtlsIdArr = explode(',', $row[csf('sales_order_dtls_id')]);
							for($zs =0; $zs<count($expSalesDtlsIdArr); $zs++)
							{
								$alesDtlsIdArr[$expSalesDtlsIdArr[$zs]] = $expSalesDtlsIdArr[$zs];
							}
							$sales_order_dtls_id = implode(',', $alesDtlsIdArr);
							//$sales_order_dtls_id = $row[csf('sales_order_dtls_id')];

							$sales_id = $row[csf('id')];
							$within_group = $row[csf('within_group')];
							$pre_cost_fabric_cost_dtls_id = $row[csf('pre_cost_fabric_cost_dtls_id')];
							$buyer_id = $row[csf('buyer_id')];
							$buyer_name = $job_no_array[$sales_booking_no]['buyer_id'];
							//for grey qty
							//$grey_qty = $row[csf('grey_qty')];
							//echo $sales_booking_no."=".$sales_id."=".$body_part_id."=".$determination_id."=".$gsm."=".$dia."=".$color_type_id."=".$pre_cost_fabric_cost_dtls_id."<br/>";
							//$grey_qty = $greyQtyData[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id]['grey_qty'];
							$grey_qty = $greyQtyData[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id]['grey_qty'];

							// Start 

							$print_booking='';
							$bookingNo=$sales_booking_no;
							if($row[csf('booking_type')]==1){

								 $booking_mst_arr= sql_select("select distinct  a.fabric_source, a.is_approved, a.job_no, a.po_break_down_id, b.fab_nature from wo_booking_mst a, wo_booking_dtls b where b.booking_no=a.booking_no and b.job_no=a.job_no and a.booking_no = '$bookingNo' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.booking_type=1 and b.booking_type=1");

								$fabric_source=$booking_mst_arr[0]['FABRIC_SOURCE'];
								$is_approved = $booking_mst_arr[0]['IS_APPROVED'];
								$booking_job = $booking_mst_arr[0]['JOB_NO'];
								$booking_po_break_down_id = $booking_mst_arr[0]['PO_BREAK_DOWN_ID'];

								foreach($booking_mst_arr as $value){
									$fabric_natu .= ",". $value[csf('fab_nature')];
								}

								$fabric_natu = ltrim($fabric_natu,",");



								foreach($print_report_format_part as $row_id)
								{ 
										if($row_id==1 && $print_report_format_id==1) 
										{ 												
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_gr','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==2 && $print_report_format_id==2) 
										{ 		
																										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report','".$i."')\"> ".$bookingNo." <a/>";
										}
										else if($row_id==849 && $print_report_format_id==849)	
										{ 
																		
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_bl1','".$i."')\"> ".$bookingNo." <a/>";
																	
										}
										else if($row_id==3 && $print_report_format_id==3)	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report3','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==892 && $print_report_format_id==892)	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report3_v1','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==4 && $print_report_format_id==4) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report1','".$i."')\"> ".$bookingNo." <a/>";
										} 	
										else if($row_id==5 && $print_report_format_id==5) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report2','".$i."')\"> ".$bookingNo." <a/>";
										} 	
										else if($row_id==6 && $print_report_format_id==6) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report4','".$i."')\"> ".$bookingNo." <a/>";
										} 	
										else if($row_id==7 && $print_report_format_id==7)  	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report5','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==13 && $print_report_format_id==13) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report13','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==28 && $print_report_format_id==28) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_akh','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==45 && $print_report_format_id==45) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==53 && $print_report_format_id==53) 	
										{ 		
																										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_jk','".$i."')\"> ".$bookingNo." <a/>";
										}
										else if($row_id==432 && $print_report_format_id==432)	
										{ 
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_fn','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==73 && $print_report_format_id==73) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_mf','".$i."')\"> ".$bookingNo." <a/>";
										} 
																	
										else if($row_id==84 && $print_report_format_id==84)  	
										{ 
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_islam','".$i."')\"> ".$bookingNo." <a/>";
										} 		
																	
										else if($row_id==93 && $print_report_format_id==93) 
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_libas','".$i."')\"> ".$bookingNo." <a/>";
										} 
										else if($row_id==129 && $print_report_format_id==129) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print5','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==193 && $print_report_format_id==193) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print4','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==269 && $print_report_format_id==269) 	
										{ 
																	
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_knit','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==280 && $print_report_format_id==280) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print14','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==39 && $print_report_format_id==39) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print39','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==304 && $print_report_format_id==304) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report10','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==719 && $print_report_format_id==719) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report16','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==723 && $print_report_format_id==723) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report17','".$i."')\"> ".$bookingNo." <a/>";
										} 

										else if($row_id==833 && $print_report_format_id==833) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report17_v1','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==339 && $print_report_format_id==339) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report18','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==370 && $print_report_format_id==370) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print19','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==383 && $print_report_format_id==383) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print20','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==404 && $print_report_format_id==404) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report21','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==419 && $print_report_format_id==419) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report22','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==426 && $print_report_format_id==426) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print23','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==452 && $print_report_format_id==452) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report_print24','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==786 && $print_report_format_id==786) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report25','".$i."')\"> ".$bookingNo." <a/>";
										} 
										
										else if($row_id==502 && $print_report_format_id==502) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report26','".$i."')\"> ".$bookingNo." <a/>";
										} 

										
										else if($row_id==437 && $print_report_format_id==437) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report27','".$i."')\"> ".$bookingNo." <a/>";
										} 

										
										else if($row_id==865 && $print_report_format_id==865) 	
										{ 
										
											$print_booking="<a href='#' onClick=\"generate_worder_report3('".$bookingNo."','".$company_name."','".$fabric_source."','".$is_approved."','".$booking_job."','".$booking_po_break_down_id."','".$fabric_natu."','".$row_id."','show_fabric_booking_report28','".$i."')\"> ".$bookingNo." <a/>";
										} 
				
								}
							}
							
							if($print_booking=='') $print_booking=$bookingNo;

							// var_dump($print_booking);

							/// END



							$status = ($type == 1) ? 1 : 0;
							if($within_group == 1)
							{
								/*$program_qnty = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['program_qnty'];
								$plan_id = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['mst_id'];
								$prog_no = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['prog_no'];*/

								$program_qnty = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['program_qnty'];
								$plan_id = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['mst_id'];
								$prog_no = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['prog_no'];

								$prog_no = implode(",", $prog_no);
							}
							else
							{
								$sales_dtls_id = array_unique(explode(",",$sales_order_dtls_id));
								$program_qnty = 0;
								$prog_no='';
								//print_r($sales_dtls_id);
								foreach ($sales_dtls_id as $rows)
								{
									if($type==3)
									{
										$plan_id .= $program_data_sales_array[$rows][$status]['mst_id'].",";
										$prog_no .= $program_data_sales_array[$rows][$status]['prog_no'].",";
										$program_qnty += $grey_qty; //$program_data_sales_array[$rows][$status]['program_qnty'];
									}
									else
									{
										$plan_id .= $program_data_sales_array[$rows][$status]['mst_id'].",";
										$prog_no .= $program_data_sales_array[$rows][$status]['prog_no'].",";
										$program_qnty = $program_data_sales_array[$rows][$status]['program_qnty'];
									}
								}
							}

							$balance_qnty = number_format($grey_qty - $program_qnty,2,".","");
							$pre_cost_id = $booking_data_array[$sales_booking_no][$desc][$gsm][$dia][$color_type_id];

							if($row[csf('booking_without_order')]==0)
							{
								//echo $sales_booking_no.'='.$pre_cost_fabric_cost_dtls_id.'='.$gsm.'='.$dia.'='.$color_type_id.'<br/>';
								$body_partType = $booking_body_part_type_array[$sales_booking_no][$pre_cost_fabric_cost_dtls_id][$gsm][$dia][$color_type_id];
							}
							else
							{
								//echo $sales_booking_no.'='.$pre_cost_fabric_cost_dtls_id.'='.$gsm.'='.$dia.'='.$color_type_id.'='.$body_part_id.'<br/>';
								$body_partType = $booking_body_part_type_array[$sales_booking_no][$pre_cost_fabric_cost_dtls_id][$gsm][$dia][$color_type_id][$body_part_id];
							}

							if($body_partType==40 || $body_partType==50)
							{
								$isCollarAndCuffType=1;
							}
							else
							{
								$isCollarAndCuffType=2;
							}

							if (($planning_status == 2 && $balance_qnty <= 0 && $grey_qty>0) || ($planning_status == 1 && (($within_group == 1)?$balance_qnty > 0:$balance_qnty > 0)))
							{
								if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								if (!in_array($dia, $dia_array)) {
									if ($k != 1) {
										?>
										<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
											<td colspan="15" align="right"><b>Sub Total</b></td>
											<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
											<td align="right">
												<b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
												<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
											</tr>
											<?
											$total_dia_qnty = 0;
											$total_program_qnty = 0;
											$total_balance = 0;
											$i++;
										}
										?>
										<tr bgcolor="#EFEFEF" id="tr_<? echo $i; ?>">
											<td colspan="18">
												<b>Dia/Width: <?php echo $dia; ?></b>
											</td>
										</tr>
										<?
										$dia_array[] = $row[csf('dia')];
										$k++;
										$i++;
									}

									if ($within_group == 1) {
										$buyer = $buyer_arr[$buyer_name];
									} else {
										$buyer = $buyer_arr[$buyer_id];
									}
									// Making status array usng approval necessity setup status starts-----------------

									if($row[csf('booking_type')]==4)
									{
										$status_arr=$sample_setup_status;
									}
									if($row[csf('booking_type')]==1 && $row[csf('is_short')]==1)
									{
										$status_arr=$short_setup_status;
									}
									else
									{
										$status_arr=$main_setup_status;
									}
									$approval_status=$is_approved_status_arr[$sales_booking_no];
									$reqsn_found_or_not = 0;
									if($type == 3){
										$prog_nos = rtrim($prog_no, ", ");
										if($prog_nos=="")
										{
											$hidden = "display:none;";
										}
										else
										{
											$hidden = "";
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer; <? echo $hidden; ?>" >
											<?
										} else {
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="selected_row('<? echo $i; ?>','<? echo $status_arr[$approval_status]; ?>')" id="tr_<? echo $i; ?>">


												<?
											}

											$plan_id = implode(",",array_filter(array_unique(explode(",", chop($plan_id,",")))));
											?>
											<td width="40" align='center'><? echo $z; ?></td>
											<td width="50" align='center' id="plan_id_<? echo $i; ?>"><? echo rtrim($plan_id,", "); ?></td>
											<td width="60" align='center' id="prog_no_<? echo $i; ?>"><p>
												<?
												$print_program_no = "";
												$prog_no_arr = array_unique(explode(",", $prog_no));
												foreach ($prog_no_arr as $prog)
												{
													if($prog != "")
													{
														//$print_program_no .= "<a href='##' onclick=\"generate_report2(" . $row[csf('company_id')] . "," . $prog . "," . $program_info_format_id . ")\">" . $prog . "</a>,";
														$print_program_no .= "<a href='##' onclick=\"generate_report2(" . $company_name . "," . $prog . "," . $program_info_format_id . ")\">" . $prog . "</a>,";
													}
												}
												echo rtrim($print_program_no,", ");
												?>
											</p></td>
											<td id="booking_no_<? echo $i; ?>" align='center'><? echo $print_booking; ?></td>
											<td width="70" align="center"><? echo $booking_date; ?></td>
											<td width="100" align='center'><p><? echo $internal_ref; ?></p></td>
											<td width="60" align='center'><p><? echo $buyer; ?></p></td>
											<td align='center'><? echo $job_no; ?></td>
											<td width="100" align='center'><p><? echo $style_ref_no; ?></p></td>
											<td align='center'><p><? echo $body_part[$body_part_id]; ?></p></td>
											<td width="70" align='center'><p><? echo $color_type[$color_type_id]; ?></p></td>
											<td align='center' id="desc_<? echo $i; ?>" title="<? echo "Yarn Count Determination ID: ".$determination_id; ?>"><p><? echo $desc; ?></p></td>
											<td width="50" align='center' id="gsm_weight_<? echo $i; ?>"><p><? echo $gsm; ?></p></td>
											<td width="50" align='center' id="dia_width_<? echo $i; ?>"><p><? echo $dia; ?></p></td>
											<td width="70" align='center'><? echo $fabric_typee[$width_dia_type]; ?></td>
											<td align="right" id="booking_qnty_<? echo $i; ?>" width="70">
												<? echo number_format($grey_qty, 2, '.', ''); ?>
											</td>
											<td align="right" width="70">
												<? if ($program_qnty > 0) echo number_format($program_qnty, 2, '.', ''); ?>
											</td>
											<td align="right" id="ballance_qnty_<? echo $i; ?>"><? echo number_format($balance_qnty, 2, '.', ''); ?></td>
											<?php
											if($type == 3){
												$prog_no = implode("_",array_unique(explode(",", rtrim($prog_no,', '))));
												$plan_id = implode("_",array_unique(explode(",", rtrim($plan_id,', '))));
												?>
												<td><span class="formbutton" title="Click to Active" onClick="activePlan(this,'<? echo $plan_id; ?>','<? echo $prog_no; ?>','<? echo $balance_qnty; ?>','<? echo $sales_order_dtls_id; ?>','<? echo $grey_qty; ?>')">Active</span>
												</td>
												<?
											}
											?>
											<input type="hidden" name="buyer_id[]" id="buyer_id_<? echo $i; ?>"
											value="<? echo $row[csf('buyer_id')]; ?>"/>

											<input type="hidden" name="body_part_id[]" id="body_part_id_<? echo $i; ?>"
											value="<? echo $body_part_id; ?>"/>
											<input type="hidden" name="color_type_id[]" id="color_type_id_<? echo $i; ?>"
											value="<? echo $color_type_id; ?>"/>
											<input type="hidden" name="determination_id[]" id="determination_id_<? echo $i; ?>"
											value="<? echo $determination_id; ?>"/>
											<input type="hidden" name="fabric_typee[]" id="fabric_typee_<? echo $i; ?>"
											value="<? echo $width_dia_type; ?>"/>
											<input type="hidden" name="pre_cost_id[]" id="pre_cost_id_<? echo $i; ?>"
											value="<? echo $pre_cost_fabric_cost_dtls_id; ?>"/>
											<input type="hidden" name="job_id[]" id="job_id_<? echo $i; ?>"
											value="<? echo $sales_id; ?>"/>
											<input type="hidden" name="job_dtls_id[]" id="job_dtls_id_<? echo $i; ?>"
											value="<? echo $row[csf('dtls_id')]; ?>"/>
											<input type="hidden" name="withinGroup[]" id="withinGroup_<? echo $i; ?>"
											value="<? echo $within_group; ?>"/>
											<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>"
											value="<? echo $sales_booking_no; ?>"/>
											<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i; ?>"
											value="<? echo $row[csf('booking_without_order')]; ?>"/>
											<input type="hidden" name="reqsn_found_or_not[]" id="reqsn_found_or_not_<? echo $i; ?>"
											value="<? echo $reqsn_found_or_not; ?>"/>
											<input type="hidden" name="sales_order_dtls_id[]" id="sales_order_dtls_id<? echo $i; ?>"
											value="<? echo $sales_order_dtls_id; ?>"/>
											<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
											id="pre_cost_fabric_cost_dtls_id<? echo $i; ?>" value="<? echo $pre_cost_fabric_cost_dtls_id; ?>"/>
											<input type="hidden" name="hdn_body_partType[]" id="hdn_body_partType_<? echo $i; ?>" value="<? echo $isCollarAndCuffType; ?>"/>


										</tr>
										<?
										if($hidden=="")
										{
											//$total_dia_qnty += $row[csf('grey_qty')];
											$total_dia_qnty += $grey_qty;
											$total_program_qnty += $program_qnty;
											$total_balance += $balance_qnty;

											//$total_qnty += $row[csf('grey_qty')];
											$total_qnty += $grey_qty;
											$grand_total_program_qnty += $program_qnty;
											$grand_total_balance += $balance_qnty;

											$i++;
											$z++;
										}
									}
								}

								if ($i > 1) {
									?>
									<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
										<td colspan="15" align="right"><b>Sub Total</b></td>
										<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>
									</tr>
									<?
								}
								?>
							</tbody>
							<tfoot>
								<th colspan="15" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<? echo $company_name; ?>"/></th>
								<th align="right"><? echo number_format($total_qnty, 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($grand_total_balance, 2, '.', ''); ?></th>
								<th><input type="hidden" name="hiddenVariableCollarCuff" id="hiddenVariableCollarCuff" value="<? echo $txtVariableCollarCuff; ?>"/>
							</tfoot>
						</table>
					</div>
				</fieldset>
			</form>
		<?
	}
	//for Deleted booking
	else if($type == 3)
	{
		if($type == 2)
		{
			$qry_cond = ' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.is_revised=1 ';
		}
		else
		{
			$qry_cond = ' and a.status_active=1 and a.is_deleted=0 and b.status_active=0 and b.is_deleted=1 and c.status_active=0 and c.is_deleted=1 and c.is_revised=0 ';
		}

		$sqlRevised = "
		SELECT
			a.company_id, a.buyer_id, a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, upper(a.dia) as dia, a.width_dia_type, b.id, b.mst_id,b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, c.po_id, c.sales_order_dtls_ids, d.booking_date, d.job_no, d.style_ref_no,b.active_for_production
		FROM
			ppl_planning_info_entry_mst a,
			ppl_planning_info_entry_dtls b,
			ppl_planning_entry_plan_dtls c,
			fabric_sales_order_mst d
		WHERE
			a.id=b.mst_id and b.id=c.dtls_id and c.po_id = d.id
			and b.is_sales=1
			".$qry_cond."
			".$ppl_booking_cond."
			".$ppl_job_no_cond."
		GROUP BY
			a.company_id, a.buyer_id, a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.mst_id,b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, c.po_id, c.sales_order_dtls_ids, d.booking_date, d.job_no, d.style_ref_no,b.active_for_production";
		//echo $sqlRevised; die;
		$sqlRevisedRslt = sql_select($sqlRevised);
		$salesOrderIdArr = array();
		$revisedDataArr = array();
		$progNoArr = array();
		foreach($sqlRevisedRslt as $row)
		{
			$progNoArr[$row[csf('id')]] = $row[csf('id')];
			$salesOrderIdArr[$row[csf('po_id')]] = $row[csf('po_id')];

			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['plan_no'] = $row[csf('mst_id')];
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['program_no'][] = $row[csf('id')];
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['booking_date'] = $row[csf('booking_date')];
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['buyer_id'] = ($row[csf('within_group')]==1?$company_arr[$row[csf('buyer_id')]]:$buyer_arr[$row[csf('buyer_id')]]);
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['sales_order_no'] = $row[csf('job_no')];
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];

			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['color_id'][] = $row[csf('color_id')];
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['program_qnty'][$row[csf('id')]] += $row[csf('program_qnty')];
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['po_id'] = $row[csf('po_id')];
			$revisedDataArr[$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]][$row[csf('id')]]['active_for_production'] = $row[csf('active_for_production')];
		}


		$requisitionSQL="select knit_id,requisition_no from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0" .where_con_using_array($progNoArr, '0', 'knit_id');
		$sqlRequisitionRslt=sql_select($requisitionSQL);
		foreach($sqlRequisitionRslt as $row)
		{
			$progWiseRequArr[$row[csf('knit_id')]]['requisition_no']=$row[csf('requisition_no')];
		}
		//echo "<pre>";
		//print_r($revisedDataArr); die;

		/*
		$sqlSales ="
		select
			a.mst_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.grey_qty, a.pre_cost_fabric_cost_dtls_id
		from
			fabric_sales_order_dtls a
			left join fabric_sales_order_dtls b on (a.mst_id=b.mst_id and b.status_active=0 and b.is_deleted=1
		and a.pre_cost_fabric_cost_dtls_id= b.pre_cost_fabric_cost_dtls_id and a.color_id=b.color_id)
		where
			a.mst_id in(".implode(',', $salesOrderIdArr).") and a.status_active=1 and a.is_deleted=0
		group by
			a.mst_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty,  a.grey_qty, a.pre_cost_fabric_cost_dtls_id
		order by
			a.body_part_id";
		//echo $sqlSales;
		$sqlSalesRslt = sql_select($sqlSales);
		$salesDataArr = array();
		foreach($sqlSalesRslt as $row)
		{
			$salesDataArr[$row[csf('mst_id')]][$row[csf('body_part_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_type_id')]][$row[csf('color_id')]]['grey_qty'] += $row[csf('grey_qty')];
		}
		*/
		//echo "<pre>";
		//print_r($salesDataArr); die;

		//for production
		$sqlProduction = "SELECT a.booking_id, b.grey_receive_qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id=b.mst_id and a.entry_form=2 and a.item_category = 13 ".where_con_using_array($progNoArr, '0', 'a.booking_id');
		//echo $sqlProduction;
		$sqlProductionRslt = sql_select($sqlProduction);
		$productonData = array();
		foreach($sqlProductionRslt as $row)
		{
			$productonData[$row[csf('booking_id')]]['production_qty'] += $row[csf('grey_receive_qnty')];
		}
		//echo "<pre>";
		//print_r($productonData); die;

		$status_arr = array();
		$check_production_arr = array();
		$check_sales_order_status = sql_select("select a.mst_id,a.pre_cost_fabric_cost_dtls_id,listagg(a.status_active, ',') within group (order by a.id) as status_active from fabric_sales_order_dtls a group by a.pre_cost_fabric_cost_dtls_id");
		foreach ($check_sales_order_status as $status_row)
		{
			$status_arr[$status_row[csf('pre_cost_fabric_cost_dtls_id')]][$status_row[csf('mst_id')]] = $status_row[csf('status_active')];
		}
		//echo "<pre>";
		//print_r($status_arr); die;

		$check_program_in_req_issue = sql_select("select a.id,a.knit_id,b.recv_number from ppl_yarn_requisition_entry a left join inv_receive_master b on a.knit_id=b.booking_id where b.company_id=$company_name and b.entry_form=2 and b.item_category=13 and b.receive_basis=2");
		foreach ($check_program_in_req_issue as $check_production)
		{
			$check_production_arr[$check_production[csf('knit_id')]] = $check_production[csf('recv_number')];
		}
		//echo "<pre>";
		//print_r($check_production_arr); die;
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset>
				<legend>Fabric Description Details</legend>
				<input type="button" value="Delete Program" name="generate" id="generate" class="formbutton" style="width:150px" onClick="delete_prog()"/>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                    <thead>
                        <th width="40"></th>
                        <th width="40">SL</th>
                        <th width="50">Plan Id</th>
                        <th width="60">Prog. No</th>
                        <th width="60">Requ. No</th>
                        <th width="100">Booking No</th>
                        <th width="70">Booking Date</th>
                        <th width="60">Buyer</th>
                        <th width="105">Sales Order No</th>
                        <th width="100">Style</th>
                        <th width="80">Body Part</th>
                        <th width="70">Color Type</th>
                        <th width="140">Fabric Desc.</th>
                        <th width="50">Gsm</th>
                        <th width="50">Dia</th>
                        <th width="70">Width/Dia Type</th>
                        <th width="70">Color</th>
                        <!--<th width="70">Sales Order Qty</th>-->
                        <th width="70">Prog. Qnty</th>
                        <!--<th>Balance Prog. Qnty</th>-->
                        <th>Production Qnty</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?
                        $i = 0;
                        $k = 1;
                        $z = 1;
                        $dia_array = array();
                        $a = '';
                        $colspan = 20;
                        $colspan2 = 17;
                        foreach ($revisedDataArr as $die=>$diaArr)
                        {
                            $tatalDia_programQty = 0;
                            $tatalDia_productonQty = 0;
                            ?>
                            <tr>
                                <td colspan="<?php echo $colspan; ?>">Dia/Width: <?php echo $die; ?></td>
                            </tr>
                            <?php
                            foreach($diaArr as $bookingNo=>$bookingArr)
                            {
                                foreach($bookingArr as $bodyPartId=>$bodyPartArr)
                                {
                                    foreach($bodyPartArr as $colorTypeId=>$colorTypeArr)
                                    {
                                        foreach($colorTypeArr as $fabDesc=>$fabDescArr)
                                        {
                                            foreach($fabDescArr as $gsm=>$gsmArr)
                                            {

                                                foreach($gsmArr as $widthDia=>$widthDiaArr)
												{
                                                	foreach($widthDiaArr as $programNos=>$row)
													{

	                                                    $i++;
	                                                    //for sales order qty and color name
	                                                    //$salesOrderQty = 0;
	                                                    $colorName = '';
	                                                    foreach($row['color_id'] as $color)
	                                                    {
	                                                        $expColor = explode(',', $color);
	                                                        foreach($expColor as $clrID)
	                                                        {
	                                                            //$salesOrderQty += $salesDataArr[$row['po_id']][$bodyPartId][$fabDesc][$gsm][$die][$colorTypeId][$clrID]['grey_qty'];
	                                                            if($colorName != '')
	                                                            {
	                                                                $colorName .= ', ';
	                                                            }
	                                                            $colorName .= $color_library[$clrID];
	                                                        }
	                                                    }

	                                                    //for program qty and production qty
	                                                    $programQty = 0;
	                                                    $productonQty = 0;
	                                                    $requisitionNo="";
	                                                    foreach($row['program_no'] as $progNo)
	                                                    {
	                                                        $programQty += $row['program_qnty'][$progNo];
	                                                        $productonQty += $productonData[$progNo]['production_qty'];
	                                                        if($progWiseRequArr[$progNo]['requisition_no']!="")
	                                                        {
	                                                        	$requisitionNo.=$progWiseRequArr[$progNo]['requisition_no'].',';
	                                                        }
	                                                    }

	                                                    //for balance qty
	                                                    //$balanceQty = $salesOrderQty - $programQty;

	                                                    if($row['active_for_production']==1){$disableStatus="disabled";}

	                                                    ?>
	                                                    <tr>
	                                                        <td align="center" valign="middle">
	                                                        <input type="checkbox" id="tbl_<? echo $i; ?>" name="check[]" <? echo $disabled; ?>/>
	                                                        <input type="hidden" id="promram_id_<? echo $i; ?>" name="promram_id[]" value="<? echo implode(', ', $row['program_no']); ?>"/>
	                                                        </td>
	                                                        <td><?php echo $i;?></td>
	                                                        <td><?php echo $row['plan_no']; ?></td>
	                                                        <td title="<?echo $type;?>">
	                                                        <?php
	                                                        //echo implode(', ', $row['program_no']);
	                                                        if ($type==3)
	                                                        {

	                                                        	$print_program_no = "";
																$prog_no_arr = array_unique(explode(",", implode(', ', $row['program_no'])));
																//print_r($prog_no_arr);

																foreach ($prog_no_arr as $prog)
																{
																	if($prog != "")
																	{
																		$print_program_no .= "<a href='##' onclick=\"generate_delete_booking_report(" . $company_name . "," . $prog . "," . $program_info_format_id . ")\">" . $prog . "</a>,";
																	}
																}
																echo rtrim($print_program_no,", ");
	                                                        }
	                                                        else
	                                                        {
	                                                        	echo implode(', ', $row['program_no']);
	                                                        }

	                                                        ?></td>
	                                                        <td><?php echo rtrim($requisitionNo,", "); ?></td>
	                                                        <td><?php echo $bookingNo; ?></td>
	                                                        <td><?php echo date('d-m-Y', strtotime($row['booking_date'])); ?></td>
	                                                        <td><?php echo $row['buyer_id']; ?></td>
	                                                        <td><?php echo $row['sales_order_no']; ?></td>
	                                                        <td><?php echo $row['style_ref_no']; ?></td>
	                                                        <td><?php echo $body_part[$bodyPartId]; ?></td>
	                                                        <td><?php echo $color_type[$colorTypeId]; ?></td>
	                                                        <td><?php echo $fabDesc; ?></td>
	                                                        <td><?php echo $gsm; ?></td>
	                                                        <td><?php echo $die; ?></td>
	                                                        <td><?php echo $fabric_typee[$widthDia]; ?></td>
	                                                        <td><?php echo $colorName; ?></td>
	                                                        <!--<td><?php //echo $salesOrderQty; ?></td>-->
	                                                        <td><input type="text" class="text_boxes_numeric" name="prog_qty[]" id="prog_qty_<?php echo $i; ?>" value="<?php echo number_format($programQty, 2, '.', ''); ?>" style="width:80px"/></td>
	                                                        <!--<td><?php //echo $balanceQty; ?></td>-->
	                                                        <td align="right"><?php echo number_format($productonQty, 2, '.', ''); ?></td>
	                                                        <td align="center">
	                                                        <input type="button" value="Update" onClick="fnc_update(<?php echo $i; ?>,0);" class="formbutton" style="width:80px">

	                                                        <?
	                                                        	if($_SESSION['logic_erp']['user_level']==2)
	                                                        	{
	                                                        		?>
	                                                        			 <input title="This button carry only flag to available this program on production page" type="button"  value="<? if($row['active_for_production']==1){ echo "Activated";}else{echo "Active";}; ?>" onClick="fnc_update(<?php echo $i; ?>,1);" class="formbutton" style="width:80px">
	                                                        		<?
	                                                        	}
	                                                        ?>

	                                                        <input type="hidden" name="reqsn_found_or_not[]" id="reqsn_found_or_not_<?php echo $i; ?>" value="<?php echo $reqsn_found_or_not; ?>"/>
	                                                        </td>
	                                                    </tr>
	                                                    <?php

	                                                    $tatalDia_programQty += number_format($programQty, 2, '.', '');
	                                                    $tatalDia_productonQty += number_format($productonQty, 2, '.', '');

	                                                    $grand_tatalDia_programQty += number_format($programQty, 2, '.', '');
	                                                    $grand_tatalDia_productonQty += number_format($productonQty, 2, '.', '');
	                                                }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                            <tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
                                <td colspan="<?php echo $colspan2; ?>" align="right"><b>Sub Total</b></td>
                               <!-- <td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>-->
                                <td align="right"><b><? echo number_format($tatalDia_programQty, 2, '.', ''); ?></b></td>
                                <!--<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>-->
                                <td align="right"><b><? echo number_format($tatalDia_productonQty, 2, '.', ''); ?></b></td>
                                <td align="right"><td>
                                <?
                            	if($_SESSION['logic_erp']['user_level']==2)
                            	{
                            		?>
                            			 <td align="right"><td>
                            		<?
                            	}
                            	?>

                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
						<tfoot>
                        	<th colspan="17" align="right">Grand Total<input type="hidden" name="company_id" id="company_id"
                            value="<? echo $company_name; ?>"/></th>
                            <!--<th align="right"><? echo number_format($total_qnty, 2, '.', ''); ?></th>-->
                            <th align="right"><? echo number_format($grand_tatalDia_programQty, 2, '.', ''); ?></th>
                            <!--<th align="right"><? echo number_format($grand_total_balance, 2, '.', ''); ?></th>-->
                            <th align="right"><? echo number_format($grand_tatalDia_productonQty, 2, '.', ''); ?></th>
                            <th><input type="hidden" name="hiddenVariableCollarCuff" id="hiddenVariableCollarCuff" value="<? echo $txtVariableCollarCuff; ?>"/>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </form>
        <?
    }
    //for revised booking
    else
	{
		if($type == 2)
		{
			$qry_cond = ' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.is_revised=1 ';
		}
		else
		{
			$qry_cond = ' and a.status_active=1 and a.is_deleted=0 and b.status_active=0 and b.is_deleted=1 and c.status_active=0 and c.is_deleted=1 and c.is_revised=0 ';
		}

		$sqlRevised = "
		SELECT
			a.company_id, a.buyer_id, a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, upper(a.dia) as dia, a.width_dia_type, b.id, b.mst_id,b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, c.po_id, c.sales_order_dtls_ids, d.booking_date, d.job_no, d.style_ref_no
		FROM
			ppl_planning_info_entry_mst a,
			ppl_planning_info_entry_dtls b,
			ppl_planning_entry_plan_dtls c,
			fabric_sales_order_mst d
		WHERE
			a.id=b.mst_id and b.id=c.dtls_id and c.po_id = d.id
			and b.is_sales=1
			".$qry_cond."
			".$ppl_booking_cond."
			".$ppl_job_no_cond."
		GROUP BY
			a.company_id, a.buyer_id, a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.mst_id,b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, c.po_id, c.sales_order_dtls_ids, d.booking_date, d.job_no, d.style_ref_no";
		//echo $sqlRevised; die;
		$sqlRevisedRslt = sql_select($sqlRevised);
		$salesOrderIdArr = array();
		$revisedDataArr = array();
		$progNoArr = array();
		foreach($sqlRevisedRslt as $row)
		{
			$progNoArr[$row[csf('id')]] = $row[csf('id')];
			$salesOrderIdArr[$row[csf('po_id')]] = $row[csf('po_id')];

			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['plan_no'] = $row[csf('mst_id')];
			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['program_no'][] = $row[csf('id')];
			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['booking_date'] = $row[csf('booking_date')];
			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['buyer_id'] = ($row[csf('within_group')]==1?$company_arr[$row[csf('buyer_id')]]:$buyer_arr[$row[csf('buyer_id')]]);
			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['sales_order_no'] = $row[csf('job_no')];
			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['style_ref_no'] = $row[csf('style_ref_no')];

			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['color_id'][] = $row[csf('color_id')];
			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['program_qnty'][$row[csf('id')]] += $row[csf('program_qnty')];
			$revisedDataArr[$row[csf('id')]][$row[csf('dia')]][$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('width_dia_type')]]['po_id'] = $row[csf('po_id')];
		}


		$requisitionSQL="select knit_id,requisition_no from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0" .where_con_using_array($progNoArr, '0', 'knit_id');
		$sqlRequisitionRslt=sql_select($requisitionSQL);
		foreach($sqlRequisitionRslt as $row)
		{
			$progWiseRequArr[$row[csf('knit_id')]]['requisition_no']=$row[csf('requisition_no')];
		}
		//echo "<pre>";
		//print_r($revisedDataArr); die;

		/*
		$sqlSales ="
		select
			a.mst_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.grey_qty, a.pre_cost_fabric_cost_dtls_id
		from
			fabric_sales_order_dtls a
			left join fabric_sales_order_dtls b on (a.mst_id=b.mst_id and b.status_active=0 and b.is_deleted=1
		and a.pre_cost_fabric_cost_dtls_id= b.pre_cost_fabric_cost_dtls_id and a.color_id=b.color_id)
		where
			a.mst_id in(".implode(',', $salesOrderIdArr).") and a.status_active=1 and a.is_deleted=0
		group by
			a.mst_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty,  a.grey_qty, a.pre_cost_fabric_cost_dtls_id
		order by
			a.body_part_id";
		//echo $sqlSales;
		$sqlSalesRslt = sql_select($sqlSales);
		$salesDataArr = array();
		foreach($sqlSalesRslt as $row)
		{
			$salesDataArr[$row[csf('mst_id')]][$row[csf('body_part_id')]][$row[csf('fabric_desc')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_type_id')]][$row[csf('color_id')]]['grey_qty'] += $row[csf('grey_qty')];
		}
		*/
		//echo "<pre>";
		//print_r($salesDataArr); die;

		//for production
		$sqlProduction = "SELECT a.booking_id, b.grey_receive_qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id=b.mst_id and a.entry_form=2 and a.item_category = 13 ".where_con_using_array($progNoArr, '0', 'a.booking_id');
		//echo $sqlProduction;
		$sqlProductionRslt = sql_select($sqlProduction);
		$productonData = array();
		foreach($sqlProductionRslt as $row)
		{
			$productonData[$row[csf('booking_id')]]['production_qty'] += $row[csf('grey_receive_qnty')];
		}
		//echo "<pre>";
		//print_r($productonData); die;

		$status_arr = array();
		$check_production_arr = array();
		$check_sales_order_status = sql_select("select a.mst_id,a.pre_cost_fabric_cost_dtls_id,listagg(a.status_active, ',') within group (order by a.id) as status_active from fabric_sales_order_dtls a group by a.pre_cost_fabric_cost_dtls_id");
		foreach ($check_sales_order_status as $status_row)
		{
			$status_arr[$status_row[csf('pre_cost_fabric_cost_dtls_id')]][$status_row[csf('mst_id')]] = $status_row[csf('status_active')];
		}
		//echo "<pre>";
		//print_r($status_arr); die;

		$check_program_in_req_issue = sql_select("select a.id,a.knit_id,b.recv_number from ppl_yarn_requisition_entry a left join inv_receive_master b on a.knit_id=b.booking_id where b.company_id=$company_name and b.entry_form=2 and b.item_category=13 and b.receive_basis=2");
		foreach ($check_program_in_req_issue as $check_production)
		{
			$check_production_arr[$check_production[csf('knit_id')]] = $check_production[csf('recv_number')];
		}
		//echo "<pre>";
		//print_r($check_production_arr); die;
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset>
				<legend>Fabric Description Details</legend>
				<input type="button" value="Delete Program" name="generate" id="generate" class="formbutton" style="width:150px" onClick="delete_prog()"/>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                    <thead>
                        <th width="40"></th>
                        <th width="40">SL</th>
                        <th width="50">Plan Id</th>
                        <th width="60">Prog. No</th>
                        <th width="60">Requ. No</th>
                        <th width="100">Booking No</th>
                        <th width="70">Booking Date</th>
                        <th width="60">Buyer</th>
                        <th width="105">Sales Order No</th>
                        <th width="100">Style</th>
                        <th width="80">Body Part</th>
                        <th width="70">Color Type</th>
                        <th width="140">Fabric Desc.</th>
                        <th width="50">Gsm</th>
                        <th width="50">Dia</th>
                        <th width="70">Width/Dia Type</th>
                        <th width="70">Color</th>
                        <!--<th width="70">Sales Order Qty</th>-->
                        <th width="70">Prog. Qnty</th>
                        <!--<th>Balance Prog. Qnty</th>-->
                        <th>Production Qnty</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?
                        $i = 0;
                        $k = 1;
                        $z = 1;
                        $dia_array = array();
                        $a = '';
                        $colspan = 20;
                        $colspan2 = 17;
                        foreach ($revisedDataArr as $program=>$programArr)
                        {
	                        foreach ($programArr as $die=>$diaArr)
	                        {
	                            $tatalDia_programQty = 0;
	                            $tatalDia_productonQty = 0;
	                            ?>
	                            <tr>
	                                <td colspan="<?php echo $colspan; ?>">Dia/Width: <?php echo $die; ?></td>
	                            </tr>
	                            <?php
	                            foreach($diaArr as $bookingNo=>$bookingArr)
	                            {
	                                foreach($bookingArr as $bodyPartId=>$bodyPartArr)
	                                {
	                                    foreach($bodyPartArr as $colorTypeId=>$colorTypeArr)
	                                    {
	                                        foreach($colorTypeArr as $fabDesc=>$fabDescArr)
	                                        {
	                                            foreach($fabDescArr as $gsm=>$gsmArr)
	                                            {
	                                                foreach($gsmArr as $widthDia=>$row)
	                                                {
	                                                    $i++;
	                                                    //for sales order qty and color name
	                                                    //$salesOrderQty = 0;
	                                                    $colorName = '';
	                                                    foreach($row['color_id'] as $color)
	                                                    {
	                                                        $expColor = explode(',', $color);
	                                                        foreach($expColor as $clrID)
	                                                        {
	                                                            //$salesOrderQty += $salesDataArr[$row['po_id']][$bodyPartId][$fabDesc][$gsm][$die][$colorTypeId][$clrID]['grey_qty'];
	                                                            if($colorName != '')
	                                                            {
	                                                                $colorName .= ', ';
	                                                            }
	                                                            $colorName .= $color_library[$clrID];
	                                                        }
	                                                    }

	                                                    //for program qty and production qty
	                                                    $programQty = 0;
	                                                    $productonQty = 0;
	                                                    $requisitionNo="";
	                                                    foreach($row['program_no'] as $progNo)
	                                                    {
	                                                        $programQty += $row['program_qnty'][$progNo];
	                                                        $productonQty += $productonData[$progNo]['production_qty'];
	                                                        if($progWiseRequArr[$progNo]['requisition_no']!="")
	                                                        {
	                                                        	$requisitionNo.=$progWiseRequArr[$progNo]['requisition_no'].',';
	                                                        }
	                                                    }

	                                                    //for balance qty
	                                                    //$balanceQty = $salesOrderQty - $programQty;

	                                                    ?>
	                                                    <tr>
	                                                        <td align="center" valign="middle">
	                                                        <input type="checkbox" id="tbl_<? echo $i; ?>" name="check[]" <? echo $disabled; ?>/>
	                                                        <input type="hidden" id="promram_id_<? echo $i; ?>" name="promram_id[]" value="<? echo implode(', ', $row['program_no']); ?>"/>
	                                                        </td>
	                                                        <td><?php echo $i;?></td>
	                                                        <td><?php echo $row['plan_no']; ?></td>
	                                                        <td title="<?echo $type;?>">
	                                                        <?php
	                                                        //echo implode(', ', $row['program_no']);
	                                                        if ($type==3)
	                                                        {

	                                                        	$print_program_no = "";
																$prog_no_arr = array_unique(explode(",", implode(', ', $row['program_no'])));
																//print_r($prog_no_arr);







																foreach ($prog_no_arr as $prog)
																{
																	if($prog != "")
																	{
																		$print_program_no .= "<a href='##' onclick=\"generate_delete_booking_report(" . $company_name . "," . $prog . "," . $program_info_format_id . ")\">" . $prog . "</a>,";
																	}
																}
																echo rtrim($print_program_no,", ");
	                                                        }
	                                                        else
	                                                        {
	                                                        	echo implode(', ', $row['program_no']);
	                                                        }

	                                                        ?></td>
	                                                        <td><?php echo rtrim($requisitionNo,", "); ?></td>
	                                                        <td><?php echo $bookingNo; ?></td>
	                                                        <td><?php echo date('d-m-Y', strtotime($row['booking_date'])); ?></td>
	                                                        <td><?php echo $row['buyer_id']; ?></td>
	                                                        <td><?php echo $row['sales_order_no']; ?></td>
	                                                        <td><?php echo $row['style_ref_no']; ?></td>
	                                                        <td><?php echo $body_part[$bodyPartId]; ?></td>
	                                                        <td><?php echo $color_type[$colorTypeId]; ?></td>
	                                                        <td><?php echo $fabDesc; ?></td>
	                                                        <td><?php echo $gsm; ?></td>
	                                                        <td><?php echo $die; ?></td>
	                                                        <td><?php echo $fabric_typee[$widthDia]; ?></td>
	                                                        <td><?php echo $colorName; ?></td>
	                                                        <!--<td><?php //echo $salesOrderQty; ?></td>-->
	                                                        <td><input type="text" class="text_boxes_numeric" name="prog_qty[]" id="prog_qty_<?php echo $i; ?>" value="<?php echo number_format($programQty, 2, '.', ''); ?>" style="width:80px"/></td>
	                                                        <!--<td><?php //echo $balanceQty; ?></td>-->
	                                                        <td align="right"><?php echo number_format($productonQty, 2, '.', ''); ?></td>
	                                                        <td align="center">
	                                                        <input type="button" value="Update" onClick="fnc_update(<?php echo $i; ?>,0);" class="formbutton" style="width:80px">
	                                                        <input type="hidden" name="reqsn_found_or_not[]" id="reqsn_found_or_not_<?php echo $i; ?>" value="<?php echo $reqsn_found_or_not; ?>"/>
	                                                        </td>
	                                                    </tr>
	                                                    <?php
	                                                    $tatalDia_programQty += number_format($programQty, 2, '.', '');
	                                                    $tatalDia_productonQty += number_format($productonQty, 2, '.', '');

	                                                    $grand_tatalDia_programQty += number_format($programQty, 2, '.', '');
	                                                    $grand_tatalDia_productonQty += number_format($productonQty, 2, '.', '');
	                                                }
	                                            }
	                                        }
	                                    }
	                                }
	                            }
	                            ?>
	                            <tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
	                                <td colspan="<?php echo $colspan2; ?>" align="right"><b>Sub Total</b></td>
	                               <!-- <td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>-->
	                                <td align="right"><b><? echo number_format($tatalDia_programQty, 2, '.', ''); ?></b></td>
	                                <!--<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>-->
	                                <td align="right"><b><? echo number_format($tatalDia_productonQty, 2, '.', ''); ?></b></td>
	                            </tr>
	                            <?php
                        	}
                        }
                        ?>
                        </tbody>
						<tfoot>
                        	<th colspan="17" align="right">Grand Total<input type="hidden" name="company_id" id="company_id"
                            value="<? echo $company_name; ?>"/></th>
                            <!--<th align="right"><? echo number_format($total_qnty, 2, '.', ''); ?></th>-->
                            <th align="right"><? echo number_format($grand_tatalDia_programQty, 2, '.', ''); ?></th>
                            <!--<th align="right"><? echo number_format($grand_total_balance, 2, '.', ''); ?></th>-->
                            <th align="right"><? echo number_format($grand_tatalDia_productonQty, 2, '.', ''); ?></th>
                            <th><input type="hidden" name="hiddenVariableCollarCuff" id="hiddenVariableCollarCuff" value="<? echo $txtVariableCollarCuff; ?>"/>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </form>
        <?
    }
	exit();
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), "yyyy-mm-dd", "");
		} else {
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), '', '', 1);
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), '', '', 1);
		}

		if (str_replace("'", '', $updateId) != "") {
			// CHECK IF (PROGRAM QUANTITY+EXISTING PROGRAM QNTY) IS NOT GREATER THAN BOOKING QNTY
			$get_existing_program_qty = return_field_value("sum(program_qnty) as program_qnty", "ppl_planning_entry_plan_dtls", "mst_id=$updateId and status_active=1 and is_deleted=0", "program_qnty");

			if ((str_replace("'", "", $txt_program_qnty)+($get_existing_program_qty*1)) > ceil(str_replace("'", "", $hdn_booking_qnty))) {
				echo "14**Program quantity can not be greater than Booking quantitys.";
				disconnect($con);
				exit();
			}
		}else{
			if (str_replace("'", "", $txt_program_qnty) > ceil(str_replace("'", "", $hdn_booking_qnty))) {
				echo "14**Program quantity can not be greater than Booking quantity";
				disconnect($con);
				exit();
			}
		}

		if (str_replace("'", "", $within_group)==2)
		{
			$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.within_group=2 and a.booking_no=$booking_no and b.status_active=1 and b.is_deleted=0", "knitting_qnty");
		}
		else
		{
			$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_no=$booking_no and b.status_active=1 and b.is_deleted=0", "knitting_qnty");
		}

		if ($knit_qty > str_replace("'", "", $txt_program_qnty)) {
			echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
			disconnect($con);
			exit();
		}

		$id = '';
		$dia=strtoupper($dia);
		if (str_replace("'", '', $updateId) == "") {
			$id = return_next_id("id", "ppl_planning_info_entry_mst", 1);
			$body_part_idArr=explode(',', $body_part_id);
			$body_part_id_mst=$body_part_idArr[0];
			$field_array = "id, company_id, within_group, buyer_id, booking_no, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, is_sales, inserted_by, insert_date";
			$data_array = "(" . $id . "," . $companyID . "," . $within_group . "," . $buyer_id . ",'" . $booking_no . "'," . $body_part_id_mst . "," . $color_type_id . "," . $determination_id . "," . $hdn_fab_desc . ",'" . $gsm . "','" . $dia . "'," . $fabric_typee . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		} else {
			$id = str_replace("'", '', $updateId);
			$flag = 1;
		}

		$dtls_id = return_next_id("id", "ppl_planning_info_entry_dtls", 1);
		$field_array_dtls = "id, mst_id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio,  machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, location_id, advice, is_sales, batch_no, no_of_ply, tube_ref_no, inserted_by, insert_date";
		$txt_fabric_dia=strtoupper($txt_fabric_dia);
		$data_array_dtls = "(" . $dtls_id . "," . $id . "," . $cbo_knitting_source . "," . $cbo_knitting_party . "," . $hidden_color_id . "," . $cbo_color_range . "," . $txt_machine_dia . "," . $cbo_dia_width_type . "," . $txt_machine_gg . "," . $txt_fabric_dia . "," . $txt_program_qnty . "," . $txt_stitch_length . "," . $txt_spandex_stitch_length . "," . $txt_draft_ratio . "," . $machine_id . "," . $txt_machine_capacity . "," . $txt_distribution_qnty . "," . $cbo_knitting_status . "," . $txt_start_date . "," . $txt_end_date . "," . $txt_program_date . "," . $cbo_feeder . "," . $txt_remarks . "," . $save_data . "," . $cbo_location_name . "," . $hidden_advice_data . ",1," . $txt_batch_no . "," . $txt_no_of_ply . "," . $txt_tube_ref_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
		$field_array_plan_dtls = "id, mst_id, dtls_id, company_id, within_group, buyer_id, booking_no, po_id, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id,program_qnty, is_sales,sales_order_dtls_ids,pre_cost_fabric_cost_dtls_id, inserted_by, insert_date";
		$data = str_replace("'", "", $data);
		if ($data != "") {
			$data = explode("_", $data);
			$sales_dtls_id = explode("_", $sales_order_dtls_id);
			$pre_cost = explode("_", $pre_cost);
			for ($i = 0; $i < count($data); $i++) {
				$plan_data = explode("**", $data[$i]);
				$booking_no = $plan_data[0];
				$job_id = $plan_data[1];
				$withinGroup = $plan_data[2];
				$buyer_id = $plan_data[4];
				$body_part_id = $plan_data[5];
				$dia_width_type = $plan_data[6];
				$desc = trim($plan_data[7]);
				$gsm_weight = $plan_data[8];
				$dia_width = $plan_data[9];
				$dia_width = strtoupper($dia_width);
				$determination_id = $plan_data[10];
				$booking_qnty = $plan_data[11];
				$color_type_id = $plan_data[12];

				if ($db_type == 0) {
					$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
					$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
				} else {
					$start_date = change_date_format($start_date, '', '', 1);
					$end_date = change_date_format($end_date, '', '', 1);
				}

				$perc = ($booking_qnty / $tot_booking_qnty) * 100;
				$prog_qnty = number_format(($perc * str_replace("'", '', $txt_program_qnty) / 100), 2, '.', '');

				if ($data_array_plan_dtls != "") $data_array_plan_dtls .= ",";

				$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $id . "," . $dtls_id . "," . $companyID . "," . $withinGroup . "," . $buyer_id . ",'" . $booking_no . "'," . $job_id . "," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "'," . $gsm . ",'" . $dia_width . "'," . $dia_width_type . ",0," . $prog_qnty . ",1,'" . $sales_dtls_id[$i] . "','" . $pre_cost[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$plan_dtls_id = $plan_dtls_id + 1;
			}
		}

		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";

		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";

		$save_data = str_replace("'", "", $save_data);
		if ($save_data != "") {
			$save_data = explode(",", $save_data);
			for ($i = 0; $i < count($save_data); $i++) {
				$machine_wise_data = explode("_", $save_data[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
				$dia = strtoupper($dia);
				$capacity = $machine_wise_data[2];
				$qnty = $machine_wise_data[3];
				$noOfDays = $machine_wise_data[4];

				$dateWise_qnty = 0;
				$bl_qnty = $qnty;

				if ($machine_wise_data[5] != "") $startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
				if ($machine_wise_data[6] != "") $endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

				if ($startDate != "" && $endDate != "") {
					$sCurrentDate = date("Y-m-d", strtotime("-1 day", strtotime($startDate)));
					$days = $noOfDays;
					$fraction = 0;
					$days_complete = 0;
					while ($sCurrentDate < $endDate) {
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
						if ($days >= 1) {
							$fraction = 0;
							$days_complete = 1;
							$dateWise_qnty = $capacity;
						} else {
							$fraction = 1;
							$days_complete = $days;
							$dateWise_qnty = $bl_qnty;
						}

						$days = $days - 1;
						$bl_qnty = $bl_qnty - $capacity;

						if ($db_type == 0) $curr_date = $sCurrentDate; else $curr_date = change_date_format($sCurrentDate, '', '', 1);

						if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";
						$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $id . "," . $dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "','" . $machine_dtls_id . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
					}
				}

				if ($db_type == 0) {
					$mstartDate = $startDate;
					$mendDate = $endDate;
				} else {
					$mstartDate = change_date_format($startDate, '', '', 1);
					$mendDate = change_date_format($endDate, '', '', 1);
				}

				if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $id . "," . $dtls_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$machine_dtls_id = $machine_dtls_id + 1;
			}
		}

		$feeder_dtls_id = return_next_id("id", "ppl_planning_feeder_dtls", 1);
		$field_array_feeder_dtls = "id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder,sequence, inserted_by, insert_date";

		$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
		if ($hidden_no_of_feeder_data != "")
		{
			$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
			for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++) {
				$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
				$pre_cost_id = $color_wise_data[0];
				$color_id = $color_wise_data[1];
				$stripe_color_id = $color_wise_data[2];
				$no_of_feeder = $color_wise_data[3];
				$txtFeederSequence = $color_wise_data[5];

				if ($data_array_feeder_dtls != "") $data_array_feeder_dtls .= ",";

				$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $id . "," . $dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "','" . $txtFeederSequence . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$feeder_dtls_id = $feeder_dtls_id + 1;
			}
		}

		$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
		if ($hidden_collarCuff_data != "")
		{
			$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
			$field_array_collar_cuff_dtls = "id, mst_id, dtls_id, body_part_id, grey_size, finish_size, qty_pcs, needle_per_cm, inserted_by, insert_date,is_sales";

			$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
			for ($i = 0; $i < count($hidden_collarCuff_data); $i++) {
				$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
				$body_part_id = $collarCuff_wise_data[0];
				$grey_size = $collarCuff_wise_data[1];
				$finish_size = $collarCuff_wise_data[2];
				$qty_pcs = $collarCuff_wise_data[3];
				$needle_per_cm = $collarCuff_wise_data[4];

				if ($data_array_collar_cuff_dtls != "") $data_array_collar_cuff_dtls .= ",";

				$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $id . "," . $dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "','".$needle_per_cm."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1)";

				$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
			}
		}

		// == Came Design ==
		$hidden_came_dsign_string_data = str_replace("'", "", $hidden_came_dsign_string_data);
		if ($hidden_came_dsign_string_data != "")
		{
			$cam_design_dtls_id = return_next_id("id", "ppl_planning_cam_design_dtls", 1);
			$field_array_cam_design_dtls = "id, mst_id, dtls_id, cmd1, cmd2, cmd3, cmd4, cmd5, cmd6, cmd7, cmd8, cmd9, cmd10, cmd11, cmd12, cmd13, cmd14, cmd15, cmd16, cmd17, cmd18, cmd19, cmd20, cmd21, cmd22, cmd23, cmd24, inserted_by, insert_date,is_sales";

			$came_dsign_string_data = explode(",", $hidden_came_dsign_string_data);
			for ($i = 0; $i < count($came_dsign_string_data); $i++) {
				$came_dsign_data = explode("_", $came_dsign_string_data[$i]);
				$udpdateId = $came_dsign_data[0];
				$cmd1 = $came_dsign_data[1];
				$cmd2 = $came_dsign_data[2];
				$cmd3 = $came_dsign_data[3];
				$cmd4 = $came_dsign_data[4];
				$cmd5 = $came_dsign_data[5];
				$cmd6 = $came_dsign_data[6];
				$cmd7 = $came_dsign_data[7];
				$cmd8 = $came_dsign_data[8];
				$cmd9 = $came_dsign_data[9];
				$cmd10 = $came_dsign_data[10];
				$cmd11 = $came_dsign_data[11];
				$cmd12 = $came_dsign_data[12];
				$cmd13 = $came_dsign_data[13];
				$cmd14 = $came_dsign_data[14];
				$cmd15 = $came_dsign_data[15];
				$cmd16 = $came_dsign_data[16];
				$cmd17 = $came_dsign_data[17];
				$cmd18 = $came_dsign_data[18];
				$cmd19 = $came_dsign_data[19];
				$cmd20 = $came_dsign_data[20];
				$cmd21 = $came_dsign_data[21];
				$cmd22 = $came_dsign_data[22];
				$cmd23 = $came_dsign_data[23];
				$cmd24 = $came_dsign_data[24];

				if ($data_array_cam_design_dtls != "") $data_array_cam_design_dtls .= ",";

				$data_array_cam_design_dtls .= "(" . $cam_design_dtls_id . "," . $id . "," . $dtls_id . ",'" . $cmd1 . "','" . $cmd2 . "','" . $cmd3 . "','" . $cmd4 . "','" . $cmd5 . "','" . $cmd6 . "','" . $cmd7 . "','" . $cmd8 . "','" . $cmd9 . "','" . $cmd10 . "','" . $cmd11 . "','" . $cmd12 . "','" . $cmd13 . "','" . $cmd14 . "','" . $cmd15 . "','" . $cmd16 . "','" . $cmd17 . "','" . $cmd18 . "','" . $cmd19 . "','" . $cmd20 . "','" . $cmd21 . "','" . $cmd22 . "','" . $cmd23 . "','" . $cmd24 . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1)";

				$cam_design_dtls_id = $cam_design_dtls_id + 1;
			}
		}
		// === came design end===

		if(str_replace("'", "", $prog_needle_layout_data_have)==1)
		{
			$needle_layout_id = return_next_id("id", "ppl_planning_needle_layout", 1);
			$field_array_needle_layout = "id,plan_id, program_no, dial, cylinder, dial_row1, dial_row2, no_of_feeder, cylinder_row1, cylinder_row2, cylinder_row3, cylinder_row4, yarn_ends, lfa, yarn_tension, grey_gsm, t_dry_weight, t_dry_width, rpm, f_roll_width, laid_width,active_feeder,rev_per_kg,dial_height,is_sales,inserted_by, insert_date";

			$data_array_needle_layout = "(".$needle_layout_id."," . $id . "," . $dtls_id . "," . $prog_hidden_dial . "," . $prog_hidden_cylinder . "," . $prog_hidden_dial_row1 . "," . $prog_hidden_dial_row2 . "," . $prog_hidden_no_of_feeder . "," . $prog_hidden_cylinder_row1 . "," . $prog_hidden_cylinder_row2 . "," . $prog_hidden_cylinder_row3 . "," . $prog_hidden_cylinder_row4 . "," . $prog_hidden_yarn_ends . "," . $prog_hidden_lfa . "," . $prog_hidden_yarn_tension . "," . $prog_hidden_grey_gsm . "," . $prog_hidden_tdry_weight . "," . $prog_hidden_tdry_width . "," . $prog_hidden_rpm . "," . $prog_hidden_froll_width . "," . $prog_hidden_laid_width . "," . $prog_hidden_active_feeder . "," . $prog_hidden_rev_per_kg . "," . $prog_hidden_dial_height  . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		}

		//echo "10**".$data_array_cam_design_dtls; die();
		// === CAME END ===

		//for color wise program
		$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
		$hidden_size_wise_prog_string = str_replace("'", "", $hidden_size_wise_prog_string);
		if( $hidden_color_wise_prog_data != "" )
		{
			$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
			$field_array_color_wise_break_down = "id, plan_id, program_no, color_id, color_prog_qty,size_wise_prog_string, inserted_by, insert_date";

			$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
			$size_wise_prog_data = explode("**", $hidden_size_wise_prog_string);
			$color_wise_mstID = array();
			for ($i = 0; $i < count($color_wise_prog_data); $i++)
			{
				if($color_wise_prog_data[$i] != '')
				{
					$sizeWiseDataString=$size_wise_prog_data[$i];
					$colorWiseProgData = array();
					$color_id = '';
					$color_prog_qty = 0;

					$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
					$color_id = $colorWiseProgData[0];
					$color_prog_qty = $colorWiseProgData[1];

					if ($data_array_color_wise_break_down != "")
						$data_array_color_wise_break_down .= ",";

					if($color_prog_qty>0)
					{
						$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $id . "," . $dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "','" . $sizeWiseDataString . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$color_wise_mstID[$color_id]=$color_wise_break_down_id;
						$color_wise_break_down_id = $color_wise_break_down_id + 1;
					}
				}
			}
		}
		//end
		/*oci_rollback($con);
		echo "5**0**0**".$data_array_color_wise_break_down;
		disconnect($con);
		die;*/

		if( $hidden_color_wise_prog_data != "" && $hidden_size_wise_prog_string != "")
		{
			$size_wise_break_down_id = return_next_id("id", "ppl_size_wise_break_down ", 1);
			$field_array_size_wise_break_down = "id,color_wise_mst_id, plan_id, program_no, color_id,grey_size_id,finish_size_id,per_kg, kg_wise_total_qnty,body_part_id,current_qty, inserted_by, insert_date";
			$size_wise_prog_datas = explode("**", $hidden_size_wise_prog_string);
			for ($inc = 0; $inc < count($size_wise_prog_datas); $inc++)
			{
				$size_wise_prog_dataz = explode("##", $size_wise_prog_datas[$inc]);
				for ($i = 0; $i < count($size_wise_prog_dataz); $i++)
				{
					if($size_wise_prog_dataz[$i] != '')
					{

						$sizeWiseProgData = array();
						$color_id = '';
						$gmts_size_id = '';
						$finish_size_id = '';
						$per_kg = '';
						$size_wise_current_qty = '';
						$size_kg_wise_prog_qty = 0;

						$sizeWiseProgData = explode("_", $size_wise_prog_dataz[$i]);
						$color_id = $sizeWiseProgData[0];
						$gmts_size_id = $sizeWiseProgData[1];
						$finish_size_id = $sizeWiseProgData[2];
						$per_kg = $sizeWiseProgData[3];
						$size_kg_wise_prog_qty = $sizeWiseProgData[4];
						$size_wise_body_part_id = $sizeWiseProgData[6];
						$size_wise_current_qty = $sizeWiseProgData[7];

						if ($data_array_size_wise_break_down != "")
							$data_array_size_wise_break_down .= ",";

						if($size_kg_wise_prog_qty>0)
						{
							$colorWiseMstId=$color_wise_mstID[$color_id];
							$data_array_size_wise_break_down .= "(" . $size_wise_break_down_id . "," . $colorWiseMstId . "," . $id . "," . $dtls_id . ",'" . $color_id . "','" . $gmts_size_id . "','" . $finish_size_id . "','" . $per_kg . "','" . $size_kg_wise_prog_qty . "','" . $size_wise_body_part_id . "','" . $size_wise_current_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$size_wise_break_down_id = $size_wise_break_down_id + 1;
						}

					}
				}

			}
		}


		$rmsintegretion =return_field_value("rms_integretion","variable_settings_production","company_name='$companyID' and variable_list=7 and is_deleted=0 and status_active=1");
		if($rmsintegretion==1) // rms integretion: yes
		{
			$exists_sl = sql_select("select a.booking_no,max(a.update_sl) as update_sl_no from ppl_planning_entry_plan_dtls a  where a.booking_no = '$booking_no' and a.status_active = 1 and a.is_deleted = 0 group by booking_no");
		}


		if (str_replace("'", '', $updateId) == "")
		{
			//echo "10**insert into ppl_planning_info_entry_mst (".$field_array.") Values ".$data_array."";die;
			$rID = sql_insert("ppl_planning_info_entry_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1; else $flag = 0;
		}
		else
		{
			$flag = 1;
		}

		//echo "10**insert into ppl_planning_info_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		$rID2 = sql_insert("ppl_planning_info_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1) {
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($data != "") {
			if ($data_array_plan_dtls != "") {
				//echo "10**insert into ppl_planning_entry_plan_dtls (".$field_array_plan_dtls.") Values ".$data_array_plan_dtls."";die;
				$rIDdtls = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
				if ($flag == 1) {
					if ($rIDdtls) $flag = 1; else $flag = 0;
				}
			}
		}

		// ==== Rms Integretion depending veriable setting start=====///
		if($rmsintegretion==1) // rms integretion: yes
		{
			$incrementSl = 0;
			$exists_slNo = $exists_sl[0][csf('update_sl_no')]*1;
			if($exists_slNo>0)
			{
				$incrementSl = $exists_slNo+1;
			}

			//echo "10**update ppl_planning_entry_plan_dtls set update_sl=$incrementSl where dtls_id=$dtls_id"; die();
			$rmsIntegretionID = execute_query("update ppl_planning_entry_plan_dtls set update_sl=$incrementSl where dtls_id=$dtls_id", 0);
			if ($flag == 1)
			{
				if ($rmsIntegretionID) $flag = 1;
				else $flag = 0;
			}
		}
		// ==== Rms Integretion depending veriable setting start=====///

		if ($save_data != "") {
			if ($data_array_machine_dtls != "") {
				//echo "10**insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID3 = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($flag == 1) {
					if ($rID3) $flag = 1; else $flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "") {
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID4 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1) {
					if ($rID4) $flag = 1; else $flag = 0;
				}
			}
		}

		//echo "10**".$flag;die;
		//$hidden_no_of_feeder_data=str_replace("'","",$hidden_no_of_feeder_data);
		if ($hidden_no_of_feeder_data != "") {
			if ($data_array_feeder_dtls != "") {
				//echo "10**insert into ppl_planning_feeder_dtls (".$field_array_feeder_dtls.") Values ".$data_array_feeder_dtls."";die;
				$rID5 = sql_insert("ppl_planning_feeder_dtls", $field_array_feeder_dtls, $data_array_feeder_dtls, 0);
				if ($flag == 1) {
					if ($rID5) $flag = 1; else $flag = 0;
				}
			}
		}

		if ($data_array_collar_cuff_dtls != "") {
			//echo "10**insert into ppl_planning_collar_cuff_dtls (".$field_array_collar_cuff_dtls.") Values ".$data_array_collar_cuff_dtls."";die;
			$rID6 = sql_insert("ppl_planning_collar_cuff_dtls", $field_array_collar_cuff_dtls, $data_array_collar_cuff_dtls, 0);
			if ($flag == 1) {
				if ($rID6) $flag = 1; else $flag = 0;
			}
		}

		if ($data_array_cam_design_dtls != "") {
			//echo "10**insert into ppl_planning_cam_design_dtls (".$field_array_cam_design_dtls.") Values ".$data_array_cam_design_dtls."";die;
			$rID7 = sql_insert("ppl_planning_cam_design_dtls", $field_array_cam_design_dtls, $data_array_cam_design_dtls, 0);
			if ($flag == 1) {
				if ($rID7) $flag = 1; else $flag = 0;
			}
		}

		if ($data_array_needle_layout != "") {
			//echo "10**insert into ppl_planning_needle_layout (".$field_array_needle_layout.") Values ".$data_array_needle_layout."";die;
			$rID8  = sql_insert("ppl_planning_needle_layout", $field_array_needle_layout, $data_array_needle_layout, 0);
			if ($flag == 1) {
				if ($rID8) $flag = 1; else $flag = 0;
			}
		}

		//for color wise program
		if ($data_array_color_wise_break_down != "")
		{
			//echo "10**insert into ppl_color_wise_break_down (".$field_array_color_wise_break_down.") Values ".$data_array_color_wise_break_down."";die;
			$rID9 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
			if ($flag == 1)
			{
				if ($rID9) $flag = 1;
				else $flag = 0;
			}
		}
		if ($data_array_size_wise_break_down != "") {
		//echo "10**insert into ppl_size_wise_break_down (".$field_array_size_wise_break_down.") Values ".$data_array_size_wise_break_down."";die;
			$rID10 = sql_insert("ppl_size_wise_break_down", $field_array_size_wise_break_down, $data_array_size_wise_break_down, 0);
			if ($flag == 1) {
				if ($rID10) $flag = 1; else $flag = 0;
			}
		}


		//echo "10**";$rmsIntegretionID $rID6
		/*echo "10**".$rID . "_" . $rID2 . "_" . $rIDdtls . "_" . $rmsIntegretionID . "_" . $rID3 . "_" . $rID4 . "_" . $rID5 . "_" . $rID6 . "_" . $rID7."_".$rID8."_".$rID9."_".$rID10."_".$flag;
		disconnect($con);
		die();*/

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $id . "**0";
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

 		// CHECK IF (PROGRAM QUANTITY+EXISTING PROGRAM QNTY) IS NOT GREATER THAN BOOKING QNTY
		$get_existing_program_qty = return_field_value("sum(program_qnty) as program_qnty", "ppl_planning_entry_plan_dtls", "booking_no='$booking_no' and mst_id=$updateId and dtls_id != $update_dtls_id and status_active=1 and is_deleted=0", "program_qnty");

		$totalProgQty = number_format( str_replace("'", "", $txt_program_qnty)+($get_existing_program_qty*1) , 2, ".","" );
		$booking_qty = number_format( str_replace("'", "", $hdn_booking_qnty) , 2, "." , "" );

		if ( $totalProgQty > $booking_qty ) {
			echo "14**Program quantity can not be greater than Booking quantity";
			disconnect($con);
			exit();
		}
		if (str_replace("'", "", $within_group)==2)
		{
			$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.within_group=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");
		}
		else
		{
			$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		}

		if ($knit_qty > str_replace("'", "", $txt_program_qnty)) {
			if(str_replace("'", "", $txt_program_qnty) < str_replace("'", "", $hiddenProgramQnty))
			{
				echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
				disconnect($con);
				exit();
			}
		}

		//dated 17.08.2021
		$programQnty = str_replace("'", "", $txt_program_qnty)*1;
		$preProgramQnty = str_replace("'", "", $hiddenProgramQnty)*1;
		$balanceQnty = str_replace("'", "", $balanceProgramQnty)*1;
		if(number_format(($programQnty - $preProgramQnty),2,'.','') > number_format($balanceQnty,2,'.','') )
		{
			echo "14**Program Qty. Can not Be Greater Than Booking Balance Qty.";
			disconnect($con);
			exit();
		}
		//end

		/*
		| if issue found then
		| knitting party can't update
		*/
		$sqlIssue = sql_select("select c.issue_number from ppl_yarn_requisition_entry a, inv_transaction b, inv_issue_master c where a.requisition_no = b.requisition_no and b.mst_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.item_category = 1 and b.transaction_type = 2 and a.knit_id = ".$update_dtls_id."");
		$issueData = array();
		foreach($sqlIssue as $prow)
		{
			$issueData[$prow[csf('issue_number')]] = $prow[csf('issue_number')];
		}

		if(!empty($issueData))
		{
			$sqlProg = sql_select("SELECT knitting_party AS KNITTING_PARTY, color_id AS COLOR_ID, color_range AS COLOR_RANGE, machine_gg AS MACHINE_GG, machine_dia AS MACHINE_DIA, width_dia_type AS WIDTH_DIA_TYPE, fabric_dia AS FABRIC_DIA, stitch_length AS STITCH_LENGTH FROM ppl_planning_info_entry_dtls WHERE id = ".$update_dtls_id."");
			$exist_knitting_party = '';
			foreach($sqlProg as $row)
			{
				$exist_knitting_party = $row['KNITTING_PARTY'];
			}

			//for knitting party
			if($exist_knitting_party != str_replace("'","",$cbo_knitting_party))
			{
				echo "14**Issue found and issue no : ".implode(', ', $issueData).".\nYou can't change knitting party.";
				disconnect($con);
				exit();
			}
		}

		$color_id = 0;
		$field_array_update = "knitting_source*knitting_party*color_id*color_range*machine_dia*width_dia_type*machine_gg*fabric_dia*program_qnty*stitch_length*spandex_stitch_length*draft_ratio*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*program_date*feeder*remarks*save_data*location_id*advice*batch_no*no_of_ply*tube_ref_no*updated_by*update_date";
		$txt_fabric_dia=strtoupper($txt_fabric_dia);
		$data_array_update = $cbo_knitting_source . "*" . $cbo_knitting_party . "*" . $hidden_color_id . "*" . $cbo_color_range . "*" . $txt_machine_dia . "*" . $cbo_dia_width_type . "*" . $txt_machine_gg . "*" . $txt_fabric_dia . "*" . $txt_program_qnty . "*" . $txt_stitch_length . "*" . $txt_spandex_stitch_length . "*" . $txt_draft_ratio . "*" . $machine_id . "*" . $txt_machine_capacity . "*" . $txt_distribution_qnty . "*" . $cbo_knitting_status . "*" . $txt_start_date . "*" . $txt_end_date . "*" . $txt_program_date . "*" . $cbo_feeder . "*" . $txt_remarks . "*" . $save_data . "*" . $cbo_location_name . "*" . $hidden_advice_data . "*" . $txt_batch_no . "*" . $txt_no_of_ply . "*" . $txt_tube_ref_no . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
		$field_array_plan_dtls = "id, mst_id, dtls_id, company_id, within_group, buyer_id, booking_no, po_id, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, program_qnty, is_sales,sales_order_dtls_ids,pre_cost_fabric_cost_dtls_id, inserted_by, insert_date,update_sl";

		// this section is only for customers(e.g. Northern Toshrifa) who may use RMS for knitting production
		$rmsintegretion =return_field_value("rms_integretion","variable_settings_production","company_name='$companyID' and variable_list=7 and is_deleted=0 and status_active=1");
		$update_sl=0;
		if($rmsintegretion==1) // rms integretion: yes
		{
			$exists_sl = sql_select("select max(a.update_sl) as update_sl_no from ppl_planning_entry_plan_dtls a where a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0");
			$update_sl = $exists_sl[0][csf('update_sl_no')]+1;
		}

		$data = str_replace("'", "", $data);
		if ($data != "") {
			$data = explode("_", $data);
			$sales_dtls_id = explode("_", $sales_order_dtls_id);
			$pre_cost = explode("_", $pre_cost);
			for ($i = 0; $i < count($data); $i++) {
				$plan_data = explode("**", $data[$i]);
				$booking_no = $plan_data[0];
				$job_id = $plan_data[1];
				$withinGroup = $plan_data[2];
				$buyer_id = $plan_data[4];
				$body_part_id = $plan_data[5];
				$dia_width_type = $plan_data[6];
				$desc = trim($plan_data[7]);
				$gsm_weight = $plan_data[8];
				$dia_width = $plan_data[9];
				$dia_width = strtoupper($dia_width);
				$determination_id = $plan_data[10];
				$booking_qnty = $plan_data[11];
				$color_type_id = $plan_data[12];

				$perc = ($booking_qnty / $tot_booking_qnty) * 100;
				$prog_qnty = ($perc * str_replace("'", '', $txt_program_qnty)) / 100;

				if ($db_type == 0) {
					$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
					$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
				} else {
					$start_date = change_date_format($start_date, '', '', 1);
					$end_date = change_date_format($end_date, '', '', 1);
				}

				if ($data_array_plan_dtls != "") $data_array_plan_dtls .= ",";
				$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $updateId . "," . $update_dtls_id . "," . $companyID . "," . $withinGroup . "," . $buyer_id . ",'" . $booking_no . "'," . $job_id . "," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "'," . $gsm . ",'" . $dia_width . "'," . $dia_width_type . ",0," . $prog_qnty . ",1,'" . $sales_dtls_id[$i] . "','" . $pre_cost[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$update_sl.")";

				$plan_dtls_id = $plan_dtls_id + 1;
			}
		}

		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";
		$field_array_machine_dtls_update = "machine_id*dia*capacity*distribution_qnty*no_of_days*start_date*end_date*updated_by*update_date";

		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";

		$save_data = str_replace("'", "", $save_data);
		if ($save_data != "") {
			$save_data = explode(",", $save_data);
			for ($i = 0; $i < count($save_data); $i++) {
				$machine_wise_data = explode("_", $save_data[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
				$dia = strtoupper($dia);
				$capacity = $machine_wise_data[2];
				$qnty = $machine_wise_data[3];
				$noOfDays = $machine_wise_data[4];
				$dtls_id = $machine_wise_data[7];

				$dateWise_qnty = 0;
				$bl_qnty = $qnty;

				if ($machine_wise_data[5] != "") $startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
				if ($machine_wise_data[6] != "") $endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

				if ($db_type == 0) {
					$mstartDate = $startDate;
					$mendDate = $endDate;
				} else {
					$mstartDate = change_date_format($startDate, '', '', 1);
					$mendDate = change_date_format($endDate, '', '', 1);
				}

				if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$machine_plan_id = $machine_dtls_id;
				$machine_dtls_id = $machine_dtls_id + 1;

				if ($startDate != "" && $endDate != "") {
					$sCurrentDate = date("Y-m-d", strtotime("-1 day", strtotime($startDate)));
					$days = $noOfDays;
					$fraction = 0;
					$days_complete = 0;

					while ($sCurrentDate < $endDate) {
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));

						if ($days >= 1) {
							$fraction = 0;
							$days_complete = 1;
							$dateWise_qnty = $capacity;
						} else {
							$fraction = 1;
							$days_complete = $days;
							$dateWise_qnty = $bl_qnty;
						}

						$days = $days - 1;
						$bl_qnty = $bl_qnty - $capacity;

						if ($db_type == 0) $curr_date = $sCurrentDate; else $curr_date = change_date_format($sCurrentDate, '', '', 1);

						if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";

						$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "','" . $machine_plan_id . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
					}
				}
			}
		}

		// == Came Design ==
		$hidden_came_dsign_string_data = str_replace("'", "", $hidden_came_dsign_string_data);
		if ($hidden_came_dsign_string_data != "")
		{
			$field_array_cam_design_dtls_update = "cmd1*cmd2*cmd3*cmd4*cmd5*cmd6*cmd7*cmd8*cmd9*cmd10*cmd11*cmd12*cmd13*cmd14*cmd15*cmd16*cmd17*cmd18*cmd19*cmd20*cmd21*cmd22*cmd23*cmd24*updated_by*update_date*status_active*is_deleted";

			$cam_design_dtls_id = return_next_id("id", "ppl_planning_cam_design_dtls", 1);
			$field_array_cam_design_dtls = "id, mst_id, dtls_id, cmd1, cmd2, cmd3, cmd4, cmd5, cmd6, cmd7, cmd8, cmd9, cmd10, cmd11, cmd12, cmd13, cmd14, cmd15, cmd16, cmd17, cmd18, cmd19, cmd20, cmd21, cmd22, cmd23, cmd24, inserted_by, insert_date,is_sales";

			$came_dsign_string_data = explode(",", $hidden_came_dsign_string_data);
			for ($i = 0; $i < count($came_dsign_string_data); $i++) {
				$came_dsign_data = explode("_", $came_dsign_string_data[$i]);
				$came_udpdateId = $came_dsign_data[0];
				$cmd1 = $came_dsign_data[1];
				$cmd2 = $came_dsign_data[2];
				$cmd3 = $came_dsign_data[3];
				$cmd4 = $came_dsign_data[4];
				$cmd5 = $came_dsign_data[5];
				$cmd6 = $came_dsign_data[6];
				$cmd7 = $came_dsign_data[7];
				$cmd8 = $came_dsign_data[8];
				$cmd9 = $came_dsign_data[9];
				$cmd10 = $came_dsign_data[10];
				$cmd11 = $came_dsign_data[11];
				$cmd12 = $came_dsign_data[12];
				$cmd13 = $came_dsign_data[13];
				$cmd14 = $came_dsign_data[14];
				$cmd15 = $came_dsign_data[15];
				$cmd16 = $came_dsign_data[16];
				$cmd17 = $came_dsign_data[17];
				$cmd18 = $came_dsign_data[18];
				$cmd19 = $came_dsign_data[19];
				$cmd20 = $came_dsign_data[20];
				$cmd21 = $came_dsign_data[21];
				$cmd22 = $came_dsign_data[22];
				$cmd23 = $came_dsign_data[23];
				$cmd24 = $came_dsign_data[24];

				if ($came_udpdateId !="") {

					$cam_upd_id_arr[] = $came_udpdateId;
					$data_array_came_design_update[$came_udpdateId] = explode("*", ("'" . $cmd1 . "'*'". $cmd2 ."'*'". $cmd3 ."'*'". $cmd4 ."'*'". $cmd5 ."'*'". $cmd6 ."'*'". $cmd7 ."'*'". $cmd8 ."'*'". $cmd9 ."'*'". $cmd10 ."'*'". $cmd11 ."'*'". $cmd12 ."'*'". $cmd13 ."'*'". $cmd14 ."'*'". $cmd15 ."'*'". $cmd16 ."'*'". $cmd17 ."'*'". $cmd18 ."'*'". $cmd19 ."'*'". $cmd20 ."'*'". $cmd21 ."'*'". $cmd22 ."'*'". $cmd23 ."'*'". $cmd24 . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*1*0"));
				}else{

					if ($data_array_cam_design_dtls != "") $data_array_cam_design_dtls .= ",";

					$data_array_cam_design_dtls .= "(" . $cam_design_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $cmd1 . "','" . $cmd2 . "','" . $cmd3 . "','" . $cmd4 . "','" . $cmd5 . "','" . $cmd6 . "','" . $cmd7 . "','" . $cmd8 . "','" . $cmd9 . "','" . $cmd10 . "','" . $cmd11 . "','" . $cmd12 . "','" . $cmd13 . "','" . $cmd14 . "','" . $cmd15 . "','" . $cmd16 . "','" . $cmd17 . "','" . $cmd18 . "','" . $cmd19 . "','" . $cmd20 . "','" . $cmd21 . "','" . $cmd22 . "','" . $cmd23 . "','" . $cmd24 . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1)";
					$cam_design_dtls_id = $cam_design_dtls_id + 1;
				}
			}
		}

		// === came design end ===
		if( (str_replace("'", "", $prog_needle_layout_data_have)==1) &&  (str_replace("'", "", $prog_update_needle_layout_id)>0) )
		{
			//$needle_layout_id = return_next_id("id", "ppl_planning_needle_layout", 1);
			$update_field_array_needle_layout = "dial*cylinder*dial_row1*dial_row2*no_of_feeder*cylinder_row1*cylinder_row2*cylinder_row3*cylinder_row4*yarn_ends*lfa*yarn_tension*grey_gsm*t_dry_weight*t_dry_width*rpm*f_roll_width*laid_width*active_feeder*rev_per_kg*dial_height*updated_by*update_date";

			$update_data_array_needle_layout = $prog_hidden_dial . "*" . $prog_hidden_cylinder . "*" . $prog_hidden_dial_row1 . "*" . $prog_hidden_dial_row2 . "*" . $prog_hidden_no_of_feeder . "*" . $prog_hidden_cylinder_row1 . "*" . $prog_hidden_cylinder_row2 . "*" . $prog_hidden_cylinder_row3 . "*" . $prog_hidden_cylinder_row4 . "*" . $prog_hidden_yarn_ends . "*" . $prog_hidden_lfa . "*" . $prog_hidden_yarn_tension . "*" . $prog_hidden_grey_gsm . "*" . $prog_hidden_tdry_weight . "*" . $prog_hidden_tdry_width . "*" . $prog_hidden_rpm . "*" . $prog_hidden_froll_width . "*" . $prog_hidden_laid_width . "*" . $prog_hidden_active_feeder . "*" . $prog_hidden_rev_per_kg . "*" . $prog_hidden_dial_height . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		}else{

			if(str_replace("'", "", $prog_needle_layout_data_have)==1)
			{
				$needle_layout_id = return_next_id("id", "ppl_planning_needle_layout", 1);
				$field_array_needle_layout = "id,plan_id, program_no, dial, cylinder, dial_row1, dial_row2, no_of_feeder, cylinder_row1, cylinder_row2, cylinder_row3, cylinder_row4, yarn_ends, lfa, yarn_tension, grey_gsm, t_dry_weight, t_dry_width, rpm, f_roll_width, laid_width, active_feeder, rev_per_kg, dial_height, is_sales,inserted_by, insert_date";

				$data_array_needle_layout = "(".$needle_layout_id."," . $updateId . "," . $update_dtls_id . "," . $prog_hidden_dial . "," . $prog_hidden_cylinder . "," . $prog_hidden_dial_row1 . "," . $prog_hidden_dial_row2 . "," . $prog_hidden_no_of_feeder . "," . $prog_hidden_cylinder_row1 . "," . $prog_hidden_cylinder_row2 . "," . $prog_hidden_cylinder_row3 . "," . $prog_hidden_cylinder_row4 . "," . $prog_hidden_yarn_ends . "," . $prog_hidden_lfa . "," . $prog_hidden_yarn_tension . "," . $prog_hidden_grey_gsm . "," . $prog_hidden_tdry_weight . "," . $prog_hidden_tdry_width . "," . $prog_hidden_rpm . "," . $prog_hidden_froll_width . "," . $prog_hidden_laid_width . "," . $prog_hidden_active_feeder . "," . $prog_hidden_rev_per_kg . "," . $prog_hidden_dial_height . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
		}

		$feeder_dtls_id = return_next_id("id", "ppl_planning_feeder_dtls", 1);
		$field_array_feeder_dtls = "id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder,sequence, inserted_by, insert_date";

		$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
		if ($hidden_no_of_feeder_data != "") {
			$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
			for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++) {
				$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
				$pre_cost_id = $color_wise_data[0];
				$color_id = $color_wise_data[1];
				$stripe_color_id = $color_wise_data[2];
				$no_of_feeder = $color_wise_data[3];
				$txtFeederSequence = $color_wise_data[5];

				if ($data_array_feeder_dtls != "") $data_array_feeder_dtls .= ",";

				$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "','" . $txtFeederSequence . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$feeder_dtls_id = $feeder_dtls_id + 1;
			}
		}

		$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
		if ($hidden_collarCuff_data != "") {
			$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
			$field_array_collar_cuff_dtls = "id, mst_id, dtls_id, body_part_id, grey_size, finish_size, qty_pcs, needle_per_cm, inserted_by, insert_date";

			$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
			for ($i = 0; $i < count($hidden_collarCuff_data); $i++) {
				$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
				$body_part_id = $collarCuff_wise_data[0];
				$grey_size = $collarCuff_wise_data[1];
				$finish_size = $collarCuff_wise_data[2];
				$qty_pcs = $collarCuff_wise_data[3];
				$needle_per_cm = $collarCuff_wise_data[4];

				if ($data_array_collar_cuff_dtls != "") $data_array_collar_cuff_dtls .= ",";

				$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "','" . $needle_per_cm . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
			}
		}

		//for color wise program
		$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
		$hidden_size_wise_prog_string = str_replace("'", "", $hidden_size_wise_prog_string);
		if ($hidden_color_wise_prog_data != "")
		{
			$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
			$field_array_color_wise_break_down = "id, plan_id, program_no, color_id, color_prog_qty,size_wise_prog_string, inserted_by, insert_date";
			$field_array_color_wise_prog_update = "color_id*color_prog_qty*updated_by*update_date*status_active*is_deleted";

			$color_wise_prog_data = array();
			$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
			$size_wise_prog_data = explode("**", $hidden_size_wise_prog_string);
			$color_wise_mstID = array();
			for ($i = 0; $i < count($color_wise_prog_data); $i++)
			{
				if($color_wise_prog_data[$i] != '')
				{
					$colorWiseProgData = array();
					$color_id = '';
					$color_prog_qty = 0;
					$color_wise_break_down_udpdateId = 0;

					$sizeWiseDataString=$size_wise_prog_data[$i];
					$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
					$color_id = $colorWiseProgData[0];
					$color_prog_qty = $colorWiseProgData[1];
					$color_wise_break_down_udpdateId = $colorWiseProgData[2];

					if ($color_wise_break_down_udpdateId>0)
					{
						$status_active = 1;
						$is_deleted = 0;
						if($color_prog_qty <= 0)
						{
							$status_active = 0;
							$is_deleted = 1;
						}

						$colorprog_upd_id_arr[] = $color_wise_break_down_udpdateId;
						$data_array_color_wise_prog_update[$color_wise_break_down_udpdateId] = explode("*", ("'" . $color_id . "'*'". $color_prog_qty . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'". $status_active . "'*'". $is_deleted . "'"));
						$color_wise_mstID[$color_id]=$color_wise_break_down_udpdateId;

					}
					else
					{
						if ($data_array_color_wise_break_down != "")
							$data_array_color_wise_break_down .= ",";
						if($color_prog_qty>0)
						{
							$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "','" . $sizeWiseDataString . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$color_wise_mstID[$color_id]=$color_wise_break_down_id;
							$color_wise_break_down_id = $color_wise_break_down_id + 1;
						}
					}
				}
			}
		}
		// == Size prog data ==

		if( $hidden_color_wise_prog_data != "" && $hidden_size_wise_prog_string != "")
		{
			$size_wise_break_down_id = return_next_id("id", "ppl_size_wise_break_down ", 1);
			$field_array_size_wise_break_down = "id,color_wise_mst_id, plan_id, program_no, color_id,grey_size_id,finish_size_id,per_kg, kg_wise_total_qnty,body_part_id,current_qty, inserted_by, insert_date";
			$size_wise_prog_datas = explode("**", $hidden_size_wise_prog_string);
			for ($inc = 0; $inc < count($size_wise_prog_datas); $inc++)
			{
				$size_wise_prog_dataz = explode("##", $size_wise_prog_datas[$inc]);
				for ($i = 0; $i < count($size_wise_prog_dataz); $i++)
				{
					if($size_wise_prog_dataz[$i] != '')
					{

						$sizeWiseProgData = array();
						$color_id = '';
						$gmts_size_id = '';
						$finish_size_id = '';
						$per_kg = '';
						$size_wise_current_qty = '';
						$size_kg_wise_prog_qty = 0;

						$sizeWiseProgData = explode("_", $size_wise_prog_dataz[$i]);
						$color_id = $sizeWiseProgData[0];
						$gmts_size_id = $sizeWiseProgData[1];
						$finish_size_id = $sizeWiseProgData[2];
						$per_kg = $sizeWiseProgData[3];
						$size_kg_wise_prog_qty = $sizeWiseProgData[4];
						$size_wise_body_part_id = $sizeWiseProgData[6];
						$size_wise_current_qty = $sizeWiseProgData[7];
						$size_wise_primary_update_id = $sizeWiseProgData[8];


						if ($data_array_size_wise_break_down != "")
							$data_array_size_wise_break_down .= ",";

						if ($size_wise_primaryUpdateId != "")
							$size_wise_primaryUpdateId .= ",";

						if($size_kg_wise_prog_qty>0)
						{
							$colorWiseMstId=$color_wise_mstID[$color_id];
							$data_array_size_wise_break_down .= "(" . $size_wise_break_down_id . "," . $colorWiseMstId . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $gmts_size_id . "','" . $finish_size_id . "','" . $per_kg . "','" . $size_kg_wise_prog_qty . "','" . $size_wise_body_part_id . "','" . $size_wise_current_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$size_wise_break_down_id = $size_wise_break_down_id + 1;

							$size_wise_primaryUpdateId.=$size_wise_primary_update_id;

						}
					}
				}

			}
		}
		//Query Execution Start
		$delete = execute_query("delete from ppl_planning_entry_plan_dtls where dtls_id=$update_dtls_id", 0);
		if ($delete) $flag = 1; else $flag = 0;

		$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 1);
		if ($flag == 1) {
			if ($rID) $flag = 1; else $flag = 0;
		}

		if ($data != "") {
			if ($data_array_plan_dtls != "") {

				$rID2 = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
				if ($flag == 1) {
					if ($rID2) $flag = 1; else $flag = 0;
				}
			}
		}

		$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete_datewise) $flag = 1; else $flag = 0;
		}
		//echo "10**";
		$delete_machine = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id", 0);

		if ($flag == 1) {
			if ($delete_machine) $flag = 1; else $flag = 0;
		}

		if ($save_data != "") {
			if ($data_array_machine_dtls != "") {
				//echo"insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID3 = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($flag == 1) {
					if ($rID3) $flag = 1; else $flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "") {
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID4 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1) {
					if ($rID4) $flag = 1; else $flag = 0;
				}
			}
		}

		$delete_feeder = execute_query("delete from ppl_planning_feeder_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete_feeder) $flag = 1; else $flag = 0;
		}

		if ($hidden_no_of_feeder_data != "") {
			if ($data_array_feeder_dtls != "") {
				//echo "10**insert into ppl_planning_feeder_dtls (".$field_array_feeder_dtls.") Values ".$data_array_feeder_dtls."";die;
				$rID5 = sql_insert("ppl_planning_feeder_dtls", $field_array_feeder_dtls, $data_array_feeder_dtls, 0);
				if ($flag == 1) {
					if ($rID5) $flag = 1; else $flag = 0;
				}
			}
		}

		$delete_collar_cuff = execute_query("delete from ppl_planning_collar_cuff_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete_collar_cuff) $flag = 1; else $flag = 0;
		}

		if ($data_array_collar_cuff_dtls != "") {
			//echo "10**insert into ppl_planning_collar_cuff_dtls (".$field_array_collar_cuff_dtls.") Values ".$data_array_collar_cuff_dtls."";die;
			$rID6 = sql_insert("ppl_planning_collar_cuff_dtls", $field_array_collar_cuff_dtls, $data_array_collar_cuff_dtls, 0);

			if ($flag == 1) {
				if ($rID6) $flag = 1; else $flag = 0;
			}
		}

		//-------------------------------Count Feeding

		$hidden_count_feeding_data = str_replace("'", "", $hidden_count_feeding_data);

		if($hidden_count_feeding_data  != "")
		{
			$count_feeding_id = return_next_id("id", "ppl_planning_count_feed_dtls", 1);
			$field_array_count_feeding_dtls = "id, mst_id, dtls_id, seq_no, count_id,feeding_id,prod_id,prod_desc, inserted_by, insert_date";

			$hidden_count_feeding_data_arr = explode(",", $hidden_count_feeding_data);
			for ($i = 0; $i < count($hidden_count_feeding_data_arr); $i++) {
				$count_feeding_data_arr = explode("_", $hidden_count_feeding_data_arr[$i]);
				$seq_no = $count_feeding_data_arr[0];

				$count_id = $count_feeding_data_arr[1];
				$feeding_id = $count_feeding_data_arr[2];
				$yarn_prod_id = $count_feeding_data_arr[3];
				$yarn_prod_desc = $count_feeding_data_arr[4];
				if ($data_array_count_feeding_dtls != "") $data_array_count_feeding_dtls .= ",";
				$data_array_count_feeding_dtls .= "(" . $count_feeding_id . "," . $updateId . "," . $update_dtls_id . "," . $seq_no . "," . $count_id . "," . $feeding_id. ",'" . $yarn_prod_id. "','" . $yarn_prod_desc. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$count_feeding_id = $count_feeding_id + 1;
			}


			$delete_feeding = execute_query("delete from ppl_planning_count_feed_dtls where dtls_id=$update_dtls_id", 0);
			if($delete_feeding) $flag = 1; else $flag = 0;
			if($flag == 1 && $data_array_count_feeding_dtls)
			{
				//echo "10**".$field_array_count_feeding_dtls."=".$data_array_count_feeding_dtls;
				$rID7 = sql_insert("ppl_planning_count_feed_dtls", $field_array_count_feeding_dtls, $data_array_count_feeding_dtls, 0);
				if ($rID7) $flag = 1; else $flag = 0;
			}
		}

		// came design
		if ($came_udpdateId !="") {

			if (count($data_array_came_design_update) > 0) {
				$rID8 = execute_query(bulk_update_sql_statement("ppl_planning_cam_design_dtls", "id", $field_array_cam_design_dtls_update, $data_array_came_design_update, $cam_upd_id_arr));

				if ($flag == 1) {
					if ($rID8) $flag = 1; else $flag = 0;
				}
			}
		}
		else
		{
			if ($data_array_cam_design_dtls != "") {
					//echo "10**insert into ppl_planning_cam_design_dtls (".$field_array_cam_design_dtls.") Values ".$data_array_cam_design_dtls."";die;
				$rID8 = sql_insert("ppl_planning_cam_design_dtls", $field_array_cam_design_dtls, $data_array_cam_design_dtls, 0);
				if ($flag == 1) {
					if ($rID8) $flag = 1; else $flag = 0;
				}
			}
		}


		if( (str_replace("'", "", $prog_needle_layout_data_have)==1) &&  (str_replace("'", "", $prog_update_needle_layout_id)>0) )
		{

			if($update_data_array_needle_layout !="")
			{

				$update_needle_layout_id = str_replace("'", "", $prog_update_needle_layout_id);

				//echo "10**$update_needle_layout_id==$update_data_needle_layout"; die();
				$rID9 = sql_update("ppl_planning_needle_layout", $update_field_array_needle_layout, $update_data_array_needle_layout, "id", $update_needle_layout_id, 0);

				if ($flag == 1) {
					if ($rID9) $flag = 1; else $flag = 0;
				}

			}

		}else{

			if( str_replace("'", "", $prog_needle_layout_data_have)==1 ) {
				if ($data_array_needle_layout != "") {
					//echo "10**insert into ppl_planning_needle_layout (".$field_array_needle_layout.") Values ".$data_array_needle_layout."";die;
					$rID9  = sql_insert("ppl_planning_needle_layout", $field_array_needle_layout, $data_array_needle_layout, 0);
					if ($flag == 1) {
						if ($rID9) $flag = 1; else $flag = 0;
					}
				}
			}
		}
		//--------------------------------- end

		//for color wise program
		if ($hidden_color_wise_prog_data != "")
		{
			if (count($colorprog_upd_id_arr)>0)
			{
				// update
				$rID10 = true;
				if (count($data_array_color_wise_prog_update) > 0)
				{
					//echo "10**".bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr);
					$rID10 = execute_query(bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr));
				}

				if ($flag == 1)
				{
					if ($rID10) $flag = 1;
					else $flag = 0;
				}
			}

			$rID11 = true;
			if ($data_array_color_wise_break_down != "")
			{
				// new color insert
				//echo "10**insert into ppl_color_wise_break_down (".$field_array_color_wise_break_down.") Values ".$data_array_color_wise_break_down."";die;
				$rID11 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
			}

			if ($flag == 1)
			{
				if ($rID11) $flag = 1;
				else $flag = 0;
			}
		}

		//=======Size wise ==============
		if ($data_array_size_wise_break_down != "") {
			foreach ($color_wise_mstID as $colorIDS => $colorMstID) {
				$delete_size_tbl = execute_query("delete from ppl_size_wise_break_down where color_wise_mst_id=$colorMstID", 0);
			}
			if ($delete_size_tbl) $flag = 1; else $flag = 0;

			//echo "10**insert into ppl_size_wise_break_down (".$field_array_size_wise_break_down.") Values ".$data_array_size_wise_break_down."";die;
			$rID12 = sql_insert("ppl_size_wise_break_down", $field_array_size_wise_break_down, $data_array_size_wise_break_down, 0);
			if ($flag == 1) {
				if ($rID12) $flag = 1; else $flag = 0;
			}
		}


		/*oci_rollback($con);
		echo "10**$rID##$rID1##$rID2##$rID3##$rID4##$rID5##$rID6##$rID7##$rID8##$rID9##$rID10##$rID11##$rID12##$flag";
		disconnect($con);
		die;*/




		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $updateId) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $updateId) . "**0";
			} else {
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 2)
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$knit_qty = sql_select("select a.knit_id from ppl_yarn_requisition_entry a where a.knit_id=$update_dtls_id and a.is_deleted=0 and a.status_active=1");
		if ($knit_qty[0][csf('knit_id')] != "") {
			echo "14**Program already used in Requisition. So it can not be deleted";
			disconnect($con);
			exit();
		}

		$is_knitting_production = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($is_knitting_production != "") {
			echo "14**Program can not be deleted. Program Already used in Knitting Production. Production Quantity is = $is_knitting_production";
			disconnect($con);
			exit();
		}

		$field_array_update = "status_active*is_deleted*updated_by*update_date";
		$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		if ($rID) $flag = 1; else $flag = 0;

		$rID2 = sql_update("ppl_planning_entry_plan_dtls", $field_array_update, $data_array_update, "dtls_id", $update_dtls_id, 0);
		if ($flag == 1) {
			if ($rID2) $flag = 1; else $flag = 0;
		}

		$rID13 = sql_update("ppl_color_wise_break_down", $field_array_update, $data_array_update, "program_no", $update_dtls_id, 0);
		if ($flag == 1)
		{
			if ($rID13) $flag = 1; else $flag = 0;
		}

		$delete = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete) $flag = 1; else $flag = 0;
		}

		$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete_datewise) $flag = 1; else $flag = 0;
		}

		$cam_design_field_array_update = "status_active*is_deleted*updated_by*update_date";
		$cam_design_data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID3 = sql_update("ppl_planning_cam_design_dtls", $cam_design_field_array_update, $cam_design_data_array_update, "dtls_id", $update_dtls_id, 0);
		if ($rID3) $flag = 1; else $flag = 0;

		$needle_layout_field_array_update = "status_active*is_deleted*updated_by*update_date";
		$needle_layout_data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID3 = sql_update("ppl_planning_needle_layout", $needle_layout_field_array_update, $needle_layout_data_array_update, "program_no", $update_dtls_id, 0);
		if ($rID3) $flag = 1; else $flag = 0;

		/*$delete_feeder=execute_query( "delete from ppl_planning_feeder_dtls where dtls_id=$update_dtls_id",1);
		if($flag==1)
		{
			if($delete_feeder) $flag=1; else $flag=0;
		}*/

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $updateId) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "7**0**1";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $updateId) . "**0";
			} else {
				oci_rollback($con);
				echo "7**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "update_program")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();
	if ($db_type == 0)
	{
		mysql_query("BEGIN");
	}
	if($activeBtn==1)
	{
		$field_array_update = "active_for_production*updated_by*update_date";
		$data_array_update = 1 . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $program_id, 0);
		$rID2 = sql_update("ppl_planning_entry_plan_dtls", $field_array_update, $data_array_update, "dtls_id", $program_id, 0);
	}
	else
	{
		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$program_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($knit_qty > $prog_qty)
		{
			echo "20";
			disconnect($con);
			exit();
		}

		$field_array_update = "program_qnty*updated_by*update_date";
		$data_array_update = $prog_qty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $program_id, 0);
		$rID2 =1;
	}

	if ($db_type == 0)
	{
		if ($rID && $rID2)
		{
			mysql_query("COMMIT");
			echo "1";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "6";
		}
	}
	else if ($db_type == 2 || $db_type == 1)
	{
		if ($rID && $rID2)
		{
			oci_commit($con);
			echo "1";
		}
		else
		{
			oci_rollback($con);
			echo "6";
		}
	}
	disconnect($con);
	die;
}

if ($action == "delete_program")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$knit_qty = sql_select("select a.knit_id from ppl_yarn_requisition_entry a where a.knit_id in($program_ids) and a.is_deleted=0 and a.status_active=1");
	if ($knit_qty[0][csf('knit_id')] != "") {
		echo "14**Program already used in Requisition. So it can not be deleted";
		disconnect($con);
		exit();
	}

	$is_knitting_production = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($program_ids) and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

	if ($is_knitting_production != "") {
		echo "14**Program can not be deleted. Program Already used in Knitting Production. Production Quantity is = $is_knitting_production";
		disconnect($con);
		exit();
	}

	$rID = execute_query("update ppl_planning_info_entry_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in($program_ids)", 0);
	if ($rID) $flag = 1; else $flag = 0;

	$rID2 = execute_query("update ppl_planning_entry_plan_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where dtls_id in($program_ids)", 0);
	if ($flag == 1) {
		if ($rID2) $flag = 1; else $flag = 0;
	}

	$delete = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id in($program_ids)", 0);
	if ($flag == 1) {
		if ($delete) $flag = 1; else $flag = 0;
	}

	$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id in($program_ids)", 0);
	if ($flag == 1) {
		if ($delete_datewise) $flag = 1; else $flag = 0;
	}

	$delete_feeder = execute_query("delete from ppl_planning_feeder_dtls where dtls_id in($program_ids)", 1);
	if ($flag == 1) {
		if ($delete_feeder) $flag = 1; else $flag = 0;
	}

	if ($db_type == 0) {
		if ($flag == 1) {
			mysql_query("COMMIT");
			echo "2**0";
		} else {
			mysql_query("ROLLBACK");
			echo "7**0**1";
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($flag == 1) {
			oci_commit($con);
			echo "2**0";
		} else {
			oci_rollback($con);
			echo "7**0**1";
		}
	}
	disconnect($con);
	die;
}

if ($action == "prog_qnty_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
		<script>
			function fnc_image_upload()
			{
				var img_ref_id = $("#update_dtls_id").val();
				//alert(img_ref_id);return;
				if(img_ref_id == "")
				{
					alert('Please Select or Save any Information before File Upload.');return;
				}
				file_uploader ( '../../', img_ref_id,'', 'Planning Info Entry For Sales Order', 0,1);
			}
			function window_close(){
			parent.emailwindow.hide();
			}
		</script>
	<?
	$current_date = date("d-m-Y");
	$dataArray = sql_select("select id, machine_dia, machine_gg, upper(fabric_dia) as fabric_dia, stitch_length from fabric_mapping where mst_id=$determination_id and status_active=1 and is_deleted=0");

	//for body part type
	$expPreCostDtlsId = explode('_', $pre_cost);
	foreach($expPreCostDtlsId as $key=>$val)
	{
		$preCostFabricCostDtlsId[$val] = $val;
	}
	$sqlBodyPartType = "SELECT id, body_part_type FROM wo_pre_cost_fabric_cost_dtls WHERE status_active = 1 AND is_deleted = 0 ".where_con_using_array($preCostFabricCostDtlsId, '0', 'id');
	$RsltBodyPartType = sql_select($sqlBodyPartType);
	$bodyPartType = '';
	foreach($RsltBodyPartType as $row)
	{
		if($row[csf('body_part_type')] == 40 || $row[csf('body_part_type')] ==50)
		{
			$bodyPartType = $row[csf('body_part_type')];
		}
	}
	//echo "<pre>";
	//print_r($_SESSION['logic_erp']['mandatory_field'][282]);
	//echo "</pre>";
	?>
	<script>
		<?
		if(isset($_SESSION['logic_erp']['data_arr'][282]))
		{
			$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][282]);
			echo "var field_level_data= ". $data_arr . ";\n";
		}
		?>
		var permission = '<? echo $permission; ?>';
		var bodyPartType = '<?php echo $bodyPartType; ?>';

		function openpage_machine() {
			var save_string = $('#save_data').val();
			var txt_machine_dia = $('#txt_machine_dia').val();
			var cbo_knitting_source = $('#cbo_knitting_source').val();
			var update_dtls_id = $('#update_dtls_id').val();
			var txt_program_qnty = $('#txt_program_qnty').val();
			var txt_machine_gg = $('#txt_machine_gg').val();
			var cbo_knitting_party = $('#cbo_knitting_party').val();

			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=machine_info_popup&save_string=' + save_string + '&companyID=' + '<? echo $companyID; ?>' + '&txt_machine_dia=' + txt_machine_dia + '&update_dtls_id=' + update_dtls_id + '&txt_program_qnty='+txt_program_qnty + '&txt_machine_gg='+txt_machine_gg + '&cbo_knitting_party='+cbo_knitting_party+ '&cbo_knitting_source='+cbo_knitting_source;
			var title = 'Machine Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_machine_no = this.contentDoc.getElementById("hidden_machine_no").value;
				var hidden_machine_id = this.contentDoc.getElementById("hidden_machine_id").value;
				var save_string = this.contentDoc.getElementById("save_string").value;
				var hidden_machine_capacity = this.contentDoc.getElementById("hidden_machine_capacity").value;
				var hidden_distribute_qnty = this.contentDoc.getElementById("hidden_distribute_qnty").value;
				var hidden_min_date = this.contentDoc.getElementById("hidden_min_date").value;
				var hidden_max_date = this.contentDoc.getElementById("hidden_max_date").value;

				$('#txt_machine_no').val(hidden_machine_no);
				$('#machine_id').val(hidden_machine_id);
				$('#save_data').val(save_string);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#txt_distribution_qnty').val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				days_req();
			}
		}

		function days_req() {
			txt_start_date = $('#txt_start_date').val();
			txt_end_date = $('#txt_end_date').val();

			if (txt_start_date != "" && txt_end_date != "") {
				var days_req = date_diff('d', txt_start_date, txt_end_date);
				$('#txt_days_req').val(days_req + 1);
			}
			else {
				$('#txt_days_req').val('');
			}
		}

		function openpage_color()
		{
        	var prog_no = $('#update_dtls_id').val();
			var hidden_color_id = $('#hidden_color_id').val();
			var program_color_id = $('#txt_hdn_colors').val();
			var save_color_wise_prog_data = $('#hidden_color_wise_prog_data').val();
			var hdnVariableCollarCuff = $('#hdnVariableCollarCuff').val();

			var booking_no='<? echo $booking_no; ?>';
			var desc='<? echo $desc; ?>';
			booking_no=encodeURIComponent(String(booking_no));
			desc=encodeURIComponent(String(desc));
			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=color_info_popup&companyID=' + '<? echo $companyID; ?>' + '&job_id=' + '<? echo $job_id; ?>' + '&booking_no=' + booking_no + '&dia=' + '<? echo $dia; ?>' + '&hidden_color_id=' + hidden_color_id  +"&save_color_wise_prog_data="+save_color_wise_prog_data + '&program_color_id=' + program_color_id + '&gsm=' + '<?php echo $gsm; ?>' + '&body_part_id=' + '<? echo $body_part_id; ?>' + '&desc=' + desc + '&sales_order_dtls_id=' + '<?php echo $sales_order_dtls_id;?>' + '&width_dia_type=' + '<?php echo $fabric_type;?>'+"&plan_id="+"<?php echo $plan_id; ?>"+ '&prog_no=' + prog_no+"&hdnVariableCollarCuff="+hdnVariableCollarCuff+ '&pre_cost_id=' + '<? echo $pre_cost_id; ?>'+ '&pre_cost=' + '<? echo $pre_cost; ?>'+ '&bookingWithoutOrder=' + '<? echo $bookingWithoutOrder; ?>'+ '&determination_id=' + '<? echo $determination_id; ?>';

			var title = 'Color Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=740px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function ()
			{
        		var theform = this.contentDoc.forms[0];
        		var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;
        		var hidden_size_wise_prog_string = this.contentDoc.getElementById("hidden_size_wise_prog_string").value;
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;
        		var hidden_colorRange = this.contentDoc.getElementById("hidden_colorRange").value;

        		$('#txt_color').val(hidden_color_no);
        		$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#hidden_size_wise_prog_string').val(hidden_size_wise_prog_string);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);
        		$('#cbo_color_range').val(hidden_colorRange);
			}
		}

		function fnc_program_entry(operation)
		{
			if (form_validation('cbo_knitting_source*cbo_knitting_party', 'Knitting Source*Knitting Party') == false)
			{
				return;
			}

			var booking_qnty = $("#hdn_booking_qnty").val() * 1;
			$("#hdn_operation").val(operation);
			var knit_source = $("#cbo_knitting_source").val();

			/*
			| if cbo_knitting_source = Out-bound Subcontract then
			| location is not mandatory though
			| Mandatory Field setting for location is yes
			*/
			if(knit_source == 3)
			{
				<?
				$mandatory_field = array();
				foreach($_SESSION['logic_erp']['mandatory_field'][282] as $key=>$val)
				{
					if($val != 'cbo_location_name')
					{
						if($val != 'txt_machine_no')
						{
							$mandatory_field[] = $val;
						}
					}
				}
				?>
				if('<? echo implode('*',$mandatory_field); ?>')
				{
					if (form_validation('<? echo implode('*',$mandatory_field);?>','<? echo implode('*',$mandatory_field);?>')==false)
					{
						return;
					}
				}
			}
			else
			{
				// alert(22);
				if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][282]);?>')
				{
					if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][282]);?>', '<? echo implode('*',$_SESSION['logic_erp']['field_message'][282]);?>')==false)
					{
						return;
					}
				}
			}

			//for body part type
			if(bodyPartType == 40 || bodyPartType == 50)
			{
				if (form_validation('txt_no_of_ply*txt_program_qnty', 'No Of Ply*Program Qnty') == false)
				{
					return;
				}
			}
			else
			{
				if (form_validation('txt_machine_dia*txt_machine_gg*txt_fabric_dia*txt_program_qnty', 'Machine Dia*Machine GG*Finish Fabric Dia*Program Qnty') == false)
				{
					return;
				}
			}

			var hiddenProgramQnty = $("#hiddenProgramQnty").val()*1;
    		var balanceProgramQnty = $("#balanceProgramQnty").val()*1;
    		var program_qnty = $("#txt_program_qnty").val()*1;

    		if(operation == 0)
    		{
    			if(balanceProgramQnty  <  program_qnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty="+balanceProgramQnty+'='+program_qnty);
    				$("#txt_program_qnty").val(balanceProgramQnty);
    				return;
    			}
    		}
    		else if(operation == 1)
    		{
				if((program_qnty - hiddenProgramQnty).toFixed(2) > balanceProgramQnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty");
    				$("#txt_program_qnty").val(hiddenProgramQnty);
    				return;
    			}
    		}

			if(operation == 0 || operation == 1)
			{
        		var hidden_color_wise_total = $('#hidden_color_wise_total').val()*1;
        		var txt_program_qnty = $('#txt_program_qnty').val()*1;

        		if( hidden_color_wise_total != txt_program_qnty  )
        		{
        			alert('Mismatch Program quantity and Color Wise program quantity');
        			$('#txt_program_qnty').focus();
        			return;
        		}
        	}

			data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*cbo_location_name*hidden_advice_data*hidden_no_of_feeder_data*hidden_collarCuff_data*hidden_count_feeding_data*hidden_came_dsign_string_data*hdn_fab_desc*prog_hidden_dial*prog_hidden_cylinder*prog_hidden_dial_row1*prog_hidden_dial_row2*prog_hidden_no_of_feeder*prog_hidden_yarn_tension*prog_hidden_cylinder_row1*prog_hidden_cylinder_row2*prog_hidden_cylinder_row3*prog_hidden_cylinder_row4*prog_hidden_yarn_ends*prog_hidden_lfa*prog_hidden_grey_gsm*prog_hidden_tdry_weight*prog_hidden_tdry_width*prog_hidden_rpm*prog_hidden_froll_width*prog_hidden_laid_width*prog_hidden_active_feeder*prog_hidden_rev_per_kg*prog_hidden_dial_height*prog_needle_layout_data_have*prog_update_needle_layout_id*txt_batch_no*txt_no_of_ply*txt_tube_ref_no*hidden_color_wise_prog_data*hiddenProgramQnty*balanceProgramQnty*hidden_size_wise_prog_string', "../../") + '&companyID='+<? echo $companyID; ?>+
			'&gsm=' + '<? echo $gsm; ?>' + '&dia=' + '<? echo trim($dia); ?>' + '&determination_id='+<? echo $determination_id; ?>+
			'&booking_no=' + encodeURIComponent(String('<? echo $booking_no; ?>')) + '&data='+encodeURIComponent(String(<? echo $data; ?>))+
			'&body_part_id='+ encodeURIComponent(String('<? echo $body_part_id; ?>'))+
			'&color_type_id='+<? echo $color_type_id; ?>+
			'&fabric_typee='+<? echo $fabric_type; ?>+
			'&tot_booking_qnty='+<? echo $booking_qnty; ?>+
			'&buyer_id='+<? echo $buyer_id; ?>+
			'&within_group='+<? echo $within_group; ?>+
			'&sales_order_dtls_id=<? echo $sales_order_dtls_id; ?>' + '&pre_cost_id=<? echo $pre_cost_id; ?>' + '&pre_cost=<? echo $pre_cost; ?>' + '&hdn_booking_qnty=' + booking_qnty;

			freeze_window(operation);
			//alert($data);return;
			http.open("POST", "planning_info_entry_for_sales_order_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_program_entry_Reply_info;
		}

		function fnc_program_entry_Reply_info()
		{
			if (http.readyState == 4)
			{
				var reponse = trim(http.responseText).split('**');
				show_msg(reponse[0]);

				if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2))
				{
					var progBalance = 0;
                	var PreProgramQnty = $("#hiddenProgramQnty").val()*1;
                	if(reponse[0] == 0 )
					{
                		progBalance = $("#balanceProgramQnty").val()*1 - $("#txt_program_qnty").val()*1;
                	}
                	else if(reponse[0] == 1 )
                	{
                		progBalance = $("#balanceProgramQnty").val()*1 + PreProgramQnty - $("#txt_program_qnty").val()*1 ;
                	}


					if($("#hdn_operation").val() == 0)
					{
						$('#txt_batch_no').val('');
						$('#txt_tube_ref_no').val('');
						$('#txt_program_date').val('<? echo $current_date;?>');
					}
					else
					{
						reset_form('programQnty_1', '', '', 'txt_program_date,<? echo $current_date;?>', '', 'hdn_booking_qnty*cbo_dia_width_type*hdnVariableCollarCuff*hidden_sales_dia');
					}

					$('#updateId').val(reponse[1]);
					show_list_view(reponse[1], 'planning_info_details', 'list_view', 'planning_info_entry_for_sales_order_controller', '');
					set_button_status(0, permission, 'fnc_program_entry', 1);

                    $("#txt_program_qnty").val(progBalance.toFixed(2));
                    $("#balanceProgramQnty").val(progBalance.toFixed(2));
				}
				if (reponse[0] == 14)
				{
					alert(reponse[1]);
				}
				release_freezing();
			}
		}

		function balance_cal()
		{
			$("#hiddenProgramQnty").val($("#txt_program_qnty").val()*1);
		}

		function active_inactive() {
			var knitting_source = document.getElementById('cbo_knitting_source').value;

			reset_form('', '', 'txt_machine_no*machine_id*txt_machine_capacity*txt_distribution_qnty*txt_days_req*cbo_location_name', 'txt_program_date,<? echo $current_date; ?>', '', '');
			if (knitting_source == 1) {
				//document.getElementById('txt_machine_no').disabled = false;
				document.getElementById('cbo_location_name').disabled = false;
			}
			else {
				//document.getElementById('txt_machine_no').disabled = true;
				document.getElementById('cbo_location_name').disabled = true;
			}
		}

		function openpage_feeder() {
			var no_of_feeder_data = $('#hidden_no_of_feeder_data').val();
			var hidden_color_id = $('#hidden_color_id').val();
			var hidden_sales_dia = $('#hidden_sales_dia').val();
			var color_type_id =<? echo $color_type_id; ?>;

			if (!(color_type_id == 2 || color_type_id == 3 || color_type_id == 4)) {
				alert("Only for Stripe");
				return;
			}

			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=feeder_info_popup&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>'+'&hidden_color_id='+hidden_color_id+ '&sales_order_dtls_id=' + '<? echo $sales_order_dtls_id; ?>'+ '&within_group=' + '<? echo $within_group; ?>'+ '&job_id=' + '<? echo $job_id; ?>'+'&hidden_sales_dia='+hidden_sales_dia;
			var title = 'Stripe Measurement Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_no_of_feeder_data = this.contentDoc.getElementById("hidden_no_of_feeder_data").value;

				$('#hidden_no_of_feeder_data').val(hidden_no_of_feeder_data);
			}
		}

		function openpage_collarCuff() {
			var collarCuff_data = $('#hidden_collarCuff_data').val();
			var update_dtls_id = $('#update_dtls_id').val();
			/*if (update_dtls_id == "") {
				alert("Save Data First");
				return;
			}*/
			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&body_part_id='+'<? echo $body_part_id; ?>&booking_no=' + '<? echo $booking_no; ?>&bodyPartType=' + bodyPartType;
			var title = 'Collar & Cuff Measurement Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

				$('#hidden_collarCuff_data').val(hidden_collarCuff_data);
			}
		}

		function openpage_cam_design() {
			var updateDtlsId = $('#update_dtls_id').val();
			var mstId = $("#updateId").val();
			var came_dsign_string_data = $('#hidden_came_dsign_string_data').val();

			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=cam_design_info_popup&hidden_came_dsign_string_data=' + came_dsign_string_data + '&update_dtls_id='+updateDtlsId;
			var title = 'Cam Design Information';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=250px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var came_dsign_string_data = this.contentDoc.getElementById("hidden_came_dsign_string_data").value;
				$('#hidden_came_dsign_string_data').val(came_dsign_string_data);
			}
		}

		function openpage_needle_layout()
		{
			var prog_no = $('#update_dtls_id').val();
			var plan_id = $("#updateId").val();
			var update_needle_layout_id = $("#prog_update_needle_layout_id").val();
			var hidden_dial = $('#prog_hidden_dial').val();
			var hidden_cylinder = $('#prog_hidden_cylinder').val();
			var hidden_dial_row1 = $('#prog_hidden_dial_row1').val();
			var hidden_dial_row2 = $('#prog_hidden_dial_row2').val();
			var hidden_no_of_feeder = $('#prog_hidden_no_of_feeder').val();
			var hidden_cylinder_row1 = $('#prog_hidden_cylinder_row1').val();
			var hidden_cylinder_row2 = $('#prog_hidden_cylinder_row2').val();
			var hidden_cylinder_row3 = $('#prog_hidden_cylinder_row3').val();
			var hidden_cylinder_row4 = $('#prog_hidden_cylinder_row4').val();
			var hidden_yarn_ends = $('#prog_hidden_yarn_ends').val();
			var hidden_lfa = $('#prog_hidden_lfa').val();
			var hidden_yarn_tension = $('#prog_hidden_yarn_tension').val();
			var hidden_grey_gsm = $('#prog_hidden_grey_gsm').val();
			var hidden_tdry_weight = $('#prog_hidden_tdry_weight').val();
			var hidden_tdry_width = $('#prog_hidden_tdry_width').val();
			var hidden_rpm = $('#prog_hidden_rpm').val();
			var hidden_froll_width = $('#prog_hidden_froll_width').val();
			var hidden_laid_width = $('#prog_hidden_laid_width').val();

			var hidden_active_feeder = $('#prog_hidden_active_feeder').val();
			var hidden_rev_per_kg = $('#prog_hidden_rev_per_kg').val();
			var hidden_dial_height = $('#prog_hidden_dial_height').val();

			//var prog_needle_layout_data_have = $('#prog_needle_layout_data_have').val();

			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=needle_layout_info_popup&prog_no='+prog_no + '&plan_id=' + plan_id+ '&update_needle_layout_id=' + update_needle_layout_id+ '&hidden_dial=' + hidden_dial+ '&hidden_cylinder=' + hidden_cylinder+ '&hidden_dial_row1=' + hidden_dial_row1+ '&hidden_dial_row2=' + hidden_dial_row2+ '&hidden_no_of_feeder=' + hidden_no_of_feeder+ '&hidden_cylinder_row1=' + hidden_cylinder_row1+ '&hidden_cylinder_row2=' + hidden_cylinder_row2+ '&hidden_cylinder_row3=' + hidden_cylinder_row3+ '&hidden_cylinder_row4=' + hidden_cylinder_row4+ '&hidden_yarn_ends=' + hidden_yarn_ends+ '&hidden_lfa=' + hidden_lfa+ '&hidden_yarn_tension=' + hidden_yarn_tension+ '&hidden_grey_gsm=' + hidden_grey_gsm+ '&hidden_tdry_weight=' + hidden_tdry_weight+ '&hidden_tdry_width=' + hidden_tdry_width+ '&hidden_rpm=' + hidden_rpm+ '&hidden_froll_width=' + hidden_froll_width+ '&hidden_laid_width=' + hidden_laid_width+ '&hidden_active_feeder=' + hidden_active_feeder+ '&hidden_rev_per_kg=' + hidden_rev_per_kg+ '&hidden_dial_height=' + hidden_dial_height;

			var title = 'Needle Layout Information';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];

				$("#prog_hidden_dial").val(this.contentDoc.getElementById("hidden_dial").value);
				$('#prog_hidden_cylinder').val(this.contentDoc.getElementById("hidden_cylinder").value);
				$('#prog_hidden_dial_row1').val(this.contentDoc.getElementById("hidden_dial_row1").value);
				$('#prog_hidden_dial_row2').val(this.contentDoc.getElementById("hidden_dial_row2").value);
				$('#prog_hidden_no_of_feeder').val(this.contentDoc.getElementById("hidden_no_of_feeder").value);
				$('#prog_hidden_cylinder_row1').val(this.contentDoc.getElementById("hidden_cylinder_row1").value);
				$('#prog_hidden_cylinder_row2').val(this.contentDoc.getElementById("hidden_cylinder_row2").value);
				$('#prog_hidden_cylinder_row3').val(this.contentDoc.getElementById("hidden_cylinder_row3").value);
				$('#prog_hidden_cylinder_row4').val(this.contentDoc.getElementById("hidden_cylinder_row4").value);
				$('#prog_hidden_yarn_ends').val(this.contentDoc.getElementById("hidden_yarn_ends").value);
				$('#prog_hidden_lfa').val(this.contentDoc.getElementById("hidden_lfa").value);
				$('#prog_hidden_yarn_tension').val(this.contentDoc.getElementById("hidden_yarn_tension").value);
				$('#prog_hidden_grey_gsm').val(this.contentDoc.getElementById("hidden_grey_gsm").value);
				$('#prog_hidden_tdry_weight').val(this.contentDoc.getElementById("hidden_tdry_weight").value);
				$('#prog_hidden_tdry_width').val(this.contentDoc.getElementById("hidden_tdry_width").value);
				$('#prog_hidden_rpm').val(this.contentDoc.getElementById("hidden_rpm").value);
				$('#prog_hidden_froll_width').val(this.contentDoc.getElementById("hidden_froll_width").value);
				$('#prog_hidden_laid_width').val(this.contentDoc.getElementById("hidden_laid_width").value);

				$('#prog_hidden_active_feeder').val(this.contentDoc.getElementById("hidden_active_feeder").value);
				$('#prog_hidden_rev_per_kg').val(this.contentDoc.getElementById("hidden_rev_per_kg").value);
				$('#prog_hidden_dial_height').val(this.contentDoc.getElementById("hidden_dial_height").value);

				$('#prog_needle_layout_data_have').val(this.contentDoc.getElementById("needle_layout_data_have").value);

				//$('#prog_no').val(this.contentDoc.getElementById("update_dtls_id").value);
				//$("#plan_id").val(this.contentDoc.getElementById("updateId").value);
			}

		}

		function openpage_advice() {
			var hidden_advice_data = $('#hidden_advice_data').val();

			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=advice_info_popup&hidden_advice_data=' + hidden_advice_data;
			var title = 'Advice Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var advice_data = this.contentDoc.getElementById("txt_advice").value;
				$('#hidden_advice_data').val(advice_data);
			}
		}

		function openpage_count_feeding()
		{
			var count_feeding_data = $('#hidden_count_feeding_data').val();
			var update_dtls_id = $('#update_dtls_id').val();
			if (update_dtls_id == "") {
				alert("Save Data First");
				return;
			}
			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=count_feeding_data_popup&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id + '&job_id=' + '<? echo $job_id; ?>';
			var title = 'Count Feeding';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_count_feeding_data = this.contentDoc.getElementById("hidden_count_feeding_data").value;
				$('#hidden_count_feeding_data').val(hidden_count_feeding_data);
			}
		}
	</script>

</head>
<body>
	<div align="center">
		<? echo load_freeze_divs("../../../", $permission, 1); ?>
		<form name="programQnty_1" id="programQnty_1">
			<fieldset style="width:900px;">
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="890"
				align="center">
				<thead>
					<th width="300">Fabric Description</th>
					<th width="80">GSM</th>
					<th width="80">Dia</th>
					<th>Booking Qnty</th>
				</thead>
				<tr bgcolor="#FFFFFF">
					<td>
						<p><? echo $desc; ?></p>
						<input type="hidden" name="hdn_fab_desc" id="hdn_fab_desc" value="<? echo trim($desc); ?>" readonly/>
					</td>
					<td><? echo $gsm; ?></td>
					<td><? echo $dia; ?></td>
					<td align="right"><? echo number_format($booking_qnty, 2); ?></td>
				</tr>
			</table>
		</fieldset>
		<fieldset style="width:900px; margin-top:5px;">
			<legend>New Entry</legend>
			<input type="hidden" id="hdn_booking_qnty" name="hdn_booking_qnty" value="<? echo $booking_qnty; ?>"/>
			<input type="hidden" name="hdnVariableCollarCuff" id="hdnVariableCollarCuff" value="<? echo $hdnVariableCollarCuff; ?>">
			<table width="900" align="center" border="0">
				<tr>
					<td class="must_entry_caption">Knitting Source</td>
					<td>
						<?
						echo create_drop_down("cbo_knitting_source", 152, $knitting_source, "", 1, "-- Select --", 1, "active_inactive();load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value+'**'+$companyID, 'load_drop_down_knitting_party','knitting_party');", 0, '1,3');
						?>
					</td>
					<td class="must_entry_caption">Knitting Party</td>
					<td id="knitting_party">
						<?
						echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 1, "");
						//load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value, 'load_drop_down_location','location_td');
						?>
					</td>
					<td>Color</td>
					<td>
						<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;" placeholder="Browse" onClick="openpage_color();" readonly/>
						<input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
						<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" readonly>
						<input type="hidden" name="hidden_size_wise_prog_string" id="hidden_size_wise_prog_string"
							readonly>
						<input type="hidden" name="hidden_color_wise_total" id="hidden_color_wise_total" readonly>
					</td>
				</tr>
				<tr>
					<td>Color Range</td>
					<td>
						<?
						echo create_drop_down("cbo_color_range", 152, $color_range, "", 1, "-- Select --", 0, "");
						?>
					</td>
					<td class="must_entry_caption" id="td_machine_dia">Machine Dia</td>
					<td>
						<input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric"
						style="width:60px;" maxlength="3" title="Maximum 3 Character" value=""/>
						<?
						echo create_drop_down("cbo_dia_width_type", 100, $fabric_typee, "", 1, "-- Select --", $fabric_type, "");
						?>
					</td>
					<td class="must_entry_caption" id="td_machine_gg">Machine GG</td>
					<td>
						<input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes"
						style="width:140px;"/>
					</td>
				</tr>
				<tr>
					<td class="must_entry_caption" id="td_fabric_dia">Finish Fabric Dia</td>
					<td>
						<input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes"
						style="width:140px;" value="<? echo  $dia; ?>"/>
						<input type="hidden" name="hidden_sales_dia" id="hidden_sales_dia"
						class="text_boxes" value="<? echo  $dia; ?>">
					</td>
					<td class="must_entry_caption">Program Qnty</td>
					<td>
						<input type="hidden" value="" id="hiddenProgramQnty">
						<input type="hidden" value="<? echo number_format($balance_qnty, 2, '.', '');?>" id="balanceProgramQnty">
                        <input type="text" name="txt_program_qnty" id="txt_program_qnty" class="text_boxes_numeric"
						style="width:165px;"/>
					</td>
					<td>Program / Entry date</td>
					<td>
						<input type="text" name="txt_program_date" id="txt_program_date" class="datepicker"
						style="width:140px" value="<? echo $current_date; ?>" readonly>
					</td>
				</tr>
				<tr>
					<td>Stitch Length</td>
					<td>
						<input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes"
						style="width:140px;"/>
					</td>
					<td>Spandex Stitch Length</td>
					<td>
						<input type="text" name="txt_spandex_stitch_length" id="txt_spandex_stitch_length"
						class="text_boxes" style="width:165px;"/>
					</td>
					<td>Draft Ratio</td>
					<td>
						<input type="text" name="txt_draft_ratio" id="txt_draft_ratio" class="text_boxes_numeric"
						style="width:140px;"/>
					</td>
				</tr>
				<tr>
					<td>Machine No</td>
					<td>
						<input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes"
						placeholder="Double Click For Search" style="width:140px;"
						onDblClick="openpage_machine();" readonly/>
						<input type="hidden" name="machine_id" id="machine_id" class="text_boxes" readonly/>
					</td>
					<td>Machine Capacity</td>
					<td>
						<input type="text" name="txt_machine_capacity" id="txt_machine_capacity"
						placeholder="Display" class="text_boxes_numeric" style="width:165px;"
						disabled="disabled"/>
					</td>
					<td>Distribution Qnty</td>
					<td>
						<input type="text" name="txt_distribution_qnty" id="txt_distribution_qnty"
						placeholder="Display" class="text_boxes_numeric" style="width:65px;"
						disabled="disabled"/>
						<input type="text" name="txt_days_req" id="txt_days_req" placeholder="Days Req."
						class="text_boxes_numeric" style="width:60px;" disabled="disabled"/>
					</td>
				</tr>
				<tr>
					<td>Start Date</td>
					<td>
						<input type="text" name="txt_start_date" id="txt_start_date" class="datepicker"
						style="width:140px" value="<? echo $start_date; ?>" readonly>
					</td>
					<td>End Date</td>
					<td>
						<input type="text" name="txt_end_date" id="txt_end_date" class="datepicker"
						style="width:165px" value="<? echo $end_date; ?>" readonly>
					</td>
					<td>Status</td>
					<td>
						<?
						echo create_drop_down("cbo_knitting_status", 152, $knitting_program_status, "", 1, "--Select Status--", 0, "");
						?>
					</td>
				</tr>
				<tr>
					<td>Feeder</td>
					<td>
						<?
						$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");
						echo create_drop_down("cbo_feeder", 152, $feeder, "", 1, "--Select Feeder--", 0, "");
						?>
					</td>
					<td>
						<input type="button" name="feeder" class="formbuttonplasminus" value="No Of Feeder"
						onClick="openpage_feeder();" style="width:100px;"/>
						<input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data"
						class="text_boxes">
						<span style="font-weight: bold;margin-left: 10px;">Program No</span>
					</td>
					<td><input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes"
						placeholder="Display" disabled style="width:165px"></td>
						<td>Remarks</td>
						<td>
							<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"
							style="width:140px">
						</td>
					</tr>
                <tr>
                    <td>Location</td>
                    <td id="location_td">
                        <?

						echo create_drop_down("cbo_location_name", 152, $blank_array, "", 1, "-- Select Location --", 1, "");

                        ?>
                    </td>
                    <td>
                        <input type="button" name="feeder" class="formbuttonplasminus" value="Advice"
                        onClick="openpage_advice();" style="width:100px"/>
                        <input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes">

                        <input type="button" name="feeder" class="formbuttonplasminus" value="Collar & Cuff"
                        onClick="openpage_collarCuff();" style="width:100px"/>
                        <input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data"
                        class="text_boxes">

                    </td>
                    <td>
                        <input type="button" name="feeder" class="formbuttonplasminus" value="Count Feeding"
                        onClick="openpage_count_feeding();" style="width:100px"/>
                        <input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data"
                        class="text_boxes">
						<input type="button" class="image_uploader" id="uploader" style="width:60px" value="Add Image" onClick="fnc_image_upload();">
                    </td>

                    <td>
                        <input type="button" name="feeder" class="formbuttonplasminus" value="Cam Design"
                        onClick="openpage_cam_design();" style="width:100px"/>
                        <input type="hidden" name="hidden_came_dsign_string_data" id="hidden_came_dsign_string_data" value=""
                        class="text_boxes">
                    </td>
                    <td>
                        <input type="button" name="feeder" class="formbuttonplasminus" value="Needle Layout"
                        onClick="openpage_needle_layout();" style="width:100px"/>

                        <input type="hidden" id="prog_hidden_dial" value="">
                        <input type="hidden" id="prog_hidden_cylinder" value="">
                        <input type="hidden" id="prog_hidden_dial_row1" value="">
                        <input type="hidden" id="prog_hidden_dial_row2" value="">
                        <input type="hidden" id="prog_hidden_no_of_feeder" value="">
                        <input type="hidden" id="prog_hidden_cylinder_row1" value="">
                        <input type="hidden" id="prog_hidden_cylinder_row2" value="">
                        <input type="hidden" id="prog_hidden_cylinder_row3" value="">
                        <input type="hidden" id="prog_hidden_cylinder_row4" value="">
                        <input type="hidden" id="prog_hidden_yarn_ends" value="">
                        <input type="hidden" id="prog_hidden_lfa" value="">
                        <input type="hidden" id="prog_hidden_yarn_tension" value="">
                        <input type="hidden" id="prog_hidden_grey_gsm" value="">
                        <input type="hidden" id="prog_hidden_tdry_weight" value="">
                        <input type="hidden" id="prog_hidden_tdry_width" value="">
                        <input type="hidden" id="prog_hidden_rpm" value="">
                        <input type="hidden" id="prog_hidden_froll_width" value="">
                        <input type="hidden" id="prog_hidden_laid_width" value="">
                        <input type="hidden" id="prog_hidden_active_feeder" value="">
                        <input type="hidden" id="prog_hidden_rev_per_kg" value="">
                        <input type="hidden" id="prog_hidden_dial_height" value="">
                        <input type="hidden" id="prog_update_needle_layout_id" value="">
                        <input type="hidden" id="prog_needle_layout_data_have" value="">
                    </td>
                </tr>
                <tr>
                    <td>Batch No</td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px">
                    </td>
                    <td>Tube/Ref. No <input type="text" name="txt_tube_ref_no" id="txt_tube_ref_no" class="text_boxes" style="width:130px">
                    </td>
                </tr>
                <tr>
                    <td id="td_no_of_ply">No Of Ply</td>
                    <td>
                        <input type="text" name="txt_no_of_ply" id="txt_no_of_ply" class="text_boxes_numeric" style="width:140px">
                    </td>
                    <td>Color Type  <input type="text" value="<? echo $color_type[$color_type_id]; ?>" name="txt_color_type" id="txt_color_type" class="text_boxes" style="width:130px" disabled>
                </tr>
                <tr>
                    <td colspan="4" align="right" class="button_container">
                        <?
                        echo load_submit_buttons($permission, "fnc_program_entry", 0, 0, "reset_form('programQnty_1','','','txt_start_date,$start_date*txt_end_date,$end_date*txt_program_date,$current_date','','updateId*txt_color')", 1);
                        ?>
                    </td>
                    <td colspan="2" align="left" valign="top" class="button_container">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close"
                        onClick="parent.emailwindow.hide();" style="width:100px;"/>
                        <input type="hidden" name="save_data" id="save_data" class="text_boxes">
                        <input type="hidden" name="updateId" id="updateId" class="text_boxes"
                        value="<? echo trim(str_replace("'", '', $plan_id)); ?>">
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
                        <input type="hidden" name="hdn_operation" id="hdn_operation" class="text_boxes">
                    </td>
                </tr>
				</table>
			</fieldset>
			<div id="list_view" style="margin-top:5px">
				<?
				if (str_replace("'", '', $plan_id) != "") {
					?>
					<script>
						show_list_view('<? echo str_replace("'", '', $plan_id); ?>', 'planning_info_details', 'list_view', 'planning_info_entry_for_sales_order_controller', '');

					</script>
					<?
				}
				?>
			</div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	//for body part type
	if(bodyPartType == 40 || bodyPartType == 50)
	{
		$('#td_machine_dia').removeAttr('class').removeAttr('title').html('').text('Machine Dia');
		$('#td_machine_gg').removeAttr('class').removeAttr('title').html('').text('Machine GG');
		$('#td_fabric_dia').removeAttr('class').removeAttr('title').html('').text('Finish Fabric Dia');
		$('#td_no_of_ply').attr('class','must_entry_caption').attr('title', 'must_entry_caption').css('color','blue');
	}

	load_drop_down( 'planning_info_entry_for_sales_order_controller', 1+'**'+ <?php echo $companyID; ?>,'load_drop_down_knitting_party','knitting_party');
	load_drop_down( 'planning_info_entry_for_sales_order_controller',  $('#cbo_knitting_party').val(),'load_drop_down_location','location_td');

	setFieldLevelAccess('<?php echo $companyID;?>');

</script>
</html>
<?
exit();
}

if ($action == "color_info_popup")
{
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$color_mixing_in_knittingplan = return_field_value("color_mixing_in_knitting_plan", "variable_settings_production", "company_name=$companyID and variable_list=53");
	if($color_mixing_in_knittingplan==1)
	{
		$is_color_mixing = 1;
	}
	else
	{
		$is_color_mixing = 0;
	}
	?>
	<script>
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
			set_all();
		});

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				js_set_value(i);
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function set_all()
		{
			var old = document.getElementById('txt_color_row_id').value;
			if (old != "")
			{
				old = old.split(",");
				for (var i = 0; i < old.length; i++)
				{
					js_set_value(old[i])
				}
			}
		}
		//js_set_value
		function js_set_value(str)
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {

				selected_id.push($('#txt_individual_id' + str).val());
				selected_name.push($('#txt_individual' + str).val());
			}
			else {
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

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}

		//fnc_close
		function fnc_close(colorMixing)
		{
			var save_string = "";
			var save_string_size_wise = "";
			var breakOut = true;
			var total_prog_qty = 0;
			var color_name_string = '';
			var color_id_string = '';
			var color_prog_qty_string = '';
			var allowed_qty = 0;
			var colorQtyArr = [];
			var hdnColorRangeIds = 0;

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var coloProgUpdateId = $(this).find('input[name="colo_prog_update_id[]"]').val();
				var txtColorId = $(this).find('input[name="text_colorid_[]"]').val();
				var hdnColorRangeId = $(this).find('input[name="text_color_range_[]"]').val()* 1;
				var txtColorName = $(this).find('input[name="text_color_name_[]"]').val().trim();
				var txtColorProgQty = $(this).find('input[name="text_color_prog_qty[]"]').val() * 1;
				var hidden_color_allowed_qty = $(this).find('input[name="hidden_color_allowed_qty[]"]').val() * 1;
				var hidden_color_prev_prog_qty = $(this).find('input[name="hidden_color_prev_prog_qty[]"]').val() * 1;
				var txt_individual_color_blqty = $(this).find('input[name="txt_individual_color_blqty[]"]').val() * 1;
				var updateQty = $(this).find('input[name="text_color_prog_qty[]"]').attr('data-update-qty')*1;

				var hdn_size_wise_save_string = $(this).find('input[name="hdn_size_wise_save_string[]"]').val();

				//if(txtColorProgQty>0 || coloProgUpdateId !=0)
				if(txtColorProgQty>0 || (coloProgUpdateId !=0 && updateQty > 0))
				{
					if (save_string == "")
					{
						save_string = txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string = txtColorName;
						color_id_string = txtColorId;
						//color_prog_qty_string = txtColorProgQty;
						save_string_size_wise = hdn_size_wise_save_string;

					}
					else
					{
						save_string += "," + txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string += "," + txtColorName;
						color_id_string += "," + txtColorId;
						//color_prog_qty_string += "," + txtColorProgQty;
						save_string_size_wise += "**" + hdn_size_wise_save_string;
					}

					if(txtColorProgQty>0)
					{
						colorQtyArr.push(txtColorProgQty);
					}

					total_prog_qty += txtColorProgQty;

					if(hidden_color_allowed_qty<(hidden_color_prev_prog_qty+txtColorProgQty))
					{
						alert("Program quantity can not be greater than Balance quantity");
						$(this).find('input[name="text_color_prog_qty[]"]').focus();
						return;
					}

					hdnColorRangeIds=hdnColorRangeId;
				}
			});

			//if (total_prog_qty < 1)
			if (total_prog_qty < 0 || total_prog_qty ==0 ||  total_prog_qty =="")  // issue id: 28959
			{
				alert("Program quantity zero is not allowed");
				$('#text_color_prog_qty_1').focus();
				return false;
			}

			if(colorMixing!=1)
			{
				if(colorQtyArr.length>1)
				{
					alert('Color Mixing is not allowed');
					return;
				}
			}

			/*if(colorQtyArr.length==1)
			{
				hdnColorRangeId=hdnColorRangeId;
			}
			else
			{
				hdnColorRangeId=0;
			}*/

			//alert(save_string+'='+total_prog_qty+'='+color_id_string+'='+color_name_string);

			$('#hidden_color_wise_prog_data').val(save_string);
			$('#hidden_size_wise_prog_string').val(save_string_size_wise);
			$('#hidden_total_prog_qty').val(total_prog_qty.toFixed(2));
			$('#txt_selected_id').val(color_id_string);
			$('#txt_selected').val(color_name_string);
			$('#hidden_colorRange').val(hdnColorRangeIds);
			parent.emailwindow.hide();
		}

		//func_onkeyup_color_qty
		function func_onkeyup_color_qty(rowId)
		{
			var bookingQty = $('#hidden_color_allowed_qty'+rowId).val()*1;
			var previousQty = $('#hidden_color_prev_prog_qty_'+rowId).val()*1;
			var qty = $('#text_color_prog_qty_'+rowId).val()*1;
			var updateQty = $('#text_color_prog_qty_'+rowId).attr('data-update-qty');
			var totColorProgQty = 0;

			if(qty < 0)
			{
				alert("Program quantity can't be less than zero.");
				$('#text_color_prog_qty_'+rowId).val(updateQty);
				$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
				{
					totColorProgQty += $(this).find('input[name="text_color_prog_qty[]"]').val()*1;
				});
				$("#totProgQty").val(totColorProgQty.toFixed(2));
				return;
			}

			if(bookingQty < (previousQty+qty))
			{
				alert("Program quantity can't exceed booking quantity");
				$('#text_color_prog_qty_'+rowId).val(updateQty);
				$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
				{
					totColorProgQty += $(this).find('input[name="text_color_prog_qty[]"]').val()*1;
				});
				$("#totProgQty").val(totColorProgQty.toFixed(2));
				return;
			}

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				totColorProgQty += $(this).find('input[name="text_color_prog_qty[]"]').val()*1;
			});
			$("#totProgQty").val(totColorProgQty.toFixed(2));
		}

		function openpage_color_and_size_wise(rowId,colorId) {
        	var hidden_color_id = $('#hidden_color_id').val();
        	//var prog_no = $('#update_dtls_id').val();
        	var save_color_wise_prog_data = $('#hidden_color_wise_prog_data').val();

        	var sizeWiseSaveStringData = $('#hdn_size_wise_save_string_'+rowId).val();
        	var color_wise_break_down_mst_id = $('#colo_prog_update_id_'+rowId).val();
        	var prog_no = $('#color_prog_no_'+rowId).val();


        	var page_link = " planning_info_entry_for_sales_order_controller.php?action=color_and_sizewise_info_popup&companyID="+<? echo $companyID; ?>+"&po_id="+"<? echo $po_id; ?>"+"&pre_cost_id="+"<? echo $pre_cost_id; ?>"+"&pre_cost="+"<? echo $pre_cost; ?>"+"&booking_no="+"<? echo trim($booking_no); ?>"+"&dia="+"<?php echo $dia; ?>"+"&hidden_color_id="+hidden_color_id +"&save_color_wise_prog_data="+save_color_wise_prog_data +"&plan_id="+"<?php echo $plan_id; ?>"+ '&prog_no=' + prog_no +"&colorId="+colorId+"&rowId="+rowId+"&color_wise_break_down_mst_id="+color_wise_break_down_mst_id+"&sizeWiseSaveStringData="+encodeURIComponent(String(sizeWiseSaveStringData))+"&body_part_id="+"<? echo $body_part_id; ?>"+"&bookingWithoutOrder="+"<?php echo $bookingWithoutOrder; ?>"+"&determination_id="+"<?php echo $determination_id; ?>";
        	var title = 'Size Info';

        	//alert(colorId);return;

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,"width=570px,height=250px,center=1,resize=1,scrolling=0", '../');

        	emailwindow.onclose = function ()
        	{
        		var theform = this.contentDoc.forms[0];
        		/*var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;

        		$('#txt_color').val(hidden_color_no);
        		$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);*/
        		//var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_row_id = this.contentDoc.getElementById("hidden_color_row_id").value;
				var hidden_grandtotal_kg_qty = this.contentDoc.getElementById("hidden_grandtotal_kg_qty").value;
				var hidden_size_wise_prog_data = this.contentDoc.getElementById("hidden_size_wise_prog_data").value;

				$('#text_color_prog_qty_'+hidden_color_row_id).val(hidden_grandtotal_kg_qty);
				$('#hdn_size_wise_save_string_'+hidden_color_row_id).val(hidden_size_wise_prog_data);

        	}
        }

	</script>
</head>
<body>
	<div align="center" style="width:700px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:700px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="260">Color</th>
							<th width="80">Qnty</th>
							<th width="90">Prog. Qty</th>
							<th width="80">Prev. Prog. Qty</th>
							<th>Balance</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" class="text_boxes" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" class="text_boxes" value=""/>

                            <input type="hidden" name="txt_selected_color_bl_qty" id="txt_selected_color_bl_qty" class="text_boxes" value=""/>
							<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_size_wise_prog_string" id="hidden_size_wise_prog_string" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_prog_qty" id="hidden_total_prog_qty" class="text_boxes" value="">
							<input type="hidden" name="hidden_colorRange" id="hidden_colorRange" class="text_boxes" value="">
						</thead>
					</table>
					<div style="width:700px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search">
						<tbody>
							<?
							//for program information
							if($plan_id!="")
							{
								$plan_sql = "SELECT b.id AS ID, b.plan_id AS PLAN_ID, b.program_no AS PROGRAM_NO, b.color_id AS COLOR_ID, b.color_prog_qty COLOR_PROG_QTY,b.size_wise_prog_string as SIZE_WISE_PROG_STRING FROM ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b WHERE a.dtls_id = b.program_no AND a.mst_id = b.plan_id AND a.po_id IN(".$job_id.") AND b.plan_id = ".$plan_id." AND b.status_active=1 AND b.is_deleted=0 GROUP BY b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty,b.size_wise_prog_string";

								//$plan_sql = "SELECT b.id AS ID, b.plan_id AS PLAN_ID, b.program_no AS PROGRAM_NO, b.color_id AS COLOR_ID, a.program_qnty COLOR_PROG_QTY FROM ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b WHERE a.dtls_id = b.program_no AND a.mst_id = b.plan_id AND a.po_id IN(2693) AND b.plan_id = 9721 AND b.status_active=1 AND b.is_deleted=0 and a.SALES_ORDER_DTLS_IDS in('17305,17307')GROUP BY b.id, b.plan_id, b.program_no, b.color_id, a.program_qnty ";

								//$plan_sql = "SELECT b.id AS ID, b.plan_id AS PLAN_ID, b.program_no AS PROGRAM_NO, b.color_id AS COLOR_ID, a.program_qnty COLOR_PROG_QTY FROM ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b WHERE a.dtls_id = b.program_no AND a.mst_id = b.plan_id AND a.po_id IN(".$job_id.") AND b.plan_id = ".$plan_id." AND b.status_active=1 AND b.is_deleted=0 and a.SALES_ORDER_DTLS_IDS in('".$sales_order_dtls_id."') GROUP BY b.id, b.plan_id, b.program_no, b.color_id, a.program_qnty";


								//echo $plan_sql;
								$plan_data = sql_select($plan_sql);
								$color_prog_data = array();
								foreach ($plan_data as $row)
								{
									$color_plan_data[$row['PLAN_ID']][$row['COLOR_ID']]['color_prog_qty_total'] += $row['COLOR_PROG_QTY'];
									$color_plan_data[$row['PLAN_ID']][$row['COLOR_ID']]['colo_prog_update_id'] = $row['ID'];

									$color_prog_data[$row['PLAN_ID']][$row['PROGRAM_NO']][$row['COLOR_ID']]['colo_prog_update_id'] = $row['ID'];
									$color_prog_data[$row['PLAN_ID']][$row['PROGRAM_NO']][$row['COLOR_ID']]['color_prog_qty'] = $row['COLOR_PROG_QTY'];
									$color_prog_data[$row['PLAN_ID']][$row['PROGRAM_NO']][$row['COLOR_ID']]['size_wise_prog_string'] = $row['SIZE_WISE_PROG_STRING'];
								}
							}
							else
							{
								//$save_color_wise_prog_data = array();
								$save_color_wise_prog_data = explode(",", $save_color_wise_prog_data);

								$colo_prog_update_id_arr = array();
								for ($k = 0; $k < count($save_color_wise_prog_data); $k++)
								{
									if($save_color_wise_prog_data[$k] != '')
									{
										$colorWiseProgData = explode("_", $save_color_wise_prog_data[$k]);
										foreach ($colorWiseProgData as $key => $value)
										{
											$colo_prog_update_id_arr[$colorWiseProgData[0]] = $colorWiseProgData[2];
										}
									}
								}

								//print_r($colo_prog_update_id_arr);

							}
							//end

							$sales_dtls_id = "";
							$hidden_color_id = explode(",", $hidden_color_id);
							$program_color_id = array_unique(explode(",", $program_color_id));
							$sales_job_ids = explode("_", $sales_order_dtls_id);

							$bodypartId = explode(",", $body_part_id);
							$bodypartId = implode(",", array_unique($bodypartId));


							foreach ($sales_job_ids as $row)
							{
								$sales_dtls_id .= "," . $row;
							}

							$sales_dtls_id = ltrim($sales_dtls_id, ",");
							$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

							$dia=strtoupper($dia);
							$sql_colorRange = "SELECT color_id AS COLOR_ID, COLOR_RANGE_ID FROM fabric_sales_order_dtls WHERE status_active=1 AND is_deleted=0 AND mst_id=".$job_id." AND id IN(".$sales_dtls_id.") AND body_part_id IN(".$bodypartId.") AND trim(fabric_desc) = '".$desc."' AND upper(dia) = '".$dia."' AND gsm_weight = '".$gsm."' AND width_dia_type = '".$width_dia_type."' GROUP BY color_id,color_range_id";
							$result_colorRange = sql_select($sql_colorRange);
							foreach ($result_colorRange as $rows)
							{
								$color_range_idArr[$rows['COLOR_ID']]['color_range_id'] = $rows['COLOR_RANGE_ID'];
							}


							$sql = "SELECT color_id AS COLOR_ID, sum(grey_qty) AS QTY FROM fabric_sales_order_dtls WHERE status_active=1 AND is_deleted=0 AND mst_id=".$job_id." AND id IN(".$sales_dtls_id.") AND body_part_id IN(".$bodypartId.") AND trim(fabric_desc) = '".$desc."' AND upper(dia) = '".$dia."' AND gsm_weight = '".$gsm."' AND width_dia_type = '".$width_dia_type."' GROUP BY color_id";
							//echo $sql;
							$result = sql_select($sql);

							$body_part_type=return_library_array("select id, body_part_type from lib_body_part where status_active=1",'id','body_part_type');
							//echo $cbo_body_part;
							//echo $body_part_type[$cbo_body_part];die;
							$bodyPart=explode(",",$body_part_id);
							if($body_part_type[$bodyPart[0]] == 40 || $body_part_type[$bodyPart[0]] == 50){
							$popupOnOff = 1;
							} else {
								$popupOnOff = 0;
							}

							$i = 1;
							$tot_qnty = 0;
							$tot_clr_prog_qnty = 0;
							$tot_prev_qnty = 0;
							$tot_balance_qnty = 0;
							foreach ($result as $row)
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								if (in_array($row['COLOR_ID'], $hidden_color_id))
								{
									if ($color_row_id == "")
										$color_row_id = $i;
									else
										$color_row_id .= "," . $i;
								}

								if (in_array($row['COLOR_ID'], $program_color_id))
								{
									$color = "background-color:lightgreen;";
								}
								else
								{
									$color = "";
								}
								if (in_array($row['COLOR_ID'], $hidden_color_id))
								{
									$selected_color = "background-color:green;";
								}
								else
								{
									$selected_color = "";
								}
								//echo $save_color_wise_prog_data."yes";
								//for program information
								if($plan_id!="")
								{
									$colo_prog_update_id = $color_prog_data[$plan_id][$prog_no][$row['COLOR_ID']]['colo_prog_update_id'];
								}
								else
								{
									$colo_prog_update_id = $colo_prog_update_id_arr[$row['COLOR_ID']];
								}


								$color_prog_qty = $color_prog_data[$plan_id][$prog_no][$row['COLOR_ID']]['color_prog_qty'];
								$color_total_prog_qty = $color_plan_data[$plan_id][$row['COLOR_ID']]['color_prog_qty_total'];
								$blance = ($row['QTY']-($color_total_prog_qty));
								$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);
								$sizeWiseSaveData=$color_prog_data[$plan_id][$prog_no][$row['COLOR_ID']]['size_wise_prog_string'];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer; <?php echo $color; ?> <?php echo $selected_color; ?>" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="40" align="center"><? echo $i; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row['COLOR_ID']; ?>"/>
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<? echo $color_library[$row['COLOR_ID']]; ?>"/>
										<input type="hidden" name="color_prog_no[]" id="color_prog_no_<?php echo $i; ?>" value="<? echo  $update_id= ($colo_prog_update_id!="")?$prog_no:"0"; ?>"/>
										<input type="hidden" name="colo_prog_update_id[]" id="colo_prog_update_id_<?php echo $i; ?>" value="<? echo  $update_id= ($colo_prog_update_id!="")?$colo_prog_update_id:"0"; ?>"/>
										<input type="hidden" name="hdn_size_wise_save_string[]" id="hdn_size_wise_save_string_<?php echo $i; ?>" value="<? if($sizeWiseSaveData!=""){echo $sizeWiseSaveData;} ?>"/>
									</td>
									<td width="260"><? echo $color_library[$row['COLOR_ID']]; ?>
										<input type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" value="<? echo $row['COLOR_ID']; ?>"/>
										<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" value="<? echo $color_library[$row['COLOR_ID']]; ?>"/>
										<input type="hidden" name="text_color_range_[]" id="text_color_range_<? echo $i;?>" value="<? echo $color_range_idArr[$row['COLOR_ID']]['color_range_id']; ?>"/>
									</td>
									<td width="80" align="right"><? echo number_format($row['QTY'], 2); ?>
										<input type="hidden" name="hidden_color_allowed_qty[]" id="hidden_color_allowed_qty<? echo $i;?>" value="<? echo number_format($row['QTY'], 2, '.', ''); ?>"/>
									</td>

									<?
									if(str_replace("'", '', $hdnVariableCollarCuff)==1)
									{
										if($popupOnOff==1)
										{
											?>

												<td width="90"><input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: right;" placeholder="Write" data-update-qty="<?php echo $text_color_prog_qty; ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" onClick="openpage_color_and_size_wise('<?php echo $i; ?>','<?php echo $row['COLOR_ID']; ?>')" readonly />

											<?
										}
										else
										{
											?>
												<td width="90"><input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: right;" placeholder="Write" data-update-qty="<?php echo $text_color_prog_qty; ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" /></td>
											<?
										}

									}
									else
									{
										?>

											<td width="90"><input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: right;" placeholder="Write" data-update-qty="<?php echo $text_color_prog_qty; ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" /></td>

										<?
									}

									?>

									<td width="80" align="right"><? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2):"0"; ?>
										<input type="hidden" name="hidden_color_prev_prog_qty[]" id="hidden_color_prev_prog_qty_<? echo $i;?>" value="<? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2, '.', ''):"0"; ?>"/>
									</td>
									<td align="right"><? echo $balanceQty = ($blance>0)?number_format($blance ,2):"0" ; ?>
										<input type="hidden" name="txt_individual_color_blqty[]" id="txt_individual_color_blqty<?php echo $i; ?>" value="<? echo $balanceQty = ($blance>0)?number_format($blance ,2, '.', ''):"0" ; ?>"/>
									</td>
							</tr>
							<?
							$i++;
							$tot_qnty += $row['QTY'];
							//$tot_qnty += number_format($row['QTY'],2, '.', '');
							$tot_clr_prog_qnty += $text_color_prog_qty;
							$tot_prev_qnty += $previous_color_prog_qty;
							$tot_balance_qnty += $balanceQty;
						}
						?>
						<input type="hidden" name="txt_color_row_id" id="txt_color_row_id" value="<?php echo $color_row_id; ?>"/>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" align="right"><b>Total</b></th>
							<th align="right" style="padding: 1px 1px;"><? echo number_format($tot_qnty, 2); ?></th>
                            <th><input type="text" id="totProgQty" name="totProgQty" class="text_boxes" style="width:80px; text-align:right" readonly value="<? echo number_format($tot_clr_prog_qnty, 2); ?>" /></th>
							<th align="right" style="padding: 1px 1px;"><? echo number_format($tot_prev_qnty, 2); ?></th>
							<th align="right" style="padding: 1px 1px;"><? echo number_format($tot_balance_qnty, 2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div style="width:100%; margin-left:10px; margin-top:5px">
			<div style="width:43%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/> Check /
				Uncheck All
			</div>
			<div style="width:57%; float:left" align="left">
				<!--<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>-->
				<input type="button" name="close" onClick="fnc_close('<?php echo $is_color_mixing; ?>');" class="formbutton" value="Close" style="width:100px"/>
			</div>
		</div>
	</fieldset>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
//csf('color_id')
//csf('qty')
//csf('fabric_color_id'
}
if ($action == "color_and_sizewise_info_popup")
{
	echo load_html_head_contents("Size Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
			set_all();
		});

		var selected_id = new Array, selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function set_all() {
			var old = document.getElementById('txt_color_row_id').value;
			if (old != "") {
				old = old.split(",");
				for (var i = 0; i < old.length; i++) {
					js_set_value(old[i])
				}
			}
		}

		/*
		function js_set_value2(str)
		{
			var id = $('#txt_individual_id' + str).val()
			var name = $('#txt_individual' + str).val();
			var color_balance_qty = $('#txt_individual_color_blqty' + str).val();
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_color_bl_qty').val(color_balance_qty);
			//parent.emailwindow.hide();
		}*/

		function js_set_value(str)
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {

				selected_id.push($('#txt_individual_id' + str).val());
				selected_name.push($('#txt_individual' + str).val());
			}
			else {
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

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}

		//func_onkeyup_color_size_qty
		function func_onkeyup_color_size_qty(rowId)
		{
			//var color_size_qty_pcs = $('#hidden_color_size_qty_pcs'+rowId).val()*1;
			var current_qty_pcs = $('#text_current_qty_'+rowId).val()*1;

			var per_pcs_qty = $('#text_per_pcs_qty_'+rowId).val()*1;
			var tot_kg=current_qty_pcs/per_pcs_qty;
			$('#txt_tot_kg_'+rowId).val(tot_kg.toFixed(2));
		}
		function func_onkeyup_sum_qnty(rowId)
		{
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
			tbl_row_count = tbl_row_count - 1;
			var totalSum=0;
			for (var i = 1; i <= tbl_row_count; i++) {
				totalSum+= $('#txt_tot_kg_'+i).val()*1;
			}
			$('#txt_sumID').text(totalSum)*1;
		}

		function fnc_close()
		{
			var save_string = "";
			var breakOut = true;
			var total_per_kg_qty = 0;
			var total_kg_qty = 0;
			var color_name_string = '';
			var color_id_string = '';

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var sizeProgUpdatePrimaryIds="";
				var coloProgUpdateId = $(this).find('input[name="size_prog_update_id[]"]').val();
				var sizeProgUpdatePrimaryId = $(this).find('input[name="size_prog_update_primary_id[]"]').val();

				var txtColorId = $(this).find('input[name="text_colorid_[]"]').val();
				var txtColorName = $(this).find('input[name="text_color_name_[]"]').val().trim();

				var txtGreySizeId = $(this).find('input[name="text_greySizeid_[]"]').val().trim();
				var txtGreySizeName = $(this).find('input[name="text_grey_size_name_[]"]').val().trim();

				var txtFinishSizeId = $(this).find('input[name="text_finishSizeid_[]"]').val().trim();
				var txtBodyPartId = $(this).find('input[name="text_body_part_[]"]').val();

				var txtSizePerKgQty = $(this).find('input[name="text_per_pcs_qty[]"]').val() * 1;
				var textCurrentQty = $(this).find('input[name="text_current_qty[]"]').val() * 1;

				var hidden_size_tot_kg = $(this).find('input[name="txt_tot_kg[]"]').val() * 1;
				var hidden_grandtotal_kg_qty = $(this).find('input[name="hidden_grandtotal_kg_qty[]"]').val() * 1;
				var updateQty = $(this).find('input[name="text_per_pcs_qty[]"]').attr('data-update-qty')*1;

				//if(txtSizePerKgQty>0 || coloProgUpdateId !=0)


				if(txtSizePerKgQty>0 || (coloProgUpdateId !=0 && updateQty > 0))
				{
					if(sizeProgUpdatePrimaryId>0){
						sizeProgUpdatePrimaryIds="_"+ sizeProgUpdatePrimaryId;
					}


					if (save_string == "")
					{
						save_string = txtColorId + "_" + txtGreySizeId + "_" + txtFinishSizeId + "_" + txtSizePerKgQty+ "_" + hidden_size_tot_kg+ "_" + coloProgUpdateId+ "_" + txtBodyPartId+ "_" + textCurrentQty+sizeProgUpdatePrimaryIds;
						color_name_string = txtColorName;
						color_id_string = txtColorId;
						//color_prog_qty_string = txtSizePerKgQty;

					}
					else
					{
						save_string += "##" + txtColorId + "_" + txtGreySizeId + "_" + txtFinishSizeId + "_" + txtSizePerKgQty+ "_" + hidden_size_tot_kg+ "_" + coloProgUpdateId+ "_" + txtBodyPartId+ "_" + textCurrentQty+sizeProgUpdatePrimaryIds;
						color_name_string += "," + txtColorName;
						color_id_string =  txtColorId;
						//color_prog_qty_string += "," + txtSizePerKgQty;
					}

					total_per_kg_qty += txtSizePerKgQty;
					total_kg_qty += hidden_size_tot_kg;


				}
			});

			if (total_per_kg_qty <= 0)
			{
				alert("Per KG quantity zero is not allowed");
				$('#text_per_pcs_qty_1').focus();
				return false;
			}

			$('#hidden_size_wise_prog_data').val(save_string);
			$('#hidden_total_kg_qty').val(total_per_kg_qty.toFixed(2));
			$('#hidden_grandtotal_kg_qty').val(total_kg_qty.toFixed(2));
			$('#txt_selected_id').val(color_id_string);
			$('#txt_selected').val(color_name_string);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="left" style="width:570px; margin-left:-18px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:550px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="60">Gmt Size</th>
							<th width="70">Finish Size</th>
							<th width="70">Qty Pcs</th>
							<th width="70">Current Qty</th>
							<th width="70">Balance</th>
							<th width="70">Per Kg Qty</th>
							<th >Total Kg</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" value=""/>
							<input type="hidden" name="hidden_size_wise_prog_data" id="hidden_size_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_kg_qty" id="hidden_total_kg_qty" class="text_boxes" value="">
							<input type="hidden" name="hidden_grandtotal_kg_qty" id="hidden_grandtotal_kg_qty" class="text_boxes" value="">
							<input type="hidden" name="hidden_color_row_id" id="hidden_color_row_id" class="text_boxes" value="<? echo $rowId; ?>">
						</thead>
					</table>
					<div style="width:550px; overflow-y:scroll; max-height:215px;" id="buyer_list_view" align="left">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="530" class="rpt_table"
						id="tbl_list_search">
							<tbody>
								<?
								$totalSumation=0;
								if($sizeWiseSaveStringData!="")
								{
									$saveStringsExp=explode("##", $sizeWiseSaveStringData);
									foreach ($saveStringsExp as $val) {
										$saveStrData=explode("_", $val);
										$saveStringsExpArr[$saveStrData[1]][$saveStrData[2]]['perKg']=$saveStrData[3];
										$saveStringsExpArr[$saveStrData[1]][$saveStrData[2]]['totKg']=$saveStrData[4];
										$totalSumation+=$saveStrData[4];
									}
								}
								/*echo "<pre>";
								print_r($saveStringsExpArr);
								echo "</pre>";*/

								$hidden_color_id = explode(",", $hidden_color_id);
								$pre_cost_id = explode(",", $pre_cost_id);
								$pre_cost_id = implode(",", array_unique($pre_cost_id));
								$pre_cost_idss = explode("_", $pre_cost);
								$pre_cost_idss = implode(",", array_unique($pre_cost_idss));
								$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
								$size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");

								$dia=strtoupper($dia);
								if ($dia!= "" || $db_type == 0) {
									$dia_cond = "b.dia_width like '%$dia%'";
									$dia_cond2 = "b.dia like '%$dia%'";
								} else
								{
									$dia_cond = "b.dia_width is null";
									$dia_cond2 = "b.dia is null";
								}

								//$sql = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";
								/*$sql = "select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity,sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent
								from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
								where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
								and  b.color_size_table_id=d.id and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss)
								and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
								group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent  order by d.size_number_id";*/

								if($bookingWithoutOrder==1)
								{
									$sql = "select c.sample_mst_id,b.fabric_color as fabric_color_id,b.body_part as body_part_id,f.size_id as size_number_id,sum(b.grey_fabric) as qnty ,null as order_quantity, null as plan_cut_qnty,null as colar_cuff_per,e.body_part_type ,null as colar_excess_percent, null as cuff_excess_percent ,f.total_qty ,
									c.sample_color as gmts_color,x.item_size as finishSize,x.qnty_pcs
									from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b ,sample_development_fabric_acc d,sample_requisition_coller_cuff x,sample_development_dtls c,sample_development_size f, lib_body_part e
									where a.booking_no=b.booking_no and b.dtls_id=d.id and b.gmts_item_id=d.gmts_item_id and b.body_part=d.body_part_id and b.dia=d.dia and b.lib_yarn_count_deter_id=d.determination_id and d.id=x.dtls_id and c.sample_mst_id=x.mst_id
									and d.sample_mst_id=c.sample_mst_id
									and c.id=f.dtls_id and c.sample_mst_id=f.mst_id and b.body_part=e.id and a.booking_no='$booking_no' and a.company_id=$companyID and a.item_category=2 and b.fabric_color=$colorId and $dia_cond2  and b.body_part in($body_part_id)
									and f.size_id=x.size_id and x.sample_color=c.sample_color and x.status_active=1 and x.is_deleted=0
									and b.lib_yarn_count_deter_id in ($determination_id) and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and f.is_deleted=0
									group by c.sample_mst_id,b.fabric_color,b.body_part ,e.body_part_type ,f.size_id,f.total_qty ,c.sample_color,x.item_size,x.qnty_pcs
									order by f.size_id ";
								}
								else
								{
									$sql = "select x.fabric_color_id, x.body_part_id,x.size_number_id,x.qnty ,x.order_quantity,x.plan_cut_qnty,x.colar_cuff_per,x.body_part_type,x.colar_excess_percent, x.cuff_excess_percent,x.item_size,x.entry_form from (select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity,sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent,null as item_size,a.entry_form 
									from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
									where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
									and  b.color_size_table_id=d.id and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond  and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss)
									and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.body_part_id in($body_part_id) and a.is_short=2 and b.booking_type=1
									group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent,a.entry_form

									union all
									select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity,
									sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent,null as item_size,a.entry_form 
									from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
									where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
									and b.po_break_down_id=d.po_break_down_id  and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond  and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss)
									and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and a.is_short=2 and b.booking_type=1 and c.body_part_id in($body_part_id) and a.is_short<>2 and b.booking_type<>1
									group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent,a.entry_form
									union all
									select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity,sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent,null as item_size,a.entry_form 
									from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
									where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
									and b.po_break_down_id=d.po_break_down_id and b.gmts_size=d.size_number_id  and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond  and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss)
									and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.is_short=1 and c.body_part_id in($body_part_id)
									group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent,a.entry_form

									union all 

									select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,null as order_quantity,sum(d.qty) as plan_cut_qnty,null as colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent,d.item_size,a.entry_form from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_booking_colar_culff_dtls d,lib_body_part e where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id=d.po_break_down_id and c.body_part_id=e.id and c.job_no=d.job_no and b.id=d.wo_non_ord_samp_booking_dtl_id and b.booking_no=d.booking_no and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond  and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss) and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.is_short=1 and a.booking_type=1 and c.body_part_id in($body_part_id)  and a.entry_form=88
									group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent,d.item_size,a.entry_form ) x group by x.fabric_color_id, x.body_part_id,x.size_number_id,x.qnty ,x.order_quantity,x.plan_cut_qnty,x.colar_cuff_per,x.body_part_type,x.colar_excess_percent, x.cuff_excess_percent,x.item_size,x.entry_form order by x.size_number_id";
									//and b.po_break_down_id=d.po_break_down_id and b.gmts_size=d.size_number_id and b.fabric_color_id =d.color_number_id
									//echo $sql;
									//and a.is_short<>2 and b.booking_type<>1 and a.is_short=2 and b.booking_type=1
									//and a.is_short=1 and b.fabric_color_id =d.color_number_id


									$sql_gmts_size = sql_select("select b.fabric_color_id, c.body_part_id,d.gmts_sizes,d.item_size
									from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d
									where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and c.id=d.pre_cost_fabric_cost_dtls_id and c.job_no=d.job_no and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID
									and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss) and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.body_part_id in($body_part_id)
									group by b.fabric_color_id, c.body_part_id,d.gmts_sizes,d.item_size");
									foreach ($sql_gmts_size as $row)
									{
										if($row[csf('item_size')]!='0')
										{
											$finish_size_arr[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('gmts_sizes')]]['item_size']=$row[csf('item_size')];
										}
									}
								}

								if($prog_no>0)
								{
									//color_wise_break_down_mst_id
									//$plan_sql = "select id, plan_id, program_no, color_id, color_prog_qty from ppl_color_wise_break_down where plan_id = $plan_id and status_active=1 and is_deleted=0";

									/*echo $size_plan_sql = "select b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty
									from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b , ppl_size_wise_break_down c
									where a.dtls_id = b.program_no and a.mst_id = b.plan_id and b.id=c.color_wise_mst_id and b.program_no=c.program_no and b.plan_id=c.plan_id and b.color_id=c.color_id and b.plan_id =".$plan_id." and b.status_active=1 and b.is_deleted=0 and b.program_no=$prog_no and b.id=$color_wise_break_down_mst_id
									GROUP BY b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty";*/

									$size_plan_sql = "select b.id, b.plan_id, b.program_no, b.color_id,c.id as size_tbl_id,c.grey_size_id,c.finish_size_id,c.per_kg,c.current_qty,c.kg_wise_total_qnty,c.body_part_id
									from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b , ppl_size_wise_break_down c
									where a.dtls_id = b.program_no and a.mst_id = b.plan_id and b.id=c.color_wise_mst_id and b.program_no=c.program_no and b.plan_id=c.plan_id and b.color_id=c.color_id  and b.plan_id =".$plan_id." and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.program_no=$prog_no and b.id=$color_wise_break_down_mst_id and c.body_part_id in($body_part_id)
									GROUP BY b.id, b.plan_id, b.program_no, b.color_id,c.id,c.grey_size_id,c.finish_size_id,c.per_kg,current_qty,c.kg_wise_total_qnty,c.body_part_id";



									$size_plan_data = sql_select($size_plan_sql);
									$color_prog_data = array();
									foreach ($size_plan_data as $row)
									{
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['per_kg'] = $row[csf('per_kg')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['kg_wise_total_qnty'] = $row[csf('kg_wise_total_qnty')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['current_qty'] = $row[csf('current_qty')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['size_prog_update_id'] = $row[csf('id')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['size_prog_update_primary_id'] = $row[csf('size_tbl_id')];
									}
								}

								//cummulitive qnty
								$cumm_size_plan_sql = "select b.id, b.plan_id, b.program_no, b.color_id,c.id as size_tbl_id,c.grey_size_id,c.finish_size_id,c.per_kg,c.kg_wise_total_qnty,c.current_qty,c.body_part_id
								from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b , ppl_size_wise_break_down c
								where a.dtls_id = b.program_no and a.mst_id = b.plan_id and b.id=c.color_wise_mst_id and b.program_no=c.program_no and b.plan_id=c.plan_id and b.color_id=c.color_id and b.plan_id =".$plan_id." and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_no= '$booking_no' and a.pre_cost_fabric_cost_dtls_id in ($pre_cost_idss) and c.body_part_id in($body_part_id) GROUP BY b.id, b.plan_id, b.program_no, b.color_id,c.id,c.grey_size_id,c.finish_size_id,c.per_kg,c.kg_wise_total_qnty,c.current_qty,c.body_part_id";



								$cumm_size_plan_data = sql_select($cumm_size_plan_sql);
								foreach ($cumm_size_plan_data as $row)
								{
									$cumm_size_prog_data[$row[csf('plan_id')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['per_kg']+= $row[csf('per_kg')];
									$cumm_size_prog_data[$row[csf('plan_id')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['kg_wise_total_qnty']+= $row[csf('kg_wise_total_qnty')];
									$size_prog_data_balance[$row[csf('plan_id')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['current_qty']+= $row[csf('current_qty')];

								}

								/*echo "<pre>";
								print_r($size_prog_data);*/

								$result = sql_select($sql);
								$i = 1;
								$tot_qnty = 0;
								foreach ($result as $row)
								{
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";



									if (in_array($row[csf('fabric_color_id')], $hidden_color_id)) {
										if ($color_row_id == "") $color_row_id = $i; else $color_row_id .= "," . $i;
									}
									//echo $plan_id."==".$row[csf('fabric_color_id')];

									//$colo_prog_update_id = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
									/*$colo_prog_update_id = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
									$color_prog_qty = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['color_prog_qty'];
									$color_total_prog_qty = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['color_prog_qty_total'];
									$blance = ($row[csf('qnty')]-($color_total_prog_qty));
									$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);*/

									if($bookingWithoutOrder==1)
									{
										$finshSizeId=$row[csf('finishSize')];
									}
									else
									{
										if($row[csf('entry_form')]==88)
										{
											$finshSizeId=$row[csf('item_size')];
											if($finshSizeId=="")
											{
												$finshSizeId=$finish_size_arr[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('size_number_id')]]['item_size'];
											}
										}
										else
										{
											$finshSizeId=$finish_size_arr[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('size_number_id')]]['item_size'];
										}
									}


									$per_kg=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['per_kg'];
									$kg_wise_total_qnty=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['kg_wise_total_qnty'];

									//echo $plan_id."=".$row[csf('fabric_color_id')]."=".$row[csf('size_number_id')]."=".$finshSizeId."=".$row[csf('body_part_id')]."<br/>";
									$current_total_qnty=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['current_qty'];

									$size_prog_update_id=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['size_prog_update_id'];
									$size_prog_update_primary_id=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['size_prog_update_primary_id'];

									$current_total_qnty_balance=$size_prog_data_balance[$plan_id][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['current_qty'];

									$cumm_per_kg=$cumm_size_prog_data[$plan_id][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['per_kg'];
									$cumm_kg_wise_total_qnty=$cumm_size_prog_data[$plan_id][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['kg_wise_total_qnty'];

									//echo $cumm_kg_wise_total_qnty;

									if($row[csf('body_part_type')]==50)
									{
										$plantCutQnty=$row[csf('plan_cut_qnty')]*2;
									}
									else
									{
										$plantCutQnty=$row[csf('plan_cut_qnty')];
									}
									$collar_ex_per=$row[csf('colar_cuff_per')];
									$cuff_excess_percent=$row[csf('cuff_excess_percent')];
									$colar_excess_percent=$row[csf('colar_excess_percent')];


									if($row[csf('body_part_type')]==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                    else if($row[csf('body_part_type')]==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }


                                    $tot_exPer=($plantCutQnty*$collar_ex_per)/100;
									$colar_excess_per=$tot_exPer;
								    $collerqty=($plantCutQnty+$colar_excess_per);

								    //$tot_qnty += $row[csf('order_quantity')];
								    $tot_qnty += number_format($collerqty);

								    if($bookingWithoutOrder==1)
									{
										$collerqty=$row[csf('qnty_pcs')];
										$kg_wise_total_qnty=$kg_wise_total_qnty;
										$tot_qnty += number_format($collerqty);
									}
									else
									{
										if($row[csf('entry_form')]==88)
										{
											$collerqty=$row[csf('plan_cut_qnty')];
										}
										else
										{
											$collerqty=$collerqty;
										}
										$kg_wise_total_qnty=$kg_wise_total_qnty;
									}



									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">

										<td width="40" align="center"><? echo $i; ?>
											<input style="max-width: 20px;" type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
											<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
											<input type="hidden" name="size_prog_update_id[]" id="size_prog_update_id_<?php echo $i; ?>" value="<? echo  $update_id= ($size_prog_update_id!="")?$size_prog_update_id:"0"; ?>"/>
											<input type="hidden" name="size_prog_update_primary_id[]" id="size_prog_update_primary_id_<?php echo $i; ?>" value="<? echo  $size_primary_update_id= ($size_prog_update_primary_id!="")?$size_prog_update_primary_id:"0"; ?>"/>

											<input type="hidden" name="text_body_part_[]" id="text_body_part_<? echo $i;?>" value="<? echo $row[csf('body_part_id')]; ?>"/>

										</td>

										<td width="60" align="center">
											<p><? echo $size_library[$row[csf('size_number_id')]]; ?></p>
											<input style="max-width: 40px;" type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
											<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
											<input type="hidden" name="text_grey_size_name_[]" id="text_grey_size_name_<? echo $i;?>" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>"/>
											<input type="hidden" name="text_greySizeid_[]" id="text_greySizeid_<? echo $i;?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
										</td>
										<td width="70" align="center">
											<? echo $finshSizeId; ?>

											<input style="max-width: 60px;" type="hidden" name="text_finishSizeid_[]" id="text_finishSizeid_<? echo $i;?>" value="<? echo $finshSizeId; ?>"/>

										</td>
										<td width="70" align="right">
											<? echo number_format($collerqty); //number_format($row[csf('order_quantity')], 2); ?>
											<input style="max-width: 60px;" type="hidden" name="hidden_color_size_qty_pcs[]" id="hidden_color_size_qty_pcs<? echo $i;?>" value="<? echo number_format($collerqty); ?>"/>
										</td>

										<td width="70" align="right">
											<input style="max-width: 60px;" type="text" class="text_boxes_numeric" name="text_current_qty[]" id="text_current_qty_<? echo $i;?>" value="<?


											echo  $text_current_prog_qty= ($current_total_qnty>0)?$current_total_qnty:"";


											?>" style="text-align: center;" placeholder="Write" data-update-qty="<?php echo $text_current_prog_qty; ?>"  />

											<?
											 $cumm_size_prog_qty= ($cumm_per_kg>0)?$cumm_per_kg:"";
											 $cumm_kg_wise_prog_qty= ($cumm_kg_wise_total_qnty>0)?$cumm_kg_wise_total_qnty:"";

											//echo $cumm_kg_wise_prog_qty;

											?>
										</td>
										<td width="70" align="right" title="<? echo "Qty Pcs(".number_format($collerqty,0,'.','').")-Prev.Qty($current_total_qnty_balance)"; ?>">
											<?
												$balancing=number_format($collerqty,0,'.','')-$current_total_qnty_balance;
												echo $balancing;
												?>
										</td>

										<td width="70" align="center">
											<input style="max-width: 60px;" type="text" class="text_boxes_numeric" name="text_per_pcs_qty[]" id="text_per_pcs_qty_<? echo $i;?>" value="<?

											/*if($sizeWiseSaveStringData!="")
											{
												echo $saveStringsExpArr[$row[csf('size_number_id')]][$finshSizeId]['perKg'];

											}else{} */
											echo  $text_size_prog_qty= ($per_kg>0)?$per_kg:"";

											?>" style="text-align: center;" placeholder="Write" data-update-qty="<?php echo $text_size_prog_qty; ?>" onKeyUp="func_onkeyup_color_size_qty('<?php echo $i; ?>');func_onkeyup_sum_qnty('<?php echo $i; ?>');"  />
										</td>
										<td  align="center">

											<input style="max-width: 50px;"  type="text" class="text_boxes_numeric" name="txt_tot_kg[]" id="txt_tot_kg_<? echo $i;?>" readonly value="<?
											echo  $text_size_total_kg_prog_qty= ($kg_wise_total_qnty>0)?$kg_wise_total_qnty:"";
											/*if($sizeWiseSaveStringData!="")
											{

												echo $saveStringsExpArr[$row[csf('size_number_id')]][$finshSizeId]['totKg'];
											}
											else{} */


												?>">
											<input type="hidden" name="hidden_size_tot_kg[]" id="hidden_size_tot_kg_<? echo $i;?>" value=""/>

										</td>
									</tr>
									<?
									$i++;
								}
								?>
								<input type="hidden" name="txt_color_row_id" id="txt_color_row_id" value="<?php echo $color_row_id; ?>"/>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3" align="right"><b>Total</b></th>
									<th align="right"><? echo number_format($tot_qnty); ?></th>
									<th align="right">&nbsp;</th>
									<th align="right">&nbsp;</th>
									<th align="right">&nbsp;</th>
									<th id="txt_sumID" align="right"><? echo number_format($totalSumation, 2); ?>&nbsp;</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div style="width:100%; margin-left:10px; margin-top:5px">
					<div style="width:43%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/> Check /
						Uncheck All
					</div>
					<div style="width:57%; float:left" align="left">
						<input type="button" name="close" onClick="fnc_close();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if ($action == "feeder_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function fnc_close() {
			var save_string = '';
			var tot_row = $("#tbl_list_search tbody tr").length;

			for (var i = 1; i <= tot_row; i++) {
				var txtPreCostId = $('#txtPreCostId_' + i).val();
				var txtColorId = $('#txtColorId_' + i).val();
				var txtStripeColorId = $('#txtStripeColorId_' + i).val();
				var txtNoOfFeeder = $('#txtNoOfFeeder_' + i).val();
				var txtFeederSequence = $('#txtFeederSequence_' + i).val();
				var serialNo = i;

				if (save_string == "") {
					save_string = txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder+ "_" + serialNo+ "_" + txtFeederSequence;
				}
				else {
					save_string += "," + txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder+ "_" + serialNo+ "_" + txtFeederSequence;
				}

			}

			$('#hidden_no_of_feeder_data').val(save_string);

			parent.emailwindow.hide();
		}

		function calculate_total() {
			var tot_row = $("#tbl_list_search tbody tr").length;

			var ddd = {dec_type: 6, comma: 0, currency: ''}

			math_operation("txtTotFeeder", "txtNoOfFeeder_", "+", tot_row, ddd);
		}

	</script>

</head>

<body>
	<div style="width:630px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:620px; margin-top:10px; margin-left:5px">
				<input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data" class="text_boxes"
				value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="140">Color</th>
						<th width="130">Stripe Color</th>
						<th width="90">Measurement</th>
						<th width="70">UOM</th>
						<th>No Of Feeder</th>
					</thead>
				</table>
				<div style="width:618px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$noOfFeeder_array = array();
						//echo $no_of_feeder_data;
						//xxxxxx
						$no_of_feeder_data = explode(",", $no_of_feeder_data);

						$pre_cost_id = explode(",", $pre_cost_id);
						$pre_cost_id = implode(",", array_unique($pre_cost_id));

						$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

						for ($i = 0; $i < count($no_of_feeder_data); $i++) {
							$color_wise_data = explode("_", $no_of_feeder_data[$i]);
							$pre_cost_fabric_cost_dtls_id = $color_wise_data[0];
							$color_id = $color_wise_data[1];
							$stripe_color = $color_wise_data[2];
							$no_of_feeder = $color_wise_data[3];
							$serialNo = $color_wise_data[4];
							$feedearSquence = $color_wise_data[5];

							$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color][$feedearSquence] = $no_of_feeder;
						}
						//----------------
						//echo "<pre>";
						//print_r($noOfFeeder_array);
						//----------------

						if($hidden_color_id!="")
						{
							$colorCondition = "and color_number_id in($hidden_color_id)";
						}
						//echo $sales_order_dtls_id .'='.$within_group;
						if($within_group==2)
						{
							//$withInGrpCondition=" sales_dtls_id in($sales_order_dtls_id) and";
							$withInGrpCondition="and a.sales_dtls_id in($sales_order_dtls_id) and b.id=$job_id and";
						}
						else
						{
							//$withInGrpCondition=" pre_cost_fabric_cost_dtls_id in($pre_cost_id) and";
							$withInGrpCondition="and a.pre_cost_fabric_cost_dtls_id in($pre_cost_id) and b.id=$job_id and";
						}
						$sql = "select a.pre_cost_fabric_cost_dtls_id as pre_cost_id, a.color_number_id, a.stripe_color, a.measurement, a.uom,a.sales_dtls_id,a.totfidder,c.dia,a.sequence from wo_pre_stripe_color a,fabric_sales_order_mst b,fabric_sales_order_dtls c where a.job_no=b.job_no and b.id=c.mst_id and a.job_no=c.job_no_mst and a.sales_dtls_id=c.id $withInGrpCondition  a.status_active=1 and a.is_deleted=0 $colorCondition and a.sales_dtls_id is not null and c.dia ='$hidden_sales_dia' and a.color_number_id=c.color_id order by a.color_number_id,a.id";
						//$sql = "select a.pre_cost_fabric_cost_dtls_id as pre_cost_id, a.color_number_id, a.stripe_color, a.measurement, a.uom,a.sales_dtls_id,a.totfidder from wo_pre_stripe_color a,fabric_sales_order_mst b where a.job_no=b.job_no $withInGrpCondition a.status_active=1 and a.is_deleted=0 $colorCondition and a.sales_dtls_id is not null  order by  a.color_number_id,a.id";

						//$sql = "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom,sales_dtls_id,totfidder from wo_pre_stripe_color where $withInGrpCondition status_active=1 and is_deleted=0 $colorCondition and sales_dtls_id is not null  order by color_number_id, stripe_color,measurement";
						//order by  pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color asc
						//order by color_number_id, stripe_color,measurement

						$result = sql_select($sql);
						$i = 1;
						$tot_feeder = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							if($within_group==2){$withInGrpPreCsot_or_salesDtlsId=$row[csf('sales_dtls_id')];}else{$withInGrpPreCsot_or_salesDtlsId=$row[csf('pre_cost_id')];}
							$no_of_feeder = $noOfFeeder_array[$withInGrpPreCsot_or_salesDtlsId][$row[csf('color_number_id')]][$row[csf('stripe_color')]][$row[csf('sequence')]];


							//echo $withInGrpPreCsot_or_salesDtlsId."-".$row[csf('color_number_id')]."-".$row[csf('stripe_color')]."-".$i."<br/>";
							//echo $withInGrpPreCsot_or_salesDtlsId."-";
							//$tot_feeder += $no_of_feeder;
							//echo $no_of_feeder."-";
							//----------------
							//echo $withInGrpPreCsot_or_salesDtlsId.'='.$row[csf('color_number_id')].'='.$row[csf('stripe_color')].'='.$i."<br/>";
							//----------------

							?>
							<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
							<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txtPreCostId[]" id="txtPreCostId_<?php echo $i ?>"
								value="<? if($within_group==2){echo $row[csf('sales_dtls_id')];}else{echo $row[csf('pre_cost_id')];}  ?>"/>
								<input type="hidden" name="txtColorId[]" id="txtColorId_<?php echo $i ?>"
								value="<? echo $row[csf('color_number_id')]; ?>"/>
								<input type="hidden" name="txtStripeColorId[]"
								id="txtStripeColorId_<?php echo $i ?>"
								value="<? echo $row[csf('stripe_color')]; ?>"/>
							</td>
							<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
							<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
							<td width="90"><input type="text" name="txtMeasurement[]"
								id="txtMeasurement_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:80px" value="<? echo $row[csf('measurement')]; ?>"
								disabled/></td>
							<td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
							</td>
							<td align="center">
								<input type="text" name="txtNoOfFeeder[]" id="txtNoOfFeeder_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:90px"
								value="<?

								/*if($within_group==2 && $no_of_feeder=="")
								{
									echo $row[csf('totfidder')];
								}
								else
								{
									echo $no_of_feeder;
								}  */

								if($no_of_feeder=="")
								{
									echo $row[csf('totfidder')];
									$tot_feeder += $row[csf('totfidder')];
								}
								else
								{
									echo $no_of_feeder;
									$tot_feeder += $no_of_feeder;
								}

									?>"


									onKeyUp="calculate_total();"/>

									<input type="hidden" name="txtFeederSequence[]" id="txtFeederSequence_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" value="<? echo $row[csf('sequence')]; ?>" />
							</td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<th colspan="5">Total</th>
						<th style="text-align:center"><input type="text" name="txtTotFeeder" id="txtTotFeeder"
							class="text_boxes_numeric" style="width:90px"
							value="<? echo $tot_feeder; ?>" disabled/></th>
						</tfoot>
					</table>
				</div>
				<table width="600" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "collarCuff_info_popup")
{
	echo load_html_head_contents("Collar & Cuff Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function add_break_down_tr(i) {
			//var row_num = $('#txt_tot_row').val();
			var row_num = $('#tbl_list_search tbody tr').length;
			row_num++;
			/*
			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			console.log(clone);

			clone.find("input,select").each(function () {

				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

		}).end();//.appendTo("#tbl_list_search")
		$("#tr_" + i).after(clone);
		*/

		$("#tbl_list_search tbody tr:last").clone().find("input,select").each(function(){
			$(this).attr('id');
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					'name': function(_, name) { return name },
					'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_list_search");
		var j = 1;
		$("#tbl_list_search tbody").find('tr').each(function()
		{
			$(this).removeAttr('id').attr('id','tr_'+j);
			j++;
		});



			$('#txtGrey_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + row_num + ");");
			$('#txtNeedlePerCm_' + row_num).removeAttr("value").attr("value", "");

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			$('#txt_tot_row').val(row_num);
			reArrangeSl();
			set_all_onclick();
		}

		function reArrangeSl() {
			var i = 0;
			$("#tbl_list_search").find('tbody tr').each(function () {
				i++;
				$(this).find("td:eq(0)").text(i);
			});
			$('#txt_tot_row').val(i);
		}

		function fn_deleteRow(rowNo)
		{
			if (rowNo != 1)
			{
				$("#tr_" + rowNo).remove();
				var i = 1;
				$("#tbl_list_search tbody").find('tr').each(function(){
					$(this).removeAttr('id').attr('id','tr_'+i);

					var tr_id = $(this).attr('id');
					console.log('tr => '+tr_id);

					$("#"+tr_id).find("input,select").each(function(){
						$(this).attr({
							'id': function(_, id) {var id=id.split("_"); return id[0] +"_"+ i },
							'name': function(_, name) {var name=name.split("_"); return name[0] }
						});
					});
					$("#"+tr_id).find("td").each(function(){
						var td_id = $(this).attr('id');
						if(td_id)
						{
							var td_id=td_id.split("_");
							td_id = td_id[0] +"_"+ i;
							$(this).attr('id',td_id);
						}
					});
					$('#txtQtyPcs_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + i + ");");
					$('#increase_' + i).removeAttr("value").attr("value", "+");
					$('#decrease_' + i).removeAttr("value").attr("value", "-");
					$('#increase_' + i).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + i + ");");
					$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
					i++;
				});
				reArrangeSl();
				calculate_tot_qnty();
			}
		}

		function fnc_close() {
			var save_string = "";
			var breakOut = true;
			$("#tbl_list_search").find('tbody tr').each(function () {
				if (breakOut == false) {
					return;
				}

				var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();
				var txtGrey = $(this).find('input[name="txtGrey[]"]').val();
				var txtFinish = $(this).find('input[name="txtFinish[]"]').val();
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				var txtNeedlePerCm = $(this).find('input[name="txtNeedlePerCm[]"]').val() * 1;

				if (txtQtyPcs < 1) {
					alert("Please Insert Qty. Pcs");
					$(this).find('input[name="txtQtyPcs[]"]').focus();
					breakOut = false;
					return false;
				}

				if (save_string == "") {
					save_string = bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs + "_" + txtNeedlePerCm;
				}
				else {
					save_string += "," + bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs  + "_" + txtNeedlePerCm;
				}
			});

			if (breakOut == false) {
				return;
			}
			$('#hidden_collarCuff_data').val(save_string);
			parent.emailwindow.hide();
		}

		function calculate_tot_qnty() {
			var txtTotQtyPcs = '';
			$("#tbl_list_search").find('tbody tr').each(function () {
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				txtTotQtyPcs = txtTotQtyPcs * 1 + txtQtyPcs * 1;
			});

			$('#txtTotQtyPcs').val(Math.round(txtTotQtyPcs));
		}
	</script>
</head>
<body>
	<div style="width:630px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:630px; margin-top:5px">
				<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data" class="text_boxes"
				value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="625" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="100">Body Part</th>
						<th width="100">Grey Size</th>
						<th width="100">Finish Size</th>
						<th width="100">Qty. Pcs</th>
						<th width="100">Needle Per CM</th>
						<th></th>
					</thead>
				</table>
				<div style="width:625px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="605" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$collarCuff_data = ($collarCuff_data != "") ? explode(",", $collarCuff_data) : array();
						$totQtyPcs = 0;
						$row_no = 1;
						if (!empty($collarCuff_data))
						{
							$sl = 1;
							for ($i = 0; $i < count($collarCuff_data); $i++)
							{
								$body_part_wise_data = explode("_", $collarCuff_data[$i]);
								$body_part_id = $body_part_wise_data[0];
								$grey = $body_part_wise_data[1];
								$finish = $body_part_wise_data[2];
								$qty = $body_part_wise_data[3];
								$needlePerCm = $body_part_wise_data[4];
								$totQtyPcs += $qty;
								?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $row_no; ?>">
									<td width="30" align="center" title="<?=$row_no;?>"><? echo $sl++; ?></td>
									<td width="100">
										<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $row_no ?>"
										value="<? echo $body_part[$body_part_id]; ?>" class="text_boxes"
										style="width:80px" disabled/>
										<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $row_no ?>"
										value="<? echo $body_part_id; ?>"/>
									</td>
									<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $row_no; ?>"
										class="text_boxes" style="width:80px"
										value="<? echo $grey; ?>"/>
									</td>
									<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $row_no; ?>"
										class="text_boxes" style="width:80px"
										value="<? echo $finish; ?>"/>
									</td>
									<td width="100">
										<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $row_no; ?>"
										class="text_boxes_numeric" style="width:80px" value="<? echo $qty; ?>"
										onKeyUp="calculate_tot_qnty();"/>
									</td>
									<td width="100">
										<input type="text" name="txtNeedlePerCm[]" id="txtNeedlePerCm_<? echo $row_no; ?>"
										class="text_boxes_numeric" style="width:80px" value="<? echo $needlePerCm; ?>" />
									</td>
									<td>
										<input type="button" id="increase_<? echo $row_no; ?>" name="increase[]"
										style="width:30px" class="formbuttonplasminus" value="+"
										onClick="add_break_down_tr( <? echo $row_no; ?> )"/>
										<input type="button" id="decrease_<? echo $row_no; ?>" name="decrease[]"
										style="width:30px" class="formbuttonplasminus" value="-"
										onClick="fn_deleteRow(<? echo $row_no; ?>);"/>
									</td>
								</tr>
								<?
								$row_no++;
							}
						}
						else
						{
							$pre_cost_id = implode(",", array_unique(explode(",", $pre_cost_id)));
							//$sql = "select a.body_part_id, b.item_size from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.id in($pre_cost_id) and a.body_part_id in($body_part_id) group by a.body_part_id, b.item_size order by a.body_part_id";
							//for coller/cuff qty
							/*
							$book_data=array();
							$sql="select id,booking_no,job_no,po_break_down_id, pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,excess_per,qty from wo_booking_colar_culff_dtls where booking_no ='".$booking_no."' and pre_cost_fabric_cost_dtls_id= '".$pre_cost_id."' and status_active=1 and is_deleted=0 ";
							$sql_data=sql_select($sql);
							foreach($sql_data as $sql_data_row)
							{
								$book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['gmts_qty']=$sql_data_row[csf('gmts_qty')];
								$book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['excess_per']=$sql_data_row[csf('excess_per')];
								$book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['qty']=$sql_data_row[csf('qty')];
								$book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['dtls_id']=$sql_data_row[csf('id')];
							}
							*/
							//echo "<pre>";
							//print_r($book_data); die;
							$bookingNo=explode("-",$booking_no);

							if($bookingNo[1]=="SMN")
							{
								/*$sql="SELECT x.body_part as body_part_id,d.sample_color, d.size_id, d.item_size, d.qnty_pcs
								from wo_non_ord_samp_booking_dtls  x,sample_development_mst a,sample_development_dtls b ,sample_development_size c,sample_requisition_coller_cuff d
								where x.style_id=a.id and  a.id=b.sample_mst_id and b.id=c.dtls_id and x.fab_status_id=d.dtls_id and a.entry_form_id=203 and a.status_active=1 and a.is_deleted=0 and x.body_part in(".$body_part_id.") and a.id in(
								SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and b.body_part in(".$body_part_id.") and a.booking_no='".$booking_no."' and a.entry_form_id=140 and a.status_active=1
								and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
								) group by x.body_part,d.sample_color,d.size_id, d.item_size, d.qnty_pcs";*/

								$sql="SELECT x.body_part as body_part_id,d.sample_color, d.size_id, d.item_size, d.qnty_pcs
								from wo_non_ord_samp_booking_dtls  x,sample_development_mst a,sample_development_dtls b ,sample_development_size c,sample_requisition_coller_cuff d
								where x.style_id=a.id and  a.id=b.sample_mst_id and b.id=c.dtls_id and x.dtls_id=d.dtls_id and a.entry_form_id=203 and a.status_active=1 and a.is_deleted=0 and x.body_part in(".$body_part_id.") and a.id in(
								SELECT b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and b.body_part in(".$body_part_id.") and a.booking_no='".$booking_no."' and a.entry_form_id=140 and a.status_active=1
								and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
								) group by x.body_part,d.sample_color,d.size_id, d.item_size, d.qnty_pcs";
							}
							else
							{

						 		$sql="select e.colar_excess_percent,e.cuff_excess_percent,e.item_size,e.body_part_id,e.po_break_down_id,f.size_number_id,f.color_number_id,f.color_order,f.size_order,sum(f.plan_cut_qnty) as plan_cut_qnty,g.po_number from wo_po_color_size_breakdown f join (select a.colar_excess_percent,a.cuff_excess_percent,b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight,d.color_number_id,d.gmts_sizes ,upper(d.dia_width) as dia_width,d.item_size from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.id=d.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and d.color_number_id=b.gmts_color_id and upper(d.dia_width)=upper(b.dia_width)  and c.id IN(".$pre_cost_id.") and c.body_part_id in(".$body_part_id.") and a.booking_no='".$booking_no."' and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1) e on e.job_no=f.job_no_mst and e.po_break_down_id=f.po_break_down_id and e.color_number_id=f.color_number_id and e.gmts_sizes=f.size_number_id and f.status_active=1 and f.is_deleted=0 join wo_po_break_down g on g.id=f.po_break_down_id and g.job_no_mst=f.job_no_mst group by e.colar_excess_percent,e.cuff_excess_percent,e.item_size,e.body_part_id,e.po_break_down_id,f.size_number_id,f.color_number_id,f.color_order,f.size_order,g.po_number  order by f.color_order,f.size_order";
							}
							//echo $sql;
							$result = sql_select($sql);
							$dataArr = array();
							foreach ($result as $row)
							{
								/*
								if($book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty'])
								{
									$qty = $book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty'];
								}
								else
								{
									$qty = 0;
									$gmts_qty = $row[csf('plan_cut_qnty')];
									if($bodyPartType == 50)
									{
										$qty = $gmts_qty*2;
									}
									elseif($bodyPartType == 40)
									{
										$qty = $gmts_qty*1;
									}
								}
								*/
								$qty = 0;
								$dataArr[$row[csf('body_part_id')]][$row[csf('item_size')]]['qty'] += $qty;
							}
							//echo "<pre>";
							//print_r($dataArr); die;

							$i = 1;
							foreach ($dataArr as $bodyPartId=>$bodyPartArr)
							{
								foreach ($bodyPartArr as $itemSize=>$row)
								{
									$totQtyPcs += $row['qty'];
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $row_no; ?>">
										<td width="30" align="center" title="<?=$row_no;?>"><? echo $sl; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $row_no ?>"
											value="<? echo $body_part[$bodyPartId]; ?>" class="text_boxes"
											style="width:80px" disabled/>
											<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $row_no ?>"
											value="<? echo $bodyPartId; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $row_no; ?>"
											class="text_boxes" style="width:80px"
											value=""/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $row_no; ?>"
											class="text_boxes" style="width:80px"
											value="<? echo $itemSize; ?>"/>
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $row_no; ?>"
											class="text_boxes_numeric" style="width:80px"
											value="<? echo $row['qty']; ?>" onKeyUp="calculate_tot_qnty();"/>
										</td>
										<td width="100">
											<input type="text" name="txtNeedlePerCm[]" id="txtNeedlePerCm_<? echo $row_no; ?>"
											class="text_boxes_numeric" style="width:80px"
											value="" />
										</td>
										<td>
											<input type="button" id="increase_<? echo $row_no; ?>" name="increase[]"
											style="width:30px" class="formbuttonplasminus" value="+"
											onClick="add_break_down_tr( <? echo $row_no; ?> )"/>
											<input type="button" id="decrease_<? echo $row_no; ?>" name="decrease[]"
											style="width:30px" class="formbuttonplasminus" value="-"
											onClick="fn_deleteRow(<? echo $row_no; ?>);"/>
										</td>
									</tr>
									<?
									$i++;
									$row_no++;
								}
							}
						}
						?>
					</tbody>
					<tfoot>
						<th colspan="4">Total</th>
						<th style="text-align:center"><input type="text" name="txtTotQtyPcs" id="txtTotQtyPcs"
							class="text_boxes_numeric" style="width:80px"
							value="<? echo $totQtyPcs; ?>" disabled/><input
							type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $i - 1; ?>"/></th>
							<th></th>
						</tfoot>
					</table>
				</div>
				<table width="500" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//-- cam design
if ($action == "cam_design_info_popup")
{
	echo load_html_head_contents("Cam Design Information", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		function fnc_close()
		{
			var save_came_dsign_string = "";
			$("#tbl_came_design").find('tbody tr').each(function () {
				var updateId = $(this).find('input[name="updateid[]"]').val();
				var cmd1 = $(this).find('input[name="cmd1[]"]').val().toUpperCase().trim();
				var cmd2 = $(this).find('input[name="cmd2[]"]').val().toUpperCase().trim();
				var cmd3 = $(this).find('input[name="cmd3[]"]').val().toUpperCase().trim();
				var cmd4 = $(this).find('input[name="cmd4[]"]').val().toUpperCase().trim();
				var cmd5 = $(this).find('input[name="cmd5[]"]').val().toUpperCase().trim();
				var cmd6 = $(this).find('input[name="cmd6[]"]').val().toUpperCase().trim();
				var cmd7 = $(this).find('input[name="cmd7[]"]').val().toUpperCase().trim();
				var cmd8 = $(this).find('input[name="cmd8[]"]').val().toUpperCase().trim();
				var cmd9 = $(this).find('input[name="cmd9[]"]').val().toUpperCase().trim();
				var cmd10 = $(this).find('input[name="cmd10[]"]').val().toUpperCase().trim();
				var cmd11 = $(this).find('input[name="cmd11[]"]').val().toUpperCase().trim();
				var cmd12 = $(this).find('input[name="cmd12[]"]').val().toUpperCase().trim();
				var cmd13 = $(this).find('input[name="cmd13[]"]').val().toUpperCase().trim();
				var cmd14 = $(this).find('input[name="cmd14[]"]').val().toUpperCase().trim();
				var cmd15 = $(this).find('input[name="cmd15[]"]').val().toUpperCase().trim();
				var cmd16 = $(this).find('input[name="cmd16[]"]').val().toUpperCase().trim();
				var cmd17 = $(this).find('input[name="cmd17[]"]').val().toUpperCase().trim();
				var cmd18 = $(this).find('input[name="cmd18[]"]').val().toUpperCase().trim();
				var cmd19 = $(this).find('input[name="cmd19[]"]').val().toUpperCase().trim();
				var cmd20 = $(this).find('input[name="cmd20[]"]').val().toUpperCase().trim();
				var cmd21 = $(this).find('input[name="cmd21[]"]').val().toUpperCase().trim();
				var cmd22 = $(this).find('input[name="cmd22[]"]').val().toUpperCase().trim();
				var cmd23 = $(this).find('input[name="cmd23[]"]').val().toUpperCase().trim();
				var cmd24 = $(this).find('input[name="cmd24[]"]').val().toUpperCase().trim();
				/*if (txtQtyPcs < 1) {
					alert("Please Insert Qty. Pcs");
					$(this).find('input[name="txtQtyPcs[]"]').focus();
					breakOut = false;
					return false;
				}*/
				if (save_came_dsign_string == "") {
					save_came_dsign_string = updateId +"_"+ cmd1 + "_" + cmd2 + "_" + cmd3 + "_" + cmd4+ "_" + cmd5+ "_" + cmd6+ "_" + cmd7+ "_" + cmd8+ "_" + cmd9+ "_" + cmd10+ "_" + cmd11+ "_" + cmd12+ "_" + cmd13+ "_" + cmd14+ "_" + cmd15+ "_" + cmd16+ "_" + cmd17+ "_" + cmd18+ "_" + cmd19+ "_" + cmd20+ "_" + cmd21+ "_" + cmd22+ "_" + cmd23+ "_" + cmd24;
				}
				else {
					save_came_dsign_string += "," + updateId +"_"+ cmd1 + "_" + cmd2 + "_" + cmd3 + "_" + cmd4+ "_" + cmd5+ "_" + cmd6+ "_" + cmd7+ "_" + cmd8+ "_" + cmd9+ "_" + cmd10+ "_" + cmd11+ "_" + cmd12+ "_" + cmd13+ "_" + cmd14+ "_" + cmd15+ "_" + cmd16+ "_" + cmd17+ "_" + cmd18+ "_" + cmd19+ "_" + cmd20+ "_" + cmd21+ "_" + cmd22+ "_" + cmd23+ "_" + cmd24;
				}
			});
			/*
			if (breakOut == false) {
				return;
			}*/
			$('#hidden_came_dsign_string_data').val("'"+save_came_dsign_string+"'");
			parent.emailwindow.hide();
		};

	</script>

	</head>

	<body>
		<div style="width:900px;" align="center">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:900px; margin-top:5px">
					<input type="hidden" name="hidden_came_dsign_string_data" id="hidden_came_dsign_string_data" value="" >
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
						<thead>
							<th width="4%">SL</th>
							<?
							for ($i=1; $i<=24; $i++)
							{
								?>
								<th width="4%"><? echo $i; ?></th>
								<?
							}
							?>
						</thead>
					</table>
					<div style="width:890px;" id="buyer_list_view">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table"
						id="tbl_came_design">
						<tbody>
							<?
							$sql_camdesign = "select b.id, b.mst_id, b.dtls_id, b.cmd1, b.cmd2, b.cmd3, b.cmd4, b.cmd5, b.cmd6, b.cmd7, b.cmd8, b.cmd9, b.cmd10, b.cmd11, b.cmd12, b.cmd13, b.cmd14, b.cmd15, b.cmd16, b.cmd17, b.cmd18, b.cmd19, b.cmd20, b.cmd21, b.cmd22, b.cmd23, b.cmd24 from ppl_planning_info_entry_dtls a, ppl_planning_cam_design_dtls b where a.id=b.dtls_id and a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$update_dtls_id and b.dtls_id=$update_dtls_id and a.is_sales=1 and b.is_sales=1";

							$sql_camdesign_data = sql_select($sql_camdesign);

							if(empty($sql_camdesign_data))
							{
								$row_data =  array(1,2,3,4,5,6,7,8);
							}else{
								$row_data = $sql_camdesign_data;
							}
							$i=1;
							foreach ($row_data as $row)
							{
								?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i ;?>">
									<td width="4%" align="center"><? echo $i; ?>
									<input type="hidden" name="updateid[]" id="updateid_<?echo $i?>"  value="<? echo $id = ($row[csf('id')]!="")?$row[csf('id')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd1[]" id="cmd1_<?php echo $i ?>" value="<? echo $cmd1 = ($row[csf('cmd1')]!="")?$row[csf('cmd1')]:""; ?>" class="text_boxes" style="width:70%; text-transform: uppercase;" />
								</td>
								<td width="4%">
									<input type="text" name="cmd2[]" id="cmd2_<? echo $i; ?>"
									class="text_boxes " style="width:70%; text-transform: uppercase; text-align: center;"
									value="<? echo $cmd1 = ($row[csf('cmd2')]!="")?$row[csf('cmd2')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd3[]" id="cmd3_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase; text-align: center;"
									value="<? echo $cmd3 = ($row[csf('cmd3')]!="")?$row[csf('cmd3')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd4[]" id="cmd4_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase; text-align: center;"
									value="<? echo $cmd4 = ($row[csf('cmd4')]!="")?$row[csf('cmd4')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd5[]" id="cmd5_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase; text-align: center;"
									value="<? echo $cmd5 = ($row[csf('cmd5')]!="")?$row[csf('cmd5')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd6[]" id="cmd6_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd6 = ($row[csf('cmd6')]!="")?$row[csf('cmd6')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd7[]" id="cmd7_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd7 = ($row[csf('cmd7')]!="")?$row[csf('cmd7')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd8[]" id="cmd8_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd8 = ($row[csf('cmd8')]!="")?$row[csf('cmd8')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd9[]" id="cmd9_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd9 = ($row[csf('cmd9')]!="")?$row[csf('cmd9')]:""; ?>">
								</td>
								<td width="4%">
									<input type="text" name="cmd10[]" id="cmd10_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd10 = ($row[csf('cmd10')]!="")?$row[csf('cmd10')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd11[]" id="cmd11_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd11 = ($row[csf('cmd11')]!="")?$row[csf('cmd11')]:""; ?>"/>
								</td>
								<td width="4%">
									<input type="text" name="cmd12[]" id="cmd12_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd12 = ($row[csf('cmd12')]!="")?$row[csf('cmd12')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd13[]" id="cmd13_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd13 = ($row[csf('cmd13')]!="")?$row[csf('cmd13')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd14[]" id="cmd14_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd14 = ($row[csf('cmd14')]!="")?$row[csf('cmd14')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd15[]" id="cmd15_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd15 = ($row[csf('cmd15')]!="")?$row[csf('cmd15')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd16[]" id="cmd16_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd16 = ($row[csf('cmd16')]!="")?$row[csf('cmd16')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd17[]" id="cmd17_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd17 = ($row[csf('cmd17')]!="")?$row[csf('cmd17')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd18[]" id="cmd18_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd18 = ($row[csf('cmd18')]!="")?$row[csf('cmd18')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd19[]" id="cmd19_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd19 = ($row[csf('cmd19')]!="")?$row[csf('cmd19')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd20[]" id="cmd20_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd20 = ($row[csf('cmd20')]!="")?$row[csf('cmd20')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd21[]" id="cmd21_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd21 = ($row[csf('cmd21')]!="")?$row[csf('cmd21')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd22[]" id="cmd22_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd22 = ($row[csf('cmd22')]!="")?$row[csf('cmd22')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd23[]" id="cmd23_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd23 = ($row[csf('cmd23')]!="")?$row[csf('cmd23')]:""; ?>"/>
								</td>
								<td width="4%"><input type="text" name="cmd24[]" id="cmd24_<? echo $i; ?>"
									class="text_boxes" style="width:70%; text-transform: uppercase;text-align: center;"
									value="<? echo $cmd24 = ($row[csf('cmd24')]!="")?$row[csf('cmd24')]:""; ?>"/>
								</td>
							</tr>
							<?
							$i++;
						}

						?>
					</tbody>
				</div>
				<table width="890" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

//-- cam design
if ($action == "needle_layout_info_popup")
{
	echo load_html_head_contents("Cam Design Information", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		function fnc_close()
		{
			var txt_dial = $('#txt_dial').val().toUpperCase().trim();
			var txt_cylinder = $('#txt_cylinder').val().toUpperCase().trim();

			var txt_dial_row1col1 = $('#txt_dial_row1col1').val().toUpperCase().trim();

			var txt_dial_row1col2 = $('#txt_dial_row1col2').val().toUpperCase().trim();
			var txt_dial_row1col3 = $('#txt_dial_row1col3').val().toUpperCase().trim();
			var txt_dial_row1col4 = $('#txt_dial_row1col4').val().toUpperCase().trim();
			var txt_dial_row1col5 = $('#txt_dial_row1col5').val().toUpperCase().trim();
			var txt_dial_row1col6 = $('#txt_dial_row1col6').val().toUpperCase().trim();
			var txt_dial_row2col1 = $('#txt_dial_row2col1').val().toUpperCase().trim();
			var txt_dial_row2col2 = $('#txt_dial_row2col2').val().toUpperCase().trim();
			var txt_dial_row2col3 = $('#txt_dial_row2col3').val().toUpperCase().trim();
			var txt_dial_row2col4 = $('#txt_dial_row2col4').val().toUpperCase().trim();
			var txt_dial_row2col5 = $('#txt_dial_row2col5').val().toUpperCase().trim();
			var txt_dial_row2col6 = $('#txt_dial_row2col6').val().toUpperCase().trim();

			var txt_no_of_feeder_col1 = $('#txt_no_of_feeder_col1').val().toUpperCase().trim();
			var txt_no_of_feeder_col2 = $('#txt_no_of_feeder_col2').val().toUpperCase().trim();
			var txt_no_of_feeder_col3 = $('#txt_no_of_feeder_col3').val().toUpperCase().trim();
			var txt_no_of_feeder_col4 = $('#txt_no_of_feeder_col4').val().toUpperCase().trim();
			var txt_no_of_feeder_col5 = $('#txt_no_of_feeder_col5').val().toUpperCase().trim();
			var txt_no_of_feeder_col6 = $('#txt_no_of_feeder_col6').val().toUpperCase().trim();

			var txt_cylinder_row1col1 = $('#txt_cylinder_row1col1').val().toUpperCase().trim();
			var txt_cylinder_row1col2 = $('#txt_cylinder_row1col2').val().toUpperCase().trim();
			var txt_cylinder_row1col3 = $('#txt_cylinder_row1col3').val().toUpperCase().trim();
			var txt_cylinder_row1col4 = $('#txt_cylinder_row1col4').val().toUpperCase().trim();
			var txt_cylinder_row1col5 = $('#txt_cylinder_row1col5').val().toUpperCase().trim();
			var txt_cylinder_row1col6 = $('#txt_cylinder_row1col6').val().toUpperCase().trim();

			var txt_cylinder_row2col1 = $('#txt_cylinder_row2col1').val().toUpperCase().trim();
			var txt_cylinder_row2col2 = $('#txt_cylinder_row2col2').val().toUpperCase().trim();
			var txt_cylinder_row2col3 = $('#txt_cylinder_row2col3').val().toUpperCase().trim();
			var txt_cylinder_row2col4 = $('#txt_cylinder_row2col4').val().toUpperCase().trim();
			var txt_cylinder_row2col5 = $('#txt_cylinder_row2col5').val().toUpperCase().trim();
			var txt_cylinder_row2col6 = $('#txt_cylinder_row2col6').val().toUpperCase().trim();

			var txt_cylinder_row3col1 = $('#txt_cylinder_row3col1').val().toUpperCase().trim();
			var txt_cylinder_row3col2 = $('#txt_cylinder_row3col2').val().toUpperCase().trim();
			var txt_cylinder_row3col3 = $('#txt_cylinder_row3col3').val().toUpperCase().trim();
			var txt_cylinder_row3col4 = $('#txt_cylinder_row3col4').val().toUpperCase().trim();
			var txt_cylinder_row3col5 = $('#txt_cylinder_row3col5').val().toUpperCase().trim();
			var txt_cylinder_row3col6 = $('#txt_cylinder_row3col6').val().toUpperCase().trim();

			var txt_cylinder_row4col1 = $('#txt_cylinder_row4col1').val().toUpperCase().trim();
			var txt_cylinder_row4col2 = $('#txt_cylinder_row4col2').val().toUpperCase().trim();
			var txt_cylinder_row4col3 = $('#txt_cylinder_row4col3').val().toUpperCase().trim();
			var txt_cylinder_row4col4 = $('#txt_cylinder_row4col4').val().toUpperCase().trim();
			var txt_cylinder_row4col5 = $('#txt_cylinder_row4col5').val().toUpperCase().trim();
			var txt_cylinder_row4col6 = $('#txt_cylinder_row4col6').val().toUpperCase().trim();

			var txt_yarn_ends_col1 = $('#txt_yarn_ends_col1').val().trim();
			var txt_yarn_ends_col2 = $('#txt_yarn_ends_col2').val().trim();
			var txt_yarn_ends_col3 = $('#txt_yarn_ends_col3').val().trim();
			var txt_yarn_ends_col4 = $('#txt_yarn_ends_col4').val().trim();
			var txt_yarn_ends_col5 = $('#txt_yarn_ends_col5').val().trim();

			var txt_lfa_col1 = $('#txt_lfa_col1').val().trim();
			var txt_lfa_col2 = $('#txt_lfa_col2').val().trim();
			var txt_lfa_col3 = $('#txt_lfa_col3').val().trim();
			var txt_lfa_col4 = $('#txt_lfa_col4').val().trim();
			var txt_lfa_col5 = $('#txt_lfa_col5').val().trim();

			var txt_yarn_tension_col1 = $('#txt_yarn_tension_col1').val().trim();
			var txt_yarn_tension_col2 = $('#txt_yarn_tension_col2').val().trim();
			var txt_yarn_tension_col3 = $('#txt_yarn_tension_col3').val().trim();
			var txt_yarn_tension_col4 = $('#txt_yarn_tension_col4').val().trim();
			var txt_yarn_tension_col5 = $('#txt_yarn_tension_col5').val().trim();

			var txt_grey_gsm = $('#txt_grey_gsm').val();
			var txt_tdry_weight = $('#txt_tdry_weight').val();
			var txt_tdry_width = $('#txt_tdry_width').val();
			var txt_rpm = $('#txt_rpm').val();
			var txt_froll_width = $('#txt_froll_width').val();
			var txt_laid_width = $('#txt_laid_width').val();

			var txt_active_feeder = $('#txt_active_feeder').val();
			var txt_rev_per_kg = $('#txt_rev_per_kg').val();
			var txt_dial_height = $('#txt_dial_height').val();

			// Multiple row col data
			var dial_row1 = txt_dial_row1col1+"__"+txt_dial_row1col2+"__"+txt_dial_row1col3+"__"+txt_dial_row1col4+"__"+txt_dial_row1col5+"__"+txt_dial_row1col6;
			var dial_row2 = txt_dial_row2col1+"__"+txt_dial_row2col2+"__"+txt_dial_row2col3+"__"+txt_dial_row2col4+"__"+txt_dial_row2col5+"__"+txt_dial_row2col6;

			var no_of_feeder = txt_no_of_feeder_col1+"__"+txt_no_of_feeder_col2+"__"+txt_no_of_feeder_col3+"__"+txt_no_of_feeder_col4+"__"+txt_no_of_feeder_col5+"__"+txt_no_of_feeder_col6;

			var cylinder_row1 = txt_cylinder_row1col1+"__"+txt_cylinder_row1col2+"__"+txt_cylinder_row1col3+"__"+txt_cylinder_row1col4+"__"+txt_cylinder_row1col5+"__"+txt_cylinder_row1col6;

			var cylinder_row2 = txt_cylinder_row2col1+"__"+txt_cylinder_row2col2+"__"+txt_cylinder_row2col3+"__"+txt_cylinder_row2col4+"__"+txt_cylinder_row2col5+"__"+txt_cylinder_row2col6;

			var cylinder_row3 = txt_cylinder_row3col1+"__"+txt_cylinder_row3col2+"__"+txt_cylinder_row3col3+"__"+txt_cylinder_row3col4+"__"+txt_cylinder_row3col5+"__"+txt_cylinder_row3col6;

			var cylinder_row4 = txt_cylinder_row4col1+"__"+txt_cylinder_row4col2+"__"+txt_cylinder_row4col3+"__"+txt_cylinder_row4col4+"__"+txt_cylinder_row4col5+"__"+txt_cylinder_row4col6;

			var yarn_ends = txt_yarn_ends_col1+"__"+txt_yarn_ends_col2+"__"+txt_yarn_ends_col3+"__"+txt_yarn_ends_col4+"__"+txt_yarn_ends_col5;

			var lfa = txt_lfa_col1+"__"+txt_lfa_col2+"__"+txt_lfa_col3+"__"+txt_lfa_col4+"__"+txt_lfa_col5;

			var yarn_tension = txt_yarn_tension_col1+"__"+txt_yarn_tension_col2+"__"+txt_yarn_tension_col3+"__"+txt_yarn_tension_col4+"__"+txt_yarn_tension_col5;

			$('#hidden_dial').val(txt_dial);
    		$('#hidden_cylinder').val(txt_cylinder);
    		$('#hidden_dial_row1').val(dial_row1);
    		$('#hidden_dial_row2').val(dial_row2);
    		$('#hidden_no_of_feeder').val(no_of_feeder);
    		$('#hidden_cylinder_row1').val(cylinder_row1);
    		$('#hidden_cylinder_row2').val(cylinder_row2);
    		$('#hidden_cylinder_row3').val(cylinder_row3);
    		$('#hidden_cylinder_row4').val(cylinder_row4);
    		$('#hidden_yarn_ends').val(yarn_ends);
    		$('#hidden_lfa').val(lfa);
    		$('#hidden_yarn_tension').val(yarn_tension);
    		$('#hidden_grey_gsm').val(txt_grey_gsm);
    		$('#hidden_tdry_weight').val(txt_tdry_weight);
    		$('#hidden_tdry_width').val(txt_tdry_width);
    		$('#hidden_rpm').val(txt_rpm);
    		$('#hidden_froll_width').val(txt_froll_width);
    		$('#hidden_laid_width').val(txt_laid_width);
    		$('#hidden_active_feeder').val(txt_active_feeder);
    		$('#hidden_rev_per_kg').val(txt_rev_per_kg);
    		$('#hidden_dial_height').val(txt_dial_height);

    		if(txt_dial!="" || txt_cylinder!="" || dial_row1!="" || dial_row2!="" || no_of_feeder!="" ||
    		cylinder_row1!="" || cylinder_row2!="" || cylinder_row3!="" || cylinder_row4!=""
    		|| yarn_ends!=""  || lfa!="" || yarn_tension!="" || txt_grey_gsm!="" || txt_tdry_weight!="" || txt_tdry_width!=""  || txt_rpm!="" || txt_froll_width!="" || txt_laid_width!=""
    		|| txt_active_feeder!="" || txt_rev_per_kg!="" || txt_dial_height!="" )
    		{
    			var data_have = 1;
    		}else{
    			var data_have = 0;
    		}

    		$('#needle_layout_data_have').val(data_have);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div style="width:780px;" align="left">
			<style type="text/css">
			    #needle-layout{
			        border-collapse:collapse;
			        table-layout:fixed;
			        width:600pt;
			        font-size: 20px;
			    }
			</style>
			<form name="searchwofrm" id="searchwofrm">

				<fieldset style="width:750px; margin-top:5px">
				<input type="hidden" id="hidden_dial" value="">
				<input type="hidden" id="hidden_cylinder" value="">
				<input type="hidden" id="hidden_dial_row1" value="">
				<input type="hidden" id="hidden_dial_row2" value="">
				<input type="hidden" id="hidden_no_of_feeder" value="">
				<input type="hidden" id="hidden_cylinder_row1" value="">
				<input type="hidden" id="hidden_cylinder_row2" value="">
				<input type="hidden" id="hidden_cylinder_row3" value="">
				<input type="hidden" id="hidden_cylinder_row4" value="">
				<input type="hidden" id="hidden_yarn_ends" value="">
				<input type="hidden" id="hidden_lfa" value="">
				<input type="hidden" id="hidden_yarn_tension" value="">
				<input type="hidden" id="hidden_grey_gsm" value="">
				<input type="hidden" id="hidden_tdry_weight" value="">
				<input type="hidden" id="hidden_tdry_width" value="">
				<input type="hidden" id="hidden_rpm" value="">
				<input type="hidden" id="hidden_froll_width" value="">
				<input type="hidden" id="hidden_laid_width" value="">
				<input type="hidden" id="hidden_active_feeder" value="">
				<input type="hidden" id="hidden_rev_per_kg" value="">
				<input type="hidden" id="hidden_dial_height" value="">

				<input type="hidden" id="needle_layout_data_have" value="">
				<input type="hidden" id="update_needle_layout_id" value="<? echo ($update_needle_layout_id!="")?str_replace("'", "", $update_needle_layout_id):""; ?>">

				<?
				$update_needle_layout_id = str_replace("'", "", $update_needle_layout_id);

				if($update_needle_layout_id>0)
				{
					$prog_no = str_replace("'", "", $prog_no);
					$plan_id = str_replace("'", "", $plan_id);

					$hidden_dial = trim(str_replace("'", "", $hidden_dial));
					$hidden_cylinder = trim(str_replace("'", "", $hidden_cylinder));
					$hidden_dial_row1 = trim(str_replace("'", "", $hidden_dial_row1));
					$hidden_dial_row2 = trim(str_replace("'", "", $hidden_dial_row2));
					$hidden_no_of_feeder = trim(str_replace("'", "", $hidden_no_of_feeder));
					$hidden_cylinder_row1 = trim(str_replace("'", "", $hidden_cylinder_row1));
					$hidden_cylinder_row2 = trim(str_replace("'", "", $hidden_cylinder_row2));
					$hidden_cylinder_row3 = trim(str_replace("'", "", $hidden_cylinder_row3));
					$hidden_cylinder_row4 = trim(str_replace("'", "", $hidden_cylinder_row4));
					$hidden_yarn_ends = trim(str_replace("'", "", $hidden_yarn_ends));
					$hidden_lfa = trim(str_replace("'", "", $hidden_lfa));
					$hidden_yarn_tension = trim(str_replace("'", "", $hidden_yarn_tension));
					$hidden_grey_gsm = trim(str_replace("'", "", $hidden_grey_gsm));
					$hidden_tdry_weight = trim(str_replace("'", "", $hidden_tdry_weight));
					$hidden_tdry_width = trim(str_replace("'", "", $hidden_tdry_width));
					$hidden_rpm = trim(str_replace("'", "", $hidden_rpm));
					$hidden_froll_width = trim(str_replace("'", "", $hidden_froll_width));
					$hidden_laid_width = trim(str_replace("'", "", $hidden_laid_width));
					$hidden_active_feeder = trim(str_replace("'", "", $hidden_active_feeder));
					$hidden_rev_per_kg = trim(str_replace("'", "", $hidden_rev_per_kg));
					$hidden_dial_height = trim(str_replace("'", "", $hidden_dial_height));

					if($hidden_dial_row1!="")
					{
						$dial_row1_data_arr = explode("__", $hidden_dial_row1);

						$txt_dial_row1col1 = ($dial_row1_data_arr[0]!="")?$dial_row1_data_arr[0]:"";
						$txt_dial_row1col2 = ($dial_row1_data_arr[1]!="")?$dial_row1_data_arr[1]:"";
						$txt_dial_row1col3 = ($dial_row1_data_arr[2]!="")?$dial_row1_data_arr[2]:"";
						$txt_dial_row1col4 = ($dial_row1_data_arr[3]!="")?$dial_row1_data_arr[3]:"";
						$txt_dial_row1col5 = ($dial_row1_data_arr[4]!="")?$dial_row1_data_arr[4]:"";
						$txt_dial_row1col6 = ($dial_row1_data_arr[5]!="")?$dial_row1_data_arr[5]:"";
					}

					if($hidden_dial_row2!="")
					{
						$dial_row2_data_arr = explode("__", $hidden_dial_row2);

						$txt_dial_row2col1 = ($dial_row2_data_arr[0]!="")?$dial_row2_data_arr[0]:"";
						$txt_dial_row2col2 = ($dial_row2_data_arr[1]!="")?$dial_row2_data_arr[1]:"";
						$txt_dial_row2col3 = ($dial_row2_data_arr[2]!="")?$dial_row2_data_arr[2]:"";
						$txt_dial_row2col4 = ($dial_row2_data_arr[3]!="")?$dial_row2_data_arr[3]:"";
						$txt_dial_row2col5 = ($dial_row2_data_arr[4]!="")?$dial_row2_data_arr[4]:"";
						$txt_dial_row2col6 = ($dial_row2_data_arr[5]!="")?$dial_row2_data_arr[5]:"";
					}

					if($hidden_no_of_feeder!="")
					{
						$no_of_feeder_data_arr = explode("__", $hidden_no_of_feeder);

						$txt_no_of_feeder_col1 = ($no_of_feeder_data_arr[0]!="")?$no_of_feeder_data_arr[0]:"";
						$txt_no_of_feeder_col2 = ($no_of_feeder_data_arr[1]!="")?$no_of_feeder_data_arr[1]:"";
						$txt_no_of_feeder_col3 = ($no_of_feeder_data_arr[2]!="")?$no_of_feeder_data_arr[2]:"";
						$txt_no_of_feeder_col4 = ($no_of_feeder_data_arr[3]!="")?$no_of_feeder_data_arr[3]:"";
						$txt_no_of_feeder_col5 = ($no_of_feeder_data_arr[4]!="")?$no_of_feeder_data_arr[4]:"";
						$txt_no_of_feeder_col6 = ($no_of_feeder_data_arr[5]!="")?$no_of_feeder_data_arr[5]:"";
					}

					if($hidden_cylinder_row1!="")
					{
						$cylinder_row1_data_arr = explode("__", $hidden_cylinder_row1);

						$txt_cylinder_row1col1 = ($cylinder_row1_data_arr[0]!="")?$cylinder_row1_data_arr[0]:"";
						$txt_cylinder_row1col2 = ($cylinder_row1_data_arr[1]!="")?$cylinder_row1_data_arr[1]:"";
						$txt_cylinder_row1col3 = ($cylinder_row1_data_arr[2]!="")?$cylinder_row1_data_arr[2]:"";
						$txt_cylinder_row1col4 = ($cylinder_row1_data_arr[3]!="")?$cylinder_row1_data_arr[3]:"";
						$txt_cylinder_row1col5 = ($cylinder_row1_data_arr[4]!="")?$cylinder_row1_data_arr[4]:"";
						$txt_cylinder_row1col6 = ($cylinder_row1_data_arr[5]!="")?$cylinder_row1_data_arr[5]:"";
					}

					if($hidden_cylinder_row2!="")
					{
						$cylinder_row2_data_arr = explode("__", $hidden_cylinder_row2);

						$txt_cylinder_row2col1 = ($cylinder_row2_data_arr[0]!="")?$cylinder_row2_data_arr[0]:"";
						$txt_cylinder_row2col2 = ($cylinder_row2_data_arr[1]!="")?$cylinder_row2_data_arr[1]:"";
						$txt_cylinder_row2col3 = ($cylinder_row2_data_arr[2]!="")?$cylinder_row2_data_arr[2]:"";
						$txt_cylinder_row2col4 = ($cylinder_row2_data_arr[3]!="")?$cylinder_row2_data_arr[3]:"";
						$txt_cylinder_row2col5 = ($cylinder_row2_data_arr[4]!="")?$cylinder_row2_data_arr[4]:"";
						$txt_cylinder_row2col6 = ($cylinder_row2_data_arr[5]!="")?$cylinder_row2_data_arr[5]:"";
					}

					if($hidden_cylinder_row3!="")
					{
						$cylinder_row3_data_arr = explode("__", $hidden_cylinder_row3);

						$txt_cylinder_row3col1 = ($cylinder_row3_data_arr[0]!="")?$cylinder_row3_data_arr[0]:"";
						$txt_cylinder_row3col2 = ($cylinder_row3_data_arr[1]!="")?$cylinder_row3_data_arr[1]:"";
						$txt_cylinder_row3col3 = ($cylinder_row3_data_arr[2]!="")?$cylinder_row3_data_arr[2]:"";
						$txt_cylinder_row3col4 = ($cylinder_row3_data_arr[3]!="")?$cylinder_row3_data_arr[3]:"";
						$txt_cylinder_row3col5 = ($cylinder_row3_data_arr[4]!="")?$cylinder_row3_data_arr[4]:"";
						$txt_cylinder_row3col6 = ($cylinder_row3_data_arr[5]!="")?$cylinder_row3_data_arr[5]:"";
					}

					if($hidden_cylinder_row4!="")
					{
						$cylinder_row4_data_arr = explode("__", $hidden_cylinder_row4);

						$txt_cylinder_row4col1 = ($cylinder_row4_data_arr[0]!="")?$cylinder_row4_data_arr[0]:"";
						$txt_cylinder_row4col2 = ($cylinder_row4_data_arr[1]!="")?$cylinder_row4_data_arr[1]:"";
						$txt_cylinder_row4col3 = ($cylinder_row4_data_arr[2]!="")?$cylinder_row4_data_arr[2]:"";
						$txt_cylinder_row4col4 = ($cylinder_row4_data_arr[3]!="")?$cylinder_row4_data_arr[3]:"";
						$txt_cylinder_row4col5 = ($cylinder_row4_data_arr[4]!="")?$cylinder_row4_data_arr[4]:"";
						$txt_cylinder_row4col6 = ($cylinder_row4_data_arr[5]!="")?$cylinder_row4_data_arr[5]:"";
					}

					if($hidden_yarn_ends!="")
					{
						$yarn_ends_data_arr = explode("__", $hidden_yarn_ends);

						$txt_yarn_ends_col1 = ($yarn_ends_data_arr[0]!="")?$yarn_ends_data_arr[0]:"";
						$txt_yarn_ends_col2 = ($yarn_ends_data_arr[1]!="")?$yarn_ends_data_arr[1]:"";
						$txt_yarn_ends_col3 = ($yarn_ends_data_arr[2]!="")?$yarn_ends_data_arr[2]:"";
						$txt_yarn_ends_col4 = ($yarn_ends_data_arr[3]!="")?$yarn_ends_data_arr[3]:"";
						$txt_yarn_ends_col5 = ($yarn_ends_data_arr[4]!="")?$yarn_ends_data_arr[4]:"";
					}

					if($hidden_lfa!="")
					{
						$lfa_data_arr = explode("__", $hidden_lfa);

						$txt_lfa_col1 = ($lfa_data_arr[0]!="")?$lfa_data_arr[0]:"";
						$txt_lfa_col2 = ($lfa_data_arr[1]!="")?$lfa_data_arr[1]:"";
						$txt_lfa_col3 = ($lfa_data_arr[2]!="")?$lfa_data_arr[2]:"";
						$txt_lfa_col4 = ($lfa_data_arr[3]!="")?$lfa_data_arr[3]:"";
						$txt_lfa_col5 = ($lfa_data_arr[4]!="")?$lfa_data_arr[4]:"";
					}

					if($hidden_yarn_tension!="")
					{
						$yarn_tension_data_arr = explode("__", $hidden_yarn_tension);

						$txt_yarn_tension_col1 = ($yarn_tension_data_arr[0]!="")?$yarn_tension_data_arr[0]:"";
						$txt_yarn_tension_col2 = ($yarn_tension_data_arr[1]!="")?$yarn_tension_data_arr[1]:"";
						$txt_yarn_tension_col3 = ($yarn_tension_data_arr[2]!="")?$yarn_tension_data_arr[2]:"";
						$txt_yarn_tension_col4 = ($yarn_tension_data_arr[3]!="")?$yarn_tension_data_arr[3]:"";
						$txt_yarn_tension_col5 = ($yarn_tension_data_arr[4]!="")?$yarn_tension_data_arr[4]:"";
					}
				}
				?>

				<div style="width:740px;" id="needle_list_view">

					<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  id="needle-layout">

					    <tr height='21'>
					        <td colspan='4' height='21' width='237' style='background-color: #00b0f0;'>Needle Layout</td>
					        <td colspan='4' rowspan='3' width='392' style='width:296pt;'>&nbsp;</td>
					    </tr>
					    <tr height='20'>
					        <td colspan="2" height='20' style='background-color: #ffcc00;'>Dial</td>
					        <td colspan="2" style='background-color: #ffff99;'>
					        	<input type="text" name="txt_dial" id="txt_dial" value="<? echo $txt_dial = ($hidden_dial!="")?$hidden_dial:""; ?>" class="text_boxes" style="width:92%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td colspan="2" height='20' style='background-color: #ffcc00;'>Cylinder</td>
					        <td colspan="2" style='background-color: #ffff99;'>
					        	<input type="text" name="txt_cylinder" id="txt_cylinder" value="<? echo $txt_cylinder = ($hidden_cylinder!="")?$hidden_cylinder:""; ?>" class="text_boxes" style="width:92%; text-transform: uppercase;" />
					        </td>
					    </tr>

					    <tr height='20'>
					        <td rowspan='7' style="vertical-align: middle;transform: rotate(270deg);">Cam Setting</td>
					        <td rowspan='2' align="center">Dial</td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row1col1" id="txt_dial_row1col1" value="<? echo $txt_dial_row1col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row1col2" id="txt_dial_row1col2" value="<? echo $txt_dial_row1col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row1col3" id="txt_dial_row1col3" value="<? echo $txt_dial_row1col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row1col4" id="txt_dial_row1col4" value="<? echo $txt_dial_row1col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row1col5" id="txt_dial_row1col5" value="<? echo $txt_dial_row1col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row1col6" id="txt_dial_row1col6" value="<? echo $txt_dial_row1col6;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>

					    <tr height='20'>
					        <td height='20' align="center">
					        	<input type="text" name="txt_dial_row2col1" id="txt_dial_row2col1" value="<? echo $txt_dial_row2col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td>
					        	<input type="text" name="txt_dial_row2col2" id="txt_dial_row2col2" value="<? echo $txt_dial_row2col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row2col3" id="txt_dial_row2col3" value="<? echo $txt_dial_row2col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row2col4" id="txt_dial_row2col4" value="<? echo $txt_dial_row2col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row2col5" id="txt_dial_row2col5" value="<? echo $txt_dial_row2col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_dial_row2col6" id="txt_dial_row2col6" value="<? echo $txt_dial_row2col6;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>

					    <tr height='20'>
					        <td height='20' style='text-align: center;'>No Of Feeder</td>
					        <td>
					        	<input type="text" name="txt_no_of_feeder_col1" id="txt_no_of_feeder_col1" value="<? echo $txt_no_of_feeder_col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_no_of_feeder_col2" id="txt_no_of_feeder_col2" value="<? echo $txt_no_of_feeder_col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_no_of_feeder_col3" id="txt_no_of_feeder_col3" value="<? echo $txt_no_of_feeder_col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_no_of_feeder_col4" id="txt_no_of_feeder_col4" value="<? echo $txt_no_of_feeder_col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_no_of_feeder_col5" id="txt_no_of_feeder_col5" value="<? echo $txt_no_of_feeder_col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_no_of_feeder_col6" id="txt_no_of_feeder_col6" value="<? echo $txt_no_of_feeder_col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td rowspan='4' style='vertical-align: middle; transform: rotate(270deg);'>Cylinder</td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row1col1" id="txt_cylinder_row1col1" value="<? echo $txt_cylinder_row1col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row1col2" id="txt_cylinder_row1col2" value="<? echo $txt_cylinder_row1col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row1col3" id="txt_cylinder_row1col3" value="<? echo $txt_cylinder_row1col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row1col4" id="txt_cylinder_row1col4" value="<? echo $txt_cylinder_row1col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>

					        <td align="center">
					        	<input type="text" name="txt_cylinder_row1col5" id="txt_cylinder_row1col5" value="<? echo $txt_cylinder_row1col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row1col6" id="txt_cylinder_row1col6" value="<? echo $txt_cylinder_row1col6;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row2col1" id="txt_cylinder_row2col1" value="<? echo $txt_cylinder_row2col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>

					        <td align="center">
					        	<input type="text" name="txt_cylinder_row2col2" id="txt_cylinder_row2col2" value="<? echo $txt_cylinder_row2col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row2col3" id="txt_cylinder_row2col3" value="<? echo $txt_cylinder_row2col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row2col4" id="txt_cylinder_row2col4" value="<? echo $txt_cylinder_row2col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row2col5" id="txt_cylinder_row2col5" value="<? echo $txt_cylinder_row2col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row2col6" id="txt_cylinder_row2col6" value="<? echo $txt_cylinder_row2col6;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row3col1" id="txt_cylinder_row3col1" value="<? echo $txt_cylinder_row3col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>

					        <td align="center">
					        	<input type="text" name="txt_cylinder_row3col2" id="txt_cylinder_row3col2" value="<? echo $txt_cylinder_row3col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row3col3" id="txt_cylinder_row3col3" value="<? echo $txt_cylinder_row3col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row3col4" id="txt_cylinder_row3col4" value="<? echo $txt_cylinder_row3col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row3col5" id="txt_cylinder_row3col5" value="<? echo $txt_cylinder_row3col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row3col6" id="txt_cylinder_row3col6" value="<? echo $txt_cylinder_row3col6;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row4col1" id="txt_cylinder_row4col1" value="<? echo $txt_cylinder_row4col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row4col2" id="txt_cylinder_row4col2" value="<? echo $txt_cylinder_row4col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row4col3" id="txt_cylinder_row4col3" value="<? echo $txt_cylinder_row4col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row4col4" id="txt_cylinder_row4col4" value="<? echo $txt_cylinder_row4col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row4col5" id="txt_cylinder_row4col5" value="<? echo $txt_cylinder_row4col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_cylinder_row4col6" id="txt_cylinder_row4col6" value="<? echo $txt_cylinder_row4col6;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td colspan='3' height='21' style='background-color: #c2d69a;'>Yarn Ends</td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_ends_col1" id="txt_yarn_ends_col1" value="<? echo $txt_yarn_ends_col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_ends_col2" id="txt_yarn_ends_col2" value="<? echo $txt_yarn_ends_col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_ends_col3" id="txt_yarn_ends_col3" value="<? echo $txt_yarn_ends_col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_ends_col4" id="txt_yarn_ends_col4" value="<? echo $txt_yarn_ends_col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_ends_col5" id="txt_yarn_ends_col5" value="<? echo $txt_yarn_ends_col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td colspan='3' height='21' style='background-color: #75923c;'>LFA</td>
					        <td align="center">
					        	<input type="text" name="txt_lfa_col1" id="txt_lfa_col1" value="<? echo $txt_lfa_col1;?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_lfa_col2" id="txt_lfa_col2" value="<? echo $txt_lfa_col2;?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_lfa_col3" id="txt_lfa_col3" value="<? echo $txt_lfa_col3;?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_lfa_col4" id="txt_lfa_col4" value="<? echo $txt_lfa_col4;?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_lfa_col5" id="txt_lfa_col5" value="<? echo $txt_lfa_col5;?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>
					        <td colspan='3' height='21' style='background-color: #ccc0da;'>Yarn Tension</td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_tension_col1" id="txt_yarn_tension_col1" value="<? echo $txt_yarn_tension_col1;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_tension_col2" id="txt_yarn_tension_col2" value="<? echo $txt_yarn_tension_col2;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_tension_col3" id="txt_yarn_tension_col3" value="<? echo $txt_yarn_tension_col3;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_tension_col4" id="txt_yarn_tension_col4" value="<? echo $txt_yarn_tension_col4;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td align="center">
					        	<input type="text" name="txt_yarn_tension_col5" id="txt_yarn_tension_col5" value="<? echo $txt_yarn_tension_col5;?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>
					    <tr height='20'>

					        <td colspan='3' height='20' style="background-color: #31849b;">Grey GSM ( gm/m2)</td>
					        <td align="center">
					        	<input type="text" name="txt_grey_gsm" id="txt_grey_gsm" value="<? echo $txt_grey_gsm = ($hidden_grey_gsm!="")?$hidden_grey_gsm:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td style="background-color: #b8cce4;">T.Dry Weight ( gm/m2)</td>
					        <td align="center">
					        	<input type="text" name="txt_tdry_weight" id="txt_tdry_weight" value="<? echo $txt_tdry_weight = ($hidden_tdry_weight!="")?$hidden_tdry_weight:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td style="background-color: #bfbfbf;">T.Dry Width</td>
					        <td align="center">
					        	<input type="text" name="txt_tdry_width" id="txt_tdry_width" value="<? echo $txt_tdry_width = ($hidden_tdry_width!="")?$hidden_tdry_width:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>

					    <tr height='20'>
					        <td colspan='3' height='21' style="background-color: #93cddd;">RPM</td>
					        <td align="center">
					        	<input type="text" name="txt_rpm" id="txt_rpm" value="<? echo $txt_rpm = ($hidden_rpm!="")?$hidden_rpm:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td style="background-color: #538ed5;">F.Roll Width</td>
					        <td align="center">
					        	<input type="text" name="txt_froll_width" id="txt_froll_width" value="<? echo $txt_froll_width = ($hidden_froll_width!="")?$hidden_froll_width:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td style="background-color: #a5a5a5;">Laid Width</td>
					        <td align="center">
					        	<input type="text" name="txt_laid_width" id="txt_laid_width" value="<? echo $txt_laid_width = ($hidden_laid_width!="")?$hidden_laid_width:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>

					    <tr height='20'>
					        <td colspan='3' height='21' style="background-color: #93cddd;">Total Active Feeder</td>
					        <td align="center">
					        	<input type="text" name="txt_active_feeder" id="txt_active_feeder" value="<? echo $txt_active_feeder = ($hidden_active_feeder!="")?$hidden_active_feeder:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td style="background-color: #538ed5;">Rev per Kg</td>
					        <td align="center">
					        	<input type="text" name="txt_rev_per_kg" id="txt_rev_per_kg" value="<? echo $txt_rev_per_kg = ($hidden_rev_per_kg!="")?$hidden_rev_per_kg:""; ?>" class="text_boxes_numeric" style="width:84%; text-transform: uppercase;" />
					        </td>
					        <td style="background-color: #a5a5a5;">Dial Height</td>
					        <td align="center">
					        	<input type="text" name="txt_dial_height" id="txt_dial_height" value="<? echo $txt_dial_height = ($hidden_dial_height!="")?$hidden_dial_height:""; ?>" class="text_boxes" style="width:84%; text-transform: uppercase;" />
					        </td>
					    </tr>

					</table>

				</div>
				<table width="860" id="tbl_close">
					<tr>
						<td colspan="4" align="center" class="button_container">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "planning_info_details")
{
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	//$sql = "select id, knitting_source, knitting_party, color_range, machine_dia, machine_gg, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, status, program_date, color_id, tube_ref_no from ppl_planning_info_entry_dtls where mst_id in ($data) and status_active=1 and is_deleted=0";


	$sql = "select a.id, a.knitting_source, a.knitting_party, a.color_range, a.machine_dia, a.machine_gg, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.status, a.program_date, a.color_id, a.tube_ref_no, listagg(b.sales_order_dtls_ids || '-' || coalesce(b.body_part_id, 0), '__')
	within group ( order by a.id )  as salesDtlsId_wise_bodyPartId
	from ppl_planning_info_entry_dtls a,PPL_PLANNING_ENTRY_PLAN_DTLS b where
	a.mst_id=b.mst_id and a.id=b.dtls_id and a.mst_id in ($data) and a.status_active=1 and a.is_deleted=0 and a.is_revised=0 and b.is_revised=0 group by a.id, a.knitting_source, a.knitting_party, a.color_range, a.machine_dia, a.machine_gg, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.status, a.program_date, a.color_id, a.tube_ref_no";
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1110" class="rpt_table">
		<thead>
			<th width="90">Knitting Source</th>
			<th width="100">Knitting Company</th>
			<th width="60">Program No</th>
			<th width="60">Tube/Ref. No</th>
			<th width="90">Color</th>
			<th width="90">Color Range</th>
			<th width="70">Machine Dia</th>
			<th width="70">Machine GG</th>
			<th width="80">Program Qnty</th>
			<th width="75">Stitch Length</th>
			<th width="80">Span. Stitch Length</th>
			<th width="70">Draft Ratio</th>
			<th width="75">Program Date</th>
			<th>Status</th>
		</thead>
	</table>
	<div style="width:1110px; max-height:140px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$colors = "";
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			if ($row[csf('knitting_source')] == 1)
				$knit_party = $company_arr[$row[csf('knitting_party')]];
			else
				$knit_party = $supllier_arr[$row[csf('knitting_party')]];

			//for color name
			$color_name=explode(",",$row[csf('color_id')]);
			$color_id = array();
			foreach ($color_name as $val)
			{
				if($val>0)
					$color_id[$color_library[$val]] = $color_library[$val];
			}
			$color=implode(', ', $color_id);
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data('<? echo $row[csf('id')]."**".$row[csf('salesDtlsId_wise_bodyPartId')]; ?>','populate_data_from_planning_info', 'planning_info_entry_for_sales_order_controller' );balance_cal();setFieldLevelAccess('<?= $row[csf('knitting_party')];?>');">
				<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
				<td width="100"><p><? echo $knit_party; ?></p></td>
				<td width="60"><p><? echo $row[csf('id')]; ?></p></td>
				<td width="60"><p><? echo $row[csf('tube_ref_no')]; ?></p></td>
				<td width="90"><p><? echo $color; ?></p></td>
				<td width="90"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
				<td width="70"><p><? echo $row[csf('machine_dia')]; ?></p></td>
				<td width="70"><? echo $row[csf('machine_gg')]; ?></td>
				<td width="80" align="right"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
				<td width="75"><p><? echo $row[csf('stitch_length')]; ?></p></td>
				<td width="80"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
				<td width="70" align="right"><? echo number_format($row[csf('draft_ratio')], 2); ?></td>
				<td width="75" align="right"><? echo change_date_format($row[csf('program_date')]); ?></td>
				<td><p><? echo $knitting_program_status[$row[csf('status')]]; ?></p></td>
			</tr>
			<?
			$colors .= $row[csf('color_id')] . ",";
			$i++;
		}
		?>
	</table>
	<input type="hidden" id="txt_hdn_colors" value="<?php echo $colors; ?>"/>
</div>
<?
exit();
}

if ($action == "populate_data_from_planning_info")
{


	$expData = explode('**', $data);
	// print_r($expData);die;
	$data = $expData[0];
	$salesDtlsId_wise_bodyPartId = $expData[1];
	// $companyID = $expData[2];

	// echo "field_lavel_access(".$companyID .");\n";

	//echo "document.getElementById('knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";

	$salIDSs="";$bodyPartiDSs="";
	$salesDtlsIdsArrss=array();$bodypartArrss=array();$salesDtlsId_wise_bodyPartIdArr=array();
	$salesDtlsIdsArr=explode('__', $salesDtlsId_wise_bodyPartId);
	foreach ($salesDtlsIdsArr as $keys => $salesIDS) {
		$salesDtlsIdsArrs=explode('-', $salesIDS);
		$salesDtlsIdsArrss[]=$salesDtlsIdsArrs[0];
		$bodypartArrss[]=$salesDtlsIdsArrs[1];

		$salesDtlsId_wise_bodyPartIdArr[$salesIDS]=$salesIDS;
	}
	foreach ($salesDtlsIdsArrss as $salIDS) {
			 $salIDSs.=$salIDS.",";
	}
	foreach ($bodypartArrss as $bodyDS) {
			 $bodyPartiDSs.=$bodyDS.",";
	}
	$salIDSs=chop($salIDSs,",");
	$bodyPartiDSs=chop($bodyPartiDSs,",");

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

	$sql_count_feed = "select seq_no,count_id,feeding_id,prod_id,prod_desc from ppl_planning_count_feed_dtls where dtls_id=$data  order by seq_no ";
	$data_array_count_feed = sql_select($sql_count_feed);
	foreach ($data_array_count_feed as $row) {
		$count_feeding_data_arr[]=$row[csf('seq_no')].'_'.$row[csf('count_id')].'_'.$row[csf('feeding_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('prod_desc')];
	}
	$count_feeding_data_arr_str=implode(',',$count_feeding_data_arr);

	$sql = "select id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, upper(fabric_dia) as fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, no_fo_feeder_data, location_id, advice, batch_no, no_of_ply, tube_ref_no from ppl_planning_info_entry_dtls where id=$data";

	$data_array = sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_knitting_source').value 			= '" . $row[csf("knitting_source")] . "';\n";
		echo "load_drop_down('planning_info_entry_for_sales_order_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("knitting_party")] . "+'**1', 'load_drop_down_knitting_party_new','knitting_party');\n";

		$color = '';
		$color_id = explode(",", $row[csf("color_id")]);
		foreach ($color_id as $val) {
			if ($color == "") $color = $color_library[$val]; else $color .= "," . $color_library[$val];
		}

		echo "document.getElementById('knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";

		echo "load_drop_down('planning_info_entry_for_sales_order_controller', " . $row[csf("knitting_party")] . ", 'load_drop_down_location','location_td');\n";

		echo "document.getElementById('txt_color').value 					= '" . $color . "';\n";
		echo "document.getElementById('hidden_color_id').value 				= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('hidden_color_wise_total').value 		= '" . $row[csf("program_qnty")] . "';\n";
		echo "document.getElementById('cbo_color_range').value 				= '" . $row[csf("color_range")] . "';\n";
		echo "document.getElementById('txt_machine_dia').value 				= '" . $row[csf("machine_dia")] . "';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '" . $row[csf("width_dia_type")] . "';\n";
		echo "document.getElementById('txt_machine_gg').value 				= '" . $row[csf("machine_gg")] . "';\n";
		echo "document.getElementById('txt_fabric_dia').value 				= '" . $row[csf("fabric_dia")] . "';\n";
		echo "document.getElementById('txt_program_qnty').value 			= '" . $row[csf("program_qnty")] . "';\n";
		echo "document.getElementById('txt_stitch_length').value 			= '" . $row[csf("stitch_length")] . "';\n";
		echo "document.getElementById('txt_spandex_stitch_length').value 	= '" . $row[csf("spandex_stitch_length")] . "';\n";
		echo "document.getElementById('txt_draft_ratio').value 				= '" . $row[csf("draft_ratio")] . "';\n";
		echo "document.getElementById('txt_batch_no').value 				= '" . $row[csf("batch_no")] . "';\n";
		echo "document.getElementById('txt_no_of_ply').value 				= '" . $row[csf("no_of_ply")] . "';\n";
		echo "document.getElementById('txt_tube_ref_no').value 				= '" . $row[csf("tube_ref_no")] . "';\n";

		echo "active_inactive();\n";

		echo "document.getElementById('machine_id').value 					= '" . $row[csf("machine_id")] . "';\n";
		$machine_ids = $row[csf("machine_id")];
		$machine_no = '';
		$machine_id = explode(",", $row[csf("machine_id")]);
		foreach ($machine_id as $val) {
			if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
		}

		echo "document.getElementById('txt_machine_no').value 				= '" . $machine_no . "';\n";
		echo "document.getElementById('txt_machine_capacity').value 		= '" . $row[csf("machine_capacity")] . "';\n";
		echo "document.getElementById('txt_distribution_qnty').value 		= '" . $row[csf("distribution_qnty")] . "';\n";

		echo "document.getElementById('cbo_knitting_status').value 			= '" . $row[csf("status")] . "';\n";
		echo "document.getElementById('txt_start_date').value 				= '" . change_date_format($row[csf("start_date")]) . "';\n";
		echo "document.getElementById('txt_end_date').value 				= '" . change_date_format($row[csf("end_date")]) . "';\n";
		echo "document.getElementById('txt_program_date').value 			= '" . change_date_format($row[csf("program_date")]) . "';\n";
		echo "document.getElementById('cbo_feeder').value 					= '" . $row[csf("feeder")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";

		if($machine_ids!="")
		{
			$save_data = '';
			$data_machine_array = sql_select("select id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date from ppl_planning_info_machine_dtls where dtls_id='$data' and machine_id in($machine_ids) and status_active=1 and is_deleted=0");
			foreach ($data_machine_array as $row_m) {
				$start_date = change_date_format($row_m[csf("start_date")]);
				$end_date = change_date_format($row_m[csf("end_date")]);

				if ($save_data == "") {
					$save_data = $row_m[csf("machine_id")] . "_" . $row_m[csf("dia")] . "_" . $row_m[csf("capacity")] . "_" . $row_m[csf("distribution_qnty")] . "_" . $row_m[csf("no_of_days")] . "_" . $start_date . "_" . $end_date . "_" . $row_m[csf("id")];
				} else {
					$save_data .= "," . $row_m[csf("machine_id")] . "_" . $row_m[csf("dia")] . "_" . $row_m[csf("capacity")] . "_" . $row_m[csf("distribution_qnty")] . "_" . $row_m[csf("no_of_days")] . "_" . $start_date . "_" . $end_date . "_" . $row_m[csf("id")];
				}
			}
		}

		$str = '';
		$data_machine_array = sql_select("select id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder,sequence from ppl_planning_feeder_dtls where dtls_id='$data' and status_active=1 and is_deleted=0 order by pre_cost_id, color_id, stripe_color_id asc");
		$incrementNo=1;
		foreach ($data_machine_array as $row_m) {
			if ($str == '') $str = $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")]. "_" .$incrementNo . "_" . $row_m[csf("sequence")];
			else   $str .= "," . $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")]. "_" .$incrementNo. "_" . $row_m[csf("sequence")];
			$incrementNo++;
		}
		echo "document.getElementById('hidden_no_of_feeder_data').value 					= '" . $str . "';\n";//$row[csf("save_data")]
		echo "document.getElementById('save_data').value 					= '" . $save_data . "';\n";//$row[csf("save_data")]
		echo "document.getElementById('cbo_location_name').value 			= '" . $row[csf("location_id")] . "';\n";
		$advice = str_replace("\n","\\n",$row[csf("advice")]); //die();
		echo "document.getElementById('hidden_advice_data').value 			= '" . $advice . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_program_no').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('hidden_count_feeding_data').value	= '" .$count_feeding_data_arr_str. "';\n";
		echo "days_req();\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_program_entry',1);\n";

		//for collar cuff
		$str_collar = '';
		$data_collar_cuff = sql_select("select id, mst_id, dtls_id, body_part_id, grey_size,finish_size, qty_pcs, needle_per_cm from ppl_planning_collar_cuff_dtls where dtls_id='$data' and status_active=1 and is_deleted=0 order by finish_size asc");
		foreach ($data_collar_cuff as $row_collar)
		{
			if ($str_collar == '')
				$str_collar = $row_collar[csf("body_part_id")] . "_" . $row_collar[csf("grey_size")] . "_" . $row_collar[csf("finish_size")] . "_" . $row_collar[csf("qty_pcs")] . "_" . $row_collar[csf("needle_per_cm")];
			else
				$str_collar .= "," . $row_collar[csf("body_part_id")] . "_" . $row_collar[csf("grey_size")] . "_" . $row_collar[csf("finish_size")] . "_" . $row_collar[csf("qty_pcs")] . "_" . $row_collar[csf("needle_per_cm")];
		}
		echo "document.getElementById('hidden_collarCuff_data').value 		= '" . $str_collar . "';\n";//$row[csf("save_data")]
	}

	$sql_needle = "select id, plan_id, program_no, dial, cylinder, dial_row1, dial_row2, no_of_feeder, cylinder_row1, cylinder_row2, cylinder_row3, cylinder_row4, yarn_ends, lfa, yarn_tension, grey_gsm, t_dry_weight, t_dry_width, rpm, f_roll_width, laid_width,active_feeder, rev_per_kg, dial_height from ppl_planning_needle_layout where program_no = $data";

	$sql_needle_data = sql_select($sql_needle);

	foreach ($sql_needle_data as $row)
	{
		echo "document.getElementById('prog_hidden_dial').value 	= '" . $row[csf("dial")] . "';\n";
		echo "document.getElementById('prog_hidden_cylinder').value 	= '" . $row[csf("cylinder")] . "';\n";
		echo "document.getElementById('prog_hidden_dial_row1').value 	= '" . $row[csf("dial_row1")] . "';\n";
		echo "document.getElementById('prog_hidden_dial_row2').value 	= '" . $row[csf("dial_row2")] . "';\n";
		echo "document.getElementById('prog_hidden_no_of_feeder').value 	= '" . $row[csf("no_of_feeder")] . "';\n";
		echo "document.getElementById('prog_hidden_cylinder_row1').value 	= '" . $row[csf("cylinder_row1")] . "';\n";
		echo "document.getElementById('prog_hidden_cylinder_row2').value 	= '" . $row[csf("cylinder_row2")] . "';\n";
		echo "document.getElementById('prog_hidden_cylinder_row3').value 	= '" . $row[csf("cylinder_row3")] . "';\n";
		echo "document.getElementById('prog_hidden_cylinder_row4').value 	= '" . $row[csf("cylinder_row4")] . "';\n";
		echo "document.getElementById('prog_hidden_yarn_ends').value 	= '" . $row[csf("yarn_ends")] . "';\n";
		echo "document.getElementById('prog_hidden_lfa').value 	= '" . $row[csf("lfa")] . "';\n";
		echo "document.getElementById('prog_hidden_yarn_tension').value 	= '" . $row[csf("yarn_tension")] . "';\n";
		echo "document.getElementById('prog_hidden_grey_gsm').value 	= '" . $row[csf("grey_gsm")] . "';\n";
		echo "document.getElementById('prog_hidden_tdry_weight').value 	= '" . $row[csf("t_dry_weight")] . "';\n";
		echo "document.getElementById('prog_hidden_tdry_width').value 	= '" . $row[csf("t_dry_width")] . "';\n";
		echo "document.getElementById('prog_hidden_rpm').value 	= '" . $row[csf("rpm")] . "';\n";
		echo "document.getElementById('prog_hidden_froll_width').value 	= '" . $row[csf("f_roll_width")] . "';\n";
		echo "document.getElementById('prog_hidden_laid_width').value 	= '" . $row[csf("laid_width")] . "';\n";
		echo "document.getElementById('prog_hidden_active_feeder').value 	= '" . $row[csf("active_feeder")] . "';\n";
		echo "document.getElementById('prog_hidden_rev_per_kg').value 	= '" . $row[csf("rev_per_kg")] . "';\n";
		echo "document.getElementById('prog_hidden_dial_height').value 	= '" . $row[csf("dial_height")] . "';\n";
		echo "document.getElementById('prog_update_needle_layout_id').value 	= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('prog_needle_layout_data_have').value 	= '1';\n";
	}

	//for booking qty
	//$sql = "SELECT color_id AS COLOR_ID, sum(grey_qty) AS QTY FROM fabric_sales_order_dtls WHERE status_active=1 AND is_deleted=0 AND mst_id=".$job_id." AND id IN(".$sales_dtls_id.") AND body_part_id IN(".$body_part_id.") AND trim(fabric_desc) = '".$desc."' AND dia = '".$dia."' AND gsm_weight = '".$gsm."' AND width_dia_type = '".$width_dia_type."' GROUP BY color_id";

	//$sqlBookingQty = "SELECT b.color_id AS COLOR_ID, SUM(b.grey_qty) AS QTY FROM ppl_planning_entry_plan_dtls a, fabric_sales_order_dtls b WHERE a.po_id = b.mst_id AND a.sales_order_dtls_ids = b.id AND a.dtls_id = ".$data." GROUP BY b.color_id";


	/*$salesDtlsId_wise_bodyPartIdArr = explode("__", $salesDtlsId_wise_bodyPartId);
	foreach ($salesDtlsId_wise_bodyPartIdArr as $key => $value) {
		$salesDtlsId_wise_bodyPartIdArr[$value]=$value;
	}
		*/


	$sql_primary = sql_select("SELECT b.color_id AS COLOR_ID, SUM(b.grey_qty) AS QTY ,  a.sales_order_dtls_ids,b.body_part_id
	FROM ppl_planning_entry_plan_dtls a, fabric_sales_order_dtls b
	WHERE a.po_id = b.mst_id and b.id in($salIDSs)  AND b.body_part_id IN($bodyPartiDSs) AND a.dtls_id = $data GROUP BY b.color_id, a.sales_order_dtls_ids,b.body_part_id");
	$po_color_bookingReqQnty=array();
	foreach ($sql_primary as $row)
	{
		if($salesDtlsId_wise_bodyPartIdArr[$row[csf('sales_order_dtls_ids')]."-".$row[csf('body_part_id')]]==$row[csf('sales_order_dtls_ids')]."-".$row[csf('body_part_id')])
		{
			$po_color_bookingReqQnty[$row[csf('color_id')]]["qnty"] +=  $row[csf('QTY')];

		}
	}

	/*echo "<pre>";
	print_r($po_color_bookingReqQnty);*/

	$sqlBookingQty = "SELECT b.color_id AS COLOR_ID, SUM(b.grey_qty) AS QTY FROM ppl_planning_entry_plan_dtls a, fabric_sales_order_dtls b WHERE a.po_id = b.mst_id and b.id in($salIDSs) AND a.dtls_id = ".$data." AND b.body_part_id IN($bodyPartiDSs) GROUP BY b.color_id";


	$dataBookingQty = sql_select($sqlBookingQty);
	$bookingQtyArr = array();
	foreach($dataBookingQty as $zasu)
	{
		//$bookingQtyArr[$zasu['COLOR_ID']] = $zasu['QTY'];
		$bookingQtyArr[$zasu[csf("COLOR_ID")]] = $po_color_bookingReqQnty[$zasu[csf('COLOR_ID')]]["qnty"];
	}

	//for color
	$sql_color_prog = "SELECT id AS ID, plan_id AS PLAN_ID, program_no AS PROGRAM_NO, color_id AS COLOR_ID, color_prog_qty AS COLOR_PROG_QTY,size_wise_prog_string as SIZE_WISE_PROG_STRING FROM ppl_color_wise_break_down WHERE program_no = ".$data." AND status_active=1 AND is_deleted=0";
	$color_prog_data = sql_select($sql_color_prog);


	$colorTableId="";$sizeStrings=array();
	foreach ($color_prog_data as $colorRows)
	{
		$colorTableId.= $colorRows[csf("color_id")].",";
		$sizeStrings[]=explode("##", $colorRows["SIZE_WISE_PROG_STRING"]);
	}
	$sizeString=array();

	foreach ($sizeStrings as $keyData => $colorRowss)
	{
		foreach ($colorRowss as $keyDatas => $colorRowsss)
		{
			$sizeString[]=$colorRowsss;
		}
	}

	/*echo "<pre>";
	print_r($sizeString); die;*/

	$colorTableId=chop($colorTableId,",");
	//$sql_size_prog = sql_select("select id from ppl_size_wise_break_down where program_no = $data and color_id in($colorTableId) and status_active=1 and is_deleted=0 order by id asc");
	$sql_size_prog = sql_select("select a.id,a.color_id from ppl_size_wise_break_down a where a.program_no = $data and a.color_id in($colorTableId) and a.status_active=1 and a.is_deleted=0 order by a.id asc");
	$sizeIndex=0;
	$sizeStrings="";
	foreach ($sql_size_prog as $sizeRow)
	{
		//echo $sizeRow[csf("id")]."stringxxxxxxxxxx";

		$arrColWiseSizeString[$sizeRow[csf("color_id")]].=$sizeString[$sizeIndex]."_".$sizeRow[csf("id")]."##";
		$sizeStrings.=$sizeString[$sizeIndex]."_".$sizeRow[csf("id")]."##";
		$sizeIndex++;
	}
	$sizeStrings=chop($sizeStrings,"##");


	foreach ($arrColWiseSizeString as $keyColor => $valueString) {
		$colorWiseSizeString= chop($valueString,"##");
		$arrColorWiseSizeString[$keyColor]=  $colorWiseSizeString;
	}


	/*echo "<pre>";
	print_r($arrColorWiseSizeString); die;
	echo $sizeStrings."<br/>"; die;*/


	if(count($color_prog_data>0))
	{
		$saveString = "";
		$saveStringForSizeWise = "";
		$totalProgQty = 0;
		foreach ($color_prog_data as $colorRow)
		{
			if($saveString=="")
			{
				$saveString =  $colorRow['COLOR_ID'] . "_" . $colorRow['COLOR_PROG_QTY']. "_" . $colorRow['ID']. "_" . $bookingQtyArr[$colorRow['COLOR_ID']];
				//$saveStringForSizeWise = $colorRow["SIZE_WISE_PROG_STRING"];
				//$saveStringForSizeWise = $sizeStrings;
				$saveStringForSizeWise = $arrColorWiseSizeString[$colorRow['COLOR_ID']];
			}
			else
			{
				$saveString .= "," . $colorRow['COLOR_ID'] . "_" . $colorRow['COLOR_PROG_QTY']. "_" . $colorRow['ID']. "_" . $bookingQtyArr[$colorRow['COLOR_ID']];
				//$saveStringForSizeWise .= "**" . $colorRow["SIZE_WISE_PROG_STRING"];
				//$saveStringForSizeWise .= "**" . $sizeStrings;
				$saveStringForSizeWise .= "**" . $arrColorWiseSizeString[$colorRow['COLOR_ID']];
			}

			$totalProgQty += $colorRow['COLOR_PROG_QTY'];
		}
		echo "document.getElementById('hidden_color_wise_prog_data').value 	= '" . $saveString . "';\n";
		echo "document.getElementById('hidden_size_wise_prog_string').value 	= '" . $saveStringForSizeWise . "';\n";
	}

	exit();
}

if ($action == "count_feeding_data_popup")
{
	echo load_html_head_contents("Count Feeding", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function add_break_down_tr(i) {
			var row_num = $('#tbl_list_search tr').length;
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {

				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

			}).end();

			$("#tr_" + i).after(clone);
			//$('#txtSeqNo_' + row_num).removeAttr("value").attr("value", row_num);
			$('#cboCount_' + row_num).removeAttr("value").attr("value", 0);
			$('#cboFeeding_' + row_num).removeAttr("value").attr("value", 0);
			$('#txtDesc_' + row_num).removeAttr("value").attr("value", "");
			$('#txtDesc_' + row_num).removeAttr("placeholder").attr("placeholder", "Browse");

			var salesID=$('#txtsalesID_' + row_num).val();
			$('#txtDesc_' + row_num).removeAttr("ondblclick").attr("ondblclick", "browse_desc_popup(" + row_num + ","+salesID+");");

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			reArrangeSl();
			set_all_onclick();
		}

		function reArrangeSl() {
			var i = 0;
			$("#tbl_list_search").find('tbody tr').each(function () {
				i++;
				$(this).find("td:eq(0)").text(i);
				$(this).find("td:eq(1) input").val(i);
			});
		}

		function fn_deleteRow(rowNo) {
			if (rowNo != 1) {
				$("#tr_" + rowNo).remove();
				reArrangeSl();
			}
		}

		function fnc_close() {
			var save_string = "";
			var breakOut = true;
			$("#tbl_list_search").find('tbody tr').each(function () {
				if (breakOut == false) {
					return;
				}

				var txtSeqNo = $(this).find('input[name="txtSeqNo[]"]').val();
				var cboCount = $(this).find('select[name="cboCount[]"]').val();
				var cboFeeding = $(this).find('select[name="cboFeeding[]"]').val();
				var txtProdId = $(this).find('input[name="txtProdId[]"]').val();
				var txtProdDesc = $(this).find('input[name="txtDesc[]"]').val();


				if (save_string == "") {
					save_string = txtSeqNo + "_" + cboCount + "_" + cboFeeding+ "_" + txtProdId+ "_" + txtProdDesc;
				}
				else {
					save_string += "," + txtSeqNo + "_" + cboCount + "_" + cboFeeding+ "_" + txtProdId+ "_" + txtProdDesc;
				}


			});

			if (breakOut == false) {
				return;
			}

			$('#hidden_count_feeding_data').val(save_string);
			parent.emailwindow.hide();
		}


		function browse_desc_popup(sl,salesId) {
			//var companyID = $("#cbo_company_name").val();
			//var buyerID = $("#cbo_buyer_name").val();
			var page_link = 'planning_info_entry_for_sales_order_controller.php?action=browse_desc_popup_action&sl=' + sl + '&salesId=' + salesId;
			var title = 'Description Popup';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=453px,height=170px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var prod_name = this.contentDoc.getElementById("hidden_prod_name").value;
				var prod_id = this.contentDoc.getElementById("hidden_prod_id").value;
				var count_id = this.contentDoc.getElementById("hidden_count_id").value;
				var hidden_row_sl = this.contentDoc.getElementById("hidden_row_sl").value;

				$('#txtDesc_'+hidden_row_sl).val(prod_name);
				$('#txtProdId_'+hidden_row_sl).val(prod_id);
				$('#cboCount_'+hidden_row_sl).val(count_id);
			}
		}
	</script>

</head>

<body>
	<div style="width:530px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:530px; margin-top:5px">
				<input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data" class="text_boxes"
				value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="100">Seq. No</th>
						<th width="100">Composition</th>
						<th width="100">Count</th>
						<th width="100">Feeding</th>
						<th></th>
					</thead>
				</table>
				<div style="width:525px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="505" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count","id","yarn_count");


						$count_feeding_data_arr = ($count_feeding_data != "") ? explode(",", $count_feeding_data) : array();
						if (!empty($count_feeding_data)){
							$sl = 1;
							for ($i = 0; $i < count($count_feeding_data_arr); $i++) {
								$count_feeding_data = explode("_", $count_feeding_data_arr[$i]);
								$seq = $count_feeding_data[0];
								$count_id = $count_feeding_data[1];
								$feeding_id = $count_feeding_data[2];
								$production_id = $count_feeding_data[3];
								$prodDesc = $count_feeding_data[4];
								?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl; ?>">
									<td width="30" align="center"><? echo $sl; ?></td>
									<td width="100">
										<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $sl ?>" value="<? echo $seq;?>" class="text_boxes" style="width:80px"/>
									</td>
									<td width="100">
										<input type="text" name="txtDesc[]" id="txtDesc_<?php echo $sl ?>" value="<? echo $prodDesc;?>" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="browse_desc_popup('<?php echo $sl; ?>','<?php echo $job_id; ?>')"/>

										<input type="hidden" name="txtProdId[]" id="txtProdId_<?php echo $sl ?>" value="<? echo $production_id;?>" class="text_boxes" style="width:80px"/>

										<input type="hidden" name="txtsalesID[]" id="txtsalesID_<?php echo $i; ?>" value="<?php echo $job_id; ?>" class="text_boxes" style="width:80px"/>

									</td>
									<td width="100">
										<?
										echo create_drop_down( "cboCount_".$sl, 80, $yarn_count_arr,"", 1, "-- Count --",$count_id, "",0,"","","","","","","cboCount[]");
										?>
									</td>
									<td width="100">
										<?
										echo create_drop_down( "cboFeeding_".$sl, 80, $feeding_arr,"", 1, "-- Feeding --",$feeding_id, "",0,"","","","","","","cboFeeding[]");
										?>
									</td>
									<td>
										<input type="button" id="increase_<? echo $sl; ?>" name="increase[]"
										style="width:30px" class="formbuttonplasminus" value="+"
										onClick="add_break_down_tr( <? echo $sl; ?> )"/>
										<input type="button" id="decrease_<? echo $sl; ?>" name="decrease[]"
										style="width:30px" class="formbuttonplasminus" value="-"
										onClick="fn_deleteRow(<? echo $sl; ?>);"/>
									</td>
								</tr>
								<?
								$sl++;
							}
						}
						else
						{
							$i=1;$sl=1;
							?>
							<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="30" align="center"><? echo $sl++; ?></td>
								<td width="100">
									<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i; ?>" value="1" class="text_boxes" style="width:80px"/>
								</td>
								<td width="100">
									<input type="text" name="txtDesc[]" id="txtDesc_<?php echo $i ?>"  class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="browse_desc_popup('<?php echo $i; ?>','<?php echo $job_id; ?>')"/>

									<input type="hidden" name="txtProdId[]" id="txtProdId_<?php echo $i; ?>" value="" class="text_boxes" style="width:80px"/>

									<input type="hidden" name="txtsalesID[]" id="txtsalesID_<?php echo $i; ?>" value="<?php echo $job_id; ?>" class="text_boxes" style="width:80px"/>

								</td>
								<td width="100">
									<?
									echo create_drop_down( "cboCount_".$i, 80, $yarn_count_arr,"", 1, "-- Count --", $selected, "",0,"","","","","","","cboCount[]");
									?>
								</td>
								<td width="100">
									<?
									echo create_drop_down( "cboFeeding_".$i, 80, $feeding_arr,"", 1, "-- Feeding --", $selected, "",0,"","","","","","","cboFeeding[]");
									?>
								</td>
								<td>
									<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
									style="width:30px" class="formbuttonplasminus" value="+"
									onClick="add_break_down_tr(<? echo $i; ?>)"/>
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
									style="width:30px" class="formbuttonplasminus" value="-"
									onClick="fn_deleteRow(<? echo $i; ?>);"/>
								</td>
							</tr>
							<?
							//$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table width="500" id="tbl_close">
				<tr>
					<td align="center">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close"
						onClick="fnc_close();" style="width:100px"/>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "advice_info_popup")
{
	echo load_html_head_contents("Advice Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
</head>
<body>
	<div style="width:430px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:400px; margin-top:10px;">
				<input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes" value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
					<tr>
						<td><textarea name="txt_advice" id="txt_advice" class="text_area"
							style="width:385px; height:120px;"><? echo $hidden_advice_data; ?></textarea></td>
						</tr>
					</table>
					<table width="400" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="parent.emailwindow.hide();" style="width:100px"/>
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "machine_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$machine_mixing_in_knittingplan = return_field_value("machine_mixing", "variable_settings_production", "company_name=$companyID and variable_list=157");
	$is_machine_mixing= $machine_mixing_in_knittingplan[0]['machine_mixing'];


	?>

	<style type="text/css">
		.highlight {
			background: #2e9500;
		}

		.highlight a {
			background-color: #42B373 !important;
			background-image :none !important;
			color: #ffffff !important;
			opacity: 0.7;
		}

		.program_calendar {
			height: 18px;
			font-size: 11px;
			line-height: 16px;
			padding: 0 5px;
			text-align:left;
			border: 1px solid #676767;
			border-radius: 3px;
			border-radius: .5em;
		}
	</style>

	<script>
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function calculate_qnty(tr_id) {
			var distribution_qnty = $('#txt_distribution_qnty_' + tr_id).val() * 1;

			//Function return for Machine mixing varialbe if No
			var fnc_return=fnc_check_machine_mixing_variable();
			if(fnc_return==1)
			{
				alert('Machine Mixing Not Allowed. Check Variable');
				$('#txt_noOfDays_' + tr_id).val('');
				$('#txt_startDate_' + tr_id).val('');
				$('#txt_endDate_' + tr_id).val('');
				$('#txt_distribution_qnty_' + tr_id).val('');
				return;
			}

			if (distribution_qnty > 0) {
				$('#search' + tr_id).css('background-color', 'yellow');
			}
			else {
				$('#search' + tr_id).css('background-color', '#FFFFCC');
			}

			calculate_total_qnty('txt_distribution_qnty_', 'txt_total_distribution_qnty');
		}

		function calculate_total_qnty(field_id, total_field_id) {
			var tot_row = $("#tbl_list_search tbody tr").length - 1;

			var ddd = {dec_type: 2, comma: 0, currency: ''}

			math_operation(total_field_id, field_id, "+", tot_row, ddd);

		}
		function fnc_check_machine_mixing_variable() {
			var machine_mixing_variable='<? echo $is_machine_mixing; ?>';
			var tot_rows = $("#tbl_list_search tbody tr").length - 1;
			var increment_counter=0;
			for (var x = 1; x <= tot_rows; x++) {
				var distribution_qnty = $('#txt_distribution_qnty_' + x).val() * 1;
				if(distribution_qnty>0)
				{
					increment_counter+=1;
				}
			}
			if(machine_mixing_variable==2 && increment_counter>1)
			{
				//alert('Machine Mixing Variable NO');
				return 1;
			}
		}


		function fnc_close() {
			var save_string = '';
			var allMachineId = '';
			var allMachineNo = '';
			var tot_capacity = '';
			var tot_distribution_qnty = '';
			var min_date = '';
			var max_date = '';
			var hidden_prog_qnty = $('#hidden_prog_qnty').val();
			var tot_row = $("#tbl_list_search tbody tr").length - 1;

			for (var i = 1; i <= tot_row; i++) {
				var machineId = $('#txt_individual_id' + i).val();
				var machineNo = $('#txt_individual' + i).val();
				var capacity = $('#txt_capacity_' + i).val();
				var distributionQnty = $('#txt_distribution_qnty_' + i).val();
				var noOfDays = $('#txt_noOfDays_' + i).val();
				var startDate = $('#txt_startDate_' + i).val();
				var endDate = $('#txt_endDate_' + i).val();
				var dtls_id = $('#dtls_id_' + i).val();

				if (distributionQnty * 1 > 0) {
					if (save_string == "") {
						save_string = machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId = machineId;
						allMachineNo = machineNo;
					}
					else {
						save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId += "," + machineId;
						allMachineNo += "," + machineNo;
					}

					if (min_date == '') {
						min_date = startDate;
					}

					if (date_compare(min_date, startDate) == false) {
						min_date = startDate;
					}

					if (date_compare(min_date, endDate) == false) {
						min_date = endDate;
					}

					if (max_date == '') {
						max_date = startDate;
					}

					if (date_compare(max_date, startDate) == true) {
						max_date = startDate;
					}

					if (date_compare(max_date, endDate) == true) {
						max_date = endDate;
					}

					tot_capacity = tot_capacity * 1 + capacity * 1;
					tot_distribution_qnty = tot_distribution_qnty * 1 + distributionQnty * 1;
				}
			}

			if(tot_distribution_qnty > hidden_prog_qnty){
				alert("Distribution quantity can not be greater than Program quantity");
				return;
			}else{
				$('#hidden_machine_id').val(allMachineId);
				$('#hidden_machine_no').val(allMachineNo);
				$('#save_string').val(save_string);
				$('#hidden_machine_capacity').val(tot_capacity);
				$('#hidden_distribute_qnty').val(tot_distribution_qnty);
				$('#hidden_min_date').val(min_date);
				$('#hidden_max_date').val(max_date);
			}

			parent.emailwindow.hide();
		}

		function fn_add_date_field(row_no) {
			var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val() * 1;

			if (distribute_qnty == 0 || distribute_qnty < 0) {
				alert("Please Insert Distribution Qnty First.");
				$('#txt_startDate_' + row_no).val('');
				$('#txt_distribution_qnty_' + row_no).focus();
				return;
			}

			if ($('#txt_startDate_' + row_no).val() != "") {
				var days_req = $('#txt_noOfDays_' + row_no).val();

				days_req = Math.ceil(days_req);
				if (days_req > 0) {
					days_req = days_req - 1;
					$("#txt_endDate_" + row_no).val(add_days($('#txt_startDate_' + row_no).val(), days_req));
				}

				var txt_startDate = $('#txt_startDate_' + row_no).val();
				var txt_endDate = $('#txt_endDate_' + row_no).val();
				var machine_id = $('#txt_individual_id' + row_no).val();

				var data = machine_id + "**" + txt_startDate + "**" + txt_endDate + "**" + '<? echo $update_dtls_id; ?>';
				var response = return_global_ajax_value(data, 'date_duplication_check', '', 'planning_info_entry_for_sales_order_controller');
				var response = response.split("_");
                //alert(response);return;
                if (response[0] != 0) {
                	alert("Date Overlaping for this machine. Dates Are (" + response[1] + ").");
                	$('#txt_startDate_' + row_no).val('');
                	$('#txt_endDate_' + row_no).val('');
                	return;
                }
            }
        }

        function calculate_noOfDays(row_no) {
        	var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val();
        	var machine_capacity = $('#txt_capacity_' + row_no).val();

        	var days_req = distribute_qnty * 1 / machine_capacity * 1;
        	$('#txt_noOfDays_' + row_no).val(days_req.toFixed(2));

        	if (distribute_qnty * 1 > 0) {
        		fn_add_date_field(row_no);
        	}
        	else {
        		$('#txt_noOfDays_' + row_no).val('');
        		$('#txt_startDate_' + row_no).val('');
        		$('#txt_endDate_' + row_no).val('');
        	}
        }


        // declare bookedDays global
        var bookedDays = [];
		// perform initial json request for free days
		fn_machine_book_dates();

		$(document).ready(function()
		{
			// fairly standard configuration, importantly containing beforeShowDay and onChangeMonthYear custom methods
			$('.program_calendar').datepicker({
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				changeYear: true,
				beforeShowDay:highlightDays,
				onChangeMonthYear: fn_machine_book_dates
			});
		});


		function fn_machine_book_dates(row_no)
		{
			var machine_id = $('#txt_individual_id' + row_no).val();

			if(machine_id!="" && machine_id!="undefined")
			{
				var update_dtls_id = '<? echo $update_dtls_id; ?>';
				var data ={"machine_id":machine_id,"update_dtls_id":update_dtls_id}

				$.ajax({
					type: "POST",
					url: "planning_info_entry_for_sales_order_controller.php?action=machine_allready_book_dates",
					data: data,
					cache: false,
					dataType: "json",
					success: function(response_data){
						$.each(response_data, function(index, value) {
							if (value!= "") {
	  							bookedDays.push(value); // add this date to the bookedDays array
	  						}
	  					});
					}
				})
			}
		}


		function highlightDays(date)
		{
			for (var i = 0; i < bookedDays.length; i++)
			{
				if (bookedDays[i] == $.datepicker.formatDate('dd-mm-yy', date))
				{
					return [true, 'highlight', 'All ready book this date'];
				}
			}
			return [true,''];
		}
	</script>
</head>
<body>
	<div style="width:830px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:820px; margin-top:10px; margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_id" id="hidden_machine_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_no" id="hidden_machine_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_capacity" id="hidden_machine_capacity" class="text_boxes" value="">
				<input type="hidden" name="hidden_distribute_qnty" id="hidden_distribute_qnty" class="text_boxes" value="">
				<input type="hidden" name="hidden_min_date" id="hidden_min_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_max_date" id="hidden_max_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_prog_qnty" id="hidden_prog_qnty" class="text_boxes" value="<? echo $txt_program_qnty;?>">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="80">Floor</th>
						<th width="60">Machine No</th>
						<th width="60">Dia</th>
						<th width="60">GG</th>
						<th width="80">Group</th>
						<th width="90">Capacity</th>
						<th width="90">Distribution Qnty</th>
						<th width="60">No. Of Days</th>
						<th width="80">Start Date</th>
						<th>End Date</th>
					</thead>
				</table>
				<div style="width:818px; overflow-y:scroll; max-height:220px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$qnty_array = array();
						$save_string = explode(",", $save_string);
						for ($i = 0; $i < count($save_string); $i++)
						{
							$machine_wise_data = explode("_", $save_string[$i]);
							$machine_id = $machine_wise_data[0];
							$capacity = $machine_wise_data[2];
							$distribution_qnty = $machine_wise_data[3];
							$noOfDays = $machine_wise_data[4];
							$startDate = $machine_wise_data[5];
							$endDate = $machine_wise_data[6];
							$dtls_id = $machine_wise_data[7];

							$qnty_array[$machine_id]['capacity'] = $capacity;
							$qnty_array[$machine_id]['distribution'] = $distribution_qnty;
							$qnty_array[$machine_id]['noOfDays'] = $noOfDays;
							$qnty_array[$machine_id]['startDate'] = $startDate;
							$qnty_array[$machine_id]['endDate'] = $endDate;
							$qnty_array[$machine_id]['dtls_id'] = $dtls_id;
						}

						$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
						if($txt_machine_gg!="")
						{
							$machinCond = "and gauge='$txt_machine_gg'";
						}

						/*
						|---------------------------------------------------------------
						| if textile sales maintain is no then
						| the machine no will be party machine no otherwise
						| LC company michine no
						|---------------------------------------------------------------
						*/
						/*$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name = ".$companyID." and variable_list=66 and status_active=1");
						if($variable_textile_sales_maintain[0][csf('production_entry')] ==2)
						{
							$companyID = $cbo_knitting_party;
						}*/

						if($cbo_knitting_source==3)
						{
							$outboundPartCond="and party_id=$cbo_knitting_party";
						}
						$vs_sql = "select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where company_id=$companyID and category_id=1 and status_active=1 and is_deleted=0 $outboundPartCond $machinCond order by seq_no";// and dia_width='$txt_machine_dia'
						$vs_result = sql_select($vs_sql);
						$i = 1;
						$tot_capacity = 0;
						$tot_distribution_qnty = 0;
						foreach ($vs_result as $row)
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$capacity = $qnty_array[$row[csf('id')]]['capacity'];
							if ($capacity == "")
							{
								$capacity = $row[csf('prod_capacity')];
							}

							$distribution_qnty = $qnty_array[$row[csf('id')]]['distribution'];
							if($distribution_qnty > 0) $bgcolor = "yellow"; else $bgcolor = $bgcolor;

							$noOfDays = $qnty_array[$row[csf('id')]]['noOfDays'];
							$startDate = $qnty_array[$row[csf('id')]]['startDate'];
							$endDate = $qnty_array[$row[csf('id')]]['endDate'];
							$dtls_id = $qnty_array[$row[csf('id')]]['dtls_id'];

							$tot_capacity += $capacity;
							$tot_distribution_qnty += $distribution_qnty;

							?>
							<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>"
								value="<? echo $row[csf('id')]; ?>"/>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>"
								value="<? echo $row[csf('machine_no')]; ?>"/>
							</td>
							<td width="80"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('machine_no')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('gauge')]; ?></p></td>
							<td width="80" align="center"><p><? echo $row[csf('machine_group')]; ?></p></td>
							<td width="90" align="center">
								<input type="text" name="txt_capacity[]" id="txt_capacity_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:75px" value="<? echo $capacity; ?>"
								onKeyUp="calculate_total_qnty('txt_capacity_','txt_total_capacity');calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="90">
								<input type="text" name="txt_distribution_qnty[]"
								id="txt_distribution_qnty_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:75px" value="<? echo $distribution_qnty; ?>"
								onKeyUp="calculate_qnty(<? echo $i; ?>);calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="60">
								<input type="text" name="txt_noOfDays[]" id="txt_noOfDays_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:45px" value="<? echo $noOfDays; ?>"
								onKeyUp="calculate_noOfDays(<? echo $i; ?>);" disabled="disabled"/>
							</td>
							<td align="center" width="80">
								<input type="text" name="txt_startDate[]" id="txt_startDate_<? echo $i; ?>"
								class="program_calendar" style="width:67px" value="<? echo $startDate; ?>"
								onChange="fn_add_date_field(<? echo $i; ?>);" onClick="fn_machine_book_dates(<? echo $i; ?>)"/>
								<!-- onChange="fn_add_date_field(<? //echo $i; ?>);" -->
							</td>
							<td align="center">
								<input type="text" name="txt_endDate[]" id="txt_endDate_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $endDate; ?>"
								disabled="disabled"/>
								<input type="hidden" name="dtls_id[]" id="dtls_id_<? echo $i; ?>"
								value="<? echo $dtls_id; ?>" disabled="disabled"/>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6" align="right"><b>Total</b></th>
						<th align="center"><input type="text" name="txt_total_capacity" id="txt_total_capacity"
							class="text_boxes_numeric" style="width:75px" readonly
							disabled="disabled" value="<? echo $tot_capacity; ?>"/></th>
							<th align="center"><input type="text" name="txt_total_distribution_qnty"
								id="txt_total_distribution_qnty" class="text_boxes_numeric"
								style="width:75px" readonly disabled="disabled"
								value="<? echo $tot_distribution_qnty; ?>"/></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<table width="700" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "date_duplication_check")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
		$endDate = change_date_format(trim($data[2]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
		$endDate = change_date_format(trim($data[2]), '', '', 1);
	}
	$update_dtls_id = $data[3];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' group by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = '';
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if ($row[csf('days_complete')] >= 1) {
				if ($data == '') $data = change_date_format($row[csf('distribution_date')]); else $data .= "," . change_date_format($row[csf('distribution_date')]);
			}
		}

		if ($data == '') echo "0_"; else echo "1" . "_" . $data;
	} else {
		echo "0_";
	}

	exit();
}

if ($action == "machine_allready_book_dates")
{
	extract($_REQUEST);
	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' group by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	if (count($data_array) > 0) {
		$dateslist = array();
		foreach ($data_array as $row) {
			if ($row[csf('days_complete')] >= 1) {
				$dateslist[] = date("d-m-Y", strtotime($row[csf('distribution_date')]));
			}
		}
	}

	if(!empty($dateslist))
	{
		header('Content-type: application/json');
		echo json_encode($dateslist);
	}
	exit();
}

if ($action == "style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

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
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');


			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:600px;">
				<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>PO Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
					<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
					<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
				</thead>
				<tbody>
					<tr>
						<td id="buyer_td">
							<?
							echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
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
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'planning_info_entry_for_sales_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:90px;"/>
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

if ($action == "create_job_search_list_view")
{
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$company_id = $data[0];
	$buyer_id = $data[1];
	$po_buyer_id = $data[2];
	$within_group = $data[3];
	$search_by = $data[4];
	$search_string = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
	if ($buyer_id == 0) $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id=$buyer_id";
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
	else $year_field = "";//defined Later
	if ($within_group == 1) {
		$sql = "select * from ( select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond and a.buyer_id=$buyer_id and fabric_source in(1,2)
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond  and a.buyer_id=$buyer_id and (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num) order by id DESC";

	} else {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=$within_group $search_field_cond order by a.id DESC";
	}
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:600px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('within_group')] == 1)
				$buyer = $company_arr[$row[csf('buyer_id')]];
			else
				$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
				<td width="40"><? echo $i; ?>
				<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>"
				value="<? echo $row[csf('id')]; ?>"/>
				<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>"
				value="<? echo $row[csf('job_no')]; ?>"/>
			</td>
			<td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
			<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
			<td width="70" align="center"><p><? echo $buyer; ?>&nbsp;</p></td>
			<td width="120" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
			<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
		</tr>
		<?
		$i++;
	}
	?>
</table>
</div>
<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
	<tr>
		<td align="center" height="30" valign="bottom">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
					Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
					value="Close" style="width:100px"/>
				</div>
			</div>
		</td>
	</tr>
</table>
<?
exit();
}


if ($action == "internal_ref_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(internal_ref)
		{
			$('#hidden_internal_ref').val(internal_ref);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:750px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:100%;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="835" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Po Buyer</th>
						<th>Booking Date</th>
						<th>Booking Type</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="150">Please Enter Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $companyID; ?>">
							<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
							value="<? echo $cbo_within_group; ?>">
							<input type="hidden" name="hidden_internal_ref" id="hidden_internal_ref" class="text_boxes"
							value="">
						</th>
					</thead>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							readonly>
						</td>
						<td align="center">
							<?
							$booking_type_arr = array(1 => "Fabric Booking", 2 => "Sample Booking");
							echo create_drop_down("cbo_booking_type", 100, $booking_type_arr, "", 0, '', '', '');
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No", 2 => "Job No", 3 => "IR/IB");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 3, $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_internal_ref_search_list_view', 'search_div', 'planning_info_entry_for_sales_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:90px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_internal_ref_search_list_view")
{
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);
	$booking_type = trim($data[7]);

	if ($buyer_id == 0)
	{
		$buyer_id_cond = "";
	}
	else
	{
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	$search_field_cond_2 = "";

	if (trim($data[0]) != "")
	{
		if ($search_by == 1)
		{
			if ($cbo_within_group == 1)
			{
				$search_field_cond = "and a.booking_no like '$search_string'";
				$search_field_cond_2 = "and a.booking_no like '$search_string'";
			}
			else
			{
				$search_field_cond = "and c.sales_booking_no like '$search_string'";
				$search_field_cond_2 = "and b.sales_booking_no like '$search_string'";
			}
		}
		else if($search_by == 3 || $search_by == 1) {
				//for internal ref.
				$internalRef_cond = '';$booking_nos_cond = '';$booking_nos_cond2 = '';
				$internalRef_cond = " and a.grouping like '$search_string'";
				$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond group by b.booking_no,a.job_no_mst,a.grouping");
				$booking_nos="";$bookingArrChk=array();$internalRefArr=array();
				foreach ($sql_bookings as $row) {
					$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
					if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
					{
						$booking_nos.="'".$row[csf('booking_no')]."',";
						$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
					}
				}
				$booking_nos=chop($booking_nos,",");
				$booking_nos_cond = "and a.booking_no in($booking_nos)";
				$booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
				unset($sql_bookings);
		}
		else
		{
			$search_field_cond = "and a.job_no like '$search_string'";
			//for internal ref.
			$internalRef_cond = '';$booking_nos_cond = '';$booking_nos_cond2 = '';
			$internalRef_cond = " and a.job_no_mst like '$search_string'";
			$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond group by b.booking_no,a.job_no_mst,a.grouping");
			$booking_nos="";$bookingArrChk=array();$internalRefArr=array();
			foreach ($sql_bookings as $row) {
				$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
				if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
					$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			$booking_nos=chop($booking_nos,",");
			$booking_nos_cond = "and a.booking_no in($booking_nos)";
			$booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
			unset($sql_bookings);
		}
	}
	/*else
	{
			//for internal ref.
			$booking_nos_cond = '';$booking_nos_cond2 = '';
			$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,a.job_no_mst,a.grouping");
			$booking_nos="";$bookingArrChk=array();$internalRefArr=array();
			foreach ($sql_bookings as $row) {
				$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
				if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
					$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			$booking_nos=chop($booking_nos,",");
			$booking_nos_cond = "and a.booking_no in($booking_nos)";
			$booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
			unset($sql_bookings);
	}
*/
	$date_cond = '';
	if ($cbo_within_group == 1)
	{

	}
	$date_field = ($cbo_within_group == 2) ? "c.booking_date" : "a.booking_date";
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($cbo_within_group == 1)
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, fabric_sales_order_mst c where a.job_no=b.job_no and a.booking_no=c.sales_booking_no and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond $booking_nos_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no,b.style_ref_no";
		}
		//for sample booking
		else
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, fabric_sales_order_mst c, sample_development_mst d where a.booking_no=b.booking_no and a.booking_no=c.sales_booking_no and b.style_id = d.id and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond $booking_nos_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no";
		}
	}
	else
	{
		$sql = "select c.id, c.sales_booking_no booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no from fabric_sales_order_mst c where c.company_id=$company_id and c.status_active =1 and c.is_deleted=0 $date_cond $search_field_cond $booking_nos_cond2 and c.within_group=2 group by c.id, c.sales_booking_no, c.booking_date, c.buyer_id, c.company_id, c.job_no, c.style_ref_no";
	}
	//echo $sql;

	$result = sql_select($sql);
	$poArr = array();
	$buyerArr = array();
	$jobsArrChks = array();
	$jobs_nos="";
	foreach ($result as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];

		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}

		if ($row[csf('job_no')] != "")
		{
			if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
			{
				$jobs_nos.="'".$row[csf('job_no')]."',";
				$jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
			}
		}
	}



	//for partial
	if($db_type==0)
	{
		$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, group_concat(c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
	}
	else
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
		}
		//for sample booking
		else
		{
			//$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond  group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id";
		}
	}
	//echo $sql_partial;
	$result_partial = sql_select($sql_partial);
	foreach ($result_partial as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];

		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
		if ($row[csf('job_no')] != "")
		{
			if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
			{
				$jobs_nos.="'".$row[csf('job_no')]."',";
				$jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
			}
		}
	}
	//echo "<pre>";
	//print_r($buyerArr);
	$jobs_nos=chop($jobs_nos,",");
	if (trim($data[0]) == "")
	{
		//for internal ref.
		$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst in($jobs_nos) group by b.booking_no,a.job_no_mst,a.grouping");
		$internalRefArr=array();
		foreach ($sql_bookings as $row) {
			$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
		}
		unset($sql_bookings);
	}

	//for company details
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');

	//for buyer details
	//$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_arr = array();
	if(!empty($buyerArr))
	{
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1".where_con_using_array($buyerArr,0,'id'), "id", "buyer_name");
	}

	//for buyer details
	//$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "buyer_name");
	$po_arr = array();
	if(!empty($poArr))
	{
		$po_arr = return_library_array("select id, po_number from wo_po_break_down where 1=1".where_con_using_array($poArr,0,'id'), "id", "buyer_name");
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Job No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th width="100">IR/IB</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:840px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$j = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td width="100"><p><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></p></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}

		//for partial
		foreach ($result_partial as $row)
		{
			if ($j % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?>')">
				<td width="40"><? echo $j; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td width="100"><p><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></p></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$j++;
		}
		?>
	</table>
</div>
<?
exit();
}


if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(booking_no)
		{
			$('#hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:750px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:100%;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="835" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Po Buyer</th>
						<th>Booking Date</th>
						<th>Booking Type</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="150">Please Enter Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $companyID; ?>">
							<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
							value="<? echo $cbo_within_group; ?>">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes"
							value="">
						</th>
					</thead>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							readonly>
						</td>
						<td align="center">
							<?
							$booking_type_arr = array(1 => "Fabric Booking", 2 => "Sample Booking");
							echo create_drop_down("cbo_booking_type", 100, $booking_type_arr, "", 0, '', '', '');
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No", 2 => "Job No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_booking_search_list_view', 'search_div', 'planning_info_entry_for_sales_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:90px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);
	$booking_type = trim($data[7]);

	if ($buyer_id == 0)
	{
		$buyer_id_cond = "";
	}
	else
	{
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	$search_field_cond_2 = "";

	if (trim($data[0]) != "")
	{
		if ($search_by == 1)
		{
			if ($cbo_within_group == 1)
			{
				$search_field_cond = "and a.booking_no like '$search_string'";
				$search_field_cond_2 = "and a.booking_no like '$search_string'";
			}
			else
			{
				$search_field_cond = "and c.sales_booking_no like '$search_string'";
				$search_field_cond_2 = "and b.sales_booking_no like '$search_string'";
			}
		}
		else
		{
			$search_field_cond = "and a.job_no like '$search_string'";
		}
	}

	$date_cond = '';
	if ($cbo_within_group == 1)
	{

	}
	$date_field = ($cbo_within_group == 2) ? "c.booking_date" : "a.booking_date";
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($cbo_within_group == 1)
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, fabric_sales_order_mst c where a.job_no=b.job_no and a.booking_no=c.sales_booking_no and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no,b.style_ref_no";
		}
		//for sample booking
		else
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, fabric_sales_order_mst c, sample_development_mst d where a.booking_no=b.booking_no and a.booking_no=c.sales_booking_no and b.style_id = d.id and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no";
		}
	}
	else
	{
		$sql = "select c.id, c.sales_booking_no booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no from fabric_sales_order_mst c where c.company_id=$company_id and c.status_active =1 and c.is_deleted=0 $date_cond $search_field_cond and c.within_group=2 group by c.id, c.sales_booking_no, c.booking_date, c.buyer_id, c.company_id, c.job_no, c.style_ref_no";
	}
	//echo $sql;

	$result = sql_select($sql);
	$poArr = array();
	$buyerArr = array();
	foreach ($result as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];

		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
	}

	//for partial
	if($db_type==0)
	{
		$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, group_concat(c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
	}
	else
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
		}
		//for sample booking
		else
		{
			//$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond  group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id";
		}
	}
	//echo $sql_partial;
	$result_partial = sql_select($sql_partial);
	foreach ($result_partial as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];

		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
	}
	//echo "<pre>";
	//print_r($buyerArr);

	//for company details
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');

	//for buyer details
	//$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_arr = array();
	if(!empty($buyerArr))
	{
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1".where_con_using_array($buyerArr,0,'id'), "id", "buyer_name");
	}

	//for buyer details
	//$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "buyer_name");
	$po_arr = array();
	if(!empty($poArr))
	{
		$po_arr = return_library_array("select id, po_number from wo_po_break_down where 1=1".where_con_using_array($poArr,0,'id'), "id", "buyer_name");
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Job No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:740px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$j = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}

		//for partial
		foreach ($result_partial as $row)
		{
			if ($j % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
				<td width="40"><? echo $j; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$j++;
		}
		?>
	</table>
</div>
<?
exit();
}

if ($action == "load_drop_down_knitting_party")
{
	//$companyID = 3;
	//echo "field_level_accessing(".$companyID .");\n";



	//echo "load_drop_down_knitting_party YEs";die;
	$data = explode("**", $data);
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value, 'load_drop_down_location','location_td');", "");
	} else if ($data[0] == 3) {
		if ($data[2] == 1) $selected_id = $data[1]; else $selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 177, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value, 'load_drop_down_location','location_td');");
	} else {
		echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 0, "load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value, 'load_drop_down_location','location_td');");
	}


	exit();
}
if ($action == "load_drop_down_knitting_party_new")
{
	//$companyID = 3;
	//echo "field_level_accessing(".$companyID .");\n";



	//echo "load_drop_down_knitting_party YEs";die;
	$data = explode("**", $data);
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value, 'load_drop_down_location','location_td');", "");
	} else if ($data[0] == 3) {

		$sql = "SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name   UNION ALL SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b,ppl_planning_info_entry_dtls c where  c.KNITTING_PARTY = a.id and     a.id=b.supplier_id and b.party_type=20 and a.status_active IN(1,3) and a.is_deleted=0 group by a.id, a.supplier_name  order by supplier_name";


		if ($data[2] == 1) $selected_id = $data[1]; else $selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 177, "$sql", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value, 'load_drop_down_location','location_td');");
	} else {
		echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 0, "load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value, 'load_drop_down_location','location_td');");
	}


	exit();
}
if($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $company_ids = str_replace("'","",$data);

    $check_location_sql=sql_select("SELECT id,location_name from lib_location where company_id in($company_ids)  and status_active =1 and is_deleted=0 order by location_name");

    if(count($check_location_sql)==1){
    	echo create_drop_down( "cbo_location_name", 152, "SELECT id,location_name from lib_location where company_id in($company_ids)  and status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "--Select Location--", 0, "","" );
    }
    else{
    	echo create_drop_down( "cbo_location_name", 152, "SELECT id,location_name from lib_location where company_id in($company_ids)  and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", 0, "","" );
    }
	exit();
}

if($action == "activePlan")
{
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}
	$data = explode("**",$data);
	$prog_no = explode("_",$data[1]);
	$plan_id = implode(",",explode("_",$data[0]));
	$program_qnty = $data[2];
	$dtls_ids = $data[3];
	$program_qnty = $data[4];
	$get_colors_by_dtls_id = sql_select("select id,color_id from fabric_sales_order_dtls where id in ($dtls_ids)");
	$color_arr = array();
	foreach ($get_colors_by_dtls_id as $color) {
		$color_arr[] = $color[csf("color_id")];
	}
	$color_ids = implode(",",$color_arr);

	// check if knitting production is done
	$check_program_in_production=sql_select("select booking_id from inv_receive_master where booking_id in($prog_no) and entry_form=2 and item_category=13 and receive_basis=2 and status_active=1 and is_deleted=0");
	$revised_sql = (!empty($check_program_in_production))?"is_revised=1, ":"";

	$rID1 = execute_query("update ppl_planning_info_entry_mst set status_active=1, is_deleted=0, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in($plan_id)", 0);
	if($rID1==1) $flag=1; else $flag=0;
	foreach ($prog_no as $program)
	{
		$rID2 = execute_query("update ppl_planning_info_entry_dtls set program_qnty=$program_qnty,color_id='$color_ids',status_active=1, is_deleted=0,$revised_sql updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where mst_id in($plan_id) and id in($program)", 0);

		//echo "update ppl_planning_info_entry_dtls set program_qnty=$program_qnty,color_id='$color_ids',status_active=1, is_deleted=0,$revised_sql updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where mst_id in($plan_id) and id in($program)<br>";

		if($rID2==1) $flag=1; else $flag=0;
		$rID3 = execute_query("update ppl_planning_entry_plan_dtls set program_qnty=$program_qnty,sales_order_dtls_ids='$dtls_ids',status_active=1, is_deleted=0,$revised_sql updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where mst_id in($plan_id) and dtls_id in($program)", 0);
		if($rID3==1) $flag=1; else $flag=0;
	}
	$rID3 = execute_query("update fabric_sales_order_dtls set status_active=1, is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in($dtls_ids)", 0);
	//echo "10**".$program_qnty;oci_rollback($con);die;
	if($rID3==1) $flag=1; else $flag=0;
	if ($db_type == 0) {
		if ($flag==1) {
			mysql_query("COMMIT");
			echo "1";
		} else {
			mysql_query("ROLLBACK");
			echo "6";
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($flag==1) {
			oci_commit($con);
			echo "1";
		} else {
			oci_rollback($con);
			echo "6";
		}
	}
	disconnect($con);
	die;
}

if ($action == "barcode_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info/ Barcode", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>

	<script>

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
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_barcode_no' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_barcode_no' + str).val());
			}
			else {
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
			<fieldset style="width:700px;">
				<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>PO Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
					<th>Barcode No</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
					<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos" value=""/>
				</thead>
				<tbody>
					<tr>
						<td id="buyer_td">
							<?
							echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
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
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>

						<td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td>

						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'planning_info_entry_for_sales_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:90px;"/>
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

if ($action == "create_barcode_search_list_view")
{
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$company_id = $data[0];
	$po_buyer_id = $data[1];
	$within_group = $data[2];
	$search_by = $data[3];
	$search_string = trim($data[4]);
	$barcode_no = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($barcode_no != "") {
		$search_field_cond = " and d.barcode_no = '" . $barcode_no . "'";
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";

	if ($po_buyer_id == 0)
	{
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
	else $year_field = "";//defined Later
	if ($within_group == 1) {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num,d.barcode_no from fabric_sales_order_mst a,wo_booking_mst b,fabric_sales_order_dtls d where a.sales_booking_no = b.booking_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond and b.company_id=$company_id and fabric_source in(1,2)
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num,d.barcode_no from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c,fabric_sales_order_dtls d where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond  and b.company_id=$company_id and (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num,d.barcode_no";

	} else {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id,d.barcode_no from fabric_sales_order_mst a,fabric_sales_order_dtls d where a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=$within_group $search_field_cond $po_buyer_id_cond  order by a.id";
	}
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th width="100">Barcode</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('within_group')] == 1)
				$buyer = $company_arr[$row[csf('buyer_id')]];
			else
				$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
				<td width="40"><? echo $i; ?>
				<input type="hidden" name="txt_barcode_no" id="txt_barcode_no<?php echo $i ?>"
				value="<? echo $row[csf('barcode_no')]; ?>"/>
			</td>
			<td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
			<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
			<td width="70" align="center"><p><? echo $buyer; ?>&nbsp;</p></td>
			<td width="120" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
			<td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
			<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
		</tr>
		<?
		$i++;
	}
	?>
</table>
</div>
<table width="800" cellspacing="0" cellpadding="0" style="border:none" align="center">
	<tr>
		<td align="center" height="30" valign="bottom">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
					Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
					value="Close" style="width:100px"/>
				</div>
			</div>
		</td>
	</tr>
</table>
<?
exit();
}

if ($action == "prog_info_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$path = $data[2];
	//echo $path;die;
	echo load_html_head_contents("Program Qnty Info", $path, 1, 1, '', '', '');

	$company_details = return_library_array("select id,company_name from lib_company where id=".$company_id."", "id", "company_name");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$country_arr = return_library_array("select id, country_name from lib_country where status_active=1 and is_deleted=0", 'id', 'country_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

	if($program_id!="")
	{
		$plan_info=sql_select("SELECT a.style_ref_no, a.within_group, a.job_no, a.company_id,a.location_id as sales_order_location_id, a.po_company_id, a.buyer_id, a.po_buyer, b.dtls_id, b.booking_no, b.gsm_weight as fin_gsm, b.dia as fin_dia, b.fabric_desc, c.program_date, c.end_date, c.knitting_source, c.knitting_party, c.location_id, c.color_range, c.stitch_length, c.machine_dia, c.machine_gg, c.program_qnty, c.machine_id, c.width_dia_type, c.color_id, c.fabric_dia, c.remarks, c.advice, c.no_of_ply
		from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b, ppl_planning_info_entry_dtls c
		where a.id=b.po_id and b.dtls_id=c.id and b.dtls_id=".$program_id." and b.status_active=1 and b.is_deleted=0");
	}
	//echo "<pre>";
	//print_r($plan_info); die;

	$product_details_array = array();
	//$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand FROM product_details_master WHERE item_category_id=1 AND company_id=".$company_id." AND status_active=1 AND is_deleted=0 AND id IN(SELECT prod_id FROM ppl_yarn_requisition_entry WHERE knit_id='".$program_id."' AND status_active=1 AND is_deleted=0)";
	$result = sql_select($sql);
	foreach ($result as $row)
	{
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
	//echo "<pre>";
	//print_r($product_details_array);

	$sql_machin = "SELECT dtls_id, machine_id, SUM(distribution_qnty) AS distribution_qnty FROM ppl_planning_info_machine_dtls WHERE status_active=1 AND is_deleted=0 AND dtls_id IN($program_id) GROUP BY dtls_id, machine_id ORDER BY machine_id";
	$machine_datas = sql_select($sql_machin);
	$machineData = array();
	foreach ($machine_datas as $mcrow)
	{
		$machineData[$mcrow[csf('dtls_id')]][$mcrow[csf('machine_id')]] = $mcrow[csf('distribution_qnty')];
	}
	?>
	<div style="width:1000px;">
		<style>
			table, th, td {
				/*border-bottom:1px solid black;*/
				border-collapse: collapse;
			}
		</style>
		<div style="width:100%;">
			<table style="width:800px; border-bottom:1px solid black;">
				<tr>
					<td width="60%" align="center" style="font-size: 16px; font-family: arial; font-weight: bolder;"><? echo $company_details[$company_id]; ?></td>
					<td width="15%">&nbsp;</td>
					<td width="25%">&nbsp;</td>
				</tr>
				<tr>
					<td width="60%" align="center">
						<b>
							<?
							$compAddressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$company_id."");
							$address = "";
							foreach ($compAddressArray as $result) {
								$address .=  "Plot No:". $result[csf('plot_no')] ." Level No: " .$result[csf('level_no')]." Road No: ".$result[csf('road_no')]." Block No: ".$result[csf('block_no')]." City No: ".$result[csf('city')]." Zip Code: ".$result[csf('zip_code')]. "<br>".$country_arr[$result[csf('country_id')]] ."<br>".$result[csf('email')]."<br>".$result[csf('website')]  ;
							}
							echo $address;
							?>
						</b>
					</td>
					<td width="15%" style="border-top: 1px solid black; border-left: 1px solid black; padding-left: 5px;"><b>Program No:</b></td>
					<td width="25%" style="border-top: 1px solid black; border-right: 1px solid black;"><b><? echo $plan_info[0][csf('dtls_id')];?></b></td>
				</tr>
				<tr>
					<td width="60%" align="center">&nbsp;</td>
					<td width="15%" style="border-left: 1px solid black; padding-left: 5px;"><b>Sales Order No:</b></td>
					<td width="25%" style="border-right: 1px solid black;"><b><? echo $plan_info[0][csf('job_no')];?></b></td>
				</tr>
				<tr>
					<td width="60%" align="center" style="font-size: 20px; font-weight: bold;">Knitting Program Slip</td>
					<td width="15%" style="border-left: 1px solid black; padding-left: 5px;" ><b>Fabric/Booking No:</b></td>
					<td width="25%" style="border-right: 1px solid black;"><b><? echo $plan_info[0][csf('booking_no')];?></b></td>
				</tr>
			</table>
			<br><br>
			<table width="800" style="float: left;font-weight: bold; margin-bottom:25px;">
				<tr>
					<td colspan="4"><b>Attention- Knitting Manager</b></td>
				</tr>
				<tr>
					<td style="padding:0px 10px 0px 20px;">Factory</td>
					<td style="border-bottom: 1px solid black; font-size: 20px;">
						<?php
						if ($plan_info[0][csf('knitting_source')] == 1)
							echo $company_details[$plan_info[0][csf('knitting_party')]];
						else if ($plan_info[0][csf('knitting_source')] == 3)
							echo $supllier_arr[$plan_info[0][csf('knitting_party')]];
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td> Program Date:  <b><?php echo change_date_format($plan_info[0][csf('program_date')]);?></b></td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">Address</td>
					<td style="border-bottom: 1px solid black; padding-top:20px;">
						<?
						$address = '';
						if ($plan_info[0][csf('knitting_source')] == 1)
						{
							foreach ($compAddressArray as $result)
							{
								$address .=  $result[csf('plot_no')]." ".$result[csf('level_no')]." ".$result[csf('road_no')]." ".$result[csf('block_no')]." ".$result[csf('city')]." ".$result[csf('zip_code')]."<br>";
								$address .= $country_arr[$result[csf('country_id')]]."<br>";
								$address .= $result[csf('email')]."<br>";
								$address .= $result[csf('website')];
							}

						}
						else if ($plan_info[0][csf('knitting_source')] == 3)
						{
							$address = return_field_value("address_1", "lib_supplier", "id=" . $plan_info[0][csf('knitting_party')]);
						}
						echo $address;
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">PO Company:</td>
					<td style="border-bottom: 1px solid black;">
						<?
						echo $company_details[$plan_info[0][csf('po_company_id')]];
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td style="border:1px solid black;text-align:center;">Target Date of Completion</td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">Location:</td>
					<td>
						<?
						if ($plan_info[0][csf('knitting_source')] == 1)
						{
							$location = return_field_value("location_name", "lib_location", "id='" . $plan_info[0][csf('location_id')] . "'");
						}
						else if ($plan_info[0][csf('knitting_source')] == 3)
						{
							$location = return_field_value("location_name", "lib_location", "id='" . $plan_info[0][csf('sales_order_location_id')] . "'");
						}
						echo $location;
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td style="border:1px solid black;text-align:center;"><? echo change_date_format($plan_info[0][csf('end_date')]);?></td>
				</tr>
			</table>
			<table class="rpt_table" width="800" cellspacing="0" cellpadding="0" border="1" rules="all">
				<thead>
					<tr>
						<th>SL</th>
						<th>Requisition No</th>
						<th>Lot No</th>
						<th>Yarn Description</th>
						<th>Brand</th>
						<th>Requisition Qnty</th>
						<th>Yarn Color</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$tot_reqsn_qnty = 0;
					$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='".$program_id."' and status_active=1 and is_deleted=0";
					//echo $sql;
					$nameArray = sql_select($sql);
					if(!empty($nameArray))
					{
						foreach ($nameArray as $selectResult)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">

								<td><? echo $i; ?></td>
								<td align="center"><? echo $selectResult[csf('requisition_no')]; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
								<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
								<td align="center"><? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></td>
								<td>&nbsp;</td>
							</tr>
							<?
							$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
							$i++;
						}

					}else{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
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
					<th colspan="5" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
			<br>
			<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all">

				<thead>
					<tr>
						<th width="50">MC. No/SL</th>
						<th width="85">Buyer</th>
						<th width="80">Style No</th>
						<th width="100">Fab Type</th>
						<th width="90">Garments Color</th>
						<th width="50">MC Dia & Gauge</th>
						<th width="20">No Of Ply</th>
						<th width="20">Fin Dia</th>
						<th width="20">Fin GSM</th>
						<th width="50">SL</th>
						<th width="50">Colour Range</th>
						<th width="50">Program Quantity</th>
						<th width="50">Remarks</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$total_distribution_qty = 0;
					$fabric_arr = explode(",",$plan_info[0][csf('fabric_desc')]);
					$machine_idarr = explode(",", $plan_info[0][csf("machine_id")]);
					$prog_distriqty = 0;
					foreach ($machine_idarr as $machineid)
					{
						$distributionQnty = $machineData[$plan_info[0][csf("dtls_id")]][$machineid];
						if($distributionQnty>0)
						{
							$prog_distriqty = $distributionQnty;
						}
						else
						{
							$prog_distriqty = $plan_info[0][csf("program_qnty")];
						}

						if($machineid!="")
						{
							$machineSl = $machine_arr[$machineid];
						}
						else
						{
							$machineSl = 1;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="65" align="center">
								<? echo $machineSl;?>
							</td>
							<td width="100">
								<?
								if ($plan_info[0][csf('within_group')] == 1)
								{
									$buyer = $buyer_arr[$plan_info[0][csf("po_buyer")]];
								}
								else
								{
									$buyer = $buyer_arr[$plan_info[0][csf("buyer_id")]];
								}

								echo $buyer;
								?>
							</td>
							<td width="100"><? echo $plan_info[0][csf("style_ref_no")];  ?></td>
							<td width="100"><? echo $fabric_arr[0]; ?></td>
							<td width="65">
								<?
								$color_id_arr = array_unique(explode(",", $plan_info[0][csf('color_id')]));
								$all_color = "";
								foreach ($color_id_arr as $color_id)
								{
									$all_color .= $color_library[$color_id] . ",";
								}
								$all_color = chop($all_color, ",");
								echo $all_color;
								?>
							</td>
							<td width="50"><? echo $plan_info[0][csf('machine_dia')] . "X" . $plan_info[0][csf('machine_gg')]; ?></td>
							<td width="10"><? echo $plan_info[0][csf('no_of_ply')];?></td>
							<td width="10"><? echo $plan_info[0][csf('fabric_dia')];?></td>
							<td width="10"><? echo $plan_info[0][csf('fin_gsm')];?></td>
							<td width="10" align="center"><? echo $plan_info[0][csf('stitch_length')];?></td>
							<td width="50"><? echo $color_range[$plan_info[0][csf('color_range')]]; ?></td>
							<td width="50" align="right"><? echo number_format($prog_distriqty, 2); ?></td>
							<td width="50"><? echo $plan_info[0][csf('remarks')]; ?></td>
						</tr>
						<?
						//$total_distribution_qty += $row[csf('distribution_qnty')];
						$total_distribution_qty += $prog_distriqty;

						if($machineid!="")
						{
							$machineSl++;
						}
					}
					?>
					<tfoot>
						<th colspan="11" align="right"><b>Total</b></th>
						<th style="text-align: right;"><? echo number_format($total_distribution_qty, 2); ?></th>
						<th>&nbsp;</th>
					</tfoot>
				</tbody>
			</table>
			<br>
			<span> Advice:  <? echo $plan_info[0][csf('advice')]; ?> </span>
			<div style="width:100%; float:left;padding-top:10px;">
				<?
				//$sql_stripe_feeder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=".$program_id." and a.no_of_feeder>0   group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder");

				$sql_stripe_feeder = sql_select("select b.pre_cost_fabric_cost_dtls_id as pre_cost_id, b.color_number_id as color_id, b.stripe_color as stripe_color_id,a.no_of_feeder, b.measurement, b.uom
				from ppl_planning_feeder_dtls a,wo_pre_stripe_color  b
				where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id  and b.stripe_color=a.stripe_color_id
				and b.status_active=1 and b.is_deleted=0
				and a.dtls_id=".$program_id." and a.no_of_feeder>0
				and b.sales_dtls_id is not null
				group by  b.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.stripe_color,a.no_of_feeder, b.measurement, b.uom ");
				//order by b.color_number_id, b.stripe_color,b.measurement

				if (count($sql_stripe_feeder) > 0)
				{
					?>
					<table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="6" align="center">Stripe Measurement Information:</th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Combo Color</th>
								<th width="100">Stripe Color</th>
								<th width="100">Measurement </th>
								<th width="50">Uom</th>
								<th width="100">No Of Feeder</th>
							</tr>
						</thead>

						<tbody>
							<?
							$i = 1;
							$total_feeder = 0;
							foreach ($sql_stripe_feeder as $row)
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="100"><? echo $color_library[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $color_library[$row[csf('stripe_color_id')]]; ?></td>
									<td width="100" align="center">
										<?
										echo number_format($row[csf('measurement')], 2);
										$total_measurement += $row[csf('measurement')];
										?>
									</td>
									<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
									<td width="100" align="center">
										<? echo number_format($row[csf('no_of_feeder')], 0);
										$total_feeder += $row[csf('no_of_feeder')]; ?>
									</td>
								</tr>
								<?
								$i++;
							}
							?>
						</tbody>

						<tfoot>
							<th colspan="3" align="right"><b>Total</b></th>
							<th style="text-align: center;"><? echo number_format($total_measurement, 0); ?></th>
							<th>&nbsp;</th>
							<th style="text-align: center;"><? echo number_format($total_feeder, 0); ?></th>
						</tfoot>
					</table>
					<?
				}

				$sql_collar_cuff_dtls = sql_select("select body_part_id, grey_size, finish_size, qty_pcs, needle_per_cm from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id=$program_id");

				if (count($sql_collar_cuff_dtls) > 0)
				{
					?>
					<table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="6" align="center">Collar & Cuff Measurement Information:</th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Body Part</th>
								<th width="100">Grey Size</th>
								<th width="100">Finish Size</th>
								<th width="50">Qty. Pcs</th>
								<th width="100">Needle Per CM</th>
							</tr>
						</thead>

						<tbody>
							<?
							$k = 1;
							$total_cuff_qty = 0;
							foreach ($sql_collar_cuff_dtls as $cuff_row)
							{
								if ($k % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>">
									<td width="50" align="center"><? echo $k; ?></td>
									<td width="100" align="center"><? echo $body_part[$cuff_row[csf('body_part_id')]]; ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('grey_size')]; ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('finish_size')]; ?></td>
									<td width="50" align="right"><? echo number_format($cuff_row[csf('qty_pcs')], 0); ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('needle_per_cm')]; ?></td>
								</tr>
								<?
								$total_qty_pcs += $cuff_row[csf('qty_pcs')];
								$k++;
							}
							?>
						</tbody>

						<tfoot>
							<th colspan="4" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($total_qty_pcs,0);?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<?
				}
				?>
			</div>

			<div style="width:100%; float:left; padding-top:20px;">

				<?
				$sql_stripe_colorwise = sql_select("select a.stripe_color_id, a.no_of_feeder,sum(b.fabreqtotkg) as fabreqtotkg , max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.stripe_color_id, a.no_of_feeder");

				if (count($sql_stripe_colorwise) > 0)
				{

					?>
					<table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="5" align="center">Colour Wise Quantity</th>
							</tr>
							<tr>
								<th width="100">Stripe Color</th>
								<th width="100">Measurement</th>
								<th width="100">UOM</th>
								<th width="100">Total Feeder</th>
								<th width="100">Quantity(Kg)</th>
							</tr>
						</thead>

						<tbody>
							<?
							$y = 1;
							foreach ($sql_stripe_colorwise as $colorwise_row)
							{
								if ($y % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="100"><? echo $color_library[$colorwise_row[csf('stripe_color_id')]]; ?></td>
									<td width="100" align="center"><? echo number_format($colorwise_row[csf('measurement')], 2);?></td>
									<td width="100" align="center"><? echo $unit_of_measurement[$colorwise_row[csf('uom')]]; ?></td>
									<td width="100" align="center"><? echo number_format($colorwise_row[csf('no_of_feeder')], 0);?></td>
									<td width="100" align="right"><? echo number_format($colorwise_row[csf('fabreqtotkg')], 0);?></td>
								</tr>
								<?
								$y++;
							}
							?>

						</tbody>
					</table>
					<?
				}

				$sql_count_feed = "select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=$program_id and status_active=1 and is_deleted=0 order by seq_no";
				$data_array_count_feed = sql_select($sql_count_feed);
				if(count($data_array_count_feed)>0)
				{
					?>
					<table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="4" align="center">Count Feeding</th>
							</tr>
							<tr>
								<th width="50">Seq. No</th>
								<th width="100">Count</th>
								<th width="100">Feeding</th>
								<th width="100">Percentage</th>
							</tr>
						</thead>

						<tbody>
							<?
							$feeding_arr = array(1 => 'Knit', 2 => 'Binding', 3 => 'Loop');
							$j=1;
							foreach ($data_array_count_feed as $count_feed_row)
							{
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr>
									<td width="50" align="center"><? echo $count_feed_row[csf('seq_no')]; ?></td>
									<td width="100" align="center"><? echo $count_arr[$count_feed_row[csf('count_id')]];?></td>
									<td width="100"><? echo $feeding_arr[$count_feed_row[csf('feeding_id')]];?></td>
									<td width="100">&nbsp;</td>
								</tr>
								<?
								$j++;
							}
							?>
						</tbody>
					</table>
					<?
				}
				?>
			</div>

			<div style="width:100%; float:left;padding-top:10px;">
				<?
				$sql_cam_design = "select id,cmd1, cmd2, cmd3, cmd4, cmd5, cmd6, cmd7, cmd8, cmd9, cmd10, cmd11, cmd12, cmd13, cmd14, cmd15, cmd16, cmd17, cmd18, cmd19, cmd20, cmd21, cmd22, cmd23, cmd24 from ppl_planning_cam_design_dtls where dtls_id=$program_id and status_active=1 and is_deleted=0 order by id";
				$data_cam_design = sql_select($sql_cam_design);
				if (count($data_cam_design) > 0)
				{

					?>
					<table width="100%" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="25" align="center">Cam Design Information</th>
							</tr>
							<tr>
								<th width="4%">SL</th>
								<?
								for ($i=1; $i<=24; $i++)
								{
									?>
									<th width="4%"><? echo $i; ?></th>
									<?
								}
								?>
							</tr>
						</thead>
					</table>

					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"
					id="tbl_came_design">
					<tbody>
						<?
						$sl=1;
						foreach ($data_cam_design as $row)
						{
							if ($sl % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="4%" align="center"><? echo $sl; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd1')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd2')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd3')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd4')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd5')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd6')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd7')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd8')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd9')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd10')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd11')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd12')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd13')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd14')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd15')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd16')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd17')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd18')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd19')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd20')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd21')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd22')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd23')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd24')]; ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</tbody>
				</table>
				<?
			}
			?>
			</div>



			<!--- Needle layout --->
			<div  style="width:100%; float:left; padding-top: 20px;">
				<style type="text/css">
				    #needle-layout{
				        border-collapse:collapse;
				        table-layout:fixed;
				        width:600pt;
				        font-size: 20px;
				    }
				</style>
				<?
				$sql_needle_layout = "select PLAN_ID, PROGRAM_NO, DIAL, CYLINDER, DIAL_ROW1, DIAL_ROW2, NO_OF_FEEDER, CYLINDER_ROW1, CYLINDER_ROW2, CYLINDER_ROW3, CYLINDER_ROW4, YARN_ENDS, LFA, YARN_TENSION, GREY_GSM, T_DRY_WEIGHT, T_DRY_WIDTH, RPM, F_ROLL_WIDTH, LAID_WIDTH,ACTIVE_FEEDER, REV_PER_KG, DIAL_HEIGHT from ppl_planning_needle_layout where PROGRAM_NO=$program_id AND STATUS_ACTIVE=1 AND IS_DELETED=0";

				$data_needle_layout = sql_select($sql_needle_layout);

				if (count($data_needle_layout) > 0)
				{
					foreach ($data_needle_layout as $row)
					{
						if($row['DIAL_ROW1']!="")
						{
							$dial_row1_data_arr = explode("__", $row['DIAL_ROW1']);

							$dial_row1col1 = ($dial_row1_data_arr[0]!="")?$dial_row1_data_arr[0]:"";
							$dial_row1col2 = ($dial_row1_data_arr[1]!="")?$dial_row1_data_arr[1]:"";
							$dial_row1col3 = ($dial_row1_data_arr[2]!="")?$dial_row1_data_arr[2]:"";
							$dial_row1col4 = ($dial_row1_data_arr[3]!="")?$dial_row1_data_arr[3]:"";
							$dial_row1col5 = ($dial_row1_data_arr[4]!="")?$dial_row1_data_arr[4]:"";
							$dial_row1col6 = ($dial_row1_data_arr[5]!="")?$dial_row1_data_arr[5]:"";
						}

						if($row['DIAL_ROW2']!="")
						{
							$dial_row2_data_arr = explode("__", $row['DIAL_ROW2']);

							$dial_row2col1 = ($dial_row2_data_arr[0]!="")?$dial_row2_data_arr[0]:"";
							$dial_row2col2 = ($dial_row2_data_arr[1]!="")?$dial_row2_data_arr[1]:"";
							$dial_row2col3 = ($dial_row2_data_arr[2]!="")?$dial_row2_data_arr[2]:"";
							$dial_row2col4 = ($dial_row2_data_arr[3]!="")?$dial_row2_data_arr[3]:"";
							$dial_row2col5 = ($dial_row2_data_arr[4]!="")?$dial_row2_data_arr[4]:"";
							$dial_row2col6 = ($dial_row2_data_arr[5]!="")?$dial_row2_data_arr[5]:"";
						}

						if($row['NO_OF_FEEDER']!="")
						{
							$no_of_feeder_data_arr = explode("__", $row['NO_OF_FEEDER']);

							$no_of_feeder_col1 = ($no_of_feeder_data_arr[0]!="")?$no_of_feeder_data_arr[0]:"";
							$no_of_feeder_col2 = ($no_of_feeder_data_arr[1]!="")?$no_of_feeder_data_arr[1]:"";
							$no_of_feeder_col3 = ($no_of_feeder_data_arr[2]!="")?$no_of_feeder_data_arr[2]:"";
							$no_of_feeder_col4 = ($no_of_feeder_data_arr[3]!="")?$no_of_feeder_data_arr[3]:"";
							$no_of_feeder_col5 = ($no_of_feeder_data_arr[4]!="")?$no_of_feeder_data_arr[4]:"";
							$no_of_feeder_col6 = ($no_of_feeder_data_arr[5]!="")?$no_of_feeder_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW1']!="")
						{
							$cylinder_row1_data_arr = explode("__", $row['CYLINDER_ROW1']);

							$cylinder_row1col1 = ($cylinder_row1_data_arr[0]!="")?$cylinder_row1_data_arr[0]:"";
							$cylinder_row1col2 = ($cylinder_row1_data_arr[1]!="")?$cylinder_row1_data_arr[1]:"";
							$cylinder_row1col3 = ($cylinder_row1_data_arr[2]!="")?$cylinder_row1_data_arr[2]:"";
							$cylinder_row1col4 = ($cylinder_row1_data_arr[3]!="")?$cylinder_row1_data_arr[3]:"";
							$cylinder_row1col5 = ($cylinder_row1_data_arr[4]!="")?$cylinder_row1_data_arr[4]:"";
							$cylinder_row1col6 = ($cylinder_row1_data_arr[5]!="")?$cylinder_row1_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW2']!="")
						{
							$cylinder_row2_data_arr = explode("__", $row['CYLINDER_ROW2']);

							$cylinder_row2col1 = ($cylinder_row2_data_arr[0]!="")?$cylinder_row2_data_arr[0]:"";
							$cylinder_row2col2 = ($cylinder_row2_data_arr[1]!="")?$cylinder_row2_data_arr[1]:"";
							$cylinder_row2col3 = ($cylinder_row2_data_arr[2]!="")?$cylinder_row2_data_arr[2]:"";
							$cylinder_row2col4 = ($cylinder_row2_data_arr[3]!="")?$cylinder_row2_data_arr[3]:"";
							$cylinder_row2col5 = ($cylinder_row2_data_arr[4]!="")?$cylinder_row2_data_arr[4]:"";
							$cylinder_row2col6 = ($cylinder_row2_data_arr[5]!="")?$cylinder_row2_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW3']!="")
						{
							$cylinder_row3_data_arr = explode("__", $row['CYLINDER_ROW3']);

							$cylinder_row3col1 = ($cylinder_row3_data_arr[0]!="")?$cylinder_row3_data_arr[0]:"";
							$cylinder_row3col2 = ($cylinder_row3_data_arr[1]!="")?$cylinder_row3_data_arr[1]:"";
							$cylinder_row3col3 = ($cylinder_row3_data_arr[2]!="")?$cylinder_row3_data_arr[2]:"";
							$cylinder_row3col4 = ($cylinder_row3_data_arr[3]!="")?$cylinder_row3_data_arr[3]:"";
							$cylinder_row3col5 = ($cylinder_row3_data_arr[4]!="")?$cylinder_row3_data_arr[4]:"";
							$cylinder_row3col6 = ($cylinder_row3_data_arr[5]!="")?$cylinder_row3_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW4']!="")
						{
							$cylinder_row4_data_arr = explode("__", $row['CYLINDER_ROW4']);

							$cylinder_row4col1 = ($cylinder_row4_data_arr[0]!="")?$cylinder_row4_data_arr[0]:"";
							$cylinder_row4col2 = ($cylinder_row4_data_arr[1]!="")?$cylinder_row4_data_arr[1]:"";
							$cylinder_row4col3 = ($cylinder_row4_data_arr[2]!="")?$cylinder_row4_data_arr[2]:"";
							$cylinder_row4col4 = ($cylinder_row4_data_arr[3]!="")?$cylinder_row4_data_arr[3]:"";
							$cylinder_row4col5 = ($cylinder_row4_data_arr[4]!="")?$cylinder_row4_data_arr[4]:"";
							$cylinder_row4col6 = ($cylinder_row4_data_arr[5]!="")?$cylinder_row4_data_arr[5]:"";
						}

						if($row['YARN_ENDS']!="")
						{
							$yarn_ends_data_arr = explode("__", $row['YARN_ENDS']);

							$yarn_ends_col1 = ($yarn_ends_data_arr[0]!="")?$yarn_ends_data_arr[0]:"";
							$yarn_ends_col2 = ($yarn_ends_data_arr[1]!="")?$yarn_ends_data_arr[1]:"";
							$yarn_ends_col3 = ($yarn_ends_data_arr[2]!="")?$yarn_ends_data_arr[2]:"";
							$yarn_ends_col4 = ($yarn_ends_data_arr[3]!="")?$yarn_ends_data_arr[3]:"";
							$yarn_ends_col5 = ($yarn_ends_data_arr[4]!="")?$yarn_ends_data_arr[4]:"";
						}

						if($row['LFA']!="")
						{
							$lfa_data_arr = explode("__", $row['LFA']);

							$lfa_col1 = ($lfa_data_arr[0]!="")?$lfa_data_arr[0]:"";
							$lfa_col2 = ($lfa_data_arr[1]!="")?$lfa_data_arr[1]:"";
							$lfa_col3 = ($lfa_data_arr[2]!="")?$lfa_data_arr[2]:"";
							$lfa_col4 = ($lfa_data_arr[3]!="")?$lfa_data_arr[3]:"";
							$lfa_col5 = ($lfa_data_arr[4]!="")?$lfa_data_arr[4]:"";
						}

						if($row['YARN_TENSION']!="")
						{
							$yarn_tension_data_arr = explode("__", $row['YARN_TENSION']);

							$yarn_tension_col1 = ($yarn_tension_data_arr[0]!="")?$yarn_tension_data_arr[0]:"";
							$yarn_tension_col2 = ($yarn_tension_data_arr[1]!="")?$yarn_tension_data_arr[1]:"";
							$yarn_tension_col3 = ($yarn_tension_data_arr[2]!="")?$yarn_tension_data_arr[2]:"";
							$yarn_tension_col4 = ($yarn_tension_data_arr[3]!="")?$yarn_tension_data_arr[3]:"";
							$yarn_tension_col5 = ($yarn_tension_data_arr[4]!="")?$yarn_tension_data_arr[4]:"";
						}
					?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  id="needle-layout">

					        <tr height='21'>
						        <td colspan='8' height='21' width='237' style='background-color: #00b0f0; text-align: center;'>Needle Layout</td>
						    </tr>
						    <tr height='20'>
						        <td colspan="2" height='20' style='background-color: #ffcc00;'>Dial</td>
						        <td colspan="2">
						        	<? echo $row['DIAL'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan="2" height='20' style='background-color: #ffcc00;'>Cylinder</td>
						        <td colspan="2">
						        	<? echo $row['CYLINDER'];?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td rowspan='7' style="vertical-align: middle;transform: rotate(270deg);">
						        	Cam Setting
						        </td>
						        <td rowspan='2' align="center">Dial</td>
						        <td align="center">
						        	<? echo $dial_row1col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col6; ?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td height='20' align="center">
						        	<? echo $dial_row2col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col6; ?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td height='20' style='text-align: center;'>No Of Feeder</td>
						        <td align="center">
						        	<? echo $no_of_feeder_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td rowspan='4'> <div style='vertical-align: middle; transform: rotate(270deg);'> Cylinder </dive></td>
						        <td align="center">
						        	<? echo $cylinder_row1col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center">
						        	<? echo $cylinder_row2col1; ?>
						        </td>

						        <td align="center">
						        	<? echo $cylinder_row2col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center">
						        	<? echo $cylinder_row3col1; ?>
						        </td>

						        <td align="center">
						        	<? echo $cylinder_row3col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center">
						        	<? echo $cylinder_row4col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #c2d69a;'>Yarn Ends</td>
						        <td align="center">
						        	<? echo  $yarn_ends_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #75923c;'>LFA</td>
						        <td align="center">
						        	<? echo  $lfa_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #ccc0da;'>Yarn Tension</td>
						        <td align="center">
						        	<? echo  $yarn_tension_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='20' style="background-color: #31849b;">Grey GSM</td>
						        <td align="center">
						        	<? echo $row['GREY_GSM'];?>
						        </td>
						        <td style="background-color: #b8cce4;">T.Dry Weight</td>
						        <td align="center">
						        	<? echo $row['T_DRY_WEIGHT'];?>
						        </td>
						        <td style="background-color: #bfbfbf;">T.Dry Width</td>
						        <td align="center">
						        	<? echo $row['T_DRY_WIDTH'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style="background-color: #93cddd;">RPM</td>
						        <td align="center">
						        	<? echo $row['RPM'];?>
						        </td>
						        <td style="background-color: #538ed5;">F.Roll Width</td>
						        <td align="center">
						        	<? echo $row['F_ROLL_WIDTH'];?>
						        </td>
						        <td style="background-color: #a5a5a5;">Laid Width</td>
						        <td align="center">
						        	<? echo $row['LAID_WIDTH'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style="background-color: #93cddd;">Total Active Feeder</td>
						        <td align="center">
						        	<? echo $row['ACTIVE_FEEDER'];?>
						        </td>
						        <td style="background-color: #538ed5;">Rev per Kg</td>
						        <td align="center">
						        	<? echo $row['REV_PER_KG'];?>
						        </td>
						        <td style="background-color: #a5a5a5;">Dial Height </td>
						        <td align="center">
						        	<? echo $row['DIAL_HEIGHT'];?>
						        </td>
						    </tr>

						</table>
					<?
					}
				}
				?>

			</div>

			<!-- Needle layout end -->

			<?
				// coller cuff size wise part

				$sql_info ="select coller_cuf_size_planning from variable_settings_production where company_name='$company_id' and variable_list=53 and status_active=1 and is_deleted=0";
				//echo $sql_info;// die;
				$result_dtls = sql_select($sql_info);
				$collarCuff=$result_dtls[0]['COLLER_CUF_SIZE_PLANNING'];

				if($collarCuff==1)
				{

					$sql_fedder = sql_select("select a.id,c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id,
					a.stripe_color_id, min(b.measurement) as measurement
					from ppl_planning_feeder_dtls a, wo_pre_stripe_color b,ppl_size_wise_break_down c
					where   a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.mst_id=c.plan_id and a.dtls_id=c.program_no and c.color_id=a.color_id and c.program_no in($program_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.no_of_feeder>0 and b.sales_dtls_id>0  group by a.id,c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id ,a.stripe_color_id order by a.id, c.color_id asc");


					$size_plan_sql = sql_select("select c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id, sum(c.current_qty) as current_qty from ppl_size_wise_break_down c where c.program_no in($program_id) group by  c.plan_id,c.program_no, c.color_id,c.body_part_id,c.finish_size_id");
				}
				else
				{

					$sql_fedder = sql_select("select a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, min(b.measurement) as measurement,c.body_part_id,c.finish_size as finish_size_id from ppl_planning_feeder_dtls a, wo_pre_stripe_color b,ppl_color_wise_break_down d ,ppl_planning_collar_cuff_dtls c where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.mst_id=d.plan_id and a.dtls_id=d.program_no and d.color_id=a.color_id and d.program_no=c.dtls_id and d.plan_id=c.mst_id and c.dtls_id in($program_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and a.dtls_id in($program_id) and a.no_of_feeder>0 and b.sales_dtls_id>0 group by a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,d.color_id,c.body_part_id,c.finish_size order by a.id, d.color_id");

					$size_plan_sql = sql_select("select c.mst_id as plan_id,c.dtls_id as program_no, c.body_part_id,c.finish_size as finish_size_id, sum(c.qty_pcs) as current_qty,b.color_id
					from ppl_color_wise_break_down b ,ppl_planning_collar_cuff_dtls c
					where   b.program_no=c.dtls_id and b.plan_id=c.mst_id and c.dtls_id in($program_id)
					group by   c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,b.color_id
					order by b.color_id asc");


					//select c.mst_id as plan_id,c.dtls_id as program_no, c.body_part_id,c.finish_size as finish_size_id, sum(c.qty_pcs) as current_qty,b.color_id from ppl_color_wise_break_down b left join ppl_planning_collar_cuff_dtls c on  b.program_no=c.dtls_id and b.plan_id=c.mst_id where c.dtls_id in(17846) group by   c.mst_id,c.dtls_id, c.body_part_id,c.finish_size,b.color_id order by b.color_id asc

				}

				$qntyArry=array();
				foreach ($size_plan_sql as $rowData)
				{
					if(!in_array($rowData[csf('current_qty')],$current_qty_duplicate_chk))
					{
						$current_qty_duplicate_chk[]=$rowData[csf('current_qty')];
						$qntyArry[$rowData[csf('body_part_id')]][$rowData[csf('color_id')]][$rowData[csf('finish_size_id')]]+=$rowData[csf('current_qty')];
					}
				}
				/*echo "<pre>";
				print_r($mainDataArry);
				echo "</pre>";*/

				$plan_color_type_array = return_library_array("select dtls_id, color_type_id from PPL_PLANNING_ENTRY_PLAN_DTLS where dtls_id in($program_id) group by dtls_id,color_type_id", "dtls_id", "color_type_id");

				$color_type_id=$plan_color_type_array[$program_id];


				/*$sql_fedder = sql_select("select a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id, min(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.dtls_id in($program_ids)  and a.no_of_feeder>0 group by a.id,a.mst_id,a.dtls_id, a.color_id, a.stripe_color_id order by a.id");*/

				if (count($sql_fedder) > 0) {
					foreach ($sql_fedder as $row)
					{
						$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['stripe_color_id']=$row[csf('stripe_color_id')];
						$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['measurement']=$row[csf('measurement')];
						$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['body_part_id']=$row[csf('body_part_id')];
						$arrMeasurement[$row[csf('mst_id')]][$row[csf('dtls_id')]][$row[csf('body_part_id')]][$row[csf('finish_size_id')]][$row[csf('color_id')]][$row[csf('stripe_color_id')]][$row[csf('measurement')]]['finish_size_id']=$row[csf('finish_size_id')];
					}
				}


				$bodypart_library = return_library_array("select id,body_part_full_name from lib_body_part", "id", "body_part_full_name");

					foreach ($arrMeasurement as $planId => $planData)
					{
						foreach ($planData as $progNo => $progData)
						{
							foreach ($progData as $body_part_id => $body_part_idData)
							{
								foreach ($body_part_idData as $finish_size_id => $finish_size_idData)
								{
									foreach ($finish_size_idData as $color_ids => $color_idData)
									{
										foreach ($color_idData as $strp_color_ids => $strip_colorData)
										{
											foreach ($strip_colorData as $measurementNo => $rows)
											{
												$bodyPartCountArr[$body_part_id]+=1;
												$colorIdsCountArr[$color_ids]+=1;
												$finishSizeCountArr[$finish_size_id]+=1;
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
								<th width="50">Finish Size	</th>
								<th>Quantity Pcs</th>
							</tr>
						</thead>
						<tbody>
							<?
						$i = 1;
						if ($color_type_id == 2 || $color_type_id == 3 || $color_type_id == 4)
						{
							foreach ($arrMeasurement as $planId => $planData)
							{
								foreach ($planData as $progNo => $progData)
								{
									foreach ($progData as $body_part_id => $body_part_idData)
									{
										foreach ($body_part_idData as $finish_size_id => $finish_size_idData)
										{
											foreach ($finish_size_idData as $color_ids => $color_idData)
											{
												foreach ($color_idData as $strp_color_ids => $strip_colorData)
												{
													foreach ($strip_colorData as $measurementNo => $rows)
													{
														$bodyPart_span = $bodyPartCountArr[$body_part_id]++;
														$colorIds_span = $colorIdsCountArr[$color_ids]++;
														$finishSize_span = $finishSizeCountArr[$finish_size_id]++;
														?>

														<tr>
															<?
															if(!in_array($body_part_id,$body_part_id_chk))
															{
																$body_part_id_chk[]=$body_part_id;
																?>
																<td align="center" rowspan="<? echo $bodyPart_span ;?>"><p><? echo $i; ?>&nbsp;</p></td>
																<td align="center" rowspan="<? echo $bodyPart_span ;?>"><? echo $bodypart_library[$body_part_id];?></td>
																<?
															}

															if(!in_array($color_ids,$color_id_chk))
															{
																$color_id_chk[]=$color_ids;
																?>
																<td align="center" rowspan="<? echo $colorIds_span ;?>"><p><? echo $color_library[$color_ids]; ?>&nbsp;</p></td>
																<?
															}
															?>
															<td align="center"><p><? echo $color_library[$strp_color_ids]; ?>&nbsp;</p></td>
															<td align="right"><p><? echo number_format($measurementNo,2); ?>&nbsp;</p></td>
															<?
															if(!in_array($finish_size_id,$finishSize_chk))
															{
																$finishSize_chk[]=$finish_size_id;
																?>
																<td align="right" rowspan="<? echo $finishSize_span ;?>"><p><? echo $finish_size_id; ?>&nbsp;</p></td>
																<td align="right" rowspan="<? echo $finishSize_span ;?>"><p><? echo $qntyArry[$body_part_id][$color_ids][$finish_size_id]; ?>&nbsp;</p></td>
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
						}
						else
						{
							foreach ($size_plan_sql as $rows)
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								//$stripeColor=$arrMeasurement[$rows[csf('plan_id')]][$rows[csf('program_no')]][$rows[csf('color_id')]]['stripe_color_id'];
								//$measurQnty=$arrMeasurement[$rows[csf('plan_id')]][$rows[csf('program_no')]][$rows[csf('color_id')]]['measurement'];
								?>
								<tr>
									<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
									<td align="center"><p><? echo $bodypart_library[$rows[csf('body_part_id')]];; ?></p></td>
									<td align="center"><p><? echo $color_library[$rows[csf('color_id')]]; ?>&nbsp;</p></td>
									<td align="center"><p></p></td>
									<td align="right"><p></p></td>
									<td align="right"><p><? echo $rows[csf('finish_size_id')]; ?></p></td>
									<td align="right"><p><? echo number_format($rows[csf('current_qty')],2); ?>&nbsp;</p></td>
								</tr>
								<?
								$i++;
							}
						}
						?>
						</tbody>
					</table>



		<div style="width:100%; float:left; padding-top:30px;">
			<table style="width:100%; float:left;" class="rpt_table">
				<tr>
					<td rowspan="3" valign="top">Special Instruction:</td>
					<td>Any type of fabric faults is not acceptable.(Patta,Sinker/Needle Mark,Loop/Hole,Tara,Fly,Oil Sport )</td>
				</tr>
				<tr>
					<td>Factory must mention the Program Number on the Delivery Challan and Bill/ Invoice.</td>
				</tr>
				<tr>
					<td>Roll marking must be done with Parmanent marker</td>
				</tr>
			</table>
		</div>

		<div style="width:100%; float:left; padding-top:20px;">
			<table style="width:100%; float:left;">
				<tr>
					<td>Received & Accepted by: </td>
					<td>&nbsp;</td>
					<td>Prepared By: </td>
					<td>&nbsp;</td>
					<td>Authorized Signature: </td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</table>
	</div>
	</div>
	<?
}

if ($action == "delete_booking_prog_info_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$path = $data[2];
	//echo $path;die;
	echo load_html_head_contents("Program Qnty Info", $path, 1, 1, '', '', '');

	$company_details = return_library_array("select id,company_name from lib_company where id=".$company_id."", "id", "company_name");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$country_arr = return_library_array("select id, country_name from lib_country where status_active=1 and is_deleted=0", 'id', 'country_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

	if($program_id!="")
	{
		$plan_info=sql_select("SELECT a.style_ref_no, a.within_group, a.job_no, a.company_id,a.location_id as sales_order_location_id, a.po_company_id, a.buyer_id, a.po_buyer, b.dtls_id, b.booking_no, b.gsm_weight as fin_gsm, b.dia as fin_dia, b.fabric_desc, c.program_date, c.end_date, c.knitting_source, c.knitting_party, c.location_id, c.color_range, c.stitch_length, c.machine_dia, c.machine_gg, c.program_qnty, c.machine_id, c.width_dia_type, c.color_id, c.fabric_dia, c.remarks, c.advice, c.no_of_ply
		from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b, ppl_planning_info_entry_dtls c
		where a.id=b.po_id and b.dtls_id=c.id and b.dtls_id=".$program_id." and b.status_active=0 and b.is_deleted=1");
	}
	//echo "<pre>";
	//print_r($plan_info); die;

	$product_details_array = array();
	//$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand FROM product_details_master WHERE item_category_id=1 AND company_id=".$company_id." AND status_active=1 AND is_deleted=0 AND id IN(SELECT prod_id FROM ppl_yarn_requisition_entry WHERE knit_id='".$program_id."' AND status_active=1 AND is_deleted=0)";
	$result = sql_select($sql);
	foreach ($result as $row)
	{
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
	//echo "<pre>";
	//print_r($product_details_array);

	$sql_machin = "SELECT dtls_id, machine_id, SUM(distribution_qnty) AS distribution_qnty FROM ppl_planning_info_machine_dtls WHERE status_active=1 AND is_deleted=0 AND dtls_id IN($program_id) GROUP BY dtls_id, machine_id ORDER BY machine_id";
	$machine_datas = sql_select($sql_machin);
	$machineData = array();
	foreach ($machine_datas as $mcrow)
	{
		$machineData[$mcrow[csf('dtls_id')]][$mcrow[csf('machine_id')]] = $mcrow[csf('distribution_qnty')];
	}
	?>
	<div style="width:1000px;">
		<style>
			table, th, td {
				/*border-bottom:1px solid black;*/
				border-collapse: collapse;
			}
		</style>
		<div style="width:100%;">
			<table style="width:800px; border-bottom:1px solid black;">
				<tr>
					<td width="60%" align="center" style="font-size: 16px; font-family: arial; font-weight: bolder;"><? echo $company_details[$company_id]; ?></td>
					<td width="15%">&nbsp;</td>
					<td width="25%">&nbsp;</td>
				</tr>
				<tr>
					<td width="60%" align="center">
						<b>
							<?
							$compAddressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$company_id."");
							$address = "";
							foreach ($compAddressArray as $result) {
								$address .=  "Plot No:". $result[csf('plot_no')] ." Level No: " .$result[csf('level_no')]." Road No: ".$result[csf('road_no')]." Block No: ".$result[csf('block_no')]." City No: ".$result[csf('city')]." Zip Code: ".$result[csf('zip_code')]. "<br>".$country_arr[$result[csf('country_id')]] ."<br>".$result[csf('email')]."<br>".$result[csf('website')]  ;
							}
							echo $address;
							?>
						</b>
					</td>
					<td width="15%" style="border-top: 1px solid black; border-left: 1px solid black; padding-left: 5px;"><b>Program No:</b></td>
					<td width="25%" style="border-top: 1px solid black; border-right: 1px solid black;"><b><? echo $plan_info[0][csf('dtls_id')];?></b></td>
				</tr>
				<tr>
					<td width="60%" align="center">&nbsp;</td>
					<td width="15%" style="border-left: 1px solid black; padding-left: 5px;"><b>Sales Order No:</b></td>
					<td width="25%" style="border-right: 1px solid black;"><b><? echo $plan_info[0][csf('job_no')];?></b></td>
				</tr>
				<tr>
					<td width="60%" align="center" style="font-size: 20px; font-weight: bold;">Knitting Program Slip</td>
					<td width="15%" style="border-left: 1px solid black; padding-left: 5px;" ><b>Fabric/Booking No:</b></td>
					<td width="25%" style="border-right: 1px solid black;"><b><? echo $plan_info[0][csf('booking_no')];?></b></td>
				</tr>
			</table>
			<br><br>
			<table width="800" style="float: left;font-weight: bold; margin-bottom:25px;">
				<tr>
					<td colspan="4"><b>Attention- Knitting Manager</b></td>
				</tr>
				<tr>
					<td style="padding:0px 10px 0px 20px;">Factory</td>
					<td style="border-bottom: 1px solid black; font-size: 20px;">
						<?php
						if ($plan_info[0][csf('knitting_source')] == 1)
							echo $company_details[$plan_info[0][csf('knitting_party')]];
						else if ($plan_info[0][csf('knitting_source')] == 3)
							echo $supllier_arr[$plan_info[0][csf('knitting_party')]];
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td> Program Date:  <b><?php echo change_date_format($plan_info[0][csf('program_date')]);?></b></td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">Address</td>
					<td style="border-bottom: 1px solid black; padding-top:20px;">
						<?
						$address = '';
						if ($plan_info[0][csf('knitting_source')] == 1)
						{
							foreach ($compAddressArray as $result)
							{
								$address .=  $result[csf('plot_no')]." ".$result[csf('level_no')]." ".$result[csf('road_no')]." ".$result[csf('block_no')]." ".$result[csf('city')]." ".$result[csf('zip_code')]."<br>";
								$address .= $country_arr[$result[csf('country_id')]]."<br>";
								$address .= $result[csf('email')]."<br>";
								$address .= $result[csf('website')];
							}

						}
						else if ($plan_info[0][csf('knitting_source')] == 3)
						{
							$address = return_field_value("address_1", "lib_supplier", "id=" . $plan_info[0][csf('knitting_party')]);
						}
						echo $address;
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">PO Company:</td>
					<td style="border-bottom: 1px solid black;">
						<?
						echo $company_details[$plan_info[0][csf('po_company_id')]];
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td style="border:1px solid black;text-align:center;">Target Date of Completion</td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">Location:</td>
					<td>
						<?
						if ($plan_info[0][csf('knitting_source')] == 1)
						{
							$location = return_field_value("location_name", "lib_location", "id='" . $plan_info[0][csf('location_id')] . "'");
						}
						else if ($plan_info[0][csf('knitting_source')] == 3)
						{
							$location = return_field_value("location_name", "lib_location", "id='" . $plan_info[0][csf('sales_order_location_id')] . "'");
						}
						echo $location;
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td style="border:1px solid black;text-align:center;"><? echo change_date_format($plan_info[0][csf('end_date')]);?></td>
				</tr>
			</table>
			<table class="rpt_table" width="800" cellspacing="0" cellpadding="0" border="1" rules="all">
				<thead>
					<tr>
						<th>SL</th>
						<th>Requisition No</th>
						<th>Lot No</th>
						<th>Yarn Description</th>
						<th>Brand</th>
						<th>Requisition Qnty</th>
						<th>Yarn Color</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$tot_reqsn_qnty = 0;
					$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='".$program_id."' and status_active=1 and is_deleted=0";
					//echo $sql;
					$nameArray = sql_select($sql);
					if(!empty($nameArray))
					{
						foreach ($nameArray as $selectResult)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">

								<td><? echo $i; ?></td>
								<td align="center"><? echo $selectResult[csf('requisition_no')]; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
								<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
								<td align="center"><? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></td>
								<td>&nbsp;</td>
							</tr>
							<?
							$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
							$i++;
						}

					}else{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
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
					<th colspan="5" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
			<br>
			<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all">

				<thead>
					<tr>
						<th width="50">MC. No/SL</th>
						<th width="85">Buyer</th>
						<th width="80">Style No</th>
						<th width="100">Fab Type</th>
						<th width="90">Garments Color</th>
						<th width="50">MC Dia & Gauge</th>
						<th width="20">No Of Ply</th>
						<th width="20">Fin Dia</th>
						<th width="20">Fin GSM</th>
						<th width="50">SL</th>
						<th width="50">Colour Range</th>
						<th width="50">Program Quantity</th>
						<th width="50">Remarks</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$total_distribution_qty = 0;
					$fabric_arr = explode(",",$plan_info[0][csf('fabric_desc')]);
					$machine_idarr = explode(",", $plan_info[0][csf("machine_id")]);
					$prog_distriqty = 0;
					foreach ($machine_idarr as $machineid)
					{
						$distributionQnty = $machineData[$plan_info[0][csf("dtls_id")]][$machineid];
						if($distributionQnty>0)
						{
							$prog_distriqty = $distributionQnty;
						}
						else
						{
							$prog_distriqty = $plan_info[0][csf("program_qnty")];
						}

						if($machineid!="")
						{
							$machineSl = $machine_arr[$machineid];
						}
						else
						{
							$machineSl = 1;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="65" align="center">
								<? echo $machineSl;?>
							</td>
							<td width="100">
								<?
								if ($plan_info[0][csf('within_group')] == 1)
								{
									$buyer = $buyer_arr[$plan_info[0][csf("po_buyer")]];
								}
								else
								{
									$buyer = $buyer_arr[$plan_info[0][csf("buyer_id")]];
								}

								echo $buyer;
								?>
							</td>
							<td width="100"><? echo $plan_info[0][csf("style_ref_no")];  ?></td>
							<td width="100"><? echo $fabric_arr[0]; ?></td>
							<td width="65">
								<?
								$color_id_arr = array_unique(explode(",", $plan_info[0][csf('color_id')]));
								$all_color = "";
								foreach ($color_id_arr as $color_id)
								{
									$all_color .= $color_library[$color_id] . ",";
								}
								$all_color = chop($all_color, ",");
								echo $all_color;
								?>
							</td>
							<td width="50"><? echo $plan_info[0][csf('machine_dia')] . "X" . $plan_info[0][csf('machine_gg')]; ?></td>
							<td width="10"><? echo $plan_info[0][csf('no_of_ply')];?></td>
							<td width="10"><? echo $plan_info[0][csf('fabric_dia')];?></td>
							<td width="10"><? echo $plan_info[0][csf('fin_gsm')];?></td>
							<td width="10" align="center"><? echo $plan_info[0][csf('stitch_length')];?></td>
							<td width="50"><? echo $color_range[$plan_info[0][csf('color_range')]]; ?></td>
							<td width="50" align="right"><? echo number_format($prog_distriqty, 2); ?></td>
							<td width="50"><? echo $plan_info[0][csf('remarks')]; ?></td>
						</tr>
						<?
						//$total_distribution_qty += $row[csf('distribution_qnty')];
						$total_distribution_qty += $prog_distriqty;

						if($machineid!="")
						{
							$machineSl++;
						}
					}
					?>
					<tfoot>
						<th colspan="11" align="right"><b>Total</b></th>
						<th style="text-align: right;"><? echo number_format($total_distribution_qty, 2); ?></th>
						<th>&nbsp;</th>
					</tfoot>
				</tbody>
			</table>
			<br>
			<span> Advice:  <? echo $plan_info[0][csf('advice')]; ?> </span>
			<div style="width:100%; float:left;padding-top:10px;">
				<?
				$sql_stripe_feeder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=".$program_id." and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder");
				if (count($sql_stripe_feeder) > 0)
				{
					?>
					<table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="6" align="center">Stripe Measurement Information:</th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Combo Color</th>
								<th width="100">Stripe Color</th>
								<th width="100">Measurement </th>
								<th width="50">Uom</th>
								<th width="100">No Of Feeder</th>
							</tr>
						</thead>

						<tbody>
							<?
							$i = 1;
							$total_feeder = 0;
							foreach ($sql_stripe_feeder as $row)
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="100"><? echo $color_library[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $color_library[$row[csf('stripe_color_id')]]; ?></td>
									<td width="100" align="center">
										<?
										echo number_format($row[csf('measurement')], 2);
										$total_measurement += $row[csf('measurement')];
										?>
									</td>
									<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
									<td width="100" align="center">
										<? echo number_format($row[csf('no_of_feeder')], 0);
										$total_feeder += $row[csf('no_of_feeder')]; ?>
									</td>
								</tr>
								<?
								$i++;
							}
							?>
						</tbody>

						<tfoot>
							<th colspan="3" align="right"><b>Total</b></th>
							<th style="text-align: center;"><? echo number_format($total_measurement, 0); ?></th>
							<th>&nbsp;</th>
							<th style="text-align: center;"><? echo number_format($total_feeder, 0); ?></th>
						</tfoot>
					</table>
					<?
				}

				$sql_collar_cuff_dtls = sql_select("select body_part_id, grey_size, finish_size, qty_pcs, needle_per_cm from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id=$program_id");

				if (count($sql_collar_cuff_dtls) > 0)
				{
					?>
					<table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="6" align="center">Collar & Cuff Measurement Information:</th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Body Part</th>
								<th width="100">Grey Size</th>
								<th width="100">Finish Size</th>
								<th width="50">Qty. Pcs</th>
								<th width="100">Needle Per CM</th>
							</tr>
						</thead>

						<tbody>
							<?
							$k = 1;
							$total_cuff_qty = 0;
							foreach ($sql_collar_cuff_dtls as $cuff_row)
							{
								if ($k % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>">
									<td width="50" align="center"><? echo $k; ?></td>
									<td width="100" align="center"><? echo $body_part[$cuff_row[csf('body_part_id')]]; ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('grey_size')]; ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('finish_size')]; ?></td>
									<td width="50" align="right"><? echo number_format($cuff_row[csf('qty_pcs')], 0); ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('needle_per_cm')]; ?></td>
								</tr>
								<?
								$total_qty_pcs += $cuff_row[csf('qty_pcs')];
								$k++;
							}
							?>
						</tbody>

						<tfoot>
							<th colspan="4" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($total_qty_pcs,0);?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<?
				}
				?>
			</div>

			<div style="width:100%; float:left; padding-top:20px;">

				<?
				$sql_stripe_colorwise = sql_select("select a.stripe_color_id, a.no_of_feeder,sum(b.fabreqtotkg) as fabreqtotkg , max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.stripe_color_id, a.no_of_feeder");

				if (count($sql_stripe_colorwise) > 0)
				{

					?>
					<table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="5" align="center">Colour Wise Quantity</th>
							</tr>
							<tr>
								<th width="100">Stripe Color</th>
								<th width="100">Measurement</th>
								<th width="100">UOM</th>
								<th width="100">Total Feeder</th>
								<th width="100">Quantity(Kg)</th>
							</tr>
						</thead>

						<tbody>
							<?
							$y = 1;
							foreach ($sql_stripe_colorwise as $colorwise_row)
							{
								if ($y % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="100"><? echo $color_library[$colorwise_row[csf('stripe_color_id')]]; ?></td>
									<td width="100" align="center"><? echo number_format($colorwise_row[csf('measurement')], 2);?></td>
									<td width="100" align="center"><? echo $unit_of_measurement[$colorwise_row[csf('uom')]]; ?></td>
									<td width="100" align="center"><? echo number_format($colorwise_row[csf('no_of_feeder')], 0);?></td>
									<td width="100" align="right"><? echo number_format($colorwise_row[csf('fabreqtotkg')], 0);?></td>
								</tr>
								<?
								$y++;
							}
							?>

						</tbody>
					</table>
					<?
				}

				$sql_count_feed = "select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=$program_id and status_active=1 and is_deleted=0 order by seq_no";
				$data_array_count_feed = sql_select($sql_count_feed);
				if(count($data_array_count_feed)>0)
				{
					?>
					<table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="4" align="center">Count Feeding</th>
							</tr>
							<tr>
								<th width="50">Seq. No</th>
								<th width="100">Count</th>
								<th width="100">Feeding</th>
								<th width="100">Percentage</th>
							</tr>
						</thead>

						<tbody>
							<?
							$feeding_arr = array(1 => 'Knit', 2 => 'Binding', 3 => 'Loop');
							$j=1;
							foreach ($data_array_count_feed as $count_feed_row)
							{
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr>
									<td width="50" align="center"><? echo $count_feed_row[csf('seq_no')]; ?></td>
									<td width="100" align="center"><? echo $count_arr[$count_feed_row[csf('count_id')]];?></td>
									<td width="100"><? echo $feeding_arr[$count_feed_row[csf('feeding_id')]];?></td>
									<td width="100">&nbsp;</td>
								</tr>
								<?
								$j++;
							}
							?>
						</tbody>
					</table>
					<?
				}
				?>
			</div>

			<div style="width:100%; float:left;padding-top:10px;">
				<?
				$sql_cam_design = "select id,cmd1, cmd2, cmd3, cmd4, cmd5, cmd6, cmd7, cmd8, cmd9, cmd10, cmd11, cmd12, cmd13, cmd14, cmd15, cmd16, cmd17, cmd18, cmd19, cmd20, cmd21, cmd22, cmd23, cmd24 from ppl_planning_cam_design_dtls where dtls_id=$program_id and status_active=1 and is_deleted=0 order by id";
				$data_cam_design = sql_select($sql_cam_design);
				if (count($data_cam_design) > 0)
				{

					?>
					<table width="100%" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="25" align="center">Cam Design Information</th>
							</tr>
							<tr>
								<th width="4%">SL</th>
								<?
								for ($i=1; $i<=24; $i++)
								{
									?>
									<th width="4%"><? echo $i; ?></th>
									<?
								}
								?>
							</tr>
						</thead>
					</table>

					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"
					id="tbl_came_design">
					<tbody>
						<?
						$sl=1;
						foreach ($data_cam_design as $row)
						{
							if ($sl % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="4%" align="center"><? echo $sl; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd1')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd2')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd3')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd4')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd5')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd6')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd7')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd8')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd9')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd10')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd11')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd12')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd13')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd14')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd15')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd16')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd17')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd18')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd19')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd20')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd21')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd22')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd23')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd24')]; ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</tbody>
				</table>
				<?
			}
			?>
			</div>



			<!--- Needle layout --->
			<div  style="width:100%; float:left; padding-top: 20px;">
				<style type="text/css">
				    #needle-layout{
				        border-collapse:collapse;
				        table-layout:fixed;
				        width:600pt;
				        font-size: 20px;
				    }
				</style>
				<?
				$sql_needle_layout = "select PLAN_ID, PROGRAM_NO, DIAL, CYLINDER, DIAL_ROW1, DIAL_ROW2, NO_OF_FEEDER, CYLINDER_ROW1, CYLINDER_ROW2, CYLINDER_ROW3, CYLINDER_ROW4, YARN_ENDS, LFA, YARN_TENSION, GREY_GSM, T_DRY_WEIGHT, T_DRY_WIDTH, RPM, F_ROLL_WIDTH, LAID_WIDTH,ACTIVE_FEEDER, REV_PER_KG, DIAL_HEIGHT from ppl_planning_needle_layout where PROGRAM_NO=$program_id AND STATUS_ACTIVE=1 AND IS_DELETED=0";

				$data_needle_layout = sql_select($sql_needle_layout);

				if (count($data_needle_layout) > 0)
				{
					foreach ($data_needle_layout as $row)
					{
						if($row['DIAL_ROW1']!="")
						{
							$dial_row1_data_arr = explode("__", $row['DIAL_ROW1']);

							$dial_row1col1 = ($dial_row1_data_arr[0]!="")?$dial_row1_data_arr[0]:"";
							$dial_row1col2 = ($dial_row1_data_arr[1]!="")?$dial_row1_data_arr[1]:"";
							$dial_row1col3 = ($dial_row1_data_arr[2]!="")?$dial_row1_data_arr[2]:"";
							$dial_row1col4 = ($dial_row1_data_arr[3]!="")?$dial_row1_data_arr[3]:"";
							$dial_row1col5 = ($dial_row1_data_arr[4]!="")?$dial_row1_data_arr[4]:"";
							$dial_row1col6 = ($dial_row1_data_arr[5]!="")?$dial_row1_data_arr[5]:"";
						}

						if($row['DIAL_ROW2']!="")
						{
							$dial_row2_data_arr = explode("__", $row['DIAL_ROW2']);

							$dial_row2col1 = ($dial_row2_data_arr[0]!="")?$dial_row2_data_arr[0]:"";
							$dial_row2col2 = ($dial_row2_data_arr[1]!="")?$dial_row2_data_arr[1]:"";
							$dial_row2col3 = ($dial_row2_data_arr[2]!="")?$dial_row2_data_arr[2]:"";
							$dial_row2col4 = ($dial_row2_data_arr[3]!="")?$dial_row2_data_arr[3]:"";
							$dial_row2col5 = ($dial_row2_data_arr[4]!="")?$dial_row2_data_arr[4]:"";
							$dial_row2col6 = ($dial_row2_data_arr[5]!="")?$dial_row2_data_arr[5]:"";
						}

						if($row['NO_OF_FEEDER']!="")
						{
							$no_of_feeder_data_arr = explode("__", $row['NO_OF_FEEDER']);

							$no_of_feeder_col1 = ($no_of_feeder_data_arr[0]!="")?$no_of_feeder_data_arr[0]:"";
							$no_of_feeder_col2 = ($no_of_feeder_data_arr[1]!="")?$no_of_feeder_data_arr[1]:"";
							$no_of_feeder_col3 = ($no_of_feeder_data_arr[2]!="")?$no_of_feeder_data_arr[2]:"";
							$no_of_feeder_col4 = ($no_of_feeder_data_arr[3]!="")?$no_of_feeder_data_arr[3]:"";
							$no_of_feeder_col5 = ($no_of_feeder_data_arr[4]!="")?$no_of_feeder_data_arr[4]:"";
							$no_of_feeder_col6 = ($no_of_feeder_data_arr[5]!="")?$no_of_feeder_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW1']!="")
						{
							$cylinder_row1_data_arr = explode("__", $row['CYLINDER_ROW1']);

							$cylinder_row1col1 = ($cylinder_row1_data_arr[0]!="")?$cylinder_row1_data_arr[0]:"";
							$cylinder_row1col2 = ($cylinder_row1_data_arr[1]!="")?$cylinder_row1_data_arr[1]:"";
							$cylinder_row1col3 = ($cylinder_row1_data_arr[2]!="")?$cylinder_row1_data_arr[2]:"";
							$cylinder_row1col4 = ($cylinder_row1_data_arr[3]!="")?$cylinder_row1_data_arr[3]:"";
							$cylinder_row1col5 = ($cylinder_row1_data_arr[4]!="")?$cylinder_row1_data_arr[4]:"";
							$cylinder_row1col6 = ($cylinder_row1_data_arr[5]!="")?$cylinder_row1_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW2']!="")
						{
							$cylinder_row2_data_arr = explode("__", $row['CYLINDER_ROW2']);

							$cylinder_row2col1 = ($cylinder_row2_data_arr[0]!="")?$cylinder_row2_data_arr[0]:"";
							$cylinder_row2col2 = ($cylinder_row2_data_arr[1]!="")?$cylinder_row2_data_arr[1]:"";
							$cylinder_row2col3 = ($cylinder_row2_data_arr[2]!="")?$cylinder_row2_data_arr[2]:"";
							$cylinder_row2col4 = ($cylinder_row2_data_arr[3]!="")?$cylinder_row2_data_arr[3]:"";
							$cylinder_row2col5 = ($cylinder_row2_data_arr[4]!="")?$cylinder_row2_data_arr[4]:"";
							$cylinder_row2col6 = ($cylinder_row2_data_arr[5]!="")?$cylinder_row2_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW3']!="")
						{
							$cylinder_row3_data_arr = explode("__", $row['CYLINDER_ROW3']);

							$cylinder_row3col1 = ($cylinder_row3_data_arr[0]!="")?$cylinder_row3_data_arr[0]:"";
							$cylinder_row3col2 = ($cylinder_row3_data_arr[1]!="")?$cylinder_row3_data_arr[1]:"";
							$cylinder_row3col3 = ($cylinder_row3_data_arr[2]!="")?$cylinder_row3_data_arr[2]:"";
							$cylinder_row3col4 = ($cylinder_row3_data_arr[3]!="")?$cylinder_row3_data_arr[3]:"";
							$cylinder_row3col5 = ($cylinder_row3_data_arr[4]!="")?$cylinder_row3_data_arr[4]:"";
							$cylinder_row3col6 = ($cylinder_row3_data_arr[5]!="")?$cylinder_row3_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW4']!="")
						{
							$cylinder_row4_data_arr = explode("__", $row['CYLINDER_ROW4']);

							$cylinder_row4col1 = ($cylinder_row4_data_arr[0]!="")?$cylinder_row4_data_arr[0]:"";
							$cylinder_row4col2 = ($cylinder_row4_data_arr[1]!="")?$cylinder_row4_data_arr[1]:"";
							$cylinder_row4col3 = ($cylinder_row4_data_arr[2]!="")?$cylinder_row4_data_arr[2]:"";
							$cylinder_row4col4 = ($cylinder_row4_data_arr[3]!="")?$cylinder_row4_data_arr[3]:"";
							$cylinder_row4col5 = ($cylinder_row4_data_arr[4]!="")?$cylinder_row4_data_arr[4]:"";
							$cylinder_row4col6 = ($cylinder_row4_data_arr[5]!="")?$cylinder_row4_data_arr[5]:"";
						}

						if($row['YARN_ENDS']!="")
						{
							$yarn_ends_data_arr = explode("__", $row['YARN_ENDS']);

							$yarn_ends_col1 = ($yarn_ends_data_arr[0]!="")?$yarn_ends_data_arr[0]:"";
							$yarn_ends_col2 = ($yarn_ends_data_arr[1]!="")?$yarn_ends_data_arr[1]:"";
							$yarn_ends_col3 = ($yarn_ends_data_arr[2]!="")?$yarn_ends_data_arr[2]:"";
							$yarn_ends_col4 = ($yarn_ends_data_arr[3]!="")?$yarn_ends_data_arr[3]:"";
							$yarn_ends_col5 = ($yarn_ends_data_arr[4]!="")?$yarn_ends_data_arr[4]:"";
						}

						if($row['LFA']!="")
						{
							$lfa_data_arr = explode("__", $row['LFA']);

							$lfa_col1 = ($lfa_data_arr[0]!="")?$lfa_data_arr[0]:"";
							$lfa_col2 = ($lfa_data_arr[1]!="")?$lfa_data_arr[1]:"";
							$lfa_col3 = ($lfa_data_arr[2]!="")?$lfa_data_arr[2]:"";
							$lfa_col4 = ($lfa_data_arr[3]!="")?$lfa_data_arr[3]:"";
							$lfa_col5 = ($lfa_data_arr[4]!="")?$lfa_data_arr[4]:"";
						}

						if($row['YARN_TENSION']!="")
						{
							$yarn_tension_data_arr = explode("__", $row['YARN_TENSION']);

							$yarn_tension_col1 = ($yarn_tension_data_arr[0]!="")?$yarn_tension_data_arr[0]:"";
							$yarn_tension_col2 = ($yarn_tension_data_arr[1]!="")?$yarn_tension_data_arr[1]:"";
							$yarn_tension_col3 = ($yarn_tension_data_arr[2]!="")?$yarn_tension_data_arr[2]:"";
							$yarn_tension_col4 = ($yarn_tension_data_arr[3]!="")?$yarn_tension_data_arr[3]:"";
							$yarn_tension_col5 = ($yarn_tension_data_arr[4]!="")?$yarn_tension_data_arr[4]:"";
						}
					?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  id="needle-layout">

					        <tr height='21'>
						        <td colspan='8' height='21' width='237' style='background-color: #00b0f0; text-align: center;'>Needle Layout</td>
						    </tr>
						    <tr height='20'>
						        <td colspan="2" height='20' style='background-color: #ffcc00;'>Dial</td>
						        <td colspan="2">
						        	<? echo $row['DIAL'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan="2" height='20' style='background-color: #ffcc00;'>Cylinder</td>
						        <td colspan="2">
						        	<? echo $row['CYLINDER'];?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td rowspan='7' style="vertical-align: middle;transform: rotate(270deg);">
						        	Cam Setting
						        </td>
						        <td rowspan='2' align="center">Dial</td>
						        <td align="center">
						        	<? echo $dial_row1col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row1col6; ?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td height='20' align="center">
						        	<? echo $dial_row2col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col6; ?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td height='20' style='text-align: center;'>No Of Feeder</td>
						        <td align="center">
						        	<? echo $no_of_feeder_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $no_of_feeder_col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td rowspan='4'> <div style='vertical-align: middle; transform: rotate(270deg);'> Cylinder </dive></td>
						        <td align="center">
						        	<? echo $cylinder_row1col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row1col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center">
						        	<? echo $cylinder_row2col1; ?>
						        </td>

						        <td align="center">
						        	<? echo $cylinder_row2col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row2col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center">
						        	<? echo $cylinder_row3col1; ?>
						        </td>

						        <td align="center">
						        	<? echo $cylinder_row3col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row3col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center">
						        	<? echo $cylinder_row4col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col2; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col3; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col4; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col5; ?>
						        </td>
						        <td align="center">
						        	<? echo $cylinder_row4col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #c2d69a;'>Yarn Ends</td>
						        <td align="center">
						        	<? echo  $yarn_ends_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_ends_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #75923c;'>LFA</td>
						        <td align="center">
						        	<? echo  $lfa_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo  $lfa_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #ccc0da;'>Yarn Tension</td>
						        <td align="center">
						        	<? echo  $yarn_tension_col1; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col2; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col3; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col4; ?>
						        </td>
						        <td align="center">
						        	<? echo  $yarn_tension_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='20' style="background-color: #31849b;">Grey GSM</td>
						        <td align="center">
						        	<? echo $row['GREY_GSM'];?>
						        </td>
						        <td style="background-color: #b8cce4;">T.Dry Weight</td>
						        <td align="center">
						        	<? echo $row['T_DRY_WEIGHT'];?>
						        </td>
						        <td style="background-color: #bfbfbf;">T.Dry Width</td>
						        <td align="center">
						        	<? echo $row['T_DRY_WIDTH'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style="background-color: #93cddd;">RPM</td>
						        <td align="center">
						        	<? echo $row['RPM'];?>
						        </td>
						        <td style="background-color: #538ed5;">F.Roll Width</td>
						        <td align="center">
						        	<? echo $row['F_ROLL_WIDTH'];?>
						        </td>
						        <td style="background-color: #a5a5a5;">Laid Width</td>
						        <td align="center">
						        	<? echo $row['LAID_WIDTH'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style="background-color: #93cddd;">Total Active Feeder</td>
						        <td align="center">
						        	<? echo $row['ACTIVE_FEEDER'];?>
						        </td>
						        <td style="background-color: #538ed5;">Rev per Kg</td>
						        <td align="center">
						        	<? echo $row['REV_PER_KG'];?>
						        </td>
						        <td style="background-color: #a5a5a5;">Dial Height </td>
						        <td align="center">
						        	<? echo $row['DIAL_HEIGHT'];?>
						        </td>
						    </tr>

						</table>
					<?
					}
				}
				?>

			</div>

		<!-- Needle layout end -->

		<div style="width:100%; float:left; padding-top:30px;">
			<table style="width:100%; float:left;" class="rpt_table">
				<tr>
					<td rowspan="3" valign="top">Special Instruction:</td>
					<td>Any type of fabric faults is not acceptable.(Patta,Sinker/Needle Mark,Loop/Hole,Tara,Fly,Oil Sport )</td>
				</tr>
				<tr>
					<td>Factory must mention the Program Number on the Delivery Challan and Bill/ Invoice.</td>
				</tr>
				<tr>
					<td>Roll marking must be done with Parmanent marker</td>
				</tr>
			</table>
		</div>

		<div style="width:100%; float:left; padding-top:20px;">
			<table style="width:100%; float:left;">
				<tr>
					<td>Received & Accepted by: </td>
					<td>&nbsp;</td>
					<td>Prepared By: </td>
					<td>&nbsp;</td>
					<td>Authorized Signature: </td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</table>
	</div>
	</div>
	<?
}



if ($action == "browse_desc_popup_action")
{
	echo load_html_head_contents("Description Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(productId,countId,sl,prodName)
		{
			$('#hidden_prod_id').val(productId);
			$('#hidden_count_id').val(countId);
			$('#hidden_prod_name').val(prodName);
			$('#hidden_row_sl').val(sl);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<?

	$supplier_dtls = return_library_array("select id, short_name from  lib_supplier", 'id', 'short_name');
	$yarnd_count_lib = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');

	//for product information
	$sql_product = "SELECT id AS ID, supplier_id AS SUPPLIER_ID, lot AS LOT, product_name_details AS PRODUCT_NAME,yarn_count_id as YARN_COUNT_ID FROM product_details_master WHERE id IN(SELECT item_id FROM inv_material_allocation_mst WHERE po_break_down_id = '".$salesId."' AND item_category=1 AND status_active=1 AND is_deleted=0)";
	//echo $sql_product;
	$sql_product_rslt = sql_select($sql_product);
	$product_data_arr = array();
	foreach($sql_product_rslt as $row)
	{
		$product_data_arr[$row['ID']]['supplier_id'] = $row['SUPPLIER_ID'];
		$product_data_arr[$row['ID']]['lot'] = $row['LOT'];
		$product_data_arr[$row['ID']]['product_name'] = $row['PRODUCT_NAME'];
		$product_data_arr[$row['ID']]['yarn_count_id'] = $row['YARN_COUNT_ID'];
	}
	unset($sql_product_rslt);

	$sql = "SELECT a.id AS ID, a.job_no AS SALES_ORDER_NO, a.po_break_down_id AS SALES_ORDER_ID, a.item_id AS ITEM_ID, a.qnty AS QTY, a.allocation_date AS ALLOCATION_DATE, a.is_dyied_yarn AS IS_DYIED_YARN, a.REMARKS FROM inv_material_allocation_mst a WHERE a.po_break_down_id = '".$salesId."' AND a.item_category=1 AND a.status_active=1";
	// AND  entry_form=475
	//echo $sql;
	$sql_rslt = sql_select($sql);
	$sales_id_arr = array();
	foreach($sql_rslt as $row)
	{
		$sales_id_arr[$row['SALES_ORDER_ID']] = $row['SALES_ORDER_ID'];
	}

	//for sales order infomation
	$sql_fab_sales = sql_select("SELECT id AS ID, within_group as WITHIN_GROUP FROM fabric_sales_order_mst WHERE id IN(".implode(',',$sales_id_arr).")");
	$fab_sales_arr = array();
	foreach($sql_fab_sales as $row)
	{
		$fab_sales_arr[$row['ID']]['within_group'] = $row['WITHIN_GROUP'];
	}
	//end for sales order infomation

	$data_arr = array();
	$prod_id_arr = array();
	foreach($sql_rslt as $row)
	{
		$is_dyied_yarn = 0;
		if($row['IS_DYIED_YARN'] == 1)
		{
			$is_dyied_yarn = 1;
		}

		$prod_id_arr[$row['ITEM_ID']] = $row['ITEM_ID'];

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['id'] = $row['ID'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['allocation_date'] = $row['ALLOCATION_DATE'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['qty'] = $row['QTY'];

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['company'] = $company_dtls[$company_id];

		//for buyer
		if($fab_sales_arr[$row['SALES_ORDER_ID']]['within_group'] == 1)
		{
			$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['customer'] = $company_short_dtls[$customer_id];
		}
		else
		{
			$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['customer'] = $buyer_dtls[$customer_id];
		}

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['customer_buyer'] = $buyer_dtls[$customer_buyer_id];

		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['supplier_id'] = $supplier_dtls[$product_data_arr[$row['ITEM_ID']]['supplier_id']];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['lot'] = $product_data_arr[$row['ITEM_ID']]['lot'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['yarn_count_id'] = $product_data_arr[$row['ITEM_ID']]['yarn_count_id'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['product_id'] = $row['ITEM_ID'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['product_name'] = $product_data_arr[$row['ITEM_ID']]['product_name'];
		$data_arr[$is_dyied_yarn][$row['SALES_ORDER_NO']][$row['ITEM_ID']]['remarks'] = $row['REMARKS'];
	}
	unset($sql_rslt);


	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="430" class="rpt_table" >
		<thead>
			<th width="40">SL</th>
			<th width="80">Count</th>
			<th width="120">Composition</th>
			<th width="120">Supplier</th>
			<th>Lot</th>
		</thead>
	</table>
	<div style="width:430px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="410" class="rpt_table"
			id="tbl_list_search">
			  <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes"
				value="">
				<input type="hidden" name="hidden_count_id" id="hidden_count_id" class="text_boxes"
				value="">
			<input type="hidden" name="hidden_prod_name" id="hidden_prod_name" class="text_boxes"
				value="">
			<input type="hidden" name="hidden_row_sl" id="hidden_row_sl" class="text_boxes"
				value="">

			<?
			$i = 1;
			foreach ($data_arr[0] as $sales_order_no=>$sales_order_no_arr)
			{
				foreach ($sales_order_no_arr as $product_id=>$row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";


					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value('<? echo $row['product_id']; ?>','<? echo $row['yarn_count_id']; ?>','<? echo $sl; ?>','<? echo $row['product_name']; ?>')">
							<td width="40" align="center"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo $yarnd_count_lib[$row['yarn_count_id']]; ?></p></td>
							<td width="120" align="center"><p><? echo $row['product_name']; ?></p></td>
							<td width="120" style="max-width: 200px;"><p><? echo $row['supplier_id']; ?></p></td>
							<td><p><? echo  $row['lot']; ?> <input type="hidden" name="prod_id[]" value="<? echo $row['product_id'];?>" /></p></td>
					</tr>
					<?
					$i++;


				}
			}


			?>
		</table>
	</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


?>