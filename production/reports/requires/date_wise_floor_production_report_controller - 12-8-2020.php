<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.commisions.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 0, "-- Select --", $selected, "",0 );   exit();   	 
}

if ($action=="load_drop_down_floor")
{ 
	echo create_drop_down( "cbo_floor", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id in($data) and production_process=5 order by floor_name","id,floor_name", 0, "", $selected, "",0 ); 
	exit();    	 
}



 
if($action=="report_generate")
{ 	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//====================== load library ======================== 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
 	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	
	
	//echo $txt_date;cbo_floor
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$prod_date = $txt_date;
	

	$sql_cond = "";
	$sql_cond .= ($cbo_company_name=="") ? "" : " and d.serving_company in($cbo_company_name)";
	$sql_cond .= ($cbo_location=="") ? "" : " and d.location in($cbo_location)";
	$sql_cond .= ($cbo_floor=="") ? "" : " and d.floor_id in($cbo_floor)";
	if(str_replace("'", "", $txt_date_from) !="" && str_replace("'", "", $txt_date_to) !="")
	{
		$sql_cond.=" and d.production_date between $txt_date_from and $txt_date_to";
	}
	// echo $sql_cond;die(); 	
	$sql= "SELECT a.JOB_NO,d.serving_company,d.production_date,d.floor_id,c.po_break_down_id,
	sum(e.production_qnty) as PRODUCTION_QTY ,
	sum(c.ORDER_RATE*e.production_qnty) as FOB_VALUE,
	min(b.PO_QUANTITY) as PO_QUANTITY
	from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 $sql_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.produced_by=1 
	
	group by d.serving_company,a.JOB_NO,c.po_break_down_id,d.floor_id,d.production_date order by d.floor_id,d.production_date";
    //echo $sql;die();//and a.JOB_NO='TIL-20-00004' 
	$sql_res = sql_select($sql);
	if(count($sql_res)==0)
	{
		?>
			<div style="color: red;font-size: 16px;font-weight: bold;text-align: center;">Data not found!</div>
		<?
		die();
	}
	
	
 	foreach ($sql_res as $val) 
	{
		$jobArr[$val[JOB_NO]]=$val[JOB_NO];
	}
 
 
	
			$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){
					$job_con =" and (a.job_no in('".implode("','",$job_no_process)."')";
					$job_con2 =" and (b.JOB_NO_MST in('".implode("','",$job_no_process)."')";
				} 
				else{
					$job_con .=" or a.job_no in('".implode("','",$job_no_process)."')";
					$job_con2 .=" or b.JOB_NO_MST in('".implode("','",$job_no_process)."')";
				}
				$p++;
			}
			$job_con .=")";
			$job_con2 .=")";
	
			
$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set,b.incometax_cost,b.interest_cost from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 $job_con";
			 //echo $sql_pre; 
			$pre_result=sql_select($sql_pre);
			foreach($pre_result as $row)
			{
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				
				$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost']=$row[csf("incometax_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['interest_cost']=$row[csf("interest_cost")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
				
			}
			
		unset($pre_result);			
			
		$sqlOrder="select a.TOTAL_SET_QNTY,b.JOB_NO_MST,sum(b.ORDER_TOTAL) as FOV_VAL,sum(b.ORDER_QUANTITY) as ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,wo_po_color_size_breakdown b where a.JOB_NO=b.JOB_NO_MST $job_con2 group by a.TOTAL_SET_QNTY,b.JOB_NO_MST";
		$sqlOrder_result=sql_select($sqlOrder);
			foreach($sqlOrder_result as $val)
			{
				//$jobData[$row[JOB_NO_MST]][FOV_VAL]=$row[FOV_VAL];	
				//$jobData[$row[JOB_NO_MST]][ORDER_QUANTITY]=$row[ORDER_QUANTITY];
				
				$jobDataArr[PO_QUANTITY][$val[JOB_NO_MST]]=$val[ORDER_QUANTITY];
				$jobDataArr[FOB_VALUE][$val[JOB_NO_MST]]+=$val[FOV_VAL];
				$jobDataArr[TOTAL_SET_QNTY][$val[JOB_NO_MST]]=$val[TOTAL_SET_QNTY];
				
			}			
	//echo $sqlOrder;die;
	
	//class................................................start;
	 $all_jobs=implode("','",$jobArr);
		
	 $condition= new condition();
	 $condition->company_name("in($cbo_company_name)");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
			
	if($db_type==0 || $db_type==2)
	 {
		 if(str_replace("'","",$all_jobs)!='')
		{
			$condition->job_no("in('".$all_jobs."')");
		}
	}
	
	
	$condition->init();
	$other= new other($condition);
	 //echo $other->getQuery(); die;
	$fabric= new fabric($condition);
	$yarn= new yarn($condition);
	$conversion= new conversion($condition);
	$trim= new trims($condition);
	$emblishment= new emblishment($condition);
	$wash= new wash($condition);
	$commercial= new commercial($condition);
	$commision= new commision($condition);
	
	

	$other_costing_arr=$other->getAmountArray_by_job();
	$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
	$conversionCostArr=$conversion->getAmountArray_by_job();
	$trimsCostArr=$trim->getAmountArray_by_job();
	$emblishmentCostArr=$emblishment->getAmountArray_by_job();
	$washCostArr=$wash->getAmountArray_by_job();
	$commercialCostArr=$commercial->getAmountArray_by_job();
	$commisionCostArr=$commision->getAmountArray_by_job();
		
