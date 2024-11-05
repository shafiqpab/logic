<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.commisions.php');

error_reporting(1);
ini_set('display_errors',1);
ini_set('memory_limit','8056M');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate________")
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
		
			/*$onlyJobQty_sql=sql_select("select b.id, sum(b.po_quantity*c.TOTAL_SET_QNTY) as po_quantity,(c.avg_unit_price/c.TOTAL_SET_QNTY) as avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1  group by b.id,c.avg_unit_price,c.TOTAL_SET_QNTY ");
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("id")]]['po_quantity']=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("id")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
			}*/
		
		
		
		 $sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty	,
			max(a.ex_factory_date) as ex_factory_date,max(b.pub_shipment_date) as pub_shipment_date			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and d.entry_from=158 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		
		
		//echo $sql; die;
		
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
					
					/*$fabricCostArr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getOrderWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_order();
					$trimsCostArr=$trim->getAmountArray_by_order();
					$emblishmentCostArr=$emblishment->getAmountArray_by_order();
					$washCostArr=$wash->getAmountArray_by_order();
					$commercialCostArr=$commercial->getAmountArray_by_order();
					$commisionCostArr=$commision->getAmountArray_by_order();*/

					
					//print_r($other_costing_arr);
					//$other_costing_arr=$other->getAmountArray_by_job();
  					//$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
					//$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
					//$conversionCostArr=$conversion->getAmountArray_by_job();
					//$trimsCostArr=$trim->getAmountArray_by_job();
					//$emblishmentCostArr=$emblishment->getAmountArray_by_job();
					//$washCostArr=$wash->getAmountArray_by_job();
					//$commercialCostArr=$commercial->getAmountArray_by_job();
					//$commisionCostArr=$commision->getAmountArray_by_job();
 
					
					//---------------
					$other_costing_arr=$other->getAmountArray_by_order();
					$fabricCostArr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
					$yarnCostArr=$yarn->getOrderWiseYarnAmountArray();
					$conversionCostArr=$conversion->getAmountArray_by_order();
					$trimsCostArr=$trim->getAmountArray_by_order();
					$emblishmentCostArr=$emblishment->getAmountArray_by_order();
					$washCostArr=$wash->getAmountArray_by_order();
					$commercialCostArr=$commercial->getAmountArray_by_order();
					$commisionCostArr=$commision->getAmountArray_by_order();					
					//-------------------
					
					
					
					//print_r($commisionCostArr);
					/*$other_costing_arr=$conversion->getAmountArray_by_order();
					$other_costing_arr=$trim->getAmountArray_by_order();
					
					$other_costing_arr=$emblishment->getAmountArray_by_order();
					$other_costing_arr=$wash->getAmountArray_by_order();
					$other_costing_arr=$commercial->getAmountArray_by_order();
					$other_costing_arr=$commision->getAmountArray_by_order();*/
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	foreach($po_wise_export_arr as $po_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_cm_cost=$job_wise_export_arr[$row[("job_no")]]['cm_cost']; // total cm cost from class 4
					$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$po_id]);
					$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$po_id]);
					$yarn_cost_amount=$yarnCostArr[$po_id];
					//*$po_wise_export_arr[$po_id]['total_set_qnty']
					$conversion_cost_amount=array_sum($conversionCostArr[$po_id]);
					$trims_cost_amount=$trimsCostArr[$po_id];
					$emblishment_cost_amount=$emblishmentCostArr[$po_id];
					$wash_cost_amount=$washCostArr[$po_id];
					$commercial_cost_amount=$commercialCostArr[$po_id];
					$commision_cost_amount=$commisionCostArr[$po_id];

					$po_cm_cost_val=$other_costing_arr[$po_id]['cm_cost'];
					$po_lab_test_val=$other_costing_arr[$po_id]['lab_test'];
					$po_inspection_val=$other_costing_arr[$po_id]['inspection'];
					$po_currier_cost_val=$other_costing_arr[$po_id]['currier_pre_cost'];
					$po_design_cost_val=$other_costing_arr[$po_id]['design_cost'];
					$po_studio_cost_val=$other_costing_arr[$po_id]['studio_cost'];
						//$interest_expense=$pre_cost_date_arr[$po_id]['interest_expense']/100;
						//$income_tax=$pre_cost_date_arr[$po_id]['income_tax']/100;
					$po_freight_val=$other_costing_arr[$po_id]['freight'];
					$po_common_oh_val=$other_costing_arr[$po_id]['common_oh'];
					$po_depr_amor_pre_cost_val=$other_costing_arr[$po_id]['depr_amor_pre_cost'];
					$po_certificate_pre_cost_val=$other_costing_arr[$po_id]['certificate_pre_cost'];
					
					$totalOtherCost=($po_cm_cost_val+$po_lab_test_val+$po_inspection_val+$po_currier_cost_val+$po_design_cost_val+$po_studio_cost_val+$po_freight_val+$po_common_oh_val+$po_depr_amor_pre_cost_val+$po_certificate_pre_cost_val);
					
					$totalCost_second=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+$conversion_cost_amount+$trims_cost_amount+$emblishment_cost_amount+$wash_cost_amount+$commercial_cost_amount+$commision_cost_amount);
					
					
					
					$totalCost=($totalOtherCost+$totalCost_second);
					$qnty_unit_price_value_fob=$row[('po_quantity_pcs')]*$row[('unit_price')];
					//$ex_fact_mergin_new_price=($qnty_unit_price_value_fob-$totalCost)/$row[('po_quantity_pcs')];
					
					$ex_fact_mergin_new_price=($qnty_unit_price_value_fob-$totalCost)/$row[('po_quantity_pcs')];
					
					$ex_fact_mergin_new=$row[('ex_fac_qty')]*$ex_fact_mergin_new_price;
					$job_no=$row[("job_no")];
					$costing_per=$job_wise_export_arr[$job_no]['costing_per'];
					$cm_cost=$job_wise_export_arr[$job_no]['cm_cost'];
					$order_value=$row['po_quantity_pcs']*$row['unit_price'];
						
					if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
					$other_value=$job_wise_export_arr[$job_no]['other_value'];
					if($other_value!=0)
					{
						$otherCost=$other_value/$order_price_per_dzn*$row[('po_quantity_pcs')]; 
						//$cmValue_mergin = $order_value-$otherCost; 
					}
					$cm_mergin_pcs=$ex_fact_mergin_new_price;
					$ex_fact_mergin=$row[('ex_fac_qty')]*$cm_mergin_pcs;
					$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
					
					$jobQntryPcs=$row['po_quantity_pcs'];
					$jobUnitPcs=$row[('unit_price')];
					//$exFactory_CM_Cost_USD=$po_cm_cost/$row[('ex_fac_qty')]*$poQntryPcs;
					//$exFactory_CM_Cost_USD=$po_cm_cost/$jobQntryPcs;
					$exFactory_CM_Cost_USD=$po_cm_cost/$order_price_per_dzn/$row['total_set_qnty'];
			   ?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="150"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
							<td width="130" align="center"><p><? echo $row[('style')]; ?></p></td>
							<td width="80"><p title="<? echo $row[('job')]; ?>"><? echo $row[('job_no')]; ?></p></td>
							<td width="120"><div style="word-break:break-all"><? echo $row[('po_number')]; ?></div></td>
                            <td width="80" align="center"><? echo change_date_format($row['pub_shipment_date']);?></td>
                            <td width="80" align="center"><? echo change_date_format($row['ex_factory_date']);?></td>
                            
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($jobQntryPcs,0); ?></div></td>
							<td width="60"  align="right"><p><? echo number_format($jobUnitPcs,2); ?></p></td>
							<td width="80" align="right" title="Budget page CM/Costing Per/Set Ratio"><div style="word-wrap:break-all"><? $cm_cost_per_pcs=$cm_cost_pcs;echo number_format($exFactory_CM_Cost_USD,4); ?></div></td>
                            <td width="80"align="right"  title="Margin perSet=<? echo $cm_mergin_pcs;?> / Set Ratio"><div style="word-break:break-all"><? echo number_format($cm_mergin_pcs,4); ?></div></td>
							<td width="80"  align="right" title="Ex-fac Qty"> <div style="word-break:break-all"> <? echo number_format($row[('ex_fac_qty')],0); ?>   </div> </td>
							<td width="80" align="right" > <?  echo number_format($row[('ex_fac_qty')]*$row[('unit_price')],2); ?></td>
							<td width="80" align="right" title="CM Cost Per Pcs(USD) * Ex-Factory Qty"><div style="word-break:break-all"><? echo number_format($exFactory_CM_Cost_USD*$row[('ex_fac_qty')],2);?> </div></td>
                            
                            <td width="80" align="right"><div style="word-break:break-all" title="<? echo 'totalCost: '.$totalCost.'; ='.'((totalValue-totalCost)/poQuantityPcs)*exFacQty'; ?>"><?
							//$ex_fac_qty_cm_cost_mergin=$ex_fact_mergin+($row[('ex_fac_qty')]*$cm_cost_per_pcs);
							 //echo number_format($ex_fact_mergin,2);
							echo number_format($ex_fact_mergin_new,2); 
							 ?> </div>
                             </td> 
                             
							<td width="" align="right" title="Ex-Factory CM Cost(USD) + Ex-Factory Margin(USD)"><p>
							 <? 
							 $ex_fac_qty_cm_cost_mergin=$ex_fact_mergin_new+($exFactory_CM_Cost_USD*$row[('ex_fac_qty')]);
							 
							  echo number_format($ex_fac_qty_cm_cost_mergin,2);
							 ?>
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
						$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						$conversion_cost_amount=array_sum($conversionCostArr[$row[('job_no')]]);
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
							 <? echo number_format($cm_percent,2)."%";  ?>
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
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and d.entry_from=158 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by max(a.ex_factory_date)";
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
						
						$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=array_sum($conversionCostArr[$row[('job_no')]]);
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
		
			/*$onlyJobQty_sql=sql_select("select b.job_no_mst, sum(b.po_quantity) as po_quantity,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1  group by b.job_no_mst,c.avg_unit_price ");
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
			}*/
			
			
			
			
			//print_r($job_wise_export_arr);
			 $sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and d.entry_from=158 and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		
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
                        <th>CM%</th>
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
/*						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						$conversion_cost_amount=array_sum($conversionCostArr[$row[('job_no')]]);
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

*/	

						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						
						$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=array_sum($conversionCostArr[$row[('job_no')]]);
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

					$buyer_wise_arr[$row['buyer_name']]['ExFactoryCMCostWithMarginUSD']=$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_Margin_USD']+$buyer_wise_arr[$row['buyer_name']]['Ex_Factory_CM_Cost_USD'];
					$buyer_wise_arr[$row['buyer_name']]['cm_percent']=(($buyer_wise_arr[$row['buyer_name']]['ExFactoryCMCostWithMarginUSD']/$buyer_wise_arr[$row['buyer_name']]['exfactory_fob_value'])*100);
					
					$buyer_wise_arr[$row['buyer_name']]['po_number_arr'][] = $row[('po_number')];
					$buyer_wise_arr[$row['buyer_name']]['style_arr'][] = $row[('style')];
					
					//echo number_format($ex_fac_qty_cm_cost_mergin,2);
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
							<td width="150"><? echo $buyer_arr[$buyer_id]; ?></td>
							<td width="50" align="center"><?php echo $style_count; ?></td>
							<td width="50" align="center"><?php echo $po_count; ?></td>
							<td width="90"  align="right" title="Ex-fac Qty"> <div style="word-break:break-all"> <? echo number_format($buyer_data['ex_fac_qty'],0); ?>   </div> </td>
							<td width="90" align="right" > <?  echo number_format($buyer_data['exfactory_fob_value'],4);?></td>
							<td width="90" align="right" title="CM Cost Per Pcs(USD) * Ex-Factory Qty"><div style="word-break:break-all"><? echo number_format($buyer_data['Ex_Factory_CM_Cost_USD'],4);?> </div></td>
                            <td width="90" align="right"><div style="word-break:break-all" title="">
								<?php
									echo number_format($buyer_data['Ex_Factory_Margin_USD'],4); 
							 	?> 
                             </div></td> 
							<td width="90" align="right"><p> <? 
								// $ExFactoryCMCostWithMarginUSD=$buyer_data['Ex_Factory_Margin_USD']+$buyer_data['Ex_Factory_CM_Cost_USD'];
								echo number_format($buyer_data['ExFactoryCMCostWithMarginUSD'],4);// $buyer_data['Ex_Factory_CM_Cost_With_Margin_USD']  ?> </p>
							</td>
                            <td width="" align="right"><p>
							 <?php 
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
							//$total_ex_fac_qty_cm_cost_mergin+=$buyer_data['Ex_Factory_CM_Cost_With_Margin_USD'];
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
			 /*$sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			sum(distinct b.po_quantity) as po_quantity,
			sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";*/
		
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
 
					//print_r($commisionCostArr);
					/*$other_costing_arr=$conversion->getAmountArray_by_order();
					$other_costing_arr=$trim->getAmountArray_by_order();
					
					$other_costing_arr=$emblishment->getAmountArray_by_order();
					$other_costing_arr=$wash->getAmountArray_by_order();
					$other_costing_arr=$commercial->getAmountArray_by_order();
					$other_costing_arr=$commision->getAmountArray_by_order();*/
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	foreach($po_wise_export_arr as $po_id=>$row)
				 {
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=array_sum($conversionCostArr[$row[('job_no')]]);
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
						
						/*echo 'po_cm_cost_val='.$po_cm_cost_val.'<br/>';
						echo 'po_lab_test_val='.$po_lab_test_val.'<br/>';
						echo 'po_inspection_val='.$po_inspection_val.'<br/>';
						echo 'po_freight_val='.$po_freight_val.'<br/>';
						echo 'po_common_oh_val='.$po_common_oh_val.'<br/>';
						echo 'po_depr_amor_pre_cost_val='.$po_depr_amor_pre_cost_val.'<br/>';
						echo 'po_certificate_pre_cost_val='.$po_certificate_pre_cost_val.'<br/>';
						echo 'fabric_cost_knit_amount='.$fabric_cost_knit_amount.'<br/>';
						echo 'fabric_cost_woven_amount='.$fabric_cost_woven_amount.'<br/>';
						echo 'yarn_cost_amount='.$yarn_cost_amount.'<br/>';
						echo 'conversion_cost_amount='.$conversion_cost_amount.'<br/>';
						echo 'trims_cost_amount='.$trims_cost_amount.'<br/>';
						echo 'emblishment_cost_amount='.$emblishment_cost_amount.'<br/>';
						echo 'wash_cost_amount='.$wash_cost_amount.'<br/>';
						echo 'commercial_cost_amount='.$commercial_cost_amount.'<br/>';
						echo 'commision_cost_amount='.$commision_cost_amount.'<br/>';
						echo 'po_currier_cost_val='.$po_currier_cost_val.'<br/>';
						echo 'po_design_cost_val='.$po_design_cost_val.'<br/>';
						echo 'po_studio_cost_val='.$po_studio_cost_val.'<br/>';*/
						
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
							<td width="130" align="center"><p><? echo $row[('style')]; ?></p></td>
							<td width="80"><p title="<? echo $row[('job')]; ?>"><? echo $row[('job_no')]; ?></p></td>
							<td width="120"><div style="word-break:break-all"><? echo $row[('po_number')]; ?></div></td>
                            <td width="80" align="center"><? echo change_date_format($row['pub_shipment_date']);?></td>
                            <td width="80" align="center"><? echo change_date_format($row['ex_factory_date']);?></td>
                            
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
						$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						$conversion_cost_amount=array_sum($conversionCostArr[$row[('job_no')]]);
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
							 <? echo number_format($cm_percent,2)."%";  ?>
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
		
		 $sql= "select c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,c.id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
			max(a.ex_factory_date) as ex_factory_date		
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.shiping_status = 3 and d.entry_from=158 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.job_no,c.id,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by max(a.ex_factory_date)";
		 //echo $sql;c.buyer_name,
		
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$jobIdArr[$row[csf("id")]]=$row[csf("id")];
		}
		
		$maxDateChkRes = sql_select("select a.job_no,a.id as job_id, 
		sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
		max(c.ex_factory_date) ex_factory_date
		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no = b.job_no_mst and b.id = c.po_break_down_id and b.status_active = 1 and c.status_active=1 and b.shiping_status = 3 and a.id in (".implode(',',$jobIdArr).") group by a.job_no,a.id");
		foreach ($maxDateChkRes as $md)
		{
			$maxDateChkArr[$md[csf("job_no")]]["ex_date"]= $md[csf("ex_factory_date")];
			$maxDateChkArr[$md[csf("job_no")]]["ex_qty"] = $md[csf("ex_factory_qnty")]-$md[csf("ret_ex_factory_qnty")];
		}		
		
		
		
		//print_r($sql_result);die;
		$jobArr=array(0);
		foreach($sql_result as $row)
		{
			if(strtotime($maxDateChkArr[$row[csf("job_no")]]["ex_date"]) <= strtotime($txt_date_to))
			{
				$po_wise_export_arr[$row[csf("job_no")]]['job']=$row[csf("job_no_prefix_num")];
				$po_wise_export_arr[$row[csf("job_no")]]['job_no']=$row[csf("job_no")];	
				$po_wise_export_arr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];	
				$po_wise_export_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
				$po_wise_export_arr[$row[csf("job_no")]]['ex_fac_qty']=$maxDateChkArr[$row[csf("job_no")]]["ex_qty"];	
				$po_wise_export_arr[$row[csf("job_no")]]['ex_factory_date']=$row[csf("ex_factory_date")];
				$jobArr[$row[csf("job_no")]]=$row[csf("job_no")];
			}

		}
		
		$sqlProLog="select JOBNO, max(PRODUCTION_DATE) as PRODUCTION_DATE,sum(AVAILABLE_MIN) as AVAILABLE_MIN   from PRODUCTION_LOGICSOFT where 1=1  ".where_con_using_array($jobArr,1,'JOBNO')." group by JOBNO ORDER BY PRODUCTION_DATE";
		//echo $sqlProLog;die;
		$sqlProLogRes=sql_select($sqlProLog);
		foreach($sqlProLogRes as $row)
		{
			$proLogDataArr[AVL_MIN][$row[JOBNO]]=$row[AVAILABLE_MIN];
			$proLogDataArr[PRO_DATE][]=$row[PRODUCTION_DATE];
			$proLogDataArr[PRO_DATE_BY_JOB][$row[JOBNO]]=date("d-m-Y",strtotime($row[PRODUCTION_DATE]));
		}
		
		//print_r($proLogDataArr[PRO_DATE]);
		
		$sqlCpm="select COMPANY_ID,APPLYING_PERIOD_DATE,APPLYING_PERIOD_TO_DATE,COST_PER_MINUTE  from LIB_STANDARD_CM_ENTRY where COMPANY_ID=$cbo_company_name ";//and APPLYING_PERIOD_DATE >= '{$proLogDataArr[PRO_DATE][0]}' and APPLYING_PERIOD_TO_DATE<= '".end($proLogDataArr[PRO_DATE])."'
		$sqlCpmRes=sql_select($sqlCpm);
		foreach($sqlCpmRes as $row)
		{
			$tot_month = datediff( 'd', $row[APPLYING_PERIOD_DATE],$row[APPLYING_PERIOD_TO_DATE]);
			for($i=0; $i<= $tot_month; $i++ )
			{
				$next_month=add_date($row[APPLYING_PERIOD_DATE],$i);
				$dateKey=date("d-m-Y",strtotime($next_month));
				$cpmDataArr[$dateKey]=$row[COST_PER_MINUTE];
			}
 		}
		
		$conversion_rate=return_field_value("CONVERSION_RATE","CURRENCY_CONVERSION_RATE", "id=(select max(id) from CURRENCY_CONVERSION_RATE where CURRENCY=2 and COMPANY_ID=$cbo_company_name)","CONVERSION_RATE");

		
			
		
		$job_no_list_arr=array_chunk($jobArr,999);
		$p=1;
		foreach($job_no_list_arr as $job_no_process)
		{
			if($p==1){
				$job_con =" and (b.job_no_mst in('".implode("','",$job_no_process)."')";
			} 
			else{
				$job_con .=" or b.job_no_mst in('".implode("','",$job_no_process)."')";
			}
			$p++;
		}
		$job_con .=")";
			
			
			
		$sql_order="SELECT b.JOB_NO_MST, 
		COUNT(CASE WHEN b.SHIPING_STATUS = 3 THEN b.id END) AS TOTAL_FULL_SHIP,
		COUNT(CASE WHEN b.SHIPING_STATUS in (1,2, 3) THEN b.id END) AS TOTAL_PO
		FROM wo_po_break_down b
		WHERE  b.is_deleted = 0 AND b.status_active = 1  $job_con  
		group by b.job_no_mst";  //echo $sql_order;die;
		$sql_order_result=sql_select($sql_order);
		//$fullShipJo=array();
		foreach($sql_order_result as $row)
		{
			if(($row[TOTAL_PO]-$row[TOTAL_FULL_SHIP]) < 1){
				// $fullShipJo[$row[JOB_NO_MST]]=$row[JOB_NO_MST];
				 
			}
			else
			{
				unset($po_wise_export_arr[$row[JOB_NO_MST]]);
				unset($jobArr[$row[JOB_NO_MST]]);
			}
		
		}
		
		
	//print_r($fullShipJo);die;		
			$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){
					$job_con =" and (b.job_no_mst in('".implode("','",$job_no_process)."')";
					$job_con2 =" and (b.job_no in('".implode("','",$job_no_process)."')";
				} 
				else{
					$job_con .=" or b.job_no_mst in('".implode("','",$job_no_process)."')";
					$job_con2 .=" or b.job_no in('".implode("','",$job_no_process)."')";
				}
				$p++;
			}
			$job_con .=")";
			$job_con2 .=")";
			
			$onlyJobQty_sql=sql_select("select b.id,b.job_no_mst,c.quotation_id, sum(b.po_quantity) as po_quantity, sum(c.total_set_qnty*b.po_quantity) as po_quantity_pcs,max(b.UNIT_PRICE/c.total_set_qnty) as unit_price,c.avg_unit_price,c.total_set_qnty from wo_po_break_down b ,wo_po_details_master c  
			where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1 $job_con   group by b.id,b.job_no_mst,c.avg_unit_price,c.total_set_qnty,c.quotation_id ");// and b.id in(".implode(',',$poIdArr).")
			
			$job_wise_export_arr=array();
			foreach($onlyJobQty_sql as $row)
			{
				if($row[csf('quotation_id')]){
					$quotationIdArr[$row[csf("job_no_mst")]]=$row[csf('quotation_id')];
				}
				
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']+=$row[csf("po_quantity")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity_pcs']+=$row[csf("po_quantity_pcs")];	
				$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];			
				$job_wise_export_arr[$row[csf("job_no_mst")]]['unit_price']=$row[csf("unit_price")];			
				$job_wise_export_arr[$row[csf("job_no_mst")]]['total_set_qnty']=$row[csf("total_set_qnty")];	
				//$po_pcs_arr[$row[csf("id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];	
				//$po_pcs_arr[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];
				
				$jobDataArr[po_id][$row[csf("id")]]=$row[csf("id")];
				$poWiseJobDataArr[$row[csf("id")]]=$row[csf("job_no_mst")];
				
					
			}
		 //print_r($quotationIdArr); die;
		
		
		
		
		$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set,b.incometax_cost,b.interest_cost from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 $job_con2";
			//echo $sql_pre;die;
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
		 
	
	
	 $all_jobs=implode("','",$jobArr);
		
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
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
	//$other_costing_arr=$other->getAmountArray_by_job();
	
	
	$fabricCostArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
	$conversionCostArr=$conversion->getAmountArray_by_job();
	$trimsCostArr=$trim->getAmountArray_by_job();
	$emblishmentCostArr=$emblishment->getAmountArray_by_job();
	$washCostArr=$wash->getAmountArray_by_job();
	$commercialCostArr=$commercial->getAmountArray_by_job();
	$commisionCostArr=$commision->getAmountArray_by_job();
		
		
	
	//print_r($other_costing_arr);die;
	
		
	//Actual Matarial Cost................................Start;
	
	$exchange_rate_arr=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst",'job_no','exchange_rate');
	//$quotationIdArr[$row[csf("job_no_mst")]]
	
			if(count($quotationIdArr)>0){
			$quaOfferQnty=array(); $quaConfirmPrice=array(); $quaConfirmPriceDzn=array(); $quaPriceWithCommnPcs=array(); $quaCostingPer=array(); $quaCostingPerQty=array();$quaCostingPerQty=array();
			$sqlQua="select a.id,a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id in(".implode(',',$quotationIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			//echo $sqlQua;die;
			$dataQua=sql_select($sqlQua);
			foreach($dataQua as $rowQua){
				$quaOfferQnty[$rowQua[csf('id')]]=$rowQua[csf('offer_qnty')];
				$quaConfirmPrice[$rowQua[csf('id')]]=$rowQua[csf('confirm_price')];
				$quaConfirmPriceDzn[$rowQua[csf('id')]]=$rowQua[csf('confirm_price_dzn')];
				$quaPriceWithCommnPcs[$rowQua[csf('id')]]=$rowQua[csf('price_with_commn_pcs')];
				$quaCostingPer[$rowQua[csf('id')]]=$rowQua[csf('costing_per')];
				
				if($quaCostingPer[$rowQua[csf('id')]]==1) $quaCostingPerQty[$rowQua[csf('id')]]=12;
				if($quaCostingPer[$rowQua[csf('id')]]==2) $quaCostingPerQty[$rowQua[csf('id')]]=1;
				if($quaCostingPer[$rowQua[csf('id')]]==3) $quaCostingPerQty[$rowQua[csf('id')]]=24;
				if($quaCostingPer[$rowQua[csf('id')]]==4) $quaCostingPerQty[$rowQua[csf('id')]]=36;
				if($quaCostingPer[$rowQua[csf('id')]]==5) $quaCostingPerQty[$rowQua[csf('id')]]=48;
			}
		}
		
		
		
			$po_no_list_arr=array_chunk($jobDataArr[po_id],999);
			$p=1;
			foreach($po_no_list_arr as $po_no_process)
			{
				if($p==1){
					$po_con_a =" and (a.po_breakdown_id in(".implode(",",$po_no_process).")";
					$po_con_b =" and (b.po_breakdown_id in(".implode(",",$po_no_process).")";
					$po_con_b2 =" and (b.order_id in(".implode(",",$po_no_process).")";
					$po_con_b3 =" and (b.po_break_down_id in(".implode(",",$po_no_process).")";
				} 
				else{
					$po_con_a .=" or a.po_breakdown_id in(".implode(",",$po_no_process).")";
					$po_con_b .=" or b.po_breakdown_id in(".implode(",",$po_no_process).")";
					$po_con_b2 .=" or b.order_id in(".implode(",",$po_no_process).")";
					$po_con_b3 .=" or b.po_break_down_id in(".implode(",",$po_no_process).")";
				}
				$p++;
			}
			$po_con_a .=")";
			$po_con_b .=")";
			$po_con_b2 .=")";
			$po_con_b3 .=")";
	
		
		
		//Yarn....................
	 $issue_return=sql_select("select a.po_breakdown_id,a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity as quantity,a.issue_purpose,a.returnable_qnty, a.is_sales, b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,(a.quantity*c.cons_rate) as cons_amount,c.order_amount,c.issue_id,d.booking_no,c.receive_basis from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.issue_id=d.id and a.trans_type in(4,5,6) and a.entry_form in(9,11) $po_con_a and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
		$YarnIssueReturn = $booking_req_return = array();
		foreach ($issue_return as $row) {
			$YarnIssueReturn['amount'][$row[csf("po_breakdown_id")]]+=$row[csf("cons_amount")];
		}
		
		$YarnIssue=array();
		$issue_details = sql_select("select a.po_breakdown_id,a.prod_id,a.quantity as quantity,a.issue_purpose,b.product_name_details,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,b.lot,(a.quantity*c.cons_rate) as cons_amount_issue,c.cons_amount,c.receive_basis,d.booking_no, c.requisition_no from order_wise_pro_details a,product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type=2 and a.entry_form=3  and d.entry_form=3 $po_con_a and d.issue_purpose not in(2,8) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.prod_id");
		
		$issue_arr = $booking_req = array();
		foreach ($issue_details as $row) {
			$YarnIssue['amount'][$row[csf("po_breakdown_id")]]+=$row[csf("cons_amount_issue")];
		}
		
		
		
	//fabric...................................
		$sql = "select a.id,b.po_break_down_id,a.item_category,a.exchange_rate,b.grey_fab_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 $po_con_b3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2){
				$fabPurArr['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['knit']['amount'][$fabPur_row[csf('po_break_down_id')]]+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['woven']['amount'][$fabPur_row[csf('po_break_down_id')]]+=$fabPur_row[csf('amount')];
				}
			}
			$booking_idArr[$fabPur_row[csf('id')]]=$fabPur_row[csf('id')];
		}	
		$booking_cond_for_in=where_con_using_array($booking_idArr,0,"c.booking_id");
		 $sql_yarn_trans = "select  a.transfer_criteria,b.transfer_value_in_usd,b.fso_no,c.sales_booking_no,c.po_job_no  from inv_item_transfer_mst a, inv_item_transfer_dtls b,fabric_sales_order_mst c where a.id=b.mst_id and c.job_no=b.fso_no  and a.transfer_criteria=1 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1  and a.COMPANY_ID=$cbo_company_name  $booking_cond_for_in";
		$data_fso_yarn=sql_select($sql_yarn_trans);
		foreach($data_fso_yarn as $row){
			$yarnTransData['acl']['amount'][$row[csf('po_job_no')]]+=$row[csf('transfer_value_in_usd')];
		}
		//print_r($yarnTransData);

		foreach($YarnIssue['amount'] as $po_id=>$val){
			$YarnData['acl']['amount'][$po_id]=($YarnIssue['amount'][$po_id]-$YarnIssueReturn['amount'][$po_id])/$exchange_rate_arr[$poWiseJobDataArr[$po_id]];
		}
			
		//Knitting cost..................
		$sql = "select  b.order_id,b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 $po_con_b2 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$knitData['acl']['amount'][$row_knit[csf('order_id')]]+=$row_knit[csf('amount')]/$exchange_rate_arr[$poWiseJobDataArr[$row_knit[csf('order_id')]]];
		}
	
		$sql = "select  b.order_id,b.delivery_qty,b.rate,b.amount,c.product_name_details from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 $po_con_b2 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$knitData['acl']['amount'][$row_knit[csf('order_id')]]+=$row_knit[csf('amount')]/$exchange_rate_arr[$poWiseJobDataArr[$row_knit[csf('order_id')]]];
		}
		
		//Dying Finishing--------------------
		$sql = "select  b.order_id,b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=4 $po_con_b2 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$finishData['acl']['amount'][$row_finish[csf('order_id')]]+=$row_finish[csf('amount')]/$exchange_rate_arr[$poWiseJobDataArr[$row_finish[csf('order_id')]]];
		}
		
		
		$sql = "select  b.order_id,b.body_part_id,b.receive_qty,b.rate,b.amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.process_id=4 and b.sub_process_id in(31,35) $po_con_b2 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			$finishData['acl']['amount'][$row_aop[csf('order_id')]]+=$row_aop[csf('amount')]/$exchange_rate_arr[$poWiseJobDataArr[$row_aop[csf('order_id')]]];
		}
		
		//Trim cost.........................
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		
		$sql_trim_trans="select a.item_group,b.order_rate as rate,b.trans_type, b.po_breakdown_id,b.prod_id, (b.quantity) as qnty,
		(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
		(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty,
		(CASE WHEN b.trans_type=5 THEN a.rate*b.quantity END) AS in_amt,
		(CASE WHEN b.trans_type=6 THEN a.rate*b.quantity END) AS out_amt,
		c.from_order_id
		from inv_item_transfer_dtls a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.dtls_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78) and b.trans_type in(5,6) $po_con_b ";
		//echo $sql_trim_trans;die;
		$result_trim_trans=sql_select( $sql_trim_trans );
		$trim_in_qnty=$trim_out_qnty=$trim_in_amt=$trim_out_amt=0;
		foreach ($result_trim_trans as $row)
		{
			$rate=$row[csf('rate')];
			$con_factor=$trim_groupArr[$row[csf('item_group')]]['con_factor'];
			$trimsRecArr[$row[csf('item_group')]]['in_amt'][$row[csf('po_breakdown_id')]]+=$row[csf('in_qty')]*$con_factor*$rate;
			$trimsRecArr[$row[csf('item_group')]]['out_amt'][$row[csf('po_breakdown_id')]]+=$row[csf('out_qty')]*$con_factor*$rate;
		
		}
		
		
	$trimsRecArr=array();
		$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,b.quantity as quantity,b.order_rate as rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_con_b ");
		foreach($receive_qty_data as $row){
			$trimsRecArr[$row[csf('item_group_id')]][$row[csf('po_breakdown_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$row[csf('rate')];
		}		
		
		foreach($trimsRecArr as $ind=>$value2){
			foreach($value2 as $po_id=>$value){
				//$trimData['acl']['amount'][$po_id]=($trimsRecArr[$ind][$po_id]['amount']+$trimsRecArr[$ind]['in_amt'][$po_id])-($trimsRecArr[$ind][$po_id]['amount']+$trimsRecArr[$ind]['out_amt'][$po_id]);
			$trimData['acl']['amount'][$po_id]+=($trimsRecArr[$ind][$po_id]['amount']+$trimsRecArr[$ind]['in_amt'][$po_id])-$trimsRecArr[$ind]['out_amt'][$po_id];

			}
		}
		
		
		//print_r($trimData['acl']['amount']);die;
		
		
		//Emb----------------------------
	$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name !=3 $po_con_b3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$embData['acl']['amount'][$row[csf('po_break_down_id')]]+=$row[csf('amount')];
	
		}

		//wash------------
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name =3 $po_con_b3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$washData['acl']['amount'][$row[csf('po_break_down_id')]]+=$row[csf('amount')];
	
		}
	// Commision Cost....................................
		
		
			$job_no_list_arr=array_chunk($jobArr,999);
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){
					$jobCon =" and (job_no in('".implode("','",$job_no_process)."')";
				} 
				else{
					$jobCon .=" or job_no in('".implode("','",$job_no_process)."')";
				}
				$p++;
			}
			$jobCon .=")";
		
		
		$commiData=array();
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where 1=1 $jobCon ";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['amount'][$row[csf('job_no')]]+=$commiAmtArr[$row[csf('job_no')]][$row[csf('id')]];
		}
		
 	//Commarcial Cost.....................................
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where  1=1 $jobCon ";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commaData['pre']['amount'][$row[csf('job_no')]]+=$commaAmtArr[$row[csf('job_no')]][$row[csf('id')]];
		}		
		 //print_r($commiAmtArr);
		//print_r($commaData['pre']['amount']);
		
		
		// Other Cost............................
		$otherData=array();
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost,job_no  from  wo_pre_cost_dtls where 1=1 $jobCon";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['amount'][$row[csf('job_no')]]=$other_cost[$row[csf('job_no')]]['freight'];
	
			$otherData['pre']['lab_test']['amount'][$row[csf('job_no')]]=$other_cost[$row[csf('job_no')]]['lab_test'];
	
			$otherData['pre']['inspection']['amount'][$row[csf('job_no')]]=$other_cost[$row[csf('job_no')]]['inspection'];
	
			$otherData['pre']['currier_pre_cost']['amount'][$row[csf('job_no')]]=$other_cost[$row[csf('job_no')]]['currier_pre_cost'];
	
			$otherData['pre']['cm_cost']['amount'][$row[csf('job_no')]]=$other_cost[$row[csf('job_no')]]['cm_cost'];
		}
		
		
		
		if(count($quotationIdArr)){
			$jobWiseQuotationIdArr=array_flip($quotationIdArr);
			
			$sql = "select quotation_id,id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where quotation_id in(".implode(',',$quotationIdArr).")";
			
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty[$row[csf('quotation_id')]])*($quaOfferQnty[$row[csf('quotation_id')]]);
				$otherData['mkt']['freight']['amount'][$jobWiseQuotationIdArr[$row[csf('quotation_id')]]]=$freightAmt;
	
				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty[$row[csf('quotation_id')]])*($quaOfferQnty[$row[csf('quotation_id')]]);
				$otherData['mkt']['lab_test']['amount'][$jobWiseQuotationIdArr[$row[csf('quotation_id')]]]=$labTestAmt;
	
				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty[$row[csf('quotation_id')]])*($quaOfferQnty[$row[csf('quotation_id')]]);
				$otherData['mkt']['inspection']['amount'][$jobWiseQuotationIdArr[$row[csf('quotation_id')]]]=$inspectionAmt;
	
				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty[$row[csf('quotation_id')]])*($quaOfferQnty[$row[csf('quotation_id')]]);
				$otherData['mkt']['currier_pre_cost']['amount'][$jobWiseQuotationIdArr[$row[csf('quotation_id')]]]=$currierPreCostAmt;
	
				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty[$row[csf('quotation_id')]])*($quaOfferQnty[$row[csf('quotation_id')]]);
				$otherData['mkt']['cm_cost']['amount'][$jobWiseQuotationIdArr[$row[csf('quotation_id')]]]=$cmCostAmt;
			}
		}
		
		
		//Move po wise data to job wise.......................
		foreach($poWiseJobDataArr as $poId=>$jobNo){
			$yarn_costArr[$jobNo]+=$YarnData['acl']['amount'][$poId];
			$fp_costArr[$jobNo]+=$fabPurArr['acl']['woven']['amount'][$poId]+$fabPurArr['acl']['knit']['amount'][$poId];
			$knitting_costArr[$jobNo]+=$knitData['acl']['amount'][$poId];
			$df_costArr[$jobNo]+=$finishData['acl']['amount'][$poId];
			$trims_costArr[$jobNo]+=$trimData['acl']['amount'][$poId];
			$embt_costArr[$jobNo]+=$embData['acl']['amount'][$poId];
			$wash_costArr[$jobNo]+=$washData['acl']['amount'][$poId];
		}
		
				
		
				
	//Actual Matarial Cost................................end;	
		
		
		$width=	2300;			
		?>
        <div style="width:<?= $width;?>px;">
       
                <table width="<?= $width;?>"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="17" class="form_caption">
                            <strong style="font-size:16px;">Company:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                    </table>
                
                <table width="<?= $width;?>" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                   	<tr>
                    	 <th colspan="14" style="background:#CCCCCC" id="th_pre_cost">Pre-Costing</th>
                         <th colspan="7"   style="background:#999999" id="th_ex_fact_cost">Ex-Factory</th>
                         <th colspan="3"  style="background:#FFCC33" id="th_acl_material_cost">Actual Matarial Cost</th>
                         <th colspan="3" style="background:#99CCCC" id="th_acl_cm_cost" >Actual CM Cost</th>
                         <th colspan="2"  style="background:#CC9966"  id="th_acl_margin_cost">Actual Margin</th>
                        
                    </tr>
                   
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Buyer</th>
                        <th width="130">Style Name</th>
                        <th width="80">Job No</th>
                        <th width="80">Job Qty Pcs</th>
                        <th width="80">Unite Price</th>
                        <th width="80">Order Value</th>
                        <th width="80">Pre-Cost Matarial Cost</th>
                        <th width="50">RM%</th>
                        <th width="80">Pre-Cost CM Value</th>
                        <th width="50">CM%</th>
                        <th width="80">Pre-Cost Margin Value</th>
                        <th width="50">Margin%</th>
                        <th width="80">CM Cost With Margin(USD)</th>
                        
                        
                        <th width="80">Last Ex-Factory Date</th>
                       
                        <th width="80">Ex-Factory Qty(Pcs)</th>
                        <th width="80">Ex-Factory FOB Value(USD)</th>
						<th width="80">Short Qty</th>	
                        <th width="80">Short Value</th>	
                        <th width="80">Excess Qty</th>	
                        <th width="80">Excess Value</th>
                        
                        <th width="80">Actual Matarial Cost</th>
                        <th width="60">RM Cost%</th>	
                        <th width="80">Matarial Cost Deff</th>	
                        
                        <th width="80">Actual CM Cost</th>
                         <th width="60">CM Cost%</th>
                         <th width="80">Def CM With Margin</th>
                       
                        
                        
                        <th width="80">Actual Margin</th>
                        <th>Actual Margin %</th>
                        </tr>
                    </thead>
                </table>
            <div style="width:<?= $width+20;?>px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
               <?
 
					
									
					$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
              	foreach($po_wise_export_arr as $po_id=>$row)
				{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_cm_cost=$other_costing_arr[$row[('job_no')]]['cm_cost']; // total cm cost from class 4
						$yarnTrans_acl_cost=$yarnTransData['acl']['amount'][$row['job_no']];
						//echo $yarnTrans_cost.'d';
						$fabric_cost_knit_amount=array_sum($fabricCostArr['knit']['grey'][$row[('job_no')]]);
						$fabric_cost_woven_amount=array_sum($fabricCostArr['woven']['grey'][$row[('job_no')]]);
						$yarn_cost_amount=$yarnCostArr[$row[('job_no')]];
						//*$po_wise_export_arr[$row[('job_no')]]['total_set_qnty']
						$conversion_cost_amount=array_sum($conversionCostArr[$row[('job_no')]]);
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
						
						$deffdlc_cost=$other_costing_arr[$row[('job_no')]]['deffdlc_cost'];
						$incometax_cost=$job_wise_export_arr[$row[csf("job_no")]]['incometax_cost'];	
						$interest_cost=$job_wise_export_arr[$row[csf("job_no")]]['interest_cost'];
						
						
						$totalCost=($fabric_cost_knit_amount+$fabric_cost_woven_amount+$yarn_cost_amount+ $commision_cost_amount+$conversion_cost_amount+$trims_cost_amount+$po_cm_cost_val+$po_lab_test_val+$emblishment_cost_amount+$po_inspection_val+$wash_cost_amount+$po_currier_cost_val+$commercial_cost_amount+$po_freight_val+$po_common_oh_val+$po_certificate_pre_cost_val+$po_depr_amor_pre_cost_val+$deffdlc_cost+$incometax_cost+$interest_cost);

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
					$unit_price_pcs=$job_wise_export_arr[$row[('job_no')]]['unit_price'];
					//$exFactory_CM_Cost_USD=$po_cm_cost/$row[('ex_fac_qty')]*$poQntryPcs;
					
					$exFactory_CM_Cost_USD=$po_cm_cost/$jobQntryPcs;
					
					$po_cm_cost=$job_wise_export_arr[$row[("job_no")]]['cm_cost']; 
					$exFactory_CM_Cost_USD_2=$po_cm_cost/$order_price_per_dzn/$job_wise_export_arr[$row[("job_no")]]['total_set_qnty'];

					$short_qty=$job_wise_export_arr[$row[('job_no')]]['job_quantity_pcs']-$row[('ex_fac_qty')];
					$excess_qty=$row[('ex_fac_qty')]-$job_wise_export_arr[$row[('job_no')]]['job_quantity_pcs'];
					$short_qty=($short_qty>0)?$short_qty:0;
					$excess_qty=($excess_qty>0)?$excess_qty:0;
					$short_val=($short_qty*$unit_price_pcs);
					$excess_val=($excess_qty*$unit_price_pcs);
					
					
					$total_short_qty+=$short_qty;
					$total_excess_qty+=$excess_qty;
					$total_short_val+=$short_val;
					$total_excess_val+=$excess_val;
					
					
					//	Actual Matarial Cost...........................		
					$ActualMatarialCost=0;
					$ActualMatarialCost=$yarn_costArr[$row[('job_no')]]+
					$fp_costArr[$row[('job_no')]]+
					$yarnTrans_acl_cost+
					$knitting_costArr[$row[('job_no')]]+
					$df_costArr[$row[('job_no')]]+
					$trims_costArr[$row[('job_no')]]+
					$embt_costArr[$row[('job_no')]]+
					$wash_costArr[$row[('job_no')]]+
					$commiData['pre']['amount'][$row[('job_no')]]+
					$commaData['pre']['amount'][$row[('job_no')]]+
					$otherData['pre']['freight']['amount'][$row[('job_no')]]+
					$otherData['pre']['lab_test']['amount'][$row[('job_no')]]+
					$otherData['pre']['inspection']['amount'][$row[('job_no')]]+
					$otherData['pre']['currier_pre_cost']['amount'][$row[('job_no')]];
					
					
					
					
					/*$ActualMatarialCost2=$yarn_costArr[$row[('job_no')]].'+'.
					$fp_costArr[$row[('job_no')]].'+'.
					$knitting_costArr[$row[('job_no')]].'+'.
					$df_costArr[$row[('job_no')]].'+'.
					$trims_costArr[$row[('job_no')]].'+'.
					$embt_costArr[$row[('job_no')]].'+'.
					$wash_costArr[$row[('job_no')]].'+'.
					$commiData['pre']['amount'][$row[('job_no')]].'+'.
					$commaData['pre']['amount'][$row[('job_no')]].'+'.
					$otherData['pre']['freight']['amount'][$row[('job_no')]].'+'.
					$otherData['acl']['lab_test']['amount'][$row[('job_no')]].'+'.
					$otherData['pre']['inspection']['amount'][$row[('job_no')]].'+'.
					$otherData['acl']['currier_pre_cost']['amount'][$row[('job_no')]];
					echo $ActualMatarialCost2."<br>";*/

			   
			   ?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
							<td width="130" align="center"><p><a href='##' onClick="generate_link('bomRpt2','<?= $row[('job_no')]; ?>')"><? echo $row[('style')]; ?></a></p></td>
							<td width="80"><p title="<? echo $row[('job')]; ?>"><? echo $row[('job_no')]; ?></p></td>
							<td width="80" align="right"><? echo $job_wise_export_arr[$row[('job_no')]]['job_quantity_pcs']; ?></td>
                            <td width="80" align="center"><? echo  number_format($unit_price_pcs,2);?></td>
                            <td width="80" align="right"><? echo  $orderValue=($job_wise_export_arr[$row[('job_no')]]['job_quantity_pcs']*$unit_price_pcs);?></td>
                           
                            <td width="80" align="right"><?= number_format($totalCost-$po_cm_cost_val,4);$tot_pre_material_cost=$totalCost-$po_cm_cost_val; ?></td>
                             <td width="50" title="Pre Cost Material Cost/Order Value*100" align="right"><?  echo number_format(($tot_pre_material_cost/$orderValue)*100,2);?></td>
                            
                            <td width="80" align="right"><?=number_format($po_cm_cost_val,2);?></td>
                            <td width="50" title="Pre Cost CM Value/Order Value*100" align="right"><?  echo number_format(($po_cm_cost_val/$orderValue)*100,2);?></td>
                            <td width="80" align="right"><?=number_format($PreCostMarginValue=$orderValue-$totalCost,2);?></td>
                             <td width="50" title="Pre-Cost Margin Value/Order Value*100" align="right"><?  echo number_format(($PreCostMarginValue/$orderValue)*100,2);?></td>
                            
                            <td width="80" align="right"><p>
							 <? 
							echo number_format($po_cm_cost_val+$PreCostMarginValue,2);
							// echo number_format($ex_fac_qty_cm_cost_mergin+($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2),2);
							 //$TotalExFactoryCMCostWithMarginUSD+=$ex_fac_qty_cm_cost_mergin+($row[('ex_fac_qty')]*$exFactory_CM_Cost_USD_2);
							 ?>
                             </p>
							</td>
                               
                            <td width="80" align="center"><? echo change_date_format($row['ex_factory_date']);?></td>
                            
						 
							<td width="80"  align="right" title="Ex-fac Qty">
							<? 
								
								$total_ex_fac_qty+=$row[('ex_fac_qty')];
							 ?> 
                             <a href='##' onClick="open_exfactory_qty_dtls('<?= $row[('job_no')]; ?>')"><? echo number_format($row[('ex_fac_qty')],0);?></a>
                             </td>
							<td width="80" align="right" title="<?= $row[('ex_fac_qty')].'*'.$unit_price_pcs;?>" > <?  echo number_format($ExFactoryFOBValueUSD=$row[('ex_fac_qty')]*$unit_price_pcs,4); 
							
							$total_ex_fac_val+=$row[('ex_fac_qty')]*$unit_price_pcs;
							?></td>
                            <td width="80" align="right"><?= number_format($short_qty); ?></td>
                            <td width="80" align="right"><?= number_format($short_val,2); ?></td>
                            <td width="80" align="right"><?= number_format($excess_qty); ?></td>
                            <td width="80" align="right"><?= number_format($excess_val,2); ?></td>

                            <td width="80" align="right"><a href='##' onClick="open_actual_cost_dtls('<?= $row[('job_no')]; ?>')"><?= number_format($ActualMatarialCost,2);?></a></td>
                             <td width="60" title="Actual Material Cost/Ex-Fact FOB Value*100" align="right"><?  echo number_format(($ActualMatarialCost/$ExFactoryFOBValueUSD)*100,2);?></td>
                             
                            <td width="80" align="right"><? echo number_format(($totalCost-$po_cm_cost_val)-$ActualMatarialCost,2); ?></td>
                            
                            
                            
                            <td width="80" align="right" title="<?=$proLogDataArr[AVL_MIN][$row[('job_no')]].'*'.$cpmDataArr[$proLogDataArr[PRO_DATE_BY_JOB][$row[('job_no')]]].'/'.$conversion_rate;?>"><?=fn_number_format(($ActualCMCost=($proLogDataArr[AVL_MIN][$row[('job_no')]]*$cpmDataArr[$proLogDataArr[PRO_DATE_BY_JOB][$row[('job_no')]]])/$conversion_rate),2);?></td>
                             <td width="60" title="Actual CM Cost Value/Ex-Fact FOB Value*100" align="right"><?  echo number_format(($ActualCMCost/$ExFactoryFOBValueUSD)*100,2);?></td>
                            
                             
                            <td width="80" align="right"><?  echo number_format($AfterCostCMWithMarginValue=($row[('ex_fac_qty')]*$unit_price_pcs)-$ActualMatarialCost,2); ?></td>
                           
                           
                            
                            <td width="80" align="right"><?=fn_number_format($ActualMargin=($ExFactoryFOBValueUSD-($ActualMatarialCost+$ActualCMCost)),2);?></td>
                            <td align="right"><?=fn_number_format(($ActualMargin/$ExFactoryFOBValueUSD)*100,4);?></td>
                            
                            
                   	</tr>
                            <?
							$total_order_value+=$orderValue;
							$totalPreCostMatarialCost+=($totalCost-$po_cm_cost_val);
							$totalActualMatarialCost+=$ActualMatarialCost;
							$totalMatarialCostDeff+=($totalCost-$po_cm_cost_val)-$ActualMatarialCost;
							$totalAfterCostCMWithMarginValue+=$AfterCostCMWithMarginValue;
							$totalDefCMWithMargin+=$DefCMWithMargin;
							
							$totalCMCostPerPcsUSD+=$exFactory_CM_Cost_USD_2;
							$totalCMMarginPerPcsUSD+=$CMMarginPerPcsUSD;
							
                            $totalPreCostCMValue+=$po_cm_cost_val;
                            $totalPreCostMarginValue+=$PreCostMarginValue;
							$totalActualCMCost+=$ActualCMCost;
							$totalActualMargin+=$ActualMargin;
							
							$i++;
					}
							?>
               </table>
            <table width="<?= $width;?>" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer" align="left">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80"><?= $total_order_value;?></th>
                       
                        <th width="80"><?= number_format($totalPreCostMatarialCost,2);?></th>
                         <th width="50"><?=number_format($totalPreCostMatarialCost/$total_order_value*100,2);;?></th>
                        <th width="80"><?=number_format($totalPreCostCMValue,2);?></th>
                         <th width="50"><?=number_format($totalPreCostCMValue/$total_order_value*100,2);;?></th>
                         <th width="80"><?=number_format($totalPreCostMarginValue,2);?></th>
                          <th width="50"><?=number_format($totalPreCostMarginValue/$total_order_value*100,2);;?></th>
                         <th width="80"><?= number_format(($totalPreCostCMValue+$totalPreCostMarginValue),2); ?></th>
                        
                        <th width="80">&nbsp;</th>
                       
                        <th width="80"><?= number_format($total_ex_fac_qty,0); ?></th>
                        <th width="80"><?= number_format($total_ex_fac_val,2); ?></th>
                        <th width="80"><?= number_format($total_short_qty);?></th>
                        <th width="80"><?= number_format($total_short_val,2);?></th>
                        <th width="80"><?= number_format($total_excess_qty);?></th>
                        <th width="80"><?= number_format($total_excess_val,2);?></th>
                     
                        <th width="80"><?= number_format($totalActualMatarialCost,2);?></th>
                         <th width="60"><? //fn_number_format($totalActualCMCost,2);?></th>
                        
                        <th width="80"><?= fn_number_format($totalMatarialCostDeff,2);?></th>
                        <th width="80"><?=fn_number_format($totalActualCMCost,2);?></th>
                         <th width="60"><? //fn_number_format($totalActualCMCost,2);?></th>
                         
                        <th width="80"><?= fn_number_format($totalAfterCostCMWithMarginValue,2);?></th>
                       
                        <th width="80"><?=fn_number_format($totalActualMargin,2);?></th>
                        <th></th>
                    
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


