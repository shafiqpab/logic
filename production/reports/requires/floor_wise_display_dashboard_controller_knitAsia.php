<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	$cbo_floor=str_replace("'","",trim($cbo_floor));
	$cbo_company_name=str_replace("'","",trim($cbo_company_name));
	$cbo_location_name=str_replace("'","",trim($cbo_location_name));
	//$txt_date=str_replace("'","",trim($txt_date));
	$max_height=str_replace("'","",trim($max_height));
	//var_dump($cbo_location_name);
	if ($db_type==0) $txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    else $txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
    	
	// convert company name to id
    $cbo_company_id = return_field_value('id', 'lib_company',"company_name='$cbo_company_name'");
    $cbo_location_id  = return_field_value('id', 'lib_location',"location_name='$cbo_location_name' and company_id=$cbo_company_id and status_active=1 and is_deleted=0");
	
    $cbo_floor_id  = return_field_value('id', 'lib_prod_floor',"floor_name='$cbo_floor' and company_id=$cbo_company_id and location_id=$cbo_location_id and status_active=1 and is_deleted=0");
	//var_dump($cbo_location_id);
   // echo $cbo_floor_id.'system';
    $buyer_library    = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');
    $floor_library    = return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");

    // Check Variable Setting Use Prod Resource Allocation
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
    

	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	if($prod_reso_allo == 1)
	{
	    $dataArray = sql_select("SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_id and a.location_id=$cbo_location_id and b.pr_date='$txt_date'");
	  
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
			$line_ids = $row[csf('id')];
			$prod_reso_line_arr[$row[csf('id')]]=$row[csf('line_number')];
		}
	}	
	unset($dataArray);
	//echo "<pre>";print_r($prod_resource_array);die;

	$company_id_cond=$location_id_cond=$floor_id_cond=$txt_date_cond='';
	if ($cbo_company_id !='') $company_id_cond=" and a.serving_company=$cbo_company_id";
	if ($cbo_location_id !='') $location_id_cond=" and a.location=$cbo_location_id";
	if ($cbo_floor_id !='') $floor_id_cond=" and a.floor_id=$cbo_floor_id";
	if ($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";

	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($cbo_company_id) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($cbo_company_id) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	}

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
    //$prod_start_hour=$start_time_arr[1]['pst'];
	//if($prod_start_hour=='') 
	$prod_start_hour='09:00';
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

	
	if($db_type==0)
	{

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
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_id_cond $location_id_cond $floor_id_cond $txt_date_cond
			group by a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number
			order by a.sewing_line";
	}
	else
	{
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
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_id_cond $location_id_cond $floor_id_cond $txt_date_cond 
			group by a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number
			order by a.sewing_line"; 	
	}
	//echo $sql;die;
	
	$result = sql_select($sql);
	$item_count=0;
	$total_smv=0;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$totalAlterQnt = $totalSpotQnt = $totalRejectQnt = 0;
	$totalGoodAlrSpotRejectQnty=0;
	$day_terget=$current_target=0;
	$varriance=$trend=0;
	$planned=$countLineNumber=0;

	$production_data=array();
	foreach($result as $row)
	{
		$count_hour=0;	
		
		$poId .= $row[csf("po_id")].",";
		if($row[csf('prod_reso_allo')]==1)
		{
			$floor_name .= $floor_library[$row[csf('floor_id')]].',';
			$buyer_name .= $buyer_library[$row[csf('buyer_name')]].',';
			$style_ref_no .= $row[csf('style_ref_no')].',';
			$job_no .= $row[csf('job_no')].',';
			$po_number .= $row[csf('po_number')].',';

			$operator = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$man_power   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['man_power'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			if ($check_line_number_arr[$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			}			

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");

			if ($check_item_number_arr[$row[csf('sewing_line')]][$row[csf('smv_pcs_set')]] == '' )
			{
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
				$check_item_number_arr[$row[csf('sewing_line')]][$row[csf('smv_pcs_set')]]=$row[csf('smv_pcs_set')];
			}			

		    $production_data[$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';
		    $production_data[$line_name]["operator"]=$operator;
			$production_data[$line_name]["helper"]=$helper;
			$production_data[$line_name]["hourly_target"]=$hourly_target;
			$production_data[$line_name]["working_hour"]=$working_hour;
			$production_data[$line_name]["man_power"]=$man_power;
			$production_data[$line_name]["day_terget"]=$hourly_target*$working_hour;			

		    for($h=$hour; $h<$last_hour; $h++)
			{		
						
				if ($h == 14) continue;
				$count_hour++;

				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);				
				
				$production_data[$line_name]["production_date"]=$row[csf("production_date")];
				$production_data[$line_name]["current_target"]=$hourly_target*$count_hour;
				$production_data[$line_name]["target_efficiency"]=$target_efficiency;
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

				if ($h == 15 && date('G') == 14) break;
				if (date('G') == $h) break;
        		
			}
		}
		else
		{
			$floor_name .= $floor_library[$row[csf('floor_id')]].',';
			$buyer_name .= $buyer_library[$row[csf('buyer_name')]].',';
			$style_ref_no .= $row[csf('style_ref_no')].',';
			$job_no .= $row[csf('job_no')].',';
			$po_number .= $row[csf('po_number')].',';

			$operator = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$man_power   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['man_power'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			if ($check_line_number_arr[$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			}			

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");

			if ($check_item_number_arr[$row[csf('sewing_line')]][$row[csf('smv_pcs_set')]] == '' )
			{
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
				$check_item_number_arr[$row[csf('sewing_line')]][$row[csf('smv_pcs_set')]]=$row[csf('smv_pcs_set')];
			}			

		    $production_data[$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';
		    $production_data[$line_name]["operator"]=$operator;
			$production_data[$line_name]["helper"]=$helper;
			$production_data[$line_name]["hourly_target"]=$hourly_target;
			$production_data[$line_name]["working_hour"]=$working_hour;
			$production_data[$line_name]["man_power"]=$man_power;
			$production_data[$line_name]["day_terget"]=$hourly_target*$working_hour;			

		    for($h=$hour; $h<$last_hour; $h++)
			{		
						
				if ($h == 14) continue;
				$count_hour++;

				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);				
				
				$production_data[$line_name]["production_date"]=$row[csf("production_date")];
				$production_data[$line_name]["current_target"]=$hourly_target*$count_hour;
				$production_data[$line_name]["target_efficiency"]=$target_efficiency;
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

				if ($h == 15 && date('G') == 14) break;
				if (date('G') == $h) break;
        		
			}
		}	
	}

	//echo '<pre>';print_r($production_data);
	$total_smv=$total_smv/$item_count;
	foreach ($production_data as $key => $val) 
	{
		$day_terget+=$val['day_terget'];	
		$current_target+=$val['current_target'];	
		$operator+=$val['operator'];	
		$helper+=$val['helper'];
	}
	//echo $day_terget.'**'.$current_target.'**'.$operator.'**'.$helper;

	/*$day_terget=array_sum(array_column($production_data, 'day_terget'));	
    $current_target=array_sum(array_column($production_data, 'current_target'));
	$operator=array_sum(array_column($production_data, 'operator'));
	$helper=array_sum(array_column($production_data, 'helper'));*/
	$varriance=$current_target-$totalProdQnty;
	$trend=$totalProdQnty/$count_hour*10;

	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$dhu = ($totalProdQnty/$totalGoodAlrSpotRejectQnty)*100-100; //(qcQty/total_qty)*100-100;
	$efficiency=(($totalProdQnty*$total_smv)/(($operator+$helper)*$count_hour*60))*100;
	//efficiency=(prod_qty*smv)/((operators+helpers)*Number of hours*Minutes)
	$order_no = implode(',', array_flip(array_flip(explode(',', rtrim($po_number,',')))));
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));
	$floor_name = implode(',', array_flip(array_flip(explode(',', rtrim($floor_name,',')))));
	$buyer_name = implode(',', array_flip(array_flip(explode(',', rtrim($buyer_name,',')))));
	$style_ref_no = implode(',', array_flip(array_flip(explode(',', rtrim($style_ref_no,',')))));
	$job_no = implode(',', array_flip(array_flip(explode(',', rtrim($job_no,',')))));

	// ===========FOR SEWING DATA(TODAY,TOTAL)==================
	if($db_type==0)
	{
		$prod_qnty_data = "SELECT a.prod_reso_allo, a.sewing_line, 
		sum(case when a.production_type in(4) and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_input,
		sum(case when a.production_type in(5) and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_output, 
		sum(case when a.production_type in(4) and a.production_date='$txt_date' then a.production_quantity else 0 END) as today_sewing_input,
		sum(case when a.production_type in(4) and a.production_date<'$txt_date' then a.production_quantity else 0 END) as total_sewing_input_except_current_date,
		sum(case when a.production_type in(5) and a.production_date<'$txt_date' then a.production_quantity else 0 END) as total_sewing_output_except_current_date
		FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
		WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type in(4,5) and d.shiping_status!=3 $company_id_cond
		GROUP BY a.prod_reso_allo, a.sewing_line";
	}
	else
	{
		$prod_qnty_data = "SELECT a.prod_reso_allo, a.sewing_line, 
		sum(case when a.production_type in(4) and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_input,		
		sum(case when a.production_type in(5) and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_output, 
		sum(case when a.production_type in(4) and a.production_date='$txt_date' then a.production_quantity else 0 END) as today_sewing_input,
		sum(case when a.production_type in(4) and a.production_date<'$txt_date' then a.production_quantity else 0 END) as total_sewing_input_except_current_date,
		sum(case when a.production_type in(5) and a.production_date<'$txt_date' then a.production_quantity else 0 END) as total_sewing_output_except_current_date
		FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
		WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type in(4,5) and d.shiping_status!=3 $company_id_cond 
		GROUP BY a.prod_reso_allo, a.sewing_line";
	}
	//echo $sql_qcAlterSpotReject;die;
	$total_sewing_input=$total_sewing_output=$today_sewing_input=0;
	$total_sewing_input_except_current_date=$total_sewing_output_except_current_date=0;
	$prod_qnty_data_res = sql_select($prod_qnty_data);
	foreach($prod_qnty_data_res as $row)
	{	
		if($row[csf('prod_reso_allo')]==1)
		{			
			$total_sewing_input  = $row[csf('total_sewing_input')];
			$total_sewing_output = $row[csf('total_sewing_output')];
			$today_sewing_input  = $row[csf('today_sewing_input')];		

			$total_sewing_input_except_current_date  = $row[csf('total_sewing_input_except_current_date')];
			$total_sewing_output_except_current_date = $row[csf('total_sewing_output_except_current_date')];
		}
		else
		{
			$total_sewing_input  = $row[csf('total_sewing_input')];
			$total_sewing_output = $row[csf('total_sewing_output')];
			$today_sewing_input  = $row[csf('today_sewing_input')];

			$total_sewing_input_except_current_date  = $row[csf('total_sewing_input_except_current_date')];
			$total_sewing_output_except_current_date = $row[csf('total_sewing_output_except_current_date')];
		}
	}

	$wip_except_cur_date = $total_sewing_input_except_current_date-$total_sewing_output_except_current_date;
    $wip = $wip_except_cur_date+$today_sewing_input;


    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='knit_order_entry' and master_tble_id='".$result[0][csf('job_no')]."'","image_location");
    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id=$cbo_company_id and form_name='company_details' and is_deleted=0 and file_type=1");

   
	?>

	<style type="text/css">
		#secondtime table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
		.rpt_table td {
		    border: 1px solid #000;
		}
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="firsttime">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height: 8%;">
    			<td style="width: 5%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<?
		        	foreach ($data_array as $img_row) 
		        	{
		        		?>
		        		<p><img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='40' width='40' align="middle"/></p>
		        		<?	
		        	}	
		        	?>
    			</td>
    			<td style="width: 22%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">Floor KPI Dashboard</td>
    			<td style="width: 30%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= $cbo_company_name; ?></td>
    			<td style="width: 23%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?
					$cb_floor = ($cbo_floor) ? ' ('.$cbo_floor.')' :'';
					echo $cbo_location_name.' '.$cb_floor;
				 ?>
				</td>
    			<td style="width: 8%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= $count_hour.'Hr'; ?></td>
    			<td style="width: 12%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 5%;">
    			<td colspan="2" style="color: white; font-weight:bold; text-align: left; vertical-align: middle;">
    				<p style="font-size: 2vw;">PRODUCTION</p>
    			</td>
    			<td rowspan="2" style="color: white; font-size: 2vw; font-weight:bold;">
    				<p style="font-size: 2vw; height: 35%; text-align: left;">Day Target</p>    				
    				<p style="font-size: 6vw; height: 65%; text-align: right; font-weight:900;"><?= $day_terget; ?>&nbsp;</p>
    			</td>
    			<td rowspan="3" colspan="3" style="color: white; font-size: 2vw; font-weight:bold; ">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Efficiency</p>
    				<p style="height: 70%; width: 100%; background-color: #262626;">   					
    					<span style="width: 90%; height: 100%; font-size: 6vw; font-weight:900; text-align: center; background-color: #262626; float: left; display: block;"><?= number_format($efficiency,2); ?>%</span>    					
    					<!-- <span style="width:10%; height: 100%; text-align: right; background-color: #f00; float: left; display: block;">xyz</span> -->
    				</p> 					
    				<!-- <p style="font-size: 2vw; height: 10%; text-align: right; vertical-align: middle;">Actual</p> -->    					
    				<p style="font-size: 2vw; height: 10%; text-align: center; vertical-align: middle;"><span>Planned</span>&nbsp;<span style="font-weight:900;"><?= number_format($planned/$countLineNumber,2); ?>%</span></p>    					
    			</td>
			</tr>

			<tr style="height: 25%; width: 100%;">
    			<td colspan="2" style="color: white; font-weight:bold;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;  width: 100%;">Target</p>
    				<p style="font-size:6vw; height: 80%; font-weight:900; text-align: right;"><span style="width: 80%; text-align: right;"><?=  $current_target; ?></span><span style="width: 20%; text-align: right;">&nbsp;&nbsp;</span></p>
    			</td>    			
			</tr>
			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Actual</p>
    				<?
	        		if ($current_target<=$totalProdQnty)
	        		{
	        			?>
    					<p style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  $totalProdQnty; ?></span><span style="width: 20%; text-align: right; color: green; font-weight:900;">&#8679;</span></p>
    					<?
    				}
    				else
    				{
    					?>
    					<p style="font-size:6vw; height: 80%; font-weight:900; text-align: right; "><span style="width: 80%; text-align: right;"><?=  $totalProdQnty; ?></span><span style="width: 20%; text-align: right; color: red; font-weight:900;">&#8681;</span></p>
    					<?
    				}
    				?>	
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 30%; text-align: left;">Trend</p>
    				<p style="font-size:6vw; height: 70%; text-align: right; font-weight:900;"><?= round($trend); ?>&nbsp;</p>
    			</td>
			</tr>
			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">    				
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Variance</p>
    				<?
	        		if ($varriance < 0)
	        		{
	        			?>
	        			<p style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; color: green; font-weight:bold;">&#8679;</span></p>        			
	        			<?
	        		}
	        		else
	        		{
	        			?>
	        			<p style="font-size:6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; color: red; font-weight:bold;">&#8681;</span></p>
	        			<?
	        		}
	        		?>    				
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 30%; text-align: left;">DHU%</p>
    				<p style="font-size:6vw; height: 70%; text-align: right; color: red; font-weight:900;"><?= trim(number_format($dhu,2),'-'); ?>%&nbsp;</p>
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; ">
    				<p style="height: 20%;">&nbsp;</p>
    				<p style="font-size:6vw; height: 60%; text-align: center; font-weight:900;"><?= $operator; ?></p>
    				<p style="font-size: 2vw; height: 40%; text-align: center; ">Operator</p>
    			</td>
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; ">
    				<p style="height: 20%;">&nbsp;</p>
    				<p style="font-size:6vw; height: 60%; text-align: center; font-weight:900;"><?= $helper; ?></p>
    				<p style="font-size: 2vw; height: 40%; text-align: center; ">Helper</p>
    			</td>
			</tr>
			<tr style="height: 8%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; text-align: center; "><span>Number of Line:</span>&nbsp;<span style="font-weight:900;"><?= $countLineNumber; ?></span></p>
    			</td>
    			<td colspan="4" style="color: white; font-size: 2.5vw; font-weight:bold; text-align: center; vertical-align: middle; background-color: #FF0000;">
    				<p><span>Alter&nbsp;-&nbsp;</span><span style="font-weight: 900;"><?= $totalAlterQnty; ?>;</span><span>&nbsp;&nbsp;Spot&nbsp;-&nbsp;</span><span style="font-weight: 900;"><?= $totalSpotQnty; ?>;</span>&nbsp;&nbsp;Reject&nbsp;-&nbsp;<span style="font-weight: 900;"><?= $totalRejectQnty; ?>;</span><span>&nbsp;&nbsp;Total&nbsp;-&nbsp;</span><span style="font-weight: 900;"><?= ($totalAlterQnty+$totalSpotQnty+$totalRejectQnty); ?>;</span></p>
    			</td>
			</tr>
		</table>        
    </div>

    <div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="secondtime">
    	<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
    		<tr style="height: 8%;">
    			<td style="width: 5%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<?
		        	foreach ($data_array as $img_row) 
		        	{
		        		?>
		        		<p><img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='40' width='40' align="middle"/></p>
		        		<?	
		        	}	
		        	?>
    			</td>
    			<td colspan="2" style="width: 25%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">Floor KPI Dashboard</td>
    			<td colspan="3" style="width: 29%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;"><?= $cbo_company_name; ?></td>
    			<td colspan="3" style="width: 17%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
				<?
					$cb_floor = ($cbo_floor) ? ' ('.$cbo_floor.')' :'';
					echo $cbo_location_name.' '.$cb_floor; 
				?>
				</td>
    			<td colspan="2" style="width: 10%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= $count_hour.'Hr'; ?></td>
    			<td colspan="2" style="width: 14%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 7%;">
    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Line</td>
    			<td style="width: 13%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Buyer</td>
    			<td style="width: 12%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Style</td>

    			<td style="width: 17%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">PO</td>
    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">SMV</td>
    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">MP</td>

    			<td style="width: 7%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">D.Target</td>
    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Target</td>
    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Actual</td>

    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Variance</td>
    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Trend</td>
    			<td style="width: 8%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">Eff%</td>
    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;">DHU%</td>
			</tr>
			<?
			$total_variance=$total_man_power=0;
			$total_day_terget=$total_current_target=0;
			$total_actual=$total_trend=0;
			$line_wise_dhu=$totalGoodAlterSpotRejectQnty=0;
			foreach ($production_data as $line_name => $row) 
			{
				$buyerName=implode(',', array_flip(array_flip(explode(',', rtrim($row['buyer_name'],',')))));
				if (strlen($buyerName) > 12) $final_buyerName=substr($buyerName, 0, 12).'..';
				else $final_buyerName=$buyerName;
				$styleRefNo=implode(',', array_flip(array_flip(explode(',', rtrim($row['style_ref_no'],',')))));
				if (strlen($styleRefNo) > 12) $final_styleRefNo=substr($styleRefNo, 0, 12).'..';
				else $final_styleRefNo=$styleRefNo;
				$poNumber=implode(',', array_flip(array_flip(explode(',', rtrim($row['po_number'],',')))));
				if (strlen($poNumber) > 15) $final_poNumber=substr($poNumber, 0, 15).'..';
				else $final_poNumber=$poNumber;
				
				$line_wise_total_smv=$row['smv_sum']/$row['smv_avg'];
				$totalGoodAlterSpotRejectQnty = $row['totalProdQnty']+$row['totalAlterQnty']+$row['totalSpotQnty']+$row['totalRejectQnty'];
				$line_wise_dhu = ($row['totalProdQnty']/$totalGoodAlterSpotRejectQnty)*100-100; //(qcQty/total_qty)*100-100;
				$line_wise_efficiency=(($row['totalProdQnty']*$line_wise_total_smv)/(($row['man_power'])*$count_hour*60))*100;
				?>
				<tr style="height: 7%; background-color: #595959;">
	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: left; vertical-align: middle;"><?= $line_name; ?></td>
	    			<td style="width: 13%; color: white; font-size: 1.5vw; font-weight:bold; text-align: left; vertical-align: middle;"><?= $final_buyerName; ?></td>
	    			<td style="width: 12%; color: white; font-size: 1.5vw; font-weight:bold; text-align: left; vertical-align: middle;"><?= $final_styleRefNo; ?></td>
	    			<td style="width: 17%; color: white; font-size: 1.5vw; font-weight:bold; text-align: left; vertical-align: middle;"><?= $final_poNumber; ?></td>
	    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><?= number_format($line_wise_total_smv,2); ?></td>
	    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><?= $row['man_power']; ?></td>

	    			<td style="width: 7%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><?= $row['day_terget']; ?></td>
	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><?= $row['current_target']; ?></td>	    			

	    			<?
	        		if ( $row['current_target'] <= $row['totalProdQnty'] )
	        		{
	        			?>
    					<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><p><span style="width: 80%; text-align: right;"><?= $row['totalProdQnty']; ?></span><span style="width: 20%; text-align: right; color: green; font-weight:900;">&#8679;</span></p></td>
    					<?
    				}
    				else
    				{
    					?>
    					<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><p><span style="width: 80%; text-align: right;"><?= $row['totalProdQnty']; ?></span><span style="width: 20%; text-align: right; color: red; font-weight:900;">&#8681;</span></p></td>
    					<?
    				}
    				?>

    				<?
	        		if ( $row['current_target'] <= $row['totalProdQnty'] )
	        		{
	        			?>
    					<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><p><span style="width: 80%; text-align: right;"><?= ($row['totalProdQnty']-$row['current_target']); ?></span><span style="width: 20%; text-align: right; color: green; font-weight:900;">&#8679;</span></p></td>
    					<?
    					$total_variance+=$row['totalProdQnty']-$row['current_target'];
    				}
    				else
    				{
    					?>
    					<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><p><span style="width: 80%; text-align: right;"><?= ($row['current_target']-$row['totalProdQnty']); ?></span><span style="width: 20%; text-align: right; color: red; font-weight:900;">&#8681;</span></p></td>
    					<?
    					$total_variance+=$row['current_target']-$row['totalProdQnty'];
    				}
    				?>

	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><?= round($row['totalProdQnty']/$count_hour*10); ?></td>

	    			<?
	        		if ( $row['target_efficiency'] <= $line_wise_efficiency )
	        		{
	        			?>
    					<td style="width: 8%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><p><span style="width: 80%; text-align: right;"><?= number_format($line_wise_efficiency,2); ?>%</span><span style="width: 20%; text-align: right; color: green; font-weight:900;">&#8679;</span></p></td>
    					<?
    				}
    				else
    				{
    					?>
    					<td style="width: 8%; color: white; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><p><span style="width: 80%; text-align: right;"><?= number_format($line_wise_efficiency,2); ?>%</span><span style="width: 20%; text-align: right; color: red; font-weight:900;">&#8681;</span></p></td>
    					<?
    				}
    				?>
	    			<td style="width: 6%; color: red; font-size: 1.5vw; font-weight:bold; text-align: right; vertical-align: middle;"><?= trim(number_format($line_wise_dhu,2),'-'); ?>%</td>
				</tr>
				<?
				$total_man_power+=$row['man_power'];
				$total_day_terget+=$row['day_terget'];
				$total_current_target+=$row['current_target'];
				$total_actual+=$row['totalProdQnty'];			
				$total_trend+=($row['totalProdQnty']/$count_hour*10);
				
			}
			?>
			<? 
			$blank_row=10-count($production_data);
			for ($i=1; $i<=$blank_row; $i++) 
			{
				?>		
				<tr style="height: 7%;">
	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 13%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 12%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>

	    			<td style="width: 17%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>

	    			<td style="width: 7%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>

	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 8%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
	    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center; vertical-align: middle;"></td>
				</tr>
				<?
			}
			?>
			<tr style="height: 7%;">
    			<td colspan="5" style="width: 6%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;">Total&nbsp;</td>
    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"><?= $total_man_power; ?></td>

    			<td style="width: 7%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"><?= $total_day_terget; ?></td>
    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"><?= $total_current_target; ?></td>
    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"><?= $total_actual; ?></td>

    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"><?= $total_variance; ?></td>
    			<td style="width: 5%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"><?= round($total_trend); ?></td>
    			<td style="width: 8%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"></td>
    			<td style="width: 6%; color: white; font-size: 1.5vw; font-weight:900; text-align: right; vertical-align: middle;"></td>
			</tr>

    	</table>
    </div>

    <?
	exit();
	

}

?>