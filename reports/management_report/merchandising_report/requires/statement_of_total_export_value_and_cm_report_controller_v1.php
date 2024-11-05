<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.others.php');
require_once('../../../../includes/class3/class.fabrics.php');
require_once('../../../../includes/class3/class.yarns.php');
require_once('../../../../includes/class3/class.conversions.php');
require_once('../../../../includes/class3/class.trims.php');
require_once('../../../../includes/class3/class.emblishments.php');
require_once('../../../../includes/class3/class.washes.php');
require_once('../../../../includes/class3/class.commercials.php');
require_once('../../../../includes/class3/class.commisions.php');

error_reporting(1);
ini_set('display_errors',1);

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}


if($action=="report_generate_3")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_title=str_replace("'","",$report_title);
	$reportType=str_replace("'","",$reportType);
        $cbo_item_catgory=str_replace("'","",$cbo_item_catgory);
        
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
		$date_cond="";
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and a.ex_factory_date between '$start_date' and '$end_date'";
			
		}
                if($cbo_item_catgory != "")
                {
                    $cbo_item_cond = " and c.product_category in ($cbo_item_catgory) ";
                }
	
	ob_start();
	if($reportType==3)
	{
		
		$i=1;
		
		
		 $sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty	,
			max(a.ex_factory_date) as ex_factory_date,max(b.pub_shipment_date) as pub_shipment_date			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and d.entry_from=158 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		//echo $sql;
		
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		foreach($sql_result as $row)
		{
			$po_wise_export_arr[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];	
			$po_wise_export_arr[$row[csf("po_id")]]['job']=$row[csf("job_no_prefix_num")];
			$po_wise_export_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['style']=$row[csf("style_ref_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_fac_qty']=$row[csf("ex_factory_qnty")]-$row[csf("ret_ex_factory_qnty")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity']=$row[csf("po_quantity")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_factory_date']=$row[csf("ex_factory_date")];
			$po_wise_export_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
			
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
		
			$poIdArr[$row[csf("po_id")]]=$row[csf("po_id")];
			$jobArr[$row[csf("job_no")]]=$row[csf("job_no")];
		}
		
	
		
			$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$job_con =" and (b.job_no_mst in('".implode("','",$job_no_process)."')";} 
				else{$job_con .=" or b.job_no_mst in('".implode("','",$job_no_process)."')";}
				$p++;
			}
			$job_con .=")";
			
			
			$onlyJobQty_sql=sql_select("select b.id,b.job_no_mst, sum(b.po_quantity) as po_quantity, sum(c.total_set_qnty*b.po_quantity) as po_quantity_pcs,max(c.avg_unit_price/c.total_set_qnty) as unit_price,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1 $job_con   group by b.id,b.job_no_mst,c.avg_unit_price ");// and b.id in(".implode(',',$poIdArr).")
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']+=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
				$po_pcs_arr[$row[csf("id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];	
				$po_pcs_arr[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];	
			}
			//print_r($job_wise_export_arr['OG-19-00959']); die;
		
		
		$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set,b.incometax_cost,b.interest_cost from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0";
			$pre_result=sql_select($sql_pre);
			foreach($pre_result as $row)
			{
				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost']=$row[csf("incometax_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['interest_cost']=$row[csf("interest_cost")];
			}
			
			
		unset($pre_result);
		 $all_job_no=array_unique(explode(",",$all_full_job));
			$all_jobs="";
			foreach($all_job_no as $jno)
			{
					if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
			}
	
	
	 ///print_r($all_jobs); die;
		
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
			
		if($db_type==0 || $db_type==2)
		 {
			if(str_replace("'","",$all_jobs)!='')
			{
				$condition->job_no("in($all_jobs)");
			}
			else{
				$condition->job_no("in('0')");
			}
		}				
		?>
        <div style="width:1250px;">
                <table width="1250"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="14" class="form_caption">
                            <strong style="font-size:16px;">Company:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="14" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                    </table>
               
                <table width="1410" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="150">Buyer</th>
                        <th width="130">Style Name</th>
                        <th width="80">Job No</th>
                        <th width="120">Order No</th>
                        
                        <th width="80">Pub shipment date</th>
                        <th width="80">Ex-factory date</th>
                        
                        <th width="100">Order Qty (Pcs)</th>
                        <th width="60">Unit Price(USD)</th>
                        <th width="80">CM Cost Per Pcs(USD)</th>
                        <th width="80">CM Margin Per Pcs(USD)</th>
                        <th width="80">Ex-Factory Qty(Pcs)</th>
                        <th width="80">Ex-Factory FOB Value(USD)</th>
                        <th width="80">Ex-Factory CM Cost(USD)</th>
                        <th width="80">Ex-Factory Margin(USD)</th>
                        <th width="">Ex-Factory CM Cost With Margin(USD)</th>
                    </thead>
                </table>
            <div style="width:1428px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
               <?
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
					//print_r($other_costing_arr);
  					$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_job();
					$trimsCostArr=$trim->getAmountArray_by_job();
					$emblishmentCostArr=$emblishment->getAmountArray_by_job();
					$washCostArr=$wash->getAmountArray_by_job();
					$commercialCostArr=$commercial->getAmountArray_by_job();
					$commisionCostArr=$commision->getAmountArray_by_job();
 
					
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	foreach($po_wise_export_arr as $po_id=>$row)
				 {
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						
						$fabric_cost_knit_amount=($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						
						$fabric_cost_woven_amount=($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=($conversionCostArr[$row[('job_no')]]);
						$trims_cost_amount=$trimsCostArr[$row[('job_no')]];
						$emblishment_cost_amount=$emblishmentCostArr[$row[('job_no')]];
						$wash_cost_amount=$washCostArr[$row[('job_no')]];
						$commercial_cost_amount=$commercialCostArr[$row[('job_no')]];
						$commision_cost_amount=$commisionCostArr[$row[('job_no')]];

						$po_cm_cost_val=$other_costing_arr[$row[('job_no')]]['cm_cost'];
						$po_lab_test_val=$other_costing_arr[$row[('job_no')]]['lab_test'];
						$po_inspection_val=$other_costing_arr[$row[('job_no')]]['inspection'];
						$po_currier_cost_val=$other_costing_arr[$row[('job_no')]]['currier_pre_cost'];
						$po_design_cost_val=$other_costing_arr[$row[('job_no')]]['design_cost'];
						$po_studio_cost_val=$other_costing_arr[$row[('job_no')]]['studio_cost'];
							//$interest_expense=$pre_cost_date_arr[$row[('job_no')]]['interest_expense']/100;
							//$income_tax=$pre_cost_date_arr[$row[('job_no')]]['income_tax']/100;
						$po_freight_val=$other_costing_arr[$row[('job_no')]]['freight'];
						$po_common_oh_val=$other_costing_arr[$row[('job_no')]]['common_oh'];
						$po_depr_amor_pre_cost_val=$other_costing_arr[$row[('job_no')]]['depr_amor_pre_cost'];
						$po_certificate_pre_cost_val=$other_costing_arr[$row[('job_no')]]['certificate_pre_cost'];
						
						$deffdlc_cost=$other_costing_arr[$row[('job_no')]]['deffdlc_cost'];
						$incometax_cost=$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost'];	
						$interest_cost=$job_wise_export_arr[$row[csf("job_no")]]['interest_cost'];
						
						
						//$totalOtherCost=($po_cm_cost_val+$po_lab_test_val+$po_inspection_val+$po_currier_cost_val+$po_design_cost_val+$po_studio_cost_val+$po_freight_val+$po_common_oh_val+$po_depr_amor_pre_cost_val+$po_certificate_pre_cost_val);
						
						//$totalCost_second=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+$conversion_cost_amount+$trims_cost_amount+$emblishment_cost_amount+$wash_cost_amount+$commercial_cost_amount+$commision_cost_amount);
						//$totalCost=($totalOtherCost+$totalCost_second);
						
						
						
						$totalCost=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);

						//echo $totalCost;die;
						
						

						$qnty_unit_price_value_fob=$job_wise_export_arr[$row[('job_no')]]['job_quantity']*$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];

						
						$ex_fact_mergin_new_price=($qnty_unit_price_value_fob-$totalCost)/$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
						$ex_fact_mergin_new=$row[('ex_fac_qty')]*$ex_fact_mergin_new_price;

						$job_no=$row[("job_no")];
						
						$costing_per=$job_wise_export_arr[$job_no]['costing_per'];
						$cm_cost=$job_wise_export_arr[$job_no]['cm_cost'];
						$order_value=$row['po_quantity']*$row['unit_price'];
						
					if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
					$other_value=$job_wise_export_arr[$job_no]['other_value'];
					if($other_value!=0)
					{
					$otherCost=$other_value/$order_price_per_dzn*$row[('po_quantity')]; 
					//$cmValue_mergin = $order_value-$otherCost; 
					}
					$cm_mergin_pcs=$ex_fact_mergin_new_price;
					$ex_fact_mergin=$row[('ex_fac_qty')]*$cm_mergin_pcs;
					$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
					
					$jobQntryPcs=$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
					$jobUnitPcs=$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
					//$exFactory_CM_Cost_USD=$po_cm_cost/$row[('ex_fac_qty')]*$poQntryPcs;
					$exFactory_CM_Cost_USD=$po_cm_cost/$jobQntryPcs;
					
					$po_cm_cost=$job_wise_export_arr[$row[("job_no")]]['cm_cost']; 
					$exFactory_CM_Cost_USD_2=$po_cm_cost/$order_price_per_dzn/$row['total_set_qnty'];
					
			   ?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="150"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
							<td width="130" align="center"><p><? echo $row[('style')]; ?></p></td>
							<td width="80"><p title="<? echo $row[('job')]; ?>"><? echo $row[('job_no')]; ?></p></td>
							<td width="120"><div style="word-break:break-all"><? echo $row[('po_number')]; ?></div></td>
                            <td width="80" align="center"><? echo change_date_format($row['pub_shipment_date']);?></td>
                            <td width="80" align="center"><? echo change_date_format($row['ex_factory_date']);?></td>
                            
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($po_pcs_arr[$po_id]['po_quantity_pcs']); ?></div></td>
							<td width="60"  align="right"><p><? echo number_format($po_pcs_arr[$po_id]['unit_price'],4); ?></p></td>
							<td width="80" align="right" title=""><div style="word-wrap:break-all"><? $cm_cost_per_pcs=$cm_cost_pcs;echo number_format($exFactory_CM_Cost_USD_2,4); ?></div></td>
                            <td width="80"align="right"  title="<?  echo 'Total Value:('.$qnty_unit_price_value_fob.'- total cost: '.$totalCost.')/ Total PCS Qty: '.$job_wise_export_arr[$row[('job_no')]]['job_quantity'];?>">
                            <div style="word-break:break-all"><? echo number_format($cm_mergin_pcs/$row['total_set_qnty'],4); ?></div></td>
							<td width="80"  align="right" title="Ex-fac Qty"> <div style="word-break:break-all"> <? echo number_format($row[('ex_fac_qty')],0);
							$total_ex_fac_qty+=$row[('ex_fac_qty')];
							
							 ?>   </div> </td>
							<td width="80" align="right" title="<?= $row[('ex_fac_qty')].'*'.$po_pcs_arr[$po_id]['unit_price'];?>" > <?  echo number_format($row[('ex_fac_qty')]*$po_pcs_arr[$po_id]['unit_price'],2); 
							
							$total_ex_fac_val+=$row[('ex_fac_qty')]*$po_pcs_arr[$po_id]['unit_price'];
							?></td>
							<td width="80" align="right" title="CM Cost Per Pcs(USD) * Ex-Factory Qty"><div style="word-break:break-all"><? echo number_format($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2,2);
							$total_ex_fac_cm_cost+=$row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2;
							
							?> </div></td>
                            
                            <td width="80" align="right" title="<?=$row[('ex_fac_qty')].'*'.($cm_mergin_pcs/$row['total_set_qnty']);?>"><p><?
							
							$ex_fac_qty_cm_cost_mergin=$row[('ex_fac_qty')]*($cm_mergin_pcs/$row['total_set_qnty']);
							 //echo number_format($ex_fact_mergin,2);
							echo number_format($ex_fac_qty_cm_cost_mergin,2); 
							$total_ex_fac_mergin+=$ex_fac_qty_cm_cost_mergin;
							 ?> </p></td> 
                             
							<td width="" align="right"><p>
							 <? echo number_format($ex_fac_qty_cm_cost_mergin+($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2),2);
							 
							 $TotalExFactoryCMCostWithMarginUSD+=$ex_fac_qty_cm_cost_mergin+($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2);
							 ?>
                             </p>
							</td>
                            
                   	</tr>
                            <?
							$total_po_qty_pcs+=$po_pcs_arr[$po_id]['po_quantity_pcs'];
							//$total_ex_fac_qty+=$row[('ex_fac_qty')];
							//$total_ex_fac_val+=$row[('ex_fac_qty')]*$row[('unit_price')];
							//$ex_fac_cm_cost+=$exFactory_CM_Cost_USD*$row[('ex_fac_qty')];
							
							
							
							//$total_ex_fac_qty_cm_cost_mergin+=$ex_fac_qty_cm_cost_mergin;
							
							$i++;
					}
							?>
               </table>
            <table width="1410" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100"><? echo number_format($total_po_qty_pcs,0); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80"><? echo number_format($total_ex_fac_qty,0); ?></th>
                        <th width="80"><? echo number_format($total_ex_fac_val); ?></th>
                        <th width="80"><? echo number_format($total_ex_fac_cm_cost); ?></th>
                        <th width="80"><? echo number_format($total_ex_fac_mergin); ?></th>
                        <th><? echo number_format($TotalExFactoryCMCostWithMarginUSD); ?></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>
	   
		<?
	}
	
	else if($reportType==4)
	{
		
		$i=1;
		
			$onlyJobQty_sql=sql_select("select b.job_no_mst, sum(b.po_quantity) as po_quantity,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1  group by b.job_no_mst,c.avg_unit_price ");
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
			}
			//print_r($job_wise_export_arr);
			 $sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and d.entry_from=158 and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		
		//echo $sql;
		
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		foreach($sql_result as $row)
		{
			$po_wise_export_arr[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];	
			$po_wise_export_arr[$row[csf("po_id")]]['job']=$row[csf("job_no_prefix_num")];
			$po_wise_export_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['style']=$row[csf("style_ref_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_fac_qty']=$row[csf("ex_factory_qnty")]-$row[csf("ret_ex_factory_qnty")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity']=$row[csf("po_quantity")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
			$jobArr[$row[csf("job_no")]]=$row[csf("job_no")];
		}
		
		
			$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$job_con =" and (b.job_no_mst in('".implode("','",$job_no_process)."')";} 
				else{$job_con .=" or b.job_no_mst in('".implode("','",$job_no_process)."')";}
				$p++;
			}
			$job_con .=")";
			
			$onlyJobQty_sql=sql_select("select b.id,b.job_no_mst, sum(b.po_quantity) as po_quantity, sum(c.total_set_qnty*b.po_quantity) as po_quantity_pcs,max(c.avg_unit_price/c.total_set_qnty) as unit_price,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1 $job_con   group by b.id,b.job_no_mst,c.avg_unit_price ");// and b.id in(".implode(',',$poIdArr).")
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']+=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
				$po_pcs_arr[$row[csf("id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];	
				$po_pcs_arr[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];	
			}		
		
		$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set,b.incometax_cost,b.interest_cost from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0";
			$pre_result=sql_select($sql_pre);
			foreach($pre_result as $row)
			{
				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost']=$row[csf("incometax_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['interest_cost']=$row[csf("interest_cost")];
			}

			
			
			
			unset($pre_result);
				 $all_job_no=array_unique(explode(",",$all_full_job));
					$all_jobs="";
					foreach($all_job_no as $jno)

					{
							if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
					}
	 ///print_r($all_jobs); die;
		
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
			
			 	if($db_type==0 || $db_type==2)
				 {
					 if(str_replace("'","",$all_jobs)!='')
					{
						$condition->job_no("in($all_jobs)");
					}
				}				
		?>
        <div style="width:850px;">
                <table width="850"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="8" class="form_caption">
                            <strong style="font-size:16px;">Company:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="8" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                    </table>
               
                <table width="850" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="150">Buyer</th>
                        <th width="50">No of Style</th>
                        <th width="50">No Of PO</th>
                        <th width="90">Ex-Factory Qty(Pcs)</th>
                        <th width="90">Ex-Factory FOB Value(USD)</th>
                        <th width="90">Ex-Factory CM Cost(USD)</th>
                        <th width="90">Ex-Factory Margin(USD)</th>
                        <th width="90">Ex-Factory CM Cost With Margin(USD)</th>
                        <th width="">CM%</th>
                    </thead>
                </table>
            <div style="width:870px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
               <?
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
					//print_r($other_costing_arr);
  					$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_job();
					$trimsCostArr=$trim->getAmountArray_by_job();
					$emblishmentCostArr=$emblishment->getAmountArray_by_job();
					$washCostArr=$wash->getAmountArray_by_job();
					$commercialCostArr=$commercial->getAmountArray_by_job();
					$commisionCostArr=$commision->getAmountArray_by_job();
 
				
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	 foreach($po_wise_export_arr as $po_id=>$row)
				 {
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	

						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						
						$fabric_cost_knit_amount=($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=($conversionCostArr[$row[('job_no')]]);
						$trims_cost_amount=$trimsCostArr[$row[('job_no')]];
						$emblishment_cost_amount=$emblishmentCostArr[$row[('job_no')]];
						$wash_cost_amount=$washCostArr[$row[('job_no')]];
						$commercial_cost_amount=$commercialCostArr[$row[('job_no')]];
						$commision_cost_amount=$commisionCostArr[$row[('job_no')]];

						$po_cm_cost_val=$other_costing_arr[$row[('job_no')]]['cm_cost'];
						$po_lab_test_val=$other_costing_arr[$row[('job_no')]]['lab_test'];
						$po_inspection_val=$other_costing_arr[$row[('job_no')]]['inspection'];
						$po_currier_cost_val=$other_costing_arr[$row[('job_no')]]['currier_pre_cost'];
						$po_design_cost_val=$other_costing_arr[$row[('job_no')]]['design_cost'];
						$po_studio_cost_val=$other_costing_arr[$row[('job_no')]]['studio_cost'];
							//$interest_expense=$pre_cost_date_arr[$row[('job_no')]]['interest_expense']/100;
							//$income_tax=$pre_cost_date_arr[$row[('job_no')]]['income_tax']/100;
						$po_freight_val=$other_costing_arr[$row[('job_no')]]['freight'];
						$po_common_oh_val=$other_costing_arr[$row[('job_no')]]['common_oh'];
						$po_depr_amor_pre_cost_val=$other_costing_arr[$row[('job_no')]]['depr_amor_pre_cost'];
						$po_certificate_pre_cost_val=$other_costing_arr[$row[('job_no')]]['certificate_pre_cost'];
						
						$deffdlc_cost=$other_costing_arr[$row[('job_no')]]['deffdlc_cost'];
						$incometax_cost=$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost'];	
						$interest_cost=$job_wise_export_arr[$row[csf("job_no")]]['interest_cost'];
						
					//$totalOtherCost=($po_cm_cost_val+$po_lab_test_val+$po_inspection_val+$po_currier_cost_val+$po_design_cost_val+$po_studio_cost_val+$po_freight_val+$po_common_oh_val+$po_depr_amor_pre_cost_val+$po_certificate_pre_cost_val);
						//$totalCost_second=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+$conversion_cost_amount+$trims_cost_amount+$emblishment_cost_amount+$wash_cost_amount+$commercial_cost_amount+$commision_cost_amount);
						
						//$totalCost=($totalOtherCost+$totalCost_second);
						
						
						$totalCost=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);
						
						
						
						
						$qnty_unit_price_value_fob=$job_wise_export_arr[$row[('job_no')]]['job_quantity']*$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
						$ex_fact_mergin_new_price=($qnty_unit_price_value_fob-$totalCost)/$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
						//$ex_fact_mergin_new=$row[('ex_fac_qty')]*$ex_fact_mergin_new_price;
						
						$ex_fact_mergin_new=$row[('ex_fac_qty')]*($ex_fact_mergin_new_price/$row['total_set_qnty']);
						
						
						$job_no=$row[("job_no")];
						$costing_per=$job_wise_export_arr[$job_no]['costing_per'];
						$cm_cost=$job_wise_export_arr[$job_no]['cm_cost'];
						$order_value=$row['po_quantity']*$row['unit_price'];
						
					if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
					$other_value=$job_wise_export_arr[$job_no]['other_value'];
					if($other_value!=0)
					{
					$otherCost=$other_value/$order_price_per_dzn*$row[('po_quantity')]; 
					//$cmValue_mergin = $order_value-$otherCost; 
					}
					
					
					$cm_mergin_pcs=$ex_fact_mergin_new_price;
					$ex_fact_mergin=$row[('ex_fac_qty')]*$cm_mergin_pcs;
					
					
					$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
					
					$jobQntryPcs=$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
					$jobUnitPcs=$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
					//$exFactory_CM_Cost_USD=$po_cm_cost/$jobQntryPcs;
					
					$job_cm_cost=$job_wise_export_arr[$row[("job_no")]]['cm_cost'];
					$exFactory_CM_Cost_USD=$job_cm_cost/$order_price_per_dzn/$row['total_set_qnty'];
					
					
					$ex_fac_qty_cm_cost_mergin=$ex_fact_mergin+($row[('ex_fac_qty')]*$cm_cost_pcs);
					
					
					$buyer_wise_arr[$row['buyer_name']]['ex_fac_qty']+=$row[('ex_fac_qty')];
					//$buyer_wise_arr[$row['buyer_name']]['exfactory_fob_value']+=($row[('ex_fac_qty')]*$row['unit_price']);
					$buyer_wise_arr[$row['buyer_name']]['exfactory_fob_value']+=($row[('ex_fac_qty')]*$po_pcs_arr[$po_id]['unit_price']);
					
					$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_CM_Cost_USD']+=($exFactory_CM_Cost_USD*$row[('ex_fac_qty')]);
					$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_Margin_USD']+=$ex_fact_mergin_new;
					$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_CM_Cost_With_Margin_USD']+=$ex_fac_qty_cm_cost_mergin;
					//echo number_format($ex_fac_qty_cm_cost_mergin,2);

					$buyer_wise_arr[$row['buyer_name']]['ExFactoryCMCostWithMarginUSD']=$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_Margin_USD']+$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_CM_Cost_USD'];
					$buyer_wise_arr[$row['buyer_name']]['cm_percent']=(($buyer_wise_arr[$row['buyer_name']]['ExFactoryCMCostWithMarginUSD']/$buyer_wise_arr[$row['buyer_name']]['exfactory_fob_value'])*100);

					$buyer_wise_arr[$row['buyer_name']]['po_number_arr'][] = $row[('po_number')];
					$buyer_wise_arr[$row['buyer_name']]['style_arr'][] = $row[('style')];
				 }

				 // Sorting function
				function sortByCm($a, $b) {
				    return $b['cm_percent'] - $a['cm_percent'];
				}
				uasort($buyer_wise_arr, 'sortByCm');	// sorty result by CM % DESC
					
				foreach($buyer_wise_arr as $buyer_id=>$buyer_data)
				{
					$short_excess_qty=0;
					$short_excess_value=0;
					$short_excess_qty=$buyer_data['short_excess'];
					$short_excess_value=$buyer_data['short_excess_value'];
					$exfactory_fob_value=$buyer_data['exfactory_fob_value'];
					$exfactory_margin_value=$buyer_data['exfactory_margin'];
					$exfactory_subcontact=$buyer_data['outbound'];

					$po_count = count(array_unique($buyer_data['po_number_arr']));
					$style_count = count(array_unique($buyer_data['style_arr']));
					
					//$exFactory_CM_Cost_USD_2=$po_cm_cost/$order_price_per_dzn/$row['total_set_qnty'];
					
			   ?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
							<td width="150"><? echo $buyer_arr[$buyer_id];; ?></td>
                            <td width="50" align="center"><?php echo $style_count; ?></td>
							<td width="50" align="center"><?php echo $po_count; ?></td>
							<td width="90"  align="right" title="Ex-fac Qty"> <div style="word-break:break-all"> <? echo number_format($buyer_data['ex_fac_qty'],0); ?>   </div> </td>
							<td width="90" align="right" > <?  echo number_format($buyer_data['exfactory_fob_value'],4);?></td>
							<td width="90" align="right" title="CM Cost Per Pcs(USD) * Ex-Factory Qty"><div style="word-break:break-all"><? echo number_format($buyer_data['Ex_Factory_CM_Cost_USD'],4);?> </div></td>
                            <td width="90" align="right"><div style="word-break:break-all" title="">
								<? 
								echo number_format($buyer_data['Ex_Factory_Margin_USD'],4); 
							 	?> 
                             </div></td> 
							<td width="90" align="right"><p> 
							<? 
								// $ExFactoryCMCostWithMarginUSD=$buyer_data['Ex_Factory_Margin_USD']+$buyer_data['Ex_Factory_CM_Cost_USD'];
								// echo number_format($ExFactoryCMCostWithMarginUSD,4); //
								echo number_format($buyer_data['ExFactoryCMCostWithMarginUSD'],4);$buyer_data['Ex_Factory_CM_Cost_With_Margin_USD']  
							?> 
                            </p>
							</td>
                            <td width="" align="right"><p>
							 <? 
							 	// $cm_percent=(($ExFactoryCMCostWithMarginUSD/$buyer_data['exfactory_fob_value'])*100);

								echo number_format($buyer_data['cm_percent'],2)."%";  ?>
                             </p>
							</td>
                   	</tr>
                            <?
							
							$total_ex_fac_qty+=$buyer_data['ex_fac_qty'];
							$total_ex_fac_val+=$buyer_data['exfactory_fob_value'];
							$ex_fac_cm_cost+=$buyer_data['Ex_Factory_CM_Cost_USD'];
							$total_ex_fac_mergin+=$buyer_data['Ex_Factory_Margin_USD'];
							// $total_ex_fac_qty_cm_cost_mergin+=$ExFactoryCMCostWithMarginUSD;
							$total_ex_fac_qty_cm_cost_mergin+=$buyer_data['ExFactoryCMCostWithMarginUSD'];
							$i++;
					}
							?>
               </table>
            <table width="850" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                    	<th width="50">&nbsp;</th>
                    	<th width="50">&nbsp;</th>
                        <th width="90" id="value_total_ex_fac_qty" align="right"><? echo number_format($total_ex_fac_qty,0); ?></th>
                        <th width="90" id="value_total_ex_fac_val" align="right"><? echo number_format($total_ex_fac_val); ?></th>
                        <th width="90" id="value_total_ex_fac_cm_cost" align="right"><? echo number_format($ex_fac_cm_cost); ?></th>
                        <th width="90" id="value_total_ex_fac_qty_mergin" align="right"><? echo number_format($total_ex_fac_mergin); ?></th>
                        <th  width="90" id="value_total_ex_fac_qty_cm_cost_mergin" align="right"><? echo number_format($total_ex_fac_qty_cm_cost_mergin); ?></th>
                        <th><? echo number_format(($total_ex_fac_qty_cm_cost_mergin*100)/$total_ex_fac_val,4)."%"; ?> </th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
              <table width="750">
            		<tr>
                        <td align="center"  class="form_caption">&nbsp;  </td>
                        
                    </tr>
                    <tr>
                         <td align="center"  class="form_caption">&nbsp;  </td>
                    </tr>
             </table>
            
            <table width="350"  cellspacing="0"  border="1" class="rpt_table" rules="all">
                    <tr>
                        <td align="left" width="200">TOTAL CM</td>
                        <td width="200" align="right"><? echo number_format($total_ex_fac_qty_cm_cost_mergin); ?> </td>
                    </tr>
                    <tr>
                        <td align="left">TOTAL EXPORT VALUE</td>
                        <td align="right"><? echo number_format($total_ex_fac_val);  ?> </td>
                    </tr>
                    <tr>
                        <td align="left">CM %</td>
                        <td align="right"><? echo number_format(($total_ex_fac_qty_cm_cost_mergin*100)/$total_ex_fac_val,4)."%"; ?> </td>
                    </tr>
              </table>
        </div>
		<?
	}
	
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
	echo "$total_data####$filename####$reportType";
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_title=str_replace("'","",$report_title);
	$reportType=str_replace("'","",$reportType);
        $cbo_item_catgory=str_replace("'","",$cbo_item_catgory);
        
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
		$date_cond="";
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and a.ex_factory_date between '$start_date' and '$end_date'";
			
		}
                if($cbo_item_catgory != "")
                {
                    $cbo_item_cond = " and c.product_category in ($cbo_item_catgory) ";
                }
	
	ob_start();
	if($reportType==1)
	{
		
		$i=1;
		
			$onlyJobQty_sql=sql_select("select b.job_no_mst, sum(b.po_quantity) as po_quantity,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1  group by b.job_no_mst,c.avg_unit_price ");
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
			}
			//print_r($job_wise_export_arr);
			 $sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty	,
			max(a.ex_factory_date) as ex_factory_date,max(b.pub_shipment_date) as pub_shipment_date		
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and d.entry_from=111 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		
		 //echo $sql;die;
		
		$sql_result=sql_select($sql);
		if(count($sql_result)==0){echo "Not Found####$filename####$reportType";exit();}
		
		
		foreach($sql_result as $row)
		{
			$po_wise_export_arr[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];	
			$po_wise_export_arr[$row[csf("po_id")]]['job']=$row[csf("job_no_prefix_num")];
			$po_wise_export_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['style']=$row[csf("style_ref_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_fac_qty']=$row[csf("ex_factory_qnty")]-$row[csf("ret_ex_factory_qnty")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity']=$row[csf("po_quantity")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_factory_date']=$row[csf("ex_factory_date")];
			$po_wise_export_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
			
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
		}
		$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0";
			$pre_result=sql_select($sql_pre);
			foreach($pre_result as $row)
			{
				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
			}
			unset($pre_result);
				 $all_job_no=array_unique(explode(",",$all_full_job));
					$all_jobs="";
					foreach($all_job_no as $jno)
					{
							if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
					}
	 ///print_r($all_jobs); die;
		
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
			
			 	if($db_type==0 || $db_type==2)
				 {
					 if(str_replace("'","",$all_jobs)!='')
					{
						$condition->job_no("in($all_jobs)");
					}
				}				
		?>
        <div style="width:1410px;">
                <table width="1410"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="16" class="form_caption">
                            <strong style="font-size:16px;">Company:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="16" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                    </table>
               
                <table width="1410" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="150">Buyer</th>
                        <th width="130">Style Name</th>
                        <th width="80">Job No</th>
                        <th width="120">Order No</th>
                        <th width="80">Pub shipment date</th>
                        <th width="80">Ex-factory date</th>
                        <th width="100">Order Qty (Pcs)</th>
                        <th width="60">Unit Price(USD)</th>
                        <th width="80">CM Cost Per Pcs(USD)</th>
                        <th width="80">CM Margin Per Pcs(USD)</th>
                        <th width="80">Ex-Factory Qty(Pcs)</th>
                        <th width="80">Ex-Factory FOB Value(USD)</th>
                        <th width="80">Ex-Factory CM Cost(USD)</th>
                        <th width="80">Ex-Factory Margin(USD)</th>
                        <th width="">Ex-Factory CM Cost With Margin(USD)</th>
                    </thead>
                </table>
            <div style="width:1428px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
               <?
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
					
					/*$fabricCostArr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getOrderWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_order();
					$trimsCostArr=$trim->getAmountArray_by_order();
					$emblishmentCostArr=$emblishment->getAmountArray_by_order();
					$washCostArr=$wash->getAmountArray_by_order();
					$commercialCostArr=$commercial->getAmountArray_by_order();
					$commisionCostArr=$commision->getAmountArray_by_order();*/

					$other_costing_arr=$other->getAmountArray_by_job();
					//print_r($other_costing_arr);
  					$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_job();
					$trimsCostArr=$trim->getAmountArray_by_job();
					$emblishmentCostArr=$emblishment->getAmountArray_by_job();
					$washCostArr=$wash->getAmountArray_by_job();
					$commercialCostArr=$commercial->getAmountArray_by_job();
					$commisionCostArr=$commision->getAmountArray_by_job();
 
					
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	foreach($po_wise_export_arr as $po_id=>$row)
				 {
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						$fabric_cost_knit_amount=($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=($conversionCostArr[$row[('job_no')]]);
						$trims_cost_amount=$trimsCostArr[$row[('job_no')]];
						$emblishment_cost_amount=$emblishmentCostArr[$row[('job_no')]];
						$wash_cost_amount=$washCostArr[$row[('job_no')]];
						$commercial_cost_amount=$commercialCostArr[$row[('job_no')]];
						$commision_cost_amount=$commisionCostArr[$row[('job_no')]];

						$po_cm_cost_val=$other_costing_arr[$row[('job_no')]]['cm_cost'];
						$po_lab_test_val=$other_costing_arr[$row[('job_no')]]['lab_test'];
						$po_inspection_val=$other_costing_arr[$row[('job_no')]]['inspection'];
						$po_currier_cost_val=$other_costing_arr[$row[('job_no')]]['currier_pre_cost'];
						$po_design_cost_val=$other_costing_arr[$row[('job_no')]]['design_cost'];
						$po_studio_cost_val=$other_costing_arr[$row[('job_no')]]['studio_cost'];
							//$interest_expense=$pre_cost_date_arr[$row[('job_no')]]['interest_expense']/100;
							//$income_tax=$pre_cost_date_arr[$row[('job_no')]]['income_tax']/100;
						$po_freight_val=$other_costing_arr[$row[('job_no')]]['freight'];
						$po_common_oh_val=$other_costing_arr[$row[('job_no')]]['common_oh'];
						$po_depr_amor_pre_cost_val=$other_costing_arr[$row[('job_no')]]['depr_amor_pre_cost'];
						$po_certificate_pre_cost_val=$other_costing_arr[$row[('job_no')]]['certificate_pre_cost'];
						
						$totalOtherCost=($po_cm_cost_val+$po_lab_test_val+$po_inspection_val+$po_currier_cost_val+$po_design_cost_val+$po_studio_cost_val+$po_freight_val+$po_common_oh_val+$po_depr_amor_pre_cost_val+$po_certificate_pre_cost_val);
						
						$totalCost_second=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+$conversion_cost_amount+$trims_cost_amount+$emblishment_cost_amount+$wash_cost_amount+$commercial_cost_amount+$commision_cost_amount);
						
						
						
						$totalCost=($totalOtherCost+$totalCost_second);
						//$qnty_unit_price_value_fob=$job_wise_export_arr[$row[('job_no')]]['job_quantity']*$row[('unit_price')];
						$qnty_unit_price_value_fob=$job_wise_export_arr[$row[('job_no')]]['job_quantity']*$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];

						
						$ex_fact_mergin_new_price=($qnty_unit_price_value_fob-$totalCost)/$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
						$ex_fact_mergin_new=$row[('ex_fac_qty')]*$ex_fact_mergin_new_price;

						$job_no=$row[("job_no")];
						
						$costing_per=$job_wise_export_arr[$job_no]['costing_per'];
						$cm_cost=$job_wise_export_arr[$job_no]['cm_cost'];
						$order_value=$row['po_quantity']*$row['unit_price'];
						
					if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
					$other_value=$job_wise_export_arr[$job_no]['other_value'];
					if($other_value!=0)
					{
					$otherCost=$other_value/$order_price_per_dzn*$row[('po_quantity')]; 
					//$cmValue_mergin = $order_value-$otherCost; 
					}
					$cm_mergin_pcs=$ex_fact_mergin_new_price;
					$ex_fact_mergin=$row[('ex_fac_qty')]*$cm_mergin_pcs;
					$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
					
					$jobQntryPcs=$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
					$jobUnitPcs=$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
					//$exFactory_CM_Cost_USD=$po_cm_cost/$row[('ex_fac_qty')]*$poQntryPcs;
					$exFactory_CM_Cost_USD=$po_cm_cost/$jobQntryPcs;
					
			   ?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="150"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
							<td width="130"   align="center"><p><? echo $row[('style')]; ?></p></td>
							<td width="80" ><div style="word-break:break-all" title="<? echo $row[('job_no')]; ?>"><? echo $row[('job')]; ?></div></td>
							<td width="120"><div style="word-break:break-all"><? echo $row[('po_number')]; ?></div></td>
                            <td width="80" align="center" ><? echo change_date_format($row['pub_shipment_date']);?></td>
                            <td width="80" align="center" ><? echo change_date_format($row['ex_factory_date']);?></td>
                            
                            
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($jobQntryPcs,0); ?></div></td>
							<td width="60"  align="right"><p><? echo number_format($jobUnitPcs,2); ?></p></td>
							<td width="80" align="right" title="CM Cost/Order Qty pcs"><div style="word-wrap:break-all"><? $cm_cost_per_pcs=$cm_cost_pcs;echo number_format($exFactory_CM_Cost_USD,4); ?></div></td>
                            <td width="80"align="right"  title="<?  echo 'Total Value:('.$qnty_unit_price_value_fob.'- total cost: '.$totalCost.')/ Total PCS Qty: '.$job_wise_export_arr[$row[('job_no')]]['job_quantity'];?>"><div style="word-break:break-all"><? echo number_format($cm_mergin_pcs,4); ?></div></td>
							<td width="80"  align="right" title="Ex-fac Qty"> <div style="word-break:break-all"> <? echo number_format($row[('ex_fac_qty')],0); ?>   </div> </td>
							<td width="80" align="right" > <?  echo number_format($row[('ex_fac_qty')]*$row[('unit_price')],2); ?></td>
							<td width="80" align="right" title="CM Cost Per Pcs(USD) * Ex-Factory Qty"><div style="word-break:break-all"><? echo number_format($exFactory_CM_Cost_USD*$row[('ex_fac_qty')],2);?> </div></td>
                            
                            <td width="80" align="right"><div style="word-break:break-all" title="<? echo 'totalCost: '.$totalCost.'; ='.'((totalValue-totalCost)/poQuantityPcs)*exFacQty'; ?>"><?
							$ex_fac_qty_cm_cost_mergin=$ex_fact_mergin+($row[('ex_fac_qty')]*$cm_cost_per_pcs);
							 //echo number_format($ex_fact_mergin,2);
							echo number_format($ex_fact_mergin_new,2); 
							 ?> </div></td> 
                             
							<td width="" align="right"><p>
							 <? echo number_format($ex_fac_qty_cm_cost_mergin,2);?>
                             </p>
							</td>
                            
                   	</tr>
                            <?
							$total_po_qty_pcs+=$row[('po_quantity_pcs')];
							$total_ex_fac_qty+=$row[('ex_fac_qty')];
							$total_ex_fac_val+=$row[('ex_fac_qty')]*$row[('unit_price')];
							$ex_fac_cm_cost+=$exFactory_CM_Cost_USD*$row[('ex_fac_qty')];
							$total_ex_fac_qty_cm_cost_mergin+=$ex_fac_qty_cm_cost_mergin;
							$total_ex_fac_mergin+=$ex_fact_mergin;
							$i++;
					}
							?>
               </table>
            <table width="1410" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100" id="value_total_po_qty"><? echo number_format($total_po_qty_pcs,0); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80" id="value_total_ex_fac_qty"><? echo number_format($total_ex_fac_qty,0); ?></th>
                        <th width="80" id="value_total_ex_fac_val"><? echo number_format($total_ex_fac_val); ?></th>
                        <th width="80" id="value_total_ex_fac_cm_cost"><? echo number_format($ex_fac_cm_cost); ?></th>
                        <th width="80" id="value_total_ex_fac_qty_mergin"><? echo number_format($total_ex_fac_mergin); ?></th>
                        <th id="value_total_ex_fac_qty_cm_cost_mergin"><? echo number_format($total_ex_fac_qty_cm_cost_mergin); ?></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>
	   
		<?
	}
	else if($reportType==2)
	{
		
		$i=1;
		
			$onlyJobQty_sql=sql_select("select b.job_no_mst, sum(b.po_quantity) as po_quantity,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1  group by b.job_no_mst,c.avg_unit_price ");
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
			}
			
		$sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and d.entry_from=111 and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		
		//echo $sql;
		
		$sql_result=sql_select($sql);
		if(count($sql_result)==0){echo "Not Found####$filename####$reportType";exit();}
		 
		foreach($sql_result as $row)
		{
			$po_wise_export_arr[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];	
			$po_wise_export_arr[$row[csf("po_id")]]['job']=$row[csf("job_no_prefix_num")];
			$po_wise_export_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['style']=$row[csf("style_ref_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_fac_qty']=$row[csf("ex_factory_qnty")]-$row[csf("ret_ex_factory_qnty")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity']=$row[csf("po_quantity")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
		}
		$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0";
			$pre_result=sql_select($sql_pre);
			foreach($pre_result as $row)
			{
				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
			}
			unset($pre_result);
				 $all_job_no=array_unique(explode(",",$all_full_job));
					$all_jobs="";
					foreach($all_job_no as $jno)
					{
							if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
					}
	 ///print_r($all_jobs); die;
		
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
			
			 	if($db_type==0 || $db_type==2)
				 {
					 if(str_replace("'","",$all_jobs)!='')
					{
						$condition->job_no("in($all_jobs)");
					}
				}				
		?>
        <div style="width:750px;">
                <table width="750"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="8" class="form_caption">
                            <strong style="font-size:16px;">Company:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="8" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                    </table>
               
                <table width="750" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="150">Buyer</th>
                        <th width="90">Ex-Factory Qty(Pcs)</th>
                        <th width="90">Ex-Factory FOB Value(USD)</th>
                        <th width="90">Ex-Factory CM Cost(USD)</th>
                        <th width="90">Ex-Factory Margin(USD)</th>
                        <th width="90">Ex-Factory CM Cost With Margin(USD)</th>
                        <th width="">CM%</th>
                    </thead>
                </table>
            <div style="width:770px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
               <?
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
					//print_r($other_costing_arr);
  					$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_job();
					$trimsCostArr=$trim->getAmountArray_by_job();
					$emblishmentCostArr=$emblishment->getAmountArray_by_job();
					$washCostArr=$wash->getAmountArray_by_job();
					$commercialCostArr=$commercial->getAmountArray_by_job();
					$commisionCostArr=$commision->getAmountArray_by_job();
 
				
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	 foreach($po_wise_export_arr as $po_id=>$row)
				 {
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						$fabric_cost_knit_amount=($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						$conversion_cost_amount=($conversionCostArr[$row[('job_no')]]);
						$trims_cost_amount=$trimsCostArr[$row[('job_no')]];
						$emblishment_cost_amount=$emblishmentCostArr[$row[('job_no')]];
						$wash_cost_amount=$washCostArr[$row[('job_no')]];
						$commercial_cost_amount=$commercialCostArr[$row[('job_no')]];
						$commision_cost_amount=$commisionCostArr[$row[('job_no')]];
						$po_cm_cost_val=$other_costing_arr[$row[('job_no')]]['cm_cost'];
						$po_lab_test_val=$other_costing_arr[$row[('job_no')]]['lab_test'];
						$po_inspection_val=$other_costing_arr[$row[('job_no')]]['inspection'];
						$po_currier_cost_val=$other_costing_arr[$row[('job_no')]]['currier_pre_cost'];
						$po_design_cost_val=$other_costing_arr[$row[('job_no')]]['design_cost'];
						$po_studio_cost_val=$other_costing_arr[$row[('job_no')]]['studio_cost'];
						$po_freight_val=$other_costing_arr[$row[('job_no')]]['freight'];
						$po_common_oh_val=$other_costing_arr[$row[('job_no')]]['common_oh'];
						$po_depr_amor_pre_cost_val=$other_costing_arr[$row[('job_no')]]['depr_amor_pre_cost'];
						$po_certificate_pre_cost_val=$other_costing_arr[$row[('job_no')]]['certificate_pre_cost'];
						$totalOtherCost=($po_cm_cost_val+$po_lab_test_val+$po_inspection_val+$po_currier_cost_val+$po_design_cost_val+$po_studio_cost_val+$po_freight_val+$po_common_oh_val+$po_depr_amor_pre_cost_val+$po_certificate_pre_cost_val);
						$totalCost_second=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+$conversion_cost_amount+$trims_cost_amount+$emblishment_cost_amount+$wash_cost_amount+$commercial_cost_amount+$commision_cost_amount);
						
						$totalCost=($totalOtherCost+$totalCost_second);
						$qnty_unit_price_value_fob=$job_wise_export_arr[$row[('job_no')]]['job_quantity']*$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
						$ex_fact_mergin_new_price=($qnty_unit_price_value_fob-$totalCost)/$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
						$ex_fact_mergin_new=$row[('ex_fac_qty')]*$ex_fact_mergin_new_price;
						$job_no=$row[("job_no")];
						$costing_per=$job_wise_export_arr[$job_no]['costing_per'];
						$cm_cost=$job_wise_export_arr[$job_no]['cm_cost'];
						$order_value=$row['po_quantity']*$row['unit_price'];
						
					if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
					$other_value=$job_wise_export_arr[$job_no]['other_value'];
					if($other_value!=0)
					{
					$otherCost=$other_value/$order_price_per_dzn*$row[('po_quantity')]; 
					//$cmValue_mergin = $order_value-$otherCost; 
					}
					
					
					$cm_mergin_pcs=$ex_fact_mergin_new_price;
					$ex_fact_mergin=$row[('ex_fac_qty')]*$cm_mergin_pcs;
					
					
					$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
					
					$jobQntryPcs=$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
					$jobUnitPcs=$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
					$exFactory_CM_Cost_USD=$po_cm_cost/$jobQntryPcs;
					$ex_fac_qty_cm_cost_mergin=$ex_fact_mergin+($row[('ex_fac_qty')]*$cm_cost_pcs);
					
					
					$buyer_wise_arr[$row['buyer_name']]['ex_fac_qty']+=$row[('ex_fac_qty')];
					$buyer_wise_arr[$row['buyer_name']]['exfactory_fob_value']+=($row[('ex_fac_qty')]*$row['unit_price']);
					$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_CM_Cost_USD']+=($exFactory_CM_Cost_USD*$row[('ex_fac_qty')]);
					$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_Margin_USD']+=$ex_fact_mergin_new;
					$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_CM_Cost_With_Margin_USD']+=$ex_fac_qty_cm_cost_mergin;
					//echo number_format($ex_fac_qty_cm_cost_mergin,2);
				 }
					
				foreach($buyer_wise_arr as $buyer_id=>$buyer_data)
				{
					$short_excess_qty=0;
					$short_excess_value=0;
					$short_excess_qty=$buyer_data['short_excess'];
					$short_excess_value=$buyer_data['short_excess_value'];
					$exfactory_fob_value=$buyer_data['exfactory_fob_value'];
					$exfactory_margin_value=$buyer_data['exfactory_margin'];
					$exfactory_subcontact=$buyer_data['outbound'];
					
					$cm_percent=(($buyer_data['Ex_Factory_CM_Cost_With_Margin_USD']/$buyer_data['exfactory_fob_value'])*100);
					
			   ?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
							<td width="150"><? echo $buyer_arr[$buyer_id];; ?></td>
							<td width="90"  align="right" title="Ex-fac Qty"> <div style="word-break:break-all"> <? echo number_format($buyer_data['ex_fac_qty'],0); ?>   </div> </td>
							<td width="90" align="right" > <?  echo number_format($buyer_data['exfactory_fob_value'],4);?></td>
							<td width="90" align="right" title="CM Cost Per Pcs(USD) * Ex-Factory Qty"><div style="word-break:break-all"><? echo number_format($buyer_data['Ex_Factory_CM_Cost_USD'],4);?> </div></td>
                            <td width="90" align="right"><div style="word-break:break-all" title=""><? echo number_format($buyer_data['Ex_Factory_Margin_USD'],4); 
							 ?> </div></td> 
							<td width="90" align="right"><p> <? echo number_format($buyer_data['Ex_Factory_CM_Cost_With_Margin_USD'],4);   ?> </p>
							</td>
                            <td width="" align="right"><p>
							 <? echo number_format($cm_percent,4)."%";  ?>
                             </p>
							</td>
                   	</tr>
                            <?
							
							$total_ex_fac_qty+=$buyer_data['ex_fac_qty'];
							$total_ex_fac_val+=$buyer_data['exfactory_fob_value'];
							$ex_fac_cm_cost+=$buyer_data['Ex_Factory_CM_Cost_USD'];
							$total_ex_fac_mergin+=$buyer_data['Ex_Factory_Margin_USD'];
							$total_ex_fac_qty_cm_cost_mergin+=$buyer_data['Ex_Factory_CM_Cost_With_Margin_USD'];
							$i++;
					}
							?>
               </table>
            <table width="750" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="90" id="value_total_ex_fac_qty" align="right"><? echo number_format($total_ex_fac_qty,0); ?></th>
                        <th width="90" id="value_total_ex_fac_val" align="right"><? echo number_format($total_ex_fac_val); ?></th>
                        <th width="90" id="value_total_ex_fac_cm_cost" align="right"><? echo number_format($ex_fac_cm_cost); ?></th>
                        <th width="90" id="value_total_ex_fac_qty_mergin" align="right"><? echo number_format($total_ex_fac_mergin); ?></th>
                        <th  width="90" id="value_total_ex_fac_qty_cm_cost_mergin" align="right"><? echo number_format($total_ex_fac_qty_cm_cost_mergin); ?></th>
                        <th><? echo number_format(($total_ex_fac_qty_cm_cost_mergin*100)/$total_ex_fac_val,4)."%"; ?> </th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
              <table width="750">
            		<tr>
                        <td align="center"  class="form_caption">&nbsp;  </td>
                        
                    </tr>
                    <tr>
                         <td align="center"  class="form_caption">&nbsp;  </td>
                    </tr>
             </table>
            
            <table width="350"  cellspacing="0"  border="1" class="rpt_table" rules="all">
                    <tr>
                        <td align="left" width="200">TOTAL CM</td>
                        <td width="200" align="right"><? echo number_format($total_ex_fac_qty_cm_cost_mergin); ?> </td>
                    </tr>
                    <tr>
                        <td align="left">TOTAL EXPORT VALUE</td>
                        <td align="right"><? echo number_format($total_ex_fac_val);  ?> </td>
                    </tr>
                    <tr>
                        <td align="left">CM %</td>
                        <td align="right"><? echo number_format(($total_ex_fac_qty_cm_cost_mergin*100)/$total_ex_fac_val,4)."%"; ?> </td>
                    </tr>
              </table>
        </div>
		<?
	}
	
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
	echo "$total_data####$filename####$reportType";
	exit();
}


if($action=="report_generate_5")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_title=str_replace("'","",$report_title);
	$reportType=str_replace("'","",$reportType);
        $cbo_item_catgory=str_replace("'","",$cbo_item_catgory);
        
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
		$date_cond="";
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and a.ex_factory_date between '$start_date' and '$end_date'";
			
		}
                if($cbo_item_catgory != "")
                {
                    $cbo_item_cond = " and c.product_category in ($cbo_item_catgory) ";
                }
	
	ob_start();
	if($reportType==5)
	{
		
		$i=1;
		
		
		 $sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty	,
			max(a.ex_factory_date) as ex_factory_date,max(b.pub_shipment_date) as pub_shipment_date			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and d.entry_from=158 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		//echo $sql;
		
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		foreach($sql_result as $row)
		{
			$po_wise_export_arr[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];	
			$po_wise_export_arr[$row[csf("po_id")]]['job']=$row[csf("job_no_prefix_num")];
			$po_wise_export_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['style']=$row[csf("style_ref_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_fac_qty']=$row[csf("ex_factory_qnty")]-$row[csf("ret_ex_factory_qnty")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity']=$row[csf("po_quantity")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_factory_date']=$row[csf("ex_factory_date")];
			$po_wise_export_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
			
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
		
			$poIdArr[$row[csf("po_id")]]=$row[csf("po_id")];
			$jobArr[$row[csf("job_no")]]=$row[csf("job_no")];
		}
		
	
		
			$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$job_con =" and (b.job_no_mst in('".implode("','",$job_no_process)."')";} 
				else{$job_con .=" or b.job_no_mst in('".implode("','",$job_no_process)."')";}
				$p++;
			}
			$job_con .=")";
			
			
			$onlyJobQty_sql=sql_select("select b.id,b.job_no_mst, sum(b.po_quantity) as po_quantity, sum(c.total_set_qnty*b.po_quantity) as po_quantity_pcs,max(c.avg_unit_price/c.total_set_qnty) as unit_price,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1 $job_con   group by b.id,b.job_no_mst,c.avg_unit_price ");// and b.id in(".implode(',',$poIdArr).")
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']+=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
				$po_pcs_arr[$row[csf("id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];	
				$po_pcs_arr[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];	
			}
			//print_r($job_wise_export_arr['OG-19-00959']); die;
		
		
		$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set,b.incometax_cost,b.interest_cost from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0";
			$pre_result=sql_select($sql_pre);
			foreach($pre_result as $row)
			{
				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost']=$row[csf("incometax_cost")];	
				$job_wise_export_arr[$row[csf("job_no")]]['interest_cost']=$row[csf("interest_cost")];
			}
			
			
		unset($pre_result);
		 $all_job_no=array_unique(explode(",",$all_full_job));
			$all_jobs="";
			foreach($all_job_no as $jno)
			{
					if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
			}
	
	
	 ///print_r($all_jobs); die;
		
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
			
		if($db_type==0 || $db_type==2)
		 {
			if(str_replace("'","",$all_jobs)!='')
			{
				$condition->job_no("in($all_jobs)");
			}
			else{
				$condition->job_no("in('0')");
			}
		}				
		?>
        <div style="width:1250px;">
                <table width="1250"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="14" class="form_caption">
                            <strong style="font-size:16px;">Company:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="14" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                    </table>
               
                <table width="1410" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="150">Buyer</th>
                        <th width="130">Style Name</th>
                        <th width="80">Job No</th>
                        <th width="120">Order No</th>
                        
                        <th width="80">Pub shipment date</th>
                        <th width="80">Ex-factory date</th>
                        
                        <th width="100">Order Qty (Pcs)</th>
                        <th width="60">Unit Price(USD)</th>
                        <th width="80">CM Cost Per Pcs(USD)</th>
                        <th width="80">CM Margin Per Pcs(USD)</th>
                        <th width="80">Ex-Factory Qty(Pcs)</th>
                        <th width="80">Ex-Factory FOB Value(USD)</th>
                        <th width="80">Ex-Factory CM Cost(USD)</th>
                        <th width="80">Ex-Factory Margin(USD)</th>
                        <th width="">Ex-Factory CM Cost With Margin(USD)</th>
                    </thead>
                </table>
            <div style="width:1428px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
               <?
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
					//print_r($other_costing_arr);
  					$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_job();
					$trimsCostArr=$trim->getAmountArray_by_job();
					$emblishmentCostArr=$emblishment->getAmountArray_by_job();
					$washCostArr=$wash->getAmountArray_by_job();
					$commercialCostArr=$commercial->getAmountArray_by_job();
					$commisionCostArr=$commision->getAmountArray_by_job();
 
					
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	foreach($po_wise_export_arr as $po_id=>$row)
				 {
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						
						$fabric_cost_knit_amount=($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						
						$fabric_cost_woven_amount=($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=($conversionCostArr[$row[('job_no')]]);
						$trims_cost_amount=$trimsCostArr[$row[('job_no')]];
						$emblishment_cost_amount=$emblishmentCostArr[$row[('job_no')]];
						$wash_cost_amount=$washCostArr[$row[('job_no')]];
						$commercial_cost_amount=$commercialCostArr[$row[('job_no')]];
						$commision_cost_amount=$commisionCostArr[$row[('job_no')]];

						$po_cm_cost_val=$other_costing_arr[$row[('job_no')]]['cm_cost'];
						$po_lab_test_val=$other_costing_arr[$row[('job_no')]]['lab_test'];
						$po_inspection_val=$other_costing_arr[$row[('job_no')]]['inspection'];
						$po_currier_cost_val=$other_costing_arr[$row[('job_no')]]['currier_pre_cost'];
						$po_design_cost_val=$other_costing_arr[$row[('job_no')]]['design_cost'];
						$po_studio_cost_val=$other_costing_arr[$row[('job_no')]]['studio_cost'];
							//$interest_expense=$pre_cost_date_arr[$row[('job_no')]]['interest_expense']/100;
							//$income_tax=$pre_cost_date_arr[$row[('job_no')]]['income_tax']/100;
						$po_freight_val=$other_costing_arr[$row[('job_no')]]['freight'];
						$po_common_oh_val=$other_costing_arr[$row[('job_no')]]['common_oh'];
						$po_depr_amor_pre_cost_val=$other_costing_arr[$row[('job_no')]]['depr_amor_pre_cost'];
						$po_certificate_pre_cost_val=$other_costing_arr[$row[('job_no')]]['certificate_pre_cost'];
						
						$deffdlc_cost=$other_costing_arr[$row[('job_no')]]['deffdlc_cost'];
						$incometax_cost=$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost'];	
						$interest_cost=$job_wise_export_arr[$row[csf("job_no")]]['interest_cost'];
						
						
						//$totalOtherCost=($po_cm_cost_val+$po_lab_test_val+$po_inspection_val+$po_currier_cost_val+$po_design_cost_val+$po_studio_cost_val+$po_freight_val+$po_common_oh_val+$po_depr_amor_pre_cost_val+$po_certificate_pre_cost_val);
						
						//$totalCost_second=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+$conversion_cost_amount+$trims_cost_amount+$emblishment_cost_amount+$wash_cost_amount+$commercial_cost_amount+$commision_cost_amount);
						//$totalCost=($totalOtherCost+$totalCost_second);
						
						
						
						$totalCost=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);

						//echo $totalCost;die;
						
						

						$qnty_unit_price_value_fob=$job_wise_export_arr[$row[('job_no')]]['job_quantity']*$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];

						
						$ex_fact_mergin_new_price=($qnty_unit_price_value_fob-$totalCost)/$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
						$ex_fact_mergin_new=$row[('ex_fac_qty')]*$ex_fact_mergin_new_price;

						$job_no=$row[("job_no")];
						
						$costing_per=$job_wise_export_arr[$job_no]['costing_per'];
						$cm_cost=$job_wise_export_arr[$job_no]['cm_cost'];
						$order_value=$row['po_quantity']*$row['unit_price'];
						
					if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
					$other_value=$job_wise_export_arr[$job_no]['other_value'];
					if($other_value!=0)
					{
					$otherCost=$other_value/$order_price_per_dzn*$row[('po_quantity')]; 
					//$cmValue_mergin = $order_value-$otherCost; 
					}
					$cm_mergin_pcs=$ex_fact_mergin_new_price;
					$ex_fact_mergin=$row[('ex_fac_qty')]*$cm_mergin_pcs;
					$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
					
					$jobQntryPcs=$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
					$jobUnitPcs=$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
					//$exFactory_CM_Cost_USD=$po_cm_cost/$row[('ex_fac_qty')]*$poQntryPcs;
					$exFactory_CM_Cost_USD=$po_cm_cost/$jobQntryPcs;
					
					$po_cm_cost=$job_wise_export_arr[$row[("job_no")]]['cm_cost']; 
					$exFactory_CM_Cost_USD_2=$po_cm_cost/$order_price_per_dzn/$row['total_set_qnty'];
					
			   ?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="150"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
							<td width="130" align="center"><p><? echo $row[('style')]; ?></p></td>
							<td width="80"><p title="<? echo $row[('job')]; ?>"><? echo $row[('job_no')]; ?></p></td>
							<td width="120"><div style="word-break:break-all"><? echo $row[('po_number')]; ?></div></td>
                            <td width="80" align="center"><? echo change_date_format($row['pub_shipment_date']);?></td>
                            <td width="80" align="center"><? echo change_date_format($row['ex_factory_date']);?></td>
                            
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($po_pcs_arr[$po_id]['po_quantity_pcs']); ?></div></td>
							<td width="60"  align="right"><p><? echo number_format($po_pcs_arr[$po_id]['unit_price'],4); ?></p></td>
							<td width="80" align="right" title=""><div style="word-wrap:break-all"><? $cm_cost_per_pcs=$cm_cost_pcs;echo number_format($exFactory_CM_Cost_USD_2,4); ?></div></td>
                            <td width="80"align="right"  title="<?  echo 'Total Value:('.$qnty_unit_price_value_fob.'- total cost: '.$totalCost.')/ Total PCS Qty: '.$job_wise_export_arr[$row[('job_no')]]['job_quantity'];?>">
                            <div style="word-break:break-all"><? echo number_format($cm_mergin_pcs/$row['total_set_qnty'],4); ?></div></td>
							<td width="80"  align="right" title="Ex-fac Qty"> <div style="word-break:break-all"> <? echo number_format($row[('ex_fac_qty')],0);
							$total_ex_fac_qty+=$row[('ex_fac_qty')];
							
							 ?>   </div> </td>
							<td width="80" align="right" title="<?= $row[('ex_fac_qty')].'*'.$po_pcs_arr[$po_id]['unit_price'];?>" > <?  echo number_format($row[('ex_fac_qty')]*$po_pcs_arr[$po_id]['unit_price'],2); 
							
							$total_ex_fac_val+=$row[('ex_fac_qty')]*$po_pcs_arr[$po_id]['unit_price'];
							?></td>
							<td width="80" align="right" title="CM Cost Per Pcs(USD) * Ex-Factory Qty"><div style="word-break:break-all"><? echo number_format($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2,2);
							$total_ex_fac_cm_cost+=$row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2;
							
							?> </div></td>
                            
                            <td width="80" align="right" title="<?=$row[('ex_fac_qty')].'*'.($cm_mergin_pcs/$row['total_set_qnty']);?>"><p><?
							
							$ex_fac_qty_cm_cost_mergin=$row[('ex_fac_qty')]*($cm_mergin_pcs/$row['total_set_qnty']);
							 //echo number_format($ex_fact_mergin,2);
							echo number_format($ex_fac_qty_cm_cost_mergin,2); 
							$total_ex_fac_mergin+=$ex_fac_qty_cm_cost_mergin;
							 ?> </p></td> 
                             
							<td width="" align="right"><p>
							 <? echo number_format($ex_fac_qty_cm_cost_mergin+($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2),2);
							 
							 $TotalExFactoryCMCostWithMarginUSD+=$ex_fac_qty_cm_cost_mergin+($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2);
							 ?>
                             </p>
							</td>
                            
                   	</tr>
                            <?
							$total_po_qty_pcs+=$po_pcs_arr[$po_id]['po_quantity_pcs'];
							//$total_ex_fac_qty+=$row[('ex_fac_qty')];
							//$total_ex_fac_val+=$row[('ex_fac_qty')]*$row[('unit_price')];
							//$ex_fac_cm_cost+=$exFactory_CM_Cost_USD*$row[('ex_fac_qty')];
							
							
							
							//$total_ex_fac_qty_cm_cost_mergin+=$ex_fac_qty_cm_cost_mergin;
							
							$i++;
					}
							?>
               </table>
            <table width="1410" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100"><? echo number_format($total_po_qty_pcs,0); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80"><? echo number_format($total_ex_fac_qty,0); ?></th>
                        <th width="80"><? echo number_format($total_ex_fac_val); ?></th>
                        <th width="80"><? echo number_format($total_ex_fac_cm_cost); ?></th>
                        <th width="80"><? echo number_format($total_ex_fac_mergin); ?></th>
                        <th><? echo number_format($TotalExFactoryCMCostWithMarginUSD); ?></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>
	   
		<?
	}
	
	
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
	echo "$total_data####$filename####$reportType";
	exit();
}




if($action=="ex_date_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	//echo $ex_factory_date."***".$company_id."***".$order_id;
	$country_arr=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Date Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Date</th>
                        <th width="100">Challan</th>
                        <th width="100">Country</th>
                        <th width="100">Delv. Qty</th>
                        <th width="">Return Qty</th>
                     </tr>   
                </thead>
                <tbody>	 	
					<?
						$sql_res=sql_select("select b.po_break_down_id as po_id, 
						
						sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty 
						from  pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{
						
							$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
						}
						
						$i=1;
						if($ex_factory_date_ref[1]==2)
						{ 
							$sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id, 
							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
							sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty
							
							 from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0 group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";
						}
						else
						{
							 $sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id, 
							
							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
							
							from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";
							
							/*$sql_qnty="Select c.ex_factory_date, sum(c.ex_factory_qnty) as ex_factory_qnty,c.challan_no,c.country_id 
							from wo_po_details_master a, wo_po_break_down b,  pro_ex_factory_mst c
							where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and c.po_break_down_id=$order_id and c.status_active=1 and c.is_deleted=0 and c.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' 
							group by c.ex_factory_date,c.challan_no,c.country_id order by c.ex_factory_date ";*/
						}
						//echo $sql_qnty;
						$sql_dtls=sql_select($sql_qnty);
						foreach($sql_dtls as $row_real)
						{ 
							 if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
							 if($ex_factory_date_ref[1]==2)
							 {
								 $return_qty=$row_real[csf("ex_factory_ret_qnty")];
							 }
							 else
							 {
								$return_qty=$ex_factory_qty_arr[$row_real[csf("po_break_down_id")]]['return_qty']; 
							 }
							  
							
							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td><? echo $i; ?></td> 
									<td  align="center"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                                    <td ><? echo $row_real[csf("challan_no")]; ?></td>
                                    <td ><? echo $country_arr[$row_real[csf("country_id")]]; ?></td>
									<td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")]-$return_qty,2); ?>&nbsp;</td>
                                    <td width="" align="right"><? echo number_format($return_qty,2); ?>&nbsp;</td>
								</tr>
							<? 
							$total_ex_qnty+=$row_real[csf("ex_factory_qnty")];
							$total_return_ex_qnty+=$return_qty;
							$i++;
						}
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="4" align="right"><strong>Total :</strong></th>
                        <th align="right"><? echo number_format($total_ex_qnty,2); ?></th>
                        <th align="right"><? echo number_format($total_return_ex_qnty,2); ?> </th>
                    </tr>
                    <tr>
                    	<th colspan="4" align="right"><strong>Total Balance:</strong></th>
                        <th align="right" colspan="2"><? echo number_format($total_ex_qnty-$total_return_ex_qnty,2); ?></th>
                        
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>    
    <?	
}
disconnect($con);
?>
