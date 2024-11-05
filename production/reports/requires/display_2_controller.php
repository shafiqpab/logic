<?php
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/display_2_controller', this.value, 'load_drop_down_floor', 'floor_td' ); get_php_form_data( this.value, 'eval_multi_select', 'requires/display_2_controller' );",0 );   	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );	 
	exit();
}

if ($action == "eval_multi_select") 
{
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,'0');getFloorId();') ,3000)];\n";
    echo '$(function()
    {
        $("#floor_td a").click(function()
        {           
            getFloorId();
        });
    })';
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
	$garments_item=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');

	if($db_type == 2)
	{
		$prod_reso_arr = return_library_array( "SELECT a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type == 0)
	{
		$prod_reso_arr = return_library_array( "SELECT a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}	

	$cbo_floor=str_replace("'","",$cbo_floor);
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
	if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
	if($cbo_floor=="") $floor="";else $floor=" and a.floor_id in($cbo_floor)";
	if(str_replace("'","",$cbo_line)==0) $line="";else $line=" and a.sewing_line=$cbo_line";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
	if(str_replace("'","",trim($txt_job_no))=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num=$txt_job_no";
	if(str_replace("'","",trim($txt_order_no))=="") $order_no_cond=""; else $order_no_cond=" and c.po_number=$txt_order_no";

    if(str_replace("'","",$cbo_location)==0) $loc=""; else $loc=" and a.location_id=$cbo_location";
	if($cbo_floor=="") $flr="";else $flr=" and a.floor_id in($cbo_floor)";
	if(str_replace("'","",$cbo_line)==0) $lin=""; else $lin=" and a.id=$cbo_line";

	$prod_resource_array = array();	
    $prod_resource_query = "SELECT a.id, a.location_id, a.floor_id, a.line_number, b.pr_date, b.target_per_hour, b.working_hour, b.man_power, b.style_ref_id, b.active_machine, b.operator, b.helper, c.target_efficiency from prod_resource_mst a,  prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_name $loc $flr $lin and b.pr_date=$txt_date";
    // echo $prod_resource_query;die;
  	$prod_resource_result = sql_select($prod_resource_query);
	foreach($prod_resource_result as $row)
	{
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['location_id']=$row[csf('location_id')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['line_number']=$row[csf('line_number')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['pr_date']=change_date_format($row[csf('pr_date')]);
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['working_hour']=$row[csf('working_hour')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['active_machine']=$row[csf('active_machine')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['target_efficiency']=$row[csf('target_efficiency')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['operator']=$row[csf('operator')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['helper']=$row[csf('helper')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['man_power']=$row[csf('man_power')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['target_per_hour']=$row[csf('target_per_hour')];
		$prod_resource_array[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
	}
	//echo '<pre>';print_r($prod_resource_array);
	?>
	<div style="text-align: center; color: red; font-size: 20px;">
		<?php 
		    if (count($prod_resource_result) < 1) {
		        echo "Data Not Found !!";
		        die;
	        }
	    ?>
	</div>
	<?
	$start_time_arr=array();
	if($db_type==0)
	{
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

	$lineWiseProd_arr=array(); 
	$prod_arr=array(); 
	$start_hour_arr=array();
	
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{		
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
    $start_hour_arr[$j+1]='23:59';
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.ver_align_mid{vertical-align: middle;}
	</style>
    <div style="width:100%">
        <div style="width:100%; font-weight:bold;">
        	Line Wise Hourly Production<br/>
        	Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
        </div>
        <?
        ob_start();
		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		if($db_type==0)
		{
			if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
			$txt_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date)));
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
		}		
		else if($db_type==2)
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
				where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $buyer_name $location $floor $line $txt_date_from $style_no_cond  $order_no_cond $job_no_cond 
				group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down, a.location, a.floor_id,a.production_date 
				order by a.floor_id, a.sewing_line"; 	
		}
	    // echo $sql;
		$result = sql_select($sql);
		$totalGood=0;$totalAlter=0;$totalSpot=0;$totalReject=0;$totalinputQnty=0;
		$production_data=array();
		$poId = "";
		foreach($result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$order_number = implode(',',array_unique(explode(",",$row[csf("po_break_down_id")])));
			//echo $order_number.'system';
			$poId .= $order_number.",";
			$totalGood += $row[csf("good_qnty")];
			$totalAlter += $row[csf("alter_qnty")];
			$totalSpot += $row[csf("spot_qnty")];
			$totalReject += $row[csf("reject_qnty")];
				
			if($row[csf("prod_reso_allo")]==1)
			{
				//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
				$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name = "";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= $lineArr[$resource_id].", ";
				}
				$line_name = chop($line_name," , ");

				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["company_id"]=$row[csf("company_id")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["location"]=$row[csf("location")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["floor_id"]=$row[csf("floor_id")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["production_date"]=$row[csf("production_date")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["sewing_line"]=$line_name;
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["job_no"]=$row[csf("job_no")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["buyer_name"]=$row[csf("buyer_name")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["item_number_id"].=$row[csf("item_number_id")].",";
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["smv_pcs_set"]=$row[csf("smv_pcs_set")].",";
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["po_break_down_id"]=$row[csf("po_break_down_id")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["grouping"]=$row[csf("grouping")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["file_no"]=$row[csf("file_no")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["po_number"]=$row[csf("po_number")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["supervisor"]=$row[csf("supervisor")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["good_qnty"]+=$row[csf("good_qnty")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["alter_qnty"]+=$row[csf("alter_qnty")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["spot_qnty"]+=$row[csf("spot_qnty")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["reject_qnty"]+=$row[csf("reject_qnty")];
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					$prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["$prod_hour"]+=$row[csf("$prod_hour")];
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["$alter_hour"]+=$row[csf("$alter_hour")];
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["$spot_hour"]+=$row[csf("$spot_hour")];
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["$reject_hour"]+=$row[csf("$reject_hour")];
				}
			}
			else
			{
				//echo 'system';
				//$line_id = $row[csf('sewing_line')];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["company_id"]=$row[csf("company_id")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["location"]=$row[csf("location")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["floor_id"]=$row[csf("floor_id")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["production_date"]=$row[csf("production_date")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["sewing_line"]=$row[csf("sewing_line")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["job_no"]=$row[csf("job_no")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["buyer_name"]=$row[csf("buyer_name")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["item_number_id"].=$row[csf("item_number_id")].",";
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["smv_pcs_set"]=$row[csf("smv_pcs_set")].",";
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["po_break_down_id"]=$row[csf("po_break_down_id")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["grouping"]=$row[csf("grouping")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["file_no"]=$row[csf("file_no")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["po_number"]=$row[csf("po_number")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["supervisor"]=$row[csf("supervisor")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["good_qnty"]+=$row[csf("good_qnty")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["alter_qnty"]+=$row[csf("alter_qnty")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["spot_qnty"]+=$row[csf("spot_qnty")];
				$production_data[$row[csf("location")]][$row[csf("floor_id")]][$row[csf('sewing_line')]]["reject_qnty"]+=$row[csf("reject_qnty")];
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					$prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$line_id]["$prod_hour"]+=$row[csf("$prod_hour")];
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$line_id]["$alter_hour"]+=$row[csf("$alter_hour")];
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$line_id]["$spot_hour"]+=$row[csf("$spot_hour")];
					$production_data[$row[csf("location")]][$row[csf("floor_id")]][$line_id]["$reject_hour"]+=$row[csf("$reject_hour")];
				}
			}	
		}
		ksort($production_data);
		$poIds = chop($poId,',');

		// ====================FOR SEWING DATA(TODAY,TOTAL) =================
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
		}
		else
		{
			if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
			$txt_date = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date)));
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
				$line_resource_mst_arr = explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
				$line_name = "";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= $lineArr[$resource_id].", ";
				}
				$line_name = chop($line_name," , ");
				$prod_qnty_data_arr[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['total_sewing_input']=$row[csf("total_sewing_input")];
				$prod_qnty_data_arr[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['total_sewing_output']=$row[csf("total_sewing_output")];
				$prod_qnty_data_arr[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['today_sewing_input']=$row[csf("today_sewing_input")];
			}
			else
			{
				//$line_id = $row[csf('sewing_line')];
				$prod_qnty_data_arr[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['total_sewing_input']=$row[csf("total_sewing_input")];
				$prod_qnty_data_arr[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['total_sewing_output']=$row[csf("total_sewing_output")];
				$prod_qnty_data_arr[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['today_sewing_input']=$row[csf("today_sewing_input")];				
			}			
		}

		$fr_data_arr=array();
		$fr_sql="SELECT id, frdate, line, style, description, product_type, order_no, color, plan_qty from fr_import where frdate='$txt_date'";
		//echo $fr_sql; die;
		$fr_sql_res = sql_select($fr_sql);
		foreach($fr_sql_res as $row)
		{
			$ex_job=explode("::",$row[csf("style")]);
			$fr_data_arr[$row[csf("line")]][$ex_job[0]][$row[csf("order_no")]]['isfr']=$row[csf("color")];
		}
		unset($fr_sql_res);
	
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
				$prod_8=0; $prod_9=0; $prod_10=0; $prod_11=0; $prod_12=0; $prod_13=0; $prod_14=0; $prod_15=0; $prod_16=0;
				$prod_17=0; $prod_18=0; $prod_19=0; $prod_20=0; $prod_21=0; $prod_22=0; $prod_23=0;
                foreach($prod_resource_array as $loc_id=>$loc_val)
            	{
					foreach($loc_val as $floor_id=>$floor_val)
					{
						foreach($floor_val as $line_id=>$row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$good_qnty  = $production_data[$loc_id][$floor_id][$line_id]["good_qnty"];
							$alter_qnty = $production_data[$loc_id][$floor_id][$line_id]["alter_qnty"];
							$spot_qnty  = $production_data[$loc_id][$floor_id][$line_id]["spot_qnty"];
							$reject_qnty= $production_data[$loc_id][$floor_id][$line_id]["reject_qnty"];	
							//total good,alter,reject qnty
							$totalGood_qty  += $good_qnty;
							$totalAlter_qty += $alter_qnty;
							$totalSpot_qty  += $spot_qnty;
							$totalReject_qty+= $reject_qnty;		

							$today_input  = $prod_qnty_data_arr[$loc_id][$floor_id][$line_id]['today_sewing_input'];
							$total_input  = $prod_qnty_data_arr[$loc_id][$floor_id][$line_id]['total_sewing_input'];
							$total_output = $prod_qnty_data_arr[$loc_id][$floor_id][$line_id]['total_sewing_output'];

							$po_number        = $production_data[$loc_id][$floor_id][$line_id]["po_number"];
							$grouping         = $production_data[$loc_id][$floor_id][$line_id]["grouping"];
							$file_no          = $production_data[$loc_id][$floor_id][$line_id]["file_no"];
							$buyer_name       = $production_data[$loc_id][$floor_id][$line_id]["buyer_name"];
							$job_no_prefix_num= $production_data[$loc_id][$floor_id][$line_id]["job_no_prefix_num"];
							$style_ref_no     = $production_data[$loc_id][$floor_id][$line_id]["style_ref_no"];
							$item_number_id   = $production_data[$loc_id][$floor_id][$line_id]["item_number_id"];
							$sewing_line      = $production_data[$loc_id][$floor_id][$line_id]["sewing_line"];
							$prod_reso_allo      = $production_data[$loc_id][$floor_id][$line_id]["prod_reso_allo"];

							$order_number=implode(',',array_unique(explode(",",$po_number)));
							$grouping=implode(',',array_unique(explode(",",$grouping)));
							$file_no=implode(',',array_unique(explode(",",$file_no)));

							$total_hterget += $row['target_per_hour'];
							$man_power = $row['man_power']; 
							$hourly_capacity_qty = $operator*60/$total_smv;
							$active_machine_line = $row['active_machine'];

							$pre_result_capty_utl = ($active_machine_line*60)/$total_smv;

							$hourly_target_efficiency = ($hourly_target/$hourly_capacity_qty)*100;

							$left_total_capacity_qty=$left_total_prod_Effic=$left_total_target_blance=$left_total_hourly_target=$left_total_equivalent_basic_qty=0;

							$current_production_hour = 0;  // count current production hour

							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour  ="prod_hour".substr($start_hour_arr[$k],0,2)."";
								$alter_hour ="alter_hour".substr($start_hour_arr[$k],0,2)."";
								$spot_hour  ="spot_hour".substr($start_hour_arr[$k],0,2)."";
								$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
								$totalGoodQnt  += $production_data[$loc_id][$floor_id][$line_id][$prod_hour];
								$totalAlterQnt += $production_data[$loc_id][$floor_id][$line_id][$alter_hour];
								$totalSpotQnt  += $production_data[$loc_id][$floor_id][$line_id][$spot_hour];
								$totalRejectQnt+= $production_data[$loc_id][$floor_id][$line_id][$reject_hour];
								$qc_pass = $production_data[$loc_id][$floor_id][$line_id][$prod_hour];
								//$ss +=$qc_pass;
								$prod08  += $production_data[$loc_id][$floor_id][$line_id][$prod_hour];

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

								$total_goods[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$prod_hour];
								$total_alter[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$alter_hour];
								$total_reject[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$reject_hour];
								$total_spot[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$spot_hour]; 
							}
							$prod_8 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour08"];
							$prod_9 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour09"];
							$prod_10 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour10"];
							$prod_11 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour11"];
							$prod_12 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour12"];
							$prod_13 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour13"];
							$prod_14 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour14"];
							$prod_15 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour15"];
							$prod_16 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour16"];
							$prod_17 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour17"];
							$prod_18 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour18"];
							$prod_19 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour19"];
							$prod_20 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour20"];
							$prod_21 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour21"];
							$prod_22 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour22"];
							$prod_23 += $production_data[$loc_id][$floor_id][$line_id]["prod_hour23"];						
						}
					}
				}	
                ?>
                <thead> 	 	 	 	 	 	
                    <tr>
                        <th width="3%" class="wrd_brk ver_align_mid center" rowspan="2">Line</th>
                        <th width="19%" class="wrd_brk ver_align_mid center" rowspan="2">Order Description</th>
                        <th width="3%" class="wrd_brk ver_align_mid center">WIP</th>
                        <th width="4%" class="wrd_brk ver_align_mid center">H.Target</th>
                        <th width="3%" class="wrd_brk ver_align_mid center">Optr</th>
                        <th width="3%" class="wrd_brk ver_align_mid center" rowspan="2">SMV</th>
                        <?
                        for($k=$hour; $k<=$last_hour; $k++)
						{ //echo $hour;
							$cur_hour=substr($start_hour_arr[$k],0,2); 
							?>
	                        <th width="3%" class="wrd_brk ver_align_mid center" style="<?php if (date('H') == $cur_hour) { ?> background-color: #F00; background-image: none; color: #FFF;<?php } ?>"><?  echo substr($start_hour_arr[$k],0,5); ?></th>
							<?	
						}
						?>
						<th width="4%" class="wrd_brk ver_align_mid center">Total QC</th>
						<th width="4%" class="wrd_brk ver_align_mid center">Reject</th>
						<th width="3%" class="wrd_brk ver_align_mid center" rowspan="2">Day Target</th>
						<th width="3%" class="wrd_brk ver_align_mid center" rowspan="2">Current Achv %</th>
						<th width="3%" class="wrd_brk ver_align_mid center" rowspan="2">Capty Utl %</th>
                    </tr>
                    <tr>
                    	<th width="3%" class="wrd_brk ver_align_mid center">Input</th>
                        <th width="4%" class="wrd_brk ver_align_mid center">Eff%</th>
                        <th width="3%" class="wrd_brk ver_align_mid center">Hlpr</th>
						<?
							$total_qty = 0;
							$counter = 0;
						    for($k=$hour; $k<=$last_hour; $k++)
							{
								?>
								<th width="3%" class="wrd_brk ver_align_mid">
									<? 
									$prod_val = "prod_".$k; 
									$percent_cal = $$prod_val*100/$total_hterget;      	     
	                        	    if ($$prod_val != 0)
	                        	    {	                                    
	                                    $total_qty += $percent_cal;
	                                    $counter++;
	                                    echo $$prod_val.'/'.'<br>'.number_format($percent_cal).'%';
	                        	    } 
	                        	    else 
	                        	    {
	                        	    	echo 0;
	                        	    }
	                        	    ?>
								</th>
								<?
							}
						?>
                        
                        <th width="4%" class="wrd_brk ver_align_mid">
                        	<?	                        	
	                            $percent_avg = $total_qty/$counter;
	                            if ($totalGoodQnt != '') {
	                            	echo $totalGoodQnt.'/'.'<br>'.number_format($percent_avg).'%';
	                            } else {
	                            	echo 0;
	                            }		                                               
	                        ?>
                        </th>
                        <th width="4%" class="wrd_brk ver_align_mid">Alter/Spot</th>
                    </tr>
                </thead>


                <tbody>	
	                <?php
	                // echo '<pre>';print_r($prod_resource_array);	                
	                foreach($prod_resource_array as $loc_id=>$loc_val)
                	{
                		//ksort($loc_val);
						foreach($loc_val as $floor_id=>$floor_val)
						{
							ksort($floor_val);
							foreach($floor_val as $line_id=>$row)
							{

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$good_qnty  = $production_data[$loc_id][$floor_id][$line_id]["good_qnty"];
								$alter_qnty = $production_data[$loc_id][$floor_id][$line_id]["alter_qnty"];
								$spot_qnty  = $production_data[$loc_id][$floor_id][$line_id]["spot_qnty"];
								$reject_qnty= $production_data[$loc_id][$floor_id][$line_id]["reject_qnty"];	
								//total good,alter,reject qnty
								$totalGood_qty  += $good_qnty;
								$totalAlter_qty += $alter_qnty;
								$totalSpot_qty  += $spot_qnty;
								$totalReject_qty+= $reject_qnty;		

								$today_input  = $prod_qnty_data_arr[$loc_id][$floor_id][$line_id]['today_sewing_input'];
								$total_input  = $prod_qnty_data_arr[$loc_id][$floor_id][$line_id]['total_sewing_input'];
								$total_output = $prod_qnty_data_arr[$loc_id][$floor_id][$line_id]['total_sewing_output'];

								$po_number        = $production_data[$loc_id][$floor_id][$line_id]["po_number"];
								$grouping         = $production_data[$loc_id][$floor_id][$line_id]["grouping"];
								$file_no          = $production_data[$loc_id][$floor_id][$line_id]["file_no"];
								$buyer_name       = $production_data[$loc_id][$floor_id][$line_id]["buyer_name"];
								$job_no_prefix_num= $production_data[$loc_id][$floor_id][$line_id]["job_no_prefix_num"];
								$style_ref_no     = $production_data[$loc_id][$floor_id][$line_id]["style_ref_no"];
								$item_number_id   = $production_data[$loc_id][$floor_id][$line_id]["item_number_id"];
								$sewing_line      = $production_data[$loc_id][$floor_id][$line_id]["sewing_line"];
								$prod_reso_allo   = $production_data[$loc_id][$floor_id][$line_id]["prod_reso_allo"];
								$smv_pcs_set      = $production_data[$loc_id][$floor_id][$line_id]["smv_pcs_set"];

								$order_number=implode(',',array_unique(explode(",",$po_number)));
								$grouping=implode(',',array_unique(explode(",",$grouping)));
								$file_no=implode(',',array_unique(explode(",",$file_no)));

								$item_number_id = chop($item_number_id,',');
								$item_arr = explode(",",$item_number_id);								
								if (count($item_arr) > 1)
								{
									foreach ($item_arr as $value) 
									{
										$item_name .= $garments_item[$value].',';
									}
									$item_name = chop($item_name,',');
								}
								else
								{									
									$item_name = $garments_item[$item_number_id];
								}
								
								//echo $item_number_id.'**';
								//$is_fr=$fr_data_arr[$line_name][$row["job_no"]][$order_number]['isfr'];
								//$frline_tdcolor="";
								//if($is_fr=="") $frline_tdcolor="#F00";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
									<td width="3%" class="wrd_brk ver_align_mid center" bgcolor="<? echo $frline_tdcolor; ?>">
										<? 
										if ($prod_reso_allo == 1) 
											echo $sewing_line; 
										else 
											echo $lineArr[$row['line_number']]; 
										?>											
									</td>
									<td width="19%" class="wrd_brk ver_align_mid center" title='Bname=<?php echo $buyer_short_library[$buyer_name];?> Job=<?php echo $job_no_prefix_num;?> Style=<?php echo $style_ref_no;?> Order=<?php echo $order_number;?> Item=<?php echo $item_name;?>'>
										<?
										if ($order_number !='')
										{
											echo $buyer_short_library[$buyer_name].', '.$job_no_prefix_num.', '.$style_ref_no.', '.$order_number.', '.$item_name;
										}
										else echo '';
										?>											
									</td>
									<td width="3%" class="wrd_brk ver_align_mid center" title="<? echo 'Total Input='.$total_input.' and Total Output='.$total_output.' and Today Input='.$today_input; ?>">
										<?
										    $wip = ($total_input - $total_output);
										    if ($wip==0 && $today_input==0) {
										    	echo '';
										    } else {
										    	echo $wip."<br>".$today_input;
										    }
									    ?>
									</td>
									<td width="4%" class="wrd_brk ver_align_mid center">
										<? echo $row['target_per_hour'].'<br/>'.$row['target_efficiency']; ?>				
									</td>
									
									<td width="3%" class="wrd_brk ver_align_mid center">
										<?
										$operator = $row['operator'];
										$helper = $row['helper'];
										if ($operator == '' && $helper == '') {
											echo '';
										} elseif ($operator == '' && $helper != '') {
                                            echo '0'.'<br>'.$helper;
										} elseif ($operator != '' && $helper == '') {
											echo $operator.'<br>'.'0';
										} else {	
											echo $operator.'<br>'.$helper;
										}											
										?>
									</td>
									<td width="3%" class="wrd_brk ver_align_mid center">
                                        <?
                                            $smv_pcs_string=chop($smv_pcs_set,",");
										    $smv_string_arr=explode("__",$smv_pcs_string);
										    foreach($smv_string_arr as $gmtsId)
										    {					
											    $smv_arr=explode("_",$gmtsId);											    
											    if($smv_arr[0] == $item_number_id){
												    echo $total_smv = number_format($smv_arr[2],2);
											    }
										    }  
									    ?>
									</td>									
									<?
									$man_power = $row['man_power']; 
									$hourly_capacity_qty = $operator*60/$total_smv;
									$active_machine_line = $row['active_machine'];
									$pre_result_capty_utl = ($active_machine_line*60)/$total_smv;
									$hourly_target_efficiency = ($hourly_target/$hourly_capacity_qty)*100;
									$left_total_capacity_qty=$left_total_prod_Effic=$left_total_target_blance=$left_total_hourly_target=$left_total_equivalent_basic_qty=0;

									$current_production_hour = 0;  // count current production hour

									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour  ="prod_hour".substr($start_hour_arr[$k],0,2)."";
										$alter_hour ="alter_hour".substr($start_hour_arr[$k],0,2)."";
										$spot_hour  ="spot_hour".substr($start_hour_arr[$k],0,2)."";
										$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										$totalGoodQnt  += $production_data[$loc_id][$floor_id][$line_id][$prod_hour];
										$totalAlterQnt += $production_data[$loc_id][$floor_id][$line_id][$alter_hour];
										$totalSpotQnt  += $production_data[$loc_id][$floor_id][$line_id][$spot_hour];
										$totalRejectQnt+= $production_data[$loc_id][$floor_id][$line_id][$reject_hour];
										$qc_pass = $production_data[$loc_id][$floor_id][$line_id][$prod_hour];

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
										?>
										<td width="3%" class="wrd_brk ver_align_mid center">
											<? 
										    if ($production_data[$loc_id][$floor_id][$line_id][$prod_hour] != '0')
										        echo $production_data[$loc_id][$floor_id][$line_id][$prod_hour];
										    else 
										        echo ''; 
											?>
										</td>
										<?
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

										$total_goods[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$prod_hour];
										$total_alter[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$alter_hour];
										$total_reject[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$reject_hour];
										$total_spot[$prod_hour]+= $production_data[$loc_id][$floor_id][$line_id][$spot_hour]; 
									}

									?>
	 	                            <td width="4%" class="wrd_brk ver_align_mid center">
	 	                            	<? if ($good_qnty != '') echo $good_qnty; else echo ''; ?>                		
	 	                            </td>
	 	                            <td width="4%" class="wrd_brk ver_align_mid center">
								    	<? 
                                        if (($reject_qnty == '0' || $reject_qnty == '') && ($alter_qnty == '0' || $alter_qnty == '') && ($spot_qnty == '0' || $spot_qnty == ''))
							    	        echo '';
							    	    else 
							    	        echo $reject_qnty.'<br/>'.$alter_qnty.'/'.$spot_qnty;  
								    	?>
									</td>
									<td width="3%" class="wrd_brk ver_align_mid center"><? echo $row['tpd']; ?></td>
									<td width="3%" class="wrd_brk ver_align_mid center">
										<? 
										$line_achive=(($good_qnty+$reject_qnty)/$row['tpd'])*100;
										echo number_format($line_achive).'%'; 
										?>
									</td>
									<td width="3%" class="wrd_brk ver_align_mid center">
										<?
										$mid_result_capty_utl = $pre_result_capty_utl*$current_production_hour;
										$capty_utl = $good_qnty*100/$mid_result_capty_utl;
										echo fn_number_format($capty_utl).'%'; 
										?>												
                                    </td>																	
								<?								
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

/*Calculation
Current Achv = ((Total QC + Reject)/Day Target)*100;
Capty Utl = (Total QC * 100) / ((Active machine line*60/SMV)*Production hour);*/

?>