//class ................................................end;
 
 
 
 	
 	foreach ($sql_res as $val) 
	{
		
	
		//from class ....................start;	
			
			$po_cm_cost=$other_costing_arr[$val[JOB_NO]]['cm_cost'];
			
			$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$val[JOB_NO]]);
			$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$val[JOB_NO]]);
			$yarn_cost_amount=$yarnCostArr[$val[JOB_NO]];
			//*$po_wise_export_arr[$val[JOB_NO]]['total_set_qnty']
			$conversion_cost_amount=array_sum($conversionCostArr[$val[JOB_NO]]);
			$trims_cost_amount=$trimsCostArr[$val[JOB_NO]];
			$emblishment_cost_amount=$emblishmentCostArr[$val[JOB_NO]];
			$wash_cost_amount=$washCostArr[$val[JOB_NO]];
			$commercial_cost_amount=$commercialCostArr[$val[JOB_NO]];
			$commision_cost_amount=$commisionCostArr[$val[JOB_NO]];
			
			$po_cm_cost_val=$other_costing_arr[$val[JOB_NO]]['cm_cost'];
			$po_lab_test_val=$other_costing_arr[$val[JOB_NO]]['lab_test'];
			$po_inspection_val=$other_costing_arr[$val[JOB_NO]]['inspection'];
			$po_currier_cost_val=$other_costing_arr[$val[JOB_NO]]['currier_pre_cost'];
			$po_design_cost_val=$other_costing_arr[$val[JOB_NO]]['design_cost'];
			$po_studio_cost_val=$other_costing_arr[$val[JOB_NO]]['studio_cost'];
			
			$po_freight_val=$other_costing_arr[$val[JOB_NO]]['freight'];
			$po_common_oh_val=$other_costing_arr[$val[JOB_NO]]['common_oh'];
			$po_depr_amor_pre_cost_val=$other_costing_arr[$val[JOB_NO]]['depr_amor_pre_cost'];
			$po_certificate_pre_cost_val=$other_costing_arr[$val[JOB_NO]]['certificate_pre_cost'];
			
			$deffdlc_cost=$other_costing_arr[$val[JOB_NO]]['deffdlc_cost'];
			$incometax_cost=$job_wise_export_arr[$val[JOB_NO]]['incometax_cost'];	
			$interest_cost=$job_wise_export_arr[$val[JOB_NO]]['interest_cost'];
			
			
			$totalCost=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);
			
			$qnty_unit_price_value_fob=$job_wise_export_arr[$val[JOB_NO]]['job_quantity']*$job_wise_export_arr[$val[JOB_NO]]['avg_unit_price'];
			
			
			$cm_mergin_pcs=($jobDataArr[FOB_VALUE][$val[JOB_NO]]-$totalCost)/$jobDataArr[PO_QUANTITY][$val[JOB_NO]];
			$cm_cost=$job_wise_export_arr[$val[JOB_NO]]['cm_cost'];
			//$cm_cost=$other_costing_arr[$val[JOB_NO]]['cm_cost'];
			

			//echo array_sum($jobDataArr[FOB_VALUE][$val[JOB_NO]]).'-'.$totalCost.')/'.array_sum($jobDataArr[PO_QUANTITY][$val[JOB_NO]]);;
			
			//from class ....................end;	
		
		$costing_per=$job_wise_export_arr[$val[JOB_NO]]['costing_per'];
		if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
		else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
		else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
		else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
		else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
		
		$cm_cost_pcs=($cm_cost/$order_price_per_dzn)/$jobDataArr[TOTAL_SET_QNTY][$val[JOB_NO]];
		
		
		
		
		$floorIdArr[$val[csf('serving_company')]][$val[csf('floor_id')]]=$floor_library[$val[csf('floor_id')]];
		
		$dataArray[$val[csf('production_date')]][$val[csf('serving_company')]][$val[csf('floor_id')]][PRODUCTION_QTY] +=$val[PRODUCTION_QTY];
		$dataArray[$val[csf('production_date')]][$val[csf('serving_company')]][$val[csf('floor_id')]][FOB_VALUE] +=$val[FOB_VALUE];
		
		
		$dataArray[$val[csf('production_date')]][$val[csf('serving_company')]][$val[csf('floor_id')]][CM_VALUE] +=($val[PRODUCTION_QTY]*$cm_cost_pcs);
		$dataArray[$val[csf('production_date')]][$val[csf('serving_company')]][$val[csf('floor_id')]][MARGIN_VALUE] +=($val[PRODUCTION_QTY]*$cm_mergin_pcs);
		
		
		
		$dataArray[$val[csf('production_date')]][$val[csf('serving_company')]][$val[csf('floor_id')]][JOB_NO][$val[JOB_NO]] =$val[JOB_NO];
		
	}
	
	//echo $sssss;
	//print_r($dataArray);die;
	
	
	$width=600;
	foreach($floorIdArr as $company_id=>$dataRows){
		$width+=count($dataRows)*5*90;	
	}
	
	
	ob_start();
	?>
    <div style="width:<?= $width+20;?>px; margin: 0 auto"> 
        <div>
            <table width="<?= $width;?>" cellspacing="0" border="1" align="left" class="rpt_table" rules="all" id="table_header_1">
                <thead> 	 	 	 	 	 	
                    <tr>
                        <th rowspan="3" width="35">SL</th>
                        <th rowspan="3">Date</th>
						<? foreach($floorIdArr as $company_id=>$dataRows){
							echo "<th colspan='".(count($dataRows)*5)."' width='90'>$company_library[$company_id]</th>";} 
						?> 
                        <th rowspan="2" colspan='5'>Day Total</th> 
                    </tr>
                    
                    <tr>
                        <? 
                        foreach($floorIdArr as $company_id=>$dataRows){
                            foreach($dataRows as $floor_id=>$floor_name){
								echo "<th colspan='5'>$floor_name</th>";
							} 
						} 
						?>  
                     </tr>
                    <tr>
						<? 
                        foreach($floorIdArr as $company_id=>$dataRows){
                            foreach($dataRows as $floor_id=>$floor_name){
								echo "<th width='90'>Production Qty</th>";
								echo "<th width='90'>Production FOB</th>";
								echo "<th width='90'>CM Value</th>";
								echo "<th width='90'>Margin Value</th>";
								echo "<th width='90'>CM With Margin</th>";
							} 
						}
						?>
                        <th width='90'>Prod.Qty</th> 
                        <th width='90'>Prod. FOB</th> 
                        <th width='90'>CM Value</th> 
                        <th width='90'>Margin Value</th> 
                        <th width='90'>CM With Margin</th> 
                    </tr>
                </thead>
            </table>
            <div style="max-height:400px; overflow-y:auto; width:<?= $width+20;?>px" id="scroll_body">
                <table border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table"  width="<?= $width;?>" rules="all" id="table_body">
                	<tbody>
                        
                        <?
						$total_pro_data_arr=array();
						$i=1;
						foreach($dataArray as $productionDate=>$companyDataArr){
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35" align="center"><?= $i;?></td>
                            <td align="center"><?= change_date_format($productionDate);?></td>
							<? 
							foreach($floorIdArr as $company_id=>$dataRows){
								foreach($dataRows as $floor_id=>$floor_name){
									?>
									
                                    <td align='right' width='90'><a href="javascript:open_popup('order_qty_dtls',900,<?= $company_id;?>,'<?= $productionDate;?>',<?= $floor_id;?>);"><?= $companyDataArr[$company_id][$floor_id][PRODUCTION_QTY]; ?></a></td>
                                    <td align='right' width='90'><a href="javascript:open_popup('fob_dtls',960,<?= $company_id;?>,'<?= $productionDate;?>',<?= $floor_id;?>);"><?= number_format($companyDataArr[$company_id][$floor_id][FOB_VALUE],2); ?></a></td>
                                    <td align='right' width='90' title="<?= "CM PCS : ".$cm_cost/$order_price_per_dzn;?>"><a href="javascript:open_popup('cm_val_dtls',1140,<?= $company_id;?>,'<?= $productionDate;?>',<?= $floor_id;?>);"><?= number_format($companyDataArr[$company_id][$floor_id][CM_VALUE],2); ?></a></td>
                                    <td align='right' width='90'><a href="javascript:open_popup('margin_val_dtls',1140,<?= $company_id;?>,'<?= $productionDate;?>',<?= $floor_id;?>);"><?= number_format($companyDataArr[$company_id][$floor_id][MARGIN_VALUE],2); ?></a></td>
                                    <td align='right' width='90'><?= number_format($companyDataArr[$company_id][$floor_id][CM_VALUE]+$companyDataArr[$company_id][$floor_id][MARGIN_VALUE],2); ?></td>
                                <?
                                
									//Total data arr...............................................
									$total_pro_data_arr[qty][$company_id][$floor_id]+=$companyDataArr[$company_id][$floor_id][PRODUCTION_QTY];
									$total_pro_data_arr[fob][$company_id][$floor_id]+=$companyDataArr[$company_id][$floor_id][FOB_VALUE];
									$total_pro_data_arr[cm_val][$company_id][$floor_id]+=$companyDataArr[$company_id][$floor_id][CM_VALUE];
									$total_pro_data_arr[margin_val][$company_id][$floor_id]+=$companyDataArr[$company_id][$floor_id][MARGIN_VALUE];
								
								
								
									//Day Total data arr...............................................
									$total_pro_data_arr[qty][$productionDate]+=$companyDataArr[$company_id][$floor_id][PRODUCTION_QTY];
									$total_pro_data_arr[fob][$productionDate]+=$companyDataArr[$company_id][$floor_id][FOB_VALUE];
									$total_pro_data_arr[cm_val][$productionDate]+=$companyDataArr[$company_id][$floor_id][CM_VALUE];
									$total_pro_data_arr[margin_val][$productionDate]+=$companyDataArr[$company_id][$floor_id][MARGIN_VALUE];
								
								
								
								//Floor Summary data arr...............................................
									$summary_data_arr[$floor_id][qty]+=$companyDataArr[$company_id][$floor_id][PRODUCTION_QTY];
									$summary_data_arr[$floor_id][fob]+=$companyDataArr[$company_id][$floor_id][FOB_VALUE];
									$summary_data_arr[$floor_id][cm_val]+=$companyDataArr[$company_id][$floor_id][CM_VALUE];
									$summary_data_arr[$floor_id][margin_val]+=$companyDataArr[$company_id][$floor_id][MARGIN_VALUE];
								
								} 
                            } 
							$i++;
							
                            ?> 
                            <td align='right' width='90'><?= $total_pro_data_arr[qty][$productionDate];?></td> 
                            <td align='right' width='90'><?= number_format($total_pro_data_arr[fob][$productionDate],2);?></td> 
                            <td align='right' width='90'><?= number_format($total_pro_data_arr[cm_val][$productionDate],2);?></td> 
                            <td align='right' width='90'><?= number_format($total_pro_data_arr[margin_val][$productionDate],2);?></td> 
                            <td align='right' width='90'><?= number_format($total_pro_data_arr[cm_val][$productionDate]+$total_pro_data_arr[margin_val][$productionDate],2);?></td> 
 
                        </tr>
                        <? } ?>
                	</tbody>
               	</table>
               	<table width="<?= $width;?>" cellspacing="0" border="1" align="left" class="rpt_table gd-color3" rules="all" id="table_header_1">
                    <tfoot>
                        <th width="35"></th>
                        <th></th>
                        <? 
                        foreach($floorIdArr as $company_id=>$dataRows){
                            foreach($dataRows as $floor_id=>$floor_name){
                                echo "<th width='90'>".$total_pro_data_arr[qty][$company_id][$floor_id]."</th>";
                                echo "<th width='90'>".number_format($total_pro_data_arr[fob][$company_id][$floor_id],2)."</th>";
                                echo "<th width='90'>".number_format($total_pro_data_arr[cm_val][$company_id][$floor_id],2)."</th>";
                                echo "<th width='90'>".number_format($total_pro_data_arr[margin_val][$company_id][$floor_id],2)."</th>";
                                echo "<th width='90'>".number_format($total_pro_data_arr[cm_val][$company_id][$floor_id]+$total_pro_data_arr[margin_val][$company_id][$floor_id],2)."</th>";
                            
                            } 
                        } 
                        $i++;
                        ?>
                            <th width='90'><?= array_sum($total_pro_data_arr[qty]);?></th> 
                            <th width='90'><?= number_format(array_sum($total_pro_data_arr[fob]),2);?></th> 
                            <th width='90'><?= number_format(array_sum($total_pro_data_arr[cm_val]),2);?></th> 
                            <th width='90'><?= number_format(array_sum($total_pro_data_arr[margin_val]),2);?></th> 
                            <th width='90'><?= number_format(array_sum($total_pro_data_arr[cm_val])+array_sum($total_pro_data_arr[margin_val]),2);?></th> 
                    </tfoot>
                </table>	
            </div>    
        </div>
        <br />
    </div><!-- end main div -->
         
	<?
	$dtlsData=ob_get_contents();
	ob_clean();
	
	ob_start();
	?>
    <div style="overflow:hidden; margin: 0 auto; width:600px;">
    <table border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table" rules="all" id="table_body">
    	<thead>
        	<tr>
                <th colspan="7">Floor Summary</th>
            </tr>
        	<tr>
                <th width="35">SL</th>
                <th width="110">Floor</th>
                <th width="90">Prod.Qty</th>
                <th width="90">Prod. FOB</th>
                <th width="90">CM Value</th>
                <th width="90">Margin Value</th>
                <th width="90">Total CM With Margin</th>
            </tr>
        </thead>
        <? 
		$i=1;
		foreach($summary_data_arr as $floor_id=>$row){
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$summaryTotal[qty]+=$row[qty];
			$summaryTotal[fob]+=$row[fob];
			$summaryTotal[cm_val]+=$row[cm_val];
			$summaryTotal[margin_val]+=$row[margin_val];
			$summaryTotal[cm_margin_val]+=($row[cm_val]+$row[margin_val]);
		
		?>
        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
        	<td align="center"><?= $i;?></td>
        	<td><?= $floor_library[$floor_id];?></td>
        	<td align="right"><?= $row[qty];?></td>
        	<td align="right"><?= number_format($row[fob],2);?></td>
        	<td align="right"><?= number_format($row[cm_val],2);?></td>
        	<td align="right"><?= number_format($row[margin_val],2);?></td>
        	<td align="right"><?= number_format($row[cm_val]+$row[margin_val],2);?></td>
        </tr>
        <? 
		$i++;
		} 
		?>
        <tfoot>
        	<th colspan="2"></th>
        	<th align="right"><?= $summaryTotal[qty];?></th>
        	<th align="right"><?= number_format($summaryTotal[fob],2);?></th>
        	<th align="right"><?= number_format($summaryTotal[cm_val],2);?></th>
        	<th align="right"><?= number_format($summaryTotal[margin_val],2);?></th>
        	<th align="right"><?= number_format($summaryTotal[cm_margin_val],2);?></th>
        </tfoot>
        
    </table>
    </div>
    <?
	$summaryData=ob_get_contents();
	ob_clean();
	
	

	$thml=$summaryData."<br><br>".$dtlsData;
		
	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,$thml);
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo $thml."####".$name;
	exit();
}


