<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$colorname_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_short_library = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
}

if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' ", "id,location_name", 1, "-- Select --", $selected, "");
	exit();
}

if ($action == "print_report_button_setting") {

	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
	echo $print_report_format;
}

if ($db_type == 0) $insert_year = "SUBSTRING_INDEX(a.insert_date, '-', 1)";
if ($db_type == 2) $insert_year = "extract( year from b.insert_date)";


if ($action == "job_no_search_popup") {
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {


			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
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
				<fieldset style="width:780px;">
					<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Company</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Job No</th>
							<th> Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Job No", 2 => "Style Ref");
									$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<? echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'style_wise_linking_summary_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if ($action == "create_job_no_search_list_view") {
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	$company_con = '';
	if (empty($company_id)) {
		echo "Select Company First";
		die;
	} else {
		$company_con = " and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field = '';
	if (!empty($data[2])) {
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like " . $search_string;
	}


	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year";
		//$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year";
		//$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	} else {
		$year_field = "";
		// $year_cond = "";
	} //defined Later



	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";

	// echo $sql;

	$conclick = "id,job_no";
	$style = $data[5];
	if ($style == 1) {
		$conclick = "id,style_ref_no";
	}

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '', 1);
	exit();
}

if ($action == "report_generate") {
	extract($_REQUEST);

	$company_arr = return_library_array("SELECT ID, COMPANY_NAME FROM LIB_COMPANY", "id", "company_name");
	$buyer_arr = return_library_array("SELECT ID, BUYER_NAME FROM LIB_BUYER WHERE STATUS_ACTIVE=1 AND IS_DELETED=0", 'id', 'buyer_name');
	//$operation_arr = return_library_array("SELECT OPERATION_ID, OPERATION_NAME FROM PPL_CUT_LAY_BUNDLE_OPERATION WHERE STATUS_ACTIVE=1 AND IS_DELETED=0", 'OPERATION_ID', 'OPERATION_NAME');
	$operation_arr = return_library_array("select id,operation_name from lib_sewing_operation_entry", "id","operation_name" );

	$company_id 	= str_replace("'", "", $cbo_company_id);
	$wo_company_id 	= str_replace("'", "", $cbo_wo_company_id);
	$buyer_id 		= str_replace("'", "", $cbo_buyer_id);
	$job_no 		= str_replace("'", "", $txt_job_no);
	$style_ref_no 	= str_replace("'", "", $txt_style_ref_no);
	$hide_job_id 	= str_replace("'", "", $hide_job_id);
	$shipment_status = str_replace("'", "", $cbo_shipment_status);
	$date_from 		= str_replace("'", "", $txt_date_from);
	$date_to 		= str_replace("'", "", $txt_date_to);
	$type 			= str_replace("'", "", $type);

	//echo "Company Id: " . $company_id . "<br> Working Company Id: " . $wo_company_id . "<br> buyer_id: " . $buyer_id . "<br>job_no: " . $job_no . "<br>style_ref_no: " . $style_ref_no . "<br>hide_job_id" . $hide_job_id . "<br>shipment_status" . $shipment_status . "<br> date_from" . $date_from . "<br>date_to" . $date_to . "<br>type" . $type."<br>";
	$sql_cond = "";
	$sql_cond .= ($company_id != 0) ? " AND A.COMPANY_NAME=$company_id" : "";
	$sql_cond .= ($wo_company_id != 0) ? " AND E.SERVING_COMPANY=$wo_company_id" : "";
	$sql_cond .= ($location_id != 0) ? " AND A.LOCATION_NAME=$location_id" : "";
	$sql_cond .= ($buyer_id != 0) ? " AND A.BUYER_NAME=$buyer_id" : "";
	$sql_cond .= ($shipment_status != 0) ? " AND B.SHIPING_STATUS=$shipment_status" : "";
	$sql_cond .= ($hide_job_id != "") ? " AND A.ID IN($hide_job_id)" : "";
	$sql_cond .= ($date_from != "") ? " AND E.PRODUCTION_DATE = '$date_from'" : "";

	$sql_mst = "SELECT A.ID,A.JOB_NO,A.COMPANY_NAME,A.BUYER_NAME,A.STYLE_REF_NO,B.PO_QUANTITY,C.OPERATOR_ID,COUNT(C.OPERATION_DATE) as OPERATION_DATE,D.OPERATION_ID,D.RATE,E.SERVING_COMPANY,E.PRODUCTION_DATE,SUM(F.PRODUCTION_QNTY) AS PRODUCTION_QNTY
	FROM WO_PO_DETAILS_MASTER A,WO_PO_BREAK_DOWN B, PRO_LINKING_OPERATION_MST C, PRO_LINKING_OPERATION_DTLS D,PRO_GARMENTS_PRODUCTION_MST E, PRO_GARMENTS_PRODUCTION_DTLS F WHERE C.ID=D.MST_ID AND B.ID=D.ORDER_ID AND A.ID=B.JOB_ID AND E.PO_BREAK_DOWN_ID=B.ID AND E.ID=F.MST_ID
	AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
	AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
	AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 
	AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 
	$sql_cond
	GROUP BY A.ID,A.JOB_NO,A.COMPANY_NAME, A.BUYER_NAME,A.STYLE_REF_NO,B.PO_QUANTITY,C.OPERATOR_ID,D.OPERATION_ID,D.RATE,E.SERVING_COMPANY,E.PRODUCTION_DATE";
	// echo $sql_mst."<br>";
	$result = sql_select($sql_mst);
	$job_operation_arr = array();
	$date_wise_operator_arr = array();
	foreach ($result as $val) {
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["JOB_NO"] = $val["JOB_NO"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["COMPANY_NAME"] = $val["COMPANY_NAME"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["SERVING_COMPANY"] = $val["SERVING_COMPANY"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["BUYER_NAME"] = $val["BUYER_NAME"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["STYLE_REF_NO"] = $val["STYLE_REF_NO"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["OPERATION_ID"] = $val["OPERATION_ID"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["PRODUCTION_DATE"] = $val["PRODUCTION_DATE"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["PRODUCTION_QNTY"] = $val["PRODUCTION_QNTY"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["QTY"] = $val["PO_QUANTITY"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["OPERATOR_ID"] = $val["OPERATOR_ID"];
		$job_operation_arr[$val["JOB_NO"]][$val["OPERATION_ID"]]["WORKING_DAY"] = $val["OPERATION_DATE"];

		$date_wise_operator_arr[$val['PRODUCTION_DATE']] .= $val['OPERATOR_ID']."**";

		$all_style_no[$val["STYLE_REF_NO"]]=$val["STYLE_REF_NO"];
		$all_operation_id[$val["OPERATION_ID"]]=$val["OPERATION_ID"];
		$all_operator_id[$val["OPERATOR_ID"]]=$val["OPERATOR_ID"];
	}
	// echo "<pre>";
	// print_r($job_operation_arr);
	// echo "</pre>";
	$all_style_no=where_con_using_array($all_style_no,1,'A.STYLE_REF');
	$all_operation_id=where_con_using_array($all_operation_id,1,'B.LIB_SEWING_ID');
	$all_operator_id=where_con_using_array($all_operator_id,1,'A.OPERATOR_ID');
	
	// ========================================Working Hour==============================
	$response = file_get_contents('http://182.160.125.188:8081/hrm/api/api_data.php?company_id=1&from_date='.change_date_format($date_from));
    $response = json_decode($response,true);
    // echo "<pre>"; print_r($response);die();
    $api_data_array = array();
    foreach ($response as $att_key => $att_value) 
    {
    	foreach ($att_value as $at_date => $date_value) 
    	{
    		foreach ($date_value as $key => $val) 
    		{
    			$api_data_array[strtotime($at_date)][$val['ID_CARD_NO']] += $val['WORKING_HOURS_WITHOUT_BREAK'];
    		}
    	}
    }
	//  echo "<pre>";print_r($api_data_array);die();
	$date_wise_op_wo_hour = array();
	foreach ($date_wise_operator_arr as $date_key => $op_val) 
    {
    	$ex_op = array_filter(array_unique(explode("**", $op_val)));
    	foreach ($ex_op as $key => $op_id) 
    	{
    		$manpower[$op_id]++;
    		$date_wise_op_wo_hour[$date_key] += $api_data_array[strtotime($date_key)][$op_id];
    		// $date_wise_op_wo_hour2[$date_key][$op_id] += $api_data_array[strtotime($date_key)][$op_id];
    	}
    }
	// echo "<pre>";
	// print_r($manpower);
	// echo "</pre>";
	// =====================================================================================================
	


	foreach ($job_operation_arr as $job_key => $job_data) {
		foreach ($job_data as $operation_key => $operation_data) {
			$job_count[$job_key]++;
			$operation_count[$job_key][$operation_key]++;
		}
	}
	// echo "<pre>";print_r($style_job_array);die();
	// ==================Style wise Data==============
	$sql_style="SELECT A.STYLE_REF,B.LIB_SEWING_ID, B.HELPER_SMV,B.EFFICIENCY,B.TARGET_ON_FULL_PERC,B.TARGET_ON_EFFI_PERC from PPL_GSD_ENTRY_MST A, PPL_GSD_ENTRY_DTLS B WHERE A.ID=B.MST_ID $all_style_no $all_operation_id 
	AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
	AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
	";
	//echo $sql_style;
	$style_result = sql_select($sql_style);
	$style_data_arr=array();
	foreach ($style_result as $val) 
	{
		$style_data_arr[$val["STYLE_REF"]][$val["LIB_SEWING_ID"]]["SMV"]=$val["HELPER_SMV"];
	}

	ob_start();
	if ($type == 1) {
	?>
		<fieldset style="width: 1590px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Company : <?= $company_arr[$company_id]; ?></h2>
				<h2>Style Wise Linking Summary</h2>
				<h2>Date : <?= change_date_format($date_from); ?></h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table" rules="all" width="1570" align="center">
					<thead>
						<tr>
							<th width="30" align="center">Sl</th>
							<th width="120" align="center">Working Compnay</th>
							<th width="80" align="center">Buyer</th>
							<th width="80" align="center">Job No</th>
							<th width="80" align="center">Style</th>
							<th width="80">Order Qty</th>
							<th width="80">Knit Qty</th>
							<th width="80">Operation</th>
							<th width="80">SMV</th>
							<th width="120">Previous Qty</th>
							<th width="100">Today Qty.</th>
							<th width="80">Total Qty.</th>
							<th width="80">Prod. Min</th>
							<th width="80">Work. Min</th>
							<th width="80">Loss Min</th>
							<th width="80">Manpower</th>
							<th width="80">Working Days</th>
							<th width="80">Opt. Eff%</th>
							<th width="80">Style Eff%</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:300px; width:1590px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table" rules="all" width="1570" align="center" id="table_body">
						<tbody>
							<?
							$job_chk = array();
							$operation_chk = array();
							$i = 1;
							foreach ($job_operation_arr as $job_key => $job_data) {
								foreach ($job_data as $operation_key => $row) {
									$job_rowspan = $job_count[$job_key];
									$operation_rowspan = $operation_count[$job_key][$operation_key];
							?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
										<?
										if (!in_array($job_key, $job_chk)) {
											$job_chk[] = $job_key;
										?>
											<td width="30" rowspan="<?= $job_rowspan; ?>" style="vertical-align: middle;text-align: center;"><?= $i; ?></td>
											<td width="120" rowspan="<?= $job_rowspan; ?>" style="vertical-align: middle;text-align: center;"><?= $company_arr[$row["SERVING_COMPANY"]]; ?></td>
											<td width="80" rowspan="<?= $job_rowspan; ?>" style="vertical-align: middle;text-align: center;"><?= $buyer_arr[$row["BUYER_NAME"]]; ?></td>
											<td width="80" rowspan="<?= $job_rowspan; ?>" style="vertical-align: middle;text-align: center;"><?=$job_key;?></td>
											<td width="80" rowspan="<?= $job_rowspan; ?>" style="vertical-align: middle;text-align: center;"><?=$row["STYLE_REF_NO"];?></td>
											<td width="80" rowspan="<?= $job_rowspan; ?>" style="vertical-align: middle;text-align: center;"><?=$row["QTY"];?></td>
											<td width="80" rowspan="<?= $job_rowspan; ?>" style="vertical-align: middle;text-align: center;">&nbsp;</td>
										<?
										} ?>

										<td width="80"><?= $operation_arr[$operation_key]; ?></td>
										<td width="80"><?=$smv=$style_data_arr[$row["STYLE_REF_NO"]][$operation_key]["SMV"];?></td>
										<td width="120">
											<?
											$production_date=$row["PRODUCTION_DATE"];
											if ($production_date<$date_from) {
												echo $previous_qty=$row["PRODUCTION_QNTY"];
											}
											?>
										</td>
										<td width="100"><?
											
											if ($production_date==$date_from) {
												echo $today_qty=$row["PRODUCTION_QNTY"];
											}
											?></td>
										<td width="80"><?=$previous_qty+$today_qty;?></td>
										<td width="80">
											<?=$prod_min=$today_qty*$smv;
											$sum_prod_min+=$prod_min;
											?>
										</td>
										<td width="80">&nbsp;<?=$sum_working_min=0?></td>

										<!-- Loss min Colmun in Future add entry form then add to summary part from Shohel vai -->
										<td width="80"><?=$sum_loss_min=0?></td> 
										<!-- ============================================== -->

										<td width="80"><?=$manpower[$row["OPERATOR_ID"]];?></td>
										<td width="80"><?=$row["WORKING_DAY"];?></td>
										<td width="80"><?=$prod_min/($working_min-$loss_min);?></td>
										<td width="80"><?=$sum_prod_min/($sum_working_min-$sum_loss_min)?></td>
									</tr>
							<?
									$i++;
								}
							}
							?>
							<tr style="background: #cddcdc;font-weight: bold;text-align: right;">
								<td colspan="8"><strong>Style Total:</strong></td>
								<td width="80">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="8" style="text-align: right;"><strong>Grand Total</strong></td>
								<td width="80">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>

							</tr>
						</tfoot>
					</table>
				</div>
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$type####" . implode("__", $style_name_arr) . "####" . implode("__", $style_total_defect) . "####" . implode("__", $style_total_reject);
	exit();
}
