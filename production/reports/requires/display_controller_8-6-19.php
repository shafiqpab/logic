<?php
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 
// header('Content-type:text/html; charset=utf-8');
// require_once('../../../includes/common.php');

// $data=$_REQUEST['data'];
// $action=$_REQUEST['action'];
if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/display_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/display_controller' );load_drop_down( 'requires/display_controller',document.getElementById('cbo_floor').value+'_'+this.value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/display_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 ); 
	
	//echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_hourly_production_report2_controller', this.value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}

if ($action == "eval_multi_select") 
{
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
		 if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
		 if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
	
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
		}
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line", 110,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
	}
	exit();
}

if($action=="report_generate")
{ 	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	
	//echo $txt_job_no;cbo_floor
	$cbo_floor=str_replace("'","",$cbo_floor);
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
	if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
	if($cbo_floor=="") $floor="";else $floor=" and a.floor_id in($cbo_floor)";
	if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";
	if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
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
	if($prod_start_hour=="") $prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
	//echo $hour;die;
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
        <div style="width:100%; font-weight:bold;">
        	Line Wise Hourly Production<br/>
        	Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
        </div>

        <?php
        ob_start();
		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
			
		if($db_type==0)
		{
			if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
			$txt_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date)));
			//echo $txt_date;die;
		    $txt_date_from=" and a.production_date='$txt_date'";

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
				 $sql.="sum(CASE WHEN a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
				        sum(CASE WHEN a.production_hour<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
						sum(CASE WHEN a.production_hour<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
						sum(CASE WHEN a.production_hour<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				else
				{
			    $sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
				       sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
					   sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
					   sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				$first=$first+1;
			}
			$prod_hour="prod_hour".$last_hour;
			$alter_hour="alter_hour".$last_hour;
			$spot_hour="spot_hour".$last_hour;
			$reject_hour="reject_hour".$last_hour;
			$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
				     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
				     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
				     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
		
			$sql.="	from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
					where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $buyer_name $txt_date_from $style_no_cond  $order_no_cond $job_no_cond group by a.prod_reso_allo, a.floor_id, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line"; 
			// echo $sql;die; //$txt_date
		}
		
		if($db_type==2)
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
				 $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
				        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
						sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
						sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				else
				{
			    $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
				       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
					   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
					   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
				}
				$first=$first+1;
			}
			$prod_hour="prod_hour".$last_hour;
			$alter_hour="alter_hour".$last_hour;
			$spot_hour="spot_hour".$last_hour;
			$reject_hour="reject_hour".$last_hour;
			$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
				     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
																
			$sql.=" FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
				 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $buyer_name $txt_date_from $style_no_cond  $order_no_cond $job_no_cond 
				 group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down, a.location, a.floor_id,a.production_date 
				 order by a.floor_id, a.sewing_line"; 	
		}
		// echo $sql;die;   

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
		// ================================= FOR SEWING DATA(TODAY,TOTAL) =====================================================
		if($db_type==0)
		{
			if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
			$txt_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date)));
			$prod_qnty_data = "SELECT a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, c.job_no,a.item_number_id, 
			sum(case when a.production_type=4 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_input, 
			sum(case when a.production_type=5 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_output, 
			sum(case when a.production_type=4 and a.production_date='$txt_date' then a.production_quantity else 0 END) as today_sewing_input 
			FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
			WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.po_break_down_id in($poIds)
			GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id,c.job_no,a.item_number_id";

		}else{
			$prod_qnty_data = "SELECT a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, c.job_no,a.item_number_id, sum(case when a.production_type=4 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_input, sum(case when a.production_type=5 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as total_sewing_output, sum(case when a.production_type=4 and a.production_date='$txt_date' then a.production_quantity else 0 END) as today_sewing_input 
			FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
			WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.po_break_down_id in($poIds)
			GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id,c.job_no,a.item_number_id";
		}
		//echo $prod_qnty_data;
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
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['total_sewing_input']=$row[csf("total_sewing_input")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['total_sewing_output']=$row[csf("total_sewing_output")];
				$prod_qnty_data_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]]['today_sewing_input']=$row[csf("today_sewing_input")];
				
			}

		}

		$fr_data_arr=array();
		$txt_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date)));
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
                $totalGoodQnt=0; $totalAlterQnt=0; $totalSpotQnt=0; $totalRejectQnt=0; 
                $total_hterget = 0; //H terget initial
				//print_r($production_data);die;
				$prod_09=0; $prod_10=0; $prod_11=0; $prod_12=0; $prod_13=0; $prod_14=0; $prod_15=0; $prod_16=0;
				$prod_17=0; $prod_18=0; $prod_19=0; $prod_20=0; $prod_21=0; $prod_22=0; $prod_23=0; $prod_24=0;
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

								$pre_result_capty_utl = ($active_machine_line*60)/$total_smv;

								$hourly_target_efficiency = ($hourly_target/$hourly_capacity_qty)*100;

								$left_total_capacity_qty=$left_total_prod_Effic=$left_total_target_blance=$left_total_hourly_target=$left_total_equivalent_basic_qty=0;

								$current_production_hour = 0;  // count current production hour

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

								$prod_09 += $row["prod_hour08"];
								$prod_10 += $row["prod_hour09"];
								$prod_11 += $row["prod_hour10"];
								$prod_12 += $row["prod_hour11"];
								$prod_13 += $row["prod_hour12"];
								$prod_14 += $row["prod_hour13"];
								$prod_15 += $row["prod_hour14"];
								$prod_16 += $row["prod_hour15"];
								$prod_17 += $row["prod_hour16"];
								$prod_18 += $row["prod_hour17"];
								$prod_19 += $row["prod_hour18"];
								$prod_20 += $row["prod_hour19"];
								$prod_21 += $row["prod_hour20"];
								$prod_22 += $row["prod_hour21"];
								$prod_23 += $row["prod_hour22"];
								$prod_24 += $row["prod_hour23"];
							}
						}
					}
				}				
                ?>
                <thead> 	 	 	 	 	 	
                    <tr>
                        <th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">Line</th>
                        <th rowspan="2" width="19%" style="vertical-align:middle; word-break:break-all" align="center">Order Description</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">WIP</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">H.Target</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Optr</th>
                        <th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">SMV</th>
						<th width="3%"   style="vertical-align:middle; word-break:break-all; <?php if (date('H') == '09') { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">9.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 10) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">10.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 11) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">11.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 12) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">12.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 13) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">13.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 14) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">14.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 15) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">15.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 16) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">16.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 17) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">17.00</th>						
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 18) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">18.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 19) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">19.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 20) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">20.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 21) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">21.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 22) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">22.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all; <?php if (date('H') == 23) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>" align="center">23.00</th>
						<th width="3%" style="vertical-align:middle; word-break:break-all" align="center">23.59</th>

						<th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Total QC</th>
						<th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Reject</th>
						<th rowspan="2" width="3%" style="vertical-align:middle;" align="center">Day Target</th>
						<th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">Current Achv %</th>
						<th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">Capty Utl %</th>

                    </tr>
                    <tr>
                    	<th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Input</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Eff%</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Hlpr</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
                                $percent_cal_09 = $prod_09*100/$total_hterget;                        	     
                        	    if ($prod_09 != 0){
                                    echo $prod_09.'/'.'<br>'.number_format($percent_cal_09).'%';
                        	    } else {
                        	    	echo 0;
                        	    }
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
                                $percent_cal_10 = $prod_10*100/$total_hterget;                        	     
                        	    if ($prod_10 != 0){
                                    echo $prod_10.'/'.'<br>'.number_format($percent_cal_10).'%';
                        	    } else {
                        	    	echo 0;
                        	    }
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_11 = $prod_11*100/$total_hterget;                        	     
	                        	if ($prod_11 != 0){
	                                echo $prod_11.'/'.'<br>'.number_format($percent_cal_11).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_12 = $prod_12*100/$total_hterget;                        	     
	                        	if ($prod_12 != 0){
	                                echo $prod_12.'/'.'<br>'.number_format($percent_cal_12).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_13 = $prod_13*100/$total_hterget;                        	     
	                        	if ($prod_13 != 0){
	                                echo $prod_13.'/'.'<br>'.number_format($percent_cal_13).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_14 = $prod_14*100/$total_hterget;                        	     
	                        	if ($prod_14 != 0){
	                                echo $prod_14.'/'.'<br>'.number_format($percent_cal_14).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_15 = $prod_15*100/$total_hterget;                        	     
	                        	if ($prod_15 != 0){
	                                echo $prod_15.'/'.'<br>'.number_format($percent_cal_15).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_16 = $prod_16*100/$total_hterget;                        	     
	                        	if ($prod_16 != 0){
	                                echo $prod_16.'/'.'<br>'.number_format($percent_cal_16).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_17 = $prod_17*100/$total_hterget;                        	     
	                        	if ($prod_17 != 0){
	                                echo $prod_17.'/'.'<br>'.number_format($percent_cal_17).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_18 = $prod_18*100/$total_hterget;                        	     
	                        	if ($prod_18 != 0){
	                                echo $prod_18.'/'.'<br>'.number_format($percent_cal_18).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_19 = $prod_19*100/$total_hterget;                        	     
	                        	if ($prod_19 != 0){
	                                echo $prod_19.'/'.'<br>'.number_format($percent_cal_19).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_20 = $prod_20*100/$total_hterget;                        	     
	                        	if ($prod_20 != 0){
	                                echo $prod_20.'/'.'<br>'.number_format($percent_cal_20).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_21 = $prod_21*100/$total_hterget;                        	     
	                        	if ($prod_21 != 0){
	                                echo $prod_21.'/'.'<br>'.number_format($percent_cal_21).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_22 = $prod_22*100/$total_hterget;                        	     
	                        	if ($prod_22 != 0){
	                                echo $prod_22.'/'.'<br>'.number_format($percent_cal_22).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_23 = $prod_23*100/$total_hterget;                        	     
	                        	if ($prod_23 != 0){
	                                echo $prod_23.'/'.'<br>'.number_format($percent_cal_23).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all;">
                        	<?php 
	                            $percent_cal_24 = $prod_24*100/$total_hterget;                        	     
	                        	if ($prod_24 != 0){
	                                echo $prod_24.'/'.'<br>'.number_format($percent_cal_24).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        </th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all;">
                        	<?php
	                        	$percent_arr = array($percent_cal_09, $percent_cal_10, $percent_cal_11, $percent_cal_12, $percent_cal_13, $percent_cal_14, $percent_cal_15, $percent_cal_16, $percent_cal_17, $percent_cal_18, $percent_cal_19, $percent_cal_20, $percent_cal_21, $percent_cal_22, $percent_cal_23, $percent_cal_24);
	                        	$count = 0;
	                        	$percent_sum = 0;
	                        	foreach ($percent_arr as $value) {
	                    	     	if ($value != 0) {
	                    	     		$count++;
	                    	     		$percent_sum = $percent_sum + $value;
	                    	     	}
	                        	} 
	                            $percent_avg = $percent_sum/$count;
	                            if ($totalGoodQnt != '') {
	                            	echo $totalGoodQnt.'/'.'<br>'.number_format($percent_avg).'%';
	                            } else {
	                            	echo 0;
	                            }
		                                               
	                        ?>
                        </th>
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
								$total_input = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['total_sewing_input'];
								$total_output = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['total_sewing_output'];

								$order_number=implode(',',array_unique(explode(",",$row[("po_number")])));
								$grouping=implode(',',array_unique(explode(",",$row[("grouping")])));
								$file_no=implode(',',array_unique(explode(",",$row[("file_no")])));

								$is_fr=$fr_data_arr[$line_name][$row["job_no"]][$order_number]['isfr'];
								$frline_tdcolor="";
								if($is_fr=="") $frline_tdcolor="#F00";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
									<td width="3%" bgcolor="<? echo $frline_tdcolor; ?>" style="vertical-align:middle; word-break:break-all" align="center"><p><? echo $line_name; ?></p></td>
									<td width="19%" style="vertical-align:middle; word-break:break-all" align="center" title='Bname=<?php echo $buyer_short_library[$row[("buyer_name")]];?> Job=<?php echo $row[("job_no_prefix_num")];?> Style=<?php echo $row[("style_ref_no")];?> Order=<?php echo $order_number;?> Item=<?php echo $garments_item[$gmts_id];?>'><p><? echo $buyer_short_library[$row[("buyer_name")]].', '.$row[("job_no_prefix_num")].', '.$row[("style_ref_no")].', '.$order_number.', '.$garments_item[$gmts_id]; ?></p></td>


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
									 //$total_hterget += $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];  //h terget calculation
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

										?>

										<td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
											<?php  
											    if ($row[($prod_hour)] != '0')
											        echo $row[($prod_hour)];
											    else 
											        echo ''; 
											?>
										</td>

										<?php
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
									
									<td width="4%" style="vertical-align:middle; word-break:break-all" align="center">
								    	<? 
                                            if ($row[("reject_qnty")] == '0' && $row[("alter_qnty")] == '0' && $row[("spot_qnty")] == '0')
								    	        echo '';
								    	    else
								    	        echo $row[("reject_qnty")].'<br/>'.$row[("alter_qnty")].'/'.$row[("spot_qnty")];    
								    	?>
									</td>


									<td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
										<? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>
									</td>
									<td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
										<? $line_achive=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']*100;
										echo number_format($line_achive).'%'; ?>
									</td>
									<td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
										<?php
										    $mid_result_capty_utl = $pre_result_capty_utl*$current_production_hour;
										    $capty_utl = $row[("good_qnty")]*100/$mid_result_capty_utl;
										    echo number_format($capty_utl).'%'; 
										?></td>								
																
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
    foreach (glob($user_id."_*.xls") as $filename)
    {       
        @unlink($filename);
    }
    $name=$user_id."_".time().".xls";
    $create_new_excel = fopen($name, 'w');  
    $is_created = fwrite($create_new_excel,ob_get_contents());
    //$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
    echo "####".$name;
    exit();
}

    ?>