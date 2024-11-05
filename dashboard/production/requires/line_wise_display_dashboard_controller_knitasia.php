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
	$cbo_line_name=str_replace("'","",trim($cbo_line));
	//$txt_date=str_replace("'","",trim($txt_date));
	$max_height=str_replace("'","",trim($max_height));

	if($db_type==0) $txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    else $txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
	
	// convert company name to id
    $cbo_company_id = return_field_value('id', 'lib_company',"company_name like'%$cbo_company_name%'");
    $buyer_library    = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');
    $floor_library    = return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
	//echo "select id from lib_prod_floor where floor_name=$cbo_floor and status_active=1 and is_deleted=0";

	$cbo_location_id=return_field_value("id", "lib_location", "location_name='$cbo_location_name' and company_id=$cbo_company_id and status_active=1 and is_deleted=0", "id");
	$location_cond=$location_cond2=$location_cond2="";
	if ($cbo_location_id != "") {
		$location_cond=" and a.location=$cbo_location_id";
		$location_cond2=" and location_name=$cbo_location_id";
		$location_cond3=" and a.location_id=$cbo_location_id";
	}
	//echo $location_cond;
	
	$cbo_floor_id=return_field_value("a.id as id", "lib_prod_floor a", "a.floor_name='$cbo_floor_name' and a.company_id=$cbo_company_id $location_cond3 and a.status_active=1 and a.is_deleted=0", "id");
	$floor_cond=$floor_cond2=$floor_cond3="";
	if ($cbo_floor_id != "")
	{
		$floor_cond=" and a.floor_id=$cbo_floor_id";
		$floor_cond2=" and floor_name=$cbo_floor_id";
		$floor_cond3=" and a.floor_id=$cbo_floor_id";
	} 
	//echo $floor_cond;

	if ($cbo_line_name != "")
	{
		$ex_line = explode(',', $cbo_line_name);
		foreach ($ex_line as $value)
		{
			$line_id = return_field_value('id','lib_sewing_line',"company_name=$cbo_company_id and line_name='$value' and status_active=1 and is_deleted=0 $location_cond2 $floor_cond2");
			$line_ids .= $line_id.',';
		}

		$line_ids = chop($line_ids,',');
	}

    $lines_cond="";
	if ($line_ids != "") $lines_cond=" and a.line_number in('$line_ids')";  
	//echo $lines_cond;

    // Check Variable Setting Use Prod Resource Allocation
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");    

	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	$lineIds="";
	if($prod_reso_allo == 1)
	{
		$sql_acresource="SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_id $location_cond3 $floor_cond3 $lines_cond and b.pr_date='$txt_date'";
	    $dataArray = sql_select($sql_acresource);
	  
		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['working_hour']=$row[csf('working_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_efficiency']=$row[csf('target_efficiency')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['operator']=$row[csf('operator')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['helper']=$row[csf('helper')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['man_power']=$row[csf('man_power')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$lineIds .= $row[csf('id')].',';
			$prod_reso_line_arr[$row[csf('id')]]=$row[csf('line_number')];
		}
	}	
	unset($dataArray);
	//echo "<pre>";print_r($prod_resource_array);die;

	$company_cond=$line=$txt_date_cond='';
	$lineIds = implode(',', array_flip(array_flip(explode(',', rtrim($lineIds,',')))));
	if($cbo_company_name !='') $company_cond=" and a.serving_company=$cbo_company_id";
	if($cbo_line_name !='') $line=" and a.sewing_line in($lineIds)";
	if($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";

	$start_time_arr=array();
	$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name=$cbo_company_id and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
    //$prod_start_hour=$start_time_arr[1]['pst'];
	//if($prod_start_hour=='') 
	$prod_start_hour='08:00';
	$start_time=explode(':',$prod_start_hour);
	$hour=substr($start_time[0],1,1);

	$last_hour=19;
	$minutes=$start_time[1]; 
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{		
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	//echo "<pre>";print_r($start_hour_arr);die;
    $start_hour_arr[$j+1]='23:59';

	$sql="SELECT a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id as po_id, a.sewing_line, sum(a.production_quantity) as good_qnty, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down as smv_pcs_set, c.po_number, c.grouping, ";
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
		$replace_hour="replace_hour".substr($bg_hour,0,2);
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
	$prod_hour='prod_hour'.$last_hour;
	$alter_hour="alter_hour".$last_hour;
	$spot_hour="spot_hour".$last_hour;
	$reject_hour="reject_hour".$last_hour;
	$replace_hour="replace_hour".$last_hour;

	$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[20]' and a.production_type=5 THEN replace_qty else 0 END) AS $replace_hour";
														
	$sql.=" FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
		where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_cond $floor_cond $location_cond $line $txt_date_cond
		group by a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number, c.grouping"; 	
	//die();
	//echo $sql;//die;
	
	$result = sql_select($sql);

	foreach($result as $row)
	{
		$poId .= $row[csf("po_id")].",";
		$floor_name .= $floor_library[$row[csf('floor_id')]].',';
		$buyer_name .= $buyer_library[$row[csf('buyer_name')]].',';
		$style_ref_no .= $row[csf('style_ref_no')].',';
		$job_no .= $row[csf('job_no')].',';
		$po_number .= $row[csf('po_number')].',';
		if ($row[csf('grouping')] != "") $grouping .= $row[csf('grouping')].',';
	}
	
	$order_no = implode(',', array_flip(array_flip(explode(',', rtrim($po_number,',')))));
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));
	$floor_name = implode(',', array_flip(array_flip(explode(',', rtrim($floor_name,',')))));
	$buyer_name = implode(',', array_flip(array_flip(explode(',', rtrim($buyer_name,',')))));
	$style_ref_no = implode(',', array_flip(array_flip(explode(',', rtrim($style_ref_no,',')))));
	$job_no = implode(',', array_flip(array_flip(explode(',', rtrim($job_no,',')))));
	$grouping = implode(',', array_flip(array_flip(explode(',', rtrim($grouping,',')))));

	$smv_source=sql_select("select company_name, smv_source from variable_settings_production where variable_list=25 and status_active=1 and is_deleted=0 and company_name=$cbo_company_id");
	foreach($smv_source as $val){
		$smv_source=$val[csf('smv_source')];
	}
	//echo $smv_source;
	$sql_item="SELECT b.id as color_size_id, b.po_break_down_id, b.order_quantity, c.gmts_item_id, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and b.is_deleted=0 and b.po_break_down_id in($order_ids)";
	$resultItem=sql_select($sql_item);
	$item_smv_array=array();
	$check_color_size_arr=array();
	foreach($resultItem as $row)
	{
		if ($check_color_size_arr[$row[csf('color_size_id')]]=="")
		{
			if($smv_source==1)
			{
				$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('gmts_item_id')]]['smv']=$row[csf('smv_pcs')];
				$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('gmts_item_id')]]['order_qty']+=$row[csf('order_quantity')];
			}
			else if($smv_source==2)
			{
				$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('gmts_item_id')]]['smv']=$row[csf('smv_pcs_precost')];
				$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('gmts_item_id')]]['order_qty']+=$row[csf('order_quantity')];
			}
			$check_color_size_arr[$row[csf('color_size_id')]]=$row[csf('color_size_id')];
		}		
	}
	//echo '<pre>';print_r($item_smv_array);

	$item_smv=$total_smv=$total_order_qty=0;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$totalAlterQnty = $totalSpotQnty = $totalRejectQnty = $totalReplaceQnty = 0;
	$totalGoodAlrSpotRejectQnty=0;
	$day_terget=$current_target=0;
	$varriance=$trend=$count_hour=0;
	$planned=$countLineNumber=0;	

	$production_data=array();
	foreach($result as $row)
	{
		$count_hour=0;
		if($row[csf('prod_reso_allo')]==1)
		{
			$operator = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			if ($check_line_number_arr[$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			}

			//$total_smv+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			//$total_order_qty+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

			$item_smv=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv'];

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");			

		    for($h=$hour; $h<=$last_hour; $h++)
			{
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				$replace_hour="replace_hour".substr($bg_hour,0,2);
				$production_data[$line_name]["production_date"]=$row[csf("production_date")];
				$production_data[$line_name]["operator"]=$operator;
				$production_data[$line_name]["helper"]=$helper;
				$production_data[$line_name]["hourly_target"]=$hourly_target;
				$production_data[$line_name]["working_hour"]=$working_hour;
				$production_data[$line_name]["target_efficiency"]=$target_efficiency;
				$production_data[$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$production_data[$line_name]["$replace_hour"]+=$row[csf("$replace_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];
				$totalReplaceQnty +=$row[csf($replace_hour)];

				$total_smv+=$item_smv*$row[csf($prod_hour)];

				if (date('G') == $h) break;  // Up to  Current hour calculation	
				$count_hour++;
			}
		}
		else
		{
			$operator = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			if ($check_line_number_arr[$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			}

			//$total_smv+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			//$total_order_qty+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			$item_smv=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv'];

			$line_name = $lineArr[$row[csf('sewing_line')]];

		    for($h=$hour; $h<$last_hour; $h++)
			{						
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				$replace_hour="replace_hour".substr($bg_hour,0,2);
				$production_data[$line_name]["production_date"]=$row[csf("production_date")];
				$production_data[$line_name]["operator"]=$operator;
				$production_data[$line_name]["helper"]=$helper;
				$production_data[$line_name]["hourly_target"]=$hourly_target;
				$production_data[$line_name]["working_hour"]=$working_hour;
				$production_data[$line_name]["target_efficiency"]=$target_efficiency;
				$production_data[$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$production_data[$line_name]["$replace_hour"]+=$row[csf("$replace_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];
				$totalReplaceQnty +=$row[csf($replace_hour)];

				$total_smv+=$item_smv*$row[csf($prod_hour)];
				
				if (date('G') == $h) break;  // Up to  Current hour calculation
				$count_hour++;
			}
		}	
	}
	//echo $count_hour;//die;
	//echo '<pre>';print_r($production_data);die;

	$current_minute=date('i', time());
	$current_hour=$current_minute/60;

	// Hour calculation without lunch our and count hour is not greter than working hour
	if (date('G')>13){
		$count_hour=$count_hour-1;
	}
	$total_count_hour=$count_hour+$current_hour;
	if ($total_count_hour>$working_hour) $total_count_hour=$working_hour;
	//echo $total_smv.'**'.$totalProdQnty;
	$smv_average=$total_smv/$totalProdQnty;
	//$smv_average=$total_smv/$total_order_qty;
	$day_terget=$hourly_target*$working_hour;
	$current_target=$hourly_target*$total_count_hour;
	$varriance=$current_target-$totalProdQnty;
	$trend=($totalProdQnty/$total_count_hour)*$working_hour;

	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$dhu = ($totalProdQnty/$totalGoodAlrSpotRejectQnty)*100-100; //(qcQty/total_qty)*100-100;
	$efficiency=(($totalProdQnty*$smv_average)/(($operator+$helper)*$total_count_hour*60))*100;
	//efficiency=(prod_qty*smv)/((operators+helpers)*Number of hours*Minutes)

	if ($order_ids != "")
	{
		$sql_defect = "SELECT b.defect_qty as defect_qty FROM pro_garments_production_mst a, pro_gmts_prod_dft b WHERE a.id=b.mst_id and b.production_type=5 and b.po_break_down_id in($order_ids) $company_cond $floor_cond $location_cond $line $txt_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		//and b.insert_date between '".$txt_date."' and '".$txt_date." 11:59:59 PM'
		$sql_defect_res=sql_select($sql_defect);
		$totDefectQnty=0;
		foreach ($sql_defect_res as $row) {
			$totDefectQnty += $row[csf('defect_qty')];
		}
	}
	//echo $totDefectQnty;
	//echo$totDefectQnty.'**'.$totalGoodAlrSpotRejectQnty;die;
   
	?>

	<style type="text/css">
		#secondtime table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
		.rpt_table td {
		    border: 3px solid #000;
		    padding-left: 1px;
		}
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="firsttime">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height: 8%;">    			
    			<td style="width: 30%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">Buyer: <? echo $buyer_name; ?></td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
				ST: <? if (strlen($style_ref_no)>12) echo substr($style_ref_no,0,10).'..'; else echo $style_ref_no; ?>
				</td>
    			<td style="width: 10%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">FL: <? echo $cbo_floor_name; ?></td>
				<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;">Line: <? echo $cbo_line_name; ?></td>
    			<td style="width: 8%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;">
				<?				
				if ($count_hour == 0) $time='0'.$count_hour.':'.date('i', time());
				else if (date('G')==13) $time='0'.$count_hour.':00';
				else if ($count_hour==$working_hour && $count_hour < 10) $time='0'.$count_hour.':00';
				else if ($count_hour==$working_hour && $count_hour > 9) $time=$count_hour.':00';
				else if ($count_hour > 9) $time=$count_hour.':'.date('i', time());
				else  $time='0'.$count_hour.':'.date('i', time());
				echo $time;
				?>
				</td>
    			<td style="width: 12%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 5%;">
    			<td style="color: white; font-weight:bold; text-align: left; vertical-align: middle;">
    				<div style="font-size: 2vw;">PRODUCTION</div>
    			</td>
    			<td colspan="2" rowspan="2" style="color: white; font-size: 2vw; font-weight:bold;">
    				<div style="font-size: 2vw; height: 35%; text-align: left;">Day Target</div>    				
    				<div style="font-size: 6vw; height: 65%; text-align: right; font-weight:900;"><?= $day_terget; ?>&nbsp;</div>
    			</td>
    			<td rowspan="3" colspan="3" style="color: white; font-size: 2vw; font-weight:bold; width: 33%; height: 75%;">
    				<table style="width: 100%;height: 100%;">
    					<tr>
    						<td colspan="3" style="border:0; width: 100%; font-size: 2vw;" align="left" >Efficiency</td>
    					</tr>
    					<tr>	
    						<td colspan="3" style="border:0;" align="center">
    							<canvas id="canvas" style="width:100%; max-width:100%"></canvas>
    							<input type="hidden" id="efficiency" value="<?= floor($efficiency); ?>">
    						</td>    						
    					</tr>
    					<tr>
    						<td colspan="3" style="border:0; text-align: center; width: 100%" align="center" ><div style="font-size: 2vw; text-align: center; vertical-align: middle;"><span>Planned</span>&nbsp;<span style="font-weight:900;"><?= number_format($planned/$countLineNumber,2); ?>%</span></div>
    						</td>
    					</tr>
    				</table> 					
    			</td>
			</tr>

			<tr style="height: 25%; width: 100%;">
    			<td style="color: white; font-weight:bold;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;  width: 100%;">Target</div>
    				<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right;"><span style="width: 80%; text-align: right;"><?= floor($current_target); ?></span><span style="width: 20%; text-align: right;">&nbsp;&nbsp;</span></div>
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
    					<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  $totalProdQnty; ?></span><span style="width: 20%; text-align: right; color: green; font-weight:900;"></span></div>
    					<?
    				}
    				else
    				{
    					?>
							<div style="font-size: 4vw; text-align: right; float:right; color:red;">&#9660;</div>
						</div>
    					<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  $totalProdQnty; ?></span><span style="width: 20%; text-align: right;  font-weight:900;"></span></div>
    					<?
    				}
    				?>	
    			</td>
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;">Trend</div>
    				<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><?= round($trend); ?>&nbsp;</div>
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
	        			<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; font-weight:bold;"></span></div>
	        			<?
	        		}
	        		else
	        		{
	        			?>
							<div style="font-size: 4vw; text-align: right; float:right; color:red;">&#9660;</div>
						</div>
	        			<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; font-weight:bold;"></span></div>
	        			<?
	        		}
	        		?>    				
    			</td>
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;">DHU%</div>
    				<div style="font-size:6vw; height: 80%; text-align: right; color: red; font-weight:900;"><?= trim(number_format($dhu,2),'-'); ?>%&nbsp;</div>
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
    				<div style="font-weight: 900;">SMV Avg:&nbsp;<?= number_format($smv_average,2); ?></div>
    			</td>
    			<td colspan="5" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle; background-color: #FF0000;">
    				<div><span>Defectives:&nbsp;</span><span style="font-weight: 900;"><?=($totalAlterQnty+$totalSpotQnty+$totalRejectQnty); ?>,</span><span>&nbsp;&nbsp;Defects:&nbsp;</span><span style="font-weight: 900;"><?= $totDefectQnty; ?>,</span>&nbsp;&nbsp;Rectified:&nbsp;<span style="font-weight: 900;"><?= $totalReplaceQnty; ?>,</span><span>&nbsp;&nbsp;Reject:&nbsp;</span><span style="font-weight: 900;"><?= $totalRejectQnty; ?></span></div>
    			</td>
			</tr>
		</table>        
    </div>


	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none; background-color: #262626;" id="secondtime">
    	<table style="width: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
    		<tr style="height: 10%;">    			
    			<td colspan="2" style="width: 26%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">Buyer: <? echo $buyer_name; ?></td>
    			<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
				ST: <? if (strlen($style_ref_no)>12) echo substr($style_ref_no,0,10).'..'; else echo $style_ref_no; ?>
				</td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">Floor: <? echo $cbo_floor_name; ?></td>
				<td style="width: 10%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;">
				Line: <? echo $cbo_line_name; ?>
				</td>
    			<td style="width: 12%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;">
					<?
					if ($count_hour == 0) $time='0'.$count_hour.':'.date('i', time());
					else if (date('G')==13) $time='0'.$count_hour.':00';
					else if ($count_hour==$working_hour && $count_hour < 10) $time='0'.$count_hour.':00';
					else if ($count_hour==$working_hour && $count_hour > 9) $time=$count_hour.':00';
					else if ($count_hour > 9) $time=$count_hour.':'.date('i', time());
					else  $time='0'.$count_hour.':'.date('i', time());
					echo $time;
					?>
				</td>
    			<td style="width: 12%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><? echo date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 8%;">
				<td style="width: 8%;color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Hour</td>
				<td style="width: 18%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Hourly (Pcs) Actual/Plan</td>
				<td style="width: 18%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Cum. (Pcs) Actual/Plan</td>
				<td style="width: 20%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Variance (Pcs) Hourly/Cum.</td>
				<td style="width: 10%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">DHU%</td>
				<td style="width: 12%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Actual Eff. %</td>
				<td style="width: 12%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Eff. Variance %</td>   			
			</tr>
			<?
			$hour_count=1;
			$varriancePcsHourly=0;
			$varriancePcsCum=$tot_hourly_target=0;
			$tot_good_qty=0;    		
			foreach($production_data as $line => $row)
			{
				for($h=$hour; $h<=$last_hour; $h++)
				{
					if ( date('G') == 13 && $h >= 13) { break; }
					if ($h == 13) { continue; }
					
					//if ($h>=13 && $h<14){
					/* if ($h>13){
						$hour_count=$hour_count;
					} */
					
					if ($hour_count>$working_hour) { break; }
					
					$hourlyTotalGoodAlterSpotRejectQty=0;
					$hourlyDhu=0;
					//$hourly_target=$target_efficiency=0;
					//$operator=$helper=0;
					$prod_hour='prod_hour'.substr($start_hour_arr[$h],0,2);
					$alter_hour='alter_hour'.substr($start_hour_arr[$h],0,2);
					$spot_hour='spot_hour'.substr($start_hour_arr[$h],0,2);
					$reject_hour='reject_hour'.substr($start_hour_arr[$h],0,2);
					$hourlyTotalGoodAlterSpotRejectQty=$row[$alter_hour]+$row[$spot_hour]+$row[$reject_hour];
					$hourly_target = $row['hourly_target'];
					$target_efficiency = $row['target_efficiency'];
					$operator = $row['operator'];
					$helper   = $row['helper'];
					$varriancePcsHourly=$hourly_target - $row[$prod_hour];
					$tot_hourly_target += $hourly_target;
					$tot_good_qty += $row[$prod_hour];
					//$actual_efficiency = ($row[$prod_hour]*$total_smv)/(($operator+$helper)*60);
					$hourlyDhu = ($hourlyTotalGoodAlterSpotRejectQty/$row[$prod_hour])*100; //(qcQty/total_qty)*100-100;
					//$actual_efficiency=(($row[$prod_hour]*$total_smv)/(($operator+$helper)*60))*100;
					if (date('G') == $h) $actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*$current_minute))*100;
					else $actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*60))*100;
					?>
					<tr id="tr_<?= $h; ?>"style="height: 7%;" >
						<td style="width: 8%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= $hour_count; ?></td>
						<?
						if ($row[$prod_hour] < $hourly_target)
						{			    				
							?>
							<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><span style="color: red;"><?= number_format($row[$prod_hour],0); ?></span><span><?= '/'.$hourly_target; ?></span></td>
							<?
						}
						else
						{
							?>
							<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><span style="color: green;"><?= $row[$prod_hour]; ?></span><span><?= '/'.$hourly_target; ?></span></td>
							<?
						}
						?>

						<?
						if ($tot_good_qty < $tot_hourly_target)
						{
							?>
							<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span><span><?= '/'.$tot_hourly_target; ?></span></td>
							<?
						}
						else
						{
							?>
							<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><span style="color: green;"><?= $tot_good_qty; ?></span><span><?= '/'.$tot_hourly_target; ?></span></td>
							<?
						}
						?>


						<?
						if ( $varriancePcsHourly > 0)
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width: 20%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><span style="color: green;"><?= $varriancePcsHourly; ?></span><span><?= '/'.trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}
						else
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width: 20%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><span style="color: red;"><?= trim($varriancePcsHourly,'-'); ?></span><span><?= '/'.trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}	
						?>	
						<td style="width: 10%; color: red; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= trim(number_format($hourlyDhu,2),'-').'%';?></td>
						<td style="width: 12%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= number_format($actual_efficiency,2); ?>%</td>
						<?			    			
						$efficiency_variance=$target_efficiency-$actual_efficiency;
							
						if ($efficiency_variance > 0)
						{
							?>
							<td style="width: 12%; color: red; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
							<?= ($actual_efficiency>0)?number_format(trim($efficiency_variance,'-'),2):"0.00"; ?>%
							</td>
							<?
						}
						else
						{
							?>
							<td style="width: 12%; color: green; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
							<?= 
							($actual_efficiency>0)?number_format(trim($efficiency_variance,'-'),2):0.00; 
							?>%</td>
							<?
						}	
						?>			    			
					</tr>
					<?
								
					if (date('G') == $h) { break; }
					$hour_count++;
					//if ( date('G') == 13 && $h >= 12){ break;}
					    					    		
		    	}
		    }			
	    	?>
    	</table>		
    </div>
    <?
	exit();
}

?>