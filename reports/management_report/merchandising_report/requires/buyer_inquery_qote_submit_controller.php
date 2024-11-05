<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Inquery VS Quatation Report

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	01/09/2014
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

include('../../../../includes/common.php');

session_start();
extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$date = date('Y-m-d');
$user_id = $_SESSION['logic_erp']['user_id'];
$buyer_cond = set_user_lavel_filtering(' and buy.id', 'buyer_id');
$company_cond = set_user_lavel_filtering(' and comp.id', 'company_id');

$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$company_short_name_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
$company_team_name_arr = return_library_array("select id,team_name from lib_marketing_team", 'id', 'team_name');
$ffl_merchandiser_arr = return_library_array("select id,team_member_name from  lib_mkt_team_member_info", 'id', 'team_member_name');
$weak_of_year = return_library_array("select week_date,week from  week_of_year", 'week_date', 'week');
$inquery_confirm_date = return_library_array("select a.inquery_id,b.confirm_date from  wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.inquery_id>0", 'inquery_id', 'confirm_date');
$yarn_count_array = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
$lib_buyer_season = return_library_array("Select id, season_name from  lib_buyer_season where  status_active=1", 'id', 'season_name');
//select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name

if ($action == "load_drop_down_buyer") {

	echo create_drop_down("cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/buyer_inquery_qote_submit_controller', this.value, 'load_drop_down_season', 'season_td');", 0);
	exit();
}

if ($action == "load_drop_down_season") {
	echo create_drop_down("cbo_season_id", 60, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "-Season-", "", "");
	exit();
}

if ($action == "get_company_config") {
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=11 and report_id=85 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
	exit();
}


