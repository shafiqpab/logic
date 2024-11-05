<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",trim($cbo_company_name));
	$max_height=str_replace("'","",trim($max_height));
	$txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
    	
    $cbo_company_id = return_field_value('id', 'lib_company',"company_name='$cbo_company_name'")*1;
    $buyer_library  = return_library_array('select id, short_name from lib_buyer where status_active=1', 'id', 'short_name');
    $floor_library  = return_library_array("select id, floor_name from lib_prod_floor where status_active=1", "id", "floor_name");
	
	$lineArr=array();
    $sql_line = sql_select("SELECT ID, LINE_NAME, SEWING_GROUP from lib_sewing_line where company_name='$cbo_company_id' and status_active=1 and is_deleted=0 order by id");
	foreach ( $sql_line as $row)
	{
		$lineArr[$row['ID']]['line_name']    = $row['LINE_NAME'];
		$lineArr[$row['ID']]['sewing_group'] = $row['SEWING_GROUP'];
	}
	//echo '<pre>';print_r($lineArr);die;
	
    // Check Variable Setting Use Prod Resource Allocation
    //$prod_reso_allo=return_field_value('auto_update','variable_settings_production',"variable_list=23 and is_deleted=0 and status_active=1");
    $prod_reso_allo=1;  // default set
	
	$sql_resource = "SELECT a.ID, a.COMPANY_ID, a.LOCATION_ID, a.FLOOR_ID, a.LINE_NUMBER, b.PR_DATE, b.TARGET_PER_HOUR, b.WORKING_HOUR, b.MAN_POWER, b.STYLE_REF_ID, b.ACTIVE_MACHINE, b.OPERATOR, b.HELPER, b.IRON_MAN, c.TARGET_EFFICIENCY from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and b.pr_date='$txt_date' and a.company_id='$cbo_company_id' and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0";
	$sql_resource_res=sql_select($sql_resource);
	$prod_resource_array=array();
	$sewing_group=$line_id_first="";
	$production_data=array();
	$actual_resource_line_arr=array();
	foreach($sql_resource_res as $row)
	{
		$prod_resource_array[$row['ID']]['line_number'] = $row['LINE_NUMBER'];
		$lineResource_arr = explode(",",$row['LINE_NUMBER']);
		
		$line_id_first=$lineResource_arr[0];		
		$sewing_group=$lineArr[$line_id_first]['sewing_group'];
		$actual_resource_line_arr[$sewing_group]++;

		$production_data[$sewing_group]["operator"]+=$row['OPERATOR'];
		$production_data[$sewing_group]["helper"]+=$row['HELPER'];
		$production_data[$sewing_group]["iron_man"]+=$row['IRON_MAN'];
		$production_data[$sewing_group]["active_machine"]+=$row['ACTIVE_MACHINE'];
		$production_data[$sewing_group]["hourly_target"]+=$row['TARGET_PER_HOUR'];
		$production_data[$sewing_group]["working_hour"]+=$row['WORKING_HOUR'];
		$production_data[$sewing_group]["man_power"]+=$row['MAN_POWER'];			
		$production_data[$sewing_group]["planned"]+=$row['TARGET_EFFICIENCY'];
		$production_data[$sewing_group]["target_efficiency"]+=$row['TARGET_EFFICIENCY'];
		$production_data[$sewing_group]["day_terget"]+=$row['TARGET_PER_HOUR']*$row['WORKING_HOUR'];
	}
	unset($sql_resource_res);
	//echo "<pre>";print_r($production_data);die;
	
	$total_line_resource=array_sum($actual_resource_line_arr);

	$company_id_cond=$txt_date_cond="";
	if ($cbo_company_id >0 ) $company_id_cond=" and a.serving_company=$cbo_company_id";
	if ($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";

	$start_time_data_arr=sql_select("SELECT COMPANY_NAME, SHIFT_ID, TO_CHAR(prod_start_time,'HH24:MI') as PROD_START_TIME, TO_CHAR(prod_end_time,'HH24:MI') as PROD_END_TIME, TO_CHAR(lunch_start_time,'HH24:MI') as LUNCH_START_TIME, TO_CHAR(lunch_end_time,'HH24:MI') as LUNCH_END_TIME from variable_settings_production where  company_name='$cbo_company_id' and variable_list=26 and status_active=1 and is_deleted=0");
	$start_time_arr=array();
	$lunch_difference_arr=array();
	$lunch_difference_arr2=array();
	foreach($start_time_data_arr as $row)
	{
		if ($row['PROD_START_TIME'] != "") {
			$start_time_arr[$row['SHIFT_ID']]['pst']=$row['PROD_START_TIME'];
			$start_time_arr[$row['SHIFT_ID']]['pet']=$row['PROD_END_TIME'];
			$start_time_arr[$row['SHIFT_ID']]['lst']=$row['LUNCH_START_TIME'];
			$start_time_arr[$row['SHIFT_ID']]['let']=$row['LUNCH_END_TIME'];
			$ex_lunch_start_time= explode(':', $row['LUNCH_START_TIME']);
			$lunch_hour   = (int)$ex_lunch_start_time[0];
			$lunch_minute = (int)$ex_lunch_start_time[1];
			$lunch_start_time = $lunch_hour*60+$lunch_minute;
			$lunch_duration = date("i",(strtotime($row['LUNCH_START_TIME'])-strtotime($row['LUNCH_END_TIME'])));
			$lunch_difference_arr[$lunch_start_time] = $lunch_duration;
			$lunch_difference_arr2[$lunch_hour]['hour'] = $lunch_hour;
			$lunch_difference_arr2[$lunch_hour]['duration'] = $lunch_duration;
		}
	}
	
	$prod_start_hour=$start_time_arr[1]['pst'];
	if($prod_start_hour=='') $prod_start_hour='06:00';
	$start_time=explode(':',$prod_start_hour);
	$hour=(int)substr($start_time[0],1,1);

	$minutes=$start_time[1]; $last_hour=23;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;

	for($j=$hour;$j<$last_hour;$j++)
	{		
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
    $start_hour_arr[$j+1]='23:59';
	
	$sql="SELECT a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id as po_id, a.sewing_line, sum(a.production_quantity) as good_qnty, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down as smv_pcs_set, c.po_number,";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$bg_hour=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour='prod_hour'.substr($bg_hour,0,2);
		$alter_hour="alter_hour".substr($bg_hour,0,2);
		$spot_hour="spot_hour".substr($bg_hour,0,2);
		$reject_hour="reject_hour".substr($bg_hour,0,2);

		if($first==1)
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN production_quantity else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN alter_qnty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN reject_qnty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN spot_qnty else 0 END) AS $spot_hour,";
		}
		else
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN production_quantity else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN alter_qnty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN reject_qnty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' THEN spot_qnty else 0 END) AS $spot_hour,";
		}
		$first=$first+1;
	}
	$prod_hour='prod_hour'.$last_hour;
	$alter_hour="alter_hour".$last_hour;
	$spot_hour="spot_hour".$last_hour;
	$reject_hour="reject_hour".$last_hour;

	$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' THEN production_quantity else 0 END) AS $prod_hour,
		sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' THEN alter_qnty else 0 END) AS $alter_hour,
		sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'THEN reject_qnty else 0 END) AS $reject_hour,
		sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'THEN spot_qnty else 0 END) AS $spot_hour";
														
	$sql.=" FROM pro_garments_production_mst a, wo_po_break_down c, wo_po_details_master b
		where a.po_break_down_id=c.id and c.job_id=b.id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 $txt_date_cond $company_id_cond
		group by a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number
		order by a.sewing_line"; 	
	//echo $sql;die;
	
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$poId .= $row[csf("po_id")].",";		
	}
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));

	$smv_source=sql_select("SELECT COMPANY_NAME, SMV_SOURCE from variable_settings_production where variable_list=25 and status_active=1 and is_deleted=0 and company_name=$cbo_company_id");
	foreach($smv_source as $val){
		$smv_source=$val['SMV_SOURCE'];
	}
	//echo $smv_source;
	$sql_item="SELECT b.id as COLOR_SIZE_ID, b.PO_BREAK_DOWN_ID, b.ORDER_QUANTITY, c.GMTS_ITEM_ID, c.SMV_PCS, c.SMV_PCS_PRECOST from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and b.is_deleted=0 and b.po_break_down_id in($order_ids)";

	$resultItem=sql_select($sql_item);
	$item_smv_array=array();
	$check_color_size_arr=array();
	foreach($resultItem as $row)
	{
		if ($check_color_size_arr[$row['COLOR_SIZE_ID']]=="")
		{
			if($smv_source==1)
			{
				$item_smv_array[$row['PO_BREAK_DOWN_ID']][$row['GMTS_ITEM_ID']]['smv']=$row['SMV_PCS'];
				$item_smv_array[$row['PO_BREAK_DOWN_ID']][$row['GMTS_ITEM_ID']]['order_qty']+=$row['ORDER_QUANTITY'];
			}
			else if($smv_source==2)
			{
				$item_smv_array[$row['PO_BREAK_DOWN_ID']][$row['GMTS_ITEM_ID']]['smv']=$row['SMV_PCS_PRECOST'];
				$item_smv_array[$row['PO_BREAK_DOWN_ID']][$row['GMTS_ITEM_ID']]['order_qty']+=$row['ORDER_QUANTITY'];
			}
			$check_color_size_arr[$row['COLOR_SIZE_ID']]=$row['COLOR_SIZE_ID'];
		}		
	}
	unset($resultItem);
	//echo '<pre>';print_r($item_smv_array);

	$item_count=0;
	$total_smv=$tot_smv=$total_order_qty=0;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$totalAlterQnt = $totalSpotQnt = $totalRejectQnt = 0;
	$totalGoodAlrSpotRejectQnty=0;
	$day_terget=$current_target=0;
	$varriance=$trend=0;
	$planned=$countLineNumber=0;
	$operator=0;
	$sewing_group='';

	$check_line_number_arr=array();
	$check_item_number_arr=array();
	foreach($result as $row)
	{
		$count_hour=0;	
		
		$poId .= $row[csf("po_id")].",";
		$floor_name .= $floor_library[$row[csf('floor_id')]].',';
		$buyer_name .= $buyer_library[$row[csf('buyer_name')]].',';
		$style_ref_no .= $row[csf('style_ref_no')].',';
		$job_no .= $row[csf('job_no')].',';
		$po_number .= $row[csf('po_number')].',';
		

		$line_resource_mst_arr = explode(",",$prod_resource_array[$row[csf('sewing_line')]]['line_number']);
		$line_id_first=$line_resource_mst_arr[0];		

		$sewing_group=$lineArr[$line_id_first]['sewing_group'];
		
		$tot_smv+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
		$total_order_qty+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

		$production_data[$sewing_group]["group_wise_total_smv"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
		$production_data[$sewing_group]["group_wise_total_order_qty"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

		if ($check_line_number_arr[$row[csf('sewing_line')]] == '' )
		{
			$countLineNumber++;
			$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];			
			$production_data[$sewing_group]["countLineNumber"]++;
			$production_data[$sewing_group]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
			$production_data[$sewing_group]["po_number"].=$row[csf("po_number")].',';
			$production_data[$sewing_group]["style_ref_no"]=$row[csf("style_ref_no")].',';
			$production_data[$sewing_group]["job_no"].=$row[csf('job_no')].',';
			$production_data[$sewing_group]["floor"]=$floor_library[$row[csf('floor_id')]].',';			
		}

		for($h=$hour; $h<$last_hour; $h++)
		{		
					
			//if ($h == 14) continue;
			$count_hour++;

			$bg=$start_hour_arr[$h];
			$bg_hour=$start_hour_arr[$h];
			$prod_hour="prod_hour".substr($bg_hour,0,2);
			$alter_hour="alter_hour".substr($bg_hour,0,2);
			$spot_hour="spot_hour".substr($bg_hour,0,2);
			$reject_hour="reject_hour".substr($bg_hour,0,2);				

			//$production_data[$sewing_group]["current_target"]+=$hourly_target;			
			$production_data[$sewing_group]["$prod_hour"]+=$row[csf("$prod_hour")];
			$production_data[$sewing_group]["$alter_hour"]+=$row[csf("$alter_hour")];
			$production_data[$sewing_group]["$spot_hour"]+=$row[csf("$spot_hour")];
			$production_data[$sewing_group]["$reject_hour"]+=$row[csf("$reject_hour")];
			$totalProdQnty += $row[csf($prod_hour)];
			$totalAlterQnty += $row[csf($alter_hour)];
			$totalSpotQnty += $row[csf($spot_hour)];
			$totalRejectQnty +=$row[csf($reject_hour)];

			$production_data[$sewing_group]["totalProdQnty"]+=$row[csf("$prod_hour")];
			$production_data[$sewing_group]["totalAlterQnty"]+=$row[csf("$alter_hour")];
			$production_data[$sewing_group]["totalSpotQnty"]+=$row[csf("$spot_hour")];
			$production_data[$sewing_group]["totalRejectQnty"]+=$row[csf("$reject_hour")];

			//if ($h == 15 && date('G') == 14) break;
			if (date('G') == $h) break;
			
		}
	}


	$operator=$helper=$total_smv_avg=$lineWiseTotalSmv=0;
	//$total_smv_avg=$total_smv/$item_count;
	//echo $tot_smv.'**'.$total_order_qty;
	$total_smv_avg=$tot_smv/$total_order_qty;
	foreach ($production_data as $key => $val) 
	{
		$day_terget+=$val['day_terget'];
		$current_target+=$val['hourly_target']*$count_hour;
		$operator+=$val['operator'];
		$helper+=$val['helper'];
		$planned+=$val['planned'];
		$hourly_target+=$val['hourly_target'];
	}
	//echo $day_terget.'**'.$current_target;

	//echo '<pre>';print_r($production_data);

	//266336**83580

	// Avoid Lunch Time
	$lunch_break_time=0;
	$lunch_break_time2=0;
	$lunch_break_time_day=0;
	$today_total_minutes=(int)date("H")*60+(int)date("i");	

	//echo '<pre>';print_r($lunch_difference_arr);
	$total_break_time=0;
	foreach ($lunch_difference_arr as $minutes => $break_time)
	{
		if ($today_total_minutes>=$minutes){
			$lunch_break_time+=floor(($hourly_target/60)*$break_time);
			$lunch_break_time2+=$break_time/60;
		}
		$total_break_time+=$break_time;
	}

	$lunch_break_time_day=floor(($hourly_target/60)*$total_break_time);
	$day_terget=$day_terget-$lunch_break_time_day;
	$current_target=$current_target-$lunch_break_time;
	$varriance=$current_target-$totalProdQnty;
	$trend=$totalProdQnty/($count_hour-1-$lunch_break_time2)*15;

	$totalGoodAlrSpotQnty=0;
	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$totalAlrSpotQnty=$totalAlterQnty+$totalSpotQnty;
	$dhu = ($totalAlrSpotQnty/$totalProdQnty)*100;
	$efficiency=(($totalProdQnty*$total_smv_avg)/($operator*($count_hour-1-$lunch_break_time2)*60))*100;	

    $com_group_name=return_field_value("company_name", "lib_company", "status_active=1 and is_deleted=0");

	$sql_defect = "SELECT a.sewing_line,sum(case when b.defect_type_id=3 then b.defect_qty else 0 end) as alter_defect_qty, sum(case when b.defect_type_id=4 then b.defect_qty else 0 end) as spot_defect_qty
		FROM pro_garments_production_mst a, pro_gmts_prod_dft b
		WHERE a.id=b.mst_id and a.production_type=5 and a.po_break_down_id in($order_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.defect_type_id in(3,4) $company_id_cond $txt_date_cond
		group by a.sewing_line";
	// echo $sql_defect;die;	
	$sql_defect_res=sql_select($sql_defect);
	$totAlterDefectQnty=0;
	$totSpotDefectQnty=0;
	$defectDataArray = array();
	foreach ($sql_defect_res as $row) 
	{
		$line_resource_mst_arr = explode(",",$prod_resource_array[$row[csf('sewing_line')]]['line_number']);
		$line_id_first=$line_resource_mst_arr[0];		

		$sewing_group=$lineArr[$line_id_first]['sewing_group'];

		$totAlterDefectQnty += $row[csf('alter_defect_qty')];
		$totSpotDefectQnty += $row[csf('spot_defect_qty')];

		$defectDataArray[$sewing_group]['alter'] += $row[csf('alter_defect_qty')];
		$defectDataArray[$sewing_group]['spot'] += $row[csf('spot_defect_qty')];
	}

   
	?>

	<style type="text/css">
		
		/* #secondtime  {
		    border: 3px solid black;
		    border-collapse: collapse;
		} */

		.rpt_table td {
		    border: 3px solid #000;
		    padding-left: 1px;
		}
		
		
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="firsttime">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height: 8%; background-color:#FF0000;">
    			<td style="width: 29%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">KPI Dashboard</td>
    			<td style="width: 31%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= $com_group_name; ?> </td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= $count_hour.'Hr'; ?></td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 5%;">
    			<td style="color: white; font-weight:bold; text-align: left; vertical-align: middle;">
    				<p style="font-size: 2vw;">PRODUCTION</p>
    			</td>
    			<td rowspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 35%; text-align: left;">Day Target</p>
    				<p style="font-size: 6vw; height: 65%; text-align: right; font-weight:900;" title="Hourly Target*Working Hour"><?= $day_terget; ?>&nbsp;</p>
    			</td>
    			<td rowspan="3" colspan="2" style="color: white; font-size: 2vw; font-weight:bold;">
    				<table style="width: 100%;height: 100%;">
    					<tr>
    						<td colspan="2" style="border:0; width: 100%; font-size: 2vw;" align="left" >Efficiency</td>
    					</tr>
    					<tr>	
    						<td colspan="2" style="border:0;" align="center" title="<? echo '(('.$totalProdQnty.'*'.$total_smv_avg.')/('.$operator.'*('.$count_hour.'-1-'.$lunch_break_time2.')*60))*100'; ?>">
    							<canvas id="canvas" style="width:100%; max-width:100%"></canvas>
    							<input type="hidden" id="efficiency" value="<?= floor($efficiency); ?>">
    						</td>		
    					</tr>
    					<tr>
    						<td colspan="2" style="border:0; text-align: center; width: 100%" align="center" ><p style="font-size: 2vw; text-align: center; vertical-align: middle;"><span>Planned</span>&nbsp;<span style="font-weight:900;" title="Total Target Efficiency/Total Line Number"><?= number_format($planned/$countLineNumber,2); ?>%</span></p>
    						</td>
    					</tr>
    				</table>     					
    			</td>
			</tr>

			<tr style="height: 25%;">
    			<td style="color: white; font-weight:bold; font-weight:bold;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Target</p>
    				<p style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;" title="Hourly Target*Count Hour=<? echo $hourly_target.'*'.$count_hour; ?>"><?= $current_target; ?>&nbsp;</p>
    			</td>    			
			</tr>
			<tr style="height: 25%;">
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Actual</p>
    				<?
	        		if ($current_target<=$totalProdQnty)
	        		{
	        			?>
    					<div style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: right;"><?= $totalProdQnty; ?></span>&nbsp;<span style="width: 20%; height: 80%; text-align: right; color: green;">&#9650;</span></div>
    					<?
    				}
    				else
    				{
    					?>
    					<div style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: right;"><?= $totalProdQnty; ?></span><span style="width: 20%; height: 80%; text-align: right; color: red;">&#9660;</span></div>
    					<?
    				}
    				?>	
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Trend</p>
    				<p style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;" title="<? echo $totalProdQnty.'/('.$count_hour.'-1-'.$lunch_break_time2.')*15'; ?>"><?= round($trend); ?>&nbsp;</p>
    			</td>
			</tr>
			<tr style="height: 25%;">
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: right; vertical-align: middle;">    				
    				<p style="font-size: 2vw; height: 20%; text-align: left; font-weight:bold;">Variance</p>
    				<?
	        		if ($varriance < 0)
	        		{
	        			?>
	        			<div style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;" title="Target-Total Prod Qty=<? echo $current_target.'-'.$totalProdQnty; ?>"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; color: green; font-weight:bold;">&#9650;</span></div>        			
	        			<?
	        		}
	        		else
	        		{
	        			?>
	        			<div style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;" title="Target-Total Prod Qty=<? echo $current_target.'-'.$totalProdQnty; ?>"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; color: red; font-weight:bold;">&#9660;</span></div>
	        			<?
	        		}
	        		?>    				
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: right; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Defective%</p>
    				<p style="font-size: 6vw; height: 80%; text-align: right; color: red; font-weight:900;" title="(Total Alter, Spot Qty/Total Prod)*100=<? echo '('.$totalAlrSpotQnty.'/'.$totalProdQnty.')*100'; ?>"><?= trim(number_format($dhu,2),'-'); ?>%&nbsp;</p>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold;">
    				<p style="height: 20%;">&nbsp;</p>
    				<p style="font-size: 6vw; height: 60%; text-align: center; font-weight:900;"><?= $operator; ?></p>
    				<p style="font-size: 2vw; height: 40%; text-align: center;">Operator</p>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold;">
    				<p style="height: 20%;">&nbsp;</p>
    				<p style="font-size: 6vw; height: 60%; text-align: center; font-weight:900;"><?= $helper; ?></p>
    				<p style="font-size: 2vw; height: 40%; text-align: center;">Helper</p>
    			</td>
			</tr>
			<tr style="height: 8%;">
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 1.7vw; text-align: center; "><span>Total Line:</span>&nbsp;<span style="font-weight:900;"><?= $countLineNumber.'/'.$total_line_resource; ?>,</span>&nbsp;<span>SMV Avg:</span>&nbsp;<span style="font-weight:900;" title="(Total SMV/Total Order Qty)/Total Order Qty"><?= number_format($total_smv_avg,2); ?></span></p>
    			</td>
    			<td colspan="3" style="color: white; font-size: 2vw; font-weight:850; text-align: center; vertical-align: middle; background-color: #FF0000;">
    				<p><span>Alter:&nbsp;</span><span style="font-weight: 850;"><?=$totAlterDefectQnty; ?>;</span><span>&nbsp;&nbsp;Spot:&nbsp;</span><span style="font-weight: 850;"><?=$totSpotDefectQnty; ?>;</span>&nbsp;&nbsp;Reject(Pcs):&nbsp;<span style="font-weight: 850;"><?= $totalRejectQnty; ?>;</span><br><span>&nbsp;&nbsp;TTL No of Defects:&nbsp;</span><span style="font-weight: 850;"><?= ($totAlterDefectQnty+$totSpotDefectQnty); ?>;</span><span>&nbsp;&nbsp;TTL Defective Qty(Pcs):&nbsp;</span><span style="font-weight: 850;"><?= ($totalAlterQnty+$totalSpotQnty); ?></span></p>
    			</td>
			</tr>
		</table>        
    </div>

	<!-- ================================ 2nd part start =========================== -->

    <div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="secondtime">
		<div style="width:100%; height:3%; font-size: 1.2vw;">
			<div style="width:70%; float:left; font-weight:bold;"><strong>GROUP WISE PRODUCTION DASHBOARD</strong></div>
			<div style="width:10%; float:left; font-weight:bold;"><strong><?= $count_hour.'Hr'; ?></strong></div>
			<div style="width:20%; float:left; font-weight:bold;"><strong><? echo date("F d, Y h:i A"); ?></strong></div>
		</div>
		<div style="width:100%; height:97%;">
		<?
		$groupWiseTotalSmvAvg=$efficiency=$trend=$planned=$varriance=$dhu=0;
		ksort($production_data);
		//echo '<pre>';print_r($production_data);
		$day_terget=$current_target=0;
		$totalAlrSpotQnty=0;
		$border=1;
		$border_color="";

		foreach ($production_data as $group_name => $row)
		{
			$lunch_break_time_board2=$lunch_break_time2_board2=$lunch_break_time_day_board2=0;
			$total_break_time=0;
			foreach ($lunch_difference_arr as $minutes => $break_time)
			{
				if ($today_total_minutes>=$minutes){
					$lunch_break_time_board2+=floor(($row['hourly_target']/60)*$break_time);
					$lunch_break_time2_board2+=$break_time/60;
				}
				$total_break_time+=$break_time;
			}
			
			$lunch_break_time_day_board2=floor(($row['hourly_target']/60)*$total_break_time);			
			$groupWiseTotalSmvAvg=number_format(($row['group_wise_total_smv']/$row['group_wise_total_order_qty']),2);

			$day_terget=$row['day_terget']-$lunch_break_time_day_board2;
			$current_target=($row['hourly_target']*$count_hour)-$lunch_break_time_board2;
			//echo $groupWiseTotalSmvAvg.'syastem';

			$efficiency=(($row['totalProdQnty']*$groupWiseTotalSmvAvg)/($row['operator']*($count_hour-1-$lunch_break_time2_board2)*60))*100;
			//echo $row['totalProdQnty'].'*'.$groupWiseTotalSmvAvg.')/('.$row['operator'].'*('.$count_hour.'-(1+'.$lunch_break_time2_board2.'))*60))*100'.'system';
			$trend=$row['totalProdQnty']/($count_hour-1-$lunch_break_time2_board2)*15;
			$planned=number_format($row['planned']/$row['countLineNumber'],2);
			$varriance=$current_target-$row['totalProdQnty'];
			$totalAlrSpotQnty=$row['totalAlterQnty']+$row['totalSpotQnty'];
			$dhu=($totalAlrSpotQnty/$row['totalProdQnty'])*100;
			//echo $border.'hsdgfdhfb'.$border_color;

			$border_right=$border_left="";
			if (count($production_data)!=$border) {
				$border_right="border-right:10px solid #fff";
			}
			if ($border!=1) $border_left="border-left:0";
			$border++;
			?>
			<div style="width:33.33%; height: 100%; float:left;">
				<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left; <? echo $border_right; ?>; <? echo $border_left; ?>" rules="all" cellpadding="0" cellspacing="0">
					<tr style="height: 8%; background-color:#FF0000; <? echo $border_left; ?>">
						<td colspan="4" style="width: 100%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center; vertical-align: middle; border-top:1px solid #000;"><strong><? echo $group_name; ?></strong></td>						
					</tr>
					<tr style="height: 5%; border:3px solid #000; <? echo $border_left; ?>">
						<td style="color: white; font-weight:bold; text-align: left; vertical-align: middle; border:3px solid #000; <? echo $border_left; ?>">
							<div style="font-size: 1.5vw;">PRODUCTION</div>
						</td>
						<td rowspan="2" style="color: white; font-size: 1vw; font-weight:bold; text-align: center; vertical-align: middle;border:1px solid #000;<? echo $border_left; ?>">
							<div style="font-size: 1.5vw; height: 35%; text-align: left;">Day Target</div>
							<div style="font-size: 2vw; height: 65%; text-align: center; font-weight:900;"><?= $day_terget; ?>&nbsp;</div>
						</td>
						<td rowspan="2" colspan="2" style="color: white; font-size: 1vw; font-weight:bold; border:3px solid #000;">
							<div style="font-size: 1.5vw; height: 35%; text-align: left;">Efficiency</div>
							<div style="font-size: 2vw; height: 65%; text-align: center; font-weight:900;"><?= round($efficiency); ?>&nbsp;</div>
						</td>
					</tr>

					<tr style="height: 25%; border:3px solid #000;<? echo $border_left; ?>">
						<td style="color: white; font-weight:bold; font-weight:bold; border:3px solid #000;<? echo $border_left; ?>">
							<div style="font-size: 1.5vw; height: 20%; text-align: left;">Target</div>
							<div style="font-size: 2vw; height: 80%; text-align: center; font-weight:900;" title="Hourly Target*Count Hour=<? echo $row['hourly_target'].'*'.$count_hour; ?>"><?= $current_target; ?>&nbsp;</div>
						</td>    			
					</tr>

					<tr style="height: 25%; border:3px solid #000;<? echo $border_left; ?>">
						<td style="color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle; border:3px solid #000;<? echo $border_left; ?>">
							<div style="font-size: 1.5vw; height: 20%; text-align: left; width: 100%">Actual</div>
							<?
							if ($current_target<=$row['totalProdQnty'])
							{
								?>
								<div style="font-size: 2vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: center;"><?= $row['totalProdQnty']; ?></span>&nbsp;<span style="width: 20%; height: 80%; text-align: right; color: green;">&#9650;</span></div>
								<?
							}
							else
							{
								?>
								<div style="font-size: 2vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: center;"><?= $row['totalProdQnty']; ?></span><span style="width: 20%; height: 20%; text-align: right; color: red;">&#9660;</span></div>
								<?
							}
							?>	
						</td>
						<td style="color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;border:3px solid #000;<? echo $border_left; ?>">
							<div style="font-size: 1.5vw; height: 20%; text-align: left;">Trend</div>
							<div style="font-size: 2vw; height: 80%; text-align: center; font-weight:900;"><?= round($trend); ?>&nbsp;</div>
						</td>
						<td colspan="2" style="text-align: center; border:3px solid #000;" align="center" <? echo $border_left; ?>>
							<div style="font-size: 1.5vw; height: 20%; text-align: left; font-weight:900; color: #fff;">Planned</div>
							<div style="font-size: 2vw; height: 80%; text-align: center; font-weight:900; color: #fff;"><?= $planned; ?>%</div>							
					</tr>

					<tr style="height: 25%; border:3px solid #000;<? echo $border_left; ?>">
						<td style="color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;border:3px solid #000;<? echo $border_left; ?>">    				
							<div style="font-size: 1.5vw; height: 20%; text-align: left; font-weight:bold; width:100%">Variance</div>
							<?
							if ($varriance < 0)
							{
								?>
								<div style="font-size: 2vw; height: 80%; text-align: center; font-weight:900;"><span style="width: 80%; text-align: center;"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; color: green; font-weight:bold;">&#9650;</span></div>        			
								<?
							}
							else
							{
								?>
								<div style="font-size: 2vw; height: 80%; text-align: center; font-weight:900;"><span style="width: 80%; text-align: center;"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; color: red; font-weight:bold;">&#9660;</span></div>
								<?
							}
							?>    				
						</td>
						<td style="color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;border:3px solid #000;<? echo $border_left; ?>">
							<div style="font-size: 1.5vw; height: 20%; text-align: left;">Defective%</div>
							<div style="font-size: 2vw; height: 80%; text-align: center; color: red; font-weight:900;"><?= trim(number_format($dhu,2),'-'); ?>%&nbsp;</div>
						</td>
						<td style="color: white; font-size: 1.5vw; font-weight:bold;border:3px solid #000;">
							<div style="font-size: 1.5vw; height: 20%; text-align: center;">Operator</div>
							<div style="font-size: 2vw; height: 80%; text-align: center; font-weight:900;"><?= $row['operator']; ?></div>							
						</td>
						<td style="color: white; font-size: 1.5vw; font-weight:bold;border:3px solid #000;">
							<div style="font-size: 1.5vw; height: 20%; text-align: center;">Helper</div>
							<div style="font-size: 2vw; height: 80%; text-align: center; font-weight:900;"><?= $row['helper']; ?></div>							
						</td>
					</tr>
				
					<tr style="height: 8%; <? echo $border_left; ?>">
						<td style="color: white; font-size: 1.3vw; font-weight:bold; text-align: center; vertical-align: middle;border:3px solid #000; border-bottom:1px solid #000;<? echo $border_left; ?>">
							<div style="font-size: 1.3vw; text-align: center; "><span>Total Line:</span>&nbsp;<span style="font-weight:900;"><?= $row['countLineNumber'].'/'.$actual_resource_line_arr[$group_name]; ?>,</span>&nbsp;<br><span>SMV Avg:</span>&nbsp;<span style="font-weight:900;"><?= number_format($groupWiseTotalSmvAvg,2); ?></span></div>
						</td>
						<td colspan="3" style="color: white; font-size: 1.3vw; font-weight:900; text-align: center; vertical-align: middle; background-color: #FF0000;border:3px solid #000; border-bottom:1px solid #000;">
							<div><span>Alter:&nbsp;</span><span style="font-weight: 900;"><?=$defectDataArray[$group_name]['alter']; ?>;</span><span>&nbsp;&nbsp;Spot:&nbsp;</span><span style="font-weight: 900;"><?=$defectDataArray[$group_name]['spot']; ?>;</span><br>Reject:&nbsp;<span style="font-weight: 900;"><?= $row['totalRejectQnty']; ?>;</span>&nbsp;&nbsp;<span> T.Defect:&nbsp;</span><span style="font-weight: 900;"><?= ($defectDataArray[$group_name]['alter']+$defectDataArray[$group_name]['spot']); ?></span></div>
						</td>
					</tr>			
				</table>
			</div>
			<?

		}
		?>
		</div>
    </div>
    <?
	exit();
}

?>