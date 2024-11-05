<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

/*require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.others.php');
*/

require_once('../../../../includes/common.php');
/*require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');
require_once('../../../../includes/class3/class.conversions.php');
require_once('../../../../includes/class3/class.emblishments.php');
require_once('../../../../includes/class3/class.commisions.php');
require_once('../../../../includes/class3/class.commercials.php');
require_once('../../../../includes/class3/class.others.php');
require_once('../../../../includes/class3/class.trims.php');
require_once('../../../../includes/class3/class.fabrics.php');
require_once('../../../../includes/class3/class.washes.php');
*/
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$date=date('Y-m-d');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );  
	exit();  	 
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_name=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$cbo_year_name=str_replace("'","",$cbo_year_start);
	$cbo_month=str_replace("'","",$cbo_month_start);
	$cbo_end_year_name=str_replace("'","",$cbo_year_end);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_type=str_replace("'","",$cbo_type);
	$txt_order_no=str_replace("'","",$txt_order_no);
	//echo $pre_cont_v1;
	//echo $pre_cont_v2;
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}

	$tot_month = datediff( 'm', $s_date,$e_date);
	
	
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($s_date,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
	}
	//print_r($tot_month);
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_id='$location_id'";
	
	if($cbo_type==1){
	
		$dtls_arr=array();
		$sql_daydtls=sql_select("select month_id,mst_id, sum(no_of_line) as no_of_line from lib_capacity_calc_dtls where  day_status=1 group by month_id,mst_id");
		foreach( $sql_daydtls as $rowd)
		{
			if($rowd[csf("no_of_line")]>0)
			{
			$dtls_arr[$rowd[csf("mst_id")]][$rowd[csf("month_id")]]=$rowd[csf("no_of_line")];
			}
		}
		unset($sql_daydtls);
				
 		// $sql_data_smv=sql_select("select a.id,a.comapny_id, a.year, a.avg_machine_line, a.basic_smv, a.effi_percent, c.month_id, c.capacity_month_min, c.working_day from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id=$cbo_company_name $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name and c.month_id between $cbo_month and $cbo_month_end");	

//and b.month_id between $cbo_month 
		//   $sql_data_smv=sql_select("SELECT a.id,count(b.id) as  working_day,a.year,b.month_id,b.day_status,b.no_of_line,sum(b.capacity_min) as capacity_month_min, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and a.year between $cbo_year_name and $cbo_end_year_name and b.month_id=$cbo_month_end and a.capacity_source=1 $locatin_cond and b.day_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND b.date_calc between '$s_date' and '$e_date'	group by a.id,b.month_id,a.year,b.day_status,b.no_of_line, a.avg_machine_line, a.basic_smv, a.effi_percent");
		// $sql_data_smv=sql_select("SELECT a.id,count(b.id) as  working_day,a.year,c.month_id,b.day_status,b.no_of_line,sum(b.capacity_min) as capacity_month_min, a.avg_machine_line, a.basic_smv, c.efficiency_per as effi_percent from lib_capacity_calc_mst a,lib_capacity_calc_dtls b,lib_capacity_year_dtls c where a.id=b.mst_id  and a.id=c.mst_id and b.mst_id=c.mst_id and a.comapny_id=$cbo_company_name and a.year between $cbo_year_name and $cbo_end_year_name  and a.capacity_source=1 $locatin_cond and b.day_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0	AND b.date_calc between '$s_date' and '$e_date' group by a.id,c.month_id,a.year,b.day_status,b.no_of_line, a.avg_machine_line, a.basic_smv, c.efficiency_per");
		$sql_data_smv=sql_select("SELECT a.id,count(b.id) as  working_day,a.year,c.month_id,b.day_status,b.no_of_line,sum(b.capacity_min) as capacity_month_min, c.avg_mch_line as avg_machine_line, c.basic_smv, c.efficiency_per as effi_percent from lib_capacity_calc_mst a,lib_capacity_calc_dtls b,lib_capacity_year_dtls c where a.id=b.mst_id  and a.id=c.mst_id and b.mst_id=c.mst_id and a.comapny_id=$cbo_company_name and a.year between $cbo_year_name and $cbo_end_year_name  and a.capacity_source=1 $locatin_cond and b.day_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0	AND b.date_calc between '$s_date' and '$e_date' group by a.id,c.month_id,a.year,b.day_status,b.no_of_line, c.avg_mch_line, c.basic_smv, c.efficiency_per");
		
		$capacity_arr=array();
		foreach( $sql_data_smv as $row)
		{
			$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['clock_hrs']=(($row[csf("capacity_month_min")]/$row[csf("effi_percent")])*100);
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['efficency']=$row[csf("effi_percent")];
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['basic_smv']=$row[csf("basic_smv")];
		
					
				$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['line']=$row[csf("avg_machine_line")];
				$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['working_day']=$row[csf("working_day")];
				$no_of_line=0;
				$no_of_line=$dtls_arr[$row[csf("id")]][$row[csf("month_id")]];///$row[csf("working_day")];
				
				$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_line']=$no_of_line/$row[csf("working_day")];
				
				//$tot_hrs=0;
				//$tot_hrs=$no_of_line*$row[csf("avg_machine_line")]*$financial_ar[$month]*$row[csf("working_day")];
				
				//$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_hrs']=$tot_hrs;
		
		
		
		
		
		}
		//print_r($basic_smv_arr);die;
		unset($sql_data_smv);
		
		$locatin_cond="";
		if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
		
		$sql_con_po="SELECT a.set_smv, a.total_set_qnty, b.id as po_id, b.pub_shipment_date as shipment_date, b.po_total_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst AND a.company_name=$cbo_company_name $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// and b.id=41672
		//echo $sql_con_po;die;
		
		
		
		$knit_cost_arr=array(1,2,3,4);
		$fabric_dyeingCost_arr=array(25,26,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79);
		$aop_cost_arr=array(35,36,37);
		$fab_finish_cost_arr=array(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129);
		$washing_cost_arr=array(64,82,89);
		
		$po_arr=array();
		$sql_data_po=sql_select($sql_con_po);
		foreach( $sql_data_po as $row_po)
		{
			$date_key=date("Y-m",strtotime($row_po[csf("shipment_date")]));
			$year_key=date("Y",strtotime($row_po[csf("shipment_date")]));
			
			$ex_month='';
			$ex_month=explode('-',$date_key);
			$monthId=0;
			
			if($ex_month[1]==10){
				$monthId=$ex_month[1];
			}
			else{
				$monthId=str_replace('0','',$ex_month[1]);
			}
			
			$confirm_qty=0; $projected_qty=0;
			
			$confirm_qty=($row_po[csf("confirm_qty")])*$row_po[csf("set_smv")];
			$projected_qty=($row_po[csf("projected_qty")])*$row_po[csf("set_smv")];
			$po_arr[$date_key]['booked_sah_con']+=$confirm_qty;
			$po_arr[$date_key]['booked_sah_proj']+=$projected_qty;
			$po_arr[$date_key]['booked_eqv_qty']+=($confirm_qty+$projected_qty)/$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
			$po_arr[$date_key]['confirm_value']+=$row_po[csf("confirm_value")];
			$po_arr[$date_key]['projected_value']+=$row_po[csf("projected_value")];
			$po_arr[$date_key]['confirm_qty']+=$row_po[csf("confirm_qty")]*$row_po[csf("total_set_qnty")];
			$po_arr[$date_key]['projected_qty']+=$row_po[csf("projected_qty")]*$row_po[csf("total_set_qnty")];
			
		}
		
		foreach(explode(',',$pre_cont_v1) as $value_sting){
			list($date_key,$cm_value)=explode('**',$value_sting);
				$po_arr[$date_key]['cm_value']+=$cm_value;	
		}
		
		
		foreach(explode(',',$pre_cont_v2) as $value_sting){
			list($date_key,$cm_value)=explode('**',$value_sting);
				$po_arr[$date_key]['cm_value']+=$cm_value;	
		}
		
		
		unset($sql_data_po);
		unset($other_costing_arr);
		$exchange_rate_sql=sql_select("select conversion_rate,con_date from currency_conversion_rate where status_active=1 and is_deleted=0 and currency=2 order by con_date");
		foreach($exchange_rate_sql as $row){
			$date_key=date("Y-m",strtotime($row[csf("con_date")]));
			$exchange_rate_arr[$date_key]=$row[csf("conversion_rate")];
		}
		//var_dump($exchange_rate_arr);
	
		$financial_arr=array();
		$cpAvgRateArray=sql_select( "select applying_period_to_date, cost_per_minute,monthly_cm_expense, working_hour from  lib_standard_cm_entry where company_id='$cbo_company_name' order by applying_period_to_date" );
		foreach( $cpAvgRateArray as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("applying_period_to_date")]));
			if($exchange_rate_arr[$date_key]==""){
				for($i=1;$i<=12;$i++){
				$prv_date=date("Y-m",strtotime($date_key." -$i month"));
					if($exchange_rate_arr[$prv_date]){
						$exchange_rate_arr[$date_key]=$exchange_rate_arr[$prv_date];
						break;
					}
				}
			}
			
			$monthCapAvgArr[$date_key] = $row[csf("monthly_cm_expense")]/$exchange_rate_arr[$date_key];
			$financial_ar[$date_key]=$row[csf("working_hour")];
		}
		unset($cpAvgRateArray);
		
		 //echo "<pre>";print_r($month_arr); echo "</pre>";
		
 		ob_start();	
		$month_count=count($month_arr);
		$tbl_width=130+($month_count*100);
		$bgcolor1="#FFFFFF";
		$bgcolor2="#E9F3FF";
		?>
        <div style="width:<? echo $tbl_width+25; ?>px; margin:0 auto">
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $month_count+1; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $month_count+1; ?>" align="center" style="border:none; font-size:14px;">
						   <b><? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></b>                               
						</td>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tbl_width+20; ?>px" id="scroll_body">
			<table cellspacing="0" width="<? echo $tbl_width; ?>" border="1" rules="all" class="rpt_table" id="scroll_body">
				<thead>
					<tr>
						<th width="130">Details</th>
						<?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
						?>
							<th width="100"><? echo $months[$monthId].', '.$ex_month[0]; ?></th>
						<? } ?>
					</tr>
				</thead>
				<tbody>
					<tr bgcolor="#FFCCFF">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Capacity</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Clock Hrs</strong></td>
						<?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$clock_hrs=0;
								//$clock_hrs=$capacity_arr[$ex_month[0]][$monthId]['clock_hrs']/60;
								
								
								$clock_hrs=$capacity_arr[$ex_month[0]][$monthId]['tot_line']*$capacity_arr[$ex_month[0]][$monthId]['line']*$financial_ar[$month_id]*$capacity_arr[$ex_month[0]][$monthId]['working_day'];
								$msgTtl=$capacity_arr[$ex_month[0]][$monthId]['tot_line'].'*'.$capacity_arr[$ex_month[0]][$monthId]['line'].'*'.$financial_ar[$month_id].'*'.$capacity_arr[$ex_month[0]][$monthId]['working_day'];
								
								
						?>
							<td width="100" title="<?=$msgTtl.'='.$ex_month[0].'='.$monthId;?>" align="right"><a href='#report_details' onClick="fnc_details_popup('<? echo $month_id; ?>','<? echo $cbo_company_name; ?>','<? echo $location_id; ?>','<? echo "0"; ?>','<? echo "clock_hrs_popup"; ?>');"><? echo $clock_hrs//number_format($clock_hrs,0,'.',','); ?></a></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Efficency (%)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$efficency=0;
								$efficency=$capacity_arr[$ex_month[0]][$monthId]['efficency'];
						?>
							<td width="100" align="right"><? echo number_format($efficency,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>SAH</strong></td>
						 <?
							$sah_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$sah=0;
								//$sah=(($capacity_arr[$ex_month[0]][$monthId]['clock_hrs']/60)*$capacity_arr[$ex_month[0]][$monthId]['efficency'])/100;
								$sah=(($clock_hrs_for_sah/60)*$capacity_arr[$ex_month[0]][$monthId]['efficency'])/100;
								$sah_arr[$month_id]=$sah;
						?>
							<td width="100" align="right" title="(Clock Hr(<?=$clock_hrs_for_sah;?>)/60)*Efficency(<?=$capacity_arr[$ex_month[0]][$monthId]['efficency'];?>)/100"><? echo number_format($sah,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Eqv. Basic Qty. (Pcs)</strong></td>
						 <?
							$basic_smv_arr=array(); $cap_equ_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$basic_smv=0; 
								$basic_smv=($sah_arr[$month_id]*60)/$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
								$basic_smv_arr[$month_id]=$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
								$cap_equ_arr[$month_id]=$basic_smv;
						?>
							<td width="100" align="right"><? echo number_format($basic_smv,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FFCCAA">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Booked</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Booked SAH- Confirmed</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$booked_sah_con=0;
								$booked_sah_con=$po_arr[$month_id]['booked_sah_con']/60;
						?>
							<td width="100" align="right"><a href='#report_details' onClick="fnc_details_popup('<? echo $month_id; ?>','<? echo $cbo_company_name; ?>','<? echo $location_id; ?>','<? echo "1"; ?>','<? echo "order_popup"; ?>');"><? echo number_format($booked_sah_con,0,'.',','); ?></a></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Booked SAH-Projected</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$booked_sah_proj=0;
								$booked_sah_proj=$po_arr[$month_id]['booked_sah_proj']/60;
						?>
							<td width="100" align="right"><a href='#report_details' onClick="fnc_details_popup('<? echo $month_id; ?>','<? echo $cbo_company_name; ?>','<? echo $location_id; ?>','<? echo "2"; ?>','<? echo "order_popup"; ?>');"><? echo number_format($booked_sah_proj,0,'.',','); ?></a></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Total Booked- SAH</strong></td>
						 <?
							$booked_sah_arr=array();
							foreach($month_arr as $month_id)
							{
								$tot_booked_sah=0;
								$tot_booked_sah=($po_arr[$month_id]['booked_sah_con']/60)+($po_arr[$month_id]['booked_sah_proj']/60);
								$booked_sah_arr[$month_id]=$tot_booked_sah;
						?>
							<td width="100" align="right"><? echo number_format($tot_booked_sah,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Eqv. Basic Qty. (Pcs)</strong></td>
						 <? $book_equ_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$eqv_basic_qty=0;
								$eqv_basic_qty=$po_arr[$month_id]['booked_eqv_qty'];
								$book_equ_arr[$month_id]=$eqv_basic_qty;
						?>
							<td width="100" align="right">
							<? 
							echo number_format($eqv_basic_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Booked % -Confirm</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$booked_per_con=0;
								$booked_per_con=(($po_arr[$month_id]['booked_sah_con']/60)/$sah_arr[$month_id])*100;
								$booked_perCon_arr[$month_id]=number_format($booked_per_con,0,'.',',');
						?>
							<td width="100" align="right">
							<? 
							echo number_format($booked_per_con,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Booked % -Projection</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$booked_per_proj=0;
								$booked_per_proj=(($po_arr[$month_id]['booked_sah_proj']/60)/$sah_arr[$month_id])*100;
								$booked_perProj_arr[$month_id]=number_format($booked_per_proj,0,'.',',');
						?>
							<td width="100" align="right">
							<? 
							echo number_format($booked_per_proj,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Booked (%)</strong></td>
						 <? $book_per_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$booked_per=0;
								$booked_per=$booked_perCon_arr[$month_id]+$booked_perProj_arr[$month_id];
								$book_per_arr[$month_id]=number_format($booked_per,0,'.',',');
								//$booked_per=$booked_per*100;
						?>
							<td width="100" align="right"><? echo number_format($booked_per,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#0099FF">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Variance</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Over/Under Booked SAH</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$over_under_sah=0;
								$over_under_sah=$booked_sah_arr[$month_id]-$sah_arr[$month_id];
						?>
							<td width="100" align="right"><? echo $over_under_sah<0 ?   "( ".number_format( abs($over_under_sah),0,'.',',')." )" :  number_format($over_under_sah,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Over/Under Booked (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$over_under_pcs=0;
								$over_under_pcs=$book_equ_arr[$month_id]-$cap_equ_arr[$month_id];
								//($booked_sah_arr[$month_id]-$sah_arr[$month_id])/$basic_smv_arr[$month_id];
						?>
							<td width="100" align="right"><? echo $over_under_pcs<0 ?   "( ".number_format( abs($over_under_pcs),0,'.',',')." )" :  number_format($over_under_pcs,0,'.',','); //echo number_format($over_under_pcs,2,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Over/Under Booked (%)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$over_under_per=0;
								$over_under_per=$book_per_arr[$month_id]-100;//($booked_sah_arr[$month_id]-$sah_arr[$month_id])/$sah_arr[$month_id]*100;
						?>
							<td width="100" align="right"><?  echo $over_under_per<0 ?   "( ".number_format( abs($over_under_per),0,'.',',')." )" :  number_format($over_under_per,0,'.',',');//echo number_format($over_under_per,2,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#66CC66">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Others Info</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Booked CM</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$cm_value=0;
								$cm_value=$po_arr[$month_id]['cm_value'];
								
								//print_r($po_arr)
								
						?>
							<td width="100" align="right"><? echo number_format($cm_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FFFF66">
						<td><strong>CPM</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$cpm=0;
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								//$cpm=(($monthCapAvgArr[$month_id]*1)/$capacity_arr[$ex_month[0]][$monthId]['efficency'])*100;
								$cpm=($monthCapAvgArr[$month_id]*1)/($booked_sah_arr[$month_id]*60);
						?>
							<td width="100" align="right"><? echo number_format($cpm,3,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FFCC33">
						<td><strong>EPM</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								if($ex_month[1]==10)
								{
									$monthId=$ex_month[1];
								}
								else
								{
									$monthId=str_replace('0','',$ex_month[1]);
								}
								$epm=0;
								
								$epm=$po_arr[$month_id]['cm_value']/($booked_sah_arr[$month_id]*60);
								
						?>
							<td width="100" align="right"><? echo number_format($epm,3,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FF6600">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Actual Order Info</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Value Confirmed (USD)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$confirm_value=0;
								$confirm_value=$po_arr[$month_id]['confirm_value'];
						?>
							<td width="100" align="right"><? echo number_format($confirm_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Value Projections (USD)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$projected_value=0;
								$projected_value=$po_arr[$month_id]['projected_value'];
						?>
							<td width="100" align="right"><? echo number_format($projected_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Total Value (USD)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$tot_value=0;
								$tot_value=$po_arr[$month_id]['confirm_value']+$po_arr[$month_id]['projected_value'];
						?>
							<td width="100" align="right"><? echo number_format($tot_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Qty Confirmed (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$confirm_qty=0;
								$confirm_qty=$po_arr[$month_id]['confirm_qty'];
						?>
							<td width="100" align="right"><? echo number_format($confirm_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Qty Projections (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$projected_qty=0;
								$projected_qty=$po_arr[$month_id]['projected_qty'];
						?>
							<td width="100" align="right"><? echo number_format($projected_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Total Qty (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$tot_qty=0;
								$tot_qty=$po_arr[$month_id]['confirm_qty']+$po_arr[$month_id]['projected_qty'];
						?>
							<td width="100" align="right"><? echo number_format($tot_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
				</tbody>
			 </table>
			 </div>
		</div>
	<?
	}
	else if($cbo_type==2){
		
		
		foreach(explode(',',$pre_cont_v2) as $value_sting){
			list($po_key,$cm_value)=explode('**',$value_sting);
				$po_wise_cm_value_arr[$po_key]=$cm_value;	
				//$po_arr[$po_key]['cm_value']+=$cm_value;	
		}
		
        $dtls_arr=array();
		$sql_daydtls=sql_select("select month_id,mst_id, sum(no_of_line) as no_of_line from lib_capacity_calc_dtls where  day_status=1 group by month_id,mst_id");
		foreach( $sql_daydtls as $rowd)
		{
			$dtls_arr[$rowd[csf("mst_id")]][$rowd[csf("month_id")]]=$rowd[csf("no_of_line")];
		}
		unset($sql_daydtls);
		
		//$sql_data_smv=sql_select("select a.comapny_id, a.year, a.basic_smv, a.effi_percent, c.month_id, c.capacity_month_min from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id=$cbo_company_name $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name");
 		$sql_data_smv=sql_select("select a.id,a.comapny_id, a.year, a.avg_machine_line, a.basic_smv, a.effi_percent, c.month_id, c.capacity_month_min, c.working_day from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id=$cbo_company_name $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name");



		

		
		$capacity_arr=array();
		foreach( $sql_data_smv as $row)
		{
			$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['clock_hrs']=(($row[csf("capacity_month_min")]/$row[csf("effi_percent")])*100);
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['efficency']=$row[csf("effi_percent")];
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['basic_smv']=$row[csf("basic_smv")];
		
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['line']=$row[csf("avg_machine_line")];
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['working_day']=$row[csf("working_day")];
			$no_of_line=0;
			$no_of_line=$dtls_arr[$row[csf("id")]][$row[csf("month_id")]];///$row[csf("working_day")];
			$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_line']=$no_of_line/$row[csf("working_day")];
		
		}
		//print_r($basic_smv_arr);die;
		unset($sql_data_smv);
		
		//echo $txt_order_no;die;
		
		
		$locatin_cond="";
		if($location_id>0){$locatin_cond=" AND a.location_name='$location_id'";}
		if($txt_order_no!=''){$locatin_cond.=" AND b.po_number='".trim($txt_order_no)."'";}
		
		$dateCon="and ((c.TASK_FINISH_DATE between '$s_date' and '$e_date') or (c.TASK_START_DATE  between '$s_date' and '$e_date' ))";
		
		$sql_con_po="SELECT b.id,c.TASK_START_DATE, c.TASK_FINISH_DATE, ( (c.TASK_FINISH_DATE-c.TASK_START_DATE)+1 ) as PLAN_LEAD_TIME,a.set_smv, a.total_set_qnty, b.id as po_id,B.PO_NUMBER, b.pub_shipment_date as shipment_date, b.po_total_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b,TNA_PROCESS_MST c
		WHERE a.job_no = b.job_no_mst and a.job_no=c.JOB_NO and b.id=c.PO_NUMBER_ID and c.TASK_NUMBER=86 and c.TASK_TYPE=1 AND a.company_name=$cbo_company_name $locatin_cond $dateCon and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// and b.id=41672
		// echo $sql_con_po; die;
		
		
		
		
		$knit_cost_arr=array(1,2,3,4);
		$fabric_dyeingCost_arr=array(25,26,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79);
		$aop_cost_arr=array(35,36,37);
		$fab_finish_cost_arr=array(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129);
		$washing_cost_arr=array(64,82,89);
		
		$po_arr=array();
		$sql_data_po=sql_select($sql_con_po);
		foreach( $sql_data_po as $row_po)
		{
			
			//$lead_time = datediff( 'd', $row_po[TASK_START_DATE],$row_po[TASK_FINISH_DATE]);
			
			
			$shipment_date=strtotime($row_po[csf("shipment_date")]);
			$TASK_START_DATE=strtotime($row_po[TASK_START_DATE]);
			$TASK_FINISH_DATE=strtotime($row_po[TASK_FINISH_DATE]);
			//$first_ship_date = strtotime('first day of this month', strtotime($row_po[csf("shipment_date")]));
			//$last_ship_date = strtotime('last day of this month', strtotime($row_po[csf("shipment_date")]));
			
			
			foreach($month_arr as $ym){
				list($y,$m)=explode('-',$ym);
				$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $m, $y);
				
				$first_date = strtotime("01-$m-$y");
				$last_date = strtotime("$daysinmonth-$m-$y");
				
				$month_lead_time=0;
				if($TASK_START_DATE >= $first_date && $TASK_FINISH_DATE <= $last_date ){
					$month_lead_time = datediff( 'd', $row_po[TASK_START_DATE],$row_po[TASK_FINISH_DATE]);
				}
				else if($TASK_START_DATE >= $first_date && $TASK_START_DATE <= $last_date  && $TASK_FINISH_DATE >= $last_date  ){
					$month_lead_time = datediff( 'd', $row_po[TASK_START_DATE],date('d-m-Y',$last_date));
					// if($month_lead_time==2){echo $row_po[ID].'='.$row_po[TASK_FINISH_DATE];die;}
				}
				else if($TASK_START_DATE < $first_date &&  $TASK_FINISH_DATE <= $last_date &&  $TASK_FINISH_DATE>=$first_date){
					$month_lead_time = datediff( 'd', date('d-m-Y',$first_date),$row_po[TASK_FINISH_DATE]);
				}
				
				else if($TASK_START_DATE < $first_date && $TASK_FINISH_DATE >= $first_date && $TASK_FINISH_DATE >= $last_date ){
					$month_lead_time = datediff( 'd', date('d-m-Y',$first_date),date('d-m-Y',$last_date));
				}

		
				
/*				 if($row_po[ID]==56747){
				 echo $TASK_START_DATE .'>='. $first_date .'&&'. $TASK_START_DATE .'<='. $last_date  .'&&'. $TASK_FINISH_DATE .'>='. $last_date;
				 } 
*/			 
				
				
				$date_key=date("Y-m",$first_date); 
				$year_key=date("Y",$first_date);
				
				$ex_month='';
				$ex_month=explode('-',$date_key);
				$monthId=0;
				$monthId=$ex_month[1]*1;
				
				
				$confirm_qty=0; $projected_qty=0;
				
				
				$confirm_rate=($row_po[csf("confirm_value")]/$row_po[csf("confirm_qty")])*1;
				$projected_rate=$row_po[csf("projected_value")]/$row_po[csf("projected_qty")];
				
				$confirm_rate=(is_nan($confirm_rate))?0:$confirm_rate;
				$projected_rate=(is_nan($projected_rate))?0:$projected_rate;
				
				
				
				$tna_confirm_qty=($row_po[csf("confirm_qty")]/$row_po[PLAN_LEAD_TIME])*$month_lead_time;
				$tna_projected_qty=($row_po[csf("projected_qty")]/$row_po[PLAN_LEAD_TIME])*$month_lead_time;
				
				  
				  //echo $row_po[csf("confirm_qty")].'/'.($row_po[PLAN_LEAD_TIME]+1).')*'.$month_lead_time.'------';
				
				$po_arr[$date_key]['booked_sah_con']+=$tna_confirm_qty*$row_po[csf("set_smv")];
				$po_arr[$date_key]['booked_sah_proj']+=$tna_projected_qty*$row_po[csf("set_smv")];
				$po_arr[$date_key]['booked_eqv_qty']+=($tna_confirm_qty+$tna_projected_qty)/$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
				$po_arr[$date_key]['confirm_value']+=($confirm_rate*$tna_confirm_qty);
				$po_arr[$date_key]['projected_value']+=($projected_rate*$tna_projected_qty);
				
				
				$confirm_qty_pcs=(($row_po[csf("confirm_qty")]*$row_po[csf("total_set_qnty")])/$row_po[PLAN_LEAD_TIME])*$month_lead_time;
				$projected_qty_pcs=(($row_po[csf("projected_qty")]*$row_po[csf("total_set_qnty")])/$row_po[PLAN_LEAD_TIME])*$month_lead_time;
				$po_arr[$date_key]['confirm_qty']+=$confirm_qty_pcs;
				$po_arr[$date_key]['projected_qty']+=$projected_qty_pcs;
			
				
				$poQty=($row_po[csf("confirm_qty")]+$row_po[csf("projected_qty")])*$row_po[csf("total_set_qnty")];
				$po_arr[$date_key]['cm_value']+=($po_wise_cm_value_arr[$row_po[csf("po_id")]]/$poQty)*($confirm_qty_pcs+$projected_qty_pcs);
				
				
				/*$po_arr222[$row_po[PO_NUMBER]]['MONTH_LEAD_TIME']+=$month_lead_time;
				$po_arr222[$row_po[PO_NUMBER]]['PLAN_LEAD_TIME']+=$row_po[PLAN_LEAD_TIME];
				$po_arr222[$row_po[PO_NUMBER]]['confirm_qty']+=($confirm_qty_pcs*1);
				$po_arr222[$row_po[PO_NUMBER]]['projected_qty']+=($projected_qty_pcs*1);*/
			
			
			}//month loof
			
		}
		
		
		
		
		
		
		//echo "<pre>";print_r($po_arr222); echo "</pre>";die;
		
		
		/*		
		foreach(explode(',',$pre_cont_v1) as $value_sting){
			list($date_key,$cm_value)=explode('**',$value_sting);
				$po_arr[$date_key]['cm_value']+=$cm_value;	
		}
		
		
		foreach(explode(',',$pre_cont_v2) as $value_sting){
			list($date_key,$cm_value)=explode('**',$value_sting);
				$po_arr[$date_key]['cm_value']+=$cm_value;	
		}
		*/
		
		
		
		
		unset($sql_data_po);
		unset($other_costing_arr);
		$exchange_rate_sql=sql_select("select conversion_rate,con_date from currency_conversion_rate where status_active=1 and is_deleted=0 and currency=2 order by con_date");
		foreach($exchange_rate_sql as $row){
			$date_key=date("Y-m",strtotime($row[csf("con_date")]));
			$exchange_rate_arr[$date_key]=$row[csf("conversion_rate")];
		}
		//var_dump($exchange_rate_arr);
	
		$cpAvgRateArray=sql_select( "select applying_period_to_date, cost_per_minute,monthly_cm_expense,working_hour from  lib_standard_cm_entry where company_id='$cbo_company_name' order by applying_period_to_date" );
		foreach( $cpAvgRateArray as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("applying_period_to_date")]));
			if($exchange_rate_arr[$date_key]==""){
				for($i=1;$i<=12;$i++){
				$prv_date=date("Y-m",strtotime($date_key." -$i month"));
					if($exchange_rate_arr[$prv_date]){
						$exchange_rate_arr[$date_key]=$exchange_rate_arr[$prv_date];
						break;
					}
				}
			}
			
			$monthCapAvgArr[$date_key] = $row[csf("monthly_cm_expense")]/$exchange_rate_arr[$date_key];
			$financial_ar[$date_key]=$row[csf("working_hour")];
		}
		unset($cpAvgRateArray);
		
		

		ob_start();	
		$month_count=count($month_arr);
		$tbl_width=130+($month_count*100);
		$bgcolor1="#FFFFFF";
		$bgcolor2="#E9F3FF";
		?>
		<div style="width:<? echo $tbl_width+25; ?>px; margin:0 auto">
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $month_count+1; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $month_count+1; ?>" align="center" style="border:none; font-size:14px;">
						   <b><? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></b>                               
						</td>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tbl_width+20; ?>px" id="scroll_body">
			<table cellspacing="0" width="<? echo $tbl_width; ?>" border="1" rules="all" class="rpt_table" id="scroll_body">
				<thead>
					<tr>
						<th width="130">Details</th>
						<?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;
								$monthId=$ex_month[1]*1;
								
						?>
							<th width="100"><? echo $months[$monthId].', '.$ex_month[0]; ?></th>
						<? } ?>
					</tr>
				</thead>
				<tbody>
					<tr bgcolor="#FFCCFF">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Capacity</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Clock Hrs</strong></td>
						<?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
								
								$clock_hrs=0;
								//$clock_hrs=$capacity_arr[$ex_month[0]][$monthId]['clock_hrs']/60;
								$clock_hrs=$capacity_arr[$ex_month[0]][$monthId]['tot_line']*$capacity_arr[$ex_month[0]][$monthId]['line']*$financial_ar[$month_id]*$capacity_arr[$ex_month[0]][$monthId]['working_day'];

						?>
							<td width="100" align="right"><a href='#report_details' onClick="fnc_details_popup('<? echo $month_id; ?>','<? echo $cbo_company_name; ?>','<? echo $location_id; ?>','<? echo "0"; ?>','<? echo "clock_hrs_popup"; ?>');"><? echo number_format($clock_hrs,0,'.',','); ?></a></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Efficency (%)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
								
								$efficency=0;
								$efficency=$capacity_arr[$ex_month[0]][$monthId]['efficency'];
						?>
							<td width="100" align="right"><? echo number_format($efficency,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>SAH</strong></td>
						 <?
							$sah_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
								
								$sah=0;
								$sah=(($capacity_arr[$ex_month[0]][$monthId]['clock_hrs']/60)*$capacity_arr[$ex_month[0]][$monthId]['efficency'])/100;
								$sah_arr[$month_id]=$sah;
						?>
							<td width="100" align="right" title="<?
								echo $capacity_arr[$ex_month[0]][$monthId]['clock_hrs'];
							
							?>"><? echo number_format($sah,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Eqv. Basic Qty. (Pcs)</strong></td>
						 <?
							$basic_smv_arr=array(); $cap_equ_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;

								$basic_smv=0; 
								$basic_smv=($sah_arr[$month_id]*60)/$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
								$basic_smv_arr[$month_id]=$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
								$cap_equ_arr[$month_id]=$basic_smv;
						?>
							<td width="100" align="right"><? echo number_format($basic_smv,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FFCCAA">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Booked</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Booked SAH- Confirmed</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$booked_sah_con=0;
								$booked_sah_con=$po_arr[$month_id]['booked_sah_con']/60;
						?>
							<td width="100" align="right">
							<? //echo number_format($booked_sah_con,0,'.',','); ?>
                            
                             <a href='#report_details' onClick="fnc_details_popup('<? echo $month_id; ?>','<? echo $cbo_company_name; ?>','<? echo $location_id; ?>','<? echo "1"; ?>','<? echo "order_popup_tna"; ?>');"><? echo number_format($booked_sah_con,0,'.',','); ?></a>
                            
                            </td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Booked SAH-Projected</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$booked_sah_proj=0;
								$booked_sah_proj=$po_arr[$month_id]['booked_sah_proj']/60;
						?>
							<td width="100" align="right">
							<? //echo number_format($booked_sah_proj,0,'.',','); ?>
                            <a href='#report_details' onClick="fnc_details_popup('<? echo $month_id; ?>','<? echo $cbo_company_name; ?>','<? echo $location_id; ?>','<? echo "2"; ?>','<? echo "order_popup_tna"; ?>');"><? echo number_format($booked_sah_proj,0,'.',','); ?></a>
                            </td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Total Booked- SAH</strong></td>
						 <?
							$booked_sah_arr=array();
							foreach($month_arr as $month_id)
							{
								$tot_booked_sah=0;
								$tot_booked_sah=($po_arr[$month_id]['booked_sah_con']/60)+($po_arr[$month_id]['booked_sah_proj']/60);
								$booked_sah_arr[$month_id]=$tot_booked_sah;
						?>
							<td width="100" align="right"><? echo number_format($tot_booked_sah,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Eqv. Basic Qty. (Pcs)</strong></td>
						 <? $book_equ_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
							
								$eqv_basic_qty=0;
								//$eqv_basic_qty=$po_arr[$month_id]['booked_eqv_qty'];
								$eqv_basic_qty=($booked_sah_arr[$month_id]*60)/$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];

								$book_equ_arr[$month_id]=$eqv_basic_qty;
						?>
							<td width="100" align="right">
							<? 
							echo number_format($eqv_basic_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Booked % -Confirm</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
							
								$booked_per_con=0;
								$booked_per_con=(($po_arr[$month_id]['booked_sah_con']/60)/$sah_arr[$month_id])*100;
								$booked_perCon_arr[$month_id]=number_format($booked_per_con,0,'.',',');
						?>
							<td width="100" align="right">
							<? 
							echo number_format($booked_per_con,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Booked % -Projection</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
								
								$booked_per_proj=0;
								$booked_per_proj=(($po_arr[$month_id]['booked_sah_proj']/60)/$sah_arr[$month_id])*100;
								$booked_perProj_arr[$month_id]=number_format($booked_per_proj,0,'.',',');
						?>
							<td width="100" align="right">
							<? 
							echo number_format($booked_per_proj,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Booked (%)</strong></td>
						 <? $book_per_arr=array();
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
							
								$booked_per=0;
								$booked_per=$booked_perCon_arr[$month_id]+$booked_perProj_arr[$month_id];
								$book_per_arr[$month_id]=number_format($booked_per,0,'.',',');
								//$booked_per=$booked_per*100;
						?>
							<td width="100" align="right"><? echo number_format($booked_per,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#0099FF">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Variance</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Over/Under Booked SAH</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$over_under_sah=0;
								$over_under_sah=$booked_sah_arr[$month_id]-$sah_arr[$month_id];
						?>
							<td width="100" align="right"><? echo $over_under_sah<0 ?   "( ".number_format( abs($over_under_sah),0,'.',',')." )" :  number_format($over_under_sah,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Over/Under Booked (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$over_under_pcs=0;
								$over_under_pcs=$book_equ_arr[$month_id]-$cap_equ_arr[$month_id];
								
						?>
							<td width="100" align="right"><? echo $over_under_pcs<0 ?   "( ".number_format( abs($over_under_pcs),0,'.',',')." )" :  number_format($over_under_pcs,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Over/Under Booked (%)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$over_under_per=0;
								$over_under_per=$book_per_arr[$month_id]-100;
						?>
							<td width="100" align="right"><?  echo $over_under_per<0 ?   "( ".number_format( abs($over_under_per),0,'.',',')." )" :  number_format($over_under_per,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#66CC66">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Others Info</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Booked CM</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
								
								$cm_value=0;
								$cm_value=$po_arr[$month_id]['cm_value'];
						?>
							<td width="100" align="right"><? echo number_format($cm_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FFFF66">
						<td><strong>CPM</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$cpm=0;
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
								$cpm=($monthCapAvgArr[$month_id]*1)/($booked_sah_arr[$month_id]*60);
								
						?>
							<td width="100" align="right"><? echo number_format($cpm,3,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FFCC33">
						<td><strong>EPM</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$ex_month='';
								$ex_month=explode('-',$month_id);
								$monthId=0;$monthId=$ex_month[1]*1;
								$epm=0;
								$epm=$po_arr[$month_id]['cm_value']/($booked_sah_arr[$month_id]*60);
								
						?>
							<td width="100" align="right"><? echo number_format($epm,3,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="#FF6600">
						<td colspan="<? echo $month_count+1; ?>" align="left"><strong>Actual Order Info</strong></td>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Value Confirmed (USD)</strong></td>
						 <?
							
							foreach($month_arr as $month_id)
							{
								$confirm_value=0;
								$confirm_value=$po_arr[$month_id]['confirm_value'];
						?>
							<td width="100" align="right"><? echo number_format($confirm_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Value Projections (USD)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$projected_value=0;
								$projected_value=$po_arr[$month_id]['projected_value'];
						?>
							<td width="100" align="right"><? echo number_format($projected_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Total Value (USD)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$tot_value=0;
								$tot_value=$po_arr[$month_id]['confirm_value']+$po_arr[$month_id]['projected_value'];
						?>
							<td width="100" align="right"><? echo number_format($tot_value,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Qty Confirmed (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$confirm_qty=0;
								$confirm_qty=$po_arr[$month_id]['confirm_qty'];
						?>
							<td width="100" align="right"><? echo number_format($confirm_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor2; ?>">
						<td><strong>Qty Projections (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$projected_qty=0;
								$projected_qty=$po_arr[$month_id]['projected_qty'];
						?>
							<td width="100" align="right"><? echo number_format($projected_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><strong>Total Qty (Pcs)</strong></td>
						 <?
							foreach($month_arr as $month_id)
							{
								$tot_qty=0;
								$tot_qty=$po_arr[$month_id]['confirm_qty']+$po_arr[$month_id]['projected_qty'];
						?>
							<td width="100" align="right"><? echo number_format($tot_qty,0,'.',','); ?></td>
						<? } ?>
					</tr>
				</tbody>
			 </table>
             <b>Note:</b>Total Qty (Pcs) = (Order Qty/Total Plan Lead Time)* Month Plan Lead Time
			 </div>
		</div>
	<?
		
	}//end else
	
	
	
	
	foreach (glob("*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$filename=time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$fdata=ob_get_contents();
	fwrite($create_new_doc,$fdata);
	ob_end_clean();
	echo "$fdata####$filename";
	exit();	
}

if($action=="clock_hrs_popup")
{
	echo load_html_head_contents("Clock Hours Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_month='';
	$ex_month=explode('-',$month);
	$monthId=0;
	
	if($ex_month[1]==10)
		$monthId=$ex_month[1];
	else
		$monthId=str_replace('0','',$ex_month[1]);
	?>
	<fieldset style="width:350px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="350" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="3">Clock Hours Details</th>
                    </tr>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="200">Particulars</th>
                        <th>Value</th>
                    </tr>
				</thead>
                <?
				$financial_arr=array();
				$sql_fina_param=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where company_id='$company_id' and status_active=1 and is_deleted=0 ");
				foreach( $sql_fina_param as $rowf)
				{
					$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
					$financial_ar[$date_key]=$rowf[csf("working_hour")];
				}
				unset($sql_fina_param);
				
				$dtls_arr=array();
				$sql_daydtls=sql_select("select mst_id, sum(no_of_line) as no_of_line from lib_capacity_calc_dtls where month_id='$monthId' and day_status=1 group by mst_id");
				foreach( $sql_daydtls as $rowd)
				{
					$dtls_arr[$rowd[csf("mst_id")]]=$rowd[csf("no_of_line")];
				}
				unset($sql_daydtls);
				$locatin_cond="";
				if($location_id>0) $locatin_cond=" and a.location_id='$location_id'";
				// $sql_data_smv=sql_select("select a.id, a.comapny_id, a.year, a.avg_machine_line, a.basic_smv, a.effi_percent, c.month_id, c.capacity_month_min, c.working_day from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id='$company_id' $locatin_cond  and a.year='$ex_month[0]' and c.month_id='$monthId'" );
				
				// $sql_data_smv=sql_select("SELECT a.id,count(b.id) as  working_day,a.year,b.month_id,b.day_status,b.no_of_line,sum(b.capacity_min) as capacity_month_min, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$company_id and a.year='$ex_month[0]' and b.month_id='$monthId' and a.capacity_source=1 and b.day_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0	group by a.id,b.month_id,a.year,b.day_status,b.no_of_line, a.avg_machine_line, a.basic_smv, a.effi_percent");
				$sql_data_smv=sql_select("select a.id, a.comapny_id, a.year, c.avg_mch_line as avg_machine_line, c.basic_smv, c.efficiency_per as effi_percent, c.month_id, c.capacity_month_min, c.working_day from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id='$company_id' $locatin_cond  and a.year='$ex_month[0]' and c.month_id='$monthId'" );


				
				$capacity_arr=array();
				foreach( $sql_data_smv as $row)
				{
					$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
					
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['clock_hrs']=(($row[csf("capacity_month_min")]/$row[csf("effi_percent")])*100);
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['efficency']=$row[csf("effi_percent")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['basic_smv']=$row[csf("basic_smv")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['line']=$row[csf("avg_machine_line")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['working_day']=$row[csf("working_day")];
					$no_of_line=0;
					$no_of_line=$dtls_arr[$row[csf("id")]];///$row[csf("working_day")];
					
					// $capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_line']=$no_of_line/$row[csf("working_day")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_line']=$row[csf("no_of_line")];
					
					$tot_hrs=0;
					$tot_hrs=$no_of_line*$row[csf("avg_machine_line")]*$financial_ar[$month]*$row[csf("working_day")];
					
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_hrs']=$tot_hrs;
				}
				unset($sql_data_smv);
				?>
                <tbody>
                    <tr bgcolor="#FFCCFF">
                        <td>1</td>
                        <td>Total Line</td>
                        <td align="right"><? echo number_format($capacity_arr[$ex_month[0]][$monthId]['tot_line'],0,'.',','); ?></td>
                	</tr>
                    <tr bgcolor="#FFFFFF">
                        <td>2</td>
                        <td>Man MC Ratio/Line</td>
                        <td align="right"><? echo number_format($capacity_arr[$ex_month[0]][$monthId]['line'],0,'.',','); ?></td>
                	</tr>
                    <tr bgcolor="#FFCCFF">
                        <td>3</td>
                        <td>Working Hrs/Day</td>
                        <td align="right"><? echo number_format($financial_ar[$month],0,'.',','); ?></td>
                	</tr>
                    <tr bgcolor="#FFFFFF">
                        <td>4</td>
                        <td>Monthly Working Day</td>
                       <td align="right"><? echo number_format($capacity_arr[$ex_month[0]][$monthId]['working_day'],0,'.',','); ?></td>
                	</tr>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="2" align="center"><strong>Clock Hours</strong></td>
                        <td align="right">
						<?
						//echo $capacity_arr[$ex_month[0]][$monthId]['tot_line'].'*'.$capacity_arr[$ex_month[0]][$monthId]['line'].'*'.$financial_ar[$month].'*'.$capacity_arr[$ex_month[0]][$monthId]['working_day'].'===';
						
						$clock_hours=$capacity_arr[$ex_month[0]][$monthId]['tot_line']*$capacity_arr[$ex_month[0]][$monthId]['line']*$financial_ar[$month]*$capacity_arr[$ex_month[0]][$monthId]['working_day'];
						 //echo number_format($capacity_arr[$ex_month[0]][$monthId]['tot_hrs'],0,'.',','); 
						 echo number_format($clock_hours,0,'.',','); 
						 ?></td>
                	</tr>
                    <tr bgcolor="#FFCCFF">
                        <td>5</td>
                        <td>Efficency (%)</td>
                        <td align="right"><? echo number_format($capacity_arr[$ex_month[0]][$monthId]['efficency'],0,'.',','); ?></td>
                	</tr>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="2" align="center"><strong>SAH (Stand. Available Hrs)</strong></td>
                        <td align="right"><? 
						$sah_stnd_avai=0;
						$sah_stnd_avai=($clock_hours*$capacity_arr[$ex_month[0]][$monthId]['efficency'])/100;
						echo number_format($sah_stnd_avai,0,'.',','); ?></td>
                	</tr>
                    <tr>
                        <td bgcolor="#FFFFFF">6</td>
                        <td>Basic SMV</td>
                        <td align="right"><? echo number_format($capacity_arr[$ex_month[0]][$monthId]['basic_smv'],2,'.',','); ?></td>
                	</tr>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="2" align="center"><strong>Eqv. Basic Qty (Pcs)</strong></td>
                        <td align="right"><? 
							$eqv_basic_qty=0;
							$eqv_basic_qty=($sah_stnd_avai*60)/$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
							echo number_format($eqv_basic_qty,0,'.',','); ?></td>
                	</tr>
                </tbody>
            </table>
        </div>
    </fieldset>
	<?
	exit();
}

if($action=="order_popup")
{
	echo load_html_head_contents("Clock Hours Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_month='';
	$ex_month=explode('-',$month);
	$monthId=0;
	
	if($ex_month[1]==10)
		$monthId=$ex_month[1];
	else
		$monthId=str_replace('0','',$ex_month[1]);
		
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $monthId,$ex_month[0]);
	$s_date=$ex_month[0]."-".$monthId."-"."01";
	$e_date=$ex_month[0]."-".$monthId."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	
	$string_dtls='';
	if($type==1)
	{
		$string_dtls="Confirm Booked SAH Details";
	}
	else
	{
		$string_dtls="Projections Booked SAH Details";
	}
	$width=1120;
	?>
    <script>
		var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["td_ordQty","td_ordQtyPcs","td_setQty","td_eqvBasicQty","td_unitPrice","td_ordValue"],
					col: [8,10,11,12,13,14],
					operation: ["sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
    </script>
	<fieldset style="width:<?=$width+20;?>px; margin-left:3px">
		<div>
			<table border="1" class="rpt_table" rules="all" width="<?=$width;?>" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="16"><? echo $string_dtls; ?></th>
                    </tr>
                    <tr>
                        <th width="25">Sl</th>
                        <th width="80">Buyer</th>
                        <th width="80">Style</th>
                        <th width="80">Order No</th>
                        <th width="70">Ship Date</th>
                        <th width="70">TNA Start Date</th>
                        <th width="70">TNA Finish Date</th>
                        <th width="60">SMV</th>
                        <th width="80">Order Qty</th>
                        <th width="40">UOM</th>
                        <th width="80">Order Qty (Pcs)</th>
                        <th width="60">Total SMV</th>
                        <th width="80">Eqv. Basic Qty (Pcs)</th>
                        <th width="50">Unit Price</th>
                        <th width="90">Order Value</th>
                        <th>Team Leader</th>
                    </tr>
				</thead>
            </table>
           <div style="max-height:300px; overflow-y:scroll; width:<?=$width+20;?>px" id="scroll_body">
        	<table cellspacing="0" border="1" class="rpt_table" width="<?=$width;?>px" rules="all" id="tbl_body" >
                <?
				$financial_arr=array();
				$sql_fina_param=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where company_id='$company_id' and status_active=1 and is_deleted=0 ");
				foreach( $sql_fina_param as $rowf)
				{
					$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
					$financial_ar[$date_key]=$rowf[csf("working_hour")];
				}
				unset($sql_fina_param);
				
				$dtls_arr=array();
				$sql_daydtls=sql_select("select mst_id, sum(no_of_line) as no_of_line from lib_capacity_calc_dtls where month_id='$monthId' and day_status=1 group by mst_id");
				foreach( $sql_daydtls as $rowd)
				{
					$dtls_arr[$rowd[csf("mst_id")]]=$rowd[csf("no_of_line")];
				}
				unset($sql_daydtls);
				$locatin_cond="";
				if($location_id>0) $locatin_cond=" and a.location_id='$location_id'";
				
				$sql_data_smv=sql_select("select a.id, a.comapny_id, a.year, a.avg_machine_line, a.basic_smv, a.effi_percent, c.month_id, c.capacity_month_min, c.working_day from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id='$company_id' $locatin_cond and a.year='$ex_month[0]' and c.month_id='$monthId'" );
				
				$capacity_arr=array();
				foreach( $sql_data_smv as $row)
				{
					$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
					
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['clock_hrs']=(($row[csf("capacity_month_min")]/$row[csf("effi_percent")])*100);
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['efficency']=$row[csf("effi_percent")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['basic_smv']=$row[csf("basic_smv")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['line']=$row[csf("avg_machine_line")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['working_day']=$row[csf("working_day")];
					$no_of_line=0;
					$no_of_line=$dtls_arr[$row[csf("id")]];///$row[csf("working_day")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_line']=$no_of_line/$row[csf("working_day")];
					
					$tot_hrs=0;
					$tot_hrs=$no_of_line*$row[csf("avg_machine_line")]*$financial_ar[$month]*$row[csf("working_day")];
					
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_hrs']=$tot_hrs;
				}
				unset($sql_data_smv);
				
				$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
				$temLeader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
				
				$locatin_cond="";
				if($location_id>0) $locatin_cond=" and a.location_name='$location_id'";
				
				$order_sql="select min(c.TASK_START_DATE) TASK_START_DATE, max(c.TASK_FINISH_DATE) TASK_FINISH_DATE,b.PO_NUMBER,a.buyer_name, a.style_ref_no, a.set_smv, a.order_uom, a.total_set_qnty, a.team_leader, b.pub_shipment_date, sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as order_value from wo_po_details_master a, wo_po_break_down b 
				LEFT JOIN TNA_PROCESS_MST c on b.id=c.PO_NUMBER_ID and c.TASK_NUMBER=86 and c.task_type=1
				where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND a.company_name='$company_id' $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and b.is_confirmed='$type' group by b.PO_NUMBER,a.buyer_name, a.style_ref_no, a.set_smv, a.order_uom, a.total_set_qnty, a.team_leader, b.pub_shipment_date";
				
				
				//echo $order_sql;
				 
				
				
				$order_sql_res=sql_select($order_sql); $i=1;
				foreach($order_sql_res as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$ord_qty_pcs=0; $unit_price=0; $eqv_basic_qty=0; 
					$ord_qty_pcs=$row[csf("po_quantity")]*$row[csf("total_set_qnty")];
					$unit_price=$row[csf("order_value")]/$row[csf("po_quantity")];
					
					$eqv_basic_qty=$ord_qty_pcs*($row[csf("set_smv")]/$capacity_arr[$ex_month[0]][$monthId]['basic_smv']);
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<td width="25"><? echo $i; ?></td>
                        <td width="80"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("PO_NUMBER")]; ?></p></td>
                        <td width="70" align="center"><p><? echo change_date_format($row[csf("pub_shipment_date")]); ?></p></td>
                        <td width="70" align="center"><p><? echo change_date_format($row[csf("TASK_START_DATE")]); ?></p></td>
                        <td width="70" align="center"><p><? echo change_date_format($row[csf("TASK_FINISH_DATE")]); ?></p></td>
                        <td width="60" align="center"><p><? echo $row[csf("set_smv")]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("po_quantity")],0); ?></p></td>
                        <td width="40"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
                        <td width="80" align="right"><? echo number_format($ord_qty_pcs,0); ?></td>
                        <td width="60" align="right"><? $total_set_smv=$row[csf("set_smv")]*$row[csf("po_quantity")]; echo number_format( $total_set_smv,2); //echo number_format($row[csf("total_set_qnty")],2); ?></td>
                        <td width="80" align="right"><? echo number_format($eqv_basic_qty,0); ?></td>
                        <td width="50" align="right"><? echo number_format($unit_price,2); ?></td>
                        <td width="90" align="right"><? echo number_format($row[csf("order_value")],2); ?></td>
                        <td><? echo $temLeader_arr[$row[csf("team_leader")]]; ?></td>
                    </tr>
                	<?
					$tot_ord_qty+=$row[csf("po_quantity")];
					$tot_ord_qty_pcs+=$ord_qty_pcs;
					$tot_set_qty+=$row[csf("set_smv")];
					$tot_eqv_basic_qty+=$eqv_basic_qty;
					$tot_unit_price+=$unit_price;
					$tot_order_value+=$row[csf("order_value")];
					$i++;
				}
			   ?>
            </table>
            </div>
            <table width="<?=$width;?>px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                <tfoot>
                    <tr>
                        <th width="25">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">Total</th>
                        <th width="80" id="td_ordQty"><? echo number_format($tot_ord_qty,2); ?></th>
                        <th width="40">&nbsp;</th>
                        <th width="80" id="td_ordQtyPcs"><? echo number_format($tot_ord_qty_pcs,2); ?></th>
                        <th width="60" id="td_setQty"><? echo number_format($tot_set_qty,2); ?></th>
                        <th width="80" id="td_eqvBasicQty"><? echo number_format($tot_eqv_basic_qty,2); ?></th>
                        <th width="50" id="td_unitPrice"><? echo number_format($tot_unit_price,2); ?></th>
                        <th width="90" id="td_ordValue"><? echo number_format($tot_order_value,2); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            
        </div>
    </fieldset>
    <script> setFilterGrid("tbl_body",-1,tableFilters);</script>
	<?
	exit();
}


if($action=="order_popup_tna")
{
	echo load_html_head_contents("Clock Hours Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_month='';
	$ex_month=explode('-',$month);
	$monthId=0;
	
	if($ex_month[1]==10)
		$monthId=$ex_month[1];
	else
		$monthId=str_replace('0','',$ex_month[1]);
		
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $monthId,$ex_month[0]);
	$s_date=$ex_month[0]."-".$monthId."-"."01";
	$e_date=$ex_month[0]."-".$monthId."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	
	$string_dtls='';
	if($type==1)
	{
		$string_dtls="Confirm Booked SAH Details";
	}
	else
	{
		$string_dtls="Projections Booked SAH Details";
	}
	$width=($popup_width-35);
	?>
    <script>
		var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["td_ordQty","td_ordQtyPcs","td_setQty","td_eqvBasicQty","td_unitPrice","td_ordValue"],
					col: [8,10,11,12,13,14],
					operation: ["sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
    </script>
	<fieldset style="width:<?=$width;?>px; margin-left:3px">
		<div>
			<table border="1" width="<?=$width;?>" class="rpt_table" rules="all" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="16"><? echo $string_dtls; ?></th>
                    </tr>
                    <tr>
                        <th width="25">Sl</th>
                        <th width="80">Buyer</th>
                        <th width="80">Style</th>
                        <th width="80">PO No</th>
                        <th width="70">Ship Date</th>
                        <th width="70">Plan Start Date</th>
                        <th width="70">Plan Finish Date</th>
                        <th width="60">SMV</th>
                        <th width="80">Order Qty</th>
                        <th width="40">UOM</th>
                        <th width="80">Order Qty (Pcs)</th>
                        <th width="60">Total SMV</th>
                        <th width="80">Eqv. Basic Qty (Pcs)</th>
                        <th width="50">Unit Price</th>
                        <th width="90">Order Value</th>
                        <th>Team Leader</th>
                    </tr>
				</thead>
            </table>
           <div style="max-height:300px; overflow-y:scroll; width:<?=$width+10;?>px" id="scroll_body">
        	<table cellspacing="0" border="1" class="rpt_table" width="<?=$width;?>px" rules="all" id="tbl_body" >
                <?
				$financial_arr=array();
				$sql_fina_param=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where company_id='$company_id' and status_active=1 and is_deleted=0 ");
				foreach( $sql_fina_param as $rowf)
				{
					$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
					$financial_ar[$date_key]=$rowf[csf("working_hour")];
				}
				unset($sql_fina_param);
				
				$dtls_arr=array();
				$sql_daydtls=sql_select("select mst_id, sum(no_of_line) as no_of_line from lib_capacity_calc_dtls where month_id='$monthId' and day_status=1 group by mst_id");
				foreach( $sql_daydtls as $rowd)
				{
					$dtls_arr[$rowd[csf("mst_id")]]=$rowd[csf("no_of_line")];
				}
				unset($sql_daydtls);
				$locatin_cond="";
				if($location_id>0) $locatin_cond=" and a.location_id='$location_id'";
				
				$sql_data_smv=sql_select("select a.id, a.comapny_id, a.year, a.avg_machine_line, a.basic_smv, a.effi_percent, c.month_id, c.capacity_month_min, c.working_day from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id='$company_id' $locatin_cond and a.year='$ex_month[0]' and c.month_id='$monthId'" );
				
				$capacity_arr=array();
				foreach( $sql_data_smv as $row)
				{
					$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
					
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['clock_hrs']=(($row[csf("capacity_month_min")]/$row[csf("effi_percent")])*100);
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['efficency']=$row[csf("effi_percent")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['basic_smv']=$row[csf("basic_smv")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['line']=$row[csf("avg_machine_line")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['working_day']=$row[csf("working_day")];
					$no_of_line=0;
					$no_of_line=$dtls_arr[$row[csf("id")]];///$row[csf("working_day")];
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_line']=$no_of_line/$row[csf("working_day")];
					
					$tot_hrs=0;
					$tot_hrs=$no_of_line*$row[csf("avg_machine_line")]*$financial_ar[$month]*$row[csf("working_day")];
					
					$capacity_arr[$row[csf("year")]][$row[csf("month_id")]]['tot_hrs']=$tot_hrs;
				}
				unset($sql_data_smv);
				
				$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
				$temLeader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
				
				/*$locatin_cond="";
				if($location_id>0) $locatin_cond=" and a.location_name='$location_id'";
				
				$order_sql="select a.buyer_name, a.style_ref_no, a.set_smv, a.order_uom, a.total_set_qnty, a.team_leader, b.pub_shipment_date, sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as order_value from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND a.company_name='$company_id' $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and b.is_confirmed='$type' group by a.buyer_name, a.style_ref_no, a.set_smv, a.order_uom, a.total_set_qnty, a.team_leader, b.pub_shipment_date";*/
				//echo $order_sql;
				
			
			
			$locatin_cond="";
			if($location_id>0){$locatin_cond=" AND a.location_name='$location_id'";}
			if($txt_order_no!=''){$locatin_cond.=" AND b.po_number='".trim($txt_order_no)."'";}
			
			$dateCon="and ((c.TASK_FINISH_DATE between '$s_date' and '$e_date') or (c.TASK_START_DATE  between '$s_date' and '$e_date' ))";
			
			$order_sql="SELECT b.PO_NUMBER,a.buyer_name, a.style_ref_no, a.set_smv, a.order_uom, a.total_set_qnty, a.team_leader, b.pub_shipment_date,c.TASK_START_DATE, c.TASK_FINISH_DATE, ( (c.TASK_FINISH_DATE-c.TASK_START_DATE)+1 ) as PLAN_LEAD_TIME,a.set_smv, a.total_set_qnty, b.id as po_id,B.PO_NUMBER, b.pub_shipment_date as shipment_date, b.po_total_price,
			(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirme_qty,
			(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
			(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as  confirme_value,
			(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
			FROM wo_po_details_master a, wo_po_break_down b,TNA_PROCESS_MST c
			WHERE a.job_no = b.job_no_mst and a.job_no=c.JOB_NO and b.id=c.PO_NUMBER_ID and c.TASK_NUMBER=86 and c.TASK_TYPE=1 AND a.company_name=$company_id $locatin_cond $dateCon and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_confirmed=$type";// and b.id=41672
			// echo $order_sql; 		
				
				$order_sql_res=sql_select($order_sql); $i=1;
				foreach($order_sql_res as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				//...........................................
				$first_date = strtotime($s_date);
				$last_date = strtotime($e_date);
				
				$shipment_date=strtotime($row[csf("shipment_date")]);
				$TASK_START_DATE=strtotime($row['TASK_START_DATE']);
				$TASK_FINISH_DATE=strtotime($row['TASK_FINISH_DATE']);
				
				$month_lead_time=0;
				if($TASK_START_DATE >= $first_date && $TASK_FINISH_DATE <= $last_date ){
					$month_lead_time = datediff( 'd', $row[TASK_START_DATE],$row[TASK_FINISH_DATE]);
				}
				else if($TASK_START_DATE >= $first_date && $TASK_START_DATE <= $last_date  && $TASK_FINISH_DATE >= $last_date  ){
					$month_lead_time = datediff( 'd', $row[TASK_START_DATE],date('d-m-Y',$last_date));
				}
				else if($TASK_START_DATE < $first_date &&  $TASK_FINISH_DATE <= $last_date &&  $TASK_FINISH_DATE>=$first_date){
					$month_lead_time = datediff( 'd', date('d-m-Y',$first_date),$row[TASK_FINISH_DATE]);
				}
				
				else if($TASK_START_DATE < $first_date && $TASK_FINISH_DATE >= $first_date && $TASK_FINISH_DATE >= $last_date ){
					$month_lead_time = datediff( 'd', date('d-m-Y',$first_date),date('d-m-Y',$last_date));
				}
			
				
				$confirm_qty=0; $projected_qty=0;
				
				$confirm_rate=($row[csf("confirme_value")]/$row[csf("confirme_qty")])*1;
				$projected_rate=$row[csf("projected_value")]/$row[csf("projected_qty")];
				
				$confirm_rate=(is_nan($confirm_rate))?0:$confirm_rate;
				$projected_rate=(is_nan($projected_rate))?0:$projected_rate;
				
				
				$tna_confirm_qty=($row[csf("confirme_qty")]/$row[PLAN_LEAD_TIME])*$month_lead_time;
				$tna_projected_qty=($row[csf("projected_qty")]/$row[PLAN_LEAD_TIME])*$month_lead_time;
				
				
				 //echo $month_lead_time.',';	
				//.........................
					
					
					
					//$unit_price=$row[csf("order_value")]/$row[csf("po_quantity")];
					
					
					//$row[csf("order_value")]=($type==1)?$row[csf("confirme_value")]:$row[csf("projected_value")];
					$row[csf("po_quantity")]=($type==1)?$tna_confirm_qty:$tna_projected_qty;
					$row[csf("order_value")]=($type==1)?($tna_confirm_qty*$confirm_rate):($tna_projected_qty*$projected_rate);


					$ord_qty_pcs=0; $unit_price=0; $eqv_basic_qty=0; 
					$ord_qty_pcs=$row[csf("po_quantity")]*$row[csf("total_set_qnty")];
					$unit_price=($type==1)?$confirm_rate:$projected_rate;
					
					
					
					
					
					
					$eqv_basic_qty=$ord_qty_pcs*($row[csf("set_smv")]/$capacity_arr[$ex_month[0]][$monthId]['basic_smv']);
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<td width="25"><? echo $i; ?></td>
                        <td width="80"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                        <td width="80"><p><? echo $row['PO_NUMBER']; ?></p></td>
                        <td width="70"><p><? echo change_date_format($row[csf("pub_shipment_date")]); ?></p></td>
                        <td width="70"><p><? echo change_date_format($row['TASK_START_DATE']); ?></p></td>
                        <td width="70"><p><? echo change_date_format($row['TASK_FINISH_DATE']); ?></p></td>
                        
                        <td width="60" align="center"><p><? echo $row[csf("set_smv")]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("po_quantity")],0); ?></p></td>
                        <td width="40" align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
                        <td width="80" align="right"><? echo number_format($ord_qty_pcs,0); ?></td>
                        <td width="60" align="right"><? $total_set_smv=$row[csf("set_smv")]*$row[csf("po_quantity")]; echo number_format( $total_set_smv,2); //echo number_format($row[csf("total_set_qnty")],2); ?></td>
                        <td width="80" align="right"><? echo number_format($eqv_basic_qty,0); ?></td>
                        <td width="50" align="right"><? echo number_format($unit_price,2); ?></td>
                        <td width="90" align="right"><? echo number_format($row[csf("order_value")],2); ?></td>
                        <td><? echo $temLeader_arr[$row[csf("team_leader")]]; ?></td>
                    </tr>
                	<?
					$tot_ord_qty+=$row[csf("po_quantity")];
					$tot_ord_qty_pcs+=$ord_qty_pcs;
					$tot_set_qty+=$row[csf("set_smv")];
					$tot_eqv_basic_qty+=$eqv_basic_qty;
					$tot_unit_price+=$unit_price;
					$tot_order_value+=$row[csf("order_value")];
					$i++;
				}
			   ?>
            </table>
            </div>
            <table width="<?=$width;?>px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                <tfoot>
                    <tr>
                        <th width="25">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">Total</th>
                        <th width="80" id="td_ordQty"><? echo number_format($tot_ord_qty,2); ?></th>
                        <th width="40">&nbsp;</th>
                        <th width="80" id="td_ordQtyPcs"><? echo number_format($tot_ord_qty_pcs,2); ?></th>
                        <th width="60" id="td_setQty"><? echo number_format($tot_set_qty,2); ?></th>
                        <th width="80" id="td_eqvBasicQty"><? echo number_format($tot_eqv_basic_qty,2); ?></th>
                        <th width="50" id="td_unitPrice"><? echo number_format($tot_unit_price,2); ?></th>
                        <th width="90" id="td_ordValue"><? echo number_format($tot_order_value,2); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            
        </div>
    </fieldset>
    <script> setFilterGrid("tbl_body",-1,tableFilters);</script>
	<?
	exit();
}



?>