<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");


require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');

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
	$cbo_date_cat_id=str_replace("'","",$cbo_date_cat_id);
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
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
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_id='$location_id'";
	
	
	$working_hour_arr=array();
	$sql_fina_param=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where company_id in($cbo_company_name) and status_active=1 and is_deleted=0 order by applying_period_date");
	foreach( $sql_fina_param as $rowf)
	{
		$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
		$working_hour_arr[$date_key]=$rowf[csf("working_hour")];
	}
	unset($sql_fina_param);

	 $sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name and c.month_id between $cbo_month and $cbo_month_end group by a.comapny_id,a.location_id,a.year,a.effi_percent, a.basic_smv,a.avg_machine_line,b.no_of_line,c.working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs"; //echo $sql;
	$sql_data_smv=sql_select($sql);
	$tot_row_data_smv=count($sql_data_smv);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
		$capacity_arr[$date_key]['efficency']+=$row[csf("effi_percent")];
		$capacity_arr[$date_key]['basic_smv']+=$row[csf("basic_smv")];
		$capacity_arr[$date_key]['avg_machine_line']+=$row[csf("avg_machine_line")];
		$capacity_arr[$date_key]['working_day']+=$row[csf("working_day")];
		$capacity_arr[$date_key]['no_of_line']+=$row[csf("no_of_line")];
		$capacity_arr[$date_key]['tot_mc_val']+=$row[csf("no_of_line")]*$row[csf("avg_machine_line")];
		$capacity_arr[$date_key]['capacity_month_min']+=$row[csf("capacity_month_min")];
		$capacity_arr[$date_key]['capacity_month_pcs']+=$row[csf("capacity_month_pcs")];
		$location_id_arr[$date_key].=$row[csf("location_id")].',';
	}
	unset($sql_data_smv);
	
	//var_dump($capacity_arr);die;
	
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
	

	if($cbo_date_cat_id==1)
	{
	$sql="SELECT a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.pub_shipment_date,b.unit_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else //Country Ship Date
	{
		$sql="SELECT a.job_no,a.set_smv,a.set_break_down,a.buyer_name,a.total_set_qnty, c.country_ship_date as pub_shipment_date,b.unit_price,
	(c.order_quantity/a.total_set_qnty) as po_quantity,
	(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $locatin_cond AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	
	//echo $sql;
	$sql_data=sql_select($sql);
	foreach( $sql_data as $row)
	{
		
		$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
		$order_data_array[$year_month]['rowspan']+=1;
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];
		
		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		if($cbo_date_cat_id==1)
			{
				foreach($set_break_down_arr as $set_break_down){
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1)
				{
				$confirm_qty=$row[csf('confirm_qty')]*$set;
				$project_qty=$row[csf('projected_qty')]*$set;
				}
				else
				{
					$confirm_qty=$row[csf('confirm_qty')];
					$project_qty=$row[csf('projected_qty')];
				}
			
			$confirm_value=$row[csf('confirm_value')];
			$project_value=$row[csf('projected_value')];
			
			
			
			
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
				
				//$set_smv_array[$date_key][$row[csf('buyer_name')]]['set_smv']+=$row[csf('po_quantity')];
			
			//$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;
			//$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;
			
			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$project_qty;
			
			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);
			
			//$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			//$projected_booked_parcent_arr[$date_key]+=(($smv*$project_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;

			}
			
		}
		else
		{
			$item_id=$set_break_down_arr[0];
			$set=$set_break_down_arr[1];
			$smv=$set_break_down_arr[2];
			
			
			$confirm_qty=$row[csf('confirm_qty')];
			$project_qty=$row[csf('projected_qty')];
			
			$confirm_value=$row[csf('confirm_value')];
			$project_value=$row[csf('projected_value')];
			
			
			$po_qty_set_smv=$row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv=$po_qty_set_smv/$row[csf('po_quantity')];
			//$set_smv_array[$date_key][$row[csf('buyer_name')]]['set_smv']+=$po_qty_set_smv/$row[csf('po_quantity')];
			
			//echo $smv.', ';
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			//$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;
			//$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;
			
			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;
			
			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);
			
			//$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			//$projected_booked_parcent_arr[$date_key]+=(($smv*$project_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;

		}
		
		
		//$order_data_array[$date_key][$row[csf('buyer_name')]]+=$row[csf('confirm_qty')];
		//$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$row[csf('confirm_value')];
		//$order_data_array[$date_key][$row[csf('buyer_name')]]['confirm_value']+=$row[csf('confirm_value')];
		//$smv_arr[$date_key][$row[csf("buyer_name")]][$row[csf("job_no")]]=$row[csf("smv_pcs")];
		
		//$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($row[csf("smv_pcs")]*$row[csf('confirm_qty')]);
	}
	
	  //var_dump($order_data_array['2017-01']);
	
	$sql_data="select pd.plan_qnty as pdplan_qnty,b.pub_shipment_date from wo_po_break_down b,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  b.id=c.po_break_down_id and pd.plan_id=c.plan_id and c.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between  '$s_date' and '$e_date'";
	$data_result=sql_select($sql_data);
	$plan_qty_arr=array();
	foreach( $data_result as $row)
	{
		$plan_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]+=$row[csf("pdplan_qnty")];
	}
	
	//var_dump($plan_qty_arr);
	

	ob_start();	
	?>
	<div style="margin:0 auto; width:1500px;">
        <table width="1680" border="0" cellpadding="2" cellspacing="0"> 
            <thead>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                </tr>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:14px;">
                       <b><? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></b>
                    </td>
                </tr>
            </thead>
        </table>
        <div style="width:1680px;" id="scroll_body">
        <table cellspacing="0" width="1660" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="23">Monthly Capacity Information</th>
               </tr>
                <tr>
                   <th width="70">Month</th>
                 
                  
                   <th width="60">Total MC/Man</th>
				    <th width="60">Line</th>
                   <th width="60">Working Hrs/Day</th>
                   <th width="60">Target Eff. %</th>
                   <th width="60">Working Day</th>
                   <th width="60">Capacity Mints [100 % Eff.]</th>
                   <th width="60">Factory Capacity PC</th>
                   <th width="60">Factory Capacity Mints</th>
                   <th width="60">Projected Qty (Pcs)</th>
                   <th width="60">Confirmed Qty (Pcs)</th>
				    <th width="60">Total Pcs (Proj + Conf)</th>
				   
				   <th width="60">Projected FOB  Value</th>
                   <th width="60">Confirmed FOB  Value</th>
				   
				  
				   <th width="60">Total FOB (Proj + Conf)</th>
				   
                   <th width="60">Projected Mints</th>
                   <th width="60">Confirmed Mints</th>
				   <th width="100">Total Mints (Proj + Conf)</th>
                   <th width="80">Planned</th>
                   <th width="80">Balanced Planning</th>
                  
                   
                   <th width="60">Capacity Utilization %</th>
                   <th width="60">Over Booking</th>
                   <th>Less Booking %</th>
                   
                </tr>
            </thead>
            <tbody>
				 <? $i=1;
                 foreach($month_arr as $year_month){
                    list($year,$month)=explode('-',$year_month);
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$tot_mc_val= $capacity_arr[$year_month]['tot_mc_val'];
                	$totalMechine=$tot_mc_val;//$capacity_arr[$year_month]['no_of_line']*$capacity_arr[$year_month]['avg_machine_line'];
                    $capacityMints=$totalMechine*$working_hour_arr[$year_month]*$capacity_arr[$year_month]['working_day']*60;
                    $less_over=$over_parcent=$less_parcent='';
					$less_over=$booked_parcent_arr[$year_month]-100;
					if($less_over>0){$over_parcent=number_format($less_over,2);}
					else{$less_parcent=number_format($less_over,2);}
					
					$location_ids=rtrim($location_id_arr[$year_month],',');
					
					
					$location_ids=array_unique(explode(",",$location_ids));
					$tot_location=count($location_ids);
					
					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="70"><b><? echo $months[$month*1].'-'.$year; ?></b></td>
                   
				    <td width="60" align="right"><? echo $totalMechine; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['no_of_line']; ?></td>
                   
                    <td width="60" align="right"><? echo $working_hour_arr[$year_month]; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['efficency']/$tot_location; ?></td>
                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month]['working_day']/$tot_location); ?></td>
                    <td width="60" align="right"><? echo $capacityMints; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_pcs']; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_min']; ?></td>
                    
                    <td width="60" align="right"><? echo array_sum($projected_qty_array[$year_month]); ?></td>
                    <td width="60" align="right"><? echo array_sum($confirm_qty_array[$year_month]); ?></td>
					 <td width="100" align="right">
					<? 
					$projectedPlusConfirmedQty=array_sum($projected_qty_array[$year_month])+array_sum($confirm_qty_array[$year_month]);
					echo number_format($projectedPlusConfirmedQty,2);
					?>
                     </td>
					
					<td width="60" align="right"><? echo number_format(array_sum($projected_value_array[$year_month]),0); ?></td>
                    <td width="60" align="right"><? echo number_format(array_sum($confirm_value_array[$year_month]),0); ?></td>
					
					<td width="60" align="right"><?
					$tot_conf_proj_value=array_sum($confirm_value_array[$year_month])+array_sum($projected_value_array[$year_month]);
					 echo number_format($tot_conf_proj_value,0); ?></td>
					
					
                    <td width="60" align="right"><? echo number_format(array_sum($projected_booked_smv_mints_arr[$year_month]),2); ?></td>
                    <td width="60" align="right"><? echo number_format(array_sum($booked_smv_mints_arr[$year_month]),2); ?></td>
                     <td width="60" align="right">
					<? 
					$projectedPlusConfirmedMint=array_sum($projected_booked_smv_mints_arr[$year_month])+array_sum($booked_smv_mints_arr[$year_month]);
					echo number_format($projectedPlusConfirmedMint,2);
					 ?>
                    </td>
                    
                    <td width="80" align="right"><? echo $plan_qty_arr[$year_month]; ?></td>
                    <td width="80" align="right"><? echo array_sum($confirm_qty_array[$year_month])-$plan_qty_arr[$year_month]; ?></td>
                    
                    
                    
                  
                   
                    
                    <td width="60" align="right"><? echo number_format($booked_parcent_arr[$year_month],2); ?></td>
                    <td width="60" align="right"><? echo number_format($over_parcent,2); ?></td>
                    <td align="right"><? echo number_format($less_parcent,2); ?></td>
                
                </tr>
                <? $i++;} ?>
            </tbody>
         </table>
         </div>
         
         <!--//Order information.............................................-->
         <br /><br />
        <div style="width:1180px;">
        <table cellspacing="0" width="1160" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="13">Buyer wise capacity booked summary</th>
               </tr>
                <tr>
                   <th width="70">Month</th>
                   <th width="130">Buyer</th>
                   <th width="80">Projected Qty (Pcs)</th>
                   <th width="80">Confirmed Qty (Pcs)</th>
                   <th width="80">Total Pcs (Proj + Conf)</th>
                   <th width="80">Projected Value (USD)</th>
                   <th width="80">Confirmed Value (USD)</th>
				   
				   <th width="80">Total FOB Value (Proj + Conf)</th>
					  
                  
                   <th width="60">Avg. SMV</th>
                   <th width="80">Projected Mints</th>
                   <th width="80">Confirmed Mints</th>
				    <th width="80">Total Mints (Proj + Conf)</th>
                   <th>Booked %</th>
                </tr>
            </thead>
            <tbody>
                     <? $i=1;
					 foreach($month_arr as $year_month){
						 $tr=0;
						list($year,$month)=explode('-',$year_month);
						
						$rowspan=count($order_data_array[$year_month])+2;
						
						$fn = "change_color('trb_".$i."','".$bgcolor."')";
						echo '
						<tr bgcolor="'.$bgcolor.'" id="trb_'.$i.'"  onclick="'.$fn.'">
							<td width="70" rowspan="'.$rowspan.'" valign="middle"><b>'.$months[$month*1].'-'.$year.'</b></td>';

						foreach($order_data_array[$year_month] as $buyer_id=>$confirm_qty){
							$bookedMints=$booked_smv_mints_arr[$year_month][$buyer_id];
							$projectedbookedMints=$projected_booked_smv_mints_arr[$year_month][$buyer_id];
							$project_qty= $projected_qty_array[$year_month][$buyer_id];
							
							//$avgSMV=$bookedMints/$confirm_qty;
							$avgSMV=($bookedMints+$projectedbookedMints)/($confirm_qty+$project_qty);
							//$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
							$bookedParcent=0;
							if($capacity_arr[$year_month]['capacity_month_min'])
							{
							$bookedParcent=(($bookedMints+$projectedbookedMints)/$capacity_arr[$year_month]['capacity_month_min'])*100;
							}

							
							$confirm_qty_arr[$year_month]+=$confirm_qty;
							$projected_qty_arr[$year_month]+=$project_qty;

							$confirm_value_arr[$year_month]+=$confirm_value_array[$year_month][$buyer_id];
							$projected_value_arr[$year_month]+=$projected_value_array[$year_month][$buyer_id];

							$booked_mints_arr[$year_month]+=$bookedMints;
							$projected_booked_mints_arr[$year_month]+=$projectedbookedMints;
							
							$avg_smg_arr[$year_month]+=$avgSMV;
							//$booked_parcent_arr[$year_month]+=$bookedParcent;
							
						 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						 $fn = "change_color('trb_".$i."','".$bgcolor."')";
						 
						
						 
						 if($tr!=0){echo '<tr bgcolor="'.$bgcolor.'" id="trb_'.$i.'"  onclick="'.$fn.'">';}
						 ?>
                        <td width="130"><? echo $buyer_arr[$buyer_id]; ?></td>
                        <td width="80" align="right"><? echo $project_qty; ?></td>
                        <td width="80" align="right"><? echo $confirm_qty; ?></td>
                        <td width="80" align="right"><? echo $project_qty+$confirm_qty; ?></td>
                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id],2); ?></td>
                        <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$buyer_id],2); ?></td>
						<td width="80" align="right"><? 
						$tot_conf_proj_val=$projected_value_array[$year_month][$buyer_id]+$confirm_value_array[$year_month][$buyer_id];
						echo number_format($tot_conf_proj_val,2); ?></td>
						
                       
                        <td width="60" align="right"><? echo number_format($avgSMV,2); ?></td>
                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
						<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
                        <td align="right"><? echo number_format($bookedParcent,2); ?></td>
                    </tr>
                    <? $tr++;$i++;} ?>
                    <tr bgcolor="#DDD">
                        <th>Total:</th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_value_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_value_arr[$year_month],0); ?></th>
						
						<th align="right"><? echo number_format($projected_value_arr[$year_month]+$confirm_value_arr[$year_month],0); ?></th>
						
                       
                        <th align="right"><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></td>
                         <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
						 <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>
                    </tr>
                    
                    <tr bgcolor="#FFFF00">
                        <th colspan="11">
                        	<? 
								$less_over=$booked_parcent_arr[$year_month]-100;
								if($less_over>0){echo "Capacity booked Over %";}
								else{echo "Capacity booked Less %";}
							?>
                            
                        </th>
                        <th align="right"><? echo number_format($less_over,2); ?></th>
                    </tr>
                    
                    
                    
                    
					<?
					
					} ?>
            </tbody>
         </table>
         </div>
         
         
         
	</div>
	<?
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
if($action=="report_generate3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_name=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$cbo_year_name=str_replace("'","",$cbo_year_start);
	$cbo_month=str_replace("'","",$cbo_month_start);
	$cbo_end_year_name=str_replace("'","",$cbo_year_end);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_date_cat_id=str_replace("'","",$cbo_date_cat_id);
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
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
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_id='$location_id'";
	
	
	$working_hour_arr=array();
	$sql_fina_param=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where company_id in($cbo_company_name) and status_active=1 and is_deleted=0 order by applying_period_date");
	foreach( $sql_fina_param as $rowf)
	{
		$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
		$working_hour_arr[$date_key]=$rowf[csf("working_hour")];
	}
	unset($sql_fina_param);

	 $sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name and c.month_id between $cbo_month and $cbo_month_end group by a.comapny_id,a.location_id,a.year,a.effi_percent, a.basic_smv,a.avg_machine_line,b.no_of_line,c.working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs"; //echo $sql;
	$sql_data_smv=sql_select($sql);
	$tot_row_data_smv=count($sql_data_smv);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
		$capacity_arr[$date_key]['efficency']+=$row[csf("effi_percent")];
		$capacity_arr[$date_key]['basic_smv']+=$row[csf("basic_smv")];
		$capacity_arr[$date_key]['avg_machine_line']+=$row[csf("avg_machine_line")];
		$capacity_arr[$date_key]['working_day']+=$row[csf("working_day")];
		$capacity_arr[$date_key]['no_of_line']+=$row[csf("no_of_line")];
		$capacity_arr[$date_key]['tot_mc_val']+=$row[csf("no_of_line")]*$row[csf("avg_machine_line")];
		$capacity_arr[$date_key]['capacity_month_min']+=$row[csf("capacity_month_min")];
		$capacity_arr[$date_key]['capacity_month_pcs']+=$row[csf("capacity_month_pcs")];
		$location_id_arr[$date_key].=$row[csf("location_id")].',';
	}
	unset($sql_data_smv);
	
	//var_dump($capacity_arr);die;
	
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
	

	if($cbo_date_cat_id==1)
	{
	$sql="SELECT a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.pub_shipment_date,b.unit_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,b.id as po_id,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else //Country Ship Date
	{
		$sql="SELECT a.job_no,a.set_smv,a.set_break_down,a.buyer_name,a.total_set_qnty, c.country_ship_date as pub_shipment_date,b.unit_price,b.id as po_id,
	(c.order_quantity/a.total_set_qnty) as po_quantity,
	(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $locatin_cond AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	
//	echo $sql;
	$sql_data=sql_select($sql);
	foreach( $sql_data as $row)
	{
		
		$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
		$order_data_array[$year_month]['rowspan']+=1;
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];
		$po_wise_rate_arr[$row[csf('po_id')]]['rate']=$rate_in_pcs;
		$po_wise_rate_arr[$row[csf('po_id')]]['buyer']=$row[csf('buyer_name')];
		
		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		if($cbo_date_cat_id==1)
			{
				foreach($set_break_down_arr as $set_break_down){
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1)
				{
				$confirm_qty=$row[csf('confirm_qty')]*$set;
				$project_qty=$row[csf('projected_qty')]*$set;
				}
				else
				{
					$confirm_qty=$row[csf('confirm_qty')];
					$project_qty=$row[csf('projected_qty')];
				}
				$allpo_ids.=$row[csf('po_id')].',';
			
			$confirm_value=$row[csf('confirm_value')];
			$project_value=$row[csf('projected_value')];
			
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
				
				//$set_smv_array[$date_key][$row[csf('buyer_name')]]['set_smv']+=$row[csf('po_quantity')];
			
			//$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;
			//$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;
			
			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$project_qty;
			
			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);
			
			//$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			//$projected_booked_parcent_arr[$date_key]+=(($smv*$project_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;

			}
			
		}
		else
		{
			$item_id=$set_break_down_arr[0];
			$set=$set_break_down_arr[1];
			$smv=$set_break_down_arr[2];
			
			$allpo_ids.=$row[csf('po_id')].',';
			
			$confirm_qty=$row[csf('confirm_qty')];
			$project_qty=$row[csf('projected_qty')];
			
			$confirm_value=$row[csf('confirm_value')];
			$project_value=$row[csf('projected_value')];
			
			
			$po_qty_set_smv=$row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv=$po_qty_set_smv/$row[csf('po_quantity')];
			//$set_smv_array[$date_key][$row[csf('buyer_name')]]['set_smv']+=$po_qty_set_smv/$row[csf('po_quantity')];
			
			//echo $smv.', ';
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			//$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;
			//$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;
			
			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;
			
			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);
			
			//$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			//$projected_booked_parcent_arr[$date_key]+=(($smv*$project_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;

		}
		
		
		//$order_data_array[$date_key][$row[csf('buyer_name')]]+=$row[csf('confirm_qty')];
		//$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$row[csf('confirm_value')];
		//$order_data_array[$date_key][$row[csf('buyer_name')]]['confirm_value']+=$row[csf('confirm_value')];
		//$smv_arr[$date_key][$row[csf("buyer_name")]][$row[csf("job_no")]]=$row[csf("smv_pcs")];
		
		//$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($row[csf("smv_pcs")]*$row[csf('confirm_qty')]);
	}
	// $allpo_ids=rtrim($allpo_ids,',');
	 $tot_po_ids=implode(',',array_unique(explode(',',$allpo_ids)));
	// echo  $tot_po_ids;
	  //var_dump($order_data_array['2017-01']);
	  $poIds=chop($tot_po_ids,',');$po_cond_for_in="";
	 $po_ids=count(array_unique(explode(",",$poIds)));
	// echo  $po_ids.'dd';
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" c.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
		}
		else
		{
			$po_cond_for_in=" and c.po_break_down_id in($poIds)";
			
		}
						
	
	$sql_data="select pd.plan_qnty as pdplan_qnty,b.pub_shipment_date from wo_po_break_down b,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  b.id=c.po_break_down_id and pd.plan_id=c.plan_id and c.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between  '$s_date' and '$e_date'";
	$data_result=sql_select($sql_data);
	$plan_qty_arr=array();
	foreach( $data_result as $row)
	{
		$plan_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]+=$row[csf("pdplan_qnty")];
	}
	if($cbo_date_cat_id==1)
		{
			 $sql_exf="select c.po_break_down_id as po_id,b.pub_shipment_date,
			(CASE WHEN  c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as exf_qnty,
			(CASE WHEN  c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ret_exf_qnty
			from  pro_ex_factory_mst c,wo_po_break_down b where b.id=c.po_break_down_id and  b.status_active=1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted =0 and b.pub_shipment_date between  '$s_date' and '$e_date' $po_cond_for_in";
			$sql_exf_res=sql_select($sql_exf);
			$exfact_qty_arr=array();
			foreach($sql_exf_res as $row)
			{
				$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
				$bal_exfact_qty=$row[csf('exf_qnty')]-$row[csf('ret_exf_qnty')];
				$po_wise_rate=$po_wise_rate_arr[$row[csf('po_id')]]['rate'];
				$po_buyer=$po_wise_rate_arr[$row[csf('po_id')]]['buyer'];
				$exfact_qty_arr[$date_key]['exfact_qty']+=$bal_exfact_qty;
				$exfact_qty_arr[$date_key]['exfact_value']+=$bal_exfact_qty*$po_wise_rate;
				$buyer_exfact_qty_arr[$date_key][$po_buyer]['exfact_qty']+=$bal_exfact_qty;
				$buyer_exfact_qty_arr[$date_key][$po_buyer]['exfact_value']+=$bal_exfact_qty*$po_wise_rate;
				
			}
	}
	else
	{
		  $sql_exf="select c.po_break_down_id as po_id,d.country_ship_date as pub_shipment_date,
			(CASE WHEN  c.entry_form!=85 THEN e.production_qnty ELSE 0 END) as exf_qnty,
			(CASE WHEN  c.entry_form=85 THEN e.production_qnty ELSE 0 END) as ret_exf_qnty
			from  pro_ex_factory_mst c,pro_ex_factory_dtls e,wo_po_break_down b,wo_po_color_size_breakdown  d where c.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and e.mst_id=c.id and d.id=e.color_size_break_down_id and c.item_number_id=d.item_number_id and d.country_id=c.country_id and  b.status_active=1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted =0 and d.country_ship_date between  '$s_date' and '$e_date' $po_cond_for_in ";
			$sql_exf_res=sql_select($sql_exf);
			$exfact_qty_arr=array();
			foreach($sql_exf_res as $row)
			{
				$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
				$bal_exfact_qty=$row[csf('exf_qnty')]-$row[csf('ret_exf_qnty')];
				$po_wise_rate=$po_wise_rate_arr[$row[csf('po_id')]]['rate'];
				$po_buyer=$po_wise_rate_arr[$row[csf('po_id')]]['buyer'];
				$exfact_qty_arr[$date_key]['exfact_qty']+=$row[csf('exf_qnty')]-$row[csf('ret_exf_qnty')];
				$exfact_qty_arr[$date_key]['exfact_value']+=$bal_exfact_qty*$po_wise_rate;
				$buyer_exfact_qty_arr[$date_key][$po_buyer]['exfact_qty']+=$bal_exfact_qty;
				$buyer_exfact_qty_arr[$date_key][$po_buyer]['exfact_value']+=$bal_exfact_qty*$po_wise_rate;
				
			}
	}
	unset($sql_exf_res);
	
	//var_dump($plan_qty_arr);
	

	ob_start();	
	?>
	<div style="margin:0 auto; width:1500px;">
        <table width="1680" border="0" cellpadding="2" cellspacing="0"> 
            <thead>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                </tr>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:14px;">
                       <b><? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></b>
                    </td>
                </tr>
            </thead>
        </table>
        <div style="width:1680px; margin:5px;" id="scroll_body">
        <table cellspacing="0" width="1460" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="16"><b style="float:left">Monthly</b></th>
					<th align="center" colspan="4">Export status &nbsp;<? echo $s_date.'To '. $e_date ?></th>
               </tr>
                <tr>
                   <th width="70">Month</th>
                 
                  
                   <th width="60">Total MC/Man</th>
				    <th width="60">Line</th>
                   <th width="60">Working Hrs/Day</th>
                   <th width="60">Target Eff. %</th>
                   <th width="60">Working Day</th>
                  <!-- <th width="60">Capacity Mints [100 % Eff.]</th>-->
                   <th width="60">Factory Capacity PC</th>
                  <!-- <th width="60">Factory Capacity Mints</th>-->
                   <th width="60">Projected Qty (Pcs)</th>
                   <th width="60">Confirmed Qty (Pcs)</th>
				    <th width="60">Total Pcs (Proj + Conf)</th>
				   
				   <th width="60">Projected FOB  Value</th>
                   <th width="60">Confirmed FOB  Value</th>
				   
				  
				   <th width="60">Total FOB (Proj + Conf)</th>
				   
                  
                   <th width="80">Planned</th>
                   <th width="80">Balanced Planning</th>
                  
                   
                   <th width="60">Capacity Utilization %</th>
				   
				    <th width="80">Export Qty</th>
                   <th width="80">FOB Value</th> 
				   <th width="80">Balanced Export Qty</th>
				   <th width="">Balanced  FOB Value</th>
                  <!-- <th width="60">Over Booking</th>
                   <th>Less Booking %</th>-->
                   
                </tr>
            </thead>
            <tbody>
				 <? $i=1;$total_totalMechine=$total_no_of_line=$total_capacity_month_pcs=$total_projected_qty=$total_confirm_qty=$total_projectedPlusConfirmedQty=$total_projected_value= $total_confirm_value= $total_conf_proj_value= $total_plan_qty=$total_balance_planning=$total_exfact_qty=$total_exfact_value=$total_balance_exfact_qty=$total_balanced_fob_value=0;
                 foreach($month_arr as $year_month){
                    list($year,$month)=explode('-',$year_month);
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$tot_mc_val= $capacity_arr[$year_month]['tot_mc_val'];
                	$totalMechine=$tot_mc_val;//$capacity_arr[$year_month]['no_of_line']*$capacity_arr[$year_month]['avg_machine_line'];
                    $capacityMints=$totalMechine*$working_hour_arr[$year_month]*$capacity_arr[$year_month]['working_day']*60;
                    $less_over=$over_parcent=$less_parcent='';
					$less_over=$booked_parcent_arr[$year_month]-100;
					if($less_over>0){$over_parcent=number_format($less_over,2);}
					else{$less_parcent=number_format($less_over,2);}
					
					$location_ids=rtrim($location_id_arr[$year_month],',');
					
					
					$location_ids=array_unique(explode(",",$location_ids));
					$tot_location=count($location_ids);
					$projectedPlusConfirmedQty=array_sum($projected_qty_array[$year_month])+array_sum($confirm_qty_array[$year_month]);
					$tot_conf_proj_value=array_sum($confirm_value_array[$year_month])+array_sum($projected_value_array[$year_month]);
					
					$exfact_qty=$exfact_qty_arr[$year_month]['exfact_qty'];
					$exfact_value=$exfact_qty_arr[$year_month]['exfact_value'];
					$balance_exfact_qty=$projectedPlusConfirmedQty-$exfact_qty;
					$balanced_fob_value=$tot_conf_proj_value-$exfact_value;
					
					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="70"><b><? echo $months[$month*1].'-'.$year; ?></b></td>
                   
				    <td width="60" align="right"><? echo $totalMechine; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['no_of_line']; ?></td>
                   
                    <td width="60" align="right"><? echo $working_hour_arr[$year_month]; ?></td>
                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month]['efficency']/$tot_location,2,'.',''); ?></td>
                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month]['working_day']/$tot_location); ?></td>
                  <!--  <td width="60" align="right"><? //echo $capacityMints; ?></td>-->
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_pcs']; ?></td>
                   <!-- <td width="60" align="right"><? //echo $capacity_arr[$year_month]['capacity_month_min']; ?></td>-->
                    
                    <td width="60" align="right"><? echo array_sum($projected_qty_array[$year_month]); ?></td>
                    <td width="60" align="right"><? echo array_sum($confirm_qty_array[$year_month]); ?></td>
					 <td width="100" align="right">
					<? 
					
					echo number_format($projectedPlusConfirmedQty,0);
					?>
                     </td>
					
					<td width="60" align="right"><? echo number_format(array_sum($projected_value_array[$year_month]),2); ?></td>
                    <td width="60" align="right"><? echo number_format(array_sum($confirm_value_array[$year_month]),2); ?></td>
					
					<td width="60" align="right"><?
					
					 echo number_format($tot_conf_proj_value,0); ?></td>
					                    
                    <td width="80" align="right"><? echo $plan_qty_arr[$year_month]; ?></td>
                    <td width="80" align="right"><? echo array_sum($confirm_qty_array[$year_month])-$plan_qty_arr[$year_month]; ?></td>
                    <td width="60" align="right"><? echo number_format($booked_parcent_arr[$year_month],2); ?></td>
				
				   <td width="80" align="right"><? echo number_format($exfact_qty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($exfact_value,2); ?></td>
                     <td width="80" align="right">
					<? 
					
					echo number_format($balance_exfact_qty,0);
					 ?>
                    </td>
					 <td width="" align="right"><? echo number_format($balanced_fob_value,2); ?></td>
                   <!-- <td width="60" align="right"><? //echo number_format($over_parcent,2); ?></td>
                    <td align="right"><?// echo number_format($less_parcent,2); ?></td>-->
                
                </tr>
                <? $i++;
				$total_totalMechine+=$totalMechine;
				$total_no_of_line+=$capacity_arr[$year_month]['no_of_line'];
				$total_capacity_month_pcs+=$capacity_arr[$year_month]['capacity_month_pcs'];;
				$total_projected_qty+=array_sum($projected_qty_array[$year_month]);
				$total_confirm_qty+=array_sum($confirm_qty_array[$year_month]);
				
				$total_projectedPlusConfirmedQty+=$projectedPlusConfirmedQty;
				$total_projected_value+=array_sum($projected_value_array[$year_month]);
			    $total_confirm_value+=array_sum($confirm_value_array[$year_month]);
				 
				 $total_conf_proj_value+=$tot_conf_proj_value;
				 $total_plan_qty+=$plan_qty_arr[$year_month];
				 
				
				$total_balance_planning+=array_sum($confirm_qty_array[$year_month])-$plan_qty_arr[$year_month];
				$total_exfact_qty+=$exfact_qty;
				$total_exfact_value+=$exfact_value;
				$total_balance_exfact_qty+=$balance_exfact_qty;
				$total_balanced_fob_value+=$balanced_fob_value;
				} ?>
            </tbody>
			<tfoot>
			<tr>
			<th>Grand Total </th>
			<th><? echo number_format($total_totalMechine,0); ?> </th>
			<th><? echo number_format($total_no_of_line,0); ?> </th>
			<th colspan="3"><? //echo number_format($total_working_day,0); ?> </th>
			<th><? echo number_format($total_capacity_month_pcs,0); ?> </th>
			<th><? echo number_format($total_projected_qty,0); ?> </th>
			<th><? echo number_format($total_confirm_qty,0); ?> </th>
			<th><? echo number_format($total_projectedPlusConfirmedQty,0); ?> </th>
			<th><? echo number_format($total_projected_value,2); ?> </th>
			<th><? echo number_format($total_confirm_value,2); ?> </th>
			<th><? echo number_format($total_conf_proj_value,2); ?> </th>
			<th><? echo number_format($total_plan_qty,2); ?> </th>
			<th><? echo number_format($total_balance_planning,2); ?> </th>
			<th><? //echo number_format($total_balance_exfact_qty,2); ?> </th>
			<th><? echo number_format($total_exfact_qty,0); ?> </th>
			
			<th><? echo number_format($total_exfact_value,2); ?> </th>
			<th><? echo number_format($total_balance_exfact_qty,0); ?> </th>
			 <th align="right"><? echo number_format($total_balanced_fob_value,2); ?></th>
			</tr>
			</tfoot>
         </table>
         </div>
         
         <!--//Order information.............................................-->
         <br /><br />
        <div style="width:1490px;margin:5px;">
        <table cellspacing="0" width="1480" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		
					<th colspan="13"><b style="float:left">Buyer wise capacity booked summary</b></th>
					<th align="center" colspan="4">Export status &nbsp;<? echo $s_date.'To '. $e_date ?></th>
               </tr>
                <tr>
                   <th width="70">Month</th>
                   <th width="130">Buyer</th>
                   <th width="80">Projected Qty (Pcs)</th>
                   <th width="80">Confirmed Qty (Pcs)</th>
                   <th width="80">Total Pcs (Proj + Conf)</th>
                   <th width="80">Projected Value (USD)</th>
                   <th width="80">Confirmed Value (USD)</th>
				   <th width="80">Total FOB Value (Proj + Conf)</th>
                  
                   <th width="60">Avg. SMV</th>
                   <th width="80">Projected Mints</th>
                   <th width="80">Confirmed Mints</th>
				   <th width="80">Total Mints (Proj + Conf)</th>
				   <th width="60">Booked %</th>
					 
                   <th width="80">Export Qty</th>
				   <th width="80">FOB Value</th>
                   <th width="80">Balanced Export Qty</th>
                   <th width="">Balanced  FOB Value</th>
				   
					
                </tr>
            </thead>
            <tbody>
                     <? $i=1;
					 foreach($month_arr as $year_month){
						 $tr=0;
						list($year,$month)=explode('-',$year_month);
						
						$rowspan=count($order_data_array[$year_month])+2;
						
						$fn = "change_color('trb_".$i."','".$bgcolor."')";
						echo '
						<tr bgcolor="'.$bgcolor.'" id="trb_'.$i.'"  onclick="'.$fn.'">
							<td width="70" rowspan="'.$rowspan.'" valign="middle"><b>'.$months[$month*1].'-'.$year.'</b></td>';

						foreach($order_data_array[$year_month] as $buyer_id=>$confirm_qty){
							$bookedMints=$booked_smv_mints_arr[$year_month][$buyer_id];
							$projectedbookedMints=$projected_booked_smv_mints_arr[$year_month][$buyer_id];
							$project_qty= $projected_qty_array[$year_month][$buyer_id];
							
							//$avgSMV=$bookedMints/$confirm_qty;
							$avgSMV=($bookedMints+$projectedbookedMints)/($confirm_qty+$project_qty);
							//$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
							$bookedParcent=0;
							if($capacity_arr[$year_month]['capacity_month_min'])
							{
							$bookedParcent=(($bookedMints+$projectedbookedMints)/$capacity_arr[$year_month]['capacity_month_min'])*100;
							}

							
							$confirm_qty_arr[$year_month]+=$confirm_qty;
							$projected_qty_arr[$year_month]+=$project_qty;

							$confirm_value_arr[$year_month]+=$confirm_value_array[$year_month][$buyer_id];
							$projected_value_arr[$year_month]+=$projected_value_array[$year_month][$buyer_id];

							$booked_mints_arr[$year_month]+=$bookedMints;
							$projected_booked_mints_arr[$year_month]+=$projectedbookedMints;
							
							$avg_smg_arr[$year_month]+=$avgSMV;
							//$booked_parcent_arr[$year_month]+=$bookedParcent;
							
							$buyer_exfact_qty=$buyer_exfact_qty_arr[$year_month][$buyer_id]['exfact_qty'];
							$buyer_exfact_value=$buyer_exfact_qty_arr[$year_month][$buyer_id]['exfact_value'];
							
							//$buyer_exfact_qty_mon_arr[$year_month]+=$buyer_exfact_qty;
							//$buyer_exfact_value_mon_arr[$year_month]+=$buyer_exfact_value;
							
							$tot_po_qty_pcs=$project_qty+$confirm_qty;
							$tot_conf_proj_val=$projected_value_array[$year_month][$buyer_id]+$confirm_value_array[$year_month][$buyer_id];
							
							
							$tot_balance_exfact_qty=$tot_po_qty_pcs-$buyer_exfact_qty;
							$tot_balance_exfact_value=$tot_conf_proj_val-$buyer_exfact_value;
							
							$buyer_exfact_qty_mon_arr[$year_month]+=$buyer_exfact_qty;
							$buyer_exfact_value_mon_arr[$year_month]+=$buyer_exfact_value;
							
							$buyer_tot_balance_exfact_qty_arr[$year_month]+=$tot_balance_exfact_qty;
							$buyer_tot_balance_exfact_val_arr[$year_month]+=$tot_balance_exfact_value;
							
						 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						 $fn = "change_color('trb_".$i."','".$bgcolor."')";
						 
						
						 
						 if($tr!=0){echo '<tr bgcolor="'.$bgcolor.'" id="trb_'.$i.'"  onclick="'.$fn.'">';}
						 ?>
                        <td width="130"><? echo $buyer_arr[$buyer_id]; ?></td>
                        <td width="80" align="right"><? echo $project_qty; ?></td>
                        <td width="80" align="right"><? echo $confirm_qty; ?></td>
                        <td width="80" align="right"><? echo $tot_po_qty_pcs; ?></td>
                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id],2); ?></td>
                        <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$buyer_id],2); ?></td>
						<td width="80" align="right"><? 
						
						echo number_format($tot_conf_proj_val,2); ?></td>
						
                       
                        <td width="60" align="right"><? echo number_format($avgSMV,2); ?></td>
                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
						<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
                        <td width="60" align="right"><? echo number_format($bookedParcent,2); ?></td>
						
						<td width="80" align="right"><? echo number_format($buyer_exfact_qty,0); ?></td>
						<td width="80" align="right"><? echo number_format($buyer_exfact_value,2); ?></td>
						<td width="80" align="right"><? echo number_format($tot_balance_exfact_qty,0); ?></td>
						<td width="" align="right"><? echo number_format($tot_balance_exfact_value,2); ?></td>
						
                    </tr>
                    <? $tr++;$i++;} ?>
                    <tr bgcolor="#DDD">
                        <th>Total:</th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_value_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_value_arr[$year_month],0); ?></th>
						
						<th align="right"><? echo number_format($projected_value_arr[$year_month]+$confirm_value_arr[$year_month],0); ?></th>
						
                       
                        <th align="right"><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></td>
                         <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
						 <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>
						 <th align="right"><? echo number_format($buyer_exfact_qty_mon_arr[$year_month],0); ?></th>
						  <th align="right"><? echo number_format($buyer_exfact_value_mon_arr[$year_month],2); ?></th>
						   <th align="right"><? echo number_format($buyer_tot_balance_exfact_qty_arr[$year_month],0); ?></th>
						    <th align="right"><? echo number_format($buyer_tot_balance_exfact_val_arr[$year_month],2); ?></th>
                    </tr>
                    
                    <tr bgcolor="#FFFF00">
                        <th colspan="11">
                        	<? 
								$less_over=$booked_parcent_arr[$year_month]-100;
								if($less_over>0){echo "Capacity booked Over %";}
								else{echo "Capacity booked Less %";}
							?>
                            
                        </th>
                        <th align="right"><? echo number_format($less_over,2); ?></th>
						<th align="right"><? //echo number_format($less_over,2); ?></th>
						<th align="right"><? //echo number_format($less_over,2); ?></th>
						<th align="right"><? //echo number_format($less_over,2); ?></th>
						<th align="right"><? //echo number_format($less_over,2); ?></th>
                    </tr>
                    
                    
                    
					<?
					
					} ?>
            </tbody>
         </table>
         </div>
         
         
         
	</div>
	<?
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
if($action=="item_report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_name=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$cbo_year_name=str_replace("'","",$cbo_year_start);
	$cbo_month=str_replace("'","",$cbo_month_start);
	$cbo_end_year_name=str_replace("'","",$cbo_year_end);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_date_cat_id=str_replace("'","",$cbo_date_cat_id);
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
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
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_id='$location_id'";
	

	
	$working_hour_arr=array();
	$sql_fina_param=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where company_id in($cbo_company_name) and status_active=1 and is_deleted=0 order by applying_period_date");
	foreach( $sql_fina_param as $rowf)
	{
		$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
		$working_hour_arr[$date_key]=$rowf[csf("working_hour")];
	}
	unset($sql_fina_param);

	 $sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name and c.month_id between $cbo_month and $cbo_month_end group by a.comapny_id,a.location_id,a.year,a.effi_percent, a.basic_smv,a.avg_machine_line,b.no_of_line,c.working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs"; //echo $sql;
	$sql_data_smv=sql_select($sql);
	$tot_row_data_smv=count($sql_data_smv);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
		$capacity_arr[$date_key]['efficency']+=$row[csf("effi_percent")];
		$capacity_arr[$date_key]['basic_smv']+=$row[csf("basic_smv")];
		$capacity_arr[$date_key]['avg_machine_line']+=$row[csf("avg_machine_line")];
		$capacity_arr[$date_key]['working_day']+=$row[csf("working_day")];
		$capacity_arr[$date_key]['no_of_line']+=$row[csf("no_of_line")];
		$capacity_arr[$date_key]['tot_mc_val']+=$row[csf("no_of_line")]*$row[csf("avg_machine_line")];
		$capacity_arr[$date_key]['capacity_month_min']+=$row[csf("capacity_month_min")];
		$capacity_arr[$date_key]['capacity_month_pcs']+=$row[csf("capacity_month_pcs")];
		$location_id_arr[$date_key].=$row[csf("location_id")].',';
	}
	unset($sql_data_smv);
	
	//var_dump($capacity_arr);die;
	
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
	
	$condition= new condition();
	$condition->company_name("in($cbo_company_name)");
	 if(str_replace("'","",$cbo_date_cat_id) ==2 && str_replace("'","",$cbo_month_end)!=''){
		  $condition->country_ship_date(" between '$s_date' and '$e_date'");
	 }
	else if(str_replace("'","",$cbo_date_cat_id) ==1 && str_replace("'","",$cbo_month_end)!=''){
		 //$condition->country_ship_date(" between '$start_date' and '$end_date'");
		  $condition->pub_shipment_date(" between '$s_date' and '$e_date'");
	 }
			 
			
			  $condition->init();
			  
			$other= new other($condition);
			//echo $other->getQuery(); die;
			$other_costing_arr=$other->getAmountArray_by_orderAndGmtsitem();
			//print_r($other_costing_arr);
			$conversion= new conversion($condition);
			$conversion_costing_arr=$conversion->getAmountArray_by_orderAndGmtsitem();
			//print_r($conversion_costing_arr);
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_orderAndGmtsitem();
			
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish();
			//	print_r($fabric_costing_arr);
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndGmtsitem();
			
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndGmtsitem();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_orderAndGmtsitem();
			 
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndGmtsitem();
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderAndGmtsItemWiseYarnAmountArray();
			
	
			
	
			$sql="select a.id  as quotation_id, a.gmts_item_id, a.company_id, a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom, a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.price_with_commn_dzn, b.total_cost, b.costing_per_id from wo_price_quotation a, wo_price_quotation_costing_mst b  where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND a.company_id in($cbo_company_name) and a.offer_qnty>0  AND a.est_ship_date between '$s_date' and '$e_date' order  by a.id ";
			$sql_quot_result=sql_select($sql);
			$all_quot_id="";
			foreach($sql_quot_result as $row)
			{
				$date_key=date("Y-m",strtotime($row[csf("est_ship_date")]));
				
				if($all_quot_id=="") $all_quot_id=$row[csf("quotation_id")]; else $all_quot_id.=",".$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref")]]['costing_per']=$row[csf("costing_per")];
				$style_wise_arr[$row[csf("style_ref")]]['gmts_item_id']=$row[csf("gmts_item_id")];
				$style_wise_arr[$row[csf("style_ref")]]['shipment_date'].=$row[csf('est_ship_date')].',';
			
				$style_wise_arr[$row[csf("style_ref")]]['buyer_name']=$row[csf("buyer_id")];
				$offer_qnty_pcs=$row[csf('offer_qnty')]*$row[csf('ratio')];
				
				
				
				$order_data_array[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]+=0;
				$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['offer_qnty']+=$row[csf("offer_qnty")];
				$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['offer_value']+=$row[csf("offer_qnty")]*$row[csf("price_with_commn_pcs")];
				
				
				
				$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['offer_qnty']+=$row[csf("offer_qnty")];
				$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['price_with_commn_dzn']=$row[csf("offer_qnty")]*$row[csf("price_with_commn_pcs")];
				$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
				$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['costing_per_id']=$row[csf("costing_per_id")];
			}
			//echo "<pre>";
			//print_r($order_data_array_not_quot_arr);
	
			if($cbo_date_cat_id==1)
			{
			 /* $sql="SELECT a.job_no,a.set_smv,a.set_break_down,a.quotation_id,a.buyer_name,a.total_set_qnty,b.id as po_id, b.pub_shipment_date,b.unit_price,c.item_number_id,
			(c.order_quantity/a.total_set_qnty) as po_quantity,
			(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
			(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
			(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
			(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
			FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
			WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 order by b.pub_shipment_date ";*/
			 $sql="SELECT a.job_no,a.set_smv,a.set_break_down,a.quotation_id,a.buyer_name,a.total_set_qnty,b.id as po_id, b.pub_shipment_date,b.unit_price,a.gmts_item_id as item_number_id,
			(b.po_quantity/a.total_set_qnty) as po_quantity,
			(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
			(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
			(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
			(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
			FROM wo_po_details_master a, wo_po_break_down b
			WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   order by b.pub_shipment_date ";
			}
			else //Country Ship Date
			{
				$sql="SELECT a.job_no,a.set_smv,a.set_break_down,a.quotation_id,a.buyer_name,a.total_set_qnty,b.id as po_id, c.country_ship_date as pub_shipment_date,b.unit_price,c.item_number_id,
			(c.order_quantity/a.total_set_qnty) as po_quantity,
			(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
			(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
			(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
			(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
			FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
			WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $locatin_cond AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 order by b.pub_shipment_date";
			}
	
		//echo $sql;
		
		$sql_data=sql_select($sql);
		//foreach($month_arr as $year_month)
		//{
				
				
		foreach( $sql_data as $row)
		{
		
			$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
			//if($date_key==$year_month)
				//{
			$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];
			
			$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
			if($cbo_date_cat_id==1)
			{
				//foreach($set_break_down_arr as $set_break_down){
				//list($item_ids,$set,$smv)=explode('_',$set_break_down);
				$item_id=$row[csf('item_number_id')];
				
				$test_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['lab_test'];
				$freight_cost= $other_costing_arr[$row[csf('po_id')]][$item_id]['freight'];
				$inspection=$other_costing_arr[$row[csf('po_id')]][$item_id]['inspection'];
				$certificate_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['certificate_pre_cost'];
				$common_oh=$other_costing_arr[$row[csf('po_id')]][$item_id]['common_oh'];
				$currier_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['currier_pre_cost'];
				//if($other_costing_arr[$row[csf('po_id')]][$item_id]['cm_cost']>0)
				//{
				$cm_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['cm_cost'];
				//}
				//else
				//{
					//$cm_cost=0;
				//}
				
				$cm_value_order_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['cm_cost']+=$cm_cost;
				
				
				$tot_other_cost=$test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost;
				$conversion_cost=array_sum($conversion_costing_arr[$row[csf('po_id')]][$item_id]);
				$trim_cost=$trims_costing_arr[$row[csf('po_id')]][$item_id];
			
				$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$item_id]);
				$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);
				
				$emblishment_cost=$emblishment_costing_arr[$row[csf('po_id')]][$item_id]+$emblishment_costing_arr_name_wash[$row[csf('po_id')]][$item_id];
				$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]][$item_id];
				$commission_cost=$commission_costing_arr[$row[csf('po_id')]][$item_id];
				$yarn_cost=$yarn_costing_arr[$row[csf('po_id')]][$item_id];
				//$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);
				//echo $tot_other_cost.',';
				$total_cost=$conversion_cost+$trim_cost+$fab_purchase_knit+$fab_purchase_woven+$emblishment_cost+$commercial_cost+$yarn_cost+$commission_cost+$tot_other_cost;
					
				$confirm_qty=$row[csf('confirm_qty')];
				$project_qty=$row[csf('projected_qty')];
				
				$confirm_value=$row[csf('confirm_value')];
				$project_value=$row[csf('projected_value')];
				
				$po_qty_set_smv=$row[csf('set_smv')]*$row[csf('po_quantity')];
				$smv=$po_qty_set_smv/$row[csf('po_quantity')];
				//$item_buyer=$row[csf('item_number_id')].'_'.$row[csf('buyer_name')];
				
				$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$confirm_qty;
				//$order_data_array2[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
				$projected_order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$project_qty;
				$total_cost_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['total_cost']+=$total_cost;
				
				
				
				
				
				
				//$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]][$row[csf("quotation_id")]];
				
				if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty']>0)
				{
					$quotation_data_arr[$date_key][$item_id][$row[csf('buyer_name')]]['quoted_qty']=$quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty'];
				}
				if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn']>0)
				{
					$quotation_data_arr[$date_key][$item_id][$row[csf('buyer_name')]]['quoted_val']=$quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn'];	
				}
				
				
				
				
				
				
				
				$job_no_array[$date_key][$item_id][$row[csf('buyer_name')]]['job'].=$row[csf('job_no')].',';
			
				$confirm_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$confirm_qty;
				$projected_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$project_qty;
				
				$summary_confirm_qty_array[$date_key]+=$confirm_qty;
				$summary_projected_qty_array[$date_key]+=$project_qty;
				
				$confirm_value_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$rate_in_pcs*$confirm_qty;
				$projected_value_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$rate_in_pcs*$project_qty;
				
				$summary_confirm_value_array[$date_key]+=$rate_in_pcs*$confirm_qty;
				$summary_projected_value_array[$date_key]+=$rate_in_pcs*$project_qty;
				
				
				$booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
				$projected_booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]+=($smv*$project_qty);
				
				$summary_booked_smv_mints_arr[$date_key]+=($smv*$confirm_qty);
				$summary_projected_booked_smv_mints_arr[$date_key]+=($smv*$project_qty);
				
				//$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
				//$projected_booked_parcent_arr[$date_key]+=(($smv*$project_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
				$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;
	
				//}
				
			}
			else
			{
				//$item_id=$set_break_down_arr[0];
				$set=$set_break_down_arr[1];
				$smv=$set_break_down_arr[2];
				
				$confirm_qty=$row[csf('confirm_qty')];
				$project_qty=$row[csf('projected_qty')];
				$confirm_value=$row[csf('confirm_value')];
				$project_value=$row[csf('projected_value')];
				$item_id=$row[csf('item_number_id')];
				
				$test_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['lab_test'];
				$freight_cost= $other_costing_arr[$row[csf('po_id')]][$item_id]['freight'];
				$inspection=$other_costing_arr[$row[csf('po_id')]][$item_id]['inspection'];
				$certificate_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['certificate_pre_cost'];
				$common_oh=$other_costing_arr[$row[csf('po_id')]][$item_id]['common_oh'];
				$currier_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['currier_pre_cost'];
				
				$cm_cost=$other_costing_arr[$row[csf('po_id')]][$item_id]['cm_cost'];
				$cm_value_order_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['cm_cost']=$cm_cost;
				
				$tot_other_cost=$test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost;
				$conversion_cost=array_sum($conversion_costing_arr[$row[csf('po_id')]][$item_id]);
				$trim_cost=$trims_costing_arr[$row[csf('po_id')]][$item_id];
			
				$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$item_id]);
				$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);
				
				$emblishment_cost=$emblishment_costing_arr[$row[csf('po_id')]][$item_id]+$emblishment_costing_arr_name_wash[$row[csf('po_id')]][$item_id];
				$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]][$item_id];
				$commission_cost=$commission_costing_arr[$row[csf('po_id')]][$item_id];
				$yarn_cost=$yarn_costing_arr[$row[csf('po_id')]][$item_id];
				//$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);
				//echo $tot_other_cost.',';
				$total_cost=$conversion_cost+$trim_cost+$fab_purchase_knit+$fab_purchase_woven+$emblishment_cost+$commercial_cost+$yarn_cost+$commission_cost+$tot_other_cost;
				$total_cost_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['total_cost']=$total_cost;
				
				$po_qty_set_smv=$row[csf('set_smv')]*$row[csf('po_quantity')];
				$smv=$po_qty_set_smv/$row[csf('po_quantity')];
				//$set_smv_array[$date_key][$row[csf('buyer_name')]]['set_smv']+=$po_qty_set_smv/$row[csf('po_quantity')];
				
				//echo $smv.', ';
				$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$confirm_qty;
				$projected_order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$project_qty;
				
				$confirm_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$confirm_qty;
				$projected_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$project_qty;
				
				if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty'] >0)
				{
					$quotation_data_arr[$date_key][$item_id][$row[csf("buyer_name")]]['quoted_qty']=$quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty'];
				}
				if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn'] >0)
				{
					$quotation_data_arr[$date_key][$item_id][$row[csf('buyer_name')]]['quoted_val']=$quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn'];
				}
			
				
				$summary_confirm_qty_array[$date_key]+=$confirm_qty;
				$summary_projected_qty_array[$date_key]+=$project_qty;
				$confirm_value_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
				$projected_value_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;
				
				$summary_confirm_value_array[$date_key]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
				$summary_projected_value_array[$date_key]+=$project_value;//$rate_in_pcs*$project_qty;
				
				$booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
				$projected_booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]+=($smv*$project_qty);
				$summary_booked_smv_mints_arr[$date_key]+=($smv*$confirm_qty);
				$summary_projected_booked_smv_mints_arr[$date_key]+=($smv*$project_qty);
				
				$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;
	
			}
			
			//}
		}
	//}
	//print_r($summary_confirm_qty_array).'d';
	 	foreach($order_data_array as $year_month=>$item_data)
		{
			$mon_row_span=0;
			 foreach($item_data as $item_id=>$buyer_data)
			 {
				$item_row_span=0;
				foreach($buyer_data as $buyer_id=>$confirm_qty)
				{
					$mon_row_span++;
					$item_row_span++;
				}
				$item_rowspan_arr[$year_month][$item_id]=$item_row_span;
				$mon_rowspan_arr[$year_month]=$mon_row_span;
				
			 }
					
		}
		
	/*echo $set_smv="select a.buyer_name,b.pub_shipment_date,d.gmts_item_id,d.smv_set from wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details d where  a.job_no=b.job_no_mst and a.job_no=d.job_no and  b.job_no_mst=d.job_no and b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and a.company_name in($cbo_company_name) $locatin_cond group by a.buyer_name,b.pub_shipment_date,d.gmts_item_id,d.smv_set";
	$set_smv_result=sql_select($set_smv);
	foreach($set_smv_result as $row)
	{
	$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
	$set_smv_array[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_name")]]=$row)[csf("smv_set")];
	}
	unset($set_smv_result);*/
	
	
	 // var_dump($order_data_array);
	
	$sql_data="select pd.plan_qnty as pdplan_qnty,b.pub_shipment_date from wo_po_break_down b,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  b.id=c.po_break_down_id and pd.plan_id=c.plan_id and c.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between  '$s_date' and '$e_date'";
	$data_result=sql_select($sql_data);
	$plan_qty_arr=array();
	foreach( $data_result as $row)
	{
		$plan_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]+=$row[csf("pdplan_qnty")];
	}
	
		
		
	
	//var_dump($plan_qty_arr);
	

	ob_start();	
	?>
	<div style="margin:0 auto; width:1500px; margin-left:10px;">
        <table width="1680" border="0" cellpadding="2" cellspacing="0"> 
            <thead>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                </tr>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:14px;">
                       <b><? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></b>
                    </td>
                </tr>
            </thead>
        </table>
        <div style="width:1680px;" id="scroll_body">
        <table cellspacing="0" width="1660" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="23">Monthly Capacity Information</th>
               </tr>
                <tr>
                   <th width="70">Month</th>
                 
                  
                   <th width="60">Total MC/Man</th>
				    <th width="60">Line</th>
                   <th width="60">Working Hrs/Day</th>
                   <th width="60">Target Eff. %</th>
                   <th width="60">Working Day</th>
                   <th width="60">Capacity Mints [100 % Eff.]</th>
                   <th width="60">Factory Capacity PC</th>
                   <th width="60">Factory Capacity Mints</th>
                   <th width="60">Projected Qty (Pcs)</th>
                   <th width="60">Confirmed Qty (Pcs)</th>
				    <th width="60">Total Pcs (Proj + Conf)</th>
				   
				   <th width="60">Projected FOB  Value</th>
                   <th width="60">Confirmed FOB  Value</th>
				   
				  
				   <th width="60">Total FOB (Proj + Conf)</th>
				   
                   <th width="60">Projected Mints</th>
                   <th width="60">Confirmed Mints</th>
				   <th width="100">Total Mints (Proj + Conf)</th>
                   <th width="80">Planned</th>
                   <th width="80">Balanced Planning</th>
                   
                   <th width="60">Capacity Utilization %</th>
                   <th width="60">Over Booking</th>
                   <th>Less Booking %</th>
                   
                </tr>
            </thead>
            <tbody>
				 <? $i=1;
                 foreach($month_arr as $year_month){
                    list($year,$month)=explode('-',$year_month);
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$tot_mc_val= $capacity_arr[$year_month]['tot_mc_val'];
                	$totalMechine=$tot_mc_val;//$capacity_arr[$year_month]['no_of_line']*$capacity_arr[$year_month]['avg_machine_line'];
                    $capacityMints=$totalMechine*$working_hour_arr[$year_month]*$capacity_arr[$year_month]['working_day']*60;
                    $less_over=$over_parcent=$less_parcent='';
					$less_over=$booked_parcent_arr[$year_month]-100;
					if($less_over>0){$over_parcent=number_format($less_over,2);}
					else{$less_parcent=number_format($less_over,2);}
					
					$location_ids=rtrim($location_id_arr[$year_month],',');
					
					$location_ids=array_unique(explode(",",$location_ids));
					$tot_location=count($location_ids);
					
					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="70"><b><? echo $months[$month*1].'-'.$year; ?></b></td>
                   
				    <td width="60" align="right"><? echo $totalMechine; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['no_of_line']; ?></td>
                   
                    <td width="60" align="right"><? echo $working_hour_arr[$year_month]; ?></td>
                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month]['efficency']/$tot_location,2); ?></td>
                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month]['working_day']/$tot_location); ?></td>
                    <td width="60" align="right"><? echo $capacityMints; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_pcs']; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_min']; ?></td>
                    
                    <td width="60" align="right"><? echo $summary_projected_qty_array[$year_month]; ?></td>
                    <td width="60" align="right"><? echo $summary_confirm_qty_array[$year_month]; ?></td>
					 <td width="100" align="right">
					<? 
					$projectedPlusConfirmedQty=$summary_projected_qty_array[$year_month]+$summary_confirm_qty_array[$year_month];
					echo number_format($projectedPlusConfirmedQty,2);
					?>
                     </td>
					
					<td width="60" align="right"><? echo number_format($summary_projected_value_array[$year_month],0); ?></td>
                    <td width="60" align="right"><? echo number_format($summary_confirm_value_array[$year_month],0); ?></td>
					
					<td width="60" align="right"><?
					$tot_conf_proj_value=$summary_confirm_value_array[$year_month]+$summary_projected_value_array[$year_month];
					 echo number_format($tot_conf_proj_value,0); ?></td>
					
					
                    <td width="60" align="right"><? echo number_format($summary_projected_booked_smv_mints_arr[$year_month],2); ?></td>
                    <td width="60" align="right"><? echo number_format($summary_booked_smv_mints_arr[$year_month],2); ?></td>
                     <td width="60" align="right">
					<? 
					$projectedPlusConfirmedMint=$summary_projected_booked_smv_mints_arr[$year_month]+$summary_booked_smv_mints_arr[$year_month];
					echo number_format($projectedPlusConfirmedMint,2);
					 ?>
                    </td>
                    
                    <td width="80" align="right"><? echo $plan_qty_arr[$year_month]; ?></td>
                    <td width="80" align="right"><? echo $summary_confirm_qty_array[$year_month]-$plan_qty_arr[$year_month]; ?></td>
                    
                    
                    <td width="60" align="right"><? echo number_format($booked_parcent_arr[$year_month],2); ?></td>
                    <td width="60" align="right"><? echo number_format($over_parcent,2); ?></td>
                    <td align="right"><? echo number_format($less_parcent,2); ?></td>
                
                </tr>
                <? $i++;} ?>
            </tbody>
         </table>
         </div>
         
         <!--//Order information.............................................-->
         <br /><br />
        <div style="width:1500px;">
        <table cellspacing="0" width="1490" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="18">Item and Buyer Wise Capacity Cooked Summary</th>
               </tr>
                <tr>
                   <th width="70">Month</th>
				   <th width="100">Item</th>
                   <th width="130">Buyer</th>
				   <th width="80">Quoted Qty</th>
                   <th width="80">Projected Qty (Pcs)</th>
                   <th width="80">Confirmed Qty (Pcs)</th>
                   <th width="80">Total Pcs (Proj + Conf)</th>
				   <th width="80">Quoted Value</th>
					 
                   <th width="80">Projected Value (USD)</th>
                   <th width="80">Confirmed Value (USD)</th>
				   
				   <th width="80">Total FOB Value (Proj + Conf)</th>
					  
                  
                   <th width="60">Avg. SMV</th>
                   <th width="80">Projected Mints</th>
                   <th width="80">Confirmed Mints</th>
				    <th width="80">Total Mints (Proj + Conf)</th>
                   <th width="80">Booked %</th>
				    <th width="70">CM %</th>
					<th>RM %</th>
                </tr>
            </thead>
            <tbody>
                     <? $i=1;
					 foreach($order_data_array as $year_month=>$item_data)
					 {
						
						list($year,$month)=explode('-',$year_month);
						$m=1;
						foreach($item_data as $item_id=>$buyer_data)
						{
							$n=1;
							foreach($buyer_data as $buyer_id=>$confirm_qty)
							{
							
								
							$bookedMints			= $booked_smv_mints_arr[$year_month][$item_id][$buyer_id];
							$projectedbookedMints	= $projected_booked_smv_mints_arr[$year_month][$item_id][$buyer_id];
							$project_qty			= $projected_qty_array[$year_month][$item_id][$buyer_id];
							
							$quoted_qty				= $quotation_data_arr[$year_month][$item_id][$buyer_id]['quoted_qty'];
							$quoted_val				= $quotation_data_arr[$year_month][$item_id][$buyer_id]['quoted_val'];
							
							
							
							
							
							$buyer_quot_offer_qty	= $order_data_array_not_quot_arr[$year_month][$item_id][$buyer_id]['offer_qnty'];
							$buyer_quot_offer_value	= $order_data_array_not_quot_arr[$year_month][$item_id][$buyer_id]['offer_value'];
							
							if($quoted_qty!=0) 
							{
								$quoted_qty	= $quoted_qty; 
								$quoted_val	= $quoted_val; 
							}
							else 
							{
								$quoted_qty	= $buyer_quot_offer_qty;
								$quoted_val	= $buyer_quot_offer_value;
							}
							
							$quoted_qty_bal	= ($quoted_qty-$confirm_qty);
							
							
							
							
							
							$job_no=rtrim($job_no_array[$year_month][$item_id][$buyer_id]['job'],',');
							$job_nos=implode(",",array_unique(explode(",",$job_no)));
							
							//echo $tot_pre_cost;
							//$total_pre_cost+=$tot_pre_cost;
							//$avgSMV=$bookedMints/$confirm_qty;
							$avgSMV=($bookedMints+$projectedbookedMints)/($confirm_qty+$project_qty);
							//$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
							$bookedParcent=0;
							if($capacity_arr[$year_month]['capacity_month_min'])
							{
							$bookedParcent=(($bookedMints+$projectedbookedMints)/$capacity_arr[$year_month]['capacity_month_min'])*100;
							}

							
							$confirm_qty_arr[$year_month]+=$confirm_qty;
							$projected_qty_arr[$year_month]+=$project_qty;
							
							//$quoted_qty_arr[$year_month]+=$quoted_qty;
							//$quoted_val_arr[$year_month]+=$quoted_val;
							
							
							
							if($quoted_qty_bal>0){
								$quoted_qty_arr[$year_month]+=$quoted_qty_bal;
								$quoted_val_arr[$year_month]+=$quoted_val;
							}
							
							
							
							
							
							$confirm_value_arr[$year_month]+=$confirm_value_array[$year_month][$item_id][$buyer_id];
							$projected_value_arr[$year_month]+=$projected_value_array[$year_month][$item_id][$buyer_id];

							$booked_mints_arr[$year_month]+=$bookedMints;
							$projected_booked_mints_arr[$year_month]+=$projectedbookedMints;
							
							$avg_smg_arr[$year_month]+=$avgSMV;
							//$booked_parcent_arr[$year_month]+=$bookedParcent;
							$mon_rowspan=$mon_rowspan_arr[$year_month];
							$item_rowspan=$item_rowspan_arr[$year_month][$item_id];
							
							$tot_conf_proj_val=$projected_value_array[$year_month][$item_id][$buyer_id]+$confirm_value_array[$year_month][$item_id][$buyer_id];
							
							$cm_value=$cm_value_order_item_array[$year_month][$item_id][$buyer_id]['cm_cost'];
							$cm_value_percent=($cm_value/$tot_conf_proj_val)*100;
							
							$tot_pre_cost=$total_cost_item_array[$year_month][$item_id][$buyer_id]['total_cost'];
							$rm_value_percent=($tot_pre_cost/$tot_conf_proj_val)*100;
							
							$totla_cm_val_arr[$year_month]+=$cm_value;
							$total_cost_rm_arr[$year_month]+=$tot_pre_cost;
							
							
							
							 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 ?>
							 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr2_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $i; ?>" style="font-size:13px">
							 <?
							 if($m==1)
							 {
							 ?>
							<td width="70" valign="middle" rowspan="<? echo $mon_rowspan; ?>"><? echo  $months[$month*1].'-'.$year; ?></td>
							<?
							 }
							 if($n==1)
							 {
							?>
							<td width="100" rowspan="<? echo $item_rowspan; ?>"><? echo $garments_item[$item_id]; ?></td>
							<?
							 }
							?>
							<td width="130" title="<? echo $job_nos;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
							<td width="80" align="right"><? 
							//echo number_format($quoted_qty,0); 
							if($quoted_qty_bal>0){
								echo number_format($quoted_qty_bal,0); 
							}else{
								echo "0";
							}
							//echo number_format($quoted_qty_bal,0);
							?></td>
							<td width="80" align="right"><? echo number_format($project_qty,0); ?></td>
							<td width="80" align="right"><? echo number_format($confirm_qty,0); ?></td>
							<td width="80" align="right"><? echo number_format($project_qty+$confirm_qty,0); ?></td>
							<td width="80" align="right"><? 
							//echo number_format($quoted_val,2);
							if($quoted_qty_bal>0){
								echo number_format($quoted_val,2); 
							}else{
								echo number_format(0,2); 
							} 
							
							?></td>
							 
							<td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$item_id][$buyer_id],2); ?></td>
							<td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$item_id][$buyer_id],2); ?></td>
							<td width="80" align="right"><? 
							
							echo number_format($tot_conf_proj_val,2); ?></td>
							
						   
							<td width="60" align="right"><? echo number_format($avgSMV,2); ?></td>
							<td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
							<td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
							<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
							<td width="80" align="right"><? echo number_format($bookedParcent,2); ?></td>
							<td width="70" title="(TotalCM/FOB Value*100)<? echo 'Total CM Value='.$cm_value;?>" align="right"><? echo number_format($cm_value_percent,2); ?></td>
							<td width="" align="right" title="(Total Cost-CM/FOB Value*100)<? echo 'Total Cost='.$tot_pre_cost;?>"><? echo number_format($rm_value_percent,2); ?></td>
							 
						</tr>
                    	<? 
							$m++; $n++;$i++;
						}
					} ?>
                    <tr bgcolor="#DDD">
                        <th colspan="3">Total:</th>
						<th align="right"><? echo number_format($quoted_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
						<th align="right"><? echo number_format($quoted_val_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($projected_value_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($confirm_value_arr[$year_month],2); ?></th>
						
						<th align="right"><? echo number_format($projected_value_arr[$year_month]+$confirm_value_arr[$year_month],0); ?></th>
						
                       
                        <th align="right"><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></td>
                         <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
						 <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>
						  <th title="" align="right" <? //if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><?
						  $tot_cm_percent=($totla_cm_val_arr[$year_month]/($projected_value_arr[$year_month]+$confirm_value_arr[$year_month]))*100;
						    $tot_rm_percent=($total_cost_rm_arr[$year_month]/($projected_value_arr[$year_month]+$confirm_value_arr[$year_month]))*100;
						  
						   echo number_format($tot_cm_percent,2); ?></th>
						   <th title="" align="right" <? //if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($tot_rm_percent,2); ?></th>
                    </tr>
                    
                    <tr bgcolor="#FFFF00">
                        <th colspan="15">
                        	<? 
								$less_over=$booked_parcent_arr[$year_month]-100;
								if($less_over>0){echo "Capacity booked Over %";}
								else{echo "Capacity booked Less %";}
							?>
                            
                        </th>
                        <th align="right"><? echo number_format($less_over,2); ?></th>
						<th align="right"><? //echo number_format($less_over,2); ?></th>
						<th align="right"><? //echo number_format($less_over,2); ?></th>
                    </tr>
					<?
					
					} ?>
            </tbody>
         </table>
         </div>
         
         
         
	</div>
	<?
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
				$sql_data_smv=sql_select("select a.id, a.comapny_id, a.year, a.avg_machine_line, a.basic_smv, a.effi_percent, c.month_id, c.capacity_month_min, c.working_day from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id='$company_id' $locatin_cond  and a.year='$ex_month[0]' and c.month_id='$monthId'" );
				
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
                        <td align="right"><? echo number_format($capacity_arr[$ex_month[0]][$monthId]['basic_smv'],0,'.',','); ?></td>
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
	?>
    <script>
		var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["td_ordQty","td_ordQtyPcs","td_setQty","td_eqvBasicQty","td_unitPrice","td_ordValue"],
					col: [5,7,8,9,10,11],
					operation: ["sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
    </script>
	<fieldset style="width:920px; margin-left:3px">
		<div>
			<table border="1" class="rpt_table" rules="all" width="920" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="13"><? echo $string_dtls; ?></th>
                    </tr>
                    <tr>
                        <th width="25">Sl</th>
                        <th width="80">Buyer</th>
                        <th width="80">Style</th>
                        <th width="70">Ship Date</th>
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
           <div style="max-height:300px; overflow-y:scroll; width:920px" id="scroll_body">
        	<table cellspacing="0" border="1" class="rpt_table" width="900px" rules="all" id="tbl_body" >
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
				
				$order_sql="select a.buyer_name, a.style_ref_no, a.set_smv, a.order_uom, a.total_set_qnty, a.team_leader, b.pub_shipment_date, sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as order_value from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND a.company_name='$company_id' $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and b.is_confirmed='$type' group by a.buyer_name, a.style_ref_no, a.set_smv, a.order_uom, a.total_set_qnty, a.team_leader, b.pub_shipment_date";
				$order_sql_res=sql_select($order_sql); $i=1;
				foreach($order_sql_res as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$ord_qty_pcs=0; $unit_price=0; $eqv_basic_qty=0; 
					$ord_qty_pcs=$row[csf("po_quantity")]*$row[csf("total_set_qnty")];
					$unit_price=$row[csf("po_quantity")]/$row[csf("order_value")];
					
					$eqv_basic_qty=$ord_qty_pcs*($row[csf("set_smv")]/$capacity_arr[$ex_month[0]][$monthId]['basic_smv']);
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<td width="25"><? echo $i; ?></td>
                        <td width="80"><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
                        <td width="80"><? echo $row[csf("style_ref_no")]; ?></td>
                        <td width="70"><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
                        <td width="60" align="right"><? echo $row[csf("set_smv")]; ?></td>
                        <td width="80" align="right"><? echo number_format($row[csf("po_quantity")],0); ?></td>
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
            <table width="920px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                <tfoot>
                    <tr>
                        <th width="25">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
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