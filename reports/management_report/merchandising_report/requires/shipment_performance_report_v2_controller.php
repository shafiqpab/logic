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
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "load_drop_down( 'requires/shipment_performance_report_v2_controller', this.value, 'load_drop_down_brand', 'brand_td')" );  	 
	exit();
}
if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 140, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0 order by id ASC", "id", "company_name");
$rpt_type_arr=array(1 => "Buyer Wise", 2 => "Brand Wise", 3 => "Team Wise", 4 => "Month Wise");
$rpt_value_typeArr=array(1 => "Qty", 2 => "Value", 3 => "Minute");

if($action=="report_generate")
{ 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_product_department=str_replace("'","",$cbo_product_department)==0?'%%':str_replace("'","",$cbo_product_department);
	
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
	
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$cbo_value_type=str_replace("'","",$cbo_value_type);
	$preYearHtml=str_replace("'","",$cbo_year_selection)-3;
	$preYear=str_replace("'","",$cbo_year_selection)-4;
	$searchYear=str_replace("'","",$cbo_year_selection);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	
	$hdcolorArr=array(1 => "#FFFF00", 2 => "#00FF00", 3 => "#00FFFF", 4=>"#7CFC00");
	
	$allYearArr=array(); $headcolorArr=array();
	$c=1;
	for ($i=$preYearHtml; $i<=$searchYear; $i++)
	{
		$allYearArr[$i]=$i;
		
		$headcolorArr[$i]=$hdcolorArr[$c];
		$c++;
	}
	//print_r($allYearArr); die;
	
	//$txt_date_from=str_replace("'","",$txt_date_from);
	//$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_date_from="01-Jan-".str_replace("'","",$preYear);
	$txt_date_to="31-Dec-".str_replace("'","",$cbo_year_selection);
	
	$targetyear_cond="to_char(b.sales_target_date,'YYYY')";
	$year_cond="to_char(b.pub_shipment_date,'YYYY')";
	$dateCondSalse=""; $dateCondJob="";
	//$dateCondSalse=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	//$dateCondJob=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	
	$dateCondSalse=" and $targetyear_cond between '".$preYear."' and '".str_replace("'","",$cbo_year_selection)."'";
	$dateCondJob=" and $year_cond between '".$preYear."' and '".str_replace("'","",$cbo_year_selection)."'";
	$shipmentDateCond=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	$financialParaDateCond=" and a.year between '".$preYear."' and '".str_replace("'","",$cbo_year_selection)."'";
	
	if($team_leader==0)
	{
		$tlCondSalse=""; $tlCondJob="";
	}
	else
	{
		$tlCondSalse=" and a.team_leader=$cbo_team_leader";
		$tlCondJob=" and a.team_leader=$cbo_team_leader";
	}
	if($cbo_brand_id==0)
	{
		$brandCondSalse=""; $brandCondJob="";
	}
	else
	{
		$brandCondSalse=" and a.BRAND_ID=$cbo_brand_id";
		$brandCondJob=" and a.team_leader=$cbo_brand_id";
	}
	
	//echo $date_cond.'='.$date_cond2; die;
	
	/*$sql="select min(c.working_day) as working_day, min(b.date_calc) as date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($company_name) and a.year=$year and  c.month_id=$monthid   ";
	 //echo $sql;
	$sql_data_calc=sql_select($sql);
	if(count($sql_data_calc)>0)
	{
		$working_day=$sql_data_calc[0][csf('working_day')];
		echo $working_day;
	}
	else
	{
	 	echo '';
	}*/
	
	$sql_sales=sql_select("select A.ID, A.COMPANY_ID, A.BUYER_ID, A.BRAND_ID, A.TEAM_LEADER from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id =$company_name $buyerCondSalse $brandCondSalse $tlCondSalse $dateCondSalse order by b.sales_target_date,a.company_id");
	//echo "select A.ID, A.COMPANY_ID, A.BUYER_ID, A.BRAND_ID, A.TEAM_LEADER from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id =$company_name $buyerCondSalse $brandCondSalse $tlCondSalse $dateCondSalse order by b.sales_target_date,a.company_id"; die;
	
	$sale_data_arr=array(); $buyerBrandTLArr=array();
	foreach($sql_sales as $row)
	{
		$buyerBrandTmLId=0;
		if($cbo_report_type==1)
		{
			$buyerBrandTmLId=$row["BUYER_ID"];
		}
		else if($cbo_report_type==2)
		{
			$buyerBrandTmLId=$row["BRAND_ID"];
		}
		else if($cbo_report_type==3)
		{
			$buyerBrandTmLId=$row["TEAM_LEADER"];
		}
		
		$buyerBrandTLArr[$buyerBrandTmLId]=$buyerBrandTmLId;
		
		$sale_data_arr[$row["ID"]]['buyer_brand_tl']=$buyerBrandTmLId;
		$mst_id_arr[$row["ID"]]=$row["ID"];
	}
	unset($sql_sales);

	$salesmstid_cond=where_con_using_array($mst_id_arr,0,"mst_id");
	$sql_nature="SELECT MST_ID, ID, TARGET_TYPE, NATURE_ID, TARGET_MONTH, TARGET_YEAR, TARGET_QTY FROM WO_SALES_TARGET_NATURE_DTLS WHERE NATURE_ID in (21,22,23,24) and status_active=1 and is_deleted=0 $salesmstid_cond";
	
	$res_nature=sql_select($sql_nature);  $salesTgtDataArr=array();
	foreach($res_nature as $row)
	{
		
		$dateNature="";
		$dateNature=$row['TARGET_MONTH'].'-'.$row['TARGET_YEAR']; 
		
		$reportDisplayType="";
		if($cbo_report_type==4)
		{
			$reportDisplayType="MY";
		}
		else
		{
			$reportDisplayType=$sale_data_arr[$row["MST_ID"]]['buyer_brand_tl'];
		}
		$salesTgtDataArr[$reportDisplayType]['1'][$dateNature][$row['TARGET_TYPE']][0]['tgtVal_min']+=$row['TARGET_QTY'];
		if($cbo_value_type==0) $row['TARGET_TYPE']=1;
		//if($row['TARGET_TYPE']==1)
			//echo $reportDisplayType.'--1--'.$dateNature.'--'.$row['TARGET_TYPE'].'--'.$row['NATURE_ID'].'tg<br>';
		$salesTgtDataArr[$reportDisplayType]['1'][$dateNature][$row['TARGET_TYPE']][$row['NATURE_ID']]['tgtQty_val_min']+=$row['TARGET_QTY'];
		
	}
	unset($res_nature);
	//echo "<pre>";
	//print_r($salesTgtDataArr["MY"][1]['1-2022']); die;
	
	//echo $cbo_date_type;
	$sql="select A.JOB_NO, A.BUYER_NAME, A.PRODUCT_GROUP, A.BRAND_ID, A.TEAM_LEADER, A.SET_SMV, C.ORDER_QUANTITY AS PO_QUANTITY, b.PO_RECEIVED_DATE AS SHIPMENT_DATE, C.ORDER_TOTAL AS AMOUNT from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.product_group in (21,22,23,24) and a.company_name=$company_name $buyerCondJob $tlCondJob $dateCondJob ";
	
	//echo $sql;
	$sql_res=sql_select($sql);
	
	$achieveDataArr=array();

	foreach($sql_res as $row)
	{
		$buyerBrandTmLId=0;
		if($cbo_report_type==1)
		{
			$buyerBrandTmLId=$row["BUYER_NAME"];
		}
		else if($cbo_report_type==2)
		{
			$buyerBrandTmLId=$row["BRAND_ID"];
		}
		else if($cbo_report_type==3)
		{
			$buyerBrandTmLId=$row["TEAM_LEADER"];
		}
		else if($cbo_report_type==4)
		{
			$reportDisplayType="MY";
		}
		$shipmonth=date("Y-m",strtotime($row['SHIPMENT_DATE']));
		
		$exmonth=explode('-',$shipmonth);
		$newmonth=($exmonth[1]*1).'-'.($exmonth[0]*1);
		//echo $newmonth.'<br>';
		$qty_val_min=0;
		if($cbo_value_type==1) $qty_val_min=$row['PO_QUANTITY'];
		else if($cbo_value_type==2) $qty_val_min=$row['AMOUNT'];
		else if($cbo_value_type==3) $qty_val_min=$row['PO_QUANTITY']*$row['SET_SMV'];
		
		$achivMint=0;
		$achivMint=$row[csf('po_quantity')]*$row[csf('set_smv')];
		if($cbo_value_type==0) $cbo_value_type=1;
		$achieveDataArr[$reportDisplayType]['2'][$newmonth][$cbo_value_type][$row['PRODUCT_GROUP']]['poQty_val_min']+=$qty_val_min;
		$achieveDataArr[$reportDisplayType]['2'][$newmonth][3][0]['poval']+=$row['AMOUNT'];
		$achieveDataArr[$reportDisplayType]['2'][$newmonth][2][0]['pomin']+=$row['PO_QUANTITY']*$row['SET_SMV'];
	}
	unset($sql_res);
	//echo "<pre>";
	//print_r($yearSummDataArr[2]);die;
	//echo $sql;
	
	
	
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

	$tgtNatureArr=array(21,22,23,24);
	$tgtTypeArr=array(1=>"Qty",2=>"SMV [Minute]",3=>"Value");
	$yearWiseHeadArr=array(1=>"Target",2=>"Achieved",3=>"Achvd %",4=>"Index % [Growth]");
	
	$comcolspna=4*4*7;
	$width=(4*7*4*70)+400;
	ob_start();
	//print_r($yearArr); die;
	?>
	<fieldset style="width:<?=$width; ?>px; margin-top:10px;">
        <table cellpadding="0" cellspacing="0" width="<?=$width; ?>">
        	<tr>
            	<td align="center" width="100%" colspan="<?=$comcolspna; ?>" class="form_caption"><strong><?=$company_library[$company_name]; ?></strong></td>
            </tr>
            <tr>
                <td align="center" width="100%" colspan="<?=$comcolspna; ?>" class="form_caption"><strong><?=$report_title.' [Year-'.str_replace("'","",$cbo_year_selection).'], '.$rpt_type_arr[$cbo_report_type].' ['.$rpt_value_typeArr[$cbo_value_type].']'; ?></strong></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width; ?>" class="rpt_table" >
            <thead>
                <tr>
                    <th width="100" rowspan="4">Month</th>
                    <th width="100" rowspan="4">Working Day</th>
                    <?
                    foreach($allYearArr as $yearWise)
                    {
                        ?><th colspan="28" style="background:<?=$headcolorArr[$yearWise]; ?>"><?=$yearWise.' Business Status'; ?></th><?
                    }
                    ?>
                    <th width="100" rowspan="4">Avg Qty Per Style</th>
                    <th rowspan="4">Shipment Performance</th>
                </tr>
                <tr>
                    <?
                    foreach($allYearArr as $yearWise)
                    {
                        foreach($yearWiseHeadArr as $yaerhdid=>$yaerhdval)
                        {
                            ?><th colspan="7" style="background:<?=$headcolorArr[$yearWise]; ?>"><?=$yaerhdval; ?></th><?
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    foreach($allYearArr as $yearWise)
                    {
                        foreach($yearWiseHeadArr as $yaerhdid=>$yaerhdval)
                        {
                            $i=1;
                            foreach($tgtTypeArr as $tgttypeid=>$tgttypeval)
                            {
                                if($i==1)
                                {
                                    ?><th colspan="5"><?=$tgttypeval; ?></th><?
                                }
                                else
                                {
                                    ?><th rowspan="2" width="70"><?=$tgttypeval; ?></th><?
                                }
                                $i++;
                            }
                        }
                    } 
                    ?>
                </tr>
                <tr>
                    <?
                    foreach($allYearArr as $yearWise)
                    {
                        foreach($yearWiseHeadArr as $yaerhdid=>$yaerhdval)
                        {
                            $i=1;
                            foreach($tgtTypeArr as $tgttypeid=>$tgttypeval)
                            {
                                if($i==1)
                                {
                                    foreach($tgtNatureArr as $natureid)
                                    {
                                        ?><th width="70" ><?=$fbooking_order_nature[$natureid]; ?></th><?
                                    }
                                    ?><th width="70">Total</th><?
                                }
                                $i++;
                            }
                        }
                    }
                    ?>
                    
                </tr>
            </thead>
        	<tbody>
            <? $i=1; $monthTotalArr=array(); $$totAvgQtyPerStyle=0;
			foreach($months as $month_id=>$monthval)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>"> 
                    <td width="100" valign="middle"><?=$monthval; ?></td>
                    <td width="100">??</td>
                    <? $count=0;
					$reportDisplayType="";
					if($cbo_report_type==4)
					{
						$reportDisplayType="MY";
					}
                    foreach($allYearArr as $yearWise)
					{
						$dateNature=""; $dateNatureGrouth="";
						$dateNature=$month_id.'-'.$yearWise;
						$dateNatureGrouth=$month_id.'-'.($yearWise-1);
						$tgtQtyPer=0; $achiQtyPer=0;
						foreach($yearWiseHeadArr as $yaerhdid=>$yaerhdval)
						{	$c=1; 
							foreach($tgtTypeArr as $tgttypeid=>$tgttypeval)
							{
								if($c==1)
								{
									$subRowTotal=0;
									foreach($tgtNatureArr as $natureid)
									{
										if($cbo_value_type==0) $cbo_value_type=1;
										if($yaerhdid==1)
										{
											$naturetgtQty_val_min=0;
											//echo $reportDisplayType.'--'.$yaerhdid.'--'.$dateNature.'--'.$cbo_value_type.'--'.$natureid.'<br>';
											$naturetgtQty_val_min=$salesTgtDataArr[$reportDisplayType][$yaerhdid][$dateNature][$cbo_value_type][$natureid]['tgtQty_val_min'];
											$subRowTotal+=$naturetgtQty_val_min;
											$tgtQtyPer+=$naturetgtQty_val_min;
											?><td width="70" align="right"><?=$naturetgtQty_val_min; ?></td><?
										}
										else if ($yaerhdid==2)
										{
											$naturePoQty_val_min=0;
											$naturePoQty_val_min=$achieveDataArr[$reportDisplayType][$yaerhdid][$dateNature][$cbo_value_type][$natureid]['poQty_val_min'];
											$subRowTotal+=$naturePoQty_val_min;
											$achiQtyPer+=$naturePoQty_val_min;
											?><td width="70" align="right"><?=$naturePoQty_val_min; ?></td><?
										}
										else if ($yaerhdid==3)
										{
											$achiveQty_val_minPer=0;
											if($achieveDataArr[$reportDisplayType][2][$dateNature][$cbo_value_type][$natureid]['poQty_val_min']>0 && $salesTgtDataArr[$reportDisplayType][1][$dateNature][$cbo_value_type][$natureid]['tgtQty_val_min']>0)
											{
											$achiveQty_val_minPer=($achieveDataArr[$reportDisplayType][2][$dateNature][$cbo_value_type][$natureid]['poQty_val_min']/$salesTgtDataArr[$reportDisplayType][1][$dateNature][$cbo_value_type][$natureid]['tgtQty_val_min'])*100;
											}
											//$subRowTotal+=$naturePoQty_val_min;
											?><td width="70" align="right"><?=fn_number_format($achiveQty_val_minPer,2); ?></td><?
										}
										else if ($yaerhdid==4)
										{
											$grouthQty_val_minPer=0;
											if($salesTgtDataArr[$reportDisplayType][1][$dateNatureGrouth][$cbo_value_type][$natureid]['tgtQty_val_min']>0 && $salesTgtDataArr[$reportDisplayType][1][$dateNature][$cbo_value_type][$natureid]['tgtQty_val_min']>0)
											{
											$grouthQty_val_minPer=($salesTgtDataArr[$reportDisplayType][1][$dateNatureGrouth][$cbo_value_type][$natureid]['tgtQty_val_min']/$salesTgtDataArr[$reportDisplayType][1][$dateNature][$cbo_value_type][$natureid]['tgtQty_val_min'])*100;
											}
											//$subRowTotal+=$naturePoQty_val_min;
											?><td width="70" align="right"><?=fn_number_format($grouthQty_val_minPer,2); ?></td><?
										}
										else
										{
											?><td width="70" align="right">&nbsp;</td><?
										}
									}
									$rowSmv=$rowVal=0;
									if($yaerhdid==1)
									{
									$rowSmv=$salesTgtDataArr[$reportDisplayType][$yaerhdid][$dateNature][2][0]['tgtVal_min'];
									$rowVal=$salesTgtDataArr[$reportDisplayType][$yaerhdid][$dateNature][3][0]['tgtVal_min'];
									}
									else if ($yaerhdid==2)
									{
										$rowSmv=$achieveDataArr[$reportDisplayType][$yaerhdid][$dateNature][2][0]['pomin'];
										$rowVal=$achieveDataArr[$reportDisplayType][$yaerhdid][$dateNature][3][0]['poval'];
									}
									else if ($yaerhdid==3)
									{
										$subRowTotal=$rowSmv=$rowVal=0;
										$subRowTotal=fn_number_format(($achiQtyPer/$tgtQtyPer)*100,2);
										//echo $achiQtyPer.'/'.$tgtQtyPer.'<br>';
										$rowSmv=fn_number_format(($achieveDataArr[$reportDisplayType][2][$dateNature][2][0]['pomin']/$salesTgtDataArr[$reportDisplayType][1][$dateNature][2][0]['tgtVal_min'])*100,2);
										$rowVal=fn_number_format(($achieveDataArr[$reportDisplayType][2][$dateNature][3][0]['poval']/$salesTgtDataArr[$reportDisplayType][1][$dateNature][3][0]['tgtVal_min'])*100,2);
									}
									//if()
									?>
                                    <td width="70" align="right"><?=$subRowTotal; ?></td>
                                    <td width="70" align="right"><?=$rowSmv; ?></td>
									<td width="70" align="right"><?=$rowVal; ?></td>
									<?
								}
								$c++;
							}
						}
					}
				?>
                	<td width="100"><?=$count; ?></td>
                    <td>Ship Perf</td>
				</tr>
				<?
				$i++;
			}
			//print_r($monthTotalArr); die;
			die;
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