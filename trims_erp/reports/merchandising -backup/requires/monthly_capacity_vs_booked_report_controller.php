<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];




if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--All--", $selected, "check_unique_id();","","","","","",3 );
	exit();		 
}




if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_section_id=str_replace("'","",$cbo_section_id);
	$cbo_sub_section_id=str_replace("'","",$cbo_sub_section_id);
	$cbo_from_month=str_replace("'","",$cbo_from_month);
	$cbo_from_year=str_replace("'","",$cbo_from_year);
	$cbo_to_month=str_replace("'","",$cbo_to_month);
	$cbo_to_year=str_replace("'","",$cbo_to_year);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0 and id=$cbo_company_id","id","company_name");
	
	//Capacity con........................................................
	$whre_con='';
	if($cbo_company_id!=0){$whre_con.=" and a.company_id=$cbo_company_id";}
	if($cbo_location_id!=0){$whre_con.=" and a.location_id=$cbo_location_id";}
	if($cbo_section_id!=0){$whre_con.=" and a.section_id=$cbo_section_id";}
	if($cbo_sub_section_id!=0){$whre_con.=" and a.sub_section_id=$cbo_sub_section_id";}
	
	//Booked con........................................................
	$whre_booked_con='';
	if($cbo_company_id!=0){$whre_booked_con.=" and a.company_id=$cbo_company_id";}
	if($cbo_location_id!=0){$whre_booked_con.=" and a.location_id=$cbo_location_id";}
	if($cbo_section_id!=0){$whre_booked_con.=" and b.section=$cbo_section_id";}
	if($cbo_sub_section_id!=0){$whre_booked_con.=" and b.sub_section=$cbo_sub_section_id";}


	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_to_month, $cbo_to_year);
	$start_date=$cbo_from_year."-".$cbo_from_month."-"."01";
	$end_date=$cbo_to_year."-".$cbo_to_month."-".$daysinmonth;

	
	if($db_type==0) 
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd');
		$end_date=change_date_format($end_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$start_date=change_date_format($start_date,'','',1);
		$end_date=change_date_format($end_date,'','',1);
	}
	
	
	
	$tot_month = datediff( 'm', $start_date,$end_date);
	$width=(($tot_month+1)*265)+250;

	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($start_date,$i);
		$month_arr[]=date("M-Y",strtotime($next_month));
	}
	//print_r($month_arr);	
		
		
	$capacity_sql="select a.company_id,a.location_id,a.section_id,a.sub_section_id,a.capacity_year,b.month_id,b.monthly_capacity_tk,c.daily_capacity_usd,c.capacity_date  from trims_capacity_calculation_mst a, trims_capacity_cal_dtls b, trims_capacity_cal_day_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.month_id=c.month_id and b.mst_id=c.mst_id