if($action=='order_qty_dtls'){
	echo load_html_head_contents("Order Qty Dtls", "../../../", 1, 1,'','','');
	extract($_REQUEST);
 	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
 	$buyer_lib=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 


	$where_cond = " and d.serving_company =$company_id";
	$where_cond.=" and d.production_date between '$pro_date' and '$pro_date'";
	$where_cond.=" and d.FLOOR_ID = $floor_id";
	
$sql= "SELECT d.FLOOR_ID,d.SEWING_LINE,a.JOB_NO,c.PO_BREAK_DOWN_ID,a.BUYER_NAME,a.STYLE_REF_NO,b.PO_NUMBER,d.ITEM_NUMBER_ID,
	sum(e.production_qnty) as PRODUCTION_QTY ,
	min(b.PO_QUANTITY) as PO_QUANTITY
	from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1  and d.produced_by=1 $where_cond
	group by d.FLOOR_ID,d.SEWING_LINE,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_NO,d.ITEM_NUMBER_ID,b.PO_NUMBER,c.PO_BREAK_DOWN_ID order by d.floor_id,d.SEWING_LINE";
    //echo $sql;
	$sql_res = sql_select($sql);
	
 	$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and status_active=1 and is_deleted=0");
	$resource_alocate_line=return_library_array( "select id, line_number from prod_resource_mst", "id", "line_number"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	foreach ($sql_res as $val) 
	{
		
		
		if($prod_reso_allocation==1)
		{
			$sewing_line=$resource_alocate_line[$val[SEWING_LINE]];
			$sewing_line_arr=explode(",",$sewing_line);
			$sewing_line_name=array();
			foreach($sewing_line_arr as $line_id)
			{
				$sewing_line_name[$line_id]=$line_library[$line_id];
			}
			$sewing_line_name=implode(",",$sewing_line_name);
			 $val[SEWING_LINE]=$sewing_line_name;
		}
		else
		{
			 $val[SEWING_LINE]=$line_library[$val[SEWING_LINE]];
		}		
		
		
		
		$key=$val[FLOOR_ID].$val[SEWING_LINE].$val[JOB_NO].$val[BUYER_NAME].$val[STYLE_REF_NO].$val[ITEM_NUMBER_ID].$val[PO_BREAK_DOWN_ID];
		$orderDataArr[$key]=array(
			FLOOR_ID=>$val[FLOOR_ID],
			SEWING_LINE=>$val[SEWING_LINE],
			JOB_NO=>$val[JOB_NO],
			PO_QUANTITY=>$val[PO_QUANTITY],
			PRODUCTION_QTY=>$val[PRODUCTION_QTY],
			PO_NUMBER=>$val[PO_NUMBER],
			BUYER_NAME=>$val[BUYER_NAME],
			STYLE_REF_NO=>$val[STYLE_REF_NO],
			ITEM_NUMBER_ID=>$val[ITEM_NUMBER_ID],
		);
		$floor_marge[$val[FLOOR_ID].$val[SEWING_LINE]]+=1;
		$floor_marge_data[$val[FLOOR_ID].$val[SEWING_LINE]]+=$val[PRODUCTION_QTY];
		
				
	}	
	$name=$user_id."_order_popup.xls";
	
	?>
    <script>
	
    function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;} </style><body>'+document.getElementById('report_container3').innerHTML+'</body</html>'); 
        d.close();
    }
	</script>    
     

    <a href="<?= $name;?>" style="text-decoration:none">
    <input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>
    </a>
    <input type="button" onclick="new_window()" value="HTML" name="Print" class="formbutton" style="width:100px"/>
    
    <? ob_start();?>
    <div id="report_container3" style="overflow:hidden; margin: 0 auto;">
    <table width="100%" border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table" rules="all" id="table_body">
		<thead>
			<th>Floor</th>
			<th>Line No</th>
			<th>Buyer</th>
			<th>Style Ref.</th>
			<th>Order No</th>
			<th>Job No</th>
			<th>Garments Item</th>
			<th>Order Qnty</th>
			<th>Production Qty </th>
            <th>Line total</th>
        </thead>
        <tbody>
        <? 
		$total_pro_qty=0;$line_pro_qty=0;
		foreach($orderDataArr as $rows){
		?>
		<tr>
			<td><?= $floor_lib[$rows[FLOOR_ID]];?></td>
			<td><?= $rows[SEWING_LINE];?></td>
			<td><?= $buyer_lib[$rows[BUYER_NAME]];?></td>
			<td><?= $rows[STYLE_REF_NO];?></td>
			<td><?= $rows[PO_NUMBER];?></td>
			<td><?= $rows[JOB_NO];?></td>
			<td><?= $garments_item[$rows[ITEM_NUMBER_ID]];?></td>
			<td align="right"><?= $rows[PO_QUANTITY];?></td>
			<td align="right"><?= $rows[PRODUCTION_QTY];?></td>
			<? if($floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]>0){ ?>
            <td valign="middle" align="center" rowspan="<?= $floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]];?>" >
				<?
					echo $floor_marge_data[$rows[FLOOR_ID].$rows[SEWING_LINE]];
					$floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]=0;
				?>
            </td>
            <? } ?>
            
        </tr>
        <?
		$total_pro_qty+=$rows[PRODUCTION_QTY];
		}
		?>
        </tbody>
		<tfoot>
			<th></th>
			<th> </th>
			<th></th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th><?= $total_pro_qty;?></th>
			<th></th>
        </tfoot>
    </table>
    
    
    </div>
            

