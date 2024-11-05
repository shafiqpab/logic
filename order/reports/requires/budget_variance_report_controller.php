<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
//include('../../../includes/class4/class.fabrics2.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

if($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
   
	?>
	<script>
	function js_set_value(id)
	{ 
		var str=id.split("_");
		$('#txt_job_id').val(str[0]);
		$('#txt_job_no').val(str[1]);
		$('#txt_styleref').val(str[2]);
		parent.emailwindow.hide();
	}
	</script>
 	<input type="hidden" id="txt_job_id" />
 	<input type="hidden" id="txt_job_no" />
    <input type="hidden" id="txt_styleref" />
    <?
	if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
	
	if($db_type==0) 
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	$sql ="select id, style_ref_no, job_no, job_no_prefix_num as job_prefix, $year_field from wo_po_details_master where $company_name $buyer_name $year_cond order by id desc";
	//echo $sql; 
	echo create_list_view("list_view", "Job No,Year,Style Ref. No.","100,100,200","450","360",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "job_prefix,year,style_ref_no", "budget_variance_report_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();	
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$job_id=str_replace("'","",$hidd_job_id);
	
	$jobyear=str_replace("'","",$cbo_year);
	$stylerefno=str_replace("'","",$txt_styleref);
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyerCond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyerCond="";
		}
		else $buyerCond="";
	}
	else $buyerCond=" and a.buyer_name=$cbo_buyer";
	
	if($db_type==0) 
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	$jobCond="";
	if ($job_id!="") 
	{
		$jobCond=" and a.job_id in ($job_id)";
	}
	else if($job_no!="")
	{
		$jobCond=" and a.job_no in ($txt_job_no)";
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	//$factoryMarArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$sqlBom="select id, costing_per, job_no, sourcing_approved from wo_pre_cost_mst where job_id in ($job_id) and status_active=1 and is_deleted=0";
	$sqlBomRes=sql_select($sqlBom); $bomidArr=array(); $costingPerStr="";
	foreach($sqlBomRes as $brow)
	{
		$bomidArr[$brow[csf('id')]]=$brow[csf('id')];
		
		if($brow[csf('costing_per')]==1) $costingPerStr="DZN";
		elseif($brow[csf('costing_per')]==2) $costingPerStr="PCS";	
		elseif($brow[csf('costing_per')]==3) $costingPerStr="2 DZN";
		elseif($brow[csf('costing_per')]==4) $costingPerStr="3 DZN";
		elseif($brow[csf('costing_per')]==5) $costingPerStr="4 DZN";
	}
	unset($sqlBomRes);
	
	$bomids=implode(",",$bomidArr);
	
	$sqlAppNo="select mst_id, min(approved_no) as minapproved_no, max(approved_no) as maxapproved_no from approval_history where mst_id in ($bomids) and entry_form=47 group by mst_id";
	//echo $sqlAppNo;die;
	
	$sqlRes=sql_select($sqlAppNo); $appNoArr=array(); $approvno=0; 
	foreach($sqlRes as $arow)
	{
		$appNoArr[$arow[csf('mst_id')]]['minappno']=$arow[csf('minapproved_no')];
		$appNoArr[$arow[csf('mst_id')]]['maxappno']=$arow[csf('maxapproved_no')];
		$approvno=$arow[csf('minapproved_no')].','.$arow[csf('maxapproved_no')];
	}
	unset($sqlRes);
	
	if($approvno=="" || $approvno==0) 
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>Sourcing Post Cost History Not Found.</font>";
		die;	
	}
	
	ob_start();
	?>
    <div>
    <!--<table width="1200px" cellspacing="0" style="display:none">
    	<tr class="form_caption" style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:16px; font-weight:bold"><?//=$companyArr[$cbo_company]; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:12px; font-weight:bold"><?//=show_company($cbo_company,'',''); ?></td>
        </tr>
        <tr class="form_caption" style="border:none;">
            <td colspan="14" align="center" style="border:none; font-size:14px; font-weight:bold" ><?//=$report_title; ?></td>
        </tr>
    </table>-->
    <?
	 $sqlpo="SELECT a.job_id as JOB_ID,a.set_break_down as SET_BREAK_DOWN,a.total_set_qnty as TOTAL_SET_QNTY,order_uom as ORDER_UOM, a.approved_no AS APPROVEDNO, a.job_no AS JOB_NO, a.style_ref_no as STYLERREF, a.buyer_name as BUYERID, b.po_id AS POID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_dtls_mst_his a, wo_po_break_down_his b, wo_po_color_size_his c, wo_pre_cost_dtls_histry d where a.job_id=b.job_id and b.po_id=c.po_break_down_id and a.job_id=d.job_id and b.job_id=d.job_id and c.job_id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.company_name='$cbo_company' and a.approved_no=b.approved_no and b.approved_no=c.approved_no and c.approved_no=d.approved_no and a.approval_page=b.approval_page and b.approval_page=c.approval_page and c.approval_page=d.approval_page and a.approved_no in ($approvno) and a.approval_page=47 $buyerCond $jobCond $year_cond";
	//echo $sqlpo; die; //and a.job_no='$job_no'
	$sqlpoRes = sql_select($sqlpo);
	//print_r($sqlpoRes); die;
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid=""; $jobQtyArr=array(); $summArr=array();
	foreach($sqlpoRes as $row)
	{
		$summArr[$row['APPROVEDNO']]['b']=$buyerArr[$row['BUYERID']];
		$summArr[$row['APPROVEDNO']]['j']=$row['JOB_NO'];
		$summArr[$row['APPROVEDNO']]['s']=$row['STYLERREF'];
		$summArr[$row['APPROVEDNO']]['q']+=$row['ORDER_QUANTITY']/$row['TOTAL_SET_QNTY'];
		$summArr[$row['APPROVEDNO']]['ratio']=$row['TOTAL_SET_QNTY'];
		$summArr[$row['APPROVEDNO']]['uom']=$row['ORDER_UOM'];
		$summArr[$row['APPROVEDNO']]['setBreak']=$row['SET_BREAK_DOWN'];
		$order_uom=$row['ORDER_UOM'];
		
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['APPROVEDNO']][$row['JOB_ID']]=$costingPerQty;
		$jobDataArr[$row['APPROVEDNO']][$row['JOB_ID']]['plan']+=$row['PLAN_CUT_QNTY']/$row['TOTAL_SET_QNTY'];
		$jobDataArr[$row['APPROVEDNO']][$row['JOB_ID']]['poqty']+=$row['ORDER_QUANTITY']/$row['TOTAL_SET_QNTY'];
		
		$po_arr[$row['APPROVEDNO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['APPROVEDNO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['APPROVEDNO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['APPROVEDNO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['APPROVEDNO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']][$row['POID']]['poqty']+=$row['ORDER_QUANTITY']/$row['TOTAL_SET_QNTY'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']][$row['POID']]['planqty']+=$row['PLAN_CUT_QNTY']/$row['TOTAL_SET_QNTY'];
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
    <table width="510px" cellspacing="0" border="1" class="rpt_table" rules="all">
		<thead>
			<tr>
            	<th width="60">Version</th>
				<th width="120">Buyer/Brand</th>
				<th width="80">Job No</th>
				<th width="140">Style</th>
				<th>P.O. Qty</th>
			</tr>
         </thead>
         <tbody>
         	<?
			
			foreach($summArr as $vno=>$vdata)
			{ 
			?>
         	<tr>
            	<td style="word-break:break-all" align="center">V:<?=$vno; ?></td>
				<td style="word-break:break-all"><?=$vdata['b']; ?></td>
				<td style="word-break:break-all"><?=$vdata['j']; ?></td>
				<td style="word-break:break-all"><?=$vdata['s']; ?></td>
				<td style="word-break:break-all" align="center"><?=$vdata['q'].' '.$unit_of_measurement[$vdata['uom']]; ?></td>
			</tr>
            <? } ?>
         </tbody>
    </table>
    <br/>
    <?
	
	$gmtsitemRatioSql="select approved_no as APPROVENO, job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO, smv_pcs as SMV_PCS from wo_po_dtls_item_set_his where 1=1 and approved_no in ($approvno) and approval_page=47 $jobCondS $jobidCondition";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['APPROVENO']][$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$jobDataArr[$row['APPROVENO']][$row['JOB_ID']]['smv']+=$row['SMV_PCS'];
		//echo $row['SMV_PCS'].'='.$row['SET_ITEM_RATIO'].'<br>';
		
	}
	unset($gmtsitemRatioSqlRes);
	
	//Contrast Details
	$sqlContrast="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_fab_concolor_dtls_h a where 1=1 and a.approved_no in ($approvno) and a.approval_page=47 and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";
	//echo $sqlContrast; die;
	$sqlContrastRes = sql_select($sqlContrast);
	$sqlContrastArr=array();
	foreach($sqlContrastRes as $row)
	{
		$sqlContrastArr[$row['APPROVENO']][$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
	}
	unset($sqlContrastRes);
	
	//Stripe Details
	$sqlStripe="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color_h a where 1=1 and a.status_active=1 and a.is_deleted=0 and a.approved_no in ($approvno) and a.approval_page=47 $jobCond $jobidCond";
	//echo $sqlStripe; die;
	$sqlStripeRes = sql_select($sqlStripe);
	$sqlStripeArr=array();
	foreach($sqlStripeRes as $row)
	{
		$sqlStripeArr[$row['APPROVENO']][$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
		$sqlStripeArr[$row['APPROVENO']][$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
	}
	unset($sqlStripeRes);
	
	
	//Fabric Details
	$sqlfab="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS FABID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.fabric_description as FABRIC_DESCRIPTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, a.budget_on as BUDGET_ON, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.sourcing_rate as RATE, a.sourcing_amount AS AMOUNT
	from wo_pre_cost_fabric_cost_dtls_h a, wo_pre_fab_avg_con_dtls_h b
	where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.approved_no=b.approved_no and a.approval_page=b.approval_page and a.approved_no in ($approvno) and a.approval_page=47 and b.approval_page=47 $jobCond $jobidCond order by a.pre_cost_fabric_cost_dtls_id asc";
	 // echo $sqlfab; die;
	$sqlfabRes = sql_select($sqlfab);
	$fabIdWiseGmtsDataArr=array();
	foreach($sqlfabRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
		
		$fabIdWiseGmtsDataArr[$row['APPROVENO']][$row['FABID']]['item']=$row['ITEM_NUMBER_ID'];
		$fabIdWiseGmtsDataArr[$row['APPROVENO']][$row['FABID']]['fnature']=$row['FAB_NATURE_ID'];
		$fabIdWiseGmtsDataArr[$row['APPROVENO']][$row['FABID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
		$fabIdWiseGmtsDataArr[$row['APPROVENO']][$row['FABID']]['color_type']=$row['COLOR_TYPE_ID'];
		$fabIdWiseGmtsDataArr[$row['APPROVENO']][$row['FABID']]['uom']=$row['UOM'];
		
		$poQty=$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		$poPlanQty=0;
		if($row['BUDGET_ON']==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
		
		$costingPer=$costingPerArr[$row['APPROVENO']][$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['APPROVENO']][$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$finReq=($poPlanQty/$itemRatio)*($row['CONS']/$costingPer);
		$greyReq=($poPlanQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
		
		$finAmt=$finReq*$row['RATE'];
		$greyAmt=$greyReq*$row['RATE'];
		
		//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['f'][$row['FABID']]['fabric']=$row['FABRIC_DESCRIPTION'];
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['f'][$row['FABID']]['uom']=$row['UOM'];
		
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['f'][$row['FABID']]['qty']+=$greyReq;
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['f'][$row['FABID']]['amt']+=$greyAmt;
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['f'][$row['FABID']]['dzn']=$row['AMOUNT'];
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['f'][$row['FABID']]['rate']=$row['RATE'];
	}
	unset($sqlfabRes); 
	
	//Trims Details
	$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");
	$sqlTrim="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_trim_cost_dtls_id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.sourcing_rate AS RATE, a.sourcing_amount AS AMOUNT, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
	from wo_pre_cost_trim_cost_dtls_his a, wo_pre_cost_trim_co_cons_dtl_h b
	where 1=1 and a.pre_cost_trim_cost_dtls_id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approval_page=b.approval_page and a.approved_no in ($approvno) and a.approval_page=47 and b.approval_page=47 $jobCond $jobidCond order by a.pre_cost_trim_cost_dtls_id asc";
	//echo $sqlTrim; die;
	$sqlTrimRes = sql_select($sqlTrim);
	
	foreach($sqlTrimRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$costingPer=$costingPerArr[$row['APPROVENO']][$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['APPROVENO']][$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
		//print_r($poCountryId);
		
		if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
		{
			$poQty=$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			//$poPlanQty=0;
			//if($row['BUDGET_ON']==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
			
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
					$poQty=$poCountryArr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$poCountryArr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
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
		if(trim($row['DESCRIPTION'])=='0' ) $row['DESCRIPTION']="";
		$trimDesc="";
		if(trim($row['DESCRIPTION'])!="" ) $trimDesc=', '.$row['DESCRIPTION'];
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['t'][$row['TRIMID']]['trimdesc']=$lib_item_group_arr[$row['TRIM_GROUP']].$trimDesc;
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['t'][$row['TRIMID']]['trimamtdzn']=$row['AMOUNT'];
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['t'][$row['TRIMID']]['uom']=$row['CONS_UOM'];
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['t'][$row['TRIMID']]['trimqty']+=$consQnty;
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['t'][$row['TRIMID']]['trimtotqty']+=$consTotQnty;
		
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['t'][$row['TRIMID']]['trimamt']+=$consAmt;
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['t'][$row['TRIMID']]['trimtotamt']+=$consTotAmt;
	}
	unset($sqlTrimRes); 
	//print_r($reqQtyAmtArr); die;
	
	
	$sqlEmb="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_embe_cost_dtls_id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.sourcing_rate AS RATE, a.sourcing_amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
from wo_pre_cost_embe_cost_dtls_his a, wo_pre_emb_avg_con_dtls_h b 
where 1=1 and a.cons_dzn_gmts>0 and
a.job_id=b.job_id and a.pre_cost_embe_cost_dtls_id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approval_page=b.approval_page and a.approved_no in ($approvno) and a.approval_page=47 and b.approval_page=47 $jobCond $jobidCond order by a.pre_cost_embe_cost_dtls_id asc";
	//echo $sqlEmb; die;
	$sqlEmbRes = sql_select($sqlEmb);
	$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$emblishment_other_type_arr);
	foreach($sqlEmbRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$costingPer=$costingPerArr[$row['APPROVENO']][$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['APPROVENO']][$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		$budget_on=$row['BUDGET_ON'];
		
		$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
		//print_r($poCountryId);
		$calPoPlanQty=0;
		
		if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
		{
			$poQty=$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			
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
					$poQty=$poCountryArr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$poCountryArr[$row['APPROVENO']][$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					
					if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
					$consQty=0;
					$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
					$consQnty+=$consQty;
					//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
					$consAmt+=$consQty*$row['RATE'];
				}
			}
		}
		
		if($row['EMB_TYPE']=="") $row['EMB_TYPE']=0;
		$embtypestr="";
		if($row['EMB_TYPE']!=0)
		{
			if($row['EMB_NAME']==1) $embtypestr=', '.$emblishment_print_type[$row['EMB_TYPE']];
			else if($row['EMB_NAME']==2) $embtypestr=', '.$emblishment_embroy_type[$row['EMB_TYPE']];
			else if($row['EMB_NAME']==3) $embtypestr=', '.$emblishment_wash_type[$row['EMB_TYPE']];
			else if($row['EMB_NAME']==4) $embtypestr=', '.$emblishment_spwork_type[$row['EMB_TYPE']];
			else if($row['EMB_NAME']==5) $embtypestr=', '.$emblishment_gmts_type[$row['EMB_TYPE']];
		}
		
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['e'][$row['EMB_ID']]['emb_name']=$emblishment_name_array[$row['EMB_NAME']].$embtypestr;
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['e'][$row['EMB_ID']]['embamtdzn']=$row['AMOUNT'];
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['e'][$row['EMB_ID']]['emb_qty']+=$consQnty;
		$reqQtyAmtArr[$row['APPROVENO']][$row['JOB_ID']]['e'][$row['EMB_ID']]['emb_amt']+=$consAmt;
	}
	unset($sqlEmbRes);
	$othersStrArr=array(1=>"Lab Test", 2=>"Currier", 3=>"Inspection", 4=>"Commercial",  6=>"Others");//5=>"Commission",
	//title="Freight+Certif.Cost+Design Cost+Studio Cost+Deprec.&Amort.+Operating Expenses+Deprec.&Amort.+Interest+Income Tax"
	
	$sqlOthers="select approved_no as APPROVEDNO, job_id as JOB_ID, lab_test as LAB_TEST, currier_pre_cost as CURRIER, inspection as INSPECTION, commission as COMMISSION, comm_cost as COMM_COST, freight as FREIGHT, certificate_pre_cost as CERTIFICATE, design_cost as DESIGN_COST, studio_cost as STUDIO_COST, depr_amor_pre_cost as DEPR_AMOR, common_oh as COMMON_OH, interest_cost as INTEREST_COST, incometax_cost as INCOMETAX_COST, cm_cost as CM_COST, price_pcs_or_set as PRICE_PCS, price_dzn as PRICE_DZN from wo_pre_cost_dtls_histry where 1=1 and status_active=1 and is_deleted=0 and approved_no in ($approvno) and approval_page=47 $jobCondS $jobidCondition";
	$sqlOthersres = sql_select($sqlOthers);
	foreach($sqlOthersres as $row)
	{
		$poqty=$planqty=0;
		$planqty=$jobDataArr[$row['APPROVEDNO']][$row['JOB_ID']]['plan'];
		$poqty=$jobDataArr[$row['APPROVEDNO']][$row['JOB_ID']]['poqty'];
		$costingparval=$costingPerArr[$row['APPROVEDNO']][$row['JOB_ID']];
		$item_ratio=$summArr[$row['APPROVEDNO']]['ratio'];
		
		$labAmt=$currierAmt=$inspectionAmt=$commissionAmt=$commlAmt=$freightAmt=$certificateAmt=$designAmt=$studioAmt=$deprAmt=$commonOhAmt=$interestAmt=$incometaxAmt=$othersAmt=$cmAmt=0;
		//echo $row['LAB_TEST'].'='.$costingparval.'='.$planqty.'<br>';
		if( ($row['LAB_TEST']*1)!=0) $labAmt=($row['LAB_TEST']/$costingparval)*$poqty;
		if( ($row['CURRIER']*1)!=0) $currierAmt=($row['CURRIER']/$costingparval)*$poqty;
		if( ($row['INSPECTION']*1)!=0) $inspectionAmt=($row['INSPECTION']/$costingparval)*$poqty;
		//if( ($row['COMMISSION']*1)!=0) $commissionAmt=($row['COMMISSION']/$costingparval)*$poqty;
		if( ($row['COMM_COST']*1)!=0) $commlAmt=($row['COMM_COST']/$costingparval)*$poqty;
		if( ($row['FREIGHT']*1)!=0) $freightAmt=($row['FREIGHT']/$costingparval)*$poqty;
		if( ($row['CERTIFICATE']*1)!=0) $certificateAmt=($row['CERTIFICATE']/$costingparval)*$poqty;
		if( ($row['DESIGN_COST']*1)!=0) $designAmt=($row['DESIGN_COST']/$costingparval)*$poqty;
		if( ($row['STUDIO_COST']*1)!=0) $studioAmt=($row['STUDIO_COST']/$costingparval)*$poqty;
		if( ($row['DEPR_AMOR']*1)!=0) $deprAmt=($row['DEPR_AMOR']/$costingparval)*$poqty;
		if( ($row['COMMON_OH']*1)!=0) $commonOhAmt=($row['COMMON_OH']/$costingparval)*$poqty;
		if( ($row['INTEREST_COST']*1)!=0) $interestAmt=($row['INTEREST_COST']/$costingparval)*$poqty;
		if( ($row['INCOMETAX_COST']*1)!=0) $incometaxAmt=($row['INCOMETAX_COST']/$costingparval)*$poqty;
		
		if( ($row['CM_COST']*1)!=0) $cmAmt=($row['CM_COST']/$costingparval)*$poqty;
		//echo $labAmt;
		$othersAmt=$freightAmt+$certificateAmt+$designAmt+$studioAmt+$deprAmt+$commonOhAmt+$interestAmt+$incometaxAmt;
		
		$othersDzn=$row['FREIGHT']+$row['CERTIFICATE']+$row['DESIGN_COST']+$row['STUDIO_COST']+$row['DEPR_AMOR']+$row['COMMON_OH']+$row['INTEREST_COST']+$row['INCOMETAX_COST'];
		
		($othersAmt/$planqty)*$costingparval;
		//echo $row['APPROVEDNO'].'='.$row['JOB_ID'].'='.$labAmt.'<br>';
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][1]['amt']=$labAmt;
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][2]['amt']=$currierAmt;
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][3]['amt']=$inspectionAmt;
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][4]['amt']=$commlAmt;
		//$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][5]['amt']=$commissionAmt;
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][6]['amt']=$currierAmt;
		
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][1]['odzn']=$row['LAB_TEST'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][2]['odzn']=$row['CURRIER'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][3]['odzn']=$row['INSPECTION'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][4]['odzn']=$row['COMM_COST'];
		//$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][5]['odzn']=$row['COMMISSION'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][6]['odzn']=$othersDzn;
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][6]['amt']=$othersAmt;
		
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['cm'][0]['cmdzn']=$row['CM_COST'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['cm'][0]['cmamt']=$cmAmt;
		
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['price'][0]['pcs']=$row['PRICE_PCS'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['price'][0]['dzn']=$row['PRICE_DZN'];
		
		$otCap="";
		$otCap="Freight(".number_format($freightAmt,2).")+Certif.Cost(".number_format($certificateAmt,2).")+Design Cost(".number_format($designAmt,2).")+Studio Cost(".number_format($studioAmt,2).")+Deprec.&Amort.(".number_format($deprAmt,2).")+Operating Expenses(".number_format($commonOhAmt,2).")+Interest(".number_format($interestAmt,2).")+Income Tax(".number_format($incometaxAmt,2).")";
		//echo $otCap.'=<br>';
		
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOB_ID']]['o'][6]['otherscap']=$otCap;
	}
	unset($sqlOthersres);
	
	//print_r($reqQtyAmtArr[1][20043]['o'][6]['otherscap']);
	
	$sqlComm="select approved_no as APPROVEDNO, pre_cost_commiss_cost_dtls_id AS COMMID, job_id as JOBID, particulars_id AS PARTICULARS_ID, commision_rate AS COMMISION_RATE, commission_amount AS COMMISSION_AMOUNT from wo_pre_cost_commis_cost_dtls_h where 1=1 and is_deleted=0 and status_active=1 and approved_no in ($approvno) and approval_page=47 $jobidCondition";
	$sqlCommres = sql_select($sqlComm);
	foreach($sqlCommres as $row)
	{
		$poqty=$planqty=$commamt=0;
		$planqty=$jobDataArr[$row['APPROVEDNO']][$row['JOBID']]['plan'];
		$poqty=$jobDataArr[$row['APPROVEDNO']][$row['JOBID']]['poqty'];
		$costingparval=$costingPerArr[$row['APPROVEDNO']][$row['JOBID']];
		
		$commamt=($row['COMMISSION_AMOUNT']/$costingparval)*$poqty;
		
		if($row['PARTICULARS_ID']==1) $capComm="Buying Commission"; else $capComm="UK Office Commission";
		
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOBID']]['comm'][$capComm]['dznamt']=$row['COMMISSION_AMOUNT'];
		$reqQtyAmtArr[$row['APPROVEDNO']][$row['JOBID']]['comm'][$capComm]['amt']=$commamt;
	}
	unset($sqlCommres);
	
	//print_r($reqQtyAmtArr[1][27565]['o'][6]['otherscap']);
	//echo "kausar"; die;
	
	$exappno=explode(",",$approvno);
	?>
	<table width="1600px" cellspacing="0" border="1" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th colspan="5" style="background:#D4BF55">First Budget [V:<?=$exappno[0]; ?>]</th>
				<th colspan="5" style="background:#CCF">Last Budget [V:<?=$exappno[1]; ?>]</th>
				<th colspan="2" style="background:#D4BFAA">Budget Variance</th>
				<th rowspan="2">Un-Approve Request</th>
			</tr>
			<tr>
				<th width="300">Item Description</th>
				<th width="50">UOM</th>
				<th width="70">Amt/<?=$costingPerStr; ?></th>
				<th width="80">Required Qty</th>     
				<th width="97">Value [USD]</th>
				
				<th width="300">Item Description</th>
				<th width="50">UOM</th>
				<th width="70">Amt/<?=$costingPerStr; ?></th>
				<th width="80">Required Qty</th>     
				<th width="97">Value [USD]</th>
				
				<th width="70">Amt/<?=$costingPerStr; ?></th>
				<th width="97">Value [USD]</th>
			 </tr>
		</thead>
	</table>
	<div style="width:1600px; max-height:400px; overflow-y:scroll" id="scroll_body"> 
		<table width="1580px" border="1" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<tr>
				<td width="600" valign="top">
					<table width="100%" style="margin:0px" border="1" cellspacing="0" class="rpt_table" rules="all">
						<? $varianceArr=array(); $firstVDzn=0; $firstVAmt=0;
                        foreach($reqQtyAmtArr[$exappno[0]] as $idjob=>$jobData)
                        {
                            foreach($jobData['f'] as $fabid=>$fabData)
                            {
                                ?>
                                <tr bgcolor="#99FFFF">
                                    <td width="300" height="31" style="word-break:break-all" title="<?=$fabData['fabric']; ?>"><?=substr($fabData['fabric'], 0, 120); ?>&nbsp;</td>
                                    <td width="50" height="31" style="word-break:break-all"><?=$unit_of_measurement[$fabData['uom']]; ?></td>
                                    <td width="70" height="31" style="word-break:break-all" align="right"><?=number_format($fabData['dzn'],4); ?></td>
                                    <td width="80" height="31" style="word-break:break-all" align="right"><?=number_format($fabData['qty'],4); ?></td>
                                    <td height="31" style="word-break:break-all" align="right"><?=number_format($fabData['amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['f'][$fabid][$exappno[0]]['fdzn']=$fabData['dzn'];
                                $varianceArr[$idjob]['f'][$fabid][$exappno[0]]['famt']=$fabData['amt'];
                                $firstVDzn+=$fabData['dzn'];
                                $firstVAmt+=$fabData['amt'];
                            }
                            foreach($jobData['t'] as $tid=>$trimData)
                            {
                                ?>
                                <tr bgcolor="#FFBF55">
                                    <td width="300" style="word-break:break-all" title="<?=$trimData['trimdesc']; ?>"><?=substr($trimData['trimdesc'], 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all"><?=$unit_of_measurement[$trimData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($trimData['trimamtdzn'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><?=number_format($trimData['trimqty'],4); ?></td>
                                    <td style="word-break:break-all" align="right"><?=number_format($trimData['trimamt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['t'][$tid][$exappno[0]]['tdzn']=$trimData['trimamtdzn'];
                                $varianceArr[$idjob]['t'][$tid][$exappno[0]]['tamt']=$trimData['trimamt'];
                                $firstVDzn+=$trimData['trimamtdzn'];
                                $firstVAmt+=$trimData['trimamt'];
                            }
                            foreach($jobData['e'] as $eid=>$embData)
                            {
                                ?>
                                <tr bgcolor="#CCCCFF">
                                    <td width="300" style="word-break:break-all" title="<?=$embData['emb_name']; ?>"><?=substr($embData['emb_name'], 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all">&nbsp;<? //=$unit_of_measurement[$embData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($embData['embamtdzn'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><?=number_format($embData['emb_qty'],4); ?></td>
                                    <td style="word-break:break-all" align="right"><?=number_format($embData['emb_amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['e'][$eid][$exappno[0]]['edzn']=$embData['embamtdzn'];
                                $varianceArr[$idjob]['e'][$eid][$exappno[0]]['eamt']=$embData['emb_amt'];
                                
                                $firstVDzn+=$embData['embamtdzn'];
                                $firstVAmt+=$embData['emb_amt'];
                            }
                            foreach($jobData['o'] as $oid=>$otherData)
                            {
                                ?>
                                <tr>
                                    <td width="300" style="word-break:break-all" title="<?=$otherData['otherscap']; ?>"><?=substr($othersStrArr[$oid], 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all">&nbsp;<? //=$unit_of_measurement[$embData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($otherData['odzn'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><? //=number_format($otherData['emb_qty'],2); ?>&nbsp;</td>
                                    <td style="word-break:break-all" align="right" title="<?=$otherData['otherscap']; ?>"><?=number_format($otherData['amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['o'][$oid][$exappno[0]]['odzn']=$otherData['odzn'];
                                $varianceArr[$idjob]['o'][$oid][$exappno[0]]['oamt']=$otherData['amt'];
                                
                                $firstVDzn+=$otherData['odzn'];
                                $firstVAmt+=$otherData['amt'];
                            }
							foreach($jobData['comm'] as $comid=>$commData)
                            {
                                ?>
                                <tr bgcolor="#FFFF55">
                                    <td width="300" style="word-break:break-all"><?=substr($comid, 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all">&nbsp;<? //=$unit_of_measurement[$embData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($commData['dznamt'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><? //=number_format($otherData['emb_qty'],2); ?>&nbsp;</td>
                                    <td style="word-break:break-all" align="right"><?=number_format($commData['amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['comm'][$comid][$exappno[0]]['dznamt']=$commData['dznamt'];
                                $varianceArr[$idjob]['comm'][$comid][$exappno[0]]['amt']=$commData['amt'];
                                
                                $firstVDzn+=$commData['dznamt'];
                                $firstVAmt+=$commData['amt'];
                            }
                        }
                        
                        //print_r($varianceArr[27425]['f']); die;
                        ?>
                        <tr bgcolor="#A0A0A4">
                            <td width="300" style="word-break:break-all">Total Cost[Without CM]</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($firstVDzn,4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right"><?=number_format($firstVAmt,4); ?></td>
                        </tr>
                        <?
						foreach($reqQtyAmtArr[$exappno[0]] as $idjob=>$jData)
						{
							$cmAmtDzn=($reqQtyAmtArr[$exappno[0]][$job_id]['price'][0]['pcs']*12)-$firstVDzn;
							$cmAmt=($cmAmtDzn/12)*$jobDataArr[$exappno[0]][$idjob]['poqty'];
							//echo ($reqQtyAmtArr[$exappno[0]][$job_id]['price'][0]['pcs']*12).'-'.$firstVDzn.'='.$cmAmtDzn.'/12='.$jobDataArr[$exappno[0]][$idjob]['poqty'].'<br>';
							
							//$jData['cm'][0]['cmdzn'];
							?>
							<tr>
								<td width="300" style="word-break:break-all" title="<?=$reqQtyAmtArr[$exappno[0]][$job_id]['price'][0]['pcs'].'='.$firstVDzn; ?>">CM Amount/<?=$costingPerStr; ?>[USD]&nbsp;</td>
								<td width="50" style="word-break:break-all">&nbsp;</td>
								<td width="70" style="word-break:break-all" align="right"><?=number_format($cmAmtDzn,4); ?></td>
								<td width="80" style="word-break:break-all" align="right">&nbsp;</td>
								<td style="word-break:break-all" align="right"><?=number_format($cmAmt,4); ?></td>
							</tr>
							<?
							$firstVDzn+=$cmAmtDzn;
							$firstVAmt+=$cmAmt;
							$cmArr[$exappno[0]]['cmdzn']=$cmAmtDzn;
							$cmArr[$exappno[0]]['cmamt']=$cmAmt;
						}
						?>
                        <tr bgcolor="#A0A0A4">
                            <td width="300" style="word-break:break-all">Grand Total Cost</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($firstVDzn,4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right"><?=number_format($firstVAmt,4); ?></td>
                        </tr>
                        <tr>
                            <td width="300" style="word-break:break-all">FOB/<?=$unit_of_measurement[$order_uom]; ?>[USD]</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($reqQtyAmtArr[$exappno[0]][$job_id]['price'][0]['pcs'],4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="300" style="word-break:break-all">SMV/<?=$unit_of_measurement[$order_uom]; ?></td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($jobDataArr[$exappno[0]][$job_id]['smv'],4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="300" style="word-break:break-all" title="CM/Costing Per/Sew SMV">EPM[USD]</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right" title="CM/Costing Per/Sew SMV"><?
							$epmDznFirst=$cmAmtDzn/$costingPerArr[$exappno[0]][$job_id]/$jobDataArr[$exappno[0]][$job_id]['smv'];
							//$epmDznFirst=$reqQtyAmtArr[$exappno[0]][$job_id]['cm'][0]['cmdzn']/$costingPerArr[$exappno[0]][$job_id]/$jobDataArr[$exappno[0]][$job_id]['smv'];
							echo number_format($epmDznFirst,4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right">&nbsp;</td>
                        </tr>
                        
					</table>
				</td>
				<td width="600" valign="top">
					<table width="100%" style="margin:0px" border="1" cellspacing="0" class="rpt_table" rules="all">
						<? $lastVDzn=$lastVAmt=0;
                        foreach($reqQtyAmtArr[$exappno[1]] as $idjob=>$jobData)
                        {
                            foreach($jobData['f'] as $fabid=>$fabData)
                            {
                                ?>
                                <tr bgcolor="#99FFFF">
                                    <td width="300" height="31" style="word-break:break-all" title="<?=$fabData['fabric']; ?>"><?=substr($fabData['fabric'], 0, 120); ?>&nbsp;</td>
                                    <td width="50" height="31" style="word-break:break-all"><?=$unit_of_measurement[$fabData['uom']]; ?></td>
                                    <td width="70" height="31" style="word-break:break-all" align="right"><?=number_format($fabData['dzn'],4); ?></td>
                                    <td width="80" height="31" style="word-break:break-all" align="right"><?=number_format($fabData['qty'],4); ?></td>
                                    <td height="31" style="word-break:break-all" align="right"><?=number_format($fabData['amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['f'][$fabid][$exappno[1]]['fdzn']=$fabData['dzn'];
                                $varianceArr[$idjob]['f'][$fabid][$exappno[1]]['famt']=$fabData['amt'];
                                $lastVDzn+=$fabData['dzn'];
                                $lastVAmt+=$fabData['amt'];
                            }
                            foreach($jobData['t'] as $tid=>$trimData)
                            {
                                ?>
                                <tr bgcolor="#FFBF55">
                                    <td width="300" style="word-break:break-all" title="<?=$trimData['trimdesc']; ?>"><?=substr($trimData['trimdesc'], 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all"><?=$unit_of_measurement[$trimData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($trimData['trimamtdzn'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><?=number_format($trimData['trimqty'],4); ?></td>
                                    <td style="word-break:break-all" align="right"><?=number_format($trimData['trimamt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['t'][$tid][$exappno[1]]['tdzn']=$trimData['trimamtdzn'];
                                $varianceArr[$idjob]['t'][$tid][$exappno[1]]['tamt']=$trimData['trimamt'];
                                $lastVDzn+=$trimData['trimamtdzn'];
                                $lastVAmt+=$trimData['trimamt'];
                            }
                            foreach($jobData['e'] as $eid=>$embData)
                            {
                                ?>
                                <tr bgcolor="#CCCCFF">
                                    <td width="300" style="word-break:break-all" title="<?=$embData['emb_name']; ?>"><?=substr($embData['emb_name'], 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all">&nbsp;<? //=$unit_of_measurement[$embData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($embData['embamtdzn'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><?=number_format($embData['emb_qty'],4); ?></td>
                                    <td style="word-break:break-all" align="right"><?=number_format($embData['emb_amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['e'][$eid][$exappno[1]]['edzn']=$embData['embamtdzn'];
                                $varianceArr[$idjob]['e'][$eid][$exappno[1]]['eamt']=$embData['emb_amt'];
                                $lastVDzn+=$embData['embamtdzn'];
                                $lastVAmt+=$embData['emb_amt'];
                            }
                            foreach($jobData['o'] as $oid=>$otherData)
                            {
                                ?>
                                <tr>
                                    <td width="300" style="word-break:break-all" title="<?=$otherData['otherscap']; ?>"><?=substr($othersStrArr[$oid], 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all">&nbsp;<? //=$unit_of_measurement[$embData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($otherData['odzn'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><? //=number_format($otherData['emb_qty'],2); ?>&nbsp;</td>
                                    <td style="word-break:break-all" align="right" title="<?=$otherData['otherscap']; ?>"><?=number_format($otherData['amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['o'][$oid][$exappno[1]]['odzn']=$otherData['odzn'];
                                $varianceArr[$idjob]['o'][$oid][$exappno[1]]['oamt']=$otherData['amt'];
                                
                                $lastVDzn+=$otherData['odzn'];
                                $lastVAmt+=$otherData['amt'];
                            }
							foreach($jobData['comm'] as $comid=>$commData)
                            {
                                ?>
                                <tr bgcolor="#FFFF55">
                                    <td width="300" style="word-break:break-all"><?=substr($comid, 0, 62); ?>&nbsp;</td>
                                    <td width="50" style="word-break:break-all">&nbsp;<? //=$unit_of_measurement[$embData['uom']]; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($commData['dznamt'],4); ?></td>
                                    <td width="80" style="word-break:break-all" align="right"><? //=number_format($otherData['emb_qty'],2); ?>&nbsp;</td>
                                    <td style="word-break:break-all" align="right"><?=number_format($commData['amt'],4); ?></td>
                                </tr>
                                <?
                                $varianceArr[$idjob]['comm'][$comid][$exappno[1]]['dznamt']=$commData['dznamt'];
                                $varianceArr[$idjob]['comm'][$comid][$exappno[1]]['amt']=$commData['amt'];
                                
                                $lastVDzn+=$commData['dznamt'];
                                $lastVAmt+=$commData['amt'];
                            }
                        }
                        ?>
                        <tr bgcolor="#A0A0A4">
                            <td width="300" style="word-break:break-all">Total Cost[Without CM]&nbsp;</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($lastVDzn,4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right"><?=number_format($lastVAmt,4); ?></td>
                        </tr>
                        <?
                        foreach($reqQtyAmtArr[$exappno[1]] as $idjob=>$jData)
						{
							$cmAmtDzn=($reqQtyAmtArr[$exappno[1]][$job_id]['price'][0]['pcs']*12)-$lastVDzn;//$jData['cm'][0]['cmdzn'];
							$cmAmt=($cmAmtDzn/12)*$jobDataArr[$exappno[1]][$idjob]['poqty'];
							?>
							<tr>
								<td width="300" style="word-break:break-all">CM Amount/<?=$costingPerStr; ?>[USD]&nbsp;</td>
								<td width="50" style="word-break:break-all">&nbsp;</td>
								<td width="70" style="word-break:break-all" align="right"><?=number_format($cmAmtDzn,4); ?></td>
								<td width="80" style="word-break:break-all" align="right">&nbsp;</td>
								<td style="word-break:break-all" align="right"><?=number_format($cmAmt,4); ?></td>
							</tr>
							<?
							$lastVDzn+=$cmAmtDzn;
							$lastVAmt+=$cmAmt;
							
							$cmArr[$exappno[1]]['cmdzn']=$cmAmtDzn;
							$cmArr[$exappno[1]]['cmamt']=$cmAmt;
						}
						?>
                        <tr bgcolor="#A0A0A4">
                            <td width="300" style="word-break:break-all">Grand Total Cost</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($lastVDzn,4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right"><?=number_format($lastVAmt,2); ?></td>
                        </tr>
                        <tr>
                            <td width="300" style="word-break:break-all">FOB/<?=$unit_of_measurement[$order_uom]; ?>[USD]</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($reqQtyAmtArr[$exappno[1]][$job_id]['price'][0]['pcs'],4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="300" style="word-break:break-all">SMV/<?=$unit_of_measurement[$order_uom]; ?></td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($jobDataArr[$exappno[1]][$job_id]['smv'],4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="300" style="word-break:break-all" title="CM/Costing Per/Sew SMV">EPM[USD]</td>
                            <td width="50" style="word-break:break-all">&nbsp;</td>
                            <td width="70" style="word-break:break-all" align="right" title="CM/Costing Per/Sew SMV"><?
							$epmDznLast=$cmAmtDzn/$costingPerArr[$exappno[1]][$job_id]/$jobDataArr[$exappno[1]][$job_id]['smv'];
							
							//$reqQtyAmtArr[$exappno[1]][$job_id]['cm'][0]['cmdzn']/$costingPerArr[$exappno[1]][$job_id]/$jobDataArr[$exappno[1]][$job_id]['smv'];
							echo number_format($epmDznLast,4); ?></td>
                            <td width="80" style="word-break:break-all" align="right">&nbsp;</td>
                            <td style="word-break:break-all" align="right">&nbsp;</td>
                        </tr>
					</table>
				</td>
				<td width="170" valign="top">
                	<table width="100%" style="margin:0px" border="1" cellspacing="0" class="rpt_table" rules="all">
						<?
                        foreach($varianceArr as $idjob=>$jobData)
                        {
                            foreach($jobData['f'] as $fabid=>$fvData)
                            {
                                $fabamtdzn=$fabamt=0;
                                $fabamtdzn=$fvData[$exappno[0]]['fdzn']-$fvData[$exappno[1]]['fdzn'];
                                $fabamt=$fvData[$exappno[0]]['famt']-$fvData[$exappno[1]]['famt'];
                                ?>
                                <tr>
                                    <td width="70" height="31" style="word-break:break-all" align="right"><?=number_format($fabamtdzn,4); ?></td>
                                    <td height="31" style="word-break:break-all" align="right"><?=number_format($fabamt,4); ?></td>
                                </tr>
                                <?
                            }
                            foreach($jobData['t'] as $tid=>$trimData)
                            {
                                $trimamtdzn=$trimamt=0;
                                $trimamtdzn=$trimData[$exappno[0]]['tdzn']-$trimData[$exappno[1]]['tdzn'];
                                $trimamt=$trimData[$exappno[0]]['tamt']-$trimData[$exappno[1]]['tamt'];
                                ?>
                                <tr>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($trimamtdzn,4); ?></td>
                                    <td style="word-break:break-all" align="right"><?=number_format($trimamt,4); ?></td>
                                </tr>
                                <?
                            }
                            foreach($jobData['e'] as $eid=>$embData)
                            {
                                $embamtdzn=$embamt=0;
                                $embamtdzn=$embData[$exappno[0]]['edzn']-$embData[$exappno[1]]['edzn'];
                                $embamt=$embData[$exappno[0]]['eamt']-$embData[$exappno[1]]['eamt'];
                                ?>
                                <tr>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($embamtdzn,4); ?></td>
                                    <td style="word-break:break-all" align="right"><?=number_format($embamt,4); ?></td>
                                </tr>
                                <?
                            }
                            foreach($jobData['o'] as $oid=>$otherData)
                            {
                                $othersamtdzn=$othersamt=0;
                                $othersamtdzn=$otherData[$exappno[0]]['odzn']-$otherData[$exappno[1]]['odzn'];
                                $othersamt=$otherData[$exappno[0]]['oamt']-$otherData[$exappno[1]]['oamt'];
                                ?>
                                <tr>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($othersamtdzn,4); ?>&nbsp;</td>
                                    <td style="word-break:break-all" align="right"><?=number_format($othersamt,4); ?></td>
                                </tr>
                                <?
                            }
							foreach($jobData['comm'] as $comid=>$comData)
                            {
                                $comamtdzn=$comamt=0;
                                $comamtdzn=$comData[$exappno[0]]['dznamt']-$comData[$exappno[1]]['dznamt'];
                                $comamt=$comData[$exappno[0]]['oamt']-$comData[$exappno[1]]['oamt'];
                                ?>
                                <tr>
                                    <td width="70" style="word-break:break-all" align="right"><?=number_format($comamtdzn,4); ?>&nbsp;</td>
                                    <td style="word-break:break-all" align="right"><?=number_format($comamt,4); ?></td>
                                </tr>
                                <?
                            }
                        }
						$varianceTotCostWithoutCmDzn=$firstVDzn-$lastVDzn;
						$varianceTotCostWithoutCm=$firstVAmt-$lastVAmt;
						
						$varianceCmDzn=$cmArr[$exappno[0]]['cmdzn']-$cmArr[$exappno[1]]['cmdzn'];
						
						//(($reqQtyAmtArr[$exappno[0]][$job_id]['price'][0]['pcs']*12)-$firstVDzn)-(($reqQtyAmtArr[$exappno[1]][$job_id]['price'][0]['pcs']*12)-$lastVDzn);
						$varianceCm=$cmArr[$exappno[0]]['cmamt']-$cmArr[$exappno[1]]['cmamt'];//$reqQtyAmtArr[$exappno[0]][$job_id]['cm'][0]['cmamt']-$reqQtyAmtArr[$exappno[1]][$job_id]['cm'][0]['cmamt'];
						
						$varianceGtotDzn=$firstVDzn-$lastVDzn;
						$varianceGtot=$firstVAmt-$lastVAmt;
						
						$varianceFob=$reqQtyAmtArr[$exappno[0]][$job_id]['price'][0]['pcs']-$reqQtyAmtArr[$exappno[1]][$job_id]['price'][0]['pcs'];
						$varianceSmv=$jobDataArr[$exappno[0]][$job_id]['smv']-$jobDataArr[$exappno[1]][$job_id]['smv'];
						$varianceEpm=$epmDznFirst-$epmDznLast;
                        ?>
                        <tr bgcolor="#A0A0A4">
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($varianceTotCostWithoutCmDzn,4); ?>&nbsp;</td>
                            <td style="word-break:break-all" align="right"><?=number_format($varianceTotCostWithoutCm,4); ?></td>
                        </tr>
                        <tr>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($varianceCmDzn,4); ?>&nbsp;</td>
                            <td style="word-break:break-all" align="right"><?=number_format($varianceCm,4); ?></td>
                        </tr>
                        <tr bgcolor="#A0A0A4">
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($varianceGtotDzn,4); ?>&nbsp;</td>
                            <td style="word-break:break-all" align="right"><?=number_format($varianceCm,4); ?></td>
                        </tr>
                        <tr>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($varianceFob,4); ?>&nbsp;</td>
                            <td style="word-break:break-all" align="right"><? //=number_format($varianceCm,4); ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($varianceSmv,4); ?>&nbsp;</td>
                            <td style="word-break:break-all" align="right"><? //=number_format($varianceCm,4); ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="70" style="word-break:break-all" align="right"><?=number_format($varianceEpm,4); ?>&nbsp;</td>
                            <td style="word-break:break-all" align="right"><? //=number_format($varianceCm,4); ?>&nbsp;</td>
                        </tr>
					</table>
                </td>
				<td valign="top" style="word-break:break-all">
                <?
				$un_approve_reques_data_cause_array=sql_select("select b.id, b.approval_cause, b.user_id, b.insert_date, b.update_date from fabric_booking_app_cause_source b where  b.booking_id in (".$bomids.") and b.entry_form=47 and b.approval_type=2 and b.status_active=1 order by b.id ASC");
				//echo"select b.id,b.approval_cause,b.user_id,b.insert_date,b.update_date from fabric_booking_app_cause_source b where  b.booking_id in (".$bomids.") and b.entry_form=47 and b.approval_type=2 and b.status_active=1 order by b.id ASC"; 
				$approval_cause="";
				foreach($un_approve_reques_data_cause_array as $hrow)
				{
					//echo $hrow[csf('approval_cause')].'<br>';
					/*if($causeArrChk[$hrow[csf('approval_cause')].'_'.$hrow[csf('id')]]=='')
					{*/
						//$unapprove_cause_arr[$approval_data]['approval_cause']=$hrow[csf('approval_cause')];
						if($approval_caus=="") $approval_caus=$hrow[csf('approval_cause')]; else $approval_caus.=', '.$hrow[csf('approval_cause')];
					//ddd}
				}
				echo $approval_caus;
				?>
                </td>
			</tr>
        </table>
    </div>
   
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
	echo "$total_data####$filename####$totRow";
	exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Image PopUp","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$jobNos="'".implode(",",explode(',',$job_no))."'";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id in ($jobNos) and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			if($row[csf('image_location')]!="")
			{
				?>
				<td><img src='../../../<?=$row[csf('image_location')]; ?>' height='250' width='300' /></td>
				<?
			}
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}
?>