if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$cbo_company_name = str_replace("'", "", $cbo_company_name);
	$txt_inqu_no = str_replace("'", "", $txt_inqu_no);
	$txt_department = str_replace("'", "", $txt_department);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$report_type = str_replace("'", "", $report_type);
	//echo $report_type;
	$pc_current_date = date("Y-m", strtotime($pc_date));

	if ($txt_date_from == "" || $txt_date_to == "") {
		$date_cond = "";
	} else {
		if (str_replace("'", "", $cbo_search_by) == 1) {
			$date_cond = " and inquery_date between '$txt_date_from' and '$txt_date_to'";
		} else {
			$date_cond = " and est_ship_date between '$txt_date_from' and '$txt_date_to'";
		}
	}
	if ($txt_inqu_no != '') $inqu_no_cond = "and buyer_request='$txt_inqu_no'";
	else $inqu_no_cond = "";
	if ($txt_department != '') $department_cond = "and department_name='$txt_department'";
	else $department_cond = "";

	$start_date = return_field_value("min(insert_date)", " wo_quotation_inquery", "status_active=1 and is_deleted=0 and company_id=$cbo_company_name");

	$end_date = date("Y-m-01");


	$start_month = date("Y-m", strtotime($start_date));
	//$end_month=date("Y-m");
	$end_month = date("Y-m");
	$end_date2 = date("Y-m-d");
	if ($db_type == 2) {
		$start_date = change_date_format($start_date, 'yyyy-mm-dd', '-', 1);
		$end_date2 = change_date_format($end_date2, 'yyyy-mm-dd', '-', 1);
	} else if ($db_type == 0) {
	}

	//$diff = abs(strtotime($start_month) - strtotime($end_month));
	//$total_months = floor($diff / (30*60*60*24));
	$total_months = datediff("m", $start_month, $end_month);

	$last_month = date("Y-m", strtotime("+1 Months", strtotime($end_month)));

	$previous_month_year = date("Y-m", strtotime("-1 Months", strtotime($end_month)));
	$array_previous_month_year = explode("-", $previous_month_year);
	$number_of_dayes_prev_moth = cal_days_in_month(CAL_GREGORIAN, $array_previous_month_year[1], $array_previous_month_year[0]);
	$previous_month_end_date = $previous_month_year . "-" . $number_of_dayes_prev_moth;

	if ($db_type == 2) {
		$previous_month_end_date = change_date_format($previous_month_end_date, 'yyyy-mm-dd', '-', 1);
	}

	$month_identify = explode("-", $end_date2);
	$month = $month_identify[1];
	$year = $month_identify[0];
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	//$current_month_end_date=$year."-".$month."-".$num_days;
	//$current_month_end_date=$year."-".$month;
	$current_month_end_date = date("Y-m-d");
	if ($db_type == 2) {
		$current_month_end_date = change_date_format($current_month_end_date, 'yyyy-mm-dd', '-', 1);
	}

	if ($end_date != "") {
		$str_cond = "and insert_date between '$start_date' and '" . $previous_month_end_date . " 11:59:59 PM'";

		$end_date3 = date("Y-m-01", strtotime($end_date2));
		if ($db_type == 2) {
			$end_date3 = change_date_format($end_date3, 'yyyy-mm-dd', '-', 1);
		}
		//$str_cond_curr="and insert_date between '".$end_date3."' and '".$current_month_end_date." 11:59:59 PM'";
		$str_cond_curr = "and inquery_date between '" . $end_date3 . "' and '" . $current_month_end_date . " 11:59:59 PM'"; //by monzu
	} else {
		$str_cond = "";
		$str_cond_curr = "";
	}
	ob_start();
	if ($report_type == 1) {
?>
		<!--=============================================================Total Summary Start=============================================================================================-->
		<div style="width:790px">
			<table width="100%" cellspacing="0">
				<tr>
					<td colspan="7" align="center">
						<font size="3"><strong><?php echo $company_details[$company_name]; ?> </strong> </font>
					</td>
				</tr>
				<tr class="form_caption">
					<td colspan="7" align="center">
						<font size="3"><strong>Total Pending Inquiry Summary </strong></font>
					</td>
				</tr>
			</table>
			<table border="1" rules="all" class="rpt_table" width="800">
				<thead>
					<th width="30">SL</th>
					<th width="220"> Particullars </th>
					<th width="100">No of Inquiry </th>
					<th width="100">No of Buyer</th>
					<th width="120">Submission Pending </th>
					<th>Confirmation Pending</th>
				</thead>

				<?
				//echo "SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond ";
				$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
				$cbo_season_id = str_replace("'", "", $cbo_season_id);
				$hidden_inquery_id = str_replace("'", "", $hidden_inquery_id);
				if ($cbo_buyer_name != 0) $buyer_cond = "and buyer_id='$cbo_buyer_name'";
				else  $buyer_cond = "";
				if ($cbo_season_id != 0) $session_cond = "and season_buyer_wise='$cbo_season_id'";
				else  $session_cond = "";
				if (str_replace("'", "", $txt_inq_no) != "") $inquery_cond = "and system_number_prefix_num=$txt_inq_no";
				else  $inquery_cond = "";
				if (str_replace("'", "", $txt_style_ref) != "") $style_cond = "and style_refernce=$txt_style_ref";
				else  $style_cond = "";

				$sql_summary = sql_select("SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond");
				$inquery_id = "";
				foreach ($sql_summary as $row_summary) {
					if ($inquery_confirm_date[$row_summary[csf("id")]] == '' || $inquery_confirm_date[$row_summary[csf("id")]] == '0000-00-00') {
						if ($inquery_id == "") $inquery_id = $row_summary[csf('id')];
						else $inquery_id = $inquery_id . "," . $row_summary[csf('id')];
						$num_of_buyer[$row_summary[csf('buyer_id')]] = $row_summary[csf('buyer_id')];
						$num_of_inquery[$row_summary[csf('id')]] = $row_summary[csf('id')];
					}
				}
				$sql_summary2 = sql_select("SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond_curr $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond");
				$inquery_id_cur = "";
				foreach ($sql_summary2 as $row_summary2) {
					if ($inquery_confirm_date[$row_summary2[csf("id")]] == '' || $inquery_confirm_date[$row_summary2[csf("id")]] == '0000-00-00') {
						if ($inquery_id_cur == "") $inquery_id_cur = $row_summary2[csf('id')];
						else $inquery_id_cur = $inquery_id_cur . "," . $row_summary2[csf('id')];
						$num_of_buyer_currnet[$row_summary2[csf('buyer_id')]] = $row_summary2[csf('buyer_id')];
						$num_of_inquery_currnet[$row_summary2[csf('id')]] = $row_summary2[csf('id')];
					}
				}
				if ($inquery_id != "") {
					$sql_summary3 = sql_select("SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id)");
				}
				$inquery_id_sub = "";
				foreach ($sql_summary3 as $row) {
					if ($inquery_id_sub == "") $inquery_id_sub = $row[csf('id')];
					else $inquery_id_sub = $inquery_id_sub . "," . $row[csf('id')];
				}
				if ($inquery_id_cur != "") {
					$sql_summary4 = sql_select("SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id_cur)");
				}
				$inquery_id_sub_curr = "";
				foreach ($sql_summary4 as $row) {
					if ($inquery_id_sub_curr == "") $inquery_id_sub_curr = $row[csf('id')];
					else $inquery_id_sub_curr = $inquery_id_sub_curr . "," . $row[csf('id')];
				}


				if ($db_type == 0) $confirm_date_cond = "and confirm_date!='0000-00-00'";
				else if ($db_type == 2) $confirm_date_cond = "and confirm_date IS NOT NULL ";
				if ($inquery_id_sub != "") {
					$sql_summary5 = sql_select("SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub) $confirm_date_cond");
				}
				$inquery_id_con = "";
				foreach ($sql_summary5 as $row) {
					if ($inquery_id_con == "") $inquery_id_con = $row[csf('id')];
					else $inquery_id_con = $inquery_id_con . "," . $row[csf('id')];
				}
				if ($inquery_id_sub_curr != "") {
					$sql_summary6 = sql_select("SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub_curr) $confirm_date_cond");
				}
				$inquery_id_con_curr = "";
				foreach ($sql_summary6 as $row) {
					if ($inquery_id_con_curr == "") $inquery_id_con_curr = $row[csf('id')];
					else $inquery_id_con_curr = $inquery_id_con_curr . "," . $row[csf('id')];
				}


				$curr_month = date("F", strtotime($end_month)) . ", " . date("Y", strtotime($end_month));

				?>
				<tr bgcolor="<? echo "#E9F3FF"; ?>">
					<td rowspan="2" valign="middle" align="center">1</td>
					<td rowspan="2" valign="middle">Previous To Current Month</td>
					<td align="right" rowspan="2" valign="middle"><? $no_inquery = count($num_of_inquery);
																	echo $no_inquery;
																	$summary_grand_total_inquery = $no_inquery; ?></td>
					<td align="right" rowspan="2" valign="middle"><? $no_of_buyer = count($num_of_buyer);
																	echo $no_of_buyer; ?></td>
					<td align="right"><? $no_inquery_sub = (count($num_of_inquery) - count($sql_summary3));
										echo $no_inquery_sub;
										$summary_grand_total_submission = $no_inquery_sub; ?></td>
					<td align="right"><? $no_inquery_confirm = (count($sql_summary3) - count($sql_summary5));
										echo $no_inquery_confirm;
										$summary_grand_total_confirm = $no_inquery_confirm; ?></td>
				</tr>
				<tr>
					<td align="right"><? $sub_percent = ($no_inquery_sub / $no_inquery) * 100;
										echo number_format($sub_percent, 2) . "%"; ?></td>
					<td align="right"><? $confirm_percent = ($no_inquery_confirm / $no_inquery) * 100;
										echo number_format($confirm_percent, 2) . "%"; ?></td>
				</tr>
				<tr bgcolor="<? echo "#FFFFFF"; ?>">
					<td align="center" valign="middle" rowspan="2">2</td>
					<td valign="middle" rowspan="2"> <? echo $curr_month; ?> </td>
					<td align="right" valign="middle" rowspan="2"><? $no_inquery_cur = count($num_of_inquery_currnet);
																	echo $no_inquery_cur;
																	$summary_grand_total_inquery += $no_inquery_cur; ?></td>
					<td align="right" valign="middle" rowspan="2"><? $no_of_buyer_cur = count($num_of_buyer_currnet);
																	echo $no_of_buyer_cur;  ?></td>
					<td align="right"><? $no_inquery_cur_sub = (count($num_of_inquery_currnet) - count($sql_summary4));
										echo $no_inquery_cur_sub;
										$summary_grand_total_submission += $no_inquery_cur_sub; ?></td>
					<td align="right"><? $no_inquery_cur_confirm = (count($sql_summary4) - count($sql_summary6));
										echo $no_inquery_cur_confirm;
										$summary_grand_total_confirm += $no_inquery_cur_confirm; ?></td>
				</tr>
				<tr>
					<td align="right"><? $sub_percent_curr = ($no_inquery_cur_sub / $no_inquery_cur) * 100;
										echo number_format($sub_percent_curr, 2) . "%"; ?></td>
					<td align="right"><? $confirm_percent_curr = ($no_inquery_cur_confirm / $no_inquery_cur) * 100;
										echo number_format($confirm_percent_curr, 2) . "%"; ?></td>
				</tr>
				<tfoot>
					<tr>
						<th colspan="2" align="right">Total</th>
						<th><? echo $summary_grand_total_inquery; ?></th>
						<th><? //echo $summary_grand_total_lc_value; 
							?></th>
						<th><? echo $summary_grand_total_submission; ?></th>
						<th><? echo $summary_grand_total_confirm; ?></th>
					</tr>
					<tr>
						<th colspan="4" align="right">Total Percentage:</th>
						<th align="center"><? $total_percent_sub = ($summary_grand_total_submission / $summary_grand_total_inquery) * 100;
											echo number_format($total_percent_sub, 2) . "%"; ?></th>
						<th align="center"><? $total_percent_confirm = ($summary_grand_total_confirm / $summary_grand_total_inquery) * 100;
											echo number_format($total_percent_confirm, 2) . "%"; ?></th>
					</tr>
				</tfoot>
			</table>
		</div>

		<br />
		<!--=============================================================Total Summary End=============================================================================================-->


		<!--=============================================================Total Current Month Strat=======================================================================================-->
		<fieldset style="width:1250px">
			<legend>Month Wise Total Summary</legend>
			<table width="1200">
				<tr>
					<? $s = 0;
					for ($i = 0; $i <= $total_months; $i++) {
						$last_month = date("Y-m", strtotime("-1 Months", strtotime($last_month)));
						$month_query = $last_month . "-" . "%%";
						//echo $month_query,"jahid";die;
						if ($db_type == 2) {
							if ($i == 0) {
								$month_query_cond_sub = "and b.insert_date between '$month_query_start_date' and '" . $month_query_end_date . " 11:59:59 PM'";
							}
							$month_inquery_id = return_field_value("LISTAGG(CAST(id as varchar(4000)),',')WITHIN GROUP (ORDER BY id) as inquery_id", " wo_quotation_inquery", "status_active=1 and is_deleted=0 and company_id=$cbo_company_name and to_char(insert_date,'YYYY-MM-DD') like '$month_query'", "inquery_id");
							$month_inquery_id = implode(",", array_unique(explode(",", $month_inquery_id)));

							$month_query_cond_sub = "and to_char(b.insert_date,'YYYY-MM-DD') like '$month_query'";
						} else if ($db_type == 0) {
							if ($i == 0) {
								$month_query_cond_sub = "and b.insert_date between '$month_query_start_date' and '" . $month_query_end_date . " 11:59:59 PM'";
							}
							//echo "select group_concat(distinct id) as inquery_id from  wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and insert_date like '$month_query' jahid";
							$month_inquery_id = return_field_value("group_concat(distinct id) as inquery_id", " wo_quotation_inquery", "status_active=1 and is_deleted=0 and company_id=$cbo_company_name and insert_date like '$month_query'", "inquery_id");
							$month_inquery_id = implode(",", array_unique(explode(",", $month_inquery_id)));

							$month_query_cond_sub = "and b.insert_date like '$month_query'";
						}

						if ($i == 0) {
							$month_query_start_date = $last_month . "-01";
							$month_query_end_date = date("d");
							$month_query_end_date = $last_month . "-" . $month_query_end_date;
							if ($db_type == 2) {
								$month_query_start_date = change_date_format($month_query_start_date, 'yyyy-mm-dd', '-', 1);
								$month_query_end_date = change_date_format($month_query_end_date, 'yyyy-mm-dd', '-', 1);
							}
							//$month_query_cond="and insert_date between '$month_query_start_date' and '".$month_query_end_date." 11:59:59 PM'";
							$month_query_cond = "and inquery_date between '$month_query_start_date' and '" . $month_query_end_date . " 11:59:59 PM'"; //by monzu


						} else {
							if ($db_type == 0) {
								//$month_query_cond="and insert_date like '$month_query'";
								$month_query_cond = "and inquery_date like '$month_query'"; //by monzu
							} else {
								//$month_query_cond="and to_char(insert_date,'YYYY-MM-DD') like '$month_query'";
								$month_query_cond = "and to_char(inquery_date,'YYYY-MM-DD') like '$month_query'";
							}
						}
						//echo $month_query_cond."jahid";die;
						if ($month_inquery_id == "") $month_inquery_id = 0;
						if ($db_type == 0) $confirm_date_cond = "and b.confirm_date='0000-00-00'";
						else if ($db_type == 2) $confirm_date_cond = "and b.confirm_date IS NULL ";
						//echo "SELECT count(a.id) as no_quatation_confirm,a.buyer_id from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.inquery_id in($month_inquery_id) $confirm_date_cond group by a.buyer_id jahid";
						$sql_quation_month = sql_select("SELECT count(a.id) as no_quatation_confirm,a.buyer_id from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.inquery_id in($month_inquery_id) $confirm_date_cond group by a.buyer_id");
						foreach ($sql_quation_month as $row) {
							$break_confirm[$row[csf("buyer_id")]][$last_month] = $row[csf("no_quatation_confirm")];
						}
						$sql_month = "SELECT id,buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name $month_query_cond $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond";
						//echo $sql_month."jahud";
						$sql_result = sql_select($sql_month);
						$count = 0;
						foreach ($sql_result as $row) {
							if ($inquery_confirm_date[$row[csf("id")]] == '' || $inquery_confirm_date[$row[csf("id")]] == '0000-00-00') {
								$res_month[$last_month][$row[csf("buyer_id")]][$row[csf("id")]] = $row[csf("id")];
								$count++;
							}
						}
						if ($count > 0) {
							if ($s % 3 == 0) $tr = "</tr><tr>";
							else $tr = "";
							echo $tr;
					?>
							<td valign="top">
								<div style="width:400px">
									<table width="400" cellspacing="0" class="display">
										<tr>
											<td colspan="5" align="center">
												<font size="3"><strong>Total Summary
														<? $month_name = date("F", strtotime($last_month)) . ", " . date("Y", strtotime($last_month));
														echo $month_name;
														?>
													</strong></font>
											</td>
										</tr>
									</table>
									<table width="400" class="rpt_table" border="1" rules="all">
										<thead>
											<th width="50">SL</th>
											<th width="90">Buyer Name</th>
											<th width="90">No Of Inquiry</th>
											<th width="90">Submission Pending</th>
											<th width="90">Confirm Pending</th>
										</thead>
										<?
										$d = 1;
										$tot_po_qnty = 0;
										$tot_po_val = 0;
										foreach ($res_month[$last_month] as $buyer_id => $inq_id) {
											$no_of_inq = count($inq_id);
											$month_sub_pendin = $no_of_inq - $break_confirm[$buyer_id][$last_month];
											$month_confirm_pendin = $break_confirm[$buyer_id][$last_month];
											if ($d % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>">
												<td><? echo $d; ?></td>
												<td>
													<div style="word-wrap:break-word; width:150px"><? echo $buyer_short_name_arr[$buyer_id]; ?></div>
												</td>
												<td align="right"><? echo $no_of_inq;
																	$tot_inqyery_qnty += $no_of_inq; ?></td>
												<td align="right"><? echo number_format($month_sub_pendin, 0);
																	$total_sub_pendin += $month_sub_pendin; ?></td>
												<td align="right"><? echo number_format($month_confirm_pendin, 0);
																	$total_confirm_pendin += $month_confirm_pendin; ?></td>
											</tr>
										<?
											$d++;
										}
										?>
										<tfoot>
											<tr>
												<th colspan="2" align="right">Total</th>
												<th><? echo number_format($tot_inqyery_qnty, 0); ?></th>
												<th><? echo number_format($total_sub_pendin, 0); ?></th>
												<th><? echo number_format($total_confirm_pendin, 0); ?></th>
											</tr>
										</tfoot>
									</table>
								</div>
							</td>
					<?
							$tot_inqyery_qnty = "";
							$total_sub_pendin = "";
							$total_confirm_pendin = "";
							$s++;
						}
					}
					?>
				</tr>
			</table>
		</fieldset>
		<br />
		<div id="detail_report" style="height:auto; width:1780px; margin:0 auto; padding:0;">
			<fieldset>
				<legend> Details Report</legend>
				<!--<div style="width:1670px;">-->
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1780" align="left">
					<tr>
						<td colspan="24" width="1780"><span style="height:15px; width:15px; background-color:#FF3; float:left; margin-left:400px; margin-right:10px;"></span><span style="float:left;">2 Days but quotation not submited</span><span style="height:15px; width:15px; background-color:#FF0000; float:left;  margin-left:10px; margin-right:10px;"></span><span style="float:left;">3 Days but quotation not submited</span><span style="height:15px; width:15px; background-color:#FF0000; float:left;  margin-left:10px; margin-right:10px;"></span><span style="float:left;">1 Day Delay From OPD </span></td>
					</tr>
				</table>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1780" align="left">

					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="60">Month</th>
							<th width="40">Year</th>
							<th width="40">Inq. Id</th>
							<th width="40">Qut. Id</th>
							<th width="70">Season</th>
							<th width="80">Buyer Request No</th>
							<th width="100">Style Name</th>
							<th width="70">M-List</th>
							<th width="100">Customer/BH Merchandiser</th>
							<th width="100">Department/ Merchandiser</th>
							<th width="60">Status</th>
							<th width="65">Status Date</th>
							<th width="65">Insert Date</th>
							<th width="65">OPD Date</th>
							<th width="65">Request Receive Date</th>
							<th width="60">Days To Sub.</th>
							<th width="60">Delay To Conf.</th>
							<th width="70">Approx Forecast Qty</th>
							<th width="50">TOD</th>
							<th width="150">Fabrication & Composition</th>
							<th width="70">Yarn Count</th>
							<th width="80">Quoted Price</th>
							<th width="80">Target Price</th>
							<th>Remarks</th>
						</tr>
					</thead>
				</table>
				<!--        </div>
-->
				<div style="width:1780px; max-height:410px; overflow-y: scroll;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" width="1762" border="1" rules="all" class="rpt_table" id="tbl_ship_pending">
						<tbody>
							<?
							if ($db_type == 0) $confirm_date_cond = "and b.confirm_date!='0000-00-00'";
							else if ($db_type == 2) $confirm_date_cond = "and b.confirm_date IS NOT NULL ";


							$sql_quatation = sql_select("SELECT id, inquery_id, product_code, m_list_no, bh_marchant, company_id, buyer_id, quot_date, est_ship_date,offer_qnty
			from  wo_price_quotation
			where company_id =$cbo_company_name and status_active=1 and is_deleted=0 and inquery_id>0");

							foreach ($sql_quatation as $row) {
								$quatation_mst_arr[$row[csf("inquery_id")]]['inquery_id'] = $row[csf("inquery_id")];
								$quatation_mst_arr[$row[csf("inquery_id")]]['id'] = $row[csf("id")];
								$quatation_mst_arr[$row[csf("inquery_id")]]['product_code'] = $row[csf("product_code")];
								$quatation_mst_arr[$row[csf("inquery_id")]]['m_list_no'] = $row[csf("m_list_no")];
								$quatation_mst_arr[$row[csf("inquery_id")]]['bh_marchant'] = $row[csf("bh_marchant")];
								$quatation_mst_arr[$row[csf("inquery_id")]]['quot_date'] = $row[csf("quot_date")];
								$quatation_mst_arr[$row[csf("inquery_id")]]['est_ship_date'] = $row[csf("est_ship_date")];
								$quatation_mst_arr[$row[csf("inquery_id")]]['offer_qnty'] = $row[csf("offer_qnty")];
							}
							//var_dump($quatation_mst_arr);

							$sql_quatation_costing = sql_select("SELECT a.id as quation_id,a.op_date, a.inquery_id, b.id as quation_costing_id, b.a1st_quoted_price, b.a1st_quoted_price_date, b.revised_price, b.revised_price_date, b.confirm_price,b.terget_qty,b.confirm_date
			from  wo_price_quotation a, wo_price_quotation_costing_mst b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.inquery_id>0");

							foreach ($sql_quatation_costing as $row) {
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['inquery_id'] = $row[csf("inquery_id")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['quation_id'] = $row[csf("quation_id")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['quation_costing_id'] = $row[csf("quation_costing_id")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['a1st_quoted_price'] = $row[csf("a1st_quoted_price")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['a1st_quoted_price_date'] = $row[csf("a1st_quoted_price_date")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['revised_price'] = $row[csf("revised_price")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['revised_price_date'] = $row[csf("revised_price_date")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['confirm_price'] = $row[csf("confirm_price")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['terget_qty'] = $row[csf("terget_qty")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['confirm_date'] = $row[csf("confirm_date")];
								$quatation_costing_mst_arr[$row[csf("inquery_id")]]['op_date'] = $row[csf("op_date")];
							}
							//var_dump($quatation_costing_mst_arr);
							$sql_fabrication_composition = sql_select("SELECT a.id as quation_id, a.inquery_id, b.construction, b.composition
			from  wo_price_quotation a, wo_pri_quo_fabric_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0");

							foreach ($sql_fabrication_composition as $row) {
								$fabrication_composition[$row[csf("inquery_id")]] .= $row[csf("construction")] . " " . $row[csf("composition")] . ",";
							}

							if ($db_type == 2) {
								$sql_yarn_count = sql_select("SELECT  a.inquery_id, LISTAGG(CAST(b.count_id as varchar(4000)),',')WITHIN GROUP (ORDER BY b.count_id) as count_id
			from  wo_price_quotation a, wo_pri_quo_fab_yarn_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by a.inquery_id");
							} else if ($db_type == 0) {
								$sql_yarn_count = sql_select("SELECT  a.inquery_id, group_concat(distinct b.count_id ) as count_id
			from  wo_price_quotation a, wo_pri_quo_fab_yarn_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by a.inquery_id");
							}
							//echo $sql_yarn_count;die;
							foreach ($sql_yarn_count as $row) {
								$count_id_arr[$row[csf("inquery_id")]] = implode(",", array_unique(explode(",", $row[csf("count_id")])));
							}



							if ($start_date != "") {
								$str_cond3 = "and insert_date between '$start_date' and '$current_month_end_date 11:59:59 PM' ";
							} else {
								$str_cond3 = "";
							}
							$i = 1;
							$sql_inquery = "SELECT id, system_number_prefix_num, company_id, buyer_id, season, inquery_date, buyer_request, remarks,dealing_marchant, style_refernce, insert_date,season_buyer_wise
			from  wo_quotation_inquery
			where company_id =$cbo_company_name and status_active=1 and is_deleted=0  $str_cond3 $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond order by inquery_date DESC";
							//echo $sql_inquery;
							$sql_result = sql_select($sql_inquery);
							foreach ($sql_result as $row) {
								if ($inquery_confirm_date[$row[csf("id")]] == '' || $inquery_confirm_date[$row[csf("id")]] == '0000-00-00') {
									$insert_datetime = explode(' ', $row[csf("insert_date")]);
									$insert_date = $insert_datetime[0];
									$insert_date = date('d-m-Y', strtotime($insert_date));
									$inquery_date = date('d-m-Y', strtotime($row[csf('inquery_date')]));
									$op_date = $quatation_costing_mst_arr[$row[csf("id")]]['op_date'];
									//$quot_op_date=date('d-m-Y',strtotime($op_date));
									//echo $price_op_date.'='.$inquery_date;
									if ($op_date != '0000-00-00' && $op_date != '') {
										$quot_op_date = date('d-m-Y', strtotime($op_date));
									} else {
										$quot_op_date = '';
									}
									$opd_dateToDelay = datediff('d', $insert_date, $quot_op_date);
									//echo $opd_dateToDelay.'d';
									if ($opd_dateToDelay > 1) {
										$td_color_opd = "red";
									} else {
										$td_color_opd = "";
									}

									if ($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '0000-00-00' && $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '') {
										$con_sub_date = change_date_format($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date']);
										//echo $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'];
									} else {
										if ($quatation_mst_arr[$row[csf("id")]]['inquery_id'] != 0) {
											$con_sub_date = change_date_format($quatation_mst_arr[$row[csf("id")]]['quot_date']);

											$dateToSub = datediff('d', $row[csf('inquery_date')], $quatation_mst_arr[$row[csf("id")]]['quot_date']);
											//$dateToSub=datediff( 'd', $inquery_date, $insert_date);
											$dateToConf = datediff('d', $quatation_mst_arr[$row[csf("id")]]['quot_date'], $pc_date_time);
										} else {
											$dateToSub = datediff('d', $row[csf('inquery_date')], $pc_date_time);
											//$dateToSub=datediff( 'd',  $inquery_date, $insert_date);
											$dateToConf = datediff('d', $row[csf('inquery_date')], $pc_date_time);
											//echo change_date_format($row[csf('inquery_date')]);
											//echo $pc_date_time;
										}
									}

									//$month_year=date("Y-m",strtotime($row[csf("insert_date")]));

									$month_year = date("Y-m", strtotime($row[csf("inquery_date")])); //by monzu
									$month_year_arr = (explode("-", $month_year));
									$month_val = ($month_year_arr[1] * 1);
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
							?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i; ?></td>
										<td width="60" align="center">
											<p><? echo $months[$month_val]; ?></p>
										</td>
										<td width="40" align="center">
											<p><? echo $month_year_arr[0]; ?></p>
										</td>
										<td width="40" align="center">
											<p><? echo $row[csf("system_number_prefix_num")]; ?></p>
										</td>
										<td width="40" align="center">
											<p><? echo $quatation_mst_arr[$row[csf("id")]]['id']; ?></p>
										</td>
										<td width="70">
											<p><? echo $lib_buyer_season[$row[csf('season_buyer_wise')]]; ?></p>
										</td>
										<td width="80">
											<p><? echo $row[csf('buyer_request')]; ?></p>
										</td>
										<td width="100">
											<p><? echo $row[csf('style_refernce')]; ?></p>
										</td>
										<td width="70">
											<p><? echo $quatation_mst_arr[$row[csf("id")]]['m_list_no']; ?></p>
										</td>
										<td width="100">
											<p><? echo $buyer_short_name_arr[$row[csf('buyer_id')]] . "<br>" . $quatation_mst_arr[$row[csf("id")]]['bh_marchant']; ?></p>
										</td>
										<td width="100">
											<p><? echo $quatation_mst_arr[$row[csf("id")]]['product_code'] . "<br>" . $ffl_merchandiser_arr[$row[csf("dealing_marchant")]]; ?></p>
										</td>
										<?
										if ($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '0000-00-00' && $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '') {
											$status = "Confirmed";
											//echo $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'];
										} else {
											if ($quatation_mst_arr[$row[csf("id")]]['inquery_id'] != 0) $status = "Submitted";
											else $status = "Not Submitted";
										}
										if ($status == 'Submitted') {
										?>
											<td width="60" align="center">
												<p><? echo $status; ?></p>
											</td>
											<?
										} else if ($status == 'Not Submitted') {
											if ($dateToSub > 2) {
											?>
												<td width="60" align="center" bgcolor="#FF0000">
													<p><? echo $status; ?></p>
												</td>
											<?
											} else {
											?>
												<td width="60" align="center" bgcolor="#FFFF00">
													<p><? echo $status; ?></p>
												</td>
										<?
											}
										}
										?>
										<td width="65">
											<p><? echo $con_sub_date; ?></p>
										<td width="65">
											<p><? echo $insert_date; ?></p>
										<td width="65" bgcolor="<? echo $td_color_opd; ?>">
											<p><? if ($op_date != '0000-00-00' && $op_date != '') {
													echo $quot_op_date;
												} ?></p>
										</td>
										<td align="center" width="65">
											<p><? if ($row[csf('inquery_date')] != '0000-00-00' && $row[csf('inquery_date')] != "")  echo change_date_format($row[csf('inquery_date')]);  ?></p>
										</td>
										<td width="60" align="center">
											<p><? echo $dateToSub . " Days"; ?></p>
										</td>
										<td width="60" align="center">
											<p><? echo $dateToConf . " Days"; ?></p>
										</td>
										<td align="right" width="70">
											<p><? echo number_format($quatation_mst_arr[$row[csf("id")]]['offer_qnty'], 0, "", "");
												$total_offer_qty += $quatation_mst_arr[$row[csf("id")]]['offer_qnty']; ?></p>
										</td>
										<td width="50">
											<p><? if ($weak_of_year[$quatation_mst_arr[$row[csf("id")]]['est_ship_date']] != "") echo "W " . $weak_of_year[$quatation_mst_arr[$row[csf("id")]]['est_ship_date']]; ?></p>
										</td>
										<td width="150">
											<p>
												<?
												$fabrication_all = "";
												$k = 1;
												$fabrication_arr = array_filter(explode(",", substr($fabrication_composition[$row[csf("id")]], 0, -1)));

												if (count($fabrication_arr) > 0) {
													foreach ($fabrication_arr as $val) {
														if ($fabrication_all != "") $fabrication_all .= "<br>";
														$fabrication_all .= $k . ". " . $val;
														$k++;
													}
												}
												echo $fabrication_all;
												?>
											</p>
										</td>
										<td width="70">
											<p>
												<?
												$yarn_count_all = "";
												$yarn_count_arr_all = explode(",", $count_id_arr[$row[csf("id")]]);
												foreach ($yarn_count_arr_all as $yarn_id) {
													if ($yarn_count_all != "") $yarn_count_all .= ", ";
													$yarn_count_all .= $yarn_count_array[$yarn_id];
												}
												echo $yarn_count_all;
												?>
											</p>
										</td>
										<?
										if ($quatation_costing_mst_arr[$row[csf("id")]]['confirm_price'] != 0) {
											$quted_price = $quatation_costing_mst_arr[$row[csf("inquery_id")]]['confirm_price'];
										} else {
											if ($quatation_costing_mst_arr[$row[csf("id")]]['revised_price'] != 0) {
												$quted_price = $quatation_costing_mst_arr[$row[csf("id")]]['revised_price'];
											} else {
												$quted_price = $quatation_costing_mst_arr[$row[csf("id")]]['a1st_quoted_price'];
											}
										}
										if ($quted_price < $quatation_costing_mst_arr[$row[csf("id")]]['terget_qty']) {
										?>
											<td width="80" align="right" bgcolor="#FFFF00">
												<p><? echo number_format($quted_price, 2);
													$total_qtd_price += $quted_price; ?> </p>
											</td>
										<?
										} else {
										?>
											<td width="80" align="right">
												<p><? echo number_format($quted_price, 2);
													$total_qtd_price += $quted_price; ?></p>
											</td>
										<?
										}
										?>
										<td align="right" width="80" style="padding-right:3px;">
											<p><? echo number_format($quatation_costing_mst_arr[$row[csf("id")]]['terget_qty'], 2);
												$total_target_qty += $quatation_costing_mst_arr[$row[csf("id")]]['terget_qty']; ?></p>
										</td>
										<td>
											<p><? echo $row[csf("remarks")]; ?></p>
										</td>
									</tr>
							<?
									$i++;
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="18" align="right">Grand Total</th>
								<th align="right"><? echo  number_format($total_offer_qty, 0); ?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th align="right"><? echo number_format($total_qtd_price, 2); ?></th>
								<th align="right"><? echo number_format($total_target_qty, 2); ?></th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>

	<?
	} else if ($report_type == 2) //Report 2 Start...
	{
		$imge_arr = return_library_array("select master_tble_id, image_location from  common_photo_library where file_type=1", 'master_tble_id', 'image_location');
	?>
		<!--=============================================================Total Summary Start=============================================================================================-->
		<div style="width:790px">


			<?
			//echo "SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond ";
			$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
			$cbo_season_id = str_replace("'", "", $cbo_season_id);
			$hidden_inquery_id = str_replace("'", "", $hidden_inquery_id);
			if ($cbo_buyer_name != 0) $buyer_cond = "and buyer_id='$cbo_buyer_name'";
			else  $buyer_cond = "";
			if ($cbo_season_id != 0) $session_cond = "and season_buyer_wise='$cbo_season_id'";
			else  $session_cond = "";
			if (str_replace("'", "", $txt_inq_no) != "") $inquery_cond = "and system_number_prefix_num=$txt_inq_no";
			else  $inquery_cond = "";
			if (str_replace("'", "", $txt_style_ref) != "") $style_cond = "and style_refernce=$txt_style_ref";
			else  $style_cond = "";

			$sql_summary = sql_select("SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond");
			$inquery_id = "";
			foreach ($sql_summary as $row_summary) {
				if ($inquery_confirm_date[$row_summary[csf("id")]] == '' || $inquery_confirm_date[$row_summary[csf("id")]] == '0000-00-00') {
					if ($inquery_id == "") $inquery_id = $row_summary[csf('id')];
					else $inquery_id = $inquery_id . "," . $row_summary[csf('id')];
					$num_of_buyer[$row_summary[csf('buyer_id')]] = $row_summary[csf('buyer_id')];
					$num_of_inquery[$row_summary[csf('id')]] = $row_summary[csf('id')];
				}
			}
			//echo "SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond_curr";
			$sql_summary2 = sql_select("SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond_curr $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond");
			$inquery_id_cur = "";
			foreach ($sql_summary2 as $row_summary2) {
				if ($inquery_confirm_date[$row_summary2[csf("id")]] == '' || $inquery_confirm_date[$row_summary2[csf("id")]] == '0000-00-00') {
					if ($inquery_id_cur == "") $inquery_id_cur = $row_summary2[csf('id')];
					else $inquery_id_cur = $inquery_id_cur . "," . $row_summary2[csf('id')];
					$num_of_buyer_currnet[$row_summary2[csf('buyer_id')]] = $row_summary2[csf('buyer_id')];
					$num_of_inquery_currnet[$row_summary2[csf('id')]] = $row_summary2[csf('id')];
				}
			}
			//echo count($num_of_inquery_currnet);die;
			//echo "SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id)";
			if ($inquery_id != "") {
				$sql_summary3 = sql_select("SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id)");
			}
			$inquery_id_sub = "";
			foreach ($sql_summary3 as $row) {
				if ($inquery_id_sub == "") $inquery_id_sub = $row[csf('id')];
				else $inquery_id_sub = $inquery_id_sub . "," . $row[csf('id')];
			}
			//echo "SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id_cur)";
			if ($inquery_id_cur != "") {
				$sql_summary4 = sql_select("SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id_cur)");
			}
			$inquery_id_sub_curr = "";
			foreach ($sql_summary4 as $row) {
				if ($inquery_id_sub_curr == "") $inquery_id_sub_curr = $row[csf('id')];
				else $inquery_id_sub_curr = $inquery_id_sub_curr . "," . $row[csf('id')];
			}


			if ($db_type == 0) $confirm_date_cond = "and confirm_date!='0000-00-00'";
			else if ($db_type == 2) $confirm_date_cond = "and confirm_date IS NOT NULL ";
			if ($inquery_id_sub != "") {
				//echo "SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub) $confirm_date_cond";
				$sql_summary5 = sql_select("SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub) $confirm_date_cond");
			}
			$inquery_id_con = "";
			foreach ($sql_summary5 as $row) {
				if ($inquery_id_con == "") $inquery_id_con = $row[csf('id')];
				else $inquery_id_con = $inquery_id_con . "," . $row[csf('id')];
			}
			//echo "SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id_cur)";
			if ($inquery_id_sub_curr != "") {
				//echo "SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub_curr) $confirm_date_cond";
				$sql_summary6 = sql_select("SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub_curr) $confirm_date_cond");
			}
			$inquery_id_con_curr = "";
			foreach ($sql_summary6 as $row) {
				if ($inquery_id_con_curr == "") $inquery_id_con_curr = $row[csf('id')];
				else $inquery_id_con_curr = $inquery_id_con_curr . "," . $row[csf('id')];
			}


			$curr_month = date("F", strtotime($end_month)) . ", " . date("Y", strtotime($end_month));
			//wo_price_quotation_costing_mst  wo_price_quotation
			?>

		</div>

		<!--=============================================================Total Summary End=============================================================================================-->


		<!--=============================================================Total Current Month Strat=======================================================================================-->

		<div id="detail_report" style="height:auto; width:1750px; margin:0 auto; padding:0;">

			<legend> Details Report <? echo $report_type; ?></legend>
			<!--<div style="width:1670px;">-->
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1750" align="left">
				<tr>
					<td colspan="21" width="1750"><span style="height:15px; width:15px; background-color:#FF3; float:left; margin-left:400px; margin-right:10px;"></span><span style="float:left;">2 Days but quotation not submited</span><span style="height:15px; width:15px; background-color:#FF0000; float:left;  margin-left:10px; margin-right:10px;"></span><span style="float:left;">3 Days but quotation not submited</span></td>
				</tr>
			</table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1750" align="left">

				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="60">Month</th>
						<th width="40">Year</th>
						<th width="80">Buyer Name</th>
						<th width="60">Buyer Inquiry No</th>
						<th width="80">Department</th>
						<th width="100">Style Name</th>
						<th width="80">Image</th>
						<th width="60">File</th>
						<th width="70">Body Color</th>
						<th width="150">Fabric. & Composition</th>
						<th width="100">Department/ Merchandiser</th>
						<th width="60">Status</th>
						<th width="70">Status Date</th>
						<th width="60">Days To Sub.</th>
						<th width="60">Delay To Conf.</th>
						<th width="70">Inquire R/C Date</th>
						<th width="70">Bulk Offer Qty</th>
						<th width="70">Bulk Est. Ship Date</th>
						<th width="70">Actual Quot Date</th>
						<th width="70"> am. Sub Date</th>
						<th width="70">Buyer Target Price</th>
						<th width="70">Buyer Submit Price</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table>
			<!--        </div>
-->
			<div style="width:1770px; max-height:410px; overflow-y: scroll;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" width="1750" border="1" rules="all" class="rpt_table" id="tbl_ship_pending">
					<tbody>
						<?
						if ($db_type == 0) $confirm_date_cond = "and b.confirm_date!='0000-00-00'";
						else if ($db_type == 2) $confirm_date_cond = "and b.confirm_date IS NOT NULL ";


						$sql_quatation = sql_select("SELECT id, inquery_id, product_code, m_list_no, bh_marchant, company_id, buyer_id, quot_date, est_ship_date,offer_qnty
			from  wo_price_quotation
			where company_id =$cbo_company_name and status_active=1 and is_deleted=0 and inquery_id>0");

						foreach ($sql_quatation as $row) {
							$quatation_mst_arr[$row[csf("inquery_id")]]['inquery_id'] = $row[csf("inquery_id")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['id'] = $row[csf("id")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['product_code'] = $row[csf("product_code")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['m_list_no'] = $row[csf("m_list_no")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['bh_marchant'] = $row[csf("bh_marchant")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['quot_date'] = $row[csf("quot_date")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['est_ship_date'] = $row[csf("est_ship_date")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['offer_qnty'] = $row[csf("offer_qnty")];
						}
						//var_dump($quatation_mst_arr);

						$sql_quatation_costing = sql_select("SELECT a.id as quation_id, a.inquery_id, b.id as quation_costing_id, b.a1st_quoted_price, b.a1st_quoted_price_date, b.revised_price, b.revised_price_date, b.confirm_price,b.terget_qty,b.confirm_date
			from  wo_price_quotation a, wo_price_quotation_costing_mst b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.inquery_id>0");

						foreach ($sql_quatation_costing as $row) {
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['inquery_id'] = $row[csf("inquery_id")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['quation_id'] = $row[csf("quation_id")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['quation_costing_id'] = $row[csf("quation_costing_id")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['a1st_quoted_price'] = $row[csf("a1st_quoted_price")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['a1st_quoted_price_date'] = $row[csf("a1st_quoted_price_date")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['revised_price'] = $row[csf("revised_price")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['revised_price_date'] = $row[csf("revised_price_date")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['confirm_price'] = $row[csf("confirm_price")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['terget_qty'] = $row[csf("terget_qty")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['confirm_date'] = $row[csf("confirm_date")];
						}
						$data_img = sql_select("select file_type,image_location,master_tble_id  from common_photo_library  where   form_name='quotation_inquery' and is_deleted=0 and file_type in(1,2)");
						$system_img_arr = array();
						foreach ($data_img as $row) {
							if ($row[csf('file_type')] == 1) {
								$system_img_arr[$row[csf('master_tble_id')]]['img'] = $row[csf('image_location')];
							} else if ($row[csf('file_type')] == 2) {
								$system_img_arr[$row[csf('master_tble_id')]]['file'] = $row[csf('image_location')];
							}
						}
						//var_dump($quatation_costing_mst_arr);
						$sql_fabrication_composition = sql_select("SELECT a.id as quation_id, a.inquery_id, b.construction, b.composition
			from  wo_price_quotation a, wo_pri_quo_fabric_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0");

						foreach ($sql_fabrication_composition as $row) {
							$fabrication_composition[$row[csf("inquery_id")]] .= $row[csf("construction")] . " " . $row[csf("composition")] . ",";
						}

						if ($db_type == 2) {
							$sql_yarn_count = sql_select("SELECT  a.inquery_id, LISTAGG(CAST(b.count_id as varchar(4000)),',')WITHIN GROUP (ORDER BY b.count_id) as count_id
			from  wo_price_quotation a, wo_pri_quo_fab_yarn_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by a.inquery_id");
						} else if ($db_type == 0) {
							$sql_yarn_count = sql_select("SELECT  a.inquery_id, group_concat(distinct b.count_id ) as count_id
			from  wo_price_quotation a, wo_pri_quo_fab_yarn_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by a.inquery_id");
						}
						//echo $sql_yarn_count;die;
						foreach ($sql_yarn_count as $row) {
							$count_id_arr[$row[csf("inquery_id")]] = implode(",", array_unique(explode(",", $row[csf("count_id")])));
						}

						if ($start_date != "") {
							$str_cond3 = "and insert_date between '$start_date' and '$current_month_end_date 11:59:59 PM' ";
						} else {
							$str_cond3 = "";
						}
						$i = 1;
						$sql_inquery = "SELECT id, system_number_prefix_num, system_number,buyer_request, company_id, buyer_id, season_buyer_wise as season, inquery_date, buyer_request, buyer_id, department_name, offer_qty, remarks, dealing_marchant, style_refernce, actual_sam_send_date, color, fabrication, actual_req_quot_date, req_quotation_date, target_sam_sub_date, insert_date, est_ship_date, buyer_target_price, buyer_submit_price
			from  wo_quotation_inquery
			where company_id =$cbo_company_name and status_active=1 and is_deleted=0  $str_cond3 $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond order by inquery_date DESC";
						//echo $sql_inquery;
						$sql_result = sql_select($sql_inquery);
						$total_buyer_target_price = 0;
						$total_buyer_submit_price = 0;
						foreach ($sql_result as $row) {

							if ($inquery_confirm_date[$row[csf("id")]] == '' || $inquery_confirm_date[$row[csf("id")]] == '0000-00-00') {
								if ($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '0000-00-00' && $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '') {
									$con_sub_date = change_date_format($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date']);
									//echo $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'];
								} else {
									if ($quatation_mst_arr[$row[csf("id")]]['inquery_id'] != 0) {
										$con_sub_date = change_date_format($quatation_mst_arr[$row[csf("id")]]['quot_date']);

										$dateToSub = datediff('d', $row[csf('inquery_date')], $quatation_mst_arr[$row[csf("id")]]['quot_date']);
										$dateToConf = datediff('d', $quatation_mst_arr[$row[csf("id")]]['quot_date'], $pc_date_time);
									} else {
										$dateToSub = datediff('d', $row[csf('inquery_date')], $pc_date_time);
										$dateToConf = datediff('d', $row[csf('inquery_date')], $pc_date_time);
										//echo change_date_format($row[csf('inquery_date')]);
										//echo $pc_date_time;
									}
								}

								//$month_year=date("Y-m",strtotime($row[csf("insert_date")]));
								$month_year = date("Y-m", strtotime($row[csf("inquery_date")])); //by monzu
								$month_year_arr = (explode("-", $month_year));
								$month_val = ($month_year_arr[1] * 1);
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$fabrication = explode(",", $row[csf('fabrication')]);
								$fabrics_type = '';
								foreach ($fabrication as $fab_data) {
									$fab_des = explode("_", $fab_data);
									if ($fabrics_type != '') {
										$fabrics_type .= "," . $fab_des[0] . ' ' . $fab_des[1];
									} else {
										$fabrics_type = $fab_des[0] . ' ' . $fab_des[1];
									}
									//	print_r($fab_data);

								}
								$img_name = $system_img_arr[$row[csf('id')]]['img'];
								$file_name = $system_img_arr[$row[csf('id')]]['file'];

						?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60" align="center">
										<p><? echo $months[$month_val]; ?></p>
									</td>
									<td width="40" align="center">
										<p><? echo $month_year_arr[0]; ?></p>
									</td>
									<td width="80" align="center">
										<p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p>
									</td>
									<td width="60" title="<? echo $row[csf('id')]; ?>" align="center">
										<p><? echo $row[csf("buyer_request")]; ?></p>
									</td>
									<td width="80" align="center">
										<p><? echo $row[csf("department_name")]; ?></p>
									</td>
									<td width="100" align="center">
										<p><? echo $row[csf('style_refernce')]; //$quatation_mst_arr[$row[csf("id")]]['id']; 
											?></p>
									</td>
									<td width="80" onclick="openmypage_image('requires/buyer_inquery_qote_submit_controller.php?action=show_image&sys_id=<? echo $row[csf("id")] ?>','Image View')">
										<p>
											<? if ($img_name != '') {
											?>
												<img src='../../../<? echo $imge_arr[$row[csf('id')]]; ?>' height='60' width='80' />
											<?
											} else {
												echo '&nbsp;';
											}
											?>
										</p>

									</td>
									<td width="60">
										<p>
											<?
											if ($file_name != "") {
											?>
												<input type="button" class="image_uploader" id="fileno_<? echo $i; ?>" style="width:40px" value="File" onClick="openmypage_file('requires/buyer_inquery_qote_submit_controller.php?action=show_file&sys_id=<? echo $row[csf("id")] ?>','File View')" />
											<?
											}
											?>
										</p>
									</td>
									<td width="70" align="center">
										<p><? echo $row[csf("color")]; ?></p>
									</td>
									<td width="150" align="center">
										<p><? echo $fabrics_type; ?></p>
									</td>

									<td width="100">
										<p><? echo $ffl_merchandiser_arr[$row[csf("dealing_marchant")]]; ?></p>
									</td>

									<?
									if ($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '0000-00-00' && $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '') {
										$status = "Confirmed";
										echo $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'];
									} else //$row[csf("actual_sam_send_date")];
									{
										if ($row[csf("actual_sam_send_date")] != '0000-00-00' && $row[csf("actual_sam_send_date")] != '') $status = "Submitted";
										else $status = "Not Submitted";
									}
									if ($status == 'Submitted') {
									?>
										<td width="60" align="center">
											<p><? echo $status; ?></p>
										</td>
										<?
									} else if ($status == 'Not Submitted') {
										if ($dateToSub > 2) {
										?>
											<td width="60" align="center" bgcolor="#FF0000">
												<p><? echo $status; ?></p>
											</td>
										<?
										} else {
										?>
											<td width="60" align="center" bgcolor="#FFFF00">
												<p><? echo $status; ?></p>
											</td>
									<?
										}
									}
									?>
									<td width="70">
										<p><? echo change_date_format($row[csf("actual_sam_send_date")]); ?></p>
									</td>


									<td width="60">
										<p><? echo  $dateToSub . " Days"; ?></p>
									</td>

									<td width="60">
										<p><? echo $dateToConf . " Days"; //$con_sub_date; 
											?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("req_quotation_date")]);  ?></p>
									</td>
									<td width="70" align="right">
										<p><? echo $row[csf("offer_qty")]; //number_format($quatation_mst_arr[$row[csf("id")]]['offer_qnty'],0,"","");
											$total_offer_qty += $row[csf("offer_qty")]; //$quatation_mst_arr[$row[csf("id")]]['offer_qnty'];//$$row[csf("offer_qty")]; 
											?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("est_ship_date")]);  ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("actual_req_quot_date")]); ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("target_sam_sub_date")]); ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo $row[csf("buyer_target_price")]; ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo $row[csf("buyer_submit_price")]; ?></p>
									</td>


									<td>
										<p><? echo $row[csf("remarks")]; ?></p>
									</td>
								</tr>
						<?
								$i++;
								$total_buyer_submit_price += $row[csf("buyer_submit_price")];
								$total_buyer_target_price += $row[csf("buyer_target_price")];
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="17" align="right">Grand Total</th>
							<th align="right"><? echo  number_format($total_offer_qty, 0); ?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>


							<th><? echo  number_format($total_buyer_target_price, 0); ?></th>
							<th><? echo  number_format($total_buyer_submit_price, 0); ?></th>
							<th>&nbsp;</th>

						</tr>
					</tfoot>
				</table>
			</div>

		</div>
	<?
	} else if ($report_type == 3) //Report 3 Start...
	{
		$imge_arr = return_library_array("select master_tble_id, image_location from  common_photo_library where file_type=1", 'master_tble_id', 'image_location');
		$fabrication_library = return_library_array("select id,CONSTRUCTION from lib_yarn_count_determina_mst", "id", "CONSTRUCTION");
		$gsm_arr = return_library_array("select id,GSM_WEIGHT from lib_yarn_count_determina_mst", "id", "GSM_WEIGHT");
	?>
		<!--=============================================================Total Summary Start=============================================================================================-->
		<div style="width:790px">


			<?
			$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
			$cbo_season_id = str_replace("'", "", $cbo_season_id);
			$hidden_inquery_id = str_replace("'", "", $hidden_inquery_id);
			if ($cbo_buyer_name != 0) $buyer_cond = "and buyer_id='$cbo_buyer_name'";
			else  $buyer_cond = "";
			if ($cbo_season_id != 0) $session_cond = "and season_buyer_wise='$cbo_season_id'";
			else  $session_cond = "";
			if (str_replace("'", "", $txt_inq_no) != "") $inquery_cond = "and system_number_prefix_num=$txt_inq_no";
			else  $inquery_cond = "";
			if (str_replace("'", "", $txt_style_ref) != "") $style_cond = "and style_refernce=$txt_style_ref";
			else  $style_cond = "";

			$sql_summary = sql_select("SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond");
			$inquery_id = "";
			foreach ($sql_summary as $row_summary) {
				if ($inquery_confirm_date[$row_summary[csf("id")]] == '' || $inquery_confirm_date[$row_summary[csf("id")]] == '0000-00-00') {
					if ($inquery_id == "") $inquery_id = $row_summary[csf('id')];
					else $inquery_id = $inquery_id . "," . $row_summary[csf('id')];
					$num_of_buyer[$row_summary[csf('buyer_id')]] = $row_summary[csf('buyer_id')];
					$num_of_inquery[$row_summary[csf('id')]] = $row_summary[csf('id')];
				}
			}
			//echo "SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond_curr";
			$sql_summary2 = sql_select("SELECT id, buyer_id from wo_quotation_inquery where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $str_cond_curr $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond");
			$inquery_id_cur = "";
			foreach ($sql_summary2 as $row_summary2) {
				if ($inquery_confirm_date[$row_summary2[csf("id")]] == '' || $inquery_confirm_date[$row_summary2[csf("id")]] == '0000-00-00') {
					if ($inquery_id_cur == "") $inquery_id_cur = $row_summary2[csf('id')];
					else $inquery_id_cur = $inquery_id_cur . "," . $row_summary2[csf('id')];
					$num_of_buyer_currnet[$row_summary2[csf('buyer_id')]] = $row_summary2[csf('buyer_id')];
					$num_of_inquery_currnet[$row_summary2[csf('id')]] = $row_summary2[csf('id')];
				}
			}
			//echo count($num_of_inquery_currnet);die;
			//echo "SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id)";
			if ($inquery_id != "") {
				$sql_summary3 = sql_select("SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id)");
			}
			$inquery_id_sub = "";
			foreach ($sql_summary3 as $row) {
				if ($inquery_id_sub == "") $inquery_id_sub = $row[csf('id')];
				else $inquery_id_sub = $inquery_id_sub . "," . $row[csf('id')];
			}
			//echo "SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id_cur)";
			if ($inquery_id_cur != "") {
				$sql_summary4 = sql_select("SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id_cur)");
			}
			$inquery_id_sub_curr = "";
			foreach ($sql_summary4 as $row) {
				if ($inquery_id_sub_curr == "") $inquery_id_sub_curr = $row[csf('id')];
				else $inquery_id_sub_curr = $inquery_id_sub_curr . "," . $row[csf('id')];
			}


			if ($db_type == 0) $confirm_date_cond = "and confirm_date!='0000-00-00'";
			else if ($db_type == 2) $confirm_date_cond = "and confirm_date IS NOT NULL ";
			if ($inquery_id_sub != "") {
				//echo "SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub) $confirm_date_cond";
				$sql_summary5 = sql_select("SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub) $confirm_date_cond");
			}
			$inquery_id_con = "";
			foreach ($sql_summary5 as $row) {
				if ($inquery_id_con == "") $inquery_id_con = $row[csf('id')];
				else $inquery_id_con = $inquery_id_con . "," . $row[csf('id')];
			}
			//echo "SELECT id,inquery_id from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and inquery_id in($inquery_id_cur)";
			if ($inquery_id_sub_curr != "") {
				//echo "SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub_curr) $confirm_date_cond";
				$sql_summary6 = sql_select("SELECT id,quotation_id from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0 and quotation_id in($inquery_id_sub_curr) $confirm_date_cond");
			}
			$inquery_id_con_curr = "";
			foreach ($sql_summary6 as $row) {
				if ($inquery_id_con_curr == "") $inquery_id_con_curr = $row[csf('id')];
				else $inquery_id_con_curr = $inquery_id_con_curr . "," . $row[csf('id')];
			}


			$curr_month = date("F", strtotime($end_month)) . ", " . date("Y", strtotime($end_month));
			//wo_price_quotation_costing_mst  wo_price_quotation
			?>

		</div>

		<!--=============================================================Total Summary End=============================================================================================-->


		<!--=============================================================Total Current Month Strat=======================================================================================-->

		<div id="detail_report" style="height:auto; width:1850px; margin:0 auto; padding:0;">

			<legend> Details Report <? echo $report_type; ?></legend>
			<!--<div style="width:1670px;">-->
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1850" align="left">
				<tr>
					<td colspan="21" width="1850"><span style="height:15px; width:15px; background-color:#FF3; float:left; margin-left:400px; margin-right:10px;"></span><span style="float:left;">2 Days but quotation not submited</span><span style="height:15px; width:15px; background-color:#FF0000; float:left;  margin-left:10px; margin-right:10px;""></span><span style=" float:left;">3 Days but quotation not submited</span></td>
				</tr>
			</table>
			<table style="margin-left:10px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1850" align="left">

				<thead>
					<tr>
						<th width="30">SL No</th>
						<th width="60">Buyer Name</th>
						<th width="60">Buyer Inquiry No</th>
						<th width="80">Month</th>
						<th width="60">Year</th>
						<th width="80">Style Name</th>
						<th width="120">Image</th>
						<th width="80">Body Color</th>
						<th width="150">Fabrication</th>
						<th width="70">GSM</th>
						<th width="100">Department Name</th>
						<th width="100">Days To Sub. Sample</th>
						<th width="60">Inquire R/C Date</th>
						<th width="70"> Target Sam. Sub Date</th>
						<th width="60">Target Quation Date</th>
						<th width="60">Bulk Offer Qty</th>
						<th width="70"> Bulk Est. Shipment Date</th>
						<th width="70">Actual Quation Date</th>
						<th width="70">Actual Sam. Sub Date</th>
						<th width="70">Buyer Target Price</th>
						<th width="70">Submission Price</th>
						<th width="70">Order Status</th>
						<th>Remark</th>
					</tr>
				</thead>
			</table>
			<div style="width:1870px; max-height:410px; overflow-y: scroll;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" width="1850" border="1" rules="all" class="rpt_table" id="tbl_ship_pending">
					<tbody>
						<?
						if ($db_type == 0) $confirm_date_cond = "and b.confirm_date!='0000-00-00'";
						else if ($db_type == 2) $confirm_date_cond = "and b.confirm_date IS NOT NULL ";


						$sql_quatation = sql_select("SELECT id, inquery_id, product_code, m_list_no, bh_marchant, company_id, buyer_id, quot_date, est_ship_date,offer_qnty
			from  wo_price_quotation
			where company_id =$cbo_company_name and status_active=1 and is_deleted=0 and inquery_id>0");

						foreach ($sql_quatation as $row) {
							$quatation_mst_arr[$row[csf("inquery_id")]]['inquery_id'] = $row[csf("inquery_id")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['id'] = $row[csf("id")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['product_code'] = $row[csf("product_code")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['m_list_no'] = $row[csf("m_list_no")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['bh_marchant'] = $row[csf("bh_marchant")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['quot_date'] = $row[csf("quot_date")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['est_ship_date'] = $row[csf("est_ship_date")];
							$quatation_mst_arr[$row[csf("inquery_id")]]['offer_qnty'] = $row[csf("offer_qnty")];
						}
						//var_dump($quatation_mst_arr);

						$sql_quatation_costing = sql_select("SELECT a.id as quation_id, a.inquery_id, b.id as quation_costing_id, b.a1st_quoted_price, b.a1st_quoted_price_date, b.revised_price, b.revised_price_date, b.confirm_price,b.terget_qty,b.confirm_date
			from  wo_price_quotation a, wo_price_quotation_costing_mst b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.inquery_id>0");

						foreach ($sql_quatation_costing as $row) {
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['inquery_id'] = $row[csf("inquery_id")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['quation_id'] = $row[csf("quation_id")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['quation_costing_id'] = $row[csf("quation_costing_id")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['a1st_quoted_price'] = $row[csf("a1st_quoted_price")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['a1st_quoted_price_date'] = $row[csf("a1st_quoted_price_date")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['revised_price'] = $row[csf("revised_price")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['revised_price_date'] = $row[csf("revised_price_date")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['confirm_price'] = $row[csf("confirm_price")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['terget_qty'] = $row[csf("terget_qty")];
							$quatation_costing_mst_arr[$row[csf("inquery_id")]]['confirm_date'] = $row[csf("confirm_date")];
						}
						$data_img = sql_select("select file_type,image_location,master_tble_id  from common_photo_library  where   form_name='quotation_inquery' and is_deleted=0 and file_type in(1,2)");
						$system_img_arr = array();
						foreach ($data_img as $row) {
							if ($row[csf('file_type')] == 1) {
								$system_img_arr[$row[csf('master_tble_id')]]['img'] = $row[csf('image_location')];
							} else if ($row[csf('file_type')] == 2) {
								$system_img_arr[$row[csf('master_tble_id')]]['file'] = $row[csf('image_location')];
							}
						}
						//var_dump($quatation_costing_mst_arr);
						$sql_fabrication_composition = sql_select("SELECT a.id as quation_id, a.inquery_id, b.construction, b.composition
			from  wo_price_quotation a, wo_pri_quo_fabric_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0");

						foreach ($sql_fabrication_composition as $row) {
							$fabrication_composition[$row[csf("inquery_id")]] .= $row[csf("construction")] . " " . $row[csf("composition")] . ",";
						}

						if ($db_type == 2) {
							$sql_yarn_count = sql_select("SELECT  a.inquery_id, LISTAGG(CAST(b.count_id as varchar(4000)),',')WITHIN GROUP (ORDER BY b.count_id) as count_id
			from  wo_price_quotation a, wo_pri_quo_fab_yarn_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by a.inquery_id");
						} else if ($db_type == 0) {
							$sql_yarn_count = sql_select("SELECT  a.inquery_id, group_concat(distinct b.count_id ) as count_id
			from  wo_price_quotation a, wo_pri_quo_fab_yarn_cost_dtls b
			where a.id=b.quotation_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by a.inquery_id");
						}
						//echo $sql_yarn_count;die;
						foreach ($sql_yarn_count as $row) {
							$count_id_arr[$row[csf("inquery_id")]] = implode(",", array_unique(explode(",", $row[csf("count_id")])));
						}

						if ($start_date != "") {
							$str_cond3 = "and insert_date between '$start_date' and '$current_month_end_date 11:59:59 PM' ";
						} else {
							$str_cond3 = "";
						}
						$i = 1;
						$sql_inquery = "SELECT id, system_number_prefix_num, system_number, buyer_request, company_id, buyer_id, season_buyer_wise as season, inquery_date, buyer_request, buyer_id, department_name, offer_qty, remarks, dealing_marchant, style_refernce, actual_sam_send_date, color, fabrication, actual_req_quot_date, req_quotation_date, target_sam_sub_date, insert_date, est_ship_date, buyer_target_price, buyer_submit_price
			from  wo_quotation_inquery
			where company_id =$cbo_company_name and status_active=1 and is_deleted=0  $str_cond3 $buyer_cond $session_cond $date_cond $inqu_no_cond $department_cond $inquery_cond $style_cond order by inquery_date DESC";
						//echo $sql_inquery;
						$sql_result = sql_select($sql_inquery);
						$total_buyer_target_price = 0;
						$total_buyer_submit_price = 0;
						foreach ($sql_result as $row) {

							if ($inquery_confirm_date[$row[csf("id")]] == '' || $inquery_confirm_date[$row[csf("id")]] == '0000-00-00') {
								if ($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '0000-00-00' && $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '') {
									$con_sub_date = change_date_format($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date']);
									//echo $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'];
								} else {
									if ($quatation_mst_arr[$row[csf("id")]]['inquery_id'] != 0) {
										$con_sub_date = change_date_format($quatation_mst_arr[$row[csf("id")]]['quot_date']);

										$dateToSub = datediff('d', $row[csf('target_sam_sub_date')], $quatation_mst_arr[$row[csf("id")]]['quot_date']);
										$dateToConf = datediff('d', $quatation_mst_arr[$row[csf("id")]]['quot_date'], $pc_date_time);
									} else {
										$dateToSub = datediff('d', $row[csf('target_sam_sub_date')], $pc_date_time);
										$dateToConf = datediff('d', $row[csf('inquery_date')], $pc_date_time);
										//echo change_date_format($row[csf('inquery_date')]);
										//echo $pc_date_time;
									}
								}

								//$month_year=date("Y-m",strtotime($row[csf("insert_date")]));
								$month_year = date("Y-m", strtotime($row[csf("inquery_date")])); //by monzu
								$month_year_arr = (explode("-", $month_year));
								$month_val = ($month_year_arr[1] * 1);
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$fabrication = explode(",", $row[csf('fabrication')]);

								$fabrics_type = '';
								$gsm = '';
								foreach ($fabrication as $fab_data) {
									//print_r($fab_data);
									$fab_des = explode("_", $fab_data);
									if ($fabrics_type != '') {
										$fabrics_type .= "," . $fab_des[0] . ' ' . $fab_des[1];
										$gsm .= $fab_des[2];
									} else {
										$fabrics_type = $fab_des[0] . ' ' . $fab_des[1];
										$gsm .= $fab_des[2];
									}
									//	print_r($fab_data);

								}
								$img_name = $system_img_arr[$row[csf('id')]]['img'];
								$file_name = $system_img_arr[$row[csf('id')]]['file'];

						?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="60" align="center">
										<p><? echo $buyer_arr[$row[csf("buyer_id")]]; //echo $months[$month_val]; 
											?></p>
									</td>
									<td width="60" title="<? echo $row[csf('id')]; ?>" align="center">
										<p><? echo $row[csf("system_number_prefix_num")]; //echo $month_year_arr[0]; 
											?></p>
									</td>
									<td width="80" align="center">
										<p><? echo $months[$month_val]; //echo $buyer_arr[$row[csf("buyer_id")]]; 
											?></p>
									</td>
									<td width="60" align="center">
										<p><? echo $month_year_arr[0]; //echo $row[csf("buyer_request")]; 
											?></p>
									</td>
									<td width="80" align="center">
										<p><? echo $row[csf('style_refernce')]; //echo $row[csf("department_name")]; 
											?></p>
									</td>

									<td width="120" onclick="openmypage_image('requires/buyer_inquery_qote_submit_controller.php?action=show_image&sys_id=<? echo $row[csf("id")] ?>','Image View')">
										<p>
											<? if ($img_name != '') {
											?>
												<img src='../../../../<? echo $imge_arr[$row[csf('id')]]; ?>' height='100' width='120' />click
											<?
											} else {
												echo '&nbsp;';
											}
											?>
										</p>

									</td>
									<td width="80">
										<p><? echo $row[csf("color")]; ?></p>
									</td>
									<td width="150" align="center">
										<p><? echo $fabrication_library[$row[csf('fabrication')]]; ?></p>
									</td>
									<td width="70" align="center">
										<p><? echo $gsm_arr[$row[csf('fabrication')]]; ?></p>
									</td>
									<td width="100" align="center">
										<p><? echo $row[csf("department_name")]; ?></p>
									</td>
									<td width="100">
										<p><? echo  $dateToSub . " Days"; ?></p>
									</td>
									<td align="center" width="60">
										<p><? echo change_date_format($row[csf("inquery_date")]);  ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("target_sam_sub_date")]); ?></p>
									</td>
									<td align="center" width="60">
										<p><? echo change_date_format($row[csf("req_quotation_date")]); //echo change_date_format($row[csf("est_ship_date")]);  
											?></p>
									</td>
									<td width="60" align="right">
										<p><? echo $row[csf("offer_qty")]; //number_format($quatation_mst_arr[$row[csf("id")]]['offer_qnty'],0,"","");
											$total_offer_qty += $row[csf("offer_qty")]; //$quatation_mst_arr[$row[csf("id")]]['offer_qnty'];//$$row[csf("offer_qty")]; 
											?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("est_ship_date")]);  ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("actual_req_quot_date")]); ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo change_date_format($row[csf("actual_sam_send_date")]); ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo $row[csf("buyer_target_price")]; ?></p>
									</td>
									<td align="center" width="70">
										<p><? echo $row[csf("buyer_submit_price")]; ?></p>
									</td>
									<?
									if ($quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '0000-00-00' && $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'] != '') {
										$status = "Confirmed";
										echo $quatation_costing_mst_arr[$row[csf("id")]]['confirm_date'];
									} else //$row[csf("actual_sam_send_date")];
									{
										if ($row[csf("actual_sam_send_date")] != '0000-00-00' && $row[csf("actual_sam_send_date")] != '') $status = "Submitted";
										else $status = "Not Submitted";
									}
									if ($status == 'Submitted') {
									?>
										<td width="70" align="center">
											<p><? echo $status; ?></p>
										</td>
										<?
									} else if ($status == 'Not Submitted') {
										if ($dateToSub > 2) {
										?>
											<td width="70" align="center" bgcolor="#FF0000">
												<p><? echo $status; ?></p>
											</td>
										<?
										} else {
										?>
											<td width="70" align="center" bgcolor="#FFFF00">
												<p><? echo $status; ?></p>
											</td>
									<?
										}
									}
									?>
									<td>
										<p><? echo $row[csf("remarks")]; ?></p>
									</td>

								</tr>
						<?
								$i++;
								$total_buyer_submit_price += $row[csf("buyer_submit_price")];
								$total_buyer_target_price += $row[csf("buyer_target_price")];
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="15" align="right">Grand Total</th>
							<th align="right"><? echo  number_format($total_offer_qty, 0); ?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>

		</div>

	<?

	} else if ($report_type == 4) //Zakaria joy (25/08/2020)
	{
		$imge_arr = return_library_array("select master_tble_id, image_location from  common_photo_library where file_type=1", 'master_tble_id', 'image_location');
		$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
		$cbo_season_id = str_replace("'", "", $cbo_season_id);
		$hidden_inquery_id = str_replace("'", "", $hidden_inquery_id);

		if (str_replace("'", "", $cbo_buyer_name) == 0) {
			if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
				if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				else $buyer_cond = "";
			} else $buyer_cond = "";
		} else $buyer_cond = " and a.buyer_id=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)

		if ($cbo_season_id != 0) $session_cond = "and a.season_buyer_wise='$cbo_season_id'";
		else  $session_cond = "";
		if (str_replace("'", "", $txt_inq_no) != "") $inquery_cond = "and a.system_number_prefix_num=$txt_inq_no";
		else  $inquery_cond = "";
		if (str_replace("'", "", $txt_inqu_no) != "") $binquery_cond = "and a.buyer_request='$txt_inqu_no'";
		else  $binquery_cond = "";
		if (str_replace("'", "", $txt_department) != "") $departmentCond = "and a.department_name='$txt_department'";
		else  $departmentCond = "";
		if (str_replace("'", "", $txt_style_ref) != "") $style_cond = "and a.style_refernce=$txt_style_ref";
		else  $style_cond = "";

		if ($txt_date_from == "" || $txt_date_to == "") $date_cond = "";
		else {
			if (str_replace("'", "", $cbo_search_by) == 1) {
				$date_cond = " and a.inquery_date between '$txt_date_from' and '$txt_date_to'";
			} else {
				$date_cond = " and a.est_ship_date between '$txt_date_from' and '$txt_date_to'";
			}
		}


		//$imge_arr=sql_select("select image_location,master_tble_id from common_photo_library where form_name='quotation_inquery' and is_deleted=0 and file_type in(1)");
		$imge_arr = return_library_array("select master_tble_id, image_location from  common_photo_library where file_type=1 and form_name='quotation_inquery' and is_deleted=0", 'master_tble_id', 'image_location');
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		//echo "SELECT a.id, a.buyer_id, a.buyer_request, a.style_refernce, a.color_id, a.bh_merchant, a.offer_qty, a.inquery_date, a.est_ship_date, a.possible_order_con, a.lead_time, a.target_req_cout, a.actual_req_quot_date, a.target_sam_sub_date, a.remarks, a.fabrication, a.actual_sam_send_date, a.req_quotation_date, a.buyer_target_price, a.status_active, b.price_stage, b.stage, b.price_date, b.price, b.id as info_id from wo_quotation_inquery a left join wo_quo_inq_price_info b on a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $buyer_cond $session_cond $inquery_cond $binquery_cond $departmentCond $style_cond $date_cond"; die;
		$get_inquery_data = sql_select("SELECT a.id, a.buyer_id, a.buyer_request, a.style_refernce, a.color_id, a.bh_merchant, a.offer_qty, a.inquery_date, a.est_ship_date, a.possible_order_con, a.lead_time, a.target_req_cout, a.actual_req_quot_date, a.target_sam_sub_date, a.remarks, a.fabrication, a.actual_sam_send_date, a.req_quotation_date, a.buyer_target_price, a.status_active, b.price_stage, b.stage, b.price_date, b.price, b.id as info_id,c.stage_id, c.sample_date , c.remarks as sample_remark, c.id as sample_id from wo_quotation_inquery a left join wo_quo_inq_price_info b on a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 left join wo_quo_inq_sample_info c on a.id=c.mst_id and c.status_active=1 and c.is_deleted=0 where  a.is_deleted=0 and a.company_id=$cbo_company_name $buyer_cond $session_cond $inquery_cond $binquery_cond $departmentCond $style_cond $date_cond order by a.id , b.id, c.id ");
		$columns = array('buyer_id', 'buyer_request', 'style_refernce', 'color_id', 'bh_merchant', 'offer_qty', 'inquery_date', 'est_ship_date', 'possible_order_con', 'lead_time', 'target_req_cout', 'actual_req_quot_date', 'target_sam_sub_date', 'remarks', 'fabrication', 'actual_sam_send_date', 'req_quotation_date', 'buyer_target_price', 'status_active');
		foreach ($get_inquery_data as $row) {
			foreach ($columns as $column) {
				$inquery_data_arr[$row[csf('id')]][$column] = $row[csf($column)];
			}
			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['price_stage'] = $row[csf('price_stage')];
			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['stage'] = $row[csf('stage')];
			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['price_date'] = $row[csf('price_date')];
			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['price'] = $row[csf('price')];

			$inquery_data_arr[$row[csf('id')]]['sample_info'][$row[csf('sample_id')]]['stage_id'] = $row[csf('stage_id')];

			$inquery_data_arr[$row[csf('id')]]['sample_info'][$row[csf('sample_id')]]['sample_date'] = $row[csf('sample_date')];
			$inquery_data_arr[$row[csf('id')]]['sample_info'][$row[csf('sample_id')]]['sample_remark'] = $row[csf('sample_remark')];


			$mst_id[$row[csf('id')]] = $row[csf('id')];
		}
		$master_id = implode(",", $mst_id);
		$submitted_date_sql = sql_select("SELECT price_date,mst_id from wo_quo_inq_price_info where mst_id in ($master_id) and status_active=1 and is_deleted=0 and price_stage=1");
		foreach ($submitted_date_sql as $row) {
			$price_submitted_arr[$row[csf('mst_id')]] = $row[csf('price_date')];
		}

		/*echo '<pre>';
	print_r($inquery_data_arr);*/
	?>
		<style type="text/css">
			.rpt_table td {
				text-align: center;
				vertical-align: middle;
			}
		</style>
		<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1850" align="left">

			<thead>
				<tr>
					<th width="30">SL No</th>
					<th width="100">Buyer Name</th>
					<th width="60">Buyer Inquiry No</th>
					<th width="80">Style Name</th>
					<th width="120">Image</th>
					<th width="80">Body Color</th>
					<th width="150">Fabrication</th>
					<th width="70">GSM</th>
					<th width="100">BH Mechandiser</th>
					<th width="60">Bulk Offer Qty</th>
					<th width="60">Inquiry Received Date</th>
					<th width="70">Bulk Est. Shipment Date</th>
					<th width="70">Possible order confirmation Date</th>
					<th width="60">Leadtime</th>
					<th width="70">Target Sam. Sub Date</th>
					<th width="70">Actual Sam. Sub Date</th>
					<th width="70">Target Quotion Date</th>
					<th width="70">Buyer Target Price</th>
					<th width="70">Sample Submission status</th>
					<th width="70">Price submission status</th>
					<th width="70">Inquiry Status</th>
					<th width="120">Remark</th>
					<th width="70">Stage</th>
					<th width="70">Submitted Price</th>
					<th width="70">Date</th>
					<th width="100">Price Stage</th>
				</tr>
			</thead>
			<tbody>
				<?
				$sl = 1;
				$rowspan = 0;
				foreach ($inquery_data_arr as $id => $value) {
					$k = 1;
					$fabrication = explode(",", $value['fabrication']);
					$fabrics_type = '';
					$gsm = '';
					foreach ($fabrication as $fab_data) {
						$fab_des = explode("_", $fab_data);
						if ($fabrics_type != '') {
							$fabrics_type .= "," . $fab_des[0] . ' ' . $fab_des[1];
							$gsm .= $fab_des[2];
						} else {
							$fabrics_type = $fab_des[0] . ' ' . $fab_des[1];
							$gsm .= $fab_des[2];
						}
					}

					foreach ($value['price_info'] as $dtls_id => $data) {
						if (empty($first_price_date)) {
							$first_price_date = $data['price_date'];
						}
					}

					$first_sample_date = '';
					foreach ($value['sample_info'] as $dtls_id => $data) {
						if (empty($first_sample_date)) {
							$first_sample_date = $data['sample_date'];
						}
					}
					$sample_sub_status = "";
					if (!empty($first_sample_date) && !empty($value['target_sam_sub_date'])) {
						$dateToSub = datediff('d',  $first_sample_date, $value['target_sam_sub_date']);

						if ($dateToSub >= 0) {
							$sample_sub_status = "Timely Submitted";
						} else {
							$sample_sub_status = "Late Submitted";
						}
					} else if ($value['actual_sam_send_date'] == '' || empty($value['actual_sam_send_date']) || empty($sample_sub_status)) {
						$sample_sub_status = "Not Submitted";
					}

					$price_sub_status = "";
					if (!empty($first_price_date) && !empty($value['req_quotation_date'])) {
						$priceSub = datediff('d', $first_price_date, $value['req_quotation_date']);

						if ($priceSub >= 0) {
							$price_sub_status = "Timely Submitted";
						} else {
							$price_sub_status = "Late Submitted";
						}
					} else if ($value['target_sam_sub_date'] == '' || empty($value['target_sam_sub_date']) || empty($price_sub_status)) {
						$price_sub_status = "Not Submitted";
					}

					$rowspan = count($value['price_info']);
				?>
					<tr>
						<td rowspan="<? echo $rowspan ?>"><? echo $sl; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $buyer_arr[$value['buyer_id']]; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $value['buyer_request']; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $value['style_refernce']; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? if ($imge_arr[$id] != '') { ?><img src='../../../<? echo $imge_arr[$id]; ?>' height='60' width='80' /><? } ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $color_arr[$value['color_id']]; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $fabrics_type; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $gsm; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $value['bh_merchant']; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $value['offer_qty']; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo change_date_format($value['inquery_date']); ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo change_date_format($value['est_ship_date']); ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo change_date_format($value['possible_order_con']); ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo datediff('d', $value['possible_order_con'], $value['est_ship_date']); ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo change_date_format($value['target_sam_sub_date']); ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo change_date_format($value['actual_sam_send_date']); ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo change_date_format($value['req_quotation_date']); ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $value['buyer_target_price']; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $sample_sub_status ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $price_sub_status; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $inquery_status_arr[$value['status_active']]; ?></td>
						<td rowspan="<? echo $rowspan ?>"><? echo $value['remarks']; ?></td>
						<? foreach ($value['price_info'] as $dtls_id => $data) { ?>
							<? if ($k != 1) echo '<tr>' ?>
							<td><? echo $inquery_stage_arr[$data['stage']]; ?></td>
							<td><? echo $data['price']; ?></td>
							<td><? echo change_date_format($data['price_date']); ?></td>
							<td><? echo $inquery_price_arr[$data['price_stage']]; ?></td>
					</tr>
				<? $k++;
						} ?>

			<?
					$sl++;
				}
			?>
			</tbody>
		</table>
	<?

	} else if ($report_type == 5) //Helal (14/12/2020)
	{
		$imge_arr = return_library_array("select master_tble_id, image_location from  common_photo_library where file_type=1", 'master_tble_id', 'image_location');
		$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
		$cbo_season_id = str_replace("'", "", $cbo_season_id);
		$hidden_inquery_id = str_replace("'", "", $hidden_inquery_id);
		$price_stage = str_replace("'", "", $price_stage);

		if (str_replace("'", "", $cbo_buyer_name) == 0) {
			if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
				if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				else $buyer_cond = "";
			} else $buyer_cond = "";
		} else $buyer_cond = " and a.buyer_id=$cbo_buyer_name"; //.str_replace("'","",$cbo_buyer_name)

		if ($cbo_season_id != 0) $session_cond = "and a.season_buyer_wise='$cbo_season_id'";
		else  $session_cond = "";
		if (str_replace("'", "", $txt_inq_no) != "") $inquery_cond = "and a.system_number_prefix_num=$txt_inq_no";
		else  $inquery_cond = "";
		if (str_replace("'", "", $txt_inqu_no) != "") $binquery_cond = "and a.buyer_request='$txt_inqu_no'";
		else  $binquery_cond = "";
		if (str_replace("'", "", $txt_department) != "") $departmentCond = "and a.department_name='$txt_department'";
		else  $departmentCond = "";
		if (str_replace("'", "", $txt_style_ref) != "") $style_cond = "and a.style_refernce=$txt_style_ref";
		else  $style_cond = "";

		if ($txt_date_from == "" || $txt_date_to == "") $date_cond = "";
		else {
			if (str_replace("'", "", $cbo_search_by) == 1) {
				$date_cond = " and a.inquery_date between '$txt_date_from' and '$txt_date_to'";
			} else {
				$date_cond = " and a.est_ship_date between '$txt_date_from' and '$txt_date_to'";
			}
		}


		//$imge_arr=sql_select("select image_location,master_tble_id from common_photo_library where form_name='quotation_inquery' and is_deleted=0 and file_type in(1)");
		$imge_arr = return_library_array("select master_tble_id, image_location from  common_photo_library where file_type=1 and form_name='quotation_inquery' and is_deleted=0", 'master_tble_id', 'image_location');
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

		if (!empty($price_stage)) {
			$get_inquery_data = sql_select("SELECT a.id, a.buyer_id, a.buyer_request, a.style_refernce, a.color_id, a.bh_merchant, a.offer_qty, a.inquery_date, a.est_ship_date, a.possible_order_con, a.lead_time, a.target_req_cout, a.actual_req_quot_date, a.target_sam_sub_date, a.remarks, a.fabrication, a.actual_sam_send_date, a.req_quotation_date, a.buyer_target_price,a.week_no,a.customer_year, a.status_active, b.price_stage, b.stage, b.price_date, b.price, b.id as info_id,c.stage_id,c.sample_date, c.remarks as sample_remark, c.id as sample_id from wo_quotation_inquery a  join wo_quo_inq_price_info b on a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.stage=$price_stage left join wo_quo_inq_sample_info c on a.id=c.mst_id and c.status_active=1 and c.is_deleted=0 where  a.is_deleted=0 and a.company_id=$cbo_company_name $buyer_cond $session_cond $inquery_cond $binquery_cond $departmentCond $style_cond $date_cond order by a.id,b.id,c.id ");
		} else {
			$get_inquery_data = sql_select("SELECT a.id, a.buyer_id, a.buyer_request, a.style_refernce, a.color_id, a.bh_merchant, a.offer_qty, a.inquery_date, a.est_ship_date, a.possible_order_con, a.lead_time, a.target_req_cout, a.actual_req_quot_date, a.target_sam_sub_date, a.remarks, a.fabrication, a.actual_sam_send_date, a.req_quotation_date, a.buyer_target_price,a.week_no,a.customer_year, a.status_active, b.price_stage, b.stage, b.price_date, b.price, b.id as info_id,c.stage_id, c.sample_date , c.remarks as sample_remark, c.id as sample_id from wo_quotation_inquery a left join wo_quo_inq_price_info b on a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 left join wo_quo_inq_sample_info c on a.id=c.mst_id and c.status_active=1 and c.is_deleted=0 where  a.is_deleted=0 and a.company_id=$cbo_company_name $buyer_cond $session_cond $inquery_cond $binquery_cond $departmentCond $style_cond $date_cond order by a.id,b.id,c.id ");
		}


		$columns = array('buyer_id', 'week_no', 'customer_year', 'buyer_request', 'style_refernce', 'color_id', 'bh_merchant', 'offer_qty', 'inquery_date', 'est_ship_date', 'possible_order_con', 'lead_time', 'target_req_cout', 'actual_req_quot_date', 'target_sam_sub_date', 'remarks', 'fabrication', 'actual_sam_send_date', 'req_quotation_date', 'buyer_target_price', 'status_active');
		foreach ($get_inquery_data as $row) {
			foreach ($columns as $column) {
				$inquery_data_arr[$row[csf('id')]][$column] = $row[csf($column)];
			}


			$sample_date_arr[$row[csf('sample_date')]] = $row[csf('sample_date')];


			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['price_stage'] = $row[csf('price_stage')];
			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['stage'] = $row[csf('stage')];
			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['price_date'] = $row[csf('price_date')];
			$inquery_data_arr[$row[csf('id')]]['price_info'][$row[csf('info_id')]]['price'] = $row[csf('price')];




			$inquery_data_arr[$row[csf('id')]]['sample_info'][$row[csf('sample_id')]]['stage_id'] = $row[csf('stage_id')];
			$inquery_data_arr[$row[csf('id')]]['sample_info'][$row[csf('sample_id')]]['sample_date'] = $row[csf('sample_date')];
			$inquery_data_arr[$row[csf('id')]]['sample_info'][$row[csf('sample_id')]]['sample_remark'] = $row[csf('sample_remark')];

			$mst_id[$row[csf('id')]] = $row[csf('id')];
		}
		$master_id = implode(",", $mst_id);
		$submitted_date_sql = sql_select("SELECT mst_id,price_date,id,price,price_stage,stage from wo_quo_inq_price_info where mst_id in ($master_id) and status_active=1 and is_deleted=0 group by mst_id,price_date,id,price,price_stage,stage order by id");

		$string = "";
		$date = "";
		foreach ($submitted_date_sql as $row) {
			$price_max_date_arr[$row[csf('mst_id')]] = $row[csf('price_date')];

			$date = date('Y-m-d', strtotime($row[csf('price_date')]));



			$string = $row[csf('mst_id')] . "*" . $row[csf('price_stage')] . "*" . $date . "*" . $row[csf('id')];

			$price_date_arr[$row[csf('mst_id')]][$row[csf('price_stage')]][$date] = $date;
			$price_id_arr[$row[csf('mst_id')]][$row[csf('price_stage')]][$date][$row[csf('id')]] = $row[csf('id')];


			$price_data_arr[$string]['price_stage'] = $row[csf('price_stage')];
			$price_data_arr[$string]['stage'] = $row[csf('stage')];
			$price_data_arr[$string]['price_date'] = $row[csf('price_date')];
			$price_data_arr[$string]['price'] = $row[csf('price')];
		}
		// echo "<pre>";
		// print_r($price_data_arr);
		$sample_date_sql = sql_select("SELECT mst_id,sample_date,stage_id,sample_date,remarks,id from wo_quo_inq_sample_info where mst_id in ($master_id) and status_active=1 and is_deleted=0 group by mst_id,sample_date,stage_id,sample_date,remarks,id order by id");
		$date = "";
		$string = "";
		foreach ($sample_date_sql as $row) {
			$sample_max_date_arr[$row[csf('mst_id')]] = $row[csf('sample_date')];

			$date = date('Y-m-d H:i:s', strtotime($row[csf('sample_date')]));

			$sample_date_arr[$row[csf('mst_id')]][$date] = $date;
			$sample_id_arr[$row[csf('mst_id')]][$date][$row[csf('id')]] = $row[csf('id')];

			$string = $row[csf('mst_id')] . "*" . $date . "*" . $row[csf('id')];

			$sample_data_arr[$string]['stage_id'] = $row[csf('stage_id')];
			$sample_data_arr[$string]['sample_remark'] = $row[csf('remarks')];
		}
		$inquery_sample_arr = array(1 => "1st Submission", 2 => "2nd Submission", 3 => "3rd Submission");

		// echo '<pre>';
		// print_r($price_date_arr);
	?>
		<style type="text/css">
			.rpt_table td {
				text-align: center;
				vertical-align: middle;
			}
		</style>
		<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2020" align="left">

			<thead>
				<tr>
					<th width="30">SL No</th>
					<th width="100">Buyer Name</th>
					<th width="70">Customer Sales Year</th>
					<th width="70">Customer Sales Week</th>
					<th width="60">Buyer Inquiry No</th>
					<th width="80">Style Name</th>
					<th width="120">Image</th>
					<th width="80">Body Color</th>
					<th width="150">Fabrication</th>
					<th width="70">GSM</th>
					<th width="100">BH Mechandiser</th>
					<th width="60">Bulk Offer Qty</th>
					<th width="120">Remark</th>
					<th width="60">Inquiry Received Date</th>
					<th width="70">Bulk Est. Shipment Date</th>
					<th width="70">Possible order confirmation Date</th>
					<th width="60">Leadtime</th>
					<th width="70">Target Sam. Sub Date</th>
					<!-- <th width="70">Actual Sam. Sub Date</th> -->
					<th width="70">Sample Send Date</th>
					<th width="70">Target Quotion Date</th>
					<th width="70">Price Submitted Date</th>
					<th width="70">Price Stage</th>
					<th width="70">Submitted Price</th>
					<th width="70">Buyer Target Price</th>
					<th width="70">Sample Submission status</th>
					<th width="70">Price submission status</th>
					<th width="70">Sample Remark</th>
					<th width="70">Inquiry Status</th>



					<!-- <th width="100">Price Stage</th>

					<th width="70">Sample Stage</th> -->



				</tr>
			</thead>
			<tbody>
				<?
				$sl = 1;
				$rowspan = 0;
				$string2 = "";
				$submit_string = "";
				foreach ($inquery_data_arr as $id => $value) {
					$k = 1;
					$fabrication = explode(",", $value['fabrication']);
					$fabrics_type = '';
					$gsm = '';
					$price_date = rtrim($price_max_date_arr[$id], ",");
					$sample_date = rtrim($sample_max_date_arr[$id], ",");
					foreach ($fabrication as $fab_data) {
						$fab_des = explode("_", $fab_data);
						if ($fabrics_type != '') {
							$fabrics_type .= "," . $fab_des[0] . ' ' . $fab_des[1];
							$gsm .= $fab_des[2];
						} else {
							$fabrics_type = $fab_des[0] . ' ' . $fab_des[1];
							$gsm .= $fab_des[2];
						}
					}
					$data_arr = array();
					$cnt = 1;
					$first_price_date = '';
					$submit_price_date = '';
					$submit_string = '';
					$string2 = '';
					foreach ($value['price_info'] as $dtls_id => $data) {
						if (empty($first_price_date)) {
							$first_price_date = $data['price_date'];
						}


						$pdate = max($price_date_arr[$id][$data['price_stage']]);
						$pminId = max($price_id_arr[$id][$data['price_stage']][$pdate]);
						if ($data['price_stage'] == 1) {
							$submit_string = $id . "*" . $data['price_stage'] . "*" . $pdate . "*" . $pminId;
							$submit_price_date = $pdate;
						} else {
							$string2 = $id . "*" . $data['price_stage'] . "*" . $pdate . "*" . $pminId;
						}




						$cnt++;
					}
					$cnt = 1;
					$first_sample_date = '';
					$sdate = '';
					$sminId = '';
					$string3 = '';
					foreach ($value['sample_info'] as $dtls_id => $data) {
						if (empty($first_sample_date)) {
							$first_sample_date = $data['sample_date'];
						}

						$sdate = max($sample_date_arr[$id]);
						$sminId = max($sample_id_arr[$id][$sdate]);
						$string3 = $id . "*" . $sdate . "*" . $sminId;

						// $data_arr[$cnt]['stage_id']=$inquery_sample_arr[$data['stage_id']];
						// $data_arr[$cnt]['sample_date']=change_date_format($data['sample_date']);
						// $data_arr[$cnt]['sample_remark']=$data['sample_remark'];

						$cnt++;
					}
					$sample_sub_status = "";
					if (!empty($first_sample_date) && !empty($value['target_sam_sub_date'])) {
						$dateToSub = datediff('d',  $first_sample_date, $value['target_sam_sub_date']);

						if ($dateToSub >= 0) {
							$sample_sub_status = "Timely Submitted";
						} else {
							$sample_sub_status = "Late Submitted";
						}
					} else if ($value['actual_sam_send_date'] == '' || empty($value['actual_sam_send_date']) || empty($sample_sub_status)) {
						$sample_sub_status = "Not Submitted";
					}

					$price_sub_status = "";
					if (!empty($first_price_date) && !empty($value['req_quotation_date'])) {
						$priceSub = datediff('d', $first_price_date, $value['req_quotation_date']);

						if ($priceSub >= 0) {
							$price_sub_status = "Timely Submitted";
						} else {
							$price_sub_status = "Late Submitted";
						}
					} else if ($value['target_sam_sub_date'] == '' || empty($value['target_sam_sub_date']) || empty($price_sub_status)) {
						$price_sub_status = "Not Submitted";
					}

					$price_rows = count($value['price_info']);
					$sample_rows = count($value['sample_info']);
					$rowspan = max($price_rows, $sample_rows);
					$lead_time = datediff('d', $value['possible_order_con'], $value['est_ship_date']);
				?>
					<tr>
						<td><? echo $sl; ?></td>
						<td><? echo $buyer_arr[$value['buyer_id']]; ?></td>
						<td><? echo $value['customer_year']; ?></td>
						<td><? echo $value['week_no']; ?></td>
						<td><? echo $value['buyer_request']; ?></td>
						<td><? echo $value['style_refernce']; ?></td>
						<td><? if ($imge_arr[$id] != '') { ?><img src='../../../<? echo $imge_arr[$id]; ?>' height='60' width='80' /><? } ?></td>
						<td><? echo $color_arr[$value['color_id']]; ?></td>
						<td><? echo $fabrics_type; ?></td>
						<td><? echo $gsm; ?></td>
						<td><? echo $value['bh_merchant']; ?></td>
						<td><? echo $value['offer_qty']; ?><? $total_offer_qty += $value['offer_qty']; ?> </td>
						<td><? echo $value['remarks']; ?></td>
						<td><? echo $value['inquery_date']; ?></td>
						<td><? echo $value['est_ship_date']; ?></td>
						<td><? echo $value['possible_order_con']; ?></td>
						<td><? echo $lead_time; ?></td>
						<td><? echo $value['target_sam_sub_date']; ?></td>
						<!-- <td><? echo change_date_format($value['actual_sam_send_date']); ?></td> -->
						<td><? if ($sdate) {
								echo date('d-M-Y', strtotime($sdate));
							} //echo change_date_format($sdate); 
							?></td>
						<td><? echo $value['req_quotation_date']; ?></td>
						<td><? if ($submit_price_date) {
								echo date('d-M-Y', strtotime($submit_price_date));
							}; ?></td>
						<td><? echo $inquery_stage_arr[$price_data_arr[$submit_string]['stage']];; //$data_arr[$cn]['stage']; 
							?></td>
						<td><? echo $price_data_arr[$submit_string]['price']; //$data_arr[$cn]['price']; 
							?></td>
						<td><? echo $value['buyer_target_price']; ?></td>
						<td><? echo $sample_sub_status ?></td>
						<td><? echo $price_sub_status; ?></td>
						<td><? echo $sample_data_arr[$string3]['sample_remark']; //$data_arr[$cn]['sample_remark']; 
							?></td>
						<td><? echo $inquery_status_arr[$value['status_active']]; ?></td>
					</tr>
				<?
					$sl++;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="11" align="right">Total</th>
					<th align="center"><? echo  number_format($total_offer_qty, 0); ?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<!-- <th>&nbsp;</th> -->
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<!-- <th>&nbsp;</th>
					<th>&nbsp;</th> -->
				</tr>
			</tfoot>
		</table>
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
	echo "$total_data****$filename";
	disconnect($con);
	exit();
}  // end if($type=="sewing_production_summary")


if ($action == "show_image") {
	echo load_html_head_contents("Image Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$sys_id' and form_name in('quotation_inquery_front_image','quotation_inquery_back_image') and is_deleted=0 and file_type=1");
	?>
	<table>
		<tr>
			<?
			foreach ($data_array as $row) {
			?>
				<td><a href="<? $row[csf('image_location')] ?>" target="_new"><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='400px' width='400px' align="middle" /></a></td>
			<?
			}
			?>
		</tr>
	</table>
<?
	//}
	//exit();
}

if ($action == "show_file") {
	echo load_html_head_contents("Buyer Inquery File", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$sys_id' and form_name='quotation_inquery' and is_deleted=0 and file_type=2");
?>
	<table>
		<tr>
			<?
			foreach ($data_array as $row) {
			?>
				<td><a href="../../../../<? echo $row[csf('image_location')] ?>" target="_new">
						<img src="../../../../file_upload/blank_file.png" width="80" height="60"> </a>
				</td>
			<?
			}
			?>
		</tr>
	</table>
<?
}
?>