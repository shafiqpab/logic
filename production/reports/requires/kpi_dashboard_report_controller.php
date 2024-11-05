<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
//$user_name=$_SESSION['logic_erp']['user_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");

if($action=="report_generate")
{ 
    // var_dump($_REQUEST);die();
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $cbo_company_id=str_replace("'","",$cbo_company_name); 

    if ($db_type==0) $txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    else $txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
    $txt_date_cond="";
	if ($txt_date !='') $txt_date_cond=" and a.production_date='$txt_date'";
    
    $company_cond="";
	if ($cbo_company_id !='') $company_cond=" and a.serving_company in($cbo_company_id)";

    // ============================ resource data ============================
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name in($cbo_company_id) and variable_list=23 and is_deleted=0 and status_active=1");    

	$prod_resource_array=array();
	$prod_reso_line_arr=array();
	$lineIds="";
	if($prod_reso_allo == 1)
	{
	    $dataArray = sql_select("SELECT a.id, a.company_id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id in($cbo_company_id) and b.pr_date='$txt_date'");
	  
		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['working_hour']=$row[csf('working_hour')];
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_efficiency']=$row[csf('target_efficiency')];
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['operator']=$row[csf('operator')];
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['helper']=$row[csf('helper')];
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['man_power']=$row[csf('man_power')];
			$prod_resource_array[$row[csf('company_id')]][$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$line_ids = $row[csf('id')];
			$prod_reso_line_arr[$row[csf('company_id')]][$row[csf('id')]]=$row[csf('line_number')];
		}
	}	
	unset($dataArray);
	// echo "<pre>";print_r($prod_resource_array);die;	
	$reso_data_arr = array();
	foreach ($prod_resource_array as $com_key => $com_data) 
	{
		foreach ($com_data as $line => $line_data) 
		{
			foreach ($line_data as $date => $r) 
			{
				$reso_data_arr[$com_key]['helper'] += $r['helper'];
				$reso_data_arr[$com_key]['operator'] += $r['operator'];
				$reso_data_arr[$com_key]['man_power'] += $r['man_power'];
			}			
			$reso_data_arr[$com_key]['tot_line']++;

		}
	}
	// echo "<pre>";print_r($reso_data_arr);die;	
	$start_time_arr=array();
	$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name in($cbo_company_id) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
    $prod_start_hour=$start_time_arr[1]['pst'];
	//if($prod_start_hour=='') 
	//$prod_start_hour='08:00';
	$start_time=explode(':',$prod_start_hour);
	
	$hour = $start_time[0]*1;
	$cur_hour = date('G') - $hour;
	if(date('G')>13)
	{
		$cur_hour = $cur_hour - 1;
	}

	$time1 = strtotime($hour.":00");
	$time2 = strtotime(date('H:i:s'));
	$difference_hour = round(((abs($time2 - $time1) / 3600)-1),0);
	

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

    // ==================================== production data =======================================
    $sql="SELECT a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id as po_id, a.sewing_line, sum(a.production_quantity) as good_qnty, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down as smv_pcs_set, c.po_number, c.grouping,a.serving_company, ";
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
		where a.po_break_down_id=c.id and b.id=c.job_id and a.production_type in (5) and  a.status_active=1 and a.is_deleted=0 $company_cond $txt_date_cond 
		group by a.prod_reso_allo, a.production_date, a.floor_id, a.item_number_id, a.po_break_down_id, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name, b.set_break_down, c.po_number,c.grouping,a.serving_company
		order by a.sewing_line"; 	
	// echo $sql;die;
    $result = sql_select($sql);
	foreach($result as $row)
	{
		$poId .= $row[csf("po_id")].",";
		$style_ref_no .= $row[csf('style_ref_no')].',';
		$job_no .= $row[csf('job_no')].',';
	}
    
	$order_ids = implode(',', array_flip(array_flip(explode(',', rtrim($poId,',')))));

    $smv_source=sql_select("SELECT company_name, smv_source from variable_settings_production where variable_list=25 and status_active=1 and is_deleted=0 and company_name in($cbo_company_id)");
	foreach($smv_source as $val){
		$smv_source=$val[csf('smv_source')];
	}
	//echo $smv_source;

	if ($smv_source==3) 
	{
		$sql_item="SELECT c.gmts_item_id, c.applicable_period, c.total_smv, b.id as color_size_id, b.po_break_down_id, b.order_quantity
		from ppl_gsd_entry_mst c, wo_po_details_master a, wo_po_color_size_breakdown b
		where c.STYLE_REF=a.STYLE_REF_NO and a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.bulletin_type=4 and b.po_break_down_id in($order_ids) order by c.applicable_period desc";
		$resultItem=sql_select($sql_item);
		$item_smv_array=array();
		$check_color_size_arr=array();
		foreach($resultItem as $row)
		{
			if ($check_color_size_arr[$row[csf('color_size_id')]]=="")
			{
				$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('gmts_item_id')]]['smv']=$row[csf('total_smv')];
				$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('gmts_item_id')]]['order_qty']+=$row[csf('order_quantity')];
				$check_color_size_arr[$row[csf('color_size_id')]]=$row[csf('color_size_id')];
			}		
		}
	}
	else
	{
		$sql_item="SELECT b.id as color_size_id, b.po_break_down_id, b.order_quantity, c.gmts_item_id, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in($order_ids)";
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
	$totalProdQntyforEffici=0;

	$production_data=array();
	$max_wo_hour_arr = array();
	$com_wise_max_wo_hour_arr = array();
	foreach($result as $row)
	{
		$count_hour=0;	
		if (strtotime($txt_date) != strtotime(date('d-m-Y'))) 
		{
			$count_hour = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
		}
		// echo $row[csf("po_id")].']['.$row[csf("item_number_id")].'<br>';
		
		if($row[csf('prod_reso_allo')]==1)
		{
			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('serving_company')]][$row[csf('sewing_line')]]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");	

			$operator = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$man_power   = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['man_power'];
			$hourly_target = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			$max_wo_hour_arr[$working_hour] = $working_hour;
			$com_wise_max_wo_hour_arr[$row[csf('serving_company')]][$working_hour] = $working_hour;

			if ($check_line_number_arr[$row[csf('serving_company')]][$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('serving_company')]][$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

				$production_data[$row[csf('serving_company')]][$line_name]["plan_effi"]+=$prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			}
			
			
			$total_order_qty+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

					
			
			$production_data[$row[csf('serving_company')]][$line_name]["line_wise_total_smv"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];			
			$production_data[$row[csf('serving_company')]][$line_name]["line_wise_total_order_qty"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

		    $production_data[$row[csf('serving_company')]][$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$row[csf('serving_company')]][$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$row[csf('serving_company')]][$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';
			$production_data[$row[csf('serving_company')]][$line_name]["grouping"]=$row[csf("grouping")].',';
		    $production_data[$row[csf('serving_company')]][$line_name]["operator"]=$operator;
			$production_data[$row[csf('serving_company')]][$line_name]["helper"]=$helper;
			$production_data[$row[csf('serving_company')]][$line_name]["hourly_target"]=$hourly_target;
			$production_data[$row[csf('serving_company')]][$line_name]["working_hour"]=$working_hour;
			$production_data[$row[csf('serving_company')]][$line_name]["man_power"]=$man_power;
			$production_data[$row[csf('serving_company')]][$line_name]["day_terget"]=$hourly_target*$working_hour;

			

			$production_data2[$row[csf('serving_company')]]["total_smv"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			$production_data2[$row[csf('serving_company')]]["total_order_qty"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			// echo $hourly_target."*".$working_hour."<br>";	
			$item_smv=$item_smv_array[$row["PO_ID"]][$row["ITEM_NUMBER_ID"]]['SMV'];
			$production_data[$row[csf('serving_company')]][$line_name]["current_target"]=$hourly_target*$cur_hour;
		

		    for($h=$hour; $h<=$last_hour; $h++)
			{
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				$replace_hour="replace_hour".substr($bg_hour,0,2);						
				
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];
				$totalReplaceQnty +=$row[csf($replace_hour)];		
				
				$production_data[$row[csf('serving_company')]][$line_name]["production_date"]=$row[csf("production_date")];
				// $production_data[$row[csf('serving_company')]][$line_name]["current_target"]=$hourly_target*$count_hour;
				$production_data[$row[csf('serving_company')]][$line_name]["target_efficiency"]=$target_efficiency;
				$production_data[$row[csf('serving_company')]][$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$replace_hour"]+=$row[csf("$replace_hour")];
				$total_smv += $item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$row[csf($prod_hour)];
		

				$production_data[$row[csf('serving_company')]][$line_name]["total_smv"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$row[csf($prod_hour)];

				$production_data[$row[csf('serving_company')]][$line_name]["totalProdQnty"]+=$row[csf("$prod_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalAlterQnty"]+=$row[csf("$alter_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalSpotQnty"]+=$row[csf("$spot_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalRejectQnty"]+=$row[csf("$reject_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalReplaceQnty"]+=$row[csf("$replace_hour")];
				$production_data2[$row[csf('serving_company')]]["totalProdQntyforEffici"] += $row[csf($prod_hour)]*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*100;
				// echo $row[csf($prod_hour)]."*".$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']."*100<br>";
				// echo $b+=$row[csf("$prod_hour")]."<br><br>";
				if (strtotime($txt_date) == strtotime(date('d-m-Y'))) 
				{
					if (date('G') == $h) break;  // Up to  Current hour calculation
					$count_hour++;
				}

				if ($count_hour >= $working_hour) 
				{
					$count_hour = $working_hour;
				}

				
			}
		}
		else
		{
			$operator = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['operator'];
			$helper   = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['helper'];
			$man_power   = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['man_power'];
			$hourly_target = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$working_hour = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['working_hour'];
			$target_efficiency = $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];

			if ($check_line_number_arr[$row[csf('serving_company')]][$row[csf('sewing_line')]] == '' )
			{
				$countLineNumber++;
				$check_line_number_arr[$row[csf('serving_company')]][$row[csf('sewing_line')]]=$row[csf('sewing_line')];
				$planned += $prod_resource_array[$row[csf('serving_company')]][$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			}
			
			$total_smv+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];
			$total_order_qty+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

			$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('serving_company')]][$row[csf('sewing_line')]]);
			$line_name = "";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= $lineArr[$resource_id].", ";
			}
			$line_name = chop($line_name," , ");


		    $production_data[$row[csf('serving_company')]][$line_name]["buyer_name"]=$buyer_library[$row[csf('buyer_name')]].',';
		    $production_data[$row[csf('serving_company')]][$line_name]["po_number"].=$row[csf("po_number")].',';
		    $production_data[$row[csf('serving_company')]][$line_name]["style_ref_no"]=$row[csf("style_ref_no")].',';
			$production_data[$row[csf('serving_company')]][$line_name]["grouping"]=$row[csf("grouping")].',';
		    $production_data[$row[csf('serving_company')]][$line_name]["operator"]=$operator;
			$production_data[$row[csf('serving_company')]][$line_name]["helper"]=$helper;
			$production_data[$row[csf('serving_company')]][$line_name]["hourly_target"]=$hourly_target;
			$production_data[$row[csf('serving_company')]][$line_name]["working_hour"]=$working_hour;
			$production_data[$row[csf('serving_company')]][$line_name]["man_power"]=$man_power;
			$production_data[$row[csf('serving_company')]][$line_name]["day_terget"]=$hourly_target*$working_hour;
			
			$production_data[$row[csf('serving_company')]][$line_name]["line_wise_total_smv"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];			
			$production_data[$row[csf('serving_company')]][$line_name]["line_wise_total_order_qty"]+=$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_qty'];

		    for($h=$hour; $h<$last_hour; $h++)
			{
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				$replace_hour="replace_hour".substr($bg_hour,0,2);				
				
				$production_data[$row[csf('serving_company')]][$line_name]["production_date"]=$row[csf("production_date")];
				$production_data[$row[csf('serving_company')]][$line_name]["current_target"]=$hourly_target*$count_hour;
				$production_data[$row[csf('serving_company')]][$line_name]["target_efficiency"]=$target_efficiency;
				$production_data[$row[csf('serving_company')]][$line_name]["$prod_hour"]+=$row[csf("$prod_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$alter_hour"]+=$row[csf("$alter_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$spot_hour"]+=$row[csf("$spot_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$reject_hour"]+=$row[csf("$reject_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["$replace_hour"]+=$row[csf("$replace_hour")];

				$totalProdQntyforEffici += $row[csf($prod_hour)]*$item_smv_array[$row[csf("po_id")]][$row[csf("item_number_id")]]['smv']*100;
				$totalProdQnty += $row[csf($prod_hour)];
				$totalAlterQnty += $row[csf($alter_hour)];
				$totalSpotQnty += $row[csf($spot_hour)];
				$totalRejectQnty +=$row[csf($reject_hour)];
				$totalReplaceQnty +=$row[csf($replace_hour)];

				$production_data[$row[csf('serving_company')]][$line_name]["totalProdQnty"]+=$row[csf("$prod_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalAlterQnty"]+=$row[csf("$alter_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalSpotQnty"]+=$row[csf("$spot_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalRejectQnty"]+=$row[csf("$reject_hour")];
				$production_data[$row[csf('serving_company')]][$line_name]["totalReplaceQnty"]+=$row[csf("$replace_hour")];

				if (date('G') == $h) break;  // Up to  Current hour calculation
				$count_hour++;
			}
		}	
	}

	// echo '<pre>';print_r($production_data);die;
	// echo $count_hour;die;

	$operator=$helper=$man_power=0;
	$smv_average=$total_working_hour=0;
	$highest_working_hour=0;
	$tot_hourly_target = 0;
	$data_array = array();
	$total_copany = 0;
	foreach ($production_data as $com_key => $com_data) 
	{
		foreach ($com_data as $line_no => $val) 
		{
			/* if ($val['working_hour']>$highest_working_hour) 
			{
				$highest_working_hour=$val['working_hour'];
			} */
			$tot_hourly_target += $val['hourly_target'];
			$operator += $val['operator'];
			$helper += $val['helper'];
			$data_array[$com_key]['hourly_target'] += $val['hourly_target'];
			$data_array[$com_key]['day_terget'] += $val['day_terget'];
			$data_array[$com_key]['current_target'] += $val['current_target'];
			$data_array[$com_key]['operator'] += $val['operator'];
			$data_array[$com_key]['helper'] += $val['helper'];
			$data_array[$com_key]['man_power'] += $val['man_power'];
			$data_array[$com_key]['working_hour'] += $val['working_hour'];
			$data_array[$com_key]['totalProdQnty'] += $val['totalProdQnty'];
			$data_array[$com_key]['totalAlterQnty'] += $val['totalAlterQnty'];
			$data_array[$com_key]['totalSpotQnty'] += $val['totalSpotQnty'];
			$data_array[$com_key]['totalRejectQnty'] += $val['totalRejectQnty'];
			$data_array[$com_key]['totalReplaceQnty'] += $val['totalReplaceQnty'];
			$data_array[$com_key]['totalProdQntyforEffici'] = $production_data2[$com_key]['totalProdQntyforEffici'];

			$data_array[$com_key]['varience'] += $val['current_target'] - $val['totalProdQnty'];
			$data_array[$com_key]['highest_working_hour'] = $highest_working_hour;
			$data_array[$com_key]['plan_effi'] += $val['plan_effi'];
			$data_array[$com_key]['total_smv'] += $val['total_smv'];
			$data_array[$com_key]['avg_smv'] = $production_data2[$com_key]['total_smv'] / $production_data2[$com_key]['total_order_qty'];
			$data_array[$com_key]['tot_line']++;
			// echo $line_no."<br>";
		}
		$total_copany++;
	}
	// echo '<pre>';print_r($data_array);die;
	$current_minute=date('i', time());
	$current_hour=$current_minute/60;	

	$working_hour = max($max_wo_hour_arr);
	// echo $working_hour;
	if ($working_hour>$highest_working_hour) 
	{
		$highest_working_hour=$working_hour;
	}

	// Hour calculation without lunch our and count hour is not greter than working hour
	if (strtotime($txt_date) == strtotime(date('d-m-Y'))) 
	{
		if (date('G')>13){
			$count_hour=$count_hour-1;
		}
		$count_hour=$count_hour;
		$avg_count_hour=$count_hour+$current_hour;
		if ($avg_count_hour>$highest_working_hour) 
		{
			$count_hour=$highest_working_hour;
			$avg_count_hour=ceil($total_working_hour/$countLineNumber);
		}
	}
	else
	{
		$avg_count_hour = 	$working_hour;
	}
	$totalGoodAlrSpotRejectQnty=$totalProdQnty+$totalAlterQnty+$totalSpotQnty+$totalRejectQnty;
	$gr_dhu = ($totalProdQnty/$totalGoodAlrSpotRejectQnty)*100-100; //(qcQty/total_qty)*100-100;
	// echo $current_target=$tot_hourly_target*floor($avg_count_hour);
	// echo $tot_hourly_target."*".floor($avg_count_hour);
	
	$smv_average=$total_smv/$totalProdQnty;
	// echo $efficiency=(($totalProdQnty*$smv_average)/(($operator+$helper)*$avg_count_hour*60))*100;
	
	

    $tbl_width = 1410;
    ob_start();

    ?>
    <div>
        <table width="<?=$tbl_width;?>" cellspacing="0" border="1" align="left" class="rpt_table" rules="all" id="table_header" >
            <thead>    
                <tr> 
                    <th width="120">Working Com.</th>
                    <th width="60">Production Date</th>
                    <th width="60">Prod Hr.</th>
                    <th width="60">Day Target</th>
                    <th width="60">Prod. Target</th>
                    <th width="60">Actual Prod.</th>
                    <th width="60">Variance</th>
                    <th width="60" title="[(totalProdQnty/avg_count_hour)*highest_working_hour]">Trend</th>
                    <th width="60" title="[totalProdQnty*total_smv_avg/(operator+helper)*current_hours*60*100]">Actual Effi.</th>                    
                    <th width="60" title="[Total Target efficiency / Total Line No.]">Planned Effi.</th>
                    <th width="60">Active Ope.</th>
                    <th width="60">Assign Ope.</th>
                    <th width="60">Active Helper</th>
                    <th width="60">Assign Helper</th>                    
                    <th width="60">Active Line Nos</th>
                    <th width="60">Assign Line</th>
                    <th width="60" >Avg. SMV</th>                 
                    <th width="60" >Alter</th>                 
                    <th width="60" >Spot</th>                 
                    <th width="60" >Reject</th>                 
                    <th width="60" >Total Defect</th>                 
                    <th width="60" >DHU%</th>   
                </tr>              
            </thead>
            <tbody>  
				<?
				$i=1;
				$tot_day_target = 0;
				$tot_cur_target = 0;
				$tot_prod_qty = 0;
				$tot_varience = 0;
				$tot_varience = 0;
				$tot_trend = 0;
				$tot_eff = 0;
				$tot_plan_eff = 0;
				$tot_act_operator= 0;
				$tot_assign_operator= 0;
				$tot_act_helper= 0;
				$tot_assign_helper= 0;
				$tot_act_line= 0;
				$tot_assign_line= 0;
				$tot_avg_smv= 0;
				$tot_avg_smv= 0;
				$tot_alt_qty= 0;
				$tot_spt_qty= 0;
				$tot_rej_qty= 0;
				$tot_dft_qty= 0;
				$tot_dhu = 0;
				$total_copany = 0;
				foreach ($data_array as $com_id => $v) 
				{
					if (strtotime($txt_date) != strtotime(date('d-m-Y'))) 
					{
						$count_hour = max($com_wise_max_wo_hour_arr[$com_id]);
						// $count_hour=$highest_working_hour;
						$time_hour = $count_hour.":00 Hr";
					}
					else
					{
						$time_hour=$count_hour.':'.date('i', time()).' Min';
					}
					
					// $highest_working_hour = max($max_wo_hour_arr[$com_id]);
					// $efficiency=($v['totalProdQntyforEffici']/($v['man_power']*$difference_hour*60));
					$avg_smv = $v['total_smv'] / $v['totalProdQnty'];
					if (strtotime($txt_date) == strtotime(date('d-m-Y'))) 
					{
						$efficiency=(($v['totalProdQnty']*$avg_smv)/(($v['operator']+$v['helper'])*$difference_hour*60))*100;
					}
					else
					{
						$efficiency=(($v['totalProdQnty']*$avg_smv)/(($v['operator']+$v['helper'])*$highest_working_hour*60))*100;
					}
					// echo $v['totalProdQnty']."/".$avg_smv."*".$avg_count_hour."*60<br>";
					
					$trend = ($v['totalProdQnty']/$avg_count_hour)*$highest_working_hour;
					// echo $v['totalProdQnty']."/".$avg_count_hour.")*".$highest_working_hour."<br>";
					$plan_effi = $v['plan_effi']/$v['tot_line'];
					// echo $v['plan_effi']."/".$v['tot_line']."<br>";
					$totalGoodAlrSpotRejectQnty = $v['totalProdQnty'] + $v['totalAlterQnty'] + $v['totalSpotQnty'] + $v['totalRejectQnty'];
					$total_dft = $v['totalAlterQnty'] + $v['totalSpotQnty'] + $v['totalRejectQnty'];
					$dhu = ($v['totalProdQnty']/$totalGoodAlrSpotRejectQnty)*100-100; 
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')"> 
						<td><?=$company_arr[$com_id];?></td>
						<td><?=change_date_format($txt_date,0);?></td>
						<td><?=$time_hour;?></td>
						<td align="right"><?=number_format($v['day_terget'],0);?></td>
						<? if (strtotime($txt_date) == strtotime(date('d-m-Y'))) {?>
						<td align="right"><?=number_format($v['current_target'],0);?></td>
						<? $varience = $v['hourly_target']*floor($avg_count_hour)- $v['totalProdQnty'];} else{?>
							<td align="right"><?=number_format($v['day_terget'],0);?></td>
						<? $varience = $v['day_terget'] - $v['totalProdQnty'];}?>
						<td align="right"><?=number_format($v['totalProdQnty'],0);?></td>
						<td align="right"><?=number_format($varience,0);?></td>
						<td align="right"><?=number_format($trend,0);?></td>
						<td align="right"><?=number_format($efficiency,2);?></td>                    
						<td align="right"><?=number_format($plan_effi,2);?></td>
						<td align="right"><?=number_format($v['operator'],0);?></td>
						<td align="right"><?=number_format($reso_data_arr[$com_id]['operator'],0);?></td>
						<td align="right"><?=number_format($v['helper'],0);?></td>
						<td align="right"><?=number_format($reso_data_arr[$com_id]['helper'],0);?></td>                    
						<td align="right"><?=number_format($v['tot_line'],0);?></td>
						<td align="right"><?=number_format($reso_data_arr[$com_id]['tot_line'],0);?></td>
						<td align="right"><?=number_format($v['avg_smv'],2);?></td>
						<td align="right"><?=number_format($v['totalAlterQnty'],0);?></td>
						<td align="right"><?=number_format($v['totalSpotQnty'],0);?></td>
						<td align="right"><?=number_format($v['totalRejectQnty'],0);?></td>
						<td align="right"><?=number_format($total_dft,0);?></td>
						<td align="right"><?if (is_nan($dhu)) echo $dhu2 = '0.00'; else echo $dhu2 = trim(number_format($dhu,2),'-');;?></td> 
					</tr>
					<?
					$i++;
					$total_copany++;
					$tot_day_target += $v['day_terget'];
					if (strtotime($txt_date) == strtotime(date('d-m-Y'))) 
					{
						$tot_cur_target += $v['hourly_target']*floor($avg_count_hour);
					}
					else
					{
						$tot_cur_target += $v['day_terget'];
					}
					$tot_prod_qty += $v['totalProdQnty'];
					$tot_varience += $varience;
					$tot_trend += $trend;
					$tot_eff += $efficiency;
					$tot_plan_eff += $plan_effi;
					$tot_act_operator+= $v['operator'];
					$tot_assign_operator+= $reso_data_arr[$com_id]['operator'];
					$tot_act_helper+= $v['helper'];
					$tot_assign_helper+= $reso_data_arr[$com_id]['helper'];
					$tot_act_line+= $v['tot_line'];
					$tot_assign_line+= $reso_data_arr[$com_id]['tot_line'];
					$tot_avg_smv+= $v['avg_smv'];
					$tot_alt_qty+= $v['totalAlterQnty'];
					$tot_spt_qty+= $v['totalSpotQnty'];
					$tot_rej_qty+= $v['totalRejectQnty'];
					$tot_dft_qty+= $total_dft;
					$tot_dhu += $dhu2;
					// $tot_avg_smv += $avg_smv;
					$tot_smv += $v['total_smv'];
				}
				$avg_smv = $tot_smv/$tot_prod_qty;
				if (strtotime($txt_date) == strtotime(date('d-m-Y'))) 
				{
					$tot_eff=(($tot_prod_qty*$avg_smv)/(($tot_act_operator+$tot_act_helper)*$difference_hour*60))*100;
				}
				else
				{
					$tot_eff=(($tot_prod_qty*$avg_smv)/(($tot_act_operator+$tot_act_helper)*$highest_working_hour*60))*100;
				}
				$tot_plan_eff = $planned / $tot_act_line;
				?>                
            </tbody>            
            <tfoot>  
                <tr>              
                    <th>Total</th>
                    <th></th>
                    <th></th>
                    <th><?=number_format($tot_day_target,0);?></th>
                    <th><?=number_format($tot_cur_target,0);?></th>
                    <th><?=number_format($tot_prod_qty,0);?></th>
                    <th><?=number_format($tot_varience,0);?></th>
                    <th><?=number_format($tot_trend,0);?></th>
                    <th><?=number_format($tot_eff,2);?></th>                    
                    <th><?=number_format($tot_plan_eff,2);?></th>
                    <th><?=number_format($tot_act_operator,0);?></th>
                    <th><?=number_format($tot_assign_operator,0);?></th>
                    <th><?=number_format($tot_act_helper,0);?></th>
                    <th><?=number_format($tot_assign_helper,0);?></th>                    
                    <th><?=number_format($tot_act_line,0);?></th>
                    <th><?=number_format($tot_assign_line,0);?></th>
                    <th><?=number_format($smv_average,2);?></th>                 
                    <th><?=number_format($tot_alt_qty,0);?></th>                 
                    <th><?=number_format($tot_spt_qty,0);?></th>                 
                    <th><?=number_format($tot_rej_qty,0);?></th>                 
                    <th><?=number_format($tot_dft_qty,0);?></th>                 
                    <th><?=number_format(abs($gr_dhu),2);?></th> 
                </tr>                
            </tfoot>
        </table>
    </div>
    <?
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    //$filename=$user_id."_".$name.".xls";
    echo "$total_data####$filename";

    exit();
}