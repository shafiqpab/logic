<?php
date_default_timezone_set("Asia/Dhaka");
require_once('includes/common.php');

$sql1="SELECT    COLOR_ID    FROM     INV_PURCHASE_REQUISITION_DTLS    WHERE   COLOR_ID IN('91794')";
$sql1r = sql_select($sql1);
if(count($sql1r)>0) echo "INV_PURCHASE_REQUISITION_DTLS <br>";

$sql2="SELECT    COLOR    FROM     PRODUCT_DETAILS_MASTER    WHERE    COLOR    IN('91794')";
$sql2r = sql_select($sql2);
if(count($sql2r)>0) echo "PRODUCT_DETAILS_MASTER <br>";

$sql3="SELECT    COLOR_ID    FROM     PRO_BATCH_CREATE_MST    WHERE    COLOR_ID    IN('91794')";
$sql3r = sql_select($sql3);
if(count($sql3r)>0) echo "PRO_BATCH_CREATE_MST <br>";

$sql4="SELECT    COLOR_ID    FROM     PRO_FINISH_FABRIC_RCV_DTLS    WHERE    COLOR_ID    IN( '91794')";
$sql4r = sql_select($sql4);
if(count($sql4r)>0) echo "PRO_FINISH_FABRIC_RCV_DTLS <br>";

$sql5="SELECT    COLOR_ID    FROM     PRO_GREY_PROD_DELIVERY_DTLS    WHERE    COLOR_ID    IN( '91794')";
$sql5r = sql_select($sql5);
if(count($sql5r)>0) echo "PRO_GREY_PROD_DELIVERY_DTLS <br>";

$sql6="SELECT    COLOR_ID    FROM     PRO_RECIPE_ENTRY_MST    WHERE    COLOR_ID    IN( '91794')";
$sql6r = sql_select($sql6);
if(count($sql6r)>0) echo "PRO_RECIPE_ENTRY_MST <br>";

$sql7="SELECT    SAMPLE_COLOR    FROM     SAMPLE_DEVELOPMENT_DTLS    WHERE    SAMPLE_COLOR    IN( '91794')";
$sql7r = sql_select($sql7);
if(count($sql7r)>0) echo "SAMPLE_DEVELOPMENT_DTLS <br>";

$sql8="SELECT    COLOR_ID    FROM     SAMPLE_DEVELOPMENT_RF_COLOR    WHERE    COLOR_ID    IN( '91794')";
$sql8r = sql_select($sql8);
if(count($sql8r)>0) echo "SAMPLE_DEVELOPMENT_RF_COLOR <br>";

$sql9="SELECT    FABRIC_COLOR    FROM     SAMPLE_DEVELOPMENT_RF_COLOR    WHERE    FABRIC_COLOR    IN( '91794')";
$sql9r = sql_select($sql9);
if(count($sql9r)>0) echo "SAMPLE_DEVELOPMENT_RF_COLOR <br>";

$sql10="SELECT    COLOR_ID    FROM     SAMPLE_EX_FACTORY_COLORSIZE    WHERE   COLOR_ID IN( '91794')";
$sql10r = sql_select($sql10);
if(count($sql10r)>0) echo "SAMPLE_EX_FACTORY_COLORSIZE <br>";

$sql11="SELECT    COLOR_ID    FROM     SAMPLE_SEWING_OUTPUT_COLORSIZE    WHERE    COLOR_ID    IN( '91794')";
$sql11r = sql_select($sql11);
if(count($sql11r)>0) echo "SAMPLE_SEWING_OUTPUT_COLORSIZE <br>";

$sql12="SELECT    COLOR_ID    FROM     SUBCON_DELIVERY_DTLS    WHERE    COLOR_ID    IN( '91794')";
$sql12r = sql_select($sql12);
if(count($sql12r)>0) echo "SUBCON_DELIVERY_DTLS <br>";

$sql13="SELECT    COLOR_ID    FROM     SUBCON_ORD_BREAKDOWN    WHERE    COLOR_ID    IN( '91794')";
$sql13r = sql_select($sql13);
if(count($sql13r)>0) echo "SUBCON_ORD_BREAKDOWN <br>";

$sql14="SELECT    AOP_COLOR_ID    FROM     SUBCON_ORD_DTLS    WHERE    AOP_COLOR_ID    IN( '91794')";
$sql14r = sql_select($sql14);
if(count($sql14r)>0) echo "SUBCON_ORD_DTLS <br>";

