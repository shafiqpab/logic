<?php
date_default_timezone_set("Asia/Dhaka");
require_once('includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

if($user_id=="" || $user_id==0) $user_id=9999;
// Dia Upper
$con = connect();
/*$sqldiaup="select id as ID, upper(dia_width) as  DIA_WIDTH from wo_pre_cos_fab_co_avg_con_dtls where 1=1";
$sqldiaupRes = sql_select($sqldiaup); $i=0; $a=0;
foreach($sqldiaupRes as $row)
{
	$i++; $a++;
	//echo "update wo_pre_cos_fab_co_avg_con_dtls set dia_width='".$row["DIA_WIDTH"]."' where id='".$row["ID"]."';<br>";
	$query=execute_query("update wo_pre_cos_fab_co_avg_con_dtls set dia_width='".$row["DIA_WIDTH"]."' where id='".$row["ID"]."'");
}
unset($sqldiaupRes);
echo $i;// die;
*/
/*$sqlbookdiaup="select id as ID, upper(dia_width) as DIA_WIDTH from WO_BOOKING_DTLS where 1=1 and BOOKING_TYPE = 4";
$sqlbookdiaupRes = sql_select($sqlbookdiaup); $i=0;
foreach($sqlbookdiaupRes as $row)
{
	$i++;
	//echo "update wo_pre_cos_fab_co_avg_con_dtls set dia_width='".$row["DIA_WIDTH"]."' where id='".$row["ID"]."';<br>";
	$query=execute_query("update WO_BOOKING_DTLS set dia_width='".$row["DIA_WIDTH"]."' where id='".$row["ID"]."'");
}
unset($sqlbookdiaupRes);*/

$sqlpidiaup="select id as ID, upper(dia_width) as DIA_WIDTH from COM_PI_ITEM_DETAILS where 1=1";
$sqlpidiaupRes = sql_select($sqlpidiaup); $i=0;
foreach($sqlpidiaupRes as $row)
{
	if($row["DIA_WIDTH"]!="")
	{
	$i++;
	//echo "update wo_pre_cos_fab_co_avg_con_dtls set dia_width='".$row["DIA_WIDTH"]."' where id='".$row["ID"]."';<br>";
	$query=execute_query("update COM_PI_ITEM_DETAILS set dia_width='".$row["DIA_WIDTH"]."' where id='".$row["ID"]."'");
	}
}
unset($sqlpidiaupRes);

echo $i;// die;

oci_commit($con); disconnect($con); die;



//PO Details
$job_no='';
//$job_no='JMF-23-00155';//FAL-21-01507
$jobYearCond="";
//$jobYearCond=" and to_char(a.insert_date,'YYYY')='2023'";

$currdate=date("d-M-Y", time() - 86400);
//$currdate="30-Nov-2023";
$predate="01-Feb-2023";
//echo $currdate; die;
$date_cond="";
//$date_cond="and (d.insert_date between '".$predate."' and '".$currdate." 11:59:59 PM' or d.update_date between '".$predate."' and '".$currdate." 11:59:59 PM')";

if($job_no!="") { $jobCond="and a.job_no='$job_no'"; $jobCondS="and job_no='$job_no'"; } else { $jobCond=""; $jobCondS="";}
$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id  and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $jobCond $jobYearCond $date_cond";
//echo $sqlpo; die; //and a.job_no='$job_no'
$sqlpoRes = sql_select($sqlpo);
//print_r($sqlpoRes); die;
$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
foreach($sqlpoRes as $row)
{
	$costingPerQty=0;
	if($row['COSTING_PER']==1) $costingPerQty=12;
	elseif($row['COSTING_PER']==2) $costingPerQty=1;	
	elseif($row['COSTING_PER']==3) $costingPerQty=24;
	elseif($row['COSTING_PER']==4) $costingPerQty=36;
	elseif($row['COSTING_PER']==5) $costingPerQty=48;
	else $costingPerQty=0;
	
	$costingPerArr[$row['JOB_ID']]=$costingPerQty;
	
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
	
	$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	
	$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	if($jobid=="") $jobid=$row['JOB_ID']; else $jobid.=','.$row['JOB_ID'];
}
unset($sqlpoRes);
$ujobid=array_unique(explode(",",$jobid));
$cjobid=count($ujobid);
$jobIds=implode(",",$ujobid);
$jobidCond=''; $jobidCondition='';
if($db_type==2 && $cjobid>1000)
{
	$jobidCond=" and (";
	$jobidCondition=" and (";
	$jobIdsArr=array_chunk(explode(",",$jobIds),999);
	foreach($jobIdsArr as $ids)
	{
		$ids=implode(",",$ids);
		$jobidCond.=" a.job_id in($ids) or"; 
		$jobidCondition.=" job_id in($ids) or"; 
	}
	$jobidCond=chop($jobidCond,'or ');
	$jobidCond.=")";
	
	$jobidCondition=chop($jobidCondition,'or ');
	$jobidCondition.=")";
}
else
{
	if($jobIds==""){ $jobidCond=""; } else { $jobidCond=" and a.job_id in($jobIds)"; }
	if($jobIds==""){ $jobidCondition=""; } else { $jobidCondition=" and job_id in($jobIds)"; }
}

//die;
//echo "ff"; die;
//Item Ratio Details
$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $jobCondS $jobidCondition";
//echo $gmtsitemRatioSql; die;
$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
$jobItemRatioArr=array();
foreach($gmtsitemRatioSqlRes as $row)
{
	$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
}
unset($gmtsitemRatioSqlRes);

//echo "ff"; die;
//Contrast Details
$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";
//echo $sqlContrast; die;
$sqlContrastRes = sql_select($sqlContrast);
$sqlContrastArr=array();
foreach($sqlContrastRes as $row)
{
	$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
}
unset($sqlContrastRes);

//Stripe Details
$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";
//echo $sqlStripe; die;
$sqlStripeRes = sql_select($sqlStripe);
$sqlStripeArr=array();
foreach($sqlStripeRes as $row)
{
	$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
	$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
}
unset($sqlStripeRes);
//echo "ff"; die;
//Fabric Details
$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobCond $jobidCond";
//echo $sqlfab; die;
$sqlfabRes = sql_select($sqlfab);
$fabIdWiseGmtsDataArr=array();
foreach($sqlfabRes as $row)
{
	$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
	
	$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
	$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
	$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
	$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
	$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
	
	$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
	$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
	
	$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
	$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
	
	$finAmt=$finReq*$row['RATE'];
	$greyAmt=$greyReq*$row['RATE'];
	
	//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
	if($itemRatio!="")
	{
		if($row['FABRIC_SOURCE']==1)
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_amt']+=$finAmt;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_amt']+=$greyAmt;
		}
		else if($row['FABRIC_SOURCE']==2)
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchfin_qty']+=$finReq;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchgrey_qty']+=$greyReq;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchfin_amt']+=$finAmt;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchgrey_amt']+=$greyAmt;
		}
	}
}
unset($sqlfabRes); 
//echo "ff"; die;
//print_r($reqQtyAmtArr); die;
//Yarn Details
$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 

