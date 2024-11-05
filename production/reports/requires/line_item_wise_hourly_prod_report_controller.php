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
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_item_wise_hourly_prod_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/line_item_wise_hourly_prod_report_controller',document.getElementById('cbo_floor').value+'_'+this.value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_item_wise_hourly_prod_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 ); 
	
	//echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_item_wise_hourly_prod_report_controller', this.value, 'load_drop_down_line', 'line_td' );",0 ); 
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
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by floor_name, sewing_line_serial","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$floor_sl_Arr = return_library_array("select id,floor_serial_no from lib_prod_floor","id","floor_serial_no");
	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	
	//echo $txt_date;
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
	if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
	if(str_replace("'","",$cbo_floor)==0)$floor="";else $floor=" and a.floor_id=$cbo_floor";
	if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
	$shift_cond = (str_replace("'","",$cbo_shift_name)==0)? "" :" and a.shift_name= $cbo_shift_name";
	$prod_resource_array=array();
	$dataArray=sql_select("select a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name");

    
	foreach($dataArray as $row)
	{
		$prod_resource_array[$row[csf('id')]][$row[csf('pr_date')]]['target_per_hour']=$row[csf('target_per_hour')];
		$prod_resource_array[$row[csf('id')]][$row[csf('pr_date')]]['tpd']=$row[csf('target_per_hour')]*$row[csf('working_hour')];
	}
	
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
	/* $prod_qnty_data=sql_select("select floor_id, location, prod_reso_allo, sewing_line, po_break_down_id, sum(production_quantity) as prod_qnty from pro_garments_production_mst where  production_type=4 group by floor_id, location, prod_reso_allo, sewing_line, po_break_down_id");
	foreach($prod_qnty_data as $row)
	{
		$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]]=$row[csf("prod_qnty")];
	}
	 */
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
		$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  group by a.prod_reso_allo, a.sewing_line, a.item_number_id, b.job_no order by a.floor_id, a.sewing_line";  
								}
				//echo $$db_type;die;			
		if($db_type==2)
				{
				$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id, listagg(c.po_number,',') within group (order by po_number) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,a.shift_name,
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
					 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond $shift_cond group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date,a.shift_name order by a.floor_id, a.sewing_line"; 
								}//$txt_date
								// echo $sql;
								
								$result = sql_select($sql);
								$totalGood=0;$totalAlter=0;$totalReject=0;$totalinputQnty=0;
								$production_data=array();
								$po_id_array=array();
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
									// $inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]];
									$po_id_array[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
									if($row[csf("prod_reso_allo")]==1)
									{
										$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
										$line_name="";
										foreach($line_resource_mst_arr as $resource_id)
										{
											$line_name.=$lineArr[$resource_id].", ";
										}
										$line_name=chop($line_name," , ");
										//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];, a.item_number_id
										//$line_name=$prod_reso_line_arr[$row[('sewing_line')]];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["company_id"]=$row[csf("company_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["location"]=$row[csf("location")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["floor_id"]=$row[csf("floor_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["production_date"]=$row[csf("production_date")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["sewing_line"]=$row[('SEWING_LINE')];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no"]=$row[csf("job_no")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["style_ref_no"]=$row[csf("style_ref_no")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["buyer_name"]=$row[csf("buyer_name")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["item_number_id"]=$row[csf("item_number_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_number"]=$row[csf("po_number")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["supervisor"]=$row[csf("supervisor")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["good_qnty"]=$row[csf("good_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["alter_qnty"]=$row[csf("alter_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["spot_qnty"]=$row[csf("spot_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["reject_qnty"]=$row[csf("reject_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["shift_name"]=$row[csf("shift_name")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["sewing_line_id"]=$row[('SEWING_LINE')];
										for($h=$hour;$h<$last_hour;$h++)
										{
											$bg=$start_hour_arr[$h];
											$bg_hour=$start_hour_arr[$h];
											//$end=substr(add_time($start_hour_arr[$h],60),0,8);
											$prod_hour="prod_hour".substr($bg_hour,0,2);
											$alter_hour="alter_hour".substr($bg_hour,0,2);
											$spot_hour="spot_hour".substr($bg_hour,0,2);
											$reject_hour="reject_hour".substr($bg_hour,0,2);
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$prod_hour"]=$row[csf("$prod_hour")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$alter_hour"]=$row[csf("$alter_hour")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$spot_hour"]=$row[csf("$spot_hour")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$reject_hour"]=$row[csf("$reject_hour")];
											
										}
									}
									else
									{
										$line_name=$lineArr[$row[('sewing_line')]];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["company_id"]=$row[csf("company_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["location"]=$row[csf("location")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["floor_id"]=$row[csf("floor_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["production_date"]=$row[csf("production_date")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["sewing_line"]=$line_name;
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no"]=$row[csf("job_no")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["style_ref_no"]=$row[csf("style_ref_no")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["buyer_name"]=$row[csf("buyer_name")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["item_number_id"]=$row[csf("item_number_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_number"]=$row[csf("po_number")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["supervisor"]=$row[csf("supervisor")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["good_qnty"]=$row[csf("good_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["alter_qnty"]=$row[csf("alter_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["spot_qnty"]=$row[csf("spot_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["reject_qnty"]=$row[csf("reject_qnty")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["shift_name"]=$row[csf("shift_name")];
										$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["sewing_line_id"]=$row[('SEWING_LINE')];
										for($h=$hour;$h<$last_hour;$h++)
										{
											$bg=$start_hour_arr[$h];
											$bg_hour=$start_hour_arr[$h];
											//$end=substr(add_time($start_hour_arr[$h],60),0,8);
											$prod_hour="prod_hour".substr($bg_hour,0,2);
											$alter_hour="alter_hour".substr($bg_hour,0,2);
											$spot_hour="spot_hour".substr($bg_hour,0,2);
											$reject_hour="reject_hour".substr($bg_hour,0,2);
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$prod_hour"]=$row[csf("$prod_hour")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$alter_hour"]=$row[csf("$alter_hour")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$spot_hour"]=$row[csf("$spot_hour")];
											$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$reject_hour"]=$row[csf("$reject_hour")];
											
										}
									}
									
									$grand_total = $totalGood_qty+$totalAlter_qty+$totalSpot_qty+$totalReject_qty;
										
									$summary_total_parc=($totalGood_qty/$grand_total)*100;
									$summary_total_parcalter=($totalAlter_qty/$grand_total)*100;
									$summary_total_parcspot=($totalSpot_qty/$grand_total)*100;
									$summary_total_parcreject=($totalReject_qty/$grand_total)*100;
								}
								// echo "<pre>";print_r($production_data);die;
								//=================================== CLEAR TEMP ENGINE ====================================
								$con = connect();
								execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 167 and ref_from in(2)");
								oci_commit($con);
						
								//=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
								fnc_tempengine("gbl_temp_engine", $user_id, 167, 2,$po_id_array, $empty_arr);

								$prod_qnty_data=sql_select("select a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, a.production_quantity as prod_qnty,shift_name from pro_garments_production_mst a,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and production_type=4 and tmp.user_id=$user_id and tmp.entry_form=167 and tmp.ref_from=2 and a.status_active=1 and is_deleted=0 "); 
								$prod_qnty_data_arr = array();
								foreach($prod_qnty_data as $v)
								{
									$prod_qnty_data_arr[$v["FLOOR_ID"]][$v["LOCATION"]][$v["PROD_RESO_ALLO"]][$v["SEWING_LINE"]][$v["PO_BREAK_DOWN_ID"]][$v['SHIFT_NAME']] +=$v["PROD_QNTY"];
								}
								// echo "<pre>";print_r($prod_qnty_data_arr);
								//=================================== CLEAR TEMP ENGINE ====================================
								execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=167 and ref_from in(2)");
								oci_commit($con);
								disconnect($con);
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
					
							  $table_width=1620+($last_hour-$hour+1)*50;
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
                                    <th width="50">Shift</th>
                                    <th width="90">Line No</th>
                                    <th width="110">Job No</th>
                                    <th width="100">Style Ref.</th>
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
                                    foreach($production_data as $floor_id=>$value)
                                    {
										ksort($value);
										foreach($value as $line_name=>$val)
										{
											foreach($val as $item_val)
											{
												foreach($item_val as $shift_data)
												{ 
													foreach($shift_data as $row)
													{
															
														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
														$totalGood += $row[("good_qnty")];
														$totalAlter += $row[("alter_qnty")];
														$totalSpot += $row[("spot_qnty")];
														$totalReject += $row[("reject_qnty")];
													
														$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]][$row["shift_name"]];
 														// echo $row[("floor_id")]."*".$row[("location")]."*".$row[("prod_reso_allo")]."*".$row['sewing_line_id']."*".$row[("po_break_down_id")]."*".$row[("shift_name")]; die;
														
														$order_number=implode(',',array_unique(explode(",",$row[csf("po_number")])));
																									
														?>
														<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
															<td width="20"><? echo $i; ?></td>    
															<td width="100"><p><? echo $location_library[$row[("location")]]; ?></p></td>
															<td width="80"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
															<td width="50"><p><? echo $shift_name[$row["shift_name"]]; ?></p></td>
															<td width="90"><p><? echo $line_name;// $line_library[$row[csf("sewing_line")]];$row[csf("line_name")];; ?></p></td>
															<td width="110" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
															<td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
															<td width="100"><p><? echo $order_number; ?></p></td>
															<td width="60"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
															<td width="150"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td> 
															<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
															<td width="70" align="right">
															<? 
															
															echo $prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour']; 
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
															
															<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd']; ?>&nbsp;</td>
															<td width="70" align="right">
															<? $line_achive=($row[("good_qnty")]+$row[("reject_qnty")])/$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd']*100;
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
															$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
															$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
															$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
														}
														
													
													}
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
                                    <th width="50">&nbsp;</th>
                                    <th width="90">&nbsp;</th>
                                    <th width="110">&nbsp;</th>
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
				$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, group_concat(distinct(a.po_break_down_id)) as po_break_down_id, group_concat(distinct(c.po_number)) as po_number, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  group by a.prod_reso_allo, a.floor_id, a.sewing_line, a.item_number_id, b.job_no order by a.floor_id, a.sewing_line"; // echo $sql;
 		//$txt_date
				
			}
			
			if($db_type==2)
			  {
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id, listagg(c.po_number,',') within group (order by po_number) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,a.shift_name,
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
					 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond $shift_cond group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date,a.shift_name order by a.floor_id, a.sewing_line";  
				
			}
				// echo $sql;die;
				$result = sql_select($sql);
				$totalGood=0;$totalAlter=0;$totalSpot=0;$totalReject=0;$totalinputQnty=0;
				$production_data=array();
				
				$po_id_array = array();
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					//total good,alter,reject qnty
					$totalGood += $row[csf("good_qnty")];
					$totalAlter += $row[csf("alter_qnty")];
					$totalSpot += $row[csf("spot_qnty")];
					$totalReject += $row[csf("reject_qnty")];
					/* $inputQnty=$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]]; */
					
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
					$po_id_array[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
					if($row[csf("prod_reso_allo")]==1)
					{
						//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];a.item_number_id
						$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
						$line_name="";
						foreach($line_resource_mst_arr as $resource_id)
						{
							$line_name.=$lineArr[$resource_id].", ";
						}
						$line_name=chop($line_name," , ");
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["company_id"]=$row[csf("company_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["location"]=$row[csf("location")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["floor_id"]=$row[csf("floor_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["production_date"]=$row[csf("production_date")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["sewing_line"]=$row[csf("sewing_line")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no"]=$row[csf("job_no")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["buyer_name"]=$row[csf("buyer_name")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["item_number_id"]=$row[csf("item_number_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_number"]=$row[csf("po_number")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["supervisor"]=$row[csf("supervisor")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["good_qnty"]=$row[csf("good_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["alter_qnty"]=$row[csf("alter_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["spot_qnty"]=$row[csf("spot_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["reject_qnty"]=$row[csf("reject_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["shift_name"]=$row[csf("shift_name")];
						for($h=$hour;$h<=$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$prod_hour"]=$row[csf("$prod_hour")];
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$alter_hour"]=$row[csf("$alter_hour")];
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$spot_hour"]=$row[csf("$spot_hour")];
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$reject_hour"]=$row[csf("$reject_hour")];
							
						}
					}
					else
					{
						$line_name=$lineArr[$row[csf('sewing_line')]];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["company_id"]=$row[csf("company_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["location"]=$row[csf("location")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["floor_id"]=$row[csf("floor_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["production_date"]=$row[csf("production_date")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["sewing_line"]=$row[csf("sewing_line")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["job_no"]=$row[csf("job_no")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["buyer_name"]=$row[csf("buyer_name")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["item_number_id"]=$row[csf("item_number_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["po_number"]=$row[csf("po_number")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["supervisor"]=$row[csf("supervisor")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["good_qnty"]=$row[csf("good_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["alter_qnty"]=$row[csf("alter_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["spot_qnty"]=$row[csf("spot_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["reject_qnty"]=$row[csf("reject_qnty")];
						$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["shift_name"]=$row[csf("shift_name")];
						for($h=$hour;$h<=$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$prod_hour"]=$row[csf("$prod_hour")];
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$alter_hour"]=$row[csf("$alter_hour")];
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$spot_hour"]=$row[csf("$spot_hour")];
							$production_data[$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("item_number_id")]][$row[csf("shift_name")]]["$reject_hour"]=$row[csf("$reject_hour")];
							
						}
					}
					
					
					
					
				}
				ksort($production_data);

				//=================================== CLEAR TEMP ENGINE ====================================
				$con = connect();
				execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 167 and ref_from in(1)");
				oci_commit($con);
		  
				//=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
				fnc_tempengine("gbl_temp_engine", $user_id, 167, 1,$po_id_array, $empty_arr);

				$prod_qnty_data=sql_select("select a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, a.production_quantity as prod_qnty,shift_name from pro_garments_production_mst a,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and production_type=4 and tmp.user_id=$user_id and tmp.entry_form=167 and tmp.ref_from=1 and a.status_active=1 and is_deleted=0 ");
				$prod_qnty_data_arr = array();
				foreach($prod_qnty_data as $v)
				{
					$prod_qnty_data_arr[$v["FLOOR_ID"]][$v["LOCATION"]][$v["PROD_RESO_ALLO"]][$v["SEWING_LINE"]][$v["PO_BREAK_DOWN_ID"]][$v['SHIFT_NAME']] +=$v["PROD_QNTY"];
				}
				// echo "<pre>"; print_r($prod_qnty_data_arr);die;
				
                $grand_total = $totalGood+$totalAlter+$totalSpot+$totalReject;
                    
                $summary_total_parc=($totalGood/$grand_total)*100;
                $summary_total_parcalter=($totalAlter/$grand_total)*100;
                $summary_total_parcspot=($totalSpot/$grand_total)*100;
                $summary_total_parcreject=($totalReject/$grand_total)*100;

			$subcon_prod_qnty_data = sql_select("select floor_id, location_id, line_id, order_id, sum(production_qnty) as prod_qnty from  subcon_gmts_prod_dtls where  production_type=2 group by floor_id, location_id, line_id, order_id");
			foreach($subcon_prod_qnty_data as $row)
			{
				$subcon_prod_qnty_data_arr[$row["FLOOR_ID"]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]]=$row[csf("prod_qnty")];
			}
			
			
			$i=1; $grand_total_good_sub=0; $grand_alter_good_sub=0; $grand_total_spot_sub=0; $grand_total_reject_sub=0;
			$first=1;
			$total_goods=array();
			$total_alter=array();
			$total_reject=array();
			$total_spot=array();
				
			if($db_type==0)
			{
				
				$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id as sewing_line, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job, c.cust_style_ref, b.party_id, a.gmts_item_id, group_concat(distinct(a.order_id)) as po_break_down_id, group_concat(distinct(c.order_no)) as po_number, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
					where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.floor_id, a.line_id, a.gmts_item_id, b.subcon_job order by a.floor_id, a.line_id "; //$txt_date production_date
				
			}//listagg(a.order_id,',') within group (order by order_id) as order_id, listagg(c.order_no,',') within group (order by order_no) as order_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
			//echo $sql_subcon;
			if($db_type==2)
			{
				$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job, c.cust_style_ref, b.party_id, a.gmts_item_id, listagg(a.order_id,',') within group (order by order_id) as po_break_down_id, listagg(c.order_no,',') within group (order by order_no) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
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
						
						$inputQnty=$subcon_prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location_id")]][$row[csf("line_id")]][$row[csf("order_id")]];
						//echo $row[csf("prod_reso_allo")];
						if($row[csf("prod_reso_allo")]==1)
						{
							//$line_name=$lineArr[$prod_reso_line_arr[$row[('sewing_line')]]];gmts_item_id
							$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('line_id')]]);
							$line_name="";
							foreach($line_resource_mst_arr as $resource_id)
							{
								$line_name.=$lineArr[$resource_id].", ";
							}
							$line_name=chop($line_name," , ");
							//echo $row[csf("location")].'==';
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["company_id"]=$row[csf("company_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["location_id"]=$row[csf("location_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["floor_id"]=$row[csf("floor_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["production_date"]=$row[csf("production_date")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["sewing_line"]=$row[csf("line_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["job_no"]=$row[csf("subcon_job")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["cust_style_ref"]=$row[csf("cust_style_ref")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["party_id"]=$row[csf("party_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["po_number"]=$row[csf("po_number")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["supervisor"]=$row[csf("supervisor")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["good_qnty"]=$row[csf("good_qnty")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["alter_qnty"]=$row[csf("alter_qnty")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["spot_qnty"]=$row[csf("spot_qnty")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["reject_qnty"]=$row[csf("reject_qnty")];
							for($h=$hour;$h<$last_hour;$h++)
							{
								$bg=$start_hour_arr[$h];
								$bg_hour=$start_hour_arr[$h];
								//$end=substr(add_time($start_hour_arr[$h],60),0,8);
								$prod_hour="prod_hour".substr($bg_hour,0,2);
								$alter_hour="alter_hour".substr($bg_hour,0,2);
								$spot_hour="spot_hour".substr($bg_hour,0,2);
								$reject_hour="reject_hour".substr($bg_hour,0,2);
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$prod_hour"]=$row[csf("$prod_hour")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$alter_hour"]=$row[csf("$alter_hour")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$spot_hour"]=$row[csf("$spot_hour")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$reject_hour"]=$row[csf("$reject_hour")];
								
							}
						}
						else
						{
							$line_name=$lineArr[$row[('line_id')]];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["company_id"]=$row[csf("company_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["location_id"]=$row[csf("location_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["floor_id"]=$row[csf("floor_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["production_date"]=$row[csf("production_date")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["sewing_line"]=$row[csf("line_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["job_no"]=$row[csf("subcon_job")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["cust_style_ref"]=$row[csf("cust_style_ref")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["party_id"]=$row[csf("party_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["po_number"]=$row[csf("po_number")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["supervisor"]=$row[csf("supervisor")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["good_qnty"]=$row[csf("good_qnty")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["alter_qnty"]=$row[csf("alter_qnty")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["spot_qnty"]=$row[csf("spot_qnty")];
							$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["reject_qnty"]=$row[csf("reject_qnty")];
							for($h=$hour;$h<$last_hour;$h++)
							{
								$bg=$start_hour_arr[$h];
								$bg_hour=$start_hour_arr[$h];
								//$end=substr(add_time($start_hour_arr[$h],60),0,8);
								$prod_hour="prod_hour".substr($bg_hour,0,2);
								$alter_hour="alter_hour".substr($bg_hour,0,2);
								$spot_hour="spot_hour".substr($bg_hour,0,2);
								$reject_hour="reject_hour".substr($bg_hour,0,2);
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$prod_hour"]=$row[csf("$prod_hour")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$alter_hour"]=$row[csf("$alter_hour")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$spot_hour"]=$row[csf("$spot_hour")];
								$production_subcon_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]][$row[csf("gmts_item_id")]]["$reject_hour"]=$row[csf("$reject_hour")];
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

				//=================================== CLEAR TEMP ENGINE ====================================
				$con = connect();
				execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 167 and ref_from in(1)");
				oci_commit($con);
				disconnect($con);
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
            $table_width=1590+($last_hour-$hour+1)*50;
           ?>
            <div style="width:200px; font-weight:bold">Production-Regular Order</div>
            <div>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                <thead> 	 	 	 	 	 	
                    <tr height="50">
                        <th width="30">Sl.</th>    
                        <th width="90">Location</th>
                        <th width="70">Floor</th>
                        <th width="50">Shift</th>
                        <th width="50">Line No</th>
                        <th width="50">Job No</th>
                        <th width="110">Style Ref.</th>
                        <th width="100">Order No</th>
                        <th width="60">Buyer</th>
                        <th width="100">Item</th>
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
					//ksort($production_data);
                    foreach($production_data as $fsl=>$fsl_value)
                    {
						 foreach($fsl_value as $flowre_id=>$value)
                    	{
							ksort($value);
							foreach($value as $line_name=>$val)
							{
								foreach($val as $item_val)
								{
									foreach($item_val as $shift_val )
									{
										foreach($shift_val as $row )
										{
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
											//total good,alter,reject qnty
											$totalGood_qty += $row[("good_qnty")];
											$totalAlter_qty += $row[("alter_qnty")];
											$totalSpot_qty += $row[("spot_qnty")];
											$totalReject_qty += $row[("reject_qnty")];
											$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]][$row['shift_name']];
											$order_number=implode(',',array_unique(explode(",",$row[("po_number")])));
											?>
											<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td width="30" align="center"><? echo $i; ?></td>    
												<td width="90" align="center"><p><? echo $location_library[$row["location"]]; ?></p></td>
												<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
												<td width="50" align="center"><p><?= $shift_name[$row['shift_name']];?></p></td>
												<td width="50" align="center"><p><? echo $line_name;// $line_library[$row[csf("sewing_line")]];$row[csf("line_name")];; ?></p></td>
												<td width="50" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
												<td width="110" align="center"><p><? echo $row[("style_ref_no")]; ?></p></td>
												<td  width="100" align="center"><a href="##" onClick="openmypage_order(<? echo $cbo_company_name; ?>,'<?=$row[("po_break_down_id")] ?>','<?=$row[("item_number_id")] ?>','OrderPopup')"><?= $order_number; ?></a></td>
											
												<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
												<td width="100" align="center"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td> 
												<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
												<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour']; ?>&nbsp;</td>
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
												
												<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][$row[csf('production_date')]]['tpd']; ?>&nbsp;</td>
												<td width="70" align="right">
													<? $line_achive=($row[("good_qnty")]+$row[csf("reject_qnty")])/$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd']*100;
													echo number_format($line_achive,2)."%"; ?>&nbsp;</td>
												<? $expArr = explode(",",$row[csf("supervisor")]); ?>
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
												$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
												$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
												$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
											}
										}
									
									}
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
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="100" align="right">Grand Total: </th>  
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
                        <th width="50">Line No</th>
                        <th width="50">Job No</th>
                        <th width="110">Style Ref.</th>
                        <th width="100">Order No</th>
                        <th width="60">Buyer</th>
                        <th width="100">Item</th>
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
							foreach($val as $item_val)
							{
								foreach($item_val as $row)
								{
									
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									//total good,alter,spot,reject qnty
									$totalGoodSubSub += $row[("good_qnty")];
									$totalAlterSubSub += $row[("alter_qnty")];
									$totalSpotSubSub += $row[("spot_qnty")];
									$totalRejectSubSub += $row[("reject_qnty")];
									
									$inputQntySub=$subcon_prod_qnty_data_arr[$row["floor_id"]][$row["location_id"]][$row["sewing_line"]][$row["po_break_down_id"]];
									//echo $row["location"].'==';
									
									$order_num="";
									$ex_po=array_unique(explode(',',$row[("po_number")]));
									foreach($ex_po as $po_no)
									{
										if($order_num=="") $order_num=$po_no; else $order_num.=','.$po_no;
									}
									
									?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="30" align="center"><? echo $i; ?></td>    
										<td width="90" align="center"><p><? echo $location_library[$row["location_id"]]; ?></p></td>
										<td width="70" align="center"><p><? echo $floor_library[$row["floor_id"]]; ?></p></td>
										<td width="50" align="center"><p><? echo $line_name; ?></p></td>
										<td width="50" align="center"><p><? echo $row["job_no_prefix_num"]; ?></p></td>
										<td width="110" align="center"><p><? echo $row["cust_style_ref"]; ?></p></td>
										<td width="100" align="center"><p><? echo $order_num; ?></p></td>
										<td width="60" align="center"><p><? echo $buyer_short_library[$row["party_id"]]; ?></p></td>
										<td width="100" align="center"><p><? echo $garments_item[$row["gmts_item_id"]]; ?></p></td> 
										<td width="70" align="right"><p><? echo $inputQntySub; ?></p></td> 
										<td width="70" align="right"><? echo $prod_resource_array[$row['sewing_line']][$row['production_date']]['target_per_hour']; ?>&nbsp;</td>
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
										<td width="70" align="right"><? echo $prod_resource_array[$row[('line_id')]][$row[('production_date')]]['tpd']; ?>&nbsp;</td>
										<td width="70" align="right">
										<? $line_achive_sub=($row[("good_qnty")]+$row[("reject_qnty")])/$prod_resource_array[$row[('line_id')]][$row[('production_date')]]['tpd']*100;
										echo number_format($line_achive_sub,2)."%"; ?>&nbsp;</td>
										<? $expArr = explode(",",$row[("supervisor")]); ?>
										<td width=""><? echo $expArr[count($expArr)-1]; ?></td>  
									</tr>
									<?
									$i++;
									$totalinputQntySub+=$inputQntySub;
									//$totaltargetperhouresub+=$prod_resource_array[$row[('line_id')]][$row[('production_date')]]['target_per_hour'];
									//$totaldaytargetsub+=$prod_resource_array[$row[('line_id')]][$row[('production_date')]]['tpd'];
									$totallineachivepesubr+=$line_achive;
									
									if($duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=="")
									{
										$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
										$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
										$duplicate_array[$row[('prod_reso_allo')]][$row[('sewing_line')]]=$row[('sewing_line')];
									}
								
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
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="100" align="right">Grand Total: </th>  
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
		
		$sql_line_remark=sql_select("select remarks,production_hour from pro_garments_production_mst where company_id=".$company_id." and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and status_active=1 and is_deleted=0 order by production_hour");
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

if ($action=='OrderPopup')
{
	// echo load_html_head_contents("Line Item Wise Production Report", "../../../", 1, 1,$unicode,'','');
	echo load_html_head_contents("Line Item Wise Production Report", "../../../", 1, 1,'','','');
	// print_r($_REQUEST);
	extract($_REQUEST);

	$po_id=explode("*",$order_id);
	$po_id=implode(",",$po_id);
	$item_id=$_REQUEST['item_id'];
	$company_name=str_replace("'","",$_REQUEST['company_name']);
	$color_variable_setting=return_field_value("ex_factory","variable_settings_production","company_name='$company_id' and variable_list=1 and status_active=1 and is_deleted=0","ex_factory");
	//echo $color_variable_setting;die;
	$status_active=$_SESSION["status_active"];
	if($status_active==1)
	{
		$po_tbl_cond=" and a.status_active=1 ";
		$po_tbl_cond2=" and b.status_active=1 ";

	}
	else if($status_active==2)
	{
		$po_tbl_cond=" and a.status_active=2 ";
		$po_tbl_cond2=" and b.status_active=2 ";

	}
	else if($status_active==3)
	{
		$po_tbl_cond=" and a.status_active=3 ";
		$po_tbl_cond2=" and b.status_active=3 ";

	}
	else if($status_active==4)
	{
		$po_tbl_cond=" and a.status_active in(1,2,3) ";
		$po_tbl_cond2=" and b.status_active in(1,2,3) ";

	}

	$ex_fact_qty_arr=array();
	if($color_variable_setting==2 || $color_variable_setting==3)
	{
		$sql_exfect="SELECT c.color_number_id, c.size_number_id, sum(CASE WHEN entry_form!=85 THEN production_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN production_qnty ELSE 0 END) AS production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.item_number_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  c.color_number_id, c.size_number_id";//sum(b.production_qnty) as production_qnty
		//echo $sql_exfect;die;
		$sql_result_exfact=sql_select($sql_exfect);
		foreach($sql_result_exfact as $row)
		{
			$ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("production_qnty")];
		}
	}

 ?>
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 </div>

	<div style="width:700px" align="center" id="details_reports">
		<legend>Color And Size Wise Summary</legend>
		<table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
			<thead>
				<tr>
					<th width="100">Buyer</th>
					<th width="100">Job Number</th>
					<th width="100">Style Name</th>
					<th width="300">Order Number</th>
					<th width="100">Internal Ref</th>
					<th width="100">Ship Date</th>
					<th width="100">Item Name</th>
					<th width="100">Order Qty.</th>
				</tr>
			</thead>
			<?
				$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
				
					$sql = "SELECT a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio,a.grouping
						from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
						where a.job_id=b.id and b.id=c.job_id and a.id in ($po_id) and c.gmts_item_id=$item_id $po_tbl_cond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio,a.grouping";
				
				// echo $sql;die;
				$resultRow=sql_select($sql);

				$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");

			?>
			<tr>
				<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
				<td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
				<td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
				<td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
				<td><p><? echo $resultRow[0][csf("grouping")]; ?></p></td>
				<td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
				<td><? echo $garments_item[$item_id]; ?></td>
				<td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
			</tr>
			<?
			$prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_id) and item_number_id=$item_id and is_deleted=0 and status_active=1");
			// echo $prod_sewing_sql;die;
			foreach($prod_sewing_sql as $sewingRow);
			?>
			<tr>
				<td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
				<td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
				<td></td>
				<td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
			</tr>
		</table>
		<?

		$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

		$color_library=array(); $size_library=array(); $color_library_plan=array(); $dataQty=array();
		$colorSizeData=sql_select("SELECT color_number_id, size_number_id, order_quantity, plan_cut_qnty, excess_cut_perc from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 order by size_order");
		// echo $colorSizeData;die;
		foreach($colorSizeData as $csRow)
		{
			if($csRow[csf('color_number_id')]>0)
			{
				$color_library[$csRow[csf('color_number_id')]]+=$csRow[csf('order_quantity')];
				$color_library_plan[$csRow[csf('color_number_id')]]+=$csRow[csf('plan_cut_qnty')];
			}

			if($csRow[csf('size_number_id')]>0)
			{
				$size_library[$csRow[csf('size_number_id')]]=$csRow[csf('size_number_id')];
			}

			$dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][1]+=$csRow[csf('order_quantity')];
			$dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][2]+=$csRow[csf('plan_cut_qnty')];
			$dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][3]+=$csRow[csf('excess_cut_perc')];
			$dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][4]+=1;
		}

		$prodDataQty=array();
		
					$prod_sql=sql_select("SELECT d.color_number_id, d.size_number_id,
					NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
					NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
					NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
					NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
					NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty,
					NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
					NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty,
					NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
					NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty,
					NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
					NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
					NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qty ELSE 0 END),0) AS sewingout_reject,
					NVL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty,
					NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
					NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty,
					NVL(sum(CASE WHEN c.production_type ='80' THEN c.production_qnty ELSE 0 END),0) AS woven_finish_qnty,
					NVL(sum(CASE WHEN c.production_type ='9' THEN c.production_qnty ELSE 0 END),0) AS cutting_delivery

				from
					pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
				where
					a.id=c.mst_id and d.po_break_down_id in (".$po_id.") and a.item_number_id='$item_id' and c.color_size_break_down_id=d.id and c.status_active=1 and a.status_active=1  group by d.color_number_id, d.size_number_id");

					//echo $prod_sql;die;
	

		foreach($prod_sql as $row)
		{
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cutting_qnty']=$row[csf('cutting_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printing_qnty']=$row[csf('printing_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printreceived_qnty']=$row[csf('printreceived_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['emb_qnty']=$row[csf('emb_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['embreceived_qnty']=$row[csf('embreceived_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['wash_qnty']=$row[csf('wash_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['washreceived_qnty']=$row[csf('washreceived_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sp_qnty']=$row[csf('sp_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['spreceived_qnty']=$row[csf('spreceived_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewingin_qnty']=$row[csf('sewingin_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewingout_qnty']=$row[csf('sewingout_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewingout_reject']=$row[csf('sewingout_reject')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finishin_qnty']=$row[csf('finishin_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['iron_qnty']=$row[csf('iron_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finish_qnty']=$row[csf('finish_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['woven_finish_qnty']=$row[csf('woven_finish_qnty')];
			$prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cutting_delivery_qnty']=$row[csf('cutting_delivery')];
		}
		// var_dump($color_library1);
		// echo "<br>";
		//  print_r($size_library1);]

		/*$color_library=sql_select("select distinct(color_number_id) as color_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1");
		$size_library=sql_select("select distinct(size_number_id) as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_number_id!=0 and status_active=1");*/
		$prod_defect_sql = "SELECT a.defect_qty, c.po_break_down_id, c.item_number_id,d.color_number_id,d.size_number_id
		from pro_gmts_prod_dft a, pro_garments_production_mst c,wo_po_color_size_breakdown d
		where a.mst_id=c.id and c.po_break_down_id=d.po_break_down_id and d.id=a.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and c.po_break_down_id in (".$po_id.") and a.defect_point_id=50 and a.defect_type_id=2 and a.production_type = 5";
		//   echo $prod_defect_sql;die;
		$defect_arr=array();
		$defect_query=sql_select($prod_defect_sql);

		foreach($defect_query as $v)
		{
			$defect_arr[$v[csf('color_number_id')]][$v[csf('size_number_id')]]['defect_qty']+=$v[csf('defect_qty')];
		}
		//   echo '<pre>';print_r($defect_arr); echo '</pre>';
		$count = count($size_library);
		$width= $count*70+350;
		?>
		<div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:700px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
		<table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
			<thead>
				<tr>
					<th width="100">Color Name</th>
					<th width="170">Production Type</th>
					<?
					foreach($size_library as $sizeId=>$val)
					{
					?>
						<th width="80"><? echo $size_Arr_library[$sizeId]; ?></th>
					<?
					}
					?>
					<th width="60">Total</th>
			</tr>
			</thead>
			<?

			foreach($color_library as $colorId=>$totalorderqnty)
			{
				if($color_variable_setting==2 || $color_variable_setting==3) $row_span=29; else $row_span=28;
				?>
				<tr>
					<td rowspan="<? echo $row_span; ?>" valign="middle"><? echo $color_Arr_library[$colorId]; ?></td>
					<?
						$bgcolor1="#E9F3FF";
						$bgcolor2="#FFFFFF";
					?>
				</tr>
				<tr bgcolor="<? echo $bgcolor1; ?>">
					<td><b>Order Quantity</b></td>
					<?
					$color_size_qty=0;
					foreach($size_library as $sizeId=>$sizeRes)
					{
						$color_size_qty=$dataQty[$colorId][$sizeId][1];
					?>
						<td><? echo $dataQty[$colorId][$sizeId][1]; ?></td>
					<?
					}
					?>
					<td><? echo $totalorderqnty; ?></td>
				</tr>
				<tr bgcolor="<? echo $bgcolor2; ?>">
					<td><b>Plan To Cut (AVG <? echo number_format($dataQty[$colorId][$sizeId][3]/$dataQty[$colorId][$sizeId][4],2); ?>)% </b></td>
					<?
					foreach($size_library as $sizeId=>$sizeRes)
					{
					?>
						<td title="Excess Cut <? echo $dataQty[$colorId][$sizeId][3]; ?>%"><? echo $dataQty[$colorId][$sizeId][2]; ?></td>
					<?
					}
					?>
					<td><? echo $color_library_plan[$colorId]; ?></td>
				</tr>
				<?
					$total_cutting=0; $total_cutting_balance=0; $total_sew_in=0; $total_sew_in_bla=0; $total_sew_out=0; $total_sew_out_bla=0; $total_fin_in=0;$total_fin_out=0; $total_fin_out_bla=0; $total_iron_out=0; $total_exfact_qnty=0; $inhand_qnty=0;  $total_exfact_qnty_bla=0;$total_sewloss_qnty=0;
					$total_print_issue=0;$total_print_rcv=0;$total_cutting_delivery = 0; $total_cutting_delivery_bla=0; $total_embro_issue=0;$total_embro_rcv=0; $total_sp_issue=0;$total_sp_rcv=0; $total_wash_issue=0;$total_wash_rcv=0; $sewingout_reject=0; $totalsewout_reject_bal=0;
					$cutting_html=''; $cutting_balance_html='';$sewin_html=''; $sewin_blance_html='';$sewout_html=''; $sewloss_html='';$sewoutreject_html=''; $sewoutreject_html_bla=""; $sewout_html_bla='';$finisin_html='';$finisout_html=''; $finisout_html_bln='';$iron_html=''; $exfact_html=''; $exfact_html_bla=''; $inhand_html=''; $woven_finish_html='';
					$printiss_html=''; $printrcv_html=''; $cutting_delivery_html=''; $cutting_delivery_html_bla=''; $embroiss_html=''; $embrorcv_html=''; $spiss_html=''; $sprcv_html=''; $washiss_html=''; $washrcv_html='';
					foreach($size_library as $sizeId=>$sizeRes)
					{
						$cutting_qnty=$prodDataQty[$colorId][$sizeId]['cutting_qnty'];
						$printing_qnty=$prodDataQty[$colorId][$sizeId]['printing_qnty'];
						$printreceived_qnty=$prodDataQty[$colorId][$sizeId]['printreceived_qnty'];
						$emb_qnty=$prodDataQty[$colorId][$sizeId]['emb_qnty'];
						$embreceived_qnty=$prodDataQty[$colorId][$sizeId]['embreceived_qnty'];
						$wash_qnty=$prodDataQty[$colorId][$sizeId]['wash_qnty'];
						$washreceived_qnty=$prodDataQty[$colorId][$sizeId]['washreceived_qnty'];
						$sp_qnty=$prodDataQty[$colorId][$sizeId]['sp_qnty'];
						$spreceived_qnty=$prodDataQty[$colorId][$sizeId]['spreceived_qnty'];
						$sewingin_qnty=$prodDataQty[$colorId][$sizeId]['sewingin_qnty'];
						$sewingout_qnty=$prodDataQty[$colorId][$sizeId]['sewingout_qnty'];
						$sewloss_qnty=$defect_arr[$colorId][$sizeId]['defect_qty'];
						$sewingout_reject=$prodDataQty[$colorId][$sizeId]['sewingout_reject'] - $sewloss_qnty;
						$finishin_qnty=$prodDataQty[$colorId][$sizeId]['finishin_qnty'];
						$iron_qnty=$prodDataQty[$colorId][$sizeId]['iron_qnty'];
						$finish_qnty=$prodDataQty[$colorId][$sizeId]['finish_qnty'];
						$woven_finish_qnty=$prodDataQty[$colorId][$sizeId]['woven_finish_qnty'];
						$cutting_delivery_qnty=$prodDataQty[$colorId][$sizeId]['cutting_delivery_qnty'];
						$resRow[csf($col)]=$dataQty[$colorId][$sizeId][2];
						if($cutting_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($cutting_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($cutting_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$cutting_html .='<td '.$bgCol.'>'.$cutting_qnty.'</td>';
						$cutting_balance_html .='<td '.$bgCol2.'>'.($dataQty[$colorId][$sizeId][1]-$prodDataQty[$colorId][$sizeId]['cutting_qnty']).'</td>';

						$total_cutting+=$cutting_qnty;
						$total_cutting_balance+=$dataQty[$colorId][$sizeId][1]-$cutting_qnty;

						if($cons_embr>0)
						{
							if($printing_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($printing_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($printing_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						$printiss_html .='<td '.$bgCol.'>'.$printing_qnty.'</td>';
						$total_print_issue+=$printing_qnty;

						if($cons_embr>0)
						{
							if($printreceived_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($printreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($printreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';

						$printrcv_html .='<td '.$bgCol.'>'.$printreceived_qnty.'</td>';
						$total_print_rcv+=$printreceived_qnty;

						if($cons_embr>0)
						{
							if($emb_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($emb_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($emb_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						$embroiss_html .='<td '.$bgCol.'>'.$emb_qnty.'</td>';
						$total_embro_issue+=$emb_qnty;

						if($cons_embr>0)
						{
							if($embreceived_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($embreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($embreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';

						$embrorcv_html .='<td '.$bgCol.'>'.$embreceived_qnty.'</td>';
						$total_embro_rcv+=$embreceived_qnty;

						if($cons_embr>0)
						{
							if($sp_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($sp_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($sp_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						$spiss_html .='<td '.$bgCol.'>'.$sp_qnty.'</td>';
						$total_sp_issue+=$sp_qnty;

						if($cons_embr>0)
						{
							if($spreceived_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($spreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($spreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';

						$sprcv_html .='<td '.$bgCol.'>'.$spreceived_qnty.'</td>';
						$total_sp_rcv+=$spreceived_qnty;

						if($cons_embr>0)
						{
							if($wash_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($wash_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($wash_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						$washiss_html .='<td '.$bgCol.'>'.$wash_qnty.'</td>';
						$total_wash_issue+=$wash_qnty;

						if($cons_embr>0)
						{
							if($washreceived_qnty==0)$bgCol="bgcolor='#FF0000'";
							else if($washreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
							else if($washreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';

						$washrcv_html .='<td '.$bgCol.'>'.$washreceived_qnty.'</td>';
						$total_wash_rcv+=$washreceived_qnty;

						if($cutting_delivery_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($cutting_delivery_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($cutting_delivery_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$cutting_delivery_html .='<td '.$bgCol.'>'.$cutting_delivery_qnty.'</td>';
						$cutting_delivery_html_bla .='<td '.$bgCol1.'>'.($prodDataQty[$colorId][$sizeId]['cutting_qnty']-$prodDataQty[$colorId][$sizeId]['cutting_delivery_qnty']).'</td>';
						$total_cutting_delivery+=$cutting_delivery_qnty;
						$total_cutting_delivery_bla+=($prodDataQty[$colorId][$sizeId]['cutting_qnty']-$prodDataQty[$colorId][$sizeId]['cutting_delivery_qnty']);



						if($sewingin_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($sewingin_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($sewingin_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$sewin_html .='<td '.$bgCol.'>'.$sewingin_qnty.'</td>';
						$sewin_blance_html.='<td '.$bgCol2.'>'.($dataQty[$colorId][$sizeId][1]-$prodDataQty[$colorId][$sizeId]['sewingin_qnty']).'</td>';

						$total_sew_in+=$sewingin_qnty;
						$total_sew_in_bla+=$dataQty[$colorId][$sizeId][1]-$sewingin_qnty;

						if($inhand_qnty==0)$bgCol="bgcolor='#FF0000'";

						else if($sewingin_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($sewingin_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$inhand_html .='<td '.$bgCol1.'>'.($prodDataQty[$colorId][$sizeId]['cutting_delivery_qnty']-$prodDataQty[$colorId][$sizeId]['sewingin_qnty']).'</td>';
						$inhand_qnty+=$cutting_delivery_qnty-$sewingin_qnty;

						if($sewingout_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($sewingout_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($sewingout_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$sewout_html .='<td '.$bgCol.'>'.$sewingout_qnty.'</td>';
						$total_sew_out+=$sewingout_qnty;
						if($sewloss_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($sewloss_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($sewloss_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$sewloss_html .='<td '.$bgCol.'>'.$sewloss_qnty.'</td>';
						$total_sewloss_qnty+=$sewloss_qnty;

						$total_sew_out_bla+=$dataQty[$colorId][$sizeId][1]-$sewingout_qnty;

						if($sewingout_reject==0)$bgCol="bgcolor='#FF0000'";
						else if($sewingout_reject < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($sewingout_reject >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$sewoutreject_html .='<td '.$bgCol.'>'.$sewingout_reject.'</td>';
						$sewoutreject_html_bla .='<td '.$bgCo2.' title="Sewing Balance=Sewing Input-(Sewing Output+Sewing Out Reject+Sew Loss)">'.($sewingin_qnty- ($sewingout_qnty+$sewingout_reject+$sewloss_qnty)).'</td>';

						$totalsewout_reject+=$sewingout_reject;
						$totalsewout_reject_bal+=($sewingin_qnty- ($sewingout_qnty+$sewingout_reject+$sewloss_qnty));

						$sewout_html_bla .='<td '.$bgCo2.'>'.($dataQty[$colorId][$sizeId][1]- $prodDataQty[$colorId][$sizeId]['sewingout_qnty']).'</td>';
						/*if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'";
						else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
						$total_fin_in+=$prodRow[csf("finishin_qnty")];*/

						if($finish_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($finish_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($finish_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$finisout_html .='<td '.$bgCol.'>'.$finish_qnty.'</td>';
						$finisout_html_bln .='<td '.$bgCol2.'>'.($dataQty[$colorId][$sizeId][1]- $prodDataQty[$colorId][$sizeId]['finish_qnty']).'</td>';
						$total_fin_out+=$finish_qnty;
						$total_fin_out_bla+=$dataQty[$colorId][$sizeId][1]-$finish_qnty;

						if($woven_finish_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($woven_finish_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($woven_finish_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$woven_finish_html .='<td '.$bgCol.'>'.$woven_finish_qnty.'</td>';
						$total_woven_finish_qnty+=$woven_finish_qnty;

						if($iron_qnty==0)$bgCol="bgcolor='#FF0000'";
						else if($iron_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						else if($iron_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						$iron_html .='<td '.$bgCol.'>'.$iron_qnty.'</td>';
						$total_iron_out+=$iron_qnty;

						//if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'";
						//else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
						//else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						if($color_variable_setting==2 || $color_variable_setting==3)
						{
							$bgCol=="bgcolor='#FFFFFF'";
							$exfact_html.='<td>'.$ex_fact_qty_arr[$colorId][$sizeId].'&nbsp;</td>';

							$total_exfact_qnty+=$ex_fact_qty_arr[$colorId][$sizeId];
						}

						$exfact_html_bla.='<td>'.($dataQty[$colorId][$sizeId][1]-$ex_fact_qty_arr[$colorId][$sizeId]).'&nbsp;</td>';

					$total_exfact_qnty_bla+=$dataQty[$colorId][$sizeId][1]-$ex_fact_qty_arr[$colorId][$sizeId];


					}// end size foreach loop

					?>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Cutting</b></td>
							<? echo $cutting_html; ?>
							<td><? echo $total_cutting; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Cutting Balance</b></td>
							<? echo $cutting_balance_html; ?>
							<td><? echo $total_cutting_balance ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Print Issue</b></td>
							<? echo $printiss_html; ?>
							<td><? echo $total_print_issue; ?></td>
						</tr>

						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Print Received</b></td>
							<? echo $printrcv_html; ?>
							<td><? echo $total_print_rcv; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Embro Issue</b></td>
							<? echo $embroiss_html; ?>
							<td><? echo $total_embro_issue; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Embro Received</b></td>
							<? echo $embrorcv_html; ?>
							<td><? echo $total_embro_rcv; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Issue For Special Works</b></td>
							<? echo $spiss_html; ?>
							<td><? echo $total_sp_issue; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Recv. From Special Works</b></td>
							<? echo $sprcv_html; ?>
							<td><? echo $total_sp_rcv; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Cutting Delivery</b></td>
							<? echo $cutting_delivery_html; ?>
							<td><? echo $total_cutting_delivery; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Cutting Delivery Balance </b></td>
							<? echo $cutting_delivery_html_bla; ?>
							<td><? echo $total_cutting_delivery_bla ;?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Sewing Input</b></td>
						<? echo $sewin_html; ?>
							<td><? echo $total_sew_in; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Sewing Input Balance </b></td>
							<?	echo $sewin_blance_html?>
							<td><? echo $total_sew_in_bla?></td>
						</tr>

						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Inhand(Supper shop balance)</b></td>
							<? echo $inhand_html ?>
							<td><? echo $inhand_qnty  ?></td>
						</tr>

						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Sewing Output</b></td>
							<? echo $sewout_html; ?>
							<td><? echo $total_sew_out; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Sew Loss</b></td>
							<? echo $sewloss_html; ?>
							<td><? echo $total_sewloss_qnty; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Sewing Out Reject</b></td>
							<? echo $sewoutreject_html; ?>
							<td><? echo $totalsewout_reject; ?></td>
						</tr>

						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Sewing Balance</b></td>
							<? echo $sewoutreject_html_bla; ?>
							<td><? echo $totalsewout_reject_bal; ?></td>
						</tr>

						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><p><b>Sewing Output Balance </b></p></td>
							<? echo $sewout_html_bla ;?>
							<td><? echo $total_sew_out_bla ; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Issue For Wash</b></td>
							<? echo $washiss_html; ?>
							<td><? echo $total_wash_issue; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Recv. From Wash</b></td>
							<? echo $washrcv_html; ?>
							<td><? echo $total_wash_rcv; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Iron Output</b></td>
							<? echo $iron_html; ?>
							<td><? echo $total_iron_out; ?></td>
						</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Finishing Output</b></td>
						<? echo $finisout_html; ?>
							<td><? echo $total_fin_out; ?></td>
						</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
							<td><b>Woven Finish Output</b></td>
						<? echo $woven_finish_html; ?>
							<td><? echo $total_woven_finish_qnty; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Finishing Balance</b></td>
							<? echo $finisout_html_bln ?>
							<td><? echo $total_fin_out_bla  ?></td>
						</tr>
						<?
						if($color_variable_setting==2 || $color_variable_setting==3)
						{
							?>
							<tr>
								<td><b>Ex-Factory Qty.</b></td>
								<? echo $exfact_html; ?>
								<td><? echo $total_exfact_qnty; ?>&nbsp;</td>

							</tr>

							<?

						}
						?>
						<tr>
							<td><b>Ex-Factory Balance.</b></td>
							<? echo $exfact_html_bla; ?>
							<td><? echo $total_exfact_qnty_bla; ?>&nbsp;</td>
						</tr>
				<?
				$totalsewout_reject = 0;
				}// end color foreach loop
				?>


	</table>
	</div>


 <?
 exit();

}// end if condition


?>