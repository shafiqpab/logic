<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
//require_once('../../../../includes/class4/class.conditions.php');
//require_once('../../../../includes/class4/class.reports.php');
//require_once('../../../../includes/class4/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-All-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-Buyer-", $selected, "load_drop_down( 'requires/business_analysis_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}

if ($action=="load_drop_down_party_type")
{
	echo create_drop_down( "cbo_client", 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-Client-", $selected, "" );
	exit();
}

$companyArr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

function poArrWiseExmfgCost($poid,$type,$user_id)
{
	//echo load_html_head_contents("Excess Mat. Cost", "../../../../", 1, 1,$unicode,'','');
	//echo $poid.'=';//die;
	//$expData=explode('__',$data);
	//print_r($expData);
	//$poid=trim($expData[0]);
	//$type=$expData[1];
	//gbl_temp_engine g where a.po_break_down_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10
	$gExcessFinishFabCost=0;
	if(!empty($poid))
	{
		$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10";
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes);
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
		$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1  $jobidCondition";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		}
		unset($gmtsitemRatioSqlRes);
		
		$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		}
		unset($sqlContrastRes);
		
		//Stripe Details
		$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
		}
		unset($sqlStripeRes);
		
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.lib_yarn_count_deter_id as DETAID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.composition as COMPOSITION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.dia_width as DIA_WIDTH, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
		from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,gbl_temp_engine g
		where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.fabric_source=1 ";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $fabDescArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$fabIdWiseGmtsDataArr[$row['ID']]['CONSTRUCTION']=$row['CONSTRUCTION'];
			$fabIdWiseGmtsDataArr[$row['ID']]['DETAID']=$row['DETAID'];
			$fabcolorArr=array();
			if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
			{
				foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
				{
					$fabcolorArr[$row['ID']][$row['COLOR_NUMBER_ID']][$fabcolor]=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
				}
			}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			//$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
			//$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			
			$finAmt=$finReq*$row['RATE'];
			//$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			
			
			$fullfab=$row['CONSTRUCTION'].', '.$row['COMPOSITION'].', '.$row['GSM_WEIGHT'].', '.$row['DIA_WIDTH'];
			$fabDescArr[$row['ID']]['fab']=$fullfab;
			if($row['FABRIC_SOURCE']==2)
			{
				if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
				{
					foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
					{
						$cons=0;
						$cons=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
						$finReq=($planQty/$itemRatio)*($cons/$costingPer);
						$finAmt=$finReq*$row['RATE'];
						
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_amt']+=$greyAmt;
					}
				}
				else if ($sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]!="" && $row['COLOR_SIZE_SENSITIVE']==3)
				{
					$cons=0;
					$fabcolor=$sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']];
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
				else
				{
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
				
			}
		}
		unset($sqlfabRes);
		
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 

		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b,gbl_temp_engine g where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10";
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
			$reqYarnArr[$row['COPM_ONE_ID']][$row['COUNT_ID']]['yarn_qty']+=$yarnReq;
			$reqYarnArr[$row['COPM_ONE_ID']][$row['COUNT_ID']]['yarn_amt']+=$yarnAmt;
		}
		unset($sqlYarnRes);
		
		//print_r($reqYarnArr);
		
                //Total Cost as per Budget
				foreach($reqYarnArr as $compo=>$compodata)
				{
					foreach($compodata as $countid=>$countdata)
					{
						
						
						$gYarnBomQty+=$countdata['yarn_qty'];
						$gYarnBomAmt+=$countdata['yarn_amt'];
					}
				}
				
		
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b,gbl_temp_engine g where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id =g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convFabArr=array();
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
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
				}
			}
		}
		//echo "ff"; die;
		$convReqQtyAmtArr=array(); $convRateArr=array();
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
			$libYarnDetaid=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['DETAID'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			$convRateArr[$row['CONVERTION_ID']]['fab']=$fabDescArr[$row['PRECOSTID']]['fab'];
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
						$reqqnty=$qnty;
						$convAmt=$qnty*$convrate;
					}
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yamt']+=$convAmt;
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
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				else if($consProcessId==1 || $consProcessId==33)
				{
					$convrate=$row['CHARGE_UNIT'];
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				//echo $convrate.'='.$row['CHARGE_UNIT'].'='.$itemRatio.'='.$requirment.'='.$costingPer."<br>";
				if($consProcessId==134)
				{
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yamt']+=$convAmt;
				}
				if($consProcessId==1)
				{
					$fabconstruction=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['CONSTRUCTION'];
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kqty']+=$reqqnty;
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kamt']+=$convAmt;
				}
				if($consProcessId==31)
				{
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdqty']+=$reqqnty;
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdamt']+=$convAmt;
					
				}
				if($consProcessId==67 || $consProcessId==68 || $consProcessId==35)
				{
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaqty']+=$reqqnty;
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaamt']+=$convAmt;
				}
				$convRateArr[$row['POID']][$consProcessId][$rateColorId][$libYarnDetaid]['fdrate']=$convrate;
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes);
		
		//print_r($convReqQtyAmtArr);die;
		
                //Yarn Sevice Cost as per Budge
				foreach($convReqQtyAmtArr['yd'] as $po_id=>$podata)
				{
					foreach ($podata as $processid=>$convdata) 
					{
						foreach($convdata as $colorid=>$colordata)
						{
							
							$bomYarnDAvgRate=0;
							$bomYarnDAvgRate=$colordata['yamt']/$colordata['yqty'];
							
							
							
							$gYarndBomQty+=$colordata['yqty'];
							$gYarndBomAmt+=$colordata['yamt'];
						}
					}
					
				}
				

                //Knitting Cost as per Budget
				foreach($convReqQtyAmtArr['knit'] as $po_id=>$podata )
				{
					foreach ($podata as $processid=>$convdata) 
					{
						foreach($convdata as $fabconst=>$fabconstdata)
						{
							
							$bomKnitAvgRate=0;
							$bomKnitAvgRate=$fabconstdata['kamt']/$fabconstdata['kqty'];
							
							$gknitBomQty+=$fabconstdata['kqty'];
							$gknitBomAmt+=$fabconstdata['kamt'];
						}
					}
				}
				
				foreach($convReqQtyAmtArr['fd'] as $po_id=>$podata )
				{
					foreach ($podata as $processid=>$convdata) 
					{

						foreach($convdata as $fabcolor=>$fabcolordata)
						{
							
							$bomFdAvgRate=0;
							$bomFdAvgRate=$fabcolordata['fdamt']/$fabcolordata['fdqty'];
							$gFdBomQty+=$fabcolordata['fdqty'];
							$gFdBomAmt+=$fabcolordata['fdamt'];
						}
					}
				}
				
				//PBA Cost as per Budget 
				foreach($convReqQtyAmtArr['pba'] as $po_id=>$podata )
				{
					foreach ($podata as $processid=>$convdata) 
					{
					
						$bomPbaAvgRate=0;
						$bomPbaAvgRate=$procdata['pba']['pbaamt']/$procdata['pba']['pbaqty'];
						
						$gPbaBomQty+=$procdata['pba']['pbaqty'];
						$gPbaBomAmt+=$procdata['pba']['pbaamt'];
					}
				}
				$totalBomCost=fn_number_format($gYarnBomAmt,8,".","")+fn_number_format($gYarndBomAmt,8,".","")+fn_number_format($gknitBomAmt,8,".","")+fn_number_format($gFdBomAmt,8,".","")+fn_number_format($gPbaBomAmt,8,".","");
				//echo $totalBomCost;die;
		
		$sqlYIssue="SELECT a.id as issue_id, a.issue_number, a.booking_no, a.knit_dye_source, a.knit_dye_company, b.quantity as issue_qnty, b.prod_id, d.cons_rate, a.issue_purpose from inv_issue_master a, order_wise_pro_details b, inv_transaction d,gbl_temp_engine g 
			where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id =g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose  in (1,2,15,50) order by a.id ASC";
		$sqlYarnIssue=sql_select($sqlYIssue);
		$yarnStolenArr=array(); $yarnratearr=array(); $yarnWoArr=array();
		foreach($sqlYarnIssue as $yirow)
		{
			$yarnStolenArr[$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissqty']+=$yirow[csf("issue_qnty")];
			$yarnStolenArr[$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissamt']+=$yirow[csf("issue_qnty")]*($yirow[csf("cons_rate")]/82);
			$yarnratearr[$yirow[csf("issue_id")]][$yirow[csf("prod_id")]]=($yirow[csf("cons_rate")]/82);
			$yarnWoArr[$yirow[csf("booking_no")]]['']['booking_no']=$yirow[csf("knit_dye_source")];
			$yarnWoArr[$yirow[csf("booking_no")]][$yirow[csf("prod_id")]]['rate']=($yirow[csf("cons_rate")]/82);
		}
		unset($sqlYarnIssue);
		
		$sql_ret = "SELECT a.id, a.recv_number, a.knitting_source, a.knitting_company, b.quantity, b.prod_id, d.issue_id, b.issue_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d ,gbl_temp_engine g
			where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id =g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose in (1,2,15,50) ";
		$sqlYarnIssueRet=sql_select($sql_ret);
		foreach($sqlYarnIssueRet as $yirrow)
		{
			$retrate=$yarnratearr[$yirrow[csf("issue_id")]][$yirrow[csf("prod_id")]];
			$yarnStolenArr[$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretqty']+=$yirrow[csf("quantity")];
			$yarnStolenArr[$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretamt']+=$yirrow[csf("quantity")]*$retrate;
		}
		unset($sqlYarnIssueRet);
		
		$sqlGray="select a.knitting_source, a.knitting_company, a.receive_purpose, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.quantity as quantity, b.order_yarn_rate as kniting_charge from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d ,gbl_temp_engine g
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2,22) and c.entry_form in (2,22) and c.po_breakdown_id==g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$sqlGrayRec=sql_select($sqlGray);
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$yarnStolenArr[1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecqty']+=$grrow[csf("quantity")];
			$yarnStolenArr[1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]);
		}
		unset($sqlGrayRec);
		
		$sqlRec = "SELECT a.id, a.recv_number, a.booking_no, a.knitting_source, a.supplier_id as knitting_company, d.grey_quantity as quantity, b.prod_id, d.cons_avg_rate as order_rate, a.receive_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d ,gbl_temp_engine g
			where a.id=d.mst_id and d.transaction_type=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=1 and b.po_breakdown_id =g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in (1,2,15,50) ";
		$sqlYarnRec=sql_select($sqlRec);
		foreach($sqlYarnRec as $yrrow)
		{
			$knitsource=$yarnWoArr[$yrrow[csf("booking_no")]]['']['booking_no'];
			$yarnrecrate=$yarnWoArr[$yrrow[csf("booking_no")]][$yrrow[csf("prod_id")]]['rate'];
			
			$yarnStolenArr[$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecqty']+=$yrrow[csf("quantity")];
			$yarnStolenArr[$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecamt']+=$yrrow[csf("quantity")]*($yrrow[csf("order_rate")]/82);
		}
		unset($sqlYarnRec);
		
		
				//Stolen Yarn Value info 
				foreach($yarnStolenArr as $issuepurpose=>$issuepurposedata)
				{
					foreach($issuepurposedata as $ysource=>$ysourcedata)
					{
						foreach($ysourcedata as $ysourcecom=>$ysourcecomdata)
						{
							
							
							$issqty=$issAmt=$stolenQty=$stolenAmt=0;
							$issqty=$ysourcecomdata['yissqty']-$ysourcecomdata['yissretqty'];
							$issAmt=$ysourcecomdata['yissamt']-$ysourcecomdata['yissretamt'];
							$stolenQty=$issqty-$ysourcecomdata['yrecqty'];
							$stolenAmt=$issAmt-$ysourcecomdata['yrecamt'];
							
							$gstolenQty+=$stolenQty;
							$gstolenAmt+=$stolenAmt;
						}
					}
				}
				


		$sqlGYIssue="SELECT a.id as issue_id, b.quantity as issue_qnty, b.prod_id, c.cons_rate, d.lot, d.brand_supplier, d.yarn_count_id, d.yarn_comp_type1st from inv_issue_master a, order_wise_pro_details b, inv_transaction c, product_details_master d,gbl_temp_engine g
			where a.id=c.mst_id and c.transaction_type=2 and c.item_category=1 and c.id=b.trans_id and c.prod_id=d.id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sqlGYIssueRes=sql_select($sqlGYIssue); $greyYarnDtlsArr=array();
		foreach($sqlGYIssueRes as $isrow)
		{
			$str="";
			$str=$isrow[csf("yarn_count_id")].'**'.$isrow[csf("yarn_comp_type1st")].'**'.$isrow[csf("brand_supplier")].'**'.$isrow[csf("lot")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecdata']=$str;
			//$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecqty']+=$isrow[csf("issue_qnty")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecrate']=($isrow[csf("cons_rate")]/82);
		}
		unset($sqlGYIssueRes);
		
		$sqlGray="SELECT a.id,b.id as dtls_id, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.quantity as quantity, b.kniting_charge, b.order_yarn_rate,a.knitting_source from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d ,gbl_temp_engine g
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2) and c.entry_form in (2) and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlGray;
		$sqlGrayRec=sql_select($sqlGray); $greymstIdArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$greymstIdArr[$grrow[csf("id")]]=$grrow[csf("id")];
		}
		$recv_cond=where_con_using_array($greymstIdArr,0,"receive_id");

		$knitting_bill_sql="SELECT b.receive_id,b.currency_id,b.rate, a.company_id,a.bill_date FROM subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and a.entry_form=438 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_cond";
		//echo $knitting_bill_sql;
		$knitting_bill_res=sql_select($knitting_bill_sql);
		$recv_wise_knitting_charge=array();
		foreach ($knitting_bill_res as $row)
		{
			$con_rate=set_conversion_rate($row[csf('currency_id')], $row[csf('bill_date')],$row[csf('company_id')]);
			// echo "<pre>";
			// echo $con_rate;
			// echo "</pre>";
			$recv_wise_knitting_charge[$row[csf('receive_id')]]=($row[csf('rate')]*$con_rate);
		}
		
		$greyMst_cond=where_con_using_array($greymstIdArr,0,"mst_id");
		
		$sqlYarn="select prod_id, used_qty,dtls_id,mst_id from pro_material_used_dtls where entry_form=2 and status_active=1 and is_deleted=0 $greyMst_cond";
		//echo $sqlYarn;
		$sqlYarnUsed=sql_select($sqlYarn); $yusedArr=array(); $yusedArr1=array();
		foreach($sqlYarnUsed as $yurow)
		{
			$yusedArr[$yurow[csf("prod_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['yqty']+=$yurow[csf("used_qty")];
		}
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		//$prod_arr_used=array();
		foreach($sqlGrayRec as $grrow)
		{
			
			$grayDataArr[$grrow[csf("product_name_details")]]['yprodid'].=','.$grrow[csf("yarn_prod_id")];
			$grayDataArr[$grrow[csf("product_name_details")]]['grecqty']+=$grrow[csf("quantity")];

			//$grayDataArr[$grrow[csf("product_name_details")]]['grecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]/82);
			//$prodrateArr[$grrow[csf("product_name_details")]]=($grrow[csf("kniting_charge")]/82);
			//$grrow[csf("knitting_source")]==1
			
			
			if($grrow[csf("knitting_source")]==1)
			{
				$grayDataArr[$grrow[csf("product_name_details")]]['grecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]/82);
				$prodrateArr[$grrow[csf("product_name_details")]]=($grrow[csf("kniting_charge")]/82);
			}
			else
			{
				$grayDataArr[$grrow[csf("product_name_details")]]['grecamt']+=$grrow[csf("quantity")]*($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
				$prodrateArr[$grrow[csf("product_name_details")]]=($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
			}
			
			
			
			$exyarnid=explode(",",$grrow[csf("yarn_prod_id")]);
			
			foreach($exyarnid as $ynid)
			{
				
				$greyYarnDtlsArr[$ynid]['yrecqty']+=$yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty'];
				$greyYarnDtlsArr[$ynid]['yrecamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty']*$grrow[csf("order_yarn_rate")]);
				$grayDataArr[$grrow[csf("product_name_details")]]['yrntotamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty']*$grrow[csf("order_yarn_rate")]);
				
				// $greyYarnDtlsArr[$ynid]['yrecqty']+=$yusedArr[$ynid]['yqty'];
				// $greyYarnDtlsArr[$ynid]['yrecamt']+=$yusedArr[$ynid]['yqty']*$grrow[csf("order_yarn_rate")];
				// $grayDataArr[$grrow[csf("product_name_details")]]['yrntotamt']+=$yusedArr[$ynid]['yqty']*$grrow[csf("order_yarn_rate")];
			}
		}
		unset($sqlGrayRec);
		// echo "<pre>";
		// print_r($grayDataArr);
		// echo "</pre>";
		//echo $grayDataArr[$grrow[csf("product_name_details")]]['yrntotamt'];
		
		//print_r($greyYarnDtlsArr);
		$sqlTrans = "SELECT a.from_order_id, a.to_order_id, b.to_prod_id, b.from_prod_id, c.trans_type, c.quantity as transfer_qnty, d.product_name_details,b.rate,b.transfer_value from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d,gbl_temp_engine g where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form=13 and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlTrans;
		$sqlTransRes=sql_select($sqlTrans); $greyTransDtlsArr=array(); $greyTransinDtlsArr=array(); $trnsPoIdArr=array();
		foreach($sqlTransRes as $gtrrow)
		{
			if($gtrrow[csf("trans_type")]==5)
			{
				$greyTransinDtlsArr[$gtrrow[csf("product_name_details")]]['trinpoid'].=$gtrrow[csf("from_order_id")].',';
				$greyTransinDtlsArr[$gtrrow[csf("product_name_details")]]['trinqty']+=$gtrrow[csf("transfer_qnty")];

				//$greyTransinDtlsArr[$gtrrow[csf("product_name_details")]]['trinamt']+=($gtrrow[csf("transfer_qnty")]*$prodrateArr[$gtrrow[csf("product_name_details")]]);

				//change here
				$greyTransinDtlsArr[$gtrrow[csf("product_name_details")]]['trinamt']+=($gtrrow[csf("transfer_value")]/82);

				//array_push($trnsPoIdArr,$gtrrow[csf('from_order_id')]);
				
				//echo $gtrrow[csf('from_order_id')].'i <br>';
			}
			else if($gtrrow[csf("trans_type")]==6)
			{
				$greyTransDtlsArr[$gtrrow[csf("product_name_details")]]['troutpoid'].=$gtrrow[csf("to_order_id")].',';
				$greyTransDtlsArr[$gtrrow[csf("product_name_details")]]['troutqty']+=$gtrrow[csf("transfer_qnty")];
				$greyTransDtlsArr[$gtrrow[csf("product_name_details")]]['troutamt']+=$gtrrow[csf("transfer_qnty")]*$prodrateArr[$gtrrow[csf("product_name_details")]];
				//array_push($trnsPoIdArr,$gtrrow[csf('to_order_id')]);
				//echo $gtrrow[csf('to_order_id')].' <br>';
			}
			$trnsPoIdArr[$gtrrow[csf('to_order_id')]]=$gtrrow[csf('to_order_id')];
			$trnsPoIdArr[$gtrrow[csf('from_order_id')]]=$gtrrow[csf('from_order_id')];
		}
		unset($sqlTransRes);
		
		


                //Actual Gray Fabric cost
				
				foreach($grayDataArr as $gprodname=>$gprodnamedata)
				{
					$span=1;
						
					$transoutAvgRate=0;
					$exyprodid=array_filter(array_unique(explode(",",$gprodnamedata['yprodid'])));
					$countYarn=count($exyprodid);
					
					$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
					$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
					
					
					$transoutQty=$greyTransDtlsArr[$gprodname]['troutqty'];

					//$transoutAvgRate=$greytotamt/$gprodnamedata['yrntotamt'];
					$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
					$transoutAvgRate=fn_number_format($transoutAvgRate,8,".","");
					if($transoutQty==0 || $transoutQty=="") $transoutAvgRate=0;
					$transoutAmt=$transoutQty*$transoutAvgRate;
					$actualGreyCost=0;
					$actualGreyCost=$greytotamt-$transoutAmt;
					
					foreach($exyprodid as $yid)
					{
						
						$yqtykg=$greyYarnDtlsArr[$yid]['yrecqty'];
						$yamt=$greyYarnDtlsArr[$yid]['yrecamt'];
						$yavgprice=$yamt/$yqtykg;
						
						$gyarnQty+=$yqtykg;
						$gyarnAmt+=$yamt;
					}
					
					$ggrayQty+=$gprodnamedata['grecqty'];
					$ggrayAmt+=$greytotamt;
					
					$gtransoutQty+=$transoutQty;
					$gtransoutAmt+=$transoutAmt;
					$gactualgrayAmt+=$actualGreyCost;
				}
				


                //Transfer In Status
				
				foreach($greyTransinDtlsArr as $fabricdtls=>$fabricdtlsdata)
				{
						$bomTransinAvgRate=0;
						$bomTransinAvgRate=$fabricdtlsdata['trinamt']/$fabricdtlsdata['trinqty'];
						
						$gTransinQty+=$fabricdtlsdata['trinqty'];
						$gTransinAmt+=$fabricdtlsdata['trinamt'];
				}


				//Total Gray fabric cost
				$gTotalGreyFabCost=$gactualgrayAmt+$gTransinAmt;
				
		
		$sqlIss="SELECT b.color_id as COLOR_ID, c.prod_id as PROD_ID, c.po_breakdown_id as POID, b.rate as RATE, c.quantity as QUANTITY from inv_grey_fabric_issue_dtls b, order_wise_pro_details c,gbl_temp_engine g where b.id=c.dtls_id and c.entry_form in (16) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 ";
		$sqlIssArr=sql_select($sqlIss); $grayRateArr=array();
		foreach($sqlIssArr as $grow)
		{
			$grayRateArr[$grow["COLOR_ID"]][$grow["PROD_ID"]]['rate']=$grow["RATE"];
		}
		unset($sqlIssArr);
		//print_r($grayRateArr);
		
		$sqlBatch = "SELECT a.id, a.color_id, a.entry_form, b.po_id, b.prod_id, b.batch_qnty as quantity, c.product_name_details, c.detarmination_id from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c,gbl_temp_engine g where a.id=b.mst_id and b.prod_id=c.id and b.po_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10 and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$batchDataArr=array();
		$sqlBatchArr=sql_select($sqlBatch); $batchidarr=array(); $batchcolorarr=array(); $batchpoarr=array();
		foreach($sqlBatchArr as $brow)
		{
			//echo $grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate'].'i<br>';
			$amt=$brow[csf("quantity")]*($grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate']/82);
			$batchDataArr[$brow[csf("color_id")]][$brow[csf("product_name_details")]]['batch_qty']+=$brow[csf("quantity")];
			$batchDataArr[$brow[csf("color_id")]][$brow[csf("product_name_details")]]['batch_amt']+=$amt;
			$batchidarr[$brow[csf("id")]]=$brow[csf("id")];
			$batchcolorarr[$brow[csf("id")]]=$brow[csf("color_id")];
			$batchpoarr[$brow[csf("id")]][$brow[csf("prod_id")]]=$brow[csf("po_id")];
			$batchDataArr[$brow[csf("color_id")]][$brow[csf("product_name_details")]]['dyeamt']+=$brow[csf("quantity")]*$convRateArr[$brow[csf("po_id")]][31][$brow[csf("color_id")]][$brow[csf("detarmination_id")]]['fdrate'];
		}
		unset($sqlBatchArr);
		//print_r($batchDataArr);
		
		$batchid_cond=where_con_using_array($batchidarr,0,"a.batch_id");
		
		$sqlSP="SELECT a.batch_id, a.process_id, b.prod_id, b.production_qty, c.product_name_details, c.detarmination_id from pro_fab_subprocess a, pro_fab_subprocess_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=34 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchid_cond";
		$sqlSPArr=sql_select($sqlSP); $specialDataArr=array();
		foreach($sqlSPArr as $brow)
		{
			$batchcolor=$batchcolorarr[$brow[csf("batch_id")]];
			$po_id=$batchpoarr[$brow[csf("batch_id")]][$brow[csf("prod_id")]];
			$amt=$brow[csf("production_qty")]*($convRateArr[$po_id][$brow[csf("process_id")]][$batchcolor][$brow[csf("detarmination_id")]]['fdrate']);
			
			$specialDataArr[$po_id][$brow[csf("process_id")]][$batchcolor][$brow[csf("product_name_details")]]['sp_qty']+=$brow[csf("production_qty")];
			$specialDataArr[$po_id][$brow[csf("process_id")]][$batchcolor][$brow[csf("product_name_details")]]['sp_amt']+=$amt;
		}
		unset($sqlSPArr);
		
		/*$sqlSer="select b.color_id, b.batch_issue_qty as production_qty, c.product_name_details from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in ($poid)";
		$sqlSerArr=sql_select($sqlSer); $aopDataArr=array();
		foreach($sqlSerArr as $brow)
		{
			$batchcolor=$batchcolorarr[$brow[csf("batch_id")]];
			$amt=$brow[csf("production_qty")]*($grayRateArr[$batchcolor][$brow[csf("prod_id")]]['rate']/82);
			
			$aopDataArr[35][$brow[csf("color_id")]][$brow[csf("product_name_details")]]['sp_qty']+=$brow[csf("production_qty")];
			$aopDataArr[35][$brow[csf("color_id")]][$brow[csf("product_name_details")]]['sp_amt']+=$amt;
		}
		unset($sqlSerArr);*/
		
		$dataArrayfinish = "SELECT a.id as ID, a.entry_form as ENTRY_FORM, a.booking_id as BOOKINGID, a.knitting_source as KNITTING_SOURCE, a.receive_basis as RECEIVEBASIS, a.currency_id as CURRENCY_ID, b.rate as RATE, c.po_breakdown_id as POID, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, b.grey_used_qty as GREY_USED_QTY, b.receive_qnty as RECEIVE_QNTY, b.grey_fabric_rate as GREY_FABRIC_RATE, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d,gbl_temp_engine g where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2 and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10";
		$dataArrayfinishArr=sql_select($dataArrayfinish);
		$bookingidArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				$bookingidArr[$row['BOOKINGID']]=$row['BOOKINGID'];
			}
		}
		$bookingid_cond=where_con_using_array($bookingidArr,0,"a.id");
		$servBookSql="select b.booking_no, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.dia_width, b.rate from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingid_cond";
		$servBookSqlArr=sql_select($servBookSql); $bookingRateArr=array();
		foreach($servBookSqlArr as $worow)
		{
			//echo $convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab'].'<br>';
			$bookingRateArr[$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab']][$worow[csf("fabric_color_id")]]['worate']=$worow[csf("rate")];
		}
		unset($servBookSqlArr);
		
		$recDataRetArr=array(); $finishDataArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			if($row['ENTRY_FORM']==7)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				//$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_amt']+=$amt;
				
				$recDataRetArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
			}
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				//$avgQty=((1-($row['QUANTITY']/$row['GREY_USED_QTY']))*$row['GREY_USED_QTY'])+$row['QUANTITY'];
				$avgQty=($row['GREY_USED_QTY']/$row['RECEIVE_QNTY'])*$row['QUANTITY'];
				//echo $avgQty.'='.$row['QUANTITY'].'='.$row['GREY_USED_QTY'].'='.'kausar<br>';
				$amt=$avgQty*($row['GREY_FABRIC_RATE']/82);
				$batchDataArr[$row['POID']][$row['COLOR_ID']][$row['PRODUCT_NAME_DETAILS']]['batch_qty']+=$avgQty;
				$batchDataArr[$row['POID']][$row['COLOR_ID']][$row['PRODUCT_NAME_DETAILS']]['batch_amt']+=$amt;
				$finWoRate=$bookingRateArr[$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['worate'];
				$amt=$avgQty*$finWoRate;
				$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_amt']+=$amt;
			}
		}
		unset($dataArrayfinishArr);
		//print_r($recDataRetArr[52550][54013]); die;
		
		/*$sqlRet="select a.received_id as RECEIVED_ID, b.prod_id as PROD_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, b.cons_uom as UOM from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (46) and c.entry_form in (46) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$poid' ";
		$sqlRetArr=sql_select($sqlRet);
		foreach($sqlRetArr as $row)
		{
			$recRate=$amt=0;
			$recRate=$recDataRetArr[$row['RECEIVED_ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']*1;
			//echo $recRate;
			if(($recRate)>0)
			{
				$amt=$row['QUANTITY']*$recRate;
				$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinRet_qty']+=$row['QUANTITY'];
				$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinRet_amt']+=$amt;
			}
		}
		unset($sqlRetArr);*/
		//print_r($reqQtyAmtArr);
		
		$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, b.batch_id as BATCH_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d,gbl_temp_engine g where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=880 and g.ref_from=10";
		
		$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array(); $transOutArr=array(); $transInArr=array(); $batchIDArr=array();
		foreach($sqlTransArr as $row)
		{
			$transVal=$amt=0;
			$transVal=$row['TRANSFER_VALUE']/82;
			//echo $recRate;
			if($row['TRANS_TYPE']==5)//trans in
			{
				/*if($batchEntryFormArr[$row['BATCH_ID']]==37)
				{*/
					//$amt=$row['QUANTITY']*$transRate;
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTin_qty']+=$row['QUANTITY'];
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTin_amt']+=$transVal;
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['trinpoid'].=$row['FROM_ORDER_ID'].',';
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['batchid']=$row['BATCH_ID'];
					array_push($batchIDArr,$row['BATCH_ID']);
				//}
			}
			else if($row['TRANS_TYPE']==6)//trans out
			{
				//$amt=$row['QUANTITY']*$transRate;
				$transOutArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTout_qty']+=$row['QUANTITY'];
				$transOutArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTout_amt']+=$transVal;
				$transOutArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTout_ref'].=$row['TO_ORDER_ID'].',';
			}
			array_push($trnsPoIdArr,$row['TO_ORDER_ID']);
			array_push($trnsPoIdArr,$row['FROM_ORDER_ID']);
		}
		unset($sqlTransArr);
		//print_r($transOutArr);
		
		
		$batchID_cond=where_con_using_array($batchIDArr,0,"id");
		$batchEntryArr=return_library_array( "select id, entry_form from pro_batch_create_mst where 1=1 $batchID_cond", "id", "entry_form");
		
                //Actual Gray Fabric cost
				foreach($batchDataArr as $po_id=>$podata)
				{
					foreach ($podata as $fabcolorid=>$fabcolordata)
					{
						foreach($fabcolordata as $fabric=>$fabdata)
						{
							
							$peachFinishQty=$specialDataArr[$po_id][67][$fabcolorid][$fabric]['sp_qty'];
							$peachFinishAmt=$specialDataArr[$po_id][67][$fabcolorid][$fabric]['sp_amt'];
							
							
							$brushingQty=$specialDataArr[$po_id][68][$fabcolorid][$fabric]['sp_qty'];
							$brushingAmt=$specialDataArr[$po_id][68][$fabcolorid][$fabric]['sp_amt'];
							$brushingRate=$brushingAmt/$brushingQty;
							
							$aopQty=$specialDataArr[$po_id][35][$fabcolorid][$fabric]['sp_qty'];
							$aopAmt=$specialDataArr[$po_id][35][$fabcolorid][$fabric]['sp_amt'];
							
							
							$finishAmt=$finishDataArr[$po_id][$fabric][$fabcolorid]['finrec_amt'];
							//echo $fabdata['batch_amt'].'-'.$fabdata['dyeamt'].'-'.$peachFinishAmt.'-'.$brushingAmt.'-'.$aopAmt.'-'.$finishAmt.'<br>';
							$finishFabCost=$fabdata['batch_amt']+$fabdata['dyeamt']+$peachFinishAmt+$brushingAmt+$aopAmt+$finishAmt;
							
							$finishQty=$finishDataArr[$po_id][$fabric][$fabcolorid]['finrec_qty'];
							$finRate=$finishFabCost/$finishQty;
							
							
							
							$trnsOUtQty=$trnsOUtAmt=0;
							$trnsOUtQty=$transOutArr[$po_id][$fabric][$fabcolorid]['finTout_qty'];
							//$trnsOUtAmt=$transOutArr[$poid][$fabric][$fabcolorid]['finTout_amt'];
							$finRate=fn_number_format($finRate,8,".","");
							$trnsOUtQty=fn_number_format($trnsOUtQty,8,".","");
							$trnsOUtAmt=$finRate*$trnsOUtQty;
							
							
							$fabricFinishCost=$finishFabCost-$trnsOUtAmt;
							
							
							$gbatchQty+=$fabdata['batch_qty'];
							$gbatchAmt+=$fabdata['batch_amt'];
							$gdyeingAmt+=$fabdata['dyeamt'];
							
							$gPeachFinishQty+=$peachFinishQty;
							$gPeachFinishAmt+=$peachFinishAmt;
							
							$gBrushingQty+=$brushingQty;
							$gBrushingAmt+=$brushingAmt;
							
							$gAopQty+=$aopQty;
							$gAopAmt+=$aopAmt;
							
							$gFinishQty+=$finishQty;
							$gFinishAmt+=$finishFabCost;
							
							$gTransOutQty+=$trnsOUtQty;
							$gTransOutAmt+=$trnsOUtAmt;
							
							$gFabricAmt+=$fabricFinishCost;
						}
					}
					
				}
				


                //Transfer In Status
				foreach($transInArr as $po_id=>$podata)
				{
					foreach ($podata as $fabricdtls=>$fabricdtlsdata) 
					{
						foreach($fabricdtlsdata as $fabriccolor=>$colordata)
						{
							if($batchEntryArr[$colordata['batchid']]!=37)
							{
								
								$bomTransinAvgRate=0;
								$bomTransinAvgRate=$colordata['finTin_amt']/$colordata['finTin_qty'];
								
								$gfinishQty+=$colordata['finTin_qty'];
								$gfinishAmt+=$colordata['finTin_amt'];
							}
						}
					}
					
				}
				
				$gTotalFinishFabCost=$gFabricAmt+$gfinishAmt;
				$gActualFinishFabcost=$gTotalFinishFabCost+($gTotalGreyFabCost-$gbatchAmt);
				$gExcessFinishFabCost=$gActualFinishFabcost+($gstolenAmt-$totalBomCost);
				//echo $gExcessFinishFabCost."=".$gActualFinishFabcost."+(".$gstolenAmt."-".$totalBomCost.")";die;
				
	}
	//echo $gExcessFinishFabCost;die;
	return fn_number_format($gExcessFinishFabCost,8,".","");
	exit();
}

function poWiseExmfgCost($poids,$type)
{
	//echo load_html_head_contents("Excess Mat. Cost", "../../../../", 1, 1,$unicode,'','');
	//echo $poids.'=';die;
	//$expData=explode('__',$data);
	//print_r($expData);
	//$poid=trim($expData[0]);
	//$type=$expData[1];
	//echo $type;
	global $user_id;
	$gExcessFinishFabCost=0; $refFormPoCond=0; $refFormJobCond=0;
	if($type==3)
	{
		$refFormPoCond="1";
		$refFormJobCond="2";
	}
	if($type==4)
	{
		$refFormPoCond="3";
		$refFormJobCond="4";
	}
	if($type==5)
	{
		$refFormPoCond="5";
		$refFormJobCond="6";
	}
	if(!empty($poids))
	{
		$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d, gbl_temp_engine f where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and b.id=f.ref_val and f.user_id = ".$user_id." and f.entry_form=880 and f.ref_from='$refFormPoCond' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";
		
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes);
		$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid=""; $poidsArr=array();
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
			$poidsArr[$row['ID']]=$row['ID'];
			
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
		
		//print_r($poidsArr); die;
		//Item Ratio Details
		$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details a, gbl_temp_engine b where 1=1 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=880 and b.ref_from='$refFormJobCond'";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		}
		unset($gmtsitemRatioSqlRes);
		//print_r($jobItemRatioArr);die;
		
		$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a, gbl_temp_engine b where 1=1 and a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=880 and b.ref_from='$refFormJobCond'";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		}
		unset($sqlContrastRes);
		
		//Stripe Details
		$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a, gbl_temp_engine b where 1=1 and a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=880 and b.ref_from='$refFormJobCond'";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
		}
		unset($sqlStripeRes);
		
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.lib_yarn_count_deter_id as DETAID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.composition as COMPOSITION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.dia_width as DIA_WIDTH, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
		from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, gbl_temp_engine c
		where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=880 and c.ref_from='$refFormPoCond' and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.fabric_source=1 ";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $fabDescArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$fabIdWiseGmtsDataArr[$row['ID']]['CONSTRUCTION']=$row['CONSTRUCTION'];
			$fabIdWiseGmtsDataArr[$row['ID']]['DETAID']=$row['DETAID'];
			$fabcolorArr=array();
			if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
			{
				foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
				{
					$fabcolorArr[$row['ID']][$row['COLOR_NUMBER_ID']][$fabcolor]=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
				}
			}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			$finAmt=$finReq*$row['RATE'];
			//$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			$fullfab=$row['CONSTRUCTION'].','.$row['COMPOSITION'].','.$row['GSM_WEIGHT'].','.$row['DIA_WIDTH'];
			$fullwofab=$row['CONSTRUCTION'].','.$row['COMPOSITION'].','.$row['GSM_WEIGHT'];
			$fabDescArr[$row['ID']]['fab']=$fullfab;
			$fabDescArr[$row['ID']]['fabwo']=$fullwofab;
			if($row['FABRIC_SOURCE']==2)
			{
				if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
				{
					foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
					{
						$cons=0;
						$cons=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
						$finReq=($planQty/$itemRatio)*($cons/$costingPer);
						$finAmt=$finReq*$row['RATE'];
						
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_amt']+=$greyAmt;
					}
				}
				else if ($sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]!="" && $row['COLOR_SIZE_SENSITIVE']==3)
				{
					$cons=0;
					$fabcolor=$sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']];
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
				else
				{
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
			}
		}
		unset($sqlfabRes);
		//print_r($fabIdWiseGmtsDataArr); die;
		
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 

		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b, gbl_temp_engine c where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.po_break_down_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=880 and c.ref_from='$refFormPoCond' and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		//echo $sqlYarn; die;
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
			//if($row['PRECOSTID']==23899) { echo $planQty.'='.$itemRatio.'='.$row['CONS_QNTY'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'='.$row['RATE'].'<br>';}
			$reqYarnArr[$row['POID']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['yarn_qty']+=$yarnReq;
			$reqYarnArr[$row['POID']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['yarn_amt']+=$yarnAmt;
			//$t[$row['YARN_ID']]+=$yarnAmt;
		}
		unset($sqlYarnRes);
		//echo "<pre>";
		//print_r($t); die;
		
		//Total Cost as per Budget
		foreach($reqYarnArr as $poid=>$podata)
		{
			foreach($podata as $compo=>$compodata)
			{
				foreach($compodata as $countid=>$countdata)
				{
					$bomYarnAvgRate=0;
					$bomYarnAvgRate=$countdata['yarn_amt']/$countdata['yarn_qty'];
					
					$gYarnBomQty[$poid]+=$countdata['yarn_qty'];
					$gYarnBomAmt[$poid]+=$countdata['yarn_amt'];
				}
			}
		}
		//print_r($gYarnBomAmt[18469]); //die;
		
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b, gbl_temp_engine c where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.po_break_down_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=880 and c.ref_from='$refFormPoCond' and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convFabArr=array();
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
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
				}
			}
		}
		//echo "ff"; die;
		$convReqQtyAmtArr=array(); $convRateArr=array();
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
			$libYarnDetaid=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['DETAID'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			$convRateArr[$row['CONVERTION_ID']]['fab']=$fabDescArr[$row['PRECOSTID']]['fab'];
			$convRateArr[$row['CONVERTION_ID']]['fabwo']=$fabDescArr[$row['PRECOSTID']]['fabwo'];
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
						$reqqnty=$qnty;
						$convAmt=$qnty*$convrate;
					}
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yamt']+=$convAmt;
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
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				else if($consProcessId==1 || $consProcessId==33)
				{
					$convrate=$row['CHARGE_UNIT'];
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				//echo $convrate.'='.$row['CHARGE_UNIT'].'='.$itemRatio.'='.$requirment.'='.$costingPer."<br>";
				if($consProcessId==134)
				{
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yamt']+=$convAmt;
				}
				if($consProcessId==1)
				{
					$fabconstruction=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['CONSTRUCTION'];
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kqty']+=$reqqnty;
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kamt']+=$convAmt;
				}
				if($consProcessId==31)
				{
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdqty']+=$reqqnty;
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdamt']+=$convAmt;
				}
				if($consProcessId==67 || $consProcessId==68 || $consProcessId==35 || $consProcessId==213 || $consProcessId==214 || $consProcessId==217 || $consProcessId==236 || $consProcessId==237 || $consProcessId==270)
				{
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaqty']+=$reqqnty;
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaamt']+=$convAmt;
				}
				$convRateArr[$row['POID']][$consProcessId][$rateColorId][$libYarnDetaid]['fdrate']=$convrate;
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes);
		
		//print_r($convReqQtyAmtArr[18908][1]);
		
		//Yarn Sevice Cost as per Budge
		foreach($convReqQtyAmtArr['yd'] as $poid=>$podata)
		{
			foreach($podata as $processid=>$convdata)
			{
				foreach($convdata as $colorid=>$colordata)
				{
					$bomYarnDAvgRate=0;
					$bomYarnDAvgRate=$colordata['yamt']/$colordata['yqty'];
					
					$gYarndBomQty[$poid]+=$colordata['yqty'];
					$gYarndBomAmt[$poid]+=$colordata['yamt'];
				}
			}
		}

		//Knitting Cost as per Budget
		foreach($convReqQtyAmtArr['knit'] as $poid=>$podata)
		{
			foreach($podata as $processid=>$convdata)
			{
				foreach($convdata as $fabconst=>$fabconstdata)
				{
					$bomKnitAvgRate=0;
					$bomKnitAvgRate=$fabconstdata['kamt']/$fabconstdata['kqty'];
					
					$gknitBomQty[$poid]+=$fabconstdata['kqty'];
					$gknitBomAmt[$poid]+=$fabconstdata['kamt'];
				}
			}
		}
		//print_r($gknitBomAmt[18469]);
		foreach($convReqQtyAmtArr['fd'] as $poid=>$podata)
		{	
			foreach($podata as $processid=>$convdata)
			{
				foreach($convdata as $fabcolor=>$fabcolordata)
				{
					$bomFdAvgRate=0;
					$bomFdAvgRate=$fabcolordata['fdamt']/$fabcolordata['fdqty'];
					$gFdBomQty[$poid]+=$fabcolordata['fdqty'];
					$gFdBomAmt[$poid]+=$fabcolordata['fdamt'];
				}
			}
		}
		//print_r($gFdBomAmt[18469]);		
		//PBA Cost as per Budget 
		foreach($convReqQtyAmtArr['pba'] as $poid=>$podata)
		{
			foreach($podata as $processid=>$procdata)
			{
				$bomPbaAvgRate=0;
				$bomPbaAvgRate=$procdata['pba']['pbaamt']/$procdata['pba']['pbaqty'];
				
				$gPbaBomQty[$poid]+=$procdata['pba']['pbaqty'];
				$gPbaBomAmt[$poid]+=$procdata['pba']['pbaamt'];
			}
		}
		$totalBomCostArr=array();
		foreach($poidsArr as $poid)
		{
			$totalBomCostArr[$poid]=$gYarnBomAmt[$poid]+$gYarndBomAmt[$poid]+$gknitBomAmt[$poid]+$gFdBomAmt[$poid]+$gPbaBomAmt[$poid];
			//echo $gYarnBomAmt[$poid].'=='.$gYarndBomAmt[$poid].'=='.$gknitBomAmt[$poid].'=='.$gFdBomAmt[$poid].'=='.$gPbaBomAmt[$poid].'<br>';
			//2469.4878====155.22994==1197.1945==
		}
		//print_r($totalBomCostArr[18469]); //die;
		
		$sqlYIssue="SELECT a.id as issue_id, a.issue_number, a.booking_no, a.knit_dye_source, a.knit_dye_company, a.issue_purpose, b.cons_rate, c.quantity as issue_qnty, c.po_breakdown_id, c.prod_id from inv_issue_master a, inv_transaction b, order_wise_pro_details c, gbl_temp_engine e
			where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=2 and b.item_category=1 and c.trans_type=2 and c.entry_form=3 and c.issue_purpose in (1,2,15,50) and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id ASC";
			 
		//echo $sqlYIssue; die;
		$sqlYarnIssue=sql_select($sqlYIssue);
		$yarnStolenArr=array(); $yarnratearr=array(); $yarnWoArr=array();
		foreach($sqlYarnIssue as $yirow)
		{
			$yarnStolenArr[$yirow[csf("po_breakdown_id")]][$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissqty']+=$yirow[csf("issue_qnty")];
			$yarnStolenArr[$yirow[csf("po_breakdown_id")]][$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissamt']+=$yirow[csf("issue_qnty")]*($yirow[csf("cons_rate")]/82);
			
			$yarnratearr[$yirow[csf("issue_id")]][$yirow[csf("prod_id")]]=($yirow[csf("cons_rate")]/82);
			$yarnWoArr[$yirow[csf("booking_no")]]['']['booking_no']=$yirow[csf("knit_dye_source")];
			$yarnWoArr[$yirow[csf("booking_no")]][$yirow[csf("prod_id")]]['rate']=($yirow[csf("cons_rate")]/82);
		}
		unset($sqlYarnIssue);
		
		$sql_ret = "SELECT a.id, a.recv_number, a.knitting_source, a.knitting_company, b.quantity, b.po_breakdown_id, b.prod_id, d.issue_id, b.issue_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d, gbl_temp_engine e
			where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9
			and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond'
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose in (1,2,15,50) ";
		//echo $sql_ret; die;
		$sqlYarnIssueRet=sql_select($sql_ret);
		foreach($sqlYarnIssueRet as $yirrow)
		{
			$retrate=$yarnratearr[$yirrow[csf("issue_id")]][$yirrow[csf("prod_id")]];
			$yarnStolenArr[$yirrow[csf("po_breakdown_id")]][$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretqty']+=$yirrow[csf("quantity")];
			$yarnStolenArr[$yirrow[csf("po_breakdown_id")]][$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretamt']+=$yirrow[csf("quantity")]*$retrate;
		}
		unset($sqlYarnIssueRet);
		
		$sqlGray="select a.knitting_source, a.knitting_company, a.receive_purpose, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.po_breakdown_id, c.quantity as quantity, b.order_yarn_rate as kniting_charge from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e 
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2,22) and c.entry_form in (2,22) 
		and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond'
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlGray; die;
		$sqlGrayRec=sql_select($sqlGray);
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$yarnStolenArr[$grrow[csf("po_breakdown_id")]][1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecqty']+=$grrow[csf("quantity")];
			$yarnStolenArr[$grrow[csf("po_breakdown_id")]][1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]);
		}
		unset($sqlGrayRec);
		
		$sqlRec = "SELECT a.id, a.recv_number, a.booking_no, a.knitting_source, a.supplier_id as knitting_company, d.grey_quantity as quantity, b.po_breakdown_id, b.prod_id, d.cons_avg_rate as order_rate, a.receive_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d, gbl_temp_engine e 
			where a.id=d.mst_id and d.transaction_type=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=1 and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in (1,2,15,50) ";
		//echo $sqlRec; die;
		$sqlYarnRec=sql_select($sqlRec);
		foreach($sqlYarnRec as $yrrow)
		{
			$knitsource=$yarnWoArr[$yrrow[csf("booking_no")]]['']['booking_no'];
			$yarnrecrate=$yarnWoArr[$yrrow[csf("booking_no")]][$yrrow[csf("prod_id")]]['rate'];
			
			$yarnStolenArr[$yrrow[csf("po_breakdown_id")]][$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecqty']+=$yrrow[csf("quantity")];
			$yarnStolenArr[$yrrow[csf("po_breakdown_id")]][$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecamt']+=$yrrow[csf("quantity")]*($yrrow[csf("order_rate")]/82);
		}
		unset($sqlYarnRec);
		
		//Stolen Yarn Value info 
		foreach($yarnStolenArr as $poid=>$podata)
		{
			foreach($podata as $issuepurpose=>$issuepurposedata)
			{
				foreach($issuepurposedata as $ysource=>$ysourcedata)
				{
					foreach($ysourcedata as $ysourcecom=>$ysourcecomdata)
					{
						$issqty=$issAmt=$stolenQty=$stolenAmt=0;
						$issqty=$ysourcecomdata['yissqty']-$ysourcecomdata['yissretqty'];
						$issAmt=$ysourcecomdata['yissamt']-$ysourcecomdata['yissretamt'];
						$stolenQty=$issqty-$ysourcecomdata['yrecqty'];
						$stolenAmt=$issAmt-$ysourcecomdata['yrecamt'];
						
						//echo $issuepurpose.'-'.$poid.'-'.$ysourcecomdata['yissamt'].'-'.$stolenAmt."<br>";
						$gstolenQty[$poid]+=$stolenQty;
						$gstolenAmt[$poid]+=$stolenAmt;
					}
				}
			}
		}
		//print_r($gstolenAmt[18469]);
		$sqlGYIssue="SELECT a.id as issue_id, b.quantity as issue_qnty, b.po_breakdown_id, b.prod_id, c.cons_rate, d.lot, d.brand_supplier, d.yarn_count_id, d.yarn_comp_type1st, d.product_name_details from inv_issue_master a, order_wise_pro_details b, inv_transaction c, product_details_master d, gbl_temp_engine e
			where a.id=c.mst_id and c.transaction_type=2 and c.item_category=1 and c.id=b.trans_id and c.prod_id=d.id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond'
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			
			//echo $sqlGYIssue; die;
		$sqlGYIssueRes=sql_select($sqlGYIssue); $greyYarnDtlsArr=array();
		foreach($sqlGYIssueRes as $isrow)
		{
			$str="";
			$str=$isrow[csf("yarn_count_id")].'**'.$isrow[csf("yarn_comp_type1st")].'**'.$isrow[csf("brand_supplier")].'**'.$isrow[csf("lot")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecdata']=$str;
			//$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecqty']+=$isrow[csf("issue_qnty")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecrate']=($isrow[csf("cons_rate")]/82);
		}
		unset($sqlGYIssueRes);
		
		$sqlGray="select a.id,b.id as dtls_id, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.po_breakdown_id, c.quantity as quantity, b.kniting_charge, b.order_yarn_rate,a.knitting_source from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2) and c.entry_form in (2) 
		and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond'
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		
		//echo $sqlGray; die;
		$sqlGrayRec=sql_select($sqlGray); $greymstIdArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$greymstIdArr[$grrow[csf("id")]]=$grrow[csf("id")];
		}
		$recv_cond=where_con_using_array($greymstIdArr,0,"receive_id");

		$knitting_bill_sql="SELECT b.receive_id,b.currency_id,b.rate, a.company_id,a.bill_date FROM subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and a.entry_form=438 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_cond";
		//echo $knitting_bill_sql; die;
		$knitting_bill_res=sql_select($knitting_bill_sql);
		$recv_wise_knitting_charge=array();
		foreach ($knitting_bill_res as $row)
		{
			$con_rate=set_conversion_rate($row[csf('currency_id')], $row[csf('bill_date')],$row[csf('company_id')]);
			// echo "<pre>";
			// echo $con_rate;
			// echo "</pre>";
			$recv_wise_knitting_charge[$row[csf('receive_id')]]=($row[csf('rate')]*$con_rate);
		}
		
		$greyMst_cond=where_con_using_array($greymstIdArr,0,"mst_id");
		
		$sqlYarn="select prod_id, used_qty,dtls_id,mst_id,amount from pro_material_used_dtls where entry_form=2 and status_active=1 and is_deleted=0 $greyMst_cond";
		//echo $sqlYarn;
		$sqlYarnUsed=sql_select($sqlYarn); $yusedArr=array(); $yusedArr1=array();
		foreach($sqlYarnUsed as $yurow)
		{
			$yusedArr[$yurow[csf("prod_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['amount']+=$yurow[csf("amount")];
		}
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		//$prod_arr_used=array();
		foreach($sqlGrayRec as $grrow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$grrow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			
			$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['yprodid'].=','.$grrow[csf("yarn_prod_id")];
			$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['grecqty']+=$grrow[csf("quantity")];

			if($grrow[csf("knitting_source")]==1)
			{
				$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]/82);
				$prodrateArr[$fabDescription]=($grrow[csf("kniting_charge")]/82);
			}
			else
			{
				$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
				$prodrateArr[$fabDescription]=($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
			}
			
			$exyarnid=explode(",",$grrow[csf("yarn_prod_id")]);
			
			foreach($exyarnid as $ynid)
			{
				$greyYarnDtlsArr[$grrow[csf("po_breakdown_id")]][$fabDescription][$ynid]['yrecqty']+=$yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty'];
				$greyYarnDtlsArr[$grrow[csf("po_breakdown_id")]][$fabDescription][$ynid]['yrecamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);
				$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['yrntotamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);
			}
		}
		unset($sqlGrayRec);
		// echo "<pre>";
		// print_r($grayDataArr);
		// echo "</pre>";
		//echo $grayDataArr[$grrow[csf("product_name_details")]]['yrntotamt'];
		
		//print_r($greyYarnDtlsArr);
		$sqlTrans = "select c.po_breakdown_id, a.from_order_id, a.to_order_id, b.to_prod_id, b.from_prod_id, c.trans_type, c.quantity as transfer_qnty, d.product_name_details,b.rate,b.transfer_value from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form=13 and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		
		//echo $sqlTrans; die;
		$sqlTransRes=sql_select($sqlTrans); $greyTransDtlsArr=array(); $greyTransinDtlsArr=array(); $trnsPoIdArr=array();
		foreach($sqlTransRes as $gtrrow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$gtrrow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			if($gtrrow[csf("trans_type")]==5)
			{
				//$greyTransinDtlsArr[$gtrrow[csf("po_breakdown_id")]][$fabDescription]['trinpoid'].=$gtrrow[csf("from_order_id")].',';
				$greyTransinDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['trinpoid']=$gtrrow[csf("to_order_id")];
				$greyTransinDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['trinqty']+=$gtrrow[csf("transfer_qnty")];
				//$greyTransinDtlsArr[$fabDescription]['trinamt']+=($gtrrow[csf("transfer_qnty")]*$prodrateArr[$fabDescription]);
				//change here
				$greyTransinDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['trinamt']+=($gtrrow[csf("transfer_value")]/82);
				//array_push($trnsPoIdArr,$gtrrow[csf('from_order_id')]);
				//echo $gtrrow[csf('from_order_id')].'i <br>';
				
				$gFromPoIdArr[$gtrrow[csf('from_order_id')]]=$gtrrow[csf('from_order_id')];
			}
			else if($gtrrow[csf("trans_type")]==6)
			{
				$greyTransDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['troutpoid']=$gtrrow[csf("to_order_id")];
				$greyTransDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['troutqty']+=$gtrrow[csf("transfer_qnty")];
				$greyTransDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['troutamt']+=$gtrrow[csf("transfer_qnty")]*$prodrateArr[$fabDescription];
				//array_push($trnsPoIdArr,$gtrrow[csf('to_order_id')]);
				//echo $gtrrow[csf("to_order_id")].'<br>';
			}
			$trnsPoIdArr[$gtrrow[csf('to_order_id')]]=$gtrrow[csf('to_order_id')];
			$trnsPoIdArr[$gtrrow[csf('from_order_id')]]=$gtrrow[csf('from_order_id')];
		}
		unset($sqlTransRes);
		
		$greyTransInRateFromFnc=fncGreyFinTransInRate($gFromPoIdArr,1);

		//Actual Gray Fabric cost
		$productWiseRate=array();
		foreach($grayDataArr as $poid=>$podata)
		{
			foreach($podata as $gprodname=>$gprodnamedata)
			{
				$span=1;
					
				$transoutAvgRate=0;
				$exyprodid=array_filter(array_unique(explode(",",$gprodnamedata['yprodid'])));
				$countYarn=count($exyprodid);
				
				$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
				$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
				
				$transoutrefId=$transoutref="";
				$transoutrefId=array_filter(array_unique(explode(",",$greyTransDtlsArr[$poid][$gprodname]['troutpoid'])));
				
				$transoutQty=$greyTransDtlsArr[$poid][$gprodname]['troutqty'];
	
				//$transoutAvgRate=$greytotamt/$gprodnamedata['yrntotamt'];
				$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
				$transoutAvgRate=fn_number_format($transoutAvgRate,8,".","");
				
				//echo $poid.'<br>';
				
				if($transoutQty==0 || $transoutQty=="") $transoutAvgRate=0;
				$transoutAmt=$transoutQty*$transoutAvgRate;
				$actualGreyCost=0;
				$actualGreyCost=$greytotamt-$transoutAmt;
				//echo $greytotamt.'-'.$transoutAmt.'<br>';
				
				//$productWiseRate[$poid][$gprodname]['rate']=$transoutAvgRate;
				$productWiseRate[$poid][$gprodname]['qty']+=$gprodnamedata['grecqty']-$transoutQty;
				$productWiseRate[$poid][$gprodname]['amt']+=$actualGreyCost;
				
				foreach($exyprodid as $yid)
				{
					$yqtykg=$greyYarnDtlsArr[$poid][$gprodname][$yid]['yrecqty'];
					$yamt=$greyYarnDtlsArr[$poid][$gprodname][$yid]['yrecamt'];
					$yavgprice=$yamt/$yqtykg;
					
					$gyarnQty+=$yqtykg;
					$gyarnAmt+=$yamt;
				}
				
				$ggrayQty+=$gprodnamedata['grecqty'];
				$ggrayAmt+=$greytotamt;
				
				$gtransoutQty+=$transoutQty;
				$gtransoutAmt+=$transoutAmt;
				$gactualgrayAmt[$poid]+=$actualGreyCost;
			}
		}
		//print_r($gactualgrayAmt[18469]);

		//Transfer In Status
		foreach($greyTransinDtlsArr as $tpoid=>$podata)
		{
			foreach($podata as $fabricdtls=>$fabricdtlsdata)
			{
				$bomTransinAvgRate=0;
				//$bomTransinAvgRate=$fabricdtlsdata['trinamt']/$fabricdtlsdata['trinqty'];
				$bomTransinAvgRate=$greyTransInRateFromFnc[$tpoid][$fabricdtls];
				$gTransInAmt=$fabricdtlsdata['trinqty']*$bomTransinAvgRate;
				//echo $bomTransinAvgRate.'-'.$fabricdtlsdata['trinqty'].'-'.$fabricdtlsdata['trinpoid'].'<br>';
				//$productWiseRate[$fabricdtlsdata['trinpoid']][$fabricdtls]['rate']=$bomTransinAvgRate;
				$productWiseRate[$fabricdtlsdata['trinpoid']][$fabricdtls]['qty']+=$fabricdtlsdata['trinqty'];
				$productWiseRate[$fabricdtlsdata['trinpoid']][$fabricdtls]['amt']+=$gTransInAmt;
				$gTransinQty[$fabricdtlsdata['trinpoid']]+=$fabricdtlsdata['trinqty'];
				$gTransinAmt[$fabricdtlsdata['trinpoid']]+=$gTransInAmt;
			}
		}
		/*echo "<pre>";
		print_r($productWiseRate[17287]);*/
		//Total Gray fabric cost
		$gTotalGreyFabCostArr=array();
		foreach($poidsArr as $poid)
		{
			$gTotalGreyFabCostArr[$poid]+=$gactualgrayAmt[$poid]+$gTransinAmt[$poid];
		}
		//print_r($gTotalGreyFabCostArr[18469]);
		
		
		$sqlIss="select b.color_id as COLOR_ID, c.prod_id as PROD_ID, c.po_breakdown_id as POID, b.rate as RATE, c.quantity as QUANTITY from inv_grey_fabric_issue_dtls b, order_wise_pro_details c, gbl_temp_engine e where b.id=c.dtls_id and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond' and c.entry_form in (16) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		
		$sqlIssArr=sql_select($sqlIss); $grayRateArr=array();
		foreach($sqlIssArr as $grow)
		{
			$grayRateArr[$grow["COLOR_ID"]][$grow["PROD_ID"]]['rate']=$grow["RATE"];
		}
		unset($sqlIssArr);
		//print_r($grayRateArr);
		
		$sqlBatch = "select a.id, a.color_id, a.entry_form, b.po_id, b.prod_id, b.batch_qnty as quantity, c.product_name_details, c.detarmination_id from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c, gbl_temp_engine e where a.id=b.mst_id and b.prod_id=c.id and b.po_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond' and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		
		$batchDataArr=array();
		$sqlBatchArr=sql_select($sqlBatch); $batchidarr=array(); $batchcolorarr=array(); $batchProdPoarr=array(); 
		foreach($sqlBatchArr as $brow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$brow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			//echo $grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate'].'i<br>';
			$amt=$brow[csf("quantity")]*($grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate']/82);
			$batchDataArr[$brow[csf("po_id")]][$brow[csf("color_id")]][$fabDescription]['batch_qty']+=$brow[csf("quantity")];
			$batchDataArr[$brow[csf("po_id")]][$brow[csf("color_id")]][$fabDescription]['batch_amt']+=$amt;
			$batchidarr[$brow[csf("id")]]=$brow[csf("id")];
			$batchcolorarr[$brow[csf("id")]]=$brow[csf("color_id")];
			
			$batchProdPoarr[$brow[csf("id")]][$brow[csf("prod_id")]]=$brow[csf("po_id")];
			
			$batchDataArr[$brow[csf("po_id")]][$brow[csf("color_id")]][$fabDescription]['dyeamt']+=$brow[csf("quantity")]*$convRateArr[$brow[csf("po_id")]][31][$brow[csf("color_id")]][$brow[csf("detarmination_id")]]['fdrate'];
		}
		unset($sqlBatchArr);
		//print_r($batchDataArr);
		
		$batchid_cond=where_con_using_array($batchidarr,0,"a.batch_id");
		
		$sqlSP="select a.batch_id, a.process_id, b.prod_id, b.production_qty, c.product_name_details, c.detarmination_id from pro_fab_subprocess a, pro_fab_subprocess_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=34 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchid_cond";
		$sqlSPArr=sql_select($sqlSP); $specialDataArr=array();
		foreach($sqlSPArr as $brow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$brow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			
			$batchcolor=$batchcolorarr[$brow[csf("batch_id")]];
			$batchPo=$batchProdPoarr[$brow[csf("batch_id")]][$brow[csf("prod_id")]];
			$amt=$brow[csf("production_qty")]*($convRateArr[$batchPo][$brow[csf("process_id")]][$batchcolor][$brow[csf("detarmination_id")]]['fdrate']);
			if($brow[csf("process_id")]==213 || $brow[csf("process_id")]==214 || $brow[csf("process_id")]==217 || $brow[csf("process_id")]==236 || $brow[csf("process_id")]==237 || $brow[csf("process_id")]==270 || $brow[csf("process_id")]==35) $brow[csf("process_id")]=35;
			$specialDataArr[$batchPo][$brow[csf("process_id")]][$batchcolor][$fabDescription]['sp_qty']+=$brow[csf("production_qty")];
			$specialDataArr[$batchPo][$brow[csf("process_id")]][$batchcolor][$fabDescription]['sp_amt']+=$amt;
		}
		unset($sqlSPArr);
		
		$sql_outsite_service="SELECT a.febric_description_id, a.process_id, a.wo_qty, a.batch_issue_qty, a.rate, a.amount, a.order_id, a.fin_dia, a.fin_gsm, a.grey_used, c.fabric_color_id, c.po_break_down_id, a.id
			FROM pro_grey_batch_dtls a, inv_receive_mas_batchroll b, wo_booking_dtls c, gbl_temp_engine e
			WHERE a.mst_id = b.id AND a.booking_dtls_id = c.id 
			and c.po_break_down_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond'
			AND b.entry_form = 92 AND c.is_deleted=0 AND a.is_deleted=0 AND b.is_deleted=0";
		//echo $sql_outsite_service;
		$res_outsite_service=sql_select($sql_outsite_service);
		$data_outsite_service=array();
		$fab_deter_mina_ids=array();
		$fin_dia_arr=array();
		foreach ($res_outsite_service as $row)
		{
			$fab_deter_mina_ids[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$fin_dia_arr[$fab_id]=$row[csf('fin_gsm')].",".$row[csf('fin_dia')];
		}
		$determina_cond=where_con_using_array($fab_deter_mina_ids,0,"a.id");
		$composition_arr=array(); $constructtion_arr=array();
	    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $determina_cond";
		$lib_composition_array = return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name", "id", "composition_name");
	    //echo $sql_deter;
	    $data_array=sql_select($sql_deter);
	    foreach($data_array as $row )
	    {
	    	$fab_id=$row[csf('id')];
	        if(array_key_exists($fab_id,$composition_arr))
	        {
	            $composition_arr[$fab_id]=$composition_arr[$fab_id].",".$lib_composition_array[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	        else
	        {
	            $composition_arr[$fab_id]=$row[csf('construction')].",".$lib_composition_array[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	    }
	    //print_r($composition_arr[4]);
	    
	    foreach ($res_outsite_service as $row)
		{
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$product_details=$composition_arr[$row[csf('febric_description_id')]].",".$fin_dia_arr[$fab_id];
			if($row[csf('process_id')]==33)
			{
				$data_outsite_service[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('grey_used')];
				$data_outsite_service[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('grey_used')]*$row[csf('rate')]);
			}
			else
			{
				if($row[csf('process_id')]==213 || $row[csf('process_id')]==214 || $row[csf('process_id')]==217 || $row[csf('process_id')]==236 || $row[csf('process_id')]==237 || $row[csf('process_id')]==270 || $row[csf('process_id')]==35) $row[csf('process_id')]=35;
				
				$data_outsite_service[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('batch_issue_qty')];
				$data_outsite_service[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('batch_issue_qty')]*$row[csf('rate')]);
			}
		}
		
		/*$sql_heat_set="SELECT b.po_id, c.product_name_details, c.detarmination_id, b.batch_qnty AS batch_qnty, a.color_id
						FROM pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c, gbl_temp_engine e
						WHERE a.id = b.mst_id AND a.entry_form = 0 AND c.id=b.prod_id 
						
						and b.po_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond'
						AND b.status_active = 1 AND b.is_deleted = 0 AND a.is_deleted = 0";*/
						
						
						
		$sql_heat_set="SELECT b.po_id, c.product_name_details, e.batch_qty AS batch_qnty, a.color_id
						FROM pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c, pro_fab_subprocess d, pro_fab_subprocess_dtls e, gbl_temp_engine f
						WHERE a.id = b.mst_id AND c.id=b.prod_id AND a.id=d.batch_id AND d.id=e.mst_id AND b.prod_id=e.prod_id AND c.id=e.prod_id AND a.entry_form = 0 and b.po_id=f.ref_val and f.user_id = ".$user_id." and f.entry_form=880 and f.ref_from='$refFormPoCond' AND d.entry_form = 32 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND a.is_deleted = 0";
		//echo $sql_heat_set;

		$res_heat_set=sql_select($sql_heat_set);

		$data_heat_set=array();
		//print_r($convRateArr[$poid][33]);
		foreach ($res_heat_set as $row) 
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row[csf('product_name_details')]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$rate=0;
			$rate=$convRateArr[$row[csf('po_id')]][33][$row[csf('color_id')]][$row[csf("detarmination_id")]]['fdrate'];
			$data_heat_set[$row[csf('po_id')]][$row[csf('color_id')]][$fabDescription]['amount']+=($row[csf('batch_qnty')]*$rate);
			$data_heat_set[$row[csf('po_id')]][$row[csf('color_id')]][$fabDescription]['qnty']+=$row[csf('batch_qnty')];
		}
		
		$dataArrayfinish = "select a.id as ID, a.entry_form as ENTRY_FORM, a.booking_id as BOOKINGID, a.knitting_source as KNITTING_SOURCE, a.receive_basis as RECEIVEBASIS, a.currency_id as CURRENCY_ID, b.batch_id as BATCHID, b.rate as RATE, c.po_breakdown_id as POID, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, b.grey_used_qty as GREY_USED_QTY, b.receive_qnty as RECEIVE_QNTY, b.grey_fabric_rate as GREY_FABRIC_RATE, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2";
		
		//echo $dataArrayfinish; die;
		$dataArrayfinishArr=sql_select($dataArrayfinish);
		$bookingidArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				$bookingidArr[$row['BOOKINGID']]=$row['BOOKINGID'];
			}
		}
		$bookingid_cond=where_con_using_array($bookingidArr,0,"a.id");
		$servBookSql="select a.id, b.booking_no, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.dia_width, b.rate, b.process from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingid_cond";
		$servBookSqlArr=sql_select($servBookSql); $bookingRateArr=array(); $bookingProcessRateArr=array();
		foreach($servBookSqlArr as $worow)
		{
			$conv_fabric="";
			
			if($worow[csf("dia_width")]=="") $conv_fabric=$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab'];
			else $conv_fabric=$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fabwo'].','.$worow[csf("dia_width")];
			//echo $conv_fabric.'<br>';
			$fabDescription="";
			$exFabric=array_filter(explode(",",$conv_fabric));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			
			$bookingProcessRateArr[$worow[csf("process")]][$fabDescription][$worow[csf("fabric_color_id")]]['worateprocess']=$worow[csf("rate")];
			$bookingRateArr[$worow[csf("id")]][$fabDescription][$worow[csf("fabric_color_id")]]['worate']=$worow[csf("rate")];
		}
		unset($servBookSqlArr);
		
		$recDataRetArr=array(); $finishDataArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row['PRODUCT_NAME_DETAILS']));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			if($row['ENTRY_FORM']==7)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				//$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_amt']+=$amt;
				
				$recDataRetArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
				$fabProdPurchBatchArr[$row['POID']][$fabDescription][$row['COLOR_ID']][$row['BATCHID']]=1;
			}
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				//$avgQty=((1-($row['QUANTITY']/$row['GREY_USED_QTY']))*$row['GREY_USED_QTY'])+$row['QUANTITY'];
				$avgQty=($row['GREY_USED_QTY']/$row['RECEIVE_QNTY'])*$row['QUANTITY'];
				//echo $avgQty.'='.$row['QUANTITY'].'='.$row['GREY_USED_QTY'].'='.'kausar<br>';
				$amt=$avgQty*($row['GREY_FABRIC_RATE']/82);
				$batchDataArr[$row['POID']][$row['COLOR_ID']][$fabDescription]['batch_qty']+=$avgQty;
				$batchDataArr[$row['POID']][$row['COLOR_ID']][$fabDescription]['batch_amt']+=$amt;
				$finWoRate=$bookingRateArr[$row['BOOKINGID']][$fabDescription][$row['COLOR_ID']]['worate'];
				$amt=$avgQty*$finWoRate;
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_amt']+=$amt;
				
				$fabProdPurchBatchArr[$row['POID']][$fabDescription][$row['COLOR_ID']][$row['BATCHID']]=1;
				
				$grey_used_amt=0;
				
				$grey_used_amt=$row['GREY_USED_QTY']*$finWoRate;
				//echo $row['GREY_USED_QTY'].'='.$bookingRateArr[$fabDescription][$row['COLOR_ID']]['worate'].'<br>';
				$batchDataArr[$row['POID']][$row['COLOR_ID']][$fabDescription]['dyeqtyrec']+=$row['GREY_USED_QTY'];
				$batchDataArr[$row['POID']][$row['COLOR_ID']][$fabDescription]['dyeamtrec']+=$grey_used_amt;
			}
		}
		unset($dataArrayfinishArr);
		//print_r($finishDataArr[54013]); die;
		
		$sqlTrans="SELECT c.po_breakdown_id as POID, a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, b.batch_id as BATCH_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id=e.ref_val and e.user_id = ".$user_id." and e.entry_form=880 and e.ref_from='$refFormPoCond' and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		
		//echo $sqlTrans; die;
		$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array(); $transOutArr=array(); $transInArr=array(); $batchIDArr=array(); $fFromPoIdArr=array();
		foreach($sqlTransArr as $row)
		{
			$transVal=$amt=0;
			$transVal=$row['TRANSFER_VALUE']/82;
			//echo $recRate;
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row['PRODUCT_NAME_DETAILS']));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			if($row['TRANS_TYPE']==5)//trans in
			{
				/*if($batchEntryFormArr[$row['BATCH_ID']]==37)
				{*/
					//$amt=$row['QUANTITY']*$transRate;
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTin_qty']+=$row['QUANTITY'];
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTin_amt']+=$transVal;
					//$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['trinpoid'].=$row['FROM_ORDER_ID'].',';
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['trinpoid']=$row['FROM_ORDER_ID'];
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['batchid']=$row['BATCH_ID'];
					array_push($batchIDArr,$row['BATCH_ID']);
					
					$fFromPoIdArr[$row['FROM_ORDER_ID']]=$row['FROM_ORDER_ID'];
				//}
			}
			else if($row['TRANS_TYPE']==6)//trans out
			{
				if($fabProdPurchBatchArr[$row['POID']][$fabDescription][$row['COLOR_ID']][$row['BATCH_ID']]==1)
				{
					//$amt=$row['QUANTITY']*$transRate;
					$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_qty']+=$row['QUANTITY'];
					$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_amt']+=$transVal;
					$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_ref'].=$row['TO_ORDER_ID'].',';
				}
			}
			array_push($trnsPoIdArr,$row['TO_ORDER_ID']);
			array_push($trnsPoIdArr,$row['FROM_ORDER_ID']);
		}
		unset($sqlTransArr);
		//print_r($transInArr);
		
		$finishTransInRateFromFnc=fncGreyFinTransInRate($fFromPoIdArr,2);
		
		$batchID_cond=where_con_using_array($batchIDArr,0,"id");
		$batchEntryArr=return_library_array( "select id, entry_form from pro_batch_create_mst where 1=1 $batchID_cond", "id", "entry_form");
		
		//Actual Gray Fabric cost
		foreach($batchDataArr as $poid=>$podata)
		{
			foreach($podata as $fabcolorid=>$fabcolordata)
			{
				foreach($fabcolordata as $fabric=>$fabdata)
				{
					$greyAvgRate=0;
					// $greyAvgRate=$fabdata['batch_amt']/$fabdata['batch_qty'];
					/*if($productWiseRate[$poid][$fabric]['amt']=="" && $productWiseRate[$poid][$fabric]['qty']=="")
					{*/
						$greyAvgRate=($productWiseRate[$poid][$fabric]['amt']/$productWiseRate[$poid][$fabric]['qty']);
					/*}
					else $greyAvgRate=0;*/
					$fabdata['batch_amt']=fn_number_format(($greyAvgRate*$fabdata['batch_qty']),8,".",""); // change batch amount 
					if($fabdata['batch_amt']=="") $fabdata['batch_amt']=0;
					/*if($poid==18908)
					{ echo $fabdata['batch_amt'].'='.$greyAvgRate.'*'.($fabdata['batch_qty']*1).'<br>';}*/
					$dyeqty=$dyeamt=0;
					$dyeqty=$fabdata['batch_qty']+$fabdata['dyeqtyrec'];
					$dyeamt=$fabdata['dyeamt']+$fabdata['dyeamtrec'];
					$dyeAvgRate=$dyeamt/$dyeqty;
					//echo $fabdata['batch_amt'].'-'.$dyeamt.'-'.$greyAvgRate.'<br>';
					//echo $productWiseRate[$poid][$fabric]['amt'].'-'.$productWiseRate[$poid][$fabric]['qty'].'-'.$fabdata['batch_qty'].'-'.$greyAvgRate.'<br>';
					
					//383687.55-73148.62-27569.4
	
					$heatSetQty=$data_heat_set[$poid][$fabcolorid][$fabric]['qnty']+$data_outsite_service[$poid][$fabcolorid][$fabric][33]['qnty'];
					$heatSetAmt=$data_heat_set[$poid][$fabcolorid][$fabric]['amount']+$data_outsite_service[$poid][$fabcolorid][$fabric][33]['amount'];
					$heatSetRate=$heatSetAmt/$heatSetQty;
					
					$peachFinishQty=$specialDataArr[$poid][67][$fabcolorid][$fabric]['sp_qty']+$data_outsite_service[$poid][$fabcolorid][$fabric][67]['qnty'];
					$peachFinishAmt=$specialDataArr[$poid][67][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$poid][$fabcolorid][$fabric][67]['amount'];
					$peachFinishRate=$peachFinishAmt/$peachFinishQty;
					//echo $specialDataArr[$poid][67][$fabcolorid][$fabric]['sp_amt'].'-'.$data_outsite_service[$poid][$fabcolorid][$fabric][67]['amount'].'<br>';
					
					$brushingQty=$specialDataArr[$poid][68][$fabcolorid][$fabric]['sp_qty']+$data_outsite_service[$poid][$fabcolorid][$fabric][68]['qnty'];
					$brushingAmt=$specialDataArr[$poid][68][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$poid][$fabcolorid][$fabric][68]['amount'];
					$brushingRate=$brushingAmt/$brushingQty;
					
					$aopQty=$specialDataArr[$poid][35][$fabcolorid][$fabric]['sp_qty']+$data_outsite_service[$poid][$fabcolorid][$fabric][35]['qnty'];
					$aopAmt=$specialDataArr[$poid][35][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$poid][$fabcolorid][$fabric][35]['amount'];
					$aopRate=$aopAmt/$aopQty;
					
					$finishAmt=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_amt'];
					//echo $fabdata['batch_amt'].'-'.$dyeamt.'-'.$peachFinishAmt.'-'.$brushingAmt.'-'.$aopAmt.'-'.$finishAmt.'-'.$heatSetAmt.'<br>';
					//echo $finishAmt.'<br>';
					$finishFabCost=$fabdata['batch_amt']+$dyeamt+$peachFinishAmt+$brushingAmt+$aopAmt+$finishAmt+$heatSetAmt;
					
					
					$finishQty=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_qty'];
					$finRate=$finishFabCost/$finishQty;
					//echo $finishFabCost."-".$finRate."<br>";
					
					$trnsOUtQty=$trnsOUtAmt=0;
					$trnsOUtQty=$transOutArr[$poid][$fabric][$fabcolorid]['finTout_qty'];
					//$trnsOUtAmt=$transOutArr[$poid][$fabric][$fabcolorid]['finTout_amt'];
					$finRate=fn_number_format($finRate,8,".","");
					$trnsOUtQty=fn_number_format($trnsOUtQty,8,".","");
					$trnsOUtAmt=$finRate*$trnsOUtQty;
					
					$fabricFinishCost=$finishFabCost-$trnsOUtAmt;
					
					$gbatchQty+=$fabdata['batch_qty'];
					$gbatchAmt[$poid]+=$fabdata['batch_amt'];
					$gdyeingAmt+=$dyeamt;
					
					$a+=$fabdata['batch_amt'];
					$b+=$dyeamt;
					$c+=$trnsOUtAmt;
					
					$gFabricAmt[$poid]+=$fabricFinishCost;
				}
			}
		}
		//echo $gbatchQty."-".$a."-".$b."-".$c;
		 //396268.42-174595.06-5081.752 
		 //396287.29
		//print_r($gFabricAmt[18469]);// $gdyeingAmt;

		//Transfer In Status
		foreach($transInArr as $poid=>$podata)
		{
			foreach($podata as $fabricdtls=>$fabricdtlsdata)
			{
				foreach($fabricdtlsdata as $fabriccolor=>$colordata)
				{
					if($batchEntryArr[$colordata['batchid']]!=37)
					{
						$bomTransinAvgRate=$finTransinAmt=0;
						//$bomTransinAvgRate=$colordata['finTin_amt']/$colordata['finTin_qty'];
						//echo "kk";
						//$bomTransinAvgRate=$finishTransInRateFromFnc[$colordata['trinpoid']][$fabriccolor][$fabricdtls];
						$bomTransinAvgRate=$finishTransInRateFromFnc[$fabriccolor][$fabricdtls];
							
						$finTransinAmt=$colordata['finTin_qty']*$bomTransinAvgRate;
						//echo $poid.'*'.$fabriccolor.'*'.$fabricdtls.'*'.$bomTransinAvgRate.'<br>';
						
						$gfinishQty[$poid]+=$colordata['finTin_qty'];
						$gfinishAmt[$poid]+=$finTransinAmt;
					}
				}
			}
		}
		//echo $gFabricAmt;
		//print_r($gfinishAmt[18469]);
		$gExcessFinishFabCostArr=array();
		foreach($poidsArr as $poid)
		{
			$gTotalFinishFabCost=$gActualFinishFabcost=$gExcessFinishFabCost=0;
			
			$gTotalFinishFabCost=$gFabricAmt[$poid]+$gfinishAmt[$poid];
			$gActualFinishFabcost=$gTotalFinishFabCost+($gTotalGreyFabCostArr[$poid]-$gbatchAmt[$poid]);
			/*if($poid==18908)
			{ echo $gActualFinishFabcost.'='.$gTotalFinishFabCost.'='.$gTotalGreyFabCostArr[$poid].'='.$gbatchAmt[$poid].'<br>'; }*/
			
			$gExcessFinishFabCost=$gActualFinishFabcost+($gstolenAmt[$poid]-$totalBomCostArr[$poid]);
			$gExcessFinishFabCostArr[$poid]=fn_number_format($gExcessFinishFabCost,8,".","");
			/*if($poid==18469)
			{ echo $gTotalFinishFabCost.'='.$gActualFinishFabcost.'='.$gExcessFinishFabCost.'<br>'; }*/
			/*if($gExcessFinishFabCost!=0)
			{
				//echo $gstolenAmt[$poid].'='.$totalBomCostArr[$poid].'<br>';
			}*/
		}
	}
	//echo "==";
	//print_r($gExcessFinishFabCostArr[18469]);
	//return fn_number_format($gExcessFinishFabCost,8,".","");
	return $gExcessFinishFabCostArr;
	exit();
}

if($action=="buyer_details_list_view")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$a="123456789012345678946587878976";
	//echo bcadd($a,$a,20); die;
	$report_type=str_replace("'","",$reporttype);
	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$shipStatus=str_replace("'","",$cbo_ship_status);
	
	$orderStatus=str_replace("'","",$cbo_order_status);
	$cbo_status=str_replace("'","",$cbo_status);
	$buyer_id=str_replace("'","",$cbo_buyer_name);
	$season_id=str_replace("'","",$cbo_season_id);
	$client_id=str_replace("'","",$cbo_client);
	$style_ref=str_replace("'","",$txt_style_ref);
	
	$from_year=str_replace("'","",$cbo_from_year);
	$to_year=str_replace("'","",$cbo_to_year);
	
	$buyerCond = ""; $calAlloBuyerCond=""; $buyerYarnCond="";
	if ($buyer_id == 0) 
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) 
		{
			if ($_SESSION['logic_erp']["buyer_id"] != "")
			{
				$buyerCond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$calAlloBuyerCond = " and b.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyerYarnCond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			}
			else
			{
				$buyerCond = "";
				$calAlloBuyerCond=""; 
				$buyerYarnCond="";
			}
		}
		else 
		{
			$buyerCond = "";
			$calAlloBuyerCond=""; 
			$buyerYarnCond="";
		}
	} 
	else 
	{
		$buyerCond = " and a.buyer_name=$buyer_id";
		$calAlloBuyerCond="and b.buyer_id='$buyer_id'";
		$buyerYarnCond=" and a.buyer_id='$buyer_id'";
	}
	
	$exfirstYear=explode('-',$from_year);
	$exlastYear=explode('-',$to_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exlastYear[1];
	$yearMonth_arr=array(); $yearStartEnd_arr=array(); $j=12; $i=1;
	$startDate=''; $endDate="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$yearMonth_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	
	$month_cond=""; $calDateCond=""; $monthYarn_cond="";

	$month_cond="and b.shipment_date between '$startDate' and '$endDate'";
	
	$calDateCond="and b.date_calc between '$startDate' and '$endDate'";
	$monthYarn_cond="and b.transaction_date between '$startDate' and '$endDate'";
	if($season_id==0) $seasonYarn_cond=""; else $seasonYarn_cond=" and d.season in ( $season_id )";
	if($location_id==0) $locationYarn_cond=""; else $locationYarn_cond=" and d.location_id in ( $location_id )";
	
	//var_dump($fiscalMonth_arr);
	if($location_id!=0) $capLocationCond="and a.location_id='$location_id'"; else $capLocationCond="";
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($shipStatus==1) $shipStatusCond="and b.shiping_status in (1,2)"; else if($shipStatus==2) $shipStatusCond="and b.shiping_status in (3)"; else $shipStatusCond="";
	if($orderStatus==0) $orderStatusCond=""; else $orderStatusCond=" and b.is_confirmed in ( $orderStatus )";
	if($season_id==0) $seasonCond=""; else $seasonCond=" and a.season_buyer_wise in ( $season_id )";
	if($client_id==0) $clientCond=""; else $clientCond=" and a.client_id in ( $client_id )";
	if(trim($style_ref)=="") $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='$style_ref'";
	
	$sql_po="select a.job_no as JOB_NO, a.id as JOBID, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as POID, b.shipment_date as SHIPMENT_DATE, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shiping_status as SHIPING_STATUS, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $month_cond $buyerCond $jobLocationCond $shipStatusCond $orderStatusCond $seasonCond $clientCond $styleRefCond";//(a.set_smv/a.total_set_qnty)
	//echo $sql_po; die;
	$sql_po_res=sql_select($sql_po); $poidstr=$jobId=""; $poididarr=array(); $jobididarr=array();
	$Ex_Mat_Cost_From_Function=array();
	$buyer_wise_po_arr=array(); $powisebuyerArr=array();
	foreach($sql_po_res as $row)
	{
		$poidstr.=$row['POID'].',';
		$poididarr[$row['POID']]=$row['POID'];
		$jobididarr[$row['JOBID']]=$row['JOBID'];
		if($jobId=="") $jobId="'".$row["JOBID"]."'"; else $jobId.=",'".$row["JOBID"]."'";
		//$exMatCostP=poWiseExmfgCost($row['POID'],3);
		//$Ex_Mat_Cost_From_Function[$row["BUYER_NAME"]]+=fn_number_format($exMatCostP,8,".","");
		//echo "<pre>".$row['POID']."=>".$exMatCostP."</pre>";
		$buyer_wise_po_arr[$row["BUYER_NAME"]][$row['POID']]=$row['POID'];
		$powisebuyerArr[$row['POID']]=$row['BUYER_NAME'];
	}
	$con = connect();
	//print_r($buyer_wise_po_arr); die;
	$po_ids=array();
	$po_ids=array_filter(array_unique(explode(",",$poidstr)));
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=880");
	oci_commit($con);
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 1, $poididarr, $empty_arr);//PO ID
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 2, $jobididarr, $empty_arr);//Job ID
	//fnc_tempengine($table_name, $user_id, $entry_form, $ref_from, $ref_id_arr,  $ref_str_arr)
	
	$exMatCostP=poWiseExmfgCost(implode(",",$poididarr),3);
	//echo "kausar"; die;
	//print_r($exMatCostP);
	//asort($exMatCostP);
	foreach($exMatCostP as $rpoid=>$pdata)
	{
		$Ex_Mat_Cost_From_Function[$powisebuyerArr[$rpoid]]=bcadd($Ex_Mat_Cost_From_Function[$powisebuyerArr[$rpoid]],$pdata,6);
		//$Ex_Mat_Cost_From_Function[$powisebuyerArr[$rpoid]]+=fn_number_format($pdata,8,".","");
	}
	/*echo "<pre>";
	print_r($exMatCostP); die;*/
	
	$actualPoSql=sql_select("select a.job_no as JOBNO, a.po_break_down_id as POID, a.acc_po_no as ACTUALPO from wo_po_acc_po_info a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=880 and b.ref_from=1 and a.status_active=1 and a.is_deleted=0");
	//echo "select job_no as JOBNO, po_break_down_id as POID, acc_po_no as ACTUALPO from wo_po_acc_po_info where status_active=1 and is_deleted=0 $accpoid_cond";
	$actualPoArr=array();
	foreach($actualPoSql as $actrow)
	{
		$actualPoArr[$actrow["POID"]][$actrow["ACTUALPO"]]=$actrow["ACTUALPO"];
	}
	unset($actualPoSql);
	
	$budgetAmt_arr=array();
	$sqlBomAmt="select a.job_id as JOB_ID, a.po_id as PO_ID, a.greypurch_amt as GREYPURCH_AMT, a.finpurch_amt as FINPURCH_AMT, a.yarn_amt as YARN_AMT, a.conv_amt as CONV_AMT, a.trim_amt as TRIM_AMT, a.emb_qty as EMB_QTY, a.emb_amt as EMB_AMT, a.wash_qty as WASH_QTY, a.wash_amt as WASH_AMT,a.print_qty as PRINT_QTY,a.print_amt AS PRINT_AMT,a.special_works_qty as SPECIAL_WORKS_QTY,a.special_works_amt as SPECIAL_WORKS_AMT,a.gmts_dyeing_qty as GMTS_DYEING_QTY,a.gmts_dyeing_amt as GMTS_DYEING_AMT,a.others_qty as OTHERS_QTY,a.others_amt AS OTHERS_AMT from bom_process a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and a.status_active=1 and a.is_deleted=0 ";
	//echo $sqlBomAmt;die;
	
	$sqlBomAmtRes=sql_select($sqlBomAmt);
	foreach($sqlBomAmtRes as $row)
	{
		$budgetAmt_arr[$row["PO_ID"]]['fab']=$row["GREYPURCH_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['purchfin_amt']=$row["FINPURCH_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['yarn']=$row["YARN_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['conv']=$row["CONV_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['trim']=$row["TRIM_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['embqty']=$row["EMB_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['emb']=$row["EMB_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['washqty']=$row["WASH_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['wash']=$row["WASH_AMT"];

		// adding process by helal
		$budgetAmt_arr[$row["PO_ID"]]['print_qty']=$row["PRINT_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['print_amt']=$row["PRINT_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_qty']=$row["SPECIAL_WORKS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_amt']=$row["SPECIAL_WORKS_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_qty']=$row["GMTS_DYEING_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_amt']=$row["GMTS_DYEING_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['others_qty']=$row["OTHERS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['others_amt']=$row["OTHERS_AMT"];
		// adding process by helal
	}
	unset($sqlBomAmtRes);

	// echo "<pre>";
	// print_r($budgetAmt_arr);
	// echo "</pre>";
	
	$sql_budget="select a.job_no as JOB_NO, a.approved as APPROVED, a.costing_per as COSTING_PER, a.exchange_rate as EXCHANGE_RATE, b.margin_pcs_bom as MARGIN_PCS_BOM from wo_pre_cost_mst a, wo_pre_cost_dtls b, gbl_temp_engine c where a.job_id=b.job_id and a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=2 and c.user_id = ".$user_id." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	
	$sql_budget_res=sql_select($sql_budget); $budget_arr=array();
	foreach($sql_budget_res as $row)
	{
		$budget_arr[$row["JOB_NO"]]['app']=$row["APPROVED"];
		$budget_arr[$row["JOB_NO"]]['margin_pcs']=$row["MARGIN_PCS_BOM"];
		$budget_arr[$row["JOB_NO"]]['costing_per']=$row["COSTING_PER"];
		$budget_arr[$row["JOB_NO"]]['exchange_rate']=$row["EXCHANGE_RATE"];
	}
	unset($sql_budget_res);
	
	$po_month_arr=array(); $job_arr=array(); $po_arr=array(); $poExchangeRatearr=array(); $fullshipedpoArr=array();
	foreach($sql_po_res as $row)
	{
		$costing_per=0; $costingPer=0; $matCost=0; $poMatCost=0; $actualPono=0;
		$costing_per=$budget_arr[$row["JOB_NO"]]['costing_per'];
		$poExchangeRatearr[$row['POID']]=$budget_arr[$row["JOB_NO"]]['exchange_rate'];
		$actualPono=count($actualPoArr[$row["POID"]]);
		
		if($costing_per==1) $costingPer=12;
		if($costing_per==2) $costingPer=1;
		if($costing_per==3) $costingPer=24;
		if($costing_per==4) $costingPer=36;
		if($costing_per==5) $costingPer=48;

		$matCost=$budgetAmt_arr[$row['POID']]['fab']+$budgetAmt_arr[$row['POID']]['yarn']+$budgetAmt_arr[$row['POID']]['conv']+$budgetAmt_arr[$row['POID']]['trim']+$budgetAmt_arr[$row['POID']]['emb']+$budgetAmt_arr[$row['POID']]['wash']+$budgetAmt_arr[$row['POID']]['print_amt']+$budgetAmt_arr[$row['POID']]['special_works_amt']+$budgetAmt_arr[$row['POID']]['gmts_dyeing_amt']+$budgetAmt_arr[$row['POID']]['others_amt']; // adding process by helal
		
		$mfgMaterialCost=$budgetAmt_arr[$row['POID']]['yarn']+$budgetAmt_arr[$row['POID']]['conv'];
		//Month Buyer Details
		$month_buyer="";
		$shipment_date=date("Y-m",strtotime($row["SHIPMENT_DATE"]));
		//$month_buyer=$row["BUYER_NAME"].'_'.$shipment_date;
		$month_buyer=$row["BUYER_NAME"];
		
		$buyerMonth_list[$month_buyer]=$month_buyer;
		
		$poQtyPcs=0; $poValue=0; $booked_min=0;
		$poQtyPcs=$row["PO_QUANTITY"];
		$poValue=$row["PO_TOTAL_PRICE"];
		$booked_min=$row["SET_SMV"];
		
		$po_month_arr[$month_buyer]['min']+=$booked_min;
		$po_month_arr[$month_buyer]['pcs']+=$poQtyPcs;
		$po_month_arr[$month_buyer]['val']+=$poValue;
		$po_month_arr[$month_buyer]['actualpo']+=$actualPono;
		$po_month_arr[$month_buyer]['mfgMatCost']+=$mfgMaterialCost;
		$po_month_arr[$month_buyer]['purchfin_amt']=bcadd($po_month_arr[$month_buyer]['purchfin_amt'],$budgetAmt_arr[$row['POID']]['purchfin_amt'],6);
		//$po_month_arr[$month_buyer]['purchfin_amt']+=$budgetAmt_arr[$row['POID']]['purchfin_amt'];
		$po_month_arr[$month_buyer]['trim']+=$budgetAmt_arr[$row['POID']]['trim'];
		$po_month_arr[$month_buyer]['washamt']+=$budgetAmt_arr[$row['POID']]['wash'];
		$po_month_arr[$month_buyer]['embamt']+=$budgetAmt_arr[$row['POID']]['emb'];

		// adding process by helal
		$po_month_arr[$month_buyer]['print_amt']+=$budgetAmt_arr[$row['POID']]['print_amt'];
		$po_month_arr[$month_buyer]['special_works_amt']+=$budgetAmt_arr[$row['POID']]['special_works_amt'];
		$po_month_arr[$month_buyer]['gmts_dyeing_amt']+=$budgetAmt_arr[$row['POID']]['gmts_dyeing_amt'];
		$po_month_arr[$month_buyer]['others_amt']+=$budgetAmt_arr[$row['POID']]['others_amt'];
		// adding process by helal

		if($row["SHIPING_STATUS"]==3)
		{
			$po_month_arr[$month_buyer]['fullshiped']+=$poValue;
			$po_arr[$row['POID']]['fullship_qty']+=$poQtyPcs;
			$fullshipedpoArr[$row['POID']]=$poValue;
		}
		else if($row["SHIPING_STATUS"]==2)
		{
			$po_month_arr[$month_buyer]['partial']+=$poValue;
		}
		else 
		{
			$po_month_arr[$month_buyer]['pending']+=$poValue;
		}
		
		$job_arr[$month_buyer]['job'][$row["JOB_NO"]]=$row["JOB_NO"];
		$po_arr[$row['POID']]['po_id']=1;
		$po_arr[$row['POID']]['month_buyer']=$month_buyer;
		$po_arr[$row['POID']]['ship_sta']=$row[csf("SHIPING_STATUS")];
		$po_arr[$row['POID']]['po_price']=$row[csf("UNIT_PRICE")];
		$po_arr[$row['POID']]['poVal']+=$poValue;
		
		if($budget_arr[$row["JOB_NO"]]['app']==1)
		{
			$po_arr[$row['POID']]['apppo']=1;
			$job_arr[$month_buyer]['fobjob'][$row["JOB_NO"]]=$row["JOB_NO"];
			$po_month_arr[$month_buyer]['fob']+=$poValue;
			$margin=0;
			$margin=$budget_arr[$row["JOB_NO"]]['margin_pcs']*($poQtyPcs/$row[csf("TOTAL_SET_QNTY")]);
			$po_month_arr[$month_buyer]['margin']+=$margin;
			$poMatCost=$matCost;//($matCost/$costingPer)*($poQtyPcs/$row[csf("total_set_qnty")]);
			$po_month_arr[$month_buyer]['matCost']+=$poMatCost;
		}
	}
	//print_r($po_month_arr[1]['purchfin_amt']); //die;
	//var_dump($buyerMonth_list);
	asort($buyerMonth_list);
	$shortBookingNo=array(); $sampleOrderBookingNo=array(); $emblWoRateArr=array();
	$shortBookingSql=sql_select("select a.po_break_down_id, a.emblishment_name, a.rate, a.booking_type, a.is_short, a.booking_no from wo_booking_dtls a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and b.user_id = ".$user_id." and a.booking_type in (1,2,4,6) and a.status_active=1 and a.is_deleted=0");// and a.is_short in (1,2)
	foreach($shortBookingSql as $row)
	{
		/*if($row[csf("booking_type")]==1 || $row[csf("booking_type")]==2)
		{
			if($row[csf("is_short")]==1)
			{
				$shortBookingNo[$row[csf("booking_no")]]=$row[csf("booking_no")];
			}
		}
		else */if($row[csf("booking_type")]==4)
		{
			$sampleOrderBookingNo[$row[csf("booking_no")]]=$row[csf("booking_no")];
		}
		if($row[csf("booking_type")]==6)
		{
			if($row[csf("emblishment_name")]==1 || $row[csf("emblishment_name")]==2 || $row[csf("emblishment_name")]==3)
			{
				$emblWoRateArr[$row[csf("po_break_down_id")]][$row[csf("emblishment_name")]]=$row[csf("rate")];;
			}
		}
	}
	unset($shortBookingSql);
	//echo "select a.id, a.booking_no_id, a.booking_no from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and b.is_short=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$batchBookingsql=sql_select( "select a.id, a.booking_no_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b, gbl_temp_engine c where a.id=b.mst_id and b.po_id=c.ref_val and c.entry_form=880 and c.ref_from=1 and c.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$batchBookingNo=array(); $sampleBatchBookingNo=array();
	foreach($batchBookingsql as $row)
	{
		if($sampleOrderBookingNo[$row[csf("booking_no")]]!="")
		{
			$sampleBatchBookingNo[$row[csf("id")]]=$row[csf("booking_no")];
		}
		/*else if($shortBookingNo[$row[csf("booking_no")]]!="")
		{
			$batchBookingNo[$row[csf("id")]]=$row[csf("booking_no")];
		}*/
	}
	unset($batchBookingsql);
	//print_r($batchBookingNo);
	
	$dataSqlTrans ="select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, b.batch_id as BATCHID, c.po_breakdown_id as POID, c.entry_form as ENTRY_FORM, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=1 and e.user_id = ".$user_id."
	and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2";
	//echo $dataSqlTrans;
	$dataArrayTrans= sql_select($dataSqlTrans);
	$recDataRetArr=array(); $batchCheckArr=array(); $batchIDArr=array(); $fabProdPurchBatchArr=array();
	foreach($dataArrayTrans as $row)
	{
		$amt=$rate=0;
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		$rate=$row['RATE']/82;
		if($row['ENTRY_FORM']==37)
		{
			if($po_arr[$row['POID']]['apppo']==1)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				$amt=bcmul($row['QUANTITY'],$rate,10);
				//echo $row['QUANTITY'].'='.($row['RATE']/82).'='.$amt.'<br>';
				$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_qty']+=$row['QUANTITY'];
				//$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_amt']+=$amt;
				$exfinrec_arr[$month_buyer]['finrec']=bcadd($amt,$exfinrec_arr[$month_buyer]['finrec'],10);
				//$exfinrec_arr[$row['POID']]['finrec']+=$amt;
				//$recIdWiseRateArr[$row['ID']][$row['POID']]=$rate;
				$exsampleorderfinrec_arr[$row['POID']]['samfinrec']+=$sampleAmt;
				$recIdWiseRateArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
			}
		}
		else
		{
			$batchCheckArr[$row['PROD_ID']][$row['BATCHID']]=1;
		}
		array_push($batchIDArr,$row['BATCHID']);
		$fabProdPurchBatchArr[$row['POID']][$row['PROD_ID']][$row['COLOR_ID']][$row['BATCHID']]=1;
	}
	unset($dataArrayTrans);
	
	/*$finishRec="select a.id, a.entry_form, a.receive_basis, a.booking_no as booking_no_mst, a.currency_id, a.exchange_rate, b.prod_id, b.batch_id, b.booking_no as booking_no_dtls, b.rate, c.po_breakdown_id, c.quantity, c.color_id
		 from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=1 and d.user_id = ".$user_id." and a.receive_basis in (1,2,4) and a.entry_form in(7,37) and c.entry_form in(7,37) and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	$finishRec_res=sql_select($finishRec);
	
	$exfinrec_arr=array(); $exsampleorderfinrec_arr=array(); $recIdWiseRateArr=array(); $fabProdPurchBatchArr=array();
	foreach($finishRec_res as $row)
	{
		$amount=$sampleAmt=$rate=0;
		
		$rate=$row[csf("rate")]/82;
		$month_buyer=$po_arr[$row[csf("po_breakdown_id")]]['month_buyer'];
		
		if($row[csf("entry_form")]==37)
		{
			if($row[csf("receive_basis")]==2)
			{
				if($sampleBatchBookingNo[$row[csf("booking_no_mst")]]!="")
				{
					$sampleAmt=$row[csf("quantity")]*$rate;
				}
				else //if($shortBookingNo[$row[csf("booking_no_mst")]]!="")
				{
					$recIdWiseRateArr[$row[csf("id")]][$row[csf("po_breakdown_id")]]=$rate;
					$amount=bcmul($row[csf("quantity")],$rate,10);
				}
			}
			else //if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6 || $row[csf("receive_basis")]==9)
			{
				if($sampleBatchBookingNo[$row[csf("booking_no_dtls")]]!="")
				{
					$sampleAmt=bcmul($row[csf("quantity")],$rate,10);
				}
				else //if($shortBookingNo[$row[csf("booking_no_dtls")]]!="")
				{
					$recIdWiseRateArr[$row[csf("id")]][$row[csf("po_breakdown_id")]]=$rate;
					$amount=bcmul($row[csf("quantity")],$rate,10);
				}
			}
		}
		else
		{
			$batchCheckArr[$row[csf('prod_id')]][$row[csf('batch_id')]]=1;
		}
		//$finamt=fn_number_format($amount,0,".","");
		
		if($po_arr[$row[csf("po_breakdown_id")]]['apppo']==1)
		{
			$exfinrec_arr[$month_buyer]['finrec']=bcadd($amount,$exfinrec_arr[$month_buyer]['finrec'],10);
			$exsampleorderfinrec_arr[$month_buyer]['samfinrec']+=$sampleAmt;
		}
		$fabProdPurchBatchArr[$row[csf("po_breakdown_id")]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('batch_id')]]=1;
	}
	unset($finishRec_res);*/
	//print_r($exfinrec_arr); //die;
	
	$sqlRet="select a.received_id as RECEIVED_ID, b.prod_id as PROD_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, b.cons_uom as UOM from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (46) and c.entry_form in (46) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=5 and e.user_id = ".$user_id." ";
	$sqlRetArr=sql_select($sqlRet); 
	foreach($sqlRetArr as $row)
	{
		$recRate=$amt=0;
		$recRate=$recIdWiseRateArr[$row['RECEIVED_ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']*1;
		$finRecReturnAmt=bcmul($row['QUANTITY'],$recRate,10);
		//echo $recRate;
		if(($recRate)>0)
		{
			$month_buyer=$po_arr[$row['POID']]['month_buyer'];
			if($po_arr[$row['POID']]['apppo']==1)
			{
				//$exfinrec_arr[$row['POID']]['finrec']-=$amt;
				$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$finRecReturnAmt,10);
			}
		}
	}
	unset($sqlRetArr);
	
	/*$finRecRetSql="select a.received_id, b.booking_no, c.po_breakdown_id, c.quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=46 and c.entry_form=46 and c.trans_type=3 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=1 and d.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";//and c.po_breakdown_id=43036
	$finRecRetSqlData=sql_select($finRecRetSql);
	foreach($finRecRetSqlData as $row)
	{
		$recRate=$finRecReturnAmt=0;
		
		$recRate=$recIdWiseRateArr[$row[csf("received_id")]][$row[csf("po_breakdown_id")]];
		$finRecReturnAmt=bcmul($row[csf("quantity")],$recRate,10);
		//echo $finRecReturnAmt;
		$month_buyer=$po_arr[$row[csf("po_breakdown_id")]]['month_buyer'];
		if($po_arr[$row[csf("po_breakdown_id")]]['apppo']==1)
		{
			$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$finRecReturnAmt,10);
		}
	}
	unset($finRecRetSqlData);*/
	//print_r($exfinrec_arr);// die;
	
	$batchID_cond=where_con_using_array($batchIDArr,0,"id");
	$batchEntryArr=return_library_array( "select id, entry_form from pro_batch_create_mst where 1=1 ", "id", "entry_form");
	
	$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, b.batch_id as BATCHID, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=1 and e.user_id = ".$user_id."";
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['TRANSFER_VALUE']/82;
		//echo $recRate;
		$month_buyer=$po_arr[$row["POID"]]['month_buyer'];
		if($row['TRANS_TYPE']==5 && $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']]!=1 && $batchEntryArr[$row['BATCHID']]==37)//trans in// 
		{
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				//$exfinrec_arr[$row['TO_ORDER_ID']]['finrec']+=$transVal;
				$exfinrec_arr[$month_buyer]['finrec']=bcadd($transVal,$exfinrec_arr[$month_buyer]['finrec'],10);
			}
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			//$amt=$row['QUANTITY']*$transRate;
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1 && $fabProdPurchBatchArr[$row['POID']][$row['PROD_ID']][$row['COLOR_ID']][$row['BATCHID']]==1)
			{
				//$exfinrec_arr[$row['FROM_ORDER_ID']]['finrec']-=$transVal;
				$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$transVal,10);
				//echo "1 ".$transVal;
			}
		}
		//array_push($batchIDArr,$row['BATCHID']);
	}
	unset($sqlTransArr);
	
	$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, b.batch_id as BATCHID, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=1 and d.user_id = ".$user_id."";
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array(); // $exfinrec_arr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['TRANSFER_VALUE']/82;
		
		//echo $recRate;
		$month_buyer=$po_arr[$row["POID"]]['month_buyer'];
		if($row['TRANS_TYPE']==5)//trans in
		{
			//$amt=$row['QUANTITY']*$transRate;
			
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				$exfinrec_arr[$month_buyer]['finrec']=bcadd($transVal,$exfinrec_arr[$month_buyer]['finrec'],10);//bcdiv($row['TRANSFER_VALUE'],$transrate,10);
			}
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			//$amt=$row['QUANTITY']*$transRate;
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1)
			{
				$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$transVal,10);//bcdiv($row['TRANSFER_VALUE'],$transrate,10);
			}
		}
	}
	unset($sqlTransArr);
	//print_r($exfinrec_arr);
	
	$piBookingsql=sql_select( "select a.id, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.importer_id='$company_id' and a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$piBookingNo=array();
	foreach($piBookingsql as $row)
	{
		if($shortBookingNo[$row[csf("work_order_no")]]!="")
		{
			$piBookingNo[$row[csf("id")]]=$row[csf("work_order_no")];
		}
	}
	unset($piBookingsql);
	
	$trimsSql ="select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, c.po_breakdown_id as POID, c.quantity as QUANTITY
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (24) and c.entry_form in (24) and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=1 and d.user_id = ".$user_id."";
	$trimsSql_res=sql_select($trimsSql);
	$extrimsrec_arr=array();
	
	foreach($trimsSql_res as $row)
	{
		$amount=0;
		$amount=$row['QUANTITY']*$row['RATE'];
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		if($po_arr[$row['POID']]['apppo']==1)
		{
			$extrimsrec_arr[$month_buyer]['trimrec']+=$amount;
		}
	}
	unset($trimsSql_res);
	//print_r($extrimsrec_arr);
	
	$sqlRet="select a.received_id as RECEIVED_ID, b.rate as RATE, c.po_breakdown_id as POID, c.quantity as QUANTITY from inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (49) and c.entry_form in (49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=1 and d.user_id = ".$user_id." ";
	$sqlRetArr=sql_select($sqlRet); //$extrimsrec_arr=array();
	foreach($sqlRetArr as $row)
	{
		$amt=0;
		$amt=$row['QUANTITY']*($row['RATE']/82);
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		if($po_arr[$row['POID']]['apppo']==1)
		{
			$extrimsrec_arr[$month_buyer]['trimrec']-=$amt;
		}
	}
	unset($sqlRetArr);
	//print_r($extrimsrec_arr);
	
	$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, b.transfer_qnty as QUANTITY from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=4 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (78) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=1 and d.user_id = ".$user_id."";
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();  //$extrimsrec_arr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['RATE']/82;
		//echo $recRate;
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		if($row['TRANS_TYPE']==5)//trans in
		{
			$amt=$row['QUANTITY']*$transVal;
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				$extrimsrec_arr[$month_buyer]['trimrec']+=$amt;
			}
			
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			$amt=$row['QUANTITY']*$transVal;
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1)
			{
				$extrimsrec_arr[$month_buyer]['trimrec']-=$amt;
			}
		}
	}
	unset($sqlTransArr);
	//print_r($extrimsrec_arr); //die;
	
	$generalAccSql="select a.id, b.cons_rate, b.prod_id, b.order_id, b.cons_quantity
		 from inv_issue_master a, inv_transaction b, gbl_temp_engine c where b.order_id=c.ref_val and c.entry_form=880 and c.ref_from=1 and c.user_id = ".$user_id." and a.id=b.mst_id and a.entry_form in(21) and b.transaction_type=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$generalAccSql_res=sql_select($generalAccSql); $returnIssueIdArr=array();
	$exgeneralAcc_arr=array();
	foreach($generalAccSql_res as $row)
	{
		array_push($returnIssueIdArr,$row[csf("id")]);
		$amount=$rate=0;
		$rate=$row[csf("cons_rate")]/82;
		$amount=$row[csf("cons_quantity")]*$rate;
		$month_buyer=$po_arr[$row[csf("order_id")]]['month_buyer'];
		if($po_arr[$row[csf("order_id")]]['apppo']==1)
		{
			$exgeneralAcc_arr[$month_buyer]['generalacciss']+=$amount;
		}
		$gaccRetArr[$row[csf("id")]][$row[csf("prod_id")]]=$row[csf("order_id")];
	}
	unset($generalAccSql_res);
	$issueid_cond=where_con_using_array($returnIssueIdArr,0,"b.issue_id");
	//print_r($exgeneralAcc_arr); die;
	
	$generalAccRet="select b.cons_rate as RATE, b.prod_id as PROD_ID, b.cons_quantity as QUANTITY, b.issue_id as ISSUE_ID
		 from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in(27) and b.transaction_type=4 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issueid_cond";
		 
	$generalAccRetArr=sql_select($generalAccRet); //$exgeneralAcc_arr=array();
	foreach($generalAccRetArr as $row)
	{
		$po_id=$gaccRetArr[$row["ISSUE_ID"]][$row["PROD_ID"]];
		$month_buyer=$po_arr[$po_id]['month_buyer'];
		$amount=$rate=0;
		$rate=$row["RATE"]/82;
		$amount=$row["QUANTITY"]*$rate;
		if($po_arr[$po_id]['apppo']==1)
		{
			$exgeneralAcc_arr[$month_buyer]['generalacciss']-=$amount;
		}
	}
	unset($generalAccRetArr);
	//print_r($exgeneralAcc_arr);
	
	//$sqlYarn="select a.id, a.buyer_id, b.transaction_date, (b.cons_amount/b.cons_rate) as amt from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.company_id ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.transaction_date between '$startDate' and '$endDate' and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and a.entry_form=3 and a.booking_no like '%-SMN-%'"; 
	
	$sqlYarn="select a.id, d.buyer_name as buyer_id, b.transaction_date, (b.cons_amount/b.cons_rate) as amt 
	from inv_issue_master a, inv_transaction b, wo_non_ord_samp_booking_dtls c, sample_development_mst d
	
	where a.id=b.mst_id and a.booking_no=c.booking_no and c.style_id=d.id and a.company_id ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and a.entry_form=3 and a.issue_purpose in (8,31) and a.booking_no like '%-SMN-%' and b.transaction_date between '$startDate' and '$endDate' $buyerYarnCond $seasonYarn_cond $locationYarn_cond";
	//echo $sqlYarn; //die;
	$exYarnIssue_arr=array(); $exYarnIssueBuyer_arr=array();
	$sqlYarn_res=sql_select($sqlYarn);  $sampleBuyer=array(); $issueWiseBuyerArr=array();
	foreach($sqlYarn_res as $row)
	{
		$shipment_date=date("Y-m",strtotime($row[csf("transaction_date")]));
		$exYarnIssue_arr[$row[csf("buyer_id")]]['yarniss']+=$row[csf("amt")];
		$sampleBuyer[$row[csf("buyer_id")]]=$row[csf("buyer_id")];
		
		$issueWiseBuyerArr[$row[csf("id")]]=$row[csf("buyer_id")];
	}
	unset($sqlYarn_res);
	//print_r($exYarnIssue_arr); die;
	
	$sqlYarnIssueRet="select a.issue_id, (b.cons_amount/b.cons_rate) as amt from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.item_category=1  and b.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_no like '%-SMN-%'"; 
	//echo $sqlYarnIssueRet; die;
	$sqlYarnIssueRetData=sql_select($sqlYarnIssueRet); 
	foreach($sqlYarnIssueRetData as $row)
	{
		$monthBuyer="";
		$monthBuyer=$issueWiseBuyerArr[$row[csf("issue_id")]];
		
		$exYarnIssue_arr[$monthBuyer]['yarniss']-=$row[csf("amt")];
		$exYarnIssueBuyer_arr[$monthBuyer]['yarniss']-=$row[csf("amt")];
	}
	unset($sqlYarnIssueRetData);
	asort($buyerMonth_list);
	
		
	
	$sqlGmt="select a.po_break_down_id as POID, a.production_type as PRODTYPE, a.production_source as PRODSOURCE, a.embel_name as EMBLNAME, a.production_quantity as PRODQTY from pro_garments_production_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and b.user_id = ".$user_id." and a.production_type in (3,4) and a.status_active=1 and a.is_deleted=0";//and a.embel_name in (1,2,3)
	//echo $sqlGmt;die;
	$sqlGmtArr=sql_select($sqlGmt);
	$emblArr=array(); $gmtProdArr=array();
	foreach($sqlGmtArr as $row)
	{
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		if( $row['PRODTYPE']==3)
		{
			$printrate=0;
			
			if($row['EMBLNAME']==1) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['print_amt']/$budgetAmt_arr[$row["POID"]]['print_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==2)
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['emb']/$budgetAmt_arr[$row["POID"]]['embqty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==3)
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['wash']/$budgetAmt_arr[$row["POID"]]['washqty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==4) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row["POID"]]['special_works_amt']/$budgetAmt_arr[$row['POID']]['special_works_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==5) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row["POID"]]['gmts_dyeing_amt']/$budgetAmt_arr[$row['POID']]['gmts_dyeing_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==99) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row["POID"]]['others_amt']/$budgetAmt_arr[$row['POID']]['others_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			
			$emblamt=0;
			$emblamt=$row['PRODQTY']*($printrate/12);
			//echo $row['EMBLNAME'].'='.$row['PRODSOURCE'].'='.$printrate.'<br>';
			
			$emblArr[$month_buyer][$row['EMBLNAME']]+=fn_number_format($emblamt,8,".","");
		}
		else if( $row['PRODTYPE']==4)
		{
			$gmtProdArr[$month_buyer][$row['PRODTYPE']]+=$row['PRODQTY'];
		}
	}
	unset($sqlGmtArr);
	
	//print_r($gmtProdArr); die;

	// echo "<pre>";
	// print_r($emblArr);
	// echo "</pre>";
	
	$focClaim_arr=array();
	$sql_focClaim="select a.po_break_down_id, a.shiping_mode, a.foc_or_claim, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and b.user_id = ".$user_id." and a.shiping_mode=2 and a.foc_or_claim=2 and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id, a.shiping_mode, a.foc_or_claim";
	$sql_focClaim_res=sql_select($sql_focClaim);
	foreach($sql_focClaim_res as $row)
	{
		$focClaim_arr[$row[csf("po_break_down_id")]]=$row[csf("shiping_mode")].'_'.$row[csf("foc_or_claim")].'_'.$row[csf("ex_factory_qnty")];
	}
	unset($sql_focClaim_res);
	
	$sql_ship="select a.po_break_down_id, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and b.user_id = ".$user_id." and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id"; //and a.ex_factory_date between '$startDate' and '$endDate'
	$sql_ship_res=sql_select($sql_ship); $exfactory_year_arr=array(); $exfactory_buyer_arr=array(); $deliveryPoArr=array(); $shipvalArr=array();
	foreach($sql_ship_res as $row)
	{
		$deliveryPoArr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
		if($po_arr[$row[csf("po_break_down_id")]]['po_id']==1)
		{
			//Month Summary
			$fiscalMonth=""; $exQtyPcs=0; $exValue=0;
			$exQtyPcs=$row[csf("ex_factory_qnty")];
			$month_buyer=$po_arr[$row[csf("po_break_down_id")]]['month_buyer'];
			$exfactory_year_arr[$month_buyer]['pcs']+=$exQtyPcs;
			$shipvalArr[$row[csf("po_break_down_id")]]['val']+=$exQtyPcs*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
			
			$focClaim=explode('_',$focClaim_arr[$row[csf("po_break_down_id")]]);
			if($focClaim[0]==2  && $focClaim[1]==2)
			{
				$exfactory_year_arr[$month_buyer]['air']+=$focClaim[2];
			}
			
			if($row[csf("shiping_mode")]==2  && $row[csf("foc_or_claim")]==2)
			{
				$exfactory_year_arr[$month_buyer]['air']+=$exQtyPcs;
			}
			
			if($po_arr[$row[csf("po_break_down_id")]]['ship_sta']==2)
			{
				$exfactory_year_arr[$month_buyer]['partial']+=$exQtyPcs*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
			}
			$shortExcessShip=0;
			if($po_arr[$row[csf("po_break_down_id")]]['ship_sta']==3 && $po_arr[$row[csf("po_break_down_id")]]['apppo']==1)
			{
				$exfactory_year_arr[$month_buyer]['fullship']+=$exQtyPcs*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
				$shortExcessShip=($exQtyPcs-$po_arr[$row[csf("po_break_down_id")]]['fullship_qty'])*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
				
				if($shortExcessShip>0) $exfactory_year_arr[$month_buyer]['excessship']+=$shortExcessShip;
				if($shortExcessShip<0) $exfactory_year_arr[$month_buyer]['shortship']+=$shortExcessShip;
			}
		}
	}
	//print_r($fullshipedpoArr);
	
	foreach($fullshipedpoArr as $po_id=>$refCloseQty)
	{
		//echo $po_id.'-'.$deliveryPoArr[$po_id];
		if($deliveryPoArr[$po_id]=='')
		{
			if($po_arr[$po_id]['apppo']==1)
			{
				$refclyear=$po_arr[$po_id]['month_buyer'];
				$exfactory_year_arr[$refclyear]['shortship']-=$refCloseQty;
			}
		}
	}
	
	$sqlClaim="select a.po_id, a.base_on_ex_val as claim, a.air_freight, a.sea_freight, a.discount from wo_buyer_claim_mst a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0";
	//echo $sqlClaim; die;
	$sqlClaim_res=sql_select($sqlClaim); $claim_year_arr=array();
	foreach($sqlClaim_res as $crow)
	{
		$month_buyer=$po_arr[$crow[csf("po_id")]]['month_buyer'];
		if($po_arr[$crow[csf("po_id")]]['apppo']==1)
		{
			$claim_year_arr[$month_buyer]['claim']+=$crow[csf("claim")];
			$claim_year_arr[$month_buyer]['air']+=$crow[csf("air_freight")];
			$claim_year_arr[$month_buyer]['sea']+=$crow[csf("sea_freight")];
			$claim_year_arr[$month_buyer]['discount']+=$crow[csf("discount")];
			$claimPoId[$month_buyer][$crow[csf("po_id")]]=$crow[csf("po_id")];
		}
	}
	unset($sqlClaim_res);
	
	$sql_proRealization="select b.invoice_id from com_export_proceed_realization a, com_export_doc_submission_invo b where a.invoice_bill_id=b.doc_submission_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_proRealization_res=sql_select($sql_proRealization); $invoice_proRealArr=array();
	foreach($sql_proRealization_res as $prrow)
	{
		$invoice_proRealArr[$prrow[csf("invoice_id")]]=1;
	}
	unset($sql_proRealization_res);
	
	$sqlInvoice="select po_breakdown_id, sum(current_invoice_value) as inv_val from com_export_invoice_ship_dtls where status_active=1 and is_deleted=0 and current_invoice_value>0 group by po_breakdown_id";
	$sqlInvoice_res=sql_select($sqlInvoice); $invoice_year_arr=array(); $poInvArr=array();
	foreach($sqlInvoice_res as $irow)
	{
		if($po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==2 || $po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==3)
		{
			$poInvArr[$irow[csf("po_breakdown_id")]]=$irow[csf("po_breakdown_id")];
			$clyear=$po_arr[$irow[csf("po_breakdown_id")]]['month_buyer'];
			$povalue=$invoiceBal=0;
			$povalue=$shipvalArr[$irow[csf("po_breakdown_id")]]['val'];//$po_arr[$irow[csf("po_breakdown_id")]]['poVal'];
			$invoiceBal=$povalue-$irow[csf("inv_val")];
			$invoice_year_arr[$clyear]+=$invoiceBal;
		}
	}
	unset($sqlInvoice_res);
	//$po_arr[$row[csf("id")]]['ship_sta']
	
	foreach($po_arr as $poid=>$st)
	{
		if($st['ship_sta']==2 || $st['ship_sta']==3)
		{
			if($poInvArr[$poid]=="")
			{
				$clyear=$po_arr[$poid]['month_buyer'];
				$povalue=$shipvalArr[$poid]['val'];
				$invoice_year_arr[$clyear]+=$povalue;
			}
		}
	}
	
	$sqlInvoiceRec="select a.mst_id, a.po_breakdown_id, sum(a.current_invoice_value) as inv_val from com_export_invoice_ship_dtls a, gbl_temp_engine b where  a.po_breakdown_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and a.current_invoice_value>0 group by a.mst_id, a.po_breakdown_id";
	$sqlInvoiceRec_res=sql_select($sqlInvoiceRec); $invoiceRec_yearArr=array();
	foreach($sqlInvoiceRec_res as $irow)
	{
		if($po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==2 || $po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==3)
		{
			$clyear=$po_arr[$irow[csf("po_breakdown_id")]]['month_buyer'];
			
			if($invoice_proRealArr[$irow[csf("mst_id")]]==1)
				$invoiceRec_yearArr[$clyear]+=0;
			else
				$invoiceRec_yearArr[$clyear]+=$irow[csf("inv_val")];
		}
	}
	unset($sqlInvoiceRec_res);
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=880");
	oci_commit($con);
	disconnect($con);
	//var_dump($exfactory_year_arr);
	ob_start();
	?>
    <div style="width:1400px; margin:0px 5px 5px 15px;">
        <div class="buyerdetails" style="width:1400px; font-size:14px; font-weight:bold; color:Black; background-color:#008080" align="center"><? echo "Buyer Summary: Fiscal Year -". $exfirstYear[0].' & '.$lastYear; ?></div>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1400px; margin-top:5px" rules="all">
                <tr align="center" style="font-weight:bold">
                    <td width="70">Buyer</td>
                    <td width="50">No of Style</td>
                    <td width="70">No of PO</td>
                    <td width="70">Cap. Booking Min</td>
                    
                    <td width="70">Booked Pcs</td>
                    <td width="70">Booked Value[$]</td>
                    
                    <td width="70">Budget Appd [$]</td>
                    <td width="50">Budget Appd %</td>
                    
                    <td width="70">Margin [$]</td>
                    <td width="50">Margin %</td>
                    <td width="50">Mat. Cons %</td>
                    <td width="70">Ex Mat Cost[$]</td>
                    <td width="70">Excess Ship [$]</td>
                    <td width="70">Short Ship [$]</td>
                    <td width="70">Penalty[$]</td>
                    <td width="70">Actual Margin [$]</td>
                    <td width="50">Actual margin %</td>
                    <td width="70">Ship Pending [$]</td>
                    <td width="70">Input to Ship %</td>
                    <td width="70">Invoice Pending [$]</td>
                    <td>Receivable[$]</td>
                </tr>
                <?  $tmpbuyerArr=array();
				foreach($buyerMonth_list as $val)
				{
					$exval=explode("_",$val);
					$monthName=""; $buyer_id='';
					$monthName=$buyerArr[$exval[0]]; $buyerId=date("M-y",strtotime($exval[1]));
					$buyer_id=$exval[0];
					
					//echo $yearF.'<br>';
					$actualpono=$bbooked_min=$bbooked_pcs=$bbooked_val=$bfobjob=$bjob=$bmargin=$bfob=$bfullshiped=$bexfactory_pcs=$bexfactory_air=$bmatCostPer=0;
						
					$bbooked_min=$po_month_arr[$val]['min'];
					$bbooked_pcs=$po_month_arr[$val]['pcs'];
					$bbooked_val=$po_month_arr[$val]['val'];
					$actualpono=$po_month_arr[$val]['actualpo'];
					$bfobjob=count($job_arr[$val]['fobjob']);
					$bjob=count($job_arr[$val]['job']);
					
					$tmpbuyerArr[$val]=$buyer_id;

					$bmargin=$po_month_arr[$val]['margin'];
					$bfob=$po_month_arr[$val]['fob'];
					$bfullshiped=$po_month_arr[$val]['fullshiped'];
					$bexfactory_pcs=$exfactory_year_arr[$val]['pcs'];
					$bexfactory_air=$exfactory_year_arr[$val]['air'];
					
					$bmatCostPer=($po_month_arr[$val]['matCost']/$bfob)*100;
					
					$excessFinishFabCost=$Ex_Mat_Cost_From_Function[$val];

					//echo "<pre>".$excessFinishFabCost."=".$actualFinishFabcost."+(".$yarnStolen."-".$po_month_arr[$val]['mfgMatCost'].")</pre>";
					//echo $exfinrec_arr[$val]['finrec'].'-'.$po_month_arr[$val]['purchfin_amt'];
					$finPurAmt=bcsub($exfinrec_arr[$val]['finrec'],$po_month_arr[$val]['purchfin_amt']);
					$finPurAmt=($finPurAmt*1);
					$trimRecAmt=$extrimsrec_arr[$val]['trimrec']-$po_month_arr[$val]['trim'];
					//echo $exgeneralAcc_arr[$val]['generalacciss'].'='.$trimRecAmt.'='.$po_month_arr[$val]['trim'];
					
					$washAmt=$emblArr[$val][3]-$po_month_arr[$val]['washamt'];
					//echo "<pre>".$washAmt."=".$emblArr[$val][3]."-".$po_month_arr[$val]['washamt']."</pre>";
					$embleAmt=$emblArr[$val][2]-$po_month_arr[$val]['embamt'];
					// adding process by helal
					$print_amt=$emblArr[$val][1]-$po_month_arr[$val]['print_amt'];
					$special_works_amt=$emblArr[$val][4]-$po_month_arr[$val]['special_works_amt'];
					$gmts_dyeing_amt=$emblArr[$val][5]-$po_month_arr[$val]['gmts_dyeing_amt'];
					$others_amt=$emblArr[$val][99]-$po_month_arr[$val]['others_amt'];
					// adding process by helal

					
					$bookPer=$approved_per=$marginPer=$fob_pcs=$excessShip=$shortShip=$inputtoShipper=0;
					
					$approved_per=($bfob/$bbooked_val)*100;
					$marginPer=($bmargin/$bfob)*100;
					
					$fob_pcs=$bbooked_val/$bbooked_pcs;
					$excessShip=$exfactory_year_arr[$val]['excessship'];//($bexfactory_pcs*$fob_pcs)-$bbooked_val;
					//if($excessShip<0) $excessShip=0;
					$shortShip=str_replace("-","",$exfactory_year_arr[$val]['shortship']);//$bbooked_val-(($bexfactory_pcs*$fob_pcs)+$bfullshiped);
					//if($shortShip<0) $shortShip=0;

					$excessCost=($finPurAmt*1)+$trimRecAmt+$exgeneralAcc_arr[$val]['generalacciss']+$exYarnIssue_arr[$val]['yarniss']+$exsampleorderfinrec_arr[$val]['samfinrec']+$excessFinishFabCost+$embleAmt+$washAmt+$print_amt+$special_works_amt+$gmts_dyeing_amt+$others_amt; // adding process by helal
					
					$shipPending=$po_month_arr[$val]['pending']+($po_month_arr[$val]['partial']-$exfactory_year_arr[$val]['partial']);
					$inputtoShipper=($bexfactory_pcs/$gmtProdArr[$val][4])*100;
					$pendingCiVal=$invProdRealVal=0;
					$pendingCiVal=$invoice_year_arr[$val];
					$invProdRealVal=$invoiceRec_yearArr[$val];
					
					$panalty=0;
					$panalty=$claim_year_arr[$val]['claim']+$claim_year_arr[$val]['air']+$claim_year_arr[$val]['sea']+$claim_year_arr[$val]['discount'];
					
					$actualMargin=(($po_month_arr[$val]['margin']+$excessShip)-($excessCost+$shortShip+$panalty));
					$actualMarginPer=($actualMargin/$bfob)*100;
					?>
					<tr class="buyerdetails">
                        <td style="word-break:break-all"><a href='#report_details' onclick="generate_report('<?=$company_id; ?>','<?=$location_id; ?>','<?=$shipStatus; ?>','<?=$orderStatus; ?>','<?=$cbo_status; ?>','<?=$season_id; ?>','<?=$client_id; ?>','<?=$style_ref; ?>','<?=$from_year; ?>','<?=$to_year; ?>','<?=$buyer_id; ?>','4');"><?=$buyerArr[$buyer_id]; ?></a></td>
						<td align="right"><?=$bjob; ?></td>
                        <td align="right"><?=$actualpono; ?></td>
						<td align="right"><? echo fn_number_format($bbooked_min); ?></td>
                        
						<td align="right"><? echo fn_number_format($bbooked_pcs); ?></td>
						<td align="right"><? echo fn_number_format($bbooked_val); ?></td>
						
						<td align="right"><? echo fn_number_format($bfob); ?></td>
						<td align="right"><? echo fn_number_format($approved_per,2,'','',0); ?></td>
                        
						<td align="right"><? echo fn_number_format($bmargin); ?></td>
						<td align="right"><? echo fn_number_format($marginPer,2,'','',0); ?></td>
                        <td align="right">
                        <?
						if($style_ref=="") $style_ref=0;
                        $data_buyer=$company_id.'__'.$location_id.'__'.$shipStatus.'__'.$orderStatus.'__'.$cbo_status.'__'.$season_id.'__'.$client_id.'__'.$style_ref.'__'.$startDate.'__'.$endDate.'__'.$buyer_id;
						?>
                        <a href="#report_details" onclick="fncexcesscost('matConsPer_popup','<?=$data_buyer; ?>','850px');"><? echo fn_number_format($bmatCostPer,2,'','',0); ?></a><? //echo fn_number_format($bmatCostPer,2,'','',0); ?></td>
                        <td align="right"><a href="#report_details" onclick="fncexcesscost('excesscost_popup','<?=$finPurAmt.'__'.$trimRecAmt.'__'.$exgeneralAcc_arr[$val]['generalacciss'].'__'.$exYarnIssue_arr[$val]['yarniss'].'__'.$exsampleorderfinrec_arr[$val]['samfinrec'].'__0__'.$excessFinishFabCost.'__'.$embleAmt.'__'.$washAmt.'__'.$print_amt.'__'.$special_works_amt.'__'.$gmts_dyeing_amt.'__'.$others_amt; ?>','1050px');"><? echo fn_number_format($excessCost); ?></a></td>
                        
                        
						<td align="right"><? echo fn_number_format($excessShip); ?></td>
						<td align="right"><? echo fn_number_format($shortShip); ?></td>
						<td align="right"><a href="#report_details" onclick="fncpanalty('panalty_popup','<?=$claim_year_arr[$val]['claim'].'__'.$claim_year_arr[$val]['air'].'__'.$claim_year_arr[$val]['sea'].'__'.$claim_year_arr[$val]['discount'].'__'.implode(",", $claimPoId[$buyer_id]).'_'.$buyer_id; ?>','550px');"><? echo fn_number_format($panalty); ?></a></td>
                        <td align="right"><? echo fn_number_format($actualMargin); ?></td>
                        <td align="right"><? echo fn_number_format($actualMarginPer,2,'','',0); ?></td>
						<td align="right"><? echo fn_number_format($shipPending); ?></td>
                        <td align="right" >
                        	<a href="#report_details" onclick="inputToShip('input_to_ship','<?=$buyer_id; ?>','<?=$season_id; ?>','');">
                        		<? echo fn_number_format($inputtoShipper); ?>
                        	</a>
                        </td>
                        <td align="right"><? echo fn_number_format($pendingCiVal); ?></td>
                        <td align="right">
                        	
                        	<a href="#report_details" onclick="inputToShip('receiveble_break_down','<?=$buyer_id; ?>','<?=$season_id; ?>','');">
                        		<? echo fn_number_format($invProdRealVal); ?>
                        	</a>	
                        </td>
					</tr>
					<?
					$totalstyle+=$bjob;
					$totalpo+=$actualpono;
					$buyerBooked_min+=$bbooked_min;
					$buyerBooked_pcs+=$bbooked_pcs;
					$buyerBooked_val+=$bbooked_val;
					$buyerfob+=$bfob;
					$buyerMargin+=$bmargin;
					$buyerExcessCost+=($excessCost*1);
					$buyerExcessShip+=$excessShip;
					$buyerShortShip+=$shortShip;
					$buyerPanalty+=$panalty;
					$buyerActualMargin+=$actualMargin;
					$buyerShipPending+=$shipPending;
					$buyerPendingCiVal+=$pendingCiVal;
					$buyerInvRealVal+=$invProdRealVal;
				}
				
				$excbuyer=array_diff($sampleBuyer, $tmpbuyerArr);
				//print_r($excbuyer);
				if(count($excbuyer)>0)
				{
					$excessCost=0;
					foreach($excbuyer as $buyerId)
					{
						//echo $buyerId;
						$excessCost+=$exfinrec_arr[$buyerId]['finrec']+$extrimsrec_arr[$buyerId]['trimrec']+$exgeneralAcc_arr[$buyerId]['generalacciss']+$exYarnIssue_arr[$buyerId]['yarniss']+$exsampleorderfinrec_arr[$buyerId]['samfinrec'];
					}
					?>
					<tr class="buyerdetails">
						<td bgcolor="#7FFF55">Others :<? //=$buyerArr[$buyerId]; ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right" bgcolor="#7FFF55" title=""><?=fn_number_format($excessCost); ?></td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
					</tr>
					<?
					$buyerExcessCost+=$excessCost;
				}
				?>
                <tr align="center" style="font-weight:bold; background-color:#CCC">
                	<td><span id="buyerdetailstotal" class="adl-signs" onClick="yearT(this.id,'.buyerdetails')" style="background-color:#2ABF00">+</span>&nbsp;&nbsp;Buyer Details Total:</td>
                    <td align="right"><? echo fn_number_format($totalstyle); ?></td>
                    <td align="right"><? echo fn_number_format($totalpo); ?></td>
                    <td align="right"><? echo fn_number_format($buyerBooked_min); ?></td>
                    
                    <td align="right"><? echo fn_number_format($buyerBooked_pcs); ?></td>
                    <td align="right"><? echo fn_number_format($buyerBooked_val); ?></td>
                    
                    <td align="right"><? echo fn_number_format($buyerfob); ?></td>
                    <td>&nbsp;</td>
                   
                    <td align="right"><? echo fn_number_format($buyerMargin); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($buyerExcessCost); ?></td>
                    <td align="right"><? echo fn_number_format($buyerExcessShip); ?></td>
                    <td align="right"><? echo fn_number_format($buyerShortShip); ?></td>
                    <td align="right"><? echo fn_number_format($buyerPanalty); ?></td>
                    <td align="right"><? echo fn_number_format($buyerActualMargin); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><? echo fn_number_format($buyerShipPending); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><? echo fn_number_format($buyerPendingCiVal); ?></td>
                    <td align="right"><?=fn_number_format($buyerInvRealVal); ?></td>
                </tr>
          </table>
      </div>
    <?
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}

if($action=="season_details_list_view")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	$exData=explode("***",$data);
	
	$company_id=$exData[0];
	$location_id=$exData[1];
	$shipStatus=$exData[2];
	
	$orderStatus=$exData[3];
	$cbo_status=$exData[4];
	$buyer_id=$exData[5];
	$season_id=$exData[6];
	$client_id=$exData[7];
	$style_ref=$exData[8];
	
	$from_year=$exData[9];
	$to_year=$exData[10];
	
	$seasonArr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	
	$buyerCond = ""; $calAlloBuyerCond=""; $buyerYarnCond="";
	if ($buyer_id == 0) 
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) 
		{
			if ($_SESSION['logic_erp']["buyer_id"] != "")
			{
				$buyerCond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$calAlloBuyerCond = " and b.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyerYarnCond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			}
			else
			{
				$buyerCond = "";
				$calAlloBuyerCond=""; 
				$buyerYarnCond="";
			}
		}
		else 
		{
			$buyerCond = "";
			$calAlloBuyerCond=""; 
			$buyerYarnCond="";
		}
	} 
	else 
	{
		$buyerCond = " and a.buyer_name=$buyer_id";
		$calAlloBuyerCond="and b.buyer_id='$buyer_id'";
		$buyerYarnCond=" and a.buyer_id='$buyer_id'";
	}
	
	$exfirstYear=explode('-',$from_year);
	$exlastYear=explode('-',$to_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exlastYear[1];
	$yearMonth_arr=array(); $yearStartEnd_arr=array(); $j=12; $i=1;
	$startDate=''; $endDate="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$yearMonth_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	
	$month_cond=""; $calDateCond=""; $monthYarn_cond="";

	$month_cond="and b.shipment_date between '$startDate' and '$endDate'";
	
	$calDateCond="and b.date_calc between '$startDate' and '$endDate'";
	$monthYarn_cond="and b.transaction_date between '$startDate' and '$endDate'";
	
	if($season_id==0) $seasonYarn_cond=""; else $seasonYarn_cond=" and d.season in ( $season_id )";
	if($location_id==0) $locationYarn_cond=""; else $locationYarn_cond=" and d.location_id in ( $location_id )";
	//var_dump($fiscalMonth_arr);
	if($location_id!=0) $capLocationCond="and a.location_id='$location_id'"; else $capLocationCond="";
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($shipStatus==1) $shipStatusCond="and b.shiping_status in (1,2)"; else if($shipStatus==2) $shipStatusCond="and b.shiping_status in (3)"; else $shipStatusCond="";
	if($orderStatus==0) $orderStatusCond=""; else $orderStatusCond=" and b.is_confirmed in ( $orderStatus )";
	if($season_id==0) $seasonCond=""; else $seasonCond=" and a.season_buyer_wise in ( $season_id )";
	if($client_id==0) $clientCond=""; else $clientCond=" and a.client_id in ( $client_id )";
	if(trim($style_ref)==0) $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='$style_ref'";
	
	$sql_po="select a.job_no as JOB_NO, a.id as JOBID, a.buyer_name as BUYER_NAME, a.season_buyer_wise as SEASON_ID, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as POID, b.shipment_date as SHIPMENT_DATE, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shiping_status as SHIPING_STATUS, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $month_cond $buyerCond $jobLocationCond $shipStatusCond $orderStatusCond $seasonCond $clientCond $styleRefCond";//(a.set_smv/a.total_set_qnty)
	//echo $sql_po;
	$sql_po_res=sql_select($sql_po); $poidstr=$jobId=""; $poididarr=array(); $jobididarr=array(); $powiseSeasonArr=array();
	$Ex_Mat_Cost_From_Function=array();
	foreach($sql_po_res as $row)
	{
		$poidstr.=$row['POID'].',';
		$poididarr[$row['POID']]=$row['POID'];
		$jobididarr[$row['JOBID']]=$row['JOBID'];
		if($jobId=="") $jobId="'".$row["JOBID"]."'"; else $jobId.=",'".$row["JOBID"]."'";
		$powiseSeasonArr[$row['POID']]=$row['SEASON_ID'];
		//$exMatCostP=poWiseExmfgCost($row['POID'],4);
		//$Ex_Mat_Cost_From_Function[$row['SEASON_ID']]+=fn_number_format($exMatCostP,8,".","");
		//echo "<pre>".$row['POID']."=>".$exMatCostP."</pre>";
	}
	$po_ids=array();
	$po_ids=array_filter(array_unique(explode(",",$poidstr)));
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3,4) and ENTRY_FORM=880");
	oci_commit($con);
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 3, $poididarr, $empty_arr);//PO ID
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 4, $jobididarr, $empty_arr);//Job ID
	//echo "SELECT * FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3,4) and ENTRY_FORM=880";
	//die;
	
	$exMatCostP=poWiseExmfgCost(implode(",",$poididarr),4);
	//echo "<pre>";
	//print_r($exMatCostP);
	//asort($exMatCostP);
	foreach($exMatCostP as $rpoid=>$pdata)
	{
		$Ex_Mat_Cost_From_Function[$powiseSeasonArr[$rpoid]]=bcadd($Ex_Mat_Cost_From_Function[$powiseSeasonArr[$rpoid]],$pdata,6);
		//if($powiseSeasonArr[$rpoid]==390) echo $rpoid."\t".$pdata.'<br>';
		
		//if($rpoid==18469) echo $rpoid."\t".$pdata.'<br>';
		
	}
	//echo "<pre>";
	//print_r($Ex_Mat_Cost_From_Function[391]); die;
	
	$actualPoSql=sql_select("select a.job_no as JOBNO, a.po_break_down_id as POID, a.acc_po_no as ACTUALPO from wo_po_acc_po_info a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=880 and b.ref_from=3 and a.status_active=1 and a.is_deleted=0");
	//echo "select job_no as JOBNO, po_break_down_id as POID, acc_po_no as ACTUALPO from wo_po_acc_po_info where status_active=1 and is_deleted=0 $accpoid_cond";
	$actualPoArr=array();
	foreach($actualPoSql as $actrow)
	{
		$actualPoArr[$actrow["POID"]][$actrow["ACTUALPO"]]=$actrow["ACTUALPO"];
	}
	unset($actualPoSql);
	
	$budgetAmt_arr=array();
	$sqlBomAmt="select a.job_id as JOB_ID, a.po_id as PO_ID, a.greypurch_amt as GREYPURCH_AMT, a.finpurch_amt as FINPURCH_AMT, a.yarn_amt as YARN_AMT, a.conv_amt as CONV_AMT, a.trim_amt as TRIM_AMT, a.emb_qty as EMB_QTY, a.emb_amt as EMB_AMT, a.wash_qty as WASH_QTY, a.wash_amt as WASH_AMT,a.print_qty as PRINT_QTY,a.print_amt AS PRINT_AMT,a.special_works_qty as SPECIAL_WORKS_QTY,a.special_works_amt as SPECIAL_WORKS_AMT,a.gmts_dyeing_qty as GMTS_DYEING_QTY,a.gmts_dyeing_amt as GMTS_DYEING_AMT,a.others_qty as OTHERS_QTY,a.others_amt AS OTHERS_AMT from bom_process a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 ";
	
	$sqlBomAmtRes=sql_select($sqlBomAmt);
	foreach($sqlBomAmtRes as $row)
	{
		$budgetAmt_arr[$row["PO_ID"]]['fab']=$row["GREYPURCH_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['purchfin_amt']=$row["FINPURCH_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['yarn']=$row["YARN_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['conv']=$row["CONV_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['trim']=$row["TRIM_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['embqty']=$row["EMB_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['emb']=$row["EMB_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['washqty']=$row["WASH_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['wash']=$row["WASH_AMT"];

		// adding process by helal
		$budgetAmt_arr[$row["PO_ID"]]['print_qty']=$row["PRINT_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['print_amt']=$row["PRINT_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_qty']=$row["SPECIAL_WORKS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_amt']=$row["SPECIAL_WORKS_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_qty']=$row["GMTS_DYEING_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_amt']=$row["GMTS_DYEING_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['others_qty']=$row["OTHERS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['others_amt']=$row["OTHERS_AMT"];
		// adding process by helal
	}
	// echo "<pre>";
	// print_r($budgetAmt_arr);
	// echo "</pre>";
	unset($sqlBomAmtRes);
	
	$sql_budget="select a.job_no as JOB_NO, a.approved as APPROVED, a.costing_per as COSTING_PER, a.exchange_rate as EXCHANGE_RATE, b.margin_pcs_bom as MARGIN_PCS_BOM from wo_pre_cost_mst a, wo_pre_cost_dtls b, gbl_temp_engine c where a.job_id=b.job_id and a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=4 and c.user_id = ".$user_id." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	
	$sql_budget_res=sql_select($sql_budget); $budget_arr=array();
	foreach($sql_budget_res as $row)
	{
		$budget_arr[$row["JOB_NO"]]['app']=$row["APPROVED"];
		$budget_arr[$row["JOB_NO"]]['margin_pcs']=$row["MARGIN_PCS_BOM"];
		$budget_arr[$row["JOB_NO"]]['costing_per']=$row["COSTING_PER"];
		$budget_arr[$row["JOB_NO"]]['exchange_rate']=$row["EXCHANGE_RATE"];
	}
	unset($sql_budget_res);
	
	$po_month_arr=array(); $job_arr=array(); $po_arr=array(); $poExchangeRatearr=array(); $fullshipedpoArr=array();
	foreach($sql_po_res as $row)
	{
		$costing_per=0; $costingPer=0; $matCost=0; $poMatCost=0; $actualPono=0;
		$costing_per=$budget_arr[$row["JOB_NO"]]['costing_per'];
		$poExchangeRatearr[$row['POID']]=$budget_arr[$row["JOB_NO"]]['exchange_rate'];
		$actualPono=count($actualPoArr[$row["POID"]]);
		
		if($costing_per==1) $costingPer=12;
		if($costing_per==2) $costingPer=1;
		if($costing_per==3) $costingPer=24;
		if($costing_per==4) $costingPer=36;
		if($costing_per==5) $costingPer=48;
		$month_buyer="";
		$month_buyer=$row["SEASON_ID"];

		$matCost=$budgetAmt_arr[$row['POID']]['fab']+$budgetAmt_arr[$row['POID']]['yarn']+$budgetAmt_arr[$row['POID']]['conv']+$budgetAmt_arr[$row['POID']]['trim']+$budgetAmt_arr[$row['POID']]['emb']+$budgetAmt_arr[$row['POID']]['wash']+$budgetAmt_arr[$row['POID']]['print_amt']+$budgetAmt_arr[$row['POID']]['special_works_amt']+$budgetAmt_arr[$row['POID']]['gmts_dyeing_amt']+$budgetAmt_arr[$row['POID']]['others_amt'];// adding process by helal
		//Month Buyer Details
		$mfgMaterialCost=$budgetAmt_arr[$row['POID']]['yarn']+$budgetAmt_arr[$row['POID']]['conv'];
		$po_month_arr[$month_buyer]['mfgMatCost']+=$mfgMaterialCost;
		//
		$shipment_date=date("Y-m",strtotime($row["SHIPMENT_DATE"]));
		//$month_buyer=$row["BUYER_NAME"].'_'.$shipment_date;
		
		$buyerMonth_list[$month_buyer]=$month_buyer;
		
		$poQtyPcs=0; $poValue=0; $booked_min=0;
		$poQtyPcs=$row["PO_QUANTITY"];
		$poValue=$row["PO_TOTAL_PRICE"];
		$booked_min=$row["SET_SMV"];
		
		$po_month_arr[$month_buyer]['min']+=$booked_min;
		$po_month_arr[$month_buyer]['pcs']+=$poQtyPcs;
		$po_month_arr[$month_buyer]['val']+=$poValue;
		$po_month_arr[$month_buyer]['actualpo']+=$actualPono;
		//$po_month_arr[$month_buyer]['purchece_amt']+=$budgetAmt_arr[$row['POID']]['purchfin_amt'];
		
		$po_month_arr[$month_buyer]['purchece_amt']=bcadd($po_month_arr[$month_buyer]['purchece_amt'],$budgetAmt_arr[$row['POID']]['purchfin_amt'],6);
		
		$po_month_arr[$month_buyer]['trim']+=$budgetAmt_arr[$row['POID']]['trim'];
		$po_month_arr[$month_buyer]['embamt']+=$budgetAmt_arr[$row['POID']]['emb'];
		$po_month_arr[$month_buyer]['washamt']+=$budgetAmt_arr[$row['POID']]['wash'];

		// adding process by helal
		$po_month_arr[$month_buyer]['print_amt']+=$budgetAmt_arr[$row['POID']]['print_amt'];
		$po_month_arr[$month_buyer]['special_works_amt']+=$budgetAmt_arr[$row['POID']]['special_works_amt'];
		$po_month_arr[$month_buyer]['gmts_dyeing_amt']+=$budgetAmt_arr[$row['POID']]['gmts_dyeing_amt'];
		$po_month_arr[$month_buyer]['others_amt']+=$budgetAmt_arr[$row['POID']]['others_amt'];
		// adding process by helal

		if($row["SHIPING_STATUS"]==3)
		{
			$po_month_arr[$month_buyer]['fullshiped']+=$poValue;
			$po_arr[$row['POID']]['fullship_qty']+=$poQtyPcs;
			$fullshipedpoArr[$row['POID']]=$poValue;
		}
		else if($row["SHIPING_STATUS"]==2)
		{
			$po_month_arr[$month_buyer]['partial']+=$poValue;
		}
		else 
		{
			$po_month_arr[$month_buyer]['pending']+=$poValue;
		}
		
		$job_arr[$month_buyer]['job'][$row["JOB_NO"]]=$row["JOB_NO"];
		$po_arr[$row['POID']]['po_id']=1;
		$po_arr[$row['POID']]['month_buyer']=$month_buyer;
		$po_arr[$row['POID']]['ship_sta']=$row[csf("SHIPING_STATUS")];
		$po_arr[$row['POID']]['po_price']=$row[csf("UNIT_PRICE")];
		$po_arr[$row['POID']]['poVal']+=$poValue;
		
		if($budget_arr[$row["JOB_NO"]]['app']==1)
		{
			$po_arr[$row['POID']]['apppo']=1;
			$job_arr[$month_buyer]['fobjob'][$row["JOB_NO"]]=$row["JOB_NO"];
			$po_month_arr[$month_buyer]['fob']+=$poValue;
			$margin=0;
			$margin=$budget_arr[$row["JOB_NO"]]['margin_pcs']*($poQtyPcs/$row[csf("TOTAL_SET_QNTY")]);
			$po_month_arr[$month_buyer]['margin']+=$margin;
			$poMatCost=$matCost;//($matCost/$costingPer)*($poQtyPcs/$row[csf("total_set_qnty")]);
			$po_month_arr[$month_buyer]['matCost']+=$poMatCost;
		}
	}
	//print_r($po_month_arr[113]['purchece_amt']); die;
	//var_dump($po_year_arr);
	asort($buyerMonth_list);
	$shortBookingNo=array(); $sampleOrderBookingNo=array(); $emblWoRateArr=array();
	$shortBookingSql=sql_select("select a.po_break_down_id, a.emblishment_name, a.rate, a.booking_type, a.is_short, a.booking_no from wo_booking_dtls a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.booking_type in (1,2,4,6) and a.status_active=1 and a.is_deleted=0");// and a.is_short in (1,2)
	foreach($shortBookingSql as $row)
	{
		/*if($row[csf("booking_type")]==1 || $row[csf("booking_type")]==2)
		{
			if($row[csf("is_short")]==1)
			{
				$shortBookingNo[$row[csf("booking_no")]]=$row[csf("booking_no")];
			}
		}
		else */if($row[csf("booking_type")]==4)
		{
			$sampleOrderBookingNo[$row[csf("booking_no")]]=$row[csf("booking_no")];
		}
		if($row[csf("booking_type")]==6)
		{
			if($row[csf("emblishment_name")]==1 || $row[csf("emblishment_name")]==2 || $row[csf("emblishment_name")]==3)
			{
				$emblWoRateArr[$row[csf("po_break_down_id")]][$row[csf("emblishment_name")]]=$row[csf("rate")];
			}
		}
	}
	//echo "select a.id, a.booking_no_id, a.booking_no from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and b.is_short=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$batchBookingsql=sql_select( "select a.id, a.booking_no_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b, gbl_temp_engine c where a.id=b.mst_id and b.po_id=c.ref_val and c.entry_form=880 and c.ref_from=3 and c.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$batchBookingNo=array(); $sampleBatchBookingNo=array();
	foreach($batchBookingsql as $row)
	{
		if($sampleOrderBookingNo[$row[csf("booking_no")]]!="")
		{
			$sampleBatchBookingNo[$row[csf("id")]]=$row[csf("booking_no")];
		}
		/*else if($shortBookingNo[$row[csf("booking_no")]]!="")
		{
			$batchBookingNo[$row[csf("id")]]=$row[csf("booking_no")];
		}*/
	}
	unset($batchBookingsql);
	//print_r($batchBookingNo);
	
	
	/*$finishRec="select a.id, a.entry_form, a.receive_basis, a.booking_no as booking_no_mst, a.currency_id, a.exchange_rate, b.batch_id, b.booking_no as booking_no_dtls, b.rate, c.po_breakdown_id, c.quantity, c.prod_id, c.color_id
		 from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=3 and d.user_id = ".$user_id." and a.receive_basis in (1,2,4,5) and a.entry_form in(7,37) and c.entry_form in(7,37) and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
		 
	$dataSqlTrans ="select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, b.batch_id as BATCHID, c.po_breakdown_id as POID, c.entry_form as ENTRY_FORM, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=3 and e.user_id = ".$user_id." and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2";
	//echo $finishRec;die;
	$dataArrayTrans=sql_select($dataSqlTrans);
	
	$exfinrec_arr=array(); $exsampleorderfinrec_arr=array(); $recIdWiseRateArr=array(); $batchCheckArr=array(); $fabProdPurchBatchArr=array();
	foreach($dataArrayTrans as $row)
	{
		$amt=$rate=0;
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		$rate=$row['RATE']/82;
		if($row['ENTRY_FORM']==37)
		{
			if($po_arr[$row['POID']]['apppo']==1)
			{
				$amt=bcmul($row['QUANTITY'],$rate,10);
				//echo $row['QUANTITY'].'='.($row['RATE']/82).'='.$amt.'<br>';
				$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_qty']+=$row['QUANTITY'];
				//$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_amt']+=$amt;
				
				$exfinrec_arr[$month_buyer]['finrec']=bcadd($amt,$exfinrec_arr[$month_buyer]['finrec'],10);
				//$recIdWiseRateArr[$row['ID']][$row['POID']]=$rate;
				$exsampleorderfinrec_arr[$month_buyer]['samfinrec']+=$sampleAmt;
				$recIdWiseRateArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
			}
		}
		else
		{
			$batchCheckArr[$row['PROD_ID']][$row['BATCHID']]=1;
		}
		array_push($batchIDArr,$row['BATCHID']);
		$fabProdPurchBatchArr[$row['POID']][$row['PROD_ID']][$row['COLOR_ID']][$row['BATCHID']]=1;
	}
	unset($dataArrayTrans);
	
	/*foreach($finishRec_res as $row)
	{
		$amount=$sampleAmt=$rate=0;
		$rate=$row[csf("rate")]/82;
		
		if($row[csf("entry_form")]==37)
		{
			if($row[csf("receive_basis")]==2)
			{
				if($sampleBatchBookingNo[$row[csf("booking_no_mst")]]!="")
				{
					$sampleAmt=$row[csf("quantity")]*$rate;
				}
				else //if($shortBookingNo[$row[csf("booking_no_mst")]]!="")
				{
					$recIdWiseRateArr[$row[csf("id")]][$row[csf("po_breakdown_id")]]=$rate;
					//$amount=$row[csf("quantity")]*$rate;
					$amount=bcmul($row[csf("quantity")],$rate,10);
				}
			}
			else //if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6 || $row[csf("receive_basis")]==9)
			{
				if($sampleBatchBookingNo[$row[csf("booking_no_dtls")]]!="")
				{
					$sampleAmt=$row[csf("quantity")]*$rate;
				}
				else //if($shortBookingNo[$row[csf("booking_no_dtls")]]!="")
				{
					$recIdWiseRateArr[$row[csf("id")]][$row[csf("po_breakdown_id")]]=$rate;
					//$amount=$row[csf("quantity")]*$rate;
					$amount=bcmul($row[csf("quantity")],$rate,10);
				}
			}
		}
		else
		{
			$batchCheckArr[$row[csf('prod_id')]][$row[csf('batch_id')]]=1;
		}
		$month_buyer=$po_arr[$row[csf("po_breakdown_id")]]['month_buyer'];
		if($po_arr[$row[csf("po_breakdown_id")]]['apppo']==1)
		{
			//$exfinrec_arr[$month_buyer]['finrec']+=$amount;
			$exfinrec_arr[$month_buyer]['finrec']=bcadd($amount,$exfinrec_arr[$month_buyer]['finrec'],10);
			//echo $amount."<br>";
			$exsampleorderfinrec_arr[$month_buyer]['samfinrec']+=$sampleAmt;
		}
		$fabProdPurchBatchArr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]]=1;
	}
	unset($finishRec_res);*/
	/* echo "<pre>";
	 print_r($exfinrec_arr);
	 die;*/
	
	//$finRecRetSql="select a.received_id, b.booking_no, c.po_breakdown_id, c.quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=46 and c.entry_form=46 and c.trans_type=3 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=3 and d.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";//and c.po_breakdown_id=43036
	$finRecRetSql="select a.received_id as RECEIVED_ID, b.prod_id as PROD_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, b.cons_uom as UOM from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (46) and c.entry_form in (46) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=3 and e.user_id = ".$user_id." ";
	//echo $finRecRetSql;die;
	$finRecRetSqlData=sql_select($finRecRetSql);
	
	foreach($finRecRetSqlData as $row)
	{
		$recRate=$amt=0;
		$recRate=$recIdWiseRateArr[$row['RECEIVED_ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']*1;
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		//echo $recRate;
		if(($recRate)>0)
		{
			$amt=bcmul($row['QUANTITY'],$recRate,10);
			if($po_arr[$row['POID']]['apppo']==1)
			{
				$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$amt,10);
			}
		}
	}
	unset($finRecRetSqlData);
	
	/*foreach($finRecRetSqlData as $row)
	{
		$recRate=$finRecReturnAmt=0;
		$month_buyer=$po_arr[$row[csf("po_breakdown_id")]]['month_buyer'];
		$recRate=$recIdWiseRateArr[$row[csf("received_id")]][$row[csf("po_breakdown_id")]];
		//$finRecReturnAmt=$row[csf("quantity")]*$recRate;
		$finRecReturnAmt=bcmul($row[csf("quantity")],$recRate,10);
		// echo "<pre>";
		// echo $row[csf("received_id")]."_".$row[csf("po_breakdown_id")]." ";
		// echo $po_arr[$row[csf("po_breakdown_id")]]['apppo'];
		if($po_arr[$row[csf("po_breakdown_id")]]['apppo']==1)
		{
			//$exfinrec_arr[$month_buyer]['finrec']-=$finRecReturnAmt;
			$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$finRecReturnAmt,10);
			//echo " ".$finRecReturnAmt;
		}
		//echo "</pre>";
	}*/
	 /*echo "<pre>";
	 print_r($exfinrec_arr);
	 echo "</pre>";*/
	//unset($finRecRetSqlData);
	
	$batchID_cond=where_con_using_array($batchIDArr,0,"id");
	$batchEntryArr=return_library_array( "select id, entry_form from pro_batch_create_mst where 1=1 ", "id", "entry_form");
	
	//$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, b.batch_id as BATCHID, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=3 and d.user_id = ".$user_id."";
	$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, b.batch_id as BATCHID, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=3 and e.user_id = ".$user_id."";
	//echo $sqlTrans;die;
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();  //$exfinrec_arr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['TRANSFER_VALUE']/82;
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		//echo $recRate;
		//echo $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']].'='.$row['FROM_PROD_ID'].'='.$row['BATCHID'].'='.$batchEntryArr[$row['BATCHID']].'<br>';
		if($row['TRANS_TYPE']==5 && $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']]!=1 && $batchEntryArr[$row['BATCHID']]==37)//trans in// 
		{
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				$exfinrec_arr[$month_buyer]['finrec']=bcadd($transVal,$exfinrec_arr[$month_buyer]['finrec'],10);
			}
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			//$amt=$row['QUANTITY']*$transRate;
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1 && $fabProdPurchBatchArr[$row['POID']][$row['PROD_ID']][$row['COLOR_ID']][$row['BATCHID']]==1)
			{
				$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$transVal,10);
				//echo "1 ".$transVal;
			}
		}
		//array_push($batchIDArr,$row['BATCHID']);
	}
	unset($sqlTransArr);
	
	/*foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['TRANSFER_VALUE']/82;
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		//echo $recRate;
		if($row['TRANS_TYPE']==5)//trans in
		{
			//$amt=$row['QUANTITY']*$transRate;
			
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				//$exfinrec_arr[$month_buyer]['finrec']+=$transVal;
				$exfinrec_arr[$month_buyer]['finrec']=bcadd($transVal,$exfinrec_arr[$month_buyer]['finrec'],10);
			}
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			//$amt=$row['QUANTITY']*$transRate;
			//echo "<pre>";
			//echo $po_arr[$row['FROM_ORDER_ID']]['apppo'];
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1)
			{
				//$exfinrec_arr[$month_buyer]['finrec']-=$transVal;
				$exfinrec_arr[$month_buyer]['finrec']=bcsub($exfinrec_arr[$month_buyer]['finrec'],$transVal,10);
				//echo " ".$transVal;
			}
			//echo "</pre>";
		}
	}
	unset($sqlTransArr);*/

	// echo "<pre>";
	// print_r($exfinrec_arr);
	// echo "</pre>";
	
	$piBookingsql=sql_select( "select a.id, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.importer_id='$company_id' and a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$piBookingNo=array();
	foreach($piBookingsql as $row)
	{
		if($shortBookingNo[$row[csf("work_order_no")]]!="")
		{
			$piBookingNo[$row[csf("id")]]=$row[csf("work_order_no")];
		}
	}
	unset($piBookingsql);
	
	//$trimsSql="select a.receive_basis, a.booking_no, a.booking_id, a.currency_id, b.rate, c.po_breakdown_id, c.quantity from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c where a.company_id='$company_id' and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(24) and c.entry_form in(24)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//$trimsSql="SELECT a.receive_basis, a.booking_no, a.booking_id, a.currency_id, b.rate, c.po_breakdown_id, c.quantity from inv_receive_master a join  inv_trims_entry_dtls b on a.id=b.mst_id join order_wise_pro_details c on b.id=c.dtls_id join wo_booking_mst d on a.booking_id = d.id and a.booking_no=d.booking_no where a.company_id='$company_id' and a.entry_form in(24) and c.entry_form in(24) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.booking_type in(2)  and d.item_category=4 and d.status_active=1 and d.is_deleted=0";
	
	$trimsSql ="select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, c.po_breakdown_id as POID, c.quantity as QUANTITY
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (24) and c.entry_form in (24) and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=3 and d.user_id = ".$user_id."";

	$trimsSql_res=sql_select($trimsSql);
	$extrimsrec_arr=array();
	foreach($trimsSql_res as $row)
	{
		$amount=$rate=0;
		$amount=$row["QUANTITY"]*$row["RATE"];
		
		$month_buyer=$po_arr[$row["POID"]]['month_buyer'];
		if($po_arr[$row["POID"]]['apppo']==1)
		{
			$extrimsrec_arr[$month_buyer]['trimrec']+=$amount;
		}
	}
	unset($trimsSql_res);
	//print_r($extrimsrec_arr); die;
	
	$sqlRet="select a.received_id as RECEIVED_ID, b.rate as RATE, c.po_breakdown_id as POID, c.quantity as QUANTITY from inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (49) and c.entry_form in (49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=3 and d.user_id = ".$user_id." ";
	$sqlRetArr=sql_select($sqlRet); //$extrimsrec_arr=array();
	foreach($sqlRetArr as $row)
	{
		$amt=0;
		$amt=$row['QUANTITY']*($row['RATE']/82);
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		if($po_arr[$row['POID']]['apppo']==1)
		{
			$extrimsrec_arr[$month_buyer]['trimrec']-=$amt;
		}
	}
	unset($sqlRetArr);
	
	$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, b.transfer_qnty as QUANTITY from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=4 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (78) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=3 and d.user_id = ".$user_id."";
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();  //$extrimsrec_arr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['RATE']/82;
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		//echo $recRate;
		if($row['TRANS_TYPE']==5)//trans in
		{
			$amt=$row['QUANTITY']*$transVal;
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				$extrimsrec_arr[$month_buyer]['trimrec']+=$amt;
			}
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			$amt=$row['QUANTITY']*$transVal;
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1)
			{
				$extrimsrec_arr[$month_buyer]['trimrec']-=$amt;
			}
		}
	}
	unset($sqlTransArr);
	
	$generalAccSql="select a.id, b.cons_rate, b.prod_id, b.order_id, b.cons_quantity
		 from inv_issue_master a, inv_transaction b, gbl_temp_engine c where b.order_id=c.ref_val and c.entry_form=880 and c.ref_from=3 and c.user_id = ".$user_id." and a.id=b.mst_id and a.entry_form in(21) and b.transaction_type=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$generalAccSql_res=sql_select($generalAccSql); $returnIssueIdArr=array();
	$exgeneralAcc_arr=array();
	foreach($generalAccSql_res as $row)
	{
		array_push($returnIssueIdArr,$row[csf("id")]);
		$amount=$rate=0;
		$rate=$row[csf("cons_rate")]/82;
		$amount=$row[csf("cons_quantity")]*$rate;
		$month_buyer=$po_arr[$row[csf("order_id")]]['month_buyer'];
		if($po_arr[$row[csf("order_id")]]['apppo']==1)
		{
			$exgeneralAcc_arr[$month_buyer]['generalacciss']+=$amount;
		}
		$gaccRetArr[$row[csf("id")]][$row[csf("prod_id")]]=$row[csf("order_id")];
	}
	unset($generalAccSql_res);
	$issueid_cond=where_con_using_array($returnIssueIdArr,0,"b.issue_id");
	

	$generalAccRet="select b.cons_rate as RATE, b.prod_id as PROD_ID, b.cons_quantity as QUANTITY, b.issue_id as ISSUE_ID
		 from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in(27) and b.transaction_type=4 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issueid_cond";
		 
	$generalAccRetArr=sql_select($generalAccRet); //$exgeneralAcc_arr=array();
	foreach($generalAccRetArr as $row)
	{
		$po_id=$gaccRetArr[$row["ISSUE_ID"]][$row["PROD_ID"]];
		$month_buyer=$po_arr[$po_id]['month_buyer'];
		$amount=$rate=0;
		$rate=$row["RATE"]/82;
		$amount=$row["QUANTITY"]*$rate;
		if($po_arr[$po_id]['apppo']==1)
		{
			$exgeneralAcc_arr[$month_buyer]['generalacciss']-=$amount;
		}
	}
	unset($generalAccRetArr);
	//print_r($exgeneralAcc_arr);
	
	$sqlYarn="select a.id, d.season, b.transaction_date, (b.cons_amount/b.cons_rate) as amt 
	from inv_issue_master a, inv_transaction b, wo_non_ord_samp_booking_dtls c, sample_development_mst d
	
	where a.id=b.mst_id and a.booking_no=c.booking_no and c.style_id=d.id and a.company_id ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and a.entry_form=3 and a.issue_purpose in (8,31) and a.booking_no like '%-SMN-%' $monthYarn_cond $buyerYarnCond $seasonYarn_cond $locationYarn_cond"; 
	//echo $sqlYarn; die;
	
	$exYarnIssue_arr=array(); $sampleBuyer=array(); $issueWiseBuyerArr=array(); $issueidArr=array();
	$sqlYarn_res=sql_select($sqlYarn); 
	foreach($sqlYarn_res as $row)
	{
		$month_buyer="";
		$shipment_date=date("Y-m",strtotime($row[csf("transaction_date")]));
		$month_buyer=$shipment_date;
		//$monthBuyer=$row[csf("buyer_id")].'_'.$shipment_date;
		$monthBuyer=$row[csf("season")];
		
		$exYarnIssue_arr[$monthBuyer]['yarniss']+=$row[csf("amt")];
		
		$buyerMonth_list[$monthBuyer]=$monthBuyer;
		
		$issueWiseBuyerArr[$row[csf("id")]]=$monthBuyer;
		$issueidArr[$row[csf("id")]]=$row[csf("id")];
	}
	unset($sqlYarn_res);
	asort($buyerMonth_list);
	/*echo "<pre>";
	print_r($buyerMonth_list);*/
	//echo "<pre>";
	//print_r($exYarnIssue_arr); die;
	
	$yarnissueidCond=where_con_using_array($issueidArr,0,"a.issue_id");
	
	$sqlYarnIssueRet="select a.issue_id, (b.cons_amount/b.cons_rate) as amt from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.company_id ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.item_category=1 and b.item_category=1 and b.transaction_type=4 and a.entry_form=9 $yarnissueidCond"; //and a.booking_no like '%-SMN-%'
	//echo $sqlYarnIssueRet; die;
	$sqlYarnIssueRetData=sql_select($sqlYarnIssueRet); 
	foreach($sqlYarnIssueRetData as $row)
	{
		$monthBuyer="";
		$monthBuyer=$issueWiseBuyerArr[$row[csf("issue_id")]];
		if($monthBuyer!="")
		{
			$exYarnIssue_arr[$monthBuyer]['yarniss']-=$row[csf("amt")];
			
			$buyerMonth_list[$monthBuyer]=$monthBuyer;
		}
	}
	unset($sqlYarnIssueRetData);
	asort($buyerMonth_list);
	
	//Fabric mfg cost Start

	
	//Fabric mfg cost End


	
	$sqlGmt="select a.po_break_down_id as POID, a.production_type as PRODTYPE, a.production_source as PRODSOURCE, a.embel_name as EMBLNAME, a.production_quantity as PRODQTY from pro_garments_production_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.production_type in (3,4) and a.status_active=1 and a.is_deleted=0";// and a.embel_name in (1,2,3)
	$sqlGmtArr=sql_select($sqlGmt);
	$emblArr=array(); $gmtProdArr=array();
	foreach($sqlGmtArr as $row)
	{
		$month_buyer=$po_arr[$row['POID']]['month_buyer'];
		if($row['PRODTYPE']==3)
		{
			$printrate=0;
			if($row['EMBLNAME']==1) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['print_amt']/$budgetAmt_arr[$row["POID"]]['print_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==2)
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['emb']/$budgetAmt_arr[$row["POID"]]['embqty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==3)
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['wash']/$budgetAmt_arr[$row["POID"]]['washqty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==4) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['special_works_amt']/$budgetAmt_arr[$row["POID"]]['special_works_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==5) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['gmts_dyeing_amt']/$budgetAmt_arr[$row["POID"]]['gmts_dyeing_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==99) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['others_amt']/$budgetAmt_arr[$row["POID"]]['others_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			$emblamt=0;
			$emblamt=$row['PRODQTY']*($printrate/12);
			//echo $row['EMBLNAME'].'='.$row['PRODSOURCE'].'='.$printrate.'<br>';
			
			$emblArr[$month_buyer][$row['EMBLNAME']]+=fn_number_format($emblamt,8,".","");

		}
		else if($row['PRODTYPE']==4)
		{
			$gmtProdArr[$month_buyer][$row['PRODTYPE']]+=$row['PRODQTY'];
		}
	}
	unset($sqlGmtArr);
	
	// echo "<pre>";
	// print_r($emblArr); 
	// echo "</pre>";

	//die;
	
	$focClaim_arr=array();
	$sql_focClaim="select a.po_break_down_id, a.shiping_mode, a.foc_or_claim, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.shiping_mode=2 and a.foc_or_claim=2 and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id, a.shiping_mode, a.foc_or_claim";
	$sql_focClaim_res=sql_select($sql_focClaim);
	foreach($sql_focClaim_res as $row)
	{
		$focClaim_arr[$row[csf("po_break_down_id")]]=$row[csf("shiping_mode")].'_'.$row[csf("foc_or_claim")].'_'.$row[csf("ex_factory_qnty")];
	}
	unset($sql_focClaim_res);
	
	$sql_ship="select a.po_break_down_id, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id"; //and a.ex_factory_date between '$startDate' and '$endDate'
	$sql_ship_res=sql_select($sql_ship); $exfactory_year_arr=array(); $exfactory_buyer_arr=array(); $deliveryPoArr=array(); $shipvalArr=array();
	foreach($sql_ship_res as $row)
	{
		$deliveryPoArr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
		if($po_arr[$row[csf("po_break_down_id")]]['po_id']==1)
		{
			//Month Summary
			$fiscalMonth=""; $exQtyPcs=0; $exValue=0;
			$exQtyPcs=$row[csf("ex_factory_qnty")];
			$month_buyer=$po_arr[$row[csf("po_break_down_id")]]['month_buyer'];
			$exfactory_year_arr[$month_buyer]['pcs']+=$exQtyPcs;
			$shipvalArr[$row[csf("po_break_down_id")]]['val']+=$exQtyPcs*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
			
			$focClaim=explode('_',$focClaim_arr[$row[csf("po_break_down_id")]]);
			if($focClaim[0]==2  && $focClaim[1]==2)
			{
				$exfactory_year_arr[$month_buyer]['air']+=$focClaim[2];
			}
			
			if($row[csf("shiping_mode")]==2  && $row[csf("foc_or_claim")]==2)
			{
				$exfactory_year_arr[$month_buyer]['air']+=$exQtyPcs;
			}
			
			if($po_arr[$row[csf("po_break_down_id")]]['ship_sta']==2)
			{
				$exfactory_year_arr[$month_buyer]['partial']+=$exQtyPcs*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
			}
			$shortExcessShip=0;
			if($po_arr[$row[csf("po_break_down_id")]]['ship_sta']==3 && $po_arr[$row[csf("po_break_down_id")]]['apppo']==1)
			{
				$exfactory_year_arr[$month_buyer]['fullship']+=$exQtyPcs*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
				$shortExcessShip=($exQtyPcs-$po_arr[$row[csf("po_break_down_id")]]['fullship_qty'])*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
				
				if($shortExcessShip>0) $exfactory_year_arr[$month_buyer]['excessship']+=$shortExcessShip;
				if($shortExcessShip<0) $exfactory_year_arr[$month_buyer]['shortship']+=$shortExcessShip;
			}
		}
	}
	//print_r($fullshipedpoArr);
	
	foreach($fullshipedpoArr as $po_id=>$refCloseQty)
	{
		//echo $po_id.'-'.$deliveryPoArr[$po_id];
		if($deliveryPoArr[$po_id]=='')
		{
			if($po_arr[$po_id]['apppo']==1)
			{
				$refclyear=$po_arr[$po_id]['month_buyer'];
				$exfactory_year_arr[$refclyear]['shortship']-=$refCloseQty;
			}
		}
	}
	
	$sqlClaim="select a.po_id, a.base_on_ex_val as claim, a.air_freight, a.sea_freight, a.discount from wo_buyer_claim_mst a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0";
	//echo $sqlClaim; die;
	$sqlClaim_res=sql_select($sqlClaim); $claim_year_arr=array();
	foreach($sqlClaim_res as $crow)
	{
		$month_buyer=$po_arr[$crow[csf("po_id")]]['month_buyer'];
		if($po_arr[$crow[csf("po_id")]]['apppo']==1)
		{
			$claim_year_arr[$month_buyer]['claim']+=$crow[csf("claim")];
			$claim_year_arr[$month_buyer]['air']+=$crow[csf("air_freight")];
			$claim_year_arr[$month_buyer]['sea']+=$crow[csf("sea_freight")];
			$claim_year_arr[$month_buyer]['discount']+=$crow[csf("discount")];
			$claimPoId[$month_buyer][$crow[csf("po_id")]]=$crow[csf("po_id")];
		}
	}
	unset($sqlClaim_res);
	
	$sql_proRealization="select b.invoice_id from com_export_proceed_realization a, com_export_doc_submission_invo b where a.invoice_bill_id=b.doc_submission_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_proRealization_res=sql_select($sql_proRealization); $invoice_proRealArr=array();
	foreach($sql_proRealization_res as $prrow)
	{
		$invoice_proRealArr[$prrow[csf("invoice_id")]]=1;
	}
	unset($sql_proRealization_res);
	
	$sqlInvoice="select a.po_breakdown_id, sum(a.current_invoice_value) as inv_val from com_export_invoice_ship_dtls a, gbl_temp_engine b where a.po_breakdown_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and a.current_invoice_value>0 group by a.po_breakdown_id";
	$sqlInvoice_res=sql_select($sqlInvoice); $invoice_year_arr=array(); $poInvArr=array();
	foreach($sqlInvoice_res as $irow)
	{
		if($po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==2 || $po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==3)
		{
			$poInvArr[$irow[csf("po_breakdown_id")]]=$irow[csf("po_breakdown_id")];
			$clyear=$po_arr[$irow[csf("po_breakdown_id")]]['month_buyer'];
			$povalue=$invoiceBal=0;
			$povalue=$shipvalArr[$irow[csf("po_breakdown_id")]]['val'];//$po_arr[$irow[csf("po_breakdown_id")]]['poVal'];
			$invoiceBal=$povalue-$irow[csf("inv_val")];
			$invoice_year_arr[$clyear]+=$invoiceBal;
		}
	}
	unset($sqlInvoice_res);
	//$po_arr[$row[csf("id")]]['ship_sta']
	
	foreach($po_arr as $poid=>$st)
	{
		if($st['ship_sta']==2 || $st['ship_sta']==3)
		{
			if($poInvArr[$poid]=="")
			{
				$clyear=$po_arr[$poid]['month_buyer'];
				$povalue=$shipvalArr[$poid]['val'];
				$invoice_year_arr[$clyear]+=$povalue;
			}
		}
	}
	
	$sqlInvoiceRec="select a.mst_id, a.po_breakdown_id, sum(a.current_invoice_value) as inv_val from com_export_invoice_ship_dtls a, gbl_temp_engine b where  a.po_breakdown_id=b.ref_val and b.entry_form=880 and b.ref_from=3 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and a.current_invoice_value>0 group by a.mst_id, a.po_breakdown_id";
	$sqlInvoiceRec_res=sql_select($sqlInvoiceRec); $invoiceRec_yearArr=array();
	foreach($sqlInvoiceRec_res as $irow)
	{
		if($po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==2 || $po_arr[$irow[csf("po_breakdown_id")]]['ship_sta']==3)
		{
			$clyear=$po_arr[$irow[csf("po_breakdown_id")]]['month_buyer'];
			
			if($invoice_proRealArr[$irow[csf("mst_id")]]==1)
				$invoiceRec_yearArr[$clyear]+=0;
			else
				$invoiceRec_yearArr[$clyear]+=$irow[csf("inv_val")];
		}
	}
	unset($sqlInvoiceRec_res);
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (3,4) and ENTRY_FORM=880");
	oci_commit($con);
	disconnect($con);
	//var_dump($exfactory_year_arr);
	ob_start();
	?>
    <div style="width:1400px; margin:0px 5px 5px 15px;">
        <div class="seasondetails" style="width:1400px; font-size:14px; font-weight:bold; color:Black; background-color:#008080" align="center"><? echo "Season Details :".$buyerArr[$buyer_id]; ?></div>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1400px; margin-top:5px" rules="all">
                <tr align="center" style="font-weight:bold">
                    <td width="70">Season</td>
                    <td width="50">No of Style</td>
                    <td width="70">No of PO</td>
                    <td width="70">Cap. Booking Min</td>
                    
                    <td width="70">Booked Pcs</td>
                    <td width="70">Booked Value($)</td>
                    
                    <td width="70">Budget Appd ($)</td>
                    <td width="50">Budget Appd %</td>
                    
                    <td width="70">Margin ($)</td>
                    <td width="50">Margin %</td>
                    <td width="50">Mat. Cons %</td>
                    <td width="70">Ex Mat Cost($)</td>
                    <td width="70">Excess Ship ($)</td>
                    <td width="70">Short Ship ($)</td>
                    <td width="70">Penalty($)</td>
                    <td width="70">Actual Margin ($)</td>
                    <td width="50">Actual margin %</td>
                    <td width="70">Ship Pending ($)</td>
                    <td width="70">Input to Ship %</td>
                    <td width="70">Invoice Pending ($)</td>
                    <td>Receivable($)</td>
                </tr>
                <? 
				foreach($buyerMonth_list as $val)
				{
					$exval=explode("_",$val);
					$monthName=""; //$buyer_id='';
					$monthName=$buyerArr[$exval[0]]; $buyerId=date("M-y",strtotime($exval[1]));
					$season_id=$val;
					
					//echo $yearF.'<br>';
					$actualpono=$bbooked_min=$bbooked_pcs=$bbooked_val=$bfobjob=$bjob=$bmargin=$bfob=$bfullshiped=$bexfactory_pcs=$bexfactory_air=$bmatCostPer=0;
						
					$bbooked_min=$po_month_arr[$val]['min'];
					$bbooked_pcs=$po_month_arr[$val]['pcs'];
					$bbooked_val=$po_month_arr[$val]['val'];
					$actualpono=$po_month_arr[$val]['actualpo'];
					$bfobjob=count($job_arr[$val]['fobjob']);
					$bjob=count($job_arr[$val]['job']);
					

					$bmargin=$po_month_arr[$val]['margin'];
					$bfob=$po_month_arr[$val]['fob'];
					$bfullshiped=$po_month_arr[$val]['fullshiped'];
					$bexfactory_pcs=$exfactory_year_arr[$val]['pcs'];
					//echo "<pre>".$bexfactory_pcs."=".$exfactory_year_arr[$val]['pcs']."</pre>";
					$bexfactory_air=$exfactory_year_arr[$val]['air'];
					
					$bmatCostPer=($po_month_arr[$val]['matCost']/$bfob)*100;
					
					$bookPer=$approved_per=$marginPer=$fob_pcs=$excessShip=$shortShip=$inputtoShipper=0;
					
					$approved_per=($bfob/$bbooked_val)*100;
					$marginPer=($bmargin/$bfob)*100;
					
					$fob_pcs=$bbooked_val/$bbooked_pcs;
					$excessShip=$exfactory_year_arr[$val]['excessship'];//($bexfactory_pcs*$fob_pcs)-$bbooked_val;
					//if($excessShip<0) $excessShip=0;
					$shortShip=str_replace("-","",$exfactory_year_arr[$val]['shortship']);//$bbooked_val-(($bexfactory_pcs*$fob_pcs)+$bfullshiped);
					//if($shortShip<0) $shortShip=0;
					
					//remove_for_po_wise_function
					
					$excessFinishFabCost=$Ex_Mat_Cost_From_Function[$val];
					//echo "<pre>".$excessFinishFabCost."=".$actualFinishFabcost."+(".$yarnStolen."-".$po_month_arr[$val]['mfgMatCost'].")</pre>";
					
					//$finPurAmt=$exfinrec_arr[$val]['finrec']-$po_month_arr[$val]['purchece_amt'];
					//echo $exfinrec_arr[$val]['finrec'].'-'.$po_month_arr[$val]['purchece_amt'];
					$finPurAmt=bcsub($exfinrec_arr[$val]['finrec'],$po_month_arr[$val]['purchece_amt']);
					$finPurAmt=($finPurAmt*1);
					//echo "<pre>".$finPurAmt."=".$exfinrec_arr[$val]['finrec']."-".$po_month_arr[$val]['purchece_amt']."</pre>";
					$trimRecAmt=$extrimsrec_arr[$val]['trimrec']-$po_month_arr[$val]['trim'];
					//echo "<pre>".$extrimsrec_arr[$val]['trimrec']."=".$po_month_arr[$val]['trim']."</pre>";
					$washAmt=$emblArr[$val][3]-$po_month_arr[$val]['washamt'];
					//echo "<pre>".$washAmt."=".$emblArr[$val][3]."-".$po_month_arr[$val]['washamt']."</pre>";
					
					$embleAmt=$emblArr[$val][2]-$po_month_arr[$val]['embamt'];

					// adding process by helal
					$print_amt=$emblArr[$val][1]-$po_month_arr[$val]['print_amt'];
					$special_works_amt=$emblArr[$val][4]-$po_month_arr[$val]['special_works_amt'];
					$gmts_dyeing_amt=$emblArr[$val][5]-$po_month_arr[$val]['gmts_dyeing_amt'];
					$others_amt=$emblArr[$val][5]-$po_month_arr[$val]['others_amt'];
					// adding process by helal

					$excessCost=$finPurAmt+$trimRecAmt+$exgeneralAcc_arr[$val]['generalacciss']+$exYarnIssue_arr[$val]['yarniss']+$exsampleorderfinrec_arr[$val]['samfinrec']+$excessFinishFabCost+$embleAmt+$washAmt+$print_amt+$special_works_amt+$gmts_dyeing_amt+$others_amt; // adding process by helal
					
					$shipPending=$po_month_arr[$val]['pending']+($po_month_arr[$val]['partial']-$exfactory_year_arr[$val]['partial']);
					$inputtoShipper=($bexfactory_pcs/$gmtProdArr[$val][4])*100;
					//echo "<pre>".$inputtoShipper."=(".$bexfactory_pcs."/".$gmtProdArr[$val][4].")*.100";
					
					$pendingCiVal=$invProdRealVal=0;
					$pendingCiVal=$invoice_year_arr[$val];
					$invProdRealVal=$invoiceRec_yearArr[$val];
					
					$panalty=0;
					$panalty=$claim_year_arr[$val]['claim']+$claim_year_arr[$val]['air']+$claim_year_arr[$val]['sea']+$claim_year_arr[$val]['discount'];
					
					$actualMargin=(($po_month_arr[$val]['margin']+$excessShip)-($excessCost+$shortShip+$panalty));
					$actualMarginPer=($actualMargin/$bfob)*100;
					?>
					<tr class="seasondetails">
                    	<td style="word-break:break-all" ><a href='#report_details' onclick="generate_report('<?=$company_id; ?>','<?=$location_id; ?>','<?=$shipStatus; ?>','<?=$orderStatus; ?>','<?=$cbo_status; ?>','<?=$season_id; ?>','<?=$client_id; ?>','<?=$style_ref; ?>','<?=$from_year; ?>','<?=$to_year; ?>','<?=$buyer_id; ?>','5');"><? if($seasonArr[$season_id]=="") echo "-"; else echo $seasonArr[$season_id]; ?></a></td>
                        
						<td align="right"><?=$bjob; ?></td>
                        <td align="right"><?=$actualpono; ?></td>
						<td align="right"><? echo fn_number_format($bbooked_min); ?></td>
                        
						<td align="right"><? echo fn_number_format($bbooked_pcs); ?></td>
						<td align="right"><? echo fn_number_format($bbooked_val); ?></td>
						
						<td align="right"><? echo fn_number_format($bfob); ?></td>
						<td align="right"><? echo fn_number_format($approved_per,2,'','',0); ?></td>
                        
						<td align="right"><? echo fn_number_format($bmargin); ?></td>
						<td align="right"><? echo fn_number_format($marginPer,2,'','',0); ?></td>
                        <td align="right"><? 
						if($style_ref=="") $style_ref=0;
						//	echo $season_buyer_id.'xx';
						 $data_season=$company_id.'__'.$location_id.'__'.$shipStatus.'__'.$orderStatus.'__'.$cbo_status.'__'.$season_id.'__'.$client_id.'__'.$style_ref.'__'.$startDate.'__'.$endDate.'__'.$season_buyer_id;
						//echo fn_number_format($bmatCostPer,2,'','',0); ?>
                         <a href="#report_details" onclick="fncexcesscost('matConsPer_popup','<?=$data_season; ?>','850px');"><? echo fn_number_format($bmatCostPer,2,'','',0); ?></a>
                        </td>
                        <td align="right" title="<?=$exfinrec_arr[$val]['finrec']-$po_month_arr[$val]['purchece_amt'].'='.$exfinrec_arr[$val]['finrec'].'-'.$po_month_arr[$val]['purchece_amt']; ?>"><a href="#report_details" onclick="fncexcesscost('excesscost_popup','<?=$finPurAmt.'__'.$trimRecAmt.'__'.$exgeneralAcc_arr[$val]['generalacciss'].'__'.$exYarnIssue_arr[$val]['yarniss'].'__'.$exsampleorderfinrec_arr[$val]['samfinrec'].'__0__'.$excessFinishFabCost.'__'.$embleAmt.'__'.$washAmt.'__'.$print_amt.'__'.$special_works_amt.'__'.$gmts_dyeing_amt.'__'.$others_amt; ?>','1050px');"><? echo fn_number_format($excessCost); ?></a></td>
                    
                        
						<td align="right"><? echo fn_number_format($excessShip); ?></td>
						<td align="right"><? echo fn_number_format($shortShip); ?></td>
						<td align="right"><a href="#report_details" onclick="fncpanalty('panalty_popup','<?=$claim_year_arr[$val]['claim'].'__'.$claim_year_arr[$val]['air'].'__'.$claim_year_arr[$val]['sea'].'__'.$claim_year_arr[$val]['discount'].'__'.implode(',', $claimPoId[$val]); ?>','550px');"><? echo fn_number_format($panalty); ?></a></td>
                        <td align="right"><? echo fn_number_format($actualMargin); ?></td>
                        <td align="right"><? echo fn_number_format($actualMarginPer,2,'','',0); ?></td>
						<td align="right"><? echo fn_number_format($shipPending); ?></td>
                        <td align="right">
                        	<a href="#report_details" onclick="inputToShip('input_to_ship','<?=$buyer_id; ?>','<?=$season_id; ?>','');">
                        		<? echo fn_number_format($inputtoShipper); ?>
                        	</a>	
                        </td>
                        <td align="right"><? echo fn_number_format($pendingCiVal); ?></td>
                        <td align="right">
                        	
                        	<a href="#report_details" onclick="inputToShip('receiveble_break_down','<?=$buyer_id; ?>','<?=$season_id; ?>','');">
                        		<? echo fn_number_format($invProdRealVal); ?>
                        	</a>	
                       </td>
					</tr>
					<?
					$totalstyle+=$bjob;
					$totalpo+=$actualpono;
					$buyerBooked_min+=$bbooked_min;
					$buyerBooked_pcs+=$bbooked_pcs;
					$buyerBooked_val+=$bbooked_val;
					$buyerExcessShort_min+=$excessShort_min;
					$buyerfob+=$bfob;
					$buyerMargin+=$bmargin;
					$buyerExcessCost+=($excessCost*1);
					$buyerExcessShip+=$excessShip;
					$buyerShortShip+=$shortShip;
					$buyerPanalty+=$panalty;
					$buyerActualMargin+=$actualMargin;
					$buyerShipPending+=$shipPending;
					$buyerPendingCiVal+=$pendingCiVal;
					$buyerInvRealVal+=$invProdRealVal;
				}
				?>
                <tr align="center" style="font-weight:bold; background-color:#CCC">
                	<td><span id="seasondetailstotal" class="adl-signs" onClick="yearT(this.id,'.seasondetails')" style="background-color:#2ABF00">+</span>&nbsp;&nbsp;Season Details Total:</td>
                    <td align="right"><? echo fn_number_format($totalstyle); ?></td>
                    <td align="right"><? echo fn_number_format($totalpo); ?></td>
                    <td align="right"><? echo fn_number_format($buyerBooked_min); ?></td>
                    
                    <td align="right"><? echo fn_number_format($buyerBooked_pcs); ?></td>
                    <td align="right"><? echo fn_number_format($buyerBooked_val); ?></td>
                    
                    <td align="right"><? echo fn_number_format($buyerfob); ?></td>
                    <td>&nbsp;</td>
                   
                    <td align="right"><? echo fn_number_format($buyerMargin); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($buyerExcessCost); ?></td>
                    <td align="right"><? echo fn_number_format($buyerExcessShip); ?></td>
                    <td align="right"><? echo fn_number_format($buyerShortShip); ?></td>
                    <td align="right"><? echo fn_number_format($buyerPanalty); ?></td>
                    <td align="right"><? echo fn_number_format($buyerActualMargin); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><? echo fn_number_format($buyerShipPending); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><? echo fn_number_format($buyerPendingCiVal); ?></td>
                    <td align="right"><?=fn_number_format($buyerInvRealVal); ?></td>
                </tr>
          </table>
      </div>
    <?
	exit();
}


if($action=="po_details_list_view")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	
	$exData=explode("***",$data);
	//print_r($exData);die;
	$company_id=$exData[0];
	$location_id=$exData[1];
	$shipStatus=$exData[2];
	
	$orderStatus=$exData[3];
	$cbo_status=$exData[4];
	$buyer_id=$exData[5];
	$season_id=$exData[6];
	$client_id=$exData[7];
	$style_ref=$exData[8];
	
	$from_year=$exData[9];
	$to_year=$exData[10];
	
	$buyerCond = ""; $calAlloBuyerCond=""; $buyerYarnCond="";
	if ($buyer_id == 0) 
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) 
		{
			if ($_SESSION['logic_erp']["buyer_id"] != "")
			{
				$buyerCond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$calAlloBuyerCond = " and b.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyerYarnCond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			}
			else
			{
				$buyerCond = "";
				$calAlloBuyerCond=""; 
				$buyerYarnCond="";
			}
		}
		else 
		{
			$buyerCond = "";
			$calAlloBuyerCond=""; 
			$buyerYarnCond="";
		}
	} 
	else 
	{
		$buyerCond = " and a.buyer_name=$buyer_id";
		$calAlloBuyerCond="and b.buyer_id='$buyer_id'";
		$buyerYarnCond=" and a.buyer_id='$buyer_id'";
	}
	
	$exfirstYear=explode('-',$from_year);
	$exlastYear=explode('-',$to_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exlastYear[1];
	//echo $firstYear.'='.$lastYear; die;
	$yearMonth_arr=array(); $yearStartEnd_arr=array(); $j=12; $i=1;
	$startDate=''; $endDate="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$yearMonth_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	//echo $startDate.'='.$endDate; die;
	
	$month_cond=""; $calDateCond=""; $calAlloBuyerCond="";
	$month_cond="and b.shipment_date between '$startDate' and '$endDate'";
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($shipStatus==1) $shipStatusCond="and b.shiping_status in (1,2)"; else if($shipStatus==2) $shipStatusCond="and b.shiping_status in (3)"; else $shipStatusCond="";
	if($orderStatus==0) $orderStatusCond=""; else $orderStatusCond=" and b.is_confirmed in ( $orderStatus )";
	if($season_id==0) $seasonCond=""; else $seasonCond=" and a.season_buyer_wise in ( $season_id )";
	if($client_id==0) $clientCond=""; else $clientCond=" and a.client_id in ( $client_id )";
	if(trim($style_ref)==0) $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='$style_ref'";
	
	$sql_po="select a.job_no, a.id as jobid, a.buyer_name, a.style_ref_no, a.total_set_qnty, (b.po_quantity*a.set_smv) as set_smv, b.id, b.po_number, b.grouping, b.shipment_date, (b.unit_price/a.total_set_qnty) as unit_price, b.shiping_status, (b.po_quantity*a.total_set_qnty) as po_quantity, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $month_cond $buyerCond $jobLocationCond $shipStatusCond $orderStatusCond $seasonCond $clientCond $styleRefCond order by b.grouping ASC";//(a.set_smv/a.total_set_qnty)
	
	//echo $sql_po;// die;
	$sql_po_res=sql_select($sql_po); 
	
	foreach($sql_po_res as $row)
	{
		$poidstr.=$row[csf('id')].',';
		$poididarr[$row[csf('id')]]=$row[csf('id')];
		$jobididarr[$row[csf('jobid')]]=$row[csf('jobid')];
		if($jobId=="") $jobId="'".$row[csf("jobid")]."'"; else $jobId.=",'".$row[csf("jobid")]."'";
		//echo "<pre>".$row[csf('id')]."</pre>";
	}
	//$po_ids=array_filter(array_unique(explode(",",$poidstr)));
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (5,6) and ENTRY_FORM=880");
	oci_commit($con);
	//print_r($poididarr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 5, $poididarr, $empty_arr);//PO ID
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 6, $jobididarr, $empty_arr);//Job ID
	
	$exMatCostP=poWiseExmfgCost(implode(",",$poididarr),5);
	//print_r($exMatCostP[18908]);
	//die;
	/*foreach($exMatCostP as $rpoid=>$pdata)
	{
		$Ex_Mat_Cost_From_Function[$powiseSeasonArr[$rpoid]]+=fn_number_format($pdata,8,".","");
	}*/

	//echo "Select * FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (5,6) and ENTRY_FORM=880";die;
	
	$actualPoSql=sql_select("select a.job_no, a.po_break_down_id, a.acc_po_no, a.remarks from wo_po_acc_po_info a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=880 and b.ref_from=5 and a.status_active=1 and a.is_deleted=0");
	$actualPoArr=array(); $isactualpoarr=array();
	foreach($actualPoSql as $actrow)
	{
		$actualPoArr[$actrow[csf("po_break_down_id")]][$actrow[csf("acc_po_no")]]=$actrow[csf("acc_po_no")];
		if(trim($actrow[csf("remarks")])!="")
			$isactualpoarr[$actrow[csf("po_break_down_id")]]=$actrow[csf("remarks")];
	}
	unset($actualPoSql);
	
	$budgetAmt_arr=array();
	//$sqlBomAmt="select a.job_id as JOB_ID, a.po_id as PO_ID, a.greypurch_amt as GREYPURCH_AMT, a.finpurch_amt as FINPURCH_AMT, a.yarn_amt, a.conv_amt, a.trim_amt, a.emb_qty, a.emb_amt as EMB_AMT, a.wash_qty, a.wash_amt,a.print_qty as PRINT_QTY, a.print_amt AS PRINT_AMT, a.special_works_qty as SPECIAL_WORKS_QTY,a.special_works_amt as SPECIAL_WORKS_AMT,a.gmts_dyeing_qty as GMTS_DYEING_QTY,a.gmts_dyeing_amt as GMTS_DYEING_AMT,a.others_qty as OTHERS_QTY,a.others_amt AS OTHERS_AMT from bom_process a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0";
	
	
	$sqlBomAmt="select a.job_id as JOB_ID, a.po_id as PO_ID, a.greypurch_amt as GREYPURCH_AMT, a.finpurch_amt as FINPURCH_AMT, a.yarn_amt as YARN_AMT, a.conv_amt as CONV_AMT, a.trim_amt as TRIM_AMT, a.emb_qty as EMB_QTY, a.emb_amt as EMB_AMT, a.wash_qty as WASH_QTY, a.wash_amt as WASH_AMT,a.print_qty as PRINT_QTY,a.print_amt AS PRINT_AMT,a.special_works_qty as SPECIAL_WORKS_QTY,a.special_works_amt as SPECIAL_WORKS_AMT,a.gmts_dyeing_qty as GMTS_DYEING_QTY,a.gmts_dyeing_amt as GMTS_DYEING_AMT,a.others_qty as OTHERS_QTY,a.others_amt AS OTHERS_AMT from bom_process a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 ";
	$sqlBomAmtRes=sql_select($sqlBomAmt);
	foreach($sqlBomAmtRes as $row)
	{
		/*$budgetAmt_arr[$row[csf("po_id")]]['fab']=$row[csf("greypurch_amt")];
		$budgetAmt_arr[$row[csf("po_id")]]['purchfin_amt']=$row[csf("finpurch_amt")];
		$budgetAmt_arr[$row[csf("po_id")]]['yarn']=$row[csf("yarn_amt")];
		$budgetAmt_arr[$row[csf("po_id")]]['conv']=$row[csf("conv_amt")];
		$budgetAmt_arr[$row[csf("po_id")]]['trim']+=$row[csf("trim_amt")];
		$budgetAmt_arr[$row[csf("po_id")]]['embqty']=$row[csf("emb_qty")];
		//$budgetAmt_arr[$row[csf("po_id")]]['emb']=$row[csf("PRINT_AMT")]+$row["SPECIAL_WORKS_AMT"]+$row["GMTS_DYEING_AMT"]+$row["OTHERS_AMT"];
		$budgetAmt_arr[$row[csf("po_id")]]['washqty']=$row[csf("wash_qty")];
		$budgetAmt_arr[$row[csf("po_id")]]['wash']=$row[csf("wash_amt")];

		// adding process by helal
		$budgetAmt_arr[$row["PO_ID"]]['print_qty']=$row["PRINT_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['print_amt']=$row["PRINT_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_qty']=$row["SPECIAL_WORKS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_amt']=$row["SPECIAL_WORKS_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_qty']=$row["GMTS_DYEING_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_amt']=$row["GMTS_DYEING_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['others_qty']=$row["OTHERS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['others_amt']=$row["OTHERS_AMT"];*/
		$budgetAmt_arr[$row["PO_ID"]]['fab']=$row["GREYPURCH_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['purchfin_amt']=$row["FINPURCH_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['yarn']=$row["YARN_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['conv']=$row["CONV_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['trim']=$row["TRIM_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['embqty']=$row["EMB_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['emb']=$row["EMB_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['washqty']=$row["WASH_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['wash']=$row["WASH_AMT"];

		// adding process by helal
		$budgetAmt_arr[$row["PO_ID"]]['print_qty']=$row["PRINT_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['print_amt']=$row["PRINT_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_qty']=$row["SPECIAL_WORKS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['special_works_amt']=$row["SPECIAL_WORKS_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_qty']=$row["GMTS_DYEING_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['gmts_dyeing_amt']=$row["GMTS_DYEING_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['others_qty']=$row["OTHERS_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['others_amt']=$row["OTHERS_AMT"];
		// adding process by helal
	}
	unset($sqlBomAmtRes);
	// echo "<pre>";
	// print_r($budgetAmt_arr);
	// echo "</pre>";
	
	$sql_budget="select a.job_no, a.approved, a.costing_per, a.costing_date, a.exchange_rate, b.margin_pcs_bom from wo_pre_cost_mst a, wo_pre_cost_dtls b, gbl_temp_engine c where a.job_id=b.job_id and a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=6 and c.user_id = ".$user_id." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	
	$sql_budget_res=sql_select($sql_budget); $budget_arr=array();
	foreach($sql_budget_res as $row)
	{
		$budget_arr[$row[csf("job_no")]]['app']=$row[csf("approved")];
		$budget_arr[$row[csf("job_no")]]['margin_pcs']=$row[csf("margin_pcs_bom")];
		$budget_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
		$budget_arr[$row[csf("job_no")]]['exchange_rate']=$row[csf("exchange_rate")];
		$budget_arr[$row[csf("job_no")]]['costing_date']=$row[csf("costing_date")];
	}
	unset($sql_budget_res);
	
	
	$po_dtls_arr=array(); $job_arr=array(); $po_arr=array(); $poExchangeRatearr=array(); $fullshipedpoArr=array();
	$countRow=count($sql_po_res); $po_id_arr=array();
	foreach($sql_po_res as $row)
	{
		//array_push($po_id_arr, $row[csf('id')]);
		$costing_per=0; $costingPer=0; $matCost=0; $poMatCost=0;
		$costing_per=$budget_arr[$row[csf("job_no")]]['costing_per'];
		$poExchangeRatearr[$row[csf("id")]]=$budget_arr[$row[csf("job_no")]]['exchange_rate'];
		
		if($costing_per==1) $costingPer=12;
		if($costing_per==2) $costingPer=1;
		if($costing_per==3) $costingPer=24;
		if($costing_per==4) $costingPer=36;
		if($costing_per==5) $costingPer=48;
		
		$month_buyer="";
		$shipment_date=date("Y-m",strtotime($row[csf("shipment_date")]));
		$month_buyer=$row[csf("buyer_name")].'_'.$shipment_date;
		
		$poQtyPcs=0; $poValue=0; $booked_min=0;
		$poQtyPcs=$row[csf("po_quantity")];
		$poValue=$row[csf("po_total_price")];
		$booked_min=$row[csf("set_smv")];
		
		$po_dtls_arr[$row[csf("id")]]['intRef']=$row[csf("grouping")];
		$po_dtls_arr[$row[csf("id")]]['styleRef']=$row[csf("style_ref_no")];
		$po_dtls_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
		$po_dtls_arr[$row[csf("id")]]['costing_date']=$budget_arr[$row[csf("job_no")]]['costing_date'];
		$po_dtls_arr[$row[csf("id")]]['costing_per']=$costing_per;
		$po_dtls_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$po_dtls_arr[$row[csf("id")]]['poQty']=$poQtyPcs;
		$po_dtls_arr[$row[csf("id")]]['poRate']=$row[csf("unit_price")];
		$po_dtls_arr[$row[csf("id")]]['poVal']=$poValue;
		$po_dtls_arr[$row[csf("id")]]['min']=$booked_min;
		$po_dtls_arr[$row[csf("id")]]['shiping_status']=$row[csf("shiping_status")];
		$po_dtls_arr[$row[csf("id")]]['month_buyer']=$month_buyer;

		$matCost=$budgetAmt_arr[$row[csf("id")]]['fab']+$budgetAmt_arr[$row[csf("id")]]['yarn']+$budgetAmt_arr[$row[csf("id")]]['conv']+$budgetAmt_arr[$row[csf("id")]]['trim']+$budgetAmt_arr[$row[csf("id")]]['wash']+$budgetAmt_arr[$row[csf("id")]]['print_amt']+$budgetAmt_arr[$row[csf("id")]]['special_works_amt']+$budgetAmt_arr[$row[csf("id")]]['gmts_dyeing_amt']+$budgetAmt_arr[$row[csf("id")]]['others_amt'];// adding process by helal
		$mfgMaterialCost=$budgetAmt_arr[$row[csf("id")]]['yarn']+$budgetAmt_arr[$row[csf("id")]]['conv'];
		$po_dtls_arr[$row[csf("id")]]['mfgMatCost']=$mfgMaterialCost;
		
		if($row[csf("shiping_status")]==3)
		{
			$po_dtls_arr[$row[csf("id")]]['fullshiped']+=$poValue;
			$po_arr[$row[csf("id")]]['fullship_qty']=$poQtyPcs;
			$fullshipedpoArr[$row[csf("id")]]=$poValue;
		}
		else if($row[csf("shiping_status")]==2)
		{
			$po_dtls_arr[$row[csf("id")]]['partial']+=$poValue;
		}
		else 
		{
			$po_dtls_arr[$row[csf("id")]]['pending']+=$poValue;
		}
		
		$job_arr[$row[csf("id")]]['job'][$row[csf("id")]]=$row[csf("id")];
		$po_arr[$row[csf("id")]]['po_id']=1;
		$po_arr[$row[csf("id")]]['month_buyer']=$month_buyer;
		$po_arr[$row[csf("id")]]['ship_sta']=$row[csf("shiping_status")];
		$po_arr[$row[csf("id")]]['po_price']=$row[csf("unit_price")];
		$po_arr[$row[csf("id")]]['amc']=$budgetAmt_arr[$row[csf("id")]]['fab'].' F+'.$budgetAmt_arr[$row[csf("id")]]['yarn'].' Y+'.$budgetAmt_arr[$row[csf("id")]]['conv'].' C+'.$budgetAmt_arr[$row[csf("id")]]['trim'].' T+'.$budgetAmt_arr[$row[csf("id")]]['emb'].' E+'.$budgetAmt_arr[$row[csf("id")]]['wash'].' W';
		
		if($budget_arr[$row[csf("job_no")]]['app']==1)
		{
			$poMatCost=$matCost;//($matCost/$costingPer)*($poQtyPcs/$row[csf("total_set_qnty")]);
			$margin=$marginPer=0;
			$margin=$budget_arr[$row[csf("job_no")]]['margin_pcs']*($poQtyPcs/$row[csf("total_set_qnty")]);
			$marginPer=($margin/$poValue)*100;
			
			$po_dtls_arr[$row[csf("id")]]['appval']=$poValue;
			$po_dtls_arr[$row[csf("id")]]['matCost']=$poMatCost;
			$po_dtls_arr[$row[csf("id")]]['margin']=$margin;
			$po_dtls_arr[$row[csf("id")]]['marginper']=$marginPer;
			$po_arr[$row[csf("id")]]['apppo']=1;
			
			$job_arr[$row[csf("id")]]['fobjob'][$row[csf("id")]]=$row[csf("id")];
			
			//$po_month_arr[$month_buyer]['fob']+=$poValue;
		}
	}
	
	//var_dump($actualPoArr);
	//asort($buyerMonth_list);
	
	$shortBookingNo=array(); $shortBookingRate=array(); $sampleOrderBookingNo=array(); $sampleOrderBookingRate=array();
	
	$shortBookingSql=sql_select("select a.po_break_down_id, a.emblishment_name, a.rate, a.booking_type, a.is_short, a.booking_no from wo_booking_dtls a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.booking_type in (1,2,4,6) and a.status_active=1 and a.is_deleted=0");
	foreach($shortBookingSql as $row)
	{
		/*if(($row[csf("booking_type")]==1 || $row[csf("booking_type")]==2))
		{
			if($row[csf("is_short")]==1)
			{
				$shortBookingNo[$row[csf("booking_no")]]=$row[csf("booking_no")];
			}
			$shortBookingRate[$row[csf("booking_no")]]+=$row[csf("amount")]/$row[csf("wo_qnty")];
		}
		else */if($row[csf("booking_type")]==4)
		{
			$sampleOrderBookingNo[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$sampleOrderBookingRate[$row[csf("booking_no")]]+=$row[csf("amount")]/$row[csf("wo_qnty")];
		}
		if($row[csf("booking_type")]==6)
		{
			if($row[csf("emblishment_name")]==1 || $row[csf("emblishment_name")]==2 || $row[csf("emblishment_name")]==3)
			{
				$emblWoRateArr[$row[csf("po_break_down_id")]][$row[csf("emblishment_name")]]=$row[csf("rate")];;
			}
		}
	}
	//echo "select a.id, a.booking_no_id, a.booking_no from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and b.is_short=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//$batchBookingsql=sql_select( "select id, booking_no_id, booking_no from pro_batch_create_mst where company_id='$company_id' and status_active=1 and is_deleted=0");
	$batchBookingsql=sql_select( "select a.id, a.booking_no_id, a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b, gbl_temp_engine c where a.id=b.mst_id and b.po_id=c.ref_val and c.entry_form=880 and c.ref_from=5 and c.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$batchBookingNo=array(); $sampleBatchBookingNo=array();
	foreach($batchBookingsql as $row)
	{
		if($sampleOrderBookingNo[$row[csf("booking_no")]]!="")
		{
			$sampleBatchBookingNo[$row[csf("id")]]=$row[csf("booking_no")];
			$sampleBatchBookingRate[$row[csf("id")]]=$sampleOrderBookingRate[$row[csf("booking_no")]];
		}
		/*else if($shortBookingNo[$row[csf("booking_no")]]!="")
		{
			$batchBookingNo[$row[csf("id")]]=$row[csf("booking_no")];
			$batchBookingRate[$row[csf("id")]]=$shortBookingRate[$row[csf("booking_no")]];
		}*/
	}
	unset($batchBookingsql);
	//print_r($batchBookingNo);
	
	/*$finishRec="select a.id, a.entry_form, a.receive_basis, a.booking_no as booking_no_mst, a.currency_id, a.exchange_rate, b.prod_id, b.batch_id, b.booking_no as booking_no_dtls, b.rate, c.po_breakdown_id, c.quantity, c.color_id
		 from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=5 and d.user_id = ".$user_id." and a.receive_basis in (1,2,4) and a.entry_form in(7,37) and c.entry_form in(7,37) and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";*/
		 
	/*$dataArrayTrans = sql_select("select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, b.batch_id as BATCHID, c.po_breakdown_id as POID, c.entry_form as ENTRY_FORM, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2 and c.po_breakdown_id='$poid'");*/
    //echo $finishRec;die;
		 
	//$finishRec_res=sql_select($finishRec);
	
	
	
	$dataSqlTrans ="select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, b.batch_id as BATCHID, c.po_breakdown_id as POID, c.entry_form as ENTRY_FORM, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=5 and e.user_id = ".$user_id."
	
	
	and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2";
	//echo $dataSqlTrans;
	$dataArrayTrans= sql_select($dataSqlTrans);
	$recDataRetArr=array(); $batchCheckArr=array(); $batchIDArr=array(); $fabProdPurchBatchArr=array();
	foreach($dataArrayTrans as $row)
	{
		$amt=0;
		
		if($row['ENTRY_FORM']==37)
		{
			if($po_arr[$row['POID']]['apppo']==1)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				//echo $row['QUANTITY'].'='.($row['RATE']/82).'='.$amt.'<br>';
				$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_qty']+=$row['QUANTITY'];
				//$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_amt']+=$amt;
				
				$exfinrec_arr[$row['POID']]['finrec']+=$amt;
				//$recIdWiseRateArr[$row['ID']][$row['POID']]=$rate;
				$exsampleorderfinrec_arr[$row['POID']]['samfinrec']+=$sampleAmt;
				$recIdWiseRateArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
			}
		}
		else
		{
			$batchCheckArr[$row['PROD_ID']][$row['BATCHID']]=1;
		}
		array_push($batchIDArr,$row['BATCHID']);
		$fabProdPurchBatchArr[$row['POID']][$row['PROD_ID']][$row['COLOR_ID']][$row['BATCHID']]=1;
	}
	unset($dataArrayTrans);
	
	/*foreach($finishRec_res as $row)
	{
		$amount=$sampleAmt=$rate=0;
		$rate=$row[csf("rate")]/82;
		
		if($row[csf("entry_form")]==37)
		{
			if($row[csf("receive_basis")]==2)
			{
				if($sampleBatchBookingNo[$row[csf("booking_no_mst")]]!="")
				{
					$sampleAmt=$row[csf("quantity")]*$rate;//*$sampleOrderBookingRate[$row[csf("booking_no_mst")]];
				}
				else // if($shortBookingNo[$row[csf("booking_no_mst")]]!="")
				{
					$recIdWiseRateArr[$row[csf("id")]][$row[csf("po_breakdown_id")]]=$rate;
					//echo $rate.'=='.$row[csf("rate")].'=='.$poExchangeRatearr[$row[csf("po_breakdown_id")]].'=='.$row[csf("booking_no_mst")].'b<br>';
					$amount=$row[csf("quantity")]*$rate;//*$shortBookingRate[$row[csf("booking_no_mst")]];
				}
			}
			else //if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6 || $row[csf("receive_basis")]==9)
			{
				if($sampleBatchBookingNo[$row[csf("booking_no_dtls")]]!="")
				{
					$sampleAmt=$row[csf("quantity")]*$rate;//*$sampleOrderBookingRate[$row[csf("booking_no_mst")]];
				}
				else // if($shortBookingNo[$row[csf("booking_no_dtls")]]!="")
				{
					$recIdWiseRateArr[$row[csf("id")]][$row[csf("po_breakdown_id")]]=$rate;
					//echo $rate.'=='.$row[csf("rate")].'=='.$poExchangeRatearr[$row[csf("po_breakdown_id")]].'r<br>';
					$amount=$row[csf("quantity")]*$rate;//*$shortBookingRate[$row[csf("booking_no_mst")]];
				}
			}
		}
		else
		{
			$batchCheckArr[$row[csf('prod_id')]][$row[csf('batch_id')]]=1;
		}
		if($po_arr[$row[csf("po_breakdown_id")]]['apppo']==1)
		{
			//echo $row[csf("quantity")].'='.$rate.'='.$amount.'<br>';
			
			
		}
		$fabProdPurchBatchArr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]]=1;
	}
	unset($finishRec_res);*/ //die;
	 /*echo "<pre>";
	 print_r($exfinrec_arr);*/ //die;
	 
	 $sqlRet="select a.received_id as RECEIVED_ID, b.prod_id as PROD_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, b.cons_uom as UOM from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (46) and c.entry_form in (46) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=5 and e.user_id = ".$user_id." ";
	$sqlRetArr=sql_select($sqlRet); 
	foreach($sqlRetArr as $row)
	{
		$recRate=$amt=0;
		$recRate=$recIdWiseRateArr[$row['RECEIVED_ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']*1;
		//echo $recRate;
		if(($recRate)>0)
		{
			$amt=$row['QUANTITY']*$recRate;
			//$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinRet_qty']+=$row['QUANTITY'];
			//$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinRet_amt']+=$amt;
			
			if($po_arr[$row['POID']]['apppo']==1)
			{
				$exfinrec_arr[$row['POID']]['finrec']-=$amt;
			}
		}
	}
	unset($sqlRetArr);
	
	/*echo "<pre>";
	 print_r($exfinrec_arr);*/
	
	/*$finRecRetSql="select a.received_id, b.booking_no, c.po_breakdown_id, c.quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=46 and c.entry_form=46 and c.trans_type=3 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=5 and d.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";//and c.po_breakdown_id=43036
	$finRecRetSqlData=sql_select($finRecRetSql);
	foreach($finRecRetSqlData as $row)
	{
		$recRate=$finRecReturnAmt=0;
		
		$recRate=$recIdWiseRateArr[$row[csf("received_id")]][$row[csf("po_breakdown_id")]];
		$finRecReturnAmt=$row[csf("quantity")]*$recRate;
		if($po_arr[$row[csf("po_breakdown_id")]]['apppo']==1)
		{
			$exfinrec_arr[$row[csf("po_breakdown_id")]]['finrec']-=$finRecReturnAmt;
		}
	}
	unset($finRecRetSqlData);*/
	// echo "<pre>";
	// print_r($exfinrec_arr); //die;
	// echo "</pre>";
	
	$batchID_cond=where_con_using_array($batchIDArr,0,"id");
	$batchEntryArr=return_library_array( "select id, entry_form from pro_batch_create_mst where 1=1 ", "id", "entry_form");
	
	$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, b.batch_id as BATCHID, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d, gbl_temp_engine e where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=e.ref_val and e.entry_form=880 and e.ref_from=5 and e.user_id = ".$user_id."";
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['TRANSFER_VALUE']/82;
		//echo $recRate;
		//echo $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']].'='.$row['FROM_PROD_ID'].'='.$row['BATCHID'].'='.$batchEntryArr[$row['BATCHID']].'<br>';
		if($row['TRANS_TYPE']==5 && $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']]!=1 && $batchEntryArr[$row['BATCHID']]==37)//trans in// 
		{
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				$exfinrec_arr[$row['TO_ORDER_ID']]['finrec']+=$transVal;
			}
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			//$amt=$row['QUANTITY']*$transRate;
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1 && $fabProdPurchBatchArr[$row['POID']][$row['PROD_ID']][$row['COLOR_ID']][$row['BATCHID']]==1)
			{
				$exfinrec_arr[$row['FROM_ORDER_ID']]['finrec']-=$transVal;
				//echo "1 ".$transVal;
			}
		}
		//array_push($batchIDArr,$row['BATCHID']);
	}
	unset($sqlTransArr);
	/*echo "<pre>";
	 print_r($exfinrec_arr);*/
	/*$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, b.batch_id as BATCHID, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=5 and d.user_id = ".$user_id."";
	//echo $sqlTrans;die;
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();  //$exfinrec_arr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['TRANSFER_VALUE']/82;
		//echo $recRate;
		if($row['TRANS_TYPE']==5 && $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']]!=1)//trans in
		{
			//$amt=$row['QUANTITY']*$transRate;
			
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				$exfinrec_arr[$row['TO_ORDER_ID']]['finrec']+=$transVal;
				// echo "<pre>";
				// echo "2 ".$transVal;
				// echo "</pre>";
			}
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			//$amt=$row['QUANTITY']*$transRate;
			//echo "<pre>";
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1 && $fabProdPurchBatchArr[$row['POID']][$row['PROD_ID']][$row['COLOR_ID']][$row['BATCHID']]==1)
			{
				$exfinrec_arr[$row['FROM_ORDER_ID']]['finrec']-=$transVal;
				//echo "1 ".$transVal;
			}
			//echo "</pre>";
		}
	}
	unset($sqlTransArr);*/
	// echo "<pre>";
	// print_r($exfinrec_arr); //die;
	// echo "</pre>";
	
	$piBookingsql=sql_select( "select a.id, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.importer_id='$company_id' and a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$piBookingNo=array();
	foreach($piBookingsql as $row)
	{
		$piBookingNo[$row[csf("id")]]=$row[csf("work_order_no")];
		$piBookingRate[$row[csf("id")]]=$shortBookingRate[$row[csf("work_order_no")]];
	}
	unset($piBookingsql);
	
	$trimsSql ="select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, c.po_breakdown_id as POID, c.quantity as QUANTITY
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (24) and c.entry_form in (24) and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=5 and d.user_id = ".$user_id."";
	$trimsSql_res=sql_select($trimsSql);
	$extrimsrec_arr=array();
	
	foreach($trimsSql_res as $row)
	{
		$amount=0;
		$amount=$row['QUANTITY']*$row['RATE'];
		if($po_arr[$row['POID']]['apppo']==1)
		{
			$extrimsrec_arr[$row['POID']]['trimrec']+=$amount;
		}
	}
	unset($trimsSql_res);
	//print_r($extrimsrec_arr);
	
	$sqlRet="select a.received_id as RECEIVED_ID, b.rate as RATE, c.po_breakdown_id as POID, c.quantity as QUANTITY from inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (49) and c.entry_form in (49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=5 and d.user_id = ".$user_id." ";
	$sqlRetArr=sql_select($sqlRet); //$extrimsrec_arr=array();
	foreach($sqlRetArr as $row)
	{
		$amt=0;
		$amt=$row['QUANTITY']*($row['RATE']/82);
		
		if($po_arr[$row['POID']]['apppo']==1)
		{
			$extrimsrec_arr[$row['POID']]['trimrec']-=$amt;
		}
	}
	unset($sqlRetArr);
	//print_r($extrimsrec_arr);
	
	$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, b.transfer_qnty as QUANTITY from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, gbl_temp_engine d where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=4 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (78) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.po_breakdown_id=d.ref_val and d.entry_form=880 and d.ref_from=5 and d.user_id = ".$user_id."";
	$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();  //$extrimsrec_arr=array();
	foreach($sqlTransArr as $row)
	{
		$transVal=$amt=0;
		$transVal=$row['RATE']/82;
		//echo $recRate;
		if($row['TRANS_TYPE']==5)//trans in
		{
			$amt=$row['QUANTITY']*$transVal;
			if($po_arr[$row['TO_ORDER_ID']]['apppo']==1)
			{
				$extrimsrec_arr[$row['TO_ORDER_ID']]['trimrec']+=$amt;
			}
			
		}
		else if($row['TRANS_TYPE']==6)//trans out
		{
			$amt=$row['QUANTITY']*$transVal;
			if($po_arr[$row['FROM_ORDER_ID']]['apppo']==1)
			{
				$extrimsrec_arr[$row['FROM_ORDER_ID']]['trimrec']-=$amt;
			}
		}
	}
	unset($sqlTransArr);
	//print_r($extrimsrec_arr); //die;
	
	$generalAccSql="select a.id, b.cons_rate, b.prod_id, b.order_id, b.cons_quantity
		 from inv_issue_master a, inv_transaction b, gbl_temp_engine c where b.order_id=c.ref_val and c.entry_form=880 and c.ref_from=5 and c.user_id = ".$user_id." and a.id=b.mst_id and a.entry_form in(21) and b.transaction_type=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$generalAccSql_res=sql_select($generalAccSql); $returnIssueIdArr=array();
	$exgeneralAcc_arr=array();
	foreach($generalAccSql_res as $row)
	{
		array_push($returnIssueIdArr,$row[csf("id")]);
		$amount=$rate=0;
		$rate=$row[csf("cons_rate")]/82;
		$amount=$row[csf("cons_quantity")]*$rate;
		if($po_arr[$row[csf("order_id")]]['apppo']==1)
		{
			$exgeneralAcc_arr[$row[csf("order_id")]]['generalacciss']+=$amount;
		}
		$gaccRetArr[$row[csf("id")]][$row[csf("prod_id")]]=$row[csf("order_id")];
	}
	unset($generalAccSql_res);
	$issueid_cond=where_con_using_array($returnIssueIdArr,0,"b.issue_id");
	
	$generalAccRet="select b.cons_rate as RATE, b.prod_id as PROD_ID, b.cons_quantity as QUANTITY, b.issue_id as ISSUE_ID
		 from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form in(27) and b.transaction_type=4 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issueid_cond";
		 
	$generalAccRetArr=sql_select($generalAccRet); //$exgeneralAcc_arr=array();
	foreach($generalAccRetArr as $row)
	{
		$po_id=$gaccRetArr[$row["ISSUE_ID"]][$row["PROD_ID"]];
		$amount=$rate=0;
		$rate=$row["RATE"]/82;
		$amount=$row["QUANTITY"]*$rate;
		if($po_arr[$po_id]['apppo']==1)
		{
			$exgeneralAcc_arr[$po_id]['generalacciss']-=$amount;
		}
	}
	unset($generalAccRetArr);
	//print_r($exgeneralAcc_arr);
	
	//remove_for_po_wise_function
	
	
	$sqlGmt="select a.po_break_down_id as POID, a.production_type as PRODTYPE, a.production_source as PRODSOURCE, a.embel_name as EMBLNAME, a.production_quantity as PRODQTY from pro_garments_production_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.production_type in (3,4)  and a.status_active=1 and a.is_deleted=0";//and a.embel_name in (1,2,3)
	$sqlGmtArr=sql_select($sqlGmt);
	$emblArr=array(); $gmtProdArr=array();
	foreach($sqlGmtArr as $row)
	{
		if($row['PRODTYPE']==3)
		{
			$printrate=0;
			// adding process by helal
			if($row['EMBLNAME']==1) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['print_amt']/$budgetAmt_arr[$row["POID"]]['print_qty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==2)
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['emb']/$budgetAmt_arr[$row["POID"]]['embqty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==3)
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['wash']/$budgetAmt_arr[$row["POID"]]['washqty'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==4) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['special_works_qty']/$budgetAmt_arr[$row["POID"]]['special_works_amt'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==5) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['gmts_dyeing_qty']/$budgetAmt_arr[$row["POID"]]['gmts_dyeing_amt'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			else if($row['EMBLNAME']==99) // adding process by helal
			{
				if($row['PRODSOURCE']==1)
				{
					$printrate=$budgetAmt_arr[$row['POID']]['others_qty']/$budgetAmt_arr[$row["POID"]]['others_amt'];
				}
				else if($row['PRODSOURCE']==3)
				{
					$printrate=$emblWoRateArr[$row['POID']][$row['EMBLNAME']];
				}
			}
			$emblamt=0;
			$emblamt=$row['PRODQTY']*($printrate/12);
			//echo $row['EMBLNAME'].'='.$row['PRODSOURCE'].'='.$printrate.'<br>';
			

			$emblArr[$row['POID']][$row['EMBLNAME']]+=fn_number_format($emblamt,8,".","");
		}
		else if($row['PRODTYPE']==4)
		{
			$gmtProdArr[$row['POID']][$row['PRODTYPE']]+=$row['PRODQTY'];
		}
	}
	unset($sqlGmtArr);

	// echo "<pre>";
	// print_r($emblArr);
	// echo "</pre>";
	
	$focClaim_arr=array();
	$sql_focClaim="select a.po_break_down_id, a.shiping_mode, a.foc_or_claim, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.shiping_mode=2 and a.foc_or_claim=2 and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id, a.shiping_mode, a.foc_or_claim";
	$sql_focClaim_res=sql_select($sql_focClaim);
	foreach($sql_focClaim_res as $row)
	{
		$focClaim_arr[$row[csf("po_break_down_id")]]=$row[csf("shiping_mode")].'_'.$row[csf("foc_or_claim")].'_'.$row[csf("ex_factory_qnty")];
	}
	unset($sql_focClaim_res);
	
	$sql_ship="select a.po_break_down_id, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id"; //and a.ex_factory_date between '$startDate' and '$endDate'
	//echo $sql_ship;die;
	$sql_ship_res=sql_select($sql_ship); $exfactory_year_arr=array(); $exfactory_buyer_arr=array(); $deliveryPoArr=array();
	foreach($sql_ship_res as $row)
	{
		$deliveryPoArr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
		if($po_arr[$row[csf("po_break_down_id")]]['po_id']==1)
		{
			//Month Summary
			$fiscalMonth=""; $exQtyPcs=0; $exValue=0;
			$exQtyPcs=$row[csf("ex_factory_qnty")];
			$month_buyer=$po_arr[$row[csf("po_break_down_id")]]['month_buyer'];
			$exfactory_year_arr[$row[csf("po_break_down_id")]]['pcs']+=$exQtyPcs;
			$exValue=$exQtyPcs*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
			$exfactory_year_arr[$row[csf("po_break_down_id")]]['val']+=$exValue;
			
			$focClaim=explode('_',$focClaim_arr[$row[csf("po_break_down_id")]]);
			if($focClaim[0]==2  && $focClaim[1]==2)
			{
				$exfactory_year_arr[$row[csf("po_break_down_id")]]['air']+=$focClaim[2];
			}
			
			if($po_arr[$row[csf("po_break_down_id")]]['ship_sta']==2)
			{
				$exfactory_year_arr[$row[csf("po_break_down_id")]]['partial']+=$exValue;
			}
			$shortExcessShip=0;
			if($po_arr[$row[csf("po_break_down_id")]]['ship_sta']==3 && $po_arr[$row[csf("po_break_down_id")]]['apppo']==1)
			{
				$exfactory_year_arr[$row[csf("po_break_down_id")]]['fullship']+=$exValue;
				$shortExcessShip=($exQtyPcs-$po_arr[$row[csf("po_break_down_id")]]['fullship_qty'])*$po_arr[$row[csf("po_break_down_id")]]['po_price'];
				
				if($shortExcessShip>0) $exfactory_year_arr[$row[csf("po_break_down_id")]]['excessship']+=$shortExcessShip;
				if($shortExcessShip<0) $exfactory_year_arr[$row[csf("po_break_down_id")]]['shortship']+=$shortExcessShip;
			}
		}
	}
	//print_r($exfactory_year_arr);
	
	foreach($fullshipedpoArr as $po_id=>$refCloseQty)
	{
		//echo $po_id.'-'.$deliveryPoArr[$po_id];
		if($deliveryPoArr[$po_id]=='')
		{
			if($po_arr[$po_id]['apppo']==1)
			{
				$exfactory_year_arr[$po_id]['shortship']-=$refCloseQty;
			}
		}
	}
	
	$sql_proRealization="select b.invoice_id from com_export_proceed_realization a, com_export_doc_submission_invo b where a.invoice_bill_id=b.doc_submission_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// and b.invoice_id in (4529,4531,4526,4527,4528,4530)
	$sql_proRealization_res=sql_select($sql_proRealization); $invoice_proRealArr=array();
	foreach($sql_proRealization_res as $prrow)
	{
		$invoice_proRealArr[$prrow[csf("invoice_id")]]=1;
	}
	unset($sql_proRealization_res);
	
	//print_r($invoice_proRealArr); die;
	
	$sqlInvoice="select po_breakdown_id, sum(current_invoice_value) as inv_val from com_export_invoice_ship_dtls where status_active=1 and is_deleted=0 and current_invoice_value>0  group by po_breakdown_id";// and po_breakdown_id=42205
	$sqlInvoice_res=sql_select($sqlInvoice); $invoice_year_arr=array(); $poInvArr=array();
	foreach($sqlInvoice_res as $irow)
	{
		if($po_dtls_arr[$irow[csf("po_breakdown_id")]]['shiping_status']==2 || $po_dtls_arr[$irow[csf("po_breakdown_id")]]['shiping_status']==3)
		{
			$poInvArr[$irow[csf("po_breakdown_id")]]=$irow[csf("po_breakdown_id")];
			$povalue=$invoiceBal=0;
			$povalue=$exfactory_year_arr[$irow[csf("po_breakdown_id")]]['val'];//$po_dtls_arr[$irow[csf("po_breakdown_id")]]['poVal'];
			//echo $povalue.'-'.$irow[csf("inv_val")].'<br>';
			$invoiceBal=$povalue-$irow[csf("inv_val")];
			$invoice_year_arr[$irow[csf("po_breakdown_id")]]+=$invoiceBal;
		}
	}
	unset($sqlInvoice_res);
	
	foreach($po_dtls_arr as $poid=>$st)
	{
		if($st['shiping_status']==2 || $st['shiping_status']==3)
		{
			if($poInvArr[$poid]=="")
			{
				$povalue=$exfactory_year_arr[$poid]['val'];
				$invoice_year_arr[$poid]+=$povalue;
			}
		}
	}
	//die;
	$sqlInvoiceRec="select a.mst_id, a.po_breakdown_id, sum(a.current_invoice_value) as inv_val from com_export_invoice_ship_dtls a, gbl_temp_engine b where a.po_breakdown_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0 and a.current_invoice_value>0 group by a.mst_id, a.po_breakdown_id";// and po_breakdown_id=42205
	$sqlInvoiceRec_res=sql_select($sqlInvoiceRec); $invoiceRec_yearArr=array();
	foreach($sqlInvoiceRec_res as $irow)
	{
		if($po_dtls_arr[$irow[csf("po_breakdown_id")]]['shiping_status']==2 || $po_dtls_arr[$irow[csf("po_breakdown_id")]]['shiping_status']==3)
		{
			if($invoice_proRealArr[$irow[csf("mst_id")]]==1)
				$invoiceRec_yearArr[$irow[csf("po_breakdown_id")]]+=0;
			else
				$invoiceRec_yearArr[$irow[csf("po_breakdown_id")]]+=$irow[csf("inv_val")];
		}
	}
	unset($sqlInvoiceRec_res);
	//print_r($invoiceRec_yearArr); die;
	
	//$sqlClaim="select po_id, base_on_ex_val as claim, air_freight, sea_freight, discount from wo_buyer_claim_mst where status_active=1 and is_deleted=0";
	$sqlClaim="select a.po_id, a.base_on_ex_val as claim, a.air_freight, a.sea_freight, a.discount from wo_buyer_claim_mst a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=5 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0";
	//echo $sqlClaim; die;
	$sqlClaim_res=sql_select($sqlClaim); $claim_year_arr=array();
	foreach($sqlClaim_res as $crow)
	{
		if($po_arr[$crow[csf("po_id")]]['apppo']==1)
		{
			$claim_year_arr[$crow[csf("po_id")]]['claim']+=$crow[csf("claim")];
			$claim_year_arr[$crow[csf("po_id")]]['air']+=$crow[csf("air_freight")];
			$claim_year_arr[$crow[csf("po_id")]]['sea']+=$crow[csf("sea_freight")];
			$claim_year_arr[$crow[csf("po_id")]]['discount']+=$crow[csf("discount")];
		}
	}
	unset($sqlClaim_res);
	
	$pre_cost2_print_button_arr=return_library_array( "select template_name, format_id from lib_report_template where module_id = 2 and report_id = 43 and is_deleted = 0 and status_active=1", "template_name", "format_id");
	list($first_print_button)=explode(',',$pre_cost2_print_button_arr[$company_id]);
	$print_button_action_arr=array(50=>'preCostRpt',51=>'preCostRpt2',52=>'bomRpt',63=>'bomRpt2',156=>'accessories_details',157=>'accessories_details2',158=>'preCostRptWoven',159=>'bomRptWoven',170=>'preCostRpt3',171=>'preCostRpt4',142=>'preCostRptBpkW',192=>'checkListRpt');
	$print_button_action = $print_button_action_arr[$first_print_button];
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (5,6) and ENTRY_FORM=880");
	oci_commit($con);
	disconnect($con);
	//var_dump($exfactory_year_arr);
	$season_sql=sql_select( "select id,season_name from lib_buyer_season where status_active=1 and is_deleted=0 and id=$season_id");	
	ob_start();
	?>
    <div style="width:1400px; margin:0px 5px 5px 15px;">
        <div class="podtls" style="width:1400px; font-size:14px; font-weight:bold; color:Black; background-color:#008040" align="center"><?="PO Details of Buyer by Season wise :". $season_sql[0][csf('season_name')]; ?></div>
            <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1400px; margin-top:5px" rules="all">
                <tr align="center" style="font-weight:bold">
                    <td width="70">Ref No</td>
                    <td width="70">Style No</td>
                    <td width="70">No Of PO</td>
                    <td width="60">Pcs Qty.</td>
                    <td width="40">Avg. Rate</td>
                    <td width="70">PO Value($)</td>
                    <td width="60">Minute</td>
                    <td width="60">Budget Appd ($)</td>
                    <td width="50">Budget Appd %</td>
                    
                    <td width="60">Margin ($)</td>
                    <td width="50">Margin %</td>
                    <td width="50">Mat. Cons %</td>
                    <td width="60">Ex Mat Cost($)</td>
                    <td width="60">Excess Ship ($)</td>
                    <td width="60">Short Ship ($)</td>
                    <td width="60">Penalty($)</td>
                    <td width="60">Actual Margin ($)</td>
                    <td width="50">Actual margin %</td>
                    <td width="60">Ship Pending ($)</td>
                    <td width="60">Input to Ship %</td>
                    <td width="60">Invoice Pending ($)</td>
                    <td width="60">Receivable($)</td>
                    <td>Status</td>
                </tr>
                <?
				$q=1; $i=1;
				$tot_noofpo=0;
				foreach($po_dtls_arr as $poid=>$val)
				{
					$exval=explode("_",$val['month_buyer']);
					$monthName="";
					$buyername=$buyerArr[$exval[0]]; $monthname=date("M-y",strtotime($exval[1]));
					
					$intRef=$styleRef=$poNo=$poQty=$poRate=$poVal=$min=$appval=$matCost=$margin=$marginPer=$excessShip=$shortShip=$exfactory_air=$shipPending=$shiping_status="";
					$intRef=$val['intRef'];
					$styleRef=$val['styleRef'];
					$poNo=$val['po'];
					$poQty=$val['poQty'];
					$poRate=$val['poRate'];
					$poVal=$val['poVal'];
					$min=$val['min'];
					$appval=$val['appval'];
					$matCost=$val['matCost'];
					$margin=$val['margin'];
					$shiping_status=$val['shiping_status'];
					
					$totpo=count($job_arr[$poid]['fobjob']);
					$apppo=count($job_arr[$poid]['job']);
					$approved_per=($appval/$poVal)*100;
					
					$matCostPer=($matCost/$appval)*100;
					$marginPer=$val['marginper'];//($margin/$appval)*100;
					
					$excessShip=$exfactory_year_arr[$poid]['excessship'];
					$shortShip=str_replace("-","",$exfactory_year_arr[$poid]['shortship']);
					$exfactory_air=$exfactory_year_arr[$poid]['air'];
					
					$excessFinishFabCost=0;
					$excessFinishFabCost=$exMatCostP[$poid]*1;
					$excessFinishFabCost=fn_number_format($excessFinishFabCost,8,".","");
					/*if($poid==18908)
					{
					echo "<pre>".$excessFinishFabCost."=".($exMatCostP[$poid]*1)."</pre>";
					}*/
					
					$washAmt=$emblArr[$poid][3]-$budgetAmt_arr[$poid]['wash'];
					$embleAmt=$emblArr[$poid][2]-$budgetAmt_arr[$poid]['emb'];

					// adding process by helal
					$print_amt=$emblArr[$poid][1]-$budgetAmt_arr[$poid]['print_amt'];
					$special_works_amt=$emblArr[$poid][4]-$budgetAmt_arr[$poid]['special_works_amt'];
					$gmts_dyeing_amt=$emblArr[$poid][5]-$budgetAmt_arr[$poid]['gmts_dyeing_amt'];
					$others_amt=$emblArr[$poid][5]-$budgetAmt_arr[$poid]['others_amt'];
					// adding process by helal
					
					//$finPurAmt=$exfinrec_arr[$poid]['finrec']-$budgetAmt_arr[$poid]['purchfin_amt'];
					$finPurAmt=bcsub($exfinrec_arr[$poid]['finrec'],$budgetAmt_arr[$poid]['purchfin_amt']);
					$finPurAmt=($finPurAmt*1);
					/*if($poid==18904)
					{
					echo "<pre>".$finPurAmt."=".$exfinrec_arr[$poid]['finrec']."-".$budgetAmt_arr[$poid]['purchfin_amt']."</pre>";
					}*/
					$trimRecAmt=$extrimsrec_arr[$poid]['trimrec']-$budgetAmt_arr[$poid]['trim'];
					//echo "<pre>".$trimRecAmt."=".$extrimsrec_arr[$poid]['trimrec']."-".$budgetAmt_arr[$poid]['trim']."</pre>";
					//echo $extrimsrec_arr[$poid]['trimrec'].'='.$budgetAmt_arr[$poid]['trim'];
					
					$excessCost=$finPurAmt+$trimRecAmt+$exgeneralAcc_arr[$poid]['generalacciss']+$exsampleorderfinrec_arr[$poid]['samfinrec']+$excessFinishFabCost+$embleAmt+$washAmt+$print_amt+$special_works_amt+$gmts_dyeing_amt+$others_amt; // adding process by helal
					
					$shipPending=$val['pending']+($val['partial']-$exfactory_year_arr[$poid]['partial']);
					
					$panalty=$inputtoShipper=0;
					$panalty=$claim_year_arr[$poid]['claim']+$claim_year_arr[$poid]['air']+$claim_year_arr[$poid]['sea']+$claim_year_arr[$poid]['discount'];
					
					$actualMargin=(($margin+$excessShip)-($excessCost+$shortShip+$panalty));
					$actualMarginPer=($actualMargin/$appval)*100;
					
					$inputtoShipper=($exfactory_year_arr[$poid]['pcs']/$gmtProdArr[$poid][4])*100;
					//echo "<pre>".$inputtoShipper."=(".$exfactory_year_arr[$poid]['pcs']."/".$gmtProdArr[$poid][4].")*.100";
					
					$pendingCiVal=$invoiceRec=0;
					$pendingCiVal=$invoice_year_arr[$poid];
					$invoiceRec=$invoiceRec_yearArr[$poid];
					
					$noofpo=count($actualPoArr[$poid]);
					
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr class="podtls" bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
						<td style="word-break:break-all" title="<?=$poid; ?>"><?=$intRef; ?></td>
						<td style="word-break:break-all" title="<?=$styleRef; ?>"><?=substr($styleRef, 0, 12); ?></td>
						<td style="word-break:break-all"><a href='#report_details' onclick="generate_preCostReport('<?=$company_id; ?>','<?=$val['job_no']; ?>', '<?=$exval[0]; ?>','<?=$styleRef; ?>','<?=change_date_format($val['costing_date']); ?>','<?=$poid; ?>','<?=$val['costing_per']; ?>','<?=$print_button_action;?>');"><?=$noofpo; ?></a></td>
						<td align="right"><?=fn_number_format($poQty); ?></td>
						<td align="right"><?=fn_number_format($poRate,3,'','',0); ?></td>
						<td align="right"><?=fn_number_format($poVal); ?></td>
						<td align="right"><?=fn_number_format($min); ?></td>
						<td align="right"><?=fn_number_format($appval); ?></td>
                        <td align="right"><?=fn_number_format($approved_per,2,'','',0); ?></td>
                        
						<td align="right"><?=fn_number_format($margin); ?></td>
						<td align="right"><?=fn_number_format($marginPer,2,'','',0); ?></td>
                        <td align="right" title="<?=$po_arr[$poid]['amc'].'===('.$matCost.'/'.$appval.')*100';?>"><? //number_format($matCostPer,2,'','',0); 
                        if($style_ref=="") $style_ref=0;
						//	echo $season_buyer_id.'xx';
						 $data_po=$company_id.'__'.$location_id.'__'.$shipStatus.'__'.$orderStatus.'__'.$cbo_status.'__'.$season_id.'__'.$client_id.'__'.$style_ref.'__'.$startDate.'__'.$endDate.'__'.$buyer_id.'__'.$poid;
						  ?>
                         <a href="#report_details" onclick="fncexcesscost('matConsPer_popup','<?=$data_po; ?>','850px');"><? echo fn_number_format($matCostPer,2,'','',0); ?></a>
                         
                        </td>
                        
                        <td align="right" title="<?='Finish-'.$finPurAmt.'; Trim-'.$trimRecAmt.'; G.Acc-'.$exgeneralAcc_arr[$poid]['generalacciss'].'__0; S.Acc-'.$exsampleorderfinrec_arr[$poid]['samfinrec'].'; POID-'.$poid.'; F.Mfg-'.$excessFinishFabCost.'; Embl-'.$embleAmt.'; Wash-'.$washAmt.'; Print-'.$print_amt.'; Sp-'.$special_works_amt.'; G.Dye-'.$gmts_dyeing_amt.'; Other-'.$others_amt; ?>">
                        
                        
                        <a href="#report_details" onclick="fncexcesscost('excesscost_popup','<?=$finPurAmt.'__'.$trimRecAmt.'__'.$exgeneralAcc_arr[$poid]['generalacciss'].'__0__'.$exsampleorderfinrec_arr[$poid]['samfinrec'].'__'.$poid.'__'.$excessFinishFabCost.'__'.$embleAmt.'__'.$washAmt.'__'.$print_amt.'__'.$special_works_amt.'__'.$gmts_dyeing_amt.'__'.$others_amt; ?>','1300px');"><?=fn_number_format($excessCost); ?></a></td>
                        
						<td align="right"><?=fn_number_format($excessShip); ?></td>
						<td align="right"><?=fn_number_format($shortShip); ?></td>
						<td align="right"><a href="#report_details" onclick="fncpanalty('panalty_popup','<?=$claim_year_arr[$poid]['claim'].'__'.$claim_year_arr[$poid]['air'].'__'.$claim_year_arr[$poid]['sea'].'__'.$claim_year_arr[$poid]['discount'].'__'.$poid; ?>','550px');"><?=fn_number_format($panalty); ?></a></td>
                        <td align="right"><?=fn_number_format($actualMargin); ?></td>
                        <td align="right"><?=fn_number_format($actualMarginPer,2,'','',0); ?></td>
                        
						<td align="right"><?=fn_number_format($shipPending); ?></td>
                        <td align="right" title="<?='Ship='.$exfactory_year_arr[$poid]['pcs'].'/ Input='.$gmtProdArr[$poid][4]; ?>">
                        	<a href="#report_details" onclick="inputToShip('input_to_ship','<?=$buyer_id; ?>','<?=$season_id; ?>','<?=$poid;?>');">
                        		<? echo fn_number_format($inputtoShipper); ?>
                        	</a>	
                        </td>
                        <td align="right"><?=fn_number_format($pendingCiVal); ?></td>
                        <td align="right">
                        	<a href="#report_details" onclick="inputToShip('receiveble_break_down','<?=$buyer_id; ?>','<?=$season_id; ?>','<?=$poid;?>');">
                        		<? echo fn_number_format($invoiceRec); ?>
                        	</a>
                        </td>
                        <?
						if($isactualpoarr[$poid]!="") {
						?>
						<td style="word-break:break-all; background-color:red"><a href="##" onClick="generate_poremarkspopup('<?=$poid; ?>','podetails_popup');"> <?=substr($shipment_status[$shiping_status], 0, 15); ?></a></td>
                        <? } else { ?>
                        <td style="word-break:break-all"><?=substr($shipment_status[$shiping_status], 0, 15); ?></td>
                        <? } ?>
					</tr>
					<?
					$i++;
					$gpoQty+=$poQty;
					$gpoVal+=$poVal;
					$gmin+=$min;
					$gappval+=$appval;
					$gmargin+=$margin;
					$gexcessCost+=$excessCost;
					$gexcessShip+=$excessShip;
					$gshortShip+=$shortShip;
					$gPanalty+=$panalty;
					$gactualMargin+=$actualMargin;
					$gshipPending+=$shipPending;
					$gPendingCiVal+=$pendingCiVal;
					$gInvRecVal+=$invoiceRec;
					$tot_noofpo+=$noofpo;
				}
				?>
                <tr align="center" style="font-weight:bold; background-color:#CCC">
                	<td colspan="2"><span id="podtlstotal" class="adl-signs" onClick="yearT(this.id,'.podtls')" style="background-color:#2ABF00">+</span>&nbsp;&nbsp;Po Details Total:</td>
                    <td align="right"><?=fn_number_format($tot_noofpo); ?></td>
                    <td align="right"><?=fn_number_format($gpoQty); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><?=fn_number_format($gpoVal); ?></td>
                    <td align="right"><?=fn_number_format($gmin); ?></td>
                    <td align="right"><?=fn_number_format($gappval); ?></td>
                    <td>&nbsp;</td>
                    
                    <td align="right"><?=fn_number_format($gmargin); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><?=fn_number_format($gexcessCost); ?></td>
                    <td align="right"><?=fn_number_format($gexcessShip); ?></td>
                    <td align="right"><?=fn_number_format($gshortShip); ?></td>
                    <td align="right"><?=fn_number_format($gPanalty); ?></td>
                    <td align="right"><?=fn_number_format($gactualMargin); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><?=fn_number_format($gshipPending); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><?=fn_number_format($gPendingCiVal); ?></td>
                    <td align="right"><?=fn_number_format($gInvRecVal); ?></td>
                    <td>&nbsp;</td>
                </tr>
          </table>
      </div>
    <?
	exit();
}

if($action=="excesscost_popup")
{
	echo load_html_head_contents("Excess Mat. Cost", "../../../../", 1, 1,$unicode,'','');
	//echo $poid.'=';//die;
	//echo $data;
	$expData=explode('__',$data);
	//print_r($expData);
	$poid=$expData[5];
	//print_r($expData);
	$fab_link=""; $fab_endlink=""; $acc_link=""; $acc_endlink=""; $gacc_link=""; $gacc_endlink=""; $mfg_link=""; $mfg_endlink="";  $jobnref="";
	if($poid!=0)
	{
		$fab_link='<a href="#report_details" onClick="fnc_dtlslist('.$poid.',0);">';
		$fab_endlink='</a>';
		
		$acc_link='<a href="#report_details" onClick="fnc_dtlslist('.$poid.',1);">';
		$acc_endlink='</a>';
		
		$gacc_link='<a href="#report_details" onClick="fnc_dtlslist('.$poid.',2);">';
		$gacc_endlink='</a>';
		
		$mfg_link='<a href="#report_details" onClick="fnc_dtlslist('.$poid.',3);">';
		$mfg_endlink='</a>';
		
		$sqlpo="select b.job_no_mst AS JOB_NO, b.grouping as GROUPING from wo_po_break_down b where b.is_deleted=0 and b.status_active=1 and b.id='$poid' group by b.job_no_mst, b.grouping";
		$sqlpoRes = sql_select($sqlpo);
		foreach($sqlpoRes as $row)
		{
			$jobnref='For Job No : '.$row["JOB_NO"].'; IR No : '.$row["GROUPING"];
		}
		unset($sqlpoRes);
		
		$reqQtyAmtArr=array();
		$sqlBomAmt="select a.job_id, a.po_id, a.greypurch_amt, a.finpurch_amt, a.yarn_amt, a.conv_amt, a.trim_amt, a.emb_qty, a.emb_amt, a.wash_qty, a.wash_amt,a.print_qty as PRINT_QTY,a.print_amt AS PRINT_AMT,a.special_works_qty as SPECIAL_WORKS_QTY,a.special_works_amt as SPECIAL_WORKS_AMT,a.gmts_dyeing_qty as GMTS_DYEING_QTY,a.gmts_dyeing_amt as GMTS_DYEING_AMT,a.others_qty as OTHERS_QTY,a.others_amt AS OTHERS_AMT from bom_process a where a.po_id=$poid and a.status_active=1 and a.is_deleted=0";
		$sqlBomAmtRes=sql_select($sqlBomAmt);
		foreach($sqlBomAmtRes as $row)
		{
			$reqQtyAmtArr[1]['qty']+=$row["PRINT_QTY"];
			$reqQtyAmtArr[1]['amt']+=$row["PRINT_AMT"];

			$reqQtyAmtArr[2]['qty']+=$row[csf("emb_qty")];
			$reqQtyAmtArr[2]['amt']+=$row[csf("emb_amt")];

			$reqQtyAmtArr[3]['qty']+=$row[csf("wash_qty")];
			$reqQtyAmtArr[3]['amt']+=$row[csf("wash_amt")];

			$reqQtyAmtArr[4]['qty']+=$row["SPECIAL_WORKS_QTY"];
			$reqQtyAmtArr[4]['amt']+=$row["SPECIAL_WORKS_AMT"];

			$reqQtyAmtArr[5]['qty']+=$row["GMTS_DYEING_QTY"];
			$reqQtyAmtArr[5]['amt']+=$row["GMTS_DYEING_AMT"];

			$reqQtyAmtArr[99]['qty']+=$row["OTHERS_QTY"];
			$reqQtyAmtArr[99]['amt']+=$row["OTHERS_AMT"];
		}
		unset($sqlBomAmtRes);
		
		$woSql="select po_break_down_id as POID, rate as RATE, emblishment_name as EMBLNAME from wo_booking_dtls where booking_type=6 and emblishment_name in (1,2,3) and status_active=1 and is_deleted=0 and po_break_down_id='$poid'";
		$woSqlArr=sql_select($woSql);
		$emblWoRateArr=array();
		foreach($woSqlArr as $row)
		{
			$emblWoRateArr[$row['EMBLNAME']]=$row['RATE'];
		}
		unset($woSqlArr);
		
		$sqlGmt="select po_break_down_id as POID, production_source as PRODSOURCE, embel_name as EMBLNAME, production_quantity as PRODQTY from pro_garments_production_mst where production_type=3 and embel_name in (1,2,3) and status_active=1 and is_deleted=0 and po_break_down_id='$poid'";
		$sqlGmtArr=sql_select($sqlGmt);
		$emblArr=array();
		foreach($sqlGmtArr as $row)
		{
			$printrate=0;
			if($row['PRODSOURCE']==1)
			{
				$printrate=$reqQtyAmtArr[$row['EMBLNAME']]['amt']/$reqQtyAmtArr[$row['EMBLNAME']]['qty'];
			}
			else if($row['PRODSOURCE']==3)
			{
				$printrate=$emblWoRateArr[$row['EMBLNAME']];
			}
			$emblamt=0;
			$emblamt=$row['PRODQTY']*($printrate/12);
			//echo $row['PRODSOURCE'].'='.$printrate.'<br>';
			
			$emblArr[$row['EMBLNAME']]+=fn_number_format($emblamt,8,".","");
		}
		unset($sqlGmtArr);
	}
	$printCost=$emblArr[1]-$reqQtyAmtArr[1]['amt'];
	$emblCost=$emblArr[2]-$reqQtyAmtArr[2]['amt'];
	$washCost=$emblArr[3]-$reqQtyAmtArr[3]['amt'];
	$otherCost=($emblArr[4]+$emblArr[5]+$emblArr[99])-($reqQtyAmtArr[4]['amt']+$reqQtyAmtArr[5]['amt']+$reqQtyAmtArr[99]['amt']);

	if(empty($poid))
	{
		$emblCost=$expData[7];
		$washCost=$expData[8];
		$printCost=$expData[9];
		$otherCost=($expData[10]+$expData[11]+$expData[12]);
	}
	
	if($expData[1]=="") $expData[1]=0;
	if($expData[2]=="") $expData[2]=0;

	$total_cost=$expData[0]+$expData[1]+$expData[2]+$expData[3]+$expData[4]+$expData[6]+$printCost+$emblCost+$washCost+$otherCost;
	
	?>
    <script>
	function fnc_dtlslist(poid,type)
	{
		$('#search_div').html('');
		if(type==0 || type==1)
		{
			show_list_view (poid+'__'+type, 'exmat_details_list_view', 'search_div', 'business_analysis_report_controller', '');
		}
		else if(type==2)
		{
			show_list_view (poid+'__'+type, 'exmatgacc_details_list_view', 'search_div', 'business_analysis_report_controller', '');
		}
		else if(type==3)
		{
			show_list_view (poid+'__'+type, 'exmfg_details_list_view', 'search_div', 'business_analysis_report_controller', '');
		}
	}	
	</script>
	<fieldset style="width:1045px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Excess Mat. Cost  <?=$jobnref; ?></th>
                    </tr>
                    <tr>
                        <th width="100">Purchase Fabric Cost</th>
                        <th width="110">Acc. Cost</th>
                        <th width="110">General Acc. Cost</th>
                        <th width="80">Dev. Sample Cost</th>
                        <th width="80">Production Sample Cost</th>
                        <th width="100">Fabric mfg cost</th>
                        <th width="100">Printing Cost</th>
                        <th width="90">Embroidery Cost </th>
                        <th width="90" >Washing Cost</th>
                        <th width="75" title="SPECIAL_WORKS + GMTS_DYEING + OTHERS">Other Cost</th>
                        <th >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="right"><?=$fab_link; ?><?=fn_number_format($expData[0]*1); ?><?=$fab_endlink; ?></td>
                        <td align="right"><?=$acc_link; ?><?=fn_number_format($expData[1]*1); ?><?=$acc_endlink; ?></td>
                        <td align="right"><?=$gacc_link; ?><?=fn_number_format($expData[2]*1); ?><?=$gacc_endlink; ?></td>
                        <td align="right"><?=fn_number_format($expData[3]); ?></td> 
                        <td align="right"><?=fn_number_format($expData[4]); ?></td>
                        <td align="right" title="<?=$expData[6]?>"><?=$mfg_link; ?><?=fn_number_format($expData[6]); ?><?=$mfg_endlink; ?></td> 
                        <td align="right" title="<?="BOM Amt=".$reqQtyAmtArr[1]['amt'].'-Actual Amt='.$emblArr[1]; ?> "><?=fn_number_format($printCost,2,'','',0); ?></td>
                        <td align="right" title="<?="BOM Amt=".$reqQtyAmtArr[2]['amt'].'-Actual Amt='.$emblArr[2]; ?> "><?=fn_number_format($emblCost,2,'','',0); ?></td>
                        <td align="right" title="<?="BOM Amt=".$reqQtyAmtArr[3]['amt'].'-Actual Amt='.$emblArr[3]; ?> "><?=fn_number_format($washCost,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($otherCost)?></td>
                        <td align="right"><?=fn_number_format($total_cost);?></td>
                    </tr>
                </tbody>
            </table>
        </div> 
	</fieldset>
    <div style="margin-top:15px" id="search_div"></div>
	<?
	exit();
}

if($action=="exmat_details_list_view")
{
	echo load_html_head_contents("Excess Mat. Cost", "../../../../", 1, 1,$unicode,'','');
	//echo $poid.'=';//die;
	$expData=explode('__',$data);
	//print_r($expData);
	$poid=trim($expData[0]);
	$type=$expData[1];
	if($poid!=0)
	{
		$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id='$poid'";
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes);
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
		$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1  $jobidCondition";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		}
		unset($gmtsitemRatioSqlRes);
		if($type==0)
		{
			$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
			//echo $sqlContrast; die;
			$sqlContrastRes = sql_select($sqlContrast);
			$sqlContrastArr=array();
			foreach($sqlContrastRes as $row)
			{
				$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
			}
			unset($sqlContrastRes);
			
			//Stripe Details
			$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
			//echo $sqlStripe; die;
			$sqlStripeRes = sql_select($sqlStripe);
			$sqlStripeArr=array();
			foreach($sqlStripeRes as $row)
			{
				$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
				$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
			}
			unset($sqlStripeRes);
			
			$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.composition as COMPOSITION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.dia_width as DIA_WIDTH, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
	from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$poid' and a.fabric_source=2 $jobidCond";
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
				$fabcolorArr=array();
				if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
				{
					foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
					{
						$fabcolorArr[$row['ID']][$row['COLOR_NUMBER_ID']][$fabcolor]=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
					}
				}
				
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				$costingPer=$costingPerArr[$row['JOB_ID']];
				$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
				
				//$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
				//$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
				
				$finAmt=$finReq*$row['RATE'];
				//$greyAmt=$greyReq*$row['RATE'];
				
				//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
				
				/*if($row['FABRIC_SOURCE']==1)
				{
					$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
					$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_amt']+=$finAmt;
					$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_amt']+=$greyAmt;
				}
				else */
				$fullfab=$row['CONSTRUCTION'].', '.$row['COMPOSITION'].', '.$row['GSM_WEIGHT'].', '.$row['DIA_WIDTH'];
				if($row['FABRIC_SOURCE']==2)
				{
					if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
					{
						foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
						{
							$cons=0;
							$cons=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
							$finReq=($planQty/$itemRatio)*($cons/$costingPer);
							$finAmt=$finReq*$row['RATE'];
							
							$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
							//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
							$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
							//$reqQtyAmtArr[$row['POID']]['purchgrey_amt']+=$greyAmt;
						}
					}
					else if ($sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]!="" && $row['COLOR_SIZE_SENSITIVE']==3)
					{
						$cons=0;
						$fabcolor=$sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']];
						$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
						$finAmt=$finReq*$row['RATE'];
						
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
					}
					else
					{
						$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
						$finAmt=$finReq*$row['RATE'];
						
						$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_qty']+=$finReq;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
						$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_amt']+=$finAmt;
					}
				}
			}
			unset($sqlfabRes);
		}
		else if ($type==1)
		{
			//Trims Details
			$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
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
							//$consTotAmt+=$consTotQty*$row['RATE'];
							$consTotAmt+=$consTotQnty*$row['RATE'];
						}
					}
				}
				
				//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
				$reqQtyAmtArr[$row['POID']][$row['TRIM_GROUP']][$row['CONS_UOM']]['trimqty']+=$consQnty;
				$reqQtyAmtArr[$row['POID']][$row['TRIM_GROUP']][$row['CONS_UOM']]['trimtotqty']+=$consTotQnty;
				
				$reqQtyAmtArr[$row['POID']][$row['TRIM_GROUP']][$row['CONS_UOM']]['trimamt']+=$consAmt;
				$reqQtyAmtArr[$row['POID']][$row['TRIM_GROUP']][$row['CONS_UOM']]['trimtotamt']+=$consTotAmt;
			}
			unset($sqlTrimRes); 
			//print_r($reqQtyAmtArr); die;
		}
		//unset($reqQtyAmtArr);
		if($type==0)//Fabric
		{
			$dataArrayTrans = sql_select("select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, b.batch_id as BATCHID, c.po_breakdown_id as POID, c.entry_form as ENTRY_FORM, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2 and c.po_breakdown_id='$poid'");
			$recDataRetArr=array(); $batchCheckArr=array(); $batchIDArr=array(); $fabProdPurchBatchArr=array();
			foreach($dataArrayTrans as $row)
			{
				$amt=0;
				if($row['ENTRY_FORM']==37)
				{
					$amt=$row['QUANTITY']*($row['RATE']/82);
					//echo $row['QUANTITY'].'='.($row['RATE']/82).'='.$amt.'<br>';
					$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_qty']+=$row['QUANTITY'];
					$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinrec_amt']+=$amt;
					
					$recDataRetArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
				}
				else
				{
					$batchCheckArr[$row['PROD_ID']][$row['BATCHID']]=1;
				}
				array_push($batchIDArr,$row['BATCHID']);
				$fabProdPurchBatchArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['BATCHID']]=1;
			}
			unset($dataArrayTrans);
			//print_r($batchCheckArr);//die;
			
			$sqlRet="select a.received_id as RECEIVED_ID, b.prod_id as PROD_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, b.cons_uom as UOM from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and a.entry_form in (46) and c.entry_form in (46) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$poid' ";
			$sqlRetArr=sql_select($sqlRet); 
			foreach($sqlRetArr as $row)
			{
				$recRate=$amt=0;
				$recRate=$recDataRetArr[$row['RECEIVED_ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']*1;
				//echo $recRate;
				if(($recRate)>0)
				{
					$amt=$row['QUANTITY']*$recRate;
					$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinRet_qty']+=$row['QUANTITY'];
					$reqQtyAmtArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinRet_amt']+=$amt;
				}
			}
			unset($sqlRetArr);
			//print_r($reqQtyAmtArr);
			
			$batchID_cond=where_con_using_array($batchIDArr,0,"id");
			$batchEntryArr=return_library_array( "select id, entry_form from pro_batch_create_mst where 1=1 ", "id", "entry_form");//$batchID_cond
			
			$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, b.batch_id as BATCHID, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$poid'";
			$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();
			foreach($sqlTransArr as $row)
			{
				$transVal=$amt=0;
				$transVal=$row['TRANSFER_VALUE']/82;
				//echo $recRate;
				//echo $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']].'='.$row['FROM_PROD_ID'].'='.$row['BATCHID'].'='.$batchEntryArr[$row['BATCHID']].'<br>';
				if($row['TRANS_TYPE']==5 && $batchCheckArr[$row['FROM_PROD_ID']][$row['BATCHID']]!=1 && $batchEntryArr[$row['BATCHID']]==37)//trans in// 
				{
					//$amt=$row['QUANTITY']*$transRate;
					$reqQtyAmtArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinTin_qty']+=$row['QUANTITY'];
					$reqQtyAmtArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinTin_amt']+=$transVal;
					$reqQtyAmtArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinTin_ref'].=$row['FROM_ORDER_ID'].',';
					array_push($trnsPoIdArr,$row['FROM_ORDER_ID']);
				}
				else if($row['TRANS_TYPE']==6)//trans out
				{
					//$amt=$row['QUANTITY']*$transRate;
					if($fabProdPurchBatchArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['BATCHID']]==1)
					{
						$reqQtyAmtArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinTout_qty']+=$row['QUANTITY'];
						$reqQtyAmtArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinTout_amt']+=$transVal;
						$reqQtyAmtArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']][$row['UOM']]['purchfinTout_ref'].=$row['TO_ORDER_ID'].',';
						array_push($trnsPoIdArr,$row['TO_ORDER_ID']);
					}
				}
				//array_push($batchIDArr,$row['BATCHID']);
			}
			unset($sqlTransArr);
			
			$fabpoid_cond=where_con_using_array($trnsPoIdArr,0,"id");
			$poRefArr=return_library_array( "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond", "id", "grouping");
			
			//echo "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond";
		?>
        <div style="width:1270px;" align="center">
            <table cellpadding="0" width="1270px" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="19">Purchase Fabric Cost</th>
                    </tr>
                    <tr>
                        <th colspan="6">Budget</th>
                        <th colspan="3">Actual Purchase</th>
                        <th colspan="4">Transfer In</th>
                        <th colspan="4">Transfer out</th>
                        <th colspan="2">Difference</th>
                    </tr>
                    <tr>
                        <th width="210">Item</th>
                        <th width="110">F. Color</th>
                        <th width="55">Qty.</th>
                        <th width="30">Unit</th>
                        <th width="40">Avg Rate</th>
                        <th width="60">Amount</th>
                        
                        <th width="55">Qty.</th>
                        <th width="40">Avg Rate</th>
                        <th width="60">Amount</th>
                        
                        <th width="70">Ref No</th>
                        <th width="55">Qty.</th>
                        <th width="40">Avg Rate</th>
                        <th width="60">Amount</th>
                        
                        <th width="70">Ref No</th>
                        <th width="55">Qty.</th>
                        <th width="40">Avg Rate</th>
                        <th width="60">Amount</th>
                        
                        <th width="55">Qty.</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				$colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
				foreach($reqQtyAmtArr[$poid] as $fabric=>$fabdata)
				{
					foreach($fabdata as $fabcolorid=>$fabcolordata)
					{
						foreach($fabcolordata as $uom=>$uomdata)
						{
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$bomfabAvgRate=0; $actPurQty=$actPurAmt=$actualAvgRate=0;
							$bomfabAvgRate=$uomdata['purchfin_amt']/$uomdata['purchfin_qty'];
							
							$actPurQty=$uomdata['purchfinrec_qty']-$uomdata['purchfinRet_qty'];
							$actPurAmt=$uomdata['purchfinrec_amt']-$uomdata['purchfinRet_amt'];
							$actualAvgRate=$actPurAmt/$actPurQty;
							
							$transInAvgRate=$transOutAvgRate=$difQty=$difAmt=0;
							$transInAvgRate=$uomdata['purchfinTin_amt']/$uomdata['purchfinTin_qty'];
							$transOutAvgRate=$uomdata['purchfinTout_amt']/$uomdata['purchfinTout_qty'];
							
							$difQty=($actPurQty+$uomdata['purchfinTin_qty'])-($uomdata['purchfinTout_qty']+$uomdata['purchfin_qty']);
							$difAmt=($actPurAmt+$uomdata['purchfinTin_amt'])-($uomdata['purchfinTout_amt']+$uomdata['purchfin_amt']);
							
							$transinrefId=$transinref="";
							$transinrefId=array_filter(array_unique(explode(",",$uomdata['purchfinTin_ref'])));
							
							foreach($transinrefId as $topoid)
							{
								if($transinref=="") $transinref=$poRefArr[$topoid]; else $transinref.=", ".$poRefArr[$topoid];
							}
							
							$transoutrefId=$transoutref="";
							$transoutrefId=array_filter(array_unique(explode(",",$uomdata['purchfinTout_ref'])));
							
							foreach($transoutrefId as $frompoid)
							{
								if($transoutref=="") $transoutref=$poRefArr[$frompoid]; else $transoutref.=", ".$poRefArr[$frompoid];
							}
							?>
                            <tr bgcolor="<?=$bgcolor; ?>" >
                                <td width="210" style="word-break:break-all" title="<?=$fabric; ?>"><?=substr($fabric, 0, 40); ?></td>
                                <td width="110" style="word-break:break-all" title="<?=$colorArr[$fabcolorid]; ?>"><?=substr($colorArr[$fabcolorid], 0, 20); ?></td>
                                <td width="55" align="right"><?=fn_number_format($uomdata['purchfin_qty'],2,'','',0); ?></td>
                                <td width="30"><?=$unit_of_measurement[$uom]; ?></td>
                                <td width="40" align="right"><?=fn_number_format($bomfabAvgRate,2,'','',0); ?></td>
                                <td width="60" align="right"><?=fn_number_format($uomdata['purchfin_amt'],2,'','',0); ?></td>
                                
                                <td width="55" align="right"><?=fn_number_format($actPurQty,2,'','',0); ?></td>
                                <td width="40" align="right"><?=fn_number_format($actualAvgRate,2,'','',0); ?></td>
                                <td width="60" align="right"><?=fn_number_format($actPurAmt,2,'','',0); ?></td>
                                
                                <td width="70" style="word-break:break-all"><?=$transinref; ?></td>
                                <td width="55" align="right"><?=fn_number_format($uomdata['purchfinTin_qty'],2,'','',0); ?></td>
                                <td width="40" align="right"><?=fn_number_format($transInAvgRate,2,'','',0); ?></td>
                                <td width="60" align="right"><?=fn_number_format($uomdata['purchfinTin_amt'],2,'','',0); ?></td>
                                
                                <td width="70" style="word-break:break-all"><?=$transoutref; ?></td>
                                <td width="55" align="right"><?=fn_number_format($uomdata['purchfinTout_qty'],2,'','',0); ?></td>
                                <td width="40" align="right"><?=fn_number_format($transOutAvgRate,2,'','',0); ?></td>
                                <td width="60" align="right"><?=fn_number_format($uomdata['purchfinTout_amt'],2,'','',0); ?></td>
                                
                                <td width="55" align="right"><?=fn_number_format($difQty,2,'','',0); ?></td>
                                <td align="right"><?=fn_number_format($difAmt,2,'','',0); ?></td>
                            </tr>
                            <?
							$i++;
							
							$gBomQty+=$uomdata['purchfin_qty'];
							$gBomAmt+=$uomdata['purchfin_amt'];
							$gActualQty+=$actPurQty;
							$gActualAmt+=$actPurAmt;
							
							$gTransInQty+=$uomdata['purchfinTin_qty'];
							$gTransInAmt+=$uomdata['purchfinTin_amt'];
							$gTransOutQty+=$uomdata['purchfinTout_qty'];
							$gTransOutAmt+=$uomdata['purchfinTout_amt'];
							
							$gDiffQty+=$difQty;
							$gDiffAmt+=$difAmt;
						}
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="210">&nbsp;</td>
                        <td width="110">Total : </td>
                        <td width="55" align="right"><?=fn_number_format($gBomQty,2,'','',0); ?></td>
                        <td width="30">&nbsp;</td>
                        <td width="40">&nbsp;</td>
                        <td width="60" align="right"><?=fn_number_format($gBomAmt,2,'','',0); ?></td>
                        
                        <td width="55" align="right"><?=fn_number_format($gActualQty,2,'','',0); ?></td>
                        <td width="40">&nbsp;</td>
                        <td width="60" align="right"><?=fn_number_format($gActualAmt,2,'','',0); ?></td>
                        
                        <td width="70">&nbsp;</td>

                        <td width="55" align="right"><?=fn_number_format($gTransInQty,2,'','',0); ?></td>
                        <td width="40">&nbsp;</td>
                        <td width="60" align="right"><?=fn_number_format($gTransInAmt,2,'','',0); ?></td>
                        
                        <td width="70">&nbsp;</td>
                        <td width="55" align="right"><?=fn_number_format($gTransOutQty,2,'','',0); ?></td>
                        <td width="40">&nbsp;</td>
                        <td width="60" align="right"><?=fn_number_format($gTransOutAmt,2,'','',0); ?></td>
                        
                        <td width="55" align="right"><?=fn_number_format($gDiffQty,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($gDiffAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <?
		}
		else if($type==1) //trims
		{
			$dataArrayTrans = sql_select("select a.id as ID, a.currency_id as CURRENCY_ID, b.rate as RATE, c.po_breakdown_id as POID, c.entry_form as ENTRY_FORM, 
 c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.receive_basis in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (24) and c.entry_form in (24) and d.item_category_id=4 and c.po_breakdown_id='$poid'");
			
			foreach($dataArrayTrans as $row)
			{
				$amt=$row['QUANTITY']*$row['RATE'];
				$reqQtyAmtArr[$row['POID']][$row['ITEM_GROUP_ID']][$row['UOM']]['trimrec_qty']+=$row['QUANTITY'];
				$reqQtyAmtArr[$row['POID']][$row['ITEM_GROUP_ID']][$row['UOM']]['trimrec_amt']+=$amt;
			}
			unset($dataArrayTrans);
			//print_r($recDataRetArr[52550][54013]); die;
			
			$sqlRet="select a.received_id as RECEIVED_ID, b.prod_id as PROD_ID, b.rate as RATE, c.po_breakdown_id as POID, b.item_group_id as ITEM_GROUP_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS, b.uom as UOM from inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form in (49) and c.entry_form in (49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$poid' ";
			$sqlRetArr=sql_select($sqlRet);
			foreach($sqlRetArr as $row)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				$reqQtyAmtArr[$row['POID']][$row['ITEM_GROUP_ID']][$row['UOM']]['trimRet_qty']+=$row['QUANTITY'];
				$reqQtyAmtArr[$row['POID']][$row['ITEM_GROUP_ID']][$row['UOM']]['trimRet_amt']+=$amt;
			}
			unset($sqlRetArr);
			//print_r($reqQtyAmtArr);
			
			$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.item_group as ITEMGROUP, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, c.po_breakdown_id as POID, b.transfer_qnty as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=4 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (78) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$poid'";
			$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array();
			foreach($sqlTransArr as $row)
			{
				$transVal=$amt=0;
				$transVal=$row['RATE']/82;
				//echo $recRate;
				if($row['TRANS_TYPE']==5)//trans in
				{
					$amt=$row['QUANTITY']*$transVal;
					$reqQtyAmtArr[$row['TO_ORDER_ID']][$row['ITEMGROUP']][$row['UOM']]['trimTin_qty']+=$row['QUANTITY'];
					$reqQtyAmtArr[$row['TO_ORDER_ID']][$row['ITEMGROUP']][$row['UOM']]['trimTin_amt']+=$amt;
					$reqQtyAmtArr[$row['TO_ORDER_ID']][$row['ITEMGROUP']][$row['UOM']]['trimTin_ref'].=$row['FROM_ORDER_ID'].',';
					array_push($trnsPoIdArr,$row['FROM_ORDER_ID']);
				}
				else if($row['TRANS_TYPE']==6)//trans out
				{
					$amt=$row['QUANTITY']*$transVal;
					$reqQtyAmtArr[$row['FROM_ORDER_ID']][$row['ITEMGROUP']][$row['UOM']]['trimTout_qty']+=$row['QUANTITY'];
					$reqQtyAmtArr[$row['FROM_ORDER_ID']][$row['ITEMGROUP']][$row['UOM']]['trimTout_amt']+=$amt;
					$reqQtyAmtArr[$row['FROM_ORDER_ID']][$row['ITEMGROUP']][$row['UOM']]['trimTout_ref'].=$row['TO_ORDER_ID'].',';
					array_push($trnsPoIdArr,$row['TO_ORDER_ID']);
				}
			}
			unset($sqlTransArr);
			
			$fabpoid_cond=where_con_using_array($trnsPoIdArr,0,"id");
			$poRefArr=return_library_array( "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond", "id", "grouping");
			//echo "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond";
		?>
        <div style="width:1230px;" align="center">
            <table cellpadding="0" width="1230px" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="18">Accessories Cost Details</th>
                    </tr>
                    <tr>
                        <th colspan="5">Budget</th>
                        <th colspan="3">Actual Purchase</th>
                        <th colspan="4">Transfer In</th>
                        <th colspan="4">Transfer out</th>
                        <th colspan="2">Difference</th>
                    </tr>
                    <tr>
                        <th width="150">Item Group</th>
                        <th width="60">Qty.</th>
                        <th width="40">Unit</th>
                        <th width="50">Avg Rate</th>
                        <th width="70">Amount</th>
                        
                        <th width="60">Qty.</th>
                        <th width="50">Avg Rate</th>
                        <th width="70">Amount</th>
                        
                        <th width="80">Ref No</th>
                        <th width="60">Qty.</th>
                        <th width="50">Avg Rate</th>
                        <th width="70">Amount</th>
                        
                        <th width="80">Ref No</th>
                        <th width="60">Qty.</th>
                        <th width="50">Avg Rate</th>
                        <th width="70">Amount</th>
                        
                        <th width="60">Qty.</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				$itemGroupArr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");
				foreach($reqQtyAmtArr[$poid] as $itemgroup=>$itemgroupdata)
				{
					foreach($itemgroupdata as $uom=>$uomdata)
					{
						if($itemgroup!="")
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$bomTrimAvgRate=0; $actRecQty=$actRecAmt=$actualAvgRate=0;
							$bomTrimAvgRate=$uomdata['trimtotamt']/$uomdata['trimtotqty'];
							
							$actRecQty=$uomdata['trimrec_qty']-$uomdata['trimRet_qty'];
							$actRecAmt=$uomdata['trimrec_amt']-$uomdata['trimRet_amt'];
							$actualAvgRate=$actRecAmt/$actRecQty;
							
							$transInAvgRate=$transOutAvgRate=$difQty=$difAmt=0;
							$transInAvgRate=$uomdata['trimTin_amt']/$uomdata['trimTin_qty'];
							$transOutAvgRate=$uomdata['trimTout_amt']/$uomdata['trimTout_qty'];
							
							$difQty=($actRecQty+$uomdata['trimTin_qty'])-($uomdata['trimTout_qty']+$uomdata['trimtotqty']);
							$difAmt=($actRecAmt+$uomdata['trimTin_amt'])-($uomdata['trimTout_amt']+$uomdata['trimtotamt']);
							
							$transinrefId=$transinref="";
							$transinrefId=array_filter(array_unique(explode(",",$uomdata['trimTin_ref'])));
							
							foreach($transinrefId as $topoid)
							{
								if($transinref=="") $transinref=$poRefArr[$topoid]; else $transinref.=", ".$poRefArr[$topoid];
							}
							
							$transoutrefId=$transoutref="";
							$transoutrefId=array_filter(array_unique(explode(",",$uomdata['trimTout_ref'])));
							
							foreach($transoutrefId as $frompoid)
							{
								if($transoutref=="") $transoutref=$poRefArr[$frompoid]; else $transoutref.=", ".$poRefArr[$frompoid];
							}
							?>
							<tr bgcolor="<?=$bgcolor; ?>" >
								<td width="150" style="word-break:break-all"><?=$itemGroupArr[$itemgroup]; ?></td>
								<td width="60" align="right"><?=fn_number_format($uomdata['trimtotqty'],2,'','',0); ?></td>
								<td width="40"><?=$unit_of_measurement[$uom]; ?></td>
								<td width="50" align="right"><?=fn_number_format($bomTrimAvgRate,2,'','',0); ?></td>
								<td width="70" align="right"><?=fn_number_format($uomdata['trimtotamt'],2,'','',0); ?></td>
								
								<td width="60" align="right"><?=fn_number_format($actRecQty,2,'','',0); ?></td>
								<td width="50" align="right"><?=fn_number_format($actualAvgRate,2,'','',0); ?></td>
								<td width="70" align="right"><?=fn_number_format($actRecAmt,2,'','',0); ?></td>
								
								<td width="80" style="word-break:break-all"><?=$transinref; ?></td>
								<td width="60" align="right"><?=fn_number_format($uomdata['trimTin_qty'],2,'','',0); ?></td>
								<td width="50" align="right"><?=fn_number_format($transInAvgRate,2,'','',0); ?></td>
								<td width="70" align="right"><?=fn_number_format($uomdata['trimTin_amt'],2,'','',0); ?></td>
								
								<td width="80" style="word-break:break-all"><?=$transoutref; ?></td>
								<td width="60" align="right"><?=fn_number_format($uomdata['trimTout_qty'],2,'','',0); ?></td>
								<td width="50" align="right"><?=fn_number_format($transOutAvgRate,2,'','',0); ?></td>
								<td width="70" align="right"><?=fn_number_format($uomdata['trimTout_amt'],2,'','',0); ?></td>
								
								<td width="60" align="right"><?=fn_number_format($difQty,2,'','',0); ?></td>
								<td align="right"><?=fn_number_format($difAmt,2,'','',0); ?></td>
							</tr>
							<?
							$i++;
							
							$gBomQty+=$uomdata['trimtotqty'];
							$gBomAmt+=$uomdata['trimtotamt'];
							$gActualQty+=$actRecQty;
							$gActualAmt+=$actRecAmt;
							
							$gTransInQty+=$uomdata['trimTin_qty'];
							$gTransInAmt+=$uomdata['trimTin_amt'];
							$gTransOutQty+=$uomdata['trimTout_qty'];
							$gTransOutAmt+=$uomdata['trimTout_amt'];
							
							$gDiffQty+=$difQty;
							$gDiffAmt+=$difAmt;
						}
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="150">Total : </td>
                        <td width="60" align="right"><?=fn_number_format($gBomQty,2,'','',0); ?></td>
                        <td width="40">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="70" align="right"><?=fn_number_format($gBomAmt,2,'','',0); ?></td>
                        
                        <td width="60" align="right"><?=fn_number_format($gActualQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td width="70" align="right"><?=fn_number_format($gActualAmt,2,'','',0); ?></td>
                        
                        <td width="80">&nbsp;</td>
                        <td width="60" align="right"><?=fn_number_format($gTransInQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td width="70" align="right"><?=fn_number_format($gTransInAmt,2,'','',0); ?></td>
                        
                        <td width="80">&nbsp;</td>
                        <td width="60" align="right"><?=fn_number_format($gTransOutQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td width="70" align="right"><?=fn_number_format($gTransOutAmt,2,'','',0); ?></td>
                        
                        <td width="60" align="right"><?=fn_number_format($gDiffQty,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($gDiffAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <?
		}
	}
	exit();
}

if($action=="exmatgacc_details_list_view")
{
	echo load_html_head_contents("Excess Mat. Cost", "../../../../", 1, 1,$unicode,'','');
	//echo $poid.'=';//die;
	$expData=explode('__',$data);
	//print_r($expData);
	$poid=trim($expData[0]);
	$type=$expData[1];
	if($poid!=0)
	{
		$generalAccSql="select a.id as ID, b.prod_id as PRODID, b.order_id as POID, b.cons_quantity as QUANTITY, b.cons_rate as RATE, c.item_description as ITEM_DESCRIPTION, c.unit_of_measure as UOM
		 from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form in(21) and b.transaction_type=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id='$poid'";
	
		$generalAccSql_res=sql_select($generalAccSql);
		$exgeneralAcc_arr=array(); $gaccRetArr=array(); $returnIssueIdArr=array();
		foreach($generalAccSql_res as $row)
		{
			array_push($returnIssueIdArr,$row['ID']);
			$amount=$rate=0;
			$rate=$row["RATE"]/82;
			$amount=$row["QUANTITY"]*$rate;
			
			$exgeneralAcc_arr[$row["POID"]][$row["ITEM_DESCRIPTION"]][$row["UOM"]]['gaccqty']+=$row["QUANTITY"];
			$exgeneralAcc_arr[$row["POID"]][$row["ITEM_DESCRIPTION"]][$row["UOM"]]['gaccamt']+=$amount;
			
			$gaccRetArr[$row["ID"]][$row["PRODID"]]=$row["POID"];
		}
		unset($generalAccSql_res);
		
		$issueid_cond=where_con_using_array($returnIssueIdArr,0,"b.issue_id");
		
		$generalAccRet="select b.cons_rate as RATE, b.prod_id as PROD_ID, b.cons_quantity as QUANTITY, b.issue_id as ISSUE_ID, c.item_description as ITEM_DESCRIPTION , c.unit_of_measure as UOM
		 from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form in(27) and b.transaction_type=4 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issueid_cond";
		 
		$generalAccRetArr=sql_select($generalAccRet);
		foreach($generalAccRetArr as $row)
		{
			$po_id=$gaccRetArr[$row["ISSUE_ID"]][$row["PROD_ID"]];
			$amount=$rate=0;
			$rate=$row["RATE"]/82;
			$amount=$row["QUANTITY"]*$rate;
			
			$exgeneralAcc_arr[$po_id][$row["ITEM_DESCRIPTION"]][$row["UOM"]]['gaccretqty']+=$row["QUANTITY"];
			$exgeneralAcc_arr[$po_id][$row["ITEM_DESCRIPTION"]][$row["UOM"]]['gaccretamt']+=$amount;
		}
		unset($generalAccRetArr);
		
		?>
        <div style="width:520px;" align="center">
            <table cellpadding="0" width="520px" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="7">General Acc. Cost Details</th>
                    </tr>
                    <tr>
                        <th width="150">Item Name</th>
                        <th width="70">Issue Qty.</th>
                        <th width="70">Return Qty.</th>
                        <th width="60">Actual Use</th>
                        <th width="70">Unit</th>
                        <th width="50">Avg Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($exgeneralAcc_arr[$poid] as $itemdesc=>$itemgroupdata)
				{
					foreach($itemgroupdata as $uom=>$uomdata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gAccActualUse=$gAccActualAmt=$gAccAvgRate=0;
						$gAccActualUse=$uomdata['gaccqty']-$uomdata['gaccretqty'];
						$gAccActualAmt=$uomdata['gaccamt']-$uomdata['gaccretamt'];
						$gAccAvgRate=$gAccActualAmt/$gAccActualUse;
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
							<td width="150" style="word-break:break-all"><?=$itemdesc; ?></td>
							<td width="70" align="right"><?=fn_number_format($uomdata['gaccqty'],2,'','',0); ?></td>
							<td width="70" align="right"><?=fn_number_format($uomdata['gaccretqty'],2,'','',0); ?></td>
							<td width="60" align="right"><?=fn_number_format($gAccActualUse,2,'','',0); ?></td>
							<td width="70"><?=$unit_of_measurement[$uom]; ?></td>
							
							<td width="50" align="right"><?=fn_number_format($gAccAvgRate,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($gAccActualAmt,2,'','',0); ?></td>
						</tr>
						<?
						$i++;
						
						$grandActualAmt+=$gAccActualAmt;
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="150">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        
                        <td width="50">Total : </td>
                        <td align="right"><?=fn_number_format($grandActualAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <?
	}
}

if($action=="exmfg_details_list_view")
{
	echo load_html_head_contents("Excess Mat. Cost", "../../../../", 1, 1,$unicode,'','');
	//echo $poid.'=';//die;
	$expData=explode('__',$data);
	//print_r($expData);
	$poid=trim($expData[0]);
	$type=$expData[1];
	if($poid!=0)
	{
		$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id='$poid'";
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes);
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
		$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1  $jobidCondition";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		}
		unset($gmtsitemRatioSqlRes);
		
		$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		}
		unset($sqlContrastRes);
		
		//Stripe Details
		$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
		}
		unset($sqlStripeRes);
		
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.lib_yarn_count_deter_id as DETAID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.composition as COMPOSITION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.dia_width as DIA_WIDTH, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
	from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$poid' and a.fabric_source=1 ";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $fabDescArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$fabIdWiseGmtsDataArr[$row['ID']]['CONSTRUCTION']=$row['CONSTRUCTION'];
			$fabIdWiseGmtsDataArr[$row['ID']]['DETAID']=$row['DETAID'];
			$fabcolorArr=array();
			if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
			{
				foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
				{
					$fabcolorArr[$row['ID']][$row['COLOR_NUMBER_ID']][$fabcolor]=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
				}
			}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			//$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
			//$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			
			$finAmt=$finReq*$row['RATE'];
			//$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			
			
			$fullfab=$row['CONSTRUCTION'].','.$row['COMPOSITION'].','.$row['GSM_WEIGHT'].','.$row['DIA_WIDTH'];
			$fullwofab=$row['CONSTRUCTION'].','.$row['COMPOSITION'].','.$row['GSM_WEIGHT'];
			$fabDescArr[$row['ID']]['fab']=$fullfab;
			$fabDescArr[$row['ID']]['fabwo']=$fullwofab;
			if($row['FABRIC_SOURCE']==2)
			{
				if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
				{
					foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
					{
						$cons=0;
						$cons=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
						$finReq=($planQty/$itemRatio)*($cons/$costingPer);
						$finAmt=$finReq*$row['RATE'];
						
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_amt']+=$greyAmt;
					}
				}
				else if ($sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]!="" && $row['COLOR_SIZE_SENSITIVE']==3)
				{
					$cons=0;
					$fabcolor=$sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']];
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
				else
				{
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
				
			}
		}
		unset($sqlfabRes);
		
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 

		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id='$poid'";
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
			$reqYarnArr[$row['POID']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['yarn_qty']+=$yarnReq;
			$reqYarnArr[$row['POID']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['yarn_amt']+=$yarnAmt;
		}
		unset($sqlYarnRes);
		
		//print_r($reqYarnArr);
		?>
        <div style="width:450px;" align="center">
            <table cellpadding="0" width="450px" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="5">Total Cost as per Budget</th>
                    </tr>
                    <tr>
                    	<th colspan="5">Yarn Cost as per Budget </th>
                    </tr>
                    <tr>
                        <th width="200">Yarn Composition</th>
                        <th width="60">Count</th>
                        <th width="60">Qty.</th>
                        <th width="50">Avg Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				$lib_yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");
				foreach($reqYarnArr[$poid] as $compo=>$compodata)
				{
					foreach($compodata as $countid=>$countdata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$bomYarnAvgRate=0;
						$bomYarnAvgRate=$countdata['yarn_amt']/$countdata['yarn_qty'];
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
							<td width="200" style="word-break:break-all" title="<?=$composition[$compo]; ?>"><?=substr($composition[$compo], 0, 35); ?></td>
							<td width="60" style="word-break:break-all"><?=$lib_yarn_count[$countid]; ?></td>
							<td width="60" align="right"><?=fn_number_format($countdata['yarn_qty'],2,'','',0); ?></td>
							<td width="50" align="right"><?=fn_number_format($bomYarnAvgRate,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($countdata['yarn_amt'],2,'','',0); ?></td>
						</tr>
						<?
						$i++;
						
						$gYarnBomQty+=$countdata['yarn_qty'];
						$gYarnBomAmt+=$countdata['yarn_amt'];
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="200">&nbsp;</td>
                        <td width="60">Total : </td>
                        <td width="60" align="right"><?=fn_number_format($gYarnBomQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gYarnBomAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <?
		
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id='$poid'";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convFabArr=array();
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
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
				}
			}
		}
		//echo "ff"; die;
		$convReqQtyAmtArr=array(); $convRateArr=array();
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
			$libYarnDetaid=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['DETAID'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			$convRateArr[$row['CONVERTION_ID']]['fab']=$fabDescArr[$row['PRECOSTID']]['fab'];
			$convRateArr[$row['CONVERTION_ID']]['fabwo']=$fabDescArr[$row['PRECOSTID']]['fabwo'];
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
						$reqqnty=$qnty;
						$convAmt=$qnty*$convrate;
					}
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yamt']+=$convAmt;
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
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				else if($consProcessId==1 || $consProcessId==33)
				{
					$convrate=$row['CHARGE_UNIT'];
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				//echo $convrate.'='.$row['CHARGE_UNIT'].'='.$itemRatio.'='.$requirment.'='.$costingPer."<br>";
				if($consProcessId==134)
				{
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yamt']+=$convAmt;
				}
				if($consProcessId==1)
				{
					$fabconstruction=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['CONSTRUCTION'];
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kqty']+=$reqqnty;
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kamt']+=$convAmt;
				}
				if($consProcessId==31)
				{
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdqty']+=$reqqnty;
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdamt']+=$convAmt;
					
				}
				if($consProcessId==67 || $consProcessId==68 || $consProcessId==35 || $consProcessId==213 || $consProcessId==214 || $consProcessId==217 || $consProcessId==236 || $consProcessId==237 || $consProcessId==270)
				{
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaqty']+=$reqqnty;
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaamt']+=$convAmt;
				}
				$convRateArr[$row['POID']][$consProcessId][$rateColorId][$libYarnDetaid]['fdrate']=$convrate;
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes);
		//print_r($convRateArr[18145][31][10983]);
		//print_r($convRateArr[54013][67][7706]); die;
		
		//print_r($convReqQtyAmtArr);
		?>
        <div style="width:450px;" align="center">
            <table cellpadding="0" width="450px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="5">Yarn Sevice Cost as per Budget</th>
                    </tr>
                    <tr>
                        <th width="150">Process</th>
                        <th width="80">Item/Yarn Color</th>
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				$colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
				foreach($convReqQtyAmtArr['yd'][$poid] as $processid=>$convdata)
				{
					$q=1; $countyd=count($convdata);
					foreach($convdata as $colorid=>$colordata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$bomYarnDAvgRate=0;
						$bomYarnDAvgRate=$colordata['yamt']/$colordata['yqty'];
						$itemColor="";
						if($processid==134) $itemColor="Yarn"; else $itemColor=$colorArr[$colorid];
						
						
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
                        <? if($q==1) { ?>
							<td width="150" style="word-break:break-all" rowspan="<?=$countyd; ?>"><?=$conversion_cost_head_array[$processid]; ?></td>
                        <? } ?>
							<td width="80" style="word-break:break-all"><?=$itemColor; ?></td>
							<td width="60" align="right"><?=fn_number_format($colordata['yqty'],2,'','',0); ?></td>
							<td width="50" align="right"><?=fn_number_format($bomYarnDAvgRate,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($colordata['yamt'],2,'','',0); ?></td>
						</tr>
						<?
						$i++; $q++;
						
						$gYarndBomQty+=$colordata['yqty'];
						$gYarndBomAmt+=$colordata['yamt'];
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="150">&nbsp;</td>
                        <td width="80">Total : </td>
                        <td width="60" align="right"><?=fn_number_format($gYarndBomQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gYarndBomAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        
        <div style="width:400px;" align="center">
            <table cellpadding="0" width="400px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="4">Knitting Cost as per Budget </th>
                    </tr>
                    <tr>
                        <th width="150">Fabric Structure</th>
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($convReqQtyAmtArr['knit'][$poid] as $processid=>$convdata)
				{
					foreach($convdata as $fabconst=>$fabconstdata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$bomKnitAvgRate=0;
						$bomKnitAvgRate=$fabconstdata['kamt']/$fabconstdata['kqty'];
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
							<td width="200" style="word-break:break-all" title="<?=$fabconst; ?>"><?=substr($fabconst, 0, 35); ?></td>
							<td width="60" align="right"><?=fn_number_format($fabconstdata['kqty'],2,'','',0); ?></td>
							<td width="50" align="right"><?=fn_number_format($bomKnitAvgRate,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($fabconstdata['kamt'],2,'','',0); ?></td>
						</tr>
						<?
						$i++;
						
						$gknitBomQty+=$fabconstdata['kqty'];
						$gknitBomAmt+=$fabconstdata['kamt'];
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="200">Total : </td>
                        <td width="60" align="right"><?=fn_number_format($gknitBomQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gknitBomAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        
        <div style="width:400px;" align="center">
            <table cellpadding="0" width="400px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="4">Dyeing Cost as per Budget</th>
                    </tr>
                    <tr>
                        <th width="200">Fabric Color </th>
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($convReqQtyAmtArr['fd'][$poid] as $processid=>$convdata)
				{
					foreach($convdata as $fabcolor=>$fabcolordata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$bomFdAvgRate=0;
						$bomFdAvgRate=$fabcolordata['fdamt']/$fabcolordata['fdqty'];
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
							<td width="200" style="word-break:break-all" title="<?=$colorArr[$fabcolor]; ?>"><?=substr($colorArr[$fabcolor], 0, 35); ?></td>
							<td width="60" align="right"><?=fn_number_format($fabcolordata['fdqty'],2,'','',0); ?></td>
							<td width="50" align="right"><?=fn_number_format($bomFdAvgRate,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($fabcolordata['fdamt'],2,'','',0); ?></td>
						</tr>
						<?
						$i++;
						
						$gFdBomQty+=$fabcolordata['fdqty'];
						$gFdBomAmt+=$fabcolordata['fdamt'];
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="200">Total : </td>
                        <td width="60" align="right"><?=fn_number_format($gFdBomQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gFdBomAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        
        <div style="width:400px;" align="center">
            <table cellpadding="0" width="400px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="4">PBA Cost as per Budget </th>
                    </tr>
                    <tr>
                        <th width="180">Finish Process</th>
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($convReqQtyAmtArr['pba'][$poid] as $processid=>$procdata)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$bomPbaAvgRate=0;
					$bomPbaAvgRate=$procdata['pba']['pbaamt']/$procdata['pba']['pbaqty'];
					?>
					<tr bgcolor="<?=$bgcolor; ?>" >
						<td width="180" style="word-break:break-all"><?=$conversion_cost_head_array[$processid]; ?></td>
						<td width="60" align="right"><?=fn_number_format($procdata['pba']['pbaqty'],2,'','',0); ?></td>
						<td width="50" align="right"><?=fn_number_format($bomPbaAvgRate,2,'','',0); ?></td>
						<td align="right"><?=fn_number_format($procdata['pba']['pbaamt'],2,'','',0); ?></td>
					</tr>
					<?
					$i++;
					
					$gPbaBomQty+=$procdata['pba']['pbaqty'];
					$gPbaBomAmt+=$procdata['pba']['pbaamt'];
				}
				$totalBomCost=$gYarnBomAmt+$gYarndBomAmt+$gknitBomAmt+$gFdBomAmt+$gPbaBomAmt;
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td width="180">Total : </td>
                        <td width="60" align="right"><?=fn_number_format($gPbaBomQty,2,'','',0); ?></td>
                        <td width="50">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gPbaBomAmt,2,'','',0); ?></td>
					</tr>
					<tr style="background-color:#CCC">
						<td colspan="3" align="right" title="Yarn + Yarn Service + Knitting + Dyeing+ PBA">Total Budget Cost: </td>
                        <td align="right"><?=fn_number_format($totalBomCost,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <?
		
		$sqlYIssue="SELECT a.id as issue_id, a.issue_number, a.booking_no, a.knit_dye_source, a.knit_dye_company, b.quantity as issue_qnty, b.prod_id, d.cons_rate, a.issue_purpose from inv_issue_master a, order_wise_pro_details b, inv_transaction d 
			where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($poid) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose  in (1,2,15,50) order by a.id ASC";
		$sqlYarnIssue=sql_select($sqlYIssue);
		$yarnStolenArr=array(); $yarnratearr=array(); $yarnWoArr=array();
		foreach($sqlYarnIssue as $yirow)
		{
			$yarnStolenArr[$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissqty']+=$yirow[csf("issue_qnty")];
			$yarnStolenArr[$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissamt']+=$yirow[csf("issue_qnty")]*($yirow[csf("cons_rate")]/82);
			$yarnratearr[$yirow[csf("issue_id")]][$yirow[csf("prod_id")]]=($yirow[csf("cons_rate")]/82);
			$yarnWoArr[$yirow[csf("booking_no")]]['']['booking_no']=$yirow[csf("knit_dye_source")];
			$yarnWoArr[$yirow[csf("booking_no")]][$yirow[csf("prod_id")]]['rate']=($yirow[csf("cons_rate")]/82);
		}
		unset($sqlYarnIssue);
		
		$sql_ret = "SELECT a.id, a.recv_number, a.knitting_source, a.knitting_company, b.quantity, b.prod_id, d.issue_id, b.issue_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d 
			where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($poid) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose in (1,2,15,50) ";
		//echo $sql_ret;
		$sqlYarnIssueRet=sql_select($sql_ret);
		foreach($sqlYarnIssueRet as $yirrow)
		{
			$retrate=$yarnratearr[$yirrow[csf("issue_id")]][$yirrow[csf("prod_id")]];
			$yarnStolenArr[$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretqty']+=$yirrow[csf("quantity")];
			$yarnStolenArr[$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretamt']+=$yirrow[csf("quantity")]*$retrate;
		}
		unset($sqlYarnIssueRet);
		
		$sqlGray="SELECT a.knitting_source, a.knitting_company, a.receive_purpose, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.quantity as quantity, b.order_yarn_rate as kniting_charge from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d 
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2,22) and c.entry_form in (2,22) and c.po_breakdown_id in($poid) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlGray;
		$sqlGrayRec=sql_select($sqlGray);
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$yarnStolenArr[1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecqty']+=$grrow[csf("quantity")];
			$yarnStolenArr[1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]);
		}
		unset($sqlGrayRec);
		
		$sqlRec = "SELECT a.id, a.recv_number, a.booking_no, a.knitting_source, a.supplier_id as knitting_company, d.grey_quantity as quantity, b.prod_id, d.cons_avg_rate as order_rate, a.receive_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d 
			where a.id=d.mst_id and d.transaction_type=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=1 and b.po_breakdown_id in ($poid) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in (1,2,15,50) ";
		//echo $sqlRec;
		$sqlYarnRec=sql_select($sqlRec);
		foreach($sqlYarnRec as $yrrow)
		{
			$knitsource=$yarnWoArr[$yrrow[csf("booking_no")]]['']['booking_no'];
			$yarnrecrate=$yarnWoArr[$yrrow[csf("booking_no")]][$yrrow[csf("prod_id")]]['rate'];
			
			$yarnStolenArr[$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecqty']+=$yrrow[csf("quantity")];
			$yarnStolenArr[$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecamt']+=$yrrow[csf("quantity")]*($yrrow[csf("order_rate")]/82);
		}
		unset($sqlYarnRec);
		
		$supplieArr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		?>
        <div style="width:600px;" align="center">
            <table cellpadding="0" width="600px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="8">Actual Cost</th>
                    </tr>
                    <tr>
                    	<th colspan="8">Stolen Yarn Value info </th>
                    </tr>
                    <tr>
                        <th width="80">Factory type</th>
                        <th width="120">Factory Name</th>
                        <th width="60">Del. Qty.</th>
                        <th width="60">Del. Amount</th>
                        <th width="60">Rec. Qty.</th>
                        <th width="60">Rec. Amount</th>
                        <th width="60">Stolen. Qty.</th>
                        <th>Stolen. Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($yarnStolenArr as $issuepurpose=>$issuepurposedata)
				{
					foreach($issuepurposedata as $ysource=>$ysourcedata)
					{
						foreach($ysourcedata as $ysourcecom=>$ysourcecomdata)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$knitdyeCom="";
							if($ysource==1) $knitdyeCom=$companyArr[$ysourcecom]; else $knitdyeCom=$supplieArr[$ysourcecom];
							$issqty=$issAmt=$stolenQty=$stolenAmt=0;
							$issqty=$ysourcecomdata['yissqty']-$ysourcecomdata['yissretqty'];
							$issAmt=$ysourcecomdata['yissamt']-$ysourcecomdata['yissretamt'];
							$stolenQty=$issqty-$ysourcecomdata['yrecqty'];
							$stolenAmt=$issAmt-$ysourcecomdata['yrecamt'];
							
							//echo $issuepurpose.'-'.$ysourcecomdata['yissamt'].'-'.$ysourcecomdata['yissretamt'].'-'.$ysourcecomdata['yrecamt'].'<br>';
							?>
							<tr bgcolor="<?=$bgcolor; ?>" >
								<td style="word-break:break-all" title="<?=$yarn_issue_purpose[$issuepurpose]; ?>"><?=substr($yarn_issue_purpose[$issuepurpose], 0, 17); ?></td>
                                <td style="word-break:break-all" title="<?=$knitdyeCom; ?>"><?=substr($knitdyeCom, 0, 20); ?></td>
								<td align="right"><?=fn_number_format($issqty,2,'','',0); ?></td>
								<td align="right"><?=fn_number_format($issAmt,2,'','',0); ?></td>
                                <td align="right"><?=fn_number_format($ysourcecomdata['yrecqty'],2,'','',0); ?></td>
								<td align="right"><?=fn_number_format($ysourcecomdata['yrecamt'],2,'','',0); ?></td>
                                <td align="right"><?=fn_number_format($stolenQty,2,'','',0); ?></td>
								<td align="right"><?=fn_number_format($stolenAmt,2,'','',0); ?></td>
							</tr>
							<?
							$i++;
							
							$gstolenQty+=$stolenQty;
							$gstolenAmt+=$stolenAmt;
						}
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td colspan="6">Total : </td>
                        <td align="right"><?=fn_number_format($gstolenQty,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($gstolenAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <?
		$sqlGYIssue="SELECT a.id as issue_id, b.quantity as issue_qnty, b.prod_id, c.cons_rate, d.lot, d.brand_supplier, d.yarn_count_id, d.yarn_comp_type1st,d.product_name_details from inv_issue_master a, order_wise_pro_details b, inv_transaction c, product_details_master d
			where a.id=c.mst_id and c.transaction_type=2 and c.item_category=1 and c.id=b.trans_id and c.prod_id=d.id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($poid) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	    //echo $sqlGYIssue;
		$sqlGYIssueRes=sql_select($sqlGYIssue); $greyYarnDtlsArr=array();
		foreach($sqlGYIssueRes as $isrow)
		{
			$str="";
			$str=$isrow[csf("yarn_count_id")].'**'.$isrow[csf("yarn_comp_type1st")].'**'.$isrow[csf("brand_supplier")].'**'.$isrow[csf("lot")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecdata']=$str;
			//$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecqty']+=$isrow[csf("issue_qnty")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecrate']=($isrow[csf("cons_rate")]/82);
		}
		unset($sqlGYIssueRes);
		
		$sqlGray="select a.id,b.id as dtls_id, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.quantity as quantity, b.kniting_charge, b.order_yarn_rate,a.knitting_source from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d 
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2) and c.entry_form in (2) and c.po_breakdown_id in($poid) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlGray;
		$sqlGrayRec=sql_select($sqlGray); $greymstIdArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$greymstIdArr[$grrow[csf("id")]]=$grrow[csf("id")];
		}
		$recv_cond=where_con_using_array($greymstIdArr,0,"receive_id");

		$knitting_bill_sql="SELECT b.receive_id,b.currency_id,b.rate, a.company_id,a.bill_date FROM subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and a.entry_form=438 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_cond";
		//echo $knitting_bill_sql;
		$knitting_bill_res=sql_select($knitting_bill_sql);
		$recv_wise_knitting_charge=array();
		foreach ($knitting_bill_res as $row)
		{
			$con_rate=set_conversion_rate($row[csf('currency_id')], $row[csf('bill_date')],$row[csf('company_id')]);
			// echo "<pre>";
			// echo $con_rate;
			// echo "</pre>";
			$recv_wise_knitting_charge[$row[csf('receive_id')]]=($row[csf('rate')]*$con_rate);
		}
		
		$greyMst_cond=where_con_using_array($greymstIdArr,0,"mst_id");
		
		$sqlYarn="select prod_id, used_qty,dtls_id,mst_id,amount from pro_material_used_dtls where entry_form=2 and status_active=1 and is_deleted=0 $greyMst_cond";
		//echo $sqlYarn;
		$sqlYarnUsed=sql_select($sqlYarn); $yusedArr=array(); $yusedArr1=array();
		foreach($sqlYarnUsed as $yurow)
		{
			$yusedArr[$yurow[csf("prod_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['amount']+=$yurow[csf("amount")];
		}
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		//$prod_arr_used=array();
		foreach($sqlGrayRec as $grrow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$grrow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$grayDataArr[$fabDescription]['yprodid'].=','.$grrow[csf("yarn_prod_id")];
			$grayDataArr[$fabDescription]['grecqty']+=$grrow[csf("quantity")];
			if($grrow[csf("knitting_source")]==1)
			{
				$grayDataArr[$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]/82);
				$prodrateArr[$fabDescription]=($grrow[csf("kniting_charge")]/82);
			}
			else
			{
				$grayDataArr[$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
				$prodrateArr[$fabDescription]=($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
			}
			
			$exyarnid=explode(",",$grrow[csf("yarn_prod_id")]);
			
			foreach($exyarnid as $ynid)
			{
				$greyYarnDtlsArr[$fabDescription][$ynid]['yrecqty']+=$yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty'];
				$greyYarnDtlsArr[$fabDescription][$ynid]['yrecamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);
				$grayDataArr[$fabDescription]['yrntotamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);;
			}
		}
		unset($sqlGrayRec);
		// echo "<pre>";
		// print_r($grayDataArr);
		// echo "</pre>";
		//echo $grayDataArr[$grrow[csf("product_name_details")]]['yrntotamt'];
		
		//print_r($greyYarnDtlsArr);
		$sqlTrans = "select a.from_order_id, a.to_order_id, b.to_prod_id, b.from_prod_id, c.trans_type, c.quantity as transfer_qnty, d.product_name_details, b.rate, b.transfer_value from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form=13 and c.po_breakdown_id in ($poid) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlTrans;
		$sqlTransRes=sql_select($sqlTrans); $greyTransDtlsArr=array(); $greyTransinDtlsArr=array(); $trnsPoIdArr=array(); $gFromPoIdArr=array();
		foreach($sqlTransRes as $gtrrow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$gtrrow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			if($gtrrow[csf("trans_type")]==5)
			{
				//$greyTransinDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['trinpoid'].=$gtrrow[csf("from_order_id")].',';
				$greyTransinDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['trinpoid']=$gtrrow[csf("from_order_id")];
				
				$greyTransinDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['trinqty']+=$gtrrow[csf("transfer_qnty")];

				//$greyTransinDtlsArr[$fabDescription]['trinamt']+=($gtrrow[csf("transfer_qnty")]*$prodrateArr[$fabDescription]);

				//change here
				$greyTransinDtlsArr[$gtrrow[csf("from_order_id")]][$fabDescription]['trinamt']+=($gtrrow[csf("transfer_value")]/82);

				//array_push($trnsPoIdArr,$gtrrow[csf('from_order_id')]);
				$gFromPoIdArr[$gtrrow[csf('from_order_id')]]=$gtrrow[csf('from_order_id')];
				
				//echo $gtrrow[csf('from_order_id')].'i <br>';
			}
			else if($gtrrow[csf("trans_type")]==6)
			{
				$greyTransDtlsArr[$fabDescription]['troutpoid'].=$gtrrow[csf("to_order_id")].',';
				$greyTransDtlsArr[$fabDescription]['troutqty']+=$gtrrow[csf("transfer_qnty")];
				$greyTransDtlsArr[$fabDescription]['troutamt']+=$gtrrow[csf("transfer_qnty")]*$prodrateArr[$fabDescription];
				//array_push($trnsPoIdArr,$gtrrow[csf('to_order_id')]);
				//echo $gtrrow[csf('to_order_id')].' <br>';
			}
			$trnsPoIdArr[$gtrrow[csf('to_order_id')]]=$gtrrow[csf('to_order_id')];
			$trnsPoIdArr[$gtrrow[csf('from_order_id')]]=$gtrrow[csf('from_order_id')];
		}
		unset($sqlTransRes);
		 //print_r($gFromPoIdArr);
		
		$greyTransInRateFromFnc=fncGreyFinTransInRate($gFromPoIdArr,1);
		
		$fabpoid_cond=where_con_using_array($trnsPoIdArr,0,"id");
		$poRefArr=return_library_array( "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond", "id", "grouping");
		//echo "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond";
		
		?>
        <div style="width:1220px;" align="center">
            <table cellpadding="0" width="1220px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="16">Actual Gray Fabric cost</th>
                    </tr>
                    <tr>
                        <th width="180">Fabric Details</th>
                        <th width="70">Count</th>
                        <th width="80">Composition</th>
                        <th width="70">Brand</th>
                        <th width="70">Lot</th>
                        <th width="60">KG</th>
                        <th width="60">Price</th>
                        <th width="70">Total Price</th>
                        
                        <th width="60">Grey Rcv Qty</th>
                        <th width="60">Knitting Charge</th>
                        <th width="70" title="Total price=Knitting Cost + Yarn Cost">Fabric Price</th>
                        <th width="80">Tran. Out Ref</th>
                        <th width="60">Out Qty</th>
                        <th width="60">Rate/Kg</th>
                        <th width="70" title="Total price=Out Qty * Rate/Kg">Total price </th>
                        <th title="Actual Gray Fab cost=Fabric Price-Total price">Actual Gray Fab cost</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
				$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
				$productWiseRate=array();
				foreach($grayDataArr as $gprodname=>$gprodnamedata)
				{
					$span=1;
						
					$transoutAvgRate=0;
					$exyprodid=array_filter(array_unique(explode(",",$gprodnamedata['yprodid'])));
					$countYarn=count($exyprodid);
					
					$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
					$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
					
					$transoutrefId=$transoutref="";
					$transoutrefId=array_filter(array_unique(explode(",",$greyTransDtlsArr[$gprodname]['troutpoid'])));
					foreach($transoutrefId as $topoid)
					{
						if($transoutref=="") $transoutref=$poRefArr[$topoid]; else $transoutref.=", ".$poRefArr[$topoid];
					}
					$transoutQty=$greyTransDtlsArr[$gprodname]['troutqty'];

					//$transoutAvgRate=$greytotamt/$gprodnamedata['yrntotamt'];
					$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
					$transoutAvgRate=fn_number_format($transoutAvgRate,8,".","");
					$productWiseRate[$gprodname]['rate']=$transoutAvgRate;
					$productWiseRate[$gprodname]['qty']+=$gprodnamedata['grecqty'];
					$productWiseRate[$gprodname]['amt']+=$greytotamt;
					
					
					//if($transoutQty==0 || $transoutQty=="") $transoutAvgRate=0;
					$transoutAmt=$transoutQty*$transoutAvgRate;
					$actualGreyCost=0;
					$actualGreyCost=$greytotamt-$transoutAmt;
					//echo $actualGreyCost.'-'.$greytotamt.'-'.$transoutAmt.'<br>';
					$c=1;
					foreach($exyprodid as $yid)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$exydata=explode("**",$greyYarnDtlsArr[$yid]['yrecdata']);
						$excountidyarn=explode("**",$exydata[0]);
						$yarncount="";
						foreach($excountidyarn as $countid)
						{
							if($yarncount=="") $yarncount=$yarn_count_arr[$countid]; else $yarncount.=','.$yarn_count_arr[$countid];
						}
						$composition_string = $composition[$exydata[1]];
						
						$ybrand=$brand_arr[$exydata[2]];
						$ylot=$exydata[3];
						$yqtykg=$greyYarnDtlsArr[$gprodname][$yid]['yrecqty'];
						$yamt=$greyYarnDtlsArr[$gprodname][$yid]['yrecamt'];
						$yavgprice=$yamt/$yqtykg;
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
							<? if($c==1) { ?>
							<td style="word-break:break-all" rowspan="<?=$countYarn; ?>" title="<?=$gprodname; ?>"><?=substr($gprodname, 0, 34); ?></td>
							<? } ?>
							<td style="word-break:break-all" title="<?=$yid?>"><?=$yarncount; ?></td>
							<td style="word-break:break-all" title="<?=$composition_string; ?>"><?=substr($composition_string, 0, 13); ?></td>
							<td style="word-break:break-all" title="<?=$ybrand; ?>"><?=substr($ybrand, 0, 12); ?></td>
							<td style="word-break:break-all" title="<?=$ylot; ?>"><?=substr($ylot, 0, 12); ?></td>
							<td align="right"><?=fn_number_format($yqtykg,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($yavgprice,4,'','',0); ?></td>
							<td align="right"><?=fn_number_format($yamt,2,'','',0); ?></td>
							<? if($c==1) { ?>
							<td align="right" rowspan="<?=$countYarn; ?>"><?=fn_number_format($gprodnamedata['grecqty'],2,'','',0); ?></td>
							<td align="right" rowspan="<?=$countYarn; ?>"><?=fn_number_format($greyAvgPrice,4,'','',0); ?></td>
							<td align="right" title="knitting cost=<?=$gprodnamedata['grecamt']?> , yarn cost=<?=$gprodnamedata['yrntotamt']?>" rowspan="<?=$countYarn; ?>"><?=fn_number_format($greytotamt,2,'','',0); ?></td>
							<td style="word-break:break-all" rowspan="<?=$countYarn; ?>"><?=$transoutref; ?></td>
							<td align="right" rowspan="<?=$countYarn; ?>"><?=fn_number_format($transoutQty,2,'','',0); ?></td>
							<td align="right" rowspan="<?=$countYarn; ?>"><?=fn_number_format($transoutAvgRate,4,'','',0); ?></td>
							<td align="right" rowspan="<?=$countYarn; ?>"><?=fn_number_format($transoutAmt,2,'','',0); ?></td>
							<td title="<?=$actualGreyCost?>=<?=$greytotamt?>-<?=$transoutAmt?>" align="right" rowspan="<?=$countYarn; ?>"><?=fn_number_format($actualGreyCost,2,'','',0); ?></td>
							<? } ?>
						</tr>
						<?
						$i++; $c++;
						$gyarnQty+=$yqtykg;
						$gyarnAmt+=$yamt;
					}
					
					$ggrayQty+=$gprodnamedata['grecqty'];
					$ggrayAmt+=$greytotamt;
					
					$gtransoutQty+=$transoutQty;
					$gtransoutAmt+=$transoutAmt;
					$gactualgrayAmt+=$actualGreyCost;
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td colspan="5">Total : </td>
                        <td align="right"><?=fn_number_format($gyarnQty,2,'','',0); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=fn_number_format($gyarnAmt,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($ggrayQty,2,'','',0); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=fn_number_format($ggrayAmt,2,'','',0); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=fn_number_format($gtransoutQty,2,'','',0); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=fn_number_format($gtransoutAmt,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($gactualgrayAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        
        <div style="width:450px;" align="center">
            <table cellpadding="0" width="450px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="5">Transfer In Status</th>
                    </tr>
                    <tr>
                    	<th width="80">Ref. No</th>
                        <th width="150">Fabric Details</th>
                        <th width="60">Trans Qty.</th>
                        <th width="50">Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($greyTransinDtlsArr as $tipoid=>$tipodtlsdata)
				{
					foreach($tipodtlsdata as $fabricdtls=>$fabricdtlsdata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$bomTransinAvgQty=$bomTransinAvgRate=$gTransInAmt=0;
						//$bomTransinAvgRate=$fabricdtlsdata['trinamt']/$fabricdtlsdata['trinqty'];
						/*$bomTransinAvgQty=$greyTransInRateFromFnc[$fabricdtlsdata['trinpoid']][$fabricdtls]['qty'];
						$bomTransinAvgAmt=$greyTransInRateFromFnc[$fabricdtlsdata['trinpoid']][$fabricdtls]['amt'];*/
						//$transoutQty=$greyTransDtlsArr[$gprodname]['troutqty'];
						$bomTransinAvgRate=$greyTransInRateFromFnc[$tipoid][$fabricdtls];
						
						$gTransInAmt=$fabricdtlsdata['trinqty']*$bomTransinAvgRate;
						
						$transinref="";
						$transinrefId=$tipoid;//array_filter(array_unique(explode(",",$fabricdtlsdata['trinpoid'])));
						$productWiseRate[$fabricdtls]['rate']=$bomTransinAvgRate;
						$productWiseRate[$fabricdtls]['qty']+=$fabricdtlsdata['trinqty'];
						$productWiseRate[$fabricdtls]['amt']+=$gTransInAmt;
						
						/*foreach($transinrefId as $frompoid)
						{
							if($transinref=="") $transinref=$poRefArr[$frompoid]; else $transinref.=", ".$poRefArr[$frompoid];
						}*/
						$transinref=$poRefArr[$tipoid];
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
							<td style="word-break:break-all"><?=$transinref; ?></td>
							<td style="word-break:break-all" title="<?=$fabricdtls; ?>"><?=substr($fabricdtls, 0, 30); ?></td>
							<td align="right"><?=fn_number_format($fabricdtlsdata['trinqty'],2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($bomTransinAvgRate,4,'','',0); ?></td>
							<td align="right"><?=fn_number_format($gTransInAmt,2,'','',0); ?></td>
						</tr>
						<?
						$i++;
						
						$gTransinQty+=$fabricdtlsdata['trinqty'];
						$gTransinAmt+=$gTransInAmt;
					}
				}
				$gTotalGreyFabCost=$gactualgrayAmt+$gTransinAmt;
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td colspan="2" align="right">Total : </td>
                        <td align="right"><?=fn_number_format($gTransinQty,2,'','',0); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=fn_number_format($gTransinAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <br />
        <div style="width:350px;" align="center">
        	<table cellpadding="0" width="550px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th width="130" title="Actual Grey Fabric Cost + Transfer In Grey Fabric Cost">Total Gray fabric cost </th>
                        <th align="right"><?=fn_number_format($gTotalGreyFabCost,2,'','',0); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
        <br />
        <?
		
		$sqlIss="select b.color_id as COLOR_ID, c.prod_id as PROD_ID, c.po_breakdown_id as POID, b.rate as RATE, c.quantity as QUANTITY from inv_grey_fabric_issue_dtls b, order_wise_pro_details c where b.id=c.dtls_id and c.entry_form in (16) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$poid' ";
		$sqlIssArr=sql_select($sqlIss); $grayRateArr=array();
		foreach($sqlIssArr as $grow)
		{
			$grayRateArr[$grow["COLOR_ID"]][$grow["PROD_ID"]]['rate']=$grow["RATE"];
		}
		unset($sqlIssArr);
		//print_r($grayRateArr);
		
		$sqlBatch = "select a.id, a.color_id, a.entry_form, b.po_id, b.prod_id, b.batch_qnty as quantity, c.product_name_details, c.detarmination_id from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.po_id in($poid) and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$batchDataArr=array();
		$sqlBatchArr=sql_select($sqlBatch); $batchidarr=array(); $batchcolorarr=array(); 
		foreach($sqlBatchArr as $brow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$brow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			//echo $grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate'].'i<br>';
			$amt=$brow[csf("quantity")]*($grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate']/82);
			$batchDataArr[$brow[csf("color_id")]][$fabDescription]['batch_qty']+=$brow[csf("quantity")];
			$batchDataArr[$brow[csf("color_id")]][$fabDescription]['batch_amt']+=$amt;
			$batchidarr[$brow[csf("id")]]=$brow[csf("id")];
			$batchcolorarr[$brow[csf("id")]]=$brow[csf("color_id")];
			
			$batchDataArr[$brow[csf("color_id")]][$fabDescription]['dyeamt']+=$brow[csf("quantity")]*$convRateArr[$poid][31][$brow[csf("color_id")]][$brow[csf("detarmination_id")]]['fdrate'];
			//echo "<pre>".$brow[csf("color_id")]."**".$brow[csf("product_name_details")]."=".$brow[csf("detarmination_id")]."</pre>";
		}
		unset($sqlBatchArr);
		//print_r($batchDataArr[12744]); die;
		
		$batchid_cond=where_con_using_array($batchidarr,0,"a.batch_id");
		
		$sqlSP="select a.batch_id, a.process_id, b.prod_id, b.production_qty, c.product_name_details, c.detarmination_id from pro_fab_subprocess a, pro_fab_subprocess_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=34 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchid_cond";
		$sqlSPArr=sql_select($sqlSP); $specialDataArr=array();
		foreach($sqlSPArr as $brow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$brow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			
			$batchcolor=$batchcolorarr[$brow[csf("batch_id")]];
			$amt=$brow[csf("production_qty")]*($convRateArr[$poid][$brow[csf("process_id")]][$batchcolor][$brow[csf("detarmination_id")]]['fdrate']);
			if($brow[csf("process_id")]==213 || $brow[csf("process_id")]==214 || $brow[csf("process_id")]==217 || $brow[csf("process_id")]==236 || $brow[csf("process_id")]==237 || $brow[csf("process_id")]==270 || $brow[csf("process_id")]==35) $brow[csf("process_id")]=35;
			$specialDataArr[$brow[csf("process_id")]][$batchcolor][$fabDescription]['sp_qty']+=$brow[csf("production_qty")];
			$specialDataArr[$brow[csf("process_id")]][$batchcolor][$fabDescription]['sp_amt']+=$amt;
		}
		unset($sqlSPArr);
		//print_r($specialDataArr[67][7706]); die;

		$sql_outsite_service="SELECT a.febric_description_id, a.process_id, a.wo_qty, a.batch_issue_qty, a.rate, a.amount, a.order_id, a.fin_dia, a.fin_gsm, a.grey_used, c.fabric_color_id, c.po_break_down_id, a.id
			FROM pro_grey_batch_dtls a, inv_receive_mas_batchroll b, wo_booking_dtls c
			WHERE a.mst_id = b.id AND a.booking_dtls_id = c.id AND b.entry_form = 92 AND c.is_deleted=0 AND a.is_deleted=0 AND b.is_deleted=0 AND c.po_break_down_id = $poid";
		//echo $sql_outsite_service;
		$res_outsite_service=sql_select($sql_outsite_service);
		$data_outsite_service=array();
		$fab_deter_mina_ids=array();
		$fin_dia_arr=array();
		foreach ($res_outsite_service as $row)
		{
			$fab_deter_mina_ids[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$fin_dia_arr[$fab_id]=$row[csf('fin_gsm')].",".$row[csf('fin_dia')];
		}
		$determina_cond=where_con_using_array($fab_deter_mina_ids,0,"a.id");
		$composition_arr=array(); $constructtion_arr=array();
	    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $determina_cond";
	    //echo $sql_deter;
	    $data_array=sql_select($sql_deter);
	    foreach($data_array as $row )
	    {
	    	$fab_id=$row[csf('id')];
	        if(array_key_exists($fab_id,$composition_arr))
	        {
	            $composition_arr[$fab_id]=$composition_arr[$fab_id].",".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	        else
	        {
	            $composition_arr[$fab_id]=$row[csf('construction')].",".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	    }
		
	    //print_r($composition_arr);
	    
	    
		//print_r($data_outsite_service[7706]); die;

		/*$sql_heat_set="SELECT c.product_name_details, c.detarmination_id, SUM(b.batch_qnty) AS batch_qnty, a.color_id
						FROM pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c
						WHERE a.id = b.mst_id AND a.entry_form = 0 AND c.id=b.prod_id AND b.status_active = 1 AND b.is_deleted = 0 AND b.po_id = $poid AND a.is_deleted = 0
						GROUP BY c.product_name_details, c.detarmination_id, a.color_id";*/
		$sql_heat_set="SELECT c.product_name_details, SUM(e.batch_qty) AS batch_qnty, a.color_id
						FROM pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c, pro_fab_subprocess d, pro_fab_subprocess_dtls e
						WHERE a.id = b.mst_id AND c.id=b.prod_id AND a.id=d.batch_id AND d.id=e.mst_id AND b.prod_id=e.prod_id AND c.id=e.prod_id AND a.entry_form = 0 AND d.entry_form = 32 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND b.po_id = $poid AND a.is_deleted = 0
						GROUP BY c.product_name_details, c.detarmination_id, a.color_id";
		//echo $sql_heat_set;

		$res_heat_set=sql_select($sql_heat_set);

		$data_heat_set=array();
		//print_r($convRateArr[$poid][33]);
		foreach ($res_heat_set as $row) 
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row[csf('product_name_details')]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$rate=0;
			$rate=$convRateArr[$poid][33][$row[csf('color_id')]][$row[csf("detarmination_id")]]['fdrate'];
			$data_heat_set[$row[csf('color_id')]][$fabDescription]['amount']+=($row[csf('batch_qnty')]*$rate);
			$data_heat_set[$row[csf('color_id')]][$fabDescription]['qnty']+=$row[csf('batch_qnty')];
		}
		
		$dataArrayfinish = "select a.id as ID, a.entry_form as ENTRY_FORM, a.booking_id as BOOKINGID, a.knitting_source as KNITTING_SOURCE, a.receive_basis as RECEIVEBASIS, a.currency_id as CURRENCY_ID, b.batch_id as BATCHID, b.rate as RATE, c.po_breakdown_id as POID, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, b.grey_used_qty as GREY_USED_QTY, b.receive_qnty as RECEIVE_QNTY, b.grey_fabric_rate as GREY_FABRIC_RATE, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2 and c.po_breakdown_id='$poid'";
		$dataArrayfinishArr=sql_select($dataArrayfinish);
		$bookingidArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				$bookingidArr[$row['BOOKINGID']]=$row['BOOKINGID'];
			}
		}
		$bookingid_cond=where_con_using_array($bookingidArr,0,"a.id");
		$servBookSql="select a.id, b.booking_no, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.dia_width, b.rate, b.process from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingid_cond";
		$servBookSqlArr=sql_select($servBookSql); $bookingRateArr=array(); $bookingProcessRateArr=array();
		foreach($servBookSqlArr as $worow)
		{
			$conv_fabric="";
			
			if($worow[csf("dia_width")]=="") $conv_fabric=$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab'];
			else $conv_fabric=$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fabwo'].','.$worow[csf("dia_width")];
			//echo $conv_fabric.'<br>';
			$fabDescription="";
			$exFabric=array_filter(explode(",",$conv_fabric));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			
			//if($row[csf('process_id')]==213 || $row[csf('process_id')]==214 || $row[csf('process_id')]==217 || $row[csf('process_id')]==236 || $row[csf('process_id')]==237 || $row[csf('process_id')]==270 || $row[csf('process_id')]==35) $row[csf('process_id')]=35;
			
			$bookingProcessRateArr[$worow[csf("process")]][$fabDescription][$worow[csf("fabric_color_id")]]['worateprocess']=$worow[csf("rate")];
			$bookingRateArr[$worow[csf("id")]][$fabDescription][$worow[csf("fabric_color_id")]]['worate']=$worow[csf("rate")];
		}
		unset($servBookSqlArr);
		
		foreach ($res_outsite_service as $row)
		{
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$product_details=$composition_arr[$row[csf('febric_description_id')]].",".$fin_dia_arr[$fab_id];
			
			$finWoRate=$row[csf('rate')];//$bookingProcessRateArr[$row[csf('process_id')]][$product_details][$row[csf('fabric_color_id')]]['worateprocess'];
			if($row[csf('process_id')]==33)
			{
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('grey_used')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('grey_used')]*$finWoRate);
			}
			else
			{
				if($row[csf('process_id')]==213 || $row[csf('process_id')]==214 || $row[csf('process_id')]==217 || $row[csf('process_id')]==236 || $row[csf('process_id')]==237 || $row[csf('process_id')]==270 || $row[csf('process_id')]==35) $row[csf('process_id')]=35;
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('batch_issue_qty')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('batch_issue_qty')]*$finWoRate);
			}
		}
		
		$recDataRetArr=array(); $finishDataArr=array(); $fabProdPurchBatchArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row['PRODUCT_NAME_DETAILS']));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			
			if($row['ENTRY_FORM']==7)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				//$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_amt']+=$amt;
				//echo $row['POID'].'-'.$row['PRODUCT_NAME_DETAILS'].'-'.$row['COLOR_ID'].'<br>';
				
				$recDataRetArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
				$fabProdPurchBatchArr[$row['POID']][$fabDescription][$row['COLOR_ID']][$row['BATCHID']]=1;
			}
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				//$avgQty=((1-($row['QUANTITY']/$row['GREY_USED_QTY']))*$row['GREY_USED_QTY'])+$row['QUANTITY'];
				$avgQty=($row['GREY_USED_QTY']/$row['RECEIVE_QNTY'])*$row['QUANTITY'];
				//echo $avgQty.'='.$row['QUANTITY'].'='.$row['GREY_USED_QTY'].'='.'kausar<br>';
				$amt=$avgQty*($row['GREY_FABRIC_RATE']/82);
				//echo "<pre>".$amt."=".$avgQty."*(".$row['GREY_FABRIC_RATE']."/82)</pre>";
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['batch_qty']+=$avgQty;
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['batch_amt']+=$amt;
				$finWoRate=$bookingRateArr[$row['BOOKINGID']][$fabDescription][$row['COLOR_ID']]['worate'];
				$amt=$avgQty*$finWoRate;
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_amt']+=$amt;
				$fabProdPurchBatchArr[$row['POID']][$fabDescription][$row['COLOR_ID']][$row['BATCHID']]=1;
				
				$grey_used_amt=0;
				
				$grey_used_amt=$row['GREY_USED_QTY']*$finWoRate;
				//echo $row['GREY_USED_QTY'].'='.$bookingRateArr[$fabDescription][$row['COLOR_ID']]['worate'].'<br>';
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['dyeqtyrec']+=$row['GREY_USED_QTY'];
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['dyeamtrec']+=$grey_used_amt;
			}
			//echo "<pre>".$row['ENTRY_FORM']."=".$row['KNITTING_SOURCE']."=".$row['RECEIVEBASIS']."</pre>";
		}
		unset($dataArrayfinishArr);
		//print_r($batchDataArr); die;
		
		$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, b.batch_id as BATCH_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$poid'";
		
		$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array(); $transOutArr=array(); $transInArr=array(); $batchIDArr=array(); $fFromPoIdArr=array();
		foreach($sqlTransArr as $row)
		{
			$transVal=$amt=0;
			$transVal=$row['TRANSFER_VALUE']/82;
			
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row['PRODUCT_NAME_DETAILS']));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			//echo $recRate;
			if($row['TRANS_TYPE']==5)//trans in
			{
				/*if($batchEntryFormArr[$row['BATCH_ID']]==37)
				{*/
					//$amt=$row['QUANTITY']*$transRate;
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTin_qty']+=$row['QUANTITY'];
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTin_amt']+=$transVal;
					//$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['trinpoid'].=$row['FROM_ORDER_ID'].',';
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['trinpoid']=$row['FROM_ORDER_ID'];
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['batchid']=$row['BATCH_ID'];
					array_push($batchIDArr,$row['BATCH_ID']);
					
					$fFromPoIdArr[$row['FROM_ORDER_ID']]=$row['FROM_ORDER_ID'];
				//}
			}
			else if($row['TRANS_TYPE']==6)//trans out
			{
				//$amt=$row['QUANTITY']*$transRate;
				if($fabProdPurchBatchArr[$row['POID']][$fabDescription][$row['COLOR_ID']][$row['BATCH_ID']]==1)
				{
					$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_qty']+=$row['QUANTITY'];
					$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_amt']+=$transVal;
					$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_ref'].=$row['TO_ORDER_ID'].',';
				}
				array_push($batchIDArr,$row['BATCH_ID']);
			}
			array_push($trnsPoIdArr,$row['TO_ORDER_ID']);
			array_push($trnsPoIdArr,$row['FROM_ORDER_ID']);
		}
		unset($sqlTransArr);
		//print_r($transInArr);
		
		$finishTransInRateFromFnc=fncGreyFinTransInRate($fFromPoIdArr,2,'','',0);
		
		//print_r($finishTransInRateFromFnc);
		
		$fabpoid_cond=where_con_using_array($trnsPoIdArr,0,"id");
		$poRefArr=return_library_array( "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond", "id", "grouping");
		
		$batchID_cond=where_con_using_array($batchIDArr,0,"id");
		$batchEntryArr=return_library_array( "select id, entry_form from pro_batch_create_mst where 1=1 $batchID_cond", "id", "entry_form");
		//echo "select id, grouping from wo_po_break_down where 1=1 $fabpoid_cond";
		?>
        <div style="width:1340px;" align="center">
            <table cellpadding="0" width="1340px" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="26">Actual Finish Fabric Cost</th>
                    </tr>
                    <tr>
                        <th colspan="5">&nbsp;</th>
                        <th colspan="3">Heatsetting Cost</th>
                        <th colspan="2">Dyeing cost</th>
                        <th colspan="3">Peach Finish Cost</th>
                        <th colspan="3">Brushing cost</th>
                        <th colspan="3">AOP Cost</th>
                        <th width="60" rowspan="2">Finish Fabric cost </th>
                        <th width="70" rowspan="2">Finish Fabric Qty</th>
                        <th width="50" rowspan="2">Cost/kg </th>
                        
                        <th colspan="3">Transfer out</th>
                        <th rowspan="2">Fabric Cost</th>
                        
                    </tr>
                    <tr>
                    	<th width="80">F. Color</th>
                        <th width="150">Fabric Details</th>
                        <th width="60">Grey Qty.</th>
                        <th width="40">Gray fab cost/kg</th>
                        <th width="50">Total Grey cost </th>
                        
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th width="70">Total</th>

                        <th width="50">Rate</th>
                        <th width="70">Amount</th>
                        
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th width="70">Total</th>
                        
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th width="70">Total</th>
                        
                        <th width="60">Qty.</th>
                        <th width="50">Rate</th>
                        <th width="70">Total</th>
                        
                        <th width="80">Ref No</th>
                        <th width="60">Qty</th>
                        <th width="70">Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				$colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
				foreach($batchDataArr as $fabcolorid=>$fabcolordata)
				{
					foreach($fabcolordata as $fabric=>$fabdata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$greyAvgRate=fn_number_format($productWiseRate[$fabric]['amt']/$productWiseRate[$fabric]['qty'],8,".","");;
						$fabdata['batch_amt']=$greyAvgRate*$fabdata['batch_qty']; // change batch amount 
						$dyeqty=$dyeamt=0;
						$dyeqty=$fabdata['batch_qty']+$fabdata['dyeqtyrec'];
						$dyeamt=$fabdata['dyeamt']+$fabdata['dyeamtrec'];
						
						$dyeAvgRate=$dyeamt/$dyeqty;
						//echo $fabdata['batch_amt'].'-'.$fabdata['batch_qty'].'-'.$greyAvgRate.'<br>';
						
						$heatSetQty=$data_heat_set[$fabcolorid][$fabric]['qnty']+$data_outsite_service[$fabcolorid][$fabric][33]['qnty'];
						$heatSetAmt=$data_heat_set[$fabcolorid][$fabric]['amount']+$data_outsite_service[$fabcolorid][$fabric][33]['amount'];
						$heatSetRate=$heatSetAmt/$heatSetQty;

						$peachFinishQty=$specialDataArr[67][$fabcolorid][$fabric]['sp_qty']+$data_outsite_service[$fabcolorid][$fabric][67]['qnty'];
						$peachFinishAmt=$specialDataArr[67][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][67]['amount'];
						$peachFinishRate=$peachFinishAmt/$peachFinishQty;
						//echo $specialDataArr[67][$fabcolorid][$fabric]['sp_amt'].'-'.$data_outsite_service[$fabcolorid][$fabric][67]['amount'].'<br>';
						$brushingQty=$specialDataArr[68][$fabcolorid][$fabric]['sp_qty']+$data_outsite_service[$fabcolorid][$fabric][68]['qnty'];
						$brushingAmt=$specialDataArr[68][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][68]['amount'];
						$brushingRate=$brushingAmt/$brushingQty;
						
						$aopQty=$specialDataArr[35][$fabcolorid][$fabric]['sp_qty']+$data_outsite_service[$fabcolorid][$fabric][35]['qnty'];
						$aopAmt=$specialDataArr[35][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][35]['amount'];
						$aopRate=$aopAmt/$aopQty;
						
						$finishAmt=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_amt'];
						$ftitel=$fabdata['batch_amt'].'-'.$dyeamt.'-'.$peachFinishAmt.'-'.$brushingAmt.'-'.$aopAmt.'-'.$finishAmt.'-'.$heatSetAmt.'<br>';
						$finishFabCost=$fabdata['batch_amt']+$dyeamt+$peachFinishAmt+$brushingAmt+$aopAmt+$finishAmt+$heatSetAmt;
						
						$finishQty=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_qty'];
						
						//echo $poid.'-'.$fabric.'-'.$fabcolorid.'<br>';
						$finRate=$finishFabCost/$finishQty;
						
						$transoutrefId=$transoutref=""; $trnsOUtQty=$trnsOUtAmt=0;
						//if($batchEntryArr[$fabdata['batchid']]!=37)
						$transoutrefId=array_filter(array_unique(explode(",",$transOutArr[$poid][$fabric][$fabcolorid]['finTout_ref'])));
						
						foreach($transoutrefId as $frompoid)
						{
							if($transoutref=="") $transoutref=$poRefArr[$frompoid]; else $transoutref.=", ".$poRefArr[$frompoid];
						}
						
						
						$trnsOUtQty=$transOutArr[$poid][$fabric][$fabcolorid]['finTout_qty'];

						//$trnsOUtAmt=$transOutArr[$poid][$fabric][$fabcolorid]['finTout_amt'];
						$finRate=fn_number_format($finRate,8,".","");
						$trnsOUtQty=fn_number_format($trnsOUtQty,8,".","");
						$trnsOUtAmt=$finRate*$trnsOUtQty;
						
						$fabricFinishCost=$finishFabCost-$trnsOUtAmt;
						?>
						<tr bgcolor="<?=$bgcolor; ?>" >
							<td style="word-break:break-all" title="<?=$colorArr[$fabcolorid]; ?>"><?=substr($colorArr[$fabcolorid], 0, 10); ?> </td>
							<td style="word-break:break-all" title="<?=$fabric; ?>"><?=substr($fabric, 0, 20); ?></td>
							<td align="right"><?=fn_number_format($fabdata['batch_qty'],2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($greyAvgRate,2,'','',0); ?></td>
							<td align="right" title="<?=$productWiseRate[$fabric]['amt'].'-'.$productWiseRate[$fabric]['qty']; ?>"><?=fn_number_format($fabdata['batch_amt'],2,'','',0); ?></td>

							<td align="right" title="InHouse=<?=$data_heat_set[$fabcolorid][$fabric]['qnty'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][33]['qnty'];?>"><?=fn_number_format($heatSetQty,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($heatSetRate,2,'','',0); ?></td>
							<td align="right" title="InHouse=<?=$data_heat_set[$fabcolorid][$fabric]['amount'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][33]['amount'];?>"><?=fn_number_format($heatSetAmt,2,'','',0); ?></td>
							
							<td align="right"><?=fn_number_format($dyeAvgRate,2,'','',0); ?></td>
							<td align="right" title="<?='In-'.$fabdata['dyeamt'].'+ Out-'.$fabdata['dyeamtrec']; ?>"><?=fn_number_format($dyeamt,2,'','',0); ?></td>
							
							<td align="right" title="InHouse=<?=$specialDataArr[67][$fabcolorid][$fabric]['sp_qty'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][67]['qnty'];?>"><?=fn_number_format($peachFinishQty,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($peachFinishRate,2,'','',0); ?></td>
							<td align="right" title="InHouse=<?=$specialDataArr[67][$fabcolorid][$fabric]['sp_amt'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][67]['amount'];?>"><?=fn_number_format($peachFinishAmt,2,'','',0); ?></td>
							
							<td align="right" title="InHouse=<?=$specialDataArr[68][$fabcolorid][$fabric]['sp_qty'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][68]['qnty'];?>"><?=fn_number_format($brushingQty,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($brushingRate,2,'','',0); ?></td>
							<td align="right" title="InHouse=<?=$specialDataArr[68][$fabcolorid][$fabric]['sp_amt'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][68]['amount'];?>"><?=fn_number_format($brushingAmt,2,'','',0); ?></td>
							
							<td align="right" title="InHouse=<?=$specialDataArr[35][$fabcolorid][$fabric]['sp_qty'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][35]['qnty'];?>"><?=fn_number_format($aopQty,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($aopRate,2,'','',0); ?></td>
							<td align="right" title="InHouse=<?=$specialDataArr[35][$fabcolorid][$fabric]['sp_amt'];?>,OutSite=<?=$data_outsite_service[$fabcolorid][$fabric][35]['amount'];?>"><?=fn_number_format($aopAmt,2,'','',0); ?></td>
							
							<td align="right" title="<?=$ftitel; ?>"><?=fn_number_format($finishFabCost,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($finishQty,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($finRate,2,'','',0); ?></td>
							
							<td style="word-break:break-all"><?=$transoutref; ?></td>
							<td align="right"><?=fn_number_format($trnsOUtQty,2,'','',0); ?></td>
							<td align="right"><?=fn_number_format($trnsOUtAmt,2,'','',0); ?></td>
							
							<td align="right"><?=fn_number_format($fabricFinishCost,2,'','',0); ?></td>
						</tr>
						<?
						$i++;
						
						$gbatchQty+=$fabdata['batch_qty'];
						$gbatchAmt+=$fabdata['batch_amt'];
						
						$gdyeingAmt+=$dyeamt;

						$gHeadSetQty+=$heatSetQty;
						$gHeadSetAmt+=$heatSetAmt;
						
						$gPeachFinishQty+=$peachFinishQty;
						$gPeachFinishAmt+=$peachFinishAmt;
						
						$gBrushingQty+=$brushingQty;
						$gBrushingAmt+=$brushingAmt;
						
						$gAopQty+=$aopQty;
						$gAopAmt+=$aopAmt;
						
						$gFinishQty+=$finishQty;
						$gFinishAmt+=$finishFabCost;
						
						$gTransOutQty+=$trnsOUtQty;
						$gTransOutAmt+=$trnsOUtAmt;
						
						$gFabricAmt+=$fabricFinishCost;
					}
				}
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td style="word-break:break-all">&nbsp;</td>
                        <td style="word-break:break-all">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gbatchQty,2,'','',0); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gbatchAmt,2,'','',0); ?></td>

                        <td align="right"><?=fn_number_format($gHeadSetQty,2,'','',0); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gHeadSetAmt,2,'','',0); ?></td>
                        
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gdyeingAmt,2,'','',0); ?></td>
                        
                        <td align="right"><?=fn_number_format($gPeachFinishQty,2,'','',0); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gPeachFinishAmt,2,'','',0); ?></td>
                        
                        <td align="right"><?=fn_number_format($gBrushingQty,2,'','',0); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gBrushingAmt,2,'','',0); ?></td>
                        
                        <td align="right"><?=fn_number_format($gAopQty,2,'','',0); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gAopAmt,2,'','',0); ?></td>
                        
                        <td align="right"><?=fn_number_format($gFinishAmt,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($gFinishQty,2,'','',0); ?></td>
                        <td align="right">&nbsp;</td>
                        
                        <td style="word-break:break-all">&nbsp;</td>
                        <td align="right"><?=fn_number_format($gTransOutQty,2,'','',0); ?></td>
                        <td align="right"><?=fn_number_format($gTransOutAmt,2,'','',0); ?></td>
                        
                        <td align="right"><?=fn_number_format($gFabricAmt,2,'','',0); ?></td>
                    </tr>
				</tfoot>
            </table>
        </div>
        
        <div style="width:550px;" align="center">
            <table cellpadding="0" width="550px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th colspan="6">Finish fabric Transfer In Info</th>
                    </tr>
                    <tr>
                    	<th width="80">Ref. No</th>
                        <th width="80">Fab. Color</th>
                        <th width="170">Fabric Details</th>
                        <th width="60">Trans Qty.</th>
                        <th width="50">Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($transInArr[$poid] as $fabricdtls=>$fabricdtlsdata)
				{
					foreach($fabricdtlsdata as $fabriccolor=>$colordata)
					{
						if($batchEntryArr[$colordata['batchid']]!=37)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$bomTransinAvgQty=$bomTransinAvgAmt=$bomTransinAvgRate=$finTransinAmt=0;
							//$bomTransinAvgRate=$colordata['finTin_amt']/$colordata['finTin_qty'];
							$bomTransinAvgRate=$finishTransInRateFromFnc[$fabriccolor][$fabricdtls];
							
							/*$bomTransinAvgQty=$finishTransInRateFromFnc[$fabriccolor][$fabricdtls]['qty'];
							$bomTransinAvgAmt=$finishTransInRateFromFnc[$fabriccolor][$fabricdtls]['amt'];
							$bomTransinAvgRate=$bomTransinAvgAmt/$bomTransinAvgQty;*/
							
							
							//$finishTransInRateArr[$fabcolorid][$fabric]=$finRate;
							//$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['trinpoid']=$row['FROM_ORDER_ID'];
							
							$finTransinAmt=$colordata['finTin_qty']*$bomTransinAvgRate;
							
							$transinref="";
							$transinrefId=array_filter(array_unique(explode(",",$colordata['trinpoid'])));
							
							foreach($transinrefId as $frompoid)
							{
								if($transinref=="") $transinref=$poRefArr[$frompoid]; else $transinref.=", ".$poRefArr[$frompoid];
							}
							?>
							<tr bgcolor="<?=$bgcolor; ?>" >
								<td style="word-break:break-all"><?=$transinref; ?></td>
								<td style="word-break:break-all" title="<?=$colorArr[$fabriccolor]; ?>"><?=substr($colorArr[$fabriccolor], 0, 10); ?></td>
								<td style="word-break:break-all" title="<?=$fabricdtls; ?>"><?=substr($fabricdtls, 0, 22); ?></td>
								<td align="right"><?=fn_number_format($colordata['finTin_qty'],2,'','',0); ?></td>
								<td align="right"><?=fn_number_format($bomTransinAvgRate,2,'','',0); ?></td>
								<td align="right"><?=fn_number_format($finTransinAmt,2,'','',0); ?></td>
							</tr>
							<?
							$i++;
							
							$gfinishQty+=$colordata['finTin_qty'];
							$gfinishAmt+=$finTransinAmt;
						}
					}
				}
				
				$gTotalFinishFabCost=$gFabricAmt+$gfinishAmt;
				$gActualFinishFabcost=$gTotalFinishFabCost+($gTotalGreyFabCost-$gbatchAmt);
				$gExcessFinishFabCost=($gActualFinishFabcost+$gstolenAmt)-$totalBomCost;
				?>
                </tbody>
                <tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total : </td>
                        <td align="right"><?=fn_number_format($gfinishQty,2,'','',0); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=fn_number_format($gfinishAmt,2,'','',0); ?></td>
					</tr>
				</tfoot>
            </table>
        </div>
        
        <div style="width:350px;" align="center">
        	<table cellpadding="0" width="550px" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                    	<th width="130" title="Finish Fabric Cost(<?=$gFabricAmt; ?>) + Transfer In Finish Fabric Cost(<?=$gfinishAmt; ?>)">Total Finish Fabric Cost</th>
                        <th align="right"><?=fn_number_format($gTotalFinishFabCost,2,'','',0); ?></th>
                    </tr>
                    <tr>
                    	<th width="130" title="Total Finish Fabric Cost(<?=$gTotalFinishFabCost; ?>) + (Total Grey Fabric Cost (<?=$gTotalGreyFabCost; ?>) - Total Used Grey Fabric Cost during produced finish fabric (<?=$gbatchAmt; ?>))">Actual Fabric Cost </th>
                        <th align="right"><?=fn_number_format($gActualFinishFabcost,2,'','',0); ?></th>
                    </tr>
                    <tr>
                    	<th width="130" title="Actual Fabric Cost(<?=$gActualFinishFabcost; ?>) + Yarn Stolen Amount(<?=$gstolenAmt; ?>) - Total Budget Cost(<?=$totalBomCost; ?>)">Excess MFG Fabric Cost</th>
                        <th align="right"><?=fn_number_format($gExcessFinishFabCost,2,'','',0); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
        <?
	}
	exit();
}

function fncGreyFinTransInRate($transInPoId,$type)
{
	//print_r($transInPoId); die;
	//Grey
	if($type==1)
	{
		$yarnpoid_cond=where_con_using_array($transInPoId,0,"b.po_breakdown_id");
		
		$sqlGYIssue="SELECT a.id as issue_id, b.quantity as issue_qnty, b.prod_id, b.po_breakdown_id, c.cons_rate, d.lot, d.brand_supplier, d.yarn_count_id, d.yarn_comp_type1st,d.product_name_details from inv_issue_master a, order_wise_pro_details b, inv_transaction c, product_details_master d
			where a.id=c.mst_id and c.transaction_type=2 and c.item_category=1 and c.id=b.trans_id and c.prod_id=d.id and b.trans_type=2 and b.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarnpoid_cond";
		//echo $sqlGYIssue; die;
		$sqlGYIssueRes=sql_select($sqlGYIssue); $greyYarnDtlsArr=array();
		foreach($sqlGYIssueRes as $isrow)
		{
			$str="";
			$str=$isrow[csf("yarn_count_id")].'**'.$isrow[csf("yarn_comp_type1st")].'**'.$isrow[csf("brand_supplier")].'**'.$isrow[csf("lot")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecdata']=$str;
			//$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecqty']+=$isrow[csf("issue_qnty")];
			$greyYarnDtlsArr[$isrow[csf("po_breakdown_id")]][$isrow[csf("prod_id")]]['yrecrate']=($isrow[csf("cons_rate")]/82);
		}
		unset($sqlGYIssueRes);
		
		$greypoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlGray="select a.id,b.id as dtls_id, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.po_breakdown_id, c.quantity as quantity, b.kniting_charge, b.order_yarn_rate, a.knitting_source from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d 
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2) and c.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $greypoid_cond";
		//if($type==2) echo $sqlGray;
		$sqlGrayRec=sql_select($sqlGray); $greymstIdArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$greymstIdArr[$grrow[csf("id")]]=$grrow[csf("id")];
		}
		$recv_cond=where_con_using_array($greymstIdArr,0,"receive_id");
	
		$knitting_bill_sql="SELECT b.receive_id, b.currency_id, b.rate, a.company_id, a.bill_date FROM subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and a.entry_form=438 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_cond";
		//echo $knitting_bill_sql;
		$knitting_bill_res=sql_select($knitting_bill_sql);
		$recv_wise_knitting_charge=array();
		foreach ($knitting_bill_res as $row)
		{
			$con_rate=set_conversion_rate($row[csf('currency_id')], $row[csf('bill_date')],$row[csf('company_id')]);
			$recv_wise_knitting_charge[$row[csf('receive_id')]]=($row[csf('rate')]*$con_rate);
		}
		
		$greyMst_cond=where_con_using_array($greymstIdArr,0,"mst_id");
		
		$sqlYarn="select prod_id, used_qty,dtls_id,mst_id,amount from pro_material_used_dtls where entry_form=2 and status_active=1 and is_deleted=0 $greyMst_cond";
		//echo $sqlYarn;
		$sqlYarnUsed=sql_select($sqlYarn); $yusedArr=array(); $yusedArr1=array();
		foreach($sqlYarnUsed as $yurow)
		{
			$yusedArr[$yurow[csf("prod_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['amount']+=$yurow[csf("amount")];
		}
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		//$prod_arr_used=array();
		foreach($sqlGrayRec as $grrow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$grrow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['yprodid'].=','.$grrow[csf("yarn_prod_id")];
			$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['grecqty']+=$grrow[csf("quantity")];
			if($grrow[csf("knitting_source")]==1)
			{
				$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]/82);
				$prodrateArr[$grrow[csf("po_breakdown_id")]][$fabDescription]=($grrow[csf("kniting_charge")]/82);
			}
			else
			{
				$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
				$prodrateArr[$grrow[csf("po_breakdown_id")]][$fabDescription]=($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
			}
			
			$exyarnid=explode(",",$grrow[csf("yarn_prod_id")]);
			
			foreach($exyarnid as $ynid)
			{
				$greyYarnDtlsArr[$grrow[csf("po_breakdown_id")]][$fabDescription][$ynid]['yrecqty']+=$yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty'];
				$greyYarnDtlsArr[$grrow[csf("po_breakdown_id")]][$fabDescription][$ynid]['yrecamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);
				$grayDataArr[$grrow[csf("po_breakdown_id")]][$fabDescription]['yrntotamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);;
			}
		}
		unset($sqlGrayRec);
		
		$transInRateArr=array(); $productWiseRate=array();
		foreach($grayDataArr as $po_id=>$podata)
		{
			foreach($podata as $gprodname=>$gprodnamedata)
			{
				$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
				$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
							
				$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
				$transoutAvgRate=fn_number_format($transoutAvgRate,8,".","");
				//echo $gprodnamedata['yrntotamt'].'='.$gprodnamedata['grecqty'].'='.$greyAvgPrice.'<br>';
				
				//$productWiseRate[$po_id][$gprodname]=$transoutAvgRate;
				
				$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
				$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
				//$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
				$transInRateArr[$po_id][$gprodname]=$transoutAvgRate;
			}
		}
		/*if ($type==2)
		{
		echo "<pre>";
		print_r($productWiseRate);
		}*/
	
	
		return $transInRateArr;
	}
	else if ($type==2)//Finish
	{
		$stylepoid_cond=where_con_using_array($transInPoId,0,"b.id");
		$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $stylepoid_cond";
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes);
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
		/*if($db_type==2 && $cjobid>1000)
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
		}*/
		
		$jobidCond=where_con_using_array($ujobid,0,"a.job_id");
		$jobidCondition=where_con_using_array($ujobid,0,"job_id");
		//die;
		//echo "ff"; die;
		//Item Ratio Details
		$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1  $jobidCondition";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		}
		unset($gmtsitemRatioSqlRes);
		
		$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];

		}
		unset($sqlContrastRes);
		
		//Stripe Details
		$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
		}
		unset($sqlStripeRes);
		$fabpoid_cond=where_con_using_array($transInPoId,0,"b.po_break_down_id");
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.lib_yarn_count_deter_id as DETAID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.composition as COMPOSITION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.dia_width as DIA_WIDTH, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
	from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.fabric_source=1 $fabpoid_cond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $fabDescArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$fabIdWiseGmtsDataArr[$row['ID']]['CONSTRUCTION']=$row['CONSTRUCTION'];
			$fabIdWiseGmtsDataArr[$row['ID']]['DETAID']=$row['DETAID'];
			$fabcolorArr=array();
			if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
			{
				foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
				{
					$fabcolorArr[$row['ID']][$row['COLOR_NUMBER_ID']][$fabcolor]=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
				}
			}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			//$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
			//$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			
			$finAmt=$finReq*$row['RATE'];
			//$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			
			
			$fullfab=$row['CONSTRUCTION'].','.$row['COMPOSITION'].','.$row['GSM_WEIGHT'].','.$row['DIA_WIDTH'];
			$fullwofab=$row['CONSTRUCTION'].','.$row['COMPOSITION'].','.$row['GSM_WEIGHT'];
			$fabDescArr[$row['ID']]['fab']=$fullfab;
			$fabDescArr[$row['ID']]['fabwo']=$fullwofab;
			if($row['FABRIC_SOURCE']==2)
			{
				if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
				{
					foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
					{
						$cons=0;
						$cons=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
						$finReq=($planQty/$itemRatio)*($cons/$costingPer);
						$finAmt=$finReq*$row['RATE'];
						
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_amt']+=$greyAmt;
					}
				}
				else if ($sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]!="" && $row['COLOR_SIZE_SENSITIVE']==3)
				{
					$cons=0;
					$fabcolor=$sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']];
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
				else
				{
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
			}
		}
		unset($sqlfabRes);
		
		$convpoid_cond=where_con_using_array($transInPoId,0,"a.po_break_down_id");
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $convpoid_cond";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convFabArr=array();
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
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
				}
			}
		}
		//echo "ff"; die;
		$convReqQtyAmtArr=array(); $convRateArr=array();
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
			$libYarnDetaid=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['DETAID'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			$convRateArr[$row['CONVERTION_ID']]['fab']=$fabDescArr[$row['PRECOSTID']]['fab'];
			$convRateArr[$row['CONVERTION_ID']]['fabwo']=$fabDescArr[$row['PRECOSTID']]['fabwo'];
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
						$reqqnty=$qnty;
						$convAmt=$qnty*$convrate;
					}
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yamt']+=$convAmt;
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
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				else if($consProcessId==1 || $consProcessId==33)
				{
					$convrate=$row['CHARGE_UNIT'];
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				//echo $convrate.'='.$row['CHARGE_UNIT'].'='.$itemRatio.'='.$requirment.'='.$costingPer."<br>";
				if($consProcessId==134)
				{
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yamt']+=$convAmt;
				}
				if($consProcessId==1)
				{
					$fabconstruction=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['CONSTRUCTION'];
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kqty']+=$reqqnty;
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kamt']+=$convAmt;
				}
				if($consProcessId==31)
				{
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdqty']+=$reqqnty;
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdamt']+=$convAmt;
					
				}
				if($consProcessId==67 || $consProcessId==68 || $consProcessId==35)
				{
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaqty']+=$reqqnty;
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaamt']+=$convAmt;
				}
				$convRateArr[$row['POID']][$consProcessId][$rateColorId][$libYarnDetaid]['fdrate']=$convrate;
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes);
		//print_r($convRateArr);
		//return $convRateArr[54013][67][7706]; die;
		//echo $transInPoId.'jj'; die;
		
		$yisspoid_cond=where_con_using_array($transInPoId,0,"b.po_breakdown_id");
		$sqlYIssue="SELECT a.id as issue_id, a.issue_number, a.booking_no, a.knit_dye_source, a.knit_dye_company, b.quantity as issue_qnty, b.prod_id, d.cons_rate, a.issue_purpose from inv_issue_master a, order_wise_pro_details b, inv_transaction d 
			where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 $yisspoid_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose  in (1,2,15,50) order by a.id ASC";
		//echo $sqlYIssue;
		$sqlYarnIssue=sql_select($sqlYIssue);
		$yarnStolenArr=array(); $yarnratearr=array(); $yarnWoArr=array();
		foreach($sqlYarnIssue as $yirow)
		{
			$yarnStolenArr[$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissqty']+=$yirow[csf("issue_qnty")];
			$yarnStolenArr[$yirow[csf("issue_purpose")]][$yirow[csf("knit_dye_source")]][$yirow[csf("knit_dye_company")]]['yissamt']+=$yirow[csf("issue_qnty")]*($yirow[csf("cons_rate")]/82);
			$yarnratearr[$yirow[csf("issue_id")]][$yirow[csf("prod_id")]]=($yirow[csf("cons_rate")]/82);
			$yarnWoArr[$yirow[csf("booking_no")]]['']['booking_no']=$yirow[csf("knit_dye_source")];
			$yarnWoArr[$yirow[csf("booking_no")]][$yirow[csf("prod_id")]]['rate']=($yirow[csf("cons_rate")]/82);
		}
		unset($sqlYarnIssue);
		
		$sql_ret = "SELECT a.id, a.recv_number, a.knitting_source, a.knitting_company, b.quantity, b.prod_id, d.issue_id, b.issue_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d 
			where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 $yisspoid_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose in (1,2,15,50) ";
		//echo $sql_ret;
		$sqlYarnIssueRet=sql_select($sql_ret);
		foreach($sqlYarnIssueRet as $yirrow)
		{
			$retrate=$yarnratearr[$yirrow[csf("issue_id")]][$yirrow[csf("prod_id")]];
			$yarnStolenArr[$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretqty']+=$yirrow[csf("quantity")];
			$yarnStolenArr[$yirrow[csf("issue_purpose")]][$yirrow[csf("knitting_source")]][$yirrow[csf("knitting_company")]]['yissretamt']+=$yirrow[csf("quantity")]*$retrate;
		}
		unset($sqlYarnIssueRet);
		
		$graypoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlGray="SELECT a.knitting_source, a.knitting_company, a.receive_purpose, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.quantity as quantity, b.order_yarn_rate as kniting_charge from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d 
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2,22) and c.entry_form in (2,22) $graypoid_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlGray;
		$sqlGrayRec=sql_select($sqlGray);
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$yarnStolenArr[1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecqty']+=$grrow[csf("quantity")];
			$yarnStolenArr[1][$grrow[csf("knitting_source")]][$grrow[csf("knitting_company")]]['yrecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]);
		}
		unset($sqlGrayRec);
		
		$grayrecpoid_cond=where_con_using_array($transInPoId,0,"b.po_breakdown_id");
		$sqlRec = "SELECT a.id, a.recv_number, a.booking_no, a.knitting_source, a.supplier_id as knitting_company, d.grey_quantity as quantity, b.prod_id, d.cons_avg_rate as order_rate, a.receive_purpose
			from inv_receive_master a, order_wise_pro_details b, inv_transaction d 
			where a.id=d.mst_id and d.transaction_type=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=1 $grayrecpoid_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in (1,2,15,50) ";
		//echo $sqlRec;
		$sqlYarnRec=sql_select($sqlRec);
		foreach($sqlYarnRec as $yrrow)
		{
			$knitsource=$yarnWoArr[$yrrow[csf("booking_no")]]['']['booking_no'];
			$yarnrecrate=$yarnWoArr[$yrrow[csf("booking_no")]][$yrrow[csf("prod_id")]]['rate'];
			
			$yarnStolenArr[$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecqty']+=$yrrow[csf("quantity")];
			$yarnStolenArr[$yrrow[csf("receive_purpose")]][$knitsource][$yrrow[csf("knitting_company")]]['yrecamt']+=$yrrow[csf("quantity")]*($yrrow[csf("order_rate")]/82);
		}
		unset($sqlYarnRec);
		
		$sqlGYIssue="SELECT a.id as issue_id, b.quantity as issue_qnty, b.prod_id, c.cons_rate, d.lot, d.brand_supplier, d.yarn_count_id, d.yarn_comp_type1st,d.product_name_details from inv_issue_master a, order_wise_pro_details b, inv_transaction c, product_details_master d
			where a.id=c.mst_id and c.transaction_type=2 and c.item_category=1 and c.id=b.trans_id and c.prod_id=d.id and b.trans_type=2 and b.entry_form=3 $grayrecpoid_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	    //echo $sqlGYIssue;
		$sqlGYIssueRes=sql_select($sqlGYIssue); $greyYarnDtlsArr=array();
		foreach($sqlGYIssueRes as $isrow)
		{
			$str="";
			$str=$isrow[csf("yarn_count_id")].'**'.$isrow[csf("yarn_comp_type1st")].'**'.$isrow[csf("brand_supplier")].'**'.$isrow[csf("lot")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecdata']=$str;
			//$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecqty']+=$isrow[csf("issue_qnty")];
			$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecrate']=($isrow[csf("cons_rate")]/82);
		}
		unset($sqlGYIssueRes);
		
		$graypoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlGray="select a.id,b.id as dtls_id, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.quantity as quantity, b.kniting_charge, b.order_yarn_rate,a.knitting_source from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d 
		 
		where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2) and c.entry_form in (2) $graypoid_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlGray;
		$sqlGrayRec=sql_select($sqlGray); $greymstIdArr=array();
		foreach($sqlGrayRec as $grrow)
		{
			$greymstIdArr[$grrow[csf("id")]]=$grrow[csf("id")];
		}
		$recv_cond=where_con_using_array($greymstIdArr,0,"receive_id");

		$knitting_bill_sql="SELECT b.receive_id,b.currency_id,b.rate, a.company_id,a.bill_date FROM subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and a.entry_form=438 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_cond";
		//echo $knitting_bill_sql;
		$knitting_bill_res=sql_select($knitting_bill_sql);
		$recv_wise_knitting_charge=array();
		foreach ($knitting_bill_res as $row)
		{
			$con_rate=set_conversion_rate($row[csf('currency_id')], $row[csf('bill_date')],$row[csf('company_id')]);
			$recv_wise_knitting_charge[$row[csf('receive_id')]]=($row[csf('rate')]*$con_rate);
		}
		
		$greyMst_cond=where_con_using_array($greymstIdArr,0,"mst_id");
		
		$sqlYarn="select prod_id, used_qty,dtls_id,mst_id,amount from pro_material_used_dtls where entry_form=2 and status_active=1 and is_deleted=0 $greyMst_cond";
		//echo $sqlYarn;
		$sqlYarnUsed=sql_select($sqlYarn); $yusedArr=array(); $yusedArr1=array();
		foreach($sqlYarnUsed as $yurow)
		{
			$yusedArr[$yurow[csf("prod_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['yqty']+=$yurow[csf("used_qty")];
			$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['amount']+=$yurow[csf("amount")];
		}
		$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
		//$prod_arr_used=array();
		foreach($sqlGrayRec as $grrow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$grrow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$grayDataArr[$fabDescription]['yprodid'].=','.$grrow[csf("yarn_prod_id")];
			$grayDataArr[$fabDescription]['grecqty']+=$grrow[csf("quantity")];
			if($grrow[csf("knitting_source")]==1)
			{
				$grayDataArr[$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]/82);
				$prodrateArr[$fabDescription]=($grrow[csf("kniting_charge")]/82);
			}
			else
			{
				$grayDataArr[$fabDescription]['grecamt']+=$grrow[csf("quantity")]*($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
				$prodrateArr[$fabDescription]=($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
			}
			
			$exyarnid=explode(",",$grrow[csf("yarn_prod_id")]);
			
			foreach($exyarnid as $ynid)
			{
				$greyYarnDtlsArr[$fabDescription][$ynid]['yrecqty']+=$yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty'];
				$greyYarnDtlsArr[$fabDescription][$ynid]['yrecamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);
				$grayDataArr[$fabDescription]['yrntotamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);;
			}
		}
		unset($sqlGrayRec);
		
		$graytranspoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlTrans = "select a.from_order_id, a.to_order_id, b.to_prod_id, b.from_prod_id, c.trans_type, c.quantity as transfer_qnty, d.product_name_details, b.rate, b.transfer_value from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form=13 $graytranspoid_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $sqlTrans;
		$sqlTransRes=sql_select($sqlTrans); $greyTransDtlsArr=array(); $greyTransinDtlsArr=array(); $trnsPoIdArr=array(); $gFromPoIdArr=array();
		foreach($sqlTransRes as $gtrrow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$gtrrow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			if($gtrrow[csf("trans_type")]==5)
			{
				//$greyTransinDtlsArr[$fabDescription]['trinpoid'].=$gtrrow[csf("from_order_id")].',';
				$greyTransinDtlsArr[$fabDescription]['trinpoid']=$gtrrow[csf("from_order_id")];
				$greyTransinDtlsArr[$fabDescription]['trinqty']+=$gtrrow[csf("transfer_qnty")];

				//$greyTransinDtlsArr[$fabDescription]['trinamt']+=($gtrrow[csf("transfer_qnty")]*$prodrateArr[$fabDescription]);

				//change here
				$greyTransinDtlsArr[$fabDescription]['trinamt']+=($gtrrow[csf("transfer_value")]/82);

				//array_push($trnsPoIdArr,$gtrrow[csf('from_order_id')]);
				$gFromPoIdArr[$gtrrow[csf('from_order_id')]]=$gtrrow[csf('from_order_id')];
				
				//echo $gtrrow[csf('from_order_id')].'i <br>';
			}
			else if($gtrrow[csf("trans_type")]==6)
			{
				$greyTransDtlsArr[$fabDescription]['troutpoid'].=$gtrrow[csf("to_order_id")].',';
				$greyTransDtlsArr[$fabDescription]['troutqty']+=$gtrrow[csf("transfer_qnty")];
				$greyTransDtlsArr[$fabDescription]['troutamt']+=$gtrrow[csf("transfer_qnty")]*$prodrateArr[$fabDescription];
				//array_push($trnsPoIdArr,$gtrrow[csf('to_order_id')]);
				//echo $gtrrow[csf('to_order_id')].' <br>';
			}
			$trnsPoIdArr[$gtrrow[csf('to_order_id')]]=$gtrrow[csf('to_order_id')];
			$trnsPoIdArr[$gtrrow[csf('from_order_id')]]=$gtrrow[csf('from_order_id')];
		}
		unset($sqlTransRes);
		
		
		foreach($grayDataArr as $gprodname=>$gprodnamedata)
		{
			$span=1;
				
			$transoutAvgRate=0;
			$exyprodid=array_filter(array_unique(explode(",",$gprodnamedata['yprodid'])));
			$countYarn=count($exyprodid);
			
			$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
			$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
			
			$transoutrefId=$transoutref="";
			$transoutrefId=array_filter(array_unique(explode(",",$greyTransDtlsArr[$gprodname]['troutpoid'])));
			foreach($transoutrefId as $topoid)
			{
				if($transoutref=="") $transoutref=$poRefArr[$topoid]; else $transoutref.=", ".$poRefArr[$topoid];
			}
			$transoutQty=$greyTransDtlsArr[$gprodname]['troutqty'];

			//$transoutAvgRate=$greytotamt/$gprodnamedata['yrntotamt'];
			$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
			$transoutAvgRate=fn_number_format($transoutAvgRate,8,".","");
			$productWiseRate[$gprodname]['rate']=$transoutAvgRate;
			$productWiseRate[$gprodname]['qty']+=$gprodnamedata['grecqty'];
			$productWiseRate[$gprodname]['amt']+=$greytotamt;
			
			//echo $greytotamt.'-'.$gprodnamedata['grecqty'].'-'.$transoutAvgRate.'<br>';
			
			
			//if($transoutQty==0 || $transoutQty=="") $transoutAvgRate=0;
			$transoutAmt=$transoutQty*$transoutAvgRate;
			$actualGreyCost=0;
			$actualGreyCost=$greytotamt-$transoutAmt;
			//echo $actualGreyCost.'-'.$greytotamt.'-'.$transoutAmt.'<br>';
			$c=1;
			foreach($exyprodid as $yid)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$exydata=explode("**",$greyYarnDtlsArr[$yid]['yrecdata']);
				$excountidyarn=explode("**",$exydata[0]);
				$yarncount="";
				foreach($excountidyarn as $countid)
				{
					if($yarncount=="") $yarncount=$yarn_count_arr[$countid]; else $yarncount.=','.$yarn_count_arr[$countid];
				}
				$composition_string = $composition[$exydata[1]];
				
				$ybrand=$brand_arr[$exydata[2]];
				$ylot=$exydata[3];
				$yqtykg=$greyYarnDtlsArr[$gprodname][$yid]['yrecqty'];
				$yamt=$greyYarnDtlsArr[$gprodname][$yid]['yrecamt'];
				$yavgprice=$yamt/$yqtykg;
				
				$i++; $c++;
				$gyarnQty+=$yqtykg;
				$gyarnAmt+=$yamt;
			}
			
			$ggrayQty+=$gprodnamedata['grecqty'];
			$ggrayAmt+=$greytotamt;
			
			$gtransoutQty+=$transoutQty;
			$gtransoutAmt+=$transoutAmt;
			$gactualgrayAmt+=$actualGreyCost;
		}
		
		foreach($greyTransinDtlsArr as $fabricdtls=>$fabricdtlsdata)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bomTransinAvgQty=$bomTransinAvgRate=$gTransInAmt=0;
			$bomTransinAvgRate=$fabricdtlsdata['trinamt']/$fabricdtlsdata['trinqty'];
			/*$bomTransinAvgQty=$greyTransInRateFromFnc[$fabricdtlsdata['trinpoid']][$fabricdtls]['qty'];
			$bomTransinAvgAmt=$greyTransInRateFromFnc[$fabricdtlsdata['trinpoid']][$fabricdtls]['amt'];*/
			//echo $fabricdtlsdata['trinamt'].'-'.$fabricdtlsdata['trinqty'];
			//$bomTransinAvgRate=$greyTransInRateFromFnc[$fabricdtlsdata['trinpoid']][$fabricdtls];
			
			$gTransInAmt=$fabricdtlsdata['trinqty']*$bomTransinAvgRate;
			
			$transinref="";
			$transinrefId=array_filter(array_unique(explode(",",$fabricdtlsdata['trinpoid'])));
			$productWiseRate[$fabricdtls]['rate']=$bomTransinAvgRate;
			$productWiseRate[$fabricdtls]['qty']+=$fabricdtlsdata['trinqty'];
			$productWiseRate[$fabricdtls]['amt']+=$gTransInAmt;
			
			foreach($transinrefId as $frompoid)
			{
				if($transinref=="") $transinref=$poRefArr[$frompoid]; else $transinref.=", ".$poRefArr[$frompoid];
			}
			$i++;
			
			$gTransinQty+=$fabricdtlsdata['trinqty'];
			$gTransinAmt+=$gTransInAmt;
		}
		
		
		$isspoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlIss="select b.color_id as COLOR_ID, c.prod_id as PROD_ID, c.po_breakdown_id as POID, b.rate as RATE, c.quantity as QUANTITY from inv_grey_fabric_issue_dtls b, order_wise_pro_details c where b.id=c.dtls_id and c.entry_form in (16) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $isspoid_cond ";
		$sqlIssArr=sql_select($sqlIss); $grayRateArr=array();
		foreach($sqlIssArr as $grow)
		{
			$grayRateArr[$grow["COLOR_ID"]][$grow["PROD_ID"]]['rate']=$grow["RATE"];
		}
		unset($sqlIssArr);
		//print_r($grayRateArr);die;
		$batchpoid_cond=where_con_using_array($transInPoId,0,"b.po_id");
		$sqlBatch = "select a.id, a.color_id, a.entry_form, b.po_id, b.prod_id, b.batch_qnty as quantity, c.product_name_details, c.detarmination_id from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchpoid_cond";
		$batchDataArr=array();
		$sqlBatchArr=sql_select($sqlBatch); $batchidarr=array(); $batchcolorarr=array(); $batchProdPoarr=array(); 
		foreach($sqlBatchArr as $brow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$brow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			//echo $grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate'].'-'.$convRateArr[$brow[csf("po_id")]][31][$brow[csf("color_id")]][$brow[csf("detarmination_id")]]['fdrate'].'i<br>';
			$amt=$brow[csf("quantity")]*($grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate']/82);
			$batchDataArr[$brow[csf("color_id")]][$fabDescription]['batch_qty']+=$brow[csf("quantity")];
			$batchDataArr[$brow[csf("color_id")]][$fabDescription]['batch_amt']+=$amt;
			$batchDataArr[$brow[csf("color_id")]][$fabDescription]['poid']=$brow[csf("po_id")];
			$batchidarr[$brow[csf("id")]]=$brow[csf("id")];
			$batchcolorarr[$brow[csf("id")]]=$brow[csf("color_id")];
			
			$batchProdPoarr[$brow[csf("id")]][$brow[csf("prod_id")]]=$brow[csf("po_id")];
			
			$batchDataArr[$brow[csf("color_id")]][$fabDescription]['dyeamt']+=$brow[csf("quantity")]*$convRateArr[$brow[csf("po_id")]][31][$brow[csf("color_id")]][$brow[csf("detarmination_id")]]['fdrate'];
			//echo "<pre>".$brow[csf("color_id")]."**".$fabDescription."=".$brow[csf("detarmination_id")]."</pre>";
		}
		unset($sqlBatchArr);
		//print_r($batchDataArr[12744]); die;
		
		$batchid_cond=where_con_using_array($batchidarr,0,"a.batch_id");
		
		$sqlSP="select a.batch_id, a.process_id, b.prod_id, b.production_qty, c.product_name_details, c.detarmination_id from pro_fab_subprocess a, pro_fab_subprocess_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=34 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchid_cond";
		$sqlSPArr=sql_select($sqlSP); $specialDataArr=array();
		foreach($sqlSPArr as $brow)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$brow[csf("product_name_details")]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$batchcolor=$batchcolorarr[$brow[csf("batch_id")]]; 
			$batchpoid=$batchProdPoarr[$brow[csf("batch_id")]][$brow[csf("prod_id")]];
			$amt=$brow[csf("production_qty")]*($convRateArr[$batchpoid][$brow[csf("process_id")]][$batchcolor][$brow[csf("detarmination_id")]]['fdrate']);
			//if($brow[csf("process_id")]==67) echo $convRateArr[$transInPoId][$brow[csf("process_id")]][$batchcolor][$brow[csf("detarmination_id")]]['fdrate'];
			$specialDataArr[$brow[csf("process_id")]][$batchcolor][$fabDescription]['sp_qty']+=$brow[csf("production_qty")];
			$specialDataArr[$brow[csf("process_id")]][$batchcolor][$fabDescription]['sp_amt']+=$amt;
		}
		unset($sqlSPArr);
		//print_r($specialDataArr[67][7706]); die;
		$outsbpoid_cond=where_con_using_array($transInPoId,0,"c.po_break_down_id");
		$sqlOutSiteService="SELECT a.febric_description_id, a.process_id, a.wo_qty, a.batch_issue_qty, a.rate, a.amount, a.order_id, a.fin_dia, a.fin_gsm, a.grey_used, c.fabric_color_id, c.po_break_down_id, a.id
			FROM pro_grey_batch_dtls a, inv_receive_mas_batchroll b, wo_booking_dtls c
			WHERE a.mst_id = b.id AND a.booking_dtls_id = c.id AND b.entry_form = 92 AND c.is_deleted=0 AND a.is_deleted=0 AND b.is_deleted=0 $outsbpoid_cond";
		//echo $sqlOutSiteService;
		$resOutSiteService=sql_select($sqlOutSiteService);
		//print_r($resOutSiteService);
		$data_outsite_service=array(); $fab_deter_mina_ids=array(); $fin_dia_arr=array();
		foreach ($resOutSiteService as $row)
		{
			$fab_deter_mina_ids[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$fin_dia_arr[$fab_id]=$row[csf('fin_gsm')].",".$row[csf('fin_dia')];
		}
		//print_r($fab_deter_mina_ids); die;
		$determina_cond=where_con_using_array($fab_deter_mina_ids,0,"a.id");
		$composition_arr=array(); $constructtion_arr=array();
	    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $determina_cond";
	   // echo $sql_deter;
	   $lib_composition_array = return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name", "id", "composition_name");
	   //print_r($lib_composition_array); die;
	    $data_array=sql_select($sql_deter);
	    foreach($data_array as $row )
	    {
	    	$fab_id=$row[csf('id')];
	        if(array_key_exists($fab_id,$composition_arr))
	        {
	            $composition_arr[$fab_id]=$composition_arr[$fab_id].",".$lib_composition_array[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	        else
	        {
	            $composition_arr[$fab_id]=$row[csf('construction')].",".$lib_composition_array[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	    }
	    //print_r($composition_arr);
	    
	    foreach ($resOutSiteService as $row)
		{
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$product_details=$composition_arr[$row[csf('febric_description_id')]].",".$fin_dia_arr[$fab_id];
			if($row[csf('process_id')]==33)
			{
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('grey_used')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('grey_used')]*$row[csf('rate')]);
			}
			else
			{
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('batch_issue_qty')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('batch_issue_qty')]*$row[csf('rate')]);
			}
			/*if($row[csf('process_id')]==67)
			{
				echo $row[csf('fabric_color_id')].'-'.$product_details.'-'.$row[csf('process_id')].'-'.$row[csf('batch_issue_qty')].'-'.$row[csf('rate')].'<br>';
			}*/
		}
		//print_r($data_outsite_service[7706]); die;
		$heatpoid_cond=where_con_using_array($transInPoId,0,"b.po_id");
		$sql_heat_set="SELECT c.product_name_details, SUM(e.batch_qty) AS batch_qnty, a.color_id
						FROM pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c, pro_fab_subprocess d, pro_fab_subprocess_dtls e
						WHERE a.id = b.mst_id AND c.id=b.prod_id AND a.id=d.batch_id AND d.id=e.mst_id AND b.prod_id=e.prod_id AND c.id=e.prod_id AND a.entry_form = 0 AND d.entry_form = 32 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND a.is_deleted = 0 $heatpoid_cond
						GROUP BY c.product_name_details, c.detarmination_id, a.color_id";
		//echo $sql_heat_set;

		$res_heat_set=sql_select($sql_heat_set);

		$data_heat_set=array();
		//print_r($convRateArr[$poid][33]);
		foreach ($res_heat_set as $row) 
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row[csf('product_name_details')]));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$rate=0;
			$rate=$convRateArr[$row[csf('po_id')]][33][$row[csf('color_id')]][$row[csf("detarmination_id")]]['fdrate'];
			$data_heat_set[$row[csf('color_id')]][$fabDescription]['amount']+=($row[csf('batch_qnty')]*$rate);
			$data_heat_set[$row[csf('color_id')]][$fabDescription]['qnty']+=$row[csf('batch_qnty')];
		}
		$finpoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$dataArrayfinish = "select a.id as ID, a.entry_form as ENTRY_FORM, a.booking_id as BOOKINGID, a.knitting_source as KNITTING_SOURCE, a.receive_basis as RECEIVEBASIS, a.currency_id as CURRENCY_ID, b.rate as RATE, c.po_breakdown_id as POID, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, b.grey_used_qty as GREY_USED_QTY, b.receive_qnty as RECEIVE_QNTY, b.grey_fabric_rate as GREY_FABRIC_RATE, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2 $finpoid_cond";
		$dataArrayfinishArr=sql_select($dataArrayfinish);
		$bookingidArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				$bookingidArr[$row['BOOKINGID']]=$row['BOOKINGID'];
			}
		}
		$bookingid_cond=where_con_using_array($bookingidArr,0,"a.id");
		$servBookSql="select a.id, b.booking_no, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.dia_width, b.rate from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingid_cond";
		$servBookSqlArr=sql_select($servBookSql); $bookingRateArr=array();
		foreach($servBookSqlArr as $worow)
		{
			//echo $convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab'].'<br>';
			$conv_fabric="";
			
			if($worow[csf("dia_width")]=="") $conv_fabric=$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab'];
			else $conv_fabric=$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fabwo'].','.$worow[csf("dia_width")];
			//echo $conv_fabric.'<br>';
			$fabDescription="";
			$exFabric=array_filter(explode(",",$conv_fabric));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			
			$bookingProcessRateArr[$worow[csf("process")]][$fabDescription][$worow[csf("fabric_color_id")]]['worateprocess']=$worow[csf("rate")];
			$bookingRateArr[$worow[csf("id")]][$fabDescription][$worow[csf("fabric_color_id")]]['worate']=$worow[csf("rate")];
		}
		unset($servBookSqlArr);
		
		foreach ($res_outsite_service as $row)
		{
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$product_details=$composition_arr[$row[csf('febric_description_id')]].",".$fin_dia_arr[$fab_id];
			
			$finWoRate=$bookingProcessRateArr[$row[csf('process_id')]][$product_details][$row[csf('fabric_color_id')]]['worateprocess'];
			if($row[csf('process_id')]==33)
			{
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('grey_used')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('grey_used')]*$finWoRate);
			}
			else
			{
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('batch_issue_qty')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('batch_issue_qty')]*$finWoRate);
			}
		}
		
		$recDataRetArr=array(); $finishDataArr=array(); //$batchDataArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row['PRODUCT_NAME_DETAILS']));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			if($row['ENTRY_FORM']==7)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				//$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_amt']+=$amt;
				
				$recDataRetArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
			}
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				$avgQty=$amt=0;
				//$avgQty=((1-($row['QUANTITY']/$row['GREY_USED_QTY']))*$row['GREY_USED_QTY'])+$row['QUANTITY'];
				$avgQty=($row['GREY_USED_QTY']/$row['RECEIVE_QNTY'])*$row['QUANTITY'];
				//echo $avgQty.'='.$row['QUANTITY'].'='.$row['GREY_USED_QTY'].'='.'kausar<br>';
				$amt=$avgQty*($row['GREY_FABRIC_RATE']/82);
				//echo "<pre>".$amt."=".$avgQty."*(".$row['GREY_FABRIC_RATE']."/82)</pre>";
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['batch_qty']+=$avgQty;
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['batch_amt']+=$amt;
				$finWoRate=$bookingRateArr[$row['BOOKINGID']][$fabDescription][$row['COLOR_ID']]['worate'];
				$amt=$avgQty*$finWoRate;
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				$finishDataArr[$row['POID']][$fabDescription][$row['COLOR_ID']]['finrec_amt']+=$amt;
				
				$grey_used_amt=0;
				
				$grey_used_amt=$row['GREY_USED_QTY']*$finWoRate;
				//echo $row['GREY_USED_QTY'].'='.$bookingRateArr[$fabDescription][$row['COLOR_ID']]['worate'].'<br>';
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['dyeqtyrec']+=$row['GREY_USED_QTY'];
				$batchDataArr[$row['COLOR_ID']][$fabDescription]['dyeamtrec']+=$grey_used_amt;
			}
			//echo "<pre>".$row['ENTRY_FORM']."=".$row['KNITTING_SOURCE']."=".$row['RECEIVEBASIS']."</pre>";
		}
		unset($dataArrayfinishArr);
		//print_r($batchDataArr); die;
		$fintranspoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, b.batch_id as BATCH_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $fintranspoid_cond";
		
		$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array(); $transOutArr=array(); $transInArr=array(); $batchIDArr=array();
		foreach($sqlTransArr as $row)
		{
			$fabDescription="";
			$exFabric=array_filter(explode(",",$row['PRODUCT_NAME_DETAILS']));
			foreach($exFabric as $frow)
			{
				if($fabDescription=="") $fabDescription=trim($frow); else $fabDescription.=','.trim($frow);
			}
			$transVal=$amt=0;
			$transVal=$row['TRANSFER_VALUE']/82;
			//echo $recRate;
			if($row['TRANS_TYPE']==5)//trans in
			{
				/*if($batchEntryFormArr[$row['BATCH_ID']]==37)
				{*/
					//$amt=$row['QUANTITY']*$transRate;
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTin_qty']+=$row['QUANTITY'];
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTin_amt']+=$transVal;
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['trinpoid'].=$row['FROM_ORDER_ID'].',';
					$transInArr[$row['TO_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['batchid']=$row['BATCH_ID'];
					array_push($batchIDArr,$row['BATCH_ID']);
				//}
			}
			else if($row['TRANS_TYPE']==6)//trans out
			{
				//$amt=$row['QUANTITY']*$transRate;
				$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_qty']+=$row['QUANTITY'];
				$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_amt']+=$transVal;
				$transOutArr[$row['FROM_ORDER_ID']][$fabDescription][$row['COLOR_ID']]['finTout_ref'].=$row['TO_ORDER_ID'].',';
			}
			array_push($trnsPoIdArr,$row['TO_ORDER_ID']);
			array_push($trnsPoIdArr,$row['FROM_ORDER_ID']);
		}
		unset($sqlTransArr);
		//print_r($transOutArr);
		$finishTransInRateArr=array();
		foreach($batchDataArr as $fabcolorid=>$fabcolordata)
		{
			foreach($fabcolordata as $fabric=>$fabdata)
			{
				if($batchEntryArr[$colordata['batchid']]!=37)
				{
					$greyavgrate=0;
					$greyavgrate=$productWiseRate[$fabric]['amt']/$productWiseRate[$fabric]['qty'];
					$fabdata['batch_amt']=$greyavgrate*$fabdata['batch_qty']; // change batch amount 
					$poid=$fabdata['poid'];
					
					$dyeqty=$dyeamt=0;
					$dyeqty=$fabdata['batch_qty']+$fabdata['dyeqtyrec'];
					$dyeamt=$fabdata['dyeamt']+$fabdata['dyeamtrec'];
					
					$heatSetAmt=$data_heat_set[$fabcolorid][$fabric]['amount']+$data_outsite_service[$fabcolorid][$fabric][33]['amount'];
					$peachFinishAmt=$specialDataArr[67][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][67]['amount'];
					$brushingAmt=$specialDataArr[68][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][68]['amount'];
					$aopAmt=$specialDataArr[35][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][35]['amount'];
					$finishAmt=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_amt'];
					
					$finishFabCost=$fabdata['batch_amt']+$dyeamt+$peachFinishAmt+$brushingAmt+$aopAmt+$finishAmt+$heatSetAmt;
						
					$finishQty=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_qty'];
					$finRate=$finishFabCost/$finishQty;
					
					$ftitel=$fabdata['batch_amt'].'-'.$dyeamt.'-'.$peachFinishAmt.'-'.$brushingAmt.'-'.$aopAmt.'-'.$finishAmt.'<br>';
					//if($poid==18469) echo $ftitel.'='.$finRate;
					//echo $poid.'*'.$fabcolorid.'*'.$fabric.'*'.$bomTransinAvgRate.'<br>';
					//echo $specialDataArr[67][$fabcolorid][$fabric]['sp_amt'].'-'.$data_outsite_service[$fabcolorid][$fabric][67]['amount'].'-'.$peachFinishAmt.'-'.$fabric."<br>";
					//echo $poid.'<br>';
					$finishTransInRateArr[$fabcolorid][$fabric]=$finRate;
				}
			}
		}
		return $finishTransInRateArr;
	}
	//exit();
}

function fncGreyFinTransInRaterr($transInPoId,$type)
{
	//print_r($transInPoId); die;
	//Grey
	$yarnpoid_cond=where_con_using_array($transInPoId,0,"b.po_breakdown_id");
	
	$sqlGYIssue="SELECT a.id as issue_id, b.quantity as issue_qnty, b.prod_id, c.cons_rate, d.lot, d.brand_supplier, d.yarn_count_id, d.yarn_comp_type1st,d.product_name_details from inv_issue_master a, order_wise_pro_details b, inv_transaction c, product_details_master d
		where a.id=c.mst_id and c.transaction_type=2 and c.item_category=1 and c.id=b.trans_id and c.prod_id=d.id and b.trans_type=2 and b.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarnpoid_cond";
	//echo $sqlGYIssue; die;
	$sqlGYIssueRes=sql_select($sqlGYIssue); $greyYarnDtlsArr=array();
	foreach($sqlGYIssueRes as $isrow)
	{
		$str="";
		$str=$isrow[csf("yarn_count_id")].'**'.$isrow[csf("yarn_comp_type1st")].'**'.$isrow[csf("brand_supplier")].'**'.$isrow[csf("lot")];
		$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecdata']=$str;
		//$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecqty']+=$isrow[csf("issue_qnty")];
		$greyYarnDtlsArr[$isrow[csf("prod_id")]]['yrecrate']=($isrow[csf("cons_rate")]/82);
	}
	unset($sqlGYIssueRes);
	
	$greypoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
	$sqlGray="select a.id,b.id as dtls_id, b.prod_id, b.yarn_prod_id, b.febric_description_id, d.product_name_details, c.quantity as quantity, b.kniting_charge, b.order_yarn_rate,a.knitting_source from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, product_details_master d 
	 
	where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in (2) and c.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $greypoid_cond";
	//echo $sqlGray;
	$sqlGrayRec=sql_select($sqlGray); $greymstIdArr=array();
	foreach($sqlGrayRec as $grrow)
	{
		$greymstIdArr[$grrow[csf("id")]]=$grrow[csf("id")];
	}
	$recv_cond=where_con_using_array($greymstIdArr,0,"receive_id");

	$knitting_bill_sql="SELECT b.receive_id,b.currency_id,b.rate, a.company_id,a.bill_date FROM subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and a.entry_form=438 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_cond";
	//echo $knitting_bill_sql;
	$knitting_bill_res=sql_select($knitting_bill_sql);
	$recv_wise_knitting_charge=array();
	foreach ($knitting_bill_res as $row)
	{
		$con_rate=set_conversion_rate($row[csf('currency_id')], $row[csf('bill_date')],$row[csf('company_id')]);
		$recv_wise_knitting_charge[$row[csf('receive_id')]]=($row[csf('rate')]*$con_rate);
	}
	
	$greyMst_cond=where_con_using_array($greymstIdArr,0,"mst_id");
	
	$sqlYarn="select prod_id, used_qty,dtls_id,mst_id,amount from pro_material_used_dtls where entry_form=2 and status_active=1 and is_deleted=0 $greyMst_cond";
	//echo $sqlYarn;
	$sqlYarnUsed=sql_select($sqlYarn); $yusedArr=array(); $yusedArr1=array();
	foreach($sqlYarnUsed as $yurow)
	{
		$yusedArr[$yurow[csf("prod_id")]]['yqty']+=$yurow[csf("used_qty")];
		$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['yqty']+=$yurow[csf("used_qty")];
		$yusedArr1[$yurow[csf("prod_id")]][$yurow[csf("mst_id")]][$yurow[csf("dtls_id")]]['amount']+=$yurow[csf("amount")];
	}
	$grayDataArr=array(); $greyYarnIdArr=array(); $prodrateArr=array();
	//$prod_arr_used=array();
	foreach($sqlGrayRec as $grrow)
	{
		$grayDataArr[$grrow[csf("product_name_details")]]['yprodid'].=','.$grrow[csf("yarn_prod_id")];
		$grayDataArr[$grrow[csf("product_name_details")]]['grecqty']+=$grrow[csf("quantity")];
		if($grrow[csf("knitting_source")]==1)
		{
			$grayDataArr[$grrow[csf("product_name_details")]]['grecamt']+=$grrow[csf("quantity")]*($grrow[csf("kniting_charge")]/82);
			$prodrateArr[$grrow[csf("product_name_details")]]=($grrow[csf("kniting_charge")]/82);
		}
		else
		{
			$grayDataArr[$grrow[csf("product_name_details")]]['grecamt']+=$grrow[csf("quantity")]*($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
			$prodrateArr[$grrow[csf("product_name_details")]]=($recv_wise_knitting_charge[$grrow[csf('id')]]/82);
		}
		
		$exyarnid=explode(",",$grrow[csf("yarn_prod_id")]);
		
		foreach($exyarnid as $ynid)
		{
			$greyYarnDtlsArr[$grrow[csf("product_name_details")]][$ynid]['yrecqty']+=$yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['yqty'];
			$greyYarnDtlsArr[$grrow[csf("product_name_details")]][$ynid]['yrecamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);
			$grayDataArr[$grrow[csf("product_name_details")]]['yrntotamt']+=($yusedArr1[$ynid][$grrow[csf("id")]][$grrow[csf("dtls_id")]]['amount']/82);;
		}
	}
	unset($sqlGrayRec);
	
	$transInRateArr=array();
	foreach($grayDataArr as $gprodname=>$gprodnamedata)
	{
		$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
		$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
					
		$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
		$transoutAvgRate=fn_number_format($transoutAvgRate,8,".","");
		
		$productWiseRate[$gprodname]=$transoutAvgRate;
		
		$greyAvgPrice=$gprodnamedata['grecamt']/$gprodnamedata['grecqty'];
		$greytotamt=$gprodnamedata['yrntotamt']+($gprodnamedata['grecqty']*$greyAvgPrice);
		$transoutAvgRate=$greytotamt/$gprodnamedata['grecqty'];
		$transInRateArr[$gprodname]=$transoutAvgRate;
	}
	
	if($type==1)
	{
		return $transInRateArr;
	}
	else if ($type==2)//Finish
	{
		$stylepoid_cond=where_con_using_array($transInPoId,0,"b.id");
		$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $stylepoid_cond";
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes);
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
		/*if($db_type==2 && $cjobid>1000)
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
		}*/
		
		$jobidCond=where_con_using_array($ujobid,0,"a.job_id");
		$jobidCondition=where_con_using_array($ujobid,0,"job_id");
		//die;
		//echo "ff"; die;
		//Item Ratio Details
		$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1  $jobidCondition";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		}
		unset($gmtsitemRatioSqlRes);
		
		$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		}
		unset($sqlContrastRes);
		
		//Stripe Details
		$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
		}
		unset($sqlStripeRes);
		$fabpoid_cond=where_con_using_array($transInPoId,0,"b.po_break_down_id");
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.lib_yarn_count_deter_id as DETAID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.composition as COMPOSITION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.dia_width as DIA_WIDTH, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
	from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.fabric_source=1 $fabpoid_cond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $fabDescArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$fabIdWiseGmtsDataArr[$row['ID']]['CONSTRUCTION']=$row['CONSTRUCTION'];
			$fabIdWiseGmtsDataArr[$row['ID']]['DETAID']=$row['DETAID'];
			$fabcolorArr=array();
			if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
			{
				foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
				{
					$fabcolorArr[$row['ID']][$row['COLOR_NUMBER_ID']][$fabcolor]=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
				}
			}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			//$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
			//$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			
			$finAmt=$finReq*$row['RATE'];
			//$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			
			
			$fullfab=$row['CONSTRUCTION'].', '.$row['COMPOSITION'].', '.$row['GSM_WEIGHT'].', '.$row['DIA_WIDTH'];
			$fabDescArr[$row['ID']]['fab']=$fullfab;
			if($row['FABRIC_SOURCE']==2)
			{
				if(!empty($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip']))
				{
					foreach($sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['strip'] as $fabcolor)
					{
						$cons=0;
						$cons=$sqlStripeArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['fabreq'][$fabcolor];
						$finReq=($planQty/$itemRatio)*($cons/$costingPer);
						$finAmt=$finReq*$row['RATE'];
						
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
						$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
						//$reqQtyAmtArr[$row['POID']]['purchgrey_amt']+=$greyAmt;
					}
				}
				else if ($sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]!="" && $row['COLOR_SIZE_SENSITIVE']==3)
				{
					$cons=0;
					$fabcolor=$sqlContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']];
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$fabcolor][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
				else
				{
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$finAmt=$finReq*$row['RATE'];
					
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_qty']+=$finReq;
					//$reqQtyAmtArr[$row['POID']]['purchgrey_qty']+=$greyReq;
					$reqQtyAmtArr[$row['POID']][$fullfab][$row['COLOR_NUMBER_ID']][$row['UOM']]['purchfin_amt']+=$finAmt;
				}
			}
		}
		unset($sqlfabRes);
		
		$convpoid_cond=where_con_using_array($transInPoId,0,"a.po_break_down_id");
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $convpoid_cond";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convFabArr=array();
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
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
				}
			}
		}
		//echo "ff"; die;
		$convReqQtyAmtArr=array(); $convRateArr=array();
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
			$libYarnDetaid=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['DETAID'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			$convRateArr[$row['CONVERTION_ID']]['fab']=$fabDescArr[$row['PRECOSTID']]['fab'];
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
						$reqqnty=$qnty;
						$convAmt=$qnty*$convrate;
					}
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId][$stripe_color_id]['yamt']+=$convAmt;
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
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				else if($consProcessId==1 || $consProcessId==33)
				{
					$convrate=$row['CHARGE_UNIT'];
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty=$qnty;
					$convAmt=$qnty*$convrate;
				}
				//echo $convrate.'='.$row['CHARGE_UNIT'].'='.$itemRatio.'='.$requirment.'='.$costingPer."<br>";
				if($consProcessId==134)
				{
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yqty']+=$reqqnty;
					$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yamt']+=$convAmt;
				}
				if($consProcessId==1)
				{
					$fabconstruction=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['CONSTRUCTION'];
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kqty']+=$reqqnty;
					$convReqQtyAmtArr['knit'][$row['POID']][$consProcessId][$fabconstruction]['kamt']+=$convAmt;
				}
				if($consProcessId==31)
				{
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdqty']+=$reqqnty;
					$convReqQtyAmtArr['fd'][$row['POID']][$consProcessId][$rateColorId]['fdamt']+=$convAmt;
					
				}
				if($consProcessId==67 || $consProcessId==68 || $consProcessId==35)
				{
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaqty']+=$reqqnty;
					$convReqQtyAmtArr['pba'][$row['POID']][$consProcessId]['pba']['pbaamt']+=$convAmt;
				}
				$convRateArr[$row['POID']][$consProcessId][$rateColorId][$libYarnDetaid]['fdrate']=$convrate;
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes);
		//print_r($convRateArr);
		//return $convRateArr[54013][67][7706]; die;
		//echo $transInPoId.'jj'; die;
		
		$isspoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlIss="select b.color_id as COLOR_ID, c.prod_id as PROD_ID, c.po_breakdown_id as POID, b.rate as RATE, c.quantity as QUANTITY from inv_grey_fabric_issue_dtls b, order_wise_pro_details c where b.id=c.dtls_id and c.entry_form in (16) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $isspoid_cond ";
		$sqlIssArr=sql_select($sqlIss); $grayRateArr=array();
		foreach($sqlIssArr as $grow)
		{
			$grayRateArr[$grow["COLOR_ID"]][$grow["PROD_ID"]]['rate']=$grow["RATE"];
		}
		unset($sqlIssArr);
		//print_r($grayRateArr);die;
		$batchpoid_cond=where_con_using_array($transInPoId,0,"b.po_id");
		$sqlBatch = "select a.id, a.color_id, a.entry_form, b.po_id, b.prod_id, b.batch_qnty as quantity, c.product_name_details, c.detarmination_id from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchpoid_cond";
		$batchDataArr=array();
		$sqlBatchArr=sql_select($sqlBatch); $batchidarr=array(); $batchcolorarr=array(); $batchProdPoarr=array(); 
		foreach($sqlBatchArr as $brow)
		{
			//echo $grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate'].'-'.$convRateArr[$brow[csf("po_id")]][31][$brow[csf("color_id")]][$brow[csf("detarmination_id")]]['fdrate'].'i<br>';
			$amt=$brow[csf("quantity")]*($grayRateArr[$brow[csf("color_id")]][$brow[csf("prod_id")]]['rate']/82);
			$batchDataArr[$brow[csf("color_id")]][$brow[csf("product_name_details")]]['batch_qty']+=$brow[csf("quantity")];
			$batchDataArr[$brow[csf("color_id")]][$brow[csf("product_name_details")]]['batch_amt']+=$amt;
			$batchDataArr[$brow[csf("color_id")]][$brow[csf("product_name_details")]]['poid']=$brow[csf("po_id")];
			$batchidarr[$brow[csf("id")]]=$brow[csf("id")];
			$batchcolorarr[$brow[csf("id")]]=$brow[csf("color_id")];
			
			$batchProdPoarr[$brow[csf("id")]][$brow[csf("prod_id")]]=$brow[csf("po_id")];
			
			$batchDataArr[$brow[csf("color_id")]][$brow[csf("product_name_details")]]['dyeamt']+=$brow[csf("quantity")]*$convRateArr[$brow[csf("po_id")]][31][$brow[csf("color_id")]][$brow[csf("detarmination_id")]]['fdrate'];
			//echo "<pre>".$brow[csf("color_id")]."**".$brow[csf("product_name_details")]."=".$brow[csf("detarmination_id")]."</pre>";
		}
		unset($sqlBatchArr);
		//print_r($batchDataArr); die;
		
		$batchid_cond=where_con_using_array($batchidarr,0,"a.batch_id");
		
		$sqlSP="select a.batch_id, a.process_id, b.prod_id, b.production_qty, c.product_name_details, c.detarmination_id from pro_fab_subprocess a, pro_fab_subprocess_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=34 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batchid_cond";
		$sqlSPArr=sql_select($sqlSP); $specialDataArr=array();
		foreach($sqlSPArr as $brow)
		{
			$batchcolor=$batchcolorarr[$brow[csf("batch_id")]]; 
			$batchpoid=$batchProdPoarr[$brow[csf("batch_id")]][$brow[csf("prod_id")]];
			$amt=$brow[csf("production_qty")]*($convRateArr[$batchpoid][$brow[csf("process_id")]][$batchcolor][$brow[csf("detarmination_id")]]['fdrate']);
			//if($brow[csf("process_id")]==67) echo $convRateArr[$transInPoId][$brow[csf("process_id")]][$batchcolor][$brow[csf("detarmination_id")]]['fdrate'];
			$specialDataArr[$brow[csf("process_id")]][$batchcolor][$brow[csf("product_name_details")]]['sp_qty']+=$brow[csf("production_qty")];
			$specialDataArr[$brow[csf("process_id")]][$batchcolor][$brow[csf("product_name_details")]]['sp_amt']+=$amt;
		}
		unset($sqlSPArr);
		//print_r($specialDataArr[67][7706]); die;
		$outsbpoid_cond=where_con_using_array($transInPoId,0,"c.po_break_down_id");
		$sqlOutSiteService="SELECT a.febric_description_id, a.process_id, a.wo_qty, a.batch_issue_qty, a.rate, a.amount, a.order_id, a.fin_dia, a.fin_gsm, a.grey_used, c.fabric_color_id, c.po_break_down_id, a.id
			FROM pro_grey_batch_dtls a, inv_receive_mas_batchroll b, wo_booking_dtls c
			WHERE a.mst_id = b.id AND a.booking_dtls_id = c.id AND b.entry_form = 92 AND c.is_deleted=0 AND a.is_deleted=0 AND b.is_deleted=0 $outsbpoid_cond";
		//echo $sqlOutSiteService;
		$resOutSiteService=sql_select($sqlOutSiteService);
		//print_r($resOutSiteService);
		$data_outsite_service=array(); $fab_deter_mina_ids=array(); $fin_dia_arr=array();
		foreach ($resOutSiteService as $row)
		{
			$fab_deter_mina_ids[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$fin_dia_arr[$fab_id]=$row[csf('fin_gsm')].", ".$row[csf('fin_dia')];
		}
		//print_r($fab_deter_mina_ids); die;
		$determina_cond=where_con_using_array($fab_deter_mina_ids,0,"a.id");
		$composition_arr=array(); $constructtion_arr=array();
	    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $determina_cond";
	   // echo $sql_deter;
	   $lib_composition_array = return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name", "id", "composition_name");
	   //print_r($lib_composition_array); die;
	    $data_array=sql_select($sql_deter);
	    foreach($data_array as $row )
	    {
	    	$fab_id=$row[csf('id')];
	        if(array_key_exists($fab_id,$composition_arr))
	        {
	            $composition_arr[$fab_id]=$composition_arr[$fab_id].", ".$lib_composition_array[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	        else
	        {
	            $composition_arr[$fab_id]=$row[csf('construction')].", ".$lib_composition_array[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	        }
	    }
	    //print_r($composition_arr);
	    
	    foreach ($resOutSiteService as $row)
		{
			$fab_id=$row[csf('febric_description_id')]."##".$row[csf('id')];
			$product_details=$composition_arr[$row[csf('febric_description_id')]].", ".$fin_dia_arr[$fab_id];
			if($row[csf('process_id')]==33)
			{
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('grey_used')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('grey_used')]*$row[csf('rate')]);
			}
			else
			{
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['qnty']+=$row[csf('batch_issue_qty')];
				$data_outsite_service[$row[csf('fabric_color_id')]][$product_details][$row[csf('process_id')]]['amount']+=($row[csf('batch_issue_qty')]*$row[csf('rate')]);
			}
			/*if($row[csf('process_id')]==67)
			{
				echo $row[csf('fabric_color_id')].'-'.$product_details.'-'.$row[csf('process_id')].'-'.$row[csf('batch_issue_qty')].'-'.$row[csf('rate')].'<br>';
			}*/
		}
		//print_r($data_outsite_service[7706]); die;
		$heatpoid_cond=where_con_using_array($transInPoId,0,"b.po_id");
		$sql_heat_set="SELECT b.po_id, c.product_name_details, c.detarmination_id, b.batch_qnty AS batch_qnty, a.color_id
						FROM pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c
						WHERE a.id = b.mst_id AND a.entry_form = 0 AND c.id=b.prod_id AND b.status_active = 1 AND b.is_deleted = 0  AND a.is_deleted = 0 $heatpoid_cond";
		//echo $sql_heat_set;

		$res_heat_set=sql_select($sql_heat_set);

		$data_heat_set=array();
		//print_r($convRateArr[$poid][33]);
		foreach ($res_heat_set as $row) 
		{
			$rate=0;
			$rate=$convRateArr[$row[csf('po_id')]][33][$row[csf('color_id')]][$row[csf("detarmination_id")]]['fdrate'];
			$data_heat_set[$row[csf('color_id')]][$row[csf('product_name_details')]]['amount']+=($row[csf('batch_qnty')]*$rate);
			$data_heat_set[$row[csf('color_id')]][$row[csf('product_name_details')]]['qnty']+=$row[csf('batch_qnty')];
		}
		$finpoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$dataArrayfinish = "select a.id as ID, a.entry_form as ENTRY_FORM, a.booking_id as BOOKINGID, a.knitting_source as KNITTING_SOURCE, a.receive_basis as RECEIVEBASIS, a.currency_id as CURRENCY_ID, b.rate as RATE, c.po_breakdown_id as POID, c.trans_type as TRANS_TYPE, c.prod_id as PROD_ID, c.color_id as COLOR_ID, c.quantity as QUANTITY, b.grey_used_qty as GREY_USED_QTY, b.receive_qnty as RECEIVE_QNTY, b.grey_fabric_rate as GREY_FABRIC_RATE, d.product_name_details as PRODUCT_NAME_DETAILS, d.unit_of_measure as UOM
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and d.item_category_id=2 $finpoid_cond";
		$dataArrayfinishArr=sql_select($dataArrayfinish);
		$bookingidArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				$bookingidArr[$row['BOOKINGID']]=$row['BOOKINGID'];
			}
		}
		$bookingid_cond=where_con_using_array($bookingidArr,0,"a.id");
		$servBookSql="select b.booking_no, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.dia_width, b.rate from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingid_cond";
		$servBookSqlArr=sql_select($servBookSql); $bookingRateArr=array();
		foreach($servBookSqlArr as $worow)
		{
			//echo $convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab'].'<br>';
			$bookingRateArr[$convRateArr[$worow[csf("pre_cost_fabric_cost_dtls_id")]]['fab']][$worow[csf("fabric_color_id")]]['worate']=$worow[csf("rate")];
		}
		unset($servBookSqlArr);
		
		$recDataRetArr=array(); $finishDataArr=array(); //$batchDataArr=array();
		foreach($dataArrayfinishArr as $row)
		{
			if($row['ENTRY_FORM']==7)
			{
				$amt=$row['QUANTITY']*($row['RATE']/82);
				$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				//$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_amt']+=$amt;
				
				$recDataRetArr[$row['ID']][$row['POID']][$row['PROD_ID']][$row['COLOR_ID']]['rate']=($row['RATE']/82);
			}
			if($row['ENTRY_FORM']==37 && $row['KNITTING_SOURCE']==3 && $row['RECEIVEBASIS']==11)
			{
				//$avgQty=((1-($row['QUANTITY']/$row['GREY_USED_QTY']))*$row['GREY_USED_QTY'])+$row['QUANTITY'];
				$avgQty=($row['GREY_USED_QTY']/$row['RECEIVE_QNTY'])*$row['QUANTITY'];
				//echo $avgQty.'='.$row['QUANTITY'].'='.$row['GREY_USED_QTY'].'='.'kausar<br>';
				$amt=$avgQty*($row['GREY_FABRIC_RATE']/82);
				//echo "<pre>".$amt."=".$avgQty."*(".$row['GREY_FABRIC_RATE']."/82)</pre>";
				$batchDataArr[$row['COLOR_ID']][$row['PRODUCT_NAME_DETAILS']]['batch_qty']+=$avgQty;
				$batchDataArr[$row['COLOR_ID']][$row['PRODUCT_NAME_DETAILS']]['batch_amt']+=$amt;
				$finWoRate=$bookingRateArr[$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['worate'];
				$amt=$avgQty*$finWoRate;
				$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_qty']+=$row['QUANTITY'];
				$finishDataArr[$row['POID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finrec_amt']+=$amt;
			}
			//echo "<pre>".$row['ENTRY_FORM']."=".$row['KNITTING_SOURCE']."=".$row['RECEIVEBASIS']."</pre>";
		}
		unset($dataArrayfinishArr);
		//print_r($batchDataArr); die;
		$fintranspoid_cond=where_con_using_array($transInPoId,0,"c.po_breakdown_id");
		$sqlTrans="SELECT a.from_order_id as FROM_ORDER_ID, a.to_order_id as TO_ORDER_ID, b.from_prod_id as FROM_PROD_ID, b.uom as UOM, b.rate as RATE, b.transfer_value as TRANSFER_VALUE, c.trans_type as TRANS_TYPE, b.batch_id as BATCH_ID, c.po_breakdown_id as POID, c.color_id as COLOR_ID, c.quantity as QUANTITY, d.product_name_details as PRODUCT_NAME_DETAILS from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type in (5,6) and c.entry_form in (14,15,134) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $fintranspoid_cond";
		
		$sqlTransArr=sql_select($sqlTrans); $trnsPoIdArr=array(); $transOutArr=array(); $transInArr=array(); $batchIDArr=array();
		foreach($sqlTransArr as $row)
		{
			$transVal=$amt=0;
			$transVal=$row['TRANSFER_VALUE']/82;
			//echo $recRate;
			if($row['TRANS_TYPE']==5)//trans in
			{
				/*if($batchEntryFormArr[$row['BATCH_ID']]==37)
				{*/
					//$amt=$row['QUANTITY']*$transRate;
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTin_qty']+=$row['QUANTITY'];
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTin_amt']+=$transVal;
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['trinpoid'].=$row['FROM_ORDER_ID'].',';
					$transInArr[$row['TO_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['batchid']=$row['BATCH_ID'];
					array_push($batchIDArr,$row['BATCH_ID']);
				//}
			}
			else if($row['TRANS_TYPE']==6)//trans out
			{
				//$amt=$row['QUANTITY']*$transRate;
				$transOutArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTout_qty']+=$row['QUANTITY'];
				$transOutArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTout_amt']+=$transVal;
				$transOutArr[$row['FROM_ORDER_ID']][$row['PRODUCT_NAME_DETAILS']][$row['COLOR_ID']]['finTout_ref'].=$row['TO_ORDER_ID'].',';
			}
			array_push($trnsPoIdArr,$row['TO_ORDER_ID']);
			array_push($trnsPoIdArr,$row['FROM_ORDER_ID']);
		}
		unset($sqlTransArr);
		//print_r($transOutArr);
		$finishTransInRateArr=array();
		foreach($batchDataArr as $fabcolorid=>$fabcolordata)
		{
			foreach($fabcolordata as $fabric=>$fabdata)
			{
				if($batchEntryArr[$colordata['batchid']]!=37)
				{
					$fabdata['batch_amt']=$productWiseRate[$fabric]*$fabdata['batch_qty']; // change batch amount 
					$poid=$fabdata['poid'];
					
					
					$heatSetAmt=$data_heat_set[$fabcolorid][$fabric]['amount']+$data_outsite_service[$fabcolorid][$fabric][33]['amount'];
					$peachFinishAmt=$specialDataArr[67][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][67]['amount'];
					$brushingAmt=$specialDataArr[68][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][68]['amount'];
					$aopAmt=$specialDataArr[35][$fabcolorid][$fabric]['sp_amt']+$data_outsite_service[$fabcolorid][$fabric][35]['amount'];
					$finishAmt=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_amt'];
					
					$finishFabCost=$fabdata['batch_amt']+$fabdata['dyeamt']+$peachFinishAmt+$brushingAmt+$aopAmt+$finishAmt+$heatSetAmt;
						
					$finishQty=$finishDataArr[$poid][$fabric][$fabcolorid]['finrec_qty'];
					$finRate=$finishFabCost/$finishQty;
					
					//echo $ftitel=$fabdata['batch_amt'].'-'.$fabdata['dyeamt'].'-'.$peachFinishAmt.'-'.$brushingAmt.'-'.$aopAmt.'-'.$finishAmt.'<br>';
					//echo $specialDataArr[67][$fabcolorid][$fabric]['sp_amt'].'-'.$data_outsite_service[$fabcolorid][$fabric][67]['amount'].'-'.$peachFinishAmt.'-'.$fabric."<br>";
					
					$finishTransInRateArr[$fabcolorid][$fabric]=$finRate;
				}
			}
		}
		return $finishTransInRateArr;
	}
	//exit();
}

if($action=="panalty_popup")
{
	echo load_html_head_contents("Excess Mat. Cost", "../../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('__',$data);
	$tot=$expData[0]+$expData[1]+$expData[2]+$expData[3];
	$claimPodata=explode('_', $expData[4]);
	
	//echo $claimPodata[0];
	$sqlpo="select b.job_no_mst AS JOB_NO, b.grouping as GROUPING from wo_po_break_down b where b.is_deleted=0 and b.status_active=1 and b.id in ($claimPodata[0]) group by b.job_no_mst, b.grouping";
	$sqlpoRes = sql_select($sqlpo); $jobnref="";
	foreach($sqlpoRes as $row)
	{
		if(  count(explode(",",$claimPodata[0]))<2) $jobnref='For Job No : '.$row["JOB_NO"].'; IR No : '.$row["GROUPING"];
	}
	unset($sqlpoRes);
	//$clain_poid=
	?>
	<script type="text/javascript">
		function fnc_dtlslist(poid,buyer){
			show_list_view (poid+'__'+buyer, 'claim_details_list_view', 'search_div', 'business_analysis_report_controller', '');
		}
	</script>
	<fieldset style="width:500px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="5">Penalty <?=$jobnref; ?></th>
                    </tr>
                    <tr>
                        <th width="100">Claim</th>
                        <th width="100">Air Freight</th>
                        <th width="100">Sea Freight</th>
                        <th width="100">Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="right"><?=fn_number_format($expData[0]); ?></td>
                        <td align="right"><a href="#report_details" onClick="fnc_dtlslist('<? echo $claimPodata[0]; ?>',<? echo $claimPodata[1]; ?>);"><?=fn_number_format($expData[1]); ?></a></td>
                        <td align="right"><?=fn_number_format($expData[2]); ?></td>
                        <td align="right"><?=fn_number_format($expData[3]); ?></td> 
                        <td align="right"><?=fn_number_format($tot); ?></td>
                    </tr>
                </tbody>
            </table>
            </div> 
	</fieldset>
	<div style="margin-top:15px" id="search_div"></div>
	<?
	exit();
}

if($action=="claim_details_list_view")
{
	echo load_html_head_contents("Air cost Breakdown", "../../../../", 1, 1,$unicode,'','');
	//echo $poid.'=';//die;
	$expData=explode('__',$data);
	$sqlClaim="select po_id, base_on_ex_val as claim, air_freight, sea_freight, discount, claim_entry_date from wo_buyer_claim_mst where status_active=1 and is_deleted=0 and po_id in ($expData[0])";
	//echo $sqlClaim; die;
	$sqlClaimRes=sql_select($sqlClaim); $claim_year_arr=array();
	foreach($sqlClaimRes as $crow)
	{
		$year=date('Y',strtotime($crow[csf("claim_entry_date")]));
		$month=date("F",strtotime($crow[csf("claim_entry_date")]));
		$claim_year_arr[$year][$month]['air']+=$crow[csf("air_freight")];
	}
	?>
	<fieldset style="width:320px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr><th colspan="3">Air cost Breakdown</th></tr>
                    <tr>
                        <th width="100">Year</th>
                        <th width="100">Month</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <? foreach($claim_year_arr as $y=>$mdata) {
                	foreach ($mdata as $m => $data) { ?>
                	 	<tr>
                        <td style="word-break:break-all"><?=$y; ?></td>
                        <td style="word-break:break-all"><?=$m; ?></td>
                        <td style="word-break:break-all"><?= fn_number_format($data['air']); ?></td>
                    	</tr>
                	<? } 
                    
                     } ?>
                </tbody>
            </table>
            </div> 
	</fieldset>
	<?
	exit();
}

if($action=="podetails_popup")
{
	echo load_html_head_contents("PO Remarks Popup", "../../../../", 1, 1,$unicode,'','');
	//echo $data;//die;
	
	$sqlpo="select b.job_no_mst AS JOB_NO, b.grouping as GROUPING from wo_po_break_down b where b.is_deleted=0 and b.status_active=1 and b.id='$data' group by b.job_no_mst, b.grouping";
	$sqlpoRes = sql_select($sqlpo); $jobnref="";
	foreach($sqlpoRes as $row)
	{
		if($data!="")$jobnref='For Job No : '.$row["JOB_NO"].'; IR No : '.$row["GROUPING"];
	}
	unset($sqlpoRes);
	
	$sqlpo="select job_no as job_no_mst, acc_po_no as po_number, remarks as details_remarks from wo_po_acc_po_info where po_break_down_id=$data and remarks is not null";
	$sqlpores=sql_select($sqlpo);
	?>
	<fieldset style="width:400px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="3"><?=$jobnref; ?></th>
                    </tr>
                    <tr>
                        <th width="100">Job</th>
                        <th width="100">Actual Po No</th>
                        <th>Po Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <? foreach($sqlpores as $row) { ?>
                    <tr>
                        <td style="word-break:break-all"><?=$row[csf("job_no_mst")]; ?></td>
                        <td style="word-break:break-all"><?=$row[csf("po_number")]; ?></td>
                        <td style="word-break:break-all"><?=$row[csf("details_remarks")]; ?></td>
                    </tr>
                    <? } ?>
                </tbody>
            </table>
            </div> 
	</fieldset>
	<?
	exit();
}

if($action=="input_to_ship")
{
	echo load_html_head_contents("Shipment performance", "../../../../", 1, 1,$unicode,'','');
	$data=explode("***", $data);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	$cbo_company=$data[0];
	$cbo_buyer=$data[1];
	//$cbo_buyer=358;
	$season_id=$data[2];
	$txt_po_id=$data[3];
	$cbo_location=$data[4];
	$shipStatus=$data[5];
	$orderStatus=$data[6];
	$cbo_status=$data[7];
	$cbo_client=$data[8];
	$txt_style_ref=$data[9];
	$from_year=$data[10];
	//$from_year='2023-2024';
	$to_year=$data[11];

	$sql_cond='';
	
	if($shipStatus==1) $sql_cond.=" and b.shiping_status in (1,2)"; else if($shipStatus==2) $sql_cond.=" and b.shiping_status in (3)"; else $shipStatusCond="";
	/*if(!empty($orderStatus)) $sql_cond.=" and b.is_confirmed in ( $orderStatus )";
	if(!empty($season_id)) $sql_cond.=" and a.season_buyer_wise in ( $season_id )";
	if(!empty($client_id)) $sql_cond.=" and a.client_id in ( $client_id )";
	if(!empty(trim($style_ref))) $sql_cond.=" and a.style_ref_no='$style_ref'";
	if(!empty(trim($txt_po_id))) $sql_cond.=" and b.id in ($txt_po_id)";
	if(!empty(trim($cbo_status))) $sql_cond.=" and b.status_active in ($cbo_status)";*/
	if($orderStatus!=0) $sql_cond.=" and b.is_confirmed in ( $orderStatus )";
	if($season_id!=0) $sql_cond.=" and a.season_buyer_wise in ( $season_id )";
	if($client_id!=0) $sql_cond.=" and a.client_id in ( $client_id )";
	if(trim($style_ref)!="") $sql_cond.=" and a.style_ref_no='$style_ref'";
	if(trim($txt_po_id)!="") $sql_cond.=" and b.id in ($txt_po_id)";
	if(trim($cbo_status)!=0) $sql_cond.=" and b.status_active in ($cbo_status)";

	$exfirstYear=explode('-',$from_year);
	$exlastYear=explode('-',$to_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exlastYear[1];
	$yearMonth_arr=array(); $yearStartEnd_arr=array(); $j=12; $i=1;
	$startDate=''; $endDate="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$yearMonth_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$date_from=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$date_to=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	
		if ($date_from && $date_to ) 
		{
			if ($db_type == 0) {
				$sql_cond .= " and b.shipment_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and b.shipment_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
			}
		}
		
		if ($cbo_company>0) $sql_cond .= " and dm.delivery_company_id = $cbo_company";
		if ($cbo_location>0) $sql_cond .= " and dm.delivery_location_id = $cbo_location";
		if ($cbo_buyer>0) $sql_cond .= " and a.buyer_name = $cbo_buyer";
		//if ($cbo_dealing_merchant>0) $sql_cond .= " and a.DEALING_MARCHANT = $cbo_dealing_merchant";
		
		//
		$sql_ret= " and a.shipment_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
		$sql_res=sql_select("select a.job_no_mst, a.grouping, b.po_break_down_id as po_id, c.color_size_break_down_id, c.production_qnty as return_qnty, d.order_rate
                from pro_ex_factory_mst b, pro_ex_factory_dtls c, wo_po_color_size_breakdown d, wo_po_break_down a
                where b.id = c.mst_id and c.color_size_break_down_id = d.id and a.id=d.po_break_down_id and b.entry_form = 85 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 $sql_ret");
		
		$ex_return_qty_arr=array(); $jobirno="";
		foreach($sql_res as $row)
		{
			$ex_return_value =  $row[csf('return_qnty')]*$row[csf('order_rate')];
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_qty']=$row[csf('return_qnty')];                    
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_value']=$ex_return_value;
			$ex_return_qty_arr[$row[csf('po_id')]]['color_size_list'].=$row[csf('color_size_break_down_id')].",";
			
			$jobirno="Job No : ".$row[csf('job_no_mst')].'; IR No : '.$row[csf('grouping')];
		}   
	   
	  /* $sql_country_w="SELECT  dm.delivery_company_id, dm.delivery_location_id, m.id, m.po_break_down_id, m.foc_or_claim, m.ex_factory_date, m.ex_factory_qnty as ex_qnty, a.buyer_name,a.DEALING_MARCHANT, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.ship_mode, a.client_id, b.po_number, b.shipment_date, b.pub_shipment_date, b.grouping, b.shiping_status, c.country_ship_date, c.order_quantity, c.order_rate, c.order_total, c.shiping_status as cshiping_status, d.production_qnty, d.production_qnty*c.order_rate as ex_value, c.id as color_size, m.actual_po $delayShortSelect
        from pro_ex_factory_delivery_mst dm, pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,
        pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id and a.id = c.job_id and a.id = b.job_id and b.id = c.po_break_down_id and m.id = d.mst_id $sql_cond and d.color_size_break_down_id = c.id  and dm.delivery_company_id<>0 and m.entry_form<>85 and dm.delivery_company_id is not null and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and length(m.actual_po)>0  order by m.id";*/
		//$actual_po = sql_select("SELECT actual_po_id,actual_po_dtls_id,sum(ex_fact_qty) as ex_fact_qty from pro_ex_factory_actual_po_details where mst_id = $mst_id group by actual_po_id,actual_po_dtls_id");
		$sql_country_w="SELECT dm.delivery_company_id, dm.delivery_location_id, m.id, m.po_break_down_id, m.foc_or_claim, m.actual_po, m.ex_factory_date, m.ex_factory_qnty as ex_qnty, a.buyer_name, a.DEALING_MARCHANT, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.ship_mode, a.client_id, b.po_number, b.shipment_date, b.pub_shipment_date, b.grouping, b.shiping_status,
		 
		  c.id as color_size, c.acc_ship_date as country_ship_date, e.po_qty as order_quantity, e.unit_price as order_rate, e.unit_value as order_total,
		  d.ex_fact_qty as production_qnty, d.ex_fact_qty *e.unit_price as ex_value
		  
		  
        from pro_ex_factory_delivery_mst dm, pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,
        pro_ex_factory_actual_po_details d, wo_po_acc_po_info_dtls e
        where dm.id = m.delivery_mst_id and a.id = b.job_id and a.id = c.job_id and b.id = c.po_break_down_id and m.id = d.mst_id and c.id=e.mst_id and b.id=e.po_break_down_id $sql_cond and c.id=d.actual_po_id and d.actual_po_dtls_id = e.id  and dm.delivery_company_id<>0 and m.entry_form<>85 and dm.delivery_company_id is not null and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and length(d.actual_po_id)>0  order by m.id";
		
		//echo $sql_country_w;die;
    
	    $country_w_res = sql_select($sql_country_w);
	    $data_buyer_wise = array(); $qntyChkArr = array(); $result_array= array(); $jobPoWiseArr= array(); $poIds=''; 
	    $actual_po_arr=array(); $po_id_arrs=array(); $client_id_arr=array();
	    foreach($country_w_res as  $row)
	    {
	    	array_push($po_id_arrs, $row[csf('po_break_down_id')]);
	    	$act=explode(",", $row[csf('actual_po')]);
	    	foreach ($act as $key => $act_po) 
	    	{
	    		array_push($actual_po_arr, $act_po);
	    	}
			$client_id_arr[$row[csf('po_break_down_id')]]=$row[csf('client_id')];
			if($txt_po_id!="") $jobirno=" For Job No : ".$row[csf('job_no')].'; IR No : '.$row[csf('grouping')];
		}
		//CLIENT_ID
		//$po_id_cond=where_con_using_array(array_filter(array_unique($po_id_arrs)),0,"b.id");
		//$client_id_arr=return_library_array( "select a.client_id,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and b.is_deleted=0  $po_id_cond order by id desc", "id", "client_id"  );

	   // $actual_po_cond=where_con_using_array(array_filter(array_unique($actual_po_arr)),0,"id");

	    //$sql_actutal_po="SELECT acc_ship_date,po_break_down_id FROM wo_po_acc_po_info where status_active=1 AND is_deleted=0 $actual_po_cond";
	    //$actutal_po_data=return_library_array( "select id,acc_ship_date from wo_po_acc_po_info where status_active=1 and is_deleted=0 $actual_po_cond order by id desc", "id", "acc_ship_date"  );	
	   
	    foreach($country_w_res as  $row)
	    {
			$poIds.=$row[csf("po_break_down_id")].",";
	        $buffer_time = 0;
	        $client_id=$client_id_arr[$row[csf("po_break_down_id")]];
	        if($buyerArr[$row[csf("buyer_name")]]["buffer_time"])
	        {
	            $buffer_time = "+".$buyerArr[$row[csf('buyer_name')]]['buffer_time']." days";
	        }
			//echo $row[csf("pub_shipment_date")]; die;
			$shipDate=$shippingStatus="";
			
			$acc_ship_date=
			$expt=explode(",", $row[csf('actual_po')]);
			//echo "<pre>". $actutal_po_data[$expt[0]]."</pre>";
			$time_diff_buffer = strtotime($row[csf('country_ship_date')]) - strtotime($row[csf("ex_factory_date")]);
			

			$time_diff = strtotime($row[csf('country_ship_date')]) - strtotime($row[csf("ex_factory_date")]);
			$shipDate=$row[csf('country_ship_date')];
			$shippingStatus=$row[csf("shiping_status")];
			
			$poStr="";
			
			$poStr=$row[csf("buyer_name")].'__'.$row[csf("job_no")].'__'.$row[csf("style_ref_no")].'__'.$row[csf("po_number")].'__'.$row[csf("ship_mode")].'__'.$shipDate.'__'.$shippingStatus.'__'.$row[csf("po_break_down_id")].'__'.$row[csf("DEALING_MARCHANT")];

	        $data_buyer_wise[$row[csf("buyer_name")]]["ex_qnty"] += $row[csf("production_qnty")];
			$data_buyer_wise[$row[csf("buyer_name")]]["ex_val"] += $row[csf("ex_value")];
			if($row[csf("foc_or_claim")]==2){
				$data_buyer_wise[$row[csf("buyer_name")]]["exClaimQnty"] += $row[csf("production_qnty")];
				
				$result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['exClaimQnty'] += $row[csf("production_qnty")];
			}
			
	        if($time_diff_buffer > 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["early"] += $row[csf("production_qnty")];
				
				$result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['early'] += $row[csf("production_qnty")];
				
				//$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['early']+= $row[csf("production_qnty")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['early']+= $row[csf("production_qnty")];
	        }
	        else if($time_diff_buffer < 0 )
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["delay"] += $row[csf("production_qnty")];

				
				$result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['delay'] += $row[csf("production_qnty")];

				
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['delay']+= $row[csf("production_qnty")];
	        }
	        else if($time_diff_buffer == 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["ontime"] += $row[csf("production_qnty")];

				
				$result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['ontime'] += $row[csf("production_qnty")];

				
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['ontime']+= $row[csf("production_qnty")];
	        }

	        $result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("production_qnty")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("ex_value")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";
			
			
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]]['ex_qnty']+= $row[csf("production_qnty")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]]['ex_value']+= $row[csf("ex_value")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]]['ex_date']=$row[csf("ex_factory_date")];
			
			if (!in_array($row[csf("color_size")],$color_sizeTmpArr) )
	        {
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]]['poQty']+=$row[csf("order_quantity")]; 
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]]['poVal']+=$row[csf("order_total")];   
				$color_sizeTmpArr[]=$row[csf('color_size')];
			}
	    }
	
		/*$poIds=implode(",",array_filter(array_unique(explode(",",$poIds))));
		$tot_rows=count(explode(",",$poIds));
		$poIds_country_cond="";
		
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_country_cond=" and (";
			
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_country_cond.=" a.id in($ids) or ";
			}
			$poIds_country_cond=chop($poIds_country_cond,'or ');
			$poIds_country_cond.=")";
		}
		else
		{
			$poIds_country_cond=" and a.id in ($poIds)";
		}
	
		$sqlQty="SELECT a.id, a.shipment_date, a.pub_shipment_date, b.country_ship_date, c.production_qnty 
	        from wo_po_break_down a, wo_po_color_size_breakdown b, pro_ex_factory_dtls c
	        where a.id = b.po_break_down_id and c.color_size_break_down_id = b.id and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $poIds_country_cond";
		$sqlQty_res = sql_select($sqlQty); $totExQtyArr=array();
		foreach($sqlQty_res as  $row)
		{
			$shipDate="";
			if($cbo_type == 1) $shipDate=$row[csf("pub_shipment_date")];
			else if($cbo_type == 2) $shipDate=$row[csf("country_ship_date")];
			else if($cbo_type == 3) $shipDate=$row[csf("shipment_date")];
			
			$totExQtyArr[$row[csf("id")]][$shipDate]+=$row[csf("production_qnty")];
		}
		unset($sqlQty_res);*/

		//print_r($result_array);
	?>
	<fieldset style="width:700px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
						<tr>
							<td colspan="7" align="center" style="font-size:15px; font-weight:bold;">Export Summary By Shipment Performance <?=$jobirno; ?></td>
						</tr>  
						<tr>
							<th width="110" rowspan="2" style="word-break: break-all;">Ex-Factory Qty</th>
                            <th colspan="2" style="word-break: break-all;">Early</th>
							<th colspan="2" style="word-break: break-all;">On Time</th>
							<th colspan="2" style="word-break: break-all;">Delay</th>
						</tr>
                        <tr>
							<th width="70" style="word-break: break-all;">Qty</th>
							<th width="50" style="word-break: break-all;">%</th>
							<th width="70" style="word-break: break-all;">Qty</th>
							<th width="50" style="word-break: break-all;">%</th>
							<th width="70" style="word-break: break-all;">Qty</th>
							<th width="50">%</th>
							
						</tr>
					</thead>
                <tbody>
                	<? $m=1;
                		foreach($result_array as $company_id => $company_data)
						{
							foreach($company_data as $buyer_id => $row)
							{
								if ($m % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$po_arr =  array_filter(array_unique(explode(",",chop($row["po_id"],","))));
								$ex_return_qnty = 0;$ex_return_value=0;
								foreach($po_arr as $po_id)
								{
									$color_size_arr = array_filter(array_unique(explode(",",chop($ex_return_qty_arr[$po_id]["color_size_list"],","))));
									
									foreach($color_size_arr as $color_size_id)
									{
										$ex_return_qnty +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_qty'];
										$ex_return_value +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_value'];
									}
								}
								$ex_qnty_after_return=  $row["ex_qnty"] ;//- $ex_return_qnty;
								$ex_value_after_return=  $row["ex_value"];// - $ex_return_value;
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
								<?
								$row["po_id"]; $row["color_size"];
								
								$earlyPer = ($row["early"]/$row["ex_qnty"])*100;
								$onTimePer = ($row["ontime"]/$row["ex_qnty"])*100;
								$delayPer = ($row["delay"]/$row["ex_qnty"])*100;
								?>
									<td width="70" style="word-break: break-all;" align="right" title="Return : <?=$ex_return_qnty?>"><?=$ex_qnty_after_return;?></td>
                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["early"],0);?></td>
                                    <td width="50" style="word-break: break-all;" align="right"><?=fn_number_format($earlyPer,2,'','',0);?></td>
                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["ontime"],0);?></td>
                                    <td width="50"  style="word-break: break-all;" align="right"><?=fn_number_format($onTimePer,2,'','',0);?></td>
                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["delay"],0) ;?></td>
                                    <td width="50" style="word-break: break-all;" align="right"><?=fn_number_format($delayPer,2,'','',0);?></td>
								</tr>    
								<?
								$m++;
							}
						}
						?>
                </tbody>
            </table>
            </div> 
	</fieldset>
	<?
	exit();
}

if($action=="receiveble_break_down")
{
	echo load_html_head_contents("Shipment performance", "../../../../", 1, 1,$unicode,'','');
	$data=explode("***", $data);
	$cbo_company=$data[0];
	$cbo_buyer=$data[1];
	$season_id=$data[2];
	$txt_po_id=$data[3];
	$cbo_location=$data[4];
	$shipStatus=$data[5];
	$orderStatus=$data[6];
	$cbo_status=$data[7];
	$cbo_client=$data[8];
	$txt_style_ref=$data[9];
	$from_year=$data[10];
	$to_year=$data[11];

	$sql_cond='';
	
	//if($shipStatus==1) $sql_cond.=" and b.shiping_status in (1,2)"; else if($shipStatus==2) $sql_cond.=" and b.shiping_status in (3)"; else $shipStatusCond="";
	//if(!empty($orderStatus)) $sql_cond.=" and b.is_confirmed in ( $orderStatus )";
	//if(!empty($season_id)) $sql_cond.=" and a.season_buyer_wise in ( $season_id )";
	//if(!empty($client_id)) $sql_cond.=" and a.client_id in ( $client_id )";
	//if(!empty(trim($style_ref))) $sql_cond.=" and a.style_ref_no='$style_ref'";
	//if(!empty(trim($txt_po_id))) $sql_cond.=" and (a.all_order_no like '%,".$txt_po_id."' or a.all_order_no '%,".$txt_po_id.",%' or a.all_order_no like '".$txt_po_id.",%' or a.all_order_no = '".$txt_po_id."')";
	//if(!empty(trim($cbo_status))) $sql_cond.=" and b.status_active in ($cbo_status)";

	$exfirstYear=explode('-',$from_year);
	$exlastYear=explode('-',$to_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exlastYear[1];
	$yearMonth_arr=array(); $yearStartEnd_arr=array(); $j=12; $i=1;
	$startDate=''; $endDate="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$yearMonth_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$date_from=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$date_to=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	
		$sql_po_cond='';
		if ($cbo_company>0)
		{
			$sql_cond .= " and c.company_id = $cbo_company";
			$sql_po_cond .= " and a.company_name = $cbo_company";
		} 
		//if ($cbo_location>0) $sql_cond .= " and dm.delivery_location_id = $cbo_location";
		if ($cbo_buyer>0)
		{
			$sql_cond .= " and c.buyer_id = $cbo_buyer";
			$sql_po_cond .= " and a.buyer_name = $cbo_buyer";
		} 
		
		//if ($cbo_dealing_merchant>0) $sql_cond .= " and a.DEALING_MARCHANT = $cbo_dealing_merchant";
		$seasion_cond='';
		if(!empty($season_id) || ($date_from && $date_to ) )
		{
			if(!empty($season_id))
			{
				//$sql_cond.=" and c.season_buyer_wise in ( $season_id )";
				$sql_po_cond.=" and a.season_buyer_wise in ( $season_id )";
			} 
			if ($date_from && $date_to ) 
			{
				if ($db_type == 0) {
					$sql_po_cond .= " and b.shipment_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_po_cond .= " and b.shipment_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
				}
			}
			$sql_po="SELECT a.job_no, b.id, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and b.is_deleted=0 $sql_po_cond";
			//echo $sql_po;
			$po_res=sql_select($sql_po);
			$po_id_arrs=array(); $jobirno="";
			foreach ($po_res as $row) 
			{
				array_push($po_id_arrs, $row[csf('id')]);
				if($txt_po_id!="") $jobirno="For Job No : ".$row[csf('job_no')].'; IR No : '.$row[csf('grouping')];
			}
			$seasion_cond=where_con_using_array($po_id_arrs,0,"f.po_breakdown_id");
			
		}
		if(!empty($txt_po_id))
		{
			
			$seasion_cond=" and f.po_breakdown_id in ($txt_po_id)";
		}
		
		

		$sql=" SELECT sum(f.current_invoice_value) as inv_val,c.possible_reali_date
			    FROM com_export_doc_submission_invo a,
			         com_export_doc_submission_mst c,
			         com_export_invoice_ship_mst d,
			         com_export_invoice_ship_dtls f
			   WHERE     c.id = a.doc_submission_mst_id
			         AND A.INVOICE_ID = D.ID
			         and d.id=f.mst_id
			         AND c.entry_form = 40
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND c.status_active = 1
			         AND c.is_deleted = 0
			         AND d.status_active = 1
			         AND d.is_deleted = 0
			         AND f.status_active = 1
			         AND a.net_invo_value > 0
			         AND c.possible_reali_date is not null
			         $sql_cond $seasion_cond
			         AND c.id NOT IN (SELECT f.invoice_bill_id
			                            FROM com_export_proceed_realization f
			                           WHERE status_active = 1)
			GROUP BY c.possible_reali_date";
		//echo $sql;
		$res=sql_select($sql);

		//print_r($result_array);

	?>
	<fieldset style="width:700px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <td colspan="2" align="center" style="font-size:15px; font-weight:bold;">Receiveble Break Down <?=$jobirno; ?></td>
                    </tr>  
                    <tr>
                        <th width="100" style="word-break: break-all;">Date </th>
                        <th width="150" style="word-break: break-all;">Amount </th>
                    </tr>
                </thead>
                <tbody>
                	<? 
                		$m=1;
                		foreach ($res as $row) 
                		{
                			if ($m % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
								<tr bgcolor="<?=$bgcolor; ?>" <?=$stylecolor; ?> onClick="change_color('tr_<?=$m; ?>', '<?=$bgcolor; ?>')" id="tr_<?=$m; ?>">
                                <td width="100" style="word-break: break-all;" align="right" title="Return : <?=$ex_return_qnty?>"><?=change_date_format($row[csf('possible_reali_date')]);?></td>
                                <td width="150" style="word-break: break-all;" align="right"><?=fn_number_format($row[csf('inv_val')],0);?></td>
                            </tr>    
                            <?
                            $m++;
							$grecval+=$row[csf('inv_val')];
                		}
					?>
                </tbody>
                <tfoot>
                	<tr>
                        <th width="100" align="right">Total </th>
                        <th width="150" style="word-break: break-all;"><?=fn_number_format($grecval,0); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div> 
	</fieldset>
	<?
	exit();
}

if($action=="matConsPer_popup") //
{
		//  $data_season=$company_id.'__'.$location_id.'__'.$shipStatus.'__'.$orderStatus.'__'.$cbo_status.'__'.$season_id.'__'.$client_id.'__'.$style_ref.'__'.$from_year.'__'.$to_year.'__'.$buyer_id;
		echo load_html_head_contents("Material Cons Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("__",$data);
		$company_id=$ex_dara[0];
		$location_id=$ex_dara[1];
		$shipStatus=$ex_dara[2];
		$orderStatus=$ex_dara[3];
		$cbo_status=$ex_dara[4];
		$season_id=$ex_dara[5];
		$client_id=$ex_dara[6];
		$style_ref=$ex_dara[7];
		$from_year=$ex_dara[8];
		$to_year=$ex_dara[9];
		$buyer_id=$ex_dara[10];
		$po_id=$ex_dara[11];
		
	//	echo $from_year.'=='.$to_year;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
		
	$from_year=str_replace("'","",$from_year);
	$to_year=str_replace("'","",$to_year);
	
	$buyerCond = ""; $calAlloBuyerCond=""; $buyerYarnCond="";
	if ($buyer_id == 0) 
	{
		$buyerCond = "";
	} 
	else 
	{
		$buyerCond = " and a.buyer_name=$buyer_id";
		//$calAlloBuyerCond="and b.buyer_id='$buyer_id'";
		//$buyerYarnCond=" and a.buyer_id='$buyer_id'";
	}
	
	$month_cond="and b.shipment_date between '$from_year' and '$to_year'";
	//var_dump($fiscalMonth_arr);
	if($po_id!="") $PoIdCond="and b.id in($po_id)"; else $PoIdCond="";
	if($location_id!=0) $capLocationCond="and a.location_id='$location_id'"; else $capLocationCond="";
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($shipStatus==1) $shipStatusCond="and b.shiping_status in (1,2)"; else if($shipStatus==2) $shipStatusCond="and b.shiping_status in (3)"; else $shipStatusCond="";
	if($orderStatus==0) $orderStatusCond=""; else $orderStatusCond=" and b.is_confirmed in ( $orderStatus )";
	if($season_id==0) $seasonCond=""; else $seasonCond=" and a.season_buyer_wise in ( $season_id )";
	if($client_id==0) $clientCond=""; else $clientCond=" and a.client_id in ( $client_id )";
	if(trim($style_ref)=="" || trim($style_ref)=='0') $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='$style_ref'";
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($shipStatus==1) $shipStatusCond="and b.shiping_status in (1,2)"; else if($shipStatus==2) $shipStatusCond="and b.shiping_status in (3)"; else $shipStatusCond="";
	if($orderStatus==0) $orderStatusCond=""; else $orderStatusCond=" and b.is_confirmed in ( $orderStatus )";
	if($season_id==0) $seasonCond=""; else $seasonCond=" and a.season_buyer_wise in ( $season_id )";
	if($client_id==0) $clientCond=""; else $clientCond=" and a.client_id in ( $client_id )";
	if(trim($style_ref)=="" || trim($style_ref)=='0') $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='$style_ref'";
	 $sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO,a.total_set_qnty AS TOTAL_SET_QNTY, b.id AS ID, b.GROUPING, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY,c.order_total as ORDER_TOTAL, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER ,d.cm_cost as CM_COST,d.common_oh as COMMON_OH,d.comm_cost AS COMM_COST,d.lab_test as LAB_TEST,d.inspection AS INSPECTION,d.freight AS FREIGHT ,d.currier_pre_cost AS CURRIER_PRE_COST,d.certificate_pre_cost AS CERTIFICATE_PRE_COST,d.depr_amor_pre_cost AS DEPR_AMOR_PRE_COST,d.incometax_cost AS INCOMETAX_COST,d.interest_cost AS INTEREST_COST,d.design_cost AS DESIGN_COST,d.studio_cost AS STUDIO_COST,d.commission AS COMMISSION,d.deffdlc_cost AS DEFFDLC_COST from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.company_name ='$company_id' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $PoIdCond $month_cond $buyerCond $jobLocationCond $shipStatusCond $orderStatusCond $seasonCond $clientCond $styleRefCond";
	//echo $sqlpo; die; //and a.job_no='$job_no' currier_pre_cost
	$sqlpoRes = sql_select($sqlpo);
	//print_r($sqlpoRes); die;
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid=""; $jobref="";
	$poidstr=$jobId=""; $poididarr=array(); $jobididarr=array();
	$tot_fob_value=0;$tot_cm_cost=$tot_comercial=$tot_operating_head=$tot_inspection=$tot_currier_cost=$tot_certificate=$tot_depr_amor=$tot_incometax=$tot_interest=$tot_design=$tot_studio=$tot_deffdlc=$tot_commission=$tot_freight=$tot_lab=0;
	foreach($sqlpoRes as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		$cm_cost_dzn=$row['CM_COST'];
		$comercial_dzn=$row['COMM_COST'];
		$operating_head_dzn=$row['COMMON_OH'];
		$inspection_dzn=$row['INSPECTION'];
		$currier_dzn=$row['CURRIER_PRE_COST'];
		$certificate_dzn=$row['CERTIFICATE_PRE_COST'];
		$depr_amor_dzn=$row['DEPR_AMOR_PRE_COST'];
		$incometax=$row['INCOMETAX_COST'];
		$interest_dzn=$row['INTEREST_COST'];
		$design_dzn=$row['DESIGN_COST'];
		$studio_dzn=$row['STUDIO_COST'];
		$deffdlc_dzn=$row['DEFFDLC_COST'];
		$commission_dzn=$row['COMMISSION'];
		$freight_dzn=$row['FREIGHT'];
		$lab_dzn=$row['LAB_TEST']; 
		
		$jobref='Job No : '.$row['JOB_NO'].'; IR No : '.$row['GROUPING'];
			
		$total_set_qnty=$row['TOTAL_SET_QNTY'];
		$order_qty=$row['ORDER_QUANTITY'];
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		$tot_fob_value+=$row['ORDER_TOTAL'];
		
		// ========Other Cost============
		
		$tot_cm_cost +=($order_qty/$total_set_qnty)*($cm_cost_dzn/$costingPerQty);
		$tot_comercial +=($order_qty/$total_set_qnty)*($comercial_dzn/$costingPerQty);
		$tot_operating_head +=($order_qty/$total_set_qnty)*($operating_head_dzn/$costingPerQty);
		$tot_inspection +=($order_qty/$total_set_qnty)*($inspection_dzn/$costingPerQty);
		$tot_currier_cost +=($order_qty/$total_set_qnty)*($currier_dzn/$costingPerQty);
		$tot_certificate +=($order_qty/$total_set_qnty)*($certificate_dzn/$costingPerQty);
		$tot_depr_amor +=($order_qty/$total_set_qnty)*($depr_amor_dzn/$costingPerQty);
		$tot_incometax +=($order_qty/$total_set_qnty)*($incometax/$costingPerQty);
		$tot_interest+=($order_qty/$total_set_qnty)*($interest_dzn/$costingPerQty);
		$tot_design+=($order_qty/$total_set_qnty)*($design_dzn/$costingPerQty);
		$tot_studio+=($order_qty/$total_set_qnty)*($studio_dzn/$costingPerQty);
		$tot_deffdlc+=($order_qty/$total_set_qnty)*($deffdlc_dzn/$costingPerQty);
		$tot_commission+=($order_qty/$total_set_qnty)*($commission_dzn/$costingPerQty);
		$tot_freight+=($order_qty/$total_set_qnty)*($freight_dzn/$costingPerQty);
		$tot_lab+=($order_qty/$total_set_qnty)*($lab_dzn/$costingPerQty);
		
		//
		$poidstr.=$row['ID'].',';
		$poididarr[$row['ID']]=$row['ID'];
		$jobididarr[$row['JOB_ID']]=$row['JOB_ID'];
		if($jobId=="") $jobId="'".$row["JOB_ID"]."'"; else $jobId.=",'".$row["JOB_ID"]."'";
	}
	unset($sqlpoRes);



	/*$sql_po="select a.job_no as JOB_NO, a.id as JOBID, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as POID, b.shipment_date as SHIPMENT_DATE, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shiping_status as SHIPING_STATUS, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $month_cond $buyerCond $jobLocationCond $shipStatusCond $orderStatusCond $seasonCond $clientCond $styleRefCond";//(a.set_smv/a.total_set_qnty)
	//echo $sql_po;
	$sql_po_res=sql_select($sql_po);
	foreach($sql_po_res as $row)
	{
		$poidstr.=$row['POID'].',';
		$poididarr[$row['POID']]=$row['POID'];
		$jobididarr[$row['JOBID']]=$row['JOBID'];
		if($jobId=="") $jobId="'".$row["JOBID"]."'"; else $jobId.=",'".$row["JOBID"]."'";
	}*/
	$po_ids=array();
	$po_ids=array_filter(array_unique(explode(",",$poidstr)));
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=880");
	oci_commit($con);
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 1, $poididarr, $empty_arr);//PO ID
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 880, 2, $jobididarr, $empty_arr);//Job ID
	//fnc_tempengine($table_name, $user_id, $entry_form, $ref_from, $ref_id_arr,  $ref_str_arr)
	//die;
	 
		/* $budgetAmt_arr=array();
	$sqlBomAmt="select a.job_id as JOB_ID, a.po_id as PO_ID, a.greypurch_amt as GREYPURCH_AMT, a.yarn_amt as YARN_AMT, a.conv_amt as CONV_AMT, a.trim_amt as TRIM_AMT, a.emb_qty as EMB_QTY, a.emb_amt as EMB_AMT, a.wash_qty as WASH_QTY, a.wash_amt as WASH_AMT from bom_process a, gbl_temp_engine b where a.po_id=b.ref_val and b.entry_form=880 and b.ref_from=1 and a.status_active=1 and a.is_deleted=0 ";
	
	$sqlBomAmtRes=sql_select($sqlBomAmt);
	foreach($sqlBomAmtRes as $row)
	{
		$budgetAmt_arr[$row["PO_ID"]]['fab']=$row["GREYPURCH_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['yarn']=$row["YARN_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['conv']=$row["CONV_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['trim']=$row["TRIM_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['embqty']=$row["EMB_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['emb']=$row["EMB_AMT"];
		$budgetAmt_arr[$row["PO_ID"]]['washqty']=$row["WASH_QTY"];
		$budgetAmt_arr[$row["PO_ID"]]['wash']=$row["WASH_AMT"];
	}
	unset($sqlBomAmtRes);
	
	$sql_budget="select a.job_no as JOB_NO, a.approved as APPROVED, a.costing_per as COSTING_PER, a.exchange_rate as EXCHANGE_RATE, b.margin_pcs_bom as MARGIN_PCS_BOM from wo_pre_cost_mst a, wo_pre_cost_dtls b, gbl_temp_engine c where a.job_id=b.job_id and a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	
	$sql_budget_res=sql_select($sql_budget); $budget_arr=array();
	foreach($sql_budget_res as $row)
	{
		$budget_arr[$row["JOB_NO"]]['app']=$row["APPROVED"];
		$budget_arr[$row["JOB_NO"]]['margin_pcs']=$row["MARGIN_PCS_BOM"];
		$budget_arr[$row["JOB_NO"]]['costing_per']=$row["COSTING_PER"];
		$budget_arr[$row["JOB_NO"]]['exchange_rate']=$row["EXCHANGE_RATE"];
	}
	unset($sql_budget_res);
	*/
	$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details a,gbl_temp_engine c where  1=1 and    a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=2  ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
	}
	unset($gmtsitemRatioSqlRes);


	$sql_budget="select a.job_id AS JOB_ID,a.job_no as JOB_NO, a.approved as APPROVED, a.costing_per as COSTING_PER, a.exchange_rate as EXCHANGE_RATE, b.margin_pcs_bom as MARGIN_PCS_BOM from wo_pre_cost_mst a, wo_pre_cost_dtls b, gbl_temp_engine c where a.job_id=b.job_id and a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	
	$sql_budget_res=sql_select($sql_budget); $budget_arr=array();
	foreach($sql_budget_res as $row)
	{
		 $costingPer=$costingPerArr[$row['JOB_ID']];
	}
	unset($sql_budget_res);
	

	/*$po_month_arr=array(); $job_arr=array(); $po_arr=array(); $poExchangeRatearr=array(); $fullshipedpoArr=array();
	foreach($sql_po_res as $row)
	{
		$costing_per=0; $costingPer=0; $matCost=0; $poMatCost=0; $actualPono=0;
		$costing_per=$budget_arr[$row["JOB_NO"]]['costing_per'];
		$poExchangeRatearr[$row['POID']]=$budget_arr[$row["JOB_NO"]]['exchange_rate'];
		$actualPono=count($actualPoArr[$row["POID"]]);
		
		if($costing_per==1) $costingPer=12;
		if($costing_per==2) $costingPer=1;
		if($costing_per==3) $costingPer=24;
		if($costing_per==4) $costingPer=36;
		if($costing_per==5) $costingPer=48;

		$matCost=$budgetAmt_arr[$row['POID']]['fab']+$budgetAmt_arr[$row['POID']]['yarn']+$budgetAmt_arr[$row['POID']]['conv']+$budgetAmt_arr[$row['POID']]['trim']+$budgetAmt_arr[$row['POID']]['emb']+$budgetAmt_arr[$row['POID']]['wash'];
		//Month Buyer Details
		$month_buyer="";
		$shipment_date=date("Y-m",strtotime($row["SHIPMENT_DATE"]));
		//$month_buyer=$row["BUYER_NAME"].'_'.$shipment_date;
		$month_buyer=$row["BUYER_NAME"];
		
		$buyerMonth_list[$month_buyer]=$month_buyer;
		
		$poQtyPcs=0; $poValue=0; $booked_min=0;
		$poQtyPcs=$row["PO_QUANTITY"];
		$poValue=$row["PO_TOTAL_PRICE"];
		$booked_min=$row["SET_SMV"];
		
		$po_month_arr[$month_buyer]['min']+=$booked_min;
		$po_month_arr[$month_buyer]['pcs']+=$poQtyPcs;
		$po_month_arr[$month_buyer]['val']+=$poValue;
		$po_month_arr[$month_buyer]['actualpo']+=$actualPono;
		if($row["SHIPING_STATUS"]==3)
		{
			$po_month_arr[$month_buyer]['fullshiped']+=$poValue;
			$po_arr[$row['POID']]['fullship_qty']=$poQtyPcs;
			$fullshipedpoArr[$row['POID']]=$poValue;
		}
		else if($row["SHIPING_STATUS"]==2)
		{
			$po_month_arr[$month_buyer]['partial']+=$poValue;
		}
		else 
		{
			$po_month_arr[$month_buyer]['pending']+=$poValue;
		}
		
		$job_arr[$month_buyer]['job'][$row["JOB_NO"]]=$row["JOB_NO"];
		$po_arr[$row['POID']]['po_id']=1;
		$po_arr[$row['POID']]['month_buyer']=$month_buyer;
		$po_arr[$row['POID']]['ship_sta']=$row[csf("SHIPING_STATUS")];
		$po_arr[$row['POID']]['po_price']=$row[csf("UNIT_PRICE")];
		$po_arr[$row['POID']]['poVal']+=$poValue;
		
		if($budget_arr[$row["JOB_NO"]]['app']==1)
		{
			$po_arr[$row['POID']]['apppo']=1;
			$job_arr[$month_buyer]['fobjob'][$row["JOB_NO"]]=$row["JOB_NO"];
			$po_month_arr[$month_buyer]['fob']+=$poValue;
			$margin=0;
			$margin=$budget_arr[$row["JOB_NO"]]['margin_pcs']*($poQtyPcs/$row[csf("TOTAL_SET_QNTY")]);
			$po_month_arr[$month_buyer]['margin']+=$margin;
			$poMatCost=$matCost;//($matCost/$costingPer)*($poQtyPcs/$row[csf("total_set_qnty")]);
			$po_month_arr[$month_buyer]['matCost']+=$poMatCost;
		}
	}
	//var_dump($po_month_arr);
	asort($buyerMonth_list);*/
	
	$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a,gbl_temp_engine c where 1=1 and  a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=2  and a.status_active=1 and a.is_deleted=0 ";
	//echo $sqlContrast; die;
	$sqlContrastRes = sql_select($sqlContrast);
	$sqlContrastArr=array();
	foreach($sqlContrastRes as $row)
	{
		$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
	}
	unset($sqlContrastRes);
	//Stripe Details
	$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a, gbl_temp_engine c where 1=1 and  a.job_id=c.ref_val and c.entry_form=880 and c.ref_from=2  and a.status_active=1 and a.is_deleted=0 ";
	//echo $sqlStripe; die;
	$sqlStripeRes = sql_select($sqlStripe);
	$sqlStripeArr=array();
	foreach($sqlStripeRes as $row)
	{
		$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
		$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
	}
	unset($sqlStripeRes);
	$job_cond_for_in=where_con_using_array($jobididarr,0,"a.job_id");
	$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
	from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $job_cond_for_in "; 
	//echo $sqlfab; die;
	$sqlfabRes = sql_select($sqlfab);
	$fabIdWiseGmtsDataArr=array();
	$tot_purchfin_amt=$purchgrey_amt=0;
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
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_amt']+=$finAmt;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_amt']+=$greyAmt;
		}
		else if($row['FABRIC_SOURCE']==2)
		{
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchfin_qty']+=$finReq;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchgrey_qty']+=$greyReq;
			$tot_purchfin_amt+=$finAmt;
			//$purchgrey_amt+=$greyAmt;
		}
	}
	//echo $tot_purchfin_amt."=";die;
	unset($sqlfabRes); 
	
	$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
	
	from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_cond_for_in";
	//echo $sqlYarn;
	$sqlYarnRes = sql_select($sqlYarn);
	$tot_yarn_amt=0;
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
		$tot_yarn_amt+=$yarnAmt;
	}
	unset($sqlYarnRes);
	
	$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
	from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_cond_for_in";
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
		$process_id=$row['CONS_PROCESS'];
		//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
		//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
		$conv_reqAmtArr[$process_id]+=$convAmt;
	}
	unset($sqlConvRes); 
	$fab_dye_finish_cost_arr=array(31,33,9,64,92,101,129,132,140,146,155,156,158,162,167,168,188,193);
	
	$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
	where 1=1 and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_cond_for_in";
	//echo $sqlTrim; die;
	$sqlTrimRes = sql_select($sqlTrim);
	$tot_trimtotamt=0;
	foreach($sqlTrimRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
		//print_r($poCountryId);
		$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
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
					//$consAmt=0;
					//$consAmt=$consTotQty*$row['RATE'];
					$consTotAmt+=$consAmt;
				}
			}
		}
		//echo $consTotAmt.'<br>';
		//echo $poQty.'='.$row['TOT_CONS'].'='.$costingPer.'='.$itemRatio.'='.$consTotQty.'<br>';
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotqty']+=$consTotQnty;
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimamt']+=$consAmt;
		//$t[$row['TRIMID']]+=$consTotAmt;
		$tot_trimtotamt+=$consTotAmt;
	}
	unset($sqlTrimRes); 
	/*echo "<pre>";
	print_r($t); die;*/
	
	$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
	from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
	where 1=1 and a.cons_dzn_gmts>0 and
	a.job_id=b.job_id and a.id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_cond_for_in";
	//echo $sqlEmb; die;
	$sqlEmbRes = sql_select($sqlEmb);
	
	foreach($sqlEmbRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$EMB_NAME=$row['EMB_NAME'];
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
			$wash_reqQtyAmtArr[$EMB_NAME]+=$consAmt;
		}
		else
		{
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['embqty']+=$consQnty;
			$emb_reqQtyAmtArr[$EMB_NAME]+=$consAmt;
		}
	}
	unset($sqlEmbRes); 



	//===========end============
 
	 
	 
	//asort($dying_prod_array);
	//echo $po_ids;
	
	
	// =================================== subcon kniting =============================
	  $width_td="520";
	   ?>
       <div style="margin-left:50px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Cost Breakdown Report </b> </caption>
       
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
		<thead>
        	<tr>
            	<th colspan="4"><? if($po_id!="") echo $jobref; ?></th>
            </tr>
        	<tr>
                <th width="50">Serial</th>
                <th width="300">Particulars </th>
                <th width="100">Amount</th>
                <th>%</th>
            </tr>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px;" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			 
            $i=1;$k=1; 
			$bgcolor="#E9F3FF"; $bgcolor2="#FFFFFF";
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Yarn Purchase cost </td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_yarn_amt,2,'','',0); ?></p></td>
					<td width="" title="Yarn Amount/Fob Value(<? echo $tot_fob_value;?>)*100" align="center"><p><? echo fn_number_format(($tot_yarn_amt/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Fabric purchase cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_purchfin_amt,2,'','',0); ?></p></td>
					<td width="" align="center" title="Fab Purchase Amount/Fob Value(<? echo $tot_fob_value;?>)*100">
                    <p><? echo fn_number_format(($tot_purchfin_amt/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                
                 <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Yarn twisting cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($conv_reqAmtArr[134],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($conv_reqAmtArr[134]/$tot_fob_value)*100,2,'','',0);; ?></p></td>
				</tr>
                 <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Yarn dyeing cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($conv_reqAmtArr[30],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($conv_reqAmtArr[30]/$tot_fob_value)*100,2,'','',0);; ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Knitting Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($conv_reqAmtArr[1],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($conv_reqAmtArr[1]/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
                    <?
					$dye_finish=0;
                    foreach($fab_dye_finish_cost_arr as $processId)
					{
						$dye_finish+=$conv_reqAmtArr[$processId];
					}
					?>
					 <td width="300">Dyeing & finishing Cost </td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($dye_finish,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($dye_finish/$tot_fob_value)*100,2,'','',0);; ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Brushing Cost </td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($conv_reqAmtArr[68],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($conv_reqAmtArr[68]/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Peach Finishing</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($conv_reqAmtArr[67],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($conv_reqAmtArr[67]/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">AOP Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($conv_reqAmtArr[35],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($conv_reqAmtArr[35]/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                 <?
				$i++;
				?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Trims Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_trimtotamt,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_trimtotamt/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Printing Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($emb_reqQtyAmtArr[1],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($emb_reqQtyAmtArr[1]/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;//Gmts.Wash
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Embroidery Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($emb_reqQtyAmtArr[2],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($emb_reqQtyAmtArr[2]/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;//Gmts.Wash
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Gmts.Wash Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($wash_reqQtyAmtArr[3],2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($wash_reqQtyAmtArr[3]/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
				<?
				$i++;$b++;
				 $all_conv_cost=$conv_reqAmtArr[35]+$conv_reqAmtArr[67]+$conv_reqAmtArr[68]+$dye_finish+$conv_reqAmtArr[1]+$conv_reqAmtArr[30]+$conv_reqAmtArr[134]+$conv_reqAmtArr[137];
				 
				$total_material_cost=$tot_purchfin_amt+$tot_yarn_amt+$all_conv_cost+$tot_trimtotamt+$emb_reqQtyAmtArr[1]+$emb_reqQtyAmtArr[2]+$wash_reqQtyAmtArr[3];
				 
				
			 //$tot_cm_cost=$tot_comercial=$tot_operating_head=$tot_inspection=$tot_currier_cost=$tot_certificate=$tot_depr_amor=$tot_incometax=$tot_interest=$tot_design=$tot_studio=$tot_deffdlc=$tot_commission=$tot_freight=$tot_lab=0;
			?>
             <tr class="tbl_bottom">
                <td width="350" colspan="2"  align="right"> Material & servicing Cost </td>
                
				<td width="100" style="word-break:break-all"><p>&nbsp;<? echo fn_number_format($total_material_cost,2,'','',0); ?></p></td>
				<td width="" align="center"><p><? echo fn_number_format(($total_material_cost/$tot_fob_value)*100,2,'','',0); ?></p></td>
          </tr>
          <? 
          // =================2nd =========================part\\
		  
		  ?>
          <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">CM cost </td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_cm_cost,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_cm_cost/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Comml. Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_comercial,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_comercial/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                 <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Lab Test </td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_lab,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_lab/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                 <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Inspection</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_inspection,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_inspection/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                 <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Freight</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_freight,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_freight/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Courier Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_currier_cost,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_currier_cost/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Certificate Cost </td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_certificate,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_certificate/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Deffd. LC Cost </td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_deffdlc,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_deffdlc/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Design Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_design,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_design/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Studio Cost</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_studio,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_studio/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                 <?
				$i++;
				?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Opert. Exp.</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_operating_head,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_operating_head/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Interest</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_interest,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_interest/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;//Gmts.Wash
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Income Tax</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_incometax,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_incometax/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                  <?
				$i++;//Gmts.Wash 
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Depc. & Amort.</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_depr_amor,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_depr_amor/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
                   <?
				$i++;//Gmts.Wash Commission
				?>
                <tr bgcolor="<? echo $bgcolor2;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>" style="font-size:11px">
                    <td width="50"><p>&nbsp;<? echo $i; ?></p></td>
					 <td width="300">Commission</td>
					<td width="100" style="word-break:break-all" align="right"><p>&nbsp;<? echo fn_number_format($tot_commission,2,'','',0); ?></p></td>
					<td width="" align="center"><p><? echo fn_number_format(($tot_commission/$tot_fob_value)*100,2,'','',0); ?></p></td>
				</tr>
				<?
				$i++; 
				 $total_other_cost=$tot_cm_cost+$tot_comercial+$tot_operating_head+$tot_inspection+$tot_currier_cost+$tot_certificate+$tot_depr_amor+$tot_incometax+$tot_interest+$tot_design+$tot_studio+$tot_deffdlc+$tot_commission+$tot_freight+$tot_lab;
				
			 $grnd_total=$total_material_cost+$total_other_cost;
			?>
             <tr class="tbl_bottom">
                <td width="350" colspan="2"  align="right"> Total </td>
                
				<td width="100" style="word-break:break-all"><p>&nbsp;<? echo fn_number_format($grnd_total,2,'','',0); ?></p></td>
				<td width="" align="center"><p><? echo fn_number_format(($grnd_total/$tot_fob_value)*100,2); ?></p></td>
          </tr>
        </table>
      </div>
    </div>
    <?
	exit();
}
?>