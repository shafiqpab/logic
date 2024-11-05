<?
//echo ini_get('memory_limit'). "<br/>";
//echo memory_get_usage() . "<br/>"; // 36640
//ini_set('memory_limit','3072M')."<br/>";
//$st= microtime(true);
//include('includes/common.php');
/*include('includes/class4/class.conditions.php');
include('includes/class4/class.reports.php');
include('includes/class4/class.commisions.php');
include('includes/class4/class.trims.php');
include('includes/class4/class.fabrics.php');
include('includes/class4/class.yarns.php');
include('includes/class4/class.conversions.php');
include('includes/class4/class.others.php');
include('includes/class4/class.emblishments.php');
include('includes/class4/class.commercials.php');
include('includes/class4/class.washes.php');*/
//include('includes/class4/cm_gmt_class.php');

	
function fnc_po_wise_cm_gmt_class($com,$po_id)
{
			
			//echo $com.'='.$po_id;die;
			$condition= new condition();
			$companyCond="";$companyCond2="";
			if($com>0)
			{
			$condition->company_name("in($com)");
			$companyCond="and company_id in($com)";
			$companyCond2="and a.company_name in($com)";
			}
			
			if($po_id!='' || $po_id!=0)
			{
				$condition->po_id_in("$po_id"); 
			}
			$condition->init();
			$fabric= new fabric($condition);
			$trim= new trims($condition);
			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$commercial= new commercial($condition);
			$other= new other($condition);
			$commision= new commision($condition);
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			//echo $fabric->getQuery();die;
			$fabric_amount_arr=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
			//print_r($fabric_amount_arr);
			$commision_ord_cost_arr=$commision->getAmountArray_by_order();
			$commision_item_cost_arr=$commision->getAmountArray_by_orderAndItemid();
			$trim_cost_arr=$trim->getAmountArray_by_order();
			$yarn_cost_arr=$yarn->getOrderWiseYarnAmountArray();
			$conv_po_amount_arr=$conversion->getAmountArray_by_order();
			$conv_process_amount_arr=$conversion->getAmountArray_by_orderAndProcess();
			$emblishment_amount_arr=$emblishment->getAmountArray_by_order();
			$wash_amount_arr=$wash->getAmountArray_by_order();
			$commercial_amount_arr=$commercial->getAmountArray_by_orderAndItemid();
			$other_cost_arr=$other->getAmountArray_by_order();
		
			$sql_std_para=sql_select("select id,company_id,interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date,operating_expn from lib_standard_cm_entry where  status_active=1 and is_deleted=0 and cost_per_minute>0  and operating_expn>0  $companyCond order by id desc");	
			$financial_para_arr=array();
			foreach($sql_std_para as $row )
			{
				$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
						$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
						$financial_para_arr[$row[csf('company_id')]][$newdate]['operating_expn']=$row[csf('operating_expn')];
				}
				//$cost_per_minute=$row[csf("cost_per_minute")];
			}
			unset($sql_std_para);
			$poIds=chop($po_id,','); $po_cond_in="";
			$po_ids=count(array_unique(explode(",",$po_id)));
			if($po_ids>1000)
			{
			$po_cond_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$po_cond_in.=" b.id in($ids) or"; 
			}
			$po_cond_in=chop($po_cond_in,'or ');
			$po_cond_in.=")";
			}
			else
			{
			$po_cond_in=" and b.id in($poIds)";
			}
			 $sql_po="select a.job_no,a.company_name, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.plan_cut,b.po_total_price,c.costing_per,c.costing_date,d.studio_cost,d.studio_percent from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_pre_cost_dtls d  where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id and c.job_id=d.job_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $po_cond_in  $companyCond2 order  by b.id ";
			$po_result=sql_select($sql_po);
		//	$tot_po_qty_pcs=$total_fob_value=$tot_net_fob_value=0;
			$k=1;$tot_cm_gmt_value_dzn_arr=array();
			foreach($po_result as $row)
			{
				$costing_per=$row[csf('costing_per')];
				if($costing_per==1) $dzn_qnty=12;
				else if($costing_per==3) $dzn_qnty=12*2;
				else if($costing_per==4) $dzn_qnty=12*3;
				else if($costing_per==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
			
				$tot_po_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$tot_po_qty_pcs_dzn=$tot_po_qty_pcs/12;
				$total_fob_value=$row[csf('po_total_price')];
				$costing_date=$row[csf('costing_date')];$studio_percent=$row[csf('studio_percent')];
				$poid=$row[csf('po_id')];
				$costing_date=change_date_format($costing_date,'','',1);
				$operating_expn=0;
				$operating_expn=$financial_para_arr[$row[csf('company_name')]][$costing_date]['operating_expn'];
			
			
			
			$freight_cost=$other_cost_arr[$poid]['freight'];
			
			 $tot_dye_chemi_process_amount=array_sum($conv_process_amount_arr[$poid][101]);
			 $tot_yarn_dye_process_amount=array_sum($conv_process_amount_arr[$poid][30]);
			 $tot_aop_process_amount=array_sum($conv_process_amount_arr[$poid][35]);
			 $tot_conv_cost= $tot_dye_chemi_process_amount+$tot_yarn_dye_process_amount+$tot_aop_process_amount;
			
			$lab_test=$other_cost_arr[$poid]['lab_test'];
			$inspection_cost=$other_cost_arr[$poid]['inspection'];
			$currier_cost=$other_cost_arr[$poid]['currier_pre_cost'];
			$certificate_cost=$other_cost_arr[$poid]['certificate_pre_cost'];
			
			$commission_foreign_cost=$commision_item_cost_arr[$poid][1];
			$commission_lc_cost=$commision_item_cost_arr[$poid][2];
			$commercial_lc_cost=$commercial_amount_arr[$poid][1];
			$commercial_without_lc_cost=$commercial_amount_arr[$poid][0]+$commercial_amount_arr[$poid][2]+$commercial_amount_arr[$poid][3]+$commercial_amount_arr[$poid][4];
	
			$yarn_po_cost=$yarn_cost_arr[$poid];
			$trims_po_cost=$trim_cost_arr[$poid];
			$conv_po_cost=array_sum($conv_po_amount_arr[$poid]);
			$cm_fabric_cost=$conv_po_cost-$tot_conv_cost;
			$fabric_amount=array_sum($fabric_amount_arr['knit']['grey'][$poid][2])+array_sum($fabric_amount_arr['woven']['grey'][$poid][2]);
			
			$net_fob_value=$total_fob_value-($commission_foreign_cost+$freight_cost+$commercial_lc_cost);
			$tot_operating_expense=($net_fob_value*$operating_expn)/100;
			$tot_studio_po_wise_cost=($net_fob_value*$studio_percent)/100;
			//echo $net_fob_value.',';
			$other_direct_expense=$lab_test+$inspection_cost+$currier_cost+$certificate_cost+$commission_lc_cost+$tot_studio_po_wise_cost;
		//tot_po_qty_pcs_dzn
			$wash_po_cost=$wash_amount_arr[$poid];
			$emblishment_po_cost=$emblishment_amount_arr[$poid]+$wash_po_cost;
			$total_btb=$fabric_amount+$yarn_po_cost+$tot_conv_cost+$trims_po_cost+$commercial_without_lc_cost+$emblishment_po_cost+$tot_operating_expense+$other_direct_expense;
			$tot_cm_gmt_value=$net_fob_value-($cm_fabric_cost+$total_btb);
			$tot_cm_gmt_value_dzn=$tot_cm_gmt_value/$tot_po_qty_pcs_dzn;
			
			$tot_cm_gmt_value_dzn_arr[$poid]['value']=$tot_cm_gmt_value;
			$tot_cm_gmt_value_dzn_arr[$poid]['dzn']=$tot_cm_gmt_value_dzn;
			$tot_cm_gmt_value_dzn_arr[$poid]['pcs']=$tot_cm_gmt_value_dzn/$dzn_qnty;
				$k++;
			}
			unset($po_result);
			return $tot_cm_gmt_value_dzn_arr;

die;
}
?>
