<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	//var_dump($process);die;
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",trim($cbo_company_name));
	$cbo_location_name=str_replace("'","",trim($cbo_location_name));
	$cbo_floor=str_replace("'","",trim($cbo_floor));
	$cbo_line=str_replace("'","",trim($cbo_line));
	//$txt_date=str_replace("'","",trim($txt_date));
	$max_height=str_replace("'","",trim($max_height));
	//var_dump($cbo_location_name);

	if($db_type==0)
    {
    	$txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    }
    else
    {
    	$txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
    }
	
	// convert company name to id
    $cbo_company_name = return_field_value('id', 'lib_company',"company_name like'%$cbo_company_name%'");
	$cbo_location_id  = return_field_value('id', 'lib_location',"location_name='$cbo_location_name' and company_id=$cbo_company_name and status_active=1 and is_deleted=0");
    $cbo_floor_id  = return_field_value('id', 'lib_prod_floor',"floor_name='$cbo_floor' and company_id=$cbo_company_name and location_id=$cbo_location_id and status_active=1 and is_deleted=0");
    $floor_library    = return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
    $location_library = return_library_array( "select id, location_name from lib_location", "id", "location_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
    $buyer_library    = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');
	//var_dump($cbo_floor_id);
   // echo $cbo_floor_id.'system';
    // convert Line name to id 
    $ex_line = explode(',', $cbo_line);
    foreach ($ex_line as $value)
    {
    	$line_id = return_field_value('id','lib_sewing_line',"company_name=$cbo_company_name and location_name=$cbo_location_id and floor_name=$cbo_floor_id and line_name='$value' and status_active=1 and is_deleted=0");
    	$line_ids .= $line_id.',';
    }
    $line_ids = chop($line_ids,',');
    //echo $line_ids;

    // Check Variable Setting Use Prod Resource Allocation
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
    

	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	if($prod_reso_allo == 1)
	{
	    $dataArray = sql_select("SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_name and a.location_id=$cbo_location_id and a.floor_id=$cbo_floor_id and a.line_number='$line_ids' and b.pr_date='$txt_date'");
	  
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

	$company_name=$location_id_cond=$floor_id_cond=$line=$txt_date_cond='';
	if($cbo_company_name !='') $company_name=" and a.serving_company=$cbo_company_name";
	if ($cbo_location_id !='') $location_id_cond=" and a.location=$cbo_location_id";
	if ($cbo_floor_id !='') $floor_id_cond=" and a.floor_id=$cbo_floor_id";
	if($cbo_line !='') $line=" and a.sewing_line in($line_ids)";
	if($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";

	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($cbo_company_name) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($cbo_company_name) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
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

		$sql="SELECT a.prod_reso_allo, a.production_date, a.location,a.floor_id, a.item_number_id, a.po_break_down_id as po_id, a.sewing_line, sum(a.production_quantity) as good_qnty, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down as smv_pcs_set, c.po_number,";
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
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location_id_cond $floor_id_cond $line $txt_date_cond
			group by a.prod_reso_allo, a.production_date, a.location,a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number";
	}
	else
	{
		$sql="SELECT a.prod_reso_allo, a.production_date, a.location,a.floor_id, a.item_number_id, a.po_break_down_id as po_id, a.sewing_line, sum(a.production_quantity) as good_qnty, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down as smv_pcs_set, c.po_number,";
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
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location_id_cond $floor_id_cond $line $txt_date_cond
			group by a.prod_reso_allo, a.production_date,a.location, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number"; 	
	}
	//die();
	//echo $sql;die;
	
	$result = sql_select($sql);
	$item_count=0;
	$total_smv=0;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$totalAlterQnt = $totalSpotQnt = $totalRejectQnt = 0;
	$totalGoodAlrSpotRejectQnty=0;
	$day_terget=$current_target=0;
	$varriance=$trend=0;

	$production_data=array();
	foreach($result as $row)
	{
		$count_hour=0;
		$poId .= $row[csf("po_id")].",";
		if($row[csf('prod_reso_allo')]==1)
		{
			$location_name .= $location_library[$row[csf('location')]].',';
			$floor_name .= $floor_library[$row[csf('floor_id')]].',';
			$buyer_name .= $buyer_library[$row[csf('buyer_name')]].',';
			$style_ref_no .= $row[csf('style_ref_no')].',';
			$job_no .= $row[csf('job_no')].',';
			$po_number .= $row[csf('po_number')].',';

			$operator = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");

			$smv_pcs_string=$row[csf("smv_pcs_set")];
			$smv_string_arr=explode("__",$smv_pcs_string);
		    foreach($smv_string_arr as $itemId)
		    {					
			    $smv_arr=explode("_",$itemId);
			    //echo '<pre>';print_r($smv_arr);die;
			    if($smv_arr[0] == $row[csf("item_number_id")]){
			    	$item_count++;
				    $total_smv += number_format($smv_arr[2],2);
			    }
		    }

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
				$production_data[$line_name]["operator"]=$operator;
				$production_data[$line_name]["helper"]=$helper;
				$production_data[$line_name]["hourly_target"]=$hourly_target;
				$production_data[$line_name]["working_hour"]=$working_hour;
				$production_data[$line_name]["target_efficiency"]=$target_efficiency;
				$production_data[$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnt += $row[csf($alter_hour)];
				$totalSpotQnt += $row[csf($spot_hour)];
				$totalRejectQnt +=$row[csf($reject_hour)];

				if ($h == 15 && date('G') == 14) break;
				if (date('G') == $h) break;
        		
			}
		}
		else
		{
			$location_name .= $location_library[$row[csf('location')]].',';
			$floor_name .= $floor_library[$row[csf('floor_id')]].',';
			$buyer_name .= $buyer_library[$row[csf('buyer_name')]].',';
			$style_ref_no .= $row[csf('style_ref_no')].',';
			$job_no .= $row[csf('job_no')].',';

			$operator = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			$line_name = $lineArr[$row[csf('sewing_line')]];

			$smv_pcs_string=$row[csf("smv_pcs_set")];
			$smv_string_arr=explode("__",$smv_pcs_string);
		    foreach($smv_string_arr as $itemId)
		    {					
			    $smv_arr=explode("_",$itemId);
			    //echo '<pre>';print_r($smv_arr);die;
			    if($smv_arr[0] == $row[csf("item_number_id")]){
				    $total_smv += number_format($smv_arr[2],2);
			    }
		    }

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
				$production_data[$line_name]["operator"]=$operator;
				$production_data[$line_name]["helper"]=$helper;
				$production_data[$line_name]["hourly_target"]=$hourly_target;
				$production_data[$line_name]["working_hour"]=$working_hour;
				$production_data[$line_name]["target_efficiency"]=$target_efficiency;
				$production_data[$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnt += $row[csf($alter_hour)];
				$totalSpotQnt += $row[csf($spot_hour)];
				$totalRejectQnt +=$row[csf($reject_hour)];	

				if ($h == 15 && date('G') == 14) break;
				if (date('G') == $h) break;				
        		
			}
		}	
	}
	//echo $count_hour;die;
	//echo  $total_smv;die;	
	//echo '<pre>';print_r($production_data);
	$total_smv=$total_smv/$item_count;
	$day_terget=$hourly_target*$working_hour;
	$current_target=$hourly_target*$count_hour;
	$varriance=$current_target-$totalProdQnty;
	$trend=$totalProdQnty/$count_hour*10;

	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnt+$totalSpotQnt+$totalRejectQnt;
	$dhu = ($totalProdQnty/$totalGoodAlrSpotRejectQnty)*100-100; //(qcQty/total_qty)*100-100;
	$efficiency=(($totalProdQnty*$total_smv)/(($operator+$helper)*$count_hour*60))*100;
	//efficiency=(prod_qty*smv)/((operators+helpers)*Number of hours*Minutes)
	$order_no = implode(',', array_flip(array_flip(explode(',', rtrim($po_number,',')))));
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));
	$location_name = implode(',', array_flip(array_flip(explode(',', rtrim($location_name,',')))));
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
		WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type in(4,5) and d.shiping_status!=3 $company_name $line 
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
		WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type in(4,5) and d.shiping_status!=3 $company_name $line 
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

	/*$sql_defect = "SELECT a.sewing_line, sum(b.defect_qty) as defect_qty
		FROM pro_garments_production_mst a, pro_gmts_prod_dft b
		WHERE a.id=b.mst_id and a.production_type=5 and a.po_break_down_id in($order_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $line
		group by a.sewing_line";
	$sql_defect_res=sql_select($sql_defect);
	$totDefectQnty=0;
	foreach ($sql_defect_res as $row) {
		$totDefectQnty += $row[csf('defect_qty')];
	}*/
	//echo$totDefectQnty.'**'.$totalGoodAlrSpotRejectQnty;die;
	

    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='knit_order_entry' and master_tble_id='".$result[0][csf('job_no')]."'","image_location");
    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id=$cbo_company_name and form_name='company_details' and is_deleted=0 and file_type=1");

   
	?>

	<style type="text/css">
		#secondtime table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		}
	</style>
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="firsttime">
        <div style="width:100%; height:4%; font-weight:bold; font-size: 1.5vw;">
        	<? echo date('F d, Y G:i'); ?>        		
        </div>
        <div style="width: 100%; height: 6%; background-color: #F00; overflow: hidden; clear: both; margin-left: 0;">
        	<div style="width: 25%; height: 100%; background-color: #F00; float: left; border-right: 2px solid white; vertical-align: middle;">
	        	<p style="color: white; font-size: 2vw; font-weight:bold; height: 100%; vertical-align: middle;"><?= $location_name.' ('.$floor_name.')'; ?></p>
        	</div>
        	<div style="width: 25%; height: 100%; background-color: #F00; float: left; border-right: 2px solid white; vertical-align: middle;"> 
        		<p style="color: white; font-size: 2vw; font-weight:bold; height: 100%; vertical-align: middle;"><?= $buyer_name; ?></p>   		    	
        	</div>
        	<div style="width: 12%; height: 100%; background-color: #F00; float: left; border-right: 2px solid white; vertical-align: middle;"> 
        		<p style="color: white; font-size: 2vw; font-weight:bold; height: 100%; vertical-align: middle;"><?= $line_name; ?></p> 
        	</div>

        	<div style="width: 30%; height: 100%; background-color: #F00; float: left; border-right: 2px solid white; vertical-align: middle;" id="xyz"> 
        		<p style="color: white; font-size: 2vw; font-weight:bold; height: 100%; vertical-align: middle;" id="order_buyer"><?= $order_no; ?></p>
        		<!-- <p style="color: white; font-size: 2vw; font-weight:bold; display: none;" id="buyer">Buyer</p> -->        		    	
        	</div>

        	<div style="width: 5%; height: 100%; background-color: #F00; float: left; vertical-align: middle;"> 
        		<p style="color: white; font-size: 2vw; font-weight:bold; height: 100%;vertical-align: middle;"><?= $count_hour.'Hr'; ?></p>	    	
        	</div>   	
        </div>

        <div style="width: 100%; height: 18%; overflow: hidden;">
        	<div style="width: 33%; height: 100%; background-color: #2975D8; float: left;">
	        	<p style="color: white; font-size: 5vw; font-weight:bold;"><?= $current_target; ?></p>
	        	<p style="color: white; font-size: 2vw; font-weight:bold;">Current Target</p>   	
        	</div>
        	<div style="width: 33.5%; height: 100%; background-color: #123665; float: left;"> 
        		<p style="color: white; font-size: 5vw; font-weight:bold;"><?= $day_terget; ?></p>
        		<p style="color: white; font-size: 2vw; font-weight:bold;">Day Target</p> 
        	</div>  
        	<div style="width: 33.5%; height: 100%; background-color: #2975D8; float: left;"> 
        		<p style="color: white; font-size: 5vw; font-weight:bold;"><?= round($trend); ?></p>
        		<p style="color: white; font-size: 2vw; font-weight:bold;">Trend</p>
        	</div>       	
        </div>

        <div style="width: 100%; height: 20%; overflow: hidden;">
        	<div style="width: 33%; height: 100%; background-color: #123665; float: left;">
        		<?
        		if ($current_target<=$totalProdQnty)
        		{
        			?>
        			<p><span style="color: white; font-size: 5vw; font-weight:bold;"><?= $totalProdQnty; ?></span><span style="font-size: 5vw; color: green; font-weight:900;"><strong>&nbsp;&#8679;</strong></span></p>        			
        			<?
        		}
        		else
        		{
        			?>
        			<p><span style="color: white; font-size: 5vw; font-weight:bold;"><?= $totalProdQnty; ?></span><span style="font-size: 5vw; color: red; font-weight:900;"><strong>&nbsp;&#8681;</strong></span></p>
        			<?
        		}
        		?>	
	        	<p style="color: white; font-size: 2vw; font-weight:bold;">Total Production</p>
        	</div>
        	<div style="width: 33.5%; height: 100%; background-color: #2975D8; float: left;">
        		<?
        		if ($varriance < 0)
        		{
        			?>
        			<p><span style="color: white; font-size: 5vw; font-weight:bold;"><?= trim(round($varriance),'-'); ?></span><span style="font-size: 5vw; color: green; font-weight:bold;">&nbsp;&#8679;</span></p>        			
        			<?
        		}
        		else
        		{
        			?>
        			<p><span style="color: white; font-size: 5vw; font-weight:bold;"><?= round($varriance); ?></span><span style="font-size: 5vw; color: red; font-weight:bold;">&nbsp;&#8681;</span></p>
        			<?
        		}
        		?>
	        	<p style="color: white; font-size: 2vw; font-weight:bold;">Variance</p>
        	</div>
        	<div style="width: 33.5%; height: 100%; background-color: #123665; float: left;">
        		<p style="color: white; font-size: 5vw; font-weight:bold;"><? echo round($efficiency); ?>%</p>
	        	<p style="color: white; font-size: 2vw; font-weight:bold;">Efficiency</p>
        	</div>     	
        </div>

        <div style="width: 100%; height: 22%; overflow: hidden;">
        	<div style="width: 33%; height: 100%; background-color: #2975D8; float: left;">
        		<p style="color: white; font-size: 1.5vw; font-weight:bold; text-align: left;">Man Power</p>
        		<p style="width: 100%; color: white; font-size: 5vw; font-weight:bold;"><span style="width: 60%; text-align: right;"><?= $operator; ?></span>&nbsp;&nbsp;&nbsp;<span style="width: 40%; text-align: center;"><?= $helper; ?></span></p>
        		<p style="width: 100%; color: white; font-size: 1.5vw; font-weight:bold;"><span style="width: 60%; text-align: right;">Operators&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;<span style="width: 40%; text-align: center;">Helpers&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
        		
        	</div>
        	<div style="width: 33.5%; height: 100%; background-color: #123665; float: left;">
        		<p style="color: red; font-size: 5vw; font-weight:bold;"><?= trim(number_format($dhu,2),'-'); ?>%</p>
	        	<p style="color: white; font-size: 2vw; font-weight:bold;">DHU%</p>
        	</div>
        	<div style="width: 33.5%; height: 100%; background-color: #2975D8; float: left;">
        		<p style="color: white; font-size: 5vw; font-weight:bold;"><?= $wip; ?></p>
	        	<p style="color: white; font-size: 2vw; font-weight:bold;">WIP Total</p>
        	</div>     	
        </div>

        <div style="width: 100%; height: 30%; background-color: #FFFFBF; overflow: hidden;">
        	<div style="float: left; width: 85%; height: 100%; font-size: 3.5vw;">
        		<p style="background-color: #F00; color: white; font-weight:bold; height: 33%;">Alter&nbsp;-&nbsp;<?= $totalAlterQnt; ?>&nbsp;:&nbsp;Spot&nbsp;-&nbsp;<?= $totalSpotQnt; ?>&nbsp;:&nbsp;Reject&nbsp;-&nbsp;<?= $totalRejectQnt; ?></p>
	        	<p style="color: black; font-weight:bold; height: 33%;">Job&nbsp;<?= $job_no; ?>&nbsp;&nbsp;&nbsp;SMV&nbsp;<? if($total_smv != 0) echo number_format($total_smv,2); ?></p>
	        	<p style="color: black; font-weight:bold; height: 34%;">Sty.&nbsp;<?= $style_ref_no; ?></p>
	        </div>
        	<div style="float: left; width: 15%;height: 100%; overflow: hidden;">
        		<img style="width: 100%; height: 100%; border: 1px solid #FFFFBF;" src="../../<? echo $image_location; ?>">
        	</div>
        </div>        
    </div>


    <div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif; display: none;" id="secondtime">
    	<div style="width: 100%; height: 7%; background-color: #16365c; overflow: hidden; clear: both; margin-left: 0; border-bottom: 1px solid black; overflow: hidden;">
    		<div style="width: 4%; height: 100%; background-color: #16365c; float: left; border-right: 2px solid black;">
	        	<?
	        	foreach ($data_array as $img_row) {
	        	?>
	        	<p><img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle"/></p>
	        	<?	
	        	}	
	        	?>
        	</div>
        	<div style="width: 20%; height: 100%; background-color: #16365c; float: left; border-right: 2px solid black;">
	        	<p style="color: white; font-size: 2vw; font-weight:bold; text-align: center;"><?= $location_name.' ('.$floor_name.')'; ?></p>
        	</div>
        	<div style="width: 25%; height: 100%; background-color: #16365c; float: left; border-right: 2px solid black;"> 
        		<p style="color: white; font-size: 2vw; font-weight:bold;"><?= $buyer_name; ?></p>        		    	
        	</div>
        	<div style="width: 14%; height: 100%; background-color: #16365c; float: left; border-right: 2px solid black;"> 
        		<p style="color: white; font-size: 2vw; font-weight:bold;"><?= $line_name; ?></p>        		    	
        	</div>
        	<div style="width: 25%; height: 100%; background-color: #16365c; float: left; border-right: 2px solid black;">
        		<p style="color: white; font-size: 2vw; font-weight:bold;"><?= $order_no; ?></p>
        	</div>
        	<div style="width: 10%; height: 100%; background-color: #16365c; float: left;"> 
        		<p style="color: white; font-size: 2vw; font-weight:bold;"><?= date('h:i A', time()); ?></p>   		    	
        	</div>   	
        </div>
        <div style="width: 100%; height: 93%; background-color: #16365c; overflow: hidden; clear: both; margin-left: 0; overflow: hidden;">
    		<table style="width: 100%; height: 10%; background-color: #16365c; overflow: hidden; clear: both; margin-left: 0; float: left;">
    			<tr>
	    			<td style="width: 8%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center;">Hour</td>
	    			<td style="width: 18%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center;">Hourly (Pcs) Actual/Plan</td>
	    			<td style="width: 18%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center;">Cum. (Pcs) Actual/Plan</td>
	    			<td style="width: 20%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center;">Variance (Pcs) Hourly/Cum.</td>
	    			<td style="width: 10%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center;">DHU%</td>
	    			<td style="width: 12%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center;">Actual Eff. %</td>
	    			<td style="width: 12%; color: white; font-size: 1.5vw; font-weight:bold; text-align: center;">Eff. Variance %</td>
    			</tr>
    			<?
	    		$hour_count=0;
	    		$varriancePcsHourly=0;
	    		$varriancePcsCum=$tot_hourly_target=0;
	    		$tot_good_qty=0;    		
	    		foreach($production_data as $line => $row)
				{
		    		for($h=$hour; $h<=$last_hour; $h++)
		    		{

		    			if ($h == 14) continue;
		    			$hour_count++;

		    			$hourlyTotalGoodAlterSpotRejectQty=0;
		    			$hourlyDhu=0;
		    			//$hourly_target=$target_efficiency=0;
		    			//$operator=$helper=0;
		    			$prod_hour='prod_hour'.substr($start_hour_arr[$h],0,2);
		    			$alter_hour='alter_hour'.substr($start_hour_arr[$h],0,2);
		    			$spot_hour='spot_hour'.substr($start_hour_arr[$h],0,2);
		    			$reject_hour='reject_hour'.substr($start_hour_arr[$h],0,2);
		    			$hourlyTotalGoodAlterSpotRejectQty=$row[$prod_hour]+$row[$alter_hour]+$row[$spot_hour]+$row[$reject_hour];
		    			$hourly_target = $row['hourly_target'];
						$target_efficiency = $row['target_efficiency'];
						$operator = $row['operator'];
						$helper   = $row['helper'];
						$varriancePcsHourly=$hourly_target - $row[$prod_hour];
						$tot_hourly_target += $hourly_target;
						$tot_good_qty += $row[$prod_hour];
						//$actual_efficiency = ($row[$prod_hour]*$total_smv)/(($operator+$helper)*60);
						$hourlyDhu = ($row[$prod_hour]/$hourlyTotalGoodAlterSpotRejectQty)*100-100; //(qcQty/total_qty)*100-100;
						$actual_efficiency=(($row[$prod_hour]*$total_smv)/(($operator+$helper)*60))*100;
						
			    		?>	
		    			<tr id="tr_<?= $h; ?>">
			    			<td style="width: 8%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><?= $hour_count; ?></td>
			    			<?
			    			if ($row[$prod_hour] < $hourly_target)
			    			{			    				
			    				?>
			    				<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><span style="color: red;"><?= $row[$prod_hour]; ?></span><span><?= '/'.$hourly_target; ?></span></td>
			    				<?
			    			}
			    			else
			    			{
			    				?>
			    				<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><span style="color: green;"><?= $row[$prod_hour]; ?></span><span><?= '/'.$hourly_target; ?></span></td>
			    				<?
			    			}
			    			?>

			    			<?
			    			if ($tot_good_qty < $tot_hourly_target)
			    			{
			    				?>
			    				<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><span style="color: red;"><?= $tot_good_qty; ?></span><span><?= '/'.$tot_hourly_target; ?></span></td>
			    				<?
			    			}
			    			else
			    			{
			    				?>
			    				<td style="width: 18%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><span style="color: green;"><?= $tot_good_qty; ?></span><span><?= '/'.$tot_hourly_target; ?></span></td>
			    				<?
			    			}
			    			?>


			    			<?
			    			if ( $varriancePcsHourly > 0)
			    			{
			    				$varriancePcsCum+=$varriancePcsHourly;
			    				?>
			    				<td style="width: 20%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><span style="color: green;"><?= $varriancePcsHourly; ?></span><span><?= '/'.trim($varriancePcsCum,'-'); ?></span></td>
			    				<?
			    			}
			    			else
			    			{
			    				$varriancePcsCum+=$varriancePcsHourly;
			    				?>
			    				<td style="width: 20%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><span style="color: red;"><?= trim($varriancePcsHourly,'-'); ?></span><span><?= '/'.trim($varriancePcsCum,'-'); ?></span></td>
			    				<?
			    			}	
			    			?>	
			    			<td style="width: 10%; color: red; font-size: 2vw; font-weight:bold; text-align: center;"><?= trim(number_format($hourlyDhu,2),'-').'%';?></td>
			    			<td style="width: 12%; color: white; font-size: 2vw; font-weight:bold; text-align: center;"><?= number_format($actual_efficiency,2); ?>%</td>
			    			<?			    			
			    			$efficiency_variance=$target_efficiency-$actual_efficiency;
			    			if ($efficiency_variance > 0)
			    			{
			    				?>
			    				<td style="width: 12%; color: red; font-size: 2vw; font-weight:bold; text-align: center;"><?= number_format($efficiency_variance,2); ?>%</td>
			    				<?
			    			}
			    			else
			    			{
			    				?>
			    				<td style="width: 12%; color: green; font-size: 2vw; font-weight:bold; text-align: center;"><?= number_format(trim($efficiency_variance,'-'),2); ?>%</td>
			    				<?
			    			}	
			    			?>			    			
		    			</tr>
			    		<?
			    		if ($h == 15 && date('G') == 14) break;
			    		if (date('G') == $h) break;			    					    		
		    		}
		    	}
		    	//echo $hour_count.'system';
	    		?>
    		</table>    		
        </div>
    </div>
    <?
	exit();
	

}

?>