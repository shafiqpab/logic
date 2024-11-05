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

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}




if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

if($action=="print_button_variable_setting")
{
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in(".$data.") and module_id=11 and report_id=70 and is_deleted=0 and status_active=1","format_id","format_id");
	echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit();	
}

if($action=="report_generate_backup")
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

	$cbo_company_arr=explode (",", $cbo_company_name);
	$total_selected_company = count($cbo_company_arr);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	//$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

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

	$sql="select id, company_id, buyer_id, quot_date, order_qty from  wo_price_quotation_v3_mst where status_active=1 and is_deleted =0 AND company_id in($cbo_company_name) AND quot_date between '$s_date' and '$e_date'";
	$sql_quot_result=sql_select($sql);
	foreach($sql_quot_result as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("quot_date")]));
		$quot_wise_arr[$date_key][$row[csf("buyer_id")]]['offer_qnty']+=$row[csf("order_qty")];
		$order_data_array[$date_key][$row[csf('buyer_id')]]+=0;
	}
	unset($sql_quot_result);

	$sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c
	 where a.id=b.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond
	 and a.year between $cbo_year_name and $cbo_end_year_name and b.mst_id=c.mst_id and c.month_id between 1 and 12 and a.is_deleted=0 and a.status_active=1 group by a.comapny_id, a.location_id, a.year, a.effi_percent, a.basic_smv, a.avg_machine_line, b.no_of_line, c.working_day, c.month_id, c.capacity_month_min, c.capacity_month_pcs";
	 //and   c.month_id between $cbo_month and $cbo_month_end
	 //and b.date_calc between '$s_date' and '$e_date'
	// echo $sql;
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
	//echo "<pre>";
	//print_r($capacity_arr);die;
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
	else if($cbo_date_cat_id==3)
	{
	$sql="SELECT a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.shipment_date as pub_shipment_date,b.unit_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $locatin_cond AND b.shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
		{
			foreach($set_break_down_arr as $set_break_down)
			{
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1  || $cbo_date_cat_id==3)
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
				//$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;
				$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
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
			//$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
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

	//echo $sql_data;
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
					$tot_mc_val = $capacity_arr[$year_month]['tot_mc_val'];
                	$totalMechine=$tot_mc_val;//$capacity_arr[$year_month]['no_of_line']*$capacity_arr[$year_month]['avg_machine_line'];
                    $capacityMints=$totalMechine*$working_hour_arr[$year_month]*$capacity_arr[$year_month]['working_day']*60;
                    $less_over=$over_parcent=$less_parcent='';
					$less_over=$booked_parcent_arr[$year_month]-100;
					if($less_over>0){$over_parcent=number_format($less_over,2);}
					else{$less_parcent=number_format($less_over,2);}

					$location_ids=rtrim($location_id_arr[$year_month],',');


					$location_ids=array_unique(explode(",",$location_ids));
					$tot_location=count($location_ids);
					//echo $capacity_arr[$year_month]['efficency']
					$total_eff = $capacity_arr[$year_month]['efficency']/$tot_location;

					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="70"><b><? echo $months[$month*1].'-'.$year; ?></b></td>

				    <td width="60" align="right"><? echo $totalMechine; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['no_of_line']; ?></td>

                    <td width="60" align="right"><? echo $working_hour_arr[$year_month]; ?></td>
                    <td width="60" align="right"><? echo $total_eff/$total_selected_company ?></td>
                    <td width="60" align="right"><? echo number_format(($capacity_arr[$year_month]['working_day']/$tot_location)/$total_selected_company); ?></td>
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
        <div style="width:1280px;">
        <table cellspacing="0" width="1260" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
                <tr>
                	<th colspan="14">Buyer wise capacity booked summary</th>
                </tr>
                <tr>
                    <th width="70">Month</th>
                    <th width="130">Buyer</th>
                    <th width="100">Quotation Qty</th>
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

						arsort($order_data_array[$year_month]);

						foreach($order_data_array[$year_month] as $buyer_id=>$confirm_qty)
						{
							$bookedMints	= $booked_smv_mints_arr[$year_month][$buyer_id];
							$projectedbookedMints=$projected_booked_smv_mints_arr[$year_month][$buyer_id];
							$project_qty	= $projected_qty_array[$year_month][$buyer_id];

							$quoted_qty		= $quot_wise_arr[$year_month][$buyer_id]['offer_qnty'];

							//$avgSMV=$bookedMints/$confirm_qty;
							$avgSMV=($bookedMints+$projectedbookedMints) / (($confirm_qty+$project_qty)>0?($confirm_qty+$project_qty):1);
							//$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
							$bookedParcent=0;
							if($capacity_arr[$year_month]['capacity_month_min'])
							{
								//$bookedParcent=(($bookedMints+$projectedbookedMints)/$capacity_arr[$year_month]['capacity_month_min'])*100;
								$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
								$bookedParcentFormula = "(Confirmed Mints / Factory Capacity Mints) * 100";
							}

							$confirm_qty_arr[$year_month]+=$confirm_qty;
							$projected_qty_arr[$year_month]+=$project_qty;

							$quoted_qty_arr[$year_month]+=$quoted_qty;

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
                        <td width="100" align="right"><? echo number_format($quoted_qty,0); ?></td>
                        <td width="80" align="right"><? echo $project_qty; ?></td>
                        <td width="80" align="right"><? echo $confirm_qty; ?></td>
                        <td width="80" align="right"><? echo $project_qty+$confirm_qty; ?></td>
                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id],2); ?></td>
                        <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$buyer_id],2); ?></td>
						<td width="80" align="right"><?
						$tot_conf_proj_val=$projected_value_array[$year_month][$buyer_id]+$confirm_value_array[$year_month][$buyer_id];
						echo number_format($tot_conf_proj_val,2); ?></td>


                        <td width="60" align="right" title="<? echo "(".$bookedMints."+".$projectedbookedMints.")/(".$confirm_qty."+".$project_qty.")"; ?>"><? echo number_format($avgSMV,2); ?></td>
                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
						<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
                        <td align="right" title="<? echo $bookedParcentFormula; ?>"><? echo number_format($bookedParcent,2); ?></td>
                    </tr>
                    <?
					$tr++;$i++;
					}
					?>
                    <tr bgcolor="#DDD">

                        <th>Total:</th>
                        <th align="right"><? echo number_format($quoted_qty_arr[$year_month],0); ?></th>
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
                        <th colspan="12">
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

	$cbo_company_arr=explode (",", $cbo_company_name);
	$total_selected_company = count($cbo_company_arr);

	$conver_to_million=str_replace ("'","",$conver_to_million);
	$cbo_buyer_id=str_replace ("'","",$cbo_buyer_id);



	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	//$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");


	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;

	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	$tot_month = datediff( 'm', $s_date,$e_date);


	foreach ($cbo_company_arr as $company_id) {
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($s_date,$i);
			$month_arr[date("Y",strtotime($next_month))][date("m",strtotime($next_month))][$company_id]=date("Y-m",strtotime($next_month)).'-'.$company_id;
		}
	}
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_id='$location_id'";
	if($cbo_buyer_id!=''){$buyer_cond=" AND buyer_id in($cbo_buyer_id)";}

	$working_hour_arr=array();
	$sql_fina_param=sql_select("select company_id, applying_period_date, working_hour from lib_standard_cm_entry where company_id in($cbo_company_name) and status_active=1 and is_deleted=0 order by applying_period_date");
	foreach( $sql_fina_param as $rowf)
	{
		$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")])).'-'.$rowf[csf("company_id")];
		//$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
		$working_hour_arr[$date_key]=$rowf[csf("working_hour")];
	}
	unset($sql_fina_param);




	$sql="select id, company_id, buyer_id, quot_date, order_qty from  wo_price_quotation_v3_mst where status_active=1 and is_deleted =0 AND company_id in($cbo_company_name) $buyer_cond AND quot_date between '$s_date' and '$e_date'";
	$sql_quot_result=sql_select($sql);
	foreach($sql_quot_result as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("quot_date")])).'-'.$row[csf("company_id")];
		//$date_key=date("Y-m",strtotime($row[csf("quot_date")]));
		$quot_wise_arr[$date_key][$row[csf("buyer_id")]]['offer_qnty']+=$row[csf("order_qty")];
		$order_data_array[$date_key][$row[csf('buyer_id')]]+=0;
	}
	unset($sql_quot_result);


	$sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c
	 where a.id=b.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond
	 and a.year between $cbo_year_name and $cbo_end_year_name and b.mst_id=c.mst_id and c.month_id between 1 and 12 and a.is_deleted=0 and a.status_active=1 group by a.comapny_id, a.location_id, a.year, a.effi_percent, a.basic_smv, a.avg_machine_line, b.no_of_line, c.working_day, c.month_id, c.capacity_month_min, c.capacity_month_pcs";
	 //and   c.month_id between $cbo_month and $cbo_month_end
	 //and b.date_calc between '$s_date' and '$e_date'
	//echo $sql;
	$sql_data_smv=sql_select($sql);
	$tot_row_data_smv=count($sql_data_smv);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")])).'-'.$row[csf("comapny_id")];
		//$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
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
	/*echo "<pre>";
	print_r($capacity_arr);die;*/
	//var_dump($capacity_arr);die;

	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
	if($cbo_buyer_id!=''){$buyer_cond=" AND a.buyer_name in($cbo_buyer_id)";}
	//$conver_to_million
	
	if($cbo_date_cat_id==1)
	{
	$sql="SELECT a.company_name, a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.pub_shipment_date,b.unit_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else if($cbo_date_cat_id==3)
	{
	$sql="SELECT a.company_name, a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.shipment_date as pub_shipment_date,b.unit_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else //Country Ship Date
	{
		$sql="SELECT a.company_name,a.job_no,a.set_smv,a.set_break_down,a.buyer_name,a.total_set_qnty, c.country_ship_date as pub_shipment_date,b.unit_price,
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

		$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")])).'-'.$row[csf("company_name")];
		$order_data_array[$year_month]['rowspan']+=1;
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];

		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
		{
			foreach($set_break_down_arr as $set_break_down)
			{
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1  || $cbo_date_cat_id==3)
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
				//$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;
				$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
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
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);

			$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
		}


	}

	  //var_dump($order_data_array['2017-01']);

	$sql_data="select c.company_id, pd.plan_qnty as pdplan_qnty,b.pub_shipment_date from wo_po_break_down b,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  b.id=c.po_break_down_id and pd.plan_id=c.plan_id and c.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between  '$s_date' and '$e_date'";
	