if($action=="report_generate_6")
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
	if($reportType==6)
	{
		
 		
		 $sql= "select c.QUOTATION_ID,c.COMPANY_NAME, c.BUYER_NAME, c.JOB_NO_PREFIX_NUM,c.STYLE_REF_NO,c.JOB_NO,c.ID AS JOB_ID,c.ID, 
		 sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY, 
		 sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as RET_EX_FACTORY_QNTY, 
		 max(a.ex_factory_date) as EX_FACTORY_DATE, c.TOTAL_SET_QNTY,b.PO_QUANTITY,b.UNIT_PRICE ,b.id as PO_ID	
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and c.company_name=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.shiping_status = 3 and d.entry_from=158 $date_cond $buyer_id_cond $cbo_item_cond  group by  c.QUOTATION_ID,c.job_no,c.id,c.company_name, c.buyer_name,b.id, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty,b.po_quantity order by max(a.ex_factory_date)";
  		//echo $sql;
		$exfac_sql=sql_select($sql);
		$jobArr=array(0);
		foreach($exfac_sql as $row)
		{
				$po_wise_export_arr[$row[JOB_NO]][JOB_PREFIX_NUM]=$row[JOB_NO_PREFIX_NUM];
				$po_wise_export_arr[$row[JOB_NO]][JOB_NO]=$row[JOB_NO];	
				$po_wise_export_arr[$row[JOB_NO]][STYLE_REF_NO]=$row[STYLE_REF_NO];	
				$po_wise_export_arr[$row[JOB_NO]][BUYER_NAME]=$row[BUYER_NAME];
				$po_wise_export_arr[$row[JOB_NO]][EXF_QTY]+=($row[EX_FACTORY_QNTY]-$row[RET_EX_FACTORY_QNTY]);	
				$po_wise_export_arr[$row[JOB_NO]][EXF_VAL]+=($row[EX_FACTORY_QNTY]-$row[RET_EX_FACTORY_QNTY])*$row[UNIT_PRICE];	
				$po_wise_export_arr[$row[JOB_NO]][EXF_DATE]=change_date_format($row[EX_FACTORY_DATE]);
				
				$po_wise_export_arr[$row[JOB_NO]][JOB_QTY_PCS][$row[PO_ID]]=$row[PO_QUANTITY]*$row[TOTAL_SET_QNTY];
				$po_wise_export_arr[$row[JOB_NO]][JOB_QTY_VAL][$row[PO_ID]]=($row[PO_QUANTITY]*$row[TOTAL_SET_QNTY])*$row[UNIT_PRICE];

				$jobArr[$row[JOB_ID]]=$row[JOB_ID];
				$jobNoArr[$row[JOB_NO]]=$row[JOB_NO];
				$poIdArr[$row[PO_ID]]=$row[PO_ID];
				$quationIdArr[$row[QUOTATION_ID]]=$row[QUOTATION_ID];
				$jobByPoArr[$row[PO_ID]]=$row[JOB_NO];
		
		
		}
		

 	
		
		
		
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		$condition->buyer_name("=$cbo_buyer_name");
	}
			
	if(count($jobArr))
	{
		$condition->jobid_in(implode(',',$jobArr));
	}
 	
	
	$condition->init();

	$fabric= new fabric($condition);
	$fabricCostArr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
	//print_r($fabricCostArr);
	
	$yarn= new yarn($condition);
	$yarnCostArr=$yarn->getJobWiseYarnAmountArray();
	
	$conversion= new conversion($condition);
	$conversionCostArr=$conversion->getQtyArray_by_jobAndProcess();	

	$commercial= new commercial($condition);
	$commercialCostArr=$commercial->getAmountArray_by_job();
		
	$commision= new commision($condition);
	$commisionCostArr=$commision->getAmountArray_by_job();
	

	//echo $commisionCostArr['FAL-21-00193'];die;
 	//$other_costing_arr=$other->getAmountArray_by_job();
	
	
	$trim= new trims($condition);
	$trimsCostArr=$trim->getAmountArray_by_job();
	
	$emblishment= new emblishment($condition);
	$emblishmentCostArr=$emblishment->getAmountArray_by_job();
	
	$wash= new wash($condition);
	$washCostArr=$wash->getAmountArray_by_job();
 
 	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_job(); 
	//echo $other->getQuery(); die;
		
	//print_r($conversionCostArr ['FAL-21-00703'][1] ); 
	//print_r($conversionCostArr ['FAL-21-00703'][31] ); 
 
				
	//Actual Matarial Cost................................end;	
		
	$exchange_rate_arr=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst where 1=1 ".where_con_using_array($jobArr,0,'JOB_ID')."",'job_no','exchange_rate');
	 
	 //Yarn....................
	 $issue_return=sql_select("select a.id,a.trans_id,a.trans_type,a.entry_form,a.PO_BREAKDOWN_ID,a.prod_id,a.quantity as quantity,a.issue_purpose,a.returnable_qnty, a.is_sales, b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,(a.quantity*c.cons_rate) as cons_amount,c.order_amount,c.issue_id,d.booking_no,c.receive_basis from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.issue_id=d.id and a.trans_type in(4,5,6) and a.entry_form in(9,11) ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
		$YarnIssueReturn = $booking_req_return = array();
		foreach ($issue_return as $row) {
			$job_no = $jobByPoArr[$row[PO_BREAKDOWN_ID]];
			
			$YarnIssueReturn['qty'][$job_no] += $row[csf("quantity")];
			$YarnIssueReturn['amount'][$job_no]+=$row[csf("cons_amount")];
		}
		
		$YarnIssue=array();
		$issue_details = sql_select("select a.PO_BREAKDOWN_ID,a.prod_id,a.quantity as quantity,a.issue_purpose,b.product_name_details,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,b.lot,(a.quantity*c.cons_rate) as cons_amount_issue,c.cons_amount,c.receive_basis,d.booking_no, c.requisition_no from order_wise_pro_details a,product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type=2 and a.entry_form=3  and d.entry_form=3  ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and d.issue_purpose not in(2,8) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.prod_id");
		$issue_arr = $booking_req = array();
		foreach ($issue_details as $row) {
			$job_no = $jobByPoArr[$row[PO_BREAKDOWN_ID]];
			$YarnIssue['amount'][$job_no]+=$row[csf("cons_amount_issue")];
		}
		//$YarnData['acl']['amount']=($YarnIssue['amount']-$YarnIssueReturn['amount']);
		
//print_r($YarnIssue['amount']);

		
//fabric...................................
		$sql = "select a.id,a.item_category,a.exchange_rate,b.grey_fab_qnty,b.fin_fab_qnty,b.PO_BREAK_DOWN_ID,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2){
				$fabPurArr['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$job_no = $jobByPoArr[$row[PO_BREAK_DOWN_ID]];
					$fabPurArr['acl']['knit']['amount'][$job_no]+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$job_no = $jobByPoArr[$row[PO_BREAK_DOWN_ID]];
					$fabPurArr['acl']['woven']['amount'][$job_no]+=$fabPur_row[csf('amount')];
				}
			}
			$booking_idArr[$fabPur_row[csf('id')]]=$fabPur_row[csf('id')];
			$job_booking_idArr[$fabPur_row[csf('id')]] = $jobByPoArr[$fabPur_row[PO_BREAK_DOWN_ID]];
		}
		
		
		
		
	