<?	
	$html=ob_get_contents();
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	
	exit(); 	
}


if($action=='fob_dtls'){
	echo load_html_head_contents("Order Qty Dtls", "../../../", 1, 1,'','','');
	extract($_REQUEST);
 	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
 	$buyer_lib=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 


	$where_cond = " and d.serving_company =$company_id";
	$where_cond.=" and d.production_date between '$pro_date' and '$pro_date'";
	$where_cond.=" and d.FLOOR_ID = $floor_id";
	
$sql= "SELECT d.FLOOR_ID,d.SEWING_LINE,a.JOB_NO,c.PO_BREAK_DOWN_ID,a.BUYER_NAME,a.STYLE_REF_NO,b.PO_NUMBER,d.ITEM_NUMBER_ID,

	sum(c.ORDER_RATE*e.production_qnty) as FOB_VALUE,

	sum(e.production_qnty) as PRODUCTION_QTY ,
	min(b.PO_QUANTITY) as PO_QUANTITY
	from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1  and d.produced_by=1 $where_cond
	group by d.FLOOR_ID,d.SEWING_LINE,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_NO,d.ITEM_NUMBER_ID,b.PO_NUMBER,c.PO_BREAK_DOWN_ID order by d.floor_id,d.SEWING_LINE";
   //echo $sql;
 	$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and status_active=1 and is_deleted=0");
	$resource_alocate_line=return_library_array( "select id, line_number from prod_resource_mst", "id", "line_number"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	
	
	$sql_res = sql_select($sql);
	
 	foreach ($sql_res as $val) 
	{
		
		if($prod_reso_allocation==1)
		{
			$sewing_line=$resource_alocate_line[$val[SEWING_LINE]];
			$sewing_line_arr=explode(",",$sewing_line);
			$sewing_line_name=array();
			foreach($sewing_line_arr as $line_id)
			{
				$sewing_line_name[$line_id]=$line_library[$line_id];
			}
			$sewing_line_name=implode(",",$sewing_line_name);
			 $val[SEWING_LINE]=$sewing_line_name;
		}
		else
		{
			 $val[SEWING_LINE]=$line_library[$val[SEWING_LINE]];
		}		
		
		
		
		$key=$val[FLOOR_ID].$val[SEWING_LINE].$val[JOB_NO].$val[BUYER_NAME].$val[STYLE_REF_NO].$val[ITEM_NUMBER_ID].$val[PO_BREAK_DOWN_ID];
		$orderDataArr[$key]=array(
			FLOOR_ID=>$val[FLOOR_ID],
			SEWING_LINE=>$val[SEWING_LINE],
			JOB_NO=>$val[JOB_NO],
			PO_QUANTITY=>$val[PO_QUANTITY],
			PRODUCTION_QTY=>$val[PRODUCTION_QTY],
			PO_NUMBER=>$val[PO_NUMBER],
			BUYER_NAME=>$val[BUYER_NAME],
			STYLE_REF_NO=>$val[STYLE_REF_NO],
			ITEM_NUMBER_ID=>$val[ITEM_NUMBER_ID],
			FOB_VALUE=>$val[FOB_VALUE],
		);
		$floor_marge[$val[FLOOR_ID].$val[SEWING_LINE]]+=1;
		$floor_marge_data[$val[FLOOR_ID].$val[SEWING_LINE]]+=$val[FOB_VALUE];
		
				
	}	
	
	
	$name=$user_id."_fob_dtls.xls";
	
	?>
    <script>
	
	function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;} </style><body>'+document.getElementById('report_container3').innerHTML+'</body</html>'); 
        d.close();
    }
	</script>    
    
    <a href="<?= $name;?>" style="text-decoration:none">
    <input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>
    </a>
    <input type="button" onclick="new_window()" value="HTML" name="Print" class="formbutton" style="width:100px"/>
    
    <? ob_start();?>
    <div style="overflow:hidden; margin: 0 auto;" id="report_container3">
    <table width="100%" border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table" rules="all" id="table_body">
		<thead>
			<th>Floor</th>
			<th>Line No</th>
			<th>Buyer</th>
			<th>Style Ref.</th>
			<th>Order No</th>
			<th>Job No</th>
			<th>Garments Item</th>
			<th>Order Qnty</th>
            <th>FOB Price/Pcs</th>
			<th>Production Qty </th>
			<th>Production FOB Value</th>
            <th>Line total</th>
        </thead>
        <tbody>
        <? 
		$total_pro_qty=0;
		$i=1;
		foreach($orderDataArr as $rows){ 
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td><?= $floor_lib[$rows[FLOOR_ID]];?></td>
			<td><?= $rows[SEWING_LINE];?></td>
			<td><?= $buyer_lib[$rows[BUYER_NAME]];?></td>
			<td><?= $rows[STYLE_REF_NO];?></td>
			<td><?= $rows[PO_NUMBER];?></td>
			<td><?= $rows[JOB_NO];?></td>
			<td><?= $garments_item[$rows[ITEM_NUMBER_ID]];?></td>
			<td align="right"><?= $rows[PO_QUANTITY];?></td>
			<td align="right"><?= number_format($fob_price_pcs=$rows[FOB_VALUE]/$rows[PRODUCTION_QTY],2);?></td>
			<td align="right"><?= $rows[PRODUCTION_QTY];?></td>
            <td align="right"><?= number_format($production_fob_val=$rows[FOB_VALUE],2);?></td>
			<? if($floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]>0){ ?>
            <td valign="middle" align="center" rowspan="<?= $floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]];?>" >
				<?
					echo $floor_marge_data[$rows[FLOOR_ID].$rows[SEWING_LINE]];
					$floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]=0;
				?>
            </td>
            <? } ?>
        </tr>
        <?
		$total_pro_qty+=$rows[PRODUCTION_QTY];
		$total_pro_fob_val+=$production_fob_val;
		}
		?>
        </tbody>
		<tfoot>
			<th></th>
			<th> </th>
			<th></th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th><?= $total_pro_qty;?></th>
			<th><?= number_format($total_pro_fob_val,2);?> </th>
			<th> </th>
        </tfoot>
    </table>
    
    
    </div>
            

<?	
	$html=ob_get_contents();
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	exit(); 	
}


