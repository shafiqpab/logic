<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board for Management Dashboard For Production.
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	14-9-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
extract($_REQUEST);
if ($print == 1) {
	$printLink = "../../";
	include("../../includes/common.php");
	echo load_html_head_contents("Graph", "../../", "", '', '', '', 1);
	$width = "92";
} else {
	$width = "92";
}


//--------------------------------------------------------------------------------------------------------------------

?>
<script src="<? echo $printLink; ?>chart/highcharts_v2.js"></script>

<?

if ($_SESSION['logic_erp']["month__arr___"] == "")  {
	list($lcCompany, $location, $floor, $workingCompany) = explode('__', $_REQUEST['cp']);
	if ($workingCompany) {
		$company_cond = " and a.company_name=$workingCompany";
		$company_cond2 = " and a.company_id=$workingCompany";
	} else if ($lcCompany) {
		$company_cond = " and a.company_name=$lcCompany";
		$company_cond2 = " and a.company_id=$lcCompany";
	} else {
		$company_cond = "";
		$company_cond2 = "";
	}
	if ($_SESSION["logic_erp"]["company_id"] != '') {
		$company_cond .= " and a.company_name in(" . $_SESSION["logic_erp"]["company_id"] . ")";
		$company_cond2 .= " and a.company_id in(" . $_SESSION["logic_erp"]["company_id"] . ")";
	}

	$month_arr = array();
	$month_prev = date('Y-m-d', strtotime('-1 month'));
	$month_next = date("Y-m-d", time());
	$month_start = add_month(date("Y-m-d", time()), -11);
	$month_end = add_month(date("Y-m-d", time()), 0);
	if ($db_type == 0) {
		$startDate = date("Y-m-d", strtotime($month_start));
		$endDate = date("Y-m-t", strtotime($month_end));
		$monthStartDate = date("Y-m-d", strtotime($month_prev));
		$monthEndDate = date("Y-m-d", strtotime($month_next));
	} else {
		$startDate = date("d-M-Y", strtotime($month_start));
		$endDate = date("t-M-Y", strtotime($month_end));
		$monthStartDate = date("d-M-Y", strtotime($month_prev));
		$monthEndDate = date("d-M-Y", strtotime($month_next));
	}

	$production_sql = "SELECT a.production_date,a.COMPANY_ID,SERVING_COMPANY,
		sum(case when a.production_type=1 then b.production_qnty else 0 end) as CUTTING_PRODUCTION_QNTY,
		sum(case when a.production_type=5 then b.production_qnty else 0 end) as SEWING_PRODUCTION_QNTY,
		sum(case when a.production_type=8 then b.production_qnty else 0 end) as FINISHING_PRODUCTION_QNTY
		from pro_garments_production_mst a, pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type in(1,5,8) and a.status_active=1 and b.status_active=1 $company_cond2 and a.production_date between '$startDate' and '$endDate'
		group by a.production_date,a.company_id,SERVING_COMPANY";
	 //echo $production_sql;die();
	$production_result = sql_select($production_sql);

	foreach ($production_result as $row) {
		$monthKey = date("M y", strtotime($row['PRODUCTION_DATE']));
		$yearlyCuttingProduction[$monthKey] += $row['CUTTING_PRODUCTION_QNTY'];
		$yearlySewingProduction[$monthKey] += $row['SEWING_PRODUCTION_QNTY'];
		$yearlyFinishingProduction[$monthKey] += $row['FINISHING_PRODUCTION_QNTY'];
		if (strtotime($row['PRODUCTION_DATE']) <= strtotime($month_next) && strtotime($month_prev) <= strtotime($row['PRODUCTION_DATE'])) {
			$dayKey = date("d-M-Y", strtotime($row['PRODUCTION_DATE']));
			$dailyCuttingProduction[$dayKey] += $row['CUTTING_PRODUCTION_QNTY'];
			$dailySewingProduction[$dayKey] += $row['SEWING_PRODUCTION_QNTY'];

			$companyDailyCuttingProduction[$row['SERVING_COMPANY']][$dayKey] += $row['CUTTING_PRODUCTION_QNTY'];
			$companyDailySewingProduction[$row['SERVING_COMPANY']][$dayKey] += $row['SEWING_PRODUCTION_QNTY'];
			$companyDailyFinishingProduction[$row['SERVING_COMPANY']][$dayKey] += $row['FINISHING_PRODUCTION_QNTY'];
		}


	}
	


	unset($production_result);
		$delivery_sql=" SELECT b.EX_FACTORY_DATE, sum(CASE WHEN b.entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY, sum(CASE WHEN b.entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY  from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.delivery_company_id<>0  and a.status_active=1 and b.status_active=1 and b.EX_FACTORY_DATE between '$startDate' and '$endDate' $company_cond2  group by b.EX_FACTORY_DATE
		UNION ALL
		SELECT a.EX_FACTORY_DATE,sum(c.size_pass_qty) as EX_FACTORY_QNTY,0 EX_FACTORY_RETURN_QNTY from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and a.status_active=1 and b.status_active=1 and c.status_active=1   and a.EX_FACTORY_DATE between '$startDate' and '$endDate' $company_cond2 group by a.EX_FACTORY_DATE ";

		 //echo $delivery_sql;die;

	$delivery_result = sql_select($delivery_sql);

	foreach ($delivery_result as $row) 
	{
		$monthKey = date("M y", strtotime($row['EX_FACTORY_DATE']));
		$yearlyExportProduction[$monthKey] += ($row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY']);
	}

	//print_r($delivery_id_check);die; SERVICE_SOURCE


	unset($delivery_result);

	$dyeing_sql = "SELECT a.production_date, a.service_source, sum(b.production_qty) as PRODUCTION_QTY
		from pro_fab_subprocess a, pro_fab_subprocess_dtls b 
		where a.id=b.mst_id and a.entry_form=35 and b.entry_page=35 and a.load_unload_id=2 and a.status_active=1 and b.status_active=1 and a.production_date between '$startDate' and '$endDate' $company_cond2
		group by a.production_date, a.service_source";
	$dyeing_result = sql_select($dyeing_sql);

	foreach ($dyeing_result as $row) {
		$monthKey = date("M y", strtotime($row['PRODUCTION_DATE']));
		if($row['SERVICE_SOURCE']==1){
			$yearlyDyeingProduction[$monthKey] += $row['PRODUCTION_QTY'];
		}
		elseif($row['SERVICE_SOURCE']==3){
			$yearlyDyeingProduction2[$monthKey] += $row['PRODUCTION_QTY'];
		}
		
		if (strtotime($row['PRODUCTION_DATE']) <= strtotime($month_next) && strtotime($month_prev) <= strtotime($row['PRODUCTION_DATE'])) {
			$dayKey = date("d-M-Y", strtotime($row['PRODUCTION_DATE']));

			if($row['SERVICE_SOURCE']==1){
				$dailyDyeingProduction[$dayKey] += $row['PRODUCTION_QTY'];
			}
			elseif($row['SERVICE_SOURCE']==3){
				$dailyDyeingProduction2[$dayKey] += $row['PRODUCTION_QTY'];
			}
		}
	}
	unset($dyeing_result);
	
    // Knitting Inhouse and outbound
	$knitting_inhouse_sql = "SELECT a.receive_date, a.knitting_source, sum(b.grey_receive_qnty) as PRODUCTION_QTY
		from inv_receive_master a, pro_grey_prod_entry_dtls b 
		where a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and b.status_active=1 and a.receive_date between '$startDate' and '$endDate' $company_cond2
		group by a.receive_date, a.knitting_source";
	  //echo $knitting_inhouse_sql;exit;
	$knitting_inhouse_result = sql_select($knitting_inhouse_sql);

	foreach ($knitting_inhouse_result as $row) { 
		$monthKey = date("M y", strtotime($row['RECEIVE_DATE']));
		// Inhouse Production
		if($row['KNITTING_SOURCE']==1){
			$yearlyKnittingInhouseProduction[$monthKey] += $row['PRODUCTION_QTY'];
		}
		// Outbound Production
		elseif($row['KNITTING_SOURCE']==3){
			$yearlyKnittingOutboundProduction[$monthKey] += $row['PRODUCTION_QTY'];
		}
		
		if (strtotime($row['RECEIVE_DATE']) <= strtotime($month_next) && strtotime($month_prev) <= strtotime($row['RECEIVE_DATE'])) {
			$dayKey = date("d-M-Y", strtotime($row['RECEIVE_DATE']));
			

			// Inhouse Production
			if($row['KNITTING_SOURCE']==1){
				$dailyKnittingProduction[$dayKey] += $row['PRODUCTION_QTY'];
			}
			// Outbound Production
			elseif($row['KNITTING_SOURCE']==3){
				$dailyKnittingProduction2[$dayKey] += $row['PRODUCTION_QTY'];
			}

			
		}
	}
	unset($knitting_inhouse_result);


	

	$packing_sql = "SELECT a.production_date, sum(b.production_qnty) as PRODUCTION_QTY
					from pro_garments_production_mst a, pro_garments_production_dtls b 
					where a.id=b.mst_id and a.production_type='8' and b.production_type=8 and a.status_active=1 and b.status_active=1 and a.production_date between '$monthStartDate' and '$monthEndDate' $company_cond2
					group by a.production_date";
	$packing_result = sql_select($packing_sql);

	foreach ($packing_result as $row) {
		$dayKey = date("d-M-Y", strtotime($row['PRODUCTION_DATE']));
		$dailyPackingProduction[$dayKey] += $row['PRODUCTION_QTY'];
	}
	unset($packing_result);
	$comp_arr=return_library_array("select id,COMPANY_SHORT_NAME from lib_company", "id","COMPANY_SHORT_NAME");
	$buyer_order_sql = "SELECT a.buyer_name as buyer_id, sum(b.po_quantity) as PO_QUANTITY,c.buyer_name
		from wo_po_details_master a, wo_po_break_down b, lib_buyer c 
		where a.id=b.job_id and a.buyer_name=c.id and a.status_active=1 and b.status_active=1 and a.buyer_name>0 and b.pub_shipment_date between '$startDate' and '$endDate' $company_cond
		group by a.buyer_name,c.buyer_name";
	$buyer_order_result = sql_select($buyer_order_sql);

	$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
	foreach ($buyer_order_result as $row) {
		$buyerWiseOrder[] = "'" . $row['BUYER_NAME'] . "'," . $row['PO_QUANTITY'];
		// $buyerColor[] = "#" . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)];
	}
	unset($buyer_order_result);

	$team_leader_order_sql = "SELECT a.team_leader, sum(b.po_quantity) as PO_QUANTITY, c.team_leader_name
		from wo_po_details_master a, wo_po_break_down b, lib_marketing_team c 
		where a.id=b.job_id and a.team_leader=c.id and a.status_active=1 and b.status_active=1 and a.team_leader>0 and b.pub_shipment_date between '$startDate' and '$endDate' $company_cond
		group by a.team_leader,c.team_leader_name";
	$team_leader_order_result = sql_select($team_leader_order_sql);

	foreach ($team_leader_order_result as $row) {
		$teamLeaderWiseOrder[] = "'" . $row['TEAM_LEADER_NAME'] . "'," . $row['PO_QUANTITY'];
	}
	unset($team_leader_order_result);

	$buyer_sample_order_sql = "SELECT a.buyer_name as BUYER_ID, sum(b.sample_prod_qty) as PROD_QTY, c.buyer_name
		from sample_development_mst a, sample_development_dtls b, lib_buyer c 
		where a.id=b.sample_mst_id and a.buyer_name=c.id and a.entry_form_id=203 and b.entry_form_id=203 and a.status_active=1 and b.status_active=1 and a.buyer_name>0 and a.requisition_date between '$startDate' and '$endDate' $company_cond2
		group by a.buyer_name,c.buyer_name";
	$buyer_sample_order_result = sql_select($buyer_sample_order_sql);

	foreach ($buyer_sample_order_result as $row) {
		$buyerSampleWiseOrder[] =  "'" . $row['BUYER_NAME'] . "'," . $row['PROD_QTY'];
	}
	unset($buyer_sample_order_result);

	//----------------------------------------------------------------------------------------
	$remain_months = datediff("m", $month_start, date("Y-m-d", strtotime($month_end)));
	for ($e = 0; $e <= $remain_months; $e++) {
		$tmp = add_month(date("Y-m-d", strtotime($month_start)), $e);
		$month_arr[$e] = date("M y", strtotime($tmp));
	}
	//----------------------------------------------------------------------------------------
	$remain_days = datediff("d", $month_prev, $month_next);
	$cd = strtotime($month_prev);
	for ($e = 0; $e < $remain_days; $e++) {
		$day_arr[$e] = date('d-M-Y', mktime(0, 0, 0, date('m', $cd), date('d', $cd) + $e, date('Y', $cd)));
		$day_arr1[$e] = date('d-M', mktime(0, 0, 0, date('m', $cd), date('d', $cd) + $e, date('Y', $cd)));
	}
	//----------------------------------------------------------------------------------------
	foreach ($month_arr as $key => $monthYear) {
		$yearCutProd[] = number_format($yearlyCuttingProduction[$monthYear], 0, '.', '');
		$yearSewProd[] = number_format($yearlySewingProduction[$monthYear], 0, '.', '');
		$yearFinshProd[] = number_format($yearlyFinishingProduction[$monthYear], 0, '.', '');
		$yearExportProd[] = number_format($yearlyExportProduction[$monthYear], 0, '.', '');
		$yearDyeProd[] = number_format($yearlyDyeingProduction[$monthYear], 0, '.', '');
		$yearDyeProd2[] = number_format($yearlyDyeingProduction2[$monthYear], 0, '.', '');
		$yearKnitInhouseProd[] = number_format($yearlyKnittingInhouseProduction[$monthYear], 0, '.', '');
		$yearKnitOutboundProd[] = number_format($yearlyKnittingOutboundProduction[$monthYear], 0, '.', '');
	}

	

	foreach ($day_arr as $key => $dayMonth) {
		$dailyDyeProd[] = number_format($dailyDyeingProduction[$dayMonth], 0, '.', '');
		$dailyDyeProd2[] = number_format($dailyDyeingProduction2[$dayMonth], 0, '.', '');
		$dailyKnitProd[] = number_format($dailyKnittingProduction[$dayMonth], 0, '.', '');
		$dailyKnitProd2[] = number_format($dailyKnittingProduction2[$dayMonth], 0, '.', '');
		$dailyCutProd[] = number_format($dailyCuttingProduction[$dayMonth], 0, '.', '');
		$dailySewProd[] = number_format($dailySewingProduction[$dayMonth], 0, '.', '');
		$dailyPackingProd[] = number_format($dailyPackingProduction[$dayMonth], 0, '.', '');
	}


	##################### Grape ##################################
	$_SESSION['logic_erp']["month_arr"] = $month_arr;
	$_SESSION['logic_erp']["yearCutProd"] = $yearCutProd;
	$_SESSION['logic_erp']["yearSewProd"] = $yearSewProd;
	$_SESSION['logic_erp']["yearFinshProd"] = $yearFinshProd;
	$_SESSION['logic_erp']["yearExportProd"] = $yearExportProd;
	$_SESSION['logic_erp']["yearDyeProd"] = $yearDyeProd;
	$_SESSION['logic_erp']["yearKnitInhouseProd"] = $yearKnitInhouseProd;
	$_SESSION['logic_erp']["yearlyDyeingProduction"] = $yearlyDyeingProduction;
	$_SESSION['logic_erp']["yearlyKnittingProduction"] = $yearlyKnittingInhouseProduction;
	$_SESSION['logic_erp']["day_arr"] = $day_arr;
	$_SESSION['logic_erp']["dailyDyeProd"] = $dailyDyeProd;
	$_SESSION['logic_erp']["dailyKnitProd"] = $dailyKnitProd;
	$_SESSION['logic_erp']["dailyCutProd"] = $dailyCutProd;
	$_SESSION['logic_erp']["dailySewProd"] = $dailySewProd;
	$_SESSION['logic_erp']["dailyPackingProd"] = $dailyPackingProd;
	$_SESSION['logic_erp']["buyerWiseOrder"] = $buyerWiseOrder;
	$_SESSION['logic_erp']["teamLeaderWiseOrder"] = $teamLeaderWiseOrder;
	$_SESSION['logic_erp']["buyerSampleWiseOrder"] = $buyerSampleWiseOrder;
	$_SESSION['logic_erp']["startToEndYear"] = date("d M, Y", strtotime($startDate)) . " - " . date("d M, Y", strtotime($endDate));
	$_SESSION['logic_erp']["startToEndMonth"] = date("d M, Y", strtotime($month_prev)) . " - " . date("d M, Y", strtotime($month_next));
} else {
	##################### Grape ##################################
	$month_arr = $_SESSION['logic_erp']["month_arr"];
	$yearCutProd = $_SESSION['logic_erp']["yearCutProd"];
	$yearSewProd = $_SESSION['logic_erp']["yearSewProd"];
	$yearFinshProd = $_SESSION['logic_erp']["yearFinshProd"];
	$yearExportProd = $_SESSION['logic_erp']["yearExportProd"];
	$yearDyeProd = $_SESSION['logic_erp']["yearDyeProd"];
	$yearKnitInhouseProd = $_SESSION['logic_erp']["yearKnitInhouseProd"];
	$yearKnitOutboundProd = $_SESSION['logic_erp']["yearKnitOutboundProd"];
	$yearlyDyeingProduction = $_SESSION['logic_erp']["yearlyDyeingProduction"];
	$yearlyKnittingProduction = $_SESSION['logic_erp']["yearlyKnittingProduction"];
	$day_arr = $_SESSION['logic_erp']["day_arr"];
	$dailyDyeProd = $_SESSION['logic_erp']["dailyDyeProd"];
	$dailyKnitProd = $_SESSION['logic_erp']["dailyKnitProd"];
	$dailyCutProd = $_SESSION['logic_erp']["dailyCutProd"];
	$dailySewProd = $_SESSION['logic_erp']["dailySewProd"];
	$dailyPackingProd = $_SESSION['logic_erp']["dailyPackingProd"];
	$buyerWiseOrder = $_SESSION['logic_erp']["buyerWiseOrder"];
	$teamLeaderWiseOrder = $_SESSION['logic_erp']["teamLeaderWiseOrder"];
	$buyerSampleWiseOrder = $_SESSION['logic_erp']["buyerSampleWiseOrder"];
	$startToEndYear = $_SESSION['logic_erp']["startToEndYear"];
	$startToEndMonth = $_SESSION['logic_erp']["startToEndMonth"];
}

$monthArray = json_encode($month_arr);
$dayArr = json_encode($day_arr);
$yearCutProd = implode(',', $yearCutProd);
$yearSewProd = implode(',', $yearSewProd);
$yearFinshProd = implode(',', $yearFinshProd);
$yearExportProd = implode(',', $yearExportProd);
$yearDyeProd = implode(',', $yearDyeProd);
$yearDyeProd2 = implode(',', $yearDyeProd2);
$yearKnitInhouseProd = implode(',', $yearKnitInhouseProd);
$yearKnitOutboundProd = implode(',', $yearKnitOutboundProd);
$dailyDyeProd = implode(',', $dailyDyeProd);
$dailyDyeProd2 = implode(',', $dailyDyeProd2);
$dailyKnitProd = implode(',', $dailyKnitProd);
$dailyKnitProd2 = implode(',', $dailyKnitProd2);
$dailyCutProd = implode(',', $dailyCutProd);
$dailySewProd = implode(',', $dailySewProd);
$dailyPackingProd = implode(',', $dailyPackingProd);
$buyerWiseOrder = implode('],[', $buyerWiseOrder);
$teamLeaderWiseOrder = implode('],[', $teamLeaderWiseOrder);
$buyerSampleWiseOrder = implode('],[', $buyerSampleWiseOrder);
 
?>
<div style="margin:10px;width:100%;">
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container"></div>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container2"></div>
		<table class="tblData canvas_info" border="1" rules="all">
			<tbody>
				<tr bgcolor="#D5D9D8">
					<td width="100"><b></b></td>
					<? foreach ($month_arr as $month) {
						echo "<td width='100' align='center'><b>". $month . "</b></td>";
					} ?>
				</tr>
				<tr>
					<td width="100"><b>Knitting Qty</b></td>
					<? foreach ($month_arr as $month) {
						echo "<td align='right'>";
						if (!empty($yearlyKnittingInhouseProduction[$month])) {
							echo number_format($yearlyKnittingInhouseProduction[$month]);
						}
						echo "&nbsp;</td>";
					} ?>
				</tr>
				<tr>
					<td width="100"><b>Dyeing Qty</b></td>
					<? foreach ($month_arr as $month) {
						echo "<td align='right'>";
						if (!empty($yearlyDyeingProduction[$month])) {
							echo number_format($yearlyDyeingProduction[$month]);
						}
						echo "&nbsp;</td>";
					} ?>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container3"></div>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container4"></div>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container5"></div>
		<table class="tblData canvas_info" border="1" rules="all" >
			<tbody>
				<tr bgcolor="#D5D9D8">
					<td width="100"><b>Company</b></td>
					<? foreach ($day_arr1 as $day) {
						echo "<td width='100' align='center'><b>" . $day . "</b></td>";
					} ?>
				</tr>
				     
					<?php
					
					foreach($companyDailyCuttingProduction as $companyId=>$rowArr){
					?>
					<tr>
					<td width="220"><b><?=$comp_arr[$companyId];?></b></td>
					
					<? 
					
					foreach ($day_arr as $day) {
						
						echo "<td width='100' align='center'><b>" . $rowArr[$day] . "</b></td>";
					}
					?>
					</tr>
					<?
					
					} ?>
					
				
				
			</tbody>
		</table>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
	   
		<div id="container6"></div>
		<table class="tblData canvas_info" border="1" rules="all">
			<tbody>
				<tr bgcolor="#D5D9D8">
					<td width="100"><b>Company</b></td>
					<? foreach ($day_arr1 as $day) {
						echo "<td width='100' align='center'><b>" . $day . "</b></td>";
					} ?>
				</tr>
				     
					<?php
					
					foreach($companyDailySewingProduction as $companyId=>$rowArr){
					?>
					<tr>
					<td width="220"><b><?=$comp_arr[$companyId];?></b></td>
					<? foreach ($day_arr as $day) {
						
						echo "<td width='100' align='center'><b>" . $rowArr[$day] . "</b></td>";
					}
					?>
					</tr>
					
					<?
					
					} ?>
				
				
			</tbody>
		</table>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container7"></div>
		<table class="tblData canvas_info" border="1" rules="all">
			<tbody>
				<tr bgcolor="#D5D9D8">
					<td width="100"><b>Company</b></td>
					<? foreach ($day_arr1 as $day) {
						echo "<td width='100' align='center'><b>" . $day . "</b></td>";
					} ?>
				</tr>
				     
					<?php
					foreach($companyDailyFinishingProduction as $companyId=>$rowArr){
					?>
					<tr>
					<td width="220"><b><?=$comp_arr[$companyId];?></b></td>
					<? foreach ($day_arr as $day) {
						
						echo "<td width='100' align='center'><b>" . $rowArr[$day] . "</b></td>";
					}
					?>
					</tr>
					 <?
					
					} ?>
				
				
			</tbody>
		</table>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container8"></div>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container9"></div>
	</div>
	<div style="width:1000px;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
		<div id="container10"></div>
	</div>
	<a href="home_graph/dashborad/management_dashboard_for_production.php?print=1" target="_blank"><img src="img/print.jpg" height="20" alt="" /></a>
</div>

<script>
	Highcharts.chart('container', {
		chart: {
			type: 'column',
		},
		title: {
			text: 'Yearly Production'
		},
		subtitle: {
			text: '<?= $startToEndYear; ?>'
		},
		xAxis: {
			categories: <?= $monthArray; ?>,
		},
		yAxis: {
			allowDecimals: true,
			min: 0,
			title: {
				text: 'Production Qty in PCS'
			}
		},
		tooltip: {
			crosshairs: true,
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} PCS</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
			/* formatter: function() {
				return '<b>' + this.x + '</b><br/>' + this.series.name + ': ' + Highcharts.numberFormat(this.y, 0, '.', ',') + '<br/>';
			} */
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			}
		},
		series: [{
				name: 'CUTTING QUANTITY',
				data: [<?= $yearCutProd; ?>],
				stack: '1',
				color: '#3399FF',
			}, {
				name: 'SEWING QUANTITY',
				data: [<?= $yearSewProd; ?>],
				stack: '2',
				color: '#FF3300'

			}, {
				name: 'FINISHING QUANTITY',
				data: [<?= $yearFinshProd; ?>],
				stack: '3',
				color: '#33CC33'
			},
			{
				name: 'EXPORT QUANTITY',
				data: [<?= $yearExportProd; ?>],
				stack: '4',
				color: '#8EAADB',
			}
		]

	});
    // work
	Highcharts.chart('container2', {
		chart: {
			type: 'column',
		},
		title: {
			text: 'Yearly Knitting and Dyeing Production Report'
		},
		subtitle: {
			text: '<?= $startToEndYear; ?>'
		},
		xAxis: {
			categories: <?= $monthArray; ?>,
		},
		yAxis: {
			allowDecimals: true,
			min: 0,
			title: {
				text: 'Production Qty in KG'
			}
		},
		tooltip: {
			crosshairs: true,
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} KG</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			}
		},
		series: [
			{
				name: 'Knitting Inhouse Qty',
				data: [<?= $yearKnitInhouseProd; ?>],
				stack: '1',
				color: '#8B4513',
			}, {
				name: 'Knitting Outbound Qty',
				data: [<?= $yearKnitOutboundProd; ?>],
				stack: '1',
				color: '#df9f9f',
			}, {
				name: 'Dyeing Inhouse Qty',
				data: [<?= $yearDyeProd; ?>],
				stack: '2',
				color: '#4169E1'
			}, {
				name: 'Dyeing Outbound Qty',
				data: [<?= $yearDyeProd2; ?>],
				stack: '2',
				color: '#99c2ff'
			}
	    ]

	});

	Highcharts.chart('container3', {
		chart: {
			type: 'column',
		},
		title: {
			text: 'Date Wise Knitting'
		},
		subtitle: {
			text: '<?= $startToEndMonth; ?>'
		},
		xAxis: {
			categories: <?= $dayArr; ?>,
		},
		yAxis: {
			allowDecimals: true,
			min: 0,
			title: {
				text: 'Production Qty in KG'
			}
		},
		tooltip: {
			crosshairs: true,
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} KG</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			}
		},
		series: [
			{
				name: 'Knitting Inhouse Qty',
				data: [<?= $dailyKnitProd; ?>],
				stack: '1',
				color: '#8B4513',
			},
			{
				name: 'Knitting Outbound Qty',
				data: [<?= $dailyKnitProd2; ?>],
				stack: '1',
				color: '#df9f9f',
			}
		]

	});

	Highcharts.chart('container4', {
		chart: {
			type: 'column',
		},
		title: {
			text: 'Date Wise Dyeing'
		},
		subtitle: {
			text: '<?= $startToEndMonth; ?>'
		},
		xAxis: {
			categories: <?= $dayArr; ?>,
		},
		yAxis: {
			allowDecimals: true,
			min: 0,
			title: {
				text: 'Production Qty in KG'
			}
		},
		tooltip: {
			crosshairs: true,
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} KG</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			}
		},
		series: [
			{
				name: 'Dyeing Inhouse Qty',
				data: [<?= $dailyDyeProd; ?>],
				stack: '1',
				color: '#4169E1',
			},
			{
				name: 'Dyeing Outbound Qty',
				data: [<?= $dailyDyeProd2; ?>],
				stack: '1',
				color: '#349beb',
			}
		]

	});

	Highcharts.chart('container5', {
		chart: {
			type: 'column',
		},
		title: {
			text: 'Date Wise Cutting'
		},
		subtitle: {
			text: '<?= $startToEndMonth; ?>'
		},
		xAxis: {
			categories: <?= $dayArr; ?>,
		},
		yAxis: {
			allowDecimals: true,
			min: 0,
			title: {
				text: 'Production Qty in PCS'
			}
		},
		tooltip: {
			crosshairs: true,
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} PCS</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			}
		},
		series: [{
			name: 'CUTTING QUANTITY',
			data: [<?= $dailyCutProd; ?>],
			stack: '1',
			color: '#3399FF',
		}]

	});

	Highcharts.chart('container6', {
		chart: {
			type: 'column',
		},
		title: {
			text: 'Date Wise Sewing'
		},
		subtitle: {
			text: '<?= $startToEndMonth; ?>'
		},
		xAxis: {
			categories: <?= $dayArr; ?>,
		},
		yAxis: {
			allowDecimals: true,
			min: 0,
			title: {
				text: 'Production Qty in PCS'
			}
		},
		tooltip: {
			crosshairs: true,
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} PCS</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			}
		},
		series: [{
			name: 'SEWING QUANTITY',
			data: [<?= $dailySewProd; ?>],
			stack: '1',
			color: '#FF3300',
		}]
	});

	Highcharts.chart('container7', {
		chart: {
			type: 'column',
		},
		title: {
			text: 'Date Wise Finish'
		},
		subtitle: {
			text: '<?= $startToEndMonth; ?>'
		},
		xAxis: {
			categories: <?= $dayArr; ?>,
		},
		yAxis: {
			allowDecimals: true,
			min: 0,
			title: {
				text: 'Production Qty in PCS'
			}
		},
		tooltip: {
			crosshairs: true,
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} PCS</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			}
		},
		series: [{
			name: 'FINISH QUANTITY',
			data: [<?= $dailyPackingProd; ?>],
			stack: '1',
			color: '#3399FF',
		}]
	});

	Highcharts.chart('container8', {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		title: {
			text: 'Buyer Wise Order'
		},
		subtitle: {
			text: '<?= $startToEndYear; ?>'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		accessibility: {
			point: {
				valueSuffix: '%'
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				}
			}
		},
		series: [{
			name: 'Brands',
			colorByPoint: true,
			data: [
				[<?= $buyerWiseOrder; ?>]
			]
		}]
	});

	Highcharts.chart('container9', {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		title: {
			text: 'Account Holder Wise Order'
		},
		subtitle: {
			text: '<?= $startToEndYear; ?>'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		accessibility: {
			point: {
				valueSuffix: '%'
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				}
			}
		},
		series: [{
			name: 'Brands',
			colorByPoint: true,
			data: [
				[<?= $teamLeaderWiseOrder; ?>]
			]
		}]
	});

	Highcharts.chart('container10', {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		title: {
			text: 'Buyer Wise Sample'
		},
		subtitle: {
			text: '<?= $startToEndYear; ?>'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		accessibility: {
			point: {
				valueSuffix: '%'
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				}
			}
		},
		series: [{
			name: 'Brands',
			colorByPoint: true,
			data: [
				[<?= $buyerSampleWiseOrder; ?>]
			]
		}]
	});
</script>
<style>
	.tblData td,
	th {
		margin: 5px 0 20px 10px;
		border: 1px solid #000;
	}

	.canvas_info {
		width: 95% !important;
		font-size: 10px !important;
	}

	td {
		word-break: break-word;
	}
</style>
<?
function add_month($orgDate, $mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0, 0, 0, date('m', $cd) + $mon, 1, date('Y', $cd)));
	return $retDAY;
}
?>