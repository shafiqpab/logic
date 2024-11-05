<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

    $cbo_company_name=return_field_value("id", "lib_company", "company_name=$cbo_company_name and status_active=1 and is_deleted=0", "id");
    $cbo_location_name=return_field_value("id", "lib_location", "location_name=$cbo_location_name and status_active=1 and is_deleted=0", "id");
	$max_height=str_replace("'","",trim($max_height));

	if ($db_type==0) $txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    else $txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));    

	$txt_date_cond='';
	if ($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";

	$start_time_arr=array();
	$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name in(1) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
    $prod_start_hour=$start_time_arr[1]['pst'];
	//$prod_start_hour='08:00';
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

	$variable_time_hour= $hour;
    $current_time_hour_minute=explode(':',date('H:i', time()));
    //$current_hours=number_format((($current_time_hour_minute[0]-$variable_time_hour)+$current_time_hour_minute[1]/60),2);
    //$count_prod_hours=$current_time_hour_minute[0]-$variable_time_hour;
	//echo $current_hours;
    	
    $buyer_library    = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');
    $floor_library    = return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
    $prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$smv_source=sql_select("select company_name, smv_source from variable_settings_production where variable_list=25 and status_active=1 and is_deleted=0 and company_name=$cbo_company_name");
	foreach($smv_source as $val){
		$smv_source=$val[csf('smv_source')];
	}
	//echo '<pre>';print_r($smv_source_arr);
	$sql_item="SELECT a.company_name, b.id, c.gmts_item_id, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$resultItem=sql_select($sql_item);
	$item_smv_arr=array();
	foreach($resultItem as $row)
	{
		if($smv_source==1)
		{
			$item_smv_array[$row[csf('id')]][$row[csf('gmts_item_id')]]=$row[csf('smv_pcs')];
		}
		else if($smv_source==2)
		{
			$item_smv_array[$row[csf('id')]][$row[csf('gmts_item_id')]]=$row[csf('smv_pcs_precost')];
		}
	}

    // Check Variable Setting Use Prod Resource Allocation
    //$prod_reso_allo=return_field_value('auto_update','variable_settings_production',"variable_list=23 and is_deleted=0 and status_active=1");
    $prod_reso_allo=1;  // default set
	$prod_resource_array=array();
	if($prod_reso_allo == 1)
	{
		$sql_actual_resource="SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency, TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=c.id and a.id=c.mst_id and c.id=d.mast_dtl_id and d.mst_id=a.id and a.company_id=$cbo_company_name and a.location_id=$cbo_location_name and b.pr_date='$txt_date' and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0";
	    $dataArray = sql_select($sql_actual_resource);
	  	$target=$day_terget=0;
	  	$operator=$helper=0;
	  	$count_line_numbers=0;
		$start_time_line_wise_arr=array();
		foreach($dataArray as $row)
		{
			$start_time_line_wise_arr[$row[csf('line_number')]]=$row[csf('prod_start_time')];
			$start_time_line_wise=explode(':',$row[csf('prod_start_time')]);
			$hour_line_wise=substr($start_time_line_wise[0],1,1);
			//$totatl_hour_line_wise+=substr($start_time_line_wise[0],1,1);
			//$total_minute_line_wise+=substr($start_time_line_wise[1],1,1);		
    		$current_hours=number_format((($current_time_hour_minute[0]-$hour_line_wise)+$current_time_hour_minute[1]/60),2);
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['working_hour']=$row[csf('working_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_efficiency']=$row[csf('target_efficiency')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['man_power']=$row[csf('man_power')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$line_ids = $row[csf('id')];
			$operator+=$row[csf('operator')];
			$helper+=$row[csf('helper')];
			$day_terget+=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$target+=$row[csf('target_per_hour')]*$current_hours;
			$count_line_numbers++;
		}
	}	
	unset($dataArray);

	//echo $count_line_numbers; 
	//echo "<pre>";print_r($start_time_line_wise_arr);die;

	foreach($start_time_line_wise_arr as $time_val)
	{
		$line_wise_start_time=explode(':',$time_val);
		$line_wise_start_hour=substr($line_wise_start_time[0],1,1)+substr($line_wise_start_time[1],1,1)/60;
		$current_time_hour=$current_time_hour_minute[0]+$current_time_hour_minute[1]/60;
		//echo $current_time_hour.'system';
		$line_wise_hour=$current_time_hour-$line_wise_start_hour;
		$line_wise_total_hour+=$line_wise_hour;				
	}
	//echo $line_wise_total_hour;

	$count_prod_hours=$line_wise_total_hour/$count_line_numbers;
	$exp_prod_hours=explode(".",$count_prod_hours);
	$final_prod_hour=$exp_prod_hours[0];
	$final_prod_minute=round(($count_prod_hours-$exp_prod_hours[0])*60);
	
	$sql="SELECT a.prod_reso_allo, a.production_date, a.location, a.floor_id, a.item_number_id, a.po_break_down_id as po_id, a.sewing_line, sum(a.production_quantity) as good_qnty, b.company_name, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down as smv_pcs_set, c.po_number,";
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
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
		}
		else
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
		}
		$first=$first+1;
	}
	$prod_hour='prod_hour'.$last_hour;
	$alter_hour="alter_hour".$last_hour;
	$spot_hour="spot_hour".$last_hour;
	$reject_hour="reject_hour".$last_hour;

	$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
														
	$sql.=" FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
		where a.production_type in (5) and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.location=$cbo_location_name $txt_date_cond 
		group by a.prod_reso_allo, a.production_date, a.location, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.company_name, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number
		order by a.sewing_line";
	//echo $sql;die;
	
	$result = sql_select($sql);
	$item_count=0;
	$total_smv=0;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$totalAlterQnt = $totalSpotQnt = $totalRejectQnt = 0;
	$totalGoodAlrSpotRejectQnty=0;
	$current_target=0;
	$varriance=$trend=0;
	$planned=$countLineNumber=0;
	$production_data=array();
	$po_wise_qty_arr=array();
	$item_smv_total=0;
	foreach($result as $row)
	{

		$poId .= $row[csf("po_id")].",";
		if($row[csf('prod_reso_allo')]==1)
		{

			$countLineNumber++;
			$planned += $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			$line_name = $prod_reso_line_arr[$row[csf('sewing_line')]];

			$po_wise_qty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]+=$row[csf("good_qnty")]*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]];

			$smv_pcs_string=$row[csf("smv_pcs_set")];
			$smv_string_arr=explode("__",$smv_pcs_string);
		    foreach($smv_string_arr as $itemId)
		    {					
			    $smv_arr=explode("_",$itemId);
			    //echo '<pre>';print_r($smv_arr);die;
			    if($smv_arr[0] == $row[csf("item_number_id")]){
			    	$item_count++;
			    	//echo $line_item_count.'system';
				    $total_smv += number_format($smv_arr[2],2);
				    $production_data[$line_name]['smv_sum']+=number_format($smv_arr[2],2);
				    $production_data[$line_name]['smv_avg']++;
			    }
		    }

		    $production_data[$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';

		    for($h=$hour; $h<$last_hour; $h++)
			{						
				/*if ($h == 14) continue;
				$count_hour++;*/

				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);				
				
				$production_data[$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];

				$production_data[$line_name]["totalProdQnty"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["totalAlterQnty"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["totalSpotQnty"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["totalRejectQnty"]+=$row[csf("$reject_hour")];

				/*if ($h == 15 && date('G') == 14) break;
				if (date('G') == $h) break;*/
        		
			}
		}
		else
		{
			$countLineNumber++;
			$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
			$planned += $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			$po_wise_qty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("good_qnty")]*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]];
	
			$line_name = $val[csf('sewing_line')];
			$smv_pcs_string=$row[csf("smv_pcs_set")];
			$smv_string_arr=explode("__",$smv_pcs_string);
		    foreach($smv_string_arr as $itemId)
		    {					
			    $smv_arr=explode("_",$itemId);
			    //echo '<pre>';print_r($smv_arr);die;
			    if($smv_arr[0] == $row[csf("item_number_id")]){
			    	$item_count++;
			    	//echo $line_item_count.'system';
				    $total_smv += number_format($smv_arr[2],2);
				    $production_data[$line_name]['smv_sum']+=number_format($smv_arr[2],2);
				    $production_data[$line_name]['smv_avg']++;
			    }
		    }	

		    $production_data[$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';
		

		    for($h=$hour; $h<$last_hour; $h++)
			{						
				/*if ($h == 14) continue;
				$count_hour++;*/
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);

				$production_data[$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];

				$production_data[$line_name]["totalProdQnty"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["totalAlterQnty"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["totalSpotQnty"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["totalRejectQnty"]+=$row[csf("$reject_hour")];
				/*if ($h == 15 && date('G') == 14) break;
				if (date('G') == $h) break;*/        		
			}
		}	
	}
	//echo '<pre>';print_r($po_wise_qty_arr);
	$poIds=rtrim($poId,",");
	$total_smv_avg=$lineWiseTotalSmv=0;
	$total_smv_avg=$total_smv/$item_count;
	foreach ($production_data as $key => $val) 
	{
		$lineWiseTotalSmv+=$val['smv_sum']/$val['smv_avg'];
	}

	$varriance=$target-$totalProdQnty;
	$trend=$totalProdQnty/$current_hours*11;


	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$dhu = ($totalProdQnty/$totalGoodAlrSpotRejectQnty)*100-100; //(qcQty/total_qty)*100-100;
	$efficiency=(($totalProdQnty*$total_smv_avg)/(($operator+$helper)*$current_hours*60))*100;
	//efficiency=(prod_qty*smv)/((operators+helpers)*Number of hours*Minutes)
    $group_name=return_field_value("group_name", "lib_group", "status_active=1 and is_deleted=0");	

	foreach($po_wise_qty_arr as $po_id=>$item_value)
	{
		foreach($item_value as $item_id=>$val)
		{
			$total_poduce_min+=$val;
		}
	}
   //echo $total_poduce_min;
    $smv_average=$total_poduce_min/$totalProdQnty;
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
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif;" id="firsttime">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height: 8%;">
    			<td style="width: 5%; text-align: center; vertical-align: middle; background-color:#fff;">
		        	<p><img src='../../images/youthGroupLogo.png' align="middle"/></p>
    			</td>
    			<td style="width: 24%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">KPI Dashboard</td>
    			<td style="width: 31%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">Comfit Composite Knit Ltd.</td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= $final_prod_hour.'Hr'.'&nbsp;'.$final_prod_minute.'M'; ?></td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 5%;">
    			<td colspan="2" style="color: white; font-weight:bold; text-align: left; vertical-align: middle;">
    				<p style="font-size: 2vw;">Today Production</p>
    			</td>
    			<td rowspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 35%; text-align: left;">Day Target</p>
    				<p style="font-size: 5vw; height: 65%; text-align: right; font-weight:900;"><?= $day_terget; ?>&nbsp;</p>
    			</td>
    			<td rowspan="3" colspan="2" style="color: white; font-size: 2vw; font-weight:bold;">
    				<table style="width: 100%;height: 100%;">
    					<tr>
    						<td colspan="2" style="border:0; width: 100%; font-size: 2vw;" align="left">Efficiency</td>
    					</tr>
    					<tr>	
    						<td colspan="2" style="border:0;" align="center">
    							<canvas id="canvas" style="width:100%; max-width:100%"></canvas>
    							<input type="hidden" id="efficiency" value="<?= round($efficiency); ?>">
    						</td>    						
    					</tr>
    					<tr>
    						<td colspan="2" style="border:0; text-align: center; width: 100%" align="center" ><p style="font-size: 2vw; text-align: center; vertical-align: middle;"><span>Planned</span>&nbsp;<span style="font-weight:900;"><?= number_format($planned/$countLineNumber,2); ?>%</span></p>
    						</td>
    					</tr>
    				</table>     					
    			</td>
			</tr>

			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-weight:bold; font-weight:bold;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Current Hour Target</p>
    				<p style="font-size: 5vw; height: 80%; text-align: right; font-weight:900;"><?= round($target); ?>&nbsp;</p>
    			</td>    			
			</tr>
			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Actual Production</p>
    				<?
	        		if ($current_target<=$totalProdQnty)
	        		{
	        			?>
    					<p style="font-size: 5vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: right;"><?= $totalProdQnty; ?></span>&nbsp;<span style="width: 20%; height: 80%; text-align: right; color: green;">&#8679;</span></p>
    					<?
    				}
    				else
    				{
    					?>
    					<p style="font-size: 5vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: right;"><?= $totalProdQnty; ?></span><span style="width: 20%; height: 80%; text-align: right; color: red;">&#8681;</span></p>
    					<?
    				}
    				?>	
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Trend</p>
    				<p style="font-size: 5vw; height: 80%; text-align: right; font-weight:900;"><?= round($trend); ?>&nbsp;</p>
    			</td>
			</tr>
			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: right; vertical-align: middle;">    				
    				<p style="font-size: 2vw; height: 20%; text-align: left; font-weight:bold;">Variance</p>
    				<?
	        		if ($varriance < 0)
	        		{
	        			?>
	        			<p style="font-size: 5vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; color: green; font-weight:bold;">&#8679;</span></p>        			
	        			<?
	        		}
	        		else
	        		{
	        			?>
	        			<p style="font-size: 5vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; color: red; font-weight:bold;">&#8681;</span></p>
	        			<?
	        		}
	        		?>    				
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: right; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">DHU%</p>
    				<p style="font-size: 5vw; height: 80%; text-align: right; color: red; font-weight:900;"><?= trim(number_format($dhu,2),'-'); ?>%&nbsp;</p>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold;">
    				<p style="height: 20%;">&nbsp;</p>
    				<p style="font-size: 5vw; height: 60%; text-align: center; font-weight:900;"><?= $operator; ?></p>
    				<p style="font-size: 2vw; height: 40%; text-align: center;">Operator</p>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold;">
    				<p style="height: 20%;">&nbsp;</p>
    				<p style="font-size: 5vw; height: 60%; text-align: center; font-weight:900;"><?= $helper; ?></p>
    				<p style="font-size: 2vw; height: 40%; text-align: center;">Helper</p>
    			</td>
			</tr>
			<tr style="height: 8%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; text-align: center; "><span>Total Line:</span>&nbsp;<span style="font-weight:900;"><?= $count_line_numbers; ?>,</span>&nbsp;<span>SMV Avg:</span>&nbsp;<span style="font-weight:900;"><?= number_format($smv_average,2);//number_format($lineWiseTotalSmv/$countLineNumber,2); ?></span></p>
    			</td>
    			<td colspan="3" style="color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle; background-color: #FF0000;">
    				<p><span>Alter&nbsp;-&nbsp;</span><span style="font-weight: 900;"><?= $totalAlterQnty; ?>;</span><span>&nbsp;&nbsp;Spot&nbsp;-&nbsp;</span><span style="font-weight: 900;"><?= $totalSpotQnty; ?>;</span>&nbsp;&nbsp;Reject&nbsp;-&nbsp;<span style="font-weight: 900;"><?= $totalRejectQnty; ?>;</span><span>&nbsp;&nbsp;Total&nbsp;-&nbsp;</span><span style="font-weight: 900;"><?= ($totalAlterQnty+$totalSpotQnty+$totalRejectQnty); ?>;</span></p>
    			</td>
			</tr>
		</table>        
    </div>
    <?
	exit();
}

?>