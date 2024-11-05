<? 
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="report_generate")
{ 	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name" );
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name" );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name" );	 
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

    $cbo_company_name=return_field_value("id", "lib_company", "company_name=$cbo_company_name and status_active=1 and is_deleted=0", "id");
    $cbo_floor=return_field_value("id", "lib_prod_floor", "floor_name=$cbo_floor and status_active=1 and is_deleted=0", "id");
    //echo $cbo_floor.'system';

	$company_cond=$location_cond=$floor_cond=$line_cond='';
	$style_no_cond=$job_no_cond=$order_no_cond='';
	$company_cond=" and a.serving_company=$cbo_company_name";
	$floor_cond=" and a.floor_id=$cbo_floor";
	if(str_replace("'","",$cbo_location) !=0) $location_cond=" and a.location=$cbo_location";
	if(str_replace("'","",$cbo_line) !=0) $line_cond=" and a.sewing_line=$cbo_line";
	if(str_replace("'","",trim($txt_style_no)) !='')  $style_no_cond=" and b.style_ref_no=$txt_style_no";
	if(str_replace("'","",trim($txt_job_no)) !='') $job_no_cond=" and b.job_no_prefix_num=$txt_job_no";
	if(str_replace("'","",trim($txt_order_no)) !='') $order_no_cond=" and c.po_number=$txt_order_no";

	$date_cond='';
	if($db_type==0)
    {
    	$txt_date = date("Y-m-d", strtotime(str_replace("'", "", trim($txt_date))));
    	$date_cond=" and a.production_date='$txt_date'";
    }
    else
    {
    	$txt_date = date("d-M-Y", strtotime(str_replace("'", "", trim($txt_date))));
    	$date_cond=" and a.production_date='$txt_date'";
    }

	$prod_resource_array=array();
    $dataArray=sql_select("SELECT a.ID, a.LINE_NUMBER, a.FLOOR_ID, b.PR_DATE, b.TARGET_PER_HOUR, b.WORKING_HOUR, b.MAN_POWER, b.STYLE_REF_ID, b.ACTIVE_MACHINE, b.OPERATOR, b.HELPER, c.TARGET_EFFICIENCY from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_name and b.pr_date='$txt_date'");  
	foreach($dataArray as $row)
	{
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['TARGET_PER_HOUR']=$row['TARGET_PER_HOUR'];
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['WORKING_HOUR']=$row['WORKING_HOUR'];
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['ACTIVE_MACHINE']=$row['ACTIVE_MACHINE'];
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['TARGET_EFFICIENCY']=$row['TARGET_EFFICIENCY'];
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['OPERATOR']=$row['OPERATOR'];
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['HELPER']=$row['HELPER'];
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['MAN_POWER']=$row['MAN_POWER'];
		$prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['TPD']=$row['TARGET_PER_HOUR']*$row['WORKING_HOUR'];
	}

	$start_time_arr=array();
	/*if($db_type==0)
	{
		$start_time_data_arr=sql_select("SELECT COMPANY_NAME, SHIFT_ID, TIME_FORMAT( prod_start_time, '%H:%i' ) as PROD_START_TIME, TIME_FORMAT( lunch_start_time, '%H:%i' ) as LUNCH_START_TIME from variable_settings_production where company_name in($cbo_company_name) and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("SELECT COMPANY_NAME, SHIFT_ID, TO_CHAR(prod_start_time,'HH24:MI') as PROD_START_TIME, TO_CHAR(lunch_start_time,'HH24:MI') as LUNCH_START_TIME from variable_settings_production where  company_name in($cbo_company_name) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");	
	}

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row['SHIFT_ID']]['PST']=$row['PROD_START_TIME'];
		$start_time_arr[$row['SHIFT_ID']]['LST']=$row['LUNCH_START_TIME'];
	}*/
	
    //$prod_start_hour=$start_time_arr[1]['PST'];
    $prod_start_hour='';
	if($prod_start_hour=="") $prod_start_hour="06:00";
	$start_time=explode(":",$prod_start_hour);
	$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
	//echo $hour;die;
	$lineWiseProd_arr=array(); 
	$prod_arr=array(); 
	$start_hour_arr=array();
	
	$prod_start_hour="06:00";
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour; $j<$last_hour; $j++)
	{		
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
    $start_hour_arr[$j+1]='23:59';
	//print_r($start_hour_arr);
	//var_dump($prod_resource_array);		
	?>

    <div style="width:100%">
        <div style="width:100%; font-weight:bold;"> <? echo $floor_library[$cbo_floor]; ?>: Sewing QC Status (
        	<?  echo date("l, F Y", strtotime($txt_date)); ?>)
        </div>

        <?
		$i=1;	
		//listagg(c.po_number,',') within group (order by po_number) as PO_NUMBER, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as PO_BREAK_DOWN_ID,	
		$sql="SELECT  a.COMPANY_ID, a.LOCATION, a.FLOOR_ID, a.PROD_RESO_ALLO, a.PRODUCTION_DATE, a.SEWING_LINE, b.JOB_NO_PREFIX_NUM, b.JOB_NO, b.STYLE_REF_NO, b.BUYER_NAME, a.ITEM_NUMBER_ID, b.GMTS_ITEM_ID, b.set_break_down as SMV_PCS_SET, rtrim(xmlagg(xmlelement(e,a.po_break_down_id,', ').extract('//text()') order by a.po_break_down_id).getclobval(),', ') as PO_BREAK_DOWN_ID, rtrim(xmlagg(xmlelement(e,c.po_number,', ').extract('//text()') order by c.po_number).getclobval(),', ') as PO_NUMBER, listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as SUPERVISOR, listagg(c.grouping,',') within group (order by grouping) as GROUPING, listagg(c.file_no,',') within group (order by file_no) as FILE_NO,
			sum(a.production_quantity) as GOOD_QNTY, 
			sum(a.alter_qnty) as ALTER_QNTY,
			sum(a.spot_qnty) as SPOT_QNTY, 
			sum(a.reject_qnty) as REJECT_QNTY,";
		$first=1;
		$total_goods=array();
		$total_alter=array();
		$total_reject=array();
		$total_spot=array();
		for($h=$hour; $h<$last_hour; $h++)
		{
			$bg=$start_hour_arr[$h];
			$bg_hour=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,8);
			$prod_hour="PROD_HOUR".substr($bg_hour,0,2);
			$alter_hour="ALTER_HOUR".substr($bg_hour,0,2);
			$spot_hour="SPOT_HOUR".substr($bg_hour,0,2);
			$reject_hour="REJECT_HOUR".substr($bg_hour,0,2);
			if($first==1)
			{
				$sql.=" sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
					sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
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
		$prod_hour="PROD_HOUR".$last_hour;
		$alter_hour="ALTER_HOUR".$last_hour;
		$spot_hour="SPOT_HOUR".$last_hour;
		$reject_hour="REJECT_HOUR".$last_hour;
		$sql.=" sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
			sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]'  and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
			sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
			sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour";
															
		$sql.=" FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
			where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_cond $location_cond $floor_cond $line_cond $date_cond $style_no_cond $order_no_cond $job_no_cond 
			group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no,b.job_no_prefix_num,b.style_ref_no,b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down, a.location, a.floor_id,a.production_date 
			order by a.floor_id, a.sewing_line"; 	
		//echo $sql;die;   

		$result = sql_select($sql);
		$totalGood=$totalAlter=$totalSpot=0;
		$totalReject=$totalinputQnty=0;
		$poId ='';
		$production_data=array();		
		foreach($result as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$order_number = implode(',',array_unique(explode(",",$row[csf('PO_BREAK_DOWN_ID')]->load())));
			$poId .= $order_number.',';

			$totalGood += $row['GOOD_QNTY'];
			$totalAlter += $row['ALTER_QNTY'];
			$totalSpot += $row['SPOT_QNTY'];
			$totalReject += $row['REJECT_QNTY'];			

			if($row['PROD_RESO_ALLO']==1)
			{
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row['SEWING_LINE']]);
				$line_name='';

				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].', ';
				}

				$line_name=chop($line_name,' , ');

				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['LOCATION']=$row['LOCATION'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['FLOOR_ID']=$row['FLOOR_ID'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PROD_RESO_ALLO']=$row['PROD_RESO_ALLO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PRODUCTION_DATE']=$row['PRODUCTION_DATE'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SEWING_LINE']=$row['SEWING_LINE'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO']=$row['JOB_NO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'].=$row['ITEM_NUMBER_ID'].",";
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SMV_PCS_SET']=$row['SMV_PCS_SET'].",";
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_BREAK_DOWN_ID']=$row[csf('PO_BREAK_DOWN_ID')]->load();
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['GROUPING']=$row['GROUPING'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['FILE_NO']=$row['FILE_NO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_NUMBER']=$row[csf('PO_NUMBER')]->load();
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SUPERVISOR']=$row['SUPERVISOR'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['GOOD_QNTY']+=$row['GOOD_QNTY'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ALTER_QNTY']+=$row['ALTER_QNTY'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SPOT_QNTY']+=$row['SPOT_QNTY'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['REJECT_QNTY']+=$row['REJECT_QNTY'];
				for($h=$hour; $h<=$last_hour; $h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					$prod_hour="PROD_HOUR".substr($bg_hour,0,2);
					$alter_hour="ALTER_HOUR".substr($bg_hour,0,2);
					$spot_hour="SPOT_HOUR".substr($bg_hour,0,2);
					$reject_hour="REJECT_HOUR".substr($bg_hour,0,2);
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$prod_hour"]+=$row["$prod_hour"];
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$alter_hour"]+=$row["$alter_hour"];
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$spot_hour"]+=$row["$spot_hour"];
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$reject_hour"]+=$row["$reject_hour"];
				}
			}
			else
			{
				$line_name=$lineArr[$row['SEWING_LINE']];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['LOCATION']=$row['LOCATION'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['FLOOR_ID']=$row['FLOOR_ID'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PROD_RESO_ALLO']=$row['PROD_RESO_ALLO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PRODUCTION_DATE']=$row['PRODUCTION_DATE'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SEWING_LINE']=$row['SEWING_LINE'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO']=$row['JOB_NO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'].=$row['ITEM_NUMBER_ID'].",";
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SMV_PCS_SET']=$row['SMV_PCS_SET'].",";
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_BREAK_DOWN_ID']=$row[csf('PO_BREAK_DOWN_ID')]->load();
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['GROUPING']=$row['GROUPING'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['FILE_NO']=$row['FILE_NO'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_NUMBER']=$row[csf('PO_NUMBER')]->load();
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SUPERVISOR']=$row['SUPERVISOR'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['GOOD_QNTY']+=$row['GOOD_QNTY'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ALTER_QNTY']+=$row['ALTER_QNTY'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SPOT_QNTY']+=$row['SPOT_QNTY'];
				$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['REJECT_QNTY']+=$row['REJECT_QNTY'];
				for($h=$hour; $h<=$last_hour; $h++)
				{
					$bg=$start_hour_arr[$h];
					$bg_hour=$start_hour_arr[$h];
					$prod_hour="PROD_HOUR".substr($bg_hour,0,2);
					$alter_hour="ALTER_HOUR".substr($bg_hour,0,2);
					$spot_hour="SPOT_HOUR".substr($bg_hour,0,2);
					$reject_hour="REJECT_HOUR".substr($bg_hour,0,2);
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$PROD_HOUR"]+=$row["$prod_hour"];
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$alter_hour"]+=$row["$alter_hour"];
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$spot_hour"]+=$row["$spot_hour"];
					$production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$reject_hour"]+=$row["$reject_hour"];
					
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
		// ======================= FOR SEWING DATA(TODAY,TOTAL) =============================
		if($db_type==0)
		{
			$prod_qnty_data = "SELECT a.FLOOR_ID, a.LOCATION, a.PROD_RESO_ALLO, a.SEWING_LINE, a.PO_BREAK_DOWN_ID, c.JOB_NO,a.ITEM_NUMBER_ID, 
			sum(case when a.production_type=4 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_INPUT, 
			sum(case when a.production_type=5 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_OUTPUT, 
			sum(case when a.production_type=4 and a.production_date='$txt_date' then a.production_quantity else 0 END) as TODAY_SEWING_INPUT 
			FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
			WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.po_break_down_id in($poIds)
			GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id,c.job_no,a.item_number_id";

		}
		else
		{
			$prod_qnty_data = "SELECT a.FLOOR_ID, a.LOCATION, a.PROD_RESO_ALLO, a.SEWING_LINE, a.PO_BREAK_DOWN_ID, c.JOB_NO,a.ITEM_NUMBER_ID, 
			sum(case when a.production_type=4 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_INPUT, 
			sum(case when a.production_type=5 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_OUTPUT, 
			sum(case when a.production_type=4 and a.production_date='$txt_date' then a.production_quantity else 0 END) as TODAY_SEWING_INPUT 
			FROM pro_garments_production_mst a, wo_po_details_master c, wo_po_break_down d 
			WHERE a.po_break_down_id=d.id and d.job_no_mst=c.job_no and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type in(4,5) and a.po_break_down_id in($poIds) $company_cond $floor_cond $line_cond
			GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id,c.job_no,a.item_number_id";
		}
		//echo $prod_qnty_data;
		$prod_qnty_data_arr = array();
		$prod_qnty_data_res = sql_select($prod_qnty_data);
		foreach($prod_qnty_data_res as $row)
		{	
			if($row['PROD_RESO_ALLO']==1)
			{			
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row['SEWING_LINE']]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}

				$line_name=chop($line_name," , ");
				$prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_INPUT']+=$row['TOTAL_SEWING_INPUT'];
				$prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_OUTPUT']+=$row['TOTAL_SEWING_OUTPUT'];
				$prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TODAY_SEWING_INPUT']+=$row['TODAY_SEWING_INPUT'];
			}
			else
			{
				$line_name=$lineArr[$row['SEWING_LINE']];
				$prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_INPUT']+=$row['TOTAL_SEWING_INPUT'];
				$prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_OUTPUT']+=$row['TOTAL_SEWING_OUTPUT'];
				$prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TODAY_SEWING_INPUT']+=$row['TODAY_SEWING_INPUT'];
			}
		}
		
		$fr_data_arr=array();
		$fr_sql="select ID, FRDATE, LINE, STYLE, DESCRIPTION, PRODUCT_TYPE, ORDER_NO, coLor, PLAN_QTY from fr_import where frdate='$txt_date'";
		//echo $fr_sql; die;
		$fr_sql_res = sql_select($fr_sql);
		foreach($fr_sql_res as $row)
		{
			$ex_job=explode("::",$row['STYLE']);
			$fr_data_arr[$row['LINE']][$ex_job[0]][$row['ORDER_NO']]['ISFR']=$row['COLOR'];
		}
		unset($fr_sql_res);
		?>                  
		<style type="text/css">
			.wrd_brk{
				vertical-align:middle;
				word-break:break-all;				
			}
			.center{text-align: center;}
			.left{text-align: left;}
			.right{text-align: right;}
		</style>
        <div width="100%">
            <table width="100%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                
                <?   //table header calculation
                $totalGoodQnt=$totalAlterQnt=$totalSpotQnt=$totalRejectQnt=0; 
                $total_hterget = 0; //H terget initial
				//print_r($production_data);die;
				$prod_06=$prod_07=$prod_08=$prod_09=$prod_10=$prod_11=$prod_12=$prod_13=$prod_14=0;
				$prod_15=$prod_16=$prod_17=$prod_18=$prod_19=$prod_20=$prod_21=$prod_22=$prod_23=0;
                foreach($production_data as $flowre_id=>$value)
                {
					ksort($value);
					foreach($value as $line_name=>$gmts_val)
					{
						foreach($gmts_val as $job_id=>$val)
						{
							foreach($val as $gmts_id => $row)
							{
								$total_hterget += $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TARGET_PER_HOUR'];  //h terget calculation
								$man_power = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['MAN_POWER']; 
								$hourly_capacity_qty = $operator*60/$total_smv;
								$active_machine_line = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['ACTIVE_MACHINE'];

								$pre_result_capty_utl = ($active_machine_line*60)/$total_smv;
								$hourly_target_efficiency = ($hourly_target/$hourly_capacity_qty)*100;

								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="PROD_HOUR".substr($start_hour_arr[$k],0,2)."";
									$alter_hour="ALTER_HOUR".substr($start_hour_arr[$k],0,2)."";
									$spot_hour="SPOT_HOUR".substr($start_hour_arr[$k],0,2)."";
									$reject_hour="REJECT_HOUR".substr($start_hour_arr[$k],0,2)."";
									$totalGoodQnt += $row[$prod_hour];
									$totalAlterQnt += $row[$alter_hour];
									$totalSpotQnt += $row[$spot_hour];
									$totalRejectQnt +=$row[$reject_hour]; 
								}

								$prod_06 += $row["PROD_HOUR06"];
								$prod_07 += $row["PROD_HOUR07"];
								$prod_08 += $row["PROD_HOUR08"];
								$prod_09 += $row["PROD_HOUR09"];
								$prod_10 += $row["PROD_HOUR10"];
								$prod_11 += $row["PROD_HOUR11"];
								$prod_12 += $row["PROD_HOUR12"];
								$prod_13 += $row["PROD_HOUR13"];
								$prod_14 += $row["PROD_HOUR14"];
								$prod_15 += $row["PROD_HOUR15"];
								$prod_16 += $row["PROD_HOUR16"];
								$prod_17 += $row["PROD_HOUR17"];
								$prod_18 += $row["PROD_HOUR18"];
								$prod_19 += $row["PROD_HOUR19"];
								$prod_20 += $row["PROD_HOUR20"];
								$prod_21 += $row["PROD_HOUR21"];
								$prod_22 += $row["PROD_HOUR22"];
								$prod_23 += $row["PROD_HOUR23"];
							}
						}
					}
				}				
                ?>
                <thead> 	 	 	 	 	 	
                    <tr>
                        <th rowspan="2" width="4%" class="wrd_brk center">Line</th>
                        <th rowspan="2" width="13%" class="wrd_brk center">Order Description</th>
                        <th width="3%" class="wrd_brk center">WIP</th>
                        <th width="3.5%" class="wrd_brk center">H.Target</th>
                        <th width="3%" class="wrd_brk center">Optr</th>
                        <th rowspan="2" width="2.5%" class="wrd_brk center">SMV</th>
                        <?
                        for($k=$hour; $k<=$last_hour; $k++)
                        {
                        	$cur_hour=substr($start_hour_arr[$k],0,2);
                        	?>
                          	<th width="3%" class="wrd_brk center" style="<? if (date('H') == $cur_hour) { ?> background-color: #F00; background-image: none; color: #FFF;<? } ?>"><? echo substr($start_hour_arr[$k],0,5); ?>                        		
                          	</th>
                        <?	
                        }
                        ?>
                        <th width="4%" class="wrd_brk center">Total QC</th>
						<th width="4%" class="wrd_brk center">Reject</th>
						<th rowspan="2" width="3%" class="wrd_brk center">Day Target</th>
						<th rowspan="2" width="3%" class="wrd_brk center">Current Achv %</th>
						<th rowspan="2" width="" class="wrd_brk center">Capty Utl %</th>
                    </tr>
                    <tr>
                    	<th width="3%" class="wrd_brk center">Input</th>
                        <th width="3.5%" class="wrd_brk center">Eff%</th>
                        <th width="3%" class="wrd_brk center">Hlpr</th>
                        <?
                        $percent_cal_arr=array();
                        for($k=$hour; $k<=$last_hour; $k++)
						{
							$cur_hour=substr($start_hour_arr[$k],0,2);
							$cur_prod = 'prod_'.$cur_hour;
							$cur_percent_cal = 'percent_cal_'.$cur_hour;
							?>
							<th width="3%" class="wrd_brk center" title="Total Current Hour Production / (Total Current Hour Production*100/Total H Target)">
                        	<?
	                            $$cur_percent_cal = $$cur_prod*100/$total_hterget;
	                            array_push($percent_cal_arr, $$cur_percent_cal);                   	     
	                        	if ($$cur_prod != 0){
	                                echo $$cur_prod.'/'.(fn_number_format($$cur_percent_cal)).'%';
	                        	} else {
	                        	   echo 0;
	                        	}
                        	?>
                        	</th>
							<?
						}
						?>	
                        
                        <th width="4%" class="wrd_brk center" title="Total Production Qnty / Average Percentage">
                        	<?
	                        	$percent_arr = array($percent_cal_06,$percent_cal_07,$percent_cal_08, $percent_cal_09, $percent_cal_10, $percent_cal_11, $percent_cal_12, $percent_cal_13, $percent_cal_14, $percent_cal_15, $percent_cal_16, $percent_cal_17, $percent_cal_18, $percent_cal_19, $percent_cal_20, $percent_cal_21, $percent_cal_22, $percent_cal_23);
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
	                            	echo $totalGoodQnt.'/'.fn_number_format($percent_avg).'%';
	                            } else {
	                            	echo 0;
	                            }
		                                               
	                        ?>
                        </th>
                        <th width="4%" class="wrd_brk center">Alter/Spot</th>
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
								$totalGood_qty += $row['GOOD_QNTY'];
								$totalAlter_qty += $row['ALTER_QNTY'];
								$totalSpot_qty += $row['SPOT_QNTY'];
								$totalReject_qty += $row['REJECT_QNTY'];
								$today_input = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TODAY_SEWING_INPUT'];
								$total_input = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TOTAL_SEWING_INPUT'];
								$total_output = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TOTAL_SEWING_OUTPUT'];
								//echo $total_input.'**'.$total_output.'sys';
								//echo $flowre_id.'**'.$line_name.'**'.$job_id.'**'.$gmts_id.'sys';
								$order_number=implode(',',array_unique(explode(",",$row['PO_NUMBER'])));
								$grouping=implode(',',array_unique(explode(",",$row['GROUPING'])));
								$file_no=implode(',',array_unique(explode(",",$row['FILE_NO'])));
								
								$is_fr=$fr_data_arr[$line_name][$row['JOB_NO']][$order_number]['ISFR'];
								$frline_tdcolor="";
								
								if($is_fr=="") $frline_tdcolor="#F00";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
									<td width="4%" bgcolor="<? echo $frline_tdcolor; ?>" style="vertical-align:middle; word-break:break-all; font-size: 14px; font-weight: bold;" align="center"><p><? echo $line_name; ?></p></td>
									<td width="13%" class="wrd_brk center" title='Bname=<? echo $buyer_short_library[$row['BUYER_NAME']];?> Job=<? echo $row['JOB_NO_PREFIX_NUM'];?> Style=<?php echo $row['STYLE_REF_NO'];?> Order=<? echo $order_number;?> Item=<?php echo $garments_item[$gmts_id];?>'><p><? echo $buyer_short_library[$row['BUYER_NAME']].', '.$row['JOB_NO_PREFIX_NUM'].', '.$row['STYLE_REF_NO'].', '.$order_number.', '.$garments_item[$gmts_id]; ?></p></td>

									<td width="3%" class="wrd_brk center" title="<? echo 'Total Input='.$total_input.' and Total Output='.$total_output; ?>"><p>
										<?
										    $wip = ($total_input - $total_output);
										    if ($wip==0 && $today_input==0) {
										    	echo '';
										    } else {
										    	echo $wip."<br>".$today_input;
										    }
									    ?>
									</p></td>

									<td width="3%" class="wrd_brk center"><? echo $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TARGET_PER_HOUR'].'<br/>'.$prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TARGET_EFFICIENCY'];
									 $total_hterget += $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TARGET_PER_HOUR'];  //h terget calculation
									    ?></td>
									
									<td width="3%" class="wrd_brk center"><p>
										<?
										$operator = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['OPERATOR'];
										$helper = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['HELPER'];
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

									<td width="2.5%" class="wrd_brk center"><p>
                                        <? 
                                            $smv_pcs_string=chop($row['SMV_PCS_SET'],",");
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
								
									<?
									$man_power = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['MAN_POWER']; 
									$hourly_capacity_qty = $operator*60/$total_smv;
									$active_machine_line = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['ACTIVE_MACHINE'];

									$pre_result_capty_utl = ($active_machine_line*60)/$total_smv;

									$hourly_target_efficiency = ($hourly_target/$hourly_capacity_qty)*100;

									$left_total_capacity_qty=$left_total_prod_Effic=$left_total_target_blance=$left_total_hourly_target=$left_total_equivalent_basic_qty=0;

									$current_production_hour = 0;  // count current production hour

									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="PROD_HOUR".substr($start_hour_arr[$k],0,2)."";
										$alter_hour="ALTER_HOUR".substr($start_hour_arr[$k],0,2)."";
										$spot_hour="SPOT_HOUR".substr($start_hour_arr[$k],0,2)."";
										$reject_hour="REJECT_HOUR".substr($start_hour_arr[$k],0,2)."";
										$totalGoodQnt += $row[$prod_hour];
										$totalAlterQnt += $row[$alter_hour];
										$totalSpotQnt += $row[$spot_hour];
										$totalRejectQnt +=$row[$reject_hour];
										$qc_pass = $row[$prod_hour];

                                        if ($qc_pass != 0){
                                        	$current_production_hour++;
                                        }
										?>

										<td width="3%" class="wrd_brk center">
											<?  
											    if ($row[$prod_hour] != '0')
											        echo $row[$prod_hour];
											    else echo ''; 
											?>
										</td>
										<?										
									}
                                   
									?>
	 	                            <td width="4%" class="wrd_brk center"><? echo $row['GOOD_QNTY']; ?></td>
									
									<td width="4%" class="wrd_brk center">
								    	<? 
                                            if ($row['REJECT_QNTY'] == '0' && $row['ALTER_QNTY'] == '0' && $row['SPOT_QNTY'] == '0')
								    	        echo '';
								    	    else
								    	        echo $row['REJECT_QNTY'].'<br/>'.$row['ALTER_QNTY'].'/'.$row['SPOT_QNTY'];    
								    	?>
									</td>


									<td width="3%" class="wrd_brk center">
										<? echo $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TPD']; ?>
									</td>
									<td width="3%" class="wrd_brk center" title="(Total QC + Reject) / (Target Per Hour * Working Hour * 100)">
										<? $line_achive=($row['GOOD_QNTY']+$row['REJECT_QNTY'])/$prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TPD']*100;
										echo fn_number_format($line_achive).'%'; ?>
									</td>
									<td width="" class="wrd_brk center" title="Total QC*100 / (((Active Machine Line*60)/Total SMV))*Current Production Hour)">
										<?
										    $mid_result_capty_utl = $pre_result_capty_utl*$current_production_hour;
										    $capty_utl = $row['GOOD_QNTY']*100/$mid_result_capty_utl;
										    echo fn_number_format($capty_utl).'%';
										?>	
									</td>					
								<?
								$i++;								
							}
						}	
					}
            	}
                ?>
                </tbody>
            </table>
        </div>
    </div><!-- end main div -->
    <?
	exit();
}

?>