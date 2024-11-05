<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

	require_once('../../includes/common.php');

	$user_name = $_SESSION['logic_erp']['user_id'];

	$data = $_REQUEST['data'];
	$action = $_REQUEST['action'];
	$permission = $_SESSION['page_permission'];


// get buyer condition according to priviledge
	if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
		if ($_SESSION['logic_erp']["buyer_id"] != "") {
			$buyer_id_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = "";
	}

	if ($action == "overlapped_popup") {
		echo load_html_head_contents("Overlapped Info", "../../", 1, 1, '', '', '');
		extract($_REQUEST);
		?>
		<script>

			function fnc_close() {
				var plan_ids = '';
				var row_num = $('#tbl_list_search tbody tr').length;
				for (var j = 1; j <= row_num; j++) {
					var plan_id = $('#planId_' + j).text();
					if ($('#check_' + j).is(':checked')) {
						if (plan_ids == "") {
							plan_ids = plan_id;
						}
						else {
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

if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	if ($data[1] == 1) {
		echo create_drop_down("cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 0);
	} else if ($data[1] == 2) {
		echo create_drop_down("cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='" . $data[0] . "' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_id_cond order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	} else {
		echo create_drop_down("cbo_buyer_name", 140, $blank_array, "", 1, "-- Select Buyer --", 0, "");
	}

	exit();
}

if ($action == "load_drop_down_floor") {
	echo create_drop_down("cbo_floor_id", 160, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$datediff = datediff('d', str_replace("'", "", $txt_date_from), str_replace("'", "", $txt_date_to));

	if (str_replace("'", "", $cbo_floor_id) == 0) $floor_cond = ""; else $floor_cond = " and floor_id=$cbo_floor_id";

	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$machine_data_array = array();
	$machine_data = sql_select("select id, floor_id, machine_no, dia_width, gauge, prod_capacity from lib_machine_name where company_id=$cbo_company_name and category_id=1 and status_active=1 and is_deleted=0 $floor_cond order by floor_id, dia_width");//, cast(machine_no as unsigned)
	foreach ($machine_data as $row) {
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
	for ($j = 0; $j < $datediff; $j++) {
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

	if ($action == "plan_deails") {
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

			if ($action == "booking_item_details_popup") {
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
        				data = $('#bookingNo_' + j).val() + "**"
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
        				data += "_" + $('#bookingNo_' + j).val() + "**"
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

        	var page_link = 'planning_info_entry_for_sales_order_controller.php?action=prog_qnty_popup&gsm=' + gsm + '&dia=' + dia + '&desc=' + desc + '&within_group=' + within_group + '&job_id=' + job_id + '&booking_qnty=' + booking_qnty + '&companyID=' + companyID + '&data="' + data + '"' + '&plan_id=' + plan_id + '&determination_id=' + determination_id + '&booking_no=' + booking_no + '&body_part_id=' + body_part_id + '&fabric_type=' + fabric_typee + '&pre_cost_id=' + pre_cost_id + '&buyer_id=' + buyer_id + '&job_dtls_id=' + job_dtls_id + '&color_type_id=' + color_type_id + '&sales_order_dtls_id=' + sales_order_dtls_id;
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

        function fnc_update(i) {
        	var prog_qty = $('#prog_qty_' + i).val();
        	var program_id = $('#promram_id_' + i).val();
        	var data = "action=update_program&operation=" + operation + '&program_id=' + program_id + '&prog_qty=' + prog_qty;
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

if ($action == "booking_item_details") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$within_group = str_replace("'", "", $cbo_within_group);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$planning_status = str_replace("'", "", $cbo_planning_status);

	$job_no_cond = "";
	$booking_cond = "";
	if (str_replace("'", "", $hide_job_id) != "") {
		$job_no_cond = "and a.id in(" . str_replace("'", "", $hide_job_id) . ")";
		$ppl_job_no_cond = "and c.po_id in(" . str_replace("'", "", $hide_job_id) . ")";
	}

	$txt_booking = "%" . str_replace("'", "", trim($txt_booking_no)) . "%";
	if (str_replace("'", "", trim($txt_booking_no)) != "") {
		$booking_cond = "and a.sales_booking_no like '$txt_booking'";
		$ppl_booking_cond = "and a.booking_no like '$txt_booking'";
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and a.within_group=$within_group";
	if ($within_group == 1) {
		if ($buyer_name == 0) $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id=$buyer_name";
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

	if ($type == 2) // Revised
	{
		$sql = "select a.*,(select sum(q.grey_qty) grey_qty from fabric_sales_order_dtls q where q.id in (select regexp_substr(a.sales_order_dtls_ids,'[^,]+', 1, level) from dual  connect by regexp_substr(a.sales_order_dtls_ids, '[^,]+', 1, level) is not null)) grey_qty from (select a.company_id, a.buyer_id,a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id,b.mst_id,b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks,e.job_no,e.style_ref_no,e.booking_date,c.sales_order_dtls_ids from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c,fabric_sales_order_mst e where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id $ppl_booking_cond $ppl_job_no_cond and a.is_deleted=0 and a.status_active=1 and  b.is_sales=1 and e.is_deleted=0 and e.status_active=1 and c.is_revised=1 group by a.company_id, a.buyer_id,a.within_group, a.booking_no, c.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id,b.mst_id,b.knitting_source, b.knitting_party, b.color_id, c.id,b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks,e.job_no,e.style_ref_no,e.booking_date, c.sales_order_dtls_ids) a";
	}else{
		if ($type == 1){
			$active_status_sql = "and b.status_active=1 and b.is_deleted=0";
			
			if($within_group==1){ // within_group yes
				$sql = " select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,cast(c.po_break_down_id as varchar2(4000)) po_break_down_id, a.is_apply_last_update,a.is_master_part_updated,listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no=c.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond $within_group_cond $date_cond and c.fabric_source in(1,2) and a.booking_without_order=0 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id 
				union all
				select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, '' as po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond $within_group_cond $date_cond and (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id";
			}else{
				$sql = "select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,(select c.po_break_down_id from wo_booking_mst c where a.sales_booking_no = c.booking_no) po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond $within_group_cond $date_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id order by b.dia";
			}
			
		}else{
			$active_status_sql = "and b.status_active=0 and b.is_deleted=1 and d.status_active=0 and d.is_deleted=1";
			$sql = "select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,(select c.po_break_down_id from wo_booking_mst c where a.sales_booking_no = c.booking_no) po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,ppl_planning_entry_plan_dtls d where a.id=b.mst_id and a.id=d.po_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond $within_group_cond $date_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id order by b.dia";
		}
		 //echo $sql;
	}
	$all_sales_booking_arr=array();
	$nameArray = sql_select($sql);
	foreach ($nameArray as $value) 
	{
		/*if ($value[csf('within_group')]==1) 
		{
			$all_sales_booking_arr[]=$value[csf('sales_booking_no')];
			$sales_booking_arr[] = "'".$value[csf('sales_booking_no')]."'";
		}*/
		$all_sales_booking_arr[]=$value[csf('sales_booking_no')];
		$sales_booking_arr[] = "'".$value[csf('sales_booking_no')]."'";
	}

	$booking_data_array = array();
	$program_data_array = array();
	$booking_program_arr = array();

	$pre_cost_sql = sql_select("select a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type 
		from wo_booking_mst a inner join wo_booking_dtls b on b.booking_no = a.booking_no
		where a.booking_no in(".implode(",",$sales_booking_arr).") 
		group by a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type");
	foreach ($pre_cost_sql as $row) {
		$desc = $row[csf('construction')] . " " . $row[csf('copmposition')];
		$booking_data_array[$row[csf('booking_no')]][$desc][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_type')]] = $row[csf('pre_cost_fabric_cost_dtls_id')];
	}

	if ($db_type == 0) {
		$sql_plan = "select id,mst_id,booking_no, po_id, yarn_desc as job_dtls_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id, group_concat(dtls_id) as prog_no,sum(program_qnty) as program_qnty,SALES_ORDER_DTLS_IDS,pre_cost_fabric_cost_dtls_id,b.recv_number from ppl_planning_entry_plan_dtls left join inv_receive_master b on a.id=b.booking_id where status_active=1 and is_deleted=0 and is_sales=1 and a.booking_no in(".implode(",",$sales_booking_arr).") group by id,mst_id,booking_no, po_id, yarn_desc, body_part_id, fabric_desc, gsm_weight, dia, color_type_id,SALES_ORDER_DTLS_IDS,pre_cost_fabric_cost_dtls_id,b.recv_number";
	} else {
		$sql_plan = "select a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, listagg(a.dtls_id, ',') within group (order by a.dtls_id) as prog_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids, a.pre_cost_fabric_cost_dtls_id,a.status_active from ppl_planning_entry_plan_dtls a where a.is_sales=1 and a.is_revised=0 and a.booking_no in(".implode(",",$sales_booking_arr).") group by a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,a.status_active";
	}

	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan) {
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];

		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['prog_no'][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['sales_order_dtls_ids'] = $rowPlan[csf('sales_order_dtls_ids')];
		$program_data_array1[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]]['program'] .= $rowPlan[csf('prog_no')] . ",";
		$booking_program_arr[$rowPlan[csf('booking_no')]] .= $rowPlan[csf('prog_no')] . ",";

		// for sales order if within group no
		$sales_order_dtls_ids = explode(",",$rowPlan[csf('sales_order_dtls_ids')]);
		foreach ($sales_order_dtls_ids as $sales_dtls_row) {
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['prog_no'] .= $rowPlan[csf('prog_no')].",";
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		}
	}
	$date_cond = '';
	$date_from = str_replace("'", "", trim($txt_date_from));
	$date_to = str_replace("'", "", trim($txt_date_to));
	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and a.insert_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.insert_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($type == 2) // Revised
	{
		$status_arr = array();
		$check_production_arr = array();
		$check_sales_order_status = sql_select("select a.mst_id,a.pre_cost_fabric_cost_dtls_id,listagg(a.status_active, ',') within group (order by a.id) as status_active from fabric_sales_order_dtls a group by a.pre_cost_fabric_cost_dtls_id");
		foreach ($check_sales_order_status as $status_row) {
			$status_arr[$status_row[csf('pre_cost_fabric_cost_dtls_id')]][$status_row[csf('mst_id')]] = $status_row[csf('status_active')];
		}

		$check_program_in_req_issue = sql_select("select a.id,a.knit_id,b.recv_number from ppl_yarn_requisition_entry a left join inv_receive_master b on a.knit_id=b.booking_id where b.company_id=$company_name and b.entry_form=2 and b.item_category=13 and b.receive_basis=2");
		foreach ($check_program_in_req_issue as $check_production) {
			$check_production_arr[$check_production[csf('knit_id')]] = $check_production[csf('recv_number')];
		}
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset>
				<legend>Fabric Description Details</legend>
				<input type="button" value="Delete Program" name="generate" id="generate" class="formbutton"
				style="width:150px" onClick="delete_prog()"/>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"
				id="tbl_list_search">
				<thead>
					<th width="40"></th>
					<th width="40">SL</th>
					<th width="50">Plan Id</th>
					<th width="60">Prog. No</th>
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
					<th width="70">Sales Order Qty</th>
					<th width="70">Prog. Qnty</th>
					<th>Balance Prog. Qnty</th>
					<th>Production Qnty</th>
					<th></th>
				</thead>
				<tbody>
					<?
					
					$nameArray = sql_select($sql);
					$i = 1;
					$k = 1;
					$z = 1;
					$dia_array = array();
					$a = '';
					foreach ($nameArray as $row) {
						$program_id = $row[csf('id')];
						$booking_no = $row[csf('booking_no')];
						$booking_date = $row[csf('booking_date')];
						$sales_job_no = $row[csf('job_no')];
						$style_ref_no = $row[csf('style_ref_no')];
						$grey_qty = $row[csf('grey_qty')];
						$color_name = "";
						$plan_id = $row[csf('mst_id')];
						$gsm = $row[csf('gsm_weight')];
						$dia = $row[csf('dia')];
						$desc = $row[csf('fabric_desc')];
						$determination_id = $row[csf('determination_id')];
						$program_qnty = $row[csf('program_qnty')];
						$colors = $row[csf('color_id')];
						$color_id = explode(",", $colors);
						$body_part_name = $body_part[$row[csf('body_part_id')]];
						$color_type_name = $color_type[$row[csf('color_type_id')]];
						$fabric_type_name = $fabric_typee[$row[csf('width_dia_type')]];
						$within_group = $row[csf('within_group')];
						$buyer_id = $row[csf('buyer_id')];
						foreach ($color_id as $row2) {
							$color_name .= $color_library[$row2] . ",";
						}
						$knitting_production = sql_select("select b.grey_receive_qnty qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.booking_id=$program_id and a.item_category=13");

						$prog_no = implode(",", array_unique(explode(",", $prog_no)));
						$balance_qnty = ($grey_qty - $program_qnty);

						$reqsn_id = return_field_value("a.id as reqsn_id", "ppl_planning_info_entry_dtls a,ppl_yarn_requisition_entry b", "a.id=b.knit_id and a.mst_id='" . $plan_id . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "reqsn_id");

						if ($reqsn_id == "") $reqsn_found_or_not = 0; else $reqsn_found_or_not = 1;
						if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						if (!in_array($dia, $dia_array)) {
							if ($k != 1) {
								?>
								<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
									<td colspan="16" align="right"><b>Sub Total</b></td>
									<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b>
									</td>
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
								$buyer = $company_arr[$buyer_id];
							} else {
								$buyer = $buyer_arr[$buyer_id];
							}
							$reqsn_found_or_not = 0;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer"
								onClick="selected_row('<? echo $i; ?>')" id="tr_<? echo $i; ?>">
								<td width="40" align="center" valign="middle">
									<input type="checkbox" id="tbl_<? echo $i; ?>"
									name="check[]" <? echo $disabled; ?>/>
									<input id="promram_id_<? echo $i; ?>" name="promram_id[]" type="hidden"
									value="<? echo trim($program_id); ?>"/>
								</td>
								<td width="40" align='center'><? echo $z; ?></td>
								<td width="50" align='center' id="plan_id_<? echo $i; ?>"><? echo trim($plan_id); ?></td>
								<td width="60" align='center' id="prog_no_<? echo $i; ?>">
									<a href='##'
									onClick="generate_report2(<? echo $row[csf('company_id')] . "," . $program_id; ?>)"><? echo $program_id; ?></a>
								&nbsp;</td>
								<td width="100" id="booking_no_<? echo $i; ?>" align='center'>
									<p><? echo $booking_no; ?></p></td>
									<td width="70" align="center"><? echo change_date_format($booking_date); ?></td>
									<td width="60" align='center'><p><? echo $buyer; ?></p></td>
									<td width="105" align='center'><p><? echo $sales_job_no; ?></p></td>
									<td width="100" align='center'><p><? echo $style_ref_no; ?></p></td>
									<td width="80" align='center'><p><? echo $body_part_name; ?></p></td>
									<td width="70" align='center'><p><? echo $color_type_name; ?></p></td>
									<td width="140" align='center' id="desc_<? echo $i; ?>"><p><? echo $desc; ?></p></td>
									<td width="50" align='center' id="gsm_weight_<? echo $i; ?>"><p><? echo $gsm; ?></p>
									</td>
									<td width="50" align='center' id="dia_width_<? echo $i; ?>"><p><? echo $dia; ?></p></td>
									<td width="70" align='center'><? echo $fabric_type_name; ?></td>
									<td width="70"><? echo rtrim($color_name, ','); ?>&nbsp;</td>
									<td align="right" id="booking_qnty_<? echo $i; ?>"
										width="70"><? echo number_format($grey_qty, 2, '.', ''); ?></td>
										<td align="right" width="70">
											<input type="text" class="text_boxes_numeric" name="prog_qty[]"
											id="prog_qty_<? echo $i; ?>" value="<? echo $program_qnty; ?>"
											style="width:80px"/>
										</td>
										<td align="right"><? echo number_format($balance_qnty, 2); ?></td>
										<td align="right"><? echo number_format($knitting_production[0][csf('qnty')], 2); ?></td>
										<td align="center">
											<input type="button" value="Update" onClick="fnc_update(<? echo $i; ?>);"
											class="formbutton" style="width:80px">
											<input type="hidden" name="reqsn_found_or_not[]"
											id="reqsn_found_or_not_<? echo $i; ?>"
											value="<? echo $reqsn_found_or_not; ?>"/>
										</td>

									</tr>
									<?
									$total_dia_qnty += $grey_qty;
									$total_program_qnty += $program_qnty;
									$total_balance += $balance_qnty;
									$total_production += $knitting_production[0][csf('qnty')];

									$total_qnty += $grey_qty;
									$grand_total_program_qnty += $program_qnty;
									$grand_total_balance += $balance_qnty;
									$grand_total_production += $knitting_production[0][csf('qnty')];

									$i++;
									$z++;
								}
								if ($i > 1) {
									?>
									<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
										<td colspan="16" align="right"><b>Sub Total</b></td>
										<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_production, 2, '.', ''); ?></b></td>
									</tr>
									<?
								}
								?>
							</tbody>
							<tfoot>
								<th colspan="16" align="right">Grand Total<input type="hidden" name="company_id" id="company_id"
									value="<? echo $company_name; ?>"/></th>
									<th align="right"><? echo number_format($total_qnty, 2, '.', ''); ?></th>
									<th align="right"><? echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
									<th align="right"><? echo number_format($grand_total_balance, 2, '.', ''); ?></th>
									<th align="right"><? echo number_format($grand_total_production, 2, '.', ''); ?></th>
								</tfoot>
							</table>
						</div>
					</fieldset>
				</form>
				<?
			} else {
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

				
				if(!empty($all_sales_booking_arr)){
					$booking_list=implode(",", array_unique($all_sales_booking_arr)); 			
					$is_approved_status_arr = return_library_array( "select booking_no, is_approved from wo_booking_mst where booking_no in ('".$booking_list."')",'booking_no','is_approved');
				}
				if(!empty($all_sales_booking_arr)){
					$job_no_array=array();
					$booking_list=implode(",", array_unique($all_sales_booking_arr));
					$sql_data=sql_select("select a.id, b.buyer_name,c.booking_no from wo_po_break_down a, wo_po_details_master b, wo_booking_dtls c where b.job_no=a.job_no_mst and a.id=c.po_break_down_id and c.booking_no in ('".$booking_list."') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						union all
						select 0 as id, buyer_id,booking_no from wo_non_ord_samp_booking_mst where booking_no in ('".$booking_list."') and status_active=1 and is_deleted=0
						");
					foreach ($sql_data as $row) {
						$job_no_array[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_name')];
					}
				}
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
								if($type == 3){
									echo '<th>&nbsp;</th>';
								}
								?>
							</thead>
							<tbody>
								<?
								$i = 1;
								$k = 1;
								$z = 1;
								$dia_array = array();
								$nameArray = sql_select($sql);
								$a = '';
								foreach ($nameArray as $row) {
									$plan_id = '';
									$grey_qty = $row[csf('grey_qty')];
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
									$sales_order_dtls_id = $row[csf('sales_order_dtls_id')];
									$sales_id = $row[csf('id')];
									$within_group = $row[csf('within_group')];
									$pre_cost_fabric_cost_dtls_id = $row[csf('pre_cost_fabric_cost_dtls_id')];
									$buyer_id = $row[csf('buyer_id')];
									$buyer_name = $job_no_array[$sales_booking_no]['buyer_id'];

									$status = ($type == 1) ? 1 : 0;							
									if($within_group == 1){
										$program_qnty = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['program_qnty'];
										$plan_id = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['mst_id'];
										$prog_no = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['prog_no'];
										$prog_no = implode(",", $prog_no);
									}else{
										$sales_dtls_id = array_unique(explode(",",$sales_order_dtls_id));
										$program_qnty = 0;
										$prog_no='';
										//print_r($sales_dtls_id);
										foreach ($sales_dtls_id as $row) {
											if($type==3){
												$plan_id .= $program_data_sales_array[$row][$status]['mst_id'].",";
												$prog_no .= $program_data_sales_array[$row][$status]['prog_no'].",";
												$program_qnty += $grey_qty; //$program_data_sales_array[$row][$status]['program_qnty'];
											}else{
												$plan_id .= $program_data_sales_array[$row][$status]['mst_id'].",";
												$prog_no .= $program_data_sales_array[$row][$status]['prog_no'].",";
												$program_qnty = $program_data_sales_array[$row][$status]['program_qnty'];
											}
										}
									}

									$balance_qnty = number_format($grey_qty - $program_qnty,2,".","");
									$pre_cost_id = $booking_data_array[$sales_booking_no][$desc][$gsm][$dia][$color_type_id];
									if (($planning_status == 2 && $balance_qnty <= 0) || ($planning_status == 1 && (($within_group == 1)?$balance_qnty > 0:$balance_qnty > 0))) {
										if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
										if (!in_array($dia, $dia_array)) {
											if ($k != 1) {
												?>
												<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
													<td colspan="14" align="right"><b>Sub Total</b></td>
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
													<td colspan="17">
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
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" >
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
													<td width="60" align='center' id="prog_no_<? echo $i; ?>">
														<?
														$print_program_no = "";
														$prog_no_arr = array_unique(explode(",", $prog_no));
														foreach ($prog_no_arr as $prog) {
															if($prog != ""){
																$print_program_no .= "<a href='##' onclick=\"generate_report2(" . $row[csf('company_id')] . "," . $prog . ")\">" . $prog . "</a>,";
															}
														}
														echo rtrim($print_program_no,", ");
														?>
													</td>
													<td id="booking_no_<? echo $i; ?>" align='center'><? echo $sales_booking_no; ?></td>
													<td width="70" align="center"><? echo $booking_date; ?></td>
													<td width="60" align='center'><p><? echo $buyer; ?></p></td>
													<td align='center'><? echo $job_no; ?></td>
													<td width="100" align='center'><p><? echo $style_ref_no; ?></p></td>
													<td align='center'><p><? echo $body_part[$body_part_id]; ?></p></td>
													<td width="70" align='center'><p><? echo $color_type[$color_type_id]; ?></p></td>
													<td align='center' id="desc_<? echo $i; ?>"><p><? echo $desc; ?></p></td>
													<td width="50" align='center' id="gsm_weight_<? echo $i; ?>"><p><? echo $gsm; ?></p></td>
													<td width="50" align='center' id="dia_width_<? echo $i; ?>"><p><? echo $dia; ?></p></td>
													<td width="70" align='center'><? echo $fabric_typee[$width_dia_type]; ?></td>
													<td align="right" id="booking_qnty_<? echo $i; ?>" width="70">
														<? echo number_format($grey_qty, 2, '.', ''); ?>
													</td>
													<td align="right" width="70">
														<? if ($program_qnty > 0) echo number_format($program_qnty, 2, '.', ''); ?>
													</td>
													<td align="right"><? echo number_format($balance_qnty, 2, '.', ''); ?></td>
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
													<input type="hidden" name="reqsn_found_or_not[]" id="reqsn_found_or_not_<? echo $i; ?>"
													value="<? echo $reqsn_found_or_not; ?>"/>
													<input type="hidden" name="sales_order_dtls_id[]" id="sales_order_dtls_id<? echo $i; ?>"
													value="<? echo $sales_order_dtls_id; ?>"/>
													<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
													id="pre_cost_fabric_cost_dtls_id<? echo $i; ?>" value="<? echo $pre_cost_fabric_cost_dtls_id; ?>"/>
												</tr>
												<?
												$total_dia_qnty += $row[csf('grey_qty')];
												$total_program_qnty += $program_qnty;
												$total_balance += $balance_qnty;

												$total_qnty += $row[csf('grey_qty')];
												$grand_total_program_qnty += $program_qnty;
												$grand_total_balance += $balance_qnty;

												$i++;
												$z++;
											}
										}

										if ($i > 1) {
											?>
											<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
												<td colspan="14" align="right"><b>Sub Total</b></td>
												<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
												<td align="right"><b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
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

			if ($action == "prog_qnty_popup") {
				echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
				extract($_REQUEST);
				$current_date = date("d-m-Y");

				$dataArray = sql_select("select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id=$determination_id and status_active=1 and is_deleted=0");
				?>
				<script>
					var permission = '<? echo $permission; ?>';
					function openpage_machine() {

						/*if (form_validation('txt_machine_gg', 'Machine GG') == false) {
				            return;
				        }*/

						var save_string = $('#save_data').val();
						var txt_machine_dia = $('#txt_machine_dia').val();
						var update_dtls_id = $('#update_dtls_id').val();
						var txt_program_qnty = $('#txt_program_qnty').val();
						var txt_machine_gg = $('#txt_machine_gg').val();

						var page_link = 'planning_info_entry_for_sales_order_controller.php?action=machine_info_popup&save_string=' + save_string + '&companyID=' + '<? echo $companyID; ?>' + '&txt_machine_dia=' + txt_machine_dia + '&update_dtls_id=' + update_dtls_id + '&txt_program_qnty='+txt_program_qnty + '&txt_machine_gg='+txt_machine_gg;
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

					function openpage_color() {
						var hidden_color_id = $('#hidden_color_id').val();
						var program_color_id = $('#txt_hdn_colors').val();
						var page_link = 'planning_info_entry_for_sales_order_controller.php?action=color_info_popup&companyID=' + '<? echo $companyID; ?>' + '&job_id=' + '<? echo $job_id; ?>' + '&booking_no=' + '<? echo $booking_no; ?>' + '&dia=' + '<? echo $dia; ?>' + '&hidden_color_id=' + hidden_color_id + '&program_color_id=' + program_color_id + '&gsm=' + '<?php echo $gsm; ?>' + '&body_part_id=' + '<? echo $body_part_id; ?>' + '&desc=' + '<?php echo $desc;?>' + '&sales_order_dtls_id=' + '<?php echo $sales_order_dtls_id;?>';
						var title = 'Color Info';

						emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=300px,center=1,resize=1,scrolling=0', '../../');
						emailwindow.onclose = function () {
							var theform = this.contentDoc.forms[0];
							var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
							var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
							var hidden_color_qnty = this.contentDoc.getElementById("txt_selected_qnty").value;
							var qnty_arr = new Array();
							var total_color_qnty = 0;
							qnty_arr = hidden_color_qnty.split(",");
							$.each(qnty_arr, function (i) {
								total_color_qnty += parseFloat(qnty_arr[i]);
							});

							$('#txt_color').val(hidden_color_no);
							$('#hidden_color_id').val(hidden_color_id);
							$('#txt_program_qnty').val(total_color_qnty);
						}
					}

					function fnc_program_entry(operation) {
						var booking_qnty = $("#hdn_booking_qnty").val() * 1;
						if (form_validation('txt_machine_dia*txt_machine_gg*txt_fabric_dia*txt_program_qnty', 'Machine Dia*Machine GG*Finish Fabric Dia*Program Qnty') == false) {
							return;
						}

						data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*txt_color*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*cbo_location_name*hidden_advice_data*hidden_no_of_feeder_data*hidden_collarCuff_data*hidden_count_feeding_data', "../../") + '&companyID='+<? echo $companyID; ?>+
						'&gsm=' + '<? echo $gsm; ?>' + '&dia=' + '<? echo trim($dia); ?>' + '&desc=' + '<? echo trim($desc); ?>' + '&determination_id='+<? echo $determination_id; ?>+
						'&booking_no=' + '<? echo $booking_no; ?>' + '&data='+<? echo $data; ?>+
						'&body_part_id='+<? echo $body_part_id; ?>+
						'&color_type_id='+<? echo $color_type_id; ?>+
						'&fabric_typee='+<? echo $fabric_type; ?>+
						'&tot_booking_qnty='+<? echo $booking_qnty; ?>+
						'&buyer_id='+<? echo $buyer_id; ?>+
						'&within_group='+<? echo $within_group; ?>+
						'&sales_order_dtls_id=<? echo $sales_order_dtls_id; ?>' + '&pre_cost_id=<? echo $pre_cost_id; ?>' + '&pre_cost=<? echo $pre_cost; ?>' + '&hdn_booking_qnty=' + booking_qnty;

						freeze_window(operation);

						http.open("POST", "planning_info_entry_for_sales_order_controller.php", true);
						http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = fnc_program_entry_Reply_info;
					}

					function fnc_program_entry_Reply_info() {
						if (http.readyState == 4) {
							var reponse = trim(http.responseText).split('**');

							show_msg(reponse[0]);

							if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)) {
								reset_form('programQnty_1', '', '', 'txt_program_date,<? echo $current_date;?>', '', 'hdn_booking_qnty*cbo_dia_width_type');
								$('#updateId').val(reponse[1]);
								show_list_view(reponse[1], 'planning_info_details', 'list_view', 'planning_info_entry_for_sales_order_controller', '');
								set_button_status(0, permission, 'fnc_program_entry', 1);
							}
							if (reponse[0] == 14) {
								alert(reponse[1]);
							}
							release_freezing();
						}
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
						var color_type_id =<? echo $color_type_id; ?>;

						if (!(color_type_id == 2 || color_type_id == 3 || color_type_id == 4)) {
							alert("Only for Stripe");
							return;
						}

						var page_link = 'planning_info_entry_for_sales_order_controller.php?action=feeder_info_popup&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>';
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
						var page_link = 'planning_info_entry_for_sales_order_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>';
						var title = 'Collar & Cuff Measurement Info';

						emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../');
						emailwindow.onclose = function () {
							var theform = this.contentDoc.forms[0];
							var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

							$('#hidden_collarCuff_data').val(hidden_collarCuff_data);
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
						var page_link = 'planning_info_entry_for_sales_order_controller.php?action=count_feeding_data_popup&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id;
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
					<? echo load_freeze_divs("../../", $permission, 1); ?>
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
								<td><p><? echo $desc; ?></p></td>
								<td><? echo $gsm; ?>&nbsp;</td>
								<td><? echo $dia; ?>&nbsp;</td>
								<td align="right"><? echo number_format($booking_qnty, 2); ?></td>
							</tr>
						</table>
					</fieldset>
					<fieldset style="width:900px; margin-top:5px;">
						<legend>New Entry</legend>
						<input type="hidden" id="hdn_booking_qnty" name="hdn_booking_qnty" value="<? echo $booking_qnty; ?>"/>
						<table width="900" align="center" border="0">
							<tr>
								<td>Knitting Source</td>
								<td>
									<?
									echo create_drop_down("cbo_knitting_source", 152, $knitting_source, "", 1, "-- Select --", 1, "active_inactive();load_drop_down( 'planning_info_entry_for_sales_order_controller', this.value+'**'+$companyID, 'load_drop_down_knitting_party','knitting_party');", 0, '1,3');
									?>
								</td>
								<td>Knitting Party</td>
								<td id="knitting_party">
									<?
									echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 1, "");
									?>
								</td>
								<td>Color</td>
								<td>
									<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;"
									placeholder="Browse" onClick="openpage_color();" readonly/>
									<input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
								</td>
							</tr>
							<tr>
								<td>Color Range</td>
								<td>
									<?
									echo create_drop_down("cbo_color_range", 152, $color_range, "", 1, "-- Select --", 0, "");
									?>
								</td>
								<td class="must_entry_caption">Machine Dia</td>
								<td>
									<input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric"
									style="width:60px;" maxlength="3" title="Maximum 3 Character" value=""/>
									<?
									echo create_drop_down("cbo_dia_width_type", 100, $fabric_typee, "", 1, "-- Select --", $fabric_type, "");
									?>
								</td>
								<td class="must_entry_caption">Machine GG</td>
								<td>
									<input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes"
									style="width:140px;"/>
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Finish Fabric Dia</td>
								<td>
									<input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes"
									style="width:140px;"/>
								</td>
								<td class="must_entry_caption">Program Qnty</td>
								<td>
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
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Program No.</b>
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
										echo create_drop_down("cbo_location_name", 152, "select id,location_name from lib_location where company_id='$companyID' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
										?>
									</td>
									<td>
										<input type="button" name="feeder" class="formbuttonplasminus" value="Advice"
										onClick="openpage_advice();" style="width:100px"/>
										<input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes">
									</td>
									<td>
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
				
				load_drop_down( 'planning_info_entry_for_sales_order_controller', 1+'**'+ <?php echo $companyID; ?>,'load_drop_down_knitting_party','knitting_party');

			</script>
			</html>
			<?
			exit();
		}

		if ($action == "feeder_info_popup") {
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

						if (save_string == "") {
							save_string = txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
						}
						else {
							save_string += "," + txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
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

									$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color] = $no_of_feeder;
								}

								$sql = "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id in($pre_cost_id) and status_active=1 and is_deleted=0";
								$result = sql_select($sql);
								$i = 1;
								$tot_feeder = 0;
								foreach ($result as $row) {
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

									$no_of_feeder = $noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]];
									$tot_feeder += $no_of_feeder;

									?>
									<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
										<td width="40" align="center"><? echo $i; ?>
											<input type="hidden" name="txtPreCostId[]" id="txtPreCostId_<?php echo $i ?>"
											value="<? echo $row[csf('pre_cost_id')]; ?>"/>
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
												value="<? echo $no_of_feeder; ?>" onKeyUp="calculate_total();"/>
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

		if ($action == "collarCuff_info_popup") {
			echo load_html_head_contents("Collar & Cuff Info", "../../", 1, 1, '', '', '');
			extract($_REQUEST);
			?>

			<script>

				function add_break_down_tr(i) {
					var row_num = $('#txt_tot_row').val();
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

            }).end();//.appendTo("#tbl_list_search")

					$("#tr_" + i).after(clone);

					$('#txtGrey_' + row_num).removeAttr("value").attr("value", "");
					$('#txtFinish_' + row_num).removeAttr("value").attr("value", "");
					$('#txtQtyPcs_' + row_num).removeAttr("value").attr("value", "");
					$('#txtQtyPcs_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + row_num + ");");

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
				}

				function fn_deleteRow(rowNo) {
					if (rowNo != 1) {
						$("#tr_" + rowNo).remove();
						reArrangeSl();
						calculate_tot_qnty();
					}
				}

				function fnc_close() {
					var save_string = '';
					var breakOut = true;
					$("#tbl_list_search").find('tbody tr').each(function () {
						if (breakOut == false) {
							return;
						}

						var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();
						var txtGrey = $(this).find('input[name="txtGrey[]"]').val();
						var txtFinish = $(this).find('input[name="txtFinish[]"]').val();
						var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;

						if (txtQtyPcs < 1) {
							alert("Please Insert Qty. Pcs");
							$(this).find('input[name="txtQtyPcs[]"]').focus();
							breakOut = false;
							return false;
						}

						if (save_string == "") {
							save_string = bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
						}
						else {
							save_string += "," + bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
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
			<div style="width:530px;" align="center">
				<form name="searchwofrm" id="searchwofrm">
					<fieldset style="width:530px; margin-top:5px">
						<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data" class="text_boxes"
						value="">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table">
							<thead>
								<th width="30">SL</th>
								<th width="100">Body Part</th>
								<th width="100">Grey Size</th>
								<th width="100">Finish Size</th>
								<th width="100">Qty. Pcs</th>
								<th></th>
							</thead>
						</table>
						<div style="width:525px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="505" class="rpt_table"
							id="tbl_list_search">
							<tbody>
								<?
								$collarCuff_data = explode(",", $collarCuff_data);
								$prev_data_arr = array();
								$pre_cost_id = implode(",", array_unique(explode(",", $pre_cost_id)));

								for ($i = 0; $i < count($collarCuff_data); $i++) {
									$body_part_wise_data = explode("_", $collarCuff_data[$i]);
									$body_part_id = $body_part_wise_data[0];
									$grey = $body_part_wise_data[1];
									$finish = $body_part_wise_data[2];
									$qty = $body_part_wise_data[3];

									$prev_data_arr[$body_part_id] .= $grey . "__" . $finish . "__" . $qty . "#";
								}

								$body_part_ids = "2,3,4,8,22,40,55";
								$sql = "select a.body_part_id, b.item_size from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.id in($pre_cost_id) and a.body_part_id in($body_part_ids) group by a.body_part_id, b.item_size order by a.body_part_id";
								$result = sql_select($sql);

								$i = 1;
								$totQtyPcs = 0;
								foreach ($result as $row) {
									$bodyPartData = array_filter(explode("#", chop($prev_data_arr[$row[csf('body_part_id')]], '#')));
									if (count($bodyPartData) > 0) {
										foreach ($bodyPartData as $datas) {
											if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

											$prevDatas = explode("__", $datas);

											$grey = $prevDatas[0];
											$finish = $prevDatas[1];
											$qtyPcs = $prevDatas[2];
											$totQtyPcs += $qtyPcs;

											?>
											<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
												<td width="30" align="center"><? echo $i; ?></td>
												<td width="100">
													<input type="text" name="txtBodyPartId[]"
													id="txtBodyPartId_<?php echo $i ?>"
													value="<? echo $body_part[$row[csf('body_part_id')]]; ?>"
													class="text_boxes" style="width:80px" disabled/>
													<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
													value="<? echo $row[csf('body_part_id')]; ?>"/>
												</td>
												<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
													class="text_boxes" style="width:80px"
													value="<? echo $grey; ?>"/></td>
													<td width="100"><input type="text" name="txtFinish[]"
														id="txtFinish_<? echo $i; ?>" class="text_boxes"
														style="width:80px" value="<? echo $finish; ?>"/></td>
														<td width="100">
															<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
															class="text_boxes_numeric" style="width:80px"
															value="<? echo $qtyPcs; ?>" onKeyUp="calculate_tot_qnty();"/>
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
													$i++;
												}
											} else {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

												$finish = $row[csf('item_size')];

												?>
												<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
													<td width="30" align="center"><? echo $i; ?></td>
													<td width="100">
														<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
														value="<? echo $body_part[$row[csf('body_part_id')]]; ?>"
														class="text_boxes" style="width:80px" disabled/>
														<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
														value="<? echo $row[csf('body_part_id')]; ?>"/>
													</td>
													<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
														class="text_boxes" style="width:80px" value=""/></td>
														<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
															class="text_boxes" style="width:80px"
															value="<? echo $finish; ?>"/></td>
															<td width="100">
																<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
																class="text_boxes_numeric" style="width:80px" value=""
																onKeyUp="calculate_tot_qnty();"/>
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
														$i++;
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

					if ($action == "planning_info_details") {
						$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
						$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
						$sql = "select id, knitting_source, knitting_party, color_range, machine_dia, machine_gg, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, status, program_date,color_id from ppl_planning_info_entry_dtls where mst_id in ($data) and status_active=1 and is_deleted=0";
						?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
							<thead>
								<th width="90">Knitting Source</th>
								<th width="100">Knitting Company</th>
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
						<div style="width:900px; max-height:140px; overflow-y:scroll" id="list_container_batch" align="left">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="882" class="rpt_table"
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
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
									onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_data_from_planning_info', 'planning_info_entry_for_sales_order_controller' );">
									<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
									<td width="100"><p><? echo $knit_party; ?></p></td>
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

				if ($action == "populate_data_from_planning_info") {
					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
					$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

					$sql_count_feed = "select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=$data order by seq_no";
					$data_array_count_feed = sql_select($sql_count_feed);
					foreach ($data_array_count_feed as $row) {
						$count_feeding_data_arr[]=$row[csf('seq_no')].'_'.$row[csf('count_id')].'_'.$row[csf('feeding_id')];
					}
					$count_feeding_data_arr_str=implode(',',$count_feeding_data_arr);



					$sql = "select id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, no_fo_feeder_data, location_id, advice from ppl_planning_info_entry_dtls where id=$data";
					$data_array = sql_select($sql);
					foreach ($data_array as $row) {
						echo "document.getElementById('cbo_knitting_source').value 			= '" . $row[csf("knitting_source")] . "';\n";

						echo "load_drop_down('planning_info_entry_for_sales_order_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("knitting_party")] . "+'**1', 'load_drop_down_knitting_party','knitting_party');\n";

						$color = '';
						$color_id = explode(",", $row[csf("color_id")]);
						foreach ($color_id as $val) {
							if ($color == "") $color = $color_library[$val]; else $color .= "," . $color_library[$val];
						}

						echo "document.getElementById('knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";
						echo "document.getElementById('txt_color').value 					= '" . $color . "';\n";
						echo "document.getElementById('hidden_color_id').value 				= '" . $row[csf("color_id")] . "';\n";
						echo "document.getElementById('cbo_color_range').value 				= '" . $row[csf("color_range")] . "';\n";
						echo "document.getElementById('txt_machine_dia').value 				= '" . $row[csf("machine_dia")] . "';\n";
						echo "document.getElementById('cbo_dia_width_type').value 			= '" . $row[csf("width_dia_type")] . "';\n";
						echo "document.getElementById('txt_machine_gg').value 				= '" . $row[csf("machine_gg")] . "';\n";
						echo "document.getElementById('txt_fabric_dia').value 				= '" . $row[csf("fabric_dia")] . "';\n";
						echo "document.getElementById('txt_program_qnty').value 			= '" . $row[csf("program_qnty")] . "';\n";
						echo "document.getElementById('txt_stitch_length').value 			= '" . $row[csf("stitch_length")] . "';\n";
						echo "document.getElementById('txt_spandex_stitch_length').value 	= '" . $row[csf("spandex_stitch_length")] . "';\n";
						echo "document.getElementById('txt_draft_ratio').value 				= '" . $row[csf("draft_ratio")] . "';\n";

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

						$str = '';
						$data_machine_array = sql_select("select id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder from ppl_planning_feeder_dtls where dtls_id='$data' and status_active=1 and is_deleted=0");
						foreach ($data_machine_array as $row_m) {
							if ($str == '') $str = $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")];
							else   $str .= "," . $row_m[csf("pre_cost_id")] . "_" . $row_m[csf("color_id")] . "_" . $row_m[csf("stripe_color_id")] . "_" . $row_m[csf("no_of_feeder")];
						}
		echo "document.getElementById('hidden_no_of_feeder_data').value 					= '" . $str . "';\n";//$row[csf("save_data")]
		echo "document.getElementById('save_data').value 					= '" . $save_data . "';\n";//$row[csf("save_data")]
		echo "document.getElementById('cbo_location_name').value 			= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('hidden_advice_data').value 			= '" . $row[csf("advice")] . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_program_no').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('hidden_count_feeding_data').value	= '" .$count_feeding_data_arr_str. "';\n";
		echo "days_req();\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_program_entry',1);\n";

		$str_collar = '';
		$data_collar_cuff = sql_select("select id, mst_id, dtls_id, body_part_id, grey_size,finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where dtls_id='$data' and status_active=1 and is_deleted=0");

		foreach ($data_collar_cuff as $row_collar) {
			if ($str_collar == '') $str_collar = $row_collar[csf("body_part_id")] . "_" . $row_collar[csf("grey_size")] . "_" . $row_collar[csf("finish_size")] . "_" . $row_collar[csf("qty_pcs")];
			else   $str_collar .= "," . $row_collar[csf("body_part_id")] . "_" . $row_collar[csf("grey_size")] . "_" . $row_collar[csf("finish_size")] . "_" . $row_collar[csf("qty_pcs")];
		}
		echo "document.getElementById('hidden_collarCuff_data').value 		= '" . $str_collar . "';\n";//$row[csf("save_data")]
		exit();
	}
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


if ($action == "save_update_delete") {
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

		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_no=$booking_no and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($knit_qty > str_replace("'", "", $txt_program_qnty)) {
			echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
			disconnect($con);
			exit();
		}


		$id = '';

		if (str_replace("'", '', $updateId) == "") {
			$id = return_next_id("id", "ppl_planning_info_entry_mst", 1);

			$field_array = "id, company_id, within_group, buyer_id, booking_no, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, is_sales, inserted_by, insert_date";
			$data_array = "(" . $id . "," . $companyID . "," . $within_group . "," . $buyer_id . ",'" . $booking_no . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $fabric_typee . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		} else {
			$id = str_replace("'", '', $updateId);
			$flag = 1;
		}
		//echo "10**".$flag;die;

		$dtls_id = return_next_id("id", "ppl_planning_info_entry_dtls", 1);
		$field_array_dtls = "id, mst_id, knitting_source, knitting_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio,  machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, location_id, advice, is_sales, inserted_by, insert_date";

		$data_array_dtls = "(" . $dtls_id . "," . $id . "," . $cbo_knitting_source . "," . $cbo_knitting_party . "," . $hidden_color_id . "," . $cbo_color_range . "," . $txt_machine_dia . "," . $cbo_dia_width_type . "," . $txt_machine_gg . "," . $txt_fabric_dia . "," . $txt_program_qnty . "," . $txt_stitch_length . "," . $txt_spandex_stitch_length . "," . $txt_draft_ratio . "," . $machine_id . "," . $txt_machine_capacity . "," . $txt_distribution_qnty . "," . $cbo_knitting_status . "," . $txt_start_date . "," . $txt_end_date . "," . $txt_program_date . "," . $cbo_feeder . "," . $txt_remarks . "," . $save_data . "," . $cbo_location_name . "," . $hidden_advice_data . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

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
		$field_array_feeder_dtls = "id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder, inserted_by, insert_date";

		$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
		if ($hidden_no_of_feeder_data != "") {
			$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
			for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++) {
				$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
				$pre_cost_id = $color_wise_data[0];
				$color_id = $color_wise_data[1];
				$stripe_color_id = $color_wise_data[2];
				$no_of_feeder = $color_wise_data[3];

				if ($data_array_feeder_dtls != "") $data_array_feeder_dtls .= ",";

				$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $id . "," . $dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$feeder_dtls_id = $feeder_dtls_id + 1;
			}
		}

		$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
		if ($hidden_collarCuff_data != "") {
			$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
			$field_array_collar_cuff_dtls = "id, mst_id, dtls_id, body_part_id, grey_size, finish_size, qty_pcs, inserted_by, insert_date,is_sales";

			$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
			for ($i = 0; $i < count($hidden_collarCuff_data); $i++) {
				$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
				$body_part_id = $collarCuff_wise_data[0];
				$grey_size = $collarCuff_wise_data[1];
				$finish_size = $collarCuff_wise_data[2];
				$qty_pcs = $collarCuff_wise_data[3];

				if ($data_array_collar_cuff_dtls != "") $data_array_collar_cuff_dtls .= ",";

				$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $id . "," . $dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1)";

				$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
			}
		}
		
		if (str_replace("'", '', $updateId) == "") {
			$rID = sql_insert("ppl_planning_info_entry_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1; else $flag = 0;
		} else {
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
		$rmsintegretion =return_field_value("rms_integretion","variable_settings_production","company_name='$companyID' and variable_list=7 and is_deleted=0 and status_active=1");
		if($rmsintegretion==1) // rms integretion: yes
		{
			$exists_sl = sql_select("select a.booking_no,max(a.update_sl) as update_sl_no from ppl_planning_entry_plan_dtls a  where a.booking_no = '$booking_no' and a.status_active = 1 and a.is_deleted = 0 group by booking_no");

			$exists_slNo = $exists_sl[0][csf('update_sl_no')];
			
			if($exists_slNo>0)
			{
				$incrementSl = $exists_slNo+1; 
			}

			$rmsIntegretionID = execute_query("update ppl_planning_entry_plan_dtls set update_sl=$incrementSl  where dtls_id=$dtls_id", 0);

			if ($rmsIntegretionID) $flag = 1; else $flag = 0;			
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

		//echo "10**";
		//echo $rID . "_" . $rID2 . "_" . $rID3 . "_" . $rID4 . "_" . $rID5;
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
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

 		// CHECK IF (PROGRAM QUANTITY+EXISTING PROGRAM QNTY) IS NOT GREATER THAN BOOKING QNTY
		$get_existing_program_qty = return_field_value("sum(program_qnty) as program_qnty", "ppl_planning_entry_plan_dtls", "booking_no='$booking_no' and mst_id=$updateId and dtls_id != $update_dtls_id and status_active=1 and is_deleted=0", "program_qnty");
		if ((str_replace("'", "", $txt_program_qnty)+($get_existing_program_qty*1)) > str_replace("'", "", $hdn_booking_qnty)) {
			echo "14**Program quantity can not be greater than Booking quantity";
			disconnect($con);
			exit();
		}

		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($knit_qty > str_replace("'", "", $txt_program_qnty)) {
			echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
			disconnect($con);
			exit();
		}

		$color_id = 0;
		$field_array_update = "knitting_source*knitting_party*color_id*color_range*machine_dia*width_dia_type*machine_gg*fabric_dia*program_qnty*stitch_length*spandex_stitch_length*draft_ratio*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*program_date*feeder*remarks*save_data*location_id*advice*updated_by*update_date";

		$data_array_update = $cbo_knitting_source . "*" . $cbo_knitting_party . "*" . $hidden_color_id . "*" . $cbo_color_range . "*" . $txt_machine_dia . "*" . $cbo_dia_width_type . "*" . $txt_machine_gg . "*" . $txt_fabric_dia . "*" . $txt_program_qnty . "*" . $txt_stitch_length . "*" . $txt_spandex_stitch_length . "*" . $txt_draft_ratio . "*" . $machine_id . "*" . $txt_machine_capacity . "*" . $txt_distribution_qnty . "*" . $cbo_knitting_status . "*" . $txt_start_date . "*" . $txt_end_date . "*" . $txt_program_date . "*" . $cbo_feeder . "*" . $txt_remarks . "*" . $save_data . "*" . $cbo_location_name . "*" . $hidden_advice_data . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

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
		//

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

				/*if ($dtls_id == "") {
					if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
					$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$machine_plan_id = $machine_dtls_id;
					$machine_dtls_id = $machine_dtls_id + 1;
				} else {
					$dtlsId_arr[] = $dtls_id;
					$data_array_update_dtls[$dtls_id] = explode("*", ($machine_id . "*'" . $dia . "'*'" . $capacity . "'*'" . $qnty . "'*'" . $noOfDays . "'*'" . $mstartDate . "'*'" . $mendDate . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
					$machine_plan_id = $dtls_id;
				}*/

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

		$feeder_dtls_id = return_next_id("id", "ppl_planning_feeder_dtls", 1);
		$field_array_feeder_dtls = "id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder, inserted_by, insert_date";

		$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
		if ($hidden_no_of_feeder_data != "") {
			$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
			for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++) {
				$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
				$pre_cost_id = $color_wise_data[0];
				$color_id = $color_wise_data[1];
				$stripe_color_id = $color_wise_data[2];
				$no_of_feeder = $color_wise_data[3];

				if ($data_array_feeder_dtls != "") $data_array_feeder_dtls .= ",";

				$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$feeder_dtls_id = $feeder_dtls_id + 1;
			}
		}

		$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
		if ($hidden_collarCuff_data != "") {
			$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
			$field_array_collar_cuff_dtls = "id, mst_id, dtls_id, body_part_id, grey_size, finish_size, qty_pcs, inserted_by, insert_date";

			$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
			for ($i = 0; $i < count($hidden_collarCuff_data); $i++) {
				$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
				$body_part_id = $collarCuff_wise_data[0];
				$grey_size = $collarCuff_wise_data[1];
				$finish_size = $collarCuff_wise_data[2];
				$qty_pcs = $collarCuff_wise_data[3];

				if ($data_array_collar_cuff_dtls != "") $data_array_collar_cuff_dtls .= ",";

				$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
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

		/*$deletem=execute_query( "delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id",0);
		if($flag==1)
		{
			if($deletem) $flag=1; else $flag=0;
		}*/

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

			/*if (count($data_array_update_dtls) > 0) {
				$rID_update = execute_query(bulk_update_sql_statement("ppl_planning_info_machine_dtls", "id", $field_array_machine_dtls_update, $data_array_update_dtls, $dtlsId_arr));
				if ($flag == 1) {
					if ($rID_update) $flag = 1; else $flag = 0;
				}
			}*/

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
			$field_array_count_feeding_dtls = "id, mst_id, dtls_id, seq_no, count_id,feeding_id, inserted_by, insert_date";

			$hidden_count_feeding_data_arr = explode(",", $hidden_count_feeding_data);
			for ($i = 0; $i < count($hidden_count_feeding_data_arr); $i++) {
				$count_feeding_data_arr = explode("_", $hidden_count_feeding_data_arr[$i]);
				$seq_no = $count_feeding_data_arr[0];
				$count_id = $count_feeding_data_arr[1];
				$feeding_id = $count_feeding_data_arr[2];
				if ($data_array_count_feeding_dtls != "") $data_array_count_feeding_dtls .= ",";
				$data_array_count_feeding_dtls .= "(" . $count_feeding_id . "," . $updateId . "," . $update_dtls_id . "," . $seq_no . "," . $count_id . "," . $feeding_id. "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$count_feeding_id = $count_feeding_id + 1;
			}


			$delete_feeding = execute_query("delete from ppl_planning_count_feed_dtls where dtls_id=$update_dtls_id", 0);
			if($delete_feeding) $flag = 1; else $flag = 0;
			if($flag == 1 && $data_array_count_feeding_dtls) 
			{
				$rID7 = sql_insert("ppl_planning_count_feed_dtls", $field_array_count_feeding_dtls, $data_array_count_feeding_dtls, 0);
				if ($rID7) $flag = 1; else $flag = 0;
			}
		}

		//---------------------------------

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
	} else if ($operation == 2) {
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

		$delete = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete) $flag = 1; else $flag = 0;
		}

		$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete_datewise) $flag = 1; else $flag = 0;
		}

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

if ($action == "advice_info_popup") {
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

if ($action == "machine_info_popup") {
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<style type="text/css">
		
		.event a {
		    background-color: #42B373 !important;
		    background-image :none !important;
		    color: #ffffff !important;
		}
	</style>

	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function calculate_qnty(tr_id) {
			var distribution_qnty = $('#txt_distribution_qnty_' + tr_id).val() * 1;
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

       freeDays.push(value.freeDate); 

        function fn_machine_book_dates(row_no) 
        {			
			var machine_id = $('#txt_individual_id' + row_no).val();
			var dates = "";
			if(machine_id!="")
			{
				var update_dtls_id = '<? echo $update_dtls_id; ?>';
				var data ={"machine_id":machine_id,"update_dtls_id":update_dtls_id}

				$.ajax({
				  	type: "POST",
				  	url: "planning_info_entry_for_sales_order_controller.php?action=machine_allready_book_dates",
				  	data: data,
				  	cache: false,
				  	dataType: "json",

				  	success: function(response){
				  		//console.log(response);
				  		//var dates = ['10-04-2019', '25-04-2019', '29-04-2019'];
				  		//var tips  = ['All rady book'];   
				  		 $('.calendar5').datepicker({
					        beforeShowDay: function (date) {
						      for (var i = 0; i < response.length; i++) {
						        if (response[i] == $.datepicker.formatDate('dd-mm-yy', date)) {
						          //console.log(date);
						          return [0, 'event', 'All ready book this date'];
						        }
						      }
						      return [true];
						    },
				            dateFormat: 'dd-mm-yy',
							changeMonth: true,
							changeYear: true
				        });

				 	}
				})/*.done(function (response) {
			        if (response!="") {
			           
			            $('.calendar5').datepicker({
					        beforeShowDay: function (date) {
						      for (var i = 0; i < response.length; i++) {
						        if (response[i] == $.datepicker.formatDate('dd-mm-yy', date)) {
						          console.log(date);
						          return [true, 'event', tips[i]];
						        }
						      }
						      return [true];
						    },
				            dateFormat: 'dd-mm-yy',
							changeMonth: true,
							changeYear: true
				        }); 
			        } 
			    });*/
			}

        }

        //var dates = ['04/12/2019', '04/15/2019']; //
   		

       /* function highlightDays(date) {

	        for (var i = 0; i < dates.length; i++) {
	            if (new Date(dates[i]).toString() == date.toString()) {              
	                return [true, 'event', tips[i]];
	            }
	    	}
        	return [true, ''];
     	} 
		*/


     	
		
        jQuery(document).ready(function() {
	        
	        // An array of dates
	       /* var eventDates = {};
	        eventDates[ new Date( '04/04/2019' )] = new Date( '04/04/2019' );
	        eventDates[ new Date( '04/06/2019' )] = new Date( '04/06/2019' );
	        eventDates[ new Date( '04/20/2019' )] = new Date( '04/20/2019' );
	        eventDates[ new Date( '04/25/2019' )] = new Date( '04/25/2019' );*/

	        // datepicker
	      				 
	    });


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

						for ($i = 0; $i < count($save_string); $i++) {
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
						

						$sql = "select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where company_id=$companyID and category_id=1 and status_active=1 and is_deleted=0 $machinCond order by seq_no";// and dia_width='$txt_machine_dia'
						$result = sql_select($sql);

						$i = 1;
						$tot_capacity = 0;
						$tot_distribution_qnty = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$capacity = $qnty_array[$row[csf('id')]]['capacity'];
							if ($capacity == "") {
								$capacity = $row[csf('prod_capacity')];
							}

							$distribution_qnty = $qnty_array[$row[csf('id')]]['distribution'];

							if ($distribution_qnty > 0) $bgcolor = "yellow"; else $bgcolor = $bgcolor;

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
									class="calendar5" style="width:67px" value="<? echo $startDate; ?>"
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

if ($action == "date_duplication_check") {
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


if ($action == "machine_allready_book_dates") {
	extract($_REQUEST);
	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' group by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	if (count($data_array) > 0) {
		$bookeDates = array();
		foreach ($data_array as $row) {
			if ($row[csf('days_complete')] >= 1) {	
				$dateslist[] = date("d-m-Y", strtotime($row[csf('distribution_date')]));	
			}
		}
	}

	echo json_encode($dateslist);


	exit();
}



if ($action == "color_info_popup") {
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
            //set_all();
        });

		var selected_id = new Array, selected_name = new Array();
		var selected_qnty = new Array();

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

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_individual_id' + str).val());
				selected_name.push($('#txt_individual' + str).val());
				selected_qnty.push($('#txt_individual_qnty' + str).val());

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_qnty.splice(i, 1);
			}
			var id = '';
			var name = '';
			var qnty = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				qnty += selected_qnty[i] + ',';

			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			qnty = qnty.substr(0, qnty.length - 1);
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_qnty').val(qnty);
		}
	</script>

</head>

<body>
	<div align="center" style="width:390px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:380px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="360" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="160">Color</th>
							<th>Qnty</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" value=""/>
							<input type="hidden" name="txt_selected_qnty" id="txt_selected_qnty" value=""/>
						</thead>
					</table>
					<div style="width:360px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="342" class="rpt_table"
						id="tbl_list_search">
						<tbody>
							<?
							$sales_dtls_id = "";
							$hidden_color_id = explode(",", $hidden_color_id);
							$program_color_id = array_unique(explode(",", $program_color_id));
							$sales_job_ids = explode("_", $sales_order_dtls_id);
							foreach ($sales_job_ids as $row) {
								$sales_dtls_id .= "," . $row;
							}
							$sales_dtls_id = ltrim($sales_dtls_id, ",");
							$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
							$sql = "select color_id, sum(grey_qty) as qty from fabric_sales_order_dtls where status_active=1 and is_deleted=0 and mst_id=$job_id and id in($sales_dtls_id) group by color_id";
							$result = sql_select($sql);

							$i = 1;
							$tot_qnty = 0;
							foreach ($result as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$tot_qnty += $row[csf('qty')];
								if (in_array($row[csf('color_id')], $hidden_color_id)) {
									if ($color_row_id == "") $color_row_id = $i; else $color_row_id .= "," . $i;
								}

								if (in_array($row[csf('color_id')], $program_color_id)) {
									$color = "background-color:lightgreen;";
								} else {
									$color = "";
								}
								if (in_array($row[csf('color_id')], $hidden_color_id)) {
									$selected_color = "background-color:green;";
								} else {
									$selected_color = "";
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"
									style="text-decoration:none; cursor:pointer; <?php echo $color; ?> <?php echo $selected_color; ?>"
									id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="40" align="center"><? echo $i; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>"
										value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>
										<input type="hidden" name="txt_individual_qnty"
										id="txt_individual_qnty<?php echo $i; ?>"
										value="<? echo $row[csf('qty')]; ?>"/>
									</td>
									<td width="160"
									style="padding: 1px 10px;"><? echo $color_library[$row[csf('color_id')]]; ?></td>
									<td align="right"
									style="padding: 1px 10px;"><? echo number_format($row[csf('qty')], 2); ?></td>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" name="txt_color_row_id" id="txt_color_row_id"
							value="<?php echo $color_row_id; ?>"/>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" align="right"><b>Total</b></th>
								<th align="right"
								style="padding: 1px 10px;"><? echo number_format($tot_qnty, 2); ?></th>
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
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
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

if ($action == "style_ref_search_popup") {
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

if ($action == "create_job_search_list_view") {
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
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond $po_buyer_id_cond and b.company_id=$buyer_id and fabric_source in(1,2)
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,WO_NON_ORD_SAMP_BOOKING_MST b,wo_non_ord_samp_booking_dtls c where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond $po_buyer_id_cond  and b.company_id=$buyer_id and (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num,c.fabric_source";

	} else {
		$sql = " select a.id, to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and within_group=$within_group $search_field_cond order by a.id";
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

if ($action == "booking_no_search_popup") {
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_no) {
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
				<table cellpadding="0" cellspacing="0" width="745" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Po Buyer</th>
						<th>Booking Date</th>
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
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'planning_info_entry_for_sales_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:90px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if ($action == "create_booking_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);

	if ($buyer_id == 0) {
		$buyer_id_cond = "";
	} else {
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) {
			if ($cbo_within_group == 1) {
				$search_field_cond = "and a.booking_no like '$search_string'";
			} else {
				$search_field_cond = "and c.sales_booking_no like '$search_string'";
			}
		} else {
			$search_field_cond = "and a.job_no like '$search_string'";
		}
	}

	$date_cond = '';
	if ($cbo_within_group == 1) {

	}
	$date_field = ($cbo_within_group == 2) ? "c.booking_date" : "a.booking_date";
	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

	if ($cbo_within_group == 1) {
		$sql = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no from wo_booking_mst a, wo_po_details_master b,fabric_sales_order_mst c where a.job_no=b.job_no and a.booking_no=c.sales_booking_no and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,a.po_break_down_id,b.job_no,b.style_ref_no";
	} else {
		$sql = "select c.id, c.sales_booking_no booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no 
		from fabric_sales_order_mst c where c.company_id=$company_id and c.status_active =1 and c.is_deleted=0 $date_cond $search_field_cond and c.within_group=2
		group by c.id, c.sales_booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no";
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
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "") {
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id) {
					if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
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

		$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c,fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
		$result_partial = sql_select($sql_partial);
		foreach ($result_partial as $row) {
			if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "") {
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id) {
					if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
				<td width="40"><? echo $j; ?>p</td>
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

if ($action == "load_drop_down_knitting_party") {
	$data = explode("**", $data);
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "", "");
	} else if ($data[0] == 3) {
		if ($data[2] == 1) $selected_id = $data[1]; else $selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 177, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "");
	} else {
		echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 0, "");
	}
	exit();
}

if ($action == "delete_program") {
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

if ($action == "update_program") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$program_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

	if ($knit_qty > $prog_qty) {
		echo "20";
		disconnect($con);
		exit();
	}

	$field_array_update = "program_qnty*updated_by*update_date";
	$data_array_update = $prog_qty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
	$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $program_id, 0);

	if ($db_type == 0) {
		if ($rID) {
			mysql_query("COMMIT");
			echo "1";
		} else {
			mysql_query("ROLLBACK");
			echo "6";
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($rID) {
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

if($action == "activePlan"){
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
	foreach ($prog_no as $program) {		
		$rID2 = execute_query("update ppl_planning_info_entry_dtls set program_qnty=$program_qnty,color_id='$color_ids',status_active=1, is_deleted=0,$revised_sql updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where mst_id in($plan_id) and id in($program)", 0);
		if($rID2==1) $flag=1; else $flag=0;		
		$rID3 = execute_query("update ppl_planning_entry_plan_dtls set program_qnty=$program_qnty,sales_order_dtls_ids='$dtls_ids',status_active=1, is_deleted=0,$revised_sql updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where mst_id in($plan_id) and dtls_id in($program)", 0);
		if($rID3==1) $flag=1; else $flag=0;		
	}
	$rID3 = execute_query("update fabric_sales_order_dtls set status_active=1, is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in($dtls_ids)", 0);

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
?>