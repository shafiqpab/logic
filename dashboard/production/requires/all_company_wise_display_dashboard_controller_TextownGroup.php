<? 

header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	//$cbo_company_name=str_replace("'","",trim($cbo_company_name));
	//$txt_date=str_replace("'","",trim($txt_date));
	$max_height=str_replace("'","",trim($max_height));

	if ($db_type==0) $txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    else $txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
	$txt_date_cond="";
	if ($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
	
    $prod_reso_allo=1;
	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	if($prod_reso_allo == 1)
	{
		$dataArray = sql_select("SELECT a.id as ID, a.company_id as COMPANY_ID, a.location_id as LOCATION_ID, a.floor_id as FLOOR_ID, a.line_number as LINE_NUMBER, b.pr_date as PR_DATE, b.target_per_hour as TARGET_PER_HOUR, b.working_hour as WORKING_HOUR, b.man_power as MAN_POWER, b.active_machine as ACTIVE_MACHINE, b.operator as OPERATOR, b.helper as HELPER, c.target_efficiency as TARGET_EFFICIENCY from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and b.pr_date='$txt_date'");
	  
		foreach($dataArray as $row)
		{
			$prod_resource_array[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['ID']]['target_per_hour']=$row['TARGET_PER_HOUR'];
			$prod_resource_array[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['ID']]['working_hour']=$row['WORKING_HOUR'];
			$prod_resource_array[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['ID']]['active_machine']=$row['ACTIVE_MACHINE'];
			$prod_resource_array[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['ID']]['target_efficiency']=$row['TARGET_EFFICIENCY'];
			$prod_resource_array[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['ID']]['operator']=$row['OPERATOR'];
			$prod_resource_array[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['ID']]['helper']=$row['HELPER'];
			$prod_resource_array[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['ID']]['tpd']=$row['TARGET_PER_HOUR']*$row['WORKING_HOUR'];
			$prod_reso_line_arr[$row['ID']]=$row['LINE_NUMBER'];
		}		
	}	
	unset($dataArray);
	//echo "<pre>";print_r($prod_resource_array);die;	

	$start_time_arr=array();
	$group_prod_start_time=sql_select("select min(TO_CHAR(prod_start_time,'HH24:MI')) as PROD_START_TIME  from variable_settings_production where variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");

	$prod_start_hour=$group_prod_start_time[0]['PROD_START_TIME'];
	if($prod_start_hour=='') $prod_start_hour='08:00';	
	$start_time=explode(':',$prod_start_hour);
	$hour=substr($start_time[0],1,1);

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
	
	$sql="SELECT a.serving_company as SERVING_COMPANY, a.location as LOCATION_ID, a.floor_id as FLOOR_ID, a.prod_reso_allo as PROD_RESO_ALLO, a.production_date as PRODUCTION_DATE, a.item_number_id as ITEM_NUMBER_ID, a.po_break_down_id as PO_ID, a.sewing_line as SEWING_LINE, sum(a.production_quantity) as GOOD_QNTY, b.job_no as JOB_NO, b.style_ref_no as STYLE_REF_NO, b.buyer_name as BUYER_NAME, b.set_break_down as SMV_PCS_SET, c.po_number as PO_NUMBER, c.grouping as GROUPING, ";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$bg_hour=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour='PROD_HOUR'.substr($bg_hour,0,2);
		$alter_hour="ALTER_HOUR".substr($bg_hour,0,2);
		$spot_hour="SPOT_HOUR".substr($bg_hour,0,2);
		$reject_hour="REJECT_HOUR".substr($bg_hour,0,2);
		$replace_hour="REPLACE_HOUR".substr($bg_hour,0,2);

		if($first==1)
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN replace_qty else 0 END) AS $replace_hour,";
		}
		else
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN replace_qty else 0 END) AS $replace_hour,";
		}
		$first=$first+1;
	}
	$prod_hour='PROD_HOUR'.$last_hour;
	$alter_hour="ALTER_HOUR".$last_hour;
	$spot_hour="SPOT_HOUR".$last_hour;
	$reject_hour="REJECT_HOUR".$last_hour;
	$replace_hour="REPLACE_HOUR".$last_hour;

	$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN replace_qty else 0 END) AS $replace_hour";
														
	$sql.=" FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
		where a.production_type in (5) and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0  $txt_date_cond 
		group by a.serving_company, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number, c.grouping
		order by a.sewing_line"; 	
	//echo $sql;die;
	
	$result = sql_select($sql);
	$po_break_down_arr=array();
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$poId .= $row["PO_ID"].",";	
	}
	
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));

	$smv_source=1;
	$sql_item="SELECT b.id as COLOR_SIZE_ID, b.po_break_down_id as PO_BREAK_DOWN_ID, b.order_quantity as ORDER_QUANTITY, c.gmts_item_id as GMTS_ITEM_ID, c.smv_pcs as SMV_PCS, c.smv_pcs_precost as SMV_PCS_PRECOST from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and b.is_deleted=0 and b.po_break_down_id in($order_ids)";
	$resultItem=sql_select($sql_item);
	$item_smv_array=array();
	$check_color_size_arr=array();
	foreach($resultItem as $row)
	{
		if ($check_color_size_arr[$row['COLOR_SIZE_ID']]=="")
		{
			if($smv_source==1) $item_smv_array[$row['PO_BREAK_DOWN_ID']][$row['GMTS_ITEM_ID']]['SMV']=$row['SMV_PCS'];
			else if($smv_source==2) $item_smv_array[$row['PO_BREAK_DOWN_ID']][$row['GMTS_ITEM_ID']]['SMV']=$row['SMV_PCS_PRECOST'];
			$check_color_size_arr[$row['COLOR_SIZE_ID']]=$row['COLOR_SIZE_ID'];
		}		
	}
	//echo '<pre>';print_r($item_smv_array);

	$item_smv=$item_count=$total_smv=0;
	$operator=$helper=0;
	$hourly_target=$efficiency=$totalProdQnty = 0;
	$totalAlterQnty=$totalSpotQnty=$totalRejectQnty=$totalReplaceQnty=0;
	$totalGoodAlrSpotRejectQnty=0;
	$day_terget=$current_target=0;
	$varriance=$trend=$count_hour=0;
	$planned=$countLineNumber=0;
	$operators=0;
	$production_data=array();
	foreach($result as $row)
	{
		//$operator=0;
		$count_hour=0;		
		if($row['PROD_RESO_ALLO']==1)
		{

			$operator = $prod_resource_array[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['SEWING_LINE']]['operator'];
			$helper = $prod_resource_array[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['SEWING_LINE']]['helper'];
			$working_hour = $prod_resource_array[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['SEWING_LINE']]['working_hour'];
			$target_efficiency = $prod_resource_array[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['SEWING_LINE']]['target_efficiency'];
			$day_terget = $prod_resource_array[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['SEWING_LINE']]['tpd'];
			$hourly_target=$prod_resource_array[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$row['SEWING_LINE']]['target_per_hour'];

			if ($check_line_number_arr[$row['SEWING_LINE']] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row['SEWING_LINE']]=$row['SEWING_LINE'];
				$planned       += $target_efficiency;
			}
			
			$item_smv=$item_smv_array[$row["PO_ID"]][$row["ITEM_NUMBER_ID"]]['SMV'];

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row['SEWING_LINE']]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");		
			
			
			$production_data[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$line_name]["hourly_target"]=$hourly_target;
			$production_data[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$line_name]["day_terget"]=$day_terget;
			$production_data[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$line_name]["operator"]=$operator;
			$production_data[$row['SERVING_COMPANY']][$row['LOCATION_ID']][$row['FLOOR_ID']][$line_name]["helper"]=$helper;
			
			for($h=$hour; $h<$last_hour; $h++)
			{
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="PROD_HOUR".substr($bg_hour,0,2);
				$alter_hour="ALTER_HOUR".substr($bg_hour,0,2);
				$spot_hour="SPOT_HOUR".substr($bg_hour,0,2);
				$reject_hour="REJECT_HOUR".substr($bg_hour,0,2);
				$replace_hour="REPLACE_HOUR".substr($bg_hour,0,2);					
				
				$totalProdQnty += $row[$prod_hour];
				$totalAlterQnty += $row[$alter_hour];
				$totalSpotQnty += $row[$spot_hour];
				$totalRejectQnty +=$row[$reject_hour];
				$totalReplaceQnty +=$row[$replace_hour];

				$total_smv+=$item_smv*$row[$prod_hour];
			
				if (date('G') == $h) break;  // Up to  Current hour calculation
				$count_hour++;
				
			}
		}
	}
	//echo '<pre>';print_r($production_data);
	//echo $operator;	
	$smv_average=$total_working_hour=0;
	$highest_working_hour=0;
	if ($totalProdQnty != 0) $smv_average=$total_smv/$totalProdQnty;

	$total_working_hour+=$working_hour;
	if ($working_hour>$highest_working_hour) {
		$highest_working_hour=$working_hour;
	}

	$current_minute=date('i', time());
	$current_hour=$current_minute/60;	

	// Hour calculation without lunch our and count hour is not greter than working hour
	if (date('G')>13){
		$count_hour=$count_hour-1;
	}
	$count_hour=$count_hour;
	$avg_count_hour=$count_hour+$current_hour;
	if ($avg_count_hour>$highest_working_hour) {
		$count_hour=$highest_working_hour;
		$avg_count_hour=ceil($total_working_hour/$countLineNumber);
	}

	$day_terget=$hourly_target=$operator=$helper=0;

	foreach($production_data as $company_id => $location_data)
	{
		foreach($location_data as $location_id => $floor_data)
		{
			foreach($floor_data as $floor_id => $line_data)
			{
				foreach($line_data as $row)
				{
					$hourly_target+=$row['hourly_target'];
					$day_terget+=$row['day_terget'];
					$operator+=$row['operator'];
					$helper+=$row['helper'];
				}
			}
		}
	}

	$current_target=$hourly_target*$avg_count_hour;
	$varriance=$current_target-$totalProdQnty;

	$trend=($totalProdQnty/$avg_count_hour)*$highest_working_hour;

	//echo $totalProdQnty.'**'.$total_smv_avg.'**'.$operator.'**'.$helper.'**'.$count_hour;
	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$dhu = ($totalProdQnty/$totalGoodAlrSpotRejectQnty)*100-100; //(qcQty/total_qty)*100-100;
	$efficiency=(($totalProdQnty*$smv_average)/(($operator+$helper)*$avg_count_hour*60))*100;
	?>

	<style type="text/css">
		#secondtime table, th, td {
		    border: 3px solid black;
		    border-collapse: collapse;
		}
		.rpt_table td {
		    border: 3px solid #000;
		    padding-left: 1px;
		}

		body {
			background-color: #262626;
		}
		
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; " id="firsttime">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height: 8%;">
    			<td style="width: 30%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">KPI Dashboard</td>
    			<td style="width: 30%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= 'Textown Group'; ?></td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;">
				<?
				//$convert_time_hour=ltrim(number_format(date('i', time())/60,2),'0.');
				$time_hour=number_format($count_hour+(date('i', time())/60),2);				
				echo $time_hour.' Hr';
				?>
				</td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 5%;">
    			<td style="color: white; font-weight:bold; text-align: left; vertical-align: middle;">
    				<div style="font-size: 2vw;">PRODUCTION</div>
    			</td>
    			<td rowspan="2" style="color: white; font-size: 2vw; font-weight:bold;">
    				<div style="font-size: 2vw; height: 35%; text-align: left;">Day Target</div>    				
    				<div style="font-size: 6vw; height: 65%; text-align: right; font-weight:900;"><?= $day_terget; ?>&nbsp;</div>
    			</td>
    			<td rowspan="3" colspan="2" style="color: white; font-size: 2vw; font-weight:bold; width: 33%; height: 75%;">
    				<table style="width: 100%;height: 100%;">
    					<tr>
    						<td colspan="2" style="border:0; width: 100%; font-size: 2vw;" align="left" >Efficiency</td>
    					</tr>
    					<tr>	
    						<td colspan="2" style="border:0;" align="center" title="((Total Prod*((Total Item SMV*Total Order Qty)/Total Order Qty))/((Operator+Helper)*Avg Count Hour*60))*100">
    							<canvas id="canvas" style="width:100%; max-width:100%"></canvas>
    							<input type="hidden" id="efficiency" value="<? if (is_nan($dhu)) echo '0'; else echo floor($efficiency); ?>">
    						</td>    						
    					</tr>
    					<tr>
    						<td colspan="2" style="border:0; text-align: center; width: 100%" align="center" ><div style="font-size: 2vw; text-align: center; vertical-align: middle;"><span>Planned</span>&nbsp;<span style="font-weight:900;" title="Total Target Efficiency/Total Line Number"><? if ( $countLineNumber!=0) echo number_format($planned/$countLineNumber,2); else echo '0.00'; ?>%</span></div>
    						</td>
    					</tr>
    				</table> 					
    			</td>
			</tr>

			<tr style="height: 25%; width: 100%;">
    			<td style="color: white; font-weight:bold;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;  width: 100%;">Target</div>
    				<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right;" title="Hourly Target*Avg Count Hour=<? echo $hourly_target.'*'.$avg_count_hour; ?>"><span style="width: 80%; text-align: right;"><?=  floor($current_target); ?></span><span style="width: 20%; text-align: right;">&nbsp;&nbsp;</span></div>
    			</td>    			
			</tr>
			<tr style="height: 25%;">
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
					<div style="height: 20%;">
						<div style="font-size: 2vw; text-align: left; float:left;">Actual</div>
    				<?
	        		if ($current_target<=$totalProdQnty)
	        		{
	        			?>
							<div style="font-size: 4vw; text-align: right; float:right; color:green;">&#9650;</div>
						</div>
    					<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  $totalProdQnty; ?></span><span style="width: 20%; text-align: right; font-weight:900;"></span></div>
    					<?
    				}
    				else
    				{
    					?>
							<div style="font-size: 4vw; text-align: right; float:right; color:red;">&#9660;</div>
						</div>
    					<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  $totalProdQnty; ?></span><span style="width: 20%; text-align: right; font-weight:900;"></span></div>
    					<?
    				}
    				?>	
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;">Trend</div>
    				<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;" title="(Total Prod/Avg Count Hour)*Highest Working Hour=<? echo '('.$totalProdQnty.'/'.$avg_count_hour.')*'.$highest_working_hour; ?>"><? if (is_nan($trend)) echo '0'; else echo round($trend); ?>&nbsp;</div>
    			</td>
			</tr>
			<tr style="height: 25%;">
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">    				
					<div style="height: 20%;">	
    					<div style="font-size: 2vw; text-align: left; float:left;">Variance</div>
    				<?
	        		if ($varriance < 0)
	        		{
	        			?>
							<div style="font-size: 4vw; text-align: right; float:right; color:green;">&#9650;</div>
						</div>
	        			<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;" title="Target-Total Prod"><span style="width: 80%; text-align: right;"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; font-weight:bold;"></span></div>        			
	        			<?
	        		}
	        		else
	        		{
	        			?>
							<div style="font-size: 4vw; text-align: right; float:right; color:red;">&#9660;</div>
						</div>
	        			<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;" title="Target-Total Prod"><span style="width: 80%; text-align: right;"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; font-weight:bold;"></span></div>
	        			<?
	        		}
	        		?>    				
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;">DHU%</div>
    				<div style="font-size:6vw; height: 80%; text-align: right; color: red; font-weight:900;" title="(Total Prod/(Total Prod+Alter+Spot+Reject))*100-100"><? if (is_nan($dhu)) echo '0.00'; else echo trim(number_format($dhu,2),'-'); ?>%&nbsp;</div>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; ">
					<div style="font-size: 2vw; height: 20%; text-align: center; ">Operator</div>
    				<div style="font-size:6vw; height: 80%; text-align: center; font-weight:900;"><?= $operator; ?></div>    				
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; ">
					<div style="font-size: 2vw; height: 20%; text-align: center; ">Helper</div>
    				<div style="font-size:6vw; height: 80%; text-align: center; font-weight:900;"><?= $helper; ?></div>    				
    			</td>
			</tr>
			<tr style="height: 8%;">
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<div style="font-size: 1.8vw; text-align: center; "><span>Line Nos:</span>&nbsp;<span style="font-weight:900;"><?= $countLineNumber; ?>,</span>&nbsp;<span>SMV Avg:</span>&nbsp;<span style="font-weight:900;" title="((Total Item SMV*Total Order Qty)/Total Order Qty)"><?= number_format($smv_average,2); ?></span></div>
    			</td>
    			<td colspan="3" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle; background-color: #FF0000;">
    				<div><span>Alter:&nbsp;</span><span style="font-weight: 900;"><?= $totalAlterQnty; ?>,</span><span>&nbsp;&nbsp;Spot:&nbsp;</span><span style="font-weight: 900;"><?= $totalSpotQnty; ?>,</span><span>&nbsp;&nbsp;Reject:&nbsp;</span><span style="font-weight: 900;"><?= $totalRejectQnty; ?>,</span><span>&nbsp;&nbsp;Total:&nbsp;</span><span style="font-weight: 900;"><?= ($totalAlterQnty+$totalSpotQnty+$totalRejectQnty); ?></span></div>
    			</td>
			</tr>
		</table>        
    </div> 
    <?
	exit();
	

}

?>