if($action=='cm_val_dtls'){
	echo load_html_head_contents("Order Qty Dtls", "../../../", 1, 1,'','','');
	
	
	extract($_REQUEST);
 	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
 	$buyer_lib=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 


	$where_cond = " and d.serving_company =$company_id";
	$where_cond.=" and d.production_date between '$pro_date' and '$pro_date'";
	$where_cond.=" and d.FLOOR_ID = $floor_id";
	
$sql= "SELECT  a.TOTAL_SET_QNTY,d.FLOOR_ID,d.SEWING_LINE,a.JOB_NO,c.PO_BREAK_DOWN_ID,a.BUYER_NAME,a.STYLE_REF_NO,b.PO_NUMBER,d.ITEM_NUMBER_ID,

	sum(c.ORDER_RATE*e.production_qnty) as FOB_VALUE,

	sum(e.production_qnty) as PRODUCTION_QTY ,
	sum(e.production_qnty) as PRODUCTION_QTY,
	min(b.PO_QUANTITY) as PO_QUANTITY
	from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1  and d.produced_by=1 $where_cond
	group by a.TOTAL_SET_QNTY,d.FLOOR_ID,d.SEWING_LINE,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_NO,d.ITEM_NUMBER_ID,b.PO_NUMBER,c.PO_BREAK_DOWN_ID order by d.floor_id,d.SEWING_LINE";
    
	$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and status_active=1 and is_deleted=0");
	$resource_alocate_line=return_library_array( "select id, line_number from prod_resource_mst", "id", "line_number"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );

	$sql_res = sql_select($sql);
	$jobArr=array();
 	foreach ($sql_res as $val) 
	{
		
    	if($prod_reso_allocation==1)
		{
			$sewing_line=$resource_alocate_line[$val[SEWING_LINE]];
			$sewing_line_arr=explode(",",$sewing_line);
			$sewing_line_name=array();
			foreach($sewing_line_arr as $line_id)
			{
				$sewing_line_name[$line_id]=$line_library[$line_id];
			}
			$sewing_line_name=implode(",",$sewing_line_name);
			 $val[SEWING_LINE]=$sewing_line_name;
		}
		else
		{
			 $val[SEWING_LINE]=$line_library[$val[SEWING_LINE]];
		}		
		
		
		$key=$val[FLOOR_ID].$val[SEWING_LINE].$val[JOB_NO].$val[BUYER_NAME].$val[STYLE_REF_NO].$val[ITEM_NUMBER_ID].$val[PO_BREAK_DOWN_ID];
		$orderDataArr[$key]=array(
			FLOOR_ID=>$val[FLOOR_ID],
			SEWING_LINE=>$val[SEWING_LINE],
			JOB_NO=>$val[JOB_NO],
			PO_QUANTITY=>$val[PO_QUANTITY],
			PRODUCTION_QTY=>$val[PRODUCTION_QTY],
			PO_NUMBER=>$val[PO_NUMBER],
			BUYER_NAME=>$val[BUYER_NAME],
			STYLE_REF_NO=>$val[STYLE_REF_NO],
			ITEM_NUMBER_ID=>$val[ITEM_NUMBER_ID],
			FOB_VALUE=>$val[FOB_VALUE],
		);
		$jobArr[$val[JOB_NO]]=$val[JOB_NO];
		$jobDataArr[PO_QUANTITY][$val[JOB_NO]][$val[PO_BREAK_DOWN_ID]]=$val[PO_QUANTITY];
		$jobDataArr[FOB_VALUE][$val[JOB_NO]][$val[PO_BREAK_DOWN_ID]]=$val[FOB_VALUE];
		
		$jobDataArr[TOTAL_SET_QNTY][$val[JOB_NO]]=$val[TOTAL_SET_QNTY];
		
		$floor_marge[$val[FLOOR_ID].$val[SEWING_LINE]]+=1;
		$floor_marge_data[$val[FLOOR_ID].$val[SEWING_LINE]][$val[JOB_NO]]+=$val[PRODUCTION_QTY];
		
			
				
	}	
	
	$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){
					$job_con =" and (a.job_no in('".implode("','",$job_no_process)."')";
					$job_con2 =" and (b.JOB_NO_MST in('".implode("','",$job_no_process)."')";
				} 
				else{
					$job_con .=" or a.job_no in('".implode("','",$job_no_process)."')";
					$job_con2 .=" or b.JOB_NO_MST in('".implode("','",$job_no_process)."')";
				}
				
				$p++;
			}
			$job_con .=")";
			$job_con2 .=")";
	
			
			
