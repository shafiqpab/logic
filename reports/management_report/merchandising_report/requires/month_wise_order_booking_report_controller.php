<?
include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if($action=="get_company_config"){
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=85 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
	exit();
}

if($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	$type=str_replace("'","",$type);
	
	$companyArr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

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

	if($type==1)
	{
			$date_cond="";
			if($cbo_date_category==1) $date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
			else if($cbo_date_category==2) $date_cond="AND b.shipment_date between '$s_date' and '$e_date'";

			$sql_con_po="SELECT a.job_no, a.company_name, a.buyer_name, b.pub_shipment_date, b.shipment_date, b.is_confirmed, (b.po_quantity*a.total_set_qnty)  as po_quantity
			FROM wo_po_details_master a, wo_po_break_down b
			WHERE a.job_no=b.job_no_mst  AND a.company_name=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.buyer_name";

			$poQtyArr=array();
			$sql_data_po=sql_select($sql_con_po);
			foreach( $sql_data_po as $row_po)
			{
				$monthKey="";
				if($cbo_date_category==1) $monthKey=date("Y-m",strtotime($row_po[csf("pub_shipment_date")]));
				else if($cbo_date_category==2) $monthKey=date("Y-m",strtotime($row_po[csf("shipment_date")]));
				
				$poQtyArr[$row_po[csf("buyer_name")]][$monthKey][$row_po[csf("is_confirmed")]]+=$row_po[csf("po_quantity")];
			}
			unset($sql_data_po);
			//print_r($poQtyArr); die;
			
			/*$sql_capacity="Select a.comapny_id, b.date_calc, b.capacity_pcs 
			from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.date_calc between '$s_date' and '$e_date'";
			$sql_capacityRes=sql_select($sql_capacity); $capacityArr=array();
			foreach($sql_capacityRes as $row)
			{
				$monthKey="";
				$monthKey=date("Y-m",strtotime($row[csf("date_calc")]));
				$capacityArr[$monthKey]+=$row[csf("capacity_pcs")];
			}
			unset($sql_capacityRes);*/
			
			$sql_capacity="Select a.company_id, b.year_month_name, b.sales_target_qty as capacity_pcs from wo_sales_target_mst a, wo_sales_target_dtls b  where a.id=b.sales_target_mst_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 AND a.starting_year between '$cbo_year_name' and '$cbo_end_year_name'";
			$sql_capacityRes=sql_select($sql_capacity); $capacityArr=array();
			foreach($sql_capacityRes as $row)
			{
				$monthKey="";
				//echo $row[csf("starting_year")].'-'.$row[csf("starting_month")].'<br>';
				$exmonth=explode(",",$row[csf("year_month_name")]);
				$monthKey=date("Y-m",strtotime($exmonth[1].'-'.$exmonth[0]));//date("Y-m",strtotime($row[csf("date_calc")]));
				$capacityArr[$monthKey]+=$row[csf("capacity_pcs")];
			}
			unset($sql_capacityRes);
			//print_r($capacityArr); die;
			
			$monthCount=count($month_arr);

			$width=($monthCount*90)+370;
			$spnTitle=$monthCount+4;
			ob_start();
			$titleStr="";
			if($cbo_date_category==1) $titleStr="Pub. Ship Date";
			else if($cbo_date_category==2) $titleStr="Actual Ship Date";
			?> 
			<div>
		    	<fieldset style="width:100%;">
		            <table width="<?=$width; ?>">
		                <tr class="form_caption">
		                    <td colspan="<?=$spnTitle; ?>" align="center"><strong><?=$companyArr[$cbo_company_name]; ?></strong></td>
		                </tr>
		                <tr class="form_caption">
		                    <td colspan="<?=$spnTitle; ?>" align="center"><strong><?=$report_title.' ['.$titleStr.']'; ?></strong></td>
		                </tr>
		            </table>
		            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		                <thead>
		                    <tr>
		                        <th width="30">SL</th>
		                        <th width="100">Buyer</th>
		                        <th width="100">Particulars /Month</th>
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
		            <div style="width:<?=$width; ?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
		            <table class="rpt_table" width="<?=$width-17; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
		            	<tr bgcolor="#FFFFAA">
		                	<td width="30">&nbsp;</td>
		                    <td width="100">&nbsp;</td>
		                    <td width="100"><b>Capacity (Qty):</b></td>
		                    <? $rCapQty=0; 
							foreach($month_arr as $monthVal)
		                    {
								//echo date("Y-m",strtotime($monthVal)).'<br>';
								$rCapQty+=$capacityArr[$monthVal];
		                        ?>
		                    	<td width="90" align="right" style="word-break:break-all"><?=fn_number_format($capacityArr[$monthVal]); ?>&nbsp;</td>
								<?
		                    }
		                    ?>
		                    <td align="right" style="word-break:break-all"><?=fn_number_format($rCapQty); ?>&nbsp;</td>
		                </tr>
		                <?
							$i=1; $k=2; $grandMonthTotArr=array();
							foreach($poQtyArr as $buyerId=>$buyerData)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								 <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
									<td width="30" align="center" valign="middle" rowspan="2"><?=$k-1; ?></td>
									<td width="100" rowspan="2" valign="middle"><?=$buyerArr[$buyerId]; ?></td>
									<td width="100">Projected (Qty)</td>
									<?
									$rProjectedQty=0; $buyerMonthTotArr=array();
									foreach($month_arr as $monthVal)
									{
										$projectedQty=0;
										$projectedQty=$buyerData[$monthVal][2];
										$rProjectedQty+=$projectedQty;
										$buyerMonthTotArr[$monthVal]+=$projectedQty;
										$grandMonthTotArr[$monthVal]['proj']+=$projectedQty;
										?>
										<td width="90" align="right" style="word-break:break-all"><?=fn_number_format($projectedQty); ?>&nbsp;</td>
										<?
									}
									$i++;
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<td align="right" style="word-break:break-all"><?=fn_number_format($rProjectedQty); ?>&nbsp;</td>
								 </tr>
		                         
		                         <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
									<td width="100">Confirm (Qty)</td>
									<?
									$rConfirmQty=0;
									foreach($month_arr as $monthVal)
									{
										$confirmQty=0;
										$confirmQty=$buyerData[$monthVal][1];
										$rConfirmQty+=$confirmQty;
										$buyerMonthTotArr[$monthVal]+=$confirmQty;
										$grandMonthTotArr[$monthVal]['conf']+=$confirmQty;
										?>
										<td width="90" align="right" style="word-break:break-all"><?=fn_number_format($confirmQty); ?>&nbsp;</td>
										<?
									}
									?>
									<td align="right" style="word-break:break-all"><?=fn_number_format($rConfirmQty); ?>&nbsp;</td>
								 </tr>
		                         
		                         <tr bgcolor="#FFCCFF">
									<td colspan="3" align="right"><b>Buyer [<?=$buyerArr[$buyerId]; ?>] Total :</b></td>
									<?
									$rBuyerMonthTot=0;
									foreach($month_arr as $monthVal)
									{
										$buyerMonthTot=0;
										$buyerMonthTot=$buyerMonthTotArr[$monthVal];
										$rBuyerMonthTot+=$buyerMonthTot;
										?>
										<td width="90" align="right" style="word-break:break-all"><b><?=fn_number_format($buyerMonthTot); ?></b>&nbsp;</td>
										<?
									}
									?>
									<td align="right" style="word-break:break-all"><b><?=fn_number_format($rBuyerMonthTot); ?></b>&nbsp;</td>
								 </tr>
								<?
								$i++; $k++;
							}
							$grandMonthTotalArr=array();
						?>
		            </table>
		            </div>
		            <table class="tbl_bottom" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		                <thead>
		                    <tr>
		                        <td width="30">&nbsp;</td>
		                        <td width="100">&nbsp;</td>
		                        <td width="100"><b>Tot. Proj. (Qty):</b></td>
		                        <?
		                            $rGProjectedTot=0;
		                            foreach($month_arr as $monthVal)
		                            {
		                                $gProjectedTot=0;
		                                $gProjectedTot=$grandMonthTotArr[$monthVal]['proj'];
		                                $rGProjectedTot+=$gProjectedTot;
		                                $grandMonthTotalArr[$monthVal]+=$gProjectedTot;
		                                ?>
		                                <td width="90" align="right" style="word-break:break-all"><b><?=fn_number_format($gProjectedTot); ?></b>&nbsp;</td>
		                                <?
		                            }
		                            ?>
		                        <td align="right" style="word-break:break-all"><b><?=fn_number_format($rGProjectedTot); ?></b>&nbsp;</td>
		                    </tr>
		                    <tr>
		                        <td colspan="3"><b>Tot. Confirm (Qty):</b></td>
		                        <?
		                            $rGConfirmTot=0;
		                            foreach($month_arr as $monthVal)
		                            {
		                                $gConfirmTot=0;
		                                $gConfirmTot=$grandMonthTotArr[$monthVal]['conf'];
		                                $rGConfirmTot+=$gConfirmTot;
		                                $grandMonthTotalArr[$monthVal]+=$gConfirmTot;
		                                ?>
		                                <td width="90" align="right" style="word-break:break-all"><b><?=fn_number_format($gConfirmTot); ?></b>&nbsp;</td>
		                                <?
		                            }
		                            ?>
		                        <td align="right" style="word-break:break-all"><b><?=fn_number_format($rGConfirmTot); ?></b>&nbsp;</td>
		                    </tr>
		                    <tr>
		                        <td colspan="3"><b>Grand Total (Qty):</b></td>
		                        <?
		                            $rGrandTot=0;
		                            foreach($month_arr as $monthVal)
		                            {
		                                $grandTot=0;
		                                $grandTot=$grandMonthTotalArr[$monthVal];
		                                $rGrandTot+=$grandTot;
		                                ?>
		                                <td width="90" align="right" style="word-break:break-all"><b><?=fn_number_format($grandTot); ?></b>&nbsp;</td>
		                                <?
		                            }
		                            ?>
		                        <td align="right" style="word-break:break-all"><b><?=fn_number_format($rGrandTot); ?></b>&nbsp;</td>
		                    </tr>
		                    <tr bgcolor="#AAFF55">
		                        <th width="30">&nbsp;</th>
		                        <th width="100">&nbsp;</th>
		                        <th width="100"><b>Bal. Capacity (Qty):</b></th>
		                        <?
		                            $rCapBalTot=0;
		                            foreach($month_arr as $monthVal)
		                            {
		                                $capBalTot=0;
		                                $capBalTot=$capacityArr[$monthVal]-$grandMonthTotalArr[$monthVal];
		                                $rCapBalTot+=$capBalTot;
		                                ?>
		                                <th width="90" align="right" style="word-break:break-all"><b><?=fn_number_format($capBalTot); ?></b>&nbsp;</th>
		                                <?
		                            }
		                            ?>
		                        <th align="right" style="word-break:break-all"><b><?=fn_number_format($rCapBalTot); ?></b>&nbsp;</th>
		                    </tr>
		                </thead>
		            </table>
		        </fieldset>
		    </div>
		    <?
	}
	else if($type==2)
	{
			$date_cond="";
			$sle_cond="";
			if($cbo_date_category==1){
				$date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
				$sle_cond="ltrim (to_char (b.pub_shipment_date, 'yyyy-mm'), '0') as year_month";
			} 
			else if($cbo_date_category==2){
				$date_cond="AND b.shipment_date between '$s_date' and '$e_date'";
				$sle_cond="ltrim (to_char (b.shipment_date, 'yyyy-mm'), '0') as year_month";
			} 

			$sql_con_po="SELECT a.job_no, a.company_name, a.buyer_name, b.pub_shipment_date, b.shipment_date, b.is_confirmed, c.order_quantity as po_quantity
			FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id AND a.company_name=$cbo_company_name $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.buyer_name";

			$poQtyArr=array();
			$sql_data_po=sql_select($sql_con_po);
			foreach( $sql_data_po as $row_po)
			{
				$monthKey="";
				if($cbo_date_category==1) $monthKey=date("Y-m",strtotime($row_po[csf("pub_shipment_date")]));
				else if($cbo_date_category==2) $monthKey=date("Y-m",strtotime($row_po[csf("shipment_date")]));
				
				$poQtyArr[$row_po[csf("buyer_name")]][$monthKey][$row_po[csf("is_confirmed")]]+=$row_po[csf("po_quantity")];
			}
			unset($sql_data_po);
			//print_r($poQtyArr); die;
			
			/*$sql_capacity="Select a.comapny_id, b.date_calc, b.capacity_pcs 
			from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.date_calc between '$s_date' and '$e_date'";
			$sql_capacityRes=sql_select($sql_capacity); $capacityArr=array();
			foreach($sql_capacityRes as $row)
			{
				$monthKey="";
				$monthKey=date("Y-m",strtotime($row[csf("date_calc")]));
				$capacityArr[$monthKey]+=$row[csf("capacity_pcs")];
			}
			unset($sql_capacityRes);*/
			
			$sql_capacity="Select a.company_id, b.year_month_name, b.sales_target_qty as capacity_pcs from wo_sales_target_mst a, wo_sales_target_dtls b  where a.id=b.sales_target_mst_id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 AND a.starting_year between '$cbo_year_name' and '$cbo_end_year_name'";
			$sql_capacityRes=sql_select($sql_capacity); $capacityArr=array();
			foreach($sql_capacityRes as $row)
			{
				$monthKey="";
				//echo $row[csf("starting_year")].'-'.$row[csf("starting_month")].'<br>';
				$exmonth=explode(",",$row[csf("year_month_name")]);
				$monthKey=date("Y-m",strtotime($exmonth[1].'-'.$exmonth[0]));//date("Y-m",strtotime($row[csf("date_calc")]));
				$capacityArr[$monthKey]+=$row[csf("capacity_pcs")];
			}
			unset($sql_capacityRes);
			//print_r($capacityArr); die;


			$sql_main="SELECT a.buyer_name,
					       a.job_no,
					       b.shipment_date,
					       (b.po_quantity*a.total_set_qnty) as  po_quantity,
					       $sle_cond,
					       b.is_confirmed,
					       b.unit_price,
					       b.po_total_price
					    from wo_po_details_master a, wo_po_break_down b
					    where     a.id = b.job_id
					    	AND a.company_name=$cbo_company_name
					       and a.job_no = b.job_no_mst
					       
					     
					      
					      and  a.status_active=1 
					      and a.is_deleted=0 
					      and b.status_active=1 
					      and b.is_deleted=0 
					      
					      
					      $date_cond order by a.buyer_name";
			//echo $sql_main;
			//print_r($month_arr);
			$results=sql_select($sql_main);

			$job_no_arr=array();

			foreach ($results as $row) 
			{
				array_push($job_no_arr, $row[csf('job_no')]);
			}
			$job_no_arr=array_unique($job_no_arr);

			$sql_cost=sql_select("SELECT b.cm_cost ,a.costing_per , a.sew_smv,b.job_no from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.status_active=1 and b.status_active=1  ".where_con_using_array($job_no_arr,1,"b.job_no"));
			

			$job_wise_data=array();

			foreach ($sql_cost as $row) {
				$job_wise_data[$row[csf('job_no')]]['cm_cost']=$row[csf('cm_cost')];
				$job_wise_data[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				$job_wise_data[$row[csf('job_no')]]['sew_smv']=$row[csf('sew_smv')];
			}
			$confirmed_arr=array();
			$projected_arr=array();

			$buyer_confirmed=array();
			$buyer_projected=array();

			$confirm_order_min=array();
			$project_order_min=array();

			foreach ($results as $row) 
			{
				$sew_smv=$job_wise_data[$row[csf('job_no')]]['sew_smv'];
				$costing_per=$job_wise_data[$row[csf('job_no')]]['costing_per'];
				$cm_cost=$job_wise_data[$row[csf('job_no')]]['cm_cost'];

				if($row[csf('is_confirmed')]==1)
				{

					$confirmed_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['job_no'].=$row[csf('job_no')].",";
					$confirmed_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['po_quantity']+=$row[csf('po_quantity')];
					//$confirmed_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['sew_smv']+=$row[csf('sew_smv')];
					$confirmed_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['order_value']+=$row[csf('po_total_price')];
					$confirmed_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['order_min']+=($row[csf('po_quantity')]*$sew_smv);
					$confirm_order_min[$row[csf('year_month')]]+=($row[csf('po_quantity')]*$sew_smv);
					array_push($buyer_confirmed, $row[csf('buyer_name')]);



					$cm=0;
					if($costing_per==1)
					{
						$cm=$cm_cost/12;
					}
					else if($costing_per==2)
					{
						$cm=$cm_cost;
					}
					else if($costing_per==3)
					{
						$cm=$cm_cost/24;
					}
					else if($costing_per==4)
					{
						$cm=$cm_cost/36;
					}
					else if($costing_per==5)
					{
						$cm=$cm_cost/48;
					}
					$confirmed_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['cm_cost']+=($cm*$row[csf('po_quantity')]);



				}
				else if($row[csf('is_confirmed')]==2)
				{
					$projected_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['job_no'].=$row[csf('job_no')].",";
					$projected_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['po_quantity']+=$row[csf('po_quantity')];
					//$projected_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['sew_smv']+=$row[csf('sew_smv')];
					$projected_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['order_value']+=$row[csf('po_total_price')];

					$projected_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['order_min']+=($row[csf('po_quantity')]*$sew_smv);
					$confirm_order_min[$row[csf('year_month')]]+=($row[csf('po_quantity')]*$sew_smv);

					array_push($buyer_projected, $row[csf('buyer_name')]);
					$cm=0;
					if($costing_per==1)
					{
						$cm=$cm_cost/12;
					}
					else if($costing_per==2)
					{
						$cm=$cm_cost;
					}
					else if($costing_per==3)
					{
						$cm=$cm_cost/24;
					}
					else if($costing_per==4)
					{
						$cm=$cm_cost/36;
					}
					else if($costing_per==5)
					{
						$cm=$cm_cost/48;
					}
					$projected_arr[$row[csf('buyer_name')]][$row[csf('year_month')]]['cm_cost']+=($cm*$row[csf('po_quantity')]);
				}
			}

			foreach ($confirmed_arr as $buyer_id => $buyer_wise) 
			{
				foreach ($buyer_wise as $year_month => $month_wise) 
				{
					if($month_wise['po_quantity']>0)
					{
						$confirmed_arr[$buyer_id][$year_month]['sew_smv']= $month_wise['order_min']/$month_wise['po_quantity'];
					}
					else{
						$confirmed_arr[$buyer_id][$year_month]['sew_smv']= 0;
					}
					
				}
			}

			foreach ($projected_arr as $buyer_id => $buyer_wise) 
			{
				foreach ($buyer_wise as $year_month => $month_wise) 
				{
					

					if($month_wise['po_quantity']>0)
					{
						$projected_arr[$buyer_id][$year_month]['sew_smv']= $month_wise['order_min']/$month_wise['po_quantity'];
					}
					else{
						$projected_arr[$buyer_id][$year_month]['sew_smv']= 0;
					}
				}
			}
			

			
			
			$monthCount=count($month_arr);

			$width=($monthCount*800)+130;
			$spnTitle=$monthCount+4;
			ob_start();
			$titleStr="";
			if($cbo_date_category==1) $titleStr="Pub. Ship Date";
			else if($cbo_date_category==2) $titleStr="Actual Ship Date";

			$pro_share_total=array();
			$pro_month_wise=array();
			$com_month_wise=array();
			?> 
			<div>
		    	<fieldset style="width:100%;">
		            <table width="<?=$width; ?>">
		                <tr class="form_caption">
		                    <td colspan="<?=$spnTitle; ?>" align="center"><strong><?=$companyArr[$cbo_company_name]; ?></strong></td>
		                </tr>
		                <tr class="form_caption">
		                    <td colspan="<?=$spnTitle; ?>" align="center"><strong><?=$report_title.' ['.$titleStr.']'; ?></strong></td>
		                </tr>
		            </table>

		            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		            	<caption style="justify-content: left;text-align: left;font-weight: bold;">Projected Order</caption>
		                <thead>
		                	
		                    <tr>
		                        <th rowspan="2" width="30">SL</th>
		                        <th rowspan="2" width="100">Buyer</th>
		                        
		                        <? foreach($month_arr as $monthVal)
		                        {
		                            ?>
		                            <th colspan="8" width="720"><?=date("M-y",strtotime($monthVal)); ?></th>
		                            <?
		                        }
		                        ?>
		                        
		                    </tr>
		                    <tr>
		                    	 <? foreach($month_arr as $monthVal)
		                        {
		                            ?>
			                    	


			                        <th width="100" title="Order value/Order qnty">Avg FOB ($)</th>
			                        <th width="100" title="CM value / Order Qnty">Avg CM ($)</th>
			                        <th width="100" title="Order min/PO Quantity">Avg SMV</th>
			                        <th width="100">Quantity<br> (Pcs)</th>
			                        <th width="100" title="SMV * PO Quantity">Order Minute</th>
			                        <th width="100" title="(Order min/Total order min )*100">Share%</th>
			                        <th width="100" >Order Value ($)</th>
			                        <th width="100" title="CM Cost  * PO Qnty">CM Value ($)</th>
			                         <?
		                        }
		                        ?>
		                    </tr>
		                </thead>
		            </table>
		            
		            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" >
		            	<tbody id="scroll_body" style="max-height:400px; overflow-y:scroll">
		            		<?php 

		            			$i=1;

		            			$buyer_projected=array_unique($buyer_projected);

		            			

		            			foreach ($buyer_projected as $key => $value) 
		            			{
		            				
		            				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		            				?>

		            				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
					                	<td width="30"><? echo $i; ?></td>
					                    <td width="100"><?php echo $buyerArr[$value]; ?></td>


					                   
					                    <? foreach($month_arr as $monthVal)
					                        {

					                        	$cm_value=$projected_arr[$value][$monthVal]['cm_cost'];
					                        	$order_qnty=$projected_arr[$value][$monthVal]['po_quantity'];
					                        	$sew_smv=$projected_arr[$value][$monthVal]['sew_smv'];
					                        	$avg_cm=0;
					                        	if($order_qnty==0)
					                        	{
					                        		$avg_cm=0;
					                        	}
					                        	else{
					                        		$avg_cm=$cm_value/$order_qnty;
					                        	}


					                        	$order_value=$projected_arr[$value][$monthVal]['order_value'];

					                        	if($order_qnty==0)
					                        	{
					                        		$avg_fob=0;
					                        	}
					                        	else{
					                        		$avg_fob=$order_value/$order_qnty;
					                        	}

					                        	$order_min=$sew_smv*$order_qnty;

					                        	$share=0;
					                        	
					                        	if($confirm_order_min[$monthVal]>0)
					                        	{
					                        		$share=($order_min/$confirm_order_min[$monthVal])*100;
					                        	}
					                        	
					                        	

					                        	$pro_month_wise[$monthVal]['avg_fob']+=$avg_fob;
					                        	$pro_month_wise[$monthVal]['avg_cm']+=$avg_cm;
					                        	$pro_month_wise[$monthVal]['sew_smv']+=$sew_smv;
					                        	$pro_month_wise[$monthVal]['order_qnty']+=$order_qnty;
					                        	$pro_month_wise[$monthVal]['order_min']+=$order_min;
					                        	$pro_month_wise[$monthVal]['order_value']+=$order_value;
					                        	$pro_month_wise[$monthVal]['cm_value']+=$cm_value;
					                        	$pro_month_wise[$monthVal]['share']+=$share;
					                        	$pro_month_wise[$monthVal]['cnt']+=1;
					                            ?>
						                    	<td align="right" width="100" title="Order value/Order qnty"><?php echo number_format($avg_fob,2) ?></td>
						                        <td align="right" width="100" title="CM value / Order Qnty"><?php echo number_format($avg_cm,2); ?></td>
						                        <td align="right" width="100" title="Order min/PO Quantity"><?php echo number_format($sew_smv,2); ?></td>
						                        <td align="right" width="100" title="SMV * PO Quantity"><?php echo number_format($order_qnty); ?></td>
						                        <td align="right" width="100"><?php echo number_format($order_min,2); ?></td>
						                        <td align="right" width="100" title="(Order min/Total order min )*100"><?php echo number_format($share,2) ?>%</td>
						                        <td align="right" width="100"><?php echo number_format($order_value,2); ?></td>
						                        <td align="right" width="100" title="CM Cost  * PO Qnty"><?php echo number_format($cm_value,2); ?></td>
						                         <?
					                        }
					                        ?>
					                </tr>

		            				<?
		            				$i++;
		            			}

		            		 ?>
		            		
		            	</tbody>
		                
		            </table>
		           
		            <table class="tbl_bottom" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		                <tfoot>
		                
		                  <tfoot>
		                    <tr >
			                	<td width="30">&nbsp;</td>
			                    <td width="100" align="right">Total</td>
			                   
			                    <? foreach($month_arr as $monthVal)
			                        {
			                            ?>
				                    	<td align="right" width="100"><?php echo number_format($pro_month_wise[$monthVal]['avg_fob']/$pro_month_wise[$monthVal]['cnt'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($pro_month_wise[$monthVal]['avg_cm']/$pro_month_wise[$monthVal]['cnt'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($pro_month_wise[$monthVal]['sew_smv']/$pro_month_wise[$monthVal]['cnt'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($pro_month_wise[$monthVal]['order_qnty']) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($pro_month_wise[$monthVal]['order_min'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($pro_month_wise[$monthVal]['share'],2) ?>%</td>
				                        <td align="right"  width="100"><?php echo number_format($pro_month_wise[$monthVal]['order_value'],2) ?></td>
				                        <td align="right" width="100"><?php echo number_format($pro_month_wise[$monthVal]['cm_value'],2) ?></td>
				                         <?
			                        }
			                        ?>
			                </tr>
		                </tfoot>
		            </table>
		            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		            	<caption style="justify-content: left;text-align: left;font-weight: bold;">Confirmed Order</caption>
		                <thead>
		                	
		                    <tr>
		                        <th rowspan="2" width="30">SL</th>
		                        <th rowspan="2" width="100">Buyer</th>
		                        
		                        <? foreach($month_arr as $monthVal)
		                        {
		                            ?>
		                            <th colspan="8" width="720"><?=date("M-y",strtotime($monthVal)); ?></th>
		                            <?
		                        }
		                        ?>
		                        
		                    </tr>
		                    <tr>
		                    	 <? foreach($month_arr as $monthVal)
		                        {
		                            ?>
			                    	<th width="100" title="Order value/Order qnty">Avg FOB ($)</th>
			                        <th width="100" title="CM value / Order Qnty">Avg CM ($)</th>
			                        <th width="100" title="Order min/PO Quantity">Avg SMV</th>
			                        <th width="100">Quantity<br> (Pcs)</th>
			                        <th width="100" title="SMV * Po Quantity">Order Minute</th>
			                        <th width="100" title="(Order min/Total order min )*100">Share%</th>
			                        <th width="100">Order Value ($)</th>
			                        <th width="100" title="CM Cost  * PO Qnty">CM Value ($)</th>
			                         <?
		                        }
		                        ?>
		                    </tr>
		                </thead>
		            </table>
		           <!--  <div style="width:<?=$width+100; ?>px; max-height:400px; overflow-y:scroll" id="scroll_body"> -->
		            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" >
		            	<tbody  id="scroll_body" style="max-height:400px; overflow-y:scroll">
		            		<?php 
		            			$cnt=$i;
		            			$i=1;

		            			$buyer_confirmed=array_unique($buyer_confirmed);

		            			foreach ($buyer_confirmed as $key => $value) 
		            			{
		            				
		            				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		            				?>

		            				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i+$cnt; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i+$cnt; ?>">
					                	<td width="30"><? echo $i; ?></td>
					                    <td width="100"><?php echo $buyerArr[$value]; ?></td>


					                   
					                    <? foreach($month_arr as $monthVal)
					                        {

					                        	$cm_value=$confirmed_arr[$value][$monthVal]['cm_cost'];
					                        	$order_qnty=$confirmed_arr[$value][$monthVal]['po_quantity'];
					                        	$sew_smv=$confirmed_arr[$value][$monthVal]['sew_smv'];
					                        	$avg_cm=0;
					                        	if($order_qnty==0)
					                        	{
					                        		$avg_cm=0;
					                        	}
					                        	else{
					                        		$avg_cm=$cm_value/$order_qnty;
					                        	}


					                        	$order_value=$confirmed_arr[$value][$monthVal]['order_value'];

					                        	if($order_qnty==0)
					                        	{
					                        		$avg_fob=0;
					                        	}
					                        	else{
					                        		$avg_fob=$order_value/$order_qnty;
					                        	}

					                        	$order_min=$sew_smv*$order_qnty;

					                        	$share=0;
					                        	
					                        	if($confirm_order_min[$monthVal]>0)
					                        	{
					                        		$share=($order_min/$confirm_order_min[$monthVal])*100;
					                        	}
					                        	
					                        	$com_month_wise[$monthVal]['avg_fob']+=$avg_fob;
					                        	$com_month_wise[$monthVal]['avg_cm']+=$avg_cm;
					                        	$com_month_wise[$monthVal]['sew_smv']+=$sew_smv;
					                        	$com_month_wise[$monthVal]['order_qnty']+=$order_qnty;
					                        	$com_month_wise[$monthVal]['order_min']+=$order_min;
					                        	$com_month_wise[$monthVal]['order_value']+=$order_value;
					                        	$com_month_wise[$monthVal]['cm_value']+=$cm_value;
					                        	$com_month_wise[$monthVal]['share']+=$share;
					                        	$com_month_wise[$monthVal]['cnt']+=1;

					                        	
					                            ?>
						                    	<td align="right" width="100" title="Order value/Order qnty"><?php echo number_format($avg_fob,2) ?></td>
						                        <td align="right" width="100" title="CM value / Order Qnty"><?php echo number_format($avg_cm,2); ?></td>
						                        <td align="right" width="100" title="Order min/PO Quantity"><?php echo number_format($sew_smv,2); ?></td>
						                        <td align="right" width="100"><?php echo number_format($order_qnty); ?></td>
						                        <td align="right" width="100" title="SMV * PO Quantity"><?php echo number_format($order_min,2); ?></td>
						                        <td align="right" width="100" title="(Order min/Total order min )*100"><?php echo number_format($share,2) ?>%</td>
						                        <td align="right" width="100"><?php echo number_format($order_value,2); ?></td>
						                        <td align="right" width="100" title="CM Cost  * PO Qnty"><?php echo number_format($cm_value,2); ?></td>
						                         <?
					                        }
					                        ?>
					                </tr>

		            				<?
		            				$i++;
		            			}

		            		 ?>
		            		
		            	</tbody>
		                
		            </table>
		           
		            <table class="tbl_bottom" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		                <tfoot>
		                    <tr >
			                	<td width="30">&nbsp;</td>
			                    <td width="100" align="right">Total</td>
			                   
			                    <? foreach($month_arr as $monthVal)
			                        {
			                            ?>
				                    	<td align="right" width="100"><?php echo number_format($com_month_wise[$monthVal]['avg_fob']/$com_month_wise[$monthVal]['cnt'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['avg_cm']/$com_month_wise[$monthVal]['cnt'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['sew_smv']/$com_month_wise[$monthVal]['cnt'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['order_qnty']) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['order_min'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['share'],2) ?>%</td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['order_value'],2) ?></td>
				                        <td align="right" width="100"><?php echo number_format($com_month_wise[$monthVal]['cm_value'],2) ?></td>
				                         <?
			                        }
			                        ?>
			                </tr>


		                </tfoot>
		            </table>

		            <table class="tbl_bottom" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		                <tfoot>
		                    <tr bgcolor="#dddddd">
		                    	<td width="30">&nbsp;</td>
			                    <td width="100" align="right">Grand Total</td>
			                	
			                    
			                   
			                    <? foreach($month_arr as $monthVal)
			                        {
			                            ?>
				                    	<td align="right" width="100"><?php echo number_format(($com_month_wise[$monthVal]['avg_fob']+$pro_month_wise[$monthVal]['avg_fob'])/($pro_month_wise[$monthVal]['cnt']+$com_month_wise[$monthVal]['cnt']),2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format(($com_month_wise[$monthVal]['avg_cm']+$pro_month_wise[$monthVal]['avg_cm'])/($pro_month_wise[$monthVal]['cnt']+$com_month_wise[$monthVal]['cnt']),2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format(($com_month_wise[$monthVal]['sew_smv']+$pro_month_wise[$monthVal]['sew_smv'])/($pro_month_wise[$monthVal]['cnt']+$com_month_wise[$monthVal]['cnt']),2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['order_qnty']+$pro_month_wise[$monthVal]['order_qnty']) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['order_min']+$pro_month_wise[$monthVal]['order_min'],2) ?></td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['share']+$pro_month_wise[$monthVal]['share'],2) ?>%</td>
				                        <td align="right"  width="100"><?php echo number_format($com_month_wise[$monthVal]['order_value']+$pro_month_wise[$monthVal]['order_value'],2) ?></td>
				                        <td align="right" width="100"><?php echo number_format($com_month_wise[$monthVal]['cm_value']+$pro_month_wise[$monthVal]['cm_value'],2) ?></td>
				                         <?
			                        }
			                        ?>
			                </tr>
		                </tfoot>

		                
				                      
		            </table>


		        </fieldset>
		    </div>
		    <?

	}
	
	
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
	echo "$html****$filename";
	//echo "$html";
	exit();
}
?>