/*	$sql_data="SELECT c.company_id, a.plan_qnty as pdplan_qnty,b.pub_shipment_date
  FROM wo_po_break_down b,
       ppl_sewing_plan_board_dtls pd,
       ppl_sewing_plan_board_powise a,
       ppl_sewing_plan_board c
 WHERE b.id = c.po_break_down_id
       AND pd.plan_id = c.plan_id 
	   and c.plan_id=a.plan_id
       AND c.company_id  in($cbo_company_name)
       AND b.status_active = 1
       AND b.is_deleted = 0
       AND c.status_active = 1
       AND c.is_deleted = 0
       AND b.pub_shipment_date BETWEEN '$s_date' and '$e_date'  ";	*/
	
	$data_result=sql_select($sql_data);
	$plan_qty_arr=array();
	foreach( $data_result as $row)
	{
		$plan_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")])).'-'.$row[csf("company_id")]]+=$row[csf("pdplan_qnty")];
	}
	//var_dump($plan_qty_arr);
	$sqlProduction="select b.company_id, a.pub_shipment_date , sum(b.production_quantity) as production_quantity from wo_po_break_down a,pro_garments_production_mst b 
    where a.id=b.po_break_down_id and b.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.company_id in($cbo_company_name) and a.pub_shipment_date between '$s_date' and '$e_date'
    group by b.company_id, a.pub_shipment_date";
	$sqlProductionResult=sql_select($sqlProduction);
	$productionDataArr=array();
	foreach($sqlProductionResult as $row)
	{ 
		 $prod_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")])).'-'.$row[csf("company_id")]]+=$row[csf("production_quantity")];
	}
	

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
                	<? $i=0;
                	$comma = '';
                	foreach ($cbo_company_arr as $key => $company_id) {
                		if($i != 0) {$comma = " And " ;} else {$comma = " " ;}
                		$company_full_name .= $comma.$companyArr[$company_id];
                		$i++;
                	}
                	echo '<b>'.$company_full_name.'</b>';
                   		?>
                	</td>
                </tr>
            </thead>
        </table>
        <div style="width:1840px;" id="scroll_body">
        <table cellspacing="0" width="1820" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="26">Monthly Capacity Information</th>
               </tr>
                <tr>
                	<th width="100">Company</th>
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
                   <th width="80">Total Sewing Production Qty</th>
                   <th width="80">Balance Sewing Qty</th>


                   <th width="60">Capacity Utilization %</th>
                   <th width="60">Over Booking</th>
                   <th>Less Booking %</th>

                </tr>
            </thead>
            <tbody>
				 <? $i=1;/*
				 echo '<pre>';
				 print_r($month_arr);*/
				 		$gtotal_machine='';
                 		$gtotal_line='';
                 		$gtotal_capacity_mints='';
                 		$gtotal_factory_capacity_pc ='';
                 		$gtotal_factory_capacity_mints = '';
                 		$gtotal_projected_qty = '';
                 		$gtotal_confirmed_qty = '';
                 		$gtotal_total_pcs ='';
                 		$gtotal_projected_fob_value = '';
                 		$gtotal_confirmed_fob_value = '';
                 		$gtotal_total_fob = '';
                 		$gtotal_projected_mints = '';
                 		$gtotal_confirmed_mints = '';
                 		$gtotal_total_mints = '';
                 		$gtotal_Planned = '';	
						$gtotal_sewing_out_prod_qty = '';	$gtotal_sewing_balance_qty = '';
                 		$gtotal_balanced_planning = '';

                 foreach($month_arr as $year=>$year_data){
                 	foreach ($year_data as $month => $company_data) {
                 		$total_machine='';
                 		$total_line='';
                 		$total_capacity_mints='';
                 		$total_factory_capacity_pc ='';
                 		$total_factory_capacity_mints = '';
                 		$total_projected_qty = '';
                 		$total_confirmed_qty = '';
                 		$total_total_pcs ='';
                 		$total_projected_fob_value = '';
                 		$total_confirmed_fob_value = '';
                 		$total_total_fob = '';
                 		$total_projected_mints = '';
                 		$total_confirmed_mints = '';
                 		$total_total_mints = '';$total_balance_sewing_qty='';$total_sewing_out_prod_qty='';
                 		$total_Planned = '';
                 		$total_balanced_planning = '';
                 		foreach ($company_data as $company => $year_month_company) {
                 			list($year,$month,$company)=explode('-',$year_month_company);
		                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$tot_mc_val = $capacity_arr[$year_month_company]['tot_mc_val'];
		                	$totalMechine=$tot_mc_val;
		                    //$capacityMints=$totalMechine*$working_hour_arr[$year_month_company]*$capacity_arr[$year_month_company]['working_day']*60;
		                    $less_over=$over_parcent=$less_parcent='';
							$less_over=$booked_parcent_arr[$year_month_company]-100;
							if($less_over>0){$over_parcent=number_format($less_over,2);}
							else{$less_parcent=number_format($less_over,2);}

							$location_ids=rtrim($location_id_arr[$year_month_company],',');

							$location_ids=array_unique(explode(",",$location_ids));
							$tot_location=count($location_ids);
							$total_eff = $capacity_arr[$year_month_company]['efficency']/$tot_location;

							$total_machine +=$totalMechine;
							$total_line +=$capacity_arr[$year_month_company]['no_of_line'];
							$total_capacity_mints += $capacityMints;
							$total_factory_capacity_pc += $capacity_arr[$year_month_company]['capacity_month_pcs'];
							$total_factory_capacity_mints += $capacity_arr[$year_month_company]['capacity_month_min'];
							$total_projected_qty += array_sum($projected_qty_array[$year_month_company]);
							$total_confirmed_qty += array_sum($confirm_qty_array[$year_month_company]);
							$total_projected_fob_value += array_sum($projected_value_array[$year_month_company]);
							$total_confirmed_fob_value += array_sum($confirm_value_array[$year_month_company]);
							$total_projected_mints += array_sum($projected_booked_smv_mints_arr[$year_month_company]);
							$total_confirmed_mints += array_sum($booked_smv_mints_arr[$year_month_company]);
							$total_Planned += $plan_qty_arr[$year_month_company];
							$total_balanced_planning += array_sum($confirm_qty_array[$year_month_company])-$plan_qty_arr[$year_month_company];
		                    $capacityMints=$totalMechine*$working_hour_arr[$year_month_company]*number_format($capacity_arr[$year_month_company]['working_day']/$tot_location,2)*60;
							$sewing_out_prod_qty=$prod_qty_arr[$year_month_company];
							

							?>
		                	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                	<td width="70"><? echo $companyArr[$company]?></td>
			                    <td width="70"><? echo $months[$month*1].'-'.$year; ?></td>

							    <td width="60" align="right"><? echo $totalMechine;   ?></td>
			                    <td width="60" align="right"><? echo $capacity_arr[$year_month_company]['no_of_line'];  ?></td>

			                    <td width="60" align="right"><? echo $working_hour_arr[$year_month_company]; ?></td>
			                    <td width="60" align="right"><? echo number_format($total_eff,2) ?></td>
			                    <td width="60" align="right"><? echo number_format(($capacity_arr[$year_month_company]['working_day']/$tot_location),2); ?></td>
			                    <td width="60" align="right"><? echo  $capacityMints; ?></td>
			                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month_company]['capacity_month_pcs'],2); ?></td>
			                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month_company]['capacity_month_min'],2); ?></td>

			                    <td width="60" align="right"><? echo number_format(array_sum($projected_qty_array[$year_month_company]),2); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($confirm_qty_array[$year_month_company]),2); ?></td>
								 <td width="100" align="right">
								<?
								$projectedPlusConfirmedQty=array_sum($projected_qty_array[$year_month_company])+array_sum($confirm_qty_array[$year_month_company]);
								$total_total_pcs += $projectedPlusConfirmedQty;
								echo number_format($projectedPlusConfirmedQty,2);
								?>
			                     </td>
								<td width="60" align="right"><? echo number_format(array_sum($projected_value_array[$year_month_company])/$conver_to_million,4); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($confirm_value_array[$year_month_company])/$conver_to_million,4); ?></td>
								<td width="60" align="right"><?
								$tot_conf_proj_value=array_sum($confirm_value_array[$year_month_company])+array_sum($projected_value_array[$year_month_company]);
								$total_total_fob += $tot_conf_proj_value;
								 echo number_format($tot_conf_proj_value/$conver_to_million,4); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($projected_booked_smv_mints_arr[$year_month_company]),2); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($booked_smv_mints_arr[$year_month_company]),2); ?></td>
			                     <td width="60" align="right">
								<?
								$projectedPlusConfirmedMint=array_sum($projected_booked_smv_mints_arr[$year_month_company])+array_sum($booked_smv_mints_arr[$year_month_company]);
								$total_total_mints += $projectedPlusConfirmedMint;
								echo number_format($projectedPlusConfirmedMint,2);
								 ?>
			                    </td>
			                    <td width="80" align="right"><? echo $plan_qty_arr[$year_month_company]; ?></td>
			                    <td width="80" align="right"><? echo array_sum($confirm_qty_array[$year_month_company])-$plan_qty_arr[$year_month_company]; ?></td>
                                <td width="80" align="right"><? echo number_format($sewing_out_prod_qty,2); ?></td>
                                <td width="80" align="right" title="Tot Po Qty-Sewing Out"><? $balance_sewing_qty=$projectedPlusConfirmedQty-$sewing_out_prod_qty;
								echo number_format($balance_sewing_qty,2);
								$total_balance_sewing_qty+=$balance_sewing_qty;
								$total_sewing_out_prod_qty+=$sewing_out_prod_qty;
								 ?></td>
                                
			                    <td width="60" align="right"><? echo number_format($booked_parcent_arr[$year_month_company],2); ?></td>
			                    <td width="60" align="right"><? echo number_format($over_parcent,2); ?></td>
			                    <td align="right"><? echo number_format($less_parcent,2); ?></td>

		                	</tr>
		                	<? $i++;
		                	$totalMonth = count($year_data);
                 		}
                 			$gtotal_machine += $total_machine;
		                	$gtotal_line += $total_line;
		                	$gtotal_capacity_mints += $total_capacity_mints;
		                	$gtotal_factory_capacity_pc += $total_factory_capacity_pc;
		                	$gtotal_factory_capacity_mints += $total_factory_capacity_mints;
		                	$gtotal_projected_qty += $total_projected_qty;
		                	$gtotal_confirmed_qty += $total_confirmed_qty;
		                	$gtotal_total_pcs += $total_total_pcs;
		                	$gtotal_projected_fob_value += $total_projected_fob_value;
		                	$gtotal_confirmed_fob_value += $total_confirmed_fob_value;
		                	$gtotal_total_fob += $total_total_fob;
		                	$gtotal_projected_mints += $total_projected_mints;
		                	$gtotal_confirmed_mints += $total_confirmed_mints;
		                	$gtotal_total_mints += $total_total_mints;
		                	$gtotal_Planned += $total_Planned;
		                	$gtotal_balanced_planning += $total_balanced_planning;
							
							$gtotal_sewing_balance_qty += $balance_sewing_qty;
							$gtotal_sewing_out_prod_qty += $sewing_out_prod_qty;
							
                 		if(($totalMonth > 1 && $total_selected_company > 1) || ($totalMonth >= 1 && $total_selected_company >= 2) || ($totalMonth >= 2 && $total_selected_company >= 2)){ ?>
                 		<tr>
		                    <td width="140" colspan="2" align="right"><strong>Month Total</strong></td>
						    <td width="60" align="right"><strong><? echo $total_machine;  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo $total_line;  ?></strong></td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right"><strong><? echo number_format($total_capacity_mints,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_factory_capacity_pc,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_factory_capacity_mints,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_projected_qty,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_confirmed_qty,2);  ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($total_total_pcs,2);  ?></strong></td>
							<td width="60" align="right"><strong><? echo number_format($total_projected_fob_value/$conver_to_million,4);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_confirmed_fob_value/$conver_to_million,4);  ?></strong></td>
							<td width="60" align="right"><strong><? echo number_format($total_total_fob/$conver_to_million,4);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_projected_mints,0);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_confirmed_mints,0);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo $total_total_mints;  ?></strong></td>
		                    <td width="80" align="right"><strong><? echo $total_Planned;  ?></strong></td>
		                    <td width="80" align="right"><strong><? echo $total_balanced_planning;  ?></strong></td>
                             <td width="80" align="right"><strong><? echo number_format($total_sewing_out_prod_qty,2);  ?></strong></td>
                             <td width="80" align="right"><strong><? echo number_format($total_balance_sewing_qty,2);  ?></strong></td>
		                    <td width="60" align="right"></td>
		                    <td width="60" align="right"></td>
		                    <td align="right"></td>
                 		</tr>
                 		<?
                 		}
                 	}
            	}
            	if($totalMonth > 1){
            ?>
            			<tr>
		                    <td width="140" colspan="2" align="right"><strong>Grand Total</strong></td>
						    <td width="60" align="right"><strong><? echo $gtotal_machine ?></strong></td>
		                    <td width="60" align="right"><strong><? echo $gtotal_line ?></strong></td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_capacity_mints,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_factory_capacity_pc,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_factory_capacity_mints,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_projected_qty,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_confirmed_qty,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($gtotal_total_pcs,2); ?></strong></td>
							<td width="60" align="right"><strong><? echo number_format($gtotal_projected_fob_value/$conver_to_million,4); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_confirmed_fob_value/$conver_to_million,4) ?></strong></td>
							<td width="60" align="right"><strong><? echo number_format($gtotal_total_fob/$conver_to_million,4) ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_projected_mints,0) ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_confirmed_mints,0) ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_total_mints,2) ?></strong></td>
		                    <td width="80" align="right"><strong><? echo number_format($gtotal_Planned,2) ?></strong></td>
		                    <td width="80" align="right"><strong><? echo number_format($gtotal_balanced_planning,2) ?></strong></td>
                            <td width="80" align="right"><strong><? echo number_format($gtotal_sewing_out_prod_qty,2); ?></strong></td>
                            <td width="80" align="right"><strong><? echo number_format($gtotal_sewing_balance_qty,2); ?></strong></td>
		                    <td width="60" align="right"></td>
		                    <td width="60" align="right"></td>
		                    <td align="right"></td>
                 		</tr>
                 		<? }
                 		?>
            </tbody>
         </table>
         </div>

         <!--//Order information.............................................-->
         <br /><br />
        <div style="width:1280px;">
        <table cellspacing="0" width="1260" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
                <tr>
                	<th colspan="15">Buyer wise capacity booked summary</th>
                </tr>
                <tr>
                	<th width="70">Company</th>
                    <th width="70">Month</th>
                    <th width="130">Buyer</th>
                    <th width="100">Quotation Qty</th>
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
                    <?
                    $gtotal_quotation_qty = '';
			 		$gtotal_projected_qty = '';
			 		$gtotal_confirmed_qty = '';
			 		$gtotal_total_pcs = '';
			 		$gtotal_projected_value = '';
			 		$gtotal_confirmed_value = '';
			 		$gtotal_Total_fob_value = '';
			 		$gtotal_projected_mints = '';
			 		$gtotal_confirmed_mints = '';
			 		$gtotal_total_mints = '';
                    $i=1;
					 foreach($month_arr as $year => $year_data){
					 	foreach ($year_data as $month => $company_data) {
					 		$total_quotation_qty = '';
					 		$total_projected_qty = '';
					 		$total_confirmed_qty = '';
					 		$total_total_pcs = '';
					 		$total_projected_value = '';
					 		$total_confirmed_value = '';
					 		$total_Total_fob_value = '';
					 		$total_projected_mints = '';
					 		$total_confirmed_mints = '';
					 		$total_total_mints = '';

					 		foreach ($company_data as $company => $year_month) {
					 			$tr=0;
								list($year,$month,$company)=explode('-',$year_month);
								if(count($order_data_array[$year_month]) != 0){
									$rowspan=count($order_data_array[$year_month])+2;
								}
								else{
									$rowspan =3;
								}


								$fn = "change_color('trb_".$i."','".$bgcolor."')";
								echo '
								<tr bgcolor="'.$bgcolor.'" id="trb_'.$i.'"  onclick="'.$fn.'">
									<td width="100" rowspan="'.$rowspan.'" valign="middle">'.$companyArr[$company].'</td><td width="70" rowspan="'.$rowspan.'" valign="middle">'.$months[$month*1].'-'.$year.'</td>';

								arsort($order_data_array[$year_month]);
							if(count($order_data_array[$year_month])>0){
								foreach($order_data_array[$year_month] as $buyer_id=>$confirm_qty)
								{
									$bookedMints	= $booked_smv_mints_arr[$year_month][$buyer_id];
									$projectedbookedMints=$projected_booked_smv_mints_arr[$year_month][$buyer_id];
									$project_qty	= $projected_qty_array[$year_month][$buyer_id];

									$quoted_qty		= $quot_wise_arr[$year_month][$buyer_id]['offer_qnty'];

									//$avgSMV=$bookedMints/$confirm_qty;
									$avgSMV=($bookedMints+$projectedbookedMints) / (($confirm_qty+$project_qty)>0?($confirm_qty+$project_qty):1);
									//$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
									$bookedParcent=0;
									if($capacity_arr[$year_month]['capacity_month_min'])
									{
										//$bookedParcent=(($bookedMints+$projectedbookedMints)/$capacity_arr[$year_month]['capacity_month_min'])*100;
										$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
										$bookedParcentFormula = "(Confirmed Mints / Factory Capacity Mints) * 100";
									}

									$confirm_qty_arr[$year_month]+=$confirm_qty;
									$projected_qty_arr[$year_month]+=$project_qty;

									$quoted_qty_arr[$year_month]+=$quoted_qty;

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
			                        <td width="100" align="right"><? echo number_format($quoted_qty,0); ?></td>
			                        <td width="80" align="right"><? echo $project_qty; ?></td>
			                        <td width="80" align="right"><? echo $confirm_qty; ?></td>
			                        <td width="80" align="right"><? echo $project_qty+$confirm_qty; ?></td>
			                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id]/$conver_to_million,4); ?></td>
			                        <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$buyer_id]/$conver_to_million,4); ?></td>
									<td width="80" align="right"><?
									$tot_conf_proj_val=$projected_value_array[$year_month][$buyer_id]+$confirm_value_array[$year_month][$buyer_id];
									echo number_format($tot_conf_proj_val/$conver_to_million,4); ?></td>


			                        <td width="60" align="right" title="<? echo "(".$bookedMints."+".$projectedbookedMints.")/(".$confirm_qty."+".$project_qty.")"; ?>"><? echo number_format($avgSMV,2); ?></td>
			                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
			                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
									<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
			                        <td align="right" title="<? echo $bookedParcentFormula; ?>"><? echo number_format($bookedParcent,2); ?></td>
			                    </tr>
			                    <?
								$tr++;
								$i++;
								}
							}
							else{
								echo "<td colspan='13'></></tr>";
							}
							$total_quotation_qty += $quoted_qty_arr[$year_month];
							$total_projected_qty += $projected_qty_arr[$year_month];
							$total_confirmed_qty += $confirm_qty_arr[$year_month];
					 		$total_total_pcs += $projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month];
					 		$total_projected_value += $projected_value_arr[$year_month];
					 		$total_confirmed_value += $confirm_value_arr[$year_month];
					 		$total_Total_fob_value += $projected_value_arr[$year_month]+$confirm_value_arr[$year_month];

					 		$total_projected_mints += $projected_booked_mints_arr[$year_month];
					 		$total_confirmed_mints += $booked_mints_arr[$year_month];
					 		$total_total_mints += $projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month];
							?>
		                    <tr bgcolor="#DDD">

		                        <th>Total:</th>
		                        <th align="right"><? echo number_format($quoted_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($projected_value_arr[$year_month]/$conver_to_million,4); ?></th>
		                        <th align="right"><? echo number_format($confirm_value_arr[$year_month]/$conver_to_million,4); ?></th>

                                <th align="right"><? echo number_format(($projected_value_arr[$year_month]+$confirm_value_arr[$year_month])/$conver_to_million,4); ?></th>


		                        <th align="right"><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></td>
		                         <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
		                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
								 <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
		                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>
		                    </tr>

		                    <tr bgcolor="#FFFF00">
		                        <th colspan="12">
		                        	<?
										$less_over=$booked_parcent_arr[$year_month]-100;
										if($less_over>0){echo "Capacity booked Over %";}
										else{echo "Capacity booked Less %";}
									?>

		                        </th>
		                        <th align="right"><? echo number_format($less_over,2); ?></th>
		                    </tr>
							<?
							$totalMonth = count($year_data);
					 		}
					 		$gtotal_quotation_qty += $total_quotation_qty;
							$gtotal_projected_qty += $total_projected_qty;
							$gtotal_confirmed_qty += $total_confirmed_qty;
					 		$gtotal_total_pcs += $total_total_pcs;
					 		$gtotal_projected_value += $total_projected_value;
					 		$gtotal_confirmed_value += $total_confirmed_value;
					 		$gtotal_Total_fob_value += $total_Total_fob_value;
					 		$gtotal_projected_mints += $total_projected_mints;
					 		$gtotal_confirmed_mints += $total_confirmed_mints;
					 		$gtotal_total_mints += $total_total_mints;
					 		if(($totalMonth > 1 && $total_selected_company > 1) || ($totalMonth >= 1 && $total_selected_company >= 2) || ($totalMonth >= 2 && $total_selected_company >= 2)){
					 		?>
					 		<tr style="outline: thin solid">
		                        <th colspan="3" align="right">Month Total</th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_quotation_qty,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_projected_qty,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_confirmed_qty,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_total_pcs,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_projected_value/$conver_to_million,4); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_confirmed_value/$conver_to_million,4); ?></th>
								<th align="center" style="outline: thin solid"><? echo number_format($total_Total_fob_value/$conver_to_million,4); ?></th>
		                        <th align="center" style="outline: thin solid"></td>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_projected_mints,2); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_confirmed_mints,2); ?></th>
								<th align="center" style="outline: thin solid"><? echo number_format($total_total_mints,2); ?></th>
		                        <th align="center" style="outline: thin solid"></th>
		                    </tr>
					 		<?
					 		}
					 	}
					}
					if($totalMonth > 1){
					?>
					<tr style="outline: thin solid">
                        <th colspan="3" align="right" style="outline: thin solid">Grand Total</th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_quotation_qty,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_projected_qty,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_confirmed_qty,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_total_pcs,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_projected_value/$conver_to_million,4); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_confirmed_value/$conver_to_million,4); ?></th>
						<th align="center" style="outline: thin solid"><? echo number_format($gtotal_Total_fob_value/$conver_to_million,4); ?></th>
                        <th align="center" style="outline: thin solid"> </td>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_projected_mints,2); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_confirmed_mints,2); ?></th>
						<th align="center" style="outline: thin solid"><? echo number_format($gtotal_total_mints,2); ?></th>
                        <th align="center" style="outline: thin solid"></th>
                    </tr>
                    <? } ?>
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

	$cbo_company_arr=explode (",", $cbo_company_name);
    $total_selected_company = count($cbo_company_arr);
	$conver_to_million=str_replace ("'","",$conver_to_million);


	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	//$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

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

	 $sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond  and a.year between $cbo_year_name and $cbo_end_year_name  and c.month_id between 1 and 12 and a.is_deleted =0 and status_active=1 group by a.comapny_id,a.location_id,a.year,a.effi_percent, a.basic_smv,a.avg_machine_line,b.no_of_line,c.working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs"; //echo $sql;
	 // and c.month_id between $cbo_month and $cbo_month_end
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
	if(str_replace("'","",$cbo_buyer_id)!=''){$buyer_cond=" AND a.buyer_name in($cbo_buyer_id)";}

	//echo $buyer_cond;die;


	if($cbo_date_cat_id==1)
	{
		$sql="SELECT a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.pub_shipment_date,b.unit_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,b.id as po_id,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $locatin_cond  $buyer_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else if($cbo_date_cat_id==3)
	{
		$sql="SELECT a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.shipment_date as pub_shipment_date, b.unit_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,b.id as po_id,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $locatin_cond $buyer_cond AND b.shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
		WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $locatin_cond $buyer_cond AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
			{
				foreach($set_break_down_arr as $set_break_down){
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
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

			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);
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

			//echo $smv.', ';
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;


			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);

			$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;

		}


	}

	 $tot_po_ids=implode(',',array_unique(explode(',',$allpo_ids)));

	  $poIds=chop($tot_po_ids,',');$po_cond_for_in="";
	 $po_ids=count(array_unique(explode(",",$poIds)));
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
	else if($cbo_date_cat_id==3)
	{
		$sql_exf="select c.po_break_down_id as po_id,b.shipment_date,
		(CASE WHEN  c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as exf_qnty,
		(CASE WHEN  c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ret_exf_qnty
		from  pro_ex_factory_mst c,wo_po_break_down b where b.id=c.po_break_down_id and  b.status_active=1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted =0 and b.shipment_date between  '$s_date' and '$e_date' $po_cond_for_in";
		$sql_exf_res=sql_select($sql_exf);
		foreach($sql_exf_res as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("shipment_date")]));
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
			//echo $exfact_qty_arr['2019-08']['exfact_qty'].'DDD'.$exfact_qty;;
					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="70"><b><? echo $months[$month*1].'-'.$year; ?></b></td>

				    <td width="60" align="right"><? echo $totalMechine; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['no_of_line']; ?></td>

                    <td width="60" align="right"><? echo $working_hour_arr[$year_month]; ?></td>
                    <td width="60" align="right"><? echo number_format(($capacity_arr[$year_month]['efficency']/$tot_location)/$total_selected_company,2,'.',''); ?></td>
                    <td width="60" align="right"><? echo number_format(($capacity_arr[$year_month]['working_day']/$tot_location)/$total_selected_company); ?></td>
                  <!--  <td width="60" align="right"><? //echo $capacityMints; ?></td>-->
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_pcs']; ?></td>
                   <!-- <td width="60" align="right"><? //echo $capacity_arr[$year_month]['capacity_month_min']; ?></td>-->

                    <td width="60" align="right"><? echo array_sum($projected_qty_array[$year_month]); ?></td>
                    <td width="60" align="right"><? echo array_sum($confirm_qty_array[$year_month]); ?></td>
					<td width="100" align="right"><? echo number_format($projectedPlusConfirmedQty,0); ?></td>

					<td width="60" align="right"><? echo number_format(array_sum($projected_value_array[$year_month])/$conver_to_million,2); ?></td>
                    <td width="60" align="right"><? echo number_format(array_sum($confirm_value_array[$year_month])/$conver_to_million,2); ?></td>

					<td width="60" align="right"><?

					 echo number_format($tot_conf_proj_value/$conver_to_million,2); ?></td>

                    <td width="80" align="right"><? echo $plan_qty_arr[$year_month]; ?></td>
                    <td width="80" align="right"><? echo array_sum($confirm_qty_array[$year_month])-$plan_qty_arr[$year_month]; ?></td>
                    <td width="60" align="right" title="(SMV*ConfirmQty+SMV*ProjQty)/Capacity Mon Minute*100"><? echo number_format($booked_parcent_arr[$year_month],2);
					 ?></td>

				   <td width="80" align="right"><? echo number_format($exfact_qty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($exfact_value/$conver_to_million,2); ?></td>
                     <td width="80" align="right"><? echo number_format($balance_exfact_qty,0); ?></td>
					 <td width="" align="right"><? echo number_format($balanced_fob_value/$conver_to_million,2); ?></td>
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
				
				if($balance_exfact_qty>0){$total_balance_exfact_qty+=$balance_exfact_qty;}
				
				if($balanced_fob_value>0){$total_balanced_fob_value+=$balanced_fob_value;}
				
				
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
			<th><? echo number_format($total_projected_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_confirm_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_conf_proj_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_plan_qty,2); ?> </th>
			<th><? echo number_format($total_balance_planning,2); ?> </th>
			<th><? //echo number_format($total_balance_exfact_qty,2); ?> </th>
			<th><? echo number_format($total_exfact_qty,0); ?> </th>

			<th><? echo number_format($total_exfact_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_balance_exfact_qty,0); ?> </th>
			 <th align="right"><? echo number_format($total_balanced_fob_value/$conver_to_million,2); ?></th>
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
                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id]/$conver_to_million,2); ?></td>
                        <td width="80" align="right"><? echo number_format(($confirm_value_array[$year_month][$buyer_id]/$conver_to_million),2); ?></td>
						<td width="80" align="right"><?

						echo number_format(($tot_conf_proj_val/$conver_to_million),2); ?></td>


                        <td width="60" align="right"><? echo number_format($avgSMV,2); ?></td>
                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
						<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
                        <td width="60" align="right"><? echo number_format($bookedParcent,2); ?></td>

						<td width="80" align="right"><? echo number_format($buyer_exfact_qty,0); ?></td>
						<td width="80" align="right"><? echo number_format($buyer_exfact_value/$conver_to_million,2); ?></td>
						<td width="80" align="right"><? echo number_format($tot_balance_exfact_qty,0); ?></td>
						<td width="" align="right"><? echo number_format(($tot_balance_exfact_value/$conver_to_million),2); ?></td>

                    </tr>
                    <? $tr++;$i++;} ?>
                    <tr bgcolor="#DDD">
                        <th>Total:</th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_value_arr[$year_month]/$conver_to_million,2); ?></th>
                        <th align="right"><? echo number_format($confirm_value_arr[$year_month]/$conver_to_million,2); ?></th>

						<th align="right"><? echo number_format((($projected_value_arr[$year_month]+$confirm_value_arr[$year_month])/$conver_to_million),2); ?></th>


                        <th align="right">
						<? echo  number_format((($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month])),2); ?>
                        </td>
                         <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
						 <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>
						 <th align="right"><? echo number_format($buyer_exfact_qty_mon_arr[$year_month],0); ?></th>
						  <th align="right"><? echo number_format($buyer_exfact_value_mon_arr[$year_month]/$conver_to_million,2); ?></th>
						   <th align="right"><? echo number_format($buyer_tot_balance_exfact_qty_arr[$year_month],0); ?></th>
						    <th align="right"><? echo number_format($buyer_tot_balance_exfact_val_arr[$year_month]/$conver_to_million,2); ?></th>
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

