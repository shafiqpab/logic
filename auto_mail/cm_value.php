<?


require_once('../includes/class4/class.conditions.php');
require_once('../includes/class4/class.reports.php');
require_once('../includes/class4/class.yarns.php');
require_once('../includes/class4/class.conversions.php');
require_once('../includes/class4/class.emblishments.php');
require_once('../includes/class4/class.commisions.php');
require_once('../includes/class4/class.commercials.php');
require_once('../includes/class4/class.others.php');
require_once('../includes/class4/class.trims.php');
require_once('../includes/class4/class.fabrics.php');
require_once('../includes/class4/class.washes.php');

//$txt_job_no=288;


function cm_value($company_arr,$po_arr){
	$company_name=implode(',',$company_arr);
	$txt_order_no=implode(',',$po_arr);


	if($company_name){
		$company_con_name_a=" and a.company_name in($company_name)";
		$company_con_id=" and company_id in($company_name)";
	}
	else
	{
		$company_con_name_a="";
		$company_con_id="";
	}




	
	if(count($po_arr)>999)
	{
		$data_arr_chunk=array_chunk($po_arr,999) ;
		$p=0;
		foreach($data_arr_chunk as $chunk_arr)
		{
			if($p==0) $where_con =" and ( b.id in(".implode(",",$chunk_arr).")"; 
			else  $where_con .=" or b.id in(".implode(",",$chunk_arr).")";
			$p=1;
		}
		$where_con .=")";
	}
	else
	{
		$where_con=" and b.id in($txt_order_no)";
	}
  	

	$sql_budget="select a.total_set_qnty, a.company_name, a.job_no, b.plan_cut, b.is_confirmed, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b , wo_pre_cost_mst c where a.job_no=b.job_no_mst and  c.job_no=b.job_no_mst and  a.job_no=c.job_no $company_con_name_a and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $where_con";
	
	$result_sql_budget=sql_select($sql_budget);
	foreach($result_sql_budget as $row )
	{ 
		$job_data_arr['total_set_qnty'][$row[csf('job_no')]]=$row[csf('total_set_qnty')];
		$job_data_arr['job_no'][$row[csf('job_no')]]=$row[csf('job_no')];

	}



		$cpm_arr=array();
		$sql_cpm=sql_select("select applying_period_date, applying_period_to_date, cost_per_minute,company_id from lib_standard_cm_entry where is_deleted=0 and status_active=1 $company_con_id ");//$date_max_profit
		foreach($sql_cpm as $cpMrow )
		{
			$applying_period_date=change_date_format($cpMrow[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($cpMrow[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$cpm_arr[$cpMrow[csf('company_id')]][$newdate]['cpm']=$cpMrow[csf('cost_per_minute')];
			}
		}


//---------------------------------------------------
	if(count($job_data_arr['job_no'])>999)
	{
		$data_arr_chunk=array_chunk($job_data_arr['job_no'],999) ;
		$p=0;
		foreach($data_arr_chunk as $chunk_arr)
		{
			if($p==0) $where_con =" and ( job_no in('".implode("','",$chunk_arr)."')"; 
			else  $where_con .=" or job_no in('".implode("','",$chunk_arr)."')";
			$p=1;
		}
		$where_con .=")";
	}
	else
	{
		$where_con=" and job_no in('".implode("','",$job_data_arr['job_no'])."')";
	}
	
	$smv_eff_arr=array(); $costing_library=array();
	$sql_smv_cpm=sql_select("select job_no, costing_date, sew_smv, sew_effi_percent,exchange_rate from wo_pre_cost_mst where status_active=1 and is_deleted=0 $where_con ");
	foreach($sql_smv_cpm as $smv_cpm )
	{
		
		$costing_library[$smv_cpm[csf('job_no')]]=$smv_cpm[csf('costing_date')];
		$smv_eff_arr[$smv_cpm[csf('job_no')]]['smv']=$smv_cpm[csf('sew_smv')];
		$smv_eff_arr[$smv_cpm[csf('job_no')]]['eff']=$smv_cpm[csf('sew_effi_percent')];
		$smv_eff_arr[$smv_cpm[csf('job_no')]]['exc_rate']=$smv_cpm[csf('exchange_rate')]; // new
		
		$smv_eff_arr[$smv_cpm[csf('job_no')]]['cpm']=$cpm_arr[change_date_format($smv_cpm[csf('costing_date')],'','',1)]['cpm'];
		$smv_eff_arr[$smv_cpm[csf('job_no')]]['finPercent']=$finance_arr[change_date_format($smv_cpm[csf('costing_date')],'','',1)]['finPercent'];
	}
	unset($sql_smv_cpm);

//----------------------------------------------
	
	if(count($po_arr)>999)
	{
		$data_arr_chunk=array_chunk($po_arr,999) ;
		$p=0;
		foreach($data_arr_chunk as $chunk_arr)
		{
			if($p==0) $where_con =" and ( b.id in(".implode(",",$chunk_arr).")"; 
			else  $where_con .=" or b.id in(".implode(",",$chunk_arr).")";
			$p=1;
		}
		$where_con .=")";
	}
	else
	{
		$where_con=" and b.id in($txt_order_no)";
	}
	//echo $where_con;die;
	
	$sql="select c.job_no,c.fabric_cost,b.id as po_id,c.deffdlc_cost,c.incometax_cost,c.interest_cost from wo_po_break_down b,wo_pre_cost_dtls c
	where b.job_no_mst=c.job_no $where_con"; //echo $sql;die;
	$fabric_cost_result=sql_select( $sql );

	foreach($fabric_cost_result as $row)
	{
		$total_set_qnty=$job_data_arr['total_set_qnty'][$row[csf('job_no')]];
		
		$fabric_cost_pcs_arr[$row[csf('po_id')]]=($row[csf('fabric_cost')]/12)/$total_set_qnty;
		$deffdlc_cost_arr[$row[csf('po_id')]]=($row[csf('deffdlc_cost')]/12)/$total_set_qnty;
		$interest_cost_arr[$row[csf('po_id')]]=($row[csf('interest_cost')]/12)/$total_set_qnty;
		$incometax_cost_arr[$row[csf('po_id')]]=($row[csf('incometax_cost')]/12)/$total_set_qnty;

	}

 
		 $condition= new condition();
		 if(str_replace("'","",$cbo_company_name)>0){
			 $condition->company_name("in($cbo_company_name)");
		 }
		 if(str_replace("'","",$txt_order_no)!='')
		 {
			//$condition->po_id("in($txt_order_no)");
			$condition->po_id_in($txt_order_no);
		 }
		

		 $condition->init();

		 $costing_per_arr=$condition->getCostingPerArr();


		$fabric= new fabric($condition);
		 //echo $fabric->getQuery(); die;
		$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();

		//print_r($costing_per_arr);
		//$fabric->unsetDataArray();

		$yarn= new yarn($condition);

		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		//print_r($yarn_costing_arr);

		$yarn= new yarn($condition);
		$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
		$yarn= new yarn($condition);
		$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
		$yarn= new yarn($condition);
		$yarn_required=$yarn->getOrderWiseYarnQtyArray();


		//$yarn->unsetDataArray();
		//echo $yarn->getQuery(); die;
		$conversion= new conversion($condition);
		//echo $conversion->getQuery();
		$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
		$conversion_costing_po_arr=$conversion->getAmountArray_by_order();

		$conversion_qty= new conversion($condition);
		$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();

		//$conversion->unsetDataArray();

		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_order();
		//$trims->unsetDataArray();

		$emblishment= new emblishment($condition);
		$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order();

		$wash= new wash($condition);
		$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

		$knit_cost_arr=array(1,2,3,4);
		$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
		$aop_cost_arr=array(35,36,37,40);
		$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
		$washing_cost_arr=array(140,142,148,64);
		$washing_qty_arr=array(140,142,148,64);

		//*************************************************************************************


		$order_cm_val_arr=array();
		foreach($result_sql_budget as $row )
		{ 
			
			//$dzn_qnty=$costing_per_arr[$row[csf('job_no')]];
			//$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
			$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
			//$order_qty=$row[csf('po_quantity')];
			//$order_uom=$row[csf('order_uom')];

			//$yarn_req=$yarn_required[$row[csf('po_id')]];
			//$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
			$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
			$order_value=$row[csf('po_total_price')];
			
			
			
		
			$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];
			$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
			$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
			$yarn_cost_percent=($yarn_costing/$order_value)*100;

			$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
			$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
			$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;

			$conv_cost_poWise=array_sum($conversion_costing_po_arr[$row[csf('po_id')]]);
			$tot_fabric_cost=$yarn_costing+$fab_purchase+$conv_cost_poWise;


			$knit_cost=0;$knite_qty=0;
			foreach($knit_cost_arr as $process_id)
			{
				$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);
				$knite_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$process_id]);
			}

			$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;

			$fabric_dyeing_cost=0;$fabric_dyeing_qty=0;
			foreach($fabric_dyeingCost_arr as $fab_process_id)
			{
				$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);
				$fabric_dyeing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$fab_process_id]);
			}

			$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
			$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
			$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
			$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);

			$fabric_finish=0;
			foreach($fab_finish_cost_arr as $fin_process_id)
			{
				$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);
			}

			$all_over_cost=0;
			foreach($aop_cost_arr as $aop_process_id)
			{
				$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);
			}

			$washing_cost=0;
			foreach($washing_cost_arr as $w_process_id)
			{
				$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);
			}

			$washing_qty=0;
			foreach($washing_qty_arr as $w_process_id)
			{
				$washing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$w_process_id]);
			}



			$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
			$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
			$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
			$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
			$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
			$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
			$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
			$local=$commission_costing_arr[$row[csf('po_id')]][2];
			$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
			$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
			$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
			$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
			$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
			$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];

			$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
			$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;

			$finance_chrg=$order_value*$smv_eff_arr[$row[csf('job_no')]]['finPercent']/100;

			$dfc_cost=$other_costing_arr[$row[csf('po_id')]]['deffdlc_cost'];
			$interest_cost=$interest_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;
			$incometax_cost=$incometax_cost_arr[$row[csf('po_id')]]*$order_qty_pcs;


			$total_material_cost=$tot_fabric_cost+$trim_amount+$print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount+$commercial_cost+$test_cost+$freight_cost+$inspection+$certificate_cost+$finance_chrg+$currier_cost+$dfc_cost+$interest_cost+$incometax_cost;

			
			//$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
			//$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
			//$cm_value=$net_order_val-$others_cost_value;



			$tot_forg_local=$foreign+$local; 					 
			$tot_netFOB=$order_value-$tot_forg_local; 
			$tot_cmValue=$tot_netFOB-$total_material_cost; 
			
			$order_cm_val_arr[$row[csf('po_id')]]=$tot_cmValue;
		}


	return $order_cm_val_arr;
}



	

?>
		
		
		
		

