<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}

include('../../../includes/common.php');


if (!function_exists('pre')) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
}

$user_id	= $_SESSION['logic_erp']['user_id'];
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];

if ($db_type == 0) $null_value = "IFNULL";
else $null_value = "nvl";


$country_name = return_library_array("select id,country_name from   lib_country", "id", "country_name");


if ($action == "multiple_company_search") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode, 1);
	extract($_REQUEST);
?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			if (str != "") str = str.split("_");

			if ($("#tr_" + str[0]).css("display") != 'none') {

				toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

				if (jQuery.inArray(str[1], selected_id) == -1) {
					selected_id.push(str[1]);
					selected_name.push(str[2]);
				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == str[1]) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}
				var id = '';
				var name = '';
				var attach_id = '';
				for (var i = 0; i < selected_id.length; i++) {
					id += selected_id[i] + ',';
					name += selected_name[i] + '*';
				}
				id = id.substr(0, id.length - 1);
				name = name.substr(0, name.length - 1);

				$('#txt_selected_id').val(id);
				$('#txt_selected').val(name);
			}
		}
	</script>
	</head>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" />
	<input type="hidden" name="txt_selected" id="txt_selected" />

	</html>
<?php

	$sql = "select comp.id,comp.company_name from lib_company comp,lib_ac_period_mst b where comp.status_active=1 and comp.is_deleted=0 and comp.id=b.company_id $company_cond group by comp.id,comp.company_name order by comp.company_name";

	echo  create_list_view("list_view", "Company Name", "200", "315", "345", 0, $sql, "js_set_value", "id,company_name", "", 1, "0", $arr, "company_name", "balance_sheet_group_of_company_wise_controller", 'setFilterGrid("list_view",-1);', '0', 0, 1);
}