$sql_pre = "select a.COSTING_DATE,a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set,b.incometax_cost,b.interest_cost from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 $job_con";
			//echo $sql_pre; 
			$pre_result=sql_select($sql_pre);
			$job_wise_export_arr=array();
			foreach($pre_result as $row)
			{
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				
				$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost']=$row[csf("incometax_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['interest_cost']=$row[csf("interest_cost")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['COSTING_DATE']=$row[csf("COSTING_DATE")];	
			}
			
		unset($pre_result);	
		
		
		//........................................
		$sqlOrder="select a.TOTAL_SET_QNTY,b.JOB_NO_MST,a.SET_SMV,a.GMTS_ITEM_ID,sum(b.ORDER_TOTAL) as FOV_VAL,sum(b.ORDER_QUANTITY) as ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,wo_po_color_size_breakdown b where a.JOB_NO=b.JOB_NO_MST $job_con2 group by a.TOTAL_SET_QNTY,a.SET_SMV,a.GMTS_ITEM_ID,b.JOB_NO_MST";
		$sqlOrder_result=sql_select($sqlOrder);
			foreach($sqlOrder_result as $row)
			{
				$jobData[$row[JOB_NO_MST]][FOV_VAL]+=$row[FOV_VAL];	
				$jobData[$row[JOB_NO_MST]][ORDER_QUANTITY]+=$row[ORDER_QUANTITY];
				$jobData[$row[JOB_NO_MST]][TOTAL_SET_QNTY]+=$row[TOTAL_SET_QNTY];
				$jobData[$row[JOB_NO_MST]][TOTAL_SET_QNTY]+=$row[TOTAL_SET_QNTY];
				
				$itemSMVData[$row[GMTS_ITEM_ID]]=$row[SET_SMV];
			}
	
		
		//class................................................start;
		 $all_jobs=implode("','",$jobArr);
			
		 $condition= new condition();
		 $condition->company_name("=$company_id");
		 if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("=$cbo_buyer_name");
		 }
				
		if($db_type==0 || $db_type==2)
		 {
			 if(str_replace("'","",$all_jobs)!='')
			{
				$condition->job_no("in('".$all_jobs."')");
			}
		}
		
		
		$condition->init();
		$other= new other($condition);
		//echo $other->getQuery(); die;
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		
		
	
		$other_costing_arr=$other->getAmountArray_by_job();
		$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
		$conversionCostArr=$conversion->getAmountArray_by_job();
		$trimsCostArr=$trim->getAmountArray_by_job();
		$emblishmentCostArr=$emblishment->getAmountArray_by_job();
		$washCostArr=$wash->getAmountArray_by_job();
		$commercialCostArr=$commercial->getAmountArray_by_job();
		$commisionCostArr=$commision->getAmountArray_by_job();
			
	//class ................................................end;
	$name=$user_id."_cm_val_dtls.xls";
	?>
    
    <script>
	
	
 	function generate_report(type,job_no,company_name,buyer_name,style_ref,costing_date,po_breack_down_id,costing_per)
	{
		var rate_amt=2; var zero_val='';
		if(type!='mo_sheet')
		{
			var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		}
		var excess_per_val="";
		if(type=='mo_sheet')
		{
			excess_per_val = prompt("Please enter your Excess %", "0");
			if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
		}

		if (r==true) zero_val="1"; else zero_val="0";
	
		var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&txt_job_no='"+job_no+"'&cbo_company_name="+company_name+"&cbo_buyer_name="+buyer_name+"&txt_style_ref='"+style_ref+"'&txt_costing_date='"+costing_date+"'&txt_po_breack_down_id=''&cbo_costing_per="+costing_per+"&print_option_id=''";
		show_msg('3');
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
		
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			//show_msg('3');
			release_freezing();
		}
	}
    
	
	
	
	function new_window()
    {
		var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;} </style><body>'+document.getElementById('report_container3').innerHTML+'</body</html>'); 
        d.close();
    }
	</script> 
    
    <div style="display:none;" id="data_panel"></div>   
    
    <a href="<?= $name;?>" style="text-decoration:none">
    <input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>
    </a>
    <input type="button" onclick="new_window()" value="HTML" name="Print" class="formbutton" style="width:100px"/>
    
    <? ob_start();?>
    <div style="overflow:hidden; margin: 0 auto;" id="report_container3">
    <table width="100%" border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table" rules="all" id="table_body">
		<thead>
			<th>Floor</th>
			<th>Line No</th>
			<th>Buyer</th>
			<th>Style Ref.</th>
			<th>Order No</th>
			<th>Job No</th>
			<th>Garments Item</th>
			<th>Order Qnty</th>
            <th>SMV</th>
            <th>CM/Dzn</th>
            <th>Margin /Dzn</th>
            <th>CM/Pcs</th>
			<th>Production Qty </th>
			<th>Production CM Value</th>
            <th>Line total</th>
        </thead>
        <tbody>
        <? 
		$total_pro_qty=0;
		$i=1;
		foreach($orderDataArr as $rows){ 
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		
		
		
//from class ....................start;	
			
			$po_cm_cost=$other_costing_arr[$rows[JOB_NO]]['cm_cost'];
			
			$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$rows[JOB_NO]]);
			$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$rows[JOB_NO]]);
			$yarn_cost_amount=$yarnCostArr[$rows[JOB_NO]];
			//*$po_wise_export_arr[$rows[JOB_NO]]['total_set_qnty']
			$conversion_cost_amount=array_sum($conversionCostArr[$rows[JOB_NO]]);
			$trims_cost_amount=$trimsCostArr[$rows[JOB_NO]];
			$emblishment_cost_amount=$emblishmentCostArr[$rows[JOB_NO]];
			$wash_cost_amount=$washCostArr[$rows[JOB_NO]];
			$commercial_cost_amount=$commercialCostArr[$rows[JOB_NO]];
			$commision_cost_amount=$commisionCostArr[$rows[JOB_NO]];
			
			$po_cm_cost_val=$other_costing_arr[$rows[JOB_NO]]['cm_cost'];
			$po_lab_test_val=$other_costing_arr[$rows[JOB_NO]]['lab_test'];
			$po_inspection_val=$other_costing_arr[$rows[JOB_NO]]['inspection'];
			$po_currier_cost_val=$other_costing_arr[$rows[JOB_NO]]['currier_pre_cost'];
			$po_design_cost_val=$other_costing_arr[$rows[JOB_NO]]['design_cost'];
			$po_studio_cost_val=$other_costing_arr[$rows[JOB_NO]]['studio_cost'];
			
			$po_freight_val=$other_costing_arr[$rows[JOB_NO]]['freight'];
			$po_common_oh_val=$other_costing_arr[$rows[JOB_NO]]['common_oh'];
			$po_depr_amor_pre_cost_val=$other_costing_arr[$rows[JOB_NO]]['depr_amor_pre_cost'];
			$po_certificate_pre_cost_val=$other_costing_arr[$rows[JOB_NO]]['certificate_pre_cost'];
			
			$deffdlc_cost=$other_costing_arr[$rows[JOB_NO]]['deffdlc_cost'];
			$incometax_cost=$job_wise_export_arr[$rows[JOB_NO]]['incometax_cost'];	
			$interest_cost=$job_wise_export_arr[$rows[JOB_NO]]['interest_cost'];
			
			
			$totalCost=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);
			
			$qnty_unit_price_value_fob=$job_wise_export_arr[$rows[JOB_NO]]['job_quantity']*$job_wise_export_arr[$rows[JOB_NO]]['avg_unit_price'];
			
			
			//$cm_mergin_pcs=(array_sum($jobDataArr[FOB_VALUE][$rows[JOB_NO]])-$totalCost)/array_sum($jobDataArr[PO_QUANTITY][$rows[JOB_NO]]);
			$cm_cost=$job_wise_export_arr[$rows[JOB_NO]]['cm_cost']/$jobDataArr[TOTAL_SET_QNTY][$rows[JOB_NO]];
			
			
			$cm_mergin_pcs=($jobData[$rows[JOB_NO]][FOV_VAL]-$totalCost)/$jobData[$rows[JOB_NO]][ORDER_QUANTITY];
			
			//from class ....................end;	
		
		
		
	
	//echo array_sum($jobDataArr[FOB_VALUE][$rows[JOB_NO]]).'-'.$totalCost.')/'.array_sum($jobDataArr[PO_QUANTITY][$rows[JOB_NO]]);
		$costing_per=$job_wise_export_arr[$rows[JOB_NO]]['costing_per'];
		if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
		else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
		else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
		else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
		else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
		
		$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
		
		
		
		//------------------------
		$line_pro_cm_value=0;
		foreach($floor_marge_data[$rows[FLOOR_ID].$rows[SEWING_LINE]] as $job=>$pro_value){
			
			$costing_per=$job_wise_export_arr[$job]['costing_per'];
			if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			
			$cmCost=$job_wise_export_arr[$job]['cm_cost']/$jobDataArr[TOTAL_SET_QNTY][$job];
			$cmCostPcs=$cmCost/$order_price_per_dzn;
			
			$line_pro_cm_value+=$cmCostPcs*$pro_value;
		}
		
		 //echo $cm_cost_pcs;
		
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td><?= $floor_lib[$rows[FLOOR_ID]];?></td>
			<td><?= $rows[SEWING_LINE];?></td>
			<td><?= $buyer_lib[$rows[BUYER_NAME]];?></td>
			<td><?= $rows[STYLE_REF_NO];?></td>
			<td><?= $rows[PO_NUMBER];?></td>
			<td><a href="javascript:generate_report('bomRpt2','<?= $rows[JOB_NO];?>',<?= $company_id;?>,'<?= $rows[BUYER_NAME];?>','<?= $rows[STYLE_REF_NO];?>','<?= $job_wise_export_arr[$rows[JOB_NO]]['COSTING_DATE'];?>','',<?= $costing_per;?>);"><?= $rows[JOB_NO];?></a></td>
			<td><?= $garments_item[$rows[ITEM_NUMBER_ID]];?></td>
			<td align="right"><?= $rows[PO_QUANTITY];?></td>
            
			<td align="center"><?= $itemSMVData[$rows[ITEM_NUMBER_ID]];?></td>
			<td align="center"><?= $cm_cost_pcs*12 ;?></td>
			<td align="center"><?= number_format($cm_mergin_pcs*12,2);?></td>
            
			<td align="right"><?= number_format($cm_cost_pcs,2);?></td>
			<td align="right"><?= $rows[PRODUCTION_QTY];?></td>
            <td align="right"><?= number_format($production_cm_val=$cm_cost_pcs*$rows[PRODUCTION_QTY],2);?></td>
			<? if($floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]>0){ ?>
            <td valign="middle" align="center" rowspan="<?= $floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]];?>" >
				<?
					echo number_format($line_pro_cm_value,2);
					$floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]=0;
				?>
            </td>
            <? } ?>
        </tr>
        <?
		$total_pro_qty+=$rows[PRODUCTION_QTY];
		$total_pro_cm_val+=$production_cm_val;
		}
		?>
        </tbody>
		<tfoot>
			<th></th>
			<th> </th>
			<th></th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th><?= $total_pro_qty;?></th>
			<th><?= number_format($total_pro_cm_val,2);?> </th>
			<th> </th>
        </tfoot>
    </table>
    
    
    </div>
            

<?	
	$html=ob_get_contents();
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	exit(); 	
}


