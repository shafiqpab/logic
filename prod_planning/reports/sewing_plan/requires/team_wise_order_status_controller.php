<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
function pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}
function get_next_month($month)
{
	$next_mon =month_add($month,1);
	$date=date_create($next_mon);
	return date_format($date,'M-Y');
}
function getCorrespondingMonth($date) {
    // Parse the input date
    $date = date_create_from_format('d-M-y', $date);

    // Determine the corresponding month
    $correspondingMonth = date_modify($date, '-10 days')->format('M-Y');

    return $correspondingMonth;
}
if ($action=="load_drop_down_buyer")
{
	extract($_REQUEST);
	//echo  "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name";die;
	$buyer_arr = return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($company_id) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id","buyer_name"); 

	echo create_drop_down( "cbo_buyer_name", 140,$buyer_arr ,"", 0, "- All Buyer -", $selected, "" );
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_team_leader = str_replace("'","",$cbo_team_leader);
	$category_by = str_replace("'","",$cbo_category_by);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$txt_start_date = str_replace("'","",$txt_start_date);
	$txt_end_date = str_replace("'","",$txt_end_date);
	$cbo_status = str_replace("'","",$cbo_status);
 	$dateFormat = "M-Y";

	if ($type == 1) 
	{
			
		$companyArr=return_library_array( "select id, COMPANY_SHORT_NAME from lib_company", "id", "COMPANY_SHORT_NAME"  );
		$team_arr=return_library_array( "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name" );
		//--------------------------------------------------------------------------------------------------------------------
		
		$tot_month = datediff( 'm',$txt_start_date, $txt_end_date);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($txt_start_date,$i);
			$month_arr[]=date($dateFormat,strtotime($next_month));
		}
		
		
		
		if($cbo_company_id!=''){$where_con = " AND a.company_name in($cbo_company_id)";}
		if($cbo_status!='' || $cbo_status != 0){$where_con .= " AND b.IS_CONFIRMED in($cbo_status)";}
		if($cbo_team_leader!=''){$where_con .= " AND a.TEAM_LEADER in($cbo_team_leader)";}

		if($txt_start_date && $txt_end_date){
			if($category_by == 1){
				$where_con .= "and b.pack_handover_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.pack_handover_date ";
			}elseif($category_by == 2){
				$where_con .= "and b.pub_shipment_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.pub_shipment_date ";
			}elseif($category_by == 3){
				$where_con .= "and b.shipment_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.shipment_date ";
			}elseif($category_by == 4){
				$where_con .= "and c.country_ship_date between '$txt_start_date' and '$txt_end_date'";
				$field ="c.country_ship_date ";
			}
		}
		
		$sql_con_po="SELECT a.TEAM_LEADER, a.COMPANY_NAME,b.IS_CONFIRMED, b.PUB_SHIPMENT_DATE, b.shipment_date,  c.order_quantity as PO_QUANTITY,a.SET_SMV,a.LOCATION_NAME
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id $where_con and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.company_name";
		//echo $sql_con_po;die;
		
		$poQtyArr=array();$dataArr=array();
		$sql_data_po=sql_select($sql_con_po);
		foreach( $sql_data_po as $row_po)
		{
			//$companyByJobArr[$row_po[csf("job_no")]]=$row_po[csf("company_name")];
			$monthKey=date($dateFormat,strtotime($row_po["PUB_SHIPMENT_DATE"]));
			//$key=$row_po[COMPANY_NAME].'__'.$row_po[TEAM_LEADER];
			$key='0__'.$row_po["TEAM_LEADER"];
			$poMinuteArr["QTY"][$row_po["IS_CONFIRMED"]][$monthKey][$key]+=$row_po["PO_QUANTITY"];
			$poMinuteArr["MIN"][$row_po["IS_CONFIRMED"]][$monthKey][$key]+=$row_po["PO_QUANTITY"]*$row_po["SET_SMV"];
			$dataArr[$key]=1;
			
			$team_wise_min[$monthKey][$key]+=$row_po["PO_QUANTITY"]*$row_po["SET_SMV"];
		}
		unset($sql_data_po);		
			
			
		//capacity....................................................
		if($cbo_company_id!=''){$whereCon=" AND a.COMAPNY_ID in($cbo_company_id)";}
		$date_cond_capacity="AND DATE_CALC between '$txt_start_date' and '$txt_end_date'";
		$capacitySql="select a.COMAPNY_ID,b.DAY_STATUS,b.DATE_CALC,b.CAPACITY_MIN from LIB_CAPACITY_CALC_MST a ,LIB_CAPACITY_CALC_DTLS b where a.id=b.mst_id $whereCon $date_cond_capacity";
		//echo $capacitySql;die;
		$capacitySqlResult=sql_select($capacitySql);
		$capacity_data_arr=array();
		foreach($capacitySqlResult as $row)
		{
			$dateKey=date($dateFormat,strtotime($row['DATE_CALC']));
			//$capacity_data_arr[$dateKey]+=$row[CAPACITY_MIN];
			$working_day_arr[$dateKey][$row['DAY_STATUS']]+=1;
		}
		
	
		//Salse Tar....................................................
		if($cbo_company_id!=''){$whereCon=" AND a.COMPANY_ID in($cbo_company_id)";}
		$date_cond_sales_target="AND SALES_TARGET_DATE between '$txt_start_date' and '$txt_end_date'";
		$salseTargetSql="select a.COMPANY_ID,b.SALES_TARGET_QTY,b.SALES_TARGET_DATE,b.SALES_TARGET_MINT from WO_SALES_TARGET_MST a ,WO_SALES_TARGET_DTLS b where a.id=b.SALES_TARGET_MST_ID $whereCon $date_cond_sales_target";
		//echo $salseTargetSql;die;
		$salseTargetSqlResult=sql_select($salseTargetSql);
		$salse_target_arr=array();
		foreach($salseTargetSqlResult as $row)
		{
			$dateKey=date($dateFormat,strtotime($row["SALES_TARGET_DATE"]));
			$salse_target_arr[$dateKey]+=$row["SALES_TARGET_QTY"];
			
			$capacity_data_arr[$dateKey]+=$row["SALES_TARGET_MINT"];
		}

		$monthCount=count($month_arr);
		$width=($monthCount*180)+310;
		$spnTitle=$monthCount+4;
		$million=1000000;
		ob_start();	
		?>
		
		<div style="width:<?=$width+22; ?>px; float:left;">
			<fieldset style="width:100%;">
				<table width="<?=$width; ?>" align="left">
					<tr class="form_caption">
						<td colspan="<?=$spnTitle; ?>" align="center"><strong>Team Wise Order Status</strong></td>
					</tr>
				</table>
				<table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="100">Team</th>
							<th width="80">Month</th>
							<? foreach($month_arr as $monthVal)
							{
								?>
								<th colspan="2">
									<?=$monthVal; ?>
									(Working Days: <?=$working_day_arr[$monthVal][1]; ?>)
								</th>
								<?
							}
							?>
						</tr>
						<tr style="background:#9C9">
							<td colspan="3">Target Forcast (Million) </td>
							<? foreach($month_arr as $monthVal)
							{
								?>
								<td colspan="2" align="right"><?=number_format($salse_target_arr[$monthVal]/$million,2); ?></td>
								<?
							}
							?>
						</tr>
						<tr style="background:#CFF">
							<td colspan="3">Target Capacity Minutes (Million)</td>
							<? foreach($month_arr as $monthVal)
							{
								?>
								<td colspan="2" align="right"><?=number_format(($capacity_data_arr[$monthVal]/$million),2); ?></td>
								<?
							}
							?>
						</tr>
						<tr>
							<th width="30"></th>
							<th width="100"></th>
							<th width="80">Status</th>
							<? foreach($month_arr as $monthVal)
							{
								?>
								<th width="90">PCS</th>
								<th width="90">Minutes</th>
								<?
							}
							?>
						</tr>
					</thead>
				</table>
				<div style="width:<?=$width+18; ?>px; max-height:400px; overflow-y:scroll; float:left;" id="scroll_body">
					<table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
						<?
							$i=1; $k=2; $capacityMonthArr=array();$allocatedMonthArr=array();
							foreach($dataArr as $company_team=>$dataRows)
							{
								list($company_id,$team_id)=explode('__',$company_team);
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
									<td width="30" align="center" valign="middle" rowspan="3"><?=$k-1; ?></td>
									<td width="100" rowspan="3" valign="middle"><?=$team_arr[$team_id]; ?></td>
									<td width="80">Confirm</td>
									<?
									$totalPlanMinit=0;
									foreach($month_arr as $monthVal)
									{
										$po_qty=$poMinuteArr[QTY][1][$monthVal][$company_team];
										$po_min=$poMinuteArr[MIN][1][$monthVal][$company_team];
										?>
										<td width="90" align="right"><?=fn_number_format($po_qty); ?></td>
										<td width="90" align="right"><?=fn_number_format($po_min,2); ?></td>
										<?
									}
									?>
								</tr>
								
								<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
									<td>Projection</td>
									<?
									$totalAllocated=0;
									foreach($month_arr as $monthVal)
									{
										$po_qty=$poMinuteArr[QTY][2][$monthVal][$company_team];
										$po_min=$poMinuteArr[MIN][2][$monthVal][$company_team];
										?>
										<td align="right"><?=fn_number_format($po_qty); ?></td>
										<td align="right"><?=fn_number_format($po_min,2); ?></td>
										<?
									}
									?>
								</tr>
								
								<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
									<td>Sub-Con</td>
									<?
									foreach($month_arr as $monthVal)
									{
										?>
										<td align="right"></td>
										<td align="right"></td>
										<?
									}
									?>
								</tr>
								
								<?
								$i++; $k++;
							}
							$grandMonthTotalArr=array();
						?>
						<tfoot>
								<tr>
									<th colspan="2" rowspan="5" valign="middle"></th>
									<th>Confirm</th>
									<?
									$totalAllocated=0;
									foreach($month_arr as $monthVal)
									{
										$po_qty=array_sum($poMinuteArr[QTY][1][$monthVal]);
										$po_min=array_sum($poMinuteArr[MIN][1][$monthVal]);
										?>
										<th align="right"><?=fn_number_format($po_qty); ?></th>
										<th align="right"><?=fn_number_format($po_min,2); ?></th>
										<?
									}
									?>
								</tr>                
							
								<tr>
									<th>Projection</th>
									<?
									$totalAllocated=0;
									foreach($month_arr as $monthVal)
									{
										$po_qty=array_sum($poMinuteArr["QTY"][2][$monthVal]);
										$po_min=array_sum($poMinuteArr["MIN"][2][$monthVal]);
										?>
										<th align="right"><?=fn_number_format($po_qty); ?></th>
										<th align="right"><?=fn_number_format($po_min,2); ?></th>
										<?
									}
									?>
								</tr>
								
								<tr>
									<th>Sub-Con</th>
									<?
									$totalAllocated=0;
									foreach($month_arr as $monthVal)
									{
										?>
										<th align="right"></th>
										<th align="right"></th>
										<?
									}
									?>
								</tr>
								
								<tr>
									<th>Grand Total</th>
									<?
									$totalAllocated=0;
									foreach($month_arr as $monthVal)
									{
										$g_po_qty=array_sum($poMinuteArr["QTY"][1][$monthVal])+array_sum($poMinuteArr["QTY"][2][$monthVal]);
										$g_po_min=array_sum($poMinuteArr["MIN"][1][$monthVal])+array_sum($poMinuteArr["MIN"][2][$monthVal]);
										?>
										<th align="right"><?=fn_number_format($g_po_qty); ?></th>
										<th align="right"><?=fn_number_format($g_po_min,2); ?></th>
										<?
									}
									?>
								</tr>
								
								<tr>
									<th>Balance to Fillup</th>
									<?
									$totalAllocated=0;
									foreach($month_arr as $monthVal)
									{
										$g_po_min=array_sum($poMinuteArr["MIN"][1][$monthVal])+array_sum($poMinuteArr["MIN"][2][$monthVal]);
										
										?>
										<th align="right"></th>
										<th align="right"><?=fn_number_format((($capacity_data_arr[$monthVal]/$million)-($g_po_min/$million)),2); ?></th>
										
										<?
									}
									?>
								</tr>
								
								
							</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
		
		<div style="margin-left:5px;">
			<fieldset style=" width:200px;">
			<table class="rpt_table" cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<thead>
					<tr>
							<th colspan="3">Summary</th>
					</tr>
					<tr>
							<th>Team Name</th>
							<th>Minutes</th>
							<th>Minutes % </th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($month_arr as $monthVal)
				{
				?>
					<tr><td align="center" colspan="3"><b><?=$monthVal;?></b></td></tr>
				
				<tbody>
					<? foreach($team_wise_min[$monthVal] as $company_team=>$min){
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					list($company_id,$team_id)=explode('__',$company_team);
					?>
					<tr bgcolor="<?=$bgcolor;?>">
						<td><?=$team_arr[$team_id]; ?></td>
						<td align="right"><?=number_format($min,2);?></td>
						<td align="right"><?= number_format(($min/array_sum($team_wise_min[$monthVal])*100),2);?></td>
					</tr>
					<? $i++;} ?>
						<tr>
							<td><b>Total</b></td>
							<td align="right"><b><?= number_format(array_sum($team_wise_min[$monthVal]),2);?></b></td>
							<td align="right"><b>100.00</b></td>
						</tr>
					</tbody>
				<? } ?>
			</table>
			</fieldset>
		</div>
		<?
	}
	elseif ($type == 2) //Team Wise 	
	{
			

		$tot_month = datediff( 'm',$txt_start_date, $txt_end_date);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($txt_start_date,$i);
			$month_arr[]=date($dateFormat,strtotime($next_month));
		}

		$companyArr=return_library_array( "select id, COMPANY_SHORT_NAME from lib_company", "id", "COMPANY_SHORT_NAME"  );
		//--------------------------------------------------------------------------------
 
		if($cbo_company_id!=''){$where_con=" and a.company_name in($cbo_company_id)";}
		if($cbo_status!='' || $cbo_status!=0){$where_con.=" and b.is_confirmed in($cbo_status)";}
		if($cbo_team_leader!=''){$where_con.=" and a.team_leader in($cbo_team_leader)";}

		if($txt_start_date && $txt_end_date){
			if($category_by == 1){
				$where_con .= "and b.pack_handover_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.pack_handover_date ";
			}elseif($category_by == 2){
				$where_con .= "and b.pub_shipment_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.pub_shipment_date ";
			}elseif($category_by == 3){
				$where_con .= "and b.shipment_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.shipment_date ";
			}elseif($category_by == 4){
				$where_con .= "and c.country_ship_date between '$txt_start_date' and '$txt_end_date'";
				$field ="c.country_ship_date ";
			}
		}

		// echo $date_cond;die;


	    $sql_con_po = "SELECT d.team_name, a.company_name, a.set_smv,a.location_name,a.TOTAL_SET_QNTY, b.is_confirmed, b.po_received_date, $field as pub_shipment_date, b.shipment_date, b.pack_handover_date, b.id as po_id, c.order_quantity as po_quantity, c.country_ship_date FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,lib_marketing_team d WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and d.id=a.team_leader $where_con and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by d.team_name, a.company_name";
		//echo $sql_con_po;die;
         
		// pre($team_arr); die;
		// ROW SPAN CALCULATION
		$statusArr = explode(',',$cbo_status); 
		$count_status = count($statusArr); 
		if ($count_status == 0 || $cbo_status=='' || $cbo_status==0)
		{
			$count_status = 3;
			$statusArr = [1,2,3];
		}
		
		$poQtyArr=array();$dataArr=array();
		$sql_data_po=sql_select($sql_con_po);
		foreach( $sql_data_po as $row_po)
		{ 
			$smv_set_qty = $row_po['SET_SMV']/$row_po['TOTAL_SET_QNTY'];
			$monthKey=getCorrespondingMonth($row_po['PUB_SHIPMENT_DATE']); //Declared on the top
			$key='0__'.$row_po['TEAM_NAME'];
			$poMinuteArr['QTY'][$row_po['IS_CONFIRMED']][$monthKey][$key]+=$row_po['PO_QUANTITY'];
			$poMinuteArr['MIN'][$row_po['IS_CONFIRMED']][$monthKey][$key]+=$row_po['PO_QUANTITY']*$smv_set_qty;
			$dataArr[$key]=1; 
		}  
 
		unset($sql_data_po);
		// pre ($poMinuteArr);die;
			
		$monthCount=count($month_arr);
		$width=($monthCount*360)+310;
		$spnTitle=$monthCount+4;
		$million=1000000;
		ob_start();	
		?>
		
		<div style="width:<?=$width+22; ?>px; float:left;">
			<fieldset style="width:100%;">
				<table width="<?=$width; ?>" align="left">
					<tr class="form_caption">
						<td colspan="<?=$spnTitle; ?>" align="center"><strong>Team Wise Order Status</strong></td>
					</tr>
				</table>
				<table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="100">Team</th>
							<th width="80">Month</th>
							<? foreach($month_arr as $key => $monthVal)
							{ 
								?>
								<th colspan="3" title="11-<?=$monthVal; ?> to 10-<?= get_next_month($monthVal) ; ?>"> <?=$monthVal ?>  </th>
								<?
							}
							?>
							<th rowspan="2" width="90">Total Qty/Pcs</th>
							<th rowspan="2" width="90">Total Minutes</th>
						</tr>  
						<tr>
							<th width="30"></th>
							<th width="100"></th>
							<th width="80"> <p>Order Status</p></th>
							<? foreach($month_arr as $monthVal)
							{
								?>
								<th width="90">Qty/Pcs</th>
								<th width="90">Total Minutes</th>
								<th width="90">Minutes %</th>
								<?
							}
							?>
						</tr> 
					</thead>
				</table>
				<div style="width:<?=$width+18; ?>px; max-height:400px; overflow-y:scroll; float:left;" id="scroll_body">
					<table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
						<?
							$i=1; $k=2; $capacityMonthArr=array();$allocatedMonthArr=array();

							 
							foreach($dataArr as $company_team=>$dataRows)
							{ 
								$row_status = true;
								list($company_id,$team_name)=explode('__',$company_team);
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if (in_array(1,$statusArr)) 
								{
									?> 
									<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
										<?
											if ($row_status == true) 
											{
												?>
													<td width="30" align="center" valign="middle" rowspan="<?=$count_status?>"><?=$k-1; ?></td>
													<td width="100" rowspan="<?=$count_status?>" valign="middle"><?=$team_name; ?></td>
												<?	 
												$row_status = false;	
											}
										?>
										<td width="80">Confirm</td>
										<?
										$total_po_qty = 0; 
										$total_po_min = 0;  

										foreach($month_arr as $monthVal)
										{
											$po_qty=$poMinuteArr['QTY'][1][$monthVal][$company_team];
											$po_min=$poMinuteArr['MIN'][1][$monthVal][$company_team];

											$team_po_min=$poMinuteArr['MIN'][1][$monthVal][$company_team] + $poMinuteArr['MIN'][2][$monthVal][$company_team];
											$g_po_min=array_sum($poMinuteArr['MIN'][1][$monthVal])+array_sum($poMinuteArr['MIN'][2][$monthVal]);
											$min_per = ($team_po_min / $g_po_min)*100;
											$total_po_qty += $po_qty;
											$total_po_min += $po_min;
											
											?>
											<td width="90" align="right"><?=number_format($po_qty); ?></td>
											<td width="90" align="right"><?=number_format($po_min,2); ?></td> 
											<td width="90" align="center" valign='middle' rowspan="<?=$count_status?>"><?= round($min_per)."%"; ?></td>
											<?
										}
										?>
										<td width="90" align="right"><?=number_format($total_po_qty); ?></td>
										<td width="90" align="right"><?=number_format($total_po_min,2); ?></td>
									</tr> 
									<?
								}	
								if (in_array(2,$statusArr))
								{
									?>		
										<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
											<?
												if ($row_status == true) 
												{
													?>
														<td width="30" align="center" valign="middle" rowspan="<?=$count_status?>"><?=$k-1; ?></td>
														<td width="100" rowspan="<?=$count_status?>" valign="middle"><?=$team_arr[$team_id]; ?></td>
													<?	 
													$row_status = false;	
												}
											?>
											<td>Projection</td>
											<?
											$total_po_qty = 0; 
											$total_po_min = 0; 
											foreach($month_arr as $monthVal)
											{
												$po_qty=$poMinuteArr['QTY'][2][$monthVal][$company_team];
												$po_min=$poMinuteArr['MIN'][2][$monthVal][$company_team];
												$total_po_qty += $po_qty;
												$total_po_min += $po_min;
												?>
												<td align="right"><?=fn_number_format($po_qty); ?></td>
												<td align="right"><?=fn_number_format($po_min,2); ?></td>  
												<?
											}
											?>
											<td width="90" align="right"><?=number_format($total_po_qty); ?></td>
											<td width="90" align="right"><?=number_format($total_po_min,2); ?></td>
										</tr>
									<?
								}	
								if (in_array(3,$statusArr))
								{	
									?>
										<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
											<?
												if ($row_status == true) 
												{
													?>
														<td width="30" align="center" valign="middle" rowspan="<?=$count_status?>"><?=$k-1; ?></td>
														<td width="100" rowspan="<?=$count_status?>" valign="middle"><?=$team_arr[$team_id]; ?></td>
													<?	 
													$row_status = false;	
												}
											?>
											<td>Sub-Con</td>
											<?
											foreach($month_arr as $monthVal)
											{
												?>
												<td align="right"></td>
												<td align="right"></td>  
												<?
											}
											?>
											<td align="right"></td>
											<td align="right"></td>  
										</tr> 
									<?
								}
								?>
								<tr bgcolor='#C2DDF2'>
									<th colspan="2"></th>
									<th align="left">Team Total </th>
									<?
									$total_po_qty = 0; 
									$total_po_min = 0;
									foreach($month_arr as $monthVal)
									{
										$team_po_qty=$poMinuteArr['QTY'][1][$monthVal][$company_team] + $poMinuteArr['QTY'][2][$monthVal][$company_team];
										$team_po_min=$poMinuteArr['MIN'][1][$monthVal][$company_team] + $poMinuteArr['MIN'][2][$monthVal][$company_team];
										$g_po_min=array_sum($poMinuteArr['MIN'][1][$monthVal])+array_sum($poMinuteArr['MIN'][2][$monthVal]);
										$min_per = ($team_po_min / $g_po_min)*100;
										$total_po_qty += $team_po_qty;
										$total_po_min += $team_po_min;
										?>
										<th align="right"><?=number_format($team_po_qty); ?></th>
										<th align="right"><?=number_format($team_po_min,2); ?></th>
										<th align="center"><?=round($min_per).'%'; ?></th>
										<?
									}
									?>
									<td width="90" align="right"><?=number_format($total_po_qty); ?></td>
									<td width="90" align="right"><?=number_format($total_po_min,2); ?></td>
								</tr>
								<?
								$i++; $k++;
							} 
							$grandMonthTotalArr=array();
						?>
						<tfoot>
							<tr bgcolor="#EDE0B7">
								<th colspan="2"></th>
								<th style="text-align: left;">Grand Total</th>
								<?
								$gt_po_qty = 0; 
								$gt_po_min = 0; 
								foreach($month_arr as $monthVal)
								{
									$g_po_qty=array_sum($poMinuteArr['QTY'][1][$monthVal])+array_sum($poMinuteArr['QTY'][2][$monthVal]);
									$g_po_min=array_sum($poMinuteArr['MIN'][1][$monthVal])+array_sum($poMinuteArr['MIN'][2][$monthVal]);
									$min_per = ($g_po_min / $g_po_min)*100;
									$gt_po_qty += $g_po_qty;
									$gt_po_min += $g_po_min;
									?>
										<th align="right"><?=number_format($g_po_qty); ?></th>
										<th align="right"><?=number_format($g_po_min,2); ?></th>
										<th style="text-align:center;"><?=round($min_per).'%'; ?></th>
									<?
								}
								?>
								<th align="right"><?=number_format($gt_po_qty); ?></th>
								<th align="right"><?=number_format($gt_po_min,2); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>       
              
		<?
	}
	elseif ($type == 3)  //Buyer Wise    
	{
			
		$companyArr=return_library_array( "select id, COMPANY_SHORT_NAME from lib_company", "id", "COMPANY_SHORT_NAME"  );
		$buyerArr= return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	
		//---------------------------------------------------------------------------------------

		$tot_month = datediff( 'm',$txt_start_date, $txt_end_date);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($txt_start_date,$i);
			$month_arr[]=date($dateFormat,strtotime($next_month));
		}
		 
		if($cbo_company_id!=''){$where_con=" and a.company_name in($cbo_company_id)";}
		if($cbo_buyer_name!=''){$where_con=" and a.buyer_name in($cbo_buyer_name)";}
		if($cbo_status!='' || $cbo_status!=0){$where_con.=" and b.is_confirmed in($cbo_status)";}
		if($cbo_team_leader!=''){$where_con.=" and a.team_leader in($cbo_team_leader)";}


		if($txt_start_date && $txt_end_date){
			if($category_by == 1){
				$where_con .= "and b.pack_handover_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.pack_handover_date ";
			}elseif($category_by == 2){
				$where_con .= "and b.pub_shipment_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.pub_shipment_date ";
			}elseif($category_by == 3){
				$where_con .= "and b.shipment_date between '$txt_start_date' and '$txt_end_date'";
				$field ="b.shipment_date ";
			}elseif($category_by == 4){
				$where_con .= "and c.country_ship_date between '$txt_start_date' and '$txt_end_date'";
				$field ="c.country_ship_date ";
			}
		}
		
		$sql_con_po="SELECT d.team_name, a.company_name,a.buyer_name,b.is_confirmed, $field as pub_shipment_date, b.shipment_date,  c.order_quantity as po_quantity,a.set_smv,a.location_name, a.TOTAL_SET_QNTY
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,lib_marketing_team d
		WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id And a.team_leader=d.id $where_con and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by d.team_name, a.company_name";
		// echo $sql_con_po;die;

		// ROW SPAN CALCULATION
		$statusArr = explode(',',$cbo_status); 
		$count_status = count($statusArr);
		if ($count_status == 0 || $cbo_status=='' || $cbo_status==0)
		{
			$count_status = 3;
			$statusArr = [1,2,3];
		}

		$poQtyArr=array();$dataArr=array();
		$sql_data_po=sql_select($sql_con_po);
		foreach( $sql_data_po as $row_po)
		{
			$smv_set_qty = $row_po['SET_SMV']/$row_po['TOTAL_SET_QNTY'];
			$monthKey=getCorrespondingMonth($row_po['PUB_SHIPMENT_DATE']); //Declared on the top 
			$key='0__'.$row_po['TEAM_NAME'];
			$poMinuteArr['QTY'][$row_po['IS_CONFIRMED']][$monthKey][$key][$row_po['BUYER_NAME']]+=$row_po['PO_QUANTITY'];
			$poMinuteArr['MIN'][$row_po['IS_CONFIRMED']][$monthKey][$key][$row_po['BUYER_NAME']]+=$row_po['PO_QUANTITY']*$row_po['SET_SMV'];
			// $poMinuteArr[$monthKey]['TOTAL_MIN'] += ($row_po['PO_QUANTITY']*$row_po['SET_SMV']);
			$poMinuteArr[$monthKey]['TOTAL_MIN'] += ($row_po['PO_QUANTITY']*$smv_set_qty);
			$poMinuteArr[$monthKey]['TOTAL_QTY'] += $row_po['PO_QUANTITY'];
			$dataArr[$key][$row_po['BUYER_NAME']]=$row_po['BUYER_NAME'];
			$team_wise_min[$monthKey][$key]+=$row_po['PO_QUANTITY']*$row_po['SET_SMV'];
		}
		unset($sql_data_po);		
		// pre ($dataArr);die;
		$monthCount=count($month_arr);
		$width=($monthCount*270)+310;
		$spnTitle=$monthCount+4;
		$million=1000000; 
		ob_start();	
		?>
		
		<div style="width:<?=$width+22; ?>px; float:left;">
			<fieldset style="width:100%;">
				<table width="<?=$width; ?>" align="left">
					<tr class="form_caption">
						<td colspan="<?=$spnTitle; ?>" align="center"><strong>Team Wise Order Status</strong></td>
					</tr>
				</table>
				<table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="100">Team</th>
							<th width="80">Buyer</th>
							<th width="80">Month</th>
							<? 
							foreach($month_arr as $monthVal)
							{
								if($category_by == 1){
								?>
									<th colspan="2" title="<?= $start_from;?>-<?=$monthVal; ?> to <?= $end_from; ?>-<?= get_next_month($monthVal) ; ?>"> <?=get_next_month($monthVal) ?>  </th> 
								<?
								}
								else{
									?>
									<th colspan="2" title="<?= $start_from;?>-<?=$monthVal; ?> to <?= $end_from; ?>-<?= get_next_month($monthVal) ; ?>"> <?=$monthVal ?> </th>
									<?
								}
							}
							?>
							<th rowspan="2" width="90">Total Qty/Pcs</th>
							<th rowspan="2" width="90">Total Minutes</th>
						</tr>  
						<tr>
							<th width="30"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="80">Order Status</th>
							<? foreach($month_arr as $monthVal)
							{
								?>
								<th width="90">Qty/Pcs</th>
								<th width="90">Total Minutes</th>
								<?
							}
							?>
						</tr> 
					</thead>
				</table>
				<div style="width:<?=$width+18; ?>px; max-height:400px; overflow-y:scroll; float:left;" id="scroll_body">
					<table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
						<?
							$i=0; $k=2; $capacityMonthArr=array();$allocatedMonthArr=array();
							foreach($dataArr as $company_team=>$dataRows)
							{
								$team_total_arr = array();
								foreach ($dataRows as $buyer_id)
								{
									list($company_id,$team_name)=explode('__',$company_team);
									$i++;
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$row_status = true;
									if (in_array(1,$statusArr)) 
									{ 
										?> 
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
												<?
												if ($row_status == true) 
												{
													?>
														<td width="30" align="center" valign="middle" rowspan="<?=$count_status?>"><?=$i; ?></td>
														<td width="100" rowspan="<?=$count_status?>" valign="middle"><?=$team_name; ?></td>
														<td width="80" rowspan="<?=$count_status?>" valign="middle"><?= $buyerArr[$buyer_id]; ?></td>
													<?	 
													$row_status = false;	
												}
												?> 
												<td width="80">Confirm</td>
												<?
												$total_po_qty = 0; 
												$total_po_min = 0;
												foreach($month_arr as $monthVal)
												{
													$po_qty=$poMinuteArr['QTY'][1][$monthVal][$company_team][$buyer_id];
													$po_min=$poMinuteArr['MIN'][1][$monthVal][$company_team][$buyer_id];
													// Team Total Array 
													$row_po_qty= $po_qty + $poMinuteArr['QTY'][2][$monthVal][$company_team][$buyer_id];
													$row_po_min= $po_min + $poMinuteArr['MIN'][2][$monthVal][$company_team][$buyer_id];

													$team_total_arr[$monthVal][$company_team]['TEAM_QTY']  += $row_po_qty; 
													$team_total_arr[$monthVal][$company_team]['TEAM_MIN']  += $row_po_min;
													
													$total_po_qty += $po_qty;
													$total_po_min += $po_min;
													?>
													<td width="90" align="right"><?=number_format($po_qty); ?></td>
													<td width="90" align="right"><?=number_format($po_min,2); ?></td> 
													<?
												}  
												?>
												<td width="90" align="right"><?=number_format($total_po_qty); ?></td>
												<td width="90" align="right"><?=number_format($total_po_min,2); ?></td>
											</tr> 
										<?
									}
									if (in_array(2,$statusArr)) 
									{ 
										?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
												<?
													if ($row_status == true) 
													{
														?>
															<td width="30" align="center" valign="middle" rowspan="<?=$count_status?>"><?=$k-1; ?></td>
															<td width="100" rowspan="<?=$count_status?>" valign="middle"><?=$team_arr[$team_id]; ?></td>
															<td width="80" rowspan="<?=$count_status?>" valign="middle"><?= $buyerArr[$buyer_id]; ?></td>
														<?	 
														$row_status = false;	
													}
												?> 
												<td>Projection</td>
												<?
												$total_po_qty = 0; 
												$total_po_min = 0;
												foreach($month_arr as $monthVal)
												{
													$po_qty=$poMinuteArr['QTY'][2][$monthVal][$company_team][$buyer_id];
													$po_min=$poMinuteArr['MIN'][2][$monthVal][$company_team][$buyer_id];
													$total_po_qty += $po_qty;
													$total_po_min += $po_min;
													?>
													<td align="right"><?=number_format($po_qty); ?></td>
													<td align="right"><?=number_format($po_min,2); ?></td>  
													<?
												}
												?>
												<td width="90" align="right"><?=number_format($total_po_qty); ?></td>
												<td width="90" align="right"><?=number_format($total_po_min,2); ?></td>
											</tr>
										<?
									} 

									if (in_array(3,$statusArr)) 
									{ 
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
												<?
													if ($row_status == true) 
													{
														?>
															<td width="30" align="center" valign="middle" rowspan="<?=$count_status?>"><?=$k-1; ?></td>
															<td width="100" rowspan="<?=$count_status?>" valign="middle"><?=$team_arr[$team_id]; ?></td>
															<td width="80" rowspan="<?=$count_status?>" valign="middle"><?= $buyerArr[$buyer_id]; ?></td>
														<?	 
														$row_status = false;	
													}
												?>
												<td>Sub-Con</td>
												<?
												foreach($month_arr as $monthVal)
												{
													?>
													<td align="right"></td>
													<td align="right"></td>  
													<?
												}
												?>
												<td align="right"></td>
												<td align="right"></td>
											</tr> 
											<?
									} 
										?> 
										<tr  style="color:#111;background:#aeaeae;">
											<th colspan="3"></th>
											<th align="left">Buyer Total </th>
											<? 
											$total_po_qty = 0; 
											$total_po_min = 0;
											foreach($month_arr as $monthVal)
											{
												$buyer_po_qty=$poMinuteArr['QTY'][1][$monthVal][$company_team][$buyer_id] + $poMinuteArr['QTY'][2][$monthVal][$company_team][$buyer_id];
												$buyer_po_min=$poMinuteArr['MIN'][1][$monthVal][$company_team][$buyer_id] + $poMinuteArr['MIN'][2][$monthVal][$company_team][$buyer_id];
												$total_po_qty += $buyer_po_qty;
												$total_po_min += $buyer_po_min;
												?>
												<th align="right"><?=number_format($buyer_po_qty); ?></th>
												<th align="right"><?=number_format($buyer_po_min,2); ?></th> 
												<?
											}
											?>
												<th align="right"><?=number_format($total_po_qty); ?></th>
												<th align="right"><?=number_format($total_po_min,2); ?></th> 
										</tr>
									<?
								}	
								?>	
								<tr style="background:#C2DDF2;">
									<th colspan="3"></th>
									<th align="left">Team Total </th>
									<?
									$total_po_qty = 0; 
									$total_po_min = 0;
									foreach($month_arr as $monthVal)
									{
										$team_po_qty= $team_total_arr[$monthVal][$company_team]['TEAM_QTY'] ;
										$team_po_min=$team_total_arr[$monthVal][$company_team]['TEAM_MIN'];
										$total_po_qty += $team_po_qty;
										$total_po_min += $team_po_min;
										?>
										<th align="right"><?=number_format($team_po_qty); ?></th>
										<th align="right"><?=number_format($team_po_min,2); ?></th> 
										<?
									}
									?>
									<th align="right"><?=number_format($total_po_qty); ?></th>
									<th align="right"><?=number_format($total_po_min,2); ?></th> 
								</tr>
								<? 
							}  
						?>
						<tfoot>
							<tr bgcolor="#877676">
								<th colspan="3"></th>
								<th style="text-align: left;">Grand Total</th>
								<?
								$total_po_qty = 0; 
								$total_po_min = 0;
								foreach($month_arr as $monthVal)
								{
									$g_po_qty= $poMinuteArr[$monthVal]['TOTAL_QTY'];
									$g_po_min= $poMinuteArr[$monthVal]['TOTAL_MIN']; 
									$total_po_qty += $g_po_qty;
									$total_po_min += $g_po_min;
									?>
										<th align="right"><?=number_format($g_po_qty); ?></th>
										<th align="right"><?=number_format($g_po_min,2); ?></th> 
									<?
								}
								?>
								<th align="right"><?=number_format($total_po_qty); ?></th>
								<th align="right"><?=number_format($total_po_min,2); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>       
              
		<?
	}
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename####$type";
	exit();
}
?>
      
 