$sql15="SELECT    ITEM_COLOR_ID    FROM     SUBCON_ORD_DTLS    WHERE    ITEM_COLOR_ID    IN( '91794')";
$sql15r = sql_select($sql15);
if(count($sql15r)>0) echo "SUBCON_ORD_DTLS <br>";

$sql16="SELECT    COLOR_ID    FROM     SUBCON_PRODUCTION_DTLS    WHERE    COLOR_ID    IN( '91794')";
$sql16r = sql_select($sql16);
if(count($sql16r)>0) echo "SUBCON_PRODUCTION_DTLS <br>";

$sql17="SELECT    COLOR_ID    FROM     SUB_MATERIAL_DTLS    WHERE    COLOR_ID    IN( '91794')";
$sql17r = sql_select($sql17);
if(count($sql17r)>0) echo "SUB_MATERIAL_DTLS <br>";

$sql18="SELECT    FABRIC_COLOR    FROM     WO_NON_ORD_SAMP_BOOKING_DTLS    WHERE    FABRIC_COLOR    IN( '91794')";
$sql18r = sql_select($sql18);
if(count($sql18r)>0) echo "WO_NON_ORD_SAMP_BOOKING_DTLS <br>";

$sql19="SELECT    GMTS_COLOR    FROM     WO_NON_ORD_SAMP_BOOKING_DTLS    WHERE    GMTS_COLOR    IN( '91794')";
$sql19r = sql_select($sql19);
if(count($sql19r)>0) echo "WO_NON_ORD_SAMP_BOOKING_DTLS <br>";

$sql20="SELECT    STRIPE_COLOR    FROM     WO_SHORT_STRIPE_COLOR    WHERE    STRIPE_COLOR    IN( '91794')";
$sql20r = sql_select($sql20);
if(count($sql20r)>0) echo "WO_SHORT_STRIPE_COLOR <br>";

$sql21="SELECT    YARN_COLOR    FROM     WO_YARN_DYEING_DTLS_FIN_PROD    WHERE    YARN_COLOR    IN( '91794')";
$sql21r = sql_select($sql21);
if(count($sql21r)>0) echo "WO_YARN_DYEING_DTLS_FIN_PROD <br>";

$sql22="SELECT    COLOR_ID    FROM     INV_GREY_FABRIC_ISSUE_DTLS    WHERE    COLOR_ID    IN( '91794')";
$sql22r = sql_select($sql22);
if(count($sql22r)>0) echo "INV_GREY_FABRIC_ISSUE_DTLS <br>";

$sql23="SELECT    COLOR_ID    FROM     PRO_GREY_BATCH_DTLS    WHERE    COLOR_ID    IN( '91794')";
$sql23r = sql_select($sql23);
if(count($sql23r)>0) echo "PRO_GREY_BATCH_DTLS <br>";

$sql24="SELECT    COLOR_ID    FROM     PRO_GREY_PROD_ENTRY_DTLS    WHERE    COLOR_ID    IN('91794')";
$sql24r = sql_select($sql24);
if(count($sql24r)>0) echo "PRO_GREY_PROD_ENTRY_DTLS <br>";




die;

$user_id=$_SESSION['logic_erp']['user_id'];

if($user_id=="" || $user_id==0) $user_id=9999;

//PO Details
$job_no='';
$job_no='UG-20-00028';
$jobYearCond="";
$jobYearCond=" and to_char(a.insert_date,'YYYY')='2020'";