from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobCond $jobidCond";
//echo $sqlYarn;
$sqlYarnRes = sql_select($sqlYarn);
foreach($sqlYarnRes as $row)
{
	$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
	
	$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
	
	$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
	$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
	
	$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
	
	$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
	
	$yarnAmt=$yarnReq*$row['RATE'];
	
	//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
	if($itemRatio!="")
	{
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_qty']+=$yarnReq;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_amt']+=$yarnAmt;
	}
}
unset($sqlYarnRes); 
//print_r($reqQtyAmtArr); die;
//die;
//echo "ff"; die;
//Convaersion Details
$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobCond $jobidCond";
//echo $sqlConv; die;
$sqlConvRes = sql_select($sqlConv);
$convConsRateArr=array();
foreach($sqlConvRes as $row)
{
	$id=$row['CONVERTION_ID'];
	$colorBreakDown=$row['COLOR_BREAK_DOWN'];
	if($colorBreakDown !="")
	{
		$arr_1=explode("__",$colorBreakDown);
		for($ci=0;$ci<count($arr_1);$ci++)
		{
			$arr_2=explode("_",$arr_1[$ci]);
			$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
			$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
		}
	}
}
//echo "ff"; die;
foreach($sqlConvRes as $row)
{
	$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
	$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
	
	$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
	$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
	
	$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
	$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
	$consProcessId=$row['CONS_PROCESS'];
	$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
	
	if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
	{
		$qnty=0; $convrate=0;
		foreach($stripe_color as $stripe_color_id)
		{
			$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
			$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
			
			$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
			$qnty=($planQty/$itemRatio)*($requirment/$costingPer);

			if($convrate>0){
				$reqqnty+=$qnty;
				$convAmt+=$qnty*$convrate;
			}
		}
	}
	else
	{
		$convrate=$requirment=$reqqnty=0;
		$rateColorId=$row['COLOR_NUMBER_ID'];
		if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];

		if($row['COLOR_BREAK_DOWN']!="") $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate=$row['CHARGE_UNIT'];
		
		if($convrate>0){
			$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
			$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
			$reqqnty+=$qnty;
			$convAmt+=$qnty*$convrate;
		}
	}
	
	//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
	if($itemRatio!="")
	{
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
	}
}
unset($sqlConvRes); 
//print_r($reqQtyAmtArr); die;
//die;