/*
|------------------------------------------------------------------------------------------------------------
|	Report Create data - 06/12/2022, By - Reaz Uddin , Report for - Evance . Emplementer - Ananta
|------------------------------------------------------------------------------------------------------------
*/
if ($action == "generate_report") {
	$data = explode("__", $data);

	$company_name		= $data[0];
	$exchange_rate		= number_format($data[3],2);
	
	$new_conn		= integration_params(3); // Accounts Module

	$company_short_name_arr = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");

	if ($db_type == 0) {
		$txt_form_date	= change_date_format($data[1], "yyyy-mm-dd");
		$txt_to_date 	= change_date_format($data[2], "yyyy-mm-dd");
	}
	if ($db_type == 2) {
		$txt_form_date 	= change_date_format($data[1], "yyyy-mm-dd", "-", 1);
		$txt_to_date 	= change_date_format($data[2], "yyyy-mm-dd", "-", 1);
	}

	$companyCond = "";
	if ($company_name) {
		$companyCond  = " and d.COMPANY_ID in($company_name)";
		$companyCond2 = " and c.company_name in($company_name)";
	}

	//echo "<pre>";
	//print_r($balance_type_arr);


	$report_data_array = array();

	$sql_balance_type = "select d.company_id, d.ac_code, b.account_type from ac_coa_mst d, lib_account_group b where  d.is_deleted=0 and d.status_active=1 and  b.is_deleted=0 and b.status_active=1 and d.ac_subgroup_id=b.id $companyCond";
	$sql_balance_type_result 	= sql_select($sql_balance_type,'',$new_conn);
	$balance_type_arr 			= array();
	foreach ($sql_balance_type_result as $row1) {
		$balance_type_arr[$row1[csf('company_id')]][$row1[csf('ac_code')]] = $row1[csf('account_type')];
	}
	unset($sql_balance_type_result);



	$sql = "SELECT a.ID, a.PARITICULARS_NAME, a.SEQUENCE_ID, b.AC_CODE, d.COMPANY_ID, sum($null_value(c.DEBIT_AMOUNT,0)) as DEBIT_AMOUNT, sum($null_value(c.CREDIT_AMOUNT,0)) as CREDIT_AMOUNT FROM lib_bep_report_particular_set a, lib_bep_report_ac_set b, AC_TRANSACTION_MST d, AC_TRANSACTION_DTLS c 
WHERE  a.id = b.pariticulars_id AND c.ACCOUNT_CODE=b.AC_CODE $companyCond and d.JOURNAL_DATE between '$txt_form_date' and '$txt_to_date' and c.TRANSECTION_MST_ID=d.id  AND a.status_active = 1 AND a.is_deleted = 0  AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 group by a.ID, a.PARITICULARS_NAME, a.SEQUENCE_ID, b.AC_CODE, d.COMPANY_ID order by a.SEQUENCE_ID";
	//echo $sql;
	$result = sql_select($sql,'',$new_conn);
	$companyIdArr = array();
	foreach ($result as $row) {
		$companyIdArr[$row['COMPANY_ID']] = $row['COMPANY_ID'];

		$particularName = $row['ID'] . "__" . $row['PARITICULARS_NAME'];

		if ($balance_type_arr[$row['COMPANY_ID']][$row['AC_CODE']] == 2) {
			$report_data_array[$particularName][$row['COMPANY_ID']]['balance'] += $row['DEBIT_AMOUNT'] - $row['CREDIT_AMOUNT'];
		} else {
			$report_data_array[$particularName][$row['COMPANY_ID']]['balance'] += $row['CREDIT_AMOUNT'] - $row['DEBIT_AMOUNT'];
		}
	}

	//echo "<pre>";
	//print_r($report_data_array);
	//die;

	// =================================================================================================
	//												ERP PRODUCTION DATA
	// =================================================================================================
	$prod_sql = "select c.company_name,c.id as job_id,c.job_quantity as job_qty,c.avg_unit_price,d.id as po_id,b.production_qnty as prod_qty,e.order_quantity as po_qty,e.order_rate from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_details_master c,wo_po_break_down d,wo_po_color_size_breakdown e where a.id=b.mst_id and c.id=d.job_id and d.id=e.po_break_down_id and e.id=b.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $companyCond2 and a.production_date between '$txt_form_date' and '$txt_to_date'  and a.production_type =5";
	// echo $prod_sql ; die;
	$prod_sql_res = sql_select($prod_sql);
	$prod_arr = array();
	$job_id_arr = array();
	$po_id_arr = array();
	$$job_wise_comp_arr = array();
	foreach ($prod_sql_res as $v) 
	{
		if(!$job_id_arr[$v['JOB_ID']])
		{
			$prod_arr[$v['COMPANY_NAME']]['JOB_FOB'] 	+= ( $v['JOB_QTY'] * $v['AVG_UNIT_PRICE']);
		}

		$prod_arr[$v['COMPANY_NAME']]['PROD_QTY'] 	+= $v['PROD_QTY']; 
		$prod_arr[$v['COMPANY_NAME']]['PO_FOB'] 	+= ( $v['PROD_QTY'] *  $v['ORDER_RATE'] );
		

		$job_id_arr[$v['JOB_ID']] 					 = $v['JOB_ID'];
		$po_id_arr[$v['PO_ID']] 					 = $v['PO_ID'];
		$job_wise_comp_arr[$v['JOB_ID']] 			 = $v['COMPANY_NAME'];
		// $prod_arr[$v['COMPANY_NAME']]['ORDER_RATE'] += $v['ORDER_RATE'];
	}
	// pre($prod_arr); die;
	//=================================== CLEAR TEMP ENGINE ====================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 93 and ref_from in (1,2,3)");
	oci_commit($con);   
	//=================================== INSERT PO ID AND JOB ID INTO TEMP ENGINE ====================================

	fnc_tempengine("gbl_temp_engine", $user_id, 93, 1,$po_id_arr, $empty_arr); 
	fnc_tempengine("gbl_temp_engine", $user_id, 93, 2,$job_id_arr, $empty_arr);  


	// =================================================================================================
	//												FOB AND BTB Open

	// Ref Style Wise Order Entry Follow up Report  			Style Button
	// =================================================================================================

	$fabric_trims_booking_data=sql_select("SELECT c.amount as amount,a.company_name,e.emb_name from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c ,wo_booking_mst b , wo_pre_cost_embe_cost_dtls e,gbl_temp_engine tmp
	where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=b.booking_no and b.entry_form=574  and c.pre_cost_fabric_cost_dtls_id=e.id and c.po_break_down_id=tmp.ref_val and c.booking_type=6 and a.garments_nature=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and e.status_active=1 and tmp.entry_form=93 and tmp.ref_from=1 and tmp.user_id=$user_id ");
	$ti=0;
	$wi=0;
	$fb=0;
	foreach ($fabric_trims_booking_data as $row) {
		if($row[csf('emb_name')]==3){
			$wash_b2b_value[$row['COMPANY_NAME']] += $row[csf('amount')];
		}else{
			$emblishment_b2b_value[$row['COMPANY_NAME']] += $row[csf('amount')];
		}
	}
	// pre($wash_b2b_value); die;
	// echo "hello";die;
	$pi_number_data=sql_select("SELECT b.booking_mst_id,f.company_name,c.amount as pi_amount , b.amount as wo_amount from wo_booking_dtls b,com_pi_item_details c,com_pi_master_details d,wo_po_details_master f,gbl_temp_engine tmp where b.booking_no = c.work_order_no and d.id = c.pi_id and b.job_no = f.job_no and b.po_break_down_id = tmp.ref_val and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.item_category_id in (3) and d.status_active=1 and d.is_deleted=0  and tmp.entry_form=93 and tmp.ref_from=1 and tmp.user_id=$user_id "); 
	$pi_data_arr=array();
	$fi=0;
	$ti=0;
	//$fabric_b2b_value+=$row[csf('amount')];
	foreach ($pi_number_data as $row) {
		$net_pi_value[$row['COMPANY_NAME']] +=$row[csf('pi_amount')];
		$job_wise_wo_amount[$row['COMPANY_NAME']] +=$row[csf('wo_amount')];
		$booking_id_arr[$row[csf('booking_mst_id')]]=$row[csf('booking_mst_id')];
	}
	// pre($booking_id_arr); die;
	//=================================== INSERT PO ID AND JOB ID INTO TEMP ENGINE ====================================
	fnc_tempengine("gbl_temp_engine", $user_id, 93, 3,$booking_id_arr, $empty_arr); 
	
	
	$fabric_booking_amount=sql_select("SELECT b.amount as wo_amount,a.company_id from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine tmp where a.id=b.booking_mst_id and b.booking_mst_id=tmp.ref_val and a.status_active=1 and b.is_deleted=0 and tmp.entry_form=93 and tmp.ref_from=3 and tmp.user_id=$user_id and b.booking_type=1"); 
		
	foreach($fabric_booking_amount as $row){
		$booking_amount[$row['COMPANY_ID']] += $row[csf('wo_amount')];
	}
	// pre($booking_amount); die;
	foreach ($companyIdArr as $comId) 
	{
		$job_wo_ratio[$comId] 		= ($job_wise_wo_amount[$comId] / $booking_amount[$comId] ) *100;
		$fabric_b2b_value[$comId] 	= $job_wo_ratio[$comId] * $net_pi_value[$comId] /100;
	}
	// pre($fabric_b2b_value); die;
	$pi_number_data_acc=sql_select("SELECT d.item_category_id,c.amount,f.company_name
	from wo_booking_dtls b,com_pi_item_details c,com_pi_master_details d ,wo_po_details_master f,gbl_temp_engine tmp where b.booking_no=c.work_order_no and b.id=c.work_order_dtls_id and b.job_no = f.job_no and d.id=c.pi_id and  f.style_ref_no= c.buyer_style_ref  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.item_category_id in (4) and d.status_active=1 and d.is_deleted=0 and c.order_id=tmp.ref_val and tmp.entry_form=93 and tmp.ref_from=1 and tmp.user_id=$user_id ");

	foreach ($pi_number_data_acc as $row) {
		if($row['ITEM_CATEGORY_ID']==4){
			$accessories_b2b_value[$row['COMPANY_NAME']] +=$row['AMOUNT'];
		} 
	}
	// pre($accessories_b2b_value); die;
	// echo "hello";
	// pre($prod_arr);die;

	


	$erp_data_arr = array();
	foreach ($companyIdArr as $comId) 
	{
		$total_b2b_value = $fabric_b2b_value[$comId]+$accessories_b2b_value[$comId]+$wash_b2b_value[$comId]+$emblishment_b2b_value[$comId];
		$prod_qty 	= $prod_arr[$comId]['PROD_QTY'];
		/* $po_qty 	= $prod_arr[$comId]['PO_QTY'];
		$unit_price = $prod_arr[$comId]['ORDER_RATE']; */
		$prod_fob 	= $prod_arr[$comId]['PO_FOB'];
		$job_fob 	= $prod_arr[$comId]['JOB_FOB'];
		$cm_earn 	= ($job_fob);
		$prod_fob_tk= $prod_fob * $exchange_rate;  
		$cm_earn_tk	= $cm_earn * $exchange_rate;  
		$earn_per_unit	= $cm_earn / $prod_qty;  
		$unit_earn_tk	= $earn_per_unit * $exchange_rate;  

		$erp_data_arr[$comId][1] = $prod_qty;
		$erp_data_arr[$comId][2] = $prod_fob;
		$erp_data_arr[$comId][3] = $prod_fob_tk;
		$erp_data_arr[$comId][4] = $cm_earn;
		$erp_data_arr[$comId][5] = $cm_earn_tk;
		$erp_data_arr[$comId][6] = $earn_per_unit;
		$erp_data_arr[$comId][7] = $unit_earn_tk;

		$erp_data_arr['GT'][1] += $prod_qty;
		$erp_data_arr['GT'][2] += $prod_fob;
		$erp_data_arr['GT'][3] += $prod_fob_tk;
		$erp_data_arr['GT'][4] += $cm_earn;
		$erp_data_arr['GT'][5] += $cm_earn_tk;
		$erp_data_arr['GT'][6] += $earn_per_unit;
		$erp_data_arr['GT'][7] += $unit_earn_tk;
	}

	//=================================== CLEAR TEMP ENGINE ====================================
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=93 and ref_from in(1,2,3)");
	oci_commit($con);  
	disconnect($con);

	$div_width 	= 500 + (count($companyIdArr) * 200);

	ob_start();
?>
	<div style="width:<?php echo $div_width; ?>px; font-family: sans-serif;" align="left">
		<table width="100%">
			<tr style="" id="">
				<td align="center" style="font-size:15px;"><b><?php echo 'BEP Analysis Report'; ?></b></td>
			</tr>
			<tr>
				<td align="center"><b>From Date: </b><?php echo $data[1]; ?><b> To date: </b><?php echo $data[2]; ?></td>
			</tr>
		</table>
		<table width="100%" class="rpt_table" rules="all" style="font-size:14px;" border="1" cellpadding="0" cellspacing="0">
			<thead>
				<tr valign="middle">
					<th width='50' rowspan="2">SL</th>
					<th width='250' rowspan="2">Particulars</th>
					<?
					foreach ($companyIdArr as $comId) {
					?>
						<th colspan="2"> <? echo $company_short_name_arr[$comId];  ?></th>
					<? }  ?>
					<th colspan="2">Total</th>
				</tr>
				<tr>
					<?
					foreach ($companyIdArr as $comId) {
					?>
						<th width='120'>Tk</th>
						<th width='80'>%</th>
					<? }  ?>
					<th width='120'>Tk</th>
					<th width='80'>%</th>
				</tr>
			</thead>

			<tbody id="table_body">
				<?php
				$i = 1;
				$production_data_caption_arr = array(1 => "Production Quantity(pcs)", 2 => "Production FOB (USD)",  3 => "Production FOB (@ tk.$exchange_rate)", 4 => "CM Earn ($)", 5 => "CM Earn (TK @ $exchange_rate)", 6 => "Per unit earned CM($)", 7 => "Per unit earned CM(TK)");
				// ============================ ERP Production data ==========================================================
				foreach ($production_data_caption_arr as $key => $production_data_caption) {
					$particularTotal = 0;
				?>
					<tr>
						<td width="50" align="center"><?php echo $i; ?></td>
						<td width="250"><?php echo $production_data_caption; ?></td>
						<?
						foreach ($companyIdArr as $comId) {
						?>
							<td width="120" align="right"><?= number_format($erp_data_arr[$comId][$i],2);?></td>
							<td width="80" align="right"><?php //echo $i;
															?>%</td>
						<?
							//$particularTotal += $particular_data_arr[$comId]['balance'] ;
						}
						?>
						<td width="120" align="right"><?= number_format($erp_data_arr['GT'][$i],2);
														?></td>
						<td width="80" align="right"><?php //echo $i;
														?>%</td>
					</tr>
				<?php
					$i++;
				}

				// ============================ ACC data ==========================================================
				$FactoryOverhead = array();
				$TotalExpenses = array();
				foreach ($report_data_array as $particularName => $particular_data_arr) {
					$particularTotal = 0;
				?>
					<tr>
						<td width="50" align="center"><?php echo $i; ?></td>
						<td width="250"><?php

										$particularName =  explode("__", $particularName);
										echo $particularName[1];

										//echo $particularName;
										?></td>
						<?
						foreach ($companyIdArr as $comId) {
						?>
							<td width="120" align="right"><?php echo number_format($particular_data_arr[$comId]['balance'], 2); ?></td>
							<td width="80" align="right"><?php //echo number_format($particular_data_arr[$comId]['balance'],2);
															?>%</td>
						<?
							$particularTotal += $particular_data_arr[$comId]['balance'];

							$FactoryOverhead[$comId][total_amt] += $particular_data_arr[$comId]['balance'];
							$FactoryOverhead[$comId][total_par] += 0;

							$TotalExpenses[$comId][total_amt] += $particular_data_arr[$comId]['balance'];
							$TotalExpenses[$comId][total_par] += 0;
						}
						?>
						<td width="120" align="right"><?php echo number_format($particularTotal, 2); ?></td>
						<td width="80" align="right"><?php //echo number_format($particularTotal,2);
														?>%</td>
					</tr>
					<?php
					// ---------------------- Factory Overhead ---------------------------------------
					if ($i == 14) {
					?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold;">
							<td width="" align="right" colspan="2">Factory Overhead</td>

							<?
							$FactoryOverheadTot = 0;
							$FactoryOverheadParTot = 0;
							foreach ($companyIdArr as $comId) {
							?>
								<td width="120" align="right"><?php echo number_format($FactoryOverhead[$comId][total_amt], 2); ?></td>
								<td width="80" align="right"><?php echo number_format($FactoryOverhead[$comId][total_par], 2); ?>%</td>
							<?
								$FactoryOverheadTot += $FactoryOverhead[$comId][total_amt];
								$FactoryOverheadParTot += $FactoryOverhead[$comId][total_par];

								$FactoryOverhead[$comId][total_amt]	-= $FactoryOverhead[$comId][total_amt];
								$FactoryOverhead[$comId][total_par]	-= $FactoryOverhead[$comId][total_par];
							}
							?>
							<td width="120" align="right"><?php echo number_format($FactoryOverheadTot, 2); ?></td>
							<td width="80" align="right"><?php echo number_format($FactoryOverheadParTot, 2); ?>%</td>
						</tr>
					<?php
						//unset($FactoryOverhead);
					}

					// ----------------------------- Total Administrative -----------------------------
					if ($i == 19) {
					?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold;">
							<td width="" align="right" colspan="2">Total Administrative</td>

							<?
							$FactoryOverheadTot = 0;
							$FactoryOverheadParTot = 0;
							foreach ($companyIdArr as $comId) {
							?>
								<td width="120" align="right"><?php echo number_format($FactoryOverhead[$comId][total_amt], 2); ?></td>
								<td width="80" align="right"><?php echo number_format($FactoryOverhead[$comId][total_par], 2); ?>%</td>
							<?
								$FactoryOverheadTot += $FactoryOverhead[$comId][total_amt];
								$FactoryOverheadParTot += $FactoryOverhead[$comId][total_par];
							}
							?>
							<td width="120" align="right"><?php echo number_format($FactoryOverheadTot, 2); ?></td>
							<td width="80" align="right"><?php echo number_format($FactoryOverheadParTot, 2); ?>%</td>
						</tr>
				<?php
						//unset($FactoryOverhead);
					}
					$i++;
				}
				?>
				<tr bgcolor="#CCCCCC" style="font-weight:bold;">
					<td width="" align="right" colspan="2">Total Expenses</td>

					<?
					$TotalExpensesTot = 0;
					$TotalExpensesParTot = 0;
					foreach ($companyIdArr as $comId) {
					?>
						<td width="120" align="right"><?php echo number_format($TotalExpenses[$comId][total_amt], 2); ?></td>
						<td width="80" align="right"><?php echo number_format($TotalExpenses[$comId][total_par], 2); ?>%</td>
					<?
						$TotalExpensesTot += $TotalExpenses[$comId][total_amt];
						$TotalExpensesParTot += $TotalExpenses[$comId][total_par];
					}
					?>
					<td width="120" align="right"><?php echo number_format($TotalExpensesTot, 2); ?></td>
					<td width="80" align="right"><?php echo number_format($TotalExpensesParTot, 2); ?>%</td>
				</tr>

				<tr bgcolor="#CCCCCC" style="font-weight:bold;">
					<td width="" align="right" colspan="2">Net Margin</td>
					<?
					$TotCMEarnTk = 0; // ERP CM Earn  Data------------------------------
					$TotCMEarnPar = 0; // ERP CM Earn  Data------------------------------

					$NetMarginTot = 0;
					$NetMarginParTot = 0;
					foreach ($companyIdArr as $comId) {
					?>
						<td width="120" align="right"><?php echo number_format(($TotCMEarnTk - $TotalExpenses[$comId][total_amt]), 2); ?></td>
						<td width="80" align="right"><?php echo number_format(($TotCMEarnPar - $TotalExpenses[$comId][total_par]), 2); ?>%</td>
					<?
						$NetMarginTot 		+= ($TotCMEarnPar - $TotalExpenses[$comId][total_amt]);
						$NetMarginParTot 	+= ($TotCMEarnPar - $TotalExpenses[$comId][total_par]);
					}
					?>
					<td width="120" align="right"><?php echo number_format($NetMarginTot, 2); ?></td>
					<td width="80" align="right"><?php echo number_format($NetMarginParTot, 2); ?>%</td>
				</tr>
				<?php

				//echo "<pre>";
				//print_r($sub_group_arr);die;
				?>
			</tbody>
		</table>
	</div>
	<?php
	$html = ob_get_contents();
	ob_flush();

	foreach (glob("*_" . $user_id . ".xls") as $filename)  // Only delete current user created excel/pdf file @reaz
	{
		@unlink($filename);
	}
	//html to xls convert
	$name = time();

	$name = "$name" . "_" . $user_id . ".xls";
	$create_new_excel = fopen('' . $name, 'w');
	$is_created = fwrite($create_new_excel, $html);

	($sql_photo[0][csf('image_location')] != "") ? $companyLogoLocation = "../../" . $sql_photo[0][csf('image_location')] : $companyLogoLocation = "";
	?>
	<input type="hidden" id="txt_excl_link" value="<?php echo 'requires/' . $name; ?>" />
	<input type="hidden" id="txt_imagelocation" value="<?php echo $companyLogoLocation; ?>" />
<?php
}

?>