if($action=="report_generate4")
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

	$cbo_company_arr=explode (",", $cbo_company_name);
	$total_selected_company = count($cbo_company_arr);
	$conver_to_million=str_replace ("'","",$conver_to_million);
	$cbo_buyer_id=str_replace ("'","",$cbo_buyer_id);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");


	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;

	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	$tot_month = datediff( 'm', $s_date,$e_date);


	foreach ($cbo_company_arr as $company_id) {
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($s_date,$i);
			$month_arr[date("Y",strtotime($next_month))][date("m",strtotime($next_month))][$company_id]=date("Y-m",strtotime($next_month)).'-'.$company_id;
		}
	}
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_id='$location_id'";


	$working_hour_arr=array();
	$sql_fina_param=sql_select("select company_id, applying_period_date, working_hour from lib_standard_cm_entry where company_id in($cbo_company_name) and status_active=1 and is_deleted=0 order by applying_period_date");
	foreach( $sql_fina_param as $rowf)
	{
		$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")])).'-'.$rowf[csf("company_id")];
		$working_hour_arr[$date_key]=$rowf[csf("working_hour")];
	}
	unset($sql_fina_param);




	$sql="select id, company_id, buyer_id, quot_date, order_qty from  wo_price_quotation_v3_mst where status_active=1 and is_deleted =0 AND company_id in($cbo_company_name) AND quot_date between '$s_date' and '$e_date'";
	$sql_quot_result=sql_select($sql);
	foreach($sql_quot_result as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("quot_date")])).'-'.$row[csf("company_id")];
		$quot_wise_arr[$date_key][$row[csf("buyer_id")]]['offer_qnty']+=$row[csf("order_qty")];
		$order_data_array[$date_key][$row[csf('buyer_id')]]+=0;
	}
	unset($sql_quot_result);


	$sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c
	 where a.id=b.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond
	 and a.year between $cbo_year_name and $cbo_end_year_name and b.mst_id=c.mst_id and c.month_id between 1 and 12 and a.is_deleted=0 and a.status_active=1 group by a.comapny_id, a.location_id, a.year, a.effi_percent, a.basic_smv, a.avg_machine_line, b.no_of_line, c.working_day, c.month_id, c.capacity_month_min, c.capacity_month_pcs";
	//echo $sql;
	$sql_data_smv=sql_select($sql);
	$tot_row_data_smv=count($sql_data_smv);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")])).'-'.$row[csf("comapny_id")];
		//$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
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

	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
	if($cbo_buyer_id !=''){$buyer_cond=" AND a.buyer_name in($cbo_buyer_id)";}


	if($cbo_date_cat_id==1)
	{
	$sql="SELECT a.company_name, a.id as job_id,a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.pub_shipment_date,b.unit_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else if($cbo_date_cat_id==3)
	{
	$sql="SELECT a.company_name, a.id as job_id,a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.shipment_date as pub_shipment_date,b.unit_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else //Country Ship Date
	{
		$sql="SELECT a.company_name,a.id as job_id,a.job_no,a.set_smv,a.set_break_down,a.buyer_name,a.total_set_qnty, c.country_ship_date as pub_shipment_date,b.unit_price,
	(c.order_quantity/a.total_set_qnty) as po_quantity,
	(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}

	//echo $sql;
	$sql_data=sql_select($sql);
	foreach( $sql_data as $row)
	{

		$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")])).'-'.$row[csf("company_name")];
		$order_data_array[$year_month]['rowspan']+=1;
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];

		$job_wise_qty_arr[$row[csf('job_id')]]+=$confirm_qty;
		$job_wise_date_arr[$row[csf('job_id')]]=$date_key;
		$date_wise_job_arr[$date_key][$row[csf('job_id')]]=$row[csf('job_id')];
		$job_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		
		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
		{
			foreach($set_break_down_arr as $set_break_down)
			{
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1  || $cbo_date_cat_id==3)
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
				$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
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
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);

			$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
		}


	}


	// var_dump($date_wise_job_arr);die;
	
				
	
	
	
	$pre_sql="select a.job_id,a.approved,a.partial_approved,a.costing_per,b.margin_pcs_set from wo_pre_cost_mst a ,wo_pre_cost_dtls b where  a.job_id=b.job_id ";
	
	$p=1;
	$job_list_arr=array_chunk($job_arr,999);
	foreach($job_list_arr as $job_no_process)
	{
		if($p==1){$pre_sql .=" and (a.job_id in(".implode(",",$job_no_process).")";} 
		else{$pre_sql .=" or a.job_id in(".implode(",",$job_no_process).")";}
		$p++;
	}
	$pre_sql .=")";	
	
	$pre_sql_result=sql_select($pre_sql);
	$pre_cost_data_arr=array();
	foreach( $pre_sql_result as $row)
	{
		if($row[csf("costing_per")]==1){
			$pcs_qty=12;	
		}
		else if($row[csf("costing_per")]==2){
			$pcs_qty=1;	
		}
		else if($row[csf("costing_per")]==3){
			$pcs_qty=24;	
		}
		else if($row[csf("costing_per")]==4){
			$pcs_qty=36;	
		}
		else if($row[csf("costing_per")]==5){
			$pcs_qty=48;	
		}
		
		$margin_pcs=$row[csf("margin_pcs_set")]/$pcs_qty;
		
		
		$pre_cost_data_arr['margin_pcs'][$job_wise_date_arr[$row[csf('job_id')]]]+=$margin_pcs*$job_wise_qty_arr[$row[csf('job_id')]]; 
		
		if($row[csf('approved')]==2){
		$pre_cost_data_arr['approved'][$job_wise_date_arr[$row[csf('job_id')]]][$row[csf('job_id')]]=1;
		}
		
		
		
	}
	
	//var_dump($pre_cost_data_arr['margin_pcs']);
	
	
	
	$sql_data="select c.company_id, pd.plan_qnty as pdplan_qnty,b.pub_shipment_date from wo_po_break_down b,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  b.id=c.po_break_down_id and pd.plan_id=c.plan_id and c.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between  '$s_date' and '$e_date'";
	$data_result=sql_select($sql_data);
	$plan_qty_arr=array();
	foreach( $data_result as $row)
	{
		$plan_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")])).'-'.$row[csf("company_id")]]+=$row[csf("pdplan_qnty")];
	}
	//var_dump($plan_qty_arr);

	ob_start();
	?>
	<div style="margin:0 auto; width:2220px;">
        <table width="2200" border="0" cellpadding="2" cellspacing="0">
            <thead>
                <tr class="form_caption">
                    <td colspan="32" align="center" style="font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
                </tr>
                <tr class="form_caption">
                   <td colspan="32" align="center" style="font-size:14px;">
                	<? $i=0;
                	$comma = '';
                	foreach ($cbo_company_arr as $key => $company_id) {
                		if($i != 0) {$comma = " And " ;} else {$comma = " " ;}
                		$company_full_name .= $comma.$companyArr[$company_id];
                		$i++;
                	}
                	echo '<b>'.$company_full_name.'</b>';
                   		?>
                	</td>
                </tr>
            </thead>
        </table>
        <div style="width:2220px;" id="scroll_body">
        <table cellspacing="0" width="2200" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="32">Monthly Capacity Information</th>
               </tr>
                <tr>
                	<th width="100">Company</th>
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
                   
                   <th width="60">Eqv Conf. Basic Qnty(Pcs)</th>
                   <th width="60">% of Capacity Confirmed (Pcs)</th>
                   
				    <th width="60">Total Pcs (Proj + Conf)</th>

                   <th width="60">Total Eqv Basic Pcs (Proj+Conf)</th>
                   <th width="60">% of Capacity Confirmed+Projected (Pcs)</th>
                   
                   
                   <th width="60">Projected FOB  Value</th>
                   <th width="60">Confirmed FOB  Value</th>
				   <th width="60">Total FOB (Proj + Conf)</th>
                   <th width="60">Projected Mints</th>
                   <th width="60">Confirmed Mints</th>
                   
                   <th width="60">% Capacity Confirmd (Min)</th>
                   
				   <th width="100">Total Mints (Proj + Conf)</th>
                   
                   <th width="60">% of Capacity Confirmed+Projected (Min)</th>
                   
                   <th width="80">Planned</th>
                   <th width="80">Balanced Planning</th>
                   <th width="60">Capacity Utilization %</th>
                   <th width="60">Over Booking</th>
                   <th width="60">Less Booking %</th>
                   <th width="60">% of Approved CS</th>
                   <th>% Margin</th>

                </tr>
            </thead>
            <tbody>
				 <? 	$i=1;
				 		$gtotal_machine='';
                 		$gtotal_line='';
                 		$gtotal_capacity_mints='';
                 		$gtotal_factory_capacity_pc ='';
                 		$gtotal_factory_capacity_mints = '';
                 		$gtotal_projected_qty = '';
                 		$gtotal_confirmed_qty = '';
                 		$gtotal_total_pcs ='';
                 		$gtotal_projected_fob_value = '';
                 		$gtotal_confirmed_fob_value = '';
                 		$gtotal_total_fob = '';
                 		$gtotal_projected_mints = '';
                 		$gtotal_confirmed_mints = '';
                 		$gtotal_total_mints = '';
                 		$gtotal_Planned = '';
                 		$gtotal_balanced_planning = '';

                 foreach($month_arr as $year=>$year_data){
                 	foreach ($year_data as $month => $company_data) {
                 		$total_machine='';
                 		$total_line='';
                 		$total_capacity_mints='';
                 		$total_factory_capacity_pc ='';
                 		$total_factory_capacity_mints = '';
                 		$total_projected_qty = '';
                 		$total_confirmed_qty = '';
                 		$total_total_pcs ='';
                 		$total_projected_fob_value = '';
                 		$total_confirmed_fob_value = '';
                 		$total_total_fob = '';
                 		$total_projected_mints = '';
                 		$total_confirmed_mints = '';
                 		$total_total_mints = '';
                 		$total_Planned = '';
                 		$total_balanced_planning = '';
						$total_eqvConfBasicQntyPcs='';
                 		foreach ($company_data as $company => $year_month_company) {
                 			list($year,$month,$company)=explode('-',$year_month_company);
		                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$tot_mc_val = $capacity_arr[$year_month_company]['tot_mc_val'];
		                	$totalMechine=$tot_mc_val;
		                    //$capacityMints=$totalMechine*$working_hour_arr[$year_month_company]*$capacity_arr[$year_month_company]['working_day']*60;
		                    $less_over=$over_parcent=$less_parcent='';
							$less_over=$booked_parcent_arr[$year_month_company]-100;
							if($less_over>0){$over_parcent=number_format($less_over,2);}
							else{$less_parcent=number_format($less_over,2);}

							$location_ids=rtrim($location_id_arr[$year_month_company],',');

							$location_ids=array_unique(explode(",",$location_ids));
							$tot_location=count($location_ids);
							$total_eff = $capacity_arr[$year_month_company]['efficency']/$tot_location;

							$total_machine +=$totalMechine;
							$total_line +=$capacity_arr[$year_month_company]['no_of_line'];
							
							$total_factory_capacity_pc += $capacity_arr[$year_month_company]['capacity_month_pcs'];
							$total_factory_capacity_mints += $capacity_arr[$year_month_company]['capacity_month_min'];
							$total_projected_qty += array_sum($projected_qty_array[$year_month_company]);
							$total_confirmed_qty += array_sum($confirm_qty_array[$year_month_company]);
							$total_projected_fob_value += array_sum($projected_value_array[$year_month_company]);
							$total_confirmed_fob_value += array_sum($confirm_value_array[$year_month_company]);
							$total_projected_mints += array_sum($projected_booked_smv_mints_arr[$year_month_company]);
							$total_confirmed_mints += array_sum($booked_smv_mints_arr[$year_month_company]);
							$total_Planned += $plan_qty_arr[$year_month_company];
							$total_balanced_planning += array_sum($confirm_qty_array[$year_month_company])-$plan_qty_arr[$year_month_company];
		                    
							
							$capacityMints=$totalMechine*$working_hour_arr[$year_month_company]*number_format($capacity_arr[$year_month_company]['working_day']/$tot_location,2)*60;
							$total_capacity_mints += $capacityMints;

							?>
		                	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                	<td width="70"><? echo $companyArr[$company]?></td>
			                    <td width="70"><? echo $months[$month*1].'-'.$year; ?></td>

							    <td width="60" align="right"><? echo $totalMechine;   ?></td>
			                    <td width="60" align="right"><? echo $capacity_arr[$year_month_company]['no_of_line'];  ?></td>

			                    <td width="60" align="right"><? echo $working_hour_arr[$year_month_company]; ?></td>
			                    <td width="60" align="right"><? echo number_format($total_eff,2) ?></td>
			                    <td width="60" align="right"><? echo number_format(($capacity_arr[$year_month_company]['working_day']/$tot_location),2); ?></td>
			                    <td width="60" align="right"><? echo  $capacityMints; ?></td>
			                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month_company]['capacity_month_pcs'],2); ?></td>
			                    <td width="60" align="right"><? echo number_format($capacity_arr[$year_month_company]['capacity_month_min'],2); ?></td>

			                    <td width="60" align="right"><? echo number_format(array_sum($projected_qty_array[$year_month_company]),2); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($confirm_qty_array[$year_month_company]),2); ?></td>
                                <td width="60" align="right"><? 
								$eqvConfBasicQntyPcs=array_sum($booked_smv_mints_arr[$year_month_company])/($capacity_arr[$year_month_company]['capacity_month_min']/$capacity_arr[$year_month_company]['capacity_month_pcs']);
								
								$eqvConfBasicQntyPcs=$eqvConfBasicQntyPcs*1;
								echo fn_number_format($eqvConfBasicQntyPcs,2);
								$total_eqvConfBasicQntyPcs += $eqvConfBasicQntyPcs;
								
								?></td>
                                <td width="60" align="right">
								<? 
								echo number_format(($eqvConfBasicQntyPcs/$capacity_arr[$year_month_company]['capacity_month_pcs'])*100,2);
								?>
                                </td>
                                 <td width="100" align="right">
								<?
								$projectedPlusConfirmedQty=array_sum($projected_qty_array[$year_month_company])+array_sum($confirm_qty_array[$year_month_company]);
								$total_total_pcs += $projectedPlusConfirmedQty;
								echo number_format($projectedPlusConfirmedQty,2);
								?>
			                     </td>
                                 
                                <td width="60" align="right">
                                <? $projectedPlusConfirmedMint=array_sum($projected_booked_smv_mints_arr[$year_month_company])+array_sum($booked_smv_mints_arr[$year_month_company]);
								
								$TotalEqvBasicPcsProjConf=($projectedPlusConfirmedMint/($capacity_arr[$year_month_company]['capacity_month_min']/$capacity_arr[$year_month_company]['capacity_month_pcs']));
								echo number_format($TotalEqvBasicPcsProjConf,2);
								?>

                                </td>
                                <td width="60" align="right"><? echo number_format(($TotalEqvBasicPcsProjConf/$capacity_arr[$year_month_company]['capacity_month_pcs'])*100,2);?></td>
                                 
                                 
								<td width="60" align="right"><? echo number_format(array_sum($projected_value_array[$year_month_company])/$conver_to_million,4
); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($confirm_value_array[$year_month_company])/$conver_to_million,4
); ?></td>
								<td width="60" align="right"><?
								$tot_conf_proj_value=array_sum($confirm_value_array[$year_month_company])+array_sum($projected_value_array[$year_month_company]);
								$total_total_fob += $tot_conf_proj_value;
								 echo number_format($tot_conf_proj_value/$conver_to_million,4
); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($projected_booked_smv_mints_arr[$year_month_company]),2); ?></td>
			                    <td width="60" align="right"><? echo number_format(array_sum($booked_smv_mints_arr[$year_month_company]),2); ?></td>
			                     
                                 
                                <td width="60" align="right"><? echo number_format(array_sum($booked_smv_mints_arr[$year_month_company])/$capacity_arr[$year_month_company]['capacity_month_min']*100,2);?></td>
                                 
                                 
                                 <td width="60" align="right">
								<?
								$projectedPlusConfirmedMint=array_sum($projected_booked_smv_mints_arr[$year_month_company])+array_sum($booked_smv_mints_arr[$year_month_company]);
								$total_total_mints += $projectedPlusConfirmedMint;
								echo number_format($projectedPlusConfirmedMint,2);
								 ?>
			                    </td>
                                
                                <td width="60" align="right"><? echo number_format($projectedPlusConfirmedMint/$capacity_arr[$year_month_company]['capacity_month_min']*100,2);?></td>
                                
                                
			                    <td width="80" align="right"><? echo number_format($plan_qty_arr[$year_month_company],2); ?></td>
			                    <td width="80" align="right"><? echo number_format(array_sum($confirm_qty_array[$year_month_company])-$plan_qty_arr[$year_month_company],2); ?></td>
			                    <td width="60" align="right"><? echo number_format($booked_parcent_arr[$year_month_company],2); ?></td>
			                    <td width="60" align="right"><? echo number_format($over_parcent,2); ?></td>
			                    <td width="60"  align="right"><? echo number_format($less_parcent,2); ?></td>
                                <td width="60" align="right">
								<? //echo number_format((count($pre_cost_data_arr['approved'][$year_month_company])/count($date_wise_job_arr[$year_month_company]))*100,2); ?>
                                </td>
                                <td align="right"><? //echo number_format($pre_cost_data_arr['margin_pcs'][$year_month_company],2); ?></td>
                                
                                

		                	</tr>
		                	<? $i++;
		                	$totalMonth = count($year_data);
                 		}	$gtotal_total_eqvConfBasicQntyPcs += $total_eqvConfBasicQntyPcs;
                 			$gtotal_machine += $total_machine;
		                	$gtotal_line += $total_line;
		                	$gtotal_capacity_mints += $total_capacity_mints;
		                	$gtotal_factory_capacity_pc += $total_factory_capacity_pc;
		                	$gtotal_factory_capacity_mints += $total_factory_capacity_mints;
		                	$gtotal_projected_qty += $total_projected_qty;
		                	$gtotal_confirmed_qty += $total_confirmed_qty;
		                	$gtotal_total_pcs += $total_total_pcs;
		                	$gtotal_projected_fob_value += $total_projected_fob_value;
		                	$gtotal_confirmed_fob_value += $total_confirmed_fob_value;
		                	$gtotal_total_fob += $total_total_fob;
		                	$gtotal_projected_mints += $total_projected_mints;
		                	$gtotal_confirmed_mints += $total_confirmed_mints;
		                	$gtotal_total_mints += $total_total_mints;
		                	$gtotal_Planned += $total_Planned;
		                	$gtotal_balanced_planning += $total_balanced_planning;
                 		if(($totalMonth > 1 && $total_selected_company > 1) || ($totalMonth >= 1 && $total_selected_company >= 2) || ($totalMonth >= 2 && $total_selected_company >= 2)){ ?>
                 		<tr bgcolor="#CCCCCC">
		                    <td width="140" colspan="2" align="right"><strong>Month Total</strong></td>
						    <td width="60" align="right"><strong><? echo $total_machine;  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo $total_line;  ?></strong></td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right"><strong><? echo number_format($total_capacity_mints,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_factory_capacity_pc,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_factory_capacity_mints,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_projected_qty,2);  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_confirmed_qty,2);  ?></strong></td>
							<td width="100" align="right"><strong><? echo fn_number_format($total_eqvConfBasicQntyPcs,0)
							//$total_total_pcs;  ?></strong></td>
							<td width="60" align="right"><strong></strong></td>
		                    <td width="60" align="right"><strong></strong></td>
							<td width="60" align="right"><strong></strong></td>
		                    <td width="60" align="right"><strong></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_projected_fob_value/$conver_to_million,4
);  ?></strong></td>
		                    <td width="80" align="right"><strong><? echo number_format($total_confirmed_fob_value/$conver_to_million,4); ?><? //echo $total_Planned;  ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($total_total_fob/$conver_to_million,4) ?></strong></td>
		                    <td width="80" align="right"><strong><? echo number_format($total_projected_mints,0);  ?><? //echo $total_balanced_planning;  ?></strong></td>
		                    <td width="60" align="right"><? echo number_format($total_confirmed_mints,0);  ?></td>
		                    <td width="60" align="right"></td>
		                    <td align="right"><? echo number_format($total_total_mints,2);  ?></td>
                            
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                 		</tr>
                 		<?
                 		}
                 	}
            	}
            	if($totalMonth > 1){
            ?>
            			<tr>
		                    <td width="140" colspan="2" align="right"><strong>Grand Total</strong></td>
						    <td width="60" align="right"><strong><? echo $gtotal_machine ?></strong></td>
		                    <td width="60" align="right"><strong><? echo $gtotal_line ?></strong></td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right">&nbsp;</td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_capacity_mints,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_factory_capacity_pc,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_factory_capacity_mints,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_projected_qty,2); ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_confirmed_qty,2); ?></strong></td>
		                    <td width="60" align="right">
                            <?
							//$GrandEqvConfBasicQntyPcs=$gtotal_confirmed_mints/($gtotal_factory_capacity_mints/$gtotal_factory_capacity_pc);
							
							echo number_format($gtotal_total_eqvConfBasicQntyPcs,2);
							?>
                            </td>
                            <td width="60" align="right"><? echo number_format($GrandEqvConfBasicQntyPcs/$gtotal_factory_capacity_pc*100,2)?></td>
                            
                            <td width="100" align="right"><strong><? echo number_format($gtotal_total_pcs,2); ?></strong></td>
							<td width="60" align="right"><? 
							$GrandTotalEqvBasicPcsProjConf = ($gtotal_projected_mints +$gtotal_confirmed_mints)/($gtotal_factory_capacity_mints/$gtotal_factory_capacity_pc); 
							echo number_format($GrandTotalEqvBasicPcsProjConf,2);
							?></td>
                            <td width="60" align="right"><? echo number_format(($GrandTotalEqvBasicPcsProjConf/$gtotal_factory_capacity_pc)*100,2)?></td>
                            
                            <td width="60" align="right"><strong><? echo number_format($gtotal_projected_fob_value,0); ?></strong></td>
                            
                            
                            
                            <td width="60" align="right"><strong><? echo number_format($gtotal_confirmed_fob_value,0) ?></strong></td>
                            
                            <td width="60" align="right"><strong><? echo number_format($gtotal_total_fob,2) ?></strong></td>
		                    <td width="60" align="right"><strong><? echo number_format($gtotal_projected_mints,0) ?></strong></td>
                            <td width="60" align="right"><strong><? echo number_format($gtotal_confirmed_mints,0) ?></strong></td>
		                    <td width="60" align="right"><? echo number_format(($gtotal_factory_capacity_mints/$gtotal_confirmed_mints)*100,2) ?></td>
                            <td width="60" align="right"><strong><? echo number_format($gtotal_total_mints,0) ?></strong></td>
                            <td width="60" align="right"><? echo number_format($gtotal_total_mints/$gtotal_factory_capacity_mints*100,2) ?></td>
                            <td width="80" align="right"><strong><? echo number_format($gtotal_Planned,2) ?></strong></td>
		                    <td width="80" align="right"><strong><? echo number_format($gtotal_balanced_planning,2) ?></strong></td>
		                    <td width="60" align="right"></td>
		                    <td width="60" align="right"></td>
		                    <td align="right"></td>
                            <td width="60" align="right"></td>
                            <td width="60" align="right"></td>
                 		</tr>
                 		<? }
                 		?>
            </tbody>
         </table>
         </div>

         <!--//Order information.............................................-->
         <br /><br />
        <div style="width:1280px;">
        <table cellspacing="0" width="1260" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
                <tr>
                	<th colspan="15">Buyer wise capacity booked summary</th>
                </tr>
                <tr>
                	<th width="70">Company</th>
                    <th width="70">Month</th>
                    <th width="130">Buyer</th>
                    <th width="100">Quotation Qty</th>
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
                    <?
                    $gtotal_quotation_qty = '';
			 		$gtotal_projected_qty = '';
			 		$gtotal_confirmed_qty = '';
			 		$gtotal_total_pcs = '';
			 		$gtotal_projected_value = '';
			 		$gtotal_confirmed_value = '';
			 		$gtotal_Total_fob_value = '';
			 		$gtotal_projected_mints = '';
			 		$gtotal_confirmed_mints = '';
			 		$gtotal_total_mints = '';
                    $i=1;
					 foreach($month_arr as $year => $year_data){
					 	foreach ($year_data as $month => $company_data) {
					 		$total_quotation_qty = '';
					 		$total_projected_qty = '';
					 		$total_confirmed_qty = '';
					 		$total_total_pcs = '';
					 		$total_projected_value = '';
					 		$total_confirmed_value = '';
					 		$total_Total_fob_value = '';
					 		$total_projected_mints = '';
					 		$total_confirmed_mints = '';
					 		$total_total_mints = '';

					 		foreach ($company_data as $company => $year_month) {
					 			$tr=0;
								list($year,$month,$company)=explode('-',$year_month);
								if(count($order_data_array[$year_month]) != 0){
									$rowspan=count($order_data_array[$year_month])+2;
								}
								else{
									$rowspan =3;
								}


								$fn = "change_color('trb_".$i."','".$bgcolor."')";
								echo '
								<tr bgcolor="'.$bgcolor.'" id="trb_'.$i.'"  onclick="'.$fn.'">
									<td width="100" rowspan="'.$rowspan.'" valign="middle">'.$companyArr[$company].'</td><td width="70" rowspan="'.$rowspan.'" valign="middle">'.$months[$month*1].'-'.$year.'</td>';

								arsort($order_data_array[$year_month]);
							if(count($order_data_array[$year_month])>0){
								foreach($order_data_array[$year_month] as $buyer_id=>$confirm_qty)
								{
									$bookedMints	= $booked_smv_mints_arr[$year_month][$buyer_id];
									$projectedbookedMints=$projected_booked_smv_mints_arr[$year_month][$buyer_id];
									$project_qty	= $projected_qty_array[$year_month][$buyer_id];

									$quoted_qty		= $quot_wise_arr[$year_month][$buyer_id]['offer_qnty'];

									//$avgSMV=$bookedMints/$confirm_qty;
									$avgSMV=($bookedMints+$projectedbookedMints) / (($confirm_qty+$project_qty)>0?($confirm_qty+$project_qty):1);
									//$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
									$bookedParcent=0;
									if($capacity_arr[$year_month]['capacity_month_min'])
									{
										//$bookedParcent=(($bookedMints+$projectedbookedMints)/$capacity_arr[$year_month]['capacity_month_min'])*100;
										$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
										$bookedParcentFormula = "(Confirmed Mints / Factory Capacity Mints) * 100";
									}

									$confirm_qty_arr[$year_month]+=$confirm_qty;
									$projected_qty_arr[$year_month]+=$project_qty;

									$quoted_qty_arr[$year_month]+=$quoted_qty;

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
			                        <td width="100" align="right"><? echo number_format($quoted_qty,0); ?></td>
			                        <td width="80" align="right"><? echo number_format($project_qty,0); ?></td>
			                        <td width="80" align="right"><? echo number_format($confirm_qty,0); ?></td>
			                        <td width="80" align="right"><? echo number_format($project_qty+$confirm_qty,0); ?></td>
			                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id]/$conver_to_million,4
); ?></td>
			                        <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$buyer_id]/$conver_to_million,4
); ?></td>
									<td width="80" align="right"><?
									$tot_conf_proj_val=$projected_value_array[$year_month][$buyer_id]+$confirm_value_array[$year_month][$buyer_id];
									echo number_format($tot_conf_proj_val/$conver_to_million,4
); ?></td>


			                        <td width="60" align="right" title="<? echo "(".$bookedMints."+".$projectedbookedMints.")/(".$confirm_qty."+".$project_qty.")"; ?>"><? echo number_format($avgSMV,2); ?></td>
			                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
			                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
									<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
			                        <td align="right" title="<? echo $bookedParcentFormula; ?>"><? echo number_format($bookedParcent,2); ?></td>
			                    </tr>
			                    <?
								$tr++;
								$i++;
								}
							}
							else{
								echo "<td colspan='13'></></tr>";
							}
							$total_quotation_qty += $quoted_qty_arr[$year_month];
							$total_projected_qty += $projected_qty_arr[$year_month];
							$total_confirmed_qty += $confirm_qty_arr[$year_month];
					 		$total_total_pcs += $projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month];
					 		$total_projected_value += $projected_value_arr[$year_month];
					 		$total_confirmed_value += $confirm_value_arr[$year_month];
					 		$total_Total_fob_value += $projected_value_arr[$year_month]+$confirm_value_arr[$year_month];

					 		$total_projected_mints += $projected_booked_mints_arr[$year_month];
					 		$total_confirmed_mints += $booked_mints_arr[$year_month];
					 		$total_total_mints += $projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month];
							?>
		                    <tr bgcolor="#DDD">

		                        <th>Total:</th>
		                        <th align="right"><? echo number_format($quoted_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
		                        <th align="right"><? echo number_format($projected_value_arr[$year_month]/$conver_to_million,4
); ?></th>
		                        <th align="right"><? echo number_format($confirm_value_arr[$year_month]/$conver_to_million,4
); ?></th>

								<th align="right"><? echo number_format(($projected_value_arr[$year_month]+$confirm_value_arr[$year_month])/$conver_to_million,4
); ?></th>


		                        <th align="right"><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></td>
		                         <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
		                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
								 <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
		                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>
		                    </tr>

		                    <tr bgcolor="#FFFF00">
		                        <th colspan="12">
		                        	<?
										$less_over=$booked_parcent_arr[$year_month]-100;
										if($less_over>0){echo "Capacity booked Over %";}
										else{echo "Capacity booked Less %";}
									?>

		                        </th>
		                        <th align="right"><? echo number_format($less_over,2); ?></th>
		                    </tr>
							<?
							$totalMonth = count($year_data);
					 		}
					 		$gtotal_quotation_qty += $total_quotation_qty;
							$gtotal_projected_qty += $total_projected_qty;
							$gtotal_confirmed_qty += $total_confirmed_qty;
					 		$gtotal_total_pcs += $total_total_pcs;
					 		$gtotal_projected_value += $total_projected_value;
					 		$gtotal_confirmed_value += $total_confirmed_value;
					 		$gtotal_Total_fob_value += $total_Total_fob_value;
					 		$gtotal_projected_mints += $total_projected_mints;
					 		$gtotal_confirmed_mints += $total_confirmed_mints;
					 		$gtotal_total_mints += $total_total_mints;
					 		if(($totalMonth > 1 && $total_selected_company > 1) || ($totalMonth >= 1 && $total_selected_company >= 2) || ($totalMonth >= 2 && $total_selected_company >= 2)){
					 		?>
					 		<tr style="outline: thin solid">
		                        <th colspan="3" align="right">Month Total</th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_quotation_qty,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_projected_qty,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_confirmed_qty,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_total_pcs,0); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_projected_value/$conver_to_million,4
); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_confirmed_value/$conver_to_million,4
); ?></th>
								<th align="center" style="outline: thin solid"><? echo number_format($total_Total_fob_value/$conver_to_million,4
); ?></th>
		                        <th align="center" style="outline: thin solid"></td>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_projected_mints,2); ?></th>
		                        <th align="center" style="outline: thin solid"><? echo number_format($total_confirmed_mints,2); ?></th>
								<th align="center" style="outline: thin solid"><? echo number_format($total_total_mints,2); ?></th>
		                        <th align="center" style="outline: thin solid"></th>
		                    </tr>
					 		<?
					 		}
					 	}
					}
					if($totalMonth > 1){
					?>
					<tr style="outline: thin solid">
                        <th colspan="3" align="right" style="outline: thin solid">Grand Total</th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_quotation_qty,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_projected_qty,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_confirmed_qty,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_total_pcs,0); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_projected_value/$conver_to_million,4
); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_confirmed_value/$conver_to_million,4
); ?></th>
						<th align="center" style="outline: thin solid"><? echo number_format($gtotal_Total_fob_value/$conver_to_million,4
); ?></th>
                        <th align="center" style="outline: thin solid"> </td>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_projected_mints,2); ?></th>
                        <th align="center" style="outline: thin solid"><? echo number_format($gtotal_confirmed_mints,2); ?></th>
						<th align="center" style="outline: thin solid"><? echo number_format($gtotal_total_mints,2); ?></th>
                        <th align="center" style="outline: thin solid"></th>
                    </tr>
                    <? } ?>
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



