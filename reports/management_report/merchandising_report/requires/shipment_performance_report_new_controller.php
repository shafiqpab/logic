<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
extract($_REQUEST);
//------------------------------------------------------------------------------------------------------------
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/shipment_performance_report_new_controller', this.value, 'load_drop_down_brand', 'brand_td')" );   	 
	exit();
}
if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 140, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0 order by id ASC", "id", "company_name");


if($action=="report_generate")
{ 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_product_department=str_replace("'","",$cbo_product_department)==0?'%%':str_replace("'","",$cbo_product_department);
	
	if($company_name!=0)$company_library=array($company_name=>$company_library[$company_name]); else $company_name=''; 
	
	$buyerCondJob=""; $buyerCondSalse="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyerCondSalse=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyerCondJob=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyerCondSalse=""; $buyerCondJob="";
			}
		}
		else
		{
			$buyerCondSalse=""; $buyerCondJob="";
		}
	}
	else
	{
		$buyerCondSalse=" and a.buyer_id=$cbo_buyer_name";
		$buyerCondJob=" and a.buyer_name=$cbo_buyer_name";
	}
	
	$preYear=str_replace("'","",$cbo_year_selection)-5;
	$searchYear=str_replace("'","",$cbo_year_selection);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	$team_leader=str_replace("'","",$cbo_team_leader);
	
	//$txt_date_from=str_replace("'","",$txt_date_from);
	//$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_date_from="01-Jan-".str_replace("'","",$cbo_year_selection);
	$txt_date_to="31-Dec-".str_replace("'","",$cbo_year_selection);
	
	$targetyear_cond="to_char(b.sales_target_date,'YYYY')";
	$year_cond="to_char(b.pub_shipment_date,'YYYY')";
	$dateCondSalse=""; $dateCondJob="";
	$cbo_date_type=2;
	$cbo_date_type=str_replace("'","",$cbo_date_type);	
	if($cbo_date_type==2)	
	{
		if($db_type==2) 
		{
			 $dateCondSalse=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 $dateCondJob=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
	}
	else if($cbo_date_type==1)
	{
		if($db_type==2) 
		{
			 $dateCondSalse=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 $dateCondJob=" and c.country_ship_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
	}
	else
	{	
		if($db_type==2) 
		{
			$dateCondSalse=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			$dateCondJob=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
	}
	
	$dateCondSalse=" and $targetyear_cond between '".$preYear."' and '".str_replace("'","",$cbo_year_selection)."'";
	$dateCondJob=" and $year_cond between '".$preYear."' and '".str_replace("'","",$cbo_year_selection)."'";
	$shipmentDateCond=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	
	if($team_leader==0)
	{
		$tlCondSalse=""; $tlCondJob="";
	}
	else
	{
		$tlCondSalse=" and a.team_leader=$cbo_team_leader";
		$tlCondJob=" and a.team_leader=$cbo_team_leader";
	}
	
	//echo $date_cond.'='.$date_cond2; die;
	ob_start();
	//echo $cbo_date_type;
	if($cbo_date_type==1)
	{
		$sql="select a.job_no, a.quality_level, a.set_smv, sum(c.order_quantity) as po_quantity, c.country_ship_date as shipment_date, sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.quality_level in (12,13,14) and a.company_name=$company_name $buyerCondJob $tlCondJob $dateCondJob GROUP BY a.job_no, a.quality_level, a.set_smv, c.country_ship_date";
	}
	else
	{
		$sql="select a.job_no, a.quality_level, a.set_smv, sum(b.po_quantity*a.total_set_qnty) as po_quantity, b.pub_shipment_date as shipment_date, sum(b.po_total_price) as amount from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.quality_level in (12,13,14) and a.company_name=$company_name $buyerCondJob $tlCondJob $dateCondJob GROUP BY a.job_no, a.quality_level, a.set_smv, b.pub_shipment_date";
	}
	//echo $sql;
	$sql_res=sql_select($sql);
	
	$achieveTgtDateArr=array(); $noOfJobArr=array(); $yearSummDataArr=array(); $yearSummJobArr=array();

	foreach($sql_res as $row)
	{
		$shipmonth=date("Y-m",strtotime($row[csf('shipment_date')]));
		$exmonth=explode('-',$shipmonth);
		$newmonth=($exmonth[1]*1).'-'.$exmonth[0];
		$achivMint=0;
		$achivMint=$row[csf('po_quantity')]*$row[csf('set_smv')];
		//echo $newmonth.'-o<br>';
		$achieveTgtDateArr[1][$newmonth][$row[csf('quality_level')]]+=$row[csf('po_quantity')];
		$achieveTgtDateArr[2][$newmonth][$row[csf('quality_level')]]+=$row[csf('amount')];
		$achieveTgtDateArr[3][$newmonth][$row[csf('quality_level')]]+=$achivMint;
		
		$yearSummDataArr[2][1][$exmonth[0]][$row[csf('quality_level')]]+=$row[csf('po_quantity')];
		$yearSummDataArr[2][2][$exmonth[0]][$row[csf('quality_level')]]+=$row[csf('amount')];
		$yearSummDataArr[2][3][$exmonth[0]][$row[csf('quality_level')]]+=$achivMint;
		
		//$achieveTgtDateArr[4][$newmonth][$row[csf('quality_level')]]=$row[csf('job_no')];
		$noOfJobArr[$newmonth][$row[csf('quality_level')]][$row[csf('job_no')]]=$row[csf('job_no')];
		$yearSummJobArr[$exmonth[0]][$row[csf('quality_level')]][$row[csf('job_no')]]=$row[csf('job_no')];
	}
	unset($sql_res);
	//echo "<pre>";
	//print_r($yearSummDataArr[2]);die;
	//echo $sql;
	
	$sql_sales=sql_select("select a.id, a.company_id, a.team_leader, b.sales_target_date, b.sales_target_qty as sales_target_qty,b.sales_target_value,b.sales_target_mint as sales_target_mint from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id =$company_name  $buyerCondSalse $tlCondSalse $dateCondSalse order by b.sales_target_date,a.company_id");
	
	$sale_data_arr=array(); $salesTgtDateArr=array(); 
	foreach($sql_sales as $row)
	{
		$sale_data_arr[$row[csf("id")]]=$row[csf("company_id")];
		$mst_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	unset($sql_sales);

	$nature_cond=where_con_using_array($mst_id_arr,0,"mst_id");
	$sql_nature="SELECT MST_ID, ID, TARGET_TYPE, NATURE_ID, TARGET_MONTH, TARGET_YEAR, TARGET_QTY FROM WO_SALES_TARGET_NATURE_DTLS WHERE NATURE_ID in (12,13,14) and status_active=1 and is_deleted=0 $nature_cond";
	
	$res_nature=sql_select($sql_nature);
	foreach ($res_nature as $row)
	{
		$dateNature=$row[csf('TARGET_MONTH')].'-'.$row[csf('TARGET_YEAR')]; 
		
		$salesTgtDateArr[$row[csf('TARGET_TYPE')]][$dateNature][$row[csf('NATURE_ID')]]+=$row[csf('TARGET_QTY')];
		$yearSummDataArr[1][$row[csf('TARGET_TYPE')]][$row[csf('TARGET_YEAR')]][$row[csf('NATURE_ID')]]+=$row[csf('TARGET_QTY')];
	}
	unset($res_nature);
	//print_r($yearSummDataArr[1][1]);
	
	if($cbo_date_type==1)
	{
		$shipsql="select a.quality_level, a.set_smv, a.job_no, b.id as poid, c.order_quantity as po_quantity, c.country_ship_date as shipment_date, c.order_total as amount, d.ex_factory_date as ex_factory_date, d.shiping_mode, d.foc_or_claim, sum(e.production_qnty) as outputQty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, pro_ex_factory_mst d, pro_ex_factory_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and b.job_id=c.job_id and d.id=e.mst_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id
		
		 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $buyerCondJob $tlCondJob $shipmentDateCond GROUP BY a.quality_level, a.set_smv, a.job_no, b.id, c.order_quantity, c.country_ship_date, c.order_total, d.ex_factory_date, d.shiping_mode, d.foc_or_claim";
	}
	else
	{
		$shipsql="select a.quality_level, a.set_smv, a.job_no, b.id, b.pub_shipment_date as shipment_date, b.shiping_status, (b.po_quantity*a.total_set_qnty) as po_quantity, (b.po_total_price) as amount, c.ex_factory_date as ex_factory_date, c.shiping_mode, c.foc_or_claim, (c.ex_factory_qnty) as outputQty from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_name $buyerCondJob $tlCondJob $shipmentDateCond";
	}
	//echo $shipsql;
	$shipdatasql=sql_select($shipsql);
	$monthWiseShipArr=array(); $delayArr=array(); 

	foreach($shipdatasql as $srow)
	{
		$shipmonth=date("Y-m",strtotime($srow[csf('ex_factory_date')]));
		$exmonth=explode('-',$shipmonth);
		$newmonth=($exmonth[1]*1).'-'.$exmonth[0];
		$achivMint=0;
		$achivMint=$srow[csf('po_quantity')]*$srow[csf('set_smv')];
		//echo $newmonth.'-o<br>';
		$exAmt=($srow[csf('amount')]/$srow[csf('po_quantity')])*$srow[csf('outputQty')];
		$monthWiseShipArr[$newmonth]['qty']+=$srow[csf('outputQty')];
		$monthWiseShipArr[$newmonth]['amt']+=$exAmt;
		if($srow[csf('shiping_status')]!=3)
		{
			$delayArr[$newmonth]['id'][$srow[csf('id')]]=$srow[csf('id')];
			$monthWiseShipArr[$newmonth]['dpcs']+=$srow[csf('po_quantity')]-$srow[csf('outputQty')];
		}
		if($srow[csf('shiping_status')]==3)
		{
			if($srow[csf('po_quantity')]>$srow[csf('outputQty')])
				$monthWiseShipArr[$newmonth]['short']+=$srow[csf('po_quantity')]-$srow[csf('outputQty')];
			else if($srow[csf('po_quantity')]<$srow[csf('outputQty')])
				$monthWiseShipArr[$newmonth]['excess']+=$srow[csf('outputQty')]-$srow[csf('po_quantity')];
		}
		if($srow[csf('shiping_mode')]==2)
		{
			$monthWiseShipArr[$newmonth]['air']+=$srow[csf('outputQty')];
		}
		if($srow[csf('foc_or_claim')]==2)
		{
			$monthWiseShipArr[$newmonth]['claim']+=$srow[csf('outputQty')];
		}
	}
	unset($shipdatasql);

	$total_company=count($company_library);
	$colspan=$total_company+4;

	$tgtNatureArr=array(12,13,14);
	$tgtTypeArr=array(1=>"[PCS]",2=>"[Value]",3=>"[MNT]");
	$perTypeArr=array(1=>"Target",2=>"Achievment",3=>"No. of Styles/Job");
	
	$width=((340*count($tgtTypeArr)))+((480*count($tgtTypeArr))+80)+340+100+600;
	
	$tot_month = datediff( 'm', $txt_date_from,$txt_date_to);
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($txt_date_from,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
	}
	$yearArr=array();
	for($i=$preYear; $i<$searchYear; $i++ )
	{
		$yearArr[$i]=$i;
	}
	
	arsort($yearArr);
	//print_r($yearArr); die;
	?>
	 <fieldset style="width:<?=$width; ?>px; margin-top:10px;">
        	<table cellpadding="0" cellspacing="0" width="<?=$width; ?>">
                 <tr>
                   <td align="center" width="100%" colspan="<?=$colspan; ?>" class="form_caption"><strong><?=$report_title.' [Year-'.str_replace("'","",$cbo_year_selection).']'; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
                <thead>
                	<tr>
                        <th width="100" rowspan="2">Month</th>
                        <?
						foreach($perTypeArr as $perType=>$perTypeVal)
						{
							if($perType==1 || $perType==2)
							{
								foreach($tgtTypeArr as $tgtType=>$tgtTypeVal)
								{
									if($perType==1) $colspan=4; 
									else if($perType==2) 
									{
										if($tgtType==2) $colspan=7; else $colspan=6;
									}
									?><th colspan="<?=$colspan; ?>"><?=$perTypeVal.' '.$tgtTypeVal; ?></th><?
								}
							}
							else if($perType==3)
							{
								?><th colspan="4"><?=$perTypeVal; ?></th><?
							}
						}
						?>
						<th width="100" rowspan="2">Avg Qty Per Style</th>
                        <th colspan="8">Shipment Performance</th>
                    </tr>
                    <tr>
                    	<?
						foreach($perTypeArr as $perType=>$perTypeVal)
						{
							if($perType==1 || $perType==2)
							{
								foreach($tgtTypeArr as $tgtType=>$tgtTypeVal)
								{
									foreach($tgtNatureArr as $tnid)
									{
										?><th width="80"><?=$fbooking_order_nature[$tnid]; ?></th><?
									}
									?>
								<th width="100">Total</th>
								<?
									if($perType==2) 
									{
										?>
										<th width="80">Target Variation</th>
										<th width="80">Achived %</th>
										<?
										if($tgtType==2) 
										{
											?><th width="80">Avg. Price [PCS]</th><?
										}
									}
								} 
							}
							else if($perType==3)
							{
								foreach($tgtNatureArr as $tnid)
								{
									?><th width="80"><?=$fbooking_order_nature[$tnid]; ?></th><?
								}
								?><th width="100">Total</th><?
							}
						}?>	
						<th width="70">Qty[PCS]</th>
                        <th width="80">Value[$]</th>
                        <th width="70">Delay NO OF PO</th>
                        <th width="70">Delay PO Qty. [Pcs]</th>
                        <th width="60">Short</th>
                        <th width="60">Excess</th>
                        <th width="60">Air Ship Qty.</th>
                        <th>Claim</th>
                    </tr>
                </thead>
            <tbody>
            <? $i=1; $monthTotalArr=array(); $$totAvgQtyPerStyle=0;
			foreach($month_arr as $month_id)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>"> 
                    <td width="100" valign="middle">
                    <? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].', '.$y; ?>
                    </td>
                    <? 
                    $dateNatureNew=$m.'-'.$y;
					//echo $dateNatureNew.'-po<br>';
					
                    foreach($perTypeArr as $perType=>$perTypeVal)
                    {
						if($perType==1 || $perType==2)
						{
							foreach($tgtTypeArr as $tgtType=>$tgtTypeVal)
							{
								$rowsubtotal=$achivExVal=$achivExPer=$achivPricePcs=0;
								foreach($tgtNatureArr as $tnid)
								{
									$qtyValMin=0;
									if($perType==1)
									{
										$qtyValMin=$salesTgtDateArr[$tgtType][$dateNatureNew][$tnid];
									}
									else if($perType==2)
									{
										$qtyValMin=$achieveTgtDateArr[$tgtType][$dateNatureNew][$tnid];
										$achivPricePcs=array_sum($achieveTgtDateArr[2][$dateNatureNew])/array_sum($achieveTgtDateArr[1][$dateNatureNew]);
										
									}
									$achivExVal+=$salesTgtDateArr[$tgtType][$dateNatureNew][$tnid]-$achieveTgtDateArr[$tgtType][$dateNatureNew][$tnid];
									$achivExPer=array_sum($salesTgtDateArr[$tgtType][$dateNatureNew])/array_sum($achieveTgtDateArr[$tgtType][$dateNatureNew]);
									$rowsubtotal+=$qtyValMin;
									$monthTotalArr[$perType][$tgtType][$tnid]+=$qtyValMin;
									?><td width="80" align="right"><?=fn_number_format($qtyValMin); ?></td><?
								}
								?><td width="100" align="right"><?=fn_number_format($rowsubtotal); ?></td><?
								
								if($perType==2) 
								{	
									$tgtvariation=$achievePer=$avgPricePcs=0;
									$tgtvariation=$achivExVal;//$achivExValuArr[$dateNatureNew][$tgtType]['vari'];
									//echo "$tgtvariation";die();
									$achievePer=($achivExPer*100)*1;
									$avgPricePcs=$achivPricePcs;
									?>
									<td width="80" align="right"><?=fn_number_format($tgtvariation); ?></td>
									<td width="80" align="right"><?=fn_number_format($achievePer,2); ?></td>
									<?
									if($tgtType==2) 
									{
										?><td width="80" align="right"><?=fn_number_format($avgPricePcs,2); ?></td><?
									}
								}
							}
						}
						else if($perType==3)
						{
							$rowtotStyle=0;
							foreach($tgtNatureArr as $tnid)
							{
								//$rowsubtotal+=$qtyValMin;
								$noofjobstyle=0;
								$noofjobstyle=count($noOfJobArr[$dateNatureNew][$tnid]);
								
								$rowtotStyle+=$noofjobstyle;
								$monthTotalArr[$perType][0][$tnid]+=$noofjobstyle;
								?><td width="80" align="right"><?=$noofjobstyle; ?></td><?
							}
							?><td width="100" align="right"><?=fn_number_format($rowtotStyle); ?></td><?
						}
                    }
					$styleAvgQty=0;
					if(($rowtotStyle*1)>0)
					{
						$styleAvgQty=array_sum($achieveTgtDateArr[1][$dateNatureNew])/$rowtotStyle;
						$totAvgQtyPerStyle+=array_sum($achieveTgtDateArr[1][$dateNatureNew])/$rowtotStyle;
					}
					?>
					<td width="100" align="right"><?=fn_number_format($styleAvgQty); ?></td>
                    <td width="70" align="right"><?=fn_number_format($monthWiseShipArr[$dateNatureNew]['qty']); ?></td>
                    <td width="80" align="right"><?=fn_number_format($monthWiseShipArr[$dateNatureNew]['amt'],2); ?></td>
                    <td width="70" align="center"><?=count($delayArr[$dateNatureNew]['id']); ?></td>
                    <td width="70" align="right"><?=fn_number_format($monthWiseShipArr[$dateNatureNew]['dpcs']); ?></td>
                    <td width="60" align="right"><?=fn_number_format($monthWiseShipArr[$dateNatureNew]['short']); ?></td>
                    <td width="60" align="right"><?=fn_number_format($monthWiseShipArr[$dateNatureNew]['excess']); ?></td>
                    <td width="60" align="right"><?=fn_number_format($monthWiseShipArr[$dateNatureNew]['air']); ?></td>
                    <td align="right"><?=fn_number_format($monthWiseShipArr[$dateNatureNew]['claim']); ?></td>
                </tr>
				<?
                $i++;
				$totShipQty+=$monthWiseShipArr[$dateNatureNew]['qty'];
				$totShipVal+=$monthWiseShipArr[$dateNatureNew]['amt'];
				$totDelayCount+=count($delayArr[$dateNatureNew]['id']);
				$totDelayPcs+=$monthWiseShipArr[$dateNatureNew]['dpcs'];
				$totShort+=$monthWiseShipArr[$dateNatureNew]['short'];
				$totExcess+=$monthWiseShipArr[$dateNatureNew]['excess'];
				$totAir+=$monthWiseShipArr[$dateNatureNew]['air'];
				$totClaim+=$monthWiseShipArr[$dateNatureNew]['claim'];
			}
			//print_r($monthTotalArr); die;
			?>
            </tbody>
            <tfoot>
				<tr bgcolor="#AA9F55">
                    <td width="100" align="right"><b><?=$searchYear; ?> Total :</b></td>
                    <? 
                    $dateNatureNew=$m.'-'.$y;
					//echo $dateNatureNew.'-po<br>';
                    foreach($perTypeArr as $perType=>$perTypeVal)
                    {
						//$achivExVal=;
						if($perType==1 || $perType==2)
						{
							foreach($tgtTypeArr as $tgtType=>$tgtTypeVal)
							{
								foreach($tgtNatureArr as $tnid)
								{
									?>
									<td width="80" align="right"><?=fn_number_format($monthTotalArr[$perType][$tgtType][$tnid]); ?></td>
									<? 
								}
								?>
								<td width="100" align="right"><?=fn_number_format(array_sum($monthTotalArr[$perType][$tgtType])); ?></td>
								<?		
								if($perType==2) 
								{	
									$totAchivExVal=array_sum($monthTotalArr[1][$tgtType])-array_sum($monthTotalArr[2][$tgtType])*100;
									$totAchivExPer=array_sum($monthTotalArr[1][$tgtType])/array_sum($monthTotalArr[2][$tgtType])*100;
									?>
									<td width="80" align="right"><?=number_format($totAchivExVal); ?></td>
									<td width="80" align="right"><?=fn_number_format($totAchivExPer,2); ?></td>
									<?
									if($tgtType==2) 
									{
										?><td width="80" align="right">&nbsp;</td><?
									}
								}
							}
						}
						else if($perType==3)
						{
							$rowtotStyle=0;
							foreach($tgtNatureArr as $tnid)
							{
								//$rowsubtotal+=$qtyValMin;
								$totnoofjobstyle=0;
								$totnoofjobstyle=$monthTotalArr[$perType][0][$tnid];
								$rowtotnoofjobstyle+=$totnoofjobstyle;
								?><td width="80" align="right"><?=$totnoofjobstyle; ?></td><?
							}
							?><td width="100" align="right"><?=$rowtotnoofjobstyle; ?></td><?
						}
                    }
					?>

					<td width="100" align="right"><?=fn_number_format($totAvgQtyPerStyle); ?></td>
                    <td width="70" align="right"><?=fn_number_format($totShipQty); ?></td>
                    <td width="80" align="right"><?=fn_number_format($totShipVal,2); ?></td>
                    <td width="70" align="center"><?=$totDelayCount; ?></td>
                    <td width="70" align="right"><?=fn_number_format($totDelayPcs); ?></td>
                    <td width="60" align="right"><?=fn_number_format($totShort); ?></td>
                    <td width="60" align="right"><?=fn_number_format($totExcess); ?></td>
                    <td width="60" align="right"><?=fn_number_format($totAir); ?></td>
                    <td align="right"><?=fn_number_format($totClaim); ?></td>
                </tr>
                <tr bgcolor="#AA9F55">
                    <td width="100" align="right"><b><?=$searchYear; ?> Avg/Month :</b></td>
                    <? 
                    $dateNatureNew=$m.'-'.$y; $rowtotnoofjobstyle=0;
					//echo $dateNatureNew.'-po<br>';
                    foreach($perTypeArr as $perType=>$perTypeVal)
                    {
						if($perType==1 || $perType==2)
						{
							foreach($tgtTypeArr as $tgtType=>$tgtTypeVal)
							{
								foreach($tgtNatureArr as $tnid)
								{
									?><td width="80" align="right"><?=fn_number_format($monthTotalArr[$perType][$tgtType][$tnid]/12); ?></td><? 
								}
								?>
								<td width="100" align="right"><?=fn_number_format(array_sum($monthTotalArr[$perType][$tgtType])/12); ?></td>
								<?		
								if($perType==2) 
								{	
									$totAchivExVal=array_sum($monthTotalArr[1][$tgtType])-array_sum($monthTotalArr[2][$tgtType])*100;
									$totAchivExPer=array_sum($monthTotalArr[1][$tgtType])/array_sum($monthTotalArr[2][$tgtType])*100;
									?>
									<td width="80" align="right"><?=fn_number_format($totAchivExVal/12); ?></td>
									<td width="80" align="right"><?=fn_number_format($totAchivExPer/12,2); ?></td>
									<?
									if($tgtType==2) 
									{
										?><td width="80" align="right">&nbsp;</td><?
									}
								}
							}
						}
						else if($perType==3)
						{
							$rowtotStyle=0;
							foreach($tgtNatureArr as $tnid)
							{
								//$rowsubtotal+=$qtyValMin;
								$totnoofjobstyle=0;
								$totnoofjobstyle=$monthTotalArr[$perType][0][$tnid];
								$rowtotnoofjobstyle+=$totnoofjobstyle;
								?><td width="80" align="right"><?=fn_number_format($totnoofjobstyle/12); ?></td><?
							}
							?><td width="100" align="right"><?=fn_number_format($rowtotnoofjobstyle/12); ?></td><?
						}
                    }
					?>
					<td width="100" align="right"><?=fn_number_format($totAvgQtyPerStyle/12); ?></td>
                    <td width="70" align="right"><?=fn_number_format($totShipQty/12); ?></td>
                    <td width="80" align="right"><?=fn_number_format($totShipVal/12,2); ?></td>
                    <td width="70" align="center">&nbsp;</td>
                    <td width="70" align="right">&nbsp;</td>
                    <td width="60" align="right">&nbsp;</td>
                    <td width="60" align="right">&nbsp;</td>
                    <td width="60" align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                </tr>
                <? $a=1;
				foreach($yearArr as $year_val)
				{
					if($a==1) $bcolor="#FFDF00";
					else if($a==2) $bcolor="#AAFF00";
					else if($a==3) $bcolor="#55DF00";
					else if($a==4) $bcolor="#7FDF55";
					else if($a==5) $bcolor="#A0A0A4";
					$a++;
					?>
                    <tr bgcolor="<?=$bcolor; ?>">
                        <td width="100" align="center"><b><?=$year_val; ?> Total :</b></td>
                        <? 
                        $dateNatureNew=$m.'-'.$y;
                        //echo $dateNatureNew.'-po<br>';
                        foreach($perTypeArr as $perType=>$perTypeVal)
                        {
                            //$achivExVal=;
                            if($perType==1 || $perType==2)
                            {
                                foreach($tgtTypeArr as $tgtType=>$tgtTypeVal)
                                {
                                    foreach($tgtNatureArr as $tnid)
                                    {
										?><td width="80" align="right"><?=fn_number_format($yearSummDataArr[$perType][$tgtType][$year_val][$tnid]); ?></td><? 
                                    }
                                    ?>
                                    <td width="100" align="right"><?=fn_number_format(array_sum($yearSummDataArr[$perType][$tgtType][$year_val])); ?></td>
                                    <?		
									if($perType==2) 
									{	
										$totyearAchivExVal=$totyearAchivExPer=0;
										
										$totyearAchivExVal=array_sum($yearSummDataArr[1][$tgtType][$year_val])-array_sum($yearSummDataArr[2][$tgtType][$year_val]);
										$totyearAchivExPer=array_sum($yearSummDataArr[2][$tgtType][$year_val])/array_sum($yearSummDataArr[1][$tgtType][$year_val])*100;
										?>
										<td width="80" align="right"><?=number_format($totyearAchivExVal); ?></td>
										<td width="80" align="right"><?=fn_number_format($totyearAchivExPer,2); ?></td>
										<?
										if($tgtType==2) 
										{
											?><td width="80" align="right">&nbsp;</td><?
										}
									}
                                }
                            }
                            else if($perType==3)
                            {
                                $rowyeartotnoofjobstyle=0;
                                foreach($tgtNatureArr as $tnid)
                                {
                                    //$rowsubtotal+=$qtyValMin;
                                    $totyearnoofjobstyle=0;
                                    $totyearnoofjobstyle=count($yearSummJobArr[$year_val][$tnid]);
                                    $rowyeartotnoofjobstyle+=$totyearnoofjobstyle;
									?><td width="80" align="right"><?=$totyearnoofjobstyle; ?></td><?
                                }
                                ?><td width="100" align="right"><?=$rowyeartotnoofjobstyle; ?></td><?
                            }
                        }
                        ?>
    
                        <td width="100" align="right">&nbsp;</td>
                        <td width="70" align="right">&nbsp;</td>
                        <td width="80" align="right">&nbsp;</td>
                        <td width="70" align="center">&nbsp;</td>
                        <td width="70" align="right">&nbsp;</td>
                        <td width="60" align="right">&nbsp;</td>
                        <td width="60" align="right">&nbsp;</td>
                        <td width="60" align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr bgcolor="<?=$bcolor; ?>">
                        <td width="100" align="right"><b><?=$year_val; ?> Avg/Month :</b></td>
                        <? 
                        $dateNatureNew=$m.'-'.$y; $rowtotnoofjobstyle=0;
                        //echo $dateNatureNew.'-po<br>';
                        foreach($perTypeArr as $perType=>$perTypeVal)
                        {
                            if($perType==1 || $perType==2)
                            {
                                foreach($tgtTypeArr as $tgtType=>$tgtTypeVal)
                                {
                                    foreach($tgtNatureArr as $tnid)
                                    {
										?><td width="80" align="right"><?=fn_number_format($yearSummDataArr[$perType][$tgtType][$year_val][$tnid]/12); ?></td><? 
                                    }
                                    ?>
                                    <td width="100" align="right"><?=fn_number_format(array_sum($yearSummDataArr[$perType][$tgtType][$year_val])/12); ?></td>
                                    <?		
									if($perType==2) 
									{	
										$totyearAchivExVal=$totyearAchivExPer=0;
										$totAvgAchivExVal=array_sum($yearSummDataArr[1][$tgtType][$year_val])-array_sum($yearSummDataArr[2][$tgtType][$year_val]);
										$totAvgAchivExPer=array_sum($yearSummDataArr[2][$tgtType][$year_val])/array_sum($yearSummDataArr[1][$tgtType][$year_val])*100;
										?>
										<td width="80" align="right"><?=fn_number_format($totAvgAchivExVal/12); ?></td>
										<td width="80" align="right"><?=fn_number_format($totAvgAchivExPer/12,2); ?></td>
										<?
										if($tgtType==2) 
										{
											?><td width="80" align="right">&nbsp;</td><?
										}
									}
                                }
                            }
                            else if($perType==3)
                            {
                                $rowavgtotnoofjobstyle=0;
                                foreach($tgtNatureArr as $tnid)
                                {
                                    //$rowsubtotal+=$qtyValMin;
                                    $totavgnoofjobstyle=0;
                                    $totavgnoofjobstyle=count($yearSummJobArr[$year_val][$tnid]);
                                    $rowavgtotnoofjobstyle+=$totavgnoofjobstyle;
                                    ?><td width="80" align="right"><?=fn_number_format($totavgnoofjobstyle/12); ?></td><?
                                }
                                ?><td width="100" align="right"><?=fn_number_format($rowavgtotnoofjobstyle/12); ?></td><?
                            }
                        }
                        ?>
                        <td width="100" align="right">&nbsp;</td>
                        <td width="70" align="right">&nbsp;</td>
                        <td width="80" align="right">&nbsp;</td>
                        <td width="70" align="center">&nbsp;</td>
                        <td width="70" align="right">&nbsp;</td>
                        <td width="60" align="right">&nbsp;</td>
                        <td width="60" align="right">&nbsp;</td>
                        <td width="60" align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <?
                }
				?>
            </tfoot>
        </table>
    </fieldset>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	
	exit();	
}