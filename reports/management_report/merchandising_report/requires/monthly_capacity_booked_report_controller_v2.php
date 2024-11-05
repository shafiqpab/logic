<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$date=date('Y-m-d');


if($action=="get_pre_cost_data")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	list($cbo_company_name,$location_id,$cbo_year_name,$cbo_month,$cbo_end_year_name,$cbo_month_end,$cbo_type)=explode('_',$data);
	
	
	
	
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}

	$tot_month = datediff( 'm', $s_date,$e_date);
	
	if($cbo_year_name==$cbo_end_year_name)
	{
		$tot_month=$tot_month;
	}
	else
	{
		$tot_month=$tot_month-1;
	}
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($s_date,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
	}
	
	$locatin_cond="";
	if($location_id>0) $locatin_cond=" AND a.location_name='$location_id'";
	
/*	$sql_con_po="SELECT a.set_smv, a.total_set_qnty, b.id as po_id, b.pub_shipment_date as shipment_date, b.po_total_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name=$cbo_company_name $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";*/
	
if($cbo_type==1){	
	$sql_con_po="SELECT a.set_smv, a.total_set_qnty, b.id as po_id, b.pub_shipment_date as shipment_date, b.po_total_price
		FROM wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c
		WHERE a.job_no=b.job_no_mst AND b.job_no_mst=c.job_no AND a.job_no=c.job_no AND c.entry_from=158 AND a.company_name=$cbo_company_name $locatin_cond AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";	
}
else
{
	$dateCon="and ((d.TASK_FINISH_DATE between '$s_date' and '$e_date') or (d.TASK_START_DATE  between '$s_date' and '$e_date' ))";
	
	
	$sql_con_po="SELECT a.set_smv, a.total_set_qnty, b.id as po_id, b.pub_shipment_date as shipment_date, b.po_total_price
		FROM wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c,TNA_PROCESS_MST d
		WHERE a.job_no=b.job_no_mst AND b.job_no_mst=c.job_no AND a.job_no=c.job_no AND c.entry_from=158 AND a.company_name=$cbo_company_name $locatin_cond $dateCon and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no=d.JOB_NO and b.id=d.PO_NUMBER_ID and d.TASK_NUMBER=86 and d.TASK_TYPE=1";
}
	
	// echo $sql_con_po;die; and b.id=41672
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$s_date)!='' && str_replace("'","",$e_date)!='')
	{
		$condition->pub_shipment_date(" between '$s_date' and '$e_date'");
	}
	$condition->init();
	
	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_order();

	$commission= new commision($condition);
	$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
	
	$commercial= new commercial($condition);
	$commercial_costing_arr=$commercial->getAmountArray_by_order();
	
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
	
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
	
	$conversion= new conversion($condition);
	$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
	
	$trims= new trims($condition);
	$trims_costing_arr=$trims->getAmountArray_by_order();
	
	$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	
	$wash= new wash($condition);
	$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
	

	//$conversion_qty= new conversion($condition);
	//$conversion_qty_arr_process = $conversion_qty->getQtyArray_by_orderAndProcess();


	$knit_cost_arr=array(1,2,3,4);
	//$fabric_dyeingCost_arr=array(25,26,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79);
	$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
	
	
	//$aop_cost_arr=array(35,36,37);
	$aop_cost_arr=array(35,36,37,40);
	//$fab_finish_cost_arr=array(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129);
	$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
	
	
	//$washing_cost_arr=array(64,82,89);
	$washing_cost_arr=array(140,142,148,64);
	//$washing_qty_arr=array(140,142,148,64);
	
	$po_arr=array();
	$sql_data_po=sql_select($sql_con_po);
	foreach( $sql_data_po as $row_po)
	{
		$date_key=date("Y-m",strtotime($row_po[csf("shipment_date")]));
		$year_key=date("Y",strtotime($row_po[csf("shipment_date")]));
		
		$ex_month='';
		$ex_month=explode('-',$date_key);
		$monthId=0;
		
		if($ex_month[1]==10)
			$monthId=$ex_month[1];
		else
			$monthId=str_replace('0','',$ex_month[1]);
		
		$confirm_qty=0; $projected_qty=0;
		//$confirm_qty=($row_po[csf("confirm_qty")]*$row_po[csf("total_set_qnty")])*$row_po[csf("set_smv")];
		//$projected_qty=($row_po[csf("projected_qty")]*$row_po[csf("total_set_qnty")])*$row_po[csf("set_smv")];
		
/*		$confirm_qty=($row_po[csf("confirm_qty")])*$row_po[csf("set_smv")];
		$projected_qty=($row_po[csf("projected_qty")])*$row_po[csf("set_smv")];
			
		//$po_arr[$date_key]['booked_sah_con']+=$confirm_qty*$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
		//$po_arr[$date_key]['booked_sah_proj']+=$projected_qty*$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
		
		$po_arr[$date_key]['booked_sah_con']+=$confirm_qty;
		$po_arr[$date_key]['booked_sah_proj']+=$projected_qty;
		$po_arr[$date_key]['booked_eqv_qty']+=($confirm_qty+$projected_qty)/$capacity_arr[$ex_month[0]][$monthId]['basic_smv'];
		
		//$cm_value=$other_costing_arr[$row_po[csf('po_id')]]['cm_cost'];
		//$po_arr[$date_key]['cm_value']+=$cm_value;
		$po_arr[$date_key]['confirm_value']+=$row_po[csf("confirm_value")];
		$po_arr[$date_key]['projected_value']+=$row_po[csf("projected_value")];
		$po_arr[$date_key]['confirm_qty']+=$row_po[csf("confirm_qty")]*$row_po[csf("total_set_qnty")];
		$po_arr[$date_key]['projected_qty']+=$row_po[csf("projected_qty")]*$row_po[csf("total_set_qnty")];
*/		
		
		
		// data from class start
		
		$yarn_costing=$yarn_costing_arr[$row_po[csf('po_id')]];
		$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row_po[csf('po_id')]]);
		$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row_po[csf('po_id')]]);
		$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
		$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row_po[csf('po_id')]][30]);
		$heat_setting_cost=array_sum($conversion_costing_arr_process[$row_po[csf('po_id')]][33]);
		$trim_amount= $trims_costing_arr[$row_po[csf('po_id')]];
		$test_cost=$other_costing_arr[$row_po[csf('po_id')]]['lab_test'];
		$print_amount=$emblishment_costing_arr_name[$row_po[csf('po_id')]][1];
		$embroidery_amount=$emblishment_costing_arr_name[$row_po[csf('po_id')]][2];
		$special_amount=$emblishment_costing_arr_name[$row_po[csf('po_id')]][4];
		$other_amount=$emblishment_costing_arr_name[$row_po[csf('po_id')]][5];
		$wash_cost=$emblishment_costing_arr_name_wash[$row_po[csf('po_id')]][3];
		$commercial_cost=$commercial_costing_arr[$row_po[csf('po_id')]];
		$foreign=$commission_costing_arr[$row_po[csf('po_id')]][1];
		$local=$commission_costing_arr[$row_po[csf('po_id')]][2];
		$freight_cost=$other_costing_arr[$row_po[csf('po_id')]]['freight'];
		$inspection=$other_costing_arr[$row_po[csf('po_id')]]['inspection'];
		$certificate_cost=$other_costing_arr[$row_po[csf('po_id')]]['certificate_pre_cost'];
		$common_oh=$other_costing_arr[$row_po[csf('po_id')]]['common_oh'];
		$currier_cost=$other_costing_arr[$row_po[csf('po_id')]]['currier_pre_cost'];
		$cm_cost=$other_costing_arr[$row_po[csf('po_id')]]['cm_cost'];
		$order_value=$row_po[csf('po_total_price')];
		
		
		$deffdlc_cost=$other_costing_arr[$row_po[csf('po_id')]]['deffdlc_cost'];
		$design_cost=$other_costing_arr[$row_po[csf('po_id')]]['design_cost'];
		$studio_cost=$other_costing_arr[$row_po[csf('po_id')]]['studio_cost'];
		
		
		
		$knit_cost=0;
		foreach($knit_cost_arr as $process_id)
		{
			$knit_cost+=array_sum($conversion_costing_arr_process[$row_po[csf('po_id')]][$process_id]);	
		}
		
		$washing_cost=0;
		foreach($washing_cost_arr as $w_process_id)
		{
			$washing_cost+=array_sum($conversion_costing_arr_process[$row_po[csf('po_id')]][$w_process_id]);	
		}
		
		$all_over_cost=0;
		foreach($aop_cost_arr as $aop_process_id)
		{
			$all_over_cost+=array_sum($conversion_costing_arr_process[$row_po[csf('po_id')]][$aop_process_id]);	
		}
		
		$fabric_dyeing_cost=0;
		foreach($fabric_dyeingCost_arr as $fab_process_id)
		{
			$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row_po[csf('po_id')]][$fab_process_id]);	
		}
		
		$fabric_finish=0;
		foreach($fab_finish_cost_arr as $fin_process_id)
		{
			$fabric_finish+=array_sum($conversion_costing_arr_process[$row_po[csf('po_id')]][$fin_process_id]);	
		}
		
		
		/*$washing_qty=0;
		foreach($washing_qty_arr as $w_process_id)
		{
			$washing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$w_process_id]);	
		}*/
						
		

		
		$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost + $deffdlc_cost+$design_cost+$studio_cost;  //
		
		$others_cost_value = $total_cost -($cm_cost+$freight_cost+$commercial_cost+($foreign+$local));
		$net_order_val=$order_value-(($foreign+$local)+$commercial_cost+$freight_cost);
		$cm_value=$net_order_val-$others_cost_value;
		//echo $cm_value.'=<br>=';
		//$po_arr[$date_key]['cm_value']+=$cm_value;
		$po_arr[$date_key]+=number_format($cm_value,6,'.','');
		$po_wise_cm_value_arr[$row_po[csf('po_id')]]+=number_format($cm_value,6,'.','');
		
	}
	
	if($cbo_type==1){
		foreach($po_arr as $dateKey=>$dateValue){
			$dataArr[$dateKey]=$dateKey.'**'.$dateValue;	
		}
	}
	else{
	
		foreach($po_wise_cm_value_arr as $poKey=>$cmValue){
			$dataArr[$poKey]=$poKey.'**'.$cmValue;	
		}
	}
	
	
	
	
	echo implode(',',$dataArr);
	exit();	
}


?>