if($job_no!="") { $jobCond="and a.job_no='$job_no'"; $jobCondS="and job_no='$job_no'"; } else { $jobCond=""; $jobCondS="";}
$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, b.po_number as PO_NUMBER, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $jobCond $jobYearCond";
//echo $sqlpo; die; //and a.job_no='$job_no'
$sqlpoRes = sql_select($sqlpo);
//print_r($sqlpoRes);
$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $jobid=""; $poDataArr=array();
foreach($sqlpoRes as $row)
{
	/*$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
	
	$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	
	$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];*/
	$poDataArr[$row['ID']]['job']=$row['JOB_NO'];
	$poDataArr[$row['ID']]['po']=$row['PO_NUMBER'];
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
?>
<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:1000px;" rules="all">
    <tr align="center" style="font-weight:bold">
        <td width="40">SL.</td>
        <td width="110">JOB</td>
        <td width="110">PO</td>
        <td width="100">Fabric Prod. Amt.</td>
        <td width="100">Fabric Pur. Amt.</td>
        <td width="100">Yarn Amt.</td>
        
        <td width="100">Conv. Amt.</td>
        <td width="100">Trim Amt.</td>
        
        <td width="100">Embl. Amt.</td>
        <td>Wash Amt.</td>
    </tr>
</table>
<div style="width:1000px; max-height:400px; overflow-y:scroll" id="scroll_body">
    <table class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
    $sqlbom="select id, job_id, po_id, po_qty, plan_qty, greyprod_qty, greyprod_amt, finprod_qty, finprod_amt, greypurch_qty, greypurch_amt, finpurch_qty, finpurch_amt, yarn_qty, yarn_amt, conv_qty, conv_amt, trim_qty, trim_amt, emb_qty, emb_amt, wash_qty, wash_amt from bom_process where status_active=1 and is_deleted=0 $jobidCondition order by job_id Desc";
    //echo $sqlbom; die;
    $sqlbomRes = sql_select($sqlbom);
    $i=1;
    foreach($sqlbomRes as $row)
    {
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        ?>
        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
            <td width="40" align="center"><?=$i; ?></td>
            <td width="110" style="word-break:break-all"><?=$poDataArr[$row[csf('po_id')]]['job']; ?></td>
            <td width="110" style="word-break:break-all"><?=$poDataArr[$row[csf('po_id')]]['po']; ?></td>
            <td width="100" style="word-break:break-all" align="right"><?=fn_number_format($row[csf('greyprod_amt')],2); ?></td>
            <td width="100" style="word-break:break-all" align="right"><?=fn_number_format($row[csf('greypurch_amt')],2); ?></td>
            <td width="100" style="word-break:break-all" align="right"><?=fn_number_format($row[csf('yarn_amt')],2); ?></td>
            <td width="100" style="word-break:break-all" align="right"><?=fn_number_format($row[csf('conv_amt')],2); ?></td>
            <td width="100" style="word-break:break-all" align="right"><?=fn_number_format($row[csf('trim_amt')],2); ?></td>
            
            <td width="100" style="word-break:break-all" align="right"><?=fn_number_format($row[csf('emb_amt')],2); ?></td>
            <td style="word-break:break-all" align="right"><?=fn_number_format($row[csf('wash_amt')],2); ?></td>
          </tr>
        <?
		$i++;
    }
    unset($sqlbomRes);


die;
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

//Bom Master Details
$bomMstSql="select job_id AS JOB_ID, costing_per AS COSTING_PER from wo_pre_cost_mst where status_active=1 and is_deleted=0 $jobCondS $jobidCondition";
//echo $bomMstSql; die;
$bomMstSqlRes = sql_select($bomMstSql);
$costingPerArr=array();
foreach($bomMstSqlRes as $row)
{
	$costingPerQty=0;
	if($row['COSTING_PER']==1) $costingPerQty=12;
	elseif($row['COSTING_PER']==2) $costingPerQty=1;	
	elseif($row['COSTING_PER']==3) $costingPerQty=24;
	elseif($row['COSTING_PER']==4) $costingPerQty=36;
	elseif($row['COSTING_PER']==5) $costingPerQty=48;
	else $costingPerQty=0;
	
	$costingPerArr[$row['JOB_ID']]=$costingPerQty;
}
unset($bomMstSqlRes);
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
unset($sqlfabRes); 
//echo "ff"; die;
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
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_qty']+=$yarnReq;
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_amt']+=$yarnAmt;
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
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
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
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotqty']+=$consTotQnty;
	
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimamt']+=$consAmt;
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotamt']+=$consTotAmt;
}
unset($sqlTrimRes); 
//print_r($reqQtyAmtArr); die;

$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
where 1=1 and a.cons_dzn_gmts>0 and
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
	if($row['EMB_NAME']==3)
	{
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['washqty']+=$consQnty;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['washamt']+=$consAmt;
	}
	else
	{
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['embqty']+=$consQnty;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['embamt']+=$consAmt;
	}
}
unset($sqlEmbRes); 
//echo "<pre>";
//print_r($reqQtyAmtArr); die;

