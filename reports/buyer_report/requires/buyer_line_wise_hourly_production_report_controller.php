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
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/buyer_line_wise_hourly_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/buyer_line_wise_hourly_production_report_controller',document.getElementById('cbo_floor').value+'_'+this.value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/buyer_line_wise_hourly_production_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 ); 
	
	//echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/buyer_line_wise_hourly_production_report_controller', this.value, 'load_drop_down_line', 'line_td' );",0 ); 
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
	
	//echo $txt_date;
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.serving_company=$cbo_company_name";
	if(str_replace("'","",$cbo_location)==0)$location="";else $location=" and a.location=$cbo_location";
	if(str_replace("'","",$cbo_floor)==0)$floor="";else $floor=" and a.floor_id=$cbo_floor";
	if(str_replace("'","",$cbo_line)==0)$line="";else $line=" and a.sewing_line=$cbo_line";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	if(str_replace("'","",trim($txt_style_no))=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no=$txt_style_no";
	
	$prod_resource_array=array();
	$dataArray=sql_select("SELECT a.id, a.line_number, a.floor_id, b.pr_date, b.target_per_hour, b.working_hour, b.style_ref_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name");

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
		$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=19;
		//echo $hour;
		//print_r($start_time);
		$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
		
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		//print_r($start_hour_arr);
		for($j=$hour;$j<$last_hour;$j++)
		{
			//echo $last_hour;
			$start_hour=add_time($start_hour,60);
			 $time_valid="19:00:00";
			 if($time_valid>=$start_hour)
			 {
				// echo $start_hour;
				 $start_hour_arr[$j+1]=substr($start_hour,0,5);
			 }
			 //$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		//print_r($start_hour_arr);
	    $start_hour_arr[$j+1]='19:59';
		//print_r($start_hour_arr);
	//var_dump($prod_resource_array);
	
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
			
			for($h=$hour;$h<$last_hour+1;$h++)
		         {
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					$end=substr(add_time($start_hour_arr[$h],60),0,8);
					$prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);//TIME_FORMAT(c.production_hour, '%H:%i' )
					if($first==1)
					{
					 $sql.="sum(CASE WHEN a.production_hour<='$end' and TIME_FORMAT(a.production_hour, '%H:%i')<='$time_valid'  and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					        sum(CASE WHEN a.production_hour<='$end'  and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
							sum(CASE WHEN a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
							sum(CASE WHEN a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
					else
					{
				    $sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					       sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
						   sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
						   sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				$first=$first+1;
				}
				$prod_hour="prod_hour".$last_hour;
				$alter_hour="alter_hour".$last_hour;
				$spot_hour="spot_hour".$last_hour;
				$reject_hour="reject_hour".$last_hour;
				$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_hour<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
					     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
					     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
			
																	
				$sql.="	from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  group by a.prod_reso_allo, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line";  
		}
		//echo $$db_type;die;			
		if($db_type==2)
		{
			$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id, listagg(c.po_number,',') within group (order by po_number) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
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
					//echo $bg.'<br>';
					$time_valid="19:00:00";
			 			
						
					$bg_hour=$start_hour_arr[$h];
					$end=substr(add_time($start_hour_arr[$h],60),0,8);
					$prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);
					//if($time_valid>=$end)
					//{
					if($first==1)
					{
					 $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
							sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
							sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
					else
					{
				    $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
						   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
						   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				$first=$first+1;
					//}
			}
			$prod_hour="prod_hour".$last_hour;
			$alter_hour="alter_hour".$last_hour;
			$spot_hour="spot_hour".$last_hour;
			$reject_hour="reject_hour".$last_hour;
			$time_valid2="19:00:00";
			$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
					     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
					     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
																	
				$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
					 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line"; 
		}//$txt_date
		//echo $sql;
								
		$result = sql_select($sql);
		$po_id_array = array();
		foreach ($result as $key => $val) 
		{
			$po_id_array[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
		}
		$po_id_cond = where_con_using_array($po_id_array,0,"po_break_down_id");

		$prod_qnty_data=sql_select("SELECT floor_id, location, prod_reso_allo, sewing_line, po_break_down_id, production_quantity as prod_qnty from pro_garments_production_mst where  production_type=4 and status_active=1 and is_deleted=0 $po_id_cond");
		foreach($prod_qnty_data as $row)
		{
			$prod_qnty_data_arr[$row[csf("floor_id")]][$row[csf("location")]][$row[csf("prod_reso_allo")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]]+=$row[csf("prod_qnty")];
		}	
		// echo "<pre>";print_r($prod_qnty_data_arr);echo "</pre>";
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
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["sewing_line"]=$row[csf('sewing_line')];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("item_number_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]=$row[csf("good_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]=$row[csf("alter_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]=$row[csf("spot_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]=$row[csf("reject_qnty")];
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					 $bg_hour=$start_hour_arr[$h];
					//$end=substr(add_time($start_hour_arr[$h],60),0,8);
					$prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]=$row[csf("$prod_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]=$row[csf("$alter_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]=$row[csf("$spot_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]=$row[csf("$reject_hour")];
					
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
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("item_number_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]=$row[csf("good_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]=$row[csf("alter_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]=$row[csf("spot_qnty")];
				$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]=$row[csf("reject_qnty")];
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					//$end=substr(add_time($start_hour_arr[$h],60),0,8);
					 $prod_hour="prod_hour".substr($bg_hour,0,2);
					$alter_hour="alter_hour".substr($bg_hour,0,2);
					$spot_hour="spot_hour".substr($bg_hour,0,2);
					$reject_hour="reject_hour".substr($bg_hour,0,2);
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]=$row[csf("$prod_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]=$row[csf("$alter_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]=$row[csf("$spot_hour")];
					$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]=$row[csf("$reject_hour")];
					
				}
			}
			// echo "<pre>";print_r($production_data);echo "</pre>";
			
			$grand_total = $totalGood_qty+$totalAlter_qty+$totalSpot_qty+$totalReject_qty;
				
			$summary_total_parc=($totalGood_qty/$grand_total)*100;
			$summary_total_parcalter=($totalAlter_qty/$grand_total)*100;
			$summary_total_parcspot=($totalSpot_qty/$grand_total)*100;
			$summary_total_parcreject=($totalReject_qty/$grand_total)*100;
		}
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

	  	$table_width=1570+($last_hour-$hour+1)*50;
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
                        <th width="100">Order No</th>
                        <th width="60">Buyer</th>
                        <th width="150">Item</th> 
                        <th width="70">Input Qnty</th>
                        <th width="70">Hourly Target</th>
                        <th width="80">Quality</th>
						 <?
                        for($k=$hour+1; $k<=$last_hour+1; $k++)
                        {
							 
							  $time_valid="19:00";
							// if($time_valid>=$k)
							// {
								//echo $k;
                        ?>
                          <th width="50" style="vertical-align:middle"><div class="block_div">
						     <?  //print_r($start_hour_arr[$k]); 
							 echo substr($start_hour_arr[$k],0,5);   ?></div>
                          </th>
                        <?	
							 // }
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
								foreach($val as $row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
									$totalGood += $row[("good_qnty")];
									$totalAlter += $row[("alter_qnty")];
									$totalSpot += $row[("spot_qnty")];
									$totalReject += $row[("reject_qnty")];
									$inputQnty=$prod_qnty_data_arr[$row[("floor_id")]][$row[("location")]][$row[("prod_reso_allo")]][$row[("sewing_line")]][$row[("po_break_down_id")]];
									// echo $row[("floor_id")]."**".$row[("location")]."**".$row[("prod_reso_allo")]."**".$row[("sewing_line")]."**".$row[("po_break_down_id")]."<br>";
									
									$order_number=implode(',',array_unique(explode(",",$row["po_number"])));
																				
								?>
									<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
										<td width="20"><? echo $i; ?></td>    
										<td width="100"><p><? echo $location_library[$row[("location")]]; ?></p></td>
										<td width="80"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
										<td width="90"><p><? echo $line_name;// $line_library[$row[j("sewing_line")]];$row[csf("line_name")];; ?></p></td>
										<td width="110" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
										<td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
										<td width="100"><p><? echo $order_number; ?></p></td>
										<td width="60"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
										<td width="150"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td> 
										<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
										<td width="70" align="right">
										<? 
										
										echo $prod_resource_array[$row[('sewing_line')]][change_date_format($row[('production_date')])]['target_per_hour']; 
										?>&nbsp;</td>
										
									 <td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
									 <?
					
									 for($k=$hour; $k<=$last_hour; $k++)
									  {
										// echo $last_hour=$last_hour+1;
										 $time_valid="19:00";
							 		// 	if($time_valid>=$k)
										// {
										//echo $k;
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
										// }
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
                        <th width="60">&nbsp;</th>
                        <th width="150">Grand Total</th>
                        <th width="70"><? echo $totalinputQnty; ?></th> 
                        <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
                        <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
                        <?
                          for($k=$hour; $k<=$last_hour; $k++)
                           {
							  $time_valid="19:00";
							 // if($time_valid>=$k)
							 // {
							   $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								//$alter_hour="alter_hour".substr($start_hour_arr[$k],0,2)."";
								//$spot_hour="spot_hour".substr($start_hour_arr[$k],0,2)."";
								$reject_hour="reject_hour".substr($start_hour_arr[$k],0,2)."";
						
							  ?>
							   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
						      <?
							 // }
							     
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
					$time_valid="19:00:00";
					if($first==1)
					{
					 $sql.="sum(CASE WHEN a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					        sum(CASE WHEN a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
							sum(CASE WHEN a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
							sum(CASE WHEN a.production_hour<='$end' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
					else
					{
				    $sql.="sum(CASE WHEN a.production_hour>'$bg' and  TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid'  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					       sum(CASE WHEN a.production_hour>'$bg' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid' and  a.production_hour<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
						   sum(CASE WHEN a.production_hour>'$bg' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid' and  a.production_hour<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
						   sum(CASE WHEN a.production_hour>'$bg' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid' and  a.production_hour<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				$first=$first+1;
				}
				$prod_hour="prod_hour".$last_hour;
				$alter_hour="alter_hour".$last_hour;
				$spot_hour="spot_hour".$last_hour;
				$reject_hour="reject_hour".$last_hour;
				$time_valid2="19:00:00";
				$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid2' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid2' and a.production_hour<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
					     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid2' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
					     sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.production_hour,'%H:%i')<='$time_valid2' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
			
																	
				$sql.="	from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  group by a.prod_reso_allo, a.floor_id, a.sewing_line, b.job_no order by a.floor_id, a.sewing_line"; // echo $sql;
 		//$txt_date
				
			}
			
			if($db_type==2)
			  {
					$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.job_no_prefix_num, b.job_no, b.style_ref_no, b.buyer_name, a.item_number_id, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as po_break_down_id, listagg(c.po_number,',') within group (order by po_number) as po_number, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
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
					$time_valid="19:00:00";
					if($first==1)
					{
					 $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
							sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
							sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
					else
					{
				    $sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					       sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
						   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
						   sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid' and  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				$first=$first+1;
				}
				$prod_hour="prod_hour".$last_hour;
				$alter_hour="alter_hour".$last_hour;
				$spot_hour="spot_hour".$last_hour;
				$reject_hour="reject_hour".$last_hour;
				$time_valid2="19:00:00";
				$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
					     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
					     sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$time_valid2' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
																	
				$sql.=" from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
					 where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location $floor $line $txt_date_from $style_no_cond  group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id,a.location, a.floor_id,a.production_date order by a.floor_id, a.sewing_line";  
				
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
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("item_number_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]=$row[csf("good_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]=$row[csf("alter_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]=$row[csf("spot_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]=$row[csf("reject_qnty")];
						for($h=$hour;$h<$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]=$row[csf("$prod_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]=$row[csf("$alter_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]=$row[csf("$spot_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]=$row[csf("$reject_hour")];
							
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
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["item_number_id"]=$row[csf("item_number_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["supervisor"]=$row[csf("supervisor")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["good_qnty"]=$row[csf("good_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["alter_qnty"]=$row[csf("alter_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["spot_qnty"]=$row[csf("spot_qnty")];
						$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["reject_qnty"]=$row[csf("reject_qnty")];
						for($h=$hour;$h<$last_hour;$h++)
						{
							$bg=$start_hour_arr[$h];
							$bg_hour=$start_hour_arr[$h];
							//$end=substr(add_time($start_hour_arr[$h],60),0,8);
							$prod_hour="prod_hour".substr($bg_hour,0,2);
							$alter_hour="alter_hour".substr($bg_hour,0,2);
							$spot_hour="spot_hour".substr($bg_hour,0,2);
							$reject_hour="reject_hour".substr($bg_hour,0,2);
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$prod_hour"]=$row[csf("$prod_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$alter_hour"]=$row[csf("$alter_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$spot_hour"]=$row[csf("$spot_hour")];
							$production_data[$row[csf("floor_id")]][$line_name][$row[csf("job_no")]]["$reject_hour"]=$row[csf("$reject_hour")];
							
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
				
				$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job, c.cust_style_ref, b.party_id, a.gmts_item_id, group_concat(distinct(a.order_id)) as order_id, group_concat(distinct(c.order_no)) as order_no, group_concat(case when a.supervisor!='' then a.supervisor end ) as supervisor,
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
					$time_valid="19:00:00";
					
					
				if($first==1)
					{
			         $sql_subcon.="sum(CASE WHEN a.hour<='$end' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
					        sum(CASE WHEN a.hour<='$end' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							sum(CASE WHEN a.hour<='$end' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							sum(CASE WHEN a.hour<='$end' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				else
					{
				 $sql_subcon.="sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid'  and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
					       sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end'  and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid' and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						   sum(CASE WHEN a.hour>'$bg' and  a.hour<='$end' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				$first=$first+1;
				}
				$prod_hour="prod_hour".$last_hour;
				$alter_hour="alter_hour".$last_hour;
				$spot_hour="spot_hour".$last_hour;
				$reject_hour="reject_hour".$last_hour;
				$time_valid="19:00:00";
				$sql_subcon.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
					     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid'   and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
					     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid'  and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
					     sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and TIME_FORMAT(a.hour,'%H:%i')<='$time_valid'  and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
														
					$sql_subcon.=" from subcon_gmts_prod_dtls a, subcon_ord_mst b, subcon_ord_dtls c
					where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and a.production_date=$txt_date group by a.floor_id, a.line_id, b.subcon_job order by a.floor_id, a.line_id "; //$txt_date production_date
				
			}//listagg(a.order_id,',') within group (order by order_id) as order_id, listagg(c.order_no,',') within group (order by order_no) as order_no, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,
			//echo $sql_subcon;
			if($db_type==2)
			{
				$sql_subcon="select a.company_id, a.location_id, a.floor_id, a.line_id, a.prod_reso_allo, a.production_date, b.job_no_prefix_num, b.subcon_job, c.cust_style_ref, b.party_id, a.gmts_item_id, listagg(a.order_id,',') within group (order by order_id) as order_id, listagg(c.order_no,',') within group (order by order_no) as order_no,
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
					$time_valid="19:00:00";
					
					
				if($first==1)
					{
			         $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid'  and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
					        sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
							sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
							sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')<='$end' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				else
					{
				 $sql_subcon.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
					       sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid' and  TO_CHAR(a.hour,'HH24:MI')<='$end'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
						   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
						   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid' and  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour,";
					}
				$first=$first+1;
				}
				$prod_hour="prod_hour".$last_hour;
				$alter_hour="alter_hour".$last_hour;
				$spot_hour="spot_hour".$last_hour;
				$reject_hour="reject_hour".$last_hour;
				$time_valid2="19:00:00";
				$sql_subcon.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid2' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,
					     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid2' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=2 THEN alter_qnty else 0 END) AS $alter_hour,
					     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid2' and a.production_type=2 THEN reject_qnty else 0 END) AS $reject_hour,
					     sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$time_valid2' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=2 THEN spot_qnty else 0 END) AS $spot_hour";
														
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
							for($h=$hour;$h<$last_hour;$h++)
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
							for($h=$hour;$h<$last_hour;$h++)
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
            $table_width=1540+($last_hour-$hour+1)*50;
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
								
								$time_valid="19:00:00";
								if($time_valid>=$k)
								{
                            ?>
                              <th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
                            
                            <?	
								}
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
							?>
								<tr height="30" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
									<td width="30" align="center"><? echo $i; ?></td>    
									<td width="90" align="center"><p><? echo $location_library[$row[("location")]]; ?></p></td>
									<td width="70" align="center"><p><? echo $floor_library[$row[("floor_id")]]; ?></p></td>
									<td width="70" align="center"><p><? echo $line_name;// $line_library[$row[csf("sewing_line")]];$row[csf("line_name")];; ?></p></td>
									<td width="60" align="center"><p><? echo $row[("job_no_prefix_num")]; ?></p></td>
									<td width="110" align="center"><p><? echo $row[("style_ref_no")]; ?></p></td>
									<td width="100" align="center"><p><? echo $order_number; ?></p></td>
									<td width="60" align="center"><p><? echo $buyer_short_library[$row[("buyer_name")]]; ?></p></td>
									<td width="170" align="center"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td> 
									<td width="70" align="right"><p><? echo $inputQnty; ?></p></td> 
									<td width="70" align="right"><? echo $prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour']; ?>&nbsp;</td>
									<td width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
									
									<?
									for($k=$hour; $k<=$last_hour; $k++)
									{
										 $time_valid="19:00";
										if($time_valid>=$k)
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
										<? $line_achive=($row["good_qnty"]+$row["reject_qnty"])/$prod_resource_array[$row['sewing_line']][$row['production_date']]['tpd']*100;
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
									$totaltargetperhoure+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['target_per_hour'];
									$totaldaytarget+=$prod_resource_array[$row[('sewing_line')]][$row[('production_date')]]['tpd'];
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
                    <th width="60">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="170" align="right">Grand Total: </th>  
                    <th width="70"><? echo $totalinputQnty; ?></th> 
                    <th width="70"><? echo $totaltargetperhoure; ?>&nbsp;</th> 
                    <th width="80">QC Pass<hr>Alter<hr>Spot<hr>Reject</th> 
                      <?
                                      for($k=$hour; $k<=$last_hour; $k++)
                                       {
										   $time_valid="19:00:00";
										if($time_valid>=$k)
										{
												   
										    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									
										  ?>
										   <th align="right" width="50"><? echo $total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $total_spot[$prod_hour];  ?>&nbsp;<hr><? echo $total_reject[$prod_hour]; ?>&nbsp;</th> 
									      <?
										     
									   	}
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
								$time_valid="19:00:00";
								if($time_valid>=$k)
								{
                            ?>
                              <th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
                            
                            <?	
								}
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
									$time_valid="19:00";
									if($time_valid>=$k)
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
							 $time_valid="19:00";
							if($time_valid>=$k)
							{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							?>
							<th align="right" width="50"><? echo $sub_total_goods[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_alter[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_reject[$prod_hour]; ?>&nbsp;<hr><? echo $sub_total_spot[$prod_hour]; ?>&nbsp;</th> 
							<?
							}
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


?>