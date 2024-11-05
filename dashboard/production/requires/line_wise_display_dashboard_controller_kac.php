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
	$txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
	
    $buyer_library    = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');
    $floor_library    = return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
	
	// convert company name to id
    if ($cbo_company_name !="") {
        $cbo_company_id = return_field_value('id', 'lib_company',"company_name='$cbo_company_name'");
    }

    // convert location name to id
    if ($cbo_company_id != "") {
        $cbo_location_id=return_field_value("id", "lib_location", "location_name='$cbo_location_name' and company_id=$cbo_company_id and is_deleted=0", "id");
    }
    
    // convert floor name to id
    if ($cbo_company_id != "" && $cbo_location_id != "") {
        $cbo_floor_id=return_field_value("a.id as id", "lib_prod_floor a", "a.floor_name='$cbo_floor_name' and a.company_id=$cbo_company_id and a.location_id=$cbo_location_id and a.is_deleted=0", "id");
    }

	// convert line name to id
	$line_ids="";
    if ($cbo_company_id != "" && $cbo_location_id != "" && $cbo_floor_id != "")
	{
		$ex_line = explode(',', $cbo_line_name);        
		foreach ($ex_line as $value)
		{
			$line_id = return_field_value('id','lib_sewing_line',"company_name=$cbo_company_id and location_name=$cbo_location_id and floor_name=$cbo_floor_id and line_name='$value' and is_deleted=0");
			$line_ids .= $line_id.',';
		}
		$line_ids = chop($line_ids,',');
	}

    if ($cbo_company_id =="" || $cbo_location_id =="" || $cbo_floor_id =="" || $line_ids =="") {
        echo '<div style="width: 70%; height: 50px; color: red; font-weight: bold; font-size: 20px; text-align: center; background-color: #444; padding: 10px; border-radius: 15px;">Please Correct Your URL...</div>';die;
    }


    // Check Variable Setting Use Prod Resource Allocation
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");    

	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	$lineIds="";
	if($prod_reso_allo == 1)
	{
		$sql_acresource="SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_id and a.location_id=$cbo_location_id and a.floor_id=$cbo_floor_id and a.line_number in('$line_ids') and b.pr_date='$txt_date'";
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
	$lineIds = implode(',', array_flip(array_flip(explode(',', rtrim($lineIds,',')))));


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
		$rectified_hour="rectified_hour".substr($bg_hour,0,2);
		if($first==1)
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN d.production_qnty else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.replace_qty else 0 END) AS $replace_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.rectified_qty else 0 END) AS $rectified_hour,";
		}
		else
		{
			$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN d.production_qnty else 0 END) AS $prod_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.replace_qty else 0 END) AS $replace_hour,
				sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.rectified_qty else 0 END) AS $rectified_hour,";
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
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.replace_qty else 0 END) AS $replace_hour,
		sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.rectified_qty else 0 END) AS $rectified_hour";
														
	$sql.=" FROM pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c
		where a.production_type=5 and a.id=d.mst_id and a.po_break_down_id=c.id and b.id=c.job_id and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.serving_company=$cbo_company_id and a.location=$cbo_location_id and a.floor_id=$cbo_floor_id and a.sewing_line in($lineIds) and a.production_date='$txt_date'
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
	if(trim($order_ids)!="")
	{
	$sql_item="SELECT a.style_ref_no, b.id as color_size_id, b.po_break_down_id, b.order_quantity, c.gmts_item_id, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and b.is_deleted=0 and b.po_break_down_id in($order_ids)";
	//echo $sql_item;die;
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
	}

	$item_smv=$total_smv=$total_order_qty=0;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$totalAlterQnty = $totalSpotQnty = $totalRejectQnty = $totalReplaceQnty = $totalRectifiedQnty = 0;
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

		    for($h=$hour; $h<=$last_hour; $h++)
			{
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				$replace_hour="replace_hour".substr($bg_hour,0,2);
				$rectified_hour="rectified_hour".substr($bg_hour,0,2);
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
				$production_data[$line_name]["$rectified_hour"]+=$row[csf("$rectified_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];
				$totalReplaceQnty +=$row[csf($replace_hour)];
				$totalRectifiedQnty +=$row[csf($rectified_hour)];

				//$total_smv+=$item_smv*$row[csf($prod_hour)];

				if (date('G') == $h) break;  // Up to  Current hour calculation	
				$count_hour++;
			}
		}
		else
		{
			$operator = $prod_resource_array[$row[csf('sewing_line')]]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]]['helper'];
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

		    for($h=$hour; $h<$last_hour; $h++)
			{						
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				$replace_hour="replace_hour".substr($bg_hour,0,2);
				$rectified_hour="rectified_hour".substr($bg_hour,0,2);
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
				$production_data[$line_name]["$rectified_hour"]+=$row[csf("$rectified_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];
				$totalReplaceQnty +=$row[csf($replace_hour)];
				$totalRectifiedQnty +=$row[csf($rectified_hour)];

				//$total_smv+=$item_smv*$row[csf($prod_hour)];
				
				if (date('G') == $h) break;  // Up to  Current hour calculation
				$count_hour++;
			}
		}	
	}
	//echo $count_hour;//die;
	//echo '<pre>';print_r($production_data);die;
	
	//$total_smv_min=$total_order_qnty=0;
	//$smv_average=($total_smv/$total_order_qty)

	$current_minute=date('i', time());
	$current_hour=$current_minute/60;

	// Hour calculation without lunch our and count hour is not greter than working hour
	if (date('G')==13) $current_hour=0;
	else if (date('G')>13) $count_hour=$count_hour-1;

	$total_count_hour=$count_hour+$current_hour;
	if ($total_count_hour>$working_hour) $total_count_hour=$working_hour;
	
	
	$ws_gmts_item="";
	if($style_ref_nos!="")
	{
		$ws_gmts_itemsql = sql_select("select a.id,a.style_ref,a.gmts_item_id,upper(b.item_name) as item_name,(a.applicable_period) applicable_period from ppl_gsd_entry_mst a,lib_garment_item b where a.gmts_item_id=b.id and a.style_ref in(".$style_ref_nos.") and a.gmts_item_id in($item_number_ids) and a.bulletin_type=4 and a.status_active=1 and a.is_deleted=0 and a.applicable_period<='$txt_date' group by a.id,a.style_ref,a.gmts_item_id,a.applicable_period,b.item_name order by a.applicable_period desc");
		foreach ($ws_gmts_itemsql as $row)
		{
			$ws_gmts_item= $row[csf('item_name')];
			$wsItemArr[$row[csf('id')]]=$row[csf('id')];
		}
	}
	
	$job_gmts_item="";
	if($job_no!="")
	{
		$job_gmts_itemsql = sql_select("select min(t.id) as id,min(t.job_no) as jon_no,t.gmts_item_id,t.item_name from (select a.id,a.job_no,a.gmts_item_id,upper(b.item_name) as item_name from wo_po_details_mas_set_details a,lib_garment_item b where a.gmts_item_id=b.id and a.job_no in(".$job_no.")	group by a.id,a.job_no,a.gmts_item_id,b.item_name) t group by t.gmts_item_id,t.item_name");
		
		/*echo "select min(t.id) as id,min(t.job_no) as jon_no,t.gmts_item_id,t.item_name from (select a.id,a.job_no,a.gmts_item_id,upper(b.item_name) as item_name from wo_po_details_mas_set_details a,lib_garment_item b where a.gmts_item_id=b.id and a.job_no in(".$job_no.")	group by a.id,a.job_no,a.gmts_item_id,b.item_name) t group by t.gmts_item_id,t.item_name ";die;*/
		
		foreach ($job_gmts_itemsql as $row)
		{
			$job_gmts_itemsql= $row[csf('item_name')];
			//$wsItemArr[$row[csf('id')]]=$row[csf('id')];
		}
	}

	if ($order_ids != "")
	{
		//$sql_defect = "SELECT TO_CHAR(a.production_hour,'HH24') as production_hour, b.defect_qty as defect_qty FROM pro_garments_production_mst a, pro_gmts_prod_dft b WHERE a.id=b.mst_id and b.production_type=5 and b.po_break_down_id in($order_ids) $company_cond $floor_cond $location_cond $line $txt_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		$sql_defect = "SELECT TO_CHAR(a.production_hour,'HH24') as production_hour, b.defect_qty as defect_qty 
		FROM pro_garments_production_mst a, pro_gmts_prod_dft b,pro_garments_production_dtls c
		WHERE a.id=b.mst_id and b.dtls_id=c.id and b.production_type=5  and b.po_break_down_id in($order_ids)  and a.serving_company=$cbo_company_id and a.location=$cbo_location_id and a.floor_id=$cbo_floor_id and a.sewing_line in($lineIds) and a.production_date='$txt_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		and c.status_active=1";// and c.production_qnty=0 and c.reject_qty=0
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
		WHERE a.id=b.mst_id and b.dtls_id=c.id and b.production_type=5 and b.po_break_down_id in($order_ids)  and a.serving_company=$cbo_company_id and a.location=$cbo_location_id and a.floor_id=$cbo_floor_id and a.sewing_line in($lineIds) and a.production_date='$txt_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and c.status_active=1
		group by b.defect_type_id,b.defect_point_id
		order by point_count desc) where rownum<=3";// and TRUNC(b.insert_date)='6/June/2023' // and c.production_qnty=0 and c.reject_qty=0
		// echo $sql_defect_count;die;
		$sql_defectcnt_res=sql_select($sql_defect_count);
		$totDefectPoint=0;
		$DefectCount_arr=array();
		foreach ($sql_defectcnt_res as $row) {
			$defect_type_id = $row["DEFECT_TYPE_ID"];
			$defect_point_id = $row["DEFECT_POINT_ID"];
			$totDefectCount+= $row["POINT_COUNT"];
		}
	}
	
	$smv_average=($total_smv/$total_order_qty)*1;
	$day_terget=$hourly_target*$working_hour;
	$current_target=$hourly_target*$total_count_hour;
	$varriance=$current_target-$totalProdQnty;
	$trend=($totalProdQnty/$total_count_hour)*$working_hour;
	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$totalAlrSpotQnty=$totalAlterQnty+$totalSpotQnty;
	$totalAlrSpotRejectQnty=$totalAlterQnty+$totalSpotQnty;//+$totalRejectQnty
	$dhu_title = "(Total Defect Count / (Good + Alter + SPOT + Reject)) X 100 \n\n".$totDefectQnty . "/(" . $totalGoodAlrSpotRejectQnty .")" . "*100"; 
	//$dhu = ($totalAlrSpotRejectQnty/($totalProdQnty+$totalReplaceQnty))*100; //(qcQty/total_qty)*100-100;
	//$dhu = ($totDefectQnty/($totalProdQnty+$totalReplaceQnty))*100; //(qcQty/total_qty)*100-100;
	$dhu = ($totDefectQnty/($totalGoodAlrSpotRejectQnty))*100; //(qcQty/total_qty)*100-100;
	$rft = (($totalGoodAlrSpotRejectQnty-$totalAlrSpotQnty)/$totalGoodAlrSpotRejectQnty)*100;
	$efficiency=(($totalProdQnty*$smv_average)/(($operator+$helper)*$total_count_hour*60))*100;   
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