if($action=='margin_val_dtls'){
	echo load_html_head_contents("Order Qty Dtls", "../../../", 1, 1,'','','');
	extract($_REQUEST);
 	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
 	$buyer_lib=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 


	$where_cond = " and d.serving_company =$company_id";
	$where_cond.=" and d.production_date between '$pro_date' and '$pro_date'";
	$where_cond.=" and d.FLOOR_ID = $floor_id";
	
$sql= "SELECT d.FLOOR_ID,d.SEWING_LINE,a.JOB_NO,c.PO_BREAK_DOWN_ID,a.BUYER_NAME,a.STYLE_REF_NO,b.PO_NUMBER,d.ITEM_NUMBER_ID,

	sum(e.production_qnty) as PRODUCTION_QTY ,
	sum(e.production_qnty) as PRODUCTION_QTY,
	min(b.PO_QUANTITY) as PO_QUANTITY
	from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1  and d.produced_by=1 $where_cond
	group by d.FLOOR_ID,d.SEWING_LINE,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_NO,d.ITEM_NUMBER_ID,b.PO_NUMBER,c.PO_BREAK_DOWN_ID order by d.floor_id,d.SEWING_LINE";
    $prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and status_active=1 and is_deleted=0");
	$resource_alocate_line=return_library_array( "select id, line_number from prod_resource_mst", "id", "line_number"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
    

	$sql_res = sql_select($sql);
	$jobArr=array();
 	foreach ($sql_res as $val) 
	{
		
    	if($prod_reso_allocation==1)
		{
			$sewing_line=$resource_alocate_line[$val[SEWING_LINE]];
			$sewing_line_arr=explode(",",$sewing_line);
			$sewing_line_name=array();
			foreach($sewing_line_arr as $line_id)
			{
				$sewing_line_name[$line_id]=$line_library[$line_id];
			}
			$sewing_line_name=implode(",",$sewing_line_name);
			 $val[SEWING_LINE]=$sewing_line_name;
		}
		else
		{
			 $val[SEWING_LINE]=$line_library[$val[SEWING_LINE]];
		}		
		
		
		$key=$val[FLOOR_ID].$val[SEWING_LINE].$val[JOB_NO].$val[BUYER_NAME].$val[STYLE_REF_NO].$val[ITEM_NUMBER_ID].$val[PO_BREAK_DOWN_ID];
		$orderDataArr[$key]=array(
			FLOOR_ID=>$val[FLOOR_ID],
			SEWING_LINE=>$val[SEWING_LINE],
			JOB_NO=>$val[JOB_NO],
			PO_QUANTITY=>$val[PO_QUANTITY],
			PRODUCTION_QTY=>$val[PRODUCTION_QTY],
			PO_NUMBER=>$val[PO_NUMBER],
			BUYER_NAME=>$val[BUYER_NAME],
			STYLE_REF_NO=>$val[STYLE_REF_NO],
			ITEM_NUMBER_ID=>$val[ITEM_NUMBER_ID],
			FOB_VALUE=>$val[FOB_VALUE],
		);
		$jobArr[$val[JOB_NO]]=$val[JOB_NO];
		$jobDataArr[PO_QUANTITY][$val[JOB_NO]][$val[PO_BREAK_DOWN_ID]]=$val[PO_QUANTITY];
		//$jobDataArr[FOB_VALUE][$val[JOB_NO]][$val[PO_BREAK_DOWN_ID]]=$val[FOB_VALUE];
		$floor_marge[$val[FLOOR_ID].$val[SEWING_LINE]]+=1;
		$floor_marge_data[$val[FLOOR_ID].$val[SEWING_LINE]][$val[JOB_NO]]+=$val[PRODUCTION_QTY];
			
				
	}	
	
	
	$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){
					$job_con =" and (a.job_no in('".implode("','",$job_no_process)."')";
					$job_con2 =" and (b.JOB_NO_MST in('".implode("','",$job_no_process)."')";
				} 
				else{
					$job_con .=" or a.job_no in('".implode("','",$job_no_process)."')";
					$job_con2 .=" or b.JOB_NO_MST in('".implode("','",$job_no_process)."')";
				}
				$p++;
			}
			$job_con .=")";
			$job_con2 .=")";
	
			
			
$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set,b.incometax_cost,b.interest_cost from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 $job_con";
			//echo $sql_pre; 
			$pre_result=sql_select($sql_pre);
			$job_wise_export_arr=array();
			foreach($pre_result as $row)
			{
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				
				$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost']=$row[csf("incometax_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['interest_cost']=$row[csf("interest_cost")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
				
			}
			
		unset($pre_result);	
		
			//print_r($job_wise_export_arr);die;	

		

		//$sqlOrder="select a.TOTAL_SET_QNTY,b.JOB_NO_MST,sum(b.ORDER_TOTAL) as FOV_VAL,sum(b.ORDER_QUANTITY) as ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,wo_po_color_size_breakdown b where a.JOB_NO=b.JOB_NO_MST $job_con2 group by a.TOTAL_SET_QNTY,b.JOB_NO_MST";
		$sqlOrder="select a.TOTAL_SET_QNTY,b.JOB_NO_MST,a.SET_SMV,a.GMTS_ITEM_ID,sum(b.ORDER_TOTAL) as FOV_VAL,sum(b.ORDER_QUANTITY) as ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,wo_po_color_size_breakdown b where a.JOB_NO=b.JOB_NO_MST $job_con2 group by a.TOTAL_SET_QNTY,a.SET_SMV,a.GMTS_ITEM_ID,b.JOB_NO_MST";

		$sqlOrder_result=sql_select($sqlOrder);
			foreach($sqlOrder_result as $row)
			{
				$jobData[$row[JOB_NO_MST]][FOV_VAL]+=$row[FOV_VAL];	
				$jobData[$row[JOB_NO_MST]][ORDER_QUANTITY]+=$row[ORDER_QUANTITY];
				$jobData[$row[JOB_NO_MST]][TOTAL_SET_QNTY]+=$row[TOTAL_SET_QNTY];
				
				$itemSMVData[$row[GMTS_ITEM_ID]]=$row[SET_SMV];
			}
			
		
			
					
		//print_r($jobData);die;
		
		//class................................................start;
		 $all_jobs=implode("','",$jobArr);
			
		 $condition= new condition();
		 $condition->company_name("=$company_id");
		 if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("=$cbo_buyer_name");
		 }
				
		if($db_type==0 || $db_type==2)
		 {
			 if(str_replace("'","",$all_jobs)!='')
			{
				$condition->job_no("in('".$all_jobs."')");
			}
		}
		
		
		$condition->init();
		$other= new other($condition);
		//echo $other->getQuery(); die;
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);
		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		
		
	
		$other_costing_arr=$other->getAmountArray_by_job();
		$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
		$conversionCostArr=$conversion->getAmountArray_by_job();
		$trimsCostArr=$trim->getAmountArray_by_job();
		$emblishmentCostArr=$emblishment->getAmountArray_by_job();
		$washCostArr=$wash->getAmountArray_by_job();
		$commercialCostArr=$commercial->getAmountArray_by_job();
		$commisionCostArr=$commision->getAmountArray_by_job();
			
	//class ................................................end;
	$name=$user_id."_margin_val_dtls.xls";
	?>
    
    <script>
	
	
 	function generate_report(type,job_no,company_name,buyer_name,style_ref,costing_date,po_breack_down_id,costing_per)
	{
		var rate_amt=2; var zero_val='';
		if(type!='mo_sheet')
		{
			var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		}
		var excess_per_val="";
		if(type=='mo_sheet')
		{
			excess_per_val = prompt("Please enter your Excess %", "0");
			if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
		}

		if (r==true) zero_val="1"; else zero_val="0";
	
		var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&txt_job_no='"+job_no+"'&cbo_company_name="+company_name+"&cbo_buyer_name="+buyer_name+"&txt_style_ref='"+style_ref+"'&txt_costing_date='"+costing_date+"'&txt_po_breack_down_id=''&cbo_costing_per="+costing_per+"&print_option_id=''";
		show_msg('3');
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
		
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			//show_msg('3');
			release_freezing();
		}
	}
    
	
	
	
	function new_window()
    {
		var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;} </style><body>'+document.getElementById('report_container3').innerHTML+'</body</html>'); 
        d.close();
    }
	</script>
    
    <div style="display:none;" id="data_panel"></div>   
    
    <a href="<?= $name;?>" style="text-decoration:none">
    <input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>
    </a>
    <input type="button" onclick="new_window()" value="HTML" name="Print" class="formbutton" style="width:100px"/>
    
    <? ob_start();?>
    
    <div style="overflow:hidden; margin: 0 auto;" id="report_container3">
    <table width="100%" border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table" rules="all" id="table_body">
		<thead>
			<th>Floor</th>
			<th>Line No</th>
			<th>Buyer</th>
			<th>Style Ref.</th>
			<th>Order No</th>
			<th>Job No</th>
			<th>Garments Item</th>
			<th>Order Qnty</th>
            <th>SMV</th>
            <th>CM/Dzn</th>
            <th>Margin /Dzn</th>
            <th>Margin /Pcs</th>
			<th>Production Qty </th>
			<th>Production Margin Value</th>
            <th>Line total</th>
        </thead>
        <tbody>
        <? 
		$total_pro_qty=0;
		$i=1;
		foreach($orderDataArr as $rows){ 
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		
		
		
		//from class ....................start;	
			
			$po_cm_cost=$other_costing_arr[$rows[JOB_NO]]['cm_cost'];
			
			$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$rows[JOB_NO]]);
			$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$rows[JOB_NO]]);
			$yarn_cost_amount=$yarnCostArr[$rows[JOB_NO]];
			//*$po_wise_export_arr[$rows[JOB_NO]]['total_set_qnty']
			$conversion_cost_amount=array_sum($conversionCostArr[$rows[JOB_NO]]);
			$trims_cost_amount=$trimsCostArr[$rows[JOB_NO]];
			$emblishment_cost_amount=$emblishmentCostArr[$rows[JOB_NO]];
			$wash_cost_amount=$washCostArr[$rows[JOB_NO]];
			$commercial_cost_amount=$commercialCostArr[$rows[JOB_NO]];
			$commision_cost_amount=$commisionCostArr[$rows[JOB_NO]];
			
			$po_cm_cost_val=$other_costing_arr[$rows[JOB_NO]]['cm_cost'];
			$po_lab_test_val=$other_costing_arr[$rows[JOB_NO]]['lab_test'];
			$po_inspection_val=$other_costing_arr[$rows[JOB_NO]]['inspection'];
			$po_currier_cost_val=$other_costing_arr[$rows[JOB_NO]]['currier_pre_cost'];
			$po_design_cost_val=$other_costing_arr[$rows[JOB_NO]]['design_cost'];
			$po_studio_cost_val=$other_costing_arr[$rows[JOB_NO]]['studio_cost'];
			
			$po_freight_val=$other_costing_arr[$rows[JOB_NO]]['freight'];
			$po_common_oh_val=$other_costing_arr[$rows[JOB_NO]]['common_oh'];
			$po_depr_amor_pre_cost_val=$other_costing_arr[$rows[JOB_NO]]['depr_amor_pre_cost'];
			$po_certificate_pre_cost_val=$other_costing_arr[$rows[JOB_NO]]['certificate_pre_cost'];
			
			$deffdlc_cost=$other_costing_arr[$rows[JOB_NO]]['deffdlc_cost'];
			$incometax_cost=$job_wise_export_arr[$rows[JOB_NO]]['incometax_cost'];	
			$interest_cost=$job_wise_export_arr[$rows[JOB_NO]]['interest_cost'];
			
			
			$totalCost=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);
			
			$qnty_unit_price_value_fob=$job_wise_export_arr[$rows[JOB_NO]]['job_quantity']*$job_wise_export_arr[$rows[JOB_NO]]['avg_unit_price'];
			
			
			$cm_mergin_pcs=($jobData[$rows[JOB_NO]][FOV_VAL]-$totalCost)/$jobData[$rows[JOB_NO]][ORDER_QUANTITY];
			//$cm_cost=$job_wise_export_arr[$rows[JOB_NO]]['cm_cost'];
			$cm_cost=$job_wise_export_arr[$rows[JOB_NO]]['cm_cost']/$jobData[$rows[JOB_NO]][TOTAL_SET_QNTY];
			
			
			//from class ....................end;	
		
