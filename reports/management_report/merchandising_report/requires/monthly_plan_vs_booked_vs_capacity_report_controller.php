<?
include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }



if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data'
	order by location_name","id,location_name", 1, "-- All --", $selected, "",0 );
	exit();
}





if($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	
	$companyArr=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
	$locationArr=return_library_array( "select id, location_name from lib_location", "id", "location_name");




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
		$fullMonthArr[]=date("M-Y",strtotime($next_month));
	}
	
	$date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
	if($cbo_company_name!=0){$company_con=" AND a.company_name=$cbo_company_name";}
	if($cbo_location_id!=0){$location_con=" AND a.LOCATION_NAME=$cbo_location_id";}
	
	$sql_con_po="SELECT a.job_no, a.company_name, a.buyer_name, b.pub_shipment_date, b.shipment_date, b.is_confirmed, c.order_quantity as po_quantity,a.SET_SMV,a.LOCATION_NAME
	FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id $company_con $location_con $date_cond and a.status_active=1 and b.is_confirmed=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.company_name";
	//echo $sql_con_po;die;
	$poQtyArr=array();
	$sql_data_po=sql_select($sql_con_po);
	foreach( $sql_data_po as $row_po)
	{
		//$companyByJobArr[$row_po[csf("job_no")]]=$row_po[csf("company_name")];
		$monthKey=date("Y-m",strtotime($row_po[csf("pub_shipment_date")]));
		$key=$row_po[csf("company_name")].'__'.$row_po[LOCATION_NAME];
		$poMinuteArr[$key][$monthKey][$row_po[csf("is_confirmed")]]+=$row_po[csf("po_quantity")]*$row_po[SET_SMV];
	}
	unset($sql_data_po);
	
	
	
	if($cbo_company_name!=0){$company_con2=" AND a.COMAPNY_ID=$cbo_company_name";}
	if($cbo_location_id!=0){$location_con2=" AND a.LOCATION_ID=$cbo_location_id";}
	$sql_capacity="Select a.COMAPNY_ID,a.LOCATION_ID, b.CAPACITY_MIN,b.DATE_CALC from lib_capacity_calc_mst a, lib_capacity_calc_dtls b  where a.id=b.mst_id $company_con2 and a.status_active=1 and a.is_deleted=0 AND a.YEAR between '$cbo_year_name' and '$cbo_end_year_name' and b.DATE_CALC between '$s_date' and '$e_date'";
	$sql_capacityRes=sql_select($sql_capacity); 
	$capacityArr=array();
	foreach($sql_capacityRes as $row)
	{
		$monthKey=date("Y-m",strtotime($row[DATE_CALC]));
		$key=$row[COMAPNY_ID].'__'.$row[LOCATION_ID];
		$capacityArr[$key][$monthKey]+=$row[CAPACITY_MIN];
	}
	unset($sql_capacityRes);
	//print_r($capacityArr);
	
	if($cbo_company_name!=0){$company_con=" AND c.COMPANY_ID=$cbo_company_name";}
	if($cbo_location_id!=0){$location_con=" AND c.LOCATION_ID=$cbo_location_id";}
	$sql_plan="SELECT (a.SMV_PCS * pd.PLAN_QNTY)     AS PLAN_MINIT,
       c.COMPANY_ID, c.LOCATION_ID, b.PUB_SHIPMENT_DATE, b.PO_QUANTITY, pd.PLAN_DATE
  FROM wo_po_break_down b, WO_PO_DETAILS_MAS_SET_DETAILS a, ppl_sewing_plan_board_powise  pp, ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c
 WHERE a.job_no = b.job_no_mst  AND b.id = pp.po_break_down_id AND pp.plan_id = pd.plan_id AND c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and pp.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID
       AND pp.plan_id = c.plan_id AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1
       AND c.is_deleted = 0 and pd.PLAN_DATE between '$s_date' and '$e_date' $company_con $location_con";
	
	   //echo $sql_plan;die;
	$sql_planRes=sql_select($sql_plan); 
	$planMiniArr=array();
	foreach($sql_planRes as $row)
	{
		$monthKey=date("Y-m",strtotime($row[PLAN_DATE]));

		$key=$row[COMPANY_ID].'__'.$row[LOCATION_ID];
		$planMiniArr[$key][$monthKey]+=$row[PLAN_MINIT];
	}
	unset($sql_planRes);
	
	  //var_dump($planMiniArr);
	
	
	
	$monthCount=count($month_arr);
	$width=($monthCount*90)+410;
	$spnTitle=$monthCount+4;
	ob_start();
	$titleStr="";
	?> 
	<div style="width:<?=$width+22; ?>px;">
    	<fieldset style="width:100%;">
            <table width="<?=$width; ?>" align="left">
                <tr class="form_caption">
                    <td colspan="<?=$spnTitle; ?>" align="center"><strong><?=$report_title; ?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Company</th>
                        <th width="100">Location</th>
                        <th width="80">Minutes /Month</th>
                        <? foreach($month_arr as $monthVal)
                        {
                            ?>
                            <th width="90"><?=date("M-y",strtotime($monthVal)); ?></th>
                            <?
                        }
                        ?>
                        <th>Total</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?=$width+18; ?>px; max-height:400px; overflow-y:scroll; float:left;" id="scroll_body">
            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <?
					$i=1; $k=2; $capacityMonthArr=array();$allocatedMonthArr=array();
					foreach($poMinuteArr as $company_location=>$dataRows)
					{
						list($company_id,$location_id)=explode('__',$company_location);
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						 <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
							<td width="30" align="center" valign="middle" rowspan="3"><?=$k-1; ?></td>
							<td width="60" rowspan="3" valign="middle" align="center"><?=$companyArr[$company_id]; ?></td>
							<td width="100" rowspan="3" valign="middle"><?=$locationArr[$location_id]; ?></td>
                            <td width="80">Plan</td>
							<?
							$totalPlanMinit=0;
							foreach($month_arr as $monthVal)
							{
								$plan_minit=$planMiniArr[$company_location][$monthVal];
								$totalPlanMinit+=$plan_minit;
								$plan_minit_MonthArr[$monthVal]+=$plan_minit;
								?>
								<td width="90" align="right"><?=fn_number_format($plan_minit); ?>&nbsp;</td>
								<?
							}
							?>
							<td align="right"><?=fn_number_format($totalPlanMinit); ?>&nbsp;</td>
                         </tr>
                         
                         <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
							<td>Booked</td>
							<?
							$totalAllocated=0;
							foreach($month_arr as $monthVal)
							{
								$confirmQty=$dataRows[$monthVal][1]+($allocationToArr[$company_location][$monthVal]-$allocationFromArr[$company_location][$monthVal]);
								$totalAllocated+=$confirmQty;
								
								$allocatedMonthArr[$monthVal]+=$confirmQty;
								?>
								<td align="right"><?=fn_number_format($confirmQty); ?>&nbsp;</td>
								<?
							}
							?>
							<td align="right"><?=fn_number_format($totalAllocated); ?>&nbsp;</td>
						 </tr>
                         
                         <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
							<td width="80">Capacity</td>
							<?
							$totalCapacity=0;
							foreach($month_arr as $monthVal)
							{
								$capacity=$capacityArr[$company_location][$monthVal];
								$totalCapacity+=$capacity;
								$capacityMonthArr[$monthVal]+=$capacity;
								?>
								<td width="90" align="right"><?=fn_number_format($capacity); ?>&nbsp;</td>
								<?
							}
							$i++;
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<td align="right"><?=fn_number_format($totalCapacity); ?>&nbsp;</td>
						 </tr>
                         
                         
						<?
						$i++; $k++;
					}
					$grandMonthTotalArr=array();
				?>
                <tfoot>
                         <tr>
							<th colspan="3" rowspan="2" valign="middle"></th>
                            <th>Plan</th>
							<?
							$totalPlanMinit=0;
							foreach($month_arr as $monthVal)
							{
								$planMint=$plan_minit_MonthArr[$monthVal];
								$totalPlanMinit+=$planMint;
								?>
								<th align="right"><?=fn_number_format($planMint); ?>&nbsp;</th>
								<?
							}
							?>
							<th align="right"><?=fn_number_format($totalPlanMinit); ?>&nbsp;</th>
						 </tr>                
                    
                    	 <tr>
							<th>Booked</th>
							<?
							$totalAllocated=0;
							foreach($month_arr as $monthVal)
							{
								$confirmQty=$allocatedMonthArr[$monthVal];
								$totalAllocated+=$confirmQty;
								?>
								<th align="right"><?=fn_number_format($confirmQty); ?>&nbsp;</th>
								<?
							}
							?>
							<th align="right"><?=fn_number_format($totalAllocated); ?>&nbsp;</th>
						 </tr>
                         
                     </tfoot>
            </table>
            </div>
        </fieldset>
    </div>
    <?
	$capacityStr = implode(',',$capacityMonthArr);
	$allocatedStr = implode(',',$allocatedMonthArr);
	$planMintStr = implode(',',$plan_minit_MonthArr);
	$monthStr = implode("','",$fullMonthArr);
	
    $html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	
	
	
	$html.='<br><div id="container" style="width:'.$width.'px;border:1px solid #CCC;"></div>';
	
	echo "$html****$filename****$allocatedStr****$planMintStr****'$monthStr'****$capacityStr";
	
	

	
	
	exit();
}
?>