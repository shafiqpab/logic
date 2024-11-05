<?php
/*-------------------------------------------- Comments -----------------------
Purpose			         : 	This Form Will Create Garments Pre Cost Entry report.
Functionality	         :
JS Functions	         :
Created by		         :	Zakaria Joy
Creation date 	         : 	15-09-2022
QC Performed BY	         :
QC Date			         :
Comments		         : This Page only Use for Pre-cost entry page report 
-------------------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$type = $_REQUEST['type'];
$permission = $_SESSION['page_permission'];

if($action=="preCostRpt4")//Cost Rpt5, Btn Id 10 ISD-23-27566 for Renaissance
{   
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($txt_job_no=="") $job_noCond=''; else $job_noCond=" and a.job_no=".$txt_job_no."";

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";

	//array for display name
	
	$comp=return_library_array("select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array("select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array("select id, season_name from lib_buyer_season",'id','season_name');
	//$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	 $po_qty=0; $po_plun_cut_qty=0; $total_set_qnty=0; $job_in_orders = ''; $pulich_ship_date=''; $job_in_file = ''; $job_in_ref = ''; $postatus='';
	 $sql_po="select a.job_no, a.total_set_qnty, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.is_confirmed, c.item_number_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.job_no =".$txt_job_no." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	//echo $sql_po;
	$sql_po_data=sql_select($sql_po); 
	foreach($sql_po_data as $sql_po_row){
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		
		$job_in_orders .= $sql_po_row[csf('po_number')].", ";
		$pulich_ship_date = $sql_po_row[csf('pub_shipment_date')];
		$job_in_file .= $sql_po_row[csf('file_no')].",";
		$job_in_ref .= $sql_po_row[csf('grouping')].",";
		$postatus .= $order_status[$sql_po_row[csf('is_confirmed')]].",";
		//echo $sql_po_row[csf('is_confirmed')].'-'.$order_status[$sql_po_row[csf('is_confirmed')]].'<br>';
	}
	//echo $postatus; die;
	$job_in_orders = substr(trim($job_in_orders),0,-1);
	$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
	$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
	$postatus=array_unique(explode(",",rtrim($postatus,", ")));

	foreach ($job_ref as $ref){
		$ref_cond.=", ".$ref;
	}
	$file_con='';
	foreach ($job_file as $file){
		if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
	}
	$postatusstrshow='';
	foreach ($postatus as $postatusstr){
		if($postatusstrshow=='') $postatusstrshow=$postatusstr; else $postatusstrshow.=", ".$postatusstr;
	}
	//echo $postatusstrshow.'==';

	$gmtsitem_ratio_array=array();
	$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no =".$txt_job_no."");// where job_no ='FAL-14-01157'
	foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row){
		$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];
	}
	
	$financial_para=array();
	$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
	foreach($sql_std_para as $row )
	{
		$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
		$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
		$diff=datediff('d',$applying_period_date,$applying_period_to_date);
		for($j=0;$j<$diff;$j++)
		{
			//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
			$date_all=add_date(str_replace("'","",$applying_period_date),$j);
			$newdate =change_date_format($date_all,'','',1);
			$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
			$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
			$financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
		}
	}
	//print_r($financial_para['12-Nov-2023']);
	
	$job_no=str_replace("'","",$txt_job_no);
	
	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}
	if(str_replace("'",'',$txt_po_breack_down_id) !=""){
		$condition->po_id("in($txt_po_breack_down_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$yarn= new yarn($condition);
	$trim= new trims($condition);
	$emblishment= new emblishment($condition);
	$wash= new wash($condition);
	$other= new other($condition);
	
	$commercial= new commercial($condition);
	$commision= new commision($condition);
	
	$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
	
	$trims_costing_arr=$trim->getAmountArray_by_job();
	$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
	
	$commercial_costing_arr=$commercial->getAmountArray_by_job();
	$commission_costing_arr=$commision->getAmountArray_by_job();
	$other_costing_arr=$other->getAmountArray_by_job();
	
	$ttl_cm_cost=$other_costing_arr[$job_no]['cm_cost'];
	
	$sql_dtls = "select job_no, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, deffdlc_cost, deffdlc_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche
	from wo_pre_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array_new=sql_select($sql_dtls);
	
	$summary_data=array();
	foreach($data_array_new as $row_new ){
		$summary_data[price_dzn]=$row_new[csf("price_dzn")];
		$summary_data[price_dzn_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
		$summary_data[commission]=$row_new[csf("commission")];
		$summary_data[trims_cost]=$row_new[csf("trims_cost")];
		$summary_data[emb_cost]=$row_new[csf("embel_cost")];

		$summary_data[lab_test]=$row_new[csf("lab_test")];
		$summary_data[lab_test_job]=$other_costing_arr[$row_new[csf("job_no")]]['lab_test'];

		$summary_data[inspection]=$row_new[csf("inspection")];
		$summary_data[inspection_job]=$other_costing_arr[$row_new[csf("job_no")]]['inspection'];

		$summary_data[freight]=$row_new[csf("freight")];
		$summary_data[freight_job]=$other_costing_arr[$row_new[csf("job_no")]]['freight'];

		$summary_data[currier_pre_cost]=$row_new[csf("currier_pre_cost")];
		$summary_data[currier_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['currier_pre_cost'];

		$summary_data[certificate_pre_cost]=$row_new[csf("certificate_pre_cost")];
		$summary_data[certificate_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['certificate_pre_cost'];
		$summary_data[wash_cost]=$row_new[csf("wash_cost")];

		$summary_data[OtherDirectExpenses]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];
		$summary_data[OtherDirectExpenses_job]=$summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job];

		$summary_data[cm_cost]=$row_new[csf("cm_cost")];
		$summary_data[cm_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['cm_cost'];
		$summary_data[comm_cost]=$row_new[csf("comm_cost")];
		$summary_data[common_oh]=$row_new[csf("common_oh")];
		$summary_data[common_oh_job]=$other_costing_arr[$row_new[csf("job_no")]]['common_oh'];
		$summary_data[depr_amor_pre_cost]=$row_new[csf("depr_amor_pre_cost")];
		$summary_data[depr_amor_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
		$summary_data[margindzn]=$row_new[csf("margin_dzn")];
		$summary_data[fabric_percent]=$row_new[csf("fabric_cost_percent")];
		$summary_data[trims_percent]=$row_new[csf("trims_cost_percent")];
		$summary_data[wash_percent]=$row_new[csf("wash_cost_percent")];
		$summary_data[emb_percent]=$row_new[csf("embel_cost_percent")];
		$summary_data[commercial_percent]=$row_new[csf("comm_cost_percent")];
		$summary_data[currier_percent]=$row_new[csf("currier_percent")];
		$summary_data[commission_percent]=$row_new[csf("commission_percent")];
		$summary_data[lab_test_percent]=$row_new[csf("lab_test_percent")];
		$summary_data[freight_percent]=$row_new[csf("freight_percent")];
		$summary_data[margin_dzn_percent]=$row_new[csf("margin_dzn_percent")];
		$summary_data[cm_cost_percent]=$row_new[csf("cm_cost_percent")];
		$summary_data[inspection_percent]=$row_new[csf("inspection_percent")];
		$summary_data[deffdlc_percent]=$row_new[csf("deffdlc_percent")];
		$summary_data[common_oh_percent]=$row_new[csf("common_oh_percent")];
	}
	unset($data_array_new);

	$fab_knit_req_kg_avg=0; $fab_woven_req_yds_avg=0;
	
    $sql = "select a.job_no, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.style_ref_no, a.quotation_id, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price, b.costing_per, b.budget_minute, b.costing_date, b.exchange_rate, b.incoterm, b.sew_smv, b.cut_smv, b.sew_effi_percent, b.cut_effi_percent, b.approved, c.fab_knit_req_kg, c.fab_knit_fin_req_kg, c.fab_woven_req_yds, c.fab_woven_fin_req_yds, c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 $job_noCond $company_name $cbo_buyer_name order by a.job_no";
	$data_array=sql_select($sql);
	$uom=""; $sew_smv=0; $cut_smv=0; $sew_effi_percent=0; $cut_effi_percent=$order_price_per_dzn=0; $cpmCal=0; $poQty=0;
	foreach ($data_array as $row){
		$order_price_per_dzn=0; $order_job_qnty=0; $avg_unit_price=0;
		$sew_smv=$row[csf("sew_smv")];
	    $cut_smv=$row[csf("cut_smv")];
	    $sew_effi_percent=$row[csf("sew_effi_percent")];
	    $cut_effi_percent=$row[csf("cut_effi_percent")];
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$pre_costing_date=change_date_format($row[csf('costing_date')],'','',1);
		$poQty=$row[csf("job_quantity")];
		
		if($row[csf("costing_per")]==1){
            $order_price_per_dzn=12;
            $costing_for=" DZN";
        }
        else if($row[csf("costing_per")]==2){
            $order_price_per_dzn=1;
            $costing_for=" PCS";
        }
        else if($row[csf("costing_per")]==3){
            $order_price_per_dzn=24;
            $costing_for=" 2 DZN";
        }
        else if($row[csf("costing_per")]==4){
            $order_price_per_dzn=36;
            $costing_for=" 3 DZN";
        }
        else if($row[csf("costing_per")]==5){
            $order_price_per_dzn=48;
            $costing_for=" 4 DZN";
        }
		?>
        <style>
		.vl {
		  border-left: 3px solid green;
		  position: absolute;
		  left: 50%;
		  top: 0;
		}
		</style>
        <table style="width:930px">
        	<tr>
            	<td colspan="3" align="left" style="font-size:14px; font-family:'Calibri Light';">
                	<b>COST SHEET</b>
                    <br />
                    <b style="font-size:14px; font-family:'Calibri Light'; background-color:#FFFFCC">Cost Sheet Revised No: <?
						$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_pre_cost_mst a, approval_history b where a.id=b.mst_id and a.job_no=".$txt_job_no." and b.entry_form=46");
							if(isset($nameArray_approved[0][csf('approved_no')]) ){
								echo ($nameArray_approved[0][csf('approved_no')]-1)*1;
							}
							else{ echo 0; }
						 ?>
					</b>
                </td>
			    <?
                $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id=$cbo_company_name","image_location");
                $path=($path)?$path:'../../';
                ?>
                <td colspan="3" align="right"><img src='<? echo $path.$image_location; ?>' height='40' width='100' /><br /><?=date("l\, jS \of F\, Y"); ?></td>
           </tr>
       </table>

       <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px" rules="all">
            <tr>
                <td width="120" style="font-size:14px; font-family:'Calibri Light';"><b>Company</b></td>
                <td width="150" style="font-size:14px; font-family:'Calibri Light';"><b><? echo $comp[$row[csf("company_name")]]; ?></b></td>
                <td width="130" style="font-size:14px; font-family:'Calibri Light';" ><b>SMV/<?=$costing_for; ?></b></td>
                <td width="150" style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><? echo $sew_smv; ?></b></td>
                <td rowspan="12">
                	<?
					//echo $financial_para[$pre_costing_date][cost_per_minute].'=='.$row[csf("exchange_rate")].'=='.($sew_effi_percent/100);
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$job_no]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$job_no]);
					$yarn_costing=$yarn_costing_arr[$job_no];
					
					$fabricCost=$fab_purchase_knit+$fab_purchase_woven+$yarn_costing;
					//echo $financial_para[$pre_costing_date][cost_per_minute].'=='.$row[csf("exchange_rate")].'=='.($sew_effi_percent/100);
					$cpmCal=($financial_para[$pre_costing_date][cost_per_minute]/$row[csf("exchange_rate")])/($sew_effi_percent/100);
					$totMaterialCost=$fabricCost+$trims_costing_arr[$job_no]+$emblishment_costing_arr_wash[$job_no]+$emblishment_costing_arr[$job_no];
					$otherCost=$commercial_costing_arr[$job_no]+$other_costing_arr[$job_no]['currier_pre_cost']+$commission_costing_arr[$job_no]+$other_costing_arr[$job_no]['lab_test']+$other_costing_arr[$job_no]['inspection']+$other_costing_arr[$job_no]['common_oh']+$other_costing_arr[$job_no]['deffdlc_cost']+$other_costing_arr[$job_no]['freight'];
					
					$breakevencm=$cpmCal*$sew_smv*$poQty;
					$calCM=$order_values-($totMaterialCost+$otherCost);
					$cmPcs=$calCM/$poQty;
					$totalMargin=$calCM-$breakevencm;
					$marginPcs=$totalMargin/$poQty;
					
					// image show here  -------------------------------------------
					$sqlData = "select id,master_tble_id,image_location from common_photo_library where master_tble_id=".$txt_job_no."";
					$data_array_img=sql_select($sqlData);
					$path=($path)?$path:'../../';
					?>
					<div>
					<? foreach($data_array_img as $inf){ ?>
						<img  src='<? echo $path.$inf[csf("image_location")]; ?>' border="1" height='200px' width='150px' />
					<?  } ?>
					</div>
                </td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Job Number</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$row[csf("job_no")]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Planned Efficiency</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$sew_effi_percent; ?>%</b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Buyer</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC"><b>Margin/<?=$costing_for; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75"><b><?=fn_number_format($marginPcs,3); ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75" align="center"><?=fn_number_format((($totalMargin/$order_values)*100),2).' %';//$summary_data[margin_dzn_percent].' %'; ?></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Brand</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$brand_arr[$row[csf("brand_id")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" ><b>CM/<?=$costing_for; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75"><b><?=fn_number_format($cmPcs,3); ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75" align="center"><b><?=fn_number_format((($calCM/$order_values)*100),2).' %';//$summary_data[cm_cost_percent].' %'; ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Costed Date</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=change_date_format($pre_costing_date); ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC"><b>EPM</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" colspan="2"><b><? $epm=$cmPcs/$sew_smv; echo fn_number_format($epm,3); ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Season</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$season_arr[$row[csf("season_buyer_wise")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC"><b>CPM</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" colspan="2" title="CPM (USD)/Planned Efficiency"><b><?=fn_number_format($cpmCal,3); ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Garments Item</b></td>
                <?
                    $grmnt_items = "";
                    if($garments_item[$row[csf("gmts_item_id")]]=="")
                    {

                        $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
                        foreach($grmts_sql as $key=>$val){
                            $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
                        }
                        $grmnt_items = substr_replace($grmnt_items,"",-1,1);
                    }else{
                        $grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
                    }
					
					$appStatus="No"; $appStatusTdColor="#FF7377";
					if($row[csf("approved")]==1 || $row[csf("approved")]==3) { $appStatus="Yes";  $appStatusTdColor="#90EE90"; }
                ?>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$grmnt_items; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Order Qty</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$row[csf("job_quantity")]." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Buyer Style Ref. No</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$row[csf("style_ref_no")]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Plan Qty</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$po_plun_cut_qty/$total_set_qnty." ". $unit_of_measurement[$row[csf("order_uom")]];?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Costing Per</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$costing_per[$row[csf("costing_per")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Price Per Piece</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$row[csf("avg_unit_price")]; ?> USD</b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Order Status</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$postatusstrshow; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Total Value</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=fn_number_format($order_values,3); ?> USD</b></td>
                
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Terms</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$incoterm[$row[csf("incoterm")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Cost Sheet Approved</b></td>
                <td style="font-size:14px; font-family:'Calibri Light'; background-color:<?=$appStatusTdColor; ?>" colspan="2"><b><?=$appStatus; ?></b></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-family:'Calibri Light';">&nbsp;</td>
                <td style="font-size:14px; font-family:'Calibri Light';">&nbsp;</td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Commercial Cost %</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$summary_data[commercial_percent]; ?></b></td>
            </tr>
        </table>
        <?
        $order_job_qnty=$row[csf("job_quantity")];
        $avg_unit_price=$row[csf("avg_unit_price")];
	}//end first foearch
	//start	all summary report here -------------------------------------------
	
	
	$lib_yarn_count=return_library_array("select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$fabricWiseTotRateArr=array();
	?>
    <br />
    <div style="border:2px solid black; width:930px">
    <table cellpadding="1" cellspacing="1" style="width:920px">
    	<tr>
        	<td width="306" valign="top">
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:305px" rules="all">
                	<tr>
                    	<td style="font-size:14px; font-family:'Calibri Light';" colspan="3" align="center"><b>Materials Cost Summary</b></td>
                    </tr>
                	<tr>
                        <td width="120" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Item</b></td>
                        <td width="100" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Value</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b>%</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Yarn</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center" title="<?=$fab_purchase_knit.'='.$fab_purchase_woven.'='.$yarn_costing; ?>">$ <?=fn_number_format($fabricCost,3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($fabricCost/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Trims</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($trims_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($trims_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Wash</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($emblishment_costing_arr_wash[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($emblishment_costing_arr_wash[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Embellishment</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($emblishment_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($emblishment_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';"><b>&nbsp;</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"><b>&nbsp;</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"><b>&nbsp;</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><b>Total :</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><? echo fn_number_format($totMaterialCost,3); ?></b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><?=fn_number_format((($totMaterialCost/$order_values)*100),2); ?></b></td>
                    </tr>
                </table>
            </td>
            <td width="3">&nbsp;</td>
        	<td width="306" valign="top">
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:305px" rules="all">
                	<tr>
                    	<td style="font-size:14px; font-family:'Calibri Light';" colspan="3" align="center"><b>Other Costs Summary</b></td>
                    </tr>
                	<tr>
                        <td width="120" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Item</b></td>
                        <td width="100" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Value</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b>%</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Commercial Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($commercial_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($commercial_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Courier Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['currier_pre_cost'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['currier_pre_cost']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Commission</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($commission_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($commission_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Lab Test</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['lab_test'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['lab_test']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Inspection Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['inspection'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['inspection']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Opt Exp Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['common_oh'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['common_oh']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Deffd. LC/DC Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['deffdlc_cost'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['deffdlc_cost']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Freight Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['freight'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['freight']/$order_values)*100),2); ?></td>
                    </tr>
                    <!--<tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >CM Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <? //=fn_number_format($other_costing_arr[$job_no]['cm_cost'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><? //=fn_number_format((($other_costing_arr[$job_no]['cm_cost']/$order_values)*100),2); ?></td>
                    </tr>-->
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><b>Total :</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><? echo fn_number_format($otherCost,3); ?></b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><?=fn_number_format((($otherCost/$order_values)*100),2); ?></b></td>
                    </tr>
                </table>
            </td>
            <td width="3">&nbsp;</td>
            <td valign="top">
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:296px" rules="all">
                	<tr>
                    	<td style="font-size:14px; font-family:'Calibri Light';" colspan="3" align="center"><b>Order Profitability Summary</b></td>
                    </tr>
                    <tr>
                        <td width="150" style="font-size:14px; font-family:'Calibri Light';" >Total FOB Value</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($order_values,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Materials Costs</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($totMaterialCost,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Other Costs</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($otherCost,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >CM</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <? echo fn_number_format($calCM,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Breakeven CM :</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center" title="CPM*SMV*PO QTY">$<? echo fn_number_format($breakevencm,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><b>Margin :</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><? echo fn_number_format($totalMargin,3); ?></b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>
	
    <!--Yarn =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Yarn Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                    <td width="82" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Yarn Type</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Yarn Count</td>
                    <td width="200" style="font-size:14px; font-family:'Calibri Light';">Yarn Description</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Process Loss %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
          	
            
			<?
            $yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
			$sql_yarn = "select MIN(ID) AS ID, LISTAGG(CAST(FABRIC_COST_DTLS_ID AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY FABRIC_COST_DTLS_ID) as FABRIC_ROWID, COUNT_ID, COPM_ONE_ID, PERCENT_ONE, COPM_TWO_ID, PERCENT_TWO, COLOR, TYPE_ID, MIN(CONS_RATIO) AS CONS_RATIO, SUM(CONS_QNTY) AS CONS_QNTY, RATE, SUM(AMOUNT) AS AMOUNT from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color, type_id, rate";
            $sql_yarnArr=sql_select($sql_yarn); $i=1; $yarnamtTotDzn=$yarnReqAmttotal=$yarnPerTotal=0;
            foreach($sql_yarnArr as $yrow)
            {
				$item_descrition ="";
				if($yrow["PERCENT_ONE"]==100)
					$item_descrition = $lib_yarn_count[$yrow["COUNT_ID"]]." ".$composition[$yrow["COPM_ONE_ID"]]." ".$yrow["PERCENT_ONE"]."% ".$color_library[$yrow["COLOR"]]." ".$yarn_type[$yrow["TYPE_ID"]];
            	else
					$item_descrition = $lib_yarn_count[$yrow["COUNT_ID"]]." ".$composition[$yrow["COPM_ONE_ID"]]." ".$yrow["PERCENT_ONE"]."% ".$composition[$yrow["COPM_TWO_ID"]]." ".$yrow["PERCENT_TWO"]."% ".$color_library[$yrow["COLOR"]]." ".$yarn_type[$yrow["TYPE_ID"]];
					
				$yrow["UOM"]=12;
				
                $rowYarnReqQty=$rowYarnReqAmt=$yarn_per=0;
                $rowYarnReqQty=$yarn_data_array[$yrow["COUNT_ID"]][$yrow["COPM_ONE_ID"]][$yrow["PERCENT_ONE"]][$yrow["TYPE_ID"]][$yrow["COLOR"]][$yrow["RATE"]]['qty'];
                $rowYarnReqAmt=$yarn_data_array[$yrow["COUNT_ID"]][$yrow["COPM_ONE_ID"]][$yrow["PERCENT_ONE"]][$yrow["TYPE_ID"]][$yrow["COLOR"]][$yrow["RATE"]]['amount'];
                
                $yarn_per=($rowYarnReqAmt/$order_values)*100;
                ?>
                <tr>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$yarn_type[$yrow["TYPE_ID"]]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$lib_yarn_count[$yrow["COUNT_ID"]]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$item_descrition; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$yrow["UOM"]]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["CONS_QNTY"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($yrow["AVG_PROCESS_LOSS"],2); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["CONS_QNTY"],3); ?></td>
                    
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowYarnReqQty,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["RATE"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["AMOUNT"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowYarnReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarn_per,2); ?></td>
                </tr>
                <?	
				 $yarnamtTotDzn+=$yrow["AMOUNT"];
				 $yarnReqAmttotal+=$rowYarnReqAmt;
				 $yarnPerTotal+=$yarn_per;
				 
				 $exfabid="";
				 $exfabid=array_unique(array_unique(explode(",",$yrow['FABRIC_ROWID'])));
				 foreach($exfabid as $yfabid)
				 {
				 	$fabricWiseTotRateArr[$yfabid]+=$rowYarnReqAmt;
				 }
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="7" align="right"><b>Total Yarn Cost</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarnamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarnReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarnPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else { 
		if(($yarn_costing*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Yarn Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <tbody>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <td width="82" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Yarn Type</td>
                            <td width="80" style="font-size:14px; font-family:'Calibri Light';">Yarn Count</td>
                            <td width="200" style="font-size:14px; font-family:'Calibri Light';">Yarn Description</td>
                            <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                            <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                            <td width="50" style="font-size:14px; font-family:'Calibri Light';">Process Loss %</td>
                            <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                            
                            <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                            <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                            <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></td>
                            <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                            <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                        </tr>
                    
                    <?
                    $yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
                    $sql_yarn = "select MIN(ID) AS ID, LISTAGG(CAST(FABRIC_COST_DTLS_ID AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY FABRIC_COST_DTLS_ID) as FABRIC_ROWID, COUNT_ID, COPM_ONE_ID, PERCENT_ONE, COPM_TWO_ID, PERCENT_TWO, COLOR, TYPE_ID, MIN(CONS_RATIO) AS CONS_RATIO, SUM(CONS_QNTY) AS CONS_QNTY, RATE, SUM(AMOUNT) AS AMOUNT from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color, type_id, rate";
                    $sql_yarnArr=sql_select($sql_yarn); $i=1; $yarnamtTotDzn=$yarnReqAmttotal=$yarnPerTotal=0;
                    foreach($sql_yarnArr as $yrow)
                    {
                        $item_descrition ="";
                        if($yrow["PERCENT_ONE"]==100)
                            $item_descrition = $lib_yarn_count[$yrow["COUNT_ID"]]." ".$composition[$yrow["COPM_ONE_ID"]]." ".$yrow["PERCENT_ONE"]."% ".$color_library[$yrow["COLOR"]]." ".$yarn_type[$yrow["TYPE_ID"]];
                        else
                            $item_descrition = $lib_yarn_count[$yrow["COUNT_ID"]]." ".$composition[$yrow["COPM_ONE_ID"]]." ".$yrow["PERCENT_ONE"]."% ".$composition[$yrow["COPM_TWO_ID"]]." ".$yrow["PERCENT_TWO"]."% ".$color_library[$yrow["COLOR"]]." ".$yarn_type[$yrow["TYPE_ID"]];
                            
                        $yrow["UOM"]=12;
                        
                        $rowYarnReqQty=$rowYarnReqAmt=$yarn_per=0;
                        $rowYarnReqQty=$yarn_data_array[$yrow["COUNT_ID"]][$yrow["COPM_ONE_ID"]][$yrow["PERCENT_ONE"]][$yrow["TYPE_ID"]][$yrow["COLOR"]][$yrow["RATE"]]['qty'];
                        $rowYarnReqAmt=$yarn_data_array[$yrow["COUNT_ID"]][$yrow["COPM_ONE_ID"]][$yrow["PERCENT_ONE"]][$yrow["TYPE_ID"]][$yrow["COLOR"]][$yrow["RATE"]]['amount'];
                        
                        $yarn_per=($rowYarnReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$yarn_type[$yrow["TYPE_ID"]]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$lib_yarn_count[$yrow["COUNT_ID"]]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$item_descrition; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$yrow["UOM"]]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["CONS_QNTY"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($yrow["AVG_PROCESS_LOSS"],2); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["CONS_QNTY"],3); ?></td>
                            
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowYarnReqQty,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["RATE"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yrow["AMOUNT"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowYarnReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarn_per,2); ?></td>
                        </tr>
                        <?	
                         $yarnamtTotDzn+=$yrow["AMOUNT"];
                         $yarnReqAmttotal+=$rowYarnReqAmt;
                         $yarnPerTotal+=$yarn_per;
						 
						 $exfabid="";
						 $exfabid=array_unique(array_unique(explode(",",$yrow['FABRIC_ROWID'])));
						 foreach($exfabid as $yfabid)
						 {
							$fabricWiseTotRateArr[$yfabid]+=$rowYarnReqAmt;
						 }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="7" align="right"><b>Total Yarn Cost</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarnamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarnReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($yarnPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Trims =====================-->
    <?
	$sql_trim = "select a.ID, a.JOB_NO, a.TRIM_GROUP, a.DESCRIPTION, a.CONS_UOM, a.TOT_CONS, a.EX_PER, a.CONS_DZN_GMTS, a.RATE, a.AMOUNT, b.ITEM_NAME, b.TRIM_TYPE from wo_pre_cost_trim_cost_dtls a, lib_item_group b where a.TRIM_GROUP=b.id and a.job_no=".$txt_job_no." and a.status_active=1 and a.is_deleted=0 order by b.TRIM_TYPE, a.seq Asc";
	
	$data_array_trim=sql_select($sql_trim);
	
	$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
	$trim_amount_arr=$trim->getAmountArray_precostdtlsid(); $trimsDataArr=array();
	foreach($data_array_trim as $trow)
	{
		if($trow["TOT_CONS"]=="" || $trow["TOT_CONS"]==0) $trow["TOT_CONS"]=$trow["CONS_DZN_GMTS"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['ITEM_NAME']=$trow["ITEM_NAME"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['DESCRIPTION']=$trow["DESCRIPTION"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['CONS_UOM']=$trow["CONS_UOM"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['NET_CONS']=$trow["TOT_CONS"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['EX_PER']=$trow["EX_PER"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['GCONS']=$trow["CONS_DZN_GMTS"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['RATE']=$trow["RATE"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['AMOUNT']=$trow["AMOUNT"];
	}
	unset($data_array_trim);
	if($zero_value==1) { 
	?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Trims Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold;background-color:#FFC" align="center">
                    <td width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item Name</td>
                    <td width="230" style="font-size:14px; font-family:'Calibri Light';">Item Description</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
			<?
            
			$i=1; $trimamtTotDzn=$trimReqAmttotal=$trimPerTotal=0;
            foreach($trimsDataArr as $trimtype=>$trimtypedata)
            {
				?>
                <tr>
                    <td colspan="11" style="font-size:14px; font-family:'Calibri Light';word-break:break-all; background-color:#FFC"><b><?=$trim_type[$trimtype]; ?> Trims Details</b></td>
                </tr>
                <?
				$typeTrimsAmtDzn=$typeTrimsAmt=$typeTrimsAmtPer=0;
				foreach($trimtypedata as $trimid=>$trimdata)
				{
					$rowTrimReqQty=$rowTrimReqAmt=$trim_per=0;
					$rowTrimReqQty=$trim_qty_arr[$trimid];
					$rowTrimReqAmt=$trim_amount_arr[$trimid];
					
					$trim_per=($rowTrimReqAmt/$order_values)*100;
					?>
					<tr>
						<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['ITEM_NAME']; ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['DESCRIPTION']; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$trimdata['CONS_UOM']]; ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["NET_CONS"],3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["EX_PER"],2); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["GCONS"],3); ?></td>
						
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqQty,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["RATE"],3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["AMOUNT"],3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqAmt,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trim_per,2); ?></td>
					</tr>
					<?
						$typeTrimsAmtDzn+=$trimdata["AMOUNT"];
						$typeTrimsAmt+=$rowTrimReqAmt;
						$typeTrimsAmtPer+=$trim_per;
						
						$trimamtTotDzn+=$trimdata["AMOUNT"];
						$trimReqAmttotal+=$rowTrimReqAmt;
						$trimPerTotal+=$trim_per;
				}
				 ?>
                <tr style="background-color:#CCCCCC">
                    <td colspan="8" style="font-size:14px; font-family:'Calibri Light';word-break:break-all;" align="right"><b>Total <?=$trim_type[$trimtype]; ?> Trims Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtPer,2); ?></td>
                </tr>
                <?
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Grand Total Trims Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else {
		if(($trims_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Trims Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold;background-color:#FFC" align="center">
                            <th width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item Name</th>
                            <th width="230" style="font-size:14px; font-family:'Calibri Light';">Item Description</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">YY</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    
                    $i=1; $trimamtTotDzn=$trimReqAmttotal=$trimPerTotal=0;
                    foreach($trimsDataArr as $trimtype=>$trimtypedata)
                    {
                        ?>
                        <tr>
                            <td colspan="11" style="font-size:14px; font-family:'Calibri Light';word-break:break-all; background-color:#FFC"><b><?=$trim_type[$trimtype]; ?> Trims Details</b></td>
                        </tr>
                        <?
                        $typeTrimsAmtDzn=$typeTrimsAmt=$typeTrimsAmtPer=0;
                        foreach($trimtypedata as $trimid=>$trimdata)
                        {
                            $rowTrimReqQty=$rowTrimReqAmt=$trim_per=0;
                            $rowTrimReqQty=$trim_qty_arr[$trimid];
                            $rowTrimReqAmt=$trim_amount_arr[$trimid];
                            
                            $trim_per=($rowTrimReqAmt/$order_values)*100;
                            ?>
                            <tr>
                                <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['ITEM_NAME']; ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['DESCRIPTION']; ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$trimdata['CONS_UOM']]; ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["NET_CONS"],3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["EX_PER"],2); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["GCONS"],3); ?></td>
                                
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqQty,3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["RATE"],3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["AMOUNT"],3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqAmt,3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trim_per,2); ?></td>
                            </tr>
                            <?
                                $typeTrimsAmtDzn+=$trimdata["AMOUNT"];
                                $typeTrimsAmt+=$rowTrimReqAmt;
                                $typeTrimsAmtPer+=$trim_per;
                                
                                $trimamtTotDzn+=$trimdata["AMOUNT"];
                                $trimReqAmttotal+=$rowTrimReqAmt;
                                $trimPerTotal+=$trim_per;
                        }
                         ?>
                        <tr style="background-color:#CCCCCC">
                            <td colspan="8" style="font-size:14px; font-family:'Calibri Light';word-break:break-all;" align="right"><b>Total <?=$trim_type[$trimtype]; ?> Trims Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtPer,2); ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Grand Total Trims Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
			<?
		}
	}
	?>
    
    <!--Wash =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Wash Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item</td>
                    <td width="230" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Wash Type</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
            
			<?
            $sql = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name =3 and is_deleted=0 and status_active=1";
			$data_arrayWash=sql_select($sql);
			$wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
			$wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
            $washQtyTot=$washamtTotDzn=$washReqAmttotal=$washPerTotal=0;
            foreach($data_arrayWash as $wrow)
            {
                $rowWashReqQty=$rowWashReqAmt=$wash_per=0;
                $rowWashReqQty=$wash_qty[$wrow["JOB_NO"]][$wrow["ID"]];
                $rowWashReqAmt=$wash_amount[$wrow["JOB_NO"]][$wrow["ID"]];
                
                $wash_per=($rowWashReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Wash</td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_wash_type[$wrow['EMB_TYPE']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["CONS_DZN_GMTS"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqQty,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["RATE"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["AMOUNT"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wash_per,2); ?></td>
                </tr>
                <?	
				$washQtyTot+=$rowWashReqQty;
				$washamtTotDzn+=$wrow["AMOUNT"];
				$washReqAmttotal+=$rowWashReqAmt;
				$washPerTotal+=$wash_per;
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Wash Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washQtyTot,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else {
		if(($emblishment_costing_arr_wash[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Wash Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <th width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item</th>
                            <th width="230" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Wash Type</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">YY</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</th>
                            
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $sql = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name =3 and is_deleted=0 and status_active=1";
                    $data_arrayWash=sql_select($sql);
                    $wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
                    $wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
                    $washQtyTot=$washamtTotDzn=$washReqAmttotal=$washPerTotal=0;
                    foreach($data_arrayWash as $wrow)
                    {
                        $rowWashReqQty=$rowWashReqAmt=$wash_per=0;
                        $rowWashReqQty=$wash_qty[$wrow["JOB_NO"]][$wrow["ID"]];
                        $rowWashReqAmt=$wash_amount[$wrow["JOB_NO"]][$wrow["ID"]];
                        
                        $wash_per=($rowWashReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Wash</td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_wash_type[$wrow['EMB_TYPE']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["CONS_DZN_GMTS"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqQty,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["RATE"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["AMOUNT"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wash_per,2); ?></td>
                        </tr>
                        <?	
                        $washQtyTot+=$rowWashReqQty;
                        $washamtTotDzn+=$wrow["AMOUNT"];
        
                        $washReqAmttotal+=$rowWashReqAmt;
                        $washPerTotal+=$wash_per;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Wash Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washQtyTot,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Embellishment =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Embellishment Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Embellishment Name</td>
                    <td width="230" style="font-size:14px; font-family:'Calibri Light';">Embl. Type</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
          	
            
			<?
            $sql_emb = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name !=3 and is_deleted=0 and status_active=1";
			$data_arrayEmb=sql_select($sql_emb);
			$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
			$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
            $embQtyTot=$embamtTotDzn=$embReqAmttotal=$embPerTotal=0;
            foreach($data_arrayEmb as $erow)
            {
				//$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$emblishment_other_type_arr);
				
				if($erow['EMB_NAME']==1) $type_array=$emblishment_print_type;
				else if($erow['EMB_NAME']==2) $type_array=$emblishment_embroy_type;
				else if($erow['EMB_NAME']==3) $type_array=$emblishment_wash_type;
				else if($erow['EMB_NAME']==4) $type_array=$emblishment_spwork_type;
				else if($erow['EMB_NAME']==5) $type_array=$emblishment_gmts_type;
				else if($erow['EMB_NAME']==99) $type_array=$emblishment_other_type_arr;
				else $type_array=$blank_array;
				
                $rowEmbReqQty=$rowEmbReqAmt=$emb_per=0;
                $rowEmbReqQty=$emblishment_qty[$erow["JOB_NO"]][$erow["ID"]];
                $rowEmbReqAmt=$emblishment_amount[$erow["JOB_NO"]][$erow["ID"]];
                
                $emb_per=($rowEmbReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_name_array[$erow['EMB_NAME']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$type_array[$erow['EMB_TYPE']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["CONS_DZN_GMTS"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqQty,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["RATE"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["AMOUNT"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($emb_per,2); ?></td>
                </tr>
                <?	
				$embQtyTot+=$rowWashReqQty;
				$embamtTotDzn+=$erow["AMOUNT"];
				$embReqAmttotal+=$rowEmbReqAmt;
				$embPerTotal+=$emb_per;
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Embellishment Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embQtyTot,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else {
		if(($emblishment_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Embellishment Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <th width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Embellishment Name</th>
                            <th width="230" style="font-size:14px; font-family:'Calibri Light';">Embl. Type</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">YY</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</th>
                            
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $sql_emb = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name !=3 and is_deleted=0 and status_active=1";
                    $data_arrayEmb=sql_select($sql_emb);
                    $emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
                    $emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
                    $embQtyTot=$embamtTotDzn=$embReqAmttotal=$embPerTotal=0;
                    foreach($data_arrayEmb as $erow)
                    {
                        //$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$emblishment_other_type_arr);
                        
                        if($erow['EMB_NAME']==1) $type_array=$emblishment_print_type;
                        else if($erow['EMB_NAME']==2) $type_array=$emblishment_embroy_type;
                        else if($erow['EMB_NAME']==3) $type_array=$emblishment_wash_type;
                        else if($erow['EMB_NAME']==4) $type_array=$emblishment_spwork_type;
                        else if($erow['EMB_NAME']==5) $type_array=$emblishment_gmts_type;
                        else if($erow['EMB_NAME']==99) $type_array=$emblishment_other_type_arr;
                        else $type_array=$blank_array;
                        
                        $rowEmbReqQty=$rowEmbReqAmt=$emb_per=0;
                        $rowEmbReqQty=$emblishment_qty[$erow["JOB_NO"]][$erow["ID"]];
                        $rowEmbReqAmt=$emblishment_amount[$erow["JOB_NO"]][$erow["ID"]];
                        
                        $emb_per=($rowEmbReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_name_array[$erow['EMB_NAME']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$type_array[$erow['EMB_TYPE']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["CONS_DZN_GMTS"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqQty,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["RATE"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["AMOUNT"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($emb_per,2); ?></td>
                        </tr>
                        <?	
                        $embQtyTot+=$rowWashReqQty;
                        $embamtTotDzn+=$erow["AMOUNT"];
                        $embReqAmttotal+=$rowEmbReqAmt;
                        $embPerTotal+=$emb_per;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Embellishment Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embQtyTot,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Commercial =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commercial Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Commercial %</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
          	
            
			<?
            $sql_comml = "select ID, JOB_NO, ITEM_ID, RATE, AMOUNT from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
			$data_arrayComer=sql_select($sql_comml);
			$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
            foreach($data_arrayComer as $cmlrow)
            {
                $rowComlReqAmt=$coml_per=$commlAmt=0;
                $rowComlReqAmt=$commarcial_amount[$cmlrow["JOB_NO"]][$cmlrow["ID"]];
                $commlAmt=$rowComlReqAmt/$order_job_qnty;
                $coml_per=($rowComlReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$camarcial_items[$cmlrow['ITEM_ID']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmlrow["RATE"],3); ?></td>
                    
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commlAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowComlReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($coml_per,2); ?></td>
                </tr>
                <?	
            }
            ?>
            </tbody>
        </table>
    </div>
    <? } else {
		if(($commercial_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commercial Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <th width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Commercial %</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $sql_comml = "select ID, JOB_NO, ITEM_ID, RATE, AMOUNT from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
                    $data_arrayComer=sql_select($sql_comml);
                    $commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
                    foreach($data_arrayComer as $cmlrow)
                    {
                        $rowComlReqAmt=$coml_per=$commlAmt=0;
                        $rowComlReqAmt=$commarcial_amount[$cmlrow["JOB_NO"]][$cmlrow["ID"]];
                        $commlAmt=$rowComlReqAmt/$order_job_qnty;
                        $coml_per=($rowComlReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$camarcial_items[$cmlrow['ITEM_ID']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmlrow["RATE"],3); ?></td>
                            
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commlAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowComlReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($coml_per,2); ?></td>
                        </tr>
                        <?	
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Commission =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commission Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Commission %</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
          
            
			<?
			$sql_comm = "select ID, JOB_NO, PARTICULARS_ID, COMMISSION_BASE_ID, COMMISION_RATE, COMMISSION_AMOUNT from wo_pre_cost_commiss_cost_dtls where job_no=".$txt_job_no." and status_active=1";
			$data_arrayComi=sql_select($sql_comm);
			$commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
            foreach($data_arrayComi as $cmmrow)
            {
                $rowCommReqAmt=$comm_per=$commAmt=0;
                $rowCommReqAmt=$commision_amount[$cmmrow["JOB_NO"]][$cmmrow["ID"]];
                $commAmt=$rowCommReqAmt/$order_job_qnty;
                $comm_per=($rowCommReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$commission_particulars[$cmmrow['PARTICULARS_ID']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmmrow["COMMISION_RATE"],4); ?></td>
                    
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowCommReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($comm_per,2); ?></td>
                </tr>
                <?	
            }
            ?>
            </tbody>
        </table>
    </div>
    <? } else { 
		if(($commission_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
            <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commission Cost Details</b></label>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
                <thead>
                    <tr style="font-weight:bold; background-color:#FFC" align="center">
                        <th width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</th>
                        <th width="50" style="font-size:14px; font-family:'Calibri Light';">Commission %</th>
                        <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</th>
                        <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                        <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_comm = "select ID, JOB_NO, PARTICULARS_ID, COMMISSION_BASE_ID, COMMISION_RATE, COMMISSION_AMOUNT from wo_pre_cost_commiss_cost_dtls where job_no=".$txt_job_no." and status_active=1";
                $data_arrayComi=sql_select($sql_comm);
                $commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
                foreach($data_arrayComi as $cmmrow)
                {
                    $rowCommReqAmt=$comm_per=$commAmt=0;
                    $rowCommReqAmt=$commision_amount[$cmmrow["JOB_NO"]][$cmmrow["ID"]];
                    $commAmt=$rowCommReqAmt/$order_job_qnty;
                    $comm_per=($rowCommReqAmt/$order_values)*100;
                    ?>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$commission_particulars[$cmmrow['PARTICULARS_ID']]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmmrow["COMMISION_RATE"],4); ?></td>
                        
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commAmt,3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowCommReqAmt,3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($comm_per,2); ?></td>
                    </tr>
                    <?	
                }
                ?>
                </tbody>
            </table>
        </div>
            <?
		}
	}
	
	$otherCostHeadArr=array("currier_pre_cost"=>"Courier Cost","inspection"=>"Inspection Cost","common_oh"=>"Opt Exp","deffdlc_cost"=>"Deffd. LC/DC");
	
	$otherCostHeadPerArr=array("currier_pre_cost"=>"currier_percent","inspection"=>"inspection_percent","common_oh"=>"common_oh_percent","deffdlc_cost"=>"deffdlc_percent");
	
	
	 
	foreach($otherCostHeadArr as $indexcost=>$indexhead)
	{
		if($zero_value==1) { ?>
		<div style="margin-top:15px">
			<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b><?=$indexhead; ?> Details</b></label>
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
				<tbody>
					<tr style="font-weight:bold; background-color:#FFC" align="center">
						<td width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</td>
						<td width="50" style="font-size:14px; font-family:'Calibri Light';">%</td>
						<td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</td>
						<td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
						<td style="font-size:14px; font-family:'Calibri Light';">%</td>
					</tr>
				
				
				<?
					$rowcostReqAmt=$rowcost_per=$rowAmt=0;
					$rowcostReqAmt=$other_costing_arr[$job_no][$indexcost];
					$rowAmt=$rowcostReqAmt/$order_job_qnty;
					$rowcost_per=($rowcostReqAmt/$order_values)*100;
					?>
					<tr>
						<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$indexhead; ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($summary_data[$otherCostHeadPerArr[$indexcost]],3); ?></td>
						
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowAmt,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcostReqAmt,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcost_per,2); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<? } else {
			if(($other_costing_arr[$job_no][$indexcost]*1)>0)
			{
				?>
				<div style="margin-top:15px">
					<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b><?=$indexhead; ?> Cost Details</b></label>
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
						<thead>
							<tr style="font-weight:bold; background-color:#FFC" align="center">
								<th width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</th>
								<th width="50" style="font-size:14px; font-family:'Calibri Light';">%</th>
								<th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</th>
								<th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
								<th style="font-size:14px; font-family:'Calibri Light';">%</th>
							</tr>
						</thead>
						<tbody>
						<?
							$rowcostReqAmt=$rowcost_per=$rowAmt=0;
							$rowcostReqAmt=$other_costing_arr[$job_no][$indexcost];
							$rowAmt=$rowcostReqAmt/$order_job_qnty;
							$rowcost_per=($rowcostReqAmt/$order_values)*100;
							?>
							<tr>
								<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$indexhead; ?></td>
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($summary_data[$otherCostHeadPerArr[$indexcost]],3); ?></td>
								
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowAmt,3); ?></td>
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcostReqAmt,3); ?></td>
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcost_per,2); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<?
			}
		}
	}
	?>
    
    <br/>
    <?
    $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_pre_cost_mst","job_no=$txt_job_no","mst_id");
	$unapprove_sql_data_array=sql_select("select B.APPROVED_BY, B.UN_APPROVED_BY, B.APPROVED_DATE, B.UN_APPROVED_REASON, B.UN_APPROVED_DATE, B.APPROVED_NO, b.APPROVED, b.UN_APPROVED_REASON from approval_history b where b.mst_id=$mst_id and b.entry_form=77 order by b.id");


    foreach($unapprove_sql_data_array as $row){
        $unapprove_data_array[$row['APPROVED_BY']]=$row;
    }

	if(count($unapprove_data_array)>0)
	{
		$app_status_arr = array(0=>'Un App',1=>'Full App',2=>'Deny',3=>'Partial App');
        ?>
		<table width="930" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                	<th colspan="7" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Approval/Un Approval History</th>
                </tr>
                <tr>
                    <th width="30" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">SL</th>
                    <th width="150" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Name</th>
                    <th width="120" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Designation</th>
                    <th width="60" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Approval Status</th>
                    <th width="60" style="font-size:14px; font-family:'Calibri Light';">Approval No</th>
                    <th width="120" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">App. Date & Time</th>
                    <th style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Reason For Un-Approval</th>
                </tr>
            </thead>
            <tbody>
				<?
                $i=1;
                foreach($unapprove_data_array as $row)
				{
					?>
					<tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$i; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"><?=$lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?= $app_status_arr[$row['APPROVED']]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$row[csf('approved_no')];?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo ""; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"> &nbsp; <?= $row['UN_APPROVED_REASON']; ?></td>
					</tr>
					<?
					$i++;
					//$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
					//$un_approved_date=$un_approved_date[0];
					//if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;

					if($un_approved_date!="")
					{
						if($row[csf('un_approved_by')]!==0)  $row[csf('approved_by')]=$row[csf('un_approved_by')];
						?>
						<tr>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$i;?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$user_lib_name_arr[$row[csf('approved_by')]];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';"><?=$lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?='No';?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$row[csf('approved_no')];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unapproved_request_arr[$mst_id];?></td>
						</tr>
						<?
						$i++;
					}


                }
                ?>
            </tbody>
		</table>
		<?
	}
	?>
    <br>
    <table width="930" align="center">
            <tr>
            <div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
				<?
                if(count($approval_arr)>0)
                {
                    if($approved == 2 || $approved == 0){echo "Draft";}else{}
                }
                ?>
            </div>
            </tr>
    </table>
 	<?
	echo signature_table(109, $cbo_company_name, "930px",'','1','','');
	exit();
}


