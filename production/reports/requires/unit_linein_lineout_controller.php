<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="report_generate")
{ 	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	// echo "<pre>";print_r($floor_library);die;

	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}


    // convert company name to id
    $com_id_arr=array();
    $sql_com = "SELECT id, company_name from lib_company where company_name = $cbo_company_name";
    $sql_com_rslt = sql_select($sql_com);
    foreach ($sql_com_rslt as $row) {
    	$com_id_arr[$row[csf('company_name')]] = $row[csf('id')];
    }
    $cbo_company_name = $com_id_arr[$row[csf('company_name')]];


    // convert floor name to id 
    $floor_id_arr=array();
    $sql_floor = "SELECT id, floor_name from lib_prod_floor where floor_name = $cbo_floor";
    $sql_floor_rslt = sql_select($sql_floor);
    foreach ($sql_floor_rslt as $row) {
    	$floor_id_arr[$row[csf('floor_name')]] = $row[csf('id')];
    }
    $cbo_floor = $floor_id_arr[$row[csf('floor_name')]];    


	//echo $txt_job_no;cbo_floor
	$cbo_floor=str_replace("'","",$cbo_floor);
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";

	if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
	if($cbo_floor=="") $floor="";else $floor=" and a.floor_id in($cbo_floor)";
	if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";

	$txt_date = str_replace("'","",trim($txt_date));
	if ($db_type==0)
	{
		$txt_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date)));
		$txt_date_cond = " and a.production_date='$txt_date'";
	}
	else
	{
		$txt_date = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date)));
		$txt_date_cond = " and a.production_date='$txt_date'";
	}

	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; 
	else $txt_date_from=" and a.production_date=$txt_date";
	//echo $txt_date_from;die;
	if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
	if(str_replace("'","",trim($txt_job_no))=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num=$txt_job_no";
	if(str_replace("'","",trim($txt_order_no))=="") $order_no_cond=""; else $order_no_cond=" and c.po_number=$txt_order_no";
	//$sql_resource="select from_date, to_date, ";
	$prod_resource_array=array();
	
    $dataArray=sql_select("SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator,  b.helper, c.target_efficiency	from prod_resource_mst a,  prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_name");    
  
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
	}

	$start_time_arr=array();
	if($db_type==0)
	{
		//echo "select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($cbo_company_name) and  shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0";die;
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($cbo_company_name) and  shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($cbo_company_name) and  shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");	
	}

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
    $prod_start_hour=$start_time_arr[1]['pst'];
	//if($prod_start_hour=="") 
	$prod_start_hour="09:00";

	$start_time=explode(":",$prod_start_hour);
	$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
	//echo $hour;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{		
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
    $start_hour_arr[$j+1]='23:59';
		//print_r($start_hour_arr);
	//var_dump($prod_resource_array);
		
	?>

    <div style="width:100%">
        <div style="width:100%; font-weight:bold;"> <? echo $floor_library[$cbo_floor]; ?>: Sewing Production Status (
        	<?php 
                echo date("l, F d", strtotime(str_replace("'", "",  $txt_date)));		        
        	?>)
        </div>

        <?php
		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
			
		if($db_type==0)
		{

			$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down as smv_pcs_set, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
		              sum(a.production_quantity) as good_qnty, 
		              sum(a.alter_qnty) as alter_qnty,
		              sum(a.spot_qnty) as spot_qnty, 
		              sum(a.reject_qnty) as reject_qnty,";
			$first=1;
			$total_goods=array();
			$total_alter=array();
			$total_reject=array();
			$total_spot=array();
			
			for($h=$hour;$h<$last_hour;$h++)
	        {
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,8);
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				if($first==1)
				{
				 $sql.="sum(CASE WHEN a.production_hour<'$end' and a.production_type=13 THEN production_quantity else 0 END) AS $prod_hour,
				        sum(CASE WHEN a.production_hour<'$end'  and a.production_type=13 THEN alter_qnty else 0 END) AS $alter_hour,
						sum(CASE WHEN a.production_hour<'$end' and a.production_type=13 THEN reject_qnty else 0 END) AS $reject_hour,
						sum(CASE WHEN a.production_hour<'$end' and a.production_type=13 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				else
				{
			    $sql.="sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end' and a.production_type=13 THEN production_quantity else 0 END) AS $prod_hour,
				       sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end'  and a.production_type=13 THEN alter_qnty else 0 END) AS $alter_hour,
					   sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end' and a.production_type=13 THEN reject_qnty else 0 END) AS $reject_hour,
					   sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end' and a.production_type=13 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				$first=$first+1;
			}
			$prod_hour="prod_hour".$last_hour;
			$alter_hour="alter_hour".$last_hour;
			$spot_hour="spot_hour".$last_hour;
			$reject_hour="reject_hour".$last_hour;
			$sql.="sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=13 THEN production_quantity else 0 END) AS $prod_hour,
				     sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]'  and a.production_type=13 THEN alter_qnty else 0 END) AS $alter_hour,
				     sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=13 THEN reject_qnty else 0 END) AS $reject_hour,
				     sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=13 THEN spot_qnty else 0 END) AS $spot_hour";
		
			$sql.="	from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
					where a.entry_form=349 and a.production_type=13 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_cond $style_no_cond  $order_no_cond $job_no_cond group by a.prod_reso_allo, a.floor_id, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line"; 
			// echo $sql;die; //$txt_date
		}	
		else
		{

			$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down as smv_pcs_set, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id, listagg(c.po_number,',') within group (order by po_number) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor, listagg(c.grouping,',') within group (order by grouping) as grouping, listagg(c.file_no,',') within group (order by file_no) as file_no,
			sum(a.production_quantity) as good_qnty, 
			sum(a.alter_qnty) as alter_qnty,
			sum(a.spot_qnty) as spot_qnty, 
			sum(a.reject_qnty) as reject_qnty,";
			$first=1;
			$total_goods=array();
			$total_alter=array();
			$total_reject=array();
			$total_spot=array();
			for($h=$hour;$h<$last_hour;$h++)
	        {
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,8);
				$prod_hour="prod_hour".substr($bg_hour,0,2);
				$alter_hour="alter_hour".substr($bg_hour,0,2);
				$spot_hour="spot_hour".substr($bg_hour,0,2);
				$reject_hour="reject_hour".substr($bg_hour,0,2);
				if($first==1)
				{
				 $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=13 THEN production_quantity else 0 END) AS $prod_hour,
				        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end'  and a.production_type=13 THEN alter_qnty else 0 END) AS $alter_hour,
						sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=13 THEN reject_qnty else 0 END) AS $reject_hour,
						sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=13 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				else
				{
			    $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=13 THEN production_quantity else 0 END) AS $prod_hour,
				       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end'  and a.production_type=13 THEN alter_qnty else 0 END) AS $alter_hour,
					   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=13 THEN reject_qnty else 0 END) AS $reject_hour,
					   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=13 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				$first=$first+1;
			}
			$prod_hour="prod_hour".$last_hour;
			$alter_hour="alter_hour".$last_hour;
			$spot_hour="spot_hour".$last_hour;
			$reject_hour="reject_hour".$last_hour;
			$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=13 THEN production_quantity else 0 END) AS $prod_hour,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=13 THEN alter_qnty else 0 END) AS $alter_hour,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=13 THEN reject_qnty else 0 END) AS $reject_hour,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=13 THEN spot_qnty else 0 END) AS $spot_hour";
																
			$sql.=" FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				 where a.entry_form=349 and a.production_type=13 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_cond $style_no_cond  $order_no_cond $job_no_cond 
				 group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down, a.location, a.floor_id,a.production_date 
				 order by a.floor_id, a.sewing_line"; 	
		}
		//echo $sql;die;   

		$result = sql_select($sql);
		$totalGood=0;$totalAlter=0;$totalSpot=0;$totalReject=0;$totalinputQnty=0;
		$production_data=array();
		$poId = "";
		foreach($result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$order_number = implode(',',array_unique(explode(",",$row[csf("po_break_down_id")])));
			//echo $order_number;
			$poId .= $order_number.",";
			//total good,alter,reject qnty
			$totalGood += $row[csf("good_qnty")];
			$totalAlter += $row[csf("alter_qnty")];
			$totalSpot += $row[csf("spot_qnty")];
			$totalReject += $row[csf("reject_qnty")];			

			if($row[csf("prod_reso_allo")]==1)
			{
				//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name="";

				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}

				$line_name=chop($line_name," , ");

				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["company_id"]=$row[csf("company_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["location"]=$row[csf("location")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["floor_id"]=$row[csf("floor_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["production_date"]=$row[csf("production_date")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["sewing_line"]=$row[csf("sewing_line")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["job_no"]=$row[csf("job_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["item_number_id"].=$row[csf("item_number_id")].",";
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["smv_pcs_set"]=$row[csf("smv_pcs_set")].",";
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["grouping"]=$row[csf("grouping")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["file_no"]=$row[csf("file_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["po_number"]=$row[csf("po_number")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["supervisor"]=$row[csf("supervisor")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["good_qnty"]+=$row[csf("good_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["alter_qnty"]+=$row[csf("alter_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["spot_qnty"]+=$row[csf("spot_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["reject_qnty"]+=$row[csf("reject_qnty")];
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					$prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$prod_hour"]+=$row[csf("$prod_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$alter_hour"]+=$row[csf("$alter_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$spot_hour"]+=$row[csf("$spot_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$reject_hour"]+=$row[csf("$reject_hour")];
				}
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["company_id"]=$row[csf("company_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["location"]=$row[csf("location")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["floor_id"]=$row[csf("floor_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["production_date"]=$row[csf("production_date")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["sewing_line"]=$row[csf("sewing_line")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["job_no"]=$row[csf("job_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["item_number_id"].=$row[csf("item_number_id")].",";
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["smv_pcs_set"]=$row[csf("smv_pcs_set")].",";
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["grouping"]=$row[csf("grouping")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["file_no"]=$row[csf("file_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["po_number"]=$row[csf("po_number")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["supervisor"]=$row[csf("supervisor")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["good_qnty"]+=$row[csf("good_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["alter_qnty"]+=$row[csf("alter_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["spot_qnty"]+=$row[csf("spot_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["reject_qnty"]+=$row[csf("reject_qnty")];
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					//$end=substr(add_time($start_hour_arr[$h],60),0,8);
					$prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$prod_hour"]+=$row[csf("$prod_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$alter_hour"]+=$row[csf("$alter_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$spot_hour"]+=$row[csf("$spot_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$reject_hour"]+=$row[csf("$reject_hour")];
					
				}
			}	
		}
		ksort($production_data);
		//echo $poId;
		$poIds=chop($poId,',');
		?>
		<div style="text-align: center; color: red; font-size: 18px;">
			<?php 
			    if ($poIds == '') {
			        echo "Production are not started !!";
			        die;
		        }
		    ?>
		</div>
		<?
		// ===========FOR SEWING DATA(TODAY,TOTAL)==================
		if($db_type==0)
		{

			$prod_qnty_data = "SELECT a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, c.job_no,a.item_number_id, 
			sum(case when a.production_type=12 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_input, 
			sum(case when a.production_type=13 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_output, 
			sum(case when a.production_type=12 and a.production_date='$txt_date' then a.production_quantity else 0 END) as today_sewing_input
			FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
			WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.po_break_down_id in($poIds) and a.entry_form in(348,349) and a.production_type in(12,13)
			GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id,c.job_no,a.item_number_id";


			$sql_qcAlterSpotReject="SELECT  a.location, a.floor_id, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, c.job_no, a.item_number_id, sum(a.production_quantity) as good_qnty_new, sum(a.alter_qnty) as alter_qnty_new,  sum(a.spot_qnty) as spot_qnty_new, sum(a.reject_qnty) as reject_qnty_new,";
			$first=1;
			$total_goods_new=array();
			$total_alter_new=array();
			$total_reject_new=array();
			$total_spot_new=array();
			
			for($h=$hour;$h<$last_hour;$h++)
	        {
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,8);
				$prod_hour_new="prod_hour_new".substr($bg_hour,0,2);
				$alter_hour_new="alter_hour_new".substr($bg_hour,0,2);
				$spot_hour_new="spot_hour_new".substr($bg_hour,0,2);
				$reject_hour_new="reject_hour_new".substr($bg_hour,0,2);
				if($first==1)
				{
				    $sql_qcAlterSpotReject.="sum(CASE WHEN a.production_hour<'$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
				        sum(CASE WHEN a.production_hour<'$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
						sum(CASE WHEN a.production_hour<'$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
						sum(CASE WHEN a.production_hour<'$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
				}
				else
				{
			    	$sql_qcAlterSpotReject.="sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
				       sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
					   sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
					   sum(CASE WHEN a.production_hour>='$bg' and  a.production_hour<'$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
				}
				$first=$first+1;
			}
			$prod_hour_new="prod_hour_new".$last_hour;
			$alter_hour_new="alter_hour_new".$last_hour;
			$spot_hour_new="spot_hour_new".$last_hour;
			$reject_hour_new="reject_hour_new".$last_hour;
			$sql_qcAlterSpotReject.="sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
				     sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
				     sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
				     sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new";
		
			$sql_qcAlterSpotReject.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
					where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in($poIds) $txt_date_cond and c.status_active=1 and b.status_active=1 group by a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, b.job_no, a.item_number_id";

		}
		else
		{
			
			$prod_qnty_data = "SELECT a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, c.job_no,a.item_number_id, 
			sum(case when a.production_type=12 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_input,
			sum(case when a.production_type=13 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_output, 
			sum(case when a.production_type=12 and a.production_date='$txt_date' then a.production_quantity else 0 END) as today_sewing_input
			FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
			WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.po_break_down_id in($poIds) and a.entry_form in(348,349) and a.production_type in(12,13)
			GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id,c.job_no,a.item_number_id";
			//echo $prod_qnty_data;

			$sql_qcAlterSpotReject="SELECT  a.location, a.floor_id, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, b.job_no, a.item_number_id, sum(a.production_quantity) as good_qnty_new, sum(a.alter_qnty) as alter_qnty_new,  sum(a.spot_qnty) as spot_qnty_new, sum(a.reject_qnty) as reject_qnty_new,";
			$first=1;
			$total_goods_new=array();
			$total_alter_new=array();
			$total_reject_new=array();
			$total_spot_new=array();
			
			for($h=$hour;$h<$last_hour;$h++)
	        {
				$bg=$start_hour_arr[$h];
				$bg_hour=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,8);
				$prod_hour_new="prod_hour_new".substr($bg_hour,0,2);
				$alter_hour_new="alter_hour_new".substr($bg_hour,0,2);
				$spot_hour_new="spot_hour_new".substr($bg_hour,0,2);
				$reject_hour_new="reject_hour_new".substr($bg_hour,0,2);
				if($first==1)
				{
				    $sql_qcAlterSpotReject.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
				        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
						sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
						sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
				}
				else
				{
			    	$sql_qcAlterSpotReject.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
				       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
					   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
					   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new,";
				}
				$first=$first+1;
			}
			$prod_hour_new="prod_hour_new".$last_hour;
			$alter_hour_new="alter_hour_new".$last_hour;
			$spot_hour_new="spot_hour_new".$last_hour;
			$reject_hour_new="reject_hour_new".$last_hour;
			$sql_qcAlterSpotReject.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour_new,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour_new,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour_new,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour_new";
		
			$sql_qcAlterSpotReject.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
					where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in($poIds) and c.status_active=1 and b.status_active=1 $txt_date_cond group by a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, b.job_no, a.item_number_id";
		}
		//echo $sql_qcAlterSpotReject;
		$prod_qnty_data_arr = array();
		$prod_qnty_data_res = sql_select($prod_qnty_data);
		foreach($prod_qnty_data_res as $row)
		{	
			if($row[csf("prod_reso_allo")]==1)
			{			
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}

				$line_name=chop($line_name," , ");
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['total_sewing_input']=$row[csf("total_sewing_input")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['total_sewing_output']=$row[csf("total_sewing_output")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['today_sewing_input']=$row[csf("today_sewing_input")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['today_sewing_output']=$row[csf("today_sewing_output")];
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['total_sewing_input']=$row[csf("total_sewing_input")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['total_sewing_output']=$row[csf("total_sewing_output")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['today_sewing_input']=$row[csf("today_sewing_input")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['today_sewing_output']=$row[csf("today_sewing_output")];
			}
		}

		$totalGood_new=$totalAlter_new=$totalSpot_new=$totalReject_new=0;
		$prodAlterReject_Spot_arr = array();
		$prodAlterReject_Spot_res = sql_select($sql_qcAlterSpotReject);
		foreach($prodAlterReject_Spot_res as $row)
		{
			//total good,alter,reject qnty
			$totalGood_new += $row[csf("good_qnty_new")];
			$totalAlter_new += $row[csf("alter_qnty_new")];
			$totalSpot_new += $row[csf("spot_qnty_new")];
			$totalReject_new += $row[csf("reject_qnty_new")];			

			if($row[csf("prod_reso_allo")]==1)
			{
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}

				$line_name=chop($line_name," , ");

				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["good_qnty_new"]+=$row[csf("good_qnty_new")];
				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["alter_qnty_new"]+=$row[csf("alter_qnty_new")];
				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["spot_qnty_new"]+=$row[csf("spot_qnty_new")];
				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["reject_qnty_new"]+=$row[csf("reject_qnty_new")];
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					$prod_hour_new="prod_hour_new".substr($bg_hour,0,2);
					$alter_hour_new="alter_hour_new".substr($bg_hour,0,2);
					$spot_hour_new="spot_hour_new".substr($bg_hour,0,2);
					$reject_hour_new="reject_hour_new".substr($bg_hour,0,2);
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$prod_hour"]+=$row[csf("$prod_hour_new")];
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$alter_hour"]+=$row[csf("$alter_hour_new")];
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$spot_hour"]+=$row[csf("$spot_hour_new")];
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$reject_hour_new"]+=$row[csf("$reject_hour_new")];
				}
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["good_qnty_new"]+=$row[csf("good_qnty_new")];
				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["alter_qnty_new"]+=$row[csf("alter_qnty_new")];
				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["spot_qnty_new"]+=$row[csf("spot_qnty_new")];
				$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["reject_qnty_new"]+=$row[csf("reject_qnty_new")];
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					//$end=substr(add_time($start_hour_arr[$h],60),0,8);
					$prod_hour_new="prod_hour_new".substr($bg_hour,0,2);
					$alter_hour_new="alter_hour_new".substr($bg_hour,0,2);
					$spot_hour_new="spot_hour_new".substr($bg_hour,0,2);
					$reject_hour_new="reject_hour_new".substr($bg_hour,0,2);
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$prod_hour"]+=$row[csf("$prod_hour_new")];
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$alter_hour"]+=$row[csf("$alter_hour_new")];
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$spot_hour"]+=$row[csf("$spot_hour_new")];
					$prodAlterReject_Spot_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]["$reject_hour"]+=$row[csf("$reject_hour_new")];					
				}
			}	
		}
		unset($prodAlterReject_Spot_res);
		
		$fr_data_arr=array();
		//$txt_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date)));
		$fr_sql="select id, frdate, line, style, description, product_type, order_no, color, plan_qty from fr_import where frdate='$txt_date'";
		//echo $fr_sql; die;
		$fr_sql_res = sql_select($fr_sql);
		foreach($fr_sql_res as $row)
		{
			$ex_job=explode("::",$row[csf("style")]);
			$fr_data_arr[$row[csf("line")]][$ex_job[0]][$row[csf("order_no")]]['isfr']=$row[csf("color")];
		}
		unset($fr_sql_res);
	
	    // echo "<pre>";
        // 	print_r($prod_qnty_data_arr);
        // 	echo "</pre>";
        // 	die;
		$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
			
		$summary_total_parc=($totalGood/$grand_total)*100;
		$summary_total_parcalter=($totalAlter/$grand_total)*100;
		$summary_total_parcspot=($totalSpot/$grand_total)*100;
		$summary_total_parcreject=($totalReject/$grand_total)*100;		
		?>                  

        <div width="100%">
            <table width="100%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                
                <?php   //table header calculation
                $totalGoodQnt=0; $totalAlterQnt=0; $totalSpotQnt=0; $totalRejectQnt=0; $totalGood_qty_new=0;
                $total_hterget = 0; //H terget initial
				//print_r($production_data);die;
				$prod_08=0; $prod_09=0; $prod_10=0; $prod_11=0; $prod_12=0; $prod_13=0; $prod_14=0; $prod_15=0;
				$prod_16=0; $prod_17=0; $prod_18=0; $prod_19=0; $prod_20=0; $prod_21=0; $prod_22=0; $prod_23=0;
                foreach($production_data as $flowre_id=>$value)
                {
					ksort($value);
					foreach($value as $line_name=>$gmts_val)
					{
						foreach($gmts_val as $job_id=>$val)
						{
							foreach($val as $gmts_id => $row)
							{
								$total_hterget += $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];  //h terget calculation
								$man_power = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['man_power']; 
								$hourly_capacity_qty = $operator*60/$total_smv;
								$active_machine_line = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['active_machine'];

								$today_output += $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['today_sewing_output'];

								$totalGood_qty_new += $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['good_qnty_new'];

								$pre_result_capty_utl = ($active_machine_line*60)/$total_smv;

								$hourly_target_efficiency = ($hourly_target/$hourly_capacity_qty)*100;

								$left_total_capacity_qty=$left_total_prod_Effic=$left_total_target_blance=$left_total_hourly_target=$left_total_equivalent_basic_qty=0;

								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
									$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
									$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
									$totalGoodQnt += $row[($prod_hour)];
									$totalAlterQnt += $row[($alter_hour)];
									$totalSpotQnt += $row[($spot_hour)];
									$totalRejectQnt +=$row[($reject_hour)];
									$qc_pass = $row[($prod_hour)];
	
									$hourly_target_blance = $hourly_capacity_qty-$qc_pass;
									$prod_Effic = ($qc_pass/$hourly_capacity_qty)*100;
									$equivalent_basic_qty = ($total_smv/3.5)*$hourly_capacity_qty;

									$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
									$summary_total_parc=($totalGood/$grand_total)*100;
									$summary_total_parcalter=($totalAlter/$grand_total)*100;
									$summary_total_parcspot=($totalSpot/$grand_total)*100;
									$summary_total_parcreject=($totalReject/$grand_total)*100;

									$total_capacity_qty[$prod_hour] += $hourly_capacity_qty; 
									$total_hourly_target_efficiency[$prod_hour] += $hourly_target_efficiency; 
									$total_prod_Effic[$prod_hour] += $prod_Effic; 
									$total_target_blance[$prod_hour] += $hourly_target_blance;
									$total_equivalent_basic_qty[$prod_hour] += $equivalent_basic_qty;

									$left_total_capacity_qty += $hourly_capacity_qty;  
									$left_total_prod_Effic += $prod_Effic; 
									$left_total_target_blance += $hourly_target_blance;
									$left_total_hourly_target += $hourly_target_efficiency;
									$left_total_equivalent_basic_qty += $equivalent_basic_qty;

									$foot_total_capacity_qty += $hourly_capacity_qty;
									$foot_total_prod_Effic += $prod_Effic; 
									$foot_total_target_blance += $hourly_target_blance;
									$foot_total_hourly_target += $hourly_target_efficiency;
									$foot_total_equivalent_basic_qty += $equivalent_basic_qty;

									$total_goods[$prod_hour]+= $row[($prod_hour)];
									$total_alter[$prod_hour]+= $row[($alter_hour)];
									$total_reject[$prod_hour]+= $row[($reject_hour)];
									$total_spot[$prod_hour]+= $row[($spot_hour)]; 
								}

								$prod_08 += $row["prod_hour08"];
								$prod_09 += $row["prod_hour09"];
								$prod_10 += $row["prod_hour10"];
								$prod_11 += $row["prod_hour11"];
								$prod_12 += $row["prod_hour12"];
								$prod_13 += $row["prod_hour13"];
								$prod_14 += $row["prod_hour14"];
								$prod_15 += $row["prod_hour15"];
								$prod_16 += $row["prod_hour16"];
								$prod_17 += $row["prod_hour17"];
								$prod_18 += $row["prod_hour18"];
								$prod_19 += $row["prod_hour19"];
								$prod_20 += $row["prod_hour20"];
								$prod_21 += $row["prod_hour21"];
								$prod_22 += $row["prod_hour22"];
								$prod_23 += $row["prod_hour23"];
							}
						}
					}
				}				
                ?>
                <thead> 	 	 	 	 	 	
                    <tr>
                        <th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">Line</th>
                        <th rowspan="2" width="15%" style="vertical-align:middle; word-break:break-all" align="center">Order Description</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">WIP</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">H.Target</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Optr</th>
                        <th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">SMV</th>
                        <?
                        $cur_prod_count=0;
                        for($k=$hour; $k<=$last_hour; $k++)
                        {
                        	$cur_hour=substr($start_hour_arr[$k],0,2);
                        	$cur_prod = 'prod_'.$cur_hour;
                        	if ($cur_hour >= '18' && $$cur_prod != 0)
							{
								$cur_prod_count++;
							}	
                        }                       
                        
                        for($k=$hour; $k<=$last_hour; $k++)
						{
							$cur_hour=substr($start_hour_arr[$k],0,2);
							if ($cur_hour < '18')
							{	
								?>
	                        	<th width="3%" style="vertical-align:middle; word-break:break-all; <? if (date('H') == $cur_hour) { ?> background-color: #F00; background-image: none; color: #FFF;<? } ?>;"><?  echo substr($start_hour_arr[$k],0,5); ?></th>
								<?
							}
							else
							{
								$cur_prod = 'prod_'.$cur_hour;
								//$width = 18;
								if ($$cur_prod != 0)
								{
									?>
		                        	<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == $cur_hour) { ?> background-color: #F00; background-image: none; color: #FFF;<? } ?>"><?  echo substr($start_hour_arr[$k],0,5); ?></th>
									<?
								}
							}	
						}
						?>											

						<th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Total Prod</th>
						<th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Total QC</th>
						<th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Reject</th>
						<th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">Day Target</th>
                    </tr>
                    <tr>
                    	<th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Input</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Eff%</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Hlpr</th>        
                        
                        <?
                        $percent_cal_arr=array();
                        for($k=$hour; $k<=$last_hour; $k++)
						{
							$cur_hour=substr($start_hour_arr[$k],0,2);
							$cur_prod = 'prod_'.$cur_hour;
							$cur_percent_cal = 'percent_cal_'.$cur_hour;
							if ($cur_hour < '18')
							{						
								?>
								<th width="3%" style="vertical-align:middle; word-break:break-all;">
	                        	<?
		                            $$cur_percent_cal = $$cur_prod*100/$total_hterget;
		                            array_push($percent_cal_arr, $$cur_percent_cal);                   	     
		                        	if ($$cur_prod != 0){
		                                echo $$cur_prod.'/'.(number_format($$cur_percent_cal)).'%';
		                        	} else {
		                        	   echo 0;
		                        	}
	                        	?>
	                        	</th>
								<?
							}
							else 
							{
								$cur_prod = 'prod_'.$cur_hour;
								//$width = 18;
								if ($$cur_prod != 0)
								{							
									?>
									<th width="3%" style="vertical-align:middle; word-break:break-all;">
		                        		<?
			                            $$cur_percent_cal = $$cur_prod*100/$total_hterget;
			                            array_push($percent_cal_arr, $$cur_percent_cal);                   	     
			                        	if ($$cur_prod != 0){
			                                echo $$cur_prod.'/'.(number_format($$cur_percent_cal)).'%';
			                        	} else {
			                        	   echo 0;
			                        	}
		                        		?>
		                        	</th>
									<?
								}	
							}		
						}						                   
                        ?>                        
                       
                        <th width="4%" style="vertical-align:middle; word-break:break-all;">
                        	<?	                        	
	                        	$count = 0;
	                        	$percent_sum = 0;
	                        	foreach ($percent_cal_arr as $value) {
	                    	     	if ($value != 0) {
	                    	     		$count++;
	                    	     		$percent_sum = $percent_sum + $value;
	                    	     	}
	                        	} 
	                            $percent_avg = $percent_sum/$count;
	                            if ($totalGoodQnt != '') {
	                            	echo $totalGoodQnt.'/'.number_format($percent_avg).'%';
	                            } else {
	                            	echo 0;
	                            }		                                               
	                        ?>
                        </th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all;"><? echo $totalGood_qty_new; ?></th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all;">Alter/Spot</th>
                    </tr>
                </thead> 

                <tbody>	
                <?php

                foreach($production_data as $flowre_id=>$value)
                {
					ksort($value);
					foreach($value as $line_name=>$gmts_val)
					{
						foreach($gmts_val as $job_id=>$val)
						{
							foreach($val as $gmts_id => $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								//total good,alter,reject qnty
								$totalGood_qty += $row[("good_qnty")];
								$totalAlter_qty += $row[("alter_qnty")];
								$totalSpot_qty += $row[("spot_qnty")];
								$totalReject_qty += $row[("reject_qnty")];
								$today_input = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['today_sewing_input'];
								$today_output = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['today_sewing_output'];
								$total_input = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['total_sewing_input'];
								$total_output = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['total_sewing_output'];

								$totalGood_qty_new += $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['good_qnty_new'];
								$totalAlter_qty_new += $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['alter_qnty_new'];
								$totalSpot_qty_new += $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['spot_qnty_new'];
								$totalReject_qty_new += $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['reject_qnty_new'];

								$order_number=implode(',',array_unique(explode(",",$row[("po_number")])));
								$grouping=implode(',',array_unique(explode(",",$row[("grouping")])));
								$file_no=implode(',',array_unique(explode(",",$row[("file_no")])));
								
								$is_fr=$fr_data_arr[$line_name][$row["job_no"]][$order_number]['isfr'];
								$frline_tdcolor="";
								
								if($is_fr=="") {
									$frline_tdcolor="#F00";
									$frline_fontcolor="#FFF";
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
									<td width="3%" bgcolor="<? echo $frline_tdcolor; ?>" style="vertical-align:middle; word-break:break-all" align="center"><p style="color: <?php echo $frline_fontcolor; ?>; font-weight: bold;"><? echo $line_name; ?></p></td>
									<td width="15%" style="vertical-align:middle; word-break:break-all" align="center" title='Bname=<?php echo $buyer_short_library[$row[("buyer_name")]];?> Job=<?php echo $row[("job_no_prefix_num")];?> Style=<?php echo $row[("style_ref_no")];?> Order=<?php echo $order_number;?> Item=<?php echo $garments_item[$gmts_id];?>'><p><? echo $buyer_short_library[$row[("buyer_name")]].', '.$row[("job_no_prefix_num")].', '.$row[("style_ref_no")].', '.$order_number.', '.$garments_item[$gmts_id]; ?></p></td>


									<td width="3%" style="vertical-align:middle; word-break:break-all;" align="center" title="<? echo 'Total Input='.$total_input.' and Total Output='.$total_output; ?>"><p>
										<?php
										    $wip = ($total_input - $total_output);
										    if ($wip==0 && $today_input==0) {
										    	echo '';
										    } else {
										    	echo $wip."<br>".$today_input;
										    }
									    ?>
									</p></td>


									<td width="4%" style="vertical-align:middle; word-break:break-all" align="center"><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'].'<br/>'.$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_efficiency'];
									 $total_hterget += $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];  //h terget calculation
									    ?></td>
									
									<td width="3%" style="vertical-align:middle; word-break:break-all" align="center"><p>
										<?php
										$operator = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['operator'];
										$helper = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['helper'];
										if ($operator == '' && $helper == '') {
											echo '';
										} elseif ($operator == '' && $helper != '') {
                                            echo '0'.'<br>'.$helper;
										} elseif ($operator != '' && $helper == '') {
											echo $operator.'<br>'.'0';
										} else {	
											echo $operator.'<br>'.$helper;
										}											
										?></p>
									</td>

									<td width="3%" style="vertical-align:middle; word-break:break-all" align="center"><p>
                                        <?php 
                                            $smv_pcs_string=chop($row[("smv_pcs_set")],",");
										    $smv_string_arr=explode("__",$smv_pcs_string);
										    foreach($smv_string_arr as $gmtsId)
										    {					
											    $smv_arr=explode("_",$gmtsId);
											    if($smv_arr[0] == $gmts_id){
												    echo $total_smv = number_format($smv_arr[2],2);
											    }
										    }  
									    ?></p>
									</td>
								
									<?php
									$man_power = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['man_power']; 
									$hourly_capacity_qty = $operator*60/$total_smv;
									$active_machine_line = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['active_machine'];

									$pre_result_capty_utl = ($active_machine_line*60)/$total_smv;

									$hourly_target_efficiency = ($hourly_target/$hourly_capacity_qty)*100;

									$left_total_capacity_qty=$left_total_prod_Effic=$left_total_target_blance=$left_total_hourly_target=$left_total_equivalent_basic_qty=0;

									$current_production_hour = 0;  // count current production hour
									//echo $hour.'**';
									$cur_prod_count=0;
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
										$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
										$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										$totalGoodQnt += $row[($prod_hour)];
										$totalAlterQnt += $row[($alter_hour)];
										$totalSpotQnt += $row[($spot_hour)];
										$totalRejectQnt +=$row[($reject_hour)];
										$qc_pass = $row[($prod_hour)];
										//$ss +=$qc_pass;

                                        if ($qc_pass != 0){
                                        	$current_production_hour++;
                                        }

										$hourly_target_blance = $hourly_capacity_qty-$qc_pass;
										$prod_Effic = ($qc_pass/$hourly_capacity_qty)*100;
										$equivalent_basic_qty = ($total_smv/3.5)*$hourly_capacity_qty;

										$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
										$summary_total_parc=($totalGood/$grand_total)*100;
										$summary_total_parcalter=($totalAlter/$grand_total)*100;
										$summary_total_parcspot=($totalSpot/$grand_total)*100;
										$summary_total_parcreject=($totalReject/$grand_total)*100;	
										//echo $row["prod_hour09"]."System";
										$cur_hour=substr($start_hour_arr[$k],0,2);
										$cur_prod = 'prod_'.$cur_hour;
										$cur_percent_cal = 'percent_cal_'.$cur_hour;
										if ($cur_hour < '18')
										{
											?>
											<td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
												<?  
											    	if ($row[($prod_hour)] != '0')
											        	echo $row[($prod_hour)];
											    	else echo ''; 
												?>
											</td>
											<?
										}
										else
										{
											$cur_prod = 'prod_'.$cur_hour;
											//$width = 18;
											if ($$cur_prod != 0)
											{										
												?>
												<td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
													<?  
													    if ($row[($prod_hour)] != '0')
													        echo $row[($prod_hour)];
													    else echo ''; 
													?>
												</td>
												<?
											}	
										}	
										//echo $row[($prod_hour)];  
										$total_capacity_qty[$prod_hour] += $hourly_capacity_qty; 
										$total_hourly_target_efficiency[$prod_hour] += $hourly_target_efficiency; 
										$total_prod_Effic[$prod_hour] += $prod_Effic; 
										$total_target_blance[$prod_hour] += $hourly_target_blance;
										$total_equivalent_basic_qty[$prod_hour] += $equivalent_basic_qty;

										$left_total_capacity_qty += $hourly_capacity_qty;  
										$left_total_prod_Effic += $prod_Effic; 
										$left_total_target_blance += $hourly_target_blance;
										$left_total_hourly_target += $hourly_target_efficiency;
										$left_total_equivalent_basic_qty += $equivalent_basic_qty;

										$foot_total_capacity_qty += $hourly_capacity_qty;
										$foot_total_prod_Effic += $prod_Effic; 
										$foot_total_target_blance += $hourly_target_blance;
										$foot_total_hourly_target += $hourly_target_efficiency;
										$foot_total_equivalent_basic_qty += $equivalent_basic_qty;

										$total_goods[$prod_hour]+= $row[($prod_hour)];
										$total_alter[$prod_hour]+= $row[($alter_hour)];
										$total_reject[$prod_hour]+= $row[($reject_hour)];
										$total_spot[$prod_hour]+= $row[($spot_hour)]; 
									}
                                   
									?>
	 	                            <td width="4%" style="vertical-align:middle; word-break:break-all" align="center"><? echo $row[("good_qnty")]; ?></td>
	 	                            <td width="4%" style="vertical-align:middle; word-break:break-all" align="center"><?  echo $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['good_qnty_new']; ?></td>
									
									<td width="4%" style="vertical-align:middle; word-break:break-all" align="center">
								    	<? 
								    	$reject_qnty_new = $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['reject_qnty_new'];
								    	$alter_qnty_new = $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['alter_qnty_new'];
								    	$spot_qnty_new = $prodAlterReject_Spot_arr[$flowre_id][$line_name][$job_id][$gmts_id]['spot_qnty_new'];
                                            if ($reject_qnty_new == 0 && $alter_qnty_new == 0 && $spot_qnty_new == 0)
								    	        echo '';
								    	    else
								    	        echo $reject_qnty_new.'<br/>'.$alter_qnty_new.'/'.$spot_qnty_new;  
								    	?>
									</td>


									<td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
										<? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>
									</td>															
																
								<?php
								//echo $total_goods[$prod_hour]."System";
								$i++;
								$totalinputQnty+=$inputQnty;
								$total_operator+=$operator;
								$total_helper+=$helper;
								$total_Day_Target += $today_active_hour*$hourly_target;

								$totallineachiveper+=$line_achive;
								if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
								{
									$total_working_hour+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['working_hour'];
									$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
									$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
									$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
								}
							}
						}	
					}
            	}
            	//echo $totalGood_qty;

                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
                    
                $summary_total_parc=($totalGood/$grand_total)*100;
                $summary_total_parcalter=($totalAlter/$grand_total)*100;
                $summary_total_parcspot=($totalSpot/$grand_total)*100;
                $summary_total_parcreject=($totalReject/$grand_total)*100;
                ?>
                </tbody>
            </table>
        </div>

    </div><!-- end main div -->

    <?php
	exit();
}

?>