//Trims Details
$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
where 1=1 and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobCond $jobidCond";
//echo $sqlTrim; die;
$sqlTrimRes = sql_select($sqlTrim);

foreach($sqlTrimRes as $row)
{
	$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
	
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
	
	$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
	//print_r($poCountryId);
	
	if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
	{
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		
		$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
		$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
		
		$consAmt=$consQnty*$row['RATE'];
		$consTotAmt=$consTotQnty*$row['RATE'];
	}
	else
	{
		$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
		$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		foreach($poCountryId as $countryId)
		{
			if(in_array($countryId, $countryIdArr))
			{
				$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				$consQty=$consTotQty=0;
				
				$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
				$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
				
				$consQnty+=$consQty;
				$consTotQnty+=$consTotQty;
				//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
				$consAmt+=$consQty*$row['RATE'];
				$consTotAmt+=$consTotQty*$row['RATE'];
			}
		}
	}
	
	//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
	if($itemRatio!="")
	{
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotqty']+=$consTotQnty;
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimamt']+=$consAmt;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotamt']+=$consTotAmt;
	}
}
unset($sqlTrimRes); 
//print_r($reqQtyAmtArr); die;

$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
where 1=1 and b.requirment>0 and
a.job_id=b.job_id and a.id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobCond $jobidCond";
//echo $sqlEmb; die;
$sqlEmbRes = sql_select($sqlEmb);

foreach($sqlEmbRes as $row)
{
	$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
	
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
	$budget_on=$row['BUDGET_ON'];
	
	$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
	//print_r($poCountryId);
	$calPoPlanQty=0;
	
	if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
	{
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		
		if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
		$consQty=0;
		$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
		$consQnty+=$consQty;
		
		$consAmt=$consQty*$row['RATE'];
	}
	else
	{
		$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
		$consQnty=$consAmt=0;
		foreach($poCountryId as $countryId)
		{
			if(in_array($countryId, $countryIdArr))
			{
				$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
				$consQty=0;
				$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
				$consQnty+=$consQty;
				//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
				$consAmt+=$consQty*$row['RATE'];
			}
		}
	}
	
	//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
	if($itemRatio!="")
	{
		if($row['EMB_NAME']==1)
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['print_qty']+=$consQnty;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['print_amt']+=$consAmt;
		}
		else if($row['EMB_NAME']==2)
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['embqty']+=$consQnty;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['embamt']+=$consAmt;
		}
		else if($row['EMB_NAME']==3)
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['washqty']+=$consQnty;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['washamt']+=$consAmt;
		}
		else if($row['EMB_NAME']==4)
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['special_works_qty']+=$consQnty;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['special_works_amt']+=$consAmt;
		}
		else if($row['EMB_NAME']==5)
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['gmts_dyeing_qty']+=$consQnty;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['gmts_dyeing_amt']+=$consAmt;
		}
		else
		{
			//$row['EMB_NAME']==99;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['others_qty']+=$consQnty;
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['others_amt']+=$consAmt;
		}
	}
}
unset($sqlEmbRes); 
//echo "<pre>";
//print_r($reqQtyAmtArr); die;

