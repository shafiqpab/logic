<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_company_machine")
{
    extract($_REQUEST);

    $mc_sql="SELECT id,machine_no from lib_machine_name where category_id=2 and company_id in($choosenCompany) and status_active=1 and is_deleted=0 and prod_capacity is not null";

    echo create_drop_down("cbo_machine_name", 150, $mc_sql, "id,machine_no", 0, "-- Select --", $selected, "", "");

    exit();
}

if ($action == "report_generate")
{
	$process = array(&$_POST);

	extract(check_magic_quote_gpc($process));
	$cbo_company_id = str_replace("'", "", $cbo_company_name);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");

	function weekOfMonth($date) {
		//Get the first day of the month.
		$firstOfMonth = strtotime(date("Y-m-01", $date));
		//Apply above formula.
		return weekOfYear($date) - weekOfYear($firstOfMonth) + 1;
	}

	function weekOfYear($date) {
		$weekOfYear = intval(date("W", $date));
		if (date('n', $date) == "1" && $weekOfYear > 51) {
			// It's the last week of the previos year.
			return 0;
		}
		else if (date('n', $date) == "12" && $weekOfYear == 1) {
			// It's the first week of the next year.
			return 53;
		}
		else {
			// It's a "normal" week.
			return $weekOfYear;
		}
	}

	$date_cond="";
	if ($txt_date_from != "" && $txt_date_to != "")
	{
		$start_date=change_date_format($txt_date_from,'','',1);
		$end_date=change_date_format($txt_date_to,'','',1);

		$date_cond = "and A.ACTUAL_FINISH_DATE between '".$start_date."' and '".$end_date."'";
	}
	// echo $year_month;die;
	// ================================================================================

    /* ================================================================================ /
	/								Main query start here								/
	/  ================================================================================*/

	"SELECT a.ACTUAL_FINISH_DATE, b.GREY_FAB_QNTY, b.booking_no
    FROM TNA_PROCESS_MST a, WO_BOOKING_DTLS b, WO_BOOKING_MST c, wo_pre_cost_fabric_cost_dtls d
   WHERE     a.PO_NUMBER_ID = b.PO_BREAK_DOWN_ID
         AND b.BOOKING_MST_ID = c.id
         --and c.job_no=d.JOB_NO
         --and b.job_no=d.JOB_NO
         and d.id=b.pre_cost_fabric_cost_dtls_id
         AND a.TASK_NUMBER = 31
         AND TASK_TYPE = 1
         AND c.FABRIC_SOURCE = 1
         AND b.BOOKING_TYPE IN (1, 4)
         AND a.status_active = 1
         AND a.is_deleted = 0
         AND b.status_active = 1
         AND b.is_deleted = 0
         AND c.status_active = 1
         AND c.is_deleted = 0
         AND c.COMPANY_ID = 17
         AND A.ACTUAL_FINISH_DATE BETWEEN '01-Nov-2023' AND '30-Nov-2023'
ORDER BY a.ACTUAL_FINISH_DATE";
	$sql="SELECT a.ACTUAL_FINISH_DATE, b.GREY_FAB_QNTY
	FROM TNA_PROCESS_MST a, WO_BOOKING_DTLS b, WO_BOOKING_MST c
	where a.PO_NUMBER_ID=b.PO_BREAK_DOWN_ID and b.BOOKING_MST_ID=c.id and a.TASK_NUMBER=31 AND TASK_TYPE = 1 and c.FABRIC_SOURCE=1 and b.BOOKING_TYPE in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.COMPANY_ID=$cbo_company_id $date_cond order by a.ACTUAL_FINISH_DATE";
	//echo $sql;
	$sql_result = sql_select($sql);
	$y_month='';$month_year_arr=array(); $m_booked_qty_arr=array(); $days_arr=array();
	foreach ($sql_result as $key => $row)
	{
		$y_month=date('M-Y',strtotime($row['ACTUAL_FINISH_DATE']));
		$y_month1=date('Y-M',strtotime($row['ACTUAL_FINISH_DATE']));

		$days=date('Y-m-d-D',strtotime($row['ACTUAL_FINISH_DATE']));
		//$weeks=date("Y-m-W",strtotime($row['ACTUAL_FINISH_DATE']));
		$weeks=weekOfMonth(strtotime($row['ACTUAL_FINISH_DATE']));

		$month_year_arr[$y_month]=$y_month;
		$days_arr[$days]=$days;
		$weeks_arr[$y_month1.'-'.$weeks]=$y_month1.'-'.$weeks;

		$m_booked_qty_arr[$y_month]['BOOKED_QTY']+=$row['GREY_FAB_QNTY'];
		$days_booked_qty_arr[$days]['BOOKED_QTY']+=$row['GREY_FAB_QNTY'];
	}
	echo "<pre>";print_r($weeks_arr);
	// --------------Main query End-----------------------------------

	// MC_CAPACITY SQL
	$machine_sql="SELECT prod_capacity as MC_CAPACITY from lib_machine_name where category_id=2 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 and prod_capacity is not null";
	// echo $machine_sql;
	$machine_sql_result = sql_select($machine_sql);
	$mc_capacity=0;
	foreach ($machine_sql_result as $key => $row)
	{
		$mc_capacity+=$row['MC_CAPACITY'];
	}
	// echo $mc_capacity;

	$tbl_width4=300+(count($month_year_arr)*160);
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

	<fieldset style="width:<?= $tbl_width4;?>px;">
		<div align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="<? echo $tbl_width4; //echo $tbl_width; ?>" class="rpt_table">
				<thead>
					<tr>
						<th width="100" rowspan="2">Dyeing Unit</th>
						<th width="100" rowspan="2">Machine  Capacity</th>
						<?
						foreach ($month_year_arr as $month_year => $value)
						{
							?>
							<th width="<?=80;?>" colspan="2"><? echo $value; ?></th>
							<?
						}
						?>
						<th width="" rowspan="2">Varience Total</th>
					</tr>
					<tr>
						<?
						foreach ($month_year_arr as $month_year => $value)
						{
							?>
							<th width="80">Booked</th>
							<th width="80">Varience</th>
							<?
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="width:<?= $tbl_width4+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
	            <table width="<?= $tbl_width4; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<tr>
							<td width="100" valign="middle" align="center">All</td>
							<td width="100" align="right"><?=number_format($mc_capacity,2,".","");?></td>
							<?
							$tot_varience=0;
							foreach ($month_year_arr as $month_year => $value)
							{
								?>
								<td width="80" align="right"><? echo number_format($m_booked_qty_arr[$month_year]['BOOKED_QTY'],2,".",""); ?></td>
								<td width="80" align="right"><? echo number_format($m_booked_qty_arr[$month_year]['BOOKED_QTY']-$mc_capacity,2,".",""); ?></td>
								<?
								//$tot_booked_qty[$month_year]+=$booked_qty_arr[$month_year]['BOOKED_QTY'];
								$tot_varience+=$m_booked_qty_arr[$month_year]['BOOKED_QTY']-$mc_capacity;
							}
							?>
							<td width="" align="right"><? echo number_format($tot_varience,2,".","");?></td>
						</tr>
					</tbody>

				</table>
			</div>
		</div>
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