if($action=="report_generate_buyer_wise")
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

	$cbo_company_arr=explode (",", $cbo_company_name);
    $total_selected_company = count($cbo_company_arr);
	$conver_to_million=str_replace ("'","",$conver_to_million);
	$cbo_buyer_id=str_replace ("'","",$cbo_buyer_id);


	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	//$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

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

	 $sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name  and c.month_id between 1 and 12 and a.is_deleted =0 and status_active=1 group by a.comapny_id,a.location_id,a.year,a.effi_percent, a.basic_smv,a.avg_machine_line,b.no_of_line,c.working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs"; //echo $sql;
	 // and c.month_id between $cbo_month and $cbo_month_end
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

	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
	if($cbo_buyer_id !=''){$buyer_cond=" AND a.buyer_name in($cbo_buyer_id)";}

	if($cbo_date_cat_id==1)
	{
		$sql="SELECT a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.pub_shipment_date,b.unit_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,b.id as po_id,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and b.shiping_status <> 3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else if($cbo_date_cat_id==3)
	{
		$sql="SELECT a.job_no,a.set_break_down,a.buyer_name,a.total_set_qnty, b.shipment_date as pub_shipment_date, b.unit_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,b.id as po_id,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.shipment_date between '$s_date' and '$e_date' and b.shiping_status <> 3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
		WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND c.country_ship_date between '$s_date' and '$e_date' and b.shiping_status <> 3 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
			{
				foreach($set_break_down_arr as $set_break_down){
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
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

			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);
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

			//echo $smv.', ';
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;


			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);

			$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;

		}


	}

	 $tot_po_ids=implode(',',array_unique(explode(',',$allpo_ids)));

	  $poIds=chop($tot_po_ids,',');$po_cond_for_in="";
	 $po_ids=count(array_unique(explode(",",$poIds)));
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
			
			//$po_wise_exfact_qty_arr[$row[csf('po_id')]]+=$bal_exfact_qty;
			//$po_wise_exfact_val_arr[$row[csf('po_id')]]+=$bal_exfact_qty*$po_wise_rate;
		}

	}
	else if($cbo_date_cat_id==3)
	{
		$sql_exf="select c.po_break_down_id as po_id,b.shipment_date,
		(CASE WHEN  c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as exf_qnty,
		(CASE WHEN  c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ret_exf_qnty
		from  pro_ex_factory_mst c,wo_po_break_down b where b.id=c.po_break_down_id and  b.status_active=1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted =0 and b.shipment_date between  '$s_date' and '$e_date' $po_cond_for_in";
		$sql_exf_res=sql_select($sql_exf);
		foreach($sql_exf_res as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("shipment_date")]));
			$bal_exfact_qty=$row[csf('exf_qnty')]-$row[csf('ret_exf_qnty')];
			$po_wise_rate=$po_wise_rate_arr[$row[csf('po_id')]]['rate'];
			$po_buyer=$po_wise_rate_arr[$row[csf('po_id')]]['buyer'];
			$exfact_qty_arr[$date_key]['exfact_qty']+=$bal_exfact_qty;
			$exfact_qty_arr[$date_key]['exfact_value']+=$bal_exfact_qty*$po_wise_rate;
			$buyer_exfact_qty_arr[$date_key][$po_buyer]['exfact_qty']+=$bal_exfact_qty;
			$buyer_exfact_qty_arr[$date_key][$po_buyer]['exfact_value']+=$bal_exfact_qty*$po_wise_rate;
			
			//$po_wise_exfact_qty_arr[$row[csf('po_id')]]+=$bal_exfact_qty;
			//$po_wise_exfact_val_arr[$row[csf('po_id')]]+=$bal_exfact_qty*$po_wise_rate;
		}
	}
	else
	{
		  $sql_exf="select c.po_break_down_id as po_id,d.country_ship_date as pub_shipment_date,
			(CASE WHEN  c.entry_form!=85 THEN e.production_qnty ELSE 0 END) as exf_qnty,
			(CASE WHEN  c.entry_form=85 THEN e.production_qnty ELSE 0 END) as ret_exf_qnty
			from  pro_ex_factory_mst c,pro_ex_factory_dtls e,wo_po_break_down b,wo_po_color_size_breakdown  d where c.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and e.mst_id=c.id and d.id=e.color_size_break_down_id and c.item_number_id=d.item_number_id and d.country_id=c.country_id and  b.status_active=1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted =0 and d.country_ship_date between  '$s_date' and '$e_date' $po_cond_for_in ";
			$sql_exf_res=sql_select($sql_exf);
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
				
				//$po_wise_exfact_qty_arr[$row[csf('po_id')]]+=$bal_exfact_qty;
				//$po_wise_exfact_val_arr[$row[csf('po_id')]]+=$bal_exfact_qty*$po_wise_rate;

			}
	}
	unset($sql_exf_res);



	 foreach($month_arr as $year_month){
		list($year,$month)=explode('-',$year_month);

		foreach($order_data_array[$year_month] as $buyer_id=>$confirm_qty){
			$buyer_wise_data_arr[$buyer_id]=$buyer_id;
			$end_year_month=date("Y-m",strtotime($e_date));
			if($year_month!=$end_year_month){
				$buyer_wise_prev_order_qty[$buyer_id]+=($confirm_qty+$project_qty= $projected_qty_array[$year_month][$buyer_id]);
				$buyer_wise_prev_order_val[$buyer_id]+=($confirm_value_array[$year_month][$buyer_id]+$projected_value_array[$year_month][$buyer_id]);
				$buyer_wise_prev_exp_qty[$buyer_id]+=$buyer_exfact_qty_arr[$year_month][$buyer_id]['exfact_qty'];
				$buyer_wise_prev_exp_val[$buyer_id]+=$buyer_exfact_qty_arr[$year_month][$buyer_id]['exfact_value'];
			
			}
			else
			{
				$buyer_wise_curr_order_qty[$buyer_id]+=($confirm_qty+$project_qty= $projected_qty_array[$year_month][$buyer_id]);
				$buyer_wise_curr_order_val[$buyer_id]+=($confirm_value_array[$year_month][$buyer_id]+$projected_value_array[$year_month][$buyer_id]);
				$buyer_wise_curr_exp_qty[$buyer_id]+=$buyer_exfact_qty_arr[$year_month][$buyer_id]['exfact_qty'];
				$buyer_wise_curr_exp_val[$buyer_id]+=$buyer_exfact_qty_arr[$year_month][$buyer_id]['exfact_value'];
			}

		}
	 }
					




	ob_start();
	?>
	<div style="margin:0 ; width:1180px;">
        <table width="1180" border="0" cellpadding="2" cellspacing="0">
            <thead>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:16px; font-weight:bold" >
					<? 
					foreach(explode(',',$cbo_company_name) as $cid){
						$companyNameArr[]=$companyArr[$cid];	
					}
					echo implode(',',$companyNameArr); 
					?>
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:14px;">
                       <b><? echo $report_title; ?></b>
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="23" align="center" style="font-size:14px;">
                       <b><? echo $months[date('m',strtotime($s_date))*1].' To '.$months[date('m',strtotime($e_date))*1]; ?></b>
                    </td>
                </tr>
            </thead>
        </table>
        
        
        
        
        <div style="width:1240px; margin:5px;" id="scroll_body">
        <table cellspacing="0" width="1120" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr><th colspan="14"><b style="float:left">Monthly Summary</b></th></tr>
               <tr>
               		<th rowspan="2">Monthly</th>
                    <th align="center" colspan="7">Order Status </th>
                    <th align="center" colspan="2">Planning Status</th>
                    <th align="center" colspan="2">Export Status</th>
                    <th align="center" colspan="2">Total Pending Status</th>
               </tr>
                <tr>
                   <th width="60">Factory Capacity PC</th>
                   <th width="60">Projected Qty (Pcs)</th>
                   <th width="60">Confirmed Qty (Pcs)</th>
				   <th width="60">Total Pcs (Proj + Conf)</th>
				   <th width="80">Projected FOB  Value</th>
                   <th width="80">Confirmed FOB  Value</th>
				   <th width="80">Total FOB (Proj + Conf)</th>
                   <th width="80">Planned</th>
                   <th width="80">Balanced Planning</th>
				   <th width="80">Export Qty</th>
                   <th width="80">FOB Value</th>
				   <th width="80">Balanced Export Qty</th>
				   <th width="">Balanced  FOB Value</th>
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
                    
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_pcs']; ?></td>
                    
                    <td width="60" align="right"><? echo array_sum($projected_qty_array[$year_month]); ?></td>
                    <td width="60" align="right"><? echo array_sum($confirm_qty_array[$year_month]); ?></td>
                    <td width="100" align="right"><? echo number_format($projectedPlusConfirmedQty,0);?></td>
                    
                    <td width="60" align="right"><? echo number_format(array_sum($projected_value_array[$year_month])/$conver_to_million,2); ?></td>
                    <td width="60" align="right"><? echo number_format(array_sum($confirm_value_array[$year_month])/$conver_to_million,2); ?></td>
                    
                    <td width="60" align="right"><?  echo number_format($tot_conf_proj_value/$conver_to_million,2); ?></td>
                    
                    <td width="80" align="right"><? echo $plan_qty_arr[$year_month]; ?></td>
                    <td width="80" align="right"><? echo array_sum($confirm_qty_array[$year_month])-$plan_qty_arr[$year_month]; ?></td>
