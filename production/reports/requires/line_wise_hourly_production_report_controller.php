<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_hourly_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/line_wise_hourly_production_report_controller' );load_drop_down( 'requires/line_wise_hourly_production_report_controller',document.getElementById('cbo_floor').value+'_'+this.value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_hourly_production_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 ); 
	
	//echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_hourly_production_report_controller', this.value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor','0','0','','0','getFloorId()');\n";
    // echo "setTimeout[($('cbo_floor a').attr('onclick','disappear_list(cbo_floor,'0');getFloorId();') ,3000)];\n";

    exit();
}
if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[0] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[1]!="" && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[2]!="" ) $cond = " and floor_id= $explode_data[2]";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[1]!=0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[2]!="" ) $cond = " and a.floor_id= $explode_data[2]";
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
		if( $explode_data[1]!="" && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[2]!=0 ) $cond = " and floor_name= $explode_data[2]";

		echo create_drop_down( "cbo_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
	}
	exit();
}

/*if ($action=="load_drop_down_line")
{
	echo create_drop_down( "cbo_line", 110, "select id,line_name from lib_sewing_line where status_active =1 and is_deleted=0 and floor_name='$data' order by line_name","id,line_name", 1, "-- Select --", $selected, "",0 );  
	
	exit();     	 
}*/

 
if($action=="report_generate")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
	$buyer_brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$buyer_season_arr=return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$group_library=return_library_array( "select id,sewing_group from lib_sewing_line ", "id", "sewing_group"  ); 
	$groupArr = return_library_array("select id,sewing_group from lib_sewing_line order by id","id","sewing_group"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
		$prod_reso_group=return_library_array( "select a.line_number, b.sewing_group from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","line_number","sewing_group");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
		$prod_reso_group=return_library_array( "select a.line_number, b.sewing_group from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","line_number","sewing_group");
	}
	
	//echo $txt_date;cbo_floor
	if($type==0)
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
		if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
		if($cbo_floor=="") $floor="";else $floor=" and a.floor_id in($cbo_floor)";
		if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";
		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
		if(str_replace("'","",trim($txt_internal_no))=="") $internal_no_cond=""; else $internal_no_cond=" and c.grouping=$txt_internal_no";
		if(str_replace("'","",trim($txt_file_no))=="") $file_no_cond=""; else $file_no_cond=" and c.file_no=$txt_file_no";
		 
		$prod_resource_array=array();
		$dataArray=sql_select("select a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id,b.active_machine,b.man_power from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name");

		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['spent_minutes']=($row[csf('working_hour')]*$row[csf('man_power')])*60;
		}
		//var_dump($prod_resource_array);//change_date_format($txt_date,'yyyy-mm-dd')
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
			$hour=(int)substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
		//		echo $hour;die;
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
		$prod_qnty_data=sql_select("select floor_id, location, prod_reso_allo, sewing_line, po_break_down_id, sum(production_quantity) as prod_qnty from pro_garments_production_mst where  production_type=4 group by floor_id, location, prod_reso_allo, sewing_line, po_break_down_id");
		foreach($prod_qnty_data as $row)
		{
			$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]]=$row[csf("prod_qnty")];
		}
		
		//var_dump($prod_qnty_data_arr);die;
		//echo $prod_qnty_data_arr[1][1][1][88][3533].jahid;die;
		
		if (str_replace("'","",trim($cbo_subcon))==1)
		{	 
			ob_start();
			
			?>
			<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	        </style> 
	                <div style="width:2830px"> 
	                    <table width="2800" cellspacing="0" > 
	                        <tr style="border:none;">
	                                <td align="center" style="border:none; font-size:14px;">
	                                	<b>Line Wise Hourly Production</b><br />
	                                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                         
	                                </td>
	                          </tr> 
	                    </table> 
	                    	<table class="rpt_table" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                          <thead>
	                            <tr>
	                            	<th colspan="3">Summary</th> 
	                            </tr>
	                            <tr>
	                                <th width="150">&nbsp;</th> 
	                                <th width="150">Quantity</th> 
	                                <th width="150">In %</th> 
	                            </tr>
	                          </thead>
	                          <tbody>
								<?
								
			$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
			
			if($db_type==0)
			{
				$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond $file_no_cond $internal_no_cond group by a.prod_reso_allo, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line";  
									}
					//echo $$db_type;die;			
			if($db_type==2)
					{
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id,   listagg(cast(c.po_number AS VARCHAR2(4000)),',') within group (order by c.po_number)   as po_number,    listagg(cast(c.grouping AS VARCHAR2(4000)),',') within group (order by c.grouping) as grouping   ,    listagg(cast(c.file_no AS VARCHAR2(4000)),',') within group (order by c.file_no) as   file_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
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
																		
					$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond   $file_no_cond $internal_no_cond  group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line"; 
									}//$txt_date
									//echo $sql;die;
									
									$result = sql_select($sql);
									$totalGood=0;$totalAlter=0;$totalReject=0;$totalinputQnty=0;
									$production_data=array();
									foreach($result as $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
										
										//total good,alter,reject qnty
					
										$totalGood_qty += $row[csf("good_qnty")];
										$totalAlter_qty += $row[csf("alter_qnty")];
										$totalSpot_qty += $row[csf("spot_qnty")];
										$totalReject_qty += $row[csf("reject_qnty")];
										//echo "select sum(production_quantity)from pro_garments_production_mst where floor_id=".$row[csf("floor_id")]." and location=".$row[csf("location")]." and prod_reso_allo=".$row[csf("prod_reso_allo")]." and sewing_line=".$row[csf("sewing_line")]." and po_break_down_id in (".$row[csf("po_break_down_id")].") and production_type=4 <br>";
										//$inputQnty = return_field_value("sum(production_quantity)","pro_garments_production_mst","floor_id=".$row[csf("floor_id")]." and location=".$row[csf("location")]." and prod_reso_allo=".$row[csf("prod_reso_allo")]." and sewing_line=".$row[csf("sewing_line")]." and po_break_down_id in (".$row[csf("po_break_down_id")].") and production_type=4");
										$inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]];
										
										if($row[csf("prod_reso_allo")]==1)
										{
											//echo $row[csf('sewing_line')]."**";die;
											$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
											$line_name="";
											foreach($line_resource_mst_arr as $resource_id)
											{
												$line_name.=$lineArr[$resource_id].", ";
											}
											$line_name=chop($line_name," , ");
											//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
											//$line_name=$prod_reso_line_arr[$row[('sewing_line')]];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf(('sewing_line'))];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
											for($h=$hour;$h<$last_hour;$h++)
											{
												$bg=$start_hour_arr[$h];
												$bg_hour=$start_hour_arr[$h];
												//$end=substr(add_time($start_hour_arr[$h],60),0,8);
												$prod_hour="prod_hour".substr($bg_hour,0,2);
												$alter_hour="alter_hour".substr($bg_hour,0,2);
												$spot_hour="spot_hour".substr($bg_hour,0,2);
												$reject_hour="reject_hour".substr($bg_hour,0,2);
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
												
											}
										}
										else
										{
											//echo $row[('sewing_line')]."err";
											$line_name=$row[csf('sewing_line')];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$line_name;
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
											for($h=$hour;$h<$last_hour;$h++)
											{
												$bg=$start_hour_arr[$h];
												$bg_hour=$start_hour_arr[$h];
												//$end=substr(add_time($start_hour_arr[$h],60),0,8);
												$prod_hour="prod_hour".substr($bg_hour,0,2);
												$alter_hour="alter_hour".substr($bg_hour,0,2);
												$spot_hour="spot_hour".substr($bg_hour,0,2);
												$reject_hour="reject_hour".substr($bg_hour,0,2);
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
												
											}
										}
										
										$grand_total = $totalGood_qty+$totalAlter_qty+$totalSpot_qty+$totalReject_qty;
											
										$summary_total_parc=($totalGood_qty/$grand_total)*100;
										$summary_total_parcalter=($totalAlter_qty/$grand_total)*100;
										$summary_total_parcspot=($totalSpot_qty/$grand_total)*100;
										$summary_total_parcreject=($totalReject_qty/$grand_total)*100;
									}
									//echo "<pre>";print_r($production_data);die;
									?>
										<tr>
											<td>QC Pass Qty</td>  
											<td align="right"><? echo $totalGood_qty; ?> </td> 
											<td align="right"><? echo number_format($summary_total_parc,2)."%"; ?></td>
										</tr>
										<tr bgcolor="#E9F3FF" >
											<td> Alter Qty </td>
											<td align="right"><?  echo $totalAlter_qty; ?></td>
											<td align="right"><? echo number_format($summary_total_parcalter,2)."%"; ?></td>
										</tr>
										<tr bgcolor="#E9F3FF" >
											<td> Spot Qty yyyy88 </td>
											<td align="right"><?  echo $totalSpot_qty; ?></td>
											<td align="right"><? echo number_format($summary_total_parcspot,2)."%"; ?></td>
										</tr>
										<tr>
											<td>Rejected Qty</td> 
											<td align="right"><? echo $totalReject_qty; ?> </td>
											<td align="right"><? echo number_format($summary_total_parcreject,2)."%"; ?></td>
										</tr>
										</tbody>
										<tfoot>
											<tr>
												<th>Grand Total </th> 
												<th><? echo $grand_total; ?></th>  
												<th>100%</th> 
											</tr>
										</tfoot>
									  </table>
								  <?
						
								  $table_width=1770+($last_hour-$hour+1)*50;
								  $div_width=$table_width+50;
							?>
	                      <br />
	                    <div>
	                        <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                            <thead> 	 	 	 	 	 	
	                                <tr height="50">
	                                    <th width="20">Sl.</th>    
	                                    <th width="100">Location</th>
	                                    <th width="80">Floor</th>
	                                    <th width="90">Line No</th>
	                                    <th width="110">Job No</th>
	                                    <th width="100">Style Ref.</th>
	                                    <th width="100">Internal Ref.</th>
	                                    <th width="100">File No.</th>
	                                    <th width="100">Order No</th>
	                                    <th width="60">Buyer</th>
	                                    <th width="150">Item</th> 
	                                    <th width="70">Input Qnty</th>
	                                    <th width="70">Hourly Target</th>
	                                    <th width="80">Quality</th>
										 <?
	                                    for($k=$hour+1; $k<=$last_hour+1; $k++)
	                                    {
	                                    ?>
	                                      <th width="50" style="vertical-align:middle"><div class="block_div">
										     <?  echo substr($start_hour_arr[$k],0,5);   ?></div>
	                                      </th>
	                                    <?	
	                                    }
	                                    ?>
	                                    <th width="70">Total</th>
	                                    <th width="70">In %</th>
	                                    <th width="70">Day Target</th>
	                                    <th width="70">Line Achv %</th>
	                                    <th width="120">Supervisor</th> 
	                                    <th width="">Remarks</th> 
	                                 </tr>
	                            </thead>
	                        </table>
	                        <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	                            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body" >
	                                <?
									//print_r($production_data);die;
	                                    foreach($production_data as $floor_id=>$value)
	                                    {
											ksort($value);
											foreach($value as $line_name=>$val)
											{
												foreach($val as $row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
													$totalGood += $row[("good_qnty")];
													$totalAlter += $row[("alter_qnty")];
													$totalSpot += $row[("spot_qnty")];
													$totalReject += $row[("reject_qnty")];
													$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]];
													
													$order_number=implode(',',array_unique(explode(",",$row["po_number"])));
													$grouping=implode(',',array_unique(explode(",",$row["grouping"])));
													$file_no=implode(',',array_unique(explode(",",$row["file_no"])));
																								
												?>
													<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
														<td width="20"><? echo $i; ?></td>    
														<td width="100"><p><? echo $location_library[$row[("location")]]; ?></p></td>
														<td width="80"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
														<td width="90"><p><? echo $line_name;// $line_library[$row[csf("sewing_line")]];$row[csf("line_name")];; ?></p></td>
														<td width="110" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
														<td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
														<td width="100"><p><? echo $grouping; ?></p></td>
														<td width="100"><p><? echo $file_no; ?></p></td>
														<td width="100"><p><? echo $order_number; ?></p></td>
														<td width="60"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
														<td width="150"><p><?
														//echo "tanim";
																				$gmt_item_id_string=chop($row[("item_number_id")],",");
																				$gmt_item_id_arr=explode(",",$gmt_item_id_string);
																				$gmt_item_arr = array();
																				foreach($gmt_item_id_arr as $s_gmt_item)
																				{
																					$gmt_item_arr[$s_gmt_item]=$garments_item[$s_gmt_item];
																				}
																				 echo implode(",",$gmt_item_arr);
																				 echo $garments_item[$row[("item_number_id")]];
																				  ?></p></td> 
														<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
														<td width="70" align="right">
														<? 
														
														echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; 
														?>&nbsp;</td>
														
													 <td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
													 <?
									
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
														
														
														$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
														$summary_total_parc=($totalGood/$grand_total)*100;
														$summary_total_parcalter=($totalAlter/$grand_total)*100;
														$summary_total_parcspot=($totalSpot/$grand_total)*100;
														$summary_total_parcreject=($totalReject/$grand_total)*100;
												
													  ?>
													   <td width="50" align="right">&nbsp;<? echo $row[($prod_hour)]; ?><hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
													  <?
													   $total_goods[$prod_hour]+= $row[($prod_hour)];
													   $total_alter[$prod_hour]+= $row[($alter_hour)];
													   $total_reject[$prod_hour]+= $row[($reject_hour)];
													   $total_spot[$prod_hour]+= $row[($spot_hour)];	   
													  }
											
													  ?>
													   
												<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>           
														<?
															$totalQnty = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
															$good_qnty_percentage = ($row[("good_qnty")]/$totalQnty)*100;
															$alter_qnty_percentage = ($row[("alter_qnty")]/$totalQnty)*100;
															$spot_qnty_percentage = ($row[("spot_qnty")]/$totalQnty)*100;
															$reject_qnty_percentage = ($row[("reject_qnty")]/$totalQnty)*100
														?>
														<td width="70" align="right"><? echo number_format($good_qnty_percentage,2); ?><hr><? echo number_format($alter_qnty_percentage,2); ?><hr><? echo number_format($spot_qnty_percentage,2); ?><hr><? echo number_format($reject_qnty_percentage,2); ?></td>
														
														<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>&nbsp;</td>
														<td width="70" align="right">
														<? $line_achive=($row[("good_qnty")]+$row[("reject_qnty")])/$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']*100;
														echo number_format($line_achive,2)."%"; ?>&nbsp;</td>
														<? $expArr = explode(",",$row[("supervisor")]); ?>
														<td width="120"><? echo $expArr[count($expArr)-1]; ?></td>  
														<td width="" align="center">
														<?  
														 $total_po_id=explode(",",$row[("po_break_down_id")]);
														 $total_po_id=implode("*",$total_po_id);
														 $line_number_id=explode(",",$row[('sewing_line')]);
														 $line_number_id=implode("*",$line_number_id);
															
														?>
														
														<input type="button" onclick="show_line_remarks(<? echo $cbo_company_name; ?>,'<? echo $total_po_id; ?>','<? echo $row[("floor_id")]; ?>','<? echo $line_number_id; ?>',<? echo $txt_date; ?>,'remarks_popup')" value="View"  class="formbutton"/></td>  
														  
														
													 </tr>
													<?
													$i++;
													$totalinputQnty+=$inputQnty;
													//$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
													//$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
													$totallineachiveper+=$line_achive;
													if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
													{
														$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
														$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
														$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
													}
													
												}
												
											}
	                                        
	                                }
	                                ?>
	                           </table>
	                           <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                                <tfoot>
	                                	<th width="20">&nbsp;</th>    
	                                    <th width="100">&nbsp;</th>
	                                    <th width="80">&nbsp;</th>
	                                    <th width="90">&nbsp;</th>
	                                    <th width="110">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="60">&nbsp;</th>
	                                    <th width="150">Grand Total</th>
	                                    <th width="70"><? echo $totalinputQnty; ?></th> 
	                                    <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
	                                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
	                                    <?
	                                      for($k=$hour; $k<=$last_hour; $k++)
	                                       {
											    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
												//$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
												//$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
												$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										
											  ?>
											   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
										      <?
											     
										   }
	                                    
	                                    ?>
	                                                                                  
	                                    <th align="right" width="70"><? echo $totalGood_qty; ?><hr><? echo $totalAlter_qty; ?><hr><? echo $totalSpot_qty; ?><hr><? echo $totalReject_qty; ?></th>
	                                    <th align="right" width="70"><? echo number_format($summary_total_parc,2); ?><hr><? echo number_format($summary_total_parcalter,2); ?><hr><? echo number_format($summary_total_parcspot,2); ?><hr><? echo number_format($summary_total_parcreject,2); ?></th>
	                                    <th align="right" width="70"><? echo number_format($totaldaytarget); ?>&nbsp;</th> 
	                                    <th align="right" width="70"><? echo number_format($totalGood_qty*100/$totaldaytarget,2)."%"; ?>&nbsp;</th> 
	                                    <th width="120">&nbsp;</th> 
	                                    <th></th>
	                            </table>	
	                        </div>    
	                    </div>
	                    <br />
	        </div><!-- end main div -->
	         <br/>
	         <fieldset style="width:950px">
				<label   ><b>No Production Line</b></label>
	        	<table id="table_header_1" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="100">Line No</th>
						
						<th width="100">Floor</th>
						<th width="75">Man Power</th>
						<th width="75">Operator</th>
						<th width="75">Helper</th>
	                    <th width="75">Working Hour</th>
						<th width="380">Remarks</th>
						
					</thead>
				</table>
				<div style="width:950px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <? 
					
					$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
					$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line");
					
					foreach($sql_active_line as $inf)
					{	
					
					   if(str_replace("","",$inf[csf('sewing_line')])!="")
					   {
							if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
					   }
					}
							//echo $actual_line_arr;die;
				$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
				 $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
				 if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
				
				 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$cbo_company_name and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0  $line_cond");
			
						$j=1; $location_array=array(); $floor_array=array();
						foreach( $dataArray as $row )
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
	                        	<td width="40"><? echo $j; ?></td>
	                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
	                            <td width="380"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
						$j++;
						}
					?>
						
					</table>
				</div>
			</fieldset>
		<?
		
		}
		if (str_replace("'","",trim($cbo_subcon))==2)
		{
				 
			ob_start();
			?>
			<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	        </style> 
	        <div style="width:2630px"> 
	            <table width="2600" cellspacing="0" > 
	                <tr style="border:none;">
	                        <td align="center" style="border:none; font-size:14px;">
	                            <b>Line Wise Hourly Production</b><br />
	                            Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>   
								                      
	                        </td>
					<tr class="form_caption">
		            	        <td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 


						
		            </tr>
	                  </tr> 
	            </table> 
	            <br />
	            <?
				$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
					
				if($db_type==0)
				  {
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.prod_reso_allo, a.floor_id, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line"; // echo $sql; //$txt_date
					
				}
				
				if($db_type==2)
				  {
						$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, d.production_qnty,
						a.po_break_down_id, 
						c.po_number, 
						(case when a.supervisor!='' then a.supervisor end ) as supervisor, 
						c.grouping, c.file_no,
						sum(d.production_qnty) as good_qnty, 
						sum(d.alter_qty) as alter_qnty,
						sum(d.spot_qty) as spot_qnty, 
						sum(d.reject_qty) as reject_qnty,";
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
						 $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
								sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
								sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,";
						}
						else
						{
					    	$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
							   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
							   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour";
																		
					$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_dtls d
						 where a.id=d.mst_id and a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.production_type=d.production_type $company_name $location $floor $line $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date,a.po_break_down_id, d.production_qnty,
						 c.po_number,a.supervisor,c.grouping, c.file_no 
						 order by a.floor_id, a.sewing_line";  
					
				}
				//  echo $sql;die;
					$result = sql_select($sql);
					$totalGood=0;$totalAlter=0;$totalSpot=0;$totalReject=0;$totalinputQnty=0;

					$production_data=array();
					$po_item_wise_prod_qty_arr=array();
					$lc_com_array = array();
					$style_wise_po_arr = array();
					$poIdArr=array();
					$jobArr=array();
					$jobIdArr=array();
					$all_style_arr=array();
					
				
					foreach($result as $row)
					{
						

							$poIdArr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
							$jobArr[$row[csf('job_no')]] = $row[csf('job_no')];
							$jobIdArr[$row[csf('job_id')]] = $row[csf('job_id')];
							$lc_com_array[$row[csf('company_id')]] = $row[csf('company_id')];
							$all_style_arr[$row[csf('style_ref_no')]] = $row[csf('style_ref_no')];
							$style_wise_po_arr[$row[csf('style_ref_no')]][$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
						//total good,alter,reject qnty
						$totalGood += $row[csf("good_qnty")];
						$totalAlter += $row[csf("alter_qnty")];
						$totalSpot += $row[csf("spot_qnty")];
						$totalReject += $row[csf("reject_qnty")];
						$inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]];
						
						/*if($row[csf("prod_reso_allo")]==1)
						{
							$prod_line=$prod_reso_line_arr[$row[csf("sewing_line")]];
							foreach($prod_reso_arr as $line_id=>$line_no)
							{
								if($line_id==$prod_line)
								{
								}
							}
						}
						else
						{
						}*/
						
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
							

							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("sewing_line")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"].=$row[csf("po_break_down_id")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"].=$row[csf("grouping")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"].=$row[csf("file_no")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"].=$row[csf("po_number")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"].=$row[csf("supervisor")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]['po_item'].=$row[csf('po_break_down_id')]."__".$row[csf('item_number_id')]."**";

							$po_item_wise_prod_qty_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]+=$row[csf('good_qnty')];
							for($h=$hour;$h<=$last_hour;$h++)
							{
								$bg=$start_hour_arr[$h];
								$bg_hour=$start_hour_arr[$h];
								//$end=substr(add_time($start_hour_arr[$h],60),0,8);
								$prod_hour="prod_hour".substr($bg_hour,0,2);
								$alter_hour="alter_hour".substr($bg_hour,0,2);
								$spot_hour="spot_hour".substr($bg_hour,0,2);
								$reject_hour="reject_hour".substr($bg_hour,0,2);
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
								
							}
						}
						else
						{
							$line_name=$lineArr[$row[csf('sewing_line')]];
						

							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("sewing_line")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"].=$row[csf("po_break_down_id")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"].=$row[csf("grouping")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"].=$row[csf("file_no")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"].=$row[csf("po_number")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"].=$row[csf("supervisor")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]['po_item'].=$row[csf('po_break_down_id')]."__".$row[csf('item_number_id')]."**";

							$po_item_wise_prod_qty_arr[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]+=$row[csf('good_qnty')];
							for($h=$hour;$h<=$last_hour;$h++)
							{
								$bg=$start_hour_arr[$h];
								$bg_hour=$start_hour_arr[$h];
								//$end=substr(add_time($start_hour_arr[$h],60),0,8);
								$prod_hour="prod_hour".substr($bg_hour,0,2);
								$alter_hour="alter_hour".substr($bg_hour,0,2);
								$spot_hour="spot_hour".substr($bg_hour,0,2);
								$reject_hour="reject_hour".substr($bg_hour,0,2);
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
								
							}
						
						}
						
						
						
						
					}
					ksort($production_data);
					//echo "<pre>";print_r($all_style_arr);die;
					$lc_com_ids = implode(",",$lc_com_array);
					$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
					$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
					
				//  echo $smv_source;die;

			if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
			if($smv_source==3) // from gsd enrty
			{
				$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
				$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond and a.APPROVED=1 group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
				$gsdSqlResult=sql_select($sql_item);
				//echo $sql_item;die;

				foreach($gsdSqlResult as $rows)
				{
					foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
					{
						if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
						{
							$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
						}
					}
				}
			}
			else
			{
				$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
				//echo $sql_item;
				$resultItem=sql_select($sql_item);

				foreach($resultItem as $itemData)
				{
					if($smv_source==1)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
					}
					if($smv_source==2)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
					}
				}
			}
			//echo "<pre>";print_r($item_smv_array);echo "</pre>";

					
	                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
	                    
	                $summary_total_parc=($totalGood/$grand_total)*100;
	                $summary_total_parcalter=($totalAlter/$grand_total)*100;
	                $summary_total_parcspot=($totalSpot/$grand_total)*100;
	                $summary_total_parcreject=($totalReject/$grand_total)*100;

				$subcon_prod_qnty_data = sql_select("select floor_id, location_id, line_id, order_id, sum(production_qnty) as prod_qnty from  subcon_gmts_prod_dtls where  production_type=2 group by floor_id, location_id, line_id, order_id");
				foreach($subcon_prod_qnty_data as $row)
				{
					$subcon_prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]]=$row[csf("prod_qnty")];
				}
				
				
				$i=1; $grand_total_good_sub=0; $grand_alter_good_sub=0; $grand_total_spot_sub=0; $grand_total_reject_sub=0;
				$first=1;
				$total_goods=array();
				$total_alter=array();
				$total_reject=array();
				$total_spot=array();
					
				if($db_type==0)
				{
					
					$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job as job_no, c.cust_style_ref, b.party_id, a.gmts_item_id, group_concat(distinct(a.order_id)) as order_id, group_concat(distinct(c.order_no)) as order_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
					sum(a.production_qnty) as good_qnty, 
					sum(a.alter_qnty) as alter_qnty,
					sum(a.spot_qnty) as spot_qnty, 
					sum(a.reject_qnty) as reject_qnty,";
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
				         $sql_subcon.="sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN a.hour<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
								sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
								sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					else
						{
					 $sql_subcon.="sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql_subcon.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
															
						$sql_subcon.=" from subcon_gmts_prod_dtls a, subcon_ord_mst b, subcon_ord_dtls c
						where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.floor_id, a.line_id, b.subcon_job order by a.floor_id, a.line_id "; //$txt_date production_date
					
				}//listagg(a.order_id,',') within group (order by order_id) as order_id, listagg(c.order_no,',') within group (order by order_no) as order_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
				//echo $sql_subcon;
				if($db_type==2)
				{
					$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job as job_no, b.party_id, a.gmts_item_id, listagg(a.order_id,',') within group (order by a.order_id) as order_id, listagg(c.order_no,',') within group (order by c.order_no) as order_no,listagg(c.cust_style_ref,',') within group (order by c.cust_style_ref) as style_ref_no,
					LISTAGG(CAST((case when a.supervisor is not null then a.supervisor end) AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.supervisor) as supervisor,
					sum(a.production_qnty) as good_qnty, 
					sum(a.alter_qnty) as alter_qnty,
					sum(a.spot_qnty) as spot_qnty, 
					sum(a.reject_qnty) as reject_qnty,";
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
				         $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
								sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
								sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					else
						{
					 $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql_subcon.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
															
						$sql_subcon.=" from subcon_gmts_prod_dtls a, subcon_ord_mst b, subcon_ord_dtls c
						where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job,  b.party_id, a.gmts_item_id order by a.floor_id, a.line_id "; //$txt_date production_date
					
				}
				 //echo $sql_subcon;die;
				
					$result_subcon = sql_select($sql_subcon);
					$totalGoodSub=0;$totalAlterSub=0;$totalRejectSub=0;$totalSpotSub=0;$totalinputQntySub=0;
					$production_subcon_data=array();
	                    foreach($result_subcon as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	                        
	                        //total good,alter,reject qnty
	                        $totalGoodSub += $row[csf("good_qnty")];
	                        $totalAlterSub += $row[csf("alter_qnty")];
	                        $totalSpotSub += $row[csf("spot_qnty")];
	                        $totalRejectSub += $row[csf("reject_qnty")];
							//echo $row[csf("supervisor")].'==';
							$inputQnty=$subcon_prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]];
							
							if($row[csf("prod_reso_allo")]==1)
							{
								//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
								$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('line_id')]]);
								$line_name="";
								foreach($line_resource_mst_arr as $resource_id)
								{
									$line_name.=$lineArr[$resource_id].", ";
								}
								$line_name=chop($line_name," , ");
								
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("line_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("subcon_job")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("party_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("gmts_item_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("order_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("order_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
								for($h=$hour;$h<=$last_hour;$h++)
								{
									$bg=$start_hour_arr[$h];
									$bg_hour=$start_hour_arr[$h];
									//$end=substr(add_time($start_hour_arr[$h],60),0,8);
									$prod_hour="prod_hour".substr($bg_hour,0,2);
									$alter_hour="alter_hour".substr($bg_hour,0,2);
									$spot_hour="spot_hour".substr($bg_hour,0,2);
									$reject_hour="reject_hour".substr($bg_hour,0,2);
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
									
								}
							}
							else
							{
								$line_name=$lineArr[$row[csf('sewing_line')]];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("line_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("subcon_job")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("party_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("gmts_item_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("order_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("order_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
								for($h=$hour;$h<=$last_hour;$h++)
								{
									$bg=$start_hour_arr[$h];
									$bg_hour=$start_hour_arr[$h];
									//$end=substr(add_time($start_hour_arr[$h],60),0,8);
									$prod_hour="prod_hour".substr($bg_hour,0,2);
									$alter_hour="alter_hour".substr($bg_hour,0,2);
									$spot_hour="spot_hour".substr($bg_hour,0,2);
									$reject_hour="reject_hour".substr($bg_hour,0,2);
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
								}
							}
						}
	                $grand_total_sub = $totalGoodSub+$totalAlterSub+$totalSpotSub+$totalRejectSub;
	                    
	                $summary_total_parc_sub=($totalGoodSub/$grand_total_sub)*100;
	                $summary_total_parcalter_sub=($totalAlterSub/$grand_total_sub)*100;
	                $summary_total_parcspot_sub=($totalSpotSub/$grand_total_sub)*100;
	                $summary_total_parcreject_sub=($totalRejectSub/$grand_total_sub)*100;
					
					$summary_total=0;$summary_good=0;$summary_alter=0;$summary_spot=0;$summary_reject=0; $summary_total_pergood=0;$summary_total_peralter=0;$summary_total_perspot=0;$summary_total_perreject=0;
					
					$summary_total=$grand_total+$grand_total_sub;
					
					$summary_good=$totalGood+$totalGoodSub;
					$summary_alter=$totalAlter+$totalAlterSub;
					$summary_spot=$totalSpot+$totalSpotSub;
					$summary_reject=$totalReject+$totalRejectSub;
					
					
					$summary_total_pergood=($summary_good/$summary_total)*100;
					$summary_total_peralter=($summary_alter/$summary_total)*100;
					$summary_total_perspot=($summary_spot/$summary_total)*100;
					$summary_total_perreject=($summary_reject/$summary_total)*100;
					//print_r($production_subcon_data);die;
				?>
	            <div>
	                <table class="" width="1100" border="0" rules="all" cellpadding="0" cellspacing="0" >
	                    <tr>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Summary (Production-Regular Order)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th> 
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($totalGood,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_parc,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($totalAlter,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcalter,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><? echo number_format($totalSpot,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcspot,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($totalReject,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_parcreject,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($grand_total,2); ?></th>  
	                                        <th>100%</th> 
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                        <td width="" >&nbsp;</td>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Summary (Production-Subcontract Order)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th> 
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($totalGoodSub,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_parc_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($totalAlterSub,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcalter_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><?  echo number_format($totalSpotSub,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcspot_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($totalRejectSub,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_parcreject_sub,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($grand_total_sub,2); ?></th>  
	                                        <th>100%</th> 
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                        <td width="" >&nbsp;</td>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Total Summary (Regular + Subcontract)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th>
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($summary_good,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_pergood,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($summary_alter,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_peralter,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><?  echo number_format($summary_spot,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_perspot,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($summary_reject,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_perreject,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($summary_total,2); ?></th> 
	                                        <th>100%</th>  
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                     </tr>
	                </table>
	            </div>
	            <br /> 
	                       
	                       
	                       
			   <?
	            $table_width=1880+($last_hour-$hour+1)*50;
	           ?>
	            <div style="width:200px; font-weight:bold">Production-Regular Order</div>
	            <div>
	            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                <thead> 	 	 	 	 	 	
	                    <tr height="50">
	                        <th width="30">Sl.</th>    
	                        <th width="90">Location</th>
	                        <th width="70">Floor</th>
	                        <th width="70">Line No</th>
	                        <th width="70">Number of Machine</th>
	                        <th width="60">Job No</th>
	                        <th width="110">Style Ref.</th>
	                        <th width="100">Internal Ref.</th>
	                        <th width="100">File No</th>
	                        <th width="100">Order No</th>
	                        <th width="60">Buyer</th>
	                        <th width="170">Item</th>
	                        <th width="70">Input Qnty</th>
	                        <th width="70">Hourly Target</th>
	                        <th width="80">Quality</th>
	                     	 <?
	                        
	                            for($k=$hour+1; $k<=$last_hour+1; $k++)
	                            {
	                            ?>
	                              <th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
	                            
	                            <?	
	                            }
	                         ?>
	                           	 	 	 	
	                        <th width="70">Total</th>
	                        <th width="70">In %</th>
	                        <th width="70">Day Target</th>
	                        <th width="70">Line Achv %</th>
							<th width="70">Spent Minutes</th>
							<th width="70">Produced Minutes</th>
	                        <th width="120">Supervisor</th> 
	                        <th width="">Remarks</th> 
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body" >
	                <?
	                 
					  
	                    $totalGoodQnt=0; $totalAlterQnt=0; $totalSpotQnt=0; $totalRejectQnt=0;
						//print_r($production_data);die;
	                    foreach($production_data as $flowre_id=>$value)
	                    {
							ksort($value);
							foreach($value as $line_name=>$job_no)
							{
								foreach($job_no as $row)
								{
										$po_item_arr = array_unique(array_filter(explode("**",$row['po_item'])));
										$po_chk_arr = array();
									
										
										foreach ($po_item_arr as $po_item_data)
										{
													// echo $po_item_data."dddd<br>";
													$po_item_ex_arr = explode("__",$po_item_data);
													

													$produce_minit += $po_item_wise_prod_qty_arr[$flowre_id][$line_name][$row["job_no"]] [$po_item_ex_arr[0]][$po_item_ex_arr[1]]*$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
													
													
													//  echo $po_item_wise_prod_qty_arr[$flowre_id][$line_name][$row["job_no"]] [$po_item_ex_arr[0]][$po_item_ex_arr[1]]."*".$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."<br>";


										}
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									//total good,alter,reject qnty
									$totalGood_qty += $row[("good_qnty")];
									$totalAlter_qty += $row[("alter_qnty")];
									$totalSpot_qty += $row[("spot_qnty")];
									$totalReject_qty += $row[("reject_qnty")];
									$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]];
									$order_number=implode(',',array_unique(explode(",",$row[("po_number")])));
									$grouping=implode(',',array_unique(explode(",",$row[("grouping")])));
									$file_no=implode(',',array_unique(explode(",",$row[("file_no")])));
								?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="30" align="center"><? echo $i; ?></td>    
										<td width="90" align="center"><p><? echo $location_library[$row[("location")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $line_name;// $line_library[$row[csf("sewing_line")]];$row[csf("line_name")];; ?></p></td>
										<td width="70" align="center"><p><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['active_machine']; ?></p></td>
										<td width="60" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
										<td width="110" align="center"><p><? echo $row[("style_ref_no")]; ?></p></td>
										<td width="100" align="center"><p><? echo $grouping; ?></p></td>
										<td width="100" align="center"><p><? echo $file_no; ?></p></td>
										<td width="100" align="center"><p><? echo $order_number; ?></p></td>
										<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
										<td width="170" align="center"><p>
											<?
											$gmt_item_id_string=chop($row[("item_number_id")],",");
											$gmt_item_id_arr=explode(",",$gmt_item_id_string);
											$gmt_item_arr = array();
											foreach($gmt_item_id_arr as $s_gmt_item)
											{
												$gmt_item_arr[$s_gmt_item]=$garments_item[$s_gmt_item];
											}
											 echo implode(",",$gmt_item_arr);
											 echo $garments_item[$row[("item_number_id")]]; 
											 ?>																		 	
										</p></td> 
										<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
										<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; ?>&nbsp;</td>
										<td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
										
										<?
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
											
											
											$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
											$summary_total_parc=($totalGood/$grand_total)*100;
											$summary_total_parcalter=($totalAlter/$grand_total)*100;
											$summary_total_parcspot=($totalSpot/$grand_total)*100;
											$summary_total_parcreject=($totalReject/$grand_total)*100;
											
											?>
											<td width="50" align="right"><? echo $row[($prod_hour)]; ?>&nbsp;<hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
											<?
											$total_goods[$prod_hour]+= $row[($prod_hour)];
											$total_alter[$prod_hour]+= $row[($alter_hour)];
											$total_reject[$prod_hour]+= $row[($reject_hour)];
											$total_spot[$prod_hour]+= $row[($spot_hour)];	   
										}
										
										?>	 	 	 	
										<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>
										<?
											$totalQnty = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
											$good_qnty_percentage = ($row[("good_qnty")]/$totalQnty)*100;
											$alter_qnty_percentage = ($row[("alter_qnty")]/$totalQnty)*100;
											$spot_qnty_percentage = ($row[("spot_qnty")]/$totalQnty)*100;
											$reject_qnty_percentage = ($row[("reject_qnty")]/$totalQnty)*100
										?>
										<td width="70" align="right"><? echo number_format($good_qnty_percentage,2); ?><hr><? echo number_format($alter_qnty_percentage,2); ?><hr><? echo number_format($spot_qnty_percentage,2); ?><hr><? echo number_format($reject_qnty_percentage,2); ?></td>
										
										<td width="70" align="right"><? //echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[csf('production_date')])]['tpd']; 
										echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>&nbsp;</td>
										<td width="70" align="right">
											<? $line_achive=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']*100;
											echo number_format($line_achive,2)."%"; ?>&nbsp;</td>
											<td width="70"  align="right"><?=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['spent_minutes']; ?>&nbsp;</td>
											<td width="70" align="right"><?=$produce_minit?></td>


										<? $expArr = explode(",",$row["supervisor"]); ?>
										<td width="120"><? echo $expArr[count($expArr)-1]; ?></td>  
										<td width="" align="center">
										<?  
										
										 $total_po_id=explode(",",$row[("po_break_down_id")]);
										 $all_po_id=implode("*",$total_po_id);
										 $line_number_id=explode(",",$row[('sewing_line')]);
										 $line_number_id=implode("*",$line_number_id);
											
										?>
										
										<input type="button" onclick="show_line_remarks(<? echo $cbo_company_name; ?>,'<? echo $all_po_id; ?>','<? echo $row[("floor_id")]; ?>','<? echo $line_number_id; ?>',<? echo $txt_date; ?>,'remarks_popup')" value="View"  class="formbutton"/></td>  
									 </tr>
									<?
									$i++;
									$totalinputQnty+=$inputQnty;
									//$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
									//$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
									$totallineachiveper+=$line_achive;
									if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
									{
										$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
										$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
										$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
									}
								}
							}
	                }
	                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
	                    
	                $summary_total_parc=($totalGood/$grand_total)*100;
	                $summary_total_parcalter=($totalAlter/$grand_total)*100;
	                $summary_total_parcspot=($totalSpot/$grand_total)*100;
	                $summary_total_parcreject=($totalReject/$grand_total)*100;
	                ?>
	            </table>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="rpt_table_footer1" >
	                <tfoot>
	                    <th width="30">&nbsp;</th>    
	                    <th width="90">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="110">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="170" align="right">Grand Total: </th>  
	                    <th width="70"><? echo $totalinputQnty; ?></th> 
	                    <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
	                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
	                      <?
	                                      for($k=$hour; $k<=$last_hour; $k++)
	                                       {
											    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										
											  ?>
											   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
										      <?
											     
										   }
	                                    
	                                    ?>                       
	                  
	                    <th align="right" width="70"><? echo $totalGood_qty; ?><hr><? echo $totalAlter_qty; ?><hr><? echo $totalSpot_qty; ?><hr><? echo $totalReject_qty; ?></th>
	                    <th align="right" width="70"><? echo number_format($summary_total_parc,2); ?><hr><? echo number_format($summary_total_parcalter,2); ?><hr><? echo number_format($summary_total_parcspot,2); ?><hr><? echo number_format($summary_total_parcreject,2); ?></th>
	                    <th align="right" width="70"><? echo number_format($totaldaytarget); ?>&nbsp;</th> 
	                    <th align="right"width="70"><? echo number_format($totalGood_qty*100/$totaldaytarget,2)."%"; ?>&nbsp;</th> 
						<th align="right"width="70">&nbsp;</th>
						<th align="right"width="70">&nbsp;</th>
	                    <th width="120">&nbsp;</th> 
	                    <th>&nbsp;</th> 
	                </tfoot>
	            </table>
	           </div>    
	        </div>
	        <br />
	        <br />
	        <div style="width:200px; font-weight:bold">Production-Subcontract Order</div>
	            <div>
	            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
	                <thead> 	 	 	 	 	 	
	                    <tr height="50">
	                        <th width="30">Sl.</th>    
	                        <th width="90">Location</th>
	                        <th width="70">Floor</th>
	                        <th width="70">Line No</th>
	                        <th width="70">Number of Machine</th>
	                        <th width="60">Job No</th>
	                        <th width="110">Style Ref.</th>
	                        <th width="100">Order No</th>
	                        <th width="60">Buyer</th>
	                        <th width="170">Item</th>
	                        <th width="70">Input Qnty</th>
	                        <th width="70">Hourly Target</th>
	                        <th width="80">Quality</th>
	                        
	                        	 <?
	                        
	                            for($k=$hour+1; $k<=$last_hour+1; $k++)
	                            {
	                            ?>
	                              <th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
	                            
	                            <?	
	                            }
	                        ?>
	                       	 	 	 	
	                        <th width="70">Total</th>
	                        <th width="70">In %</th>
	                        <th width="70">Day Target</th>
	                        <th width="70">Line Achv %</th>
	                        <th width="">Supervisor</th> 
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body1" >
	                <? 
	                    $totalGoodSubSub=0; $totalAlterSubSub=0; $totalSpotSubSub=0; $totalRejectSubSub=0;//$prod_reso_arr_sub=array();
	                    foreach($production_subcon_data as $floor_id=>$value)
	                    {
							ksort($value);
							foreach($value as $line_name=>$job_data)
							{
								foreach($job_data as $job_no=>$row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									//total good,alter,spot,reject qnty
									$totalGoodSubSub += $row[("good_qnty")];
									$totalAlterSubSub += $row[("alter_qnty")];
									$totalSpotSubSub += $row[("spot_qnty")];
									$totalRejectSubSub += $row[("reject_qnty")];
									
									$inputQntySub=$subcon_prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("line_id")]][$row[("po_break_down_id")]];
									
									$order_num="";
									$ex_po=array_unique(explode(',',$row[("po_number")]));
									foreach($ex_po as $po_no)
									{
										if($order_num=="") $order_num=$po_no; else $order_num.=','.$po_no;
									}

									$style_ref_no="";
									$ex_style=array_unique(explode(',',$row[("style_ref_no")]));
									foreach($ex_style as $style_no)
									{
										if($style_ref_no=="") $style_ref_no=$style_no; else $style_ref_no.=','.$style_no;
									}
									
									?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="30" align="center"><? echo $i; ?></td>    
										<td width="90" align="center"><p><? echo $location_library[$row[("location")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $line_name; ?></p></td>
										<td width="70" align="center"><p><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['active_machine']; ?></p></td>
										<td width="60" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
										<td width="110" align="center"><p><? echo $style_ref_no; ?></p></td>
										<td width="100" align="center"><p><? echo $order_num; ?></p></td>
										<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
										<td width="170" align="center"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td> 
										<td width="70" align="right"><p><? echo $inputQntySub; ?></p></td> 
										<td width="70" align="right"><? echo $prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
										
										//$prod_resource_array[$row['line_id']][change_date_format($row['production_date'])]['target_per_hour']; ?>&nbsp;</td>
										<td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
										
										<?
										
										for($k=$hour; $k<=$last_hour; $k++)
										{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
										$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
										$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										$totalGoodQnt_sub += $row[($prod_hour)];
										$totalAlterQnt_sub += $row[($alter_hour)];
										$totalSpotQnt_sub += $row[($spot_hour)];
										$totalRejectQnt_sub +=$row[($reject_hour)];
										
										
										$grand_total_sub = $totalGoodSubSub+$totalAlterSubSub+$totalSpot+$totalReject;
										$summary_total_parc_sub=($totalGood/$grand_total)*100;
										$summary_total_parcalter__sub=($totalAlter/$grand_total)*100;
										$summary_total_parcspot=($totalSpot/$grand_total)*100;
										$summary_total_parcreject_sub=($totalReject/$grand_total)*100;
										
										?>
										<td width="50" align="right"><? echo $row[($prod_hour)]; ?>&nbsp;<hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
										<?
										$sub_total_goods[$prod_hour]+= $row[($prod_hour)];
										$sub_total_alter[$prod_hour]+= $row[($alter_hour)];
										$sub_total_reject[$prod_hour]+= $row[($reject_hour)];
										$sub_total_spot[$prod_hour]+= $row[($spot_hour)];	   
										}
										
										?>
										
										<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>
										<?
										$totalQntySub = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
										$good_qnty_percentage_sub = ($row[("good_qnty")]/$totalQntySub)*100;
										$alter_qnty_percentage_sub = ($row[("alter_qnty")]/$totalQntySub)*100;
										$spot_qnty_percentage_sub = ($row[("spot_qnty")]/$totalQntySub)*100;
										$reject_qnty_percentage_sub = ($row[("reject_qnty")]/$totalQntySub)*100
										?>
										<td width="70" align="right"><? echo number_format($good_qnty_percentage_sub,2); ?><hr><? echo number_format($alter_qnty_percentage_sub,2); ?><hr><? echo number_format($spot_qnty_percentage_sub,2); ?><hr><? echo number_format($reject_qnty_percentage_sub,2); ?></td>
										<td width="70" align="right"><? echo $prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']; ?>&nbsp;</td>
										<td width="70" align="right">
										<? $line_achive_sub=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']*100;
										echo number_format($line_achive_sub,2)."%"; ?>&nbsp;</td>
										<? $expArr = explode(",",$row["supervisor"]); ?>
										<td width=""><? echo $expArr[count($expArr)-1]; ?></td>  
									</tr>
									<?
									$i++;
									$totalinputQntySub+=$inputQntySub;
									//$totaltargetperhouresub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
									//$totaldaytargetsub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd'];
									$totallineachivepesubr+=$line_achive;
									
									if($duplicate_array[$row['prod_reso_allo']][$row['sewing_line']]=="")
									{
										$totaltargetperhouresub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
										$totaldaytargetsub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd'];
										$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
									}
								}
								
							}
	                        
	                    
	                }
	                $grand_total_sub = $totalGoodSub+$totalAlterSub+$totalSpotSub+$totalRejectSub;
	                    
	                $summary_total_parc_sub=($totalGoodSub/$grand_total_sub)*100;
	                $summary_total_parcalter_sub=($totalAlterSub/$grand_total_sub)*100;
	                $summary_total_parcspot_sub=($totalSpotSub/$grand_total_sub)*100;
	                $summary_total_parcreject_sub=($totalRejectSub/$grand_total_sub)*100;
	                ?>
	            </table>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="rpt_table_footer1" >
	                <tfoot>
	                    <th width="30">&nbsp;</th>    
	                    <th width="90">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="110">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="170" align="right">Grand Total: </th>  
	                    <th  width="70"><? echo $totalinputQntySub; ?></th> 
	                    <th width="70"><? echo $totaltargetperhouresub; ?>&nbsp;</th> 
	                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
						<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<th align="right" width="50"><? echo $sub_total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_reject[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_spot[$prod_hour]; ?>&nbsp;</th> 
								<?
							}  
	                    ?>                            
	                    <th align="right" width="70"><? echo $totalGoodSubSub; ?><hr><? echo $totalAlterSubSub; ?><hr><? echo $totalSpotSubSub; ?><hr><? echo $totalRejectSubSub; ?></th>
	                    <th align="right" width="70"><? echo number_format($summary_total_parc_sub,2); ?><hr><? echo number_format($summary_total_parcalter_sub,2); ?><hr><? echo number_format($summary_total_parcspot_sub,2); ?><hr><? echo number_format($summary_total_parcreject_sub,2); ?></th>
	                    <th align="right" width="70"><? echo number_format($totaldaytargetsub); ?>&nbsp;</th> 
	                    <th align="right" width="70"><? echo number_format($totalGoodSubSub*100/$totaldaytargetsub,2)."%"; ?>&nbsp;</th> 
	                    <th width="">&nbsp;</th> 
	                </tfoot>
	            </table>	
	           </div>    
	        </div>
	      <br />
	      <br />
	    	</div><!-- end main div -->
	     	<br/>
	         <fieldset style="width:950px">
				<label   ><b>No Production Line</b></label>
	        	<table id="table_header_1" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="100">Line No</th>
						
						<th width="100">Floor</th>
						<th width="75">Man Power</th>
						<th width="75">Operator</th>
						<th width="75">Helper</th>
	                    <th width="75">Working Hour</th>
						<th width="380">Remarks</th>
						
					</thead>
				</table>
				<div style="width:950px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <? 
					
					$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
					$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line
					union all
					select line_id,sum(production_qnty) as total_production from subcon_gmts_prod_dtls  where production_date=".$txt_date." and production_type=2 and status_active=1 and is_deleted=0 group by  line_id");
					
					foreach($sql_active_line as $inf)
					{	
					
					   if(str_replace("","",$inf[csf('sewing_line')])!="")
					   {
							if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
					   }
					}
							//echo $actual_line_arr;die;
				$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
				 $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
				 if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";

				 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$cbo_company_name and b.pr_date=".$txt_date." and d.shift_id=1  and a.is_deleted=0 and b.is_deleted=0 $line_cond");
						$j=1; $location_array=array(); $floor_array=array();
						foreach( $dataArray as $row )
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
	                        	<td width="40"><? echo $j; ?></td>
	                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
	                            <td width="380"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
							$j++;
							
						}
						
				
					?>
						
					</table>
				</div>
			</fieldset>
			<?
		}
	}
	else if($type==3) //Same as Show Button change only for Time AM-PM
	{


		$cbo_floor=str_replace("'","",$cbo_floor);
		if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
		if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
		if($cbo_floor=="") $floor="";else $floor=" and a.floor_id in($cbo_floor)";
		if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";
		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
		if(str_replace("'","",trim($txt_internal_no))=="") $internal_no_cond=""; else $internal_no_cond=" and c.grouping=$txt_internal_no";
		if(str_replace("'","",trim($txt_file_no))=="") $file_no_cond=""; else $file_no_cond=" and c.file_no=$txt_file_no";
		 
		$prod_resource_array=array();
		$dataArray=sql_select("select a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id,b.active_machine from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name");

		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
		}
		//var_dump($prod_resource_array);//change_date_format($txt_date,'yyyy-mm-dd')
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
		//		echo $hour;die;
			$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
			
			$start_hour=$prod_start_hour;
			$start_hour_arr[$hour]=$start_hour;
			for($j=$hour;$j<$last_hour;$j++)
			{

				
				$start_hour=add_time($start_hour,60);
				$start_hour_arr[$j+1]=substr($start_hour,0,5);
				//$start_hour_arr[$j+1]=substr(date("h:i a",strtotime($start_hour)),0,5);
			}
		    $start_hour_arr[$j+1]='23:59';
		    //$start_hour_arr[$j+1]=date("h:i a",strtotime('23:59'));
			//print_r($start_hour_arr);
		//var_dump($prod_resource_array);
		$prod_qnty_data=sql_select("select floor_id, location, prod_reso_allo, sewing_line, po_break_down_id, sum(production_quantity) as prod_qnty from pro_garments_production_mst where  production_type=4 group by floor_id, location, prod_reso_allo, sewing_line, po_break_down_id");
		foreach($prod_qnty_data as $row)
		{
			$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]]=$row[csf("prod_qnty")];
		}
		
		//var_dump($prod_qnty_data_arr);die;
		//echo $prod_qnty_data_arr[1][1][1][88][3533].jahid;die;
		
		if (str_replace("'","",trim($cbo_subcon))==1)
		{	 
			ob_start();
			
			?>
			<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	        </style> 
	                <div style="width:2830px"> 
	                    <table width="2800" cellspacing="0" > 
	                        <tr style="border:none;">
	                                <td align="center" style="border:none; font-size:14px;">
	                                	<b>Line Wise Hourly Production</b><br />
	                                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                         
	                                </td>
	                          </tr> 
	                    </table> 
	                    	<table class="rpt_table" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                          <thead>
	                            <tr>
	                            	<th colspan="3">Summary</th> 
	                            </tr>
	                            <tr>
	                                <th width="150">&nbsp;</th> 
	                                <th width="150">Quantity</th> 
	                                <th width="150">In %</th> 
	                            </tr>
	                          </thead>
	                          <tbody>
								<?
								
			$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
			
			if($db_type==0)
				{
			$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond $file_no_cond $internal_no_cond group by a.prod_reso_allo, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line";  
									}
					//echo $$db_type;die;			
			if($db_type==2)
					{
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id,   listagg(cast(c.po_number AS VARCHAR2(4000)),',') within group (order by c.po_number)   as po_number,    listagg(cast(c.grouping AS VARCHAR2(4000)),',') within group (order by c.grouping) as grouping   ,    listagg(cast(c.file_no AS VARCHAR2(4000)),',') within group (order by c.file_no) as   file_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
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
																		
					$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond   $file_no_cond $internal_no_cond  group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line"; 
									}//$txt_date
									//echo $sql;die;
									
									$result = sql_select($sql);
									$totalGood=0;$totalAlter=0;$totalReject=0;$totalinputQnty=0;
									$production_data=array();
									foreach($result as $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
										
										//total good,alter,reject qnty
					
										$totalGood_qty += $row[csf("good_qnty")];
										$totalAlter_qty += $row[csf("alter_qnty")];
										$totalSpot_qty += $row[csf("spot_qnty")];
										$totalReject_qty += $row[csf("reject_qnty")];
										//echo "select sum(production_quantity)from pro_garments_production_mst where floor_id=".$row[csf("floor_id")]." and location=".$row[csf("location")]." and prod_reso_allo=".$row[csf("prod_reso_allo")]." and sewing_line=".$row[csf("sewing_line")]." and po_break_down_id in (".$row[csf("po_break_down_id")].") and production_type=4 <br>";
										//$inputQnty = return_field_value("sum(production_quantity)","pro_garments_production_mst","floor_id=".$row[csf("floor_id")]." and location=".$row[csf("location")]." and prod_reso_allo=".$row[csf("prod_reso_allo")]." and sewing_line=".$row[csf("sewing_line")]." and po_break_down_id in (".$row[csf("po_break_down_id")].") and production_type=4");
										$inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]];
										
										if($row[csf("prod_reso_allo")]==1)
										{
											//echo $row[csf('sewing_line')]."**";die;
											$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
											$line_name="";
											foreach($line_resource_mst_arr as $resource_id)
											{
												$line_name.=$lineArr[$resource_id].", ";
											}
											$line_name=chop($line_name," , ");
											//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
											//$line_name=$prod_reso_line_arr[$row[('sewing_line')]];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf(('sewing_line'))];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
											for($h=$hour;$h<$last_hour;$h++)
											{
												$bg=$start_hour_arr[$h];
												$bg_hour=$start_hour_arr[$h];
												//$end=substr(add_time($start_hour_arr[$h],60),0,8);
												$prod_hour="prod_hour".substr($bg_hour,0,2);
												$alter_hour="alter_hour".substr($bg_hour,0,2);
												$spot_hour="spot_hour".substr($bg_hour,0,2);
												$reject_hour="reject_hour".substr($bg_hour,0,2);
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
												
											}
										}
										else
										{
											//echo $row[('sewing_line')]."err";
											$line_name=$row[csf('sewing_line')];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$line_name;
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
											for($h=$hour;$h<$last_hour;$h++)
											{
												$bg=$start_hour_arr[$h];
												$bg_hour=$start_hour_arr[$h];
												//$end=substr(add_time($start_hour_arr[$h],60),0,8);
												$prod_hour="prod_hour".substr($bg_hour,0,2);
												$alter_hour="alter_hour".substr($bg_hour,0,2);
												$spot_hour="spot_hour".substr($bg_hour,0,2);
												$reject_hour="reject_hour".substr($bg_hour,0,2);
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
												
											}
										}
										
										$grand_total = $totalGood_qty+$totalAlter_qty+$totalSpot_qty+$totalReject_qty;
											
										$summary_total_parc=($totalGood_qty/$grand_total)*100;
										$summary_total_parcalter=($totalAlter_qty/$grand_total)*100;
										$summary_total_parcspot=($totalSpot_qty/$grand_total)*100;
										$summary_total_parcreject=($totalReject_qty/$grand_total)*100;
									}
									//echo "<pre>";print_r($production_data);die;
									?>
										<tr>
											<td>QC Pass Qty</td>  
											<td align="right"><? echo $totalGood_qty; ?> </td> 
											<td align="right"><? echo number_format($summary_total_parc,2)."%"; ?></td>
										</tr>
										<tr bgcolor="#E9F3FF" >
											<td> Alter Qty </td>
											<td align="right"><?  echo $totalAlter_qty; ?></td>
											<td align="right"><? echo number_format($summary_total_parcalter,2)."%"; ?></td>
										</tr>
										<tr bgcolor="#E9F3FF" >
											<td> Spot Qty </td>
											<td align="right"><?  echo $totalSpot_qty; ?></td>
											<td align="right"><? echo number_format($summary_total_parcspot,2)."%"; ?></td>
										</tr>
										<tr>
											<td>Rejected Qty</td> 
											<td align="right"><? echo $totalReject_qty; ?> </td>
											<td align="right"><? echo number_format($summary_total_parcreject,2)."%"; ?></td>
										</tr>
										</tbody>
										<tfoot>
											<tr>
												<th>Grand Total </th> 
												<th><? echo $grand_total; ?></th>  
												<th>100%</th> 
											</tr>
										</tfoot>
									  </table>
								  <?
						
								  $table_width=1770+($last_hour-$hour+1)*50;
								  $div_width=$table_width+50;
							?>
	                      <br />
	                    <div>
	                        <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                            <thead> 	 	 	 	 	 	
	                                <tr height="50">
	                                    <th width="20">Sl.</th>    
	                                    <th width="100">Location</th>
	                                    <th width="80">Floor</th>
	                                    <th width="90">Line No</th>
	                                    <th width="110">Job No</th>
	                                    <th width="100">Style Ref.</th>
	                                    <th width="100">Internal Ref.</th>
	                                    <th width="100">File No.</th>
	                                    <th width="100">Order No</th>
	                                    <th width="60">Buyer</th>
	                                    <th width="150">Item</th> 
	                                    <th width="70">Input Qnty</th>
	                                    <th width="70">Hourly Target</th>
	                                    <th width="80">Quality</th>
										 <?
	                                    for($k=$hour+1; $k<=$last_hour+1; $k++)
	                                    {
	                                    ?>
	                                      <th width="50" style="vertical-align:middle"><div class="block_div">
										     <?  echo substr(date("h:i a",strtotime($start_hour_arr[$k])),0,8);//substr($start_hour_arr[$k],0,5); 
										       ?></div>
	                                      </th>
	                                    <?	
	                                    }
	                                    ?>
	                                    <th width="70">Total</th>
	                                    <th width="70">In %</th>
	                                    <th width="70">Day Target</th>
	                                    <th width="70">Line Achv %</th>
	                                    <th width="120">Supervisor</th> 
	                                    <th width="">Remarks</th> 
	                                 </tr>
	                            </thead>
	                        </table>
	                        <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	                            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body" >
	                                <?
									//print_r($production_data);die;
	                                    foreach($production_data as $floor_id=>$value)
	                                    {
											ksort($value);
											foreach($value as $line_name=>$val)
											{
												foreach($val as $row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
													$totalGood += $row[("good_qnty")];
													$totalAlter += $row[("alter_qnty")];
													$totalSpot += $row[("spot_qnty")];
													$totalReject += $row[("reject_qnty")];
													$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]];
													
													$order_number=implode(',',array_unique(explode(",",$row["po_number"])));
													$grouping=implode(',',array_unique(explode(",",$row["grouping"])));
													$file_no=implode(',',array_unique(explode(",",$row["file_no"])));
																								
												?>
													<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
														<td width="20"><? echo $i; ?></td>    
														<td width="100"><p><? echo $location_library[$row[("location")]]; ?></p></td>
														<td width="80"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
														<td width="90"><p><? echo $line_name;// $line_library[$row[csf("sewing_line")]];$row[csf("line_name")];; ?></p></td>
														<td width="110" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
														<td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
														<td width="100"><p><? echo $grouping; ?></p></td>
														<td width="100"><p><? echo $file_no; ?></p></td>
														<td width="100"><p><? echo $order_number; ?></p></td>
														<td width="60"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
														<td width="150"><p><?
														//echo "tanim";
																				$gmt_item_id_string=chop($row[("item_number_id")],",");
																				$gmt_item_id_arr=explode(",",$gmt_item_id_string);
																				$gmt_item_arr = array();
																				foreach($gmt_item_id_arr as $s_gmt_item)
																				{
																					$gmt_item_arr[$s_gmt_item]=$garments_item[$s_gmt_item];
																				}
																				 echo implode(",",$gmt_item_arr);
																				 echo $garments_item[$row[("item_number_id")]];
																				  ?></p></td> 
														<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
														<td width="70" align="right">
														<? 
														
														echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; 
														?>&nbsp;</td>
														
													 <td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
													 <?
									
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
														
														
														$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
														$summary_total_parc=($totalGood/$grand_total)*100;
														$summary_total_parcalter=($totalAlter/$grand_total)*100;
														$summary_total_parcspot=($totalSpot/$grand_total)*100;
														$summary_total_parcreject=($totalReject/$grand_total)*100;
												
													  ?>
													   <td width="50" align="right">&nbsp;<? echo $row[($prod_hour)]; ?><hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
													  <?
													   $total_goods[$prod_hour]+= $row[($prod_hour)];
													   $total_alter[$prod_hour]+= $row[($alter_hour)];
													   $total_reject[$prod_hour]+= $row[($reject_hour)];
													   $total_spot[$prod_hour]+= $row[($spot_hour)];	   
													  }
											
													  ?>
													   
												<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>           
														<?
															$totalQnty = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
															$good_qnty_percentage = ($row[("good_qnty")]/$totalQnty)*100;
															$alter_qnty_percentage = ($row[("alter_qnty")]/$totalQnty)*100;
															$spot_qnty_percentage = ($row[("spot_qnty")]/$totalQnty)*100;
															$reject_qnty_percentage = ($row[("reject_qnty")]/$totalQnty)*100
														?>
														<td width="70" align="right"><? echo number_format($good_qnty_percentage,2); ?><hr><? echo number_format($alter_qnty_percentage,2); ?><hr><? echo number_format($spot_qnty_percentage,2); ?><hr><? echo number_format($reject_qnty_percentage,2); ?></td>
														
														<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>&nbsp;</td>
														<td width="70" align="right">
														<? $line_achive=($row[("good_qnty")]+$row[("reject_qnty")])/$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']*100;
														echo number_format($line_achive,2)."%"; ?>&nbsp;</td>
														<? $expArr = explode(",",$row[("supervisor")]); ?>
														<td width="120"><? echo $expArr[count($expArr)-1]; ?></td>  
														<td width="" align="center">
														<?  
														 $total_po_id=explode(",",$row[("po_break_down_id")]);
														 $total_po_id=implode("*",$total_po_id);
														 $line_number_id=explode(",",$row[('sewing_line')]);
														 $line_number_id=implode("*",$line_number_id);
															
														?>
														
														<input type="button" onclick="show_line_remarks(<? echo $cbo_company_name; ?>,'<? echo $total_po_id; ?>','<? echo $row[("floor_id")]; ?>','<? echo $line_number_id; ?>',<? echo $txt_date; ?>,'remarks_popup')" value="View"  class="formbutton"/></td>  
														  
														
													 </tr>
													<?
													$i++;
													$totalinputQnty+=$inputQnty;
													//$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
													//$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
													$totallineachiveper+=$line_achive;
													if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
													{
														$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
														$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
														$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
													}
													
												}
												
											}
	                                        
	                                }
	                                ?>
	                           </table>
	                           <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                                <tfoot>
	                                	<th width="20">&nbsp;</th>    
	                                    <th width="100">&nbsp;</th>
	                                    <th width="80">&nbsp;</th>
	                                    <th width="90">&nbsp;</th>
	                                    <th width="110">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="60">&nbsp;</th>
	                                    <th width="150">Grand Total</th>
	                                    <th width="70"><? echo $totalinputQnty; ?></th> 
	                                    <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
	                                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
	                                    <?
	                                      for($k=$hour; $k<=$last_hour; $k++)
	                                       {
											    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
												//$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
												//$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
												$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										
											  ?>
											   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
										      <?
											     
										   }
	                                    
	                                    ?>
	                                                                                  
	                                    <th align="right" width="70"><? echo $totalGood_qty; ?><hr><? echo $totalAlter_qty; ?><hr><? echo $totalSpot_qty; ?><hr><? echo $totalReject_qty; ?></th>
	                                    <th align="right" width="70"><? echo number_format($summary_total_parc,2); ?><hr><? echo number_format($summary_total_parcalter,2); ?><hr><? echo number_format($summary_total_parcspot,2); ?><hr><? echo number_format($summary_total_parcreject,2); ?></th>
	                                    <th align="right" width="70"><? echo number_format($totaldaytarget); ?>&nbsp;</th> 
	                                    <th align="right" width="70"><? echo number_format($totalGood_qty*100/$totaldaytarget,2)."%"; ?>&nbsp;</th> 
	                                    <th width="120">&nbsp;</th> 
	                                    <th></th>
	                            </table>	
	                        </div>    
	                    </div>
	                    <br />
	        </div><!-- end main div -->
	         <br/>
	         <fieldset style="width:950px">
				<label   ><b>No Production Line</b></label>
	        	<table id="table_header_1" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="100">Line No</th>
						
						<th width="100">Floor</th>
						<th width="75">Man Power</th>
						<th width="75">Operator</th>
						<th width="75">Helper</th>
	                    <th width="75">Working Hour</th>
						<th width="380">Remarks</th>
						
					</thead>
				</table>
				<div style="width:950px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <? 
					
					$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
					$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line");
					
					foreach($sql_active_line as $inf)
					{	
					
					   if(str_replace("","",$inf[csf('sewing_line')])!="")
					   {
							if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
					   }
					}
							//echo $actual_line_arr;die;
				$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
				 $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
				 if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
				
				 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$cbo_company_name and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0  $line_cond");
			
						$j=1; $location_array=array(); $floor_array=array();
						foreach( $dataArray as $row )
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
	                        	<td width="40"><? echo $j; ?></td>
	                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
	                            <td width="380"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
						$j++;
						}
					?>
						
					</table>
				</div>
			</fieldset>
		<?
		
		}
		if (str_replace("'","",trim($cbo_subcon))==2)
		{
				 
			ob_start();
			?>
			<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	        </style> 
	        <div style="width:2630px"> 
	            <table width="2600" cellspacing="0" > 
	                <tr style="border:none;">
	                        <td align="center" style="border:none; font-size:14px;">
	                            <b>Line Wise Hourly Production</b><br />
	                            Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                         
	                        </td>
	                  </tr> 
	            </table> 
	            <br />
	            <?
				$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
					
				if($db_type==0)
				  {
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.prod_reso_allo, a.floor_id, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line"; // echo $sql; //$txt_date
					
				}
				
				if($db_type==2)
				  {
						$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id, listagg(c.po_number,',') within group (order by po_number) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor, listagg(c.grouping,',') within group (order by grouping) as grouping, listagg(c.file_no,',') within group (order by file_no) as file_no,
						sum(d.production_qnty) as good_qnty, 
						sum(d.alter_qty) as alter_qnty,
						sum(d.spot_qty) as spot_qnty, 
						sum(d.reject_qty) as reject_qnty,";
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
						 $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
								sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
								sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,";
						}
						else
						{
					    	$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
							   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
							   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour";
																		
					$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_dtls d
						 where a.id=d.mst_id and a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.production_type=d.production_type $company_name $location $floor $line $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line";  
					
				}
				 //echo $sql;die;
					$result = sql_select($sql);
					$totalGood=0;$totalAlter=0;$totalSpot=0;$totalReject=0;$totalinputQnty=0;
					$production_data=array();
					
				
					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
						//total good,alter,reject qnty
						$totalGood += $row[csf("good_qnty")];
						$totalAlter += $row[csf("alter_qnty")];
						$totalSpot += $row[csf("spot_qnty")];
						$totalReject += $row[csf("reject_qnty")];
						$inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]];
						
						/*if($row[csf("prod_reso_allo")]==1)
						{
							$prod_line=$prod_reso_line_arr[$row[csf("sewing_line")]];
							foreach($prod_reso_arr as $line_id=>$line_no)
							{
								if($line_id==$prod_line)
								{
								}
							}
						}
						else
						{
						}*/
						
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
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("sewing_line")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
							for($h=$hour;$h<=$last_hour;$h++)
							{
								$bg=$start_hour_arr[$h];
								$bg_hour=$start_hour_arr[$h];
								//$end=substr(add_time($start_hour_arr[$h],60),0,8);
								$prod_hour="prod_hour".substr($bg_hour,0,2);
								$alter_hour="alter_hour".substr($bg_hour,0,2);
								$spot_hour="spot_hour".substr($bg_hour,0,2);
								$reject_hour="reject_hour".substr($bg_hour,0,2);
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
								
							}
						}
						else
						{
							$line_name=$lineArr[$row[csf('sewing_line')]];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("sewing_line")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
							for($h=$hour;$h<=$last_hour;$h++)
							{
								$bg=$start_hour_arr[$h];
								$bg_hour=$start_hour_arr[$h];
								//$end=substr(add_time($start_hour_arr[$h],60),0,8);
								$prod_hour="prod_hour".substr($bg_hour,0,2);
								$alter_hour="alter_hour".substr($bg_hour,0,2);
								$spot_hour="spot_hour".substr($bg_hour,0,2);
								$reject_hour="reject_hour".substr($bg_hour,0,2);
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
								$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
								
							}
						}
						
						
						
						
					}
					ksort($production_data);
					//var_dump($production_data);die;
					
	                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
	                    
	                $summary_total_parc=($totalGood/$grand_total)*100;
	                $summary_total_parcalter=($totalAlter/$grand_total)*100;
	                $summary_total_parcspot=($totalSpot/$grand_total)*100;
	                $summary_total_parcreject=($totalReject/$grand_total)*100;

				$subcon_prod_qnty_data = sql_select("select floor_id, location_id, line_id, order_id, sum(production_qnty) as prod_qnty from  subcon_gmts_prod_dtls where  production_type=2 group by floor_id, location_id, line_id, order_id");
				foreach($subcon_prod_qnty_data as $row)
				{
					$subcon_prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]]=$row[csf("prod_qnty")];
				}
				
				
				$i=1; $grand_total_good_sub=0; $grand_alter_good_sub=0; $grand_total_spot_sub=0; $grand_total_reject_sub=0;
				$first=1;
				$total_goods=array();
				$total_alter=array();
				$total_reject=array();
				$total_spot=array();
					
				if($db_type==0)
				{
					
					$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job as job_no, c.cust_style_ref, b.party_id, a.gmts_item_id, group_concat(distinct(a.order_id)) as order_id, group_concat(distinct(c.order_no)) as order_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
					sum(a.production_qnty) as good_qnty, 
					sum(a.alter_qnty) as alter_qnty,
					sum(a.spot_qnty) as spot_qnty, 
					sum(a.reject_qnty) as reject_qnty,";
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
				         $sql_subcon.="sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN a.hour<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
								sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
								sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					else
						{
					 $sql_subcon.="sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql_subcon.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
															
						$sql_subcon.=" from subcon_gmts_prod_dtls a, subcon_ord_mst b, subcon_ord_dtls c
						where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.floor_id, a.line_id, b.subcon_job order by a.floor_id, a.line_id "; //$txt_date production_date
					
				}//listagg(a.order_id,',') within group (order by order_id) as order_id, listagg(c.order_no,',') within group (order by order_no) as order_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
				//echo $sql_subcon;
				if($db_type==2)
				{
					$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job as job_no, b.party_id, a.gmts_item_id, listagg(a.order_id,',') within group (order by a.order_id) as order_id, listagg(c.order_no,',') within group (order by c.order_no) as order_no,listagg(c.cust_style_ref,',') within group (order by c.cust_style_ref) as style_ref_no,
					LISTAGG(CAST((case when a.supervisor is not null then a.supervisor end) AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.supervisor) as supervisor,
					sum(a.production_qnty) as good_qnty, 
					sum(a.alter_qnty) as alter_qnty,
					sum(a.spot_qnty) as spot_qnty, 
					sum(a.reject_qnty) as reject_qnty,";
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
				         $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
								sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
								sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					else
						{
					 $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql_subcon.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
															
						$sql_subcon.=" from subcon_gmts_prod_dtls a, subcon_ord_mst b, subcon_ord_dtls c
						where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job,  b.party_id, a.gmts_item_id order by a.floor_id, a.line_id "; //$txt_date production_date
					
				}
				 //echo $sql_subcon;die;
				
					$result_subcon = sql_select($sql_subcon);
					$totalGoodSub=0;$totalAlterSub=0;$totalRejectSub=0;$totalSpotSub=0;$totalinputQntySub=0;
					$production_subcon_data=array();
	                    foreach($result_subcon as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	                        
	                        //total good,alter,reject qnty
	                        $totalGoodSub += $row[csf("good_qnty")];
	                        $totalAlterSub += $row[csf("alter_qnty")];
	                        $totalSpotSub += $row[csf("spot_qnty")];
	                        $totalRejectSub += $row[csf("reject_qnty")];
							//echo $row[csf("supervisor")].'==';
							$inputQnty=$subcon_prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]];
							
							if($row[csf("prod_reso_allo")]==1)
							{
								//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
								$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('line_id')]]);
								$line_name="";
								foreach($line_resource_mst_arr as $resource_id)
								{
									$line_name.=$lineArr[$resource_id].", ";
								}
								$line_name=chop($line_name," , ");
								
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("line_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("subcon_job")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("party_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("gmts_item_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("order_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("order_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
								for($h=$hour;$h<=$last_hour;$h++)
								{
									$bg=$start_hour_arr[$h];
									$bg_hour=$start_hour_arr[$h];
									//$end=substr(add_time($start_hour_arr[$h],60),0,8);
									$prod_hour="prod_hour".substr($bg_hour,0,2);
									$alter_hour="alter_hour".substr($bg_hour,0,2);
									$spot_hour="spot_hour".substr($bg_hour,0,2);
									$reject_hour="reject_hour".substr($bg_hour,0,2);
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
									
								}
							}
							else
							{
								$line_name=$lineArr[$row[csf('sewing_line')]];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("line_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("subcon_job")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("party_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("gmts_item_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("order_id")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("order_no")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
								for($h=$hour;$h<=$last_hour;$h++)
								{
									$bg=$start_hour_arr[$h];
									$bg_hour=$start_hour_arr[$h];
									//$end=substr(add_time($start_hour_arr[$h],60),0,8);
									$prod_hour="prod_hour".substr($bg_hour,0,2);
									$alter_hour="alter_hour".substr($bg_hour,0,2);
									$spot_hour="spot_hour".substr($bg_hour,0,2);
									$reject_hour="reject_hour".substr($bg_hour,0,2);
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
									$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
								}
							}
						}
	                $grand_total_sub = $totalGoodSub+$totalAlterSub+$totalSpotSub+$totalRejectSub;
	                    
	                $summary_total_parc_sub=($totalGoodSub/$grand_total_sub)*100;
	                $summary_total_parcalter_sub=($totalAlterSub/$grand_total_sub)*100;
	                $summary_total_parcspot_sub=($totalSpotSub/$grand_total_sub)*100;
	                $summary_total_parcreject_sub=($totalRejectSub/$grand_total_sub)*100;
					
					$summary_total=0;$summary_good=0;$summary_alter=0;$summary_spot=0;$summary_reject=0; $summary_total_pergood=0;$summary_total_peralter=0;$summary_total_perspot=0;$summary_total_perreject=0;
					
					$summary_total=$grand_total+$grand_total_sub;
					
					$summary_good=$totalGood+$totalGoodSub;
					$summary_alter=$totalAlter+$totalAlterSub;
					$summary_spot=$totalSpot+$totalSpotSub;
					$summary_reject=$totalReject+$totalRejectSub;
					
					
					$summary_total_pergood=($summary_good/$summary_total)*100;
					$summary_total_peralter=($summary_alter/$summary_total)*100;
					$summary_total_perspot=($summary_spot/$summary_total)*100;
					$summary_total_perreject=($summary_reject/$summary_total)*100;
					//print_r($production_subcon_data);die;
				?>
	            <div>
	                <table class="" width="1100" border="0" rules="all" cellpadding="0" cellspacing="0" >
	                    <tr>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Summary (Production-Regular Order)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th> 
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($totalGood,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_parc,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($totalAlter,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcalter,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><? echo number_format($totalSpot,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcspot,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($totalReject,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_parcreject,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($grand_total,2); ?></th>  
	                                        <th>100%</th> 
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                        <td width="" >&nbsp;</td>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Summary (Production-Subcontract Order)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th> 
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($totalGoodSub,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_parc_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($totalAlterSub,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcalter_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><?  echo number_format($totalSpotSub,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcspot_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($totalRejectSub,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_parcreject_sub,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($grand_total_sub,2); ?></th>  
	                                        <th>100%</th> 
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                        <td width="" >&nbsp;</td>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Total Summary (Regular + Subcontract)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th>
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($summary_good,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_pergood,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($summary_alter,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_peralter,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><?  echo number_format($summary_spot,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_perspot,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($summary_reject,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_perreject,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($summary_total,2); ?></th> 
	                                        <th>100%</th>  
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                     </tr>
	                </table>
	            </div>
	            <br /> 
	                       
	                       
	                       
			   <?
	            $table_width=1740+($last_hour-$hour+1)*50;
	           ?>
	            <div style="width:200px; font-weight:bold">Production-Regular Order</div>
	            <div>
	            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                <thead> 	 	 	 	 	 	
	                    <tr height="50">
	                        <th width="30">Sl.</th>    
	                        <th width="90">Location</th>
	                        <th width="70">Floor</th>
	                        <th width="70">Line No</th>
	                        <th width="70">Number of Machine</th>
	                        <th width="60">Job No</th>
	                        <th width="110">Style Ref.</th>
	                        <th width="100">Internal Ref.</th>
	                        <th width="100">File No</th>
	                        <th width="100">Order No</th>
	                        <th width="60">Buyer</th>
	                        <th width="170">Item</th>
	                        <th width="70">Input Qnty</th>
	                        <th width="70">Hourly Target</th>
	                        <th width="80">Quality</th>
	                     	 <?
	                        
	                            for($k=$hour+1; $k<=$last_hour+1; $k++)
	                            {
	                            ?>
	                              <th width="50" style="vertical-align:middle"><div class="block_div"><? echo substr(date("h:i a",strtotime($start_hour_arr[$k])),0,8);//substr($start_hour_arr[$k],0,5);  //echo substr($start_hour_arr[$k],0,5);   ?></div></th>
	                            
	                            <?	
	                            }
	                         ?>
	                           	 	 	 	
	                        <th width="70">Total</th>
	                        <th width="70">In %</th>
	                        <th width="70">Day Target</th>
	                        <th width="70">Line Achv %</th>
	                        <th width="120">Supervisor</th> 
	                        <th width="">Remarks</th> 
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body" >
	                <?
	                  // print_r($result);die;$production_data[$row[csf("prod_reso_allo")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("job_no")]]["po_number"]
					  /*
					  $lineArr = return_library_array("select id,line_name from lib_sewing_line order by line_name","id","line_name"); 
						$prod_reso_line_arr=return_library_array( "select a.id, line_number from prod_resource_mst",'id','line_number');
						if($db_type==2)
						{
							$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.line_name","id","line_name");
						}
						else if($db_type==0)
						{
							$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.line_name","id","line_name");
						}
					  */
					  
	                    $totalGoodQnt=0; $totalAlterQnt=0; $totalSpotQnt=0; $totalRejectQnt=0;
						//print_r($production_data);die;
	                    foreach($production_data as $flowre_id=>$value)
	                    {
							ksort($value);
							foreach($value as $line_name=>$val)
							{
								foreach($val as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									//total good,alter,reject qnty
									$totalGood_qty += $row[("good_qnty")];
									$totalAlter_qty += $row[("alter_qnty")];
									$totalSpot_qty += $row[("spot_qnty")];
									$totalReject_qty += $row[("reject_qnty")];
									$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]];
									$order_number=implode(',',array_unique(explode(",",$row[("po_number")])));
									$grouping=implode(',',array_unique(explode(",",$row[("grouping")])));
									$file_no=implode(',',array_unique(explode(",",$row[("file_no")])));
								?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="30" align="center"><? echo $i; ?></td>    
										<td width="90" align="center"><p><? echo $location_library[$row[("location")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $line_name;// $line_library[$row[csf("sewing_line")]];$row[csf("line_name")];; ?></p></td>
										<td width="70" align="center"><p><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['active_machine']; ?></p></td>
										<td width="60" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
										<td width="110" align="center"><p><? echo $row[("style_ref_no")]; ?></p></td>
										<td width="100" align="center"><p><? echo $grouping; ?></p></td>
										<td width="100" align="center"><p><? echo $file_no; ?></p></td>
										<td width="100" align="center"><p><? echo $order_number; ?></p></td>
										<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
										<td width="170" align="center"><p>
											<?
											$gmt_item_id_string=chop($row[("item_number_id")],",");
											$gmt_item_id_arr=explode(",",$gmt_item_id_string);
											$gmt_item_arr = array();
											foreach($gmt_item_id_arr as $s_gmt_item)
											{
												$gmt_item_arr[$s_gmt_item]=$garments_item[$s_gmt_item];
											}
											 echo implode(",",$gmt_item_arr);
											 echo $garments_item[$row[("item_number_id")]]; 
											 ?>																		 	
										</p></td> 
										<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
										<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; ?>&nbsp;</td>
										<td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
										
										<?
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
											
											
											$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
											$summary_total_parc=($totalGood/$grand_total)*100;
											$summary_total_parcalter=($totalAlter/$grand_total)*100;
											$summary_total_parcspot=($totalSpot/$grand_total)*100;
											$summary_total_parcreject=($totalReject/$grand_total)*100;
											
											?>
											<td width="50" align="right"><? echo $row[($prod_hour)]; ?>&nbsp;<hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
											<?
											$total_goods[$prod_hour]+= $row[($prod_hour)];
											$total_alter[$prod_hour]+= $row[($alter_hour)];
											$total_reject[$prod_hour]+= $row[($reject_hour)];
											$total_spot[$prod_hour]+= $row[($spot_hour)];	   
										}
										
										?>	 	 	 	
										<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>
										<?
											$totalQnty = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
											$good_qnty_percentage = ($row[("good_qnty")]/$totalQnty)*100;
											$alter_qnty_percentage = ($row[("alter_qnty")]/$totalQnty)*100;
											$spot_qnty_percentage = ($row[("spot_qnty")]/$totalQnty)*100;
											$reject_qnty_percentage = ($row[("reject_qnty")]/$totalQnty)*100
										?>
										<td width="70" align="right"><? echo number_format($good_qnty_percentage,2); ?><hr><? echo number_format($alter_qnty_percentage,2); ?><hr><? echo number_format($spot_qnty_percentage,2); ?><hr><? echo number_format($reject_qnty_percentage,2); ?></td>
										
										<td width="70" align="right"><? //echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[csf('production_date')])]['tpd']; 
										echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>&nbsp;</td>
										<td width="70" align="right">
											<? $line_achive=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']*100;
											echo number_format($line_achive,2)."%"; ?>&nbsp;</td>
										<? $expArr = explode(",",$row["supervisor"]); ?>
										<td width="120"><? echo $expArr[count($expArr)-1]; ?></td>  
										<td width="" align="center">
										<?  
										
										 $total_po_id=explode(",",$row[("po_break_down_id")]);
										 $all_po_id=implode("*",$total_po_id);
										 $line_number_id=explode(",",$row[('sewing_line')]);
										 $line_number_id=implode("*",$line_number_id);
											
										?>
										
										<input type="button" onclick="show_line_remarks(<? echo $cbo_company_name; ?>,'<? echo $all_po_id; ?>','<? echo $row[("floor_id")]; ?>','<? echo $line_number_id; ?>',<? echo $txt_date; ?>,'remarks_popup')" value="View"  class="formbutton"/></td>  
									 </tr>
									<?
									$i++;
									$totalinputQnty+=$inputQnty;
									//$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
									//$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
									$totallineachiveper+=$line_achive;
									if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
									{
										$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
										$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
										$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
									}
								}
							}
	                }
	                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
	                    
	                $summary_total_parc=($totalGood/$grand_total)*100;
	                $summary_total_parcalter=($totalAlter/$grand_total)*100;
	                $summary_total_parcspot=($totalSpot/$grand_total)*100;
	                $summary_total_parcreject=($totalReject/$grand_total)*100;
	                ?>
	            </table>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="rpt_table_footer1" >
	                <tfoot>
	                    <th width="30">&nbsp;</th>    
	                    <th width="90">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="110">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="170" align="right">Grand Total: </th>  
	                    <th width="70"><? echo $totalinputQnty; ?></th> 
	                    <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
	                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
	                      <?
	                                      for($k=$hour; $k<=$last_hour; $k++)
	                                       {
											    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										
											  ?>
											   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
										      <?
											     
										   }
	                                    
	                                    ?>                       
	                  
	                    <th align="right" width="70"><? echo $totalGood_qty; ?><hr><? echo $totalAlter_qty; ?><hr><? echo $totalSpot_qty; ?><hr><? echo $totalReject_qty; ?></th>
	                    <th align="right" width="70"><? echo number_format($summary_total_parc,2); ?><hr><? echo number_format($summary_total_parcalter,2); ?><hr><? echo number_format($summary_total_parcspot,2); ?><hr><? echo number_format($summary_total_parcreject,2); ?></th>
	                    <th align="right" width="70"><? echo number_format($totaldaytarget); ?>&nbsp;</th> 
	                    <th align="right"width="70"><? echo number_format($totalGood_qty*100/$totaldaytarget,2)."%"; ?>&nbsp;</th> 
	                    <th width="120">&nbsp;</th> 
	                    <th>&nbsp;</th> 
	                </tfoot>
	            </table>
	           </div>    
	        </div>
	        <br />
	        <br />
	        <div style="width:200px; font-weight:bold">Production-Subcontract Order</div>
	            <div>
	            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
	                <thead> 	 	 	 	 	 	
	                    <tr height="50">
	                        <th width="30">Sl.</th>    
	                        <th width="90">Location</th>
	                        <th width="70">Floor</th>
	                        <th width="70">Line No</th>
	                        <th width="70">Number of Machine</th>
	                        <th width="60">Job No</th>
	                        <th width="110">Style Ref.</th>
	                        <th width="100">Order No</th>
	                        <th width="60">Buyer</th>
	                        <th width="170">Item</th>
	                        <th width="70">Input Qnty</th>
	                        <th width="70">Hourly Target</th>
	                        <th width="80">Quality</th>
	                        
	                        	 <?
	                        
	                            for($k=$hour+1; $k<=$last_hour+1; $k++)
	                            {
	                            ?>
	                              <th width="50" style="vertical-align:middle"><div class="block_div"><? echo substr(date("h:i a",strtotime($start_hour_arr[$k])),0,8);//substr($start_hour_arr[$k],0,5);  //echo substr($start_hour_arr[$k],0,5);   ?></div></th>
	                            
	                            <?	
	                            }
	                        ?>
	                       	 	 	 	
	                        <th width="70">Total</th>
	                        <th width="70">In %</th>
	                        <th width="70">Day Target</th>
	                        <th width="70">Line Achv %</th>
	                        <th width="">Supervisor</th> 
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body1" >
	                <? 
	                    $totalGoodSubSub=0; $totalAlterSubSub=0; $totalSpotSubSub=0; $totalRejectSubSub=0;//$prod_reso_arr_sub=array();
	                    foreach($production_subcon_data as $floor_id=>$value)
	                    {
							ksort($value);
							foreach($value as $line_name=>$job_data)
							{
								foreach($job_data as $job_no=>$row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									//total good,alter,spot,reject qnty
									$totalGoodSubSub += $row[("good_qnty")];
									$totalAlterSubSub += $row[("alter_qnty")];
									$totalSpotSubSub += $row[("spot_qnty")];
									$totalRejectSubSub += $row[("reject_qnty")];
									
									$inputQntySub=$subcon_prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("line_id")]][$row[("po_break_down_id")]];
									
									$order_num="";
									$ex_po=array_unique(explode(',',$row[("po_number")]));
									foreach($ex_po as $po_no)
									{
										if($order_num=="") $order_num=$po_no; else $order_num.=','.$po_no;
									}

									$style_ref_no="";
									$ex_style=array_unique(explode(',',$row[("style_ref_no")]));
									foreach($ex_style as $style_no)
									{
										if($style_ref_no=="") $style_ref_no=$style_no; else $style_ref_no.=','.$style_no;
									}
									
									?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="30" align="center"><? echo $i; ?></td>    
										<td width="90" align="center"><p><? echo $location_library[$row[("location")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $line_name; ?></p></td>
										<td width="70" align="center"><p><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['active_machine']; ?></p></td>
										<td width="60" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
										<td width="110" align="center"><p><? echo $style_ref_no; ?></p></td>
										<td width="100" align="center"><p><? echo $order_num; ?></p></td>
										<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
										<td width="170" align="center"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td> 
										<td width="70" align="right"><p><? echo $inputQntySub; ?></p></td> 
										<td width="70" align="right"><? echo $prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
										
										//$prod_resource_array[$row['line_id']][change_date_format($row['production_date'])]['target_per_hour']; ?>&nbsp;</td>
										<td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
										
										<?
										
										for($k=$hour; $k<=$last_hour; $k++)
										{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
										$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
										$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										$totalGoodQnt_sub += $row[($prod_hour)];
										$totalAlterQnt_sub += $row[($alter_hour)];
										$totalSpotQnt_sub += $row[($spot_hour)];
										$totalRejectQnt_sub +=$row[($reject_hour)];
										
										
										$grand_total_sub = $totalGoodSubSub+$totalAlterSubSub+$totalSpot+$totalReject;
										$summary_total_parc_sub=($totalGood/$grand_total)*100;
										$summary_total_parcalter__sub=($totalAlter/$grand_total)*100;
										$summary_total_parcspot=($totalSpot/$grand_total)*100;
										$summary_total_parcreject_sub=($totalReject/$grand_total)*100;
										
										?>
										<td width="50" align="right"><? echo $row[($prod_hour)]; ?>&nbsp;<hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
										<?
										$sub_total_goods[$prod_hour]+= $row[($prod_hour)];
										$sub_total_alter[$prod_hour]+= $row[($alter_hour)];
										$sub_total_reject[$prod_hour]+= $row[($reject_hour)];
										$sub_total_spot[$prod_hour]+= $row[($spot_hour)];	   
										}
										
										?>
										
										<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>
										<?
										$totalQntySub = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
										$good_qnty_percentage_sub = ($row[("good_qnty")]/$totalQntySub)*100;
										$alter_qnty_percentage_sub = ($row[("alter_qnty")]/$totalQntySub)*100;
										$spot_qnty_percentage_sub = ($row[("spot_qnty")]/$totalQntySub)*100;
										$reject_qnty_percentage_sub = ($row[("reject_qnty")]/$totalQntySub)*100
										?>
										<td width="70" align="right"><? echo number_format($good_qnty_percentage_sub,2); ?><hr><? echo number_format($alter_qnty_percentage_sub,2); ?><hr><? echo number_format($spot_qnty_percentage_sub,2); ?><hr><? echo number_format($reject_qnty_percentage_sub,2); ?></td>
										<td width="70" align="right"><? echo $prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']; ?>&nbsp;</td>
										<td width="70" align="right">
										<? $line_achive_sub=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']*100;
										echo number_format($line_achive_sub,2)."%"; ?>&nbsp;</td>
										<? $expArr = explode(",",$row["supervisor"]); ?>
										<td width=""><? echo $expArr[count($expArr)-1]; ?></td>  
									</tr>
									<?
									$i++;
									$totalinputQntySub+=$inputQntySub;
									//$totaltargetperhouresub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
									//$totaldaytargetsub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd'];
									$totallineachivepesubr+=$line_achive;
									
									if($duplicate_array[$row['prod_reso_allo']][$row['sewing_line']]=="")
									{
										$totaltargetperhouresub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
										$totaldaytargetsub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd'];
										$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
									}
								}
								
							}
	                        
	                    
	                }
	                $grand_total_sub = $totalGoodSub+$totalAlterSub+$totalSpotSub+$totalRejectSub;
	                    
	                $summary_total_parc_sub=($totalGoodSub/$grand_total_sub)*100;
	                $summary_total_parcalter_sub=($totalAlterSub/$grand_total_sub)*100;
	                $summary_total_parcspot_sub=($totalSpotSub/$grand_total_sub)*100;
	                $summary_total_parcreject_sub=($totalRejectSub/$grand_total_sub)*100;
	                ?>
	            </table>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="rpt_table_footer1" >
	                <tfoot>
	                    <th width="30">&nbsp;</th>    
	                    <th width="90">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="110">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="170" align="right">Grand Total: </th>  
	                    <th  width="70"><? echo $totalinputQntySub; ?></th> 
	                    <th width="70"><? echo $totaltargetperhouresub; ?>&nbsp;</th> 
	                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
						<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<th align="right" width="50"><? echo $sub_total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_reject[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_spot[$prod_hour]; ?>&nbsp;</th> 
								<?
							}  
	                    ?>                            
	                    <th align="right" width="70"><? echo $totalGoodSubSub; ?><hr><? echo $totalAlterSubSub; ?><hr><? echo $totalSpotSubSub; ?><hr><? echo $totalRejectSubSub; ?></th>
	                    <th align="right" width="70"><? echo number_format($summary_total_parc_sub,2); ?><hr><? echo number_format($summary_total_parcalter_sub,2); ?><hr><? echo number_format($summary_total_parcspot_sub,2); ?><hr><? echo number_format($summary_total_parcreject_sub,2); ?></th>
	                    <th align="right" width="70"><? echo number_format($totaldaytargetsub); ?>&nbsp;</th> 
	                    <th align="right" width="70"><? echo number_format($totalGoodSubSub*100/$totaldaytargetsub,2)."%"; ?>&nbsp;</th> 
	                    <th width="">&nbsp;</th> 
	                </tfoot>
	            </table>	
	           </div>    
	        </div>
	      <br />
	      <br />
	    	</div><!-- end main div -->
	     	<br/>
	         <fieldset style="width:950px">
				<label   ><b>No Production Line</b></label>
	        	<table id="table_header_1" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="100">Line No</th>
						
						<th width="100">Floor</th>
						<th width="75">Man Power</th>
						<th width="75">Operator</th>
						<th width="75">Helper</th>
	                    <th width="75">Working Hour</th>
						<th width="380">Remarks</th>
						
					</thead>
				</table>
				<div style="width:950px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <? 
					
					$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
					$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line
					union all
					select line_id,sum(production_qnty) as total_production from subcon_gmts_prod_dtls  where production_date=".$txt_date." and production_type=2 and status_active=1 and is_deleted=0 group by  line_id");
					
					foreach($sql_active_line as $inf)
					{	
					
					   if(str_replace("","",$inf[csf('sewing_line')])!="")
					   {
							if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
					   }
					}
							//echo $actual_line_arr;die;
				$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
				 $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
				 if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";

				 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$cbo_company_name and b.pr_date=".$txt_date." and d.shift_id=1  and a.is_deleted=0 and b.is_deleted=0 $line_cond");
						$j=1; $location_array=array(); $floor_array=array();
						foreach( $dataArray as $row )
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
	                        	<td width="40"><? echo $j; ?></td>
	                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
	                            <td width="380"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
							$j++;
							
						}
						
				
					?>
						
					</table>
				</div>
			</fieldset>
			<?
		}
	}
	else if($type==4) // for Ha-meem
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
		if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
		if($cbo_floor=="") $floor="";else $floor=" and a.floor_id in($cbo_floor)";
		if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";
		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
		if(str_replace("'","",trim($txt_internal_no))=="") $internal_no_cond=""; else $internal_no_cond=" and c.grouping=$txt_internal_no";
		if(str_replace("'","",trim($txt_file_no))=="") $file_no_cond=""; else $file_no_cond=" and c.file_no=$txt_file_no";
		
		$prev_date = date('d-M-Y', strtotime('-1 day', strtotime(str_replace("'","",trim($txt_date)))));
		$prod_resource_array=array();
		$dataArray=sql_select("SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id,b.active_machine,b.operator,b.helper,b.man_power from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and b.pr_date=$txt_date");

		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['active_machine']=$row[csf('active_machine')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['operator']=$row[csf('operator')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['helper']=$row[csf('helper')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['man_power']=$row[csf('man_power')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['working_hour']=$row[csf('working_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
		}
		//var_dump($prod_resource_array);//change_date_format($txt_date,'yyyy-mm-dd')
		$start_time_arr=array();
		if($db_type==0)
		{
			$start_time_data_arr=sql_select("SELECT company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($cbo_company_name) and  shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
		}
		else
		{
			$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($cbo_company_name) and  shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");	
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
		//		echo $hour;die;
			$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
			
			$start_hour=$prod_start_hour;
			$start_hour_arr[$hour]=$start_hour;
			for($j=$hour;$j<$last_hour;$j++)
			{				
				$start_hour=add_time($start_hour,60);
				$start_hour_arr[$j+1]=substr($start_hour,0,5);
				//$start_hour_arr[$j+1]=substr(date("h:i a",strtotime($start_hour)),0,5);
			}
		    $start_hour_arr[$j+1]='23:59';
		    //$start_hour_arr[$j+1]=date("h:i a",strtotime('23:59'));
			//print_r($start_hour_arr);
		//var_dump($prod_resource_array);
		
		//var_dump($prod_qnty_data_arr);die;
		//echo $prod_qnty_data_arr[1][1][1][88][3533].jahid;die;
				
		if (str_replace("'","",trim($cbo_subcon))==2)
		{			 
			ob_start();
			?>
			<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	        </style> 
	        <div style="width:2630px"> 
	            <table width="2600" cellspacing="0" > 
	                <tr style="border:none;">
	                        <td align="center" style="border:none; font-size:14px;">
	                            <b>Line Wise Hourly Production</b><br />
	                            Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                         
	                        </td>
	                  </tr> 
	            </table> 
	            <br />
	            <?
				$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
					
				if($db_type==0)
				{
					$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.prod_reso_allo, a.floor_id, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line"; // echo $sql; //$txt_date
					
				}
				
				if($db_type==2)
				{
					$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.id as job_id, b.job_no_prefix_num, b.job_no,b.brand_id, b.style_ref_no, b.buyer_name,b.season_buyer_wise,b.season_year, a.item_number_id, c.po_quantity,c.plan_cut,c.id as po_id,
						sum(d.production_qnty) as good_qnty, 
						sum(d.alter_qty) as alter_qnty,
						sum(d.spot_qty) as spot_qnty, 
						sum(d.reject_qty) as reject_qnty,";
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
						 $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
								sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
								sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,";
						}
						else
						{
					    	$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
							   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
							   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour,";
						}
						$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN d.alter_qty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.reject_qty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.spot_qty else 0 END) AS $spot_hour";
																		
					$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_dtls d
						 where a.id=d.mst_id and a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.production_type=d.production_type $company_name $location $floor $line $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.brand_id,b.id,b.job_no_prefix_num,b.style_ref_no,b.buyer_name,b.season_buyer_wise,b.season_year, a.item_number_id, c.po_quantity,c.plan_cut,c.id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line";  
					
				}
				// echo $sql;die;
				$result = sql_select($sql);
				$totalGood=0;$totalAlter=0;$totalSpot=0;$totalReject=0;$totalinputQnty=0;
				$production_data=array();
				
				$po_chk_arr = array();
				$po_wise_job_arr = array();
				$job_id_arr = array();
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					//total good,alter,reject qnty
					$totalGood += $row[csf("good_qnty")];
					$totalAlter += $row[csf("alter_qnty")];
					$totalSpot += $row[csf("spot_qnty")];
					$totalReject += $row[csf("reject_qnty")];
					$job_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
										
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
					}
					else
					{
						$line_name=$lineArr[$row[csf('sewing_line')]];
					}
					$line_name=chop($line_name," , ");
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("sewing_line")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["brand_id"]=$row[csf("brand_id")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["season"]=$row[csf("season_buyer_wise")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["season_year"]=$row[csf("season_year")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
					for($h=$hour;$h<=$last_hour;$h++)
					{
						$bg=$start_hour_arr[$h];
						$bg_hour=$start_hour_arr[$h];
						//$end=substr(add_time($start_hour_arr[$h],60),0,8);
						$prod_hour="prod_hour".substr($bg_hour,0,2);
						$alter_hour="alter_hour".substr($bg_hour,0,2);
						$spot_hour="spot_hour".substr($bg_hour,0,2);
						$reject_hour="reject_hour".substr($bg_hour,0,2);
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
						
					}
									

					if($po_chk_arr[$row[csf("po_id")]]=="")
					{
						// $production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_quantity"]+=$row[csf("po_quantity")];
						// $production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["plan_cut"]+=$row[csf("plan_cut")];
						
						$po_chk_arr[$row[csf("po_id")]] = $row[csf("po_id")];
					}
					$po_wise_job_arr[$row[csf("po_id")]]=$row[csf("job_no")];
															
				}
				ksort($production_data);
				//var_dump($production_data);die;
				$job_id_cond = where_con_using_array($job_id_arr,0,"job_id");
				$po_id_arr = return_library_array("SELECT id,id from wo_po_break_down where status_active=1 $job_id_cond","id","id"); 
				$po_id_cond = where_con_using_array($po_id_arr,0,"po_break_down_id");
               	// ===============================================================
				$sql="SELECT floor_id, location, production_type, prod_reso_allo, sewing_line, po_break_down_id,
				(case when production_type = 4 then MIN (production_date) end) AS first_input_date, 
				sum(case when production_type=1 and production_date=$txt_date then production_quantity else 0 end) as cur_cut_qty,
				sum(case when production_type=1 then production_quantity else 0 end) as tot_cut_qty,
				sum(case when production_type=4 and production_date=$txt_date then production_quantity else 0 end) as cur_in_qty,
				sum(case when production_type=4 and production_date<=$txt_date then production_quantity else 0 end) as tot_in_qty,
				sum(case when production_type=5 and production_date<=$txt_date then production_quantity else 0 end) as tot_out_qty, 
				sum(case when production_type=5 and production_date='$prev_date' then production_quantity else 0 end) as prev_day_out_qty 
				from pro_garments_production_mst 
				where status_active=1 and production_type in(1,4,5) $po_id_cond 
				group by floor_id, location, prod_reso_allo, sewing_line, po_break_down_id,production_type";
				// echo $sql;die();
				$result = sql_select($sql);
				foreach($result as $row)
				{
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
					}
					else
					{
						$line_name=$lineArr[$row[csf('sewing_line')]];
					}

					$input_data_array[$po_wise_job_arr[$row[csf("po_break_down_id")]]]['today_cut']+=$row[csf("cur_cut_qty")];
					$input_data_array[$po_wise_job_arr[$row[csf("po_break_down_id")]]]['total_cut']+=$row[csf("tot_cut_qty")];
					$input_data_array[$po_wise_job_arr[$row[csf("po_break_down_id")]]]['tot_out_qty']+=$row[csf("tot_out_qty")];
					$input_data_array[$po_wise_job_arr[$row[csf("po_break_down_id")]]]['prev_day_out']+=$row[csf("prev_day_out_qty")];


					$input_data_array[$row[csf("floor_id")]][$line_name][$po_wise_job_arr[$row[csf("po_break_down_id")]]]['today_in']+=$row[csf("cur_in_qty")];
					$input_data_array[$row[csf("floor_id")]][$line_name][$po_wise_job_arr[$row[csf("po_break_down_id")]]]['total_in']+=$row[csf("tot_in_qty")];

					$input_data_array[$row[csf("floor_id")]][$line_name][$po_wise_job_arr[$row[csf("po_break_down_id")]]]['input_date']=$row[csf("first_input_date")];
				}		

				// print_r($input_data_array)	;die();	

				// ============================= order qty =====================
				$po_id_cond = where_con_using_array($po_id_arr,0,"id");
				$sql = "SELECT job_no_mst, po_quantity,plan_cut from wo_po_break_down where status_active=1 $po_id_cond";
				$res = sql_select($sql);
				$order_data_array = array();
				foreach ($res as $val) 
				{
					$order_data_array[$val['JOB_NO_MST']]['po_quantity'] += $val['PO_QUANTITY'];
					$order_data_array[$val['JOB_NO_MST']]['plan_cut'] += $val['PLAN_CUT'];
				}

				// ========================== lay qty ========================
				$po_id_cond = where_con_using_array($po_id_arr,0,"b.order_id");
				$sql = "SELECT a.job_no,sum(case when a.entry_date=$txt_date then b.size_qty else 0 end) as today_lay,sum(case when a.entry_date<=$txt_date then b.size_qty else 0 end) as total_lay from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $po_id_cond group by a.job_no";
				// echo $sql;
				$res = sql_select($sql);
				$lay_qty_array = array();
				foreach ($res as $val) 
				{
					$lay_qty_array[$val['JOB_NO']]['today_lay'] += $val['TODAY_LAY'];
					$lay_qty_array[$val['JOB_NO']]['total_lay'] += $val['TOTAL_LAY'];
				}

				// ============================ smv ==============================
				$job_ids = implode(",", $job_id_arr);
				$sql = "SELECT job_no,gmts_item_id,smv_pcs from wo_po_details_mas_set_details where job_id in($job_ids)";
				// echo $sql;
				$res = sql_select($sql);
				$item_smv_arr = array();
				foreach ($res as $val) 
				{
					$item_smv_arr[$val['JOB_NO']][$val['GMTS_ITEM_ID']] = $val['SMV_PCS'];
				}
				// print_r($item_smv_arr);die();	
									
	            $table_width=1730+($last_hour-$hour+1)*50;
	           ?>	            
	            <div>
		            <!-- =======================================================================/
		            /								Header Part 								/
		            /======================================================================== -->
		            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
		                <thead> 	 	 	 	 	 	
		                    <tr height="50">
		                        <th width="30"><p>Sl.</p></th>    
		                        <th width="60"><p>Line</p></th>
		                        <th width="100"><p>Buyer</p></th>
		                        <th width="100"><p>Brand</p></th>
		                        <th width="100"><p>Season/Year</p></th>
		                        <th width="100"><p>Style</p></th>
		                        <th width="100"><p>Item</p></th>
		                        <th width="60"><p>Manpower</p></th>
		                        <th width="60"><p>SMV</p></th>
		                        <th width="60"><p>Order Qty</p></th>
		                        <th width="60"><p>Plan Qty</p></th>
		                        <th width="60"><p>Input Date</p></th>
		                        <th width="60"><p>Day Cutting</p></th>
		                        <th width="60"><p>TTL Cutting</p></th>
		                        <th width="60"><p>Day Input</p></th>
		                        <th width="60"><p>Total Input</p></th>
		                        <th width="60"><p>Day Target</p></th>
		                        <th width="60"><p>Target/Hour</p></th>
		                     	<?
		                            for($k=$hour+1; $k<=$last_hour+1; $k++)
		                            {
			                            ?>
			                              	<th width="50" style="vertical-align:middle"><div class="block_div"><? echo substr(date("h:i a",strtotime($start_hour_arr[$k])),0,8);//substr($start_hour_arr[$k],0,5);  //echo substr($start_hour_arr[$k],0,5);   ?></div></th>
			                            
			                            <?	
		                            }
		                        ?>
		                           	 	 	 	
		                        <th width="60"><p>Day Production</p></th>
		                        <th width="60"><p>Style Total</p></th>
		                        <th width="60"><p>Prev Day Prod</p></th>
		                        <th width="60"><p>Day Aced %</p></th>
		                        <th width="60"><p>Fail(+/-)</p></th> 
		                        <th width="60"><p>Day Work Hour</p></th> 
		                        <th width="60"><p>Averag Per HR</p></th> 
		                        <th width="60"><p>Line Balance</p></th> 
		                     </tr>
		                </thead>
		            </table>
		            <!-- =======================================================================/
		            /									Body Part								/
		            /======================================================================== -->
		            <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
			            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body" >
			                <?
			                $i=1;
							$first=1;
							$total_goods=array();
							$gr_man_power = 0;
							$gr_order_qty = 0;
							$gr_plan_cut_qty = 0;
							$gr_day_cut = 0;
							$gr_day_tot_cut = 0;
							$gr_day_in = 0;
							$gr_day_tot_in = 0;
							$gr_day_target = 0;
							$gr_target_per_hr = 0;
							$gr_day_tot_prod = 0;
							$gr_style_tot = 0;
							$gr_prev_day_tot = 0;
							$gr_working_hr = 0;
							$gr_line_bal = 0;
							$line_check_arr = array();
		                    foreach($production_data as $flowre_id=>$floor_data)
		                    {
								ksort($floor_data);
								foreach($floor_data as $line_name=>$line_data)
								{
									foreach($line_data as $job_no=>$row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$today_cut = $lay_qty_array[$job_no]['today_lay'];
										$total_cut = $lay_qty_array[$job_no]['total_lay'];
										$tot_out_qty = $input_data_array[$job_no]['tot_out_qty'];
										$prev_day_out = $input_data_array[$job_no]['prev_day_out'];

										$today_in = $input_data_array[$flowre_id][$line_name][$job_no]['today_in'];
										$total_in = $input_data_array[$flowre_id][$line_name][$job_no]['total_in'];

										$input_date = $input_data_array[$flowre_id][$line_name][$job_no]['input_date'];

										$day_target = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
										$working_hour = $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['working_hour'];
										$day_achive = ($day_target>0) ? ($row['good_qnty']/$day_target)*100 : 0;
										$fail = $day_target - $row['good_qnty'];
										$avg_per_hour = ($working_hour>0) ? ($row['good_qnty']/$working_hour) : 0;
										$line_bal = $total_in - $tot_out_qty;

										$gmt_item_id_string=chop($row[("item_number_id")],",");
										$gmt_item_id_arr=explode(",",$gmt_item_id_string);
										$smv = '';
										foreach($gmt_item_id_arr as $s_gmt_item)
										{
											$smv.= ($smv=="") ? $item_smv_arr[$job_no][$s_gmt_item] : "/".$item_smv_arr[$job_no][$s_gmt_item];
										}
																				

									?>
										<tr bgcolor="<?=$bgcolor; ?>" onclick="change_color('tr_1nd<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_1nd<?=$i; ?>" >
											<td width="30" align="center"><?=$i; ?></td> 
											<td width="60" align="center"><p><?=$line_name;?></p></td>   
											<td width="100" align="center"><p><?=$buyer_short_library[$row[("buyer_name")]]; ?></p></td>
											<td width="100" align="center"><p><?=$buyer_brand_arr[$row[("brand_id")]]; ?></p></td>
											<td width="100" align="center"><p><?=$buyer_season_arr[$row["season"]]."/".$row["season_year"];?></p></td>
											<td width="100" align="center"><p><?=$row[("style_ref_no")];?></p></td>
											<td width="100" align="center">
												<p>														
													<?
													$gmt_item_id_string=chop($row[("item_number_id")],",");
													$gmt_item_id_arr=explode(",",$gmt_item_id_string);
													$gmt_item_arr = array();
													foreach($gmt_item_id_arr as $s_gmt_item)
													{
														$gmt_item_arr[$s_gmt_item]=$garments_item[$s_gmt_item];
													}
													echo implode(",",$gmt_item_arr);
													echo $garments_item[$row[("item_number_id")]]; 
													?>																
												</p>
											</td>
											<td width="60" align="center">
												<p>
													<?=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['man_power']; ?>
														
												</p>
											</td>
											<td width="60" align="center"><p><?=$smv;?></p></td>
											<td width="60" align="right"><p><?=number_format($order_data_array[$job_no]['po_quantity'],0); ?></p></td>
											<td width="60" align="right"><p><?=number_format($order_data_array[$job_no]['plan_cut'],0); ?></p></td>
											<td width="60" align="center"><p><?=change_date_format($input_date); ?></p></td>
											<td width="60" align="right"><p><?=number_format($today_cut,0); ?></p></td>
											<td width="60" align="right"><p><?=number_format($total_cut,0); ?></p></td>
											<td width="60" align="right"><p><?=number_format($today_in,0); ?></p></td> 
											<td width="60" align="right"><p><p><?=number_format($total_in,0); ?></p></p></td> 
											<td width="60" align="right"><?=number_format($day_target,0); ?></td>
											<td width="60" align="right"><?=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; ?>&nbsp;</td>
											
											<?
											$day_tot = 0;
											for($k=$hour; $k<=$last_hour; $k++)
											{
												$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
												
												?>
												<td width="50" align="right"><?=number_format($row[($prod_hour)],0); ?></td> 
												<?
												$day_tot += $row[($prod_hour)];
												$total_goods[$prod_hour]+= $row[($prod_hour)];  
											}
											
											?>	 	 	 	
											<td width="60" align="right"><?=number_format($day_tot,0);?></td>
											<td width="60" align="right"><?=number_format($tot_out_qty,0);?></td>
											<td width="60" align="right"><?=number_format($prev_day_out,0);?></td>
											<td width="60" align="right"><?=number_format($day_achive,2);?>%</td>
											<td width="60" align="right"><?=number_format($fail,0);?></td>
											<td width="60" align="right"><?=number_format($working_hour,0);?></td>
											<td width="60" align="right"><?=number_format($avg_per_hour,2);?></td>
											<td width="60" align="right"><?=number_format($line_bal,0);?></td>
											
										 </tr>
										<?
										$i++;		
										if($line_check_arr[$row[('sewing_line')]]=="")								
										{
											$gr_man_power += $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['man_power'];
											$gr_target_per_hr += $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
											$gr_day_target += $day_target;

											$line_check_arr[$row[('sewing_line')]] = $row[('sewing_line')];
										}
										
										$gr_order_qty += $order_data_array[$job_no]['po_quantity'];
										$gr_plan_cut_qty += $order_data_array[$job_no]['plan_cut'];
										$gr_day_cut += $today_cut;
										$gr_day_tot_cut += $total_cut;
										$gr_day_in += $today_in;
										$gr_day_tot_in += $total_in;
										$gr_day_tot_prod += $day_tot;
										$gr_style_tot += $tot_out_qty;
										$gr_prev_day_tot += $prev_day_out;
										$gr_working_hr += $working_hour;
										$gr_line_bal += $line_bal;
									}
								}
			                }
			                ?>
			            </table>
		           	</div>
		            <!-- =======================================================================/
		            /									Footer Part								/
		            /======================================================================== -->
		            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="rpt_table_footer1" >
		                <tfoot>
		                	<tr>
			                    <th width="30">&nbsp;</th>     
			                    <th width="60">&nbsp;</th>   
			                    <th width="100">&nbsp;</th>
			                    <th width="100">&nbsp;</th>
			                    <th width="100">&nbsp;</th>
			                    <th width="100">&nbsp;</th>
			                    <th width="100">Grand Total: </th>
			                    <th width="60"><?=$gr_man_power;?></th>
			                    <th width="60"></th>
			                    <th width="60" align="right"><?=number_format($gr_order_qty,0);?></th>
			                    <th width="60" align="right"><?=number_format($gr_plan_cut_qty,0);?></th>
			                    <th width="60" align="right">&nbsp;</th>
			                    <th width="60" align="right"><?=number_format($gr_day_cut,0);?></th>  
			                    <th width="60"><?=number_format($gr_day_tot_cut,0); ?></th> 
			                    <th width="60"><?=$gr_day_in; ?>&nbsp;</th> 
			                    <th width="60"><?=$gr_day_tot_in; ?></th> 
			                    <th width="60"><?=$gr_day_target; ?></th> 
			                    <th width="60"><?=$gr_target_per_hr; ?></th> 
			                    <?
	                            for($k=$hour; $k<=$last_hour; $k++)
	                            {
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								
									?>
									   	<th align="right" width="50"><? echo $total_goods[$prod_hour]; ?></th> 
								    <?
									$totalGood_qty +=$total_goods[$prod_hour];
								}                                
	                            ?>                       
			                  
			                    <th align="right" width="60"><?=$totalGood_qty; ?></th>
			                    <th align="right" width="60"><?=number_format($gr_style_tot,0);?></th>
			                    <th align="right" width="60"><?=number_format($gr_prev_day_tot,0);?></th> 
			                    <th align="right" width="60"></th> 
			                    <th width="60"></th> 
			                    <th width="60"><?=number_format($gr_working_hr,0);?></th> 
			                    <th width="60"></th> 
			                    <th width="60"><?=number_format($gr_line_bal,0);?></th> 
			                </tr>
		                </tfoot>
		            </table>    
		        </div>
		    </div><!-- end main div -->
	     
			<?
		}
	}
	else
	{		
		$cbo_floor=str_replace("'","",$cbo_floor);
		if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
		if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
		if($cbo_floor=="") $floor="";else $floor=" and a.floor_id in($cbo_floor)";
		if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";
		$sewing_group = str_replace("'","",$cbo_sewing_group);
		$sewing_group=str_replace(" ","",$sewing_group);
		$group_name = "";
		$line_name = "";
		$group_cond ="";
		if($sewing_group!='0')
		{
			$group_sql = "SELECT id from lib_sewing_line where sewing_group=$cbo_sewing_group and status_active =1 and is_deleted=0";
			$group_sql_res = sql_select($group_sql);
			foreach ($group_sql_res as $val) 
			{
				if($line_name == "")
				{
					$line_name = $val[csf('id')];
					$group_name .= "'$line_name'";
				}
				else
				{
					$line_name = $val[csf('id')];
					$group_name .= ","."'$line_name'";
				}
			}
			$group_cond = " and a.sewing_line in($group_name)";
		}
		// echo $group_cond;

		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
		if(str_replace("'","",trim($txt_internal_no))=="") $internal_no_cond=""; else $internal_no_cond=" and c.grouping=$txt_internal_no";
		if(str_replace("'","",trim($txt_file_no))=="") $file_no_cond=""; else $file_no_cond=" and c.file_no=$txt_file_no";
		 
		$prod_resource_array=array();
		$dataArray=sql_select("select a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name");

		foreach($dataArray as $row)
		{
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['target_per_hour']=$row[csf('target_per_hour')];
			$prod_resource_array[$row[csf('id')]][change_date_format($row[csf('pr_date')])]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
		}
		//var_dump($prod_resource_array);//change_date_format($txt_date,'yyyy-mm-dd')
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
		//		echo $hour;die;
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
		$prod_qnty_data=sql_select("SELECT floor_id, location, prod_reso_allo, sewing_line, po_break_down_id, sum(production_quantity) as prod_qnty from pro_garments_production_mst where  production_type=4 group by floor_id, location, prod_reso_allo, sewing_line, po_break_down_id");
		foreach($prod_qnty_data as $row)
		{
			$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]]=$row[csf("prod_qnty")];
		}
		
		//var_dump($prod_qnty_data_arr);die;
		//echo $prod_qnty_data_arr[1][1][1][88][3533].jahid;die;
		
		if (str_replace("'","",trim($cbo_subcon))==1)
		{	 
			ob_start();
			
			?>
			<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	        </style> 
	                <div style="width:2830px"> 
	                    <table width="2800" cellspacing="0" > 
	                        <tr style="border:none;">
	                                <td align="center" style="border:none; font-size:14px;">
	                                	<b>Line Wise Hourly Production</b><br />
	                                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                         
	                                </td>
	                          </tr> 
	                    </table> 
	                    	<table class="rpt_table" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                          <thead>
	                            <tr>
	                            	<th colspan="3">Summary</th> 
	                            </tr>
	                            <tr>
	                                <th width="150">&nbsp;</th> 
	                                <th width="150">Quantity</th> 
	                                <th width="150">In %</th> 
	                            </tr>
	                          </thead>
	                          <tbody>
								<?
								
			$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
			
			if($db_type==0)
			{
				$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $group_cond $txt_date_from $style_no_cond $file_no_cond $internal_no_cond group by a.prod_reso_allo, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line";  
									}
					//echo $$db_type;die;			
			if($db_type==2)
			{
				$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id,   listagg(cast(c.po_number AS VARCHAR2(4000)),',') within group (order by c.po_number)   as po_number,    listagg(cast(c.grouping AS VARCHAR2(4000)),',') within group (order by c.grouping) as grouping   ,    listagg(cast(c.file_no AS VARCHAR2(4000)),',') within group (order by c.file_no) as   file_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
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
																		
					$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $group_cond $txt_date_from $style_no_cond   $file_no_cond $internal_no_cond  group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line"; 
									}//$txt_date
									//echo $sql;die;
									
									$result = sql_select($sql);
									$totalGood=0;$totalAlter=0;$totalReject=0;$totalinputQnty=0;
									$production_data=array();
									foreach($result as $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
										
										//total good,alter,reject qnty
					
										$totalGood_qty += $row[csf("good_qnty")];
										$totalAlter_qty += $row[csf("alter_qnty")];
										$totalSpot_qty += $row[csf("spot_qnty")];
										$totalReject_qty += $row[csf("reject_qnty")];
										//echo "select sum(production_quantity)from pro_garments_production_mst where floor_id=".$row[csf("floor_id")]." and location=".$row[csf("location")]." and prod_reso_allo=".$row[csf("prod_reso_allo")]." and sewing_line=".$row[csf("sewing_line")]." and po_break_down_id in (".$row[csf("po_break_down_id")].") and production_type=4 <br>";
										//$inputQnty = return_field_value("sum(production_quantity)","pro_garments_production_mst","floor_id=".$row[csf("floor_id")]." and location=".$row[csf("location")]." and prod_reso_allo=".$row[csf("prod_reso_allo")]." and sewing_line=".$row[csf("sewing_line")]." and po_break_down_id in (".$row[csf("po_break_down_id")].") and production_type=4");
										$inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]];
										
										if($row[csf("prod_reso_allo")]==1)
										{
											//echo $row[csf('sewing_line')]."**";die;
											$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
											$line_name="";
											$groupName="";
											$groupNameArr=array();
											foreach($line_resource_mst_arr as $resource_id)
											{
												$line_name.=$lineArr[$resource_id].", ";
												// $groupName.=$groupArr[$resource_id].", ";
												$groupNameArr[$prod_reso_group2[$resource_id]]=$prod_reso_group2[$resource_id];
											}
											$line_name=chop($line_name," , ");
											// $groupName=chop($groupName," , ");
											// $groupName=implode(",", $groupNameArr);
											$groupName = $prod_reso_group[$row[csf('sewing_line')]];
											if($groupName=="")
											{
												$groupName = implode(",", $groupNameArr);
												// print_r($groupNameArr);
											}
											// $groupName = $groupArr[$row[csf('sewing_line')]];
											//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
											//$line_name=$prod_reso_line_arr[$row[('sewing_line')]];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf(('sewing_line'))];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["group_name"]=$groupName;
											for($h=$hour;$h<$last_hour;$h++)
											{
												$bg=$start_hour_arr[$h];
												$bg_hour=$start_hour_arr[$h];
												//$end=substr(add_time($start_hour_arr[$h],60),0,8);
												$prod_hour="prod_hour".substr($bg_hour,0,2);
												$alter_hour="alter_hour".substr($bg_hour,0,2);
												$spot_hour="spot_hour".substr($bg_hour,0,2);
												$reject_hour="reject_hour".substr($bg_hour,0,2);
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
												
											}
										}
										else
										{
											//echo $row[('sewing_line')]."err";
											$line_name=$row[csf('sewing_line')];
											$groupName = $groupArr[$row[csf('sewing_line')]];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$line_name;
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["group_name"]=$groupName;
											for($h=$hour;$h<$last_hour;$h++)
											{
												$bg=$start_hour_arr[$h];
												$bg_hour=$start_hour_arr[$h];
												//$end=substr(add_time($start_hour_arr[$h],60),0,8);
												$prod_hour="prod_hour".substr($bg_hour,0,2);
												$alter_hour="alter_hour".substr($bg_hour,0,2);
												$spot_hour="spot_hour".substr($bg_hour,0,2);
												$reject_hour="reject_hour".substr($bg_hour,0,2);
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
												$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
												
											}
										}
										
										$grand_total = $totalGood_qty+$totalAlter_qty+$totalSpot_qty+$totalReject_qty;
											
										$summary_total_parc=($totalGood_qty/$grand_total)*100;
										$summary_total_parcalter=($totalAlter_qty/$grand_total)*100;
										$summary_total_parcspot=($totalSpot_qty/$grand_total)*100;
										$summary_total_parcreject=($totalReject_qty/$grand_total)*100;
									}
									//echo "<pre>";print_r($production_data);die;
									?>
										<tr>
											<td>QC Pass Qty</td>  
											<td align="right"><? echo $totalGood_qty; ?> </td> 
											<td align="right"><? echo number_format($summary_total_parc,2)."%"; ?></td>
										</tr>
										<tr bgcolor="#E9F3FF" >
											<td> Alter Qty </td>
											<td align="right"><?  echo $totalAlter_qty; ?></td>
											<td align="right"><? echo number_format($summary_total_parcalter,2)."%"; ?></td>
										</tr>
										<tr bgcolor="#E9F3FF" >
											<td> Spot Qty </td>
											<td align="right"><?  echo $totalSpot_qty; ?></td>
											<td align="right"><? echo number_format($summary_total_parcspot,2)."%"; ?></td>
										</tr>
										<tr>
											<td>Rejected Qty</td> 
											<td align="right"><? echo $totalReject_qty; ?> </td>
											<td align="right"><? echo number_format($summary_total_parcreject,2)."%"; ?></td>
										</tr>
										</tbody>
										<tfoot>
											<tr>
												<th>Grand Total </th> 
												<th><? echo $grand_total; ?></th>  
												<th>100%</th> 
											</tr>
										</tfoot>
									  </table>
								  <?
						
								  $table_width=1860+($last_hour-$hour+1)*50;
								  $div_width=$table_width+50;
							?>
	                      <br />
	                    <div>
	                        <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                            <thead> 	 	 	 	 	 	
	                                <tr height="50">
	                                    <th width="20">Sl.</th>    
	                                    <th width="100">Location</th>
	                                    <th width="80">Floor</th>
	                                    <th width="90">Line No</th>
	                                    <th width="90">Group</th>
	                                    <th width="110">Job No</th>
	                                    <th width="100">Style Ref.</th>
	                                    <th width="100">Internal Ref.</th>
	                                    <th width="100">File No.</th>
	                                    <th width="100">Order No</th>
	                                    <th width="60">Buyer</th>
	                                    <th width="150">Item</th> 
	                                    <th width="70">Input Qnty</th>
	                                    <th width="70">Hourly Target</th>
	                                    <th width="80">Quality</th>
										 <?
	                                    for($k=$hour+1; $k<=$last_hour+1; $k++)
	                                    {
	                                    ?>
	                                      <th width="50" style="vertical-align:middle"><div class="block_div">
										     <?  echo substr($start_hour_arr[$k],0,5);   ?></div>
	                                      </th>
	                                    <?	
	                                    }
	                                    ?>
	                                    <th width="70">Total</th>
	                                    <th width="70">In %</th>
	                                    <th width="70">Day Target</th>
	                                    <th width="70">Line Achv %</th>
	                                    <th width="120">Supervisor</th> 
	                                    <th width="">Remarks</th> 
	                                 </tr>
	                            </thead>
	                        </table>
	                        <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	                            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body" >
	                                <?
									//print_r($production_data);die;
	                                    foreach($production_data as $floor_id=>$value)
	                                    {
											ksort($value);
											foreach($value as $line_name=>$val)
											{
												foreach($val as $row)
												{
													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
													$totalGood += $row[("good_qnty")];
													$totalAlter += $row[("alter_qnty")];
													$totalSpot += $row[("spot_qnty")];
													$totalReject += $row[("reject_qnty")];
													$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]];
													
													$order_number=implode(',',array_unique(explode(",",$row["po_number"])));
													$grouping=implode(',',array_unique(explode(",",$row["grouping"])));
													$file_no=implode(',',array_unique(explode(",",$row["file_no"])));
																								
												?>
													<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
														<td width="20"><? echo $i; ?></td>    
														<td width="100"><p><? echo $location_library[$row[("location")]]; ?></p></td>
														<td width="80"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
														<td width="90"><p><? echo $line_name; ?></p></td>
														<td width="90" title="<? echo $row['sewing_line'];?>"><p><? echo $row[("group_name")]; ?></p></td>
														<td width="110" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
														<td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
														<td width="100"><p><? echo $grouping; ?></p></td>
														<td width="100"><p><? echo $file_no; ?></p></td>
														<td width="100"><p><? echo $order_number; ?></p></td>
														<td width="60"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
														<td width="150"><p><?
														//echo "tanim";
																				$gmt_item_id_string=chop($row[("item_number_id")],",");
																				$gmt_item_id_arr=explode(",",$gmt_item_id_string);
																				$gmt_item_arr = array();
																				foreach($gmt_item_id_arr as $s_gmt_item)
																				{
																					$gmt_item_arr[$s_gmt_item]=$garments_item[$s_gmt_item];
																				}
																				 echo implode(",",$gmt_item_arr);
																				 echo $garments_item[$row[("item_number_id")]];
																				  ?></p></td> 
														<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
														<td width="70" align="right">
														<? 
														
														echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; 
														?>&nbsp;</td>
														
													 <td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
													 <?
									
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
														
														
														$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
														$summary_total_parc=($totalGood/$grand_total)*100;
														$summary_total_parcalter=($totalAlter/$grand_total)*100;
														$summary_total_parcspot=($totalSpot/$grand_total)*100;
														$summary_total_parcreject=($totalReject/$grand_total)*100;
												
													  ?>
													   <td width="50" align="right">&nbsp;<? echo $row[($prod_hour)]; ?><hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
													  <?
													   $total_goods[$prod_hour]+= $row[($prod_hour)];
													   $total_alter[$prod_hour]+= $row[($alter_hour)];
													   $total_reject[$prod_hour]+= $row[($reject_hour)];
													   $total_spot[$prod_hour]+= $row[($spot_hour)];	   
													  }
											
													  ?>
													   
												<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>           
														<?
															$totalQnty = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
															$good_qnty_percentage = ($row[("good_qnty")]/$totalQnty)*100;
															$alter_qnty_percentage = ($row[("alter_qnty")]/$totalQnty)*100;
															$spot_qnty_percentage = ($row[("spot_qnty")]/$totalQnty)*100;
															$reject_qnty_percentage = ($row[("reject_qnty")]/$totalQnty)*100
														?>
														<td width="70" align="right"><? echo number_format($good_qnty_percentage,2); ?><hr><? echo number_format($alter_qnty_percentage,2); ?><hr><? echo number_format($spot_qnty_percentage,2); ?><hr><? echo number_format($reject_qnty_percentage,2); ?></td>
														
														<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>&nbsp;</td>
														<td width="70" align="right">
														<? $line_achive=($row[("good_qnty")]+$row[("reject_qnty")])/$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']*100;
														echo number_format($line_achive,2)."%"; ?>&nbsp;</td>
														<? $expArr = explode(",",$row[("supervisor")]); ?>
														<td width="120"><? echo $expArr[count($expArr)-1]; ?></td>  
														<td width="" align="center">
														<?  
														 $total_po_id=explode(",",$row[("po_break_down_id")]);
														 $total_po_id=implode("*",$total_po_id);
														 $line_number_id=explode(",",$row[('sewing_line')]);
														 $line_number_id=implode("*",$line_number_id);
															
														?>
														
														<input type="button" onclick="show_line_remarks(<? echo $cbo_company_name; ?>,'<? echo $total_po_id; ?>','<? echo $row[("floor_id")]; ?>','<? echo $line_number_id; ?>',<? echo $txt_date; ?>,'remarks_popup')" value="View"  class="formbutton"/></td>  
														  
														
													 </tr>
													<?
													$i++;
													$totalinputQnty+=$inputQnty;
													//$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
													//$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
													$totallineachiveper+=$line_achive;
													if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
													{
														$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
														$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
														$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
													}
													
												}
												
											}
	                                        
	                                }
	                                ?>
	                           </table>
	                           <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                                <tfoot>
	                                	<th width="20">&nbsp;</th>    
	                                    <th width="100">&nbsp;</th>
	                                    <th width="80">&nbsp;</th>
	                                    <th width="90">&nbsp;</th>
	                                    <th width="90">&nbsp;</th>
	                                    <th width="110">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="100">&nbsp;</th>
	                                    <th width="60">&nbsp;</th>
	                                    <th width="150">Grand Total</th>
	                                    <th width="70"><? echo $totalinputQnty; ?></th> 
	                                    <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
	                                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
	                                    <?
	                                      for($k=$hour; $k<=$last_hour; $k++)
	                                       {
											    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
												//$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
												//$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
												$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										
											  ?>
											   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
										      <?
											     
										   }
	                                    
	                                    ?>
	                                                                                  
	                                    <th align="right" width="70"><? echo $totalGood_qty; ?><hr><? echo $totalAlter_qty; ?><hr><? echo $totalSpot_qty; ?><hr><? echo $totalReject_qty; ?></th>
	                                    <th align="right" width="70"><? echo number_format($summary_total_parc,2); ?><hr><? echo number_format($summary_total_parcalter,2); ?><hr><? echo number_format($summary_total_parcspot,2); ?><hr><? echo number_format($summary_total_parcreject,2); ?></th>
	                                    <th align="right" width="70"><? echo number_format($totaldaytarget); ?>&nbsp;</th> 
	                                    <th align="right" width="70"><? echo number_format($totalGood_qty*100/$totaldaytarget,2)."%"; ?>&nbsp;</th> 
	                                    <th width="120">&nbsp;</th> 
	                                    <th></th>
	                            </table>	
	                        </div>    
	                    </div>
	                    <br />
	        </div><!-- end main div -->
	         <br/>
	         <fieldset style="width:1050px">
				<label   ><b>No Production Line</b></label>
	        	<table id="table_header_1" class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="100">Line No</th>
						<th width="100">Group</th>
						
						<th width="100">Floor</th>
						<th width="75">Man Power</th>
						<th width="75">Operator</th>
						<th width="75">Helper</th>
	                    <th width="75">Working Hour</th>
						<th width="380">Remarks</th>
						
					</thead>
				</table>
				<div style="width:1050px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <? 
					
					$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
					$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line");
					
					foreach($sql_active_line as $inf)
					{	
					
					   if(str_replace("","",$inf[csf('sewing_line')])!="")
					   {
							if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
					   }
					}
							//echo $actual_line_arr;die;
				$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
				 $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
				 if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
				
				 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$cbo_company_name and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0  $line_cond");
			
						$j=1; $location_array=array(); $floor_array=array();
						foreach( $dataArray as $row )
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
	                        	<td width="40"><? echo $j; ?></td>
	                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100"><p><? echo $groupArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
	                            <td width="380"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
						$j++;
						}
					?>
						
					</table>
				</div>
			</fieldset>
			<?
		
		}
		if (str_replace("'","",trim($cbo_subcon))==2)
		{
				 
			ob_start();
			?>
			<style type="text/css">
	            .block_div { 
	                    width:auto;
	                    height:auto;
	                    text-wrap:normal;
	                    vertical-align:bottom;
	                    display: block;
	                    position: !important; 
	                    -webkit-transform: rotate(-90deg);
	                    -moz-transform: rotate(-90deg);
	            }
	            hr {
	                border: 0; 
	                background-color: #000;
	                height: 1px;
	            }  
	        </style> 
	        <div style="width:2630px"> 
	            <table width="2600" cellspacing="0" > 
	                <tr style="border:none;">
	                        <td align="center" style="border:none; font-size:14px;">
	                            <b>Line Wise Hourly Production</b><br />
	                            Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                         
	                        </td>
	                  </tr> 
	            </table> 
	            <br />
	            <?
				$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
					
				if($db_type==0)
				{
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(distinct(c.grouping)) as grouping, group_concat(distinct(c.file_no)) as file_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $group_cond $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.prod_reso_allo, a.floor_id, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line"; // echo $sql; //$txt_date
					
				}
				
				if($db_type==2)
				{
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id, listagg(c.po_number,',') within group (order by po_number) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor, listagg(c.grouping,',') within group (order by grouping) as grouping, listagg(c.file_no,',') within group (order by file_no) as file_no,
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
																		
					$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $group_cond $txt_date_from $style_no_cond  $file_no_cond $internal_no_cond group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line";  
					
				}
				// echo $sql;die;
				$result = sql_select($sql);
				$totalGood=0;$totalAlter=0;$totalSpot=0;$totalReject=0;$totalinputQnty=0;
				$production_data=array();
					
				
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					//total good,alter,reject qnty
					$totalGood += $row[csf("good_qnty")];
					$totalAlter += $row[csf("alter_qnty")];
					$totalSpot += $row[csf("spot_qnty")];
					$totalReject += $row[csf("reject_qnty")];
					$inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]];
					
					/*if($row[csf("prod_reso_allo")]==1)
					{
						$prod_line=$prod_reso_line_arr[$row[csf("sewing_line")]];
						foreach($prod_reso_arr as $line_id=>$line_no)
						{
							if($line_id==$prod_line)
							{
							}
						}
					}
					else
					{
					}*/
					
					if($row[csf("prod_reso_allo")]==1)
					{
						//echo $row[csf('sewing_line')]."**";die;
						$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
						$line_name="";
						$groupName="";
						$groupNameArr=array();
						foreach($line_resource_mst_arr as $resource_id)
						{
							$line_name.=$lineArr[$resource_id].", ";
							// $groupName.=$groupArr[$resource_id].", ";
							$groupNameArr[$prod_reso_group2[$resource_id]]=$prod_reso_group2[$resource_id];
						}
						$line_name=chop($line_name," , ");
						// $groupName=chop($groupName," , ");
						// $groupName=implode(",", $groupNameArr);
						$groupName = $prod_reso_group[$row[csf('sewing_line')]];
						if($groupName=="")
						{
							$groupName = implode(",", $groupNameArr);
							// print_r($groupNameArr);
						}
						//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
						//$line_name=$prod_reso_line_arr[$row[('sewing_line')]];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("sewing_line")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["group_name"]=$groupName;
						for($h=$hour;$h<=$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
							
						}
					}
					else
					{
						$line_name=$lineArr[$row[csf('sewing_line')]];
						$groupName=$groupArr[$row[csf('sewing_line')]];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("sewing_line")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"].=$row[csf("item_number_id")].",";
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["grouping"]=$row[csf("grouping")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]+=$row[csf("good_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]+=$row[csf("alter_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]+=$row[csf("spot_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]+=$row[csf("reject_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["group_name"]=$groupName;
						for($h=$hour;$h<=$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]+=$row[csf("$prod_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]+=$row[csf("$alter_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]+=$row[csf("$spot_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]+=$row[csf("$reject_hour")];
							
						}
					}									
				}
				ksort($production_data);
				//var_dump($production_data);die;
				
                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
                    
                $summary_total_parc=($totalGood/$grand_total)*100;
                $summary_total_parcalter=($totalAlter/$grand_total)*100;
                $summary_total_parcspot=($totalSpot/$grand_total)*100;
                $summary_total_parcreject=($totalReject/$grand_total)*100;

				$subcon_prod_qnty_data = sql_select("select floor_id, location_id, line_id, order_id, sum(production_qnty) as prod_qnty from  subcon_gmts_prod_dtls where  production_type=2 group by floor_id, location_id, line_id, order_id");
				foreach($subcon_prod_qnty_data as $row)
				{
					$subcon_prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]]=$row[csf("prod_qnty")];
				}
				
				
				$i=1; $grand_total_good_sub=0; $grand_alter_good_sub=0; $grand_total_spot_sub=0; $grand_total_reject_sub=0;
				$first=1;
				$total_goods=array();
				$total_alter=array();
				$total_reject=array();
				$total_spot=array();
					
				if($db_type==0)
				{
					
					$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job as job_no, c.cust_style_ref, b.party_id, a.gmts_item_id, group_concat(distinct(a.order_id)) as order_id, group_concat(distinct(c.order_no)) as order_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
					sum(a.production_qnty) as good_qnty, 
					sum(a.alter_qnty) as alter_qnty,
					sum(a.spot_qnty) as spot_qnty, 
					sum(a.reject_qnty) as reject_qnty,";
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
				         $sql_subcon.="sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN a.hour<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
								sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
								sum(CASE WHEN a.hour<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					else
						{
					 $sql_subcon.="sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql_subcon.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
															
						$sql_subcon.=" from subcon_gmts_prod_dtls a, subcon_ord_mst b, subcon_ord_dtls c
						where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.floor_id, a.line_id, b.subcon_job order by a.floor_id, a.line_id "; //$txt_date production_date
					
				}//listagg(a.order_id,',') within group (order by order_id) as order_id, listagg(c.order_no,',') within group (order by order_no) as order_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
				//echo $sql_subcon;
				if($db_type==2)
				{
					$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job as job_no, c.cust_style_ref, b.party_id, a.gmts_item_id, listagg(a.order_id,',') within group (order by a.order_id) as order_id, listagg(c.order_no,',') within group (order by c.order_no) as order_no,
					LISTAGG(CAST((case when a.supervisor is not null then a.supervisor end) AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.supervisor) as supervisor,
					sum(a.production_qnty) as good_qnty, 
					sum(a.alter_qnty) as alter_qnty,
					sum(a.spot_qnty) as spot_qnty, 
					sum(a.reject_qnty) as reject_qnty,";
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
				         $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						        sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
								sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
								sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					else
						{
					 $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						       sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
						}
					$first=$first+1;
					}
					$prod_hour="prod_hour".$last_hour;
					$alter_hour="alter_hour".$last_hour;
					$spot_hour="spot_hour".$last_hour;
					$reject_hour="reject_hour".$last_hour;
					$sql_subcon.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
															
						$sql_subcon.=" from subcon_gmts_prod_dtls a, subcon_ord_mst b, subcon_ord_dtls c
						where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job, c.cust_style_ref, b.party_id, a.gmts_item_id order by a.floor_id, a.line_id "; //$txt_date production_date					
				}
				//echo $sql_subcon;die;
				
				$result_subcon = sql_select($sql_subcon);
				$totalGoodSub=0;$totalAlterSub=0;$totalRejectSub=0;$totalSpotSub=0;$totalinputQntySub=0;
				$production_subcon_data=array();
                foreach($result_subcon as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    
                    //total good,alter,reject qnty
                    $totalGoodSub += $row[csf("good_qnty")];
                    $totalAlterSub += $row[csf("alter_qnty")];
                    $totalSpotSub += $row[csf("spot_qnty")];
                    $totalRejectSub += $row[csf("reject_qnty")];
					//echo $row[csf("supervisor")].'==';
					$inputQnty=$subcon_prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]];
					
					if($row[csf("prod_reso_allo")]==1)
					{
						//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];
						$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('line_id')]]);
						$line_name="";
						$groupName="";
						$groupNameArr=array();
						foreach($line_resource_mst_arr as $resource_id)
						{
							$line_name.=$lineArr[$resource_id].", ";
							// $groupName.=$groupArr[$resource_id].", ";
							$groupNameArr[$prod_reso_group2[$resource_id]]=$prod_reso_group2[$resource_id];
						}
						$line_name=chop($line_name," , ");
					// $groupName=chop($groupName," , ");
					// $groupName=implode(",", $groupNameArr);
					$groupName = $prod_reso_group[$row[csf('sewing_line')]];
					if($groupName=="")
					{
						$groupName = implode(",", $groupNameArr);
						// print_r($groupNameArr);
					}
						
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("line_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("subcon_job")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("cust_style_ref")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("party_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("gmts_item_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("order_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("order_no")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]=$row[csf("good_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]=$row[csf("alter_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]=$row[csf("spot_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]=$row[csf("reject_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["group_name"]=$groupName;
						for($h=$hour;$h<=$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]=$row[csf("$prod_hour")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]=$row[csf("$alter_hour")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]=$row[csf("$spot_hour")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]=$row[csf("$reject_hour")];
							
						}
					}
					else
					{
						$line_name=$lineArr[$row[csf('sewing_line')]];
						$groupName=$groupArr[$row[csf('sewing_line')]];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["company_id"]=$row[csf("company_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["location"]=$row[csf("location_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["floor_id"]=$row[csf("floor_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["production_date"]=$row[csf("production_date")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf("line_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("subcon_job")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("cust_style_ref")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("party_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("gmts_item_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("order_id")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("order_no")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]=$row[csf("good_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]=$row[csf("alter_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]=$row[csf("spot_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]=$row[csf("reject_qnty")];
						$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["group_name"]=$groupName;
						for($h=$hour;$h<=$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]=$row[csf("$prod_hour")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]=$row[csf("$alter_hour")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]=$row[csf("$spot_hour")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]=$row[csf("$reject_hour")];
						}
					}
				}
                $grand_total_sub = $totalGoodSub+$totalAlterSub+$totalSpotSub+$totalRejectSub;
                    
                $summary_total_parc_sub=($totalGoodSub/$grand_total_sub)*100;
                $summary_total_parcalter_sub=($totalAlterSub/$grand_total_sub)*100;
                $summary_total_parcspot_sub=($totalSpotSub/$grand_total_sub)*100;
                $summary_total_parcreject_sub=($totalRejectSub/$grand_total_sub)*100;
				
				$summary_total=0;$summary_good=0;$summary_alter=0;$summary_spot=0;$summary_reject=0; $summary_total_pergood=0;$summary_total_peralter=0;$summary_total_perspot=0;$summary_total_perreject=0;
				
				$summary_total=$grand_total+$grand_total_sub;
				
				$summary_good=$totalGood+$totalGoodSub;
				$summary_alter=$totalAlter+$totalAlterSub;
				$summary_spot=$totalSpot+$totalSpotSub;
				$summary_reject=$totalReject+$totalRejectSub;
				
				
				$summary_total_pergood=($summary_good/$summary_total)*100;
				$summary_total_peralter=($summary_alter/$summary_total)*100;
				$summary_total_perspot=($summary_spot/$summary_total)*100;
				$summary_total_perreject=($summary_reject/$summary_total)*100;
				//print_r($production_subcon_data);die;
				?>
				<!-- =======================================================================/
				/									Summary Part							/
				/======================================================================== -->
	            <div>
	                <table class="" width="1100" border="0" rules="all" cellpadding="0" cellspacing="0" >
	                    <tr>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Summary (Production-Regular Order)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th> 
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($totalGood,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_parc,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($totalAlter,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcalter,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><? echo number_format($totalSpot,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcspot,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($totalReject,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_parcreject,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($grand_total,2); ?></th>  
	                                        <th>100%</th> 
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                        <td width="" >&nbsp;</td>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Summary (Production-Subcontract Order)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th> 
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($totalGoodSub,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_parc_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($totalAlterSub,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcalter_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><?  echo number_format($totalSpotSub,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_parcspot_sub,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($totalRejectSub,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_parcreject_sub,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($grand_total_sub,2); ?></th>  
	                                        <th>100%</th> 
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                        <td width="" >&nbsp;</td>
	                        <td width="350" valign="top">
	                            <table class="rpt_table" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" >
	                                <thead>
	                                    <tr>
	                                        <th colspan="3">Total Summary (Regular + Subcontract)</th> 
	                                    </tr>
	                                    <tr>
	                                        <th width="150">Quality</th> 
	                                        <th width="100">Quantity</th>
	                                        <th width="80">In %</th> 
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                    <tr>
	                                        <td>QC Pass Qty</td>  
	                                        <td align="right"><? echo number_format($summary_good,2); ?> </td> 
	                                        <td align="right"><? echo number_format($summary_total_pergood,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td> Alter Qty </td>
	                                        <td align="right"><?  echo number_format($summary_alter,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_peralter,2)."%"; ?></td>
	                                    </tr>
	                                    <tr>
	                                        <td> Spot Qty </td>
	                                        <td align="right"><?  echo number_format($summary_spot,2); ?></td>
	                                        <td align="right"><? echo number_format($summary_total_perspot,2)."%"; ?></td>
	                                    </tr>
	                                    <tr bgcolor="#E9F3FF" >
	                                        <td>Rejected Qty</td> 
	                                        <td align="right"><? echo number_format($summary_reject,2); ?> </td>
	                                        <td align="right"><? echo number_format($summary_total_perreject,2)."%"; ?></td>
	                                    </tr>
	                                </tbody>
	                                <tfoot>
	                                    <tr>
	                                        <th>Grand Total </th> 
	                                        <th><? echo number_format($summary_total,2); ?></th> 
	                                        <th>100%</th>  
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </td>
	                     </tr>
	                </table>
	            </div>
	            <br /> 
	           
				<!-- =======================================================================/
				/							Production-Regular Order						/
				/======================================================================== -->            
			   <?
	            $table_width=1830+($last_hour-$hour+1)*50;
	           ?>
	            <div style="width:200px; font-weight:bold">Production-Regular Order</div>
	            <div>
	            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	                <thead> 	 	 	 	 	 	
	                    <tr height="50">
	                        <th width="30">Sl.</th>    
	                        <th width="90">Location</th>
	                        <th width="90">Group</th>
	                        <th width="70">Floor</th>
	                        <th width="70">Line No</th>
	                        <th width="60">Job No</th>
	                        <th width="110">Style Ref.</th>
	                        <th width="100">Internal Ref.</th>
	                        <th width="100">File No</th>
	                        <th width="100">Order No</th>
	                        <th width="60">Buyer</th>
	                        <th width="170">Item</th>
	                        <th width="70">Input Qnty</th>
	                        <th width="70">Hourly Target</th>
	                        <th width="80">Quality</th>
	                     	<?	                        
	                            for($k=$hour+1; $k<=$last_hour+1; $k++)
	                            {
	                            	?>
	                              	<th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>	                            
	                            	<?	
	                            }
	                        ?>
	                           	 	 	 	
	                        <th width="70">Total</th>
	                        <th width="70">In %</th>
	                        <th width="70">Day Target</th>
	                        <th width="70">Line Achv %</th>
	                        <th width="120">Supervisor</th> 
	                        <th width="">Remarks</th> 
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body" >
	                <?				  
	                    $totalGoodQnt=0; $totalAlterQnt=0; $totalSpotQnt=0; $totalRejectQnt=0;
						//print_r($production_data);die;
	                    foreach($production_data as $flowre_id=>$value)
	                    {
							ksort($value);
							foreach($value as $line_name=>$val)
							{
								foreach($val as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									//total good,alter,reject qnty
									$totalGood_qty += $row[("good_qnty")];
									$totalAlter_qty += $row[("alter_qnty")];
									$totalSpot_qty += $row[("spot_qnty")];
									$totalReject_qty += $row[("reject_qnty")];
									$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]];
									$order_number=implode(',',array_unique(explode(",",$row[("po_number")])));
									$grouping=implode(',',array_unique(explode(",",$row[("grouping")])));
									$file_no=implode(',',array_unique(explode(",",$row[("file_no")])));
								?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="30" align="center"><? echo $i; ?></td>    
										<td width="90" align="center"><p><? echo $location_library[$row[("location")]]; ?></p></td>
										<td width="90" align="center"><p><? echo $row[("group_name")]; ?></p></td>
										<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $line_name; ?></p></td>
										<td width="60" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
										<td width="110" align="center"><p><? echo $row[("style_ref_no")]; ?></p></td>
										<td width="100" align="center"><p><? echo $grouping; ?></p></td>
										<td width="100" align="center"><p><? echo $file_no; ?></p></td>
										<td width="100" align="center"><p><? echo $order_number; ?></p></td>
										<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
										<td width="170" align="center"><p>
											<?
											$gmt_item_id_string=chop($row[("item_number_id")],",");
											$gmt_item_id_arr=explode(",",$gmt_item_id_string);
											$gmt_item_arr = array();
											foreach($gmt_item_id_arr as $s_gmt_item)
											{
												$gmt_item_arr[$s_gmt_item]=$garments_item[$s_gmt_item];
											}
											 echo implode(",",$gmt_item_arr);
											 echo $garments_item[$row[("item_number_id")]]; 
											 ?>																		 	
										</p></td> 
										<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
										<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; ?>&nbsp;</td>
										<td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
										
										<?
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
											
											
											$grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
											$summary_total_parc=($totalGood/$grand_total)*100;
											$summary_total_parcalter=($totalAlter/$grand_total)*100;
											$summary_total_parcspot=($totalSpot/$grand_total)*100;
											$summary_total_parcreject=($totalReject/$grand_total)*100;
											
											?>
											<td width="50" align="right"><? echo $row[($prod_hour)]; ?>&nbsp;<hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
											<?
											$total_goods[$prod_hour]+= $row[($prod_hour)];
											$total_alter[$prod_hour]+= $row[($alter_hour)];
											$total_reject[$prod_hour]+= $row[($reject_hour)];
											$total_spot[$prod_hour]+= $row[($spot_hour)];	   
										}
										
										?>	 	 	 	
										<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>
										<?
											$totalQnty = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
											$good_qnty_percentage = ($row[("good_qnty")]/$totalQnty)*100;
											$alter_qnty_percentage = ($row[("alter_qnty")]/$totalQnty)*100;
											$spot_qnty_percentage = ($row[("spot_qnty")]/$totalQnty)*100;
											$reject_qnty_percentage = ($row[("reject_qnty")]/$totalQnty)*100
										?>
										<td width="70" align="right"><? echo number_format($good_qnty_percentage,2); ?><hr><? echo number_format($alter_qnty_percentage,2); ?><hr><? echo number_format($spot_qnty_percentage,2); ?><hr><? echo number_format($reject_qnty_percentage,2); ?></td>
										
										<td width="70" align="right"><? //echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[csf('production_date')])]['tpd']; 
										echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd']; ?>&nbsp;</td>
										<td width="70" align="right">
											<? $line_achive=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']*100;
											echo number_format($line_achive,2)."%"; ?>&nbsp;</td>
										<? $expArr = explode(",",$row["supervisor"]); ?>
										<td width="120"><? echo $expArr[count($expArr)-1]; ?></td>  
										<td width="" align="center">
										<?  
										
										 $total_po_id=explode(",",$row[("po_break_down_id")]);
										 $all_po_id=implode("*",$total_po_id);
										 $line_number_id=explode(",",$row[('sewing_line')]);
										 $line_number_id=implode("*",$line_number_id);
											
										?>
										
										<input type="button" onclick="show_line_remarks(<? echo $cbo_company_name; ?>,'<? echo $all_po_id; ?>','<? echo $row[("floor_id")]; ?>','<? echo $line_number_id; ?>',<? echo $txt_date; ?>,'remarks_popup')" value="View"  class="formbutton"/></td>  
									 </tr>
									<?
									$i++;
									$totalinputQnty+=$inputQnty;
									//$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
									//$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
									$totallineachiveper+=$line_achive;
									if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
									{
										$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour'];
										$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['tpd'];
										$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
									}
								}
							}
	                }
	                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
	                    
	                $summary_total_parc=($totalGood/$grand_total)*100;
	                $summary_total_parcalter=($totalAlter/$grand_total)*100;
	                $summary_total_parcspot=($totalSpot/$grand_total)*100;
	                $summary_total_parcreject=($totalReject/$grand_total)*100;
	                ?>
	            </table>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="rpt_table_footer1" >
	                <tfoot>
	                    <th width="30">&nbsp;</th>    
	                    <th width="90">&nbsp;</th>
	                    <th width="90">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="110">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="170" align="right">Grand Total: </th>  
	                    <th width="70"><? echo $totalinputQnty; ?></th> 
	                    <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
	                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
	                      <?
	                                      for($k=$hour; $k<=$last_hour; $k++)
	                                       {
											    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										
											  ?>
											   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
										      <?
											     
										   }
	                                    
	                                    ?>                       
	                  
	                    <th align="right" width="70"><? echo $totalGood_qty; ?><hr><? echo $totalAlter_qty; ?><hr><? echo $totalSpot_qty; ?><hr><? echo $totalReject_qty; ?></th>
	                    <th align="right" width="70"><? echo number_format($summary_total_parc,2); ?><hr><? echo number_format($summary_total_parcalter,2); ?><hr><? echo number_format($summary_total_parcspot,2); ?><hr><? echo number_format($summary_total_parcreject,2); ?></th>
	                    <th align="right" width="70"><? echo number_format($totaldaytarget); ?>&nbsp;</th> 
	                    <th align="right"width="70"><? echo number_format($totalGood_qty*100/$totaldaytarget,2)."%"; ?>&nbsp;</th> 
	                    <th width="120">&nbsp;</th> 
	                    <th>&nbsp;</th> 
	                </tfoot>
	            </table>
	           </div>    
	        </div>
	        <br />
	        <br />
	        <!-- =======================================================================/
			/						Production-Subcontract order_num					/
			/======================================================================== -->
	        <div style="width:200px; font-weight:bold">Production-Subcontract Order</div>
	            <div>
	            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
	                <thead> 	 	 	 	 	 	
	                    <tr height="50">
	                        <th width="30">Sl.</th>    
	                        <th width="90">Location</th>
	                        <th width="90">Group</th>
	                        <th width="70">Floor</th>
	                        <th width="70">Line No</th>
	                        <th width="60">Job No</th>
	                        <th width="110">Style Ref.</th>
	                        <th width="100">Order No</th>
	                        <th width="60">Buyer</th>
	                        <th width="170">Item</th>
	                        <th width="70">Input Qnty</th>
	                        <th width="70">Hourly Target</th>
	                        <th width="80">Quality</th>
	                        
	                        	 <?
	                        
	                            for($k=$hour+1; $k<=$last_hour+1; $k++)
	                            {
	                            ?>
	                              <th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
	                            
	                            <?	
	                            }
	                        ?>
	                       	 	 	 	
	                        <th width="70">Total</th>
	                        <th width="70">In %</th>
	                        <th width="70">Day Target</th>
	                        <th width="70">Line Achv %</th>
	                        <th width="">Supervisor</th> 
	                     </tr>
	                </thead>
	            </table>
	            <div style="max-height:425px; overflow-y:scroll; width:<? echo $table_width+20; ?>px" id="scroll_body">
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="table_body1" >
	                <? 
	                    $totalGoodSubSub=0; $totalAlterSubSub=0; $totalSpotSubSub=0; $totalRejectSubSub=0;//$prod_reso_arr_sub=array();
	                    foreach($production_subcon_data as $floor_id=>$value)
	                    {
							ksort($value);
							foreach($value as $line_name=>$val)
							{
								foreach($val as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									//total good,alter,spot,reject qnty
									$totalGoodSubSub += $row[("good_qnty")];
									$totalAlterSubSub += $row[("alter_qnty")];
									$totalSpotSubSub += $row[("spot_qnty")];
									$totalRejectSubSub += $row[("reject_qnty")];
									
									$inputQntySub=$subcon_prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("line_id")]][$row[("po_break_down_id")]];
									
									$order_num="";
									$ex_po=array_unique(explode(',',$row[("po_number")]));
									foreach($ex_po as $po_no)
									{
										if($order_num=="") $order_num=$po_no; else $order_num.=','.$po_no;
									}
									
									?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="30" align="center"><? echo $i; ?></td>    
										<td width="90" align="center"><p><? echo $location_library[$row[("location")]]; ?></p></td>
										<td width="90" align="center"><p><? echo $row['group_name']; ?></p></td>
										<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
										<td width="70" align="center"><p><? echo $line_name; ?></p></td>
										<td width="60" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
										<td width="110" align="center"><p><? echo $row[("cust_style_ref")]; ?></p></td>
										<td width="100" align="center"><p><? echo $order_num; ?></p></td>
										<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
										<td width="170" align="center"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td> 
										<td width="70" align="right"><p><? echo $inputQntySub; ?></p></td> 
										<td width="70" align="right"><? echo $prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
										
										//$prod_resource_array[$row['line_id']][change_date_format($row['production_date'])]['target_per_hour']; ?>&nbsp;</td>
										<td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
										
										<?
										
										for($k=$hour; $k<=$last_hour; $k++)
										{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
										$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
										$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
										$totalGoodQnt_sub += $row[($prod_hour)];
										$totalAlterQnt_sub += $row[($alter_hour)];
										$totalSpotQnt_sub += $row[($spot_hour)];
										$totalRejectQnt_sub +=$row[($reject_hour)];
										
										
										$grand_total_sub = $totalGoodSubSub+$totalAlterSubSub+$totalSpot+$totalReject;
										$summary_total_parc_sub=($totalGood/$grand_total)*100;
										$summary_total_parcalter__sub=($totalAlter/$grand_total)*100;
										$summary_total_parcspot=($totalSpot/$grand_total)*100;
										$summary_total_parcreject_sub=($totalReject/$grand_total)*100;
										
										?>
										<td width="50" align="right"><? echo $row[($prod_hour)]; ?>&nbsp;<hr><? echo $row[($alter_hour)]; ?>&nbsp;<hr><? echo $row[($spot_hour)]; ?>&nbsp;<hr><? echo $row[($reject_hour)]; ?>&nbsp;</td> 
										<?
										$sub_total_goods[$prod_hour]+= $row[($prod_hour)];
										$sub_total_alter[$prod_hour]+= $row[($alter_hour)];
										$sub_total_reject[$prod_hour]+= $row[($reject_hour)];
										$sub_total_spot[$prod_hour]+= $row[($spot_hour)];	   
										}
										
										?>
										
										<td width="70" align="right"><? echo $row[("good_qnty")]; ?>&nbsp;<hr><? echo $row[("alter_qnty")]; ?>&nbsp;<hr><? echo $row[("spot_qnty")]; ?>&nbsp;<hr><? echo $row[("reject_qnty")]; ?>&nbsp;</td>
										<?
										$totalQntySub = $row[("good_qnty")]+$row[("alter_qnty")]+$row[("spot_qnty")]+$row[("reject_qnty")];
										$good_qnty_percentage_sub = ($row[("good_qnty")]/$totalQntySub)*100;
										$alter_qnty_percentage_sub = ($row[("alter_qnty")]/$totalQntySub)*100;
										$spot_qnty_percentage_sub = ($row[("spot_qnty")]/$totalQntySub)*100;
										$reject_qnty_percentage_sub = ($row[("reject_qnty")]/$totalQntySub)*100
										?>
										<td width="70" align="right"><? echo number_format($good_qnty_percentage_sub,2); ?><hr><? echo number_format($alter_qnty_percentage_sub,2); ?><hr><? echo number_format($spot_qnty_percentage_sub,2); ?><hr><? echo number_format($reject_qnty_percentage_sub,2); ?></td>
										<td width="70" align="right"><? echo $prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']; ?>&nbsp;</td>
										<td width="70" align="right">
										<? $line_achive_sub=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd']*100;
										echo number_format($line_achive_sub,2)."%"; ?>&nbsp;</td>
										<? $expArr = explode(",",$row["supervisor"]); ?>
										<td width=""><? echo $expArr[count($expArr)-1]; ?></td>  
									</tr>
									<?
									$i++;
									$totalinputQntySub+=$inputQntySub;
									//$totaltargetperhouresub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
									//$totaldaytargetsub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd'];
									$totallineachivepesubr+=$line_achive;
									
									if($duplicate_array[$row['prod_reso_allo']][$row['sewing_line']]=="")
									{
										$totaltargetperhouresub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['target_per_hour'];
										$totaldaytargetsub+=$prod_resource_array[$row['sewing_line']][change_date_format($row['production_date'])]['tpd'];
										$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
									}
								}
								
							}
	                        
	                    
	                }
	                $grand_total_sub = $totalGoodSub+$totalAlterSub+$totalSpotSub+$totalRejectSub;
	                    
	                $summary_total_parc_sub=($totalGoodSub/$grand_total_sub)*100;
	                $summary_total_parcalter_sub=($totalAlterSub/$grand_total_sub)*100;
	                $summary_total_parcspot_sub=($totalSpotSub/$grand_total_sub)*100;
	                $summary_total_parcreject_sub=($totalRejectSub/$grand_total_sub)*100;
	                ?>
	            </table>
	            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table"  width="<? echo $table_width; ?>" rules="all" id="rpt_table_footer1" >
	                <tfoot>
	                    <th width="30">&nbsp;</th>    
	                    <th width="90">&nbsp;</th>
	                    <th width="90">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="110">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="60">&nbsp;</th>
	                    <th width="170" align="right">Grand Total: </th>  
	                    <th  width="70"><? echo $totalinputQntySub; ?></th> 
	                    <th width="70"><? echo $totaltargetperhouresub; ?>&nbsp;</th> 
	                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
						<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<th align="right" width="50"><? echo $sub_total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_reject[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_spot[$prod_hour]; ?>&nbsp;</th> 
								<?
							}  
	                    ?>                            
	                    <th align="right" width="70"><? echo $totalGoodSubSub; ?><hr><? echo $totalAlterSubSub; ?><hr><? echo $totalSpotSubSub; ?><hr><? echo $totalRejectSubSub; ?></th>
	                    <th align="right" width="70"><? echo number_format($summary_total_parc_sub,2); ?><hr><? echo number_format($summary_total_parcalter_sub,2); ?><hr><? echo number_format($summary_total_parcspot_sub,2); ?><hr><? echo number_format($summary_total_parcreject_sub,2); ?></th>
	                    <th align="right" width="70"><? echo number_format($totaldaytargetsub); ?>&nbsp;</th> 
	                    <th align="right" width="70"><? echo number_format($totalGoodSubSub*100/$totaldaytargetsub,2)."%"; ?>&nbsp;</th> 
	                    <th width="">&nbsp;</th> 
	                </tfoot>
	            </table>	
	           </div>    
	        </div>
	      <br />
	      <br />
	    	</div><!-- end main div -->
	     	<br/>
	     	<!-- =======================================================================/
			/								No Production Line							/
			/======================================================================== -->
	        <fieldset style="width:1050px">
				<label   ><b>No Production Line</b></label>
	        	<table id="table_header_1" class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="100">Line No</th>
						<th width="100">Group</th>
						
						<th width="100">Floor</th>
						<th width="75">Man Power</th>
						<th width="75">Operator</th>
						<th width="75">Helper</th>
	                    <th width="75">Working Hour</th>
						<th width="380">Remarks</th>
						
					</thead>
				</table>
				<div style="width:1050px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <? 
					
					$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
					$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line
					union all
					select line_id,sum(production_qnty) as total_production from subcon_gmts_prod_dtls  where production_date=".$txt_date." and production_type=2 and status_active=1 and is_deleted=0 group by  line_id");
					
					foreach($sql_active_line as $inf)
					{	
					
					   if(str_replace("","",$inf[csf('sewing_line')])!="")
					   {
							if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
						    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
					   }
					}
							//echo $actual_line_arr;die;
				$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
				 $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
				 if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
				 $group_cond_sub = str_replace("a.sewing_line", "a.line_number", $group_cond);
				 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$cbo_company_name and b.pr_date=".$txt_date." and d.shift_id=1  and a.is_deleted=0 and b.is_deleted=0 $line_cond $group_cond_sub");
						$j=1; $location_array=array(); $floor_array=array();
						foreach( $dataArray as $row )
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
	                        	<td width="40"><? echo $j; ?></td>
	                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100"><p><? echo $prod_reso_group[$row[csf('line_number')]]; ?>&nbsp;</p></td>
	                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
	                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
	                            <td width="380"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
	                        </tr>
	                    <?
							$j++;
							
						}
						
				
					?>
						
					</table>
				</div>
			</fieldset>
			<?
		}
	}
	
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

if($action=="remarks_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sewing_line=explode("*",$sewing_line);
	$sewing_line=implode(",",$sewing_line);
	$po_id=explode("*",$order_id);
	$po_id=implode(",",$po_id);
	
	// $sql_line_remark=sql_select("select remarks,production_hour from pro_garments_production_mst where company_id=".$company_id." and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and status_active=1 and is_deleted=0 order by production_hour");
	$sql_line_remark=sql_select("select remarks,production_hour from pro_garments_production_mst where floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and status_active=1 and is_deleted=0 order by production_hour");
	//$sql_line_remark=sql_select("select remarks,production_hour from pro_garments_production_mst where company_id=".$company_id." and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' order by production_hour");
	?>
	<fieldset style="width:520px;  ">
		<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<th width="40">SL</th>
						<th width="460">Remarks</th>
					</thead>
					<tbody>
					<?
					$i=1;
					foreach($sql_line_remark as $inf)
					{
					 if ($i%2==0)    $bgcolor="#E9F3FF";
					 else            $bgcolor="#FFFFFF";
					 if(trim($inf[csf('remarks')])!="")
					 {
					 ?>		
					   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
					</tr>
					<?
					$i++;
					 }
					}
					?>
					</tbody>
				</table>
		</div>
	</fieldset>
		  <?
}


?>