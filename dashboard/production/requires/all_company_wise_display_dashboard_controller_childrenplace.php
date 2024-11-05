<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	//$cbo_floor=str_replace("'","",trim($cbo_floor));
	//$cbo_company_name=str_replace("'","",trim($cbo_company_name));
	//$txt_date=str_replace("'","",trim($txt_date));
	$max_height=str_replace("'","",trim($max_height));

	if ($db_type==0) $txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    else $txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
    	
	// convert company name to id
    //$cbo_company_id = return_field_value('id', 'lib_company',"company_name='$cbo_company_name'");
    //$cbo_floor_id     = return_field_value('id', 'lib_prod_floor',"floor_name='$cbo_floor' and company_id=$cbo_company_id and status_active=1 and is_deleted=0");
    $buyer_library    = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');
    $floor_library    = return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");

    // Check Variable Setting Use Prod Resource Allocation
    //$prod_reso_allo=return_field_value('auto_update','variable_settings_production',"variable_list=23 and is_deleted=0 and status_active=1");
    $prod_reso_allo=1;  // default set
	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	if($prod_reso_allo == 1)
	{
	    $dataArray = sql_select("SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and b.pr_date='$txt_date'");
	  
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

	//$company_id_cond=$floor_id_cond='';
	$txt_date_cond='';
	//if ($cbo_company_id !='') $company_id_cond=" and a.serving_company=$cbo_company_id";
	//if ($cbo_floor_id !='') $floor_id_cond=" and a.floor_id=$cbo_floor_id";
	if ($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";

	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in(1) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in(1) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
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
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $txt_date_cond
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
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $txt_date_cond 
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
	$day_terget=array_sum(array_column($production_data, 'day_terget'));	
    $current_target=array_sum(array_column($production_data, 'current_target'));
	$operator=array_sum(array_column($production_data, 'operator'));
	$helper=array_sum(array_column($production_data, 'helper'));
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

    //$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='knit_order_entry' and master_tble_id='".$result[0][csf('job_no')]."'","image_location");
    //$data_array = sql_select("select image_location  from common_photo_library where master_tble_id=$cbo_company_id and form_name='company_details' and is_deleted=0 and file_type=1");

   
	?>

	<style type="text/css">
		.rpt_table td {
		    border: 2px solid #000;
		}
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; ">
		<table style="width: 100%; height: 100%; background-color: #262626; overflow: hidden; clear: both; margin-left: 0; float: left;" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
			<tr style="height: 8%;">
    			<td style="width: 5%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
		        	<p><img src='../../../images/group_logo.png' height='40' width='40' align="middle"/></p>
    			</td>
    			<td style="width: 25%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">KPI Dashboard</td>
    			<td style="width: 30%; color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">Children Place Ltd.</td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= $count_hour.'Hr'; ?></td>
    			<td style="width: 20%; color: white; font-size: 2vw; font-weight:900; text-align: center; vertical-align: middle;"><?= date('h:i A', time()); ?></td>
			</tr>

			<tr style="height: 5%;">
    			<td colspan="2" style="color: white; font-weight:bold; text-align: left; vertical-align: middle;">
    				<p style="font-size: 2vw;">PRODUCTION</p>
    			</td>
    			<td rowspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 35%; text-align: left;">Day Target</p>
    				<p style="font-size: 6vw; height: 65%; text-align: right; font-weight:900;"><?= $day_terget; ?>&nbsp;</p>
    			</td>
    			<td rowspan="3" colspan="2" style="color: white; font-size: 2vw; font-weight:bold;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Efficiency</p>
    				<p style="height: 70%; width: 100%; background-color: #262626;">   					
    					<canvas id="canvas" style="width:100%; max-width:100%"></canvas>
    					<input type="hidden" id="efficiency" value="<?= round($efficiency); ?>">
    				</p> 					
    				<!-- <p style="font-size: 2vw; height: 10%; text-align: right; vertical-align: middle;">Actual</p>  -->   					
    				<p style="font-size: 2vw; height: 10%; text-align: center; vertical-align: middle;"><span>Planned</span>&nbsp;<span style="font-weight:900;"><?= number_format($planned/$countLineNumber,2); ?>%</span></p>    					
    			</td>
			</tr>

			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-weight:bold; font-weight:bold;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Target</p>
    				<p style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><?= $current_target; ?>&nbsp;</p>
    			</td>    			
			</tr>
			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 20%; text-align: left;">Actual</p>
    				<?
	        		if ($current_target<=$totalProdQnty)
	        		{
	        			?>
    					<p style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: right;"><?= $totalProdQnty; ?></span>&nbsp;<span style="width: 20%; height: 80%; text-align: right; color: green;">&#8679;</span></p>
    					<?
    				}
    				else
    				{
    					?>
    					<p style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; height: 80%; text-align: right;"><?= $totalProdQnty; ?></span><span style="width: 20%; height: 80%; text-align: right; color: red;">&#8681;</span></p>
    					<?
    				}
    				?>	
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 30%; text-align: left;">Trend</p>
    				<p style="font-size: 6vw; height: 70%; text-align: right; font-weight:900;"><?= round($trend); ?>&nbsp;</p>
    			</td>
			</tr>
			<tr style="height: 25%;">
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: right; vertical-align: middle;">    				
    				<p style="font-size: 2vw; height: 20%; text-align: left; font-weight:bold;">Variance</p>
    				<?
	        		if ($varriance < 0)
	        		{
	        			?>
	        			<p style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= trim(round($varriance),'-'); ?></span><span style="width: 20%; text-align: right; color: green; font-weight:bold;">&#8679;</span></p>        			
	        			<?
	        		}
	        		else
	        		{
	        			?>
	        			<p style="font-size: 6vw; height: 80%; text-align: right; font-weight:900;"><span style="width: 80%; text-align: right;"><?= round($varriance); ?></span><span style="width: 20%; text-align: right; color: red; font-weight:bold;">&#8681;</span></p>
	        			<?
	        		}
	        		?>    				
    			</td>
    			<td style="color: white; font-size: 2vw; font-weight:bold; text-align: right; vertical-align: middle;">
    				<p style="font-size: 2vw; height: 30%; text-align: left;">DHU%</p>
    				<p style="font-size: 6vw; height: 70%; text-align: right; color: red; font-weight:900;"><?= trim(number_format($dhu,2),'-'); ?>%&nbsp;</p>
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
    			<td colspan="2" style="color: white; font-size: 2vw; font-weight:bold; text-align: center; vertical-align: middle;">
    				<p style="font-size: 2vw; text-align: center; vertical-align: middle;"><span>Number of Line:</span>&nbsp;<span style="font-weight:900;"><?= $countLineNumber; ?></span></p>
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