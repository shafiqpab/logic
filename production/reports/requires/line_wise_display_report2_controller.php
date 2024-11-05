<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==='report_generate')
{ 	
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));

	$cbo_line=str_replace("'","",trim($cbo_line));
	$cbo_company_name=str_replace("'","",trim($cbo_company_name));
	//$txt_date=str_replace("'","",trim($txt_date));
	$max_height=str_replace("'","",trim($max_height));

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
    $buyer_short_name = return_library_array('select id, short_name from lib_buyer', 'id', 'short_name');

    // convert Line name to id 
    $ex_line = explode(',', $cbo_line);
    foreach ($ex_line as $value)
    {
    	$line_id = return_field_value('id','lib_sewing_line',"company_name=$cbo_company_name and line_name='$value' and status_active=1 and is_deleted=0");
    	$line_ids .= $line_id.',';
    }
    $line_ids = chop($line_ids,',');
    //echo $line_ids;

    // Check Variable Setting Use Prod Resource Allocation
    $prod_reso_allo=return_field_value('auto_update','variable_settings_production',"company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
    

	$prod_resource_array=array();
	if($prod_reso_allo == 1)
	{
	    $dataArray = sql_select("SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_name and a.line_number='$line_ids' and b.pr_date='$txt_date'");
	  
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
		}
	}	
	unset($dataArray);
	//echo "<pre>";print_r($prod_resource_array);die;

	$company_name=$line=$txt_date_cond='';
	if($cbo_company_name !='') $company_name=" and a.serving_company=$cbo_company_name";
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
	
    $prod_start_hour=$start_time_arr[1]['pst'];
	if($prod_start_hour=='') $prod_start_hour='08:00';
	$start_time=explode(':',$prod_start_hour);
	$hour=(int)substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
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

		$sql="SELECT  max(id) as id, a.prod_reso_allo, a.production_date, a.sewing_line, sum(a.production_quantity) as good_qnty, b.job_no, b.style_ref_no, b.buyer_name, group_concat(c.po_number) as po_number,";
		$first=1;
		
		for($h=$hour;$h<$last_hour;$h++)
        {
			$bg=$start_hour_arr[$h];
			$bg_hour=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,8);
			$prod_hour='prod_hour'.substr($bg_hour,0,2);
			if($first==1)
			{
			 $sql.="sum(CASE WHEN a.production_hour<='$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
		    $sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first=$first+1;
		}
		$prod_hour='prod_hour'.$last_hour;
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour";
	
		$sql.=" FROM pro_garments_production_mst a, wo_po_break_down c, wo_po_details_master b
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 $company_name $line $txt_date_cond
			group by a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name";
	}
	else
	{
		$sql="SELECT max(a.id) as id, a.prod_reso_allo, a.production_date, a.sewing_line, sum(a.production_quantity) as good_qnty, b.job_no, b.style_ref_no, b.buyer_name, listagg(c.po_number,',') within group (order by a.id) as po_number,";
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
        {
			$bg=$start_hour_arr[$h];
			$bg_hour=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,8);
			$prod_hour='prod_hour'.substr($bg_hour,0,2);
			if($first==1)
			{
			    $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
		        $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first=$first+1;
		}
		$prod_hour='prod_hour'.$last_hour;
		$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type in(5) THEN production_quantity else 0 END) AS $prod_hour";
															
		$sql.=" FROM pro_garments_production_mst a, wo_po_break_down c, wo_po_details_master b
			where a.production_type in (5) and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 $company_name $line $txt_date_cond
			group by a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no, b.style_ref_no, b.buyer_name"; 	
	}
	//echo $sql;die;
	$hourly_target = $efficiency = $totalProdQnty = 0;
	$hourly_production_arr=$buyer_style_job_order_arr=array();
	$result = sql_select($sql);
	foreach($result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			for($k=$hour; $k<=$last_hour; $k++)
			{
				$prod_hour='prod_hour'.substr($start_hour_arr[$k],0,2).'';
				$hourly_production_arr[$k] += $row[csf($prod_hour)];
				$totalProdQnty += $row[csf($prod_hour)];
			}

			$po_number_arr = array();
			$po_number_arr = explode(",",$row[csf('po_number')]);
			$last_po_number= end($po_number_arr);

			$buyer_style_job_order_arr[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
			$buyer_style_job_order_arr[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
			$buyer_style_job_order_arr[$row[csf('id')]]['job']   = $row[csf('job_no')];
			$buyer_style_job_order_arr[$row[csf('id')]]['order'] = $last_po_number;

			$max_id .= $row[csf('id')].',';

		}
		else
		{
			$hourly_target = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_per_hour'];
			$efficiency = $prod_resource_array[$row[csf('sewing_line')]][change_date_format($row[csf('production_date')])]['target_efficiency'];
			for($k=$hour; $k<=$last_hour; $k++)
			{
				$prod_hour='prod_hour'.substr($start_hour_arr[$k],0,2).'';
				$hourly_production_arr[$k] += $row[csf($prod_hour)];
				$totalProdQnty += $row[csf($prod_hour)];
			}

			$po_number_arr = array();
			$po_number_arr = explode(",",$row[csf('po_number')]);
			$last_po_number= end($po_number_arr);

			$buyer_style_job_order_arr[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
			$buyer_style_job_order_arr[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
			$buyer_style_job_order_arr[$row[csf('id')]]['job']   = $row[csf('job_no')];
			$buyer_style_job_order_arr[$row[csf('id')]]['order'] = $last_po_number;

			$max_id .= $row[csf('id')].',';
			
		}	
	}
	//die;
	//echo $job_numbers;die;
	//echo '<pre>';print_r($buyer_style_job_order_arr);
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
		WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type in(4,5) $company_name $line 
		GROUP BY a.prod_reso_allo, a.sewing_line";


		$sql_qcAlterSpotReject="SELECT a.prod_reso_allo, a.sewing_line, sum(a.production_quantity) as good_qnty_new, sum(a.alter_qnty) as alter_qnty_new,  sum(a.spot_qnty) as spot_qnty_new, sum(a.reject_qnty) as reject_qnty_new,";
		$first=1;
		
		for($h=$hour;$h<$last_hour;$h++)
        {
			$bg=$start_hour_arr[$h];
			$bg_hour=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,8);
			$prod_hour_new='prod_hour_new'.substr($bg_hour,0,2);
			$alter_hour_new='alter_hour_new'.substr($bg_hour,0,2);
			$spot_hour_new='spot_hour_new'.substr($bg_hour,0,2);
			$reject_hour_new='reject_hour_new'.substr($bg_hour,0,2);
			if($first==1)
			{
			    $sql_qcAlterSpotReject.="sum(CASE WHEN a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
			        sum(CASE WHEN a.production_hour<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
					sum(CASE WHEN a.production_hour<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
					sum(CASE WHEN a.production_hour<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
			}
			else
			{
		    	$sql_qcAlterSpotReject.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
			       sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
				   sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
				   sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
			}
			$first=$first+1;
		}
		$prod_hour_new='prod_hour_new'.$last_hour;
		$alter_hour_new='alter_hour_new'.$last_hour;
		$spot_hour_new='spot_hour_new'.$last_hour;
		$reject_hour_new='reject_hour_new'.$last_hour;
		$sql_qcAlterSpotReject.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
			    sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
			    sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
			    sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new";
	
		$sql_qcAlterSpotReject.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and b.status_active=1 $company_name $line $txt_date_cond group by a.prod_reso_allo, a.sewing_line";
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
		WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type in(4,5) $company_name $line 
		GROUP BY a.prod_reso_allo, a.sewing_line";


		$sql_qcAlterSpotReject="SELECT a.prod_reso_allo, a.sewing_line, sum(a.production_quantity) as good_qnty_new, sum(a.alter_qnty) as alter_qnty_new,  sum(a.spot_qnty) as spot_qnty_new, sum(a.reject_qnty) as reject_qnty_new,";
		$first=1;		
		for($h=$hour;$h<$last_hour;$h++)
        {
			$bg=$start_hour_arr[$h];
			$bg_hour=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,8);
			$prod_hour_new='prod_hour_new'.substr($bg_hour,0,2);
			$alter_hour_new='alter_hour_new'.substr($bg_hour,0,2);
			$spot_hour_new='spot_hour_new'.substr($bg_hour,0,2);
			$reject_hour_new='reject_hour_new'.substr($bg_hour,0,2);
			if($first==1)
			{
			    $sql_qcAlterSpotReject.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
			        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
					sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
					sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
			}
			else
			{
		    	$sql_qcAlterSpotReject.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
			       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
				   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
				   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
			}
			$first=$first+1;
		}
		$prod_hour_new='prod_hour_new'.$last_hour;
		$alter_hour_new='alter_hour_new'.$last_hour;
		$spot_hour_new='spot_hour_new'.$last_hour;
		$reject_hour_new='reject_hour_new'.$last_hour;
		$sql_qcAlterSpotReject.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
			     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
			     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
			     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new";
	
		$sql_qcAlterSpotReject.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and b.status_active=1 $company_name $line $txt_date_cond group by a.prod_reso_allo, a.sewing_line";
	}
	//echo $sql_qcAlterSpotReject;die;
	$total_sewing_input = $total_sewing_output = $today_sewing_input = $total_sewing_input_except_current_date = $total_sewing_output_except_current_date=0;
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

	$prodAlterReject_Spot_res = sql_select($sql_qcAlterSpotReject);
	$totalQcQnty = $totalAlterQnty = $totalSpotQnty = $totalRejectQnty = 0;
	foreach($prodAlterReject_Spot_res as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
			$good_qnty_new   = $row[csf('good_qnty_new')];
			$alter_qnty_new  = $row[csf('alter_qnty_new')];
			$spot_qnty_new   = $row[csf('spot_qnty_new')];
			$reject_qnty_new = $row[csf('reject_qnty_new')];
			for($k=$hour; $k<=$last_hour; $k++)
			{
				$prod_hour_new   = 'prod_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$alter_hour_new  = 'alter_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$spot_hour_new   = 'spot_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$reject_hour_new = 'reject_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$totalQcQnty   += $row[csf($prod_hour_new)];
				$totalAlterQnty  += $row[csf($alter_hour_new)];
				$totalSpotQnty   += $row[csf($spot_hour_new)];
				$totalRejectQnty += $row[csf($reject_hour_new)];
			}
		}
		else
		{
			$good_qnty_new   = $row[csf('good_qnty_new')];
			$alter_qnty_new  = $row[csf('alter_qnty_new')];
			$spot_qnty_new   = $row[csf('spot_qnty_new')];
			$reject_qnty_new = $row[csf('reject_qnty_new')];
			for($k=$hour; $k<=$last_hour; $k++)
			{
				$prod_hour_new   = 'prod_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$alter_hour_new  = 'alter_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$spot_hour_new   = 'spot_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$reject_hour_new = 'reject_hour_new'.substr($start_hour_arr[$k],0,2).'';
				$totalQcQnty     += $row[csf($prod_hour_new)];
				$totalAlterQnty  += $row[csf($alter_hour_new)];
				$totalSpotQnty   += $row[csf($spot_hour_new)];
				$totalRejectQnty += $row[csf($reject_hour_new)];
			}
		}	
	}

	$explode_max_id = explode(",",rtrim($max_id,','));
	$final_max_id   = max($explode_max_id);
	$last_buyer_name   = $buyer_style_job_order_arr[$final_max_id]['buyer'];
	$last_style_number = $buyer_style_job_order_arr[$final_max_id]['style'];
	$last_job_number   = $buyer_style_job_order_arr[$final_max_id]['job'];
	$last_order_number = $buyer_style_job_order_arr[$final_max_id]['order'];

	$last_order_number_strlen = strlen($last_order_number);
	if ($last_order_number_strlen > 13)
		$last_order_number = substr($last_order_number,0,13);

 	$total_smv=0;
	$sql_smv="select id, po_job_no, gmts_item_id, total_smv from ppl_gsd_entry_mst where po_job_no='".$last_job_number."' and status_active=1 and is_deleted=0 group by id, po_job_no, gmts_item_id, total_smv order by id desc";
	$sql_smv_res = sql_select($sql_smv);
	$total_smv   = $sql_smv_res[0][csf('total_smv')];
	

    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='knit_order_entry' and master_tble_id='".$result[0][csf('job_no')]."'","image_location");

    $wip_except_cur_date = $total_sewing_input_except_current_date-$total_sewing_output_except_current_date;
    $wip = $wip_except_cur_date+$today_sewing_input;
	?>   
	
	<div style="width:100%; height: <? echo $max_height; ?>px; font-family: Arial, Helvetica, sans-serif;">
        <div style="width:100%; height:4%; font-weight:bold; text-align: center; font-size: 1.5vw;">
        	<? echo date('F d, Y G:i'); ?>        		
        </div>
        <div style="width: 100%; height: 23%; background-color: #FF0000; overflow: hidden; clear: both; margin-left: 0;">
        	<span style="width: 50%; display: inline-block; color: white; font-size: 8.2vw; font-weight:bold; text-align: center; float: left;">Line&nbsp;
        		<? echo $cbo_line; ?>
        	</span>
        	<span style="width: 50%; display: inline-block;  color: white; font-size: 6.2vw; font-weight:bold; text-align: center;float: left;">
        		<? echo $efficiency; ?>% Efficiency   			
        	</span>      	
        </div>
        <div style="width: 100%; height: 23%;">
        	<div style="width: 50%; height: 100%; background-color: #2975D8; float: left;">
	        	<p style="color: white; font-size: 5vw; font-weight:bold;"><? 
	        		$hr = date("G");
	        		$hourly_production_value='';	
	        		$hourly_production_value = $hourly_production_arr[$hr];      		
					if ($hourly_production_value == '') $hourly_production_value = 0;
	        		echo $hourly_production_value; ?></p>
	        	<p style="color: white; font-size: 3vw; font-weight:bold;">Hourly Production</p>   	
        	</div>
        	<div style="width: 50%; height: 100%; background-color: #002066; float: left;"> 
        		<p style="color: white; font-size: 5vw; font-weight:bold;"><? echo $hourly_target; ?></p>
        		<p style="color: white; font-size: 3vw; font-weight:bold;">Hourly Target</p>       	
        	</div>        	
        </div>
        <div style="width: 100%; height: 23%;">
        	<div style="width: 50%; height: 100%; background-color: #123665; float: left;">
        		<p style="color: white; font-size: 5vw; font-weight:bold;"><? echo $totalProdQnty; ?></p>
	        	<p style="color: white; font-size: 3vw; font-weight:bold;">Total Production</p>
        	</div>
        	<div style="width: 50%; height: 100%; background-color: #2975D8; float: left;">
        		<p style="color: white; font-size: 5vw; font-weight:bold;"><? echo $totalQcQnty; ?></p>
	        	<p style="color: white; font-size: 3vw; font-weight:bold;">Total QC Passed</p>
        	</div>        	
        </div>
        <div style="width: 100%; height: 23%; background-color: #FF0000; display: none;" id="firsttime">
        	<div style="float: left; width: 85%; height: 100%">
	        	<p style="color: white; font-size: 3.5vw; font-weight:bold; height: 50%;">Today Input <? echo $today_sewing_input; ?>&nbsp;(WIP&nbsp;<? echo $wip; ?>)</p>
	        	<p style="color: white; font-size: 3.5vw; font-weight:bold; height: 50%;">Alter&nbsp;<? echo $totalAlterQnty; ?>&nbsp;:&nbsp;Spot&nbsp;<? echo $totalSpotQnty; ?>&nbsp;:&nbsp;Reject&nbsp;<? echo $totalRejectQnty; ?></p>
        	</div>
        	<div style="float: left; width: 15%;height: 100%; overflow: hidden;">
        		<img style="width: 100%; height: 100%; border: 1px solid #FF0000;" src="../../<? echo $image_location; ?>">
        	</div>
        </div>
        
        <div style="width: 100%; height: 23%; background-color: #FFFFBF; display: none;" id="secondtime">
        	<div style="float: left; width: 85%; height: 100%">
	        	<p style="color: black; font-size: 3.5vw; font-weight:bold; height: 50%;">Job&nbsp;<?	echo substr($last_job_number,4);	?>&nbsp;<? echo $buyer_short_name[$last_buyer_name]; ?>&nbsp;SMV&nbsp;<? if($total_smv != 0) echo number_format($total_smv,1); ?></p>
	        	<p style="color: black; font-size: 3.5vw; font-weight:bold; height: 50%;">Sty.&nbsp;<? echo $last_style_number; ?>&nbsp;Ord.&nbsp;<? echo $last_order_number; ?></p>
	        </div>
        	<div style="float: left; width: 15%;height: 100%; overflow: hidden;">
        		<img style="width: 100%; height: 100%; border: 1px solid #FFFFBF;" src="../../<? echo $image_location; ?>">
        	</div>
        </div>        
    </div>
    <?
	exit();
	

}

?>