and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and c.capacity_date between '$start_date' and '$end_date' $whre_con";
	 //echo $capacity_sql;die;
		
	$result_capacity_sql = sql_select($capacity_sql);
	foreach($result_capacity_sql as $row)
	{
		$my=date("M-Y",strtotime($row["CAPACITY_DATE"]));
		$date_array[$row["SECTION_ID"]][$row["SUB_SECTION_ID"]][$my]+=$row[csf("daily_capacity_usd")];
		$section_total_arr[$my][$row["SECTION_ID"]]+=$row[csf("daily_capacity_usd")];
	}
	//print_r($section_total_arr);	

	
	$conversion_rate_sql="select con_date,conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 order by con_date";
	$result_conversion_rate_sql = sql_select($conversion_rate_sql);
	foreach($result_conversion_rate_sql as $row)
	{
		$dmy=date("d-M-Y",strtotime($row[csf('con_date')]));
		$conversion_rate_arr[$dmy]=$row[csf('conversion_rate')];
	}
	
	
	$booking_sql="select a.receive_date,b.sub_section,b.section,b.amount_domestic   from subcon_ord_mst a,subcon_ord_dtls b where a.subcon_job=b.job_no_mst  and a.receive_date between '$start_date' and '$end_date' $whre_booked_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	//echo $booking_sql;		
	$result_booking_sql = sql_select($booking_sql);
	foreach($result_booking_sql as $row)
	{
		
		
		$date_array[$row["SECTION"]][$row["SUB_SECTION"]][$my]+=0;
		
		$td=0;$conversion_rate=0;
		for($d=0;$d<=$td;$d++){
			$prev_month=add_date($row["RECEIVE_DATE"],-$d);
			$dmy=date("d-M-Y",strtotime($prev_month));
			if($conversion_rate_arr[$dmy]){
				$conversion_rate = $conversion_rate_arr[$dmy];
			}
			else
			{
				$td++;	
			}
		}
		
		
		$my=date("M-Y",strtotime($row["RECEIVE_DATE"]));
		$booking_sub_section_total_arr[$my][$row["SECTION"]][$row["SUB_SECTION"]]+=($row["AMOUNT_DOMESTIC"]/$conversion_rate);
		$booking_section_total_arr[$my][$row["SECTION"]]+=($row["AMOUNT_DOMESTIC"]/$conversion_rate);
	}
	
	 //print_r($booking_sub_section_total_arr);	
	// print_r($booking_section_total_arr);	
		
		
		ob_start();
		?>
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="<? echo (($tot_month+1)*3)+3;?>" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="<? echo (($tot_month+1)*3)+3;?>" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="<? echo (($tot_month+1)*3)+3;?>" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".$months[$cbo_from_month].'-'.$cbo_from_year ." To : ". $months[$cbo_to_month].'-'.$cbo_to_year ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <tr>
                    	<th rowspan="2" width="35">SL</th>
						<th rowspan="2" >Section</th>
						<th rowspan="2" width="120">Sub-section</th>
						<? foreach($month_arr as $my){echo "<th colspan='3'>$my</th>";}?>
                    </tr>
                    <tr>
						<? foreach($month_arr as $my){?>
                        <th width="100">Capacity value $</th>
						<th width="100">Booked value $</th>
						<th width="60">Booked %</th>
                        <? } ?>
                    </tr>
				</thead>
			</table>
			<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
			<?
			 	$i=1;$sl=0;
				foreach($date_array as $section_id=>$section_row)
				{ 
					$sl++;
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
				?>
				 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				
					<td rowspan="<? echo count($section_row);?>" width="35" align="center"><? echo $sl;?></td>
					<td rowspan="<? echo count($section_row);?>" valign="middle"><? echo $trims_section[$section_id];?></td>
				<?
                $flag=1;
				foreach($section_row as $sub_section_id=>$sub_section_row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
						
					if($flag!=1){?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <? } ?>
                    
                    
						<td width="120"><? echo $trims_sub_section[$sub_section_id];?></td>
						<? foreach($month_arr as $my){?>
                        <td width="100" align="right"><? echo fn_number_format($sub_section_row[$my],0);?></td>
						<td width="100" align="right"><? echo fn_number_format($booking_sub_section_total_arr[$my][$section_id][$sub_section_id],0);?></td>
						<td width="60" align="right"><? echo fn_number_format(($booking_sub_section_total_arr[$my][$section_id][$sub_section_id]/$sub_section_row[$my])*100,2);?></td>
                        <? } ?>
					</tr>
						<?
						$i++;
						$flag++;
				}
				?>
					<tr bgcolor="#CCCCCC">
						<td colspan="2" align="right"><strong>Section Total</strong></td>
						<td></td>
						<? foreach($month_arr as $my){?>
                        <td align="right"><strong><? echo fn_number_format($section_total_arr[$my][$section_id],0);?></strong></td>
						<td align="right"><strong><? echo fn_number_format($booking_section_total_arr[$my][$section_id],0);?></strong></td>
						<td align="right"><strong><? echo fn_number_format(($booking_section_total_arr[$my][$section_id]/$section_total_arr[$my][$section_id])*100,2);?></strong></td>
                        <? } ?>
					</tr>
				<?
				}
			?>
			</table>
			</div>
			<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
                    	<th  width="35"></th>
						<th align="right">Grand Total</th>
						<th width="120"></th>
						<? foreach($month_arr as $my){?>
                        <th align="right" width="100"><? echo fn_number_format(array_sum($section_total_arr[$my]),0);?></th>
						<th align="right" width="100"><? echo fn_number_format(array_sum($booking_section_total_arr[$my]),0);?></th>
						<th align="right" width="60"><? echo fn_number_format((array_sum($booking_section_total_arr[$my])/array_sum($section_total_arr[$my]))*100,2);?></th>
                        <? } ?>
					</tr>
				</tfoot>
			</table>
        </div>
        <?
	

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}



?>