//echo array_sum($jobDataArr[FOB_VALUE][$rows[JOB_NO]]).'-'.$totalCost.')/'.array_sum($jobDataArr[PO_QUANTITY][$rows[JOB_NO]]);
		
	
	//echo array_sum($jobDataArr[FOB_VALUE][$rows[JOB_NO]]).'-'.$totalCost.')/'.array_sum($jobDataArr[PO_QUANTITY][$rows[JOB_NO]]);
	
		
		
		
		
		
		$costing_per=$job_wise_export_arr[$rows[JOB_NO]]['costing_per'];
		if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
		else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
		else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
		else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
		else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
		
		$cm_cost_pcs=$cm_cost/$order_price_per_dzn;		
		
		//------------------------
		$line_pro_cm_value=0;
		foreach($floor_marge_data[$rows[FLOOR_ID].$rows[SEWING_LINE]] as $job=>$pro_value){
			
			$costing_per=$job_wise_export_arr[$job]['costing_per'];
			if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			
			$po_cm_cost=$other_costing_arr[$job]['cm_cost'];
			
			$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$job]);
			$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$job]);
			$yarn_cost_amount=$yarnCostArr[$job];

			$conversion_cost_amount=array_sum($conversionCostArr[$job]);
			$trims_cost_amount=$trimsCostArr[$job];
			$emblishment_cost_amount=$emblishmentCostArr[$job];
			$wash_cost_amount=$washCostArr[$job];
			$commercial_cost_amount=$commercialCostArr[$job];
			$commision_cost_amount=$commisionCostArr[$job];
			
			$po_cm_cost_val=$other_costing_arr[$job]['cm_cost'];
			$po_lab_test_val=$other_costing_arr[$job]['lab_test'];
			$po_inspection_val=$other_costing_arr[$job]['inspection'];
			$po_currier_cost_val=$other_costing_arr[$job]['currier_pre_cost'];
			$po_design_cost_val=$other_costing_arr[$job]['design_cost'];
			$po_studio_cost_val=$other_costing_arr[$job]['studio_cost'];
			
			$po_freight_val=$other_costing_arr[$job]['freight'];
			$po_common_oh_val=$other_costing_arr[$job]['common_oh'];
			$po_depr_amor_pre_cost_val=$other_costing_arr[$job]['depr_amor_pre_cost'];
			$po_certificate_pre_cost_val=$other_costing_arr[$job]['certificate_pre_cost'];
			
			$deffdlc_cost=$other_costing_arr[$job]['deffdlc_cost'];
			$incometax_cost=$job_wise_export_arr[$job]['incometax_cost'];	
			$interest_cost=$job_wise_export_arr[$job]['interest_cost'];
			
			
			$totalCost_=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);
			
			$cmMerginPcs=($jobData[$job][FOV_VAL]-$totalCost_)/$jobData[$job][ORDER_QUANTITY];
			
			$line_pro_cm_value+=$cmMerginPcs*$pro_value;
		}
		
		
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td><?= $floor_lib[$rows[FLOOR_ID]];?></td>
			<td><?= $rows[SEWING_LINE];?></td>
			<td><?= $buyer_lib[$rows[BUYER_NAME]];?></td>
			<td><?= $rows[STYLE_REF_NO];?></td>
			<td><?= $rows[PO_NUMBER];?></td>
			<td><a href="javascript:generate_report('bomRpt2','<?= $rows[JOB_NO];?>',<?= $company_id;?>,'<?= $rows[BUYER_NAME];?>','<?= $rows[STYLE_REF_NO];?>','<?= $job_wise_export_arr[$rows[JOB_NO]]['COSTING_DATE'];?>','',<?= $costing_per;?>);"><?= $rows[JOB_NO];?></a></td>
			<td><?= $garments_item[$rows[ITEM_NUMBER_ID]];?></td>
			<td align="right"><?= $rows[PO_QUANTITY];?></td>
			
            <td align="center"><?= $itemSMVData[$rows[ITEM_NUMBER_ID]];?></td>
			<td align="center"><?= $cm_cost_pcs*12 ;?></td>
			<td align="center"><?= number_format($cm_mergin_pcs*12,2);?></td>
            
			<td align="right"><?= number_format($cm_mergin_pcs,2);?></td>
			<td align="right"><?= $rows[PRODUCTION_QTY];?></td>
            <td align="right" title="<?= $cm_mergin_pcs.'*'.$rows[PRODUCTION_QTY];?>"><?= number_format($production_cm_val=$cm_mergin_pcs*$rows[PRODUCTION_QTY],2);?></td>
			<? if($floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]>0){ ?>
            <td valign="middle" align="center" rowspan="<?= $floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]];?>" >
				<?
					echo number_format($line_pro_cm_value,2);
					$floor_marge[$rows[FLOOR_ID].$rows[SEWING_LINE]]=0;
				?>
            </td>
            <? } ?>
        </tr>
        <?
		$total_pro_qty+=$rows[PRODUCTION_QTY];
		$total_pro_cm_val+=$production_cm_val;
		}
		?>
        </tbody>
		<tfoot>
			<th></th>
			<th> </th>
			<th></th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th> </th>
			<th><?= $total_pro_qty;?></th>
			<th><?= number_format($total_pro_cm_val,2);?> </th>
			<th> </th>
        </tfoot>
    </table>
    
    
    </div>
            

<?	
	$html=ob_get_contents();
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	exit(); 	
}





?>