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

if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$datediff = datediff('d', str_replace("'", "", $txt_date_from), str_replace("'", "", $txt_date_to));
	if (str_replace("'", "", $cbo_floor_id) == 0) $floor_cond = ""; else $floor_cond = " and floor_id=$cbo_floor_id";
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$machine_data_array = array();
	$machine_data = sql_select("select id, floor_id, machine_no, dia_width, gauge, prod_capacity from lib_machine_name where company_id=$cbo_company_name and category_id=1 and status_active=1 and is_deleted=0 $floor_cond order by floor_id, dia_width");//, cast(machine_no as unsigned)
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
			$query = "select a.fabric_desc, a.gsm_weight, b.fabric_dia, b.color_id, b.color_range, b.start_date, b.end_date, b.stitch_length from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id=$program_id";
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
		http.open("POST", "program_wise_mc_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_show_details_reponse;
		//show_list_view(cbo_company_id, 'booking_item_details', 'list_container_fabric_desc', 'requires/program_wise_mc_entry_controller', '');
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
		var page_link = 'program_wise_mc_entry_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID;
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
		var page_link = 'program_wise_mc_entry_controller.php?action=internal_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID;
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
		var page_link = 'program_wise_mc_entry_controller.php?action=booking_no_search_popup&companyID=' + companyID;
		var title = 'Booking Search';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0', '../../');
		emailwindow.onclose = function () {
			var theform = this.contentDoc.forms[0];
			var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;

			$('#txt_booking_no').val(booking_no);
		}
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

	$machineLibArr = return_library_array("select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 order by seq_no", 'id', 'machine_no');
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$print_report_format =return_field_value("format_id"," lib_report_template","template_name ='".$company_name."' and module_id=4 and report_id=88 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",",$print_report_format);

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
	else
	{
		$program_info_format_id = 272;
	}
	?>
	<script>
		function openpage_machine2(dataStr) {
		
			

			var datas=dataStr.split('_');

			var slrow= datas[0];
			var planId= datas[1];
			var update_dtls_id= datas[2];
			var companyID= datas[3];
			var cbo_knitting_party= datas[4];
			var txt_machine_gg= datas[5];
			var txt_machine_dia= datas[6];
			var txt_program_qnty= datas[7];

			var save_string = $('#save_data_'+slrow).val();
			var updated_id = $('#update_id_'+slrow).val();

			var page_link = 'requires/program_wise_mc_entry_controller.php?action=machine_info_popup&save_string=' + save_string + '&planId='+planId + '&update_dtls_id=' + update_dtls_id + '&companyID='+companyID + '&cbo_knitting_party='+cbo_knitting_party + '&txt_machine_gg='+txt_machine_gg  + '&txt_machine_dia=' + txt_machine_dia  + '&txt_program_qnty='+txt_program_qnty+ '&updated_id='+updated_id ;
			var title = 'Machine Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=360px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_machine_no = this.contentDoc.getElementById("hidden_machine_no").value;
				var hidden_machine_id = this.contentDoc.getElementById("hidden_machine_id").value;
				var save_string = this.contentDoc.getElementById("save_string").value;
				var updateId = this.contentDoc.getElementById("updateId").value;
				var hidden_machine_capacity = this.contentDoc.getElementById("hidden_machine_capacity").value;
				var hidden_distribute_qnty = this.contentDoc.getElementById("hidden_distribute_qnty").value;
				var hidden_min_date = this.contentDoc.getElementById("hidden_min_date").value;
				var hidden_max_date = this.contentDoc.getElementById("hidden_max_date").value;

				$('#txt_machine_no_'+slrow).val(hidden_machine_no);
				$('#machine_id_'+slrow).val(hidden_machine_id);
				$('#save_data_'+slrow).val(save_string);
				$('#update_id_'+slrow).val(updateId);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#distribution_qnty_'+slrow).val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				days_req();
			}
		}
	</script>
	<?
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

			/*$sql = " SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,$po_break_down_id_cast, a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no=c.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and c.fabric_source in(1,2) and a.booking_without_order=0 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, a.po_job_no
			union all
			select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, '' as po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no  order by dia";*/

			$sql = " SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,$po_break_down_id_cast, a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,d.mst_id as plan_id,d.dtls_id as program_no,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia  from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c, ppl_planning_entry_plan_dtls d,ppl_planning_info_entry_dtls e where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no and d.po_id=a.id and d.dtls_id=e.id and d.mst_id=e.mst_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and c.fabric_source in(1,2) and a.booking_without_order=0 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, a.po_job_no,d.mst_id,d.dtls_id,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia 
			";
				/*$sql="select c.po_id as id,a.company_id,c.within_group,null as job_no,null as sales_booking_no ,null as booking_id, null as buyer_id, null as style_ref_no,null as booking_date,null as po_job_no, sum(c.program_qnty) as program_qnty,
				c.body_part_id,c.color_type_id,c.fabric_desc,c.determination_id,c.gsm_weight, c.dia, c.width_dia_type, c.pre_cost_fabric_cost_dtls_id, c.mst_id as plan_id,c.dtls_id as program_no,b.knitting_party,b.machine_gg,b.machine_dia 
				from   ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c ,wo_booking_mst d 
				where a.id=b.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id and c.booking_no=d.booking_no $buyer_id_cond_to $within_group_cond 
				and c.company_id=$company_name 
				$date_cond2 and d.fabric_source in(1,2) 
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by c.po_id,a.company_id,c.within_group,
				c.body_part_id,c.color_type_id,c.fabric_desc,c.determination_id,c.gsm_weight, c.dia, c.width_dia_type, c.pre_cost_fabric_cost_dtls_id, c.mst_id,c.dtls_id,b.knitting_party,b.machine_gg,b.machine_dia ";
				*/

			/*union all
			select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, '' as po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no  order by dia*/
		}
		else
		{
			$sql = "SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,(select c.po_break_down_id from wo_booking_mst c where a.sales_booking_no = c.booking_no) po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no order by b.dia";
			//echo $sql;
		}
	}
		
	//echo $sql;
	
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
		$program_no_arr[] = "'".$value[csf('program_no')]."'";
		
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
	$program_data_array = array();
	$booking_program_arr = array();
	if(!empty($sales_booking_arr))
	{ 
		$pre_cost_sql = sql_select("select a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type
		from wo_booking_mst a inner join wo_booking_dtls b on b.booking_no = a.booking_no
		where a.booking_no in(".implode(",",$sales_booking_arr).")
		group by a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type");
	}
	
	foreach ($pre_cost_sql as $row)
	{
		$desc = $row[csf('construction')] . " " . $row[csf('copmposition')];
		$booking_data_array[$row[csf('booking_no')]][$desc][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_type')]] = $row[csf('pre_cost_fabric_cost_dtls_id')];
	}
	
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


	if(!empty($program_no_arr))
	{
		$programNos = implode(",",$program_no_arr);
	    $programNos=implode(",",array_filter(array_unique(explode(",",$programNos))));
	    if($programNos!="")
	    {
	        $programNos=explode(",",$programNos);  
	        $programNos_chnk=array_chunk($programNos,999);
	        $programNos_cond=" and";
	        $programNos_cond2=" and";
	        foreach($programNos_chnk as $progNos)
	        {
	        	if($programNos_cond==" and")
	        	{
					$programNos_cond.="(f.dtls_id in(".implode(',',$progNos).")";
					$programNos_cond2.="(a.booking_id in(".implode(',',$progNos).")";
	        	}
				else
				{
					$programNos_cond.=" or f.dtls_id in(".implode(',',$progNos).")";
					$programNos_cond2.=" or a.booking_id in(".implode(',',$progNos).")";
				}
	        }
	        $programNos_cond.=")";
	        $programNos_cond2.=")";
	        //echo $programNos_cond;die;
	    }	
		//$programNos_cond = "and b.booking_no in(".implode(",",$sales_booking_arr).")";
	}

	$machine_info_sql=sql_select("SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,$po_break_down_id_cast, a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,d.mst_id as plan_id,d.dtls_id as program_no,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia,f.id as update_id,f.mst_id,f.dtls_id,f.machine_id,f.dia as mc_dia,f.capacity,f.distribution_qnty,f.no_of_days,f.start_date,f.end_date  from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c, ppl_planning_entry_plan_dtls d,ppl_planning_info_entry_dtls e,ppl_planning_info_machine_dtls f LEFT JOIN ppl_entry_machine_datewise g ON f.mst_id=g.mst_id and f.dtls_id=g.dtls_id where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no and d.po_id=a.id and d.dtls_id=e.id and d.mst_id=e.mst_id and f.dtls_id=e.id and d.dtls_id=f.dtls_id and f.mst_id=e.mst_id  $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and c.fabric_source in(1,2) and a.booking_without_order=0  and  f.status_active=1 and f.is_deleted=0  $programNos_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, a.po_job_no,d.mst_id,d.dtls_id,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia,f.id,f.mst_id,f.dtls_id,f.machine_id,f.dia,f.capacity,f.distribution_qnty,f.no_of_days,f.start_date,f.end_date 
			");


	//$machine_info_sql=sql_select("select a.id,a.mst_id,a.dtls_id,a.machine_id,a.dia,a.capacity,a.distribution_qnty,a.no_of_days,a.start_date,a.end_date from ppl_planning_info_machine_dtls a LEFT JOIN ppl_entry_machine_datewise b  ON a.mst_id=b.mst_id and a.dtls_id=b.dtls_id where a.status_active=1 and a.is_deleted=0 $programNos_cond ");


	foreach($machine_info_sql as $rows)
	{
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['mc_saved_string'].=$rows[csf('machine_id')]."_".$machineLibArr[$rows[csf('machine_id')]]."_".$rows[csf('capacity')]."_".$rows[csf('distribution_qnty')]."_".$rows[csf('no_of_days')]."_".$rows[csf('start_date')]."_".$rows[csf('end_date')]."_".$rows[csf('dtls_id')].",";
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['update_id']=$rows[csf('id')];
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['machine_id'].=$rows[csf('machine_id')].",";
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['machine_no'].=$machineLibArr[$rows[csf('machine_id')]].",";
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['distribution_qnty']+=$rows[csf('distribution_qnty')];
	}
	unset($machine_info_sql);


	//save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;




	$sql_production_qnty=sql_select("select a.booking_no,sum(b.grey_receive_qnty) as grey_receive_qnty from inv_receive_master a,pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.receive_basis=2 $programNos_cond2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
	foreach($sql_production_qnty as $rows)
	{
		$productionQntyArr[$rows[csf('booking_no')]]['grey_receive_qnty']=$rows[csf('grey_receive_qnty')];
	}
	unset($sql_production_qnty);


	if ($db_type == 0)
	{
		$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, group_concat(a.dtls_id) as prog_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number,a.status_active from ppl_planning_entry_plan_dtls a left join inv_receive_master b on a.id=b.booking_id where a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.is_revised=0 $sales_booking_cond  group by a.id,a.mst_id,booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number,a.status_active ";
	}
	else
	{
		//$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, listagg(a.dtls_id, ',') within group (order by a.dtls_id) as prog_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids, a.pre_cost_fabric_cost_dtls_id,a.status_active from ppl_planning_entry_plan_dtls a where a.is_sales=1 and a.is_revised=0 $sales_booking_cond group by a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,a.status_active";

		$sql_plan = "SELECT b.id,b.mst_id,b.booking_no, b.po_id, b.yarn_desc as job_dtls_id, b.body_part_id, b.fabric_desc, b.gsm_weight, b.dia,b.width_dia_type, b.color_type_id, listagg(b.dtls_id, ',') within group (order by b.dtls_id) as prog_no,sum(b.program_qnty) as program_qnty,b.sales_order_dtls_ids, b.pre_cost_fabric_cost_dtls_id,b.status_active,a.determination_id 
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
		

		//$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];

		//$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['prog_no'][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]][$rowPlan[csf('prog_no')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		//$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]]['sales_order_dtls_ids'] = $rowPlan[csf('sales_order_dtls_ids')];
		
		$program_data_array1[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]]['program'] .= $rowPlan[csf('prog_no')] . ",";
		$booking_program_arr[$rowPlan[csf('booking_no')]] .= $rowPlan[csf('prog_no')] . ",";

		// for sales order if within group no
		$sales_order_dtls_ids = explode(",",$rowPlan[csf('sales_order_dtls_ids')]);
		foreach ($sales_order_dtls_ids as $sales_dtls_row)
		{
			//$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];
			//$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['prog_no'] .= $rowPlan[csf('prog_no')].",";
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]][$rowPlan[csf('prog_no')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
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
			$is_approved_status_arr = return_library_array( "select booking_no, is_approved from wo_booking_mst where booking_no in ('".$booking_list."')",'booking_no','is_approved');
		}
		
		if(!empty($all_sales_booking_arr))
		{
			$job_no_array=array();
			$booking_list=implode(",", array_unique($all_sales_booking_arr));
			$sql_data=sql_select("select a.id, b.buyer_name,c.booking_no from wo_po_break_down a, wo_po_details_master b, wo_booking_dtls c where b.job_no=a.job_no_mst and a.id=c.po_break_down_id and c.booking_no in ('".$booking_list."') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				union all
				select 0 as id, buyer_id,booking_no from wo_non_ord_samp_booking_mst where booking_no in ('".$booking_list."') and status_active=1 and is_deleted=0");
			foreach ($sql_data as $row)
			{
				$job_no_array[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_name')];
			}
		}
		
		//for grey qty
		$sqlGreyQty = " SELECT a.id, a.sales_booking_no, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,b.pre_cost_fabric_cost_dtls_id, sum(b.grey_qty) as grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id $active_status_sql and a.company_id=$company_name ".where_con_using_array($salesMstIdArr,0,'a.id')." ".where_con_using_array($salesDtlsIdArr,0,'b.id')." 
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
						<th width="100">MC No</th>
						<th width="70">MC. Dist. Qty</th>
						<th width="70">Knitting Qty</th>
						<th>Balance Prog. Qnty
							<input type="hidden" name="action_type" id="action_type" value="<? echo $type; ?>"/>
						</th>
						
					</thead>
					<tbody>
						<?
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
							$programNo = $row[csf('program_no')];
							$productionQnty=$productionQntyArr[$programNo]['grey_receive_qnty'];
							
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

							$status = ($type == 1) ? 1 : 0;
							if($within_group == 1)
							{
								/*$program_qnty = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['program_qnty'];
								$plan_id = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['mst_id'];
								$prog_no = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['prog_no'];*/

								$program_qnty = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status][$programNo]['program_qnty'];

								$program_qnty = $row[csf('program_qnty')];
								//$plan_id = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['mst_id'];
								//$prog_no = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['prog_no'];

								//$prog_no = implode(",", $prog_no);
							}
							else
							{
								$sales_dtls_id = array_unique(explode(",",$sales_order_dtls_id));
								$program_qnty = 0;
								$prog_no='';
								//print_r($sales_dtls_id);
								foreach ($sales_dtls_id as $rows)
								{
									
									//$plan_id .= $program_data_sales_array[$rows][$status]['mst_id'].",";
									//$prog_no .= $program_data_sales_array[$rows][$status]['prog_no'].",";
									$program_qnty = $program_data_sales_array[$rows][$status][$programNo]['program_qnty'];
									
								}
							}



							$balance_qnty = number_format($grey_qty - $program_qnty,2,".","");
							$pre_cost_id = $booking_data_array[$sales_booking_no][$desc][$gsm][$dia][$color_type_id];
							if (($planning_status == 2 && $balance_qnty <= 0 && $grey_qty>0) || ($planning_status == 1 && (($within_group == 1)?$balance_qnty > 0:$balance_qnty > 0)))
							{
								if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								if (!in_array($dia, $dia_array)) {
									if ($k != 1) {
										?>
										<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
											<td colspan="14" align="right"><b>Sub Total</b></td>
											<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
											<td align="right">
												<b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
												<td align="right"><b><? //echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
												<td align="right"><b><? //echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
												<td align="right"><b><? //echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
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
											<td colspan="20">
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

									if($machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['update_id']>0)
									{

										$mc_save_string=$machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['mc_saved_string'];
										
										$mc_update_id=$machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['update_id'];

										$mc_machine_id=chop($machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['machine_id'],",");
										
										$mc_machine_no=chop($machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['machine_no'],",");
										
										$mc_machine_distribution_qnty=$machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['distribution_qnty'];

									}
									
									
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" >
											<!-- onClick="selected_row('<? echo $i; ?>','<? echo $status_arr[$approval_status]; ?>')" id="tr_<? echo $i; ?>" -->
												
											<?
											//$plan_id = implode(",",array_filter(array_unique(explode(",", chop($plan_id,",")))));
											?>
											<td width="40" align='center'><? echo $z; ?></td>
											
											<td width="60" align='center' id="prog_no_<? echo $i; ?>"><p>
												<?
												//echo rtrim($plan_id,", ");

												echo "<a href='##' onclick=\"generate_report2(" . $company_name . "," . $programNo . "," . $program_info_format_id . ")\">" . $programNo . "</a>";


												
												
												?>
											</p></td>
											<td id="booking_no_<? echo $i; ?>" align='center'><? echo $sales_booking_no; ?></td>
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
											<td align="right" width="100">
												<input type="text" name="txt_machine_no[]" id="txt_machine_no_<? echo $i; ?>" class="text_boxes"
													placeholder="Machine Entry Popup" style="width:100px;"
													onDblClick="openpage_machine2('<? echo $i.'_'.$row[csf('plan_id')].'_'.$programNo.'_'.$company_name.'_'.$row[csf('knitting_party')].'_'.$row[csf('machine_gg')].'_'.$row[csf('machine_dia')].'_'.$program_qnty; ?>');" value="<? echo $mc_machine_no; ?>" readonly/>
												<input type="hidden" name="machine_id[]" id="machine_id_<? echo $i; ?>" class="text_boxes" value="<? echo $mc_machine_id; ?>" readonly/>
												<input type="hidden" name="save_data[]" id="save_data_<? echo $i; ?>" class="text_boxes" value="<? echo $mc_save_string; ?>" readonly/>
												<input type="hidden" name="update_id[]" id="update_id_<? echo $i; ?>" class="text_boxes" value="<? echo $mc_update_id; ?>" readonly/>
											</td>

											<td align="right" width="70">
												<input type="text" name="distribution_qnty[]" id="distribution_qnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $mc_machine_distribution_qnty; ?>" style="width:60px;" readonly disabled/>
											</td>
											<td align="right" width="70">
												<? echo number_format($productionQnty, 2, '.', ''); ?>
											</td>


											<td align="right" id="ballance_qnty_<? echo $i; ?>"><? $balance_qnty=($program_qnty-$productionQnty); echo number_format($balance_qnty, 2, '.', ''); ?></td>
											


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
											<input type="hidden" name="reqsn_found_or_not[]" id="reqsn_found_or_not_<? echo $i; ?>"
											value="<? echo $reqsn_found_or_not; ?>"/>
											<input type="hidden" name="sales_order_dtls_id[]" id="sales_order_dtls_id<? echo $i; ?>"
											value="<? echo $sales_order_dtls_id; ?>"/>
											<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
											id="pre_cost_fabric_cost_dtls_id<? echo $i; ?>" value="<? echo $pre_cost_fabric_cost_dtls_id; ?>"/>
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
									$mc_machine_no="";
									$mc_machine_id="";
									$mc_update_id="";
									$mc_save_string="";
									$mc_machine_distribution_qnty ="";
								}

								if ($i > 1) {
									?>
									<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
										<td colspan="14" align="right"><b>Sub Total</b></td>
										<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? //echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? //echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? //echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>
									</tr>
									<?
								}
								?>
							</tbody>
							<tfoot>
								<th colspan="14" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<? echo $company_name; ?>"/></th>
								<th align="right"><? echo number_format($total_qnty, 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? //echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? //echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? //echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($grand_total_balance, 2, '.', ''); ?></th>
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

		$hidden_plan_id=str_replace("'", "", $hidden_plan_id);
		$hidden_prog_id=str_replace("'", "", $hidden_prog_id);
		
		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";

		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";

		$save_string = str_replace("'", "", $save_string);
		if ($save_string != "") {
			$save_string = explode(",", $save_string);
			for ($i = 0; $i < count($save_string); $i++) {
				$machine_wise_data = explode("_", $save_string[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
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
						$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "','" . $machine_dtls_id . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
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
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$machine_dtls_id = $machine_dtls_id + 1;
			}
		}
		
		/*oci_rollback($con);
		echo "5**0**0**".$data_array_color_wise_break_down;
		disconnect($con);
		die;*/

		//echo "10**insert into ppl_planning_info_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;

		if ($save_string != "") {
			if ($data_array_machine_dtls != "") {
				//echo "10**insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($rID) $flag = 1; else $flag = 0;
			}

			if ($data_array_machine_dtls_datewise != "") {
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID2 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1) {
					if ($rID2) $flag = 1; else $flag = 0;
				}
			}
		}

		
		/*echo "10**".$rID . "_" . $rID2 ."_".$flag;
		disconnect($con);
		die();*/

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $machine_dtls_id ."**". $hidden_plan_id . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $machine_dtls_id ."**". $hidden_plan_id . "**0";
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
		
 		$hidden_plan_id=str_replace("'", "", $hidden_plan_id);
		$hidden_prog_id=str_replace("'", "", $hidden_prog_id);

		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";
		//$field_array_machine_dtls_update = "machine_id*dia*capacity*distribution_qnty*no_of_days*start_date*end_date*updated_by*update_date";

		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";

		$save_string = str_replace("'", "", $save_string);
		if ($save_string != "") {
			$save_string = explode(",", $save_string);
			for ($i = 0; $i < count($save_string); $i++) {
				$machine_wise_data = explode("_", $save_string[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
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
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

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

						$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "','" . $machine_plan_id . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
					}
				}
			}
		}

		



		


		$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$hidden_prog_id", 0);
		if ($delete_datewise) $flag = 1; else $flag = 0;
		
		//echo "10**";
		$delete_machine = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$hidden_prog_id", 0);

		if ($flag == 1) {
			if ($delete_machine) $flag = 1; else $flag = 0;
		}

		if ($save_string != "") {
			if ($data_array_machine_dtls != "") {
				//echo"insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($flag == 1) {
					if ($rID) $flag = 1; else $flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "") {
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID2 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1) {
					if ($rID2) $flag = 1; else $flag = 0;
				}
			}
		}

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . $machine_dtls_id ."**". str_replace("'", "", $hidden_plan_id) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . $machine_dtls_id ."**". str_replace("'", "", $hidden_plan_id) . "**0";
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
		die;
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

	if ($db_type == 0)
	{
		if ($rID)
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
		if ($rID)
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
	$current_date = date("d-m-Y");
	$dataArray = sql_select("select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id=$determination_id and status_active=1 and is_deleted=0");

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
			var update_dtls_id = $('#update_dtls_id').val();
			var txt_program_qnty = $('#txt_program_qnty').val();
			var txt_machine_gg = $('#txt_machine_gg').val();
			var cbo_knitting_party = $('#cbo_knitting_party').val();

			var page_link = 'program_wise_mc_entry_controller.php?action=machine_info_popup&save_string=' + save_string + '&companyID=' + '<? echo $companyID; ?>' + '&txt_machine_dia=' + txt_machine_dia + '&update_dtls_id=' + update_dtls_id + '&txt_program_qnty='+txt_program_qnty + '&txt_machine_gg='+txt_machine_gg + '&cbo_knitting_party='+cbo_knitting_party;
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

			var booking_no='<? echo $booking_no; ?>';
			var desc='<? echo $desc; ?>';
			booking_no=encodeURIComponent(String(booking_no));
			desc=encodeURIComponent(String(desc));
			var page_link = 'program_wise_mc_entry_controller.php?action=color_info_popup&companyID=' + '<? echo $companyID; ?>' + '&job_id=' + '<? echo $job_id; ?>' + '&booking_no=' + booking_no + '&dia=' + '<? echo $dia; ?>' + '&hidden_color_id=' + hidden_color_id + '&program_color_id=' + program_color_id + '&gsm=' + '<?php echo $gsm; ?>' + '&body_part_id=' + '<? echo $body_part_id; ?>' + '&desc=' + desc + '&sales_order_dtls_id=' + '<?php echo $sales_order_dtls_id;?>' + '&width_dia_type=' + '<?php echo $fabric_type;?>'+"&plan_id="+"<?php echo $plan_id; ?>"+ '&prog_no=' + prog_no;
			var title = 'Color Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=640px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function ()
			{
        		var theform = this.contentDoc.forms[0];
        		var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;	
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;

        		$('#txt_color').val(hidden_color_no);
        		$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);
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
						$mandatory_field[] = $val;
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
				if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][282]);?>')
				{
					if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][282]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][282]);?>')==false)
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
			
			data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*cbo_location_name*hidden_advice_data*hidden_no_of_feeder_data*hidden_collarCuff_data*hidden_count_feeding_data*hidden_came_dsign_string_data*hdn_fab_desc*prog_hidden_dial*prog_hidden_cylinder*prog_hidden_dial_row1*prog_hidden_dial_row2*prog_hidden_no_of_feeder*prog_hidden_yarn_tension*prog_hidden_cylinder_row1*prog_hidden_cylinder_row2*prog_hidden_cylinder_row3*prog_hidden_cylinder_row4*prog_hidden_yarn_ends*prog_hidden_lfa*prog_hidden_grey_gsm*prog_hidden_tdry_weight*prog_hidden_tdry_width*prog_hidden_rpm*prog_hidden_froll_width*prog_hidden_laid_width*prog_hidden_active_feeder*prog_hidden_rev_per_kg*prog_hidden_dial_height*prog_needle_layout_data_have*prog_update_needle_layout_id*txt_batch_no*txt_no_of_ply*txt_tube_ref_no*hidden_color_wise_prog_data*hiddenProgramQnty*balanceProgramQnty', "../../") + '&companyID='+<? echo $companyID; ?>+
			'&gsm=' + '<? echo $gsm; ?>' + '&dia=' + '<? echo trim($dia); ?>' + '&determination_id='+<? echo $determination_id; ?>+
			'&booking_no=' + encodeURIComponent(String('<? echo $booking_no; ?>')) + '&data='+encodeURIComponent(String(<? echo $data; ?>))+
			'&body_part_id='+<? echo $body_part_id; ?>+
			'&color_type_id='+<? echo $color_type_id; ?>+
			'&fabric_typee='+<? echo $fabric_type; ?>+
			'&tot_booking_qnty='+<? echo $booking_qnty; ?>+
			'&buyer_id='+<? echo $buyer_id; ?>+
			'&within_group='+<? echo $within_group; ?>+
			'&sales_order_dtls_id=<? echo $sales_order_dtls_id; ?>' + '&pre_cost_id=<? echo $pre_cost_id; ?>' + '&pre_cost=<? echo $pre_cost; ?>' + '&hdn_booking_qnty=' + booking_qnty;

			freeze_window(operation);
			//alert($data);return;
			http.open("POST", "program_wise_mc_entry_controller.php", true);
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
						reset_form('programQnty_1', '', '', 'txt_program_date,<? echo $current_date;?>', '', 'hdn_booking_qnty*cbo_dia_width_type');
					}
					
					$('#updateId').val(reponse[1]);
					show_list_view(reponse[1], 'planning_info_details', 'list_view', 'program_wise_mc_entry_controller', '');
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
				document.getElementById('txt_machine_no').disabled = false;
				document.getElementById('cbo_location_name').disabled = false;
			}
			else {
				document.getElementById('txt_machine_no').disabled = true;
				document.getElementById('cbo_location_name').disabled = true;
			}
		}

		function openpage_feeder() {
			var no_of_feeder_data = $('#hidden_no_of_feeder_data').val();
			var hidden_color_id = $('#hidden_color_id').val();
			var color_type_id =<? echo $color_type_id; ?>;

			if (!(color_type_id == 2 || color_type_id == 3 || color_type_id == 4)) {
				alert("Only for Stripe");
				return;
			}

			var page_link = 'program_wise_mc_entry_controller.php?action=feeder_info_popup&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>'+'&hidden_color_id='+hidden_color_id+ '&sales_order_dtls_id=' + '<? echo $sales_order_dtls_id; ?>'+ '&within_group=' + '<? echo $within_group; ?>';    
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
			var page_link = 'program_wise_mc_entry_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&body_part_id='+'<? echo $body_part_id; ?>&booking_no=' + '<? echo $booking_no; ?>&bodyPartType=' + bodyPartType;
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

			var page_link = 'program_wise_mc_entry_controller.php?action=cam_design_info_popup&hidden_came_dsign_string_data=' + came_dsign_string_data + '&update_dtls_id='+updateDtlsId;
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

			var page_link = 'program_wise_mc_entry_controller.php?action=needle_layout_info_popup&prog_no='+prog_no + '&plan_id=' + plan_id+ '&update_needle_layout_id=' + update_needle_layout_id+ '&hidden_dial=' + hidden_dial+ '&hidden_cylinder=' + hidden_cylinder+ '&hidden_dial_row1=' + hidden_dial_row1+ '&hidden_dial_row2=' + hidden_dial_row2+ '&hidden_no_of_feeder=' + hidden_no_of_feeder+ '&hidden_cylinder_row1=' + hidden_cylinder_row1+ '&hidden_cylinder_row2=' + hidden_cylinder_row2+ '&hidden_cylinder_row3=' + hidden_cylinder_row3+ '&hidden_cylinder_row4=' + hidden_cylinder_row4+ '&hidden_yarn_ends=' + hidden_yarn_ends+ '&hidden_lfa=' + hidden_lfa+ '&hidden_yarn_tension=' + hidden_yarn_tension+ '&hidden_grey_gsm=' + hidden_grey_gsm+ '&hidden_tdry_weight=' + hidden_tdry_weight+ '&hidden_tdry_width=' + hidden_tdry_width+ '&hidden_rpm=' + hidden_rpm+ '&hidden_froll_width=' + hidden_froll_width+ '&hidden_laid_width=' + hidden_laid_width+ '&hidden_active_feeder=' + hidden_active_feeder+ '&hidden_rev_per_kg=' + hidden_rev_per_kg+ '&hidden_dial_height=' + hidden_dial_height;
			
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

			var page_link = 'program_wise_mc_entry_controller.php?action=advice_info_popup&hidden_advice_data=' + hidden_advice_data;
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
			var page_link = 'program_wise_mc_entry_controller.php?action=count_feeding_data_popup&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id;
			var title = 'Count Feeding';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=300px,center=1,resize=1,scrolling=0', '../');
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
			<table width="900" align="center" border="0">
				<tr>
					<td class="must_entry_caption">Knitting Source</td>
					<td>
						<?
						echo create_drop_down("cbo_knitting_source", 152, $knitting_source, "", 1, "-- Select --", 1, "active_inactive();load_drop_down( 'program_wise_mc_entry_controller', this.value+'**'+$companyID, 'load_drop_down_knitting_party','knitting_party');", 0, '1,3');
						?>
					</td>
					<td class="must_entry_caption">Knitting Party</td>
					<td id="knitting_party">
						<?
						echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 1, "");
						//load_drop_down( 'program_wise_mc_entry_controller', this.value, 'load_drop_down_location','location_td');
						?>
					</td>
					<td>Color</td>
					<td>
						<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;" placeholder="Browse" onClick="openpage_color();" readonly/>
						<input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
						<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" readonly>
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
						show_list_view('<? echo str_replace("'", '', $plan_id); ?>', 'planning_info_details', 'list_view', 'program_wise_mc_entry_controller', '');
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
	setFieldLevelAccess('<?php echo $companyID;?>');

	load_drop_down( 'program_wise_mc_entry_controller', 1+'**'+ <?php echo $companyID; ?>,'load_drop_down_knitting_party','knitting_party');
	load_drop_down( 'program_wise_mc_entry_controller',  $('#cbo_knitting_party').val(),'load_drop_down_location','location_td');

	 

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
			var breakOut = true;
			var total_prog_qty = 0;
			var color_name_string = '';
			var color_id_string = '';
			var color_prog_qty_string = '';
			var allowed_qty = 0;
			var colorQtyArr = [];

			$("#tbl_list_search").find('tbody tr').not(":first").each(function () 
			{
				var coloProgUpdateId = $(this).find('input[name="colo_prog_update_id[]"]').val();
				var txtColorId = $(this).find('input[name="text_colorid_[]"]').val();
				var txtColorName = $(this).find('input[name="text_color_name_[]"]').val().trim();
				var txtColorProgQty = $(this).find('input[name="text_color_prog_qty[]"]').val() * 1;		
				var hidden_color_allowed_qty = $(this).find('input[name="hidden_color_allowed_qty[]"]').val() * 1;
				var hidden_color_prev_prog_qty = $(this).find('input[name="hidden_color_prev_prog_qty[]"]').val() * 1;
				var txt_individual_color_blqty = $(this).find('input[name="txt_individual_color_blqty[]"]').val() * 1;
				var updateQty = $(this).find('input[name="text_color_prog_qty[]"]').attr('data-update-qty')*1;

				//if(txtColorProgQty>0 || coloProgUpdateId !=0)
				if(txtColorProgQty>0 || (coloProgUpdateId !=0 && updateQty > 0))
				{
					if (save_string == "")
					{
						save_string = txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string = txtColorName;
						color_id_string = txtColorId;
						//color_prog_qty_string = txtColorProgQty;
						
					}
					else
					{
						save_string += "," + txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string += "," + txtColorName;
						color_id_string += "," + txtColorId;
						//color_prog_qty_string += "," + txtColorProgQty;
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
			
			//alert(save_string+'='+total_prog_qty+'='+color_id_string+'='+color_name_string);

			$('#hidden_color_wise_prog_data').val(save_string);
			$('#hidden_total_prog_qty').val(total_prog_qty.toFixed(2));			
			$('#txt_selected_id').val(color_id_string);
			$('#txt_selected').val(color_name_string);
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
	</script>
</head>
<body>
	<div align="center" style="width:600px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:600px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="160">Color</th>
							<th width="80">Qnty</th>
							<th width="90">Prog. Qty</th>
							<th width="80">Prev. Prog. Qty</th>
							<th>Balance</th>							
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" class="text_boxes" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" class="text_boxes" value=""/>
							
                            <input type="hidden" name="txt_selected_color_bl_qty" id="txt_selected_color_bl_qty" class="text_boxes" value=""/>
							<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_prog_qty" id="hidden_total_prog_qty" class="text_boxes" value="">
						</thead>
					</table>
					<div style="width:600px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="582" class="rpt_table" id="tbl_list_search">
						<tbody>
							<?
							//for program information
							if($plan_id!="")
							{
								$plan_sql = "SELECT b.id AS ID, b.plan_id AS PLAN_ID, b.program_no AS PROGRAM_NO, b.color_id AS COLOR_ID, b.color_prog_qty COLOR_PROG_QTY FROM ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b WHERE a.dtls_id = b.program_no AND a.mst_id = b.plan_id AND a.po_id IN(".$job_id.") AND b.plan_id = ".$plan_id." AND b.status_active=1 AND b.is_deleted=0 GROUP BY b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty";

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
								}
							}
							//end
							
							$sales_dtls_id = "";
							$hidden_color_id = explode(",", $hidden_color_id);
							$program_color_id = array_unique(explode(",", $program_color_id));
							$sales_job_ids = explode("_", $sales_order_dtls_id);
							foreach ($sales_job_ids as $row)
							{
								$sales_dtls_id .= "," . $row;
							}
							
							$sales_dtls_id = ltrim($sales_dtls_id, ",");
							$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
							$sql = "SELECT color_id AS COLOR_ID, sum(grey_qty) AS QTY FROM fabric_sales_order_dtls WHERE status_active=1 AND is_deleted=0 AND mst_id=".$job_id." AND id IN(".$sales_dtls_id.") AND body_part_id IN(".$body_part_id.") AND trim(fabric_desc) = '".$desc."' AND dia = '".$dia."' AND gsm_weight = '".$gsm."' AND width_dia_type = '".$width_dia_type."' GROUP BY color_id";
							//echo $sql;
							$result = sql_select($sql);

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
								
								//for program information
								$colo_prog_update_id = $color_prog_data[$plan_id][$prog_no][$row['COLOR_ID']]['colo_prog_update_id']; 
								$color_prog_qty = $color_prog_data[$plan_id][$prog_no][$row['COLOR_ID']]['color_prog_qty'];
								$color_total_prog_qty = $color_plan_data[$plan_id][$row['COLOR_ID']]['color_prog_qty_total']; 
								$blance = ($row['QTY']-($color_total_prog_qty));
								$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer; <?php echo $color; ?> <?php echo $selected_color; ?>" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="40" align="center"><? echo $i; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row['COLOR_ID']; ?>"/>
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<? echo $color_library[$row['COLOR_ID']]; ?>"/>
										<input type="hidden" name="colo_prog_update_id[]" id="colo_prog_update_id_<?php echo $i; ?>" value="<? echo  $update_id= ($colo_prog_update_id!="")?$colo_prog_update_id:"0"; ?>"/>
									</td>
									<td width="160"><? echo $color_library[$row['COLOR_ID']]; ?>
										<input type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" value="<? echo $row['COLOR_ID']; ?>"/>
										<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" value="<? echo $color_library[$row['COLOR_ID']]; ?>"/>
									</td>
									<td width="80" align="right"><? echo number_format($row['QTY'], 2); ?>
										<input type="hidden" name="hidden_color_allowed_qty[]" id="hidden_color_allowed_qty<? echo $i;?>" value="<? echo number_format($row['QTY'], 2, '.', ''); ?>"/>
									</td>
									<td width="90"><input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: right;" placeholder="Write" data-update-qty="<?php echo $text_color_prog_qty; ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" /></td>
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
				var serialNo = i;

				if (save_string == "") {
					save_string = txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder+ "_" + serialNo;
				}
				else {
					save_string += "," + txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder+ "_" + serialNo;
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

							$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color][$serialNo] = $no_of_feeder;
						}


						if($hidden_color_id!="")
						{
							$colorCondition = "and color_number_id in($hidden_color_id)";
						}
						//echo $sales_order_dtls_id .'='.$within_group;
						if($within_group==2)
						{
							$withInGrpCondition=" sales_dtls_id in($sales_order_dtls_id) and";
						}
						else
						{
							$withInGrpCondition=" pre_cost_fabric_cost_dtls_id in($pre_cost_id) and";
						}
						$sql = "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom,sales_dtls_id,totfidder from wo_pre_stripe_color where $withInGrpCondition status_active=1 and is_deleted=0 $colorCondition and sales_dtls_id is not null  order by color_number_id, stripe_color,measurement";
						//order by  pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color asc
						//order by color_number_id, stripe_color,measurement

						$result = sql_select($sql);
						$i = 1;
						$tot_feeder = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							if($within_group==2){$withInGrpPreCsot_or_salesDtlsId=$row[csf('sales_dtls_id')];}else{$withInGrpPreCsot_or_salesDtlsId=$row[csf('pre_cost_id')];}
							$no_of_feeder = $noOfFeeder_array[$withInGrpPreCsot_or_salesDtlsId][$row[csf('color_number_id')]][$row[csf('stripe_color')]][$i];
							$tot_feeder += $no_of_feeder;

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
								value="<? if($within_group==2 && $no_of_feeder==""){echo $row[csf('totfidder')];}else{echo $no_of_feeder;}  ?>" onKeyUp="calculate_total();"/>
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
							'name': function(_, name) {var name=name.split("_"); return name[0] +"_"+ i }
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
							 
							 $sql="select e.colar_excess_percent,e.cuff_excess_percent,e.item_size,e.body_part_id,e.po_break_down_id,f.size_number_id,f.color_number_id,f.color_order,f.size_order,sum(f.plan_cut_qnty) as plan_cut_qnty,g.po_number from wo_po_color_size_breakdown f join (select a.colar_excess_percent,a.cuff_excess_percent,b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight,d.color_number_id,d.gmts_sizes ,d.dia_width,d.item_size from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.id=d.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and d.color_number_id=b.gmts_color_id and d.dia_width=b.dia_width  and c.id IN(".$pre_cost_id.") and c.body_part_id in(".$body_part_id.") and a.booking_no='".$booking_no."' and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1) e on e.job_no=f.job_no_mst and e.po_break_down_id=f.po_break_down_id and e.color_number_id=f.color_number_id and e.gmts_sizes=f.size_number_id and f.status_active=1 and f.is_deleted=0 join wo_po_break_down g on g.id=f.po_break_down_id and g.job_no_mst=f.job_no_mst group by e.colar_excess_percent,e.cuff_excess_percent,e.item_size,e.body_part_id,e.po_break_down_id,f.size_number_id,f.color_number_id,f.color_order,f.size_order,g.po_number  order by f.color_order,f.size_order";
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
	$sql = "select id, knitting_source, knitting_party, color_range, machine_dia, machine_gg, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, status, program_date, color_id, tube_ref_no from ppl_planning_info_entry_dtls where mst_id in ($data) and status_active=1 and is_deleted=0";
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
				onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_data_from_planning_info', 'program_wise_mc_entry_controller' );balance_cal();">
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
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

	$sql_count_feed = "select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=$data order by seq_no";
	$data_array_count_feed = sql_select($sql_count_feed);
	foreach ($data_array_count_feed as $row) {
		$count_feeding_data_arr[]=$row[csf('seq_no')].'_'.$row[csf('count_id')].'_'.$row[csf('feeding_id')];
	}
	$count_feeding_data_arr_str=implode(',',$count_feeding_data_arr);

	$sql = "select id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, no_fo_feeder_data, location_id, advice, batch_no, no_of_ply, tube_ref_no from ppl_planning_info_entry_dtls where id=$data";

	$data_array = sql_select($sql);
	foreach ($data_array as $row) 
	{
		echo "document.getElementById('cbo_knitting_source').value 			= '" . $row[csf("knitting_source")] . "';\n";
		echo "load_drop_down('program_wise_mc_entry_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("knitting_party")] . "+'**1', 'load_drop_down_knitting_party','knitting_party');\n";

		$color = '';
		$color_id = explode(",", $row[csf("color_id")]);
		foreach ($color_id as $val) {
			if ($color == "") $color = $color_library[$val]; else $color .= "," . $color_library[$val];
		}

		echo "document.getElementById('knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";

		echo "load_drop_down('program_wise_mc_entry_controller', " . $row[csf("knitting_party")] . ", 'load_drop_down_location','location_td');\n";

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
		$data_machine_array = sql_select("select id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder from ppl_planning_feeder_dtls where dtls_id='$data' and status_active=1 and is_deleted=0 order by pre_cost_id, color_id, stripe_color_id asc");
		$incrementNo=1;
		foreach ($data_machine_array as $row_m) {
			if ($str == '') $str = $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")]. "_" .$incrementNo;
			else   $str .= "," . $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")]. "_" .$incrementNo;
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
	
	$sqlBookingQty = "SELECT b.color_id AS COLOR_ID, SUM(b.grey_qty) AS QTY FROM ppl_planning_entry_plan_dtls a, fabric_sales_order_dtls b WHERE a.po_id = b.mst_id AND a.sales_order_dtls_ids = b.id AND a.dtls_id = ".$data." GROUP BY b.color_id";
	$dataBookingQty = sql_select($sqlBookingQty);
	$bookingQtyArr = array();
	foreach($dataBookingQty as $zasu)
	{
		$bookingQtyArr[$zasu['COLOR_ID']] = $zasu['QTY'];
	}
	
	//for color
	$sql_color_prog = "SELECT id AS ID, plan_id AS PLAN_ID, program_no AS PROGRAM_NO, color_id AS COLOR_ID, color_prog_qty AS COLOR_PROG_QTY FROM ppl_color_wise_break_down WHERE program_no = ".$data." AND status_active=1 AND is_deleted=0";
	$color_prog_data = sql_select($sql_color_prog);
	if(count($color_prog_data>0))
	{
		$saveString = "";
		$totalProgQty = 0;
		foreach ($color_prog_data as $colorRow) 
		{
			if($saveString=="")
			{
				$saveString =  $colorRow['COLOR_ID'] . "_" . $colorRow['COLOR_PROG_QTY']. "_" . $colorRow['ID']. "_" . $bookingQtyArr[$colorRow['COLOR_ID']];		
			}
			else
			{
				$saveString .= "," . $colorRow['COLOR_ID'] . "_" . $colorRow['COLOR_PROG_QTY']. "_" . $colorRow['ID']. "_" . $bookingQtyArr[$colorRow['COLOR_ID']];	
			}

			$totalProgQty += $colorRow['COLOR_PROG_QTY'];
		}
		echo "document.getElementById('hidden_color_wise_prog_data').value 	= '" . $saveString . "';\n";
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


				if (save_string == "") {
					save_string = txtSeqNo + "_" + cboCount + "_" + cboFeeding;
				}
				else {
					save_string += "," + txtSeqNo + "_" + cboCount + "_" + cboFeeding;
				}


			});

			if (breakOut == false) {
				return;
			}

			$('#hidden_count_feeding_data').val(save_string);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div style="width:430px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:430px; margin-top:5px">
				<input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data" class="text_boxes"
				value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="425" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="100">Seq. No</th>
						<th width="100">Count</th>
						<th width="100">Feeding</th>
						<th></th>
					</thead>
				</table>
				<div style="width:425px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="405" class="rpt_table"
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
								?>
								<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $sl++; ?></td>
									<td width="100">
										<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="<? echo $seq;?>" class="text_boxes" style="width:80px"/>
									</td>
									<td width="100">
										<?
										echo create_drop_down( "cboCount_".$i, 80, $yarn_count_arr,"", 1, "-- Count --",$count_id, "",0,"","","","","","","cboCount[]");
										?>
									</td>
									<td width="100">
										<?
										echo create_drop_down( "cboFeeding_".$i, 80, $feeding_arr,"", 1, "-- Feeding --",$feeding_id, "",0,"","","","","","","cboFeeding[]");
										?>
									</td>
									<td>
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
										style="width:30px" class="formbuttonplasminus" value="+"
										onClick="add_break_down_tr( <? echo $i; ?> )"/>
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
										style="width:30px" class="formbuttonplasminus" value="-"
										onClick="fn_deleteRow(<? echo $i; ?>);"/>
									</td>
								</tr>
								<?
							}
						}
						else
						{
							$i=0;$sl=1;
							?>
							<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="30" align="center"><? echo $sl++; ?></td>
								<td width="100">
									<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="1" class="text_boxes" style="width:80px"/>
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
						}
						?>
					</tbody>
				</table>
			</div>
			<table width="400" id="tbl_close">
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

		var permission = '<? echo $permission; ?>';

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
			var updateId = $('#updateId').val();
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
				$('#updateId').val(updateId);
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
				var response = return_global_ajax_value(data, 'date_duplication_check', '', 'program_wise_mc_entry_controller');
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
					url: "program_wise_mc_entry_controller.php?action=machine_allready_book_dates",
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



		function fnc_machine_entry(operation)
		{

			$("#hdn_operation").val(operation);	
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

			/*if(tot_distribution_qnty > hidden_prog_qnty){
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
			}*/

			

			
			//data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('machine_id*txt_machine_capacity*txt_distribution_qnty*txt_start_date*txt_end_date*save_data*updateId*update_dtls_id', "../../") ;

			data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('hidden_plan_id*hidden_prog_id*updateId*update_dtls_id', "../../")+ '&save_string=' + save_string ;
			
			//+'&sales_order_dtls_id=<? echo $sales_order_dtls_id; ?>' + '&pre_cost_id=<? echo $pre_cost_id; ?>' + '&pre_cost=<? echo $pre_cost; ?>' + '&hdn_booking_qnty=' + booking_qnty

			freeze_window(operation);
			//alert(data);return;
			http.open("POST", "program_wise_mc_entry_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_machine_entry_Reply_info;
		}

		function fnc_machine_entry_Reply_info()
		{
			if (http.readyState == 4)
			{
				var reponse = trim(http.responseText).split('**');
				show_msg(reponse[0]);

				if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2))
				{
					
                	if(reponse[0] == 0 )
					{
                		alert("Save Success");
                	}
                	else if(reponse[0] == 1 )
                	{
                		alert("Update Success");
                	}
                	set_button_status(1, permission, 'fnc_machine_entry', 1);
                	release_freezing();
					
					$('#updateId').val(reponse[1]);
					
					
					
                   // $("#txt_program_qnty").val(progBalance.toFixed(2));
                    //$("#balanceProgramQnty").val(progBalance.toFixed(2));
				}
				
			}
		}
		



	</script>
</head>
<body>
	<div style="width:830px;">

		<? echo load_freeze_divs("../../../", $permission, 1); ?>
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:820px; margin-top:10px; margin-left:5px">
				<input type="text" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_id" id="hidden_machine_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_no" id="hidden_machine_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_capacity" id="hidden_machine_capacity" class="text_boxes" value="">
				<input type="hidden" name="hidden_distribute_qnty" id="hidden_distribute_qnty" class="text_boxes" value="">
				<input type="hidden" name="hidden_min_date" id="hidden_min_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_max_date" id="hidden_max_date" class="text_boxes" value="">
				<input type="text" name="hidden_plan_id" id="hidden_plan_id" class="text_boxes_numeric" value="<? echo $planId; ?>">
                <input type="text" name="hidden_prog_id" id="hidden_prog_id" class="text_boxes_numeric" value="<? echo $update_dtls_id; ?>">
                <input type="text" name="hidden_prog_qnty" id="hidden_prog_qnty" class="text_boxes_numeric" value="<? echo $txt_program_qnty; ?>">
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
						$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name = ".$companyID." and variable_list=66 and status_active=1");
						if($variable_textile_sales_maintain[0][csf('production_entry')] ==2)
						{
							$companyID = $cbo_knitting_party;
						}

						$vs_sql = "select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where company_id=$companyID and category_id=1 and status_active=1 and is_deleted=0 $machinCond order by seq_no";// and dia_width='$txt_machine_dia'
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
						<td colspan="4" align="right" class="button_container">
	                        <?
	                        if(str_replace("'", '', $updated_id)>0)
	                        {
	                        	echo load_submit_buttons($permission, "fnc_machine_entry", 1, 0, "", 1);
	                        }
	                        else
	                        {
	                        	echo load_submit_buttons($permission, "fnc_machine_entry", 0, 0, "", 1);
	                        }
	                        ?>

	                        <input type="hidden" name="save_data" id="save_data" class="text_boxes">
	                        <input type="hidden" name="updateId" id="updateId" class="text_boxes"
	                        value="<? echo trim(str_replace("'", '', $plan_id)); ?>">
	                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
	                        <input type="hidden" name="hdn_operation" id="hdn_operation" class="text_boxes">
	                       

	                       

                    	</td >
						<td align="center" valign="top" class="button_container">
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
							onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'program_wise_mc_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond and a.buyer_id=$buyer_id and fabric_source in(1,2)
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond  and a.buyer_id=$buyer_id and (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num";

	} else {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=$within_group $search_field_cond order by a.id";
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
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_internal_ref_search_list_view', 'search_div', 'program_wise_mc_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_booking_search_list_view', 'search_div', 'program_wise_mc_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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
	$data = explode("**", $data);
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'program_wise_mc_entry_controller', this.value, 'load_drop_down_location','location_td');", "");
	} else if ($data[0] == 3) {
		if ($data[2] == 1) $selected_id = $data[1]; else $selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 177, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "load_drop_down( 'program_wise_mc_entry_controller', this.value, 'load_drop_down_location','location_td');");
	} else {
		echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 0, "load_drop_down( 'program_wise_mc_entry_controller', this.value, 'load_drop_down_location','location_td');");
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
?>