$con = connect();
if($db_type==0) mysql_query("BEGIN");
$flag=1;

$ridDel=execute_query("delete from BOM_PROCESS",0);

if($ridDel==1) $flag=1; 
else { 
	$flag=0; 
	oci_rollback($con); 
	echo "10**".$msgfail; 
	disconnect($con); die;
}

$field_array="id, job_id, po_id, po_qty, plan_qty, greyprod_qty, greyprod_amt, finprod_qty, finprod_amt, greypurch_qty, greypurch_amt, finpurch_qty, finpurch_amt, yarn_qty, yarn_amt, conv_qty, conv_amt, trim_qty, trim_amt, emb_qty, emb_amt, wash_qty,wash_amt,print_qty,print_amt,special_works_qty,special_works_amt,gmts_dyeing_qty,gmts_dyeing_amt,others_qty,others_amt, inserted_by, insert_date, status_active, is_deleted";
$idb=return_next_id("id","bom_process",1); $data_array=array(); $add_comma=0;


$i=0; 
foreach($reqQtyAmtArr as $jobid=>$jobdata)
{
	foreach($jobdata as $poid=>$podata)
	{
		$poQty=$planQty=$finProdQty=$greyProdQty=$finPurchQty=$greyPurchQty=$yarnQty=$convQty=$trimQty=$washQty=$printQty=$embQty=0;
		$finPordAmt=$greyPordAmt=$finPurchAmt=$greyPurchAmt=$yarnAmt=$convAmt=$trimAmt=$washAmt=$printAmt=$embAmt=0;
		
		$poQty=$podata['poqty']*1; 
		$planQty=$podata['planqty']*1;
		
		$finProdQty=$podata['prodfin_qty']*1; 
		$greyProdQty=$podata['prodgrey_qty']*1;
		$finPordAmt=$podata['prodfin_amt']*1; 
		$greyPordAmt=$podata['prodgrey_amt']*1;
		
		$finPurchQty=$podata['purchfin_qty']*1; 
		$greyPurchQty=$podata['purchgrey_qty']*1;
		$finPurchAmt=$podata['purchfin_amt']*1; 
		$greyPurchAmt=$podata['purchgrey_amt']*1;
		
		$yarnQty=$podata['yarn_qty']*1; 
		$yarnAmt=$podata['yarn_amt']*1;
		
		$convQty=$podata['conv_qty']*1; 
		$convAmt=$podata['conv_amt']*1;
		
		$trimQty=$podata['trimqty']*1; 
		$trimAmt=$podata['trimamt']*1;
		
		$washQty=$podata['washqty']*1; 
		$washAmt=$podata['washamt']*1;

		$embQty=$podata['embqty']*1; 
		$embAmt=$podata['embamt']*1;

		$print_qty=$podata['print_qty']*1;
		$print_amt=$podata['print_amt']*1;

		$special_works_qty=$podata['special_works_qty']*1;
		$special_works_amt=$podata['special_works_amt']*1;

		$gmts_dyeing_qty=$podata['gmts_dyeing_qty']*1;
		$gmts_dyeing_amt=$podata['gmts_dyeing_amt']*1;

		$others_qty=$podata['others_qty']*1;
		$others_amt=$podata['others_amt']*1;
		
		//print_qty,print_amt,special_works_qty,special_works_amt,gmts_dyeing_qty,gmts_dyeing_amt,others_qty,others_amt
		//echo $idb.'-'.$jobid.'-'.$poid.'-'.$poQty.'-'.$planQty.'-'.$finProdQty.'-'.$finPordAmt.'-'.$greyProdQty.'-'.$greyPordAmt.'-'.$finPurchQty.'-'.$finPurchAmt.'-'.$greyPurchQty.'-'.$greyPurchAmt.'-'.$yarnQty.'-'.$yarnAmt.'-'.$convQty.'-'.$convAmt.'-'.$trimQty.'-'.$trimAmt.'-'.$washQty.'-'.$washAmt.'-'.$embQty.'-'.$embAmt."<br>";
		
		//if ($add_comma!=0) $data_array .=","; $add_comma=0;
		//$data_array[$poid]="(".$idb.",'".$jobid."','".$poid."','".$poQty."','".$planQty."','".$greyProdQty."','".$greyPordAmt."','".$finProdQty."','".$finPordAmt."','".$greyPurchQty."','".$greyPurchAmt."','".$finPurchQty."','".$finPurchAmt."','".$yarnQty."','".$yarnAmt."','".$convQty."','".$convAmt."','".$trimQty."','".$trimAmt."','".$embQty."','".$embAmt."','".$washQty."','".$washAmt."','".$print_qty."','".$print_amt."','".$special_works_qty."','".$special_works_amt."','".$gmts_dyeing_qty."','".$gmts_dyeing_amt."','".$others_qty."','".$others_amt."','".$user_id."','".$pc_date_time."',1,0)";
		$data_array[$poid]=" INTO bom_process (".$field_array.") VALUES (".$idb.",'".$jobid."','".$poid."','".$poQty."','".$planQty."','".$greyProdQty."','".$greyPordAmt."','".$finProdQty."','".$finPordAmt."','".$greyPurchQty."','".$greyPurchAmt."','".$finPurchQty."','".$finPurchAmt."','".$yarnQty."','".$yarnAmt."','".$convQty."','".$convAmt."','".$trimQty."','".$trimAmt."','".$embQty."','".$embAmt."','".$washQty."','".$washAmt."','".$print_qty."','".$print_amt."','".$special_works_qty."','".$special_works_amt."','".$gmts_dyeing_qty."','".$gmts_dyeing_amt."','".$others_qty."','".$others_amt."','".$user_id."','".$pc_date_time."',1,0)";

		//echo $data_array[$poid];
		$idb++; $add_comma++; $i++;
	}
}
unset($reqQtyAmtArr);
//echo $i; die;
//print_r($data_array); die;