//Yarn Transfer  cost..................fabric_sales_order_mst  
  $sql_yarn_trans = "select  c.booking_id, c.JOB_NO,a.transfer_criteria,b.transfer_value_in_usd,b.fso_no,c.sales_booking_no  from inv_item_transfer_mst a, inv_item_transfer_dtls b,fabric_sales_order_mst c where a.id=b.mst_id and c.job_no=b.fso_no ".where_con_using_array($booking_idArr,0,'c.booking_id')."  and a.transfer_criteria=1 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and a.COMPANY_ID=$cbo_company_name"; //
	$data_fso_yarn=sql_select($sql_yarn_trans);
	foreach($data_fso_yarn as $row){
		$JOB_NO=$job_booking_idArr[$row[csf('booking_id')]];
		$yarnTransData['acl']['amount'][$JOB_NO]+=$row[csf('transfer_value_in_usd')];
	}
		
	//Knitting cost..................
$sql = "select  b.ORDER_ID,b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2  ".where_con_using_array($poIdArr,0,'b.order_id')."  and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$job_no = $jobByPoArr[$row[ORDER_ID]];
			$knitData['acl']['amount'][$job_no]+=$row_knit[csf('amount')]/$exchange_rate_arr[$job_no];
		}
		$sql = "select  b.ORDER_ID,b.delivery_qty,b.rate,b.amount,c.product_name_details from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 ".where_con_using_array($poIdArr,0,'b.order_id')." and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$job_no = $jobByPoArr[$row[ORDER_ID]];
			$knitData['acl']['amount'][$job_no]+=$row_knit[csf('amount')]/$exchange_rate_arr[$job_no];
		}
		
		//Dying Finishing--------------------
		
		
		$sql = "select  b.ORDER_ID,b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=4 ".where_con_using_array($poIdArr,0,'b.order_id')."  and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$job_no = $jobByPoArr[$row[ORDER_ID]];
			$finishData['acl']['amount'][$job_no]+=$row_finish[csf('amount')]/$exchange_rate_arr[$job_no];
		}
		
		
		$sql = "select  b.ORDER_ID,b.body_part_id,b.receive_qty,b.rate,b.amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.process_id=4 and b.sub_process_id in(31,35) ".where_con_using_array($poIdArr,0,'b.order_id')."  and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			$job_no = $jobByPoArr[$row[ORDER_ID]];
			$finishData['acl']['amount'][$job_no]+=$row_aop[csf('amount')]/$exchange_rate_arr[$job_no];
		}
		//Trim cost.........................
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		
		$sql_trim_trans="select a.item_group,b.order_rate as rate,b.trans_type, b.PO_BREAKDOWN_ID,b.prod_id, (b.quantity) as qnty,
		(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
		(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty,
		(CASE WHEN b.trans_type=5 THEN a.rate*b.quantity END) AS in_amt,
		(CASE WHEN b.trans_type=6 THEN a.rate*b.quantity END) AS out_amt,
		c.from_order_id
		from inv_item_transfer_dtls a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.dtls_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78) and b.trans_type in(5,6) ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')." ";
		$result_trim_trans=sql_select( $sql_trim_trans );
		$trim_in_qnty=$trim_out_qnty=$trim_in_amt=$trim_out_amt=0;
		foreach ($result_trim_trans as $row)
		{
			$job_no = $jobByPoArr[$row[PO_BREAKDOWN_ID]];
			$con_factor=$trim_groupArr[$row[csf('item_group')]]['con_factor'];
			$trimsRecArr[$job_no]['in_amt']+=$row[csf('in_qty')]*$con_factor*$row[csf('rate')];
			$trimsRecArr[$job_no]['out_amt']+=$row[csf('out_qty')]*$con_factor*$row[csf('rate')];
		}
		
		 
	//$trimsRecArr=array();
		$receive_qty_data=sql_select("select b.PO_BREAKDOWN_ID, a.item_group_id,b.quantity as quantity,b.order_rate as rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($poIdArr,0,'b.po_breakdown_id')."" );
		foreach($receive_qty_data as $row){
			$job_no = $jobByPoArr[$row[PO_BREAKDOWN_ID]];
			$trimsRecArr[$job_no]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$row[csf('rate')];
		}		
		
		
		
		foreach($trimsRecArr as $job_no=>$value){
			$trimData['acl']['amount'][$job_no]+=(($trimsRecArr[$job_no]['amount']+$trimsRecArr[$job_no]['in_amt'])-$trimsRecArr[$job_no]['out_amt']);
		}
		
		
 	//Emb----------------------------
	$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.PO_BREAK_DOWN_ID,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name !=3 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$job_no = $jobByPoArr[$row[PO_BREAK_DOWN_ID]];
			$embData['acl']['amount'][$job_no]+=$row[csf('amount')];
	
		}
		//wash------------
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.PO_BREAK_DOWN_ID,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name =3  ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$job_no = $jobByPoArr[$row[PO_BREAK_DOWN_ID]];
			$washData['acl']['amount'][$job_no]+=$row[csf('amount')];
	
		}
	// Commision Cost....................................
		$commiData=array();
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select a.COSTING_PER,b.COMMISSION_BASE_ID,b.id,b.JOB_NO,b.particulars_id,b.commission_base_id,b.commision_rate,b.commission_amount, b.status_active,b.COMMISSION_AMOUNT from  WO_PRE_COST_MST a,wo_pre_cost_commiss_cost_dtls b  where b.JOB_ID=a.JOB_ID ".where_con_using_array($jobArr,0,'b.JOB_ID')."";
		 //echo $sql;die;
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$acCostingPerQty=0;
			if($row[COMMISSION_BASE_ID]==2) $acCostingPerQty=1;
			else if($row[COMMISSION_BASE_ID]==3) $acCostingPerQty=12;
			else if($row[COMMISSION_BASE_ID]==1){
				$COSTING_PER=$row[COSTING_PER];
				$quaCostingPerQty=0;
				if($COSTING_PER==1) $quaCostingPerQty=12;
				else if($COSTING_PER==2) $quaCostingPerQty=1;
				else if($COSTING_PER==3) $quaCostingPerQty=24;
				else if($COSTING_PER==4) $quaCostingPerQty=36;
				else if($COSTING_PER==5) $quaCostingPerQty=48;			
				$row[csf('commision_rate')]=$row[csf('COMMISSION_AMOUNT')]/$quaCostingPerQty;
				$acCostingPerQty=1;
			}
		
			$commiData['acl']['amount'][$row[JOB_NO]]+=$po_wise_export_arr[$row[JOB_NO]][EXF_QTY]*($row[csf('commision_rate')]/$acCostingPerQty);
		}
		
		
		
		
		
 	//Commarcial Cost.....................................
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, JOB_NO, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where 1=1  ".where_con_using_array($jobArr,0,'JOB_ID')."";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['amount'][$row[JOB_NO]]+=$commaAmtArr[$row[csf('job_no')]][$row[csf('id')]];
		}		
		
