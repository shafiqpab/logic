<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == 'load_drop_down_buyer') {
	echo create_drop_down("cbo_buyer_name", 100, "select distinct buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", 'id,buyer_name', 1, "-- All Buyer --", $selected, '', 0);
	exit();
}


if ($action == 'style_search_popup') {
	extract($_REQUEST);
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
?>
	<script>
		function js_set_value(str) {
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
	</script>

	<?php
	$buyer = str_replace("'", "", $buyer);
	//$company=str_replace("'","",$company);
	$job_year = str_replace("'", "", $job_year);
	$company_cond = "";
	$buyer_cond = "";

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$arr = array(0 => $buyer_arr);

	if ($company != '') $company_cond = " and company_name = $company";
	if ($buyer != 0) $buyer_cond = " and buyer_id in($buyer)";
	if ($job_year != 0) $job_year_cond = " and to_char(applicable_period,'YYYY')=$job_year";
	else $job_year_cond = "";
	$select_date = " to_char(applicable_period,'YYYY')";

	$sql = "select id,po_job_no,buyer_id,style_ref,buyer_id,company_id, to_char(applicable_period,'YYYY') as year,internal_ref from ppl_gsd_entry_mst where  status_active=1 $buyer_cond $job_year_cond and is_deleted=0 order by po_job_no desc, to_char(applicable_period,'YYYY')";
	echo create_list_view("list_view", "Buyer,Job Year,Job No,Style Ref No,Internal Ref No", "160,90,100,100,100", "610", "500", 0, $sql, "js_set_value", "style_ref", "", 1, "buyer_id,0", $arr, "buyer_id,year,po_job_no,style_ref,internal_ref", "", "setFilterGrid('list_view',-1)", "0", "", "");
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
	<script language="javascript" type="text/javascript">
		var style_no = '<?php echo $txt_ref_no; ?>';
		var style_id = '<?php echo $txt_style_ref_id; ?>';
		var style_des = '<?php echo $txt_style_ref_no; ?>';
		//alert(style_des);
		if (style_no != "") {
			style_no_arr = style_no.split(",");
			style_id_arr = style_id.split(",");
			style_des_arr = style_des.split(",");
			var str_ref = "";
			for (var k = 0; k < style_no_arr.length; k++) {
				str_ref = style_no_arr[k] + '_' + style_id_arr[k] + '_' + style_des_arr[k];
				js_set_value(str_ref);
			}
		}
	</script>

<?php

	exit();
}


if ($action == 'generate_report_1') 
{
	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y')
	{
		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);
		while ($current <= $last) {
			$dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
		}
		return $dates;
	}

	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$cbo_company_name = str_replace("'", '', $cbo_company_name);
	$cbo_working_company_name = str_replace("'", '', $cbo_working_company_name);
	$cbo_location_name = str_replace("'", '', $cbo_location_name);
	$cbo_buyer_name = str_replace("'", '', $cbo_buyer_name);
	$txt_style_no = str_replace("'", '', $txt_style_no);
	$txt_job_no = str_replace("'", '', $txt_job_no);
	$txt_order_no = str_replace("'", '', $txt_order_no);
	$cbo_sewing_line = str_replace("'", '', $cbo_sewing_line);
	$date_from = str_replace("'", '', $txt_date_from);
	$date_to = str_replace("'", '', $txt_date_to);
	$cbo_year_selection = str_replace("'", '', $cbo_year_selection);

	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$company_library = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_library = return_library_array("select id, location_name from lib_location where status_active = 1", 'id', 'location_name');
	$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
	$prod_reso_library = return_library_array("select id, line_number from prod_resource_mst", 'id', 'line_number');

	$sql_cond = '';
	$sql_resource_cond = '';

	$wo_result = array();
	$production_arr = array();
	$resource_arr = array();
	$unit_summery_arr = array();
	$buyer_summery_arr = array();
	$smv_arr = array();

	$buyer_conds = '';
 
	if ($date_from == "" && $date_to == "") $date_cond = "";
	else $date_cond = " and b.applicable_period between '" . $date_from . "' and '" . $date_to . "'";
 
	if ($cbo_buyer_name != 0) {
		$sql_cond .= " and b.buyer_id in($cbo_buyer_name)";

		$buyer_conds = " and a.buyer_name in($cbo_buyer_name)";
	}

	$company_conds = '';
	if ($cbo_company_name != 0) {
		$sql_company_cond .= " and b.buyer_id in($cbo_company_name)";

		$company_conds = " and a.company_name in($cbo_company_name)";
	}

	if ($txt_style_no != '') {
		$sql_cond .= " and b.style_ref = '$txt_style_no'";
	}
	if ($txt_style_no == "") $style_cond = "";

	else $style_cond = " and a.style_ref='$txt_style_no'";
 
	$mstDataArray = sql_select("SELECT a.PROCESS_ID,a.id,a.company_id, a.fabric_type,a.remarks,a.custom_style,a.buyer_id, a.style_ref, a.gmts_item_id, a.working_hour,a.applicable_period,a.bulletin_type,a.total_smv,a.color_type,a.extention_no,a.system_no,b.inserted_by,a.prod_description,b.insert_date,b.updated_by,b.update_date, b.allocated_mp, b.line_no, b.pitch_time, b.target, b.efficiency FROM ppl_gsd_entry_mst a, ppl_balancing_mst_entry b WHERE a.id=b.gsd_mst_id AND a.buyer_ID=$cbo_buyer_name $style_cond AND b.balancing_page=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0");
	$width = 1240;
	ob_start();
    ?>

	<style>
		.block_div {
			width: auto;
			height: auto;
			text-wrap: normal;
			vertical-align: bottom;
			position: !important;
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
		}
		.os_summary_report
		{
			word-break:break-all;
			word-wrap: break-word;
			width:150%;
			margin:0px;
			padding:0px;
		}
	</style>
	<div style="width:<? echo $width; ?>px">
		<fieldset style="width:100%;" align="center">
			<table width="<? echo $width; ?>">
				<tr class="form_caption">
					<td colspan="10" align="center">Stylewise Machine Requirement</td>
				</tr>
				<tr class="form_caption">
					<td colspan="10" align="center"><? echo $company_library[$cbo_company_name]; ?></td>
				</tr>
			</table>
 
			<?
			$po_details_master = return_library_array("select id,style_ref_no from wo_po_details_master", "id", "style_ref_no");
			
			$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id", "operation_name");
			$attach_id = return_library_array("select id,attachment_name from lib_attachment", 'id', 'attachment_name');

			$sqlDtls = "SELECT a.id, a.mst_id, a.row_sequence_no, a.body_part_id, a.lib_sewing_id, b.gmts_item_id, a.resource_gsd, a.attachment_id, a.efficiency, a.total_smv, a.target_on_full_perc, a.target_on_effi_perc, b.process_id, b.id,b.company_id, b.fabric_type, b.custom_style, b.buyer_id, b.style_ref, b.applicable_period,c.layout_mp,b.job_id FROM ppl_gsd_entry_dtls a, ppl_gsd_entry_mst b, ppl_balancing_dtls_entry c WHERE a.mst_id=b.id AND c.GSD_MST_ID=b.id AND a.id=c.GSD_DTLS_ID  AND a.is_deleted=0 AND b.is_deleted=0 AND c.is_deleted=0 $sql_cond $date_cond 
			GROUP BY a.id, a.mst_id, a.row_sequence_no, a.body_part_id, a.lib_sewing_id, a.resource_gsd, a.attachment_id, a.efficiency, a.total_smv, a.target_on_full_perc, a.target_on_effi_perc, b.process_id, b.id, b.company_id, b.fabric_type, b.custom_style, b.buyer_id, b.style_ref, b.applicable_period, b.gmts_item_id, c.layout_mp, b.job_id ORDER BY a.row_sequence_no";
 
			$sql_query = sql_select($sqlDtls);
			$production_resource_arr = return_library_array("select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$sql_query[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID", "RESOURCE_NAME");
			$tot_rows = count($sql_query);

			$i = 1;
			$style_ref_arr = array();
			$style_data_arr = array();
			$sewing_machine = array();
			$assitant_machine = array();
			foreach ($sql_query as $row) 
			{
				$style_data_arr[$row[csf('id')]]["STYLE_REF"] = $row[csf('style_ref')];
				$style_data_arr[$row[csf('id')]]["COMPANY_ID"] = $row[csf('company_id')];
				$style_data_arr[$row[csf('id')]]["BUYER_ID"] = $row[csf('buyer_id')];
				$style_data_arr[$row[csf('id')]]["gmts_item_id"] = $row[csf('gmts_item_id')];
				$style_data_arr[$row[csf('id')]]["PROCESS_ID"] = $row[csf('process_id')];
				$style_data_arr[$row[csf('id')]]["PROCESS_ID"] = $row[csf('process_id')];
				$style_data_arr[$row[csf('id')]]["TOT_LAYOUT_MP"] += $row[csf('layout_mp')];
				$style_data_arr[$row[csf('id')]]["TOTAL_SMV"] += $row[csf('total_smv')];
				$style_data_arr[$row[csf('id')]]["CM_COST"] = $row[csf('cm_cost')];
				$style_data_arr[$row[csf('id')]]["CM_COST"] = $row[csf('cm_cost')];
				$style_data_arr[$row[csf('id')]]["JOB_ID"] = $row[csf('job_id')];
 
				if($row[csf('job_id')])
				{
					$style_ref_arr[$row[csf('job_id')]] = $row[csf('job_id')];
				}

				if ($row[csf('resource_gsd')] != 40 && $row[csf('resource_gsd')] != 41 && $row[csf('resource_gsd')] != 43  && $row[csf('resource_gsd')] != 44  && $row[csf('resource_gsd')] != 68  && $row[csf('resource_gsd')] != 48  && $row[csf('resource_gsd')] != 70  && $row[csf('resource_gsd')] != 147  && $row[csf('resource_gsd')] != 69  && $row[csf('resource_gsd')] != 55 && $row[csf('resource_gsd')] != 176) {
					$item_group_arr[$row[csf('resource_gsd')]] = $row[csf('resource_gsd')];
					$balanceData[$row[csf('id')]][$row[csf('RESOURCE_GSD')]]['layout_mp'] += $row[csf('layout_mp')];
				} elseif ($row[csf('resource_gsd')] == 69) {
					$item_group[$row[csf('resource_gsd')]] = $row[csf('resource_gsd')];
					$sewing_machine[$row[csf('id')]][$row[csf('RESOURCE_GSD')]]['layout_mp'] += $row[csf('layout_mp')];
				} else {
					$assitant_machine[$row[csf('id')]][$row[csf('RESOURCE_GSD')]]['layout_mp'] += $row[csf('layout_mp')];
				}
			}
			//echo "<pre>";
			//print_r($style_data_arr);die;

			// For Costing SAM 
			$sql_dtls_master_arr = array();
			$job_id_arr = array();
			$sqlDtlsMaster = "SELECT id, style_ref_no, set_smv, job_quantity from wo_po_details_master where is_deleted=0 and is_deleted=0 and id IN (".implode(',',$style_ref_arr).")";

			//echo $sqlDtlsMaster;die;

			$sqlDtlsMasters = sql_select($sqlDtlsMaster);
			$style_qnty_arr = array();
			foreach ($sqlDtlsMasters as $row) {
				$sql_dtls_master_arr[$row['STYLE_REF_NO']] = $row['SET_SMV'];
				$job_id_arr[$row['ID']] = $row['ID'];

				$style_qnty_arr[$row['STYLE_REF_NO']] += $row[csf('JOB_QUANTITY')];
			}

			//print_r($style_ref_arr);die;
	        	

			$cost_dtls_arr = array();
            $cost_dtls_sql = "SELECT JOB_ID,CM_COST FROM WO_PRE_COST_DTLS WHERE JOB_ID IN (".implode(',',$job_id_arr).") and IS_DELETED=0";
		    $cost_dtls_sql_query = sql_select($cost_dtls_sql);
		    foreach($cost_dtls_sql_query as $row){
			    $cost_dtls_arr[$row['JOB_ID']] = $row['CM_COST'];
		    }

			$sam = $mstDataArray[0][csf('total_smv')];
			$tot_item = count($item_group_arr);
			//$width += $tot_item;
			?>
			<table class="rpt_table" width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="4">&nbsp; </th>
						<th style="font-size:20px;border-left: 2px solid #0000FF;border-right: 2px solid #0000FF;" colspan="<? echo count($item_group_arr); ?>"><b>Machine Type</b></th>
						<th>&nbsp; </th>
						<th style="font-size:20px;border-left: 2px solid #0000FF;border-right: 2px solid #0000FF;" colspan="3"><b>Manual</b></th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
					<tr height="100">
						<th width="30">SL</th>
						<th width="60">Style Ref</th>
						<th width="60">Buyer</th>
						<th width="60">Item</th>
						<th width="60">Job Qty</th>
						<?
						foreach ($item_group_arr as $item_key => $item) {
						?>
							<th width="30">
								<div class="block_div"><p class="os_summary_report"><? echo $production_resource_arr[$item_key]; ?></p></div>
							</th>
						<?
						}
						?>
						<th width="40">Machine Total</th>
						<th width="30">OP</th>
						<th width="30">HP</th>
						<th width="30">IRON</th>
						<th width="50">Manual Total</th>
						<th width="60">Production SAM</th>
						<th width="60">Costing SAM</th>
						<th width="60" title="Costing SAM-Production SAM">Variance SAM</th>
						<th width="60">Budget CM</th>
					</tr>
				</thead>
			</table>
			<?
			$i = 1; 
			?>
			<div style="width:<? echo $width + 20; ?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="<? echo $width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?
						foreach ($style_data_arr as $key => $value) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center" width="30" title="<? echo $po_qty; ?>"><? echo $i; ?></td>
								<td align="center" width="60" style="word-break: break-all;">
									<p><? echo $value["STYLE_REF"]; ?></p>
								</td>
								<td align="center" width="60">
									<p><? echo $buyer_library[$value["BUYER_ID"]]; ?></p>
								</td>
								<td align="center" width="60" style="word-break: break-all;">
									<p><? echo $garments_item[$value["gmts_item_id"]]; ?></p>
								</td>
								<td align="center" width="60" style="word-break: break-all;">
									<?= $style_qnty_arr[$value["STYLE_REF"]];?>
								</td>
								<?
								$total_layout = 0;
								foreach ($item_group_arr as $item_key_s => $item_s) {
									$layout = $balanceData[$key][$item_key_s]['layout_mp'];
								    ?>
									<td width="30" align="center"><? echo $layout;?></td>
								    <?
									$total_layout += $layout;
								}
								?>
								<td width="44" align="center" style="word-break: break-all;">
									<p><? echo $total_layout; ?></p>
								</td>
								<td width="30" align="center" style="word-break: break-all;">
									<p><? echo $total_layout; ?></p>
								</td>
								<?
								foreach ($item_group as $item_key_m => $item_m) {
									$lay = $sewing_machine[$key][$item_key_m]['layout_mp'];
								}
								?>

								<td width="30" align="center" style="word-break: break-all;">
									<p><? echo $value["TOT_LAYOUT_MP"] - $total_layout - $lay; ?></p>
								</td>
								<td width="30" align="center" style="word-break: break-all;">
								<? echo $lay; ?>
								</td>
								<td width="50" align="center" style="word-break: break-all;">
									<p><? echo $value["TOT_LAYOUT_MP"]; ?></p>
								</td>
								<td width="60" align="center" style="word-break: break-all;">
									<p><? echo $value["TOTAL_SMV"]; ?></p>
								</td>
								<td width="60" align="center" style="word-break: break-all;">
									<p><?= $sql_dtls_master_arr[$value["STYLE_REF"]] ?></p>
								</td>
								<td width="60" align="center" style="word-break: break-all;">
								<?
								$total_smv = $sql_dtls_master_arr[$value["STYLE_REF"]]-$value['TOTAL_SMV'];
								echo number_format($total_smv,2,'.','');
								?>
								</td>
								<td width="60" align="center" style="word-break: break-all;">
								<?= $cost_dtls_arr[$value['JOB_ID']];?>
								</td>
							<?
							$i++;
						}
						$po_data_arr = array();
							?>
							</tr>
					</tbody>
				</table>
			</div>
		</fieldset>
	</div>
	<?php
	$mainReportData = ob_get_contents();
	ob_clean();

	ob_start(); 
	$summeryDate = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old)) @unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $summeryDate . $mainReportData);
	$filename = $user_id . "_" . $name . ".xls";
	// echo "$total_data****$filename";
	echo $mainReportData . '**' . $summeryDate . '**' . $filename;

	exit();
}
?>