<!--                <td width="60" align="right">< ? echo number_format($booked_parcent_arr[$year_month],2); ?></td>
-->
				    <td width="80" align="right"><? echo number_format($exfact_qty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($exfact_value/$conver_to_million,2); ?></td>
                    <td width="80" align="right"><? echo number_format($balance_exfact_qty,0);?></td>
					<td width="" align="right"><? echo number_format($balanced_fob_value/$conver_to_million,2); ?></td>
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
				
				if($balance_exfact_qty>0){
					$total_balance_exfact_qty+=$balance_exfact_qty;
				}
				if($balanced_fob_value>0){
					$total_balanced_fob_value+=$balanced_fob_value;
				}
				
			}
				?>
            </tbody>
			<tfoot>
			<tr>
			<th>Grand Total </th>
			<th><? echo number_format($total_capacity_month_pcs,0); ?> </th>
			<th><? echo number_format($total_projected_qty,0); ?> </th>
			<th><? echo number_format($total_confirm_qty,0); ?> </th>
			<th><? echo number_format($total_projectedPlusConfirmedQty,0); ?> </th>
			<th><? echo number_format($total_projected_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_confirm_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_conf_proj_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_plan_qty,2); ?> </th>
			<th><? echo number_format($total_balance_planning,2); ?> </th>
			<th><? echo number_format($total_exfact_qty,0); ?> </th>

			<th><? echo number_format($total_exfact_value/$conver_to_million,2); ?> </th>
			<th><? echo number_format($total_balance_exfact_qty,0); ?> </th>
			 <th align="right"><? echo number_format($total_balanced_fob_value/$conver_to_million,2); ?></th>
			</tr>
			</tfoot>
         </table>
         </div>

         <!--//Buyer wise information.............................................-->
        
		<div style="width:1250px;margin:5px;">
        <table cellspacing="0" width="1250" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
					<th colspan="15"><b style="float:left">Buyer Wise Capacity Booked Summary</b></th>
               </tr>
                <tr>
                    <th rowspan="2">Buyer</th>
                    <th colspan="2">Schedule up to Prev. Months</th>
                    <th colspan="2">Export upto Prev. Months</th>
                    <th colspan="2">Pending from Prev. Months</th>
                    <th colspan="2">Schedule for Current Month</th>
                    <th colspan="2">Total Curent  Month</th>
                    <th colspan="2">Export During Current Month</th>
                    <th colspan="2">Total Pending </th>
                </tr>
                <tr>
                   <th width="80">Qty</th>
                   <th width="80">Value</th>
                   <th width="80">Qty</th>
                   <th width="80">Value</th>
                   <th width="80">Qty</th>
                   <th width="80">Value</th>
                   <th width="80">Qty</th>
                   <th width="80">Value</th>
                   <th width="80">Qty</th>
                   <th width="80">Value</th>
                   <th width="80">Qty</th>
                   <th width="80">Value</th>
                   <th width="80">Qty</th>
                   <th width="80">Value</th>
                </tr>
            </thead>
            <tbody>
                     <? 
					 $i=1;
					 foreach($buyer_wise_data_arr as $buyer_id){
						 $tr=0;

						 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

						 ?>
                         <tr bgcolor="<? echo $bgcolor;?>" id="trb_<? echo $i;?>"  onclick="change_color('trb_<? echo $i;?>','<? echo $bgcolor;?>')">
                         
                        <td><? echo $buyer_arr[$buyer_id]; ?></td>
                        
                        <td width="80" align="right"><? echo number_format($buyer_wise_prev_order_qty[$buyer_id],0); ?></td>
                        <td width="80" align="right"><? echo number_format($buyer_wise_prev_order_val[$buyer_id]/$conver_to_million,2); ?></td>
                       
                        <td width="80" align="right"><? echo number_format($buyer_wise_prev_exp_qty[$buyer_id],0); ?></td>
                        <td width="80" align="right"><? echo number_format($buyer_wise_prev_exp_val[$buyer_id]/$conver_to_million,2); ?></td>
                        
                        <td width="80" align="right"><? echo number_format($buyer_wise_prev_order_qty[$buyer_id]-$buyer_wise_prev_exp_qty[$buyer_id],0); ?></td>
                        <td width="80" align="right"><? echo number_format(($buyer_wise_prev_order_val[$buyer_id]-$buyer_wise_prev_exp_val[$buyer_id])/$conver_to_million,2); ?></td>
                        
                        <td width="80" align="right"><? echo number_format($buyer_wise_curr_order_qty[$buyer_id],0); ?></td>
                        <td width="80" align="right"><? echo number_format($buyer_wise_curr_order_val[$buyer_id]/$conver_to_million,2); ?></td>

                        <td width="80" align="right"><? echo number_format(($buyer_wise_prev_order_qty[$buyer_id]-$buyer_wise_prev_exp_qty[$buyer_id])+$buyer_wise_curr_order_qty[$buyer_id],0); ?></td>
                        <td width="80" align="right"><? echo number_format((($buyer_wise_prev_order_val[$buyer_id]-$buyer_wise_prev_exp_val[$buyer_id])+$buyer_wise_curr_order_val[$buyer_id])/$conver_to_million,2); ?></td>
                        
                        
                        <td width="80" align="right"><? echo number_format($buyer_wise_curr_exp_qty[$buyer_id],0); ?></td>
                        <td width="80" align="right"><? echo number_format($buyer_wise_curr_exp_val[$buyer_id]/$conver_to_million,2); ?></td>
                        
                        <td width="80" align="right"><? 
						$pending_qty =(($buyer_wise_prev_order_qty[$buyer_id]-$buyer_wise_prev_exp_qty[$buyer_id])+$buyer_wise_curr_order_qty[$buyer_id])-$buyer_wise_curr_exp_qty[$buyer_id];
						echo number_format($pending_qty,0);
						if($pending_qty>0){
							$total_pending_qty+=$pending_qty;
						}
						
						 ?></td>
                        <td width="80" align="right"><? 
						$pending_val =(($buyer_wise_prev_order_val[$buyer_id]-$buyer_wise_prev_exp_val[$buyer_id])+$buyer_wise_curr_order_val[$buyer_id])-$buyer_wise_curr_exp_val[$buyer_id];
						
						echo number_format($pending_val/$conver_to_million,2);
						if($pending_val>0){
							$total_pending_val+=$pending_val;
						}
						 
						?></td>
                        
                        
                        
                        
                    </tr>
                    <? $i++;} ?>
                    <tr bgcolor="#DDD">
                        <th>Total:</th>
                        <th align="right"><? echo number_format(array_sum($buyer_wise_prev_order_qty),0); ?></th>
                        <th align="right"><? echo number_format(array_sum($buyer_wise_prev_order_val)/$conver_to_million,2); ?></th>
                        <th align="right"><? echo number_format(array_sum($buyer_wise_prev_exp_qty),0); ?></th>
                        <th align="right"><? echo number_format(array_sum($buyer_wise_prev_exp_val)/$conver_to_million,2); ?></th>

                        <th align="right"><? echo number_format(array_sum($buyer_wise_prev_order_qty)-array_sum($buyer_wise_prev_exp_qty),0); ?></th>
                        <th align="right"><? echo number_format((array_sum($buyer_wise_prev_order_val)-array_sum($buyer_wise_prev_exp_val))/$conver_to_million,2); ?></th>
                        <th align="right"><? echo number_format(array_sum($buyer_wise_curr_order_qty),0); ?></th>
                        <th align="right"><? echo number_format(array_sum($buyer_wise_curr_order_val)/$conver_to_million,2); ?></th>
                        
                        <th width="80" align="right"><? echo number_format((array_sum($buyer_wise_prev_order_qty)-array_sum($buyer_wise_prev_exp_qty))+array_sum($buyer_wise_curr_order_qty),0); ?></th>
                        
                        <th width="80" align="right"><? echo number_format(((array_sum($buyer_wise_prev_order_val)-array_sum($buyer_wise_prev_exp_val))+array_sum($buyer_wise_curr_order_val))/$conver_to_million,2); ?></th>


                        <th align="right"><? echo number_format(array_sum($buyer_wise_curr_exp_qty),0); ?></th>
                        <th align="right"><? echo number_format(array_sum($buyer_wise_curr_exp_val)/$conver_to_million,2); ?></th>


                        <th width="80" align="right">
						<? echo number_format($total_pending_qty,0);//echo number_format((array_sum($buyer_wise_prev_order_qty)-array_sum($buyer_wise_prev_exp_qty))+array_sum($buyer_wise_curr_order_qty)-array_sum($buyer_wise_curr_exp_qty),0); ?>
                        </th>
                        <th width="80" align="right">
						<? echo number_format($total_pending_val/$conver_to_million,2);//echo number_format((array_sum($buyer_wise_prev_order_val)-array_sum($buyer_wise_prev_exp_val))+array_sum($buyer_wise_curr_order_val)-array_sum($buyer_wise_curr_exp_val),2); ?>
                        </th>
                    </tr>
            </tbody>
         </table>
         </div>        
        
        
         <!--//Month wise.............................................-->
        
        <div style="width:1390px;margin:5px;">
        <table cellspacing="0" width="1380" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
					<th colspan="17"><b style="float:left">Month Wise Capacity Booked Summary</b></th>
               </tr>
               <tr>
					<th align="center" colspan="13">Order Status</th>
					<th align="center" colspan="4">Export status &nbsp;<? echo $s_date.' To '. $e_date ?></th>
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

						$rowspan=count($order_data_array[$year_month])+1;
						if(count($order_data_array[$year_month])==0){$rowspan=2;}

						$fn = "change_color('trm_".$i."','".$bgcolor."')";
						echo '
						<tr bgcolor="'.$bgcolor.'" id="trm_'.$i.'"  onclick="'.$fn.'">
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
							
							if($tot_balance_exfact_qty>0){// negative value not allow;
								$buyer_tot_balance_exfact_qty_arr[$year_month]+=$tot_balance_exfact_qty;
							}
							if($tot_balance_exfact_value>0){// negative value not allow;
								$buyer_tot_balance_exfact_val_arr[$year_month]+=$tot_balance_exfact_value;
							}

						 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						 $fn = "change_color('trm_".$i."','".$bgcolor."')";



						 if($tr!=0){echo '<tr bgcolor="'.$bgcolor.'" id="trm_'.$i.'"  onclick="'.$fn.'">';}
						 ?>
                        <td width="130"><? echo $buyer_arr[$buyer_id]; ?></td>
                        <td width="80" align="right"><? echo $project_qty; ?></td>
                        <td width="80" align="right"><? echo $confirm_qty; ?></td>
                        <td width="80" align="right"><? echo $tot_po_qty_pcs; ?></td>
                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id]/$conver_to_million,2); ?></td>
                        <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$buyer_id]/$conver_to_million,2); ?></td>
						<td width="80" align="right"><? echo number_format($tot_conf_proj_val/$conver_to_million,2); ?></td>
                        <td width="60" align="right"><? echo number_format($avgSMV,2); ?></td>
                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
						<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
                        <td width="60" align="right"><? echo number_format($bookedParcent,2); ?></td>
						<td width="80" align="right"><? echo number_format($buyer_exfact_qty,0); ?></td>
						<td width="80" align="right"><? echo number_format($buyer_exfact_value/$conver_to_million,2); ?></td>
						<td width="80" align="right"><? echo number_format($tot_balance_exfact_qty,0); ?></td>
						<td width="" align="right"><? echo number_format($tot_balance_exfact_value/$conver_to_million,2); ?></td>
                    </tr>
                    <? $tr++;$i++;} ?>
                    <tr bgcolor="#DDD">
                        <td align="right"><b>Total:<b></td>
                        <td align="right"><b><? echo number_format($projected_qty_arr[$year_month],0); ?></b></td>
                        <td align="right"><b><? echo number_format($confirm_qty_arr[$year_month],0); ?></b></td>
                        <td align="right"><b><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></b></td>
                        <td align="right"><b><? echo number_format($projected_value_arr[$year_month]/$conver_to_million,2); ?></b></td>
                        <td align="right"><b><? echo number_format($confirm_value_arr[$year_month]/$conver_to_million,2); ?></b></td>

						<td align="right"><b><? echo number_format(($projected_value_arr[$year_month]+$confirm_value_arr[$year_month])/$conver_to_million,2); ?></b></td>


                        <td align="right"><b><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></b></td>
                         <td align="right"><b><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></b></td>
                        <td align="right"><b><? echo number_format($booked_mints_arr[$year_month],2); ?></b></td>
						 <td align="right"><b><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></b></td>
                        <td title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><b><? echo number_format($booked_parcent_arr[$year_month],2); ?></b></td>
						 <td align="right"><b><? echo number_format($buyer_exfact_qty_mon_arr[$year_month],0); ?></b></td>
						 <td align="right"><b><? echo number_format($buyer_exfact_value_mon_arr[$year_month]/$conver_to_million,2); ?></b></td>
						 <td align="right"><b><? echo number_format($buyer_tot_balance_exfact_qty_arr[$year_month],0); ?></b></td>
						 <td align="right"><b><? echo number_format($buyer_tot_balance_exfact_val_arr[$year_month]/$conver_to_million,2); ?></b></td>
                    </tr>
					<?
					} 
					?>
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


	$conver_to_million=str_replace ("'","",$conver_to_million);
	$cbo_buyer_id=str_replace ("'","",$cbo_buyer_id);
 		

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	//$buyer_arr	= return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$buyer_arr	= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;

	if($db_type==2)
	{
		$s_date	= change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date	= change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	$tot_month = datediff( 'm', $s_date,$e_date);


	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month	= month_add($s_date,$i);
		$month_arr[]= date("Y-m",strtotime($next_month));
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

	  $sql="select a.comapny_id, a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent, a.avg_machine_line, a.prod_category_id, b.no_of_line, (c.working_day) as working_day, c.month_id, c.capacity_month_min, c.capacity_month_pcs
	 from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c
	 where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name  and c.month_id between 1 and 12 and a.is_deleted=0 and a.status_active=1 group by a.comapny_id, a.location_id, a.year,a.effi_percent, a.basic_smv, a.avg_machine_line, a.prod_category_id, b.no_of_line, c.working_day, c.month_id, c.capacity_month_min, c.capacity_month_pcs";
	 //and c.month_id between $cbo_month and $cbo_month_end

	$sql_data_smv=sql_select($sql);
	$tot_row_data_smv=count($sql_data_smv);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
		$capacity_arr[$date_key]['efficency']+=$row[csf("effi_percent")];
		$capacity_arr[$date_key]['basic_smv']+=$row[csf("basic_smv")];
		$capacity_arr[$date_key]['avg_machine_line']+=$row[csf("avg_machine_line")];
		//$capacity_arr[$date_key]['working_day']+=$row[csf("working_day")];
		$capacity_arr[$date_key]['working_day']=$row[csf("working_day")];
		$capacity_arr[$date_key]['no_of_line']+=$row[csf("no_of_line")];
		$capacity_arr[$date_key]['tot_mc_val']+=$row[csf("no_of_line")]*$row[csf("avg_machine_line")];
		$capacity_arr[$date_key]['capacity_month_min']+=$row[csf("capacity_month_min")];
		$capacity_arr[$date_key]['capacity_month_pcs']+=$row[csf("capacity_month_pcs")];

		$prodCategoryArr[$date_key][$row[csf("prod_category_id")]] = $row[csf("prod_category_id")];
		$location_id_arr[$date_key].=$row[csf("location_id")].',';
	}
	unset($sql_data_smv);
	//echo "<pre>";
	//print_r($location_id_arr);die;

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
//	echo "<pre>";print_r($other_costing_arr);
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


	if($cbo_buyer_id !=''){$buyer_cond=" AND a.buyer_id in($cbo_buyer_id)";}
	
	$sql="select a.id  as quotation_id, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom, a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.price_with_commn_dzn, b.total_cost, b.cm_cost_percent, b.asking_quoted_price_percent
	from wo_price_quotation a, wo_price_quotation_costing_mst b
	where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND a.company_id in($cbo_company_name) $buyer_cond and a.offer_qnty>0  AND a.est_ship_date between '$s_date' and '$e_date'
	order  by a.est_ship_date,a.id ";
	$sql_quot_result=sql_select($sql);
	$all_quot_id="";
	foreach($sql_quot_result as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("est_ship_date")]));

		if($all_quot_id=="") $all_quot_id=$row[csf("quotation_id")]; else $all_quot_id.=",".$row[csf("quotation_id")];
		$style_wise_arr[$row[csf("style_ref")]]['costing_per']	=$row[csf("costing_per")];
		$style_wise_arr[$row[csf("style_ref")]]['gmts_item_id']	=$row[csf("gmts_item_id")];
		$style_wise_arr[$row[csf("style_ref")]]['shipment_date'].=$row[csf('est_ship_date')].',';

		$style_wise_arr[$row[csf("style_ref")]]['buyer_name']	=$row[csf("buyer_id")];
		$offer_qnty_pcs=$row[csf('offer_qnty')]*$row[csf('ratio')];


		$order_data_array[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]+=0;

		$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['cm_cost_percent']+=$row[csf("cm_cost_percent")];
		$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['asking_quoted_price_percent']+=$row[csf("asking_quoted_price_percent")];
		$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['cm_count_no']+=1;
		/*$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]][$row[csf("quotation_id")]]['cm_cost_percent']+=$row[csf("cm_cost_percent")];
		$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]][$row[csf("quotation_id")]]['total_cost_percent']+=$row[csf("total_cost_percent")];*/



		$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['offer_qnty']+=$row[csf("offer_qnty")];
		$order_data_array_not_quot_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['offer_value']+=$row[csf("offer_qnty")]*$row[csf("price_with_commn_pcs")];


		$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['offer_qnty']+=$row[csf("offer_qnty")];
		$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['price_with_commn_dzn']=$row[csf("offer_qnty")]*$row[csf("price_with_commn_pcs")];

		$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
		$quot_wise_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['costing_per_id']=$row[csf("costing_per_id")];

	}
	unset($sql_quot_result);

	//echo "<pre>";
	//print_r($quot_wise_arr);die;

	/*select a.id as quotation_id, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom, a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.price_with_commn_dzn, b.total_cost, b.cm_cost_percent, b.asking_quoted_price_percent from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND a.company_id in(1) and a.offer_qnty>0 AND a.est_ship_date between '01-Jun-2018' and '31-Dec-2018' order by a.id */
	
	$sql_quotation="select a.id as quotation_id, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.offer_qnty, a.quot_date, a.est_ship_date,
	b.price_with_commn_dzn, b.total_cost, b.cm_cost, b.cm_cost_percent
	from wo_price_quotation a, wo_price_quotation_costing_mst b
	where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND a.company_id in($cbo_company_name) $buyer_cond and a.offer_qnty>0 and b.cm_cost>0 AND a.est_ship_date between '$s_date' and '$e_date'
	order  by a.id ";
	$sql_quotationt_result = sql_select($sql_quotation);
	$costing_pre_arr = array(1=>'12',2=>'1',3=>'24',4=>'36',5=>'48');
	foreach($sql_quotationt_result as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("est_ship_date")]));
		$unitPrice = ($row[csf("offer_qnty")]/$costing_pre_arr[$row[csf('costing_per')]]);

		$quoted_data_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['cm_cost'] += $unitPrice*$row[csf("cm_cost")];
		$quoted_data_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['quted_value'] += $unitPrice*$row[csf("price_with_commn_dzn")];
		$quoted_data_arr[$date_key][$row[csf("gmts_item_id")]][$row[csf('buyer_id')]]['total_cost'] += $unitPrice* $row[csf("total_cost")];
	}
	unset($sql_quotationt_result);
	
	
	if($cbo_buyer_id !=''){$buyer_cond=" AND a.buyer_name in($cbo_buyer_id)";}
	
	if($cbo_date_cat_id==1)
	{
		 $sql_prod="select a.buyer_name,c.item_number_id as item_id,
			c.po_break_down_id as po_id,c.production_date,
			(CASE WHEN c.production_type=5 THEN c.production_quantity ELSE 0 END) as  sewing_output

		from wo_po_details_master a,pro_garments_production_mst c,wo_po_break_down b
		WHERE a.job_no=b.job_no_mst and c.po_break_down_id=b.id AND b.pub_shipment_date between '$s_date' and '$e_date' AND a.company_name in($cbo_company_name) $buyer_cond  $locatin_cond  and c.status_active=1 and a.is_deleted=0  and b.is_deleted=0 order by c.production_date";
		$prod_dataArray=sql_select($sql_prod);
		foreach($prod_dataArray as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("production_date")]));
			$sewing_out_qnty_array[$date_key][$row[csf('item_id')]][$row[csf('buyer_name')]]['sewing_output']+=$row[csf('sewing_output')];
			//$summary_prod_qty_array[$date_key]['prod_qty']+= $row[csf('sewing_output')];
		}
		unset($prod_dataArray);

		  $sql_prod_deli="select a.buyer_name,c.item_number_id as item_id,
			c.po_break_down_id as po_id,b.pub_shipment_date,c.ex_factory_date,
			(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as  factory_qnty

		from wo_po_details_master a,pro_ex_factory_mst c,wo_po_break_down b
		WHERE a.job_no=b.job_no_mst and c.po_break_down_id=b.id AND b.pub_shipment_date between '$s_date' and '$e_date' AND a.company_name in($cbo_company_name) $buyer_cond  $locatin_cond  and c.status_active=1 and a.is_deleted=0  and b.is_deleted=0 ";
		$deli_dataArray=sql_select($sql_prod_deli);
		foreach($deli_dataArray as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("ex_factory_date")]));
			$delivery_qnty_array[$date_key][$row[csf('item_id')]][$row[csf('buyer_name')]]['factory_qnty']+=$row[csf('factory_qnty')];
			//$summary_prod_qty_array[$date_key]['ship_qty']+= $row[csf('factory_qnty')];

		}
		unset($deli_dataArray);
	}
	else
	{
		 
		 $sql_prod="select a.buyer_name,d.item_number_id as item_id,
			d.po_break_down_id as po_id,d.country_ship_date as pub_shipment_date,e.production_date,
			(CASE WHEN c.production_type=5 THEN c.production_qnty ELSE 0 END) as  sewing_output

		from wo_po_details_master a,pro_garments_production_mst e,pro_garments_production_dtls c,wo_po_break_down b,wo_po_color_size_breakdown d
		WHERE a.job_no=b.job_no_mst and a.job_no=d.job_no_mst and d.po_break_down_id=b.id and e.id=c.mst_id and e.po_break_down_id=b.id and d.po_break_down_id=e.po_break_down_id and c.color_size_break_down_id=d.id AND d.country_ship_date between '$s_date' and '$e_date' AND a.company_name in($cbo_company_name) $buyer_cond  $locatin_cond  and c.status_active=1  and d.status_active=1 and a.is_deleted=0  and b.is_deleted=0 ";
		$prod_dataArray=sql_select($sql_prod);
		foreach($prod_dataArray as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("production_date")]));
			$sewing_out_qnty_array[$date_key][$row[csf('item_id')]][$row[csf('buyer_name')]]['sewing_output']+=$row[csf('sewing_output')];
			//$summary_prod_qty_array[$date_key]['prod_qty']+= $row[csf('sewing_output')];
		}
		unset($prod_dataArray);

		$sql_prod_deli="select a.buyer_name,c.item_number_id as item_id,
			c.po_break_down_id as po_id,e.country_ship_date as pub_shipment_date,c.ex_factory_date,
			(CASE WHEN c.entry_form!=85 THEN d.production_qnty ELSE 0 END) as  factory_qnty
		from wo_po_details_master a,pro_ex_factory_mst c,pro_ex_factory_dtls d,wo_po_break_down b,wo_po_color_size_breakdown e
		WHERE a.job_no=b.job_no_mst and  a.job_no=e.job_no_mst and c.po_break_down_id=b.id and c.id=d.mst_id and d.color_size_break_down_id=e.id AND e.country_ship_date between '$s_date' and '$e_date' AND a.company_name in($cbo_company_name) $buyer_cond  $locatin_cond  and c.status_active=1 and e.status_active=1 and a.is_deleted=0  and b.is_deleted=0 ";
		$deli_dataArray=sql_select($sql_prod_deli);
		foreach($deli_dataArray as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("ex_factory_date")]));
			$delivery_qnty_array[$date_key][$row[csf('item_id')]][$row[csf('buyer_name')]]['factory_qnty']+=$row[csf('factory_qnty')];
			//$summary_prod_qty_array[$date_key]['ship_qty']+= $row[csf('factory_qnty')];
		}
		unset($deli_dataArray);
	}

	if($cbo_date_cat_id==1)
	{
		$sql_po="SELECT a.job_no, a.set_smv, a.set_break_down, a.quotation_id, a.buyer_name, a.total_set_qnty, a.gmts_item_id as item_number_id,
		b.id as po_id, b.pub_shipment_date, b.unit_price,
		(b.po_quantity/a.total_set_qnty) as po_quantity,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst and  a.company_name in($cbo_company_name) $buyer_cond $locatin_cond and b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   order by b.pub_shipment_date ";
	}
	else //Country Ship Date
	{
		$sql_po="SELECT a.job_no,a.set_smv,a.set_break_down,a.quotation_id,a.buyer_name,a.total_set_qnty,b.id as po_id, c.country_ship_date as pub_shipment_date,b.unit_price,c.item_number_id,
		(c.order_quantity/a.total_set_qnty) as po_quantity,
		(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.company_name in($cbo_company_name) $buyer_cond $locatin_cond and c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 order by c.country_ship_date";
	}
	//echo $sql_po;
	
	$sql_data=sql_select($sql_po);
	$summary_data_array=array();

	//print_r($sewing_out_qnty_array);
	foreach( $sql_data as $row)
	{

		$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];

		$gmt_prod_buyer_arr[$row[csf('po_id')]]['buyer']=$row[csf('buyer_name')];

		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		if($cbo_date_cat_id==1)
		{
			$item_id		= $row[csf('item_number_id')];

			$test_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['lab_test'];
			
			$freight_cost	= $other_costing_arr[$row[csf('po_id')]][$item_id]['freight'];
			$inspection		= $other_costing_arr[$row[csf('po_id')]][$item_id]['inspection'];
			$certificate_cost = $other_costing_arr[$row[csf('po_id')]][$item_id]['certificate_pre_cost'];
			$common_oh		= $other_costing_arr[$row[csf('po_id')]][$item_id]['common_oh'];
			$currier_cost	= $other_costing_arr[$row[csf('po_id')]][$item_id]['currier_pre_cost'];
			$margin_pcs_set		= $other_costing_arr[$row[csf('po_id')]][$item_id]['margin_pcs_set'];
			//$sewing_out=$sewing_out_qnty_array[$date_key][$item_id][$row[csf('buyer_name')]]['sewing_output'];
			$delivery_qnty=$delivery_qnty_array[$date_key][$item_id][$row[csf('buyer_name')]]['factory_qnty'];

			$cm_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['cm_cost'];
			$cm_value_order_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['cm_cost']+=$cm_cost;
			$margin_value_order_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['margin_cost']+=$margin_pcs_set;

			$tot_other_cost		= $test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost;

			$conversion_cost	= array_sum($conversion_costing_arr[$row[csf('po_id')]][$item_id]);
			$trim_cost			= $trims_costing_arr[$row[csf('po_id')]][$item_id];

			$fab_purchase_knit	= array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$item_id]);
			$fab_purchase_woven	= array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);

			$emblishment_cost	= $emblishment_costing_arr[$row[csf('po_id')]][$item_id]+$emblishment_costing_arr_name_wash[$row[csf('po_id')]][$item_id];
			$commercial_cost	= $commercial_costing_arr[$row[csf('po_id')]][$item_id];
			$commission_cost	= $commission_costing_arr[$row[csf('po_id')]][$item_id];
			$yarn_cost			= $yarn_costing_arr[$row[csf('po_id')]][$item_id];

			$total_cost = $conversion_cost+$trim_cost+$fab_purchase_knit+$fab_purchase_woven+$emblishment_cost+$commercial_cost+$yarn_cost+$commission_cost+$tot_other_cost;

			$confirm_qty	= $row[csf('confirm_qty')];
			$project_qty	= $row[csf('projected_qty')];

			$confirm_value	= $row[csf('confirm_value')];
			$project_value	= $row[csf('projected_value')];

			$po_qty_set_smv = $row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv = $po_qty_set_smv/$row[csf('po_quantity')];
			//echo $date_key.'='.$item_id.'='.$row[csf('buyer_name')].'A'.$row[csf('confirm_qty')];
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['confQty']+=$row[csf('confirm_qty')];
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['margin_pcs']+=$margin_pcs_set;

			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['projQty']+=$row[csf('projected_qty')];
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['sewing_out']+=$sewing_out;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['delivery_qnty']+=$delivery_qnty;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['balance_shipout']+=$confirm_qty-$delivery_qnty;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['carray_forwad']+=$carray_forwad;
			$projected_order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]			+=$project_qty;
			$total_cost_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['total_cost']	+=$total_cost;

			$order_data_array_qty[$date_key][$item_id][$row[csf('buyer_name')]]['confQty']+=$row[csf('confirm_qty')];

			if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty']>0){
				$quotation_data_arr[$date_key][$item_id][$row[csf('buyer_name')]]['quoted_qty'] = $quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty'];
			}

			if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn']>0){
				$quotation_data_arr[$date_key][$item_id][$row[csf('buyer_name')]]['quoted_val'] = $quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn'];
			}


			$job_no_array[$date_key][$item_id][$row[csf('buyer_name')]]['job'].=$row[csf('job_no')].',';

			$confirm_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]	+= $confirm_qty;
			$projected_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]	+= $project_qty;

			$confirm_value_array[$date_key][$item_id][$row[csf('buyer_name')]]	+= $rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$item_id][$row[csf('buyer_name')]]+= $rate_in_pcs*$project_qty;

			$summary_confirm_value_array[$date_key]		+= $rate_in_pcs*$confirm_qty;
			$summary_projected_value_array[$date_key]	+= $rate_in_pcs*$project_qty;


			$booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]				+= ($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]	+= ($smv*$project_qty);

			$summary_booked_smv_mints_arr[$date_key]			+= ($smv*$confirm_qty);
			$summary_projected_booked_smv_mints_arr[$date_key]	+= ($smv*$project_qty);

			//$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$date_year_id=date("Y",strtotime($date_key));
		}
		else
		{
			//$item_id=$set_break_down_arr[0];
			$set	= $set_break_down_arr[1];
			$smv	= $set_break_down_arr[2];

			$confirm_qty	= $row[csf('confirm_qty')];
			$project_qty	= $row[csf('projected_qty')];
			$confirm_value	= $row[csf('confirm_value')];
			$project_value	= $row[csf('projected_value')];
			$item_id		= $row[csf('item_number_id')];

			$test_cost			= $other_costing_arr[$row[csf('po_id')]][$item_id]['lab_test'];
			$freight_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['freight'];
			$inspection			= $other_costing_arr[$row[csf('po_id')]][$item_id]['inspection'];
			$certificate_cost	= $other_costing_arr[$row[csf('po_id')]][$item_id]['certificate_pre_cost'];
			$common_oh			= $other_costing_arr[$row[csf('po_id')]][$item_id]['common_oh'];
			$currier_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['currier_pre_cost'];
			$margin_pcs_set		= $other_costing_arr[$row[csf('po_id')]][$item_id]['margin_pcs_set'];

			$sewing_out=$sewing_out_qnty_array[$date_key][$item_id][$row[csf('buyer_name')]]['sewing_output'];
			$delivery_qnty=$delivery_qnty_array[$date_key][$item_id][$row[csf('buyer_name')]]['factory_qnty'];

			$cm_cost			= $other_costing_arr[$row[csf('po_id')]][$item_id]['cm_cost'];
			$cm_value_order_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['cm_cost']=$cm_cost;
			$margin_value_order_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['margin_cost']+=$margin_pcs_set;
			$tot_other_cost		= $test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost;
			$conversion_cost	= array_sum($conversion_costing_arr[$row[csf('po_id')]][$item_id]);
			$trim_cost			= $trims_costing_arr[$row[csf('po_id')]][$item_id];
			$fab_purchase_knit	= array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$item_id]);
			$fab_purchase_woven	= array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);
			$emblishment_cost	= $emblishment_costing_arr[$row[csf('po_id')]][$item_id]+$emblishment_costing_arr_name_wash[$row[csf('po_id')]][$item_id];
			$commercial_cost	= $commercial_costing_arr[$row[csf('po_id')]][$item_id];
			$commission_cost	= $commission_costing_arr[$row[csf('po_id')]][$item_id];
			$yarn_cost			= $yarn_costing_arr[$row[csf('po_id')]][$item_id];
			$total_cost			= $conversion_cost+$trim_cost+$fab_purchase_knit+$fab_purchase_woven+$emblishment_cost+$commercial_cost+$yarn_cost+$commission_cost+$tot_other_cost;
			$total_cost_item_array[$date_key][$item_id][$row[csf('buyer_name')]]['total_cost']=$total_cost;
			$po_qty_set_smv		= $row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv				= $po_qty_set_smv/$row[csf('po_quantity')];

			$carray_forwad=$confirm_qty-$sewing_out;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['confQty']+=$confirm_qty;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['projQty']+=$project_qty;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['sewing_out']+=$sewing_out;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['delivery_qnty']+=$delivery_qnty;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['balance_shipout']+=$confirm_qty-$delivery_qnty;
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['carray_forwad']+=$carray_forwad;
			
			$order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]['margin_pcs']+=$margin_pcs_set;

			$order_data_array_qty[$date_key][$item_id][$row[csf('buyer_name')]]['confQty']+=$row[csf('confirm_qty')];
			$projected_order_data_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$item_id][$row[csf('buyer_name')]]+=$project_qty;

			if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty']>0){
				$quotation_data_arr[$date_key][$item_id][$row[csf('buyer_name')]]['quoted_qty']+=$quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty'];
			}

			if($quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn']>0){
				$quotation_data_arr[$date_key][$item_id][$row[csf('buyer_name')]]['quoted_val']+=$quot_wise_arr[$date_key][$item_id][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn'];
			}





		//	$summary_confirm_qty_array[$date_key]	+= $confirm_qty;
			//$summary_projected_qty_array[$date_key]	+= $project_qty;
			$confirm_value_array[$date_key][$item_id][$row[csf('buyer_name')]]		+= $confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$item_id][$row[csf('buyer_name')]]	+= $project_value;//$rate_in_pcs*$project_qty;

			$summary_confirm_value_array[$date_key]		+= $confirm_value;//$rate_in_pcs*$confirm_qty;
			$summary_projected_value_array[$date_key]	+= $project_value;//$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]	+= ($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$item_id][$row[csf("buyer_name")]]+=($smv*$project_qty);
			$summary_booked_smv_mints_arr[$date_key]			+= ($smv*$confirm_qty);
			$summary_projected_booked_smv_mints_arr[$date_key]	+= ($smv*$project_qty);
			//$booked_parcent_arr[$date_key]+=((($smv*$confirm_qty)+($smv*$project_qty))/$capacity_arr[$date_key]['capacity_month_min'])*100;
			$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;

		}
	}
	unset($sql_data);


	//print_r($summary_confirm_qty_array);

	foreach($order_data_array as $year_month=>$item_data) // for row span
	{
		$mon_row_span=0;
		 foreach($item_data as $item_id=>$buyer_data)
		 {
			$item_row_span=0;
			foreach($buyer_data as $buyer_id=>$conf_qty)
			{
				$mon_row_span++;
				$item_row_span++; 
				$cm_value_summ=$cm_value_order_item_array[$year_month][$item_id][$buyer_id]['cm_cost'];
				//$margin_pcs_value_summ=$order_data_array[$year_month][$item_id][$buyer_id]['margin_pcs'];
				$margin_pcs_value_summ=$margin_value_order_item_array[$year_month][$item_id][$buyer_id]['margin_cost'];
				$factory_qnty=$delivery_qnty_array[$year_month][$item_id][$buyer_id]['factory_qnty'];
				$sewing_output=$sewing_out_qnty_array[$year_month][$item_id][$buyer_id]['sewing_output'];
				$summary_confirm_qty_array[$year_month]+=$order_data_array_qty[$year_month][$item_id][$buyer_id]['confQty'];
				$summary_projected_qty_array[$year_month]+=$projected_qty_array[$year_month][$item_id][$buyer_id];
				$summary_prod_qty_array[$year_month]['prod_qty']+=$sewing_output;
				$summary_prod_qty_array[$year_month]['ship_qty']+=$factory_qnty;
				$summary_prod_qty_array[$year_month]['cm_value_summ']+=$cm_value_summ;
				$summary_prod_qty_array[$year_month]['margin_pcs_value_summ']+=$margin_pcs_value_summ;

			}
			$item_rowspan_arr[$year_month][$item_id]=$item_row_span;
			$mon_rowspan_arr[$year_month]=$mon_row_span;
		 }
	}
	//print_r($summary_order_data_array);


	$sql_data="select pd.plan_qnty as pdplan_qnty,b.pub_shipment_date from wo_po_break_down b,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  b.id=c.po_break_down_id and pd.plan_id=c.plan_id and c.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between  '$s_date' and '$e_date' order by b.pub_shipment_date";
	$data_result=sql_select($sql_data);

	$plan_qty_arr=array();
	foreach( $data_result as $row)
	{
		$plan_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]+=$row[csf("pdplan_qnty")];
	}
	unset($data_result);

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
        <div style="width:2200px;" id="scroll_body">
        <table cellspacing="0" width="2200" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="30">Monthly Capacity Information</th>
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

					<th width="60">Production Qty</th>
					<th width="60">Ship Out</th>
					<th width="60">Balance Shipout</th>
					<th width="60">Carry forwards from</th>

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
                    <th width="100">Less Booking %</th>
                    
                    <th width="100">CM Value</th>
                    <th width="100">Margin value</th>
                    <th width="">CM and margin value</th>
                    
                </tr>
            </thead>
            <tbody>
				 <? $i=1;
                 foreach($month_arr as $year_month){
					list($year,$month)	= explode('-',$year_month);
					$bgcolor			=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$tot_mc_val			= $capacity_arr[$year_month]['tot_mc_val'];
					$totalMechine		= $tot_mc_val;//$capacity_arr[$year_month]['no_of_line']*$capacity_arr[$year_month]['avg_machine_line'];
					$capacityMints		= $totalMechine*$working_hour_arr[$year_month]*$capacity_arr[$year_month]['working_day']*60;
					$less_over 			= $over_parcent = $less_parcent = '';
					$less_over			= $booked_parcent_arr[$year_month]-100;
					if($less_over>0){$over_parcent=number_format($less_over,2);}
					else{$less_parcent 	= number_format($less_over,2);}

					$location_ids		= rtrim($location_id_arr[$year_month],',');

					$location_ids		= array_unique(explode(",",$location_ids));
					$tot_location		= count($location_ids);
					$cm_value_summ=$summary_prod_qty_array[$year_month]['cm_value_summ'];
					$margin_pcs_value_summ=$summary_prod_qty_array[$year_month]['margin_pcs_value_summ'];

					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="70"><b><? echo $months[$month*1].'-'.$year; ?></b></td>

				    <td width="60" align="right"><? echo $totalMechine; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['no_of_line']; ?></td>

                    <td width="60" align="right"><? echo $working_hour_arr[$year_month]; ?></td>
                    <td width="60" align="right"><? echo number_format( ($capacity_arr[$year_month]['efficency']/$tot_location)/count($prodCategoryArr[$year_month]),2); ?></td>
                    <td width="60" align="right"><?
					//echo number_format($capacity_arr[$year_month]['working_day']/$tot_location);
					echo number_format($capacity_arr[$year_month]['working_day']);
					?></td>
                    <td width="60" align="right"><? echo $capacityMints; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_pcs']; ?></td>
                    <td width="60" align="right"><? echo $capacity_arr[$year_month]['capacity_month_min']; ?></td>

                    <td width="60" align="right"><? echo $summary_projected_qty_array[$year_month]; ?></td>
                    <td width="60" align="right"><? echo $summary_confirm_qty_array[$year_month]; ?></td>
					<td width="60" align="right"><? echo $summary_prod_qty_array[$year_month]['prod_qty']; ?></td>
					<td width="60" align="right"><? echo $summary_prod_qty_array[$year_month]['ship_qty']; ?></td>
					<td width="60" align="right" title="Confirm Qty-Ship Qty"><? echo $summary_confirm_qty_array[$year_month]-$summary_prod_qty_array[$year_month]['ship_qty']; ?></td>
					<td width="60" align="right" title="Confirm Qty-Prod. Qty"><? echo $summary_confirm_qty_array[$year_month]-$summary_prod_qty_array[$year_month]['prod_qty']; ?></td>

					 <td width="100" align="right">
					<?
					$projectedPlusConfirmedQty = $summary_projected_qty_array[$year_month] + $summary_confirm_qty_array[$year_month];
					echo number_format($projectedPlusConfirmedQty,2);
					?>
                     </td>

					<td width="60" align="right"><? echo number_format($summary_projected_value_array[$year_month]/$conver_to_million,4); ?></td>
                    <td width="60" align="right"><? echo number_format($summary_confirm_value_array[$year_month]/$conver_to_million,4); ?></td>

					<td width="60" align="right"><?
					$tot_conf_proj_value = $summary_confirm_value_array[$year_month] + $summary_projected_value_array[$year_month];
					 echo number_format($tot_conf_proj_value/$conver_to_million,4); ?></td>


                    <td width="60" align="right"><? echo number_format($summary_projected_booked_smv_mints_arr[$year_month],2); ?></td>
                    <td width="60" align="right"><? echo number_format($summary_booked_smv_mints_arr[$year_month],2); ?></td>
                     <td width="60" align="right">
					<?
					$projectedPlusConfirmedMint = $summary_projected_booked_smv_mints_arr[$year_month] + $summary_booked_smv_mints_arr[$year_month];
					echo number_format($projectedPlusConfirmedMint,2);
					 ?>
                    </td>

                    <td width="80" align="right"><? echo $plan_qty_arr[$year_month]; ?></td>
                    <td width="80" align="right"><? echo $summary_confirm_qty_array[$year_month]-$plan_qty_arr[$year_month]; ?></td>


                    <td width="60" align="right"><? echo number_format($booked_parcent_arr[$year_month],2); ?></td>
                    <td width="60" align="right"><? echo number_format($over_parcent,2); ?></td>
                    <td width="100" align="right"><? echo number_format($less_parcent,2); ?></td>
                     <td width="100" align="right"><? echo number_format($cm_value_summ,2); ?></td>
                     <td width="100" align="right"><? echo number_format($margin_pcs_value_summ,2); ?></td>
                     <td width="" title="CM Value+Margin Value" align="right"><? $cm_margin_value=$cm_value_summ+$margin_pcs_value_summ;echo number_format($cm_margin_value,2); ?></td>

                </tr>
                <?
				$i++;
				}
				?>
            </tbody>
         </table>
         </div>

        <!--//Order information.............................................-->
        <br /><br />
        <div style="width:2250px;">
        <table cellspacing="0" width="2230" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
               <tr>
               		<th colspan="28">Item and Buyer Wise Capacity Booked Summary</th>
               </tr>
                <tr>
                    <th width="70">Month</th>
                    <th width="100">Item</th>
                    <th width="130">Buyer</th>
                    <th width="80">Quoted Qty</th>
                    <th width="80">Projected Qty (Pcs)</th>
                    <th width="80">Confirmed Qty (Pcs)</th>

					<th width="80">Production Qty</th>
					<th width="80">Ship Out</th>
					<th width="80">Balance Shipout</th>
					<th width="80">Carry forwards from </th>

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
                    <th width="70">Pre-costing<br />CM %</th>
					<th width="70">Pre-Costing  CM Value</th>
                    <th width="70">Pre-costing<br />RM %</th>
					<th width="70">Pre-Costing  RM Value</th>


                    <th width="70">Pre-costing<br />Varience%</th>
                    <th width="70">Price Qut.<br />CM %</th>
                    <th width="70">Price Qtd.<br />RM %</th>
                    <th>Price Qut.<br />Varience </th>
                </tr>
            </thead>
            <tbody>
                     <?
					 $i=1;

					 foreach($order_data_array as $year_month => $item_data)
					 {

						list($year,$month) = explode('-',$year_month);
						$m=1;
						$r = 0;
						foreach($item_data as $item_id => $buyer_data)
						{
							$n=1;

							foreach($buyer_data as $buyer_id => $conf_qty)
							{

								$bookedMints			= $booked_smv_mints_arr[$year_month][$item_id][$buyer_id];
								$projectedbookedMints	= $projected_booked_smv_mints_arr[$year_month][$item_id][$buyer_id];
								$project_qty			= $projected_qty_array[$year_month][$item_id][$buyer_id];
								$confQty				=$order_data_array_qty[$year_month][$item_id][$buyer_id]['confQty'];

								$quoted_qty	= $quotation_data_arr[$year_month][$item_id][$buyer_id]['quoted_qty'];
								$quoted_val	= $quotation_data_arr[$year_month][$item_id][$buyer_id]['quoted_val'];


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

								$quoted_qty_bal	= ($quoted_qty-$confQty);



								$job_no=rtrim($job_no_array[$year_month][$item_id][$buyer_id]['job'],',');
								$job_nos=implode(",",array_unique(explode(",",$job_no)));

								//echo $tot_pre_cost;
								//$total_pre_cost+=$tot_pre_cost;
								//$avgSMV=$bookedMints/$conf_qty;
								$avgSMV = ($bookedMints+$projectedbookedMints)/($confQty+$project_qty);
								//$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
								$bookedParcent=0;
								if($capacity_arr[$year_month]['capacity_month_min'])
								{
									//$bookedParcent = (($bookedMints+$projectedbookedMints)/$capacity_arr[$year_month]['capacity_month_min'])*100;
									$bookedParcent = ($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
									$bookedParcentFormula = "(Confirmed Mints / Factory Capacity Mints) * 100";
								}


								$confirm_qty_arr[$year_month]	+= $confQty;
								$projected_qty_arr[$year_month]	+= $project_qty;


								if($quoted_qty_bal>0){
									$quoted_qty_arr[$year_month]	+= $quoted_qty;
									$quoted_val_arr[$year_month]	+= $quoted_val;
								}


								$confirm_value_arr[$year_month]		+= $confirm_value_array[$year_month][$item_id][$buyer_id];
								$projected_value_arr[$year_month]	+= $projected_value_array[$year_month][$item_id][$buyer_id];

								$booked_mints_arr[$year_month]			+= $bookedMints;
								$projected_booked_mints_arr[$year_month]+= $projectedbookedMints;

								$avg_smg_arr[$year_month]	+= $avgSMV;
								//$booked_parcent_arr[$year_month]+=$bookedParcent;
								$mon_rowspan	= $mon_rowspan_arr[$year_month];
								$item_rowspan	= $item_rowspan_arr[$year_month][$item_id];

								$tot_conf_proj_val	= $projected_value_array[$year_month][$item_id][$buyer_id]+$confirm_value_array[$year_month][$item_id][$buyer_id];

								$cm_value			= $cm_value_order_item_array[$year_month][$item_id][$buyer_id]['cm_cost'];
								$cm_value_percent	= ($cm_value/$tot_conf_proj_val)*100;

								$tot_pre_cost		= $total_cost_item_array[$year_month][$item_id][$buyer_id]['total_cost'];
								$rm_value_percent	= ($tot_pre_cost/$tot_conf_proj_val)*100;

								$totla_cm_val_arr[$year_month]	+= $cm_value;
								$total_cost_rm_arr[$year_month]	+= $tot_pre_cost;



								$pre_cost_variance = 100-($cm_value_percent+$rm_value_percent);

								//$quoted_data_arr[$year_month][$item_id][$buyer_id]['cm_cost'] ;
								//$quoted_data_arr[$year_month][$item_id][$buyer_id]['quted_value'];
								//$quoted_data_arr[$year_month][$item_id][$buyer_id]['total_cost'];

								$price_qut_cm	= ($quoted_data_arr[$year_month][$item_id][$buyer_id]['cm_cost']/$quoted_data_arr[$year_month][$item_id][$buyer_id]['quted_value'])*100;
								$price_qut_rm	= (($quoted_data_arr[$year_month][$item_id][$buyer_id]['total_cost']-$quoted_data_arr[$year_month][$item_id][$buyer_id]['cm_cost'] )/$quoted_data_arr[$year_month][$item_id][$buyer_id]['quted_value'])*100;





								if(($price_qut_cm+$price_qut_rm)>0){
									$price_qut_variance = 100-($price_qut_cm+$price_qut_rm);
									$r = $r+1;
								}else{
									$price_qut_variance = 0;
								}


								$totla_pre_cost_variance[$year_month]	+= $pre_cost_variance;
								$totla_price_qut_cm[$year_month]		+= $price_qut_cm;
								$totla_price_qut_rm[$year_month]		+= $price_qut_rm;
								$totla_price_qut_variance[$year_month]	+= $price_qut_variance;




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
                                    <td width="100" valign="middle" rowspan="<? echo $item_rowspan; ?>"><? echo $garments_item[$item_id]; ?></td>
                                    <?
                                    }
                                    ?>
                                    <td width="130" valign="middle" title="<? echo $job_nos;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                                    <td width="80" align="right"><?
                                    //echo number_format($quoted_qty,0);
                                    if($quoted_qty_bal>0){
                                    echo number_format($quoted_qty_bal,0);
                                    }else{
                                    echo "0";
                                    }
									$sewing_output=$sewing_out_qnty_array[$year_month][$item_id][$buyer_id]['sewing_output'];
									$delivery_qnty=$delivery_qnty_array[$year_month][$item_id][$buyer_id]['factory_qnty'];
                                    //echo number_format($quoted_qty_bal,0);
									$prod_qty_arr[$year_month]+=$sewing_output;
									$delivery_qty_arr[$year_month]+=$delivery_qnty;
									$balance_ship_qty_arr[$year_month]+=$confQty-$delivery_qnty;
									$carry_forward_qty_arr[$year_month]+=$confQty-$sewing_output;

									$cm_value_arr[$year_month]+=$cm_value;
									$pre_cost_rm_value_arr[$year_month]+=$tot_pre_cost-$cm_value;

                                    ?></td>
                                    <td width="80" align="right"><? echo number_format($project_qty,0); ?></td>
                                    <td width="80" align="right" title="<? echo $year_month.'='.$item_id.'='.$buyer_id;?>"><? echo number_format($confQty,0); ?></td>
									<td width="80" align="right"><? echo number_format($sewing_output,0); ?></td>
									<td width="80" align="right"><? echo number_format($delivery_qnty,0); ?></td>
									<td width="80" align="right" title="Confirm-Ship Out Qty"><? echo number_format($confQty-$delivery_qnty,0); ?></td>
									<td width="80" align="right" title="Confirm-Prod. Qty"><? echo number_format($confQty-$sewing_output,0); ?></td>

                                    <td width="80" align="right"><? echo number_format($project_qty+$confQty,0); ?></td>
                                    <td width="80" align="right"><?
                                    //echo number_format($quoted_val,2);
                                    if($quoted_qty_bal>0){
                                    echo number_format($quoted_val,2);
                                    }else{
                                    echo number_format(0,2);
                                    }

                                    ?></td>

                                    <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$item_id][$buyer_id]/$conver_to_million,4); ?></td>
                                    <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$item_id][$buyer_id]/$conver_to_million,4); ?></td>
                                    <td width="80" align="right"><?  echo number_format($tot_conf_proj_val/$conver_to_million,4); ?></td>


                                    <td width="60" align="right"><? echo number_format($avgSMV,2); ?></td>

                                    <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
                                    <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
                                    <td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
                                    <td width="80" align="right" title="<? echo $bookedParcentFormula; ?>"><? echo number_format($bookedParcent,2); ?></td>
                                    <td width="70" align="right" title="(TotalCM/FOB Value*100) Total CM Value = <? echo $cm_value;?>"><? echo number_format($cm_value_percent,2); ?></td>
									<td width="70" align="right" title="(Total CM Value = <? echo $cm_value;?>"><? echo number_format($cm_value,2); ?></td>
                                    <td width="70" align="right" title="(Total Cost-CM/FOB Value*100)<? echo 'Total Cost='.$tot_pre_cost;?>"><? echo number_format($rm_value_percent,2); ?></td>

                                    <td width="70" align="right" title="(Total Cost-Tot CM Val)<? echo 'Total Cost='.$tot_pre_cost;?>"><? echo number_format($tot_pre_cost-$cm_value,2); ?></td>



                                    <td width="70" align="right" ><? echo number_format($pre_cost_variance,2); ?></td>
                                    <td width="70" align="right" ><? echo number_format($price_qut_cm,2); ?></td>
                                    <td width="70" align="right" ><? echo number_format($price_qut_rm,2); ?></td>
                                    <td align="right" ><? echo number_format($price_qut_variance,2); ?></td>

                                </tr>
					<?
								$m++;
								$n++;
								$i++;
							}
						}
					?>
                    <tr bgcolor="#DDD">
                        <th colspan="3">Total:</th>
                        <th align="right"><? echo number_format($quoted_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
						<th align="right"><? echo number_format($prod_qty_arr[$year_month],0); ?></th>
						<th align="right"><? echo number_format($delivery_qty_arr[$year_month],0); ?></th>
						<th align="right"><? echo number_format($balance_ship_qty_arr[$year_month],0); ?></th>
						<th align="right"><? echo number_format($carry_forward_qty_arr[$year_month],0); ?></th>

                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($quoted_val_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($projected_value_arr[$year_month]/$conver_to_million,4); ?></th>
                        <th align="right"><? echo number_format($confirm_value_arr[$year_month]/$conver_to_million,4); ?></th>

                        <th align="right"><? echo number_format($projected_value_arr[$year_month]+$confirm_value_arr[$year_month]/$conver_to_million,4); ?></th>


                        <th align="right"><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></td>
                        <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>
                        <th title="" align="right" <? //if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><?
                        $tot_cm_percent=($totla_cm_val_arr[$year_month]/($projected_value_arr[$year_month]+$confirm_value_arr[$year_month]))*100;
                        $tot_rm_percent=($total_cost_rm_arr[$year_month]/($projected_value_arr[$year_month]+$confirm_value_arr[$year_month]))*100;
                        echo number_format($tot_cm_percent,2); ?></th>
						<th width="70" align="right" ><? echo number_format($cm_value_arr[$year_month],2); ?></th>

                        <th title="" align="right" <? //if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($tot_rm_percent,2); ?></th>
                        <?
                         $tot_pre_cost_variance	=100-($tot_cm_percent+ $tot_rm_percent);
						 $tot_price_qut_cm		=$totla_price_qut_cm[$year_month];
						 $tot_price_qut_rm		=$totla_price_qut_rm[$year_month];
						 $tot_price_qut_variance=$totla_price_qut_variance[$year_month];
						//$cm_value_arr[$year_month]+=$cm_value;
								//	$pre_cost_rm_value_arr[$year_month]
						?>
                        <th width="70" align="right" ><? echo number_format($pre_cost_rm_value_arr[$year_month],2); ?></th>
                        <th width="70" align="right" ><? echo number_format($tot_pre_cost_variance,2); ?></th>
                        <th width="70" align="right" ><? echo number_format($tot_price_qut_cm,2); ?></th>
                        <th width="70" align="right" ><? echo number_format($tot_price_qut_rm,2); ?></th>
                        <th align="right" ><? echo number_format($tot_price_qut_variance/$r,2); ?></th>
                    </tr>

                    <tr bgcolor="#FFFF00">
                        <th colspan="21">
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
                        <th align="right"><? //echo number_format($less_over,2); ?></th>
                        <th align="right"><? //echo number_format($less_over,2); ?></th>
                    </tr>

					<?
					 }
					?>
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

				//$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
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


if($action=="buyer_wise_report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name	= str_replace("'","",$cbo_company_id);
	$location_id		= str_replace("'","",$cbo_location_id);
	$cbo_year_name		= str_replace("'","",$cbo_year_start);
	$cbo_month			= str_replace("'","",$cbo_month_start);
	$cbo_end_year_name	= str_replace("'","",$cbo_year_end);
	$cbo_month_end		= str_replace("'","",$cbo_month_end);
	$cbo_date_cat_id	= str_replace("'","",$cbo_date_cat_id);
	
	$conver_to_million=str_replace ("'","",$conver_to_million);
	$cbo_buyer_id=str_replace ("'","",$cbo_buyer_id);

	$companyArr = 	return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr	=	return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");


	$daysinmonth= cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_end_year_name);
	$s_date		= $cbo_year_name."-".$cbo_month."-"."01";
	$e_date		= $cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;

	if($db_type==2)
	{
		$s_date	= change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date	= change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	$tot_month 	= datediff( 'm', $s_date,$e_date);


	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month	= month_add($s_date,$i);
		$month_arr[] = date("Y-m",strtotime($next_month));
	}
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_id='$location_id'";

	$comp_cond = " and a.company_name in($cbo_company_name)";
	if(str_replace("'","",$cbo_date_cat_id) ==1 && str_replace("'","",$cbo_month_end)!='')
	{
		$date_cond = "and b.pub_shipment_date between  '$s_date' and '$e_date'";
		$dateField = " b.pub_shipment_date as pub_shipment_date, ";
	}
	else if(str_replace("'","",$cbo_date_cat_id) ==2 && str_replace("'","",$cbo_month_end)!='')
	{
		$date_cond = "and c.country_ship_date between  '$s_date' and '$e_date'";
		$dateField = " c.country_ship_date as pub_shipment_date, ";
	}
	else if(str_replace("'","",$cbo_date_cat_id) ==3 && str_replace("'","",$cbo_month_end)!='')
	{
		$date_cond = "and b.shipment_date between  '$s_date' and '$e_date'";
		$dateField = " b.shipment_date as pub_shipment_date, ";
	}

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
	//echo "<pre>";print_r($other_costing_arr);

	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndGmtsitem();
	//echo "<pre>";print_r($conversion_costing_arr); die;

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


	$working_hour_arr=array();
	$sql_fina_param=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where company_id in($cbo_company_name) and status_active=1 and is_deleted=0 order by applying_period_date");
	foreach( $sql_fina_param as $rowf)
	{
		$date_key=date("Y-m",strtotime($rowf[csf("applying_period_date")]));
		$working_hour_arr[$date_key]=$rowf[csf("working_hour")];
	}
	unset($sql_fina_param);
	$captionButtonChange="Quotation Qty";
	
	if($cbo_buyer_id !=''){$buyer_cond=" AND a.buyer_id in($cbo_buyer_id)";}
	
	if($type==4)
	{
		$sql="select a.id  as quotation_id, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom, a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.price_with_commn_dzn, b.total_cost, b.cm_cost_percent, b.asking_quoted_price_percent
		from wo_price_quotation a, wo_price_quotation_costing_mst b
		where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND a.company_id in($cbo_company_name) $buyer_cond and a.offer_qnty>0  AND a.est_ship_date between '$s_date' and '$e_date'
		order  by a.est_ship_date,a.id ";
		$captionButtonChange="Quotation Qty";
	}
	else if($type==7)
	{
		$sql="select a.qc_no as quotation_id, a.buyer_id, a.offer_qty as offer_qnty, a.delivery_date as est_ship_date, b.tot_fob_cost as price_with_commn_pcs
		from qc_mst a, qc_tot_cost_summary b
		where a.qc_no=b.mst_id and a.revise_no=0 and a.option_id=0 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $buyer_cond and a.offer_qty>0  AND a.delivery_date between '$s_date' and '$e_date'
		order  by a.delivery_date,a.qc_no";
		$captionButtonChange="Offer Qty";
	}
	//echo $sql;
	$sql_quot_result=sql_select($sql);
	$all_quot_id="";
	foreach($sql_quot_result as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("est_ship_date")]));

		//$order_data_array_not_quot_arr[$date_key][$row[csf('buyer_id')]]['cm_cost_percent']+=$row[csf("cm_cost_percent")];
		//$order_data_array_not_quot_arr[$date_key][$row[csf('buyer_id')]]['asking_quoted_price_percent']+=$row[csf("asking_quoted_price_percent")];

		$order_data_array_not_quot_arr[$date_key][$row[csf('buyer_id')]]['offer_qnty']+=$row[csf("offer_qnty")];
		$order_data_array_not_quot_arr[$date_key][$row[csf('buyer_id')]]['offer_value']+=$row[csf("offer_qnty")]*$row[csf("price_with_commn_pcs")];

		//$quot_wise_arr[$date_key][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['offer_qnty']+=$row[csf("offer_qnty")];
		//$quot_wise_arr[$date_key][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
		//$quot_wise_arr[$date_key][$row[csf("buyer_id")]][$row[csf("quotation_id")]]['costing_per_id']=$row[csf("costing_per_id")];
		$quot_wise_arr2[$date_key][$row[csf("buyer_id")]]['offer_qnty']+=$row[csf("offer_qnty")];
		//$quot_wise_arr2[$date_key][$row[csf("buyer_id")]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
		//$quot_wise_arr2[$date_key][$row[csf("buyer_id")]]['costing_per_id']=$row[csf("costing_per_id")];
	}
	unset($sql_quot_result);

	//echo "<pre>";
	//print_r($quot_wise_arr);die;

	 $sql="select a.comapny_id,a.location_id, a.year, a.basic_smv, (a.effi_percent) as effi_percent,a.avg_machine_line,b.no_of_line,(c.working_day) as working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($cbo_company_name) $locatin_cond and a.year between $cbo_year_name and $cbo_end_year_name  and c.month_id between 1 and 12 group by a.comapny_id,a.location_id,a.year,a.effi_percent, a.basic_smv,a.avg_machine_line,b.no_of_line,c.working_day, c.month_id, c.capacity_month_min,c.capacity_month_pcs"; //echo $sql;
	 //and c.month_id between $cbo_month and $cbo_month_end and b.date_calc between '$s_date' and '$e_date'

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

	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";

	if($cbo_buyer_id !=''){$buyer_cond=" AND a.buyer_name in($cbo_buyer_id)";}
	if($cbo_date_cat_id==1)
	{
		$sql="SELECT a.job_no, a.set_break_down, a.buyer_name, a.total_set_qnty, a.quotation_id,
		b.id as po_id, b.pub_shipment_date, b.unit_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else if($cbo_date_cat_id==3)
	{
		$sql="SELECT a.job_no, a.set_break_down, a.buyer_name, a.total_set_qnty, a.quotation_id,
		b.id as po_id, b.shipment_date as pub_shipment_date, b.unit_price,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b
		WHERE a.job_no=b.job_no_mst AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND b.shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else //Country Ship Date
	{
		$sql="SELECT a.job_no, a.set_smv, a.set_break_down, a.buyer_name, a.total_set_qnty, a.quotation_id, c.country_ship_date as pub_shipment_date
		b.id as po_id, b.unit_price,
		(c.order_quantity/a.total_set_qnty) as po_quantity,
		(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id AND a.company_name in($cbo_company_name) $buyer_cond $locatin_cond AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	//echo $sql;




	$sql_data=sql_select($sql);
	foreach( $sql_data as $row)
	{

		$date_key=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
		$order_data_array[$year_month]['rowspan']+=1;
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];

		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
		{
			foreach($set_break_down_arr as $set_break_down)
			{
				list($item_id,$set,$smv)=explode('_',$set_break_down);
				if($cbo_date_cat_id==1  || $cbo_date_cat_id==3)
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


				//=======================================================================================//
				$test_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['lab_test'];
				$freight_cost	= $other_costing_arr[$row[csf('po_id')]][$item_id]['freight'];
				$inspection		= $other_costing_arr[$row[csf('po_id')]][$item_id]['inspection'];
				$certificate_cost = $other_costing_arr[$row[csf('po_id')]][$item_id]['certificate_pre_cost'];
				$common_oh		= $other_costing_arr[$row[csf('po_id')]][$item_id]['common_oh'];
				$currier_cost	= $other_costing_arr[$row[csf('po_id')]][$item_id]['currier_pre_cost'];
				$cm_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['cm_cost'];

				$cm_value_order_item_array[$date_key][$row[csf('buyer_name')]]['cm_cost']+=$cm_cost;

				$tot_other_cost		= $test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost;

				$conversion_cost	= array_sum($conversion_costing_arr[$row[csf('po_id')]][$item_id]);

				$trim_cost			= $trims_costing_arr[$row[csf('po_id')]][$item_id];

				$fab_purchase_knit	= array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$item_id]);
				$fab_purchase_woven	= array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);

				$emblishment_cost	= $emblishment_costing_arr[$row[csf('po_id')]][$item_id]+$emblishment_costing_arr_name_wash[$row[csf('po_id')]][$item_id];
				$commercial_cost	= $commercial_costing_arr[$row[csf('po_id')]][$item_id];
				$commission_cost	= $commission_costing_arr[$row[csf('po_id')]][$item_id];
				$yarn_cost			= $yarn_costing_arr[$row[csf('po_id')]][$item_id];

				$total_cost = $conversion_cost+$trim_cost+$fab_purchase_knit+$fab_purchase_woven+$emblishment_cost+$commercial_cost+$yarn_cost+$commission_cost+$tot_other_cost;

				$total_cost_item_array[$date_key][$row[csf('buyer_name')]]['total_cost']	+=$total_cost;


				$order_data_array[$date_key][$row[csf('buyer_name')]]			+= $confirm_qty;
				$projected_order_data_array[$date_key][$row[csf('buyer_name')]]	+= $project_qty;

				$confirm_qty_array[$date_key][$row[csf('buyer_name')]]		+= $confirm_qty;
				$projected_qty_array[$date_key][$row[csf('buyer_name')]]	+= $project_qty;
				//=======================================================================================//


				$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$confirm_qty;
				$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$rate_in_pcs*$project_qty;

				$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
				$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);

				$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;

				if($quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty']>0){
					$quotation_data_arr[$date_key][$row[csf('buyer_name')]]['quoted_qty'] += $quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty'];
				}

				if($quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn']>0){
					$quotation_data_arr[$date_key][$row[csf('buyer_name')]]['quoted_val'] += $quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn'];
				}
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
			//$item_id		= $row[csf('item_number_id')];


			$po_qty_set_smv=$row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv=$po_qty_set_smv/$row[csf('po_quantity')];

			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;

			//=======================================================================================//
			$test_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['lab_test'];
			$freight_cost	= $other_costing_arr[$row[csf('po_id')]][$item_id]['freight'];
			$inspection		= $other_costing_arr[$row[csf('po_id')]][$item_id]['inspection'];
			$certificate_cost = $other_costing_arr[$row[csf('po_id')]][$item_id]['certificate_pre_cost'];
			$common_oh		= $other_costing_arr[$row[csf('po_id')]][$item_id]['common_oh'];
			$currier_cost	= $other_costing_arr[$row[csf('po_id')]][$item_id]['currier_pre_cost'];
			$cm_cost		= $other_costing_arr[$row[csf('po_id')]][$item_id]['cm_cost'];

			$cm_value_order_item_array[$date_key][$row[csf('buyer_name')]]['cm_cost']+=$cm_cost;

			$tot_other_cost		= $test_cost+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost;

			$conversion_cost	= array_sum($conversion_costing_arr[$row[csf('po_id')]][$item_id]);
			$trim_cost			= $trims_costing_arr[$row[csf('po_id')]][$item_id];

			$fab_purchase_knit	= array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$item_id]);
			$fab_purchase_woven	= array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$item_id]);

			$emblishment_cost	= $emblishment_costing_arr[$row[csf('po_id')]][$item_id]+$emblishment_costing_arr_name_wash[$row[csf('po_id')]][$item_id];
			$commercial_cost	= $commercial_costing_arr[$row[csf('po_id')]][$item_id];
			$commission_cost	= $commission_costing_arr[$row[csf('po_id')]][$item_id];
			$yarn_cost			= $yarn_costing_arr[$row[csf('po_id')]][$item_id];

			$total_cost = $conversion_cost+$trim_cost+$fab_purchase_knit+$fab_purchase_woven+$emblishment_cost+$commercial_cost+$yarn_cost+$commission_cost+$tot_other_cost;

			$total_cost_item_array[$date_key][$row[csf('buyer_name')]]['total_cost']	+=$total_cost;


			$order_data_array[$date_key][$row[csf('buyer_name')]]			+= $confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]	+= $project_qty;

			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]		+= $confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]	+= $project_qty;
			//======================================================================================//


			$confirm_value_array[$date_key][$row[csf('buyer_name')]]	+= $confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]	+= $project_value;//$rate_in_pcs*$project_qty;

			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]			+= ($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]	+= ($smv*$project_qty);

			$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;

			if($quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty']>0){
				$quotation_data_arr[$date_key][$row[csf('buyer_name')]]['quoted_qty'] = $quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['offer_qnty'];
			}

			if($quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn']>0){
				$quotation_data_arr[$date_key][$row[csf('buyer_name')]]['quoted_val'] = $quot_wise_arr2[$date_key][$row[csf("buyer_name")]][$row[csf('quotation_id')]]['price_with_commn_dzn'];
			}
		}
	}


	$sql_data="select pd.plan_qnty as pdplan_qnty,b.pub_shipment_date from wo_po_break_down b,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  b.id=c.po_break_down_id and pd.plan_id=c.plan_id and c.company_id in($cbo_company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between  '$s_date' and '$e_date'";
	$data_result=sql_select($sql_data);
	$plan_qty_arr=array();
	foreach( $data_result as $row)
	{
		$plan_qty_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]+=$row[csf("pdplan_qnty")];
	}


	//echo "<pre>";
	//print_r($quotation_data_arr);//die;

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

					<td width="60" align="right"><? echo number_format(array_sum($projected_value_array[$year_month])/$conver_to_million,4); ?></td>
                    <td width="60" align="right"><? echo number_format(array_sum($confirm_value_array[$year_month])/$conver_to_million,4); ?></td>

					<td width="60" align="right"><?
					$tot_conf_proj_value=array_sum($confirm_value_array[$year_month])+array_sum($projected_value_array[$year_month]);
					 echo number_format($tot_conf_proj_value/$conver_to_million,4); ?></td>


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
        <div style="width:1280px;">
        <table cellspacing="0" width="1260" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
                <tr>
                	<th colspan="16">Buyer Wise Capacity Booked Summary</th>
                </tr>
                <tr>
                    <th width="70">Month</th>
                    <th width="130">Buyer</th>
                    <th width="100"><?=$captionButtonChange; ?></th>
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
                    <th width="80">Booked %</th>
                    <th width="80">Pre-costing CM %</th>
                    <th width="80">Pre-costing RM %</th>
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

						arsort($order_data_array[$year_month]);

						foreach($order_data_array[$year_month] as $buyer_id=>$confirm_qty)
						{
							$bookedMints	= $booked_smv_mints_arr[$year_month][$buyer_id];
							$projectedbookedMints=$projected_booked_smv_mints_arr[$year_month][$buyer_id];
							$project_qty	= $projected_qty_array[$year_month][$buyer_id];

							//=========================================================================//
							//$quoted_qty		= $quot_wise_arr[$year_month][$buyer_id]['offer_qnty'];

							$quoted_qty	= $quot_wise_arr2[$year_month][$buyer_id]['offer_qnty'];
							$quoted_val	= $quot_wise_arr2[$year_month][$buyer_id]['price_with_commn_dzn'];

							//$quoted_qty	= $quotation_data_arr[$year_month][$buyer_id]['quoted_qty'];
							//$quoted_val	= $quotation_data_arr[$year_month][$buyer_id]['quoted_val'];

							$buyer_quot_offer_qty	= $order_data_array_not_quot_arr[$year_month][$buyer_id]['offer_qnty'];
							$buyer_quot_offer_value	= $order_data_array_not_quot_arr[$year_month][$buyer_id]['offer_value'];

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

							//$quoted_qty_bal	= ($quoted_qty-$confirm_qty);
							$quoted_qty_bal	= $quoted_qty;
							//=========================================================================//
							$quoted_qty_arr[$year_month]+=$quoted_qty;
							//$quoted_qty_arr[$year_month]+=$quoted_qty_bal;

							$avgSMV=($bookedMints+$projectedbookedMints) / (($confirm_qty+$project_qty)>0?($confirm_qty+$project_qty):1);
							$bookedParcent=0;
							if($capacity_arr[$year_month]['capacity_month_min'])
							{
								$bookedParcent=($bookedMints/$capacity_arr[$year_month]['capacity_month_min'])*100;
								$bookedParcentFormula = "(Confirmed Mints / Factory Capacity Mints) * 100";
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


							//=========================================================================//
							$tot_conf_proj_val	= $projected_value_array[$year_month][$buyer_id]+$confirm_value_array[$year_month][$buyer_id];
							$cm_value			= $cm_value_order_item_array[$year_month][$buyer_id]['cm_cost'];
							$cm_value_percent	= ($cm_value/$tot_conf_proj_val)*100;

							$tot_pre_cost		= $total_cost_item_array[$year_month][$buyer_id]['total_cost'];
							$rm_value_percent	= ($tot_pre_cost/$tot_conf_proj_val)*100;

							$totla_cm_val_arr[$year_month]	+= $cm_value;
							$total_cost_rm_arr[$year_month]	+= $tot_pre_cost;
							//===========================================================================//



						 if($tr!=0){echo '<tr bgcolor="'.$bgcolor.'" id="trb_'.$i.'"  onclick="'.$fn.'">';}
						 ?>
                        <td width="130" title="<? //echo "Buyer=".$buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                        <td width="100" align="right"><? echo number_format($quoted_qty_bal,0); ?></td>
                        <td width="80" align="right"><? echo $project_qty; ?></td>
                        <td width="80" align="right"><? echo $confirm_qty; ?></td>
                        <td width="80" align="right"><? echo $project_qty+$confirm_qty; ?></td>
                        <td width="80" align="right"><? echo number_format($projected_value_array[$year_month][$buyer_id]/$conver_to_million,4); ?></td>
                        <td width="80" align="right"><? echo number_format($confirm_value_array[$year_month][$buyer_id]/$conver_to_million,4); ?></td>
						<td width="80" align="right"><?
						$tot_conf_proj_val=$projected_value_array[$year_month][$buyer_id]+$confirm_value_array[$year_month][$buyer_id];
						echo number_format($tot_conf_proj_val/$conver_to_million,4); ?></td>


                        <td width="60" align="right" title="<? echo "(".$bookedMints."+".$projectedbookedMints.")/(".$confirm_qty."+".$project_qty.")"; ?>"><? echo number_format($avgSMV,2); ?></td>
                        <td width="80" align="right"><? echo number_format($projectedbookedMints,2); ?></td>
                        <td width="80" align="right"><? echo number_format($bookedMints,2); ?></td>
						<td width="60" align="right"><? echo number_format($projectedbookedMints+$bookedMints,2); ?></td>
                        <td align="right" title="<? echo $bookedParcentFormula; ?>"><? echo number_format($bookedParcent,2); ?></td>
                        <td width="80" align="right" title="(TotalCM/FOB Value*100) Total CM Value = <? echo $cm_value;?>"><? echo number_format($cm_value_percent,2); ?></td>
                   		<td width="70" align="right" title="(Total Cost-CM/FOB Value*100)<? echo 'Total Cost='.$tot_pre_cost;?>"><? echo number_format($rm_value_percent,2); ?></td>

                    </tr>
                    <?
					$tr++;$i++;
					}
					?>
                    <tr bgcolor="#DDD">

                        <th>Total:</th>
                        <th align="right"><? echo number_format($quoted_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_qty_arr[$year_month]+$confirm_qty_arr[$year_month],0); ?></th>
                        <th align="right"><? echo number_format($projected_value_arr[$year_month]/$conver_to_million,4); ?></th>
                        <th align="right"><? echo number_format($confirm_value_arr[$year_month]/$conver_to_million,4); ?></th>

						<th align="right"><? echo number_format(($projected_value_arr[$year_month]+$confirm_value_arr[$year_month])/$conver_to_million,4); ?></th>


                        <th align="right"><? echo  number_format(($booked_mints_arr[$year_month] + $projected_booked_mints_arr[$year_month])/ ($confirm_qty_arr[$year_month]+$projected_qty_arr[$year_month]),2); ?></td>
                         <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month],2); ?></th>
                        <th align="right"><? echo number_format($booked_mints_arr[$year_month],2); ?></th>
						 <th align="right"><? echo number_format($projected_booked_mints_arr[$year_month]+$booked_mints_arr[$year_month],2); ?></th>
                        <th title="Cell will be red if value is more than 100%" align="right" <? if(round($booked_parcent_arr[$year_month])>100){ echo 'bgcolor="#F00"';}?> ><? echo number_format($booked_parcent_arr[$year_month],2); ?></th>

                        <th align="right"><?
                        $tot_cm_percent=($totla_cm_val_arr[$year_month]/($projected_value_arr[$year_month]+$confirm_value_arr[$year_month]))*100;
                        $tot_rm_percent=($total_cost_rm_arr[$year_month]/($projected_value_arr[$year_month]+$confirm_value_arr[$year_month]))*100;
                        //echo number_format($tot_cm_percent,2);
						?></th>
                        <th align="right" ><? //echo number_format($tot_rm_percent,2); ?></th>
                    </tr>

                    <tr bgcolor="#FFFF00">
                        <th colspan="12">
                        	<?
								$less_over=$booked_parcent_arr[$year_month]-100;
								if($less_over>0){echo "Capacity booked Over %";}
								else{echo "Capacity booked Less %";}
							?>

                        </th>
                        <th align="right"><? echo number_format($less_over,2); ?></th>
                        <th align="right" colspan="2">&nbsp;</th>
                    </tr>
					<?
					}
					?>
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
?>