$field_array="id, job_id, po_id, po_qty, plan_qty, greyprod_qty, greyprod_amt, finprod_qty, finprod_amt, greypurch_qty, greypurch_amt, finpurch_qty, finpurch_amt, yarn_qty, yarn_amt, conv_qty, conv_amt, trim_qty, trim_amt, emb_qty, emb_amt, wash_qty, wash_amt, inserted_by, insert_date, status_active, is_deleted";
$id=return_next_id("id","bom_process",1); $data_array=""; $add_comma=0;

$con = connect();
if($db_type==0) mysql_query("BEGIN");
$i=1;
foreach($reqQtyAmtArr as $jobid=>$jobdata)
{
	foreach($jobdata as $poid=>$podata)
	{
		$poQty=$planQty=$finProdQty=$greyProdQty=$finPurchQty=$greyPurchQty=$yarnQty=$convQty=$trimQty=$washQty=$printQty=$embQty=0;
		$finPordAmt=$greyPordAmt=$finPurchAmt=$greyPurchAmt=$yarnAmt=$convAmt=$trimAmt=$washAmt=$printAmt=$embAmt=0;
		
		$poQty=$podata['poqty']; 
		$planQty=$podata['planqty'];
		
		$finProdQty=$podata['prodfin_qty']; 
		$greyProdQty=$podata['prodgrey_qty'];
		$finPordAmt=$podata['prodfin_amt']; 
		$greyPordAmt=$podata['prodgrey_amt'];
		
		$finPurchQty=$podata['purchfin_qty']; 
		$greyPurchQty=$podata['purchgrey_qty'];
		$finPurchAmt=$podata['purchfin_amt']; 
		$greyPurchAmt=$podata['purchgrey_amt'];
		
		$yarnQty=$podata['yarn_qty']; 
		$yarnAmt=$podata['yarn_amt'];
		
		$convQty=$podata['conv_qty']; 
		$convAmt=$podata['conv_amt'];
		
		$trimQty=$podata['trimqty']; 
		$trimAmt=$podata['trimamt'];
		
		$washQty=$podata['washqty']; 
		$washAmt=$podata['washamt'];
		$embQty=$podata['embqty']; 
		$embAmt=$podata['embamt'];
		
		//echo $poQty.'-'.$planQty.'-'.$finProdQty.'-'.$finPordAmt.'-'.$greyProdQty.'-'.$greyPordAmt.'-'.$finPurchQty.'-'.$finPurchAmt.'-'.$greyPurchQty.'-'.$greyPurchAmt.'-'.$yarnQty.'-'.$yarnAmt.'-'.$convQty.'-'.$convAmt.'-'.$trimQty.'-'.$trimAmt.'-'.$washQty.'-'.$washAmt.'-'.$embQty.'-'.$embAmt."<br>";
		
		//if ($add_comma!=0) $data_array .=","; $add_comma=0;
		$data_array[$poid]="(".$id.",'".$jobid."','".$poid."','".$poQty."','".$planQty."','".$greyProdQty."','".$greyPordAmt."','".$finProdQty."','".$finPordAmt."','".$greyPurchQty."','".$greyPurchAmt."','".$finPurchQty."','".$finPurchAmt."','".$yarnQty."','".$yarnAmt."','".$convQty."','".$convAmt."','".$trimQty."','".$trimAmt."','".$embQty."','".$embAmt."','".$washQty."','".$washAmt."','".$user_id."','".$pc_date_time."',1,0)";
		$id++; $add_comma++; $i++;
	}
}
unset($reqQtyAmtArr);
//echo $i; die;
//print_r($data_array);

//echo "10**INSERT INTO bom_process (".$field_array.") VALUES ".$data_array; die;
//$rID=sql_insert("bom_process",$field_array,$data_array,1);

$msgsucc="$i  BOM Process Success.";
$msgfail="$i  BOM Process Fail.";

$data_bom=array_chunk($data_array,100);
foreach( $data_bom as $bomRows)
{
	//echo "10**INSERT INTO wo_po_details_master (".$field_job.") VALUES ".implode(",",$jobRows); die;
	//$rID=sql_insert("wo_po_details_master",$field_job,implode(",",$bomRows),0);
	$rID=sql_insert("bom_process",$field_array,implode(",",$bomRows),1);
	if($rID==1) $flag=1; //else $flag=0;
	else if($rID==0) 
	{
		//echo "10**INSERT INTO bom_process (".$field_array.") VALUES ".implode(",",$bomRows); die;
		$flag=0;
		oci_rollback($con); 
		echo "10**".$msgfail; die;
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