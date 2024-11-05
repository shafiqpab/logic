<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","",trim($cbo_company_id));
	$cbo_location_id=str_replace("'","",trim($cbo_location_id));
	$cbo_floor_id=str_replace("'","",trim($cbo_floor_id));
	//$txt_date=str_replace("'","",trim($txt_date));
	$max_height=str_replace("'","",trim($max_height));
	$txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
	
    $buyer_library    = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');
    $floor_library    = return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');

    // Check Variable Setting Use Prod Resource Allocation
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");

	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	$lineIds="";
	if($prod_reso_allo == 1)
	{
		$sql_acresource="SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_id and a.location_id=$cbo_location_id and a.floor_id=$cbo_floor_id and b.pr_date='$txt_date'";
	    $dataArray = sql_select($sql_acresource);
	  
		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('id')]]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]]['working_hour']=$row[csf('working_hour')];
			$prod_resource_array[$row[csf('id')]]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]]['target_efficiency']=$row[csf('target_efficiency')];
			$prod_resource_array[$row[csf('id')]]['operator']=$row[csf('operator')];
			$prod_resource_array[$row[csf('id')]]['helper']=$row[csf('helper')];
			$prod_resource_array[$row[csf('id')]]['man_power']=$row[csf('man_power')];
			$prod_resource_array[$row[csf('id')]]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$lineIds .= $row[csf('id')].',';
			$prod_reso_line_arr[$row[csf('id')]]=$row[csf('line_number')];
		}
	}	
	unset($dataArray);
	//echo "<pre>";print_r($prod_resource_array);die;

	//$lineIds = implode(',', array_flip(array_flip(explode(',', rtrim($lineIds,',')))));

	$start_time_arr=array();
	$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name=$cbo_company_id and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
    //$prod_start_hour=$start_time_arr[1]['pst'];
	//if($prod_start_hour=='') 
	$prod_start_hour='07:00';
	$start_time=explode(':',$prod_start_hour);
	$hour=(int) substr($start_time[0],1,1);

	$last_hour=23;
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
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN d.production_qnty else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.replace_qty else 0 END) AS $replace_hour,";
		}
		else
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN d.production_qnty else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.replace_qty else 0 END) AS $replace_hour,";
		}
		$first=$first+1;
	}
	$prod_hour='prod_hour'.$last_hour;
	$alter_hour="alter_hour".$last_hour;
	$spot_hour="spot_hour".$last_hour;
	$reject_hour="reject_hour".$last_hour;
	$replace_hour="replace_hour".$last_hour;

	$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type in(5) THEN d.production_qnty else 0 END) AS $prod_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.replace_qty else 0 END) AS $replace_hour";
														
	$sql.=" FROM pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c
		where a.production_type=5 and a.id=d.mst_id and  a.po_break_down_id=c.id and b.id=c.job_id and a.status_active=1 and a.is_deleted=0and a.serving_company=$cbo_company_id and a.location=$cbo_location_id and a.floor_id=$cbo_floor_id and a.production_date='$txt_date'
		group by a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number, c.grouping";  	
	//die();
	//echo $sql;die;

	$result = sql_select($sql);
	$item_number_id=$style_ref_nos="";
	
	foreach($result as $row)
	{
		$poId .= $row[csf("po_id")].",";
		$floor_name .= $floor_library[$row[csf('floor_id')]].',';
		$buyer_name .= $buyer_library[$row[csf('buyer_name')]].',';
		$style_ref_no .= $row[csf('style_ref_no')].',';
		//$int_ref_no .= $row[csf('grouping')].',';
		$job_no .= "'".$row[csf('job_no')]."',";
		$po_number .= $row[csf('po_number')].',';
		if ($row[csf('grouping')] != "") $grouping .= $row[csf('grouping')].',';
		$item_number_id .= $row[csf('item_number_id')].',';
		$style_ref_nos .= "'".$row[csf('style_ref_no')]."',";
		$jobNOArr[$row[csf('job_no')]]=$row[csf('job_no')];
	}
	//echo $job_no.'D';
	$order_no = implode(',', array_flip(array_flip(explode(',', rtrim($po_number,',')))));
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));
	$item_number_ids = implode(',', array_flip(array_flip(explode(',', rtrim($item_number_id,',')))));
	$floor_name = implode(',', array_flip(array_flip(explode(',', rtrim($floor_name,',')))));
	$buyer_name = implode(',', array_flip(array_flip(explode(',', rtrim($buyer_name,',')))));
	$style_ref_no = implode(',', array_flip(array_flip(explode(',', rtrim($style_ref_no,',')))));
	$style_ref_nos = implode(',', array_flip(array_flip(explode(',', rtrim($style_ref_nos,',')))));
	$job_no = implode(',', array_flip(array_flip(explode(',', rtrim($job_no,',')))));
	$grouping = implode(',', array_flip(array_flip(explode(',', rtrim($grouping,',')))));

	$smv_source=sql_select("select company_name, smv_source from variable_settings_production where variable_list=25 and status_active=1 and is_deleted=0 and company_name=$cbo_company_id");
	foreach($smv_source as $val){
		$smv_source=$val[csf('smv_source')];
	}
	//echo $smv_source;
	$sql_item="SELECT a.style_ref_no, b.id as color_size_id, b.po_break_down_id, b.order_quantity, c.gmts_item_id, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and b.is_deleted=0 and b.po_break_down_id in($order_ids)";
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
		$style_item_qnty_arr[$row[csf('style_ref_no')]][$row[csf('gmts_item_id')]]+=$row[csf('order_quantity')];
	}

	$sql_po_dtls="SELECT b.style_ref_no,a.item_number_id,c.po_quantity
	from pro_garments_production_mst a, wo_po_break_down c, wo_po_details_master b 
	where a.po_break_down_id=c.id and c.job_id=b.id and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.id in($order_ids)
	group by b.style_ref_no,a.item_number_id,c.po_quantity";
	$poResultItem=sql_select($sql_po_dtls);
	$style_item_qnty_arr=array();
	foreach($poResultItem as $row)
	{
		$style_item_qnty_arr[$row[csf('style_ref_no')]][$row[csf('item_number_id')]]+=$row[csf('po_quantity')];
	}

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
			$operator = $prod_resource_array[$row[csf('sewing_line')]]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]]['helper'];
			$man_power   = $prod_resource_array[$row[csf('sewing_line')]]['man_power'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]]['target_efficiency'];

			if ($check_line_number_arr[$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('sewing_line')]]['target_efficiency'];
			}

			$total_smv+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			$total_order_qty+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

			$item_smv=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv'];

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");
			
			$production_data[$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';
			$production_data[$line_name]["grouping"]=$row[csf("grouping")].',';
		    $production_data[$line_name]["operator"]=$operator;
			$production_data[$line_name]["helper"]=$helper;
			$production_data[$line_name]["hourly_target"]=$hourly_target;
			$production_data[$line_name]["working_hour"]=$working_hour;
			$production_data[$line_name]["man_power"]=$man_power;
			$production_data[$line_name]["day_terget"]=$hourly_target*$working_hour;

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
				$production_data[$line_name]["current_target"]=$hourly_target;
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

				$production_data[$line_name]["totalProdQnty"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["totalAlterQnty"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["totalSpotQnty"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["totalRejectQnty"]+=$row[csf("$reject_hour")];
				$production_data[$line_name]["totalReplaceQnty"]+=$row[csf("$replace_hour")];
				$production_data[$line_name]["line_wise_total_smv"]+=$item_smv*$row[csf($prod_hour)];

				$total_smv+=$item_smv*$row[csf($prod_hour)];

				if (date('G') == $h) break;  // Up to  Current hour calculation	
				$count_hour++;
			}
		}
		else
		{
			$operator = $prod_resource_array[$row[csf('sewing_line')]]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]]['helper'];
			$man_power   = $prod_resource_array[$row[csf('sewing_line')]]['man_power'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]]['target_efficiency'];

			if ($check_line_number_arr[$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('sewing_line')]]['target_efficiency'];
			}

			$total_smv+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			$total_order_qty+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			$item_smv=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv'];

			$line_name = $lineArr[$row[csf('sewing_line')]];

			$production_data[$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';
			$production_data[$line_name]["grouping"]=$row[csf("grouping")].',';
		    $production_data[$line_name]["operator"]=$operator;
			$production_data[$line_name]["helper"]=$helper;
			$production_data[$line_name]["hourly_target"]=$hourly_target;
			$production_data[$line_name]["working_hour"]=$working_hour;
			$production_data[$line_name]["man_power"]=$man_power;
			$production_data[$line_name]["day_terget"]=$hourly_target*$working_hour;	    

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
				$production_data[$line_name]["current_target"]=$hourly_target;
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

				$production_data[$line_name]["totalProdQnty"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["totalAlterQnty"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["totalSpotQnty"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["totalRejectQnty"]+=$row[csf("$reject_hour")];
				$production_data[$line_name]["totalReplaceQnty"]+=$row[csf("$replace_hour")];
				$production_data[$line_name]["line_wise_total_smv"]+=$item_smv*$row[csf($prod_hour)];

				$total_smv+=$item_smv*$row[csf($prod_hour)];
				
				if (date('G') == $h) break;  // Up to  Current hour calculation
				$count_hour++;
			}
		}	
	}
	//echo $count_hour;//die;
	//echo '<pre>';print_r($production_data);die;	
	//$total_smv_min=$total_order_qnty=0;
	//$smv_average=($total_smv/$total_order_qty)
	//echo $totalProdQnty;

	$operator=$helper=0;
	$smv_average=$total_working_hour=0;
	$highest_working_hour=0;
	foreach ($production_data as $key => $val) 
	{
		$day_terget+=$val['day_terget'];	
		$current_target+=$val['current_target'];
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
	if (date('G')==13) $current_hour=0;
	else if (date('G')>13) $count_hour=$count_hour-1;

	$count_hour=$count_hour;
	$avg_count_hour=$count_hour+$current_hour;
	if ($avg_count_hour>$highest_working_hour) {
		$count_hour=$highest_working_hour;
		$avg_count_hour=ceil($total_working_hour/$countLineNumber);
	}

	$smv_average=($total_smv/$total_order_qty)*1;
	$current_target=$current_target*$avg_count_hour;
	$varriance=$current_target-$totalProdQnty;
	$trend=($totalProdQnty/$avg_count_hour)*$highest_working_hour;
	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$totalAlrSpotQnty=$totalAlterQnty+$totalSpotQnty;
	$totalAlrSpotRejectQnty=$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$dhu = ($totalAlrSpotRejectQnty/$totalProdQnty)*100; //(qcQty/total_qty)*100-100;
	$rft = (($totalGoodAlrSpotRejectQnty-$totalAlrSpotQnty)/$totalGoodAlrSpotRejectQnty)*100;
	$efficiency=(($totalProdQnty*$smv_average)/(($operator+$helper)*$avg_count_hour*60))*100;  
	
	
	$ws_gmts_itemsql = sql_select("select a.id,a.style_ref,a.gmts_item_id,upper(b.item_name) as item_name,(a.applicable_period) applicable_period from ppl_gsd_entry_mst a,lib_garment_item b where a.gmts_item_id=b.id and a.style_ref in(".$style_ref_nos.") and a.gmts_item_id in($item_number_ids) and a.bulletin_type=4 and a.status_active=1 and a.is_deleted=0 and a.applicable_period<='$txt_date' group by a.id,a.style_ref,a.gmts_item_id,a.applicable_period,b.item_name order by a.applicable_period desc");
	
	foreach ($ws_gmts_itemsql as $row)
	{
		$ws_gmts_item= $row[csf('item_name')];
		$wsItemArr[$row[csf('id')]]=$row[csf('id')];
	}

	if ($order_ids != "")
	{
		//$sql_defect = "SELECT TO_CHAR(a.production_hour,'HH24') as production_hour, b.defect_qty as defect_qty FROM pro_garments_production_mst a, pro_gmts_prod_dft b WHERE a.id=b.mst_id and b.production_type=5 and b.po_break_down_id in($order_ids) $company_cond $floor_cond $location_cond $line $txt_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		$sql_defect = "SELECT TO_CHAR(a.production_hour,'HH24') as production_hour, b.defect_qty as defect_qty 
		FROM pro_garments_production_mst a, pro_gmts_prod_dft b,pro_garments_production_dtls c
		WHERE a.id=b.mst_id and b.dtls_id=c.id and b.production_type=5  and b.po_break_down_id in($order_ids) $company_cond $floor_cond $location_cond $txt_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		and c.status_active=1 and c.production_qnty=0 and c.reject_qty=0";
		$sql_defect_res=sql_select($sql_defect);
		$totDefectQnty=0;
		$hour_wise_totDefectQnty_arr=array();
		foreach ($sql_defect_res as $row) 
		{
			$production_hour=(int)$row[csf('production_hour')];
			if ($production_hour<=$hour && $production_hour>=1){
				$hour_wise_totDefectQnty_arr[$production_hour]+=$row[csf('defect_qty')];
			}
			else $hour_wise_totDefectQnty_arr[$production_hour]+=$row[csf('defect_qty')];
			$totDefectQnty += $row[csf('defect_qty')];
		}
	}
	
	if ($order_ids != "")
	{
		//$sql_defect_count = "SELECT * from (SELECT B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID,SUM(B.DEFECT_QTY) AS POINT_COUNT FROM pro_garments_production_mst a, pro_gmts_prod_dft b WHERE a.id=b.mst_id and b.production_type=5 and b.po_break_down_id in($order_ids) $company_cond $floor_cond $location_cond $line $txt_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.defect_type_id,b.defect_point_id order by point_count desc) where rownum<=3";

		$sql_defect_count = "SELECT * from (SELECT B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID,SUM(B.DEFECT_QTY) AS POINT_COUNT
		FROM pro_garments_production_mst a, pro_gmts_prod_dft b,pro_garments_production_dtls c 
		WHERE a.id=b.mst_id and b.dtls_id=c.id and b.production_type=5 and b.po_break_down_id in($order_ids) $company_cond $floor_cond $location_cond $txt_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and c.status_active=1 and c.production_qnty=0 and c.reject_qty=0
		group by b.defect_type_id,b.defect_point_id
		order by point_count desc) where rownum<=3";// and TRUNC(b.insert_date)='6/June/2023'

		$sql_defectcnt_res=sql_select($sql_defect_count);
		$totDefectPoint=0;
		$DefectCount_arr=array();
		foreach ($sql_defectcnt_res as $row) {
			$defect_type_id = $row["DEFECT_TYPE_ID"];
			$defect_point_id = $row["DEFECT_POINT_ID"];
			$totDefectCount+= $row["POINT_COUNT"];
		}
	}	
	 
	?>

	<style type="text/css">

		#secondtime table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
		.rpt_table td {
		    border: 4px solid #000;
		    padding-left: 1px;
		}
		:root {
		--step--2: clamp(3.13rem, 2.62rem + 2.51vw, 5.76rem);
		--step--1: clamp(3.75rem, 3.09rem + 3.29vw, 7.20rem);
		--step-0: clamp(4.50rem, 3.64rem + 4.29vw, 9.00rem);
		}

		@-webkit-keyframes marquee {
		from {
			transform: translateX(0);
		}
		to {
			transform: translateX(-100%);
		}
		}

		@keyframes marquee {
		from {
			transform: translateX(0);
		}
		to {
			transform: translateX(-100%);
		}
		}
		@-webkit-keyframes marquee-r {
		from {
			transform: translateX(-100%);
		}
		to {
			transform: translateX(0);
		}
		}
		@keyframes marquee-r {
		from {
			transform: translateX(-100%);
		}
		to {
			transform: translateX(0);
		}
		}
		@-webkit-keyframes rollCage {
		from {
			transform: rotate(0);
		}
		to {
			transform: rotate(5turn);
		}
		}
		@keyframes rollCage {
		from {
			transform: rotate(0);
		}
		to {
			transform: rotate(5turn);
		}
		}
	/*marquee start*/		
	.marquee {  
				 overflow: hidden;
				 overflow-style:marquee-block
				 position: relative;
				 align-items: center;
				 text-align-last:center;
				/* background-color: #cfb53b;*/
				/* color: #ffffff;*/
				}
	.marqueeText {
		 display: flex;
		 /*width: 100%;*/ 
		  align-items: center;
		  text-align-last:center;
		 /*color:#FF0000;*/
		 /* Starting position */
		 -moz-transform:translateX(0);
		 -webkit-transform:translateX(0);   
		 transform:translateX(0);
		 /* Apply animation to this element */  
		 -moz-animation: scroll-left 10s linear infinite;
		 -webkit-animation: scroll-left 10s linear infinite;
		 animation: scroll-left 10s linear infinite;
		}
	.marqueeText p {
	 /*width: 100%;*/
	 flex-shrink: 0;
	 margin: 0;
	 line-height: 1.2;
	/* text-align: center;*/
	}
/* Move it (define the animation) */
@-moz-keyframes scroll-left {
 0%, 45%, 100% { -moz-transform: translateX(0); }
 50%, 95% { -moz-transform: translateX(-100%); } 
}
@-webkit-keyframes scroll-left {
 0%, 45%, 100% { -webkit-transform: translateX(0); }
 50%, 95% { -webkit-transform: translateX(-100%); } 
}
@keyframes scroll-left {
 0%, 45%, 100% { 
 -moz-transform: translateX(0); /* Browser bug fix */
 -webkit-transform: translateX(0); /* Browser bug fix */
 transform: translateX(0);      
 }
  50%, 95% { 
 -moz-transform: translateX(-100%); /* Browser bug fix */
 -webkit-transform: translateX(-100%); /* Browser bug fix */
 transform: translateX(-100%);      
 }
}
/*marquee end*/
/*marquee top-bottom start*/

/*marquee top-bottom end*/
/*flip start*/		
		.flip {
			animation: rotate 10s infinite; 
			-webkit-animation: rotate 10s infinite;
		}
		
		@-webkit-keyframes rotate {
			100% {
				transform: rotateY(0deg);
			}
		}
		@keyframes rotate {
			100% {
				transform: rotateY(359deg);
			}
		}
/*flip end*/		
		.rotate {
					-webkit-animation: rotation 20s infinite linear;
				}

			@-webkit-keyframes rotation {
					from {-webkit-transform: rotate(0deg);}
					to   {-webkit-transform: rotate(359deg);}
			}
			
		.rotate2 {
					-webkit-animation: spin 10s infinite linear;
					}

				@-webkit-keyframes spin {
					0%   {-webkit-transform: rotate(0deg)}
					100% {-webkit-transform: rotate(360deg)}
				}
		.margin-top-8{margin-top:10px !important;}
		.status-red{color:red;}
		.status-green{color:green;}


.shake2 {
  animation: wiggle 10s linear infinite;
  color: #F90;/*#F90*/
}

/* Keyframes */
@keyframes wiggle {
  0% { transform: rotate(0deg); }
  25% { transform: rotate(15deg); }
  50% { transform: rotate(0eg); }
  75% { transform: rotate(-15deg); }
  100% { transform: rotate(0deg); }
}
.shake {
  animation: wiggle 5s linear infinite;
  color: #F90;/*#F90*/
}
@keyframes wiggle {
	0% { transform: translateX(0) }
 	25% { transform: translateX(15px) }
 	50% { transform: translateX(-15px) }
 	75% { transform: translateX(15px) }
 	100% { transform: translateX(0) }
} 
.blink {
                animation: blinker 2s linear infinite;
                color: #F00;
                /*font-family: sans-serif;*/
				/*font-weight:900*/
            }
            @keyframes blinker {
               50% {
                    opacity:0;
                }
            }
.border{
  border: 3px solid white;
  padding: 0px;
  border-radius:5px;
  width: 50px;
}
.rounded-bg {
    background-clip: padding-box;
    border-radius: 25px;
    background-color:#262626;
    color:white;
    padding: 5px 10px;
    display: inline; 
}
	#secondtime table tr td.fontSizeHeader { font-size: 1.8vw; color: white; font-weight:bold;}
	#secondtime table tr td.fontSizeHeader2 { font-size: 1.4vw; color: white; font-weight:bold;}
	#secondtime table tr td.fontSizeCharacter { font-size: 1.4vw; color: white; font-weight:bold;}
	#secondtime table tr td.fontSizeNumber { font-size: 2vw; color: white; font-weight:bold;}
	#secondtime table tr td{padding: 2px; }
	.textRight{text-align:right;}
	.textCenter{text-align:center;}
	.verticalMiddle{vertical-align: middle;}
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="firsttime">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height:6%;background-color:#333;"> 
            <td style="width:6%;vertical-align: middle;" align="center">
            
          <span><img class="flip" src="../../images/logic/mfg_logo.png" id="logininfo" width="70"></span>  
           
            </td>   			
    			<td style="width:17%;color:white;font-size:1.8vw;font-weight:bold;text-align:center;vertical-align:middle;"><u>BUYER</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $buyer_name; ?></marquee></td>
                
                <td style="width:11%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center;vertical-align:middle;"><u>IR NO</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $grouping; ?></marquee></td>
                
    			<td style="width:16%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center;vertical-align:middle;"><u>STYLE</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? if (strlen($style_ref_no)>100) echo substr($style_ref_no,0,10).'..'; else echo $style_ref_no; ?></marquee></td>
                
    			<td style="width:16%;color:white;font-size:1.8vw; font-weight:bold; text-align:center;vertical-align:middle;"><u>FLOOR</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $floor_library[$cbo_floor_id]; ?></marquee></td>
               
    		<td style="width:26%;color:white; font-size:1.7vw;font-weight:bold;text-align:center;vertical-align:middle;">
                <div class="marquee">
                <div class="marqueeText">
                <p>Time Past<br>
				<?				
				if ($count_hour == 0) $time='0'.$count_hour.':'.date('i', time());
				else if (date('G')==13) $time='0'.$count_hour.':00';
				else if ($count_hour==$working_hour && $count_hour < 10) $time='0'.$count_hour.':00';
				else if ($count_hour==$working_hour && $count_hour > 9) $time=$count_hour.':00';
				else if ($count_hour > 9) $time=$count_hour.':'.date('i', time());
				else  $time='0'.$count_hour.':'.date('i', time());
				echo $time;
				?>
                </p>
                <p class="Slide down..."><?=  date('d-M-y').'<br/>'.date('h:i A', time()); ?></p>
                </div>
                </div>
			</td>  

                <td style="width:8%;vertical-align: middle;" align="center">
               <span><img class="flip" src="../../images/logic/logic_logo_new.png" id="logininfo" width="115"></span>
             </td>   
                
    			<!--<td style="width: 9%; color: white; font-size: 1.6vw; font-weight:bold; text-align: center;vertical-align:middle"><?php /*?><?=  date('d-M-y').'<br/>'.date('h:i A', time()); ?><?php */?></td>-->
                
			</tr>

			<tr style="height:5%;">
    			<td colspan="2" style="color:#0FF;font-size:2vw;font-weight:bold; text-align:left; vertical-align:middle;">&nbsp PRODUCTION</td>
                <td style="color:#0FF;font-size:2vw;font-weight:bold; text-align:left;vertical-align:middle;padding-left:5px">
    				SMV: <?= fn_number_format($smv_average,2,'.','','0'); ?>
                    </td>
    			<td colspan="2" rowspan="2" style="color:white;font-weight:bold;">
					<div>
						<div style="font-size: 2vw; height: 35%; text-align: left;">&nbsp DAY TARGET</div>
                        <div>&nbsp </div> 
                        <div>&nbsp </div> 
                        <div>&nbsp </div> 
                        <div>&nbsp </div> 
						<div style="font-size: 6vw; height: 65%; text-align:right;font-weight:900; vertical-align:middle"><?= $day_terget;?>&nbsp;</div>
					</div>
    			</td> 
                
    			<td rowspan="3" colspan="3" style="color: white; font-size: 2vw; font-weight:bold; width:33%; height:75%;">
    			<table style="width: 100%;height:100%;">
                    
    			<tr style="height:5%">
    						<td colspan="3" style="border:1;font-size:1.8vw;font-weight:bold;text-align:center;vertical-align: middle;">
                            <div class="marquee">
                			<div class="marqueeText">
                            <P>EFFICIENCY %</P>
                            <P>GARMENT'S IMAGE</P>
                            </div>
                            </div>
                            </td>
    					</tr>
    					<tr style="height:30%">	
    						<td colspan="3" style="border:1;vertical-align:middle;">
								<p>
    							<canvas id="canvas" style="width:95%;"></canvas>
    							<input type="hidden" id="efficiency" value="<?= fn_number_format(round($efficiency),0,'','','0'); ?>">
                             	</p>
    						</td>
    					</tr>
                        
                        <!--<tr style="height:41%">	
    						<td colspan="3" class="shake" style="border:0;vertical-align:middle;">
    							<canvas id="canvas" style="width:110%; height:95%;"></canvas>
    							<input type="hidden" id="efficiency" value="<?php /*?><?= fn_number_format(round($efficiency),0,'','','0'); ?><?php */?>">
    						</td>    						
    					</tr>-->
                      
                  		<tr style="height:5%">
    						<td colspan="3" style="border:1; text-align:center; width: 100%;font-size: 1.8vw; text-align:center; vertical-align: middle;">
                            <p>
                            <span>Planned:</span>&nbsp;<span style="font-weight:900;"><?= fn_number_format(round($planned/$countLineNumber),0,'.','','0'); ?>%</span>&nbsp;&nbsp;
                            <span>Varriance:</span>&nbsp;<span style="font-weight:900;"><?= fn_number_format(round($planned-$efficiency),0,'.','','0'); ?>%</span> 
                            </p>
                            <p style="font-size:1.8vw; vertical-align:middle"><? echo $ws_gmts_item;?></p>
    						</td>
    				</tr>
 		<!--beeresh-->
                    <tbody> 
                    <tr style="height:2%;">
                    <td colspan="3" style="color:white;font-size:1.2vw;background-color:red;vertical-align:middle" align="center">TOTAL DEFECT COUNT =&nbsp;<span style="font-size:1.4vw;"><?= fn_number_format($totDefectQnty,0,'.','','0'); ?></span></td><!-- #090-->
                    </tr>
                    <tr style="height:2%;background-color:#666; border:0;"align="center">
                        <td style="font-size:1.3vw;vertical-align:middle">Top</td>
                        <td style="font-size:1.3vw;vertical-align:middle">Defect Name</td>
                        <td style="font-size:1.3vw;vertical-align:middle">Point Count</td> 
                    </tr>
	            
	                <?
					$i= 1;
					if(count($sql_defectcnt_res)<1)$sql_defectcnt_res=array(1=>"", 2=>"", 3=>"");
					foreach($sql_defectcnt_res as $row)					
					{
						?>
						<tr style="height:6%;color:#FF0";id="tr_<? echo $i; ?>">
						<td style="width:10%;font-size: 1.3vw; font-weight:bold;text-align:center;vertical-align: middle;background-color:#333"> 
							<span>
								<? echo $i;?>
							</span>
						</td>
						<td style="width:60%;font-size: 1vw; font-weight:bold;text-align:left;vertical-align: middle;background-color:#333">
							<span>
								<? 
								if($row["DEFECT_TYPE_ID"] == 1)
								{
									$defect_name = $sew_fin_alter_defect_type[$row["DEFECT_POINT_ID"]];
								}
								if($row["DEFECT_TYPE_ID"] == 2)
								{
									$defect_name = $sew_fin_spot_defect_type[$row["DEFECT_POINT_ID"]];
								}
								if($row["DEFECT_TYPE_ID"] == 3)
								{
									$defect_name = $sew_fin_reject_type_arr[$row["DEFECT_POINT_ID"]];
								}
								echo $defect_name;
								?>
							</span>
						</td>						
						<td style="width:auto;font-size: 1.3vw; font-weight:bold;text-align:center;vertical-align:middle;background-color:#333"> <span><? echo $row["POINT_COUNT"];?></span></td>
	                    </tr>
						<?
	                    $i++;
	                }
	                ?>
	                </tbody>
			<!--beeresh-->    
    				</table> 					
    			</td>
		</tr>

			<tr style="height: 18%; width: 100%;">
    			<td colspan="3" style="color:white; font-weight:bold;">
    				<div style="font-size: 2vw; height: 20%;color:#0FF; text-align: left;  width: 100%;">&nbsp Target</div>
    				<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right;"><span style="width: 80%; text-align: right;"><?= floor($current_target); ?></span><span style="width: 20%; text-align: right;">&nbsp;&nbsp;</span></div> 
    			</td>    			
			</tr>
			<tr style="height: 18%;">
    			<td colspan="3" style="color:white; font-size: 2vw; font-weight:bold; text-align:center; vertical-align: middle;">
					<div style="height: 20%;">
						<div style="font-size: 2vw;color:#0FF; text-align:left;float:left;">&nbsp Actual</div>						    				
    				<?
	        		if ($current_target<=$totalProdQnty)
	        		{
	        			?>
						<div style="font-size: 4vw; text-align: right; float:right; color: #0C0;">&#9650;</div>
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
    				<div style="font-size: 2vw; height: 20%; text-align: left;">&nbsp TREND</div>
    				<div style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><?= fn_number_format(round($trend),0,'.','','0'); ?>&nbsp;</div>
    			</td>
			</tr>
			<tr style="height: 18%;">
    			<td colspan="3" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
					<div style="height: 20%;">	
    					<div style="font-size: 2vw;color:#0FF; text-align: left; float:left;">&nbsp Variance</div>
    				<?
	        		if ($varriance < 0)
	        		{
	        			?>
						<div style="font-size: 4vw; text-align: right; float:right; color:#0C0;">&#9650;</div>
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
                <td style="color: white; font-size: 2vw; font-weight:bold; text-align: center;">
    	<div style="font-size: 2vw; height: 20%; text-align: left;"title="(Good Qty/Check Qty [Check Qty=Good+Alt+Spot+Reject] )*100,Check Qty=<? echo ($totalGoodAlrSpotRejectQnty);?>">&nbsp RFT %</div>
                    
                    <?
						if ($rft ==100)
						{			    				
							?>
							<div style="font-size:4.5vw; height: 80%; text-align: center;padding-top:5%;color:#0C0;font-weight:900;"><?= fn_number_format($rft,0,'.','','0');?></div>
                            
                           <? 
                         }
                         else
						{
							?>
							<div style="font-size:4.5vw; height: 80%; text-align: center;padding-top:5%;color:#0C0;font-weight:900;"><?= fn_number_format($rft,2,'.','','0.00');?></div>
							<?
						}
                        
						?>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center;">
    				<div style="font-size: 2vw; height: 20%; text-align: left;">&nbsp DHU %</div>
    				<div style="font-size:4.5vw; height: 80%; text-align: right;padding-top:5%;color: red; font-weight:900;"><?= fn_number_format($dhu,2,'.','','0.00');?>&nbsp;</div>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold;">
					<div style="font-size: 2vw; height: 20%; text-align:center; ">OPERATOR</div>    				
    				<div style="font-size:6vw; height: 80%; text-align:center;font-weight:900;"><?= fn_number_format($operator,0,'.','','0'); ?></div>    				
    			</td>
    			<td colspan="2" style="color:white; font-size:2vw;font-weight:bold;">    				
					<div style="font-size: 2vw; height: 20%; text-align: center; ">HELPER</div>
    				<div style="font-size:6vw; height: 80%; text-align: center; font-weight:900;"><?= fn_number_format($helper,0,'.','','0'); ?></div>    				
    			</td>
			</tr>
			<tr style="height: 6%;">
    			<td colspan="8" style="color: black; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle; background-color: #0EE">
                     <div>
      			<span>ALTER %</span>
                <span class="rounded-bg" style="font-weight:bold;"title="Alter Qty: <? echo $totalAlterQnty;?>"><?=fn_number_format(($totalAlterQnty/$totalProdQnty)*100,2,'.','','0.00'); ?></span>
      			<span>SPOT %</span>
                <span class="rounded-bg" style="font-weight: bold;"title="Spot Qty: <? echo $totalSpotQnty;?>"><?=fn_number_format(($totalSpotQnty/$totalProdQnty)*100,2,'.','','0.00');?></span>
      			<span>REJECT %</span>
                <span class="rounded-bg" style="font-weight: bold;"title="Reject Qty: <? echo $totalRejectQnty;?>"><?=fn_number_format(($totalRejectQnty/$totalProdQnty)*100,2,'.','','0.00');?></span>
      
      				<?
					 $rectified_title = "(".$totalReplaceQnty."/".$totalAlrSpotQnty.")*";
					 $rectifiedPer=($totalReplaceQnty/$totalAlrSpotQnty)*100;
						if ($rectifiedPer ==100)
						{			    				
							?>
								<span>RECTIFIED %</span>
                                <span class="rounded-bg" style="font-weight: bold;"title="Rectify Qty: <? echo $rectifiedPer;?>"><?=fn_number_format(($totalReplaceQnty/$totalAlrSpotQnty)*100,0,'.','','0');?>,</span>
                    		<? 
                         }
                         else
						{
							?>
								<span>RECTIFIED %</span>
                                <span class="rounded-bg" style="font-weight: bold;"title="Rectify Qty: <? echo $rectified_title;?>"><?=fn_number_format(($totalReplaceQnty/$totalAlrSpotQnty)*100,2,'.','','0.00');?></span>
							<?
						}
						?>
                    </div>
    			</td>
			</tr>
		</table>        
    </div>


	<div style="width:100%; height: <? //echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none; background-color: #262626;" id="secondtime">
    	<table style="width: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" border="0" rules="all" cellpadding="0" cellspacing="0">
    		<tr style="height: 10%;">    			
				<td class="verticalMiddle textCenter" style="width:6%;"><span><img class="flip" src="../../images/logic/mfg_logo.png" id="logininfo" width="70"></span></td>
    			<td colspan="5" class="fontSizeHeader textCenter verticalMiddle" style="width: 32%;"><u>FLOOR</u><br><? echo $floor_library[$cbo_floor_id]; ?></td>
							
    			<td colspan="3" class="fontSizeHeader textCenter verticalMiddle" style="width: 14%;">Time Past:<br>
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
    			<td colspan="3" class="fontSizeHeader textCenter verticalMiddle" style="width: 14%;"><? echo date('d-M-y').'<br/>'.date('h:i A', time()); ?></td>
				<td class="textCenter verticalMiddle" style="width:8%;" ><span><img class="flip" src="../../images/logic/logic_logo_new.png" id="logininfo" width="115"></span></td>
			</tr>

			
            
            <tr style="background-color:#003366;"><!--#003366-->
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 4%;">Line</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 13%;">Buyer</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 13%;">Style</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 13%;">IR</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 6%;">SMV</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 5%;">MP</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 8%;">D.Target</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 7%;">Target</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 7%;">Actual</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 7%;">Variance</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 7%;">Trend</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 7%;">Eff%</td>
				<td class="fontSizeHeader2 textCenter verticalMiddle" style="width: 7%;">DHU%</td>
            </tr>
            
			<?
			$total_variance=$total_man_power=0;
			$total_day_terget=$total_current_target=0;
			$total_actual=$total_trend=0;
			$line_wise_dhu=$totalGoodAlterSpotRejectQnty=0;
			$line_wise_smv_avg=0;
			ksort($production_data);
			$second_trend=0;
			$total_trend=0;
			$totalAlterSpotRejectQnty=0;
			$varriance=0;
			
			foreach ($production_data as $line_name => $row) 
			{
				$buyerName=implode(',', array_flip(array_flip(explode(',', rtrim($row['buyer_name'],',')))));				
				if (strlen($buyerName) > 12) $final_buyerName=substr($buyerName, 0, 12).'..';
				else $final_buyerName=$buyerName;
				$styleRefNo=implode(',', array_flip(array_flip(explode(',', rtrim($row['style_ref_no'],',')))));				
				if (strlen($styleRefNo) > 12) $final_styleRefNo=substr($styleRefNo, 0, 12).'..';
				else $final_styleRefNo=$styleRefNo;
				$poNumber=implode(',', array_flip(array_flip(explode(',', rtrim($row['po_number'],',')))));
				if (strlen($poNumber) > 12) $final_poNumber=substr($poNumber, 0, 12).'..';
				else $final_poNumber=$poNumber;
				$grouping=implode(',', array_flip(array_flip(explode(',', rtrim($row['grouping'],',')))));				
				if (strlen($grouping) > 12) $final_grouping=substr($grouping, 0, 12).'..';
				else $final_grouping=$grouping;
				
				$line_wise_smv_avg=$row['line_wise_total_smv']/$row['totalProdQnty'];
				$totalGoodAlterSpotRejectQnty = $row['totalProdQnty']+$row['totalAlterQnty']+$row['totalSpotQnty']+$row['totalRejectQnty'];
				$totalAlterSpotRejectQnty = $row['totalAlterQnty']+$row['totalSpotQnty']+$row['totalRejectQnty'];
				$line_wise_dhu = ($totalAlterSpotRejectQnty/$row['totalProdQnty'])*100;
				$line_wise_efficiency=(($row['totalProdQnty']*$line_wise_smv_avg)/(($row['operator']+$row['helper'])*$smv_average*60))*100;
				$second_trend=($row['totalProdQnty']/$smv_average)*$working_hour;
				$second_current_target=floor($row['current_target']*$smv_average);

				if ($second_current_target>$row['totalProdQnty']) 
				{
					$varriance=$second_current_target-$row['totalProdQnty'];
					$font_color="color: red;";
				}
				else
				{
					$varriance=$row['totalProdQnty']-$second_current_target;
					$font_color="color: white;";
				}
			
				?>
				<tr id="tr_<?= $h; ?>"style="height: 7%;" >
					<td class="fontSizeCharacter textCenter verticalMiddle" style="width: 4%;"><?= $line_name; ?></td>
					<td class="fontSizeCharacter verticalMiddle" style="width: 13%;"><span><?= $final_buyerName; ?></span></td>
					<td class="fontSizeCharacter verticalMiddle" style="width: 13%;"><span><?= $final_styleRefNo; ?></span></td>
					<td class="fontSizeCharacter verticalMiddle" style="width:13%;"><span><?= $final_grouping; ?></span>
					<td class="fontSizeNumber textRight verticalMiddle" style="width:6%; "><span></span><?= number_format($line_wise_smv_avg,2); ?></span></td>
					<td class="fontSizeNumber textRight verticalMiddle" style="width:5%;"><span><?= $row['man_power']; ?></span></td>
					<td class="fontSizeNumber textRight verticalMiddle" style="width:8%; "><span><?= $row['day_terget']; ?></span></td>
					<td class="fontSizeNumber textRight verticalMiddle" style="width: 7%;"> <span><? echo round($second_current_target); ?></span></td>											
					<td class="fontSizeNumber textRight verticalMiddle" style="width: 7%;"> <span><? echo $row['totalProdQnty']; ?></span></td>
					<td class="fontSizeNumber textRight verticalMiddle" style="width: 7%; <? echo $font_color; ?>"><span><?= $varriance; ?></span></td>					
					<td class="fontSizeNumber textRight verticalMiddle" style="width: 7%;"><span><?= round($second_trend); ?></span></td>
					<td class="fontSizeNumber textRight verticalMiddle" style="width: 7%;"><span><?= number_format($line_wise_efficiency,2); ?></span></td>
					<td class="fontSizeNumber textRight verticalMiddle" style="width: 7%;"><span><?= trim(number_format($line_wise_dhu,2),'-'); ?></span></td>
				</tr>
				<?					    					    		
		    }			
	    	?>
    	</table>		
    </div>
    <?
	exit();
}

?>