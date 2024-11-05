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
	$cbo_location_name=str_replace("'","",trim($cbo_location_name));
	$cbo_floor_name=str_replace("'","",trim($cbo_floor));
	$max_height=str_replace("'","",trim($max_height));

	$txt_date_cond="";
	$txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
	if ($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";
    	
	// convert company name to id
	$company_cond="";
    $cbo_company_id = return_field_value('id', 'lib_company',"company_name='$cbo_company_name'");	
	if ($cbo_company_id !='') $company_cond=" and a.serving_company=$cbo_company_id";
	//echo $company_cond.'rakib';die;

	$cbo_location_id=return_field_value("id", "lib_location", "location_name='$cbo_location_name' and company_id=$cbo_company_id and status_active=1 and is_deleted=0", "id");
	$location_cond=$location_cond3="";
	if ($cbo_location_id != "") {
		$location_cond=" and a.location=$cbo_location_id";
		$location_cond3=" and a.location_id=$cbo_location_id";
	}

	//echo "select a.id from lib_prod_floor a where a.floor_name='$cbo_floor_name' and a.company_id=$cbo_company_id $location_cond3 and a.status_active=1 and a.is_deleted=0";
	$cbo_floor_id=return_field_value("a.id as id", "lib_prod_floor a", "a.floor_name='$cbo_floor_name' and a.company_id=$cbo_company_id $location_cond3 and a.status_active=1 and a.is_deleted=0", "id");
	$floor_cond=$floor_cond3="";
	if ($cbo_floor_id != "")
	{
		$floor_cond=" and a.floor_id=$cbo_floor_id";
		$floor_cond3=" and a.floor_id=$cbo_floor_id";
	}
    
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");

    // Check Variable Setting Use Prod Resource Allocation
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");

	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	$lineIds="";
	if($prod_reso_allo == 1)
	{
	    $dataArray = sql_select("SELECT a.id as ID, a.line_number as LINE_NUMBER, a.floor_id as FLOOR_ID, b.pr_date as PR_DATE, b.target_per_hour as TARGET_PER_HOUR, b.working_hour as WORKING_HOUR, b.man_power as MAN_POWER, b.active_machine as ACTIVE_MACHINE, b.operator as OPERATOR, b.helper as HELPER, c.target_efficiency as TARGET_EFFICIENCY from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_id and b.pr_date='$txt_date' $location_cond3 $floor_cond3");
	  
		foreach($dataArray as $row)
		{
			$prod_resource_array[$row['ID']]['target_per_hour']=$row['TARGET_PER_HOUR'];
			$prod_resource_array[$row['ID']]['working_hour']=$row['WORKING_HOUR'];
			$prod_resource_array[$row['ID']]['active_machine']=$row['ACTIVE_MACHINE'];
			$prod_resource_array[$row['ID']]['target_efficiency']=$row['TARGET_EFFICIENCY'];
			$prod_resource_array[$row['ID']]['operator']=$row['OPERATOR'];
			$prod_resource_array[$row['ID']]['helper']=$row['HELPER'];
			$prod_resource_array[$row['ID']]['man_power']=$row['MAN_POWER'];
			$prod_resource_array[$row['ID']]['tpd']=$row['TARGET_PER_HOUR']*$row['WORKING_HOUR'];
			$line_ids = $row['ID'];
			$prod_reso_line_arr[$row['ID']]=$row['LINE_NUMBER'];
		}
	}	
	unset($dataArray);
	//echo "<pre>";print_r($prod_resource_array);die;	

	$start_time_arr=array();
	$start_time_data_arr=sql_select("select company_name as COMPANY_NAME, shift_id as SHIFT_ID, TO_CHAR(prod_start_time,'HH24:MI') as PROD_START_TIME, TO_CHAR(lunch_start_time,'HH24:MI') as LUNCH_START_TIME from variable_settings_production where company_name=$cbo_company_id and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row['SHIFT_ID']]['pst']=$row['PROD_START_TIME'];
		$start_time_arr[$row['SHIFT_ID']]['lst']=$row['LUNCH_START_TIME'];
	}
	unset($start_time_data_arr);
	
    $prod_start_hour=$start_time_arr[1]['pst'];
	//if($prod_start_hour=='') 
	//$prod_start_hour='08:00';
	$start_time=explode(':',$prod_start_hour);
	$hour=substr($start_time[0],1,1);

	$minutes=$start_time[1]; $last_hour=19;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;

	for($j=$hour;$j<$last_hour;$j++)
	{		
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
    $start_hour_arr[$j+1]='23:59';	
	
	$sql="SELECT a.prod_reso_allo as PROD_RESO_ALLO, a.production_date as PRODUCTION_DATE, a.floor_id as FLOOR_ID, a.item_number_id as ITEM_NUMBER_ID, a.po_break_down_id as PO_ID, a.sewing_line as SEWING_LINE, sum(a.production_quantity) as GOOD_QNTY, b.job_no as JOB_NO, b.style_ref_no as STYLE_REF_NO, b.buyer_name as BUYER_NAME, b.set_break_down as SMV_PCS_SET, c.po_number as PO_NUMBER, c.grouping as GROUPING, ";
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

	$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN replace_qty else 0 END) AS $replace_hour";
														
	$sql.=" FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
		where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_cond $location_cond $floor_cond $txt_date_cond 
		group by a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number,c.grouping
		order by a.sewing_line"; 	
	//echo $sql;die;
	
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$poId .= $row["PO_ID"].",";
	}
	
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));	

	$smv_source=sql_select("select company_name as COMPANY_NAME, smv_source as SMV_SOURCE from variable_settings_production where variable_list=25 and status_active=1 and is_deleted=0 and company_name=$cbo_company_id");
	foreach($smv_source as $val){
		$smv_source=$val['SMV_SOURCE'];
	}
	//echo $smv_source;
	$sql_item="SELECT b.id as COLOR_SIZE_ID, b.po_break_down_id as PO_BREAK_DOWN_ID, b.order_quantity as ORDER_QUANTITY, c.gmts_item_id as GMTS_ITEM_ID, c.smv_pcs as SMV_PCS, c.smv_pcs_precost as SMV_PCS_PRECOST from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and b.is_deleted=0 and a.company_name=$cbo_company_id and b.po_break_down_id in($order_ids)";
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
	//echo '<pre>';print_r($item_smv_array);

	$item_count=0;
	$total_smv=0;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$totalAlterQnt = $totalSpotQnt = $totalRejectQnt = $totalReplaceQnty = 0;
	$totalGoodAlrSpotRejectQnty=0;
	$day_terget=$current_target=0;
	$varriance=$trend=$count_hour=0;
	$planned=$countLineNumber=0;
	$grouping="";	

	$production_data=array();
	foreach($result as $row)
	{
		$count_hour=0;	
		
		if($row['PROD_RESO_ALLO']==1)
		{
			$operator          = $prod_resource_array[$row['SEWING_LINE']]['operator'];
			$helper            = $prod_resource_array[$row['SEWING_LINE']]['helper'];
			$hourly_target     = $prod_resource_array[$row['SEWING_LINE']]['target_per_hour'];
			$working_hour      = $prod_resource_array[$row['SEWING_LINE']]['working_hour'];
			$target_efficiency = $prod_resource_array[$row['SEWING_LINE']]['target_efficiency'];

			if ($check_line_number_arr[$row['SEWING_LINE']] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row['SEWING_LINE']]=$row['SEWING_LINE'];
				$planned += $target_efficiency;
			}
			
			$total_smv += $item_smv_array[$row["PO_ID"]][$row["ITEM_NUMBER_ID"]]['smv']*$item_smv_array[$row["PO_ID"]][$row["ITEM_NUMBER_ID"]]['order_qty'];
			$total_order_qty += $item_smv_array[$row["PO_ID"]][$row["ITEM_NUMBER_ID"]]['order_qty'];

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row['SEWING_LINE']]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");			
			
		    $production_data[$line_name]["operator"]=$operator;
			$production_data[$line_name]["helper"]=$helper;
			$production_data[$line_name]["hourly_target"]=$hourly_target;
			$production_data[$line_name]["working_hour"]=$working_hour;
			$production_data[$line_name]["day_terget"]=$hourly_target*$working_hour;
			$production_data[$line_name]["hourly_target"]=$hourly_target;
			$production_data[$line_name]["target_efficiency"]=$target_efficiency;			

		    for($h=$hour; $h<$last_hour; $h++)
			{
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="PROD_HOUR".substr($bg_hour,0,2);
				$alter_hour="ALTER_HOUR".substr($bg_hour,0,2);
				$spot_hour="SPOT_HOUR".substr($bg_hour,0,2);
				$reject_hour="REJECT_HOUR".substr($bg_hour,0,2);
				$replace_hour="REPLACE_HOUR".substr($bg_hour,0,2);				
				
				$totalProdQnty   += $row[$prod_hour];
				$totalAlterQnty  += $row[$alter_hour];
				$totalSpotQnty   += $row[$spot_hour];
				$totalRejectQnty += $row[$reject_hour];
				$totalReplaceQnty+= $row[$replace_hour];

				if (date('G') == $h) break;  // Up to  Current hour calculation
				$count_hour++;

			}
		}
	}

	//echo '<pre>';print_r($production_data);
	//echo $total_smv;

	$hourly_target=$operator=$helper=0;
	$smv_average=$total_working_hour=0;
	$highest_working_hour=0;
	$smv_average=$total_smv/$total_order_qty;
	foreach ($production_data as $key => $val) 
	{
		$day_terget+=$val['day_terget'];	
		$hourly_target+=$val['hourly_target'];
		$operator+=$val['operator'];	
		$helper+=$val['helper'];
		$total_working_hour+=$val['working_hour'];
		if ($val['working_hour']>$highest_working_hour) {
			$highest_working_hour=$val['working_hour'];
		}
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
	$current_target=$hourly_target*$avg_count_hour;

	$varriance=$current_target-$totalProdQnty;
	$trend=($totalProdQnty/$avg_count_hour)*$highest_working_hour;
	$totalGoodAlrSpotRejectQnty=($totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty);

	$dhu = ($totalProdQnty/$totalGoodAlrSpotRejectQnty)*100-100;
	$efficiency=(($totalProdQnty*$smv_average)/(($operator+$helper)*$avg_count_hour*60))*100;	
	//echo $avg_count_hour;
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
    			<td style="width: 31%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= $cbo_company_name; ?></td>
    			<td style="width: 19%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
				<?
				if ($cbo_floor_name != "") echo $cbo_location_name.'/'.$cbo_floor_name;
				else echo $cbo_location_name; 
				?>
				</td>
    			<td style="width: 8%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;">
				<?				
				$time_hour=number_format($count_hour+(date('i', time())/60),2);				
				echo $time_hour.' Hr';
				?>
				</td>
    			<td style="width: 12%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 5%;">
    			<td style="color: white; font-weight:bold; text-align: left; vertical-align: middle;">
    				<div style="font-size: 2vw;">PRODUCTION</div>
    			</td>
    			<td rowspan="2" style="color: white; font-size: 2vw; font-weight:bold;">
    				<div style="font-size: 2vw; height: 35%; text-align: left;">Day Target</div>    				
    				<div style="font-size: 6vw; height: 65%; text-align: right; font-weight:900;"><?= $day_terget; ?>&nbsp;</div>
    			</td>
    			<td rowspan="3" colspan="3" style="color: white; font-size: 2vw; font-weight:bold; width: 33%; height: 75%;">
    				<table style="width: 100%;height: 100%;">
    					<tr>
    						<td colspan="3" style="border:0; width: 100%; font-size: 2vw;" align="left" >Efficiency</td>
    					</tr>
    					<tr>	
    						<td colspan="3" style="border:0;" align="center" title="((Total Prod*((Total Item SMV*Total Order Qty)/Total Order Qty))/((Operator+Helper)*Avg Count Hour*60))*100">
    							<canvas id="canvas" style="width:100%; max-width:100%"></canvas>
    							<input type="hidden" id="efficiency" value="<?= floor($efficiency); ?>">
    						</td>    						
    					</tr>
    					<tr>
    						<td colspan="3" style="border:0; text-align: center; width: 100%" align="center" ><div style="font-size: 2vw; text-align: center; vertical-align: middle;"><span>Planned</span>&nbsp;<span style="font-weight:900;" title="Total Target Efficiency/Total Line Number"><?= number_format($planned/$countLineNumber,2); ?>%</span></div>
    						</td>
    					</tr>
    				</table> 					
    			</td>
			</tr>

			<tr style="height: 25%; width: 100%;">
    			<td style="color: white; font-weight:bold;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;  width: 100%;">Target</div>
    				<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right;"><span style="width: 80%; text-align: right;" title="Hourly Target*Avg Count Hour=<? echo $hourly_target.'*'.$avg_count_hour; ?>"><?=  floor($current_target); ?></span><span style="width: 20%; text-align: right;">&nbsp;&nbsp;</span></div>
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
    				<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;" title="(Total Prod/Avg Count Hour)*Highest Working Hour=<? echo '('.$totalProdQnty.'/'.$avg_count_hour.')*'.$highest_working_hour; ?>"><?= round($trend); ?>&nbsp;</div>
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
	        			<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;" title="Target-Total Prod"><span style="width: 80%; text-align: right;"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; font-weight:bold;"></span></div>        			
	        			<?
	        		}
	        		else
	        		{
	        			?>
							<div style="font-size: 4vw; text-align: right; float:right; color:red;">&#9660;</div>
						</div>
	        			<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;" title="Target-Total Prod"><span style="width: 80%; text-align: right;"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; font-weight:bold;"></span></div>
	        			<?
	        		}
	        		?>    				
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;">DHU%</div>
    				<div style="font-size:6vw; height: 80%; text-align: right; color: red; font-weight:900;" title="(Total Prod/(Total Prod+Alter+Spot+Reject))*100-100"><?= trim(number_format($dhu,2),'-'); ?>%&nbsp;</div>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; ">
					<div style="font-size: 2vw; height: 20%; text-align: center; ">Operator</div>
    				<div style="font-size:6vw; height: 80%; text-align: center; font-weight:900;"><?= $operator; ?></div>    				
    			</td>
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; ">
					<div style="font-size: 2vw; height: 20%; text-align: center; ">Helper</div>
    				<div style="font-size:6vw; height: 80%; text-align: center; font-weight:900;"><?= $helper; ?></div>    				
    			</td>
			</tr>
			<tr style="height: 8%;">
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<div style="font-size: 1.8vw; text-align: center; "><span>Line Nos:</span>&nbsp;<span style="font-weight:900;"><?= $countLineNumber; ?>,</span>&nbsp;<span>SMV Avg:</span>&nbsp;<span style="font-weight:900;" title="((Total Item SMV*Total Order Qty)/Total Order Qty)"><?= number_format($smv_average,2); ?></span></div>
    			</td>
    			<td colspan="4" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle; background-color: #FF0000;">
    				<div><span>Defectives:&nbsp;</span><span style="font-weight: 900;"><?=($totalAlterQnty+$totalSpotQnty+$totalRejectQnty); ?>,</span><span>&nbsp;&nbsp;Defects:&nbsp;</span><span style="font-weight: 900;"><?= $totDefectQnty; ?>,</span>&nbsp;&nbsp;Rectified:&nbsp;<span style="font-weight: 900;"><?= $totalReplaceQnty; ?>,</span><span>&nbsp;&nbsp;Reject:&nbsp;</span><span style="font-weight: 900;"><?= $totalRejectQnty; ?></span></div>
    			</td>
			</tr>
		</table>        
    </div>
    <?
	exit();	

}

?>