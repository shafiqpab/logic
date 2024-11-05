<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company='$data' AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (1,3,21,90)) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", 0, "" );
	exit();
}

if($action=="load_drop_down_cust_buyer")
{
	extract($_REQUEST);
    if ($choosenCompany > 0) {
	echo create_drop_down( "cbo_cust_buyer_id", 130, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company='$choosenCompany' AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (1,3,21,90)) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", 1, "--Select Cust Buyer--", 0, "" );
	} else {
        echo create_drop_down("cbo_cust_buyer_id", 130, $blank_array, "", 1, "-- Select Cust Buyer --", $selected, "", 0, "", "", "", "");
    }
	exit();
}

if ($action == "report_generate") // CRM 13231
{
	$process = array(&$_POST);
	
	extract(check_magic_quote_gpc($process));
	$cbo_company_id = str_replace("'", "", $cbo_company_name);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$cbo_cust_buyer_id = str_replace("'", "", $cbo_cust_buyer_id);
	$cbo_team_leader = str_replace("'", "", $cbo_team_leader);
	$cbo_within_group = str_replace("'", "", $cbo_within_group);
	$cbo_date_range_type = str_replace("'", "", $cbo_date_range_type);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$delivery_date_cond="";$booking_date_con="";$dyeing_date_con="";$issue_date_con="";$receive_date_con="";
	if ($txt_date_from != "" && $txt_date_to != "") 
	{
		$start_date=change_date_format($txt_date_from,'','',1);
		$end_date=change_date_format($txt_date_to,'','',1);
		if ($cbo_date_range_type==1) 
		{
			$delivery_date_cond = "and a.delivery_start_date between '".$start_date."' and '".$end_date."' and a.delivery_date between '".$start_date."' and '".$end_date."'";
		}
		else
		{			
			$booking_date_con = "and a.booking_date between '".$start_date."' and '".$end_date."'";
		}
		$year_month=date('n,Y',strtotime($end_date));//
		$team_leader_year_month_cond = "and c.year_month_name='$year_month'";
		$dyeing_date_con = "and A.PROCESS_END_DATE between '".$start_date."' and '".$end_date."'";
		$issue_date_con = "and A.ISSUE_DATE between '".$start_date."' and '".$end_date."'";
		$receive_date_con = "and A.RECEIVE_DATE between '".$start_date."' and '".$end_date."'";

		$month=date('n',strtotime($end_date));
		$year=date('Y',strtotime($end_date));
		//echo $month_and_year;
		$count_days=cal_days_in_month(CAL_GREGORIAN,$month,$year);
		// echo "There was $count_days days in $month-$year.";
		$fourth_week=$count_days-21;
	}
	// echo $year_month;die;

	if ($cbo_buyer_id>0) $buyer_cond=" and a.buyer_id=$cbo_buyer_id";
	if ($cbo_cust_buyer_id>0) $cust_buyer_cond=" and a.customer_buyer in($cbo_cust_buyer_id)";
	if ($cbo_team_leader>0) $team_leader_cond=" and a.team_leader in($cbo_team_leader)";
	if ($cbo_team_leader>0) $team_leader_cond2=" and a.id=$cbo_team_leader";

	// echo $delivery_start_date_cond;die;
	// =================================================================================

	//$con = connect();
    //execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (7852)");
    //oci_commit($con);

    /* ================================================================================ /
	/								Main query start here								/
	/ ================================================================================ */	
	$fso_sql = "SELECT A.JOB_NO, A.DELIVERY_START_DATE, A.DELIVERY_DATE, A.BOOKING_DATE, A.TEAM_LEADER, B.ID, B.COLOR_TYPE_ID, B.COLOR_RANGE_ID, B.COLOR_ID, B.FINISH_QTY,B.GREY_QTY, B.RMG_QTY, B.ADJUST_GREY_QNTY, B.ADJUST_FINISH_QNTY, B.GREY_QNTY_BY_UOM, B.PP_QNTY, B.MTL_QNTY, B.FPT_QNTY, B.GPT_QNTY, B.CCMP_DATA, B.COLLER_CUFF_CONS, B.PLY 
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id=b.mst_id and a.company_id=$cbo_company_id $delivery_date_cond $booking_date_con $buyer_cond $cust_buyer_cond $team_leader_cond and a.within_group=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and A.DELIVERY_START_DATE is not null order by a.delivery_start_date";
	// echo $fso_sql;die;// and a.job_no in('LSL-FSOE-23-00121','LSL-FSOE-23-00120')
	// and a.job_no in('LSL-FSOE-23-00122','LSL-FSOE-23-00120','LSL-FSOE-23-00121','LSL-FSOE-23-00123','LSL-FSOE-23-00119')
	$fso_sql_result = sql_select($fso_sql);
	$booking_summary_arr=array();$division_wise_summary_arr=array();
	foreach ($fso_sql_result as $key => $row) 
	{
		$y_month=date('M-Y',strtotime($row['DELIVERY_START_DATE']));
		$booking_summary_arr[$y_month]['TOTAL_FINISH']+=$row['FINISH_QTY'] + $row['PP_QNTY'] + $row['MTL_QNTY'] + $row['FPT_QNTY'] + $row['GPT_QNTY'];
		$booking_summary_arr[$y_month]['GREY_QTY']+=$row['GREY_QTY'];
		if ($row['COLOR_RANGE_ID']==33) 
		{
			$booking_summary_arr[$y_month]['AOP_FINISH']+=$row['FINISH_QTY'] + $row['PP_QNTY'] + $row['MTL_QNTY'] + $row['FPT_QNTY'] + $row['GPT_QNTY'];
		}
		$division_wise_summary_arr[$y_month][$row['TEAM_LEADER']]['TOTAL_FINISH']+=$row['FINISH_QTY'] + $row['PP_QNTY'] + $row['MTL_QNTY'] + $row['FPT_QNTY'] + $row['GPT_QNTY'];

		$total_finish_qty=$row['FINISH_QTY'] + $row['PP_QNTY'] + $row['MTL_QNTY'] + $row['FPT_QNTY'] + $row['GPT_QNTY'];
		$date1 = new DateTime($row['DELIVERY_START_DATE']);
		$date2 = new DateTime($row['DELIVERY_DATE']);
		$interval = $date1->diff($date2);
		$age=$interval->days+1;
		//echo $age.'<br>==';

		$par_day_qty=$total_finish_qty/$age;
		$age_fso_arr[$row['JOB_NO']]['AGE']=$age;
		$age_fso_arr[$row['JOB_NO']]['START_DATE']=$row['DELIVERY_START_DATE'];
		$age_fso_arr[$row['JOB_NO']]['END_DATE']=$row['DELIVERY_DATE'];
		$age_fso_arr[$row['JOB_NO']]['PAR_DAY_QTY']+=$par_day_qty;
	}
	// echo "<pre>";print_r($age_fso_arr);die;

	foreach ($age_fso_arr as $fso => $value) 
	{
		$fso_age=$value['AGE'];
		for ($age2=0; $age2 < $fso_age; $age2++) 
		{
			$year_and_month=date('M-Y',strtotime('+'.$age2.' day', strtotime($value['START_DATE'])));
			$d=date('d',strtotime('+'.$age2.' day', strtotime($value['START_DATE'])));

			$week_count='';
			if ( $d>=01 && $d<=07) // 1 to 7 [1st Week]
			{
				$week_count=1;
			}
			else if($d>07 && $d<=14) // 8 to 14 [2nd Week]
			{
				$week_count=2;
			}
			else if($d>14 && $d<=21) // 15 to 21 [3rd Week]
			{
				$week_count=3;
			}
			else if($d>=22 && $d<=31) // 22 to end [4th Week]
			{
				$week_count=4;
			}
			//echo $d.' * '.$week_count.' = '.$dd.'<br>';

			$tna_data_arr[$year_and_month][$week_count]['QTY']+=$value['PAR_DAY_QTY'];
			$tna_data_arr[$year_and_month][$week_count]['FSO'].=$fso.',';
		}
	}
	//echo "<pre>";print_r($tna_data_arr);die;


	// -----------------Division Wise Booking Summary (Finish) sql Start -----------------------
	if ($txt_date_to != "") 
	{
		$team_target_sql="SELECT A.ID, A.TEAM_NAME, A.TEAM_LEADER_NAME, B.STARTING_YEAR, C.YEAR_MONTH_NAME, C.SALES_TARGET_DATE, C.SALES_TARGET_QTY
		from lib_marketing_team a, wo_sales_target_mst b, wo_sales_target_dtls c
		where a.id=b.team_leader and b.id=c.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.sales_target_qty!=0 $team_leader_year_month_cond $team_leader_cond2";
		// echo $team_target_sql;die;
		$team_target_sql_result = sql_select($team_target_sql);
		$team_arr=array();$team_sales_target_qty_arr=array();
		foreach ($team_target_sql_result as $key => $row) 
		{
			$team_arr[$row['ID']]=$row['TEAM_NAME'].'<br>['.$row['TEAM_LEADER_NAME'].']';
			$team_arr_2[$row['ID']]= $row['TEAM_NAME'];
			$team_sales_target_qty_arr[$row['ID']]+=$row['SALES_TARGET_QTY'];
		}
		$number_of_team=count($team_arr);
		// echo $number_of_team;
		// echo "<pre>";print_r($team_sales_target_qty_arr);die;
	}
	// --------------Division Wise Booking Summary (Finish) sql End -----------------------------


	// --------------Dyeing, Finish Delivery, Knitting Sql Start---------------------------------
	$dyeing_sql = "SELECT A.PROCESS_END_DATE as PRODUCTION_DATE, a.RESULT, a.SERVICE_SOURCE, b.PRODUCTION_QTY
	from PRO_FAB_SUBPROCESS a, PRO_FAB_SUBPROCESS_DTLS b 
	where a.id=b.mst_id and a.company_id=$cbo_company_id $dyeing_date_con and a.LOAD_UNLOAD_ID=2 and a.ENTRY_FORM=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.PROCESS_END_DATE";
	// echo $dyeing_sql;die;
	$dyeing_sql_result = sql_select($dyeing_sql);
	$y_month='';$month_year_arr=array(); $dyeing_in_house_arr=array();$re_dyeing_arr=array();
	foreach ($dyeing_sql_result as $key => $row) 
	{
		$y_month=date('M-Y',strtotime($row['PRODUCTION_DATE']));
		$month_year_arr[$y_month]=$y_month;

		if ($row['RESULT']==1) // Shade Matched
		{
			$dyeing_in_house_arr[$y_month]['DYEING_IN_HOUSE_QTY']+=$row['PRODUCTION_QTY'];
		}
		if ($row['RESULT']==2) // Re-Dyeing Needed
		{
			$re_dyeing_arr[$y_month]['RE_DYEING_QTY']+=$row['PRODUCTION_QTY'];
		}
	}

	$finish_roll_iss_sql = "SELECT A.ISSUE_DATE, b.QNTY from INV_ISSUE_MASTER a, PRO_ROLL_DETAILS b 
	where a.id=b.mst_id and a.company_id=$cbo_company_id $issue_date_con and a.ENTRY_FORM=318 and b.ENTRY_FORM=318 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	order by a.ISSUE_DATE";
	// echo $finish_roll_iss_sql;die;
	$finish_roll_iss_sql_result = sql_select($finish_roll_iss_sql);
	$finish_iss_qty_arr=array();
	foreach ($finish_roll_iss_sql_result as $key => $row) 
	{
		$y_month=date('M-Y',strtotime($row['ISSUE_DATE']));
		$month_year_arr[$y_month]=$y_month;

		$finish_iss_qty_arr[$y_month]['FINISH_ISS_QTY']+=$row['QNTY'];
	}
	// echo "<pre>";print_r($finish_iss_qty_arr);

	$production_roll_iss_sql = "SELECT A.RECEIVE_DATE, A.KNITTING_SOURCE, B.QNTY from INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b 
	where a.id=b.mst_id and a.company_id=$cbo_company_id $receive_date_con and a.ENTRY_FORM=2 and b.ENTRY_FORM=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	order by a.RECEIVE_DATE";
	// echo $production_roll_iss_sql;die;
	$production_roll_iss_sql_result = sql_select($production_roll_iss_sql);
	$production_in_qty_arr=array();$production_out_qty_arr=array();
	foreach ($production_roll_iss_sql_result as $key => $row) 
	{
		$y_month=date('M-Y',strtotime($row['RECEIVE_DATE']));
		$month_year_arr[$y_month]=$y_month;

		if ($row['KNITTING_SOURCE']==1) 
		{
			$production_in_qty_arr[$y_month]['IN_QNTY']+=$row['QNTY'];
		}
		else
		{
			$production_out_qty_arr[$y_month]['OUT_QNTY']+=$row['QNTY'];
		}		
	}
	// echo "<pre>";print_r($production_out_qty_arr);
	// --------------Dyeing, Finish Delivery, Knitting Sql End-----------------------------------

	$tbl_width = 500;
	$division_width=380+($number_of_team*80);
	$tbl_width4=400+(count($month_year_arr)*100);
	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="1350">
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:12px"><strong><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
				if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></strong></td>
		</tr>
	</table>

	<fieldset style="width:<?=1400+($number_of_team*100);?>px;">
		<!-- Booking Summary Start -->
		<table cellpadding="0"  width="380" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
			<thead>
				<tr>
					<th colspan="4">Booking Summary</th>
				</tr>
				<tr>
					<th width="130">Month</th> 
					<th width="80">Order Qty (Finish)</th>
					<th width="80">Grey Qty</th>
					<th>AOP (Finish)</th>
				</tr>
			</thead>
			<tbody>
				<? 
				$ftsl=2;
				foreach($booking_summary_arr as $monthKey=>$val)
				{
					if ($ftsl%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor_dyeing; ?>" onClick="change_color('tr_<? echo $ftsl; ?>','<? echo $bgcolor_dyeing; ?>')" id="tr_<? echo $ftsl; ?>">
						<td><? echo $monthKey;?></td> 
						<td align="right"><? echo number_format($val['TOTAL_FINISH'],0,".","");?></td> 
						<td align="right"><? echo number_format($val['GREY_QTY'],0,".","");?></td> 
						<td align="right"><? echo number_format($val['AOP_FINISH'],0,".","");?></td> 
					</tr>
					<?
					$ftsl++;
					$tot_total_finish+=$val['TOTAL_FINISH'];
					$tot_grey_qty+=$val['GREY_QTY'];
					$tot_aop_finish+=$val['AOP_FINISH'];
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th>Total</th> 
					<th align="right"><? echo number_format($tot_total_finish,0,".","");?></th> 
					<th align="right"><? echo number_format($tot_grey_qty,0,".","");?></th> 
					<th align="right"><? echo number_format($tot_aop_finish,0,".","");?></th> 
				</tr>
			</tfoot>
		</table>
		<!-- Booking Summary End -->

		<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
			<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
		</table>

		<!-- TNA Wise Weekly Allocation (Grey) Start -->
		<table cellpadding="0"  width="420" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
			<thead>
				<tr>
					<th colspan="5">TNA Wise Weekly Allocation (Grey)</th>
				</tr>
				<tr>
					<th width="80">Month</th>
					<th width="80">1st Week<br>[01-07]</th> 
					<th width="80">2nd Week<br>[08-14]</th>
					<th width="80">3rd Week<br>[15-21]</th>
					<th>4th Week<br>[22-30 or 31]</th>
				</tr>
			</thead>
			<tbody>
				<?
				$total_target_qty=0;
				foreach ($team_arr as $team_id => $value) 
				{
					$total_target_qty+=$team_sales_target_qty_arr[$team_id];
				}
				?>
				<tr bgcolor="#B3E6F6">
					<td width="80" title="Total Capacity/month cound days*count of week days"></td> 
					<td width="80" align="right"><strong><? echo number_format(($total_target_qty/$count_days)*7,0,".","");?></strong></td>
					<td width="80" align="right"><strong><? echo number_format(($total_target_qty/$count_days)*7,0,".","");?></strong></td>
					<td width="80" align="right"><strong><? echo number_format(($total_target_qty/$count_days)*7,0,".","");?></strong></td>
					<td align="right" title="<?=$fourth_week;?>"><strong><? echo number_format(($total_target_qty/$count_days)*$fourth_week,0,".","");?></strong></td>
				</tr>
				<?
				$l=1;
				$tot_1st_week=$tot_2nd_week=$tot_3rd_week=$tot_4th_week=0;
				foreach($tna_data_arr as $year_month_k=> $year_month_v)
				{
					if ($l%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor_dyeing; ?>" onClick="change_color('tr2_<? echo $l; ?>','<? echo $bgcolor_dyeing; ?>')" id="tr2_<? echo $l; ?>">
						<td align="center"><? echo $year_month_k;?></td>
						<td align="right"><? echo number_format($year_month_v[1][QTY],0); ?></td>
						<td align="right"><? echo number_format($year_month_v[2][QTY],0); ?></td>
						<td align="right"><? echo number_format($year_month_v[3][QTY],0); ?></td>
						<td align="right"><? echo number_format($year_month_v[4][QTY],0); ?></td>
					</tr>
					<?
					$l++;
					$tot_1st_week+=$year_month_v[1][QTY];
					$tot_2nd_week+=$year_month_v[2][QTY];
					$tot_3rd_week+=$year_month_v[3][QTY];
					$tot_4th_week+=$year_month_v[4][QTY];
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th>Total </th>
					<th><? echo number_format($tot_1st_week,0);?></th>
					<th><? echo number_format($tot_2nd_week,0);?></th>
					<th><? echo number_format($tot_3rd_week,0);?></th>
					<th><? echo number_format($tot_4th_week,0);?></th>
				</tr>
			</tfoot>
		</table>
		<!-- TNA Wise Weekly Allocation (Grey) End -->

		<table cellpadding="0"  width="3" cellspacing="0" align="left"  rules="all" border="1">
			<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
		</table>
		<?
			// echo "<pre>";
			// print_r($team_arr);
		?>

		<!-- Division Wise Booking Summary (Finish) Start -->
		<table cellpadding="0"  width="<?=$division_width;?>" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
			<thead>
				<tr>
					<th colspan="<?=2+$number_of_team;?>">Division Wise Booking Summary (Finish)</th>
				</tr>
				<tr>
					<th width="80">Month</th> 
					<?
					foreach ($team_arr_2 as $team_id => $value) 
					{
						?>
						<th width="80" title="<?=$team_id;?>"><? echo $value; ?></th>
						<?
					}
					?>
					<th width="100">Total Capacity</th> 
				</tr>
			</thead>
			<tbody>
				<? 
				$sl=1;
				?>
				<tr bgcolor="#B3E6F6">
					<td align="center"><b>Capacity</b></td>
					<?
					$total_target_qty=0;
					foreach ($team_arr as $team_id => $value) 
					{
						?>
						<td width="100" align="right" title="<?=$team_id;?>"><strong><? echo number_format($team_sales_target_qty_arr[$team_id],0,".",""); ?></strong></td>
						<?
						$total_target_qty+=$team_sales_target_qty_arr[$team_id];
					}
					?>
					<td align="right"><strong><? echo number_format($total_target_qty,0,".",""); ?></strong></td>
				</tr>
				<?
				foreach($booking_summary_arr as $month_year=> $bs)
				{
					
					if ($sl%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr1_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr1_<? echo $sl; ?>">
						<td align="center"><? echo $month_year;?></td>
						<?
						foreach ($team_arr as $team_id => $value) 
						{
							?>
							<td width="100" align="right" title="<?=$team_id;?>"><? echo number_format($division_wise_summary_arr[$month_year][$team_id]['TOTAL_FINISH'],0,".",""); ?></td>
							<?
							$team_wise_tot_qty[$team_id]+=$division_wise_summary_arr[$month_year][$team_id]['TOTAL_FINISH'];
						}
						?>
						<td align="right"></td>
					</tr>
					<?
					$sl++;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th>Total </th>
					<?
					foreach ($team_arr as $team_id_key => $value) 
					{
						?>
						<th width="100"><? echo $team_wise_tot_qty[$team_id_key];  ?></th>
						<?
					}
					?>
					<th></th>
				</tr>
			</tfoot>
		</table>
		<!-- Division Wise Booking Summary (Finish) End -->

		<div align="left">
			<table class="rpt_table" width="1500" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
			</table>
		</div>
		<br/>

		<!-- Dyeing, Finish Delivery, Knitting Start -->
		<div align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="<? echo $tbl_width4; //echo $tbl_width; ?>" class="rpt_table">
				<thead>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<?
						foreach ($month_year_arr as $month_y => $value) 
						{
							?>
							<th width="100"><? echo $value; ?></th>
							<?
						}
						?>
						<th width="100">Average</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?= $tbl_width4+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
	            <table width="<?= $tbl_width4; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<tr>
							<td width="100" rowspan="2" valign="middle" align="center">Dyeing</td>
							<td width="100">In House</td>
							<?
							foreach ($month_year_arr as $month_y => $value) 
							{ 
								?>
								<td width="100" align="right"><? echo number_format($dyeing_in_house_arr[$month_y]['DYEING_IN_HOUSE_QTY'],0,".",""); ?></td>
								<?
								$tot_dyeing_in_house+=$dyeing_in_house_arr[$month_y]['DYEING_IN_HOUSE_QTY'];
							}
							?>
							<td width="100" align="right"><? echo number_format($tot_dyeing_in_house/count($month_year_arr),0,".","");?></td>
						</tr>
						<tr>
							<td width="100">Re-Dyeing</td>
							<?
							foreach ($month_year_arr as $month_y => $value) 
							{
								?>
								<td width="100" align="right"><? echo number_format($re_dyeing_arr[$month_y]['RE_DYEING_QTY'],0,".",""); ?></td>
								<?
								$tot_re_dyeing+=$re_dyeing_arr[$month_y]['RE_DYEING_QTY'];
							}
							?>
							<td width="100" align="right"><? echo number_format($tot_re_dyeing/count($month_year_arr),0,".","");?></td>
						</tr>
						<tr>
							<td width="100" valign="middle" align="center">Finish Delivery</td>
							<td width="100">In House</td>
							<?
							foreach ($month_year_arr as $month_y => $value) 
							{
								?>
								<td width="100" align="right"><? echo number_format($finish_iss_qty_arr[$month_y]['FINISH_ISS_QTY'],0,".",""); ?></td>
								<?
								$tot_fin_qty+=$finish_iss_qty_arr[$month_y]['FINISH_ISS_QTY'];
							}
							?>
							<td width="100" align="right"><? echo number_format($tot_fin_qty/count($month_year_arr),0,".",""); ?></td>
						</tr>
						<tr>
							<td width="100" rowspan="2" valign="middle" align="center">Knitting</td>
							<td width="100">In House</td>
							<?
							foreach ($month_year_arr as $month_y => $value) 
							{
								?>
								<td width="100" align="right"><? echo number_format($production_in_qty_arr[$month_y]['IN_QNTY'],0,".",""); ?></td>
								<?
								$tot_production_in_qty+=$production_in_qty_arr[$month_y]['IN_QNTY'];
							}
							?>
							<td width="100" align="right"><? echo number_format($tot_production_in_qty/count($month_year_arr),0,".",""); ?></td>
						</tr>
						<tr>
							<td width="100">Out-Bound</td>
							<?
							foreach ($month_year_arr as $month_y => $value) 
							{
								?>
								<td width="100" align="right"><? echo number_format($production_out_qty_arr[$y_month]['OUT_QNTY'],0,".",""); ?></td>
								<?
								$tot_production_out_qty+=$production_out_qty_arr[$y_month]['OUT_QNTY'];
							}
							?>
							<td width="100" align="right"><? echo number_format($tot_production_out_qty/count($month_year_arr),0,".",""); ?></td>
						</tr>
					</tbody>
					<!-- <tfoot>
						<tr class="tbl_bottom">
							<th width="100" align="right"></th>
							<th width="100" align="right">Total</th>
							<?
							foreach ($month_year_arr as $month_y => $value) 
							{
								?>
								<th width="100" align="right"><? //echo $value; ?></th>
								<?
							}
							?>
							<td width="100">&nbsp;</td>
						</tr>
					</tfoot> -->
				</table>
			</div>
		</div>
		<!-- Dyeing, Finish Delivery, Knitting End -->
	</fieldset>
	<br>
	<?
	foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}
	$name = time();
	$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
	echo "$total_data####$filename";
	exit();
}
?>