//echo "10**INSERT INTO bom_process (".$field_array.") VALUES ".$data_array; die;
//$rID=sql_insert("bom_process",$field_array,$data_array,1);


$msgsucc="$i BOM Process Success.";
$msgfail="$i BOM Process Fail.";


$data_bom=array_chunk($data_array,500);
foreach( $data_bom as $bomRows)
{
	//$query="INSERT ALL ".implode(",",$bomRows)." SELECT * FROM dual";
	$query="INSERT ALL ". implode("",$bomRows)." SELECT * FROM dual";
	$rID=execute_query($query);
	
	if($rID==1 && $flag==1) $flag=1;
	else if($rID==0) 
	{
		//$rID=sql_insert2("bom_process",$field_array,implode(",",$bomRows),1);
		echo "10**".$query;//INSERT INTO bom_process (".$field_array.") VALUES ".implode(",",$bomRows); //die;
		$flag=0;
		oci_rollback($con); 
		echo "10**".$msgfail; 
		disconnect($con); die;
	}
}
unset($data_array);
unset($data_bom);

if($db_type==0)
{
	if($flag==1)
	{
		mysql_query("COMMIT");  
		echo "0**".$msgsucc;
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "10**".$msgfail;
	}
}
else if($db_type==2)
{
	if($flag==1)
	{
		oci_commit($con);
		echo "0**".$msgsucc;
	}
	else
	{
		oci_rollback($con);
		echo "10**".$msgfail;
	}
}
disconnect($con);
die;


?> 