.Gmtimg {
  display: block;
  max-height:170px;
  padding: 8px;
  margin-left: auto;
  margin-right: auto;
  position: relative;
  text-align: left;
  
}
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="firsttime">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height:6%;background-color:#333;"> 
	            <td style="width:6%;vertical-align: middle;" align="center">
	            
	          	<span><img class="flip" src="../../images/logic/mfg_logo.png" id="logininfo" width="80"></span>  
	           
	            </td>   			
				<td style="width:17%;color:white;font-size:1.8vw;font-weight:bold;text-align:center;vertical-align:middle;"><u>BUYER</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $buyer_name; ?></marquee></td>
	            
	            <td style="width:11%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center;vertical-align:middle;"><u>IR NO</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $grouping; ?></marquee></td>
	            
				<td style="width:16%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center;vertical-align:middle;"><u>STYLE</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? if (strlen($style_ref_no)>100) echo substr($style_ref_no,0,10).'..'; else echo $style_ref_no; ?></marquee></td>
	            
				<td style="width:16%;color:white;font-size:1.8vw; font-weight:bold; text-align:center;vertical-align:middle;"><u>FLOOR</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $cbo_floor_name; ?></marquee></td>
				<td style="width:17%;color:#0FF;font-size:1.8vw;font-weight:bold;text-align:center;vertical-align:middle;"><u>LINE</u><marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $cbo_line_name; ?></marquee></td>
	               
	    		<td style="width:9%;color:white; font-size:1.7vw;font-weight:bold;text-align:center;vertical-align:middle;">
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
		    			<tr style="height:25%">	
							<td colspan="3" style="border:1;vertical-align:middle;">
		                   <div class="marquee">
		        			<div class="marqueeText">
								<p>
								<canvas id="canvas" style="width:95%;"></canvas>
								<input type="hidden" id="efficiency" value="<?= fn_number_format(round($efficiency),0,'','','0'); ?>">
		                     	</p>
		                       	<p> 
		                        <!--<img src="../../images/logic/Polo_shirt.png" width="180px" height="85px"> --> 
		                        <?
		                        /*foreach($jobNOArr as $jobNo)
								{
								$image_path = ($imge_arr[$jobNo]!="")? $imge_arr[$jobNo]:"images/logic/image_not_found4.png";//$ws_gmts_image
								$image_height = ($imge_arr[$jobNo]!="")? "200":"250";
								?>                             
		                        <img  src='../../<? echo $image_path; ?>' width="<? echo $image_height; ?>"/>
		                        <?
								}*/



								$image_path = $image_location_arr[$ws_image_id]? $image_location_arr[$ws_image_id]:"images/logic/image_not_found4.png";//$ws_gmts_image
								$image_height = ($image_location_arr[$ws_image_id]!="")? "200":"200";
								
								$image_location="";
								if(trim($job_no)!="") { 
								$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='knit_order_entry' and master_tble_id in(".$job_no.")","image_location"); }
								?>
		                        <img class="Gmtimg" src="../../<? echo $image_location;?>" >  
		                        </p>
		                        </div>
		                        </div>
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
		                    <div class="marquee">
		        			<div class="marqueeText">
		                    <p>
		                    <span>Planned:</span>&nbsp;<span style="font-weight:900;"><?= fn_number_format(round($planned/$countLineNumber),0,'.','','0'); ?>%</span>&nbsp;&nbsp;
		                    <span>Varriance:</span>&nbsp;<span style="font-weight:900;"><?= fn_number_format(round($planned-$efficiency),0,'.','','0'); ?>%</span> 
		                    </p>
		                   <!-- <p style="font-size:1.8vw; vertical-align:middle"><? //echo $ws_gmts_item;?></p>-->
		                    <p style="font-size:1.8vw; vertical-align:middle"><? echo $job_gmts_itemsql;?></p>
		                    </div>                         
		                    </div>
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
														// echo "<pre>";
														// print_r($sew_fin_alter_defect_type);
														// echo "</pre>";
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
    					<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  ($totalProdQnty+$totalReplaceQnty); ?></span><span style="width: 20%; text-align: right; color: green; font-weight:900;"></span></div>
    					<?
    				}
    				else
    				{
    					?>
							<div style="font-size: 4vw; text-align: right; float:right; color:red;">&#9660;</div>
						</div>
    					<div style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  ($totalProdQnty+$totalReplaceQnty); ?></span><span style="width: 20%; text-align: right;  font-weight:900;"></span></div>
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
    				<div title="<? echo $dhu_title; ?>" style="font-size:4.5vw; height: 80%; text-align: right;padding-top:5%;color: red; font-weight:900;"><?= fn_number_format($dhu,2,'.','','0.00');?>&nbsp;</div>
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
      			<span>ALTER</span>
                <span class="rounded-bg" style="font-weight:bold;"title="Alter Qty: <? echo $totalAlterQnty;?>"><?=fn_number_format(($totalAlterQnty/$totalProdQnty)*100,2,'.','','0.00'); ?>% <span style="border-left:2px solid; padding-left:5px;"> <? echo $totalAlterQnty;?></span></span>
				<span></span>
      			<span>SPOT</span>
                <span class="rounded-bg" style="font-weight: bold;"title="Spot Qty: <? echo $totalSpotQnty;?>"><?=fn_number_format(($totalSpotQnty/$totalProdQnty)*100,2,'.','','0.00');?>% <span style="border-left:2px solid; padding-left:5px;"> <? echo $totalSpotQnty;?></span></span>
      			<span>REJECT</span>
                <span class="rounded-bg" style="font-weight: bold;"title="Reject Qty: <? echo $totalRejectQnty;?>"><?=fn_number_format(($totalRejectQnty/$totalProdQnty)*100,2,'.','','0.00');?>% <span style="border-left:2px solid; padding-left:5px;"> <? echo $totalRejectQnty;?></span></span>
      
      				<?
					 $rectified_title = "(".$totalRectifiedQnty."/".$totalAlrSpotQnty.")*";
					 $rectifiedPer=($totalRectifiedQnty/$totalAlrSpotQnty)*100;
						if ($rectifiedPer ==100)
						{			    				
							?>
								<span>RECTIFIED</span>
                                <span class="rounded-bg" style="font-weight: bold;"title="Rectify Qty: <? echo $rectifiedPer;?>"><?=fn_number_format(($totalRectifiedQnty/$totalAlrSpotQnty)*100,0,'.','','0');?>% <span style="border-left:2px solid; padding-left:5px;"> <? echo $totalRectifiedQnty;?></span></span>
                    		<? 
                         }
                         else
						{
							?>
								<span>RECTIFIED</span>
                                <span class="rounded-bg" style="font-weight: bold;"title="Rectify Qty: <? echo $rectified_title;?>"><?=fn_number_format(($totalRectifiedQnty/$totalAlrSpotQnty)*100,2,'.','','0.00');?>% <span style="border-left:2px solid; padding-left:5px;"> <? echo $totalRectifiedQnty;?></span></span>
							<?
						}
						?>
                    </div>
    			</td>
			</tr>
		</table>        
    </div>
	<!-- ============================== 2nd part start ================================== -->

	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none; background-color: #262626;" id="secondtime">
    	<table style="width: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" border="0" rules="all" cellpadding="0" cellspacing="0">
    		<tr style="height: 10%;">    			
    			<td colspan="3" style="width: 22%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center; vertical-align: middle;"><u>BUYER</u><br> <marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $buyer_name; ?></marquee></td>
    			<td colspan="2"style="width: 18%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center; vertical-align: middle;"><u>STYLE</u><br> <marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? if (strlen($style_ref_no)>100) echo substr($style_ref_no,0,10).'..'; 
				else echo $style_ref_no; ?></marquee></td>
    			<td colspan="2" style="width: 18%; color: white; font-size: 1.8vw; font-weight:bold; text-align: center; vertical-align: middle;"><u>FLOOR</u><br> <marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $cbo_floor_name; ?></marquee></td>
				<td colspan="2" style="width: 14%; color: yellow; font-size: 1.8vw; font-weight:bold; text-align: center; vertical-align: middle;"><u>LINE</u><br> <marquee width=85% behavior="scroll" scrolldelay="200" direction="left" onmouseover="this.stop();" onmouseleave="this.start();"class="margin-top-8" ><? echo $cbo_line_name; ?></marquee></td>
    			<td colspan="2" style="width: 14%; color: white; font-size: 1.6vw; font-weight:bold; text-align: center; vertical-align: middle;">Time Past:<br>
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
    			<td colspan="2" style="width: 14%; color: white; font-size: 1.6vw; font-weight:bold; text-align: center; vertical-align: middle;"><? echo date('d-M-y').'<br/>'.date('h:i A', time()); ?></td>
			</tr>

			<tr style="height:10%; background-color:#003366">
				<td rowspan="2" style="width: 4%;  color: white; font-size: 1.4vw; font-weight:bold; text-align: center; vertical-align: middle;">Hour</td>
				<td colspan="2" style="width: 18%; color:white; font-size:1.4vw;font-weight:bold;text-align:center;vertical-align:middle;">Hourly (Pcs)</td>
				<td colspan="2" style="width: 18%; color: white; font-size: 1.4vw; font-weight:bold; text-align: center; vertical-align: middle;">Cumulative (Pcs)</td>
				<td colspan="2" style="width: 18%; color: white; font-size: 1.4vw; font-weight:bold; text-align: center; vertical-align: middle;">Variance (Pcs)</td>
				<td colspan="2" style="width: 14%; color: white; font-size: 1.4vw; font-weight:bold; text-align: center; vertical-align: middle;">DHU %</td>
				<td colspan="2" style="width: 14%; color: white; font-size: 1.4vw; font-weight:bold; text-align: center; vertical-align: middle;">Actual Efficiency %</td>
				<td colspan="2" style="width: 14%; color: white; font-size: 1.4vw; font-weight:bold; text-align: center; vertical-align: middle;">Eff. Variance %</td>   			
			</tr>
            
            <tr style="background-color:#003366"><!--#003366-->
            <td style="width: 9%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Actual</td>
            <td style="width: 9%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Plan</td>
            <td style="width: 9%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Actual</td>
            <td style="width: 9%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Plan</td>
            <td style="width: 9%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Hourly</td>
            <td style="width: 9%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Cum.</td>
            <td style="width: 7%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Hourly</td>
            <td style="width: 7%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Cum.</td>
            <td style="width: 7%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Hourly</td>
            <td style="width: 7%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Cum.</td>
            <td style="width: 7%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Hourly</td>
            <td style="width: 7%;color: white; font-size: 1.3vw; font-weight:normal; text-align: center; vertical-align: middle;">Cum.</td>
            </tr>
            
			<?
			$hour_count=1;
			$varriancePcsHourly=$tot_good_qty=0;
			$varriancePcsCum=$tot_hourly_target=0;
			$totalAlterSpotRejectQty=0;
			$cumulity_hourlyDhu=$cumulity_actual_efficiency=0; 
			$cumulity_efficiency_variance=0; 		
			foreach($production_data as $line => $row)
			{
				for($h=$hour; $h<=$last_hour; $h++)
				{
					if($h != 13)
					{
					
						if ($hour_count>$working_hour) { break; }
						
						$hourlyTotalAlterSpotRejectQty=0;
						$hourlyDhu=0;
						//$hourly_target=$target_efficiency=0;
						//$operator=$helper=0;
						$prod_hour   ='prod_hour'.substr($start_hour_arr[$h],0,2);
						$alter_hour  ='alter_hour'.substr($start_hour_arr[$h],0,2);
						$spot_hour   ='spot_hour'.substr($start_hour_arr[$h],0,2);
						$reject_hour ='reject_hour'.substr($start_hour_arr[$h],0,2);
						$replace_hour='replace_hour'.substr($start_hour_arr[$h],0,2);
						$defect_hour ='defect_hour'.substr($start_hour_arr[$h],0,2);
						$hourlyTotalAlterSpotRejectQty =$row[$alter_hour]+$row[$spot_hour]+$row[$reject_hour];
						$totalAlterSpotRejectQty       +=$hourlyTotalAlterSpotRejectQty;
						$hourly_target     = $row['hourly_target'];
						$target_efficiency = $row['target_efficiency'];
						$operator          = $row['operator'];
						$helper            = $row['helper'];
						$varriancePcsHourly=$hourly_target - $row[$prod_hour];
						$tot_hourly_target += $hourly_target;
						$tot_good_qty      += $row[$prod_hour]+$row[$replace_hour];
						$tot_defect_qty    +=$hour_wise_totDefectQnty_arr[$h];
						//$totalProdQnty += $row[csf($prod_hour)];
						//$actual_efficiency = ($row[$prod_hour]*$total_smv)/(($operator+$helper)*60);					
						//$hourlyDhu = ($hour_wise_totDefectQnty_arr[$h]/$row[$prod_hour])*100; //(qcQty/total_qty)*100-100;
						$hourlyDhu = ($totalAlterSpotRejectQty/$row[$prod_hour])*100; 
						$cumulity_hourlyDhu = ($tot_defect_qty/$tot_good_qty)*100;
						//echo $hour_wise_totDefectQnty_arr[$h].'**'.$row[$prod_hour].'**'.$tot_good_qty.'###';
						//$cumulity_hourlyDhu += ($hour_wise_totDefectQnty_arr[$h]/$tot_good_qty)*100;					
						//$actual_efficiency=(($row[$prod_hour]*$total_smv)/(($operator+$helper)*60))*100;
						//echo $current_minute.'mint'.$hour_count.'hrs' ;
						
						if (date('G') == $h) 
						{
							$actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*$current_minute))*100;
							$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*(60*($hour_count-1)+$current_minute)))*100;
						}
						else 
						{
							$actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*60))*100;
							$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*60*$hour_count))*100;
							//$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*$current_minute))*100;
						}
						?>
						<tr id="tr_<?= $h; ?>"style="height: 7%;" >
							<td style="width: 4%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= $hour_count; ?></td>
							<?
							if ($row[$prod_hour] < $hourly_target)
							{			    				
								?>
								<td style="width: 8%; color: white; font-size: 2vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= number_format(($row[$prod_hour]+$row[$replace_hour]),0); ?></span></td>                            
							<? 
							}
							else
							{
								?>
								<td style="width: 8%; color: white; font-size: 2vw; font-weight:bold; text-align:right; padding-right:20px; vertical-align: middle;"><span style="color:#0bc300;"><?= number_format(($row[$prod_hour]+$row[$replace_hour]),0); ?></span></td>
								<?
							}                        
							?>
							<td style="width: 8%; color: white; font-size: 2vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:white;"><?=$hourly_target; ?></span></td>
							<?
							if ($tot_good_qty < $tot_hourly_target)
							{
								?>
								<td style="width:8%; color: white; font-size: 2vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span></td>
								<?
							}
							else
							{
								?>
								<td style="width:8%; color: white; font-size: 2vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:#0bc300;"><?= $tot_good_qty; ?></span>
								<?
							}
							?>
							<td style="width:8%; color: white; font-size: 2vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: white;"><?=$tot_hourly_target; ?></span></td>
							<?
							if ( $varriancePcsHourly < 0)
							{
								$varriancePcsCum+=$varriancePcsHourly;
								?>
								<td style="width:8%; color: white; font-size: 2vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?= trim($varriancePcsHourly,'-'); ?></span>
								<?
							}
							else
							{
								$varriancePcsCum+=$varriancePcsHourly;
								?>
								<td style="width:8%; color: white; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;">				<?= trim($varriancePcsHourly,'-'); ?></span>
								<?
							}	
							if ( $varriancePcsCum < 0)
							{
								//$varriancePcsCum+=$varriancePcsHourly;
								?>
								<td style="width:8%; color: white; font-size: 2vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?=trim($varriancePcsCum,'-'); ?></span></td>
								<?
							}
							else
							{
								//$varriancePcsCum+=$varriancePcsHourly;
								?>
								<td style="width:8%; color: white; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span span style="color: red;"> <?=trim($varriancePcsCum,'-'); ?></span></td>
								<?
							}	
							?>

							<td style="width: 6%; color: red; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"> <span><? echo fn_number_format($hourlyDhu,2,'.','','0.00');?></span></td>
													
							<td style="width: 6%; color: red; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"> <span><? echo fn_number_format($cumulity_hourlyDhu,2,'.','','0.00'); ?></span></td>


							<td style="width: 6%; color: white; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span><?= fn_number_format(round(trim($actual_efficiency,'-')),0,'.','','0');?></span></td>
							
							<td style="width: 6%; color: white; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"> <span><?=fn_number_format(round(trim($cumulity_actual_efficiency,'-')),0,'.','','0'); ?></span></td>
							<?
														
							$efficiency_variance=$target_efficiency-$actual_efficiency;
							$cumulity_efficiency_variance=$target_efficiency-$cumulity_actual_efficiency;
							if ($efficiency_variance > 0)
							{
								?>
								<td style="width: 6%; color: red; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span><? echo fn_number_format(round(trim($efficiency_variance,'-')),0,'.','','0');?></span></td>
								<?
							}
							else
							{
								?>
								<td style="width: 6%; color:#0bc300; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span><? echo fn_number_format(round(trim($efficiency_variance,'-')),0,'.','','0');?></span></td>
								
								<?
							}
							
							if ($cumulity_efficiency_variance > 0)
							{
								?>
								<td style="width: 6%; color: red; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span><? echo fn_number_format(round(trim(  $cumulity_efficiency_variance,'-')),0,'.','','0'); ?></span></td>
								</td>
								<?
							}
							else
							{
								?>
								<td style="width: 6%; color:#0bc300; font-size: 2vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span><? echo fn_number_format(round(trim ($cumulity_efficiency_variance,'-')),0,'.','','0');?></span></td>
								<?
							}
							?>	
						</tr>
						<?
						if (date('G') == $h) { break; }
						$hour_count++;	
					}
					else
					{
						if ($hour_count>$working_hour) { break; }
						
						$hourlyTotalAlterSpotRejectQty=0;
						$hourlyDhu=0;
						//$hourly_target=$target_efficiency=0;
						//$operator=$helper=0;
						$prod_hour   ='prod_hour'.substr($start_hour_arr[$h],0,2);
						$alter_hour  ='alter_hour'.substr($start_hour_arr[$h],0,2);
						$spot_hour   ='spot_hour'.substr($start_hour_arr[$h],0,2);
						$reject_hour ='reject_hour'.substr($start_hour_arr[$h],0,2);
						$replace_hour='replace_hour'.substr($start_hour_arr[$h],0,2);
						$defect_hour ='defect_hour'.substr($start_hour_arr[$h],0,2);
						$hourlyTotalAlterSpotRejectQty =$row[$alter_hour]+$row[$spot_hour]+$row[$reject_hour];
						$totalAlterSpotRejectQty       +=$hourlyTotalAlterSpotRejectQty;
						$hourly_target     = $row['hourly_target'];
						$target_efficiency = $row['target_efficiency'];
						$operator          = $row['operator'];
						$helper            = $row['helper'];
						$varriancePcsHourly=$hourly_target - $row[$prod_hour];
						$tot_hourly_target += $hourly_target;
						$tot_good_qty      += $row[$prod_hour]+$row[$replace_hour];
						$tot_defect_qty    +=$hour_wise_totDefectQnty_arr[$h];
						//$totalProdQnty += $row[csf($prod_hour)];
						//$actual_efficiency = ($row[$prod_hour]*$total_smv)/(($operator+$helper)*60);					
						//$hourlyDhu = ($hour_wise_totDefectQnty_arr[$h]/$row[$prod_hour])*100; //(qcQty/total_qty)*100-100;
						$hourlyDhu = ($totalAlterSpotRejectQty/$row[$prod_hour])*100; 
						$cumulity_hourlyDhu = ($tot_defect_qty/$tot_good_qty)*100;
						//echo $hour_wise_totDefectQnty_arr[$h].'**'.$row[$prod_hour].'**'.$tot_good_qty.'###';
						//$cumulity_hourlyDhu += ($hour_wise_totDefectQnty_arr[$h]/$tot_good_qty)*100;					
						//$actual_efficiency=(($row[$prod_hour]*$total_smv)/(($operator+$helper)*60))*100;
						//echo $current_minute.'mint'.$hour_count.'hrs' ;
						
						if (date('G') == $h) 
						{
							$actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*$current_minute))*100;
							$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*(60*($hour_count-1)+$current_minute)))*100;
						}
						else 
						{
							$actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*60))*100;
							$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*60*$hour_count))*100;
							//$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*$current_minute))*100;
						}
						?>
						<tr id="tr_<?= $h; ?>"style="height: 7%;" >
							<td colspan="13"><div style="color:#F9F9F9;font-weight:bold;text-align:center;font-size:20px;">Lunch Hour</div></td>
						</tr>
						<?
						if (date('G') == $h) { break; }
						// $hour_count++;	
					}				    					    		
		    	}
		    }			
	    	?>
    	</table>		
    </div>
    
 	<!-- third slide start -->
 
   	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none; background-color: #262626;" id="thirdttime">
    	<table style="width: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" border="0" rules="all" cellpadding="0" cellspacing="0">
        	<tr style="height:20%;">
            <tr>
            	<td colspan="10" style=" font-size:22px;color:#FFFFFF;background-color:#000">&nbsp;</td>
            </tr>  
            	<td style="width:3%;vertical-align: middle;" align="center">
          				<span><img src="../../images/logic/mfg_logo.png" id="logininfo" width="65"></span>  
            	</td>
            	<td colspan="8" style="width:12%;color:white;font-size:3.2vw;font-weight:bold;text-align:center;vertical-align:middle;background-color:#FF0000">GARMENTS PRODUCTION DISPLAY BOARD</td>
                <td style="width:3%;vertical-align: middle;" align="center">
               			<span><img class="flip" src="../../images/logic/logic_logo_new.png" id="logininfo" width="125"></span>
             	</td>
            </tr>
            <tr>
            	<td colspan="10" style=" font-size:24px;color:#FFFFFF;background-color:#000">&nbsp;</td>
            </tr>
    		<tr style="height: 10%;"> 
    		
    			<td colspan="2" class="" style="color:white;font-size:2.1vw;font-weight:bold;text-align:left;padding-left:10px;vertical-align:middle;background-color:#060">FLOOR</td>
                
				<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: left;padding-left:10px; vertical-align: middle;"> <? echo $cbo_floor_name; ?></td>
                
                
                <td colspan="2" style="color: white; font-size: 2.1vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle; background-color: #060">DAY'S EFF.</td>
                
                <td style="width: 10%; color: white; font-size: 2.4vw; font-weight:bold; text-align: center; vertical-align: middle;"> <?= fn_number_format(round($planned/$countLineNumber),0,'.','','0');?>%</td>
                            
                <td colspan="2" style="color: white; font-size: 2.1vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle; background-color: #060">DATE</td>
                
				<td style="width:10%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><? echo date('d-M-y');?></td>
                
            </tr>
            <tr style="height: 10%;">   
				<td colspan="2" style="color: white; font-size: 2.1vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle; background-color: #060">LINE NO</td>
                
				<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle;"><? echo $cbo_line_name; ?></td>
                
                
                <td colspan="2" style="color: white; font-size: 2.1vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle; background-color: #060">DAY'S TARGET</td>
                
				<td style="width: 10%; color: white; font-size: 2.4vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= $day_terget; ?></td>
                
                <td colspan="2" style="color: white; font-size: 2.1vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle; background-color: #060">TIME</td>
                
				<td style="width: 10%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= date('h:i A', time());?></td>
			</tr>
            <tr>
            	<td colspan="10" style=" font-size:24px;color:#FFFFFF;background-color:#000">&nbsp;</td>
            </tr>       

			<tr style="height:10%; background-color:#F00">
				<td style="width:12%;  color: white; font-size: 2.5vw; font-weight:bold; text-align:center; vertical-align: middle;">KPI</td>
                <td style="width:5%;color:white;font-size:2.5vw;font-weight:bold;text-align:left;vertical-align:middle; background-color:#262626;">&nbsp;</td>
				<td colspan="2" style="width:16%; color:white; font-size:2.5vw;font-weight:bold;text-align:center;vertical-align:middle;">TARGET</td>
                <td style="width:5%;color:white;font-size:2.5vw;font-weight:bold;text-align:left;vertical-align:middle; background-color:#262626;">&nbsp;</td>
				<td colspan="2" style="width:14%; color: white; font-size: 2.5vw; font-weight:bold; text-align: center; vertical-align: middle;">ACHIEVEMENT</td>
                <td style="width:5%;color:white;font-size:2.5vw;font-weight:bold;text-align:left;vertical-align:middle; background-color:#262626;">&nbsp;</td>
				<td colspan="2" style="width:14%; color: white; font-size: 2.5vw; font-weight:bold; text-align: center; vertical-align: middle;">TARGET SHORT</td>
			</tr>
            
            <tr style="background-color:#666"><!--#003366-->
            <td style="width:7%; background-color:#262626;"> </td>
            <td style="width:5%; color: white;font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td>
            <td style="width: 10%;color: white; font-size: 2.2vw; font-weight:bold; text-align: center; vertical-align: middle;">HOURLY</td>
            <td style="width: 10%;color: white; font-size: 2.2vw; font-weight:bold; text-align: center; vertical-align: middle;">CUM.</td>
            <td style="width:5%; color: white;  font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td>
            <td style="width: 12%;color: white; font-size: 2.2vw; font-weight:bold; text-align: center; vertical-align: middle;">HOURLY</td>
            <td style="width: 12%;color: white; font-size: 2.2vw; font-weight:bold; text-align: center; vertical-align: middle;">CUM.</td>
            <td style="width:5%; color: white; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td>
            <td style="width: 12%;color: white; font-size: 2.2vw; font-weight:bold; text-align: center; vertical-align: middle;">HOURLY</td>
            <td style="width: 12%;color: white; font-size: 2.2vw; font-weight:bold; text-align: center; vertical-align: middle;">CUM.</td>
            </tr>
            
			<?
			$hour_count=1;
			$varriancePcsHourly=0;
			$varriancePcsCum=$tot_hourly_target=0;
			$tot_good_qty=0;
			$totalAlterSpotRejectQty=0;
			$cumulity_hourlyDhu=$cumulity_actual_efficiency=0; 
			$cumulity_efficiency_variance=0; 		
			foreach($production_data as $line => $row)
			{
				/*for($h=$hour; $h<=$last_hour; $h++)*/
				{
					if ( date('G') == 13 && $h >= 13) { break; }
					if ($h == 13) { continue; }
					
					//if ($h>=13 && $h<14){
					/* if ($h>13){
						$hour_count=$hour_count;
					} */
					
					if ($hour_count>$working_hour) { break; }
					
					$hourlyTotalAlterSpotRejectQty=0;
					$hourlyDhu=0;
					//$hourly_target=$target_efficiency=0;
					//$operator=$helper=0;
					$prod_hour='prod_hour'.substr($start_hour_arr[$h],0,2);
					$alter_hour='alter_hour'.substr($start_hour_arr[$h],0,2);
					$spot_hour='spot_hour'.substr($start_hour_arr[$h],0,2);
					$reject_hour='reject_hour'.substr($start_hour_arr[$h],0,2);
					$defect_hour='defect_hour'.substr($start_hour_arr[$h],0,2);
					$hourlyTotalAlterSpotRejectQty=$row[$alter_hour]+$row[$spot_hour]+$row[$reject_hour];
					$totalAlterSpotRejectQty+=$hourlyTotalAlterSpotRejectQty;
					$hourly_target = $row['hourly_target'];
					$target_efficiency = $row['target_efficiency'];
					$operator = $row['operator'];
					$helper   = $row['helper'];
					$varriancePcsHourly=$hourly_target - $row[$prod_hour];
					$tot_hourly_target += $hourly_target;
					$tot_good_qty += $row[$prod_hour];
					$tot_defect_qty +=$hour_wise_totDefectQnty_arr[$h];
					//$totalProdQnty += $row[csf($prod_hour)];
					//$actual_efficiency = ($row[$prod_hour]*$total_smv)/(($operator+$helper)*60);					
					$hourlyDhu = ($hour_wise_totDefectQnty_arr[$h]/$row[$prod_hour])*100; //(qcQty/total_qty)*100-100;
					//echo $hour_wise_totDefectQnty_arr[$h].'**'.$row[$prod_hour].'**'.$tot_good_qty.'###';
					//$cumulity_hourlyDhu += ($hour_wise_totDefectQnty_arr[$h]/$tot_good_qty)*100;
					$cumulity_hourlyDhu = ($tot_defect_qty/$tot_good_qty)*100;
					//$actual_efficiency=(($row[$prod_hour]*$total_smv)/(($operator+$helper)*60))*100;
					//echo $current_minute.'mint'.$hour_count.'hrs' ;
					
					if (date('G') == $h) {
						$actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*$current_minute))*100;
						$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*(60*($hour_count-1)+$current_minute)))*100;
					}
					else {
						$actual_efficiency=(($row[$prod_hour]*$smv_average)/(($operator+$helper)*60))*100;
						$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*60*$hour_count))*100;
						//$cumulity_actual_efficiency=(($tot_good_qty*$smv_average)/(($operator+$helper)*$current_minute))*100;
					}
					?>
					<tr style="height:12%;" >
						<td style="width:13%; color: white; font-size:3.5vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle;">EFF %</td>
                        <td style="width:5%; color: white;font-weight:bold; text-align:left; vertical-align: middle;">&nbsp;</td>
                        
						
							<td style="width: 8%; color: white; font-size: 4vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= fn_number_format(round($planned/$countLineNumber),0,'.','','0'); ?></span></td>
                            <td style="width: 8%; color: white; font-size: 4vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= fn_number_format(round($planned/$countLineNumber),0,'.','','0'); ?></span></td>
                        
							
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 
                            
						<?
						if ($tot_good_qty < $tot_hourly_target)
						{
							?>
						<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span></td>
							<?
						}
						else
						{
							?>
						<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:#0bc300;"><?= $tot_good_qty; ?></span>
							<?
						}
						?>
                        
							<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: white;"><?=$tot_hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 

						<?
						if ( $varriancePcsHourly < 0)
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}
						else
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size: 3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;">				<?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}	
						?>	
                        
                        <?
						if ( $varriancePcsCum < 0)
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}
						else
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size: 3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span span style="color: red;"> <?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}	
						?>
					</tr>
                    
      		<tr style="height: 7%;" >
						<td style="width:13%; color: white; font-size:3.5vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle;">PROD</td>
                        <td style="width:5%; color: white;font-weight:bold; text-align:left; vertical-align: middle;">&nbsp;</td>
						<?
						if ($row[$prod_hour] < $hourly_target)
						{			    				
							?>
							<td style="width: 8%; color: white; font-size: 4vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= number_format($row[$prod_hour],0); ?></span></td>
                            
                           <? 
                         }
                         else
						{
							?>
							<td style="width: 8%; color: white; font-size: 4vw; font-weight:bold; text-align:right; padding-right:20px; vertical-align: middle;"><span style="color:#0bc300;"><?=number_format($row[$prod_hour],0); ?></span></td>
							<?
						}
                        
						?>
							<td style="width: 8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:white;"><?=$hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 
                            
						<?
						if ($tot_good_qty < $tot_hourly_target)
						{
							?>
						<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span></td>
							<?
						}
						else
						{
							?>
						<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:#0bc300;"><?= $tot_good_qty; ?></span>
							<?
						}
						?>
							<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: white;"><?=$tot_hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 

						<?
						if ( $varriancePcsHourly < 0)
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}
						else
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size: 3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;">				<?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}	
						?>	
                        
                        <?
						if ( $varriancePcsCum < 0)
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size: 3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}
						else
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size: 3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span span style="color: red;"> <?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}	
						?>
					</tr>
                    
                    
                 <tr style="height: 7%;" >
						<td style="width:13%; color: white; font-size: 3.5vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle;">ALTER</td>
                        <td style="width:5%; color: white; font-weight:bold; text-align:left; vertical-align: middle;">&nbsp;</td>
						<?
						if ($row[$prod_hour] < $hourly_target)
						{			    				
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= number_format($row[$prod_hour],0); ?></span></td>
                            
                           <? 
                         }
                         else
						{
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold; text-align:right; padding-right:20px; vertical-align: middle;"><span style="color:#0bc300;"><?=number_format($row[$prod_hour],0); ?></span></td>
							<?
						}
                        
						?>
							<td style="width: 8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:white;"><?=$hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 
						<?
						if ($tot_good_qty < $tot_hourly_target)
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span></td>
							<?
						}
						else
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:#0bc300;"><?= $tot_good_qty; ?></span>
							<?
						}
						?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: white;"><?=$tot_hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 

						<?
						if ( $varriancePcsHourly < 0)
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}
						else
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;">				<?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}	
						?>	
                        
                        <?
						if ( $varriancePcsCum < 0)
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}
						else
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span span style="color: red;"> <?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}	
						?>
					</tr>
                    
                    <tr style="height: 7%;" >
						<td style="width:13%; color: white; font-size:3.5vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle;">SPOT</td>
                        <td style="width:5%; color: white;font-weight:bold; text-align:left; vertical-align: middle;">&nbsp;</td>
						<?
						if ($row[$prod_hour] < $hourly_target)
						{			    				
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= number_format($row[$prod_hour],0); ?></span></td>
                            
                           <? 
                         }
                         else
						{
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold; text-align:right; padding-right:20px; vertical-align: middle;"><span style="color:#0bc300;"><?=number_format($row[$prod_hour],0); ?></span></td>
							<?
						}
                        
						?>
							<td style="width: 8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:white;"><?=$hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 
                            
						<?
						if ($tot_good_qty < $tot_hourly_target)
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span></td>
							<?
						}
						else
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:#0bc300;"><?= $tot_good_qty; ?></span>
							<?
						}
						?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: white;"><?=$tot_hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 

						<?
						if ( $varriancePcsHourly < 0)
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}
						else
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;">				<?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}	
						?>	
                        
                        <?
						if ( $varriancePcsCum < 0)
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}
						else
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span span style="color: red;"> <?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}	
						?>
					</tr> 
                    
                    <tr style="height: 7%;" >
						<td style="width:13%; color: white; font-size:3.5vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle;">REJECT</td>
                        <td style="width:5%; color: white;font-weight:bold; text-align:left; vertical-align: middle;">&nbsp;</td>
						<?
						if ($row[$prod_hour] < $hourly_target)
						{			    				
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= number_format($row[$prod_hour],0); ?></span></td>
                            
                           <? 
                         }
                         else
						{
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold; text-align:right; padding-right:20px; vertical-align: middle;"><span style="color:#0bc300;"><?=number_format($row[$prod_hour],0); ?></span></td>
							<?
						}
                        
						?>
							<td style="width: 8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:white;"><?=$hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 
                            
						<?
						if ($tot_good_qty < $tot_hourly_target)
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span></td>
							<?
						}
						else
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:#0bc300;"><?= $tot_good_qty; ?></span>
							<?
						}
						?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: white;"><?=$tot_hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 

						<?
						if ( $varriancePcsHourly < 0)
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}
						else
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;">				<?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}	
						?>	
                        
                        <?
						if ( $varriancePcsCum < 0)
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}
						else
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span span style="color: red;"> <?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}	
						?>
					</tr>
                    
                    <tr style="height: 7%;" >
						<td style="width:13%; color: white; font-size:3.5vw; font-weight:bold; text-align:left;padding-left:10px; vertical-align: middle;">DHU %</td>
                        <td style="width:5%; color: white;font-weight:bold; text-align:left; vertical-align: middle;">&nbsp;</td>
						<?
						if ($row[$prod_hour] < $hourly_target)
						{			    				
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold;text-align:right;padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= number_format($row[$prod_hour],0); ?></span></td>
                            
                           <? 
                         }
                         else
						{
							?>
							<td style="width: 8%; color: white; font-size:4vw; font-weight:bold; text-align:right; padding-right:20px; vertical-align: middle;"><span style="color:#0bc300;"><?=number_format($row[$prod_hour],0); ?></span></td>
							<?
						}
                        
						?>
							<td style="width: 8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:white;"><?=$hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 
                            
						<?
						if ($tot_good_qty < $tot_hourly_target)
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;"><?= $tot_good_qty; ?></span></td>
							<?
						}
						else
						{
							?>
						<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color:#0bc300;"><?= $tot_good_qty; ?></span>
							<?
						}
						?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: white;"><?=$tot_hourly_target; ?></span></td>
                            
                            <td style="width:5%; color: white; font-size: 2.5vw; font-weight:bold; text-align:left; vertical-align: middle;background-color:#262626;">&nbsp;</td> 
                            

						<?
						if ( $varriancePcsHourly < 0)
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}
						else
						{
							$varriancePcsCum+=$varriancePcsHourly;
							?>
							<td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: red;">				<?= trim($varriancePcsHourly,'-'); ?></span>
							<?
						}	
						?>	
                        
                        <?
						if ( $varriancePcsCum < 0)
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold; text-align:right; padding-right:20px;vertical-align: middle;"><span style="color: #0bc300;"><?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}
						else
						{
							//$varriancePcsCum+=$varriancePcsHourly;
							?>
                            <td style="width:8%; color: white; font-size:3vw; font-weight:bold;text-align:right; padding-right:20px;vertical-align: middle;"><span span style="color: red;"> <?=trim($varriancePcsCum,'-'); ?></span></td>
							<?
						}	
						?>
					</tr>  
					<?
					//if (date('G') == $h) { break; }
					//$hour_count++;
					//if ( date('G') == 13 && $h >= 12){ break;}
					    					    		
		    	}
		    }			
	    	?>
    	</table>		
    </div> 
    <!-- third slide end -->
    <?
	exit();
}

?>