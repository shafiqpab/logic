<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

// ppl_gsd_entry_mst
// ppl_balancing_mst_entry

if ($action == "populate_data_from_breakdown") {
	$sql = "SELECT id, buyer_id, style_ref, gmts_item_id FROM ppl_gsd_entry_mst where id=$data";
	// echo $sql;die;

	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_style_ref_lo').value			= '" . $row[csf("style_ref")] . "';\n";
		echo "document.getElementById('cbo_buyer_lo').value 			= '" . $row[csf("buyer_id")] . "';\n";
		echo "document.getElementById('cbo_gmt_item_lo').value 			= '" . $row[csf("gmts_item_id")] . "';\n";
		echo "document.getElementById('breakdown_id4').value 			= '" . $row[csf("id")] . "';\n";

		$balanceId = return_field_value("id", "ppl_balancing_mst_entry", "gsd_mst_id='" . $row[csf("id")] . "' and balancing_page=1 and status_active=1 and is_deleted=0");
		//echo $balanceId;die;

		echo "document.getElementById('balanceId').value 			= '" . $balanceId . "';\n";

		$balanceData = sql_select("select id, line_shape, no_of_work_st, layout_date from ppl_balancing_mst_entry where gsd_mst_id='" . $row[csf("id")] . "' and balancing_page=3 and status_active=1 and is_deleted=0");
		
		echo "document.getElementById('cbo_line_shape').value			= '" . $balanceData[0][csf("line_shape")] . "';\n";
		echo "document.getElementById('txt_no_of_work_st').value		= '" . $balanceData[0][csf("no_of_work_st")] . "';\n";
		echo "document.getElementById('txt_layout_date').value 			= '" . change_date_format($balanceData[0][csf("layout_date")]) . "';\n";
		echo "document.getElementById('lo_update_id').value 			= '" . $balanceData[0][csf("id")] . "';\n";

		if ($balanceData[0][csf("id")] > 0) {
			echo "load_data();\n";
			echo "set_button_status(1, '" . $permission . "', 'fnc_layout_entry',5);\n";
		} else {
			echo "set_button_status(0, '" . $permission . "', 'fnc_layout_entry',5);\n";
		}

		exit();
	}
}