// Other Cost............................
		$otherData=array();
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost,JOB_NO  from  wo_pre_cost_dtls where 1=1 ".where_con_using_array($jobArr,0,'JOB_ID')."";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['amount'][$row[JOB_NO]]=$other_cost[$row[csf('job_no')]]['freight'];
	
			$otherData['pre']['lab_test']['amount'][$row[JOB_NO]]=$other_cost[$row[csf('job_no')]]['lab_test'];
	
			$otherData['pre']['inspection']['amount'][$row[JOB_NO]]=$other_cost[$row[csf('job_no')]]['inspection'];
	
			$otherData['pre']['currier_pre_cost']['amount'][$row[JOB_NO]]=$other_cost[$row[csf('job_no')]]['currier_pre_cost'];
	
			//$otherData['pre']['cm_cost']['amount'][$row[JOB_NO]]=$other_cost[$row[csf('job_no')]]['cm_cost'];
		}
		
		
/*		if(count(quationIdArr)){
			$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where 1=1 ".where_con_using_array($quationIdArr,0,'quotation_id')."";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['amount']=$freightAmt;
	
				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['amount']=$labTestAmt;
	
				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['inspection']['amount']=$inspectionAmt;
	
				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['amount']=$currierPreCostAmt;
	
				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['amount']=$cmCostAmt;
			}
		}
*/		
		//echo $sql;			
		
		

		$sqlCpm="select COMPANY_ID,APPLYING_PERIOD_DATE,APPLYING_PERIOD_TO_DATE,COST_PER_MINUTE  from LIB_STANDARD_CM_ENTRY where COMPANY_ID=$cbo_company_name ";
		$sqlCpmRes=sql_select($sqlCpm);
		foreach($sqlCpmRes as $row)
		{
			$tot_month = datediff( 'd', $row[APPLYING_PERIOD_DATE],$row[APPLYING_PERIOD_TO_DATE]);
			for($i=0; $i<= $tot_month; $i++ )
			{
				$next_month=add_date($row[APPLYING_PERIOD_DATE],$i);
				$dateKey=date("d-m-Y",strtotime($next_month));
				$cpmDataArr[$dateKey]=$row[COST_PER_MINUTE];
			}
 		}
		
		$conversion_rate=return_field_value("CONVERSION_RATE","CURRENCY_CONVERSION_RATE", "id=(select max(id) from CURRENCY_CONVERSION_RATE where CURRENCY=2 and COMPANY_ID=$cbo_company_name)","CONVERSION_RATE");
	
		
		$sqlProLog="select JOBNO, max(PRODUCTION_DATE) as PRODUCTION_DATE,sum(AVAILABLE_MIN) as AVAILABLE_MIN   from PRODUCTION_LOGICSOFT where 1=1  ".where_con_using_array($jobNoArr,1,'JOBNO')." group by JOBNO ORDER BY PRODUCTION_DATE";
		$sqlProLogRes=sql_select($sqlProLog);
		foreach($sqlProLogRes as $row)
		{
			//$proLogDataArr[AVL_MIN][$row[JOBNO]]=$row[AVAILABLE_MIN];
			//$proLogDataArr[PRO_DATE][]=$row[PRODUCTION_DATE];
			//$proLogDataArr[PRO_DATE_BY_JOB][$row[JOBNO]]=date("d-m-Y",strtotime($row[PRODUCTION_DATE]));
			$PRO_DATE_BY_JOB = date("d-m-Y",strtotime($row[PRODUCTION_DATE]));
			$ActualCMCost =($row[AVAILABLE_MIN]*$cpmDataArr[$PRO_DATE_BY_JOB])/$conversion_rate;
			$otherData['pre']['cm_cost']['amount'][$row[JOBNO]]= $ActualCMCost;
		}



		
		$width=	1960;			
		?>
        <div style="width:<?= $width;?>px;">
       
                <table width="<?= $width;?>"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="24" class="form_caption">
                            <strong style="font-size:16px;"><? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="24" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                </table>
                
                <table width="<?= $width;?>" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Buyer</th>
                        <th width="130">Style Name</th>
                        <th width="80">Job No</th>
                        <th width="80">Last Ex-Factory Date</th>
                        <th width="80">Type</th>
                        <th width="80">Job Qty</th>
                        <th width="80">Value</th>
                        <th width="80">Yarn Cost</th>
                        <th width="80">Fabric Purchase</th>
                        <th width="80">Knitting Cost</th>
                        <th width="80">Dyeing Finisheing Cost</th>
                        <th width="80">Trims Cost</th>
                        <th width="80">Embt Cost</th>
                        <th width="80">Wash Cost</th>
                        <th width="80">Commission Cost</th>
                        <th width="80">Commercial Cost</th>
                        <th width="80">Freight Cost</th>
                        <th width="80">Testing Cost</th>
                        <th width="80">Inspection Cost</th>
                        <th width="80">Courier Cost</th>
                        <th width="80">Total Matarial Cost</th>
                       	<th width="80">CM Cost</th>
                        <th>Margin</th>
                        </tr>
                    </thead>
                </table>
            <div style="width:<?= $width+20;?>px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
               <?
			 	$i=1;
              	foreach($po_wise_export_arr as $row){ 
				
					$YarnData['acl']['amount']=($YarnIssue['amount'][$row[JOB_NO]]-$YarnIssueReturn['amount'][$row[JOB_NO]])/$exchange_rate_arr[$row[JOB_NO]];
				
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			   
			   
			   			//-------------------------------------
                        $preDataArr[JQ]=array_sum($po_wise_export_arr[$row[JOB_NO]][JOB_QTY_PCS]);
                        $preDataArr[JV]=array_sum($po_wise_export_arr[$row[JOB_NO]][JOB_QTY_VAL]);
                        $preDataArr[YC]=$yarnCostArr[$row[JOB_NO]];
                        $preDataArr[FC]=$fabricCostArr[$row[JOB_NO]];
                        $preDataArr[KC]=array_sum($conversionCostArr [$row[JOB_NO]][1]);
                        $preDataArr[DC]=array_sum($conversionCostArr [$row[JOB_NO]][31]);
                        $preDataArr[TC]=$trimsCostArr[$row[JOB_NO]];
                        $preDataArr[EC]=$emblishmentCostArr[$row[JOB_NO]];
                        $preDataArr[WC]=$washCostArr[$row[JOB_NO]];
                        $preDataArr[CC]=$commisionCostArr[$row[JOB_NO]];
                        $preDataArr[COC]=$commercialCostArr[$row[JOB_NO]];
                        $preDataArr[FC]=$other_costing_arr[$row[JOB_NO]][freight];
                        $preDataArr[LC]=$other_costing_arr[$row[JOB_NO]][lab_test];
                        $preDataArr[IC]=$other_costing_arr[$row[JOB_NO]][inspection];
                        $preDataArr[CRC]=$other_costing_arr[$row[JOB_NO]][currier_pre_cost];
                        $preDataArr[CMC]=$other_costing_arr[$row[JOB_NO]][cm_cost];
						
						$total_pre_matarial_cost=$preDataArr[YC]+$preDataArr[FC]+$preDataArr[KC]+$preDataArr[DC]+$preDataArr[TC]+$preDataArr[EC]+$preDataArr[WC]+$preDataArr[CC]+$preDataArr[COC]+$preDataArr[FC]+$preDataArr[LC]+$preDataArr[IC]+$preDataArr[CRC];
						
						//-------------------------------------------
                        $acDataArr[EQ]=$row[EXF_QTY]; 
                        $acDataArr[EV]=$row[EXF_VAL]; 
                        $acDataArr[YC]=$YarnData['acl']['amount']+$yarnTransData['acl']['amount'][$row[JOB_NO]]; 
                        $acDataArr[FC]=($fabPurArr['acl']['woven']['amount'][$row[JOB_NO]]+$fabPurArr['acl']['knit']['amount'][$row[JOB_NO]]); 
                        $acDataArr[KC]=$knitData['acl']['amount'][$row[JOB_NO]]; 
                        $acDataArr[DC]=$finishData['acl']['amount'][$row[JOB_NO]]; 
                        $acDataArr[TC]=$trimData['acl']['amount'][$row[JOB_NO]]; 
                        $acDataArr[EC]=$embData['acl']['amount'][$row[JOB_NO]]; 
                        $acDataArr[WC]=$washData['acl']['amount'][$row[JOB_NO]]; 
                        $acDataArr[CC]=$commiData['acl']['amount'][$row[JOB_NO]]; 
                        $acDataArr[COC]=$commaData['pre']['amount'][$row[JOB_NO]]; 
                        $acDataArr[FC]=$otherData['pre']['freight']['amount'][$row[JOB_NO]]; 
                        $acDataArr[LC]=$otherData['pre']['lab_test']['amount'][$row[JOB_NO]]; 
                        $acDataArr[IC]=$otherData['pre']['inspection']['amount'][$row[JOB_NO]]; 
                        $acDataArr[CRC]=$otherData['pre']['currier_pre_cost']['amount'][$row[JOB_NO]]; 
                        $acDataArr[CMC]=$otherData['pre']['cm_cost']['amount'][$row[JOB_NO]]; 			   
			   
						$total_act_matarial_cost=$acDataArr[YC]+$acDataArr[FC]+$acDataArr[KC]+$acDataArr[DC]+$acDataArr[TC]+$acDataArr[EC]+$acDataArr[WC]+$acDataArr[CC]+$acDataArr[COC]+$acDataArr[FC]+$acDataArr[LC]+$acDataArr[IC]+$acDataArr[CRC];
			   
			   
			   ?>
               		<tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
                        <td rowspan="3" width="30" align="center"><?= $i; ?></td>
                        <td rowspan="3" width="100" valign="middle"><?= $buyer_arr[$row[BUYER_NAME]]; ?></td>
                        <td rowspan="3" width="130" valign="middle"><?= $row[STYLE_REF_NO]; ?></td>
                        <td rowspan="3" width="80" align="center" valign="middle"><?= $row[JOB_NO]; ?></td>
                        <td rowspan="3" width="80" align="center" valign="middle"><?= $row[EXF_DATE]; ?></td>
                        <td width="80"><strong>Pre-Costing</strong></td>
                        <td width="80" align="right"><?=number_format($preDataArr[JQ]); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[JV],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[YC],2);?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[FC],2);?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[KC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[DC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[TC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[EC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[WC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[CC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[COC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[FC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[LC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[IC],2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[CRC],2); ?></td>
                        <td width="80" align="right"><?=number_format($total_pre_matarial_cost,2); ?></td>
                        <td width="80" align="right"><?=number_format($preDataArr[CMC],2); ?></td>
                        <td align="right"><?=number_format($preMargin=$preDataArr[JV]-($total_pre_matarial_cost+$preDataArr[CMC]),2); ?></td>
                   	</tr>
                    <tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tra_<?= $i; ?>','<?= $bgcolor;?>')" id="tra_<?= $i; ?>">
                        <td><strong>Actual Costing</strong></td>
                        <td align="right"><?=number_format($acDataArr[EQ]); ?></td>
                        <td align="right"><?=number_format($acDataArr[EV],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[YC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[FC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[KC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[DC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[TC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[EC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[WC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[CC],2); ?></td>
                        <td align="right"><?=number_format($acDataArr[COC],2);?></td>
                        <td align="right"><?=number_format($acDataArr[FC],2);?></td>
                        <td align="right"><?=number_format($acDataArr[LC],2);?></td>
                        <td align="right"><?=number_format($acDataArr[IC],2);?></td>
                        <td align="right"><?=number_format($acDataArr[CRC],2); ?></td>
                        <td align="right"><?=number_format($total_act_matarial_cost,2); ?></td>
                        <td align="right"><?=number_format($acDataArr[CMC],2);?></td>
                        <td align="right"><?=number_format($acMargin=$acDataArr[EV]-($total_act_matarial_cost+$acDataArr[CMC]),2); ?></td>
                    </tr>
                    <tr bgcolor="<?= $bgcolor;?>" onClick="change_color('trd_<?= $i; ?>','<?= $bgcolor;?>')" id="trd_<?= $i; ?>">
                        <td><strong>Deff</strong></td>
                        <td align="right"><?=number_format($preDataArr[JQ]-$acDataArr[EQ]); ?></td>
                        <td align="right"><?=number_format($preDataArr[JV]-$acDataArr[EV],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[YC]-$acDataArr[YC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[FC]-$acDataArr[FC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[KC]-$acDataArr[KC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[DC]-$acDataArr[DC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[TC]-$acDataArr[TC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[EC]-$acDataArr[EC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[WC]-$acDataArr[WC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[CC]-$acDataArr[CC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[COC]-$acDataArr[COC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[FC]-$acDataArr[FC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[LC]-$acDataArr[LC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[IC]-$acDataArr[IC],2); ?></td>
                        <td align="right"><?=number_format($preDataArr[CRC]-$acDataArr[CRC],2); ?></td>
                        <td align="right"><?=number_format($deff_matrial_cost = $total_pre_matarial_cost-$total_act_matarial_cost,2);?></td>
                        <td align="right"><?=number_format($deff_cm_cost = $preDataArr[CMC]-$acDataArr[CMC],2); ?></td>
                        <td align="right"><?=number_format(($preMargin-$acMargin),2);?></td>
                    </tr>
                    <?
					$i++;
					}
					?>
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


if($action=="ex_factory_qty_pcs_dtls")
{

	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	
	
$sql= "select b.PO_NUMBER,a.EX_FACTORY_DATE,a.CHALLAN_NO,a.REMARKS,

			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as RET_EX_FACTORY_QNTY
	
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,wo_pre_cost_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and b.job_no_mst=d.job_no and  a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.entry_from=158  and c.job_no='$job_no' group by  b.PO_NUMBER,a.EX_FACTORY_DATE,a.CHALLAN_NO,a.REMARKS order by a.EX_FACTORY_DATE";
		 //echo $sql;
		
		$dataArr=sql_select($sql);	
	
				
		
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:590px"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th>Order No</th>
                        <th width="90">Ex-Factory Date</th>
                        <th>Ex-Factory Challan no</th>
                        <th width="80">Ex-Factory Qty</th>
                        <th>Remarks</th>
                     </tr>   
                </thead>
                <tbody>	 	
					<?
					$i=1;
					 foreach($dataArr as $row){
					 if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
					 	$exFacQty=$row[EX_FACTORY_QNTY]-$row[RET_EX_FACTORY_QNTY];
						$total_ex_fac_qty+=$exFacQty;
					 ?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
							<td><? echo $i; ?></td> 
							<td><? echo $row[PO_NUMBER]; ?></td>
							<td align="center"><? echo change_date_format($row[EX_FACTORY_DATE]); ?></td>
							<td><? echo $row[CHALLAN_NO]; ?></td>
							<td align="right"><? echo number_format($exFacQty,2); ?></td>
							<td><? echo $row[REMARKS]; ?></td>
						</tr>
                      <? } ?>
					
                </tbody>
                <tfoot>
                    <th colspan="4">Total</th>
                    <th><?= $total_ex_fac_qty;?></th>
                    <th></th>
                 </tfoot>   
            </table>
        </fieldset>
    </div>    
    <?	
	
	
exit();	
}

if($action=="actual_cost_dtls")
{

	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$job_no='D n C-17-01587';
	
	$exchange_rate_arr=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst",'job_no','exchange_rate');
	$g_exchange_rate=$exchange_rate_arr[$job_no];

	
	$sql= "select b.id,a.buyer_name,a.style_ref_no,a.job_no,a.quotation_id,
	sum(b.po_quantity) as po_quantity, 
	sum(a.total_set_qnty*b.po_quantity) as po_quantity_pcs,
	max(a.avg_unit_price/a.total_set_qnty) as unit_price
	from wo_po_details_master a,wo_po_break_down b
	where b.job_no_mst=a.job_no and a.job_no='$job_no' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
	group by b.id,a.buyer_name,a.style_ref_no,a.job_no,a.quotation_id
	";
	$job_sql_result=sql_select($sql);
	foreach($job_sql_result as $row){
		$quotationId=$row[csf('quotation_id')];
		$jobDataArr[po_quantity_pcs]+=$row[csf('po_quantity_pcs')];
		$jobDataArr[buyer_name]=$row[csf('buyer_name')];
		$jobDataArr[style_ref_no]=$row[csf('style_ref_no')];
		$jobDataArr[job_no]=$row[csf('job_no')];
		$jobDataArr[po_id][$row[csf('id')]]=$row[csf('id')];
	}
	
	
	
	if($quotationId){
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
			$sqlQua="select a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			//echo $sqlQua;die;
			$dataQua=sql_select($sqlQua);
			foreach($dataQua as $rowQua){
				$quaOfferQnty=$rowQua[csf('offer_qnty')];
				$quaConfirmPrice=$rowQua[csf('confirm_price')];
				$quaConfirmPriceDzn=$rowQua[csf('confirm_price_dzn')];
				$quaPriceWithCommnPcs=$rowQua[csf('price_with_commn_pcs')];
				$quaCostingPer=$rowQua[csf('costing_per')];
				$quaCostingPerQty=0;
				if($quaCostingPer==1) $quaCostingPerQty=12;
				if($quaCostingPer==2) $quaCostingPerQty=1;
				if($quaCostingPer==3) $quaCostingPerQty=24;
				if($quaCostingPer==4) $quaCostingPerQty=36;
				if($quaCostingPer==5) $quaCostingPerQty=48;
			}
		}	
		
		
		
		$condition= new condition();
		if(str_replace("'","",$job_no) !=''){
			$condition->job_no("='$job_no'");
		}
	
		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		//$fabric= new fabric($condition);
		//$yarn= new yarn($condition);
	
		//$conversion= new conversion($condition);
		//$trim= new trims($condition);
		//$emblishment= new emblishment($condition);
		//$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);		
		
		
		
	
	 //Yarn....................
	 $issue_return=sql_select("select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity as quantity,a.issue_purpose,a.returnable_qnty, a.is_sales, b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,(a.quantity*c.cons_rate) as cons_amount,c.order_amount,c.issue_id,d.booking_no,c.receive_basis from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.issue_id=d.id and a.trans_type in(4,5,6) and a.entry_form in(9,11) and a.po_breakdown_id in(".implode(",",$jobDataArr[po_id]).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
		$YarnIssueReturn = $booking_req_return = array();
		foreach ($issue_return as $row) {
			$YarnIssueReturn['qty'] += $row[csf("quantity")];
			$YarnIssueReturn['amount']+=$row[csf("cons_amount")];
		}
		
		$YarnIssue=array();
		$issue_details = sql_select("select a.po_breakdown_id,a.prod_id,a.quantity as quantity,a.issue_purpose,b.product_name_details,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,b.lot,(a.quantity*c.cons_rate) as cons_amount_issue,c.cons_amount,c.receive_basis,d.booking_no, c.requisition_no from order_wise_pro_details a,product_details_master b,inv_transaction c,inv_issue_master d where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and a.trans_type=2 and a.entry_form=3  and d.entry_form=3 and a.po_breakdown_id in(".implode(",",$jobDataArr[po_id]).") and d.issue_purpose not in(2,8) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.prod_id");

	
		
		$issue_arr = $booking_req = array();
		foreach ($issue_details as $row) {
			//$YarnIssue['qty']+=$row[csf("quantity")];
			$YarnIssue['amount']+=$row[csf("cons_amount_issue")];
		}
		//$YarnData['acl']['qty']=$YarnIssue['qty']-$YarnIssueReturn['qty'];
		$YarnData['acl']['amount']=($YarnIssue['amount']-$YarnIssueReturn['amount'])/$g_exchange_rate;
		
//fabric...................................
		$sql = "select a.id,a.item_category,a.exchange_rate,b.grey_fab_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1  and b.po_break_down_id in(".implode(",",$jobDataArr[po_id]).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2){
				$fabPurArr['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
				}
			}
			$booking_idArr[$fabPur_row[csf('id')]]=$fabPur_row[csf('id')];
		}
	
//Yarn Transfer  cost..................fabric_sales_order_mst  
  $sql_yarn_trans = "select  a.transfer_criteria,b.transfer_value_in_usd,b.fso_no,c.sales_booking_no  from inv_item_transfer_mst a, inv_item_transfer_dtls b,fabric_sales_order_mst c where a.id=b.mst_id and c.job_no=b.fso_no and c.booking_id in(".implode(",",$booking_idArr).") and a.transfer_criteria=1 and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and a.COMPANY_ID=$cbo_company_name"; //
	$data_fso_yarn=sql_select($sql_yarn_trans);
	foreach($data_fso_yarn as $row){
		$yarnTransData['acl']['amount']+=$row[csf('transfer_value_in_usd')];
	}
		
	//Knitting cost..................
$sql = "select  b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$jobDataArr[po_id]).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$knitData['acl']['amount']+=$row_knit[csf('amount')]/$g_exchange_rate;
		}
		$sql = "select  b.delivery_qty,b.rate,b.amount,c.product_name_details from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$jobDataArr[po_id]).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$knitData['acl']['amount']+=$row_knit[csf('amount')]/$g_exchange_rate;
		}
		
		//Dying Finishing--------------------
		
		
		$sql = "select  b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=4 and b.order_id in(".implode(",",$jobDataArr[po_id]).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$finishData['acl']['amount']+=$row_finish[csf('amount')]/$g_exchange_rate;
		}
		
		
		$sql = "select  b.body_part_id,b.receive_qty,b.rate,b.amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.process_id=4 and b.sub_process_id in(31,35) and b.order_id in(".implode(",",$jobDataArr[po_id]).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			$finishData['acl']['amount']+=$row_aop[csf('amount')]/$g_exchange_rate;
		}
		//Trim cost.........................
		
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
		
		
		$sql_trim_trans="select a.item_group,b.order_rate as rate,b.trans_type, b.po_breakdown_id,b.prod_id, (b.quantity) as qnty,
		(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
		(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty,
		(CASE WHEN b.trans_type=5 THEN a.rate*b.quantity END) AS in_amt,
		(CASE WHEN b.trans_type=6 THEN a.rate*b.quantity END) AS out_amt,
		c.from_order_id
		from inv_item_transfer_dtls a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.dtls_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(78) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$jobDataArr[po_id]).") ";
		$result_trim_trans=sql_select( $sql_trim_trans );
		$trim_in_qnty=$trim_out_qnty=$trim_in_amt=$trim_out_amt=0;
		foreach ($result_trim_trans as $row)
		{
			$rate=$row[csf('rate')];
			$con_factor=$trim_groupArr[$row[csf('item_group')]]['con_factor'];
			$trimsRecArr[$row[csf('item_group')]]['in_amt']+=$row[csf('in_qty')]*$con_factor*$rate;
			$trimsRecArr[$row[csf('item_group')]]['out_amt']+=$row[csf('out_qty')]*$con_factor*$rate;
		
		}
		
		
	$trimsRecArr=array();
		$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,b.quantity as quantity,b.order_rate as rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in(".implode(",",$jobDataArr[po_id]).") ");
		foreach($receive_qty_data as $row){
			//$trimsRecArr[$row[csf('item_group_id')]]['qty']+=$row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'];
			//$trimsRecArr[$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
			$trimsRecArr[$row[csf('item_group_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$row[csf('rate')];
			//$trimsRecArr[$row[csf('item_group_id')]]['cons_uom']=$trim_groupArr[$row[csf('item_group_id')]]['cons_uom'];
		}		
		
		
		foreach($trimsRecArr as $ind=>$value){
			//$trimData['acl']['amount']+=($trimsRecArr[$ind]['amount']+$trimsRecArr[$ind]['in_amt'])-($trimsRecArr[$ind]['amount']+$trimsRecArr[$ind]['out_amt']);
			$trimData['acl']['amount']+=(($trimsRecArr[$ind]['amount']+$trimsRecArr[$ind]['in_amt'])-$trimsRecArr[$ind]['out_amt']);
		}
		
 	//Emb----------------------------
	$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name !=3 and b.po_break_down_id in(".implode(",",$jobDataArr[po_id]).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$embData['acl']['amount']+=$row[csf('amount')];
	
		}
		//wash------------
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name =3 and b.po_break_down_id in(".implode(",",$jobDataArr[po_id]).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$washData['acl']['amount']+=$row[csf('amount')];
	
		}
	// Commision Cost....................................
		$commiData=array();
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$job_no."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['amount']+=$commiAmtArr[$row[csf('job_no')]][$row[csf('id')]];
		}
		
 	//Commarcial Cost.....................................
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$job_no."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['amount']+=$commaAmtArr[$row[csf('job_no')]][$row[csf('id')]];
		}		
		
// Other Cost............................
		$otherData=array();
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost,job_no  from  wo_pre_cost_dtls where job_no='".$job_no."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['amount']=$other_cost[$row[csf('job_no')]]['freight'];
	
			$otherData['pre']['lab_test']['amount']=$other_cost[$row[csf('job_no')]]['lab_test'];
	
			$otherData['pre']['inspection']['amount']=$other_cost[$row[csf('job_no')]]['inspection'];
	
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$row[csf('job_no')]]['currier_pre_cost'];
	
			$otherData['pre']['cm_cost']['amount']=$other_cost[$row[csf('job_no')]]['cm_cost'];
		}
		
		
		if($quotationId){
			$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where quotation_id='".$quotationId."'";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['amount']=$freightAmt;
	
				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['amount']=$labTestAmt;
	
				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['inspection']['amount']=$inspectionAmt;
	
				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['amount']=$currierPreCostAmt;
	
				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['amount']=$cmCostAmt;
			}
		}
		
		//echo $sql;			
		
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:1590px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Date Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style Name</th>
                        <th width="100">Job No</th>
                        <th width="80">Order Qty (Pcs)</th>
                        <th width="80">Yarn Cost</th>
                        <th width="80">Fabric booking Cost</th>
                        <th width="80">Knitting Cost</th>
                        <th width="80">Dyeing Finisheing Cost</th>
                        <th width="80">Trims Cost</th>
                        <th width="80">Embt Cost</th>
                        <th width="80">Wash Cost</th>
                        <th width="80">Commission Cost</th>
                        <th width="80">Commercial Cost</th>
                        <th width="80">Freight Cost</th>
                        <th width="80">Testing Cost</th>
                        <th width="80">Inspection Cost</th>
                        <th width="80">Courier Cost</th>
                        <th>Total Actual Matarial Cost</th>
                     </tr>   
                </thead>
                <tbody>	 	
					<?
					$i=1;
					 if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					 
					  $yarn_transfer_cost=$yarnTransData['acl']['amount']; 
					 ?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
							<td><? echo $i; ?></td> 
							<td><? echo $jobDataArr[$row[buyer_nam]]; ?></td>
							<td><? echo $jobDataArr[style_ref_no]; ?></td>
							<td><? echo $jobDataArr[job_no]; ?></td>
							<td align="center"><? echo $jobDataArr[po_quantity_pcs]; ?></td>
							<td title="YarnTransfer=<? echo $yarn_transfer_cost;?>"><? echo number_format($yarnCost=$YarnData['acl']['amount']+$yarn_transfer_cost,4); ?></td>
							<td><? echo number_format($fpCost=($fabPurArr['acl']['woven']['amount']+$fabPurArr['acl']['knit']['amount']),4); ?></td>
							<td><? echo number_format($knittingCost=$knitData['acl']['amount'],4); ?></td>
							<td><? echo number_format($dfCost=$finishData['acl']['amount'],4); ?></td>
							<td><? echo number_format($trimsCost=$trimData['acl']['amount'],4); ?></td>
							<td><? echo number_format($embtCost=$embData['acl']['amount'],4); ?></td>
							<td><? echo number_format($washCost=$washData['acl']['amount'],4); ?></td>
							<td><? echo number_format($commissionCost=$commiData['pre']['amount'],4); ?></td>
							<td><? echo number_format($commercialCost=$commaData['pre']['amount'],4); ?></td>
							<td><? echo number_format($freightCost=$otherData['pre']['freight']['amount'],4); ?></td>
							
                            <td><? echo number_format($testingCost=$otherData['pre']['lab_test']['amount'],4); ?></td>
                            
                            
                            <td><? echo number_format($inspectionCost=$otherData['pre']['inspection']['amount'],4); ?></td>
                            <td><? echo number_format($courierCost=$otherData['pre']['currier_pre_cost']['amount'],4); ?></td>
                            
                            <td><? echo number_format($yarnCost+$fpCost+$knittingCost+$dfCost+$trimsCost+$embtCost+$washCost+$commissionCost+$commercialCost+$freightCost+$testingCost+$inspectionCost+$courierCost,4); ?></td>
                            
                            
						</tr>
					
                </tbody>
            </table>
        </fieldset>
    </div>    
    <?	
	
	
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



if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=11 and report_id=108 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#show_button_1').hide();\n";
	echo "$('#show_button_2').hide();\n";
	echo "$('#show_button_3').hide();\n";
	echo "$('#show_button_4').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{ 
			if($id==108){echo "$('#show_button_1').show();\n";}
			if($id==23){echo "$('#show_button_2').show();\n";}
			if($id==195){echo "$('#show_button_3').show();\n";}			
			if($id==150){echo "$('#show_button_4').show();\n";}
		}
	}
	
	exit();	
}



disconnect($con);







?>