if ($action == "show_operation_list") {
	$data = explode("_", $data);
	$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id", "operation_name");
	$arr = array(0 => $operation_arr, 1 => $production_resource);

	$prev_entry_opa_arr = array();
	if ($data[1] > 0) {
		$layoutData = sql_select("select gsd_dtls_id from ppl_layout_dtls_entry where mst_id='" . $data[1] . "'  and is_deleted=0");
		foreach ($layoutData as $row) {
			$prev_entry_opa_arr[$row[csf('gsd_dtls_id')]] = $row[csf('gsd_dtls_id')];
		}
	}

	$layout_mp_arr = return_library_array("select gsd_dtls_id,layout_mp from ppl_balancing_dtls_entry", "gsd_dtls_id", "layout_mp");

	$sql = "SELECT a.PROCESS_ID,b.id, b.lib_sewing_id, b.row_sequence_no, b.resource_gsd, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a, ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=" . $data[0] . " and b.is_deleted=0 order by b.row_sequence_no asc";
	//echo $sql;die;
	$result = sql_select($sql);

	$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$result[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");

?>
	<div style="width:408px; ">
		<table width="388" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30"></th>
					<th width="40">SL</th>
					<th width="150">Operation Name</th>
					<th width="80">Resource</th>
					<th>Layout MP</th>
				</tr>
			</thead>
		</table>

		<table cellpadding="0" cellspacing="0" border="1" rules="all" width="388" class="rpt_table" id="list_view_lo">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#FFFFFF";
				else $bgcolor = "#E9F3FF";

				if (in_array($row[csf('id')], $prev_entry_opa_arr)) {
					$bgcolor = "#33CC00";
				}

				$data = $row[csf('id')] . "**" . $row[csf('lib_sewing_id')] . "**" . $row[csf('row_sequence_no')] . "**" . $row[csf('resource_gsd')] . "**" . number_format($row[csf('total_smv')], 2, '.', '') . "**" . number_format($row[csf('target_on_full_perc')], 2, '.', '') . "**" . $operation_arr[$row[csf('lib_sewing_id')]] . "**" . $production_resource_arr[$row[csf('resource_gsd')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td width="30" align="center" valign="middle" onClick="change_colors('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>"><input type="radio" name="copyRow" id="copyRow_<? echo $i; ?>" value="<? echo $i; ?>" /></td>
					<td width="40"><? echo $i; ?><input type="hidden" name="hiddenData[]" id="hiddenData_<? echo $i; ?>" value="<? echo $data; ?>" /></td>
					<td width="150">
						<p><? echo $operation_arr[$row[csf('lib_sewing_id')]]; ?></p>
					</td>
					<td width="80">
						<p><? echo $production_resource_arr[$row[csf('resource_gsd')]]; ?></p>
					</td>
					<td align="right"><? echo $layout_mp_arr[$row[csf('id')]]; ?>&nbsp;</td>
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

if ($action == "details_list_view") {
	 
	$data = explode("**", $data);
	// print_r( $data);die;
	$update_id = $data[0]; //2006
	$bl_update_id = $data[1]; //1489
	$line_shape = $data[2];
	$lo_update_id = $data[3];
	$NoOfWorker = $data[4]; // 1490

	//if($line_shape==0) {die;}

	$layoutDataArr = array();
	$gsdDataArr = array();
	/*$workerTrackingArr=array(); $NoOfWorker=0;
	$blData=sql_select("select worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id");
	foreach($blData as $row)
	{
		if($row[csf('worker_tracking')]=="" || !in_array($row[csf('worker_tracking')],$workerTrackingArr))
		{
			$NoOfWorker++;
		}
		$workerTrackingArr[]=$row[csf('worker_tracking')];
	}*/

	// echo $lo_update_id;die;


	if ($lo_update_id > 0) {
		$layoutData = sql_select("select gsd_dtls_id, work_station from ppl_layout_dtls_entry where mst_id=$lo_update_id and is_deleted=0");
		foreach ($layoutData as $row) {
			$layoutDataArr[$row[csf('work_station')]] .= $row[csf('gsd_dtls_id')] . ",";
		}

		//echo "<pre>";
		//print_r($layoutDataArr);die;



		$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id", "operation_name");

		$sql = "SELECT a.PROCESS_ID,b.id, b.lib_sewing_id, b.row_sequence_no, b.resource_gsd, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$update_id and b.is_deleted=0";
		//echo $sql;die;

		$result = sql_select($sql);
		foreach ($result as $row) {
			$gsdDataArr[$row[csf('id')]]['lib_sewing_id'] = $row[csf('lib_sewing_id')];
			$gsdDataArr[$row[csf('id')]]['row_sequence_no'] = $row[csf('row_sequence_no')];
			$gsdDataArr[$row[csf('id')]]['resource_gsd'] = $row[csf('resource_gsd')];
			$gsdDataArr[$row[csf('id')]]['total_smv'] = $row[csf('total_smv')];
			$gsdDataArr[$row[csf('id')]]['target_on_full_perc'] = $row[csf('target_on_full_perc')];
		}
	}
	// echo $line_shape;die;
	//print_r($layoutDataArr);

	$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$result[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");


	if ($line_shape == 2) {
		$i = 1;
		$noOfRow = 0;
		$firstHalf = round($NoOfWorker / 2);
		$secondHalf = round($NoOfWorker - $firstHalf);

		echo '<table><tr><td valign="top">';
		echo '<table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="tbl_list_layout">';
		echo '<thead><th width="20"></th>
                <th width="80">Resource</th>
				<th width="50">Seq. No.</th>
				<th width="120">Operation</th>
                <th width="50">SMV</th>
                <th width="50">Target</th><th></th>';
		echo '</thead><tbody>';

		// echo $NoOfWorker;die;
		// for($z=$firstHalf;$z>=1;$z--)
		for ($z = $NoOfWorker; $z >= 1; $z--) {
			if ($z % 2 == 1) {
				$work_stations = explode(",", chop($layoutDataArr[$z], ','));
				if (count($work_stations) > 0) {
					foreach ($work_stations as $dtlsId) {
						$noOfRow++;
						if ($i % 2 == 0) $bgcolor = "#FFFFFF";
						else $bgcolor = "#E9F3FF";
	                    ?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="trSt_<? echo $i; ?>">
							<td><? echo $z; ?></td>
							<td>
								<input type="text" name="rescName[]" id="rescName_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly" onclick="copy_data(<? echo $i; ?>,1);" placeholder="Click Copy Operation" value="<? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?>">
								<input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" style="width:68px" value="<? echo $gsdDataArr[$dtlsId]['resource_gsd']; ?>">
								<input type="hidden" name="gsdDtlsId[]" id="gsdDtlsId_<? echo $i; ?>" style="width:68px" value="<? echo $dtlsId; ?>">
								<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" style="width:68px" value="1">
							</td>
							<td><input type="text" name="seqNoL[]" id="seqNoL_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?>" style="width:38px" class="text_boxes" disabled="disabled"></td>
							<td>
								<input type="text" name="operation[]" id="operation_<? echo $i; ?>" value="<? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?>" style="width:108px" class="text_boxes" disabled="disabled">
								<input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['lib_sewing_id']; ?>">
							</td>
							<td><input type="text" name="smv[]" id="smv_<? echo $i; ?>" value="<? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?>" style="width:38px" class="text_boxes_numeric" disabled="disabled"></td>
							<td><input type="text" name="tgtPercL[]" id="tgtPercL_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?>" style="width:38px" class="text_boxes_numeric" disabled="disabled"></td>
							<td width="45">
								<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:20px" class="formbutton" value="+" onClick="add_tr(<? echo $i; ?>,1)" />
								<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:20px" class="formbutton" value="-" onClick="deleteRow(<? echo $i; ?>,1);" />
							</td>
						</tr>
					    <?
						$i++;
					}
				} else {
					$noOfRow++;
					if ($i % 2 == 0) $bgcolor = "#FFFFFF";
					else $bgcolor = "#E9F3FF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="trSt_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td>
							<input type="text" name="rescName[]" id="rescName_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly" onclick="copy_data(<? echo $i; ?>,1);" placeholder="Click Copy Operation">
							<input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly">
							<input type="hidden" name="gsdDtlsId[]" id="gsdDtlsId_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly">
							<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" style="width:68px" value="1">
						</td>
						<td><input type="text" name="seqNoL[]" id="seqNoL_<? echo $i; ?>" value="" style="width:38px" class="text_boxes" disabled="disabled"></td>
						<td>
							<input type="text" name="operation[]" id="operation_<? echo $i; ?>" value="" style="width:108px" class="text_boxes" disabled="disabled">
							<input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>">
						</td>
						<td><input type="text" name="smv[]" id="smv_<? echo $i; ?>" value="" style="width:38px" class="text_boxes_numeric" disabled="disabled"></td>
						<td><input type="text" name="tgtPercL[]" id="tgtPercL_<? echo $i; ?>" value="" style="width:38px" class="text_boxes_numeric" disabled="disabled"></td>
						<td width="45">
							<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:20px" class="formbutton" value="+" onClick="add_tr(<? echo $i; ?>,1)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:20px" class="formbutton" value="-" onClick="deleteRow(<? echo $i; ?>,1)" />
						</td>
					</tr>
					<?
					$i++;
				}
			}
		}
		echo '</tbody></table></td><td width="10"></td>';
		echo '<td valign="top"><table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="tbl_list_layout2">';
		echo '<thead><th width="20"></th>
                <th width="80">Resource</th>
				<th width="50">Seq. No.</th>
				<th width="120">Operation</th>
                <th width="50">SMV</th>
                <th width="50">Target</th><th></th>';
		echo '</thead><tbody>';
		//for($z=$firstHalf+1;$z<=$NoOfWorker;$z++)
		for ($z = $NoOfWorker; $z >= 1; $z--) {
			if ($z % 2 == 0) {
				$work_stations = explode(",", chop($layoutDataArr[$z], ','));
				if (count($work_stations) > 0) {
					foreach ($work_stations as $dtlsId) {
						$noOfRow++;
						if ($i % 2 == 0) $bgcolor = "#FFFFFF";
						else $bgcolor = "#E9F3FF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="trSt_<? echo $i; ?>">
							<td><? echo $z; ?></td>
							<td>
								<input type="text" name="rescName[]" id="rescName_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly" onclick="copy_data(<? echo $i; ?>,2);" placeholder="Click Copy Operation" value="<? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?>">
								<input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" style="width:68px" value="<? echo $gsdDataArr[$dtlsId]['resource_gsd']; ?>">
								<input type="hidden" name="gsdDtlsId[]" id="gsdDtlsId_<? echo $i; ?>" style="width:68px" value="<? echo $dtlsId; ?>">
								<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" style="width:68px" value="1">
							</td>
							<td><input type="text" name="seqNoL[]" id="seqNoL_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?>" style="width:38px" class="text_boxes" disabled="disabled"></td>
							<td>
								<input type="text" name="operation[]" id="operation_<? echo $i; ?>" value="<? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?>" style="width:108px" class="text_boxes" disabled="disabled">
								<input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['lib_sewing_id']; ?>">
							</td>
							<td><input type="text" name="smv[]" id="smv_<? echo $i; ?>" value="<? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?>" style="width:38px" class="text_boxes_numeric" disabled="disabled"></td>
							<td><input type="text" name="tgtPercL[]" id="tgtPercL_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?>" style="width:38px" class="text_boxes_numeric" disabled="disabled"></td>
							<td width="45">
								<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:20px" class="formbutton" value="+" onClick="add_tr(<? echo $i; ?>,2)" />
								<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:20px" class="formbutton" value="-" onClick="deleteRow(<? echo $i; ?>,2);" />
							</td>
						</tr>
					<?
						$i++;
					}
				} else {
					$noOfRow++;
					if ($i % 2 == 0) $bgcolor = "#FFFFFF";
					else $bgcolor = "#E9F3FF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="trSt_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td>
							<input type="text" name="rescName[]" id="rescName_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly" onclick="copy_data(<? echo $i; ?>,2);" placeholder="Click Copy Operation">
							<input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" style="width:68px" class="text_boxes" value="">
							<input type="hidden" name="gsdDtlsId[]" id="gsdDtlsId_<? echo $i; ?>" style="width:68px" class="text_boxes" value="">
							<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" style="width:68px" value="1">
						</td>
						<td><input type="text" name="seqNoL[]" id="seqNoL_<? echo $i; ?>" value="" style="width:38px" class="text_boxes" readonly="readonly"></td>
						<td>
							<input type="text" name="operation[]" id="operation_<? echo $i; ?>" value="" style="width:108px" class="text_boxes" readonly="readonly">
							<input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>">
						</td>
						<td><input type="text" name="smv[]" id="smv_<? echo $i; ?>" value="" style="width:38px" class="text_boxes_numeric" readonly="readonly"></td>
						<td><input type="text" name="tgtPercL[]" id="tgtPercL_<? echo $i; ?>" value="" style="width:38px" class="text_boxes_numeric" readonly="readonly"></td>
						<td width="45">
							<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:20px" class="formbutton" value="+" onClick="add_tr(<? echo $i; ?>,2)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:20px" class="formbutton" value="-" onClick="deleteRow(<? echo $i; ?>,2)" />
						</td>
					</tr>
				<?
					$i++;
				}
			}
		}
		echo '</tbody></table><input type="hidden" name="txt_tot_row" id="txt_tot_row" value="' . $noOfRow . '"></td></tr></table>';
	}

	else {
		echo '<table cellpadding="0" cellspacing="0" border="1" rules="all" width="630" class="rpt_table" id="tbl_list_layout">';
		echo '<thead><th width="45"></th>
                <th width="90">Resource</th>
				<th width="80">Seq. No.</th>
				<th width="150">Operation</th>
                <th width="80">SMV</th>
                <th width="50">Target</th><th></th>';
		echo '</thead><tbody>';

		$noOfRow = 0;
		$i = 1;
		$work_stations_arr = array();
		for ($z = $NoOfWorker; $z >= 1; $z--) {
			$work_stations = explode(",", chop($layoutDataArr[$z], ','));
			if (count($work_stations) > 0) {
				foreach ($work_stations as $dtlsId) {
					if ($i % 2 == 0) $bgcolor = "#FFFFFF";
					else $bgcolor = "#E9F3FF";
					if (in_array($z, $work_stations_arr)) {
						$is_original = 0;
					} else {
						$is_original = 1;
						$work_stations_arr[] = $z;
					}
					$noOfRow++;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="trSt_<? echo $i; ?>">
						<td><? echo $z; ?></td>
						<td>
							<input type="text" name="rescName[]" id="rescName_<? echo $i; ?>" style="width:78px" class="text_boxes" readonly="readonly" onclick="copy_data(<? echo $i; ?>,1);" placeholder="Click Copy Operation" value="<? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?>">
							<input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" style="width:68px" value="<? echo $gsdDataArr[$dtlsId]['resource_gsd']; ?>">
							<input type="hidden" name="gsdDtlsId[]" id="gsdDtlsId_<? echo $i; ?>" style="width:68px" value="<? echo $dtlsId; ?>">
							<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" style="width:68px" value="<? echo $is_original; ?>">
						</td>
						<td><input type="text" name="seqNoL[]" id="seqNoL_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?>" style="width:68px" class="text_boxes" disabled="disabled"></td>
						<td>
							<input type="text" name="operation[]" id="operation_<? echo $i; ?>" value="<? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?>" style="width:138px" class="text_boxes" disabled="disabled">
							<input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['lib_sewing_id']; ?>">
						</td>
						<td><input type="text" name="smv[]" id="smv_<? echo $i; ?>" value="<? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?>" style="width:68px" class="text_boxes_numeric" disabled="disabled"></td>
						<td><input type="text" name="tgtPercL[]" id="tgtPercL_<? echo $i; ?>" value="<? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?>" style="width:68px" class="text_boxes_numeric" disabled="disabled"></td>
						<td width="65">
							<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_tr(<? echo $i; ?>,1)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="deleteRow(<? echo $i; ?>,1);" />
						</td>
					</tr>
				<?
					$i++;
				}
			} else {
				$noOfRow++;
				if ($i % 2 == 0) $bgcolor = "#FFFFFF";
				else $bgcolor = "#E9F3FF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="trSt_<? echo $i; ?>">
					<td><? echo $z; ?></td>
					<td>
						<input type="text" name="rescName[]" id="rescName_<? echo $i; ?>" style="width:78px" class="text_boxes" readonly="readonly" onclick="copy_data(<? echo $i; ?>,1);" placeholder="Click Copy Operation">
						<input type="hidden" name="rescId[]" id="rescId_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly">
						<input type="hidden" name="gsdDtlsId[]" id="gsdDtlsId_<? echo $i; ?>" style="width:68px" class="text_boxes" readonly="readonly">
						<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" style="width:68px" value="1">
					</td>
					<td><input type="text" name="seqNoL[]" id="seqNoL_<? echo $i; ?>" value="" style="width:68px" class="text_boxes" disabled="disabled"></td>
					<td>
						<input type="text" name="operation[]" id="operation_<? echo $i; ?>" value="" style="width:138px" class="text_boxes" disabled="disabled">
						<input type="hidden" name="sewingId[]" id="sewingId_<? echo $i; ?>">
					</td>
					<td><input type="text" name="smv[]" id="smv_<? echo $i; ?>" value="" style="width:68px" class="text_boxes_numeric" disabled="disabled"></td>
					<td><input type="text" name="tgtPercL[]" id="tgtPercL_<? echo $i; ?>" value="" style="width:68px" class="text_boxes_numeric" disabled="disabled"></td>
					<td width="65">
						<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_tr(<? echo $i; ?>,1)" />
						<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="deleteRow(<? echo $i; ?>,1);" />
					</td>
				</tr>
	<?
				$i++;
			}
		}

		echo '<input type="hidden" name="txt_tot_row" id="txt_tot_row" value="' . $noOfRow . '"></tbody></table>';
	}
	exit();
}




if ($action == "show_summary") {
	$balanceDataArray = array();
	$blData = sql_select("select a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 and a.gsd_mst_id=$data and a.is_deleted=0 and b.is_deleted=0");
	foreach ($blData as $row) {
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv'] = $row[csf('smv')];
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp'] = $row[csf('layout_mp')];
	}

	$sqlDtls = "SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id='" . $data . "' and b.is_deleted=0 order by b.row_sequence_no asc";
	$data_array_dtls = sql_select($sqlDtls);

	$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$data_array_dtls[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");

	$tot_smv = 0;
	$tot_mp = 0;
	$helperSmv = 0;
	$machineSmv = 0;
	$sQISmv = 0;
	$fIMSmv = 0;
	$fQISmv = 0;
	$polyHelperSmv = 0;
	$pkSmv = 0;
	$htSmv = 0;
	$helperMp = 0;
	$machineMp = 0;
	$sQiMp = 0;
	$fImMp = 0;
	$fQiMp = 0;
	$polyHelperMp = 0;
	$pkMp = 0;
	$htMp = 0;
	$mpSumm = array();

	foreach ($data_array_dtls as $slectResult) {
		if ($balanceDataArray[$slectResult[csf('id')]]['smv'] > 0) {
			$smv = $balanceDataArray[$slectResult[csf('id')]]['smv'];
		} else {
			$smv = $slectResult[csf('total_smv')];
		}

		$rescId = $slectResult[csf('resource_gsd')];
		$layOut = $balanceDataArray[$slectResult[csf('id')]]['layout_mp'];

		if ($rescId == 40 || $rescId == 41 || $rescId == 43 || $rescId == 44 || $rescId == 48 || $rescId == 68 || $rescId == 70 || $rescId == 147) {
			$helperSmv = $helperSmv + $smv;
			$helperMp = $helperMp + $layOut;
		} else if ($rescId == 53) {
			$fIMSmv = $fIMSmv + $smv;
			$fImMp = $fImMp + $layOut;
		} else if ($rescId == 54) {
			$fQISmv = $fQISmv + $smv;
			$fQiMp = $fQiMp + $layOut;
		} else if ($rescId == 55) {
			$polyHelperSmv = $polyHelperSmv + $smv;
			$polyHelperMp = $polyHelperMp + $layOut;
		} else if ($rescId == 56) {
			$pkSmv = $pkSmv + $smv;
			$pkMp = $pkMp + $layOut;
		} else if ($rescId == 90) {
			$htSmv = $htSmv + $smv;
			$htMp = $htMp + $layOut;
		} else if ($rescId == 176 || $rescId == 69) {
			$imSmv = $imSmv + $smv;
			$imMp = $imMp + $layOut;
		} else {
			$machineSmv = $machineSmv + $smv;
			$machineMp = $machineMp + $layOut;

			$mpSumm[$rescId] += $layOut;
		}
		$i++;
	}
	?>
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="32%" valign="top">
				<b>SMV Summary</b>
				<table border="1" rules="all" class="rpt_table" width="100%">
					<tr bgcolor="#FFFFFF">
						<td width="100">Sewing Helper</td>
						<td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Sewing Machine</td>
						<td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td>Sewing QI</td>
						<td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Finishing I/M</td>
						<td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td>Finishing QI</td>
						<td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Poly Helper</td>
						<td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td>Packing</td>
						<td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Hand Tag</td>
						<td id="ph" align="right" style="padding-right:5px"><? echo number_format($htSmv, 2, '.', ''); ?></td>
					</tr>

					<tr bgcolor="#E9F3FF">
						<td>Iron Man</td>
						<td id="im" align="right" style="padding-right:5px"><? echo number_format($imSmv, 2, '.', ''); ?></td>
					</tr>

					<tr bgcolor="#E9F3FF">
						<td align="right"><b>Total SMV</b></td>
						<td id="totSmvSumm" align="right" style="padding-right:5px"><? echo number_format($helperSmv + $machineSmv + $sQISmv + $fIMSmv + $fQISmv + $polyHelperSmv + $pkSmv + $htSmv + $imSmv, 2, '.', ''); ?></td>
					</tr>
				</table>
			</td>
			<td width="1%" valign="top"></td>
			<td width="32%" valign="top">
				<?
				$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;

				if (strpos($helperMp, ".") != "") {
					$helperMp = number_format($helperMp, 2, '.', '');
				}

				if (strpos($machineMp, ".") != "") {
					$machineMp = number_format($machineMp, 2, '.', '');
				}

				if (strpos($sQiMp, ".") != "") {
					$sQiMp = number_format($sQiMp, 2, '.', '');
				}

				if (strpos($totatMp, ".") != "") {
					$fImMp = number_format($fImMp, 2, '.', '');
				}

				if (strpos($fQiMp, ".") != "") {
					$fQiMp = number_format($fQiMp, 2, '.', '');
				}

				if (strpos($polyHelperMp, ".") != "") {
					$polyHelperMp = number_format($polyHelperMp, 2, '.', '');
				}

				if (strpos($pkMp, ".") != "") {
					$pkMp = number_format($pkMp, 2, '.', '');
				}

				if (strpos($htMp, ".") != "") {
					$htMp = number_format($htMp, 2, '.', '');
				}
				if (strpos($imMp, ".") != "") {
					$imMp = number_format($imMp, 2, '.', '');
				}

				if (strpos($totMpSumm, ".") != "") {
					$totMpSumm = number_format($totMpSumm, 2, '.', '');
				}
				?>
				<b>Man Power Summary</b>
				<table border="1" rules="all" class="rpt_table" width="100%">
					<tr bgcolor="#FFFFFF">
						<td width="100">Sewing Helper</td>
						<td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Sewing Machine</td>
						<td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td>Sewing QI</td>
						<td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Finishing I/M</td>
						<td id="fimm" align="right" style="padding-right:5px"><? echo number_format($fImMp, 2, '.', ''); ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td>Finishing QI</td>
						<td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Poly Helper</td>
						<td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td>Packing</td>
						<td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Hand Tag</td>
						<td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
					</tr>
					<tr bgcolor="#E9F3FF">
						<td>Iron Man</td>
						<td id="imm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td align="right"><b>Total</b></td>
						<td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
					</tr>
				</table>
			</td>
			<td width="1%" valign="top"></td>
			<td valign="top">
				<b>Machine Summary</b>
				<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
					<?
					$x = 1;
					$totatMp = 0;
					foreach ($mpSumm as $key => $mp) {
						if ($x % 2 == 0) $bgcolor = '#E9F3FF';
						else $bgcolor = '#FFFFFF';

						if (strpos($mp, ".") != "") {
							$mp = number_format($mp, 2, '.', '');
						}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="170"><? echo $production_resource_arr[$key]; ?></td>
							<td align="right" style="padding-right:5px"><? echo $mp; ?></td>
						</tr>
					<?
						$totatMp += $mp;
						$x++;
					}
					if ($x % 2 == 0) $bgcolor = '#E9F3FF';
					else $bgcolor = '#FFFFFF';

					if (strpos($totatMp, ".") != "") {
						$totatMp = number_format($totatMp, 2, '.', '');
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="right"><b>Total</b></td>
						<td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$approved = 0;
	$sql = sql_select("select approved from ppl_gsd_entry_mst where id=$breakdown_id4");
	foreach ($sql as $row) {
		$approved = $row[csf('approved')];
	}
	if ($approved == 3) $approved = 1;
	else $approved = $approved;

	if ($approved == 1) {
		echo "approved**" . str_replace("'", "", $breakdown_id4);
		die;
	}

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$id = return_next_id("id", "ppl_balancing_mst_entry", 1);
		$field_array = "id,gsd_mst_id,balance_mst_id,line_shape,no_of_work_st,layout_date,balancing_page,inserted_by,insert_date,entry_form";
		$data_array = "(" . $id . "," . $breakdown_id4 . "," . $balanceId . "," . $cbo_line_shape . "," . $txt_no_of_work_st . "," . $txt_layout_date . ",3," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "', 1)";

		$field_array_dtls = "id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, smv, target_hundred_perc,work_station, entry_form";
		$dtls_id = return_next_id("id", "ppl_layout_dtls_entry", 1);

		
		for ($j = 1; $j <= $tot_row; $j++) {
			$stNo = "stNo" . $j;
			$seqNo = "seqNo" . $j;
			$gsdDtlsId = "gsdDtlsId" . $j;
			$sewingId = "sewingId" . $j;
			$rescId = "rescId" . $j;
			$smv = "smv" . $j;
			$tgtPerc = "tgtPerc" . $j;

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $breakdown_id4 . ",'" . $$gsdDtlsId . "'," . $id . ",'" . $$seqNo . "','" . $$sewingId . "','" . $$rescId . "','" . $$smv . "','" . $$tgtPerc . "','" . $$stNo . "', 1)";
			$dtls_id = $dtls_id + 1;
		}

		//echo "10**insert into ppl_balancing_dtls_entry (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID = sql_insert("ppl_balancing_mst_entry", $field_array, $data_array, 0);
		$rID2 = sql_insert("ppl_layout_dtls_entry", $field_array_dtls, $data_array_dtls, 1);
		//echo "10**".$rID."&&".$rID2;die;

		if ($rID && $rID2) {
			oci_commit($con);
			echo "0**" . $id;
		} else {
			oci_rollback($con);
			echo "5**0**0";
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

		$field_array = "line_shape*no_of_work_st*layout_date*updated_by*update_date";
		$data_array = $cbo_line_shape . "*" . $txt_no_of_work_st . "*" . $txt_layout_date . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$field_array_dtls = "id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, smv, target_hundred_perc,work_station";
		$dtls_id = return_next_id("id", "ppl_layout_dtls_entry", 1);
		// echo $dtls_id;die;
		for ($j = 1; $j <= $tot_row; $j++) {
			$stNo = "stNo" . $j;
			$seqNo = "seqNo" . $j;
			$gsdDtlsId = "gsdDtlsId" . $j;
			$sewingId = "sewingId" . $j;
			$rescId = "rescId" . $j;
			$smv = "smv" . $j;
			$tgtPerc = "tgtPerc" . $j;

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $breakdown_id4 . ",'" . $$gsdDtlsId . "'," . $lo_update_id . ",'" . $$seqNo . "','" . $$sewingId . "','" . $$rescId . "','" . $$smv . "','" . $$tgtPerc . "','" . $$stNo . "')";
			$dtls_id = $dtls_id + 1;
		}
		

		// echo "SELECT * from ppl_layout_dtls_entry where mst_id=$lo_update_id";die;
		

		$rID = sql_update("ppl_balancing_mst_entry", $field_array, $data_array, "id", $lo_update_id, 0);
		$rID2 = execute_query("delete from ppl_layout_dtls_entry where mst_id=$lo_update_id", 0);
		$rID3 = sql_insert("ppl_layout_dtls_entry", $field_array_dtls, $data_array_dtls, 1);

		// echo "10**insert into ppl_layout_dtls_entry (".$field_array_dtls.") values ".$data_array_dtls;die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $lo_update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $lo_update_id);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $lo_update_id);
			} else {
				oci_rollback($con);
				echo "6**" . str_replace("'", '', $lo_update_id);
			}
		}
		disconnect($con);
		die;
	}
	elseif($operation == 2) // Delete here
	{
		$con = connect();
		// echo $breakdown_id4.'='.$lo_update_id.'='.$balanceId;die;
		$sql="select id, operation_id, seam_length, req_qty, gsd_dtls_id from ppl_thread_cons_dtls_entry where gsd_mst_id=$breakdown_id4 and status_active=1 and is_deleted=0 order by id";
		$data_array = sql_select($sql);
		if(count($data_array)){
			echo "exit**";die;
		}

 
		$breakdown_id =  str_replace("'", '', $breakdown_id4);
		// $rID = sql_update("ppl_balancing_mst_entry", $field_array, $data_array, "id", $update_id, 0);
		// $rID3 = sql_update("ppl_layout_dtls_entry", $field_array_dtls, $data_array_dtls, 1);
 
	    $user_id = $_SESSION['logic_erp']['user_id'];
        $mstSql = "UPDATE ppl_balancing_mst_entry SET UPDATED_BY = $user_id, UPDATE_DATE = '$pc_date_time', STATUS_ACTIVE = 0, IS_DELETED = 1 WHERE gsd_mst_id = $breakdown_id";
        $rID = execute_query($mstSql);


		$dtlsSql = "UPDATE ppl_layout_dtls_entry  SET DELETED_BY = $user_id, DELETE_DATE='$pc_date_time',  status_active = 0, IS_DELETED = 1 WHERE gsd_mst_id = $breakdown_id";
        $rID2 = execute_query($dtlsSql);
		if($rID && $rID2)
        {
            oci_commit($con);    
            echo "2**".$rID;
        }
        else
        {
            oci_rollback($con);
            echo "10**".$rID;
        }
        disconnect($con);
		die;

		// $field_array = "updated_by*update_date*status_active*is_deleted";
		// $data_array = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		// echo $update_id;die;
		// $rID = sql_update("ppl_balancing_mst_entry", $field_array, $data_array, "id", $update_id, 1);
		// echo  "100***".$rID;oci_commit($con); die;
	}
}

if ($action == "layout_print") { 

	$data = explode("**", $data);
	$lo_update_id = $data[0];
	$bl_update_id = $data[1];

	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");


	$mstDataArray = sql_select("select a.company_id,a.id,a.buyer_id, a.style_ref, a.custom_style, a.gmts_item_id, a.product_dept, a.remarks,a.fabric_type,a.bulletin_type,a.applicable_period, b.line_shape, b.no_of_work_st, b.layout_date, b.inserted_by, b.insert_date, b.updated_by, b.update_date,b.balance_mst_id from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='" . $lo_update_id . "' and b.balancing_page=3");
	$update_id = $mstDataArray[0][csf('id')];
	$NoOfWorker = $mstDataArray[0][csf('no_of_work_st')];
	$line_shape = $mstDataArray[0][csf('line_shape')];


	$balancingPageArray = sql_select("select b.efficiency,b.target from ppl_balancing_mst_entry b where b.id='" . $mstDataArray[0][csf('balance_mst_id')] . "' and b.balancing_page=1");


	$image_location_arr = return_library_array("select id,image_location from common_photo_library where master_tble_id='$update_id' and form_name='gsd_entry'", "id", "image_location");

	$workerTrackingArr = array();
	$layoutDataArr = array();
	$gsdDataArr = array();
	/*$blData=sql_select("select worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id");
	foreach($blData as $row)
	{
		if($row[csf('worker_tracking')]=="" || !in_array($row[csf('worker_tracking')],$workerTrackingArr))
		{
			$NoOfWorker++;
		}
		$workerTrackingArr[]=$row[csf('worker_tracking')];
	}*/

	$layoutData = sql_select("select gsd_dtls_id, work_station from ppl_layout_dtls_entry where mst_id=$lo_update_id  and is_deleted=0 order by id");
	foreach ($layoutData as $row) {
		$layoutDataArr[$row[csf('work_station')]] .= $row[csf('gsd_dtls_id')] . ",";
	}

	$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id", "operation_name");
	$sql = "SELECT a.PROCESS_ID,b.id, b.lib_sewing_id, b.row_sequence_no, b.resource_gsd, b.total_smv, b.target_on_full_perc,b.efficiency from ppl_gsd_entry_mst a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$update_id and b.is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$gsdDataArr[$row[csf('id')]]['lib_sewing_id'] = $row[csf('lib_sewing_id')];
		$gsdDataArr[$row[csf('id')]]['row_sequence_no'] = $row[csf('row_sequence_no')];
		$gsdDataArr[$row[csf('id')]]['resource_gsd'] = $row[csf('resource_gsd')];
		$gsdDataArr[$row[csf('id')]]['total_smv'] = $row[csf('total_smv')];
		$gsdDataArr[$row[csf('id')]]['target_on_full_perc'] = $row[csf('target_on_full_perc')];
		$gsdDataArr[$row[csf('id')]]['efficiency'] = $row[csf('efficiency')];
	}

	$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$result[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");

	?>

	<div style="width:900px">
		<table width="100%">
			<tr>
				<td align="center" colspan="9"><strong><u>Layout</u></strong></td>
			</tr>
			<tr>
				<td width="80"><strong>Style Ref.</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="190"><? echo $mstDataArray[0][csf('style_ref')];
								if ($mstDataArray[0][csf('custom_style')] != "") echo " (" . $mstDataArray[0][csf('custom_style')] . ")"; ?></td>
				<td width="130"><strong>Buyer Name</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="130"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
				<td width="130"><strong>Garments Item</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Line Shape</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="padding-right:5px"><? echo $line_shape_arr[$mstDataArray[0][csf('line_shape')]]; ?></td>
				<td><strong>No Of Work Sta.</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="padding-right:5px"><? echo $NoOfWorker; ?></td>
				<td><strong>Layout Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo change_date_format($mstDataArray[0][csf('layout_date')]); ?></td>
			</tr>

			<tr>
				<td><strong>Efficiency</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $balancingPageArray[0][csf('efficiency')]; ?></td>
				<td><strong>Target</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $balancingPageArray[0][csf('target')]; ?></td>
				<td><strong>Prod. Dept</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $product_dept[$mstDataArray[0][csf('product_dept')]]; ?></td>
			</tr>

			<tr>
				<td><strong>Cust. Style</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $mstDataArray[0][csf('custom_style')]; ?></td>

				<td><strong>Fabric Type</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>

				<td><strong>Bulletin Type</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>



			</tr>


			<tr>
				<td><strong>Insert By</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $user_arr[$mstDataArray[0][csf('inserted_by')]]; ?></td>
				<td><strong>Modify By</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $user_arr[$mstDataArray[0][csf('updated_by')]]; ?></td>
				<td><strong>Applicable Period</strong></td>
				<td><strong>:</strong></td>
				<td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>


			</tr>
			<tr>
				<td><strong>Insert Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('insert_date')])); ?></td>
				<td><strong>Modify Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td colspan="4"><? if ($mstDataArray[0][csf('update_date')] != "" && $mstDataArray[0][csf('update_date')] != "0000-00-00") echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('update_date')])); ?></td>
			</tr>


			<tr>
				<td><strong>Remarks</strong></td>
				<td><strong>:</strong></td>
				<td colspan="7"><? echo $mstDataArray[0][csf('remarks')]; ?></td>
			</tr>





		</table>
		<br />
		<?

		if ($line_shape == 2) {
			$i = 1;
			$firstHalf = round($NoOfWorker / 2);
			$secondHalf = round($NoOfWorker - $firstHalf);

			echo '<table width="900"><tr><td valign="top">';
			echo '<table width="100%" align="right" cellspacing="0" border="1" rules="all">';
			echo '<thead bgcolor="#dddddd" align="center"><th width="45">Work S.-112</th>
					<th width="80">Resource</th>
					<th width="50">Seq. No.</th>
					<th>Operation</th>
					<th width="50">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
			echo '</thead><tbody>';
			for ($z = $NoOfWorker; $z >= 1; $z--) {
				if ($z % 2 == 1) {
					$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));

					if (count($work_stations) > 0) {
						$row_span = count($work_stations);
						$j = 0;
						foreach ($work_stations as $dtlsId) {
			?>
							<tr>
								<?
								if ($j == 0) {
								?>
									<td id="left_<?= $l+=1;?>" rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
								<?
								}
								?>
								<td align="center"><? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
								<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
								<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
								<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
							</tr>
						<?
							$j++;
							$i++;
						}
					} else {
						?>
						<!--<tr>
								<td><? echo $z; ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>-->
						<?
						$i++;
					}
				}
			}

			$height = ($i - 1) * 23 + 43;
			echo '</tbody></table></td><td valign="top" width="25"><table width="100%" border="1" rules="all"><tr height="' . $height . '"><td id="TableTd">Table</td></tr></table></td>';
			echo '<td valign="top"><table width="100%" align="right" cellspacing="0" border="1" rules="all">';
			echo '<thead bgcolor="#dddddd" align="center"></th><th width="45">Work S.-13</th>
					<th width="80">Resource</th>
					<th width="50">Seq. No.</th>
					<th>Operation</th>
					<th width="50">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
			echo '</thead><tbody>';
			//for($z=$firstHalf+1;$z<=$NoOfWorker;$z++)
			for ($z = $NoOfWorker; $z >= 1; $z--) {
				if ($z % 2 == 0) {
					$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
					if (count($work_stations) > 0) {
						$row_span = count($work_stations);
						$j = 0;
						foreach ($work_stations as $dtlsId) {
						?>
							<tr>
								<?
								if ($j == 0) {
								?>
									<td id="right_<?= $r+=1;?>" rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
								<?
								}
								?>
								<td align="center"><? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
								<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
								<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
								<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
							</tr>
						<?
							$j++;
						}
					} else {
						?>
						<!--<tr>
								<td><? echo $z; ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>-->
					<?
					}
				}
			}
			echo '</tbody></table></td></tr></table>';
		} else {
			echo '<table width="900" cellspacing="0" border="1" rules="all">';
			echo '<thead bgcolor="#dddddd" align="center"></th><th width="70">Work S.-11</th>
					<th width="110">Resource</th>
					<th width="70">Seq. No.</th>
					<th>Operation</th>
					<th width="75">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
			echo '</thead><tbody>';

			for ($z = $NoOfWorker; $z >= 1; $z--) {
				$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
				if (count($work_stations) > 0) {
					$row_span = count($work_stations);
					$j = 0;
					foreach ($work_stations as $dtlsId) {
					?>
						<tr>
							<?
							if ($j == 0) {
							?>
								<td rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
							<?
							}
							?>
							<td align="center"><? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?></td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
							<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
							<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
							<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
						</tr>
					<?
						$j++;
					}
				} else {
					?>
					<!--<tr>
						<td><? echo $z; ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>-->
		<?
				}
			}
			echo '</tbody></table>';
		}

 
	

		$balanceDataArray = array();
		$blData = sql_select("select a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 and a.gsd_mst_id=$update_id  and b.is_deleted=0");
		foreach ($blData as $row) {
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv'] = $row[csf('smv')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp'] = $row[csf('layout_mp')];
		}

		$sqlDtls = "SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc from ppl_gsd_entry_dtls where mst_id='" . $update_id . "' and is_deleted=0 order by row_sequence_no asc";
		$data_array_dtls = sql_select($sqlDtls);

		$tot_smv = 0;
		$tot_mp = 0;
		$helperSmv = 0;
		$machineSmv = 0;
		$sQISmv = 0;
		$fIMSmv = 0;
		$fQISmv = 0;
		$polyHelperSmv = 0;
		$pkSmv = 0;
		$htSmv = 0;
		$helperMp = 0;
		$machineMp = 0;
		$sQiMp = 0;
		$fImMp = 0;
		$fQiMp = 0;
		$polyHelperMp = 0;
		$pkMp = 0;
		$htMp = 0;
		$mpSumm = array();

		foreach ($data_array_dtls as $slectResult) {
			if ($balanceDataArray[$slectResult[csf('id')]]['smv'] > 0) {
				$smv = $balanceDataArray[$slectResult[csf('id')]]['smv'];
			} else {
				$smv = $slectResult[csf('total_smv')];
			}

			$rescId = $slectResult[csf('resource_gsd')];
			$layOut = $balanceDataArray[$slectResult[csf('id')]]['layout_mp'];

			if ($rescId == 40 || $rescId == 41 || $rescId == 43 || $rescId == 44 || $rescId == 48 || $rescId == 68 || $rescId == 69 || $rescId == 70 || $rescId == 147 ) {
				$helperSmv = $helperSmv + $smv;
				$helperMp = $helperMp + $layOut;
			} else if ($rescId == 53) {
				$fIMSmv = $fIMSmv + $smv;
				$fImMp = $fImMp + $layOut;
			} else if ($rescId == 54) {
				$fQISmv = $fQISmv + $smv;
				$fQiMp = $fQiMp + $layOut;
			} else if ($rescId == 55) {
				$polyHelperSmv = $polyHelperSmv + $smv;
				$polyHelperMp = $polyHelperMp + $layOut;
			} else if ($rescId == 56) {
				$pkSmv = $pkSmv + $smv;
				$pkMp = $pkMp + $layOut;
			} else if ($rescId == 90) {
				$htSmv = $htSmv + $smv;
				$htMp = $htMp + $layOut;
			} else if ($rescId == 176) {
				$imSmv = $imSmv + $smv;
				$imMp = $imMp + $layOut;
			} else {
				$machineSmv = $machineSmv + $smv;
				$machineMp = $machineMp + $layOut;

				$mpSumm[$rescId] += $layOut;
			}
			$i++;
		}
		?>
		<br />
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="260" valign="top">
					<b>SMV Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%">
						<tr bgcolor="#FFFFFF">
							<td width="120">Sewing Helper</td>
							<td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Sewing Machine</td>
							<td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Sewing QI</td>
							<td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Finishing I/M</td>
							<td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Finishing QI</td>
							<td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Poly Helper</td>
							<td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Packing</td>
							<td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Hand Tag</td>
							<td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Iron Man</td>
							<td id="ht" align="right" style="padding-right:5px"><? echo number_format($imSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo number_format($helperSmv + $machineSmv + $sQISmv + $fIMSmv + $fQISmv + $polyHelperSmv + $pkSmv + $htSmv + $imSmv, 2, '.', ''); ?></td>
						</tr>
					</table>
				</td>
				<td width="20" valign="top"></td>
				<td width="260" valign="top">
					<?
					$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;

					if (strpos($helperMp, ".") != "") {
						$helperMp = number_format($helperMp, 2, '.', '');
					}

					if (strpos($machineMp, ".") != "") {
						$machineMp = number_format($machineMp, 2, '.', '');
					}

					if (strpos($sQiMp, ".") != "") {
						$sQiMp = number_format($sQiMp, 2, '.', '');
					}

					if (strpos($totatMp, ".") != "") {
						$fImMp = number_format($fImMp, 2, '.', '');
					}

					if (strpos($fQiMp, ".") != "") {
						$fQiMp = number_format($fQiMp, 2, '.', '');
					}

					if (strpos($polyHelperMp, ".") != "") {
						$polyHelperMp = number_format($polyHelperMp, 2, '.', '');
					}

					if (strpos($pkMp, ".") != "") {
						$pkMp = number_format($pkMp, 2, '.', '');
					}

					if (strpos($htMp, ".") != "") {
						$htMp = number_format($htMp, 2, '.', '');
					}
					if (strpos($imMp, ".") != "") {
						$imMp = number_format($imMp, 2, '.', '');
					}

					if (strpos($totMpSumm, ".") != "") {
						$totMpSumm = number_format($totMpSumm, 2, '.', '');
					}
					?>
					<b>Man Power Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%">
						<tr bgcolor="#FFFFFF">
							<td width="120">Sewing Helper</td>
							<td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Sewing Machine</td>
							<td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Sewing QI</td>
							<td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Finishing I/M</td>
							<td id="fimm" align="right" style="padding-right:5px"><? echo $fImMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Finishing QI</td>
							<td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Poly Helper</td>
							<td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Packing</td>
							<td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Hand Tag</td>
							<td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Iron Man</td>
							<td id="htm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
						</tr>
					</table>
				</td>
				<td width="20" valign="top"></td>
				<td valign="top">
					<b>Machine Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
						<?
						$x = 1;
						$totatMp = 0;
						foreach ($mpSumm as $key => $mp) {
							if ($x % 2 == 0) $bgcolor = '#E9F3FF';
							else $bgcolor = '#FFFFFF';

							if (strpos($mp, ".") != "") {
								$mp = number_format($mp, 2, '.', '');
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="170"><? echo $production_resource_arr[$key]; ?></td>
								<td align="right" style="padding-right:5px"><? echo $mp; ?></td>
							</tr>
						<?
							$totatMp += $mp;
							$x++;
						}

						if ($x % 2 == 0) $bgcolor = '#E9F3FF';
						else $bgcolor = '#FFFFFF';

						if (strpos($totatMp, ".") != "") {
							$totatMp = number_format($totatMp, 2, '.', '');
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?
		foreach ($image_location_arr as $image_path) {
			echo '<img src="../../' . $image_path . '" height="100" style="margin:3px 3px 3px 0;" />';
		}
		?>

		<br>
		<!--<table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:830px;text-align:center;" rules="all">	
		<tr style="alignment-baseline:baseline;">
        	<td height="130" width="33%" style="text-decoration:overline; border:none"><strong>IE Department</strong></td>
            <td width="33%" style="text-decoration:overline; border:none"><strong>Rcvd[Maintenance]</strong></td>
            <td width="33%" style="text-decoration:overline; border:none"><strong>AGM</strong></td>
            <td width="33%" style="text-decoration:overline; border:none"><strong>DGM</strong></td>
        </tr>
    </table>-->
		<?
		//There is no company in layout entry for this reason max company selected;
		echo signature_table(110, "(select max(company_id) as company_id from variable_settings_signature where report_id=110)", "900px");
		?>



	</div>



<?

?>
 
<script> 
	var tot_row = <?= $secondHalf;?>;
	var totalHeight = 0;
	for(i=1;i<=tot_row;i++){
		var leftHeight = document.getElementById("left_"+i).offsetHeight;
		var rightHeight = document.getElementById("right_"+i).offsetHeight;
		let maxHeight =  (leftHeight > rightHeight)?leftHeight:rightHeight;
		document.getElementById('left_'+i).height = maxHeight;
		document.getElementById('right_'+i).height = maxHeight;
		totalHeight += maxHeight;
	}
	document.getElementById('TableTd').height = totalHeight + 60;
</script>
<?

	exit();
}

if ($action == "excel_print") {
	extract($_REQUEST);

	//$data = explode("**", $data);
	//$lo_update_id = $data[0];
	//$bl_update_id = $data[1];
	$lo_update_id = str_replace("'", "", $lo_update_id);
	$bl_update_id = str_replace("'", "", $bl_update_id);

	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");


	$mstDataArray = sql_select("select a.company_id,a.id,a.buyer_id, a.style_ref, a.custom_style, a.gmts_item_id, a.product_dept, a.remarks,a.fabric_type,a.bulletin_type,a.applicable_period, b.line_shape, b.no_of_work_st, b.layout_date, b.inserted_by, b.insert_date, b.updated_by, b.update_date,b.balance_mst_id from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='" . $lo_update_id . "' and b.balancing_page=3");
	$update_id = $mstDataArray[0][csf('id')];
	$NoOfWorker = $mstDataArray[0][csf('no_of_work_st')];
	$line_shape = $mstDataArray[0][csf('line_shape')];


	$balancingPageArray = sql_select("select b.efficiency,b.target from ppl_balancing_mst_entry b where b.id='" . $mstDataArray[0][csf('balance_mst_id')] . "' and b.balancing_page=1");


	$image_location_arr = return_library_array("select id,image_location from common_photo_library where master_tble_id='$update_id' and form_name='gsd_entry'", "id", "image_location");

	$workerTrackingArr = array();
	$layoutDataArr = array();
	$gsdDataArr = array();
	/*$blData=sql_select("select worker_tracking from ppl_balancing_dtls_entry where mst_id=$bl_update_id");
	foreach($blData as $row)
	{
		if($row[csf('worker_tracking')]=="" || !in_array($row[csf('worker_tracking')],$workerTrackingArr))
		{
			$NoOfWorker++;
		}
		$workerTrackingArr[]=$row[csf('worker_tracking')];
	}*/

	$layoutData = sql_select("select gsd_dtls_id, work_station from ppl_layout_dtls_entry where mst_id=$lo_update_id  and is_deleted=0 order by id");
	foreach ($layoutData as $row) {
		$layoutDataArr[$row[csf('work_station')]] .= $row[csf('gsd_dtls_id')] . ",";
	}

	$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id", "operation_name");
	$sql = "SELECT a.PROCESS_ID,b.id, b.lib_sewing_id, b.row_sequence_no, b.resource_gsd, b.total_smv, b.target_on_full_perc,b.efficiency from ppl_gsd_entry_mst a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$update_id and b.is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$gsdDataArr[$row[csf('id')]]['lib_sewing_id'] = $row[csf('lib_sewing_id')];
		$gsdDataArr[$row[csf('id')]]['row_sequence_no'] = $row[csf('row_sequence_no')];
		$gsdDataArr[$row[csf('id')]]['resource_gsd'] = $row[csf('resource_gsd')];
		$gsdDataArr[$row[csf('id')]]['total_smv'] = $row[csf('total_smv')];
		$gsdDataArr[$row[csf('id')]]['target_on_full_perc'] = $row[csf('target_on_full_perc')];
		$gsdDataArr[$row[csf('id')]]['efficiency'] = $row[csf('efficiency')];
	}

	$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$result[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");
	ob_start();
?>

	<style>
		@media print {
			footer {
				position: fixed;
				bottom: 0;
				margin-top: 100px;
			}

			body {
				position: absulate;
				/* height:500px; */
				top: 0px;
				bottom: 100px;
			}

		}
	</style>

	<body>
		<div style="width:900px">
			<table width="100%">
				<tr>
					<td align="center" colspan="9"><strong><u>Layout</u></strong></td>
				</tr>
				<tr>
					<td width="80"><strong>Style Ref.</strong></td>
					<td width="10"><strong>:</strong></td>
					<td width="190"><? echo $mstDataArray[0][csf('style_ref')];
									if ($mstDataArray[0][csf('custom_style')] != "") echo " (" . $mstDataArray[0][csf('custom_style')] . ")"; ?></td>
					<td width="130"><strong>Buyer Name</strong></td>
					<td width="10"><strong>:</strong></td>
					<td width="130"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
					<td width="130"><strong>Garments Item</strong></td>
					<td width="10"><strong>:</strong></td>
					<td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Line Shape</strong></td>
					<td width="10"><strong>:</strong></td>
					<td style="padding-right:5px"><? echo $line_shape_arr[$mstDataArray[0][csf('line_shape')]]; ?></td>
					<td><strong>No Of Work Sta.</strong></td>
					<td width="10"><strong>:</strong></td>
					<td style="padding-right:5px"><? echo $NoOfWorker; ?></td>
					<td><strong>Layout Date</strong></td>
					<td width="10"><strong>:</strong></td>
					<td><? echo change_date_format($mstDataArray[0][csf('layout_date')]); ?></td>
				</tr>

				<tr>
					<td><strong>Efficiency</strong></td>
					<td><strong>:</strong></td>
					<td><? echo $balancingPageArray[0][csf('efficiency')]; ?></td>
					<td><strong>Target</strong></td>
					<td><strong>:</strong></td>
					<td><? echo $balancingPageArray[0][csf('target')]; ?></td>
					<td><strong>Prod. Dept</strong></td>
					<td><strong>:</strong></td>
					<td><? echo $product_dept[$mstDataArray[0][csf('product_dept')]]; ?></td>
				</tr>

				<tr>
					<td><strong>Cust. Style</strong></td>
					<td><strong>:</strong></td>
					<td><? echo $mstDataArray[0][csf('custom_style')]; ?></td>

					<td><strong>Fabric Type</strong></td>
					<td><strong>:</strong></td>
					<td><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>

					<td><strong>Bulletin Type</strong></td>
					<td><strong>:</strong></td>
					<td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>



				</tr>


				<tr>
					<td><strong>Insert By</strong></td>
					<td width="10"><strong>:</strong></td>
					<td><? echo $user_arr[$mstDataArray[0][csf('inserted_by')]]; ?></td>
					<td><strong>Modify By</strong></td>
					<td width="10"><strong>:</strong></td>
					<td><? echo $user_arr[$mstDataArray[0][csf('updated_by')]]; ?></td>
					<td><strong>Applicable Period</strong></td>
					<td><strong>:</strong></td>
					<td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>


				</tr>
				<tr>
					<td><strong>Insert Date</strong></td>
					<td width="10"><strong>:</strong></td>
					<td><? echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('insert_date')])); ?></td>
					<td><strong>Modify Date</strong></td>
					<td width="10"><strong>:</strong></td>
					<td colspan="4"><? if ($mstDataArray[0][csf('update_date')] != "" && $mstDataArray[0][csf('update_date')] != "0000-00-00") echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('update_date')])); ?></td>
				</tr>


				<tr>
					<td><strong>Remarks</strong></td>
					<td><strong>:</strong></td>
					<td colspan="7"><? echo $mstDataArray[0][csf('remarks')]; ?></td>
				</tr>





			</table>
			<br />
			<?

			if ($line_shape == 2) {
				$i = 1;
				$firstHalf = round($NoOfWorker / 2);
				$secondHalf = round($NoOfWorker - $firstHalf);

				echo '<table width="900"><tr><td valign="top">';
				echo '<table width="100%" align="right" cellspacing="0" border="1" rules="all">';
				echo '<thead bgcolor="#dddddd" align="center"><th width="45">Work S.</th>
					<th width="80">Resource</th>
					<th width="50">Seq. No.</th>
					<th>Operation</th>
					<th width="50">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
				echo '</thead><tbody>';
				for ($z = $NoOfWorker; $z >= 1; $z--) {
					if ($z % 2 == 1) {
						$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
						if (count($work_stations) > 0) {
							$row_span = count($work_stations);
							$j = 0;
							foreach ($work_stations as $dtlsId) {
			?>
								<tr>
									<?
									if ($j == 0) {
									?>
										<td rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
									<?
									}
									?>
									<td align="center"><? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?></td>
									<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
									<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
									<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
									<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
									<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
									<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
								</tr>
							<?
								$j++;
								$i++;
							}
						} else {
							?>
							<!--<tr>
								<td><? echo $z; ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>-->
							<?
							$i++;
						}
					}
				}

				$height = ($i - 1) * 23 + 43;
				echo '</tbody></table></td><td valign="top" width="25"><table width="100%" border="1" rules="all"><tr height="' . $height . '"><td>Table</td></tr></table></td>';
				echo '<td valign="top"><table width="100%" align="right" cellspacing="0" border="1" rules="all">';
				echo '<thead bgcolor="#dddddd" align="center"></th><th width="45">Work S.</th>
					<th width="80">Resource</th>
					<th width="50">Seq. No.</th>
					<th>Operation</th>
					<th width="50">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
				echo '</thead><tbody>';
				//for($z=$firstHalf+1;$z<=$NoOfWorker;$z++)
				for ($z = $NoOfWorker; $z >= 1; $z--) {
					if ($z % 2 == 0) {
						$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
						if (count($work_stations) > 0) {
							$row_span = count($work_stations);
							$j = 0;
							foreach ($work_stations as $dtlsId) {
							?>
								<tr>
									<?
									if ($j == 0) {
									?>
										<td rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
									<?
									}
									?>
									<td align="center"><? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?></td>
									<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
									<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
									<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
									<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
									<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
									<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
								</tr>
							<?
								$j++;
							}
						} else {
							?>
							<!--<tr>
								<td><? echo $z; ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>-->
						<?
						}
					}
				}
				echo '</tbody></table></td></tr></table>';
			} else {
				echo '<table width="900" cellspacing="0" border="1" rules="all">';
				echo '<thead bgcolor="#dddddd" align="center"></th><th width="70">Work S.</th>
					<th width="110">Resource</th>
					<th width="70">Seq. No.</th>
					<th>Operation</th>
					<th width="75">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
				echo '</thead><tbody>';

				for ($z = $NoOfWorker; $z >= 1; $z--) {
					$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
					if (count($work_stations) > 0) {
						$row_span = count($work_stations);
						$j = 0;
						foreach ($work_stations as $dtlsId) {
						?>
							<tr>
								<?
								if ($j == 0) {
								?>
									<td rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
								<?
								}
								?>
								<td align="center"><? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
								<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
								<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
								<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
								<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
							</tr>
						<?
							$j++;
						}
					} else {
						?>
						<!--<tr>
						<td><? echo $z; ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>-->
			<?
					}
				}
				echo '</tbody></table>';
			}



			$balanceDataArray = array();
			$blData = sql_select("select a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 and a.gsd_mst_id=$update_id  and b.is_deleted=0");
			foreach ($blData as $row) {
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv'] = $row[csf('smv')];
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp'] = $row[csf('layout_mp')];
			}

			$sqlDtls = "SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc from ppl_gsd_entry_dtls where mst_id='" . $update_id . "' and is_deleted=0 order by row_sequence_no asc";
			$data_array_dtls = sql_select($sqlDtls);

			$tot_smv = 0;
			$tot_mp = 0;
			$helperSmv = 0;
			$machineSmv = 0;
			$sQISmv = 0;
			$fIMSmv = 0;
			$fQISmv = 0;
			$polyHelperSmv = 0;
			$pkSmv = 0;
			$htSmv = 0;
			$helperMp = 0;
			$machineMp = 0;
			$sQiMp = 0;
			$fImMp = 0;
			$fQiMp = 0;
			$polyHelperMp = 0;
			$pkMp = 0;
			$htMp = 0;
			$mpSumm = array();

			foreach ($data_array_dtls as $slectResult) {
				if ($balanceDataArray[$slectResult[csf('id')]]['smv'] > 0) {
					$smv = $balanceDataArray[$slectResult[csf('id')]]['smv'];
				} else {
					$smv = $slectResult[csf('total_smv')];
				}

				$rescId = $slectResult[csf('resource_gsd')];
				$layOut = $balanceDataArray[$slectResult[csf('id')]]['layout_mp'];

				if ($rescId == 40 || $rescId == 41 || $rescId == 43 || $rescId == 44 || $rescId == 48 || $rescId == 68 || $rescId == 69 || $rescId == 70 || $rescId == 147 ) {
					$helperSmv = $helperSmv + $smv;
					$helperMp = $helperMp + $layOut;
				} else if ($rescId == 53) {
					$fIMSmv = $fIMSmv + $smv;
					$fImMp = $fImMp + $layOut;
				} else if ($rescId == 54) {
					$fQISmv = $fQISmv + $smv;
					$fQiMp = $fQiMp + $layOut;
				} else if ($rescId == 55) {
					$polyHelperSmv = $polyHelperSmv + $smv;
					$polyHelperMp = $polyHelperMp + $layOut;
				} else if ($rescId == 56) {
					$pkSmv = $pkSmv + $smv;
					$pkMp = $pkMp + $layOut;
				} else if ($rescId == 90) {
					$htSmv = $htSmv + $smv;
					$htMp = $htMp + $layOut;
				} else if ($rescId == 176) {
					$imSmv = $imSmv + $smv;
					$imMp = $imMp + $layOut;
				} else {
					$machineSmv = $machineSmv + $smv;
					$machineMp = $machineMp + $layOut;

					$mpSumm[$rescId] += $layOut;
				}
				$i++;
			}
			?>
			<br />
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="260" valign="top">
						<b>SMV Summary</b>
						<table border="1" rules="all" class="rpt_table" width="100%">
							<tr bgcolor="#FFFFFF">
								<td width="120">Sewing Helper</td>
								<td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Sewing Machine</td>
								<td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td>Sewing QI</td>
								<td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Finishing I/M</td>
								<td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td>Finishing QI</td>
								<td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Poly Helper</td>
								<td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td>Packing</td>
								<td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Hand Tag</td>
								<td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Iron Man</td>
								<td id="ht" align="right" style="padding-right:5px"><? echo number_format($imSmv, 2, '.', ''); ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td align="right"><b>Total</b></td>
								<td align="right" style="padding-right:5px"><? echo number_format($helperSmv + $machineSmv + $sQISmv + $fIMSmv + $fQISmv + $polyHelperSmv + $pkSmv + $htSmv + $imSmv, 2, '.', ''); ?></td>
							</tr>
						</table>
					</td>
					<td width="20" valign="top"></td>
					<td width="260" valign="top">
						<?
						$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;

						if (strpos($helperMp, ".") != "") {
							$helperMp = number_format($helperMp, 2, '.', '');
						}

						if (strpos($machineMp, ".") != "") {
							$machineMp = number_format($machineMp, 2, '.', '');
						}

						if (strpos($sQiMp, ".") != "") {
							$sQiMp = number_format($sQiMp, 2, '.', '');
						}

						if (strpos($totatMp, ".") != "") {
							$fImMp = number_format($fImMp, 2, '.', '');
						}

						if (strpos($fQiMp, ".") != "") {
							$fQiMp = number_format($fQiMp, 2, '.', '');
						}

						if (strpos($polyHelperMp, ".") != "") {
							$polyHelperMp = number_format($polyHelperMp, 2, '.', '');
						}

						if (strpos($pkMp, ".") != "") {
							$pkMp = number_format($pkMp, 2, '.', '');
						}

						if (strpos($htMp, ".") != "") {
							$htMp = number_format($htMp, 2, '.', '');
						}
						if (strpos($imMp, ".") != "") {
							$imMp = number_format($imMp, 2, '.', '');
						}

						if (strpos($totMpSumm, ".") != "") {
							$totMpSumm = number_format($totMpSumm, 2, '.', '');
						}
						?>
						<b>Man Power Summary</b>
						<table border="1" rules="all" class="rpt_table" width="100%">
							<tr bgcolor="#FFFFFF">
								<td width="120">Sewing Helper</td>
								<td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Sewing Machine</td>
								<td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td>Sewing QI</td>
								<td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Finishing I/M</td>
								<td id="fimm" align="right" style="padding-right:5px"><? echo $fImMp; ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td>Finishing QI</td>
								<td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Poly Helper</td>
								<td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td>Packing</td>
								<td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Hand Tag</td>
								<td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
								<td>Iron Man</td>
								<td id="htm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td align="right"><b>Total</b></td>
								<td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
							</tr>
						</table>
					</td>
					<td width="20" valign="top"></td>
					<td valign="top">
						<b>Machine Summary</b>
						<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
							<?
							$x = 1;
							$totatMp = 0;
							foreach ($mpSumm as $key => $mp) {
								if ($x % 2 == 0) $bgcolor = '#E9F3FF';
								else $bgcolor = '#FFFFFF';

								if (strpos($mp, ".") != "") {
									$mp = number_format($mp, 2, '.', '');
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="170"><? echo $production_resource_arr[$key]; ?></td>
									<td align="right" style="padding-right:5px"><? echo $mp; ?></td>
								</tr>
							<?
								$totatMp += $mp;
								$x++;
							}

							if ($x % 2 == 0) $bgcolor = '#E9F3FF';
							else $bgcolor = '#FFFFFF';

							if (strpos($totatMp, ".") != "") {
								$totatMp = number_format($totatMp, 2, '.', '');
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="right"><b>Total</b></td>
								<td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<?
			foreach ($image_location_arr as $image_path) {
				echo '<img src="../../' . $image_path . '" height="100" style="margin:3px 3px 3px 0;" />';
			}
			?>

			<br>
	</body>




	</div>
	<?
	echo signature_table(110, "(select max(company_id) as company_id from variable_settings_signature where report_id=110)", "900px");
	?>
	<?
	$reportBody = ob_get_contents();
	ob_end_clean();

	$user_id = $_SESSION['logic_erp']['user_id'];
	$report_cat = 100;

	foreach (glob("lp*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = "lb" . $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $reportBody);
	echo "$reportBody****$filename****$report_cat";

	exit();
}


if ($action == "layout_print2") {
	$data = explode("**", $data);
	$lo_update_id = $data[0];
	$bl_update_id = $data[1];

	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");


	$mstDataArray = sql_select("select a.PROCESS_ID,a.company_id,a.id,a.buyer_id, a.style_ref, a.custom_style, a.gmts_item_id, a.product_dept, a.remarks,a.fabric_type,a.bulletin_type,a.applicable_period,a.internal_ref, b.line_shape, b.no_of_work_st, b.layout_date, b.inserted_by, b.insert_date, b.updated_by, b.update_date,b.balance_mst_id from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='" . $lo_update_id . "' and b.balancing_page=3");
	$update_id = $mstDataArray[0][csf('id')];
	$NoOfWorker = $mstDataArray[0][csf('no_of_work_st')];
	$line_shape = $mstDataArray[0][csf('line_shape')];

	$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and process_id={$mstDataArray[0]['PROCESS_ID']}  order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");


	$balancingPageArray = sql_select("select b.efficiency,b.target from ppl_balancing_mst_entry b where b.id='" . $mstDataArray[0][csf('balance_mst_id')] . "' and b.balancing_page=1");
	$image_location_arr = return_library_array("select id,image_location from common_photo_library where master_tble_id='$update_id' and form_name='gsd_entry'", "id", "image_location");

	$workerTrackingArr = array();
	$layoutDataArr = array();
	$gsdDataArr = array();

	$layoutData = sql_select("select gsd_dtls_id, work_station from ppl_layout_dtls_entry where mst_id=$lo_update_id  and is_deleted=0 order by id");
	foreach ($layoutData as $row) {
		$layoutDataArr[$row[csf('work_station')]] .= $row[csf('gsd_dtls_id')] . ",";
	}

	$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id", "operation_name");
	$sql = "SELECT id, lib_sewing_id, row_sequence_no, resource_gsd, total_smv, target_on_full_perc,efficiency from ppl_gsd_entry_dtls where mst_id=$update_id and is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$gsdDataArr[$row[csf('id')]]['lib_sewing_id'] = $row[csf('lib_sewing_id')];
		$gsdDataArr[$row[csf('id')]]['row_sequence_no'] = $row[csf('row_sequence_no')];
		$gsdDataArr[$row[csf('id')]]['resource_gsd'] = $row[csf('resource_gsd')];
		$gsdDataArr[$row[csf('id')]]['total_smv'] = $row[csf('total_smv')];
		$gsdDataArr[$row[csf('id')]]['target_on_full_perc'] = $row[csf('target_on_full_perc')];
		$gsdDataArr[$row[csf('id')]]['efficiency'] = $row[csf('efficiency')];
	}

	?>

	<div style="width:850px">
		<table width="100%">
			<tr>
				<td align="center" colspan="9"><strong><u>Layout</u></strong></td>
			</tr>
			<tr>
				<td width="80"><strong>Style Ref.</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="190"><? echo $mstDataArray[0][csf('style_ref')];
								if ($mstDataArray[0][csf('custom_style')] != "") echo " (" . $mstDataArray[0][csf('custom_style')] . ")"; ?></td>
				<td width="130"><strong>Buyer Name</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="130"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
				<td width="130"><strong>Garments Item</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Line Shape</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="padding-right:5px"><? echo $line_shape_arr[$mstDataArray[0][csf('line_shape')]]; ?></td>
				<td><strong>No Of Work Sta.</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="padding-right:5px"><? echo $NoOfWorker; ?></td>
				<td><strong>Layout Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo change_date_format($mstDataArray[0][csf('layout_date')]); ?></td>
			</tr>

			<tr>
				<td><strong>Efficiency</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $balancingPageArray[0][csf('efficiency')]; ?></td>
				<td><strong>Target</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $balancingPageArray[0][csf('target')]; ?></td>
				<td><strong>Prod. Dept</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $product_dept[$mstDataArray[0][csf('product_dept')]]; ?></td>
			</tr>

			<tr>
				<td><strong>Cust. Style</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $mstDataArray[0][csf('custom_style')]; ?></td>

				<td><strong>Fabric Type</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>

				<td><strong>Bulletin Type</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>



			</tr>


			<tr>
				<td><strong>Insert By</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $user_arr[$mstDataArray[0][csf('inserted_by')]]; ?></td>
				<td><strong>Modify By</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $user_arr[$mstDataArray[0][csf('updated_by')]]; ?></td>
				<td><strong>Applicable Period</strong></td>
				<td><strong>:</strong></td>
				<td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>


			</tr>
			<tr>
				<td><strong>Insert Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('insert_date')])); ?></td>
				<td><strong>Modify Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td colspan="4"><? if ($mstDataArray[0][csf('update_date')] != "" && $mstDataArray[0][csf('update_date')] != "0000-00-00") echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('update_date')])); ?></td>

			</tr>
			<tr>
				<td><strong>Inter. Ref</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><?= $mstDataArray[0][csf('internal_ref')]; ?></td>
				<td><strong>Remarks</strong></td>
				<td><strong>:</strong></td>
				<td colspan="4"><? echo $mstDataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br />
		<?

		if ($line_shape == 2) {
			$i = 1;
			$firstHalf = round($NoOfWorker / 2);
			$secondHalf = round($NoOfWorker - $firstHalf);

			echo '<table width="850" ><tr><td valign="top"  width="50%">';
			echo '<table width="100%" align="right" cellspacing="5">';
			echo '<tbody>';
			for ($z = $NoOfWorker; $z >= 1; $z--) {
				if ($z % 2 == 1) {
					$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
					if (count($work_stations) > 0) {
						$row_span = count($work_stations);
						$j = 0;
						foreach ($work_stations as $dtlsId) {
		?>
							<tr>
								<td width="44%" class="box" align="center"><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
								<td class="cercle" align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
								<td width="44%" class="octagon" align="center">
									<?
									$manualArr = array(40, 41, 43, 44, 48, 68, 69, 53, 54, 55, 56, 70, 90, 129);
									echo (in_array($gsdDataArr[$dtlsId]['resource_gsd'], $manualArr)) ? '<img src="../../home_css/logo/man.gif" height="30" />' : $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']];

									?>
								</td>

							</tr>
						<?
							$j++;
							$i++;
						}
					} else {

						$i++;
					}
				}
			}

			$height = ($i - 1) * 23 + 43;
			echo '</tbody></table></td>
			<td align="center" valign="middle" width="25" class="box">Table</td>';
			echo '<td valign="top"><table width="100%" align="right" cellspacing="5">';
			echo '<tbody>';
			for ($z = $NoOfWorker; $z >= 1; $z--) {
				if ($z % 2 == 0) {
					$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
					if (count($work_stations) > 0) {
						$row_span = count($work_stations);
						$j = 0;
						foreach ($work_stations as $dtlsId) {
						?>
							<tr>
								<td width="44%" class="octagon" align="center">
									<?
									$manualArr = array(40, 41, 43, 44, 48, 68, 69, 53, 54, 55, 56, 70, 90, 129);
									echo (in_array($gsdDataArr[$dtlsId]['resource_gsd'], $manualArr)) ? '<img src="../../home_css/logo/man.gif" height="30" />' : $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']];

									?>
								</td>
								<td class="cercle" align="center">
									<? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?>
								</td>
								<td width="44%" class="box" align="center"><?= $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
							</tr>
						<?
							$j++;
						}
					} else {
					}
				}
			}
			echo '</tbody></table></td></tr></table>';
		} else {
			echo '<table width="850" cellspacing="0" border="1" rules="all">';
			echo '<thead bgcolor="#dddddd" align="center">
					</th><th width="70">Work S.</th>
					<th width="110">Resource</th>
					<th width="70">Seq. No.</th>
					<th>Operation</th>
					<th width="75">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
			echo '<tbody>';

			for ($z = $NoOfWorker; $z >= 1; $z--) {
				$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
				if (count($work_stations) > 0) {
					$row_span = count($work_stations);
					$j = 0;
					foreach ($work_stations as $dtlsId) {
						?>
						<tr>
							<?
							if ($j == 0) {
							?>
								<td rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
							<?
							}
							?>
							<td align="center" title="<? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?>">
								<? //echo $production_resource[$gsdDataArr[$dtlsId]['resource_gsd']]; 
								?>
								<?
								$manualArr = array(40, 41, 43, 44, 48, 68, 69, 53, 54, 55, 56, 70, 90, 129);
								echo (in_array($gsdDataArr[$dtlsId]['resource_gsd'], $manualArr)) ? '<img src="../../home_css/logo/man.gif" height="30" />' : $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']];
								?>
							</td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
							<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
							<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
							<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
						</tr>
		<?
						$j++;
					}
				} else {
				}
			}
			echo '</tbody></table>';
		}



		$balanceDataArray = array();
		$blData = sql_select("select a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 and a.gsd_mst_id=$update_id  and b.is_deleted=0");
		foreach ($blData as $row) {
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv'] = $row[csf('smv')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp'] = $row[csf('layout_mp')];
		}

		$sqlDtls = "SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc from ppl_gsd_entry_dtls where mst_id='" . $update_id . "' and is_deleted=0 order by row_sequence_no asc";
		$data_array_dtls = sql_select($sqlDtls);

		$tot_smv = 0;
		$tot_mp = 0;
		$helperSmv = 0;
		$machineSmv = 0;
		$sQISmv = 0;
		$fIMSmv = 0;
		$fQISmv = 0;
		$polyHelperSmv = 0;
		$pkSmv = 0;
		$htSmv = 0;
		$imSmv = 0;
		$helperMp = 0;
		$machineMp = 0;
		$sQiMp = 0;
		$fImMp = 0;
		$fQiMp = 0;
		$polyHelperMp = 0;
		$pkMp = 0;
		$htMp = 0;
		$imMp = 0;
		$mpSumm = array();

		foreach ($data_array_dtls as $slectResult) {
			if ($balanceDataArray[$slectResult[csf('id')]]['smv'] > 0) {
				$smv = $balanceDataArray[$slectResult[csf('id')]]['smv'];
			} else {
				$smv = $slectResult[csf('total_smv')];
			}

			$rescId = $slectResult[csf('resource_gsd')];
			$layOut = $balanceDataArray[$slectResult[csf('id')]]['layout_mp'];

			if ($rescId == 40 || $rescId == 41 || $rescId == 43 || $rescId == 44 || $rescId == 48 || $rescId == 68 || $rescId == 69 || $rescId == 70 || $rescId == 147) {
				$helperSmv = $helperSmv + $smv;
				$helperMp = $helperMp + $layOut;
			} else if ($rescId == 53) {
				$fIMSmv = $fIMSmv + $smv;
				$fImMp = $fImMp + $layOut;
			} else if ($rescId == 54) {
				$fQISmv = $fQISmv + $smv;
				$fQiMp = $fQiMp + $layOut;
			} else if ($rescId == 55) {
				$polyHelperSmv = $polyHelperSmv + $smv;
				$polyHelperMp = $polyHelperMp + $layOut;
			} else if ($rescId == 56) {
				$pkSmv = $pkSmv + $smv;
				$pkMp = $pkMp + $layOut;
			} else if ($rescId == 90) {
				$htSmv = $htSmv + $smv;
				$htMp = $htMp + $layOut;
			} else if ($rescId == 176) {
				$imSmv = $imSmv + $smv;
				$imMp = $imMp + $layOut;
			} else {
				$machineSmv = $machineSmv + $smv;
				$machineMp = $machineMp + $layOut;

				$mpSumm[$rescId] += $layOut;
			}
			$i++;
		}
		?>
		<br />
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="260" valign="top">
					<b>SMV Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%">
						<tr bgcolor="#FFFFFF">
							<td width="120">Sewing Helper</td>
							<td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Sewing Machine</td>
							<td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Sewing QI</td>
							<td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Finishing I/M</td>
							<td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Finishing QI</td>
							<td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Poly Helper</td>
							<td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Packing</td>
							<td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Hand Tag</td>
							<td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Iron Man</td>
							<td id="ht" align="right" style="padding-right:5px"><? echo number_format($imSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo number_format($helperSmv + $machineSmv + $sQISmv + $fIMSmv + $fQISmv + $polyHelperSmv + $pkSmv + $htSmv + $imSmv, 2, '.', ''); ?></td>
						</tr>
					</table>
				</td>
				<td width="20" valign="top"></td>
				<td width="260" valign="top">
					<?
					$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;

					if (strpos($helperMp, ".") != "") {
						$helperMp = number_format($helperMp, 2, '.', '');
					}

					if (strpos($machineMp, ".") != "") {
						$machineMp = number_format($machineMp, 2, '.', '');
					}

					if (strpos($sQiMp, ".") != "") {
						$sQiMp = number_format($sQiMp, 2, '.', '');
					}

					if (strpos($totatMp, ".") != "") {
						$fImMp = number_format($fImMp, 2, '.', '');
					}

					if (strpos($fQiMp, ".") != "") {
						$fQiMp = number_format($fQiMp, 2, '.', '');
					}

					if (strpos($polyHelperMp, ".") != "") {
						$polyHelperMp = number_format($polyHelperMp, 2, '.', '');
					}

					if (strpos($pkMp, ".") != "") {
						$pkMp = number_format($pkMp, 2, '.', '');
					}

					if (strpos($htMp, ".") != "") {
						$htMp = number_format($htMp, 2, '.', '');
					}
					if (strpos($imMp, ".") != "") {
						$imMp = number_format($imMp, 2, '.', '');
					}

					if (strpos($totMpSumm, ".") != "") {
						$totMpSumm = number_format($totMpSumm, 2, '.', '');
					}
					?>
					<b>Man Power Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%">
						<tr bgcolor="#FFFFFF">
							<td width="120">Sewing Helper</td>
							<td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Sewing Machine</td>
							<td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Sewing QI</td>
							<td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Finishing I/M</td>
							<td id="fimm" align="right" style="padding-right:5px"><? echo $fImMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Finishing QI</td>
							<td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Poly Helper</td>
							<td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Packing</td>
							<td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Hand Tag</td>
							<td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Iron Man</td>
							<td id="htm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
						</tr>
					</table>
				</td>
				<td width="20" valign="top"></td>
				<td valign="top">
					<b>Machine Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
						<?
						$x = 1;
						$totatMp = 0;
						foreach ($mpSumm as $key => $mp) {
							if ($x % 2 == 0) $bgcolor = '#E9F3FF';
							else $bgcolor = '#FFFFFF';

							if (strpos($mp, ".") != "") {
								$mp = number_format($mp, 2, '.', '');
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="170"><? echo $production_resource_arr[$key]; ?></td>
								<td align="right" style="padding-right:5px"><? echo $mp; ?></td>
							</tr>
						<?
							$totatMp += $mp;
							$x++;
						}

						if ($x % 2 == 0) $bgcolor = '#E9F3FF';
						else $bgcolor = '#FFFFFF';

						if (strpos($totatMp, ".") != "") {
							$totatMp = number_format($totatMp, 2, '.', '');
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?
		foreach ($image_location_arr as $image_path) {
			echo '<img src="../../' . $image_path . '" height="100" style="margin:3px 3px 3px 0;" />';
		}
		?>


		<br>

		<?
		//There is no company in layout entry for this reason max company selected;
		echo signature_table(110, "(select max(company_id) as company_id from variable_settings_signature where report_id=110)", "900px");
		?>
	</div>
	<style>
		.octagon {
			border-radius: 10px !important;
		}

		.cercle {
			border-radius: 50% !important;
		}

		.octagon,
		.cercle,
		.box {
			border: 1px solid #000 !important;
			padding: 2px !important;
			height: 50px;
		}
	</style>
	<?
	exit();
}

if ($action == "layout_print3") {
	 
	$data = explode("**", $data);

	// print_r($data);die;

	$lo_update_id = $data[0];
	$bl_update_id = $data[1];

	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	$mstDataArray = sql_select("select a.PROCESS_ID,a.company_id,a.id,a.buyer_id, a.style_ref, a.custom_style, a.gmts_item_id, a.product_dept, a.remarks,a.fabric_type,a.bulletin_type,a.applicable_period,a.internal_ref, b.line_shape, b.no_of_work_st, b.layout_date, b.inserted_by, b.insert_date, b.updated_by, b.update_date,b.balance_mst_id from ppl_gsd_entry_mst a, ppl_balancing_mst_entry b where a.id=b.gsd_mst_id and b.id='" . $lo_update_id . "' and b.balancing_page=3");
	$update_id  = $mstDataArray[0][csf('id')];
	$NoOfWorker = $mstDataArray[0][csf('no_of_work_st')];
	$line_shape = $mstDataArray[0][csf('line_shape')];

	$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and process_id={$mstDataArray[0]['PROCESS_ID']}  order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");


	$balancingPageArray = sql_select("select b.efficiency,b.target from ppl_balancing_mst_entry b where b.id='" . $mstDataArray[0][csf('balance_mst_id')] . "' and b.balancing_page=1");
	$image_location_arr = return_library_array("select id,image_location from common_photo_library where master_tble_id='$update_id' and form_name='gsd_entry'", "id", "image_location");

	$workerTrackingArr = array();
	$layoutDataArr = array();
	$gsdDataArr = array();

	$layoutData = sql_select("select gsd_dtls_id, work_station from ppl_layout_dtls_entry where mst_id=$lo_update_id  and is_deleted=0 order by id");
	foreach ($layoutData as $row) {
		$layoutDataArr[$row[csf('work_station')]] .= $row[csf('gsd_dtls_id')] . ",";
	}

	$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id", "operation_name");
	$sql = "SELECT id, lib_sewing_id, row_sequence_no, resource_gsd, total_smv, target_on_full_perc,efficiency from ppl_gsd_entry_dtls where mst_id=$update_id and is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$gsdDataArr[$row[csf('id')]]['lib_sewing_id'] = $row[csf('lib_sewing_id')];
		$gsdDataArr[$row[csf('id')]]['row_sequence_no'] = $row[csf('row_sequence_no')];
		$gsdDataArr[$row[csf('id')]]['resource_gsd'] = $row[csf('resource_gsd')];
		$gsdDataArr[$row[csf('id')]]['total_smv'] = $row[csf('total_smv')];
		$gsdDataArr[$row[csf('id')]]['target_on_full_perc'] = $row[csf('target_on_full_perc')];
		$gsdDataArr[$row[csf('id')]]['efficiency'] = $row[csf('efficiency')];
	}
	?>

	<div style="width:850px">
		<table width="100%">
			<tr>
				<td align="center" colspan="9"><strong><u>Layout</u></strong></td>
			</tr>
			<tr>
				<td width="80"><strong>Style Ref.</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="190">
				<? echo $mstDataArray[0][csf('style_ref')];
				if ($mstDataArray[0][csf('custom_style')] != "") echo " (" . $mstDataArray[0][csf('custom_style')] . ")"; ?>
				</td>
				<td width="130"><strong>Buyer Name</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="130"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
				<td width="130"><strong>Garments Item</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Line Shape</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="padding-right:5px"><? echo $line_shape_arr[$mstDataArray[0][csf('line_shape')]]; ?></td>
				<td><strong>No Of Work Sta.</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="padding-right:5px"><? echo $NoOfWorker; ?></td>
				<td><strong>Layout Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo change_date_format($mstDataArray[0][csf('layout_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Efficiency</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $balancingPageArray[0][csf('efficiency')]; ?></td>
				<td><strong>Target</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $balancingPageArray[0][csf('target')]; ?></td>
				<td><strong>Prod. Dept</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $product_dept[$mstDataArray[0][csf('product_dept')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Cust. Style</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $mstDataArray[0][csf('custom_style')]; ?></td>
				<td><strong>Fabric Type</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>
				<td><strong>Bulletin Type</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Insert By</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $user_arr[$mstDataArray[0][csf('inserted_by')]]; ?></td>
				<td><strong>Modify By</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo $user_arr[$mstDataArray[0][csf('updated_by')]]; ?></td>
				<td><strong>Applicable Period</strong></td>
				<td><strong>:</strong></td>
				<td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
			</tr>
			<tr>
				<td><strong>Insert Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><? echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('insert_date')])); ?></td>
				<td><strong>Modify Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td colspan="4"><? if ($mstDataArray[0][csf('update_date')] != "" && $mstDataArray[0][csf('update_date')] != "0000-00-00") echo date("d-m-Y h:m:s A", strtotime($mstDataArray[0][csf('update_date')])); ?></td>
			</tr>
			<tr>
				<td><strong>Inter. Ref</strong></td>
				<td width="10"><strong>:</strong></td>
				<td><?= $mstDataArray[0][csf('internal_ref')]; ?></td>
				<td><strong>Remarks</strong></td>
				<td><strong>:</strong></td>
				<td colspan="4"><? echo $mstDataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br />
		<?
		// echo $line_shape;die;
		if ($line_shape == 2) {
			$i = 1;
			$firstHalf = round($NoOfWorker / 2);
			$secondHalf = round($NoOfWorker - $firstHalf);

			echo '<table width="850" ><tr><td valign="bottom" width="50%">';
			echo '<table width="100%" align="right" cellspacing="5">';
			echo '<tbody>';
			for ($z = $NoOfWorker; $z >= 1; $z--) {
				if ($z % 2 == 1) {
					$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
					if (count($work_stations) > 0) {
						$row_span = count($work_stations);
						$j = 0;
						foreach ($work_stations as $dtlsId) {
		                ?>
							<tr>
								<td width="44%" class="box" align="center"><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
								<td class="cercle" align="center"><? echo $z; ?></td>
								<td width="44%" class="octagon" align="center">
									<?
									$manualArr = array(40, 41, 43, 44, 48, 68, 69, 53, 54, 55, 56, 70, 90, 129);
									echo (in_array($gsdDataArr[$dtlsId]['resource_gsd'], $manualArr)) ? '<img src="../../home_css/logo/man.gif" height="30" />' : $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']];
									?>
								</td>
							</tr>
						<?
							$j++;
							$i++;
						}
					} 
					else {
						$i++;
					}
				}
			}
			$height = ($i - 1) * 23 + 43;
			echo '</tbody></table></td>
			<td align="center" valign="middle" width="25" class="box">Table</td>';
			echo '<td valign="bottom" width="50%"><table width="100%" align="right" cellspacing="5">';
			echo '<tbody>';
 
			for ($z = $NoOfWorker; $z >= 1; $z--) {
				if ($z % 2 == 0) {
					$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
					if (count($work_stations) > 0) {
						$row_span = count($work_stations);
						$j = 0;
						foreach ($work_stations as $dtlsId) {
						?>
						<tr>
							<td width="44%" class="octagon" align="center">
								<?
								$manualArr = array(40, 41, 43, 44, 48, 68, 69, 53, 54, 55, 56, 70, 90, 129);
								echo (in_array($gsdDataArr[$dtlsId]['resource_gsd'], $manualArr)) ? '<img src="../../home_css/logo/man.gif" height="30" />' : $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']];
								?>
							</td>
							<td class="cercle" align="center"><? echo $z; ?></td>
							<td width="46%" class="box" align="center"><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
						</tr>
						<?
						$j++;
						}
					} 
					else {
					}
				}
			}
			echo '</tbody></table></td></tr></table>';
		}
		
		else {
			echo '<table width="850" cellspacing="0" border="1" rules="all">';
			echo '<thead bgcolor="#dddddd" align="center">
					</th><th width="70">Work S.</th>
					<th width="110">Resource</th>
					<th width="70">Seq. No.</th>
					<th>Operation</th>
					<th width="75">SMV</th>
					<th width="75">Target (100%)</th>
					<th width="75">Efficiency</th>
					<th width="75">Target<br>(on eff)</th>';
			echo '<tbody>';

			for ($z = $NoOfWorker; $z >= 1; $z--) {
				$work_stations = array_filter(explode(",", chop($layoutDataArr[$z], ',')));
				if (count($work_stations) > 0) {
					$row_span = count($work_stations);
					$j = 0;
					foreach ($work_stations as $dtlsId) {
						?>
						<tr>
							<?
							if ($j == 0) {
							?>
								<td rowspan="<? echo $row_span; ?>" align="center"><? echo $z; ?></td>
							<?
							}
							?>
							<td align="center" title="<? echo $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']]; ?>">
								<? //echo $production_resource[$gsdDataArr[$dtlsId]['resource_gsd']]; 
								?>
								<?
								$manualArr = array(40, 41, 43, 44, 48, 68, 69, 53, 54, 55, 56, 70, 90, 129);
								echo (in_array($gsdDataArr[$dtlsId]['resource_gsd'], $manualArr)) ? '<img src="../../home_css/logo/man.gif" height="30" />' : $production_resource_arr[$gsdDataArr[$dtlsId]['resource_gsd']];
								?>
							</td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['row_sequence_no']; ?></td>
							<td><? echo $operation_arr[$gsdDataArr[$dtlsId]['lib_sewing_id']]; ?></td>
							<td align="center"><? echo number_format($gsdDataArr[$dtlsId]['total_smv'], 2, '.', ''); ?></td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['target_on_full_perc']; ?></td>
							<td align="center"><? echo $gsdDataArr[$dtlsId]['efficiency']; ?></td>
							<td align="center" title="=Target/100*Efficiency"><? echo  number_format($gsdDataArr[$dtlsId]['target_on_full_perc'] / 100 * $gsdDataArr[$dtlsId]['efficiency'], 0); ?></td>
						</tr>
		            <?
						$j++;
					}
				} 
				else {

				}
			}
			echo '</tbody></table>';
		}
 

		$balanceDataArray = array();
		$blData = sql_select("select a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 and a.gsd_mst_id=$update_id  and b.is_deleted=0");
		foreach ($blData as $row) {
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv'] = $row[csf('smv')];
			$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp'] = $row[csf('layout_mp')];
		}

		$sqlDtls = "SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv, target_on_full_perc from ppl_gsd_entry_dtls where mst_id='" . $update_id . "' and is_deleted=0 order by row_sequence_no asc";
		$data_array_dtls = sql_select($sqlDtls);

		$tot_smv = 0;
		$tot_mp = 0;
		$helperSmv = 0;
		$machineSmv = 0;
		$sQISmv = 0;
		$fIMSmv = 0;
		$fQISmv = 0;
		$polyHelperSmv = 0;
		$pkSmv = 0;
		$htSmv = 0;
		$helperMp = 0;
		$machineMp = 0;
		$sQiMp = 0;
		$fImMp = 0;
		$fQiMp = 0;
		$polyHelperMp = 0;
		$pkMp = 0;
		$htMp = 0;
		$mpSumm = array();

		foreach ($data_array_dtls as $slectResult) {
			if ($balanceDataArray[$slectResult[csf('id')]]['smv'] > 0) {
				$smv = $balanceDataArray[$slectResult[csf('id')]]['smv'];
			} else {
				$smv = $slectResult[csf('total_smv')];
			}

			$rescId = $slectResult[csf('resource_gsd')];
			$layOut = $balanceDataArray[$slectResult[csf('id')]]['layout_mp'];

			if ($rescId == 40 || $rescId == 41 || $rescId == 43 || $rescId == 44 || $rescId == 48 || $rescId == 68 || $rescId == 70 || $rescId == 147) {
				$helperSmv = $helperSmv + $smv;
				$helperMp = $helperMp + $layOut;
			} else if ($rescId == 53) {
				$fIMSmv = $fIMSmv + $smv;
				$fImMp = $fImMp + $layOut;
			} else if ($rescId == 54) {
				$fQISmv = $fQISmv + $smv;
				$fQiMp = $fQiMp + $layOut;
			} else if ($rescId == 55) {
				$polyHelperSmv = $polyHelperSmv + $smv;
				$polyHelperMp = $polyHelperMp + $layOut;
			} else if ($rescId == 56) {
				$pkSmv = $pkSmv + $smv;
				$pkMp = $pkMp + $layOut;
			} else if ($rescId == 90) {
				$htSmv = $htSmv + $smv;
				$htMp = $htMp + $layOut;
			} else if ($rescId == 176 || $rescId == 69) {
				$imSmv = $imSmv + $smv;
				$imMp = $imMp + $layOut;
			} else {
				$machineSmv = $machineSmv + $smv;
				$machineMp = $machineMp + $layOut;

				$mpSumm[$rescId] += $layOut;
			}
			$i++;
	    } 
		?>
		<br />
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="260" valign="top">
					<b>SMV Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%">
						<tr bgcolor="#FFFFFF">
							<td width="120">Sewing Helper</td>
							<td id="sh" align="right" style="padding-right:5px"><? echo number_format($helperSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Sewing Machine</td>
							<td id="sm" align="right" style="padding-right:5px"><? echo number_format($machineSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Sewing QI</td>
							<td id="sq" align="right" style="padding-right:5px"><? echo number_format($sQISmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Finishing I/M</td>
							<td id="fim" align="right" style="padding-right:5px"><? echo number_format($fIMSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Finishing QI</td>
							<td id="fq" align="right" style="padding-right:5px"><? echo number_format($fQISmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Poly Helper</td>
							<td id="ph" align="right" style="padding-right:5px"><? echo number_format($polyHelperSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Packing</td>
							<td id="pk" align="right" style="padding-right:5px"><? echo number_format($pkSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Hand Tag</td>
							<td id="ht" align="right" style="padding-right:5px"><? echo number_format($htSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Iron Man</td>
							<td id="ht" align="right" style="padding-right:5px"><? echo number_format($imSmv, 2, '.', ''); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo number_format($helperSmv + $machineSmv + $sQISmv + $fIMSmv + $fQISmv + $polyHelperSmv + $pkSmv + $htSmv + $imSmv, 2, '.', ''); ?></td>
						</tr>
					</table>
				</td>
				<td width="20" valign="top"></td>
				<td width="260" valign="top">
					<?
					$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;

					if (strpos($helperMp, ".") != "") {
						$helperMp = number_format($helperMp, 2, '.', '');
					}

					if (strpos($machineMp, ".") != "") {
						$machineMp = number_format($machineMp, 2, '.', '');
					}

					if (strpos($sQiMp, ".") != "") {
						$sQiMp = number_format($sQiMp, 2, '.', '');
					}

					if (strpos($totatMp, ".") != "") {
						$fImMp = number_format($fImMp, 2, '.', '');
					}

					if (strpos($fQiMp, ".") != "") {
						$fQiMp = number_format($fQiMp, 2, '.', '');
					}

					if (strpos($polyHelperMp, ".") != "") {
						$polyHelperMp = number_format($polyHelperMp, 2, '.', '');
					}

					if (strpos($pkMp, ".") != "") {
						$pkMp = number_format($pkMp, 2, '.', '');
					}

					if (strpos($htMp, ".") != "") {
						$htMp = number_format($htMp, 2, '.', '');
					}
					if (strpos($imMp, ".") != "") {
						$imMp = number_format($imMp, 2, '.', '');
					}

					if (strpos($totMpSumm, ".") != "") {
						$totMpSumm = number_format($totMpSumm, 2, '.', '');
					}
					?>
					<b>Man Power Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%">
						<tr bgcolor="#FFFFFF">
							<td width="120">Sewing Helper</td>
							<td id="shm" align="right" style="padding-right:5px"><? echo $helperMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Sewing Machine</td>
							<td id="smm" align="right" style="padding-right:5px"><? echo $machineMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Sewing QI</td>
							<td id="sqm" align="right" style="padding-right:5px"><? echo $sQiMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Finishing I/M</td>
							<td id="fimm" align="right" style="padding-right:5px"><? echo $fImMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Finishing QI</td>
							<td id="fqm" align="right" style="padding-right:5px"><? echo $fQiMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Poly Helper</td>
							<td id="phm" align="right" style="padding-right:5px"><? echo $polyHelperMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Packing</td>
							<td id="pkm" align="right" style="padding-right:5px"><? echo $pkMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Hand Tag</td>
							<td id="htm" align="right" style="padding-right:5px"><? echo $htMp; ?></td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Iron Man</td>
							<td id="htm" align="right" style="padding-right:5px"><? echo $imMp; ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo $totMpSumm; ?></td>
						</tr>
					</table>
				</td>
				<td width="20" valign="top"></td>
				<td valign="top">
					<b>Machine Summary</b>
					<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
						<?
						$x = 1;
						$totatMp = 0;
						foreach ($mpSumm as $key => $mp) {
							if ($x % 2 == 0) $bgcolor = '#E9F3FF';
							else $bgcolor = '#FFFFFF';

							if (strpos($mp, ".") != "") {
								$mp = number_format($mp, 2, '.', '');
							}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="170"><? echo $production_resource_arr[$key]; ?></td>
								<td align="right" style="padding-right:5px"><? echo $mp; ?></td>
							</tr>
						<?
							$totatMp += $mp;
							$x++;
						}

						if ($x % 2 == 0) $bgcolor = '#E9F3FF';
						else $bgcolor = '#FFFFFF';

						if (strpos($totatMp, ".") != "") {
							$totatMp = number_format($totatMp, 2, '.', '');
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="right"><b>Total</b></td>
							<td align="right" style="padding-right:5px"><? echo $totatMp; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?
		foreach ($image_location_arr as $image_path) {
			echo '<img src="../../' . $image_path . '" height="100" style="margin:3px 3px 3px 0;" />';
		}
		?>
 
		<br>

		<?
		//There is no company in layout entry for this reason max company selected;
		echo signature_table(110, "(select max(company_id) as company_id from variable_settings_signature where report_id=110)", "900px");
		?>
	</div>
	<style>
		.octagon {
			border-radius: 10px !important;
		}

		.cercle {
			border-radius: 50% !important;
		}

		.octagon,
		.cercle,
		.box {
			border: 1px solid #000 !important;
			padding: 2px !important;
			height: 50px;
		}
	</style>
	<?
	exit();
}







?>